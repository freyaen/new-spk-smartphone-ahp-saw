<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Criteria;
use Illuminate\Support\Str;

class CriteriaController extends Controller
{
    public function index(Request $request)
{
    $search = $request->input('search');

    $criterias = Criteria::when($search, function ($query, $search) {
            $query->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
        })
        ->orderBy('code')
        ->paginate(5)
        ->withQueryString();

    return view('pages.criterias', compact('criterias', 'search'));
}

    public function store(Request $request)
    {
        $request->validate([
            'code'    => 'required',
            'name'    => 'required',
            'type'    => 'required|in:benefit,cost',
        ]);

        Criteria::create([
            'uuid'     => Str::uuid(),
            'code'     => $request->code,
            'name'     => $request->name,
            'type'     => $request->type,
            'value'    => 0,
        ]);

        return back()->with('success', 'Kriteria berhasil ditambahkan.');
    }

    public function update(Request $request, $uuid)
    {
        $request->validate([
            'code'    => 'required',
            'name'    => 'required',
            'type'    => 'required|in:benefit,cost',
        ]);

        Criteria::findOrFail($uuid)->update([
            'code'    => $request->code,
            'name'    => $request->name,
            'type'    => $request->type,
        ]);

        return back()->with('success', 'Kriteria berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        Criteria::findOrFail($uuid)->delete();

        return back()->with('success', 'Kriteria berhasil dihapus.');
    }
}
