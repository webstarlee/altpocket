<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class Welcome extends Notification
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


     public function routeNotificationForMail()
      {
          return $this->notification['email'];
      }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
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
                         'emails.welcome', ['notification' => $this->notification]
                     )->subject('Welcome to Altpocket '.$this->notification['username'].'!');
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
