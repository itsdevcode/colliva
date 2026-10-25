<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SupportTicketUpdate extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $reference, public string $message, public bool $hasAccount)
    {
        $this->afterCommit();
    }

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)->subject('Colliva support · '.$this->reference)->line($this->message)->line('Ticket reference: '.$this->reference);
        if ($this->hasAccount) {
            $mail->action('View your ticket', route('support.ticket', $this->reference));
        } else {
            $mail->line('For follow-ups, submit a support request with this reference. Guest tickets are not visible in account ticket history.');
        }

        return $mail;
    }
}
