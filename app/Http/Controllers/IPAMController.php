<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use DB;
use Auth;
use Illuminate\Http\Input;
use App\Models\IPAM;
use App\Models\DataTables;

class IPAMController extends Controller
{
    
    public function __construct()
    {

    }

    public function ipam_serverside()
    {
        $aColumns = ['ip_address','ip_desc','id'];
        $sIndexColumn = 'id';
        $sTable = 'ip_addresses';
        $searchableColumns = ['ip_address','ip_desc'];
        $sortCondition = "ip_address ASC";
        $input =& $_GET;

        $whereCondition = " user_id='".Auth::user()->id."' ";
        
        $res = DataTables::get($input, $aColumns, $sIndexColumn, $sTable, $searchableColumns, $whereCondition, $sortCondition);
       
        $output = $res['output'];
        $rResult = $res['rResult'];
        
        foreach ($rResult as $res) 
        {
            $data = array();

            $edit_btn = '<button type="button" onclick="edit_label('.$res->id.')" class="btn btn-warning rounded-pill px-3 btn-sm">Edit Label</button>';
            $update_logs_btn = '<a href="/desc-logs/'.$res->id.'" class="btn btn-info rounded-pill px-3 btn-sm">View Label Logs</a>';
            $login_logs_btn = '<a href="/login-logs/'.$res->id.'" class="btn btn-secondary rounded-pill px-3 btn-sm">View Login Logs</a>';
            
            //====================

            $data[] = $res->ip_address;

            $data[] = '<span id="desc_'.$res->id.'">'.$res->ip_desc.'</span>';

            //options
            $data[] = $edit_btn.'&nbsp;'.$update_logs_btn.'&nbsp;'.$login_logs_btn;                      
                    
            
            $output['aaData'][] = $data;
        }

        echo json_encode($output);
    }

    public function update_desc(Request $request)
    {

        if(isset(Auth::user()->id))
        {

            $ip_id = $request['id'];
            $ip_desc = $request['desc'];

            $upd = DB::table('ip_addresses')
                   ->where('id',$ip_id)
                   ->update(['ip_desc'=>$ip_desc]);

            //add to edit label logs
            DB::table('ip_desc_logs')
            ->insert([
                'ip_id'=>$ip_id,
                'ip_desc'=>$ip_desc,
                'date_updated'=>date('Y-m-d H:i:s'),
                'updated_by'=>Auth::user()->id
            ]);

            $success = true;
            $msg = 'Success!';

        }else{
            $success = false;
            $msg = "Unauthenticated!";
        }

        echo json_encode(['success'=>$success, 'msg'=>$msg]);
    }

    public function desc_logs($ip_id)
    {
        $ip = DB::table('ip_addresses')
                 ->select('ip_address')
                 ->where('id',$ip_id)
                 ->first();
        if(isset($ip->ip_address))
        {
            $data = [
                'ip_id'=>$ip_id,
                'ip_address'=>$ip->ip_address
            ];

            return view('desc-logs',$data);

        }else{

            return redirect('ipam');
        }
    }

    public function desc_logs_serverside($ip_id)
    {
        $aColumns = ['d.ip_desc','d.date_updated','u.email'];
        $sIndexColumn = 'd.id';
        $sTable = 'ip_desc_logs d 
                   LEFT JOIN users u ON d.updated_by=u.id 
                  ';
        $searchableColumns = ['d.ip_desc'];
        $sortCondition = "d.date_updated DESC";
        $input =& $_GET;

        $whereCondition = " d.ip_id='".$ip_id."' ";
        
        $res = DataTables::get($input, $aColumns, $sIndexColumn, $sTable, $searchableColumns, $whereCondition, $sortCondition);
       
        $output = $res['output'];
        $rResult = $res['rResult'];
        
        foreach ($rResult as $res) 
        {
            $data = array();

            $data[] = $res->ip_desc;

            $data[] = $res->date_updated;                     
            
            $data[] = $res->email;       
            
            $output['aaData'][] = $data;
        }

        echo json_encode($output);
    }

}
