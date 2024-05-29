<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompetenciaUser extends Model
{
    protected $table = 'competencia_user';

    protected $fillable = [
        'user_id',
        'competencia_id'
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function competencia()
    {
        return $this->belongsTo(Competencia::class);
    }
}
