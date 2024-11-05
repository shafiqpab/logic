<?php
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This file for daily order entry report info.
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam Reza 
Creation date 	: 	24-08-2016
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.yarns.php');
require_once('../../../../includes/class4/class.conversions.php');
require_once('../../../../includes/class4/class.trims.php');
require_once('../../../../includes/class4/class.emblishments.php');
require_once('../../../../includes/class4/class.commisions.php');
require_once('../../../../includes/class4/class.commercials.php');
require_once('../../../../includes/class4/class.others.php');
require_once('../../../../includes/class4/class.washes.php');
require_once('../../../../includes/class4/class.fabrics.php');

extract($_REQUEST);
 
$pc_time= add_time(date("H:i:s",time()),360);  
$pc_date = date("Y-m-d",strtotime(add_time(date("H:i:s",time()),360)));

$permission=$_SESSION['page_permission'];

//---------------------------------------------------- Start
if($action=="print_button_variable_setting")
    {
        $print_report_format_arr = return_library_array("select format_id,format_id from lib_report_template where template_name =$data and module_id=11 and report_id=73 and is_deleted=0 and status_active=1","format_id","format_id");
        echo "print_report_button_setting('".implode(',',$print_report_format_arr)."');\n";
        exit(); 
    }

if ($action=="load_drop_down_buyer")
{


	echo create_drop_down( "cbo_buyer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name  order by buyer_name","id,buyer_name", 1, "-- All --", $selected, "" );     	 
	exit();
}

if ($action=="load_drop_down_agent")
{
	echo create_drop_down( "cbo_agent_name", 100, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (20,21)) group by a.id,a.buyer_name  order by buyer_name","id,buyer_name", 1, "-- All --", $selected, "" );  
	exit(); 	 
} 

