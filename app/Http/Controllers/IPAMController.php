<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use DB;
use Auth;
use App\Models\IPAM;
use App\Models\DataTables;


use Illuminate\Support\Facades\Input;

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

            $edit_btn = '<button type="button" onclick="edit('.$res->id.')" class="btn btn-warning rounded-pill px-3 btn-sm">Edit Label</button>';
            $update_logs_btn = '<a href="/update-logs/'.$res->id.'" class="btn btn-info rounded-pill px-3 btn-sm">View Update Logs</a>';
            $login_logs_btn = '<a href="/login-logs/'.$res->id.'" class="btn btn-secondary rounded-pill px-3 btn-sm">View Login Logs</a>';
            
            //====================

            $data[] = $res->ip_address;

            $data[] = $res->ip_desc;

            //options
            $data[] = $edit_btn.'&nbsp;'.$update_logs_btn.'&nbsp;'.$login_logs_btn;                      
                    
            
            $output['aaData'][] = $data;
        }

        echo json_encode($output);
    }

}
