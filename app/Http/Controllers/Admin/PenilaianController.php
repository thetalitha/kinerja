<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kriteria;
use App\Models\Penilaian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenilaianController extends Controller
{
    public function index()
    {
        // Ambil semua peserta aktif beserta detail
        $peserta = DB::table('users as u')
            ->join('peserta as p', 'p.id', '=', 'u.id')
            ->leftJoin('room_user as ru', function ($join) {
                $join->on('ru.user_id', '=', 'u.id')
                     ->where('ru.status', 'active');
            })
            ->leftJoin('room as r', 'r.room_id', '=', 'ru.room_id')
            ->leftJoin('users as m', 'm.id', '=', 'r.mentor_id')
            ->where('u.role', 'peserta')
            ->where('u.is_active', true)
            ->select(
                'u.id', 'u.nama', 'u.foto_profil',
                'p.nim', 'p.institut', 'p.fungsi',
                'p.periode_start', 'p.periode_end',
                'r.nama_room', 'm.nama as nama_mentor'
            )
            ->orderBy('u.nama')
            ->get();

        // Hitung jumlah leaf kriteria (yang harus diisi)
        $totalKriteria = Kriteria::leaf()->count();

        // Tandai peserta yang sudah dinilai (semua leaf kriteria sudah ada nilainya)
        $peserta = $peserta->map(function ($p) use ($totalKriteria) {
            $jumlahDinilai = Penilaian::where('user_id', $p->id)->count();
            $p->sudah_dinilai = $totalKriteria > 0 && $jumlahDinilai >= $totalKriteria;
            $p->jumlah_dinilai = $jumlahDinilai;
            $p->total_kriteria = $totalKriteria;
            return $p;
        });

        // Ambil struktur kriteria untuk form penilaian
        $kriteria = Kriteria::utama()
            ->with(['subKriteria.skalaLinguistik', 'skalaLinguistik'])
            ->orderBy('urutan')
            ->get();

        return view('admin.penilaian.index', compact('peserta', 'kriteria', 'totalKriteria'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'    => 'required|exists:users,id',
            'penilaian'  => 'required|array',
        ]);

        $userId = $request->user_id;

        foreach ($request->penilaian as $kriteriaId => $nilai) {
            $kriteria = Kriteria::find($kriteriaId);
            if (!$kriteria) continue;

            $data = [
                'user_id'          => $userId,
                'kriteria_id'      => $kriteriaId,
                'nilai_numerik'    => null,
                'nilai_linguistik' => null,
                'updated_at'       => now(),
            ];

            if ($kriteria->tipe_input === 'numerik') {
                $data['nilai_numerik'] = is_numeric($nilai) ? (float) $nilai : null;
            } elseif ($kriteria->tipe_input === 'linguistik') {
                $data['nilai_linguistik'] = $nilai ?: null;
            }

            // Upsert — update kalau sudah ada, insert kalau belum
            Penilaian::updateOrCreate(
                ['user_id' => $userId, 'kriteria_id' => $kriteriaId],
                $data
            );
        }

        return redirect()->route('admin.penilaian.index')
            ->with('success', 'Penilaian berhasil disimpan.');
    }

    public function getPenilaian($userId)
    {
        $penilaian = Penilaian::where('user_id', $userId)
            ->get()
            ->keyBy('kriteria_id');

        return response()->json($penilaian);
    }
}