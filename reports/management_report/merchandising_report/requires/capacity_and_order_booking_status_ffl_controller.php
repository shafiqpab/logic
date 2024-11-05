<?
/*-------------------------------------------- Comments----------------
Version                  :   V2
Purpose			         : 	This form will create  Capacity & order Booking Status Report
Functionality	         :	
JS Functions	         :
Created by		         :	Monzu 
Creation date 	         : 
Requirment Client        : 
Requirment By            : 
Requirment type          : 
Requirment               : 
Affected page            : 
Affected Code            :                   
DB Script                : 
Updated by 		         : 		
Update date		         : 		   
QC Performed BY	         :		
QC Date			         :	
Comments		         :  Oracle Compatible Version
-----------------------------------------------------------------------*/
session_start();
include('../../../../includes/common.php');

extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');
$_SESSION['page_permission']=$permission;

$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer where status_active=1 and is_deleted=0",'id','short_name');
$buyer_full_name_arr=return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0",'id','buyer_name'); 

$company_short_name_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
$company_arr=return_library_array( "select id, company_name from  lib_company",'id','company_name');
$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );

$supplier_details=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$country_name_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
$company_team_name_arr=return_library_array( "select id,team_name from lib_marketing_team",'id','team_name');
$company_team_member_name_arr=return_library_array( "select id,team_member_name from  lib_mkt_team_member_info",'id','team_member_name');
$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where file_type=1",'master_tble_id','image_location');

//$commission_for_shipment_schedule_arr=return_library_array( "select job_no,commission from  wo_pre_cost_dtls",'job_no','commission');
//$costing_per_arr=return_library_array( "select job_no,costing_per from  wo_pre_cost_mst",'job_no','costing_per');


$receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );   	 
} 

if ($action=="load_drop_down_team_member")
{
	echo create_drop_down( "cbo_team_member", 110, "select id,team_member_name 	 from lib_mkt_team_member_info  where team_id='$data' and status_active=1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-Select Team Member-", $selected, "" );   	 
}

if ($action=="cbo_factory_merchant")
{
	echo create_drop_down( "cbo_factory_merchant", 110, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select --", $selected, "" );
}

if ($action=="get_defult_date")
{
	$report_date_catagory=return_field_value("report_date_catagory", "variable_order_tracking", "company_name=$data  and variable_list=42 and status_active=1 and is_deleted=0");
	if($report_date_catagory=="")
	{
		$report_date_catagory=1;
	}
	echo $report_date_catagory;
	die;
}

if ($action=="load_drop_down_agent")
{
	echo create_drop_down( "cbo_agent", 150, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (20,21)) group by a.id,a.buyer_name  order by buyer_name","id,buyer_name", 1, "-- Select Agent --", $selected, "" );  
	exit(); 	 
} 




if($type=="report_generate")
{
	$data=explode("_",$data);
	if($data[0]==0) $company_name="%%"; else $company_name=$data[0];
	if($data[1]==0) $buyer_name="%%"; else $buyer_name=$data[1];
	if(trim($data[2])!="") $start_date=$data[2];
	if(trim($data[3])!="") $end_date=$data[3];
	if(trim($data[4])=="0") $team_leader="%%"; else $team_leader="$data[4]";
	if(trim($data[5])=="0") $dealing_marchant="%%"; else $dealing_marchant="$data[5]";
	if(trim($data[11])=="0") $factory_marchant="%%"; else $factory_marchant="$data[11]";
	if(trim($data[12])=="0") $agent_con=""; else $agent_con=" and a.agent_name=$data[12]";
	
	
	if($db_type==0)
	{
		$start_date=change_date_format($start_date,'yyyy-mm-dd','-');
		$end_date=change_date_format($end_date,'yyyy-mm-dd','-');
    }
	if($db_type==2)
	{
		$start_date=change_date_format($start_date,'','-',1);
		$end_date=change_date_format($end_date,'','-',1);
    }
	
	
	
	$cbo_category_by=$data[6];
	$zero_value=$data[7];
	$cbo_capacity_source=$data[8];
	$cbo_year=$data[9];
	
	if(trim($data[10])!="") $style_ref_cond="%".trim($data[10])."%"; else $style_ref_cond="%%";
	
	$cbo_year=str_replace("'","",$cbo_year);
	if($db_type==0)
	{
		if(trim($cbo_year)!=0) $year_cond_2=" and YEAR(a.insert_date)=$cbo_year"; else $year_cond="";
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(a.insert_date,'YYYY')";
		if(trim($cbo_year)!=0) $year_cond_2=" $year_field_con=$cbo_year"; else $year_cond="";
	}
	
	if($cbo_category_by==1)
	{
		if ($start_date!="" && $end_date!="")
		{
			$date_cond="and c.country_ship_date between '$start_date' and  '$end_date'";
			$date_cond_target_basic="and b.date between '$start_date' and  '$end_date'";
		}
		else	
		{
			$date_cond="";
			$date_cond_target_basic="";
		}
	}
	else if($cbo_category_by==2)
	{
		if ($start_date!="" && $end_date!="")
		{
			$date_cond=" and b.pub_shipment_date between '$start_date' and  '$end_date'";
			$date_cond_target_basic="and b.date between '$start_date' and  '$end_date'";
		}
		else	
		{
			$date_cond="";
			$date_cond_target_basic="";
		}
	}
	else
	{
		if ($start_date!="" && $end_date!="")
		{
			if($db_type==0){
			$date_cond=" and date(b.insert_date) between '$start_date' and  '$end_date'";
			$date_cond_target_basic="and b.date between '$start_date' and  '$end_date'";
			}
			else if($db_type==2){
				$date_cond=" and TRUNC(b.insert_date) between '$start_date' and  '$end_date'";
				$date_cond_target_basic="and b.date between '$start_date' and  '$end_date'";
			}
		}
		else	
		{
			$date_cond="";
			$date_cond_target_basic="";
		}
	}
	//echo $date_cond.'=='.$cbo_category_by;
	if($cbo_capacity_source==1)
	{
		$capacity_source_cond="and c.capacity_source=1";
	}
	else if($cbo_capacity_source==3)
	{
		$capacity_source_cond="and c.capacity_source=3";
	}
	else
	{
		$capacity_source_cond="";
	}
	
	if ($start_date!="" && $end_date!="")
	{
		$year="";
		$sy = date('Y',strtotime($start_date));
		$ey = date('Y',strtotime($end_date));
		$dif_y=$ey-$sy;
		for($i=1; $i<$dif_y; $i++)
		{
		 $year.= $sy+$i.","; 
		}
		$tot_year= $sy;
		if($year !="")
		{
			$tot_year.=",".$year;
		}
		if($ey!=$sy)
		{
			if($year=="")
			{
				$tot_year.=",".$ey;
			}
			else
			{
				$tot_year.=$ey;	
			}
		}
		$year_cond="and a.year_id in($tot_year)";
	}
    $target_basic_qnty=array();
	$total_target_basic_qnty=0;
    $sm = date('m',strtotime($start_date));
	$em = date('m',strtotime($end_date));
	
	
	/*echo $start_date."**".$end_date;die;
	
	if($db_type==0)
	{
    $sql_con="SELECT  a.company_id,b.buyer_id,b.allocation_percentage";
	}
	
	if($db_type==2)
	{
    $sql_con="SELECT  b.buyer_id";
	}
	
	for($i=1;$i<=12;$i++)
	{
		 $sql_con .= ",SUM(CASE WHEN d.month_id =".$i. " THEN (d.capacity_month_pcs)   END) AS capa$i ,SUM(CASE WHEN d.month_id =".$i. " THEN (d.capacity_month_pcs* b.allocation_percentage)/100   END) AS sum$i";
	}
	$sql_con .= " FROM lib_capacity_allocation_mst a,lib_capacity_allocation_dtls b, lib_capacity_calc_mst c,  lib_capacity_year_dtls d
	WHERE 
	a.id=b.mst_id AND 
	a.year_id=c.year AND 
	a.month_id=d.month_id  AND 
	c.id=d.mst_id AND 
	a.company_id=$company_name   AND 
	c.comapny_id=$company_name AND
	a.year_id=$sy and 
	d.month_id between $sm and $em 
	$capacity_source_cond  and 
	a.status_active=1 and 
	a.is_deleted=0 and 
	b.status_active=1 and 
	b.is_deleted=0 and 
	c.status_active=1 and 
	c.is_deleted=0  
	GROUP BY b.buyer_id";
	
	echo $sql_con;
	
	
	$sql_data=sql_select($sql_con);
	foreach( $sql_data as $row)
		{
			$total_sum1 = $total_sum1+$row[csf("sum1")];
			$total_sum2 = $total_sum2+$row[csf("sum2")];
			$total_sum3 = $total_sum3+$row[csf("sum3")];
			$total_sum4 = $total_sum4+$row[csf("sum4")];
			$total_sum5 = $total_sum5+$row[csf("sum5")];
			$total_sum6 = $total_sum6+$row[csf("sum6")];
			$total_sum7 = $total_sum7+$row[csf("sum7")];
			$total_sum8 = $total_sum8+$row[csf("sum8")];
			$total_sum9 = $total_sum9+$row[csf("sum9")];
			$total_sum10 = $total_sum10+$row[csf("sum10")];
			$total_sum11 = $total_sum11+$row[csf("sum11")];
			$total_sum12 = $total_sum12+$row[csf("sum12")];
			$total_cap=$row[csf("sum1")]+$row[csf("sum2")]+$row[csf("sum3")]+$row[csf("sum4")]+$row[csf("sum5")]+$row[csf("sum6")]+$row[csf("sum7")]+$row[csf("sum8")]+$row[csf("sum9")]+$row[csf("sum10")]+$row[csf("sum11")]+$row[csf("sum12")];
			$target_basic_qnty[$row[csf("buyer_id")]]=$total_cap;
			$total_target_basic_qnty+=$total_cap;
		}
	
	*/
	
	
	$sql_con="SELECT  b.buyer_id, SUM((d.capacity_pcs* b.allocation_percentage)/100) as allocate_percent_qnty 
	FROM lib_capacity_allocation_mst a,lib_capacity_allocation_dtls b, lib_capacity_calc_mst c,  lib_capacity_calc_dtls d
	WHERE 
	a.id=b.mst_id AND 
	a.year_id=c.year AND 
	a.month_id=d.month_id  AND 
	c.id=d.mst_id AND 
	a.company_id=$company_name   AND 
	c.comapny_id=$company_name AND
	a.year_id=$sy and 
	d.date_calc between '$start_date' and  '$end_date' 
	$capacity_source_cond  and 
	c.location_id>0 and
	a.status_active=1 and 
	a.is_deleted=0 and 
	b.status_active=1 and 
	b.is_deleted=0 and 
	c.status_active=1 and 
	c.is_deleted=0  
	GROUP BY b.buyer_id";
	
	//echo $sql_con;die;
	
	
	$sql_data=sql_select($sql_con);
	foreach( $sql_data as $row)
	{
		
		$total_cap=$row[csf("allocate_percent_qnty")];
		$target_basic_qnty[$row[csf("buyer_id")]]=$total_cap;
		$total_target_basic_qnty+=$total_cap;
	}
  $asking_avg_rate_arr=return_library_array( "select company_id, asking_avg_rate from lib_standard_cm_entry",'company_id','asking_avg_rate');
  $basic_smv_arr=return_library_array( "select comapny_id, basic_smv from lib_capacity_calc_mst where year=$sy",'comapny_id','basic_smv');
 // $job_smv_arr=return_library_array( "select job_no, set_smv from wo_po_details_master",'job_no','set_smv');
  $buy_sew_effi_percent_arr=return_library_array( "select id, sewing_effi_plaing_per from lib_buyer",'id','sewing_effi_plaing_per');
  

$po_total_price_tot=0;
$quantity_tot=0;
$exfactory_tot=0;


$po_total_price_tot_c=0;
$quantity_tot_c=0;

$po_total_price_tot_p=0;
$quantity_tot_p=0;

$booked_basic_qnty_tot_c=0;
$booked_basic_qnty_tot_p=0;
/*$data_array=sql_select("select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.style_description, a.job_quantity, a.product_category, a.job_no, a.location_name, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.product_code, b.id, b.is_confirmed, b.po_number, sum(c.order_quantity/a.total_set_qnty) as po_quantity, sum(c.order_quantity) as po_quantity_pcs, b.pub_shipment_date, b.po_received_date, DATEDIFF(c.country_ship_date, '$date') date_diff_1, DATEDIFF(c.country_ship_date, '$date') date_diff_2, b.unit_price, sum(c.order_total) as po_total_price, b.details_remarks, b.shiping_status,c.country_ship_date,c.country_id, sum(d.ex_factory_qnty) as ex_factory_qnty, MAX(d.ex_factory_date) as ex_factory_date, DATEDIFF(c.country_ship_date, MAX(d.ex_factory_date)) date_diff_3, DATEDIFF(c.country_ship_date,MAX(d.ex_factory_date)) date_diff_4, b.t_year, b.t_month  from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c LEFT JOIN pro_ex_factory_mst d on c.po_break_down_id = d.po_break_down_id and c.country_id=d.country_id and d.status_active=1 and d.is_deleted=0 where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id   and a.company_name like '$company_name' and a.buyer_name like '$buyer_name' and a.team_leader like '$team_leader'  and a.dealing_marchant like '$dealing_marchant' $date_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id,c.country_id order by c.country_ship_date,a.job_no_prefix_num,b.id");*/
//$db_type=0;
if($db_type==0)
{
	if($cbo_category_by==1)
	{
		//sum(c.order_quantity/a.total_set_qnty) as po_quantity
		$data_array=sql_select("select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.style_description, a.job_quantity, a.product_category, a.job_no, a.location_name, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.factory_marchant,a.product_code, b.id, b.is_confirmed, b.po_number, sum(c.order_quantity/a.total_set_qnty) as po_quantity, sum(c.order_quantity) as po_quantity_pcs, b.pub_shipment_date, b.po_received_date,date(b.insert_date) as insert_date, DATEDIFF(c.country_ship_date, '$date') date_diff_1, DATEDIFF(c.country_ship_date, '$date') date_diff_2, b.unit_price, sum(c.order_total) as  order_total, b.details_remarks, b.delay_for, b.grouping,b.file_no,c.shiping_status,c.country_remarks,c.country_ship_date,c.country_id,  b.t_year, b.t_month  
from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, lib_buyer d
where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and a.buyer_name=d.id and d.status_active=1 and a.company_name like '$company_name' and a.buyer_name like '$buyer_name' and LOWER(a.style_ref_no) like LOWER('$style_ref_cond') and a.team_leader like '$team_leader'  and a.dealing_marchant like '$dealing_marchant' and a.factory_marchant like  '$factory_marchant' $agent_con $date_cond $year_cond_2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.style_description, a.job_quantity, a.product_category, a.job_no, a.location_name, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.factory_marchant,a.product_code,b.id,b.is_confirmed,b.po_number,b.pub_shipment_date,b.po_received_date,b.unit_price,b.details_remarks, b.delay_for,b.grouping,b.file_no,b.t_year, b.t_month,c.country_id,c.country_remarks,c.country_ship_date,c.shiping_status order by c.country_ship_date,a.job_no_prefix_num,b.id");
	}
	else
	{
		
		$data_array=sql_select("select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.style_description, a.job_quantity, a.product_category, a.job_no, a.location_name, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.factory_marchant,a.product_code, b.id, b.is_confirmed, b.po_number, sum(b.po_quantity) as po_quantity, sum(b.po_quantity*a.total_set_qnty) as po_quantity_pcs,b.shiping_status, b.pub_shipment_date, b.po_received_date,date(b.insert_date) as insert_date, DATEDIFF(b.pub_shipment_date, '$date') date_diff_1, DATEDIFF(b.pub_shipment_date, '$date') date_diff_2, b.unit_price, b.po_total_price as order_total, b.details_remarks, b.delay_for, b.grouping,b.file_no, b.t_year, b.t_month  
	 from wo_po_details_master a, wo_po_break_down b , lib_buyer d
	 where a.job_no=b.job_no_mst  and a.buyer_name=d.id and d.status_active=1 and a.company_name like '$company_name' and a.buyer_name like '$buyer_name' and LOWER(a.style_ref_no) like LOWER('$style_ref_cond') and a.team_leader like '$team_leader'  and a.dealing_marchant like '$dealing_marchant' and a.factory_marchant like '$factory_marchant' $agent_con  $date_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.style_description, a.job_quantity, a.product_category, a.job_no, a.location_name, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.factory_marchant,a.product_code,b.id,b.is_confirmed,b.po_number,b.pub_shipment_date,b.po_received_date,b.unit_price,b.po_total_price,b.details_remarks, b.delay_for,b.grouping,b.file_no,b.t_year, b.t_month order by b.pub_shipment_date,a.job_no_prefix_num,b.id");
	}


/*$data_array=sql_select("select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.style_description, a.job_quantity, a.product_category, a.job_no, a.location_name, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.product_code, b.id, b.is_confirmed, b.po_number, sum(c.order_quantity/a.total_set_qnty) as po_quantity, sum(c.order_quantity) as po_quantity_pcs, b.pub_shipment_date, b.po_received_date, DATEDIFF(c.country_ship_date, '$date') date_diff_1, DATEDIFF(c.country_ship_date, '$date') date_diff_2, b.unit_price, sum(c.order_total) as po_total_price, b.details_remarks, c.shiping_status,c.country_ship_date,c.country_id,  b.t_year, b.t_month  from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id   and a.company_name like '$company_name' and a.buyer_name like '$buyer_name' and a.team_leader like '$team_leader'  and a.dealing_marchant like '$dealing_marchant' $date_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.style_description, a.job_quantity, a.product_category, a.job_no, a.location_name, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.product_code,b.id,b.is_confirmed,b.po_number,b.pub_shipment_date,b.po_received_date,b.unit_price,b.details_remarks,b.t_year, b.t_month,c.country_id,c.country_ship_date,c.shiping_status order by c.country_ship_date,a.job_no_prefix_num,b.id");*/
}
if($db_type==2)
{
	$date=date('d-m-Y');
	if($cbo_category_by==1)
	{ // sum(c.order_quantity/a.total_set_qnty) as po_quantity
		$data_array=sql_select("select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.style_description, a.job_quantity, a.product_category, a.job_no, a.location_name, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant, a.factory_marchant, a.product_code, b.id, b.is_confirmed, b.po_number, sum(c.order_quantity/a.total_set_qnty) as po_quantity, sum(c.order_quantity) as po_quantity_pcs, sum(c.order_total) as order_total, b.pub_shipment_date, b.po_received_date,min(TRUNC(b.insert_date)) as insert_date, (c.country_ship_date - to_date('$date','dd-mm-yyyy')) date_diff_1, (c.country_ship_date - to_date('$date','dd-mm-yyyy')) date_diff_2, b.unit_price, b.po_total_price, b.details_remarks, b.delay_for, b.grouping, b.file_no, c.shiping_status, c.country_remarks, c.country_ship_date, c.country_id, b.t_year, b.t_month 
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c , lib_buyer d
		where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and a.buyer_name=d.id and d.status_active=1 and a.company_name like '$company_name' and a.buyer_name like '$buyer_name' and LOWER(a.style_ref_no) like LOWER('$style_ref_cond') and a.team_leader like '$team_leader' and a.dealing_marchant like '$dealing_marchant' and a.factory_marchant like '$factory_marchant' $agent_con $date_cond $year_cond_2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
		group by a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.style_description, a.job_quantity, a.product_category, a.job_no, a.location_name, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant, a.factory_marchant, a.product_code, b.id, b.is_confirmed, b.po_number, b.pub_shipment_date, b.po_received_date, b.unit_price, b.po_total_price, b.details_remarks, b.delay_for, b.grouping, b.file_no, b.t_year, b.t_month, c.country_id, c.country_remarks, c.country_ship_date, c.shiping_status order by c.country_ship_date, a.job_no_prefix_num, b.id");
		
		
	}
	else
	{
		$data_array=sql_select("select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.style_description, a.job_quantity, a.product_category, a.job_no, a.location_name, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.factory_marchant,a.product_code, b.id, b.is_confirmed, b.po_number, sum(b.po_quantity) as po_quantity, sum(b.po_quantity*a.total_set_qnty) as po_quantity_pcs,b.shiping_status, b.pub_shipment_date, b.po_received_date, min(TRUNC(b.insert_date)) as insert_date,(b.pub_shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_1, (b.pub_shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_2, b.unit_price, b.po_total_price as order_total, b.details_remarks, b.delay_for,b.grouping,b.file_no, b.shiping_status as shiping_status2, b.t_year, b.t_month  
		from wo_po_details_master a, wo_po_break_down b , lib_buyer d
		where a.job_no=b.job_no_mst and a.buyer_name=d.id and d.status_active=1 and a.company_name like '$company_name' and a.buyer_name like '$buyer_name' and LOWER(a.style_ref_no) like LOWER('$style_ref_cond') and a.team_leader like '$team_leader'  and a.dealing_marchant like '$dealing_marchant' and a.factory_marchant like  '$factory_marchant' $agent_con $date_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 group by a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.style_description, a.job_quantity, a.product_category, a.job_no, a.location_name, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.factory_marchant,a.product_code,b.id,b.is_confirmed,b.po_number,b.shiping_status,b.pub_shipment_date,b.po_received_date,b.unit_price,b.po_total_price,b.details_remarks, b.delay_for,b.grouping,b.file_no,b.t_year, b.t_month order by b.pub_shipment_date,a.job_no_prefix_num,b.id");
	//$data_array=sql_select($sql); //a.job_no_prefix_num,b.id 
	}
}

if(trim($data[4])=="0" && trim($data[5])=="0" && trim($data[11])=="0")
{ //$team_leader="%%"; else $team_leader="$data[4]";
$sql_data="select a.company_id,a.location_id,a.year_id,a.month_id, b.buyer_id,b.allocation_percentage 
from lib_capacity_allocation_mst a, lib_capacity_allocation_dtls b, lib_buyer c 
where a.id=b.mst_id and b.buyer_id=c.id and c.status_active=1 and a.company_id like '$company_name' and b.buyer_id like '$buyer_name'  $year_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";	
}
else
{
	$capBuyerId='';
	foreach ($data_array as $rowforbuyer)
	{
		$capBuyerId.=$rowforbuyer['buyer_name'].",";
	}
	$capBuyerId=rtrim($capBuyerId,",");
	$sql_data="select a.company_id,a.location_id,a.year_id,a.month_id, b.buyer_id,b.allocation_percentage 
	from lib_capacity_allocation_mst a, lib_capacity_allocation_dtls b, lib_buyer c 
	where a.id=b.mst_id and b.buyer_id=c.id and c.status_active=1 and a.company_id like '$company_name' and   b.buyer_id in($capBuyerId)  $year_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
}
//echo $sql_data;die;
$sql_data=sql_select($sql_data);
$company_buyer_array=array();
foreach ($sql_data as $row)
{
	$company_buyer_array[$row[csf("company_id")]][$row[csf("buyer_id")]]= array("order_qty_pcs"=>0,"order_value"=>0,"ex_factory"=>0,"smv"=>0);
	$company_buyer_array_confirm[$row[csf("company_id")]][$row[csf("buyer_id")]]= array("order_qty_pcs"=>0,"order_value"=>0,"ex_factory"=>0);
	$company_buyer_array_projected[$row[csf("company_id")]][$row[csf("buyer_id")]]= array("order_qty_pcs"=>0,"order_value"=>0,"ex_factory"=>0);
}

//========================================================================================
$po_array_for_cond=array();
$job_array_for_cond=array();
foreach ($data_array as $row_po_job)
{
 $po_array_for_cond[$row_po_job[csf("id")]]=$row_po_job[csf("id")];
 $job_array_for_cond[$row_po_job[csf("job_no")]]="'".$row_po_job[csf("job_no")]."'";
}

$job_arr_cond=array_chunk($job_array_for_cond,1000, true);
$job_cond_for_in="";
$ji=0;
foreach($job_arr_cond as $key=> $value)
{
   if($ji==0)
   {
	$job_cond_for_in=" and job_no  in(".implode(",",$value).")"; 
   }
   else
   {
	$job_cond_for_in.=" or job_no  in(".implode(",",$value).")";
   }
   $ji++;
}
$job_arr_cond=array();
$job_array_for_cond=array();
$commission_for_shipment_schedule_arr=return_library_array( "select job_no,commission from  wo_pre_cost_dtls where 1=1 $job_cond_for_in",'job_no','commission');
$costing_per_arr=return_library_array( "select job_no,costing_per from  wo_pre_cost_mst where 1=1 $job_cond_for_in",'job_no','costing_per');
$job_smv_arr=return_library_array( "select job_no, set_smv from wo_po_details_master where 1=1 $job_cond_for_in",'job_no','set_smv');
//================================================================================
$po_arr_cond=array_chunk($po_array_for_cond,1000, true);
$po_cond_for_in="";
$pi=0;
foreach($po_arr_cond as $key=> $value)
{
   if($pi==0)
   {
	$po_cond_for_in=" and ( po_break_down_id  in(".implode(",",$value).")"; 
   }
   else
   {
	$po_cond_for_in.=" or po_break_down_id  in(".implode(",",$value).")";
   }
   $pi++;
}
$po_cond_for_in.=" )";
$po_arr_cond=array();
$po_array_for_cond=array();

$exfactory_data_array=array();
if($cbo_category_by==1)
{
	/*$exfactory_data=sql_select("select po_break_down_id,country_id,sum(ex_factory_qnty) as ex_factory_qnty, MAX(ex_factory_date) as ex_factory_date from pro_ex_factory_mst  where 1=1 $po_cond_for_in and status_active=1 and is_deleted=0 group by po_break_down_id,country_id");*/
	$exfactory_data=sql_select("select po_break_down_id,country_id,MAX(ex_factory_date) as ex_factory_date,
	sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
	sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty
	 
	 from pro_ex_factory_mst  where 1=1 $po_cond_for_in and status_active=1 and is_deleted=0 group by po_break_down_id,country_id");
	foreach($exfactory_data as $exfatory_row)
	{
		$exfactory_data_array[$exfatory_row[csf('po_break_down_id')]][$exfatory_row[csf('country_id')]][ex_factory_qnty]=$exfatory_row[csf('ex_factory_qnty')]-$exfatory_row[csf('ex_factory_return_qnty')];
		$exfactory_data_array[$exfatory_row[csf('po_break_down_id')]][$exfatory_row[csf('country_id')]][ex_factory_date]=$exfatory_row[csf('ex_factory_date')];
	}
}
else
{
	/*echo "select po_break_down_id,MAX(ex_factory_date) as ex_factory_date,
	sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
	sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty 
	from pro_ex_factory_mst  where 1=1 $po_cond_for_in and status_active=1 and is_deleted=0 group by po_break_down_id <br>";*/
	$exfactory_data=sql_select("select po_break_down_id,MAX(ex_factory_date) as ex_factory_date,
	sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
	sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty 
	from pro_ex_factory_mst  where 1=1 $po_cond_for_in and status_active=1 and is_deleted=0 group by po_break_down_id");
	foreach($exfactory_data as $exfatory_row)
	{
		$exfactory_data_array[$exfatory_row[csf('po_break_down_id')]][ex_factory_qnty]=$exfatory_row[csf('ex_factory_qnty')]-$exfatory_row[csf('ex_factory_return_qnty')];
		$exfactory_data_array[$exfatory_row[csf('po_break_down_id')]][ex_factory_date]=$exfatory_row[csf('ex_factory_date')];
	}
}
$exfactory_data=array();
//========================================================================

$exfactory_tot_yet=0;
$exfactory_tot_over=0;

foreach ($data_array as $row)
{
 $y = date('Y',strtotime($row[csf("pub_shipment_date")]));
 $po_total_price_tot+=$row[csf("order_total")];
 $quantity_tot+=$row[csf("po_quantity_pcs")];
 //$exfactory_tot+=$row[csf("ex_factory_qnty")];
 //$exfactory_tot+=$exfactory_data_array[$row[csf("id")]][$row[csf("country_id")]][ex_factory_qnty];
 $company_buyer_array[$row[csf("company_name")]][$row[csf("buyer_name")]][order_qty_pcs]+=$row[csf("po_quantity_pcs")];
 $company_buyer_array[$row[csf("company_name")]][$row[csf("buyer_name")]][order_value]+=$row[csf("order_total")];
 $company_buyer_array[$row[csf("company_name")]][$row[csf("buyer_name")]][style_ref_no][$row[csf("style_ref_no")]]=$row[csf("style_ref_no")];
 //$company_buyer_array[$row[csf("company_name")]][$row[csf("buyer_name")]][ex_factory]+=$row[csf("ex_factory_qnty")];
 if($cbo_category_by==1)
 {
	 $exfactory_tot+=$exfactory_data_array[$row[csf("id")]][$row[csf("country_id")]][ex_factory_qnty];
	 $company_buyer_array[$row[csf("company_name")]][$row[csf("buyer_name")]][ex_factory]+=$exfactory_data_array[$row[csf("id")]][$row[csf("country_id")]][ex_factory_qnty];
	 if($row[csf("po_quantity_pcs")]-$exfactory_data_array[$row[csf("id")]][$row[csf("country_id")]][ex_factory_qnty]>0)
	 {
		 $exfactory_tot_yet+=$row[csf("po_quantity_pcs")]-$exfactory_data_array[$row[csf("id")]][$row[csf("country_id")]][ex_factory_qnty];
	 }
	 else
	 {
		 $exfactory_tot_over+=$row[csf("po_quantity_pcs")]-$exfactory_data_array[$row[csf("id")]][$row[csf("country_id")]][ex_factory_qnty];
	 }
 }
 else
 {
	 $exfactory_tot+=$exfactory_data_array[$row[csf("id")]][ex_factory_qnty];
	 $company_buyer_array[$row[csf("company_name")]][$row[csf("buyer_name")]][ex_factory]+=$exfactory_data_array[$row[csf("id")]][ex_factory_qnty];
	 if($row[csf("po_quantity_pcs")]-$exfactory_data_array[$row[csf("id")]][ex_factory_qnty]>0)
	 {
		 $exfactory_tot_yet+=$row[csf("po_quantity_pcs")]-$exfactory_data_array[$row[csf("id")]][ex_factory_qnty];
	 }
	 else
	 {
		 $exfactory_tot_over+=$row[csf("po_quantity_pcs")]-$exfactory_data_array[$row[csf("id")]][ex_factory_qnty];
	 }
 }
 
 
 $company_buyer_array[$row[csf("company_name")]][$row[csf("buyer_name")]][smv]+=($job_smv_arr[$row[csf("job_no")]])*$row[csf('po_quantity')];
 if($precost_smv_arr[$row[csf("job_no")]] !="" || $job_smv_arr[$row[csf("job_no")]] !=0)
 {
	$booked_basic_qnty=($row[csf("po_quantity")]*($job_smv_arr[$row[csf("job_no")]]))/$basic_smv_arr[$row[csf("company_name")]];
	$company_buyer_array[$row[csf("company_name")]][$row[csf("buyer_name")]][booked_basic_qnty]+=$booked_basic_qnty;
	$booked_basic_qnty_tot+=$booked_basic_qnty;
 }

if($row[csf("is_confirmed")]==1)
{
$po_total_price_tot_c+=$row[csf("order_total")];
$quantity_tot_c+=$row[csf("po_quantity_pcs")];
$company_buyer_array_confirm[$row[csf("company_name")]][$row[csf("buyer_name")]][order_qty_pcs]+=$row[csf("po_quantity_pcs")];
$company_buyer_array_confirm[$row[csf("company_name")]][$row[csf("buyer_name")]][order_value]+=$row[csf("order_total")];
$company_buyer_array_confirm[$row[csf("company_name")]][$row[csf("buyer_name")]][style_ref_no][$row[csf("style_ref_no")]]=$row[csf("style_ref_no")];
if($cbo_category_by==1)
 {
	 $company_buyer_array_confirm[$row[csf("company_name")]][$row[csf("buyer_name")]][ex_factory]+=$exfactory_data_array[$row[csf("id")]][$row[csf("country_id")]][ex_factory_qnty];
 }
 else
 {
	$company_buyer_array_confirm[$row[csf("company_name")]][$row[csf("buyer_name")]][ex_factory]+=$exfactory_data_array[$row[csf("id")]][ex_factory_qnty];
 
 }
$company_buyer_array_confirm[$row[csf("company_name")]][$row[csf("buyer_name")]][smv]+=($job_smv_arr[$row[csf("job_no")]])*$row[csf('po_quantity')];
if($precost_smv_arr[$row[csf("job_no")]] !="" || $job_smv_arr[$row[csf("job_no")]] !=0)
 {
	$booked_basic_qnty_c=($row[csf("po_quantity")]*($job_smv_arr[$row[csf("job_no")]]))/$basic_smv_arr[$row[csf("company_name")]];
	$company_buyer_array_confirm[$row[csf("company_name")]][$row[csf("buyer_name")]][booked_basic_qnty]+=$booked_basic_qnty_c;
	$booked_basic_qnty_tot_c+=$booked_basic_qnty_c;
 }
}

if($row[csf("is_confirmed")]==2)
{
$po_total_price_tot_p+=$row[csf("order_total")];
$quantity_tot_p+=$row[csf("po_quantity_pcs")];
$company_buyer_array_projected[$row[csf("company_name")]][$row[csf("buyer_name")]][order_qty_pcs]+=$row[csf("po_quantity_pcs")];
$company_buyer_array_projected[$row[csf("company_name")]][$row[csf("buyer_name")]][order_value]+=$row[csf("order_total")];
$company_buyer_array_projected[$row[csf("company_name")]][$row[csf("buyer_name")]][style_ref_no][$row[csf("style_ref_no")]]=$row[csf("style_ref_no")];
if($cbo_category_by==1)
 {
	$company_buyer_array_projected[$row[csf("company_name")]][$row[csf("buyer_name")]][ex_factory]+=$exfactory_data_array[$row[csf("id")]][$row[csf("country_id")]][ex_factory_qnty];
 }
 else
 {
	$company_buyer_array_projected[$row[csf("company_name")]][$row[csf("buyer_name")]][ex_factory]+=$exfactory_data_array[$row[csf("id")]][ex_factory_qnty];
 }
$company_buyer_array_projected[$row[csf("company_name")]][$row[csf("buyer_name")]][smv]+=($job_smv_arr[$row[csf("job_no")]])*$row[csf('po_quantity')];
if($precost_smv_arr[$row[csf("job_no")]] !="" || $job_smv_arr[$row[csf("job_no")]] !=0)
 {
	$booked_basic_qnty_p=($row[csf("po_quantity")]*($job_smv_arr[$row[csf("job_no")]]))/$basic_smv_arr[$row[csf("company_name")]];
	$company_buyer_array_projected[$row[csf("company_name")]][$row[csf("buyer_name")]][booked_basic_qnty]+=$booked_basic_qnty_p;
 }
$booked_basic_qnty_tot_p+=$booked_basic_qnty_p;
}
}
?>
<div>
<table width="100%">
<tr>
<td align="center" id="projected_and_confirmed_order">
<table width="1400" cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all" >
<thead>
<tr>
<th>
<input type="button" id="projected_and_confirmed_order_print_button" value="Print" onClick="print_report_part_by_part('projected_and_confirmed_order','#projected_and_confirmed_order_print_button')"/>
</th>
<th colspan="19">Projected and Confirmed Order - <?  echo date('M,Y',strtotime($start_date));?> </th>
</tr>
<tr>
<th width="50">SL</th>
<th width="70">Company Name</th>
<th width="70">Buyer Name</th>
<th width="50">Avg.SMV</th>
<th width="100">Ord. Qty. (Pcs)</th>
<th width="80">No of Style</th>
<th width="100">Allocated Capacity (Pcs)</th>
<th width="50">%</th>
<th width="100">Booked Basic  Qty (Pcs)</th>
<th width="50">%</th>
<th width="80">Balance Basic (Pcs)</th>
<th width="50"> %</th>
<th width="100">Ex-factory Qty. (Pcs)</th>
<th width="100">Yet to Ex-factory (Pcs)</th>
<th width="50">Ex-fac Bal. %</th>
<th width="100">Ord. Value (USD)</th>
<th width="50">Avg.Rate (USD)</th>
<th width="50">Buyer Share % </th>
<th width="50">Avg Basic Rate (USD)</th>
<th width="">Stnd. Devn.(USD)</th>
</tr> 
</thead>
<tbody>
<?
/*$ordv = array();
foreach ($company_buyer_array as $key=>$value)
{
	$i=0;
	

	foreach($value as $buyer=> $value1)
	{
		$ordv[]=$value1[order_value];
	}
}*/

function cmp($a, $b) {
    if ($a[order_value] == $b[order_value]) {
        return 0;
    }
    return ($a[order_value] > $b[order_value]) ? -1 : 1;
}

class SortMdArray {
    public $sort_order = 'asc'; // default
    public $sort_key = 'order_value'; // default
     
    public function sortByKey(&$value) {       
        uasort($value, array(__CLASS__, 'sortByKeyCallback'));     
    }
     
    function sortByKeyCallback($a, $b) {
        if($this->sort_order == 'asc') {
            $return = $a[$this->sort_key] - $b[$this->sort_key];
        } else if($this->sort_order == 'desc') {
            $return = $b[$this->sort_key] - $a[$this->sort_key];
        }
        return $return;
    } 
}

$total_avg_sam=0;
$avg_rate=0;
$percent_total=0;
$tot_style=0;

//var_dump($company_buyer_array);

foreach ($company_buyer_array as $key=>$value)
{
	$sort = new SortMdArray;
	$sort->sort_order = 'desc';
    $sort->sortByKey($value);
    //uasort($value,'cmp');

	$i=0;
	foreach($value as $buyer=> $value1)
	{
		if($zero_value==0)
		{
			if($value1[order_qty_pcs]>0)
			{
				$i++;
				if ($i%2==0)  
				$bgcolor="#E9F3FF";
				else
				$bgcolor="#FFFFFF";	
					?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
				<td width="50" align="center"><? echo $i;?></td>
				<td width="70" align="center"><? echo $company_short_name_arr[$key];?></td>
				<td width="70" align="center"><? echo $buyer_short_name_arr[$buyer]?></td>
				<td width="50" align="right"><? echo number_format($value1[smv]/$value1[order_qty_pcs],2); $total_avg_sam+=$value1[smv];?></td>
				
				<td width="100" align="right"><? echo number_format($value1[order_qty_pcs],0);?></td>
				<td width="80" align="right"><? echo count($value1[style_ref_no]);$tot_style+=count($value1[style_ref_no]);?></td>
				<td width="100" align="right" bgcolor="#CCCCCC"><? echo number_format($target_basic_qnty[$buyer],0); $target_basic_qnty_tot+=$target_basic_qnty[$buyer];?></td>
				<td width="50" align="right" bgcolor="#CCCCCC">
				<?  echo number_format(($target_basic_qnty[$buyer]/$total_target_basic_qnty)*100,2); $percent_total+=($target_basic_qnty[$buyer]/$total_target_basic_qnty)*100; ?>
				</td>
				<td width="100" align="right" bgcolor="#CCCCCC"><? echo number_format($value1[booked_basic_qnty],0);?></td>
				<td width="50" align="right" bgcolor="#CCCCCC"><? echo number_format(($value1[booked_basic_qnty]/$target_basic_qnty[$buyer])*100,2); ?></td>
				<td width="80" align="right" bgcolor="#CCCCCC"><? echo number_format($target_basic_qnty[$buyer]-$value1[booked_basic_qnty],0);?></td>
				<td width="50" align="right" bgcolor="#CCCCCC"><? echo number_format((($target_basic_qnty[$buyer]-$value1[booked_basic_qnty])/$target_basic_qnty[$buyer])*100,2);?></td> 
				<td width="100" align="right"><? echo number_format($value1[ex_factory],0);?></td> 
				<td width="100" align="right">
				<? 
				echo number_format($value1[order_qty_pcs]-$value1[ex_factory],0);
				/*if($value1[order_qty_pcs]-$value1[ex_factory]>0)
				{
				$exfactory_tot_yet+=($value1[order_qty_pcs]-$value1[ex_factory]);
				}
				else
				{
				$exfactory_tot_over+=($value1[order_qty_pcs]-$value1[ex_factory]);
				}*/
				?>
				</td>
				<td width="50" align="right"><? echo number_format((($value1[order_qty_pcs]-$value1[ex_factory])/$value1[order_qty_pcs])*100,2); //echo number_format((($value1[order_qty_pcs]-$value1[ex_factory])/$quantity_tot)*100,2);?></td> 
				<td width="100" align="right"><? echo  number_format($value1[order_value],2);?></td>
				<td width="50" align="right"><? echo number_format($value1[order_value]/$value1[order_qty_pcs],2);?></td>
				<td width="50" align="right"><? echo number_format(($value1[order_value]/$po_total_price_tot)*100 ,2);  ?></td>
				<td width="50" align="right" title="<? echo "Asking Avg Rate: ".$asking_avg_rate_arr[$key]; $avg_rate=$asking_avg_rate_arr[$key];?>"><? echo number_format(($value1[order_value]/$value1[booked_basic_qnty]),2); ?></td>
				<td width="" align="right" title="<? echo "Avg Basic Rate-Asking Avg Rate";?>"><? echo number_format((($value1[order_value]/$value1[booked_basic_qnty])-$asking_avg_rate_arr[$key]),2); ?></td>
				</tr>
				<?
			}
		}
		else
		{
            if($value1[order_qty_pcs]>0 || $target_basic_qnty[$buyer] > 0)
			{
				$i++;
				if ($i%2==0)  
				$bgcolor="#E9F3FF";
				else
				$bgcolor="#FFFFFF";	
					?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
				<td width="50" align="center"><? echo $i;?></td>
				<td width="70" align="center"><? echo $company_short_name_arr[$key];?></td>
				<td width="70" align="center"><? echo $buyer_short_name_arr[$buyer]?></td>
				<td width="50" align="right"><? echo number_format($value1[smv]/$value1[order_qty_pcs],2); $total_avg_sam+=$value1[smv];?></td>
				
				<td width="100" align="right"><? echo number_format($value1[order_qty_pcs],0);?></td>
				<td width="80" align="right"><? echo count($value1[style_ref_no]);$tot_style+=count($value1[style_ref_no]);?></td>
				<td width="100" align="right" bgcolor="#CCCCCC"><? echo number_format($target_basic_qnty[$buyer],0); $target_basic_qnty_tot+=$target_basic_qnty[$buyer];?></td>
				<td width="50" align="right" bgcolor="#CCCCCC">
				<?  echo number_format(($target_basic_qnty[$buyer]/$total_target_basic_qnty)*100,2); $percent_total+=($target_basic_qnty[$buyer]/$total_target_basic_qnty)*100; ?>
				</td>
				<td width="100" align="right" bgcolor="#CCCCCC"><? echo number_format($value1[booked_basic_qnty],0);?></td>
				<td width="50" align="right" bgcolor="#CCCCCC"><? echo number_format(($value1[booked_basic_qnty]/$target_basic_qnty[$buyer])*100,2); ?></td>
				<td width="80" align="right" bgcolor="#CCCCCC"><? echo number_format($target_basic_qnty[$buyer]-$value1[booked_basic_qnty],0);?></td>
				<td width="50" align="right" bgcolor="#CCCCCC"><? echo number_format((($target_basic_qnty[$buyer]-$value1[booked_basic_qnty])/$target_basic_qnty[$buyer])*100,2);?></td> 
				<td width="100" align="right"><? echo number_format($value1[ex_factory],0);?></td> 
				<td width="100" align="right">
				<? 
				echo number_format($value1[order_qty_pcs]-$value1[ex_factory],0);
				/*if($value1[order_qty_pcs]-$value1[ex_factory]>0)
				{
				$exfactory_tot_yet+=($value1[order_qty_pcs]-$value1[ex_factory]);
				}
				else
				{
				$exfactory_tot_over+=($value1[order_qty_pcs]-$value1[ex_factory]);
				}*/
				?>
				</td>
				<td width="50" align="right"><? echo number_format((($value1[order_qty_pcs]-$value1[ex_factory])/$value1[order_qty_pcs])*100,2); //echo number_format((($value1[order_qty_pcs]-$value1[ex_factory])/$quantity_tot)*100,2);?></td> 
				<td width="100" align="right"><? echo  number_format($value1[order_value],2);?></td>
				<td width="50" align="right"><? echo number_format($value1[order_value]/$value1[order_qty_pcs],2);?></td>
				<td width="50" align="right"><? echo number_format(($value1[order_value]/$po_total_price_tot)*100 ,2);  ?></td>
				<td width="50" align="right" title="<? echo "Asking Avg Rate: ".$asking_avg_rate_arr[$key]; $avg_rate=$asking_avg_rate_arr[$key];?>"><? echo number_format(($value1[order_value]/$value1[booked_basic_qnty]),2); ?></td>
				<td width="" align="right" title="<? echo "Avg Basic Rate-Asking Avg Rate";?>"><? echo number_format((($value1[order_value]/$value1[booked_basic_qnty])-$asking_avg_rate_arr[$key]),2); ?></td>
				</tr>
				<?
			}
		}
	}
}
?>
</tbody>
<tfoot>
<tr>
<th width="50"></th>
<th width="70"></th>
<th width="70"></th>
<th width="50"><? echo number_format($total_avg_sam/$quantity_tot,2); ?></th>

<th width="100"><? echo number_format($quantity_tot,0); ?></th>
<th width="80"><? echo $tot_style;?></th>
<th width="100"><? echo number_format($target_basic_qnty_tot,0); ?></th>
<th width="50"><? echo number_format($percent_total,2); ?></th>
<th width="100"><? echo number_format($booked_basic_qnty_tot,0); ?></th>
<th width="50"><? echo number_format($booked_basic_qnty_tot/$target_basic_qnty_tot*100,2); ?></th>
<th width="80"><? echo number_format($target_basic_qnty_tot-$booked_basic_qnty_tot,0); ?></th>
<th width="50"><? echo number_format((($target_basic_qnty_tot-$booked_basic_qnty_tot)/$target_basic_qnty_tot)*100,2); ?></th>
<th width="100"><? echo number_format($exfactory_tot,0); ?></th> 
<th width="100"><? echo number_format($quantity_tot-$exfactory_tot,0); ?></th>
<th width="50"><? echo number_format((($quantity_tot-$exfactory_tot)/$quantity_tot)*100,2); ?></th> 
<th width="100"><?  echo number_format($po_total_price_tot,2); ?></th>
<th width="50"><?  echo number_format($po_total_price_tot/$quantity_tot,2); ?> </th>
<th width="50"><?  //echo number_format($total_target_basic_qnty,2); ?></th>

<th width="50" title="<? echo "Asking Avg Rate: ".$avg_rate;?>"><? echo number_format($po_total_price_tot/$booked_basic_qnty_tot,2); ?></th>
<th width="" title="<? echo "Avg Basic Rate-Asking Avg Rate";?>"><? echo number_format(($po_total_price_tot/$booked_basic_qnty_tot)-$avg_rate,2); ?></th>
</tr>
<tr>
<th width="50"></th>
<th width="70"></th>
<th width="70"></th>
<th width="50">&nbsp;</th>

<th width="100">&nbsp;</th>
<th width="80"></th>
<th width="100">&nbsp;</th>
<th width="50">&nbsp;</th>
<th width="100">&nbsp;</th>
<th width="50">&nbsp;</th>
<th width="80">&nbsp;</th>

<th width="100" colspan="2">Short Shipment</th> 
<th width="100"><? echo number_format($exfactory_tot_yet,0); ?></th>
<th width="50">&nbsp;</th> 
<th width="100">&nbsp;</th>
<th width="50">&nbsp; </th>
<th width="50">&nbsp;</th>

<th width="50" >&nbsp;</th>
<th width="" >&nbsp;</th>
</tr>
<tr>
<th width="50"></th>
<th width="70"></th>
<th width="70"></th>
<th width="50">&nbsp;</th>
<th width="100">&nbsp;</th>
<th width="80"></th>
<th width="100">&nbsp;</th>
<th width="50">&nbsp;</th>
<th width="100">&nbsp;</th>
<th width="50">&nbsp;</th>
<th width="80">&nbsp;</th>

<th width="100" colspan="2">Over Shipment</th> 
<th width="100"><? echo number_format(ltrim($exfactory_tot_over,'-'),0); ?></th>
<th width="50">&nbsp;</th> 
<th width="100">&nbsp;</th>
<th width="50">&nbsp; </th>
<th width="50">&nbsp;</th>

<th width="50" >&nbsp;</th>
<th width="" >&nbsp;</th>
</tr>


<tr>
<td colspan="19">&nbsp;</td>
</tr>
<tr>
<td width="" colspan="2" align="right"></td>

<td width="" colspan="2"  align="center"><strong>Ord. Qty. (Pcs)</strong></td>

<td width="" colspan="3" align="center"><strong> Eqv. Basic Qty.</strong></td>
<td width="" colspan="2" align="center"><strong>Ord. Value (USD)</strong></td>
<td width="" colspan="2" align="center"><strong>Ord. Avg.Rate (USD)</strong></td>
<td width="" colspan="2" align="center"><strong>Basic Avg.Rate (USD)</strong></td>



<td width="" colspan="6" align="right"> <strong>Company Avg. Rate (USD)</strong></td>

<td width="" align="right"><?  echo number_format($po_total_price_tot/$booked_basic_qnty_tot,2); ?></td>
</tr>
<tr>
<td width="" colspan="2" align="right"> <strong>Confirmed Order</strong></td>

<td width="" colspan="2"  align="right"><? echo number_format($quantity_tot_c,0); ?></td>

<td width="" colspan="3" align="right"><? echo number_format($booked_basic_qnty_tot_c,0); ?></td>
<td width="" colspan="2" align="right"><?  echo number_format($po_total_price_tot_c,2); ?></td>
<td width="" colspan="2" align="right"><?  echo number_format($po_total_price_tot_c/$quantity_tot_c,2); ?> </td>
<td width="" colspan="2" align="right"><?  echo number_format($po_total_price_tot_c/$booked_basic_qnty_tot_c,2); ?> </td>


<td width="50" colspan="6" align="right"> Company Asking Avg. Rate (USD)</td>
<td width="" align="right"><?  echo number_format($avg_rate,2); ?></td>
</tr>
<tr>
<td width="" colspan="2" align="right"><strong>Projected Order</strong></td>

<td width="" colspan="2"  align="right"> <? echo number_format($quantity_tot_p,0); ?></td>

<td width="" colspan="3" align="right"><? echo number_format($booked_basic_qnty_tot_p,0); ?></td>
<td width="" colspan="2" align="right"><?  echo number_format($po_total_price_tot_p,2); ?></td>
<td width="" colspan="2" align="right"><?  echo number_format($po_total_price_tot_p/$quantity_tot_p,2); ?> </td>
<td width="" colspan="2" align="right"><?  echo number_format($po_total_price_tot_p/$booked_basic_qnty_tot_p,2); ?> </td>

<td width="" colspan="6" align="right"> Company Stnd. Devn. (USD)</td>
<td width="" align="right"><? echo number_format(($po_total_price_tot/$booked_basic_qnty_tot)-$avg_rate,2); ?></td>
</tr>
<tr>
<td width="" colspan="2" align="right"><strong>Total</strong></td>

<td width="" colspan="2"  align="right"> <? echo number_format($quantity_tot_c+$quantity_tot_p,0); ?></td>

<td width="" colspan="3" align="right"><? echo number_format($booked_basic_qnty_tot_c+$booked_basic_qnty_tot_p,0); ?></td>
<td width="" colspan="2" align="right"><?  echo number_format($po_total_price_tot_c+$po_total_price_tot_p,2); ?></td>
<td width="" colspan="2" align="right"><?  //echo number_format($po_total_price_tot_p/$quantity_tot_p,2); ?> </td>
<td width="" colspan="2" align="right"><?  //echo number_format($po_total_price_tot_p/$booked_basic_qnty_tot_p,2); ?> </td>

<td width="" colspan="6" align="right"> </td>
<td width="" align="right"><? //echo number_format(($po_total_price_tot/$booked_basic_qnty_tot)-$avg_rate,2); ?></td>
</tr>
</tfoot>
</table>
</td>
</tr>
</table> 

<table width="100%">
<tr>
<td width="50%" id="confirmed_order">
<table width="800" cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all">
<thead>
<tr>
<th><input type="button" id="confirmed_order_print_button" value="Print" onClick="print_report_part_by_part('confirmed_order','#confirmed_order_print_button')"/></th>
<th colspan="10">Confirmed Order - <?  echo date('M,Y',strtotime($start_date));?></th>
</tr>
<tr>
<th width="50">SL</th>
<th width="70">Company Name</th>
<th width="70">Buyer Name</th>
<th width="50">Avg.SMV</th>
<th width="100">Ord. Qty. (Pcs)</th>
<th width="100">Style Ref No</th>
<th width="100">Eqv. Basic Qty. (Pcs)</th>
<th width="100">Ord. Value (USD)</th>
<th width="50">Buyer Share %</th>
<th width="50">Ord. Avg.Rate (USD)</th>
<th width="">Basic. Avg.Rate (USD)</th>
</tr>
</thead>
<tbody>
<?
$chart_cap="";
$chart_data="";
$i=0;
$total_avg_sam_confrim=0;
$tot_style=0;
$pie_chart="[";
$chart="[
		 {
		key: 'Cumulative Return',
		   values: [
			 ";
foreach ($company_buyer_array_confirm as $key=>$value)
{
	$sort = new SortMdArray;
	$sort->sort_order = 'desc';
    $sort->sortByKey($value);
	foreach($value as $buyer=> $value1)
	{
		if($zero_value==0)
		{
		if($value1[order_qty_pcs] > 0 || $value1[booked_basic_qnty] > 0 || $value1[order_value] >0)
		{
		$i++;
		if ($i%2==0)  
		$bgcolor="#E9F3FF";
		else
		$bgcolor="#FFFFFF";	
		$chart.="{
			'label': '".$buyer_short_name_arr[$buyer]."',
            'value': ".$value1[order_qty_pcs]."},
			";
			$pie_chart.="{
      'key': '".$buyer_short_name_arr[$buyer]."',
      'y': ".$value1[order_qty_pcs]."},
    ";
		?>
		<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_3nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_3nd<? echo $i; ?>">
		<td width="50" align="center"><? echo $i;?></td>
		<td width="70" align="center"><? echo $company_short_name_arr[$key];?></td>
		<td width="70" align="center"><? echo $buyer_short_name_arr[$buyer];?></td>
        <td width="50" align="right"><? echo number_format($value1[smv]/$value1[order_qty_pcs],2); $total_avg_sam_confirm+=$value1[smv];?></td>
		<td width="100" align="right"><? echo number_format($value1[order_qty_pcs],0);?></td>
		<td width="100" align="center"><? echo count($value1[style_ref_no]);$tot_style+=count($value1[style_ref_no]);?></td>
		<td width="100" align="right"><? echo number_format($value1[booked_basic_qnty],0);?></td>
		
		
		<td width="100" align="right"><? echo  number_format($value1[order_value],2);?></td>
		<td width="50" align="right"><? echo number_format(($value1[order_value]/$po_total_price_tot_c)*100 ,2);  ?></td>
        <td width="50" align="right"><? echo number_format($value1[order_value]/$value1[order_qty_pcs],2);?></td>
        <td width="" align="right"><? echo number_format($value1[order_value]/$value1[booked_basic_qnty],2);?></td>
		</tr>
		<?
		}
		}
		else
		{
		$i++;
		if ($i%2==0)  
		$bgcolor="#E9F3FF";
		else
		$bgcolor="#FFFFFF";	
		$chart.="{
			'label': '".$buyer_short_name_arr[$buyer]."',
            'value': ".$value1[order_qty_pcs]."},
			";
			$pie_chart.="{
      'key': '".$buyer_short_name_arr[$buyer]."',
      'y': ".$value1[order_qty_pcs]."},";
    
		?>
		<tr bgcolor="<? echo $bgcolor; ?>">
		<td width="50" align="center"><? echo $i;?></td>
		<td width="70" align="center"><? echo $company_short_name_arr[$key];?></td>
		<td width="70" align="center"><? echo $buyer_short_name_arr[$buyer];?></td>
        <td width="50" align="right"><? echo number_format($value1[smv]/$value1[order_qty_pcs],2); $total_avg_sam_confirm+=$value1[smv];?></td>
		<td width="100" align="right"><? echo number_format($value1[order_qty_pcs],0);?></td>
		<td width="100" align="center"><? echo count($value1[style_ref_no]);$tot_style+=count($value1[style_ref_no]);?></td>
		<td width="100" align="right"><? echo number_format($value1[booked_basic_qnty],0);?></td>
		
		
		<td width="100" align="right"><? echo  number_format($value1[order_value],2);?></td>
		<td width="50" align="right"><? echo number_format(($value1[order_value]/$po_total_price_tot_c)*100 ,2);  ?></td>
        <td width="50" align="right"><? echo number_format($value1[order_value]/$value1[order_qty_pcs],2);?></td>
        <td width="" align="right"><? echo number_format($value1[order_value]/$value1[booked_basic_qnty],2);?></td>
		</tr>
            
            <?
		}
	}
}
$chart.="]
		 }
		 ];
";
$pie_chart.="];";
//echo $pie_chart;
?>
</tbody>
<tfoot>
<th width="50"></th>
<th width="70"></th>
<th width="70"></th>
<th width="50"><? echo number_format($total_avg_sam_confirm/$quantity_tot_c,2); ?></th>
<th width="100"><? echo number_format($quantity_tot_c,0); ?></th>
<th width="100"><? echo $tot_style;?></th>
<th width="100"><? echo number_format($booked_basic_qnty_tot_c,0); ?></th>
<th width="100"><?  echo number_format($po_total_price_tot_c,2); ?></th>
<th width="50"><?  //echo number_format(($po_total_price_tot/$po_total_price_tot)*100,2); ?></th>
<th width="50"><?  echo number_format($po_total_price_tot_c/$quantity_tot_c,2); ?> </th>
<th width=""><?  echo number_format($po_total_price_tot_c/$booked_basic_qnty_tot_c,2); ?> </th>
</tfoot>
</table>
</td>
<td width="50%" valign="top">
<table width="650" cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all" style="margin-left:10px">
<thead>
<tr>
<th>Buyer wise Confirmed Order Chart</th>
</tr>
<tr>
<th></th>
</tr>
</thead>
<tbody>
<tr>
<td align="center">
<!--<iframe align="middle" src="chart/examples/pieChart.php?cardata=<? //echo $pie_chart;  ?>" width="650" height="400"></iframe>-->
</td>
</tr>
</tbody>
<tfoot>

</tfoot>

</table>
</td>
</tr>
</table>


<table width="100%">
<tr>
<td width="50%" id="projected_order">
<table width="800" cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all">
<thead>
<tr>
<th><input type="button" id="projected_order_print_button" value="Print" onClick="print_report_part_by_part('projected_order','#projected_order_print_button')"/></th>
<th colspan="10">Projected Order - <?  echo date('M,Y',strtotime($start_date));?></th>
</tr>
<tr>
<th width="50">SL</th>
<th width="70">Company Name</th>
<th width="70">Buyer Name</th>
<th width="50">Avg.SMV</th>
<th width="100">Ord. Qty. (Pcs)</th>
<th width="100">No of Style</th>
<th width="100">Eqv. Basic Qty. (Pcs)</th>

<th width="100">Ord. Value (USD)</th>
<th width="50">Buyer Share %</th>
<th width="50"> Ord. Avg.Rate (USD)</th>
<th width=""> Basic. Avg.Rate (USD)</th>
</tr>
</thead>
<tbody>
<?
$i=0;
$total_avg_sam_projected=0;
$tot_style=0;
$pie_chart1="[";

$chart1="[
		 {
		key: 'Cumulative Return',
		   values: [
			 ";
foreach ($company_buyer_array_projected as $key=>$value)
{
	$sort = new SortMdArray;
	$sort->sort_order = 'desc';
    $sort->sortByKey($value);
	foreach($value as $buyer=> $value1)
	{
		if($zero_value==0)
		{
		if($value1[order_qty_pcs] > 0 || $value1[booked_basic_qnty] > 0 || $value1[order_value] >0)
		{
		$i++;
		if ($i%2==0)  
		$bgcolor="#E9F3FF";
		else
		$bgcolor="#FFFFFF";	
		$chart1.="{
			'label': '".$buyer_short_name_arr[$buyer]."',
            'value': ".$value1[order_qty_pcs]."},
			";
			$pie_chart1.="{
      'key': '".$buyer_short_name_arr[$buyer]."',
      'y': ".$value1[order_qty_pcs]."},";
		?>
		<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_4nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_4nd<? echo $i; ?>">
		<td width="50" align="center"><? echo $i;?></td>
		<td width="70" align="center"><? echo $company_short_name_arr[$key];?></td>
		<td width="70" align="center"><? echo $buyer_short_name_arr[$buyer];?></td>
        <td width="50" align="right"><? echo number_format($value1[smv]/$value1[order_qty_pcs],2); $total_avg_sam_projected+=$value1[smv];?></td>
		<td width="100" align="right"><? echo number_format($value1[order_qty_pcs],0);?></td>
		<td width="100" align="right"><? echo count($value1[style_ref_no]);$tot_style+=count($value1[style_ref_no]);?></td>
		<td width="100" align="right"><? echo number_format($value1[booked_basic_qnty],0);?></td>
		<td width="100" align="right"><? echo number_format($value1[order_value],2);?></td>
		<td width="50" align="right"><? echo number_format(($value1[order_value]/$po_total_price_tot_p)*100 ,2);  ?></td>
        <td width="50" align="right"><? echo number_format($value1[order_value]/$value1[order_qty_pcs],2);?></td>
        <td width="" align="right"><? echo number_format($value1[order_value]/$value1[booked_basic_qnty],2);?></td>
		</tr>
		<?
		}
		}
		else
		{
			$i++;
		if ($i%2==0)  
		$bgcolor="#E9F3FF";
		else
		$bgcolor="#FFFFFF";	
		$chart1.="{
			'label': '".$buyer_short_name_arr[$buyer]."',
            'value': ".$value1[order_qty_pcs]."},
			"
		?>
		<tr bgcolor="<? echo $bgcolor; ?>">
		<td width="50" align="center"><? echo $i;?></td>
		<td width="70" align="center"><? echo $company_short_name_arr[$key];?></td>
		<td width="70" align="center"><? echo $buyer_short_name_arr[$buyer];?></td>
        <td width="50" align="right"><? echo number_format($value1[smv]/$value1[order_qty_pcs],2); $total_avg_sam_projected+=$value1[smv];?></td>
		<td width="100" align="right"><? echo number_format($value1[order_qty_pcs],0);?></td>
		<td width="100" align="right"><? echo count($value1[style_ref_no]);$tot_style+=count($value1[style_ref_no]);?></td>
		<td width="100" align="right"><? echo number_format($value1[booked_basic_qnty],0);?></td>
		
		
		<td width="100" align="right"><? echo number_format($value1[order_value],2);?></td>
		<td width="50" align="right"><? echo number_format(($value1[order_value]/$po_total_price_tot_p)*100 ,2);  ?></td>
        <td width="50" align="right"><? echo number_format($value1[order_value]/$value1[order_qty_pcs],2);?></td>
        <td width="" align="right"><? echo number_format($value1[order_value]/$value1[booked_basic_qnty],2);?></td>
		</tr>
            <?
		}
	}
}
$chart1.="]
		 }
		 ];
";
$pie_chart1.="];";

?>
</tbody>
<tfoot>
<th width="50"></th>
<th width="70"></th>
<th width="70"></th>
<th width="50"><? echo number_format($total_avg_sam_projected/$quantity_tot_p,2); ?></th>
<th width="100"><? echo number_format($quantity_tot_p,0); ?></th>
<th width="100"><? echo $tot_style ?></th>
<th width="100"><? echo number_format($booked_basic_qnty_tot_p,0); ?></th>

<th width="100"><?  echo number_format($po_total_price_tot_p,2); ?></th>
<th width="50"><?  //echo number_format(($po_total_price_tot/$po_total_price_tot)*100,2); ?></th>
<th width=""><?  echo number_format($po_total_price_tot_p/$quantity_tot_p,2); ?> </th>
<th width=""><?  echo number_format($po_total_price_tot_p/$booked_basic_qnty_tot_p,2); ?> </th>
</tfoot>

</table>
</td>
<td width="50%" valign="top">
<table width="650" cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all" style=" margin-left:10px">
<thead>
<tr>
<th>Buyer wise Projected Order Chart</th>
</tr>

</thead>
<tbody>
<tr>
<td>
<!--<iframe src="chart/examples/pieChart.php?cardata=<? //echo $pie_chart1;  ?>" width="650" height="450"></iframe>-->
</td>
</tr>
</tbody>
<tfoot>

</tfoot>

</table>
</td>
</tr>
</table>
<br/>
<table>
<tr>
<td bgcolor="orange" height="15" width="30"></td>
<td>Maximum 10 Days Remaing To Ship</td>
<td bgcolor="green" height="15" width="30">&nbsp;</td>
<td>On Time Shipment</td>
<td bgcolor="#2A9FFF" height="15" width="30"></td>
<td>Delay shipment</td>
<td bgcolor="red" height="15" width="30"></td>
<td>Shipment Date Over & Pending</td>


</tr>
</table>

    <h3 align="left" id="accordion_h4" class="accordion_h" onClick="accordion_menu( this.id,'content_report_panel', '')"> -Report Panel </h3>
    <div id="content_report_panel"> 
        <table width="4300" id="table_header_1" border="1" class="rpt_table" rules="all">
            <thead>
                <tr>
                    <th width="50">SL</th>
                    <th width="65" >Company</th>
                    <th width="60">Job No</th>
                    <th width="50">Buyer</th>
                    <th width="50">Agent</th>
                    <th width="150">Order No</th>
                    <th width="100">Pord. Dept Code</th>
                    <th width="30">Img</th>
                    <th width="30">File</th>
                    <th width="150">Item</th>
                    <th width="90">Style Ref</th>
                    <th width="150">Style Des</th>
                    <th width="100">Country</th>
                    <th width="100">Po Insert Date</th>
                    <? $category_type="";
						if($cbo_category_by==1){
							$category_type="Country Ship Date";
						}
						else{
							$category_type="Pub Ship Date";
						}
						
					?>
                    <th width="80"><? echo $category_type; ?></th>
                    <th width="30">Lead Time</th>
                    <th width="100">SMV</th>
                    <th width="100">Total SMV</th>
                    <th width="90">Order Qnty</th>
                    <th width="30">Uom</th>
                    <th width="90">Order Qnty(Pcs)</th>
                    <th width="100">Eqv. Basic Qty. (Pcs)</th>
                    <th width="50">Per Unit Price</th>
                    <th width="100">Order Value</th>
                    <th width="100">Commission</th>
                    <th width="100">Net Order Value</th>
                    <th width="90">Ex-Fac Qnty (Pcs)</th>
                    <th width="90">Ex-Fac Value</th>
                    <th width="90">Ex-factory Bal. (Pcs)</th>
                    <th width="90">Ex-factory Over (Pcs)</th>
                    <th width="120">Ex-factory Bal. Value</th>
                    <th width="120">Ex-factory Over. Value</th>
                    <th width="90">Short/ Over/At Per</th>
                    <th width="60">Order Status</th>
                    <th width="70">Prod. Catg</th>
                    <th width="80">PO Rec. Date</th>
                    <th width="50">Days in Hand</th>
                    <th width="100">Shipping Status</th>
                    <th width="150">Team Member</th>
                    <th width="150">Factory Merchant</th>
                    <th width="150">Team Name</th>
                    <th width="100">Internal Ref/ Grouping</th>
                    <th width="100">File No</th>
                    <th width="30">Id</th>
                    <th width="150">Delay Reason</th>
                    <th>Remarks</th>
                </tr>
            </thead>
        </table>
                <div style=" max-height:400px; overflow-y:scroll; width:4300px"  align="left" id="scroll_body">
                    <table width="4280" border="1" class="rpt_table" rules="all" id="table-body">
                    <?
					
					$template_id_arr=return_library_array("select po_number_id, template_id from tna_process_mst group by po_number_id, template_id","po_number_id","template_id");
					
                    $i=1;
                    $order_qnty_pcs_tot=0;
                    $order_qntytot=0;
                    $oreder_value_tot=0;
                    $total_ex_factory_qnty=0;
                    $total_short_access_qnty=0;
                    $total_short_access_value=0;
                    $yarn_req_for_po_total=0;
                    foreach ($data_array as $row)
                    {
						$template_id=$template_id_arr[$row[csf('id')]];
						 
						if ($i%2==0) $bgcolor="#E9F3FF";
						else $bgcolor="#FFFFFF";	
						
						if($row[csf('is_confirmed')]==2) $color_font="#F00";
						else $color_font="#000";
						
						if($cbo_category_by==1)
						{
							$ex_factory_date=$exfactory_data_array[$row[csf('id')]][$row[csf('country_id')]][ex_factory_date];
							$date_diff_3=datediff( "d", $ex_factory_date , $row[csf('country_ship_date')]);
							$date_diff_4=datediff( "d", $ex_factory_date , $row[csf('country_ship_date')]);
						}
						else
						{
							$ex_factory_date=$exfactory_data_array[$row[csf('id')]][ex_factory_date];
							$date_diff_3=datediff( "d", $ex_factory_date , $row[csf('pub_shipment_date')]);
							$date_diff_4=datediff( "d", $ex_factory_date , $row[csf('pub_shipment_date')]);
						}
					
						$cons=0;
						$costing_per_pcs=0;
						/*$data_array_costing_per=sql_select("select costing_per from  wo_pre_cost_mst where  job_no='".$row[csf('job_no')]."'");
						list($costing_per)=$data_array_costing_per;*/
                        $costing_per=$costing_per_arr[$row[csf('job_no')]];
						if($costing_per ==1) $costing_per_pcs=1*12;	
						else if($costing_per==2) $costing_per_pcs=1*1;	
						else if($costing_per==3) $costing_per_pcs=2*12;	
						else if($costing_per==4) $costing_per_pcs=3*12;	
						else if($costing_per==5) $costing_per_pcs=4*12;	
						
						/*$data_array_yarn_cons=sql_select("select yarn_cons_qnty from  wo_pre_cost_sum_dtls where  job_no='".$row[csf('job_no')]."'");
						$yarn_req_for_po=0;
						foreach($data_array_yarn_cons as $row_yarn_cons)
						{
							$cons=$row_yarn_cons[csf('yarn_cons_qnty')];
							$yarn_req_for_po=($row_yarn_cons[csf('yarn_cons_qnty')]/ $costing_per_pcs)*$row[csf('po_quantity')];
						}*/
						
						//--Calculation Yarn Required-------
						//--Color Determination-------------
						//==================================
						$shipment_performance=0;
						if($row[csf('shiping_status')]==1 && $row[csf('date_diff_1')]>10 )
						{
							$color="";	
							$number_of_order['yet']+=1;
							$shipment_performance=0;
						}
						if($row[csf('shiping_status')]==1 && ($row[csf('date_diff_1')]<=10 && $row[csf('date_diff_1')]>=0))
						{
							$color="orange";
							$number_of_order['yet']+=1;
							$shipment_performance=0;
						}
						if($row[csf('shiping_status')]==1 &&  $row[csf('date_diff_1')]<0)
						{
							$color="red";	
							$number_of_order['yet']+=1;
							$shipment_performance=0;
						}
						//=====================================
						if($row[csf('shiping_status')]==2 && $row[csf('date_diff_1')]>10 ) $color="";	
						if($row[csf('shiping_status')]==2 && ($row[csf('date_diff_1')]<=10 && $row[csf('date_diff_1')]>=0)) $color="orange";	
						if($row[csf('shiping_status')]==2 &&  $row[csf('date_diff_1')]<0) $color="red";	
						if($row[csf('shiping_status')]==2 &&  $row[csf('date_diff_2')]>=0)
						{
							$number_of_order['ontime']+=1;
							$shipment_performance=1;	
						}
						if($row[csf('shiping_status')]==2 &&  $row[csf('date_diff_2')]<0)
						{
							$number_of_order['after']+=1;
							$shipment_performance=2;	
						}
						//========================================
						if($row[csf('shiping_status')]==3 && $date_diff_3 >=0 ) $color="green";	
						if($row[csf('shiping_status')]==3 &&  $date_diff_3<0) $color="#2A9FFF";	
						if($row[csf('shiping_status')]==3 && $date_diff_4>=0 )
						{
							$number_of_order['ontime']+=1;
							$shipment_performance=1;
						}
						if($row[csf('shiping_status')]==3 && $date_diff_4<0)
						{
							$number_of_order['after']+=1;
							$shipment_performance=2;	
						}
						if($cbo_category_by==1)
						{
							$country_name=$country_name_arr[$row[csf('country_id')]];
							$ship_date=$row[csf('country_ship_date')];
							$country_remarks="";
							if($row[csf('details_remarks')] !="" && $row[csf('country_remarks')] !="") $country_remarks.=$row[csf('country_remarks')]." ,".$row[csf('details_remarks')];
							else if($row[csf('details_remarks')] !="" && $row[csf('country_remarks')] =="") $country_remarks.=$row[csf('details_remarks')];
							else if($row[csf('details_remarks')] =="" && $row[csf('country_remarks')] !="") $country_remarks.=$row[csf('country_remarks')];
						}
						else
						{
							$country_name="";
						 	$country_id=array_unique(explode(",",$row[csf('country_id')]));
							foreach($country_id as $c_id)
							{	
								if($country_name=="") $country_name=$country_name_arr[$c_id]; else $country_name.=",".$country_name_arr[$c_id];
							}
							$ship_date=$row[csf('pub_shipment_date')];
							
						 	$country_remarks=$row[csf('details_remarks')];
						}
						?>
                        <tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle" height="25" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" >
                            <td width="50" align="center" bgcolor="<? echo $color; ?>" style="word-wrap: break-word;word-break: break-all;"> <? echo $i; ?> </td>
                            <td width="65" align="center" style="word-wrap: break-word;word-break: break-all;"><? echo $company_short_name_arr[$row[csf('company_name')]];?></td>
                            <td width="60" align="center" style="word-wrap: break-word;word-break: break-all;"><? echo $row[csf('job_no_prefix_num')];?></td>
                            <td  width="50" align="center" style="word-wrap: break-word;word-break: break-all;"><? echo $buyer_short_name_arr[$row[csf('buyer_name')]];?></td>
                            
                            <td  width="50" align="center" style="word-wrap: break-word;word-break: break-all;"><? echo $buyer_short_name_arr[$row[csf('agent_name')]];?></td>
                           
                            <td  width="150" align="center" style="word-wrap: break-word;word-break: break-all;"><font style="color:<? echo $color_font; ?>">
                            <? if($cbo_category_by==1)
							{
								?>
								<a href='#report_details' onClick="country_order_dtls_popup('<? echo $row[csf('job_no')]; ?>','<? echo $row[csf('id')]; ?>','<? echo $template_id; ?>','<? echo $tna_process_type; ?>','<? echo $row[csf('country_id')]; ?>');"><? echo $row[csf('po_number')];  ?></a>
								<?
							}
							else
							{
								?>
								<a href='#report_details' onClick="order_dtls_popup('<? echo $row[csf('job_no')]; ?>','<? echo $row[csf('id')]; ?>','<? echo $template_id; ?>','<? echo $tna_process_type; ?>');"><? echo $row[csf('po_number')];  ?></a>
								<?
							}
							?>
                            <br /></font>
                            </td>
                             <td  width="100" align="center" style="word-wrap: break-word;word-break: break-all;">
                             <font style="color:<? echo $color_font; ?>">
                             <? echo $row[csf('product_code')];?><br />
                             <a href='#report_details' onClick="progress_comment_popup('<? echo $row[csf('job_no')]; ?>','<? echo $row[csf('id')]; ?>','<? echo $template_id; ?>','<? echo $tna_process_type; ?>');">TNA Prog. Report</a>
                             </font></td>
                            <td width="30" onClick="openmypage_image('requires/capacity_and_order_booking_status_ffl_controller.php?action=show_image&job_no=<? echo $row[csf("job_no")] ?>','Image View')" style="word-wrap: break-word;word-break: break-all;"><img  src='../../../<? echo $imge_arr[$row[csf('job_no')]]; ?>' height='25' width='30' /></td>
                            <td width="30">
                            <input type="button" class="image_uploader" id="system_id" style="width:28px" value="File" onClick="openmypage_file('show_file','<? echo $row[csf("job_no")]; ?>','2')"/>
                            </td>
                            <td width="150" align="center" style="word-wrap: break-word;word-break: break-all;">
                            <?
                            $gmts_item_name="";
                            $gmts_item_id=explode(',',$row[csf('gmts_item_id')]);
                            for($j=0; $j<count($gmts_item_id); $j++)
                            {
                            $gmts_item_name.= $garments_item[$gmts_item_id[$j]].",";
                            }
                            ?>
                            <p> <? echo rtrim($gmts_item_name,","); ?> </p>
                            </td>
                            <td width="90" align="center" style="word-wrap: break-word;word-break: break-all;"><? echo $row[csf('style_ref_no')];?></td>
                            <td width="150" align="center" style="word-wrap: break-word;word-break: break-all;"><? echo $row[csf('style_description')];?></td>
                            <td width="100" align="center" style="word-wrap: break-word;word-break: break-all;"><? echo $country_name; ?></td>
                            <td width="100" align="center" style="word-wrap: break-word;word-break: break-all;"><? echo change_date_format($row[csf('insert_date')],'dd-mm-yyyy','-');?></td>
                            <td width="80" align="center" style="word-wrap: break-word;word-break: break-all;"><? echo change_date_format($ship_date,'dd-mm-yyyy','-');?></td>
                            <td width="30" align="right">
                            <?
								echo $LeadTime=datediff('d',$row[csf('po_received_date')],$ship_date);
							?>
                            </td>
                            <td  width="100" align="right" style="word-wrap: break-word;word-break: break-all;">
                            <?   
                            //echo number_format($job_smv_arr[$row['job_no']],2); 
                            echo number_format($job_smv_arr[$row[csf('job_no')]],2); 
                            ?>
                            </td>
                            
                            <td  width="100" align="right" style="word-wrap: break-word;word-break: break-all;"><?  $smv= ($job_smv_arr[$row[csf('job_no')]])*$row[csf('po_quantity')]; $smv_tot+=$smv; echo number_format($smv,2); ?></td>
                            <td width="90" align="right" style="word-wrap: break-word;word-break: break-all;" title="Order Number : <? echo $row[csf('po_number')]; ?>">
                            <? 
                            echo number_format( $row[csf('po_quantity')],0);
                            $order_qntytot=$order_qntytot+$row[csf('po_quantity')];
                            $gorder_qntytot=$gorder_qntytot+$row[csf('po_quantity')];
                            ?>
                            </td>
                            <td width="30" align="center" style="word-wrap: break-word;word-break: break-all;"><? echo $unit_of_measurement[$row[csf('order_uom')]];?></td>
                            <td width="90" align="right" style="word-wrap: break-word;word-break: break-all;" title="Order Number : <? echo $row[csf('po_number')]; ?>">
                            <? 
                            $poQtyPcs=$row[csf('po_quantity')]*$row[csf('total_set_qnty')];
                            echo number_format(($row[csf('po_quantity')]*$row[csf('total_set_qnty')]),0);  
                            $order_qnty_pcs_tot=$order_qnty_pcs_tot+($row[csf('po_quantity')]*$row[csf('total_set_qnty')]);
                            $gorder_qnty_pcs_tot=$gorder_qnty_pcs_tot+($row[csf('po_quantity')]*$row[csf('total_set_qnty')]);
                            ?>
                            </td>
                            <td width="100" align="right" title="<? echo "Basic SMV:".$basic_smv_arr[$row[csf("company_name")]];?>" style="word-wrap: break-word;word-break: break-all;">
                            <? 
                            $basic_qnty_pcs= (($job_smv_arr[$row[csf('job_no')]])*$row[csf('po_quantity')])/$basic_smv_arr[$row[csf("company_name")]];
                            $basic_qnty_pcs_tot+=$basic_qnty_pcs;
                            echo number_format($basic_qnty_pcs,0);
                            
                            ?>
                            </td>
                            <td  width="50" align="right" style="word-wrap: break-word;word-break: break-all;"><? echo number_format($row[csf('unit_price')],2);?></td>
                            <td width="100" align="right" style="word-wrap: break-word;word-break: break-all;" title="Order Number : <? echo $row[csf('po_number')]; ?>">
                            <? 
                            echo number_format($row[csf('order_total')],2);
                            $oreder_value_tot=$oreder_value_tot+$row[csf('order_total')];
                            $goreder_value_tot=$goreder_value_tot+$row[csf('order_total')];
                            ?>
                            </td>
                            <td width="100"  align="right" style="word-wrap: break-word;word-break: break-all;">
                            <? 
                            $commission=($row[csf('po_quantity')]/$costing_per_pcs)*$commission_for_shipment_schedule_arr[$row[csf('job_no')]]; $commission_tot+=$commission; echo number_format($commission,2); 
                            ?>
                            </td>
                            <td width="100" align="right" style="word-wrap: break-word;word-break: break-all;"  title="Order Number : <? echo $row[csf('po_number')]; ?>"><? $net_order_value=$row[csf('order_total')]-$commission;$net_order_value_tot+=$net_order_value; echo number_format ($net_order_value,2); ?></td>
                            <td width="90" align="right" style="word-wrap: break-word;word-break: break-all;">
                            <? 
                            if($cbo_category_by==1)
                            {
                                $ex_factory_qnty=$exfactory_data_array[$row[csf("id")]][$row[csf("country_id")]][ex_factory_qnty]; 
                            }
                            else
                            {
                                $ex_factory_qnty=$exfactory_data_array[$row[csf("id")]][ex_factory_qnty]; 
                            }
							?>
                            <a href="##" onClick="generate_ex_factory_popup('ex_factory_popup','<? echo $row[csf('job_no')];?>', '<? echo $row[csf('id')]; ?>','750px')"><? echo  number_format( $ex_factory_qnty,0); ?></a>
                            
                            <?
                            //echo  number_format( $ex_factory_qnty,0); 
                            $total_ex_factory_qnty=$total_ex_factory_qnty+$ex_factory_qnty ;
                            $gtotal_ex_factory_qnty=$gtotal_ex_factory_qnty+$ex_factory_qnty ;;
                            if ($shipment_performance==0)
                            {
                            $po_qnty['yet']+=($row[csf('po_quantity')]*$row[csf('total_set_qnty')]);
                            $po_value['yet']+=100;
                            }
                            else if ($shipment_performance==1)
                            {
                            $po_qnty['ontime']+=$ex_factory_qnty;
                            $po_value['ontime']+=((100*$ex_factory_qnty)/($row[csf('po_quantity')]*$row[csf('total_set_qnty')]));
                            $po_qnty['yet']+=(($row[csf('po_quantity')]*$row[csf('total_set_qnty')])-$ex_factory_qnty);
                            }
                            else if ($shipment_performance==2)
                            {
                            $po_qnty['after']+=$ex_factory_qnty;
                            $po_value['after']+=((100*$ex_factory_qnty)/($row[csf('po_quantity')]*$row[csf('total_set_qnty')]));
                            $po_qnty['yet']+=(($row[csf('po_quantity')]*$row[csf('total_set_qnty')])-$ex_factory_qnty);
                            }
                            ?> 
                            </td>
                            <td width="90" align="right"><p><? echo number_format(($ex_factory_qnty/$row[csf('total_set_qnty')])*$row[csf('unit_price')],2); ?></p></td>
                            <td  width="90" align="right" style="word-wrap: break-word;word-break: break-all;">
                            <?
                            $short_over_shipment="";
                            $short_access_qnty=(($row[csf('po_quantity')]*$row[csf('total_set_qnty')])-$ex_factory_qnty); 
                            if($short_access_qnty>=0){
                            echo number_format($short_access_qnty,0);
                            $total_short_access_qnty=$total_short_access_qnty+$short_access_qnty;
                            $gtotal_short_access_qnty=$gtotal_short_access_qnty+$short_access_qnty;
                            $short_over_shipment="Short Shipment";
                            }
                            ?>
                            </td>
                            <td  width="90" align="right" style="word-wrap: break-word;word-break: break-all;">
                            <? 
                            //$short_access_qnty=(($row['po_quantity']*$row['total_set_qnty'])-$ex_factory_qnty); 
                            if($short_access_qnty<0){
                            echo number_format(ltrim($short_access_qnty,'-'),0);
                            $total_over_access_qnty=$total_over_access_qnty+$short_access_qnty;
                            //$gtotal_short_access_qnty=$gtotal_short_access_qnty+$short_access_qnty;
                            $short_over_shipment="Over Shipment";
                            }
                            ?>
                            </td>
                            <td width="120" align="right" style="word-wrap: break-word;word-break: break-all;">
                            <? 
                            if($short_access_qnty>=0){
                            $short_access_value=($short_access_qnty/$row[csf('total_set_qnty')])*$row[csf('unit_price')];
                            echo  number_format($short_access_value,2);
                            $total_short_access_value=$total_short_access_value+$short_access_value;
                            $gtotal_short_access_value=$gtotal_short_access_value+$short_access_value;
                            }
                            ?>
                            </td>
                            <td width="120" align="right" style="word-wrap: break-word;word-break: break-all;">
                            <? 
                            if($short_access_qnty<0){
                            $short_over_value=($short_access_qnty/$row[csf('total_set_qnty')])*$row[csf('unit_price')];
                            echo  number_format(ltrim($short_over_value,'-'),2);
                            $total_over_access_value=$total_over_access_value+$short_over_value;
                            //$gtotal_short_access_value=$gtotal_short_access_value+$short_access_value;
                            }
                            ?>
                            </td>
                            <? 
							    $sta="";
                                if(($poQtyPcs-$ex_factory_qnty)==0){$sta="At Per";}
                                else if($poQtyPcs<$ex_factory_qnty){$sta= "Over Shipment";}
                                else if($poQtyPcs>$ex_factory_qnty){$sta="Short Shipment";}?>
                            <td width="90"><? echo $sta;?></td>
                            <td width="60" align="center" style="word-wrap: break-word;word-break: break-all;"><? echo  $order_status[$row[csf('is_confirmed')]];?></td>
                            <td width="70" align="center"  style="word-wrap: break-word;word-break: break-all;"><? echo $product_category[$row[csf('product_category')]];?></td>
                            <td width="80" align="center" style="word-wrap: break-word;word-break: break-all;"><? echo change_date_format($row[csf('po_received_date')],'dd-mm-yyyy','-');?></td>
                            <td  width="50" align="center" bgcolor="<? echo $color; ?>" style="word-wrap: break-word;word-break: break-all;"> 
                            <?
                            if($row[csf('shiping_status')]==1 || $row[csf('shiping_status')]==2)
                            {
                            echo $row[csf('date_diff_1')];
                            }
                            if($row[csf('shiping_status')]==3)
                            {
                            echo $date_diff_3;
                            }
                            ?>
                            </td>
                            <td width="100" align="center" style="word-wrap: break-word;word-break: break-all;"><? echo $shipment_status[$row[csf('shiping_status')]]; ?></td>
                            <td width="150" align="center" style="word-wrap: break-word;word-break: break-all;"><? echo $company_team_member_name_arr[$row[csf('dealing_marchant')]];?></td>
                            <td width="150" align="center" style="word-wrap: break-word;word-break: break-all;"><? echo $company_team_member_name_arr[$row[csf('factory_marchant')]];?></td>
                            <td width="150" align="center" style="word-wrap: break-word;word-break: break-all;"><? echo $company_team_name_arr[$row[csf('team_leader')]];?></td>
                            <td width="100" style="word-wrap: break-word;word-break: break-all;"><? echo $row[csf('grouping')]; ?></td>
                            <td width="100" style="word-wrap: break-word;word-break: break-all;"><? echo $row[csf('file_no')]; ?></td>
                            <td width="30" style="word-wrap: break-word;word-break: break-all;"><? echo $row[csf('id')]; ?></td>
                            <td width="150" style="word-wrap: break-word;word-break: break-all;">
                            <? 
                            //echo $row[csf('delay_for')];
                            $delay_reason_all=array_unique(explode(",",$row[csf('delay_for')]));
                            $delay_all="";
                            foreach($delay_reason_all as $val)
                            {
                                $delay_all.=$delay_for[$val]." , ";
                            }
                            $delay_all=chop($delay_all," , ");
                            echo $delay_all;
                            ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;"><? echo $country_remarks; ?></td>
                        </tr>
                    <?
                    $i++;
					}
                    ?>
                    </table>
                </div>
                <table width="4300" id="report_table_footer" border="1" class="rpt_table" rules="all">
                    <tfoot>
                        <tr>
                            <th width="50"></th>
                            <th width="65" ></th>
                            <th width="60"></th>
                            <th  width="50"></th>
                            <th  width="50"></th>
                            <th  width="150"></th>
                            <th  width="100"></th>
                            <th width="30"></th>
                            <th width="30"></th>
                            <th width="150"></th>
                            <th width="90"></th>
                            <th width="150"></th>
                            <th width="100"></th>
                            <th width="100"></th>
                            <th width="80"></th>
                            <th width="30"></th>
                            <th  width="100" id=""></th>  
                            <th  width="100" id="value_smv_tot"><? echo number_format($smv_tot,2); ?></th>
                           <th width="90" id="total_order_qnty"><? //echo number_format($order_qntytot,0); ?></th>
                            <th width="30"></th>
                            
                            <th width="90" id="total_order_qnty_pcs"><? echo number_format($order_qnty_pcs_tot,0); ?></th>
                            <th width="100" id="value_yarn_req_tot"><? echo number_format($basic_qnty_pcs_tot,2); ?></th>
                             <th  width="50"></th>
                             <th width="100" id="value_total_order_value"><? echo number_format($oreder_value_tot,2); ?></th>
                            <th width="100" id="value_total_commission"><? echo number_format($commission_tot,2); ?></th>
                            <th width="100" id="value_total_net_order_value"><? echo number_format($net_order_value_tot,2); ?></th>
                            <th width="90" id="total_ex_factory_qnty"> <? echo number_format($total_ex_factory_qnty,0); ?></th>
                            <th width="90" id="total_ex_factory_val">xxxx</th>
                            <th  width="90" id="total_short_access_qnty"><? echo number_format($total_short_access_qnty,0); ?></th>
                             <th  width="90" id="total_over_access_qnty"><? echo number_format(ltrim($total_over_access_qnty,'-'),0); ?></th>
                            <th width="120" id="value_total_short_access_value"><? echo number_format($total_short_access_value,2); ?></th>
                            <th width="120" id="value_total_over_access_value"><? echo number_format(ltrim($total_over_access_value,'-'),2); ?></th>
                            <th width="90"></th>
                            <th width="60"></th>
                            <th width="70"></th>
                            <th width="80"></th>
                            <th  width="50"></th>
                            <th width="100" ></th>
                            <th width="150"> </th>
                            <th width="150"> </th>
                            <th width="150"></th>
                            <th width="100"></th>
                            <th width="100"></th>
                            <th width="30"></th>
                            <th width="150"></th>
                            <th></th>
                        </tr>
                       
                    </tfoot>
                </table>
                <div id="shipment_performance">
                    <fieldset>
                        <table width="600" border="1" cellpadding="0" cellspacing="1" class="rpt_table" rules="all" >
                        <thead>
                        <tr>
                        <th colspan="4"> <font size="4">Shipment Performance</font></th>
                        </tr>
                        <tr>
                        <th>Particulars</th><th>No of PO</th><th>PO Qnty</th><th> %</th>
                        </tr>
                        </thead>
                        <tr bgcolor="#E9F3FF">
                        <td>On Time Shipment</td><td><? echo $number_of_order['ontime']; ?>asif</td><td align="right"><? echo number_format($po_qnty['ontime'],0); ?></td><td align="right"><? echo number_format(((100*$po_qnty['ontime'])/$order_qnty_pcs_tot),2); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        <td> Delivery After Shipment Date</td><td><? echo $number_of_order['after']; ?></td><td align="right"><? echo number_format($po_qnty['after'],0); ?></td><td align="right"><? echo number_format(((100*$po_qnty['after'])/$order_qnty_pcs_tot),2); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        <td>Yet To Shipment </td><td><? echo $number_of_order['yet']; ?></td><td align="right"><? echo number_format($po_qnty['yet'],0); ?></td><td align="right"><? echo number_format(((100*$po_qnty['yet'])/$order_qnty_pcs_tot),2); ?></td>
                        </tr>
                        
                        <tr bgcolor="#E9F3FF">
                        <td> </td><td></td><td align="right"><? echo number_format($po_qnty['yet']+$po_qnty['ontime']+$po_qnty['after'],0); ?></td><td align="right"><? echo number_format(((100*$po_qnty['yet'])/$order_qnty_pcs_tot)+((100*$po_qnty['after'])/$order_qnty_pcs_tot)+((100*$po_qnty['ontime'])/$order_qnty_pcs_tot),2); ?></td>
                        </tr>
                        </table>
                    </fieldset>
                </div>
            </div>
     </div>
        
<?
}



if($action=="show_image")
{
	echo load_html_head_contents("Set Entry","../../../../", 1, 1, $unicode);
    extract($_REQUEST);
	//echo "select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1";
	$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1");
	
	?>
    <table>
    <tr>
    <?
    foreach ($data_array as $row)
	{ 
	?>
    <td><img src='../../../../<? echo $row[csf('image_location')]; ?>' height='250' width='300' /></td>
    <?
	}
	?>
    </tr>
    </table>
    
    <?
}

if($action=="work_progress_report_details")
{
	echo load_html_head_contents("Set Entry","../../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$mst_data_arr=sql_select("select a.id, a.company_name,a.job_no, a.buyer_name, a.style_ref_no, a.product_dept, a.product_category, a.gmts_item_id, a.set_smv, a.packing, b.po_number, b.po_received_date, b.pub_shipment_date from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and a.job_no='$job_no' and b.id='$po_id' and a.is_deleted=0");
	
	?>
<script>
	
	$(function() {
		$( "#nevbar" ).tabs({
			beforeLoad: function( event, ui ) {
				ui.jqXHR.fail(function() {
				ui.panel.html(
				"Couldn't load this tab. We'll try to fix this as soon as possible. " +
				"If this wouldn't be a demo." );
				});
			}
		});
	});
	
	function new_window(div)
	{
		//document.getElementById('approval_div').style.overflow="auto";
		//document.getElementById('approval_div').style.maxHeight="none";
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById(div).innerHTML+'</body</html>');
		d.close();
		//document.getElementById('approval_div').style.overflowY="scroll";
		//document.getElementById('approval_div').style.maxHeight="380px";
	}	
	
	
</script>
<fieldset style="width:99%;">
    <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
    <tr>
    	<td width="90"><strong>Company Name</strong></td>
        <td width="120"><?php echo $company_arr[$mst_data_arr[0][csf('company_name')]]; ?></td>
    	<td width="90"><strong>Buyer Name</strong></td>
        <td width="150"><?php echo $buyer_full_name_arr[$mst_data_arr[0][csf('buyer_name')]]; ?></td>
        <td width="60"><strong>Job No</strong></td>
        <td width="120"><?php echo $job_no; ?></td>
    	<td width="90"><strong>Style Ref</strong></td>
        <td width="90"><?php echo $mst_data_arr[0][csf('style_ref_no')]; ?></td>
    	<td width="90"><strong>Lead Time</strong></td>
        <td><?php echo datediff('d',$mst_data_arr[0][csf('po_received_date')],$mst_data_arr[0][csf('pub_shipment_date')]); ?></td>
    </tr>
    <tr>
    	<td><strong>Order No</strong></td>
        <td><?php echo $mst_data_arr[0][csf('po_number')]; ?></td>
    	<td><strong>Garments Item</strong></td>
        <td><?php echo $garments_item[$mst_data_arr[0][csf('gmts_item_id')]]; ?></td>
    	<td><strong>SMV</strong></td>
        <td><?php echo $mst_data_arr[0][csf('set_smv')]; ?></td>
    	<td><strong>Product Dept.</strong></td>
        <td><?php echo $pord_dept[$mst_data_arr[0][csf('product_dept')]]; ?></td>
    	<td><strong>Packing Info</strong></td>
        <td><?php echo $packing[$mst_data_arr[0][csf('packing')]]; ?></td>
    </tr>
    </table>
</fieldset>   
 <style>
	#nevbar li {border:1px solid #000;} 
	#nevbar li a { padding: 0.5em .5em;color:#000;font-weight:bold;} 
</style>   

<fieldset style="width:99%;">
<div id="nevbar">
    <ul>
        <li><a href="#order_details">Order Details</a></li>
        <li><a href="capacity_and_order_booking_status_ffl_controller.php?action=gmts_image&job_no=<? echo $job_no;?>">Gmts.Image</a></li>
        <li><a href="capacity_and_order_booking_status_ffl_controller.php?action=size_color_dtls&job_no=<? echo $job_no;?>&po_id=<? echo $po_id;?>">Size and Color Details</a></li>
        <li><a href="capacity_and_order_booking_status_ffl_controller.php?action=cost_info&txt_job_no=<? echo $job_no;?>&po_id=<? echo $po_id;?>&cbo_company_name=<? echo $mst_data_arr[0][csf('company_name')];?>">Cost Info</a></li>
        <li><a href="capacity_and_order_booking_status_ffl_controller.php?action=fab_info&txt_job_no=<? echo $job_no;?>&po_id=<? echo $po_id;?>&cbo_company_name=<? echo $mst_data_arr[0][csf('company_name')];?>">Fabric Info</a></li>
        
        <li><a href="capacity_and_order_booking_status_ffl_controller.php?action=sample_approval&job_no=<? echo $job_no;?>&po_id=<? echo $po_id;?>">Sample Approval</a></li>
        <li><a href="capacity_and_order_booking_status_ffl_controller.php?action=labdip_approval&job_no=<? echo $job_no;?>&po_id=<? echo $po_id;?>">Labdip Approval</a></li>
        <li><a href="capacity_and_order_booking_status_ffl_controller.php?action=trims_approval&job_no=<? echo $job_no;?>&po_id=<? echo $po_id;?>">Trims App</a></li>
        <li><a href="group_capacity_and_order_booking_status_ffl_controller.php?action=order_wise_production&job_no=<? echo $job_no;?>&po_number=<? echo $mst_data_arr[0][csf('po_number')];?>&cbo_company_name=<? echo $mst_data_arr[0][csf('company_name')];?>">Production Info</a></li>
        
    </ul>
<div id="order_details">
<?
$count_lib_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');

$order_data_arr=sql_select("SELECT 
a.gmts_item_id,a.dealing_marchant,
b.job_no_mst, b.po_number,(b.po_quantity*a.total_set_qnty) as po_quantity, b.po_received_date, b.unit_price, b.pub_shipment_date, b.factory_received_date,b.po_total_price as order_value
FROM wo_po_details_master a, wo_po_break_down b
WHERE b.id=$po_id and b.job_no_mst='$job_no' and a.job_no=b.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1
");

$pre_cost_data_arr=sql_select("select machine_line,prod_line_hr from wo_pre_cost_mst where job_no='$job_no' and is_deleted=0 and status_active=1");

$pre_cost_dtls_data_arr=sql_select("select a.fabric_description,a.body_part_id,a.gsm_weight_yarn,a.avg_cons_yarn, a.avg_cons, a.fabric_source from wo_pre_cost_fabric_cost_dtls a where a.job_no='$job_no' order by a.id");
foreach($pre_cost_dtls_data_arr as $rows)
{
$fb_des[]=$rows[csf('fabric_description')];
if($rows[csf('fabric_source')]==1)$fb_con+=$rows[csf('avg_cons_yarn')];
$yarn_req[]=$rows[csf('avg_cons')];	
if($rows[csf('body_part_id')]==1)$gsm_top.=$rows[csf('gsm_weight_yarn')].',';
if($rows[csf('body_part_id')]==20)$gsm_bottom.=$rows[csf('gsm_weight_yarn')].',';
}


$pre_cost_dtls_data_arr=sql_select("select b.count_id from  wo_pre_cost_fab_yarn_cost_dtls b where b.job_no='$job_no' order by b.id");
foreach($pre_cost_dtls_data_arr as $rows)
{
$yarn_coun[]=$count_lib_arr[$rows[csf('count_id')]];	
}


$yarn_coun=array_unique($yarn_coun);
$yarn_coun=implode(',',$yarn_coun);
$fb_des=implode(',',$fb_des);
$yarn_req=implode(',',$yarn_req);
?>
<fieldset>	
<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
    <tr bgcolor="#FFFFFF">
        <td width="150">&nbsp; Order Qnty</td>
        <td width="200">&nbsp; <?php echo $order_data_arr[0][csf('po_quantity')];?></td>
        <td width="150">&nbsp; Fabric Description</td>
        <td>&nbsp; <?php echo $fb_des;//$order_data_arr[0][csf('gmts_item_id')];?></td>
     </tr>
     <tr bgcolor="#E9F3FF">  
        <td>&nbsp; Unit Price</td><td>&nbsp; <?php echo $order_data_arr[0][csf('unit_price')];?></td>
        <td>&nbsp; Fabric Consumption</td><td>&nbsp; <?php echo $fb_con;?></td>
     </tr>
     <tr bgcolor="#FFFFFF">  
        <td>&nbsp; Order Value</td><td>&nbsp; <?php echo $order_data_arr[0][csf('order_value')];?></td>
        <td>&nbsp; Yarn Required</td><td>&nbsp; <?php  echo $yarn_req;?></td>
     </tr>
     <tr bgcolor="#E9F3FF">  
        <td>&nbsp; Pub-shipment Date</td><td>&nbsp; <?php echo change_date_format($order_data_arr[0][csf('pub_shipment_date')]);?></td>
        <td>&nbsp; Count</td><td>&nbsp; <?php echo $yarn_coun;?></td>
     </tr>
     <tr bgcolor="#FFFFFF">  
        <td>&nbsp; P.O Received Date</td><td>&nbsp; <?php echo change_date_format($order_data_arr[0][csf('po_received_date')]);?></td>
        <td>&nbsp; GSM</td><td>&nbsp; <?php echo 'Top: '.$gsm_top.' Bottom: '.$gsm_bottom;?></td>
     </tr>
     <tr bgcolor="#E9F3FF">  
        <td>&nbsp; Factory Recv.Date</td><td>&nbsp; <?php echo change_date_format($order_data_arr[0][csf('factory_received_date')]);?></td>
        <td>&nbsp; Machine/Line</td><td>&nbsp; <?php echo $pre_cost_data_arr[0][csf('machine_line')];?></td>
     </tr>
     <tr bgcolor="#FFFFFF">  
        <td>&nbsp; Dealing Merchant</td><td>&nbsp; <?php echo $company_team_member_name_arr[$order_data_arr[0][csf('dealing_marchant')]];?></td>
        <td>&nbsp; Prod/Line/Hrs</td><td>&nbsp; <?php echo $pre_cost_data_arr[0][csf('prod_line_hr')];?></td>
    </tr>
</table>
</fieldset>
</div>
</div>
</fieldset>

    <?
	exit;
}



if($action=="gmts_image")
{
    extract($_REQUEST);
	$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1");
	?>
<fieldset>	
   <div style="max-height:300px; overflow-y:scroll;">
    <?
    foreach ($data_array as $row)
	{ 
	?>
    <img src='../../../../<? echo $row[csf('image_location')]; ?>' style="width:310px; margin:5px; float:left;" />
    <?
	}
	?>
    </div>
</fieldset>
    <?
exit;	
}

if($action=="size_color_dtls")
{
	$size_lib_arr=return_library_array( "select id,size_name from lib_size",'id','size_name');
	$color_lib_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');
	
	$color_size_arr=sql_select("SELECT order_quantity, color_number_id, size_number_id, excess_cut_perc, plan_cut_qnty FROM wo_po_color_size_breakdown WHERE job_no_mst='$job_no' and po_break_down_id='$po_id' and status_active=1 and is_deleted=0");
	
	foreach($color_size_arr as $rows)
	{
	$size_arr[$rows[csf('size_number_id')]]=$rows[csf('size_number_id')];	
	$data_arr[$rows[csf('color_number_id')]][$rows[csf('size_number_id')]]=$rows[csf('order_quantity')];	
	}
	
	
?>
<fieldset>
<button style="float:left; cursor:pointer; border:1px solid #999; border-radius:3px;" onClick="new_window('print_report_container_size_color_dtls');">Print</button>
<div id="print_report_container_size_color_dtls">	
<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
<thead>
    <th width="35">SL</th>
    <th width="100">Color/Size</th>
    <? foreach($size_arr as $val):?>
    <th width="100"><? echo $size_lib_arr[$val];?></th>
    <? endforeach; ?>
    <th>Color Total</th>
</thead>

<tbody>
<? $i=1;
	foreach($data_arr as $color=>$size):
	$bgcolor=$i%2==0?'#E9F3FF':'#FFFFFF';
	?>
    <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" style="cursor:pointer;">
        <td align="center"><? echo $i;?></td>
        <td><? echo $color_lib_arr[$color];?></td>
      <? foreach($size_arr as $size_id){ 
	  ${'tot_qty'.$color}+=$data_arr[$color][$size_id];
	  ${'grand_qty'.$size_id}+=$data_arr[$color][$size_id];
	  
	   ?> 
        <td align="right"><? echo $data_arr[$color][$size_id];?></td>
        <? } ?>
        <td align="right"><? echo ${'tot_qty'.$color};?></td>
    </tr>
<? $i++;
endforeach;?>

</tbody>
<tfoot>
    <th width="35"></th>
    <th width="100">Size/Total</th>
    <? foreach($size_arr as $size):
		$grand_total+=${'grand_qty'.$size};
		?>
    <th width="100"><? echo ${'grand_qty'.$size};?></th>
    <? endforeach; ?>
    <th><? echo $grand_total; ?></th>
</tfoot>
</table>
</div>
</fieldset>
<?
exit;	
}
if($action=="size_color_dtls_country")
{
	$size_lib_arr=return_library_array( "select id,size_name from lib_size",'id','size_name');
	$color_lib_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');
	
	$color_size_arr=sql_select("SELECT  color_number_id, size_number_id, order_quantity, excess_cut_perc, plan_cut_qnty FROM wo_po_color_size_breakdown WHERE job_no_mst='$job_no' and po_break_down_id='$po_id' and country_id=$country_id and status_active=1 and is_deleted=0");
	
	foreach($color_size_arr as $rows)
	{
	$size_arr[$rows[csf('size_number_id')]]=$rows[csf('size_number_id')];	
	$data_arr_order[$rows[csf('color_number_id')]][$rows[csf('size_number_id')]]=$rows[csf('order_quantity')];	
	$data_arr_plan[$rows[csf('color_number_id')]][$rows[csf('size_number_id')]]=$rows[csf('plan_cut_qnty')];
	$data_arr_excess[$rows[csf('color_number_id')]][$rows[csf('size_number_id')]]=$rows[csf('excess_cut_perc')];	
	}
	
	
?>
<fieldset>	
<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
<thead>
    <th width="35">SL</th>
    <th width="100">Color/Size</th>
    <th width="100"></th>
    <? foreach($size_arr as $val):?>
    <th width="70"><? echo $size_lib_arr[$val];?></th>
    <? endforeach; ?>
    <th>Color Total</th>
</thead>

<tbody>
<? $i=1;
	foreach($data_arr_order as $color=>$size):
	$bgcolor=$i%2==0?'#E9F3FF':'#FFFFFF';
	?>
    <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" style="cursor:pointer;">
        <td align="center"><? echo $i;?></td>
        <td><? echo $color_lib_arr[$color];?></td>
        <td>
        Order Qnty<br>
        Plan Cut Qnty<br>
        Ex. Cut Percent
        </td>
      <? foreach($size_arr as $size_id){ 
	  ${'tot_qty'.$color}+=$data_arr_order[$color][$size_id];
	  ${'grand_qty'.$size_id}+=$data_arr_order[$color][$size_id];
	  
	   ?> 
        <td align="right">
		<? 
		echo $data_arr_order[$color][$size_id]."<br>";
		echo $data_arr_plan[$color][$size_id]."<br>";
		echo $data_arr_excess[$color][$size_id];
		?>
        </td>
        <? } ?>
        <td align="right"><? echo ${'tot_qty'.$color};?></td>
    </tr>
<? $i++;
endforeach;?>

</tbody>
<tfoot>
    <th width="35"></th>
    <th width="100">&nbsp;</th>
    <th width="100">Size/Total</th>
    <? foreach($size_arr as $size):
		$grand_total+=${'grand_qty'.$size};
		?>
    <th width="100"><? echo ${'grand_qty'.$size};?></th>
    <? endforeach; ?>
    <th><? echo $grand_total; ?></th>
</tfoot>
</table>
</fieldset>
<?
exit;	
}


//start cost info-----------------------------------------------------
if($action=="cost_info")
{
?>
<fieldset>	

<?	
	
	
	//echo $po_id;
	$zero_value=1;
	if($txt_job_no=="") $job_no=''; else $job_no=" and a.job_no='".$txt_job_no."'";
	
	$txt_costing_date=change_date_format(str_replace("'","",$txt_costing_date),'yyyy-mm-dd','-');
	if($cbo_company_name=="") $company_name=''; else $company_name=" and a.company_name=".$cbo_company_name."";
	if($cbo_buyer_name=="") $cbo_buyer_name=''; else $cbo_buyer_name=" and a.buyer_name=".$cbo_buyer_name."";
	if($txt_style_ref=="") $txt_style_ref=''; else $txt_style_ref=" and a.style_ref_no=".$txt_style_ref."";
	if($txt_costing_date=="") $txt_costing_date=''; else $txt_costing_date=" and b.costing_date='".$txt_costing_date."'";
 	
	
	//array for display name
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	
	 $exchange_rate_result=sql_select("select b.exchange_rate from wo_po_details_master a,wo_price_quotation b where a.job_no='$txt_job_no' and a.quotation_id=b.id and b.status_active=1 and 	b.is_deleted=0"); 
	$exchange_rate=$exchange_rate_result[0][csf('exchange_rate')];
	 
	
	$gsm_weight_top=return_field_value("gsm_weight", "wo_pre_cost_fabric_cost_dtls", "job_no='$txt_job_no' and body_part_id=1");
	$gsm_weight_bottom=return_field_value("gsm_weight", "wo_pre_cost_fabric_cost_dtls", "job_no='$txt_job_no' and body_part_id=20");
	 $po_qty=0;
	 $po_plun_cut_qty=0;
	 $total_set_qnty=0;
	 $sql_po="select a.job_no,a.total_set_qnty,b.id,c.item_number_id,c.country_id,c.color_number_id,c.size_number_id,c.order_quantity ,c.plan_cut_qnty  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst   and b.id=c.po_break_down_id  and a.job_no ='".$txt_job_no."'   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 order by b.id";
	$sql_po_data=sql_select($sql_po);
	foreach($sql_po_data as $sql_po_row)
	{
	$po_qty+=$sql_po_row[csf('order_quantity')];
	$po_plun_cut_qty+=$sql_po_row[csf('plan_cut_qnty')];
	$total_set_qnty=$sql_po_row[csf('total_set_qnty')];
	}
	
	//echo $po_qty;
	
	$gmtsitem_ratio_array=array();
	$gmtsitem_ratio_sql=sql_select("select job_no,gmts_item_id,set_item_ratio from wo_po_details_mas_set_details where job_no ='".$txt_job_no."'");// where job_no ='FAL-14-01157'
	foreach($gmtsitem_ratio_sql as $gmtsitem_ratio_sql_row)
	{
	$gmtsitem_ratio_array[$gmtsitem_ratio_sql_row[csf('job_no')]][$gmtsitem_ratio_sql_row[csf('gmts_item_id')]]=$gmtsitem_ratio_sql_row[csf('set_item_ratio')];	
	}
	$costing_per_arr=return_library_array( "select job_no,costing_per from wo_pre_cost_mst where job_no ='".$txt_job_no."'", "job_no", "costing_per");// where job_no ='FAL-14-01157'
	$financial_para=array();
	$sql_std_para=sql_select("select interest_expense,income_tax,cost_per_minute from lib_standard_cm_entry where company_id=$cbo_company_name and status_active=1 and	is_deleted=0 order by id");	
	foreach($sql_std_para as $sql_std_row)
	{
		$financial_para[interest_expense]=$sql_std_row[csf('interest_expense')];
		$financial_para[income_tax]=$sql_std_row[csf('income_tax')];
		$financial_para[cost_per_minute]=$sql_std_row[csf('cost_per_minute')];
	} 
	$fab_knit_req_kg_avg=0;
	$fab_woven_req_yds_avg=0;
	if($db_type==0)
	{
	$sql = "select a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.gmts_item_id,a.order_uom,a.job_quantity,a.avg_unit_price,b.costing_per,b.sew_smv,b.cut_smv,b.sew_effi_percent, 	b.cut_effi_percent,c.fab_knit_req_kg,c.fab_knit_fin_req_kg,c.fab_woven_req_yds,c.fab_woven_fin_req_yds,c.fab_yarn_req_kg from wo_po_details_master a, wo_pre_cost_mst b,wo_pre_cost_sum_dtls c where a.job_no=b.job_no and b.job_no=c.job_no and a.status_active=1 $job_no $company_name $cbo_buyer_name $txt_style_ref $txt_costing_date order by a.job_no";  
	}
	if($db_type==2)
	{
    $sql = "select a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.gmts_item_id,a.order_uom,a.job_quantity,a.avg_unit_price,b.costing_per,b.sew_smv,b.cut_smv,b.sew_effi_percent, 	b.cut_effi_percent,c.fab_knit_req_kg,c.fab_knit_fin_req_kg,c.fab_woven_req_yds,c.fab_woven_fin_req_yds,c.fab_yarn_req_kg from wo_po_details_master a, wo_pre_cost_mst b,wo_pre_cost_sum_dtls c where a.job_no=b.job_no and b.job_no=c.job_no and a.status_active=1 $job_no $company_name $cbo_buyer_name $txt_style_ref order by a.job_no";  
	}
	$data_array=sql_select($sql);
	
	
	?>
     <button style="float:left; cursor:pointer; border:1px solid #999; border-radius:3px;" onClick="new_window('print_report_container');">Print</button> 
    <div id="print_report_container">
    <table style="width:850px">
    	<tr><th align="center"><h3><? echo $comp[str_replace("'","",$cbo_company_name)]; ?></h3></th></tr>
    	<tr><th align="center">Pre- Costing</th></tr>
    </table>
   
	<?
	$uom="";
	$sew_smv=0;
	$cut_smv=0;
	$sew_effi_percent=0;
	$cut_effi_percent=0;
	foreach ($data_array as $row)
	{	
		$order_price_per_dzn=0;
		$order_job_qnty=0;
		$avg_unit_price=0;
		$sew_smv=$row[csf("sew_smv")];
	    $cut_smv=$row[csf("cut_smv")];
	    $sew_effi_percent=$row[csf("sew_effi_percent")];
	    $cut_effi_percent=$row[csf("cut_effi_percent")];
		
		$order_values = $row[csf("job_quantity")]*$row[csf("avg_unit_price")];
		$result =sql_select("select po_number,pub_shipment_date from wo_po_break_down where job_no_mst='$txt_job_no' and status_active=1 and is_deleted=0 order by pub_shipment_date DESC");
		$job_in_orders = '';$pulich_ship_date='';
		foreach ($result as $val)
		{
			$job_in_orders .= $val[csf('po_number')].", ";
			$pulich_ship_date = $val[csf('pub_shipment_date')];
		}
		$job_in_orders = substr(trim($job_in_orders),0,-1);
		
		?>
            	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px" rules="all">
                	<tr>
                    	<td width="80">Job Number</td>
                        <td width="80"><b><? echo $row[csf("job_no")]; ?></b></td>
                        <td width="90">Buyer</td>
                        <td width="100"><b><? echo $buyer_arr[$row[csf("buyer_name")]]; ?></b></td>
                        <td width="80">Garments Item</td>
                        <? 
							$grmnt_items = "";
							if($garments_item[$row[csf("gmts_item_id")]]=="")
							{
								
								$grmts_sql = sql_select("select job_no,gmts_item_id,set_item_ratio from wo_po_details_mas_set_details where job_no='$txt_job_no'");
								foreach($grmts_sql as $key=>$val){
									$grmnt_items .=$garments_item[$val[csf("gmts_item_id")]].", ";
								}
								$grmnt_items = substr_replace($grmnt_items,"",-1,1);
							}else{
								$grmnt_items = $garments_item[$row[csf("gmts_item_id")]];
							}
							
						?>
                        <td width="100"><b><? echo $grmnt_items; ?></b></td>
                    </tr>
                    <tr>
                    	<td>Style Ref. No </td>
                        <td colspan=""><b><? echo $row[csf("style_ref_no")]; ?></b></td>
                        
                        <td>Job Qnty</td>
                        <td><b><? $uom=$row[csf("order_uom")]; echo $row[csf("job_quantity")]." ". $unit_of_measurement[$row[csf("order_uom")]]; ?></b></td>
                         <td>Plan Cut Qty</td>
                       <td><b><? echo $po_plun_cut_qty/$total_set_qnty." ". $unit_of_measurement[$row[csf("order_uom")]];?></b></td>
                    </tr>
                    <tr>
                    	<td>Order Numbers</td>
                        <td colspan="5"><? echo $job_in_orders; ?></td>
                    </tr>
                    <tr>
                    	<td>Knit Fabric Cons</td>
                        <td><b><? echo $row[csf("fab_knit_req_kg")];$fab_knit_req_kg_avg+=$row[csf("fab_knit_req_kg")]; ?> (Kg)</b></td>
	
                        <td>Woven Fabric Cons</td>
                        <td><b><? echo $row[csf("fab_woven_req_yds")];$fab_woven_req_yds_avg+= $row[csf("fab_woven_req_yds")];?> (Yds)</b></td>
                        <td>Price Per Unit</td>
                        <td><b><? echo $row[csf("avg_unit_price")]; ?> USD</b></td>
                    </tr>
                    <tr>
                    	<td>Avg Yarn Req</td>
                        <td><b><? echo $row[csf("fab_yarn_req_kg")] ?> (Kg)</b></td>
                        <td>Costing Per</td>
                        <td><b><? echo $costing_per[$row[csf("costing_per")]];?></b></td>
                        <td>Shipment Date </td>
                        <td><b><? echo change_date_format($pulich_ship_date); ?></b></td>
                    </tr>
                    <tr>
                    <td>Knit Fin Fabric Cons</td>
                        <td><b><? echo $row[csf("fab_knit_fin_req_kg")] ?> (Kg)</b></td>
                        <td>Woven Fin Fabric Cons</td>
                        <td><b><? echo $row[csf("fab_woven_fin_req_yds")]; ?>(Yds)</b></td>
                    	<td>GSM</td>
                        <td><b><? echo $gsm_weight_top.",".$gsm_weight_bottom ?></b></td>
                       
                    </tr>
                    <tr>
                    <td>SMV</td>
                        <td colspan="5"><b>
                        <?
							echo return_field_value("set_smv", "wo_po_details_master","job_no='".$row[csf("job_no")]."' and status_active =1 and is_deleted=0");

						?>
                        </b></td>
                    </tr>
                </table>
               
            <?	
			
			if($row[csf("costing_per")]==1){$order_price_per_dzn=12;$costing_for=" DZN";}
			else if($row[csf("costing_per")]==2){$order_price_per_dzn=1;$costing_for=" PCS";}
			else if($row[csf("costing_per")]==3){$order_price_per_dzn=24;$costing_for=" 2 DZN";}
			else if($row[csf("costing_per")]==4){$order_price_per_dzn=36;$costing_for=" 3 DZN";}
			else if($row[csf("costing_per")]==5){$order_price_per_dzn=48;$costing_for=" 4 DZN";}
			$order_job_qnty=$row[csf("job_quantity")];
			$avg_unit_price=$row[csf("avg_unit_price")];
			
	}//end first foearch
	
	
	//id, fab_yarn_req_kg,fab_woven_req_yds,fab_knit_req_kg,fab_amount,yarn_cons_qnty,yarn_amount,conv_req_qnty,conv_charge_unit,conv_amount,trim_cons,trim_rate,trim_amount,emb_amount,comar_rate,comar_amount,commis_rate,commis_amount	 	
	// 	costing_per_id 	order_uom_id 	fabric_cost 	fabric_cost_percent 	trims_cost 	trims_cost_percent 	embel_cost 	
	//embel_cost_percent 	comm_cost 	comm_cost_percent 	commission 	commission_percent 	lab_test 	lab_test_percent 	inspection 	
	//inspection_percent 	cm_cost,cm_cost_percent 	freight,freight_percent,common_oh,common_oh_percent,total_cost ,total_cost_percent
	// 	price_dzn,price_dzn_percent,margin_dzn,margin_dzn_percent,price_pcs_or_set,price_pcs_or_set_percent,margin_pcs_set,margin_pcs_set_percent,cm_for_sipment_sche	
	//margin_pcs_set,margin_pcs_set_percent,cm_for_sipment_sche
	
	
	//start	all summary report here -------------------------------------------
	
 	?>
    
    
        
         
 
       
      
      <?
	
	 $sql_new = "select fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,currier_percent,certificate_pre_cost,certificate_percent,common_oh,common_oh_percent,depr_amor_pre_cost,total_cost ,total_cost_percent,price_dzn,price_dzn_percent,margin_dzn,margin_dzn_percent,price_pcs_or_set,price_pcs_or_set_percent,margin_pcs_set,margin_pcs_set_percent,cm_for_sipment_sche
			from wo_pre_cost_dtls
			where job_no='".$txt_job_no."' and status_active=1 and is_deleted=0";
			
			$data_array_new=sql_select($sql_new);
			$summary_data=array();
            foreach( $data_array_new as $row_new )
            {
				$summary_data[price_dzn]=$row_new[csf("price_dzn")];
				$summary_data[price_dzn_job]=($po_qty/($total_set_qnty*$order_price_per_dzn))*$row_new[csf("price_dzn")];
				
			    $summary_data[commission]=$row_new[csf("commission")];
				$summary_data[commission_job]=($po_qty/($total_set_qnty*$order_price_per_dzn))*$row_new[csf("commission")];
				
				
				$summary_data[trims_cost]=$row_new[csf("trims_cost")];
				
				$summary_data[emb_cost]=$row_new[csf("embel_cost")];
				$summary_data[emb_cost_job]=($po_qty/($total_set_qnty*$order_price_per_dzn))*$row_new[csf("embel_cost")];
				
				$summary_data[lab_test]=$row_new[csf("lab_test")];
				$summary_data[lab_test_job]=($po_qty/($total_set_qnty*$order_price_per_dzn))*$row_new[csf("lab_test")];
				
				$summary_data[inspection]=$row_new[csf("inspection")];
				$summary_data[inspection_job]=($po_qty/($total_set_qnty*$order_price_per_dzn))*$row_new[csf("inspection")];
				
				$summary_data[freight]=$row_new[csf("freight")];
				$summary_data[freight_job]=($po_qty/($total_set_qnty*$order_price_per_dzn))*$row_new[csf("freight")];
				
				$summary_data[currier_pre_cost]=$row_new[csf("currier_pre_cost")];
				$summary_data[currier_pre_cost_job]=($po_qty/($total_set_qnty*$order_price_per_dzn))*$row_new[csf("currier_pre_cost")];

				$summary_data[certificate_pre_cost]=$row_new[csf("certificate_pre_cost")];
				$summary_data[certificate_pre_cost_job]=($po_qty/($total_set_qnty*$order_price_per_dzn))*$row_new[csf("certificate_pre_cost")];
				$summary_data[wash_cost]=$row_new[csf("wash_cost")];
				$summary_data[wash_cost_job]=($po_qty/($total_set_qnty*$order_price_per_dzn))*$row_new[csf("wash_cost")];
				
				$summary_data[OtherDirectExpenses]=$row_new[csf("lab_test")]+$row_new[csf("inspection")]+$row_new[csf("freight")]+$row_new[csf("currier_pre_cost")]+$row_new[csf("certificate_pre_cost")]+$row_new[csf("wash_cost")];
				
				$summary_data[OtherDirectExpenses_job]=($po_qty/($total_set_qnty*$order_price_per_dzn))*$summary_data[OtherDirectExpenses];
				 
				$summary_data[cm_cost]=$row_new[csf("cm_cost")];
				$summary_data[cm_cost_job]=($po_qty/($total_set_qnty*$order_price_per_dzn))*$row_new[csf("cm_cost")];
				
				$summary_data[comm_cost]=$row_new[csf("comm_cost")];
				$summary_data[comm_cost_job]=($po_qty/($total_set_qnty*$order_price_per_dzn))*$row_new[csf("comm_cost")];
				
				$summary_data[common_oh]=$row_new[csf("common_oh")];
				$summary_data[common_oh_job]=($po_qty/($total_set_qnty*$order_price_per_dzn))*$row_new[csf("common_oh")];
				$summary_data[depr_amor_pre_cost]=$row_new[csf("depr_amor_pre_cost")];
				$summary_data[depr_amor_pre_cost_job]=($po_qty/($total_set_qnty*$order_price_per_dzn))*$row_new[csf("depr_amor_pre_cost")];
				
				
				
				//$summary_data[total_cost]=$row_new[csf("total_cost")];
				//$summary_data[trims_cost_percent]=$row_new[csf("trims_cost_percent")];
				//$summary_data[embel_cost_percent]=$row_new[csf("embel_cost_percent")];
				//$summary_data[embel_cost_percent]=$row_new[csf("embel_cost_percent")];
			}






$yarn_data=array();
$sql_yarn="select a.job_no,b.id,c.item_number_id,c.country_id,c.color_number_id,c.size_number_id,c.order_quantity ,c.plan_cut_qnty ,d.id as pre_cost_dtls_id,d.fab_nature_id,e.cons,f.id as yarn_id,f.cons_qnty,f.avg_cons_qnty,f.rate,f.amount   from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_yarn_cost_dtls f where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and a.job_no=f.job_no and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and  c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_cost_dtls_id   and e.cons !=0  and  a.job_no ='".$txt_job_no."' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 order by b.id,pre_cost_dtls_id";
$data_arr_yarn=sql_select($sql_yarn);
foreach($data_arr_yarn as $yarn_row)
{
    $costing_per_qty=0;
	$costing_per=$costing_per_arr[$yarn_row[csf('job_no')]];
	if($costing_per==1)
	{
	$costing_per_qty=12	;
	}
	if($costing_per==2)
	{
	$costing_per_qty=1;	
	}
	if($costing_per==3)
	{
	$costing_per_qty=24	;
	}
	if($costing_per==4)
	{
	$costing_per_qty=36	;
	}
	if($costing_per==5)
	{
	$costing_per_qty=48	;
	}

	$set_item_ratio=$gmtsitem_ratio_array[$yarn_row[csf('job_no')]][$yarn_row[csf('item_number_id')]];
	$reqyarnqnty =def_number_format(($yarn_row[csf("plan_cut_qnty")]/($costing_per_qty*$set_item_ratio))*$yarn_row[csf("cons_qnty")],5,"");
	
	$yarnamount=def_number_format(($reqyarnqnty*$yarn_row[csf("RATE")]),5,"");

	//$yarnamount =def_number_format(($yarn_row[csf("plan_cut_qnty")]/($costing_per_qty*$set_item_ratio))*$yarn_row[csf("amount")],5,"");

	
	$summary_data[yarn_cost][$yarn_row[csf("yarn_id")]]=$yarn_row[csf("amount")];
	$summary_data[yarn_cost_job]+=$yarnamount;
	//$summary_data[yarn_job_qty]+=$yarn_row[csf("plan_cut_qnty")];
	  //$yarn_data[$yarn_row[csf('job_no')]][$yarn_row[csf('id')]][$yarn_row[csf('item_number_id')]][$yarn_row[csf('country_id')]][$yarn_row[csf('color_number_id')]][$yarn_row[csf('size_number_id')]]['req_yarn_qnty']+=$reqyarnqnty;
	//$yarn_data[$yarn_row[csf('job_no')]][$yarn_row[csf('id')]][$yarn_row[csf('item_number_id')]][$yarn_row[csf('country_id')]][$yarn_row[csf('color_number_id')]][$yarn_row[csf('size_number_id')]]['yarn_amount']+=$yarnamount;

}


// Conversion 
$conv_data=array();
 $sql_conv="select a.job_no,b.id,c.item_number_id,c.country_id,c.color_number_id,c.size_number_id,c.order_quantity ,c.plan_cut_qnty ,d.id as pre_cost_dtls_id,d.fab_nature_id,e.cons,f.id as con_id,f.cons_process,f.req_qnty,f.avg_req_qnty,f.charge_unit,f.amount,f.color_break_down   from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_conv_cost_dtls f where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and a.job_no=f.job_no and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_description and e.pre_cost_fabric_cost_dtls_id=f.fabric_description  and e.cons !=0 and  a.job_no ='".$txt_job_no."' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1
UNION ALL
select a.job_no,b.id,c.item_number_id,c.country_id,c.color_number_id,c.size_number_id,c.order_quantity ,c.plan_cut_qnty ,d.id as pre_cost_dtls_id,d.fab_nature_id,e.cons,f.id as con_id,f.cons_process,f.req_qnty,f.avg_req_qnty,f.charge_unit,f.amount,f.color_break_down   from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_conv_cost_dtls f where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and a.job_no=f.job_no and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and f.fabric_description=0  and e.cons !=0  and  a.job_no ='".$txt_job_no."' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 ";
$data_arr_conv=sql_select($sql_conv);
foreach($data_arr_conv as $conv_row)
{
    $costing_per_qty=0;
	$costing_per=$costing_per_arr[$conv_row[csf('job_no')]];
	if($costing_per==1)
	{
	$costing_per_qty=12	;
	}
	if($costing_per==2)
	{
	$costing_per_qty=1;	
	}
	if($costing_per==3)
	{
	$costing_per_qty=24	;
	}
	if($costing_per==4)
	{
	$costing_per_qty=36	;
	}
	if($costing_per==5)
	{
	$costing_per_qty=48	;
	}
	
	$set_item_ratio=$gmtsitem_ratio_array[$conv_row[csf('job_no')]][$conv_row[csf('item_number_id')]];
	$convcolorrate=array();
	if($conv_row[csf('color_break_down')] !="")
	{
		$arr_1=explode("__",$conv_row[csf('color_break_down')]);
		for($ci=0;$ci<count($arr_1);$ci++)
		{
		$arr_2=explode("_",$arr_1[$ci]);
		$convcolorrate[$arr_2[0]]=$arr_2[1];
			
		}
	}
	//print_r($convcolorrate);
	//echo "<br/>";
	$convrate=0;
	$convqnty =def_number_format(($conv_row[csf("plan_cut_qnty")]/($costing_per_qty*$set_item_ratio))*$conv_row[csf("req_qnty")],5,"");

	/*if($conv_row[csf('color_break_down')] !="")
	{
	$convrate=$convcolorrate[$conv_row[csf('color_number_id')]];
	}
	else
	{
	$convrate=$conv_row[csf('charge_unit')];
	}*/
	$convrate=$conv_row[csf('charge_unit')];
	$convamount=def_number_format($convqnty*$convrate,5,"");

	//$convamount =def_number_format(($conv_row[csf("plan_cut_qnty")]/($costing_per_qty*$set_item_ratio))*$conv_row[csf("amount")],5,"");
	
	//$conv_data[$conv_row[csf('job_no')]][$conv_row[csf('id')]][$conv_row[csf('item_number_id')]][$conv_row[csf('country_id')]][$conv_row[csf('color_number_id')]][$conv_row[csf('size_number_id')]]['conv_qnty']+=$convqnty;
	$conv_data[cons_process][$conv_row[csf('con_id')]]=$conv_row[csf('cons_process')];
	$conv_data[amount][$conv_row[csf('con_id')]]=$conv_row[csf('amount')];
	$conv_data[amount_job][$conv_row[csf('con_id')]]+=$convamount;
	$summary_data[conver_cost_job]+=$convamount;

}

//die;
//Conversion End

//start	Trims Cost part report here -------------------------------------------
   	$sql_trim = "select id, job_no, trim_group,description,brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,status_active
			from wo_pre_cost_trim_cost_dtls  
			where job_no='".$txt_job_no."'";
	$data_array_trim=sql_select($sql_trim);
	$total_trims_cost=0; 
            foreach( $data_array_trim as $row_trim )
            { 
			   $order_qty_tr=0;
			   $dtls_data=sql_select("select po_break_down_id,cons,country_id from wo_pre_cost_trim_co_cons_dtls where wo_pre_cost_trim_cost_dtls_id=".$row_trim[csf("id")]." and cons !=0");
			   foreach($dtls_data as $dtls_data_row )
			   {
				   if($dtls_data_row[csf('country_id')]==0)
					 {
						 $txt_country_cond="";
					 }
					 else
					 {
						 $txt_country_cond ="and c.country_id in (".$dtls_data_row[csf('country_id')].")";
					 }
					 
					 $sql_po_qty=sql_select("select sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id=".$dtls_data_row[csf('po_break_down_id')]."  $txt_country_cond  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty");
	                 list($sql_po_qty_row)=$sql_po_qty;
	                 $po_qty=$sql_po_qty_row[csf('order_quantity_set')];
					 $order_qty_tr+=$po_qty;
			   }
				//$row[csf("cons_dzn_gmts")] = $row[csf("cons_dzn_gmts")]/$order_price_per_dzn*$order_qty_tr;
				$trim_amount = $row_trim[csf("amount")]/$order_price_per_dzn*$order_qty_tr;
				
 				//$trim_group=return_library_array( "select item_name,id from  lib_item_group where id=".$row[csf("trim_group")], "id", "item_name" ); 
				$total_trims_cost += $trim_amount;
				$summary_data[trims_cost_job]+=$trim_amount;
			}

//End	Trims Cost part report here -------------------------------------------


//-----------------------reza

$NetFOBValue=$summary_data[price_dzn]-$summary_data[commission];
$interest_expense=$NetFOBValue*$financial_para[interest_expense]/100;
$income_tax=$NetFOBValue*$financial_para[income_tax]/100;
foreach($conv_data[cons_process] as $key => $value){ 
$tot_conv_cost+=$conv_data[amount][$key];
}
$tot_amount_usd= array_sum($summary_data[yarn_cost])+$tot_conv_cost+$summary_data[trims_cost]+$summary_data[emb_cost]+$summary_data[lab_test]+$summary_data[inspection]+$summary_data[freight]+$summary_data[currier_pre_cost]+$summary_data[certificate_pre_cost]+$summary_data[wash_cost]+$summary_data[cm_cost]+$summary_data[comm_cost]+$summary_data[common_oh]+$summary_data[depr_amor_pre_cost]+$interest_expense+$income_tax+$summary_data[commission];
//----------------------


	  ?>
       <div style="margin-top:15px">
         <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
          <tr style="font-weight:bold">
                            
                            <td width="380" colspan="5">Order Porfitability</td>
                                               
                        </tr>
                        <tr style="font-weight:bold">
                            <td width="80">Line Items</td>
                            <td width="380">Particulars</td>
                            <td width="100">Amount (USD)/<? echo $costing_for; ?></td>
                            <td width="100">Total Value</td>
                            <td width="100">%</td>                     
                        </tr>
                        <!--<tr>
                            <td width="80">1</td>
                            <td width="380" align="left" style="font-weight:bold">Gross FOB Value</td>
                            <td width="100" align="right" style="font-weight:bold"><? //echo number_format($summary_data[price_dzn],4); ?></td>
                            <td width="100" align="right" style="font-weight:bold"><? //echo number_format($summary_data[price_dzn_job],4); ?></td>
                            <td width="180" align="right" style="font-weight:bold"><? //echo number_format(($summary_data[price_dzn_job]/$tot_amount_usd)*100,4);?></td>                     
                        </tr>-->
                        <!--<tr>
                            <td width="80">2</td>
                            <td width="380" align="left" style=" padding-left:15px">Less: commission</td>
                            <td width="100" align="right"><? //echo number_format($summary_data[commission],4); ?></td>
                            <td width="100" align="right"><? //echo number_format($summary_data[commission_job],4); ?></td>
                            <td width="180" align="right"><? //echo number_format(($summary_data[commission_job]/$tot_amount_usd)*100,4);?></td>                     
                        </tr>-->
                        <!--<tr>
                           <td width="80">3</td>
                            <?
							$NetFOBValue=$summary_data[price_dzn]-$summary_data[commission];
							$NetFOBValue_job=$summary_data[price_dzn_job]-$summary_data[commission_job];
							?>
                            <td width="380" align="left" style="font-weight:bold"><b>Net FOB Value (1-2)</b></td>
                            <td width="100" align="right" style="font-weight:bold"><? //echo number_format($NetFOBValue,4); ?></td>
                            <td width="100" align="right" style="font-weight:bold"><? //echo number_format($NetFOBValue_job,4); ?></td>
                            <td width="180" align="right" style="font-weight:bold"><? //echo number_format(($NetFOBValue_job/$tot_amount_usd)*100,4);?></td>                     
                        </tr>-->
                        <!--<tr>
                            <td width="80">4</td>
                            <td width="380" align="left" style="font-weight:bold"><b>Less: Cost of Material & Services (5+6+7+8+9) </b></td>
                            <?
							$Less_Cost_Material_Services=array_sum($summary_data[yarn_cost])+array_sum($conv_data[amount])+$summary_data[trims_cost]+$summary_data[emb_cost]+$summary_data[lab_test]+$summary_data[inspection]+$summary_data[freight]+$summary_data[currier_pre_cost]+$summary_data[certificate_pre_cost]+$summary_data[wash_cost];
							
							$Less_Cost_Material_Services_job=$summary_data[yarn_cost_job]+$summary_data[conver_cost_job]+$summary_data[trims_cost_job]+$summary_data[emb_cost_job]+$summary_data[OtherDirectExpenses_job];
							//+$summary_data[lab_test]+$summary_data[inspection]+$summary_data[freight]+$summary_data[currier_pre_cost]+$summary_data[certificate_pre_cost]+$summary_data[wash_cost];
							?>
                            <td width="100" align="right" style="font-weight:bold"> <? //echo number_format($Less_Cost_Material_Services,4); ?></td>
                            <td width="100" align="right" style="font-weight:bold"><? //echo number_format($Less_Cost_Material_Services_job,4); ?></td>
                            <td width="180" align="right" style="font-weight:bold"><? //echo number_format(($Less_Cost_Material_Services_job/$tot_amount_usd)*100,4);?></td>                     
                        </tr>-->
                       <tr>
                            <td width="80">1</td>
                            <td width="380" align="left" style=" padding-left:100px;">Yarn Cost</td>
                            <td width="100" align="right" > <? echo number_format(array_sum($summary_data[yarn_cost]),4); ?></td>
                            <td width="100" align="right"> <? $a= number_format($summary_data[yarn_cost_job],4); echo $a;$tot_val+=str_replace(',','',$a);?></td>
                            <td width="180" align="right"><? $a= number_format((array_sum($summary_data[yarn_cost])/$tot_amount_usd)*100,4); echo $a; $tot_per+=$a?></td>                     
                        </tr>
                        <tr>
                            <td width="80" valign="top">2</td>
                            <td width="380" align="left" style=" padding-left:100px" valign="bottom">

                                <table width="100%">
                                <tr><td width="180">Conversion Cost</td></tr>
                                </table>
                            
                                <table border="1" class="rpt_table" rules="all">
                                <? foreach($conv_data[cons_process] as $key => $value){ ?>
                                <tr>
                                <td width="180" align="left"><? echo $conversion_cost_head_array[$conv_data[cons_process][$key]]; ?></td>
                               
                                </tr>
                                <? }?>
                                </table>
                            </td>
                            
                            <td width="100" align="right" valign="bottom">
							<? //echo number_format(array_sum($conv_data[amount]),4); ?>
                            
                             <table>
                            <tr>
                            <td width="100" align="right" style="font-weight:bold"><? //echo number_format(array_sum($conv_data[amount]),4); ?></td>
                            </tr>
                            </table>
                            
                            <table border="1" class="rpt_table" rules="all" valign="bottom">
                            <? foreach($conv_data[cons_process] as $key => $value){ ?>
                            <tr>
                            
                            <td width="100" align="right"><? echo number_format($conv_data[amount][$key],4);?></td>
                            </tr>
                            <? }?>
                            </table>
                            </td>
                            <td width="100" align="right" valign="bottom">
							
                            <table width="100%">
                            <tr>
                            <td width="100" align="right" style="font-weight:bold"><? //echo number_format($summary_data[conver_cost_job],4); ?></td>
                            </tr>
                            </table>
                            
                            <table border="1" class="rpt_table" rules="all">
                            <? foreach($conv_data[cons_process] as $key => $value){ ?>
                            <tr>
                            
                            <td width="100" align="right"><? echo number_format($conv_data[amount_job][$key],4);$tot_val+=number_format($conv_data[amount_job][$key],4)?></td>
                            </tr>
                            <? }?>
                            </table>
                            </td>
                            <td width="180" align="right" valign="bottom">
							
                            <table width="100%">
                            <tr>
                            <td width="100" align="right" style="font-weight:bold"><? //echo number_format(($summary_data[conver_cost_job]/$summary_data[price_dzn_job])*100,4);?></td>
                            </tr>
                            </table>
                            
                            <table border="1" class="rpt_table" rules="all">
                            <? foreach($conv_data[cons_process] as $key => $value){ ?>
                            <tr>
                            
                            <td width="180" align="right"><? $a= number_format(($conv_data[amount][$key]/$tot_amount_usd)*100,4); echo $a;?></td>
                            </tr>
                            <? }?>
                            </table>
                            </td>                     
                        </tr>
                        
                        <tr>
                            <td width="80">3</td>
                            <td width="380" align="left" style=" padding-left:100px;" >Trim Cost</td>
                            <td width="100" align="right"> <? echo number_format($summary_data[trims_cost],4); ?></td>
                            <td width="100" align="right"><? $a= number_format($summary_data[trims_cost_job],4); echo $a; $tot_val+=str_replace(',','',$a); ?></td>
                            <td width="180" align="right"><? $a= number_format(($summary_data[trims_cost]/$tot_amount_usd)*100,4);  echo $a;?></td>                     
                        </tr>
                        <tr>
                            <td width="80">4</td>
                            <td width="380" align="left" style=" padding-left:100px;">Embelishment Cost</td>
                            <td width="100" align="right"> <? echo number_format($summary_data[emb_cost],4); ?></td>
                            <td width="100" align="right"><? $a= number_format($summary_data[emb_cost_job],4); echo $a; $tot_val+=str_replace(',','',$a);  ?></td>
                            <td width="180" align="right"><? $a= number_format(($summary_data[emb_cost]/$tot_amount_usd)*100,4); echo $a;?></td>                     
                        </tr>
                        <tr>
                         <?
						 //$OtherDirectExpenses=$summary_data[lab_test]+$summary_data[inspection]+$summary_data[freight]+$summary_data[currier_pre_cost]+$summary_data[certificate_pre_cost]+$summary_data[wash_cost];
						  
							//$OtherDirectExpenses_job_qty=($po_qty/($total_set_qnty*$order_price_per_dzn))*$OtherDirectExpenses;
							
						 ?>
                            <td width="80" valign="top">5</td>
                            <td width="380" align="left" style=" padding-left:100px" valign="bottom">
                            
                            <table width="100%">
                            <tr>
                            <td>Other Direct Expenses</td>
                           
                            </tr>
                            </table>
                            
                
                            <table border="1" class="rpt_table" rules="all" width="100%">
                            
                            <tr>
                            <td width="180" align="left">Lab Test</td>
                            
                            </tr>
                            
                            <tr>
                            <td width="180" align="left">Inspection</td>
                           
                            </tr>
                            
                            <tr>
                            <td width="180" align="left">Freight Cost</td>
                            
                            </tr>
                            
                            <tr>
                            <td width="180" align="left">Courier Cost</td>
                            
                            </tr>
                            
                             <tr>
                            <td width="180" align="left">Certificate Cost</td>
                           
                            </tr>
                            
                            <tr>
                            <td width="180" align="left">Garments Wash Cost</td>
                           
                            </tr>
                            
                            </table>
                            </td>
                            <td width="100" align="right" valign="bottom">
                            <table>
                            <tr>
                            <td width="100" align="right" ><? //echo number_format($summary_data[OtherDirectExpenses],4); ?></td>
                            </tr>
                            </table>
                            <table border="1" class="rpt_table" rules="all">
                            
                            <tr>
                            
                            <td width="100" align="right"><? echo number_format($summary_data[lab_test],4);?></td>
                            </tr>
                            
                            <tr>
                            
                            <td width="100" align="right"><? echo number_format($summary_data[inspection],4);?></td>
                            </tr>
                            
                            <tr>
                            
                            <td width="100" align="right"><? echo number_format($summary_data[freight],4);?></td>
                            </tr>
                            
                            <tr>
                           
                            <td width="100" align="right"><? echo number_format($summary_data[currier_pre_cost],4);?></td>
                            </tr>
                            
                             <tr>
                           
                            <td width="100" align="right"><? echo number_format($summary_data[certificate_pre_cost],4);?></td>
                            </tr>
                            
                            <tr>
                           
                            <td width="100" align="right"><? echo number_format($summary_data[wash_cost],4);?></td>
                            </tr>
                            
                            </table>
                            </td>
                            
                            
                            <td width="100" align="right" valign="bottom">
							<? //echo number_format($summary_data[OtherDirectExpenses_job],4); ?>
                            <table>
                            <tr>
                            <td width="100" align="right"><? //echo number_format($summary_data[OtherDirectExpenses_job],4); ?></td>
                            </tr>
                            </table>
                            <table border="1" class="rpt_table" rules="all">
                            
                            <tr>
                            
                            <td width="100" align="right"><? $a= number_format($summary_data[lab_test_job],4); echo $a; $tot_val+=str_replace(',','',$a); ?></td>
                            </tr>
                            
                            <tr>
                            
                            <td width="100" align="right"><? $a= number_format($summary_data[inspection_job],4);echo $a; $tot_val+=str_replace(',','',$a); ?></td>
                            </tr>
                            
                            <tr>
                            
                            <td width="100" align="right"><? $a= number_format($summary_data[freight_job],4);echo $a; $tot_val+=str_replace(',','',$a); ?></td>
                            </tr>
                            
                            <tr>
                           
                            <td width="100" align="right"><? $a= number_format($summary_data[currier_pre_cost_job],4);echo $a; $tot_val+=str_replace(',','',$a); ?></td>
                            </tr>
                            
                             <tr>
                           
                            <td width="100" align="right"><? $a= number_format($summary_data[certificate_pre_cost_job],4);echo $a; $tot_val+=str_replace(',','',$a); ?></td>
                            </tr>
                            
                            <tr>
                           
                            <td width="100" align="right"><? $a= number_format($summary_data[wash_cost_job],4);echo $a; $tot_val+=str_replace(',','',$a); ?></td>
                            </tr>
                            
                            </table>
                            </td>
                            <td width="180" align="right" valign="bottom">
							
                            <table>
                            <tr>
                            <td width="180" align="right"><? //echo number_format(($summary_data[OtherDirectExpenses_job]/$tot_amount_usd)*100,4);?></td>
                            </tr>
                            </table>
                            <table border="1" class="rpt_table" rules="all">
                            
                            <tr>
                            
                            <td width="180" align="right"><? $a= number_format(($summary_data[lab_test]/$tot_amount_usd)*100,4);echo $a;?></td>
                            </tr>
                            
                            <tr>
                            
                            <td width="180" align="right"><? $a= number_format(($summary_data[inspection]/$tot_amount_usd)*100,4);echo $a;?></td>
                            </tr>
                            
                            <tr>
                            
                            <td width="180" align="right"><? $a= number_format(($summary_data[freight]/$tot_amount_usd)*100,4);echo $a;?></td>
                            </tr>
                            
                            <tr>
                           
                            <td width="180" align="right"><? $a= number_format(($summary_data[currier_pre_cost]/$tot_amount_usd)*100,4);echo $a;?></td>
                            </tr>
                            
                             <tr>
                           
                            <td width="180" align="right"><? $a= number_format(($summary_data[certificate_pre_cost]/$tot_amount_usd)*100,4);echo $a;?></td>
                            </tr>
                            
                            <tr>
                           
                            <td width="180" align="right"><? $a= number_format(($summary_data[wash_cost]/$tot_amount_usd)*100,4);echo $a;?></td>
                            </tr>
                            
                            </table>
                            </td>                     
                        </tr>
                         <!--<tr>
                            <td width="80">10</td>
                            <td width="380" align="left" style="font-weight:bold">Contributions/Value Additions (3-4)</td>
                            <?
							$Contribution_Margin=$NetFOBValue-$Less_Cost_Material_Services;
							$Contribution_Margin_job=$NetFOBValue_job-$Less_Cost_Material_Services_job;
							?>
                            <td width="100" align="right" style="font-weight:bold"> <? //echo number_format($Contribution_Margin,4); ?></td>
                            <td width="100" align="right" style="font-weight:bold"><? //echo number_format($Contribution_Margin_job,4); ?></td>
                            <td width="180" align="right" style="font-weight:bold"><? //echo number_format(($Contribution_Margin_job/$tot_amount_usd)*100,4);?></td>                     
                        </tr>-->
                        <tr>
                            <td width="80">6</td>
                            <td width="380" align="left" style=" padding-left:15px">CM Cost </td>
                            <td width="100" align="right"><? echo number_format($summary_data[cm_cost],4); ?> </td>
                            <td width="100" align="right"><? $a= number_format($summary_data[cm_cost_job],4);echo $a; $tot_val+=str_replace(',','',$a);  ?></td>
                            <td width="180" align="right"><? $a= number_format(($summary_data[cm_cost]/$tot_amount_usd)*100,4);echo $a;?></td>                     
                        </tr>
                        <!--<tr>
                            <td width="80">12</td>
                            <td width="380" align="left" style="font-weight:bold">Gross Profit (10-11)</td>
                            <?
							$Gross_Profit=$Contribution_Margin-$summary_data[cm_cost];
							$Gross_Profit_job=$Contribution_Margin_job-$summary_data[cm_cost_job];
							?>
                            <td width="100" align="right" style="font-weight:bold"> <? //echo number_format($Gross_Profit,4); ?></td>
                            <td width="100" align="right" style="font-weight:bold"><? //echo number_format($Gross_Profit_job,4); ?></td>
                            <td width="180" align="right" style="font-weight:bold"><? //echo number_format(($Gross_Profit_job/$tot_amount_usd)*100,4);?></td>                     
                        </tr>-->
                        
                        <tr>
                            <td width="80">7</td>
                            <td width="380" align="left" style=" padding-left:15px">Commercial Cost</td>
                            
                            <td width="100" align="right"> <? echo number_format( $summary_data[comm_cost],4); ?></td>
                            <td width="100" align="right"><? $a= number_format( $summary_data[comm_cost_job],4);echo $a; $tot_val+=str_replace(',','',$a);  ?></td>
                            <td width="180" align="right"><? $a= number_format(($summary_data[comm_cost]/$tot_amount_usd)*100,4);echo $a;?></td>                     
                        </tr>
                        <tr>
                            <td width="80">8</td>
                            <td width="380" align="left" style=" padding-left:15px">Operating Expensees</td>
                            
                            <td width="100" align="right"><? echo number_format( $summary_data[common_oh],4); ?> </td>
                            <td width="100" align="right"><? $a= number_format( $summary_data[common_oh_job],4);echo $a; $tot_val+=str_replace(',','',$a);  ?> </td>
                            <td width="180" align="right"><? $a= number_format(($summary_data[common_oh]/$tot_amount_usd)*100,4);echo $a;?></td>                     
                        </tr>
                        
                        <!--<tr >
                            <td width="80">15</td>
                            <td width="380" align="left" style="font-weight:bold">Operating Profit/ Loss (12-(13+14))</td>
                            <?
							$OperatingProfitLoss=$Gross_Profit-($summary_data[comm_cost]+$summary_data[common_oh]);
							$OperatingProfitLoss_job=$Gross_Profit_job-($summary_data[comm_cost_job]+$summary_data[common_oh_job]);
							?>
                            <td width="100" align="right" style="font-weight:bold"> <? //echo number_format($OperatingProfitLoss,4); ?></td>
                            <td width="100" align="right" style="font-weight:bold"><? //echo number_format($OperatingProfitLoss_job,4); ?></td>
                            <td width="180" align="right" style="font-weight:bold"><? //echo number_format(($OperatingProfitLoss_job/$tot_amount_usd)*100,4);?></td>                     
                        </tr>-->
                         <tr>
                            <td width="80">9</td>
                            <td width="380" align="left" style=" padding-left:15px">Depreciation & Amortization </td>
                            
                            <td width="100" align="right"> <? echo number_format( $summary_data[depr_amor_pre_cost],4); ?></td>
                            <td width="100" align="right"><? $a= number_format( $summary_data[depr_amor_pre_cost_job],4);echo $a; $tot_val+=str_replace(',','',$a);  ?></td>
                            <td width="180" align="right"><? $a= number_format(($summary_data[depr_amor_pre_cost]/$tot_amount_usd)*100,4);echo $a;?></td>                     
                        </tr>
                        
                        <tr>
                        <?
						$interest_expense=$NetFOBValue*$financial_para[interest_expense]/100;
						$income_tax=$NetFOBValue*$financial_para[income_tax]/100;
						$interest_expense_job=$NetFOBValue_job*$financial_para[interest_expense]/100;
						$income_tax_job=$NetFOBValue_job*$financial_para[income_tax]/100;
						?>
                            <td width="80">10</td>
                            <td width="380" align="left" style=" padding-left:15px">Interest </td>
                            
                            <td width="100" align="right"> <? echo number_format( $interest_expense,4); ?></td>
                            <td width="100" align="right"><? $a= number_format( $interest_expense_job,4);echo $a; $tot_val+=str_replace(',','',$a);  ?></td>
                            <td width="180" align="right"><? $a= number_format(($interest_expense/$tot_amount_usd)*100,4);echo $a;?></td>                     
                        </tr>
                         <tr>
                            <td width="80">11</td>
                            <td width="380" align="left" style=" padding-left:15px">Income Tax</td>
                            
                            <td width="100" align="right"> <? echo number_format( $income_tax,4); ?></td>
                            <td width="100" align="right"><? $a= number_format( $income_tax_job,4);echo $a; $tot_val+=str_replace(',','',$a);  ?></td>
                            <td width="180" align="right"><? $a= number_format(($income_tax/$tot_amount_usd)*100,4);echo $a;?></td>                     
                        </tr>
                        <!--<tr>
                            <? 
							$Netprofit=$OperatingProfitLoss-($summary_data[depr_amor_pre_cost]+$interest_expense+$income_tax);
							$Netprofit_job=$OperatingProfitLoss_job-($summary_data[depr_amor_pre_cost_job]+$interest_expense_job+$income_tax_job);
							?>
                            <td width="80">12</td>
                            <td width="380" align="left" style="font-weight:bold">Net Profit (15-(16+17+18))</td>
                            
                            <td width="100" align="right" style="font-weight:bold"><? //echo number_format( $Netprofit,4); ?> </td>
                            <td width="100" align="right" style="font-weight:bold"><? //echo number_format( $Netprofit_job,4); ?></td>
                            <td width="180" align="right" style="font-weight:bold"><? //echo number_format(($Netprofit_job/$tot_amount_usd)*100,4);?></td>                     
                        </tr>-->
                        
                        
                        <tr>
                            <td width="80">12</td>
                            <td width="380" align="left" style=" padding-left:15px">Commission</td>
                            <td width="100" align="right"><? echo number_format($summary_data[commission],4); ?></td>
                            <td width="100" align="right"><? $a= number_format($summary_data[commission_job],4);echo $a; $tot_val+=str_replace(',','',$a);  ?></td>
                            <td width="180" align="right"><? $a= number_format(($summary_data[commission]/$tot_amount_usd)*100,4);echo $a;?></td>                     
                        </tr>
                        
                        <tr>
                            <td width="380" align="center" colspan="2"><b>Total Cost:</b></td>
                            <td width="100" align="right"><? echo $tot_amount_usd; ?></td>
                            <td width="100" align="right"><? echo $tot_val; ?></td>
                            <td width="180" align="right"><? echo ($tot_amount_usd/$tot_amount_usd)*100; ?></td>                     
                        </tr>
                        
                        
                        </table>
         </div>
      <?
	//End all summary report here -------------------------------------------
	
	
	
	//2	All Fabric Cost part here------------------------------------------- 	   	
	$sql = "select id, job_no, body_part_id, fab_nature_id, color_type_id, fabric_description, avg_cons,avg_cons_yarn, fabric_source,gsm_weight, rate, amount,avg_finish_cons,status_active   
			from wo_pre_cost_fabric_cost_dtls 
			where job_no='".$txt_job_no."'";
			
			
	$data_array=sql_select($sql);
	$knit_fab="";$woven_fab="";
 		 
		$knit_subtotal_avg_cons=0;
		$knit_subtotal_amount=0;
		$woven_subtotal_avg_cons=0;
		$woven_subtotal_amount=0;
		$grand_total_amount=0;
        $i=2;$j=2;
		foreach( $data_array as $row )
        {
            if($row[csf("fab_nature_id")]==2)//knit fabrics
			{
				$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("fabric_description")].", ".$row[csf("gsm_weight")];
				$i++;	
                $knit_fab .= '<tr>
                    <td align="left">'.$item_descrition.'</td>
                    <td align="left">'.$fabric_source[$row[csf("fabric_source")]].'</td>
                    <td align="right">'.number_format($row[csf("avg_cons")],4).'</td>
					 <td align="right">'.number_format($row[csf("avg_cons_yarn")],4).'</td>
                    <td align="right">'.number_format($row[csf("rate")],4).'</td>
                    <td align="right">'.number_format($row[csf("avg_cons_yarn")]*$row[csf("rate")],4).'</td>  
                </tr>';		
            
				$knit_subtotal_avg_cons += $row[csf("avg_cons")];
				$knit_subtotal_avg_cons_yarn += $row[csf("avg_cons_yarn")];
				$knit_subtotal_amount += $row[csf("avg_cons_yarn")]*$row[csf("rate")]; 
			}
			
			if($row[csf("fab_nature_id")]==3)//woven fabrics
			{
				$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("fabric_description")].", ".$row[csf("gsm_weight")];
				$j++;
                $woven_fab .= '<tr>
                    <td align="left">'.$item_descrition.'</td>
                    <td align="left">'.$fabric_source[$row[csf("fabric_source")]].'</td>
                    <td align="right">'.number_format($row[csf("avg_cons")],4).'</td>
					<td align="right">'.number_format($row[csf("avg_cons_yarn")],4).'</td>
                    <td align="right">'.number_format($row[csf("rate")],4).'</td>
                    <td align="right">'.number_format($row[csf("avg_cons_yarn")]*$row[csf("rate")],4).'</td>  
                </tr>';		
             
				$woven_subtotal_avg_cons += $row[csf("avg_cons")];
				$woven_subtotal_avg_cons_yarn += $row[csf("avg_cons_yarn")];
				$woven_subtotal_amount += $row[csf("avg_cons_yarn")]*$row[csf("rate")]; 
			}
        }	
	
		$knit_fab= '<div style="margin-top:15px">
				<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
					<label><b>All Fabric Cost  </b></label>
						<tr style="font-weight:bold"  align="center">
							<td width="80" rowspan="'.$i.'" ><div><b>Knit Fabric</b></div></td>
							<td width="350">Description</td>
							<td width="100">Source</td>
							<td width="100">Fab. Cons/Dzn</td>
							<td width="100">Avg. Fab. Cons/Dzn</td>
							<td width="100">Rate (USD)</td>
							<td width="100">Amount (USD)</td>
						</tr>'.$knit_fab;
		$woven_fab= '<tr><td colspan="7">&nbsp;</td></tr><tr>
						<td width="80" rowspan="'.$j.'"><div><b>Woven Fabric</b></div></td></tr>'.$woven_fab;	
						
		//knit fabrics table here
		$fab_knit_req_kg_avg_amount=$knit_subtotal_amount/$knit_subtotal_avg_cons*$fab_knit_req_kg_avg;
		$knit_fab .='<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="2" align="left">Sub Total</td>
						<td align="right">'.number_format($knit_subtotal_avg_cons,4).'</td>
						<td align="right">'.number_format($knit_subtotal_avg_cons_yarn,4).'</td>
						<td></td>
						<td align="right">'.number_format($knit_subtotal_amount,4).'</td>
					</tr>';
					/*$knit_fab .='<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="2" align="left">Sub Total (Avg)</td>
						<td align="right">'.number_format($fab_knit_req_kg_avg,4).'</td>
						<td align="right">'.number_format($fab_knit_req_kg_avg,4).'</td>
						<td></td>
						<td align="right">'.number_format($fab_knit_req_kg_avg_amount,4).'</td>
					</tr>';*/
					if($zero_value==1)
					{
  		               echo $knit_fab;
					}
					else
					{
						if($row[csf("avg_cons")]>0)
						{
							echo $knit_fab;
						}
						else
						{
							echo "";
						}
						
					}
		
		//woven fabrics table here 
		$fab_woven_req_yds_avg_amount=$woven_subtotal_amount/$woven_subtotal_avg_cons*$fab_woven_req_yds_avg;
		$woven_fab .='<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="2" align="left">Sub Total</td>
						<td align="right">'.number_format($woven_subtotal_avg_cons,4).'</td>
						<td align="right">'.number_format($woven_subtotal_avg_cons_yarn,4).'</td>
						<td></td>
						<td align="right">'.number_format($woven_subtotal_amount,4).'</td>
					</tr>';
					/*$woven_fab .='<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="2" align="left">Sub Total (Avg)</td>
						<td align="right">'.number_format($fab_woven_req_yds_avg,4).'</td>
						<td align="right">'.number_format($fab_woven_req_yds_avg,4).'</td>
						<td></td>
						<td align="right">'.number_format($fab_woven_req_yds_avg_amount,4).'</td>
					</tr>';*/
   					$woven_fab .='<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="4" align="left">Total</td>
						<td align="right"></td>
						<td></td>
						<td align="right">'.number_format(($woven_subtotal_amount+$knit_subtotal_amount),4).'</td>
					</tr></table></div>';
					/*$woven_fab .='<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="4" align="left">Total (Avg)</td>
						<td align="right"></td>
						<td></td>
						<td align="right">'.number_format(($fab_woven_req_yds_avg_amount+$fab_knit_req_kg_avg_amount),4).'</td>
					</tr></table></div>';*/
        // echo $woven_fab; 
		 if($zero_value==1)
					{
  		               echo $woven_fab;
					}
					else
					{
						if($row[csf("avg_cons")]>0)
						{
							echo $woven_fab;
						}
						else
						{
							echo "";
						}
						
					}
  		$grand_total_amount = $knit_subtotal_amount+$woven_subtotal_amount;
		//end 	All Fabric Cost part report-------------------------------------------
  	
	
		//Start	Yarn Cost part report here -------------------------------------------
		$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
		//mysql
		/*$sql = "select id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, sum(cons_qnty) as cons_qnty, rate, sum(amount) as amount
				from wo_pre_cost_fab_yarn_cost_dtls 
				where job_no=".$txt_job_no." group by count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, rate";*/
				//oracle 
				 $sql = "select min(id) as id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, min(cons_ratio) as cons_ratio , sum(cons_qnty) as cons_qnty, rate, sum(amount) as amount
				from wo_pre_cost_fab_yarn_cost_dtls 
				where job_no='".$txt_job_no."' group by count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, rate";
		$data_array=sql_select($sql);
		if($zero_value==1)
		{
		?>
        <div style="margin-top:15px">
        	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <tr style="font-weight:bold">
                    <td width="70" rowspan="<? echo count($data_array)+2; ?>"><div><b>Yarn Cost</b></div></td>
                    <td width="350">Yarn Desc</td>
                    <td width="100">Yarn Qnty</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                </tr>
			<?
            $total_qnty=0;$total_amount=0;
			foreach( $data_array as $row )
            { 
				if($row[csf("percent_one")]==100)
					$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$yarn_type[$row[csf("type_id")]];
            	else
					$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$composition[$row[csf("copm_two_id")]]." ".$row[csf("percent_two")]."% ".$yarn_type[$row[csf("type_id")]];
 			
			?>	 
                <tr>
                    <td align="left"><? echo $item_descrition; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_qnty")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
				 $total_qnty += $row[csf("cons_qnty")];
				 $total_amount += $row[csf("amount")];
            }
            ?>
            	<tr class="rpt_bottom" style="font-weight:bold">
                    <td>Total</td>
                    <td align="right"><? echo number_format($total_qnty,4); ?></td>
                    <td></td>
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>
        	</table>
      </div>
      <?
	  $grand_total_amount +=$total_amount;
	  }
	  else
	  {
		  if($fabric_cost>0)
		  {
		  ?>
           <div style="margin-top:15px">
        	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <tr style="font-weight:bold">
                    <td width="70" rowspan="<? echo count($data_array)+2; ?>"><div class="verticalText"><b>Yarn Cost</b></div></td>
                    <td width="350">Yarn Desc</td>
                    <td width="100">Yarn Qnty</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                </tr>
			<?
            $total_qnty=0;$total_amount=0;
			foreach( $data_array as $row )
            { 
				if($row[csf("percent_one")]==100)
					$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$yarn_type[$row[csf("type_id")]];
            	else
					$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$composition[$row[csf("copm_two_id")]]." ".$row[csf("percent_two")]."% ".$yarn_type[$row[csf("type_id")]];
 			
			?>	 
                <tr>
                    <td align="left"><? echo $item_descrition; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_qnty")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
				 $total_qnty += $row[csf("cons_qnty")];
				 $total_amount += $row[csf("amount")];
            }
            ?>
            	<tr class="rpt_bottom" style="font-weight:bold">
                    <td>Total</td>
                    <td align="right"><? echo number_format($total_qnty,4); ?></td>
                    <td></td>
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>
        	</table>
      </div>
      <?
	      $grand_total_amount +=$total_amount;
		  }
		  else
		  {
			 echo ""; 
		  }
	  }
	//End Yarn Cost part report here -------------------------------------------
  	
  
  
  	//start	Conversion Cost to Fabric report here -------------------------------------------
   	$sql = "select a.id,a.fabric_description as fabric_description_id, a.job_no, a.cons_process, a.req_qnty, a.charge_unit, a.amount, a.status_active,b.body_part_id,b.fab_nature_id,b.color_type_id,b.fabric_description 
			from wo_pre_cost_fab_conv_cost_dtls a left join wo_pre_cost_fabric_cost_dtls b on a.job_no=b.job_no and a.fabric_description=b.id
			where a.job_no='".$txt_job_no."' ";
	$data_array=sql_select($sql);
	if($zero_value==1)
	{
	?>

        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <tr style="font-weight:bold">
                    <td width="80" rowspan="<? echo count($data_array)+3; ?>"><div><b>Conversion Cost to Fabric</b></div></td>
                    <td width="350">Particulars</td>
                    <td width="100">Process</td>
                    <td width="100">Cons/Dzn</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
			if($row[csf("fabric_description_id")] !=0)
			{
 				$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("fabric_description")];
			}
			else
			{
				$item_descrition = "All Fabrics";
			}
			?>	 
                <tr>
                    <td align="left"><? echo $item_descrition; ?></td>
                    <td align="left"><? echo $conversion_cost_head_array[$row[csf("cons_process")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("req_qnty")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("charge_unit")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="4">Total</td>                    
                    <td align="right"><? echo $total_amount; ?></td>
                </tr>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td align="left" colspan="4">Total Fabric Cost</td>                    
                    <td align="right"><? echo number_format(($grand_total_amount+$total_amount),4); ?></td>
                </tr>
            </table>
      </div>
      <?
	}
	else
	{
		if($fabric_cost>0)
		{
	?>
     <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <tr style="font-weight:bold">
                    <td width="80" rowspan="<? echo count($data_array)+3; ?>"><div><b>Conversion Cost to Fabric</b></div></td>
                    <td width="350">Particulars</td>
                    <td width="100">Process</td>
                    <td width="100">Cons/Dzn</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
			if($row[csf("fabric_description_id")] !=0)
			{
 				$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("fabric_description")];
			}
			else
			{
				$item_descrition = "All Fabrics";
			}
			?>	 
                <tr>
                    <td align="left"><? echo $item_descrition; ?></td>
                    <td align="left"><? echo $conversion_cost_head_array[$row[csf("cons_process")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("req_qnty")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("charge_unit")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="4">Total</td>                    
                    <td align="right"><? echo $total_amount; ?></td>
                </tr>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td align="left" colspan="4">Total Fabric Cost</td>                    
                    <td align="right"><? echo number_format(($grand_total_amount+$total_amount),4); ?></td>
                </tr>
            </table>
      </div>
    <?	
		}
		else
		{
			echo "";
		}
	}
	//End Conversion Cost to Fabric report here -------------------------------------------
  	
  
 
  	//start	Trims Cost part report here -------------------------------------------
   	$sql = "select id, job_no, trim_group,description,brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,status_active
			from wo_pre_cost_trim_cost_dtls  
			where job_no='".$txt_job_no."'";
	$data_array=sql_select($sql);
	if($zero_value==1)
	{
	?>
 

        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Trims Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Item Group</td>
                    <td width="150">Description</td>
                    <td width="150">Brand/Supp Ref</td>
                    <td width="100">UOM</td>
                    <td width="100">Cons/Dzn</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
 				$trim_group=return_library_array( "select item_name,id from  lib_item_group where id=".$row[csf("trim_group")], "id", "item_name"  );
            	
			?>	 
                <tr>
                    <td align="left"><? echo $trim_group[$row[csf("trim_group")]]; ?></td>
                    <td align="left"><? echo $row[csf("description")]; ?></td>
                    <td align="left"><? echo $row[csf("brand_sup_ref")]; ?></td>
                    <td align="left"><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_dzn_gmts")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="6">Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
      </div>
      <?
	}
	else
	{
		if($trims_cost>0)
		{
	?>
    <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Trims Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Item Group</td>
                    <td width="150">Description</td>
                    <td width="150">Brand/Supp Ref</td>
                    <td width="100">UOM</td>
                    <td width="100">Cons/Dzn</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
 				$trim_group=return_library_array( "select item_name,id from  lib_item_group where id=".$row[csf("trim_group")], "id", "item_name"  );
            	
			?>	 
                <tr>
                    <td align="left"><? echo $trim_group[$row[csf("trim_group")]]; ?></td>
                    <td align="left"><? echo $row[csf("description")]; ?></td>
                    <td align="left"><? echo $row[csf("brand_sup_ref")]; ?></td>
                    <td align="left"><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_dzn_gmts")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="6">Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
      </div>
	<?
		}
		else
		{
		   echo "";	
		}
	}
	 //End Trims Cost Part report here -------------------------------------------	
  
  
 	
	//start	Embellishment Details part report here -------------------------------------------
    	$sql = "select id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active
			from wo_pre_cost_embe_cost_dtls  
			where job_no='".$txt_job_no."'";
	$data_array=sql_select($sql);
	if($zero_value==1)
	{
	?> 
 

        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Embellishment Details</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="150">Type</td>
                    <td width="150">Cons/Dzn</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
 				//$emblishment_name_array=array(1=>"Printing",2=>"Embroidery",3=>"Wash",4=>"Special Works",5=>"Others");
 				if($row[csf("emb_name")]==1)$em_type = $emblishment_print_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==2)$em_type = $emblishment_embroy_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==3)$em_type = $emblishment_wash_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==4)$em_type = $emblishment_spwork_type[$row[csf("emb_type")]];
			?>	 
                <tr>
                    <td align="left"><? echo $emblishment_name_array[$row[csf("emb_name")]]; ?></td>
                    <td align="left"><? echo $em_type; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_dzn_gmts")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="4">Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
      </div>
      <?
	}
	else
	{
		if($embel_cost>0)
		{
	?>
     <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Embellishment Details</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="150">Type</td>
                    <td width="150">Cons/Dzn</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
 				//$emblishment_name_array=array(1=>"Printing",2=>"Embroidery",3=>"Wash",4=>"Special Works",5=>"Others");
 				if($row[csf("emb_name")]==1)$em_type = $emblishment_print_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==2)$em_type = $emblishment_embroy_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==3)$em_type = $emblishment_wash_type[$row[csf("emb_type")]];
				else if($row[csf("emb_name")]==4)$em_type = $emblishment_spwork_type[$row[csf("emb_type")]];
			?>	 
                <tr>
                    <td align="left"><? echo $emblishment_name_array[$row[csf("emb_name")]]; ?></td>
                    <td align="left"><? echo $em_type; ?></td>
                    <td align="right"><? echo number_format($row[csf("cons_dzn_gmts")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="4">Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
      </div>
    <?	
		}
		else
		{
			echo "";
		}
	}
	 //End Embellishment Details Part report here -------------------------------------------	
  
  
  	//start	Commercial Cost part report here -------------------------------------------
   	$sql = "select id, job_no, item_id, rate, amount, status_active
			from  wo_pre_cost_comarci_cost_dtls  
			where job_no='".$txt_job_no."'";
	$data_array=sql_select($sql);
	if($zero_value==1)
	{
	?> 
 
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Commercial Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="100">Rate In %</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
  			?>	 
                <tr>
                    <td align="left"><? echo $camarcial_items[$row[csf("item_id")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="2">Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
      </div>
      <?
	}
	else
	{
		if($comm_cost>0)
		{
		?>
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Commercial Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
  			?>	 
                <tr>
                    <td align="left"><? echo $camarcial_items[$row[csf("item_id")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="2">Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
      </div>
        <?
		}
		else
		{
			echo "";
		}
		
	}
	 //End Commercial Cost Part report here -------------------------------------------	
  
   
  	//start	Commission Cost part report here -------------------------------------------
   	$sql = "select id,job_no,particulars_id,commission_base_id,commision_rate,commission_amount, status_active
			from  wo_pre_cost_commiss_cost_dtls  
			where job_no='".$txt_job_no."'";
	$data_array=sql_select($sql);
	if($zero_value==1)
	{
	?> 
  

        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Commission Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="150">Commission Basis</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
  			?>	 
                <tr>
                    <td align="left"><? echo $commission_particulars[$row[csf("particulars_id")]]; ?></td>
                    <td align="left"><? echo $commission_base_array[$row[csf("commission_base_id")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("commision_rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("commission_amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("commission_amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="3">Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
      </div>
      <?
	}
	else
	{
		if($commission>0)
		{
		?>
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Commission Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="150">Commission Basis</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                 </tr>
            <?
            $total_amount=0;
            foreach( $data_array as $row )
            { 
  			?>	 
                <tr>
                    <td align="left"><? echo $commission_particulars[$row[csf("particulars_id")]]; ?></td>
                    <td align="left"><? echo $commission_base_array[$row[csf("commission_base_id")]]; ?></td>
                    <td align="right"><? echo number_format($row[csf("commision_rate")],4); ?></td>
                    <td align="right"><? echo number_format($row[csf("commission_amount")],4); ?></td>
                </tr>
            <?
                 $total_amount += $row[csf("commission_amount")];
            }
            ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="3">Total</td>                    
                    <td align="right"><? echo number_format($total_amount,4); ?></td>
                </tr>                
            </table>
      </div>
        <?
		}
		else
		{
		   echo "";	
		}
		
	}
	 //End Commission Cost Part report here -------------------------------------------	
  
  
	?>
    <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:300px;" rules="all">
            <label><b>CM Details</b></label>
                <tr>
                    <td width="150">CPM (TK)</td>
                    <td width="150"><? echo $financial_para[cost_per_minute] ?></td>
                    
                 </tr>
                  <tr>
                    <td width="150">SMV</td>
                    <td width="150"><? echo $sew_smv ;//echo $sew_smv .", ".$cut_smv ?></td>
                 </tr>
                 <tr>
                    <td width="150">Efficiency %</td>
                    <td width="150"><? echo $sew_effi_percent ;//echo $sew_effi_percent .", ".$cut_effi_percent; ?></td>
                 </tr>
                 
                 <tr>
                    <td width="150">Exchange Rate</td>
                    <td width="150"><? echo $exchange_rate; ?></td>
                 </tr>
                 
                 
                 
  </table>
  </div>		
</fieldset>
 
<?
exit;	
}//cost_info
//end cost info-----------------------------------------------------




if($action=="sample_approval")
{
$sample_lib_arr=return_library_array( "select id,sample_name from lib_sample", "id", "sample_name"  );

$color_lib_arr=return_library_array( "select a.color_mst_id ,b.color_name from wo_po_color_size_breakdown a, lib_color b where a.color_number_id=b.id  and a.po_break_down_id='$po_id' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.po_break_down_id ,b.color_name, a.color_mst_id order by b.color_name",'color_mst_id','color_name');


$samp_app_data_arr=sql_select("select color_number_id, sample_type_id,target_approval_date, submitted_to_buyer, send_to_factory_date, approval_status, approval_status_date, sample_comments from wo_po_sample_approval_info where job_no_mst='$job_no' and po_break_down_id='$po_id' and is_deleted=0 and status_active=1");
foreach($samp_app_data_arr as $rows):
	$data_arr[$rows[csf('sample_type_id')]][$rows[csf('color_number_id')]]=array(
			'target_approval_date'	=>$rows[csf('target_approval_date')],
			'send_to_factory_date'	=>$rows[csf('send_to_factory_date')],
			'submitted_to_buyer'	=>$rows[csf('submitted_to_buyer')],
			'approval_status'		=>$rows[csf('approval_status')],
			'approval_status_date'	=>$rows[csf('approval_status_date')],
			'sample_comments'		=>$rows[csf('sample_comments')]
		);
endforeach;	


if(count($data_arr)){
?>
<fieldset>
<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
    <thead>
        <th width="35">Sl</th>
        <th width="150">Sample Type</th>
        <th width="80">Color</th>
        <th width="80">Target App. Date</th>
        <th width="80">Sent To Factory</th>
        <th width="80">Sub to Buyer</th>
        <th width="80">Status</th>
        <th width="80">Status Date</th>
        <th>Comment</th>
    </thead>
</table>
<div style="max-height:280px; overflow-y:scroll;">
<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
	<tbody>
<?
$i=1;
foreach($data_arr as $sample_id=>$color_rows):
$bgcolor=$i%2==0?'#E9F3FF':'#FFFFFF';
$rowspan=count($color_rows);
?>
    <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" style="cursor:pointer;">
        <td width="35" align="center" rowspan="<? echo $rowspan;?>"><? echo $i; ?></td>
        <td width="150" rowspan="<? echo $rowspan;?>"><? echo $sample_lib_arr[$sample_id]; ?></td>
       <? foreach($color_rows as $color=>$row):?>
                <td width="80" align="center"><? echo $color_lib_arr[$color]; ?></td>
                <td width="80" align="center"><? echo change_date_format($row['target_approval_date']);?></td>
                <td width="80" align="center"><? echo change_date_format($row['send_to_factory_date']);?></td>
                <td width="80" align="center"><? echo change_date_format($row['submitted_to_buyer']);?></td>
                <td width="80" align="center"><? echo $approval_status[$row['approval_status']];?></td>
                <td width="80" align="center"><? echo change_date_format($row['approval_status_date']);?></td>
                <td><? echo $row['sample_comments'];?></td>
            </tr>
	<?
	endforeach;
$i++;
endforeach;
 ?>

</tbody>
</table>

</div>
</fieldset>
<?
}
else
{
	echo "<h1>Sample Approval Entry Not Found</h1>";
}
exit;	
}

if($action=="labdip_approval")
{
//$sample_lib_arr=return_library_array( "select id,sample_name from lib_sample", "id", "sample_name"  );

$color_lib_arr=return_library_array( "select id,color_name from lib_color where is_deleted=0 and  status_active=1",'id','color_name');


$ldp_app_data_arr=sql_select("select color_name_id,lapdip_target_approval_date, send_to_factory_date, recv_from_factory_date, submitted_to_buyer, approval_status, approval_status_date, lapdip_no, lapdip_comments from wo_po_lapdip_approval_info where job_no_mst='$job_no' and po_break_down_id='$po_id' and is_deleted=0 and status_active=1");

if(count($ldp_app_data_arr)){


?>
<fieldset>
<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
    <thead>
        <th width="35">Sl</th>
        <th width="80">Color</th>
        <th width="90">Target App. Date</th>
        <th width="90">Sent To Lab Section</th>
        <th width="90">Recv. From Lab Section</th>
        <th width="90">Submission to Buyer</th>
        <th width="90">Status</th>
        <th width="90">Status Date</th>
        <th width="90">Lapdip No</th>
        <th>Comment</th>
    </thead>
</table>
<div style="max-height:280px; overflow-y:scroll;">
<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
<tbody>
<?
$i=1;
foreach($ldp_app_data_arr as $row):
$bgcolor=$i%2==0?'#E9F3FF':'#FFFFFF';
?>
    <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" style="cursor:pointer;">
        <td width="35" align="center"><? echo $i; ?></td>
        <td width="80" align="center"><? echo $color_lib_arr[$row[csf('color_name_id')]]; ?></td>
        <td width="90" align="center"><? echo change_date_format($row[csf('lapdip_target_approval_date')]); ?></td>
        <td width="90" align="center"><? echo change_date_format($row[csf('send_to_factory_date')]);?></td>
        <td width="90" align="center"><? echo change_date_format($row[csf('recv_from_factory_date')]);?></td>
        <td width="90" align="center"><? echo change_date_format($row[csf('submitted_to_buyer')]);?></td>
        <td width="90" align="center"><? echo $approval_status[$row[csf('approval_status')]];?></td>
        <td width="90" align="center"><? echo change_date_format($row[csf('approval_status_date')]);?></td>
        <td width="90"><? echo $row[csf('lapdip_no')];?></td>
        <td><? echo $row[csf('lapdip_comments')];?></td>
    </tr>
	<?
$i++;
endforeach;
 ?>

</tbody>
</table>
</div>
</fieldset>
<?
}
else
{
 echo "<h1>Labdip Approval Entry Not Found</h1>";
}
exit;	
}




if($action=="yarn_req")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
	<div style="width:850px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:845px; margin-left:10px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="810" cellpadding="0" cellspacing="0">
            	<thead>
					<th colspan="8"><b>Required Qnty Info</b></th>
				</thead>
				<thead>
                    <th width="40">SL</th>
                    <th width="120">Order No.</th>
                    <th width="120">Buyer Name</th>
                    <th width="90">Cons/Dzn</th>
                    <th width="110">Order Qnty</th>
                    <th width="110">Plan Cut Qnty</th>
                    <th width="110">Required Qnty</th>
                    <th>Shipment Date</th>
                </thead>
             </table>
             <div style="width:830px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="810" cellpadding="0" cellspacing="0">
                    <?
					$costing_per_id_library=array(); $costing_date_library=array();
					$costing_sql=sql_select("select job_no, costing_per, costing_date from wo_pre_cost_mst");
					foreach($costing_sql as $row)
					{
						$costing_per_id_library[$row[csf('job_no')]]=$row[csf('costing_per')]; 
						$costing_date_library[$row[csf('job_no')]]=$row[csf('costing_date')];
					}

				  
				   
				    $i=1; $tot_req_qnty=0;
					$sql="select a.buyer_name, a.job_no, a.total_set_qnty as ratio, b.po_number, b.po_quantity, b.pub_shipment_date, b.plan_cut, sum(c.avg_cons_qnty) as qnty from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_fab_yarn_cost_dtls c where a.job_no=b.job_no_mst and a.job_no=c.job_no and b.id in($order_id) and c.count_id='$yarn_count' and c.copm_one_id='$yarn_comp_type1st' and c.percent_one='$yarn_comp_percent1st' and c.copm_two_id='$yarn_comp_type2nd' and c.percent_two='$yarn_comp_percent2nd' and c.type_id='$yarn_type_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.id, a.buyer_name, a.job_no, a.total_set_qnty, b.po_number, b.po_quantity, b.pub_shipment_date, b.plan_cut";//sum(c.cons_qnty)
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
						$dzn_qnty=0; $required_qnty=0; $order_qnty=0; 
						if($costing_per_id_library[$row[csf('job_no')]]==1) $dzn_qnty=12;
						else if($costing_per_id_library[$row[csf('job_no')]]==3) $dzn_qnty=12*2;
						else if($costing_per_id_library[$row[csf('job_no')]]==4) $dzn_qnty=12*3;
						else if($costing_per_id_library[$row[csf('job_no')]]==5) $dzn_qnty=12*4;
						else $dzn_qnty=1;
						
						$order_qnty=$row[csf('po_quantity')]*$row[csf('ratio')];
						$plan_cut_qnty=$row[csf('plan_cut')];
						$required_qnty=$plan_cut_qnty*($row[csf('qnty')]/$dzn_qnty);
                        $tot_req_qnty+=$required_qnty;
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="40"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('po_number')]; ?></p></td>
                            <td width="120"><p><? echo $buyer_full_name_arr[$row[csf('buyer_name')]]; ?></p></td>
                            <td width="90" align="right"><p><? echo number_format($row[csf('qnty')],2); ?></p></td>
                            <td width="110" align="right"><p><? echo number_format($order_qnty,0); ?></p></td>
                            <td width="110" align="right"><p><? echo number_format($plan_cut_qnty,0); ?></p></td>
                            <td width="110" align="right"><p><? echo number_format($required_qnty,2); ?></p></td>
                            <td align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th align="right" colspan="6">Total</th>
                        <th align="right"><? echo number_format($tot_req_qnty,2); ?></th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>   
<?
exit();
}

if($action=="yarn_issue")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$brand_array=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name"  );
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
	<div style="width:870px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:865px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
            	<thead>
					<th colspan="10"><b>Yarn Issue</b></th>
				</thead>
				<thead>
                    <th width="105">Issue Id</th>
                    <th width="90">Issue To</th>
                    <th width="105">Booking No</th>
                    <th width="80">Challan No</th>
                    <th width="70">Brand</th>
                    <th width="60">Lot No</th>
                    <th width="75">Issue Date</th>
                    <th width="80">Yarn Type</th>
                    <th width="90">Issue Qnty (In)</th>
                    <th>Issue Qnty (Out)</th>
				</thead>
                <?
                $i=1; $total_yarn_issue_qnty=0; $total_yarn_issue_qnty_out=0;
				$sql="select a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, sum(b.quantity) as issue_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id from inv_issue_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=2 and d.item_category=1 and c.item_category_id=1 and d.id=b.trans_id and b.trans_type=2 and b.entry_form=3 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and c.yarn_count_id='$yarn_count' and c.yarn_comp_type1st='$yarn_comp_type1st' and c.yarn_comp_percent1st='$yarn_comp_percent1st' and c.yarn_comp_type2nd='$yarn_comp_type2nd' and c.yarn_comp_percent2nd='$yarn_comp_percent2nd' and c.yarn_type='$yarn_type_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2 group by a.id, c.id, a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, c.lot, c.yarn_type, c.product_name_details, d.brand_id";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
				
					if($row[csf('knit_dye_source')]==1) 
					{
						$issue_to=$company_library[$row[csf('knit_dye_company')]]; 
					}
					else if($row['knit_dye_source']==3) 
					{
						$issue_to=$supplier_details[$row[csf('knit_dye_company')]];
					}
					else
						$issue_to="&nbsp;";
						
                    $yarn_issued=$row[csf('issue_qnty')];
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="105"><p><? echo $row[csf('issue_number')]; ?></p></td>
                        <td width="90"><p><? echo $issue_to; ?></p></td>
                        <td width="105"><p><? echo $row[csf('booking_no')];?>&nbsp;</p></td>
                        <td width="80"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                        <td width="70"><p><? echo $brand_array[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
                        <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
                        <td width="75" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                        <td width="80"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
                        <td align="right" width="90">
							<? 
								if($row[csf('knit_dye_source')]!=3)
								{
									echo number_format($yarn_issued,2);
									$total_yarn_issue_qnty+=$yarn_issued;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                        <td align="right">
							<? 
								if($row[csf('knit_dye_source')]==3)
								{ 
									echo number_format($yarn_issued,2); 
									$total_yarn_issue_qnty_out+=$yarn_issued;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                    </tr>
                <?
                $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty,2);?></td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty_out,2);?></td>
                </tr>
                <tr style="font-weight:bold">
                    <td align="right" colspan="9">Issue Total</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty+$total_yarn_issue_qnty_out,2);?></td>
                </tr>
                <thead>
                    <th colspan="10"><b>Yarn Return</b></th>
                </thead>
                <thead>
                	<th width="105">Return Id</th>
                    <th width="90">Return From</th>
                    <th width="105">Booking No</th>
                    <th width="80">Challan No</th>
                    <th width="70">Brand</th>
                    <th width="60">Lot No</th>
                    <th width="75">Return Date</th>
                    <th width="80">Yarn Type</th>
                    <th width="90">Return Qnty (In)</th>
                    <th>Return Qnty (Out)</th>
               </thead>
                <?
                $total_yarn_return_qnty=0; $total_yarn_return_qnty_out=0;
                $sql="select a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, sum(b.quantity) as returned_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=4 and c.item_category_id=1 and d.item_category=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and c.yarn_count_id='$yarn_count' and c.yarn_comp_type1st='$yarn_comp_type1st' and c.yarn_comp_percent1st='$yarn_comp_percent1st' and c.yarn_comp_type2nd='$yarn_comp_type2nd' and c.yarn_comp_percent2nd='$yarn_comp_percent2nd' and c.yarn_type='$yarn_type_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2 group by a.id, c.id, a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, c.lot, c.yarn_type, c.product_name_details, d.brand_id";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
				
					if($row[csf('knitting_source')]==1) 
					{
						$return_from=$company_library[$row[csf('knitting_company')]]; 
					}
					else if($row['knitting_source']==3) 
					{
						$return_from=$supplier_details[$row[csf('knitting_company')]];
					}
					else
						$return_from="&nbsp;";
						
                    $yarn_returned=$row[csf('returned_qnty')];
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="105"><p><? echo $row[csf('recv_number')]; ?></p></td>
                        <td width="90"><p><? echo $return_from; ?></p></td>
                        <td width="105"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
                        <td width="80"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                        <td width="70"><p><? echo $brand_array[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
                        <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
                        <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                        <td width="80"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
                        <td align="right" width="90">
							<? 
								if($row[csf('knitting_source')]!=3)
								{
									echo number_format($yarn_returned,2);
									$total_yarn_return_qnty+=$yarn_returned;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                        <td align="right">
							<? 
								if($row[csf('knitting_source')]==3)
								{ 
									echo number_format($yarn_returned,2); 
									$total_yarn_return_qnty_out+=$yarn_returned;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                    </tr>
                <?
                $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td align="right">Balance</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty-$total_yarn_return_qnty,2);?></td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty_out-$total_yarn_return_qnty_out,2);?></td>
                </tr>
                <tfoot>    
                    <tr>
                        <th align="right" colspan="9">Total Balance</th>
                        <th align="right"><? echo number_format(($total_yarn_issue_qnty+$total_yarn_issue_qnty_out)-($total_yarn_return_qnty+$total_yarn_return_qnty_out),2);?></th>
                    </tr>
                </tfoot>
            </table>	
		</div>
	</fieldset>  
<?
exit();
}



if($action=="fab_info")
{
	
?>	
<script>
function openmypage(order_id,type,yarn_count,yarn_comp_type1st,yarn_comp_percent1st,yarn_comp_type2nd,yarn_comp_percent2nd,yarn_type)
{
	var popup_width='';
	if(type=="yarn_issue_not") 
	{
		popup_width='1000px';
	}
	else
	{
		popup_width='890px';
	}
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'capacity_and_order_booking_status_ffl_controller.php?order_id='+order_id+'&action='+type+'&yarn_count='+yarn_count+'&yarn_comp_type1st='+yarn_comp_type1st+'&yarn_comp_percent1st='+yarn_comp_percent1st+'&yarn_comp_type2nd='+yarn_comp_type2nd+'&yarn_comp_percent2nd='+yarn_comp_percent2nd+'&yarn_type_id='+yarn_type, 'Detail Veiw', 'width='+popup_width+', height=380px,center=1,resize=0,scrolling=0','../../../');
}
	


function open_febric_receive_status_order_wise_popup(order_id,type,color)
{
	var popup_width='';
	if(type=="fabric_receive" || type=="fabric_purchase" || type=="grey_issue" || type=="dye_qnty") 
	{
		popup_width='900px';
	}
	else if(type=="grey_receive" || type=="grey_purchase")
	{
		popup_width='1000px';	
	}
	else popup_width='760px';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'capacity_and_order_booking_status_ffl_controller.php?order_id='+order_id+'&action='+type+'&color='+color, 'Detail Veiw', 'width='+popup_width+', height=380px,center=1,resize=0,scrolling=0','../../../');
}


function generate_worder_report(type,booking_no,company_id,order_id,fabric_nature,fabric_source,job_no,approved,print_report_format)
{
	var print_report_format=print_report_format.split(',');
	if(print_report_format.length==1 && type==2)
	{
		if(print_report_format[0]==1)var action='show_fabric_booking_report_gr';	
		if(print_report_format[0]==2)var action='show_fabric_booking_report';	
		if(print_report_format[0]==3)var action='show_fabric_booking_report3';	
		if(print_report_format[0]==4)var action='show_fabric_booking_report1';	
		if(print_report_format[0]==5)var action='show_fabric_booking_report2';	
		if(print_report_format[0]==6)var action='show_fabric_booking_report4';	
		if(print_report_format[0]==7)var action='show_fabric_booking_report5';	
		if(print_report_format[0]==28)var action='show_fabric_booking_report_akh';	
		
	}
	else
	{
	var action='show_fabric_booking_report';	
	}
	
	var data="action="+action+
				'&txt_booking_no='+"'"+booking_no+"'"+
				'&cbo_company_name='+"'"+company_id+"'"+
				'&txt_order_no_id='+"'"+order_id+"'"+
				'&cbo_fabric_natu='+"'"+fabric_nature+"'"+
				'&cbo_fabric_source='+"'"+fabric_source+"'"+
				'&id_approved_id='+"'"+approved+"'"+
				'&txt_job_no='+"'"+job_no+"'";
	if(type==1)	
	{			
		http.open("POST","../../../../order/woven_order/requires/short_fabric_booking_controller.php",true);
	}
	else if(type==2)
	{
		http.open("POST","../../../../order/woven_order/requires/fabric_booking_controller.php",true);
	}
	else
	{
		http.open("POST","../../../../order/woven_order/requires/sample_booking_controller.php",true);
	}
	
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = generate_fabric_report_reponse;
}

function generate_fabric_report_reponse()
{
	if(http.readyState == 4) 
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
		d.close();
	}
}

	

</script>

<?	
	
	
$sql_po_cond=" and b.id =$po_id";
$company_name=$cbo_company_name;
$txt_job_no=trim($txt_job_no); 
if(trim($txt_job_no)!=""){$job_no_cond=" and a.job_no='$txt_job_no'";}
//----------------------
$color_array=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
$yarn_count_details=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");

if($db_type==0)
{
	$fabric_desc_details=return_library_array( "select job_no, group_concat(distinct(fabric_description)) as fabric_description from wo_pre_cost_fabric_cost_dtls group by job_no", "job_no", "fabric_description");
}
else
{
	$fabric_desc_details=return_library_array( "select job_no, LISTAGG(cast(fabric_description as varchar2(4000)), ',') WITHIN GROUP (ORDER BY id) as fabric_description from wo_pre_cost_fabric_cost_dtls group by job_no", "job_no", "fabric_description");
}

//-------

		if($db_type==0)
		{
			$po_color_arr=return_library_array( "select po_breakdown_id, group_concat(distinct(color_id)) as color_id from order_wise_pro_details where entry_form in(7,18,37) and color_id<>0 $color_cond_prop group by po_breakdown_id", "po_breakdown_id", "color_id");
		}
		else
		{
			$po_color_arr=return_library_array( "select po_breakdown_id, LISTAGG(cast(color_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY color_id) color_id from order_wise_pro_details where entry_form in(7,18,37) and color_id<>0 $color_cond_prop group by po_breakdown_id", "po_breakdown_id", "color_id");
		}
		




//----------------------
		$dataArrayWo=array();
		$sql_wo="select b.po_break_down_id, a.id, a.booking_no, a.insert_date, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.booking_no_prefix_num, a.job_no, a.is_short, a.is_approved, b.fabric_color_id, sum(b.fin_fab_qnty) as req_qnty, sum(b.grey_fab_qnty) as grey_req_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $color_cond_search $wo_po_cond group by b.po_break_down_id, a.id, a.booking_no, a.insert_date, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.booking_no_prefix_num, a.job_no, a.is_short, a.is_approved, b.fabric_color_id";
		$resultWo=sql_select($sql_wo);
		foreach($resultWo as $woRow)
		{
			$dataArrayWo[$woRow[csf('po_break_down_id')]].=$woRow[csf('id')]."**".$woRow[csf('booking_no')]."**".$woRow[csf('insert_date')]."**".$woRow[csf('item_category')]."**".$woRow[csf('fabric_source')]."**".$woRow[csf('company_id')]."**".$woRow[csf('booking_type')]."**".$woRow[csf('booking_no_prefix_num')]."**".$woRow[csf('job_no')]."**".$woRow[csf('is_short')]."**".$woRow[csf('is_approved')]."**".$woRow[csf('fabric_color_id')]."**".$woRow[csf('req_qnty')]."**".$woRow[csf('grey_req_qnty')].",";
		}
	
//------------------------------	
	
	$yarn_sql="select job_no, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, sum(avg_cons_qnty) as qnty from wo_pre_cost_fab_yarn_cost_dtls where job_no='$txt_job_no' and status_active=1 and is_deleted=0 group by job_no, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id";//, sum(cons_qnty) as qnty
	$resultYarn=sql_select($yarn_sql);
	foreach($resultYarn as $yarnRow)
	{
		$dataArrayYarn[$yarnRow[csf('job_no')]].=$yarnRow[csf('count_id')]."**".$yarnRow[csf('copm_one_id')]."**".$yarnRow[csf('percent_one')]."**".$yarnRow[csf('copm_two_id')]."**".$yarnRow[csf('percent_two')]."**".$yarnRow[csf('type_id')]."**".$yarnRow[csf('qnty')].",";
	}
	
//-----------------------
$costing_per_id_library=array(); $costing_date_library=array();
$costing_sql=sql_select("select job_no, costing_per, costing_date from wo_pre_cost_mst where job_no='$txt_job_no'");
foreach($costing_sql as $row)
{
	$costing_per_id_library[$row[csf('job_no')]]=$row[csf('costing_per')]; 
	$costing_date_library[$row[csf('job_no')]]=$row[csf('costing_date')];
}

//---------------------------

		$dataArrayTrans=sql_select("select po_breakdown_id, 
								sum(CASE WHEN entry_form ='2' THEN quantity ELSE 0 END) AS grey_receive,
								sum(CASE WHEN entry_form ='45' and trans_type=3 THEN quantity ELSE 0 END) AS grey_receive_return,

								sum(CASE WHEN entry_form ='16' THEN quantity ELSE 0 END) AS grey_issue,
								sum(CASE WHEN entry_form ='61' THEN quantity ELSE 0 END) AS grey_issue_roll_wise,
								sum(CASE WHEN entry_form ='51' and trans_type=4 THEN quantity ELSE 0 END) AS grey_issue_return,
								
								sum(CASE WHEN entry_form ='11' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_yarn,
								sum(CASE WHEN entry_form ='11' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_yarn,
								sum(CASE WHEN entry_form ='13' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_knit,
								sum(CASE WHEN entry_form ='13' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_knit
								from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in(2,11,13,16,45,51,61) group by po_breakdown_id");
		foreach($dataArrayTrans as $row)
		{
			$trans_qnty_arr[$row[csf('po_breakdown_id')]]['yarn_trans']=$row[csf('transfer_in_qnty_yarn')]-$row[csf('transfer_out_qnty_yarn')];
			$trans_qnty_arr[$row[csf('po_breakdown_id')]]['knit_trans']=$row[csf('transfer_in_qnty_knit')]-$row[csf('transfer_out_qnty_knit')];
			$grey_receive_return_qnty_arr[$row[csf('po_breakdown_id')]]=$row[csf('grey_receive_return')];//add by reza;
			$grey_issue_return_qnty_arr[$row[csf('po_breakdown_id')]]=$row[csf('grey_issue_return')];//add by reza;
			
			$grey_receive_qnty_arr[$row[csf('po_breakdown_id')]]=$row[csf('grey_receive')];
			$grey_issue_qnty_arr[$row[csf('po_breakdown_id')]]=$row[csf('grey_issue')]+$row[csf('grey_issue_rollwise')];
		}

//-------------------------------
$finish_purchase_qnty_arr=array();
		$dataArrayTrans=sql_select("select po_breakdown_id, color_id, 
								sum(CASE WHEN entry_form ='7' THEN quantity ELSE 0 END) AS finish_receive,
								sum(CASE WHEN entry_form ='66' THEN quantity ELSE 0 END) AS finish_receive_rollwise,
								sum(CASE WHEN entry_form ='37' THEN quantity ELSE 0 END) AS finish_purchase,
								sum(CASE WHEN entry_form ='68' THEN quantity ELSE 0 END) AS finish_purchase_rollwise,
								sum(CASE WHEN entry_form ='18' THEN quantity ELSE 0 END) AS finish_issue,
								sum(CASE WHEN entry_form ='71' THEN quantity ELSE 0 END) AS finish_issue_roll_wise,
								sum(CASE WHEN entry_form ='46' and trans_type=3 THEN quantity ELSE 0 END) AS recv_rtn_qnty,
								sum(CASE WHEN entry_form ='52' and trans_type=4 THEN quantity ELSE 0 END) AS iss_retn_qnty,
								
								sum(CASE WHEN entry_form ='15' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty,
								sum(CASE WHEN entry_form ='15' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty
								from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in(7,15,18,37,46,52,66,71,68)  and po_breakdown_id = $po_id group by po_breakdown_id, color_id");
		foreach($dataArrayTrans as $row)
		{
			$trans_qnty_fin_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['trans']=$row[csf('transfer_in_qnty')]-$row[csf('transfer_out_qnty')];
			$finish_receive_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]=$row[csf('finish_receive')]+$row[csf('finish_receive_rollwise')];
			$finish_issue_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]=$row[csf('finish_issue')]+$row[csf('finish_issue_roll_wise')];
			$finish_recv_rtn_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]=$row[csf('recv_rtn_qnty')];
			$finish_issue_rtn_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]=$row[csf('iss_retn_qnty')];

			
		}


//-------------------------

		$greyDeliveryArray=array();
		$sql_grey_delivery="select order_id, sum(current_delivery) as grey_delivery_qty from pro_grey_prod_delivery_dtls where entry_form in(53,56) and status_active=1 and is_deleted=0 and order_id =$po_id group by order_id";
		$data_grey_delivery=sql_select($sql_grey_delivery);
		foreach($data_grey_delivery as $greyDel)
		{
			$greyDeliveryArray[$greyDel[csf('order_id')]]=$greyDel[csf('grey_delivery_qty')];
		}

//----------------------------
		$batch_qnty_arr=array();
		$sql_batch="select a.color_id, b.po_id, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.batch_against<>2 and a.entry_form=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $batch_po_cond group by a.color_id, b.po_id";
		$resultBatch=sql_select($sql_batch);
		foreach($resultBatch as $batchRow)
		{
			$batch_qnty_arr[$batchRow[csf('po_id')]][$batchRow[csf('color_id')]]=$batchRow[csf('batch_qnty')];
		}
//----------------------------

		$sql_grey_purchase="select c.po_breakdown_id, sum(c.quantity) as grey_purchase_qnty from inv_receive_master a,pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.receive_basis<>9 and a.entry_form=22 and c.entry_form=22 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $grey_purchase_po_cond group by c.po_breakdown_id";
		$dataArrayGreyPurchase=sql_select($sql_grey_purchase);
		foreach($dataArrayGreyPurchase as $greyRow)
		{
			$greyPurchaseQntyArray[$greyRow[csf('po_breakdown_id')]]=$greyRow[csf('grey_purchase_qnty')];
		}
//----------------------------
		$dye_qnty_arr=array();
		$sql_dye="select b.po_id, a.color_id, sum(b.batch_qnty) as dye_qnty from pro_batch_create_mst a,pro_batch_create_dtls b, pro_fab_subprocess c where a.id=b.mst_id and a.id=c.batch_id and c.load_unload_id=2 and c.entry_form=35 and a.batch_against<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $dye_po_cond group by b.po_id, a.color_id";
		$resultDye=sql_select($sql_dye);
		foreach($resultDye as $dyeRow)
		{
			$dye_qnty_arr[$dyeRow[csf('po_id')]][$dyeRow[csf('color_id')]]=$dyeRow[csf('dye_qnty')];
		}

//----------------------------
		$dataArrayYarnIssue=array();
		$sql_yarn_iss="select a.po_breakdown_id, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, 
				sum(CASE WHEN a.entry_form ='3' THEN a.quantity ELSE 0 END) AS issue_qnty,
				sum(CASE WHEN a.entry_form ='9' THEN a.quantity ELSE 0 END) AS return_qnty
				from order_wise_pro_details a, product_details_master b where a.prod_id=b.id and b.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.issue_purpose!=2 $yarn_iss_po_cond group by a.po_breakdown_id, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type";
		$dataArrayIssue=sql_select($sql_yarn_iss);
		foreach($dataArrayIssue as $row_yarn_iss)
		{
			$dataArrayYarnIssue[$row_yarn_iss[csf('po_breakdown_id')]].=$row_yarn_iss[csf('yarn_count_id')]."**".$row_yarn_iss[csf('yarn_comp_type1st')]."**".$row_yarn_iss[csf('yarn_comp_percent1st')]."**".$row_yarn_iss[csf('yarn_comp_type2nd')]."**".$row_yarn_iss[csf('yarn_comp_percent2nd')]."**".$row_yarn_iss[csf('yarn_type')]."**".$row_yarn_iss[csf('issue_qnty')]."**".$row_yarn_iss[csf('return_qnty')].",";
		}
		
//------------------------
		$finDeliveryArray=array();
		$sql_fin_delivery="select a.order_id, b.color, sum(a.current_delivery) as fin_delivery_qty from pro_grey_prod_delivery_dtls a, product_details_master b where a.product_id=b.id and a.entry_form in(54,67) and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 $fin_delivery_po_cond group by a.order_id, b.color";
		
		$data_fin_delivery=sql_select($sql_fin_delivery);
		foreach($data_fin_delivery as $finDel)
		{
			$finDeliveryArray[$finDel[csf('order_id')]][$finDel[csf('color')]]=$finDel[csf('fin_delivery_qty')];
		}


//------------------------
/*		$sql_fin_purchase="select c.po_breakdown_id, c.color_id, sum(c.quantity) as finish_purchase from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id  and a.entry_form in(37,68) and c.entry_form in(37,68) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $fin_purchase_po_cond group by c.po_breakdown_id, c.color_id";//and a.receive_basis<>9
		$dataArrayFinPurchase=sql_select($sql_fin_purchase);
		foreach($dataArrayFinPurchase as $finRow)
		{
			$finish_purchase_qnty_arr[$finRow[csf('po_breakdown_id')]][$finRow[csf('color_id')]]=$finRow[csf('finish_purchase')];
		}
		
		
*/		$sql_fin_purchase="select c.po_breakdown_id, c.color_id, sum(c.quantity) as finish_purchase from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.receive_basis<>9 and a.entry_form=37 and c.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id, c.color_id";
		$dataArrayFinPurchase=sql_select($sql_fin_purchase);
		foreach($dataArrayFinPurchase as $finRow)
		{
			$finish_purchase_qnty_arr[$finRow[csf('po_breakdown_id')]][$finRow[csf('color_id')]]=$finRow[csf('finish_purchase')];
		}

		

//------------------------

$sql="select a.company_name, a.buyer_name, a.job_no_prefix_num, a.job_no, a.style_ref_no, a.gmts_item_id, a.order_uom, a.total_set_qnty as ratio, b.id as po_id, b.po_number, b.po_quantity as po_qnty, b.pub_shipment_date, b.shiping_status, b.insert_date, b.po_received_date, b.plan_cut from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$company_name' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $job_no_cond $sql_po_cond order by b.pub_shipment_date,a.job_no_prefix_num, b.id";

$nameArray=sql_select($sql);


$print_report_format=return_field_value("format_id","lib_report_template","template_name='".$company_name."'  and module_id=2 and report_id=1 and is_deleted=0 and status_active=1");
$format_ids=explode(",",$print_report_format_ids2);
;
?>
<div style="width:2700px; overflow-y:auto;">
<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
<thead>
<tr>
	<th width="35" rowspan="2">Sl</th>
	<th colspan="2">Order Info</th>
	<th colspan="4">Yarn Status</th>
	<th colspan="8">Grey Fabric Status</th>
	<th colspan="10">Finish Fabric Status</th>
</tr>
<tr>
    <th width="100">Main Fabric Booking No</th>
    <th width="100">Sample Fabric Booking No</th>
    
    <th width="100">Required</th>
    <th width="100">Issued</th>
    <th width="100">Net Transfer</th>
    <th width="100">Balance</th>
    
    <th width="100">Required<br/><font style="font-size:9px; font-weight:100">(As Per Booking)</font></th>
    <th width="100">Grey Production</th>
    <th width="100">Grey Recv./ Purchase</th>
    <th width="100">Net Transfer</th>
    <th width="100">Grey Available</th>
    <th width="100">Grey Balance</th>
    <th width="100">Grey Issue</th>
    <th width="100">Batch Qnty</th>
    
    <th width="100">Fabric Color</th>
    <th width="100">Required</th>
    <th width="100">Dye Qnty</th>
    <th width="100">Fabric Production</th>
    <th width="100">Fabric Recv./ Purchase</th>
    <th width="100">Net Transfer</th>
    <th width="100">Finish Available</th>
    <th width="100">Balance</th>
    <th width="100">Issue to Cutting </th>
    <th width="100">Fabric Stock/ Left Over</th>
    
</tr>
</thead>
</table>
</div>
<div style="width:2700px; overflow-y:auto;">
<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
<tbody>
<?
//=============================================================
	
	$k=1; $i=1;
	foreach($nameArray as $row)
	{
		
		$plan_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];
		
		$template_id=$template_id_arr[$row[csf('po_id')]];
		$order_qnty_in_pcs=$row[csf('po_qnty')]*$row[csf('ratio')];
		$order_qty_array[$row[csf('buyer_name')]]+=$order_qnty_in_pcs;
		$gmts_item='';
		$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
		foreach($gmts_item_id as $item_id)
		{
			if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
		}
		
		$dzn_qnty=0; $balance=0; $job_mkt_required=0; $yarn_issued=0;
		if($costing_per_id_library[$row[csf('job_no')]]==1) $dzn_qnty=12;
		else if($costing_per_id_library[$row[csf('job_no')]]==3) $dzn_qnty=12*2;
		else if($costing_per_id_library[$row[csf('job_no')]]==4) $dzn_qnty=12*3;
		else if($costing_per_id_library[$row[csf('job_no')]]==5) $dzn_qnty=12*4;
		else $dzn_qnty=1;
		$dzn_qnty=$dzn_qnty*$row[csf('ratio')];

		$yarn_data_array=array(); $mkt_required_array=array(); $yarn_desc_array_for_popup=array(); $yarn_desc_array=array(); $yarn_iss_qnty_array=array(); $s=1;
		$dataYarn=explode(",",substr($dataArrayYarn[$row[csf('job_no')]],0,-1));
		foreach($dataYarn as $yarnRow)
		{
			$yarnRow=explode("**",$yarnRow);
			$qnty=$yarnRow[6];
			$mkt_required=$plan_cut_qnty*($qnty/$dzn_qnty);
			//echo $plan_cut_qnty.'*('.$qnty.'/'.$dzn_qnty.')';
			$count_id=$yarnRow[0];
			$copm_one_id=$yarnRow[1];
			$percent_one=$yarnRow[2];
			$copm_two_id=$yarnRow[3];
			$percent_two=$yarnRow[4];
			$type_id=$yarnRow[5];
			
			$mkt_required_array[$s]=$mkt_required;
			$job_mkt_required+=$mkt_required;
			
			$yarn_data_array['count'][$s]=$yarn_count_details[$count_id];
			$yarn_data_array['type'][$s]=$yarn_type[$type_id];
			
			if($percent_two!=0)
			{
				$compos=$composition[$copm_one_id]." ".$percent_one." %"." ".$composition[$copm_two_id]." ".$percent_two." %";
			}
			else
			{
				$compos=$composition[$copm_one_id]." ".$percent_one." %"." ".$composition[$copm_two_id];
			}

			$yarn_data_array['comp'][]=$compos;
			
			$yarn_desc_array[$s]=$yarn_count_details[$count_id]." ".$compos." ".$yarn_type[$type_id];
			
			$yarn_desc_for_popup=$count_id."__".$copm_one_id."__".$percent_one."__".$copm_two_id."__".$percent_two."__".$type_id;
			$yarn_desc_array_for_popup[$s]=$yarn_desc_for_popup;
			
			$s++;
		}
		
		$dataYarnIssue=explode(",",substr($dataArrayYarnIssue[$row[csf('po_id')]],0,-1));
		foreach($dataYarnIssue as $yarnIssueRow)
		{
			$yarnIssueRow=explode("**",$yarnIssueRow);
			$yarn_count_id=$yarnIssueRow[0];
			$yarn_comp_type1st=$yarnIssueRow[1];
			$yarn_comp_percent1st=$yarnIssueRow[2];
			$yarn_comp_type2nd=$yarnIssueRow[3];
			$yarn_comp_percent2nd=$yarnIssueRow[4];
			$yarn_type_id=$yarnIssueRow[5];
			$issue_qnty=$yarnIssueRow[6];
			$return_qnty=$yarnIssueRow[7];
			
			if($yarn_comp_percent2nd!=0)
			{
				$compostion_not_req=$composition[$yarn_comp_type1st]." ".$yarn_comp_percent1st." % ".$composition[$yarn_comp_type2nd]." ".$yarn_comp_percent2nd." %";
			}
			else
			{
				$compostion_not_req=$composition[$yarn_comp_type1st]." ".$yarn_comp_percent1st." % ".$composition[$yarn_comp_type2nd];
			}
	
			$desc=$yarn_count_details[$yarn_count_id]." ".$compostion_not_req." ".$yarn_type[$yarn_type_id];
			
			
			$net_issue_qnty=$issue_qnty-$return_qnty;
			$yarn_issued+=$net_issue_qnty;
			if(!in_array($desc,$yarn_desc_array))
			{
				$yarn_iss_qnty_array['not_req']+=$net_issue_qnty; 
			}
			else
			{ 
				$yarn_iss_qnty_array[$desc]+=$net_issue_qnty;
			}
		}


		$grey_purchase_qnty=$greyPurchaseQntyArray[$row[csf('po_id')]]-$grey_receive_return_qnty_arr[$row[csf('po_id')]];
		$grey_recv_qnty=$grey_receive_qnty_arr[$row[csf('po_id')]];
		$grey_fabric_issue=$grey_issue_qnty_arr[$row[csf('po_id')]]-$grey_issue_return_qnty_arr[$row[csf('po_id')]];

		
		
		
		if(($cbo_discrepancy==1 && $grey_recv_qnty>$yarn_issued) || ($cbo_discrepancy==0))
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$buyer_name_array[$row[csf('buyer_name')]]=$buyer_short_name_library[$row[csf('buyer_name')]];
			
			$booking_array=array(); $color_data_array=array();
			$required_qnty=0; $main_booking=''; $sample_booking=''; $main_booking_excel=''; $sample_booking_excel='';
			$dataArray=array_filter(explode(",",substr($dataArrayWo[$row[csf('po_id')]],0,-1)));
			if(count($dataArray)>0)
			{ 
				foreach($dataArray as $woRow)
				{
					$woRow=explode("**",$woRow);
					$id=$woRow[0];
					$booking_no=$woRow[1];
					$insert_date=$woRow[2];
					$item_category=$woRow[3];
					$fabric_source=$woRow[4];
					$company_id=$woRow[5];
					$booking_type=$woRow[6];
					$booking_no_prefix_num=$woRow[7];
					$job_no=$woRow[8];
					$is_short=$woRow[9];
					$is_approved=$woRow[10];
					$fabric_color_id=$woRow[11];
					$req_qnty=$woRow[12];
					$grey_req_qnty=$woRow[13];
					
					$required_qnty+=$grey_req_qnty;

					if(!in_array($id,$booking_array))
					{
						$system_date=date('d-M-Y', strtotime($insert_date));
						
						if($booking_type==4)
						{
							$sample_booking.="<a href='##' style='color:#000' onclick=\"generate_worder_report('3','".$booking_no."','".$company_id."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$job_no."','".$is_approved."','".$print_report_format."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")"."</font></a><br>";
							$sample_booking_excel.="<font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")"."</font><br>";
						}
						else
						{
							if($is_short==1) $pre="S"; else $pre="M"; 
							
							$main_booking.="<a href='##' style='color:#000' onclick=\"generate_worder_report('".$is_short."','".$booking_no."','".$company_id."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$job_no."','".$is_approved."','".$print_report_format."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font></a><br>";
							$main_booking_excel.="<font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font><br>";
						}
						
						$booking_array[]=$id;
					}
					$color_data_array[$fabric_color_id]+=$req_qnty;
				}
			}
			else
			{
				$main_booking.="No Booking";
				$main_booking_excel.="No Booking";
				$sample_booking.="No Booking";
				$sample_booking_excel.="No Booking";
			}
			
			if($main_booking=="")
			{
				$main_booking.="No Booking";
				$main_booking_excel.="No Booking";
			}
			
			if($sample_booking=="") 
			{
				$sample_booking.="No Booking";
				$sample_booking_excel.="No Booking";
			}
			
			
			$finish_color=array_unique(explode(",",$po_color_arr[$row[csf('po_id')]]));
			foreach($finish_color as $color_id)
			{
				if($color_id>0)
				{ 
					$color_data_array[$color_id]+=0;
				}
			}
			
			//var_dump($color_data_array); 
			$yarn_issue_array[$row[csf('buyer_name')]]+=$yarn_issued;
			
			$grey_required_array[$row[csf('buyer_name')]]+=$required_qnty;

			$net_trans_yarn=$trans_qnty_arr[$row[csf('po_id')]]['yarn_trans'];
			$yarn_issue_array[$row[csf('buyer_name')]]+=$net_trans_yarn;
			
			//$balance=$mkt_required_value-($yarn_issued+$net_trans_yarn);
			$balance=$required_qnty-($yarn_issued+$net_trans_yarn);
			
			$yarn_balance_array[$row[csf('buyer_name')]]+=$balance;
			
			$knitted_array[$row[csf('buyer_name')]]+=$grey_recv_qnty+$grey_purchase_qnty;
			
			$net_trans_knit=$trans_qnty_arr[$row[csf('po_id')]]['knit_trans'];
			$knitted_array[$row[csf('buyer_name')]]+=$net_trans_knit;
			
			$grey_balance=$required_qnty-($grey_recv_qnty+$net_trans_knit+$grey_purchase_qnty);
			$grey_prod_balance=$required_qnty-$grey_recv_qnty;
			$grey_del_store=$greyDeliveryArray[$row[csf('po_id')]];
			$total_grey_del_store+=$grey_del_store;
			
			$grey_balance_array[$row[csf('buyer_name')]]+=$grey_balance;
			
			$grey_issue_array[$row[csf('buyer_name')]]+=$grey_fabric_issue;
			
			$grey_available=$grey_recv_qnty+$grey_purchase_qnty+$net_trans_knit;
			$tot_order_qnty+=$order_qnty_in_pcs;
			$tot_mkt_required+=$job_mkt_required;
			$tot_yarn_issue_qnty+=$yarn_issued;
			$tot_fabric_req+=$required_qnty;
			//$tot_balance+=$balance;
			//$tot_grey_recv_qnty+=$grey_recv_qnty;
			//$tot_grey_purchase_qnty+=$grey_purchase_qnty;
			//$tot_grey_balance+=$grey_available;
			$tot_grey_prod_balance+=$grey_prod_balance;
			$tot_grey_issue+=$grey_fabric_issue;
			
			
			//$tot_grey_available+=$grey_available;
			//$required_qnty;
	
			if($required_qnty>$job_mkt_required) $bgcolor_grey_td='#FF0000'; $bgcolor_grey_td='';
			
			$po_entry_date=date('d-m-Y', strtotime($row[csf('insert_date')]));
			$costing_date=$costing_date_library[$row[csf('job_no')]];
			
			$tot_color=count($color_data_array);
			//echo $tot_color.'kkk';
			if($tot_color>0)
			{
				$z=1;
				foreach($color_data_array as $key=>$value)
				{
					if($z==1) 
					{
						$display_font_color="";
						$font_end="";
					}
					else 
					{
						$display_font_color="<font style='display:none' color='$bgcolor'>";
						$font_end="</font>";
					}
					$batch_qnty=$batch_qnty_arr[$row[csf('po_id')]][$key];
					$batch_qnty_array[$row[csf('buyer_name')]]+=$batch_qnty;
					//$tot_batch_qnty+=$batch_qnty;
					
					$fin_delivery_qty=$finDeliveryArray[$row[csf('po_id')]][$key];
					
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
						<td width="35"><? echo $display_font_color.$i.$font_end; ?></td>
                        <td width="100"><? echo $display_font_color.$main_booking.$font_end; ?></td>
                        <td width="100"><? echo $display_font_color.$sample_booking.$font_end; ?></td>
                        <td width="100" align="right">
							<? 
								if($z==1)
								{
									echo "<font color='$bgcolor' style='display:none'>".number_format(array_sum($mkt_required_array),2,'.','')."</font>\n";
									$d=1; 
									foreach($mkt_required_array as $mkt_required_value)
									{
										$tot_mkt_required_value+=$mkt_required_value;
										if($d!=1)
										{
											echo "<hr/>";
										}
										$yarn_desc_for_popup_req=explode("__",$yarn_desc_array_for_popup[$d]);
										?>
										<a href="##" onClick="openmypage('<? echo $row[csf('po_id')]; ?>','yarn_req','<? echo $yarn_desc_for_popup_req[0]; ?>','<? echo $yarn_desc_for_popup_req[1]; ?>','<? echo $yarn_desc_for_popup_req[2]; ?>','<? echo $yarn_desc_for_popup_req[3]; ?>','<? echo $yarn_desc_for_popup_req[4]; ?>','<? echo $yarn_desc_for_popup_req[5]; ?>')"><? echo number_format($mkt_required_value,2,'.','');?></a>
									<?
									$d++;
									}
								}
								
							?>
						</td>
						<td width="100" align="right" bgcolor="<? echo $discrepancy_td_color; ?>">
							<? 
								if($z==1)
								{
									echo "<font color='$bgcolor' style='display:none'>".number_format($yarn_issued,2,'.','')."</font>\n";
									$d=1;
									foreach($yarn_desc_array as $yarn_desc)
									{
										if($d!=1)
										{
											echo "<hr/>";
										}
										
										$yarn_iss_qnty=$yarn_iss_qnty_array[$yarn_desc];
										$yarn_desc_for_popup=explode("__",$yarn_desc_array_for_popup[$d]);
										
										?>
										<a href="##" onClick="openmypage('<? echo $row[csf('po_id')]; ?>','yarn_issue','<? echo $yarn_desc_for_popup[0]; ?>','<? echo $yarn_desc_for_popup[1]; ?>','<? echo $yarn_desc_for_popup[2]; ?>','<? echo $yarn_desc_for_popup[3]; ?>','<? echo $yarn_desc_for_popup[4]; ?>','<? echo $yarn_desc_for_popup[5]; ?>')"><? echo number_format($yarn_iss_qnty,2,'.',''); $tot_yarn_iss_qnty+=$yarn_iss_qnty;?></a>
										<?
										$d++;
									}
									
									if($d!=1)
									{
										echo "<hr/>";
									}
									
									$yarn_desc=join(",",$yarn_desc_array);
									
									$iss_qnty_not_req=$yarn_iss_qnty_array['not_req'];
									
									?>
									<a href="##" onClick="openmypage('<? echo $row[csf('po_id')]; ?>','yarn_issue_not','<? echo $yarn_desc; ?>','','','','','')"><? echo number_format($iss_qnty_not_req,2); $tot_yarn_iss_qnty+=$iss_qnty_not_req;?></a>
								<?
								}
								?>
						</td>
						<td width="100" align="right">
						<? 
							if($z==1) 
							{
							?>
								<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','yarn_trans','')"><? echo number_format($net_trans_yarn,2,'.','');  ?></a>
							<?	
								$tot_net_trans_yarn_qnty+=$net_trans_yarn;
							}
						?>
						</td>
						<td width="100" align="right"> 
						<? 
							if($z==1) 
							{
								echo number_format($balance,2,'.',''); $total_balance+=$balance;
							}
						?>
						</td>
						<td width="100" align="right" bgcolor="<? echo $bgcolor_grey_td; ?>">
						<? 
							if($z==1) 
							{
								echo number_format($required_qnty,2,'.',''); $tot_required_qnty+=$required_qnty; 
							}
						?>
						</td>
						<td width="100" align="right" bgcolor="<? echo $discrepancy_td_color; ?>">
							<? 
								if($z==1)
								{
								?>
									<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','grey_receive','')"><? echo number_format($grey_recv_qnty,2,'.',''); $tot_grey_recv_qnty+=$grey_recv_qnty;?></a>
								<?
								}
							?>
						</td>
                        
                        <td width="100" align="right">
                            <? 
                                if($z==1)
                                {
                                ?>
                                    <a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','grey_purchase','')"><? echo number_format($grey_purchase_qnty,2,'.',''); $tot_grey_purchase_qnty+=$grey_purchase_qnty; ?></a>
                                <?
                                }
                            ?>
                        </td>
                        <td width="100" align="right">
                        <? 
                            if($z==1) 
                            {
                            ?>
                                <a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','knit_trans','')"><? echo number_format($net_trans_knit,2,'.',''); $tot_net_trans_knit+=$net_trans_knit ?></a>
                            <?
                            }
                        ?>
                        </td>
                        <td width="100" align="right">
                        <?
                            if($z==1) 
                            {
                                echo number_format($grey_available,2,'.',''); $tot_grey_available+=$grey_available;
                            }
                        ?>
                        </td>
                        <td width="100" align="right">
                            <? 
                                if($z==1)
                                {
                                    echo number_format($grey_balance,2,'.','');  $tot_grey_balance+=$grey_balance;
                                }
                            ?>
                        </td>
                        <td width="100" align="right">
                            <? 
                                if($z==1)
                                {
                                ?>
                                    <a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','grey_issue','')"><? echo number_format($grey_fabric_issue,2,'.',''); $tot_grey_fabric_issue+=$grey_fabric_issue;?></a>
                                <?
                                }
                            ?>
                        </td>
                        <td width="100" align="right">
                            <? 
                                //if($z==1)
                               // {
                                ?>
                                    <a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','batch_qnty',<? echo $key;?>)"><? echo number_format($batch_qnty,2,'.',''); $tot_batch_qnty+=$batch_qnty;?></a>
                                <?
                                //}
                            ?>
                        </td>
                        
                       
                       
						<td width="100" align="center" bgcolor="#FF9BFF">
							<p>
								<? 
									if($key==0) 
									{
										echo "-";
									}
									else
									{ 
										echo $color_array[$key]; 
									}
								?>
							</p>
						</td>
                        
                        <td width="100" align="right">
                            <? 
                                echo number_format($value,2,'.','');
                                $fin_fab_Requi_array[$row[csf('buyer_name')]]+=$value;
                                $tot_color_wise_req+=$value; 
                            ?>
                        </td>
						<? 
							$issue_to_cut_qnty=$finish_issue_qnty_arr[$row[csf('po_id')]][$key]-$finish_issue_rtn_qnty_arr[$row[csf('po_id')]][$key];
							$fab_recv_qnty=$finish_receive_qnty_arr[$row[csf('po_id')]][$key];
							$dye_qnty=$dye_qnty_arr[$row[csf('po_id')]][$key];

							$fab_purchase_qnty=$finish_purchase_qnty_arr[$row[csf('po_id')]][$key]-$finish_recv_rtn_qnty_arr[$row[csf('po_id')]][$key];

                        ?>
                        <td width="100" align="right">
                            <a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','dye_qnty','<? echo $key; ?>')"><? echo number_format($dye_qnty,2,'.',''); ?></a>
                            <?
                                $dye_qnty_array[$row[csf('buyer_name')]]+=$dye_qnty;
                                $tot_dye_qnty+=$dye_qnty;
								
                            ?>
                        </td>

                        <td width="100" align="right">
                            <a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','fabric_receive','<? echo $key; ?>')"><? echo number_format($fab_recv_qnty,2,'.',''); ?></a>
                            <?
                                $fin_fab_recei_array[$row[csf('buyer_name')]]+=$fab_recv_qnty;
                                $tot_fabric_recv+=$fab_recv_qnty;
                            ?>
                        </td>
                        <td width="100" align="right">
                            <a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','fabric_purchase','<? echo $key; ?>')"><? echo number_format(($fab_purchase_qnty),2,'.',''); ?></a>
                            <?
                                
                                $fin_fab_recei_array[$row[csf('buyer_name')]]+=$fab_purchase_qnty;
                                $tot_fabric_purchase+=($fab_purchase_qnty);
                            ?>
                        </td>
                        <td width="100" align="right">
                            <? 
                                $net_trans_finish=$trans_qnty_fin_arr[$row[csf('po_id')]][$key]['trans'];
                                $fin_fab_recei_array[$row[csf('buyer_name')]]+=$net_trans_finish;
                            ?>
                                <a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','finish_trans','<? echo $key; ?>')"><? echo number_format($net_trans_finish,2,'.','');  ?></a>
                            <?	
                                $tot_net_trans_finish_qnty+=$net_trans_finish; 
                            ?>
                        </td>
                        <td width="100" align="right">
                            <?
                                $fabric_available=$fab_recv_qnty+$fab_purchase_qnty+$net_trans_finish;
                                echo number_format($fabric_available,2,'.',''); 
                                $tot_fabric_available+=$fabric_available;
                            ?>
                        </td>
                        <td width="100" align="right">
                            <?
                                $fabric_balance=$value-($fab_recv_qnty+$fab_purchase_qnty+$net_trans_finish);
                                echo number_format($fabric_balance,2,'.',''); 
                                $fin_balance_array[$row[csf('buyer_name')]]+=$fabric_balance;
                                $tot_fabric_balance+=$fabric_balance;
                            ?>
                        </td>
                        <td width="100" align="right">
                            <a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','issue_to_cut','<? echo $key; ?>')"><? echo number_format($issue_to_cut_qnty,2,'.',''); ?></a>
                            <?
                                $issue_to_cut_array[$row[csf('buyer_name')]]+=$issue_to_cut_qnty;
                                $tot_issue_to_cut_qnty+=$issue_to_cut_qnty;
                            ?>
                        </td>
                        <td width="100" align="right">
                            <?
                                $fabric_left_over=$fabric_available-$issue_to_cut_qnty;
                                echo number_format($fabric_left_over,2,'.',''); 
                                $tot_fabric_left_over+=$fabric_left_over;
                            ?>
                        </td>



					</tr>
				<?	
				$z++;
				$k++;
				}
			}
			else
			{ 
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
					<td width="35"><? echo $i; ?></td>
                    <td width="100"><? echo $main_booking;  ?></td>
                    <td width="100"><? echo $sample_booking; ?></td>

                    <td width="100" align="right">
						<? 
							$d=1;
							foreach($mkt_required_array as $mkt_required_value)
							{
								if($d!=1){echo "<hr/>";}
								echo number_format($mkt_required_value,2,'.','');
								$tot_mkt_required_value+=$mkt_required_value;
							$d++;
							}
							
						?>
					</td>
					<td width="100" align="right" bgcolor="<? echo $discrepancy_td_color; ?>">
						<? 
							echo "<font color='$bgcolor' style='display:none'>".number_format($yarn_issued,2,'.','')."</font>\n";
							$d=1;
							foreach($yarn_desc_array as $yarn_desc)
							{
								if($d!=1){echo "<hr/>";}
								echo $yarn_iss_qnty=$yarn_iss_qnty_array[$yarn_desc];
								$d++;
							}
							
							if($d!=1)
							{
								echo "<hr/>";
							}
							$yarn_desc=join(",",$yarn_desc_array);
							$iss_qnty_not_req=$yarn_iss_qnty_array['not_req'];
							?>
							<a href="##" onClick="openmypage('<? echo $row[csf('po_id')]; ?>','yarn_issue_not','<? echo $yarn_desc; ?>','','','','','')"><? echo number_format($iss_qnty_not_req,2); $$tot_iss_qnty_not_req+=$iss_qnty_not_req; ?></a>
					</td>

					<td width="100" align="right">
						 <a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','yarn_trans','')"><? echo number_format($net_trans_yarn,2,'.','');  ?></a>
						<? 
							$tot_net_trans_yarn_qnty+=$net_trans_yarn;
						?>
					</td>
					<td width="100" align="right">
						<? 
							echo number_format($balance,2,'.','');
							$total_balance+=$balance;
						?>
					</td>
					<td width="100" align="right" bgcolor="<? echo $bgcolor_grey_td; ?>"> <? echo number_format($required_qnty,2,'.',''); $tot_required_qnty=+$required_qnty;?></td>
					<td width="100" align="right" bgcolor="<? echo $discrepancy_td_color; ?>"><a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','grey_receive','')"><? echo number_format($grey_recv_qnty,2,'.',''); $tot_grey_recv_qnty+=$grey_recv_qnty;?></a></td>
					 <td width="100" align="right"><a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','grey_purchase','')"><? echo number_format($grey_purchase_qnty,2,'.',''); $tot_grey_purchase_qnty+=$grey_purchase_qnty;?></a></td>
                                        
					<td width="100" align="right"><a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','grey_purchase','')"><? echo number_format($grey_del_store,2,'.',''); $tot_grey_del_store+=$grey_del_store;?></a></td>
					<td width="100" align="right"><a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','grey_purchase','')"><? echo number_format($grey_purchase_qnty,2,'.',''); $tot_grey_purchase_qnty+=$grey_purchase_qnty;?></a></td>
					<td width="100" align="right">
						<a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','knit_trans','')"><? echo number_format($net_trans_knit,2,'.',''); $totnet_trans_knit+=$net_trans_knit; ?></a>
						<? 
							$tot_net_trans_knit_qnty+=$net_trans_knit;
						?>
					</td>
					<td width="100" align="right"><? echo number_format($grey_balance,2,'.',''); ?></td>
					<td width="100" align="right"><a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','grey_issue','')"><? echo number_format($grey_fabric_issue,2,'.',''); $tot_grey_fabric_issue+=$grey_fabric_issue; ?></a>
					</td>
					
                    <td width="100">&nbsp;</td>
					<td width="100" align="right"><a  href="##" onClick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','batch_qnty','')"><?  //echo number_format($batch_color_qnty,2,'.',''); ?></a></td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
				</tr>
				<?	
					$tot_batch_qnty_excel+=$batch_qnty;
			$k++;
			}
		$i++;	
		}
	}// end main query  
				
?>
</tbody>
<tfoot>
    <th width="235" colspan="3">Total</th>
    
    <th width="100"><? echo number_format($tot_mkt_required_value,2,'.','');?></th>
    <th width="100"><? echo number_format($tot_yarn_iss_qnty,2,'.','');?></th>
    <th width="100"><? echo number_format($tot_net_trans_yarn_qnty,2,'.','');?> </th>
    <th width="100"><? echo number_format($total_balance,2,'.','');?></th>
    
    <th width="100"><? echo number_format($tot_required_qnty,2,'.','');?></th>
    <th width="100"><? echo number_format($tot_grey_recv_qnty,2,'.','');?></th>
    <th width="100"><? echo number_format($tot_grey_purchase_qnty,2,'.','');?></th>
    <th width="100"><? echo number_format($tot_net_trans_knit,2,'.','');?></th>
    <th width="100"><? echo number_format($tot_grey_available,2,'.','');?></th>
    <th width="100"><? echo number_format($tot_grey_balance,2,'.','');?></th>
    <th width="100"><? echo number_format($tot_grey_fabric_issue,2,'.','');?></th>
    <th width="100"><? echo number_format($tot_batch_qnty,2,'.','');?> </th>
    
    <th width="100"></th>
    <th width="100"><? echo number_format($tot_color_wise_req,2,'.','');?></th>
    <th width="100"><? echo number_format($tot_dye_qnty,2,'.','');?> </th>
    <th width="100"><? echo number_format($tot_fabric_recv,2,'.','');?></th>
    <th width="100"><? echo number_format($tot_fabric_purchase,2,'.','');?></th>
    <th width="100"><? echo number_format($tot_net_trans_finish_qnty,2,'.','');?></th>
    <th width="100"><? echo number_format($tot_fabric_available,2,'.','');?></th>
    <th width="100"><? echo number_format($tot_fabric_balance,2,'.','');?></th>
    <th width="100"><? echo number_format($tot_issue_to_cut_qnty,2,'.','');?>  </th>
    <th width="100"><? echo number_format($tot_fabric_left_over,2,'.','');?></th>
    
</tr>

</tfoot>
</table>
<?
exit;	
}

if($action=="yarn_trans")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$po_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number"  );
?>
<script>

	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
	}	
	
</script>	
	<div style="width:675px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:670px; margin-left:7px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0">
				<thead>
                	<tr>
                    	<th colspan="6">Transfer In</th>
                    </tr>
                    <tr>
                    	<th width="40">SL</th>
                        <th width="115">Transfer Id</th>
                        <th width="80">Transfer Date</th>
                        <th width="100">From Order</th>
                        <th width="170">Item Description</th>
                        <th>Transfer Qnty</th>
                    </tr>
				</thead>
                <?
                $i=1; $total_trans_in_qnty=0;
				$sql="select a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, b.from_prod_id, sum(c.quantity) as transfer_qnty, d.product_name_details from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=1 and a.transfer_criteria=4 and c.trans_type=5 and c.entry_form=11 and c.po_breakdown_id in ($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, b.from_prod_id, d.product_name_details";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                    	<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
                        <td width="100"><p><? echo $po_arr[$row[csf('from_order_id')]]; ?></p></td>
                        <td width="170"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right"><? echo number_format($row[csf('transfer_qnty')],2); ?> </td>
                    </tr>
                <?
					$total_trans_in_qnty+=$row[csf('transfer_qnty')];
                $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_trans_in_qnty,2);?></td>
                </tr>
                <thead>
                	<tr>
                    	<th colspan="6">Transfer Out</th>
                    </tr>
                    <tr>
                    	<th width="40">SL</th>
                        <th width="115">Transfer Id</th>
                        <th width="80">Transfer Date</th>
                        <th width="100">To Order</th>
                        <th width="170">Item Description</th>
                        <th>Transfer Qnty</th>
                    </tr>
				</thead>
                <?
                $total_trans_out_qnty=0;
				$sql="select a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, sum(c.quantity) as transfer_qnty, d.product_name_details from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=1 and a.transfer_criteria=4 and c.trans_type=6 and c.entry_form=11 and c.po_breakdown_id in ($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, d.product_name_details";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                    	<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
                        <td width="100"><p><? echo $po_arr[$row[csf('to_order_id')]]; ?></p></td>
                        <td width="170"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right"><? echo number_format($row[csf('transfer_qnty')],2); ?> </td>
                    </tr>
                <?
					$total_trans_out_qnty+=$row[csf('transfer_qnty')];
                $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_trans_out_qnty,2); ?></td>
                </tr>
                <tfoot>
                	<th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Net Transfer</th>
                    <th><? echo number_format($total_trans_in_qnty-$total_trans_out_qnty,2);?></th>
                </tfoot>
            </table>	
		</div>
	</fieldset>  
<?
exit();
}


if($action=="grey_purchase")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
	<div style="width:1037px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:1037px; margin-left:2px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="11"><b>Grey Receive / Purchase Info</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="115">Receive Id</th>
                    <th width="95">Receive Basis</th>
                     <th width="160">Product Details</th>
                    <th width="110">Booking/PI/ Production No</th>
                    <th width="75">Production Date</th>
                    <th width="80">Inhouse Production</th>
                    <th width="80">Outside Production</th>
                    <th width="80">Production Qnty</th>
                    <th width="65">Challan No</th>
                    <th>Kniting Com.</th>
				</thead>
             </table>
             <div style="width:1037px; max-height:330px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0">
                    <?
                    $i=1; $total_receive_qnty=0; $receive_data_arr=array();
					$product_arr=return_library_array( "select id,product_name_details from product_details_master where item_category_id=13",'id','product_name_details');
					
                    $sql="select a.id, a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.prod_id, sum(c.quantity) as quantity from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.receive_basis<>9 and a.entry_form=22 and c.entry_form=22 and c.po_breakdown_id in($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.prod_id, a.id";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                        $total_receive_qnty+=$row[csf('quantity')];
						
						$knit_com='';
						if ($row[csf('knitting_source')]==1) $knit_com=$company_library[$row[csf('knitting_company')]]; 
						else if ($row[csf('knitting_source')]==3) $knit_com=$supplier_details[$row[csf('knitting_company')]];
						else $knit_com="&nbsp;";
						
						$recv_data_arr[$row[csf('id')]]['source']=$row[csf('knitting_source')];
						$recv_data_arr[$row[csf('id')]]['com']=$knit_com;
						$recv_data_arr[$row[csf('id')]]['basis']=$receive_basis_arr[$row[csf('receive_basis')]];
						$recv_data_arr[$row[csf('id')]]['booking']=$row[csf('booking_no')];
						$recv_data_arr[$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="115"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="95"><p><? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?></p></td>
                            <td width="160"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
                            <td width="110"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
                            <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td align="right" width="80">
								<? 
                                	if($row[csf('knitting_source')]!=3)
									{
										echo number_format($row[csf('quantity')],2,'.','');
										$total_receive_qnty_in+=$row[csf('quantity')];
									}
									else echo "&nbsp;";
                                ?>
                            </td>
                            <td align="right" width="80">
								<? 
                                	if($row[csf('knitting_source')]==3)
									{
										echo number_format($row[csf('quantity')],2,'.','');
										$total_receive_qnty_out+=$row[csf('quantity')];
									}
									else echo "&nbsp;";
                                ?>
                            </td>
                            <td align="right" width="80"><? echo number_format($row[csf('quantity')],2,'.',''); ?></td>
                            <td width="65"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                            <td><p><? echo $knit_com; ?>&nbsp;</p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="6" align="right">Total</th>
                        <th align="right"><? echo number_format($total_receive_qnty_in,2,'.',''); ?></th>
                        <th align="right"><? echo number_format($total_receive_qnty_out,2,'.',''); ?></th>
                        <th align="right"><? echo number_format($total_receive_qnty,2,'.',''); ?></th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset> 
    
<!-- Grey Received Return Info -->   
    
	<fieldset style="width:1037px; margin-top:10px; margin-left:2px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="11"><b>Grey Return Info</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="115">Return Id</th>
                    <th width="95">Return Basis</th>
                     <th width="160">Product Details</th>
                    <th width="110">Booking/PI/ Production No</th>
                    <th width="75">Return Date</th>
                    <th width="80">Inhouse Return</th>
                    <th width="80">Outside Return</th>
                    <th width="80">Return Qnty</th>
                    <th width="65">Challan No</th>
                    <th>Kniting Com.</th>
				</thead>
             </table>
             <div style="width:1037px; max-height:330px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0">
                    <?
                    $i=1; $total_return_qnty=0;
					$sql="select a.issue_number, a.issue_date, a.received_id, a.challan_no, b.prod_id, sum(c.quantity) as quantity from inv_issue_master a, inv_transaction b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and a.entry_form=45 and c.entry_form=45 and c.po_breakdown_id in($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.issue_number, a.issue_date, a.received_id, a.challan_no, b.prod_id";
				    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                        $total_return_qnty+=$row[csf('quantity')];
						
						$source=$recv_data_arr[$row[csf('received_id')]]['source'];
						$knit_com=$recv_data_arr[$row[csf('received_id')]]['com'];
						$receive_basis=$recv_data_arr[$row[csf('received_id')]]['basis'];
						$booking_no=$recv_data_arr[$row[csf('received_id')]]['booking'];
						$challan_no=$recv_data_arr[$row[csf('received_id')]]['challan_no'];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('rtr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="rtr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="115"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="95"><p><? echo $receive_basis; ?></p></td>
                            <td width="160"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
                            <td width="110"><p><? echo $booking_no; ?>&nbsp;</p></td>
                            <td width="75" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                            <td align="right" width="80">
								<? 
                                	if($source!=3)
									{
										echo number_format($row[csf('quantity')],2,'.','');
										$total_return_qnty_in+=$row[csf('quantity')];
									}
									else echo "&nbsp;";
                                ?>
                            </td>
                            <td align="right" width="80">
								<? 
                                	if($source==3)
									{
										echo number_format($row[csf('quantity')],2,'.','');
										$total_return_qnty_out+=$row[csf('quantity')];
									}
									else echo "&nbsp;";
                                ?>
                            </td>
                            <td align="right" width="80"><? echo number_format($row[csf('quantity')],2,'.',''); ?></td>
                            <td width="65"><p><? echo $challan_no; ?>&nbsp;</p></td>
                            <td><p><? echo $knit_com; ?>&nbsp;</p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                    	<tr>
                            <th colspan="6" align="right">Total</th>
                            <th align="right"><? echo number_format($total_return_qnty_in,2,'.',''); ?></th>
                            <th align="right"><? echo number_format($total_return_qnty_out,2,'.',''); ?></th>
                            <th align="right"><? echo number_format($total_return_qnty,2,'.',''); ?></th>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                        </tr>
                    	<tr>
                            <th colspan="6" align="right">Grand Total</th>
                            <th align="right"><? echo number_format($total_receive_qnty_in-$total_return_qnty_in,2,'.',''); ?></th>
                            <th align="right"><? echo number_format($total_receive_qnty_out-$total_return_qnty_out,2,'.',''); ?></th>
                            <th align="right"><? echo number_format($total_receive_qnty-$total_return_qnty,2,'.',''); ?></th>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                        </tr>
                    
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>    
<?
exit();
}

if($action=="knit_trans")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$po_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number"  );
?>
<script>

	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
	}	
	
</script>	
	<div style="width:675px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:670px; margin-left:7px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0">
				<thead>
                	<tr>
                    	<th colspan="6">Transfer In</th>
                    </tr>
                    <tr>
                    	<th width="40">SL</th>
                        <th width="115">Transfer Id</th>
                        <th width="80">Transfer Date</th>
                        <th width="100">From Order</th>
                        <th width="170">Item Description</th>
                        <th>Transfer Qnty</th>
                    </tr>
				</thead>
                <?
                $i=1; $total_trans_in_qnty=0;
				$sql="select a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, b.from_prod_id, sum(c.quantity) as transfer_qnty, d.product_name_details from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=13 and a.transfer_criteria=4 and c.trans_type=5 and c.entry_form=13 and c.po_breakdown_id in ($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, b.from_prod_id, d.product_name_details";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                    	<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
                        <td width="100"><p><? echo $po_arr[$row[csf('from_order_id')]]; ?></p></td>
                        <td width="170"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right"><? echo number_format($row[csf('transfer_qnty')],2); ?> </td>
                    </tr>
                <?
					$total_trans_in_qnty+=$row[csf('transfer_qnty')];
                $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_trans_in_qnty,2);?></td>
                </tr>
                <thead>
                	<tr>
                    	<th colspan="6">Transfer Out</th>
                    </tr>
                    <tr>
                    	<th width="40">SL</th>
                        <th width="115">Transfer Id</th>
                        <th width="80">Transfer Date</th>
                        <th width="100">To Order</th>
                        <th width="170">Item Description</th>
                        <th>Transfer Qnty</th>
                    </tr>
				</thead>
                <?
                $total_trans_out_qnty=0;
				$sql="select a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, d.product_name_details, sum(c.quantity) as transfer_qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=13 and a.transfer_criteria=4 and c.trans_type=6 and c.entry_form=13 and c.po_breakdown_id in ($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, d.product_name_details";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                    	<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
                        <td width="100"><p><? echo $po_arr[$row[csf('to_order_id')]]; ?></p></td>
                        <td width="170"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right"><? echo number_format($row[csf('transfer_qnty')],2); ?> </td>
                    </tr>
                <?
					$total_trans_out_qnty+=$row[csf('transfer_qnty')];
                $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_trans_out_qnty,2); ?></td>
                </tr>
                <tfoot>
                	<th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Net Transfer</th>
                    <th><? echo number_format($total_trans_in_qnty-$total_trans_out_qnty,2);?></th>
                </tfoot>
            </table>	
		</div>
	</fieldset>  
<?
exit();
}


if($action=="batch_qnty")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$color_array=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
	<div style="width:675px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:670px; margin-left:7px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="5"><b>Batch Info</b></th>
				</thead>
				<thead>
                	<th width="50">SL</th>
                    <th width="100">Batch Date</th>
                    <th width="170">Batch No</th>
                    <th width="150">Batch Color</th>
                    <th>Batch Qnty</th>
				</thead>
             </table>
             <div style="width:667px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0">
                    <?
                    $i=1; $total_batch_qnty=0;
                    $sql="select a.batch_no, a.batch_date, a.color_id, sum(b.batch_qnty) as quantity from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and b.po_id in($order_id) and a.status_active=1 and a.batch_against<>2 and a.entry_form=0 and a.color_id=$color and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.batch_date, a.color_id";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                        $total_batch_qnty+=$row[csf('quantity')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="50"><? echo $i; ?></td>
                            <td width="100" align="center"><? echo change_date_format($row[csf('batch_date')]); ?></td>
                            <td width="170"><p><? echo $row[csf('batch_no')]; ?></p></td>
                            <td width="150"><p><? echo $color_array[$row[csf('color_id')]]; ?></p></td>
                            <td align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="4" align="right">Total</th>
                        <th align="right"><? echo number_format($total_batch_qnty,2); ?></th>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>   
<?
exit();
}

if($action=="issue_to_cut")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$batch_details=return_library_array( "select id, batch_no from pro_batch_create_mst", "id", "batch_no");
	$product_details=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
	<div style="width:740px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:740px; margin-left:7px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="730" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="7"><b>Issue To Cutting Info</b></th>
				</thead>
				<thead>
                	<th width="40">SL</th>
                    <th width="110">Issue No</th>
                    <th width="80">Challan No</th>
                    <th width="80">Issue Date</th>
                    <th width="100">Batch No</th>
                    <th width="90">Issue Qnty</th>
                    <th>Fabric Description</th>
				</thead>
             </table>
             <div style="width:738px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0">
                    <?
                    $i=1; $total_issue_to_cut_qnty=0; $issue_data_arr=array();
                    $sql="select a.id,a.issue_number, a.issue_date, b.batch_id, b.prod_id, sum(c.quantity) as quantity, a.challan_no from inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=18 and c.entry_form=18 and c.po_breakdown_id in($order_id) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.issue_number, a.issue_date, a.challan_no, b.batch_id, b.prod_id,a.id";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                        $total_issue_to_cut_qnty+=$row[csf('quantity')];
						
						$issue_data_arr[$row[csf('id')]]=$batch_details[$row[csf('batch_id')]];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="40"><? echo $i; ?></td>
                            <td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="80"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                            <td width="100"><p><? echo $batch_details[$row[csf('batch_id')]]; ?></p></td>
                            <td width="90" align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
                            <td><p><? echo $product_details[$row[csf('prod_id')]]; ?></p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="5" align="right">Total</th>
                        <th align="right"><? echo number_format($total_issue_to_cut_qnty,2); ?></th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>
    
    <!-- Issue To Cutting return Info -->
    
    <fieldset style="width:740px; margin-left:7px; margin-top:10px;">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="730" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="7"><b>Issue Return Info</b></th>
				</thead>
				<thead>
                	<th width="40">SL</th>
                    <th width="110">Issue No</th>
                    <th width="80">Challan No</th>
                    <th width="80">Return Date</th>
                    <th width="100">Batch No</th>
                    <th width="90">Return Qnty</th>
                    <th>Fabric Description</th>
				</thead>
             </table>
             <div style="width:738px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0">
                    <?
                    $i=1; $total_issue_to_cut_ret_qnty=0;
					$sql="select a.id,a.recv_number, a.receive_date, a.challan_no, b.prod_id, a.issue_id, sum(c.quantity) as quantity from inv_receive_master a, inv_transaction b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and a.entry_form=52 and c.entry_form=52 and c.po_breakdown_id in($order_id) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.recv_number, a.receive_date, a.challan_no, b.prod_id,a.id,a.issue_id";
					$result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                        $total_issue_to_cut_ret_qnty+=$row[csf('quantity')];
						$batch=$issue_data_arr[$row[csf('issue_id')]];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('rtr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="rtr_<? echo $i;?>">
                            <td width="40"><? echo $i; ?></td>
                            <td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="80"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td width="100"><p><? echo $batch; ?></p></td>
                            <td width="90" align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
                            <td><p><? echo $product_details[$row[csf('prod_id')]]; ?></p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                      <tr>
                        <th colspan="5" align="right">Total</th>
                        <th align="right"><? echo number_format($total_issue_to_cut_ret_qnty,2); ?></th>
                        <th>&nbsp;</th>
                      </tr>
                        
                      <tr>
                        <th colspan="5" align="right">Grand Total</th>
                        <th align="right"><? echo number_format($total_issue_to_cut_qnty-$total_issue_to_cut_ret_qnty,2); ?></th>
                        <th>&nbsp;</th>
                      </tr>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>
       
<?
exit();
	
}




if($action=="fabric_purchase")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	
$batch_details=return_library_array( "select id, batch_no from pro_batch_create_mst", "id", "batch_no");
$product_details=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
	
	
	
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
	<div style="width:885px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:880px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="9"><b>Fabric Receive Info</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="120">System Id</th>
                    <th width="75">Rec. Date</th>
                    <th width="80">Rec. Basis</th>
                    <th width="90">Batch No</th>
                    <th width="90">Dyeing Source</th>
                    <th width="100">Dyeing Company</th>
                    <th width="90">Receive Qnty</th>
                    <th>Fabric Description</th>
				</thead>
             </table>
             <div style="width:877px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
                    <?
                    $i=1;
                    $total_fabric_recv_qnty=0; $dye_company=''; $recv_data_arr=array();
                    $sql="select a.id, a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id, sum(c.quantity) as quantity from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=37 and c.entry_form=37 and c.po_breakdown_id in($order_id) and c.color_id='$color' and a.receive_basis<>9 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                        if($row[csf('knitting_source')]==1) 
                        {
                            $dye_company=$company_library[$row[csf('knitting_company')]]; 
                        }
                        else if($row['knitting_source']==3) 
                        {
                            $dye_company=$supplier_details[$row[csf('knitting_company')]];
                        }
                        else
                            $dye_company="&nbsp;";
                    
                        $total_fabric_recv_qnty+=$row[csf('quantity')];
						
						//$recv_data_arr[$row[csf('id')]]['sor']=$knitting_source[$row[csf('knitting_source')]];
						//$recv_data_arr[$row[csf('id')]]['com']=$dye_company;
						//$recv_data_arr[$row[csf('id')]]['basis']=$receive_basis_arr[$row[csf('receive_basis')]];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td width="80"><? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?></td>
                            <td width="90"><p><? echo $batch_details[$row[csf('batch_id')]]; ?></p></td>
                            <td width="90"><? echo $knitting_source[$row[csf('knitting_source')]]; ?></td>
                            <td width="100"><p><? echo $dye_company; ?></p></td>
                            <td width="90" align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
                            <td><p><? echo $product_details[$row[csf('prod_id')]]; ?></p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="7" align="right">Total</th>
                        <th align="right"><? echo number_format($total_fabric_recv_qnty,2); ?></th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>
    
<!--Fabric Receive return info--> 
   
	<fieldset style="width:880px; margin-left:3px; margin-top:10px;">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="9"><b>Fabric Receive Return Info</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="120">System Id</th>
                    <th width="75">Ret. Date</th>
                    <th width="80">Ret. Basis</th>
                    <th width="90">Batch No</th>
                    <th width="90">Dyeing Source</th>
                    <th width="100">Dyeing Company</th>
                    <th width="90">Return Qnty</th>
                    <th>Fabric Description</th>
				</thead>
             </table>
             <div style="width:877px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
                    <? 
					$sql_prod="select a.id, a.receive_basis, a.knitting_source, a.knitting_company from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(7,37) and c.entry_form in(7,37) and c.po_breakdown_id in($order_id) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, a.receive_basis, a.knitting_source, a.knitting_company";
                    $resultProd=sql_select($sql_prod);
        			foreach($resultProd as $row)
                    {
						if($row[csf('knitting_source')]==1) 
                        {
                            $dye_company=$company_library[$row[csf('knitting_company')]]; 
                        }
                        else if($row['knitting_source']==3) 
                        {
                            $dye_company=$supplier_details[$row[csf('knitting_company')]];
                        }
                        else
                            $dye_company="&nbsp;";
							
						$recv_data_arr[$row[csf('id')]]['sor']=$knitting_source[$row[csf('knitting_source')]];
						$recv_data_arr[$row[csf('id')]]['com']=$dye_company;
						$recv_data_arr[$row[csf('id')]]['basis']=$receive_basis_arr[$row[csf('receive_basis')]];
					}
					
                    $i=1; $total_fabric_return_qnty=0;
                    $sql="select a.issue_number, a.issue_date, a.issue_basis, a.knit_dye_source, a.knit_dye_company, a.received_id, b.batch_id_from_fissuertn as batch_id, b.prod_id, sum(c.quantity) as quantity from inv_issue_master a, inv_transaction b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and a.entry_form=46 and c.entry_form=46 and c.po_breakdown_id in($order_id) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.issue_number, a.issue_date, a.issue_basis, a.received_id, a.knit_dye_source, a.knit_dye_company, b.batch_id_from_fissuertn, b.prod_id";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	

                        $source=$recv_data_arr[$row[csf('received_id')]]['sor'];
						$dye_company=$recv_data_arr[$row[csf('received_id')]]['com'];
						$basis=$recv_data_arr[$row[csf('received_id')]]['basis'];
						$batch=$batch_details[$row[csf('batch_id')]];
                    
                        $total_fabric_return_qnty+=$row[csf('quantity')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('rtr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="rtr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="75" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                            <td width="80"><? echo $basis; ?></td>
                            <td width="90"><p><? echo $batch; ?></p></td>
                            <td width="90"><? echo $source; ?></td>
                            <td width="100"><p><? echo $dye_company; ?></p></td>
                            <td width="90" align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
                            <td><p><? echo $product_details[$row[csf('prod_id')]]; ?></p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                    	<tr>
                            <th colspan="7" align="right">Total</th>
                            <th align="right"><? echo number_format($total_fabric_return_qnty,2); ?></th>
                            <th>&nbsp;</th>
                        </tr>
                    	<tr>
                            <th colspan="7" align="right">Grand Total</th>
                            <th align="right"><? echo number_format($total_fabric_recv_qnty-$total_fabric_return_qnty,2); ?></th>
                            <th>&nbsp;</th>
                        </tr>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>    
       
<?
exit();

	
}


if($action=="finish_trans")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$po_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number"  );
?>
<script>

	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
	}	
	
</script>	
	<div style="width:675px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:670px; margin-left:7px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0">
				<thead>
                	<tr>
                    	<th colspan="6">Transfer In</th>
                    </tr>
                    <tr>
                    	<th width="40">SL</th>
                        <th width="115">Transfer Id</th>
                        <th width="80">Transfer Date</th>
                        <th width="100">From Order</th>
                        <th width="170">Item Description</th>
                        <th>Transfer Qnty</th>
                    </tr>
				</thead>
                <?
                $i=1; $total_trans_in_qnty=0;
				$sql="select a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, b.from_prod_id, d.product_name_details, sum(c.quantity) as transfer_qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=2 and a.transfer_criteria=4 and c.trans_type=5 and c.entry_form=15 and c.po_breakdown_id in ($order_id) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, b.from_prod_id, d.product_name_details";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                    	<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
                        <td width="100"><p><? echo $po_arr[$row[csf('from_order_id')]]; ?></p></td>
                        <td width="170"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right"><? echo number_format($row[csf('transfer_qnty')],2); ?> </td>
                    </tr>
                <?
					$total_trans_in_qnty+=$row[csf('transfer_qnty')];
                $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_trans_in_qnty,2);?></td>
                </tr>
                <thead>
                	<tr>
                    	<th colspan="6">Transfer Out</th>
                    </tr>
                    <tr>
                    	<th width="40">SL</th>
                        <th width="115">Transfer Id</th>
                        <th width="80">Transfer Date</th>
                        <th width="100">To Order</th>
                        <th width="170">Item Description</th>
                        <th>Transfer Qnty</th>
                    </tr>
				</thead>
                <?
                $total_trans_out_qnty=0;
				$sql="select a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, d.product_name_details, sum(c.quantity) as transfer_qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=2 and a.transfer_criteria=4 and c.trans_type=6 and c.entry_form=15 and c.po_breakdown_id in ($order_id) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, d.product_name_details";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                    	<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
                        <td width="100"><p><? echo $po_arr[$row[csf('to_order_id')]]; ?></p></td>
                        <td width="170"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right"><? echo number_format($row[csf('transfer_qnty')],2); ?> </td>
                    </tr>
                <?
					$total_trans_out_qnty+=$row[csf('transfer_qnty')];
                $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_trans_out_qnty,2); ?></td>
                </tr>
                <tfoot>
                	<th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Net Transfer</th>
                    <th><? echo number_format($total_trans_in_qnty-$total_trans_out_qnty,2);?></th>
                </tfoot>
            </table>	
		</div>
	</fieldset>  
<?
exit();
}


if($action=="fabric_receive")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$batch_details=return_library_array( "select id, batch_no from pro_batch_create_mst", "id", "batch_no");
	$product_details=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
	<div style="width:885px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:880px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="9"><b>Fabric Receive Info</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="120">System Id</th>
                    <th width="75">Rec. Date</th>
                    <th width="80">Rec. Basis</th>
                    <th width="90">Batch No</th>
                    <th width="90">Dyeing Source</th>
                    <th width="100">Dyeing Company</th>
                    <th width="90">Receive Qnty</th>
                    <th>Fabric Description</th>
				</thead>
             </table>
             <div style="width:877px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
                    <?
                    $i=1;
                    $total_fabric_recv_qnty=0; $dye_company='';
                    $sql="select a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id, sum(c.quantity) as quantity from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=7 and c.entry_form=7 and c.po_breakdown_id in($order_id) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                        if($row[csf('knitting_source')]==1) 
                        {
                            $dye_company=$company_library[$row[csf('knitting_company')]]; 
                        }
                        else if($row['knitting_source']==3) 
                        {
                            $dye_company=$supplier_details[$row[csf('knitting_company')]];
                        }
                        else
                            $dye_company="&nbsp;";
                    
                        $total_fabric_recv_qnty+=$row[csf('quantity')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td width="80"><? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?></td>
                            <td width="90"><p><? echo $batch_details[$row[csf('batch_id')]]; ?></p></td>
                            <td width="90"><? echo $knitting_source[$row[csf('knitting_source')]]; ?></td>
                            <td width="100"><p><? echo $dye_company; ?></p></td>
                            <td width="90" align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
                            <td><p><? echo $product_details[$row[csf('prod_id')]]; ?></p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="7" align="right">Total</th>
                        <th align="right"><? echo number_format($total_fabric_recv_qnty,2); ?></th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>   
<?
exit();

}




if($action=="dye_qnty")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$machine_arr = return_library_array("select id, machine_no as machine_name from lib_machine_name","id","machine_name");
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
	<div style="width:885px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:880px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="10"><b>Dyeing Info</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="70">System Id</th>
                    <th width="80">Process End Date</th>
                    <th width="100">Batch No</th>
                    <th width="70">Dyeing Source</th>
                    <th width="120">Dyeing Company</th>
                    <th width="90">Receive Qnty</th>
                    <th width="190">Fabric Description</th>
                    <th>Machine Name</th>
				</thead>
             </table>
             <div style="width:877px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
                    <?
					$i=1; $total_dye_qnty=0; $dye_company='';
					$sql="select a.batch_no, b.item_description as febric_description, sum(b.batch_qnty) as quantity, c.id, c.company_id, c.process_end_date, c.machine_id from pro_batch_create_mst a,pro_batch_create_dtls b, pro_fab_subprocess c where a.id=b.mst_id and a.id=c.batch_id and a.color_id='$color' and c.load_unload_id=2 and c.entry_form=35 and b.po_id in($order_id) and a.batch_against<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, a.batch_no, b.item_description, c.id, c.company_id, c.process_end_date, c.machine_id";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
						$dye_company=$company_library[$row[csf('company_id')]]; 
                        $total_dye_qnty+=$row[csf('quantity')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="70"><p><? echo $row[csf('id')]; ?></p></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('process_end_date')]); ?>&nbsp;</td>
                            <td width="100"><p><? echo $row[csf('batch_no')];//$batch_details[$row[csf('batch_id')]]; ?></p></td>
                            <td width="70"><? echo "Inhouse";//echo $knitting_source[$row[csf('dyeing_source')]]; ?></td>
                            <td width="120"><p><? echo $dye_company; ?></p></td>
                            <td width="90" align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
                            <td width="190"><p><? echo $row[csf('febric_description')]; ?></p></td>
                            <td><p>&nbsp;<? echo $machine_arr[$row[csf('machine_id')]]; ?></p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="6" align="right">Total</th>
                        <th align="right"><? echo number_format($total_dye_qnty,2); ?></th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>  
<?
exit();
}




if($action=="grey_issue")
{

	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
	<div style="width:885px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:880px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
                <thead>
                	<tr>
                        <th colspan="9"><b>Grey Issue Info</b></th>
                    </tr>
                    <tr>
                        <th width="40">SL</th>
                        <th width="120">Issue Id</th>
                        <th width="100">Issue Purpose</th>
                        <th width="120">Issue To</th>
                        <th width="105">Booking No</th>
                        <th width="80">Batch No</th>
                        <th width="80">Issue Date</th>
                        <th width="100">Issue Qnty (In)</th>
                        <th>Issue Qnty (Out)</th>
                    </tr>
				</thead>
             </table>
             <div style="width:877px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
                    <?
                    $i=1; $issue_to='';
                    $sql="select a.id,a.issue_number, a.issue_date, a.issue_purpose, a.knit_dye_source, a.knit_dye_company, a.booking_no, a.batch_no, sum(c.quantity) as quantity from inv_issue_master a, inv_grey_fabric_issue_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=16 and c.entry_form=16 and c.po_breakdown_id in($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id,  a.issue_number, a.issue_date, a.issue_purpose, a.knit_dye_source, a.knit_dye_company, a.booking_no, a.batch_no";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    { 
						$issue_id_arr[]=$row[csf('id')];
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                        if($row[csf('knit_dye_source')]==1) 
                        {
                            $issue_to=$company_library[$row[csf('knit_dye_company')]]; 
                        }
                        else if($row['knit_dye_source']==3) 
                        {
                            $issue_to=$supplier_details[$row[csf('knit_dye_company')]];
                        }
                        else
                            $issue_to="&nbsp;";
                    
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="40"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="100"><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></td>
                            <td width="120"><p><? echo $issue_to; ?></p></td>
                            <td width="105"><? echo $row[csf('booking_no')]; ?>&nbsp;</td>
                            <td width="80"><p><? echo $batch_details[$row[csf('batch_no')]]; ?>&nbsp;</p></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                            <td width="100" align="right">
								<?
                                    if($row[csf('knit_dye_source')]!=3)
                                    {
                                        echo number_format($row[csf('quantity')],2);
                                        $total_issue_qnty+=$row[csf('quantity')];
                                    }
                                    else echo "&nbsp;";
                                ?>
                            </td>
                            <td align="right">
                                <?
                                    if($row[csf('knit_dye_source')]==3)
                                    {
                                        echo number_format($row[csf('quantity')],2);
                                        $total_issue_qnty_out+=$row[csf('quantity')];
                                    }
                                    else echo "&nbsp;";
                                ?>
                            </td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                    	<tr>
                            <th colspan="7" align="right">Total</th>
                            <th align="right"><? echo number_format($total_issue_qnty,2); ?></th>
                            <th align="right"><? echo number_format($total_issue_qnty_out,2); ?></th>
                        </tr>
                        <tr>
                            <th colspan="7" align="right">Grand Total</th>
                            <th align="right" colspan="2"><? echo number_format($total_issue_qnty+$total_issue_qnty_out,2); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>

<!-- Grey Issue Return Info -->
   
    <fieldset style="width:880px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
                <thead>
                	<tr>
                        <th colspan="9"><b>Grey Issue Return Info</b></th>
                    </tr>
                    <tr>
                        <th width="40">SL</th>
                        <th width="120">Return Id</th>
                        <th width="100">Return Purpose</th>
                        <th width="120">Issue To</th>
                        <th width="105">Booking No</th>
                        <th width="80">Batch No</th>
                        <th width="80">Return Date</th>
                        <th width="100">Return Qnty (In)</th>
                        <th>Return Qnty (Out)</th>
                    </tr>
				</thead>
             </table>
             <div style="width:877px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
                    <? 
                    $i=1; $issue_to='';
					$sql="select a.id, a.recv_number, a.receive_date, a.receive_purpose, a.knitting_source, a.knitting_company, a.booking_no, a.batch_id, sum(c.quantity) as quantity from inv_receive_master a, inv_transaction b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and a.entry_form=51 and c.entry_form=51 and c.po_breakdown_id in($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, a.recv_number, a.receive_date, a.receive_purpose, a.knitting_source, a.knitting_company, a.booking_no, a.batch_id";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                        if($row[csf('knitting_source')]==1) 
                        {
                            $issue_to=$company_library[$row[csf('knitting_company')]]; 
                        }
                        else if($row['knitting_source']==3) 
                        {
                            $issue_to=$supplier_details[$row[csf('knitting_company')]];
                        }
                        else
                            $issue_to="&nbsp;";
                    
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('rtr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="rtr_<? echo $i;?>">
                            <td width="40"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="100"><? echo $yarn_issue_purpose[$row[csf('receive_purpose')]]; ?></td>
                            <td width="120"><p><? echo $issue_to; ?></p></td>
                            <td width="105"><? echo $row[csf('booking_no')]; ?>&nbsp;</td>
                            <td width="80"><p><? echo $batch_details[$row[csf('batch_id')]]; ?>&nbsp;</p></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td width="100" align="right">
								<?
                                    if($row[csf('knitting_source')]!=3)
                                    {
                                        echo number_format($row[csf('quantity')],2);
                                        $total_return_qnty+=$row[csf('quantity')];
                                    }
                                    else echo "&nbsp;";
                                ?>
                            </td>
                            <td align="right">
                                <?
                                    if($row[csf('knitting_source')]==3)
                                    {
                                        echo number_format($row[csf('quantity')],2);
                                        $total_return_qnty_out+=$row[csf('quantity')];
                                    }
                                    else echo "&nbsp;";
                                ?>
                            </td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                    	<tr>
                            <th colspan="7" align="right">Total</th>
                            <th align="right"><? echo number_format($total_return_qnty,2); ?></th>
                            <th align="right"><? echo number_format($total_return_qnty_out,2); ?></th>
                        </tr>
                        <tr>
                            <th colspan="7" align="right">Grand Total</th>
                            <th align="right" colspan="2"><? echo number_format((($total_issue_qnty+$total_issue_qnty_out)-($total_return_qnty+$total_return_qnty_out)),2); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>
      
<?
exit();
}





if($action=="grey_receive")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name","id","machine_no");
?>
<script>

	var tableFilters = {
						   col_operation: {
						   id: ["value_receive_qnty_in","value_receive_qnty_out","value_receive_qnty_tot"],
						   col: [7,8,9],
						   operation: ["sum","sum","sum"],
						   write_method: ["innerHTML","innerHTML","innerHTML"]
						}
					}
	$(document).ready(function(e) {
		setFilterGrid('tbl_list_search',-1,tableFilters);
	});
		
	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		$('#tbl_list_search tr:first').hide(); 
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
		
		$('#tbl_list_search tr:first').show();
	}	
	
</script>	
	<div style="width:1037px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:1037px;">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="12"><b>Grey Receive Info</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="115">Receive Id</th>
                    <th width="95">Receive Basis</th>
                    <th width="110">Product Details</th>
                    <th width="100">Booking / Program No</th>
                    <th width="60">Machine No</th>
                    <th width="75">Production Date</th>
                    <th width="80">Inhouse Production</th>
                    <th width="80">Outside Production</th>
                    <th width="80">Production Qnty</th>
                    <th width="70">Challan No</th>
                    <th>Kniting Com.</th>
				</thead>
             </table>
             <div style="width:1038px; max-height:330px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0" id="tbl_list_search">
                    <?
                    $i=1; $total_receive_qnty=0;
					$product_arr=return_library_array( "select id,product_name_details from product_details_master where item_category_id=13",'id','product_name_details'); 
					
                    $sql="select a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.machine_no_id, b.prod_id, sum(c.quantity) as quantity from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.po_breakdown_id in($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.machine_no_id, b.prod_id";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                        $total_receive_qnty+=$row[csf('quantity')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="115"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="95"><p><? echo $receive_basis[$row[csf('receive_basis')]]; ?></p></td>
                            <td width="110"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
                            <td width="100"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
                            <td width="60"><p>&nbsp;<? echo $machine_arr[$row[csf('machine_no_id')]]; ?></p></td>
                            <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td align="right" width="80">
								<? 
                                	if($row[csf('knitting_source')]!=3)
									{
										echo number_format($row[csf('quantity')],2,'.','');
										$total_receive_qnty_in+=$row[csf('quantity')];
									}
									else echo "&nbsp;";
                                ?>
                            </td>
                            <td align="right" width="80">
								<? 
                                	if($row[csf('knitting_source')]==3)
									{
										echo number_format($row[csf('quantity')],2,'.','');
										$total_receive_qnty_out+=$row[csf('quantity')];
									}
									else echo "&nbsp;";
                                ?>
                            </td>
                            <td align="right" width="80"><? echo number_format($row[csf('quantity')],2,'.',''); ?></td>
                            <td width="70"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                            <td><p><? if ($row[csf('knitting_source')]==1) echo $company_library[$row[csf('knitting_company')]]; else if ($row[csf('knitting_source')]==3) echo $supplier_details[$row[csf('knitting_company')]]; ?></p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                 </table>
            </div>
            <table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0">  
                <tfoot>
                    <th width="30">&nbsp;</th>
                    <th width="115">&nbsp;</th>
                    <th width="95">&nbsp;</th>
                    <th width="110">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="75" align="right">Total</th>
                    <th width="80" align="right" id="value_receive_qnty_in"><? echo number_format($total_receive_qnty_in,2,'.',''); ?></th>
                    <th width="80" align="right" id="value_receive_qnty_out"><? echo number_format($total_receive_qnty_out,2,'.',''); ?></th>
                    <th width="80" align="right" id="value_receive_qnty_tot"><? echo number_format($total_receive_qnty,2,'.',''); ?></th>
                    <th width="70">&nbsp;</th>
                    <th>&nbsp;</th>
                </tfoot>
            </table>	
        </div>
	</fieldset>
  
	
	<?
exit();
}


if($action=="yarn_issue_not")
{

	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$brand_array=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name"  );
	$yarn_desc_array=explode(",",$yarn_count);
	//print_r($yarn_desc_array);
?>
<script>

	function print_window()
	{
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
	}	
	
</script>	
	<div style="width:970px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:970px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="960" cellpadding="0" cellspacing="0">
            	<thead>
					<th colspan="10"><b>Yarn Issue</b></th>
				</thead>
				<thead>
                    <th width="105">Issue Id</th>
                    <th width="90">Issue To</th>
                    <th width="105">Booking No</th>
                    <th width="70">Challan No</th>
                    <th width="75">Issue Date</th>
                    <th width="70">Brand</th>
                    <th width="60">Lot No</th>
                    <th width="180">Yarn Description</th>
                    <th width="90">Issue Qnty (In)</th>
                    <th>Issue Qnty (Out)</th>
				</thead>
                <?
				$i=1; $total_yarn_issue_qnty=0; $total_yarn_issue_qnty_out=0; $yarn_desc_array_for_return=array();
				$sql_yarn_iss="select b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type from order_wise_pro_details a, product_details_master b where a.prod_id=b.id and a.po_breakdown_id in ($order_id) and a.entry_form in(3,9) and b.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.issue_purpose!=2 group by b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type";
				$dataArrayIssue=sql_select($sql_yarn_iss);
				foreach($dataArrayIssue as $row_yarn_iss)
				{
					if($row_yarn_iss[csf('yarn_comp_percent2nd')]!=0)
					{
						$compostion_not_req=$composition[$row_yarn_iss[csf('yarn_comp_type1st')]]." ".$row_yarn_iss[csf('yarn_comp_percent1st')]." %"." ".$composition[$row_yarn_iss[csf('yarn_comp_type2nd')]]." ".$row_yarn_iss[csf('yarn_comp_percent2nd')]." %";
					}
					else
					{
						$compostion_not_req=$composition[$row_yarn_iss[csf('yarn_comp_type1st')]]." ".$row_yarn_iss[csf('yarn_comp_percent1st')]." %"." ".$composition[$row_yarn_iss[csf('yarn_comp_type2nd')]];
					}
			
					$desc=$yarn_count_details[$row_yarn_iss[csf('yarn_count_id')]]." ".$compostion_not_req." ".$yarn_type[$row_yarn_iss[csf('yarn_type')]];
					
					$yarn_desc_for_return=$row_yarn_iss[csf('yarn_count_id')]."__".$row_yarn_iss[csf('yarn_comp_type1st')]."__".$row_yarn_iss[csf('yarn_comp_percent1st')]."__".$row_yarn_iss[csf('yarn_comp_type2nd')]."__".$row_yarn_iss[csf('yarn_comp_percent2nd')]."__".$row_yarn_iss[csf('yarn_type')];
					
					$yarn_desc_array_for_return[$desc]=$yarn_desc_for_return;
					
					if(!in_array($desc,$yarn_desc_array))
					{
						$sql="select a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, sum(b.quantity) as issue_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id from inv_issue_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=2 and d.item_category=1 and d.id=b.trans_id and b.trans_type=2 and b.entry_form=3 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and c.yarn_count_id='".$row_yarn_iss[csf('yarn_count_id')]."' and c.yarn_comp_type1st='".$row_yarn_iss[csf('yarn_comp_type1st')]."' and c.yarn_comp_percent1st='".$row_yarn_iss[csf('yarn_comp_percent1st')]."' and c.yarn_comp_type2nd='".$row_yarn_iss[csf('yarn_comp_type2nd')]."' and c.yarn_comp_percent2nd='".$row_yarn_iss[csf('yarn_comp_percent2nd')]."' and c.yarn_type='".$row_yarn_iss[csf('yarn_type')]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2 group by a.id, c.id, a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, c.lot, c.yarn_type, c.product_name_details, d.brand_id";
						$result=sql_select($sql);
						foreach($result as $row)
						{
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";	
						
							if($row[csf('knit_dye_source')]==1) 
							{
								$issue_to=$company_library[$row[csf('knit_dye_company')]]; 
							}
							else if($row['knit_dye_source']==3) 
							{
								$issue_to=$supplier_details[$row[csf('knit_dye_company')]];
							}
							else
								$issue_to="&nbsp;";
								
							$yarn_issued=$row[csf('issue_qnty')];
							
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="105"><p><? echo $row[csf('issue_number')]; ?></p></td>
								<td width="90"><p><? echo $issue_to; ?></p></td>
								<td width="105"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
								<td width="70"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                                <td width="75" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                                <td width="70"><p><? echo $brand_array[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
								<td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
								<td width="180"><p><? echo $row[csf('product_name_details')]; ?></p></td>
								<td align="right" width="90">
									<? 
										if($row[csf('knit_dye_source')]!=3)
										{
											echo number_format($yarn_issued,2,'.','');
											$total_yarn_issue_qnty+=$yarn_issued;
										}
										else echo "&nbsp;";
									?>
								</td>
								<td align="right">
									<? 
										if($row[csf('knit_dye_source')]==3)
										{ 
											echo number_format($yarn_issued,2,'.',''); 
											$total_yarn_issue_qnty_out+=$yarn_issued;
										}
										else echo "&nbsp;";
									?>
								</td>
							</tr>
						<?
						$i++;
						}
					}
				}
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty,2,'.',''); ?></td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty_out,2,'.',''); ?></td>
                </tr>
                <tr style="font-weight:bold">
                    <td align="right" colspan="9">Issue Total</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty+$total_yarn_issue_qnty_out,2,'.',''); ?></td>
                </tr>
                <thead>
                    <th colspan="10"><b>Yarn Return</b></th>
                </thead>
                <thead>
                	<th width="105">Return Id</th>
                    <th width="90">Return From</th>
                    <th width="105">Booking No</th>
                    <th width="70">Challan No</th>
                    <th width="75">Return Date</th>
                    <th width="70">Brand</th>
                    <th width="60">Lot No</th>
                    <th width="180">Yarn Description</th>
                    <th width="90">Return Qnty (In)</th>
                    <th>Return Qnty (Out)</th>
               </thead>
                <?
				$total_yarn_return_qnty=0; $total_yarn_return_qnty_out=0;
				foreach($yarn_desc_array_for_return as $key=>$value)
				{
					if(!in_array($key,$yarn_desc_array))
					{
						$desc=explode("__",$value);
						$yarn_count=$desc[0];
						$yarn_comp_type1st=$desc[1];
						$yarn_comp_percent1st=$desc[2];
						$yarn_comp_type2nd=$desc[3];
						$yarn_comp_percent2nd=$desc[4];
						$yarn_type_id=$desc[5];
						
						$sql="select a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, sum(b.quantity) as returned_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=4 and d.item_category=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and c.yarn_count_id='$yarn_count' and c.yarn_comp_type1st='$yarn_comp_type1st' and c.yarn_comp_percent1st='$yarn_comp_percent1st' and c.yarn_comp_type2nd='$yarn_comp_type2nd' and c.yarn_comp_percent2nd='$yarn_comp_percent2nd' and c.yarn_type='$yarn_type_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2 group by a.id, c.id, a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, c.lot, c.yarn_type, c.product_name_details, d.brand_id";
						$result=sql_select($sql);
						foreach($result as $row)
						{
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";	
						
							if($row[csf('knitting_source')]==1) 
							{
								$return_from=$company_library[$row[csf('knitting_company')]]; 
							}
							else if($row['knitting_source']==3) 
							{
								$return_from=$supplier_details[$row[csf('knitting_company')]];
							}
							else
								$return_from="&nbsp;";
								
							$yarn_returned=$row[csf('returned_qnty')];
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="105"><p><? echo $row[csf('recv_number')]; ?></p></td>
								<td width="90"><p><? echo $return_from; ?></p></td>
								<td width="105"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
								<td width="70"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                                <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
								<td width="70"><p><? echo $brand_array[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
								<td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
								<td width="180"><p><? echo $row[csf('product_name_details')]; ?></p></td>
								<td align="right" width="90">
									<? 
										if($row[csf('knitting_source')]!=3)
										{
											echo number_format($yarn_returned,2,'.','');
											$total_yarn_return_qnty+=$yarn_returned;
										}
										else echo "&nbsp;";
									?>
								</td>
								<td align="right">
									<? 
										if($row[csf('knitting_source')]==3)
										{ 
											echo number_format($yarn_returned,2,'.',''); 
											$total_yarn_return_qnty_out+=$yarn_returned;
										}
										else echo "&nbsp;";
									?>
								</td>
							</tr>
						<?
						$i++;
						}
					}
				}
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Balance</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty-$total_yarn_return_qnty,2,'.',''); ?></td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty_out-$total_yarn_return_qnty_out,2,'.',''); ?></td>
                </tr>
                <tfoot>    
                    <tr>
                        <th align="right" colspan="9">Total Balance</th>
                        <th align="right"><? echo number_format(($total_yarn_issue_qnty+$total_yarn_issue_qnty_out)-($total_yarn_return_qnty+$total_yarn_return_qnty_out),2);?></th>
                    </tr>
                </tfoot>
            </table>	
		</div>
	</fieldset>  
<?
exit();
}


if($action=="order_wise_production")
{
	?>
    <script>
	var permission = '<? echo $permission; ?>';
	
	function fn_report_generated()
	{
		var type_id=$('#cbo_type').val();
		var company_name=$('#cbo_company_name').val();
		var po_no=$('#txt_order_no').val();
		
		
		var data="action=report_generate"+
				'&cbo_company_name='+"'"+company_name+"'"+
				'&txt_order_no='+"'"+po_no+"'"+
				'&cbo_type='+"'"+type_id+"'";
		//freeze_window(3);
		http.open("POST","../../../../production/reports/requires/order_wise_production_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
			 
			//show_msg('3');
			//release_freezing();
			$('#report_container2').html(reponse[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../../../'); 
		}
	}
	
	function openmypage_remark(po_break_down_id,item_id,country_id,action)
	{
		var garments_nature = $("#cbo_garments_nature").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', '../../../../production/reports/requires/order_wise_production_report_controller.php?po_break_down_id='+po_break_down_id+'&item_id='+item_id+'&country_id='+country_id+'&action='+action, 'Remarks Veiw', 'width=550px,height=450px,center=1,resize=0,scrolling=0','../../../');
	}
	
	function openmypage_order(po_break_down_id,company_name,item_id,country_id,action)
	{
		//var garments_nature = $("#cbo_garments_nature").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', '../../../../production/reports/requires/order_wise_production_report_controller.php?po_break_down_id='+po_break_down_id+'&company_name='+company_name+'&item_id='+item_id+'&country_id='+country_id+'&action='+action, 'Order Quantity', 'width=750px,height=350px,center=1,resize=0,scrolling=0','../../../');
	}
	
	function openmypage_country_ship_date(po_break_down_id,item_id,action,production_type,floor_id,dateOrLocWise,country_id)
	{
		if(production_type==2 || production_type==3)
			var popupWidth = "width=1050px,height=350px,";
		else if (production_type==10)
			var popupWidth = "width=550px,height=420px,";
		else
			var popupWidth = "width=750px,height=420px,";
		
		if (production_type==2)
		{
			var popup_caption="Embl. Issue Details";
		}
		else if (production_type==3)
		{
			var popup_caption="Embl. Rec. Details";
		}
		else
		{
			var popup_caption="Production Quantity";
		}
			
		emailwindow=dhtmlmodal.open('EmailBox','iframe','../../../../production/reports/requires/order_wise_production_report_controller.php?po_break_down_id='+po_break_down_id+'&item_id='+item_id+'&action='+action+'&production_type='+production_type+'&floor_id='+floor_id+'&dateOrLocWise='+dateOrLocWise+'&country_id='+country_id, popup_caption, popupWidth+'center=1,resize=0,scrolling=0','../../../');
	}

	function openmypage_rej(po_id,item_id,action,location_id,floor_id,reportType,country_id)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', '../../../../production/reports/requires/order_wise_production_report_controller.php?po_id='+po_id+'&item_id='+item_id+'&action='+action+'&location_id='+location_id+'&floor_id='+floor_id+'&reportType='+reportType+'&country_id='+country_id, 'Reject Quantity', 'width=510px,height=400px,center=1,resize=0,scrolling=0','../../../');
	}

	function openmypage(po_break_down_id,item_id,action,location_id,floor_id,dateOrLocWise,country_id)
	{
		if(action==2 || action==3)
			var popupWidth = "width=1050px,height=350px,";
		else if (action==10)
			var popupWidth = "width=550px,height=420px,";
		else
			var popupWidth = "width=800px,height=470px,";
		
		if (action==2)
		{
			var popup_caption="Embl. Issue Details";
		}
		else if (action==3)
		{
			var popup_caption="Embl. Rec. Details";
		}
		else
		{
			var popup_caption="Production Quantity";
		}
			
		emailwindow=dhtmlmodal.open('EmailBox','iframe','../../../../production/reports/requires/order_wise_production_report_controller.php?po_break_down_id='+po_break_down_id+'&item_id='+item_id+'&action='+action+'&location_id='+location_id+'&floor_id='+floor_id+'&dateOrLocWise='+dateOrLocWise+'&country_id='+country_id, popup_caption, popupWidth+'center=1,resize=0,scrolling=0','../../../');
	}
	
	function openmypage_rej(po_id,item_id,action,location_id,floor_id,reportType,country_id)
	{
		var company_name=$('#cbo_company_name').val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', '../../../../production/reports/requires/order_wise_production_report_controller.php?po_id='+po_id+'&item_id='+item_id+'&action='+action+'&location_id='+location_id+'&floor_id='+floor_id+'&reportType='+reportType+'&country_id='+country_id+'&company_name='+company_name, 'Reject Quantity', 'width=660px,height=400px,center=1,resize=0,scrolling=0','../../../');
	}
	
	function disable_order( val )
	{
		$('#txt_order_no').val('');
		$('#txt_file_no').val('');
		$('#txt_ref_no').val('');
		
		if(val==1)
		{
			$('#txt_file_no').removeAttr('disabled','disabled');
			$('#txt_ref_no').removeAttr('disabled','disabled');
		}
		else
		{
			$('#txt_file_no').attr('disabled','disabled');
			$('#txt_ref_no').attr('disabled','disabled');
		}
		
		if(val==5)
		{
			$('#Order_td').html('Style Ref.');
		}
		else
		{
			$('#Order_td').html('Order No');
		}
	}
	

	function progress_comment_popup(po_id,template_id,tna_process_type)
	{
		var data="action=update_tna_progress_comment"+
								'&po_id='+"'"+po_id+"'"+
								'&template_id='+"'"+template_id+"'"+
								'&tna_process_type='+"'"+tna_process_type+"'"+
								'&permission='+"'"+permission+"'";	
								
		http.open("POST","../../../../production/requires/order_wise_production_report_controller.php",true);
		
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_progress_comment_reponse;	
	}

	function generate_progress_comment_reponse()
	{
		if(http.readyState == 4) 
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
			d.close();
		}
	}


	function openmypage_image(page_link,title)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../../../')
		emailwindow.onclose=function()
		{
		}
	}

</script>

<style>
	hr
	{
		color: #676767;
		background-color: #676767;
		height: 1px;
	}
</style>
    <input type="hidden" name="cbo_company_name" id="cbo_company_name" value="<? echo $cbo_company_name; ?>"/>
    <input type="hidden" name="txt_order_no" id="txt_order_no" value="<? echo $po_number; ?>"/>
    <input type="hidden" name="cbo_type" id="cbo_type" value="<? echo '1'; ?>"/>
    <input type="button" id="report_btn" class="formbutton" style="width:130px" value="Show Order Wise" onClick="fn_report_generated(0)" /><br>
    
    <br>
    <div style="display:none" id="data_panel"></div>   
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    <?
	exit;
}


if($action=="trims_approval")
{
$item_arr=return_library_array( "select id, item_name from lib_item_group where status_active=1 and is_deleted=0",'id','item_name');
$ldp_app_data_arr=sql_select("select id, job_no_mst, po_break_down_id, accessories_type_id, target_approval_date, sent_to_supplier, submitted_to_buyer, approval_status, approval_status_date, supplier_name, accessories_comments, current_status from wo_po_trims_approval_info where job_no_mst='$job_no' and po_break_down_id='$po_id' and is_deleted=0 and status_active=1");

?>
<script>
function openmypage(po_id,item_name,job_no,book_num,trim_dtla_id,action)
{ //alert(book_num);
	var popup_width='900px';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'capacity_and_order_booking_status_ffl_controller.php?po_id='+po_id+'&item_name='+item_name+'&job_no='+job_no+'&book_num='+book_num+'&trim_dtla_id='+trim_dtla_id+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../../');
}

	function order_req_qty_popup(company,job_no,po_id,buyer,rate,item_group,boook_no,description,country_id,trim_dtla_id,start_date,end_date,action)
	{
		//alert(country_id);
		var popup_width='800px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'capacity_and_order_booking_status_ffl_controller.php?company='+company+'&job_no='+job_no+'&po_id='+po_id+'&buyer='+buyer+'&rate='+rate+'&item_group='+item_group+'&boook_no='+boook_no+'&description='+description+'&country_id_string='+country_id+'&trim_dtla_id='+trim_dtla_id+'&start_date='+start_date+'&end_date='+end_date+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../../');
	}
	
	
	function openmypage_inhouse(po_id,item_name,action)
	{
		var popup_width='900px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'capacity_and_order_booking_status_ffl_controller.php?po_id='+po_id+'&item_name='+item_name+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../../');
	}
	
	
	function openmypage_issue(po_id,item_name,action)
	{
		var popup_width='900px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'capacity_and_order_booking_status_ffl_controller.php?po_id='+po_id+'&item_name='+item_name+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../../');
	}
	
	

</script>

<fieldset>
<? if(count($ldp_app_data_arr)){ ?>
<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
    <thead>
        <th width="35">Sl</th>
        <th width="100">Item Name</th>
        <th width="90">Target Approval Date</th>
        <th width="90">Sent To Supplier</th>
        <th width="90">Sent To Buyer</th>
        <th width="90">Status</th>
        <th width="90">Status Date</th>
        <th>Comment</th>
    </thead>
</table>
<div style="max-height:280px; overflow-y:scroll;">
<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
<tbody>
<?
$i=1;
foreach($ldp_app_data_arr as $row):
$bgcolor=$i%2==0?'#E9F3FF':'#FFFFFF';
?>
    <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" style="cursor:pointer;">
        <td width="35" align="center"><? echo $i; ?></td>
        <td width="100" align="center"><? echo $item_arr[$row[csf('accessories_type_id')]]; ?></td>
        <td width="90" align="center"><? echo change_date_format($row[csf('target_approval_date')]); ?></td>
        <td width="90" align="center"><? echo change_date_format($row[csf('sent_to_supplier')]);?></td>
        <td width="90" align="center"><? echo change_date_format($row[csf('submitted_to_buyer')]);?></td>
        <td width="90" align="center"><? echo $approval_status[$row[csf('approval_status')]];?></td>
        <td width="90" align="center"><? echo change_date_format($row[csf('approval_status_date')]);?></td>
        <td><? echo $row[csf('accessories_comments')];?></td>
    </tr>
	<?
$i++;
endforeach;
 ?>

</tbody>
</table>
</div>
<? }else{ echo "<h1>Trims Approval Entry Not Found</h1>";} ?>


<!-- ******************************************************************************************** -->

<?
	$conversion_factor_array=array();
	$conversion_factor=sql_select("select id ,trim_uom,conversion_factor from  lib_item_group  ");
	foreach($conversion_factor as $row_f)
	{
	 $conversion_factor_array[$row_f[csf('id')]]['con_factor']=$row_f[csf('conversion_factor')];
	 $conversion_factor_array[$row_f[csf('id')]]['cons_uom']=$row_f[csf('trim_uom')];
	}
	$app_sql=sql_select("select job_no_mst,accessories_type_id,approval_status from wo_po_trims_approval_info");
	$app_status_arr=array();
	foreach($app_sql as $row)
	{
		$app_status_arr[$row[csf("job_no_mst")]][$row[csf("accessories_type_id")]]=$row[csf("approval_status")];
	}
	
	$sql_po_qty_country_wise_arr=array();
	$po_job_arr=array();
	$sql_po_qty_country_wise=sql_select("select  b.id,b.job_no_mst,c.country_id, sum(c.order_quantity/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst  and b.id=c.po_break_down_id   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond group by   b.id,b.job_no_mst,c.country_id order by b.id,b.job_no_mst,c.country_id");
	foreach( $sql_po_qty_country_wise as $sql_po_qty_country_wise_row)
	{
	$sql_po_qty_country_wise_arr[$sql_po_qty_country_wise_row[csf('id')]][$sql_po_qty_country_wise_row[csf('country_id')]]=$sql_po_qty_country_wise_row[csf('order_quantity_set')];
	$po_job_arr[$sql_po_qty_country_wise_row[csf('id')]]=$sql_po_qty_country_wise_row[csf('job_no_mst')];
	}
 
    
	$po_data_arr=array();
	$po_id_string="";
	$today=date("Y-m-d");
	$sql="select a.buyer_name,a.job_no,a.job_no_prefix_num,style_ref_no,b.file_no,b.grouping, b.id,b.po_number,a.order_uom,sum(distinct b.po_quantity) as po_quantity,a.total_set_qnty  ,sum(distinct b.po_quantity*a.total_set_qnty) as po_quantity_psc ,sum(c.order_quantity) as order_quantity ,sum(c.order_quantity/a.total_set_qnty) as order_quantity_set,  b.pub_shipment_date,
	d.costing_per,
	e.id as trim_dtla_id,
	e.trim_group,
	e.remark,
	e.description,
	e.brand_sup_ref,
	e.cons_uom,
	e.cons_dzn_gmts,
	e.rate,
	e.amount,
	e.apvl_req,
	e.nominated_supp,
	e.insert_date,
	f.cons,
	f.country_id,
	f.cons as cons_cal
	from 
	wo_po_details_master a, 
	wo_po_break_down b,
	wo_po_color_size_breakdown c
	left join 
	wo_pre_cost_trim_co_cons_dtls f 
	on
	c.job_no_mst=f.job_no and
	c.po_break_down_id=f.po_break_down_id and
	f.cons > 0
	join
	wo_pre_cost_trim_cost_dtls e
	on 
	f.job_no=e.job_no  and
	e.id=f.wo_pre_cost_trim_cost_dtls_id 
	$item_group_cond
	join 
	wo_pre_cost_mst d
	on
	e.job_no =d.job_no
	where 
	a.job_no=b.job_no_mst   and 
	a.job_no=c.job_no_mst   and 
	b.id=c.po_break_down_id and 
	a.is_deleted=0          and 
	a.status_active=1       and 
	b.is_deleted=0          and 
	b.status_active=1       and 
	c.is_deleted=0          and 
	c.status_active=1       and
	c.job_no_mst='$job_no'  and 
	b.id= $po_id
	
	group by a.buyer_name,a.job_no,a.job_no_prefix_num,style_ref_no,b.file_no,b.grouping, b.id,b.po_number,a.order_uom,a.total_set_qnty,b.pub_shipment_date,d.costing_per,
	e.id,
	e.trim_group,
	e.remark,
	e.description,
	e.brand_sup_ref,
	e.cons_uom,
	e.cons_dzn_gmts,
	e.rate,
	e.amount,
	e.apvl_req,
	e.nominated_supp,
	e.insert_date,
	f.cons,
	f.pcs,
	f.country_id
	order by b.id, e.trim_group
	";
	$sql_query=sql_select($sql);
	
				$tot_rows=count($sql_query);
				$i=1;
				foreach($sql_query as $row)
				{
					      
						   
						    $dzn_qnty=0;
							if($row[csf('costing_per')]==1)
							{
								$dzn_qnty=12;
							}
							else if($row[csf('costing_per')]==3)
							{
								$dzn_qnty=12*2;
							}
							else if($row[csf('costing_per')]==4)
							{
								$dzn_qnty=12*3;
							}
							else if($row[csf('costing_per')]==5)
							{
								$dzn_qnty=12*4;
							}
							else
							{
								$dzn_qnty=1;
							}
							
							
							 $po_qty=0;
							 if($row[csf('country_id')]==0)
							 {
								$po_qty=$row[csf('po_quantity')];
							 }
							 else
							 {
								$country_id= explode(",",$row[csf('country_id')]);
								for($cou=0;$cou<=count($country_id); $cou++)
								{
								$po_qty+=$sql_po_qty_country_wise_arr[$row[csf('id')]][$country_id[$cou]];
								}
							 }
							 
							 $req_qnty=($row[csf('cons_cal')]/$dzn_qnty)*$po_qty;
							 $req_value= $row[csf('rate')]*$req_qnty;
							 
							 $po_data_arr[$row[csf('id')]][job_no]=$row[csf('job_no')];
							 $po_data_arr[$row[csf('id')]][buyer_name]=$row[csf('buyer_name')];
							 $po_data_arr[$row[csf('id')]][job_no_prefix_num]=$row[csf('job_no_prefix_num')];
							 $po_data_arr[$row[csf('id')]][style_ref_no]=$row[csf('style_ref_no')];
							 
							 $po_data_arr[$row[csf('id')]][grouping]=$row[csf('grouping')];
							 $po_data_arr[$row[csf('id')]][file_no]=$row[csf('file_no')];
							 $po_data_arr[$row[csf('id')]][order_uom]=$row[csf('order_uom')];
							 $po_data_arr[$row[csf('id')]][po_id]=$row[csf('id')];
							 $po_data_arr[$row[csf('id')]][po_number]=$row[csf('po_number')];
							 $po_data_arr[$row[csf('id')]][order_quantity_set]=$row[csf('order_quantity_set')];
							 $po_data_arr[$row[csf('id')]][order_quantity]=$row[csf('order_quantity')];
							 $po_data_arr[$row[csf('id')]][pub_shipment_date]=change_date_format($row[csf('pub_shipment_date')]);
							 $po_id_string.=$row[csf('id')].",";
							 
							 $po_data_arr[$row[csf('id')]][trim_dtla_id][$row[csf('trim_dtla_id')]]=$row[csf('trim_dtla_id')];// for rowspan
							 $po_data_arr[$row[csf('id')]][trim_group][$row[csf('trim_group')]]=$row[csf('trim_group')];
							
							 
							 $po_data_arr[$row[csf('id')]][$row[csf('trim_group')]][$row[csf('trim_dtla_id')]]=$row[csf('trim_dtla_id')]; // for rowspannn
							 $po_data_arr[$row[csf('id')]][trim_group_dtls][$row[csf('trim_dtla_id')]]=$row[csf('trim_group')];
							 
							
							  $po_data_arr[$row[csf('id')]][remark][$row[csf('trim_dtla_id')]]=$row[csf('remark')];
							  
							 $po_data_arr[$row[csf('id')]][brand_sup_ref][$row[csf('trim_dtla_id')]]=$row[csf('brand_sup_ref')];
							 $po_data_arr[$row[csf('id')]][apvl_req][$row[csf('trim_dtla_id')]]=$row[csf('apvl_req')];
							 $po_data_arr[$row[csf('id')]][insert_date][$row[csf('trim_dtla_id')]]=$row[csf('insert_date')];
							 $po_data_arr[$row[csf('id')]][req_qnty][$row[csf('trim_dtla_id')]]+=$req_qnty;
							 $po_data_arr[$row[csf('id')]][req_value][$row[csf('trim_dtla_id')]]+=$req_value;
							 $po_data_arr[$row[csf('id')]][cons_uom][$row[csf('trim_dtla_id')]]=$row[csf('cons_uom')];
							 
							 $po_data_arr[$row[csf('id')]][trim_group_from][$row[csf('trim_dtla_id')]]="Pre_cost";
							 
							 $po_data_arr[$row[csf('id')]][rate][$row[csf('trim_dtla_id')]]=$row[csf('rate')];
							 $po_data_arr[$row[csf('id')]][description][$row[csf('trim_dtla_id')]]=$row[csf('description')];
							 $po_data_arr[$row[csf('id')]][country_id][$row[csf('trim_dtla_id')]]=$row[csf('country_id')];
							 
							// $style_data_arr[$row[csf('job_no')]][wo_qnty][$row[csf('trim_dtla_id')]]+=$wo_qnty;
							// $style_data_arr[$row[csf('job_no')]][amount][$row[csf('trim_dtla_id')]]+=$amount;
							// $style_data_arr[$row[csf('job_no')]][wo_date][$row[csf('trim_dtla_id')]]=$wo_date;
							// $style_data_arr[$row[csf('job_no')]][wo_qnty_trim_group][$row[csf('trim_group')]]+=$wo_qnty;
				}
				$po_id_string=rtrim($po_id_string,",");
				if($po_id_string=="")
				{
				echo "<strong style='color:red;font-size:30px'>No Data found</strong>";
				die;
				}
				
				if($db_type==2)
				{
				  $wo_sql_without_precost=sql_select("select min(a.booking_date) as booking_date ,b.job_no,LISTAGG(CAST(a.booking_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as booking_no,LISTAGG(CAST(a.supplier_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as supplier_id, b.po_break_down_id, b.trim_group,b.pre_cost_fabric_cost_dtls_id, sum(b.wo_qnty) as wo_qnty,sum(b.amount/b.exchange_rate) as amount,sum(b.rate) as rate from wo_booking_mst a, wo_booking_dtls b where a.item_category=4 and a.booking_no=b.booking_no  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.job_no='$job_no' and b.po_break_down_id=$po_id group by b.po_break_down_id,b.trim_group,b.job_no,b.pre_cost_fabric_cost_dtls_id");//and item_from_precost=2
				}
				else if($db_type==0)
				{
				  $wo_sql_without_precost=sql_select("select min(a.booking_date) as booking_date ,b.job_no,group_concat(a.booking_no) as booking_no,group_concat(a.supplier_id) as supplier_id, b.po_break_down_id, b.trim_group,b.pre_cost_fabric_cost_dtls_id,sum(b.wo_qnty) as wo_qnty,sum(b.amount/b.exchange_rate) as amount,sum(b.rate) as rate from wo_booking_mst a, wo_booking_dtls b where a.item_category=4 and a.booking_no=b.booking_no  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.job_no='$job_no' and b.po_break_down_id=$po_id group by b.po_break_down_id,b.trim_group,b.job_no,b.pre_cost_fabric_cost_dtls_id");//and item_from_precost=2
				}
				$style_data_arr1=array();
				foreach($wo_sql_without_precost as $wo_row_without_precost)
				{
					
					$conversion_factor_rate=$conversion_factor_array[$wo_row_without_precost[csf('trim_group')]]['con_factor'];
					$cons_uom=$conversion_factor_array[$wo_row_without_precost[csf('trim_group')]]['cons_uom'];
					$booking_no=$wo_row_without_precost[csf('booking_no')];
					$supplier_id=$wo_row_without_precost[csf('supplier_id')];
					$wo_qnty=$wo_row_without_precost[csf('wo_qnty')]*$conversion_factor_rate;
					$amount=$wo_row_without_precost[csf('amount')];
					$wo_date=$wo_row_without_precost[csf('booking_date')];
					
					if($wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')] =="" || $wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')] ==0)
					{
					    $trim_dtla_id=max($po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][trim_dtla_id])+1;
						$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][trim_dtla_id][$trim_dtla_id]=$trim_dtla_id;// for rowspan
					    $po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][trim_group][$wo_row_without_precost[csf('trim_group')]]=$wo_row_without_precost[csf('trim_group')];
				        $po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][$wo_row_without_precost[csf('trim_group')]][$trim_dtla_id]=$trim_dtla_id;// for rowspannn
						$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][trim_group_dtls][$trim_dtla_id]=$wo_row_without_precost[csf('trim_group')];
						$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][cons_uom][$trim_dtla_id]=$cons_uom;
						
						$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][trim_group_from][$trim_dtla_id]="Booking Without Pre_cost";
					}
					else
					{
						$trim_dtla_id=$wo_row_without_precost[csf('pre_cost_fabric_cost_dtls_id')];
						
					}
					
					$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][wo_qnty][$trim_dtla_id]+=$wo_qnty;
					$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][amount][$trim_dtla_id]+=$amount;
					$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][wo_date][$trim_dtla_id]=$wo_date;
					$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][wo_qnty_trim_group][$wo_row_without_precost[csf('trim_group')]]+=$wo_qnty;
					
					$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][booking_no][$trim_dtla_id]=$booking_no;
					$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][supplier_id][$trim_dtla_id]=$supplier_id;
					$po_data_arr[$wo_row_without_precost[csf('po_break_down_id')]][conversion_factor_rate][$trim_dtla_id]=$conversion_factor_rate;
					
				
					 
				}
				
				$receive_qty_data=sql_select("select b.po_breakdown_id, a.item_group_id,sum(b.quantity) as quantity   from  inv_receive_master c,product_details_master d,inv_trims_entry_dtls a , order_wise_pro_details b where a.mst_id=c.id and a.trans_id=b.trans_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_breakdown_id=$po_id  group by b.po_breakdown_id, a.item_group_id order by a.item_group_id ");
				 
				foreach($receive_qty_data as $row)
				{
					if($po_data_arr[$row[csf('po_breakdown_id')]][trim_group][$row[csf('item_group_id')]]=="" || $po_data_arr[$row[csf('po_breakdown_id')]][trim_group][$row[csf('item_group_id')]]==0)
					{
						$cons_uom=$conversion_factor_array[$row[csf('item_group_id')]]['cons_uom'];
						$trim_dtla_id=max($po_data_arr[$row[csf('po_breakdown_id')]][trim_dtla_id])+1;
						$po_data_arr[$row[csf('po_breakdown_id')]][trim_dtla_id][$trim_dtla_id]=$trim_dtla_id;// for rowspan
						$po_data_arr[$row[csf('po_breakdown_id')]][trim_group][$row[csf('item_group_id')]]=$row[csf('item_group_id')];
				        $po_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$trim_dtla_id]=$trim_dtla_id;// for rowspannn
						$po_data_arr[$row[csf('po_breakdown_id')]][trim_group_dtls][$trim_dtla_id]=$row[csf('item_group_id')];
						$po_data_arr[$row[csf('po_breakdown_id')]][cons_uom][$trim_dtla_id]=$cons_uom;
						
						$po_data_arr[$row[csf('po_breakdown_id')]][trim_group_from][$trim_dtla_id]="Trim Receive";

					}
				    $po_data_arr[$row[csf('po_breakdown_id')]][inhouse_qnty][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				}
				
				$receive_rtn_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id,sum(d.quantity) as quantity   from  inv_issue_master a,inv_transaction b, product_details_master c, order_wise_pro_details d,inv_receive_master e  where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and e.id=a.received_id   and b.transaction_type=3 and a.entry_form=49 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.po_breakdown_id=$po_id group by d.po_breakdown_id, c.item_group_id order by c.item_group_id");
				
				foreach($receive_rtn_qty_data as $row)
				{
				$po_data_arr[$row[csf('po_breakdown_id')]][receive_rtn_qty][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				}
				
				$issue_qty_data=sql_select("select b.po_breakdown_id, a.item_group_id,sum(b.quantity) as quantity  from  inv_issue_master d,product_details_master p,inv_trims_issue_dtls a , order_wise_pro_details b where a.mst_id=d.id and a.prod_id=p.id and a.trans_id=b.trans_id and b.trans_type=2 and b.entry_form=25 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_breakdown_id=$po_id group by b.po_breakdown_id, a.item_group_id");
				foreach($issue_qty_data as $row)
				{
				$po_data_arr[$row[csf('po_breakdown_id')]][issue_qty][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				}
				

?>
			<table class="rpt_table" width="1000" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<th width="30">SL</th>
					<th width="120">Trims Name</th>
					<th width="100">Req Qnty</th>
					<th width="100">Pre Costing Value</th>
					<th width="90">WO Qnty</th>
                    <th width="60">Trims UOM</th>
                    <th width="100">WO Value</th>
					<th width="90">In-House Qnty</th>
					<th width="90">Receive Balance</th>
					<th width="90">Issue to Prod.</th>
					<th>Left Over/Balance</th>
				</thead>
			</table>

			<div style="width:1020px; max-height:400px; overflow-y:scroll" id="scroll_body">
				<table class="rpt_table" width="1000" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">

<?

				$i=1;
				foreach($po_data_arr as $key=>$value)
				{ 
					$gg=1;
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";     
					foreach($value[trim_group] as $key_trim=>$value_trim)
				     { 
					 
					 ?>
					<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $gg; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
					 <?
					 foreach($value[$key_trim] as $key_trim1=>$value_trim1)
				     { 
					      
						  
					?>
                    			<td width="30" title="<? echo $po_qty; ?>"><p><? echo $gg; ?>&nbsp;</p></td>
								<td width="120" title="<? echo $value[trim_group_from][$key_trim1];  ?>">
									<p>
										<? 
										echo $item_arr[$value[trim_group_dtls][$key_trim1]]; 
										//echo $value[trim_group_dtls][$key_trim1];
										?>
									&nbsp;</p>
								</td>
								<td width="100" align="right">
                                <p>
                                <a href='#report_details' onClick="order_req_qty_popup('<? echo $company_name; ?>','<? echo $value[job_no]; ?>','<? echo $value[po_id]; ?>', '<? echo $value[buyer_name]; ?>','<? echo $value[rate][$key_trim1]; ?>','<? echo $value[trim_group_dtls][$key_trim1];?>' ,'<? echo $value[booking_no][$key_trim1] ;?>','<? echo $value[description][$key_trim1];?>','<? echo $value[country_id][$key_trim1]; ?>','<? echo $value[trim_dtla_id][$key_trim1]; ?>','<? echo $start_date ?>','<? echo $end_date ?>','order_req_qty_data');">
								<? 
								$req_qty=number_format($value[req_qnty][$key_trim1],2,'.','');
								echo $req_qty; 
								$summary_array[req_qnty][$key_trim]+=$value[req_qnty][$key_trim1];
								?>
                                </a>
                                &nbsp;
                                </p>
                                </td>
                                
								<td width="100" align="right">
                                <p>
								<? 
								echo number_format($value[req_value][$key_trim1],2); 
								$total_pre_costing_value+=$value[req_value][$key_trim1];
								?>
                                &nbsp;
                                </p>
                                </td>
                                <?
							   // $conversion_factor_rate=$conversion_factor_array[$row[csf('trim_group')]]['con_factor'];
							    $wo_qnty=number_format($value[wo_qnty][$key_trim1],2,'.','');
								if($wo_qnty > $req_qty)
								{
									$color_wo="red";	
								}
								
								else if($wo_qnty < $req_qty )
								{
									$color_wo="yellow";		
								}
								
								else 
								{
								$color_wo="";	
								}
								$supplier_name_string="";
								$supplier_id_arr=array_unique(explode(',',$value[supplier_id][$key_trim1]));
								//print_r($supplier_id_arr);
								foreach($supplier_id_arr as $supplier_id_arr_key=>$supplier_id_arr_value)
								{
									$supplier_name_string.=$lib_supplier_arr[$supplier_id_arr_value].",";
								}
								
								$booking_no_arr=array_unique(explode(',',$value[booking_no][$key_trim1]));
								//$booking_no_arr_d=implode(',',$booking_no_arr);
								//print $order_id.'='.	$trim_id;// $wo_qty_array[$order_id][$trim_id]['booking_no'];
								$main_booking_no_large_data="";
								foreach($booking_no_arr as $booking_no1)
								{	
									//if($booking_no1>0)
									//{
									if($main_booking_no_large_data=="") $main_booking_no_large_data=$booking_no1; else $main_booking_no_large_data.=",".$booking_no1;
									//}
									//print($main_booking_no_large_data);
								}
								?>
								<td width="90" align="right" title="<? echo 'conversion_factor='.$value[conversion_factor_rate][$key_trim1];?>" bgcolor="<? echo $color_wo;?>"><p><a href='#report_details' onClick="openmypage('<? echo $value[po_id]; ?>','<? echo $value[trim_group_dtls][$key_trim1]; ?>','<? echo $value[job_no]; ?>','<? echo $main_booking_no_large_data;?>','<? echo $value[trim_dtla_id][$key_trim1];?>','booking_info');">
								<? 
								 echo number_format($value[wo_qnty][$key_trim1],2,'.','');
								?>
                                </a>&nbsp;</p></td>
                                <td width="60" align="center">
                                <p>
								<? 
								echo $unit_of_measurement[$value[cons_uom][$key_trim1]];
								?>&nbsp;</p></td>
                                <td width="100" align="right" title="<? echo number_format($value[rate][$key_trim1],2,'.',''); ?>">
                                <p>
								<?  
								echo number_format($value[amount][$key_trim1],2,'.',''); 
								$total_wo_value+=$value[amount][$key_trim1];
								?>
                                &nbsp;
                                </p>
                                </td>
                                
                                
                                <?
								
								$inhouse_qnty=$value[inhouse_qnty][$key_trim]-$value[receive_rtn_qty][$key_trim];
								$balance=$value[wo_qnty_trim_group][$key_trim]-$inhouse_qnty;
								$issue_qnty=$value[issue_qty][$key_trim];
								$left_overqty=$inhouse_qnty-$issue_qnty;
								
								$summary_array[inhouse_qnty][$key_trim]+=$inhouse_qnty;
								$summary_array[inhouse_qnty_bl][$key_trim]+=$balance;
								$summary_array[issue_qty][$key_trim]+=$issue_qnty;
								$summary_array[left_overqty][$key_trim]+=$left_overqty;
								?>
                                
                                <td width="90" align="right" title="<? echo "Inhouse-Qty: ".$value[inhouse_qnty][$key_trim]."\nReturn Qty: ".$value[receive_rtn_qty][$key_trim]; ?>" rowspan="<? echo $rowspannn; ?>"><p><a href='#report_details' onClick="openmypage_inhouse('<? echo $value[po_id]; ?>','<? echo $value[trim_group_dtls][$key_trim1]; ?>','booking_inhouse_info');"><? echo number_format($inhouse_qnty,2,'.',''); ?></a>&nbsp;</p></td>
								<td width="90" align="right" rowspan="<? echo $rowspannn; ?>"><p><? echo number_format($balance,2,'.',''); ?>&nbsp;</p></td>
								<td width="90" align="right" rowspan="<? echo $rowspannn; ?>"><p><a href='#report_details' onClick="openmypage_issue('<? echo $value[po_id]; ?>','<? echo $value[trim_group_dtls][$key_trim1]; ?>','booking_issue_info');"><? echo number_format($issue_qnty,2,'.',''); ?></a>&nbsp;</p></td>
								<td align="right" rowspan="<? echo $rowspannn; ?>"><p><? echo number_format($left_overqty,2,'.',''); ?>&nbsp;</p></td>
                                
							 </tr>
							
					<?
						
						$gg++;
			    }// end  foreach($value[$key_trim] as $key_trim1=>$value_trim1)
				}
				?>
                <?
				$i++;
				}
?>                
                
                </table>
            </div>

</fieldset>
<?
exit;	
}

if($action=="booking_info")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<!--<div style="width:880px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
	<fieldset style="width:870px; margin-left:3px">
		<div id="scroll_body" align="center">
        <table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
        <tr>
        <td align="center" colspan="8"><strong> WO  Summary</strong> </td>
         </tr>
        </table>
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="30">Sl</th>
                    <th width="100">Wo No</th>
                    <th width="75">Wo Date</th>
                     <th width="100">Country</th>
                     <th width="200">Item Description</th>
                    <th width="80">Wo Qty</th>
                    <th width="60">UOM</th>
                    <th width="100">Supplier</th>
				</thead>
                <tbody>
                <?
				
					
					$conversion_factor_array=array();
					$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
					$conversion_factor=sql_select("select id ,conversion_factor from  lib_item_group ");
					foreach($conversion_factor as $row_f)
					{
					$conversion_factor_array[$row_f[csf('id')]]['con_factor']=$row_f[csf('conversion_factor')];
					}
					
					$i=1;
					$country_arr_data=array();
					$sql_data=sql_select("select c.country_id,c.po_break_down_id,c.job_no_mst from wo_po_color_size_breakdown c  where c.po_break_down_id in($po_id) and c.status_active=1 and c.is_deleted=0 group by c.country_id,c.po_break_down_id,c.job_no_mst  ");
					foreach($sql_data as $row_c)
					{
					$country_arr_data[$row_c[csf('po_break_down_id')]][$row_c[csf('job_no_mst')]]['country']=$row_c[csf('country_id')];
					}
					
					
						
					$item_description_arr=array();
					$wo_sql_trim=sql_select("select b.id,b.item_color,b.job_no, b.po_break_down_id, b.description,b.brand_supplier,b.item_size from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id=b.wo_trim_booking_dtls_id and a.pre_cost_fabric_cost_dtls_id=$trim_dtla_id and a.is_deleted=0 and a.status_active=1 and a.job_no=b.job_no  group by b.id,b.po_break_down_id,b.job_no,b.description,b.brand_supplier,b.item_size,b.item_color");
					foreach($wo_sql_trim as $row_trim)
					{
					$item_description_arr[$row_trim[csf('po_break_down_id')]][$row_trim[csf('job_no')]][$trim_dtla_id]['description']=$row_trim[csf('description')];
	
					} 
					
					$boking_cond="";
					$booking_no= explode(',',$book_num);
					foreach($booking_no as $book_row)
					{
						if($boking_cond=="") $boking_cond="and a.booking_no in('$book_row'"; else  $boking_cond .=",'$book_row'";
						
					} 
					if($boking_cond!="")$boking_cond.=")";
					  $wo_sql="select a.booking_no, a.booking_date, a.supplier_id,b.job_no,b.country_id_string, b.po_break_down_id,sum(b.wo_qnty) as wo_qnty,b.uom from wo_booking_mst a, wo_booking_dtls b 
					where  a.item_category=4 and a.booking_no=b.booking_no  and a.is_deleted=0 and a.status_active=1 
					and b.status_active=1 and b.is_deleted=0 and  b.job_no='$job_no' and b.trim_group=$item_name and b.po_break_down_id in($po_id) and b.pre_cost_fabric_cost_dtls_id=$trim_dtla_id $boking_cond group by  b.po_break_down_id,b.job_no,
					a.booking_no, a.booking_date, a.supplier_id,b.uom,b.country_id_string";
					$dtlsArray=sql_select($wo_sql);
					
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
							$description=$item_description_arr[$row[csf('po_break_down_id')]][$row[csf('job_no')]][$trim_dtla_id]['description'];
							$conversion_factor_rate=$conversion_factor_array[$item_name]['con_factor'];
							$country_arr_data=explode(',',$row[csf('country_id_string')]);
							$country_name_data="";
							foreach($country_arr_data as $country_row)
								{
									if($country_name_data=="") $country_name_data=$country_name_library[$country_row]; else $country_name_data.=",".$country_name_library[$country_row];
								}
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="100"><p><? echo $row[csf('booking_no')]; ?></p></td>
                            <td width="75"><p><? echo change_date_format($row[csf('booking_date')]); ?></p></td>
                             <td width="100"><p><? echo $country_name_data; ?></p></td>
                             <td width="200"><p><?  echo $description; ?></p></td>
                            <td width="80" align="right" title="<? echo 'conversion_factor='.$conversion_factor_rate; ?>"><p><? echo number_format($row[csf('wo_qnty')]*$conversion_factor_rate,2); ?></p></td>
                            <td width="60" align="center" ><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
                            <td width="100"><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></p></td>
                        </tr>
						<?
						$tot_qty+=$row[csf('wo_qnty')]*$conversion_factor_rate;
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                   		 <td colspan="5" align="right">Total</td>
                    	<td  align="right"><? echo number_format($tot_qty,2); ?></td>
                        <td align="right">&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}


if($action=="order_req_qty_data")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	?>
<!--	<div style="width:680px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
-->	<fieldset style="width:670px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="30">Sl</th>
                    <th width="80">Buyer Name</th>
                    <th width="100">Order No</th>
                     <th width="100">Item Description</th>
                     <th width="100">Country</th>
                    <th width="80">Req. Qty.</th>
                    <th width="">Req. Rate</th>
				</thead>
                <tbody>
                <? 
					$req_arr=array();
					$red_data=sql_select("select a.id,a.job_no,a.cons, a.po_break_down_id  from wo_pre_cost_trim_co_cons_dtls a , wo_pre_cost_trim_cost_dtls b where b.id=a.wo_pre_cost_trim_cost_dtls_id and b.trim_group=$item_group and a.job_no='$job_no' and a.po_break_down_id in($po_id) and b.id=$trim_dtla_id");
					foreach($red_data as $row_data)
					{
					$req_arr[$row_data[csf('po_break_down_id')]][$row_data[csf('job_no')]]['cons']=$row_data[csf('cons')];
					}
					$wo_sql_trim=sql_select("select b.id,b.job_no, b.po_break_down_id, b.description from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id=b.wo_trim_booking_dtls_id and a.is_deleted=0 and a.status_active=1 and a.job_no=b.job_no  group by b.id,b.po_break_down_id,b.job_no,b.description ");
					foreach($wo_sql_trim as $row_trim)
					{
					$item_description_arr[$row_trim[csf('po_break_down_id')]][$row_trim[csf('job_no')]]['job_no']=$row_trim[csf('job_no')];
					$item_description_arr[$row_trim[csf('po_break_down_id')]][$row_trim[csf('job_no')]]['description']=$row_trim[csf('description')];
						
	
					}
                       	$costing_per_id=return_field_value( "costing_per", "wo_pre_cost_mst","job_no ='$job_no'");
						if($start_date !="" && $end_date!="")
						{
						$date_cond="and c.country_ship_date between '$start_date' and '$end_date'";
						}
						else
						{
						$date_cond="";
						}

					   $dzn_qnty=0;
                        if(	$costing_per_id==1)
                        {
                            $dzn_qnty=12;
                        }
                        else if($costing_per_id==3)
                        {
                            $dzn_qnty=12*2;
                        }
                        else if($costing_per_id==4)
                        {
                            $dzn_qnty=12*3;
                        }
                        else if($costing_per_id==5)
                        {
                            $dzn_qnty=12*4;
                        }
                        else
                        {
                            $dzn_qnty=1;
                        }
						
					
					$i=1;
					
					if($country_id_string==0)
					{
						$contry_cond="";
					}
					else
					{
						$contry_cond="and c.country_id in(".$country_id_string.")";
					}
					
			      $sql="select  b.id,b.job_no_mst,c.country_id, sum(c.order_quantity/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst  and b.id=c.po_break_down_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  c.job_no_mst='$job_no' and c.po_break_down_id in($po_id) $contry_cond  $date_cond  group by   b.id,b.job_no_mst,c.country_id order by b.id,b.job_no_mst,c.country_id";
			 			
					$dtlsArray=sql_select($sql);						
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
							$cons=$req_arr[$row[csf('id')]][$job_no]['cons'];
							$req_qty=($row[csf('order_quantity_set')]/$dzn_qnty)*$cons;
							//$descript=$item_description_arr[$po_id][$job_no]['description'];
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="80" align="center"><p><? echo $buyer_short_name_library[$buyer]; ?></p></td>
                            <td width="100"><p><? echo $order_arr[$row[csf('id')]]; ?></p></td>
                            <td width="100"><p><? echo $description; ?></p></td>
                            <td width="100" align="center"><p><? echo  $country_name_library[$row[csf('country_id')]]; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($req_qty,2); ?></p></td>
                            <td width="" align="right"><p><? echo number_format($rate,4); ?></p></td>
                           
                        </tr>
						<?
						$tot_qty+=$req_qty;
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td  align="right"></td>
                    	<td colspan="4" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty,2); ?> </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}

if($action=="booking_inhouse_info")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<!--<div style="width:880px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
	<fieldset style="width:870px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="30">Sl</th>
                    <th width="80">Prod. ID</th>
                    <th width="100">Recv. ID</th>
                    <th width="100">Chalan No</th>
                    <th width="100">Recv. Date</th>
                    <th width="80">Item Description.</th>
                    <th width="80">Recv. Qty.</th>
                    <th width="80">Reject Qty.</th>
				</thead>
                <tbody>
                <?
					$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
					$i=1;
					
					$receive_rtn_data=array();
					$receive_rtn_qty_data=sql_select("select a.issue_number,a.issue_date,e.id,d.po_breakdown_id, c.item_group_id,sum(d.quantity) as quantity   from  inv_issue_master a,inv_transaction b, product_details_master c, order_wise_pro_details d,inv_receive_master e  where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and e.id=a.received_id   and b.transaction_type=3 and a.entry_form=49 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and d.po_breakdown_id in($po_id) and c.item_group_id='$item_name'  group by a.issue_number,a.issue_date,e.id,d.po_breakdown_id, c.item_group_id order by c.item_group_id");
					
					foreach($receive_rtn_qty_data as $row)
					{
					$receive_rtn_data[$row[csf('id')]][issue_number]=$row[csf('issue_number')];	
					$receive_rtn_data[$row[csf('id')]][issue_date]=$row[csf('issue_date')];	
					$receive_rtn_data[$row[csf('id')]][quantity]=$row[csf('quantity')];
					}
					
					$receive_qty_data="select a.id, c.po_breakdown_id,b.item_group_id,b.prod_id as prod_id,a.challan_no,b.item_description, a.recv_number, a.receive_date, SUM(c.quantity) as quantity, sum(reject_receive_qnty) as reject_receive_qnty
					from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c 
					where a.id=b.mst_id  and a.entry_form=24 and  a.item_category=4  and b.id=c.dtls_id and b.trans_id=c.trans_id and c.trans_type=1 and  c.po_breakdown_id in($po_id)  and b.item_group_id='$item_name' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by  c.po_breakdown_id,b.item_group_id,b.prod_id,a.id,b.item_description, a.recv_number,a.challan_no, a.receive_date";

					$dtlsArray=sql_select($receive_qty_data);
					
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
							
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="80"><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="100" align="center"><p><? echo  change_date_format($row[csf('receive_date')]); ?></p></td>
                            <td width="80" align="center"><p><? echo $row[csf('item_description')]; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                             <td width="80" align="right"><p><? echo number_format($row[csf('reject_receive_qnty')],2); ?></p></td>
                        </tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$tot_rej_qty+=$row[csf('reject_receive_qnty')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_qty,2); ?></td>
                         <td><? echo number_format($tot_rej_qty,2); ?></td>
                    </tr>
                </tfoot>
            </table>
            
            <table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="30">Sl</th>
                    <th width="80">Prod. ID</th>
                    <th width="100">Return. ID</th>
                    <th width="100">Chalan No</th>
                    <th width="100">Return Date</th>
                    <th width="80">Item Description.</th>
                    <th width="160">Return Qty.</th>
				</thead>
                <tbody>
                <?
					$dtlsArray=sql_select($receive_qty_data);
					
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
						if($receive_rtn_data[$row[csf('id')]][quantity]>0)
						{
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="80"><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $receive_rtn_data[$row[csf('id')]][issue_number]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="100" align="center"><p><? echo  change_date_format($receive_rtn_data[$row[csf('id')]][issue_date]); ?></p></td>
                            <td width="80" align="center"><p><? echo $row[csf('item_description')]; ?></p></td>
                            <td width="160" align="right"><p><? echo number_format($receive_rtn_data[$row[csf('id')]][quantity],2); ?></p></td>
                        </tr>
						<?
						$tot_rtn_qty+=$receive_rtn_data[$row[csf('id')]][quantity];
						$i++;
						}
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_rtn_qty,2); ?></td>
                    </tr>
                    <tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right">Balance</td>
                        <td><? echo number_format($tot_qty-$tot_rtn_qty,2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}

if($action=="booking_issue_info")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
    <fieldset style="width:870px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="30">Sl</th>
                    <th width="80">Prod. ID</th>
                    <th width="100">Issue. ID</th>
                     <th width="100">Chalan No</th>
                     <th width="100">Issue. Date</th>
                    <th width="80">Item Description.</th>
                    <th width="100">Issue. Qty.</th>
				</thead>
                <tbody>
                <?
					$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
					$i=1;
					//$wo_sql="select a.item_group_id,a.prod_id,b.recv_number,b.receive_date,a.item_description,sum(a.cons_qnty) as cons_qnty  from inv_receive_master b, inv_trims_entry_dtls a where b.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category=4 group by a.item_group_id,a.prod_id,b.recv_number,b.receive_date,a.item_description";
					
				 $mrr_sql=("select a.id, a.issue_number,a.challan_no,b.prod_id, a.issue_date,b.item_description,SUM(c.quantity) as quantity
					from  inv_issue_master a,inv_trims_issue_dtls b, order_wise_pro_details c,product_details_master p 
					where a.id=b.mst_id  and a.entry_form=25 and p.id=b.prod_id and b.id=c.dtls_id and b.trans_id=c.trans_id and c.trans_type=2 and a.is_deleted=0 and a.status_active=1 and
					b.status_active=1 and b.is_deleted=0 and  c.po_breakdown_id in($po_id) and p.item_group_id='$item_name' group by c.po_breakdown_id,p.item_group_id,b.item_description,a.issue_number,a.id,a.issue_date,b.prod_id,a.challan_no ");					
					
					$dtlsArray=sql_select($mrr_sql);
					
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
							
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="80" align="center"><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td width="100"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="100" align="center"><p><? echo  change_date_format($row[csf('issue_date')]); ?></p></td>
                            <td width="80" align="center"><p><? echo $row[csf('item_description')]; ?></p></td>
                            <td width="100" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                        </tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_qty,2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}

if($action=="generate_report_summary")
{
	if(str_replace("'","",$cbo_company_name)==0) $company_name=""; else $company_name=" and a.company_name=".str_replace("'","",$cbo_company_name)."";
	if(str_replace("'","",$cbo_buyer_name)==0) $buyer_name=""; else $buyer_name=" and a.buyer_name=".str_replace("'","",$cbo_buyer_name)."";
	if(trim($txt_date_from)!="") $start_date=$txt_date_from;
	if(trim($txt_date_to)!="") $end_date=$txt_date_to;
	if(str_replace("'","",$cbo_team_name)=="0") $team_leader=""; else $team_leader=" and a.team_leader=".str_replace("'","",$cbo_team_name)."";
	if(str_replace("'","",$cbo_team_member)=="0") $dealing_marchant=""; else $dealing_marchant=" and a.dealing_marchant=".str_replace("'","",$cbo_team_member)."";
	//echo $txt_date_from;die;
	$strar_month=str_replace("0","",date('m', strtotime($txt_date_from)));
	$end_month=str_replace("0","",date('m', strtotime($txt_date_to)));
	//echo change_date_format(".$txt_date_from.");die;
	$end_year=date('Y', strtotime($txt_date_to));
	
	$year_id=str_replace("'","",$cbo_year);
	if($db_type==0) if ($year_id!=0) $year_cond=" and year(a.insert_date)='$year_id'"; else $year_cond="";
	else if($db_type==2) if ($year_id!=0) $year_cond=" and to_char(a.insert_date,'YYYY')='$year_id'"; else $year_cond="";
	
	//echo $start_year."dd".$end_year;die;

	/*if ($start_date!="" && $end_date!="")
		{
			$date_cond="and b.pub_shipment_date between $start_date and  $end_date";
		}
		else	
		{
			$date_cond="";
		}*/
   
   if(str_replace("'","",$cbo_category_by)==1)
	{
		 if($db_type==2) $shipment_year="  extract(year from c.country_ship_date) as year";
		if($db_type==0) $shipment_year="  year(c.country_ship_date) as year";
		if($db_type==2) $shipment_month="  extract(month from c.country_ship_date) as month";
		if($db_type==0) $shipment_month="  month(c.country_ship_date) as month";
	}
	else
	{
		 if($db_type==2) $shipment_year="  extract(year from b.pub_shipment_date) as year";
		if($db_type==0) $shipment_year="  year(b.pub_shipment_date) as year";
		if($db_type==2) $shipment_month="  extract(month from b.pub_shipment_date) as month";
		if($db_type==0) $shipment_month="  month(b.pub_shipment_date) as month";
	}
	
   
	if (str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		if(str_replace("'","",$cbo_category_by)==1)
		{
			$date_cond="and c.country_ship_date between $txt_date_from and  $txt_date_to";
		}
		else if(str_replace("'","",$cbo_category_by)==2)
		{
			$date_cond="and b.pub_shipment_date between $txt_date_from and  $txt_date_to";	
		}
		else 
		{
			if($db_type==0){
						$date_cond="and  date(b.insert_date) between $txt_date_from and  $txt_date_to";
			}
			if($db_type==2){
						$date_cond="and  TRUNC(b.insert_date) between $txt_date_from and  $txt_date_to";
			}
	
		}
		
	}
	else	
	{
		$date_cond="";
	}
		//echo $date_cond.'###'.$cbo_category_by;
 	if(str_replace("'","",$cbo_category_by)==1)
	{ //b.po_quantity*a.total_set_qnty
		$data_array=sql_select("select  a.company_name, a.buyer_name,  b.is_confirmed,  sum(c.order_quantity) as po_quantity_pcs, $shipment_year,$shipment_month,  sum(c.order_total) as po_total_price from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id  $company_name $buyer_name $team_leader $dealing_marchant $date_cond $year_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by  a.company_name, a.buyer_name,  b.is_confirmed,c.country_ship_date order by c.country_ship_date ");
	}
	else
	{ 
	//sum(b.po_quantity) as po_quantity
	// sum(b.po_quantity*a.total_set_qnty) as po_quantity_pcs
	
		$data_array=sql_select("select  a.company_name, a.buyer_name,  b.is_confirmed,  sum(b.po_quantity*a.total_set_qnty)  as po_quantity_pcs, $shipment_year,$shipment_month,  sum(b.po_total_price) as po_total_price from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst  $company_name $buyer_name $team_leader $dealing_marchant $date_cond $year_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by  a.company_name, a.buyer_name,  b.is_confirmed,b.pub_shipment_date order by b.pub_shipment_date ");
		
	}
		
		$project_data_arr=array();
		$confrim_data_arr=array();
		$month_count_arr=array();
		$total_buyer_arr=array();
		$total_qty_arr=array();
		$total_confrim=array();
		$total_project=array();
		$grand_confirm_buyer=array();
		$grand_project_buyer=array();
		
		$buyer_total=array();
	    foreach($data_array as $row)
		  {
			 $total_buyer_arr[$row[csf('company_name')]][$row[csf('buyer_name')]]=$row[csf('buyer_name')];
			 $month_count_arr[$row[csf('company_name')]][$row[csf('year')]][$row[csf('month')]]['year']=$row[csf('year')];
			 $month_count_arr[$row[csf('company_name')]][$row[csf('year')]][$row[csf('month')]]['month']=$row[csf('month')];
			 $total_qty_arr[$row[csf('company_name')]][$row[csf('buyer_name')]][$row[csf('year')]][$row[csf('month')]]['poqty']+=$row[csf('po_quantity_pcs')];
			 $buyer_total[$row[csf('company_name')]][$row[csf('year')]][$row[csf('month')]]['poqty']+=$row[csf('po_quantity_pcs')];
			 $buyer_total[$row[csf('company_name')]][$row[csf('year')]][$row[csf('month')]]['amount']+=$row[csf('po_total_price')];
			 
		if($row[csf('is_confirmed')]==1)
		   {
			 $confrim_data_arr[$row[csf('company_name')]][$row[csf('buyer_name')]][$row[csf('year')]][$row[csf('month')]]['poqty']+=$row[csf('po_quantity_pcs')];
			 $confrim_data_arr[$row[csf('company_name')]][$row[csf('buyer_name')]][$row[csf('year')]][$row[csf('month')]]['amount']+=$row[csf('po_total_price')]; 
			  $total_confrim[$row[csf('company_name')]][$row[csf('year')]][$row[csf('month')]]['poqty']+=$row[csf('po_quantity_pcs')];
			  $total_confrim[$row[csf('company_name')]][$row[csf('year')]][$row[csf('month')]]['amount']+=$row[csf('po_total_price')]; 
			  $grand_confirm_buyer[$row[csf('company_name')]][$row[csf('buyer_name')]]['poqty']+=$row[csf('po_quantity_pcs')]; 
			  $grand_confirm_buyer[$row[csf('company_name')]][$row[csf('buyer_name')]]['amount']+=$row[csf('po_total_price')]; 
			  
			  $grand_total_confirm_po+=$row[csf('po_quantity_pcs')];
			  $grand_total_confirm_amount+=$row[csf('po_total_price')];      
		   }
		   if($row[csf('is_confirmed')]==2)
		   {
			 $project_data_arr[$row[csf('company_name')]][$row[csf('buyer_name')]][$row[csf('year')]][$row[csf('month')]]['poqty']+=$row[csf('po_quantity_pcs')];
			 $project_data_arr[$row[csf('company_name')]][$row[csf('buyer_name')]][$row[csf('year')]][$row[csf('month')]]['amount']+=$row[csf('po_total_price')];
			  $total_project[$row[csf('company_name')]][$row[csf('year')]][$row[csf('month')]]['poqty']+=$row[csf('po_quantity_pcs')];
			  $total_project[$row[csf('company_name')]][$row[csf('year')]][$row[csf('month')]]['amount']+=$row[csf('po_total_price')];
			  $grand_project_buyer[$row[csf('company_name')]][$row[csf('buyer_name')]]['poqty']+=$row[csf('po_quantity_pcs')]; 
			  $grand_project_buyer[$row[csf('company_name')]][$row[csf('buyer_name')]]['amount']+=$row[csf('po_total_price')];
			  $grand_total_poject_po+=$row[csf('po_quantity_pcs')];
			  $grand_total_poject_amount+=$row[csf('po_total_price')];  
			
		   }
			
			//$month_count_arr[$row[csf('year')]][$row[csf('month')]]=$row[csf('month')];	
				
		  }
	
	            $total_month=0;
	            foreach($month_count_arr as $company_id=>$compnay_val)
                      {
                         foreach($compnay_val as $year_id=>$year_val)
                          {
                              foreach($year_val as $month_id=>$month_val)
                               {
                                $total_month=$total_month+1;
                               }
                           }
                         
                       }
	
	$table_width=120+($total_month*530)+590;
	$col_span=2+($total_month*7)+8;
	//echo $col_span;die;
	
	ob_start();
	
	?>
  <fieldset style="width:<? echo $table_width+10;  ?>px;">  
  <table id="" class="" width="<? echo $table_width;  ?>" cellspacing="0" >
     <tr class="" style="border:none; ">
       <td colspan="<? echo $col_span; ?>" align="center" style="font-size:24px"> Order booking Summary</td>
     </tr>
      <tr class="" style="border:none;">
       <td colspan="<? echo $col_span; ?>" align="center" style="font-size:20px">Company Name:  <? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></td>
     </tr>
      <tr class="" style="border:none;">
       <td colspan="<? echo $col_span; ?>" align="center" style="font-size:20px">
       Date: 
	   <? 
	    if($db_type==0){     echo change_date_format(str_replace("'","",$txt_date_from))." To  ".change_date_format(str_replace("'","",$txt_date_to)); }
		if($db_type==2){ echo str_replace("'","",$txt_date_from)."  To  ".str_replace("'","",$txt_date_to); }
	    ?> 
        
        </td>
     </tr>
  </table>
    <table id="table_header" class="rpt_table" width="<? echo $table_width;  ?>" cellpadding="0" cellspacing="0" border="1" rules="all">
      <thead>
           <tr >
                <th width="40" rowspan="2">SL</th>
                <th width="80" rowspan="2">Buyer</th>
                  <?
                    foreach($month_count_arr as $company_id=>$compnay_val)
                      {
                         foreach($compnay_val as $year_id=>$year_val)
                          {
                              foreach($year_val as $month_id=>$month_val)
                               {
                                 ?>  
                                   <th width="530" colspan="7"><?   echo $months[$month_val['month']]."   ".$month_val['year']; ?></th>
                                
                                  <?
                               }
                           }
                         
                       }
                  ?>
                 <th width="590" colspan="8">Total</th>
            </tr>
            <tr>
            <?
                  foreach($month_count_arr as $company_id=>$compnay_val)
                     {
                        foreach($compnay_val as $year_id=>$year_val)
                        {
                            foreach($year_val as $month_id=>$month_val)
                            {
                               ?>  
                                    <th width="80" >Proj. Qty</th>
                                    <th width="80" >Proj. Amt </th>
                                    <th width="80" >Conf. Qty</th>
                                    <th width="80" >Conf. Amt</th>
                                    <th width="80" >Total Qty</th>
                                    <th width="80" >Total Amt</th>
                                    <th width="50" >%</th>
                                <?
                            }
                        }
                         
                     }
            ?> 
            
                    <th width="80" >Proj. Qty</th>
                    <th width="80" >Proj. Amt </th>
                    <th width="80" >Conf. Qty</th>
                    <th width="80" >Conf. Amt</th>
                    <th width="80" >Total Qty</th>
                    <th width="80" >Total Amt</th>
                    <th width="50" >%</th>
                    <th width="60" >Avg Price</th>
            </tr>
        </thead>
        <tbody>
              <?
			  $i=1;
			    foreach($total_buyer_arr as $com_id=>$com_val)
				{
				   foreach($com_val as $buy_id=>$buy_val)
						{	
					     if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						   ?>
					  
							 <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                                    <td> <? echo $i; ?>  </td>
                                    <td align="center"> <? echo $buyer_short_name_arr[$buy_val]; ?>  </td>
										<?
                                           foreach($month_count_arr as $company_id=>$compnay_val)
                                             {
                                                foreach($compnay_val as $year_id=>$year_val)
                                                {
                                                    foreach($year_val as $month_id=>$month_val)
                                                    {
													  $buyer_pc_total=$project_data_arr[$com_id][$buy_id][$year_id][$month_id]['poqty']+$confrim_data_arr[$com_id][$buy_id][$year_id][$month_id]['poqty'];
													  	  $buyer_pc_total_amount=$project_data_arr[$com_id][$buy_id][$year_id][$month_id]['amount']+$confrim_data_arr[$com_id][$buy_id][$year_id][$month_id]['amount'];
                                                       ?>  
                                                            <td width="80"  align="right"><? echo  number_format($project_data_arr[$com_id][$buy_id][$year_id][$month_id]['poqty'],0);  ?></td>
                                                            <td width="80"  align="right"><? echo  number_format($project_data_arr[$com_id][$buy_id][$year_id][$month_id]['amount'],2);  ?> </td>
                                                            <td width="80"  align="right"><? echo  number_format($confrim_data_arr[$com_id][$buy_id][$year_id][$month_id]['poqty'],0);  ?></td>
                                                            <td width="80"  align="right"><? echo  number_format($confrim_data_arr[$com_id][$buy_id][$year_id][$month_id]['amount'],2);  ?></td>
                                                            <td width="80"  align="right"><? echo  number_format($buyer_pc_total,0);  ?></td>
                                                            <td width="80"  align="right"><? echo  number_format($buyer_pc_total_amount,2);  ?></td>
                                                            <td width="50"  align="center"><? echo  number_format(($buyer_pc_total_amount/$buyer_total[$company_id][$year_id][$month_id]['amount'])*100,2);  ?></td>
                                                            
                                                       
                                                        
                                                        <?
                                                    }
                                                }
                                                 
                                             }
                                        
                                        ?>
                                        <td width="80" align="right"><? echo  number_format($grand_project_buyer[$company_id][$buy_id]['poqty'],0);  ?></td>
                                        <td width="80" align="right"><? echo  number_format($grand_project_buyer[$company_id][$buy_id]['amount'],2);  ?> </td>
                                        <td width="80" align="right"><? echo  number_format($grand_confirm_buyer[$company_id][$buy_id]['poqty'],0);  ?></td>
                                        <td width="80" align="right"><? echo  number_format($grand_confirm_buyer[$company_id][$buy_id]['amount'],2);  ?></td>
                                        <td width="80" align="right"><? echo  number_format($grand_project_buyer[$company_id][$buy_id]['poqty']+$grand_confirm_buyer[$company_id][$buy_id]['poqty'],0);  ?></td>
                                        <td width="80" align="right"><? echo  number_format($grand_project_buyer[$company_id][$buy_id]['amount']+$grand_confirm_buyer[$company_id][$buy_id]['amount'],2);  ?></td>
                                        <td width="50" align="center">

										<?
										
										$buyer_wise_grand_total=($grand_project_buyer[$company_id][$buy_id]['amount']+$grand_confirm_buyer[$company_id][$buy_id]['amount']);
										$grand_total_all_buyer=$grand_total_poject_amount+$grand_total_confirm_amount;
										 echo number_format(($buyer_wise_grand_total/$grand_total_all_buyer)*100,2);
										  ?>

                                        </td>
                                        <td width="60" align="right">
										<?
									  	$grand_total_price_all_buyer=$grand_project_buyer[$company_id][$buy_id]['poqty']+$grand_confirm_buyer[$company_id][$buy_id]['poqty'];
										$buyer_wise_grand_total_price=$grand_project_buyer[$company_id][$buy_id]['amount']+$grand_confirm_buyer[$company_id][$buy_id]['amount'];
										
										
										  echo number_format(($buyer_wise_grand_total_price/$grand_total_price_all_buyer),2);
										 
										 
										   ?>
                                        
                                        
                                        
                                        </td>
							 </tr>
							
							<?
							$i++;
						}
				}
				?>
        </tbody>
        <tfoot>
            <tr> 
                <th width="40" ></th>
                <th width="120" >Total</th>
                <?
				  foreach($month_count_arr as $company_id=>$compnay_val)
						 {
							foreach($compnay_val as $year_id=>$year_val)
							{
								foreach($year_val as $month_id=>$month_val)
								{
								   ?>  
										<th width="80" align="right" ><? echo  number_format($total_project[$company_id][$year_id][$month_id]['poqty'],0);  ?></th>
										<th width="80" align="right"> <? echo  number_format($total_project[$company_id][$year_id][$month_id]['amount'],2);  ?></th>
										<th width="80" align="right"><? echo  number_format($total_confrim[$company_id][$year_id][$month_id]['poqty'],0);  ?></th>
										<th width="80" align="right"><? echo  number_format($total_confrim[$company_id][$year_id][$month_id]['amount'],2);  ?></th>
										<th width="80" align="right"><? echo  number_format($buyer_total[$company_id][$year_id][$month_id]['poqty'],0);  ?></th>
										<th width="80" align="right"><? echo  number_format($buyer_total[$company_id][$year_id][$month_id]['amount'],2);  ?></th>
										<th width="50" align="right" ></th>
									<?
								}
							}
							 
						 }
				 ?> 
            
                    <th width="80" align="right"><? echo  number_format($grand_total_poject_po,0); ?></th>
                    <th width="80" align="right"><? echo  number_format($grand_total_poject_amount,2); ?></th>
                    <th width="80" align="right"><? echo  number_format($grand_total_confirm_po,0); ?></th>
                    <th width="80" align="right"><? echo  number_format($grand_total_confirm_amount,2); ?></th>
                    <th width="80" align="right"><? echo  number_format($grand_total_poject_po+$grand_total_confirm_po,0); ?></th>
                    <th width="80" align="right"><? echo  number_format($grand_total_poject_amount+$grand_total_confirm_amount,2); ?></th>
                    <th width="50" align="right"></th>
                    <th width="60" align="right"> <? echo  number_format(($grand_total_poject_amount+$grand_total_confirm_amount)/($grand_total_poject_po+$grand_total_confirm_po),2); ?></th>
            </tr>
        </tfoot>
    </table>
    
    </fieldset>
    <?
	foreach (glob("$user_id*.xls") as $filename) 
		{
			if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename=$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc,ob_get_contents());
		//$filename=$user_id."_".$name.".xls";
		echo "$total_data####$filename";
		exit(); 
	
}

if($action=="show_file") //Aziz
{
	echo load_html_head_contents("Booking File","../../../../", 1, 1, $unicode);
    extract($_REQUEST);

	if($type==2)
	{
	 $data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=2");
	?>
    <table>
        <tr>
        <?
        foreach ($data_array as $row)
        {
			//echo  $row[csf('image_location')].'azzz'; 
        ?>
        <td><a href="../../../../<? echo $row[csf('image_location')] ?>" target="_new"> 
        <img src="../../../../file_upload/blank_file.png" width="80" height="60"> </a>
        </td>
        <?
        }
        ?>
        </tr>
    </table>
    <?
	}
	exit();
}


if($action=="country_work_progress_report_details")
{
	echo load_html_head_contents("Set Entry","../../../../", 1, 1, $unicode);
    extract($_REQUEST);
	//echo "select a.id, a.company_name, a.job_no, a.buyer_name, a.style_ref_no, a.product_dept, a.product_category, c.item_mst_id, a.set_smv, a.packing, b.po_number, b.po_received_date, b.pub_shipment_date, c.country_ship_date, c.order_quantity, c.order_rate, c.order_total, c.plan_cut_qnty from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.job_no='$job_no' and b.id='$po_id' and c.country_id='$country_id' and a.is_deleted=0 and c.is_deleted=0";
	if($db_type==0) $select_prod_year="year(a.insert_date) as job_year"; else if($db_type==2) $select_prod_year="to_char(a.insert_date,'YYYY') as job_year";
	$mst_data_arr=sql_select("select a.id, a.company_name, a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, a.product_dept, $select_prod_year , a.product_category, c.item_number_id, a.set_smv, a.packing, b.po_number, b.po_received_date, b.pub_shipment_date, c.country_ship_date, c.order_quantity, c.order_rate, c.order_total, c.plan_cut_qnty 
	from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c 
	where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.job_no='$job_no' and b.id='$po_id' and c.country_id='$country_id' and a.is_deleted=0 and c.is_deleted=0");
	
	 $pre_cost_data_arr=sql_select("select machine_line, prod_line_hr, costing_date from wo_pre_cost_mst where job_no='$job_no' and is_deleted=0 and status_active=1");
	
	?>
	<script>
        $(function() {
            $( "#nevbar" ).tabs({
                beforeLoad: function( event, ui ) {
                    ui.jqXHR.fail(function() {
                    ui.panel.html(
                    "Couldn't load this tab. We'll try to fix this as soon as possible. " +
                    "If this wouldn't be a demo." );
                    });
                }
            });
        });
        
        function new_window()
        {
            //document.getElementById('approval_div').style.overflow="auto";
            //document.getElementById('approval_div').style.maxHeight="none";
            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
            '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('print_report_container').innerHTML+'</body</html>');
            d.close();
            //document.getElementById('approval_div').style.overflowY="scroll";
            //document.getElementById('approval_div').style.maxHeight="380px";
        }	
    </script>
    <fieldset style="width:99%;">
        <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
        	<thead>
            	<th colspan="12">Po Details for <? echo $country_name_arr[$country_id]; ?></th>
        	</thead>
            <tr bgcolor="#FFFFFF">
                <td width="90"><strong>Company</strong></td>
                <td width="120"><? echo $company_arr[$mst_data_arr[0][csf('company_name')]]; ?></td>
                <td width="90"><strong>Buyer</strong></td>
                <td width="150"><? echo $buyer_full_name_arr[$mst_data_arr[0][csf('buyer_name')]]; ?></td>
                <td width="60"><strong>Job No</strong></td>
                <td width="120"><? echo $job_no; ?></td>
                <td width="90"><strong>Style Ref</strong></td>
                <td width="90"><? echo $mst_data_arr[0][csf('style_ref_no')]; ?></td>
                <td width="90"><strong>Country Name</strong></td>
                <td width="90"><? $contry_full_name=return_field_value("country_name", "lib_country", "id=$country_id","country_name"); echo $contry_full_name; ?></td>
                <td width="80"><strong>Lead Time</strong></td>
                <td><? echo datediff('d',$mst_data_arr[0][csf('po_received_date')],$mst_data_arr[0][csf('country_ship_date')]); ?></td>
            </tr>
            <tr bgcolor="#E9F3FF">
                <td><strong>Order No</strong></td>
                <td><?php echo $mst_data_arr[0][csf('po_number')]; ?></td>
                <td><strong>Garments Item</strong></td>
                <td><?php echo $garments_item[$mst_data_arr[0][csf('item_number_id')]]; ?></td>
                <td><strong>SMV</strong></td>
                <td><?php echo $mst_data_arr[0][csf('set_smv')]; ?></td>
                <td><strong>Product Dept.</strong></td>
                <td><?php echo $pord_dept[$mst_data_arr[0][csf('product_dept')]]; ?></td>
                <td><strong>Packing Info</strong></td>
                <td><?php echo $packing[$mst_data_arr[0][csf('packing')]]; ?></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </table>
    </fieldset>   
	<style>
		#nevbar li {border:1px solid #000;} 
		#nevbar li a { padding: 0.5em .5em;color:#000;font-weight:bold;} 
    </style>   

    <fieldset style="width:99%;">
    <div id="nevbar">
        <ul>
            <li><a href="#order_details">Order Dtls</a></li>
            <li><a href="capacity_and_order_booking_status_ffl_controller.php?action=gmts_image&job_no=<? echo $job_no;?>">Gmts.Image</a></li>
            <li><a href="capacity_and_order_booking_status_ffl_controller.php?action=country_size_color_dtls&job_no=<? echo $job_no;?>&po_id=<? echo $po_id;?>&country_id=<? echo $country_id;?>">Size and Color Dtls</a></li>
            <li><a href="capacity_and_order_booking_status_ffl_controller.php?action=country_cost_info&txt_job_no=<? echo $job_no;?>&po_id=<? echo $po_id;?>&cbo_company_name=<? echo $mst_data_arr[0][csf('company_name')];?>&all_data=<? echo $mst_data_arr[0][csf('buyer_name')].'***'.$mst_data_arr[0][csf('style_ref_no')].'***'.$pre_cost_data_arr[0][csf('costing_date')];?>">Cost Info</a></li>
            <li><a href="capacity_and_order_booking_status_ffl_controller.php?action=country_fab_info&txt_job_no=<? echo $job_no;?>&po_id=<? echo $po_id;?>&cbo_company_name=<? echo $mst_data_arr[0][csf('company_name')];?>&all_data=<? echo $mst_data_arr[0][csf('buyer_name')].'***'.$mst_data_arr[0][csf('style_ref_no')].'***'.$pre_cost_data_arr[0][csf('costing_date')].'***'.$mst_data_arr[0][csf('po_number')];?>">Fabric Info 2</a></li>
            <li><a href="capacity_and_order_booking_status_ffl_controller.php?action=country_gmts_info&txt_job_no=<? echo $mst_data_arr[0][csf('job_no_prefix_num')];?>&po_id=<? echo $po_id;?>&cbo_company_name=<? echo $mst_data_arr[0][csf('company_name')];?>&cbo_country_name=<? echo $country_id;?>&job_year=<? echo $mst_data_arr[0][csf('job_year')];?>">Gmts. Info</a></li>
            <li><a href="capacity_and_order_booking_status_ffl_controller.php?action=sample_approval&job_no=<? echo $job_no;?>&po_id=<? echo $po_id;?>">Sample App.</a></li>
            <li><a href="capacity_and_order_booking_status_ffl_controller.php?action=labdip_approval&job_no=<? echo $job_no;?>&po_id=<? echo $po_id;?>">Labdip App.</a></li>
            <li><a href="capacity_and_order_booking_status_ffl_controller.php?action=country_trims_status&job_no=<? echo $mst_data_arr[0][csf('job_no_prefix_num')];?>&po_id=<? echo $mst_data_arr[0][csf('po_number')];?>&cbo_company_name=<? echo $mst_data_arr[0][csf('company_name')];?>&job_year=<? echo $mst_data_arr[0][csf('job_year')];?>">Trims Status</a></li>
            <li><a href="capacity_and_order_booking_status_ffl_controller.php?action=country_emblishment_app&job_no=<? echo $job_no;?>&po_id=<? echo $po_id;?>">Embel. App.</a></li>
        </ul>
        <div id="order_details">
			<?
            $count_lib_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
            
            /*$order_data_arr=sql_select("SELECT c.item_number_id, a.dealing_marchant, b.job_no_mst, b.po_number, sum(c.order_quantity*a.total_set_qnty) as po_quantity, c.country_ship_date, sum(c.order_rate) as order_rate, b.po_received_date, b.factory_received_date, sum(c.order_total) as order_value
            FROM wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
            WHERE b.id=$po_id and b.job_no_mst='$job_no' and c.country_id='$country_id' and a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 
			group by c.item_number_id, a.dealing_marchant, b.job_no_mst, b.po_number, c.country_ship_date, b.po_received_date, b.factory_received_date");*/
			
			$order_data_arr=sql_select("SELECT a.dealing_marchant, b.job_no_mst, b.po_number, b.po_received_date, b.factory_received_date, b.unit_price as order_rate, c.country_ship_date, sum(c.order_quantity) as po_quantity, sum(c.order_total) as order_value
            FROM wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
            WHERE b.id=$po_id and b.job_no_mst='$job_no' and c.country_id='$country_id' and a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 
			group by a.dealing_marchant, b.job_no_mst, b.po_number, b.po_received_date, b.factory_received_date, b.unit_price, c.country_ship_date");
            //echo $country_id;
           
            $pre_cost_dtls_data_arr=sql_select("select a.fabric_description, a.body_part_id, a.gsm_weight_yarn, a.avg_cons_yarn, a.avg_cons, a.fabric_source from wo_pre_cost_fabric_cost_dtls a where a.job_no='$job_no' order by a.id");
            foreach($pre_cost_dtls_data_arr as $rows)
            {
                $fb_des[]=$rows[csf('fabric_description')];
                if($rows[csf('fabric_source')]==1)$fb_con+=$rows[csf('avg_cons_yarn')];
                $yarn_req[]=$rows[csf('avg_cons')];	
                if($rows[csf('body_part_id')]==1)$gsm_top.=$rows[csf('gsm_weight_yarn')].',';
                if($rows[csf('body_part_id')]==20)$gsm_bottom.=$rows[csf('gsm_weight_yarn')].',';
            }
            
            $pre_cost_dtls_data_arr=sql_select("select b.count_id from  wo_pre_cost_fab_yarn_cost_dtls b where b.job_no='$job_no' order by b.id");
            foreach($pre_cost_dtls_data_arr as $rows)
            {
                $yarn_coun[]=$count_lib_arr[$rows[csf('count_id')]];	
            }
            $yarn_coun=array_unique($yarn_coun);
            $yarn_coun=implode(',',$yarn_coun);
            $fb_des=implode(',',$fb_des);
            $yarn_req=implode(',',$yarn_req);
            ?>
            <fieldset>	
                <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <tr bgcolor="#FFFFFF">
                        <td width="150">Country Po Qty</td>
                        <td width="200"><? echo number_format($order_data_arr[0][csf('po_quantity')]); ?></td>
                        <td width="150">Fabric Description</td>
                        <td><? echo $fb_des; ?></td>
                    </tr>
                    <tr bgcolor="#E9F3FF">  
                        <td>Country Unit Price</td><td><? echo number_format($order_data_arr[0][csf('order_rate')],3); ?></td>
                        <td>Fabric Consumption</td><td><? echo $fb_con;?></td>
                    </tr>
                    <tr bgcolor="#FFFFFF">  
                        <td>Country Po Value</td><td><? echo number_format($order_data_arr[0][csf('order_value')],2); ?></td>
                        <td>Yarn Required</td><td><? echo $yarn_req;?></td>
                    </tr>
                    <tr bgcolor="#E9F3FF">  
                        <td>Country Ship Date</td><td>
						<? 
						$day_of_weak=return_field_value("week_day", "week_of_year", "week_date='".$order_data_arr[0][csf('country_ship_date')]."'","week_day");
						echo change_date_format($order_data_arr[0][csf('country_ship_date')])."&nbsp;&nbsp;&nbsp;&nbsp;".$day_of_weak; 
						?>
                        </td>
                        <td>Count</td><td><? echo $yarn_coun;?></td>
                    </tr>
                    <tr bgcolor="#FFFFFF">  
                        <td>P.O Received Date</td><td><? echo change_date_format($order_data_arr[0][csf('po_received_date')]);?></td>
                        <td>GSM</td><td>
						<?
						if($gsm_top!="") echo 'Top: '.$gsm_top; if($gsm_bottom!="") echo 'Bottom: '.$gsm_bottom; 
						?>
                        </td>
                    </tr>
                    <tr bgcolor="#E9F3FF">  
                        <td>Factory Recv.Date</td><td><? echo change_date_format($order_data_arr[0][csf('factory_received_date')]);?></td>
                        <td>Machine/Line</td><td><? echo $pre_cost_data_arr[0][csf('machine_line')];?></td>
                    </tr>
                    <tr bgcolor="#FFFFFF">  
                        <td>Dealing Merchant</td><td><? echo $company_team_member_name_arr[$order_data_arr[0][csf('dealing_marchant')]];?></td>
                        <td>Prod/Line/Hrs</td><td><? echo $pre_cost_data_arr[0][csf('prod_line_hr')];?></td>
                    </tr>
                </table>
            </fieldset>
        </div>
    </div>
	</fieldset>
    <?
	exit();
}

if($action=="country_cost_info")
{
	$ex_data=explode('***',$all_data);
	?>
    <script>
	
	function generate_report(type)
	{
		var zero_val='';
		var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
		if (r==true)
		{
			zero_val="1";
		}
		else
		{
			zero_val="0";
		} 
		
		eval(get_submitted_variables('txt_job_no*cbo_company_name*cbo_buyer_name*txt_style_ref*txt_costing_date'));
		var job_no=$('#txt_job_no').val();
		var company_name=$('#cbo_company_name').val();
		var buyer_name=$('#cbo_buyer_name').val();
		var style_ref=$('#txt_style_ref').val();
		var costing_date=$('#txt_costing_date').val();
		var po_breack_down_id=$('#txt_po_breack_down_id').val();
		
		 var data="action="+type+"&zero_value="+zero_val+
                    '&txt_job_no='+"'"+job_no+"'"+
                    '&cbo_company_name='+"'"+company_name+"'"+
                    '&cbo_buyer_name='+"'"+buyer_name+"'"+
                    '&txt_style_ref='+"'"+style_ref+"'"+
                    '&txt_costing_date='+"'"+costing_date+"'"+
                    '&txt_po_breack_down_id='+"'"+po_breack_down_id+"'";
		
		http.open("POST","../../../../order/woven_order/requires/pre_cost_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_generate_report_reponse;
	}
	
	function fnc_generate_report_reponse()
	{
		if(http.readyState == 4) 
		{
			$('#data_panel').html( http.responseText );
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');//<link rel="stylesheet" href="css/style_common.css" type="text/css" />
			d.close();
		}
	}
	</script>
    <input type="hidden" name="txt_job_no" id="txt_job_no" value="<? echo $txt_job_no; ?>"/>
    <input type="hidden" name="cbo_company_name" id="cbo_company_name" value="<? echo $cbo_company_name; ?>"/>
    <input type="hidden" name="cbo_buyer_name" id="cbo_buyer_name" value="<? echo $ex_data[0]; ?>"/>
    <input type="hidden" name="txt_style_ref" id="txt_style_ref" value="<? echo $ex_data[1]; ?>"/>
    <input type="hidden" name="txt_costing_date" id="txt_costing_date" value="<? echo $ex_data[2]; ?>"/>
    <input type="hidden" name="txt_po_breack_down_id" id="txt_po_breack_down_id" value="<? echo $po_id; ?>"/>
    <input type="button" id="report_btn" class="formbutton" style="width:120px" value="Pre Cost Rpt2" onClick="generate_report('preCostRpt2')" /><br>
    <div style="color:#FF0000; font-size:14px; font-weight:bold; width:1000px">All Information Mentioned for full Order Qty. since Pre-Costing not Captured Country wise.</div>
    <br>
    <div style="display:none" id="data_panel"></div>
    <?
	exit();
}

if($action=="country_fab_info")
{
	$ex_data=explode('***',$all_data);
	?>
    <script>
	var permission = '<? echo $permission; ?>';
	
	function fn_report_generated(re_type)
	{
		var type_id=$('#cbo_type').val();
		//var job_no=$('#txt_job_no').val();
		var company_name=$('#cbo_company_name').val();
		var buyer_name=$('#cbo_buyer_name').val();
		//var po_no=$('#txt_po_no').val();
		var search_string=$('#txt_search_string').val();
		
		 var data="action="+re_type+
		 			'&cbo_company_name='+"'"+company_name+"'"+
					'&cbo_buyer_name='+"'"+buyer_name+"'"+
					'&cbo_type='+"'"+type_id+"'"+
					'&txt_search_string='+"'"+search_string+"'";
		
		//freeze_window(3);
		http.open("POST","../../../../production/reports/requires/fabric_receive_status_report2_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
		
	/*function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			$('#data_panel').html( http.responseText );
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title><link rel="stylesheet" href="../management_report/merchandising_report/requires/css/style_common.css" type="text/css" /></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');//
			d.close();
		}
	}*/
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");
			//alert(response[3]);
			$('#report_container2').html(response[0]);
			//document.getElementById('report_container').innerHTML='<a href="'+response[1]+'" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:155px"/></a>'; 
			//$('#report_container').append('&nbsp;&nbsp;&nbsp;<a href="'+response[2]+'" style="text-decoration:none"><input type="button" value="Convert To Excel Short" name="excel" id="excel" class="formbutton" style="width:155px"/></a>');
			var tot_rows=$('#table_body tr').length;
			if(tot_rows>1)
			{
				var type=$('#cbo_type').val();
				if(type==1)
				{
					var tableFilters = {
				  //col_10:'none',
				 // display_all_text: " ---Show All---",
							col_operation: {
							//id: ["value_tot_order_qnty","value_tot_mkt_required","value_tot_yarn_issue","value_tot_net_trans_yarn","value_tot_yarn_balance","value_tot_fabric_req","value_tot_grey_recv_qnty","value_tot_grey_prod_balance","value_tot_net_del_store","value_tot_grey_purchase_qnty","value_tot_net_trans_knit_qnty","value_tot_grey_rec_bal","value_tot_grey_issue","value_tot_batch","value_tot_dye_qnty","value_dye_qnty_balance","value_tot_fini_req","value_tot_fini_receive","value_tot_fabric_recv_balance","value_tot_fin_delivery_qty","value_tot_fabric_purchase","value_tot_trans_finish_qnty","value_tot_fabric_available","value_tot_issue_to_cut_qnty","value_tot_fabric_left_over"],
							id: ["value_tot_order_qnty","value_tot_mkt_required","value_tot_yarnAllocationQty","value_tot_yetTo_allocate","value_tot_yarn_issue","value_tot_net_trans_yarn","value_tot_yarn_balance","value_tot_fabric_req","value_tot_grey_recv_qnty","value_tot_grey_prod_balance","value_tot_net_del_store","value_tot_greyKnitFloor","value_tot_grey_production_qnty","value_tot_grey_purchase_qnty","value_tot_net_gray_return","value_tot_net_trans_knit_qnty","value_tot_grey_available","value_tot_grey_balance","value_tot_grey_issue","value_tot_batch","value_tot_dye_qnty","value_tot_dye_qnty_balance","value_tot_fini_req","value_tot_fini_receive","value_tot_fabric_recv_balance","value_tot_fin_delivery_qty","value_tot_finProdFloor","value_tot_fabric_production","value_tot_fabric_purchase","value_tot_fab_net_return","value_tot_trans_finish_qnty","value_tot_fabric_available","value_tot_fabric_rec_bal","value_tot_issue_to_cut_qnty","value_tot_yet_to_cut","value_tot_fabric_left_over"],
							col: [11,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,		38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54],
							//col: [10,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51],
						   operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
						   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
						}
					}
				}
				else
				{
					 var tableFilters = {
					 col_operation: {
							//id: ["value_tot_order_qnty","value_tot_mkt_required","value_tot_yarn_issue","value_tot_net_trans_yarn","value_tot_yarn_balance","value_tot_fabric_req","value_tot_grey_recv_qnty","value_tot_grey_prod_balance","value_tot_net_del_store","value_tot_grey_purchase_qnty","value_tot_net_trans_knit_qnty","value_tot_grey_rec_bal","value_tot_grey_issue","value_tot_batch","value_tot_dye_qnty","value_dye_qnty_balance","value_tot_fini_req","value_tot_fini_receive","value_tot_fabric_recv_balance","value_tot_fin_delivery_qty","value_tot_fabric_purchase","value_tot_trans_finish_qnty","value_tot_fabric_available","value_tot_issue_to_cut_qnty","value_tot_fabric_left_over"],
							id: ["value_tot_order_qnty","value_tot_mkt_required","value_tot_yarnAllocationQty","value_tot_yetTo_allocate","value_tot_yarn_issue","value_tot_net_trans_yarn","value_tot_yarn_balance","value_tot_fabric_req","value_tot_grey_recv_qnty","value_tot_grey_prod_balance","value_tot_net_del_store","value_tot_greyKnitFloor","value_tot_grey_production_qnty","value_tot_grey_purchase_qnty","value_tot_net_gray_return","value_tot_net_trans_knit_qnty","value_tot_grey_available","value_tot_grey_balance","value_tot_grey_issue","value_tot_batch","value_tot_dye_qnty","value_tot_dye_qnty_balance","value_tot_fini_req","value_tot_fini_receive","value_tot_fabric_recv_balance","value_tot_fin_delivery_qty","value_tot_finProdFloor","value_tot_fabric_production","value_tot_fabric_purchase","value_tot_fab_net_return","value_tot_trans_finish_qnty","value_tot_fabric_available","value_tot_fabric_rec_bal","value_tot_issue_to_cut_qnty","value_tot_yet_to_cut","value_tot_fabric_left_over"],
							
							col: [11,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50],
						   //col: [10,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48],
						   operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
						   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
						}
					}
				}
				//setFilterGrid("table_body",-1,tableFilters);
			 }
			//append_report_checkbox('table_header_1',1);
			// $("input:checkbox").hide();
			show_msg('3');
			release_freezing();
		}
	}
	
	function new_window()
	{
		document.getElementById('company_id_td').style.visibility='visible';
		document.getElementById('date_td').style.visibility='visible';
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write(document.getElementById('buyer_summary').innerHTML);
		document.getElementById('company_id_td').style.visibility='hidden';
		document.getElementById('date_td').style.visibility='hidden';
		d.close();
	}
	
	function open_febric_receive_status_order_wise_popup(order_id,type,color)
	{
		var popup_width='';
		if(type=="fabric_receive" || type=="fabric_purchase" || type=="grey_issue" || type=="dye_qnty") 
		{
			popup_width='900px';
		}
		else if(type=="grey_receive" || type=="grey_purchase")
		{
			popup_width='1050px';	
		}
		else popup_width='760px';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', '../../../../production/reports/requires/fabric_receive_status_report2_controller.php?order_id='+order_id+'&action='+type+'&color='+color, 'Detail Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../../');
	}

function openmypage(order_id,type,yarn_count,yarn_comp_type1st,yarn_comp_percent1st,yarn_comp_type2nd,yarn_comp_percent2nd,yarn_type)
{
	var popup_width='';
	if(type=="yarn_issue_not") 
	{
		popup_width='1000px';
	}
	else
	{
		popup_width='890px';
	}
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', '../../../../production/reports/requires/fabric_receive_status_report2_controller.php?order_id='+order_id+'&action='+type+'&yarn_count='+yarn_count+'&yarn_comp_type1st='+yarn_comp_type1st+'&yarn_comp_percent1st='+yarn_comp_percent1st+'&yarn_comp_type2nd='+yarn_comp_type2nd+'&yarn_comp_percent2nd='+yarn_comp_percent2nd+'&yarn_type_id='+yarn_type, 'Detail Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../');
}

function generate_worder_report(type,booking_no,company_id,order_id,fabric_nature,fabric_source,job_no,approved)
{
	var data="action=show_fabric_booking_report"+
				'&txt_booking_no='+"'"+booking_no+"'"+
				'&cbo_company_name='+"'"+company_id+"'"+
				'&txt_order_no_id='+"'"+order_id+"'"+
				'&cbo_fabric_natu='+"'"+fabric_nature+"'"+
				'&cbo_fabric_source='+"'"+fabric_source+"'"+
				'&id_approved_id='+"'"+approved+"'"+
				'&txt_job_no='+"'"+job_no+"'";
	if(type==1)	
	{			
		http.open("POST","../../../../order/woven_order/requires/short_fabric_booking_controller.php",true);
	}
	else if(type==2)
	{
		http.open("POST","../../../../order/woven_order/requires/fabric_booking_controller.php",true);
	}
	else
	{
		http.open("POST","../../../../order/woven_order/requires/sample_booking_controller.php",true);
	}
	
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = generate_fabric_report_reponse;
}

function generate_fabric_report_reponse()
{
	if(http.readyState == 4) 
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
		d.close();
	}
}

function generate_pre_cost_report(type,job_no,company_id,buyer_id,style_ref,costing_date)
{
	var data="action="+type+
				'&txt_job_no='+"'"+job_no+"'"+
				'&cbo_company_name='+"'"+company_id+"'"+
				'&cbo_buyer_name='+"'"+buyer_id+"'"+
				'&txt_style_ref='+"'"+style_ref+"'"+
				'&txt_costing_date='+"'"+costing_date+"'"+
				"&zero_value=1"+
				'&path=../../../../';
				
	http.open("POST","../../../../order/woven_order/requires/pre_cost_entry_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_generate_report_reponse;
}

function fnc_generate_report_reponse()
{
	if(http.readyState == 4) 
	{
		$('#data_panel').html( http.responseText );
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
		d.close();
	}
}

/*function progress_comment_popup(job_no,po_id,template_id,tna_process_type)
{
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', '../../reports/management_report/merchandising_report/requires/shipment_date_wise_wp_report_controller.php?job_no='+job_no+'&po_id='+po_id+'&template_id='+template_id+'&tna_process_type='+tna_process_type+'&action=update_tna_progress_comment'+'&permission='+permission, "TNA Progress Comment", 'width=1030px,height=390px,center=1,resize=1,scrolling=0','../');
}*/


function progress_comment_popup(job_no,po_id,template_id,tna_process_type)
{
	var data="action=update_tna_progress_comment"+
							'&job_no='+"'"+job_no+"'"+
							'&po_id='+"'"+po_id+"'"+
							'&template_id='+"'"+template_id+"'"+
							'&tna_process_type='+"'"+tna_process_type+"'"+
							'&permission='+"'"+permission+"'";	
							
	http.open("POST","../../../../reports/management_report/merchandising_report/requires/shipment_date_wise_wp_report_controller.php",true);
	
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = generate_progress_comment_reponse;	
}

function generate_progress_comment_reponse()
{
	if(http.readyState == 4) 
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
		d.close();
	}
}

</script>

<style>
	hr
	{
		color: #676767;
		background-color: #676767;
		height: 1px;
	}
</style>
    
    <input type="hidden" name="txt_job_no" id="txt_job_no" value="<? echo $txt_job_no; ?>"/>
    <input type="hidden" name="cbo_company_name" id="cbo_company_name" value="<? echo $cbo_company_name; ?>"/>
    <input type="hidden" name="cbo_buyer_name" id="cbo_buyer_name" value="<? echo $ex_data[0]; ?>"/>
    <input type="hidden" name="txt_style_ref" id="txt_style_ref" value="<? echo $ex_data[1]; ?>"/>
    <input type="hidden" name="cbo_type" id="cbo_type" value="<? echo '1'; ?>"/>
    <input type="hidden" name="txt_search_string" id="txt_search_string" value="<? echo $ex_data[3]; ?>"/>
    <input type="button" id="report_btn" class="formbutton" style="width:120px" value="Show" onClick="fn_report_generated('report_generate')" /><br>
    <div style="color:#FF0000; font-size:14px; font-weight:bold; width:1000px">All Information Mentioned for full Order Qty. since Fabric Production not Captured Country wise.</div>
    <br>
    <div style="display:none" id="data_panel"></div>   
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
    <?
	exit;
}

if($action=="country_gmts_info")
{
	$ex_data=explode('***',$all_data);
	?>
    <script>
	var permission = '<? echo $permission; ?>';
	var tableFilters2 = 
	{
		col_46:'none',
		col_operation: { 
			id: ["total_order_quantity","plan_cut_qnty","total_cutting","short_excess_cut","total_print_issue_in","total_print_issue_out","total_print_receive_in","total_print_receive_out","total_emb_issue_in","total_emb_issue_out","total_emb_receive_in","total_emb_receive_out","total_sp_issue","total_sp_receive","total_sewing_input","total_sewing_out","total_subcon_sewing_input","total_subcon_sewing_out","total_sewing_input_all_qnty","total_sewing_output_all_qnty","total_wash_issue","total_wash_receive","total_iron_qnty","total_re_iron_qnty","finish_qnty_inhouse","finish_qnty_subcon","total_finish_qnty","total_rej_value_td","total_out","total_shortage"],
			col: [10,13,14,15,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,41,42,43],
			operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
 	}
	function fn_report_generated(type)
	{
		var company_name=$('#cbo_company_name').val();
		var job_no_pre=$('#txt_job_no_pre').val();
		var po_breack_down_id=$('#txt_po_breack_down_id').val();
		var country_id=$('#cbo_country_name').val();
		var cbo_job_year=$('#cbo_job_year').val();
		var data="action=report_generate&type="+type+
		 			'&cbo_company_name='+"'"+company_name+"'"+
					'&txt_style_ref='+"'"+job_no_pre+"'"+
					'&txt_order_id='+"'"+po_breack_down_id+"'"+
					'&country_id='+"'"+country_id+"'"+
					'&cbo_job_year='+"'"+cbo_job_year+"'";
		//alert(data);
		//
		http.open("POST","../../../../production/reports/requires/order_wise_production_report_format2_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
		//freeze_window(3);
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("**");
			//alert(reponse[0]); 
			$('#report_container3').html(reponse[0]);
			//release_freezing();
			//document.getElementById('report_container').innerHTML=report_convert_button('../../../../'); 
			if(reponse[1]==0)
			{
				setFilterGrid("table_body",-1,tableFilters1);
			}
			else if(reponse[1]==1)
			{
				setFilterGrid("table_body",-1,tableFilters2);
			}
			else
			{
				setFilterGrid("table_body",-1,tableFilters3);
			}
			show_msg('3');
			release_freezing();
		}
	}
	function progress_comment_popup(po_id,template_id,tna_process_type)
	{
		var data="action=update_tna_progress_comment"+
								'&po_id='+"'"+po_id+"'"+
								'&template_id='+"'"+template_id+"'"+
								'&tna_process_type='+"'"+tna_process_type+"'"+
								'&permission='+"'"+permission+"'";	
								
		http.open("POST","../../../../production/reports/requires/order_wise_production_report_format2_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_progress_comment_reponse;	
	}
	
	function generate_progress_comment_reponse()
	{
		if(http.readyState == 4) 
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
			d.close();
		}
	}
	function openmypage_order_country(po_break_down_id,company_name,item_id,country_id,color_id,action)
	{
		//var garments_nature = $("#cbo_garments_nature").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', '../../../../production/reports/requires/order_wise_production_report_format2_controller.php?po_break_down_id='+po_break_down_id+'&company_name='+company_name+'&item_id='+item_id+'&country_id='+country_id+'&color_id='+color_id+'&action='+action, 'Order Quantity', 'width=750px,height=350px,center=1,resize=0,scrolling=0','../../../');
	}
	
	function openmypage_popup(po_break_down_id,item_id,country_id,color_id,prod_popup_type,prod_popup_lelel,action)
	{
		if (prod_popup_type==1)
		{
			var popup_caption="Cutting Qnty Details";
		}
		else if (prod_popup_type==2)
		{
			var popup_caption="Embl. Issue. Details";
		}
		else if (prod_popup_type==3)
		{
			var popup_caption="Embl. Rec. Details";
		}
		else if (prod_popup_type==4)
		{
			var popup_caption="Sewing Input Details";
		}
		else if (prod_popup_type==5)
		{
			var popup_caption="Sewing Output Details";
		}
		else if (prod_popup_type==7)
		{
			var popup_caption="Iron Details";
		}
		else if (prod_popup_type==8)
		{
			var popup_caption="Finish Details";
		}
		else
		{
			var popup_caption="Ex-fact Details";
		}
			
		emailwindow=dhtmlmodal.open('EmailBox','iframe','../../../../production/reports/requires/order_wise_production_report_format2_controller.php?po_break_down_id='+po_break_down_id+'&item_id='+item_id+'&country_id='+country_id+'&color_id='+color_id+'&prod_popup_type='+prod_popup_type+'&prod_popup_lelel='+prod_popup_lelel+'&action='+action, popup_caption, 'width=500px,height=250px,center=1,resize=0,scrolling=0','../../../');
		
	}
	
	function openmypage_rej(po_id,item_id,action,country_id,color_id,reportType,rpt_leb)
	{
		//alert(country_id);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', '../../../../production/reports/requires/order_wise_production_report_format2_controller.php?po_id='+po_id+'&item_id='+item_id+'&action='+action+'&country_id='+country_id+'&color_id='+color_id+'&reportType='+reportType+'&country_id='+country_id, 'Reject Quantity', 'width=510px,height=350px,center=1,resize=0,scrolling=0','../../../');
	}
	
	function openmypage_rej_show(po_id,item_id,action,location_id,floor_id,reportType,country_id)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', '../../../../production/reports/requires/order_wise_production_report_format2_controller.php?po_id='+po_id+'&item_id='+item_id+'&action='+action+'&location_id='+location_id+'&floor_id='+floor_id+'&reportType='+reportType+'&country_id='+country_id, 'Reject Quantity', 'width=510px,height=350px,center=1,resize=0,scrolling=0','../../../../');
	}
	
	</script>
    <body onLoad="set_hotkey();">
        <input type="hidden" name="txt_job_no_pre" id="txt_job_no_pre" value="<? echo $txt_job_no; ?>"/>
        <input type="hidden" name="cbo_company_name" id="cbo_company_name" value="<? echo $cbo_company_name; ?>"/>
        <input type="hidden" name="txt_po_breack_down_id" id="txt_po_breack_down_id" value="<? echo $po_id; ?>"/>
        <input type="hidden" name="cbo_country_name" id="cbo_country_name" value="<? echo $cbo_country_name; ?>"/>
        <input type="hidden" name="cbo_job_year" id="cbo_job_year" value="<? echo $job_year; ?>"/>
        <input type="button" id="report_btn" class="formbutton" style="width:120px" value="Country Wise" onClick="fn_report_generated('1')" />
        <div style="display:none" id="data_panel"></div> 
        <div id="report_container1" align="center"></div>
        <div id="report_container3" align="left"></div>
    </body>
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    <?
	exit();
}

if($action=="country_trims_status")
{
	//$ex_data=explode('***',$all_data);
	?>
    <script>
	var permission = '<? echo $permission; ?>';
	
	function fn_report_generated()
	{
		if(form_validation('cbo_company_name','Company Name')==false)//*txt_date_from*txt_date_to*From Date*To Date
		{
			return;
		}
		else
		{	
			var cbo_company_name=$('#cbo_company_name').val();
			var txt_job_pre=$('#txt_job_pre').val();
			var txt_order_no=$('#txt_order_no').val();
			var cbo_year_selection=$('#cbo_year_selection').val();
			var cbo_search_by=$('#cbo_search_by').val();
			var data="action=report_generate&cbo_company_name="+cbo_company_name+
						'&cbo_year_selection='+"'"+cbo_year_selection+"'"+
						'&txt_job_no='+"'"+txt_job_pre+"'"+
						'&cbo_search_by='+"'"+cbo_search_by+"'"+
						'&txt_order_no='+"'"+txt_order_no+"'";
			//alert(data);
			//freeze_window(3);
			//http.open("POST","../../../../production/reports/requires/order_wise_production_report_format2_controller.php",true);
			http.open("POST","../../../../order/reports/requires/accessories_followup_report_controller_v2.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
			//alert(data);
			//freeze_window(3);
		}
	}
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
			//var tot_rows=reponse[2];
			//var search_by=reponse[3];
			$('#report_container4').html(reponse[0]);
			
			show_msg('3');
			release_freezing();
		}
	}
	
	function generate_report(company,job_no,type)
	{
		var data="action="+type+"&txt_job_no='"+job_no+"'&cbo_company_name="+company;
		http.open("POST","../../../../order/woven_order/requires/pre_cost_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_generate_report_reponse;
	}
	
	function fnc_generate_report_reponse()
	{
		if(http.readyState == 4) 
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><body>'+http.responseText+'</body</html>');
			d.close();
		}
	}
	
	function openmypage(po_id,item_name,job_no,book_num,trim_dtla_id,action)
	{ //alert(book_num);
		var popup_width='900px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', '../../../../order/reports/requires/accessories_followup_report_controller_v2.php?po_id='+po_id+'&item_name='+item_name+'&job_no='+job_no+'&book_num='+book_num+'&trim_dtla_id='+trim_dtla_id+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../../');
	}
	function openmypage_inhouse(po_id,item_name,action)
	{
		var popup_width='900px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', '../../../../order/reports/requires/accessories_followup_report_controller_v2.php?po_id='+po_id+'&item_name='+item_name+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../../');
	}
	
	function openmypage_issue(po_id,item_name,action)
	{
		var popup_width='900px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', '../../../../order/reports/requires/accessories_followup_report_controller_v2.php?po_id='+po_id+'&item_name='+item_name+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../../');
	}
	function order_qty_popup(company,job_no,po_id,buyer,action)
	{
		//alert(po_id);
		var popup_width='800px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', '../../../../order/reports/requires/accessories_followup_report_controller_v2.php?company='+company+'&job_no='+job_no+'&po_id='+po_id+'&buyer='+buyer+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../../');
	}
	
	function order_req_qty_popup(company,job_no,po_id,buyer,rate,item_group,boook_no,description,country_id,trim_dtla_id,start_date,end_date,action)
	{
		//alert(country_id);
		var popup_width='800px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', '../../../../order/reports/requires/accessories_followup_report_controller_v2.php?company='+company+'&job_no='+job_no+'&po_id='+po_id+'&buyer='+buyer+'&rate='+rate+'&item_group='+item_group+'&boook_no='+boook_no+'&description='+description+'&country_id_string='+country_id+'&trim_dtla_id='+trim_dtla_id+'&start_date='+start_date+'&end_date='+end_date+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../../');
	}
	</script>
    <body onLoad="set_hotkey();">
        <input type="hidden" name="txt_job_pre" id="txt_job_pre" value="<? echo $job_no; ?>"/>
        <input type="hidden" name="cbo_company_name" id="cbo_company_name" value="<? echo $cbo_company_name; ?>"/>
        <input type="hidden" name="txt_order_no" id="txt_order_no" value="<? echo $po_id; ?>"/>
        <input type="hidden" name="cbo_year_selection" id="cbo_year_selection" value="<? echo $job_year; ?>"/>
        <input type="hidden" name="cbo_search_by" id="cbo_search_by" value="<? echo '1'; ?>"/>
        <input type="button" id="report_btn" class="formbutton" style="width:100px" value="Show" onClick="fn_report_generated()" />
        <div id="report_container5" align="center"></div>
        <div id="report_container4"></div>
    </body>
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    <?
	exit();
}

if($action=="country_size_color_dtls")
{
	$size_lib_arr=return_library_array( "select id,size_name from lib_size",'id','size_name');
	$color_lib_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');
	
	$color_size_arr=sql_select("SELECT  color_number_id, size_number_id, order_quantity, excess_cut_perc, plan_cut_qnty FROM wo_po_color_size_breakdown WHERE job_no_mst='$job_no' and po_break_down_id='$po_id' and country_id=$country_id and status_active=1 and is_deleted=0");
	
	foreach($color_size_arr as $rows)
	{
		$size_arr[$rows[csf('size_number_id')]]=$rows[csf('size_number_id')];	
		$data_arr_order[$rows[csf('color_number_id')]][$rows[csf('size_number_id')]]=$rows[csf('order_quantity')];	
		$data_arr_plan[$rows[csf('color_number_id')]][$rows[csf('size_number_id')]]=$rows[csf('plan_cut_qnty')];
		$data_arr_excess[$rows[csf('color_number_id')]][$rows[csf('size_number_id')]]=$rows[csf('excess_cut_perc')];	
	}
	?>
	<fieldset>	
        <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <th width="35">SL</th>
                <th width="100">Color/Size</th>
                <th width="100"></th>
                	<? foreach($size_arr as $val):?>
                <th width="70"><? echo $size_lib_arr[$val];?></th>
                	<? endforeach; ?>
                <th>Color Total</th>
            </thead>
            <tbody>
				<? $i=1; $grand_total_ex=array(); $grand_total_plan=array();
                foreach($data_arr_order as $color=>$size):
                $bgcolor=$i%2==0?'#E9F3FF':'#FFFFFF';
				$color_row_plan_tot=0; $color_row_ex_tot=0;
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" style="cursor:pointer;">
                    <td align="center" rowspan="3"><? echo $i;?></td>
                    <td rowspan="3"><? echo $color_lib_arr[$color];?></td>
                    <td>Order Qnty</td>
                    <? foreach($size_arr as $size_id){ 
                    ${'tot_qty'.$color}+=$data_arr_order[$color][$size_id];
                    ${'grand_qty'.$size_id}+=$data_arr_order[$color][$size_id];
                    ?> 
                    <td align="right"><? echo number_format($data_arr_order[$color][$size_id]); ?></td>
                    <? } ?>
                    <td align="right"><? echo number_format(${'tot_qty'.$color});?></td>
                </tr>
                <tr>
                	<td>Plan Cut Qnty</td>
                    <? foreach($size_arr as $size_id){ ?>
                    <td align="right"><? echo number_format($data_arr_plan[$color][$size_id]); 
					$grand_total_plan[$size_id]+=$data_arr_plan[$color][$size_id]; 
					$color_row_plan_tot+=$data_arr_plan[$color][$size_id];
					
					?></td>
                    <? } ?>
                    <td align="right"><? echo number_format($color_row_plan_tot);?></td>
                </tr>
                <tr>
                	<td>Ex. Cut Percent</td>
                    <? foreach($size_arr as $size_id){ 
					?>
                    <td align="right"><? echo number_format($data_arr_excess[$color][$size_id]); 
					$grand_total_ex[$size_id]+=$data_arr_excess[$color][$size_id]; 
					$color_row_ex_tot=(${'tot_qty'.$color}/$color_row_plan_tot)*100;
					?></td>
                    <? } ?>
                    <td align="right"><? echo number_format(100-$color_row_ex_tot);?></td>
                </tr>
                <? $i++;
                endforeach;?>
            </tbody>
            <tfoot>
            	<tr>
                    <th width="35"></th>
                    <th width="100">&nbsp;</th>
                    <th width="100">Size/Total Order Qty</th>
                    <? foreach($size_arr as $size):
                    $grand_tot_order+=${'grand_qty'.$size};
                    ?>
                    <th width="100"><? echo number_format(${'grand_qty'.$size});?></th>
                    <? endforeach; ?>
                    <th><? echo number_format($grand_tot_order); ?></th>
                </tr>
                <tr>
                    <th width="35"></th>
                    <th width="100">&nbsp;</th>
                    <th width="100">Size/Total Plan Cut Qty</th>
                    <? foreach($size_arr as $size):
					$grand_tot_plan+=$grand_total_plan[$size];
                    ?>
                    <th width="100"><? echo number_format($grand_total_plan[$size]);?></th>
                    <? endforeach; ?>
                    <th><? echo number_format($grand_tot_plan); ?></th>
                </tr>
                <tr>
                    <th width="35"></th>
                    <th width="100">&nbsp;</th>
                    <th width="100">Size/Total Ex. Cut Precent</th>
                    <? foreach($size_arr as $size):
                    $grand_tot_ex_per=(${'grand_qty'.$size}/$grand_total_plan[$size])*100;
					
                    ?>
                    <th width="100"><? echo number_format(100-$grand_tot_ex_per);?></th>
                    <? endforeach; ?>
                    <th><? $grand_tot_ex=($grand_tot_order/$grand_tot_plan)*100; echo number_format(100-$grand_tot_ex); ?></th>
                </tr>
            </tfoot>
        </table>
	</fieldset>
	<?
	exit();	
}

if($action=="country_emblishment_app")
{
	$job_number = $job_no;
	$po_id = $po_id;
	$po_arr=return_library_array( "select id, po_number from wo_po_break_down where id in ($po_id)",'id','po_number');
?>
    <div style="width:100%" align="center">
        <fieldset style="width:900px">
                <table width="600">
                    <?
                    $job_sql= sql_select("select job_no,buyer_name,company_name,style_ref_no from wo_po_details_master where job_no='$job_number'");
                    foreach( $job_sql as $row_job);  // Master Job  table queery ends here
                     ?>
                        <tr class="form_caption">
                            <td align="center" colspan="4"><strong>Embellishment Approval Details</strong></td>	
                        </tr>
                        <tr>
                            <td align="right" width="130"> <strong>Job Number</strong> :</td> 
                            <td width="200"><? echo $job_number; ?></td> 
                            <td align="right"  width="130"><strong>Buyer Name</strong> :</td>  
                            <td><? echo $buyer_short_name_arr[$row_job[csf("buyer_name")]]; ?></td> 
                        </tr>
                        <tr>
                            <td align="right"><strong>Company Name</strong> :</td> 
                            <td><? echo $company_short_name_arr[$row_job[csf("company_name")]]; ?></td> 
                            <td align="right"><strong>Style Ref No</strong> : </td> 
                            <td><? echo $row_job[csf("style_ref_no")]; ?> </td>
                        </tr> 
                        <tr>
                            <td colspan="4" height="15"></td>	                
                        </tr>            
                </table>
    
                <div style="width:100%">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                        <thead>
                            <tr>
                                <th width="35">SL</th>
                                <th width="80">PO Number</th>
                                <th width="100">Embellis. Name</th> 
                                <th width="100">Embellis. Type</th>
                                <th width="80">Target Date</th>
                                <th width="80">To Supplier</th>
                                <th width="80">To Buyer</th>
                                <th width="80">Status</th>
                                <th width="80">Approval Date</th>
                                <th width="80">Supplier</th>
                                <th>Remarks</th>
                             </tr>   
                        </thead>
                    </table>
                </div>
                <div style="width:100%; max-height:270px; overflow-y:scroll">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                        <?
                        $i=0;
                        $emb_sql= sql_select("select a.po_break_down_id, embellishment_id, embellishment_type_id, target_approval_date, sent_to_supplier, submitted_to_buyer, approval_status, approval_status_date, b.supplier_name, embellishment_comments 
                        from  wo_po_embell_approval a left join lib_supplier b on a.supplier_name=b.id
                        where a.job_no_mst='$job_number' and a.approval_status<>0 and a.current_status=1 and a.status_active=1 and a.is_deleted=0 and a.po_break_down_id in ($po_id)"); 
                        foreach($emb_sql as $row)
                        {
                            $i++;
                            if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
                            $embl_type_name="";
                            if($row[csf("embellishment_id")]==1) $embl_type_name=$emblishment_print_type[$row[csf("embellishment_type_id")]];
                            else if($row[csf("embellishment_id")]==2) $embl_type_name=$emblishment_embroy_type[$row[csf("embellishment_type_id")]];
                            else if($row[csf("embellishment_id")]==3) $embl_type_name=$emblishment_wash_type[$row[csf("embellishment_type_id")]];
                            else if($row[csf("embellishment_id")]==4) $embl_type_name=$emblishment_spwork_type[$row[csf("embellishment_type_id")]];
                            else $embl_type_name="";
                       ?>
                       <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="35"><? echo $i; ?></td>
                            <td width="80"><? echo $po_arr[$row[csf("po_break_down_id")]];?> </td>
                            <td width="100"><p><? echo $emblishment_name_array[$row[csf("embellishment_id")]]; ?></p></td>
                            <td width="100"><p><? echo $embl_type_name; ?></p></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf("target_approval_date")]); ?></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf("sent_to_supplier")]); ?></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf("submitted_to_buyer")]); ?></td>
                            <td width="80" align="center"><p><? echo $approval_status[$row[csf("approval_status")]]; ?></p></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf("approval_status_date")]); ?></td>
                            <td width="80"><p><? echo $row[csf("supplier_name")]; ?></p></td>
                            <td><p><? echo $row[csf("embellishment_comments")]; ?></p></td>
                        </tr>
                        <? } ?>
                    </table>
                </div> 
        </fieldset>
    </div>    
    <?
    exit();
}
//Ex-Factory Delv. and Return
if($action=="ex_factory_popup")
{
 	echo load_html_head_contents("Ex-Factory Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	//echo $id;//$job_no;
	?>
	<div style="width:100%" align="center">
		<fieldset style="width:500px"> 
        <div class="form_caption" align="center"><strong>Ex-Factory Details</strong></div><br />
            <div style="width:100%"> 
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="35">SL</th>
                        <th width="90">Ex-fac. Date</th>
                        <th width="120">System /Challan no</th>
                        <th width="100">Ex-Fact. Del.Qty.</th>
                        <th width="">Ex-Fact.Return Qty.</th>
                       
                     </tr>   
                </thead> 	 	
            </table>  
        </div>
        <div style="width:100%; max-height:400px;">
            <table cellpadding="0" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
                <?
                $i=1;
                //$ex_fac_sql="SELECT id, ex_factory_date, ex_factory_qnty, challan_no, transport_com from pro_ex_factory_mst where po_break_down_id in($id) and status_active=1 and is_deleted=0";
                //echo $ex_fac_sql;
				$exfac_sql=("select b.challan_no,a.sys_number,b.ex_factory_date, 
		CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_qnty,
		CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_return_qnty 
		from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b  where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.po_break_down_id in($id) ");
                $sql_dtls=sql_select($exfac_sql);
                
                foreach($sql_dtls as $row_real)
                { 
                    if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";                               
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_l<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="35"><? echo $i; ?></td> 
                        <td width="90"><? echo change_date_format($row_real[csf("ex_factory_date")]); ?></td>
                        <td width="120"><? echo $row_real[csf("sys_number")]; ?></td>
                        <td width="100" align="right"><? echo $row_real[csf("ex_factory_qnty")]; ?></td>
                         <td width="" align="right"><? echo $row_real[csf("ex_factory_return_qnty")]; ?></td>
                    </tr>
                    <? 
                    $rec_qnty+=$row_real[csf("ex_factory_qnty")];
					 $rec_return_qnty+=$row_real[csf("ex_factory_return_qnty")];
                    $i++;
                }
                ?>
                <tfoot>
                <tr>
                    <th colspan="3">Total</th>
                    <th><? echo number_format($rec_qnty,2); ?></th>
                    <th><? echo number_format($rec_return_qnty,2); ?></th>
                </tr>
                <tr>
                 <th colspan="3">Total Balance</th>
                 <th colspan="2" align="right"><? echo number_format($rec_qnty-$rec_return_qnty,2); ?></th>
                </tr>
                </tfoot>
            </table>
        </div> 
		</fieldset>
	</div>    
	<?
    exit();	
}


?>