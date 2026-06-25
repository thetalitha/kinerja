<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peserta extends Model
{
    protected $table = 'peserta';
    protected $primaryKey = 'peserta_id';

    protected $fillable = [
        'id',
        'nim',
        'email',
        'institut',
        'fungsi',
        'periode_start',
        'periode_end',
    ];

    protected $casts = [
        'periode_start' => 'date',
        'periode_end'   => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }
}