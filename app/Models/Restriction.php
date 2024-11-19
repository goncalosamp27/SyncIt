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
    ];

    protected $casts = [
        'start' => 'datetime', 
        'duration' => 'string', 
    ];

    public static function validate($data)
    {
        $validator = Validator::make($data, [
            'member_id' => 'required|exists:member,member_id',   
            'duration' => 'required|date_format:H:i:s',          
            'admin_id' => 'required|exists:admin,admin_id',      
            'start' => 'required|date|after_or_equal:now',      
        ]);

        return $validator;
    }
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

   
    
}
