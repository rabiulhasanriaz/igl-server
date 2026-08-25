<?php

namespace App\Model;


use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;


class User extends Authenticatable
{

    use SoftDeletes;

//    protected $primaryKey = 'id';
    protected $table = 'users';
    protected $fillable = [
        'id',
    	'create_by',
    	'company_name',
    	'email',
    	'cellphone',
    	'password',
    	'status',
        'login_status',
        'last_login_time',
        'last_active_time',
    	'role',
        'position',
        'permission',
        'employee_limit',
    ];

    protected $dates = [
        'last_login_time',
        'last_active_time',
    ];


    /*my details*/
    public function userDetail()
    {
        return $this->hasOne(UserDetail::class);
    }

    /*all user who created by me*/
    public function myUsers()
    {
        return $this->hasMany(User::class, 'create_by', 'id');
    }

    /*my sms rates*/
    public function smsRates()
    {
        return $this->hasMany(AccSmsRate::class);
    }

    /*my all sender id*/
    public function senderIds()
    {
        return $this->hasMany(SenderIdUser::class);
    }

    /**/
    public function templates()
    {
        return $this->hasMany(SmsTemplate::class);
    }
    public function allFlexiload()
    {
        return $this->hasMany(LoadCampaign::class);
    }
    public function flexibooks()
    {
        return $this->hasMany(LoadFlexibook::class);
    }

    public function parentInfo()
    {
        return $this->belongsTo(User::class, 'create_by', 'id');
    }

    public static function company_name($id){
        return User::where('id',$id)->where('status',1)->first();
    }
    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class, 'user_id');
    }

    /**
     * Get all tickets assigned to this user (as admin/support agent)
     */
    public function assignedTickets()
    {
        return $this->hasMany(SupportTicket::class, 'assigned_to');
    }

    /**
     * Get all ticket replies by this user
     */
    public function ticketReplies()
    {
        return $this->hasMany(SupportTicketReply::class, 'user_id');
    }

    /**
     * Check if user is admin/support agent
     */
    public function isAdmin()
    {
        return $this->role == 1; // Assuming role 1 is admin
    }

    /**
     * Check if user is active
     */
    public function isActive()
    {
        return $this->status == 1;
    }
    public function smsLimits()
{
    return $this->hasMany(SmsIpDailyLimit::class, 'user_id');
}
}
