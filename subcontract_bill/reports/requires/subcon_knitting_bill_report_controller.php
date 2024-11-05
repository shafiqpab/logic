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
	
	if($data[1]==2)
	{
		echo create_drop_down( "cbo_party_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $selected, "","","","","","",5 ); 
	}
	else if($data[1]==1)
	{	
		$party_arr=return_library_array("select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id","company_name");
		$value = 1;
		if(count($party_arr)==1){
			$value =0;
		}
		echo create_drop_down( "cbo_party_name", 130, $party_arr,"",$value, "-- Select Party --", $selected, "load_drop_down( 'requires/knitting_bill_issue_controller', this.value, 'load_drop_down_party_location', 'party_location_td');","","","","","",5 ); 
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 130, "select sup.id, sup.supplier_name from lib_supplier sup,lib_supplier_tag_company b where sup.status_active=1 and sup.is_deleted=0 and b.supplier_id=sup.id and b.tag_company='$data[0]' $supplier_cond and sup.id in (select  supplier_id from  lib_supplier_party_type where party_type=20) order by supplier_name", "id,supplier_name", 1, "-- Select supplier --", $selected, "","","","","","",5 );
	
		
	}
	exit();
}
if($action=="load_drop_down_location")
{
	$data=explode('_',$data);
	$source=$data[0];
	$company_id=$data[1];
	if($source==1 || $source==2)
	{
		echo create_drop_down( "location_id", 130, "select id,location_name from lib_location where company_id='$company_id' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/subcon_knitting_bill_report_controller',this.value+'_'+document.getElementById('cbo_party_source').value+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_floor', 'floor_td' );" );
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
	$location_id=str_replace("'","",trim($location_id));
	$floor_id=str_replace("'","",trim($floor_id));
	$supplier_con='';
	$company_con='';
	$party_con='';
	$location_con='';
	//echo $cbo_search_by.'--'.$txt_search_common;die;
	if(!empty($cbo_supplier_name) && $cbo_supplier_name!=0)
	{
		$supplier_con=" and a.supplier_id=$cbo_supplier_name";
	}

	if(!empty($cbo_company_id) && $cbo_company_id!=0)
	{
		$company_con=" and  a.company_id=$cbo_company_id";
	}

	if(!empty($cbo_party_name) && $cbo_party_name!=0)
	{
		$party_con=" and  a.party_id=$cbo_party_name";
	}

	if(!empty($location_id) && $location_id!=0)
	{
		$location_con=" and  a.location_id=$location_id";
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
		if ($start_date!="" &&  $end_date!="") $date_cond  = "and a.bill_date  between '".change_date_format($start_date, "yyyy-mm-dd", "-")."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'"; else $date_cond ="";
	}

	if($db_type==2)
	{
		if ($start_date!="" &&  $end_date!="") $date_cond  = "and a.bill_date  between '".change_date_format($start_date, "yyyy-mm-dd", "-",1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-",1)."'"; else $date_cond ="";
	}


	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$roll_no_arr=return_library_array( "select id, no_of_roll from  pro_grey_prod_entry_dtls",'id','no_of_roll');
	$bodyPartTypeArr = return_library_array( "select id, body_part_type from lib_body_part",'id','body_part_type');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
    
	$product_dtls_arr=return_library_array( "select id,product_name_details as const_comp from  product_details_master union all select id,const_comp from lib_subcon_charge",'id','const_comp');

	$job_order_arr=array();
	$sql_job="SELECT a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.id, b.po_number,b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active!=0 and a.is_deleted=0  and b.status_active!=0 and b.is_deleted=0 
		union all 
		SELECT a.job_no_prefix_num, a.party_id as buyer_name, b.cust_style_ref as style_ref_no, b.id, b.order_no as po_number , null as grouping from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0";
	$sql_job_result =sql_select($sql_job);
	foreach($sql_job_result as $row)
	{
		$job_order_arr[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
		$job_order_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
		$job_order_arr[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
		$job_order_arr[$row[csf('id')]]['po']=$row[csf('po_number')];
		$job_order_arr[$row[csf('id')]]['int_ref']=$row[csf('grouping')];
	}
	unset($sql_job_result);
	
	

	$composition_arr = array();
	$constructtion_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		$constructtion_arr[$row[csf('id')]] = $row[csf('construction')];
		$composition_arr[$row[csf('id')]] .= $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "% ";
	}
	$parti_con="";
	if(!empty($cbo_party_source))
	{
		$parti_con=" and a.party_source in ($cbo_party_source) ";
	}
	
	$inhouse_sql="select a.bill_no , a.company_id , a.location_id, a.bill_date, a.party_id,a.party_source,b.buyer_po_id,b.po_break_down_id,b.challan_no,b.body_part_id,b.color_id,b.delivery_qty,b.delivery_dtls_id,b.delivery_id,b.order_id,b.amount,b.rate from  subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id  and  a.status_active=1 and b.status_active=1 $parti_con $date_cond $location_con $company_con $party_con  order by party_source ";

	$outbound_sql="select a.bill_no,a.bill_date,a.company_id,a.supplier_id,a.location_id,a.party_bill_no,b.challan_no,b.color_id,b.body_part_id,b.job_no,b.order_id,b.rate,b.febric_description_id,b.receive_qty,b.amount from  subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id  and a.status_active=1 and b.status_active=1  $date_cond $location_con $company_con";

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
	

	$order_ids=array();
	foreach ($inhouse_result as $data) 
	{
		$order_ids[]=$data[csf('order_id')];
	}
	foreach ($outbound_result as $data) 
	{
		$order_ids[]=$data[csf('order_id')];
	}
	$order_ids=array_unique($order_ids);

	$sql_order=sql_select("SELECT a.id, a.company_id, a.recv_number,a.booking_id,a.booking_no, a.receive_basis, a.receive_date, a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id, b.febric_description_id,b.trans_id, b.gsm,b.room,b.rack,b.self,b.bin_box,c.qnty,c.rate, c.qc_pass_qnty,c.roll_no,b.width,b.body_part_id,b.yarn_lot,b.brand_id,b.shift_name,b.floor_id,b.machine_no_id,b.yarn_count,b.color_id,b.color_range_id, c.barcode_no, c.id as roll_id, c.po_breakdown_id, c.booking_without_order, c.is_sales, c.qc_pass_qnty_pcs,b.stitch_length
    FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
    WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=58 and c.entry_form=58 ".where_con_using_array($order_ids,0,'c.po_breakdown_id') ."  and c.status_active=1 and c.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $floor_con ");
    
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
    	
    }

    $order_sql="SELECT a.id, a.company_id, a.recv_number,a.booking_id,a.booking_no, a.receive_basis, a.receive_date, a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id, b.febric_description_id,b.trans_id, b.gsm,b.room,b.rack,b.self,b.bin_box,b.width,b.body_part_id,b.yarn_lot,b.brand_id,b.shift_name,b.floor_id,b.machine_no_id,b.yarn_count,b.color_id,b.color_range_id, c.po_breakdown_id,   b.stitch_length
    FROM inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c
    WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in (22,23)  ".where_con_using_array($order_ids,0,'c.po_breakdown_id') ."  and c.status_active=1 and c.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $floor_con ";
    $sql_order=sql_select($order_sql);
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
    	
    }

    foreach ($inhouse_result as $data) 
	{
		if(!empty($job_order_arr[$data[csf('order_id')]]['buyer']))
		{
			$buyer_wise_data[$job_order_arr[$data[csf('order_id')]]['buyer']][$data[csf('party_source')]]['buyer_id']=$job_order_arr[$data[csf('order_id')]]['buyer'];
			$buyer_wise_data[$job_order_arr[$data[csf('order_id')]]['buyer']][$data[csf('party_source')]]['source']=$data[csf('party_source')];
			$buyer_wise_data[$job_order_arr[$data[csf('order_id')]]['buyer']][$data[csf('party_source')]]['qnty']+=$data[csf('delivery_qty')];
			$buyer_wise_data[$job_order_arr[$data[csf('order_id')]]['buyer']][$data[csf('party_source')]]['amount']+=$data[csf('amount')];
		}
		if(!empty($order_wise_data[$data[csf('order_id')]]['floor_id']))
		{
			$floor_wise_data[$order_wise_data[$data[csf('order_id')]]['floor_id']][$data[csf('party_source')]]['floor_id']=$order_wise_data[$data[csf('order_id')]]['floor_id'];
			$floor_wise_data[$order_wise_data[$data[csf('order_id')]]['floor_id']][$data[csf('party_source')]]['source']=$data[csf('party_source')];
			$floor_wise_data[$order_wise_data[$data[csf('order_id')]]['floor_id']][$data[csf('party_source')]]['qnty']+=$data[csf('delivery_qty')];
			$floor_wise_data[$order_wise_data[$data[csf('order_id')]]['floor_id']][$data[csf('party_source')]]['amount']+=$data[csf('amount')];
		}
	}
	foreach ($outbound_result as $data) 
	{
		if(!empty($job_order_arr[$data[csf('order_id')]]['buyer']))
		{
			//echo $data[csf('amount')];
			//die;
			$buyer_wise_data[$job_order_arr[$data[csf('order_id')]]['buyer']][3]['buyer_id']=$job_order_arr[$data[csf('order_id')]]['buyer'];
			$buyer_wise_data[$job_order_arr[$data[csf('order_id')]]['buyer']][3]['source']=3;
			$buyer_wise_data[$job_order_arr[$data[csf('order_id')]]['buyer']][3]['qnty']=$data[csf('receive_qty')];
			$buyer_wise_data[$job_order_arr[$data[csf('order_id')]]['buyer']][3]['amount']+=$data[csf('amount')];
		}
	}
	
	
	$table_width="1950"; $colspan="21";
	ob_start();
	?>
    <fieldset style="width:100%">	
        <table  width="1600">
        	<tr>
	        	<td  width="800" align="left">
	        		<table class="rpt_table" border="1" rules="all"  cellpadding="0" cellspacing="0">
	        			<thead>
	        				<tr>
	        					<th colspan="8">Knit Production Bill Summary (In-House + Outbound+In bound SubCon )</th>
	        				</tr>
	        				<tr>
	        					<th rowspan="2" width="40">Sl</th>
	        					<th rowspan="2" width="120">Buyer</th>
	        					<th colspan="2" width="200">Inhouse</th>
	        					<th colspan="2" width="200">Outbound</th>
	        					<th colspan="2" width="200">inhouse Sub-Contract</th>
	        				</tr>
	        				<tr>
	        					<th>Qty</th>
	        					<th>Value</th>
	        					<th>Qty</th>
	        					<th>Value</th>
	        					<th>Qty</th>
	        					<th>Value</th>
	        				</tr>
	        			
	        			</thead>
	        			<tbody>
	        				
	        				<?php 

	        				$i=1;
	        				$in_house_buyer_qnt=0;
	        				$in_house_buyer_amount=0;
	        				$in_bound_buyer_qnt=0;
	        				$in_bound_buyer_amount=0;
	        				$out_bound_buyer_qnt=0;
	        				$out_bound_buyer_amount=0;
	        				foreach ($buyer_wise_data as $key => $data) 
	        				{
	        					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	        						
	        					 ?>
		        				<tr  bgcolor="<?=$bgcolor; ?>" >
		        					<td  ><?=$i; ?></td>
									<td ><p><?=$buyer_arr[$key]; ?></p></td>
									<td ><p><?=number_format($data[1]['qnty'],2); ?></p></td>
									<td ><p><?=number_format($data[1]['amount'],2); ?></p></td>
		        					
		        					<td ><p><?=number_format($data[3]['qnty'],2); ?></p></td>
									<td ><p><?=number_format($data[3]['amount'],2); ?></p></td>

									<td ><p><?=number_format($data[2]['qnty'],2); ?></p></td>
									<td ><p><?=number_format($data[2]['amount'],2); ?></p></td>

		        					
		        				</tr>
		        				<?  
		        				$i++;

		        				$in_house_buyer_qnt+=$data[1]['qnty'];
		        				$in_house_buyer_amount+=$data[1]['amount'];
		        				$in_bound_buyer_qnt+=$data[2]['qnty'];
		        				$in_bound_buyer_amount+=$data[2]['amount'];
		        				$out_bound_buyer_qnt+=$data[3]['qnty'];
		        				$out_bound_buyer_amount+=$data[3]['amount'];
	        				}
	        				$i=0;

	        			?>
	        			</tbody>
	        			<tfoot>
	        				<tr  bgcolor="#ddd" >
	        					
								<td colspan="2" style="justify-content: right;text-align: right;"><p>Total</p></td>
								<td ><p><?=number_format($in_house_buyer_qnt,2); ?></p></td>
								<td ><p><?=number_format($in_house_buyer_amount,2); ?></p></td>

								<td ><p><?=number_format($out_bound_buyer_qnt,2); ?></p></td>
								<td ><p><?=number_format($out_bound_buyer_amount,2); ?></p></td>
	        					
	        					<td ><p><?=number_format($in_bound_buyer_qnt,2); ?></p></td>
								<td ><p><?=number_format($in_bound_buyer_amount,2); ?></p></td>

								

	        					
	        				</tr>
	        			</tfoot>
	        		</table>
	        	</td>
	        	<td width="200"></td>
	        	<td  width="600" align="rigth">
	        		<table class="rpt_table" border="1" rules="all"  cellpadding="0" cellspacing="0">
	        			<thead>
	        				
	        				<tr>
	        					<th rowspan="2" width="140">Floor</th>
	        					
	        					<th colspan="2" width="200">Inhouse</th>
	        					<th colspan="2" width="200">inhouse Sub-Contract</th>
	        					
	        				</tr>
	        				<tr>
	        					<th>Qty</th>
	        					<th>Value</th>
	        					<th>Qty</th>
	        					<th>Value</th>
	        					
	        				</tr>
	        			</thead>
	        			<tbody>
	        				<?php 

	        				$i=1;
	        				$in_house_floor_amount=0;
	        				$in_house_floor_qnt=0;

	        				$out_bound_floor_amount=0;
	        				$out_bound_floor_qnt=0;
	        				foreach ($floor_wise_data as $key => $data) 
	        				{
	        					
	        					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	        						
	        					 ?>
		        				<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>">
		        					
									<td ><p><?=$floor_arr[$key]; ?></p></td>
		        					<td ><p><?=number_format($data[1]['qnty'],2); ?></p></td>
									<td ><p><?=number_format($data[1]['amount'],2); ?></p></td>
									<td ><p><?=number_format($data[2]['qnty'],2); ?></p></td>
									<td ><p><?=number_format($data[2]['amount'],2); ?></p></td>
		        					
		        				</tr>
		        				<?  
			        				$i++;
			        				$in_house_floor_amount+=$data[1]['amount'];
			        				$in_house_floor_qnt+=$data[1]['qnty'];

			        				$out_bound_floor_amount+=$data[2]['amount'];
			        				$out_bound_floor_qnt+=$data[2]['qnty'];
		        				}
		        				$i=0;

		        			?>
	        			</tbody>
	        			<tfoot>
	        				<tr bgcolor="#ddd">
	        						<td style="justify-content: right;text-align: right;"><p>Total</p></td>
		        					<td ><p><?=number_format($in_house_floor_qnt,2); ?></p></td>
									<td ><p><?=number_format($in_house_floor_amount,2); ?></p></td>
									<td ><p><?=number_format($out_bound_floor_qnt,2); ?></p></td>
									<td ><p><?=number_format($out_bound_floor_amount,2); ?></p></td>
	        				</tr>
	        			</tfoot>
	        		</table>
	        	</td>
	        </tr>
        </table>
        <table class="rpt_table" border="1" rules="all" width="<?=$table_width; ?>" cellpadding="0" cellspacing="0" style="margin-top: 50px;">
            <thead>
                <tr>
                	<th width="40">SL</th>
                    <th width="110">Bill No</th>
                    <th width="60">Date</th>
                    <th width="80">Company</th>
                    <th width="100">Party</th>
                    <th width="110">Location</th>
                    <th width="100">Floor</th>
                    <th width="110">Booking No</th>
                    <th width="110">Buyer Name</th>
                    <th width="110">Internal Ref.</th>
                    <th width="110">Construction</th>
                    <th width="250">Composition</th>
                    <th width="80">F/Dia</th>
                    <th width="50">S. Length</th>
                    <th width="80">Yarn Lot</th>
                    <th width="70">Count</th>
                    <th width="70">GSM</th>
                    <th width="70">Color</th>
                    <th width="80">Bill Qty</th>
                    <th width="70">Rate/Kg</th>
                    <th width="70">Total Value</th>
                    
                </tr>

            </thead>
       
        
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

							 $ok=true;
	                        if(!empty($floor_id))
	                        {
	                        	if($order_wise_data[$row[csf('order_id')]]['floor_id']!=$floor_id)
	                        	{
	                        		$ok=false;
	                        	}
	                        }

	                        if($ok==true)
	                        {

	                            $yean_count = "";
	                            foreach (explode(",", $order_wise_data[$row[csf("order_id")]]['yarn_count']) as $y_id)
	                            {
	                            	if ($yean_count == "") $yean_count = $count_arr[$y_id];
	                            	else                   $yean_count .= "," . $count_arr[$y_id];
	                            }
	                           	if($source!=$row[csf('party_source')])
	                           	{
	                           		if($source!='')
	                           		{
	                           			 $bgcolor="#ddd"; 

	                           			?>
		                           			<tr bgcolor="<?=$bgcolor; ?>" >
		                           				<td colspan="18" style="justify-content: right;text-align: right;">Sub total</td>
												<td  align="rigth"><? echo number_format($sub_bill,2); ?></td>
												<td ></td>
												<td  align="rigth"><? echo number_format($sub_amount,2); ?></td>
											</tr>
	                           			<?
	                           			
	                           		}
	                           		$bgcolor="#efefef"; 
	                           		?>
	                           			<tr bgcolor="<?=$bgcolor; ?>" >
											<td colspan="22" align="left"><? echo $knitting_source[$row[csf('party_source')]] ?></td>
										</tr>
	                           		<?
	                           		$sub_bill=0;
									$sub_amount=0;
									
	                           	}
	                            $color_names = '';
		                        $colorIds = array_unique(explode(",", $row[csf('color_id')]));
		                        foreach ($colorIds as $color_id)
								{
		                            $color_names .= $color_arr[$color_id] . ",";
		                        }
		                        $color_names = chop($color_names, ',');


	                        
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
									<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>">
										<td  ><?=$i; ?></td>
										<td ><p><?=$row[csf('bill_no')]; ?></p></td>
										<td ><p><?=change_date_format($row[csf('bill_date')]); ?></p></td>
										<td ><p><?=$company_arr[$row[csf('company_id')]]; ?></p></td>
										<td >
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
										<td ><p><?=$location_arr[$row[csf('location_id')]]; ?></p></td>
										<td ><p><?=$floor_arr[$order_wise_data[$row[csf('order_id')]]['floor_id']]; ?></p></td>
										<td ><p><?=$order_wise_data[$row[csf('order_id')]]['booking_no']; ?></p></td>
										<td ><p><?=$buyer_arr[$job_order_arr[$row[csf('order_id')]]['buyer']] ; ?></p></td>
										<td><p><?=$job_order_arr[$row[csf('order_id')]]['int_ref'] ; ?></p></td>
										<td ><p><?=$constructtion_arr[$order_wise_data[$row[csf('order_id')]]['febric_description_id']]; ?></p></td>
										<td ><p><?=$composition_arr[$order_wise_data[$row[csf('order_id')]]['febric_description_id']]; ?></p></td>
										<td ><p><?=$order_wise_data[$row[csf('order_id')]]['width']; ?></p></td>
										<td ><p><?=$order_wise_data[$row[csf('order_id')]]['stitch_length']; ?></p></td>
										<td ><p><?=$order_wise_data[$row[csf('order_id')]]['yarn_lot']; ?></p></td>
										<td ><p><?=$yean_count; ?></p></td>
										<td ><p><?=$order_wise_data[$row[csf('order_id')]]['gsm']; ?></p></td>
										<td ><p><?=$color_names; ?></p></td>
										<td align="left"><p><?=number_format($row[csf('delivery_qty')],2); ?></p></td>
										<td align="left"><p><?=number_format($row[csf('rate')],2); ?></p></td>
										<td align="left"><p><?=number_format($row[csf('amount')],2); ?></p></td>
										
									</tr>
								<?
								$source=$row[csf('party_source')];
								$total_bill+=$row[csf('delivery_qty')];
								$total_amount+=$row[csf('amount')];
								$sub_bill+=$row[csf('delivery_qty')];
								$sub_amount+=$row[csf('amount')];
								$i++;
							}
						}
						
						if($i>1)
						{

							$bgcolor="#ddd";
							?>
							<tr bgcolor="<?=$bgcolor; ?>" >
			       				<td colspan="18" style="justify-content: right;text-align: right;">Sub total</td>
								<td  align="rigth"><? echo number_format($sub_bill,2); ?></td>
								<td ></td>
								<td  align="rigth"><? echo number_format($sub_amount,2); ?></td>
							</tr>

							<?php 
							$sub_bill=0;
							$sub_amount=0;
							

						}
						if(count($outbound_result))
						{
							$bgcolor="#efefef"; 
							?>
								<tr bgcolor="<?=$bgcolor; ?>" >
									<td colspan="22" align="left"><? echo $knitting_source[3] ?></td>
								</tr>

							<?
							
						}

						foreach($outbound_result as $row)
						{

                            $yean_count = "";
                            foreach (explode(",", $order_wise_data[$row[csf("order_id")]]['yarn_count']) as $y_id)
                            {
                            	if ($yean_count == "") $yean_count = $count_arr[$y_id];
                            	else                   $yean_count .= "," . $count_arr[$y_id];
                            }
                            if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

                            ?>
								<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>">
									<td  ><?=$i; ?></td>
									<td ><p><?=$row[csf('bill_no')]; ?></p></td>
									<td ><p><?=change_date_format($row[csf('bill_date')]); ?></p></td>
									<td ><p><?=$company_arr[$row[csf('company_id')]]; ?></p></td>
									<td ><p><?=$supplier_arr[$row[csf('supplier_id')]]; ?></p></td>
									<td ><p><?=$location_arr[$row[csf('location_id')]]; ?></p></td>
									<td ><p><?=$floor_arr[$order_wise_data[$row[csf('order_id')]]['floor_id']]; ?></p></td>
									<td ><p><?=$order_wise_data[$row[csf('order_id')]]['booking_no']; ?></p></td>
									<td ><p><?=$buyer_arr[$job_order_arr[$row[csf('order_id')]]['buyer']] ; ?></p></td>
									<td><p><?=$job_order_arr[$row[csf('order_id')]]['int_ref'] ; ?></p></td>
									<td ><p><?=$constructtion_arr[$row[csf('febric_description_id')]]; ?></p></td>
									<td ><p><?=$composition_arr[$row[csf('febric_description_id')]]; ?></p></td>
									<td ><p><?=$order_wise_data[$row[csf('order_id')]]['width']; ?></p></td>
									<td ><p><?=$order_wise_data[$row[csf('order_id')]]['stitch_length']; ?></p></td>
									<td ><p><?=$order_wise_data[$row[csf('order_id')]]['yarn_lot']; ?></p></td>
									<td ><p><?=$yean_count; ?></p></td>
									<td ><p><?=$order_wise_data[$row[csf('order_id')]]['gsm']; ?></p></td>
									<td ><p><?=$color_names; ?></p></td>
									<td align="left"><p><?=number_format($row[csf('receive_qty')],2); ?></p></td>
									<td align="left"><p><?=number_format($row[csf('rate')],2); ?></p></td>
									<td align="left"><p><?=number_format($row[csf('amount')],2); ?></p></td>
									
								</tr>
							<?
							
							$total_bill+=$row[csf('receive_qty')];
							$total_amount+=$row[csf('amount')];
							$sub_bill+=$row[csf('receive_qty')];
							$sub_amount+=$row[csf('amount')];
							$i++;

                        }

					
						if(count($outbound_result))
						{
							$bgcolor="#ddd"; 

							?>
							<tr bgcolor="<?=$bgcolor; ?>" >
			       				<td colspan="18" style="justify-content: right;text-align: right;">Sub total</td>
								<td  align="rigth"><? echo number_format($sub_bill,2); ?></td>
								<td ></td>
								<td  align="rigth"><? echo number_format($sub_amount,2); ?></td>
							</tr>

							<?php 
								$sub_bill=0;
								$sub_amount=0;

						} 
							?>
			
			</tbody>
			 <tfoot >
			 	<?php $bgcolor="#efefef";  ?>
				<tr bgcolor="<?=$bgcolor; ?>" >
       				<td colspan="18" style="justify-content: right;text-align: right;">Grand total</td>
					<td  align="rigth"><? echo number_format($total_bill,2); ?></td>
					<td ></td>
					<td  align="rigth"><? echo number_format($total_amount,2); ?></td>
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


   //show 2
if($action=="report_generate2"){

	$process = array( &$_POST );

	// echo "<pre>";
	// print_r($process);
	// echo "</pre>";
	// die;
	
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_id = str_replace("'","",trim($cbo_company_id));
	$start_date=str_replace("'","",trim($txt_date_from));
	$end_date=str_replace("'","",trim($txt_date_to));
	$cbo_party_name=str_replace("'","",trim($cbo_party_name));
	$cbo_party_source=str_replace("'","",trim($cbo_party_source));


	$party_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");

	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$location_arr = return_library_array("select id,location_name from lib_location where   status_active =1 and is_deleted=0 order by location_name","id","location_name");

	if(!empty($cbo_party_name) && $cbo_party_name!=0)
	{
		$party_cond=" and  a.party_id=$cbo_party_name";
	}

	if(!empty($cbo_party_source) && $cbo_party_source!=0)
	{
		$source_cond=" and  a.PARTY_SOURCE=$cbo_party_source";
	}
	

	if ($start_date!="" &&  $end_date!="") $date_cond  = "and a.bill_date  between '".change_date_format($start_date, "yyyy-mm-dd", "-",1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-",1)."'"; else $date_cond ="";

          
	$knit_sql=("SELECT a.id,a.party_id,a.bill_no, a.bill_date, sum(b.delivery_qty) as delivery_qty, sum(b.amount) as amount, a.location_id, a.party_source from subcon_inbound_bill_mst a,subcon_inbound_bill_dtls b where  a.id=b.mst_id and a.company_id='$cbo_company_id' and a.is_deleted=0 and a.status_active=1 and  b.is_deleted=0 and b.status_active=1 $date_cond $party_cond $source_cond group by a.id, a.party_id,a.bill_no, a.bill_date, a.location_id, a.party_source order by a.id DESC");

	// echo $knit_sql;

	$result=sql_select($knit_sql);

	?>
    <fieldset style="width: 900px;">	
        <table  width="800">
        	<tr>
	        	<td  width="800" align="left">
	        		<table class="rpt_table" border="1" rules="all"  cellpadding="0" cellspacing="0">
	        			<thead>
						<tr>
							<th colspan="7"><strong><? echo $company_arr[$cbo_company_id]; ?></strong></th>
	        					
	        				</tr>
	        				<tr>

	        					<th colspan="7">Knitting Bill Summary Report</th>
	        				</tr>
	        				<tr>
	        					<th width="40">Id</th>
	        					<th width="190">Party Name</th>
	        					<th width="190">Party Location</th>
	        					<th width="190">Date</th>
	        					<th width="190">Bill No</th>
	        					<th width="190">Qty In K.G</th>
	        					<th width="190">Amount (T.K)	</th>
	        				</tr>
	        			
	        			</thead>
	        			<tbody>
	        				
	        				<?php 

	        				$i=1;
	        				foreach ($result as $row) 
	        				{
	        						$qty_kg=$row[csf("delivery_qty")];
	        						$amount=$row[csf("amount")];
	        						$party_source=$row[csf("party_source")];

	        						if ($party_source==1) {
	        							$party_name = $company_arr[$row[csf("party_id")]];
	        						}
	        						else if ($party_source==2) {
	        							$party_name = $party_arr[$row[csf("party_id")]];
	        						}
	        					 ?>
		        				<tr  bgcolor="<?=$bgcolor; ?>" >
		        					<td  ><?=$i; ?></td>
									<td ><? echo $party_name;?></td>
									<td ><? echo $location_arr[$row[csf("location_id")]];?></td>
									<td > <? echo change_date_format($row[csf("bill_date")]); ?></td>
									<td > <? echo $row[csf("bill_no")];?></td>		        					
		        					<td style="text-align: right"> <? echo number_format($qty_kg,4);?></td>
									<td style="text-align: right"> <? echo number_format($amount,4);?></td>
								</tr>
		        				<?  
		        				$i++;
								$total_kg+=$qty_kg;
								$total_amount+=$amount;

							}
	        			?>
	        			</tbody>
	        			<tfoot>
	        				<tr  bgcolor="#ddd" >
	 							<td colspan="5" style="justify-content: right;text-align: right;"><p>Total</p></td>
								<td style="text-align: right"> <?echo number_format($total_kg,4);?></td>
								<td style="text-align: right"><?echo number_format($total_amount,4);?></td>
							</tr>
	        			</tfoot>
	        		</table>
	        	</td>
	        </tr>
        </table>
    </fieldset>
	<?
}



?>