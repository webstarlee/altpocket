<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use App\Crypto;

class PriceEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

     protected $currency;
     protected $exchange;
     protected $price;
     protected $change;




    public function __construct($currency, $exchange, $price, $change)
    {
      $this->currency = $currency;
      $this->exchange = $exchange;
      $this->price = $price;
      $this->change = $change;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new channel('price-channel');
    }

    public function broadcastWith()
    {
    return [
      'currency' => $this->currency,
      'exchange' => $this->exchange,
      'price' => $this->price,
      'change' => $this->change
          ];
    }

    public function broadcastAs()
    {
        return 'PRICE_UPDATE';
    }
}
