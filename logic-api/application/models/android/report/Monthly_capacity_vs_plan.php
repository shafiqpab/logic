<?php
class Monthly_capacity_vs_plan extends CI_Model {

	function __construct() {
		error_reporting(0);
		parent::__construct();
	}

	public function get_monthly_capacity_vs_plan_info($fromDate, $toDate, $company, $location) {
		
		list($start_month,$start_year)=explode('-',$fromDate);
		list($end_month,$end_year)=explode('-',$toDate);
		
		
		$cbo_company_name=str_replace("'","",$company);
		$cbo_location_id=str_replace("'","",$location);
		$cbo_year_name=str_replace("'","",$start_year);
		$cbo_month=str_replace("'","",$start_month);
		$cbo_month_end=str_replace("'","",$end_month);
		$cbo_end_year_name=str_replace("'","",$end_year);
		
		$com_res = $this->db->query("select ID,COMPANY_NAME from lib_company where status_active =1 and is_deleted=0  order by company_name")->result();
		foreach ($com_res as $rows) {
			$companyArr[$rows->ID] = $rows->COMPANY_NAME;
		}

		$loc_res = $this->db->query("select ID,LOCATION_NAME from lib_location where status_active =1 and is_deleted=0  order by LOCATION_NAME")->result();
		foreach ($loc_res as $rows) {
			$locationArr[$rows->ID] = $rows->LOCATION_NAME;
		}


		$daysinmonth=cal_days_in_month(CAL_GREGORIAN, $cbo_month_end, $cbo_year_name);
		$s_date=$cbo_year_name."-".$cbo_month."-"."01";
		$e_date=$cbo_end_year_name."-".$cbo_month_end."-".$daysinmonth;
		
		
		
		
		if ($this->db->dbdriver != 'mysqli') {
			$s_date = date("d-M-Y", strtotime($s_date));
			$e_date = date("d-M-Y", strtotime($e_date));
	
		}
	

		$tot_month = datediff( 'm', $s_date,$e_date);
	
	
		for($i=0; $i<= $tot_month; $i++ )
		{
			$next_month=month_add($s_date,$i);
			$month_arr[]=date("Y-m",strtotime($next_month));
			$fullMonthArr[]=date("M-Y",strtotime($next_month));
		}
	
		$date_cond="AND b.pub_shipment_date between '$s_date' and '$e_date'";
		if($cbo_company_name!=0){$company_con=" AND a.company_name=$cbo_company_name";}
		if($cbo_location_id!=0){$location_con=" AND a.LOCATION_NAME=$cbo_location_id";}
		
	
		if($cbo_company_name!=0){$company_con2=" AND a.COMAPNY_ID=$cbo_company_name";}
		if($cbo_location_id!=0){$location_con2=" AND a.LOCATION_ID=$cbo_location_id";}
		$sql_capacity="Select a.COMAPNY_ID,a.LOCATION_ID, b.CAPACITY_MIN,b.DATE_CALC from lib_capacity_calc_mst a, lib_capacity_calc_dtls b  where a.id=b.mst_id $company_con2 and a.status_active=1 and a.is_deleted=0 AND a.YEAR between '$cbo_year_name' and '$cbo_end_year_name' and b.DATE_CALC between '$s_date' and '$e_date'";
		$sql_capacityRes=$this->db->query($sql_capacity)->result();
		$capacityArr=array();
		foreach($sql_capacityRes as $row)
		{
			$monthKey=date("Y-m",strtotime($row->DATE_CALC));
			$key=$row->COMAPNY_ID.'__'.$row->LOCATION_ID;
			$capacityArr[$key][$monthKey]+=$row->CAPACITY_MIN;
		}
		unset($sql_capacityRes);	
		
	
	$sql_plan="SELECT (a.SMV_PCS * c.plan_qnty) AS PLAN_MINIT, c.COMPANY_ID, c.LOCATION_ID, b.PUB_SHIPMENT_DATE, b.PO_QUANTITY, pd.PLAN_DATE FROM wo_po_break_down b, WO_PO_DETAILS_MAS_SET_DETAILS a, ppl_sewing_plan_board_powise  pp, ppl_sewing_plan_board_dtls pd, ppl_sewing_plan_board c WHERE a.job_no = b.job_no_mst  AND b.id = pp.po_break_down_id AND pp.plan_id = pd.plan_id AND c.ITEM_NUMBER_ID=a.GMTS_ITEM_ID and pp.ITEM_NUMBER_ID=a.GMTS_ITEM_ID and c.ITEM_NUMBER_ID=a.GMTS_ITEM_ID AND pp.plan_id = c.plan_id AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 and pd.PLAN_DATE between '$s_date' and '$e_date'";
	
	 //return $sql_plan;die;
	$sql_planRes=$this->db->query($sql_plan)->result();
	$planMiniArr=array();
	foreach($sql_planRes as $row)
	{
		$monthKey=date("Y-m",strtotime($row->PLAN_DATE));

		$key=$row->COMPANY_ID.'__'.$row->LOCATION_ID;
		$planMiniArr[$key][$monthKey]+=$row->PLAN_MINIT;
	}
	unset($sql_planRes);
		
		
		
		
		
		$monthArr=array();
		$i=0;
		$monthArr[$i]['company_id']=0;
		$monthArr[$i]['company_name']='';
		$monthArr[$i]['location_id']=0;
		$monthArr[$i]['location_name']='';
		$monthArr[$i]['month']='';
		$monthArr[$i]['capacity_minute']=0;
		$monthArr[$i]['plan_minute']=0;
		
		
		foreach($capacityArr as $company_location=>$dataRows)
		{
			list($company_id,$location_id)=explode('__',$company_location);
				foreach($month_arr as $monthVal)
				{
					$capacity=$capacityArr[$company_location][$monthVal];
					$plan=$planMiniArr[$company_location][$monthVal];
					
					$monthArr[$i]['company_id']=$company_id;
					$monthArr[$i]['company_name']=$companyArr[$company_id];
					
					$monthArr[$i]['location_id']=$location_id;
					$monthArr[$i]['location_name']=($location_id)?$locationArr[$location_id]:"";
					
					$monthArr[$i]['month']=$monthVal;
					$monthArr[$i]['capacity_minute']=$capacity*1;
					$monthArr[$i]['plan_minute']=$plan*1;
					$i++;
				}
			
			
		}
						
		return $monthArr;
	
	}
	

}
