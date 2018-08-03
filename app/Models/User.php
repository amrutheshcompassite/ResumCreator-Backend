<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use OwenIt\Auditing\Contracts\UserResolver;
class User extends Eloquent implements AuthenticatableContract,AuditableContract
{
    use Notifiable;
    use SoftDeletes,Authenticatable,Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'mongodb';
    protected $collection = 'users';

    protected $fillable = [
        'name',
        'email',
        'phone_no'
    ];
}
