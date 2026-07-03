<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use HasFactory;

    protected $table = 'admin';

    protected $primaryKey = 'admin_id';

    public $timestamps = false;

    protected $fillable = [
        'email',
        'password',
    ];
    //Relationships
    // Association: 1 Admin has many Restrictions
    public function restrictions()
    {
        return $this->hasMany(Restriction::class, 'admin_id', 'admin_id');
    }
}
