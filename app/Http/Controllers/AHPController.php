<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Criteria;
use App\Models\Ahp;
use Illuminate\Support\Str;

class AHPController extends Controller
{
    public function index()
    {
        $criterias = Criteria::orderBy('code')->get();

        // Ambil semua nilai pairwise
        $pairs = Ahp::get();

        // Jika ada data → hitung, jika tidak biarkan null
        $result = $pairs->count() > 0
                ? $this->calculate($criterias, $pairs)
                : null;

        return view('pages.ahp', compact('criterias', 'pairs', 'result'));
    }

    public function store(Request $request)
    {
        $criteriaA = $request->criteria_a;
        $criteriaB = $request->criteria_b;
        $values    = $request->value;

        // Hapus semua data lama
        Ahp::truncate();

        foreach ($criteriaA as $i => $a) {

            $b = $criteriaB[$i];
            $val = $values[$i];

            // Jika user menukar posisi (B harus di kiri)
            // maka kita simpan versi yg konsisten
            if ($a > $b) {
                // swap
                $temp = $a;
                $a = $b;
                $b = $temp;

                // nilai harus dibalik
                $val = 1 / $val;
            }

            // Simpan A → B
            Ahp::create([
                'uuid'            => Str::uuid(),
                'criteria_a_uuid' => $a,
                'criteria_b_uuid' => $b,
                'value'           => $val,
            ]);

            // Simpan B → A (kebalikannya)
            Ahp::create([
                'uuid'            => Str::uuid(),
                'criteria_a_uuid' => $b,
                'criteria_b_uuid' => $a,
                'value'           => 1 / $val,
            ]);

        }

        return redirect()->route('ahp.index')
                        ->with('success', 'Data AHP berhasil disimpan');
    }

   private function calculate($criterias, $pairs) {
    $n = count($criterias);
    
    // 1. MATRIKS PERBANDINGAN
    $matrix = [];
    foreach ($criterias as $i => $a) {
        foreach ($criterias as $j => $b) {
            if ($i == $j) {
                $matrix[$i][$j] = 1;
            } else {
                $pair = $pairs
                    ->where('criteria_a_uuid', $a->uuid)
                    ->where('criteria_b_uuid', $b->uuid)
                    ->first();
                    
                $pair_rev = $pairs
                    ->where('criteria_a_uuid', $b->uuid)
                    ->where('criteria_b_uuid', $a->uuid)
                    ->first();
                
                if ($pair) {
                    $matrix[$i][$j] = (float) $pair->value;
                } elseif ($pair_rev) {
                    $matrix[$i][$j] = 1 / (float) $pair_rev->value;
                } else {
                    $matrix[$i][$j] = 1;
                }
            }
        }
    }
    
    // 2. JUMLAH KOLOM
    $colSum = [];
    for ($j = 0; $j < $n; $j++) {
        $sum = 0;
        for ($i = 0; $i < $n; $i++) {
            $sum += $matrix[$i][$j];
        }
        $colSum[$j] = $sum;
    }
    
    // 3. NORMALISASI MATRIKS
    $normalized = [];
    for ($i = 0; $i < $n; $i++) {
        for ($j = 0; $j < $n; $j++) {
            // Bulatkan seperti Excel (15 digit desimal)
            $normalized[$i][$j] = round($matrix[$i][$j] / $colSum[$j], 15);
        }
    }
    
    // 4. HITUNG PRIORITAS (BOBOT / EIGEN VECTOR)
    $weights = [];
    for ($i = 0; $i < $n; $i++) {
        // Hitung rata-rata
        $avg = array_sum($normalized[$i]) / $n;
        
        // Bulatkan ke 10 digit seperti Excel
        $weights[$i] = round($avg, 10);
        
        // Simpan ke database
        $criterias[$i]->value = $weights[$i];
        $criterias[$i]->save();
    }
    
    // 5. MATRIKS PENJUMLAHAN (Weighted Sum Matrix)
    $sumMatrix = [];
    for ($i = 0; $i < $n; $i++) {
        $rowSum = 0;
        for ($j = 0; $j < $n; $j++) {
            $value = $matrix[$i][$j] * $weights[$j];
            $sumMatrix[$i][$j] = $value;
            $rowSum += $value;
        }
        $sumMatrix[$i]['sum'] = $rowSum;
    }
    
    // 6. HITUNG λmax
    $lambda = 0;
    for ($i = 0; $i < $n; $i++) {
        $lambda += $sumMatrix[$i]['sum'] / $weights[$i];
    }
    $lambdaMax = $lambda / $n;
    
    // 7. CI dan CR
    $CI = ($lambdaMax - $n) / ($n - 1);
    
    // Random Index (RI)
    $RI = [
        1 => 0.00,
        2 => 0.00,
        3 => 0.58,
        4 => 0.90,
        5 => 1.12,
        6 => 1.24,
        7 => 1.32,
        8 => 1.41,
        9 => 1.45,
        10 => 1.49,
        11 => 1.51,
        12 => 1.48,
        13 => 1.56,
        14 => 1.57,
        15 => 1.59
    ];
    
    $CR = ($RI[$n] == 0) ? 0 : $CI / $RI[$n];
    
    return [
        'matrix' => $matrix,
        'colSum' => $colSum,
        'normalized' => $normalized,
        'weights' => $weights,
        'sumMatrix' => $sumMatrix,
        'lambdaMax' => $lambdaMax,
        'CI' => $CI,
        'CR' => $CR,
    ];
}
}
