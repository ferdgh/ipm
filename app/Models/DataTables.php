<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class DataTables extends Model
{

    /*
	 * Method to process datatables serverside.
	 */
    public static function get($input, $aColumns, $sIndexColumn = 'id', $sTable, $searchableColumns, $whereCondition, $sortCondition)
    {
        mb_internal_encoding('UTF-8');

        // $input =& $_GET;
        /**
         * Paging
         */
        $sLimit = "";
        if (isset($input['iDisplayStart']) && $input['iDisplayLength'] != '-1') {
            $sLimit = " LIMIT ".intval($input['iDisplayStart']).", ".intval($input['iDisplayLength']);
        }
          
        /**
         * Ordering
         */
        $aOrderingRules = array();
        if (isset($input['iSortCol_0'])) {
            $iSortingCols = intval($input['iSortingCols']);
            for ($i=0; $i<$iSortingCols; $i++) {
                if ($input[ 'bSortable_'.intval($input['iSortCol_'.$i]) ] == 'true') {
                    $aOrderingRules[] =
                        " ".$aColumns[ intval($input['iSortCol_'.$i]) ]." "
                        .($input['sSortDir_'.$i]==='asc' ? 'asc' : 'desc');
                }
            }
        }
            
        $sortOption = '';

        //reprocess sorting in case of count
        $_aOrderingRules = array();
        foreach ($aOrderingRules as $v) {
            $s = explode(' as ', $v); //COUNT( DISTINCT color) as colorTotal desc
            if (isset($s[1])) {
                $_aOrderingRules[] = $s[1]; //colorTotal desc
            } else {
                //default
                $_aOrderingRules[] = $v;
            }
        }
        $aOrderingRules = $_aOrderingRules;
        
        if (!empty($aOrderingRules)) {
            $sOrder = " ORDER BY ".implode(", ", $aOrderingRules);
        } else {
            $sOrder = " ORDER BY ".$sortCondition;
        }
                 
        /**
         * Filtering
         * NOTE this does not match the built-in DataTables filtering which does it
         * word by word on any field. It's possible to do here, but concerned about efficiency
         * on very large tables, and MySQL's regex functionality is very limited
         */
        $iColumnCount = count($aColumns);

        $aFilteringRules = array();
        //check if there's a space
        $_searchInput = isset($input['sSearch']) ? strtolower($input['sSearch']) : '';

        if (strpos($_searchInput, '+') !== false) 
        {
            //find closer match 
            $q = str_replace('+', ' ', $_searchInput);

            $aFilteringRules[] = " LOWER(title) LIKE '%". $q ."%'";
            $aFilteringRules[] = " LOWER(title) LIKE '% ". $q ." %'";

        }else{

            if (preg_match('/\s/', $_searchInput)) {
                $_searchEx = explode(' ', $_searchInput);

                // Individual column filtering (multi words)
                for ($i=0; $i<count($searchableColumns); $i++) {
                    for ($j=0; $j<count($_searchEx); $j++) {
                        $aFilteringRules[] = " LOWER(".$searchableColumns[$i].") LIKE '%". $_searchEx[$j] ."%'";
                    }
                }
            } else {

                if($_searchInput!='')
                {
                    // Individual column filtering (single word)
                    for ($i=0; $i<count($searchableColumns); $i++) {
                        $aFilteringRules[] = " LOWER(".$searchableColumns[$i].") LIKE '%". $_searchInput ."%'";
                    }
                }
            }
        }
        
        $whereCond = "id > 0";
        if ($whereCondition!='') {
            $whereCond = $whereCondition;
        }

        if (!empty($aFilteringRules)) {
            $sWhere = " WHERE  (".implode(" OR ", $aFilteringRules).") AND ".$whereCond." ";
        } else {
            $sWhere = " WHERE ".$whereCond;
        }
                
        /**
         * SQL queries
         * Get data to display
         */
        $aQueryColumns = array();
        foreach ($aColumns as $col) {
            if ($col != ' ') {
                $aQueryColumns[] = $col;
            }
        }
         
        $sQuery = " SELECT ".implode(", ", $aQueryColumns)."
                    FROM ".$sTable.$sWhere.$sOrder.$sLimit;
        
        $rResult = DB::select(DB::raw($sQuery));
        
        // Data set length after filtering
        // $sQuery2 = "SELECT FOUND_ROWS() as total";
        $sQuery2 = " SELECT COUNT(*) as total FROM ".$sTable.$sWhere;
        $rResultFilterTotal = DB::select(DB::raw($sQuery2));
        $iFilteredTotal = $rResultFilterTotal[0]->total;
        // Total data set length
        $iTotal = $rResultFilterTotal[0]->total;

                
        /**
         * Output
         */
        $output = array(
            "sEcho"                => isset($input['sEcho']) ? intval($input['sEcho']) : 1,
            "iTotalRecords"        => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData"               => array()
        );

        return array( 'rResult'=>$rResult, 'output'=>$output);
    }
}
