<?php
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This file for daily Woven order entry report info.
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
include('../../../../includes/common.php');
include('../../../../includes/class4/class.conditions.php');
include('../../../../includes/class4/class.reports.php');
include('../../../../includes/class4/class.yarns.php');
include('../../../../includes/class4/class.fabrics.php');
include('../../../../includes/class4/class.trims.php');

extract($_REQUEST);
 
$pc_time= add_time(date("H:i:s",time()),360);  
$pc_date = date("Y-m-d",strtotime(add_time(date("H:i:s",time()),360)));

$permission=$_SESSION['page_permission'];

//---------------------------------------------------- Start
if($action=="print_button_variable_setting")
    {
        $print_report_format_arr = return_library_array("select format_id,format_id from lib_report_template where template_name =$data and module_id=11 and report_id=265 and is_deleted=0 and status_active=1","format_id","format_id");
        echo "print_report_button_setting('".implode(',',$print_report_format_arr)."');\n";
        exit(); 
    }

if ($action=="load_drop_down_buyer")
{


	echo create_drop_down( "cbo_buyer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- All --", $selected, "" );     	 
	exit();
}

if ($action=="load_drop_down_agent")
{
	echo create_drop_down( "cbo_agent_name", 100, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (20,21))  order by buyer_name","id,buyer_name", 1, "-- All --", $selected, "" );  
	exit(); 	 
} 

