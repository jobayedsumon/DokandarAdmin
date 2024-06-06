<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessSetting extends Model
{
    protected $fillable = [
      'key',
      'value',
    ];
    public function translations()
    {
        return $this->morphMany(Translation::class, 'translationable');
    }

}
