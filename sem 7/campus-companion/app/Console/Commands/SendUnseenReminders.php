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

            foreach ($students as $student) {
                if (in_array($student->id, $readUserIds)) {
                    continue;
                }

                $message = "Reminder: You have an unread notice: '{$notice->title}'";

                // Check if already notified about this specific reminder
                $alreadyNotified = \App\Models\Notification::where('user_id', $student->id)
                    ->where('message', $message)
                    ->exists();

                if (!$alreadyNotified) {
                    \App\Models\Notification::create([
                        'user_id' => $student->id,
                        'message' => $message,
                    ]);
                    $remindersCount++;
                }
            }
        }

        $this->info("Completed! Dispatched {$remindersCount} reminder notifications.");
    }
}
