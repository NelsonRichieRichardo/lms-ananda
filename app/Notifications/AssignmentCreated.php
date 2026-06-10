<?php

namespace App\Notifications;

use App\Models\Assignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class AssignmentCreated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Assignment $assignment
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): DatabaseMessage
    {
        return new DatabaseMessage([
            'title' => __('New Assignment'),
            'message' => __('A new assignment ":title" has been added to :course.', [
                'title' => $this->assignment->title,
                'course' => $this->assignment->course->title,
            ]),
            'assignment_id' => $this->assignment->id,
            'course_id' => $this->assignment->course_id,
        ]);
    }
}
