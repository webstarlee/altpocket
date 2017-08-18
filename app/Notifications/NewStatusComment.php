<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NewStatusComment extends Notification
{
  use Queueable;

  /**
   * Create a new notification instance.
   *
   * @return void
   */
  public function __construct($notification)
  {
      $this->notification = $notification;
      $this->category = "success";
  }

  /**
   * Get the notification's delivery channels.
   *
   * @param  mixed  $notifiable
   * @return array
   */
  public function via($notifiable)
  {
    return ['database', 'broadcast'];
  }

  public function toBroadcast($notifiable)
  {
      return new BroadcastMessage([
          'value' => $this->notification['data'],
          'category' => 'success'
      ]);
    }


  /**
   * Get the mail representation of the notification.
   *
   * @param  mixed  $notifiable
   * @return \Illuminate\Notifications\Messages\MailMessage
   */
   public function toMail($notifiable)
   {
       return (new MailMessage)
                   ->subject('New comment on Altpocket')
                   ->line('There has been a new comment on your')
                   ->action('Go to question', url('/question').'/'.$this->notification['question'])
                   ->line('Thank you for using Altpocket.');
   }

  /**
   * Get the array representation of the notification.
   *
   * @param  mixed  $notifiable
   * @return array
   */
   public function toArray($notifiable)
   {
       return [
           $this->notification
       ];
   }
}
