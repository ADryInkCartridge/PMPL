<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Petunjuk extends Model
{
    use HasFactory;
    public $table = "petunjuk";
    public $timestamps = false;
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'file_path'
    ];
}
