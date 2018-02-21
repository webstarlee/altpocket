<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel Ratchet Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can define the default settings for Laravel Ratchet.
    |
    */

      'class'           => \Askedio\LaravelRatchet\PusherExample::class,
      'host'            => '0.0.0.0',
      'port'            => '8080',
      'connectionLimit' => 5000,
      'throttle'        => [
                              'onOpen'    => '5:1',
                              'onMessage' => '20:1',
                           ],
      'abortOnMessageThrottle' => false,
      'blackList'              => collect([]),
      'zmq'                    => [
          'host' => '37.139.1.10',
          'port' => 5555,
        ],
  ];
