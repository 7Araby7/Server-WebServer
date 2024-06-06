<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vaga extends Model
{
    protected $table = 'vagas';

    protected $fillable = [
        'ramo_id',
        'empresa_id',
        'titulo',
        'descricao',
        'experiencia',
        'salario_min',
        'salario_max',
        'ativo'
    ];

    public $timestamps = false;

    public function ramo()
    {
        return $this->belongsTo(Ramo::class);
    }

    public function empresa()
    {
        return $this->belongsTo(User::class, 'empresa_id');
    }

    public function competencias()
    {
        return $this->hasMany(CompetenciaVaga::class);
    }
}