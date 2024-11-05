<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');


$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_party_name")
{
	$data=explode('_',$data);
	$rpt_type=$data[2];
	if($data[1]==2)
	{
		echo create_drop_down( "cbo_party_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $selected, "","","","","","",5 ); 
	}
	else if($data[1]==1)
	{	
		  echo create_drop_down( "cbo_party_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0  and comp.id=$data[0] order by comp.company_name","id,company_name", 1, "-- Select Party --",$data[0], "","","","","","",5 );   

		/*   $party_arr=return_library_array("select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0  and comp.id=$data[0] order by comp.company_name", "id","company_name");
		$value = 1;
		if(count($party_arr)==1){
			$value =0;
		}
		echo create_drop_down( "cbo_party_name", 130, $party_arr,"",1, "-- Select Party --", $value, "load_drop_down( 'requires/knitting_bill_issue_controller', this.value, 'load_drop_down_party_location', 'party_location_td');","","","","","",5 );  */
	}
	else
	{
		//echo $rpt_type.'D';
		if($rpt_type==4) $rpt_typecond=" where party_type=21";else $rpt_typecond=" where  party_type=20";
		//echo "select sup.id, sup.supplier_name from lib_supplier sup,lib_supplier_tag_company b where sup.status_active=1 and sup.is_deleted=0 and b.supplier_id=sup.id and b.tag_company='$data[0]' $supplier_cond and sup.id in (select  supplier_id from  lib_supplier_party_type $rpt_typecond) order by supplier_name";
		
		echo create_drop_down( "cbo_party_name", 130, "select sup.id, sup.supplier_name from lib_supplier sup,lib_supplier_tag_company b where sup.status_active=1 and sup.is_deleted=0 and b.supplier_id=sup.id and b.tag_company='$data[0]' $supplier_cond and sup.id in (select  supplier_id from  lib_supplier_party_type $rpt_typecond) order by supplier_name", "id,supplier_name", 1, "-- Select supplier --", $selected, "","","","","","",5 );
	
		
	}
	exit();
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );     	 
	exit();
}
if($action=="load_drop_down_location")
{
	$data=explode('_',$data);
	$source=$data[0];
	$company_id=$data[1];
	if($source==1 || $source==2)
	{
		echo create_drop_down( "location_id", 130, "select id,location_name from lib_location where company_id='$company_id' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/knitting_and_dyeing_bill_details_report_controller',this.value+'_'+document.getElementById('cbo_party_source').value+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_floor', 'floor_td' );" );
	}
	else{
		echo create_drop_down( "location_id", 130, $blank_array,"", 1, "-- Select Location --", $selected, "",0,"","","","",5);
	}
}
if($action=="load_drop_down_floor")
{
	$data=explode('_',$data);
	$location=$data[0];
	$source=$data[1];
	$company_id=$data[2];
	if($source==1 || $source==2)
	{
		
		echo create_drop_down( "floor_id", 130,"select id,floor_name from lib_prod_floor where company_id=$company_id and production_process=2 and location_id=$location and status_active =1 and is_deleted=0 order by floor_name" ,"id,floor_name", 1, "-- Select Floor --", $selected, "",0,"","","","",5);
	}
	else{
		echo create_drop_down( "floor_id", 130, $blank_array,"", 1, "-- Select Floor --", $selected, "",0,"","","","",5);
	}
}


 
if($action=="report_generate")
{ 
	$process = array( &$_POST );

	// echo "<pre>";
	// print_r($process);
	// echo "</pre>";
	// die;
	
	extract(check_magic_quote_gpc( $process ));
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$company_arr=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name");
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier ", "id", "supplier_name");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0","id","color_name");
	$location_arr = return_library_array("select id,location_name from lib_location where   status_active =1 and is_deleted=0 order by location_name","id","location_name");
	$imge_arr=return_library_array( "select master_tble_id, image_location from  common_photo_library where file_type=1",'master_tble_id','image_location');
	$floor_arr = return_library_array("select id,floor_name from lib_prod_floor where  status_active =1 and is_deleted=0 order by floor_name", 'id', 'floor_name');
	$cbo_company_id= str_replace("'","",$cbo_company_id);
	$cbo_party_source = str_replace("'","",trim($cbo_party_source));
	$start_date=str_replace("'","",trim($txt_date_from));
	$end_date=str_replace("'","",trim($txt_date_to));
	$cbo_party_name=str_replace("'","",trim($cbo_party_name));
	$report_type=str_replace("'","",trim($report_type));
	$txt_bill_no=str_replace("'","",trim($txt_bill_no));
	$txt_challan_no=str_replace("'","",trim($txt_challan_no));
	$txt_job_no=str_replace("'","",trim($txt_job_no));
	$txt_job_id=str_replace("'","",trim($txt_job_id));
	$txt_order_no=str_replace("'","",trim($txt_order_no));
	$txt_order_id=str_replace("'","",trim($txt_order_id));
	$job_order_type=str_replace("'","",trim($cbo_job_order_type));
	
	$supplier_con='';
	$parti_con='';
	$company_con='';
	$bill_cond='';
	//echo $txt_bill_no.'--'.$txt_job_no;die;
	if(!empty($cbo_party_name) )
	{
		$supplier_con=" and a.supplier_id=$cbo_party_name";
		$parti_con=" and a.party_id=$cbo_party_name";
	}

	if(!empty($cbo_company_id) && $cbo_company_id!=0)
	{
		$company_con=" and  a.company_id=$cbo_company_id";
	}

	if(!empty($txt_bill_no))
	{
		$bill_cond=" and  a.prefix_no_num like '%$txt_bill_no%'";
	}
	if(!empty($txt_challan_no))
	{
		$challan_cond=" and  b.challan_no like '%$txt_challan_no%'";
	}

	if(!empty($floor_id))
	{
		$floor_con=" and b.floor_id=$floor_id";
	}
	$wo_no_con='';
	$search_field_cond='';
	$wo_date_con='';
	$date_cond ='';

	if($db_type==0)
	{
		if ($start_date!="" &&  $end_date!="") $date_cond  = "and a.receive_date  between '".change_date_format($start_date, "yyyy-mm-dd", "-")."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'"; else $date_cond ="";
		if ($start_date!="" &&  $end_date!="") $date_cond2  = "and a.receive_date  between '".change_date_format($start_date, "yyyy-mm-dd", "-")."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'"; else $date_cond ="";
	}
	if($db_type==2)
	{
		if ($start_date!="" &&  $end_date!="") $date_cond  = "and a.bill_date  between '".change_date_format($start_date, "yyyy-mm-dd", "-",1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-",1)."'"; else $date_cond ="";
		if ($start_date!="" &&  $end_date!="") $date_cond2  = "and a.receive_date  between '".change_date_format($start_date, "yyyy-mm-dd", "-",1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-",1)."'"; else $date_cond2 ="";
	}
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$roll_no_arr=return_library_array( "select id, no_of_roll from  pro_grey_prod_entry_dtls",'id','no_of_roll');
	$bodyPartTypeArr = return_library_array( "select id, body_part_type from lib_body_part",'id','body_part_type');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	/*$product_dtls_arr=return_library_array( "select id,product_name_details as const_comp from  product_details_master 
	union all select id,const_comp from lib_subcon_charge",'id','const_comp');*/
	 $prod_lib="select id,product_name_details as const_comp,null as yarn_description from  product_details_master 
	union all select id,const_comp,yarn_description from lib_subcon_charge";
	$prod_lib_array = sql_select($prod_lib);
		foreach ($prod_lib_array as $row) {
		$product_dtls_arr[$row[csf('id')]] = $row[csf('const_comp')];
		if($row[csf('yarn_description')]!="")
		{
	  	 $product_compsition_dtls_arr[$row[csf('id')]] =$row[csf('yarn_description')];
		}
	}
	//print_r($product_dtls_arr);
	
	

	if($job_order_type==1){
		if($txt_job_id !==""){
			$job_cond="and b.order_id in  ($txt_job_id)";
		}else{$job_cond="";}

		if($txt_order_id !=""){
			$order_cond="and b.order_id in  ($txt_order_id)";
		}else{$order_cond="";}

	}else{
		if($txt_job_id !=""){
			$job_cond="and b.job_no in  ($txt_job_no)";
		}else{$job_cond="";}

		if($txt_order_id !=""){
			$order_cond="and b.order_id in  ($txt_order_id)";
		}else{$order_cond="";}
	}

	$composition_arr = array();
	$constructtion_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		$constructtion_arr[$row[csf('id')]] = $row[csf('construction')];
		$composition_arr[$row[csf('id')]] .= $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "% ";
	}
	$parti_source_con="";
	if(!empty($cbo_party_source))
	{
		$parti_source_con=" and a.party_source in ($cbo_party_source) ";
	}
	
	 $inhouse_sql="select a.id,a.bill_no , a.company_id , a.location_id,a.party_source, a.bill_date, a.party_id,a.party_source,b.buyer_po_id,b.po_break_down_id,b.batch_id,b.challan_no,b.item_id,b.body_part_id,b.color_id,b.delivery_qty,b.delivery_dtls_id,b.delivery_id,b.order_id,b.amount,b.rate,b.currency_id as currency,b.process_id,b.add_process_name from  subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id  and  a.status_active=1 and b.status_active=1 and a.process_id=$report_type $parti_con $parti_source_con $date_cond $location_con $company_con $bill_cond $job_cond $order_cond $challan_cond order by a.id,a.party_source asc ";
	 $outbound_sql="select a.bill_no,a.bill_date,a.company_id,a.supplier_id,a.location_id,a.party_bill_no,b.challan_no,b.color_id,b.sub_process_id,b.body_part_id,b.job_no,b.order_id,b.rate,b.febric_description_id,b.receive_qty,b.amount,b.currency_id as currency ,b.process_id,a.id from  subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id  and a.status_active=1 and b.status_active=1 and a.process_id=$report_type  $date_cond $location_con $company_con $bill_cond $supplier_con $job_cond $order_cond $challan_cond";
	//echo $outbound_sql;die;
	//echo $inhouse_sql;die;
	$bill_wise=array();
	$pre_b='';
	$inhouse_result=sql_select($inhouse_sql);
	$outbound_result=sql_select($outbound_sql);
	if(!empty($cbo_party_source))
	{
		if($cbo_party_source==3)
		{
			$inhouse_result=array();
		}

		else{
			$outbound_result=array();
		}
	}
	$order_ids_inhouse=array();
	foreach ($inhouse_result as $data) 
	{
		$party_sourceId=$data[csf('party_source')];
		$batch_idArr[$data[csf('batch_id')]]=$data[csf('batch_id')];
		if(!empty($data[csf('order_id')]))
		{
			if($party_sourceId==1)
			{
				$order_ids_inhouse[]=$data[csf('order_id')];
			}
			else
			{
				$subCon_po_idArr[$data[csf('order_id')]]=$data[csf('order_id')];
			}
		}
	}
	$order_ids=array();
	foreach ($outbound_result as $data) 
	{
		if(!empty($data[csf('order_id')]))
		{
			$order_ids[]=$data[csf('order_id')];
			$order_ids_inhouse[]=$data[csf('order_id')];
		}
	}
	$order_ids=array_unique($order_ids);
	 $job_inbound_arr=array();
	$sql_inbound_job="SELECT a.job_no, a.buyer_name, a.style_ref_no, b.id, b.po_number,b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active!=0 and a.is_deleted=0  and b.status_active!=0 and b.is_deleted=0 ".where_con_using_array($order_ids_inhouse,0,'b.id');
	//echo $sql_inbound_job;
	$sql_job_inbound_result =sql_select($sql_inbound_job);
	foreach($sql_job_inbound_result as $row)
	{
		$job_inbound_arr[$row[csf('id')]]['job']=$row[csf('job_no')];
		$job_inbound_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
		$job_inbound_arr[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
		$job_inbound_arr[$row[csf('id')]]['po']=$row[csf('po_number')];
		$job_inbound_arr[$row[csf('id')]]['int_ref']=$row[csf('grouping')];
	}
	 
	$po_number_arr = return_library_array("select id, po_number from wo_po_break_down where status_active=1 ".where_con_using_array($order_ids_inhouse,0,'id'), "id", "po_number");
	$order_no_arr = return_library_array("select id, order_no from subcon_ord_dtls where status_active=1 ".where_con_using_array($subCon_po_idArr,0,'id'), "id", "order_no");
	//$batch_idArr
//	$product_dtls_arr=return_library_array( "select id,item_description from pro_batch_create_dtls where status_active=1 ".where_con_using_array($batch_idArr,0,'mst_id') ." ",'id','item_description');
	$SQL_Batch= "select id,item_description,gsm,fin_dia,body_part_id from pro_batch_create_dtls where status_active=1 ".where_con_using_array($batch_idArr,0,'mst_id') ." ";
	$sql_batch_result =sql_select($SQL_Batch);
	 foreach($sql_batch_result as $row)
	 {
	 	$product_dtls_arr[$row[csf('id')]]=$row[csf('item_description')];
	 	$batch_data_arr[$row[csf('id')]]['gsm']=$row[csf('gsm')];
	 	$batch_data_arr[$row[csf('id')]]['fin_dia']=$row[csf('fin_dia')];
	  	$batch_data_arr[$row[csf('id')]]['body']=$row[csf('body_part_id')];
	 	//$batch_data_arr[$row[csf('id')]]['int_ref']=$row[csf('grouping')];
	 }
	//print_r($product_dtls_arr);
	 $sql_dye_fin_bill="SELECT b.order_id, b.process_id, b.item_id,b.gsm, b.dia,b.batch_id,b.color_id, b.delivery_qty,b.gray_qty from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id ".where_con_using_array($subCon_po_idArr,0,'b.order_id') ." and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0";
	//echo $sql_job;
	$sql_dye_fin_result =sql_select($sql_dye_fin_bill);
	 foreach($sql_dye_fin_result as $row)
	 {
	 	$dye_fin_delivery_arr[$row[csf('order_id')]][$row[csf('item_id')]]['gray_qty']+=$row[csf('gray_qty')];
		$dye_fin_delivery_arr[$row[csf('order_id')]][$row[csf('item_id')]]['gsm']=$row[csf('gsm')];
		$dye_fin_delivery_arr[$row[csf('order_id')]][$row[csf('item_id')]]['dia']=$row[csf('dia')];
	 	//$dye_fin_delivery_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
	 }
	 unset($sql_dye_fin_result);
	$job_outbound_arr=array();
	 $sql_job="SELECT a.subcon_job as job_no, a.party_id as buyer_name, b.cust_style_ref as style_ref_no, b.id, b.order_no as po_number , null as grouping from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst ".where_con_using_array($subCon_po_idArr,0,'b.id') ." and a.status_active=1 and a.is_deleted=0";
	//echo $sql_job;
	$sql_job_result =sql_select($sql_job);
	 foreach($sql_job_result as $row)
	 {
	 	$job_outbound_arr[$row[csf('id')]]['job']=$row[csf('job_no')];
	 	$job_outbound_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
	 	$job_outbound_arr[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
	 	$job_outbound_arr[$row[csf('id')]]['po']=$row[csf('po_number')];
	 	$job_outbound_arr[$row[csf('id')]]['int_ref']=$row[csf('grouping')];
	 }
	$sql_order=sql_select("SELECT a.id, a.company_id, a.recv_number,a.booking_id,a.booking_no, a.receive_basis, a.receive_date, a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id, b.febric_description_id,b.trans_id, b.gsm,b.room,b.rack,b.self,b.bin_box,c.qnty,c.rate, c.qc_pass_qnty,c.roll_no,b.width,b.body_part_id,b.yarn_lot,b.brand_id,b.shift_name,b.floor_id,b.machine_no_id,b.yarn_count,b.color_id,b.color_range_id, c.barcode_no, c.id as roll_id, c.po_breakdown_id, c.booking_without_order, c.is_sales, c.qc_pass_qnty_pcs,b.stitch_length
    FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
    WHERE a.id=b.mst_id and b.id=c.dtls_id ".where_con_using_array($order_ids_inhouse,0,'c.po_breakdown_id') ."  and c.status_active=1 and c.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $floor_con ");
	
    $order_wise_data=array();
    $buyer_wise_data=array();
    $floor_wise_data=array();
    foreach ($sql_order as $data) 
    {
    	$order_wise_data[$data[csf('po_breakdown_id')]]['recv_number']=$data[csf('recv_number')];
    	$order_wise_data[$data[csf('po_breakdown_id')]]['booking_no']=$data[csf('booking_no')];
    	$order_wise_data[$data[csf('po_breakdown_id')]]['febric_description_id']=$data[csf('febric_description_id')];
    	$order_wise_data[$data[csf('po_breakdown_id')]]['gsm']=$data[csf('gsm')];
    	$order_wise_data[$data[csf('po_breakdown_id')]]['width']=$data[csf('width')];
    	$order_wise_data[$data[csf('po_breakdown_id')]]['body_part_id']=$data[csf('body_part_id')];
    	$order_wise_data[$data[csf('po_breakdown_id')]]['yarn_lot']=$data[csf('yarn_lot')];
    	$order_wise_data[$data[csf('po_breakdown_id')]]['floor_id']=$data[csf('floor_id')];
    	$order_wise_data[$data[csf('po_breakdown_id')]]['stitch_length']=$data[csf('stitch_length')];
    	$order_wise_data[$data[csf('po_breakdown_id')]]['yarn_count']=$data[csf('yarn_count')];
		$order_wise_data[$data[csf('po_breakdown_id')]]['rcv_qnty']+=$data[csf('receive_qnty')];
    }
		$inbound_sql_data=sql_select("SELECT a.id, a.entry_form, a.receive_basis, a.recv_number_prefix_num, a.challan_no, a.receive_date, a.booking_id as bookingno, TO_CHAR(a.insert_date,'YYYY') as year,	b.batch_id, b.prod_id, b.body_part_id, b.fabric_description_id, b.color_id, b.dia_width_type, sum(c.quantity) as rec_qnty, sum(b.no_of_roll) as carton_roll, c.po_breakdown_id, d.booking_no_id, d.booking_no,d.process_id FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, pro_batch_create_mst d 	 WHERE a.id=b.mst_id and b.id=c.dtls_id and d.id=b.batch_id and c.trans_type=1 and c.entry_form in (7,37,66,68) and c.trans_id!=0 and a.entry_form in (7,37,66,68)  AND a.company_id=$cbo_company_id and c.quantity>0  and a.receive_basis in (2,4,5,9,11) and a.item_category=2  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.batch_against in (0,1,3) and nvl(d.booking_without_order,0)=0 
		group by a.id, a.entry_form, a.receive_basis, a.recv_number_prefix_num, a.challan_no, a.receive_date, a.booking_id, a.insert_date, 
		b.batch_id, b.prod_id, b.body_part_id, b.fabric_description_id, b.color_id, b.dia_width_type, c.po_breakdown_id, d.booking_no_id,
		d.booking_no,d.process_id order by a.recv_number_prefix_num DESC ");
		foreach($inbound_sql_data as $row){
		$inbound_order_data[$row[csf('po_breakdown_id')]]['bill_rcv_qnty']+=$row[csf('rec_qnty')];
		$inbound_order_data[$row[csf('po_breakdown_id')]]['process_id']=$row[csf('process_id')];
		}
		
		$outbound_sql_data=sql_select("SELECT a.id as mst_id, a.entry_form, a.receive_basis, a.recv_number_prefix_num, a.challan_no, a.receive_date, b.booking_id as bookingno, TO_CHAR(a.insert_date,'YYYY') as year, b.batch_id, b.prod_id, b.body_part_id, b.febric_description_id as fabric_description_id, b.color_id, sum(b.batch_issue_qty) as rec_qnty, sum(b.grey_used) as grey_qty, b.id as dtls_id, b.order_id as po_breakdown_id, b.booking_id, b.booking_no, b.process_id FROM inv_receive_mas_batchroll a, pro_grey_batch_dtls b WHERE a.id=b.mst_id and a.entry_form=92 AND a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   group by a.id, a.entry_form, a.receive_basis, a.recv_number_prefix_num, a.challan_no, a.receive_date, b.booking_id, a.insert_date, b.batch_id, b.prod_id, b.body_part_id, b.febric_description_id, b.color_id,
		b.order_id, b.id, b.booking_id, b.booking_no,b.process_id order by a.recv_number_prefix_num DESC ");
		// echo "SELECT a.id as mst_id, a.entry_form, a.receive_basis, a.recv_number_prefix_num, a.challan_no, a.receive_date, b.booking_id as bookingno, TO_CHAR(a.insert_date,'YYYY') as year, b.batch_id, b.prod_id, b.body_part_id, b.febric_description_id as fabric_description_id, b.color_id, sum(b.batch_issue_qty) as rec_qnty, sum(b.grey_used) as grey_qty, b.id as dtls_id, b.order_id as po_breakdown_id, b.booking_id, b.booking_no, b.process_id FROM inv_receive_mas_batchroll a, pro_grey_batch_dtls b WHERE a.id=b.mst_id and a.entry_form=92 AND a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   group by a.id, a.entry_form, a.receive_basis, a.recv_number_prefix_num, a.challan_no, a.receive_date, b.booking_id, a.insert_date, b.batch_id, b.prod_id, b.body_part_id, b.febric_description_id, b.color_id,
		// b.order_id, b.id, b.booking_id, b.booking_no,b.process_id order by a.recv_number_prefix_num DESC ";
		foreach($outbound_sql_data as $row){
		$outbound_order_data[$row[csf('po_breakdown_id')]]['bill_rcv_qnty']+=$row[csf('rec_qnty')];
		 $outbound_order_data[$row[csf('po_breakdown_id')]]['process_id']=$row[csf('process_id')];
		$outbound_order_data2[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['process_id']=$row[csf('process_id')];
		}
	if($report_type==4)
	{
		$table_width=2100; $colspan="15";
	}
	else{
		$table_width=2000; $colspan="14";
	}
	ob_start();
	?>
    <fieldset style="width:<?php echo $table_width+20;?>" >	
       
        <table class="rpt_table" border="1" rules="all" width="<?=$table_width; ?>" cellpadding="0" cellspacing="0" style="margin-top: 50px;" >
            <thead>
                <tr>
                	<th style="word-break: break-all;" width="40">SL</th>
                    <th style="word-break: break-all;" width="110">Bill No</th>
                    <th style="word-break: break-all;" width="60">Date</th>
                    <th style="word-break: break-all;" width="100">Party</th>
                    <th style="word-break: break-all;" width="110">Buyer Name</th>
                    <th style="word-break: break-all;" width="110">Style Name</th>
                    <th style="word-break: break-all;" width="115">Job No</th>
					<th style="word-break: break-all;" width="100">Order No</th>
					<th style="word-break: break-all;" width="100">Process Name</th>
                    <th style="word-break: break-all;" width="110">Fabric<br>Construction</th>
                    <th style="word-break: break-all;" width="250">Yarn<br>Composition</th>
                    <th style="word-break: break-all;" width="70">GSM</th>
                    <th style="word-break: break-all;" width="80">F/Dia</th>
					<th style="word-break: break-all;" width="100">Body Part</th>
                    <?php if($report_type==4){ ?>
                    <th style="word-break: break-all;" width="80">Fabric Color</th>
                	<?php } ?>
					<th style="word-break: break-all;" width="100">Receive Qty</th>
                    <th style="word-break: break-all;" width="80">Bill Qty</th>
					<th style="word-break: break-all;" width="100" title="Process Loss=(Receive Qty-Bill Qty)/Receive Qty">Process Loss %</th>
                    <th style="word-break: break-all;" width="70">Rate/Kg</th>
                    <th style="word-break: break-all;" width="70">Bill Value</th>
                    <th style="word-break: break-all;">Currency</th>
                    
                </tr>
            </thead>
        </table>

		<table border="1" class="rpt_table" rules="all" width="<?=$table_width; ?>" id="table_body"  cellpadding="0" cellspacing="0">
           <tbody>
					<?php 
						$source='';
						$total_bill=0;
						$total_amount=0;
						$sub_bill=0;
						$sub_amount=0;
						$i=1;
						foreach($inhouse_result as $row)
						{

							$color_names = '';
		                        $colorIds = array_unique(explode(",", $row[csf('color_id')]));
		                        foreach ($colorIds as $color_id)
								{
		                            $color_names .= $color_arr[$color_id] . ",";
		                        }
		                        $color_names = chop($color_names, ',');

										
									$item_id=$row[csf('item_id')];
								 //	echo $row[csf('party_source')].'=';
										if($row[csf('party_source')]==1) //Party Source -In House $row[csf('party_source')]==1
										{
											$inbound_rcv_qnty=$inbound_order_data[$row[csf('order_id')]]['bill_rcv_qnty'];
											$process_id=$inbound_order_data[$row[csf('order_id')]]['process_id'];
											$process=$conversion_cost_head_array[$process_id];
											$buyer=$buyer_arr[$job_inbound_arr[$row[csf('order_id')]]['buyer']];
											$style=$job_inbound_arr[$row[csf('order_id')]]['style'];
											$job_no=$job_inbound_arr[$row[csf('order_id')]]['job'];
											$construct=$constructtion_arr[$order_wise_data[$row[csf('order_id')]]['febric_description_id']];
											$gsm=$order_wise_data[$row[csf('order_id')]]['gsm'];
											$fin_dia=$order_wise_data[$row[csf('order_id')]]['width'];
											$body=$body_part[$order_wise_data[$row[csf('order_id')]]['body_part_id']];
											$yarn_comp=$composition_arr[$order_wise_data[$row[csf('order_id')]]['febric_description_id']];
											//	echo $gsm.'<br>';
										}
										else if($row[csf('party_source')]==2) //Party Source -In Bound $row[csf('party_source')]==1
										{
											//$dye_fin_delivery_arr[$row[csf('order_id')]][$row[csf('item_id')]]['gsm']=$row[csf('gsm')];
											//$dye_fin_delivery_arr[$row[csf('order_id')]][$row[csf('item_id')]]['dia']
											$inbound_rcv_qnty=$dye_fin_delivery_arr[$row[csf('order_id')]][$item_id]['gray_qty'];
											$process=$row[csf('add_process_name')];
											$buyer=$buyer_arr[$job_outbound_arr[$row[csf('order_id')]]['buyer']];
											$style=$job_outbound_arr[$row[csf('order_id')]]['style'];
											$job_no=$job_outbound_arr[$row[csf('order_id')]]['job'];
											$construct=$product_dtls_arr[$item_id];
											$gsm=$dye_fin_delivery_arr[$row[csf('order_id')]][$item_id]['gsm'];//$batch_data_arr[$item_id]['gsm'];
											$fin_dia=$dye_fin_delivery_arr[$row[csf('order_id')]][$item_id]['dia'];
											$body=$body_part[$row[csf('body_part_id')]];//$batch_data_arr[$item_id]['body'];
											$yarn_comp=$product_compsition_dtls_arr[$item_id];
											// $product_compsition_dtls_arr[$item_id];
										 	// $product_compsition_dtls_arr[$item_id].'<br>';
											 // $product_dtls_arr
										}
										else{ //Party Source -In Bound Subcon
											$inbound_rcv_qnty=$dye_fin_delivery_arr[$row[csf('order_id')]][$item_id]['gray_qty'];
											$process=$row[csf('add_process_name')];
											$buyer=$buyer_arr[$job_outbound_arr[$row[csf('order_id')]]['buyer']];
											$style=$job_outbound_arr[$row[csf('order_id')]]['style'];
											$job_no=$job_outbound_arr[$row[csf('order_id')]]['job'];
											$construct=$product_dtls_arr[$item_id];
											$yarn_comp=$product_compsition_dtls_arr[$item_id];
											$gsm=$batch_data_arr[$item_id]['gsm'];
											$fin_dia=$batch_data_arr[$item_id]['fin_dia'];
											$body=$body_part[$batch_data_arr[$item_id]['body']];
											//echo $row[csf('order_id')].'<br>';
											
										}
										$print_inbound_report_format=return_field_value("format_id","lib_report_template","template_name =".$cbo_company_id." and module_id=8 and report_id=266 and is_deleted=0 and status_active=1");
										$print_inbound_button=explode(",",$print_inbound_report_format);
										$print_inbound_button_first=array_shift($print_inbound_button);
										//echo $print_button_first.'D';
										if($print_inbound_button_first==143) $action_button="fabric_finishing_print";
										else if($print_inbound_button_first==66) $action_button="dyeing_finishin_bill_print";
										else if($print_inbound_button_first==85) $action_button="dyeing_finishin_bill_print3";
										else if($print_inbound_button_first==160) $action_button="dyeing_finishin_bill_print4";
										else if($print_inbound_button_first==129) $action_button="dyeingFinishinBillPrint5";
										else  $action_button="";
										
	                        
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
									<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>">
										<td style="word-break: break-all;" width="40"><?=$i; ?></td>
										<td style="word-break: break-all;" width="110" title="In:A">
											<p>							
												<a href="#report_details" onClick="generate_bill_report('<? echo $row[csf('company_id')];?>','<? echo $row[csf('id')]; ?>','<? echo $row[csf('bill_no')];?>','<? echo $row[csf('process_id')];?>','<? echo $action_button;?>')">
												<?=$row[csf('bill_no')]; ?>
												</a>
											</p>
										</td>
										<td style="word-break: break-all;" width="60"><p><?=change_date_format($row[csf('bill_date')]); ?></p></td>
										
										<td style="word-break: break-all;" width="100">
											<p>
												<?

													if($row[csf('party_source')]==1)
													{
														echo $company_arr[$row[csf("party_id")]];
													}else{
														echo $buyer_arr[$row[csf("party_id")]];
													}
												 ?>
										 	
										 </p>
										</td>
										
										<td style="word-break: break-all;"  width="110"><p><?=$buyer ; ?></p></td>
										<td style="word-break: break-all;" width="110"><p><?=$style; ?></p></td>
										<td style="word-break: break-all;" width="115"><p><?=$job_no; ?></p></td>
										<td style="word-break: break-all;" width="100"><p><?
									
										if($row[csf('party_source')]==1)
										{
											echo $po_number_arr[$row[csf('order_id')]];;
										}else{
											echo $order_no_arr[$row[csf('order_id')]];
										}
										
										?></p></td>
										<td style="word-break: break-all;" width="100" title="<?=$process_id;?>"><p><?=$process; ?></p></td>
										<td style="word-break: break-all;" width="110" title="DeterId=<?=$item_id;?>" ><p><?=$construct; ?></p></td>
										<td style="word-break: break-all;" width="250"><p><?=$yarn_comp; ?></p></td>
										<td style="word-break: break-all;" width="70"><p><?=$gsm; ?></p></td>
										<td style="word-break: break-all;" width="80"><p><?=$fin_dia; ?></p></td>
										<td style="word-break: break-all;" width="100"><p><?=$body; ?></p></td>
										 <?php if($report_type==4){ ?>
					                    <td style="word-break: break-all;" width="80"> <p><?php echo $color_names; ?></p></td>
					                	<?php } ?>
										<td style="word-break: break-all;" width="100" align="right"><p><?=number_format($inbound_rcv_qnty,2); ?></p></td>
										<td style="word-break: break-all;" width="80" align="right"><p><?=number_format($row[csf('delivery_qty')],2); ?></p></td>
										<td style="word-break: break-all;" width="100" align="right" title="Process Loss=(Receive Qty-Bill Qty)/Receive Qty"><p><?=fn_number_format(($inbound_rcv_qnty-$row[csf('delivery_qty')])/$inbound_rcv_qnty,2); ?></p></td>
										<td style="word-break: break-all;" width="70" align="right"><p><?=number_format($row[csf('rate')],2); ?></p></td>
										<td style="word-break: break-all;" width="70" align="right"><p><?=number_format($row[csf('amount')],2); ?></p></td>
										<td style="word-break: break-all;"><p><?=$currency[$row[csf('currency')]]; ?></p></td>
										
									</tr>
								<?
								$source=$row[csf('party_source')];
								$total_bill+=$row[csf('delivery_qty')];
								$total_bill_qnty+=$inbound_rcv_qnty;
								$total_amount+=$row[csf('amount')];
								$sub_bill+=$row[csf('delivery_qty')];
								$sub_amount+=$row[csf('amount')];
								$i++;
							
						}
						
						
						$process="";$process='';
						foreach($outbound_result as $row)
						{

                            $color_names = '';$process='';
		                        $colorIds = array_unique(explode(",", $row[csf('color_id')]));
		                        foreach ($colorIds as $color_id)
								{
		                            $color_names .= $color_arr[$color_id] . ",";
									$process_id=$outbound_order_data2[$row[csf('order_id')]][$color_id]['process_id'];
							        $process.=$conversion_cost_head_array[$process_id]. ",";;
		                        }
		                        $color_names = chop($color_names, ',');
								$process = chop($process, ',');
								
                            if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$outbound_rcv_qnty=$outbound_order_data[$row[csf('order_id')]]['bill_rcv_qnty'];
							// $process_id=$outbound_order_data[$row[csf('order_id')]]['process_id'];
							// $process=$conversion_cost_head_array[$process_id];
							if($report_type==4)
							{
								$process_name=$conversion_cost_head_array[$row[csf('sub_process_id')]];
							}
							else
							{
								$process_name=$process;
							}
							
							$print_report_format=return_field_value("format_id","lib_report_template","template_name =".$cbo_company_id." and module_id=8 and report_id=267 and is_deleted=0 and status_active=1");
							$print_button=explode(",",$print_report_format);
							$print_button_first=array_shift($print_button);
							//echo $print_button_first.'D';
							if($print_button_first==143) $action_button="fabric_finishing_print";
							else if($print_button_first==66) $action_button="fabric_dyeing_finishing_print";
							else  $action_button="";

							$print_inbound_report_format=return_field_value("format_id","lib_report_template","template_name =".$cbo_company_id." and module_id=8 and report_id=267 and is_deleted=0 and status_active=1");
							$print_button=explode(",",$print_report_format);
							$print_button_first=array_shift($print_button);
							//echo $print_button_first.'D';
							if($print_button_first==143) $action_button="fabric_finishing_print";
							else if($print_button_first==66) $action_button="fabric_dyeing_finishing_print";
							else  $action_button="";
	                        

                            ?>
								<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>">
									<td style="word-break: break-all;" width="40"><?=$i; ?></td>
									<td style="word-break: break-all;" width="110" title="Out:B">
									<p>							
										<a href="#report_details" onClick="generate_bill_outBound_report('<? echo $row[csf('company_id')];?>','<? echo $row[csf('id')]; ?>','<? echo $row[csf('bill_no')];?>','<? echo $row[csf('process_id')];?>','<? echo $action_button;?>')">
										<?=$row[csf('bill_no')]; ?>
										</a>
									</p>
									</td>
									<td style="word-break: break-all;" width="60"><p><?=change_date_format($row[csf('bill_date')]); ?></p></td>
									
									<td style="word-break: break-all;" width="100"><p><?=$supplier_arr[$row[csf('supplier_id')]]; ?></p></td>
									
									<td style="word-break: break-all;" width="110"><p><?=$buyer_arr[$job_inbound_arr[$row[csf('order_id')]]['buyer']] ; ?></p></td>
									<td style="word-break: break-all;" width="110"><p><?=$job_inbound_arr[$row[csf('order_id')]]['style']; ?></p></td>
									<td style="word-break: break-all;" width="115"><p><?=$job_inbound_arr[$row[csf('order_id')]]['job'] ; ?></p></td>
									<td style="word-break: break-all;" width="100"><p><?=$po_number_arr[$row[csf('order_id')]]; ?></p></td>
									<td style="word-break: break-all;" width="100" title="<?=$row[csf('sub_process_id')]?>"><p><? echo $process_name; ?></p></td>
									<td style="word-break: break-all;" width="110" ><p><?=$constructtion_arr[$row[csf('febric_description_id')]]; ?></p></td>
									<td style="word-break: break-all;" width="250" ><p><?=$composition_arr[$row[csf('febric_description_id')]]; ?></p></td>
									<td style="word-break: break-all;" width="70"><p><?=$order_wise_data[$row[csf('order_id')]]['gsm']; ?></p></td>
									<td style="word-break: break-all;" width="80"><p><?=$order_wise_data[$row[csf('order_id')]]['width']; ?></p></td>
									<td style="word-break: break-all;" width="100"><p><?=$body_part[$order_wise_data[$row[csf('order_id')]]['body_part_id']]; ?></p></td>
									
									<?php if($report_type==4){ ?>
					                <td style="word-break: break-all;" width="80"> <p><?php echo $color_names; ?></p></td>
					                <?php } ?>
									<td style="word-break: break-all;" width="100" align="right"><p><?=number_format($outbound_rcv_qnty,2); ?></p></td>
									<td style="word-break: break-all;" width="80" align="right"><p><?=number_format($row[csf('receive_qty')],2); ?></p></td>
									<td style="word-break: break-all;" width="100" align="right" title="Process Loss=(Receive Qty-Bill Qty)/Receive Qty"><p><?=fn_number_format(($outbound_rcv_qnty-$row[csf('receive_qty')])/$outbound_rcv_qnty,2); ?></p></td>
									<td style="word-break: break-all;" width="70" align="right"><p><?=number_format($row[csf('rate')],2); ?></p></td>
									<td style="word-break: break-all;" width="70" align="right"><p><?=number_format($row[csf('amount')],2); ?></p></td>
									<td style="word-break: break-all;"><p><?=$currency[$row[csf('currency')]]; ?></p></td>
									
								</tr>
							<?
							
							$total_bill+=$row[csf('receive_qty')];
							$total_bill_qnty+=$outbound_rcv_qnty;
							$total_amount+=$row[csf('amount')];
							$sub_bill+=$row[csf('receive_qty')];
							$sub_amount+=$row[csf('amount')];
							$i++;

                        }

					
						
							?>
			</tbody>
		</table>
		<table border="1" class="rpt_table" rules="all" width="<?=$table_width; ?>" cellpadding="0" cellspacing="0" >
			<tfoot >
			 	<?php $bgcolor="#efefef";  ?>
				<tr bgcolor="<?=$bgcolor; ?>" >

					<td style="word-break: break-all;" width="40"></td>
                    <td style="word-break: break-all;" width="110"></td>
                    <td style="word-break: break-all;" width="60"></td>
                    <td style="word-break: break-all;" width="100"></td>
                    <td style="word-break: break-all;" width="110"></td>
                    <td style="word-break: break-all;" width="110"></td>
                    <td style="word-break: break-all;" width="115"></td>
					<td style="word-break: break-all;" width="100"></td>
					<td style="word-break: break-all;" width="100"></td>

                    <td style="word-break: break-all;" width="110"></td>
                    <td style="word-break: break-all;" width="250"></td>
                    <td style="word-break: break-all;" width="70"></td>

                    <td style="word-break: break-all;" width="80"></td>
					<td style="word-break: break-all;" width="100" align="right">Total</td>
                    <?php if($report_type==4){ ?>
                    <td style="word-break: break-all;" width="80"></td>
                	<?php } ?>
					<td style="word-break: break-all;" width="100" align="right" style="justify-content: right;font-weight: bold;" id="total_bill_qnty"><?=$total_bill_qnty;?></td>
                    <td style="word-break: break-all;" width="80" align="right" style="justify-content: right;tfont-weight: bold;" id="total_bill"><?=$total_bill;?></td>
					<td style="word-break: break-all;" width="100"></td>
                    <td style="word-break: break-all;" width="70"></td>
                    <td style="word-break: break-all;" width="70" align="right" style="justify-content: right;font-weight: bold;" id="total_amount"><?=$total_amount;?></td>
                    <td style="word-break: break-all;"></td>




       				<!-- <td colspan="<?php //echo $colspan;?>" style="justify-content: right;text-align: right;font-weight: bold;">Total</td>
					 <td  style="justify-content: right;text-align: right;font-weight: bold;" id="total_bill_qnty"><?//=$total_bill_qnty;?></td>
					<td  style="justify-content: right;text-align: right;font-weight: bold;" id="total_bill"><?//=$total_bill;?></td>
					<td ></td>
					<td ></td>
					<td  style="justify-content: right;text-align: right;font-weight: bold;" id="total_amount"><?//=$total_amount;?></td>
					<td></td> -->
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
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}
if($action=="report_generate2")
{ 
	$process = array( &$_POST );

	// echo "<pre>";
	// print_r($process);
	// echo "</pre>";
	// die;
	
	extract(check_magic_quote_gpc( $process ));
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$company_arr=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name");
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier ", "id", "supplier_name");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0","id","color_name");
	$location_arr = return_library_array("select id,location_name from lib_location where   status_active =1 and is_deleted=0 order by location_name","id","location_name");
	$imge_arr=return_library_array( "select master_tble_id, image_location from  common_photo_library where file_type=1",'master_tble_id','image_location');
	$floor_arr = return_library_array("select id,floor_name from lib_prod_floor where  status_active =1 and is_deleted=0 order by floor_name", 'id', 'floor_name');
	$cbo_company_id= str_replace("'","",$cbo_company_id);
	$cbo_party_source = str_replace("'","",trim($cbo_party_source));
	$start_date=str_replace("'","",trim($txt_date_from));
	$end_date=str_replace("'","",trim($txt_date_to));
	$cbo_party_name=str_replace("'","",trim($cbo_party_name));
	$report_type=str_replace("'","",trim($report_type));
	$txt_bill_no=str_replace("'","",trim($txt_bill_no));
	$txt_challan_no=str_replace("'","",trim($txt_challan_no));
	$txt_job_no=str_replace("'","",trim($txt_job_no));
	$txt_job_id=str_replace("'","",trim($txt_job_id));
	$txt_order_no=str_replace("'","",trim($txt_order_no));
	$txt_order_id=str_replace("'","",trim($txt_order_id));
	$job_order_type=str_replace("'","",trim($cbo_job_order_type));
	
	$supplier_con='';
	$parti_con='';
	$company_con='';
	$bill_cond='';
	//echo $cbo_search_by.'--'.$txt_search_common;die;
	if(!empty($cbo_party_name) )
	{
		$supplier_con=" and a.supplier_id=$cbo_party_name";
		$parti_con=" and a.party_id=$cbo_party_name";
	}

	if(!empty($cbo_company_id) && $cbo_company_id!=0)
	{
		$company_con=" and  a.company_id=$cbo_company_id";
	}

	if(!empty($txt_bill_no))
	{
		$bill_cond=" and  a.prefix_no_num like '%$txt_bill_no%'";
	}

	if(!empty($txt_challan_no))
	{
		$challan_cond=" and  b.challan_no like '%$txt_challan_no%'";
	}

	if(!empty($floor_id))
	{
		$floor_con=" and b.floor_id=$floor_id";
	}
	$wo_no_con='';
	$search_field_cond='';
	$wo_date_con='';
	$date_cond ='';

	if($db_type==0)
	{
		if ($start_date!="" &&  $end_date!="") $date_cond  = "and a.receive_date  between '".change_date_format($start_date, "yyyy-mm-dd", "-")."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'"; else $date_cond ="";
		if ($start_date!="" &&  $end_date!="") $date_cond2  = "and a.receive_date  between '".change_date_format($start_date, "yyyy-mm-dd", "-")."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'"; else $date_cond ="";
	}
	if($db_type==2)
	{
		if ($start_date!="" &&  $end_date!="") $date_cond  = "and a.bill_date  between '".change_date_format($start_date, "yyyy-mm-dd", "-",1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-",1)."'"; else $date_cond ="";
		if ($start_date!="" &&  $end_date!="") $date_cond2  = "and a.receive_date  between '".change_date_format($start_date, "yyyy-mm-dd", "-",1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-",1)."'"; else $date_cond2 ="";
	}
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$roll_no_arr=return_library_array( "select id, no_of_roll from  pro_grey_prod_entry_dtls",'id','no_of_roll');
	$bodyPartTypeArr = return_library_array( "select id, body_part_type from lib_body_part",'id','body_part_type');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	/*$product_dtls_arr=return_library_array( "select id,product_name_details as const_comp from  product_details_master 
	union all select id,const_comp from lib_subcon_charge",'id','const_comp');*/
	 $prod_lib="select id,product_name_details as const_comp,detarmination_id,null as yarn_description,dia_width,gsm from  product_details_master 
	union all select id,const_comp,0 as detarmination_id,yarn_description,null as dia_width,gsm from lib_subcon_charge";
	$prod_lib_array = sql_select($prod_lib);
		foreach ($prod_lib_array as $row) {
		$product_dtls_arr[$row[csf('id')]] = $row[csf('const_comp')];
		$product_fabric_dtls_arr[$row[csf('id')]] = $row[csf('detarmination_id')];
		if($row[csf('dia_width')]!="")
		{
		$prod_fabric_dtls_arr[$row[csf('id')]]['dia'] = $row[csf('dia_width')];
		}
		if($row[csf('gsm')]!="")
		{
		$prod_fabric_dtls_arr[$row[csf('id')]]['gsm'] = $row[csf('gsm')];
		}
		if($row[csf('yarn_description')]!="")
		{
	  	 $product_compsition_dtls_arr[$row[csf('id')]] =$row[csf('yarn_description')];
		}
	}
	//print_r($product_dtls_arr);
	
	

	if($job_order_type==1){
		if($txt_job_id !==""){
			$job_cond="and b.order_id in  ($txt_job_id)";
		}else{$job_cond="";}

		if($txt_order_id !=""){
			$order_cond="and b.order_id in  ($txt_order_id)";
		}else{$order_cond="";}

	}else{
		if($txt_job_id !=""){
			$job_cond="and b.job_no in  ($txt_job_no)";
		}else{$job_cond="";}

		if($txt_order_id !=""){
			$order_cond="and b.order_id in  ($txt_order_id)";
		}else{$order_cond="";}
	}

	$composition_arr = array();
	$constructtion_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		$constructtion_arr[$row[csf('id')]] = $row[csf('construction')];
		$composition_arr[$row[csf('id')]] .= $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "% ";
	}
	$parti_source_con="";
	if(!empty($cbo_party_source))
	{
		$parti_source_con=" and a.party_source in ($cbo_party_source) ";
	}
	
	 $inhouse_sql="select a.id,a.bill_no , a.company_id , a.location_id,a.party_source, a.bill_date, a.party_id,a.party_source,b.buyer_po_id,b.po_break_down_id,b.batch_id,b.challan_no,b.item_id,b.body_part_id,b.color_id,b.delivery_qty,b.delivery_dtls_id,b.delivery_id,b.order_id,b.amount,b.rate,b.currency_id as currency,b.process_id,b.add_process_name from  subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id  and  a.status_active=1 and b.status_active=1 and a.process_id=$report_type $parti_con $parti_source_con $date_cond $location_con $company_con $bill_cond $job_cond $order_cond $challan_cond order by a.id,a.party_source asc ";
	 $outbound_sql="select a.bill_no,a.bill_date,a.company_id,a.supplier_id,a.location_id,a.party_bill_no,b.challan_no,b.item_id,b.color_id,b.body_part_id,b.job_no,b.order_id,b.rate,b.febric_description_id,b.receive_qty,b.amount,b.currency_id as currency ,b.process_id from  subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id  and a.status_active=1 and b.status_active=1 and a.process_id=$report_type  $date_cond $location_con $company_con $bill_cond $supplier_con $job_cond $order_cond $challan_cond";
	//echo $inhouse_sql;
	//echo $outbound_sql;
	$bill_wise=array();
	$pre_b='';
	$inhouse_result=sql_select($inhouse_sql);
	$outbound_result=sql_select($outbound_sql);
	if(!empty($cbo_party_source))
	{
		if($cbo_party_source==3)
		{
			$inhouse_result=array();
		}

		else{
			$outbound_result=array();
		}
	}
	$order_ids_inhouse=array();
	foreach ($inhouse_result as $data) 
	{
		$party_sourceId=$data[csf('party_source')];
		$batch_idArr[$data[csf('batch_id')]]=$data[csf('batch_id')];
		if(!empty($data[csf('order_id')]))
		{
			if($party_sourceId==1)
			{
				$order_ids_inhouse[]=$data[csf('order_id')];
			}
			else
			{
				$subCon_po_idArr[$data[csf('order_id')]]=$data[csf('order_id')];
			}
		}
	}
	$order_ids=array();
	foreach ($outbound_result as $data) 
	{
		if(!empty($data[csf('order_id')]))
		{
			$order_ids[]=$data[csf('order_id')];
			$order_ids_inhouse[]=$data[csf('order_id')];
		}
	}
	$order_ids=array_unique($order_ids);
	 $job_inbound_arr=array();
	//$sql_inbound_job="SELECT a.job_no, a.buyer_name, a.style_ref_no, b.id, b.po_number,b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active!=0 and a.is_deleted=0  and b.status_active!=0 and b.is_deleted=0 ".where_con_using_array($order_ids_inhouse,0,'b.id');
	 $sql_inbound_job="select a.id as JOB_ID, a.job_no AS JOB_NO,a.style_ref_no as STYLE_REF_NO,a.buyer_name as BUYER_NAME, b.id AS ID,b.po_number as PO_NUMBER,b.grouping as GROUPING, c.item_number_id AS ITEM_NUMBER_ID, c.country_id AS COUNTRY_ID, c.color_number_id AS COLOR_NUMBER_ID, c.size_number_id AS SIZE_NUMBER_ID, c.order_quantity AS ORDER_QUANTITY, c.plan_cut_qnty AS PLAN_CUT_QNTY, c.country_ship_date AS COUNTRY_SHIP_DATE, c.article_number AS ARTICLE_NUMBER, d.costing_per AS COSTING_PER,d.EXCHANGE_RATE from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_mst d where a.id=b.job_id and b.id=c.po_break_down_id  and a.id=d.job_id and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1".where_con_using_array($order_ids_inhouse,0,'b.id');
	//echo $sql_inbound_job;
	$sql_job_inbound_result =sql_select($sql_inbound_job);
	foreach($sql_job_inbound_result as $row)
	{
		$job_inbound_arr[$row['ID']]['job']=$row['JOB_NO'];
		$job_inbound_arr[$row['ID']]['style']=$row['STYLE_REF_NO'];
		$job_inbound_arr[$row['ID']]['buyer']=$row['BUYER_NAME'];
		$job_inbound_arr[$row['ID']]['po']=$row['PO_NUMBER'];
		$job_inbound_arr[$row['ID']]['rate']=$row['EXCHANGE_RATE'];
		$po_number_arr[$row['ID']]=$row['PO_NUMBER'];
		$job_inbound_arr[$row['ID']]['int_ref']=$row['GROUPING'];
		
		$costingPerQty=0;
		if($row['COSTING_PER']==1) $costingPerQty=12;
		elseif($row['COSTING_PER']==2) $costingPerQty=1;	
		elseif($row['COSTING_PER']==3) $costingPerQty=24;
		elseif($row['COSTING_PER']==4) $costingPerQty=36;
		elseif($row['COSTING_PER']==5) $costingPerQty=48;
		else $costingPerQty=0;
		
		$costingPerArr[$row['JOB_ID']]=$costingPerQty;
		
		$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty']+=$row['ORDER_QUANTITY'];
		$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
		
		$po_arr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'].=$row['COUNTRY_ID'].',';
		
		$poCountryArr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty']+=$row['ORDER_QUANTITY'];
		$poCountryArr[$row['JOB_ID']][$row['ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
		
		//$reqQtyAmtArr[$row['JOB_ID']][$row['ID']]['poqty']+=$row['ORDER_QUANTITY'];
		//$reqQtyAmtArr[$row['JOB_ID']][$row['ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
		$jobIdArr[$row['JOB_ID']]=$row['JOB_ID'];
	}
$gmtsitemRatioSql="select job_id AS JOB_ID, gmts_item_id AS GMTS_ITEM_ID, set_item_ratio AS SET_ITEM_RATIO from wo_po_details_mas_set_details where 1=1".where_con_using_array($jobIdArr,0,'job_id');
//echo $gmtsitemRatioSql; die;
$gmtsitemRatioSqlRes = sql_select($gmtsitemRatioSql);
$jobItemRatioArr=array();
foreach($gmtsitemRatioSqlRes as $row)
{
	$jobItemRatioArr[$row['JOB_ID']][$row['GMTS_ITEM_ID']]=$row['SET_ITEM_RATIO'];
}
unset($gmtsitemRatioSqlRes);
$sqlContrast="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.gmts_color_id as COLOR_NUMBER_ID, a.contrast_color_id AS CONTRAST_COLOR_ID from wo_pre_cos_fab_co_color_dtls a where 1=1 and a.status_active=1 and a.is_deleted=0 ".where_con_using_array($jobIdArr,0,'a.job_id');
//echo $sqlContrast; die;
$sqlContrastRes = sql_select($sqlContrast);
$sqlContrastArr=array();
foreach($sqlContrastRes as $row)
{
	$sqlContrastArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]=$row['CONTRAST_COLOR_ID'];
}
unset($sqlContrastRes);
//Stripe Details
$sqlStripe="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.po_break_down_id as POID, a.item_number_id AS ITEM_NUMBER_ID, a.color_number_id as COLOR_NUMBER_ID, a.stripe_color as STRIPE_COLOR, a.size_number_id as SIZE_NUMBER_ID, a.fabreq as FABREQ, a.yarn_dyed as YARN_DYED from wo_pre_stripe_color a where 1=1 and a.status_active=1 and a.is_deleted=0".where_con_using_array($jobIdArr,0,'a.job_id');
// echo $sqlStripe; 
$sqlStripeRes = sql_select($sqlStripe);
$sqlStripeArr=array();
foreach($sqlStripeRes as $row)
{
	$sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['strip'][$row['STRIPE_COLOR']]=$row['STRIPE_COLOR'];
	$sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['fabreq'][$row['STRIPE_COLOR']]=$row['FABREQ'];
}
unset($sqlStripeRes);

$sqlfab="select a.job_id AS JOB_ID, a.id AS ID, a.item_number_id AS ITEM_NUMBER_ID, a.fab_nature_id AS FAB_NATURE_ID, a.color_type_id AS COLOR_TYPE_ID, a.fabric_source as FABRIC_SOURCE, a.color_size_sensitive AS COLOR_SIZE_SENSITIVE, a.construction AS CONSTRUCTION,a.LIB_YARN_COUNT_DETER_ID, a.gsm_weight AS GSM_WEIGHT, a.uom AS UOM, b.po_break_down_id AS POID, b.color_number_id AS COLOR_NUMBER_ID, b.gmts_sizes AS SIZE_NUMBER_ID, b.cons AS CONS, b.requirment AS REQUIRMENT, b.rate as RATE
from wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b
where 1=1 and a.id=b.pre_cost_fabric_cost_dtls_id and b.cons!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0".where_con_using_array($jobIdArr,0,'a.job_id');
//echo $sqlfab; die;
$sqlfabRes = sql_select($sqlfab);
$fabIdWiseGmtsDataArr=array();
foreach($sqlfabRes as $row)
{
	$poQty=$planQty=$costingPer=$itemRatio=$finReq=$greyReq=$finAmt=$greyAmt=0;
	
	$fabIdWiseGmtsDataArr[$row['ID']]['item']=$row['ITEM_NUMBER_ID'];
	$fabIdWiseGmtsDataArr[$row['ID']]['deter_id']=$row['LIB_YARN_COUNT_DETER_ID'];
	$fabIdWiseGmtsDataArr[$row['ID']]['fnature']=$row['FAB_NATURE_ID'];
	$fabIdWiseGmtsDataArr[$row['ID']]['sensitive']=$row['COLOR_SIZE_SENSITIVE'];
	$fabIdWiseGmtsDataArr[$row['ID']]['color_type']=$row['COLOR_TYPE_ID'];
	$fabIdWiseGmtsDataArr[$row['ID']]['uom']=$row['UOM'];
	
	$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
	$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
	$costingPer=$costingPerArr[$row['JOB_ID']];
	$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$row['ITEM_NUMBER_ID']];
	
	$finReq=($planQty/$itemRatio)*($row['CONS']/$costingPer);
	$greyReq=($planQty/$itemRatio)*($row['REQUIRMENT']/$costingPer);
	
	$finAmt=$finReq*$row['RATE'];
	$greyAmt=$greyReq*$row['RATE'];
	
}
unset($sqlfabRes);
//Convaersion Details
$sqlConv="SELECT a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id AS PRECOSTID, a.po_break_down_id as POID, a.color_number_id as COLOR_NUMBER_ID, a.gmts_sizes as SIZE_NUMBER_ID, a.dia_width AS DIA_WIDTH, a.cons AS CONS, a.requirment AS REQUIRMENT, b.id AS CONVERTION_ID, b.cons_process AS CONS_PROCESS, b.req_qnty AS REQ_QNTY, b.process_loss AS PROCESS_LOSS, b.avg_req_qnty AS AVG_REQ_QNTY, b.charge_unit AS CHARGE_UNIT, b.amount as AMOUNT, b.color_break_down AS COLOR_BREAK_DOWN, c.body_part_id from wo_pre_cos_fab_co_avg_con_dtls a, wo_pre_cost_fab_conv_cost_dtls b, wo_pre_cost_fabric_cost_dtls c  where 1=1 and a.pre_cost_fabric_cost_dtls_id=c.id and a.pre_cost_fabric_cost_dtls_id=b.fabric_description and a.cons!=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.cons_process in(1) ".where_con_using_array($jobIdArr,0,'a.job_id');
//echo $sqlConv; die;b.cons_process
$sqlConvRes = sql_select($sqlConv);
$convConsRateArr=array();
foreach($sqlConvRes as $row)
{
	$id=$row['CONVERTION_ID'];
	$colorBreakDown=$row['COLOR_BREAK_DOWN'];
	if($colorBreakDown !="")
	{
		$arr_1=explode("__",$colorBreakDown);
		for($ci=0;$ci<count($arr_1);$ci++)
		{
			$arr_2=explode("_",$arr_1[$ci]);
			$convConsRateArr[$id][$arr_2[0]][$arr_2[3]]['rate']=$arr_2[1];
			$convConsRateArr[$id][$arr_2[0]][$arr_2[3]]['cons']=$arr_2[4];
		}
	}
}
//echo "ff"; die;
foreach($sqlConvRes as $row)
{
	$poQty=$planQty=$costingPer=$itemRatio=$consQnty=$reqqnty=$convAmt=0;
	$gmtsItem=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['item'];
	
	$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$gmtsItem][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
	$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$gmtsItem][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
	$costingPer=$costingPerArr[$row['JOB_ID']];
	$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$gmtsItem];
	$deter_id=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['deter_id'];
	
	$colorTypeId=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['color_type']; 
	$colorSizeSensitive=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['sensitive'];
	$consProcessId=$row['CONS_PROCESS'];
	$stripe_color=$sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['strip'];
	
	if(($colorTypeId==2 || $colorTypeId==3 || $colorTypeId==4 || $colorTypeId==6 || $colorTypeId==31 || $colorTypeId==32 || $colorTypeId==33 || $colorTypeId==34) && $consProcessId==30 && count($stripe_color)>0)
	{
		$qnty=0; $convrate=0;
		foreach($stripe_color as $stripe_color_id)
		{
			$stripe_color_cons_dzn=$convConsRateArr[$row['CONVERTION_ID']][$row['COLOR_NUMBER_ID']][$stripe_color_id]['cons'];
			$convrate=$convConsRateArr[$row['CONVERTION_ID']][$row['COLOR_NUMBER_ID']][$stripe_color_id]['rate'];
			
			$requirment=$stripe_color_cons_dzn-($stripe_color_cons_dzn*$row['PROCESS_LOSS'])/100;
			$qnty=($planQty/$itemRatio)*($requirment/$costingPer);

			if($convrate>0){
				$reqqnty+=$qnty;
				$convAmt+=$qnty*$convrate;
			}
		}
	}
	else
	{
		$convrate=$requirment=$reqqnty=0;
		$rateColorId=$row['COLOR_NUMBER_ID'];
		if($colorSizeSensitive==3) $rateColorId=$sqlContrastArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]; else $rateColorId=$row['COLOR_NUMBER_ID'];

		if($row['COLOR_BREAK_DOWN']!="") $convrate=$convConsRateArr[$row['CONVERTION_ID']][$row['COLOR_NUMBER_ID']][$rateColorId]['rate']; else $convrate=$row['CHARGE_UNIT'];
		
		if($convrate>0){
			$requirment=$row['REQUIRMENT']-($row['REQUIRMENT']*$row['PROCESS_LOSS'])/100;
			$qnty=($planQty/$itemRatio)*($requirment/$costingPer);
			$reqqnty+=$qnty;
			$convAmt+=$qnty*$convrate;
		}
	}
	
	//echo $planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
	/* $reqQtyAmtArr[$deter_id][$row['POID']]['conv_qty']+=$reqqnty;
	$reqQtyAmtArr[$deter_id][$row['POID']]['conv_amt']+=$convAmt; */
	$reqQtyAmtArr[$row[csf('body_part_id')]][$deter_id][$row['POID']]['conv_qty']+=$reqqnty;
	$reqQtyAmtArr[$row[csf('body_part_id')]][$deter_id][$row['POID']]['conv_amt']+=$convAmt;
}
unset($sqlConvRes);
 //print_r($reqQtyAmtArr); 
	$order_no_arr = return_library_array("select id, order_no from subcon_ord_dtls where status_active=1 ".where_con_using_array($subCon_po_idArr,0,'id'), "id", "order_no");
	$SQL_Batch= "select id,item_description,gsm,fin_dia,body_part_id from pro_batch_create_dtls where status_active=1 ".where_con_using_array($batch_idArr,0,'mst_id') ." ";
	$sql_batch_result =sql_select($SQL_Batch);
	 foreach($sql_batch_result as $row)
	 {
	 	$product_dtls_arr[$row[csf('id')]]=$row[csf('item_description')];
	 	$batch_data_arr[$row[csf('id')]]['gsm']=$row[csf('gsm')];
	 	$batch_data_arr[$row[csf('id')]]['fin_dia']=$row[csf('fin_dia')];
	  	$batch_data_arr[$row[csf('id')]]['body']=$row[csf('body_part_id')];
	 	//$batch_data_arr[$row[csf('id')]]['int_ref']=$row[csf('grouping')];
	 }
	 $sql_dye_fin_bill="SELECT b.order_id, b.process_id, b.item_id,b.gsm, b.dia,b.batch_id,b.color_id, b.delivery_qty,b.gray_qty from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id ".where_con_using_array($subCon_po_idArr,0,'b.order_id') ." and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0";
	$sql_dye_fin_result =sql_select($sql_dye_fin_bill);
	 foreach($sql_dye_fin_result as $row)
	 {
	 	$dye_fin_delivery_arr[$row[csf('order_id')]][$row[csf('item_id')]]['gray_qty']+=$row[csf('gray_qty')];
		$dye_fin_delivery_arr[$row[csf('order_id')]][$row[csf('item_id')]]['gsm']=$row[csf('gsm')];
		$dye_fin_delivery_arr[$row[csf('order_id')]][$row[csf('item_id')]]['dia']=$row[csf('dia')];
	 }
	 unset($sql_dye_fin_result);
	$job_outbound_arr=array();
	 $sql_job="SELECT a.subcon_job as job_no, a.party_id as buyer_name, b.cust_style_ref as style_ref_no, b.id, b.order_no as po_number , null as grouping,c.item_id,c.rate,c.amount
	  from subcon_ord_mst a, subcon_ord_dtls b,subcon_ord_breakdown c where a.subcon_job=b.job_no_mst and c.order_id=b.id and c.mst_id=a.id ".where_con_using_array($subCon_po_idArr,0,'b.id') ." and a.status_active=1 and a.is_deleted=0";
	//echo $sql_job;
	$sql_job_result =sql_select($sql_job);
	 foreach($sql_job_result as $row)
	 {
	 	$job_outbound_arr[$row[csf('id')]]['job']=$row[csf('job_no')];
	 	$job_outbound_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
	 	$job_outbound_arr[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
	 	$job_outbound_arr[$row[csf('id')]]['po']=$row[csf('po_number')];
	 	$job_outbound_arr[$row[csf('id')]]['int_ref']=$row[csf('grouping')];
		$job_outbound_rate_arr[$row[csf('id')]][$row[csf('item_id')]]['rate']=$row[csf('rate')];
		$job_outbound_rate_arr[$row[csf('id')]][$row[csf('item_id')]]['amount']=$row[csf('amount')];
	 }
	$sql_order=sql_select("SELECT a.id, a.company_id, a.recv_number,a.booking_id,a.booking_no, a.receive_basis, a.receive_date, a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id, b.febric_description_id,b.trans_id, b.gsm,b.room,b.rack,b.self,b.bin_box,c.qnty,c.rate, c.qc_pass_qnty,c.roll_no,b.width,b.body_part_id,b.yarn_lot,b.brand_id,b.shift_name,b.floor_id,b.machine_no_id,b.yarn_count,b.color_id,b.color_range_id, c.barcode_no, c.id as roll_id, c.po_breakdown_id, c.booking_without_order, c.is_sales, c.qc_pass_qnty_pcs,b.stitch_length
    FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
    WHERE a.id=b.mst_id and b.id=c.dtls_id ".where_con_using_array($order_ids_inhouse,0,'c.po_breakdown_id') ."  and c.status_active=1 and c.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $floor_con ");
	
    $order_wise_data=array();
    $buyer_wise_data=array();
    $floor_wise_data=array();
    foreach ($sql_order as $data) 
    {
    	$order_wise_data[$data[csf('po_breakdown_id')]]['recv_number']=$data[csf('recv_number')];
    	$order_wise_data[$data[csf('po_breakdown_id')]]['booking_no']=$data[csf('booking_no')];
    	$order_wise_data[$data[csf('po_breakdown_id')]]['febric_description_id']=$data[csf('febric_description_id')];
    	$order_wise_data[$data[csf('po_breakdown_id')]]['gsm']=$data[csf('gsm')];
    	$order_wise_data[$data[csf('po_breakdown_id')]]['width']=$data[csf('width')];
    	$order_wise_data[$data[csf('po_breakdown_id')]]['body_part_id']=$data[csf('body_part_id')];
    	$order_wise_data[$data[csf('po_breakdown_id')]]['yarn_lot']=$data[csf('yarn_lot')];
    	$order_wise_data[$data[csf('po_breakdown_id')]]['floor_id']=$data[csf('floor_id')];
    	$order_wise_data[$data[csf('po_breakdown_id')]]['stitch_length']=$data[csf('stitch_length')];
    	$order_wise_data[$data[csf('po_breakdown_id')]]['yarn_count']=$data[csf('yarn_count')];
		$order_wise_data[$data[csf('po_breakdown_id')]]['rcv_qnty']+=$data[csf('receive_qnty')];
    }
		$inbound_sql_data=sql_select("SELECT a.id, a.entry_form, a.receive_basis, a.recv_number_prefix_num, a.challan_no, a.receive_date, a.booking_id as bookingno, TO_CHAR(a.insert_date,'YYYY') as year,	b.batch_id, b.prod_id, b.body_part_id, b.fabric_description_id, b.color_id, b.dia_width_type, sum(c.quantity) as rec_qnty, sum(b.no_of_roll) as carton_roll, c.po_breakdown_id, d.booking_no_id, d.booking_no,d.process_id FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, pro_batch_create_mst d 	 WHERE a.id=b.mst_id and b.id=c.dtls_id and d.id=b.batch_id and c.trans_type=1 and c.entry_form in (7,37,66,68) and c.trans_id!=0 and a.entry_form in (7,37,66,68)  AND a.company_id=$cbo_company_id and c.quantity>0  and a.receive_basis in (2,4,5,9,11) and a.item_category=2  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.batch_against in (0,1,3) and nvl(d.booking_without_order,0)=0 
		group by a.id, a.entry_form, a.receive_basis, a.recv_number_prefix_num, a.challan_no, a.receive_date, a.booking_id, a.insert_date, 
		b.batch_id, b.prod_id, b.body_part_id, b.fabric_description_id, b.color_id, b.dia_width_type, c.po_breakdown_id, d.booking_no_id,
		d.booking_no,d.process_id order by a.recv_number_prefix_num DESC ");
		foreach($inbound_sql_data as $row){
		$inbound_order_data[$row[csf('po_breakdown_id')]]['bill_rcv_qnty']+=$row[csf('rec_qnty')];
		$inbound_order_data[$row[csf('po_breakdown_id')]]['process_id']=$row[csf('process_id')];
		}
		
		$outbound_sql_data=sql_select("SELECT a.id as mst_id, a.entry_form, a.receive_basis, a.recv_number_prefix_num, a.challan_no, a.receive_date, b.booking_id as bookingno, TO_CHAR(a.insert_date,'YYYY') as year, b.batch_id, b.prod_id, b.body_part_id, b.febric_description_id as fabric_description_id, b.color_id, sum(b.batch_issue_qty) as rec_qnty, sum(b.grey_used) as grey_qty, b.id as dtls_id, b.order_id as po_breakdown_id, b.booking_id, b.booking_no, b.process_id FROM inv_receive_mas_batchroll a, pro_grey_batch_dtls b WHERE a.id=b.mst_id and a.entry_form=92 AND a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   group by a.id, a.entry_form, a.receive_basis, a.recv_number_prefix_num, a.challan_no, a.receive_date, b.booking_id, a.insert_date, b.batch_id, b.prod_id, b.body_part_id, b.febric_description_id, b.color_id,
		b.order_id, b.id, b.booking_id, b.booking_no,b.process_id order by a.recv_number_prefix_num DESC ");
		foreach($outbound_sql_data as $row){
		$outbound_order_data[$row[csf('po_breakdown_id')]]['bill_rcv_qnty']+=$row[csf('rec_qnty')];
		$outbound_order_data[$row[csf('po_breakdown_id')]]['process_id']=$row[csf('process_id')];
		}
	if($report_type==4)
	{
		$table_width=1690; $colspan="13";
	}
	else{
		$table_width=1620; $colspan="12";
	}
	ob_start();
	?>
    <fieldset style="width:<?php echo $table_width+20;?>" >	
       
        <table class="rpt_table" border="1" rules="all" width="<?=$table_width; ?>" cellpadding="0" cellspacing="0" style="margin-top: 50px;" >
            <thead>
                <tr>
                	<th style="word-break: break-all;" width="40">SL</th>
                    <th style="word-break: break-all;" width="110">Bill No</th>
                    <th style="word-break: break-all;" width="60">Date</th>
                    <th style="word-break: break-all;" width="100">Party</th>
                    <th style="word-break: break-all;" width="110">Buyer Name</th>
                    <th style="word-break: break-all;" width="110">Style Name</th>
                    <th style="word-break: break-all;" width="115">Job No</th>
					<th style="word-break: break-all;" width="100">Order No</th>
					 
                    <th style="word-break: break-all;" width="110">Fabric<br>Const./Comp.</th>
                    
                    <th style="word-break: break-all;" width="70">GSM</th>
                    <th style="word-break: break-all;" width="80">F/Dia</th>
					<th style="word-break: break-all;" width="100">Body Part</th>
                    <?php if($report_type==4){ ?>
                    <th style="word-break: break-all;" width="80">Fabric Color</th>
                	<?php } ?>
					 
                    <th style="word-break: break-all;" width="80">Bill Qty</th>
				 
                    <th style="word-break: break-all;" width="70">Rate/Kg</th>
                    <th style="word-break: break-all;" width="70">Bill Value</th>
                    <th style="word-break: break-all;" width="70">Budget Rate(TK)</th>
                    <th style="word-break: break-all;" width="70">Budget Value(TK)</th>
                    <th style="word-break: break-all;">Currency</th>
                    
                </tr>
            </thead>
        </table>

		<table border="1" class="rpt_table" rules="all" width="<?=$table_width; ?>" id="table_body"  cellpadding="0" cellspacing="0">
           <tbody>
					<?php 
						$source='';
						$total_bill=0;
						$total_amount=0;
						$sub_bill=0;
						$sub_amount=0;$total_bill=0;$tot_budget_amount=0;
						$i=1;$usd_id=2;
						foreach($inhouse_result as $row)
						{
										$color_names = '';
										$colorIds = array_unique(explode(",", $row[csf('color_id')]));
										foreach ($colorIds as $color_id)
										{
										$color_names .= $color_arr[$color_id] . ",";
										}
		                       			$color_names = chop($color_names, ',');
										$item_id=$row[csf('item_id')];$budget_avg_rate=0;
								  //	echo $row[csf('party_source')].'=';
										if($row[csf('party_source')]==1) //Party Source -In House $row[csf('party_source')]==1
										{
											$inbound_rcv_qnty=$inbound_order_data[$row[csf('order_id')]]['bill_rcv_qnty'];
											$process_id=$inbound_order_data[$row[csf('order_id')]]['process_id'];
											$process=$conversion_cost_head_array[$process_id];
											$buyer=$buyer_arr[$job_inbound_arr[$row[csf('order_id')]]['buyer']];
											$style=$job_inbound_arr[$row[csf('order_id')]]['style'];
											$job_no=$job_inbound_arr[$row[csf('order_id')]]['job'];
											$construct=$product_dtls_arr[$item_id];//$constructtion_arr[$order_wise_data[$row[csf('order_id')]]['febric_description_id']];
											$gsm=$prod_fabric_dtls_arr[$item_id]['gsm'];
											$fin_dia=$prod_fabric_dtls_arr[$item_id]['dia'];//$order_wise_data[$row[csf('order_id')]]['width'];
											$body=$body_part[$order_wise_data[$row[csf('order_id')]]['body_part_id']];
											$yarn_comp=$composition_arr[$order_wise_data[$row[csf('order_id')]]['febric_description_id']];
											$determin_id=$product_fabric_dtls_arr[$item_id];
											$job_rate=$job_inbound_arr[$row[csf('order_id')]]['rate'];
											if($row[csf('currency')]==1 && $row[csf('rate')]>0)
											{
											$rate_kg=$row[csf('rate')];
											$amount_kg=$row[csf('amount')];
											}
											else if($row[csf('currency')]==2 && $row[csf('rate')]>0) //USD
											{
												$rate_kg=$row[csf('rate')]*$job_rate;
												$amount_kg=$row[csf('amount')]*$job_rate;
											}
											
											if($report_type==2) //Knitting........
											{
												
												/* $conv_qty=$reqQtyAmtArr[$determin_id][$row[csf('order_id')]]['conv_qty'];
												$conv_amt=$reqQtyAmtArr[$determin_id][$row[csf('order_id')]]['conv_amt']; */
												$conv_qty=$reqQtyAmtArr[$row[csf('body_part_id')]][$determin_id][$row[csf('order_id')]]['conv_qty'];
												$conv_amt=$reqQtyAmtArr[$row[csf('body_part_id')]][$determin_id][$row[csf('order_id')]]['conv_amt'];
												$avg_conv_rate=$conv_amt/$conv_qty;
												if($row[csf('currency')]==1 && $avg_conv_rate>0) //BDT
												{
													$budget_avg_rate=$avg_conv_rate*$job_rate;
												}
												else if($row[csf('currency')]==2 && $avg_conv_rate>0) //USD
												{
													$budget_avg_rate=$avg_conv_rate*$job_rate;
												}
												else $budget_avg_rate=0;
											}
											 //	echo $determin_id.'<br>';
										}
										else if($row[csf('party_source')]==2) //Party Source -In Bound $row[csf('party_source')]==1
										{
											//$dye_fin_delivery_arr[$row[csf('order_id')]][$row[csf('item_id')]]['gsm']=$row[csf('gsm')];
											//$dye_fin_delivery_arr[$row[csf('order_id')]][$row[csf('item_id')]]['dia']
											$conversion_date=change_date_format($row[csf('bill_date')], "d-M-y", "-",1);
											$currency_rate=set_conversion_rate($usd_id,$conversion_date,$cbo_company_id );
											$inbound_rcv_qnty=$dye_fin_delivery_arr[$row[csf('order_id')]][$item_id]['gray_qty'];
											$process=$row[csf('add_process_name')];
											$buyer=$buyer_arr[$job_outbound_arr[$row[csf('order_id')]]['buyer']];
											$style=$job_outbound_arr[$row[csf('order_id')]]['style'];
											$job_no=$job_outbound_arr[$row[csf('order_id')]]['job'];
											$construct=$product_dtls_arr[$item_id];
											$gsm=$dye_fin_delivery_arr[$row[csf('order_id')]][$item_id]['gsm'];//$batch_data_arr[$item_id]['gsm'];
											$fin_dia=$dye_fin_delivery_arr[$row[csf('order_id')]][$item_id]['dia'];
											$body=$body_part[$row[csf('body_part_id')]];//$batch_data_arr[$item_id]['body'];
											$yarn_comp=$product_compsition_dtls_arr[$item_id];
											$job_sub_rate=$job_outbound_rate_arr[$row[csf('order_id')]][$item_id]['rate'];
											$job_sub_amount=$job_outbound_rate_arr[$row[csf('order_id')]][$item_id]['amount'];
											if($row[csf('currency')]==1 && $row[csf('rate')]>0)
											{
											$rate_kg=$row[csf('rate')];
											$amount_kg=$row[csf('amount')];
											}
											else if($row[csf('currency')]==2 && $row[csf('rate')]>0) //USD
											{
												$rate_kg=$row[csf('rate')]*$job_sub_rate;
												$amount_kg=$row[csf('amount')]*$job_sub_rate;
											}
											//echo $job_sub_rate.'TT';
											if($report_type==2) //Knitting........
											{
												if($row[csf('currency')]==1) //BDT
												{
													$job_sub_rate=$job_sub_rate;
												}
												else $job_sub_rate=$currency_rate*$job_sub_rate;
												
												if($job_sub_rate>0)
												{
												$budget_avg_rate=$job_sub_rate;
												}
												else $budget_avg_rate=0;
												
												$budget_value=$row[csf('delivery_qty')]*$budget_avg_rate;
											}
											// $product_compsition_dtls_arr[$item_id];
										 	// $product_compsition_dtls_arr[$item_id].'<br>';
											 // $product_dtls_arr
										}
										else{ //Party Source -In Bound Subcon
											$job_rate=$job_inbound_arr[$row[csf('order_id')]]['rate'];
											$inbound_rcv_qnty=$dye_fin_delivery_arr[$row[csf('order_id')]][$item_id]['gray_qty'];
											$process=$row[csf('add_process_name')];
											$buyer=$buyer_arr[$job_outbound_arr[$row[csf('order_id')]]['buyer']];
											$style=$job_outbound_arr[$row[csf('order_id')]]['style'];
											$job_no=$job_outbound_arr[$row[csf('order_id')]]['job'];
											$construct=$product_dtls_arr[$item_id];
											$yarn_comp=$product_compsition_dtls_arr[$item_id];
											$gsm=$prod_fabric_dtls_arr[$item_id]['gsm'];
											$fin_dia=$prod_fabric_dtls_arr[$item_id]['dia'];
											$body=$body_part[$batch_data_arr[$item_id]['body']];
											$determin_id=$product_fabric_dtls_arr[$row[csf('order_id')]];
											if($row[csf('currency')]==1 && $row[csf('rate')]>0)
											{
											$rate_kg=$row[csf('rate')];
											$amount_kg=$row[csf('amount')];
											}
											else if($row[csf('currency')]==2 && $row[csf('rate')]>0) //USD
											{
												$rate_kg=$row[csf('rate')]*$job_rate;
												$amount_kg=$row[csf('amount')]*$job_rate;
											}
											
											if($report_type==2) //Knitting........
											{
												/* $conv_qty=$reqQtyAmtArr[$determin_id][$row[csf('order_id')]]['conv_qty'];
												$conv_amt=$reqQtyAmtArr[$determin_id][$row[csf('order_id')]]['conv_amt']; */
												$conv_qty=$reqQtyAmtArr[$row[csf('body_part_id')]][$determin_id][$row[csf('order_id')]]['conv_qty'];
												$conv_amt=$reqQtyAmtArr[$row[csf('body_part_id')]][$determin_id][$row[csf('order_id')]]['conv_amt'];
												$avg_conv_rate=$conv_amt/$conv_qty;
												
												if($row[csf('currency')]==1 && $avg_conv_rate>0) //BDT
												{
													$budget_avg_rate=$avg_conv_rate*$job_rate;
												}
												else if($row[csf('currency')]==2 && $avg_conv_rate>0) //USD
												{
													$budget_avg_rate=$avg_conv_rate*$job_rate;
												}
												else $budget_avg_rate=0;
												
												/*if($conv_amt>0)
												{
												$budget_avg_rate=$conv_amt/$conv_qty;
												}
												else $budget_avg_rate=0;*/
											}
											
										}
										
											if($report_type==2) //Knitting........
											{
											$budget_value=$row[csf('delivery_qty')]*$budget_avg_rate;
											}
											else $budget_value='';
	                        
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
									<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>">
										<td style="word-break: break-all;" width="40"><?=$i; ?></td>
										<td style="word-break: break-all;" width="110" title="In:"><p><?=$row[csf('bill_no')]; ?></p></td>
										<td style="word-break: break-all;" width="60"><p><?=change_date_format($row[csf('bill_date')]); ?></p></td>
										
										<td style="word-break: break-all;" width="100">
											<p>
												<?

													if($row[csf('party_source')]==1)
													{
														echo $company_arr[$row[csf("party_id")]];
													}else{
														echo $buyer_arr[$row[csf("party_id")]];
													}
												 ?>
										 	
										 </p>
										</td>
										
										<td style="word-break: break-all;"  width="110"><p><?=$buyer ; ?></p></td>
										<td style="word-break: break-all;" width="110"><p><?=$style; ?></p></td>
										<td style="word-break: break-all;" width="115"><p><?=$job_no; ?></p></td>
										<td style="word-break: break-all;" width="100"><p><?
									
										if($row[csf('party_source')]==1)
										{
											echo $po_number_arr[$row[csf('order_id')]];;
										}else{
											echo $order_no_arr[$row[csf('order_id')]];
										}
										
										?></p></td>
										 
										<td style="word-break: break-all;" width="110" title="DeterId=<?=$item_id;?>" ><p><?=$construct; ?></p></td>
										 
										<td style="word-break: break-all;" width="70"><p><?=$gsm; ?></p></td>
										<td style="word-break: break-all;" width="80"><p><?=$fin_dia; ?></p></td>
										<td style="word-break: break-all;" width="100"><p><?=$body; ?></p></td>
										 <?php if($report_type==4){ ?>
					                    <td style="word-break: break-all;" width="80"> <p><?php echo $color_names; ?></p></td>
					                	<?php } ?>
										 
										<td style="word-break: break-all;" width="80" align="right"><p><?=number_format($row[csf('delivery_qty')],2); ?></p></td>
										 
										<td style="word-break: break-all;" width="70" align="right"><p><?=number_format($rate_kg,2); ?></p></td>
										<td style="word-break: break-all;" width="70" align="right"><p><?=number_format($rate_kg*$row[csf('delivery_qty')],2); ?></p></td>
                                        <td style="word-break: break-all;" width="70" align="right"><p><?=number_format($budget_avg_rate,2); ?></p></td>
                                          <td style="word-break: break-all;" title="Ex Rate=<?=$job_rate;?>" width="70" align="right"><p><?=number_format($budget_value,2); ?></p></td>
										<td title="<?=$currency[$row[csf('currency')]]; ?>" style="word-break: break-all;"><p>BDT</p></td>
										
									</tr>
								<?
								$source=$row[csf('party_source')];
								$total_bill+=$row[csf('delivery_qty')];
								$total_bill_qnty+=$inbound_rcv_qnty;
								$total_amount+=$rate_kg*$row[csf('delivery_qty')];
								$sub_bill+=$row[csf('delivery_qty')];
								$sub_amount+=$amount_kg;
								$tot_budget_amount+=$budget_value;
								$i++;
							
						}
						
						
						$process="";
						foreach($outbound_result as $row)
						{

                            $color_names = '';
		                        $colorIds = array_unique(explode(",", $row[csf('color_id')]));
		                        foreach ($colorIds as $color_id)
								{
		                            $color_names .= $color_arr[$color_id] . ",";
		                        }
		                        $color_names = chop($color_names, ',');
								
                            if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$item_id=$row[csf('item_id')];
							$outbound_rcv_qnty=$outbound_order_data[$row[csf('order_id')]]['bill_rcv_qnty'];
							$process_id=$outbound_order_data[$row[csf('order_id')]]['process_id'];
							$process=$conversion_cost_head_array[$process_id];
							$determin_id=$product_fabric_dtls_arr[$item_id];
							$job_rate=$job_inbound_arr[$row[csf('order_id')]]['rate'];
							$conv_qty=$conv_amt=$avg_conv_rate=$budget_avg_rate=0;
							if($row[csf('currency')]==1 && $row[csf('rate')]>0)
							{
							$rate_kg=$row[csf('rate')];
							$amount_kg=$row[csf('amount')];
							}
							else if($row[csf('currency')]==2 && $row[csf('rate')]>0) //USD
							{
								$rate_kg=$row[csf('rate')]*$job_rate;
								$amount_kg=$row[csf('amount')]*$job_rate;
							}
							
							if($report_type==2) //Knitting........
							{
								/* $conv_qty=$reqQtyAmtArr[$determin_id][$row[csf('order_id')]]['conv_qty'];
								$conv_amt=$reqQtyAmtArr[$determin_id][$row[csf('order_id')]]['conv_amt']; */
								$conv_qty=$reqQtyAmtArr[$row[csf('body_part_id')]][$determin_id][$row[csf('order_id')]]['conv_qty'];
								$conv_amt=$reqQtyAmtArr[$row[csf('body_part_id')]][$determin_id][$row[csf('order_id')]]['conv_amt'];
							if($conv_amt>0) //Usd
							{
							$avg_conv_rate=$conv_amt/$conv_qty;
							}
							//echo $conv_amt.'='.$conv_qty.'<br>';
							if($row[csf('currency')]==1 && $conv_amt>0) //BDT
							{
								$budget_avg_rate=$avg_conv_rate*$job_rate;
							}
							else if($row[csf('currency')]==2 && $conv_amt>0) //Usd
							{
								$budget_avg_rate=$avg_conv_rate*$job_rate;
							}
							else $budget_avg_rate=0;
							//echo $avg_conv_rate.'='.$job_rate.'<br>';
							}
	                        if($report_type==2) //Knitting........
							{
							$budget_value=$row[csf('receive_qty')]*$budget_avg_rate;
							}
							else $budget_value='';
							
							$construct=$product_dtls_arr[$item_id];//$constructtion_arr[$order_wise_data[$row[csf('order_id')]]['febric_description_id']];
							$gsm=$prod_fabric_dtls_arr[$item_id]['gsm'];
							$fin_dia=$prod_fabric_dtls_arr[$item_id]['dia'];//$order_wise_data[$row[csf('order_id')]]['width'];
							$body=$body_part[$order_wise_data[$row[csf('order_id')]]['body_part_id']];

                            ?>
								<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>">
									<td style="word-break: break-all;" width="40"><?=$i; ?></td>
									<td style="word-break: break-all;" width="110" title="Out:"><p><?=$row[csf('bill_no')]; ?></p></td>
									<td style="word-break: break-all;" width="60"><p><?=change_date_format($row[csf('bill_date')]); ?></p></td>
									
									<td style="word-break: break-all;" width="100"><p><?=$supplier_arr[$row[csf('supplier_id')]]; ?></p></td>
									
									<td style="word-break: break-all;" width="110"><p><?=$buyer_arr[$job_inbound_arr[$row[csf('order_id')]]['buyer']] ; ?></p></td>
									<td style="word-break: break-all;" width="110"><p><?=$job_inbound_arr[$row[csf('order_id')]]['style']; ?></p></td>
									<td style="word-break: break-all;" width="115"><p><?=$job_inbound_arr[$row[csf('order_id')]]['job'] ; ?></p></td>
									<td style="word-break: break-all;" width="100"><p><?=$po_number_arr[$row[csf('order_id')]]; ?></p></td>
									 
									<td style="word-break: break-all;" width="110" title="<?=$item_id;?>" ><p><?=$construct; ?></p></td>
								 
									<td style="word-break: break-all;" width="70"><p><?=$gsm; ?></p></td>
									<td style="word-break: break-all;" width="80"><p><?=$fin_dia; ?></p></td>
									<td style="word-break: break-all;" width="100"><p><?=$body; ?></p></td>
									
									<?php if($report_type==4){ ?>
					                <td style="word-break: break-all;" width="80"> <p><?php echo $color_names; ?></p></td>
					                <?php } ?>
									 
									<td style="word-break: break-all;" width="80" align="right"><p><?=number_format($row[csf('receive_qty')],2); ?></p></td>
								 
									<td style="word-break: break-all;" width="70" align="right"><p><?=number_format($rate_kg,2); ?></p></td>
									<td style="word-break: break-all;" width="70" align="right"><p><?=number_format($rate_kg*$row[csf('receive_qty')],2); ?></p></td>
                                   <td style="word-break: break-all;" width="70" align="right"><p><?=number_format($budget_avg_rate,2); ?></p></td>
                                    <td style="word-break: break-all;" width="70" title="Ex Rate=<?=$job_rate;?>" align="right"><p><?=number_format($budget_value,2); ?></p></td>
									<td title="<?=$currency[$row[csf('currency')]]; ?>" style="word-break: break-all;"><p>BDT</p></td>
									
								</tr>
							<?
							
							$total_bill+=$row[csf('receive_qty')];
							$total_bill_qnty+=$outbound_rcv_qnty;
							$total_amount+=$row[csf('amount')];
							$sub_bill+=$row[csf('receive_qty')];
							$sub_amount+=$rate_kg*$row[csf('receive_qty')];
							$tot_budget_amount+=$budget_value;
							$i++;

                        }

					
						
							?>
			</tbody>
		</table>
		<table border="1" class="rpt_table" rules="all" width="<?=$table_width; ?>" cellpadding="0" cellspacing="0" >
			<tfoot >
			 	<?php $bgcolor="#efefef";  ?>
				<tr bgcolor="<?=$bgcolor; ?>" >

					<td style="word-break: break-all;" width="40"></td>
                    <td style="word-break: break-all;" width="110"></td>
                    <td style="word-break: break-all;" width="60"></td>
                    <td style="word-break: break-all;" width="100"></td>
                    <td style="word-break: break-all;" width="110"></td>
                    <td style="word-break: break-all;" width="110"></td>
                    <td style="word-break: break-all;" width="115"></td>
					<td style="word-break: break-all;" width="100"></td>
					 

                    <td style="word-break: break-all;" width="110"></td>
                    
                    <td style="word-break: break-all;" width="70"></td>

                    <td style="word-break: break-all;" width="80"></td>
					<td style="word-break: break-all;" width="100" align="right">Total</td>
                    <?php if($report_type==4){ ?>
                    <td style="word-break: break-all;" width="80"></td>
                	<?php } ?>
					 
                    <td style="word-break: break-all;" width="80" align="right" style="justify-content: right;tfont-weight: bold;" id="total_bill"><?=$total_bill;?></td>
					 
                    <td style="word-break: break-all;" width="70"></td>
                    <td style="word-break: break-all;" width="70" align="right" style="justify-content: right;font-weight: bold;" id="total_amount"><?=number_format($total_amount,2);?></td>
                     <td style="word-break: break-all;" width="70" align="right" style="justify-content: right;font-weight: bold;" ><??></td>
                      <td style="word-break: break-all;" width="70" align="right" style="justify-content: right;font-weight: bold;" id="total_budget_amount"><?=number_format($tot_budget_amount,2);?></td>
                    <td style="word-break: break-all;"></td>



 
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
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename####$type";
	exit();
}

if($action=="job_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	//echo $style_id;die;

	?>
    <script>
		
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
    	function check_all_data() 
		{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) 
			{
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
				
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) { 
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( strCon ) 
		{
			//alert(strCon);
				var splitSTR = strCon.split("_");
				var str = splitSTR[0];
				var selectID = splitSTR[1];
				var selectDESC = splitSTR[2];
				//$('#txt_individual_id' + str).val(splitSTR[1]);
				//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
				
				toggle( document.getElementById( 'tr_' + str), '#FFFFCC' );
				
				if( jQuery.inArray( selectID, selected_id ) == -1 ) {
					selected_id.push( selectID );
					selected_name.push( selectDESC );
					selected_no.push( str );				
				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == selectID ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 );
					selected_no.splice( i, 1 ); 
				}
				var id = ''; var name = ''; var job = ''; var num='';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ',';
					num += selected_no[i] + ','; 
				}
				id 		= id.substr( 0, id.length - 1 );
				name 	= name.substr( 0, name.length - 1 ); 
				num 	= num.substr( 0, num.length - 1 );
				//alert(num);
				$('#txt_selected_id').val( id );
				$('#txt_selected').val( name ); 
				$('#txt_selected_no').val( num );
		}
		
		/*function js_set_value( str ) { //alert(str);
		toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
		
		if( jQuery.inArray( $('#txt_serial_id' + str).val(), selected_id ) == -1 ) {
			selected_id.push( $('#txt_serial_id' + str).val() );
			selected_no.push( $('#txt_serial_no' + str).val() );
 		}




		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == $('#txt_serial_id' + str).val() ) break;
			}
			selected_id.splice( i, 1 );
			selected_no.splice( i, 1 );
		}
		var id = '';	var no = '';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
			no += selected_no[i] + ',';
		}
		id = id.substr( 0, id.length - 1 );
		no = no.substr( 0, no.length - 1 );
  		$('#txt_string_id').val( id );
		$('#txt_string_no').val( no );
	}
	 
	function fn_onClosed()
	{
		var txt_string = $('#txt_string').val();
		if(txt_string==""){ alert("Please Select The Serial"); return;}
		parent.emailwindow.hide();
	}*/
		
    </script>
    <?
	$party=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
	$job_year=str_replace("'","",$job_year);
	if($party!=0) $party_cond=" and a.party_id=$party"; else $party_cond="";
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
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	if($type==1){
		$sql = "SELECT a.id,a.job_no,a.job_no_prefix_num, a.buyer_name, a.style_ref_no,a.gmts_item_id, b.id, b.po_number,b.grouping,$select_date as year from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and  a.company_name=$company $buyer_cond  $job_year_cond";
		$arr=array(3=>$buyer_arr,5=>$garments_item);
		echo create_list_view("list_view", "Po Number,Job No,Year,Buyer,Style Ref No,Item Name","160,90,100,100,100,80","700","310",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "0,0,0,buyer_name,0,gmts_item_id", $arr, "po_number,job_no_prefix_num,year,buyer_name,style_ref_no,gmts_item_id", "","setFilterGrid('list_view',-1)","0,0,0,0,0,0","",1) ;	
	}else{
		$sql="select a.id, b.cust_style_ref, a.job_no_prefix_num,$select_date as year,b.order_no,b.cust_buyer,b.item_group from  subcon_ord_mst a, subcon_ord_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.company_id='$company' $party_cond group by a.id, b.cust_style_ref, a.job_no_prefix_num, a.insert_date ,b.order_no,b.cust_buyer,b.item_group  order by a.id desc";	
		echo create_list_view("list_view", "Po Number,Job No,Year,Buyer,Style Ref No,Item Name","160,90,100,100,100,80","700","310",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "0", $arr, "order_no,job_no_prefix_num,year,cust_buyer,cust_style_ref,item_group", "","setFilterGrid('list_view',-1)","0","",1) ;	
	}
	
	//echo $sql; die;

	
 //  echo $sql; die;
	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	echo "<input type='hidden' id='txt_year' />";
	
	?>
    <script language="javascript" type="text/javascript">
	var style_no='<? echo $txt_style_ref_no;?>';
	var style_id='<? echo $txt_style_ref_id;?>';
	var style_des='<? echo $txt_style_ref;?>';
	var year='<? echo $txt_year;?>';
	//alert(style_id);
	if(style_no!="")
	{
		style_no_arr=style_no.split(",");
		style_id_arr=style_id.split(",");
		style_des_arr=style_des.split(",");
		year_arr=year.split(",");
		var str_ref="";
		for(var k=0;k<style_no_arr.length; k++)
		{
			str_ref=style_no_arr[k]+'_'+style_id_arr[k]+'_'+style_des_arr[k]+'_'+year_arr[k];
			js_set_value(str_ref);
		}
	}
	</script>
    
    <?
	
	exit();
}
if($action=="order_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	//echo $style_id;die;

	?>
    <script>
		
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
    	function check_all_data() 
		{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) 
			{
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
				
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) { 
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( strCon ) 
		{
			//alert(strCon);
				var splitSTR = strCon.split("_");
				var str = splitSTR[0];
				var selectID = splitSTR[1];
				var selectDESC = splitSTR[2];
				//$('#txt_individual_id' + str).val(splitSTR[1]);
				//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
				
				toggle( document.getElementById( 'tr_' + str), '#FFFFCC' );
				
				if( jQuery.inArray( selectID, selected_id ) == -1 ) {
					selected_id.push( selectID );
					selected_name.push( selectDESC );
					selected_no.push( str );				
				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == selectID ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 );
					selected_no.splice( i, 1 ); 
				}
				var id = ''; var name = ''; var job = ''; var num='';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ',';
					num += selected_no[i] + ','; 
				}
				id 		= id.substr( 0, id.length - 1 );
				name 	= name.substr( 0, name.length - 1 ); 
				num 	= num.substr( 0, num.length - 1 );
				//alert(num);
				$('#txt_selected_id').val( id );
				$('#txt_selected').val( name ); 
				$('#txt_selected_no').val( num );
		}
		
		/*function js_set_value( str ) { //alert(str);
		toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
		
		if( jQuery.inArray( $('#txt_serial_id' + str).val(), selected_id ) == -1 ) {
			selected_id.push( $('#txt_serial_id' + str).val() );
			selected_no.push( $('#txt_serial_no' + str).val() );
 		}




		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == $('#txt_serial_id' + str).val() ) break;
			}
			selected_id.splice( i, 1 );
			selected_no.splice( i, 1 );
		}
		var id = '';	var no = '';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
			no += selected_no[i] + ',';
		}
		id = id.substr( 0, id.length - 1 );
		no = no.substr( 0, no.length - 1 );
  		$('#txt_string_id').val( id );
		$('#txt_string_no').val( no );
	}
	 
	function fn_onClosed()
	{
		var txt_string = $('#txt_string').val();
		if(txt_string==""){ alert("Please Select The Serial"); return;}
		parent.emailwindow.hide();
	}*/
		
    </script>
    <?
	$party=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
	$job_year=str_replace("'","",$job_year);
	if($party!=0) $party_cond=" and a.party_id=$party"; else $party_cond="";
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
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	if($type==1){

		$sql = "SELECT b.id,a.job_no,a.job_no_prefix_num, a.buyer_name, a.style_ref_no,a.gmts_item_id, b.id, b.po_number,b.grouping,$select_date as year from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and  a.company_name=$company $buyer_cond  $job_year_cond";
		$arr=array(3=>$buyer_arr,5=>$garments_item);
		echo create_list_view("list_view", "Po Number,Job No,Year,Buyer,Style Ref No,Item Name","160,90,100,100,100,80","700","310",0, $sql , "js_set_value", "id,po_number", "", 1, "0,0,0,buyer_name,0,gmts_item_id", $arr, "po_number,job_no_prefix_num,year,buyer_name,style_ref_no,gmts_item_id", "","setFilterGrid('list_view',-1)","0,0,0,0,0,0","",1) ;	
	}else{
		$sql="select b.id, b.cust_style_ref, a.job_no_prefix_num,$select_date as year,b.order_no,b.cust_buyer,b.item_group from  subcon_ord_mst a, subcon_ord_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.company_id='$company' $party_cond group by b.id, b.cust_style_ref, a.job_no_prefix_num, a.insert_date ,b.order_no,b.cust_buyer,b.item_group  order by b.id desc";	
		echo create_list_view("list_view", "Po Number,Job No,Year,Buyer","160,90,100,100","500","310",0, $sql , "js_set_value", "id,order_no", "", 1, "0", $arr, "order_no,job_no_prefix_num,year,cust_buyer", "","setFilterGrid('list_view',-1)","0","",1) ;	
	}

 //  echo $sql; die;

	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	echo "<input type='hidden' id='txt_year' />";
	
	?>
    <script language="javascript" type="text/javascript">
	var style_no='<? echo $txt_style_ref_no;?>';
	var style_id='<? echo $txt_style_ref_id;?>';
	var style_des='<? echo $txt_style_ref;?>';
	var year='<? echo $txt_year;?>';
	//alert(style_id);
	if(style_no!="")
	{
		style_no_arr=style_no.split(",");
		style_id_arr=style_id.split(",");
		style_des_arr=style_des.split(",");
		year_arr=year.split(",");
		var str_ref="";
		for(var k=0;k<style_no_arr.length; k++)
		{
			str_ref=style_no_arr[k]+'_'+style_id_arr[k]+'_'+style_des_arr[k]+'_'+year_arr[k];
			js_set_value(str_ref);
		}
	}
	</script>
    
    <?
	
	exit();
}
?>