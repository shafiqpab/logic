<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
$suplier=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');


$search_by_arr=array(1=>"Date Wise Report",2=>"Wait For Heat Setting",5=>"Wait For Singeing",3=>"Wait For Dyeing",4=>"Wait For Re-Dyeing");//--------------------------------------------------------------------------------------------------------------------

if($action=="load_drop_down_buyer")
{
 	echo create_drop_down( "cbo_buyer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3))  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", 0, "" );
	exit();
}
if($action=="load_drop_down_knitting_com")
{
	$data = explode("**",$data);
	$company_id=$data[1];
	
	if($data[0]==1)
	{
		echo create_drop_down( "cbo_supplier_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "-- Select --", $company_id, "","" );
	}
	else if($data[0]==3)
	{	
		echo create_drop_down( "cbo_supplier_name", 120, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "" );
	}
	else
	{
		echo create_drop_down( "cbo_supplier_name", 120, $blank_array,"",1, "-- Select --", 0, "fnc_reset_form(2)" );
	}
	exit();
}

if ($action == "booking_no_popup")
{
	echo load_html_head_contents("Booking Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>

		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_booking_id").val(splitData[0]); 
			$("#hide_booking_no").val(splitData[1]); 
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:740px;">
					<table width="740" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
						<thead>
							<th>Buyer</th>
							<th width="170">Please Enter Booking No</th>
							<th>Booking Date</th>

							<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
							<input type="hidden" name="hide_booking_no" id="hide_booking_no" value="" />
							<input type="hidden" name="hide_booking_id" id="hide_booking_id" value="" />
						</thead>
						<tbody>
							<tr>
								<td align="center">
									<? 
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
									?>
								</td>   
								<td align="center">				
									<input type="text" style="width:150px" class="text_boxes" name="txt_booking_no" id="txt_booking_no" />	
								</td> 
								<td align="center">	
									<input type="text" style="width:70px" class="datepicker" name="txt_date_from" id="txt_date_from" readonly/> To
									<input type="text" style="width:70px" class="datepicker" name="txt_date_to" id="txt_date_to" readonly/>
								</td>     

								<td align="center">
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_booking_no').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_booking_no_search_list_view', 'search_div', 'grey_fabric_finish_fabric_process_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
								</td>
							</tr>
							<tr>
								<td colspan="4" align="center" width="95%"><? echo load_month_buttons(1); ?></td>
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
}//bookingnumbershow;
if($action == "create_booking_no_search_list_view")
{
	$data=explode('**',$data);

	if ($data[0]!=0) $company="  a.company_id='$data[0]'"; 
	if ($data[1]!=0) $buyer=" and a.buyer_id='$data[1]'"; else $buyer="";
	if ($data[2]!=0) $booking_no=" and a.booking_no_prefix_num='$data[2]'"; else $booking_no='';
	if($db_type==0)
	{
		if ($data[3]!="" &&  $data[4]!="") $booking_date  = "and s.booking_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	if($db_type==2)
	{
		if ($data[3]!="" &&  $data[4]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}

	$approved=array(0=>"No",1=>"Yes");
	$is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$arr=array (2=>$comp,3=>$buyer_arr,5=>$item_category,6=>$fabric_source,7=>$suplier,8=>$approved,9=>$is_ready);
	//pro_batch_create_mst//wo_non_ord_samp_booking_mst
 	 $sql= "(select a.id,a.booking_no_prefix_num as no_prefix_num,a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved from wo_booking_mst a,pro_batch_create_mst b where $company $buyer $booking_no $booking_date and a.booking_no=b.booking_no and a.booking_type=1 and a.is_short=2 and a.status_active=1 and a.is_deleted=0   group by a.id,a.booking_no_prefix_num ,a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved 
	union all
	 select a.id,a.booking_no_prefix_num as no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no,a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved from wo_non_ord_samp_booking_mst a ,pro_batch_create_mst b where $company $buyer $booking_no $booking_date  and a.booking_no=b.booking_no and a.booking_type=4 and b.status_active=1 and b.is_deleted=0   group by a.id,a.booking_no_prefix_num ,a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved ) order by id Desc
	";
	//echo "select a.id,a.booking_no_prefix_num as no_prefix_num,a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved from wo_booking_mst a,wo_non_ord_samp_booking_mst b where $company $buyer $booking_no $booking_date and a.booking_no=b.booking_no and a.booking_type=1 and a.is_short=2 and a.status_active=1 and a.is_deleted=0 "; 
	echo  create_list_view("list_view", "Booking No,Booking Date,Company,Buyer,Job No.,Fabric Nature,Fabric Source,Supplier,Approved,Is-Ready", "110,80,80,80,90,120,80,80,60,50","910","320",0, $sql , "js_set_value", "id,no_prefix_num", "", 1, "0,0,company_id,buyer_id,0,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", $arr , "booking_no,booking_date,company_id,buyer_id,job_no,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", '','setFilterGrid(\'list_view\',-1);','0,3,0,0,0,0,0,0,0,0','','');
	exit(); 
}



if($action=="order_number_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script type="text/javascript">
  function js_set_value(id)
	  { 
		document.getElementById('selected_id').value=id;
		  parent.emailwindow.hide();
	  }
</script>
<input type="hidden" id="selected_id" name="selected_id" /> 
<?
$buyer = str_replace("'","",$buyer_name);
$year = str_replace("'","",$year);
$buyer = str_replace("'","",$buyer_name);
if($db_type==0) $year_field="SUBSTRING_INDEX(b.insert_date, '-', 1) as year"; 
else if($db_type==2) $year_field="to_char(b.insert_date,'YYYY') as year";
if($db_type==0) $year_field_by="and YEAR(b.insert_date)"; 
else if($db_type==2) $year_field_by=" and to_char(a.insert_date,'YYYY')";
if ($company_name==0) $company=""; else $company=" and b.company_name=$company_name";
if(trim($year)!=0) $year_cond=" $year_field_by=$year"; else $year_cond="";
//echo $buyer;die;

//if ($buyer==0) $buyername=""; else $buyername=" and b.buyer_name=$buyer";//$cbo_buyer_name=($cbo_buyer_name==0)?"%%" : "%$cbo_buyer_name%";

if ($buyer==0) $buyername=""; else $buyername=" and b.buyer_name=$buyer";//$cbo_buyer_name=($cbo_buyer_name==0)?"%%" : "%$cbo_buyer_name%";
	
	$sql = "select distinct a.id,b.job_no,a.po_number,b.company_name,b.buyer_name,b.job_no_prefix_num as job_prefix,$year_field from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no $company $buyername $year_cond order by a.id desc"; 

$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
?>
<table width="370" border="1" rules="all" class="rpt_table">
	<thead>
        <tr>
            <th width="30">SL</th>
            <th width="100">Order Number</th>
            <th width="50">Job no</th>
            <th width="80">Buyer</th>
            <th width="40">Year</th>
        </tr>
   </thead>
</table>
<div style="max-height:300px; overflow:auto;">
<table id="table_body2" width="370" border="1" rules="all" class="rpt_table">
 <? $rows=sql_select($sql);
	 $i=1;
 foreach($rows as $data)
 {
	 if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
  ?>
	<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value('<? echo $data[csf('id')]."__".$data[csf('po_number')]; ?>')" style="cursor:pointer;">
		<td width="30"><? echo $i; ?></td>
		<td width="100"><p><? echo $data[csf('po_number')]; ?></p></td>
		<td width="50"><p><? echo $data[csf('job_prefix')]; ?></p></td>
		<td width="80"><p><? echo $buyer[$data[csf('buyer_name')]]; ?></p></td>
		<td width="40" align="center"><p><? echo $data[csf('year')]; ?></p></td>
	</tr>
    <? $i++; } ?>
</table>
</div>
<script> setFilterGrid("table_body2",-1); </script>
<?
	exit();
}


if($action=="report_generated")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company = str_replace("'","",$cbo_company_name);
	$buyer = str_replace("'","",$cbo_buyer_name);
	$booking_no = str_replace("'","",$txt_booking_no);
	$booking_number_hidden = str_replace("'","",$txt_hide_booking_id);
	$txt_order = str_replace("'","",$order_no);
	$hidden_order_id = str_replace("'","",$hidden_order_id);
	$year = str_replace("'","",$cbo_year_selection);
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);
	$cbo_supplier_name = str_replace("'","",$cbo_supplier_name);
	$cbo_service_source = str_replace("'","",$cbo_service_source);
	$cbo_process = str_replace("'","",$cbo_process);

	if ($buyer==0) $buyer_cond=""; else $buyer_cond="  and a.buyer_id='$buyer'";
	if ($cbo_process==0) $process_cond=""; else $process_cond="  and b.process='$cbo_process'";
	if ($cbo_process==0) $process_cond_2=""; else $process_cond_2="  and b.process_id='$cbo_process'";
	if ($cbo_supplier_name==0) $supplier_cond=""; else $supplier_cond="  and a.supplier_id='$cbo_supplier_name'";
	if ($booking_no=="") $booking_num=""; else $booking_num="  and a.booking_no like '%".str_replace("'","",$booking_no)."%'";
	if ($company==0) $comp_cond=""; else $comp_cond=" and a.company_id=$company";
	if ($txt_order=="") $order_no_cond=""; else $order_no_cond="  and a.po_number='$txt_order'";
	if ($hidden_order_id=="") $order_id_cond=""; else $order_id_cond="  and b.po_break_down_id='$hidden_order_id'";
	if($db_type==0) $year_field_by="and YEAR(a.insert_date)"; 
	else if($db_type==2) $year_field_by=" and to_char(a.insert_date,'YYYY')";
	
	if(trim($year)!=0) $year_cond=" $year_field_by=$year"; else $year_cond="";
	if($txt_date_from && $txt_date_to)
	{
		if($db_type==0)
		{
			$date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
			$date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
			$dates_com="and a.batch_date BETWEEN '$date_from' AND '$date_to'";
		}
		if($db_type==2)
		{
			$date_from=change_date_format($txt_date_from,'','',1);
			$date_to=change_date_format($txt_date_to,'','',1);
			$dates_com="and a.batch_date BETWEEN '$date_from' AND '$date_to'";
		}
	}

	if($order_no_cond!="")
	{
		$po_no_sql_search=sql_select("select a.id, a.po_number,b.style_ref_no from wo_po_break_down a,wo_po_details_master b where a.job_no_mst=b.job_no $order_no_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach($po_no_sql_search as $row)
		{
			$po_id=$row[csf("id")];
		}
	}
	if ($po_id=="") $order_no_cond_search=""; else $order_no_cond_search=" and b.po_break_down_id='$po_id'";

	$sql_service_booking="select a.id,a.booking_no_prefix_num, b.job_no as job_no_prefix_num,a.booking_no,a.booking_date,a.company_id,a.buyer_id,b.po_break_down_id,a.item_category,a.fabric_source,a.supplier_id,a.pay_mode,b.pre_cost_fabric_cost_dtls_id,sum(b.wo_qnty) as wo_qnty from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=3 and a.status_active=1 and a.is_deleted=0 and a.company_id='$company' $process_cond $booking_num $order_no_cond_search $order_id_cond $buyer_cond $supplier_cond $year_cond and a.booking_date between '$txt_date_from' and '$txt_date_to' group by a.id,a.booking_no_prefix_num, b.job_no,a.booking_no,a.booking_date,a.company_id,a.buyer_id,b.po_break_down_id,a.item_category,a.fabric_source,a.supplier_id,a.pay_mode,b.pre_cost_fabric_cost_dtls_id order by a.booking_no";
	$sql_serviceBooking=sql_select($sql_service_booking);

	$booking_nos=$po_break_down_ids="";
	foreach($sql_serviceBooking as $row)
	{
		$booking_nos.="'".$row[csf("booking_no")]."',";
		$po_break_down_ids.=$row[csf("po_break_down_id")].",";
	}
	$booking_nos=chop($booking_nos,",");
	$po_break_down_ids=chop($po_break_down_ids,",");
	//$po_no_arr = return_library_array( "select id, po_number from wo_po_break_down where id in($po_break_down_ids) and status_active=1 and is_deleted=0",'id','po_number');
 	$po_no_sql=sql_select("select a.id, a.po_number,b.style_ref_no from wo_po_break_down a,wo_po_details_master b where a.id in($po_break_down_ids) and a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach($po_no_sql as $row)
	{
		$po_no_arr[$row[csf("id")]]['po_number']=$row[csf("po_number")];
		$po_no_arr[$row[csf("id")]]['style_ref_no']=$row[csf("style_ref_no")];
	}

	$fabric_isssueToProcess_sql =sql_select("select b.booking_no, b.order_id,
		sum(case when a.entry_form=91 then b.batch_issue_qty else 0 end) as batch_issue_qty,
		sum(case when a.entry_form=92 then b.batch_issue_qty else 0 end) as batch_recv_qty  
		from inv_receive_mas_batchroll a, pro_grey_batch_dtls b where a.id=b.mst_id and a.entry_form in(91,92) and  b.order_id in($po_break_down_ids) $comp_cond $process_cond_2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by  b.booking_no, b.order_id");
	foreach($fabric_isssueToProcess_sql as $row)
	{
		$fabric_issue_recv_arr[$row[csf("booking_no")]][$row[csf("order_id")]]['batch_issue_qty']=$row[csf("batch_issue_qty")];
		$fabric_issue_recv_arr[$row[csf("booking_no")]][$row[csf("order_id")]]['batch_recv_qty']=$row[csf("batch_recv_qty")];
	}

	$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select  c.job_no,c.id,c.fabric_description,c.cons_process from wo_booking_dtls b,wo_pre_cost_fab_conv_cost_dtls c  where c.id=b.pre_cost_fabric_cost_dtls_id and b.booking_no in($booking_nos) group by c.job_no,c.id,c.fabric_description,c.cons_process");
	//echo "select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where job_no='$data[0]' ";
	foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id)
	{
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]!=0)
		{
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls 
			where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."'");
			list($fabric_description_row)=$fabric_description;
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$body_part[$fabric_description_row[csf("body_part_id")]].', 
			'.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")];
		}
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]==0)
		{
			$fabric_description_string="";
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls 
			where  job_no=".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf('job_no')]." ");
			foreach( $fabric_description as $fabric_description_row)
	        {
			$fabric_description_string.=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")]." and ";
			}
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]].=rtrim($fabric_description_string,"and ");
		}
	}

	ob_start();
	?>
	<div align="center">
		<fieldset style="width:1295px;">
			<div  align="center"> <strong> <? echo $company_library[$company]; ?> </strong><br><strong> <? echo $search_by_arr[$cbo_type]; ?> </strong>
			<br><b>
			<?
			//echo  change_date_format($date_from).' '.To.' '.change_date_format($date_to);
			echo  ($date_from == '0000-00-00' || $date_from == '' ? '' : change_date_format($date_from)).' To ';echo  ($date_to == '0000-00-00' || $date_to == '' ? '' : change_date_format($date_to));
			?> </b>
		</div>
			<div align="left">
				<table width="1300" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">            	
					<thead>
			        	<tr>
			                <th width="40">SL</th>
			                <th width="80">WO No</th>
			                <th width="120">Party Name</th>
			                <th width="120">Buyer</th>
			                <th width="120">Order</th>
			                <th width="120">Style</th>
			                <th width="150">Item Description</th>
			                <th width="60">WO Qty</th>
			                <th width="60">Fabric Issue</th>
			                <th width="60">Fabric Received</th>
			                <th width="60">Process Loss</th>
			                <th width="60">Balance</th>
			                <th>Remarks</th>
			            </tr>
					</thead>
				</table>
				<div style="width:1320px; overflow-y: scroll; max-height:380px;" id="scroll_body">
					<table width="1300" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body"> 
					<?  
						$i=1; $tot_fab_issue=0; $tot_fab_recv=0; $tot_processLoss=0; $tot_balance=0; $tot_wo_qnty=0;
						$construction_data_arr=array();
						
						foreach($sql_serviceBooking as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
								<td width="40"><? echo $i; ?></td>
		                        <td width="80" align="center"><? echo $row[csf("booking_no")]; ?></td>
		                        <td width="120"><div style="width:120px; word-wrap:break-word;"><? 
		                        if($row[csf("pay_mode")]==3 || $row[csf("pay_mode")]==5)
		                        	{ echo $company_library[$row[csf("supplier_id")]] ;}
		                        else if($row[csf("pay_mode")]==1){echo $suplier[$row[csf("supplier_id")]];} 
		                        else{echo $company_library[$row[csf("supplier_id")]];} ?>&nbsp;
		                        </div></td>
		                        <td width="120"><div style="width:90px; word-wrap:break-word;"><? echo $buyer_arr[$row[csf("buyer_id")]]; ?>&nbsp;</div></td>
		                        <td width="120"><p><? echo $po_no_arr[$row[csf("po_break_down_id")]]['po_number']; ?>&nbsp;</p></td>
		                        <td width="120"><p><? echo $po_no_arr[$row[csf("po_break_down_id")]]['style_ref_no']; ?>&nbsp;</p></td>
		                        <td width="150"><p><? echo $fabric_description_array[$row[csf("pre_cost_fabric_cost_dtls_id")]]; ?>&nbsp;</p></td>
								<td width="60" align="right"><? echo number_format($row[csf("wo_qnty")],2,'.',''); ?></td>
								<td width="60" align="right"><? $fab_issue=$fabric_issue_recv_arr[$row[csf("booking_no")]][$row[csf("po_break_down_id")]]['batch_issue_qty']; echo number_format($fab_issue,2,'.',''); ?></td>
								<td width="60" align="right"><? $fab_recv=$fabric_issue_recv_arr[$row[csf("booking_no")]][$row[csf("po_break_down_id")]]['batch_recv_qty']; echo number_format($fab_recv,2,'.',''); ?></td>
								<td width="60" align="right"><? $processLoss=($fab_issue-$fab_recv); echo number_format($processLoss,2,'.',''); ?></td>
								<td width="60" align="right"><? $balance=($fab_issue-$fab_recv)-$processLoss; echo number_format($balance,2,'.',''); ?></td>
								<td></td>
							</tr>
							<?	
							$i++;
							
							$tot_wo_qnty+=$row[csf("wo_qnty")]; 
							$tot_fab_issue+=$fab_issue; 
							$tot_fab_recv+=$fab_recv; 
							$tot_processLoss+=$processLoss;
							$tot_balance+=$balance;
							
						}
						
						?>
					</table>
				</div>     
				<table width="1300" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">               
			        <tfoot>
			            <tr>
			            	<th width="40">&nbsp;</th>
			                <th width="80">&nbsp;</th>
			                <th width="120">&nbsp;</th>
			                <th width="120">&nbsp;</th>
			                <th width="120">&nbsp;</th>
			                <th width="120">&nbsp;</th>
			                <th align="right" width="150">Total</th>
			                <th align="right" width="60" id="td_fab_woqnty_id"><? echo number_format($tot_wo_qnty,2,'.',''); ?></th>			                
			                <th align="right" width="60" id="td_fab_issue_id"><? echo number_format($tot_fab_issue,2,'.',''); ?></th>
 							<th align="right" width="60" id="td_fab_recv_id"><? echo number_format($tot_fab_recv,2,'.',''); ?></th>			                
 							<th align="right" width="60" id="td_fab_processloss_id"><? echo number_format($tot_processLoss,2,'.',''); ?></th> 							<th align="right" width="60" id="td_fab_balance_id"><? echo number_format($tot_balance,2,'.',''); ?></th>			                
 							<th>&nbsp;</th>
			            </tr>
			        </tfoot>
			    </table>
			</div>
		</fieldset>
	</div>
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
	$filename=$user_name."_".$name.".xls";
	echo "$total_data####$filename";
	
	disconnect($con);
	exit();
}	
?>