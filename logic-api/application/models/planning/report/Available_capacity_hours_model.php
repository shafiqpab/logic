<?php
class Available_capacity_hours_model extends CI_Model
{
	
    
    //($company_id,$location_id,$sew_floor,$sew_line,$start_date,$end_date)
    function Available_capacity_hours($dataArr=array())
	{
        extract($dataArr);

        if($sew_line){
            $line_con = "AND c.ID in ($sew_line)";
        }
        if($sew_floor){
            $floor_con = "AND c.FLOOR_NAME in ($sew_floor)";
        }

        $company_names = $this->db->query("select id,COMPANY_NAME FROM LIB_COMPANY where ID in($company_id)"
        )->result();

        foreach($company_names as $company_name){
            $company_name_array[$company_name->ID] = $company_name->COMPANY_NAME;
        }

        $start_date =  date('d-M-Y',strtotime($start_date));
        $end_date =  date('d-M-Y',strtotime($end_date));

        $lib_capacity_calc_query = "SELECT a.COMAPNY_ID,a.LOCATION_ID,b.DATE_CALC,b.CAPACITY_MIN,b.CAPACITY_PCS,c.FLOOR_NAME AS FLOOR_NUMBER,c.id AS LINE_ID, c.LINE_NAME,d.FLOOR_NAME from lib_capacity_calc_mst a, lib_capacity_calc_dtls b, lib_sewing_line c,lib_prod_floor d where d.ID=c.FLOOR_NAME AND d.LOCATION_ID = a.LOCATION_ID AND a.ID = b.MST_ID AND c.LOCATION_NAME = a.LOCATION_ID AND c.COMPANY_NAME = a.COMAPNY_ID AND a.COMAPNY_ID in ($company_id) AND a.LOCATION_ID in ($location_id) $floor_con $line_con AND b.DATE_CALC BETWEEN '$start_date'  AND '$end_date'";
        //echo $lib_capacity_calc_query; die;
        $line_capacity_obj = $this->db->query($lib_capacity_calc_query)->result();
        //print_r($line_capacity_obj); die;


        $line_id_arr=array();
        foreach($line_capacity_obj as $data){
            $line_id_arr[$data->LINE_ID]=$data->LINE_ID;
        }


        // and b.LOCATION_ID = $location_id
       
        $variable_setup_value=sql_select( "select YARN_ISS_WITH_SERV_APP from VARIABLE_ORDER_TRACKING where COMPANY_NAME='$company_id' and VARIABLE_LIST=67" );
        if($variable_setup_value[0]->YARN_ISS_WITH_SERV_APP==1){
            $lib_standard_cm_entry_query = "SELECT a.ID,a.COMPANY_ID,b.LOCATION_ID,b.WORKING_HOUR,b.APPLYING_PERIOD_DATE,b.APPLYING_PERIOD_TO_DATE from lib_standard_cm_entry a,lib_standard_cm_entry_dtls b where a.ID = b.MST_ID and a.COMPANY_ID = $company_id "; 
        }else{

            $lib_standard_cm_entry_query = "SELECT ID,COMPANY_ID,0 as LOCATION_ID, WORKING_HOUR,APPLYING_PERIOD_DATE,APPLYING_PERIOD_TO_DATE from lib_standard_cm_entry where COMPANY_ID = $company_id "; 
        }
       // echo $lib_standard_cm_entry_query;die;
        $cm_entry_data = $this->db->query($lib_standard_cm_entry_query)->result();
        $capacity_hour_data_arr=array();
        foreach($cm_entry_data as $data){
            $start_date_str = date_create(date('Y-m-d',strtotime($data->APPLYING_PERIOD_DATE)));
            $end_date_str = date_create(date('Y-m-d',strtotime($data->APPLYING_PERIOD_TO_DATE)));
            $diff=date_diff($start_date_str,$end_date_str);
           
            for($d=0;$d<=$diff->days;$d++){
                $date = date_format($start_date_str,"Y-m-d");
                $capacity_hour_data_arr[$data->COMPANY_ID][$data->LOCATION_ID][$date] = $data->WORKING_HOUR;        
                date_add($start_date_str,date_interval_create_from_date_string("1 days"));
            }
        }
        //print_r($capacity_hour_data_arr);die;

        $planning_board_tables = "select a.LINE_ID,a.START_DATE,a.END_DATE,a.PLAN_ID, a.PLAN_QNTY AS A_QUANTITY ,a.COMPANY_ID,a.LOCATION_ID,b.PLAN_DATE as B_PLAN_DATE,b.PLAN_QNTY, b.WORKING_HOUR  from ppl_sewing_plan_board a,ppl_sewing_plan_board_dtls b WHERE a.PLAN_ID = b.PLAN_ID AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND a.COMPANY_ID in ($company_id) AND a.LOCATION_ID in ($location_id) ";
        $planning_board_tables_res = $this->db->query($planning_board_tables)->result();
        //print_r($planning_board_tables);die;



        $plan_data_arr=array();
        foreach($planning_board_tables_res as $row){
            
            $B_PLAN_DATE = date('d-M-y',strtotime($row->B_PLAN_DATE));
            $key = $row->COMPANY_ID.'**'.$row->LOCATION_ID.'**'.$row->LINE_ID.'**'.$B_PLAN_DATE;
            //echo $key; die;
            $plan_data_arr['BOOKED_HOURS'][$key] += $row->WORKING_HOUR;
            $plan_data_arr['PLAN_QNTY'][$key] += $row->PLAN_QNTY;
       }
        //print_r($plan_data_arr);die;

        $result_array = array();

        foreach($line_capacity_obj as $data){
            $date_str = date('d-M-y',strtotime($data->DATE_CALC));
            $key = $data->COMAPNY_ID.'**'.$data->LOCATION_ID.'**'.$data->LINE_ID.'**'.$date_str;
            //echo $key;die;
            $locatin_id = ($variable_setup_value[0]->YARN_ISS_WITH_SERV_APP == 1) ? $data->LOCATION_ID : 0;
            $date = date('Y-m-d', strtotime($data->DATE_CALC));
            $data->CAPACITY_MIN = $capacity_hour_data_arr[$data->COMAPNY_ID][$locatin_id][$date];

            $result_array[] = [
                    "company_id" => $data->COMAPNY_ID,
                    "location_id" => $data->LOCATION_ID,
                    "date" => $data->DATE_CALC,
                    "floor_id" => $data->FLOOR_NUMBER,
                    "floor_name" => $data->FLOOR_NAME,
                    "line_id" => $data->LINE_ID,
                    "line_name" => $data->LINE_NAME,                  
                    "capacity_hours" =>  number_format($data->CAPACITY_MIN, 2),
                    "booked_hours" => number_format($plan_data_arr['BOOKED_HOURS'][$key], 2),
                    "available_hours" => number_format($data->CAPACITY_MIN - $plan_data_arr['BOOKED_HOURS'][$key], 2),
                    "planned_qty_pcs" => number_format($plan_data_arr['PLAN_QNTY'][$key], 2),
                ];
        }
        return $result_array;
	}
}