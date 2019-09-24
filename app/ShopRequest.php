<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShopRequest extends Model
{
    protected $fillable = [
        'name', 'count'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id', 'code',
    ];

}
