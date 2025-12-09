<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Criteria;
use App\Models\Alternative;

class SawController extends Controller
{
    public function index()
    {
        $criterias = Criteria::orderBy('code')->get();
        $alternatives = Alternative::with('criterias')->oldest()->get();


        // Normalisasi matriks (presisi tinggi)
        $normalized = [];
        foreach($alternatives as $alt){
            $normValues = [];

            foreach($criterias as $c){
                // Ambil semua nilai kolom kriteria ini
                $colValues = $alternatives->map(function($a) use ($c){
                    $crit = $a->criterias->where('criteria_uuid', $c->uuid)->first();
                    return $crit ? $crit->value : 0;
                });

                $altCrit = $alt->criterias->where('criteria_uuid', $c->uuid)->first();
                $altValue = $altCrit ? $altCrit->value : 0;

                // Normalisasi dengan BCMath
                if($c->type == 'benefit'){
                    $normValues[$c->code] = $colValues->max() > 0 
                        ? bcdiv($altValue, $colValues->max(), 12) 
                        : '0';
                } else {
                    $normValues[$c->code] = $altValue > 0
                        ? bcdiv($colValues->min(), $altValue, 12) 
                        : '0';
                }
            }

            $normalized[] = [
                'code' => $alt->code,
                'image' => $alt->image,
                'name' => $alt->name,
                'values' => $normValues
            ];
        }

        // Weighted sum (presisi tinggi)
        $weightedSum = [];
        foreach($normalized as $alt){
            $total = '0';
            $weighted = [];

            foreach($criterias as $c){
                $weighted[$c->code] = bcmul($alt['values'][$c->code], (string)$c->value, 12);
                $total = bcadd($total, $weighted[$c->code], 12);
            }

            $weightedSum[] = [
                'code' => $alt['code'],
                'image' => $alt['image'],
                'name' => $alt['name'],
                'weighted' => $weighted,
                'total' => $total
            ];
        }

        

        // Ranking berdasarkan total descending
        $ranking = $weightedSum;
        usort($ranking, fn($a, $b) => bccomp($b['total'], $a['total'], 12));



        return view('pages.saw', compact('criterias', 'alternatives', 'normalized', 'weightedSum', 'ranking'));
    }
}
