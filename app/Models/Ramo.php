<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ramo extends Model
{
    protected $table = 'ramos';

    protected $fillable = [
        'nome',
        'descricao'
    ];

    public $timestamps = false;

    public function vagas()
    {
        return $this->hasMany(Vaga::class);
    }
}