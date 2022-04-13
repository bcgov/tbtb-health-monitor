<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestCase extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    public function contacts(){
        return $this->belongsToMany('App\Models\Contact', 'contact_test_case', 'test_case_id', 'contact_id');
    }
}
