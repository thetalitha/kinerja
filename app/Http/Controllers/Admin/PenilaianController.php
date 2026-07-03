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

        $totalKriteria = Kriteria::leaf()->count();

        $peserta = $peserta->map(function ($p) use ($totalKriteria) {
            $jumlahDinilai = Penilaian::where('user_id', $p->id)->count();
            $p->sudah_dinilai = $totalKriteria > 0 && $jumlahDinilai >= $totalKriteria;
            $p->jumlah_dinilai = $jumlahDinilai;
            $p->total_kriteria = $totalKriteria;
            return $p;
        });

        $kriteria = Kriteria::utama()
            ->with(['subKriteria.skalaLinguistik', 'skalaLinguistik'])
            ->orderBy('urutan')
            ->get();

        return view('admin.penilaian.index', compact('peserta', 'kriteria', 'totalKriteria'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'   => 'required|exists:users,id',
            'penilaian' => 'required|array',
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

            if (in_array($kriteria->tipe_input, ['numerik', 'persentase'])) {
                $data['nilai_numerik'] = is_numeric($nilai) ? (float) $nilai : null;
            } elseif ($kriteria->tipe_input === 'linguistik') {
                $data['nilai_linguistik'] = $nilai ?: null;
            }

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

    public function getSyncLogbook($userId)
    {
        // Ambil data peserta untuk dapat total hari magang
        $peserta = DB::table('peserta')->where('id', $userId)->first();

        if (!$peserta || !$peserta->periode_start || !$peserta->periode_end) {
            return response()->json(['error' => 'Data periode peserta tidak lengkap.'], 422);
        }

        // Hitung total hari kerja (senin-jumat) dalam periode magang
        $start = \Carbon\Carbon::parse($peserta->periode_start);
        $end   = \Carbon\Carbon::parse($peserta->periode_end);

        // Total hari logbook yang sudah diapprove
        $totalLogbook = DB::table('logbook')
            ->where('user_id', $userId)
            ->where('is_approved', true)
            ->count();

        if ($totalLogbook === 0) {
            return response()->json(['error' => 'Belum ada logbook yang disetujui.'], 422);
        }

        $hadir = DB::table('logbook')
            ->where('user_id', $userId)
            ->where('is_approved', true)
            ->whereIn('keterangan', ['offline_kantor', 'online'])
            ->count();

        $sakit = DB::table('logbook')
            ->where('user_id', $userId)
            ->where('is_approved', true)
            ->where('keterangan', 'sakit')
            ->count();

        $izin = DB::table('logbook')
            ->where('user_id', $userId)
            ->where('is_approved', true)
            ->where('keterangan', 'izin')
            ->count();

        $alpha = DB::table('logbook')
            ->where('user_id', $userId)
            ->where('is_approved', true)
            ->where('keterangan', 'alpha')
            ->count();

        return response()->json([
            'total'  => $totalLogbook,
            'persen' => [
                'hadir' => round(($hadir / $totalLogbook) * 100, 2),
                'sakit' => round((1 - $sakit / $totalLogbook) * 100, 2),
                'izin'  => round((1 - $izin  / $totalLogbook) * 100, 2),
                'alpha' => round((1 - $alpha / $totalLogbook) * 100, 2),
            ],
            'raw' => [
                'hadir' => $hadir,
                'sakit' => $sakit,
                'izin'  => $izin,
                'alpha' => $alpha,
            ]
        ]);
    }
}