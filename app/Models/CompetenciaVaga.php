<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompetenciaVaga extends Model
{
    protected $table = 'competencia_vaga';

    protected $fillable = [
        'vaga_id',
        'competencia_id'
    ];

    public $timestamps = false;

    public function vaga()
    {
        return $this->belongsTo(Vaga::class);
    }

    public function competencia()
{
    return $this->belongsTo(Competencia::class);
}

}