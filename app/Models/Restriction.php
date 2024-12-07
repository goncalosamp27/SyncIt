<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Restriction extends Model
{
    use HasFactory;

    protected $table = 'restriction';

    protected $primaryKey = 'restriction_id';

    public $timestamps = false;

    protected $fillable = [
        'member_id',
        'duration',
        'admin_id',
        'start',
        'type',
    ];

    protected $casts = [
        'start' => 'datetime', 
    ];

    //Relationships
    // 1 Restriction belongs to 1 Member (many restrictions to one member)
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id');
    }

    // 1 Restriction belongs to 1 Admin (many restrictions to one admin)
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'admin_id');
    }

    // 1 Restriction has many RestrictionNotifications (one restriction can have many notifications)
    public function notifications()
    {
        return $this->hasMany(RestrictionNotification::class, 'restriction_id', 'restriction_id');
    }
}
