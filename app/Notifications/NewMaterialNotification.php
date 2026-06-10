<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMaterialNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public $module,
        public $course
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('New Material Available: :module', ['module' => $this->module->title]))
            ->greeting(__('Hello :name', ['name' => $notifiable->name]))
            ->line(__('A new learning material ":module" has been added to your course ":course".', [
                'module' => $this->module->title,
                'course' => $this->course->title,
            ]))
            ->action(__('View Course'), route('student.courses.show', $this->course))
            ->line(__('Thank you for using our application!'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'module_id' => $this->module->id,
            'course_id' => $this->course->id,
            'module_title' => $this->module->title,
            'course_title' => $this->course->title,
            'type' => 'new_material',
        ];
    }
}
