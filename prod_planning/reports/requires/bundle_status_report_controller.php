<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_arr=return_library_array( "select id,company_name from lib_company", "id", "company_name");
$location_arr=return_library_array( "select id,location_name from lib_location where status_active =1 and is_deleted=0  order by location_name","id","location_name");

if ($action=="set_session_data")
{
	extract($_REQUEST);
	$_SESSION['bundle_id']=$bundle_id;
	exit();
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "" );     	 
	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 120, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "--Select Location--", $selected, "load_drop_down( 'requires/bundle_status_report_controller', this.value, 'load_drop_down_group', 'group_td' );",0 );exit();
}

if ($action=="load_drop_down_group")
{
	echo create_drop_down( "cbo_floor_group", 130, "select distinct group_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and  group_name is not null order by group_name","group_name,group_name", 1, "-- Select Group --", $selected, "",0 );
}

if ($action=="load_drop_down_agent")
{
	echo create_drop_down( "cbo_agent", 120, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (20,21))  group by a.id,a.buyer_name order by buyer_name","id,buyer_name", 1, "-- All Agent --", $selected, "" ); exit(); 
} 


if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_location_id=str_replace("'","",$cbo_location_id);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$txt_job_no=trim(str_replace("'","",$txt_job_no));
	$txt_order_no=trim(str_replace("'","",$txt_order_no));
	$cbo_floor_group=trim(str_replace("'","",$cbo_floor_group));
	// echo $cbo_floor_group;die;
	$txt_internal_ref_no=trim(str_replace("'","",$txt_internal_ref_no));
	$cbo_date_category=str_replace("'","",$cbo_date_category);
	$cbo_job_year_id=str_replace("'","",$cbo_job_year_id);
	$cbo_ship_status=str_replace("'","",$cbo_ship_status);
	//$company_arr=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	$floor_group_array=return_library_array( "select id, group_name from  lib_prod_floor",'id','group_name');
	
	//$yarn_count=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	//$color_arr=return_library_array( "select id,color_name from lib_color","id","color_name");
	//--------------------------------------------------------------------------------------------------------------------
	
	if($cbo_date_category==2){
		$dateFill=" c.country_ship_date";
		$dateCaption="Country Ship Date";
	}
	elseif($cbo_date_category==4){
		$dateFill=" b.insert_date";
		$dateCaption="Insert Date";
	}
	else{//($cbo_date_category==1)
		$dateFill=" b.pub_shipment_date";
		$dateCaption="Shipment Date";
	}
	
	if ($cbo_company_id==0) $company_con=""; else $company_con=" and a.company_name=$cbo_company_id ";
	
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
		
		
	if($db_type==0)
	{
		$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
		$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
	}
	else if($db_type==2)
	{
		$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
		$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
	}
	if($start_date=="" && $end_date=="")
	{
		$date_cond="";
	}
	else
	{
		$date_cond=" and $dateFill between '$start_date' and '$end_date'";
	}
		
	if ($txt_job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no like ('%$txt_job_no') ";
	if ($txt_order_no=="") $order_cond=""; else $order_cond=" and b.po_number like ('%$txt_order_no') ";
	if ($cbo_location_id==0) $location_id_cond=""; else $location_id_cond=" and a.location_name=$cbo_location_id ";
	// if ($cbo_floor_id==0) $floor_id_cond=""; else $floor_id_cond=" and a.floor_id=$cbo_floor_id ";
	// if ($txt_floor_group==0) $floor_group_cond=""; else $floor_group_cond=" and a.location_name=$txt_floor_group ";
	if ($txt_internal_ref_no=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping= '$txt_internal_ref_no'";
	if ($cbo_job_year_id!=0){
		if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_job_year_id";
		else if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_job_year_id";
	}
	if ($cbo_ship_status==0) $ship_status_cond=""; else $ship_status_cond=" and b.shiping_status=$cbo_ship_status ";

	if($cbo_floor_group != "")
	{
		$floor_group_sql=sql_select("SELECT   id  FROM lib_prod_floor where group_name ='$cbo_floor_group' and status_active=1 ");
		$floor_group_arr=array();
		foreach($floor_group_sql as $fl)
		{
			$floor_group_arr[$fl[csf("id")]]=$fl[csf("id")];
		}
		$all_floor_by_group=implode(",",$floor_group_arr);

		$floor_group_cond.= " and e.floor_id in ($all_floor_by_group) ";
	}
	// echo "<pre>";
	// print_r($floor_group_arr);
	// echo $floor_group_cond; die;


	
	//this condition put blew of all condition;	
	if($cbo_date_category==3 && ($start_date!="" && $end_date!="")){	
		$dateCaption="Cut Plan Date";
		// $sql="select e.entry_date,f.order_id,f.gmt_item_id,f.floor_id from  ppl_cut_lay_mst e, ppl_cut_lay_dtls f where e.id=f.mst_id and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1 and e.entry_date between '$start_date' and '$end_date' and e.entry_form=77";
		$sql="select e.entry_date,f.order_ids,f.gmt_item_id from  ppl_cut_lay_mst e, ppl_cut_lay_dtls f where e.id=f.mst_id and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1 and e.entry_date between '$start_date' and '$end_date' ";
		// echo $sql;
		$sql_result=sql_select($sql);
		$cut_ent_date_arr=array();
		$order_id_arr=array();
		// foreach($sql_result as $row)
		// {	$key=$row[csf('order_id')].'_'.$row[csf('gmt_item_id')];
		// 	$cut_ent_date_arr[$key]=$row[csf('entry_date')];
		// 	$order_id_arr[$row[csf('order_id')]]=$row[csf('order_id')];
		// }
		foreach($sql_result as $row)
		{	$key=$row[csf('order_ids')].'_'.$row[csf('gmt_item_id')];
			$cut_ent_date_arr[$key]=$row[csf('entry_date')];
			$order_id_arr[$row[csf('order_ids')]]=$row[csf('order_ids')];
		}
		$date_cond=" and b.id in(".implode(',',$order_id_arr).")";
	
	}

 // echo $cbo_floor_group; die;
    if(empty($cbo_floor_group))
	{
		// echo $cbo_floor_group;
		$sql="select a.buyer_name, a.job_no, a.company_name, a.style_ref_no, b.id as po_id, b.po_number, b.po_quantity, b.plan_cut, $dateFill as shipment_date, b.grouping, c.item_number_id, c.order_quantity, c.plan_cut_qnty, c.excess_cut_perc, d.smv_set, d.set_item_ratio, a.remarks from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_po_details_mas_set_details d $table where a.job_no=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.po_break_down_id and b.id=c.po_break_down_id and a.job_no=d.job_no and c.job_no_mst=d.job_no and c.item_number_id=d.gmts_item_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $company_con $buyer_id_cond $date_cond $job_no_cond $order_cond $location_id_cond $ship_status_cond $year_cond $table_con $internal_ref_cond ";
	}else
	{
		$sql="select a.buyer_name, a.job_no, a.company_name, a.style_ref_no, b.id as po_id, b.po_number, b.po_quantity, b.plan_cut, $dateFill as shipment_date, b.grouping, c.item_number_id, c.order_quantity, c.plan_cut_qnty, c.excess_cut_perc, d.smv_set, d.set_item_ratio, a.remarks,e.floor_id from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_po_details_mas_set_details d, ppl_cut_lay_mst e $table where a.job_no=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.po_break_down_id and b.id=c.po_break_down_id and a.job_no=d.job_no and c.job_no_mst=d.job_no and c.item_number_id=d.gmts_item_id and a.job_no = e.job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and e.is_deleted = 0 and e.status_active = 1 $company_con $buyer_id_cond $date_cond $job_no_cond $order_cond $location_id_cond $ship_status_cond $year_cond $table_con $internal_ref_cond $floor_group_cond ";
	}
				
			//wo_po_details_mas_set_details
		    // echo $sql;// die; 
			 
		$sql_result=sql_select( $sql );
		$data_arr=array();
		foreach($sql_result as $row)
		{	//$key=$row[csf('job_no')].'_'.$row[csf('po_id')].'_'.$row[csf('item_number_id')];
			$key=$row[csf('po_id')].'_'.$row[csf('item_number_id')];
			$data_arr[$key]=array(
				company_name=>$row[csf('company_name')],
				job_no=>$row[csf('job_no')],
				cutting_no=>$row[csf('cutting_no')],
				buyer_name=>$row[csf('buyer_name')],
				style_ref_no=>$row[csf('style_ref_no')],
				gmt_item_id=>$row[csf('item_number_id')],
				po_number=>$row[csf('po_number')],
				shipment_date=>$row[csf('shipment_date')],
				smv_set=>$row[csf('smv_set')],
				remarks=>$row[csf('remarks')],
				grouping=>$row[csf('grouping')],
				floor_id=>$row[csf('floor_id')],
			);
			$order_qty_arr[$key][oq]=$row[csf('po_quantity')]*$row[csf('set_item_ratio')];
			$order_qty_arr[$key][pcq]=$row[csf('plan_cut')]*$row[csf('set_item_ratio')];
			//$order_qty_arr[$key][oq]+=$row[csf('order_quantity')];
			//$order_qty_arr[$key][pcq]+=$row[csf('plan_cut_qnty')];
			$order_qty_arr[$key][ecq]=$row[csf('excess_cut_perc')];
			$job_arr[$row[csf('job_no')]]=$row[csf('job_no')];
			$order_arr[$row[csf('po_id')]]=$row[csf('po_id')];
			//buyer summery data-----------------------------------
			$summary_data_arr[$row[csf('buyer_name')]][$key]+=$row[csf('order_quantity')];
		}
	$job_sting=implode("','",$job_arr);	
	$order_sting=implode(",",$order_arr);	
		//echo $sql_data;
	//-----cutting------------------------------------	
	$sql_cut="select d.entry_date as shipment_date, d.id as cutting_id, d.cutting_no, d.job_no, d.floor_id, f.order_id, e.gmt_item_id, f.bundle_no, f.size_qty from ppl_cut_lay_mst d, ppl_cut_lay_dtls e, ppl_cut_lay_bundle f where d.id=e.mst_id and f.mst_id=d.id and f.dtls_id=e.id and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0   and d.job_no in('$job_sting') and d.company_id=$cbo_company_id ";
				
	 	//echo $sql_cut;die;
		$sql_cut_result=sql_select( $sql_cut );
		$cut_data_arr=array();
		foreach($sql_cut_result as $rows)
		{	
			//$key=$rows[csf('job_no')].'_'.$rows[csf('order_id')].'_'.$rows[csf('gmt_item_id')];
			$key=$rows[csf('order_id')].'_'.$rows[csf('gmt_item_id')];
			$cut_data_arr[$key][cutting_id][$rows[csf('cutting_id')]]=$rows[csf('cutting_id')];
			$cut_data_arr[$key][tot_cut][$rows[csf('cutting_no')]]=$rows[csf('cutting_no')];
			$cut_data_arr[$key][tot_bundle][$rows[csf('bundle_no')]]=$rows[csf('bundle_no')];
			$cut_data_arr[$key][tot_bundle_qty]+=$rows[csf('size_qty')];
			$cut_data_arr[$key][floor_group]=$rows[csf('floor_id')];
		}
		// echo"<pre>";
		// print_r($cut_data_arr);
		
	//cutting qc data.................................	
		
	$sql_cutt_qc="select a.po_break_down_id,a.item_number_id,b.production_qnty,b.reject_qty,b.alter_qty,b.spot_qty,b.replace_qty,b.bundle_no,b.id as bundle_id,a.production_type,a.embel_name,a.floor_id from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.po_break_down_id in($order_sting) and a.company_id=$cbo_company_id and a.production_type in(1,2,3,4,5,7,8)";  //and b.bundle_no is not null
	// echo $sql_cutt_qc;
	$sql_cutt_qc_result=sql_select($sql_cutt_qc);
	foreach($sql_cutt_qc_result as $rows)
	{
		$key=$rows[csf('po_break_down_id')].'_'.$rows[csf('item_number_id')];
		if($rows[csf('production_type')]==1 && $rows[csf('bundle_no')]!=''){
			if($cutting_qc_arr[$key][rej_qty] && $rows[csf('reject_qty')]){$cutting_qc_arr[$key][rej_qty].='*';}
			if($rows[csf('reject_qty')]){$cutting_qc_arr[$key][rej_qty].=$rows[csf('bundle_no')].'_'.$rows[csf('reject_qty')];}
			
			$cutting_qc_arr[$key][tot_qty]+=$rows[csf('production_qnty')];
			$cutting_qc_arr[$key][tot_bundle][$rows[csf('bundle_no')]]=$rows[csf('bundle_no')];
			$cutting_qc_arr[$key][tot_bundle_id][$rows[csf('bundle_id')]]=$rows[csf('bundle_id')];
		}
		elseif($rows[csf('production_type')]==4 && $rows[csf('bundle_no')]!=''){
			$sewing_in_arr[$key][tot_qty]+=$rows[csf('production_qnty')];
			$sewing_in_arr[$key][tot_bundle][$rows[csf('bundle_no')]]=$rows[csf('bundle_no')];
			$sewing_input_id_arr[$key][tot_bundle_id][$rows[csf('bundle_id')]]=$rows[csf('bundle_id')];
			$sewing_in_out_arr[$key][tot_bundle_id][$rows[csf('bundle_id')]]=$rows[csf('bundle_id')];
		}
		elseif($rows[csf('production_type')]==5 && $rows[csf('bundle_no')]!=''){
			if($rows[csf('reject_qty')]){$sewing_out_arr[$key][rej_qty].=$rows[csf('bundle_no')].'_'.$rows[csf('reject_qty')].'*';}
			$sewing_out_arr[$key][tot_qty]+=$rows[csf('production_qnty')];
			$sewing_out_arr[$key][tot_bundle][$rows[csf('bundle_no')]]=$rows[csf('bundle_no')];
			$sewing_in_out_arr[$key][tot_bundle_id][$rows[csf('bundle_id')]]=$rows[csf('bundle_id')];
		}
		elseif($rows[csf('production_type')]==7 && $rows[csf('bundle_no')]!=''){
			if($rows[csf('reject_qty')]){$iron_arr[$key][rej_qty].=$rows[csf('bundle_no')].'_'.$rows[csf('reject_qty')].'*';}
			$iron_arr[$key][tot_qty]+=$rows[csf('production_qnty')];
			$iron_arr[$key][tot_bundle][$rows[csf('bundle_no')]]=$rows[csf('bundle_no')];
			$iron_arr[$key][tot_bundle_id][$rows[csf('bundle_no')]]=$rows[csf('bundle_id')];
		}
		elseif($rows[csf('production_type')]==8){
			$finish_gmts_arr[$key][tot_qty]+=$rows[csf('production_qnty')];
			//$finish_gmts_arr[$key][tot_bundle][$rows[csf('bundle_no')]]+=$rows[csf('bundle_no')];
		}
		//emb........................................start;
		elseif($rows[csf('production_type')]==2 && $rows[csf('embel_name')]==1 && $rows[csf('bundle_no')]!=''){
			$print_issue_arr[$key][tot_qty]+=$rows[csf('production_qnty')];
			$print_issue_arr[$key][tot_bundle][$rows[csf('bundle_no')]]=$rows[csf('bundle_no')];
			$print_issue_arr[$key][tot_bundle_id][$rows[csf('bundle_id')]]=$rows[csf('bundle_id')];
			$print_issue_rec_arr[$key][tot_bundle_id][$rows[csf('bundle_id')]]=$rows[csf('bundle_id')];
		}
		elseif($rows[csf('production_type')]==3 && $rows[csf('embel_name')]==1 && $rows[csf('bundle_no')]!=''){
			if($rows[csf('reject_qty')]){$print_rec_arr[$key][rej_qty].=$rows[csf('bundle_no')].'_'.$rows[csf('reject_qty')].'*';}
			$print_rec_arr[$key][tot_qty]+=$rows[csf('production_qnty')];
			$print_rec_arr[$key][tot_bundle][$rows[csf('bundle_no')]]=$rows[csf('bundle_no')];
			$print_issue_rec_arr[$key][tot_bundle_id][$rows[csf('bundle_id')]]=$rows[csf('bundle_id')];
		}
		
		elseif($rows[csf('production_type')]==2 && $rows[csf('embel_name')]==2 && $rows[csf('bundle_no')]!=''){
			$embroidery_issue_arr[$key][tot_qty]+=$rows[csf('production_qnty')];
			$embroidery_issue_arr[$key][tot_bundle][$rows[csf('bundle_no')]]=$rows[csf('bundle_no')];
			$embroidery_issue_arr[$key][tot_bundle_id][$rows[csf('bundle_id')]]=$rows[csf('bundle_id')];
			$embroidery_issue_rec_arr[$key][tot_bundle_id][$rows[csf('bundle_id')]]=$rows[csf('bundle_id')];
		}
		elseif($rows[csf('production_type')]==3 && $rows[csf('embel_name')]==2 && $rows[csf('bundle_no')]!=''){
			if($rows[csf('reject_qty')]){$embroidery_rec_arr[$key][rej_qty].=$rows[csf('bundle_no')].'_'.$rows[csf('reject_qty')].'*';}
			$embroidery_rec_arr[$key][tot_qty]+=$rows[csf('production_qnty')];
			$embroidery_rec_arr[$key][tot_bundle][$rows[csf('bundle_no')]]=$rows[csf('bundle_no')];
			$embroidery_issue_rec_arr[$key][tot_bundle_id][$rows[csf('bundle_id')]]=$rows[csf('bundle_id')];
		}
		elseif($rows[csf('production_type')]==2 && $rows[csf('embel_name')]==3 && $rows[csf('bundle_no')]!=''){
			$wash_issue_arr[$key][tot_qty]+=$rows[csf('production_qnty')];
			$wash_issue_arr[$key][tot_bundle][$rows[csf('bundle_no')]]=$rows[csf('bundle_no')];
			$wash_issue_arr[$key][tot_bundle_id][$rows[csf('bundle_id')]]=$rows[csf('bundle_id')];
			$wash_issue_rec_arr[$key][tot_bundle_id][$rows[csf('bundle_id')]]=$rows[csf('bundle_id')];
		}
		elseif($rows[csf('production_type')]==3 && $rows[csf('embel_name')]==3 && $rows[csf('bundle_no')]!=''){
			if($rows[csf('reject_qty')]){$wash_rec_arr[$key][rej_qty].=$rows[csf('bundle_no')].'_'.$rows[csf('reject_qty')].'*';}
			$wash_rec_arr[$key][tot_qty]+=$rows[csf('production_qnty')];
			$wash_rec_arr[$key][tot_bundle][$rows[csf('bundle_no')]]=$rows[csf('bundle_no')];
			$wash_issue_rec_arr[$key][tot_bundle_id][$rows[csf('bundle_id')]]=$rows[csf('bundle_id')];
		}
		elseif($rows[csf('production_type')]==2 && $rows[csf('embel_name')]==4 && $rows[csf('bundle_no')]!=''){
			$spacial_work_issue_arr[$key][tot_qty]+=$rows[csf('production_qnty')];
			$spacial_work_issue_arr[$key][tot_bundle][$rows[csf('bundle_no')]]=$rows[csf('bundle_no')];
			$spacial_work_issue_arr[$key][tot_bundle_id][$rows[csf('bundle_id')]]=$rows[csf('bundle_id')];
			$spacial_work_issue_rec_arr[$key][tot_bundle_id][$rows[csf('bundle_id')]]=$rows[csf('bundle_id')];
		}
		elseif($rows[csf('production_type')]==3 && $rows[csf('embel_name')]==4 && $rows[csf('bundle_no')]!=''){
			if($rows[csf('reject_qty')]){$spacial_work_rec_arr[$key][rej_qty].=$rows[csf('bundle_no')].'_'.$rows[csf('reject_qty')].'*';}
			$spacial_work_rec_arr[$key][tot_qty]+=$rows[csf('production_qnty')];
			$spacial_work_rec_arr[$key][tot_bundle][$rows[csf('bundle_no')]]=$rows[csf('bundle_no')];
			$spacial_work_issue_rec_arr[$key][tot_bundle_id][$rows[csf('bundle_id')]]=$rows[csf('bundle_id')];
		}

		if($rows[csf('reject_qty')]){$rejected_bundle_arr[$key][$rows[csf('bundle_no')]]=$rows[csf('bundle_no')];}
		if($rows[csf('reject_qty')]){$rejected_order_arr[$key][$rows[csf('po_break_down_id')]]=$rows[csf('po_break_down_id')];}
		$total_reject[$key]+=$rows[csf('reject_qty')];
		$floor_id_array[$key].=$rows[csf('floor_id')].',';
	}
	// echo "<pre>";
	// print_r($print_issue_arr);
	
	$photo_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where master_tble_id in('$job_sting')", "master_tble_id", "image_location");
	$exSqlResult =sql_select("select po_break_down_id,item_number_id,ex_factory_qnty from pro_ex_factory_mst where po_break_down_id in($order_sting) and status_active=1 and is_deleted=0");
 	foreach($exSqlResult as $rows)
	{
		$key=$rows[csf('po_break_down_id')].'_'.$rows[csf('item_number_id')];
		$ship_out[$key]+=$rows[csf('ex_factory_qnty')];
	}
		
		
		?>
        
 <!--<style>
 .css-dtls td:nth-child(17),
 .css-dtls td:nth-child(18),
 .css-dtls td:nth-child(19) {background-color:#EEE;}
 
 .css-dtls td:nth-child(20),
 .css-dtls td:nth-child(21),
 .css-dtls td:nth-child(22) {background-color:#FCF;}
 
 .css-dtls td:nth-child(23),
 .css-dtls td:nth-child(24),
 .css-dtls td:nth-child(25) {background-color:#CCC;}
 </style>-->   
    
        
    <table cellspacing="0" >
        <tr class="form_caption">
            <td style="font-size:18px;" colspan="12">
                <? echo $company_library[$company_id]; ?>                                
            </td>
        </tr>
        <tr class="form_caption">
            <td style="font-size:16px; font-weight:bold"  colspan="12"> <? echo $report_title ;?></td>
        </tr>
        <tr class="form_caption">
            <td style="font-size:12px; font-weight:bold"  colspan="12"> <? if($start_date!="" && $end_date!="") echo "From ".change_date_format($start_date)." To ".change_date_format($end_date);?></td>
        </tr>
    </table>

    <div style="width:1620px;">
	<h1>Buyer Summary</h1>
    <table width="1600" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="left">    
    <thead>  
        <tr style="font-size:12px;">  
            <th width="30" rowspan="2">Sl</th>
            <th width="100" rowspan="2">Buyer Name</th>
            <th width="80" rowspan="2">Order Qty.(Pcs)</th>
            <th width="80">Bundle Info</th>
            <th width="80">Cutting QC</th>
            
            <th width="80">Print Issue</th>
            <th width="80">Print Rcv</th>
            <th width="80">Emb. Issue</th>
            <th width="80">Emb. Rcv</th>
            <th width="80">SP. Work Issue</th>
            <th width="80">SP. Work Rcv</th>
            
            <th width="80">Sew Input</th>
            <th width="80">Sew Output</th>
            <th width="80">Wash Issue</th>
            <th width="80">Wash Rcv</th>
            <th width="80">Iron Input</th>
            <th width="80">Total Balance</th>
            <th width="80" rowspan="2">Total Finish Gmts</th>
            <th width="80" rowspan="2">Rejection Qty</th>
            <th rowspan="2">Total Ship Out Qty</th>
        </tr>
        <tr style="font-size:12px;">
        <th>No. of Bundle /Qty</th>
        <th>No. of Bundle /Qty</th>
        <th>No. of Bundle /Qty</th>
        <th>No. of Bundle /Qty</th>
        <th>No. of Bundle /Qty</th>
        <th>No. of Bundle /Qty</th>
        <th>No. of Bundle /Qty</th>
        <th>No. of Bundle /Qty</th>
        <th>No. of Bundle /Qty</th>
        <th>No. of Bundle /Qty</th>
        <th>No. of Bundle /Qty</th>
        <th>No. of Bundle /Qty</th>
        <th>No. of Bundle /Qty</th>
        <th>No. of Bundle /Qty</th>			
        </tr>
    </thead>	
    </table>   
    </div>	
    <div style="width:1620px; max-height:200px; overflow-y:scroll; clear:both" id="scroll_body_summary">	
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1600" class="rpt_table" id="tbl_list_summary" align="left">  
		<?
        $i=0;
		foreach( $summary_data_arr as $buyer_id=>$buyer_data_arr)
        {
			$i++;
			$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
			$tot_cut_bundle_qty=0;	
			$tot_cut_order_qty=0;
			$tot_cutting_qc_qty=0;
			$tot_cutting_qc_bundle_qty=0;
			$tot_sewing_in_qty=0;
			$tot_sewing_in_bundle_qty=0;
			$tot_sewing_out_qty=0;
			$tot_sewing_out_bundle_qty=0;
			$tot_iron_qty=0;
			$tot_iron_bundle_qty=0;
			$tot_finish_gmts_qty=0;
			$tot_exfactory_qty=0;
				
			$tot_embroidery_issue_qty=0;
			$tot_embroidery_issue_bundle_qty=0;
			$tot_embroidery_rec_qty=0;
			$tot_embroidery_rec_bundle_qty=0;
			
			$tot_wash_issue_qty=0;
			$tot_wash_issue_bundle_qty=0;
			$tot_wash_rec_qty=0;
			$tot_wash_rec_bundle_qty=0;
			
			$tot_spacial_work_issue_qty=0;
			$tot_spacial_work_issue_bundle_qty=0;
			$tot_spacial_work_rec_qty=0;
			$tot_spacial_work_rec_bundle_qty=0;
			
			$tot_print_issue_qty=0;
			$tot_print_issue_bundle_qty=0;
			$tot_print_rec_qty=0;
			$tot_print_rec_bundle_qty=0;
			
			$buyer_tot_rej_qty=0;
				
			foreach($buyer_data_arr as $key=>$vue){
				$tot_cut_bundle_qty+=count($cut_data_arr[$key][tot_bundle]);	
				$tot_cut_order_qty+=$cut_data_arr[$key][tot_bundle_qty];
				
				$tot_cutting_qc_qty+=$cutting_qc_arr[$key][tot_qty];
				$tot_cutting_qc_bundle_qty+=count($cutting_qc_arr[$key][tot_bundle]);
				
				$tot_sewing_in_qty+=$sewing_in_arr[$key][tot_qty];
				$tot_sewing_in_bundle_qty+=count($sewing_in_arr[$key][tot_bundle]);
				
				$tot_sewing_out_qty+=$sewing_out_arr[$key][tot_qty];
				$tot_sewing_out_bundle_qty+=count($sewing_out_arr[$key][tot_bundle]);
				
				$tot_iron_qty+=$iron_arr[$key][tot_qty];
				$tot_iron_bundle_qty+=count($iron_arr[$key][tot_bundle]);
				
				$tot_finish_gmts_qty+=$finish_gmts_arr[$key][tot_qty];
				$tot_exfactory_qty+=$ship_out[$key];
				
				$tot_embroidery_issue_qty+=$embroidery_issue_arr[$key][tot_qty];
				$tot_embroidery_issue_bundle_qty+=count($embroidery_issue_arr[$key][tot_bundle]);
				$tot_embroidery_rec_qty+=$embroidery_rec_arr[$key][tot_qty];
				$tot_embroidery_rec_bundle_qty+=count($embroidery_rec_arr[$key][tot_bundle]);
				
				$tot_wash_issue_qty+=$wash_issue_arr[$key][tot_qty];
				$tot_wash_issue_bundle_qty+=count($wash_issue_arr[$key][tot_bundle]);
				$tot_wash_rec_qty+=$wash_rec_arr[$key][tot_qty];
				$tot_wash_rec_bundle_qty+=count($wash_rec_arr[$key][tot_bundle]);
				
				$tot_spacial_work_issue_qty+=$spacial_work_issue_arr[$key][tot_qty];
				$tot_spacial_work_issue_bundle_qty+=count($spacial_work_issue_arr[$key][tot_bundle]);
				$tot_spacial_work_rec_qty+=$spacial_work_rec_arr[$key][tot_qty];
				$tot_spacial_work_rec_bundle_qty+=count($spacial_work_rec_arr[$key][tot_bundle]);
				
				$tot_print_issue_qty+=$print_issue_arr[$key][tot_qty];
				$tot_print_issue_bundle_qty+=count($print_issue_arr[$key][tot_bundle]);
				$tot_print_rec_qty+=$print_rec_arr[$key][tot_qty];
				$tot_print_rec_bundle_qty+=count($print_rec_arr[$key][tot_bundle]);
				
				
				$buyer_tot_rej_qty+=$total_reject[$key];
				// $floor_name=$floor_id_array[$key][floor_id];
				
			}
			
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="trs_<? echo $i; ?>" onClick="change_color('trs_<? echo $i; ?>','<? echo $bgcolor; ?>')" style="cursor:pointer; font-size:13px"> 
                <td width="30" align="center"><? echo $i; ?></td>
                <td width="100"><? echo $buyer_arr[$buyer_id]; ?></td>
                <td width="80" align="center">
					<? echo array_sum($buyer_data_arr); ?>
                </td>
                <td width="80" align="center">
					<? echo $tot_cut_bundle_qty; ?><br>
					<? echo $tot_cut_order_qty; ?>
                </td>
                <td width="80" align="center">
					<? echo $tot_cutting_qc_bundle_qty; ?><br>
					<? echo $tot_cutting_qc_qty; ?>
                </td>
                <td width="80" align="center">
					<? echo $tot_print_issue_bundle_qty; ?><br>
					<? echo $tot_print_issue_qty; ?>
                </td>
                <td width="80" align="center">
					<? echo $tot_print_rec_bundle_qty; ?><br>
					<? echo $tot_print_rec_qty; ?>
                </td>
                <td width="80" align="center">
					<? echo $tot_embroidery_issue_bundle_qty; ?><br>
					<? echo $tot_embroidery_issue_qty; ?>
                </td>
                <td width="80" align="center">
					<? echo $tot_embroidery_rec_bundle_qty; ?><br>
					<? echo $tot_embroidery_rec_qty; ?>
                </td>
                <td width="80" align="center">
					<? echo $tot_spacial_work_issue_bundle_qty; ?><br>
					<? echo $tot_spacial_work_issue_qty; ?>
                </td>
                <td width="80" align="center">
					<? echo $tot_spacial_work_rec_bundle_qty; ?><br>
					<? echo $tot_spacial_work_rec_qty; ?>
                </td>
                <td width="80" align="center">
					<? echo $tot_sewing_in_bundle_qty;?><br>
                    <? echo $tot_sewing_in_qty;?>
                </td>
                <td width="80" align="center">
					<? echo $tot_sewing_out_bundle_qty; ?><br>
					<? echo $tot_sewing_out_qty; ?>
                </td>
                <td width="80" align="center">
					<? echo $tot_wash_issue_bundle_qty; ?><br>
					<? echo $tot_wash_issue_qty; ?>
                </td>
                <td width="80" align="center">
					<? echo $tot_wash_rec_bundle_qty; ?><br>
					<? echo $tot_wash_rec_qty; ?>
                </td>
                <td width="80" align="center">
					<? echo $tot_iron_bundle_qty; ?><br>
					<? echo $tot_iron_qty; ?>
                </td>
                <td width="80" align="center">
					<? echo $tot_cutting_qc_bundle_qty-$tot_iron_bundle_qty; ?><br>
					<? echo $tot_cutting_qc_qty-$tot_iron_qty; ?>
                </td>
                <td width="80" align="right"><? echo $tot_finish_gmts_qty; ?></td>
                <td width="80" align="right"><? echo $buyer_tot_rej_qty; ?></td>
                <td align="right"><? echo $tot_exfactory_qty; ?></td>
			</tr>
			<?
        }
        ?>
    </table>
    </div>

 <br>

    <table width="3400" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table css-dtls">    
    <thead>  
        <tr style="font-size:12px;">  
            <th width="30" rowspan="2">SL</th>
            <th width="100" rowspan="2">Buyer</th>
            <th width="80" rowspan="2">Job No</th>
            <th width="130" rowspan="2">Style Ref</th>
            <th width="100" rowspan="2">Internal ref number</th>
            <th width="100" rowspan="2">Group</th>
            <th width="60" rowspan="2">Img</th>
            <th width="130" rowspan="2">Item</th>
            <th width="100" rowspan="2">Order No</th>
            <th width="80" rowspan="2"><? echo $dateCaption;?></th>
            <th width="80" rowspan="2">Sew SMV</th>
            <th width="80" rowspan="2">Order Qty (Pcs)</th>
            <th width="80" rowspan="2">Stand Ex. Cut %</th>
            <th width="80" rowspan="2">Plan Cut Qty</th>
            <th width="80" rowspan="2">No. of Cut</th>
            
            <th width="80">Bundle Info</th>
            <th width="80">Cutting QC</th>
            <th width="80">Cutting Balance</th>
            
            <th width="80">Print Issue</th>
            <th width="80">Print Rcv</th>
            <th width="80">Print Balance</th>
           
            <th width="80">Emb. Issue</th>
            <th width="80">Emb. Rcv</th>
            <th width="80">Emb. Balance</th>
            
            <th width="80">SP. Work Issue</th>
            <th width="80">SP. Work Rcv</th>
            <th width="80">SP. Work Balance</th>
            
            <th width="80">Sew Input</th>
            <th width="80">Sew Output</th>
            <th width="80">Sew Balance</th>
            
            <th width="80">Wash Issue</th>
            <th width="80">Wash Rcv</th>
            <th width="80">Wash Balance</th>
            
            <th width="80">Iron Input</th>
            <th width="80">Total Balance</th>
            
            <th width="80" rowspan="2">Total Finish Gmts</th>
            <th width="80" rowspan="2">Rejection Qty</th>
            <th width="80" rowspan="2">Total Ship Out Qty</th>
            <th rowspan="2">Remarks</th>
        </tr>
        <tr style="font-size:12px;">
            <th>No. of Bundle /Qty</th>
            <th>No. of Bundle /Qty</th>
            <th>No. of Bundle /Qty</th>
            <th>No. of Bundle /Qty</th>
            <th>No. of Bundle /Qty</th>
            <th>No. of Bundle /Qty</th>
            <th>No. of Bundle /Qty</th>
            <th>No. of Bundle /Qty</th>
            <th>No. of Bundle /Qty</th>
            <th>No. of Bundle /Qty</th>
            <th>No. of Bundle /Qty</th>
            <th>No. of Bundle /Qty</th>
            <th>No. of Bundle /Qty</th>
            <th>No. of Bundle /Qty</th>
            <th>No. of Bundle /Qty</th>
            <th>No. of Bundle /Qty</th>
            <th>No. of Bundle /Qty</th>
            <th>No. of Bundle /Qty</th>
            <th>No. of Bundle /Qty</th>
            <th>No. of Bundle /Qty</th>
        </tr>
    </thead>	
    </table>   
    
    <div style="width:3420px; max-height:320px; overflow-y:scroll" id="scroll_body" align="left">	
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="3400" class="rpt_table css-dtls" id="tbl_list_search">  
		<?
        $i=0;
		foreach( $data_arr as $key=>$row)
        {
			$i++;
			$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" style="cursor:pointer; font-size:13px;"> 
                <td width="30" align="center"><? echo $i; ?></td>
                <td width="100"><? echo $buyer_arr[$row['buyer_name']]; ?></td>
                <td width="80" align="center"><? echo $row['job_no']; ?></td>
                <td width="130"><p><? echo $row['style_ref_no']; ?></p></td>
                <td width="100"><p><? echo $row['grouping']; ?></p></td>
                <td width="100"><p>
					<? 
					   if(empty($cbo_floor_group))
					   {
					    echo $floor_group_array[$cut_data_arr[$key]['floor_group']]; 
					   }
					   else
					   {
						echo $floor_group_array[$row['floor_id']]; 
					   }
					?>
				</p></td>
                <td width="60" align="center" valign="middle"><img src="../../<? echo $photo_arr[$row['job_no']]; ?>" height="40"  width="50" title="" alt="No Image"></td>
                <td width="130"><p><? echo $garments_item[$row['gmt_item_id']]; ?></p></td>
                <td width="100"><p><? echo $row['po_number']; ?></p></td>
                <td width="80" align="center">
					<? 
                    if($cbo_date_category==3){echo change_date_format($cut_ent_date_arr[$key]);}
					else{echo change_date_format($row['shipment_date']);} 
                    ?>
                </td>
                <td width="80" align="center"><? echo $row['smv_set']; ?></td>
                <td width="80" align="right"><? echo $order_qty_arr[$key][oq];$tot_order_qty+=$order_qty_arr[$key][oq]; ?></td>
                <td width="80" align="right"><? echo $order_qty_arr[$key][ecq]; ?></td>
                <td width="80" align="right"><? echo $order_qty_arr[$key][pcq];$tot_plan_cut_qty+=$order_qty_arr[$key][pcq]; ?></td>
                <td width="80" align="center">
					<? $number_of_cut=count($cut_data_arr[$key][tot_cut]);$tot_number_of_cut+= $number_of_cut; ?>
                    <a href="javascript:fn_popup(2,'<? echo implode(',',$cut_data_arr[$key][cutting_id]);?>','','','','','','','<? echo $row['job_no'].'_'.$row['po_number'].'_'.$garments_item[$row['gmt_item_id']].'_'.$row['buyer_name'].'_'.$row['style_ref_no'].'_'.$order_qty_arr[$key][pcq];?>')"><? echo $number_of_cut; ?></a>
                </td>
                <td width="80" align="center">
                    <a href="javascript:fn_popup(2,'<? echo implode(',',$cut_data_arr[$key][cutting_id]);?>','','','','','','','<? echo $row['job_no'].'_'.$row['po_number'].'_'.$garments_item[$row['gmt_item_id']].'_'.$row['buyer_name'].'_'.$row['style_ref_no'].'_'.$order_qty_arr[$key][pcq];?>')">
						<? echo count($cut_data_arr[$key][tot_bundle]); ?><br>
                        <? echo $cut_data_arr[$key][tot_bundle_qty]; ?>
					</a>
                </td>
                <td width="80" align="center">
                    <a href="javascript:fn_popup(3,'<? echo implode(',',$cut_data_arr[$key][cutting_id]);?>','','','','','','','<? echo $row['job_no'].'_'.$row['po_number'].'_'.$garments_item[$row['gmt_item_id']].'_'.$row['buyer_name'].'_'.$row['style_ref_no'].'_'.$order_qty_arr[$key][pcq];?>','')">
						<? echo count($cutting_qc_arr[$key][tot_bundle]); ?><br>
                        <? echo $cutting_qc_arr[$key][tot_qty]; ?>
                    </a>
                </td>
                <td width="80" align="center">
                    <a href="javascript:fn_popup(4,'<? echo implode(',',$cut_data_arr[$key][cutting_id]).'*'.implode(',',$cutting_qc_arr[$key][tot_bundle_id]);?>','','','','','','','<? echo $row['job_no'].'_'.$row['po_number'].'_'.$garments_item[$row['gmt_item_id']].'_'.$row['buyer_name'].'_'.$row['style_ref_no'].'_'.$order_qty_arr[$key][pcq];?>','')">
						<? echo count($cut_data_arr[$key][tot_bundle])-count($cutting_qc_arr[$key][tot_bundle]); ?><br>
                        <? echo $cut_data_arr[$key][tot_bundle_qty]-$cutting_qc_arr[$key][tot_qty]; ?>
                    </a>
                </td>
               
               
                <td width="80" align="center">
                    <a href="javascript:fn_popup(5,'<? echo implode(',',$print_issue_arr[$key][tot_bundle_id]);?>','','','','','','','<? echo $row['job_no'].'_'.$row['po_number'].'_'.$garments_item[$row['gmt_item_id']].'_'.$row['buyer_name'].'_'.$row['style_ref_no'].'_'.$order_qty_arr[$key][pcq];?>','')">
						<? echo count($print_issue_arr[$key][tot_bundle]); ?><br>
                        <? echo $print_issue_arr[$key][tot_qty]; ?>
                    </a>
					
                </td>
                <td width="80" align="center">
                    <a href="javascript:fn_popup(6,'<? echo implode(',',$print_issue_rec_arr[$key][tot_bundle_id]);?>','','','','','','','<? echo $row['job_no'].'_'.$row['po_number'].'_'.$garments_item[$row['gmt_item_id']].'_'.$row['buyer_name'].'_'.$row['style_ref_no'].'_'.$order_qty_arr[$key][pcq];?>','')">
						<? echo count($print_rec_arr[$key][tot_bundle]); ?><br>
                        <? echo $print_rec_arr[$key][tot_qty]; ?>
                    </a>
					
                </td>
                <td width="80" align="center">
                    <a href="javascript:fn_popup(7,'<? echo implode(',',$print_issue_rec_arr[$key][tot_bundle_id]);?>','','','','','','','<? echo $row['job_no'].'_'.$row['po_number'].'_'.$garments_item[$row['gmt_item_id']].'_'.$row['buyer_name'].'_'.$row['style_ref_no'].'_'.$order_qty_arr[$key][pcq];?>','')">
						<? echo count($print_issue_arr[$key][tot_bundle])-count($print_rec_arr[$key][tot_bundle]); ?><br>
                        <? echo $print_issue_arr[$key][tot_qty]-$print_rec_arr[$key][tot_qty]; ?>
                    </a>
                </td>

               
                <td width="80" align="center">
                    <a href="javascript:fn_popup(10,'<? echo implode(',',$embroidery_issue_arr[$key][tot_bundle_id]);?>','','','','','','','<? echo $row['job_no'].'_'.$row['po_number'].'_'.$garments_item[$row['gmt_item_id']].'_'.$row['buyer_name'].'_'.$row['style_ref_no'].'_'.$order_qty_arr[$key][pcq];?>','')">
						<? echo count($embroidery_issue_arr[$key][tot_bundle]); ?><br>
                        <? echo $embroidery_issue_arr[$key][tot_qty]; ?>
                    </a>
                </td>
                <td width="80" align="center">
                    <a href="javascript:fn_popup(11,'<? echo implode(',',$embroidery_issue_rec_arr[$key][tot_bundle_id]);?>','','','','','','','<? echo $row['job_no'].'_'.$row['po_number'].'_'.$garments_item[$row['gmt_item_id']].'_'.$row['buyer_name'].'_'.$row['style_ref_no'].'_'.$order_qty_arr[$key][pcq];?>','')">
						<? echo count($embroidery_rec_arr[$key][tot_bundle]); ?><br>
                        <? echo $embroidery_rec_arr[$key][tot_qty]; ?>
                    </a>
                </td>
                <td width="80" align="center">
                    <a href="javascript:fn_popup(12,'<? echo implode(',',$embroidery_issue_rec_arr[$key][tot_bundle_id]);?>','','','','','','','<? echo $row['job_no'].'_'.$row['po_number'].'_'.$garments_item[$row['gmt_item_id']].'_'.$row['buyer_name'].'_'.$row['style_ref_no'].'_'.$order_qty_arr[$key][pcq];?>','')">
						<? echo count($embroidery_issue_arr[$key][tot_bundle])-count($embroidery_rec_arr[$key][tot_bundle]); ?><br>
                        <? echo $embroidery_issue_arr[$key][tot_qty]-$embroidery_rec_arr[$key][tot_qty]; ?>
                    </a>
					
                </td>
                
                <td width="80" align="center">
                    <a href="javascript:fn_popup(13,'<? echo implode(',',$spacial_work_issue_arr[$key][tot_bundle_id]);?>','','','','','','','<? echo $row['job_no'].'_'.$row['po_number'].'_'.$garments_item[$row['gmt_item_id']].'_'.$row['buyer_name'].'_'.$row['style_ref_no'].'_'.$order_qty_arr[$key][pcq];?>','')">
						<? echo count($spacial_work_issue_arr[$key][tot_bundle]); ?><br>
                        <? echo $spacial_work_issue_arr[$key][tot_qty]; ?>
                    </a>
                </td>
                <td width="80" align="center">
                    <a href="javascript:fn_popup(14,'<? echo implode(',',$spacial_work_issue_rec_arr[$key][tot_bundle_id]);?>','','','','','','','<? echo $row['job_no'].'_'.$row['po_number'].'_'.$garments_item[$row['gmt_item_id']].'_'.$row['buyer_name'].'_'.$row['style_ref_no'].'_'.$order_qty_arr[$key][pcq];?>','')">
						<? echo count($spacial_work_rec_arr[$key][tot_bundle]); ?><br>
                        <? echo $spacial_work_rec_arr[$key][tot_qty]; ?>
                    </a>
                </td>
                <td width="80" align="center">
                    <a href="javascript:fn_popup(15,'<? echo implode(',',$spacial_work_issue_rec_arr[$key][tot_bundle_id]);?>','','','','','','','<? echo $row['job_no'].'_'.$row['po_number'].'_'.$garments_item[$row['gmt_item_id']].'_'.$row['buyer_name'].'_'.$row['style_ref_no'].'_'.$order_qty_arr[$key][pcq];?>','')">
						<? echo count($spacial_work_issue_arr[$key][tot_bundle])-count($spacial_work_rec_arr[$key][tot_bundle]); ?><br>
                        <? echo $spacial_work_issue_arr[$key][tot_qty]-$spacial_work_rec_arr[$key][tot_qty]; ?>
                    </a>
					
                </td>
                
                
                <td width="80" align="center">
                    <a href="javascript:fn_popup(8,'<? echo implode(',',$sewing_input_id_arr[$key][tot_bundle_id]);?>','','','','','','','<? echo $row['job_no'].'_'.$row['po_number'].'_'.$garments_item[$row['gmt_item_id']].'_'.$row['buyer_name'].'_'.$row['style_ref_no'].'_'.$order_qty_arr[$key][pcq].'_'.$cbo_company_id;?>','')">
						<? echo count($sewing_in_arr[$key][tot_bundle]); ?><br>
                        <? echo $sewing_in_arr[$key][tot_qty]; ?>
                    </a>
                
                </td>
                <td width="80" align="center">
                    <a href="javascript:fn_popup(9,'<? echo implode(',',$sewing_in_out_arr[$key][tot_bundle_id]);?>','','','','','','','<? echo $row['job_no'].'_'.$row['po_number'].'_'.$garments_item[$row['gmt_item_id']].'_'.$row['buyer_name'].'_'.$row['style_ref_no'].'_'.$order_qty_arr[$key][pcq].'_'.$cbo_company_id;?>','')">
						<? echo count($sewing_out_arr[$key][tot_bundle]); ?><br>
                        <? echo $sewing_out_arr[$key][tot_qty]; ?>
                    </a>
                </td>
                <td width="80" align="center">
                    <a href="javascript:fn_popup(19,'<? echo implode(',',$sewing_in_out_arr[$key][tot_bundle_id]);?>','','','','','','','<? echo $row['job_no'].'_'.$row['po_number'].'_'.$garments_item[$row['gmt_item_id']].'_'.$row['buyer_name'].'_'.$row['style_ref_no'].'_'.$order_qty_arr[$key][pcq].'_'.$cbo_company_id;?>','')">
						<? echo count($sewing_in_arr[$key][tot_bundle])-count($sewing_out_arr[$key][tot_bundle]); ?><br>
                        <? echo $sewing_in_arr[$key][tot_qty]-$sewing_out_arr[$key][tot_qty]; ?>
                    </a>
                </td>
                
                <td width="80" align="center">
                    <a href="javascript:fn_popup(16,'<? echo implode(',',$wash_issue_arr[$key][tot_bundle_id]);?>','','','','','','','<? echo $row['job_no'].'_'.$row['po_number'].'_'.$garments_item[$row['gmt_item_id']].'_'.$row['buyer_name'].'_'.$row['style_ref_no'].'_'.$order_qty_arr[$key][pcq];?>','')">
						<? echo count($wash_issue_arr[$key][tot_bundle]); ?><br>
                        <? echo $wash_issue_arr[$key][tot_qty]; ?>
                    </a>
                </td>
                <td width="80" align="center">
                    <a href="javascript:fn_popup(17,'<? echo implode(',',$wash_issue_rec_arr[$key][tot_bundle_id]);?>','','','','','','','<? echo $row['job_no'].'_'.$row['po_number'].'_'.$garments_item[$row['gmt_item_id']].'_'.$row['buyer_name'].'_'.$row['style_ref_no'].'_'.$order_qty_arr[$key][pcq];?>','')">
						<? echo count($wash_rec_arr[$key][tot_bundle]); ?><br>
                        <? echo $wash_rec_arr[$key][tot_qty]; ?>
                    </a>
                </td>
                <td width="80" align="center">
                    <a href="javascript:fn_popup(18,'<? echo implode(',',$wash_issue_rec_arr[$key][tot_bundle_id]);?>','','','','','','','<? echo $row['job_no'].'_'.$row['po_number'].'_'.$garments_item[$row['gmt_item_id']].'_'.$row['buyer_name'].'_'.$row['style_ref_no'].'_'.$order_qty_arr[$key][pcq];?>','')">
						<? echo count($wash_issue_arr[$key][tot_bundle])-count($wash_rec_arr[$key][tot_bundle]); ?><br>
                        <? echo $wash_issue_arr[$key][tot_qty]-$wash_rec_arr[$key][tot_qty]; ?>
                    </a>
					
                </td>
                
                
                <td width="80" align="center">
                    <a href="javascript:fn_popup(20,'<? echo implode(',',$iron_arr[$key][tot_bundle_id]);?>','','','','','','','<? echo $row['job_no'].'_'.$row['po_number'].'_'.$garments_item[$row['gmt_item_id']].'_'.$row['buyer_name'].'_'.$row['style_ref_no'].'_'.$order_qty_arr[$key][pcq];?>','')">
						<? echo count($iron_arr[$key][tot_bundle]); ?><br>
                        <? echo $iron_arr[$key][tot_qty]; ?>
                    </a>
                </td>
                <td width="80" align="center">
				<? echo count($cutting_qc_arr[$key][tot_bundle])-count($iron_arr[$key][tot_bundle]); ?><br>
				<? echo $cutting_qc_arr[$key][tot_qty]-$iron_arr[$key][tot_qty]; ?>
                </td>
                <td width="80" align="right"><? echo $finish_gmts_arr[$key][tot_qty];$tot_fin_qty+=$finish_gmts_arr[$key][tot_qty]; ?></td>
                <td width="80" align="right"><a href="javascript:fn_popup(1,'','','<? echo $iron_arr[$key][rej_qty];?>','','','','','<? echo $row['job_no'].'_'.$row['po_number'].'_'.$garments_item[$row['gmt_item_id']];?>','<? echo implode(',',$rejected_order_arr[$key]);?>')"><? echo $total_reject[$key]; ?></a></td>
                <td width="80" align="right"><? echo $ship_out[$key];$tot_ship_out+=$ship_out[$key]; ?></td>
                <td><p><? echo $row['remarks']; ?></p></td>
			</tr>
			<?
        }
        ?>
    </table>
    </div>
    
    
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="3400" class="rpt_table">  
			<tfoot style="font-size:13px;"> 
                <th colspan="10">Total</th>
                <th width="80" align="right" id="tot_order_qty"></th>
                <th width="80"></th>
                <th width="80" align="right" id="tot_plan_cut_qty"></th>
                <th width="80" id="tot_number_of_cut" style="text-align:center;"></th>
                <th width="80" align="center"></th>
                <th width="80" align="center"></th>
                <th width="80" align="center"></th>
                <th width="80" align="center"></th>
                <th width="80" align="center"></th>
                <th width="80" align="center"></th>
                <th width="80" align="center"></th>
                <th width="80" align="center"></th>
                <th width="80" align="center"></th>
                <th width="80" align="center"></th>
                <th width="80" align="center"></th>
                <th width="80" align="center"></th>
                <th width="80" align="center"></th>
                <th width="80" align="center"></th>
                <th width="80" align="center"></th>
                <th width="80" align="center"></th>
                <th width="80" align="center"></th>
                <th width="80" align="center"></th>
                <th width="80" align="center"></th>
                <th width="80" align="center"></th>
                <th width="80" align="right" id="tot_fin_qty"></th>
                <th width="80" align="right" id="tot_rej_qty"></th>
                <th width="80" align="right" id="tot_ship_out"></th>
                <th width="212" align="right"></th>
			</tfoot>
    </table>
    
    
    
    
    
    <?
	        
	foreach (glob("$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename="".$user_name."_".$name.".xls";
	echo "$total_data####$filename";
	
	exit();
		
}

if($action=="order_no_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>
		function js_set_value( str ) 
		{
			$('#hide_order_no').val(str);
			parent.emailwindow.hide();
		}
    </script>
	</head>

	<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:780px;">
				<table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th>Buyer</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="170">Please Enter Order No</th>
						<th>Shipment Date</th>
						<th>
							<input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;">
							<input type="hidden" name="hide_order_no" id="hide_order_no" value="" /> 
						</th> 
					</thead>
					<tbody>
						<tr>
							<td align="center">
								<? 
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 );
								?>
							</td>                 
							<td align="center">	
							<?
								$search_by_arr=array(1=>"Order No",2=>"Style Ref",3=>"Job No");
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
								echo create_drop_down( "cbo_search_by", 110, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
							</td>     
							<td align="center" id="search_by_td">				
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
							</td> 
							<td align="center">
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
								<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
							</td>	
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_order_no_search_list_view', 'search_div', 'gst_print_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td colspan="5" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
						</tr>
					</tbody>
				</table>
				<div style="margin-top:15px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit(); 
}

if($action=="job_search_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	//echo $data;//die;
	$data_arr = explode("_",$data);

	?>
   <script>
		function js_set_value( str ) 
		{
			
			$('#txt_selected_no').val(str);
			parent.emailwindow.hide();
		}
    </script>
		

    <?
	$company=$data_arr[0];
	$buyer=$data_arr[1];
	//echo  $buyer ."/".$company ;
	//$job_year=str_replace("'","",$job_year);
	if($buyer!=0) $buyer_cond=" and a.buyer_name=$buyer"; else $buyer_cond="";
	if($db_type==0)
	{
		if($job_year!=0) $job_year_cond=" and year(a.insert_date)=$job_year"; else $job_year_cond="";
		$select_date=" year(a.insert_date)";
	}
	else if($db_type==2)
	{
		if($job_year!=0) $job_year_cond=" and to_char(a.insert_date,'YYYY')=$job_year"; else $job_year_cond="";
		$select_date=" to_char(a.insert_date,'YYYY')";
	}
	
	 $sql = "SELECT a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num,$select_date as year from wo_po_details_master a where a.company_name=$company $buyer_cond  and is_deleted=0 order by a.id desc"; 
	//echo $sql; die;
	echo create_list_view("list_view", "Style Ref No,Job No,Year","200,150,100","600","400",0, $sql , "js_set_value", "id,job_no,style_ref_no", "", 1, "0", $arr, "style_ref_no,job_no_prefix_num,year", "","setFilterGrid('list_view',-1)","0","","") ;	
	//echo "<input type='hidden' id='txt_selected_id' />";
	//echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	
	?>
    <script language="javascript" type="text/javascript">
	var style_no='<? echo $txt_ref_no;?>';
	var style_id='<? echo $txt_style_ref_id;?>';
	var style_des='<? echo $txt_style_ref_no;?>';
	//alert(style_id);
	if(style_no!="")
	{
		style_no_arr=style_no.split(",");
		style_id_arr=style_id.split(",");
		style_des_arr=style_des.split(",");
		var str_ref="";
		for(var k=0;k<style_no_arr.length; k++)
		{
			str_ref=style_no_arr[k]+'_'+style_id_arr[k]+'_'+style_des_arr[k];
			js_set_value(str_ref);
		}
	}
	</script>
    
    <?
	
	exit();
}



if($action=="reject_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$order_string=$bundle;
	$sql_cutt_qc="select a.po_break_down_id,a.item_number_id,b.production_qnty,b.reject_qty,b.alter_qty,b.spot_qty,b.replace_qty,b.bundle_no,a.production_type,a.embel_name from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.po_break_down_id in($order_string)  and a.production_type in(1,2,3,4,5,7,8)";  //and b.bundle_no is not null
	$sql_cutt_qc_result=sql_select($sql_cutt_qc);
	foreach($sql_cutt_qc_result as $rows)
	{
		$key=$rows[csf('po_break_down_id')].'_'.$rows[csf('item_number_id')];
		if($rows[csf('production_type')]==1 && $rows[csf('bundle_no')]!=''){
			$cutting_out_arr[$rows[csf('bundle_no')]]=$rows[csf('reject_qty')];
		}
		elseif($rows[csf('production_type')]==5 && $rows[csf('bundle_no')]!=''){
			$sewing_out_arr[$rows[csf('bundle_no')]]=$rows[csf('reject_qty')];
		}
		elseif($rows[csf('production_type')]==7 && $rows[csf('bundle_no')]!=''){
			$iron_data_arr[$rows[csf('bundle_no')]]=$rows[csf('reject_qty')];
		}
		//emb........................................start;
		elseif($rows[csf('production_type')]==3 && $rows[csf('embel_name')]==1 && $rows[csf('bundle_no')]!=''){
			$printed_data_arr[$rows[csf('bundle_no')]]=$rows[csf('reject_qty')];
		}
		
		elseif($rows[csf('production_type')]==3 && $rows[csf('embel_name')]==2 && $rows[csf('bundle_no')]!=''){
			$emb_data_arr[$rows[csf('bundle_no')]]=$rows[csf('reject_qty')];
		}
		elseif($rows[csf('production_type')]==3 && $rows[csf('embel_name')]==3 && $rows[csf('bundle_no')]!=''){
			$wash_data_arr[$rows[csf('bundle_no')]]=$rows[csf('reject_qty')];
		}
		elseif($rows[csf('production_type')]==3 && $rows[csf('embel_name')]==4 && $rows[csf('bundle_no')]!=''){
			$sp_work_data_arr[$rows[csf('bundle_no')]]=$rows[csf('reject_qty')];
		}
		if($rows[csf('reject_qty')]){$rejected_bundle_arr[$rows[csf('bundle_no')]]=$rows[csf('bundle_no')];}
	}
	
	
	?>
	<script>
	
		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_job_id").val(splitData[0]); 
			$("#hide_job_no").val(splitData[1]); 
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:620px;">
             <?
			 	list($job,$order,$item)=explode('_',$job_info);
			 ?>
             <table width="100%" border="1" rules="all">
            	<tr>
                    <td width="40"><strong>Job : </strong></td>
                    <td width="100"><? echo $job;?></td>
                    <td width="40"><strong>Order : </strong></td>
                    <td width="100"><? echo $order;?></td>
                    <td width="40"><strong>Item :</strong> </td>
                    <td><? echo $item;?></td>
                </tr>
           </table> 
            <? if(count($rejected_bundle_arr)==0){ echo "<br><h1>Not Found Any Bundle.</h1>";exit();}?>
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
            	<thead>
                    <th width="30">SL</th>
                    <th width="70">Bundle NO</th>
                    <th width="60">Cutting</th>
                    <th width="60">Print</th>
                    <th width="60">Emb.</th>
                    <th width="60">Wash.</th>
                    <th width="60">SP. Work</th>
                    <th width="60">Sew</th>
                    <th width="60">Iron</th>
                    <th>Total</th>
                </thead>
             </table>   
            <div style="max-height:300px; overflow-y:scroll;">
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
                <tbody>
                	<? 
					$i=0;
					foreach($rejected_bundle_arr as $bundle){
						$i++;
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" style="cursor:pointer;">
                        <td align="center" width="30"><? echo $i;?></td>                 
                        <td align="center" width="70"><? echo $bundle;?></td>                 
                        <td align="right" width="60"><? echo $cutting_out_arr[$bundle];$tot_cutting+=$cutting_out_arr[$bundle];?></td>                 
                       
                        <td align="right" width="60"><? echo $printed_data_arr[$bundle];$tot_print+=$printed_data_arr[$bundle];?></td>                 
                        <td align="right" width="60"><? echo $emb_data_arr[$bundle];$tot_emb+=$emb_data_arr[$bundle];?></td>                 
                        <td align="right" width="60"><? echo $wash_data_arr[$bundle];$tot_wash+=$wash_data_arr[$bundle];?></td>                 
                        <td align="right" width="60"><? echo $sp_work_data_arr[$bundle];$tot_sp_work+=$sp_work_data_arr[$bundle];?></td>                 


                        <td align="right" width="60"><? echo $sewing_out_arr[$bundle];$tot_sewing_out+=$sewing_out_arr[$bundle];?></td>                 
                        <td align="right" width="60"><? echo $iron_data_arr[$bundle];$tot_iron+=$iron_data_arr[$bundle];?></td>                 
                        <td align="right"><? $reject_grand = ($cutting_out_arr[$bundle]+$sewing_out_arr[$bundle]+$iron_data_arr[$bundle]+$printed_data_arr[$bundle]+$emb_data_arr[$bundle]+$wash_data_arr[$bundle]+$sp_work_data_arr[$bundle]); echo $reject_grand; $reject_grand_total+=$reject_grand;?></td>                 
                    </tr>
                    <? } ?>
            	</tbody>
             </table>
             </div>  
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
                <tfoot>
                    <th colspan="2">Total</th>                 
                    <th align="right" width="60"><? echo $tot_cutting;?></th>                 
                    <th align="right" width="60"><?  echo $tot_print;?></th>                 
                    <th align="right" width="60"><?  echo $tot_emb;?></th>                 
                    <th align="right" width="60"><?  echo $tot_wash;?></th>                 
                    <th align="right" width="60"><?  echo $tot_sp_work;?></th>                 
                    <th align="right" width="60"><? echo $tot_sewing_out;?></th>                 
                    <th align="right" width="60"><? echo $tot_iron;?></th>                 
                    <th align="right" width="89"><? echo $reject_grand_total;?>&nbsp;&nbsp;</th>                 
                </tfoot>
           	</table>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}


if($action=="cutting_dtls_popup")
{
	echo load_html_head_contents("Cutting Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	list($job,$order,$item,$buyer,$style,$cutQty)=explode('_',$job_info);
	$cutting=$_SESSION['bundle_id'];
	// $sql_cut="select 
	// 			d.entry_date,
	// 			d.cutting_no,
	// 			count(f.bundle_no) as no_of_bundle,
	// 			sum(f.size_qty) as qty,
	// 			g.location_id,
	// 			g.floor_id
	// 		from
	// 			ppl_cut_lay_mst d, 
	// 			ppl_cut_lay_dtls e,
	// 			ppl_cut_lay_bundle f,
	// 			lib_cutting_table g
	// 		 where 
	// 			d.table_no=g.id and
	// 			d.entry_form=77 and
	// 			d.id=e.mst_id and
	// 			f.mst_id=d.id and 
	// 			f.dtls_id=e.id and
	// 			d.id in($cutting)
	// 		group by d.entry_date,d.cutting_no,g.location_id,g.floor_id
	// 		order by d.entry_date
	// 			";
	$sql_cut="select 
				d.entry_date,
				d.cutting_no,
				count(f.bundle_no) as no_of_bundle,
				sum(f.size_qty) as qty,
				g.location_id,
				g.floor_id
			from
				ppl_cut_lay_mst d, 
				ppl_cut_lay_dtls e,
				ppl_cut_lay_bundle f,
				lib_cutting_table g
			 where 
				d.table_no=g.id and
				d.id=e.mst_id and
				f.mst_id=d.id and 
				f.dtls_id=e.id and
				  d.is_deleted = 0 and 
				 d.status_active = 1 and
				 e.is_deleted = 0 and 
				 e.status_active = 1 and
				 f.is_deleted = 0 and 
				 f.status_active = 1 and
				 g.is_deleted = 0 and 
				 g.status_active = 1 and
				d.id in($cutting)
				and d.entry_form=289 
			group by d.entry_date,d.cutting_no,g.location_id,g.floor_id
			order by d.entry_date
				";
				//echo $sql_cut;die;
		$sql_cut_result=sql_select( $sql_cut );
		$cutting_data_arr=array();
		foreach($sql_cut_result as $rows)
		{	
			$cutting_data_arr[$rows[csf('cutting_no')]]=array(
				cutting_no=>$rows[csf('cutting_no')],
				entry_date=>$rows[csf('entry_date')],
				no_of_bundle=>$rows[csf('no_of_bundle')],
				location_id=>$rows[csf('location_id')],
				qty=>$rows[csf('qty')]
			);
		}
		
	?>
	<script>
	
		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_job_id").val(splitData[0]); 
			$("#hide_job_no").val(splitData[1]); 
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
             <table width="100%" border="1" rules="all">
            	<tr>
                    <td width="40"><strong>Buyer</strong> </td>
                    <td>: <? echo $buyer_arr[$buyer];?></td>
                    <td width="40"><strong>Style</strong> </td>
                    <td>: <? echo $style;?></td>
                    <td width="70"><strong>Job</strong></td>
                    <td>: <? echo $job;?></td>
                </tr>
            	<tr>
                    <td><strong>Order</strong></td>
                    <td>: <? echo $order;?></td>
                    <td><strong>Item</strong> </td>
                    <td>: <? echo $item;?></td>
                    <td><strong>Plan Cut Qty</strong> </td>
                    <td>: <? echo $cutQty;?></td>
                </tr>
           </table> 
            <? // if($cutting_data_arr[0]==''){ echo "<br><h1>Not Found Any Bundle.</h1>";exit();}?>
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
            	<thead>
                    <th width="30">SL</th>
                    <th width="100">Cut No</th>
                    <th width="130">Location</th>
                    <th width="80">Cutting Date</th>
                    <th width="60">No. of Bundle</th>
                    <th>Bundle Qty</th>
                </thead>
             </table>   
            <div style="max-height:300px; overflow-y:scroll;">
            <table width="100%" cellspacing="3" cellpadding="3" border="1" rules="all" class="rpt_table" id="tbl_list">
                <tbody>
                	<? 
					$i=0;
					foreach($cutting_data_arr as $cutting_no=>$data_arr){
						$i++;
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" style="cursor:pointer;">
                        <td align="center" width="30"><? echo $i;?></td>                 
                        <td align="center" width="100"><? echo $cutting_no;?></td>                 
                        <td width="130"><? echo $location_arr[$data_arr[location_id]];?></td>
                        <td align="center" width="80"><? echo change_date_format($data_arr[entry_date]);?></td>
                        <td align="right" width="60"><? echo $data_arr[no_of_bundle];$tot_no_bun+=$data_arr[no_of_bundle];?></td>                 
                        <td align="right"><? echo $data_arr[qty]; $tot_qty+=$data_arr[qty];?> </td>                 
                    </tr>
                    <? } ?>
            	</tbody>
             </table>
             </div>  
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
                <tfoot>
                    <th colspan="3">Total </th>                 
                    <th align="right" width="60"><?  echo $tot_no_bun;?></th>                 
                    <th width="144" align="right"><? echo $tot_qty;?>&nbsp;&nbsp;</th>                 
                </tfoot>
           	</table>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}


if($action=="cutting_qc_dtls_popup")
{
	echo load_html_head_contents("Cutting QC Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	list($job,$order,$item,$buyer,$style,$cutQty)=explode('_',$job_info);
	$cutting=$_SESSION['bundle_id'];
	
	if($db_type==0){
		$order_by=" ORDER BY b.bundle_no";
	}
	else if($db_type==2){
		// $order_by=" ORDER BY TO_NUMBER(REPLACE(SUBSTR(b.bundle_no, INSTR(b.bundle_no, '-')+1),'-',''))";
		$order_by=" ORDER BY REPLACE(SUBSTR(b.bundle_no, INSTR(b.bundle_no, '-')+1),'-','')";
	}
	
	
	$sql_cutt_qc="SELECT a.cutting_qc_no, a.cutting_qc_date,b.bundle_no,sum(b.qc_pass_qty) as qc_pass_qty,sum(b.bundle_qty) as bundle_qty,d.location_id
    FROM pro_gmts_cutting_qc_mst a,pro_gmts_cutting_qc_dtls b, ppl_cut_lay_mst c,lib_cutting_table d
    where c.table_no=d.id and a.id=b.mst_id and a.cutting_no=c.cutting_no and a.job_no=c.job_no and c.id in($cutting) group by a.cutting_qc_no, a.cutting_qc_date,b.bundle_no,d.location_id $order_by";	
	// echo $sql_cutt_qc;
	
	
	$sql_cutt_qc_result=sql_select($sql_cutt_qc);
	foreach($sql_cutt_qc_result as $rows)
	{
		$cutting_qc_arr[$rows[csf('bundle_no')]]=array(
			cutting_qc_no=>$rows[csf('cutting_qc_no')],
			cutting_qc_date=>$rows[csf('cutting_qc_date')],
			bundle_no=>$rows[csf('bundle_no')],
			bundle_qty=>$rows[csf('bundle_qty')],
			location_id=>$rows[csf('location_id')],
			qc_pass_qty=>$rows[csf('qc_pass_qty')]
		);
	}
	
	
	
	?>
	<script>
	
		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_job_id").val(splitData[0]); 
			$("#hide_job_no").val(splitData[1]); 
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
             <table width="100%" border="1" rules="all">
            	<tr>
                    <td width="40"><strong>Buyer</strong> </td>
                    <td>: <? echo $buyer_arr[$buyer];?></td>
                    <td width="40"><strong>Style</strong> </td>
                    <td>: <? echo $style;?></td>
                    <td width="70"><strong>Job</strong></td>
                    <td>: <? echo $job;?></td>
                </tr>
            	<tr>
                    <td><strong>Order</strong></td>
                    <td>: <? echo $order;?></td>
                    <td><strong>Item</strong> </td>
                    <td>: <? echo $item;?></td>
                    <td><strong>Plan Cut Qty</strong> </td>
                    <td>: <? echo $cutQty;?></td>
                </tr>
           </table> 
            <? // if($cutting_data_arr[0]==''){ echo "<br><h1>Not Found Any Bundle.</h1>";exit();}?>
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
            	<thead>
                    <th width="30">SL</th>
                    <th width="100">Cut QC No</th>
                    <th width="130">Location</th>
                    <th width="80">Cutting QC Date </th>
                    <th width="80">Bundle No</th>
                    <th width="80">Bundle Qty</th>
                    <th>QC Pass Qty</th>
                </thead>
             </table>   
            <div style="max-height:280px; overflow-y:scroll;">
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
                <tbody>
                	<? 
					$i=0;
					foreach($cutting_qc_arr as $cutting_no=>$row){
						$i++;
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" style="cursor:pointer;">
                        <td align="center" width="30"><? echo $i;?></td>                 
                        <td align="center" width="100"><? echo $row[cutting_qc_no];?></td>  
                        <td width="130"><? echo $location_arr[$row[location_id]];?></td>
                        <td align="center" width="80"><? echo change_date_format($row[cutting_qc_date]);?></td>
                        <td align="center" width="80"><? echo $row[bundle_no];?></td>                 
                        <td align="right" width="80"><? echo $row[bundle_qty]; $tot_bun_qty+=$row[bundle_qty];?></td>                
                        <td align="right"><? echo $row[qc_pass_qty]; $tot_qty+=$row[qc_pass_qty];?> </td>                 
                    </tr>
                    <? } ?>
            	</tbody>
             </table>
             </div>  
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
                <tfoot>
                    <th colspan="4">Total</th>                 
                    <th align="right" width="80"><?  echo $tot_bun_qty;?></th>                 
                    <th width="143" align="right"><? echo $tot_qty;?>&nbsp;&nbsp;</th>                 
                </tfoot>
           	</table>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}



if($action=="cutting_blance_popup")
{
	echo load_html_head_contents("Cutting QC Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	list($job,$order,$item,$buyer,$style,$cutQty)=explode('_',$job_info);
	$cutting=$_SESSION['bundle_id'];
	list($cut_id,$bundle_id)=explode('*',$cutting);
	
	if($db_type==0){
		$order_by=" ORDER BY b.bundle_no";
	}
	else if($db_type==2){
		// $order_by=" ORDER BY TO_NUMBER(REPLACE(SUBSTR(f.bundle_no, INSTR(f.bundle_no, '-')+1),'-',''))";
		$order_by=" ORDER BY REPLACE(SUBSTR(f.bundle_no, INSTR(f.bundle_no, '-')+1),'-','')";
	}
	
	
	
	
	// $sql_cut="select 
	// 			d.entry_date,
	// 			d.cutting_no,
	// 			d.job_no,
	// 			e.order_id,
	// 			e.gmt_item_id,
	// 			f.bundle_no,
	// 			f.size_qty
	// 		from
	// 			ppl_cut_lay_mst d, 
	// 			ppl_cut_lay_dtls e,
	// 			ppl_cut_lay_bundle f
	// 		 where 
	// 			d.entry_form=77 and
	// 			d.id=e.mst_id and
	// 			f.mst_id=d.id and 
	// 			f.dtls_id=e.id and
	// 			d.id in($cut_id)
	// 			$order_by
	// 			";
	$sql_cut="SELECT 
				d.entry_date,
				d.cutting_no,
				d.job_no,
				e.order_id,
				e.gmt_item_id,
				f.bundle_no,
				f.size_qty
			from
				ppl_cut_lay_mst d, 
				ppl_cut_lay_dtls e,
				ppl_cut_lay_bundle f
			 where 
				d.id=e.mst_id and
				f.mst_id=d.id and 
				f.dtls_id=e.id and
				d.id in($cut_id)
				$order_by
				";
		//echo $sql_cut;
		$sql_cut_result=sql_select( $sql_cut );
		$cutting_arr=array();
		foreach($sql_cut_result as $rows)
		{	
		$cutting_arr[$rows[csf('bundle_no')]]=array(
			cutting_no=>$rows[csf('cutting_no')],
			entry_date=>$rows[csf('entry_date')],
			bundle_no=>$rows[csf('bundle_no')],
			bundle_qty=>$rows[csf('size_qty')],
		);
		
		}
		
		if($db_type==0){
			$order_by=" ORDER BY b.bundle_no";
		}
		else if($db_type==2){
			$order_by=" ORDER BY TO_NUMBER(REPLACE(SUBSTR(b.bundle_no, INSTR(b.bundle_no, '-')+1),'-',''))";
		}
	
	
	
	
		$bundle_id_list_arr=array_chunk(array_unique(explode(",",$bundle_id)),999);
		$sql_cutt_qc = "select a.location,a.sewing_line,a.floor_id,sum(b.production_qnty) as qc_qnty,b.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type=1  ";
		$p=1;
		foreach($bundle_id_list_arr as $bundle_id_list)
		{
			if($p==1) $sql_cutt_qc .=" and (b.id in(".implode(',',$bundle_id_list).")"; else  $sql_cutt_qc .=" or b.id in(".implode(',',$bundle_id_list).")";
			
			$p++;
		}
		$sql_cutt_qc .=")";
		$sql_cutt_qc .=" group by b.bundle_no,b.id,a.location,a.sewing_line,a.floor_id $order_by";
	
	
	
	
	//$sql_cutt_qc="  and b.id in($bundle_id) "; 
	$sql_cutt_qc_result=sql_select($sql_cutt_qc);
	foreach($sql_cutt_qc_result as $rows)
	{
		$cutting_qc_arr[location][$rows[csf('bundle_no')]]=$rows[csf('location')];
		$cutting_qc_arr[qc_qty][$rows[csf('bundle_no')]]+=$rows[csf('qc_qnty')];
	}
	

	
	?>
	<script>
	
		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_job_id").val(splitData[0]); 
			$("#hide_job_no").val(splitData[1]); 
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
             <table width="100%" border="1" rules="all">
            	<tr>
                    <td width="40"><strong>Buyer</strong> </td>
                    <td>: <? echo $buyer_arr[$buyer];?></td>
                    <td width="40"><strong>Style</strong> </td>
                    <td>: <? echo $style;?></td>
                    <td width="70"><strong>Job</strong></td>
                    <td>: <? echo $job;?></td>
                </tr>
            	<tr>
                    <td><strong>Order</strong></td>
                    <td>: <? echo $order;?></td>
                    <td><strong>Item</strong> </td>
                    <td>: <? echo $item;?></td>
                    <td><strong>Plan Cut Qty</strong> </td>
                    <td>: <? echo $cutQty;?></td>
                </tr>
           </table> 
            <? // if($cutting_data_arr[0]==''){ echo "<br><h1>Not Found Any Bundle.</h1>";exit();}?>
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
            	<thead>
                    <th width="30">SL</th>
                    <th width="100">Cut No</th>
                    <th width="130">Location</th>
                    <th width="80">Cutting Date </th>
                    <th width="80">Bundle No</th>
                    <th>Blance Qty</th>
                </thead>
             </table>   
            <div style="max-height:280px; overflow-y:scroll;">
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
                <tbody>
                	<? 
					$i=0;
					foreach($cutting_arr as $cutting_no=>$row){
						$blance = ($row[bundle_qty]-$cutting_qc_arr[qc_qty][$row[bundle_no]]);
						if($blance){
						$i++;
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" style="cursor:pointer;">
                        <td align="center" width="30"><? echo $i;?></td>                 
                        <td align="center" width="100"><? echo $row[cutting_no];?></td>  
                        <td width="130"><? echo $location_arr[$cutting_qc_arr[location][$row[bundle_no]]];?></td>
                        <td align="center" width="80"><? echo change_date_format($row[entry_date]);?></td>
                        <td align="center" width="80"><? echo $row[bundle_no];?></td>                 
                        <td align="right"><? echo $blance;$tot_bun_qty+=$blance;?> </td>                
                    </tr>
                    <? }} ?>
            	</tbody>
             </table>
             </div>  
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
                <tfoot>
                    <th colspan="4">Total</th>                 
                    <th align="right" width="123"><?  echo $tot_bun_qty;?>&nbsp;&nbsp;</th>                 
                </tfoot>
           	</table>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}


if($action=="print_issue_popup")
{
	echo load_html_head_contents("Print Issue Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	list($job,$order,$item,$buyer,$style,$cutQty)=explode('_',$job_info);
	$cutting=$_SESSION['bundle_id'];
		
	if($db_type==0){
		$order_by=" ORDER BY b.bundle_no";
	}
	else if($db_type==2){
		// $order_by=" ORDER BY TO_NUMBER(REPLACE(SUBSTR(b.bundle_no, INSTR(b.bundle_no, '-')+1),'-',''))";
		$order_by=" ORDER BY REPLACE(SUBSTR(b.bundle_no, INSTR(b.bundle_no, '-')+1),'-','')";
	}
	$cutting_id_arr = explode(",", $cutting);
	$cutting_cond = where_con_using_array($cutting_id_arr,0,"b.id");
	$sql_cutt_qc="SELECT a.challan_no,a.production_date,a.serving_company,a.location,sum(b.production_qnty) as issue_qnty,b.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type =2 and a.embel_name=1 $cutting_cond group by a.challan_no,b.bundle_no,b.id ,a.production_date,a.serving_company,a.location $order_by";  //and b.bundle_no is not null and b.id in($cutting)
	// echo $sql_cutt_qc;
	
	$sql_cutt_qc_result=sql_select($sql_cutt_qc);
	foreach($sql_cutt_qc_result as $rows)
	{
		$cutting_qc_arr[$rows[csf('bundle_no')]]=array(
			challan_no =>$rows[csf('challan_no')],
			production_date =>$rows[csf('production_date')],
			bundle_no  =>$rows[csf('bundle_no')],
			issue_qnty =>$rows[csf('issue_qnty')],
			location=>$rows[csf('location')],
			serving_company=>$rows[csf('serving_company')]
		);
	}
	
	
	
	?>
	<script>
	
		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_job_id").val(splitData[0]); 
			$("#hide_job_no").val(splitData[1]); 
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
             <table width="100%" border="1" rules="all">
            	<tr>
                    <td width="40"><strong>Buyer</strong> </td>
                    <td>: <? echo $buyer_arr[$buyer];?></td>
                    <td width="40"><strong>Style</strong> </td>
                    <td>: <? echo $style;?></td>
                    <td width="70"><strong>Job</strong></td>
                    <td>: <? echo $job;?></td>
                </tr>
            	<tr>
                    <td><strong>Order</strong></td>
                    <td>: <? echo $order;?></td>
                    <td><strong>Item</strong> </td>
                    <td>: <? echo $item;?></td>
                    <td><strong>Plan Cut Qty</strong> </td>
                    <td>: <? echo $cutQty;?></td>
                </tr>
           </table> 
            <? // if($cutting_data_arr[0]==''){ echo "<br><h1>Not Found Any Bundle.</h1>";exit();}?>
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
            	<thead>
                    <th width="30">SL</th>
                    <th width="100">Issue Ch.No</th>
                    <th width="80">Issue Date</th>
                    <th width="80">Issue To</th>
                    <th width="130">Location</th>
                    <th width="80">Bundle No</th>
                    <th>Issue Qty</th>
                </thead>
             </table>   
            <div style="max-height:280px; overflow-y:scroll;">
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
                <tbody>
                	<? 
					$i=0;
					foreach($cutting_qc_arr as $cutting_no=>$row){
						$i++;
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" style="cursor:pointer;">
                        <td align="center" width="30"><? echo $i;?></td>                 
                        <td align="center" width="100"><? echo $row[challan_no];?></td>
                        <td align="center" width="80"><? echo change_date_format($row[production_date]);?></td>
                        <td width="80"><? echo $company_arr[$row[serving_company]];?></td>
                        <td width="130"><? echo $location_arr[$row[location]];?></td>
                        <td align="center" width="80"><? echo $row[bundle_no];?></td>                 
                        <td align="right"><? echo $row[issue_qnty]; $tot_bun_qty+=$row[issue_qnty];?> </td>                
                    </tr>
                    <? } ?>
            	</tbody>
             </table>
             </div>  
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
                <tfoot>
                    <th colspan="4">Total</th>                 
                    <th align="right" width="142"><?  echo $tot_bun_qty;?>&nbsp;&nbsp;</th>                 
                </tfoot>
           	</table>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}


if($action=="print_rec_popup")
{
	echo load_html_head_contents("Print Issue Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	list($job,$order,$item,$buyer,$style,$cutQty)=explode('_',$job_info);
	$cutting=$_SESSION['bundle_id'];
	
	if($db_type==0){
		$order_by=" ORDER BY b.bundle_no";
	}
	else if($db_type==2){
		// $order_by=" ORDER BY TO_NUMBER(REPLACE(SUBSTR(b.bundle_no, INSTR(b.bundle_no, '-')+1),'-',''))";
		$order_by=" ORDER BY REPLACE(SUBSTR(b.bundle_no, INSTR(b.bundle_no, '-')+1),'-','')";
	}
		
		
		$bundle_id_list_arr=array_chunk(array_unique(explode(",",$cutting)),999);
		$sql_cutt_qc = "SELECT a.production_type,a.challan_no,a.production_date,a.serving_company,a.location,sum(b.production_qnty) as issue_qnty,b.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.embel_name=1  ";
		$p=1;
		foreach($bundle_id_list_arr as $bundle_id_list)
		{
			if($p==1) $sql_cutt_qc .=" and (b.id in(".implode(',',$bundle_id_list).")"; else  $sql_cutt_qc .=" or b.id in(".implode(',',$bundle_id_list).")";
			
			$p++;
		}
		$sql_cutt_qc .=")";
		$sql_cutt_qc .=" group by a.challan_no,b.bundle_no,b.id,a.production_date,a.serving_company,a.location,a.production_type $order_by";
	
	
	
	$sql_cutt_qc_result=sql_select($sql_cutt_qc);
	
	foreach($sql_cutt_qc_result as $rows)
	{
		if($rows[csf('production_type')]==3){$rec_qnty[$rows[csf('bundle_no')]]+=$rows[csf('issue_qnty')];}
		elseif($rows[csf('production_type')]==2){
			$cutting_qc_arr[$rows[csf('bundle_no')]]=array(
				challan_no =>$rows[csf('challan_no')],
				production_date =>$rows[csf('production_date')],
				bundle_no  =>$rows[csf('bundle_no')],
				issue_qnty =>$rows[csf('issue_qnty')],
				location=>$rows[csf('location')],
				serving_company=>$rows[csf('serving_company')]
			);
			}
	
	
	}
	
	
	
	?>
	<script>
	
		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_job_id").val(splitData[0]); 
			$("#hide_job_no").val(splitData[1]); 
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
             <table width="100%" border="1" rules="all">
            	<tr>
                    <td width="40"><strong>Buyer</strong> </td>
                    <td>: <? echo $buyer_arr[$buyer];?></td>
                    <td width="40"><strong>Style</strong> </td>
                    <td>: <? echo $style;?></td>
                    <td width="70"><strong>Job</strong></td>
                    <td>: <? echo $job;?></td>
                </tr>
            	<tr>
                    <td><strong>Order</strong></td>
                    <td>: <? echo $order;?></td>
                    <td><strong>Item</strong> </td>
                    <td>: <? echo $item;?></td>
                    <td><strong>Plan Cut Qty</strong> </td>
                    <td>: <? echo $cutQty;?></td>
                </tr>
           </table> 
            <? // if($cutting_data_arr[0]==''){ echo "<br><h1>Not Found Any Bundle.</h1>";exit();}?>
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
            	<thead>
                    <th width="30">SL</th>
                    <th width="100">Issue Ch.No</th>
                    <th width="80">Receive Date</th>
                    <th width="80">Receive From</th>
                    <th width="130">Location</th>
                    <th width="80">Bundle No</th>
                    <th width="80">Issue Qty</th>
                    <th>Receive Qty</th>
                </thead>
             </table>   
            <div style="max-height:280px; overflow-y:scroll;">
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
                <tbody>
                	<? 
					$i=0;
					foreach($cutting_qc_arr as $cutting_no=>$row){
						$i++;
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" style="cursor:pointer;">
                        <td align="center" width="30"><? echo $i;?></td>                 
                        <td align="center" width="100"><? echo $row[challan_no];?></td>
                        <td align="center" width="80"><? echo change_date_format($row[production_date]);?></td>
                        <td width="80"><? echo $company_arr[$row[serving_company]];?></td>
                        <td width="130"><? echo $location_arr[$row[location]];?></td>
                        <td align="center" width="80"><? echo $row[bundle_no];?></td> 
                        <td align="right" width="80"><? echo $row[issue_qnty]; $tot_issue_qty+=$row[issue_qnty];?></td>                
                        <td align="right"> <? echo $rec_qnty[$row[bundle_no]]; $tot_bun_qty+=$rec_qnty[$row[bundle_no]];?></td>                
                    </tr>
                    <? } ?>
            	</tbody>
             </table>
             </div>  
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
                <tfoot>
                    <th colspan="4">Total</th>                 
                    <th align="right" width="80"><?  echo $tot_issue_qty;?></th>
                    <th align="right" width="61"><?  echo $tot_bun_qty;?>&nbsp;&nbsp;</th>                 
                </tfoot>
           	</table>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}



if($action=="print_blance_popup")
{
	echo load_html_head_contents("Print Issue Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	list($job,$order,$item,$buyer,$style,$cutQty)=explode('_',$job_info);
	$cutting=$_SESSION['bundle_id'];
	if($db_type==0){
		$order_by=" ORDER BY b.bundle_no";
	}
	else if($db_type==2){
		// $order_by=" ORDER BY TO_NUMBER(REPLACE(SUBSTR(b.bundle_no, INSTR(b.bundle_no, '-')+1),'-',''))";
		$order_by=" ORDER BY REPLACE(SUBSTR(b.bundle_no, INSTR(b.bundle_no, '-')+1),'-','')";
	}

	$cutting_id_arr = explode(",", $cutting);
	$cutting_cond = where_con_using_array($cutting_id_arr,0,"b.id");
	
	
	$sql_cutt_qc="SELECT a.production_type,a.challan_no,a.production_date,a.serving_company,a.location,sum(b.production_qnty) as issue_qnty,b.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.embel_name=1 $cutting_cond group by a.production_type,a.challan_no,b.bundle_no,b.id,a.production_date,a.serving_company,a.location $order_by";  //and b.bundle_no is not null
	
	$sql_cutt_qc_result=sql_select($sql_cutt_qc);
	foreach($sql_cutt_qc_result as $rows)
	{
		$cutting_qc_arr[$rows[csf('bundle_no')]]=array(
			challan_no =>$rows[csf('challan_no')],
			production_date =>$rows[csf('production_date')],
			bundle_no  =>$rows[csf('bundle_no')],
			issue_qnty =>$rows[csf('issue_qnty')],
			location=>$rows[csf('location')],
			serving_company=>$rows[csf('serving_company')]
		);
		
			if($rows[csf('production_type')]==2){$blance_qty[$rows[csf('bundle_no')]]+=$rows[csf('issue_qnty')];}
			elseif($rows[csf('production_type')]==3){$blance_qty[$rows[csf('bundle_no')]]-=$rows[csf('issue_qnty')];}
		
	}
	
	
	
	?>
	<script>
	
		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_job_id").val(splitData[0]); 
			$("#hide_job_no").val(splitData[1]); 
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
             <table width="100%" border="1" rules="all">
            	<tr>
                    <td width="40"><strong>Buyer</strong> </td>
                    <td>: <? echo $buyer_arr[$buyer];?></td>
                    <td width="40"><strong>Style</strong> </td>
                    <td>: <? echo $style;?></td>
                    <td width="70"><strong>Job</strong></td>
                    <td>: <? echo $job;?></td>
                </tr>
            	<tr>
                    <td><strong>Order</strong></td>
                    <td>: <? echo $order;?></td>
                    <td><strong>Item</strong> </td>
                    <td>: <? echo $item;?></td>
                    <td><strong>Plan Cut Qty</strong> </td>
                    <td>: <? echo $cutQty;?></td>
                </tr>
           </table> 
            <? // if($cutting_data_arr[0]==''){ echo "<br><h1>Not Found Any Bundle.</h1>";exit();}?>
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
            	<thead>
                    <th width="30">SL</th>
                    <th width="100">Issue Ch.No</th>
                    <th width="80">Issue Date</th>
                    <th width="80">Issue To</th>
                    <th width="130">Location</th>
                    <th width="80">Bundle No</th>
                    <th>Blance Qty</th>
                </thead>
             </table>   
            <div style="max-height:280px; overflow-y:scroll;">
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
                <tbody>
                	<? 
					$i=0;
					foreach($cutting_qc_arr as $cutting_no=>$row){
						if($blance_qty[$row[bundle_no]]){
						$i++;
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" style="cursor:pointer;">
                        <td align="center" width="30"><? echo $i;?></td>                 
                        <td align="center" width="100"><? echo $row[challan_no];?></td>
                        <td align="center" width="80"><? echo change_date_format($row[production_date]);?></td>
                        <td width="80"><? echo $company_arr[$row[serving_company]];?></td>
                        <td width="130"><? echo $location_arr[$row[location]];?></td>
                        <td align="center" width="80"><? echo $row[bundle_no];?></td>                 
                        <td align="right"><? echo $blance_qty[$row[bundle_no]]; $tot_bun_qty+=$blance_qty[$row[bundle_no]];?> </td>                
                    </tr>
                    <? }} ?>
            	</tbody>
             </table>
             </div>  
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
                <tfoot>
                    <th colspan="4">Total</th>                 
                    <th align="right" width="142"><?  echo $tot_bun_qty;?>&nbsp;&nbsp;</th>                 
                </tfoot>
           	</table>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}




if($action=="sewing_in_popup")
{
	echo load_html_head_contents("sewing in Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	list($job,$order,$item,$buyer,$style,$cutQty,$company_id)=explode('_',$job_info);
	$cutting=$_SESSION['bundle_id'];
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name =$company_id and variable_list=23 and is_deleted=0 and status_active=1");
	
	if($prod_reso_allo==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_lib=array();
		$line_data=sql_select("select id, line_number from prod_resource_mst where is_deleted=0");
		foreach($line_data as $row)
		{
			$line='';
			$line_number=explode(",",$row[csf('line_number')]);
			foreach($line_number as $val)
			{
				if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
			}
			$line_lib[$row[csf('id')]]=$line;
		}

	}
	else
	{
	$line_lib=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );	
	}
	
	$floor_lib=return_library_array( "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0", "id", "floor_name"  );	

	if($db_type==0){
		$order_by=" ORDER BY b.bundle_no";
	}
	else if($db_type==2){
		// $order_by=" ORDER BY TO_NUMBER(REPLACE(SUBSTR(b.bundle_no, INSTR(b.bundle_no, '-')+1),'-',''))";
		$order_by=" ORDER BY REPLACE(SUBSTR(b.bundle_no, INSTR(b.bundle_no, '-')+1),'-','')";
	}
	
	$cutting_id_arr = explode(",", $cutting);
	$cutting_cond = where_con_using_array($cutting_id_arr,0,"b.id");
	
	$sql_cutt_qc="SELECT a.challan_no,a.production_date,a.serving_company,a.location,a.sewing_line,a.floor_id,sum(b.production_qnty) as issue_qnty,b.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type =4 $cutting_cond group by a.challan_no,b.bundle_no,b.id ,a.production_date,a.serving_company,a.location,a.sewing_line,a.floor_id $order_by";  //and b.bundle_no is not null
	
	$sql_cutt_qc_result=sql_select($sql_cutt_qc);
	foreach($sql_cutt_qc_result as $rows)
	{
		$cutting_qc_arr[$rows[csf('bundle_no')]]=array(
			challan_no =>$rows[csf('challan_no')],
			production_date =>$rows[csf('production_date')],
			bundle_no  =>$rows[csf('bundle_no')],
			issue_qnty =>$rows[csf('issue_qnty')],
			location=>$rows[csf('location')],
			sewing_line=>$rows[csf('sewing_line')],
			floor_id=>$rows[csf('floor_id')],
			serving_company=>$rows[csf('serving_company')]
		);
	}
	
	
	
	?>
	<script>
	
		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_job_id").val(splitData[0]); 
			$("#hide_job_no").val(splitData[1]); 
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
             <table width="100%" border="1" rules="all">
            	<tr>
                    <td width="40"><strong>Buyer</strong> </td>
                    <td>: <? echo $buyer_arr[$buyer];?></td>
                    <td width="40"><strong>Style</strong> </td>
                    <td>: <? echo $style;?></td>
                    <td width="70"><strong>Job</strong></td>
                    <td>: <? echo $job;?></td>
                </tr>
            	<tr>
                    <td><strong>Order</strong></td>
                    <td>: <? echo $order;?></td>
                    <td><strong>Item</strong> </td>
                    <td>: <? echo $item;?></td>
                    <td><strong>Plan Cut Qty</strong> </td>
                    <td>: <? echo $cutQty;?></td>
                </tr>
           </table> 
            <? // if($cutting_data_arr[0]==''){ echo "<br><h1>Not Found Any Bundle.</h1>";exit();}?>
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
            	<thead>
                    <th width="30">SL</th>
                    <th width="100">Issue Ch.No</th>
                    <th width="80">Input Date</th>
                    <th width="80">Sew. Company</th>
                    <th width="130">Location</th>
                    <th width="80">Floor</th>
                    <th width="80">Line</th>
                    <th width="80">Bundle No</th>
                    <th>Input Qty</th>
                </thead>
             </table>   
            <div style="max-height:280px; overflow-y:scroll;">
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
                <tbody>
                	<? 
					$i=0;
					foreach($cutting_qc_arr as $cutting_no=>$row){
						$i++;
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" style="cursor:pointer;">
                        <td align="center" width="30"><? echo $i;?></td>                 
                        <td align="center" width="100"><? echo $row[challan_no];?></td>
                        <td align="center" width="80"><? echo change_date_format($row[production_date]);?></td>
                        <td width="80"><? echo $company_arr[$row[serving_company]];?></td>
                        <td width="130"><? echo $location_arr[$row[location]];?></td>
                        <td width="80"><? echo $floor_lib[$row[floor_id]];?></td>
                        <td width="80"><? echo $line_lib[$row[sewing_line]];?></td>
                        <td align="center" width="80"><? echo $row[bundle_no];?></td>                 
                        <td align="right"><? echo $row[issue_qnty]; $tot_bun_qty+=$row[issue_qnty];?> </td>                
                    </tr>
                    <? } ?>
            	</tbody>
             </table>
             </div>  
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
                <tfoot>
                    <th colspan="4">Total</th>                 
                    <th align="right" width="80"><?  echo $tot_bun_qty;?>&nbsp;&nbsp;</th>                 
                </tfoot>
           	</table>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}


if($action=="sewing_out_popup")
{
	echo load_html_head_contents("sewing out Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	list($job,$order,$item,$buyer,$style,$cutQty,$company_id)=explode('_',$job_info);
	$cutting=$_SESSION['bundle_id'];
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name =$company_id and variable_list=23 and is_deleted=0 and status_active=1");
	
	if($prod_reso_allo==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_lib=array();
		$line_data=sql_select("select id, line_number from prod_resource_mst where is_deleted=0");
		foreach($line_data as $row)
		{
			$line='';
			$line_number=explode(",",$row[csf('line_number')]);
			foreach($line_number as $val)
			{
				if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
			}
			$line_lib[$row[csf('id')]]=$line;
		}

	}
	else
	{
	$line_lib=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );	
	}
	
	$floor_lib=return_library_array( "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0", "id", "floor_name"  );	
	
	
	if($db_type==0){
		$order_by=" ORDER BY b.bundle_no";
	}
	else if($db_type==2){
		// $order_by=" ORDER BY TO_NUMBER(REPLACE(SUBSTR(b.bundle_no, INSTR(b.bundle_no, '-')+1),'-',''))";
		$order_by=" ORDER BY REPLACE(SUBSTR(b.bundle_no, INSTR(b.bundle_no, '-')+1),'-','')";
	}
	
	$cutting_id_arr = explode(",", $cutting);
	$cutting_cond = where_con_using_array($cutting_id_arr,0,"b.id");
	
	
	$sql_cutt_qc="SELECT a.production_type,a.challan_no,a.production_date,a.serving_company,a.location,a.sewing_line,a.floor_id,sum(b.production_qnty) as issue_qnty,b.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $cutting_cond group by a.challan_no,b.bundle_no,b.id,a.production_date,a.serving_company,a.location,a.sewing_line,a.floor_id,a.production_type $order_by";  //and b.bundle_no is not null
	
	$sql_cutt_qc_result=sql_select($sql_cutt_qc);
	foreach($sql_cutt_qc_result as $rows)
	{
		if($rows[csf('production_type')]==5){$out_qnty[$rows[csf('bundle_no')]]+=$rows[csf('issue_qnty')];}
		elseif($rows[csf('production_type')]==4){
			$cutting_qc_arr[$rows[csf('bundle_no')]]=array(
				challan_no =>$rows[csf('challan_no')],
				production_date =>$rows[csf('production_date')],
				bundle_no  =>$rows[csf('bundle_no')],
				issue_qnty =>$rows[csf('issue_qnty')],
				location=>$rows[csf('location')],
				sewing_line=>$rows[csf('sewing_line')],
				floor_id=>$rows[csf('floor_id')],
				serving_company=>$rows[csf('serving_company')]
			);
			}
	
	
	}
	
	
	
	?>
	<script>
	
		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_job_id").val(splitData[0]); 
			$("#hide_job_no").val(splitData[1]); 
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
             <table width="100%" border="1" rules="all">
            	<tr>
                    <td width="40"><strong>Buyer</strong> </td>
                    <td>: <? echo $buyer_arr[$buyer];?></td>
                    <td width="40"><strong>Style</strong> </td>
                    <td>: <? echo $style;?></td>
                    <td width="70"><strong>Job</strong></td>
                    <td>: <? echo $job;?></td>
                </tr>
            	<tr>
                    <td><strong>Order</strong></td>
                    <td>: <? echo $order;?></td>
                    <td><strong>Item</strong> </td>
                    <td>: <? echo $item;?></td>
                    <td><strong>Plan Cut Qty</strong> </td>
                    <td>: <? echo $cutQty;?></td>
                </tr>
           </table> 
            <? // if($cutting_data_arr[0]==''){ echo "<br><h1>Not Found Any Bundle.</h1>";exit();}?>
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
            	<thead>
                    <th width="30">SL</th>
                    <th width="50">Issue Ch.No</th>
                    <th width="80">Out Date</th>
                    <th width="100">Sew. Company</th>
                    <th width="100">Location</th>
                    <th width="80">Floor</th>
                    <th width="80">Line</th>
                    <th width="60">Bundle No</th>
                    <th width="80">Input Qty</th>
                    <th>Output Qty</th>
                </thead>
             </table>   
            <div style="max-height:280px; overflow-y:scroll;">
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
                <tbody>
                	<? 
					$i=0;
					foreach($cutting_qc_arr as $cutting_no=>$row){
						$i++;
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" style="cursor:pointer;">
                        <td align="center" width="30"><? echo $i;?></td>                 
                        <td align="center" width="50"><? echo $row[challan_no];?></td>
                        <td align="center" width="80"><? echo change_date_format($row[production_date]);?></td>
                        <td width="100"><? echo $company_arr[$row[serving_company]];?></td>
                        <td width="100"><? echo $location_arr[$row[location]];?></td>
                        <td width="80"><? echo $floor_lib[$row[floor_id]];?></td>
                        <td width="80"><? echo $line_lib[$row[sewing_line]];?></td>
                        <td align="center" width="60"><? echo $row[bundle_no];?></td> 
                        <td align="right" width="80"><? echo $row[issue_qnty]; $tot_sew_in_qty+=$row[issue_qnty];?></td>                
                        <td align="right"><? echo $out_qnty[$row[bundle_no]]; $tot_sew_out_qty+=$out_qnty[$row[bundle_no]];?> </td>                
                    </tr>
                    <? } ?>
            	</tbody>
             </table>
             </div>  
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
                <tfoot>
                    <th colspan="6">Total</th>                 
                    <th align="right" width="80"><?  echo $tot_sew_in_qty;?></th>
                    <th align="right" width="79"><?  echo $tot_sew_out_qty;?>&nbsp;&nbsp;</th>                 
                </tfoot>
           	</table>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}


//-----
if($action=="embroidery_issue_popup")
{
	echo load_html_head_contents("Embroidery Issue Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	list($job,$order,$item,$buyer,$style,$cutQty)=explode('_',$job_info);
	$cutting=$_SESSION['bundle_id'];
	
	
	if($db_type==0){
		$order_by=" ORDER BY b.bundle_no";
	}
	else if($db_type==2){
		$order_by=" ORDER BY TO_NUMBER(REPLACE(SUBSTR(b.bundle_no, INSTR(b.bundle_no, '-')+1),'-',''))";
	}
	
	
	$sql_cutt_qc="select a.challan_no,a.production_date,a.serving_company,a.location,sum(b.production_qnty) as issue_qnty,b.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type =2 and a.embel_name=2 and b.id in($cutting) group by a.challan_no,b.bundle_no,b.id ,a.production_date,a.serving_company,a.location $order_by";  //and b.bundle_no is not null
	
	$sql_cutt_qc_result=sql_select($sql_cutt_qc);
	foreach($sql_cutt_qc_result as $rows)
	{
		$cutting_qc_arr[$rows[csf('bundle_no')]]=array(
			challan_no =>$rows[csf('challan_no')],
			production_date =>$rows[csf('production_date')],
			bundle_no  =>$rows[csf('bundle_no')],
			issue_qnty =>$rows[csf('issue_qnty')],
			location=>$rows[csf('location')],
			serving_company=>$rows[csf('serving_company')]
		);
	}
	
	
	
	?>
	<script>
	
		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_job_id").val(splitData[0]); 
			$("#hide_job_no").val(splitData[1]); 
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
             <table width="100%" border="1" rules="all">
            	<tr>
                    <td width="40"><strong>Buyer</strong> </td>
                    <td>: <? echo $buyer_arr[$buyer];?></td>
                    <td width="40"><strong>Style</strong> </td>
                    <td>: <? echo $style;?></td>
                    <td width="70"><strong>Job</strong></td>
                    <td>: <? echo $job;?></td>
                </tr>
            	<tr>
                    <td><strong>Order</strong></td>
                    <td>: <? echo $order;?></td>
                    <td><strong>Item</strong> </td>
                    <td>: <? echo $item;?></td>
                    <td><strong>Plan Cut Qty</strong> </td>
                    <td>: <? echo $cutQty;?></td>
                </tr>
           </table> 
            <? // if($cutting_data_arr[0]==''){ echo "<br><h1>Not Found Any Bundle.</h1>";exit();}?>
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
            	<thead>
                    <th width="30">SL</th>
                    <th width="100">Issue Ch.No</th>
                    <th width="80">Issue Date</th>
                    <th width="80">Issue To</th>
                    <th width="130">Location</th>
                    <th width="80">Bundle No</th>
                    <th>Issue Qty</th>
                </thead>
             </table>   
            <div style="max-height:280px; overflow-y:scroll;">
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
                <tbody>
                	<? 
					$i=0;
					foreach($cutting_qc_arr as $cutting_no=>$row){
						$i++;
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" style="cursor:pointer;">
                        <td align="center" width="30"><? echo $i;?></td>                 
                        <td align="center" width="100"><? echo $row[challan_no];?></td>
                        <td align="center" width="80"><? echo change_date_format($row[production_date]);?></td>
                        <td width="80"><? echo $company_arr[$row[serving_company]];?></td>
                        <td width="130"><? echo $location_arr[$row[location]];?></td>
                        <td align="center" width="80"><? echo $row[bundle_no];?></td>                 
                        <td align="right"><? echo $row[issue_qnty]; $tot_bun_qty+=$row[issue_qnty];?> </td>                
                    </tr>
                    <? } ?>
            	</tbody>
             </table>
             </div>  
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
                <tfoot>
                    <th colspan="4">Total</th>                 
                    <th align="right" width="142"><?  echo $tot_bun_qty;?>&nbsp;&nbsp;</th>                 
                </tfoot>
           	</table>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}


if($action=="embroidery_rec_popup")
{
	echo load_html_head_contents("Print Issue Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	list($job,$order,$item,$buyer,$style,$cutQty)=explode('_',$job_info);
	$cutting=$_SESSION['bundle_id'];
	
	if($db_type==0){
		$order_by=" ORDER BY b.bundle_no";
	}
	else if($db_type==2){
		$order_by=" ORDER BY TO_NUMBER(REPLACE(SUBSTR(b.bundle_no, INSTR(b.bundle_no, '-')+1),'-',''))";
	}
	
	
	$sql_cutt_qc="select a.production_type,a.challan_no,a.production_date,a.serving_company,a.location,sum(b.production_qnty) as issue_qnty,b.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.embel_name=2 and a.production_type in(2,3) and b.id in($cutting) group by a.challan_no,b.bundle_no,b.id,a.production_date,a.serving_company,a.location,a.production_type $order_by";  //and b.bundle_no is not null
	
	$sql_cutt_qc_result=sql_select($sql_cutt_qc);
	foreach($sql_cutt_qc_result as $rows)
	{
		if($rows[csf('production_type')]==3){$rec_qnty[$rows[csf('bundle_no')]]+=$rows[csf('issue_qnty')];}
		elseif($rows[csf('production_type')]==2){
			$cutting_qc_arr[$rows[csf('bundle_no')]]=array(
				challan_no =>$rows[csf('challan_no')],
				production_date =>$rows[csf('production_date')],
				bundle_no  =>$rows[csf('bundle_no')],
				issue_qnty =>$rows[csf('issue_qnty')],
				location=>$rows[csf('location')],
				serving_company=>$rows[csf('serving_company')]
			);
			}
	
	
	}
	
	
	
	?>
	<script>
	
		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_job_id").val(splitData[0]); 
			$("#hide_job_no").val(splitData[1]); 
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
             <table width="100%" border="1" rules="all">
            	<tr>
                    <td width="40"><strong>Buyer</strong> </td>
                    <td>: <? echo $buyer_arr[$buyer];?></td>
                    <td width="40"><strong>Style</strong> </td>
                    <td>: <? echo $style;?></td>
                    <td width="70"><strong>Job</strong></td>
                    <td>: <? echo $job;?></td>
                </tr>
            	<tr>
                    <td><strong>Order</strong></td>
                    <td>: <? echo $order;?></td>
                    <td><strong>Item</strong> </td>
                    <td>: <? echo $item;?></td>
                    <td><strong>Plan Cut Qty</strong> </td>
                    <td>: <? echo $cutQty;?></td>
                </tr>
           </table> 
            <? // if($cutting_data_arr[0]==''){ echo "<br><h1>Not Found Any Bundle.</h1>";exit();}?>
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
            	<thead>
                    <th width="30">SL</th>
                    <th width="100">Issue Ch.No</th>
                    <th width="80">Receive Date</th>
                    <th width="80">Receive From</th>
                    <th width="130">Location</th>
                    <th width="80">Bundle No</th>
                    <th width="80">Issue Qty</th>
                    <th>Receive Qty</th>
                </thead>
             </table>   
            <div style="max-height:280px; overflow-y:scroll;">
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
                <tbody>
                	<? 
					$i=0;
					foreach($cutting_qc_arr as $cutting_no=>$row){
						$i++;
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" style="cursor:pointer;">
                        <td align="center" width="30"><? echo $i;?></td>                 
                        <td align="center" width="100"><? echo $row[challan_no];?></td>
                        <td align="center" width="80"><? echo change_date_format($row[production_date]);?></td>
                        <td width="80"><? echo $company_arr[$row[serving_company]];?></td>
                        <td width="130"><? echo $location_arr[$row[location]];?></td>
                        <td align="center" width="80"><? echo $row[bundle_no];?></td> 
                        <td align="right" width="80"><? echo $row[issue_qnty]; $tot_issue_qty+=$row[issue_qnty];?></td>                
                        <td align="right"><? echo $rec_qnty[$row[bundle_no]]; $tot_bun_qty+=$rec_qnty[$row[bundle_no]];?> </td>                
                    </tr>
                    <? } ?>
            	</tbody>
             </table>
             </div>  
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
                <tfoot>
                    <th colspan="6">Total</th>                 
                    <th align="right" width="80"><?  echo $tot_issue_qty;?></th>
                    <th align="right" width="100"><?  echo $tot_bun_qty;?>&nbsp;&nbsp;</th>                 
                </tfoot>
           	</table>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}



if($action=="embroidery_blance_popup")
{
	echo load_html_head_contents("Print Issue Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	list($job,$order,$item,$buyer,$style,$cutQty)=explode('_',$job_info);
	$cutting=$_SESSION['bundle_id'];
	
	if($db_type==0){
		$order_by=" ORDER BY b.bundle_no";
	}
	else if($db_type==2){
		$order_by=" ORDER BY TO_NUMBER(REPLACE(SUBSTR(b.bundle_no, INSTR(b.bundle_no, '-')+1),'-',''))";
	}
	
	
	
	
	$sql_cutt_qc="select a.production_type,a.challan_no,a.production_date,a.serving_company,a.location,sum(b.production_qnty) as issue_qnty,b.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.embel_name=2 and a.production_type in(2,3) and b.id in($cutting) group by a.production_type,a.challan_no,b.bundle_no,b.id,a.production_date,a.serving_company,a.location $order_by";  //and b.bundle_no is not null
	
	$sql_cutt_qc_result=sql_select($sql_cutt_qc);
	foreach($sql_cutt_qc_result as $rows)
	{
		$cutting_qc_arr[$rows[csf('bundle_no')]]=array(
			challan_no =>$rows[csf('challan_no')],
			production_date =>$rows[csf('production_date')],
			bundle_no  =>$rows[csf('bundle_no')],
			issue_qnty =>$rows[csf('issue_qnty')],
			location=>$rows[csf('location')],
			serving_company=>$rows[csf('serving_company')]
		);
		
			if($rows[csf('production_type')]==2){$blance_qty[$rows[csf('bundle_no')]]+=$rows[csf('issue_qnty')];}
			elseif($rows[csf('production_type')]==3){$blance_qty[$rows[csf('bundle_no')]]-=$rows[csf('issue_qnty')];}
		
	}
	
	
	
	?>
	<script>
	
		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_job_id").val(splitData[0]); 
			$("#hide_job_no").val(splitData[1]); 
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
             <table width="100%" border="1" rules="all">
            	<tr>
                    <td width="40"><strong>Buyer</strong> </td>
                    <td>: <? echo $buyer_arr[$buyer];?></td>
                    <td width="40"><strong>Style</strong> </td>
                    <td>: <? echo $style;?></td>
                    <td width="70"><strong>Job</strong></td>
                    <td>: <? echo $job;?></td>
                </tr>
            	<tr>
                    <td><strong>Order</strong></td>
                    <td>: <? echo $order;?></td>
                    <td><strong>Item</strong> </td>
                    <td>: <? echo $item;?></td>
                    <td><strong>Plan Cut Qty</strong> </td>
                    <td>: <? echo $cutQty;?></td>
                </tr>
           </table> 
            <? // if($cutting_data_arr[0]==''){ echo "<br><h1>Not Found Any Bundle.</h1>";exit();}?>
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
            	<thead>
                    <th width="30">SL</th>
                    <th width="100">Issue Ch.No</th>
                    <th width="80">Issue Date</th>
                    <th width="80">Issue To</th>
                    <th width="130">Location</th>
                    <th width="80">Bundle No</th>
                    <th>Blance Qty</th>
                </thead>
             </table>   
            <div style="max-height:280px; overflow-y:scroll;">
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
                <tbody>
                	<? 
					$i=0;
					foreach($cutting_qc_arr as $cutting_no=>$row){
						if($blance_qty[$row[bundle_no]]){
						$i++;
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" style="cursor:pointer;">
                        <td align="center" width="30"><? echo $i;?></td>                 
                        <td align="center" width="100"><? echo $row[challan_no];?></td>
                        <td align="center" width="80"><? echo change_date_format($row[production_date]);?></td>
                        <td width="80"><? echo $company_arr[$row[serving_company]];?></td>
                        <td width="130"><? echo $location_arr[$row[location]];?></td>
                        <td align="center" width="80"><? echo $row[bundle_no];?></td>                 
                        <td align="right"><? echo $blance_qty[$row[bundle_no]]; $tot_bun_qty+=$blance_qty[$row[bundle_no]];?> </td>                
                    </tr>
                    <? }} ?>
            	</tbody>
             </table>
             </div>  
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
                <tfoot>
                    <th colspan="4">Total</th>                 
                    <th align="right" width="142"><?  echo $tot_bun_qty;?>&nbsp;&nbsp;</th>                 
                </tfoot>
           	</table>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}

if($action=="spacial_work_issue_popup")
{
	echo load_html_head_contents("Print Issue Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	list($job,$order,$item,$buyer,$style,$cutQty)=explode('_',$job_info);
	$cutting=$_SESSION['bundle_id'];
	if($db_type==0){
		$order_by=" ORDER BY b.bundle_no";
	}
	else if($db_type==2){
		$order_by=" ORDER BY TO_NUMBER(REPLACE(SUBSTR(b.bundle_no, INSTR(b.bundle_no, '-')+1),'-',''))";
	}
	
	
	$sql_cutt_qc="select a.challan_no,a.production_date,a.serving_company,a.location,sum(b.production_qnty) as issue_qnty,b.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type =2 and a.embel_name=4 and b.id in($cutting) group by a.challan_no,b.bundle_no,b.id ,a.production_date,a.serving_company,a.location $order_by";  //and b.bundle_no is not null
	
	$sql_cutt_qc_result=sql_select($sql_cutt_qc);
	foreach($sql_cutt_qc_result as $rows)
	{
		$cutting_qc_arr[$rows[csf('bundle_no')]]=array(
			challan_no =>$rows[csf('challan_no')],
			production_date =>$rows[csf('production_date')],
			bundle_no  =>$rows[csf('bundle_no')],
			issue_qnty =>$rows[csf('issue_qnty')],
			location=>$rows[csf('location')],
			serving_company=>$rows[csf('serving_company')]
		);
	}
	
	
	
	?>
	<script>
	
		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_job_id").val(splitData[0]); 
			$("#hide_job_no").val(splitData[1]); 
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
             <table width="100%" border="1" rules="all">
            	<tr>
                    <td width="40"><strong>Buyer</strong> </td>
                    <td>: <? echo $buyer_arr[$buyer];?></td>
                    <td width="40"><strong>Style</strong> </td>
                    <td>: <? echo $style;?></td>
                    <td width="70"><strong>Job</strong></td>
                    <td>: <? echo $job;?></td>
                </tr>
            	<tr>
                    <td><strong>Order</strong></td>
                    <td>: <? echo $order;?></td>
                    <td><strong>Item</strong> </td>
                    <td>: <? echo $item;?></td>
                    <td><strong>Plan Cut Qty</strong> </td>
                    <td>: <? echo $cutQty;?></td>
                </tr>
           </table> 
            <? // if($cutting_data_arr[0]==''){ echo "<br><h1>Not Found Any Bundle.</h1>";exit();}?>
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
            	<thead>
                    <th width="30">SL</th>
                    <th width="100">Issue Ch.No</th>
                    <th width="80">Issue Date</th>
                    <th width="80">Issue To</th>
                    <th width="130">Location</th>
                    <th width="80">Bundle No</th>
                    <th>Issue Qty</th>
                </thead>
             </table>   
            <div style="max-height:280px; overflow-y:scroll;">
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
                <tbody>
                	<? 
					$i=0;
					foreach($cutting_qc_arr as $cutting_no=>$row){
						$i++;
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" style="cursor:pointer;">
                        <td align="center" width="30"><? echo $i;?></td>                 
                        <td align="center" width="100"><? echo $row[challan_no];?></td>
                        <td align="center" width="80"><? echo change_date_format($row[production_date]);?></td>
                        <td width="80"><? echo $company_arr[$row[serving_company]];?></td>
                        <td width="130"><? echo $location_arr[$row[location]];?></td>
                        <td align="center" width="80"><? echo $row[bundle_no];?></td>                 
                        <td align="right"><? echo $row[issue_qnty]; $tot_bun_qty+=$row[issue_qnty];?> </td>                
                    </tr>
                    <? } ?>
            	</tbody>
             </table>
             </div>  
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
                <tfoot>
                    <th colspan="4">Total</th>                 
                    <th align="right" width="242"><?  echo $tot_bun_qty;?>&nbsp;&nbsp;</th>                 
                </tfoot>
           	</table>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}


if($action=="spacial_work_rec_popup")
{
	echo load_html_head_contents("Print Issue Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	list($job,$order,$item,$buyer,$style,$cutQty)=explode('_',$job_info);
	$cutting=$_SESSION['bundle_id'];
	
	if($db_type==0){
		$order_by=" ORDER BY b.bundle_no";
	}
	else if($db_type==2){
		$order_by=" ORDER BY TO_NUMBER(REPLACE(SUBSTR(b.bundle_no, INSTR(b.bundle_no, '-')+1),'-',''))";
	}

	$sql_cutt_qc="select a.production_type,a.challan_no,a.production_date,a.serving_company,a.location,sum(b.production_qnty) as issue_qnty,b.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.embel_name=4 and a.production_type in(2,3) and b.id in($cutting) group by a.challan_no,b.bundle_no,b.id,a.production_date,a.serving_company,a.location,a.production_type $order_by";  //and b.bundle_no is not null
	
	$sql_cutt_qc_result=sql_select($sql_cutt_qc);
	foreach($sql_cutt_qc_result as $rows)
	{
		if($rows[csf('production_type')]==3){$rec_qnty[$rows[csf('bundle_no')]]+=$rows[csf('issue_qnty')];}
		elseif($rows[csf('production_type')]==2){
			$cutting_qc_arr[$rows[csf('bundle_no')]]=array(
				challan_no =>$rows[csf('challan_no')],
				production_date =>$rows[csf('production_date')],
				bundle_no  =>$rows[csf('bundle_no')],
				issue_qnty =>$rows[csf('issue_qnty')],
				location=>$rows[csf('location')],
				serving_company=>$rows[csf('serving_company')]
			);
			}
	
	
	}
	
	
	
	?>
	<script>
	
		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_job_id").val(splitData[0]); 
			$("#hide_job_no").val(splitData[1]); 
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
             <table width="100%" border="1" rules="all">
            	<tr>
                    <td width="40"><strong>Buyer</strong> </td>
                    <td>: <? echo $buyer_arr[$buyer];?></td>
                    <td width="40"><strong>Style</strong> </td>
                    <td>: <? echo $style;?></td>
                    <td width="70"><strong>Job</strong></td>
                    <td>: <? echo $job;?></td>
                </tr>
            	<tr>
                    <td><strong>Order</strong></td>
                    <td>: <? echo $order;?></td>
                    <td><strong>Item</strong> </td>
                    <td>: <? echo $item;?></td>
                    <td><strong>Plan Cut Qty</strong> </td>
                    <td>: <? echo $cutQty;?></td>
                </tr>
           </table> 
            <? // if($cutting_data_arr[0]==''){ echo "<br><h1>Not Found Any Bundle.</h1>";exit();}?>
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
            	<thead>
                    <th width="30">SL</th>
                    <th width="100">Issue Ch.No</th>
                    <th width="80">Receive Date</th>
                    <th width="80">Receive From</th>
                    <th width="130">Location</th>
                    <th width="80">Bundle No</th>
                    <th width="80">Issue Qty</th>
                    <th>Receive Qty</th>
                </thead>
             </table>   
            <div style="max-height:280px; overflow-y:scroll;">
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
                <tbody>
                	<? 
					$i=0;
					foreach($cutting_qc_arr as $cutting_no=>$row){
						$i++;
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" style="cursor:pointer;">
                        <td align="center" width="30"><? echo $i;?></td>                 
                        <td align="center" width="100"><? echo $row[challan_no];?></td>
                        <td align="center" width="80"><? echo change_date_format($row[production_date]);?></td>
                        <td width="80"><? echo $company_arr[$row[serving_company]];?></td>
                        <td width="130"><? echo $location_arr[$row[location]];?></td>
                        <td align="center" width="80"><? echo $row[bundle_no];?></td> 
                        <td align="right" width="80"><? echo $row[issue_qnty]; $tot_issue_qty+=$row[issue_qnty];?></td>                
                        <td align="right"><? echo $rec_qnty[$row[bundle_no]]; $tot_bun_qty+=$rec_qnty[$row[bundle_no]];?> </td>                
                    </tr>
                    <? } ?>
            	</tbody>
             </table>
             </div>  
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
                <tfoot>
                    <th colspan="6">Total</th>                 
                    <th align="right" width="80"><?  echo $tot_issue_qty;?></th>
                    <th align="right" width="161"><?  echo $tot_bun_qty;?>&nbsp;&nbsp;</th>                 
                </tfoot>
           	</table>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}



if($action=="spacial_work_blance_popup")
{
	echo load_html_head_contents("Print Issue Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	list($job,$order,$item,$buyer,$style,$cutQty)=explode('_',$job_info);
	$cutting=$_SESSION['bundle_id'];
	
	if($db_type==0){
		$order_by=" ORDER BY b.bundle_no";
	}
	else if($db_type==2){
		$order_by=" ORDER BY TO_NUMBER(REPLACE(SUBSTR(b.bundle_no, INSTR(b.bundle_no, '-')+1),'-',''))";
	}
	
	$sql_cutt_qc="select a.production_type,a.challan_no,a.production_date,a.serving_company,a.location,sum(b.production_qnty) as issue_qnty,b.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.embel_name=4 and a.production_type in(2,3) and b.id in($cutting) group by a.production_type,a.challan_no,b.bundle_no,b.id,a.production_date,a.serving_company,a.location $order_by";  //and b.bundle_no is not null
	
	$sql_cutt_qc_result=sql_select($sql_cutt_qc);
	foreach($sql_cutt_qc_result as $rows)
	{
		$cutting_qc_arr[$rows[csf('bundle_no')]]=array(
			challan_no =>$rows[csf('challan_no')],
			production_date =>$rows[csf('production_date')],
			bundle_no  =>$rows[csf('bundle_no')],
			issue_qnty =>$rows[csf('issue_qnty')],
			location=>$rows[csf('location')],
			serving_company=>$rows[csf('serving_company')]
		);
		
			if($rows[csf('production_type')]==2){$blance_qty[$rows[csf('bundle_no')]]+=$rows[csf('issue_qnty')];}
			elseif($rows[csf('production_type')]==3){$blance_qty[$rows[csf('bundle_no')]]-=$rows[csf('issue_qnty')];}
		
	}
	
	
	
	?>
	<script>
	
		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_job_id").val(splitData[0]); 
			$("#hide_job_no").val(splitData[1]); 
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
             <table width="100%" border="1" rules="all">
            	<tr>
                    <td width="40"><strong>Buyer</strong> </td>
                    <td>: <? echo $buyer_arr[$buyer];?></td>
                    <td width="40"><strong>Style</strong> </td>
                    <td>: <? echo $style;?></td>
                    <td width="70"><strong>Job</strong></td>
                    <td>: <? echo $job;?></td>
                </tr>
            	<tr>
                    <td><strong>Order</strong></td>
                    <td>: <? echo $order;?></td>
                    <td><strong>Item</strong> </td>
                    <td>: <? echo $item;?></td>
                    <td><strong>Plan Cut Qty</strong> </td>
                    <td>: <? echo $cutQty;?></td>
                </tr>
           </table> 
            <? // if($cutting_data_arr[0]==''){ echo "<br><h1>Not Found Any Bundle.</h1>";exit();}?>
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
            	<thead>
                    <th width="30">SL</th>
                    <th width="100">Issue Ch.No</th>
                    <th width="80">Issue Date</th>
                    <th width="80">Issue To</th>
                    <th width="130">Location</th>
                    <th width="80">Bundle No</th>
                    <th>Blance Qty</th>
                </thead>
             </table>   
            <div style="max-height:280px; overflow-y:scroll;">
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
                <tbody>
                	<? 
					$i=0;
					foreach($cutting_qc_arr as $cutting_no=>$row){
						if($blance_qty[$row[bundle_no]]){
						$i++;
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" style="cursor:pointer;">
                        <td align="center" width="30"><? echo $i;?></td>                 
                        <td align="center" width="100"><? echo $row[challan_no];?></td>
                        <td align="center" width="80"><? echo change_date_format($row[production_date]);?></td>
                        <td width="80"><? echo $company_arr[$row[serving_company]];?></td>
                        <td width="130"><? echo $location_arr[$row[location]];?></td>
                        <td align="center" width="80"><? echo $row[bundle_no];?></td>                 
                        <td align="right"><? echo $blance_qty[$row[bundle_no]]; $tot_bun_qty+=$blance_qty[$row[bundle_no]];?> </td>                
                    </tr>
                    <? }} ?>
            	</tbody>
             </table>
             </div>  
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
                <tfoot>
                    <th colspan="4">Total</th>                 
                    <th align="right" width="242"><?  echo $tot_bun_qty;?>&nbsp;&nbsp;</th>                 
                </tfoot>
           	</table>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}

if($action=="wash_issue_popup")
{
	echo load_html_head_contents("Print Issue Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	list($job,$order,$item,$buyer,$style,$cutQty)=explode('_',$job_info);
	$cutting=$_SESSION['bundle_id'];
	
	if($db_type==0){
		$order_by=" ORDER BY b.bundle_no";
	}
	else if($db_type==2){
		$order_by=" ORDER BY TO_NUMBER(REPLACE(SUBSTR(b.bundle_no, INSTR(b.bundle_no, '-')+1),'-',''))";
	}
	
	
	$sql_cutt_qc="select a.challan_no,a.production_date,a.serving_company,a.location,sum(b.production_qnty) as issue_qnty,b.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type =2 and a.embel_name=3 and b.id in($cutting) group by a.challan_no,b.bundle_no,b.id ,a.production_date,a.serving_company,a.location $order_by";  //and b.bundle_no is not null
	
	$sql_cutt_qc_result=sql_select($sql_cutt_qc);
	foreach($sql_cutt_qc_result as $rows)
	{
		$cutting_qc_arr[$rows[csf('bundle_no')]]=array(
			challan_no =>$rows[csf('challan_no')],
			production_date =>$rows[csf('production_date')],
			bundle_no  =>$rows[csf('bundle_no')],
			issue_qnty =>$rows[csf('issue_qnty')],
			location=>$rows[csf('location')],
			serving_company=>$rows[csf('serving_company')]
		);
	}
	
	
	
	?>
	<script>
	
		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_job_id").val(splitData[0]); 
			$("#hide_job_no").val(splitData[1]); 
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
             <table width="100%" border="1" rules="all">
            	<tr>
                    <td width="40"><strong>Buyer</strong> </td>
                    <td>: <? echo $buyer_arr[$buyer];?></td>
                    <td width="40"><strong>Style</strong> </td>
                    <td>: <? echo $style;?></td>
                    <td width="70"><strong>Job</strong></td>
                    <td>: <? echo $job;?></td>
                </tr>
            	<tr>
                    <td><strong>Order</strong></td>
                    <td>: <? echo $order;?></td>
                    <td><strong>Item</strong> </td>
                    <td>: <? echo $item;?></td>
                    <td><strong>Plan Cut Qty</strong> </td>
                    <td>: <? echo $cutQty;?></td>
                </tr>
           </table> 
            <? // if($cutting_data_arr[0]==''){ echo "<br><h1>Not Found Any Bundle.</h1>";exit();}?>
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
            	<thead>
                    <th width="30">SL</th>
                    <th width="100">Issue Ch.No</th>
                    <th width="80">Issue Date</th>
                    <th width="80">Issue To</th>
                    <th width="130">Location</th>
                    <th width="80">Bundle No</th>
                    <th>Issue Qty</th>
                </thead>
             </table>   
            <div style="max-height:280px; overflow-y:scroll;">
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
                <tbody>
                	<? 
					$i=0;
					foreach($cutting_qc_arr as $cutting_no=>$row){
						$i++;
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" style="cursor:pointer;">
                        <td align="center" width="30"><? echo $i;?></td>                 
                        <td align="center" width="100"><? echo $row[challan_no];?></td>
                        <td align="center" width="80"><? echo change_date_format($row[production_date]);?></td>
                        <td width="80"><? echo $company_arr[$row[serving_company]];?></td>
                        <td width="130"><? echo $location_arr[$row[location]];?></td>
                        <td align="center" width="80"><? echo $row[bundle_no];?></td>                 
                        <td align="right"><? echo $row[issue_qnty]; $tot_bun_qty+=$row[issue_qnty];?> </td>                
                    </tr>
                    <? } ?>
            	</tbody>
             </table>
             </div>  
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
                <tfoot>
                    <th colspan="4">Total</th>                 
                    <th align="right" width="242"><?  echo $tot_bun_qty;?>&nbsp;&nbsp;</th>                 
                </tfoot>
           	</table>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}


if($action=="wash_rec_popup")
{
	echo load_html_head_contents("Print Issue Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	list($job,$order,$item,$buyer,$style,$cutQty)=explode('_',$job_info);
	$cutting=$_SESSION['bundle_id'];
	
	if($db_type==0){
		$order_by=" ORDER BY b.bundle_no";
	}
	else if($db_type==2){
		$order_by=" ORDER BY TO_NUMBER(REPLACE(SUBSTR(b.bundle_no, INSTR(b.bundle_no, '-')+1),'-',''))";
	}
	
	$sql_cutt_qc="select a.production_type,a.challan_no,a.production_date,a.serving_company,a.location,sum(b.production_qnty) as issue_qnty,b.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.embel_name=3 and a.production_type in(2,3) and b.id in($cutting) group by a.challan_no,b.bundle_no,b.id,a.production_date,a.serving_company,a.location,a.production_type $order_by";  //and b.bundle_no is not null
	
	$sql_cutt_qc_result=sql_select($sql_cutt_qc);
	foreach($sql_cutt_qc_result as $rows)
	{
		if($rows[csf('production_type')]==3){$rec_qnty[$rows[csf('bundle_no')]]+=$rows[csf('issue_qnty')];}
		elseif($rows[csf('production_type')]==2){
			$cutting_qc_arr[$rows[csf('bundle_no')]]=array(
				challan_no =>$rows[csf('challan_no')],
				production_date =>$rows[csf('production_date')],
				bundle_no  =>$rows[csf('bundle_no')],
				issue_qnty =>$rows[csf('issue_qnty')],
				location=>$rows[csf('location')],
				serving_company=>$rows[csf('serving_company')]
			);
			}
	
	
	}
	
	
	
	?>
	<script>
	
		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_job_id").val(splitData[0]); 
			$("#hide_job_no").val(splitData[1]); 
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
             <table width="100%" border="1" rules="all">
            	<tr>
                    <td width="40"><strong>Buyer</strong> </td>
                    <td>: <? echo $buyer_arr[$buyer];?></td>
                    <td width="40"><strong>Style</strong> </td>
                    <td>: <? echo $style;?></td>
                    <td width="70"><strong>Job</strong></td>
                    <td>: <? echo $job;?></td>
                </tr>
            	<tr>
                    <td><strong>Order</strong></td>
                    <td>: <? echo $order;?></td>
                    <td><strong>Item</strong> </td>
                    <td>: <? echo $item;?></td>
                    <td><strong>Plan Cut Qty</strong> </td>
                    <td>: <? echo $cutQty;?></td>
                </tr>
           </table> 
            <? // if($cutting_data_arr[0]==''){ echo "<br><h1>Not Found Any Bundle.</h1>";exit();}?>
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
            	<thead>
                    <th width="30">SL</th>
                    <th width="100">Issue Ch.No</th>
                    <th width="80">Receive Date</th>
                    <th width="80">Receive From</th>
                    <th width="130">Location</th>
                    <th width="80">Bundle No</th>
                    <th width="80">Issue Qty</th>
                    <th>Receive Qty</th>
                </thead>
             </table>   
            <div style="max-height:280px; overflow-y:scroll;">
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
                <tbody>
                	<? 
					$i=0;
					foreach($cutting_qc_arr as $cutting_no=>$row){
						$i++;
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" style="cursor:pointer;">
                        <td align="center" width="30"><? echo $i;?></td>                 
                        <td align="center" width="100"><? echo $row[challan_no];?></td>
                        <td align="center" width="80"><? echo change_date_format($row[production_date]);?></td>
                        <td width="80"><? echo $company_arr[$row[serving_company]];?></td>
                        <td width="130"><? echo $location_arr[$row[location]];?></td>
                        <td align="center" width="80"><? echo $row[bundle_no];?></td> 
                        <td align="right" width="80"><? echo $row[issue_qnty]; $tot_issue_qty+=$row[issue_qnty];?></td>                
                        <td align="right"><? echo $rec_qnty[$row[bundle_no]]; $tot_bun_qty+=$rec_qnty[$row[bundle_no]];?> </td>                
                    </tr>
                    <? } ?>
            	</tbody>
             </table>
             </div>  
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
                <tfoot>
                    <th colspan="6">Total</th>                 
                    <th align="right" width="80"><?  echo $tot_issue_qty;?></th>
                    <th align="right" width="161"><?  echo $tot_bun_qty;?>&nbsp;&nbsp;</th>                 
                </tfoot>
           	</table>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}



if($action=="wash_blance_popup")
{
	echo load_html_head_contents("Print Issue Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	list($job,$order,$item,$buyer,$style,$cutQty)=explode('_',$job_info);
	$cutting=$_SESSION['bundle_id'];
	
	if($db_type==0){
		$order_by=" ORDER BY b.bundle_no";
	}
	else if($db_type==2){
		$order_by=" ORDER BY TO_NUMBER(REPLACE(SUBSTR(b.bundle_no, INSTR(b.bundle_no, '-')+1),'-',''))";
	}
	
	
	$sql_cutt_qc="select a.production_type,a.challan_no,a.production_date,a.serving_company,a.location,sum(b.production_qnty) as issue_qnty,b.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.embel_name=3 and a.production_type in(2,3) and b.id in($cutting) group by a.production_type,a.challan_no,b.bundle_no,b.id,a.production_date,a.serving_company,a.location $order_by";  //and b.bundle_no is not null
	
	$sql_cutt_qc_result=sql_select($sql_cutt_qc);
	foreach($sql_cutt_qc_result as $rows)
	{
		$cutting_qc_arr[$rows[csf('bundle_no')]]=array(
			challan_no =>$rows[csf('challan_no')],
			production_date =>$rows[csf('production_date')],
			bundle_no  =>$rows[csf('bundle_no')],
			issue_qnty =>$rows[csf('issue_qnty')],
			location=>$rows[csf('location')],
			serving_company=>$rows[csf('serving_company')]
		);
		
			if($rows[csf('production_type')]==2){$blance_qty[$rows[csf('bundle_no')]]+=$rows[csf('issue_qnty')];}
			elseif($rows[csf('production_type')]==3){$blance_qty[$rows[csf('bundle_no')]]-=$rows[csf('issue_qnty')];}
		
	}
	
	
	
	?>
	<script>
	
		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_job_id").val(splitData[0]); 
			$("#hide_job_no").val(splitData[1]); 
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
             <table width="100%" border="1" rules="all">
            	<tr>
                    <td width="40"><strong>Buyer</strong> </td>
                    <td>: <? echo $buyer_arr[$buyer];?></td>
                    <td width="40"><strong>Style</strong> </td>
                    <td>: <? echo $style;?></td>
                    <td width="70"><strong>Job</strong></td>
                    <td>: <? echo $job;?></td>
                </tr>
            	<tr>
                    <td><strong>Order</strong></td>
                    <td>: <? echo $order;?></td>
                    <td><strong>Item</strong> </td>
                    <td>: <? echo $item;?></td>
                    <td><strong>Plan Cut Qty</strong> </td>
                    <td>: <? echo $cutQty;?></td>
                </tr>
           </table> 
            <? // if($cutting_data_arr[0]==''){ echo "<br><h1>Not Found Any Bundle.</h1>";exit();}?>
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
            	<thead>
                    <th width="30">SL</th>
                    <th width="100">Issue Ch.No</th>
                    <th width="80">Issue Date</th>
                    <th width="80">Issue To</th>
                    <th width="130">Location</th>
                    <th width="80">Bundle No</th>
                    <th>Blance Qty</th>
                </thead>
             </table>   
            <div style="max-height:280px; overflow-y:scroll;">
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
                <tbody>
                	<? 
					$i=0;
					foreach($cutting_qc_arr as $cutting_no=>$row){
						if($blance_qty[$row[bundle_no]]){
						$i++;
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" style="cursor:pointer;">
                        <td align="center" width="30"><? echo $i;?></td>                 
                        <td align="center" width="100"><? echo $row[challan_no];?></td>
                        <td align="center" width="80"><? echo change_date_format($row[production_date]);?></td>
                        <td width="80"><? echo $company_arr[$row[serving_company]];?></td>
                        <td width="130"><? echo $location_arr[$row[location]];?></td>
                        <td align="center" width="80"><? echo $row[bundle_no];?></td>                 
                        <td align="right"><? echo $blance_qty[$row[bundle_no]]; $tot_bun_qty+=$blance_qty[$row[bundle_no]];?> </td>                
                    </tr>
                    <? }} ?>
            	</tbody>
             </table>
             </div>  
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
                <tfoot>
                    <th colspan="4">Total</th>                 
                    <th align="right" width="242"><?  echo $tot_bun_qty;?>&nbsp;&nbsp;</th>                 
                </tfoot>
           	</table>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); }




if($action=="sewing_blance")
{
	echo load_html_head_contents("sewing out Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	list($job,$order,$item,$buyer,$style,$cutQty,$company_id)=explode('_',$job_info);
	$cutting=$_SESSION['bundle_id'];
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name =$company_id and variable_list=23 and is_deleted=0 and status_active=1");
	
	if($prod_reso_allo==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_lib=array();
		$line_data=sql_select("select id, line_number from prod_resource_mst where is_deleted=0");
		foreach($line_data as $row)
		{
			$line='';
			$line_number=explode(",",$row[csf('line_number')]);
			foreach($line_number as $val)
			{
				if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
			}
			$line_lib[$row[csf('id')]]=$line;
		}

	}
	else
	{
	$line_lib=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );	
	}
	
	$floor_lib=return_library_array( "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0", "id", "floor_name"  );	
	
	
		if($db_type==0){
			$order_by=" ORDER BY b.bundle_no";
		}
		else if($db_type==2){
			// $order_by=" ORDER BY TO_NUMBER(REPLACE(SUBSTR(b.bundle_no, INSTR(b.bundle_no, '-')+1),'-',''))";
			$order_by=" ORDER BY REPLACE(SUBSTR(b.bundle_no, INSTR(b.bundle_no, '-')+1),'-','')";
		}
	
	$cutting_id_arr = explode(",", $cutting);
	$cutting_cond = where_con_using_array($cutting_id_arr,0,"b.id");
	
	
	$sql_cutt_qc="SELECT a.production_type,a.challan_no,a.production_date,a.serving_company,a.location,a.sewing_line,a.floor_id,sum(b.production_qnty) as issue_qnty,b.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $cutting_cond group by a.challan_no,b.bundle_no,b.id,a.production_date,a.serving_company,a.location,a.sewing_line,a.floor_id,a.production_type $order_by";  //and b.bundle_no is not null
	
	$sql_cutt_qc_result=sql_select($sql_cutt_qc);
	foreach($sql_cutt_qc_result as $rows)
	{
		if($rows[csf('production_type')]==5){$out_qnty[$rows[csf('bundle_no')]]+=$rows[csf('issue_qnty')];}
		elseif($rows[csf('production_type')]==4){
			$cutting_qc_arr[$rows[csf('bundle_no')]]=array(
				challan_no =>$rows[csf('challan_no')],
				production_date =>$rows[csf('production_date')],
				bundle_no  =>$rows[csf('bundle_no')],
				issue_qnty =>$rows[csf('issue_qnty')],
				location=>$rows[csf('location')],
				sewing_line=>$rows[csf('sewing_line')],
				floor_id=>$rows[csf('floor_id')],
				serving_company=>$rows[csf('serving_company')]
			);
			}
	
	
	}
	
	
	
	?>
	<script>
	
		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_job_id").val(splitData[0]); 
			$("#hide_job_no").val(splitData[1]); 
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
             <table width="100%" border="1" rules="all">
            	<tr>
                    <td width="40"><strong>Buyer</strong> </td>
                    <td>: <? echo $buyer_arr[$buyer];?></td>
                    <td width="40"><strong>Style</strong> </td>
                    <td>: <? echo $style;?></td>
                    <td width="70"><strong>Job</strong></td>
                    <td>: <? echo $job;?></td>
                </tr>
            	<tr>
                    <td><strong>Order</strong></td>
                    <td>: <? echo $order;?></td>
                    <td><strong>Item</strong> </td>
                    <td>: <? echo $item;?></td>
                    <td><strong>Plan Cut Qty</strong> </td>
                    <td>: <? echo $cutQty;?></td>
                </tr>
           </table> 
            <? // if($cutting_data_arr[0]==''){ echo "<br><h1>Not Found Any Bundle.</h1>";exit();}?>
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
            	<thead>
                    <th width="30">SL</th>
                    <th width="50">Issue Ch.No</th>
                    <th width="80">Out Date</th>
                    <th width="100">Sew. Company</th>
                    <th width="100">Location</th>
                    <th width="80">Floor</th>
                    <th width="80">Line</th>
                    <th width="60">Bundle No</th>
                    <th>Blance Qty</th>
                </thead>
             </table>   
            <div style="max-height:280px; overflow-y:scroll;">
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
                <tbody>
                	<? 
					$i=0;
					foreach($cutting_qc_arr as $cutting_no=>$row){
						$blance=($row[issue_qnty]-$out_qnty[$row[bundle_no]]);
						if($blance){
						$i++;
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" style="cursor:pointer;">
                        <td align="center" width="30"><? echo $i;?></td>                 
                        <td align="center" width="50"><? echo $row[challan_no];?></td>
                        <td align="center" width="80"><? echo change_date_format($row[production_date]);?></td>
                        <td width="100"><? echo $company_arr[$row[serving_company]];?></td>
                        <td width="100"><? echo $location_arr[$row[location]];?></td>
                        <td width="80"><? echo $floor_lib[$row[floor_id]];?></td>
                        <td width="80"><? echo $line_lib[$row[sewing_line]];?></td>
                        <td align="center" width="60"><? echo $row[bundle_no];?></td> 
                        <td align="right"><? echo $blance; $tot_blance_qty+=$blance;?> </td>                
                    </tr>
                    <? }} ?>
            	</tbody>
             </table>
             </div>  
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
                <tfoot>
                    <th colspan="6">Total</th>                 
                    <th align="right" width="160"><?  echo $tot_blance_qty;?>&nbsp;&nbsp;</th>                 
                </tfoot>
           	</table>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}



if($action=="iron_input_popup")
{
	echo load_html_head_contents("sewing in Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	list($job,$order,$item,$buyer,$style,$cutQty,$company_id)=explode('_',$job_info);
	$cutting=$_SESSION['bundle_id'];
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name =$company_id and variable_list=23 and is_deleted=0 and status_active=1");
	
	if($prod_reso_allo==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_lib=array();
		$line_data=sql_select("select id, line_number from prod_resource_mst where is_deleted=0");
		foreach($line_data as $row)
		{
			$line='';
			$line_number=explode(",",$row[csf('line_number')]);
			foreach($line_number as $val)
			{
				if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
			}
			$line_lib[$row[csf('id')]]=$line;
		}

	}
	else
	{
	$line_lib=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );	
	}
	
	$floor_lib=return_library_array( "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0", "id", "floor_name"  );	

	if($db_type==0){
		$order_by=" ORDER BY b.bundle_no";
	}
	else if($db_type==2){
		$order_by=" ORDER BY TO_NUMBER(REPLACE(SUBSTR(b.bundle_no, INSTR(b.bundle_no, '-')+1),'-',''))";
	}
	
	$sql_cutt_qc="select a.challan_no,a.production_date,a.serving_company,a.location,a.sewing_line,a.floor_id,sum(b.production_qnty) as issue_qnty,b.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type =7 and b.id in($cutting) group by a.challan_no,b.bundle_no,b.id ,a.production_date,a.serving_company,a.location,a.sewing_line,a.floor_id $order_by";
	
	$sql_cutt_qc_result=sql_select($sql_cutt_qc);
	foreach($sql_cutt_qc_result as $rows)
	{
		$cutting_qc_arr[$rows[csf('bundle_no')]]=array(
			challan_no =>$rows[csf('challan_no')],
			production_date =>$rows[csf('production_date')],
			bundle_no  =>$rows[csf('bundle_no')],
			issue_qnty =>$rows[csf('issue_qnty')],
			location   =>$rows[csf('location')],
			sewing_line=>$rows[csf('sewing_line')],
			floor_id   =>$rows[csf('floor_id')],
			serving_company=>$rows[csf('serving_company')]
		);
	}
	
	
	
	?>
	<script>
	
		function js_set_value(str)
		{
			var splitData = str.split("_");
			$("#hide_job_id").val(splitData[0]); 
			$("#hide_job_no").val(splitData[1]); 
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
             <table width="100%" border="1" rules="all">
            	<tr>
                    <td width="40"><strong>Buyer</strong> </td>
                    <td>: <? echo $buyer_arr[$buyer];?></td>
                    <td width="40"><strong>Style</strong> </td>
                    <td>: <? echo $style;?></td>
                    <td width="70"><strong>Job</strong></td>
                    <td>: <? echo $job;?></td>
                </tr>
            	<tr>
                    <td><strong>Order</strong></td>
                    <td>: <? echo $order;?></td>
                    <td><strong>Item</strong> </td>
                    <td>: <? echo $item;?></td>
                    <td><strong>Plan Cut Qty</strong> </td>
                    <td>: <? echo $cutQty;?></td>
                </tr>
           </table> 
            <? // if($cutting_data_arr[0]==''){ echo "<br><h1>Not Found Any Bundle.</h1>";exit();}?>
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
            	<thead>
                    <th width="30">SL</th>
                    <th width="100">Issue Ch.No</th>
                    <th width="80">Input Date</th>
                    <th width="80">Sew. Company</th>
                    <th width="130">Location</th>
                    <th width="80">Floor</th>
                    <th width="80">Line</th>
                    <th width="80">Bundle No</th>
                    <th>Input Qty</th>
                </thead>
             </table>   
            <div style="max-height:280px; overflow-y:scroll;">
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
                <tbody>
                	<? 
					$i=0;
					foreach($cutting_qc_arr as $cutting_no=>$row){
						$i++;
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" style="cursor:pointer;">
                        <td align="center" width="30"><? echo $i;?></td>                 
                        <td align="center" width="100"><? echo $row[challan_no];?></td>
                        <td align="center" width="80"><? echo change_date_format($row[production_date]);?></td>
                        <td width="80"><? echo $company_arr[$row[serving_company]];?></td>
                        <td width="130"><? echo $location_arr[$row[location]];?></td>
                        <td width="80"><? echo $floor_lib[$row[floor_id]];?></td>
                        <td width="80"><? echo $line_lib[$row[sewing_line]];?></td>
                        <td align="center" width="80"><? echo $row[bundle_no];?></td>                 
                        <td align="right"><? echo $row[issue_qnty]; $tot_bun_qty+=$row[issue_qnty];?> </td>                
                    </tr>
                    <? } ?>
            	</tbody>
             </table>
             </div>  
            <table width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list">
                <tfoot>
                    <th colspan="4">Total</th>                 
                    <th align="right" width="80"><?  echo $tot_bun_qty;?>&nbsp;&nbsp;</th>                 
                </tfoot>
           	</table>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}







?>
      
 