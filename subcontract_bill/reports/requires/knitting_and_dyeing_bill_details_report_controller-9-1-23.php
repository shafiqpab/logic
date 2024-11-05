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
		$party_arr=return_library_array("select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id","company_name");
		$value = 1;
		if(count($party_arr)==1){
			$value =0;
		}
		echo create_drop_down( "cbo_party_name", 130, $party_arr,"",1, "-- Select Party --", $value, "load_drop_down( 'requires/knitting_bill_issue_controller', this.value, 'load_drop_down_party_location', 'party_location_td');","","","","","",5 ); 
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
		$bill_cond=" and  bill_no like ='%$txt_bill_no%'";
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
    
	$product_dtls_arr=return_library_array( "select id,product_name_details as const_comp from  product_details_master union all select id,const_comp from lib_subcon_charge",'id','const_comp');
	$po_number_arr = return_library_array("select id, po_number from wo_po_break_down", "id", "po_number");
	$order_no_arr = return_library_array("select id, order_no from subcon_ord_dtls", "id", "order_no");

	

	if($job_order_type==1){
		

		if($txt_job_id !==""){
			$job_cond="and b.order_id in  ($txt_job_id)";
		}else{$job_cond="";}

		if($txt_order_id !=""){
			$order_cond="and b.po_break_down_id in  ($txt_order_id)";
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
	
	$inhouse_sql="select a.bill_no , a.company_id , a.location_id, a.bill_date, a.party_id,a.party_source,b.buyer_po_id,b.po_break_down_id,b.challan_no,b.body_part_id,b.color_id,b.delivery_qty,b.delivery_dtls_id,b.delivery_id,b.order_id,b.amount,b.rate,b.currency_id as currency,b.process_id,b.add_process_name from  subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id  and  a.status_active=1 and b.status_active=1 and a.process_id=$report_type $parti_con $parti_source_con $date_cond $location_con $company_con $bill_cond $job_cond $order_cond order by party_source ";

	$outbound_sql="select a.bill_no,a.bill_date,a.company_id,a.supplier_id,a.location_id,a.party_bill_no,b.challan_no,b.color_id,b.body_part_id,b.job_no,b.order_id,b.rate,b.febric_description_id,b.receive_qty,b.amount,b.currency_id as currency ,b.process_id from  subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id  and a.status_active=1 and b.status_active=1 and a.process_id=$report_type  $date_cond $location_con $company_con $bill_cond $supplier_con $job_cond $order_cond";

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
		if(!empty($data[csf('order_id')]))
		{

			$order_ids_inhouse[]=$data[csf('order_id')];
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
	//echo $sql_job;
	$sql_job_inbound_result =sql_select($sql_inbound_job);
	foreach($sql_job_inbound_result as $row)
	{
		$job_inbound_arr[$row[csf('id')]]['job']=$row[csf('job_no')];
		$job_inbound_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
		$job_inbound_arr[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
		$job_inbound_arr[$row[csf('id')]]['po']=$row[csf('po_number')];
		$job_inbound_arr[$row[csf('id')]]['int_ref']=$row[csf('grouping')];
	}

	//$job_outbound_arr=array();
	//$sql_job="SELECT a.subcon_job as job_no, a.party_id as buyer_name, b.cust_style_ref as style_ref_no, b.id, b.order_no as po_number , null as grouping from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst ".where_con_using_array($order_ids,0,'b.id') ." and a.status_active=1 and a.is_deleted=0";
	//echo $sql_job;
	//$sql_job_result =sql_select($sql_job);
	// foreach($sql_job_result as $row)
	// {
	// 	$job_outbound_arr[$row[csf('id')]]['job']=$row[csf('job_no')];
	// 	$job_outbound_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
	// 	$job_outbound_arr[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
	// 	$job_outbound_arr[$row[csf('id')]]['po']=$row[csf('po_number')];
	// 	$job_outbound_arr[$row[csf('id')]]['int_ref']=$row[csf('grouping')];
	// }
	

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

								$inbound_rcv_qnty=$inbound_order_data[$row[csf('order_id')]]['bill_rcv_qnty'];

								if($row[csf('party_source')]==1)
										{
											$process_id=$inbound_order_data[$row[csf('order_id')]]['process_id'];
											$process=$conversion_cost_head_array[$process_id];
										}else{
											$process=$row[csf('add_process_name')];
											;
										}
										
	                        
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
									<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>">
										<td style="word-break: break-all;" width="40"><?=$i; ?></td>
										<td style="word-break: break-all;" width="110"><p><?=$row[csf('bill_no')]; ?></p></td>
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
										
										<td style="word-break: break-all;"  width="110"><p><?=$buyer_arr[$job_inbound_arr[$row[csf('order_id')]]['buyer']] ; ?></p></td>
										<td style="word-break: break-all;" width="110"><p><?=$job_inbound_arr[$row[csf('order_id')]]['style'] ; ?></p></td>
										<td style="word-break: break-all;" width="115"><p><?=$job_inbound_arr[$row[csf('order_id')]]['job']; ?></p></td>
										<td style="word-break: break-all;" width="100"><p><?
									
										if($row[csf('party_source')]==1)
										{
											echo $po_number_arr[$row[csf('order_id')]];;
										}else{
											echo $order_no_arr[$row[csf('order_id')]];
										}
										
										?></p></td>
										<td style="word-break: break-all;" width="100" title="<?=$process_id;?>"><p><?=$process; ?></p></td>
										<td style="word-break: break-all;" width="110"><p><?=$constructtion_arr[$order_wise_data[$row[csf('order_id')]]['febric_description_id']]; ?></p></td>
										<td style="word-break: break-all;" width="250"><p><?=$composition_arr[$order_wise_data[$row[csf('order_id')]]['febric_description_id']]; ?></p></td>
										<td style="word-break: break-all;" width="70"><p><?=$order_wise_data[$row[csf('order_id')]]['gsm']; ?></p></td>
										<td style="word-break: break-all;" width="80"><p><?=$order_wise_data[$row[csf('order_id')]]['width']; ?></p></td>
										<td style="word-break: break-all;" width="100"><p><?=$body_part[$order_wise_data[$row[csf('order_id')]]['body_part_id']]; ?></p></td>
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
							$outbound_rcv_qnty=$outbound_order_data[$row[csf('order_id')]]['bill_rcv_qnty'];
							$process_id=$outbound_order_data[$row[csf('order_id')]]['process_id'];
							$process=$conversion_cost_head_array[$process_id];
	                        

                            ?>
								<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>">
									<td style="word-break: break-all;" width="40"><?=$i; ?></td>
									<td style="word-break: break-all;" width="110"><p><?=$row[csf('bill_no')]; ?></p></td>
									<td style="word-break: break-all;" width="60"><p><?=change_date_format($row[csf('bill_date')]); ?></p></td>
									
									<td style="word-break: break-all;" width="100"><p><?=$supplier_arr[$row[csf('supplier_id')]]; ?></p></td>
									
									<td style="word-break: break-all;" width="110"><p><?=$buyer_arr[$job_inbound_arr[$row[csf('order_id')]]['buyer']] ; ?></p></td>
									<td style="word-break: break-all;" width="110"><p><?=$job_inbound_arr[$row[csf('order_id')]]['style']; ?></p></td>
									<td style="word-break: break-all;" width="115"><p><?=$job_inbound_arr[$row[csf('order_id')]]['job'] ; ?></p></td>
									<td style="word-break: break-all;" width="100"><p><?=$po_number_arr[$row[csf('order_id')]]; ?></p></td>
									<td style="word-break: break-all;" width="100" title="<?=$process_id?>"><p><?=$process; ?></p></td>
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
	if($type==1){

		$sql = "SELECT a.id,a.job_no,a.job_no_prefix_num, a.buyer_name, a.style_ref_no,a.gmts_item_id, b.id, b.po_number,b.grouping,$select_date as year from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and  a.company_name=$company $buyer_cond  $job_year_cond";

		echo create_list_view("list_view", "Po Number,Job No,Year,Buyer,Style Ref No,Item Name","160,90,100,100,100,80","700","310",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "0", $arr, "po_number,job_no_prefix_num,year,buyer_name,style_ref_no,gmts_item_id", "","setFilterGrid('list_view',-1)","0","",1) ;	
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

	if($type==1){

		$sql = "SELECT b.id,a.job_no,a.job_no_prefix_num, a.buyer_name, a.style_ref_no,a.gmts_item_id, b.id, b.po_number,b.grouping,$select_date as year from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and  a.company_name=$company $buyer_cond  $job_year_cond";

		echo create_list_view("list_view", "Po Number,Job No,Year,Buyer,Style Ref No,Item Name","160,90,100,100,100,80","700","310",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "po_number,job_no_prefix_num,year,buyer_name,style_ref_no,gmts_item_id", "","setFilterGrid('list_view',-1)","0","",1) ;	
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