<?php

namespace App\Notifications;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class SubmissionGraded extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Submission $submission
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): DatabaseMessage
    {
        return new DatabaseMessage([
            'title' => __('Assignment Graded'),
            'message' => __('Your submission for ":assignment" has been graded. Grade: :grade', [
                'assignment' => $this->submission->assignment->title,
                'grade' => $this->submission->grade,
            ]),
            'assignment_id' => $this->submission->assignment_id,
            'course_id' => $this->submission->assignment->course_id,
        ]);
    }
}
