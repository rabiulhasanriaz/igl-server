<?php

use App\Model\User;
use App\Model\UserDetail;
use App\Model\SmsCampaign_24h;
use App\Model\LoadCampaign30day;
use App\Model\SenderIdUserDefault;
use App\Model\SenderIdRegister;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class OtherHelpers
{
    /*get user logo*/
    public static function user_logo($file_address)
    {
        if ($file_address == null) {
            $file_path = asset('assets') . '/uploads/User_Logo/avatar.jpg';
        } elseif (file_exists('assets/uploads/User_Logo/' . $file_address)) {
            $file_path = asset('assets') . '/uploads/User_Logo/' . $file_address;
        } else {
            $file_path = asset('assets') . '/uploads/User_Logo/avatar.jpg';
        }
        return $file_path;
    }


    /*check schedule sms status*/
    public static function schedule_sms_status($campaign_id)
    {
        $checkScheduleSmsStatus = \App\Model\SmsCamPending::where('campaign_id', $campaign_id)->first();
        if ($checkScheduleSmsStatus) {
            return "Processing";
        } else {
            return "AllSent";
        }
    }


    /*get website logo based on url*/
    public static function website_logo()
    {
        if (Auth::user()->role == '5') {
            $reseller = User::where('id', Auth::user()->create_by)->first();
            $file_address = $reseller->userDetail['logo'];
        } elseif (Auth::user()->role == '4') {
            $reseller = User::where('id', Auth::user()->create_by)->first();
            $file_address = $reseller->userDetail['logo'];
            // $file_address = Auth::user()->userDetai['logo'];
        } else {
            $file_address = Auth::user()->userDetai['logo'];
        }

        if ($file_address == null) {
            $file_path = asset('assets') . '/uploads/default.png';
        } elseif (file_exists('assets/uploads/User_Logo/' . $file_address)) {
            $file_path = asset('assets') . '/uploads/User_Logo/' . $file_address;
        } else {
            $file_path = asset('assets') . '/uploads/default.png';
        }
        return $file_path;
    }

    /*Get employee's company Logo*/
    public static function emp_company_logo()
    {
        $company_id = \App\Model\EmployeeUser::find( Auth::guard('employee')->id() )->value('create_by');
        return asset('assets').'/uploads/User_Logo/'.UserDetail::where('user_id', $company_id)->value('logo');

    }

    /*Get employee hotline number*/
    public static function employee_hotline()
    {
        $company_id = \App\Model\EmployeeUser::find( Auth::guard('employee')->id() )->value('create_by');
        return User::find($company_id)->value('cellphone');
    }

    public static function user_creator_info($need) {

        $creator_id = Auth::user()->create_by;
        if ($creator_id != null) {
            $creator = UserDetail::where('user_id', $creator_id)->first();

            if ($need == 'hotine') {

               if ($creator->hotline == '') {
                   return '01823037726';
               } else {
                   return $creator->hotline;
               }

            }else if($need == 'company_name') {
                $creator_basic_info = User::where('id', $creator_id)->first();
                if ($creator_basic_info->company_name == '') {
                    return 'IGL Web Ltd';
                } else {
                    return $creator_basic_info->company_name;
                }

            }else if($need == 'fb_id') {

                if ($creator->facebookid == '') {
                    return 'https://www.facebook.com/iglwebltd';
                } else {
                    return $creator->facebookid;
                }

            } else {
                return null;
            }

            
        } else {
            if ($need == 'hotine') {
                return '01823037726';
            }else if($need == 'company_name') {
                return 'IGL Web Ltd';
            }else if($need == 'fb_id') {
                return 'https://www.facebook.com/iglwebltd';
            } else {
                return null;
            }
        }
    }

    /*get user hotline*/
    public static function user_hotline()
    {
        $creator_id = Auth::user()->create_by;
        if ($creator_id != null) {
            $creator = UserDetail::select('hotline')->where('user_id', $creator_id)->first();

            if ($creator->hotline == '') {
                return '01823037726';
            } else {
                return $creator->hotline;
            }
        } else {
            return '01823037726';
        }
    }

    /*get user company name*/
    public static function company_name()
    {
        $creator_id = Auth::user()->create_by;
        if ($creator_id != null) {
            $creator = User::select('company_name')->where('id', $creator_id)->first();

            if ($creator->company_name == '') {
                return 'IGL Web Ltd';
            } else {
                return $creator->company_name;
            }
        } else {
            return 'IGL Web Ltd';
        }
    }

    // Total users of an employee
    public static function get_number_of_user($employee_id) {
        return User::where('employee_user_id', $employee_id)
                    // ->where('flexi_emp_comission','!=',NULL)
                    ->count();
    }

    // Get all users
    public static  function get_all_employees($reseller_id) 
    {
        return \App\Model\EmployeeUser::where('create_by', $reseller_id)->get();
    }

    // all users of a resseler witch are asign to any employee
    public static function get_all_users($reseller_id)
    {
        $all_users = User::where('create_by', $reseller_id)->whereNotNull('employee_user_id')->get();
        return $all_users;
    }

    public static function getOS($user_agent = null)
    {
        if(!isset($user_agent) && isset($_SERVER['HTTP_USER_AGENT'])) {
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
        }

        // https://stackoverflow.com/questions/18070154/get-operating-system-info-with-php
        $os_array = [
            'windows nt 10'                              =>  'Windows 10',
            'windows nt 6.3'                             =>  'Windows 8.1',
            'windows nt 6.2'                             =>  'Windows 8',
            'windows nt 6.1|windows nt 7.0'              =>  'Windows 7',
            'windows nt 6.0'                             =>  'Windows Vista',
            'windows nt 5.2'                             =>  'Windows Server 2003/XP x64',
            'windows nt 5.1'                             =>  'Windows XP',
            'windows xp'                                 =>  'Windows XP',
            'windows nt 5.0|windows nt5.1|windows 2000'  =>  'Windows 2000',
            'windows me'                                 =>  'Windows ME',
            'windows nt 4.0|winnt4.0'                    =>  'Windows NT',
            'windows ce'                                 =>  'Windows CE',
            'windows 98|win98'                           =>  'Windows 98',
            'windows 95|win95'                           =>  'Windows 95',
            'win16'                                      =>  'Windows 3.11',
            'mac os x 10.1[^0-9]'                        =>  'Mac OS X Puma',
            'macintosh|mac os x'                         =>  'Mac OS X',
            'mac_powerpc'                                =>  'Mac OS 9',
            'linux'                                      =>  'Linux',
            'ubuntu'                                     =>  'Linux - Ubuntu',
            'iphone'                                     =>  'iPhone',
            'ipod'                                       =>  'iPod',
            'ipad'                                       =>  'iPad',
            'android'                                    =>  'Android',
            'blackberry'                                 =>  'BlackBerry',
            'webos'                                      =>  'Mobile',

            '(media center pc).([0-9]{1,2}\.[0-9]{1,2})'=>'Windows Media Center',
            '(win)([0-9]{1,2}\.[0-9x]{1,2})'=>'Windows',
            '(win)([0-9]{2})'=>'Windows',
            '(windows)([0-9x]{2})'=>'Windows',

            // Doesn't seem like these are necessary...not totally sure though..
            //'(winnt)([0-9]{1,2}\.[0-9]{1,2}){0,1}'=>'Windows NT',
            //'(windows nt)(([0-9]{1,2}\.[0-9]{1,2}){0,1})'=>'Windows NT', // fix by bg

            'Win 9x 4.90'=>'Windows ME',
            '(windows)([0-9]{1,2}\.[0-9]{1,2})'=>'Windows',
            'win32'=>'Windows',
            '(java)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2})'=>'Java',
            '(Solaris)([0-9]{1,2}\.[0-9x]{1,2}){0,1}'=>'Solaris',
            'dos x86'=>'DOS',
            'Mac OS X'=>'Mac OS X',
            'Mac_PowerPC'=>'Macintosh PowerPC',
            '(mac|Macintosh)'=>'Mac OS',
            '(sunos)([0-9]{1,2}\.[0-9]{1,2}){0,1}'=>'SunOS',
            '(beos)([0-9]{1,2}\.[0-9]{1,2}){0,1}'=>'BeOS',
            '(risc os)([0-9]{1,2}\.[0-9]{1,2})'=>'RISC OS',
            'unix'=>'Unix',
            'os/2'=>'OS/2',
            'freebsd'=>'FreeBSD',
            'openbsd'=>'OpenBSD',
            'netbsd'=>'NetBSD',
            'irix'=>'IRIX',
            'plan9'=>'Plan9',
            'osf'=>'OSF',
            'aix'=>'AIX',
            'GNU Hurd'=>'GNU Hurd',
            '(fedora)'=>'Linux - Fedora',
            '(kubuntu)'=>'Linux - Kubuntu',
            '(ubuntu)'=>'Linux - Ubuntu',
            '(debian)'=>'Linux - Debian',
            '(CentOS)'=>'Linux - CentOS',
            '(Mandriva).([0-9]{1,3}(\.[0-9]{1,3})?(\.[0-9]{1,3})?)'=>'Linux - Mandriva',
            '(SUSE).([0-9]{1,3}(\.[0-9]{1,3})?(\.[0-9]{1,3})?)'=>'Linux - SUSE',
            '(Dropline)'=>'Linux - Slackware (Dropline GNOME)',
            '(ASPLinux)'=>'Linux - ASPLinux',
            '(Red Hat)'=>'Linux - Red Hat',
            // Loads of Linux machines will be detected as unix.
            // Actually, all of the linux machines I've checked have the 'X11' in the User Agent.
            //'X11'=>'Unix',
            '(linux)'=>'Linux',
            '(amigaos)([0-9]{1,2}\.[0-9]{1,2})'=>'AmigaOS',
            'amiga-aweb'=>'AmigaOS',
            'amiga'=>'Amiga',
            'AvantGo'=>'PalmOS',
            //'(Linux)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3}(rel\.[0-9]{1,2}){0,1}-([0-9]{1,2}) i([0-9]{1})86){1}'=>'Linux',
            //'(Linux)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3}(rel\.[0-9]{1,2}){0,1} i([0-9]{1}86)){1}'=>'Linux',
            //'(Linux)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3}(rel\.[0-9]{1,2}){0,1})'=>'Linux',
            '[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3})'=>'Linux',
            '(webtv)/([0-9]{1,2}\.[0-9]{1,2})'=>'WebTV',
            'Dreamcast'=>'Dreamcast OS',
            'GetRight'=>'Windows',
            'go!zilla'=>'Windows',
            'gozilla'=>'Windows',
            'gulliver'=>'Windows',
            'ia archiver'=>'Windows',
            'NetPositive'=>'Windows',
            'mass downloader'=>'Windows',
            'microsoft'=>'Windows',
            'offline explorer'=>'Windows',
            'teleport'=>'Windows',
            'web downloader'=>'Windows',
            'webcapture'=>'Windows',
            'webcollage'=>'Windows',
            'webcopier'=>'Windows',
            'webstripper'=>'Windows',
            'webzip'=>'Windows',
            'wget'=>'Windows',
            'Java'=>'Unknown',
            'flashget'=>'Windows',

            // delete next line if the script show not the right OS
            //'(PHP)/([0-9]{1,2}.[0-9]{1,2})'=>'PHP',
            'MS FrontPage'=>'Windows',
            '(msproxy)/([0-9]{1,2}.[0-9]{1,2})'=>'Windows',
            '(msie)([0-9]{1,2}.[0-9]{1,2})'=>'Windows',
            'libwww-perl'=>'Unix',
            'UP.Browser'=>'Windows CE',
            'NetAnts'=>'Windows',
        ];

        // https://github.com/ahmad-sa3d/php-useragent/blob/master/core/user_agent.php
        $arch_regex = '/\b(x86_64|x86-64|Win64|WOW64|x64|ia64|amd64|ppc64|sparc64|IRIX64)\b/ix';
        $arch = preg_match($arch_regex, $user_agent) ? '64' : '32';

        foreach ($os_array as $regex => $value) {
            if (preg_match('{\b('.$regex.')\b}i', $user_agent)) {
                return $value.' x'.$arch;
            }
        }

        return 'Unknown';
    }

    public static function total_sms($user_id){
        return SmsCampaign_24h::where('user_id',$user_id)->count();
    }
    public static function total_flexi($user_id){
        return LoadCampaign30day::where('user_id',$user_id)->count();
    }


    
    public static function number_to_text($number) {
        $no = round($number);
        $decimal = round($number - ($no = floor($number)), 2) * 100;    
        $digits_length = strlen($no);    
        $i = 0;
        $str = array();
        $words = array(
            0 => '',
            1 => 'One',
            2 => 'Two',
            3 => 'Three',
            4 => 'Four',
            5 => 'Five',
            6 => 'Six',
            7 => 'Seven',
            8 => 'Eight',
            9 => 'Nine',
            10 => 'Ten',
            11 => 'Eleven',
            12 => 'Twelve',
            13 => 'Thirteen',
            14 => 'Fourteen',
            15 => 'Fifteen',
            16 => 'Sixteen',
            17 => 'Seventeen',
            18 => 'Eighteen',
            19 => 'Nineteen',
            20 => 'Twenty',
            30 => 'Thirty',
            40 => 'Forty',
            50 => 'Fifty',
            60 => 'Sixty',
            70 => 'Seventy',
            80 => 'Eighty',
            90 => 'Ninety');
        $digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore' , 'Crore');
        
        while ($i < $digits_length) {
            $divider = ($i == 2) ? 10 : 100;
            $number = floor($no % $divider);
            $no = floor($no / $divider);
            $i += $divider == 10 ? 1 : 2;
            if ($number) {
                $plural = (($counter = count($str)) && $number > 9) ? 's' : null;            
                $str [] = ($number < 21) ? $words[$number] . ' ' . $digits[$counter] . $plural : $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $digits[$counter] . $plural;
            } else {
                $str [] = null;
            }  
        }
        
        $Rupees = implode(' ', array_reverse($str));
        $paise = ($decimal) ? " And " . ($words[$decimal - $decimal%10]) ." " .($words[$decimal%10]) ." Paisa" : '';
        // return $paise;
        return ($Rupees ? $Rupees .' Taka': '') . $paise . " Only";
    }

    public static function userNumber($userId){
        $number = User::where('id',$userId)->first();
        return $number->cellphone;
    }

    public static function userApi($userId){
        $api = UserDetail::where('user_id',$userId)->first();
        return $api->api_key;
    }
    
    public static function userSender($userId){
        $sender = SenderIdUserDefault::where('user_id',$userId)->first();
        $senderId = SenderIdRegister::where('id',$sender->sender_id)->first();
        return $senderId->sir_sender_id;
    }


}
