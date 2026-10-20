<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    protected $fillable = [
        'id_periksa',
        'bukti_bayar',
        'status',
        'tgl_upload',
        'tgl_verifikasi',
    ];

    protected $casts = [
        'tgl_upload'     => 'datetime',
        'tgl_verifikasi' => 'datetime',
    ];

    public function periksa()
    {
        return $this->belongsTo(Periksa::class, 'id_periksa');
    }
}