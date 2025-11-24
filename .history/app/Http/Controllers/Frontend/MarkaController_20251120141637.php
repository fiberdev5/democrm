<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Marka;
use Illuminate\Support\Facades\Storage;

class MarkaController extends Controller
{
    // Markaları listele
    public function index()
    {
        $markalar = Marka::orderBy('marka', 'ASC')->get();
        return view('markalar.index', compact('markalar'));
    }

    // Marka ekleme formu (Modal için)
    public function create()
    {
        return view('markalar.create');
    }

}
