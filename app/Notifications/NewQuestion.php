<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NewQuestion extends Notification
{
    use Queueable;

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
      return ['mail', 'database', 'broadcast'];
      }
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'value' => 'There is a new question on the Help Desk.'
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
                     ->subject('New Question: "'.$this->notification['question_title'])
                     ->line('There is a new question on the help desk.')
                     ->line('The priority level is: '.$this->notification['priority'])
                     ->line('"'.$this->notification['question_description'].'"')
                     ->action('Go to question', url('/question').'/'.$this->notification['question']);*/

     return (new MailMessage)->view(
         'emails.newquestion', ['notification' => $this->notification]
     )->subject('New Question: "'.$this->notification['question_title'].'"');
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
