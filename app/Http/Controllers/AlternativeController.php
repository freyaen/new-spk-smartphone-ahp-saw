<?php

namespace App\Http\Controllers;

use App\Models\Alternative;
use App\Models\Criteria;
use App\Models\AlternativeCriteria;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AlternativeController extends Controller
{
public function index(Request $request)
{
    $search = $request->input('search');

    $alternatives = Alternative::when($search, function ($query, $search) {
            $query->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
        })
        ->oldest()
        ->paginate(5)
        ->withQueryString();

    $criterias = Criteria::orderBy('code')->get();

    return view('pages.alternatives', compact('alternatives', 'criterias', 'search'));
}


public function store(Request $request)
{
    $request->validate([
        'code' => 'required|string|max:225',
        'name' => 'required|string|max:225',
        'criteria.*' => 'required|numeric',
    ]);

    $alternative = Alternative::create([
        'uuid' => Str::uuid(),
        'code' => $request->code,
        'name' => $request->name,
    ]);

    foreach ($request->criteria as $criteria_uuid => $value) {
        AlternativeCriteria::create([
            'uuid'             => Str::uuid(),
            'alternative_uuid' => $alternative->uuid,
            'criteria_uuid'    => $criteria_uuid,
            'value'            => $value,
        ]);
    }

    return redirect()->route('alternatives.index')
                     ->with('success', 'Alternatif berhasil ditambahkan');
}

public function update(Request $request, $uuid)
{
    $alternative = Alternative::findOrFail($uuid);

    $request->validate([
        'code' => 'required|string|max:225',
        'name' => 'required|string|max:225',
        'criteria.*' => 'required|numeric',
    ]);

    $alternative->code = $request->code;
    $alternative->name = $request->name;
    $alternative->save();

    foreach ($request->criteria as $criteria_uuid => $value) {
        AlternativeCriteria::updateOrCreate(
            ['alternative_uuid' => $alternative->uuid, 'criteria_uuid' => $criteria_uuid],
            ['value' => $value, 'uuid' => Str::uuid()]
        );
    }

    return redirect()->route('alternatives.index')
                     ->with('success', 'Alternatif berhasil diperbarui');
}

public function destroy($uuid)
{
    $alternative = Alternative::findOrFail($uuid);

    $alternative->delete();

    return redirect()->route('alternatives.index')
                     ->with('success', 'Alternatif berhasil dihapus');
}


}
