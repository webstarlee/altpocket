<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
class NewFollower extends Notification
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

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {

      return (new MailMessage)->view(
          'emails.socials.newfollower', ['notification' => $this->notification]
      )->subject('New Follower on Altpocket');
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

     public function toBroadcast($notifiable)
      {
          return new BroadcastMessage([
              'value' => $this->notification['username'].' has started to follow you!',
              'category' => 'success'
          ]);
      }
}
