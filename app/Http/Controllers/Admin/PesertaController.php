<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PesertaController extends Controller
{
    public function index()
    {
        $peserta = DB::table('users as u')
        ->leftjoin('peserta as p', 'p.id', '=', 'u.id')
        ->leftJoin('room_user as ru', function ($join) {
            $join->on('ru.user_id', '=', 'u.id')
                ->where('ru.status', 'active');
        })
        ->leftJoin('room as r', 'r.room_id', '=', 'ru.room_id')
        ->leftJoin('users as m', 'm.id', '=', 'r.mentor_id')
        ->where('u.role', 'peserta')
        ->select(
            'u.id', 'u.nama', 'u.foto_profil',
            'p.peserta_id', 'p.nim', 'p.email', 'p.institut', 'p.fungsi',
            'p.periode_start', 'p.periode_end',
            'r.room_id', 'r.nama_room', 'm.nama as nama_mentor'
        )
        ->orderBy('u.nama')
        ->get();

        $rooms = DB::table('room')
            ->where('is_active', true)
            ->orderBy('nama_room')
            ->select('room_id', 'nama_room')
            ->get();

        return view('admin.peserta.index', compact('peserta', 'rooms'));
    }

    public function detail($id)
    {
        // Info dasar
        $peserta = DB::table('users as u')
            ->join('peserta as p', 'p.id', '=', 'u.id')
            ->leftJoin('room_user as ru', function ($join) {
                $join->on('ru.user_id', '=', 'u.id')
                     ->where('ru.status', 'active');
            })
            ->leftJoin('room as r', 'r.room_id', '=', 'ru.room_id')
            ->leftJoin('users as m', 'm.id', '=', 'r.mentor_id')
            ->where('u.id', $id)
            ->where('u.role', 'peserta')
            ->select(
                'u.id', 'u.nama', 'u.foto_profil',
                'p.peserta_id', 'p.nim', 'p.email', 'p.institut', 'p.fungsi',
                'p.periode_start', 'p.periode_end',
                'r.room_id', 'r.nama_room', 'm.nama as nama_mentor'
            )
            ->first();

        if (!$peserta) {
            return response()->json(['error' => 'Peserta tidak ditemukan.'], 404);
        }

        // Kehadiran dari logbooks
        $totalLogbook = DB::table('logbook')
            ->where('user_id', $id)
            ->where('is_approved', true)
            ->count();

        $hadirCount = DB::table('logbook')
            ->where('user_id', $id)
            ->where('is_approved', true)
            ->whereIn('keterangan', ['offline_kantor', 'online'])
            ->count();

        $alphaCount = DB::table('logbook')
            ->where('user_id', $id)
            ->where('is_approved', true)
            ->where('keterangan', 'alpha')
            ->count();

        $izinSakitCount = DB::table('logbook')
            ->where('user_id', $id)
            ->where('is_approved', true)
            ->whereIn('keterangan', ['izin', 'sakit'])
            ->count();

        $persenKehadiran = $totalLogbook > 0
            ? round(($hadirCount / $totalLogbook) * 100, 1)
            : 0;

        // Tugas dari submission
        $totalTugas = $peserta->room_id
            ? DB::table('task')->where('room_id', $peserta->room_id)->count()
            : 0;

        $tugasDikumpul = DB::table('submission')
            ->where('user_id', $id)
            ->whereIn('status', ['pending', 'late', 'graded'])
            ->count();

        $tugasTepat = DB::table('submission')
            ->where('user_id', $id)
            ->where('status', 'graded')
            ->count();

        $rataRataNilai = DB::table('submission')
            ->where('user_id', $id)
            ->where('status', 'graded')
            ->whereNotNull('nilai')
            ->avg('nilai');

        // Evaluasi dari mentor
        $evaluasi = DB::table('evaluasi')
            ->where('user_id', $id)
            ->latest()
            ->first();

        return response()->json([
            'peserta'          => $peserta,
            'kehadiran' => [
                'total'   => $totalLogbook,
                'hadir'   => $hadirCount,
                'alpha'   => $alphaCount,
                'izin_sakit' => $izinSakitCount,
                'persen'  => $persenKehadiran,
            ],
            'tugas' => [
                'total'       => $totalTugas,
                'dikumpul'    => $tugasDikumpul,
                'tepat_waktu' => $tugasTepat,
                'rata_nilai'  => $rataRataNilai ? round($rataRataNilai, 1) : null,
            ],
            'evaluasi' => $evaluasi,
        ]);
    }
}