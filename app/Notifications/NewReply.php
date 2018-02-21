<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
class NewReply extends Notification
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
  }

  /**
   * Get the notification's delivery channels.
   *
   * @param  mixed  $notifiable
   * @return array
   */
  public function via($notifiable)
  {
    if($notifiable->email_notifications == "on"){
    return ['mail', 'database', 'broadcast'];
    } else {
    return ['database', 'broadcast'];
    }
  }
  public function toBroadcast($notifiable)
  {
      return new BroadcastMessage([
          'value' => 'There is a reply to your answer.'
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
       /*return (new MailMessage)
                   ->subject('New response on Altpocket')
                   ->line('There has been a new response on a question you have answered.')
                   ->action('Go to question', url('/question').'/'.$this->notification['question'])
                   ->line('Thank you for using Altpocket.');*/

                   return (new MailMessage)->view(
                       'emails.newreply', ['notification' => $this->notification]
                   )->subject('New Reply on: "'.$this->notification['question_title'].'"');
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
