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
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_booking_no').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('cbo_year_selection').value, 'create_booking_no_search_list_view', 'search_div', 'service_booking_wise_finish_fabric_record_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
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
	// $cbo_year = $data[5];
	// var_dump($cbo_year);
	$cbo_year=str_replace("'","",$data[5]);
	if(trim($cbo_year)!=0)
	{
		if($db_type==0) $year_cond=" and YEAR(a.booking_date)=$cbo_year";
		else if($db_type==2) $year_cond=" and to_char(a.booking_date,'YYYY')=$cbo_year";
		else $year_cond="";
	}
	else $year_cond="";

	$approved=array(0=>"No",1=>"Yes");
	$is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$arr=array (2=>$comp,3=>$buyer_arr,5=>$item_category,6=>$fabric_source,7=>$suplier,8=>$approved,9=>$is_ready);
	//pro_batch_create_mst//wo_non_ord_samp_booking_mst

 	 $sql= "select a.id,a.booking_no_prefix_num as no_prefix_num,a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved from wo_booking_mst a,wo_booking_dtls b where $company and a.booking_no=b.booking_no and a.booking_type=3 $buyer $booking_no $booking_date $year_cond and a.status_active=1 and a.is_deleted=0   group by a.id,a.booking_no_prefix_num ,a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved order by id Desc";

	 //echo $sql;

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