if ($action=="report_generate")
{	
	//$process = array( &$_POST );
	//extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	$cbo_fabric_nature=str_replace("'","",$cbo_fabric_nature);
	$txt_file_no=str_replace("'","",$txt_file_no);
	$txt_ref_no=str_replace("'","",$txt_ref_no);
	
	if($cbo_company_name==0 || $cbo_company_name=="")
	{ 
		$company_name="";$company_name2="";
	}
	else 
	{ 
		$company_name = "and a.company_name=$cbo_company_name";$company_name2 = "and a.company_id=$cbo_company_name";
	}//fabric_source//item_category
	
	if($cbo_fabric_nature!=0) $fab_nature_cond= " and a.item_category =$cbo_fabric_nature";else $fab_nature_cond="";
	if($cbo_fabric_source!=0) $fab_source_cond= " and a.fabric_source =$cbo_fabric_source";else $fab_source_cond="";

	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_cond="";
		}
		else
		{
			$buyer_id_cond="";$buyer_id_cond2="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
		$buyer_id_cond2=" and a.buyer_id=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	$cbo_ship_status=str_replace("'","",$cbo_ship_status);
	$shipping_status_cond='';
	if($cbo_ship_status!=0)
	{
		$shipping_status_cond=" and b.shiping_status=$cbo_ship_status";
	}
	if($txt_file_no!='') $file_no_cond="and b.file_no=$txt_file_no";else $file_no_cond="";
	if($txt_ref_no!='') $ref_no_cond="and b.grouping='$txt_ref_no'";else $ref_no_cond="";
	
	$search_text='';$search_text2='';
	if(str_replace("'","",$txt_style_ref)!=""){$search_text = " and a.style_ref_no like '%".str_replace("'","",$txt_style_ref)."%'";}
	if(str_replace("'","",$txt_job_no)!=""){$search_text .= " and a.job_no like '%".str_replace("'","",$txt_job_no)."'";}
	if(str_replace("'","",$txt_job_no)!=""){$search_text2 .= " and d.job_no like '%".str_replace("'","",$txt_job_no)."%'";}
	if(str_replace("'","",$txt_order_no)!=""){$search_text .= " and b.po_number like '%".str_replace("'","",$txt_order_no)."%'";}
	if(str_replace("'","",$cbo_order_status)!=0){$search_text .= " and b.is_confirmed = '".str_replace("'","",$cbo_order_status)."'";}
	if(str_replace("'","",$cbo_agent_name)!=0) $search_text.=" and a.agent_name=$cbo_agent_name";
	if(str_replace("'","",$cbo_year)!=0)
	{
		if($db_type==0){$search_text.=" and YEAR(a.insert_date)=$cbo_year";}
		else if($db_type==2){$search_text.=" and to_char(a.insert_date,'YYYY')=$cbo_year";}
	}
	
	
	$txt_date_from=str_replace("'","",trim($txt_date_from));
	$txt_date_to=str_replace("'","",trim($txt_date_to));

	$date_category='b.pub_shipment_date';
	$date_heading='Ship Date';
	
	if($txt_date_from!="" && $txt_date_to!=""){
		if($db_type==0){
			$start_date = date('Y-m-d H:i:s',strtotime($txt_date_from)); 
			$end_date = date("Y-m-d",strtotime($txt_date_to));
			if(str_replace("'","",$cbo_date_type)==2){
				$search_text .=" and b.insert_date between '".$start_date."' and '".$end_date." 23:59:59'";
				$search_text2 .=" and c.insert_date between '".$start_date."' and '".$end_date." 23:59:59'";
			}
			else
			{
				$search_text .=" and b.pub_shipment_date between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and c.pub_shipment_date between '".$start_date."' and '".$end_date."'";
			}
		}
		else
		{
			
			$start_date = change_date_format(date("Y-m-d H:i:s",strtotime($txt_date_from)),'','',1);
			$end_date = change_date_format(date("Y-m-d H:i:s",strtotime($txt_date_to)),'','',1);
			if(str_replace("'","",$cbo_date_type)==3){
				$search_text.=" and b.insert_date between '".$start_date."' and '".$end_date." 11:59:59 PM'";
				$search_text2.=" and c.insert_date between '".$start_date."' and '".$end_date." 11:59:59 PM'";
			}
			elseif(str_replace("'","",$cbo_date_type)==4)
			{
				$search_text .=" and d.country_ship_date between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and d.country_ship_date between '".$start_date."' and '".$end_date."'";
			}
			elseif(str_replace("'","",$cbo_date_type)==2)
			{
				$search_text .=" and b.shipment_date between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and c.shipment_date between '".$start_date."' and '".$end_date."'";
			}
			else
			{
				$search_text .=" and b.pub_shipment_date between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and c.pub_shipment_date between '".$start_date."' and '".$end_date."'";
			}
		
		}
	}

	if($db_type==0){
		$start_date = date('Y-m-d H:i:s',strtotime($txt_date_from)); 
		$end_date = date("Y-m-d",strtotime($txt_date_to));
		if(str_replace("'","",$cbo_date_type)==2){
			$date_category='b.insert_date';
			$date_heading='PO Insert Date';
		}
		else
		{
			$date_category='b.pub_shipment_date';
			$date_heading='Ship Date';
		}
	}
	else
	{
		if(str_replace("'","",$cbo_date_type)==3){
			$date_category='b.insert_date';
			$date_heading='PO Insert Date';
		}
		elseif(str_replace("'","",$cbo_date_type)==2)
		{
			$date_category='b.shipment_date';
			$date_heading='Original Ship Date';
		}
		elseif(str_replace("'","",$cbo_date_type)==4)
		{
			$date_category='b.pub_shipment_date';
			$date_heading='Country Ship Date';
		}
		else
		{
			$date_category='b.pub_shipment_date';
			$date_heading='Ship Date';
		}
	}

	if($db_type==0){
		$start_date = date('Y-m-d H:i:s',strtotime($txt_date_from)); 
		$end_date = date("Y-m-d",strtotime($txt_date_to));
		if(str_replace("'","",$cbo_date_type)==2){
			$date_category='b.insert_date';
			$date_heading='PO Insert Date';
		}
		else
		{
			$date_category='b.pub_shipment_date';
			$date_heading='Ship Date';
		}
	}
	else
	{	
		if(str_replace("'","",$cbo_date_type)==4)
		{
			$date_category='b.pub_shipment_date';
			$date_heading='Country Ship Date';
		}
		elseif(str_replace("'","",$cbo_date_type)==3){
			$date_category='b.insert_date';
			$date_heading='PO Insert Date';
		}
		elseif(str_replace("'","",$cbo_date_type)==2)
		{
			$date_category='b.shipment_date';
			$date_heading='Original Ship Date';
		}
		else
		{
			$date_category='b.pub_shipment_date';
			$date_heading='Ship Date';
		}
	
	}
	
	
	//function for omit zero value
	function omitZero($value){
		if($value==0){
			return "";
			}
		else {
			return $value;
		}
	}
	//echo omitZero(10);
	
	$items_library=return_library_array( "select id, item_name from lib_garment_item where  status_active=1 and is_deleted=0", "id", "item_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0", "id", "buyer_name"  );
	$company_lib=return_library_array( "select id, company_name from lib_company where  status_active=1 and is_deleted=0", "id", "company_name"  );
	//$supplier_library = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	//$user_arr = return_library_array("select id,user_name from user_passwd where valid=1","id","user_name");
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='knit_order_entry'",'master_tble_id','image_location');
			
	//$ff_qnty_array=return_library_array( "select sum(a.fin_fab_qnty) as fin_fab_qnty,a.po_break_down_id from wo_booking_dtls a where  a.booking_type=1 $poCon and a.status_active=1 and a.is_deleted=0 group by a.po_break_down_id", "po_break_down_id", "fin_fab_qnty");
	
	$fab_sql="select b.po_break_down_id as po_id ,b.grey_fab_qnty,a.booking_no_prefix_num,a.booking_no,
	(CASE WHEN b.booking_type=1 THEN b.fin_fab_qnty END) as fin_fab_qnty
	 from wo_booking_mst a, wo_booking_dtls b,wo_po_break_down c,wo_po_details_master d where a.booking_no=b.booking_no and  b.po_break_down_id=c.id and d.job_no=c.job_no_mst and b.booking_type=1 and b.status_active=1 and b.is_deleted=0 $company_name2 $buyer_id_cond2 $fab_nature_cond $fab_source_cond $search_text2";
	$po_ids='';
	$fab_result = sql_select($fab_sql);// or die(mysql_error());
	 foreach($fab_result as $row)
	 {
		if($po_ids=='') $po_ids=$row[csf('po_id')];else $po_ids.=",".$row[csf('po_id')];
		$ff_qnty_array[$row[csf('po_id')]]+=$row[csf('fin_fab_qnty')];
		$gf_qnty_array[$row[csf('po_id')]]+=$row[csf('grey_fab_qnty')];
		$booking_no_array[$row[csf('po_id')]][$row[csf('booking_no')]]=$row[csf('booking_no_prefix_num')];
		 
	 }
	 unset($fab_result);
	// echo  $po_ids;
	 if($cbo_fabric_nature!=0 || $cbo_fabric_source!=0)
	 {
		 $all_po=array_unique(explode(",",$po_ids));
		$po_arr_cond=array_chunk($all_po,1000, true);
		$po_cond_for_in="";$po=0;
		foreach($po_arr_cond as $key=>$value)
		{
		   if($po==0)
		   {
			$po_cond_for_in=" and b.id  in(".implode(",",$value).")"; 
		
		   }
		   else //po_break_down_id
		   {
			$po_cond_for_in.=" or b.id  in(".implode(",",$value).")";
			
		   }
		   $po++;
		}	
	 }
	//echo $po_cond_for_in;die;
		if($db_type==0){$date_diff="DATEDIFF(b.shipment_date,b.po_received_date) as  date_diff,";}
		else{$date_diff=" (b.shipment_date - b.po_received_date) as  date_diff,";}
		
		$groupby=" group by a.id,b.shipment_date,b.po_received_date, a.buyer_name,a.total_set_qnty,a.job_no_prefix_num ,a.job_no,a.style_ref_no,b.po_number,a.set_smv,a.set_break_down,a.order_uom,a.gmts_item_id,b.id,a.buyer_name,b.unit_price,b.insert_date,b.pub_shipment_date,b.po_received_date,b.insert_date,b.grouping, b.file_no,b.is_confirmed,b.inserted_by,b.details_remarks";

    $sql="select $date_diff a.buyer_name,a.total_set_qnty,a.job_no_prefix_num ,a.job_no,a.style_ref_no,b.po_number,a.set_smv,a.set_break_down,a.order_uom,a.gmts_item_id,b.id,a.buyer_name,sum(d.order_quantity) as po_quantity,sum(d.order_quantity) as  po_qty,sum(d.plan_cut_qnty) as plan_cut,b.unit_price as unit_price,$date_category as pub_shipment_date,b.po_received_date,b.insert_date,b.grouping, b.file_no,b.is_confirmed,b.inserted_by,sum(d.order_total) as po_total_price,b.details_remarks,a.id as job_id  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown d where a.id=b.job_id and a.id=d.job_id  and b.id=d.po_break_down_id $company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and d.is_deleted=0 and d.status_active=1 $search_text $buyer_id_cond $shipping_status_cond $po_cond_for_in $file_no_cond $ref_no_cond $groupby order by $date_category";  
		
		/*if(str_replace("'","",$cbo_date_type)!=4) //pub_shipment_date /country_ship_date
		{
		 $groupby=" group by a.id,b.shipment_date,b.po_received_date, a.buyer_name,a.total_set_qnty,a.job_no_prefix_num ,a.job_no,a.style_ref_no,b.po_number,a.set_smv,a.set_break_down,a.order_uom,a.gmts_item_id,b.id,a.buyer_name,b.unit_price,b.insert_date,b.pub_shipment_date,b.po_received_date,b.insert_date,b.grouping, b.file_no,b.is_confirmed,b.inserted_by,b.details_remarks";

   $sql="select $date_diff a.buyer_name,a.total_set_qnty,a.job_no_prefix_num ,a.job_no,a.style_ref_no,b.po_number,a.set_smv,a.set_break_down,a.order_uom,a.gmts_item_id,b.id,a.buyer_name,sum(d.order_quantity) as po_quantity,sum(d.order_quantity) as  po_qty,sum(d.plan_cut_qnty) as plan_cut,b.unit_price as unit_price,$date_category as pub_shipment_date,b.po_received_date,b.insert_date,b.grouping, b.file_no,b.is_confirmed,b.inserted_by,sum(d.order_total) as po_total_price,b.details_remarks,a.id as job_id  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown d where a.id=b.job_id and a.id=d.job_id  and b.id=d.po_break_down_id $company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and d.is_deleted=0 and d.status_active=1 $search_text $buyer_id_cond $shipping_status_cond $po_cond_for_in $file_no_cond $ref_no_cond $groupby order by $date_category";  
		}
		else
		{
			 $groupby=" group by a.id,b.shipment_date,b.po_received_date, a.buyer_name,a.total_set_qnty,a.job_no_prefix_num ,a.job_no,a.style_ref_no,b.po_number,a.set_smv,a.set_break_down,a.order_uom,a.gmts_item_id,b.id,a.buyer_name,b.unit_price,b.insert_date,b.pub_shipment_date,b.po_received_date,b.insert_date,b.grouping, b.file_no,b.is_confirmed,b.inserted_by,b.details_remarks,d.country_ship_date,d.country_id";

   $sql="select $date_diff a.buyer_name,a.total_set_qnty,a.job_no_prefix_num ,a.job_no,a.style_ref_no,b.po_number,a.set_smv,a.set_break_down,a.order_uom,a.gmts_item_id,b.id,a.buyer_name,sum(d.order_quantity) as po_quantity,sum(d.order_quantity) as  po_qty,sum(d.plan_cut_qnty) as plan_cut,b.unit_price as unit_price,$date_category as pub_shipment_date,b.po_received_date,b.insert_date,b.grouping, b.file_no,b.is_confirmed,b.inserted_by,sum(d.order_total) as po_total_price,b.details_remarks,a.id as job_id,d.country_id  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown d where a.id=b.job_id and a.id=d.job_id  and b.id=d.po_break_down_id $company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and d.is_deleted=0 and d.status_active=1 $search_text $buyer_id_cond $shipping_status_cond $po_cond_for_in $file_no_cond $ref_no_cond $groupby order by $date_category";  
		}*/
		 
		 /*$sql="select $date_diff a.buyer_name,a.total_set_qnty,a.job_no_prefix_num ,a.job_no,a.style_ref_no,b.po_number,a.set_smv,a.set_break_down,a.order_uom,a.gmts_item_id,b.id,a.buyer_name,(b.po_quantity*a.total_set_qnty) as po_quantity,b.po_quantity as  po_qty,b.plan_cut,b.unit_price,b.pub_shipment_date,b.po_received_date,b.insert_date,b.grouping, b.file_no,b.is_confirmed,b.inserted_by,b.po_total_price,b.details_remarks,d.size_qty from wo_po_details_master a join wo_po_break_down b on a.job_no=b.job_no_mst left join ppl_cut_lay_mst c on a.job_no = c.job_no left join ppl_cut_lay_bundle d on c.id=d.mst_id where a.is_deleted=0 $company_name and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $search_text $buyer_id_cond $shipping_status_cond $po_cond_for_in $file_no_cond $ref_no_cond order by b.pub_shipment_date"; //echo $sql; die;*/
		 
	    //echo $sql;	die;
		 
	$po_id_str='';	 		
	$order_sql_result = sql_select($sql) or die(mysql_error());
	$job_no_arr=array();
	$all_job_IdArr=array();
	foreach($order_sql_result as $rows){
		//$order_data_arr[$rows[csf('buyer_name')]][]=$rows;
		$order_data_arr[]=$rows;
		$order_id_arr[$rows[csf('id')]]=$rows[csf('id')];
		if(!empty($po_id_str))
		{
			$po_id_str.=",".$rows[csf('id')];
		}
		else $po_id_str=$rows[csf('id')];
		$po_buyer_array[$rows[csf('id')]]['buyer']=$rows[csf('buyer_name')];
		$po_buyer_array[$rows[csf('id')]]['job_no']=$rows[csf('job_no')];
		array_push($job_no_arr, $rows[csf('job_no')]);
		array_push($all_job_IdArr, $rows[csf('job_id')]);
	}
	unset($order_sql_result);

	
	/*$lay_cut_qty=sql_select("select a.buyer_name,a.job_no,b.id as po_id,sum(d.size_qty) as lay_cut_qty from wo_po_details_master a join wo_po_break_down b on a.job_no=b.job_no_mst left join ppl_cut_lay_mst c on a.job_no = c.job_no left join ppl_cut_lay_bundle d on c.id=d.mst_id where a.is_deleted=0 $company_name and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $search_text $buyer_id_cond $shipping_status_cond $po_cond_for_in $file_no_cond $ref_no_cond group by a.buyer_name,a.job_no,d.size_qty order by a.buyer_name");
	$lay_cut_data= array();
	foreach ($lay_cut_qty as $data) {
		$lay_cut_data[$data[csf('job_no')]][$data[csf('buyer_name')]] = $data[csf('lay_cut_qty')];
	}*/
	
	
	
		$po_id_list_arr=array_chunk($order_id_arr,999);
	
		$poCon = " and ";$poCon2 = " and ";$poCon3 = " and ";$poCon4 = " and ";$poCon5 = " and ";$poCon6 = " and ";$poCon7 = " and ";
		$p=1;
		foreach($po_id_list_arr as $po_process)
		{
			if($p==1) $poCon .="  ( a.po_break_down_id in(".implode(',',$po_process).")"; 
			else  $poCon .=" or a.po_break_down_id in(".implode(',',$po_process).")";
			
			if($p==1) $poCon2 .="  ( b.po_id in(".implode(',',$po_process).")"; 
			else  $poCon2 .=" or b.po_id in(".implode(',',$po_process).")";
			
			if($p==1) $poCon3 .="  ( po_break_down_id in(".implode(',',$po_process).")"; 
			else  $poCon3 .=" or po_break_down_id in(".implode(',',$po_process).")";
			
			if($p==1) $poCon4 .="  ( a.po_breakdown_id in(".implode(',',$po_process).")"; 
			else  $poCon4 .=" or a.po_breakdown_id in(".implode(',',$po_process).")";
			
			
			if($p==1) $poCon5 .="  ( po_breakdown_id in(".implode(',',$po_process).")"; 
			else  $poCon5 .=" or po_breakdown_id in(".implode(',',$po_process).")";
			
			if($p==1) $poCon6 .="  ( b.order_id in(".implode(',',$po_process).")"; 
			else  $poCon6 .=" or b.order_id in(".implode(',',$po_process).")";
			if($p==1) $poCon7 .="  ( b.po_breakdown_id in(".implode(',',$po_process).")"; 
			else  $poCon7 .=" or b.po_breakdown_id in(".implode(',',$po_process).")";
			
			
			$p++;
		}


		//$poCon .=")";$poCon2 .=")";$poCon3 .=")";$poCon4 .=")";$poCon5 .=")";$poCon6 .=")";
		$poCon .=")";$poCon2 .=")";$poCon3 .=")";$poCon4 .=")";$poCon5 .=")";$poCon6 .=")";$poCon7 .=")";

$po_job_cond=where_con_using_array($order_id_arr,0,"b.order_id");
		$grey_and_dyeing_cost_sql="SELECT sum(b.amount) as amount, b.order_id as order_id,a.process_id as process_id
						  FROM subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b
						 WHERE     a.id = b.mst_id
						       AND a.status_active = 1
						       AND a.is_deleted = 0
						       AND b.status_active = 1
						       AND b.is_deleted = 0
						       AND a.process_id in( 2,4)
						       $po_job_cond
						    group by b.order_id,a.process_id
						 UNION ALL
						SELECT sum(b.amount) as amount, b.order_id as order_id,a.process_id as process_id
						  FROM subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b
						 WHERE     a.id = b.mst_id
						       AND a.status_active = 1
						       AND a.is_deleted = 0
						       AND b.status_active = 1
						       AND b.is_deleted = 0
						       AND a.process_id in ( 2,4)
						       $po_job_cond
						   group by b.order_id,a.process_id";
	    	//echo $grey_and_dyeing_cost_sql;die;
	   		 $grey_and_dyeing_cost_result=sql_select($grey_and_dyeing_cost_sql);

	   		 $condition= new condition();
			 
		 if((str_replace("'","",$cbo_date_type) ==1 || str_replace("'","",$cbo_date_type) ==0) && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!=''){
			  //$condition->country_ship_date(" between '$start_date' and '$end_date'");
			   $condition->pub_shipment_date(" between '$start_date' and '$end_date'");
		 }
		else if(str_replace("'","",$cbo_date_type) ==2 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!=''){
			  //$condition->country_ship_date(" between '$start_date' and '$end_date'");
			   $condition->shipment_date(" between '$start_date' and '$end_date'");
		 }
		 else if(str_replace("'","",$cbo_date_type) ==4 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!=''){
			$condition->country_ship_date(" between '$start_date' and '$end_date'");
			
	   }
		 else
		 {
			 if($db_type==0)
			   {
				$condition->insert_date(" between '".$start_date."' and '".$end_date." 23:59:59'");
			   }
			   else
			   {
				   $condition->insert_date(" between '".$start_date."' and '".$end_date." 11:59:59 PM'");
			   }
		 }
				 //
			 if(str_replace("'","",$txt_job_no) !=''){
				 $condition->job_no_prefix_num("in($txt_job_no)");
			}
			  if(str_replace("'","",$txt_style_ref) !=''){
				 $condition->style_ref_no("=$txt_style_ref");
			}
			if(str_replace("'","",$txt_file_no)!='')
			{
			   $condition->file_no("=$txt_file_no"); 
			}
			if(str_replace("'","",$txt_ref_no)!='')
			{
			   $condition->grouping("=$txt_ref_no"); 
			}
			if(str_replace("'","",$txt_order_no)!='')
			{
			   $condition->po_number("=$txt_order_no"); 
			}
			
			if(str_replace("'","",$po_id_str)!='')
			{
				//$condition->po_id_in("$po_id_str"); 
			}
		   $condition->init();
		   $yarn= new yarn($condition);
		 // echo $yarn->getQuery(); die;
		  $yarn_costing_arr=$yarn->getOrderCountCompositionColorAndTypeWiseYarnAmountArray();
			//print_r($yarn_costing_arr);
		 $job_cond=where_con_using_array($all_job_IdArr,0,"job_id");
		 $sql_yarn_budget = "select  count_id, copm_one_id, color,type_id  from wo_pre_cost_fab_yarn_cost_dtls where status_active=1 $job_cond group by count_id, copm_one_id, color,type_id";
		 $res_yarn_budget=sql_select($sql_yarn_budget); 

		 $po_wise_yarn_budget_data=array();

		 foreach( $res_yarn_budget as $row )
         {
     		foreach($order_id_arr as $pid)
			{
			 	$po_wise_yarn_budget_data[$pid]+=$yarn_costing_arr[$pid][$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("color")]][$row[csf("type_id")]];	
			}
		}
		// echo "<pre>";
		// print_r($po_wise_yarn_budget_data);
		// echo "</pre>";

		$po_cond_for_in4=str_replace("b.order_id", "b.po_breakdown_id", $poCon6);
		 $po_Idcond_for_in4=where_con_using_array($order_id_arr,0,"b.po_breakdown_id");
		$yarnIssueData="select b.prod_id,sum(a.cons_amount) as cons_amount,sum(a.cons_quantity) as cons_quantity
				from inv_transaction a, order_wise_pro_details b,product_details_master c where a.id=b.trans_id and c.id=b.prod_id and c.id=a.prod_id and a.item_category in(1) and a.transaction_type in(2,4,5,6) and b.entry_form ='3' and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and b.entry_form in(3,11,25)  $po_Idcond_for_in4  group by  b.prod_id";
		//echo $yarnIssueData;
		$resultyarnIssueData = sql_select($yarnIssueData);
		$all_prod_ids=array();

		foreach($resultyarnIssueData as $row)
		{
			
			array_push($all_prod_ids, $row[csf('prod_id')]);
			
			$yarn_issue_amt_arr[$row[csf('prod_id')]]['amt']+=$row[csf('cons_amount')];
			$yarn_issue_amt_arr[$row[csf('prod_id')]]['qty']+=$row[csf('cons_quantity')];
		}
		unset($resultyarnIssueData);
		
		$jobId_cond_for_in=where_con_using_array($all_job_IdArr,0,"a.job_no_id"); 
			
			$yarn_dyeing_costArray=array(); //product_id
			 $yarndyeing_sql="select b.booking_date,b.id,b.currency,b.is_short,b.ydw_no,a.job_no,a.yarn_color,a.product_id,avg(a.dyeing_charge) as dyeing_charge,  sum(a.amount) as amnt,sum(a.yarn_wo_qty) as yarn_wo_qty from wo_yarn_dyeing_mst b,wo_yarn_dyeing_dtls a where  b.id=a.mst_id and b.entry_form in(41,94) and a.status_active=1 and a.is_deleted=0 $jobId_cond_for_in group by b.id,b.currency,b.is_short,a.job_no,a.product_id,b.ydw_no,a.yarn_color,b.booking_date";
			 //echo $yarndyeing_sql;
			$yarndyeing_result = sql_select($yarndyeing_sql);
			foreach($yarndyeing_result as $yarnRow)
			{
				$yarn_dyeing_costArray[$yarnRow[csf('job_no')]]['avg_rate']=$yarnRow[csf('amnt')]/$yarnRow[csf('yarn_wo_qty')];
				$yarn_dyeing_costArray2[$yarnRow[csf('id')]]['job']=$yarnRow[csf('job_no')];
				$yarn_dyeing_costArray2[$yarnRow[csf('id')]]['ydw_no']=$yarnRow[csf('ydw_no')];
				$yarn_dyeing_isshort_arr[$yarnRow[csf('ydw_no')]]['is_short']=$yarnRow[csf('is_short')];
				$yarn_dyeing_isshort_arr[$yarnRow[csf('ydw_no')]]['booking_date']=$yarnRow[csf('booking_date')];
				$yarn_dyeing_curr_arr[$yarnRow[csf('ydw_no')]]['currency']=$yarnRow[csf('currency')];
				$yarn_dyeing_rate_arr[$yarnRow[csf('job_no')]][$yarnRow[csf('yarn_color')]]['rate']=$yarnRow[csf('dyeing_charge')];
				
				$yarn_dyeing_mst_idArr[$yarnRow[csf('id')]]=$yarnRow[csf('id')];
			}
			unset($resultyarnIssueData);


		
		// echo "<pre>";
		// print_r($yarnTrimsCostArray);
		// echo "</pre>";
	
		
		$prod_cond_for_in=where_con_using_array($all_prod_ids,0,"a.prod_id"); 
		if(count($all_prod_ids)==0)
		{
			$prod_cond_for_in="and a.prod_id in(0)";
		}
		
		 $sql_receive_for_issue="select c.booking_id,a.job_no,a.prod_id,max(a.transaction_date) as transaction_date,c.currency_id,c.receive_purpose,b.lot,b.color, 
		 sum(a.order_qnty) as qty, sum(a.order_amount) as amnt,
		 sum(CASE WHEN c.receive_purpose in(2,15,38)   THEN a.order_amount ELSE 0 END) AS order_amount_recv
		  from inv_transaction a,product_details_master b,inv_receive_master c where a.prod_id=b.id and c.id=a.mst_id and c.entry_form=1 and c.item_category=1 and a.transaction_type=1 and a.item_category=1 and a.status_active=1 and a.is_deleted=0 $prod_cond_for_in  group by c.booking_id,a.job_no,a.prod_id,c.currency_id,c.receive_purpose,b.lot,b.color";
		//echo $sql_receive_for_issue;
		$resultReceive_chek = sql_select($sql_receive_for_issue);
		
		foreach($resultReceive_chek as $row)
		{
			$yarn_receive_arr[$row[csf('prod_id')]][$row[csf('lot')]][$row[csf('color')]]['purpose']=$row[csf('receive_purpose')];
			//$receive_date_array[$row[csf('prod_id')]]['last_trans_date']=$row[csf('transaction_date')];
			$avg_rate=$row[csf('amnt')]/$row[csf('qty')];
			$receive_array[$row[csf('prod_id')]]=$avg_rate;
			if($row[csf('prod_id')]!="")
			{
				$prod_id_arr[$row[csf('prod_id')]]=$row[csf('prod_id')];
			}
		}
		//print_r($prod_id_arr);die;
		unset($resultReceive_chek);

		
		$po_cond_for_in=str_replace("b.order_id", "b.po_break_down_id", $poCon6);
		$sql_wo_aop="select a.id,a.booking_date,a.currency_id,a.item_category,a.booking_no,a.booking_type,a.is_short,b.po_break_down_id as po_id,
		(CASE WHEN a.item_category=12 and a.booking_type ='3'  THEN b.wo_qnty ELSE 0 END) AS wo_qnty,
		(CASE WHEN a.item_category=12 and a.booking_type ='3'  THEN b.amount ELSE 0 END) AS amount
		 from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no  and a.item_category in(2,3,12) and a.booking_type in(1,3,4)  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  $po_cond_for_in";
		$result_aop_rate=sql_select( $sql_wo_aop );
		foreach ($result_aop_rate as $row)
		{
			if($row[csf('item_category')]==12)
			{
				$wo_qnty=$row[csf('wo_qnty')];
				$amount=$row[csf('amount')];
				$avg_wo_aop_rate=$amount/$wo_qnty;
				$aop_prod_array[$row[csf('po_id')]]['aop_rate']=$avg_wo_aop_rate;
				$aop_prod_array[$row[csf('po_id')]]['currency_id']=$row[csf('currency_id')];
				$aop_prod_array[$row[csf('po_id')]]['booking_date']=$row[csf('booking_date')];
			}
			else
			{
				$booking_array[$row[csf('booking_no')]]['btype']=$row[csf('booking_type')];
				$booking_array[$row[csf('booking_no')]]['is_short']=$row[csf('is_short')];
				$booking_type_arr[$row[csf('id')]]['btype']=$row[csf('booking_type')];
				$booking_type_arr[$row[csf('id')]]['is_short']=$row[csf('is_short')];
			}
		}
		unset($result_aop_rate);

		$sql_yarn="select c.id,a.requisition_no,a.receive_basis,a.prod_id,(a.cons_amount) as cons_amount,c.issue_basis,c.issue_number,c.booking_no from inv_transaction a, order_wise_pro_details b, inv_issue_master c where  a.id=b.trans_id and  c.id=a.mst_id and c.entry_form in(3) and b.entry_form in(3)  and  a.transaction_type=2 $po_cond_for_in4";
		//echo $sql_yarn;
		$result_yarn=sql_select($sql_yarn);
		foreach($result_yarn as $invRow)
		{
			if($invRow[csf('issue_basis')]==1)// Booking
			{
				$yarn_booking_no_arr[$invRow[csf('id')]]=$invRow[csf('booking_no')];
			}
			else if($invRow[csf('issue_basis')]==3)// Requesition
			{
				$yarn_booking_no_arr[$invRow[csf('id')]]=$invRow[csf('requisition_no')];
			}
		}
		
		unset($result_yarn);

		$reqs_array = array();
		$po_cond_for_in15=where_con_using_array($order_id_arr,0,"b.po_id");
		$reqs_sql = sql_select("select a.knit_id, a.requisition_no as reqs_no, sum(a.yarn_qnty) as yarn_req_qnty from ppl_planning_entry_plan_dtls b,ppl_yarn_requisition_entry a where b.dtls_id=a.knit_id and b.status_active=1 and b.is_deleted=0 $po_cond_for_in15 group by a.knit_id, a.requisition_no");
		//echo "select a.knit_id, a.requisition_no as reqs_no, sum(a.yarn_qnty) as yarn_req_qnty from ppl_planning_entry_plan_dtls b,ppl_yarn_requisition_entry a where b.dtls_id=a.knit_id and b.status_active=1 and b.is_deleted=0 $po_cond_for_in15 group by a.knit_id, a.requisition_no";
		foreach ($reqs_sql as $row)
		{
			$reqs_array[$row[csf('reqs_no')]]['knit_id'] = $row[csf('knit_id')];
		}
		unset($reqs_sql);
		//echo "AAAA==";die;	
		$po_cond_for_in2=where_con_using_array($order_id_arr,0,"po_id");
		$plan_details_array = return_library_array("select dtls_id, booking_no as booking_no from ppl_planning_entry_plan_dtls where company_id=$cbo_company_name $po_cond_for_in2 group by dtls_id,booking_no", "dtls_id", "booking_no");
		//echo "select dtls_id, booking_no as booking_no from ppl_planning_entry_plan_dtls where company_id=$cbo_company_name $po_cond_for_in2 group by dtls_id,booking_no";
		//echo "BAAA==";die;	
		 $yarnTrimsData="select a.mst_id, a.transaction_type, a.receive_basis, a.item_category, a.transaction_date, a.cons_rate, b.entry_form, b.po_breakdown_id, b.prod_id, b.issue_purpose, b.quantity, b.returnable_qnty, c.lot, c.color
					from inv_transaction a, order_wise_pro_details b,product_details_master c where a.id=b.trans_id and c.id=b.prod_id and c.id=a.prod_id and a.item_category in(1) and a.transaction_type in(2,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and b.entry_form in(3)  $po_cond_for_in4 ";
		//echo $yarnTrimsData;
		
		$usd_id=2;
		 $queryText_exchange = "select con_date,currency,conversion_rate,company_id from currency_conversion_rate where currency=2 and status_active=1 and is_deleted=0 and  company_id=$cbo_company_name order by con_date desc"; 
		//echo $queryText; die;
		$exch_nameArray = sql_select($queryText_exchange, '', $new_conn);
		foreach($exch_nameArray as $row)
		{
			$exchange_rate_arr[$row[csf('company_id')]][$row[csf('currency')]]=$row[csf('conversion_rate')];
		}
		
		$yarnTrimsDataArray=sql_select($yarnTrimsData); $yarnTrimsCostArray=array();

		foreach($yarnTrimsDataArray as $invRow)
		{
			
		
			$yarn_issue_amt=$yarn_issue_amt_arr[$invRow[csf('prod_id')]]['amt'];
			$yarn_issue_qty=$yarn_issue_amt_arr[$invRow[csf('prod_id')]]['qty'];
			
			
			if($invRow[csf('receive_basis')]==1)//Booking Basis
			{
				$booking_no=$yarn_booking_no_arr[$invRow[csf('mst_id')]];
				if($invRow[csf('issue_purpose')]==2) //Yarn Dyeing purpose
				{
					$is_short=$yarn_dyeing_isshort_arr[$booking_no]['is_short'];
					$booking_type=1;
					//echo $is_short.'= '.$booking_type.', ';
				}
				else
				{
					$booking_type=$booking_array[$booking_no]['btype'];
					$is_short=$booking_array[$booking_no]['is_short'];
				}
			}
			else if($invRow[csf('receive_basis')]==3) //Requisition Basis
			{
				$booking_req_no=$yarn_booking_no_arr[$invRow[csf('mst_id')]];
				
				$prog_no=$reqs_array[$booking_req_no]['knit_id'];
				$booking_no=$plan_details_array[$prog_no];
				//echo $booking_no.'='.$prog_no.',';
				$booking_type=$booking_array[$booking_no]['btype'];
				$is_short=$booking_array[$booking_no]['is_short'];
			}
			//$transaction_date=$invRow[csf('transaction_date')];
			//$transaction_date='';
			//$transaction_date=$invRow[csf('transaction_date')];//$last_trans_date;
			//if($db_type==0) $conversion_date=change_date_format($transaction_date, "Y-m-d", "-",1);
			//else $conversion_date=change_date_format($transaction_date, "d-M-y", "-",1);
			//echo strtotime($conversion_date);die;
			 
				$exchange_rate=$exchange_rate_arr[$cbo_company_name][2];
				
				//$exchange_rate=set_conversion_rate($usd_id,$conversion_date,$cbo_company_name ); //For it Reprot not generate
			 
			 
			// echo $exchange_rate.'ds';
			$issue_rate=$yarn_issue_amt/$yarn_issue_qty;
			$avgrate=$issue_rate/$exchange_rate;


			// $iss_amnt=$invRow[csf('quantity')]*$avgrate;
			// 			//echo $iss_amnt.'m';
			// $retble_iss_amnt=$invRow[csf('returnable_qnty')]*$avgrate;
			// $yarnTrimsCostArray[$invRow[csf('po_breakdown_id')]]['grey_yarn_amt']+=$iss_amnt;


			$recv_purpose=$yarn_receive_arr[$invRow[csf('prod_id')]][$invRow[csf('lot')]][$invRow[csf('color')]]['purpose'];
			//echo "<pre>$booking_type=$is_short=$recv_purpose=".$invRow[csf('receive_basis')]."=".$invRow[csf('issue_purpose')]."</pre>";
			if($recv_purpose==16)//recv_purpose==16=Grey Yarn
			{
				if(($booking_type==1 || $booking_type==4) && $is_short==2 &&  $booking_type!='') //Main and Sample Faric Booking
				{
					if($invRow[csf('issue_purpose')]==1 || $invRow[csf('issue_purpose')]==2 || $invRow[csf('issue_purpose')]==4)//Knitting||Yarn Dyeing||Sample With Order
					{
						//echo $invRow[csf('mst_id')].'='.$recv_purpose.'='.$is_short.'='.$invRow[csf('quantity')].'-k<br>';
						$iss_amnt=$invRow[csf('quantity')]*$avgrate;
						//echo $iss_amnt.'m';
						$retble_iss_amnt=$invRow[csf('returnable_qnty')]*$avgrate;
						$yarnTrimsCostArray[$invRow[csf('po_breakdown_id')]]['grey_yarn_amt']+=$iss_amnt;
						
					}
				}
				
			}
			
			
		}
		unset($yarnTrimsDataArray);
		//print_r($yarnTrimsCostArray);
		
		//echo "CAA==";die;	

		  $yarnissue_retData="select d.booking_no,a.item_category,b.po_breakdown_id, b.prod_id, a.mst_id,a.receive_basis, b.issue_purpose,c.lot,c.color,
			(CASE WHEN a.transaction_type=4 and b.entry_form ='9' and b.trans_type=4 THEN a.transaction_date ELSE null END) AS transaction_date,
			sum(CASE WHEN a.transaction_type=4 and b.entry_form ='9' and b.trans_type=4 THEN b.quantity ELSE 0 END) AS yarn_iss_return_qty,
			sum(CASE WHEN a.transaction_type=4 and b.entry_form ='9' and b.trans_type=4 THEN a.order_amount ELSE 0 END) AS order_amount_ret,
			sum(CASE WHEN a.transaction_type=4 and b.entry_form ='9' and b.trans_type=4 THEN a.order_qnty ELSE 0 END) AS order_qnty_ret
			from inv_transaction a, order_wise_pro_details b,product_details_master c,inv_receive_master d where a.id=b.trans_id and c.id=b.prod_id and c.id=a.prod_id  and d.id=a.mst_id and a.item_category in(1) and a.transaction_type in(4) and b.entry_form in(9) and d.entry_form in(9) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1  $po_cond_for_in4  group by d.booking_no,b.po_breakdown_id, b.prod_id, a.item_category,a.transaction_date,a.receive_basis,a.mst_id,a.transaction_type,b.entry_form,b.trans_type,b.issue_purpose,c.lot,c.color";
			//echo $yarnissue_retData;
			$yarnissueretDataArray=sql_select($yarnissue_retData); $yarnissueRetCostArray=array();
			foreach($yarnissueretDataArray as $invRow)
			{
				$issue_purpose=$invRow[csf('issue_purpose')];
				$receive_basis=$invRow[csf('receive_basis')];
				//echo $receive_basis.'ff';
				if($receive_basis==1) //Booking Basis
				{
					$booking_no=$invRow[csf('booking_no')];
				}
				
				else if($receive_basis==3) //Requisition Basis
				{
					$booking_req_no=$invRow[csf('booking_no')];
					$prog_no=$reqs_array[$booking_req_no]['knit_id'];
					$booking_no=$plan_details_array[$prog_no];
					//echo $booking_req_no.'req';
				}
				$booking_type=$booking_array[$booking_no]['btype'];
				$is_short=$booking_array[$booking_no]['is_short'];
				//echo $booking_type.'='.$is_short.'='.$booking_no.'<br>';
				$returnable_ret_qnty=$invRow[csf('returnable_qnty')];
				//echo $invRow[csf('yarn_iss_return_qty')].'<br>';
				 $issue_ret_qty=$invRow[csf('yarn_iss_return_qty')];
				  $order_qnty_ret=$invRow[csf('order_qnty_ret')];
				  $order_amount_ret=$invRow[csf('order_amount_ret')];
				 $avg_rate=$order_amount_ret/$order_qnty_ret;
				//echo $avg_rate.'<br>';
				//$rate='';
				$transaction_date=$invRow[csf('transaction_date')];
				if($db_type==0) $conversion_date=change_date_format($transaction_date, "Y-m-d", "-",1);
				else $conversion_date=change_date_format($transaction_date, "d-M-y", "-",1);
				
				//$currency_rate=set_conversion_rate($usd_id,$conversion_date );
				$currency_rate=$exchange_rate_arr[$cbo_company_name][2];
				//echo $ret_avg_rate= $avg_rate/$conversion_date;
				$currency_id=$receive_curr_array[$invRow[csf('prod_id')]];
				//echo $currency_id.'dddddd';
				if($receive_array[$invRow[csf('prod_id')]]>0)
				{
					//echo $currency_id.'D';
					if($currency_id==1) $rate=$receive_array[$invRow[csf('prod_id')]]/$currency_rate;//Taka
					else $rate=$receive_array[$invRow[csf('prod_id')]];	
				}
				else $rate=$avg_rate_array[$invRow[csf('prod_id')]]/$currency_rate;
				
				$iss_ret_amnt=$issue_ret_qty*$rate;
				$retble_iss_ret_amnt=$returnable_ret_qnty*$rate;
				if(($booking_type==1 || $booking_type==4) &&  $is_short==2 &&  $booking_type!='') //Main and Sample Faric Booking
				{
					$recv_purpose=$yarn_receive_arr[$invRow[csf('prod_id')]][$invRow[csf('lot')]][$invRow[csf('color')]]['purpose'];
					if($issue_purpose==1 || $issue_purpose==4) //Knit || Sample With Order
					{
						if($recv_purpose==16) //Grey Yarn
						{
							$yarnissueRetCostArray[$invRow[csf('po_breakdown_id')]]['grey_yarn_ret_amt']+=$iss_ret_amnt;
						}
					}
					else if($issue_purpose==2) //Yarn Dyeing
					{
						if($recv_purpose==16) //Grey Yarn
						{
							$yarnissueRetCostArray[$invRow[csf('po_breakdown_id')]]['grey_yarn_ret_amt']+=$iss_ret_amnt;
						}
					}
				}
				
			}
			unset($yarnissueretDataArray);

		// echo "<pre>";
		// print_r($yarnissueRetCostArray);
		// echo "</pre>";
	    $grey_and_dyeing_cost_data=array();
	    foreach ($grey_and_dyeing_cost_result as $row) 
	    {
	    	$grey_and_dyeing_cost_data[$row[csf('order_id')]][$row[csf('process_id')]]+=$row[csf('amount')];
	    }
		
		//$lay_sql="SELECT b.order_id,sum(b.size_qty) as qnty, LISTAGG(a.id, ',') WITHIN GROUP (ORDER BY a.id) as id from ppl_cut_lay_dtls a,ppl_cut_lay_bundle b where a.id=b.dtls_id and a.status_active=1 and b.status_active=1 $poCon6 group by b.order_id ";
		$lay_sql="SELECT b.order_id,b.country_id,(b.size_qty) as qnty,a.id as id from ppl_cut_lay_dtls a,ppl_cut_lay_bundle b where a.id=b.dtls_id and a.status_active=1 and b.status_active=1 $poCon6 ";
     // echo $lay_sql;
        foreach(sql_select($lay_sql) as $rows)
		{
			$po_buyer=$po_buyer_array[$rows[csf('order_id')]]['buyer'];
			$job_no=$po_buyer_array[$rows[csf('order_id')]]['job_no'];
			
			$lay_cut_data[$job_no][$po_buyer] += $rows[csf('qnty')];
			$lay_cut_data_dtls[$rows[csf("order_id")]]['lay_cut_qty']+=$rows[csf("qnty")];
            $lay_cut_data_dtls[$rows[csf("order_id")]]['id'] .= $rows[csf("id")].",";
		}
	
	
		$dataArrayYarnIssuesQty = array(); $productsIdArray = array();
		 $sqlsTrans="select  b.po_breakdown_id, sum(b.quantity) as qnty from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(16,19,61) and a.item_category=13 and a.transaction_type in(2) and b.trans_type in(2) $trans_date $poCon7 group by  b.po_breakdown_id";
		$results_trans=sql_select( $sqlsTrans );
		foreach ($results_trans as $row)
		{
			$dataArrayYarnIssuesQty[$row[csf('po_breakdown_id')]]['qnty']+=$row[csf('qnty')];
			//$productsIdArray[$row[csf('po_breakdown_id')]].=$row[csf('prod_id')].',';
		}
		$issue_return=sql_select("select a.entry_form,a.po_breakdown_id as po_id,a.prod_id,a.quantity 
	from order_wise_pro_details a,inv_transaction b where a.trans_id=b.id and a.trans_type=4 and b.transaction_type in(4) and a.entry_form=9 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 $poCon4 ");
		foreach ($issue_return as $row)
		{
			$dataArrayYarnIssues_ret_arr[$row[csf('po_id')]]['quantity']+=$row[csf('quantity')];
		}
		//print_r($dataArrayYarnIssues_ret_arr);
	//echo "select a.entry_form,a.po_breakdown_id as po_id,a.prod_id,a.quantity 
	//from order_wise_pro_details a,inv_transaction b where a.trans_id=b.id and a.trans_type=4 and b.transaction_type in(4) and a.entry_form=9 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 $poCon4 ";
		
		//print_r( $dataArrayYarnIssuesQty);

	//$ff_qnty_array=return_library_array( "select sum(a.fin_fab_qnty) as fin_fab_qnty,a.po_break_down_id from wo_booking_dtls a where  a.booking_type=1 $poCon and a.status_active=1 and a.is_deleted=0 group by a.po_break_down_id", "po_break_down_id", "fin_fab_qnty");
	
	//$gf_qnty_array=return_library_array( "select sum(a.grey_fab_qnty) as grey_fab_qnty,a.po_break_down_id from wo_booking_dtls a  where  a.status_active=1 $poCon and a.is_deleted=0 group by a.po_break_down_id", "po_break_down_id", "grey_fab_qnty");//and a.booking_type=1
	
	$daying_qnty_array=return_library_array( "select sum(b.batch_qnty) as batch_qnty,b.po_id from pro_batch_create_mst a,pro_batch_create_dtls b, pro_fab_subprocess c where a.id=b.mst_id and a.id=c.batch_id and c.load_unload_id=2 and c.entry_form=35 and a.batch_against<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $poCon2  group by b.po_id", "po_id", "batch_qnty");
	
	
	
	$recvData=sql_select("select a.po_breakdown_id, sum(quantity) as grey_prod_qnty from order_wise_pro_details a, pro_grey_prod_entry_dtls b where a.dtls_id=b.id and a.prod_id=b.prod_id and a.entry_form =2 and a.is_deleted=0 and a.status_active=1 $poCon4 group by a.po_breakdown_id");
	foreach($recvData as $row)
	{
		$gp_qty_array[$row[csf('po_breakdown_id')]]=$row[csf('grey_prod_qnty')];
	}
	unset($recvData);
	
	
	/*$sql="select 
		po_break_down_id, 
		SUM(CASE WHEN production_type=1 THEN production_quantity END) as totalcut,
		SUM(CASE WHEN production_type=4 THEN production_quantity END) as totalinput,
		SUM(CASE WHEN production_type=5 THEN production_quantity ELSE 0 END) as totalsewing,
		SUM(CASE WHEN production_type=8 THEN production_quantity ELSE 0 END) as totalfinish,
		SUM(CASE WHEN production_type=2 THEN production_quantity ELSE 0 END) as totaembissue,
		SUM(CASE WHEN production_type=3 THEN production_quantity ELSE 0 END) as totaembrec
	from pro_garments_production_mst 
	WHERE status_active=1 $poCon3 and is_deleted=0
	group by po_break_down_id";*/
	 $sql="select 
		a.po_break_down_id, 
		SUM(CASE WHEN a.production_type=1 THEN b.production_qnty END) as totalcut,
		SUM(CASE WHEN a.production_type=4 THEN b.production_qnty END) as totalinput,
		SUM(CASE WHEN a.production_type=5 THEN b.production_qnty ELSE 0 END) as totalsewing,
		SUM(CASE WHEN a.production_type=8 THEN b.production_qnty ELSE 0 END) as totalfinish,
		SUM(CASE WHEN a.production_type=2 THEN b.production_qnty ELSE 0 END) as totaembissue,
		SUM(CASE WHEN a.production_type=3 THEN b.production_qnty ELSE 0 END) as totaembrec
	from pro_garments_production_mst a,pro_garments_production_dtls b
	WHERE  a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 $poCon group by po_break_down_id";
	$dataArray=sql_select($sql);
	foreach($dataArray as $row)
	{  
		$cut_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totalcut')];
		$sewing_in_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totalinput')];
		$sewing_out_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totalsewing')];
		$sewing_finish_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totalfinish')];
		$emb_issue_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totaembissue')];
		$emb_rec_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totaembrec')];
	
	}
	unset($dataArray);
	$ex_qnty_array=return_library_array( "select po_break_down_id, sum(ex_factory_qnty) as ex_factory_qnty from pro_ex_factory_mst where  status_active=1 $poCon3 and is_deleted=0 group by po_break_down_id", "po_break_down_id", "ex_factory_qnty");
	
	
	
		
	if(return_field_value("auto_update","variable_settings_production","company_name=$cbo_company_name and variable_list=15 and item_category_id=2 order by id","auto_update")==2)
	{
		$fabrics_avl_qnty_array=return_library_array( "select po_breakdown_id, sum(quantity) as quantity from order_wise_pro_details where entry_form in(17,37,68) $poCon5 and status_active=1 and is_deleted=0 group by po_breakdown_id", "po_breakdown_id", "quantity");
		
		
	}
	else
	{
		//$fabrics_avl_qnty_array=return_library_array( "select po_breakdown_id, sum(quantity) as quantity from order_wise_pro_details where entry_form=7 $poCon5 and status_active=1 and is_deleted=0 group by po_breakdown_id", "po_breakdown_id", "quantity");

		$fabrics_avl_qnty_array=return_library_array( "select po_breakdown_id, sum(quantity) as quantity from order_wise_pro_details where entry_form=7 $poCon5 and status_active=1 and is_deleted=0 group by po_breakdown_id
		union all
		select a.po_breakdown_id, sum(a.quantity) as quantity from order_wise_pro_details a, inv_transaction b where  a.trans_id = b.id and  a.entry_form in(37,17,68) $poCon5 and b.receive_basis in (1,2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by a.po_breakdown_id", "po_breakdown_id", "quantity");

	}


	$yarn_allocation_arr = return_library_array("select po_break_down_id, sum(qnty) as qnty from inv_material_allocation_dtls where item_category = 1 and  status_active = 1 and is_deleted=0 $poCon3 group by po_break_down_id", "po_break_down_id", "qnty");


	//$yarn_issue_array=return_library_array( "SELECT po_breakdown_id, sum(case when entry_form=3 then quantity else 0 end) - sum(case when entry_form=9 then quantity else 0 end) as quantity from order_wise_pro_details where entry_form in(3,9) $poCon5 and status_active=1 and is_deleted=0 and is_sales !=1 group by po_breakdown_id", "po_breakdown_id", "quantity"); 

	$yarn_issue_array=return_library_array( "SELECT po_breakdown_id, sum(case when entry_form=3 and issue_purpose=1 and trans_type=2 then quantity else 0 end) as quantity from order_wise_pro_details where entry_form in(3) $poCon5 and status_active=1 and is_deleted=0 and is_sales !=1 group by po_breakdown_id", "po_breakdown_id", "quantity"); 
	
	//echo "SELECT po_breakdown_id, sum(case when entry_form=3 then quantity else 0 end) - sum(case when entry_form=9 then quantity else 0 end) as quantity from order_wise_pro_details where entry_form in(3,9) $poCon5 and status_active=1 and is_deleted=0 and is_sales !=1 group by po_breakdown_id";
	//echo "select po_breakdown_id, sum(quantity) as quantity from order_wise_pro_details where entry_form=3 $poCon5 and status_active=1 and is_deleted=0 and is_sales !=1 group by po_breakdown_id";

		//	echo "A==";die;		
	//Summary Data create......................................start;
	
	foreach($order_data_arr as $rows)
	{
		
		$issQtyN=$dataArrayYarnIssuesQty[$rows[csf('id')]]['qnty'];
		//echo $rows[csf('set_smv')];
		$buyer_summary_arr[$rows[csf('buyer_name')]]['buyer_name']=$rows[csf('buyer_name')];
		$buyer_summary_arr[$rows[csf('buyer_name')]]['po_quantity']+=$rows[csf('po_quantity')];
		//$buyer_summary_arr[$rows[csf('buyer_name')]]['po_val']+=($rows[csf('po_quantity')]*($rows[csf('unit_price')]/$rows[csf('total_set_qnty')]));
		$buyer_summary_arr[$rows[csf('buyer_name')]]['po_val']+=$rows[csf('po_total_price')];//new
		
		//$lay_cut_data_dtls_countryArr[$rows[csf("order_id")]][$rows[csf("country_id")]]['lay_cut_qty']
		
		//echo $cut_qnty_array[$rows[csf('id')]].', ';
		//$buyer_summary_arr[$rows[csf('buyer_name')]]['cutting_qty']+=$rows[csf('cutting_qty')];
		$buyer_summary_arr[$rows[csf('buyer_name')]]['cutting_qty']+=$cut_qnty_array[$rows[csf('id')]];
		 
		$buyer_summary_arr[$rows[csf('buyer_name')]]['lay_cut_qty']+=$lay_cut_data_dtls[$rows[csf("id")]]['lay_cut_qty'];
		 
		//$lay_cut_data[$rows[csf('job_no')]][$rows[csf('buyer_name')]];
		
		
		
		$buyer_summary_arr[$rows[csf('buyer_name')]]['sewing_in']+=$sewing_in_qnty_array[$rows[csf('id')]];
		$buyer_summary_arr[$rows[csf('buyer_name')]]['sewing_out']+=$sewing_out_qnty_array[$rows[csf('id')]];
		$buyer_summary_arr[$rows[csf('buyer_name')]]['finish_qty']+=$sewing_finish_qnty_array[$rows[csf('id')]];
		$buyer_summary_arr[$rows[csf('buyer_name')]]['exfactory_qty']+=$ex_qnty_array[$rows[csf('id')]];
		$buyer_summary_arr[$rows[csf('buyer_name')]]['emb_rec_qty']+=$emb_rec_qnty_array[$rows[csf('id')]];
		$buyer_summary_arr[$rows[csf('buyer_name')]]['grey_req']+=round($gf_qnty_array[$rows[csf('id')]]);
		$buyer_summary_arr[$rows[csf('buyer_name')]]['yarn_alloc']+=round($yarn_allocation_arr[$rows[csf('id')]]); 
		$buyer_summary_arr[$rows[csf('buyer_name')]]['yarn_issue']+=round($yarn_issue_array[$rows[csf('id')]]-$dataArrayYarnIssues_ret_arr[$rows[csf('id')]]['quantity']); 
		$buyer_summary_arr[$rows[csf('buyer_name')]]['grey_prod']+=$gp_qty_array[$rows[csf('id')]];
		$buyer_summary_arr[$rows[csf('buyer_name')]]['grey_to_dye']+=round($issQtyN);
		//$buyer_summary_arr[$rows[csf('buyer_name')]]['grey_prod']+=round($dataArrayYarnIssue[$rows[csf('id')]]);
		$buyer_summary_arr[$rows[csf('buyer_name')]]['dyeing']+=round($daying_qnty_array[$rows[csf('id')]]);
		$buyer_summary_arr[$rows[csf('buyer_name')]]['avg_smv']+=$rows[csf('set_smv')]*$rows[csf('po_qty')];
		$buyer_summary_arr[$rows[csf('buyer_name')]]['freq']+=round($ff_qnty_array[$rows[csf('id')]]);
		$buyer_summary_arr[$rows[csf('buyer_name')]]['favl']+=round($fabrics_avl_qnty_array[$rows[csf('id')]]);
		$buyer_summary_arr[$rows[csf('buyer_name')]]['emb_issue']+=$emb_issue_qnty_array[$rows[csf('id')]];
		//$gp_per=round(($gp_qty_array[$row[csf('id')]]/$gf_qnty_array[$row[csf('id')]])*100);
		
		$buyer_tot_arr['po_quantity']+=$rows[csf('po_quantity')];
		
		//$buyer_tot_arr['po_val']+=($rows[csf('po_quantity')]*($rows[csf('unit_price')]/$rows[csf('total_set_qnty')]));
		$buyer_tot_arr['po_val']+=$rows[csf('po_total_price')];//new
		
		
		
		//$buyer_tot_arr['plan_cut']+=$rows[csf('plan_cut')];
		
		
		$buyer_tot_arr['cutting_qty']+=$cut_qnty_array[$rows[csf('id')]];
		if($lay_cut_data[$rows[csf('job_no')]][$rows[csf('buyer_name')]]>0)
		{
		$buyer_tot_arr['lay_cut_qty']+=$lay_cut_data[$rows[csf('job_no')]][$rows[csf('buyer_name')]];
		}
		
		$buyer_tot_arr['sewing_in']+=$sewing_in_qnty_array[$rows[csf('id')]];
		$buyer_tot_arr['sewing_out']+=$sewing_out_qnty_array[$rows[csf('id')]];
		$buyer_tot_arr['finish_qty']+=$sewing_finish_qnty_array[$rows[csf('id')]];
		$buyer_tot_arr['exfactory_qty']+=$ex_qnty_array[$rows[csf('id')]];
		$buyer_tot_arr['emb_rec_qty']+=$emb_rec_qnty_array[$rows[csf('id')]];
		
		$buyer_tot_arr['grey_req']+=round($gf_qnty_array[$rows[csf('id')]]);
		$buyer_tot_arr['yarn_alloc']+=round($yarn_allocation_arr[$rows[csf('id')]]);
		$buyer_tot_arr['yarn_issue']+=round($yarn_issue_array[$rows[csf('id')]]-$dataArrayYarnIssues_ret_arr[$rows[csf('id')]]['quantity']);
		//$buyer_tot_arr['grey_prod']+=round($dataArrayYarnIssue[$rows[csf('id')]]);
		$buyer_tot_arr['grey_prod']+=$gp_qty_array[$rows[csf('id')]];
		$buyer_tot_arr['grey_to_dye']+=round($issQtyN);
		$buyer_tot_arr['dyeing']+=round($daying_qnty_array[$rows[csf('id')]]);
		$buyer_tot_arr['freq']+=round($ff_qnty_array[$rows[csf('id')]]);
		$buyer_tot_arr['favl']+=round($fabrics_avl_qnty_array[$rows[csf('id')]]);
		$buyer_tot_arr['emb_issue']+=$emb_issue_qnty_array[$rows[csf('id')]];
	}
	
		

	ob_start();	
	?>
    
    <fieldset style="width:1400px;">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr><td colspan="12" align="center" style="font-size:22px;">Buyer Summary</td></tr>
        </table>
        <table width="100%" border="1" rules="all"  class="rpt_table"> 
            <thead>
                <tr style="font-size:12px"> 
                    <th width="35">Sl</th>
                    <th width="250">Buyer</th>
                    <th width="80">QTY</th>
                    <th width="80">Order Value</th>
                    <th width="80">Avg. SMV</th>
                    <th width="80">Grey Required</th>
                    <th width="80">Yarn Allocation</th>
                    <th width="80">Yarn Issue</th>
                    <th width="80">Grey Prod.</th>
                    <th width="80">Grey to Dye</th>
                    <th width="80">G2D (%)</th>
                    <th width="80">Dyeing</th>
                    <th width="80">Fabrics Required</th> 
                    <th width="80">Fabrics Available</th>
                    <th width="80">Balance</th>
                    <th width="100">Cut Lay Qty</th>
                    <th width="80">Cut Lay %</th>
                    <th width="80">Cut Qc Qty</th>
                    <th width="80">Qc %</th>
                    <th width="80">Emb. Issue</th>
                    <th width="80">Emb. Receive</th>
                    <th width="80">Sewing Input</th>
                    <th width="80">Sewing Output</th>
                    <th width="80">Finish Qty</th>
                    <th width="80">Finish %</th>
                    <th width="80">Ex-Factory</th>
                    <th width="80">Ex-Fac %</th>
               </tr>
            </thead>
            <tbody>
                <? 
				$tot_cut_per="";
				$tot_lay_cut_per="";
				$i=1;$balance_qty=$total_lay_cut_qty=0;
				foreach($buyer_summary_arr as $rows){
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					$finish_par=($rows['finish_qty']/$rows['po_quantity'])*100;
					$exfactory_par=($rows['exfactory_qty']/$rows['po_quantity'])*100;


					
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('str_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="str_<? echo $i; ?>" style="font-size:12px; cursor:pointer;" valign="middle">
                    <td><? echo $i;?></td>	
                    <td><p><? echo $buyer_library[$rows['buyer_name']];?></td>	
                    <td align="right"><? echo omitZero(number_format($rows['po_quantity'],0));?></td>	
                    <td align="right"><? echo omitZero(number_format($rows['po_val'],0));?></td>
                     
                     <td align="right" title="<? echo $rows['avg_smv'];?>">
					 <? 
					 $avg_smv=$rows['avg_smv'];
					 $po_qty=$rows['po_quantity'];
					 $smv= ($avg_smv/$po_qty);
					// echo gettype($po_qty);
					 echo omitZero(number_format($smv,2)); 
					 $cut_per=($rows['cutting_qty']/$rows['po_quantity'])*100;
					 $tot_cut_per+=$cut_per;

					 $lay_cut_per=($rows['lay_cut_qty']/$rows['po_quantity'])*100;
					 $tot_lay_cut_per+=$lay_cut_per;


					 //$cut_per='=='.$rows['cutting_qty'].'/'.$rows['po_quantity'];
					 //echo $issQtyN; //
					 ?>
                     </td>
                     <td align="right"><? echo omitZero(number_format($rows['grey_req'],0));?></td>
                     <td align="right"><? echo omitZero(number_format($rows['yarn_alloc'],0));?></td>
                     <td align="right"><? echo omitZero(number_format($rows['yarn_issue'],0));?></td>
                     <td align="right"><? echo number_format($rows['grey_prod'],0);?></td>
                     <td align="right"><? echo omitZero(number_format($rows['grey_to_dye'],0));?></td>
                     <td align="right"><? echo omitZero(number_format(($rows['grey_to_dye']/$rows['grey_req']*100),0));?></td>
                     <td align="right"><? echo omitZero(number_format($rows['dyeing']));?></td>
                     <td align="right"><? echo omitZero(number_format($rows['freq'],0));?></td>
                     <td align="right"><? echo omitZero(number_format($rows['favl'],0));?></td>
                     <td align="right"><? echo omitZero(number_format($rows['freq']-$rows['favl'],0));?></td>
                     <td align="right"><? echo omitZero(number_format($rows['lay_cut_qty']));?></td>	
                    <td align="right"><? echo omitZero(number_format($lay_cut_per,2));?></td>
                    <td align="right"><? echo omitZero(number_format($rows['cutting_qty']));?></td>	
                    <td align="right"><? echo omitZero(number_format($cut_per,2));?></td>
                    <td align="right"><? echo omitZero(number_format($rows['emb_issue'],0));?></td>		
                    <td align="right"><? echo omitZero(number_format($rows['emb_rec_qty'],0));?></td>	
                    <td align="right"><? echo omitZero(number_format($rows['sewing_in'],0));?></td>	
                    <td align="right"><? echo omitZero(number_format($rows['sewing_out'],0));?></td>	
                    <td align="right"><? echo omitZero(number_format($rows['finish_qty'],0));?></td>	
                    <td align="right"><? echo omitZero(number_format($finish_par,2));?></td>	
                    <td align="right"><? echo omitZero(number_format($rows['exfactory_qty'],0));?></td>	
                    <td align="right"><? echo omitZero(number_format($exfactory_par,2));?></td>	
                </tr>
               <? 
			   $i++;
			   $balance_qty+=$rows['freq']-$rows['favl']; 
			   $total_lay_cut_qty+=$rows['lay_cut_qty'];
			   } 
			    $tot_lay_cut_per=0;  $tot_cut_per=0;
			  $tot_lay_cut_per= ($total_lay_cut_qty/$buyer_tot_arr['po_quantity'])*100;
			   $tot_cut_per= ($buyer_tot_arr['cutting_qty']/$buyer_tot_arr['po_quantity'])*100;
			 // echo $total_lay_cut_qty.'DD'.$buyer_tot_arr['po_quantity'];
			   ?>
            </tbody>
            <tfoot>
                <th colspan="2" align="right">Total </th>	
                <th align="right"><? echo omitZero(number_format($buyer_tot_arr['po_quantity'],0));?></th>	
                <th align="right"><? echo omitZero(number_format($buyer_tot_arr['po_val']));?></th>
                <th align="right"><? //echo round($buyer_tot_arr['grey_req']);?></th>	
                <th align="right"><? echo omitZero(number_format($buyer_tot_arr['grey_req'],0));?></th>	
                <th align="right"><? echo omitZero(number_format($buyer_tot_arr['yarn_alloc'],0));?></th>	
                <th align="right"><? echo omitZero(number_format($buyer_tot_arr['yarn_issue'],0));?></th>	
                <th align="right"><? echo omitZero(number_format($buyer_tot_arr['grey_prod'],0));?></th>	
                <th align="right"><? echo omitZero(number_format($buyer_tot_arr['grey_to_dye'],0));?></th>	
                <th align="right"><? //echo round($buyer_tot_arr['dyeing']);?></th>
                <th align="right"><? echo omitZero(number_format($buyer_tot_arr['dyeing'],0));?></th>	
                <th align="right"><? echo omitZero(number_format($buyer_tot_arr['freq'],0));?></th>	
                <th align="right"><? echo omitZero(number_format($buyer_tot_arr['favl'],0));?></th>	
                <th align="right"><? echo omitZero(number_format($balance_qty,0));?></th>
                <th align="right"><? echo omitZero(number_format($total_lay_cut_qty,0));?></th>	
                <th align="right"><?  echo omitZero(number_format($tot_lay_cut_per,2));?></th>	
                <th align="right"><? echo omitZero(number_format($buyer_tot_arr['cutting_qty'],0));?></th>	
                <th align="right"><? echo omitZero(number_format($tot_cut_per,2));?></th>	
                <th align="right"><? echo omitZero(number_format($buyer_tot_arr['emb_issue'],0));?></th>	
                <th align="right"><? echo omitZero(number_format($buyer_tot_arr['emb_rec_qty'],0));?></th>	
                <th align="right"><? echo omitZero(number_format($buyer_tot_arr['sewing_in'],0));?></th>	
                <th align="right"><? echo omitZero(number_format($buyer_tot_arr['sewing_out'],0));?></th>	
                <th align="right"><? echo omitZero(number_format($buyer_tot_arr['finish_qty'],0));?></th>	
                <th align="right"><? echo omitZero(number_format(($buyer_tot_arr['finish_qty']/$buyer_tot_arr['po_quantity'])*100,0));?></th>	
                <th align="right"><? echo omitZero(number_format($buyer_tot_arr['exfactory_qty']));?></th>	
                <th align="right"><? echo omitZero(number_format(($buyer_tot_arr['exfactory_qty']/$buyer_tot_arr['po_quantity'])*100,0));?></th>	
            </tfoot>
            
        </table>
    </fieldset>
    <br />
    
    <? 
    	$new_column=60+60+60+60+60;
    	$main_width=2510;

    ?>
   

	<div style="width:<?=$main_width+$new_column+20;?>px" align="left">	
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr><td colspan="30" align="center" style="font-size:22px;">Order Follow-Up Report</td></tr>
            <tr>
                <td colspan="30" align="center" style="font-size:16px; font-weight:bold;">
					<? echo $company_lib[$cbo_company_name];?>
                </td>
            </tr>
            <? if($txt_date_from!='' && $txt_date_to!=''){?>
            <tr>
             	<td colspan="30" align="center" style="font-size:14px;">
                    From: <? echo change_date_format($txt_date_from);?> 
                    To: <? echo change_date_format($txt_date_to);?>
                </td>
            </tr>
            <? }?>
        </table>
        <table width="<?=$main_width+$new_column+20;?>" border="1" rules="all"  class="rpt_table"> 
            <thead>
                <tr style="font-size:12px"> 
                    <th width="35">Sl</th>	
                    <th width="100" style="word-break: break-all;">Buyer</th> 
                    <th width="70" style="word-break: break-all;">File No</th>
                    <th width="70" style="word-break: break-all;">Int. Ref.</th>
                    <th width="80" style="word-break: break-all;">Job No</th>
                    <th width="100" style="word-break: break-all;">Order No</th>
                    <th width="100" style="word-break: break-all;">Booking No</th>
                    <th width="100" style="word-break: break-all;">Style No</th>
                    <th width="130" style="word-break: break-all;">Item Name</th>
                    <th width="60" style="word-break: break-all;">Picture</th>	
                    <th width="100" style="word-break: break-all;">QTY</th>	
					<th width="80" style="word-break: break-all;">FOB/Pcs Price</th>	
					<th width="80" style="word-break: break-all;">value</th>
                    <th width="80" style="word-break: break-all;"><?=$date_heading;?></th>
                    <th width="60" style="word-break: break-all;">SMV</th>	
                    <th width="60" style="word-break: break-all;">Grey Req</th>
                    <th width="60" style="word-break: break-all;">Yarn Alloc</th>
                    <th width="60" style="word-break: break-all;">Yarn Issue</th>
                    <th width="60" style="word-break: break-all;">Yarn Value</th>
                    <th width="60" style="word-break: break-all;">Grey Prod.</th>	
                    <th width="60" style="word-break: break-all;">Yarn <br>Budget<br>Value</th>	
                    <th width="60" style="word-break: break-all;">Grey Cost</th>	
                    <th width="60" style="word-break: break-all;">Grey to Dye</th>	
                    <th width="60" style="word-break: break-all;">G2D (%)</th>	
                    <th width="60" style="word-break: break-all;">Dyeing</th>	
                    <th width="60" style="word-break: break-all;">Dying Cost</th>	
                    <th width="60" style="word-break: break-all;">Total Fabric Cost</th>	
                    <th width="60" style="word-break: break-all;">Fabrics Req.</th>	
                    <th width="60" style="word-break: break-all;" title="95% GREEN 75% - 95% YELLOW">Fabrics Avl.</th>	
                    <th width="60" style="word-break: break-all;">Balance</th>	
                    <th width="60" style="word-break: break-all;">Cut Lay Qty</th>	
                    <th width="60" style="word-break: break-all;">Cut Lay%</th>
                    <th width="60" style="word-break: break-all;">Cut Qc Qty</th>	
                    <th width="60" style="word-break: break-all;">Qc (%)</th>	
                    <th width="50" style="word-break: break-all;">Emb. Issue</th>	
                    <th width="60" style="word-break: break-all;">Emb. Receive</th>	
                    <th width="60" style="word-break: break-all;">Input</th>	
                    <th width="60" style="word-break: break-all;">Output</th>
                    <th width="60" style="word-break: break-all;" title="95% GREEN 75% - 95% YELLOW">Finish</th>
                    <th style="width:50px;word-break: break-all;">Ex- Factory</th>	
                    <th width="" style="word-break: break-all;">Remarks</th>
                </tr>                            	
            </thead>
        </table>
        <div style="width:<?=$main_width+$new_column+20;?>px; max-height:350px; overflow-y:scroll" id="report_div" > 
            <table id="table_body" width="<?=$main_width+$new_column;?>" border="1"  class="rpt_table" rules="all">
             
				<?php
                $i=1;
                $tot_grey_cost_value=$tot_dying_cost=$total_fabric_cost=0;
                foreach($order_data_arr as $row)
                {
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					$gp_per=round(($dataArrayYarnIssuesQty[$row[csf('id')]]['qnty']/$gf_qnty_array[$row[csf('id')]])*100);
					
					//$gp_per=round(($gp_qty_array[$row[csf('id')]]/$gf_qnty_array[$row[csf('id')]])*100);
					
					$cut_per=($cut_qnty_array[$row[csf('id')]]/$row[csf('po_quantity')])*100;
					//echo (($cut_qnty_array[$row[csf('id')]]/$row[csf('po_quantity')])*100).',dd';
					 
					$lay_cut_per=round(($lay_cut_data_dtls[$row[csf("id")]]['lay_cut_qty']/$row[csf('po_quantity')])*100);
                    
					$finFactoryPer=round(($sewing_finish_qnty_array[$row[csf('id')]]/$row[csf('po_quantity')])*100);
					if($finFactoryPer>94){$finfBgColor=' bgcolor="#00CC00" ';}
					elseif($finFactoryPer>74 && $finFactoryPer< 95){$finfBgColor='bgcolor="#FFCC33"';}
					else{$finfBgColor='';}
					
					$fabAvlPer=round(($fabrics_avl_qnty_array[$row[csf('id')]]/$ff_qnty_array[$row[csf('id')]])*100);
					if($fabAvlPer>94){$fabAvlBgColor=' bgcolor="#00CC00" ';}
					elseif($fabAvlPer>74 && $fabAvlPer< 95){$fabAvlBgColor='bgcolor="#FFCC33"';}
					else{$fabAvlBgColor='';}
					
					/*$issQty="";
					$dataProds=rtrim($productsIdArray[$row[csf('id')]],',');
					$dataProds=array_unique(explode(",",$dataProds));*/
					//print_r($dataProd);
					$itemText=array();
					$gmts_items=explode(',',$row[csf('gmts_item_id')]);
					foreach($gmts_items as $gmts_id)
					{
						$itemText[$gmts_id]=$items_library[$gmts_id];
					}
					$yarn_cost_actual=0;
					$yarn_cost_actual=$yarnTrimsCostArray[$row[csf('id')]]['grey_yarn_amt']-$yarnissueRetCostArray[$row[csf('id')]]['grey_yarn_ret_amt'];
					 //var_dump($itemText);die;
					 $issQty=$dataArrayYarnIssuesQty[$row[csf('id')]]['qnty'];   

					 $tot_yarn_value+=fn_number_format($yarn_cost_actual,2,".","");
					 $yarn_budget_value+=fn_number_format($po_wise_yarn_budget_data[$row[csf('id')]],2,".","");
            ?>	
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="font-size:12px; cursor:pointer;" valign="middle">
                    <td width="35" style="word-break: break-all;"><? echo $i;?></td>	
                    <td width="100" style="word-break: break-all;"><p><? echo $buyer_library[$row[csf('buyer_name')]];?></p></td>
                    <td width="70" style="word-break: break-all;"><p><? echo $row[csf('file_no')];?></p></td>
                    <td width="70" style="word-break: break-all;"><p><? echo $row[csf('grouping')];?></p></td> 
                    <td width="80" style="word-break: break-all;"><p><? echo $row[csf('job_no_prefix_num')];?></p></td>	
                    <td width="100" style="width:100px; word-wrap:break-word;"><p><? echo $row[csf('po_number')];?></p></td>	
                    <td width="100" style="word-break: break-all;"><p>
					<?
						$html_book=array();
						foreach($booking_no_array[$row[csf('id')]] as $booking_no=>$booking_num){
						//$html[] ="<a href='$booking_no'>$booking_num</a>";	
						$html_book[] =$booking_num;	
						}
						echo implode(',',$html_book);
					?>
                    </p></td>	
                    <td width="100" style="width:100px; word-wrap:break-word;"><p><? echo $row[csf('style_ref_no')];?></p></td>
                    <td width="130" style="width:130px; word-wrap:break-word;"><p><? echo implode(',',$itemText);?></p></td>
                    <td width="60" style="width:60px; word-wrap:break-word;" onClick="openmypage_image('requires/order_follo_up_report_controller.php?action=show_image&job_no=<? echo $row[csf("job_no")] ?>','Image View')" align="center"><img src="../../../<? echo $imge_arr[$row[csf('job_no')]];?>" height='25' width='25' /></td>	
                    <td width="100" style="word-break: break-all;" align="right"><? echo omitZero(number_format($row[csf('po_quantity')],0,'.',''));
					?></td>
					<td width="80" style="word-break: break-all;" align="right"><? echo number_format($row[csf('po_total_price')]/$row[csf('po_quantity')],2);?></td>
					<td width="80" style="word-break: break-all;" align="right"><? echo number_format($row[csf('po_total_price')],2);?></td>
                    <td width="80" style="word-break: break-all;" align="center"><? $originalDate=$row[csf('pub_shipment_date')]; echo date("d-M-Y", strtotime($originalDate));?></td>
                    <td width="60" style="word-break: break-all;" align="right"><? echo omitZero(number_format($row[csf('set_smv')],2));?></td>	
                    <td width="60" style="word-break: break-all;" align="right"><? echo omitZero(number_format($gf_qnty_array[$row[csf('id')]],0));?></td>	
                    <td width="60" style="word-break: break-all;" align="right"><? echo omitZero(number_format($yarn_allocation_arr[$row[csf('id')]],0));
					$issue_ret_qty=$dataArrayYarnIssues_ret_arr[$row[csf('id')]]['quantity'];
					?></td>	 
                    <td width="60" style="word-break: break-all;" title="<? echo 'Issue Qty-Issue Ret Qty('.$issue_ret_qty.')';?>" align="right">
                    	<? echo omitZero(number_format(($yarn_issue_array[$row[csf('id')]]-$issue_ret_qty),0));?>							
					</td>	
					<td width="60" style="word-break: break-all;"  align="right">
						
					 	<? echo number_format($yarn_cost_actual,2);?>
					 		
					</td>
                    <td width="60" style="word-break: break-all;" align="right"><? echo omitZero(number_format($gp_qty_array[$row[csf('id')]],0));?></td>	
                    <td width="60" style="word-break: break-all;" align="right"><?=fn_number_format($po_wise_yarn_budget_data[$row[csf('id')]],2)?></td>	
                    <td width="60" style="word-break: break-all;" align="right"><?=fn_number_format($grey_and_dyeing_cost_data[$row[csf('id')]][2],2)?></td>	
                    <td width="60" style="word-break: break-all;" align="right"><? echo omitZero(number_format($issQty,0));?></td>	
                    <td width="60" style="word-break: break-all;" align="right"><? echo omitZero($gp_per);?></td>	
                    <td width="60" style="word-break: break-all;" align="right"><? echo omitZero(number_format($daying_qnty_array[$row[csf('id')]],0));?></td>	
                    <td width="60" style="word-break: break-all;" align="right"><?=fn_number_format($grey_and_dyeing_cost_data[$row[csf('id')]][4],2)?></td>
                    <td width="60" style="word-break: break-all;" align="right"><?=fn_number_format($grey_and_dyeing_cost_data[$row[csf('id')]][2]+$grey_and_dyeing_cost_data[$row[csf('id')]][4]+$yarn_cost_actual,2)?></td>
                    <td width="60" style="word-break: break-all;" align="right"><? echo omitZero(number_format($ff_qnty_array[$row[csf('id')]],0));?></td>	
                    <td width="60" style="word-break: break-all;" align="right" <? echo $fabAvlBgColor;?> title="<? echo $fabAvlPer;?>%">
						<? echo omitZero(number_format($fabrics_avl_qnty_array[$row[csf('id')]]),0);?>
                    </td>	
                    <td width="60" style="word-break: break-all;" align="right"><? echo $blance=omitZero(number_format($ff_qnty_array[$row[csf('id')]]-$fabrics_avl_qnty_array[$row[csf('id')]],0));?></td>
                    <?
					 
					 $lay_cut_data_dtls_id = explode(',', rtrim($lay_cut_data_dtls[$row[csf("id")]]['id'], ','));
					 $lay_cut_qty=$lay_cut_data_dtls[$row[csf("id")]]['lay_cut_qty'];

                    $lay_cut_data_dtls_id = array_unique($lay_cut_data_dtls_id);
                    ?>
                    <td width="60" style="word-break: break-all;" align="right"><a href="javascript:void(0)" onclick="qty_details('load_cut_lay_data', '<?=implode(',', $lay_cut_data_dtls_id)?>', <?=$row[csf("id")]?>)"><? echo  omitZero(number_format($lay_cut_qty),0);//omitZero(number_format($lay_cut_data[$row[csf('job_no')]][$row[csf('buyer_name')]],0));?></a></td>
                    <td width="60" style="word-break: break-all;" align="right"><? echo omitZero(number_format($lay_cut_per,2));?></td>

                    <td width="60" style="word-break: break-all;" align="right"><a href="javascript:void(0)" onclick="qty_details('load_cut_qc_data', '', <?=$row[csf("id")]?>)"><? echo omitZero(number_format($cut_qnty_array[$row[csf('id')]],0));?></a></td>
                    <td width="60" style="word-break: break-all;" align="right"><? echo omitZero(number_format($cut_per,2));?></td>	
                    <td width="50" style="word-break: break-all;" align="right"><a href="javascript:void(0)" onclick="qty_details('load_emble_issue_data', '', <?=$row[csf("id")]?>)"><? echo omitZero(number_format($emb_issue_qnty_array[$row[csf('id')]],0));?></a></td>
                    <td width="60" style="word-break: break-all;" align="right"><a href="javascript:void(0)" onclick="qty_details('load_emble_recv_data', '', <?=$row[csf("id")]?>)"><? echo omitZero(number_format($emb_rec_qnty_array[$row[csf('id')]],0));?></a></td>
                    <td width="60" style="word-break: break-all;" align="right"><a href="javascript:void(0)" onclick="qty_details('load_sewing_in_data', '', <?=$row[csf("id")]?>)"><? echo omitZero(number_format($sewing_in_qnty_array[$row[csf('id')]],0));?></a></td>
                    <td width="60" style="word-break: break-all;" align="right"><a href="javascript:void(0)" onclick="qty_details('load_sewing_out_data', '', <?=$row[csf("id")]?>)"><? echo omitZero(number_format($sewing_out_qnty_array[$row[csf('id')]],0));?></a></td>
                    <td width="60" style="word-break: break-all;" align="right" <? echo $finfBgColor;?> title="<? echo $finFactoryPer;?>%">
                        <a href="javascript:void(0)" onclick="qty_details('load_finish_data', '', <?=$row[csf("id")]?>)"><? echo omitZero(number_format($sewing_finish_qnty_array[$row[csf('id')]],0));?></a>
                    </td>
                    <td width="50" style="word-break: break-all;" align="right"><a href="javascript:void(0)" onclick="qty_details('load_ex_factory_data', '', <?=$row[csf("id")]?>)"><? echo omitZero(number_format($ex_qnty_array[$row[csf('id')]],0));?></a></td>
                  
                    <td width="" style="word-break: break-all;"><p><? echo $row[csf('details_remarks')];?></p></td>
                </tr>
            <?
            	
				$tot_po_quantity +=$row[csf('po_quantity')];
				$tot_po_value+=$row[csf('po_total_price')];
				$tot_set_smv+=$row[csf('set_smv')];
				$tot_gf_qnty+=round($gf_qnty_array[$row[csf('id')]]);
				$tot_yarn_alloc_qnty += $yarn_allocation_arr[$row[csf('id')]];
				$tot_yarn_issue_qnty += $yarn_issue_array[$row[csf('id')]]-$issue_ret_qty;
				$tot_grey_to_dye+=$issQty;
				$tot_gp_qty+=$gp_qty_array[$row[csf('id')]];
				$tot_daying_qnty+=$daying_qnty_array[$row[csf('id')]];
				//$tot_blance+=round($blance);
				$tot_blance+=round($ff_qnty_array[$row[csf('id')]]-$fabrics_avl_qnty_array[$row[csf('id')]]);
				$tot_fabrics_avl_qnty+=$fabrics_avl_qnty_array[$row[csf('id')]];
				$tot_ff_qnty+=round($ff_qnty_array[$row[csf('id')]]);
				$tot_cut_qnty+=$cut_qnty_array[$row[csf('id')]];
				$tot_lay_cut_qnty+=$lay_cut_qty;//$lay_cut_data[$row[csf('job_no')]][$row[csf('buyer_name')]];
				$tot_emb_issue_qnty+=$emb_issue_qnty_array[$row[csf('id')]];
				$tot_emb_rec_qnty+=$emb_rec_qnty_array[$row[csf('id')]];
				$tot_sewing_in_qnty+=$sewing_in_qnty_array[$row[csf('id')]];
				$tot_sewing_out_qnty+=$sewing_out_qnty_array[$row[csf('id')]];
				$tot_sewing_finish_qnty+=$sewing_finish_qnty_array[$row[csf('id')]];
				$tot_ex_qnty+=$ex_qnty_array[$row[csf('id')]];
				$tot_grey_cost_value+=$grey_and_dyeing_cost_data[$row[csf('id')]][2];
				$tot_dying_cost+=$grey_and_dyeing_cost_data[$row[csf('id')]][4];
				$total_fabric_cost+=$grey_and_dyeing_cost_data[$row[csf('id')]][2]+$grey_and_dyeing_cost_data[$row[csf('id')]][4]+$yarn_cost_actual;
				
				
				$i++;
			} 
            ?> 
        
		</table>
        </div>

        <table width="<?=$main_width+$new_column;?>" border="1" cellpadding="0" cellspacing="0" id="report_table_footer" class="rpt_table" rules="all">
         	 <tfoot>
	           <tr>
                <th width="35">&nbsp; </th>
                <th width="100">&nbsp;</th>
                <th width="70">&nbsp;</th>	
                <th width="70">&nbsp;</th>	
                <th width="80">&nbsp;</th>	
                <th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="130">&nbsp;</th>
                <th width="60">&nbsp;</th>	
                <th width="100" id="value_po_qty_td" ><? echo number_format($tot_po_quantity,0);?></th>	
				<th width="80"></th>
			    <th width="80" id="td_po_value"><p  style="width:80px; word-wrap:break-word;"><? echo number_format($tot_po_value,0);?></p></th>	
                <th width="80"></th>
                <th width="60" id=""><? //echo round($tot_set_smv);?></th>	
                <th width="60" style="width:60px; word-wrap:break-word;" id="td_gf_qnty"><p><? echo number_format($tot_gf_qnty,0);?></p></th>
                <th width="60" id="td_yarn_alloc_qnty"><p  style="width:60px; word-wrap:break-word;"><? echo number_format($tot_yarn_alloc_qnty,0);?></p></th>
                <th width="60" id="td_yarn_issue_qnty"><p  style="width:60px; word-wrap:break-word;"><? echo number_format($tot_yarn_issue_qnty,0);?></p></th>
                <th width="60" id="td_yarn_value" ><p style="width:60px; word-wrap:break-word;"><? echo number_format($tot_yarn_value,0);?></p></th>
                <th width="60"  id="td_gp_qty"><p style="width:60px; word-wrap:break-word;"><? echo number_format($tot_gp_qty,0);?></p></th>
                <th width="60" id="td_yarn_budget_value"><p  style="width:60px; word-wrap:break-word;"><? echo number_format($yarn_budget_value,0);?></p></th>
                <th width="60" id="td_grey_cost_value"><p  style="width:60px; word-wrap:break-word;"><? echo number_format($tot_grey_cost_value,0);?></p></th>
                <th width="60" id="td_gp_to_qty"><p  style="width:60px; word-wrap:break-word;"><? echo number_format($tot_grey_to_dye,0);?></p></th>	
                <th width="60" id=""><? //echo round(($tot_gp_qty/$tot_gf_qnty)*100);?></th>	
                <th width="60" id="td_daying_qnty"><p  style="width:60px; word-wrap:break-word;"><? echo number_format($tot_daying_qnty,0);?></p></th>	
                <th width="60" id="td_dying_cost"><p  style="width:60px; word-wrap:break-word;"><? echo number_format($tot_dying_cost,0);?></p></th>	
                <th width="60" id="td_total_fabric_cost"><p  style="width:60px; word-wrap:break-word;"><? echo number_format($total_fabric_cost,0);?></p></th>	
                <th width="60" id="td_ff_qnty" ><p style="width:60px; word-wrap:break-word;"><? echo number_format($tot_ff_qnty,0);?></p></th>	
                <th width="60" id="td_fabrics_avl_qnty"><p  style="width:60px; word-wrap:break-word;"><? echo number_format($tot_fabrics_avl_qnty,0);?></p></th>	
                <th width="60" id="td_blance"><p  style="width:60px; word-wrap:break-word;"><? echo number_format($tot_blance,0);?></p></th>	
                <th width="60" id="td_lay_cut_qnty"><p  style="width:60px; word-wrap:break-word;"><? echo number_format($tot_lay_cut_qnty,0);?></p></th>	
                <th width="60" id="">&nbsp; </th>
                <th width="60" id="td_cut_qnty"><p  style="width:60px; word-wrap:break-word;"><? echo number_format($tot_cut_qnty,0);?></p></th>	
                <th width="60" id=""><? //echo round(($tot_cut_qnty/$tot_po_quantity)*100);?></th>	
                <th width="50" id="td_emb_issue_qnty"><p  style="width:50px; word-wrap:break-word;"><? echo number_format($tot_emb_issue_qnty,0);?></p></th>	
                <th width="60" id="td_emb_rec_qnty"><p  style="width:60px; word-wrap:break-word;"><? echo number_format($tot_emb_rec_qnty,0);?></p></th>	
                <th width="60" id="td_sewing_in_qnty"><p  style="width:60px; word-wrap:break-word;"><? echo number_format($tot_sewing_in_qnty,0);?></p></th>	
                <th width="60" id="td_sewing_out_qnty"><p  style="width:60px; word-wrap:break-word;"><? echo number_format($tot_sewing_out_qnty,0);?></p></th>
                <th width="60" id="td_sewing_finish_qnty" ><p style="width:60px; word-wrap:break-word;"><? echo number_format($tot_sewing_finish_qnty,0);?></p></th>
                <th width="50" id="td_ex_qnty"><p  style="width:50px; word-wrap:break-word;"><? echo number_format($tot_ex_qnty,0);?></p></th>	
               	<th width="">&nbsp;</th>	
                 </tr>
      		</tfoot>
        </table>   
	</div>
	
	<?
	$html = ob_get_contents();
	ob_clean();
	 
	foreach (glob($_SESSION['logic_erp']['user_id']."*.xls") as $filename) {
	@unlink($filename);
	}
	
	$name=time();
	$filename=$_SESSION['logic_erp']['user_id']."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,$html);
	echo "$html****$filename****1";
	exit();	
}

if ($action=="report_generate_with_tna")
{	
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	$cbo_fabric_nature=str_replace("'","",$cbo_fabric_nature);
	$txt_file_no=str_replace("'","",$txt_file_no);
	$txt_ref_no=str_replace("'","",$txt_ref_no);
	
	if($cbo_company_name==0 || $cbo_company_name=="")
	{ 
		$company_name="";$company_name2="";
	}
	else 
	{ 
		$company_name = "and a.company_name=$cbo_company_name";$company_name2 = "and a.company_id=$cbo_company_name";
	}//fabric_source//item_category
	
	if($cbo_fabric_nature!=0) $fab_nature_cond= " and a.item_category =$cbo_fabric_nature";else $fab_nature_cond="";
	if($cbo_fabric_source!=0) $fab_source_cond= " and a.fabric_source =$cbo_fabric_source";else $fab_source_cond="";

	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_cond="";
		}
		else
		{
			$buyer_id_cond="";$buyer_id_cond2="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
		$buyer_id_cond2=" and a.buyer_id=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	$cbo_ship_status=str_replace("'","",$cbo_ship_status);
	$shipping_status_cond='';
	if($cbo_ship_status!=0)
	{
		$shipping_status_cond=" and b.shiping_status=$cbo_ship_status";
	}
	if($txt_file_no!='') $file_no_cond="and b.file_no=$txt_file_no";else $file_no_cond="";
	if($txt_ref_no!='') $ref_no_cond="and b.grouping='$txt_ref_no'";else $ref_no_cond="";
	
	$search_text='';$search_text2='';
	if(str_replace("'","",$txt_style_ref)!=""){$search_text = " and a.style_ref_no like '%".str_replace("'","",$txt_style_ref)."%'";}
	if(str_replace("'","",$txt_job_no)!=""){$search_text .= " and a.job_no like '%".str_replace("'","",$txt_job_no)."'";}
	if(str_replace("'","",$txt_job_no)!=""){$search_text2 .= " and d.job_no like '%".str_replace("'","",$txt_job_no)."%'";}
	if(str_replace("'","",$txt_order_no)!=""){$search_text .= " and b.po_number like '%".str_replace("'","",$txt_order_no)."%'";}
	if(str_replace("'","",$cbo_order_status)!=0){$search_text .= " and b.is_confirmed = '".str_replace("'","",$cbo_order_status)."'";}
	if(str_replace("'","",$cbo_agent_name)!=0) $search_text.=" and a.agent_name=$cbo_agent_name";
	if(str_replace("'","",$cbo_year)!=0)
	{
		if($db_type==0){$search_text.=" and YEAR(a.insert_date)=$cbo_year";}
		else if($db_type==2){$search_text.=" and to_char(a.insert_date,'YYYY')=$cbo_year";}
	}
	
	
	$txt_date_from=str_replace("'","",trim($txt_date_from));
	$txt_date_to=str_replace("'","",trim($txt_date_to));
	
	if($txt_date_from!="" && $txt_date_to!=""){
		if($db_type==0){
			$start_date = date('Y-m-d H:i:s',strtotime($txt_date_from)); 
			$end_date = date("Y-m-d",strtotime($txt_date_to));
			if(str_replace("'","",$cbo_date_type)==2){
				$search_text .=" and b.insert_date between '".$start_date."' and '".$end_date." 23:59:59'";
				$search_text2 .=" and c.insert_date between '".$start_date."' and '".$end_date." 23:59:59'";
			}
			else
			{
				$search_text .=" and b.pub_shipment_date between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and c.pub_shipment_date between '".$start_date."' and '".$end_date."'";
			}
		}
		else
		{
			
			$start_date = change_date_format(date("Y-m-d H:i:s",strtotime($txt_date_from)),'','',1);
			$end_date = change_date_format(date("Y-m-d H:i:s",strtotime($txt_date_to)),'','',1);
			if(str_replace("'","",$cbo_date_type)==3){
				$search_text.=" and b.insert_date between '".$start_date."' and '".$end_date." 11:59:59 PM'";
				$search_text2.=" and c.insert_date between '".$start_date."' and '".$end_date." 11:59:59 PM'";
			}
			elseif(str_replace("'","",$cbo_date_type)==2)
			{
				$search_text .=" and b.shipment_date between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and c.shipment_date between '".$start_date."' and '".$end_date."'";
			}
			elseif(str_replace("'","",$cbo_date_type)==4)
			{
				$search_text .=" and d.country_ship_date between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and d.country_ship_date between '".$start_date."' and '".$end_date."'";
			}
			else
			{
				$search_text .=" and b.pub_shipment_date between '".$start_date."' and '".$end_date."'";
				$search_text2 .=" and c.pub_shipment_date between '".$start_date."' and '".$end_date."'";
			}
		
		}
	}
	
	//function for omit zero value
	function omitZero($value){
		if($value==0){
			return "";
			}
		else {
			return $value;
		}
	}
	//echo omitZero(10);
	
	$items_library=return_library_array( "select id, item_name from lib_garment_item where  status_active=1 and is_deleted=0", "id", "item_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0", "id", "buyer_name"  );
	$company_lib=return_library_array( "select id, company_name from lib_company where  status_active=1 and is_deleted=0", "id", "company_name"  );
	//$supplier_library = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	//$user_arr = return_library_array("select id,user_name from user_passwd where valid=1","id","user_name");
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='knit_order_entry'",'master_tble_id','image_location');
			
	//$ff_qnty_array=return_library_array( "select sum(a.fin_fab_qnty) as fin_fab_qnty,a.po_break_down_id from wo_booking_dtls a where  a.booking_type=1 $poCon and a.status_active=1 and a.is_deleted=0 group by a.po_break_down_id", "po_break_down_id", "fin_fab_qnty");
	
	$fab_sql="select b.po_break_down_id as po_id ,b.grey_fab_qnty,a.booking_no_prefix_num,a.booking_no,
	(CASE WHEN b.booking_type=1 THEN b.fin_fab_qnty END) as fin_fab_qnty
	 from wo_booking_mst a, wo_booking_dtls b,wo_po_break_down c,wo_po_details_master d where a.booking_no=b.booking_no and  b.po_break_down_id=c.id and d.job_no=c.job_no_mst and b.booking_type=1 and b.status_active=1 and b.is_deleted=0 $company_name2 $buyer_id_cond2 $fab_nature_cond $fab_source_cond $search_text2";
	$po_ids='';
	$fab_result = sql_select($fab_sql);// or die(mysql_error());
	 foreach($fab_result as $row)
	 {
		if($po_ids=='') $po_ids=$row[csf('po_id')];else $po_ids.=",".$row[csf('po_id')];
		$ff_qnty_array[$row[csf('po_id')]]+=$row[csf('fin_fab_qnty')];
		$gf_qnty_array[$row[csf('po_id')]]+=$row[csf('grey_fab_qnty')];
		$booking_no_array[$row[csf('po_id')]][$row[csf('booking_no')]]=$row[csf('booking_no_prefix_num')];
		 
	 }
	 unset($fab_result);
	// echo  $po_ids;
	 if($cbo_fabric_nature!=0 || $cbo_fabric_source!=0)
	 {
		 $all_po=array_unique(explode(",",$po_ids));
		$po_arr_cond=array_chunk($all_po,1000, true);
		$po_cond_for_in="";$po=0;
		foreach($po_arr_cond as $key=>$value)
		{
		   if($po==0)
		   {
			$po_cond_for_in=" and b.id  in(".implode(",",$value).")"; 
		
		   }
		   else //po_break_down_id
		   {
			$po_cond_for_in.=" or b.id  in(".implode(",",$value).")";
			
		   }
		   $po++;
		}	
	 }
//echo $po_cond_for_in;die;
		if($db_type==0){$date_diff="DATEDIFF(b.shipment_date,b.po_received_date) as  date_diff,";}
		else{$date_diff=" (b.shipment_date - b.po_received_date) as  date_diff,";}

		 $sql="select $date_diff a.buyer_name,a.total_set_qnty,a.job_no_prefix_num ,a.job_no,a.style_ref_no,b.po_number,a.set_smv,a.set_break_down,a.order_uom,a.gmts_item_id,b.id,a.buyer_name,(b.po_quantity*a.total_set_qnty) as po_quantity,b.po_quantity as  po_qty,b.plan_cut,b.unit_price,b.pub_shipment_date,b.po_received_date,b.insert_date,b.grouping, b.file_no,b.is_confirmed,b.inserted_by,b.po_total_price,b.details_remarks,c.country_ship_date from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id=d.po_break_down_id $company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $search_text $buyer_id_cond $shipping_status_cond $po_cond_for_in $file_no_cond $ref_no_cond order by b.pub_shipment_date"; //echo $sql;
		 
			
		 
		 		
	$order_sql_result = sql_select($sql) or die(mysql_error());
	foreach($order_sql_result as $rows){
		//$order_data_arr[$rows[csf('buyer_name')]][]=$rows;
		$order_data_arr[]=$rows;
		$order_id_arr[$rows[csf('id')]]=$rows[csf('id')];
	}
	unset($order_sql_result);
	
	
		$po_id_list_arr=array_chunk($order_id_arr,999);
	
		$poCon = " and ";$poCon2 = " and ";$poCon3 = " and ";$poCon4 = " and ";$poCon5 = " and ";
		$p=1;
		foreach($po_id_list_arr as $po_process)
		{
			if($p==1) $poCon .="  ( a.po_break_down_id in(".implode(',',$po_process).")"; 
			else  $poCon .=" or a.po_break_down_id in(".implode(',',$po_process).")";
			
			if($p==1) $poCon2 .="  ( b.po_id in(".implode(',',$po_process).")"; 
			else  $poCon2 .=" or b.po_id in(".implode(',',$po_process).")";
			
			if($p==1) $poCon3 .="  ( po_break_down_id in(".implode(',',$po_process).")"; 
			else  $poCon3 .=" or po_break_down_id in(".implode(',',$po_process).")";
			
			if($p==1) $poCon4 .="  ( a.po_breakdown_id in(".implode(',',$po_process).")"; 
			else  $poCon4 .=" or a.po_breakdown_id in(".implode(',',$po_process).")";
			
			if($p==1) $poCon5 .="  ( po_breakdown_id in(".implode(',',$po_process).")"; 
			else  $poCon5 .=" or po_breakdown_id in(".implode(',',$po_process).")";
			
			
			$p++;
		}
		$poCon .=")";$poCon2 .=")";$poCon3 .=")";$poCon4 .=")";$poCon5 .=")";
	
		$dataArrayYarnIssuesQty = array(); $productsIdArray = array();
		$sqlsTrans="select  b.po_breakdown_id, sum(b.quantity) as qnty from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(16,19,61) and a.item_category=13 and a.transaction_type in(2) and b.trans_type in(2) $trans_date group by  b.po_breakdown_id";
		$results_trans=sql_select( $sqlsTrans );
		foreach ($results_trans as $row)
		{
			$dataArrayYarnIssuesQty[$row[csf('po_breakdown_id')]]['qnty']+=$row[csf('qnty')];
			//$productsIdArray[$row[csf('po_breakdown_id')]].=$row[csf('prod_id')].',';
		}


	
	$daying_qnty_array=return_library_array( "select sum(b.batch_qnty) as batch_qnty,b.po_id from pro_batch_create_mst a,pro_batch_create_dtls b, pro_fab_subprocess c where a.id=b.mst_id and a.id=c.batch_id and c.load_unload_id=2 and c.entry_form=35 and a.batch_against<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $poCon2  group by b.po_id", "po_id", "batch_qnty");
	
	
	
	$recvData=sql_select("select a.po_breakdown_id, sum(quantity) as grey_prod_qnty from order_wise_pro_details a, pro_grey_prod_entry_dtls b where a.dtls_id=b.id and a.prod_id=b.prod_id and a.entry_form =2 and a.is_deleted=0 and a.status_active=1 $poCon4 group by a.po_breakdown_id");
	foreach($recvData as $row)
	{
		$gp_qty_array[$row[csf('po_breakdown_id')]]=$row[csf('grey_prod_qnty')];
	}
	unset($recvData);
	
	
	/*$sql="select 
		po_break_down_id, 
		SUM(CASE WHEN production_type=1 THEN production_quantity END) as totalcut,
		SUM(CASE WHEN production_type=4 THEN production_quantity END) as totalinput,
		SUM(CASE WHEN production_type=5 THEN production_quantity ELSE 0 END) as totalsewing,
		SUM(CASE WHEN production_type=8 THEN production_quantity ELSE 0 END) as totalfinish,
		SUM(CASE WHEN production_type=2 THEN production_quantity ELSE 0 END) as totaembissue,
		SUM(CASE WHEN production_type=3 THEN production_quantity ELSE 0 END) as totaembrec
	from pro_garments_production_mst 
	WHERE status_active=1 $poCon3 and is_deleted=0
	group by po_break_down_id";*/
	$sql="select 
		a.po_break_down_id, 
		SUM(CASE WHEN a.production_type=1 THEN b.production_qnty END) as totalcut,
		SUM(CASE WHEN a.production_type=4 THEN b.production_qnty END) as totalinput,
		SUM(CASE WHEN a.production_type=5 THEN b.production_qnty ELSE 0 END) as totalsewing,
		SUM(CASE WHEN a.production_type=8 THEN b.production_qnty ELSE 0 END) as totalfinish,
		SUM(CASE WHEN a.production_type=2 THEN b.production_qnty ELSE 0 END) as totaembissue,
		SUM(CASE WHEN a.production_type=3 THEN b.production_qnty ELSE 0 END) as totaembrec
	from pro_garments_production_mst a,pro_garments_production_dtls b
	WHERE  a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 $poCon group by po_break_down_id";
	$dataArray=sql_select($sql);
	foreach($dataArray as $row)
	{  
		$cut_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totalcut')];
		$sewing_in_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totalinput')];
		$sewing_out_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totalsewing')];
		$sewing_finish_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totalfinish')];
		$emb_issue_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totaembissue')];
		$emb_rec_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totaembrec')];
	
	}
	unset($dataArray);
	$ex_qnty_array=return_library_array( "select po_break_down_id, sum(ex_factory_qnty) as ex_factory_qnty from pro_ex_factory_mst where  status_active=1 $poCon3 and is_deleted=0 group by po_break_down_id", "po_break_down_id", "ex_factory_qnty");
	
	
	
	
if(return_field_value("auto_update","variable_settings_production","company_name=$cbo_company_name and variable_list=15 and item_category_id=2 order by id","auto_update")==2)
{
	$fabrics_avl_qnty_array=return_library_array( "select po_breakdown_id, sum(quantity) as quantity from order_wise_pro_details where entry_form=37 $poCon5 and status_active=1 and is_deleted=0 group by po_breakdown_id", "po_breakdown_id", "quantity");
	
}
else
{
	$fabrics_avl_qnty_array=return_library_array( "select po_breakdown_id, sum(quantity) as quantity from order_wise_pro_details where entry_form=7 $poCon5 and status_active=1 and is_deleted=0 group by po_breakdown_id", "po_breakdown_id", "quantity");
}

					
//Summary Data create......................................start;
	
	foreach($order_data_arr as $rows)
	{
		
		$issQtyN=$dataArrayYarnIssuesQty[$rows[csf('id')]]['qnty'];
		//echo $rows[csf('set_smv')];
		$buyer_summary_arr[$rows[csf('buyer_name')]]['buyer_name']=$rows[csf('buyer_name')];
		$buyer_summary_arr[$rows[csf('buyer_name')]]['po_quantity']+=$rows[csf('po_quantity')];
		//$buyer_summary_arr[$rows[csf('buyer_name')]]['po_val']+=($rows[csf('po_quantity')]*($rows[csf('unit_price')]/$rows[csf('total_set_qnty')]));
		$buyer_summary_arr[$rows[csf('buyer_name')]]['po_val']+=$rows[csf('po_total_price')];//new
		
		
		
		
		//$buyer_summary_arr[$rows[csf('buyer_name')]]['cutting_qty']+=$rows[csf('cutting_qty')];
		$buyer_summary_arr[$rows[csf('buyer_name')]]['cutting_qty']+=$cut_qnty_array[$rows[csf('id')]];
		
		
		$buyer_summary_arr[$rows[csf('buyer_name')]]['sewing_in']+=$sewing_in_qnty_array[$rows[csf('id')]];
		$buyer_summary_arr[$rows[csf('buyer_name')]]['sewing_out']+=$sewing_out_qnty_array[$rows[csf('id')]];
		$buyer_summary_arr[$rows[csf('buyer_name')]]['finish_qty']+=$sewing_finish_qnty_array[$rows[csf('id')]];
		$buyer_summary_arr[$rows[csf('buyer_name')]]['exfactory_qty']+=$ex_qnty_array[$rows[csf('id')]];
		$buyer_summary_arr[$rows[csf('buyer_name')]]['emb_rec_qty']+=$emb_rec_qnty_array[$rows[csf('id')]];
		$buyer_summary_arr[$rows[csf('buyer_name')]]['grey_req']+=round($gf_qnty_array[$rows[csf('id')]]);
		$buyer_summary_arr[$rows[csf('buyer_name')]]['grey_prod']+=$gp_qty_array[$rows[csf('id')]];
		$buyer_summary_arr[$rows[csf('buyer_name')]]['grey_to_dye']+=round($issQtyN);
		//$buyer_summary_arr[$rows[csf('buyer_name')]]['grey_prod']+=round($dataArrayYarnIssue[$rows[csf('id')]]);
		$buyer_summary_arr[$rows[csf('buyer_name')]]['dyeing']+=round($daying_qnty_array[$rows[csf('id')]]);
		$buyer_summary_arr[$rows[csf('buyer_name')]]['avg_smv']+=$rows[csf('set_smv')]*$rows[csf('po_qty')];
		$buyer_summary_arr[$rows[csf('buyer_name')]]['freq']+=round($ff_qnty_array[$rows[csf('id')]]);
		$buyer_summary_arr[$rows[csf('buyer_name')]]['favl']+=round($fabrics_avl_qnty_array[$rows[csf('id')]]);
		$buyer_summary_arr[$rows[csf('buyer_name')]]['emb_issue']+=$emb_issue_qnty_array[$rows[csf('id')]];
		//$gp_per=round(($gp_qty_array[$row[csf('id')]]/$gf_qnty_array[$row[csf('id')]])*100);
		
		$buyer_tot_arr['po_quantity']+=$rows[csf('po_quantity')];
		
		//$buyer_tot_arr['po_val']+=($rows[csf('po_quantity')]*($rows[csf('unit_price')]/$rows[csf('total_set_qnty')]));
		$buyer_tot_arr['po_val']+=$rows[csf('po_total_price')];//new
		
		
		
		//$buyer_tot_arr['plan_cut']+=$rows[csf('plan_cut')];
		
		
		$buyer_tot_arr['cutting_qty']+=$cut_qnty_array[$rows[csf('id')]];
		
		$buyer_tot_arr['sewing_in']+=$sewing_in_qnty_array[$rows[csf('id')]];
		$buyer_tot_arr['sewing_out']+=$sewing_out_qnty_array[$rows[csf('id')]];
		$buyer_tot_arr['finish_qty']+=$sewing_finish_qnty_array[$rows[csf('id')]];
		$buyer_tot_arr['exfactory_qty']+=$ex_qnty_array[$rows[csf('id')]];
		$buyer_tot_arr['emb_rec_qty']+=$emb_rec_qnty_array[$rows[csf('id')]];
		
		$buyer_tot_arr['grey_req']+=round($gf_qnty_array[$rows[csf('id')]]);
		//$buyer_tot_arr['grey_prod']+=round($dataArrayYarnIssue[$rows[csf('id')]]);
		$buyer_tot_arr['grey_prod']+=$gp_qty_array[$rows[csf('id')]];
		$buyer_tot_arr['grey_to_dye']+=round($issQtyN);
		$buyer_tot_arr['dyeing']+=round($daying_qnty_array[$rows[csf('id')]]);
		$buyer_tot_arr['freq']+=round($ff_qnty_array[$rows[csf('id')]]);
		$buyer_tot_arr['favl']+=round($fabrics_avl_qnty_array[$rows[csf('id')]]);
		$buyer_tot_arr['emb_issue']+=$emb_issue_qnty_array[$rows[csf('id')]];
	}
//Summary Data create......................................end;	

	ob_start();
	
	
	//TNA Part..........................................................................................
	$tna_task_id_sting = str_replace("'",'',$tna_task_id);
	if($tna_task_id_sting){
		$tna_task_id_arr = explode(',',$tna_task_id_sting);
		$total_task = count($tna_task_id_arr);
	}
	else
	{
		$tna_task_id_arr = 0;
		$total_task = 0;
	}


	if($db_type==0)
	{
		$sql ="select a.po_number_id, a.job_no, a.shipment_date, a.template_id, a.po_receive_date,b.insert_date,";
		$i=1;
	
		foreach( $tna_task_id_arr as $dval=>$id)    	
		{
			if ($i!=$total_task){$sql .="max(CASE WHEN CONCAT(a.task_number) = '".$id."' THEN concat(a.actual_start_date,'_',a.actual_finish_date,'_',a.task_start_date,'_',a.task_finish_date,'_',a.notice_date_start,'_',a.notice_date_end,'_',a.remarks,'_',a.id,'_',a.task_number)  END ) as status$id, ";
			}
			else{$sql .="max(CASE WHEN CONCAT(a.task_number) = '".$id."' THEN concat(a.actual_start_date,'_',a.actual_finish_date,'_',a.task_start_date,'_',a.task_finish_date,'_',a.notice_date_start,'_',a.notice_date_end,'_',a.remarks,'_',a.id,'_',a.task_number)  END ) as status$id ";
			}
			$i++;
		}
		
		$sql .=" from tna_process_mst a, wo_po_break_down b where a.po_number_id=b.id and a.po_number_id in( $order_id_arr )  and b.status_active=1  and b.po_quantity>0  group by a.po_number_id,a.job_no,b.insert_date order by a.shipment_date,a.po_number_id,a.job_no"; 
	}
	else
	{
		$sql ="select a.po_number_id, a.job_no, max(a.shipment_date) as shipment_date, a.template_id, max(a.po_receive_date) as po_receive_date,b.insert_date,";
		$i=1;
		
		foreach( $tna_task_id_arr as $dval=>$id)    	
		{
			if ($i!=$total_task){$sql .="max(CASE WHEN a.task_number = '".$id."' THEN a.actual_start_date || '_' || a.actual_finish_date || '_' || a.task_start_date || '_' || a.task_finish_date ||'_' || a.notice_date_start || '_' || a.notice_date_end || '_' || a.remarks || '_' || a.id  END ) as status$id, ";
			}
			else{$sql .="max(CASE WHEN a.task_number = '".$id."' THEN a.actual_start_date || '_' || a.actual_finish_date || '_' || a.task_start_date || '_' || a.task_finish_date || '_' || a.notice_date_start || '_' || a.notice_date_end || '_' || a.remarks || '_' || a.id  END ) as status$id ";
			}
			
			$i++;
		}
		//------------------

			$chunk_po_no_arr_all=array_chunk($order_id_arr,999);
			$p=1;
			foreach($chunk_po_no_arr_all as $po_id)
			{
				if($p==1) $sql_order_con .=" and (a.po_number_id in(".implode(',',$po_id).")"; 
				else $sql_order_con .=" or a.po_number_id in(".implode(',',$po_id).")";
				$p++;
			}
			$sql_order_con .=" )";
			
			
		//-------------------------------
		$sql .=" from  tna_process_mst a, wo_po_break_down b where a.po_number_id=b.id $sql_order_con  and b.status_active=1 and b.po_quantity>0 $order_status_cond  group by a.po_number_id,a.job_no,a.template_id,a.shipment_date,b.insert_date order by a.shipment_date,a.po_number_id,a.job_no"; 
		}
	//echo $sql;
		
		$tna_data_arr = sql_select($sql);
		foreach($tna_data_arr as $row)
		{
			foreach($tna_task_id_arr as $key)
			{
			   $new_data=explode("_",$row[csf('status').$key]);
			   $tnaDataArr[$row[csf('po_number_id')]][$key][start_date]=change_date_format($new_data[2]);
			   $tnaDataArr[$row[csf('po_number_id')]][$key][end_date]=change_date_format($new_data[3]);
			}

		}
	
	$tskArr=return_library_array( "select task_name,task_short_name from  lib_tna_task where status_active=1 and is_deleted=0", "task_name", "task_short_name");
	
	
	$tbWith=2190+($total_task*160);
	
	
	?>
    <br />
    <fieldset style="width:1290px;">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr><td colspan="12" align="center" style="font-size:22px;">Buyer Summary</td></tr>
        </table>
        <table width="100%" border="1" rules="all"  class="rpt_table"> 
            <thead>
                <tr style="font-size:12px"> 
                    <th width="35">Sl</th>
                    <th width="250">Buyer</th>
                    <th width="80">QTY</th>
                    <th width="80">Order Value</th>
                    <th width="80">Avg. SMV</th>
                    <th width="80">Grey Required</th>
                    <th width="80">Grey Prod.</th>
                    <th width="80">Grey to Dye</th>
                    <th width="80">G2D (%)</th>
                    <th width="80">Dyeing</th>
                    <th width="80">Fabrics Required</th> 
                    <th width="80">Fabrics Available</th>
                    <th width="80">Balance</th>
                    <th width="80">Cut Qty</th>
                    <th width="80">Cut %</th>
                    <th width="80">Emb. Issue</th>
                    <th width="80">Emb. Receive</th>
                    <th width="80">Sewing Input</th>
                    <th width="80">Sewing Output</th>
                    <th width="80">Finish Qty</th>
                    <th width="80">Finish %</th>
                    <th width="80">Ex-Factory</th>
                    <th width="80">Ex-Fac %</th>
               </tr>
            </thead>
            <tbody>
                <? 
				$tot_cut_per="";
				$i=1;$balance_qty=0;
				foreach($buyer_summary_arr as $rows){
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					$finish_par=($rows['finish_qty']/$rows['po_quantity'])*100;
					$exfactory_par=($rows['exfactory_qty']/$rows['po_quantity'])*100;
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('str_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="str_<? echo $i; ?>" style="font-size:12px; cursor:pointer;"" valign="middle">
                    <td><? echo $i;?></td>	
                    <td><p><? echo $buyer_library[$rows['buyer_name']];?></td>	
                    <td align="right"><? echo omitZero(number_format($rows['po_quantity'],0));?></td>	
                    <td align="right"><? echo omitZero(number_format($rows['po_val'],0));?></td>
                     
                     <td align="right" title="<? echo $rows['avg_smv'];?>">
					 <? 
					 $avg_smv=$rows['avg_smv'];
					 $po_qty=$rows['po_quantity'];
					 $smv= ($avg_smv/$po_qty);
					// echo gettype($po_qty);
					 echo omitZero(number_format($smv,2)); 
					 $cut_per=($rows['cutting_qty']/$rows['po_quantity'])*100;
					 $tot_cut_per+=$cut_per;
					 //$cut_per='=='.$rows['cutting_qty'].'/'.$rows['po_quantity'];
					 //echo $issQtyN; //
					 ?>
                     </td>
                     <td align="right"><? echo omitZero(number_format($rows['grey_req'],0));?></td>
                     <td align="right"><? echo number_format($rows['grey_prod'],0);?></td>
                     <td align="right"><? echo omitZero(number_format($rows['grey_to_dye'],0));?></td>
                     <td align="right"><? echo omitZero(number_format(($rows['grey_to_dye']/$rows['grey_req']*100),0));?></td>
                     <td align="right"><? echo omitZero(number_format($rows['dyeing']));?></td>
                     <td align="right"><? echo omitZero(number_format($rows['freq'],0));?></td>
                     <td align="right"><? echo omitZero(number_format($rows['favl'],0));?></td>
                     <td align="right"><? echo omitZero(number_format($rows['freq']-$rows['favl'],0));?></td>
                    <td align="right"><? echo omitZero(number_format($rows['cutting_qty']));?></td>	
                    <td align="right"><? echo omitZero(number_format($cut_per,0));?></td>
                    <td align="right"><? echo omitZero(number_format($rows['emb_issue'],0));?></td>		
                    <td align="right"><? echo omitZero(number_format($rows['emb_rec_qty'],0));?></td>	
                    <td align="right"><? echo omitZero(number_format($rows['sewing_in'],0));?></td>	
                    <td align="right"><? echo omitZero(number_format($rows['sewing_out'],0));?></td>	
                    <td align="right"><? echo omitZero(number_format($rows['finish_qty'],0));?></td>	
                    <td align="right"><? echo omitZero(number_format($finish_par,0));?></td>	
                    <td align="right"><? echo omitZero(number_format($rows['exfactory_qty'],0));?></td>	
                    <td align="right"><? echo omitZero(number_format($exfactory_par,0));?></td>	
                </tr>
               <? 
			   $i++;
			   $balance_qty+=$rows['freq']-$rows['favl'];
			   } ?>
            </tbody>
            <tfoot>
                <th colspan="2" align="right">Total </th>	
                <th align="right"><? echo omitZero(number_format($buyer_tot_arr['po_quantity'],0));?></th>	
                <th align="right"><? echo omitZero(number_format($buyer_tot_arr['po_val']));?></th>
                <th align="right"><? //echo round($buyer_tot_arr['grey_req']);?></th>	
                <th align="right"><? echo omitZero(number_format($buyer_tot_arr['grey_req'],0));?></th>	
                <th align="right"><? echo omitZero(number_format($buyer_tot_arr['grey_prod'],0));?></th>	
                <th align="right"><? echo omitZero(number_format($buyer_tot_arr['grey_to_dye'],0));?></th>	
                <th align="right"><? //echo round($buyer_tot_arr['dyeing']);?></th>
                <th align="right"><? echo omitZero(number_format($buyer_tot_arr['dyeing'],0));?></th>	
                <th align="right"><? echo omitZero(number_format($buyer_tot_arr['freq'],0));?></th>	
                <th align="right"><? echo omitZero(number_format($buyer_tot_arr['favl'],0));?></th>	
                <th align="right"><? echo omitZero(number_format($balance_qty,0));?></th>	
                <th align="right"><? echo omitZero(number_format($buyer_tot_arr['cutting_qty'],0));?></th>	
                <th align="right"><? echo omitZero(number_format($tot_cut_per,0));?></th>	
                <th align="right"><? echo omitZero(number_format($buyer_tot_arr['emb_issue'],0));?></th>	
                <th align="right"><? echo omitZero(number_format($buyer_tot_arr['emb_rec_qty'],0));?></th>	
                <th align="right"><? echo omitZero(number_format($buyer_tot_arr['sewing_in'],0));?></th>	
                <th align="right"><? echo omitZero(number_format($buyer_tot_arr['sewing_out'],0));?></th>	
                <th align="right"><? echo omitZero(number_format($buyer_tot_arr['finish_qty'],0));?></th>	
                <th align="right"><? echo omitZero(number_format(($buyer_tot_arr['finish_qty']/$buyer_tot_arr['po_quantity'])*100,0));?></th>	
                <th align="right"><? echo omitZero(number_format($buyer_tot_arr['exfactory_qty']));?></th>	
                <th align="right"><? echo omitZero(number_format(($buyer_tot_arr['exfactory_qty']/$buyer_tot_arr['po_quantity'])*100,0));?></th>	
            </tfoot>
            
        </table>
    </fieldset>
    <br />
    
    
    <fieldset>

	<div style="width:<? echo $tbWith;?>px" align="left">	
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr><td colspan="30" align="center" style="font-size:22px;">Order Follow-Up Report</td></tr>
            <tr>
                <td colspan="30" align="center" style="font-size:16px; font-weight:bold;">
					<? echo $company_lib[$cbo_company_name];?>
                </td>
            </tr>
            <? if($txt_date_from!='' && $txt_date_to!=''){?>
            <tr>
             	<td colspan="30" align="center" style="font-size:14px;">
                    From: <? echo change_date_format($txt_date_from);?> 
                    To: <? echo change_date_format($txt_date_to);?>
                </td>
            </tr>
            <? }?>
        </table>
        <table width="<? echo $tbWith;?>" border="1" rules="all"  class="rpt_table"> 
            <thead>
                <tr style="font-size:12px"> 
                    <th rowspan="2" width="35">Sl</th>	
                    <th rowspan="2" width="100">Buyer</th> 
                    <th rowspan="2" width="80">Job No</th>
                    <th rowspan="2" width="100">Order No</th>
                    <th rowspan="2" width="100">Booking No</th>
                    <th rowspan="2" width="100">Style No</th>
                    <th rowspan="2" width="100">Item Name</th>
                    <th rowspan="2" width="60">Picture</th>	
                    <th rowspan="2" width="100">QTY</th>	
                    <th rowspan="2" width="80">Ship Date</th>
                    <th rowspan="2" width="60">SMV</th>	
                    <th rowspan="2" width="60">Grey Req</th>
                    <th rowspan="2" width="60">Grey Prod.</th>	
                    <th rowspan="2" width="60">Grey to Dye</th>	
                    <th rowspan="2" width="60">G2D (%)</th>	
                    <th rowspan="2" width="60">Dyeing</th>	
                    <th rowspan="2" width="60">Fabrics Req.</th>	
                    <th rowspan="2" width="60" title="95% GREEN 75% - 95% YELLOW">Fabrics Avl.</th>	
                    <th rowspan="2" width="60">Balance</th>	
                    <th rowspan="2" width="60">Cutting</th>	
                    <th rowspan="2" width="60">Cut (%)</th>	
                    <th rowspan="2" width="50">Emb. Issue</th>	
                    <th rowspan="2" width="60">Emb. Receive</th>	
                    <th rowspan="2" width="60">Input</th>	
                    <th rowspan="2" width="60">Output</th>
                    <th rowspan="2" width="60" title="95% GREEN 75% - 95% YELLOW">Finish</th>
                    <th rowspan="2" width="50">Ex- Factory</th>	
                    <? foreach($tna_task_id_arr as $task_id){echo '<th colspan="2" width="160">'.$tskArr[$task_id].'</th>';} ?>	
                    <th rowspan="2">Remarks</th>
                </tr>
                <tr>
                    <? foreach($tna_task_id_arr as $task_id){
						echo '<th width="80">Plan Start</th>';
						echo '<th width="80">Plan End</th>';
						} 
					?>
                </tr>
                
                
                                            	
            </thead>
        </table>
        <div style="width:<? echo $tbWith+20;?>px; max-height:350px; overflow-y:scroll" id="report_div" > 
            <table id="table_body" width="<? echo $tbWith;?>" border="1"  class="rpt_table" rules="all">
              <tbody>
				<?php
                $i=1;
                foreach($order_data_arr as $row)
                {
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					$gp_per=round(($dataArrayYarnIssuesQty[$row[csf('id')]]['qnty']/$gf_qnty_array[$row[csf('id')]])*100);
					
					//$gp_per=round(($gp_qty_array[$row[csf('id')]]/$gf_qnty_array[$row[csf('id')]])*100);
					
					$cut_per=round(($cut_qnty_array[$row[csf('id')]]/$row[csf('po_quantity')])*100);
                    
					$finFactoryPer=round(($sewing_finish_qnty_array[$row[csf('id')]]/$row[csf('po_quantity')])*100);
					if($finFactoryPer>94){$finfBgColor=' bgcolor="#00CC00" ';}
					elseif($finFactoryPer>74 && $finFactoryPer< 95){$finfBgColor='bgcolor="#FFCC33"';}
					else{$finfBgColor='';}
					
					$fabAvlPer=round(($fabrics_avl_qnty_array[$row[csf('id')]]/$ff_qnty_array[$row[csf('id')]])*100);
					if($fabAvlPer>94){$fabAvlBgColor=' bgcolor="#00CC00" ';}
					elseif($fabAvlPer>74 && $fabAvlPer< 95){$fabAvlBgColor='bgcolor="#FFCC33"';}
					else{$fabAvlBgColor='';}
					
					/*$issQty="";
					$dataProds=rtrim($productsIdArray[$row[csf('id')]],',');
					$dataProds=array_unique(explode(",",$dataProds));*/
					//print_r($dataProd);
					$itemText=array();
					$gmts_items=explode(',',$row[csf('gmts_item_id')]);
					foreach($gmts_items as $gmts_id)
					{
						$itemText[$gmts_id]=$items_library[$gmts_id];
					}
					 //var_dump($itemText);die;
					 $issQty=$dataArrayYarnIssuesQty[$row[csf('id')]]['qnty'];   
            ?>	
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="font-size:12px; cursor:pointer;"" valign="middle">
                    <td width="35"><? echo $i;?></td>	
                    <td width="100"><p><? echo $buyer_library[$row[csf('buyer_name')]];?></p></td>
                    <td width="80"><p><? echo $row[csf('job_no_prefix_num')];?></p></td>	
                    <td width="100"><p><? echo "&nbsp;".$row[csf('po_number')];?></p></td>	
                    <td width="100"><p>
					<?
						$html=array();
						foreach($booking_no_array[$row[csf('id')]] as $booking_no=>$booking_num){
						$html[] =$booking_num;	
						}
						echo implode(',',$html);
					?>
                    </p></td>	
                    <td width="100"><p><? echo $row[csf('style_ref_no')];?></p></td>
                    <td width="100"><p><? echo implode(',',$itemText);?></p></td>
                    <td width="60" onClick="openmypage_image('requires/order_follo_up_report_controller.php?action=show_image&job_no=<? echo $row[csf("job_no")] ?>','Image View')" align="center"><img src="../../../<? echo $imge_arr[$row[csf('job_no')]];?>" height='25' width='30' /></td>	
                    <td width="100" align="right"><? echo omitZero(number_format($row[csf('po_quantity')],0));
					$order_qntytot=$order_qntytot+$row[csf('po_quantity')];
					?></td>
                    <td width="80" align="center"><? $originalDate=$row[csf('pub_shipment_date')]; echo date("d-M-Y", strtotime($originalDate));?></td>
                    <td width="60" align="right"><? echo omitZero($row[csf('set_smv')]);?></td>	
                    <td width="60" align="right"><? echo omitZero(number_format($gf_qnty_array[$row[csf('id')]],0));?></td>	
                    <td width="60" align="right"><? echo omitZero(number_format($gp_qty_array[$row[csf('id')]],0));?></td>	
                    <td width="60" align="right"><? echo omitZero(number_format($issQty,0));?></td>	
                    <td width="60" align="right"><? echo omitZero($gp_per);?></td>	
                    <td width="60" align="right"><? echo omitZero(number_format($daying_qnty_array[$row[csf('id')]],0));?></td>	
                    <td width="60" align="right"><? echo omitZero(number_format($ff_qnty_array[$row[csf('id')]],0));?></td>	
                    <td width="60" align="right" <? echo $fabAvlBgColor;?> title="<? echo $fabAvlPer;?>%">
						<? echo omitZero(number_format($fabrics_avl_qnty_array[$row[csf('id')]]),0);?>
                    </td>	
                    <td width="60" align="right"><? echo $blance=omitZero(number_format($ff_qnty_array[$row[csf('id')]]-$fabrics_avl_qnty_array[$row[csf('id')]],0));?></td>	
                    <td width="60" align="right"><? echo omitZero(number_format($cut_qnty_array[$row[csf('id')]],0));?></td>	
                    <td width="60" align="right"><? echo omitZero(number_format($cut_per));?></td>	
                    <td width="50" align="right"><? echo omitZero(number_format($emb_issue_qnty_array[$row[csf('id')]],0));?></td>	
                    <td width="60" align="right"><? echo omitZero(number_format($emb_rec_qnty_array[$row[csf('id')]],0));?></td>
                    <td width="60" align="right"><? echo omitZero(number_format($sewing_in_qnty_array[$row[csf('id')]],0));?></td>	
                    <td width="60" align="right"><? echo omitZero(number_format($sewing_out_qnty_array[$row[csf('id')]],0));?></td>	
                    <td width="60" align="right" <? echo $finfBgColor;?> title="<? echo $finFactoryPer;?>%">
                    	<? echo omitZero(number_format($sewing_finish_qnty_array[$row[csf('id')]],0));?>
                    </td>
					<td width="50" align="right"><? echo omitZero(number_format($ex_qnty_array[$row[csf('id')]],0));?></td>
                  
                    <? foreach($tna_task_id_arr as $task_id){
						echo '<td width="80">'.$tnaDataArr[$row[csf('id')]][$task_id][start_date].'</td>';
						echo '<td width="80">'.$tnaDataArr[$row[csf('id')]][$task_id][end_date].'</td>';
						} 
					?>
                  
                  
                  
                  
                  
                    <td><p><? echo $row[csf('details_remarks')];?></p></td>
                </tr>
            <?
            	
				$tot_po_quantity +=$row[csf('po_quantity')];
				$tot_set_smv+=$row[csf('set_smv')];
				$tot_gf_qnty+=round($gf_qnty_array[$row[csf('id')]]);
				$tot_grey_to_dye+=$issQty;
				$tot_gp_qty+=$gp_qty_array[$row[csf('id')]];
				$tot_daying_qnty+=$daying_qnty_array[$row[csf('id')]];
				//$tot_blance+=round($blance);
				$tot_blance+=round($ff_qnty_array[$row[csf('id')]]-$fabrics_avl_qnty_array[$row[csf('id')]]);
				$tot_fabrics_avl_qnty+=$fabrics_avl_qnty_array[$row[csf('id')]];
				$tot_ff_qnty+=round($ff_qnty_array[$row[csf('id')]]);
				$tot_cut_qnty+=$cut_qnty_array[$row[csf('id')]];
				$tot_emb_issue_qnty+=$emb_issue_qnty_array[$row[csf('id')]];
				$tot_emb_rec_qnty+=$emb_rec_qnty_array[$row[csf('id')]];
				$tot_sewing_in_qnty+=$sewing_in_qnty_array[$row[csf('id')]];
				$tot_sewing_out_qnty+=$sewing_out_qnty_array[$row[csf('id')]];
				$tot_sewing_finish_qnty+=$sewing_finish_qnty_array[$row[csf('id')]];
				$tot_ex_qnty+=$ex_qnty_array[$row[csf('id')]];
				
				
				$i++;
			} 
            ?> 
         </tbody>
		</table>
        </div>

        <table width="<? echo $tbWith+20;?>" border="1" cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all">
         	<tfoot>
                <td width="35"></td>
                <td width="100"></td>
                <td width="80"></td>	
                <td width="100"></td>
                <td width="100"></td>
                <td width="100"></td>
                <td width="100"></td>
                <td width="60"></td>	
                <td width="100"><p id="td_po_quantity" style="width:60px; word-wrap:break-word;"><? echo number_format($order_qntytot,0);?></p></td>	
                <td width="80"></td>
                <td width="60" id=""><? //echo round($tot_set_smv);?></td>	
                <td width="60"><p id="td_gf_qnty" style="width:60px; word-wrap:break-word;"><? echo number_format($tot_gf_qnty,0);?></p></td>
                <td width="60"><p id="td_gp_qty" style="width:60px; word-wrap:break-word;"><? echo number_format($tot_gp_qty,0);?></p></td>
                <td width="60"><p id="td_gp_to_qty" style="width:60px; word-wrap:break-word;"><? echo number_format($tot_grey_to_dye,0);?></p></td>	
                <td width="60" id=""><? //echo round(($tot_gp_qty/$tot_gf_qnty)*100);?></td>	
                <td width="60"><p id="td_daying_qnty" style="width:60px; word-wrap:break-word;"><? echo number_format($tot_daying_qnty,0);?></p></td>	
                <td width="60"><p id="td_ff_qnty" style="width:60px; word-wrap:break-word;"><? echo number_format($tot_ff_qnty,0);?></p></td>	
                <td width="60"><p id="td_fabrics_avl_qnty" style="width:60px; word-wrap:break-word;"><? echo number_format($tot_fabrics_avl_qnty,0);?></p></td>	
                <td width="60"><p id="td_blance" style="width:60px; word-wrap:break-word;"><? echo number_format($tot_blance,0);?></p></td>	
                <td width="60"><p id="td_cut_qnty" style="width:60px; word-wrap:break-word;"><? echo number_format($tot_cut_qnty,0);?></p></td>	
                <td width="60" id=""><? //echo round(($tot_cut_qnty/$tot_po_quantity)*100);?></td>	
                <td width="50"><p id="td_emb_issue_qnty" style="width:50px; word-wrap:break-word;"><? echo number_format($tot_emb_issue_qnty,0);?></p></td>	
                <td width="60"><p id="td_emb_rec_qnty" style="width:60px; word-wrap:break-word;"><? echo number_format($tot_emb_rec_qnty,0);?></p></td>	
                <td width="60"><p id="td_sewing_in_qnty" style="width:60px; word-wrap:break-word;"><? echo number_format($tot_sewing_in_qnty,0);?></p></td>	
                <td width="60"><p id="td_sewing_out_qnty" style="width:60px; word-wrap:break-word;"><? echo number_format($tot_sewing_out_qnty,0);?></p></td>
                <td width="60"><p id="td_sewing_finish_qnty" style="width:60px; word-wrap:break-word;"><? echo number_format($tot_sewing_finish_qnty,0);?></p></td>
                <td width="50"><p id="td_ex_qnty" style="width:50px; word-wrap:break-word;"><? echo number_format($tot_ex_qnty,0);?></p></td>	
				<? foreach($tna_task_id_arr as $task_id){
                    echo '<td width="80">&nbsp;</td>';
                    echo '<td width="80" >&nbsp;</td>';
                    } 
                ?>
                <td >&nbsp;</td>	
      		</tfoot>
        </table>   
	</div>
	</fieldset>	
	<?
	$html = ob_get_contents();
	ob_clean();
	 
	foreach (glob($_SESSION['logic_erp']['user_id']."*.xls") as $filename) {
	@unlink($filename);
	}
	
	$name=time();
	$filename=$_SESSION['logic_erp']['user_id']."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,$html);
	echo "$html****$filename****2";
	exit();	
}




if($action=="show_image")
{
	echo load_html_head_contents("Set Entry","../../../../", 1, 1, $unicode);
    extract($_REQUEST);
	//echo "select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1";
	$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1");
	//$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='knit_order_entry'",'master_tble_id','image_location');
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


if($action=="load_cut_lay_data"){
    echo load_html_head_contents("Set Entry","../../../../", 1, 1, $unicode);
    extract($_REQUEST);
    $items_library=return_library_array( "select id, item_name from lib_garment_item where  status_active=1 and is_deleted=0", "id", "item_name"  );
    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer where status_active = 1 and is_deleted = 0", "id", "buyer_name");
    $company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
    $location_library=return_library_array( "select id,location_name from  lib_location", "id", "location_name"  );
    $colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
    $sizeArr = return_library_array("select id,size_name from lib_size","id","size_name");
    $po_deatils = sql_select("select a.job_no, a.buyer_name, a.gmts_item_id, a.style_ref_no, a.style_description, b.po_number, to_char(b.shipment_date, 'dd-mm-YYYY') as ship_date,b.grouping, b.po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no = b.job_no_mst and b.id = $order_id and b.status_active = 1 and b.is_deleted = 0");
    //echo "select a.job_no, a.buyer_name, a.gmts_item_id, a.style_ref_no, a. a.style_description, b.po_number, to_char(b.shipment_date, 'dd-mm-YYYY') as ship_date,b.grouping, b.po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no = b.job_no_mst and b.id = $order_id and b.status_active = 1 and b.is_deleted = 0";
    ?>
        <script>
                var tableFilters =
                {
                    col_operation: {
                        id: ["total_cut_qty"],
                        col: [4],
                        operation: ["sum"],
                        write_method: ["innerHTML"]
                    }
                };
                function new_window()
                {
                    document.getElementById('scroll_body').style.overflow="auto";
                    document.getElementById('scroll_body').style.maxHeight="none";
                    $('#table_body').attr('width', '620');
                    $('#table_body tr:first').hide();
                    var w = window.open("Surprise", "#");
                    var d = w.document.open();
                    d.write(document.getElementById('details_reports').innerHTML);
                    d.close();
                    $('#table_body tr:first').show();
                    document.getElementById('scroll_body').style.overflowY="scroll";
                    document.getElementById('scroll_body').style.maxHeight="180px";
                    $('#table_body').attr('width', '600');

                }
        </script>
    <center>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
    </center>
    <div style="width: 100%" id="details_reports">
        <center>
            <table width="690" border="1"  class="rpt_table" rules="all">
                <thead>
                    <tr>
                        <td colspan="8"><h4>Cut Lay Quantity Details</h4></td>
                    </tr>
                    <tr>
                        <td colspan="8" style="color:red;">This Pop-Up Will Be Perfect When Order, Production and All Procedure Follow Color or Color & Size Level.</td>
                    </tr>
                    <tr>
                        <th width="90">Buyer</th>
                        <th width="90">Job No.</th>
                        <th width="100">Style Name</th>
                        <th width="90">Order No.</th>
                        <th width="70">Int. Ref.</th>
                        <th width="70">Ship Date</th>
                        <th width="100">Item Name</th>
                        <th>Order Qty.</th>
                    </tr>
                </thead>
                <tbody>
                <tr bgcolor="white">
                    <td><?=$buyer_arr[$po_deatils[0][csf('buyer_name')]]?></td>
                    <td align="center"><?=$po_deatils[0][csf('job_no')]?></td>
                    <td ><?=$po_deatils[0][csf('style_ref_no')]?></td>
                    <td align="center"><?=$po_deatils[0][csf('po_number')]?></td>
                    <td align="center"><?=$po_deatils[0][csf('grouping')]?></td>
                    <td align="center"><?=$po_deatils[0][csf('ship_date')]?></td>
                    <td ><?=$items_library[$po_deatils[0][csf('gmts_item_id')]]?></td>
                    <td align="right"><?=$po_deatils[0][csf('po_quantity')]?></td>
                </tr>
                </tbody>
            </table>
            <br>
            <table width="620" border="1"  class="rpt_table" rules="all">
                <thead>
                    <tr>
                        <th width="30">SL No.</th>
                        <th width="80">Cutting Date</th>
                        <th width="80">Color</th>
                        <th width="70">Size</th>
                        <th width="90">Cutting Qty.</th>
                        <th width="120">Cutting Company</th>
                        <th>Location</th>
                    </tr>
                </thead>
            </table>
            <div style="max-height:180px; overflow-y:scroll; width:620px;" id="scroll_body">
                <table width="600" border="1"  class="rpt_table" rules="all" align="left" id="table_body">
                <?
                $lay_sql="SELECT c.working_company_id, a.color_id, b.size_id, to_char(c.entry_date, 'dd-mm-YYYY') as entry_date, c.location_id, sum(b.size_qty) as qnty from ppl_cut_lay_dtls a,ppl_cut_lay_bundle b, ppl_cut_lay_mst c where a.mst_id = c.id and a.id=b.dtls_id and a.status_active=1 and b.status_active=1 and a.id in ($mst_id) and b.order_id = $order_id group by c.working_company_id, c.location_id, c.entry_date, a.color_id, b.size_id order by c.entry_date desc";
                ?>
                    <tbody>
                    <?
                    $lay_data = [];
                    foreach (sql_select($lay_sql) as $data){
                        $key = $data[csf('working_company_id')]."#".$data[csf('color_id')]."#".$data[csf('size_id')]."#".$data[csf('location_id')]."#".$data[csf('entry_date')];
                        $lay_data[$key]['company'] = $company_library[$data[csf('working_company_id')]];
                        $lay_data[$key]['color'] = $colorArr[$data[csf('color_id')]];
                        $lay_data[$key]['size'] = $sizeArr[$data[csf('size_id')]];
                        $lay_data[$key]['location'] = $location_library[$data[csf('location_id')]];
                        $lay_data[$key]['date'] = $data[csf('entry_date')];
                        $lay_data[$key]['qty'] += $data[csf('qnty')];
                    }
                    $i = 1; $total_qty = 0;
                    foreach ($lay_data as $val){
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        ?>
                        <tr bgcolor="<?=$bgcolor?>">
                            <td width="30" align="center"><?=$i?></td>
                            <td width="80" align="center"><?=$val['date']?></td>
                            <td width="80" align="center"><?=$val['color']?></td>
                            <td width="70" align="center"><?=$val['size']?></td>
                            <td width="90" align="right"><?=$val['qty']?></td>
                            <td width="120" align="center"><p><?=$val['company']?></p></td>
                            <td align="center"><?=$val['location']?></td>
                        </tr>
                        <?
                        $i++;
                        $total_qty += $val['qty'];
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <table width="620"  border="1"  class="rpt_table" rules="all">
                <tfoot>
                    <tr>
                        <th width="30"</th>
                        <th width="80"></th>
                        <th width="80"></th>
                        <th width="70" align="right">Total</th>
                        <th width="90" align="right" id="total_cut_qty"><?=$total_qty;?></th>
                        <th width="120"></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </center>
    </div>
    <script>
        setFilterGrid("table_body",-1, tableFilters);
    </script>
    <?
    exit;
}

if($action=="load_cut_qc_data"){
    echo load_html_head_contents("Set Entry","../../../../", 1, 1, $unicode);
    extract($_REQUEST);
    $supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id", "supplier_name" );
    $items_library=return_library_array( "select id, item_name from lib_garment_item where  status_active=1 and is_deleted=0", "id", "item_name"  );
    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer where status_active = 1 and is_deleted = 0", "id", "buyer_name");
    $company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
    $location_library=return_library_array( "select id,location_name from  lib_location", "id", "location_name"  );
    $colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
    $sizeArr = return_library_array("select id,size_name from lib_size","id","size_name");
    $po_deatils = sql_select("select a.job_no, a.buyer_name, a.gmts_item_id, a.style_ref_no, a.style_description, b.po_number, to_char(b.shipment_date, 'dd-mm-YYYY') as ship_date,b.grouping, b.po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no = b.job_no_mst and b.id = $order_id and b.status_active = 1 and b.is_deleted = 0");
    //echo "select a.job_no, a.buyer_name, a.gmts_item_id, a.style_ref_no, a. a.style_description, b.po_number, to_char(b.shipment_date, 'dd-mm-YYYY') as ship_date,b.grouping, b.po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no = b.job_no_mst and b.id = $order_id and b.status_active = 1 and b.is_deleted = 0";
    ?>
    <script>
        var tableFilters =
            {
                col_operation: {
                    id: ["total_cut_qty_in", "total_cut_qty_out"],
                    col: [4,5],
                    operation: ["sum", "sum"],
                    write_method: ["innerHTML", "innerHTML"]
                }
            };
        function new_window()
        {
            document.getElementById('scroll_body').style.overflow="auto";
            document.getElementById('scroll_body').style.maxHeight="none";
            $('#table_body').attr('width', '690');
            $('#table_body tr:first').hide();
            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write(document.getElementById('details_reports').innerHTML);
            d.close();
            $('#table_body tr:first').show();
            document.getElementById('scroll_body').style.overflowY="scroll";
            document.getElementById('scroll_body').style.maxHeight="170px";
            $('#table_body').attr('width', '670');

        }
    </script>
    <center>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
    </center>
    <div style="width: 100%" id="details_reports">
        <center>
            <table width="690" border="1"  class="rpt_table" rules="all">
                <thead>
                <tr>
                    <td colspan="8"><h4>Cut QC Quantity Details</h4></td>
                </tr>
                <tr>
                    <td colspan="8" style="color:red;">This Pop-Up Will Be Perfect When Order, Production and All Procedure Follow Color or Color & Size Level.</td>
                </tr>
                <tr>
                    <th width="90">Buyer</th>
                    <th width="90">Job No.</th>
                    <th width="100">Style Name</th>
                    <th width="90">Order No.</th>
                    <th width="70">Int. Ref.</th>
                    <th width="70">Ship Date</th>
                    <th width="100">Item Name</th>
                    <th>Order Qty.</th>
                </tr>
                </thead>
                <tbody>
                <tr bgcolor="white">
                    <td><?=$buyer_arr[$po_deatils[0][csf('buyer_name')]]?></td>
                    <td align="center"><?=$po_deatils[0][csf('job_no')]?></td>
                    <td ><?=$po_deatils[0][csf('style_ref_no')]?></td>
                    <td align="center"><?=$po_deatils[0][csf('po_number')]?></td>
                    <td align="center"><?=$po_deatils[0][csf('grouping')]?></td>
                    <td align="center"><?=$po_deatils[0][csf('ship_date')]?></td>
                    <td ><?=$items_library[$po_deatils[0][csf('gmts_item_id')]]?></td>
                    <td align="right"><?=$po_deatils[0][csf('po_quantity')]?></td>
                </tr>
                </tbody>
            </table>
            <br>
            <table width="690" border="1"  class="rpt_table" rules="all">
                <thead>
                    <tr>
                        <th width="30" rowspan="2">SL No.</th>
                        <th rowspan="2" width="80">Cutting QC Date</th>
                        <th rowspan="2" width="80">Color</th>
                        <th rowspan="2" width="70">Size</th>
                        <th width="160" colspan="2">Cutting QC Qty.</th>
                        <th rowspan="2" width="120">Cutting QC Company</th>
                        <th rowspan="2">Location</th>
                    </tr>
                    <tr>
                        <th width="80">In-House</th>
                        <th width="80">Out Bound</th>
                    </tr>
                </thead>
            </table>
            <div style="max-height:170px; overflow-y:scroll; width:690px;" id="scroll_body">
                <table width="670" border="1"  class="rpt_table" rules="all" align="left" id="table_body">
                    <?
                    $sql_prod=sql_select("SELECT to_char(a.production_date, 'dd-mm-YYYY') as entry_date, a.production_source, a.serving_company, a.location, c.color_number_id, c.size_number_id,
					  SUM(CASE WHEN a.production_source=1 THEN b.production_qnty ELSE 0 END) as in_house_qnty,
					  SUM(CASE WHEN a.production_source=3 THEN b.production_qnty ELSE 0 END) as out_bound_qnty
					  from pro_garments_production_mst a ,pro_garments_production_dtls b,wo_po_color_size_breakdown c where a.id=b.mst_id and a.po_break_down_id=c.po_break_down_id and c.id=b.color_size_break_down_id and a.po_break_down_id = $order_id and a.production_type=1 and b.production_type=1 and a.status_active=1 and b.status_active=1 and c.status_active in(1,2,3) and c.is_deleted=0 group by a.production_date, a.production_source, a.serving_company, a.location, c.color_number_id, c.size_number_id  order by a.production_date desc");
                  ?>
                    <tbody>
                    <?
                    $cut_qc_data = [];
                    foreach ($sql_prod as $data){
                        $key = $data[csf('serving_company')]."#".$data[csf('color_number_id')]."#".$data[csf('size_number_id')]."#".$data[csf('location')]."#".$data[csf('production_date')];
                        if($data[csf('production_source')] == 1){
                            $cut_qc_data[$key]['company'] = $company_library[$data[csf('serving_company')]];
                        }elseif($data[csf('production_source')] == 3){
                            $cut_qc_data[$key]['company'] = $supplier_library[$data[csf('serving_company')]];
                        }else{
                            $cut_qc_data[$key]['company'] = "";
                        }
                        $cut_qc_data[$key]['color'] = $colorArr[$data[csf('color_number_id')]];
                        $cut_qc_data[$key]['size'] = $sizeArr[$data[csf('size_number_id')]];
                        $cut_qc_data[$key]['location'] = $location_library[$data[csf('location')]];
                        $cut_qc_data[$key]['date'] = $data[csf('entry_date')];
                        $cut_qc_data[$key]['in_qty'] += $data[csf('in_house_qnty')];
                        $cut_qc_data[$key]['out_qty'] += $data[csf('out_bound_qnty')];
                    }
                    $i = 1; $total_qty_in = 0; $total_qty_out = 0;
                    foreach ($cut_qc_data as $val){
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        ?>
                        <tr bgcolor="<?=$bgcolor?>">
                            <td width="30" align="center"><?=$i?></td>
                            <td width="80" align="center"><?=$val['date']?></td>
                            <td width="80" align="center"><?=$val['color']?></td>
                            <td width="70" align="center"><?=$val['size']?></td>
                            <td width="80" align="right"><?=$val['in_qty']?></td>
                            <td width="80" align="right"><?=$val['out_qty']?></td>
                            <td width="120" align="center"><p><?=$val['company']?></p></td>
                            <td align="center"><?=$val['location']?></td>
                        </tr>
                        <?
                        $i++;
                        $total_qty_in += $val['in_qty'];
                        $total_qty_out += $val['out_qty'];
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <table width="690"  border="1"  class="rpt_table" rules="all">
                <tfoot>
                <tr>
                    <th width="30"</th>
                    <th width="80"></th>
                    <th width="80"></th>
                    <th width="70" align="right">Total</th>
                    <th width="80" align="right" id="total_cut_qty_in"><?=$total_qty_in;?></th>
                    <th width="80" align="right" id="total_cut_qty_out"><?=$total_qty_out;?></th>
                    <th width="120"></th>
                    <th></th>
                </tr>
                </tfoot>
            </table>
        </center>
    </div>
    <script>
        setFilterGrid("table_body",-1, tableFilters);
    </script>
    <?
    exit;
}

if($action=="load_emble_issue_data"){
    echo load_html_head_contents("Set Entry","../../../../", 1, 1, $unicode);
    extract($_REQUEST);
    $supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id", "supplier_name" );
    $items_library=return_library_array( "select id, item_name from lib_garment_item where  status_active=1 and is_deleted=0", "id", "item_name"  );
    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer where status_active = 1 and is_deleted = 0", "id", "buyer_name");
    $company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
    $location_library=return_library_array( "select id,location_name from  lib_location", "id", "location_name"  );
    $colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
    $sizeArr = return_library_array("select id,size_name from lib_size","id","size_name");
    $po_deatils = sql_select("select a.job_no, a.buyer_name, a.gmts_item_id, a.style_ref_no, a.style_description, b.po_number, to_char(b.shipment_date, 'dd-mm-YYYY') as ship_date,b.grouping, b.po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no = b.job_no_mst and b.id = $order_id and b.status_active = 1 and b.is_deleted = 0");
    //echo "select a.job_no, a.buyer_name, a.gmts_item_id, a.style_ref_no, a. a.style_description, b.po_number, to_char(b.shipment_date, 'dd-mm-YYYY') as ship_date,b.grouping, b.po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no = b.job_no_mst and b.id = $order_id and b.status_active = 1 and b.is_deleted = 0";
    ?>
    <script>
        var tableFilters =
            {
                col_operation: {
                    id: ["total_cut_qty_in", "total_cut_qty_out"],
                    col: [5,6],
                    operation: ["sum", "sum"],
                    write_method: ["innerHTML", "innerHTML"]
                }
            };
        function new_window()
        {
            document.getElementById('scroll_body').style.overflow="auto";
            document.getElementById('scroll_body').style.maxHeight="none";
            $('#table_body').attr('width', '690');
            $('#table_body tr:first').hide();
            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write(document.getElementById('details_reports').innerHTML);
            d.close();
            $('#table_body tr:first').show();
            document.getElementById('scroll_body').style.overflowY="scroll";
            document.getElementById('scroll_body').style.maxHeight="170px";
            $('#table_body').attr('width', '670');

        }
    </script>
    <center>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
    </center>
    <div style="width: 100%" id="details_reports">
        <center>
            <table width="690" border="1"  class="rpt_table" rules="all">
                <thead>
                <tr>
                    <td colspan="8"><h4>Embl. Issue Quantity Details</h4></td>
                </tr>
                <tr>
                    <td colspan="8" style="color:red;">This Pop-Up Will Be Perfect When Order, Production and All Procedure Follow Color or Color & Size Level.</td>
                </tr>
                <tr>
                    <th width="90">Buyer</th>
                    <th width="90">Job No.</th>
                    <th width="100">Style Name</th>
                    <th width="90">Order No.</th>
                    <th width="70">Int. Ref.</th>
                    <th width="70">Ship Date</th>
                    <th width="100">Item Name</th>
                    <th>Order Qty.</th>
                </tr>
                </thead>
                <tbody>
                <tr bgcolor="white">
                    <td><?=$buyer_arr[$po_deatils[0][csf('buyer_name')]]?></td>
                    <td align="center"><?=$po_deatils[0][csf('job_no')]?></td>
                    <td ><?=$po_deatils[0][csf('style_ref_no')]?></td>
                    <td align="center"><?=$po_deatils[0][csf('po_number')]?></td>
                    <td align="center"><?=$po_deatils[0][csf('grouping')]?></td>
                    <td align="center"><?=$po_deatils[0][csf('ship_date')]?></td>
                    <td ><?=$items_library[$po_deatils[0][csf('gmts_item_id')]]?></td>
                    <td align="right"><?=$po_deatils[0][csf('po_quantity')]?></td>
                </tr>
                </tbody>
            </table>
            <br>
            <table width="690" border="1"  class="rpt_table" rules="all">
                <thead>
                <tr>
                    <th width="30" rowspan="2">SL No.</th>
                    <th rowspan="2" width="80">Embl. Name</th>
                    <th rowspan="2" width="80">Embl. Issue Date</th>
                    <th rowspan="2" width="80">Color</th>
                    <th rowspan="2" width="60">Size</th>
                    <th width="140" colspan="2">Embl. Issue Qty.</th>
                    <th rowspan="2" width="100">Issue Company</th>
                    <th rowspan="2">Location</th>
                </tr>
                <tr>
                    <th width="70">In-House</th>
                    <th width="70">Out Bound</th>
                </tr>
                </thead>
            </table>
            <div style="max-height:170px; overflow-y:scroll; width:690px;" id="scroll_body">
                <table width="670" border="1"  class="rpt_table" rules="all" align="left" id="table_body">
                    <?
                    $sql_prod=sql_select("SELECT to_char(a.production_date, 'dd-mm-YYYY') as entry_date, a.embel_name, a.production_source, a.serving_company, a.location, c.color_number_id, c.size_number_id,
					  SUM(CASE WHEN a.production_source=1 THEN b.production_qnty ELSE 0 END) as in_house_qnty,
					  SUM(CASE WHEN a.production_source=3 THEN b.production_qnty ELSE 0 END) as out_bound_qnty
					  from pro_garments_production_mst a ,pro_garments_production_dtls b,wo_po_color_size_breakdown c where a.id=b.mst_id and a.po_break_down_id=c.po_break_down_id and c.id=b.color_size_break_down_id and a.po_break_down_id = $order_id and a.production_type=2 and b.production_type=2 and a.status_active=1 and b.status_active=1 and c.status_active in(1,2,3) and c.is_deleted=0 group by a.embel_name,a.production_date, a.production_source, a.serving_company, a.location, c.color_number_id, c.size_number_id  order by a.production_date desc");
                    ?>
                    <tbody>
                    <?
                    $cut_qc_data = [];
                    foreach ($sql_prod as $data){
                        $key = $data[csf('serving_company')]."#".$data[csf('embel_name')]."#".$data[csf('color_number_id')]."#".$data[csf('size_number_id')]."#".$data[csf('location')]."#".$data[csf('production_date')];
                        if($data[csf('production_source')] == 1){
                            $cut_qc_data[$key]['company'] = $company_library[$data[csf('serving_company')]];
                        }elseif($data[csf('production_source')] == 3){
                            $cut_qc_data[$key]['company'] = $supplier_library[$data[csf('serving_company')]];
                        }else{
                            $cut_qc_data[$key]['company'] = "";
                        }
                        $cut_qc_data[$key]['color'] = $colorArr[$data[csf('color_number_id')]];
                        $cut_qc_data[$key]['size'] = $sizeArr[$data[csf('size_number_id')]];
                        $cut_qc_data[$key]['location'] = $location_library[$data[csf('location')]];
                        $cut_qc_data[$key]['date'] = $data[csf('entry_date')];
                        $cut_qc_data[$key]['emble_name'] = $emblishment_name_array[$data[csf('embel_name')]];
                        $cut_qc_data[$key]['in_qty'] += $data[csf('in_house_qnty')];
                        $cut_qc_data[$key]['out_qty'] += $data[csf('out_bound_qnty')];
                    }
                    $i = 1; $total_qty_in = 0; $total_qty_out = 0;
                    foreach ($cut_qc_data as $val){
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        ?>
                        <tr bgcolor="<?=$bgcolor?>">
                            <td width="30" align="center"><?=$i?></td>
                            <td width="80" align="center"><?=$val['emble_name']?></td>
                            <td width="80" align="center"><?=$val['date']?></td>
                            <td width="80" align="center"><?=$val['color']?></td>
                            <td width="60" align="center"><?=$val['size']?></td>
                            <td width="70" align="right"><?=$val['in_qty']?></td>
                            <td width="70" align="right"><?=$val['out_qty']?></td>
                            <td width="100" align="center"><p><?=$val['company']?></p></td>
                            <td align="center"><?=$val['location']?></td>
                        </tr>
                        <?
                        $i++;
                        $total_qty_in += $val['in_qty'];
                        $total_qty_out += $val['out_qty'];
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <table width="690"  border="1"  class="rpt_table" rules="all">
                <tfoot>
                <tr>
                    <th width="30"</th>
                    <th width="80"></th>
                    <th width="80"></th>
                    <th width="80"></th>
                    <th width="60" align="right">Total</th>
                    <th width="70" align="right" id="total_cut_qty_in"><?=$total_qty_in;?></th>
                    <th width="70" align="right" id="total_cut_qty_out"><?=$total_qty_out;?></th>
                    <th width="100"></th>
                    <th></th>
                </tr>
                </tfoot>
            </table>
        </center>
    </div>
    <script>
        setFilterGrid("table_body",-1, tableFilters);
    </script>
    <?
    exit;
}

if($action=="load_emble_recv_data"){
    echo load_html_head_contents("Set Entry","../../../../", 1, 1, $unicode);
    extract($_REQUEST);
    $supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id", "supplier_name" );
    $items_library=return_library_array( "select id, item_name from lib_garment_item where  status_active=1 and is_deleted=0", "id", "item_name"  );
    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer where status_active = 1 and is_deleted = 0", "id", "buyer_name");
    $company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
    $location_library=return_library_array( "select id,location_name from  lib_location", "id", "location_name"  );
    $colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
    $sizeArr = return_library_array("select id,size_name from lib_size","id","size_name");
    $po_deatils = sql_select("select a.job_no, a.buyer_name, a.gmts_item_id, a.style_ref_no, a.style_description, b.po_number, to_char(b.shipment_date, 'dd-mm-YYYY') as ship_date,b.grouping, b.po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no = b.job_no_mst and b.id = $order_id and b.status_active = 1 and b.is_deleted = 0");
    //echo "select a.job_no, a.buyer_name, a.gmts_item_id, a.style_ref_no, a. a.style_description, b.po_number, to_char(b.shipment_date, 'dd-mm-YYYY') as ship_date,b.grouping, b.po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no = b.job_no_mst and b.id = $order_id and b.status_active = 1 and b.is_deleted = 0";
    ?>
    <script>
        var tableFilters =
            {
                col_operation: {
                    id: ["total_cut_qty_in", "total_cut_qty_out"],
                    col: [5,6],
                    operation: ["sum", "sum"],
                    write_method: ["innerHTML", "innerHTML"]
                }
            };
        function new_window()
        {
            document.getElementById('scroll_body').style.overflow="auto";
            document.getElementById('scroll_body').style.maxHeight="none";
            $('#table_body').attr('width', '690');
            $('#table_body tr:first').hide();
            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write(document.getElementById('details_reports').innerHTML);
            d.close();
            $('#table_body tr:first').show();
            document.getElementById('scroll_body').style.overflowY="scroll";
            document.getElementById('scroll_body').style.maxHeight="170px";
            $('#table_body').attr('width', '670');

        }
    </script>
    <center>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
    </center>
    <div style="width: 100%" id="details_reports">
        <center>
            <table width="690" border="1"  class="rpt_table" rules="all">
                <thead>
                <tr>
                    <td colspan="8"><h4>Embl. Receive Quantity Details</h4></td>
                </tr>
                <tr>
                    <td colspan="8" style="color:red;">This Pop-Up Will Be Perfect When Order, Production and All Procedure Follow Color or Color & Size Level.</td>
                </tr>
                <tr>
                    <th width="90">Buyer</th>
                    <th width="90">Job No.</th>
                    <th width="100">Style Name</th>
                    <th width="90">Order No.</th>
                    <th width="70">Int. Ref.</th>
                    <th width="70">Ship Date</th>
                    <th width="100">Item Name</th>
                    <th>Order Qty.</th>
                </tr>
                </thead>
                <tbody>
                <tr bgcolor="white">
                    <td><?=$buyer_arr[$po_deatils[0][csf('buyer_name')]]?></td>
                    <td align="center"><?=$po_deatils[0][csf('job_no')]?></td>
                    <td ><?=$po_deatils[0][csf('style_ref_no')]?></td>
                    <td align="center"><?=$po_deatils[0][csf('po_number')]?></td>
                    <td align="center"><?=$po_deatils[0][csf('grouping')]?></td>
                    <td align="center"><?=$po_deatils[0][csf('ship_date')]?></td>
                    <td ><?=$items_library[$po_deatils[0][csf('gmts_item_id')]]?></td>
                    <td align="right"><?=$po_deatils[0][csf('po_quantity')]?></td>
                </tr>
                </tbody>
            </table>
            <br>
            <table width="690" border="1"  class="rpt_table" rules="all">
                <thead>
                <tr>
                    <th width="30" rowspan="2">SL No.</th>
                    <th rowspan="2" width="80">Embl. Name</th>
                    <th rowspan="2" width="80">Embl. Recv. Date</th>
                    <th rowspan="2" width="80">Color</th>
                    <th rowspan="2" width="60">Size</th>
                    <th width="140" colspan="2">Embl. Recv. Qty.</th>
                    <th rowspan="2" width="100">Receive Company</th>
                    <th rowspan="2">Location</th>
                </tr>
                <tr>
                    <th width="70">In-House</th>
                    <th width="70">Out Bound</th>
                </tr>
                </thead>
            </table>
            <div style="max-height:170px; overflow-y:scroll; width:690px;" id="scroll_body">
                <table width="670" border="1"  class="rpt_table" rules="all" align="left" id="table_body">
                    <?
                    $sql_prod=sql_select("SELECT to_char(a.production_date, 'dd-mm-YYYY') as entry_date, a.embel_name, a.production_source, a.serving_company, a.location, c.color_number_id, c.size_number_id,
					  SUM(CASE WHEN a.production_source=1 THEN b.production_qnty ELSE 0 END) as in_house_qnty,
					  SUM(CASE WHEN a.production_source=3 THEN b.production_qnty ELSE 0 END) as out_bound_qnty
					  from pro_garments_production_mst a ,pro_garments_production_dtls b,wo_po_color_size_breakdown c where a.id=b.mst_id and a.po_break_down_id=c.po_break_down_id and c.id=b.color_size_break_down_id and a.po_break_down_id = $order_id and a.production_type=3 and b.production_type=3 and a.status_active=1 and b.status_active=1 and c.status_active in(1,2,3) and c.is_deleted=0 group by a.production_date, a.production_source, a.embel_name, a.serving_company, a.location, c.color_number_id, c.size_number_id  order by a.production_date desc");
                    ?>
                    <tbody>
                    <?
                    $cut_qc_data = [];
                    foreach ($sql_prod as $data){
                        $key = $data[csf('serving_company')]."#".$data[csf('embel_name')]."#".$data[csf('color_number_id')]."#".$data[csf('size_number_id')]."#".$data[csf('location')]."#".$data[csf('production_date')];
                        if($data[csf('production_source')] == 1){
                            $cut_qc_data[$key]['company'] = $company_library[$data[csf('serving_company')]];
                        }elseif($data[csf('production_source')] == 3){
                            $cut_qc_data[$key]['company'] = $supplier_library[$data[csf('serving_company')]];
                        }else{
                            $cut_qc_data[$key]['company'] = "";
                        }
                        $cut_qc_data[$key]['color'] = $colorArr[$data[csf('color_number_id')]];
                        $cut_qc_data[$key]['size'] = $sizeArr[$data[csf('size_number_id')]];
                        $cut_qc_data[$key]['location'] = $location_library[$data[csf('location')]];
                        $cut_qc_data[$key]['date'] = $data[csf('entry_date')];
                        $cut_qc_data[$key]['emble_name'] = $emblishment_name_array[$data[csf('embel_name')]];
                        $cut_qc_data[$key]['in_qty'] += $data[csf('in_house_qnty')];
                        $cut_qc_data[$key]['out_qty'] += $data[csf('out_bound_qnty')];
                    }
                    $i = 1; $total_qty_in = 0; $total_qty_out = 0;
                    foreach ($cut_qc_data as $val){
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        ?>
                        <tr bgcolor="<?=$bgcolor?>">
                            <td width="30" align="center"><?=$i?></td>
                            <td width="80" align="center"><?=$val['emble_name']?></td>
                            <td width="80" align="center"><?=$val['date']?></td>
                            <td width="80" align="center"><?=$val['color']?></td>
                            <td width="60" align="center"><?=$val['size']?></td>
                            <td width="70" align="right"><?=$val['in_qty']?></td>
                            <td width="70" align="right"><?=$val['out_qty']?></td>
                            <td width="100" align="center"><p><?=$val['company']?></p></td>
                            <td align="center"><?=$val['location']?></td>
                        </tr>
                        <?
                        $i++;
                        $total_qty_in += $val['in_qty'];
                        $total_qty_out += $val['out_qty'];
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <table width="690"  border="1"  class="rpt_table" rules="all">
                <tfoot>
                <tr>
                    <th width="30"</th>
                    <th width="80"></th>
                    <th width="80"></th>
                    <th width="80"></th>
                    <th width="60" align="right">Total</th>
                    <th width="70" align="right" id="total_cut_qty_in"><?=$total_qty_in;?></th>
                    <th width="70" align="right" id="total_cut_qty_out"><?=$total_qty_out;?></th>
                    <th width="100"></th>
                    <th></th>
                </tr>
                </tfoot>
            </table>
        </center>
    </div>
    <script>
        setFilterGrid("table_body",-1, tableFilters);
    </script>
    <?
    exit;
}

if($action=="load_sewing_in_data"){
    echo load_html_head_contents("Set Entry","../../../../", 1, 1, $unicode);
    extract($_REQUEST);
    $sewing_library=return_library_array( "select id,line_name from  lib_sewing_line", "id", "line_name");
    $floor_arr=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name");
    $supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id", "supplier_name" );
    $items_library=return_library_array( "select id, item_name from lib_garment_item where  status_active=1 and is_deleted=0", "id", "item_name"  );
    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer where status_active = 1 and is_deleted = 0", "id", "buyer_name");
    $company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
    $location_library=return_library_array( "select id,location_name from  lib_location", "id", "location_name"  );
    $colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
    $sizeArr = return_library_array("select id,size_name from lib_size","id","size_name");
    $po_deatils = sql_select("select a.job_no, a.buyer_name, a.gmts_item_id, a.style_ref_no, a.style_description, b.po_number, to_char(b.shipment_date, 'dd-mm-YYYY') as ship_date,b.grouping, b.po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no = b.job_no_mst and b.id = $order_id and b.status_active = 1 and b.is_deleted = 0");
    //echo "select a.job_no, a.buyer_name, a.gmts_item_id, a.style_ref_no, a. a.style_description, b.po_number, to_char(b.shipment_date, 'dd-mm-YYYY') as ship_date,b.grouping, b.po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no = b.job_no_mst and b.id = $order_id and b.status_active = 1 and b.is_deleted = 0";
    ?>
    <script>
        var tableFilters =
            {
                col_operation: {
                    id: ["total_cut_qty_in", "total_cut_qty_out"],
                    col: [6,7],
                    operation: ["sum", "sum"],
                    write_method: ["innerHTML", "innerHTML"]
                }
            };
        function new_window()
        {
            document.getElementById('scroll_body').style.overflow="auto";
            document.getElementById('scroll_body').style.maxHeight="none";
            $('#table_body').attr('width', '690');
            $('#table_body tr:first').hide();
            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write(document.getElementById('details_reports').innerHTML);
            d.close();
            $('#table_body tr:first').show();
            document.getElementById('scroll_body').style.overflowY="scroll";
            document.getElementById('scroll_body').style.maxHeight="170px";
            $('#table_body').attr('width', '670');

        }
    </script>
    <center>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
    </center>
    <div style="width: 100%" id="details_reports">
        <center>
            <table width="690" border="1"  class="rpt_table" rules="all">
                <thead>
                <tr>
                    <td colspan="8"><h4>Sewing In Quantity Details</h4></td>
                </tr>
                <tr>
                    <td colspan="8" style="color:red;">This Pop-Up Will Be Perfect When Order, Production and All Procedure Follow Color or Color & Size Level.</td>
                </tr>
                <tr>
                    <th width="90">Buyer</th>
                    <th width="90">Job No.</th>
                    <th width="100">Style Name</th>
                    <th width="90">Order No.</th>
                    <th width="70">Int. Ref.</th>
                    <th width="70">Ship Date</th>
                    <th width="100">Item Name</th>
                    <th>Order Qty.</th>
                </tr>
                </thead>
                <tbody>
                <tr bgcolor="white">
                    <td><?=$buyer_arr[$po_deatils[0][csf('buyer_name')]]?></td>
                    <td align="center"><?=$po_deatils[0][csf('job_no')]?></td>
                    <td ><?=$po_deatils[0][csf('style_ref_no')]?></td>
                    <td align="center"><?=$po_deatils[0][csf('po_number')]?></td>
                    <td align="center"><?=$po_deatils[0][csf('grouping')]?></td>
                    <td align="center"><?=$po_deatils[0][csf('ship_date')]?></td>
                    <td ><?=$items_library[$po_deatils[0][csf('gmts_item_id')]]?></td>
                    <td align="right"><?=$po_deatils[0][csf('po_quantity')]?></td>
                </tr>
                </tbody>
            </table>
            <br>
            <table width="690" border="1"  class="rpt_table" rules="all">
                <thead>
                <tr>
                    <th width="30" rowspan="2">SL No.</th>
                    <th rowspan="2" width="80">Sewing Date</th>
                    <th rowspan="2" width="65">Sewing Floor</th>
                    <th rowspan="2" width="60">Sewing Line</th>
                    <th rowspan="2" width="70">Color</th>
                    <th rowspan="2" width="60">Size</th>
                    <th width="140" colspan="2">Sewing Qty.</th>
                    <th rowspan="2" width="100">Sewing Company</th>
                    <th rowspan="2">Location</th>
                </tr>
                <tr>
                    <th width="70">In-House</th>
                    <th width="70">Out Bound</th>
                </tr>
                </thead>
            </table>
            <div style="max-height:170px; overflow-y:scroll; width:690px;" id="scroll_body">
                <table width="670" border="1"  class="rpt_table" rules="all" align="left" id="table_body">
                    <?
                    $sql_prod=sql_select("SELECT to_char(a.production_date, 'dd-mm-YYYY') as entry_date, a.floor_id, a.sewing_line, a.production_source, a.serving_company, a.location, c.color_number_id, c.size_number_id,
					  SUM(CASE WHEN a.production_source=1 THEN b.production_qnty ELSE 0 END) as in_house_qnty,
					  SUM(CASE WHEN a.production_source=3 THEN b.production_qnty ELSE 0 END) as out_bound_qnty
					  from pro_garments_production_mst a ,pro_garments_production_dtls b,wo_po_color_size_breakdown c where a.id=b.mst_id and a.po_break_down_id=c.po_break_down_id and c.id=b.color_size_break_down_id and a.po_break_down_id = $order_id and a.production_type=4 and b.production_type=4 and a.status_active=1 and b.status_active=1 and c.status_active in(1,2,3) and c.is_deleted=0 group by a.production_date,a.floor_id, a.sewing_line, a.production_source, a.serving_company, a.location, c.color_number_id, c.size_number_id  order by a.production_date desc");
                    ?>
                    <tbody>
                    <?
                    $cut_qc_data = [];
                    foreach ($sql_prod as $data){
                        $key = $data[csf('serving_company')]."#".$data[csf('color_number_id')]."#".$data[csf('floor_id')]."#".$data[csf('size_number_id')]."#".$data[csf('location')]."#".$data[csf('production_date')];
                        if($data[csf('production_source')] == 1){
                            $cut_qc_data[$key]['company'] = $company_library[$data[csf('serving_company')]];
                        }elseif($data[csf('production_source')] == 3){
                            $cut_qc_data[$key]['company'] = $supplier_library[$data[csf('serving_company')]];
                        }else{
                            $cut_qc_data[$key]['company'] = "";
                        }
                        $cut_qc_data[$key]['color'] = $colorArr[$data[csf('color_number_id')]];
                        $cut_qc_data[$key]['size'] = $sizeArr[$data[csf('size_number_id')]];
                        $cut_qc_data[$key]['location'] = $location_library[$data[csf('location')]];
                        $cut_qc_data[$key]['floor'] = $floor_arr[$data[csf('floor_id')]];
                        $cut_qc_data[$key]['sewing_line'][$data[csf('sewing_line')]] = $sewing_library[$data[csf('sewing_line')]];
                        $cut_qc_data[$key]['date'] = $data[csf('entry_date')];
                        $cut_qc_data[$key]['in_qty'] += $data[csf('in_house_qnty')];
                        $cut_qc_data[$key]['out_qty'] += $data[csf('out_bound_qnty')];
                    }
                    $i = 1; $total_qty_in = 0; $total_qty_out = 0;
                    foreach ($cut_qc_data as $val){
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        ?>
                        <tr bgcolor="<?=$bgcolor?>">
                            <td width="30" align="center"><?=$i?></td>
                            <td width="80" align="center"><?=$val['date']?></td>
                            <td width="65" align="center"><?=$val['floor']?></td>
                            <td width="60" align="center"><?=implode(', ',$val['sewing_line'])?></td>
                            <td width="70" align="center"><?=$val['color']?></td>
                            <td width="60" align="center"><?=$val['size']?></td>
                            <td width="70" align="right"><?=$val['in_qty']?></td>
                            <td width="70" align="right"><?=$val['out_qty']?></td>
                            <td width="100" align="center"><p><?=$val['company']?></p></td>
                            <td align="center"><?=$val['location']?></td>
                        </tr>
                        <?
                        $i++;
                        $total_qty_in += $val['in_qty'];
                        $total_qty_out += $val['out_qty'];
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <table width="690"  border="1"  class="rpt_table" rules="all">
                <tfoot>
                <tr>
                    <th width="30"</th>
                    <th width="80"></th>
                    <th width="65"></th>
                    <th width="60"></th>
                    <th width="70"></th>
                    <th width="60" align="right">Total</th>
                    <th width="70" align="right" id="total_cut_qty_in"><?=$total_qty_in;?></th>
                    <th width="70" align="right" id="total_cut_qty_out"><?=$total_qty_out;?></th>
                    <th width="100"></th>
                    <th></th>
                </tr>
                </tfoot>
            </table>
        </center>
    </div>
    <script>
        setFilterGrid("table_body",-1, tableFilters);
    </script>
    <?
    exit;
}

if($action=="load_sewing_out_data"){
    echo load_html_head_contents("Set Entry","../../../../", 1, 1, $unicode);
    extract($_REQUEST);
    $sewing_library=return_library_array( "select id,line_name from  lib_sewing_line", "id", "line_name");
    $floor_arr=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name");
    $supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id", "supplier_name" );
    $items_library=return_library_array( "select id, item_name from lib_garment_item where  status_active=1 and is_deleted=0", "id", "item_name"  );
    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer where status_active = 1 and is_deleted = 0", "id", "buyer_name");
    $company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
    $location_library=return_library_array( "select id,location_name from  lib_location", "id", "location_name"  );
    $colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
    $sizeArr = return_library_array("select id,size_name from lib_size","id","size_name");
    $po_deatils = sql_select("select a.job_no, a.buyer_name, a.gmts_item_id, a.style_ref_no, a.style_description, b.po_number, to_char(b.shipment_date, 'dd-mm-YYYY') as ship_date,b.grouping, b.po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no = b.job_no_mst and b.id = $order_id and b.status_active = 1 and b.is_deleted = 0");
    //echo "select a.job_no, a.buyer_name, a.gmts_item_id, a.style_ref_no, a. a.style_description, b.po_number, to_char(b.shipment_date, 'dd-mm-YYYY') as ship_date,b.grouping, b.po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no = b.job_no_mst and b.id = $order_id and b.status_active = 1 and b.is_deleted = 0";
    ?>
    <script>
        var tableFilters =
            {
                col_operation: {
                    id: ["total_cut_qty_in", "total_cut_qty_out"],
                    col: [6,7],
                    operation: ["sum", "sum"],
                    write_method: ["innerHTML", "innerHTML"]
                }
            };
        function new_window()
        {
            document.getElementById('scroll_body').style.overflow="auto";
            document.getElementById('scroll_body').style.maxHeight="none";
            $('#table_body').attr('width', '690');
            $('#table_body tr:first').hide();
            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write(document.getElementById('details_reports').innerHTML);
            d.close();
            $('#table_body tr:first').show();
            document.getElementById('scroll_body').style.overflowY="scroll";
            document.getElementById('scroll_body').style.maxHeight="170px";
            $('#table_body').attr('width', '670');

        }
    </script>
    <center>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
    </center>
    <div style="width: 100%" id="details_reports">
        <center>
            <table width="690" border="1"  class="rpt_table" rules="all">
                <thead>
                <tr>
                    <td colspan="8"><h4>Sewing Out Quantity Details</h4></td>
                </tr>
                <tr>
                    <td colspan="8" style="color:red;">This Pop-Up Will Be Perfect When Order, Production and All Procedure Follow Color or Color & Size Level.</td>
                </tr>
                <tr>
                    <th width="90">Buyer</th>
                    <th width="90">Job No.</th>
                    <th width="100">Style Name</th>
                    <th width="90">Order No.</th>
                    <th width="70">Int. Ref.</th>
                    <th width="70">Ship Date</th>
                    <th width="100">Item Name</th>
                    <th>Order Qty.</th>
                </tr>
                </thead>
                <tbody>
                <tr bgcolor="white">
                    <td><?=$buyer_arr[$po_deatils[0][csf('buyer_name')]]?></td>
                    <td align="center"><?=$po_deatils[0][csf('job_no')]?></td>
                    <td ><?=$po_deatils[0][csf('style_ref_no')]?></td>
                    <td align="center"><?=$po_deatils[0][csf('po_number')]?></td>
                    <td align="center"><?=$po_deatils[0][csf('grouping')]?></td>
                    <td align="center"><?=$po_deatils[0][csf('ship_date')]?></td>
                    <td ><?=$items_library[$po_deatils[0][csf('gmts_item_id')]]?></td>
                    <td align="right"><?=$po_deatils[0][csf('po_quantity')]?></td>
                </tr>
                </tbody>
            </table>
            <br>
            <table width="690" border="1"  class="rpt_table" rules="all">
                <thead>
                <tr>
                    <th width="30" rowspan="2">SL No.</th>
                    <th rowspan="2" width="80">Sewing Date</th>
                    <th rowspan="2" width="65">Sewing Floor</th>
                    <th rowspan="2" width="60">Sewing Line</th>
                    <th rowspan="2" width="70">Color</th>
                    <th rowspan="2" width="60">Size</th>
                    <th width="140" colspan="2">Sewing Out Qty.</th>
                    <th rowspan="2" width="100">Sewing Company</th>
                    <th rowspan="2">Location</th>
                </tr>
                <tr>
                    <th width="70">In-House</th>
                    <th width="70">Out Bound</th>
                </tr>
                </thead>
            </table>
            <div style="max-height:170px; overflow-y:scroll; width:690px;" id="scroll_body">
                <table width="670" border="1"  class="rpt_table" rules="all" align="left" id="table_body">
                    <?
                    $sql_prod=sql_select("SELECT to_char(a.production_date, 'dd-mm-YYYY') as entry_date, a.floor_id, a.sewing_line, a.production_source, a.serving_company, a.location, c.color_number_id, c.size_number_id,
					  SUM(CASE WHEN a.production_source=1 THEN b.production_qnty ELSE 0 END) as in_house_qnty,
					  SUM(CASE WHEN a.production_source=3 THEN b.production_qnty ELSE 0 END) as out_bound_qnty
					  from pro_garments_production_mst a ,pro_garments_production_dtls b,wo_po_color_size_breakdown c where a.id=b.mst_id and a.po_break_down_id=c.po_break_down_id and c.id=b.color_size_break_down_id and a.po_break_down_id = $order_id and a.production_type=5 and b.production_type=5 and a.status_active=1 and b.status_active=1 and c.status_active in(1,2,3) and c.is_deleted=0 group by a.production_date,a.floor_id, a.sewing_line, a.production_source, a.serving_company, a.location, c.color_number_id, c.size_number_id  order by a.production_date desc");
                    ?>
                    <tbody>
                    <?
                    $cut_qc_data = [];
                    foreach ($sql_prod as $data){
                        $key = $data[csf('serving_company')]."#".$data[csf('color_number_id')]."#".$data[csf('floor_id')]."#".$data[csf('size_number_id')]."#".$data[csf('location')]."#".$data[csf('production_date')];
                        if($data[csf('production_source')] == 1){
                            $cut_qc_data[$key]['company'] = $company_library[$data[csf('serving_company')]];
                        }elseif($data[csf('production_source')] == 3){
                            $cut_qc_data[$key]['company'] = $supplier_library[$data[csf('serving_company')]];
                        }else{
                            $cut_qc_data[$key]['company'] = "";
                        }
                        $cut_qc_data[$key]['color'] = $colorArr[$data[csf('color_number_id')]];
                        $cut_qc_data[$key]['size'] = $sizeArr[$data[csf('size_number_id')]];
                        $cut_qc_data[$key]['location'] = $location_library[$data[csf('location')]];
                        $cut_qc_data[$key]['floor'] = $floor_arr[$data[csf('floor_id')]];
                        $cut_qc_data[$key]['sewing_line'][$data[csf('sewing_line')]] = $sewing_library[$data[csf('sewing_line')]];
                        $cut_qc_data[$key]['date'] = $data[csf('entry_date')];
                        $cut_qc_data[$key]['in_qty'] += $data[csf('in_house_qnty')];
                        $cut_qc_data[$key]['out_qty'] += $data[csf('out_bound_qnty')];
                    }
                    $i = 1; $total_qty_in = 0; $total_qty_out = 0;
                    foreach ($cut_qc_data as $val){
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        ?>
                        <tr bgcolor="<?=$bgcolor?>">
                            <td width="30" align="center"><?=$i?></td>
                            <td width="80" align="center"><?=$val['date']?></td>
                            <td width="65" align="center"><?=$val['floor']?></td>
                            <td width="60" align="center"><?=implode(', ',$val['sewing_line'])?></td>
                            <td width="70" align="center"><?=$val['color']?></td>
                            <td width="60" align="center"><?=$val['size']?></td>
                            <td width="70" align="right"><?=$val['in_qty']?></td>
                            <td width="70" align="right"><?=$val['out_qty']?></td>
                            <td width="100" align="center"><p><?=$val['company']?></p></td>
                            <td align="center"><?=$val['location']?></td>
                        </tr>
                        <?
                        $i++;
                        $total_qty_in += $val['in_qty'];
                        $total_qty_out += $val['out_qty'];
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <table width="690"  border="1"  class="rpt_table" rules="all">
                <tfoot>
                <tr>
                    <th width="30"</th>
                    <th width="80"></th>
                    <th width="65"></th>
                    <th width="60"></th>
                    <th width="70"></th>
                    <th width="60" align="right">Total</th>
                    <th width="70" align="right" id="total_cut_qty_in"><?=$total_qty_in;?></th>
                    <th width="70" align="right" id="total_cut_qty_out"><?=$total_qty_out;?></th>
                    <th width="100"></th>
                    <th></th>
                </tr>
                </tfoot>
            </table>
        </center>
    </div>
    <script>
        setFilterGrid("table_body",-1, tableFilters);
    </script>
    <?
    exit;
}


if($action=="load_finish_data"){
    echo load_html_head_contents("Set Entry","../../../../", 1, 1, $unicode);
    extract($_REQUEST);
    $floor_arr=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name");
    $supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id", "supplier_name" );
    $items_library=return_library_array( "select id, item_name from lib_garment_item where  status_active=1 and is_deleted=0", "id", "item_name"  );
    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer where status_active = 1 and is_deleted = 0", "id", "buyer_name");
    $company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
    $location_library=return_library_array( "select id,location_name from  lib_location", "id", "location_name"  );
    $colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
    $sizeArr = return_library_array("select id,size_name from lib_size","id","size_name");
    $po_deatils = sql_select("select a.job_no, a.buyer_name, a.gmts_item_id, a.style_ref_no, a.style_description, b.po_number, to_char(b.shipment_date, 'dd-mm-YYYY') as ship_date,b.grouping, b.po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no = b.job_no_mst and b.id = $order_id and b.status_active = 1 and b.is_deleted = 0");
    //echo "select a.job_no, a.buyer_name, a.gmts_item_id, a.style_ref_no, a. a.style_description, b.po_number, to_char(b.shipment_date, 'dd-mm-YYYY') as ship_date,b.grouping, b.po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no = b.job_no_mst and b.id = $order_id and b.status_active = 1 and b.is_deleted = 0";
    ?>
    <script>
        var tableFilters =
            {
                col_operation: {
                    id: ["total_cut_qty_in", "total_cut_qty_out"],
                    col: [5,6],
                    operation: ["sum", "sum"],
                    write_method: ["innerHTML", "innerHTML"]
                }
            };
        function new_window()
        {
            document.getElementById('scroll_body').style.overflow="auto";
            document.getElementById('scroll_body').style.maxHeight="none";
            $('#table_body').attr('width', '690');
            $('#table_body tr:first').hide();
            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write(document.getElementById('details_reports').innerHTML);
            d.close();
            $('#table_body tr:first').show();
            document.getElementById('scroll_body').style.overflowY="scroll";
            document.getElementById('scroll_body').style.maxHeight="170px";
            $('#table_body').attr('width', '670');

        }
    </script>
    <center>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
    </center>
    <div style="width: 100%" id="details_reports">
        <center>
            <table width="690" border="1"  class="rpt_table" rules="all">
                <thead>
                <tr>
                    <td colspan="8"><h4>Finishing Quantity Details</h4></td>
                </tr>
                <tr>
                    <td colspan="8" style="color:red;">This Pop-Up Will Be Perfect When Order, Production and All Procedure Follow Color or Color & Size Level.</td>
                </tr>
                <tr>
                    <th width="90">Buyer</th>
                    <th width="90">Job No.</th>
                    <th width="100">Style Name</th>
                    <th width="90">Order No.</th>
                    <th width="70">Int. Ref.</th>
                    <th width="70">Ship Date</th>
                    <th width="100">Item Name</th>
                    <th>Order Qty.</th>
                </tr>
                </thead>
                <tbody>
                <tr bgcolor="white">
                    <td><?=$buyer_arr[$po_deatils[0][csf('buyer_name')]]?></td>
                    <td align="center"><?=$po_deatils[0][csf('job_no')]?></td>
                    <td ><?=$po_deatils[0][csf('style_ref_no')]?></td>
                    <td align="center"><?=$po_deatils[0][csf('po_number')]?></td>
                    <td align="center"><?=$po_deatils[0][csf('grouping')]?></td>
                    <td align="center"><?=$po_deatils[0][csf('ship_date')]?></td>
                    <td ><?=$items_library[$po_deatils[0][csf('gmts_item_id')]]?></td>
                    <td align="right"><?=$po_deatils[0][csf('po_quantity')]?></td>
                </tr>
                </tbody>
            </table>
            <br>
            <table width="690" border="1"  class="rpt_table" rules="all">
                <thead>
                <tr>
                    <th width="30" rowspan="2">SL No.</th>
                    <th rowspan="2" width="80">Finishing Date</th>
                    <th rowspan="2" width="70">Unit Name</th>
                    <th rowspan="2" width="70">Color</th>
                    <th rowspan="2" width="70">Size</th>
                    <th width="140" colspan="2">Finishing Qty.</th>
                    <th rowspan="2" width="100">Finishing Company</th>
                    <th rowspan="2">Location</th>
                </tr>
                <tr>
                    <th width="70">In-House</th>
                    <th width="70">Out Bound</th>
                </tr>
                </thead>
            </table>
            <div style="max-height:170px; overflow-y:scroll; width:690px;" id="scroll_body">
                <table width="670" border="1"  class="rpt_table" rules="all" align="left" id="table_body">
                    <?
                    $sql_prod=sql_select("SELECT to_char(a.production_date, 'dd-mm-YYYY') as entry_date, a.floor_id, a.production_source, a.serving_company, a.location, c.color_number_id, c.size_number_id,
					  SUM(CASE WHEN a.production_source=1 THEN b.production_qnty ELSE 0 END) as in_house_qnty,
					  SUM(CASE WHEN a.production_source=3 THEN b.production_qnty ELSE 0 END) as out_bound_qnty
					  from pro_garments_production_mst a ,pro_garments_production_dtls b,wo_po_color_size_breakdown c where a.id=b.mst_id and a.po_break_down_id=c.po_break_down_id and c.id=b.color_size_break_down_id and a.po_break_down_id = $order_id and a.production_type=8 and b.production_type=8 and a.status_active=1 and b.status_active=1 and c.status_active in(1,2,3) and c.is_deleted=0 group by a.production_date, a.floor_id, a.production_source, a.serving_company, a.location, c.color_number_id, c.size_number_id  order by a.production_date desc");
                    ?>
                    <tbody>
                    <?
                    $cut_qc_data = [];
                    foreach ($sql_prod as $data){
                        $key = $data[csf('serving_company')]."#".$data[csf('color_number_id')]."#".$data[csf('floor_id')]."#".$data[csf('size_number_id')]."#".$data[csf('location')]."#".$data[csf('production_date')];
                        if($data[csf('production_source')] == 1){
                            $cut_qc_data[$key]['company'] = $company_library[$data[csf('serving_company')]];
                        }elseif($data[csf('production_source')] == 3){
                            $cut_qc_data[$key]['company'] = $supplier_library[$data[csf('serving_company')]];
                        }else{
                            $cut_qc_data[$key]['company'] = "";
                        }
                        $cut_qc_data[$key]['color'] = $colorArr[$data[csf('color_number_id')]];
                        $cut_qc_data[$key]['size'] = $sizeArr[$data[csf('size_number_id')]];
                        $cut_qc_data[$key]['location'] = $location_library[$data[csf('location')]];
                        $cut_qc_data[$key]['floor'] = $floor_arr[$data[csf('floor_id')]];
                        $cut_qc_data[$key]['date'] = $data[csf('entry_date')];
                        $cut_qc_data[$key]['in_qty'] += $data[csf('in_house_qnty')];
                        $cut_qc_data[$key]['out_qty'] += $data[csf('out_bound_qnty')];
                    }
                    $i = 1; $total_qty_in = 0; $total_qty_out = 0;
                    foreach ($cut_qc_data as $val){
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        ?>
                        <tr bgcolor="<?=$bgcolor?>">
                            <td width="30" align="center"><?=$i?></td>
                            <td width="80" align="center"><?=$val['date']?></td>
                            <td width="70" align="center" style="word-break: break-all;"><?=$val['floor']?></td>
                            <td width="70" align="center"><?=$val['color']?></td>
                            <td width="70" align="center"><?=$val['size']?></td>
                            <td width="70" align="right"><?=$val['in_qty']?></td>
                            <td width="70" align="right"><?=$val['out_qty']?></td>
                            <td width="100" align="center"><p><?=$val['company']?></p></td>
                            <td align="center"><?=$val['location']?></td>
                        </tr>
                        <?
                        $i++;
                        $total_qty_in += $val['in_qty'];
                        $total_qty_out += $val['out_qty'];
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <table width="690"  border="1"  class="rpt_table" rules="all">
                <tfoot>
                <tr>
                    <th width="30"</th>
                    <th width="80"></th>
                    <th width="70"></th>
                    <th width="70"></th>
                    <th width="70" align="right">Total</th>
                    <th width="70" align="right" id="total_cut_qty_in"><?=$total_qty_in;?></th>
                    <th width="70" align="right" id="total_cut_qty_out"><?=$total_qty_out;?></th>
                    <th width="100"></th>
                    <th></th>
                </tr>
                </tfoot>
            </table>
        </center>
    </div>
    <script>
        setFilterGrid("table_body",-1, tableFilters);
    </script>
    <?
    exit;
}

if($action=="load_ex_factory_data"){
    echo load_html_head_contents("Set Entry","../../../../", 1, 1, $unicode);
    extract($_REQUEST);
    $supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id", "supplier_name" );
    $items_library=return_library_array( "select id, item_name from lib_garment_item where  status_active=1 and is_deleted=0", "id", "item_name"  );
    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer where status_active = 1 and is_deleted = 0", "id", "buyer_name");
    $company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
    $location_library=return_library_array( "select id,location_name from  lib_location", "id", "location_name"  );
    $colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
    $sizeArr = return_library_array("select id,size_name from lib_size","id","size_name");
    $po_deatils = sql_select("select a.job_no, a.buyer_name, a.gmts_item_id, a.style_ref_no, a.style_description, b.po_number, to_char(b.shipment_date, 'dd-mm-YYYY') as ship_date,b.grouping, b.po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no = b.job_no_mst and b.id = $order_id and b.status_active = 1 and b.is_deleted = 0");
    //echo "select a.job_no, a.buyer_name, a.gmts_item_id, a.style_ref_no, a. a.style_description, b.po_number, to_char(b.shipment_date, 'dd-mm-YYYY') as ship_date,b.grouping, b.po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no = b.job_no_mst and b.id = $order_id and b.status_active = 1 and b.is_deleted = 0";
    ?>
    <script>
        var tableFilters =
            {
                col_operation: {
                    id: ["total_cut_qty"],
                    col: [4],
                    operation: ["sum"],
                    write_method: ["innerHTML"]
                }
            };
        function new_window()
        {
            document.getElementById('scroll_body').style.overflow="auto";
            document.getElementById('scroll_body').style.maxHeight="none";
            $('#table_body').attr('width', '690');
            $('#table_body tr:first').hide();
            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write(document.getElementById('details_reports').innerHTML);
            d.close();
            $('#table_body tr:first').show();
            document.getElementById('scroll_body').style.overflowY="scroll";
            document.getElementById('scroll_body').style.maxHeight="180px";
            $('#table_body').attr('width', '670');

        }
    </script>
    <center>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
    </center>
    <div style="width: 100%" id="details_reports">
        <center>
            <table width="690" border="1"  class="rpt_table" rules="all">
                <thead>
                <tr>
                    <td colspan="8"><h4>Ex-Factory Quantity Details</h4></td>
                </tr>
                <tr>
                    <td colspan="8" style="color:red;">This Pop-Up Will Be Perfect When Order, Production and All Procedure Follow Color or Color & Size Level.</td>
                </tr>
                <tr>
                    <th width="90">Buyer</th>
                    <th width="90">Job No.</th>
                    <th width="100">Style Name</th>
                    <th width="90">Order No.</th>
                    <th width="70">Int. Ref.</th>
                    <th width="70">Ship Date</th>
                    <th width="100">Item Name</th>
                    <th>Order Qty.</th>
                </tr>
                </thead>
                <tbody>
                <tr bgcolor="white">
                    <td><?=$buyer_arr[$po_deatils[0][csf('buyer_name')]]?></td>
                    <td align="center"><?=$po_deatils[0][csf('job_no')]?></td>
                    <td ><?=$po_deatils[0][csf('style_ref_no')]?></td>
                    <td align="center"><?=$po_deatils[0][csf('po_number')]?></td>
                    <td align="center"><?=$po_deatils[0][csf('grouping')]?></td>
                    <td align="center"><?=$po_deatils[0][csf('ship_date')]?></td>
                    <td ><?=$items_library[$po_deatils[0][csf('gmts_item_id')]]?></td>
                    <td align="right"><?=$po_deatils[0][csf('po_quantity')]?></td>
                </tr>
                </tbody>
            </table>
            <br>
            <table width="690" border="1"  class="rpt_table" rules="all">
                <thead>
                <tr>
                    <th width="30">SL No.</th>
                    <th width="80">Delivery Date</th>
                    <th width="70">Color</th>
                    <th width="70">Size</th>
                    <th width="80">Delivery Qty.</th>
                    <th width="80">Challan No.</th>
                    <th width="120">Delivery Company</th>
                    <th>Location</th>
                </tr>
                </thead>
            </table>
            <div style="max-height:180px; overflow-y:scroll; width:690px;" id="scroll_body">
                <table width="670" border="1"  class="rpt_table" rules="all" align="left" id="table_body">
                    <?
                    $ex_factory="select to_char(a.ex_factory_date, 'dd-mm-YYYY') entry_date, b.challan_no, b.company_id, b.location_id, d.color_number_id, d.size_number_id, c.production_qnty
                    from pro_ex_factory_mst a, pro_ex_factory_delivery_mst b, pro_ex_factory_dtls c, wo_po_color_size_breakdown d where a.delivery_mst_id = b.id and a.id = c.mst_id and c.color_size_break_down_id = d.id and a.status_active = 1 and a.is_deleted = 0
                    and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 and a.po_break_down_id = $order_id";
                    ?>
                    <tbody>
                    <?
                    $ex_factory_data = [];
                    foreach (sql_select($ex_factory) as $data){
                        $key = $data[csf('company_id')]."#".$data[csf('color_number_id')]."#".$data[csf('size_number_id')]."#".$data[csf('location_id')]."#".$data[csf('entry_date')]."#".$data[csf('challan_no')];
                        $ex_factory_data[$key]['company'] = $company_library[$data[csf('company_id')]];
                        $ex_factory_data[$key]['color'] = $colorArr[$data[csf('color_number_id')]];
                        $ex_factory_data[$key]['size'] = $sizeArr[$data[csf('size_number_id')]];
                        $ex_factory_data[$key]['location'] = $location_library[$data[csf('location_id')]];
                        $ex_factory_data[$key]['date'] = $data[csf('entry_date')];
                        $ex_factory_data[$key]['challan'] = $data[csf('challan_no')];
                        $ex_factory_data[$key]['qty'] += $data[csf('production_qnty')];
                    }
                    $i = 1; $total_qty = 0;
                    foreach ($ex_factory_data as $val){
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        ?>
                        <tr bgcolor="<?=$bgcolor?>">
                            <td width="30" align="center"><?=$i?></td>
                            <td width="80" align="center"><?=$val['date']?></td>
                            <td width="70" align="center"><?=$val['color']?></td>
                            <td width="70" align="center"><?=$val['size']?></td>
                            <td width="80" align="right"><?=$val['qty']?></td>
                            <td width="80" align="center"><p><?=$val['challan']?></p></td>
                            <td width="120" align="center"><p><?=$val['company']?></p></td>
                            <td align="center"><?=$val['location']?></td>
                        </tr>
                        <?
                        $i++;
                        $total_qty += $val['qty'];
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <table width="690"  border="1"  class="rpt_table" rules="all">
                <tfoot>
                    <tr>
                        <th width="30"></th>
                        <th width="80"></th>
                        <th width="70"></th>
                        <th width="70" align="right">Total</th>
                        <th width="80" align="right" id="total_cut_qty"><?=$total_qty;?></th>
                        <th width="80"></th>
                        <th width="120"></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </center>
    </div>
    <script>
        setFilterGrid("table_body",-1, tableFilters);
    </script>
    <?
    exit;
}

?>
