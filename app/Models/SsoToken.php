<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SsoToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'token',
        'user_id',
        'expires_at',
    ];

    public function master_user()
    {
        return $this->belongsTo(MasterUser::class, 'user_id', 'user_id');
    }
}
