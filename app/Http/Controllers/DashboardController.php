<?php

namespace App\Http\Controllers;

use App\Models\Criteria;
use App\Models\Alternative;

class DashboardController extends Controller
{
   public function index(){
    $totalCriteria = Criteria::count();
    $totalAlternative = Alternative::count();

    $latestCriterias = Criteria::limit(5)->get();
    $latestAlternatives = Alternative::latest()->limit(5)->get();

    return view('pages.index', compact(
        'totalCriteria',
        'totalAlternative',
        'latestCriterias',
        'latestAlternatives'
    ));
}

}
