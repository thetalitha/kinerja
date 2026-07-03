<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkalaLinguistik extends Model
{
    protected $table = 'skala_linguistik';

    protected $fillable = [
        'kriteria_id',
        'label',
        'definisi',
        'urutan',
    ];

    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class, 'kriteria_id');
    }
}