if($action=="job_no_popup")
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

	if ($buyer==0) $buyername=""; else $buyername=" and b.buyer_name=$buyer";
	//$cbo_buyer_name=($cbo_buyer_name==0)?"%%" : "%$cbo_buyer_name%";

	$sql = "SELECT distinct b.job_no,b.style_ref_no,b.company_name,b.buyer_name,b.job_no_prefix_num as job_prefix,$year_field from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no $company $buyername $year_cond order by b.job_no desc";

	$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	?>
	<table width="370" border="1" rules="all" class="rpt_table">
		<thead>
	        <tr>
	            <th width="30">SL</th>
	            <th width="100">Style Number</th>
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
		<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value('<? echo $data[csf('style_ref_no')]."__".$data[csf('job_prefix')]; ?>')" style="cursor:pointer;">
			<td width="30"><? echo $i; ?></td>
			<td width="100"><p><? echo $data[csf('style_ref_no')]; ?></p></td>
			<td width="50"><p><? echo $data[csf('job_prefix')]; ?></p></td>
			<td width="80"><p><? echo $buyer[$data[csf('buyer_name')]]; ?></p></td>
			<td width="40" align="center"><p><? echo $data[csf('year')]; ?></p></td>
		</tr>
	    <? $i++;
	} ?>
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
	$txt_job_no = str_replace("'","",$txt_job_no);
	$hidden_style_no = str_replace("'","",$hidden_style_no);

	//var_dump($cbo_process);

	if ($buyer==0) $buyer_cond=""; else $buyer_cond="  and a.buyer_id='$buyer'";
	if ($cbo_process==0) $process_cond=""; else $process_cond="  and b.process='$cbo_process'";
	if ($cbo_process==0) $process_cond_2=""; else $process_cond_2="  and b.process_id='$cbo_process'";
	if ($cbo_process==0) $process_cond_3=""; else $process_cond_3="  and c.process='$cbo_process'";
	if ($cbo_supplier_name==0) $supplier_cond=""; else $supplier_cond="  and a.supplier_id='$cbo_supplier_name'";
	if ($booking_no=="") $booking_num=""; else $booking_num="  and a.booking_no like '%".str_replace("'","",$booking_no)."%'";
	if ($company==0) $comp_cond=""; else $comp_cond=" and a.company_id=$company";
	if ($booking_number_hidden==0) $booking_id_cond=""; else $booking_id_cond=" and a.id=$booking_number_hidden";
	if ($txt_order=="") $order_no_cond=""; else $order_no_cond="  and a.po_number='$txt_order'";
	if ($txt_job_no=="") $job_no_cond=""; else $job_no_cond="  and b.job_no_prefix_num='$txt_job_no'";
	if ($hidden_order_id=="") $order_id_cond=""; else $order_id_cond="  and b.po_break_down_id='$hidden_order_id'";
	if($db_type==0) $year_field_by="and YEAR(a.insert_date)";
	else if($db_type==2) $year_field_by=" and to_char(a.insert_date,'YYYY')";

	if($db_type==0) $year_job_field_by="and YEAR(c.insert_date)";
	else if($db_type==2) $year_job_field_by=" and to_char(c.insert_date,'YYYY')";

	if(trim($year)!=0) $year_cond=" $year_field_by=$year"; else $year_cond="";
	if(trim($year)!=0) $job_year_cond=" $year_job_field_by=$year"; else $job_year_cond="";
	if($txt_date_from && $txt_date_to)
	{
		if($db_type==0)
		{
			$date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
			$date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
			$dates_com="and a.batch_date BETWEEN '$date_from' AND '$date_to'";
			$booking_date_range_cond="and a.booking_date between '$date_from' and '$date_to'";
		}
		if($db_type==2)
		{
			$date_from=change_date_format($txt_date_from,'','',1);
			$date_to=change_date_format($txt_date_to,'','',1);
			$dates_com="and a.batch_date BETWEEN '$date_from' AND '$date_to'";
			$booking_date_range_cond="and a.booking_date between '$date_from' and '$date_to'";
		}
	}

	if($order_no_cond!="" || $job_no_cond!="")
	{
		$po_no_sql_search=sql_select("SELECT a.id, a.po_number,b.style_ref_no from wo_po_break_down a,wo_po_details_master b where a.job_no_mst=b.job_no $order_no_cond $job_no_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		$piIdArr = array();
		foreach($po_no_sql_search as $row)
		{
			if($piIdChk[$row[csf('id')]] == "")
			{
				$piIdChk[$row[csf('id')]] = $row[csf('id')];
				array_push($piIdArr,$row[csf('id')]);
			}

			//$po_id=$row[csf("id")];
		}
	}
	// if ($po_id=="") $order_no_cond_search=""; else $order_no_cond_search=" and b.po_break_down_id='$po_id'";
	//var_dump($piIdArr);
	if (!empty($piIdArr))
	{
		$order_no_cond_search="".where_con_using_array($piIdArr,0,'b.po_break_down_id')."";
	}

	if($cbo_process!=1)
	{
		/*$sql_service_booking="SELECT a.id,b.id as dtls_id,a.booking_no_prefix_num, b.job_no as job_no_prefix_num,a.booking_no,a.booking_date,a.company_id,a.buyer_id,b.po_break_down_id,b.dia_width,b.fabric_color_id,b.gmts_color_id,a.item_category,a.fabric_source,a.supplier_id,a.pay_mode,b.pre_cost_fabric_cost_dtls_id,sum(b.wo_qnty) as wo_qnty,b.process
		from wo_booking_mst a,wo_booking_dtls b
		where a.booking_no=b.booking_no and a.booking_type=3 and a.status_active=1 and a.is_deleted=0 and a.company_id='$company' $process_cond $booking_num $booking_id_cond $order_no_cond_search $order_id_cond $buyer_cond $supplier_cond $year_cond $booking_date_range_cond and b.wo_qnty>0
		group by a.id,b.id,a.booking_no_prefix_num, b.job_no,a.booking_no,a.booking_date,a.company_id,a.buyer_id,b.po_break_down_id,b.dia_width,b.fabric_color_id,b.gmts_color_id,a.item_category,a.fabric_source,a.supplier_id,a.pay_mode,b.pre_cost_fabric_cost_dtls_id,b.process order by a.booking_no";*/
		$sql_service_booking="SELECT a.id,b.id as dtls_id,a.booking_no_prefix_num, b.job_no as job_no_prefix_num,a.booking_no,a.booking_date,a.company_id,a.buyer_id,b.po_break_down_id,b.dia_width,b.fabric_color_id,a.item_category,a.fabric_source,a.supplier_id,a.pay_mode,b.pre_cost_fabric_cost_dtls_id,sum(b.wo_qnty) as wo_qnty,b.process
		from wo_booking_mst a,wo_booking_dtls b,wo_po_details_master c
		where a.booking_no=b.booking_no and b.job_no=c.job_no and a.booking_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id='$company' $process_cond $booking_num $booking_id_cond $order_no_cond_search $order_id_cond $buyer_cond $supplier_cond $job_year_cond $booking_date_range_cond and b.wo_qnty>0
		group by a.id,b.id,a.booking_no_prefix_num, b.job_no,a.booking_no,a.booking_date,a.company_id,a.buyer_id,b.po_break_down_id,b.dia_width,b.fabric_color_id,a.item_category,a.fabric_source,a.supplier_id,a.pay_mode,b.pre_cost_fabric_cost_dtls_id,b.process order by a.booking_no";
		//echo $sql_service_booking;
		$sql_serviceBooking=sql_select($sql_service_booking);

		$booking_nos=$po_break_down_ids=$pre_cost_fabric_cost_dtls_id="";
		foreach($sql_serviceBooking as $row)
		{
			$booking_nos.="'".$row[csf("booking_no")]."',";
			$po_break_down_ids.=$row[csf("po_break_down_id")].",";
			$pre_cost_fabric_cost_dtls_id.=$row[csf("pre_cost_fabric_cost_dtls_id")].",";

			$new_data_service_booking[$row[csf("booking_no")]][$row[csf("supplier_id")]][$row[csf("buyer_id")]][$row[csf("po_break_down_id")]][$row[csf("pre_cost_fabric_cost_dtls_id")]][$row[csf("fabric_color_id")]]['booking_no_prefix_num']=$row[csf("booking_no_prefix_num")];

			$new_data_service_booking[$row[csf("booking_no")]][$row[csf("supplier_id")]][$row[csf("buyer_id")]][$row[csf("po_break_down_id")]][$row[csf("pre_cost_fabric_cost_dtls_id")]][$row[csf("fabric_color_id")]]['supplier_id']=$row[csf("supplier_id")];

			$new_data_service_booking[$row[csf("booking_no")]][$row[csf("supplier_id")]][$row[csf("buyer_id")]][$row[csf("po_break_down_id")]][$row[csf("pre_cost_fabric_cost_dtls_id")]][$row[csf("fabric_color_id")]]['buyer_id']=$row[csf("buyer_id")];

			$new_data_service_booking[$row[csf("booking_no")]][$row[csf("supplier_id")]][$row[csf("buyer_id")]][$row[csf("po_break_down_id")]][$row[csf("pre_cost_fabric_cost_dtls_id")]][$row[csf("fabric_color_id")]]['po_break_down_id']=$row[csf("po_break_down_id")];

			$new_data_service_booking[$row[csf("booking_no")]][$row[csf("supplier_id")]][$row[csf("buyer_id")]][$row[csf("po_break_down_id")]][$row[csf("pre_cost_fabric_cost_dtls_id")]][$row[csf("fabric_color_id")]]['pre_cost_fabric_cost_dtls_id']=$row[csf("pre_cost_fabric_cost_dtls_id")];

			$new_data_service_booking[$row[csf("booking_no")]][$row[csf("supplier_id")]][$row[csf("buyer_id")]][$row[csf("po_break_down_id")]][$row[csf("pre_cost_fabric_cost_dtls_id")]][$row[csf("fabric_color_id")]]['fabric_color_id']=$row[csf("fabric_color_id")];

			$new_data_service_booking[$row[csf("booking_no")]][$row[csf("supplier_id")]][$row[csf("buyer_id")]][$row[csf("po_break_down_id")]][$row[csf("pre_cost_fabric_cost_dtls_id")]][$row[csf("fabric_color_id")]]['wo_qnty']+=$row[csf("wo_qnty")];


			$new_data_service_booking[$row[csf("booking_no")]][$row[csf("supplier_id")]][$row[csf("buyer_id")]][$row[csf("po_break_down_id")]][$row[csf("pre_cost_fabric_cost_dtls_id")]][$row[csf("fabric_color_id")]]['pay_mode']=$row[csf("pay_mode")];
			$new_data_service_booking[$row[csf("booking_no")]][$row[csf("supplier_id")]][$row[csf("buyer_id")]][$row[csf("po_break_down_id")]][$row[csf("pre_cost_fabric_cost_dtls_id")]][$row[csf("fabric_color_id")]]['dia_width']=$row[csf("dia_width")];

			$new_data_service_booking[$row[csf("booking_no")]][$row[csf("supplier_id")]][$row[csf("buyer_id")]][$row[csf("po_break_down_id")]][$row[csf("pre_cost_fabric_cost_dtls_id")]][$row[csf("fabric_color_id")]]['dtls_id'].=$row[csf("dtls_id")].',';
			$new_data_service_booking[$row[csf("booking_no")]][$row[csf("supplier_id")]][$row[csf("buyer_id")]][$row[csf("po_break_down_id")]][$row[csf("pre_cost_fabric_cost_dtls_id")]][$row[csf("fabric_color_id")]]['process']=$row[csf("process")];
		}
		/* echo "<pre>";
		print_r($new_data_service_booking);
		echo "</pre>";
		die; */
		$booking_nos=chop($booking_nos,",");
		$po_break_down_ids=chop($po_break_down_ids,",");
		$pre_cost_fabric_cost_dtls_id=chop($pre_cost_fabric_cost_dtls_id,",");
		//$po_no_arr = return_library_array( "select id, po_number from wo_po_break_down where id in($po_break_down_ids) and status_active=1 and is_deleted=0",'id','po_number');

		// echo "select a.id, a.po_number,b.style_ref_no from wo_po_break_down a,wo_po_details_master b where a.id in($po_break_down_ids) and a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	 	$po_no_sql=sql_select("select a.id, a.po_number,b.style_ref_no from wo_po_break_down a,wo_po_details_master b where a.id in($po_break_down_ids) and a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach($po_no_sql as $row)
		{
			$po_no_arr[$row[csf("id")]]['po_number']=$row[csf("po_number")];
			$po_no_arr[$row[csf("id")]]['style_ref_no']=$row[csf("style_ref_no")];
		}

		$fabric_isssueToProcess_sql = "SELECT b.booking_no,b.booking_dtls_id, b.order_id,b.body_part_id,TO_CHAR(c.fabric_color_id) as color_id,b.width as width,
		sum(case when a.entry_form=91 then b.batch_issue_qty else 0 end) as batch_issue_qty,
		sum(case when a.entry_form=92 then b.batch_issue_qty else 0 end) as batch_recv_qty,sum(case when a.entry_form=92 then b.grey_used else 0 end) as grey_used_qty, b.febric_description_id,a.entry_form
		from inv_receive_mas_batchroll a, pro_grey_batch_dtls b left join wo_booking_dtls c on b.booking_dtls_id=c.id where a.id=b.mst_id and a.entry_form in(91,92) and  b.order_id in($po_break_down_ids) $comp_cond $process_cond_2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by  b.booking_no,b.booking_dtls_id, b.order_id,b.febric_description_id,b.body_part_id ,c.fabric_color_id,b.width,a.entry_form
		union all
		select TO_CHAR(a.booking_no) as booking_no, c.id as booking_dtls_id, d.po_breakdown_id as order_id ,b.body_part_id as body_part_id ,TO_CHAR(c.fabric_color_id) as color_id,TO_CHAR(e.dia_width) as width, 0 as batch_issue_qty,
		sum(case when a.entry_form=22 then d.quantity else 0 end) as batch_recv_qty, 0 as grey_used_qty,
		b.febric_description_id as febric_description_id,a.entry_form
		from inv_receive_master a, pro_grey_prod_entry_dtls b,wo_booking_dtls c,order_wise_pro_details d,product_details_master e
		where a.id=b.mst_id and a.booking_no=c.booking_no and b.id=d.dtls_id and b.trans_id=d.trans_id and d.prod_id=e.id and a.receive_basis=11 and a.entry_form=22 and  d.po_breakdown_id in($po_break_down_ids) $comp_cond $process_cond_3
		and a.item_category=13 and d.entry_form=22 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
		and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0
		group by a.booking_no, c.id, d.po_breakdown_id ,b.body_part_id ,c.fabric_color_id,e.dia_width,b.febric_description_id,a.entry_form
		union all
		select a.wo_no as booking_no,b.booking_dtls_id, b.order_id,b.body_part_id,b.color_id,b.width as width,
		sum(case when a.entry_form=63 then b.ROLL_WGT else 0 end) as batch_issue_qty,
		sum(case when a.entry_form=65 then b.ROLL_WGT else 0 end) as batch_recv_qty,
		0 as grey_used_qty,
		c.detarmination_id as febric_description_id,a.entry_form
		from inv_receive_mas_batchroll a, pro_grey_batch_dtls b , product_details_master c
		where a.id=b.mst_id and a.entry_form in(63,65) and b.order_id in($po_break_down_ids) $comp_cond $process_cond_2
		and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.prod_id= c.id
		group by a.wo_no,b.booking_dtls_id, b.order_id,b.body_part_id ,b.color_id,b.width,a.entry_form, c.detarmination_id";
		//echo $fabric_isssueToProcess_sql;

		$fabric_isssueToProcess_sql_data =sql_select($fabric_isssueToProcess_sql);
		foreach($fabric_isssueToProcess_sql_data as $row)
		{
			$fabric_issue_recv_arr[$row[csf("booking_no")]][$row[csf("order_id")]][$row[csf("febric_description_id")]][$row[csf("body_part_id")]][$row[csf("color_id")]][$row[csf("booking_dtls_id")]]['batch_issue_qty']+=$row[csf("batch_issue_qty")];
			$fabric_issue_recv_arr[$row[csf("booking_no")]][$row[csf("order_id")]][$row[csf("febric_description_id")]][$row[csf("body_part_id")]][$row[csf("color_id")]][$row[csf("booking_dtls_id")]]['batch_recv_qty']+=$row[csf("batch_recv_qty")];

			if($row[csf("entry_form")]==92)
			{
				$fabric_issue_recv_arr[$row[csf("booking_no")]][$row[csf("order_id")]][$row[csf("febric_description_id")]][$row[csf("body_part_id")]][$row[csf("color_id")]]['grey_used_qty']+=$row[csf("grey_used_qty")];
			}


			if($row[csf("entry_form")]==63 || $row[csf("entry_form")]==65)
			{
				$fabric_issue_recv_arr[$row[csf("booking_no")]][$row[csf("order_id")]][$row[csf("febric_description_id")]][$row[csf("body_part_id")]][$row[csf("color_id")]]['batch_recv_qty']=$row[csf("batch_recv_qty")];
				$fabric_issue_recv_arr[$row[csf("booking_no")]][$row[csf("order_id")]][$row[csf("febric_description_id")]][$row[csf("body_part_id")]][$row[csf("color_id")]]['batch_issue_qty']+=$row[csf("batch_issue_qty")];
			}
			//echo $row[csf("booking_no")].'='.$row[csf("order_id")].'='.$row[csf("febric_description_id")].'='.$row[csf("body_part_id")].'='.$row[csf("width")].'='.$row[csf("color_id")].'='.$row[csf("booking_dtls_id")].'*<br>';

		}
		//echo "<pre>";print_r($fabric_issue_recv_arr);


		$fabric_isssueToProcess_sql2 = "select  TO_CHAR(a.booking_no) as booking_no,0 as booking_dtls_id, d.po_breakdown_id as order_id ,b.body_part_id as body_part_id ,
		TO_CHAR(b.color_id) as color_id,TO_CHAR(e.dia_width) as width, 0 as batch_issue_qty,
		sum(case when a.entry_form=37 then d.quantity else 0 end) as batch_recv_qty,
		sum(case when a.entry_form=37 then d.grey_used_qty else 0 end) as grey_used_qty,
		b.fabric_description_id as febric_description_id,a.entry_form,b.pre_cost_fabric_cost_dtls_id
		from inv_receive_master a, pro_finish_fabric_rcv_dtls b,order_wise_pro_details d,product_details_master e
		where a.id=b.mst_id and b.id=d.dtls_id and b.trans_id=d.trans_id and d.prod_id=e.id and a.entry_form=37 and a.item_category=2 and  a.receive_basis=11 and
		d.po_breakdown_id in($po_break_down_ids) $comp_cond  and d.entry_form=37
		and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0
		group by a.booking_no, d.po_breakdown_id ,b.body_part_id ,b.color_id,e.dia_width,b.fabric_description_id,a.entry_form,b.pre_cost_fabric_cost_dtls_id";
		//echo $fabric_isssueToProcess_sql2;
		$fabric_isssueToProcess_sql2 =sql_select($fabric_isssueToProcess_sql2);
		foreach($fabric_isssueToProcess_sql2 as $row)
		{
			if($row[csf("entry_form")]==37)
			{
				$fabric_issue_recv_arr2[$row[csf("pre_cost_fabric_cost_dtls_id")]][$row[csf("booking_no")]][$row[csf("order_id")]][$row[csf("febric_description_id")]][$row[csf("body_part_id")]][$row[csf("color_id")]]['batch_recv_qty']+=$row[csf("batch_recv_qty")];
				$fabric_issue_recv_arr2[$row[csf("pre_cost_fabric_cost_dtls_id")]][$row[csf("booking_no")]][$row[csf("order_id")]][$row[csf("febric_description_id")]][$row[csf("body_part_id")]][$row[csf("color_id")]]['grey_used_qty']+=$row[csf("grey_used_qty")];

			}
		}
		/* echo "<pre>";
		print_r($fabric_issue_recv_arr);
		echo "</pre>"; */



		$fabric_Desc_sql= sql_select("SELECT a.id,b.lib_yarn_count_deter_id,c.booking_no,b.body_part_id
		from wo_pre_cost_fab_conv_cost_dtls a, wo_pre_cost_fabric_cost_dtls b, wo_booking_dtls c
		where b.job_no=c.job_no and b.status_active=1 and c.is_deleted=0 and c.po_break_down_id in($po_break_down_ids) and a.id in($pre_cost_fabric_cost_dtls_id) and a.fabric_description = b.id and c.booking_type = 3 group by a.id,b.lib_yarn_count_deter_id,c.booking_no,b.body_part_id order by c.booking_no");
		foreach( $fabric_Desc_sql as $row)
		{
			$fabric_des_id_arr[$row[csf("id")]][$row[csf("booking_no")]]=$row[csf("lib_yarn_count_deter_id")];
			$fabric_des_body_part_id_arr[$row[csf("id")]][$row[csf("booking_no")]]=$row[csf("body_part_id")];
		}
		$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select  c.job_no,c.id,c.fabric_description,c.cons_process from wo_booking_dtls b,wo_pre_cost_fab_conv_cost_dtls c  where c.id=b.pre_cost_fabric_cost_dtls_id and b.booking_no in($booking_nos) group by c.job_no,c.id,c.fabric_description,c.cons_process");
		//echo "select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where job_no='$data[0]' ";
		foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id)
		{
			if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]!=0)
			{
				$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description,lib_yarn_count_deter_id  from  wo_pre_cost_fabric_cost_dtls
				where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."'");
				list($fabric_description_row)=$fabric_description;
				$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$body_part[$fabric_description_row[csf("body_part_id")]].',
				'.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")];
			}
			if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]==0)
			{
				$fabric_description_string="";
				$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description,lib_yarn_count_deter_id from  wo_pre_cost_fabric_cost_dtls
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
					<table width="1360" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
						<thead>
				        	<tr>
				                <th width="40">SL</th>
				                <th width="80">WO No</th>
				                <th width="120">Party Name</th>
				                <th width="120">Buyer</th>
				                <th width="120">Order</th>
				                <th width="120">Style</th>
				                <th width="150">Item Description</th>
				                <th width="150">Fabric Color</th>
				                <th width="60">WO Qty</th>
				                <th width="60">Fabric Issue</th>
				                <th width="60">Fabric Received</th>
				                <th width="60">Grey Qnty <br>Received</th>
				                <th width="60">Balance</th>
				                <th width="60">Grey Balance</th>
				                <th>Remarks</th>
				            </tr>
						</thead>
					</table>
					<div style="width:1380px; overflow-y: scroll; max-height:380px;" id="scroll_body">
						<table width="1360" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
						<?
							$tot_fab_issue=0; $tot_fab_recv=0; $tot_processLoss=0; $tot_balance=0; $tot_wo_qnty=0;$tot_grey_recv=0;$tot_grey_balance=0;
							$construction_data_arr=array();
							$colspan=1;$rowspanArr=array();
							foreach($new_data_service_booking as $bookingNo => $bookingData)
							{
								foreach($bookingData as $suplierID => $suplierData)
								{
									foreach($suplierData as $buyerID => $buyerData)
									{
										foreach($buyerData as $po_id => $poData)
										{
											foreach($poData as $fabrication_id => $fabricDAta)
											{
												foreach($fabricDAta as $fabColorId => $row)
												{
													//$rowspanArr[$customerID][$dates][$purpose]+=1;
													$rowspanArr[$bookingNo][$suplierID][$buyerID][$po_id][$fabrication_id]+=$colspan;
												}
											}
										}
									}
								}
							}
							/*echo "<pre>";
							print_r($rowspanArr);
							echo "</pre>";*/

							//$new_data_service_booking[$row[csf("booking_no")]][$row[csf("supplier_id")]][$row[csf("buyer_id")]][$row[csf("po_break_down_id")]][$row[csf("pre_cost_fabric_cost_dtls_id")]][$row[csf("gmts_color_id")]]['booking_no_prefix_num']
							$sl=1;
							$i=1;
							foreach($new_data_service_booking as $bookingNo => $bookingData)
							{
								foreach($bookingData as $suplierID => $suplierData)
								{
									foreach($suplierData as $buyerID => $buyerData)
									{
										foreach($buyerData as $po_id => $poData)
										{
											foreach($poData as $fabrication_id => $fabricDAta)
											{
												$k=1;
												foreach($fabricDAta as $fabColorId => $row)
												{
													//echo "<pre>";print_r($row);
													/*foreach($sql_serviceBooking as $row)
													{*/

													//echo $bookingNo.'='.$po_id.'='.$fabric_des_id_arr[$fabrication_id][$bookingNo].'='.$fabric_des_body_part_id_arr[$fabrication_id][$bookingNo].'='.$row["dia_width"].'='.$fabColorId.'='.$row["dtls_id"]."#<br>";

													if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
													?>

													<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $sl;?>','<? echo $bgcolor;?>')" id="tr<? echo $sl;?>">

														<?
														if($k==1)
														{
															?>

															<td rowspan="<? echo $rowspanArr[$bookingNo][$suplierID][$buyerID][$po_id][$fabrication_id]; ?>" width="40"><? echo $i; ?></td>
									                        <td rowspan="<? echo $rowspanArr[$bookingNo][$suplierID][$buyerID][$po_id][$fabrication_id]; ?>" width="80" align="center"><? echo $bookingNo; ?></td>
									                        <td title="<? echo $knit_dye_source;?>" rowspan="<? echo $rowspanArr[$bookingNo][$suplierID][$buyerID][$po_id][$fabrication_id]; ?>" align="center" width="120"><div style="width:120px; word-wrap:break-word;">
															<?
															if($row["pay_mode"]==3 || $row["pay_mode"]==5)
															{ echo $company_library[$suplierID] ;}
															else if($row["pay_mode"]==1 || $row["pay_mode"]==2){echo $suplier[$suplierID];}
															else{echo $company_library[$suplierID];}
															?>
															&nbsp;
									                        </div></td>
									                        <td rowspan="<? echo $rowspanArr[$bookingNo][$suplierID][$buyerID][$po_id][$fabrication_id]; ?>" width="120"><div style="width:90px; word-wrap:break-word;"><? echo $buyer_arr[$buyerID]; ?>&nbsp;</div></td>
									                        <td rowspan="<? echo $rowspanArr[$bookingNo][$suplierID][$buyerID][$po_id][$fabrication_id]; ?>" width="120" title="<? echo $po_id; ?>"><p><? echo $po_no_arr[$po_id]['po_number']; ?>&nbsp;</p></td>
									                        <td rowspan="<? echo $rowspanArr[$bookingNo][$suplierID][$buyerID][$po_id][$fabrication_id]; ?>" width="120"><p><? echo $po_no_arr[$po_id]['style_ref_no']; ?>&nbsp;</p></td>
									                        <td rowspan="<? echo $rowspanArr[$bookingNo][$suplierID][$buyerID][$po_id][$fabrication_id]; ?>" width="150" title="<? echo $fabrication_id; ?>"><p><? echo $fabric_description_array[$fabrication_id]; ?>&nbsp;</p></td>
															<?
															$sl++;
									                     $i++;
									                    }
									                    ?>

								                        <td rowspan="<? //echo $rowspanArr[$bookingNo][$suplierID][$buyerID][$po_id][$fabrication_id]; ?>" width="150" align="center" title="<? echo $fabColorId; ?>"><p><? echo $color_library[$fabColorId]; ?>&nbsp;</p></td>

														<td width="60" align="right"><? echo number_format($row["wo_qnty"],2,'.',''); ?></td>

														<td width="60" align="right">
															<?
															//echo $bookingNo."=".$po_id."=".$fabric_des_id_arr[$fabrication_id][$bookingNo]."=".$fabric_des_body_part_id_arr[$fabrication_id][$bookingNo]."=".$row['dia_width']."=".$fabColorId."=".$dtls_id."<br/>";
															$dtls_ids=array_unique(explode(",",chop($row["dtls_id"],',')));
															//echo "<pre>";print_r($dtls_ids);
															$fab_iss = "";
															foreach ($dtls_ids as $dtls_id) 
															{
																$fab_iss +=$fabric_issue_recv_arr[$bookingNo][$po_id][$fabric_des_id_arr[$fabrication_id][$bookingNo]][$fabric_des_body_part_id_arr[$fabrication_id][$bookingNo]][$fabColorId][$dtls_id]['batch_issue_qty'];
															}
															$fab_issue=$fab_iss+$fabric_issue_recv_arr[$bookingNo][$po_id][$fabric_des_id_arr[$fabrication_id][$bookingNo]][$fabric_des_body_part_id_arr[$fabrication_id][$bookingNo]][$fabColorId]['batch_issue_qty'];
															//91 + 63
															echo number_format($fab_issue,2,'.','');
															?>
														</td>
														<td width="60" align="right">
															<?
															if ($row["process"]==31 || $row["process"]==193)
															{
																$fab_recv=$fabric_issue_recv_arr[$bookingNo][$po_id][$fabric_des_id_arr[$fabrication_id][$bookingNo]][$fabric_des_body_part_id_arr[$fabrication_id][$bookingNo]][$fabColorId]['batch_recv_qty']+$fabric_issue_recv_arr2[$fabrication_id][$bookingNo][$po_id][$fabric_des_id_arr[$fabrication_id][$bookingNo]][$fabric_des_body_part_id_arr[$fabrication_id][$bookingNo]][$fabColorId]['batch_recv_qty'];


																$grey_used_qnty = $fabric_issue_recv_arr[$bookingNo][$po_id][$fabric_des_id_arr[$fabrication_id][$bookingNo]][$fabric_des_body_part_id_arr[$fabrication_id][$bookingNo]][$fabColorId]['grey_used_qty']+$fabric_issue_recv_arr2[$fabrication_id][$bookingNo][$po_id][$fabric_des_id_arr[$fabrication_id][$bookingNo]][$fabric_des_body_part_id_arr[$fabrication_id][$bookingNo]][$fabColorId]['grey_used_qty'];
																//$grey_used_qnty = 1;
																//echo $grey_used_qnty;
															}
															else if ($row["process"]==35)
															{
																$dtls_ids=array_unique(explode(",",chop($row["dtls_id"],',')));
																//echo "<pre>";print_r($dtls_ids);
																$fab_receive = "";
																foreach ($dtls_ids as $dtls_id) 
																{
																	$fab_receive +=$fabric_issue_recv_arr[$bookingNo][$po_id][$fabric_des_id_arr[$fabrication_id][$bookingNo]][$fabric_des_body_part_id_arr[$fabrication_id][$bookingNo]][$fabColorId][$dtls_id]['batch_recv_qty'];
																}
																$fab_recv=$fab_receive+$fabric_issue_recv_arr[$bookingNo][$po_id][$fabric_des_id_arr[$fabrication_id][$bookingNo]][$fabric_des_body_part_id_arr[$fabrication_id][$bookingNo]][$fabColorId]['batch_recv_qty']+$fabric_issue_recv_arr2[$fabrication_id][$bookingNo][$po_id][$fabric_des_id_arr[$fabrication_id][$bookingNo]][$fabric_des_body_part_id_arr[$fabrication_id][$bookingNo]][$fabColorId]['batch_recv_qty'];

																$grey_used_qnty = $fabric_issue_recv_arr[$bookingNo][$po_id][$fabric_des_id_arr[$fabrication_id][$bookingNo]][$fabric_des_body_part_id_arr[$fabrication_id][$bookingNo]][$fabColorId]['grey_used_qty']+$fabric_issue_recv_arr2[$fabrication_id][$bookingNo][$po_id][$fabric_des_id_arr[$fabrication_id][$bookingNo]][$fabric_des_body_part_id_arr[$fabrication_id][$bookingNo]][$fabColorId]['grey_used_qty'];
															}
															else
															{
																
																$dtls_ids=array_unique(explode(",",chop($row["dtls_id"],',')));
																//echo "<pre>";print_r($dtls_ids);
																$fab_receive = "";
																foreach ($dtls_ids as $dtls_id) 
																{
																	$fab_receive +=$fabric_issue_recv_arr[$bookingNo][$po_id][$fabric_des_id_arr[$fabrication_id][$bookingNo]][$fabric_des_body_part_id_arr[$fabrication_id][$bookingNo]][$fabColorId][$dtls_id]['batch_recv_qty'];
																}
																$fab_recv= $fab_receive+$fabric_issue_recv_arr[$bookingNo][$po_id][$fabric_des_id_arr[$fabrication_id][$bookingNo]][$fabric_des_body_part_id_arr[$fabrication_id][$bookingNo]][$fabColorId]['batch_recv_qty'];

																//entry_form 92  +  65
															}

															//echo number_format($fab_recv,2,'.','');

															//echo $bookingNo."=".$po_id."=".$fabric_des_id_arr[$fabrication_id][$bookingNo]."=".$fabric_des_body_part_id_arr[$fabrication_id][$bookingNo]."=".$row["dia_width"]."=".$fabColorId."=".$row["dtls_id"];
															?>
															<a href="##" onclick="openmypage_qnty('<? echo $bookingNo;?>','<? echo $po_id;?>','openmypage_receive','<? echo $fabric_des_id_arr[$fabrication_id][$bookingNo];?>','<? echo $fabric_des_body_part_id_arr[$fabrication_id][$bookingNo];?>','<? echo $row["dia_width"]; ?>','<? echo $fabColorId;?>','<? echo implode(',',array_unique(explode(",",chop($row["dtls_id"],','))));?>','','','<? echo $row["process"];?>','<? echo $fabrication_id; ?>');"><? echo number_format($fab_recv,2,".",""); ?>

															</a>
														</td>

														<td width="60" align="right">
														<a href="##" onclick="openmypage_qnty('<? echo $bookingNo;?>','<? echo $po_id;?>','openmypage_grey_receive','<? echo $fabric_des_id_arr[$fabrication_id][$bookingNo];?>','<? echo $fabric_des_body_part_id_arr[$fabrication_id][$bookingNo];?>','<? echo $row["dia_width"]; ?>','<? echo $fabColorId;?>','<? echo implode(',',array_unique(explode(",",chop($row["dtls_id"],','))));?>','','','<? echo $row["process"];?>','<? echo $fabrication_id; ?>');"><? echo number_format($grey_used_qnty,2,".",""); ?>
														</a>
															<? //echo number_format($grey_used_qnty,2,'.',''); ?>
														</td>
														<td width="60" align="right"><? $balance=($row["wo_qnty"]-$fab_recv); echo number_format($balance,2,'.',''); ?></td>
														<td width="60" align="right"><? $grey_balance=($row["wo_qnty"]-$grey_used_qnty); echo number_format($grey_balance,2,'.',''); ?></td>
														<td></td>
													</tr>
													<?

													$tot_wo_qnty+=$row["wo_qnty"];
													$tot_fab_issue+=$fab_issue;
													$tot_fab_recv+=$fab_recv;
													$tot_grey_recv+=$grey_used_qnty;
													$tot_processLoss+=$processLoss;
													$tot_balance+=$balance;
													$tot_grey_balance+=$grey_balance;
													$k++;$sl++;
												}
											}
										}
									}
								}

							}
							?>
						</table>
					</div>
					<table width="1360" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
				        <tfoot>
				            <tr>
				            	<th width="40">&nbsp;</th>
				                <th width="80">&nbsp;</th>
				                <th width="120">&nbsp;</th>
				                <th width="120">&nbsp;</th>
				                <th width="120">&nbsp;</th>
				                <th width="120">&nbsp;</th>
				                <th width="150">&nbsp;</th>
				                <th align="right" width="150">Total</th>
				                <th align="right" width="60" id="td_fab_woqnty_id"><? echo number_format($tot_wo_qnty,2,'.',''); ?></th>
				                <th align="right" width="60" id="td_fab_issue_id"><? echo number_format($tot_fab_issue,2,'.',''); ?></th>
	 							<th align="right" width="60" id="td_fab_recv_id"><? echo number_format($tot_fab_recv,2,'.',''); ?></th>
	 							<th align="right" width="60" id="td_fab_processloss_id"><? echo number_format($tot_grey_recv,2,'.',''); ?></th>
								<th align="right" width="60" id="td_fab_balance_id"><? echo number_format($tot_balance,2,'.',''); ?></th>
								<th align="right" width="60" id="td_fab_balance_id"><? echo number_format($tot_grey_balance,2,'.',''); ?></th>
	 							<th>&nbsp;</th>
				            </tr>
				        </tfoot>
				    </table>
				</div>
			</fieldset>
		</div>
		<?
	}
	else //only for KNITTING process
	{
		$sql_service_booking="select a.id,a.booking_no_prefix_num, b.job_no as job_no_prefix_num,a.booking_no,a.booking_date,a.company_id,a.buyer_id,b.po_break_down_id,b.dia_width,a.item_category,a.fabric_source,a.supplier_id,a.pay_mode,b.pre_cost_fabric_cost_dtls_id,sum(b.wo_qnty) as wo_qnty,b.process from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=3 and a.status_active=1 and a.is_deleted=0 and a.company_id='$company' $process_cond $booking_num $order_no_cond_search $order_id_cond $buyer_cond $supplier_cond $year_cond $booking_date_range_cond and b.wo_qnty>0 group by a.id,a.booking_no_prefix_num, b.job_no,a.booking_no,a.booking_date,a.company_id,a.buyer_id,b.po_break_down_id,b.dia_width,a.item_category,a.fabric_source,a.supplier_id,a.pay_mode,b.pre_cost_fabric_cost_dtls_id,b.process order by a.booking_no";
		$sql_serviceBooking=sql_select($sql_service_booking);

		$booking_nos=$po_break_down_ids=$pre_cost_fabric_cost_dtls_id="";
		foreach($sql_serviceBooking as $row)
		{
			$booking_nos.="'".$row[csf("booking_no")]."',";
			$po_break_down_ids.=$row[csf("po_break_down_id")].",";
			$pre_cost_fabric_cost_dtls_id.=$row[csf("pre_cost_fabric_cost_dtls_id")].",";

			$new_data_service_booking[$row[csf("booking_no")]][$row[csf("supplier_id")]][$row[csf("buyer_id")]][$row[csf("po_break_down_id")]][$row[csf("pre_cost_fabric_cost_dtls_id")]]['booking_no_prefix_num']=$row[csf("booking_no_prefix_num")];

			$new_data_service_booking[$row[csf("booking_no")]][$row[csf("supplier_id")]][$row[csf("buyer_id")]][$row[csf("po_break_down_id")]][$row[csf("pre_cost_fabric_cost_dtls_id")]]['supplier_id']=$row[csf("supplier_id")];

			$new_data_service_booking[$row[csf("booking_no")]][$row[csf("supplier_id")]][$row[csf("buyer_id")]][$row[csf("po_break_down_id")]][$row[csf("pre_cost_fabric_cost_dtls_id")]]['buyer_id']=$row[csf("buyer_id")];

			$new_data_service_booking[$row[csf("booking_no")]][$row[csf("supplier_id")]][$row[csf("buyer_id")]][$row[csf("po_break_down_id")]][$row[csf("pre_cost_fabric_cost_dtls_id")]]['po_break_down_id']=$row[csf("po_break_down_id")];

			$new_data_service_booking[$row[csf("booking_no")]][$row[csf("supplier_id")]][$row[csf("buyer_id")]][$row[csf("po_break_down_id")]][$row[csf("pre_cost_fabric_cost_dtls_id")]]['pre_cost_fabric_cost_dtls_id']=$row[csf("pre_cost_fabric_cost_dtls_id")];


			$new_data_service_booking[$row[csf("booking_no")]][$row[csf("supplier_id")]][$row[csf("buyer_id")]][$row[csf("po_break_down_id")]][$row[csf("pre_cost_fabric_cost_dtls_id")]]['wo_qnty']=$row[csf("wo_qnty")];


			$new_data_service_booking[$row[csf("booking_no")]][$row[csf("supplier_id")]][$row[csf("buyer_id")]][$row[csf("po_break_down_id")]][$row[csf("pre_cost_fabric_cost_dtls_id")]]['pay_mode']=$row[csf("pay_mode")];
			$new_data_service_booking[$row[csf("booking_no")]][$row[csf("supplier_id")]][$row[csf("buyer_id")]][$row[csf("po_break_down_id")]][$row[csf("pre_cost_fabric_cost_dtls_id")]]['dia_width']=$row[csf("dia_width")];

			$new_data_service_booking[$row[csf("booking_no")]][$row[csf("supplier_id")]][$row[csf("buyer_id")]][$row[csf("po_break_down_id")]][$row[csf("pre_cost_fabric_cost_dtls_id")]]['process']=$row[csf("process")];
		}
		/*echo "<pre>";
		print_r($new_data_service_booking);
		echo "</pre>";
		die;*/
		$booking_nos=chop($booking_nos,",");
		$po_break_down_ids=chop($po_break_down_ids,",");
		$pre_cost_fabric_cost_dtls_id=chop($pre_cost_fabric_cost_dtls_id,",");
		//$po_no_arr = return_library_array( "select id, po_number from wo_po_break_down where id in($po_break_down_ids) and status_active=1 and is_deleted=0",'id','po_number');

	 	$po_no_sql=sql_select("select a.id, a.po_number,b.style_ref_no from wo_po_break_down a,wo_po_details_master b where a.id in($po_break_down_ids) and a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach($po_no_sql as $row)
		{
			$po_no_arr[$row[csf("id")]]['po_number']=$row[csf("po_number")];
			$po_no_arr[$row[csf("id")]]['style_ref_no']=$row[csf("style_ref_no")];
		}


		/*$fabric_isssueToProcess_sql =sql_select("select TO_CHAR(a.booking_no) as booking_no, d.po_breakdown_id as order_id ,b.body_part_id as body_part_id,TO_CHAR(e.dia_width) as width, 0 as batch_issue_qty,
		sum(case when a.entry_form=22 then d.quantity else 0 end) as batch_recv_qty,b.febric_description_id as febric_description_id,a.entry_form
		from inv_receive_master a, pro_grey_prod_entry_dtls b,wo_booking_dtls c,order_wise_pro_details d,product_details_master e
		where a.id=b.mst_id and a.booking_no=c.booking_no and b.id=d.dtls_id and b.trans_id=d.trans_id and d.prod_id=e.id and a.receive_basis=11 and a.entry_form=22 and  d.po_breakdown_id in($po_break_down_ids) $comp_cond $process_cond_3
		and a.item_category=13 and d.entry_form=22 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
		and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0
		group by a.booking_no, d.po_breakdown_id ,b.body_part_id,e.dia_width,b.febric_description_id,a.entry_form");*/

		$fabric_isssueToProcess_sql =sql_select("select TO_CHAR(a.booking_no) as booking_no, d.po_breakdown_id as order_id ,b.body_part_id as body_part_id,TO_CHAR(e.dia_width) as width, 0 as batch_issue_qty,
		sum(case when a.entry_form=22 then d.quantity else 0 end) as batch_recv_qty,b.febric_description_id as febric_description_id,a.entry_form
		from inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details d,product_details_master e
		where a.id=b.mst_id and b.id=d.dtls_id and b.trans_id=d.trans_id and d.prod_id=e.id and a.receive_basis=11 and a.entry_form=22 and  d.po_breakdown_id in($po_break_down_ids) $comp_cond
		and a.item_category=13 and d.entry_form=22 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
		and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0
		group by a.booking_no, d.po_breakdown_id ,b.body_part_id,e.dia_width,b.febric_description_id,a.entry_form");

		foreach($fabric_isssueToProcess_sql as $row)
		{
			//$fabric_issue_recv_arr[$row[csf("booking_no")]][$row[csf("order_id")]][$row[csf("febric_description_id")]][$row[csf("body_part_id")]][$row[csf("width")]]['batch_issue_qty']=$row[csf("batch_issue_qty")];


			/*$fabric_issue_recv_arr[$row[csf("booking_no")]][$row[csf("order_id")]][$row[csf("febric_description_id")]][$row[csf("body_part_id")]][$row[csf("width")]]['batch_recv_qty']=$row[csf("batch_recv_qty")];
			if($row[csf("entry_form")]==37)
			{
				$fabric_issue_recv_arr[$row[csf("booking_no")]][$row[csf("order_id")]][$row[csf("febric_description_id")]][$row[csf("body_part_id")]][$row[csf("width")]]['batch_recv_qty']=$row[csf("batch_recv_qty")];
			}*/

			$fabric_issue_recv_arr[$row[csf("booking_no")]][$row[csf("order_id")]][$row[csf("febric_description_id")]][$row[csf("body_part_id")]][$row[csf("width")]]['batch_recv_qty']=$row[csf("batch_recv_qty")];
			if($row[csf("entry_form")]==37)
			{
				$fabric_issue_recv_arr[$row[csf("booking_no")]][$row[csf("order_id")]][$row[csf("febric_description_id")]][$row[csf("body_part_id")]][$row[csf("width")]]['batch_recv_qty']=$row[csf("batch_recv_qty")];
			}

		}
		/*echo "<pre>";
		print_r($fabric_issue_recv_arr);
		echo "</pre>";*/

		$fabric_Desc_sql= sql_select("select a.id,b.lib_yarn_count_deter_id,c.booking_no,b.body_part_id
			from wo_pre_cost_fab_conv_cost_dtls a, wo_pre_cost_fabric_cost_dtls b, wo_booking_dtls c
			where b.job_no=c.job_no and b.status_active=1 and c.is_deleted=0 and c.po_break_down_id in($po_break_down_ids) and a.id in($pre_cost_fabric_cost_dtls_id) and a.fabric_description = b.id and c.booking_type = 3 group by a.id,b.lib_yarn_count_deter_id,c.booking_no,b.body_part_id order by c.booking_no");
		foreach( $fabric_Desc_sql as $row)
		{
			$fabric_des_id_arr[$row[csf("id")]][$row[csf("booking_no")]]=$row[csf("lib_yarn_count_deter_id")];
			$fabric_des_body_part_id_arr[$row[csf("id")]][$row[csf("booking_no")]]=$row[csf("body_part_id")];
		}
		$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select  c.job_no,c.id,c.fabric_description,c.cons_process from wo_booking_dtls b,wo_pre_cost_fab_conv_cost_dtls c  where c.id=b.pre_cost_fabric_cost_dtls_id and b.booking_no in($booking_nos) group by c.job_no,c.id,c.fabric_description,c.cons_process");
		//echo "select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where job_no='$data[0]' ";
		foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id)
		{
			if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]!=0)
			{
				$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description,lib_yarn_count_deter_id  from  wo_pre_cost_fabric_cost_dtls
				where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."'");
				list($fabric_description_row)=$fabric_description;
				$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$body_part[$fabric_description_row[csf("body_part_id")]].', '. $fabric_description_row[csf("fabric_description")];
			}
			if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]==0)
			{
				$fabric_description_string="";
				$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description,lib_yarn_count_deter_id from  wo_pre_cost_fabric_cost_dtls
				where  job_no=".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf('job_no')]." ");
				foreach( $fabric_description as $fabric_description_row)
		        {
				$fabric_description_string.=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$fabric_description_row[csf("fabric_description")]." and ";

				}
				$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]].=rtrim($fabric_description_string,"and ");
			}
		}


		foreach($new_data_service_booking as $bookingNo => $bookingData)
		{
			foreach($bookingData as $suplierID => $suplierData)
			{
				foreach($suplierData as $buyerID => $buyerData)
				{
					foreach($buyerData as $po_id => $poData)
					{
						foreach($poData as $fabrication_id => $row)
						{
							$sour_data_arr[$bookingNo][$suplierID][$buyerID][$po_id][$fabric_description_array[$fabrication_id]]['booking_no_prefix_num']=$row["booking_no_prefix_num"];
							$sour_data_arr[$bookingNo][$suplierID][$buyerID][$po_id][$fabric_description_array[$fabrication_id]]['supplier_id']=$row["supplier_id"];
							$sour_data_arr[$bookingNo][$suplierID][$buyerID][$po_id][$fabric_description_array[$fabrication_id]]['buyer_id']=$row["buyer_id"];
							$sour_data_arr[$bookingNo][$suplierID][$buyerID][$po_id][$fabric_description_array[$fabrication_id]]['po_break_down_id']=$row["po_break_down_id"];
							$sour_data_arr[$bookingNo][$suplierID][$buyerID][$po_id][$fabric_description_array[$fabrication_id]]['wo_qnty']+=$row["wo_qnty"];
							$sour_data_arr[$bookingNo][$suplierID][$buyerID][$po_id][$fabric_description_array[$fabrication_id]]['pay_mode']=$row["pay_mode"];
							$sour_data_arr[$bookingNo][$suplierID][$buyerID][$po_id][$fabric_description_array[$fabrication_id]]['dia_width']=$row["dia_width"];
							$sour_data_arr[$bookingNo][$suplierID][$buyerID][$po_id][$fabric_description_array[$fabrication_id]]['process']=$row["process"];
							$sour_data_arr[$bookingNo][$suplierID][$buyerID][$po_id][$fabric_description_array[$fabrication_id]]['bodypart_deterid']=$fabric_des_body_part_id_arr[$fabrication_id][$bookingNo] .'**'. $fabric_des_id_arr[$fabrication_id][$bookingNo];

						}
					}
				}
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
				                <!-- <th width="60">Process Loss</th> -->
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
							$colspan=1;$rowspanArr=array();

							foreach($sour_data_arr as $bookingNo => $bookingData)
							{
								foreach($bookingData as $suplierID => $suplierData)
								{
									foreach($suplierData as $buyerID => $buyerData)
									{
										foreach($buyerData as $po_id => $poData)
										{
											foreach($poData as $fabrication_id => $row)
											{
												$rowspanArr[$bookingNo][$suplierID][$buyerID][$po_id]+=$colspan;
											}
										}
									}
								}
							}

							/*echo "<pre>";
							print_r($rowspanArr);
							echo "</pre>";*/

							foreach($sour_data_arr as $bookingNo => $bookingData)
							{
								foreach($bookingData as $suplierID => $suplierData)
								{
									foreach($suplierData as $buyerID => $buyerData)
									{
										foreach($buyerData as $po_id => $poData)
										{
											$k=1;
											foreach($poData as $fabrication_id => $row)
											{
												if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
												?>
												<tr bgcolor="<? //echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">

													<?
													if($k==1)
													{
														?>

														<td rowspan="<? echo $rowspanArr[$bookingNo][$suplierID][$buyerID][$po_id]; ?>" width="40"><? echo $i; ?></td>
								                        <td rowspan="<? echo $rowspanArr[$bookingNo][$suplierID][$buyerID][$po_id]; ?>" width="80" align="center"><? echo $bookingNo; ?></td>
								                        <td rowspan="<? echo $rowspanArr[$bookingNo][$suplierID][$buyerID][$po_id]; ?>" align="center" width="120"><div style="width:120px; word-wrap:break-word;"><?
								                        if($row["pay_mode"]==3 || $row["pay_mode"]==5)
								                        	{ echo $company_library[$suplierID] ;}
								                        else if($row["pay_mode"]==1){echo $suplier[$suplierID];}
								                        else{echo $company_library[$suplierID];} ?>&nbsp;
								                        </div></td>
								                        <td rowspan="<? echo $rowspanArr[$bookingNo][$suplierID][$buyerID][$po_id]; ?>" width="120"><div style="width:90px; word-wrap:break-word;"><? echo $buyer_arr[$buyerID]; ?>&nbsp;</div></td>
								                        <td rowspan="<? echo $rowspanArr[$bookingNo][$suplierID][$buyerID][$po_id]; ?>" width="120" title="<? echo $po_id; ?>"><p><? echo $po_no_arr[$po_id]['po_number']; ?>&nbsp;</p></td>
								                        <td rowspan="<? echo $rowspanArr[$bookingNo][$suplierID][$buyerID][$po_id]; ?>" width="120"><p><? echo $po_no_arr[$po_id]['style_ref_no']; ?>&nbsp;</p></td>
													<?
								                    $i++;
								                    }
								                    ?>

							                        <td rowspan="<? //echo $rowspanArr[$bookingNo][$suplierID][$buyerID][$po_id][$fabrication_id]; ?>" width="150" align="center" title="<? echo $fabrication_id; ?>"><p><? echo $fabric_description_array[$fabrication_id]; ?>&nbsp;</p></p></td>

													<td width="60" align="right"><? echo number_format($row["wo_qnty"],2,'.',''); ?></td>
													<td width="60" align="right"><?
													//echo $bookingNo."=".$po_id."=".$fabric_des_id_arr[$fabrication_id][$bookingNo]."=".$fabric_des_body_part_id_arr[$fabrication_id][$bookingNo]."=".$dia_width."=".$dtls_id."<br/>";

													$body_deter_arr = explode("**", $row["bodypart_deterid"]);
													$body_part_id = $body_deter_arr[0];
													$determination_id = $body_deter_arr[1];

													$fab_issue=$fabric_issue_recv_arr[$bookingNo][$po_id][$determination_id][$body_part_id][$row["dia_width"]]['batch_issue_qty']; echo number_format($fab_issue,2,'.','');
													?></td>
													<td width="60" align="right">
														<?

														$fab_recv=$fabric_issue_recv_arr[$bookingNo][$po_id][$determination_id][$body_part_id][$row["dia_width"]]['batch_recv_qty'];
														?>
														<a href="##" onclick="openmypage_qnty('<? echo $bookingNo;?>','<? echo $po_id;?>','openmypage_receive','<? echo $determination_id;?>','<? echo $body_part_id;?>','<? echo $row["dia_width"]; ?>','0','0','<? echo $date_from;?>','<? echo $date_to;?>','<? echo $row["process"];?>','');"><? echo number_format($fab_recv,2,".",""); ?>

														</a>
													</td>
													<td width="60" align="right"><? $balance=($row["wo_qnty"]-$fab_recv); echo number_format($balance,2,'.',''); ?></td>
													<td></td>
												</tr>
												<?

												$tot_wo_qnty+=$row["wo_qnty"];
												$tot_fab_issue+=$fab_issue;
												$tot_fab_recv+=$fab_recv;
												$tot_processLoss+=$processLoss;
												$tot_balance+=$balance;
												$k++;
											}
										}
									}
								}

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
	 							<th align="right" width="60" id="td_fab_balance_id"><? echo number_format($tot_balance,2,'.',''); ?></th>
	 							<th>&nbsp;</th>
				            </tr>
				        </tfoot>
				    </table>
				</div>
			</fieldset>
		</div>
		<?
	}

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

if($action=="report_generated_030423")
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
	$txt_job_no = str_replace("'","",$txt_job_no);
	$hidden_style_no = str_replace("'","",$hidden_style_no);

	//var_dump($cbo_process);

	if ($buyer==0) $buyer_cond=""; else $buyer_cond="  and a.buyer_id='$buyer'";
	if ($cbo_process==0) $process_cond=""; else $process_cond="  and b.process='$cbo_process'";
	if ($cbo_process==0) $process_cond_2=""; else $process_cond_2="  and b.process_id='$cbo_process'";
	if ($cbo_process==0) $process_cond_3=""; else $process_cond_3="  and c.process='$cbo_process'";
	if ($cbo_supplier_name==0) $supplier_cond=""; else $supplier_cond="  and a.supplier_id='$cbo_supplier_name'";
	if ($booking_no=="") $booking_num=""; else $booking_num="  and a.booking_no like '%".str_replace("'","",$booking_no)."%'";
	if ($company==0) $comp_cond=""; else $comp_cond=" and a.company_id=$company";
	if ($booking_number_hidden==0) $booking_id_cond=""; else $booking_id_cond=" and a.id=$booking_number_hidden";
	if ($txt_order=="") $order_no_cond=""; else $order_no_cond="  and a.po_number='$txt_order'";
	if ($txt_job_no=="") $job_no_cond=""; else $job_no_cond="  and b.job_no_prefix_num='$txt_job_no'";
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
			$booking_date_range_cond="and a.booking_date between '$date_from' and '$date_to'";
		}
		if($db_type==2)
		{
			$date_from=change_date_format($txt_date_from,'','',1);
			$date_to=change_date_format($txt_date_to,'','',1);
			$dates_com="and a.batch_date BETWEEN '$date_from' AND '$date_to'";
			$booking_date_range_cond="and a.booking_date between '$date_from' and '$date_to'";
		}
	}

	if($order_no_cond!="" || $job_no_cond!="")
	{
		$po_no_sql_search=sql_select("SELECT a.id, a.po_number,b.style_ref_no from wo_po_break_down a,wo_po_details_master b where a.job_no_mst=b.job_no $order_no_cond $job_no_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach($po_no_sql_search as $row)
		{
			$po_id=$row[csf("id")];
		}
	}
	if ($po_id=="") $order_no_cond_search=""; else $order_no_cond_search=" and b.po_break_down_id='$po_id'";

	if($cbo_process!=1)
	{
		$sql_service_booking="SELECT a.id,b.id as dtls_id,a.booking_no_prefix_num, b.job_no as job_no_prefix_num,a.booking_no,a.booking_date,a.company_id,a.buyer_id,b.po_break_down_id,b.dia_width,b.fabric_color_id,b.gmts_color_id,a.item_category,a.fabric_source,a.supplier_id,a.pay_mode,b.pre_cost_fabric_cost_dtls_id,sum(b.wo_qnty) as wo_qnty,b.process
		from wo_booking_mst a,wo_booking_dtls b
		where a.booking_no=b.booking_no and a.booking_type=3 and a.status_active=1 and a.is_deleted=0 and a.company_id='$company' $process_cond $booking_num $booking_id_cond $order_no_cond_search $order_id_cond $buyer_cond $supplier_cond $year_cond $booking_date_range_cond and b.wo_qnty>0
		group by a.id,b.id,a.booking_no_prefix_num, b.job_no,a.booking_no,a.booking_date,a.company_id,a.buyer_id,b.po_break_down_id,b.dia_width,b.fabric_color_id,b.gmts_color_id,a.item_category,a.fabric_source,a.supplier_id,a.pay_mode,b.pre_cost_fabric_cost_dtls_id,b.process order by a.booking_no";
		echo $sql_service_booking;
		$sql_serviceBooking=sql_select($sql_service_booking);

		$booking_nos=$po_break_down_ids=$pre_cost_fabric_cost_dtls_id="";
		foreach($sql_serviceBooking as $row)
		{
			$booking_nos.="'".$row[csf("booking_no")]."',";
			$po_break_down_ids.=$row[csf("po_break_down_id")].",";
			$pre_cost_fabric_cost_dtls_id.=$row[csf("pre_cost_fabric_cost_dtls_id")].",";

			$new_data_service_booking[$row[csf("booking_no")]][$row[csf("supplier_id")]][$row[csf("buyer_id")]][$row[csf("po_break_down_id")]][$row[csf("pre_cost_fabric_cost_dtls_id")]][$row[csf("gmts_color_id")]]['booking_no_prefix_num']=$row[csf("booking_no_prefix_num")];

			$new_data_service_booking[$row[csf("booking_no")]][$row[csf("supplier_id")]][$row[csf("buyer_id")]][$row[csf("po_break_down_id")]][$row[csf("pre_cost_fabric_cost_dtls_id")]][$row[csf("gmts_color_id")]]['supplier_id']=$row[csf("supplier_id")];

			$new_data_service_booking[$row[csf("booking_no")]][$row[csf("supplier_id")]][$row[csf("buyer_id")]][$row[csf("po_break_down_id")]][$row[csf("pre_cost_fabric_cost_dtls_id")]][$row[csf("gmts_color_id")]]['buyer_id']=$row[csf("buyer_id")];

			$new_data_service_booking[$row[csf("booking_no")]][$row[csf("supplier_id")]][$row[csf("buyer_id")]][$row[csf("po_break_down_id")]][$row[csf("pre_cost_fabric_cost_dtls_id")]][$row[csf("gmts_color_id")]]['po_break_down_id']=$row[csf("po_break_down_id")];

			$new_data_service_booking[$row[csf("booking_no")]][$row[csf("supplier_id")]][$row[csf("buyer_id")]][$row[csf("po_break_down_id")]][$row[csf("pre_cost_fabric_cost_dtls_id")]][$row[csf("gmts_color_id")]]['pre_cost_fabric_cost_dtls_id']=$row[csf("pre_cost_fabric_cost_dtls_id")];

			$new_data_service_booking[$row[csf("booking_no")]][$row[csf("supplier_id")]][$row[csf("buyer_id")]][$row[csf("po_break_down_id")]][$row[csf("pre_cost_fabric_cost_dtls_id")]][$row[csf("gmts_color_id")]]['gmts_color_id']=$row[csf("gmts_color_id")];

			$new_data_service_booking[$row[csf("booking_no")]][$row[csf("supplier_id")]][$row[csf("buyer_id")]][$row[csf("po_break_down_id")]][$row[csf("pre_cost_fabric_cost_dtls_id")]][$row[csf("gmts_color_id")]]['wo_qnty']=$row[csf("wo_qnty")];


			$new_data_service_booking[$row[csf("booking_no")]][$row[csf("supplier_id")]][$row[csf("buyer_id")]][$row[csf("po_break_down_id")]][$row[csf("pre_cost_fabric_cost_dtls_id")]][$row[csf("gmts_color_id")]]['pay_mode']=$row[csf("pay_mode")];
			$new_data_service_booking[$row[csf("booking_no")]][$row[csf("supplier_id")]][$row[csf("buyer_id")]][$row[csf("po_break_down_id")]][$row[csf("pre_cost_fabric_cost_dtls_id")]][$row[csf("gmts_color_id")]]['dia_width']=$row[csf("dia_width")];

			$new_data_service_booking[$row[csf("booking_no")]][$row[csf("supplier_id")]][$row[csf("buyer_id")]][$row[csf("po_break_down_id")]][$row[csf("pre_cost_fabric_cost_dtls_id")]][$row[csf("gmts_color_id")]]['dtls_id']=$row[csf("dtls_id")];
			$new_data_service_booking[$row[csf("booking_no")]][$row[csf("supplier_id")]][$row[csf("buyer_id")]][$row[csf("po_break_down_id")]][$row[csf("pre_cost_fabric_cost_dtls_id")]][$row[csf("gmts_color_id")]]['process']=$row[csf("process")];
		}
		/*echo "<pre>";
		print_r($new_data_service_booking);
		echo "</pre>";
		die;*/
		$booking_nos=chop($booking_nos,",");
		$po_break_down_ids=chop($po_break_down_ids,",");
		$pre_cost_fabric_cost_dtls_id=chop($pre_cost_fabric_cost_dtls_id,",");
		//$po_no_arr = return_library_array( "select id, po_number from wo_po_break_down where id in($po_break_down_ids) and status_active=1 and is_deleted=0",'id','po_number');

	 	$po_no_sql=sql_select("select a.id, a.po_number,b.style_ref_no from wo_po_break_down a,wo_po_details_master b where a.id in($po_break_down_ids) and a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach($po_no_sql as $row)
		{
			$po_no_arr[$row[csf("id")]]['po_number']=$row[csf("po_number")];
			$po_no_arr[$row[csf("id")]]['style_ref_no']=$row[csf("style_ref_no")];
		}

		$fabric_isssueToProcess_sql = "SELECT b.booking_no,b.booking_dtls_id, b.order_id,b.body_part_id,b.color_id,b.width as width,
		sum(case when a.entry_form=91 then b.batch_issue_qty else 0 end) as batch_issue_qty,
		sum(case when a.entry_form=92 then b.batch_issue_qty else 0 end) as batch_recv_qty,
		0 as grey_used_qty, b.febric_description_id,a.entry_form
		from inv_receive_mas_batchroll a, pro_grey_batch_dtls b where a.id=b.mst_id and a.entry_form in(91,92) and  b.order_id in($po_break_down_ids) $comp_cond $process_cond_2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by  b.booking_no,b.booking_dtls_id, b.order_id,b.febric_description_id,b.body_part_id ,b.color_id,b.width,a.entry_form
		union all
		select TO_CHAR(a.booking_no) as booking_no, c.id as booking_dtls_id, d.po_breakdown_id as order_id ,b.body_part_id as body_part_id ,TO_CHAR(c.gmts_color_id) as color_id,TO_CHAR(e.dia_width) as width, 0 as batch_issue_qty,
		sum(case when a.entry_form=22 then d.quantity else 0 end) as batch_recv_qty, 0 as grey_used_qty,
		b.febric_description_id as febric_description_id,a.entry_form
		from inv_receive_master a, pro_grey_prod_entry_dtls b,wo_booking_dtls c,order_wise_pro_details d,product_details_master e
		where a.id=b.mst_id and a.booking_no=c.booking_no and b.id=d.dtls_id and b.trans_id=d.trans_id and d.prod_id=e.id and a.receive_basis=11 and a.entry_form=22 and  d.po_breakdown_id in($po_break_down_ids) $comp_cond $process_cond_3
		and a.item_category=13 and d.entry_form=22 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
		and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0
		group by a.booking_no, c.id, d.po_breakdown_id ,b.body_part_id ,c.gmts_color_id,e.dia_width,b.febric_description_id,a.entry_form
		union all
		select  TO_CHAR(a.booking_no) as booking_no,0 as booking_dtls_id, d.po_breakdown_id as order_id ,b.body_part_id as body_part_id ,
		TO_CHAR(b.color_id) as color_id,TO_CHAR(e.dia_width) as width, 0 as batch_issue_qty,
		sum(case when a.entry_form=37 then d.quantity else 0 end) as batch_recv_qty,
		sum(case when a.entry_form=37 then d.grey_used_qty else 0 end) as grey_used_qty,
		b.fabric_description_id as febric_description_id,a.entry_form
		from inv_receive_master a, pro_finish_fabric_rcv_dtls b,order_wise_pro_details d,product_details_master e
		where a.id=b.mst_id and b.id=d.dtls_id and b.trans_id=d.trans_id and d.prod_id=e.id and a.entry_form=37 and a.item_category=2 and  a.receive_basis=11 and
		d.po_breakdown_id in($po_break_down_ids) $comp_cond  and d.entry_form=37
		and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0
		group by a.booking_no, d.po_breakdown_id ,b.body_part_id ,b.color_id,e.dia_width,b.fabric_description_id,a.entry_form
		union all
		select a.wo_no as booking_no,b.booking_dtls_id, b.order_id,b.body_part_id,b.color_id,b.width as width,
		sum(case when a.entry_form=63 then b.ROLL_WGT else 0 end) as batch_issue_qty,
		sum(case when a.entry_form=65 then b.ROLL_WGT else 0 end) as batch_recv_qty,
		0 as grey_used_qty,
		c.detarmination_id as febric_description_id,a.entry_form
		from inv_receive_mas_batchroll a, pro_grey_batch_dtls b , product_details_master c
		where a.id=b.mst_id and a.entry_form in(63,65) and b.order_id in($po_break_down_ids) $comp_cond $process_cond_2
		and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.prod_id= c.id
		group by a.wo_no,b.booking_dtls_id, b.order_id,b.body_part_id ,b.color_id,b.width,a.entry_form , c.detarmination_id";
		//echo $fabric_isssueToProcess_sql;

		$fabric_isssueToProcess_sql_data =sql_select($fabric_isssueToProcess_sql);
		foreach($fabric_isssueToProcess_sql_data as $row)
		{
			$fabric_issue_recv_arr[$row[csf("booking_no")]][$row[csf("order_id")]][$row[csf("febric_description_id")]][$row[csf("body_part_id")]][$row[csf("width")]][$row[csf("color_id")]][$row[csf("booking_dtls_id")]]['batch_issue_qty']+=$row[csf("batch_issue_qty")];

			$fabric_issue_recv_arr[$row[csf("booking_no")]][$row[csf("order_id")]][$row[csf("febric_description_id")]][$row[csf("body_part_id")]][$row[csf("width")]][$row[csf("color_id")]][$row[csf("booking_dtls_id")]]['batch_recv_qty']+=$row[csf("batch_recv_qty")];

			if($row[csf("entry_form")]==37)
			{
				$fabric_issue_recv_arr[$row[csf("booking_no")]][$row[csf("order_id")]][$row[csf("febric_description_id")]][$row[csf("body_part_id")]][$row[csf("width")]][$row[csf("color_id")]]['batch_recv_qty']+=$row[csf("batch_recv_qty")];

				$grey_recv_arr[$row[csf("booking_no")]][$row[csf("order_id")]][$row[csf("febric_description_id")]][$row[csf("body_part_id")]][$row[csf("width")]][$row[csf("color_id")]]['grey_used_qty']+=$row[csf("grey_used_qty")];;

			}

			if($row[csf("entry_form")]==63 || $row[csf("entry_form")]==65)
			{
				$fabric_issue_recv_arr[$row[csf("booking_no")]][$row[csf("order_id")]][$row[csf("febric_description_id")]][$row[csf("body_part_id")]][$row[csf("width")]][$row[csf("color_id")]]['batch_recv_qty']=$row[csf("batch_recv_qty")];
				$fabric_issue_recv_arr[$row[csf("booking_no")]][$row[csf("order_id")]][$row[csf("febric_description_id")]][$row[csf("body_part_id")]][$row[csf("width")]][$row[csf("color_id")]]['batch_issue_qty']+=$row[csf("batch_issue_qty")];
			}
			//echo $row[csf("booking_no")].'='.$row[csf("order_id")].'='.$row[csf("febric_description_id")].'='.$row[csf("body_part_id")].'='.$row[csf("width")].'='.$row[csf("color_id")].'='.$row[csf("booking_dtls_id")].'*<br>';

		}
		/* echo "<pre>";
		print_r($grey_recv_arr);
		echo "</pre>"; */



		$fabric_Desc_sql= sql_select("SELECT a.id,b.lib_yarn_count_deter_id,c.booking_no,b.body_part_id
		from wo_pre_cost_fab_conv_cost_dtls a, wo_pre_cost_fabric_cost_dtls b, wo_booking_dtls c
		where b.job_no=c.job_no and b.status_active=1 and c.is_deleted=0 and c.po_break_down_id in($po_break_down_ids) and a.id in($pre_cost_fabric_cost_dtls_id) and a.fabric_description = b.id and c.booking_type = 3 group by a.id,b.lib_yarn_count_deter_id,c.booking_no,b.body_part_id order by c.booking_no");
		foreach( $fabric_Desc_sql as $row)
		{
			$fabric_des_id_arr[$row[csf("id")]][$row[csf("booking_no")]]=$row[csf("lib_yarn_count_deter_id")];
			$fabric_des_body_part_id_arr[$row[csf("id")]][$row[csf("booking_no")]]=$row[csf("body_part_id")];
		}
		$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select  c.job_no,c.id,c.fabric_description,c.cons_process from wo_booking_dtls b,wo_pre_cost_fab_conv_cost_dtls c  where c.id=b.pre_cost_fabric_cost_dtls_id and b.booking_no in($booking_nos) group by c.job_no,c.id,c.fabric_description,c.cons_process");
		//echo "select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where job_no='$data[0]' ";
		foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id)
		{
			if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]!=0)
			{
				$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description,lib_yarn_count_deter_id  from  wo_pre_cost_fabric_cost_dtls
				where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."'");
				list($fabric_description_row)=$fabric_description;
				$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$body_part[$fabric_description_row[csf("body_part_id")]].',
				'.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")];
			}
			if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]==0)
			{
				$fabric_description_string="";
				$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description,lib_yarn_count_deter_id from  wo_pre_cost_fabric_cost_dtls
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
					<table width="1360" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
						<thead>
				        	<tr>
				                <th width="40">SL</th>
				                <th width="80">WO No</th>
				                <th width="120">Party Name</th>
				                <th width="120">Buyer</th>
				                <th width="120">Order</th>
				                <th width="120">Style</th>
				                <th width="150">Item Description</th>
				                <th width="150">Fabric Color</th>
				                <th width="60">WO Qty</th>
				                <th width="60">Fabric Issue</th>
				                <th width="60">Fabric Received</th>
				                <th width="60">Grey Qnty <br>Received</th>
				                <th width="60">Balance</th>
				                <th width="60">Grey Balance</th>
				                <th>Remarks</th>
				            </tr>
						</thead>
					</table>
					<div style="width:1380px; overflow-y: scroll; max-height:380px;" id="scroll_body">
						<table width="1360" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body">
						<?
							$tot_fab_issue=0; $tot_fab_recv=0; $tot_processLoss=0; $tot_balance=0; $tot_wo_qnty=0;$tot_grey_recv=0;$tot_grey_balance=0;
							$construction_data_arr=array();
							$colspan=1;$rowspanArr=array();
							foreach($new_data_service_booking as $bookingNo => $bookingData)
							{
								foreach($bookingData as $suplierID => $suplierData)
								{
									foreach($suplierData as $buyerID => $buyerData)
									{
										foreach($buyerData as $po_id => $poData)
										{
											foreach($poData as $fabrication_id => $fabricDAta)
											{
												foreach($fabricDAta as $gmtsColorId => $row)
												{
													//$rowspanArr[$customerID][$dates][$purpose]+=1;
													$rowspanArr[$bookingNo][$suplierID][$buyerID][$po_id][$fabrication_id]+=$colspan;
												}
											}
										}
									}
								}
							}
							/*echo "<pre>";
							print_r($rowspanArr);
							echo "</pre>";*/

							//$new_data_service_booking[$row[csf("booking_no")]][$row[csf("supplier_id")]][$row[csf("buyer_id")]][$row[csf("po_break_down_id")]][$row[csf("pre_cost_fabric_cost_dtls_id")]][$row[csf("gmts_color_id")]]['booking_no_prefix_num']
							$sl=1;
							$i=1;
							foreach($new_data_service_booking as $bookingNo => $bookingData)
							{
								foreach($bookingData as $suplierID => $suplierData)
								{
									foreach($suplierData as $buyerID => $buyerData)
									{
										foreach($buyerData as $po_id => $poData)
										{
											foreach($poData as $fabrication_id => $fabricDAta)
											{
												$k=1;
												foreach($fabricDAta as $gmtsColorId => $row)
												{
													/*foreach($sql_serviceBooking as $row)
													{*/

													//echo $bookingNo.'='.$po_id.'='.$fabric_des_id_arr[$fabrication_id][$bookingNo].'='.$fabric_des_body_part_id_arr[$fabrication_id][$bookingNo].'='.$row["dia_width"].'='.$gmtsColorId.'='.$row["dtls_id"]."#<br>";

													if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
													?>

													<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $sl;?>','<? echo $bgcolor;?>')" id="tr<? echo $sl;?>">

														<?
														if($k==1)
														{
															?>

															<td rowspan="<? echo $rowspanArr[$bookingNo][$suplierID][$buyerID][$po_id][$fabrication_id]; ?>" width="40"><? echo $i; ?></td>
									                        <td rowspan="<? echo $rowspanArr[$bookingNo][$suplierID][$buyerID][$po_id][$fabrication_id]; ?>" width="80" align="center"><? echo $bookingNo; ?></td>
									                        <td rowspan="<? echo $rowspanArr[$bookingNo][$suplierID][$buyerID][$po_id][$fabrication_id]; ?>" align="center" width="120"><div style="width:120px; word-wrap:break-word;"><?
									                        if($row["pay_mode"]==3 || $row["pay_mode"]==5)
									                        	{ echo $company_library[$suplierID] ;}
									                        else if($row["pay_mode"]==1){echo $suplier[$suplierID];}
									                        else{echo $suplier[$suplierID];} ?>&nbsp;
									                        </div></td>
									                        <td rowspan="<? echo $rowspanArr[$bookingNo][$suplierID][$buyerID][$po_id][$fabrication_id]; ?>" width="120"><div style="width:90px; word-wrap:break-word;"><? echo $buyer_arr[$buyerID]; ?>&nbsp;</div></td>
									                        <td rowspan="<? echo $rowspanArr[$bookingNo][$suplierID][$buyerID][$po_id][$fabrication_id]; ?>" width="120" title="<? echo $po_id; ?>"><p><? echo $po_no_arr[$po_id]['po_number']; ?>&nbsp;</p></td>
									                        <td rowspan="<? echo $rowspanArr[$bookingNo][$suplierID][$buyerID][$po_id][$fabrication_id]; ?>" width="120"><p><? echo $po_no_arr[$po_id]['style_ref_no']; ?>&nbsp;</p></td>
									                        <td rowspan="<? echo $rowspanArr[$bookingNo][$suplierID][$buyerID][$po_id][$fabrication_id]; ?>" width="150" title="<? echo $fabrication_id; ?>"><p><? echo $fabric_description_array[$fabrication_id]; ?>&nbsp;</p></td>
															<?
															$sl++;
									                     $i++;
									                    }
									                    ?>

								                        <td rowspan="<? //echo $rowspanArr[$bookingNo][$suplierID][$buyerID][$po_id][$fabrication_id]; ?>" width="150" align="center" title="<? echo $gmtsColorId; ?>"><p><? echo $color_library[$gmtsColorId]; ?>&nbsp;</p></td>

														<td width="60" align="right"><? echo number_format($row["wo_qnty"],2,'.',''); ?></td>

														<td width="60" align="right">
															<?
															//echo $bookingNo."=".$po_id."=".$fabric_des_id_arr[$fabrication_id][$bookingNo]."=".$fabric_des_body_part_id_arr[$fabrication_id][$bookingNo]."=".$row['dia_width']."=".$gmtsColorId."=".$dtls_id."<br/>";

															$fab_issue=$fabric_issue_recv_arr[$bookingNo][$po_id][$fabric_des_id_arr[$fabrication_id][$bookingNo]][$fabric_des_body_part_id_arr[$fabrication_id][$bookingNo]][$row["dia_width"]][$gmtsColorId][$row["dtls_id"]]['batch_issue_qty']    +  $fabric_issue_recv_arr[$bookingNo][$po_id][$fabric_des_id_arr[$fabrication_id][$bookingNo]][$fabric_des_body_part_id_arr[$fabrication_id][$bookingNo]][$row["dia_width"]][$gmtsColorId]['batch_issue_qty'];
															//91 + 63
															echo number_format($fab_issue,2,'.','');
															?>
														</td>
														<td width="60" align="right">
															<?
															if ($row["process"]==31)
															{
																$fab_recv=$fabric_issue_recv_arr[$bookingNo][$po_id][$fabric_des_id_arr[$fabrication_id][$bookingNo]][$fabric_des_body_part_id_arr[$fabrication_id][$bookingNo]][$row["dia_width"]][$gmtsColorId]['batch_recv_qty'];

																$grey_used_qnty = $grey_recv_arr[$bookingNo][$po_id][$fabric_des_id_arr[$fabrication_id][$bookingNo]][$fabric_des_body_part_id_arr[$fabrication_id][$bookingNo]][$row["dia_width"]][$gmtsColorId]['grey_used_qty'];
																//$grey_used_qnty = 1;
																//echo $grey_used_qnty;
															}
															else
															{
																$fab_recv=$fabric_issue_recv_arr[$bookingNo][$po_id][$fabric_des_id_arr[$fabrication_id][$bookingNo]][$fabric_des_body_part_id_arr[$fabrication_id][$bookingNo]][$row["dia_width"]][$gmtsColorId][$row["dtls_id"]]['batch_recv_qty']   +   $fabric_issue_recv_arr[$bookingNo][$po_id][$fabric_des_id_arr[$fabrication_id][$bookingNo]][$fabric_des_body_part_id_arr[$fabrication_id][$bookingNo]][$row["dia_width"]][$gmtsColorId]['batch_recv_qty'];
																//entry_form 92  +  65
															}

															//echo number_format($fab_recv,2,'.','');

															//echo $bookingNo."=".$po_id."=".$fabric_des_id_arr[$fabrication_id][$bookingNo]."=".$fabric_des_body_part_id_arr[$fabrication_id][$bookingNo]."=".$row["dia_width"]."=".$gmtsColorId."=".$row["dtls_id"];
															?>
															<a href="##" onclick="openmypage_qnty('<? echo $bookingNo;?>','<? echo $po_id;?>','openmypage_receive','<? echo $fabric_des_id_arr[$fabrication_id][$bookingNo];?>','<? echo $fabric_des_body_part_id_arr[$fabrication_id][$bookingNo];?>','<? echo $row["dia_width"]; ?>','<? echo $gmtsColorId;?>','<? echo $row["dtls_id"];?>','<? echo $date_from;?>','<? echo $date_to;?>','<? echo $row["process"];?>');"><? echo number_format($fab_recv,2,".",""); ?>

															</a>
														</td>

														<td width="60" align="right">
														<a href="##" onclick="openmypage_qnty('<? echo $bookingNo;?>','<? echo $po_id;?>','openmypage_grey_receive','<? echo $fabric_des_id_arr[$fabrication_id][$bookingNo];?>','<? echo $fabric_des_body_part_id_arr[$fabrication_id][$bookingNo];?>','<? echo $row["dia_width"]; ?>','<? echo $gmtsColorId;?>','<? echo $row["dtls_id"];?>','<? echo $date_from;?>','<? echo $date_to;?>','<? echo $row["process"];?>');"><? echo number_format($grey_used_qnty,2,".",""); ?>
														</a>
															<? //echo number_format($grey_used_qnty,2,'.',''); ?>
														</td>
														<td width="60" align="right"><? $balance=($row["wo_qnty"]-$fab_recv); echo number_format($balance,2,'.',''); ?></td>
														<td width="60" align="right"><? $grey_balance=($row["wo_qnty"]-$grey_used_qnty); echo number_format($grey_balance,2,'.',''); ?></td>
														<td></td>
													</tr>
													<?

													$tot_wo_qnty+=$row["wo_qnty"];
													$tot_fab_issue+=$fab_issue;
													$tot_fab_recv+=$fab_recv;
													$tot_grey_recv+=$grey_used_qnty;
													$tot_processLoss+=$processLoss;
													$tot_balance+=$balance;
													$tot_grey_balance+=$grey_balance;
													$k++;$sl++;
												}
											}
										}
									}
								}

							}
							?>
						</table>
					</div>
					<table width="1360" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
				        <tfoot>
				            <tr>
				            	<th width="40">&nbsp;</th>
				                <th width="80">&nbsp;</th>
				                <th width="120">&nbsp;</th>
				                <th width="120">&nbsp;</th>
				                <th width="120">&nbsp;</th>
				                <th width="120">&nbsp;</th>
				                <th width="150">&nbsp;</th>
				                <th align="right" width="150">Total</th>
				                <th align="right" width="60" id="td_fab_woqnty_id"><? echo number_format($tot_wo_qnty,2,'.',''); ?></th>
				                <th align="right" width="60" id="td_fab_issue_id"><? echo number_format($tot_fab_issue,2,'.',''); ?></th>
	 							<th align="right" width="60" id="td_fab_recv_id"><? echo number_format($tot_fab_recv,2,'.',''); ?></th>
	 							<th align="right" width="60" id="td_fab_processloss_id"><? echo number_format($tot_grey_recv,2,'.',''); ?></th>
								<th align="right" width="60" id="td_fab_balance_id"><? echo number_format($tot_balance,2,'.',''); ?></th>
								<th align="right" width="60" id="td_fab_balance_id"><? echo number_format($tot_grey_balance,2,'.',''); ?></th>
	 							<th>&nbsp;</th>
				            </tr>
				        </tfoot>
				    </table>
				</div>
			</fieldset>
		</div>
		<?
	}
	else //only for KNITTING process
	{
		$sql_service_booking="select a.id,a.booking_no_prefix_num, b.job_no as job_no_prefix_num,a.booking_no,a.booking_date,a.company_id,a.buyer_id,b.po_break_down_id,b.dia_width,a.item_category,a.fabric_source,a.supplier_id,a.pay_mode,b.pre_cost_fabric_cost_dtls_id,sum(b.wo_qnty) as wo_qnty,b.process from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=3 and a.status_active=1 and a.is_deleted=0 and a.company_id='$company' $process_cond $booking_num $order_no_cond_search $order_id_cond $buyer_cond $supplier_cond $year_cond $booking_date_range_cond and b.wo_qnty>0 group by a.id,a.booking_no_prefix_num, b.job_no,a.booking_no,a.booking_date,a.company_id,a.buyer_id,b.po_break_down_id,b.dia_width,a.item_category,a.fabric_source,a.supplier_id,a.pay_mode,b.pre_cost_fabric_cost_dtls_id,b.process order by a.booking_no";
		$sql_serviceBooking=sql_select($sql_service_booking);

		$booking_nos=$po_break_down_ids=$pre_cost_fabric_cost_dtls_id="";
		foreach($sql_serviceBooking as $row)
		{
			$booking_nos.="'".$row[csf("booking_no")]."',";
			$po_break_down_ids.=$row[csf("po_break_down_id")].",";
			$pre_cost_fabric_cost_dtls_id.=$row[csf("pre_cost_fabric_cost_dtls_id")].",";

			$new_data_service_booking[$row[csf("booking_no")]][$row[csf("supplier_id")]][$row[csf("buyer_id")]][$row[csf("po_break_down_id")]][$row[csf("pre_cost_fabric_cost_dtls_id")]]['booking_no_prefix_num']=$row[csf("booking_no_prefix_num")];

			$new_data_service_booking[$row[csf("booking_no")]][$row[csf("supplier_id")]][$row[csf("buyer_id")]][$row[csf("po_break_down_id")]][$row[csf("pre_cost_fabric_cost_dtls_id")]]['supplier_id']=$row[csf("supplier_id")];

			$new_data_service_booking[$row[csf("booking_no")]][$row[csf("supplier_id")]][$row[csf("buyer_id")]][$row[csf("po_break_down_id")]][$row[csf("pre_cost_fabric_cost_dtls_id")]]['buyer_id']=$row[csf("buyer_id")];

			$new_data_service_booking[$row[csf("booking_no")]][$row[csf("supplier_id")]][$row[csf("buyer_id")]][$row[csf("po_break_down_id")]][$row[csf("pre_cost_fabric_cost_dtls_id")]]['po_break_down_id']=$row[csf("po_break_down_id")];

			$new_data_service_booking[$row[csf("booking_no")]][$row[csf("supplier_id")]][$row[csf("buyer_id")]][$row[csf("po_break_down_id")]][$row[csf("pre_cost_fabric_cost_dtls_id")]]['pre_cost_fabric_cost_dtls_id']=$row[csf("pre_cost_fabric_cost_dtls_id")];


			$new_data_service_booking[$row[csf("booking_no")]][$row[csf("supplier_id")]][$row[csf("buyer_id")]][$row[csf("po_break_down_id")]][$row[csf("pre_cost_fabric_cost_dtls_id")]]['wo_qnty']=$row[csf("wo_qnty")];


			$new_data_service_booking[$row[csf("booking_no")]][$row[csf("supplier_id")]][$row[csf("buyer_id")]][$row[csf("po_break_down_id")]][$row[csf("pre_cost_fabric_cost_dtls_id")]]['pay_mode']=$row[csf("pay_mode")];
			$new_data_service_booking[$row[csf("booking_no")]][$row[csf("supplier_id")]][$row[csf("buyer_id")]][$row[csf("po_break_down_id")]][$row[csf("pre_cost_fabric_cost_dtls_id")]]['dia_width']=$row[csf("dia_width")];

			$new_data_service_booking[$row[csf("booking_no")]][$row[csf("supplier_id")]][$row[csf("buyer_id")]][$row[csf("po_break_down_id")]][$row[csf("pre_cost_fabric_cost_dtls_id")]]['process']=$row[csf("process")];
		}
		/*echo "<pre>";
		print_r($new_data_service_booking);
		echo "</pre>";
		die;*/
		$booking_nos=chop($booking_nos,",");
		$po_break_down_ids=chop($po_break_down_ids,",");
		$pre_cost_fabric_cost_dtls_id=chop($pre_cost_fabric_cost_dtls_id,",");
		//$po_no_arr = return_library_array( "select id, po_number from wo_po_break_down where id in($po_break_down_ids) and status_active=1 and is_deleted=0",'id','po_number');

	 	$po_no_sql=sql_select("select a.id, a.po_number,b.style_ref_no from wo_po_break_down a,wo_po_details_master b where a.id in($po_break_down_ids) and a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach($po_no_sql as $row)
		{
			$po_no_arr[$row[csf("id")]]['po_number']=$row[csf("po_number")];
			$po_no_arr[$row[csf("id")]]['style_ref_no']=$row[csf("style_ref_no")];
		}


		/*$fabric_isssueToProcess_sql =sql_select("select TO_CHAR(a.booking_no) as booking_no, d.po_breakdown_id as order_id ,b.body_part_id as body_part_id,TO_CHAR(e.dia_width) as width, 0 as batch_issue_qty,
		sum(case when a.entry_form=22 then d.quantity else 0 end) as batch_recv_qty,b.febric_description_id as febric_description_id,a.entry_form
		from inv_receive_master a, pro_grey_prod_entry_dtls b,wo_booking_dtls c,order_wise_pro_details d,product_details_master e
		where a.id=b.mst_id and a.booking_no=c.booking_no and b.id=d.dtls_id and b.trans_id=d.trans_id and d.prod_id=e.id and a.receive_basis=11 and a.entry_form=22 and  d.po_breakdown_id in($po_break_down_ids) $comp_cond $process_cond_3
		and a.item_category=13 and d.entry_form=22 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
		and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0
		group by a.booking_no, d.po_breakdown_id ,b.body_part_id,e.dia_width,b.febric_description_id,a.entry_form");*/

		$fabric_isssueToProcess_sql =sql_select("select TO_CHAR(a.booking_no) as booking_no, d.po_breakdown_id as order_id ,b.body_part_id as body_part_id,TO_CHAR(e.dia_width) as width, 0 as batch_issue_qty,
		sum(case when a.entry_form=22 then d.quantity else 0 end) as batch_recv_qty,b.febric_description_id as febric_description_id,a.entry_form
		from inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details d,product_details_master e
		where a.id=b.mst_id and b.id=d.dtls_id and b.trans_id=d.trans_id and d.prod_id=e.id and a.receive_basis=11 and a.entry_form=22 and  d.po_breakdown_id in($po_break_down_ids) $comp_cond
		and a.item_category=13 and d.entry_form=22 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
		and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0
		group by a.booking_no, d.po_breakdown_id ,b.body_part_id,e.dia_width,b.febric_description_id,a.entry_form");

		foreach($fabric_isssueToProcess_sql as $row)
		{
			//$fabric_issue_recv_arr[$row[csf("booking_no")]][$row[csf("order_id")]][$row[csf("febric_description_id")]][$row[csf("body_part_id")]][$row[csf("width")]]['batch_issue_qty']=$row[csf("batch_issue_qty")];


			/*$fabric_issue_recv_arr[$row[csf("booking_no")]][$row[csf("order_id")]][$row[csf("febric_description_id")]][$row[csf("body_part_id")]][$row[csf("width")]]['batch_recv_qty']=$row[csf("batch_recv_qty")];
			if($row[csf("entry_form")]==37)
			{
				$fabric_issue_recv_arr[$row[csf("booking_no")]][$row[csf("order_id")]][$row[csf("febric_description_id")]][$row[csf("body_part_id")]][$row[csf("width")]]['batch_recv_qty']=$row[csf("batch_recv_qty")];
			}*/

			$fabric_issue_recv_arr[$row[csf("booking_no")]][$row[csf("order_id")]][$row[csf("febric_description_id")]][$row[csf("body_part_id")]][$row[csf("width")]]['batch_recv_qty']=$row[csf("batch_recv_qty")];
			if($row[csf("entry_form")]==37)
			{
				$fabric_issue_recv_arr[$row[csf("booking_no")]][$row[csf("order_id")]][$row[csf("febric_description_id")]][$row[csf("body_part_id")]][$row[csf("width")]]['batch_recv_qty']=$row[csf("batch_recv_qty")];
			}

		}
		/*echo "<pre>";
		print_r($fabric_issue_recv_arr);
		echo "</pre>";*/

		$fabric_Desc_sql= sql_select("select a.id,b.lib_yarn_count_deter_id,c.booking_no,b.body_part_id
			from wo_pre_cost_fab_conv_cost_dtls a, wo_pre_cost_fabric_cost_dtls b, wo_booking_dtls c
			where b.job_no=c.job_no and b.status_active=1 and c.is_deleted=0 and c.po_break_down_id in($po_break_down_ids) and a.id in($pre_cost_fabric_cost_dtls_id) and a.fabric_description = b.id and c.booking_type = 3 group by a.id,b.lib_yarn_count_deter_id,c.booking_no,b.body_part_id order by c.booking_no");
		foreach( $fabric_Desc_sql as $row)
		{
			$fabric_des_id_arr[$row[csf("id")]][$row[csf("booking_no")]]=$row[csf("lib_yarn_count_deter_id")];
			$fabric_des_body_part_id_arr[$row[csf("id")]][$row[csf("booking_no")]]=$row[csf("body_part_id")];
		}
		$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select  c.job_no,c.id,c.fabric_description,c.cons_process from wo_booking_dtls b,wo_pre_cost_fab_conv_cost_dtls c  where c.id=b.pre_cost_fabric_cost_dtls_id and b.booking_no in($booking_nos) group by c.job_no,c.id,c.fabric_description,c.cons_process");
		//echo "select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where job_no='$data[0]' ";
		foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id)
		{
			if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]!=0)
			{
				$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description,lib_yarn_count_deter_id  from  wo_pre_cost_fabric_cost_dtls
				where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."'");
				list($fabric_description_row)=$fabric_description;
				$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$body_part[$fabric_description_row[csf("body_part_id")]].', '. $fabric_description_row[csf("fabric_description")];
			}
			if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]==0)
			{
				$fabric_description_string="";
				$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description,lib_yarn_count_deter_id from  wo_pre_cost_fabric_cost_dtls
				where  job_no=".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf('job_no')]." ");
				foreach( $fabric_description as $fabric_description_row)
		        {
				$fabric_description_string.=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$fabric_description_row[csf("fabric_description")]." and ";

				}
				$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]].=rtrim($fabric_description_string,"and ");
			}
		}


		foreach($new_data_service_booking as $bookingNo => $bookingData)
		{
			foreach($bookingData as $suplierID => $suplierData)
			{
				foreach($suplierData as $buyerID => $buyerData)
				{
					foreach($buyerData as $po_id => $poData)
					{
						foreach($poData as $fabrication_id => $row)
						{
							$sour_data_arr[$bookingNo][$suplierID][$buyerID][$po_id][$fabric_description_array[$fabrication_id]]['booking_no_prefix_num']=$row["booking_no_prefix_num"];
							$sour_data_arr[$bookingNo][$suplierID][$buyerID][$po_id][$fabric_description_array[$fabrication_id]]['supplier_id']=$row["supplier_id"];
							$sour_data_arr[$bookingNo][$suplierID][$buyerID][$po_id][$fabric_description_array[$fabrication_id]]['buyer_id']=$row["buyer_id"];
							$sour_data_arr[$bookingNo][$suplierID][$buyerID][$po_id][$fabric_description_array[$fabrication_id]]['po_break_down_id']=$row["po_break_down_id"];
							$sour_data_arr[$bookingNo][$suplierID][$buyerID][$po_id][$fabric_description_array[$fabrication_id]]['wo_qnty']+=$row["wo_qnty"];
							$sour_data_arr[$bookingNo][$suplierID][$buyerID][$po_id][$fabric_description_array[$fabrication_id]]['pay_mode']=$row["pay_mode"];
							$sour_data_arr[$bookingNo][$suplierID][$buyerID][$po_id][$fabric_description_array[$fabrication_id]]['dia_width']=$row["dia_width"];
							$sour_data_arr[$bookingNo][$suplierID][$buyerID][$po_id][$fabric_description_array[$fabrication_id]]['process']=$row["process"];
							$sour_data_arr[$bookingNo][$suplierID][$buyerID][$po_id][$fabric_description_array[$fabrication_id]]['bodypart_deterid']=$fabric_des_body_part_id_arr[$fabrication_id][$bookingNo] .'**'. $fabric_des_id_arr[$fabrication_id][$bookingNo];

						}
					}
				}
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
				                <!-- <th width="60">Process Loss</th> -->
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
							$colspan=1;$rowspanArr=array();

							foreach($sour_data_arr as $bookingNo => $bookingData)
							{
								foreach($bookingData as $suplierID => $suplierData)
								{
									foreach($suplierData as $buyerID => $buyerData)
									{
										foreach($buyerData as $po_id => $poData)
										{
											foreach($poData as $fabrication_id => $row)
											{
												$rowspanArr[$bookingNo][$suplierID][$buyerID][$po_id]+=$colspan;
											}
										}
									}
								}
							}

							/*echo "<pre>";
							print_r($rowspanArr);
							echo "</pre>";*/

							foreach($sour_data_arr as $bookingNo => $bookingData)
							{
								foreach($bookingData as $suplierID => $suplierData)
								{
									foreach($suplierData as $buyerID => $buyerData)
									{
										foreach($buyerData as $po_id => $poData)
										{
											$k=1;
											foreach($poData as $fabrication_id => $row)
											{
												if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
												?>
												<tr bgcolor="<? //echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">

													<?
													if($k==1)
													{
														?>

														<td rowspan="<? echo $rowspanArr[$bookingNo][$suplierID][$buyerID][$po_id]; ?>" width="40"><? echo $i; ?></td>
								                        <td rowspan="<? echo $rowspanArr[$bookingNo][$suplierID][$buyerID][$po_id]; ?>" width="80" align="center"><? echo $bookingNo; ?></td>
								                        <td rowspan="<? echo $rowspanArr[$bookingNo][$suplierID][$buyerID][$po_id]; ?>" align="center" width="120"><div style="width:120px; word-wrap:break-word;"><?
								                        if($row["pay_mode"]==3 || $row["pay_mode"]==5)
								                        	{ echo $company_library[$suplierID] ;}
								                        else if($row["pay_mode"]==1){echo $suplier[$suplierID];}
								                        else{echo $company_library[$suplierID];} ?>&nbsp;
								                        </div></td>
								                        <td rowspan="<? echo $rowspanArr[$bookingNo][$suplierID][$buyerID][$po_id]; ?>" width="120"><div style="width:90px; word-wrap:break-word;"><? echo $buyer_arr[$buyerID]; ?>&nbsp;</div></td>
								                        <td rowspan="<? echo $rowspanArr[$bookingNo][$suplierID][$buyerID][$po_id]; ?>" width="120" title="<? echo $po_id; ?>"><p><? echo $po_no_arr[$po_id]['po_number']; ?>&nbsp;</p></td>
								                        <td rowspan="<? echo $rowspanArr[$bookingNo][$suplierID][$buyerID][$po_id]; ?>" width="120"><p><? echo $po_no_arr[$po_id]['style_ref_no']; ?>&nbsp;</p></td>
													<?
								                    $i++;
								                    }
								                    ?>

							                        <td rowspan="<? //echo $rowspanArr[$bookingNo][$suplierID][$buyerID][$po_id][$fabrication_id]; ?>" width="150" align="center" title="<? echo $fabrication_id; ?>"><p><? echo $fabric_description_array[$fabrication_id]; ?>&nbsp;</p></p></td>

													<td width="60" align="right"><? echo number_format($row["wo_qnty"],2,'.',''); ?></td>
													<td width="60" align="right"><?
													//echo $bookingNo."=".$po_id."=".$fabric_des_id_arr[$fabrication_id][$bookingNo]."=".$fabric_des_body_part_id_arr[$fabrication_id][$bookingNo]."=".$dia_width."=".$dtls_id."<br/>";

													$body_deter_arr = explode("**", $row["bodypart_deterid"]);
													$body_part_id = $body_deter_arr[0];
													$determination_id = $body_deter_arr[1];

													$fab_issue=$fabric_issue_recv_arr[$bookingNo][$po_id][$determination_id][$body_part_id][$row["dia_width"]]['batch_issue_qty']; echo number_format($fab_issue,2,'.','');
													?></td>
													<td width="60" align="right">
														<?

														$fab_recv=$fabric_issue_recv_arr[$bookingNo][$po_id][$determination_id][$body_part_id][$row["dia_width"]]['batch_recv_qty'];
														?>
														<a href="##" onclick="openmypage_qnty('<? echo $bookingNo;?>','<? echo $po_id;?>','openmypage_receive','<? echo $determination_id;?>','<? echo $body_part_id;?>','<? echo $row["dia_width"]; ?>','0','0','<? echo $date_from;?>','<? echo $date_to;?>','<? echo $row["process"];?>');"><? echo number_format($fab_recv,2,".",""); ?>

														</a>
													</td>
													<td width="60" align="right"><? $balance=($row["wo_qnty"]-$fab_recv); echo number_format($balance,2,'.',''); ?></td>
													<td></td>
												</tr>
												<?

												$tot_wo_qnty+=$row["wo_qnty"];
												$tot_fab_issue+=$fab_issue;
												$tot_fab_recv+=$fab_recv;
												$tot_processLoss+=$processLoss;
												$tot_balance+=$balance;
												$k++;
											}
										}
									}
								}

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
	 							<th align="right" width="60" id="td_fab_balance_id"><? echo number_format($tot_balance,2,'.',''); ?></th>
	 							<th>&nbsp;</th>
				            </tr>
				        </tfoot>
				    </table>
				</div>
			</fieldset>
		</div>
		<?
	}

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


if($action=="openmypage_receive")
{
	echo load_html_head_contents("Receive Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:470px; margin-left:3px">
		<div id="scroll_body" align="center">
			<?
				$companyID=$companyID;
				$booking_no=$booking_no;
				$poID=$poID;
				$fabricDescId=$fabricDescId;
				$bodyPartId=$bodyPartId;
				$diaWidth=$diaWidth;
				$gmts_color=$gmts_color;
				$dtlsId=$dtlsId;
				$buyerId=$buyerId;
				$from_date=$from_date;
				$to_date=$to_date;
				$processId=$processId;
				$preCsotFabricCsot=$preCsotFabricCsot;

				$i=1;
				/*if($body_part_id!='') $body_part_cond=" and b.body_part_id='$body_part_id'"; else $body_part_cond="";
				if($width!='') $width_cond=" and c.width='$width'"; else $width_cond="";
				if($width!='') $width_cond2=" and d.dia_width='$width'"; else $width_cond2="";
				if($prod_ref[8])
				{
					$room_rack_cond = " and b.floor_id='$floor_id' and b.room='$room' and b.rack='$rack' and b.self = '$self'";
					$room_rack_cond2 = " and c.floor_id='$floor_id' and c.room='$room' and c.rack='$rack' and c.self = '$self'";
				}
				if($to_date != "")
				{
					//$date_condition = " and b.transaction_date  between '".$from_date."' and '".$to_date."'";
					//$date_condition_2 = " and c.transaction_date  between '".$from_date."' and '".$to_date."'";
					$date_condition   = " and b.transaction_date <= '$to_date'";
					$date_condition_2 = " and c.transaction_date <= '$to_date'";
				}
				if($transfer_in_ids!=""){$trans_id_cond = "and a.id in($transfer_in_ids)";}
				if($issue_rtn_id!=""){$retrn_id_cond = "and a.id in($issue_rtn_id)";}
				if($recv_rtn_id!=""){$retrn_id_cond2 = "and a.id in($recv_rtn_id)";}

				if($buyerId!=0){$buyerId_cond = "and c.buyer_id in($buyerId)";}*/

				if ($processId==31 || $processId==193)
				{
					$rcv_sql = sql_select("select a.recv_number,TO_CHAR(a.booking_no) as booking_no,0 as booking_dtls_id, d.po_breakdown_id as order_id ,b.body_part_id as body_part_id , TO_CHAR(b.color_id) as color_id,TO_CHAR(e.dia_width) as width, 0 as batch_issue_qty,
					sum(case when a.entry_form=37 then d.quantity else 0 end) as batch_recv_qty, b.fabric_description_id as febric_description_id,a.entry_form,c.transaction_date
					from inv_receive_master a, pro_finish_fabric_rcv_dtls b,inv_transaction c,order_wise_pro_details d,product_details_master e
					where a.id=b.mst_id and b.trans_id=c.id and b.id=d.dtls_id and b.trans_id=d.trans_id and d.prod_id=e.id and a.entry_form=37 and a.item_category=2 and a.receive_basis=11 and a.company_id=$companyID and a.booking_no='$booking_no' and d.po_breakdown_id=$poID and b.fabric_description_id=$fabricDescId and b.body_part_id=$bodyPartId and b.color_id=$gmts_color
					and d.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and d.quantity>0 and b.pre_cost_fabric_cost_dtls_id=$preCsotFabricCsot
					group by a.recv_number,a.booking_no, d.po_breakdown_id ,b.body_part_id ,b.color_id,e.dia_width,b.fabric_description_id,a.entry_form,c.transaction_date");
				}
				else if($processId==1)
				{

					$rcv_sql = sql_select("select a.recv_number,TO_CHAR(a.booking_no) as booking_no, d.po_breakdown_id as order_id ,b.body_part_id as body_part_id ,TO_CHAR(e.dia_width) as width, 0 as batch_issue_qty,
					sum(case when a.entry_form=22 then d.quantity else 0 end) as batch_recv_qty,b.febric_description_id as febric_description_id,a.entry_form,f.transaction_date
					from inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details d,product_details_master e,inv_transaction f
					where a.id=b.mst_id and b.id=d.dtls_id and b.trans_id=d.trans_id and d.prod_id=e.id and d.trans_id=f.id and a.receive_basis=11 and a.entry_form=22  and a.company_id=$companyID and a.booking_no='$booking_no' and d.po_breakdown_id=$poID and b.febric_description_id=$fabricDescId and b.body_part_id=$bodyPartId
					and a.item_category=13 and d.entry_form=22 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
					and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0
					group by a.recv_number,a.booking_no, d.po_breakdown_id ,b.body_part_id,e.dia_width,b.febric_description_id,a.entry_form,f.transaction_date ");
				}
				else if($processId==35)
				{
					$rcv_sql = sql_select("SELECT a.recv_number, b.booking_no,b.booking_dtls_id,a.receive_date as transaction_date, b.order_id,b.body_part_id,b.color_id,b.width as width, sum(case when a.entry_form=92 then b.batch_issue_qty else 0 end) as batch_recv_qty,b.febric_description_id,a.entry_form
					from inv_receive_mas_batchroll a, pro_grey_batch_dtls b left join wo_booking_dtls c on b.booking_dtls_id=c.id and c.fabric_color_id = $gmts_color where a.id=b.mst_id and a.entry_form in(92) and a.company_id=$companyID and b.booking_no='$booking_no' and b.order_id=$poID and b.febric_description_id=$fabricDescId and b.body_part_id=$bodyPartId and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.recv_number, b.booking_no,b.booking_dtls_id,a.receive_date, b.order_id,b.febric_description_id,b.body_part_id ,b.color_id,b.width,a.entry_form
						union all
					select a.recv_number, a.wo_no as booking_no, b.booking_dtls_id, a.receive_date as transaction_date, b.order_id,b.body_part_id,b.color_id,b.width as width, sum(case when a.entry_form=65 then b.ROLL_WGT else 0 end) as batch_recv_qty, c.detarmination_id as febric_description_id,a.entry_form from inv_receive_mas_batchroll a, pro_grey_batch_dtls b , product_details_master c where a.id=b.mst_id and a.entry_form in(65) and a.wo_no='$booking_no' and b.order_id in($poID) and b.body_part_id=$bodyPartId and c.detarmination_id=$fabricDescId and a.company_id=$companyID and b.color_id=$gmts_color and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.prod_id= c.id group by a.recv_number, a.wo_no,b.booking_dtls_id, a.receive_date, b.order_id,b.body_part_id ,b.color_id,b.width,a.entry_form , c.detarmination_id ");
				}

			?>
			<table border="1" class="rpt_table" rules="all" width="470" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="6">Total Receive Pop-up</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="150">Receive No</th>
						<th width="70">Transaction Date</th>
						<th>Qty.</th>
					</tr>
				</thead>
				<tbody>
					<?

					foreach($rcv_sql as $row)
					{
						$date_frm=date('Y-m-d',strtotime($from_date));
						$transaction_date=date('Y-m-d',strtotime($row[csf('receive_date')]));
						if( $from_date == "" || ( $from_date != "" && ($transaction_date >= $date_frm)))
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$tot_reject=$row[csf('returnable_qnty')];
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30"><p><? echo $i; ?></p></td>
								<td width="150" align="center"><p><? echo $row[csf('recv_number')]; ?></p></td>
								<td width="120" align="center"><p><? echo change_date_format($row[csf('transaction_date')]); ?></p></td>
								<td align="right"><p><? echo number_format($row[csf('batch_recv_qty')],2); ?></p></td>
							</tr>
							<?
							$tot_recv_qty+=$row[csf('batch_recv_qty')];
							$i++;
						}
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="3" align="right">Total</td>
						<td align="right">&nbsp;<? echo number_format($tot_recv_qty,2); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table>



		</div>
	</fieldset>
	<?
	exit();
}

if($action=="openmypage_grey_receive")
{
	echo load_html_head_contents("Receive Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$suplier=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	?>


	<fieldset style="width:470px; margin-left:3px">
	<script>

			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

				d.close();
			}

			</script>
		<div id="scroll_body" align="center">
			<?
				$companyID=$companyID;
				$booking_no=$booking_no;
				$poID=$poID;
				$fabricDescId=$fabricDescId;
				$bodyPartId=$bodyPartId;
				$diaWidth=$diaWidth;
				$gmts_color=$gmts_color;
				$dtlsId=$dtlsId;
				$buyerId=$buyerId;
				$from_date=$from_date;
				$to_date=$to_date;
				$processId=$processId;

				$i=1;
				/*if($body_part_id!='') $body_part_cond=" and b.body_part_id='$body_part_id'"; else $body_part_cond="";
				if($width!='') $width_cond=" and c.width='$width'"; else $width_cond="";
				if($width!='') $width_cond2=" and d.dia_width='$width'"; else $width_cond2="";
				if($prod_ref[8])
				{
					$room_rack_cond = " and b.floor_id='$floor_id' and b.room='$room' and b.rack='$rack' and b.self = '$self'";
					$room_rack_cond2 = " and c.floor_id='$floor_id' and c.room='$room' and c.rack='$rack' and c.self = '$self'";
				}
				if($to_date != "")
				{
					//$date_condition = " and b.transaction_date  between '".$from_date."' and '".$to_date."'";
					//$date_condition_2 = " and c.transaction_date  between '".$from_date."' and '".$to_date."'";
					$date_condition   = " and b.transaction_date <= '$to_date'";
					$date_condition_2 = " and c.transaction_date <= '$to_date'";
				}
				if($transfer_in_ids!=""){$trans_id_cond = "and a.id in($transfer_in_ids)";}
				if($issue_rtn_id!=""){$retrn_id_cond = "and a.id in($issue_rtn_id)";}
				if($recv_rtn_id!=""){$retrn_id_cond2 = "and a.id in($recv_rtn_id)";}

				if($buyerId!=0){$buyerId_cond = "and c.buyer_id in($buyerId)";}*/

				if ($processId==31 || $processId==193)
				{
					$rcv_sql = sql_select("SELECT a.recv_number,TO_CHAR(a.booking_no) as booking_no,0 as booking_dtls_id, d.po_breakdown_id as order_id ,b.body_part_id as body_part_id , TO_CHAR(b.color_id) as color_id,TO_CHAR(e.dia_width) as width, 0 as batch_issue_qty,
					sum(case when a.entry_form=37 then d.quantity else 0 end) as batch_recv_qty,
					sum(case when a.entry_form=37 then d.grey_used_qty else 0 end) as grey_used_qty,
					b.fabric_description_id as febric_description_id,a.entry_form,c.transaction_date,a.knitting_source,a.challan_no,a.knitting_company
					from inv_receive_master a, pro_finish_fabric_rcv_dtls b,inv_transaction c,order_wise_pro_details d,product_details_master e
					where a.id=b.mst_id and b.trans_id=c.id and b.id=d.dtls_id and b.trans_id=d.trans_id and d.prod_id=e.id and a.entry_form=37 and a.item_category=2 and a.receive_basis=11 and a.company_id=$companyID and a.booking_no='$booking_no' and d.po_breakdown_id=$poID and b.fabric_description_id=$fabricDescId and b.body_part_id=$bodyPartId and b.color_id=$gmts_color
					and d.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and d.quantity>0
					group by a.recv_number,a.booking_no, d.po_breakdown_id ,b.body_part_id ,b.color_id,e.dia_width,b.fabric_description_id,a.entry_form,c.transaction_date,a.knitting_source,a.challan_no,a.knitting_company");

				}
				else if($processId==35)
				{
					$rcv_sql = sql_select("SELECT a.recv_number, b.booking_no,b.booking_dtls_id,a.receive_date as transaction_date, b.order_id,b.body_part_id,b.color_id,b.width as width, sum(case when a.entry_form=92 then b.batch_issue_qty else 0 end) as batch_recv_qty,sum(case when a.entry_form=92 then b.grey_used else 0 end) as grey_used_qty,b.febric_description_id,a.entry_form,a.challan_no,a.dyeing_source as knitting_source,a.dyeing_company as knitting_company
					from inv_receive_mas_batchroll a, pro_grey_batch_dtls b left join wo_booking_dtls c on b.booking_dtls_id=c.id and c.fabric_color_id=$gmts_color where a.id=b.mst_id and a.entry_form in(92) and a.company_id=$companyID and b.booking_no='$booking_no' and b.order_id=$poID and b.febric_description_id=$fabricDescId and b.body_part_id=$bodyPartId  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.recv_number, b.booking_no,b.booking_dtls_id,a.receive_date, b.order_id,b.febric_description_id,b.body_part_id ,b.color_id,b.width,a.entry_form,a.challan_no,a.dyeing_source,a.dyeing_company
						union all
					select a.recv_number, a.wo_no as booking_no, b.booking_dtls_id, a.receive_date as transaction_date, b.order_id,b.body_part_id,b.color_id,b.width as width, sum(case when a.entry_form=65 then b.ROLL_WGT else 0 end) as batch_recv_qty, 0 as grey_used_qty, c.detarmination_id as febric_description_id,a.entry_form,a.challan_no,a.dyeing_source as knitting_source,a.dyeing_company as knitting_company from inv_receive_mas_batchroll a, pro_grey_batch_dtls b , product_details_master c where a.id=b.mst_id and a.entry_form in(65) and a.wo_no='$booking_no' and b.order_id in($poID) and b.body_part_id=$bodyPartId and b.color_id=$gmts_color and c.detarmination_id=$fabricDescId and a.company_id=$companyID and b.color_id=$gmts_color and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.prod_id= c.id group by a.recv_number, a.wo_no,b.booking_dtls_id, a.receive_date, b.order_id,b.body_part_id ,b.color_id,b.width,a.entry_form , c.detarmination_id,a.challan_no,a.dyeing_source,a.dyeing_company ");
				}
				/* else if($processId==1)
				{

					$rcv_sql = sql_select("select a.recv_number,TO_CHAR(a.booking_no) as booking_no, d.po_breakdown_id as order_id ,b.body_part_id as body_part_id ,TO_CHAR(e.dia_width) as width, 0 as batch_issue_qty,
					sum(case when a.entry_form=22 then d.quantity else 0 end) as batch_recv_qty,b.febric_description_id as febric_description_id,a.entry_form,f.transaction_date
					from inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details d,product_details_master e,inv_transaction f
					where a.id=b.mst_id and b.id=d.dtls_id and b.trans_id=d.trans_id and d.prod_id=e.id and d.trans_id=f.id and a.receive_basis=11 and a.entry_form=22  and a.company_id=$companyID and a.booking_no='$booking_no' and d.po_breakdown_id=$poID and b.febric_description_id=$fabricDescId and b.body_part_id=$bodyPartId
					and a.item_category=13 and d.entry_form=22 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
					and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0
					group by a.recv_number,a.booking_no, d.po_breakdown_id ,b.body_part_id,e.dia_width,b.febric_description_id,a.entry_form,f.transaction_date ");
				}
				else if($processId==35)
				{
					$rcv_sql = sql_select("SELECT a.recv_number, b.booking_no,b.booking_dtls_id,a.receive_date as transaction_date, b.order_id,b.body_part_id,b.color_id,b.width as width, sum(case when a.entry_form=92 then b.batch_issue_qty else 0 end) as batch_recv_qty,b.febric_description_id,a.entry_form
					from inv_receive_mas_batchroll a, pro_grey_batch_dtls b where a.id=b.mst_id and a.entry_form in(92) and a.company_id=$companyID and b.booking_no='$booking_no' and b.order_id=$poID and b.febric_description_id=$fabricDescId and b.body_part_id=$bodyPartId and b.color_id=$gmts_color and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.recv_number, b.booking_no,b.booking_dtls_id,a.receive_date, b.order_id,b.febric_description_id,b.body_part_id ,b.color_id,b.width,a.entry_form
						union all
					select a.recv_number, a.wo_no as booking_no, b.booking_dtls_id, a.receive_date as transaction_date, b.order_id,b.body_part_id,b.color_id,b.width as width, sum(case when a.entry_form=65 then b.ROLL_WGT else 0 end) as batch_recv_qty, c.detarmination_id as febric_description_id,a.entry_form from inv_receive_mas_batchroll a, pro_grey_batch_dtls b , product_details_master c where a.id=b.mst_id and a.entry_form in(65) and a.wo_no='$booking_no' and b.order_id in($poID) and b.body_part_id=$bodyPartId and c.detarmination_id=$fabricDescId and a.company_id=$companyID and b.color_id=$gmts_color and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.prod_id= c.id group by a.recv_number, a.wo_no,b.booking_dtls_id, a.receive_date, b.order_id,b.body_part_id ,b.color_id,b.width,a.entry_form , c.detarmination_id ");
				} */

			?>


			<div style="width:660px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0" align="center">
					<thead>
						<tr>
							<th colspan="8">Total Grey Recv Qnty Pop Up</th>
						</tr>
						<tr>
							<th width="30">Sl</th>
							<th width="150">System Id</th>
							<th width="100">Recv Date</th>
							<th width="100">Challan No</th>
							<th width="100">Supplier</th>
							<th width="100">Color</th>
							<th width="70">Finish Qnty</th>
							<th>Grey Qnty</th>
						</tr>
					</thead>
					<tbody>
						<?
						$i=1;
						$tot_grey_recv_qty = 0;
						foreach($rcv_sql as $row)
						{
							$date_frm=date('Y-m-d',strtotime($from_date));
							$transaction_date=date('Y-m-d',strtotime($row[csf('receive_date')]));
							if( $from_date == "" || ( $from_date != "" && ($transaction_date >= $date_frm)))
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$supplier = '';
								if($row[csf('knitting_source')]== 1)
								{
									$supplier = $company_library[$row[csf('knitting_company')]];
								}
								else if($row[csf('knitting_source')]== 3)
								{
									$supplier = $suplier[$row[csf('knitting_company')]];
								}

								?>
								<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
									<td width="30"><p><? echo $i; ?></p></td>
									<td width="150" align="center"><p><? echo $row[csf('recv_number')]; ?></p></td>
									<td width="100" align="center"><p><? echo change_date_format($row[csf('transaction_date')]); ?></p></td>
									<td width="100" align="center"><p><? echo $row[csf('challan_no')]; ?></p></td>
									<td width="100" align="center"><p><? echo $supplier; ?></p></td>
									<td width="100" align="center"><p><? echo $color_library[$row[csf('color_id')]]; ?></p></td>
									<td width="70" align="center"><p><? echo $row[csf('batch_recv_qty')]; ?></p></td>
									<td align="right"><p><? echo number_format($row[csf('grey_used_qty')],2); ?></p></td>
								</tr>
								<?
								$tot_grey_recv_qty+=$row[csf('grey_used_qty')];
								$i++;
							}
						}
						?>
					</tbody>
					<tfoot>
						<tr class="tbl_bottom">
							<td colspan="7" align="right">Total</td>
							<td align="right">&nbsp;<? echo number_format($tot_grey_recv_qty,2); ?>&nbsp;</td>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</fieldset>
	<?
	exit();
}
?>