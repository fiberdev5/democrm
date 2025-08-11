<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceStageAnswer extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function question()
    {
        return $this->belongsTo(StageQuestion::class, 'soruid');
    }

    public function plan()
    {
        return $this->belongsTo(ServicePlanning::class, 'planid');
    }
    public function service()
    {
        return $this->belongsTo(Service::class, 'servisid');
    }

    public function stageQuestion()
    {
        return $this->belongsTo(StageQuestion::class, 'soruid');
    }

    public function servicePlanning()
    {
        return $this->belongsTo(ServicePlanning::class, 'planid');
    }
}
