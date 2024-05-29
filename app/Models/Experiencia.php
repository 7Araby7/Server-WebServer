<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Experiencia extends Model
{
    protected $fillable = ['nome_empresa', 'inicio', 'fim', 'cargo', 'user_id'];
    public $timestamps = false;
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
