<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Survey;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SurveyController extends Controller
{

    public function getSurveyForm($tenant_id, $servis_id)
    {
        $servis = Service::with('musteri')->findOrFail($servis_id);
        return view('frontend.secure.survey.modal', compact('servis'));
    }

}
