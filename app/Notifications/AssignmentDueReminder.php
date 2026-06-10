<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssignmentDueReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public $assignment,
        public $course
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('Assignment Due Soon: :assignment', ['assignment' => $this->assignment->title]))
            ->greeting(__('Hello :name', ['name' => $notifiable->name]))
            ->line(__('This is a reminder that the assignment ":assignment" for course ":course" is due on :due_date.', [
                'assignment' => $this->assignment->title,
                'course' => $this->course->title,
                'due_date' => $this->assignment->due_at->timezone(config('app.timezone'))->format('M j, Y g:i A'),
            ]))
            ->action(__('View Assignment'), route('student.courses.assignments.show', [$this->course, $this->assignment]))
            ->line(__('Thank you for using our application!'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'assignment_id' => $this->assignment->id,
            'course_id' => $this->course->id,
            'assignment_title' => $this->assignment->title,
            'course_title' => $this->course->title,
            'due_date' => $this->assignment->due_at->toIso8601String(),
            'type' => 'assignment_due_reminder',
        ];
    }
}
