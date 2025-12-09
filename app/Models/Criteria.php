<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Criteria extends Model
{
    use HasUuids;

    public $incrementing = false,
    $timestamps = false;

    protected $table = 'kriteria',
              $primaryKey = 'uuid',
              $guarded = [];

    public function values()
    {
        return $this->hasMany(CriteriaValue::class);
    }
}
