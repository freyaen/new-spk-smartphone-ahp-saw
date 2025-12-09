<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Alternative extends Model
{
    use HasUuids;

    protected $table = 'alternatif',
              $primaryKey = 'uuid',
              $guarded = [];

    public function criterias()
    {
        return $this->hasMany(AlternativeCriteria::class, 'alternative_uuid', 'uuid');
    }

}
