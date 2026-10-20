<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class PembayaranDiverifikasi extends Notification
{
    public function __construct(
        public int $idPeriksa,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'judul'      => 'Pembayaran lunas',
            'pesan'      => 'Pembayaran pemeriksaan Anda sudah diverifikasi admin dan berstatus lunas.',
            'id_periksa' => $this->idPeriksa,
        ];
    }
}