<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class RestrictionNotification extends Model
{
    use HasFactory;

    protected $table = 'restriction_notification';

    protected $primaryKey = 'notification_id';


    public $timestamps = false;

    protected $fillable = [
        'notification_id',
        'restriction_id',
    ];

    public static function validate($data)
    {
        $validator = Validator::make($data, [
            'notification_id' => 'required|exists:notification,notification_id',   
            'restriction_id' => 'required|exists:restriction,restriction_id',     
        ]);

        return $validator;
    }
    //Relationships
    public function notification()
    {
        return $this->belongsTo(Notification::class, 'notification_id');
    }

    public function restriction()
    {
        return $this->belongsTo(Restriction::class, 'restriction_id');
    }

    
    
}
