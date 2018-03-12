<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $primaryKey = 'state_id';

    protected $fillable = ['state_id','state_name','state_abbr','created_by','created_at','updated_by','updated_at','end_date'];
	
    /**  Has Many Relationship with County Model  **/
    public function counties()
    {
        return $this->hasMany('App\County');
    }
    
    public static function getStateName($id)
    {
        $state = State::find($id);
        if ($state) {
            return $state->state_name;
        } else {
            return '';
        }
    }
    
    public static function getStateAbbr($id)
    {
        $state = State::find($id);
        if ($state) {
            return $state->state_abbr;
        } else {
            return '';
        }
    }
    
}
