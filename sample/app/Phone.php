<?php


namespace App;


use Illuminate\Database\Eloquent\Model;


class Phone extends Model
{
    /**
     * Get the user that owns the phone.
     */
    protected $table = 'user_phone';
    
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}