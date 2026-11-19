<?php

namespace App\Services;

use App\Models\ClassRoom;
use App\Models\Department;
use App\Models\Timetable;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Shuchkin\SimpleXLSX;

class TimetableImportService
{
    /**
     * Standardize time slots into consistent H:i-H:i strings.
     */
    public function standardizeTimeSlot(string $timeStr): string
    {
        return Timetable::normalizeSlot($timeStr);
    }

    /**
     * Parse Excel workbook and extract preview data.
     */
    public function parseWorkbook(string $filePath, int $departmentId): array
    {
        $xlsx = SimpleXLSX::parse($filePath);
        if (!$xlsx) {
            throw new \Exception('Failed to parse Excel file: ' . SimpleXLSX::parseError());
        }

        $sheetNames = $xlsx->sheetNames();
        $facultyMapping = [];
        $masterSheetIndex = 0;

        // 1. Identify Faculty Sheet (if present)
        foreach ($sheetNames as $index => $name) {
            $normalized = strtolower(trim($name));
            if (Str::contains($normalized, ['faculty', 'teacher', 'staff', 'initial', 'mapping', 'faculty_mapping'])) {
                $facultyRows = $xlsx->rows($index);
                $facultyMapping = $this->extractFacultyMapping($facultyRows);
            } elseif (Str::contains($normalized, ['master', 'timetable', 'master timetable', 'department', 'schedule'])) {
                $masterSheetIndex = $index;
            }
        }

        // 2. Load Department Teachers for Initial resolution
        $teachers = User::where('role', 'teacher')
            ->where(function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId)->orWhereNull('department_id');
            })
            ->get();

        $allTeachers = User::where('role', 'teacher')->get();

        // 3. Parse Master Timetable Rows
        $rows = $xlsx->rows($masterSheetIndex);
        if (empty($rows)) {
            throw new \Exception('The timetable sheet appears to be empty.');
        }

        $parsedData = $this->extractMasterGrid($rows, $departmentId, $facultyMapping, $teachers, $allTeachers);

        return $parsedData;
    }

    /**
     * Extract faculty initials mapping from secondary sheet rows.
     */
    protected function extractFacultyMapping(array $rows): array
    {
        $mapping = [];
        if (empty($rows)) {
            return $mapping;
        }

        $headerRow = array_shift($rows);
        $initialCol = -1;
        $nameCol = -1;
        $emailCol = -1;

        foreach ($headerRow as $idx => $header) {
            $h = strtolower(trim((string) $header));
            if (Str::contains($h, ['initial', 'acronym', 'code', 'short', 'abbr'])) {
                $initialCol = $idx;
            } elseif (Str::contains($h, ['name', 'faculty', 'teacher', 'fullname', 'full name'])) {
                $nameCol = $idx;
            } elseif (Str::contains($h, ['email', 'mail'])) {
                $emailCol = $idx;
            }
        }

        // Default if headers not strictly labeled: column 0 = Initial, column 1 = Name, column 2 = Email
        if ($initialCol === -1) {
            $initialCol = 0;
        }
        if ($nameCol === -1) {
            $nameCol = 1;
        }
        if ($emailCol === -1 && count($headerRow) > 2) {
            $emailCol = 2;
        }

        foreach ($rows as $row) {
            $initial = isset($row[$initialCol]) ? strtoupper(trim((string) $row[$initialCol])) : '';
            $name = isset($row[$nameCol]) ? trim((string) $row[$nameCol]) : '';
            $email = ($emailCol !== -1 && isset($row[$emailCol])) ? trim((string) $row[$emailCol]) : '';

            if (!empty($initial)) {
                $mapping[$initial] = [
                    'name' => $name,
                    'email' => $email,
                ];
            }
        }

        return $mapping;
    }

    /**
     * Extract Grid information from Master TT sheet rows.
     */
    protected function extractMasterGrid(array $rows, int $departmentId, array $facultyMapping, $deptTeachers, $allTeachers): array
    {
        $daysList = ['MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY'];
        $parsedDays = [];
        $classColumns = [];
        $lectures = [];
        $unmappedInitials = [];
        $mappedFaculty = [];

        $currentDay = null;

        $numRows = count($rows);
        for ($r = 0; $r < $numRows; $r++) {
            $row = $rows[$r];
            $firstCell = strtoupper(trim((string) ($row[0] ?? '')));
            $rowJoined = strtoupper(implode(' ', array_slice($row, 0, 5)));

            // Check for Day Marker
            foreach ($daysList as $dayName) {
                if ($firstCell === $dayName || Str::contains($rowJoined, $dayName)) {
                    $currentDay = ucfirst(strtolower($dayName));
                    if (!in_array($currentDay, $parsedDays)) {
                        $parsedDays[] = $currentDay;
                    }
                    break;
                }
            }

            // Check if this row or next row is Class header row (contains class names like D6AD, D7A, D10B, etc. or TIME/CLASS)
            $isClassHeaderRow = false;
            foreach ($row as $colIdx => $cell) {
                $cellStr = strtoupper(trim((string) $cell));
                if (Str::contains($cellStr, ['TIME/CLAS', 'TIME/ CLASS', 'TIME / CLASS', 'CLASS', 'TIME']) ||
                    preg_match('/^D\d{1,2}[A-Z]*/i', $cellStr) ||
                    preg_match('/^(FE|SE|TE|BE|FY|SY|TY|FINAL)[ -]?[A-Z]/i', $cellStr)) {
                    $isClassHeaderRow = true;
                    break;
                }
            }

            if ($isClassHeaderRow && empty($classColumns)) {
                // Check if previous row had Room numbers
                $prevRow = ($r > 0) ? $rows[$r - 1] : [];

                foreach ($row as $colIdx => $cell) {
                    $className = trim((string) $cell);
                    if ($colIdx === 0 || empty($className) || Str::contains(strtoupper($className), ['TIME', 'CLASS', 'TIME/CLASS'])) {
                        continue;
                    }

                    // Clean class name
                    $className = preg_replace('/\s+/', ' ', $className);
                    $roomHeader = isset($prevRow[$colIdx]) ? trim((string) $prevRow[$colIdx]) : '';
                    if (Str::contains(strtoupper($roomHeader), ['MASTER', 'TIME TABLE', 'SEM', 'DAY', 'MONDAY', 'TUESDAY'])) {
                        $roomHeader = '';
                    }

                    $parsedClass = $this->parseClassIdentifier($className);

                    $classColumns[$colIdx] = [
                        'col_idx' => $colIdx,
                        'name' => $className,
                        'display_name' => $className,
                        'room' => $roomHeader,
                        'year' => $parsedClass['year'],
                        'division' => $parsedClass['division'],
                    ];
                }
                continue;
            }

            // Check if this row is a time slot row
            $timeSlotStr = trim((string) ($row[0] ?? ''));
            if ($currentDay && !empty($timeSlotStr) && preg_match('/\d{1,2}[:.]\d{2}/', $timeSlotStr)) {
                $stdSlot = $this->standardizeTimeSlot($timeSlotStr);

                // Check for Break
                $isBreakRow = Str::contains(strtoupper(implode(' ', $row)), ['BREAK', 'LUNCH']);

                foreach ($classColumns as $colIdx => $classInfo) {
                    $cellVal = isset($row[$colIdx]) ? trim((string) $row[$colIdx]) : '';

                    if ($isBreakRow) {
                        $lectures[] = [
                            'day' => $currentDay,
                            'time_slot' => $stdSlot,
                            'class_name' => $classInfo['name'],
                            'class_year' => $classInfo['year'],
                            'class_division' => $classInfo['division'],
                            'subject' => 'BREAK',
                            'faculty_id' => null,
                            'faculty_initials' => null,
                            'faculty_name' => null,
                            'room' => '-',
                            'is_break' => true,
                            'raw_content' => 'BREAK',
                        ];
                        continue;
                    }

                    if (empty($cellVal) || in_array($cellVal, ['-', '--', 'nil', 'NIL', 'NA', 'N/A'])) {
                        continue;
                    }

                    // Parse cell into 1 or more lectures (for labs / batches / single lectures)
                    $parsedLectures = $this->parseCellLectures(
                        $cellVal,
                        $classInfo,
                        $currentDay,
                        $stdSlot,
                        $facultyMapping,
                        $deptTeachers,
                        $allTeachers
                    );

                    foreach ($parsedLectures as $lec) {
                        $lectures[] = $lec;
                        if (!empty($lec['faculty_initials'])) {
                            if ($lec['faculty_id']) {
                                $mappedFaculty[$lec['faculty_initials']] = $lec['faculty_name'];
                            } else {
                                $unmappedInitials[$lec['faculty_initials']] = ($unmappedInitials[$lec['faculty_initials']] ?? 0) + 1;
                            }
                        }
                    }
                }
            }
        }

        // If no days were explicitly tagged, default to Monday
        if (empty($parsedDays)) {
            $parsedDays = ['Monday'];
        }

        return [
            'days' => $parsedDays,
            'classes' => array_values($classColumns),
            'total_lectures' => count($lectures),
            'lectures' => $lectures,
            'mapped_faculty' => $mappedFaculty,
            'unmapped_initials' => $unmappedInitials,
            'total_classes' => count($classColumns),
        ];
    }

    /**
     * Parse class string like "D7A", "D6AD A", "SY-A", "D10B" into year and division.
     */
    protected function parseClassIdentifier(string $className): array
    {
        $clean = trim($className);

        // Pattern 1: D6AD A or D11ad A
        if (preg_match('/^(D\d{1,2}[a-zA-Z]*)\s+([A-Za-z0-9]+)$/', $clean, $m)) {
            return ['year' => $m[1], 'division' => $m[2]];
        }

        // Pattern 2: D7A or D10B or D15C
        if (preg_match('/^(D\d{1,2})([A-Za-z])$/', $clean, $m)) {
            return ['year' => $m[1], 'division' => $m[2]];
        }

        // Pattern 3: SY-A or SY A
        if (preg_match('/^(FY|SY|TY|FINAL|FE|SE|TE|BE)[ -]?([A-Za-z0-9]+)$/i', $clean, $m)) {
            return ['year' => strtoupper($m[1]), 'division' => strtoupper($m[2])];
        }

        // Fallback
        return ['year' => $clean, 'division' => 'A'];
    }

    /**
     * Parse cell contents into structured lecture(s).
     */
    protected function parseCellLectures(
        string $cellContent,
        array $classInfo,
        string $day,
        string $timeSlot,
        array $facultyMapping,
        $deptTeachers,
        $allTeachers
    ): array {
        $results = [];
        $lines = preg_split('/[\r\n]+/', $cellContent);
        $lines = array_filter(array_map('trim', $lines));

        if (empty($lines)) {
            return $results;
        }

        // Check if multi-batch slash format e.g. "DS/B/YP/209" or "OS/A/AK/206"
        $isMultiBatch = false;
        foreach ($lines as $line) {
            if (substr_count($line, '/') >= 3) {
                $isMultiBatch = true;
                break;
            }
        }

        if ($isMultiBatch) {
            foreach ($lines as $line) {
                $parts = explode('/', $line);
                if (count($parts) >= 4) {
                    $subj = trim($parts[0]);
                    $batch = trim($parts[1]);
                    $initial = strtoupper(trim($parts[2]));
                    $room = trim($parts[3]);

                    $resolvedTeacher = $this->resolveTeacher($initial, $facultyMapping, $deptTeachers, $allTeachers);

                    $results[] = [
                        'day' => $day,
                        'time_slot' => $timeSlot,
                        'class_name' => $classInfo['name'],
                        'class_year' => $classInfo['year'],
                        'class_division' => $classInfo['division'],
                        'subject' => "{$subj} (Batch {$batch})",
                        'faculty_id' => $resolvedTeacher['id'],
                        'faculty_initials' => $initial,
                        'faculty_name' => $resolvedTeacher['name'] ?? $initial,
                        'room' => !empty($room) ? $room : ($classInfo['room'] ?: 'TBD'),
                        'is_break' => false,
                        'raw_content' => $line,
                    ];
                }
            }
            return $results;
        }

        $fullText = implode(' ', $lines);

        $initial = null;
        if (preg_match('/\(([A-Za-z0-9\/\s\-]+)\)/', $fullText, $m)) {
            $initial = strtoupper(trim($m[1]));
        }

        // Extract Room (e.g. 312, B21, 412, 013B, CA11, etc.)
        $room = $classInfo['room'];
        if (preg_match('/\b(B\d{1,3}|\d{3}[A-Za-z]?|CA\d{1,2}|LAB\d?|H\d{2})\b/i', $fullText, $rm)) {
            $room = strtoupper(trim($rm[1]));
        }

        // Extract Subject
        $subject = $fullText;
        if ($initial) {
            $subject = str_replace("({$initial})", '', $subject);
            $subject = str_replace("({$m[1]})", '', $subject);
        }
        if ($room && $room !== $classInfo['room']) {
            $subject = str_replace($room, '', $subject);
        }
        $subject = trim(preg_replace('/\s+/', ' ', $subject));
        if (empty($subject)) {
            $subject = $fullText;
        }

        $resolvedTeacher = $this->resolveTeacher($initial, $facultyMapping, $deptTeachers, $allTeachers);

        $results[] = [
            'day' => $day,
            'time_slot' => $timeSlot,
            'class_name' => $classInfo['name'],
            'class_year' => $classInfo['year'],
            'class_division' => $classInfo['division'],
            'subject' => $subject,
            'faculty_id' => $resolvedTeacher['id'],
            'faculty_initials' => $initial,
            'faculty_name' => $resolvedTeacher['name'] ?? ($initial ?: '—'),
            'room' => !empty($room) ? $room : 'TBD',
            'is_break' => false,
            'raw_content' => $fullText,
        ];

        return $results;
    }

    /**
     * Resolve initials to a teacher User in the database.
     */
    protected function resolveTeacher(?string $initial, array $facultyMapping, $deptTeachers, $allTeachers): array
    {
        if (empty($initial)) {
            return [
                'id' => null,
                'name' => null,
            ];
        }

        $cleanInitial = strtoupper(trim($initial));

        // 1. Check Faculty Mapping sheet
        if (isset($facultyMapping[$cleanInitial])) {
            $mapped = $facultyMapping[$cleanInitial];
            if (!empty($mapped['email'])) {
                $user = $allTeachers->firstWhere('email', $mapped['email']);
                if ($user) {
                    return ['id' => $user->id, 'name' => $user->name];
                }
            }
            if (!empty($mapped['name'])) {
                $user = $allTeachers->first(function ($u) use ($mapped) {
                    return strcasecmp($u->name, $mapped['name']) === 0 ||
                           Str::contains(strtolower($u->name), strtolower($mapped['name']));
                });
                if ($user) {
                    return ['id' => $user->id, 'name' => $user->name];
                }
            }
        }

        // 2. Check Initials match on department teachers
        foreach ($deptTeachers as $teacher) {
            if (strtoupper($teacher->initials()) === $cleanInitial) {
                return ['id' => $teacher->id, 'name' => $teacher->name];
            }
        }

        // 3. Check Initials match on any teacher
        foreach ($allTeachers as $teacher) {
            if (strtoupper($teacher->initials()) === $cleanInitial) {
                return ['id' => $teacher->id, 'name' => $teacher->name];
            }
        }

        // 4. Return unmapped
        return [
            'id' => null,
            'name' => null,
        ];
    }

    /**
     * Persist parsed timetable records into the database.
     */
    public function importLectures(int $departmentId, array $lectures, bool $clearExisting = true): array
    {
        return DB::transaction(function () use ($departmentId, $lectures, $clearExisting) {
            $dept = Department::findOrFail($departmentId);

            $classCache = [];
            $classRooms = ClassRoom::where('department_id', $departmentId)->get();

            foreach ($lectures as $lec) {
                $cName = $lec['class_name'];
                $cYear = $lec['class_year'];
                $cDiv = $lec['class_division'];

                if (!isset($classCache[$cName])) {
                    $found = $classRooms->first(function ($cr) use ($cName, $cYear, $cDiv) {
                        return strcasecmp($cr->name, $cName) === 0 ||
                               strcasecmp("{$cr->year} {$cr->name}", $cName) === 0 ||
                               strcasecmp("{$cr->year}-{$cr->name}", $cName) === 0 ||
                               (strcasecmp($cr->year, $cYear) === 0 && strcasecmp($cr->name, $cDiv) === 0);
                    });

                    if (!$found) {
                        $found = ClassRoom::create([
                            'department_id' => $departmentId,
                            'name' => $cDiv,
                            'year' => $cYear,
                        ]);
                        $classRooms->push($found);
                    }

                    $classCache[$cName] = $found;
                }
            }

            if ($clearExisting) {
                $deptClassIds = $classRooms->pluck('id')->toArray();
                if (!empty($deptClassIds)) {
                    Timetable::whereIn('class_room_id', $deptClassIds)->delete();
                }
            }

            $defaultTeacher = User::where('role', 'teacher')
                ->where('department_id', $departmentId)
                ->first() ?? User::where('role', 'teacher')->first();

            $insertedCount = 0;
            foreach ($lectures as $lec) {
                $classRoom = $classCache[$lec['class_name']] ?? null;
                if (!$classRoom) {
                    continue;
                }

                $facultyId = (!empty($lec['is_break'])) ? null : ($lec['faculty_id'] ?? null);

                Timetable::create([
                    'day' => $lec['day'],
                    'time_slot' => $lec['time_slot'],
                    'subject' => $lec['subject'],
                    'room' => $lec['room'] ?: 'TBD',
                    'class_room_id' => $classRoom->id,
                    'faculty_id' => $facultyId,
                ]);

                $insertedCount++;
            }

            return [
                'total_inserted' => $insertedCount,
                'total_classes' => count($classCache),
                'department_name' => $dept->name,
            ];
        });
    }

    /**
     * Generate a CSV / downloadable template of the Master Timetable structure.
     */
    public function generateCsvTemplate(): string
    {
        $output = fopen('php://temp', 'r+');

        fputcsv($output, ['MASTER TIME TABLE 2025-2026 (ODD SEM)']);
        fputcsv($output, []);

        fputcsv($output, ['MONDAY']);
        fputcsv($output, ['ROOMS', 'B21', '212', '213', '312', '313', '117', '412', '413', '514', '515', '516']);
        fputcsv($output, ['TIME/CLASS', 'D6AD A', 'D6AD B', 'D6', 'D7A', 'D7B', 'D8', 'D9A', 'D9B', 'D10A', 'D10B', 'D10C']);
        fputcsv($output, ['08:30-09:30', 'CSS (NCN) B21', 'PCE TUT (POK) CA11', 'DS/B/YP/209', 'DSGT (PN) 312', 'DS/A/208/RH', 'DE(JR) 117', 'Maths (JD) 412', 'FM (CJ) 413', 'DBMS 514 (VP)', 'Devops 515 (ARK)', 'DSA 516 (DK)']);
        fputcsv($output, ['09:30-10:30', 'FDS (KUS) B21', '', 'OS/A/AK/206', 'Maths (MD) 312', 'DBMS (PLS) 301', 'Maths (NM) 117', 'DSD (MV) 412', 'Maths (AJ) 413', 'DSA 514 (RJ)', 'DSA 515 (KJ)', 'DCCN 516 (VM)']);
        fputcsv($output, ['10:30-11:30', 'PGT (NN) B21', '', 'MPMC/C/AJV/201', 'FM (CJ) 312', 'FM (DM) 013B', 'SAR(MK) 117', 'PCE (SUS) 412', 'NT (JU) 413', 'Devops 514 (PS)', 'DBMS 515 (RUL)', 'DBMS/509/C/KJ']);
        fputcsv($output, ['11:30-12:30', 'DS (POV) B21', '', 'DS (YP) 213', 'DS (RJO) 312', 'DS (RH) B32', 'AEN(PAM) 117', 'CNC (ARS) 412', 'EDC (CJ) 413', 'FM 514 (AC)', 'Devops 515 (ARK)', 'DCCN/513/A/VM']);
        fputcsv($output, ['12:30-01:30', 'BREAK', 'BREAK', 'BREAK', 'BREAK', 'BREAK', 'BREAK', 'BREAK', 'BREAK', 'BREAK', 'BREAK', 'BREAK']);
        fputcsv($output, ['01:30-02:30', '', 'FM (MD) 212', 'OS (AK) 213', 'DS/A/303/RJO', 'DSGT (SKA) 312', 'Maths for AIML (SM) 313', '', 'CNC (RN) 413', 'PCE-II 514 (Dinesh)', '', '']);
        fputcsv($output, ['02:30-03:30', 'DBMS/B/SM/210', 'Project', '', 'DBMS/B/301/MM', '', '', 'PCE (Tut) CA3', 'DSD/A/MV/401', 'DBMS/509/A/RUL', 'PCE (tut) 515', '']);
        fputcsv($output, []);

        fputcsv($output, ['TUESDAY']);
        fputcsv($output, ['ROOMS', 'B21', '212', '213', '312', '313', '117', '412', '413', '514', '515', '516']);
        fputcsv($output, ['TIME/CLASS', 'D6AD A', 'D6AD B', 'D6', 'D7A', 'D7B', 'D8', 'D9A', 'D9B', 'D10A', 'D10B', 'D10C']);
        fputcsv($output, ['08:30-09:30', 'DS (AA)', '', '', 'Maths for AIML', 'DSGT (SKA)', 'SOM/RY/117', '', 'DSD (MD)', 'DBMS 514', 'DCCN 515', 'DSA 516']);
        fputcsv($output, ['09:30-10:30', 'FDS (KUS)', '', '', 'DSGT (PN)', 'DS (RH)', 'SOM/RY/117', 'DSD (MV)', 'Maths (AJ)', 'DSA 514', 'DSA 515', 'DCCN 516']);
        fputcsv($output, ['10:30-11:30', 'PCE TUT', '', '', 'FM (CJ)', 'FM (DM)', 'MATHS (NM)', 'PCE (SUS)', 'NT (JU)', 'Devops 514', 'DBMS 515', 'DBMS (KJ)']);
        fputcsv($output, ['11:30-12:30', 'CSS (NCN)', '', '', 'DS (RJO)', 'DS (RH)', 'AEN (PAM)', 'CNC (ARS)', 'EDC (CJ)', 'FM (AC)', 'Devops (ARK)', 'Devops (BRUK)']);
        fputcsv($output, ['12:30-01:30', 'BREAK', 'BREAK', 'BREAK', 'BREAK', 'BREAK', 'BREAK', 'BREAK', 'BREAK', 'BREAK', 'BREAK', 'BREAK']);
        fputcsv($output, ['01:30-02:30', '', 'FM (MD)', 'OS (AK)', '', 'DSGT (SKA)', 'Maths (SM)', '', 'CNC (RN)', 'PCE-II', '', '']);
        fputcsv($output, ['02:30-03:30', '', '', '', '', '', '', '', '', '', '', '']);

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }
}