if ($action=="report_generate")
{	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
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

	if(str_replace("'","",$txt_job_no)!=""){
		$jobs_arr=explode(",", $txt_job_no);
		$search_text .= " and (";
		for($i=0;$i<count($jobs_arr);$i++)
		{

			//$search_text .= " and a.job_no like '%".str_replace("'","",$txt_job_no)."'";
			if($i==0)
			{
				$search_text .= "  a.job_no_prefix_num = '".str_replace("'","",trim($jobs_arr[$i]))."'";
			}
			else{
				$search_text .= " or a.job_no_prefix_num = '".str_replace("'","",trim($jobs_arr[$i]))."'";
			}
		}
		$search_text .= " ) ";
	}

	if(str_replace("'","",$txt_job_no)!="")
	{
		//$search_text2 .= " and d.job_no like '%".str_replace("'","",$txt_job_no)."%'";
		$jobs_arr=explode(",", $txt_job_no);
		$search_text2 .= " and (";
		for($i=0;$i<count($jobs_arr);$i++)
		{

			//$search_text2 .= " and d.job_no like '%".str_replace("'","",$txt_job_no)."'";
			if($i==0)
			{
				$search_text2 .= "  d.job_no_prefix_num = '".str_replace("'","",$jobs_arr[$i])."'";
			}
			else{
				$search_text2 .= " or d.job_no_prefix_num = '".str_replace("'","",$jobs_arr[$i])."'";
			}
		}
		$search_text2 .= " ) ";
	}

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
		if($db_type==0){$date_diff="DATEDIFF(b.shipment_date,b.po_received_date) as  date_diff,";}
		else{$date_diff=" (b.shipment_date - b.po_received_date) as  date_diff,";}

		 $sql="select $date_diff a.buyer_name,a.total_set_qnty,a.job_no_prefix_num ,a.job_no,a.style_ref_no,b.po_number,a.set_smv,a.set_break_down,a.order_uom,a.gmts_item_id,b.id,a.buyer_name,(b.po_quantity*a.total_set_qnty) as po_quantity,b.po_quantity as  po_qty,b.plan_cut,b.unit_price,b.pub_shipment_date,b.po_received_date,b.insert_date,b.grouping, b.file_no,b.is_confirmed,b.inserted_by,b.po_total_price,b.details_remarks from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $search_text $buyer_id_cond $shipping_status_cond $po_cond_for_in $file_no_cond $ref_no_cond order by b.pub_shipment_date"; 
		  		
	$order_sql_result = sql_select($sql) or die(mysql_error());
	foreach($order_sql_result as $rows){
		//$order_data_arr[$rows[csf('buyer_name')]][]=$rows;
		$order_data_arr[]=$rows;
		$order_id_arr[$rows[csf('id')]]=$rows[csf('id')];
		$po_buyer_array[$rows[csf('id')]]['buyer']=$rows[csf('buyer_name')];
		$po_buyer_array[$rows[csf('id')]]['job_no']=$rows[csf('job_no')];
	}
	unset($order_sql_result);
	
	
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
		$poCon .=")";$poCon2 .=")";$poCon3 .=")";$poCon4 .=")";$poCon5 .=")";$poCon6 .=")";$poCon7 .=")";
		
		$lay_sql="SELECT b.order_id,sum(b.size_qty) as qnty from ppl_cut_lay_dtls a,ppl_cut_lay_bundle b where a.id=b.dtls_id and a.status_active=1 and b.status_active=1 $poCon6 group by b.order_id ";
		foreach(sql_select($lay_sql) as $rows)
		{
			$po_buyer=$po_buyer_array[$rows[csf('order_id')]]['buyer'];
			$job_no=$po_buyer_array[$rows[csf('order_id')]]['job_no'];
			
			$lay_cut_data[$job_no][$po_buyer] += $rows[csf('qnty')];
			$lay_cut_data_dtls[$rows[csf("order_id")]]['lay_cut_qty']+=$rows[csf("qnty")];
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
	
	$daying_qnty_array=return_library_array( "select sum(b.batch_qnty) as batch_qnty,b.po_id from pro_batch_create_mst a,pro_batch_create_dtls b, pro_fab_subprocess c where a.id=b.mst_id and a.id=c.batch_id and c.load_unload_id=2 and c.entry_form=35 and a.batch_against<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $poCon2  group by b.po_id", "po_id", "batch_qnty");
	
	
	
	$recvData=sql_select("select a.po_breakdown_id, sum(quantity) as grey_prod_qnty from order_wise_pro_details a, pro_grey_prod_entry_dtls b where a.dtls_id=b.id and a.prod_id=b.prod_id and a.entry_form =2 and a.is_deleted=0 and a.status_active=1 $poCon4 group by a.po_breakdown_id");
	foreach($recvData as $row)
	{
		$gp_qty_array[$row[csf('po_breakdown_id')]]=$row[csf('grey_prod_qnty')];
	}
	unset($recvData);

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
			$fabrics_avl_qnty_array=return_library_array( "select po_breakdown_id, sum(quantity) as quantity from order_wise_pro_details where entry_form in(17,37) $poCon5 and status_active=1 and is_deleted=0 group by po_breakdown_id", "po_breakdown_id", "quantity");
			
			
		}
		else
		{
			$fabrics_avl_qnty_array=return_library_array( "select po_breakdown_id, sum(quantity) as quantity from order_wise_pro_details where entry_form=7 $poCon5 and status_active=1 and is_deleted=0 group by po_breakdown_id
			union all
			select a.po_breakdown_id, sum(a.quantity) as quantity from order_wise_pro_details a, inv_transaction b where  a.trans_id = b.id and  a.entry_form in(37,17) $poCon5 and b.receive_basis in (2,11) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by a.po_breakdown_id", "po_breakdown_id", "quantity");
		}


	//$yarn_allocation_arr = return_library_array("select po_break_down_id, sum(qnty) as qnty from inv_material_allocation_dtls where item_category = 1 and  status_active = 1 and is_deleted=0 $poCon3 group by po_break_down_id", "po_break_down_id", "qnty");


	//$yarn_issue_array=return_library_array( "SELECT po_breakdown_id, sum(case when entry_form=3 then quantity else 0 end) - sum(case when entry_form=9 then quantity else 0 end) as quantity from order_wise_pro_details where entry_form in(3,9) $poCon5 and status_active=1 and is_deleted=0 and is_sales !=1 group by po_breakdown_id", "po_breakdown_id", "quantity"); 


					
 //Summary Data create......................................start;
	
	foreach($order_data_arr as $rows)
	{
		
		$issQtyN=$dataArrayYarnIssuesQty[$rows[csf('id')]]['qnty'];
		$buyer_summary_arr[$rows[csf('buyer_name')]]['buyer_name']=$rows[csf('buyer_name')];
		$buyer_summary_arr[$rows[csf('buyer_name')]]['po_quantity']+=$rows[csf('po_quantity')];
		$buyer_summary_arr[$rows[csf('buyer_name')]]['po_val']+=$rows[csf('po_total_price')];//new
		$buyer_summary_arr[$rows[csf('buyer_name')]]['cutting_qty']+=$cut_qnty_array[$rows[csf('id')]];	
		$buyer_summary_arr[$rows[csf('buyer_name')]]['lay_cut_qty']+=$lay_cut_data_dtls[$rows[csf("id")]]['lay_cut_qty'];		
		$buyer_summary_arr[$rows[csf('buyer_name')]]['sewing_in']+=$sewing_in_qnty_array[$rows[csf('id')]];
		$buyer_summary_arr[$rows[csf('buyer_name')]]['sewing_out']+=$sewing_out_qnty_array[$rows[csf('id')]];
		$buyer_summary_arr[$rows[csf('buyer_name')]]['finish_qty']+=$sewing_finish_qnty_array[$rows[csf('id')]];
		$buyer_summary_arr[$rows[csf('buyer_name')]]['exfactory_qty']+=$ex_qnty_array[$rows[csf('id')]];
		$buyer_summary_arr[$rows[csf('buyer_name')]]['emb_rec_qty']+=$emb_rec_qnty_array[$rows[csf('id')]];
		$buyer_summary_arr[$rows[csf('buyer_name')]]['grey_req']+=round($gf_qnty_array[$rows[csf('id')]]);
		$buyer_summary_arr[$rows[csf('buyer_name')]]['grey_prod']+=$gp_qty_array[$rows[csf('id')]];
		$buyer_summary_arr[$rows[csf('buyer_name')]]['grey_to_dye']+=round($issQtyN);
		$buyer_summary_arr[$rows[csf('buyer_name')]]['dyeing']+=round($daying_qnty_array[$rows[csf('id')]]);
		$buyer_summary_arr[$rows[csf('buyer_name')]]['avg_smv']+=$rows[csf('set_smv')]*$rows[csf('po_qty')];
		$buyer_summary_arr[$rows[csf('buyer_name')]]['freq']+=round($ff_qnty_array[$rows[csf('id')]]);
		$buyer_summary_arr[$rows[csf('buyer_name')]]['favl']+=round($fabrics_avl_qnty_array[$rows[csf('id')]]);
		$buyer_summary_arr[$rows[csf('buyer_name')]]['emb_issue']+=$emb_issue_qnty_array[$rows[csf('id')]];		
		$buyer_tot_arr['po_quantity']+=$rows[csf('po_quantity')];
		$buyer_tot_arr['po_val']+=$rows[csf('po_total_price')];//new
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
		$buyer_tot_arr['grey_prod']+=$gp_qty_array[$rows[csf('id')]];
		$buyer_tot_arr['grey_to_dye']+=round($issQtyN);
		$buyer_tot_arr['dyeing']+=round($daying_qnty_array[$rows[csf('id')]]);
		$buyer_tot_arr['freq']+=round($ff_qnty_array[$rows[csf('id')]]);
		$buyer_tot_arr['favl']+=round($fabrics_avl_qnty_array[$rows[csf('id')]]);
		$buyer_tot_arr['emb_issue']+=$emb_issue_qnty_array[$rows[csf('id')]];
	}
	//Summary Data create......................................end;	

	ob_start();	
	?>
    <br />
    <fieldset style="width:1400px;">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr><td colspan="12" align="center" style="font-size:22px;">Buyer Summary</td></tr>
        </table>
        <table width="100%" border="1" rules="all"  class="rpt_table"> 
            <thead>
                <tr style="font-size:12px"> 
                    <th width="35">Sl</th>
                    <th width="250">Buyer</th>
                    <th width="80">Order Qty</th>
                    <th width="80">Order Value</th>
                    <th width="80">Avg. SMV</th>
                    <th width="80">Fabrics Required</th> 
                    <th width="80">Fabrics Available</th>
                    <th width="80">Balance</th>
                    <th width="100">Cutting Qty</th>
                    <th width="80">Cutting %</th>
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
					 echo omitZero(number_format($smv,2)); 
					 $cut_per=($rows['cutting_qty']/$rows['po_quantity'])*100;
					 $tot_cut_per+=$cut_per;

					 $lay_cut_per=($rows['lay_cut_qty']/$rows['po_quantity'])*100;
					 $tot_lay_cut_per+=$lay_cut_per;
					 ?>
                     </td>
                     <td align="right"><? echo omitZero(number_format($rows['freq'],0));?></td>
                     <td align="right"><? echo omitZero(number_format($rows['favl'],0));?></td>
                     <td align="right"><? echo omitZero(number_format($rows['freq']-$rows['favl'],0));?></td>
                     <td align="right"><? echo omitZero(number_format($rows['lay_cut_qty']));?></td>	
                    <td align="right"><? echo omitZero(number_format($lay_cut_per,0));?></td>
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
			   $total_lay_cut_qty+=$rows['lay_cut_qty'];
			   } 
			    $tot_lay_cut_per=0;  $tot_cut_per=0;
			  $tot_lay_cut_per= ($total_lay_cut_qty/$buyer_tot_arr['po_quantity'])*100;
			   $tot_cut_per= ($buyer_tot_arr['cutting_qty']/$buyer_tot_arr['po_quantity'])*100;
			   ?>
            </tbody>
            <tfoot>
                <th colspan="2" align="right">Total </th>	
                <th align="right"><? echo omitZero(number_format($buyer_tot_arr['po_quantity'],0));?></th>	
                <th align="right"><? echo omitZero(number_format($buyer_tot_arr['po_val']));?></th>
                <th align="right"><? //echo round($buyer_tot_arr['grey_req']);?></th>	
                <th align="right"><? echo omitZero(number_format($buyer_tot_arr['freq'],0));?></th>	
                <th align="right"><? echo omitZero(number_format($buyer_tot_arr['favl'],0));?></th>	
                <th align="right"><? echo omitZero(number_format($balance_qty,0));?></th>
                <th align="right"><? echo omitZero(number_format($total_lay_cut_qty,0));?></th>	
                <th align="right"><?  echo omitZero(number_format($tot_lay_cut_per,0));?></th>	
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

	<div style="width:2350px" align="left">	
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
        <table width="2100" border="1" rules="all"  class="rpt_table"> 
            <thead>
                <tr style="font-size:12px"> 
                    <th width="35">Sl</th>	
                    <th width="100">Buyer</th> 
                    <th width="70">File No</th>
                    <th width="70">Int. Ref.</th>
                    <th width="80">Job No</th>
                    <th width="100">Order No</th>
                    <th width="100">Booking No</th>
                    <th width="100">Style No</th>
                    <th width="130">Item Name</th>
                    <th width="60">Picture</th>	
                    <th width="100">Order QTY</th>	
                    <th width="80">Ship Date</th>
                    <th width="60">SMV</th>
                    <th width="60">Fabrics Req.</th>	
                    <th width="60" title="95% GREEN 75% - 95% YELLOW">Fabrics Avl.</th>	
                    <th width="60">Balance</th>	
                    <th width="60">Cutting Qty</th>	
                    <th width="60">Cutting %</th>
                    <th width="60">Cut Qc Qty</th>	
                    <th width="60">Qc (%)</th>	
                    <th width="50">Emb. Issue</th>	
                    <th width="60">Emb. Receive</th>	
                    <th width="60">Sewing Input</th>	
                    <th width="60">Sewing Output</th>
                    <th width="60" title="95% GREEN 75% - 95% YELLOW">Finish Qty</th>
                    <th style="width:50px;">Ex- Factory</th>	
                    <th width="">Remarks</th>
                </tr>                            	
            </thead>
        </table>
        <div style="width:2370px; max-height:350px; overflow-y:scroll" id="report_div" > 
            <table id="report_tbl" width="2100" border="1"  class="rpt_table" rules="all">
              <tbody>
				<?php
                $i=1;
                foreach($order_data_arr as $row)
                {
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					$gp_per=round(($dataArrayYarnIssuesQty[$row[csf('id')]]['qnty']/$gf_qnty_array[$row[csf('id')]])*100);
					
					//$gp_per=round(($gp_qty_array[$row[csf('id')]]/$gf_qnty_array[$row[csf('id')]])*100);
					
					$cut_per=round(($cut_qnty_array[$row[csf('id')]]/$row[csf('po_quantity')])*100);
					$lay_cut_per=round(($lay_cut_data_dtls[$row[csf("id")]]['lay_cut_qty']/$row[csf('po_quantity')])*100);
                    
					$finFactoryPer=round(($sewing_finish_qnty_array[$row[csf('id')]]/$row[csf('po_quantity')])*100);
					if($finFactoryPer>94){$finfBgColor=' bgcolor="#00CC00" ';}
					elseif($finFactoryPer>74 && $finFactoryPer< 95){$finfBgColor='bgcolor="#FFCC33"';}
					else{$finfBgColor='';}
					
					$fabAvlPer=round(($fabrics_avl_qnty_array[$row[csf('id')]]/$ff_qnty_array[$row[csf('id')]])*100);
					if($fabAvlPer>94){$fabAvlBgColor=' bgcolor="#00CC00" ';}
					elseif($fabAvlPer>74 && $fabAvlPer< 95){$fabAvlBgColor='bgcolor="#FFCC33"';}
					else{$fabAvlBgColor='';}
					$itemText=array();
					$gmts_items=explode(',',$row[csf('gmts_item_id')]);
					foreach($gmts_items as $gmts_id)
					{
						$itemText[$gmts_id]=$items_library[$gmts_id];
					}
					 //var_dump($itemText);die;
					 $issQty=$dataArrayYarnIssuesQty[$row[csf('id')]]['qnty'];   
            ?>	
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="font-size:12px; cursor:pointer;" valign="middle">
                    <td width="35"><? echo $i;?></td>	
                    <td width="100"><p><? echo $buyer_library[$row[csf('buyer_name')]];?></p></td>
                    <td width="70"><p><? echo $row[csf('file_no')];?></p></td>
                    <td width="70"><p><? echo $row[csf('grouping')];?></p></td> 
                    <td width="80"><p><? echo $row[csf('job_no_prefix_num')];?></p></td>	
                    <td width="100" style="width:100px; word-wrap:break-word;"><p><? echo $row[csf('po_number')];?></p></td>	
                    <td width="100"><p>
					<?
						$html=array();
						foreach($booking_no_array[$row[csf('id')]] as $booking_no=>$booking_num){
						//$html[] ="<a href='$booking_no'>$booking_num</a>";	
						$html[] =$booking_num;	
						}
						echo implode(',',$html);
					?>
                    </p></td>	
                    <td width="100" style="width:100px; word-wrap:break-word;"><p><? echo $row[csf('style_ref_no')];?></p></td>
                    <td width="130" style="width:130px; word-wrap:break-word;"><p><? echo implode(',',$itemText);?></p></td>
                    <td width="60" style="width:60px; word-wrap:break-word;" onClick="openmypage_image('requires/order_follow_up_report_woven_controller.php?action=show_image&job_no=<? echo $row[csf("job_no")] ?>','Image View')" align="center"><img src="../../../<? echo $imge_arr[$row[csf('job_no')]];?>" height='25' width='25' /></td>	
                    <td width="100" align="right"><? echo omitZero(number_format($row[csf('po_quantity')],0));?></td>
                    <td width="80" align="center"><? $originalDate=$row[csf('pub_shipment_date')]; echo date("d-M-Y", strtotime($originalDate));?></td>
                    <td width="60" align="right"><? echo omitZero($row[csf('set_smv')]);?></td>	                    
                    <td width="60" align="right"><? echo omitZero(number_format($ff_qnty_array[$row[csf('id')]],0));?></td>	
                    <td width="60" align="right" <? echo $fabAvlBgColor;?> title="<? echo $fabAvlPer;?>%">
						<? echo omitZero(number_format($fabrics_avl_qnty_array[$row[csf('id')]]),0);?>
                    </td>	
                    <td width="60" align="right"><? echo $blance=omitZero(number_format($ff_qnty_array[$row[csf('id')]]-$fabrics_avl_qnty_array[$row[csf('id')]],0));?></td>
                    <td width="60" align="right"><? echo  omitZero(number_format($lay_cut_data_dtls[$row[csf("id")]]['lay_cut_qty']),0);//omitZero(number_format($lay_cut_data[$row[csf('job_no')]][$row[csf('buyer_name')]],0));?></td>	
                    <td width="60" align="right"><? echo omitZero(number_format($lay_cut_per));?></td>

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
                  
                    <td width=""><p><? echo $row[csf('details_remarks')];?></p></td>
                </tr>
            <?
            	
				$tot_po_quantity+=$row[csf('po_quantity')];
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
				$tot_lay_cut_qnty+=$lay_cut_data_dtls[$row[csf("id")]]['lay_cut_qty'];//$lay_cut_data[$row[csf('job_no')]][$row[csf('buyer_name')]];
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

        <table width="2100" border="1" cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all">
         	<tfoot>
                <td width="35"></td>
                <td width="100"></td>
                <td width="70"></td>	
                <td width="70"></td>	
                <td width="80"></td>	
                <td width="100"></td>
                <td width="100"></td>
                <td width="100"></td>
                <td width="130"></td>
                <td width="60"></td>	
                <td width="100"><p id="td_po_quantity_" style="width:60px; word-wrap:break-word;"><? echo number_format($tot_po_quantity,0);?></p></td>	
                <td width="80"></td>
                <td width="60" id=""><? //echo round($tot_set_smv);?></td>
                <td width="60"><p id="td_ff_qnty_" style="width:60px; word-wrap:break-word;"><? echo number_format($tot_ff_qnty,0);?></p></td>	
                <td width="60"><p id="td_fabrics_avl_qnty_" style="width:60px; word-wrap:break-word;"><? echo number_format($tot_fabrics_avl_qnty,0);?></p></td>	
                <td width="60"><p id="td_blance_" style="width:60px; word-wrap:break-word;"><? echo number_format($tot_blance,0);?></p></td>	
                <td width="60"><p id="td_lay_cut_qnty_" style="width:60px; word-wrap:break-word;"><? echo number_format($tot_lay_cut_qnty,0);?></p></td>	
                <td width="60" id=""></td>
                <td width="60"><p id="td_cut_qnty_" style="width:60px; word-wrap:break-word;"><? echo number_format($tot_cut_qnty,0);?></p></td>	
                <td width="60" id=""><? //echo round(($tot_cut_qnty/$tot_po_quantity)*100);?></td>	
                <td width="50"><p id="td_emb_issue_qnty_" style="width:50px; word-wrap:break-word;"><? echo number_format($tot_emb_issue_qnty,0);?></p></td>	
                <td width="60"><p id="td_emb_rec_qnty_" style="width:60px; word-wrap:break-word;"><? echo number_format($tot_emb_rec_qnty,0);?></p></td>	
                <td width="60"><p id="td_sewing_in_qnty_" style="width:60px; word-wrap:break-word;"><? echo number_format($tot_sewing_in_qnty,0);?></p></td>	
                <td width="60"><p id="td_sewing_out_qnty_" style="width:60px; word-wrap:break-word;"><? echo number_format($tot_sewing_out_qnty,0);?></p></td>
                <td width="60"><p id="td_sewing_finish_qnty_" style="width:60px; word-wrap:break-word;"><? echo number_format($tot_sewing_finish_qnty,0);?></p></td>
                <td width="50"><p id="td_ex_qnty_" style="width:50px; word-wrap:break-word;"><? echo number_format($tot_ex_qnty,0);?></p></td>	
               	<td width="">&nbsp;</td>	
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
	echo "$html****$filename";
	exit();	
} 
if ($action=="report_generate_2") //md mamun -crm-1449 - 16-05-2023
{	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
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

	if(str_replace("'","",$txt_job_no)!=""){
		$jobs_arr=explode(",", $txt_job_no);
		$search_text .= " and (";
		for($i=0;$i<count($jobs_arr);$i++)
		{

			//$search_text .= " and a.job_no like '%".str_replace("'","",$txt_job_no)."'";
			if($i==0)
			{
				$search_text .= "  a.job_no_prefix_num = '".str_replace("'","",trim($jobs_arr[$i]))."'";
			}
			else{
				$search_text .= " or a.job_no_prefix_num = '".str_replace("'","",trim($jobs_arr[$i]))."'";
			}
		}
		$search_text .= " ) ";
	}

	if(str_replace("'","",$txt_job_no)!="")
	{
		//$search_text2 .= " and d.job_no like '%".str_replace("'","",$txt_job_no)."%'";
		$jobs_arr=explode(",", $txt_job_no);
		$search_text2 .= " and (";
		for($i=0;$i<count($jobs_arr);$i++)
		{

			//$search_text2 .= " and d.job_no like '%".str_replace("'","",$txt_job_no)."'";
			if($i==0)
			{
				$search_text2 .= "  d.job_no_prefix_num = '".str_replace("'","",$jobs_arr[$i])."'";
			}
			else{
				$search_text2 .= " or d.job_no_prefix_num = '".str_replace("'","",$jobs_arr[$i])."'";
			}
		}
		$search_text2 .= " ) ";
	}

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
	$team_leader_lib=return_library_array( "select id,team_leader_name from lib_marketing_team where status_active=1 and is_deleted=0 order by team_name", "id", "team_leader_name"  );
	$merchand_lib=return_library_array( "select id,team_member_name from lib_mkt_team_member_info where   status_active =1 and is_deleted=0 order by team_member_name", "id", "team_member_name"  );
	$season_library = return_library_array("select id, season_name from lib_buyer_season", 'id', 'season_name');
	$brand_library=return_library_array("select id, brand_name from lib_buyer_brand where status_active=1 and is_deleted=0", 'id', 'brand_name');
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
		if($db_type==0){$date_diff="DATEDIFF(b.pub_shipment_date,b.po_received_date) as  date_diff,";}
		else{$date_diff=" (b.pub_shipment_date - b.po_received_date) as  date_diff, (b.po_received_date - b.pub_shipment_date) as  date_diff2,";}

		 $sql="select $date_diff a.buyer_name,a.total_set_qnty,a.job_no_prefix_num ,a.job_no,a.style_ref_no,b.po_number,a.set_smv,a.set_break_down,a.order_uom,a.gmts_item_id,b.id,a.buyer_name,(b.po_quantity*a.total_set_qnty) as po_quantity,b.po_quantity as  po_qty,b.plan_cut,b.unit_price,b.pub_shipment_date,b.po_received_date,b.insert_date,b.grouping, b.file_no,b.is_confirmed,b.inserted_by,b.po_total_price,b.details_remarks,a.team_leader,a.dealing_marchant,a.brand_id,a.season,a.season_year,a.season_buyer_wise,b.po_received_date ,b.insert_date,a.order_uom ,b.unit_price,a.id as job_id  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $search_text $buyer_id_cond $shipping_status_cond $po_cond_for_in $file_no_cond $ref_no_cond order by b.pub_shipment_date"; 
		  		

	$order_sql_result = sql_select($sql) or die(mysql_error());
	foreach($order_sql_result as $rows){
		//$order_data_arr[$rows[csf('buyer_name')]][]=$rows;
		$order_data_arr[]=$rows;
		$order_id_arr[$rows[csf('id')]]=$rows[csf('id')];
		$po_buyer_array[$rows[csf('id')]]['buyer']=$rows[csf('buyer_name')];
		$po_buyer_array[$rows[csf('id')]]['job_no']=$rows[csf('job_no')];
		$jobIdArr[$rows[csf('job_id')]]=$rows[csf('job_id')];
		$jobNos .="'".$rows[csf('job_no')]."',";
		$job_wise_order_no[$rows[csf('job_no_prefix_num')]][$rows[csf('id')]]=$rows[csf('id')];
	}
	unset($order_sql_result);
	
		$jobNos=rtrim($jobNos,",");
		$jobIds=implode(",",$jobIdArr);
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
		$poCon .=")";$poCon2 .=")";$poCon3 .=")";$poCon4 .=")";$poCon5 .=")";$poCon6 .=")";$poCon7 .=")";
		
		$lay_sql="SELECT b.order_id,sum(b.size_qty) as qnty from ppl_cut_lay_dtls a,ppl_cut_lay_bundle b where a.id=b.dtls_id and a.status_active=1 and b.status_active=1 $poCon6 group by b.order_id ";
		foreach(sql_select($lay_sql) as $rows)
		{
			$po_buyer=$po_buyer_array[$rows[csf('order_id')]]['buyer'];
			$job_no=$po_buyer_array[$rows[csf('order_id')]]['job_no'];			
			$lay_cut_data[$job_no][$po_buyer] += $rows[csf('qnty')];
			$lay_cut_data_dtls[$rows[csf("order_id")]]['lay_cut_qty']+=$rows[csf("qnty")];
		}
	
	
		$dataArrayYarnIssuesQty = array(); $productsIdArray = array();
		 $sqlsTrans="select  b.po_breakdown_id, sum(b.quantity) as qnty from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(16,19,61) and a.item_category=13 and a.transaction_type in(2) and b.trans_type in(2) $trans_date $poCon7 group by  b.po_breakdown_id";
		$results_trans=sql_select( $sqlsTrans );
		foreach ($results_trans as $row)
		{
			$dataArrayYarnIssuesQty[$row[csf('po_breakdown_id')]]['qnty']+=$row[csf('qnty')];
			//$productsIdArray[$row[csf('po_breakdown_id')]].=$row[csf('prod_id')].',';
		}
		$issue_return=sql_select("select a.entry_form,a.po_breakdown_id as po_id,a.prod_id,a.quantity from order_wise_pro_details a,inv_transaction b where a.trans_id=b.id and a.trans_type=4 and b.transaction_type in(4) and a.entry_form=9 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 $poCon4 ");
		foreach ($issue_return as $row)
		{
			$dataArrayYarnIssues_ret_arr[$row[csf('po_id')]]['quantity']+=$row[csf('quantity')];
		}
	
	   $daying_qnty_array=return_library_array( "select sum(b.batch_qnty) as batch_qnty,b.po_id from pro_batch_create_mst a,pro_batch_create_dtls b, pro_fab_subprocess c where a.id=b.mst_id and a.id=c.batch_id and c.load_unload_id=2 and c.entry_form=35 and a.batch_against<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $poCon2  group by b.po_id", "po_id", "batch_qnty");
	
	
	
		$recvData=sql_select("select a.po_breakdown_id, sum(quantity) as grey_prod_qnty from order_wise_pro_details a, pro_grey_prod_entry_dtls b where a.dtls_id=b.id and a.prod_id=b.prod_id and a.entry_form =2 and a.is_deleted=0 and a.status_active=1 $poCon4 group by a.po_breakdown_id");
		foreach($recvData as $row)
		{
			$gp_qty_array[$row[csf('po_breakdown_id')]]=$row[csf('grey_prod_qnty')];
		}
		unset($recvData);

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

		$ex_qnty_array=return_library_array( "select po_break_down_id, sum(ex_factory_qnty) as ex_factory_qnty from pro_ex_factory_mst where  status_active=1 $poCon3 and is_deleted=0   group by po_break_down_id", "po_break_down_id", "ex_factory_qnty");
		
	
		 
	
		if(return_field_value("auto_update","variable_settings_production","company_name=$cbo_company_name and variable_list=15 and item_category_id=2 order by id","auto_update")==2)
		{
			$fabrics_avl_qnty_array=return_library_array( "select po_breakdown_id, sum(quantity) as quantity from order_wise_pro_details where entry_form in(17,37) $poCon5 and status_active=1 and is_deleted=0 group by po_breakdown_id", "po_breakdown_id", "quantity");
			
			
		}
		else
		{
			$fabrics_avl_qnty_array=return_library_array( "select po_breakdown_id, sum(quantity) as quantity from order_wise_pro_details where entry_form=7 $poCon5 and status_active=1 and is_deleted=0 group by po_breakdown_id
			union all
			select a.po_breakdown_id, sum(a.quantity) as quantity from order_wise_pro_details a, inv_transaction b where  a.trans_id = b.id and  a.entry_form in(37,17) $poCon5 and b.receive_basis in (2,11) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by a.po_breakdown_id", "po_breakdown_id", "quantity");
		}


		$fabriccostDataArray=sql_select("select a.job_no, a.costing_per_id, a.embel_cost, a.wash_cost, a.cm_cost, a.commission, a.currier_pre_cost, a.lab_test, a.inspection, a.freight, a.comm_cost,a.inspection_percent,a.job_id,a.lab_test_percent,a.wash_cost_percent,a.embel_cost_percent,a.trims_cost,a.trims_cost_percent,a.fabric_cost,a.fabric_cost_percent  from wo_pre_cost_dtls a where a.status_active=1 and a.is_deleted=0 and a.job_id in ($jobIds)");
	 
		foreach($fabriccostDataArray as $fabRow)
		{
			 $fabriccostArray[$fabRow[csf('job_id')]]['fabric_cost']=$fabRow[csf('fabric_cost')];
			 $fabriccostArray[$fabRow[csf('job_id')]]['fabric_cost_percent']=$fabRow[csf('fabric_cost_percent')];
		  	 $fabriccostArray[$fabRow[csf('job_id')]]['trims_cost']=$fabRow[csf('trims_cost')];
			 $fabriccostArray[$fabRow[csf('job_id')]]['trims_cost_percent']=$fabRow[csf('trims_cost_percent')];
			 $fabriccostArray[$fabRow[csf('job_id')]]['costing_per_id']=$fabRow[csf('costing_per_id')];
			 $fabriccostArray[$fabRow[csf('job_id')]]['embel_cost']=$fabRow[csf('embel_cost')];
			 $fabriccostArray[$fabRow[csf('job_id')]]['embel_cost_percent']=$fabRow[csf('embel_cost_percent')];
			 $fabriccostArray[$fabRow[csf('job_id')]]['wash_cost']=$fabRow[csf('wash_cost')];
			 $fabriccostArray[$fabRow[csf('job_id')]]['wash_cost_percent']=$fabRow[csf('wash_cost_percent')];
			 $fabriccostArray[$fabRow[csf('job_id')]]['cm_cost']=$fabRow[csf('cm_cost')];
			 $fabriccostArray[$fabRow[csf('job_id')]]['commission']=$fabRow[csf('commission')];
			 $fabriccostArray[$fabRow[csf('job_id')]]['currier_pre_cost']=$fabRow[csf('currier_pre_cost')];
			 $fabriccostArray[$fabRow[csf('job_id')]]['lab_test']=$fabRow[csf('lab_test')];
			 $fabriccostArray[$fabRow[csf('job_id')]]['lab_test_percent']=$fabRow[csf('lab_test_percent')];
			 $fabriccostArray[$fabRow[csf('job_id')]]['inspection']=$fabRow[csf('inspection')];
			 $fabriccostArray[$fabRow[csf('job_id')]]['inspection_percent']=$fabRow[csf('inspection_percent')];
			 $fabriccostArray[$fabRow[csf('job_id')]]['freight']=$fabRow[csf('freight')];
			 $fabriccostArray[$fabRow[csf('job_id')]]['comm_cost']=$fabRow[csf('comm_cost')];
		}

	



			$production_data=sql_select("SELECT  a.po_break_down_id,a.company_id,sum(a.production_quantity) production_quantity,sum(a.reject_qnty) reject_qnty,a.embel_name,a.production_type from pro_garments_production_mst a, wo_po_break_down b where b.id=a.po_break_down_id  and b.job_id in ($jobIds) and a.embel_name in (1,3)  and a.production_type in (2,3) and a.status_active=1 and a.is_deleted=0  group by a.po_break_down_id,a.company_id,a.embel_name,a.production_type ");
 
		foreach($production_data as $row){

			if($row[csf('production_type')]==2 && $row[csf('embel_name')]==1){
				$po_wise_data_arr[$row[csf('po_break_down_id')]]['printing_issue']+=$row[csf('production_quantity')]-$row[csf('reject_qnty')];
			}else if($row[csf('production_type')]==3 && $row[csf('embel_name')]==1){
				$po_wise_data_arr[$row[csf('po_break_down_id')]]['printing_rcev']+=$row[csf('production_quantity')]-$row[csf('reject_qnty')];
			}else if($row[csf('production_type')]==2 && $row[csf('embel_name')]==3){
				$po_wise_data_arr[$row[csf('po_break_down_id')]]['wash_issue']+=$row[csf('production_quantity')]-$row[csf('reject_qnty')];
			}else if($row[csf('production_type')]==3 && $row[csf('embel_name')]==3){
				$po_wise_data_arr[$row[csf('po_break_down_id')]]['wash_rcev']+=$row[csf('production_quantity')]-$row[csf('reject_qnty')];
			}

		}

			//=================================== Fabric data ========================================
		
		 


		$sql_fabric = "select a.job_no ,b.po_break_down_id ,avg(b.requirment) requirment ,avg(b.rate) rate
		from  wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b
		where a.id=b.pre_cost_fabric_cost_dtls_id and a.job_no=b.job_no and a.job_id in ($jobIds)  and a.is_deleted=0  
		group by a.job_no ,b.po_break_down_id ";
		$data_arr_fabric=sql_select($sql_fabric);
		foreach($data_arr_fabric as $fab_row){
			$po_wise_data_arr[$fab_row[csf("po_break_down_id")]]['fab_cons']=$fab_row[csf("requirment")];
			$po_wise_data_arr[$fab_row[csf("po_break_down_id")]]['fab_rate']=$fab_row[csf("rate")];
		}

		//=================================== Trims data ========================================
	// 	$sql_trim_color="select  d.po_break_down_id as po_id, avg(d.cons) cons,avg(d.rate) rate
	// 	from wo_pre_cost_trim_cost_dtls c,wo_pre_cost_trim_co_cons_dtls d 
	//    where c.job_no=d.job_no and d.wo_pre_cost_trim_cost_dtls_id=c.id and c.status_active=1 and c.is_deleted=0
	// 	and d.cons>0 and c.job_id in ($jobIds) group by d.po_break_down_id";

		
			$jobNos=implode(",",array_unique(explode(",",$jobNos)));
			$condition= new condition();
			if($jobNos !=""){
					$condition->job_no("in($jobNos)");
			}

			$condition->init();
			$trim= new trims($condition);

			$trim_amount_arr=$trim->getAmountArray_precostdtlsid();
			$trim_qty_arr=$trim->getQtyArray_by_precostdtlsid();

		

			$sql_trim = "select d.po_break_down_id as po_id, c.id trim_cost_dtls_id  from wo_pre_cost_trim_cost_dtls c,wo_pre_cost_trim_co_cons_dtls d
			where c.job_no=d.job_no and d.wo_pre_cost_trim_cost_dtls_id=c.id and c.status_active=1 and c.is_deleted=0 and d.cons>0 and c.job_id in ($jobIds) 		
			group by d.po_break_down_id,c.id order by po_id";

		$trim_result=sql_select($sql_trim);
		foreach($trim_result as $row)
		{

			$po_wise_data_arr[$row[csf("po_id")]]['trims_cons']=$trim_qty_arr[$row[csf("trim_cost_dtls_id")]];
			$po_wise_data_arr[$row[csf("po_id")]]['trims_amount']+=$trim_amount_arr[$row[csf("trim_cost_dtls_id")]];

		}
		//=======================================================================
		$sql_embl="select  b.job_no, b.po_break_down_id,avg(b.requirment) cons,avg(b.rate) rate,a.emb_name
		from wo_pre_cost_embe_cost_dtls a,wo_pre_cos_emb_co_avg_con_dtls b
		where   b.pre_cost_emb_cost_dtls_id=a.id and a.emb_name in (1,2,3) and a.job_id in ($jobIds)
		group by b.job_no, b.po_break_down_id,a.emb_name ";
		$embl_result=sql_select($sql_embl);

		foreach($embl_result as $row)
		{


				if($row[csf("emb_name")]==3){
					$po_wise_data_arr[$row[csf("po_break_down_id")]]['wash_cons']+=$row[csf("cons")];
					$po_wise_data_arr[$row[csf("po_break_down_id")]]['wash_rate']+=$row[csf("rate")];
				}else{
					$po_wise_data_arr[$row[csf("po_break_down_id")]]['print_emb_cons']+=$row[csf("cons")];
					$po_wise_data_arr[$row[csf("po_break_down_id")]]['print_emb_rate']+=$row[csf("rate")];

				}
			 
		}
	
		

	ob_start();	
	?>

    
    <fieldset>

	<div style="width:4430px" align="left">	
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr><td colspan="50" align="center" style="font-size:22px;">Order Follow-Up Report</td></tr>
            <tr>
                <td colspan="50" align="center" style="font-size:16px; font-weight:bold;">
					<? echo $company_lib[$cbo_company_name];?>
                </td>
            </tr>
            <? if($txt_date_from!='' && $txt_date_to!=''){?>
            <tr>
             	<td colspan="50" align="center" style="font-size:14px;">
                    From: <? echo change_date_format($txt_date_from);?> 
                    To: <? echo change_date_format($txt_date_to);?>
                </td>
            </tr>
            <? }?>
        </table>
        <table width="4300" border="1" rules="all"  class="rpt_table"> 
            <thead>
                <tr style="font-size:12px"> 
                    <th width="35">Sl</th>	
					<th width="100">Team Leader</th> 
					<th width="100">Dealing Merchant</th> 
					<th width="80">Job No</th>
                    <th width="100">Buyer</th> 
                    <th width="70">Brand</th>
                    <th width="70">Season</th>
                    <th width="60">Season Year</th>	
					<th width="100">Style No</th>
                    <th width="100">Order No</th>
                    <th width="100">PO Insert Date</th>   
					<th width="100">PO Recv. Date</th>   
					<th width="80">Ship Date</th>     
                    <th width="130">Item Name</th>  
					<th width="60">UOM</th>    
					<th width="60">SMV</th>  
					<th width="60">Unit Price</th>                 
                    <th width="100">Order QTY</th>	
					<th width="100">Order Values</th>	
					<th width="80">Lead Time</th>	

					<th width="80">Days in Hand</th>

					<th width="100">Required Fabric QTY</th>	
					<th width="100">Fabric Budget</th>	
					<th width="100">Budget %</th>


					<th width="100">Required Trims Qty</th>	
					<th width="100">Trims Budget</th>	
					<th width="100">Trims %</th>	
					<th width="100">Printing & Embro.  Req. Qty</th>	
					<th width="100">Printing & Embro. Budget</th>	
					<th width="100">Printing & Embro. %</th>	
					<th width="100">Wash Req. Qty</th>	
					<th width="100">Washing  Budget</th>	
					<th width="100">Washing  %</th>	
					<th width="100">Lab Test Budget</th>	
					<th width="100">Lab Test %</th>	
					<th width="100">Inspection Budget</th>	
					<th width="100">Inspection %</th>	
					<th width="60">Cutting Qty</th>	
					<th width="50">Emb. Issue</th>	
					<th width="60">Emb. Receive</th>	

                    <th width="60">Print issue</th>	
                    <th width="60">Print Receive</th>
					<th width="60">Sewing Input</th>	
                    <th width="60">Sewing Output</th>	


                    <th width="60">Wash Send</th>	                  
                    <th width="60">Wash Receive</th>
					<th width="60">Packing & Finishing</th>

                    <th width="60">Garments Delivery</th>	
                    <th width="70">Short/Excess Qty</th>
                    <th >Short/Excess Qty Value</th>	
                    
                </tr>                            	
            </thead>
        </table>
        <div style="width:4320px; max-height:350px; overflow-y:scroll" id="report_div" > 
            <table id="report_tbl" width="4300" border="1"  class="rpt_table" rules="all">
              <tbody>
				<?php
                $i=1;
                foreach($order_data_arr as $row)
                {
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					$gp_per=round(($dataArrayYarnIssuesQty[$row[csf('id')]]['qnty']/$gf_qnty_array[$row[csf('id')]])*100);
					
					//$gp_per=round(($gp_qty_array[$row[csf('id')]]/$gf_qnty_array[$row[csf('id')]])*100);
					
					$cut_per=round(($cut_qnty_array[$row[csf('id')]]/$row[csf('po_quantity')])*100);
					$lay_cut_per=round(($lay_cut_data_dtls[$row[csf("id")]]['lay_cut_qty']/$row[csf('po_quantity')])*100);
                    
					$finFactoryPer=round(($sewing_finish_qnty_array[$row[csf('id')]]/$row[csf('po_quantity')])*100);
					if($finFactoryPer>94){$finfBgColor=' bgcolor="#00CC00" ';}
					elseif($finFactoryPer>74 && $finFactoryPer< 95){$finfBgColor='bgcolor="#FFCC33"';}
					else{$finfBgColor='';}
					
					$fabAvlPer=round(($fabrics_avl_qnty_array[$row[csf('id')]]/$ff_qnty_array[$row[csf('id')]])*100);
					if($fabAvlPer>94){$fabAvlBgColor=' bgcolor="#00CC00" ';}
					elseif($fabAvlPer>74 && $fabAvlPer< 95){$fabAvlBgColor='bgcolor="#FFCC33"';}
					else{$fabAvlBgColor='';}
					$itemText=array();
					$gmts_items=explode(',',$row[csf('gmts_item_id')]);
					foreach($gmts_items as $gmts_id)
					{
						$itemText[$gmts_id]=$items_library[$gmts_id];
					}
					 //var_dump($itemText);die;
					$today= change_date_format($row[csf('pub_shipment_date')],'','',1);
					$daysOnHand = datediff("d",change_date_format($row[csf('po_received_date')],'','',1),$today);

					$today2= change_date_format($row[csf('po_received_date')],'','',1);
					$daysOnHand2 = datediff("d",change_date_format($row[csf('pub_shipment_date')],'','',1),$today2);

					 $issQty=$dataArrayYarnIssuesQty[$row[csf('id')]]['qnty'];   
					$fab_req_qnty=$po_wise_data_arr[$row[csf('id')]]['fab_cons']*$row[csf('po_quantity')];
					$fab_req_val=$po_wise_data_arr[$row[csf("id")]]['fab_rate']*$fab_req_qnty;
					// $trims_req_qnty=$po_wise_data_arr[$row[csf('id')]]['trims_cons']*$row[csf('po_quantity')];
					// $trims_req_val=$po_wise_data_arr[$row[csf("id")]]['trims_rate']*$trims_req_qnty;

					$print_emb_req_qnty=($po_wise_data_arr[$row[csf('id')]]['print_emb_cons']*$row[csf('po_quantity')])/count($job_wise_order_no[$row[csf('job_no_prefix_num')]]);
					$print_emb_val=($po_wise_data_arr[$row[csf("id")]]['print_emb_rate']*$print_emb_req_qnty);

					$wash_req_qnty=$po_wise_data_arr[$row[csf('id')]]['wash_cons']*$row[csf('po_quantity')];
					$wash_val=($po_wise_data_arr[$row[csf("id")]]['wash_rate']*$wash_req_qnty)/count($job_wise_order_no[$row[csf('job_no_prefix_num')]]);

					$trims_req_qnty=$po_wise_data_arr[$row[csf('id')]]['trims_cons']/count($job_wise_order_no[$row[csf('job_no_prefix_num')]]) ;
					$trims_req_val=$po_wise_data_arr[$row[csf("id")]]['trims_amount']/count($job_wise_order_no[$row[csf('job_no_prefix_num')]]) ;
					 
					

                 ?>		
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="font-size:12px; cursor:pointer;" valign="middle">
                    <td width="35"><? echo $i;?></td>	
					<td width="100" title="<?=$row[csf('team_leader')];?>"><p><? echo $team_leader_lib[$row[csf('team_leader')]];?></p></td>
					<td width="100" title="<?=$row[csf('dealing_marchant')];?>"><p><? echo $merchand_lib[$row[csf('dealing_marchant')]];?></p></td>
					<td width="80"><p><? echo $row[csf('job_no_prefix_num')];?></p></td>	
                    <td width="100"><p><? echo $buyer_library[$row[csf('buyer_name')]];?></p></td>
                    <td width="70"><p><? echo $brand_library[$row[csf('brand_id')]];?></p></td>
                    <td width="70"><p><? echo $season_library[$row[csf('season_buyer_wise')]];?></p></td> 
					<td width="60" style="width:60px; word-wrap:break-word;"  align="center"><? echo $row[csf('season_year')];?></td>	
					<td width="100" style="width:100px; word-wrap:break-word;"><p><? echo $row[csf('style_ref_no')];?></p></td>
                    <td width="100" style="width:100px; word-wrap:break-word;"><p><? echo $row[csf('po_number')];?></p></td>	
                    <td width="100"><p><? $insertDate=$row[csf('insert_date')]; echo date("d-M-Y", strtotime($insertDate)); ?></p></td>	
					<td width="100" style="width:100px; word-wrap:break-word;"><p><? $po_receivedDate=$row[csf('po_received_date')]; echo date("d-M-Y", strtotime($po_receivedDate));?></p></td>
					<td width="80" align="center"><? $originalDate=$row[csf('pub_shipment_date')]; echo date("d-M-Y", strtotime($originalDate));?></td>
                    <td width="130" style="width:130px; word-wrap:break-word;"><p><? echo implode(',',$itemText);?></p></td>       
					<td width="60" align="center"><? echo $unit_of_measurement[$row[csf('order_uom')]];?></td>	       
					<td width="60" align="center"><? echo omitZero($row[csf('set_smv')]);?></td>	
					<td width="60" align="center"><? echo omitZero($row[csf('unit_price')]);?></td>	                       
                    <td width="100" align="right"><? echo omitZero(number_format($row[csf('po_quantity')],0));?></td>
					<td width="100" align="right"><? echo omitZero(number_format($row[csf('po_quantity')]*$row[csf('unit_price')],0));?></td>
					<td width="80" align="right"><? echo omitZero(number_format($daysOnHand,0));?></td>
					<td width="80" align="right"><? echo omitZero(number_format($daysOnHand2,0));?></td>
					
					<td width="100" align="right"><? echo omitZero(number_format($fab_req_qnty,0));?></td>
					<td width="100" align="right"><? echo omitZero(number_format($fab_req_val,0));?></td>
					<td width="100" align="right"><? echo omitZero(number_format($fabriccostArray[$row[csf('job_id')]]['fabric_cost_percent'],0));?></td>

					<td width="100" align="right"><? echo omitZero(number_format($trims_req_qnty,0));?></td>
					<td width="100" align="right"><? echo omitZero(number_format($trims_req_val,0));?></td>
					<td width="100" align="right"><? echo omitZero(number_format($fabriccostArray[$row[csf('job_id')]]['trims_cost_percent'],0));?></td>

					<td width="100" align="right"><? echo omitZero(number_format($print_emb_req_qnty,0));?></td>				
					<td width="100" align="right"><? echo omitZero(number_format($print_emb_val,2));?></td>
					<td width="100" align="right"><? echo omitZero(number_format($fabriccostArray[$row[csf('job_id')]]['embel_cost_percent'],2));?></td>

					<td width="100" align="right"><? echo omitZero(number_format($wash_req_qnty,0));?></td>
					<td width="100" align="right"><? echo omitZero(number_format($wash_val,2));?></td>
					<td width="100" align="right"><? echo omitZero(number_format($fabriccostArray[$row[csf('job_id')]]['wash_cost_percent'],2));?></td>
					<td width="100" align="right"><? echo omitZero(number_format($row[csf('po_quantity')]*$fabriccostArray[$row[csf('job_id')]]['lab_test'],2));?></td>
					<td width="100" align="right"><? echo omitZero(number_format($fabriccostArray[$row[csf('job_id')]]['lab_test_percent'],2));?></td>
					<td width="100" align="right"><? echo omitZero(number_format($row[csf('po_quantity')]*$fabriccostArray[$row[csf('job_id')]]['inspection'],2));?></td>
					<td width="100" align="right"><? echo omitZero(number_format($fabriccostArray[$row[csf('job_id')]]['inspection_percent'],2));?></td>
					<td width="60" align="right"><? echo omitZero(number_format($cut_qnty_array[$row[csf('id')]],0));?></td>	
					<td width="50" align="right"><? echo omitZero(number_format($po_wise_data_arr[$row[csf('id')]]['printing_issue'],0));?></td>	
					<td width="60" align="right"><? echo omitZero(number_format($po_wise_data_arr[$row[csf('id')]]['printing_rcev'],0));?></td>
                    <td width="60" align="right"><? echo omitZero(number_format($po_wise_data_arr[$row[csf('id')]]['printing_issue'],0));?></td>	
                    <td width="60" align="right" <? echo $fabAvlBgColor;?> title="<? echo $fabAvlPer;?>%">
						<? echo omitZero(number_format($po_wise_data_arr[$row[csf('id')]]['printing_rcev']),0);?>
                    </td>	
					<td width="60" align="right"><? echo omitZero(number_format($sewing_in_qnty_array[$row[csf('id')]],0));?></td>	
                    <td width="60" align="right"><? echo omitZero(number_format($sewing_out_qnty_array[$row[csf('id')]],0));?></td>	

                    <td width="60" align="right"><? echo omitZero(number_format($po_wise_data_arr[$row[csf('id')]]['wash_issue'],0));?></td>
                    <td width="60" align="right"><? echo  omitZero(number_format($po_wise_data_arr[$row[csf('id')]]['wash_rcev']),0);?></td>	
					<td width="60" align="right" <? echo $finfBgColor;?> title="<? echo $finFactoryPer;?>%">
                    	<? echo omitZero(number_format($sewing_finish_qnty_array[$row[csf('id')]],0));?>
                    </td>


                    <td width="60" align="right"><? echo omitZero(number_format($ex_qnty_array[$row[csf('id')]]));?></td>

                
                    <td width="70" align="right"><? echo omitZero(number_format($row[csf('po_quantity')]-$ex_qnty_array[$row[csf('id')]]));?></td>	
                
                
                   
                
					<td  align="right"><? echo omitZero(number_format(($row[csf('po_quantity')]-$ex_qnty_array[$row[csf('id')]])*$row[csf('unit_price')],0));?></td>
                  
                   
                </tr>
            <?
            	
				
				
				
				$i++;
			} 
            ?> 
         </tbody>
		</table>
        </div>

        <table width="4300" border="1" cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all">
         	<tfoot>
                <td width="35"></td>
				<td width="100"></td>
				<td width="100"></td>
				<td width="80"></td>	
                <td width="100"></td>
                <td width="70"></td>	
                <td width="70"></td>	
				<td width="60"></td>	
                <td width="100"></td>
                <td width="100"></td>
                <td width="100"></td>
				<td width="100"></td>
				<td width="80"></td>
                <td width="130"></td>
				<td width="60" id=""></td>
				<td width="60" id=""></td>
				<td width="60" id=""></td>
                <td width="100" id="tot_order_qnty"></td>	
				<td width="100" id="tot_order_val"></td>
				<td width="80" >&nbsp</td>
				<td width="80" ></td>
				<td width="100" id="tot_fab_req_qnty"></td>
				<td width="100" id="tot_fab_req_val"></td>
				<td width="100"></td>

				<td width="100" id="tot_trims_req_qnty"></td>
				<td width="100" id="tot_trims_req_val"></td>
				<td width="100"></td>
				<td width="100" id="tot_print_emb_req_qnty"></td>
				<td width="100" id="tot_print_emb_req_val"></td>
				<td width="100"></td>
				<td width="100" id="tot_wash_req_qnty"></td>
				<td width="100" id="tot_wash_req_val"></td>
				<td width="100"></td>
				<td width="100" id="tot_lab_test_val"></td>
				<td width="100"></td>
				<td width="100" id="tot_inspection_val"></td>
				<td width="100"></td>
				<td width="60" id="tot_cut_qnty"></td>
				<td width="50" id="tot_emb_issue_qnty"></td>	
				<td width="60" id="tot_emb_recv_qnty"></td>	
				<td width="60" id="tot_print_issue_qnty"></td>	
				<td width="60" id="tot_print_recv_qnty"></td>		
               
				<td width="60" id="tot_sewing_in_qnty"></td>	
                <td width="60" id="tot_sewing_out_qnty"></td>

                <td width="60" id="tot_wash_issue_qnty"></td>	
                <td width="60" id="tot_wash_recv_qnty"></td>	
				<td width="60" id="tot_packing_finish_qnty"></td>
          
					
				<td width="60" id="tot_germents_deli_qnty"></td>	
                <td width="70" id="tot_short_access_qnty"></td>	             
                <td id="tot_short_access_val" ></td>	
               
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
	echo "$html****$filename****4";
	exit();	
}
if ($action=='report_generate_stylewise') {
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	$cbo_fabric_nature=str_replace("'","",$cbo_fabric_nature);
	$txt_file_no=str_replace("'","",$txt_file_no);
	$txt_ref_no=str_replace("'","",$txt_ref_no);
	$jobNo=str_replace("'", '', $txt_job_no);
	$txt_style_ref = str_replace("'", '', $txt_style_ref);
	$txt_order_no = str_replace("'", '', $txt_order_no);
	$from_date=str_replace("'", '', $txt_date_from);
	$to_date=str_replace("'", '', $txt_date_to);
	$search_cond = '';

	$year = str_replace("'", '', $cbo_year_selection);
	$cbo_year = str_replace("'", '', $cbo_year);

	if($cbo_year != 0) {
		$year = str_replace("'", '', $cbo_year);
	}

	$from_date = ($from_date == '') ? "01-01-$year" : '';
	$to_date = ($to_date == '') ? "31-12-$year" : '';

	/*if( ($jobNo=='') && ($txt_style_ref=='') && ($txt_order_no=='') && ($txt_file_no=='') && ($txt_ref_no=='') ) {
		$year = str_replace("'", '', $cbo_year_selection);
		$cbo_year = str_replace("'", '', $cbo_year);

		if($cbo_year != 0) {
			$year = str_replace("'", '', $cbo_year);
		}

		$from_date = ($from_date == '') ? "01-01-$year" : '';
		$to_date = ($to_date == '') ? "31-12-$year" : '';
	}*/

	if($jobNo != '') {
		$search_cond = "and a.job_no like '%$jobNo'";
	}
	

	if($txt_style_ref != '') {
		$search_cond = "and a.style_ref_no = '$txt_style_ref'";
	}

	if($txt_order_no != '') {
		$search_cond = "and b.po_number = '$txt_order_no'";
	}

	if($txt_file_no != '') {
		$search_cond = "and b.file_no = '$txt_file_no'";
	}

	if($txt_ref_no != '') {
		$search_cond = "and b.grouping = '$txt_ref_no'";
	}

	$po_no_arr = array();
	$booking_no_arr = array();
	// $condition= new condition();

	if($db_type==0)
	{
		if ($from_date!="" &&  $to_date!="") $shipment_date_cond = "and b.pub_shipment_date between '".change_date_format($from_date, "yyyy-mm-dd", "-")."' and '".change_date_format($to_date, "yyyy-mm-dd", "-")."'"; else $shipment_date_cond = "";
	}
	if($db_type==2)
	{
		if ($from_date!="" &&  $to_date!="") $shipment_date_cond = "and b.pub_shipment_date between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'"; else $shipment_date_cond = "";
	}

	$items_library=return_library_array( "select id, item_name from lib_garment_item where status_active=1 and is_deleted=0", "id", "item_name");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0", 'id', 'buyer_name');
	$brand_library=return_library_array("select id, brand_name from lib_buyer_brand where status_active=1 and is_deleted=0", 'id', 'brand_name');
	$price_quatation_dia=return_library_array("select gmts_sizes,dia_width from wo_pri_quo_fab_co_avg_con_dtls where wo_pri_quo_fab_co_dtls_id=$pri_fab_cost_dtls_id", "gmts_sizes","dia_width");
	$color_library=return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$trim_group_library= return_library_array("select id, item_name from lib_item_group", 'id', 'item_name');
	$supplier_library = return_library_array("select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0", 'id', 'supplier_name');
	$size_library = return_library_array("select id, size_name from lib_size", 'id', 'size_name');
	$season_library = return_library_array("select id, season_name from lib_buyer_season", 'id', 'season_name');

	$mst_query = "select a.id, b.id as po_break_down_id, a.buyer_name, a.brand_id, a.season_buyer_wise, a.season_year, a.style_ref_no, a.style_description, b.grouping, a.job_no
			from wo_po_details_master a, wo_po_break_down b
			where a.company_name = $cbo_company_name $search_cond and b.job_no_mst = a.job_no and a.status_active=1 and b.status_active=1";
	// echo $mst_query;
	$mst_result = sql_select($mst_query);

	foreach ($mst_result as $row) {
		$po_no_arr[] = $row[csf('po_break_down_id')];
		$txt_job_no = "'" . $row[csf('job_no')] . "'";
	}
	$po_no_arr = array_unique($po_no_arr);

	$po_no_str = implode(',', $po_no_arr);

	$fabric_sql = "select a.id as fabric_cost_id, a.job_no, a.fabric_description, a.item_number_id, a.uom, b.color_number_id, a.avg_cons, a.rate, a.amount, a.avg_finish_cons, a.lib_yarn_count_deter_id, a.job_plan_cut_qty, b.dia_width, b.total, a.composition, b.po_break_down_id, b.item_size
	from wo_pre_cost_fabric_cost_dtls a
    join wo_pre_cos_fab_co_avg_con_dtls b on a.id = b.pre_cost_fabric_cost_dtls_id
	where a.job_no = $txt_job_no and a.status_active=1 and a.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 and b.cons <> 0
	group by a.id, a.job_no, a.fabric_description, a.item_number_id, a.uom, b.color_number_id, a.avg_cons, a.rate, a.amount, a.avg_finish_cons, a.lib_yarn_count_deter_id, a.job_plan_cut_qty, b.dia_width, b.total, a.composition, b.po_break_down_id, b.item_size";

	// echo $fabric_sql;
	$fabric_rowspan = array();
	$fabric_result = sql_select($fabric_sql);

	$fabric_arr = array();
	foreach ($fabric_result as $row) {
		$fabric_arr[$row[csf('fabric_description')]][$row[csf('color_number_id')]]['fabric_description'] = $row[csf('fabric_description')];
		$fabric_arr[$row[csf('fabric_description')]][$row[csf('color_number_id')]]['color_number_id'] = $row[csf('color_number_id')];
		$fabric_arr[$row[csf('fabric_description')]][$row[csf('color_number_id')]]['width'] = $row[csf('dia_width')];
		$fabric_arr[$row[csf('fabric_description')]][$row[csf('color_number_id')]]['uom'] = $row[csf('uom')];
		$fabric_arr[$row[csf('fabric_description')]][$row[csf('color_number_id')]]['composition'] = $row[csf('composition')];
		$fabric_arr[$row[csf('fabric_description')]][$row[csf('color_number_id')]]['po_break_down_id'] = $row[csf('po_break_down_id')];
		$fabric_arr[$row[csf('fabric_description')]][$row[csf('color_number_id')]]['lib_yarn_count_deter_id'] = $row[csf('lib_yarn_count_deter_id')];
		$fabric_arr[$row[csf('fabric_description')]][$row[csf('color_number_id')]]['fabric_cost_id'] = $row[csf('fabric_cost_id')];
		$fabric_arr[$row[csf('fabric_description')]][$row[csf('color_number_id')]]['cutable_width'] = $row[csf('item_size')];
	}

	foreach ($fabric_arr as $fabric_desc => $fabricDescArr) {
		foreach ($fabricDescArr as $row) {
			$fabric_rowspan[$row['fabric_description']]++;
		}
	}

	$condition= new condition();
	if(str_replace("'","",$txt_job_no) !=''){
		$condition->job_no("=$txt_job_no");
	}

	$condition->init();
	$fabric= new fabric($condition);
	$trim= new trims($condition);

	$fabric_qty_arr=$fabric->getQtyArray_by_OrderFabriccostidGmtscolorAndDiaWidth_knitAndwoven_greyAndfinish();

	$fabric_req_qty = array();

	if($fabric_qty_arr['woven']['finish'] > 0) {
		foreach($fabric_qty_arr['woven']['finish'] as $poId=>$poArr){
			foreach($poArr as $fabricCostId=>$fabricCostArr){
				foreach ($fabricCostArr as $colorId => $colorArr) {
					foreach ($colorArr as $diaWidth => $diaArr) {
						foreach ($diaArr as $reqQty) {
							$fabric_req_qty[$fabricCostId][$colorId]['req_qty'] += $reqQty;
							$fabric_req_qty[$fabricCostId][$colorId]['dia_width'] = $diaWidth;
						}
					}
				}
			}
		}
	}

	$booking_sql = "select b.pre_cost_fabric_cost_dtls_id, b.po_break_down_id, b.color_number_id, a.id AS booking_id, a.fin_fab_qnty, a.grey_fab_qnty, a.rate, a.amount, a.adjust_qty, a.remark, a.dia_width, a.pre_cost_remarks, a.copmposition, a.booking_no, b.item_size, c.supplier_id
		from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_mst c
 		where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.po_break_down_id=b.po_break_down_id and a.gmts_color_id=b.color_number_id and a.dia_width=b.dia_width and a.po_break_down_id in($po_no_str) and b.cons>0 and a.booking_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no=c.booking_no
		group by b.pre_cost_fabric_cost_dtls_id,b.po_break_down_id,b.color_number_id,a.id,a.fin_fab_qnty,a.grey_fab_qnty,a.rate,a.amount, a.adjust_qty,a.remark,a.dia_width,a.pre_cost_remarks, a.copmposition, a.booking_no, b.item_size, c.supplier_id";

	// echo $booking_sql;
	$booking_result = sql_select($booking_sql);

	$booking_arr = array();

	foreach ($booking_result as $row) {
		if( isset($booking_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('color_number_id')]]) ) {
			// $booking_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('color_number_id')]]['receive_qty'] += $row[csf('grey_fab_qnty')];
			$booking_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('color_number_id')]]['booking_qty'] += $row[csf('grey_fab_qnty')];
		} else {
			// $booking_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('color_number_id')]]['receive_qty'] = $row[csf('grey_fab_qnty')];
			$booking_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('color_number_id')]]['booking_qty'] += $row[csf('grey_fab_qnty')];
		}
		$booking_no_array[] = $row[csf('booking_no')];
		$booking_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('color_number_id')]]['cutable_width'] = $row[csf('item_size')];
		// $supplierId = $row[csf('supplier_id')];
	}

	$booking_no_array = array_unique($booking_no_array);
	$booking_no_str = implode('\',\'', $booking_no_array);

	$trims_sql = "select id, job_no, trim_group, description, brand_sup_ref, remark, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, status_active, seq
	from wo_pre_cost_trim_cost_dtls
	where job_no=".$txt_job_no;
	//echo $trims_sql;
	$trims_result=sql_select($trims_sql);

	// echo $trims_sql;

	$trim_amount_arr=$trim->getAmountArray_by_jobAndPrecostdtlsid();
	$trim_qty_arr=$trim->getQtyArray_by_jobAndPrecostdtlsid();
	$totTrim=0;
	$trims_arr=array();
	foreach($trims_result as $row){
	    $trim_qty=$trim_qty_arr[$row[csf("job_no")]][$row[csf("id")]];
		$trim_amount=$trim_amount_arr[$row[csf("job_no")]][$row[csf("id")]];
		$summary_data[trims_cost_job]+=$trim_amount;

		$trims_arr[$row[csf('id')]]['trim_group']=$row[csf('trim_group')];
		$trims_arr[$row[csf('id')]]['description']=$row[csf('description')];
		$trims_arr[$row[csf('id')]]['brand_sup_ref']=$row[csf('brand_sup_ref')];
		$trims_arr[$row[csf('id')]]['remark']=$row[csf('remark')];
		$trims_arr[$row[csf('id')]]['cons_uom']=$row[csf('cons_uom')];
		$trims_arr[$row[csf('id')]]['cons_dzn_gmts']=$row[csf('cons_dzn_gmts')];
		$trims_arr[$row[csf('id')]]['rate']=$row[csf('rate')];
		$trims_arr[$row[csf('id')]]['amount']=$row[csf('amount')];
		$trims_arr[$row[csf('id')]]['apvl_req']=$row[csf('apvl_req')];
		$trims_arr[$row[csf('id')]]['nominated_supp']=$row[csf('nominated_supp_multi')];
		$trims_arr[$row[csf('id')]]['tot_cons']=$trim_qty;
		$trims_arr[$row[csf('id')]]['tot_amount']=$trim_amount;
		$totTrim+=$row[csf('cons_dzn_gmts')];
	}


	$trims_booking_sql = "select a.pre_cost_fabric_cost_dtls_id, b.description, b.item_color, sum (b.requirment) as cons, a.trim_group, a.booking_no, c.supplier_id
    				from wo_booking_dtls a, wo_trim_book_con_dtls b, wo_booking_mst c
   					where a.id = b.wo_trim_booking_dtls_id and a.booking_no=c.booking_no and a.booking_no = b.booking_no and a.job_no = $txt_job_no and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0
					group by a.pre_cost_fabric_cost_dtls_id, b.description, b.item_color, a.trim_group, a.booking_no, c.supplier_id";
	// echo $trims_booking_sql;
	$trims_booking_result=sql_select($trims_booking_sql);
	$trims_booking_no = '';

	$trims_booking_arr=array();
	foreach($trims_booking_result as $row) {
		if( isset($trims_booking_arr[$row[csf('trim_group')]]) ) {
			$trims_booking_arr[$row[csf('trim_group')]]['cons'] += $row[csf('cons')];
		} else {
			$trims_booking_arr[$row[csf('trim_group')]]['cons'] = $row[csf('cons')];
		}
		$trims_booking_no = $row[csf('booking_no')];

		$trims_booking_arr[$row[csf('trim_group')]]['supplier_id'] = $row[csf('supplier_id')];
		$trims_booking_arr[$row[csf('trim_group')]]['description'] = $row[csf('description')];
	}

	$trims_receive_sql = "select a.booking_no, b.order_id as po_id, b.item_group_id, b.item_description, b.gmts_color_id, b.item_color, sum (c.quantity) as receive_qnty, b.prod_id, a.supplier_id
    		from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c
   			where a.id = b.mst_id and a.booking_no = b.booking_no and b.id = c.dtls_id and b.trans_id = c.trans_id and c.entry_form = 24 and a.entry_form = 24 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and c.trans_type = 1 and c.po_breakdown_id in ($po_no_str)
			group by a.booking_no, b.order_id, b.item_group_id, b.item_description, b.gmts_color_id, b.item_color, b.prod_id, a.supplier_id";

	// echo $trims_receive_sql;
	$trims_receive_result=sql_select($trims_receive_sql);
	$prod_id_arr = array();
	$trims_receive_arr=array();
	foreach($trims_receive_result as $row) {
		if( isset($trims_receive_arr[$row[csf('item_group_id')]]) ) {
			$trims_receive_arr[$row[csf('item_group_id')]]['receive_qty'] += $row[csf('receive_qnty')];
		} else {
			$trims_receive_arr[$row[csf('item_group_id')]]['receive_qty'] = $row[csf('receive_qnty')];
		}
		$trims_receive_arr[$row[csf('item_group_id')]]['supplier_id'] = $row[csf('supplier_id')];
		$prod_id_arr[] = $row[csf('prod_id')];
	}

	$prod_id_str = implode(',', array_unique($prod_id_arr));

	/*$receive_sql = "select a.recv_number, a.receive_date, a.receive_basis, b.cons_quantity as receive_qty, a.buyer_id, case when a.receive_basis = 2 then a.booking_no else null end as wo_number, case when a.receive_basis = 1 then a.booking_no else null end as pi_number, c.fabric_color_id, c.grey_fab_qnty, d.lib_yarn_count_deter_id, a.challan_no, b.batch_lot
    		from inv_transaction b, inv_receive_master a, wo_booking_dtls c, wo_pre_cost_fabric_cost_dtls d
   			where a.booking_no in('$booking_no_str') and a.booking_no=c.booking_no and c.pre_cost_fabric_cost_dtls_id = d.id and a.id = b.mst_id and a.entry_form = 17 and a.item_category = 3 and b.item_category = 3 and b.transaction_type = 1 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0
		group by a.receive_date, a.receive_basis, a.buyer_id, a.booking_no, a.recv_number, c.fabric_color_id, c.grey_fab_qnty, d.lib_yarn_count_deter_id, a.challan_no, b.batch_lot, b.cons_quantity";*/

	$receive_sql = "select c.receive_basis, c.booking_without_order as without_order, a.fabric_description_id, c.store_id, a.uom, a.rate, a.color_id, b.po_breakdown_id as po_id, b.quantity, b.order_amount as amount, b.trans_type
  		from inv_receive_master c, pro_finish_fabric_rcv_dtls a, order_wise_pro_details b
 		where a.trans_id = b.trans_id and c.id = a.mst_id and b.trans_type = 1 and b.entry_form = 17 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.item_category = 3 and c.entry_form = 17 and (b.po_breakdown_id in ($po_no_str))";
	// echo $receive_sql;
	$receive_result = sql_select($receive_sql);

	$receive_arr = array();
	$batch_lot = '';
	foreach ($receive_result as $row) {
		if( isset($receive_arr[$row[csf('fabric_description_id')]][$row[csf('color_id')]]) ) {
			$receive_arr[$row[csf('fabric_description_id')]][$row[csf('color_id')]]['receive_qty'] += $row[csf('quantity')];
		} else {
			$receive_arr[$row[csf('fabric_description_id')]][$row[csf('color_id')]]['receive_qty'] = $row[csf('quantity')];
		}
		$batch_lot = $row[csf('batch_lot')];
	}

	$trims_issue_sql = "select id, item_group_id, item_description, item_color_id, item_size, order_id, item_order_id, issue_qnty
    					from inv_trims_issue_dtls
   						where prod_id in($prod_id_str) and status_active = '1' and is_deleted = '0'";


   	$trims_issue_result = sql_select($trims_issue_sql);

	$trims_issue_arr = array();
	$batch_lot = '';
	foreach ($trims_issue_result as $row) {
		if( isset($trims_issue_arr[$row[csf('item_group_id')]]) ) {
			$trims_issue_arr[$row[csf('item_group_id')]]['issue_qty'] += $row[csf('issue_qnty')];
		} else {
			$trims_issue_arr[$row[csf('item_group_id')]]['issue_qty'] = $row[csf('issue_qnty')];
		}
	}

	/*$issue_sql = "select a.prod_id, a.batch_id, a.body_part_id, c.order_id,
		         (case
		             when a.floor_id is null or a.floor_id = 0 then 0
		             else a.floor_id
		          end)
            		floor_id,
			        nvl (a.rack, 0) rack,
			         (case when a.room is null or a.room = 0 then 0 else a.room end) room,
			         (case when a.self is null or a.self = 0 then 0 else a.self end) self,
			         (case
			             when a.bin_box is null or a.bin_box = 0 then 0
			             else a.bin_box
			          end)
            		bin_box,
			        sum (case when a.transaction_type = 2 then cons_quantity end)
			            as issue_qnty,
			          d.color_id
    			from inv_issue_master b, inv_transaction a, inv_wvn_finish_fab_iss_dtls c, pro_batch_create_mst d
   				where b.entry_form = 19 and b.id = a.mst_id and a.id = c.trans_id and a.status_active = 1 and a.is_deleted = 0 and a.item_category = 3
         		and a.transaction_type = 2 and c.status_active = 1 and c.is_deleted = 0 and a.batch_id = d.id and d.booking_no in('$booking_no_str') and b.status_active = 1 and b.is_deleted = 0 and b.company_id = 1
				group by a.prod_id, a.batch_id, a.body_part_id, a.floor_id, a.room, a.rack, a.self, a.bin_box, d.color_id, c.order_id";*/

	$issue_sql = "select b.po_breakdown_id, a.color as color_id, a.item_size, a.detarmination_id, b.quantity, c.cons_rate as rate, b.entry_form, b.trans_type, d.fabric_description
  		from product_details_master a, order_wise_pro_details b, inv_transaction c, wo_pre_cost_fabric_cost_dtls d
 		where a.id = b.prod_id and b.trans_id = c.id and a.detarmination_id=d.lib_yarn_count_deter_id and d.job_no=$txt_job_no and item_category_id = 3 and a.entry_form = 0 and b.entry_form in (19, 202, 209, 258) and b.trans_type in (2, 3, 4, 6, 5) and c.transaction_type in (2, 3, 4, 6, 5) and a.status_active = 1 and b.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and (b.po_breakdown_id in ($po_no_str))";
	// echo $issue_sql;
	$issue_result = sql_select($issue_sql);

	$issue_arr = array();

	foreach ($issue_result as $row) {
		if( $row[csf('entry_form')]==19 && $row[csf('trans_type')] == 2 ) {
			if( isset($issue_arr[$row[csf('fabric_description')]][$row[csf('color_id')]]) ) {
				$issue_arr[$row[csf('fabric_description')]][$row[csf('color_id')]]['issue_qty'] += $row[csf('quantity')];
			} else {
				$issue_arr[$row[csf('fabric_description')]][$row[csf('color_id')]]['issue_qty'] = $row[csf('quantity')];
			}
		}
	}

	$color_sql = "select a.job_no, (a.job_quantity * a.total_set_qnty) as job_quantity, (c.po_quantity * a.total_set_qnty) as po_quantity, c.po_total_price, d.size_number_id, d.color_number_id, d.order_quantity, d.order_total
    	from wo_po_details_master a
        left join wo_po_break_down c 
        	on a.job_no = c.job_no_mst and c.is_deleted = 0 and c.status_active = 1
        left join wo_po_color_size_breakdown d
            on c.job_no_mst = d.job_no_mst and c.id = d.po_break_down_id and d.is_deleted = 0 and d.status_active = 1
   		where a.is_deleted = 0 and a.status_active = 1 and a.company_name = $cbo_company_name and to_char (a.insert_date, 'yyyy') = 2020 and a.job_no = $txt_job_no";

   	// echo $color_sql;
   	$color_result = sql_select($color_sql);

   	$color_arr = array();
   	$size_arr = array();

   	foreach ($color_result as $row) {
   		// $color_arr[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['size_number_id'] = $row[csf('size_number_id')];
   		$color_arr[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['order_quantity'] += $row[csf('order_quantity')];

   		$size_arr[] = $row[csf('size_number_id')];
   	}

   	$size_arr = array_unique($size_arr);

   	ob_start();
	?>
	<div id="report_div">
		<style>
			.heading th, .heading td {
				font-size: 12pt;
			}
		</style>
		<div>
			<table width="80%" cellpadding="0" cellspacing="0" style="margin: 30px auto;">
				<thead class="heading">
					<th>Buyer:</th>
					<td><?php echo $buyer_library[$mst_result[0][csf('buyer_name')]]; ?></td>
					<th>Brand:</th>
					<td><?php echo $brand_library[$mst_result[0][csf('brand_id')]]; ?></td>
					<th>Season:</th>
					<td><?php echo $season_library[$mst_result[0][csf('season_buyer_wise')]]; ?></td>
					<th>Season Year:</th>
					<td><?php echo $mst_result[0][csf('season_year')] ? $mst_result[0][csf('season_year')] : ''; ?></td>
					<th>Style Ref:</th>
					<td><?php echo $mst_result[0][csf('style_ref_no')]; ?></td>
					<th>Style Description:</th>
					<td><?php echo $mst_result[0][csf('style_description')]; ?></td>
					<th>Master Style/Int. Ref:</th>
					<td align="left"><?php echo $mst_result[0][csf('grouping')]; ?></td>
					<th>Job no:</th>
					<td><?php echo $mst_result[0][csf('job_no')]; ?></td>
				</thead>
			</table>
		</div>
		<div>
			<fieldset style="width: 500px;">
		        <table width="100%" cellpadding="0" cellspacing="0">
		            <tr><td colspan="12" align="center" style="font-size:22px;">Color Size Breakdown</td></tr>
		        </table>
		        <table width="100%" border="1" rules="all"  class="rpt_table"> 
		            <thead>
		            	<tr style="font-size:12px"> 
					        <th>color/size</th>
					        <?php
					        	foreach($size_arr as $size) {
					        		?>
					        			<th><?php echo $size_library[$size]; ?></th>
					        		<?php
					        	}
					        ?>
			            	<th>Total</th>
			            </tr>
			            <?php
			            	foreach ($color_arr as $colorId => $colorArr) {
			            		$colorTotal = 0;
			            		?>
			            		<tr>
			            			<td><?php echo $color_library[$colorId]; ?></td>
			            		<?php
			            		foreach ($colorArr as $value) {
			            				$ordQty = $value['order_quantity'];
			            				$colorTotal += $ordQty;
			            			?>
			            				<td><?php echo $ordQty; ?></td>
			            			<?php
			            		}
			            		?>
			            		<td><?php echo $colorTotal; ?></td>
			            		</tr>
			            		<?php
			            	}
			            ?>
		            </thead>
		        </table>
		    </fieldset>
		</div>
	    <div style="margin-top: 20px;">
	    	<fieldset style="width: 1000px;">
		        <table width="100%" cellpadding="0" cellspacing="0">
		            <tr><td colspan="12" align="center" style="font-size:22px;">Fabric Details</td></tr>
		        </table>
		        <table width="100%" border="1" rules="all"  class="rpt_table"> 
		            <thead>
		                <tr style="font-size:12px"> 
		                    <th>Fabrication</th>
							<th>GMT Color</th>
							<th>Width/Cutable Width</th>
							<th>UOM</th>
							<th>Required Qty</th>
							<th>Booking Qty</th>
							<th>Received Qty</th>
							<th>Issue Qty</th>
							<th>Issue Balance</th>
		               </tr>
		            </thead>
		            <tbody>
		            	<?php
		            		foreach ($fabric_arr as $fabDesc => $fabDescArr) {
		            			$rowspanCount = 0;
		            			foreach ($fabDescArr as $colorId => $value) {
		            					$receiveQty = $receive_arr[$value['lib_yarn_count_deter_id']][$value['color_number_id']]['receive_qty'];
		            					$issueQty = $issue_arr[$fabDesc][$colorId]['issue_qty'];
		            					$cutableWidth = $value['cutable_width'];
	            					?>
		            				<tr>
		            					<?php
		            						if($rowspanCount == 0) {
		            					?>
		            					<td rowspan="<?php echo $fabric_rowspan[$fabDesc]; ?>"><?php echo $value['fabric_description']; ?></td>
		            					<?php
		            						}
		            					?>
		            					<td><?php echo $color_library[$colorId]; ?></td>
		            					<td><?php echo $fabric_req_qty[$value['fabric_cost_id']][$value['color_number_id']]['dia_width'] . '/' . $cutableWidth; ?></td>
		            					<td><?php echo $unit_of_measurement[$value['uom']]; ?></td>
		            					<td><?php echo $fabric_req_qty[$value['fabric_cost_id']][$value['color_number_id']]['req_qty']; ?></td>
		            					<td><?php echo $booking_arr[$value['fabric_cost_id']][$colorId]['booking_qty']; ?></td>
		            					<td><?php echo $receiveQty; ?></td>
		            					<td><?php echo $issueQty; ?></td>
		            					<td><?php echo ($receiveQty - $issueQty); ?></td>
		            				</tr>
	            					<?php
	            					$rowspanCount++;
		            			}
		            		}
		            	?>
		            </tbody>
		        </table>
		    </fieldset>
	    </div>
	    <div style="margin-top: 20px;">
	    	<fieldset style="width: 1000px;">
		        <table width="100%" cellpadding="0" cellspacing="0">
		            <tr><td colspan="12" align="center" style="font-size:22px;">Accessories Details</td></tr>
		        </table>
		        <table width="100%" border="1" rules="all"  class="rpt_table"> 
		            <thead>
		                <tr style="font-size:12px"> 
		                    <th>Item Group</th>
							<th>Description</th>
							<th>Supplier Name</th>
							<th>Cons/Dzn</th>
							<th>UOM</th>
							<th>Required Qty</th>
							<th>Booking Qty</th>
							<th>Receive Qty</th>
							<th>Issue Qt</th>
							<th>Balance</th>
		               </tr>
		            </thead>
		            <tbody>
		            	<?php
		            		foreach ($trims_arr as $row) {
		            			$receiveQty = $trims_receive_arr[$row['trim_group']]['receive_qty'];
		            			$issueQty = $trims_issue_arr[$row['trim_group']]['issue_qty'];
		            			?>
		            			<tr>
		            				<td><?php echo $trim_group_library[$row['trim_group']]; ?></td>
		            				<td><?php echo $trims_booking_arr[$row['trim_group']]['description']; ?></td>
		            				<td><?php echo $supplier_library[$trims_booking_arr[$row['trim_group']]['supplier_id']]; ?></td>
		        					<td><?php echo number_format($row['cons_dzn_gmts'],4); ?></td>
		            				<td><?php echo $unit_of_measurement[$row['cons_uom']]; ?></td>
		        					<td><?php echo number_format($row['tot_cons'],4); ?></td>
		        					<td><?php echo $trims_booking_arr[$row['trim_group']]['cons']; ?></td>
		        					<td><?php echo $receiveQty; ?></td>
		        					<td><?php echo $issueQty; ?></td>
		        					<td><?php echo ($receiveQty - $issueQty); ?></td>
		            			</tr>
		            			<?php
		            		}
		            	?>
		            </tbody>
		            <tfoot>
		            </tfoot>
		        </table>
		    </fieldset>
	    </div>
	</div>
	<?php

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

		 $sql="select $date_diff a.buyer_name,a.total_set_qnty,a.job_no_prefix_num ,a.job_no,a.style_ref_no,b.po_number,a.set_smv,a.set_break_down,a.order_uom,a.gmts_item_id,b.id,a.buyer_name,(b.po_quantity*a.total_set_qnty) as po_quantity,b.po_quantity as  po_qty,b.plan_cut,b.unit_price,b.pub_shipment_date,b.po_received_date,b.insert_date,b.grouping, b.file_no,b.is_confirmed,b.inserted_by,b.po_total_price,b.details_remarks from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $search_text $buyer_id_cond $shipping_status_cond $po_cond_for_in $file_no_cond $ref_no_cond order by b.pub_shipment_date"; //echo $sql;
		 
			
		 
		 		
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
                    <th width="80">Order QTY</th>
                    <th width="80">Order Value</th>
                    <th width="80">Avg. SMV</th>
                    <!-- <th width="80">Grey Required</th> -->
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
                <th align="right"></th>
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
                    <th rowspan="2" width="100">Order QTY</th>	
                    <th rowspan="2" width="80">Ship Date</th>
                    <th rowspan="2" width="60">SMV</th>	
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
                    <th rowspan="2" width="60">Sewing Input</th>	
                    <th rowspan="2" width="60">Sewing Output</th>
                    <th rowspan="2" width="60" title="95% GREEN 75% - 95% YELLOW">Finish Qty</th>
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
            <table id="report_tbl" width="<? echo $tbWith;?>" border="1"  class="rpt_table" rules="all">
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
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="font-size:12px; cursor:pointer;" valign="middle">
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
                    <td width="60" onClick="openmypage_image('requires/order_follow_up_report_woven_controller.php?action=show_image&job_no=<? echo $row[csf("job_no")] ?>','Image View')" align="center"><img src="../../../<? echo $imge_arr[$row[csf('job_no')]];?>" height='25' width='30' /></td>	
                    <td width="100" align="right"><? echo omitZero(number_format($row[csf('po_quantity')],0));?></td>
                    <td width="80" align="center"><? $originalDate=$row[csf('pub_shipment_date')]; echo date("d-M-Y", strtotime($originalDate));?></td>
                    <td width="60" align="right"><? echo omitZero($row[csf('set_smv')]);?></td>	
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
            	
				$tot_po_quantity+=$row[csf('po_quantity')];
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
                <td width="100"><p id="td_po_quantity" style="width:60px; word-wrap:break-word;"><? echo number_format($tot_po_quantity,0);?></p></td>	
                <td width="80"></td>
                <td width="60" id=""></td>
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

?>
