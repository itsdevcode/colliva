<?php
namespace App\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
class MarketplaceUpdate extends Notification implements ShouldQueue
{
 use Queueable;
 public function __construct(public string $message) { $this->afterCommit(); }
 public function via(object $notifiable): array { return ['mail', 'database']; }
 public function toMail(object $notifiable): MailMessage { return (new MailMessage)->subject('Your Shoutly workspace')->line($this->message)->action('Open workspace', route('collaborations')); }
 public function toArray(object $notifiable): array { return ['message' => $this->message]; }
}
