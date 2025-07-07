<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $connection = 'simpeg';
    protected $table = 'spg_pegawai';
    protected $primaryKey = 'pegawai_id';
}
