<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use SoftDeletes;

    public function testCase(){
            return $this->belongsToMany('App\Models\TestCase', 'contact_test_case', 'contact_id', 'test_case_id');
    }
}
