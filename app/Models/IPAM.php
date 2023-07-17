<?php

namespace App\Models;

use Auth;
use DB;
use Session;

class IPAM
{

    public static function log_ip()
    {
        //get current IP Address
        $ip_address = $_SERVER['REMOTE_ADDR'];

        //check
        $ip = DB::table('ip_addresses')
             ->select('id')
             ->where('ip_address',$ip_address)
             ->where('user_id',Auth::user()->id)
             ->first();
        if(isset($ip->id))
        {
            $ip_id = $ip->id;
        }else{
            
            $ip_desc = gethostbyaddr($_SERVER['REMOTE_ADDR']);
            //save new IP
            $ip_id = DB::table('ip_addresses')
                     ->insertGetId([
                        'ip_address'=>$ip_address,
                        'ip_desc'=>$ip_desc,
                        'user_id'=>Auth::user()->id
                     ]);

            //log desc
            DB::table('ip_desc_logs')
             ->insert([
                'ip_id'=>$ip_id,
                'ip_desc'=>$ip_desc,
                'date_updated'=>date('Y-m-d H:i:s'),
                'updated_by'=>Auth::user()->id
             ]);     

        }

        //save to login logs
        DB::table('login_logs')
        ->insert([
            'user_id'=>Auth::user()->id,
            'ip_id'=>$ip_id,
            'date_created'=>date('Y-m-d H:i:s')
        ]);

    }

}
