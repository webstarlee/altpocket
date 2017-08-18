<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Redirect;
use Alert;


class NotificationController extends Controller
{
    public function readNotification($id){
        $user = Auth::user();
        $notification = $user->notifications->where('id', $id)->first();
        if($notification){
            $notification->delete();
        } 
    }
    
    
    public function readAll(){
        $user = Auth::user();
        $notifications = $user->unreadNotifications;
        
        foreach($notifications as $notification){
            $notification->markAsRead();
        }
            Alert::success('All your notifications were marked as read.', 'Notifications marked as read');
            return Redirect::back();  
    }
    
    public function toggleEmail(){
        
        if(Auth::user()){
            $user = Auth::user();
            if($user->email_notifications == "on"){
                $user->email_notifications = 'off';
                $user->save();
                Alert::success('You have disabled email notifications.', 'Email notifications disabled');
                return Redirect::back();  
            } else {
                $user->email_notifications = 'on';
                $user->save();
                Alert::success('You have eanbled email notifications.', 'Email notifications enabled');
                return Redirect::back();  
            }
        }
        
        
        
        
        
    }
    
    
}
