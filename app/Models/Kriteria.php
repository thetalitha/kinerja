<?php

namespace App\Models;

use App\Models\Penilaian;
use App\Models\SkalaLinguistik;
use Illuminate\Database\Eloquent\Model;

class Kriteria extends Model
{
    protected $table = 'kriteria';

    protected $fillable = [
        'nama',
        'definisi',
        'parent_id',
        'has_sub_kriteria',
        'tipe_input',
        'tipe_nilai',
        'sumber_data',
        'input_min',
        'input_max',
        'urutan',
    ];

    protected $casts = [
        'has_sub_kriteria' => 'boolean',
        'input_min'        => 'float',
        'input_max'        => 'float',
    ];

    public function parent()
    {
        return $this->belongsTo(Kriteria::class, 'parent_id');
    }

    public function subKriteria()
    {
        return $this->hasMany(Kriteria::class, 'parent_id')->orderBy('urutan');
    }

    public function skalaLinguistik()
    {
        return $this->hasMany(SkalaLinguistik::class, 'kriteria_id')->orderBy('urutan');
    }

    public function penilaian()
    {
        return $this->hasMany(Penilaian::class, 'kriteria_id');
    }

    // Hanya kriteria utama (tanpa parent)
    public function scopeUtama($query)
    {
        return $query->whereNull('parent_id');
    }

    // Hanya leaf node (yang bisa diisi nilai)
    public function scopeLeaf($query)
    {
        return $query->where('has_sub_kriteria', false);
    }
}