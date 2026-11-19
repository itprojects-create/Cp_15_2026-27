<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Timetable extends Model
{
    protected $guarded = [];

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class);
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(User::class, 'faculty_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'faculty_id');
    }

    public static function timeToMinutes(string $timeStr): int
    {
        $timeStr = trim($timeStr);
        $timeStr = preg_replace('/(\d{1,2})\.(\d{2})/', '$1:$2', $timeStr);
        $isPm = (bool) preg_match('/pm/i', $timeStr);
        $isAm = (bool) preg_match('/am/i', $timeStr);

        if (preg_match('/(\d{1,2}):(\d{2})/', $timeStr, $m)) {
            $h = (int) $m[1];
            $min = (int) $m[2];
            if ($isPm && $h < 12) {
                $h += 12;
            } elseif ($isAm && $h === 12) {
                $h = 0;
            } elseif (!$isPm && !$isAm) {
                // College daytime hours: 1 to 7 are PM
                if ($h >= 1 && $h <= 7) {
                    $h += 12;
                }
            }
            return $h * 60 + $min;
        }

        if (preg_match('/^(\d{1,2})/i', $timeStr, $m)) {
            $h = (int) $m[1];
            if ($isPm && $h < 12) {
                $h += 12;
            } elseif (!$isPm && !$isAm && $h >= 1 && $h <= 7) {
                $h += 12;
            }
            return $h * 60;
        }

        return 0;
    }

    public static function normalizeSlot(?string $slot): string
    {
        if (!$slot) {
            return '';
        }

        $slot = trim($slot);
        $slot = str_replace(['–', '—', ' to '], '-', $slot);
        $parts = explode('-', $slot);
        if (count($parts) !== 2) {
            return $slot;
        }

        $formatPart = function ($part) {
            $part = trim($part);
            $part = preg_replace('/(\d{1,2})\.(\d{2})/', '$1:$2', $part);

            if (preg_match('/(\d{1,2}):(\d{2})/i', $part, $m)) {
                $h = (int) $m[1];
                $min = (int) $m[2];
                if ($h >= 13) {
                    $h -= 12;
                }
                return sprintf('%02d:%02d', $h, $min);
            }

            if (preg_match('/(\d{1,2})/i', $part, $m)) {
                $h = (int) $m[1];
                if ($h >= 13) {
                    $h -= 12;
                }
                return sprintf('%02d:00', $h);
            }

            return $part;
        };

        return $formatPart($parts[0]) . '-' . $formatPart($parts[1]);
    }

    public static function hasConflict($day, $timeStart, $timeEnd, $classRoomId, $facultyId, $excludeId = null)
    {
        $query = self::where('day', $day);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        $entries = $query->get();

        $newStart = self::timeToMinutes($timeStart);
        $newEnd = self::timeToMinutes($timeEnd);

        if ($newStart <= 0 || $newEnd <= 0 || $newStart >= $newEnd) {
            return 'Invalid time range selected.';
        }

        foreach ($entries as $entry) {
            if (!str_contains($entry->time_slot, '-')) {
                continue;
            }

            [$startStr, $endStr] = explode('-', $entry->time_slot);
            $entryStart = self::timeToMinutes($startStr);
            $entryEnd = self::timeToMinutes($endStr);

            if ($entryStart <= 0 || $entryEnd <= 0 || $entryStart >= $entryEnd) {
                continue;
            }

            // Check if there is an overlap
            if (max($newStart, $entryStart) < min($newEnd, $entryEnd)) {
                // Check class conflict
                if ($entry->class_room_id == $classRoomId) {
                    return "The class is already allocated to another lecture ({$entry->subject}) during this time ({$entry->time_slot}).";
                }
                // Check teacher conflict
                if ($facultyId && $entry->faculty_id == $facultyId) {
                    return "The teacher is already allocated to another class ({$entry->classRoom?->year} {$entry->classRoom?->name} - {$entry->subject}) during this time ({$entry->time_slot}).";
                }
            }
        }

        return false;
    }
}
