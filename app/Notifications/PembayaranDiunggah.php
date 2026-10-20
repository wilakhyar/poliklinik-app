<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class PembayaranDiunggah extends Notification
{
    public function __construct(
        public int $idPeriksa,
        public string $namaPasien,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'judul'      => 'Bukti pembayaran diunggah',
            'pesan'      => "Pasien {$this->namaPasien} mengunggah bukti pembayaran.",
            'id_periksa' => $this->idPeriksa,
        ];
    }
}