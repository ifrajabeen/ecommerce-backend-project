<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Catgeory extends Model
{
    use HasFactory;
    protected $table = 'categories';

    public function products(){
        return $this->hasMany(Product::class);
    }
}
