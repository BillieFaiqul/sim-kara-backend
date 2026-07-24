<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karya extends Model
{
    use HasFactory;

    protected $table = 'karya';

    protected $fillable = [
        'user_id',
        'judul',
        'jenis',
        'level',
        'pencapaian',
        'tahun',
        'deskripsi',
        'file_path',
        'file_pendukung_path',
        'status',
        'alasan_reject',
        'tanggal_submit',
    ];

    protected $casts = [
        'tanggal_submit' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getJenisAttribute($value)
    {
        return $value;
    }

    public function getLevelAttribute($value)
    {
        return $value;
    }

    public function getStatusAttribute($value)
    {
        return $value;
    }
}
