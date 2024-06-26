<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mensagem extends Model
{
    protected $table = 'mensagem';

    protected $fillable = [
        'candidato_id',
        'empresa_id',
        'mensagem',
        'nova'
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
