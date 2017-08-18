<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Auth;

class ChatController extends Controller
{
    var $pusher;
    var $user;
    var $chatChannel;

    const DEFAULT_CHAT_CHANNEL = 'chat';

    public function __construct()
    {
        $this->pusher = App::make('pusher');
        $this->user = Session::get('user');
        $this->chatChannel = self::DEFAULT_CHAT_CHANNEL;
    }

    public function getIndex()
    {


        return view('chat', ['chatChannel' => $this->chatChannel]);
    }

    public function postMessage(Request $request)
    {
        $message = [
            'text' => e($request->input('chat_text')),
            'username' => Auth::user()->username,
            'avatar' => '/uploads/avatars/'.Auth::user()->id.'/'.Auth::user()->avatar,
            'timestamp' => (time()*1000)
        ];
        $this->pusher->trigger($this->chatChannel, 'new-message', $message);
    }
}
