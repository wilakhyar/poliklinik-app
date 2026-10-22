<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class AntrianDiperbarui implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(
        public int $idJadwal,
        public string|int $nomor,
    ) {}

    public function broadcastOn(): array
    {
        return [new Channel('antrian')];
    }

    public function broadcastAs(): string
    {
        return 'AntrianDiperbarui';
    }

    public function broadcastWith(): array
    {
        return [
            'id_jadwal' => $this->idJadwal,
            'nomor'     => $this->nomor,
        ];
    }
}