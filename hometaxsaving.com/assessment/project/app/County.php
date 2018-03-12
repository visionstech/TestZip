<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class County extends Model
{
    protected $primaryKey = 'county_id';
	
	protected $fillable = ['county_id','county_name','state_id','date_of_value','notice_date','appeal_deadline_date','county_link','created_by','created_at','updated_by','updated_at','end_date'];

    public function getNoticeDateAttribute($timestamp) {
		if ($timestamp != NULL and !empty($timestamp))
            $noticeDate = \Carbon\Carbon::parse($timestamp);
		else
			$noticeDate = null;
		return $noticeDate;
    }
	
    public function getDateOfValueAttribute($timestamp) {
		if ($timestamp != NULL and !empty($timestamp))
            return \Carbon\Carbon::parse($timestamp);
		else
			return null;
    }

    public function getAppealDeadlineDateAttribute($timestamp) {
		if ($timestamp != NULL and !empty($timestamp))        
			return \Carbon\Carbon::parse($timestamp);
		else
			return null;
    } 
    
    public static function getCountyName($id)
    {
        $county = County::find($id);
        if ($county) {
            return $county->county_name;
        } else {
            return '';
        }
    }
}
