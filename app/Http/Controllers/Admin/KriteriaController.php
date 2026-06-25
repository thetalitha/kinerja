<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KriteriaController extends Controller
{
    public function index()
    {
        $kriteria = DB::table('kriteria')
            ->whereNull('parent_id')
            ->orderBy('urutan')
            ->orderBy('id')
            ->get();

        $kriteria = $kriteria->map(function ($k) {
            $k->sub_kriteria = DB::table('kriteria')
                ->where('parent_id', $k->id)
                ->orderBy('urutan')
                ->orderBy('id')
                ->get();

            $k->sub_kriteria = $k->sub_kriteria->map(function ($sub) {
                $sub->skala = DB::table('skala_linguistik')
                    ->where('kriteria_id', $sub->id)
                    ->orderBy('urutan')
                    ->get();
                return $sub;
            });

            $k->skala = DB::table('skala_linguistik')
                ->where('kriteria_id', $k->id)
                ->orderBy('urutan')
                ->get();

            return $k;
        });

        return view('admin.kriteria.index', compact('kriteria'));
    }

    public function store(Request $request)
    {
        $hasSub = $request->input('has_sub_kriteria') === '1';
        $isLinguistik = !$hasSub && $request->tipe_input === 'linguistik';

        $request->validate([
            'nama'             => 'required|string|max:255',
            'has_sub_kriteria' => 'required',
            'tipe_input'       => 'nullable|in:numerik,linguistik','persentase',
            'tipe_nilai'       => 'nullable|in:benefit, cost',
            'input_min'        => 'nullable|numeric',
            'input_max'        => 'nullable|numeric',
            'skala'            => $isLinguistik ? 'required|array|min:2' : 'nullable|array',
            'skala.*.label'    => $isLinguistik ? 'required|string|max:100' : 'nullable|string|max:100',
        ]);


        $hasSub = $request->input('has_sub_kriteria') === '1';

        $id = DB::table('kriteria')->insertGetId([
            'nama'             => $request->nama,
            'parent_id'        => null,
            'has_sub_kriteria' => $hasSub,
            'tipe_input'       => $hasSub ? null : $request->tipe_input,
            'tipe_nilai'       => (!$hasSub && in_array($request->tipe_input, ['numerik', 'persentase'])) ? $request->tipe_nilai : null,
            'input_min'        => (!$hasSub && $request->tipe_input === 'numerik') ? $request->input_min : null,
            'input_max'        => (!$hasSub && $request->tipe_input === 'numerik') ? $request->input_max : null,
            'urutan'           => DB::table('kriteria')->whereNull('parent_id')->count() + 1,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        if (!$hasSub && $request->tipe_input === 'linguistik' && $request->skala) {
            foreach ($request->skala as $i => $skala) {
                DB::table('skala_linguistik')->insert([
                    'kriteria_id' => $id,
                    'label'       => $skala['label'],
                    'urutan'      => $i + 1,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        }

        if ($hasSub) {
            return redirect()->route('admin.kriteria.index')
                ->with('open_sub_modal', $id)
                ->with('open_sub_nama', $request->nama);
        }

        return redirect()->route('admin.kriteria.index')
            ->with('success', 'Kriteria berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $kriteria = DB::table('kriteria')->where('id', $id)->first();
        if (!$kriteria) abort(404);

        $hasSub = (bool) $kriteria->has_sub_kriteria;
        $isLinguistik = !$hasSub && $request->tipe_input === 'linguistik';

        $request->validate([
            'nama'          => 'required|string|max:255',
            'tipe_input'    => 'nullable|in:numerik,linguistik','persentase',
            'tipe_nilai'    => 'nullable|in:benefit, cost',
            'input_min'     => 'nullable|numeric',
            'input_max'     => 'nullable|numeric',
            'skala'         => $isLinguistik ? 'required|array|min:2' : 'nullable|array',
            'skala.*.label' => $isLinguistik ? 'required|string|max:100' : 'nullable|string|max:100',
        ]);

        $hasSub = (bool) $kriteria->has_sub_kriteria;

        DB::table('kriteria')->where('id', $id)->update([
            'nama'       => $request->nama,
            'tipe_input' => $hasSub ? null : $request->tipe_input,
            'tipe_nilai' => (!$hasSub && in_array($request->tipe_input, ['numerik', 'persentase'])) ? $request->tipe_nilai : null,
            'input_min'  => (!$hasSub && $request->tipe_input === 'numerik') ? $request->input_min : null,
            'input_max'  => (!$hasSub && $request->tipe_input === 'numerik') ? $request->input_max : null,
            'updated_at' => now(),
        ]);

        if (!$hasSub && $request->tipe_input === 'linguistik') {
            DB::table('skala_linguistik')->where('kriteria_id', $id)->delete();
            if ($request->skala) {
                foreach ($request->skala as $i => $skala) {
                    DB::table('skala_linguistik')->insert([
                        'kriteria_id' => $id,
                        'label'       => $skala['label'],
                        'urutan'      => $i + 1,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ]);
                }
            }
        } elseif (!$hasSub && $request->tipe_input === 'numerik') {
            DB::table('skala_linguistik')->where('kriteria_id', $id)->delete();
        }

        return redirect()->route('admin.kriteria.index')
            ->with('success', 'Kriteria berhasil diperbarui.');
    }

    public function storeSubKriteria(Request $request, $parentId)
    {
        $parent = DB::table('kriteria')->where('id', $parentId)->whereNull('parent_id')->first();
            if (!$parent) abort(404);

            $isLinguistik = $request->tipe_input === 'linguistik';

            $request->validate([
                'nama'          => 'required|string|max:255',
                'tipe_input'    => 'required|in:numerik,linguistik,persentase',
                'tipe_nilai'    => 'nullable|in:benefit,cost',
                'input_min'     => 'nullable|numeric',
                'input_max'     => 'nullable|numeric',
                'skala'         => $isLinguistik ? 'required|array|min:2' : 'nullable|array',
                'skala.*.label' => $isLinguistik ? 'required|string|max:100' : 'nullable|string|max:100',
            ]);

        $id = DB::table('kriteria')->insertGetId([
            'nama'             => $request->nama,
            'parent_id'        => $parentId,
            'has_sub_kriteria' => false,
            'tipe_input'       => $request->tipe_input,
            'tipe_nilai'       => in_array($request->tipe_input, ['numerik', 'persentase']) ? $request->tipe_nilai : null,
            'input_min'        => $request->tipe_input === 'numerik' ? $request->input_min : null,
            'input_max'        => $request->tipe_input === 'numerik' ? $request->input_max : null,
            'urutan'           => DB::table('kriteria')->where('parent_id', $parentId)->count() + 1,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        if ($request->tipe_input === 'linguistik' && $request->skala) {
            foreach ($request->skala as $i => $skala) {
                DB::table('skala_linguistik')->insert([
                    'kriteria_id' => $id,
                    'label'       => $skala['label'],
                    'urutan'      => $i + 1,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        }

        // Redirect kembali dengan modal sub masih terbuka
        return redirect()->route('admin.kriteria.index')
            ->with('open_sub_modal', $parentId)
            ->with('open_sub_nama', $parent->nama)
            ->with('success', 'Sub kriteria berhasil ditambahkan.');
    }

    public function updateSub(Request $request, $id)
    {
        $sub = DB::table('kriteria')->where('id', $id)->whereNotNull('parent_id')->first();
        if (!$sub) abort(404);

        $isLinguistik = $request->tipe_input === 'linguistik';

        $request->validate([
            'nama'          => 'required|string|max:255',
            'tipe_input'    => 'required|in:numerik,linguistik,persentase',
            'tipe_nilai'    => 'nullable|in:benefit,cost',
            'input_min'     => 'nullable|numeric',
            'input_max'     => 'nullable|numeric',
            'skala'         => $isLinguistik ? 'required|array|min:2' : 'nullable|array',
            'skala.*.label' => $isLinguistik ? 'required|string|max:100' : 'nullable|string|max:100',
        ]);

        DB::table('kriteria')->where('id', $id)->update([
            'nama'       => $request->nama,
            'tipe_input' => $request->tipe_input,
             'tipe_nilai' => in_array($request->tipe_input, ['numerik', 'persentase'])
                    ? $request->tipe_nilai : null,
            'input_min'  => $request->tipe_input === 'numerik' ? $request->input_min : null,
            'input_max'  => $request->tipe_input === 'numerik' ? $request->input_max : null,
            'updated_at' => now(),
        ]);

        DB::table('skala_linguistik')->where('kriteria_id', $id)->delete();
        if ($request->tipe_input === 'linguistik' && $request->skala) {
            foreach ($request->skala as $i => $skala) {
                DB::table('skala_linguistik')->insert([
                    'kriteria_id' => $id,
                    'label'       => $skala['label'],
                    'urutan'      => $i + 1,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        }

        return redirect()->route('admin.kriteria.index')
            ->with('success', 'Sub kriteria berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $subs = DB::table('kriteria')->where('parent_id', $id)->pluck('id');
        if ($subs->count()) {
            DB::table('skala_linguistik')->whereIn('kriteria_id', $subs)->delete();
            DB::table('kriteria')->where('parent_id', $id)->delete();
        }
        DB::table('skala_linguistik')->where('kriteria_id', $id)->delete();
        DB::table('kriteria')->where('id', $id)->delete();

        return redirect()->route('admin.kriteria.index')
            ->with('success', 'Kriteria berhasil dihapus.');
    }

    public function destroySub($id)
    {
        $sub = DB::table('kriteria')->where('id', $id)->first();
        $parentId = $sub ? $sub->parent_id : null;
        $parent = $parentId ? DB::table('kriteria')->where('id', $parentId)->first() : null;

        DB::table('skala_linguistik')->where('kriteria_id', $id)->delete();
        DB::table('kriteria')->where('id', $id)->delete();

        // Kembali ke modal sub masih terbuka
        if ($parent) {
            return redirect()->route('admin.kriteria.index')
                ->with('open_sub_modal', $parentId)
                ->with('open_sub_nama', $parent->nama)
                ->with('success', 'Sub kriteria berhasil dihapus.');
        }

        return redirect()->route('admin.kriteria.index')
            ->with('success', 'Sub kriteria berhasil dihapus.');
    }
}