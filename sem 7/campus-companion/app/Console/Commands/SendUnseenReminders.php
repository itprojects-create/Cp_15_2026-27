<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendUnseenReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:send';

    protected $description = 'Send reminder notifications to students who have unread notices older than 2 days';

    public function handle()
    {
        $this->info('Starting unseen reminders dispatcher...');

        // Notices created between 2 and 7 days ago
        $notices = \App\Models\Notice::where('created_at', '<=', now()->subDays(2))
            ->where('created_at', '>=', now()->subDays(7))
            ->get();

        $remindersCount = 0;

        foreach ($notices as $notice) {
            // Find target students
            $studentsQuery = \App\Models\User::where('role', 'student');

            if ($notice->department_id) {
                $studentsQuery->where('department_id', $notice->department_id);
            }
            if ($notice->class_room_id) {
                $studentsQuery->where('class_room_id', $notice->class_room_id);
            }

            $students = $studentsQuery->get();

            // Find students who read the notice
            $readUserIds = \App\Models\NoticeRead::where('notice_id', $notice->id)
                ->pluck('user_id')
                ->toArray();

            $message = "Reminder: You have an unread notice: '{$notice->title}'";

            // Pre-fetch all user IDs who have already received this reminder
            $alreadyNotifiedUserIds = \App\Models\Notification::where('message', $message)
                ->pluck('user_id')
                ->flip()
                ->toArray();

            $batchToInsert = [];
            $now = now();

            foreach ($students as $student) {
                if (in_array($student->id, $readUserIds)) {
                    continue;
                }

                if (!isset($alreadyNotifiedUserIds[$student->id])) {
                    $batchToInsert[] = [
                        'user_id' => $student->id,
                        'message' => $message,
                        'read' => false,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                    $remindersCount++;
                }
            }

            if (!empty($batchToInsert)) {
                // Chunk insert to respect SQL parameter limits
                foreach (array_chunk($batchToInsert, 100) as $chunk) {
                    \App\Models\Notification::insert($chunk);
                }
            }
        }

        $this->info("Completed! Dispatched {$remindersCount} reminder notifications.");
    }
}
