<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$user_store_ids = $_SESSION['logic_erp']['store_location_id'];
$user_supplier_ids = $_SESSION['logic_erp']['supplier_id'];
$user_comp_location_ids = $_SESSION['logic_erp']['company_location_id'];
//--------------------------------------------------------------------------------------------

if($action=="upto_variable_settings")
{
    $sql =  sql_select("select store_method from variable_settings_inventory where company_name = $data and item_category_id=1 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	$return_data="";
    if(count($sql)>0)
	{
		$return_data=$sql[0][csf('store_method')];
	}
	else
	{
		$return_data=0;
	}

	echo $return_data;
	die;
}

if ($action == "load_drop_down_floor") {

	echo create_drop_down("cbo_floor", 152, "select FLOOR_ROOM_RACK_ID, FLOOR_ROOM_RACK_NAME from LIB_FLOOR_ROOM_RACK_MST where COMPANY_ID = '$data'", "FLOOR_ROOM_RACK_ID,FLOOR_ROOM_RACK_NAME", 1, "-- Select --", 0, "", 1);
	exit();
}
if ($action == "load_drop_down_room") {

	echo create_drop_down("cbo_room", 152, "select FLOOR_ROOM_RACK_ID, FLOOR_ROOM_RACK_NAME from LIB_FLOOR_ROOM_RACK_MST where COMPANY_ID = '$data'", "FLOOR_ROOM_RACK_ID,FLOOR_ROOM_RACK_NAME", 1, "-- Select --", 0, "", 1);
	exit();
}
if ($action == "load_drop_down_rack") {

	echo create_drop_down("txt_rack", 152, "select FLOOR_ROOM_RACK_ID, FLOOR_ROOM_RACK_NAME from LIB_FLOOR_ROOM_RACK_MST where COMPANY_ID = '$data'", "FLOOR_ROOM_RACK_ID,FLOOR_ROOM_RACK_NAME", 1, "-- Select --", 0, "", 1);
	exit();
}
if ($action == "load_drop_down_shelf") {

	echo create_drop_down("txt_shelf", 152, "select FLOOR_ROOM_RACK_ID, FLOOR_ROOM_RACK_NAME from LIB_FLOOR_ROOM_RACK_MST where COMPANY_ID = '$data'", "FLOOR_ROOM_RACK_ID,FLOOR_ROOM_RACK_NAME", 1, "-- Select --", 0, "", 1);
	exit();
}

if ($action=="load_drop_down_location")
{
	if($user_comp_location_ids) $user_comp_location_cond = " and id in ($user_comp_location_ids)"; else $user_comp_location_cond = "";
	echo create_drop_down( "cbo_location", 170, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id=$data $user_comp_location_cond order by location_name","id,location_name", 1, "-- Select Location --", $selected,"load_room_rack_self_bin('requires/yarn_issue_return_controller*1', 'store','store_td', $('#cbo_company_id').val(), this.value);" );
	exit();
}

	/*if ($action=="load_drop_down_store")
	{
		if($user_store_ids) $user_store_cond = " and a.id in ($user_store_ids)"; else $user_store_cond = "";
		echo create_drop_down( "cbo_store_name", 100, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data' $user_store_cond and b.category_type=1 and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "-- Select --",0,"",0);
		exit();
	}
	*/

	if ($action == "load_drop_down_basis") {
		$issue_basis_requisition_or_demand_variable = return_field_value("yarn_issue_basis", "variable_settings_inventory", "company_name=$data and variable_list=28");
		if($issue_basis_requisition_or_demand_variable==2){
			$issue_basis = array(1 => "Booking", 2 => "Independent", 8 => "Demand", 4 => "Sales Order");
		}else{
			$issue_basis = array(1 => "Booking", 2 => "Independent", 3 => "Requisition", 4 => "Sales Order");
		}
		echo create_drop_down( "cbo_basis", 170, $issue_basis,"", 1, "-- Select Basis --", $selected, "active_inactive(this.value);", "", "");
		exit();
	}
	if ($action=="load_room_rack_self_bin")
	{
		load_room_rack_self_bin("requires/yarn_issue_return_controller",$data);
	}

	if ($action=="load_drop_down_knit_com")
	{
		$exDataArr = explode("**",$data);
		$knit_source=$exDataArr[0];
		$company=$exDataArr[1];
		if($company=="" || $company==0) $company_cod = ""; else $company_cod = " and id=$company";
		if($knit_source==1)
		{
			echo create_drop_down( "cbo_knitting_company", 170, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name","id,company_name", 1, "-- Select --", $company, "" );
		}
		else if($knit_source==3)
		{
			echo create_drop_down( "cbo_knitting_company", 170, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$company' and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name","id,supplier_name", 1, "-- Select --", $company, "",0 );
		}
		else
			echo create_drop_down( "cbo_knitting_company", 170, $blank_array,"", 1, "-- Select --", $selected, "","","" );
		exit();
	}

	if ($action=="fabbook_popup")
	{
		echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
		extract($_REQUEST);
		//echo $company;die;
		?>
		<script>

			function fn_check()
			{
				show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_<? echo $company; ?>_'+<? echo $receive_basis; ?>, 'create_fabbook_search_list_view', 'search_div', 'yarn_issue_return_controller', 'setFilterGrid(\'list_view\',-1)');
			}

			function js_set_value(booking_dtls)
			{
				$("#hidden_booking_number").val(booking_dtls);
				parent.emailwindow.hide();
			}
		</script>

	</head>

	<body>
		<div align="center" style="width:100%;" >
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table width="870" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
					<thead>
						<!--<tr>
							<th colspan="5" align="center"><?// echo create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" ); ?></th>
						</tr>-->
						<tr>
							<th width="160">Buyer Name </th>
							<th width="150">Search By</th>
							<th width="220" align="center" id="search_by_td_up">Job/Booking/Lot</th>
							<th width="200">Issue Date Range</th>
							<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>
						</tr>
					</thead>
					<tbody>
						<tr align="center">
							<td>
								<?
								echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "","" );
								?>
							</td>
							<td>
								<?
								$dd="change_search_event(this.value, '0*0*0*0*0*0', '0*0*0*0*0*0', '../../../')";
								echo create_drop_down( "cbo_search_by", 130, $issue_basis, "", 0, "--Select--", $receive_basis, '',1,"5,6,9,10");
								?>
							</td>
							<td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes_numeric"  name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
							</td>
							<td align="center">
								<input type="button" name="btn_show" class="formbutton" value="Show" onClick="fn_check()" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td align="center" height="40" valign="middle" colspan="5">
								<? echo load_month_buttons(1);  ?>
								<!-- Hidden field here -->
								<input type="hidden" id="hidden_booking_id" value="" />
								<input type="hidden" id="hidden_booking_number" value="" />
								<input type="hidden" name="booking_without_order" id="booking_without_order" class="text_boxes" value="">
								<!-- -END -->
							</td>
						</tr>
					</tbody>
				</tr>
			</table>
			<div align="center" style="margin-top:10px" valign="top" id="search_div"> </div>
		</form>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_fabbook_search_list_view")
{
	$ex_data = explode("_",$data);
	$buyer 				= str_replace("'","",trim($ex_data[0]));
	$txt_search_by 		= str_replace("'","",trim($ex_data[1]));
	$txt_search_common 	= str_replace("'","",trim($ex_data[2]));
	$txt_date_from 		= str_replace("'","",trim($ex_data[3]));
	$txt_date_to 		= str_replace("'","",trim($ex_data[4]));
	$company 			= str_replace("'","",trim($ex_data[5]));
	$receive_basis		= str_replace("'","",trim($ex_data[6]));
	if( $txt_date_from!="" && $txt_date_to!="" )
	{
		if($db_type==0)
		{
			$txt_date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
			$txt_date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
		}
		else
		{
			$txt_date_from=change_date_format($txt_date_from,'','',1);
			$txt_date_to=change_date_format($txt_date_to,'','',1);
		}
	}

	$buyer_arr = return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$company_arr = return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$supplier_arr = return_library_array("select id,supplier_name from lib_supplier",'id','supplier_name');

	if($receive_basis==5)
	{
		$sql_cond="";
		if($buyer) $sql_cond.=" and a.buyer_name=$buyer";
		if($txt_search_common!="") $sql_cond.=" and a.job_no_prefix_num=$txt_search_common";
		if( $txt_date_from!="" && $txt_date_to!="" )  $sql_cond.=" and d.issue_date between '$txt_date_from' and '$txt_date_to'";
		$sql="select a.id as job_id, a.job_no, a.buyer_name, a.style_ref_no, d.id as issue_id, d.issue_number, d.issue_date, sum(c.cons_quantity) as issue_qnty
		from wo_po_details_master a, inv_transaction c, inv_issue_master d 
		where a.job_no=c.job_no and c.mst_id=d.id and c.transaction_type=2 and c.entry_form=277 and d.entry_form=277 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_name=$company and c.receive_basis=$receive_basis $sql_cond
		group by a.id, a.job_no, a.buyer_name, a.style_ref_no, d.id, d.issue_number, d.issue_date
		order by a.id desc";
		
	}
	else if($receive_basis==9)
	{
		$sql_cond="";
		//if($buyer) $sql_cond.=" and a.buyer_name=$buyer";
		if($txt_search_common!="") $sql_cond.=" and a.yarn_dyeing_prefix_num=$txt_search_common";
		if( $txt_date_from!="" && $txt_date_to!="" )  $sql_cond.=" and d.issue_date between '$txt_date_from' and '$txt_date_to'";
		$sql="select a.id as job_id, a.ydw_no as job_no, c.buyer_id as buyer_name, c.style_ref_no, d.id as issue_id, d.issue_number, d.issue_date, sum(c.cons_quantity) as issue_qnty
		from wo_yarn_dyeing_mst a, inv_transaction c, inv_issue_master d 
		where a.ydw_no=c.booking_no and c.mst_id=d.id and c.transaction_type=2 and a.entry_form=335 and c.entry_form=277 and d.entry_form=277 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_id=$company and c.receive_basis=$receive_basis $sql_cond
		group by a.id, a.ydw_no, c.buyer_id, c.style_ref_no, d.id, d.issue_number, d.issue_date
		order by a.id desc";
	}
	else if($receive_basis==10)
	{
		$sql_cond="";
		//if($buyer) $sql_cond.=" and a.buyer_name=$buyer";
		if($txt_search_common!="") $sql_cond.=" and a.wo_number_prefix_num=$txt_search_common";
		if( $txt_date_from!="" && $txt_date_to!="" )  $sql_cond.=" and d.issue_date between '$txt_date_from' and '$txt_date_to'";
		$sql="select a.id as job_id, a.wo_number as job_no, c.buyer_id as buyer_name, c.style_ref_no, d.id as issue_id, d.issue_number, d.issue_date, sum(c.cons_quantity) as issue_qnty
		from wo_non_order_info_mst a, inv_transaction c, inv_issue_master d 
		where a.wo_number=c.booking_no and c.mst_id=d.id and c.transaction_type=2 and a.entry_form=284 and a.wo_basis_id=3 and c.entry_form=277 and d.entry_form=277 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_name=$company and c.receive_basis=$receive_basis $sql_cond
		group by a.id, a.wo_number, c.buyer_id, c.style_ref_no, d.id, d.issue_number, d.issue_date
		order by a.id desc";
	}
	else
	{
		$sql_cond="";
		if($buyer) $sql_cond.=" and c.buyer_id=$buyer";
		if($txt_search_common!="") $sql_cond.=" and a.cut_num_prefix_no=$txt_search_common";
		if( $txt_date_from!="" && $txt_date_to!="" )  $sql_cond.=" and d.issue_date between '$txt_date_from' and '$txt_date_to'";
		$sql="select a.id as job_id, a.cutting_no as job_no, c.buyer_id as buyer_name, c.style_ref_no, d.id as issue_id, d.issue_number, d.issue_date, sum(c.cons_quantity) as issue_qnty
		from ppl_cut_lay_mst a, inv_transaction c, inv_issue_master d 
		where a.id=c.pi_wo_batch_no and c.mst_id=d.id and c.transaction_type=2 and a.entry_form=253 and c.entry_form=277 and d.entry_form=277 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_id=$company and c.receive_basis=$receive_basis $sql_cond
		group by a.id, a.cutting_no, c.buyer_id, c.style_ref_no, d.id, d.issue_number, d.issue_date
		order by a.id desc";
	}
	//echo $sql;die;
	$sql_result=sql_select($sql);
	?>
	<div align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table" >
			<thead>
				<th width="50">SL</th>
				<th width="150">Buyer</th>
				<th width="150">Job/Book/Lot No</th>
				<th width="150">Style</th>
				<th width="110">Issue No</th>
				<th width="80">Issue Date</th>
				<th>Issue Qty.</th>
			</thead>
		</table>
		<div style="width:880px; max-height:240px; overflow-y:scroll" id="list_container_batch" >
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="860" class="rpt_table" id="list_view">
				<?
				$i=1;
				foreach ($sql_result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $receive_basis; ?>_<? echo $row[csf('job_id')]; ?>_<? echo $row[csf('job_no')]; ?>_<? echo $row[csf('issue_id')]; ?>_<? echo $row[csf('issue_number')]; ?>_<? echo $row[csf('issue_qnty')]; ?>_<? echo $row[csf('buyer_name')]; ?>_<? echo $buyer_arr[$row[csf('buyer_name')]]; ?>');">
						<td width="50" align="center"><? echo $i; ?></td>
						<td width="150"><p><? echo $buyer_arr[$row[csf("buyer_name")]];?></p></td>
						<td width="150"><p><? echo $row[csf("job_no")]; ?>&nbsp;</p></td>
						<td width="150"><p><? echo $row[csf("style_ref_no")]; ?>&nbsp;</p></td>
						<td width="110"><p><? echo $row[csf('issue_number')]; ?>&nbsp;</p></td>
						<td width="80" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
						<td align="right"><? echo number_format($row[csf('issue_qnty')],2) ?></p></td>
					</tr>
					<?
					$i++;
				}
				?>
			</table>
		</div>
	</div>
	<?
	exit();
}

if($action=="populate_knitting_source")
{
	$ex_data=explode("**",$data);
	$req_id=str_replace("'","",$ex_data[0]);
	$company_id=str_replace("'","",$ex_data[1]);
	$basis=str_replace("'","",$ex_data[2]);
	$issue_id=str_replace("'","",$ex_data[3]);

	$company_arr = return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');

	if($basis==3)
	{
		$knit_sql=sql_select("select b.knitting_source,b.knitting_party from ppl_yarn_requisition_entry a, ppl_planning_info_entry_dtls b where a.knit_id=b.id and a.requisition_no='$req_id' and a.status_active=1 and b.status_active=1");
		foreach($knit_sql as $row)
		{
			echo "$('#cbo_knitting_source').val('".$row[csf("knitting_source")]."');\n";
			echo "load_drop_down( 'requires/yarn_issue_return_controller','".$row[csf("knitting_source")]."'+'**'+'".$company_id."', 'load_drop_down_knit_com', 'knitting_company_td' );;\n";
			echo "$('#cbo_knitting_company').val('".$row[csf("knitting_party")]."');\n";
		}
	}
	else
	{
		$knit_sql=sql_select("select knit_dye_source, knit_dye_company from inv_issue_master where id=$issue_id");
		
		foreach($knit_sql as $row)
		{
			echo "$('#cbo_knitting_source').val('".$row[csf("knit_dye_source")]."');\n";
			echo "load_drop_down( 'requires/yarn_issue_return_controller','".$row[csf("knit_dye_source")]."'+'**'+'".$company_id."', 'load_drop_down_knit_com', 'knitting_company_td' );;\n";
			echo "$('#cbo_knitting_company').val('".$row[csf("knit_dye_company")]."');\n";
		}
	}
	exit();

}

if($action=="populate_knitting_source_book")
{
	if($data!="") $knit_sql=sql_select("select knit_dye_source, knit_dye_company from inv_issue_master where id=$data");
	foreach($knit_sql as $row)
	{
		echo "$('#cbo_knitting_source').val('".$row[csf("knit_dye_source")]."');\n";
		echo "load_drop_down( 'requires/yarn_issue_return_controller','".$row[csf("knitting_source")]."'+'**'+'".$company_id."', 'load_drop_down_knit_com', 'knitting_company_td' );;\n";
		echo "$('#cbo_knitting_company').val('".$row[csf("knit_dye_company")]."');\n";
		exit();
	}
}


if ($action == "adjust_allocation_to_order") {
	extract($_REQUEST);
	$data = explode("_", $data);
	$po_ids = explode(",",$data[0]);
	$po_nos = explode(",",$data[1]);
	$job_nos = explode(",",$data[2]);
	$booking_nos = explode(",",$data[3]);
	?>
	<div align="center">
		<table class="rpt_table" id="tbl_allocation_po" border="1" cellpadding="0" cellspacing="0" rules="all" width="400" align="center">
			<thead>
				<th width="200">PO Number</th>
				<th>Allocation Quantity</th>
			</thead>
			<tbody>
				<?
				$i=0;
				foreach ($po_ids as $po) { ?>
					<tr>
						<td>
							<? echo $po_nos[$i];?>
							<input type="hidden" name="txtPoId[]" value="<? echo $po;?>" />
							<input type="hidden" name="txtPoNo[]" value="<? echo $po_nos[$i];?>" />
							<input type="hidden" name="txtJobNo[]" value="<? echo $job_nos[$i];?>" />
							<input type="hidden" name="txtBookingNo[]" value="<? echo $booking_nos[$i];?>" />
						</td>
						<td align="center"><input type="text" name="txtAllocationQnty[]" id="txtAllocationQnty<? echo $po;?>" class="text_boxes_numeric" width="100" /></td>
					</tr>
					<?
					$i++;
				}
				?>
			</tbody>
		</table>
		<table width="820" cellspacing="0" cellpadding="0" style="border:none" align="center">
			<tr>
				<td align="center" height="30" valign="bottom">
					<div style="width:100%">
						<div style="width:50%; margin:auto;">
							<input type="button" name="close" onClick="fnc_close()" class="formbutton" value="Close" style="width:100px"/>
						</div>
					</div>
				</td>
			</tr>
		</table>
	</div>
	<?
}



if($action=="itemdesc_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>

	<script>
	function js_set_value(mrr)
	{
 		$("#hidden_recv_number").val(mrr); // mrr number
 		parent.emailwindow.hide();
 	}
 </script>

</head>

<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			<table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
				<thead>
					<tr>
						<th width="170">Search By</th>
						<th width="180" align="center" id="search_by_td_up">Enter Lot No</th>
						<th width="240">Date Range</th>
						<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>
					</tr>
				</thead>
				<tbody>
					<tr class="general">
						<td>
							<?
							$search_by = array(1=>'Lot No',2=>'Item Name',3=>'Store Name');
							$dd="change_search_event(this.value, '0*0*1', '0*0*select id,store_name from lib_store_location where company_id=$company', '../../../') ";
							echo create_drop_down( "cbo_search_by", 140, $search_by,"",0, "--Select--", "",$dd,0 );
							?>
						</td>
						<td width="" align="center" id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
						<td align="center">
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" />
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" />
						</td>
						<td align="center">
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_<? echo $company; ?>_'+'<? echo $booking_no; ?>'+'_'+'<? echo $basis; ?>'+'_'+'<? echo $issue_id; ?>'+'_'+'<? echo $booking_id; ?>', 'create_item_search_list_view', 'search_div', 'yarn_issue_return_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td align="center" height="40" valign="middle" colspan="5">
							<? echo load_month_buttons(1);  ?>
							<!-- Hidden field here-->
							<input type="hidden" id="hidden_recv_number" value="" />

						</td>
					</tr>
				</tbody>
			</tr>
		</table>
		<div style="margin-top:5px" align="center" valign="top" id="search_div"> </div>
	</form>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_item_search_list_view")
{
	$ex_data = explode("_",$data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$fromDate = $ex_data[2];
	$toDate = $ex_data[3];
	$company = $ex_data[4];
	$booking_no = $ex_data[5];
	$basis = $ex_data[6];
	$issue_id = $ex_data[7];
	$booking_id = $ex_data[8];

	$sql_cond="";
	if(trim($txt_search_common)!="")
	{
		if(trim($txt_search_by)==1) // for lot no
		$sql_cond .= " and c.lot LIKE '%$txt_search_common%'";
 		else if(trim($txt_search_by)==2) // for issue no
 		$sql_cond .= " and c.product_name_details LIKE '%$txt_search_common%'";
		else if(trim($txt_search_by)==3) // for chllan no
		$sql_cond .= " and b.store_id='$txt_search_common'";		
	}

	if($basis==5)
	{
		$sql_cond .= " and b.receive_basis=$basis and b.job_no='$booking_no'";
	}
	else if($basis==6)
	{
		$sql_cond .= " and b.receive_basis=$basis and b.pi_wo_batch_no=$booking_id";
	}
	else
	{
		$sql_cond .= " and b.receive_basis=$basis and b.booking_no='$booking_no'";
	}

	if( $fromDate!="" && $toDate!="" )
	{
		if($db_type==0)
		{
			$sql_cond .= " and a.issue_date between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";
		}
		else
		{
			$sql_cond .= " and a.issue_date between '".change_date_format($fromDate,'','',1)."' and '".change_date_format($toDate,'','',1)."'";
		}
	}
	if(trim($company)!="") $sql_cond .= " and a.company_id='$company'";
	if(trim($issue_id)!="" && trim($issue_id)>0) $sql_cond .= " and a.id=$issue_id";

	if($db_type==0) $year_field="YEAR(a.insert_date) as year ";
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year ";
	else $year_field="";//defined Later
	

	$sql="select a.id, a.issue_date, a.issue_basis, a.issue_purpose, a.issue_number_prefix_num, a.issue_number, a.challan_no, $year_field, c.product_name_details, c.color, c.lot, c.current_stock, c.supplier_id, c.id as prod_id, b.store_id, b.job_no, b.style_ref_no, b.buyer_id, sum(b.cons_quantity) as issue_qnty, sum(b.return_qnty) as returnable_qnty, a.company_id
	from inv_issue_master a, inv_transaction b, product_details_master c
	where a.id=b.mst_id and b.prod_id=c.id and a.status_active=1 and c.status_active=1 and b.item_category=1 and b.transaction_type=2 $sql_cond 
	group by a.id, a.issue_date, a.issue_basis, a.issue_purpose, a.issue_number_prefix_num, a.issue_number, a.challan_no, a.insert_date, c.product_name_details, c.color, c.lot, c.current_stock, c.supplier_id, c.id, b.store_id, b.job_no, b.style_ref_no, b.buyer_id, a.company_id";
	$result = sql_select($sql);
	// echo "<pre>";
	// print_r($result);
	//echo $sql;die;
	//echo $sql;// and c.current_stock>0
	$comp =  $result[0]['COMPANY_ID'];
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$arr=array(3=>$yarn_issue_purpose,5=>$color_arr,7=>$store_arr,9=>$supplier_arr);
	$comp = $result[0]['COMPANY_ID'];
	echo create_list_view("list_view", "Issue No, Year, Issue Date, Issue Purpose, Item Name Details, Color, Lot No, Store, Challan No, Supplier, Issue Qty, Returnable Qty","50,50,70,100,200,100,70,120,70,110,55","1150","260",0, $sql , "js_set_value", "prod_id,id", "", 1, "0,0,0,issue_purpose,0,color,0,store_id,0,supplier_id", $arr, "issue_number_prefix_num,year,issue_date,issue_purpose,product_name_details,color,lot,store_id,challan_no,supplier_id,issue_qnty,returnable_qnty", "","",'0,0,3,0,0,0,0,0,0,0,2,2') ;
	exit();

	?><script src="../../../includes/functions_bottom.js" type="text/javascript"></script><?
}

if($action=="populate_data_from_data")
{
	$ex_data = explode("_",$data);
	$prodID = $ex_data[0];
	$issueID = $ex_data[1];
	


	/*
	$issueID = $ex_data[2];
	$requisition_no = $ex_data[3];
	$demand_id = $ex_data[4];
	$issueBasis = $ex_data[5];
	$booking_id = $ex_data[6];
	$requisition_cond = ($issueBasis==3 && $requisition_no!="") ? "and b.requisition_no='$requisition_no'":"";
	$requisition_cond2 = ($issueBasis==3 && $requisition_no!="") ? "and requisition_no='$requisition_no'":"";

	if($issueBasis==3 && $requisition_no!="")
	{
		$booking_id = $requisition_no;

	}else if($issueBasis==1 && $ex_data[6]!=""){
		$booking_id = $ex_data[6];
	}

	if($issueBasis==8 && $demand_id!="") {
		$demand_id_cond="and b.demand_id=$demand_id";
		$demand_id_cond2="and demand_id=$demand_id";
	}else{
		$demand_id_cond=$demand_id_cond2="";
	}*/

	$totalIssuedQty = return_field_value("sum(cons_quantity+cons_reject_qnty) as issue_qnty", "inv_transaction", "prod_id=$prodID and item_category=1 and transaction_type=2 and status_active=1 and is_deleted=0 and mst_id=$issueID ","issue_qnty");

	if($db_type==0)
	{		
		$return_sql = sql_select("select sum(b.cons_quantity+IFNULL(cons_reject_qnty, 0)) as return_qty from  inv_receive_master a,inv_transaction b where a.id=b.mst_id and b.issue_id=$issueID and b.prod_id=$prodID and b.item_category=1 and b.transaction_type=4 and b.status_active=1 and b.is_deleted=0 and a.entry_form=382 and b.entry_form=382");
	}else{ 
		$return_sql = sql_select("select sum(b.cons_quantity+NVL(cons_reject_qnty, 0)) as return_qty from  inv_receive_master a,inv_transaction b where a.id=b.mst_id and b.issue_id=$issueID and b.prod_id=$prodID and b.item_category=1 and b.transaction_type=4 and b.status_active=1 and b.is_deleted=0 and a.entry_form=382 and b.entry_form=382");	
	}

	$sql = "select a.id, a.issue_date, a.booking_no, a.issue_purpose, a.issue_number_prefix_num, c.id as prod_id, c.supplier_id, c.product_name_details, c.lot, c.unit_of_measure ,c.avg_rate_per_unit, c.color, b.job_no, b.style_ref_no, b.buyer_id,sum(b.cons_quantity) as cons_quantity, sum(b.cons_amount) as cons_amount, sum(b.return_qnty) as returnable_qnty, b.floor_id, b.room, b.rack, b.self, b.bin_box, a.company_id, a.store_id
	from inv_issue_master a, inv_transaction b, product_details_master c
	where a.id=b.mst_id and b.prod_id=c.id and a.id=$issueID and c.id=$prodID and b.item_category=1 and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form=277
	group by a.id,a.issue_date,a.booking_no,a.issue_purpose,a.issue_number_prefix_num,c.id,c.supplier_id, c.product_name_details,c.lot,c.unit_of_measure,c.avg_rate_per_unit, c.color, b.job_no, b.style_ref_no, b.buyer_id, b.floor_id, b.room, b.rack, b.self, b.bin_box, a.company_id, a.store_id";
	// echo $sql;die;
	
	$res = sql_select($sql);
	
	foreach($res as $row)
	{
		if($row[csf("avg_rate_per_unit")]>0)
		{
			$avg_rate=$row[csf("avg_rate_per_unit")];
		}
		else
		{
			$avg_rate=number_format($row[csf("cons_amount")]/$row[csf("cons_quantity")],4,'.','');
		}

		echo "$('#txt_item_description').val('".$row[csf("product_name_details")]."');\n";
		echo "$('#txt_prod_id').val('".$row[csf("prod_id")]."');\n";
		echo "$('#txt_supplier_id').val('".$row[csf("supplier_id")]."');\n";
		echo "$('#txt_yarn_lot').val('".$row[csf("lot")]."');\n";
		echo "$('#cbo_uom').val(".$row[csf("unit_of_measure")].");\n";
		echo "$('#txt_rate').val(".$avg_rate.");\n";
		echo "$('#cbo_color').val('".$row[csf("color")]."');\n";
		echo "$('#txt_style_no').val('".$row[csf("style_ref_no")]."');\n";
		echo "$('#txt_job_no').val('".$row[csf("job_no")]."');\n";
		echo "$('#cbo_buyer_name').val('".$row[csf("buyer_id")]."');\n";
		//echo "$('#txt_issue_challan_no').val('".$row[csf("issue_number_prefix_num")]."');\n";
		echo "$('#txt_issue_id').val('".$row[csf("id")]."');\n";
		echo "$('#txt_issue_qnty').val('".$totalIssuedQty."');\n";
		echo "$('#hide_issue_date').val('".change_date_format($row[csf("issue_date")])."');\n";

		$totalReturned = $return_sql[0][csf('return_qty')];
		if($totalReturned=="") $totalReturned=0;
		echo "$('#txt_total_return').val('".$totalReturned."');\n";
		echo "$('#txt_total_return_display').val('".$totalReturned."');\n";
		$netUsed = $totalIssuedQty-$totalReturned;
		echo "$('#txt_net_used').val('".$netUsed."');\n";
		$returnableBl = $row[csf("returnable_qnty")]-$totalReturned;
		echo "$('#txt_returnable_qnty').val('".number_format($row[csf("returnable_qnty")],2,".","")."');\n";
		echo "$('#txt_returnable_bl_qnty').val('".number_format($returnableBl,2,".","")."');\n";
		echo "$('#cbo_store_name').val('".$row[csf("store_id")]."');\n";
		echo "$('#cbo_store_name').attr('disabled','disabled');\n";

		echo "$('#cbo_floor').val('".$row[csf("floor_id")]."');\n";
		echo "$('#cbo_room').val('".$row[csf("room")]."');\n";
		echo "$('#txt_rack').val('".$row[csf("rack")]."');\n";
		echo "$('#txt_shelf').val('".$row[csf("self")]."');\n";
		

		//echo "return_qnty_basis(".$row[csf("issue_purpose")].");\n";

		/*if($row[csf("issue_purpose")] == 7 || $row[csf("issue_purpose")] == 15 || $row[csf("issue_purpose")] == 38 || $row[csf("issue_purpose")] == 46){
			$booking_without_order = return_field_value("booking_without_order","wo_yarn_dyeing_mst","ydw_no='".$row[csf("booking_no")]."' and status_active=1 and is_deleted=0");
			if($booking_without_order==2){
				echo "$('#txt_return_qnty').attr('placeholder','Entry');\n";
				echo "$('#txt_return_qnty').removeAttr('ondblclick');\n";
				echo "$('#txt_return_qnty').removeAttr('readOnly');\n";
				echo "$('#txt_reject_qnty').removeAttr('readOnly');\n";
				echo "$('#cbo_adjust_to').val(2).attr('disabled','disabled');\n";
				echo "$('#txt_adjust_po').attr('disabled','disabled');\n";
			}else{
				echo "$('#cbo_adjust_to').val(0).removeAttr('disabled');\n";
				echo "$('#txt_adjust_po').removeAttr('disabled');\n";
			}
		}else{
			echo "$('#cbo_adjust_to').val(0).removeAttr('disabled');\n";
			echo "$('#txt_adjust_po').removeAttr('disabled');\n";
		}

		if( ($issueBasis == 2) && ($row[csf("issue_purpose")] == 54) ) // issue purpose 54 = fabric narrow
		{
			echo "$('#txt_return_qnty').attr('placeholder','Entry');\n";
			echo "$('#txt_return_qnty').removeAttr('ondblclick');\n";
			echo "$('#txt_return_qnty').removeAttr('readOnly');\n";
			echo "$('#txt_reject_qnty').removeAttr('readOnly');\n";
		}*/

	}
	exit();
}

//data save update delete here------------------------------//
if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$con = connect();
	if($db_type==0)	{ mysql_query("BEGIN"); }

	// check variable settings if allocation is available or not
	$is_update_cond = ($operation == 0 ) ? "" : " and id <> $update_id ";
	$max_transaction_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$txt_prod_id and store_id = $cbo_store_name  $is_update_cond  and status_active = 1", "max_date");
	if($max_transaction_date != "")
	{
		$max_transaction_date = date("Y-m-d", strtotime($max_transaction_date));
		$receive_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_return_date)));
		if ($receive_date < $max_transaction_date)
		{
			echo "20**Return Date Can not Be Less Than Last Transaction Date Of This Lot";
			die;
		}
	}

	$total_issue_Qty =  return_field_value("sum(cons_quantity) as issue_qnty","inv_transaction", " prod_id=$txt_prod_id and item_category=1 and transaction_type=2 and status_active=1 and is_deleted=0 and mst_id=$txt_issue_id and entry_form=277","issue_qnty");

	if($operation==0)
	{	
		if($db_type==0)
		{
			$return_qnty = return_field_value("sum(b.cons_quantity+IFNULL(cons_reject_qnty, 0)) as return_qty",  "inv_receive_master a,inv_transaction b"," a.id=b.mst_id and b.issue_id=$txt_issue_id and b.prod_id=$txt_prod_id and b.item_category=1 and b.transaction_type=4 and b.status_active=1 and b.is_deleted=0 and a.entry_form=382 and b.entry_form=382","return_qty");
		}else{
			$return_qnty = return_field_value("sum(b.cons_quantity+NVL(cons_reject_qnty, 0)) as return_qty",  "inv_receive_master a,inv_transaction b"," a.id=b.mst_id and b.issue_id=$txt_issue_id and b.prod_id=$txt_prod_id and b.item_category=1 and b.transaction_type=4 and b.status_active=1 and b.is_deleted=0 and a.entry_form=382 and b.entry_form=382","return_qty");
		}			

		//$total_return_qnty = str_replace("'", "", $txt_return_qnty) + $return_qnty;
	}
	else
	{

		if($db_type==0)
		{
			$return_qnty = return_field_value("sum(b.cons_quantity+IFNULL(cons_reject_qnty, 0)) as return_qty",  "inv_receive_master a,inv_transaction b"," a.id=b.mst_id and a.id!=$txt_mst_id and b.issue_id=$txt_issue_id and b.prod_id=$txt_prod_id and b.item_category=1 and b.transaction_type=4 and b.status_active=1 and b.is_deleted=0 and a.entry_form=382 and b.entry_form=382","return_qty"); 
		}else{
			$return_qnty = return_field_value("sum(b.cons_quantity+NVL(cons_reject_qnty, 0)) as return_qty",  "inv_receive_master a,inv_transaction b"," a.id=b.mst_id and a.id!=$txt_mst_id and b.issue_id=$txt_issue_id and b.prod_id=$txt_prod_id and b.item_category=1 and b.transaction_type=4 and b.status_active=1 and b.is_deleted=0 and a.entry_form=382 and b.entry_form=382","return_qty"); 
		}	
		//$total_return_qnty = str_replace("'", "", $txt_return_qnty) + $return_qnty;
	}

	if($operation==0 || $operation==1 )
	{
	    if (  $total_return_qnty>$total_issue_Qty ) 
	    {
			echo "31**Return quantity can not be greater than Issue Quantity.\nIssue quantity = $total_issue_Qty";
			disconnect($con);
			die();	
		}
		else
		{
			$allowedRtnQty = ($total_issue_Qty-$return_qnty-$totalRcvQty);
			$actualIssueQty = $total_issue_Qty-$return_qnty;
			if($total_return_qnty>$allowedRtnQty)
			{
				$rcvNum = implode(",",array_keys($rcvMrrData));
				echo "31**Receive Found\nReceive No=$rcvNum\nReceive quantity=$totalRcvQty\nIssue Quantity=$actualIssueQty\nAllowed Quantity=$allowedRtnQty";
				disconnect($con);
				die();	
			}	
		}
	}
    
	//echo "10**"; die();

	//$variable_set_allocation = return_field_value("allocation","variable_settings_inventory","company_name=$cbo_company_id and variable_list=18 and item_category_id = 1");
	if( $operation==0 ) // Insert Here
	{
		//---------------Check Duplicate product in Same return number ------------------------//
		$duplicate = is_duplicate_field("b.id","inv_receive_master a, inv_transaction b","a.id=b.mst_id and a.recv_number=$txt_return_no and b.prod_id=$txt_prod_id and b.issue_id=$txt_issue_id and b.transaction_type=4");
		if($duplicate==1)
		{
			echo "20**Duplicate Product is Not Allow in Same Return Number.";
			disconnect($con);
			die;
		}
		//------------------------------Check Brand END---------------------------------------//

		//adjust product master table START-------------------------------------//
		$txt_return_qnty = str_replace("'","",$txt_return_qnty);
		$txt_return_value = str_replace("'","",$txt_amount);
		$cbo_basis = str_replace("'","",$cbo_basis);

		if($txt_return_qnty=="") $txt_return_qnty=0;
		if($txt_amount=="") $txt_amount=0;

		$sql = sql_select("select product_name_details,avg_rate_per_unit,last_purchased_qnty,current_stock,stock_value,allocated_qnty,available_qnty,dyed_type from product_details_master where id=$txt_prod_id");
		$presentStock=$presentStockValue=$presentAvgRate=$allocated_qnty=$available_qnty=$allocated_qnty_balance=$available_qnty_balance=0;
		foreach($sql as $result)
		{
			$presentStock		= $result[csf("current_stock")];
			$presentStockValue	= $result[csf("stock_value")];
			$presentAvgRate		= $result[csf("avg_rate_per_unit")];
			$allocated_qnty		= $result[csf("allocated_qnty")];
			$available_qnty		= $result[csf("available_qnty")];
			$dyed_type			= $result[csf("dyed_type")];
		}
		
		/*if(str_replace("'","",$cbo_basis)==5 || str_replace("'","",$cbo_basis)==9 || str_replace("'","",$cbo_basis)==10)
		{
			$allocated_qnty_balance=$allocated_qnty;
			$available_qnty_balance=$available_qnty+str_replace("'", "", $txt_return_qnty);
		}
		elseif(str_replace("'","",$cbo_basis)==6)
		{
			$allocated_qnty_balance=$allocated_qnty+str_replace("'", "", $txt_return_qnty);
			$available_qnty_balance=$available_qnty;
		}
		else
		{
			$allocated_qnty_balance = $allocated_qnty;
			$available_qnty_balance = $available_qnty+str_replace("'", "", $txt_return_qnty);
		}*/
		
		$allocated_qnty_balance = $allocated_qnty;
		$available_qnty_balance = $available_qnty+str_replace("'", "", $txt_return_qnty);
		

		
		$nowStock 		= $presentStock+$txt_return_qnty;
		$nowStockValue 	= $presentStockValue+$txt_return_value;
		$nowAvgRate		= number_format( $nowStockValue/$nowStock,$dec_place[3],".","" );

		$field_array_prod="last_purchased_qnty*current_stock*avg_rate_per_unit*stock_value*allocated_qnty*available_qnty*updated_by*update_date";
		$data_array_prod=$txt_return_qnty."*".$nowStock."*'".$nowAvgRate."'*".$nowStockValue."*".$allocated_qnty_balance."*".$available_qnty_balance."*'".$user_id."'*'".$pc_date_time."'";
		//adjust product master table END  -------------------------------------//

		//yarn master table entry here START---------------------------------------//
		//$currency=array(1=>"Taka",2=>"USD",3=>"EURO");
		if(str_replace("'","",$txt_return_no)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)";
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";

			$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con);
			$new_recv_number = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master",$con,1,$cbo_company_id,'SYIR',382,date("Y",time()) ));

			$field_array="id, recv_number_prefix, recv_number_prefix_num, recv_number, entry_form, item_category, company_id, receive_basis, receive_date, booking_id, booking_no, knitting_source, knitting_company, challan_no, location_id, exchange_rate, currency_id, supplier_id, issue_id, remarks, inserted_by, insert_date";
			$data_array="(".$id.",'".$new_recv_number[1]."','".$new_recv_number[2]."','".$new_recv_number[0]."',382,1,".$cbo_company_id.",".$cbo_basis.",".$txt_return_date.",".$txt_booking_id.",".$txt_booking_no.",".$cbo_knitting_source.",".$cbo_knitting_company.",".$txt_return_challan_no.",".$cbo_location.",1,1,".$txt_supplier_id.",".$txt_issue_id.",".$txt_remarks.",'".$user_id."','".$pc_date_time."')";
		}
		else
		{
			$new_recv_number[0] = str_replace("'","",$txt_return_no);
			$id=str_replace("'","",$txt_mst_id);
			$field_array="receive_date*booking_id*booking_no*knitting_source*knitting_company*challan_no*location_id*exchange_rate*currency_id*supplier_id*issue_id*remarks*updated_by*update_date";
			$data_array=$txt_return_date."*".$txt_booking_id."*".$txt_booking_no."*".$cbo_knitting_source."*".$cbo_knitting_company."*".$txt_return_challan_no."*".$cbo_location."*1*1*".$txt_supplier_id."*".$txt_issue_id."*".$txt_remarks."*'".$user_id."'*'".$pc_date_time."'";
		}
		//yarn master table entry here END---------------------------------------//

		/******** original product id check start ********/
		$origin_prod_id=return_field_value("origin_prod_id","inv_transaction","prod_id=$txt_prod_id and status_active=1 and mst_id=$txt_issue_id and transaction_type in (2) and item_category=1","origin_prod_id");

		/******** original product id check end ********/

		//transaction table insert here START--------------------------------//
		$dtlsid = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		//$transaction_type=array(1=>"Receive",2=>"Issue",3=>"Receive Return",4=>"Issue Return");
		$field_array_trans = "id,mst_id,entry_form,receive_basis,company_id,supplier_id,prod_id,origin_prod_id,item_category,transaction_type,transaction_date,store_id,floor_id,room,rack,self,order_uom,order_qnty,order_rate,order_amount,cons_uom,cons_quantity,cons_reject_qnty,cons_rate,cons_amount,balance_qnty,balance_amount,issue_challan_no,issue_id,remarks,inserted_by,insert_date,job_no,style_ref_no,buyer_id,pi_wo_batch_no,booking_no,weight_editable,fabric_ref";
		$data_array_trans= "(".$dtlsid.",".$id.",382,".$cbo_basis.",".$cbo_company_id.",".$txt_supplier_id.",".$txt_prod_id.",'".$origin_prod_id."',1,4,".$txt_return_date.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_uom.",".$txt_return_qnty.",".$txt_rate.",".$txt_amount.",".$cbo_uom.",".$txt_return_qnty.",".$txt_reject_qnty.",".$txt_rate.",".$txt_amount.",".$txt_return_qnty.",".$txt_amount.",".$txt_issue_challan_no.",".$txt_issue_id.",".$txt_remarks.",'".$user_id."','".$pc_date_time."',".$txt_job_no.",".$txt_style_no.",".$cbo_buyer_name.",".$txt_booking_id.",".$txt_booking_no.",".$txt_westage_qnty.",".$txt_westage_dtls.")";
 		//transaction table insert here END ---------------------------------//


		$prodUpdate=$rID=$dtlsrID=true;
		if(str_replace("'","",$txt_return_qnty)>0)
		{
			$prodUpdate = sql_update("product_details_master",$field_array_prod,$data_array_prod,"id",$txt_prod_id,1);
		}

		if(str_replace("'","",$txt_return_no)=="")
		{
			$rID=sql_insert("inv_receive_master",$field_array,$data_array,1);
		}
		else
		{
			$rID = sql_update("inv_receive_master",$field_array,$data_array,"id",$id,1);
		}
		$dtlsrID = sql_insert("inv_transaction",$field_array_trans,$data_array_trans,1);

		//echo "10**".$prodUpdate." && ".$rID." && ".$dtlsrID;die;

		if($db_type==0)
		{
			if( $prodUpdate && $rID && $dtlsrID)
			{
				mysql_query("COMMIT");
				echo "0**".$id."**".$new_recv_number[0];
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".$new_recv_number[0];
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if( $prodUpdate && $rID && $dtlsrID)
			{
				oci_commit($con);
				echo "0**".$id."**".$new_recv_number[0];
			}
			else
			{
				oci_rollback($con);
				echo "10**".$new_recv_number[0];
			}
		}

		disconnect($con);
		die;

	}
	else if ($operation==1) // Update Here----------------------------------------------------------
	{

		//check update id
		if( str_replace("'","",$update_id) == "" || str_replace("'","",$txt_prod_id) == "" || str_replace("'","",$before_prod_id) == "" )
		{
			echo "15";
			disconnect($con);
			exit();
		}

		$mrr_issue_check = return_field_value("sum(issue_qnty) as  issue_qnty", "inv_mrr_wise_issue_details", "recv_trans_id=$update_id and status_active=1 and	is_deleted=0", "issue_qnty");
		if (str_replace("'", "", $txt_return_qnty) < $mrr_issue_check) {
			echo "31**Issue Return quantity can not be less than Issue quantity";
			disconnect($con);
			die;
		}

		$sql = sql_select("select a.cons_quantity,a.cons_amount,b.current_stock,b.stock_value,b.allocated_qnty,b.available_qnty,b.dyed_type from inv_transaction a, product_details_master b where a.id=$update_id and a.prod_id=b.id");
		$beforeReturnQnty=$beforeReturnValue=0;
		$currentStockQnty=$currentStockValue=$before_available_qnty=0;
		foreach($sql as $result)
		{
			//current stock
			$currentStockQnty		= $result[csf("current_stock")];
			$currentStockValue		= $result[csf("stock_value")];
			//before return qnty
			$beforeReturnQnty		= $result[csf("cons_quantity")];
			$beforeReturnValue		= $result[csf("cons_amount")];
			$before_allocated_qnty	= $result[csf("allocated_qnty")];
			$before_available_qnty	= $result[csf("available_qnty")];
			$dyed_type				= $result[csf("dyed_type")];
		}


		$sql = sql_select("select product_name_details,avg_rate_per_unit,last_purchased_qnty,current_stock,stock_value,allocated_qnty,available_qnty from product_details_master where id=$txt_prod_id");
		$presentStock=$presentStockValue=$presentAvgRate=$available_qnty=0;
		foreach($sql as $result)
		{
			$presentStock			= $result[csf("current_stock")];
			$presentStockValue		= $result[csf("stock_value")];
			$presentAvgRate			= $result[csf("avg_rate_per_unit")];
			$allocated_qnty			= $result[csf("allocated_qnty")];
			$available_qnty			= $result[csf("available_qnty")];
		}

		$issue_row = sql_select("select id,issue_purpose,issue_basis,entry_form,buyer_job_no,booking_no from inv_issue_master where id=$txt_issue_id");
		$issue_purpose = $issue_row[0][csf('issue_purpose')];
		$issue_basis = $issue_row[0][csf('issue_basis')];
		$entry_form = $issue_row[0]['entry_form'];

		//adjust product master table START-------------------------------------//
		$txt_return_qnty = str_replace("'","",$txt_return_qnty);
		$txt_return_value = str_replace("'","",$txt_amount);

		if($txt_return_qnty=="") $txt_return_qnty=0;
		if($txt_return_value=="") $txt_return_value=0;

		$txt_prod_id = str_replace("'","",$txt_prod_id);
		$update_array="current_stock*stock_value*allocated_qnty*available_qnty*updated_by*update_date";
		$update_data = $updateID_array = array();
		$cbo_adjust_to = str_replace("'","",$cbo_adjust_to);
		if(str_replace("'","",$txt_prod_id) == str_replace("'","",$before_prod_id))
		{
			/*if(str_replace("'","",$cbo_basis)==5 || str_replace("'","",$cbo_basis)==9 || str_replace("'","",$cbo_basis)==10)
			{
				$presentallocatedQnty=$before_allocated_qnty;
				$presentAvailableQnty=(($before_available_qnty-$beforeReturnQnty)+str_replace("'", "", $txt_return_qnty));
			}
			elseif(str_replace("'","",$cbo_basis)==6)
			{
				$presentallocatedQnty=(($before_allocated_qnty-$beforeReturnQnty)+str_replace("'", "", $txt_return_qnty));
				$available_qnty_balance=$before_available_qnty;
			}
			else
			{
				$presentallocatedQnty = $before_allocated_qnty;
				$presentAvailableQnty = $before_available_qnty-$beforeReturnQnty+$txt_return_qnty;
			}*/
			
			$presentallocatedQnty = $before_allocated_qnty;
			$presentAvailableQnty = $before_available_qnty-$beforeReturnQnty+$txt_return_qnty;
			
			$presentStockQnty   = $currentStockQnty-$beforeReturnQnty+$txt_return_qnty; //current qnty - before qnty + present return qnty
			$presentStockValue  = $currentStockValue-$beforeReturnValue+$txt_return_value;
			$avgRate			= number_format($presentStockValue/$presentStockQnty,$dec_place[3],".","");
			$data_array			= $presentStockQnty."*".$presentStockValue."*".$presentallocatedQnty."*".$presentAvailableQnty."*'".$user_id."'*'".$pc_date_time."'";
		}
		else
		{
			
			
			/*if(str_replace("'","",$cbo_basis)==5 || str_replace("'","",$cbo_basis)==9 || str_replace("'","",$cbo_basis)==10)
			{
				$adj_allocated_qnty=$before_allocated_qnty;
				$adj_available_qnty=$before_available_qnty-$beforeReturnQnty;
				
				$presentallocatedQnty = $allocated_qnty;
				$presentAvailableQnty = $available_qnty+$txt_return_qnty;
			}
			elseif(str_replace("'","",$cbo_basis)==6)
			{
				$adj_allocated_qnty=$before_allocated_qnty-$beforeReturnQnty;
				$adj_available_qnty=$before_available_qnty;
				
				$presentallocatedQnty = $allocated_qnty+$txt_return_qnty;
				$presentAvailableQnty = $available_qnty;
			}
			else
			{
				$adj_allocated_qnty=$before_allocated_qnty;
				$adj_available_qnty=$before_available_qnty-$beforeReturnQnty;
				$presentallocatedQnty = $allocated_qnty;
				$presentAvailableQnty = $available_qnty+$txt_return_qnty;
			}*/
			
			$adj_allocated_qnty=$before_allocated_qnty;
			$adj_available_qnty=$before_available_qnty-$beforeReturnQnty;
			
			$presentallocatedQnty = $allocated_qnty;
			$presentAvailableQnty = $available_qnty+$txt_return_qnty;
			
			//before
			$presentStockQnty   = $currentStockQnty-$txt_return_qnty; //current qnty - before qnty
			$presentStockValue  = $currentStockValue-$txt_return_value;

			$avgRate			= number_format($presentStockValue/$presentStockQnty,$dec_place[3],".","");
			$update_data[$before_prod_id]=explode("*",("".$presentStockQnty."*".$presentStockValue."*".$adj_allocated_qnty."*".$adj_available_qnty."*'".$user_id."'*'".$pc_date_time."'"));
			$updateID_array[]=$before_prod_id;
			//current
			$presentStockQnty   = $presentStock+$txt_return_qnty; //current qnty - before qnty + present return qnty
			$presentStockValue  = $presentStockValue+$txt_return_value;

			$avgRate			= number_format($presentStockValue/$presentStockQnty,$dec_place[3],".","");
			$update_data[$txt_prod_id]=explode("*",("".$presentStockQnty."*".$presentStockValue."*".$presentallocatedQnty."*".$presentAvailableQnty."*'".$user_id."'*'".$pc_date_time."'"));
			$updateID_array[]=$txt_prod_id;
		}
		

		$field_array_upd="receive_date*booking_id*booking_no*knitting_source*knitting_company*challan_no*location_id*exchange_rate*currency_id*supplier_id*issue_id*remarks*updated_by*update_date";
		$data_array_upd=$txt_return_date."*".$txt_booking_id."*".$txt_booking_no."*".$cbo_knitting_source."*".$cbo_knitting_company."*".$txt_return_challan_no."*".$cbo_location."*1*1*".$txt_supplier_id."*".$txt_issue_id."*".$txt_remarks."*'".$user_id."'*'".$pc_date_time."'";
		//yarn receive master table entry here END---------------------------------------//

		/******** original product id check start ********/

		//$origin_prod_id=return_field_value("origin_prod_id","inv_transaction","prod_id=$txt_prod_id and status_active=1","origin_prod_id");
		$origin_prod_id=return_field_value("origin_prod_id","inv_transaction","prod_id=$txt_prod_id and status_active=1 and mst_id=$txt_issue_id and transaction_type in (2) and item_category=1","origin_prod_id");
		/******** original product id check end ********/
		//transaction table update here START--------------------------------//
 		//$transaction_type=array(1=>"Receive",2=>"Issue",3=>"Receive Return",4=>"Issue Return");
		$field_array_trans= "receive_basis*company_id*supplier_id*prod_id*origin_prod_id*item_category*transaction_type*transaction_date*store_id*floor_id*room*rack*self*order_uom*order_qnty*order_rate*order_amount*cons_uom*cons_quantity*cons_reject_qnty*cons_rate*cons_amount*balance_qnty*balance_amount*issue_challan_no*issue_id*remarks*updated_by*update_date*job_no*style_ref_no*buyer_id*pi_wo_batch_no*booking_no";
		$data_array_trans= "".$cbo_basis."*".$cbo_company_id."*".$txt_supplier_id."*".$txt_prod_id."*'".$origin_prod_id."'*1*4*".$txt_return_date."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_uom."*".$txt_return_qnty."*".$txt_rate."*".$txt_amount."*".$cbo_uom."*".$txt_return_qnty."*".$txt_reject_qnty."*".$txt_rate."*".$txt_amount."*".$txt_return_qnty."*".$txt_amount."*".$txt_issue_challan_no."*".$txt_issue_id."*".$txt_remarks."*'".$user_id."'*'".$pc_date_time."'*".$txt_job_no."*".$txt_style_no."*".$cbo_buyer_name."*".$txt_booking_id."*".$txt_booking_no;
 		//transaction table update here END ---------------------------------//

		//order_wise_pro_details table data insert END -----//
		$id=str_replace("'","",$txt_mst_id);
		$prodUpdate=$rID=$transID =true;
		if(str_replace("'","",$txt_return_qnty)>0)
		{
			if(str_replace("'","",$txt_prod_id) == str_replace("'","",$before_prod_id))
			{
				$prodUpdate = sql_update("product_details_master",$update_array,$data_array,"id",$txt_prod_id,1);
			}
			else
			{
				$prodUpdate=execute_query(bulk_update_sql_statement("product_details_master","id",$update_array,$update_data,$updateID_array));
			}
		}
		
		$rID=sql_update("inv_receive_master",$field_array_upd,$data_array_upd,"id",$id,1);
		$transID = sql_update("inv_transaction",$field_array_trans,$data_array_trans,"id",$update_id,1);

		//echo "10**".$prodUpdate." && ".$rID." && ".$transID;die;

		if($db_type==0)
		{
			if($prodUpdate && $rID && $transID)
			{
				mysql_query("COMMIT");
				echo "1**".$id."**".str_replace("'","",$txt_return_no);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_return_no);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($prodUpdate && $rID && $transID)
			{
				oci_commit($con);
				echo "1**".$id."**".str_replace("'","",$txt_return_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_return_no);
			}
		}

		disconnect($con);
		die;
	}
	
	else if ($operation==2) // Delete Here----------------------------------------------------------
	{
		/*$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		$mrr_data=sql_select("select a.id, a.is_posted_account, a.receive_purpose, a.receive_basis, b.cons_quantity, b.cons_rate, b.cons_amount, c.id as prod_id, c.current_stock, c.stock_value, c.allocated_qnty, c.available_qnty from inv_receive_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and b.item_category=1 and b.transaction_type=4 and a.status_active=1 and b.status_active=1 and b.id=$update_id");
		$master_id=$mrr_data[0][csf("id")];
		$is_posted_account=$mrr_data[0][csf("is_posted_account")]*1;
		$receive_purpose=$mrr_data[0][csf("receive_purpose")];
		$receive_basis=$mrr_data[0][csf("receive_basis")];
		$cons_quantity=$mrr_data[0][csf("cons_quantity")];
		$cons_rate=$mrr_data[0][csf("cons_rate")];
		$cons_amount=$mrr_data[0][csf("cons_amount")];
		$prod_id=$mrr_data[0][csf("prod_id")];
		$current_stock=$mrr_data[0][csf("current_stock")];
		$stock_value=$mrr_data[0][csf("stock_value")];
		$allocated_qnty=$mrr_data[0][csf("allocated_qnty")];
		$available_qnty=$mrr_data[0][csf("available_qnty")];

		$cu_current_stock=$current_stock-$cons_quantity;
		$cu_stock_value=$stock_value-$cons_amount;
		if($cu_stock_value>0 && $cu_current_stock>0) $cu_avg_rate=$cu_stock_value/$cu_current_stock; else $cu_avg_rate=0;

		$issue_row = sql_select("select issue_purpose,issue_basis,entry_form from inv_issue_master where id=$txt_issue_id");
		$issue_purpose = $issue_row[0][csf('issue_purpose')];
		$issue_basis = $issue_row[0][csf('issue_basis')];
		$entry_form = $issue_row[0]['entry_form'];
		$cbo_adjust_to = str_replace("'","",$cbo_adjust_to);
		if ($variable_set_allocation == 1)
		{
			if(($issue_basis==3 || $issue_basis==8) && ($issue_purpose==1 || $issue_purpose==4))
			{
				$plan_sql="select a.booking_no,c.job_no,a.is_sales from ppl_yarn_requisition_entry b,ppl_planning_entry_plan_dtls a,wo_booking_dtls c where b.knit_id=a.dtls_id and a.booking_no=c.booking_no and a.status_active=1 and b.status_active=1 and b.requisition_no=$txt_booking_no and b.prod_id=$txt_prod_id group by a.booking_no,c.job_no,a.is_sales";
				$planData = sql_select($plan_sql);

				$job_no = $planData[0][csf("job_no")];
				$booking_no = $planData[0][csf("booking_no")];

				$is_sales_order=sql_select("select b.is_sales from ppl_yarn_requisition_entry a,ppl_planning_info_entry_dtls b where a.knit_id=b.id and a.prod_id=$txt_prod_id and a.requisition_no=$txt_booking_no");
				if($planData[0][csf("is_sales")]==1){
					$cu_allocated_qnty=$allocated_qnty;
					$cu_available_qnty=$available_qnty-$cons_quantity;
				}else{
					if($cbo_adjust_to != 2){
						$cu_allocated_qnty=$allocated_qnty-$cons_quantity;
						$cu_available_qnty=$available_qnty;
					}else{
						$cu_allocated_qnty=$allocated_qnty;
						$cu_available_qnty=$available_qnty-$cons_quantity;
					}
				}

			} else if($issue_basis==1 && ($issue_purpose==2 || $issue_purpose==15 || $issue_purpose==38 || $issue_purpose==46 || $issue_purpose==7)){
				$is_sales_order=sql_select("select a.is_sales,a.booking_without_order, b.job_no from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.ydw_no=$txt_booking_no and b.product_id=$txt_prod_id and a.company_id=$cbo_company_id");
				$job_no 	= $is_sales_order[0][csf("job_no")];
				$booking_no = '';
				if($entry_form == 42 || $entry_form == 135){
					$cu_allocated_qnty=$allocated_qnty;
					$cu_available_qnty=$available_qnty-$cons_quantity;
				}else{
					if($is_sales_order[0][csf("is_sales")]==1){
						$cu_allocated_qnty=$allocated_qnty;
						$cu_available_qnty=$available_qnty-$cons_quantity;
					}else{
						if($cbo_adjust_to != 2){
							$cu_allocated_qnty=$allocated_qnty-$cons_quantity;
							$cu_available_qnty=$available_qnty;
						}else{
							$cu_allocated_qnty=$allocated_qnty;
							$cu_available_qnty=$available_qnty-$cons_quantity;
						}
					}
				}
			}
			else
			{
				$cu_allocated_qnty=$allocated_qnty;
				$cu_available_qnty=$available_qnty-$cons_quantity;
			}

			if(str_replace("'","",$save_data)!="")
			{
				$save_string=explode(",",str_replace("'","",$save_data));
				$save_string_pre=explode(",",str_replace("'","",$save_data_pre));
				$po_array=array();
				for($i=0;$i<count($save_string);$i++)
				{
					$order_dtls=explode("**",$save_string[$i]);
					$order_id=$order_dtls[0];
					$order_qnty=$order_dtls[1];
					$po_array[$order_id]=$order_qnty;
					$po_id = $order_id . ",";
				}

				$pre_po_array=array();
				for($j=0;$j<count($save_string_pre);$j++)
				{
					$order_dtls=explode("**",$save_string_pre[$j]);
					$order_id=$order_dtls[0];
					$order_qnty=$order_dtls[1];
					$pre_po_array[$order_id]=$order_qnty;
				}
				$po_id = rtrim($po_id,", ");
				$booking_cond = ($booking_no!="")?" and a.booking_no='$booking_no' ":"";
				$sql_allocation = "select * from inv_material_allocation_mst a where a.po_break_down_id='$po_id' and a.item_id=" . str_replace("'", "", $txt_prod_id) . " and a.job_no='$job_no' $booking_cond and a.status_active=1 and a.is_deleted=0";
				$check_allocation_array = sql_select($sql_allocation);

				if(str_replace("'","",$cbo_adjust_to) == 2){

					// if allocation found
					if (!empty($check_allocation_array)) {

						$mst_id = $check_allocation_array[0][csf('id')];
						$qnty_break_down_str = explode(",",$check_allocation_array[0][csf('qnty_break_down')]);

						foreach($po_array as $key=>$val)
						{
							$allo_qnty = $val;
							execute_query("update inv_material_allocation_dtls set qnty=(qnty+$allo_qnty),updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where mst_id=$mst_id and job_no='$job_no' and po_break_down_id=$key and item_id = $txt_prod_id", 0);
							$po_wise_allocation[$key] = $val;
						}

						$qnty_break_down_str_new="";
						foreach ($qnty_break_down_str as $qnty_break_down_row) {
							$qnty_break_down_row_info = explode("_", $qnty_break_down_row);
							$allo_qnty = $po_wise_allocation[$qnty_break_down_row_info[1]];
							$qnty_break_down_str_new = ($qnty_break_down_row_info[0] + $allo_qnty) . "_" . $qnty_break_down_row_info[1] . "_" . $qnty_break_down_row_info[2].",";
							$mst_qnty += $allo_qnty;
						}
						$qnty_break_down_str_new=rtrim($qnty_break_down_str_new,", ");
						execute_query("update inv_material_allocation_mst set qnty=(qnty+$mst_qnty),qnty_break_down='$qnty_break_down_str_new',updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where id=$mst_id", 0);
					}
				}
			}
		}
		else
		{
			$cu_allocated_qnty=$allocated_qnty;
			$cu_available_qnty=$available_qnty-$cons_quantity;
		}

		if($is_posted_account>0)
		{
			echo "13**Delete restricted, This Information is used in another Table."; disconnect($con); oci_rollback($con); die;
		}

		$next_operation=return_field_value("max(id) as max_trans_id", "inv_transaction", "status_active=1 and item_category=1 and transaction_type<>4 and prod_id=$prod_id", "max_trans_id");
		if($next_operation)
		{
			if($next_operation>str_replace("'","",$update_id))
			{
				echo "13**Delete restricted, This Information is used in another Table."; disconnect($con); oci_rollback($con); die;
			}
		}

		$field_array = "updated_by*update_date*status_active*is_deleted";
		$data_array = "'" . $user_id . "'*'" . $pc_date_time . "'*0*1";
		$field_array_prod = "current_stock*avg_rate_per_unit*stock_value*allocated_qnty*available_qnty*updated_by*update_date";
		$data_array_prod = "".$cu_current_stock."*".$cu_avg_rate."*".$cu_stock_value."*'".$cu_allocated_qnty."'*'".$cu_available_qnty."'*'".$user_id."'*'".$pc_date_time."'";

		//$rID=sql_update("inv_receive_master",$field_array,$data_array,"recv_number","$txt_return_no",1);
		$rID=1;
		$rIDTr = sql_update("inv_transaction", $field_array, $data_array, "id", "$update_id", 1);
		$rIDProp = sql_update("order_wise_pro_details", $field_array, $data_array, "trans_id", "$update_id", 1);
		$rIDprodID = sql_update("product_details_master", $field_array_prod, $data_array_prod, "id", "$prod_id", 1);

		//echo "10**".$rID."*".$rIDTr."*".$rIDProp."*".$rIDprodID; die;
		if ($db_type == 0) {
			if ($rID && $rIDTr && $rIDProp && $rIDprodID) {
				mysql_query("COMMIT");
				echo "2**" . str_replace("'", "", $txt_mrr_no);
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . str_replace("'", "", $txt_mrr_no);
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $rIDTr && $rIDProp && $rIDprodID) {
				oci_commit($con);
				echo "2**" . str_replace("'", "", $txt_mrr_no);
			} else {
				oci_rollback($con);
				echo "10**" . str_replace("'", "", $txt_mrr_no);
			}
		}
		disconnect($con);
		die;*/
	}
}

if($action=="return_number_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>

	<script>
		function js_set_value(mrr)
		{
			$("#hidden_return_number").val(mrr);
			parent.emailwindow.hide();
		}

		/*function popup_print()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			$('#list_view tbody tr:first').hide();
			document.getElementById('list_container_batch').style.overflow="auto";
			document.getElementById('list_container_batch').style.maxHeight="none";

			d.write(document.getElementById('popup_data').innerHTML);

			document.getElementById('list_container_batch').style.overflowY="scroll";
			document.getElementById('list_container_batch').style.maxHeight="240px";

			$('#list_view tbody tr:first').show();
			d.close();
		}*/
	</script>
</head>
<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			<table width="780" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
				<thead>
					<tr>
						<th width="170">Search By</th>
						<th width="200" align="center" id="search_by_td_up">Enter Return Number</th>
						<th width="220">Date Range</th>
						<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>
					</tr>
				</thead>
				<tbody>
					<tr class="general">
						<td>
							<?
							$search_by = array(1=>'Return Number',2=>'Return Challan');
							$dd="change_search_event(this.value, '0*0', '0*0', '../../../') ";
							echo create_drop_down( "cbo_search_by", 130, $search_by,"",0, "--Select--", "",$dd,0 );
							?>
						</td>
						<td width="" align="center" id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
						<td align="center">
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" />
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" />
						</td>
						<td align="center">
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>, 'create_return_search_list_view', 'search_div', 'yarn_issue_return_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td align="center" height="40" valign="middle" colspan="5">
							<? echo load_month_buttons(1);  ?>
							<!-- Hidden field here-->
							<input type="hidden" id="hidden_return_number" value="" />
							<input type="hidden" id="hidden_posted_in_account" value="" />
							<!--END-->
						</td>
					</tr>
				</tbody>
			</tr>
		</table>
		<div style="margin-top:5px" align="center" valign="top" id="search_div"> </div>
	</form>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_return_search_list_view")
{
	$ex_data = explode("_",$data);
	$search_by = $ex_data[0];
	$search_common = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];

	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$sql_cond="";
	if($search_by==1)
	{
		if($search_common!="") $sql_cond .= " and recv_number like '%$search_common'";
	}
	else if($search_by==2)
	{
		if($search_common!="") $sql_cond .= " and challan_no='$search_common'";
	}
	if( $txt_date_from!="" && $txt_date_to!="" )
	{
		if($db_type==0)
		{
			$sql_cond .= " and a.receive_date  between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
		}
		else
		{
			$sql_cond .= " and a.receive_date  between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
		}
	}

	if($company!="") $sql_cond .= " and a.company_id='$company'";

	if($db_type==0) $year_field="YEAR(a.insert_date) as year,";
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year,";
	else $year_field="";

	$sql = "select a.id as mst_id, a.recv_number_prefix_num,a.challan_no, a.recv_number, a.company_id, a.supplier_id, a.receive_date, a.item_category, a.recv_number, a.knitting_source, a.knitting_company, $year_field b.id, b.cons_quantity, b.cons_reject_qnty, b.cons_uom, b.cons_rate, b.cons_amount, c.product_name_details,c.yarn_count_id,c.yarn_comp_type1st,c.yarn_type,c.color,c.yarn_comp_percent1st, c.id as prod_id, c.lot, a.is_posted_account
	from inv_receive_master a, inv_transaction b left join product_details_master c on b.prod_id=c.id
	where a.id=b.mst_id and b.item_category=1 and b.transaction_type=4 and a.entry_form=382 and b.entry_form=382 $sql_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
	order by a.id desc";
	?>
	<div id="popup_data" style=" width:1010px;">
		<table class="rpt_table" border="1" rules="all" cellpadding="0" cellspacing="0" width="1010">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="50">Return No</th>
					<th width="150">Knitting Company</th>
					<th width="70">Ret Challan</th>
					<th width="70">Date</th>
					<th width="40">Year</th>
					<th width="130">Supplier Name</th>
					<th width="60">Lot No</th>
					<th width="170">Item Description</th>
					<th width="80">Return Qnty</th>
					<th width="80">Rejected Qnty</th>
					<th >UOM</th>
				</tr>
			</thead>
		</table>
		<div style="width:1010px; max-height:240px; overflow-y:scroll" id="list_container_batch">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="990" class="rpt_table" id="list_view">
				<tbody>
					<?
					$supplier_arr=return_library_array("select id, supplier_name from lib_supplier",'id','supplier_name');
					$sql_result=sql_select($sql);
					$i=1;
					foreach($sql_result as $row)
					{
						if($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						if($row[csf("knitting_source")]==1) $kint_com=$company_library[$row[csf("knitting_company")]]; else $kint_com=$supplier_library[$row[csf("knitting_company")]];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row[csf("mst_id")]; ?>')" style="cursor:pointer;">
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="50" align="center" style="word-break:break-all"><p><? echo $row[csf("recv_number_prefix_num")]; ?>&nbsp;</p></td>
							<td width="150" style="word-break:break-all"><p><? echo $kint_com; ?>&nbsp;</p></td>
							<td width="70" align="center" style="word-break:break-all"><p><? echo $row[csf("challan_no")]; ?>&nbsp;</p></td>
							<td width="70" align="center" style="word-break:break-all"><? if($row[csf("receive_date")]!="" && $row[csf("receive_date")]!="0000-00-00") echo change_date_format($row[csf("receive_date")]);?></td>
							<td width="40" align="center" style="word-break:break-all"> <p><? echo $row[csf("year")]; ?>&nbsp;</p></td>
							<td width="130" style="word-break:break-all"><p><? echo $supplier_arr[$row[csf("supplier_id")]]; ?>&nbsp;</p></td>
							<td width="60" align="center" style="word-break:break-all"><p><? echo $row[csf("lot")]; ?>&nbsp;</p></td>
							<td width="170" style="word-break:break-all">
								<p>
									<?
									echo $count_arr[$row[csf('yarn_count_id')]]." ".$composition[$row[csf('yarn_comp_type1st')]]." ".$row[csf('yarn_comp_percent1st')]."% ".$yarn_type[$row[csf('yarn_type')]]." ".$color_arr[$row[csf('color')]];
									?>
								</p>
							</td>
							<td width="80" align="right"><? echo number_format($row[csf("cons_quantity")],2); ?></td>
							<td width="80" align="right"><? echo number_format($row[csf("cons_reject_qnty")],2); ?></td>
							<td align="center"><? echo $unit_of_measurement[$row[csf("cons_uom")]];?></td>
						</tr>
						<?
						$i++;
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
	<!--<div style="margin-top:5px" align="center" valign="top"><input type="button" id="btn_print" class="formbutton" style="width:100px;" value="Print" onClick="popup_print()"/> </div>-->
	<?
	exit();
}


if($action=="populate_master_from_data")
{
	$sql = "select id,recv_number,entry_form,item_category,company_id,receive_basis,receive_purpose,receive_date,booking_id,booking_no,knitting_source,knitting_company,yarn_issue_challan_no,challan_no,store_id,location_id,buyer_id,exchange_rate,currency_id,supplier_id,lc_no,source,requisition_no,issue_id from inv_receive_master where id='$data'";

	$res = sql_select($sql);
	
	$issue_id = $res[0][csf('issue_id')];

	$issue_buyer_id_arr=return_library_array("select mst_id, buyer_id from inv_transaction where mst_id=$issue_id and transaction_type=2 and entry_form=277",'mst_id','buyer_id');
	$buyer_arr 	= return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$issue_no=return_field_value("issue_number","inv_issue_master","id=$issue_id","issue_number");

	foreach($res as $row)
	{
		echo "set_button_status(0, permission, 'fnc_yarn_issue_return_entry',1,1);";
		echo "$('#txt_mst_id').val('".$row[csf("id")]."');\n";
		echo "$('#txt_return_no').val('".$row[csf("recv_number")]."');\n";
		echo "$('#cbo_company_id').val(".$row[csf("company_id")].");\n";
		echo "$('#cbo_basis').val('".$row[csf("receive_basis")]."');\n";
		//echo "active_inactive('".$row[csf("receive_basis")]."');\n";
		echo "$('#txt_booking_no').val('".$row[csf("booking_no")]."');\n";
		echo "$('#txt_booking_id').val('".$row[csf("booking_id")]."');\n";
		echo "$('#txt_issue_id').val('".$issue_id."');\n";
		echo "$('#txt_issue_challan_no').val('".$issue_no."');\n";
		echo "$('#cbo_location').val('".$row[csf("location_id")]."');\n";
		echo "$('#cbo_knitting_source').val('".$row[csf("knitting_source")]."');\n";
		echo "$('#cbo_knitting_source').attr('disabled','disabled');\n";
		echo "load_drop_down( 'requires/yarn_issue_return_controller', ".$row[csf("knitting_source")]."+'**'+".$row[csf("company_id")].", 'load_drop_down_knit_com', 'knitting_company_td' );\n";
		echo "$('#cbo_knitting_company').val('".$row[csf("knitting_company")]."');\n";
		echo "$('#cbo_knitting_company').attr('disabled','disabled');\n";
		echo "$('#txt_return_date').val('".change_date_format($row[csf("receive_date")])."');\n";
		echo "$('#txt_return_challan_no').val('".$row[csf("challan_no")]."');\n";
		echo "disable_enable_fields( 'cbo_company_id*cbo_basis*txt_booking_no', 1, '', '' );\n"; // disable true
		$issueBuyerid = $issue_buyer_id_arr[$row[csf("issue_id")]];
		echo "$('#txt_buyer_name').val('".$buyer_arr[$issueBuyerid]."');\n";
	}
	exit();
}

if($action=="show_dtls_list_view")
{
	$count_arr = return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');

	$sql = "select a.recv_number, a.company_id, a.supplier_id, a.receive_date, a.item_category, b.id, b.cons_quantity, b.cons_reject_qnty, b.cons_uom, b.cons_rate, b.cons_amount, b.job_no, b.style_ref_no, b.booking_no, c.id as prod_id, c.product_name_details, c.yarn_comp_type1st, c.yarn_count_id, c.yarn_type, c.color, c.lot, c.unit_of_measure
	from inv_receive_master a, inv_transaction b, product_details_master c
	where a.id=b.mst_id and b.prod_id=c.id and b.item_category=1 and b.transaction_type=4 and b.entry_form=382 and a.status_active=1 and b.status_active=1 and a.id=$data";

	$result = sql_select($sql);
	$i=1;$rettotalQnty=0;$rcvtotalQnty=0;$rejtotalQnty=0;$totalAmount=0;
	?>
	<table class="rpt_table" border="1" cellpadding="2" cellspacing="0" width="1000" rules="all">
		<thead>
			<tr>
				<th width="30">SL</th>
                <th width="100">Job/Booking/lot</th>
                <th width="100">Style</th>
                <th width="100">Job</th>
                <th width="80">Lot</th>
                <th width="80">Yarn Count</th>
                <th width="120">Composition</th>
                <th width="80">Yarn Type</th>
                <th width="80">Color</th>
                <th width="60">UOM</th>
				<th width="80">Return Qty</th>
				<th>Reject Qty</th>
			</tr>
		</thead>
		<tbody>
			<?
			foreach($result as $row)
			{
				if($i%2==0)
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";

				$description = $count_arr[$row[csf('yarn_count_id')]]." ".$composition[$row[csf('yarn_comp_type1st')]]." ".$row[csf('yarn_comp_percent1st')]."% ".$yarn_type[$row[csf('yarn_type')]]." ".$color_arr[$row[csf('color')]];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $row[csf("id")]."**".$row[csf("prod_id")];?>","child_form_input_data","requires/yarn_issue_return_controller")' style="cursor:pointer" >
					<td><? echo $i; ?></td>
                    <td><p><? echo $row[csf("booking_no")]; ?>&nbsp;</p></td>
					<td><p><? echo $row[csf("style_ref_no")]; ?>&nbsp;</p></td>
					<td><p><? echo $row[csf("job_no")]; ?>&nbsp;</p></td>
                    <td><p><? echo $row[csf("lot")]; ?>&nbsp;</p></td>
					<td><p><? echo $count_arr[$row[csf("yarn_count_id")]]; ?>&nbsp;</p></td>
					<td><p><? echo $composition[$row[csf("yarn_comp_type1st")]]; ?>&nbsp;</p></td>
                    <td><p><? echo $yarn_type[$row[csf("yarn_type")]]; ?>&nbsp;</p></td>
					<td><p><? echo $color_arr[$row[csf("color")]]; ?>&nbsp;</p></td>
                    <td><p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></p></td>
					<td align="right"><? echo number_format($row[csf("cons_quantity")], 2, '.', ''); ?></td>
					<td align="right"><? echo number_format($row[csf("cons_reject_qnty")], 2, '.', ''); ?></td>
				</tr>
				<? 
				$i++;
				$rettotalQnty +=$row[csf("cons_quantity")];
				$rejtotalQnty +=$row[csf("cons_reject_qnty")];
				$totalAmount +=$row[csf("cons_amount")]; 
			} ?>
			<tfoot>
				<th colspan="10">Total</th>
				<th><? echo number_format($rettotalQnty, 2, '.', ''); ?></th>
				<th><? echo number_format($rejtotalQnty, 2, '.', ''); ?></th>
			</tfoot>
		</tbody>
    </table>
    <?
    exit();
}

if($action=="child_form_input_data")
{
	$data=explode('**',$data);
	$id = $data[0];
	$description = $data[1];
	$sql = "select b.id as prod_id,a.company_id, b.product_name_details, b.lot, a.id as tr_id, a.store_id, a.floor_id, a.room, a.rack,a.self, a.issue_id, a.cons_uom, a.cons_rate, a.cons_quantity,a.cons_reject_qnty, a.cons_amount, a.issue_challan_no,a.remarks, a.job_no,a.style_ref_no,a.buyer_id, b.supplier_id, a.weight_editable, a.fabric_ref
	from inv_transaction a, product_details_master b
	where a.id=$id and a.status_active=1 and a.item_category=1 and transaction_type=4 and a.prod_id=b.id and b.status_active=1";
	
	$result = sql_select($sql);

	foreach($result as $row)
	{
		$issueids = $row[csf("issue_id")];
		$prod_ids = $row[csf("prod_id")];
	}
	
	if($issueids!="")
	{
		$issue_sql = sql_select("select a.id,a.issue_purpose,a.issue_basis,a.issue_date,a.booking_no,b.requisition_no,receive_basis,b.pi_wo_batch_no,b.prod_id,b.cons_quantity,b.return_qnty from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.id in($issueids) and b.prod_id in($prod_ids) and a.item_category=1 and b.item_category=1 and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

		$issueQtyArr = array();
		$issueReturnAbleQtyArr = array();
		$issueData = array();
		foreach($issue_sql as $row)
		{
			$issueData[$row[csf("id")]]['issue_purpose'] = $row[csf("issue_purpose")];
			$issueData[$row[csf("id")]]['issue_basis'] = $row[csf("issue_basis")];
			$issueData[$row[csf("id")]]['booking_no'] = $row[csf("booking_no")];
			$issueReturnAbleQtyArr[$row[csf("id")]][$row[csf("prod_id")]] += $row[csf("return_qnty")];
			$issueQtyArr[$row[csf("id")]][$row[csf("prod_id")]] += $row[csf("cons_quantity")];	
		}

		$issue_retur_sql = sql_select("select b.issue_id,b.prod_id, sum(b.cons_quantity) as cons_quantity,sum(b.cons_reject_qnty) as cons_reject_qnty from inv_transaction b where b.issue_id in($issueids) and b.prod_id in($prod_ids) and b.item_category=1 and b.transaction_type=4 and b.is_deleted=0 group by b.issue_id,b.prod_id");

		$issueReturnQtyArr = array();
		foreach($issue_retur_sql as $row)
		{
			$issueReturnQtyArr[$row[csf("issue_id")]][$row[csf("prod_id")]] = ($row[csf("cons_quantity")]+$row[csf("cons_reject_qnty")]);
		}	

	}		

	foreach($result as $row)
	{
		/*$issData=sql_select("select issue_purpose,issue_basis,issue_date,booking_no from inv_issue_master where id='".$row[csf("issue_id")]."'");
		$issue_purpose=$issData[0][csf('issue_purpose')];
		$issue_basis=$issData[0][csf('issue_basis')];
		$booking_no=$issData[0][csf('booking_no')];
		*/
		$issue_date = $issueData[$row[csf("issue_id")]]['issue_date'];
		$issue_purpose = $issueData[$row[csf("issue_id")]]['issue_purpose'];
		$issue_basis = $issueData[$row[csf("issue_id")]]['issue_basis'];
		
		//echo "return_qnty_basis(".$issue_purpose.");\n";
		echo "$('#hide_issue_date').val('".change_date_format($issue_date)."');\n";
		echo "$('#txt_item_description').val('".$description."');\n";
		echo "$('#txt_prod_id').val('".$row[csf("prod_id")]."');\n";
		echo "$('#txt_supplier_id').val('".$row[csf("supplier_id")]."');\n";
		echo "$('#before_prod_id').val('".$row[csf("prod_id")]."');\n";
		echo "$('#txt_yarn_lot').val('".$row[csf("lot")]."');\n";
		echo "$('#txt_job_no').val('".$row[csf("job_no")]."');\n";
		echo "$('#txt_style_no').val('".$row[csf("style_ref_no")]."');\n";
		echo "$('#cbo_buyer_name').val('".$row[csf("buyer_id")]."');\n";
		echo "load_room_rack_self_bin('requires/yarn_issue_return_controller*1', 'store','store_td', '".$row[csf('company_id')]."','"."',this.value);\n";
		echo "$('#cbo_store_name').val('".$row[csf("store_id")]."');\n";
		echo "load_room_rack_self_bin('requires/yarn_issue_return_controller', 'floor','floor_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."',this.value);\n";
		echo "$('#cbo_floor').val('".$row[csf("floor_id")]."');\n";
		if($row[csf('floor_id')])
		{
			echo "load_room_rack_self_bin('requires/yarn_issue_return_controller', 'room','room_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."',this.value);\n";
		}		
		echo "$('#cbo_room').val('".$row[csf("room")]."');\n";
		if($row[csf('room')])
		{
			echo "load_room_rack_self_bin('requires/yarn_issue_return_controller', 'rack','rack_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."',this.value);\n";
		}		
		echo "$('#txt_rack').val('".$row[csf("rack")]."');\n";
		if($row[csf('rack')])
		{
			echo "load_room_rack_self_bin('requires/yarn_issue_return_controller', 'shelf','shelf_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."',this.value);\n";
		}		
		echo "$('#txt_shelf').val('".$row[csf("self")]."');\n";
		echo "$('#txt_return_qnty').val('".$row[csf("cons_quantity")]."');\n";
		echo "$('#txt_reject_qnty').val('".$row[csf("cons_reject_qnty")]."');\n";
		echo "$('#txt_remarks').val('".$row[csf("remarks")]."');\n";
		echo "$('#cbo_uom').val('".$row[csf("cons_uom")]."');\n";
		echo "$('#txt_issue_id').val('".$row[csf("issue_id")]."');\n";
		
		echo "$('#txt_westage_qnty').val('".$row[csf("weight_editable")]."');\n";
		echo "$('#txt_westage_dtls').val('".$row[csf("fabric_ref")]."');\n";
		
		$totalIssued = $issueQtyArr[$row[csf("issue_id")]][$row[csf("prod_id")]];

		if($totalIssued=="") $totalIssued=0;
		echo "$('#txt_issue_qnty').val('".$totalIssued."');\n";

		/* 
		$totalIssued = return_field_value("sum(b.cons_quantity)","inv_issue_master a, inv_transaction b"," a.id=b.mst_id and a.id='".$row[csf("issue_id")]."' and b.prod_id='".$row[csf("prod_id")]."' and b.item_category=1 and b.transaction_type=2");

		$totalReturn_sql = sql_select("select sum(cons_quantity) as cons_quantity,sum(cons_reject_qnty) as cons_reject_qnty from inv_transaction where issue_id='".$row[csf("issue_id")]."' and prod_id='".$row[csf("prod_id")]."' and item_category=1 and transaction_type=4 and status_active=1");
		
		$totalReturn=0;

		foreach($totalReturn_sql as $row_return)
		{
			$totalReturn+=$row_return[csf("cons_quantity")]+$row_return[csf("cons_reject_qnty")];
		}
		$totalreturnable = return_field_value("sum(b.return_qnty)","inv_issue_master a, inv_transaction b"," a.id=b.mst_id and a.id='".$row[csf("issue_id")]."' and b.prod_id='".$row[csf("prod_id")]."' and b.item_category=1 and b.transaction_type=2");
		*/
		
		$totalReturn = $issueReturnQtyArr[$row[csf("issue_id")]][$row[csf("prod_id")]];
		echo "$('#txt_total_return_display').val('".$totalReturn."');\n";
		$netUsed = $totalIssued-$totalReturn;
		echo "$('#txt_net_used').val('".$netUsed."');\n";
		echo "$('#hide_net_used').val('".$row[csf("cons_quantity")]."');\n";
		$totalreturnable = $issueReturnAbleQtyArr[$row[csf("issue_id")]][$row[csf("prod_id")]];
		echo "$('#txt_returnable_qnty').val('".$totalreturnable."');\n";
		$returnableBl = $totalIssued-$totalReturn;
		echo "$('#txt_returnable_bl_qnty').val('".$returnableBl."');\n";
		if($totalReturn=="") $totalReturn=0; else $totalReturn=$totalReturn-$row[csf("cons_quantity")]-$row[csf("cons_reject_qnty")];
		echo "$('#txt_total_return').val('".$totalReturn."');\n";
		echo "$('#txt_rate').val('".$row[csf("cons_rate")]."');\n";
		echo "$('#txt_amount').val(".$row[csf("cons_amount")].");\n";
		echo "$('#txt_issue_challan_no').val('".$row[csf("issue_challan_no")]."');\n";
		echo "$('#update_id').val('".$row[csf("tr_id")]."');\n";
		//issue qnty popup data arrange
	}
	echo "set_button_status(1, permission, 'fnc_yarn_issue_return_entry',1,1);\n";
	exit();
}

if ($action=="yarn_issue_return_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	// echo "<pre>";
	// print_r($data);
	$mst_id=$data[0];
	//Wecho $mst_id."*".$data;die;
	
	$company_library 	= return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library 	= return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$store_library 		= return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$buyer_arr 			= return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$count_arr 			= return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$color_arr 			= return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');

	$sql="select a.id as mst_id, a.company_id, a.location_id, a.recv_number, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.challan_no, a.receive_date, a.receive_basis, a.booking_no, a.issue_id, b.job_no, b.style_ref_no, b.buyer_id, c.id as prod_id, c.product_name_details, c.yarn_comp_type1st, c.yarn_count_id, c.yarn_type, c.color, c.lot, c.unit_of_measure, b.id as dtls_id, b.store_id, b.cons_quantity, b.cons_reject_qnty, b.remarks
    from inv_receive_master a, inv_transaction b, product_details_master c 
    where a.id=b.mst_id and b.prod_id=c.id and b.status_active=1 and b.is_deleted=0 and a.entry_form=382 and b.entry_form=382 and b.transaction_type=4 and a.id=$mst_id";
	/*<td><p><? echo $count_arr[$row[csf("yarn_count_id")]]; ?>&nbsp;</p></td>
					<td><p><? echo $composition[$row[csf("yarn_comp_type1st")]]; ?>&nbsp;</p></td>
                    <td><p><? echo $yarn_type[$row[csf("yarn_type")]]; ?>&nbsp;</p></td>
					<td><p><? echo $color_arr[$row[csf("color")]]; ?>&nbsp;</p></td>*/
	$sql_result=sql_select($sql);
	$issue_id=$sql_result[0][csf("issue_id")];
	$issue_no=return_field_value("issue_number","inv_issue_master","id=$issue_id","issue_number");
	foreach($sql_result as $row)
	{
		$company_id=$row[csf("company_id")];
		$location_id=$row[csf("location_id")];
		$recv_number=$row[csf("recv_number")];
		$receive_basis=$row[csf("receive_basis")];
		$booking_no=$row[csf("booking_no")];
		$challan_no=$row[csf("challan_no")];
		$knitting_source=$knitting_source[$row[csf("knitting_source")]];
		if($row[csf("knitting_source")]==1) $knit_com=$company_library[$row[csf("knitting_company")]]; else $knit_com=$supplier_library[$row[csf("knitting_company")]];
		$knitting_company=$knit_com;
		$receive_date=$row[csf("receive_date")];
		$job_no_arr[$row[csf("job_no")]]=$row[csf("job_no")];
		$style_ref_no_arr[$row[csf("style_ref_no")]]=$row[csf("style_ref_no")];
		$buyer_id_arr[$row[csf("buyer_id")]]=$buyer_arr[$row[csf("buyer_id")]];
		
		$dtls_data[$row[csf("dtls_id")]]["lot"]=$row[csf("lot")];
		$dtls_data[$row[csf("dtls_id")]]["yarn_count_id"]=$count_arr[$row[csf("yarn_count_id")]];
		$dtls_data[$row[csf("dtls_id")]]["yarn_comp_type1st"]=$composition[$row[csf("yarn_comp_type1st")]];
		$dtls_data[$row[csf("dtls_id")]]["yarn_type"]=$yarn_type[$row[csf("yarn_type")]];
		$dtls_data[$row[csf("dtls_id")]]["color"]=$color_arr[$row[csf("color")]];
		$dtls_data[$row[csf("dtls_id")]]["unit_of_measure"]=$unit_of_measurement[$row[csf("unit_of_measure")]];
		$dtls_data[$row[csf("dtls_id")]]["store_id"]=$store_library[$row[csf("store_id")]];
		$dtls_data[$row[csf("dtls_id")]]["booking_no"]=$row[csf("booking_no")];
		$dtls_data[$row[csf("dtls_id")]]["remarks"]=$row[csf("remarks")];
		$dtls_data[$row[csf("dtls_id")]]["cons_quantity"]=$row[csf("cons_quantity")];
		$dtls_data[$row[csf("dtls_id")]]["return_qnty"]=$row[csf("cons_reject_qnty")];
	}
	$com_dtls = fnc_company_location_address($company_id, $location_id, 2);
	?>
	<div style="width:1130px;">
		<table width="1100" cellspacing="0" align="left">
			<tr>
				<td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $com_dtls[0]; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="6" align="center" style="font-size:14px">
					<?
						echo $com_dtls[1];
					?>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:x-large"><strong><? echo $data[2]; ?> Challan</strong></td>
			</tr>
			<tr>
				<td width="120"><strong>Return ID:</strong></td><td width="155"><? echo $recv_number; ?></td>
				<td width="120"><strong>Basis:</strong></td><td width="155"><? echo $issue_basis[$receive_basis]; ?></td>
				<td width="120"><strong>Lot/Book No:</strong></td><td width="155"><? echo $booking_no; ?></td>
                <td width="120"><strong>Return Challan:</strong></td><td><? echo $challan_no; ?></td>
			</tr>
			<tr>
				<td><strong>Return Source:</strong></td><td><? echo $knitting_source[$knitting_source]; ?></td>
				<td><strong>Knitting Com :</strong></td><td><? echo $knitting_company; ?></td>
				<td><strong>Return Date:</strong></td><td><? echo change_date_format($receive_date); ?></td>
                <td><strong>Yarn Issue No.</strong></td><td><? echo $issue_no; ?></td>
			</tr>
			<tr>
				<td><strong>Job Number:</strong></td><td><? echo implode(",",$job_no_arr); ?></td>
				<td><strong>Style Ref. :</strong></td><td><? echo implode(",",$style_ref_no_arr); ?></td>
				<td><strong>Buyer::</strong></td><td><? echo implode(",",$buyer_id_arr);  ?></td>
			</tr>
		</table>
		<br>
		<div style="width:100%;">
			<table align="left" cellspacing="0" width="1100"  border="1" rules="all" class="rpt_table" >
				<thead>
                	<tr>
                        <th width="30">SL</th>
                        <th width="60">Lot No</th>
                        <th width="80">Yarn Count</th>
                        <th width="120">Composition</th>
                        <th width="100">Yarn Type</th>
                        <th width="100">Color</th>
                        <th width="100">Store</th>
                        <th width="60">UOM</th>
                        <th width="80">Return  Qnty</th>
                        <th width="80">Reject Qnty</th>
                        <th width="100">Lot Ratio No</th>
                        <th>Remarks</th>
                    </tr>
				</thead>
				<tbody>
					<?
					$i=1;
					foreach($dtls_data as $trans_id=>$val)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td align="center"><? echo $i; ?></td>
							<td align="center"><p><? echo $val["lot"]; ?>&nbsp;</p></td>
                            <td align="center"><p><? echo $val["yarn_count_id"]; ?>&nbsp;</p></td>
                            <td><p><? echo $val["yarn_comp_type1st"]; ?>&nbsp;</p></td>
                            <td align="center"><p><? echo $val["yarn_type"]; ?>&nbsp;</p></td>
                            <td><p><? echo $val["color"]; ?>&nbsp;</p></td>
                            <td><p><? echo $val["store_id"]; ?>&nbsp;</p></td>
                            <td align="center"><p><? echo $val["unit_of_measure"]; ?>&nbsp;</p></td>
							<td align="right"><? echo number_format($val["cons_quantity"],2); ?></td>
							<td align="right"><? echo number_format($val["return_qnty"],2); ?></td>
							<td align="center">&nbsp;</td>
							<td align="center"><? echo $val["remarks"]; ?></td>
						</tr>
						<? 
						$i++; 
						$tot_qnty+=$val["cons_quantity"];
						$tot_reject_qnty += $val["return_qnty"];
					} 
					?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="8" align="right">Total :</td>
						<td align="right"><? echo $tot_qnty; ?></td>
						<td align="right"><? echo $tot_reject_qnty; ?></td>
						<td colspan="2">&nbsp;</td>
					</tr>
				</tfoot>
			</table>
			<br>
			<?
			//echo signature_table(37, $data[0], "900px");
			?>
			</div>
		</div>
		<?
		exit();
}


if($action=="westage_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
 	$job_no = str_replace("'","",$job_no);
	$westage_qnty = str_replace("'","",$westage_qnty);
	$westage_dtls = str_replace("'","",$westage_dtls);

 	?>
	<script>

	function fn_onClosed()
	{
		var num_row=$("#tbl_serial tbody tr").length;
		var dtls_data="";
		for(var i=1; i<=num_row; i++)
		{
			if($("#txtPanelWeight_"+i).val()*1>0)
			{
				if(dtls_data=="") dtls_data=$("#tdBodyPart_"+i).attr("title")+"_"+$("#txtPanelPcs_"+i).val()+"_"+$("#txtPanelWeight_"+i).val();
				else dtls_data+="="+$("#tdBodyPart_"+i).attr("title")+"_"+$("#txtPanelPcs_"+i).val()+"_"+$("#txtPanelWeight_"+i).val();
			}
		}
		$("#hdn_dtls_data").val(dtls_data);
		$("#hdn_weight").val($("#totalPanelWeight").val());
		//alert(dtls_data);
		parent.emailwindow.hide();
	}
	
	function fn_total(str)
	{
		var num_row=$("#tbl_serial tbody tr").length;
		var ddd={ dec_type:2, comma:0, currency:''};
		if(str==1)
		{
			math_operation( "totalPanelPcs", "txtPanelPcs_", '+', num_row,ddd);
		}
		else
		{
			math_operation( "totalPanelWeight", "txtPanelWeight_", '+', num_row,ddd);
		}
		
	}

	</script>
	</head>
	<body>
	<div style="width:100%;" >
	<form name="searchlcfrm_1" id="searchlcfrm_1" autocomplete="off">
    <input type="hidden" id="hdn_weight" name="hdn_weight" />
    <input type="hidden" id="hdn_dtls_data" name="hdn_dtls_data" />
    	<table width="450" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_header" align="left" >
            <thead>
                <tr>
                    <th width="50">SL No</th>
                    <th width="200">Panel Description</th>
                    <th width="100">Number Of Panel Pcs</th>
                    <th width="100">Panel Weight (Lbs)</th>
                </tr>
            </thead>
        </table>
        <div style="width:470px; min-height:220px">
		<table width="450" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_serial" style="overflow:scroll; min-height:200px" align="left">
        	
            <tbody>
                <?
				$sql="select b.ID, b.SAMPLE_MST_ID, b.BODY_PART_ID from WO_PRE_COST_FABRIC_COST_DTLS a, SAMPLE_DEVELOPMENT_FABRIC_ACC b 
				where a.SAMPLE_ID=b.SAMPLE_MST_ID and b.KNITINGGM is not null and a.STATUS_ACTIVE=1 and b.STATUS_ACTIVE=1 and a.JOB_NO='$job_no'";
				//echo $sql;//die;
				$result = sql_select($sql);
				$count=count($result );
				$i=1;
				$westage_dtls_arr=explode("=",$westage_dtls);
				$previous_data=array();
				foreach($westage_dtls_arr as $val)
				{
					$val_ref=explode("_",$val);
					$previous_data[$val_ref[0]]["panel_pcs"]=$val_ref[1];
					$previous_data[$val_ref[0]]["panel_weight"]=$val_ref[2];
				}
				$total_pcs=$total_weight=0;
				foreach($result as $row)
				{
					if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $row["ID"]; ?>" style="cursor:pointer">
						<td width="50" align="center"><? echo $i; ?></td>
						<td width="200" title="<? echo $row["BODY_PART_ID"];?>" id="tdBodyPart_<? echo $i; ?>"><? echo $time_weight_panel[$row["BODY_PART_ID"]];?></td>
						<td width="100" align="center"><input type="text" class="text_boxes_numeric" style="width:80px" id="txtPanelPcs_<? echo $i; ?>" name="txtPanelPcs[]" value="<? echo $previous_data[$row["BODY_PART_ID"]]["panel_pcs"];?>" onKeyUp="fn_total(1);" ></td>
						<td align="center"><input type="text" class="text_boxes_numeric" style="width:80px" id="txtPanelWeight_<? echo $i; ?>" name="txtPanelWeight[]" value="<? echo $previous_data[$row["BODY_PART_ID"]]["panel_weight"];?>" onKeyUp="fn_total(2);" ></td>
					</tr>
					<?
					$i++;
					$total_pcs+=$previous_data[$row["BODY_PART_ID"]]["panel_pcs"];
					$total_weight+=$previous_data[$row["BODY_PART_ID"]]["panel_weight"];
				}
				?>
			</tbody>
            <tfoot>
            	<tr>
                    <th colspan="2" align="right">Total:</th>
                    <th><input type="text" class="text_boxes_numeric" style="width:80px" id="totalPanelPcs" name="totalPanelPcs" value="<? echo $total_pcs; ?>" ></th>
                    <th><input type="text" class="text_boxes_numeric" style="width:80px" id="totalPanelWeight" name="totalPanelWeight" value="<? echo number_format($total_weight,2,'.',''); ?>" ></th>
                </tr>
            </tfoot>
        </table>
    </div>
    <div align="center"><input type="button" name="btn_close" class="formbutton" style="width:100px" value="Close" onClick="fn_onClosed()" /></div>
    </form>
   </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

<script>
//alert(serialNoArr);
	if( serialNoArr!="" )
	{
		serialNoArr=serialNoArr.split(",");
		for(var k=0;k<serialNoArr.length; k++)
		{
			js_set_value(serialNoArr[k] );
			//alert(serialNoArr[k]);
		}
	}
</script>
</html>
<?
}

?>