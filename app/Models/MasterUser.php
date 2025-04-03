<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class MasterUser extends Authenticatable
{
    use HasFactory;
    use HasApiTokens;

    protected $table = 'master_user';

    public function isPegawai()
    {
        return $this->user_level == 5; // default
    }

    public function isSkpd()
    {
        return $this->user_level == 8; // default
    }
}
