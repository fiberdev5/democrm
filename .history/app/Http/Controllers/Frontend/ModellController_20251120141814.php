<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Modell;
use App\Models\Marka;

class ModellController extends Controller
{
    // Modelleri listele
    public function index($marka_id)
    {
        $modeller = Modell::where('mid', $marka_id)->get();
        $markaSec = Marka::findOrFail($marka_id);
        return view('modeller.index', compact('modeller', 'markaSec'));
    }

        // Model ekleme formu
    public function create($marka_id)
    {
        $markaSec = Marka::findOrFail($marka_id);
        return view('modeller.create', compact('markaSec', 'marka_id'));
    }
    
}
