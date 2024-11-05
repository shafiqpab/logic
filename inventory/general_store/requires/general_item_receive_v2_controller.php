<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$permission=$_SESSION['page_permission'];
include('../../../includes/common.php');
$payment_yes_no=array(0=>"yes", 1=>"No");

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];
$user_store_ids = $_SESSION['logic_erp']['store_location_id'];
$user_supplier_ids = trim($_SESSION['logic_erp']['supplier_id']);

$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, supplier_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$supplier_id = $userCredential[0][csf('supplier_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
$item_cate_id = $userCredential[0][csf('item_cate_id')];

if ($company_id !='') {
    $company_credential_cond = "and comp.id in($company_id)";
}
if ($store_location_id !='') {
    $store_location_credential_cond = "and a.id in($store_location_id)"; 
}
if($item_cate_id !='') {
    $item_cate_credential_cond = $item_cate_id ;  
}
else
{
	$item_cate_credential_cond="".implode(",",array_flip($general_item_category))."";
}
if ($supplier_id !='') {
    $supplier_credential_cond = "and a.id in($supplier_id)";
}

if ($action=="load_drop_down_supplier")
{	 
	echo create_drop_down( "cbo_supplier", 142, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id and b.party_type in(1,5,6,7,8,30,36,37,39,92) $supplier_credential_cond and c.tag_company in($data) and a.status_active=1 and a.is_deleted=0 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
	exit();
}

if ($action=="load_drop_down_loan_party")
{
	echo create_drop_down( "cbo_loan_party", 142, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_tag_company b 
	where a.id=b.supplier_id and b.tag_company=$data and a.status_active=1 and a.is_deleted=0 and a.id in(select supplier_id from lib_supplier_party_type where party_type=91) order by supplier_name","id,supplier_name", 1, "- Select Loan Party -", $selected, "","","" );
	exit();
}

if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_name", 142, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data' and b.category_type in(4,8,9,10,11,15,16,17,18,19,20,21,22,32,33,34,35,36,37,38,39,40,41,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,89,90,91,92,93,94,99) and a.status_active=1 and a.is_deleted=0 $store_location_credential_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1,"--Select store--",0,"fn_load_floor(this.value);reset_room_rack_shelf('','cbo_store_name');");
	exit();
}

// wo/pi popup here---------// 
if ($action=="wopi_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);  
	?>
	<script>
	function js_set_value(str)
	{
		var splitData = str.split("_");		

		if(splitData[2] == "No")
		{ 
			alert("Goods receive not allowed against Un-Approved P.O. Please ensure the P.O is approved before receiving the goods"); return; 
		}

		$("#hidden_tbl_id").val(splitData[0]); // wo/pi id
		$("#hidden_wopi_number").val(splitData[1]); // wo/pi number
		parent.emailwindow.hide();
	}
	$(document).ready(function(e) {
		$("#txt_search_common").focus();
	});
    </script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
		<table width="800" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
			<thead>
				<th colspan="2"></th>
				<th>
					<? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?>
				</th>
				<th colspan="2"></th>
			</thead>
			<thead>
				<th width="150">Search By</th>
				<th width="150" align="center" id="search_by_th_up">Enter WO/PI/Req Number</th>
				<th width="150">Item Category</th>
				<th width="200">Date Range</th>
				<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
			</thead>
			<tbody>
				<tr>
					<td>
						<?
							echo create_drop_down( "cbo_search_by", 150, $receive_basis_arr,"",1, "--Select--", $receive_basis,"",1 );
						?>
					</td>
					<td width="180" align="center" id="search_by_td">
						<input type="text" style="width:150px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
					</td>
					<? ($receive_basis == 1) ? $category_disable = "" : $category_disable=1; ?>
					<td>
						<?
						echo create_drop_down( "cbo_item_category", 170, $general_item_category,"", 1, "-- Select --", "", "","$category_disable","$item_cate_credential_cond","","","" );
							// 4,8,9,10,11,15,16,17,18,19,20,21,22,32
						?> 
					</td>   
					<td align="center">
						<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
						<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
					</td>
					<td align="center">
						<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('cbo_item_category').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_year_selection').value, 'create_wopi_search_list_view', 'search_div', 'general_item_receive_v2_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />			
					</td>
				</tr>
				<tr>
					<td align="center" height="40" valign="middle" colspan="5">
						<? echo load_month_buttons(1);  ?>
						<!-- Hidden field here-->
						<input type="hidden" id="hidden_tbl_id" value="" />
						<input type="hidden" id="hidden_wopi_number" value="hidden_wopi_number" />
						<!-- END -->
					</td>
				</tr>
			</tbody>
		</table>
		<br>
		<div align="center" valign="top" id="search_div"></div>
	</form>
	</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_wopi_search_list_view")
{
 	$ex_data = explode("_",$data);
	$cbo_search_by          = $ex_data[0];
	$txt_search_common      = trim($ex_data[1]);
	$txt_date_from          = $ex_data[2];
	$txt_date_to            = $ex_data[3];
	$company                = $ex_data[4];
	$item_cat_ref           = $ex_data[5];
	$cbo_string_search_type = $ex_data[6];
	$cbo_year               = $ex_data[7];
	
	$sql_variable_setup=sql_select("select CATEGORY, OVER_RCV_PERCENT, OVER_RCV_PAYMENT from variable_inv_ile_standard where company_name=$company and variable_list=23 and status_active=1 and is_deleted=0");
	$variable_setup_data=array();
	foreach($sql_variable_setup as $val)
	{
		$variable_setup_data[$val["CATEGORY"]]["OVER_RCV_PERCENT"]=$val["OVER_RCV_PERCENT"];
		$variable_setup_data[$val["CATEGORY"]]["OVER_RCV_PAYMENT"]=$val["OVER_RCV_PAYMENT"];
	}

	$appr_status=array();

    $year_cond = " and to_char(a.insert_date,'YYYY')=$cbo_year";
	
	if( $txt_date_from!="" && $txt_date_to!="" )
	{
		$txt_date_from=change_date_format($txt_date_from,'','',1);
		$txt_date_to=change_date_format($txt_date_to,'','',1);
	}
 	
	$sql_cond="";
	if($cbo_search_by==1) // for pi
	{
		if ($txt_date_from!="" && $txt_date_to!="") $sql_cond .= " and a.pi_date between '".$txt_date_from."' and '".$txt_date_to."'";
		else $sql_cond .= " and to_char(a.pi_date,'YYYY')=$cbo_year";

		if($item_cat_ref>0) $sql_cond .= " and a.item_category_id=$item_cat_ref";
        else $sql_cond .= " and a.item_category_id in ($item_cate_credential_cond)";
	}
	else if($cbo_search_by==2) // for wo
	{
		if ($txt_date_from!="" && $txt_date_to!="") $sql_cond .= " and a.wo_date between '".$txt_date_from."' and '".$txt_date_to."'";
		else $sql_cond .= " and to_char(a.wo_date,'YYYY')=$cbo_year";
	}
	else if($cbo_search_by==7) // for requisition
	{
		if ($txt_date_from!="" && $txt_date_to!="") $sql_cond .= " and a.requisition_date between '".$txt_date_from."' and '".$txt_date_to."'";
		else $sql_cond .= " and to_char(a.requisition_date,'YYYY')=$cbo_year";
			
		if ($item_cat_ref>0) $sql_cond .= " and b.item_category=$item_cat_ref";
	}

	if ($txt_search_common!="")
	{
		if($cbo_search_by==1) // for pi
		{
			if($cbo_string_search_type==1) $sql_cond .= " and a.pi_number='$txt_search_common'";
			else if($cbo_string_search_type==2) $sql_cond .= " and a.pi_number LIKE '$txt_search_common%'";
			else if($cbo_string_search_type==3) $sql_cond .= " and a.pi_number LIKE '%$txt_search_common'";
			else $sql_cond .= " and a.pi_number LIKE '%$txt_search_common%'";	
				
			if ($company!="") $sql_cond .= " and a.importer_id='$company'";
		}
		else if($cbo_search_by==2) // for wo
		{
			if($cbo_string_search_type==1) $sql_cond .= " and wo_number_prefix_num='$txt_search_common'";
			else if($cbo_string_search_type==2) $sql_cond .= " and wo_number_prefix_num LIKE '$txt_search_common%'";
			else if($cbo_string_search_type==3) $sql_cond .= " and wo_number_prefix_num LIKE '%$txt_search_common'";
			else $sql_cond .= " and wo_number_prefix_num LIKE '%$txt_search_common%'";
			
			if($company!="") $sql_cond .= " and company_name='$company'";
		}
		else if($cbo_search_by==7) // for requisition
		{
			if($cbo_string_search_type==1) $sql_cond .= " and a.requ_prefix_num='$txt_search_common'";
			else if($cbo_string_search_type==2) $sql_cond .= " and a.requ_prefix_num LIKE '$txt_search_common%'";
			else if($cbo_string_search_type==3) $sql_cond .= " and a.requ_prefix_num LIKE '%$txt_search_common'";
			else $sql_cond .= " and a.requ_prefix_num LIKE '%$txt_search_common%'";
							
			if($company!="") $sql_cond .= " and a.company_id='$company'";
		}
 	}	
	//echo $cbo_search_by;die;	
 	
	if($cbo_search_by==1 ) //pi base
	{
		$approval_status_cond="";
		$sql_app_res=sql_select("select APPROVAL_NEED, ALLOW_PARTIAL from approval_setup_mst a, approval_setup_dtls b where a.id=b.mst_id and a.company_id='$company' and b.page_id in(18) and b.validate_page=1 and a.status_active=1 and b.status_active=1 order by a.setup_date asc");
		foreach ($sql_app_res as $row)
		{
			$approval_status=$row['APPROVAL_NEED'];
		}

		if($approval_status==1) $approval_status_cond= " and a.approved=1";

		$sql = "SELECT a.ID, a.pi_number as WOPI_NUMBER, d.LC_NUMBER, a.pi_date as WOPI_DATE, a.SUPPLIER_ID, a.CURRENCY_ID, a.SOURCE, a.item_category_id as ITEM_CATEGORY, sum(b.quantity) as QUANTITY  
		from com_pi_master_details a,com_pi_item_details b 
		left join com_btb_lc_pi c on b.pi_id=c.pi_id 
		left join com_btb_lc_master_details d on c.com_btb_lc_master_details_id=d.id
		where a.id=b.pi_id and a.item_category_id not in (1,2,3,5,6,7,12,13,14,23) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.goods_rcv_status<>1 and a.importer_id=$company $sql_cond $approval_status_cond		
		group by a.id, a.pi_number, d.lc_number, a.pi_date, a.supplier_id, a.currency_id, a.source, a.item_category_id 
		order by a.pi_date desc";
	}
	else if($cbo_search_by==2) // wo base
	{
		$sql = "SELECT a.ID, a.wo_number_prefix_num as WOPI_NUMBER, '' as LC_NUMBER, a.wo_date as WOPI_DATE, a.SUPPLIER_ID, a.CURRENCY_ID, a.SOURCE, a.LOCATION_ID, a.IS_APPROVED, a.ENTRY_FORM, b.item_category_id as ITEM_CATEGORY , sum(b.supplier_order_quantity) as QUANTITY
		from wo_non_order_info_mst a, wo_non_order_info_dtls b
		where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in(146,147) and a.pay_mode in(1,4) and a.company_name=$company $sql_cond
		group by a.id, a.wo_number_prefix_num, a.wo_date, a.supplier_id, a.currency_id, a.source, a.location_id, a.is_approved, a.entry_form, b.item_category_id
		order by a.id DESC";
		
		$sql_app_res=sql_select("select APPROVAL_NEED, ALLOW_PARTIAL from approval_setup_mst a, approval_setup_dtls b where a.id=b.mst_id and a.company_id='$company' and b.page_id in(16,22) and b.validate_page=1 and a.status_active=1 and b.status_active=1 order by a.setup_date asc");
		$approval_status_arr=array();
		foreach ($sql_app_res as $row)
		{
			//checking approval status for stationary
			$approval_status_arr[146]['status']=$row['APPROVAL_NEED'];
			$approval_status_arr[146]['allow_partial']=$row['ALLOW_PARTIAL'];
			//checking approval status for other Purchase
			$approval_status_arr[147]['status']=$row['APPROVAL_NEED'];
		}

		$sql_data=sql_select($sql);
		foreach($sql_data as $val)
		{
			if($approval_status_arr[$val['ENTRY_FORM']]['status']==1)
			{
				if($approval_status_arr[$val['ENTRY_FORM']]['allow_partial']==1)
				{
					if($val['IS_APPROVED']==1 || $val['IS_APPROVED']==3) $appr_status[$val['ID']]="Yes";
					else $appr_status[$val[csf('id')]]="No";
				}
				else
				{
					if($val['IS_APPROVED']==1) $appr_status[$val['ID']]="Yes";
					else $appr_status[$val['ID']]="No";
				}
			}
			else $appr_status[$val['ID']]="N/A";
		}
	}
	else if($cbo_search_by==7) // requisition base
	{
		$approval_cond="";
		$sql_app_res=sql_select("select APPROVAL_NEED, ALLOW_PARTIAL from approval_setup_mst a, approval_setup_dtls b where a.id=b.mst_id and a.company_id='$company' and b.page_id in(13) and b.validate_page=1 and a.status_active=1 and b.status_active=1 order by a.setup_date asc");
		foreach ($sql_app_res as $row)
		{
			$approval_status=$row['APPROVAL_NEED'];
			$allow_partial=$row['ALLOW_PARTIAL'];
		}
		if($approval_status==1)
		{
			if($allow_partial==1) $approval_cond=" and a.is_approved in (1,3)";
			else $approval_cond = " and a.is_approved in (1)";
		}
	
		$sql = "SELECT a.ID, a.requ_prefix_num as WOPI_NUMBER, '' as LC_NUMBER, a.requisition_date as WOPI_DATE, '' as SUPPLIER_ID, a.cbo_currency as CURRENCY_ID, a.SOURCEe, a.ITEM_CATEGORY_ID, b.ITEM_CATEGORY, a.LOCATION_ID, sum(b.quantity) as QUANTITY
		from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b
		where a.id = b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category in (".implode(",",array_flip($general_item_category)).") and a.pay_mode=4 and a.company_id=$company $approval_cond $sql_cond $year_cond
		group by a.id,a.requ_prefix_num,a.requisition_date,a.cbo_currency,a.source,a.item_category_id,b.item_category,a.location_id
		order by a.id desc";
	}
	//echo $sql;
	$result = sql_select($sql);

	$booking_id_all=array();
	foreach($result as $row)
	{
		$booking_id_all[$row['ID']]=$row['ID'];
	}
	$booking_id_in=where_con_using_array($booking_id_all,0,'a.booking_id');

	$receive_sql = sql_select("SELECT a.ID, a.BOOKING_ID, b.PROD_ID, sum(b.order_qnty) as RECEIVE_QNTY, b.ITEM_CATEGORY 
	from inv_receive_master a, inv_transaction b 
	where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=20 and a.receive_basis=$txt_search_by and b.transaction_type=1 $booking_id_in
	group by a.id, a.booking_id, b.prod_id,b.item_category");
	foreach($receive_sql as $row)
	{
		$receive_arr[$row["BOOKING_ID"]][$row["ITEM_CATEGORY"]]+=$row["RECEIVE_QNTY"];
	}

	$receive_return_sql = sql_select("SELECT a.BOOKING_ID, b.PROD_ID, c.CONVERSION_FACTOR, sum(b.cons_quantity) as ISSUE_QNTY, b.ITEM_CATEGORY 
	from inv_issue_master a, inv_transaction b, product_details_master c 
	where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=3 and a.received_id>0 and a.entry_form=26 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $booking_id_in
	group by a.booking_id, b.prod_id, b.item_category, c.conversion_factor");
	foreach($receive_return_sql as $row)
	{
		$receive_rtn_arr[$row["BOOKING_ID"]][$row["ITEM_CATEGORY"]]+=$row["ISSUE_QNTY"]/$row["CONVERSION_FACTOR"];
	}	
 
	$location_lib_arr=return_library_array("select id, location_name from lib_location",'id','location_name');
 	$supplier_arr=return_library_array("select id, supplier_name from lib_supplier",'id','supplier_name');
	$arr=array(1=>$location_lib_arr,4=>$supplier_arr,5=>$currency,6=>$source,7=>$item_category,8=>$appr_status);
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table">
        <thead>
            <th width="40">SL No</th>
            <th width="70">WO/PI No</th>
            <th width="120">Location</th>
            <th width="80">LC</th>
            <th width="80">Date</th>
            <th width="150">Supplier</th>
            <th width="60">Currency</th>
            <th width="80">Source</th>
            <th width="100">Item Category</th>
            <th>Approval Status</th>
        </thead>
     </table>
     <div style="width:900px; max-height:250px; overflow-y:scroll">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table" id="list_view" align="left">
        <? 
		$i=1;
		foreach ($result as $row)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; 
			else $bgcolor="#FFFFFF";

			$woPiBlance=1;
			if($variable_setup_data[$row['ITEM_CATEGORY']]["OVER_RCV_PAYMENT"]>0)
			{
				$allow_qnty=($row['QUANTITY']+(($row['QUANTITY']/100)*$variable_setup_data[$row['ITEM_CATEGORY']]["OVER_RCV_PERCENT"]));
				$woPiBlance=$allow_qnty-$receive_arr[$row["ID"]][$row['ITEM_CATEGORY']]+$receive_rtn_arr[$row["ID"]][$row['ITEM_CATEGORY']];
			}
			
			if($woPiBlance>0)
			{
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i;?>" onClick="js_set_value('<? echo $row['ID']."_".$row['WOPI_NUMBER']."_".$appr_status[$row['ID']]; ?>')">				
					<td width="40"><? echo $i; ?></td>	
					<td width="70"><p><? echo $row['WOPI_NUMBER']; ?></p></td>
					<td width="120"><p><? echo $location_lib_arr[$row['LOCATION_ID']]; ?></p></td>
					<td width="80"><p><? echo $row['LC_NUMBER']; ?></p></td> 
					<td width="80" align="center"><p><? echo change_date_format($row['WOPI_DATE']); ?> </p></td>
					<td width="150"><p><? echo $supplier_arr[$row['SUPPLIER_ID']]; ?>&nbsp;</p></td>
					<td width="60"><p><? echo $currency[$row['CURRENCY_ID']]; ?></p></td>
					<td width="80"><p><? echo $source[$row['SOURCE']]; ?></p></td>
					<td width="100"><p><? echo $item_category[$row['ITEM_CATEGORY']]; ?>&nbsp;</p></td>
					<td align="center"><p><? echo $appr_status[$row['ID']]; ?></p></td>
				</tr>
				<?
				$i++;  
			}
		}
        ?>
        </table>
     </div>
	<?
	exit();	
}

if($action=="populate_data_from_data")
{
	$sql = "SELECT ID, RECV_NUMBER, COMPANY_ID, RECEIVE_BASIS, RECEIVE_PURPOSE, LOAN_PARTY, RECEIVE_DATE, BOOKING_ID, CHALLAN_NO, CHALLAN_DATE, STORE_ID, LC_NO, SUPPLIER_ID, EXCHANGE_RATE, CURRENCY_ID, LC_NO, PAY_MODE, SOURCE, BOE_MUSHAK_CHALLAN_NO, BOE_MUSHAK_CHALLAN_DATE, REMARKS, SUPPLIER_REFERANCE, IS_POSTED_ACCOUNT, VARIABLE_SETTING, STORE_SL_NO, RCVD_BOOK_NO,ADDI_CHALLAN_DATE, BILL_NO, BILL_DATE, PURCHASER_NAME, CARRIED_BY, QC_CHECK_BY, RECEIVE_BY, GATE_ENTRY_BY, GATE_ENTRY_DATE,ADDI_RCVD_DATE, GATE_ENTRY_NO, IS_AUDITED from inv_receive_master where id='$data' and entry_form=20";
	$res = sql_select($sql);
	foreach($res as $row)
	{
		echo "$('#update_id').val(".$row["ID"].");\n";
		echo "$('#txt_mrr_no').val('".$row["RECV_NUMBER"]."');\n";
		echo "$('#cbo_company_id').val(".$row["COMPANY_ID"].");\n";
		echo"load_drop_down( 'requires/general_item_receive_v2_controller', ".$row["COMPANY_ID"].", 'load_drop_down_supplier', 'supplier' );\n";
		echo "$('#cbo_receive_basis').val(".$row["RECEIVE_BASIS"].");\n";
		echo "$('#cbo_receive_purpose').val(".$row["RECEIVE_PURPOSE"].");\n";
		echo "$('#cbo_loan_party').val(".$row["LOAN_PARTY"].");\n";
		echo "$('#txt_receive_date').val('".change_date_format($row["RECEIVE_DATE"])."');\n";
		echo "$('#txt_challan_no').val('".$row["CHALLAN_NO"]."');\n";
		echo "$('#txt_challan_date_mst').val('".change_date_format($row["CHALLAN_DATE"])."');\n";

		echo "load_drop_down('requires/general_item_receive_v2_controller', '".$row['COMPANY_ID']."', 'load_drop_down_store','store_td');\n";
		echo "$('#cbo_store_name').val(".$row["STORE_ID"].");\n";
		echo "$('#cbo_supplier').val(".$row["SUPPLIER_ID"].");\n";
		echo "$('#cbo_currency_id').val(".$row["CURRENCY_ID"].");\n";
		echo "$('#txt_sup_ref').val('".$row["SUPPLIER_REFERANCE"]."');\n";
		echo "$('#hidden_posted_in_account').val('".$row["IS_POSTED_ACCOUNT"]."');\n";
		
		$addi_info_str = $row["RCVD_BOOK_NO"]."_".change_date_format($row["ADDI_CHALLAN_DATE"])."_".$row["BILL_NO"]."_".change_date_format($row["BILL_DATE"])."_".$row["PURCHASER_NAME"]."_".$row["CARRIED_BY"]."_".$row["QC_CHECK_BY"]."_".$row["RECEIVE_BY"]."_".$row["GATE_ENTRY_BY"]."_".change_date_format($row["GATE_ENTRY_DATE"])."_".change_date_format($row["ADDI_RCVD_DATE"])."_".$row["GATE_ENTRY_NO"]."_".$row["STORE_SL_NO"];
		echo "$('#txt_addi_info').val('".$addi_info_str."');\n";
		
		if($row["CBO_CURRENCY_ID"]==1)
		{
			echo "$('#txt_exchange_rate').val(1);\n";
			echo "$('#txt_exchange_rate').attr('disabled',true);\n";
		}
		else
		{
			echo "$('#txt_exchange_rate').attr('disabled',false);\n";
		}

		echo "$('#txt_exchange_rate').val(".$row["EXCHANGE_RATE"].");\n";
		echo "$('#cbo_pay_mode').val(".$row["PAY_MODE"].");\n";
		echo "$('#cbo_source').val(".$row["SOURCE"].");\n";
		echo "$('#txt_boe_mushak_challan_no').val('".$row["BOE_MUSHAK_CHALLAN_NO"]."');\n";
		echo "$('#txt_boe_mushak_challan_date').val('".change_date_format($row["BOE_MUSHAK_CHALLAN_DATE"])."');\n";
		echo "$('#txt_remarks').val('".$row["REMARKS"]."');\n";
		
		if($row["RECEIVE_BASIS"]==1) $wopireq=return_field_value("pi_number","com_pi_master_details","id=".$row["BOOKING_ID"]."");			
		else if($row["RECEIVE_BASIS"]==2) $wopireq=return_field_value("wo_number","wo_non_order_info_mst","id=".$row["BOOKING_ID"]."");			
		else if($row["RECEIVE_BASIS"]==7) $wopireq=return_field_value("requ_no","inv_purchase_requisition_mst","id=".$row["BOOKING_ID"]."");

		echo "$('#txt_wo_pi_req').val('".$wopireq."');\n";
		echo "$('#txt_wo_pi_req_id').val(".$row["BOOKING_ID"].");\n";
		
		echo "$('#hidden_lc_id').val(".$row["LC_NO"].");\n";
		$lcNumber = return_field_value("lc_number","com_btb_lc_master_details","id='".$row["LC_NO"]."'");
		echo "$('#txt_lc_no').val('".$lcNumber."');\n";
		// Check Audited
		if($row["IS_AUDITED"]==1) echo "$('#audited').text('Audited');\n";
		else echo "$('#audited').text('');\n";

		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_general_receive',1);\n";
 	}
	exit();	
}

//after select wo/pi number get form data here---------------//
if($action=="populate_data_from_wopi_popup")
{
	$ex_data = explode("**",$data);
	$receive_basis = $ex_data[0];
	$wo_pi_ID      = $ex_data[1];
	
	if($receive_basis==1 ) //PI
	{
		$sql ="select b.ID, a.pi_number as WOPI_NUMBER, b.LC_NUMBER, a.SUPPLIER_ID, a.CURRENCY_ID, a.SOURCE, '' as PAY_MODE, a.id as PI_ID, null as REFERENCE
		from com_pi_master_details a 
		left join com_btb_lc_pi c on a.id=c.pi_id 
		left join com_btb_lc_master_details b on c.com_btb_lc_master_details_id=b.id
		where a.item_category_id not in (1,2,3,5,6,7,12,13,14) and a.status_active=1 and a.is_deleted=0 and a.id=$wo_pi_ID";
	}
	else if($receive_basis==2) //WO
	{
 		$sql = "select ID, wo_number as WOPI_NUMBER, '' as LC_NUMBER, SUPPLIER_ID, CURRENCY_ID, SOURCE, PAY_MODE, 0 as PI_ID, REFERENCE
		from wo_non_order_info_mst
		where status_active=1 and is_deleted=0 and entry_form in(146,147) and id=$wo_pi_ID";
	}
	else if($receive_basis==7) //Requisition
	{
 		$sql = "select ID, requ_no as WOPI_NUMBER, '' as LC_NUMBER, requisition_date as WOPI_DATE, '' as SUPPLIER_ID, cbo_currency as CURRENCY_ID, SOURCE, PAY_MODE, 0 as PI_ID, REFERENCE 
		from inv_purchase_requisition_mst
		where status_active=1 and is_deleted=0 and pay_mode=4 and id=$wo_pi_ID";
	}
	//echo $sql;die;
	$result = sql_select($sql);
	foreach($result as $row)
	{
		echo "$('#txt_wo_pi_req').val('".$row["WOPI_NUMBER"]."');\n";
		echo "$('#cbo_supplier').val(".$row["SUPPLIER_ID"].");\n";
		echo "$('#cbo_currency_id').val(".$row["CURRENCY_ID"].");\n";
		echo "$('#txt_ref_no').val('".$row["REFERENCE"]."');\n";
		echo "check_exchange_rate();\n";
		echo "$('#cbo_source').val(".$row["SOURCE"].");\n";
		if($receive_basis==1) echo "$('#cbo_pay_mode').val(2);\n";
		else echo "$('#cbo_pay_mode').val(".$row["PAY_MODE"].");\n";

		echo "$('#txt_lc_no').val('".$row["LC_NUMBER"]."');\n";
		if($row["LC_NUMBER"]!="") echo "$('#hidden_lc_id').val(".$row["ID"].");\n";
		else echo "$('#hidden_lc_id').val('');\n";
	}
	exit();	
}

if( $action == 'show_fabric_desc_listview' ) 
{
	$data=explode("**",$data);
	//print_r($data);
	$bookingNo_piId  = $data[0];
	$receive_basis   = $data[1];
	$company         = $data[2];
	$cbo_currency_id = $data[3];
	$exchange_rate   = $data[4];
	$source          = $data[5];
	$store_id        = $data[6];

	$sql_ile="select CATEGORY, ITEM_GROUP, SOURCE, STANDARD from variable_inv_ile_standard where source='$source' and company_name=$company and category in(".implode(",",array_flip($general_item_category)).") and status_active=1 and is_deleted=0 and variable_list=8 order by id desc";
	$sql_ile_result = sql_select($sql_ile);
	$variable_ile_standard=array();
	foreach ($sql_ile_result as $row) {
		$variable_ile_standard[$row["CATEGORY"]][$row["ITEM_GROUP"]][$row["SOURCE"]]=$row["STANDARD"];
	}
	
	$prev_entry="select pi_wo_batch_no as PI_WO_REQ_ID, PROD_ID, sum(order_qnty) as PREV_QUANTITY
	from inv_transaction
	where company_id=$company and item_category in(".implode(",",array_flip($general_item_category)).") and transaction_type=1 and receive_basis=$receive_basis and pi_wo_batch_no='$bookingNo_piId' and status_active=1 and is_deleted=0
	group by pi_wo_batch_no,prod_id";
	//echo $prev_entry;
	$prev_entry_result=sql_select($prev_entry);
	$prev_data_arr=array();
	foreach($prev_entry_result as $row)
	{
		$prev_data_arr[$row["PROD_ID"]]+=$row["PREV_QUANTITY"];
	}
	
	if($receive_basis==1)
	{
		$sql="select a.ID, a.SOURCE, b.id as PI_WO_REQ_DTLS_ID, b.ITEM_PROD_ID, b.UOM, b.QUANTITY, b.NET_PI_RATE, b.NET_PI_AMOUNT, c.ITEM_CATEGORY_ID, c.ITEM_GROUP_ID, c.ITEM_DESCRIPTION, c.ITEM_SIZE, c.SUB_GROUP_NAME, c.ITEM_NUMBER, c.ITEM_CODE, c.conversion_factor as CONVERSION_FACTOR
		from com_pi_master_details a, com_pi_item_details b, product_details_master c
		where a.id=b.pi_id and b.item_prod_id=c.id and a.id='$bookingNo_piId' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	}
	else if($receive_basis==2)
	{
		$sql = "select a.ID, a.SOURCE, b.id as PI_WO_REQ_DTLS_ID, b.item_id as ITEM_PROD_ID, b.UOM, b.supplier_order_quantity as QUANTITY, b.rate as NET_PI_RATE, b.amount as NET_PI_AMOUNT, c.ITEM_CATEGORY_ID, c.ITEM_GROUP_ID, c.ITEM_DESCRIPTION, c.ITEM_SIZE, c.SUB_GROUP_NAME, c.ITEM_NUMBER, c.ITEM_CODE, c.brand_name as BRAND, c.MODEL, c.ORIGIN, c.conversion_factor as CONVERSION_FACTOR
		from wo_non_order_info_mst a, wo_non_order_info_dtls b, product_details_master c
		where a.id=b.mst_id and b.item_id=c.id and a.id='$bookingNo_piId' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	}
	else
	{
		$sql = "select a.ID, a.SOURCE, b.id as PI_WO_REQ_DTLS_ID, b.PRODUCT_ID as ITEM_PROD_ID, b.CONS_UOM as UOM, b.quantity as QUANTITY, b.rate as NET_PI_RATE, b.amount as NET_PI_AMOUNT, c.ITEM_CATEGORY_ID, c.ITEM_GROUP_ID, c.ITEM_DESCRIPTION, c.ITEM_SIZE, c.SUB_GROUP_NAME, c.ITEM_NUMBER, c.ITEM_CODE, c.conversion_factor as CONVERSION_FACTOR
		from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c
		where a.id=b.mst_id and b.product_id=c.id and a.id='$bookingNo_piId' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	}
	//echo $sql; die;
	$data_array=sql_select($sql);

	$item_group_arr=return_library_array("select id, item_name from lib_item_group where status_active=1","id","item_name");
	$yarn_count_library = return_library_array("select id,yarn_count from lib_yarn_count where status_active=1", 'id', 'yarn_count');
	$color_library = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$i=1;
	foreach($data_array as $row)
	{
		$prev_rcv_qnty = $prev_data_arr[$row["ITEM_PROD_ID"]];
		$qnty = ($row['QUANTITY']-$prev_rcv_qnty);
		$ile_percentage=($variable_ile_standard[$row["ITEM_CATEGORY_ID"]][$row["ITEM_GROUP_ID"]][$row["SOURCE"]])/100;
		$ile_cost = 0; //ile cost = (ile/100)*rate
		if($ile_percentage>0) $ile_cost=number_format(($ile_percentage*$row["NET_PI_RATE"]),$dec_place[5],".","" ); 
		$conversion_factor = $row["CONVERSION_FACTOR"];
		$domestic_rate = return_domestic_rate($row["NET_PI_RATE"], $ile_cost, $exchange_rate, $conversion_factor);
		$book_currency = $qnty*$domestic_rate;	
		$amount = $qnty*$row["NET_PI_RATE"];
		//$book_avg_rate = $amount/$row["NET_PI_RATE"];

		if($qnty>0)
		{
			if ($i%2==0) $bgcolor="#E9F3FF"; 
			else $bgcolor="#FFFFFF";
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $i; ?>" align="center">
				<td id="sl_<? echo $i; ?>"><? echo $i; ?>
					<input type="hidden" name="updatedtlsid[]" id="updatedtlsid_<? echo $i; ?>" value="" readonly>
					<input type="hidden" name="piWoDtlsId[]" id="piWoDtlsId_<? echo $i; ?>" value="<? echo $row["PI_WO_REQ_DTLS_ID"]; ?>" readonly>
					<input type="hidden" name="prodId[]" id="prodId_<? echo $i; ?>" value="<? echo $row["ITEM_PROD_ID"]; ?>" readonly>
				</td>
                <td id="category_<? echo $i; ?>" title="<? echo $row["ITEM_CATEGORY_ID"];?>"><? echo $item_category[$row["ITEM_CATEGORY_ID"]]; ?></td>
				<td id="group_<? echo $i; ?>" title="<? echo $row["ITEM_GROUP_ID"]; ?>"><? echo $item_group_arr[$row["ITEM_GROUP_ID"]]; ?></td>
				<td id="description_<? echo $i; ?>"><? echo $row["ITEM_DESCRIPTION"]; ?></td>
				<td id="size_<? echo $i; ?>"><? echo $row["ITEM_SIZE"]; ?></td>
				<td id="subGroup_<? echo $i; ?>"><? echo $row["SUB_GROUP_NAME"]; ?></td>
				<td id="itemNumber_<? echo $i; ?>"><? echo $row["ITEM_NUMBER"]; ?></td>
				<td id="itemCode_<? echo $i; ?>"><? echo $row["ITEM_CODE"]; ?></td>
				<td id="uom_<? echo $i; ?>" title="<? echo $row["UOM"]; ?>"><? echo $unit_of_measurement[$row["UOM"]]; ?></td>
				<td id="woPiReqQnty_<? echo $i; ?>" align="right"><? echo number_format($row["QUANTITY"],4,'.',''); ?></td>
				<td id="tdreceiveqnty_<? echo $i; ?>" title="<?= $qnty;?>">
					<input type="text" name="receiveqnty[]" id="receiveqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px;" value="<? echo number_format($qnty,4,'.',''); ?>" onBlur="calculate(<? echo $i; ?>);"/>
				</td>
				<td id="lot_<? echo $i; ?>">
					<input type="text" name="txtLot[]" id="txtLot_<? echo $i; ?>" class="text_boxes" style="width:55px;" value="<? echo $row["LOT"]; ?>"/>
				</td>
				<td id="rate_<? echo $i; ?>" title="<? echo $row["NET_PI_RATE"]; ?>">
					<input type="text" name="txtRate[]" id="txtRate_<? echo $i; ?>" class="text_boxes_numeric" style="width:55px;" value="<? echo number_format($row["NET_PI_RATE"],6,'.',''); ?>"  onBlur="calculate(<? echo $i; ?>);" readonly disabled/>
				</td>
				<td id="ilePersent_<? echo $i; ?>" title="<? echo $ile_cost; ?>" align="right"><? echo number_format($ile_cost,2,'.',''); ?></td>
				<td id="amount_<? echo $i; ?>" title="<? echo $amount; ?>" align="right"><? echo number_format($amount,4,'.',''); ?></td>
				<td id="prevRcvQnty_<? echo $i; ?>" align="right"><? echo number_format($prev_rcv_qnty,4,'.',''); ?></td>
				<td id="woPiBalQnty_<? echo $i; ?>" name="woPiBalQnty[]" title="<? echo $qnty; ?>" align="right"><? echo number_format($qnty,4,'.',''); ?></td>
				<td id="tdcomments_<? echo $i; ?>">
					<input type="text" name="comments[]" id="comments_<? echo $i; ?>" class="text_boxes" style="width:90px;" value="<? echo $row['COMMENTS']; ?>" />
				</td>
				<td id="bookCurrency_<? echo $i; ?>" title="<? echo ($amount*$exchange_rate); ?>" align="right"><? echo number_format(($amount*$exchange_rate),4,'.',''); ?></td>
				<td id="tdWarrantyExpDate_<? echo $i; ?>">
					<input type="text" name="txtWarrentyExpDate[]" id="txtWarrentyExpDate_<? echo $i; ?>" class="datepicker" style="width:55px" value="" placeholder="Select Date"/>
				</td>
				<td id="serial_<? echo $i; ?>">
					<input type="text" name="txtSerial[]" id="txtSerial_<? echo $i; ?>" class="text_boxes" style="width:55px;" placeholder="Double Click" onDblClick="popup_serial(<? echo $i; ?>)" />
		            <input name="txtSerialQty[]" id="txtSerialQty_<? echo $i; ?>" type="hidden" />
				</td>
				<td id="brand_<? echo $i; ?>"><? echo $row["BRAND"]; ?></td>
				<td id="origin_<? echo $i; ?>" title="<? echo $row["ORIGIN"];?>"><? echo $row["ORIGIN"]; ?></td>
				<td id="model_<? echo $i; ?>"><? echo $row["MODEL"]; ?></td>
                <td align="center" id="floor_td_to" class="floor_td_to"><p>
					<?
					$argument = "'".$i.'_0'."'";
					echo create_drop_down( "cbo_floor_to_$i", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_room(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'cbo_floor_to');",0,"","","","","","","cbo_floor_to[]" ,"onchange_void"); ?>
                </p></td>
                <td align="center" id="room_td_to"><p>
					<? $argument = "'".$i.'_1'."'";
					echo create_drop_down( "cbo_room_to_$i", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_rack(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'cbo_room_to');",0,"","","","","","","cbo_room_to[]","onchange_void" ); ?>
                </p>
                </td>
                <td align="center" id="rack_td_to"><p>
					<? $argument = "'".$i.'_2'."'";
					echo create_drop_down( "txt_rack_to_$i", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_shelf(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'txt_rack_to');",0,"","","","","","","txt_rack_to[]","onchange_void" ); ?>
                </p></td>
                <td align="center" id="shelf_td_to"><p>
					<? $argument = "'".$i.'_3'."'";
					echo create_drop_down( "txt_shelf_to_$i", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_bin(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'txt_shelf_to');",0,"","","","","","","txt_shelf_to[]","onchange_void" ); ?>
                </p></td>
                <td align="center" id="bin_td_to"><p>
					<? $argument = "'".$i.'_4'."'"; 
					echo create_drop_down( "txt_bin_to_$i", 50,$blank_array,"", 1, "--Select--", 0, "copy_all($argument);",0,"","","","","","","txt_bin_to[]","onchange_void" ); ?>
                </p></td>
                </td>
			</tr>
			<?
			$i++;
		}
    }
	exit();
}

if( $action == 'show_fabric_desc_listview_update' ) 
{
	$data=explode("**",$data);
	//print_r($data);
	$bookingNo_piId  = $data[0];
	$receive_basis   = $data[1];
	$company         = $data[2];
	$cbo_currency_id = $data[3];
	$exchange_rate   = $data[4];
	$source          = $data[5];
	$mst_id          = $data[6];
	$store_id        = $data[7];
	$store_cond="";
	if($store_id) $store_cond=" and b.store_id=$store_id";

	$item_group_arr=return_library_array("select id, item_name from lib_item_group where status_active=1","id","item_name");

	$cr_date=date("d-m-Y");
	$exchange_rate=set_conversion_rate(2,$cr_date,$company);

	$sql_ile="select CATEGORY, ITEM_GROUP, SOURCE, STANDARD from variable_inv_ile_standard where source='$source' and company_name=$company and category in(".implode(",",array_flip($general_item_category)).") and status_active=1 and is_deleted=0 and variable_list=8 order by id desc";
	$sql_ile_result = sql_select($sql_ile);
	$variable_ile_standard=array();
	foreach ($sql_ile_result as $row) {
		$variable_ile_standard[$row["CATEGORY"]][$row["ITEM_GROUP"]][$row["SOURCE"]]=$row["STANDARD"];
	}
	
	$prev_entry="select pi_wo_req_dtls_id as PI_WO_REQ_DTLS_ID, PROD_ID, sum(ORDER_QNTY) as PREV_QUANTITY   
	from inv_transaction
	where company_id=$company and item_category in(".implode(",",array_flip($general_item_category)).") and transaction_type=1 and receive_basis=$receive_basis and pi_wo_batch_no='$bookingNo_piId' and status_active=1 and is_deleted=0 and mst_id<>$mst_id
	group by pi_wo_req_dtls_id, prod_id";
	//echo $prev_entry;
	$prev_entry_result=sql_select($prev_entry);
	$prev_data=array();
	foreach($prev_entry_result as $row)
	{
		$prev_data[$row["PROD_ID"]]+=$row["PREV_QUANTITY"];
	}

	if($receive_basis==1)
	{
		$sql="select a.ID, a.SOURCE, b.id as PI_WO_REQ_DTLS_ID, b.ITEM_PROD_ID, b.UOM, b.QUANTITY, b.NET_PI_RATE, b.NET_PI_AMOUNT, c.ITEM_CATEGORY_ID, c.ITEM_GROUP_ID, c.ITEM_DESCRIPTION, c.ITEM_SIZE, c.SUB_GROUP_NAME, c.ITEM_NUMBER, c.ITEM_CODE, c.conversion_factor as CONVERSION_FACTOR
		from com_pi_master_details a, com_pi_item_details b, product_details_master c
		where a.id=b.pi_id and b.item_prod_id=c.id and a.id='$bookingNo_piId' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	}
	else if($receive_basis==2)
	{
		$sql = "SELECT a.ID, a.SOURCE, b.PI_WO_REQ_DTLS_ID, b.prod_id as ITEM_PROD_ID, b.order_uom as UOM, b.order_qnty as QUANTITY, b.order_rate as RATE, b.order_amount as AMOUNT, b.BATCH_LOT, b.EXPIRE_DATE, b.remarks as COMMENTS, b.FLOOR_ID, b.ROOM, b.RACK, b.SELF, b.BIN_BOX, c.ITEM_CATEGORY_ID, c.ITEM_GROUP_ID, c.ITEM_DESCRIPTION, c.ITEM_SIZE, c.SUB_GROUP_NAME, c.ITEM_NUMBER, c.ITEM_CODE, c.brand_name as BRAND, c.MODEL, c.ORIGIN, c.conversion_factor as CONVERSION_FACTOR
		from inv_receive_master a, inv_transaction b, product_details_master c
		where a.id=b.mst_id and b.prod_id=c.id and a.id=$mst_id and a.company_id=$company  and a.receive_basis=$receive_basis and a.is_multi=2 and b.item_category in(".implode(",",array_flip($general_item_category)).") and b.transaction_type=1 and b.pi_wo_batch_no='$bookingNo_piId' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	}
	else
	{
		$sql = "select a.ID, a.SOURCE, b.id as PI_WO_REQ_DTLS_ID, b.PRODUCT_ID as ITEM_PROD_ID, b.CONS_UOM as UOM, b.quantity as QUANTITY, b.rate as NET_PI_RATE, b.amount as NET_PI_AMOUNT, c.ITEM_CATEGORY_ID, c.ITEM_GROUP_ID, c.ITEM_DESCRIPTION, c.ITEM_SIZE, c.SUB_GROUP_NAME, c.ITEM_NUMBER, c.ITEM_CODE, c.conversion_factor as CONVERSION_FACTOR
		from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c
		where a.id=b.mst_id and b.product_id=c.id and a.id='$bookingNo_piId' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	}

	$data_array=sql_select($sql);

	$lib_room_rack_shelf_sql = "select b.COMPANY_ID, b.LOCATION_ID, b.STORE_ID, b.FLOOR_ID, b.ROOM_ID, b.RACK_ID, b.SHELF_ID, b.BIN_ID,
	a.floor_room_rack_name as FLOOR_NAME, c.floor_room_rack_name as ROOM_NAME, d.floor_room_rack_name as RACK_NAME, 
	e.floor_room_rack_name as SHELF_NAME, f.floor_room_rack_name as BIN_NAME
	from lib_floor_room_rack_dtls b 
	left join lib_floor_room_rack_mst a on b.floor_id=a.floor_room_rack_id and a.status_active=1 and a.is_deleted=0
	left join lib_floor_room_rack_mst c on b.room_id=c.floor_room_rack_id and c.status_active=1 and c.is_deleted=0 
	left join lib_floor_room_rack_mst d on b.rack_id=d.floor_room_rack_id and d.status_active=1 and d.is_deleted=0 
	left join lib_floor_room_rack_mst e on b.shelf_id=e.floor_room_rack_id and e.status_active=1 and e.is_deleted=0 
	left join lib_floor_room_rack_mst f on b.bin_id=f.floor_room_rack_id and f.status_active=1 and f.is_deleted=0 
	where b.status_active=1 and b.is_deleted=0 and b.company_id=$company $store_cond
	order by a.floor_room_rack_name, c.floor_room_rack_name, d.floor_room_rack_name, e.floor_room_rack_name, f.floor_room_rack_name";
	// echo $lib_room_rack_shelf_sql;die;
	$lib_rrsb_arr=sql_select($lib_room_rack_shelf_sql);
	$lib_floor_arr=array();
	$lib_room_arr=$lib_rack_arr=array();
	$lib_shelf_arr=$lib_bin_arr=array();
	if(!empty($lib_rrsb_arr))
	{
		foreach ($lib_rrsb_arr as $room_rack_shelf_row) 
		{
			$company  = $room_rack_shelf_row["COMPANY_ID"];
			$floor_id = $room_rack_shelf_row["FLOOR_ID"];
			$room_id  = $room_rack_shelf_row["ROOM_ID"];
			$rack_id  = $room_rack_shelf_row["RACK_ID"];
			$shelf_id = $room_rack_shelf_row["SHELF_ID"];
			$bin_id   = $room_rack_shelf_row["BIN_ID"];

			if($floor_id!=""){
				$lib_floor_arr[$floor_id] = $room_rack_shelf_row["FLOOR_NAME"];
			}

			if($floor_id!="" && $room_id!=""){
				$lib_room_arr[$room_id] = $room_rack_shelf_row["ROOM_NAME"];
			}

			if($floor_id!="" && $room_id!="" && $rack_id!=""){
				$lib_rack_arr[$rack_id] = $room_rack_shelf_row["RACK_NAME"];
			}

			if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!=""){
				$lib_shelf_arr[$shelf_id] = $room_rack_shelf_row["SHELF_NAME"];
			}
			if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id!=""){
				$lib_bin_arr[$bin_id] = $room_rack_shelf_row["BIN_NAME"];
			}
		}
	}
	else
	{
		$lib_floor_arr[0]="";
		$lib_room_arr[0]="";
		$lib_rack_arr[0]="";
		$lib_shelf_arr[0]="";
		$lib_bin_arr[0]="";
	}
		
	$i=1;
	foreach($data_array as $row)
	{
		$qnty=0;
		$prev_rcv_qnty = $prev_data_arr[$row["ITEM_PROD_ID"]];
		$qnty = ($row['QUANTITY']-$prev_rcv_qnty);
		$ile_percentage=($variable_ile_standard[$row["ITEM_CATEGORY_ID"]][$row["ITEM_GROUP_ID"]][$row["SOURCE"]])/100;
		$ile_cost = 0; //ile cost = (ile/100)*rate
		if($ile_percentage>0) $ile_cost=number_format(($ile_percentage*$row["RATE"]),$dec_place[5],".","" ); 
		$conversion_factor = $row["CONVERSION_FACTOR"];
		$domestic_rate = return_domestic_rate($row["RATE"], $ile_cost, $exchange_rate, $conversion_factor);
		$book_currency = $qnty*$domestic_rate;
		$amount = $qnty*$row["RATE"];
		//echo $qnty."=".$booking_pi_data[$row["WO_PI_DTLS_ID"]]["book_qnty"]."=".$prev_rcv_qnty."<br>";
		if($qnty>0)
		{
			if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $i; ?>" align="center">
				<td id="sl_<? echo $i; ?>"><? echo $i; ?>
					<input type="hidden" name="updatedtlsid[]" id="updatedtlsid_<? echo $i; ?>" value="">
					<input type="hidden" name="piWoDtlsId[]" id="piWoDtlsId_<? echo $i; ?>" value="<? echo $row["PI_WO_REQ_DTLS_ID"]; ?>">
					<input type="hidden" name="prodId[]" id="prodId_<? echo $i; ?>" value="<? echo $row["ITEM_PROD_ID"]; ?>">
				</td>
                <td id="category_<? echo $i; ?>" title="<? echo $row["ITEM_CATEGORY_ID"];?>"><? echo $item_category[$row["ITEM_CATEGORY_ID"]]; ?></td>
				<td id="group_<? echo $i; ?>" title="<? echo $row["ITEM_GROUP_ID"]; ?>"><? echo $item_group_arr[$row["ITEM_GROUP_ID"]]; ?></td>
				<td id="description_<? echo $i; ?>"><? echo $row["ITEM_DESCRIPTION"]; ?></td>
				<td id="size_<? echo $i; ?>"><? echo $row["ITEM_SIZE"]; ?></td>
				<td id="subGroup_<? echo $i; ?>"><? echo $row["SUB_GROUP_NAME"]; ?></td>
				<td id="itemNumber_<? echo $i; ?>"><? echo $row["ITEM_NUMBER"]; ?></td>
				<td id="itemCode_<? echo $i; ?>"><? echo $row["ITEM_CODE"]; ?></td>
				<td id="uom_<? echo $i; ?>" title="<? echo $row["UOM"]; ?>"><? echo $unit_of_measurement[$row["UOM"]]; ?></td>
				<td id="woPiReqQnty_<? echo $i; ?>" align="right"><? echo number_format($row["QUANTITY"],4,'.',''); ?></td>
				<td id="tdreceiveqnty_<? echo $i; ?>" title="<?= $qnty;?>">
					<input type="text" name="receiveqnty[]" id="receiveqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px;" value="<? echo number_format($qnty,4,'.',''); ?>" onBlur="calculate(<? echo $i; ?>);"/>
				</td>
				<td id="lot_<? echo $i; ?>">
					<input type="text" name="txtLot[]" id="txtLot_<? echo $i; ?>" class="text_boxes" style="width:55px;" value="<? echo $row["BATCH_LOT"]; ?>"/>
				</td>
				<td id="rate_<? echo $i; ?>" title="<? echo $row["RATE"]; ?>">
					<input type="text" name="txtRate[]" id="txtRate_<? echo $i; ?>" class="text_boxes_numeric" style="width:55px;" value="<? echo number_format($row["RATE"],6,'.',''); ?>"  onBlur="calculate(<? echo $i; ?>);" readonly disabled/>
				</td>
				<td id="ilePersent_<? echo $i; ?>" title="<? echo $ile_cost; ?>" align="right"><? echo number_format($ile_cost,2,'.',''); ?></td>
				<td id="amount_<? echo $i; ?>" title="<? echo $amount; ?>" align="right"><? echo number_format($amount,4,'.',''); ?></td>
				<td id="prevRcvQnty_<? echo $i; ?>" align="right"><? echo number_format($prev_rcv_qnty,4,'.',''); ?></td>
				<td id="woPiBalQnty_<? echo $i; ?>" name="woPiBalQnty[]" title="<? echo $qnty; ?>" align="right"><? echo number_format($qnty,4,'.',''); ?></td>
				<td id="tdcomments_<? echo $i; ?>">
					<input type="text" name="comments[]" id="comments_<? echo $i; ?>" class="text_boxes" style="width:90px;" value="<? echo $row['COMMENTS']; ?>" />
				</td>
				<td id="bookCurrency_<? echo $i; ?>" title="<? echo ($amount*$exchange_rate); ?>" align="right"><? echo number_format(($amount*$exchange_rate),4,'.',''); ?></td>
				<td id="tdWarrantyExpDate_<? echo $i; ?>">
					<input type="text" name="txtWarrentyExpDate[]" id="txtWarrentyExpDate_<? echo $i; ?>" class="datepicker" style="width:55px" placeholder="Select Date" value="<? echo $row['EXPIRE_DATE']; ?>"/>
				</td>
				<td id="serial_<? echo $i; ?>">
					<input type="text" name="txtSerial[]" id="txtSerial_<? echo $i; ?>" class="text_boxes" style="width:55px;" placeholder="Double Click" onDblClick="popup_serial(<? echo $i; ?>)" />
		            <input name="txtSerialQty[]" id="txtSerialQty_<? echo $i; ?>" type="hidden" />
				</td>
				<td id="brand_<? echo $i; ?>"><? echo $row["BRAND"]; ?></td>
				<td id="origin_<? echo $i; ?>" title="<? echo $row["ORIGIN"];?>"><? echo $row["ORIGIN"]; ?></td>
				<td id="model_<? echo $i; ?>"><? echo $row["MODEL"]; ?></td>
                <td align="center" id="floor_td_to" class="floor_td_to"><p>
					<?
					$argument = "'".$i.'_0'."'";
					echo create_drop_down( "cbo_floor_to_$i", 50,$lib_floor_arr,"", 1, "--Select--", $row["FLOOR_ID"], "fn_load_room(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'cbo_floor_to');",0,"","","","","","","cbo_floor_to[]" ,"onchange_void"); ?>
                </p></td>
                <td align="center" id="room_td_to"><p>
					<? $argument = "'".$i.'_1'."'";
					echo create_drop_down( "cbo_room_to_$i", 50,$lib_room_arr,"", 1, "--Select--", $row["ROOM"], "fn_load_rack(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'cbo_room_to');",0,"","","","","","","cbo_room_to[]","onchange_void" ); ?>
                </p>
                </td>
                <td align="center" id="rack_td_to"><p>
					<? $argument = "'".$i.'_2'."'";
					echo create_drop_down( "txt_rack_to_$i", 50,$lib_rack_arr,"", 1, "--Select--", $row["RACK"], "fn_load_shelf(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'txt_rack_to');",0,"","","","","","","txt_rack_to[]","onchange_void" ); ?>
                </p></td>
                <td align="center" id="shelf_td_to"><p>
					<? $argument = "'".$i.'_3'."'";
					echo create_drop_down( "txt_shelf_to_$i", 50,$lib_shelf_arr,"", 1, "--Select--", $row["SELF"], "fn_load_bin(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'txt_shelf_to');",0,"","","","","","","txt_shelf_to[]","onchange_void" ); ?>
                </p></td>
                <td align="center" id="bin_td_to"><p>
					<? $argument = "'".$i.'_4'."'"; 
					echo create_drop_down( "txt_bin_to_$i", 50,$lib_bin_arr,"", 1, "--Select--", $row["BIN_BOX"], "copy_all($argument);",0,"","","","","","","txt_bin_to[]","onchange_void" ); ?>
                </p></td>
                </td>
			</tr>
			<?
			$i++;
		}	
	}
	exit();
}

if($action=="show_ile")
{
	$ex_data = explode("**",$data);
	$company = $ex_data[0];
	$source = $ex_data[1];
	$rate = $ex_data[2];
	$category = $ex_data[3];
	$group = $ex_data[4];
	
	$sql="select STANDARD from variable_inv_ile_standard where company_name=$company and source=$source and category=$category and item_group=$group and status_active=1 and is_deleted=0 and rownum < 2 order by id desc";
	//echo $sql;die;
	$result=sql_select($sql);
	foreach($result as $row)	{
		$ile = $row["STANDARD"];
		$ile_percentage = number_format( (($row["STANDARD"]/100)*$rate),$dec_place[5],".","" );
		echo $ile."**".$ile_percentage;
	}
	exit();
}

if($action=="serial_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
 	if(str_replace("'","",$serialString)!="")
	{
		$mainEx = explode("**",str_replace("'","",$serialString)); 
		$serialArr = explode(",",$mainEx[0]);
		$qntyArr = explode(",",$mainEx[1]);
	}	
	?>
	<script>
	function add_break_down_tr(i) 
	{
		var row_num=$('#tbl_serial tr').length-1;
		if (row_num!=i)
		{
			return false;
		}
		else
		{
			i++;			
			$("#tbl_serial tr:last").clone().find("input,select").each(function() {
				$(this).attr({
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				  'name': function(_, name) { return name + i },
				  'value': function(_, value) { return value }       
				});  
			  }).end().appendTo("#tbl_serial"); 
			$('#txtSerialNo_'+i).val('');
			$('#txtQuantity_'+i).removeClass("class").addClass("class","text_boxes_numeric");
   			$('#btnIncrease_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
			$('#btnDecrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
			$('#txtSerialNo_'+i).removeAttr("onBlur").attr("onBlur","fn_check_serial("+i+")");	
 		}
	}
	
	function fn_deletebreak_down_tr(rowNo) 
	{   
		var numRow = $('table#tbl_serial tbody tr').length; 
		if(numRow==rowNo && rowNo!=1)
		{
			$('#tbl_serial tbody tr:last').remove();
		}
 	}
	
	function fnClosed() 
	{   
		var numRow = $('table#tbl_serial tr').length;  
		var serialS="";
		var qntyS="";
		for(var i=1;i<numRow;i++)
		{
 			if(i*1>1){ serialS+=","; qntyS+=","; }
			serialS+=$("#txtSerialNo_"+i).val();
			qntyS+=$("#txtQuantity_"+i).val();
			if( form_validation('txtSerialNo_'+i,'Serial')==false )
			{
				return;
			}
		}
		var txtString = serialS;//+"**"+qntyS;
		$("#txt_string").val(txtString);
		$("#txt_qty").val(qntyS);
		parent.emailwindow.hide();
 	}
	
	function fn_check_serial(rowNo) 
	{
		if(rowNo!=1)
		{
			var table_length = $('#tbl_serial tr').length;
			for(var i=1; i<=rowNo-1; i++)
			{
				if(($('#txtSerialNo_'+i).val()*1)==($('#txtSerialNo_'+rowNo).val()*1))
				{
					$('#txtSerialNo_'+rowNo).val("");
				}
			}
		}
 	}
	</script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
	<form name="searchlcfrm_1" id="searchlcfrm_1" autocomplete="off">
		<table width="450" cellspacing="0" cellpadding="0" border="0" class="rpt_table" id="tbl_serial" >
				<thead>
					<tr>                	 
						<th width="260" class="must_entry_caption">Serial No</th>
                        <th width="80">Quantity</th>
 						<th width="120">Action</th> 
					</tr>
				</thead>
				<tbody>
                	<?
 						$chkNo = sizeof($serialArr);
						if(!empty($serialArr[0]))
						{ 
 							for($j=1;$j<=$chkNo;$j++)
							{
								?>
								<tr>
									<td>
										<input type="text" id="txtSerialNo_<? echo $j;?>" name="txtSerialNo_<? echo $j;?>" style="width:250px" class="text_boxes" value="<? echo $serialArr[$j-1]; ?>" onBlur="fn_check_serial(<? echo $j;?>)" />
									</td>
									<td>
										<input type="text" id="txtQuantity_<? echo $j;?>" name="txtQuantity_<? echo $j;?>" style="width:70px" class="text_boxes_numeric" value="<? echo $qntyArr[$j-1]; ?>" disabled />
									</td>
									<td>				
										<input type="button" id="btnIncrease_<? echo $j;?>" name="btnIncrease_<? echo $j;?>" class="formbutton" style="width:40px" onClick="add_break_down_tr(<? echo $j;?>)" value="+" />
										<input type="button" id="btnDecrease_<? echo $j;?>" name="btnDecrease_<? echo $j;?>" class="formbutton" style="width:40px" onClick="fn_deletebreak_down_tr(<? echo $j;?>)" value="-" />
									</td> 
								</tr> 
								<?	
							}
 						}
						else
						{
							?>	
                            <tr>
                                <td>
                                    <input type="text" id="txtSerialNo_1" name="txtSerialNo_1" style="width:250px" class="text_boxes" value=""  onBlur="fn_check_serial(1)" />
                                </td>
                                <td>
                                    <input type="text" id="txtQuantity_1" name="txtQuantity_1" style="width:70px" class="text_boxes_numeric" value="1" disabled />
                                </td>
                                <td>				
                                    <input type="button" id="btnIncrease_1" name="btnIncrease_1" class="formbutton" style="width:40px" onClick="add_break_down_tr(1)" value="+" />
                                    <input type="button" id="btnDecrease_1" name="btnDecrease_1" class="formbutton" style="width:40px" onClick="fn_deletebreak_down_tr(1)" value="-" />
                                </td> 
                            </tr> 
                    		<? 
						} 
						?>
				</tbody>         
			</table>  
            <div><input type="button" name="btn_close" class="formbutton" style="width:100px" value="Close" onClick="fnClosed()" /></div>  
            <!-- Hidden field here -->
			<input type="hidden" id="txt_string" value="" />
            <input type="hidden" id="txt_qty" value="" />				 
			<!-- END --> 
			</form>
	   </div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="addi_info_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);

	$user_info_arr=return_library_array("SELECT a.id, a.user_full_name, b.custom_designation from user_passwd a, lib_designation b where a.designation = b.id and a.valid = 1 order by a.user_full_name","id","user_full_name");


 	if(str_replace("'","",$pre_addi_info))
	{
		$pre_addi_info_arr = explode("_",str_replace("'","",$pre_addi_info)); 
		$pre_txt_book_no = $pre_addi_info_arr[0];
		$txt_challan_date = $pre_addi_info_arr[1];
		$txt_bill_no = $pre_addi_info_arr[2];
		$txt_bill_date = $pre_addi_info_arr[3];
		$cbo_purchaser_name = $pre_addi_info_arr[4];
		$cbo_carried_by = $pre_addi_info_arr[5];
		$cbo_qc_check_by = $pre_addi_info_arr[6];
		$cbo_receive_by = $pre_addi_info_arr[7];
		$cbo_gate_entry_by = $pre_addi_info_arr[8];

		$cbo_purchaser_name_show = $user_info_arr[$pre_addi_info_arr[4]];
		$cbo_carried_by_show = $user_info_arr[$pre_addi_info_arr[5]];
		$cbo_qc_check_by_show = $user_info_arr[$pre_addi_info_arr[6]];
		$cbo_receive_by_show = $user_info_arr[$pre_addi_info_arr[7]];
		$cbo_gate_entry_by_show = $user_info_arr[$pre_addi_info_arr[8]];

		$txt_gate_entry_date = $pre_addi_info_arr[9];
		$txt_addi_receive_date = $pre_addi_info_arr[10];
		$txt_gate_entry_no = $pre_addi_info_arr[11];
		$txt_store_sl_no = $pre_addi_info_arr[12];

	}
	
	?>
	<script>

	function fnClosed() 
	{   var txtString = "";
		txtString = $("#txt_book_no").val() + '_' + $("#txt_challan_date").val() + '_' + $("#txt_bill_no").val() + '_' + $("#txt_bill_date").val() + '_' + $("#cbo_purchaser_name").val() + '_' + $("#cbo_carried_by").val() + '_' + $("#cbo_qc_check_by").val() + '_' + $("#cbo_receive_by").val() + '_' + $("#cbo_gate_entry_by").val() + '_' + $("#txt_gate_entry_date").val()+ '_' + $("#txt_addi_receive_date").val()+ '_' + $("#txt_gate_entry_no").val()+ '_' + $("#txt_store_sl_no").val();
		$("#txt_string").val(txtString);
		parent.emailwindow.hide();
 	}

 	function openmypage_user_info(field_id)
	{
		var title = "User Info Popup";
		var pre_addi_info = $('#txt_addi_info').val();
		page_link='general_item_receive_v2_controller.php?action=user_info_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=410px, height=250px, center=1, resize=0, scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var user_id=this.contentDoc.getElementById("user_id").value;
			var txt_name=this.contentDoc.getElementById("txt_name").value;
			$('#'+field_id).val(user_id);
			$('#'+field_id+'_show').val(txt_name);
		}		
	}

	</script>
	</head>
	<body>
		<div align="center" style="width:100%;" >
			<br>
			<form name="searchlcfrm_1" id="searchlcfrm_1" autocomplete="off">
				<fieldset style="width:650px;">   
				<table  width="650" cellspacing="2" cellpadding="0" border="0" >
					<tr>
						<td width="100">
							<b>Rcvd/Book No.</b>
						</td>
						<td>
							<input type="text" id="txt_book_no" name="txt_book_no" style="width:150px" class="text_boxes" value="<? echo $pre_txt_book_no; ?>" />
						</td>
						
						<td width="100">
							<b>Receive Date</b>
						</td>
						<td>
							<input type="text" id="txt_addi_receive_date" name="txt_addi_receive_date" style="width:150px" class="datepicker" value="<? echo $txt_addi_receive_date ; ?>" readonly />
						</td>
					</tr>
					<tr>
						<td width="100">
							<b>Challan Date</b>
						</td>
						<td>
							<input type="text" id="txt_challan_date" name="txt_challan_date" style="width:150px" class="datepicker" value="<? echo $txt_challan_date ; ?>" readonly />
						</td>
						<td width="100">
							<b>Bill No.</b>
						</td>
						<td>
							<input type="text" id="txt_bill_no" name="txt_bill_no" style="width:150px" class="text_boxes" value="<? echo $txt_bill_no; ?>" />
						</td>
					</tr>
					<tr>
						<td width="100">
							<b>Bill Date</b>
						</td>
						<td>
							<input type="text" id="txt_bill_date" name="txt_bill_date" style="width:150px" class="datepicker" value="<? echo $txt_bill_date; ?>" readonly />
						</td>
						<td width="100">
							<b>Purchaser Name</b>
						</td>
						<td>
							<?
							//echo create_drop_down( "cbo_purchaser_name", 160, "select a.id, a.user_full_name from user_passwd a where a.valid = 1 order by a.user_full_name","id,user_full_name", 1, "-- Select --", $cbo_purchaser_name, "" );
							?>
							<input type="text" class="text_boxes" id="cbo_purchaser_name_show" value="<? echo $cbo_purchaser_name_show;?>" onDblClick="openmypage_user_info('cbo_purchaser_name')" style="width: 150px" placeholder="Browse" readonly >
							<input type="hidden" class="text_boxes" id="cbo_purchaser_name" value="<? echo $cbo_purchaser_name;?>" >
						</td>
					</tr>
					<tr>
						<td width="100">
							<p><b>Carried By</b>(Deliveried By)</p>
						</td>
						<td>
							<?
							//echo create_drop_down( "cbo_carried_by", 160, "select a.id, a.user_full_name from user_passwd a where a.valid = 1 order by a.user_full_name","id,user_full_name", 1, "-- Select --", $cbo_carried_by, "" );
							?>
							<input type="text" class="text_boxes" id="cbo_carried_by_show" value="<? echo $cbo_carried_by_show;?>" onDblClick="openmypage_user_info('cbo_carried_by')" style="width: 150px" placeholder="Browse" readonly >
							<input type="hidden" class="text_boxes" id="cbo_carried_by" value="<? echo $cbo_carried_by;?>" >
						</td>
						<td width="100">
							<b>QC Check By</b>
						</td>
						<td>
							<?
							//echo create_drop_down( "cbo_qc_check_by", 160, "select a.id, a.user_full_name from user_passwd a where a.valid = 1 order by a.user_full_name","id,user_full_name", 1, "-- Select --", $cbo_qc_check_by, "" );
							?>
							<input type="text" class="text_boxes" id="cbo_qc_check_by_show" value="<? echo $cbo_qc_check_by_show;?>" onDblClick="openmypage_user_info('cbo_qc_check_by')" style="width: 150px" placeholder="Browse" readonly >
							<input type="hidden" class="text_boxes" id="cbo_qc_check_by" value="<? echo $cbo_qc_check_by;?>" >
						</td>

					</tr>
					<tr>
						<td width="100">
							<b>Received By</b>
						</td>
						<td>
							<?
							//echo create_drop_down( "cbo_receive_by", 160, "select a.id, a.user_full_name from user_passwd a where a.valid = 1 order by a.user_full_name","id,user_full_name", 1, "-- Select --", $cbo_receive_by, "" );
							?>
							<input type="text" class="text_boxes" id="cbo_receive_by_show" value="<? echo $cbo_receive_by_show;?>" onDblClick="openmypage_user_info('cbo_receive_by')" style="width: 150px" placeholder="Browse" readonly >
							<input type="hidden" class="text_boxes" id="cbo_receive_by" value="<? echo $cbo_receive_by;?>" >
						</td>
						<td width="100">
							<b>Gate Entry No</b>
						</td>
						<td>
							<input type="text" id="txt_gate_entry_no" name="txt_gate_entry_no" class="text_boxes" style="width:150px" value="<? echo $txt_gate_entry_no; ?>" />
						</td>
					</tr>
					<tr>
						<td width="100">
							<b>Gate Entry By</b>
						</td>
						<td>
							<?
							//echo create_drop_down( "cbo_gate_entry_by", 160, "select a.id, a.user_full_name from user_passwd a where a.valid = 1 order by a.user_full_name","id,user_full_name", 1, "-- Select --", $cbo_gate_entry_by, "" );
							?>
							<input type="text" class="text_boxes" id="cbo_gate_entry_by_show" value="<? echo $cbo_gate_entry_by_show;?>" onDblClick="openmypage_user_info('cbo_gate_entry_by')" style="width: 150px" readonly placeholder="Browse">
							<input type="hidden" class="text_boxes" id="cbo_gate_entry_by" value="<? echo $cbo_gate_entry_by;?>" >
						</td>

						<td width="100">
							<b>Gate Entry Date</b>
						</td>
						<td>
							<input type="text" id="txt_gate_entry_date" name="txt_gate_entry_date" style="width:150px" class="datepicker" value="<? echo $txt_gate_entry_date; ?>" readonly />
						</td>

					</tr>
					<tr>
						<td width="100">
							<b>Store Sl No.</b>
						</td>
						<td>
							<input type="text" class="text_boxes" id="txt_store_sl_no" value="<? echo $txt_store_sl_no;?>" style="width: 150px">
						</td>
					</tr>

				</table>
				<br>  
	            <div><input type="button" name="btn_close" class="formbutton" style="width:100px" value="Close" onClick="fnClosed()" /></div>

				<input type="hidden" id="txt_string" value="" />
				<br>
				</fieldset>
			</form>
		</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action == "user_info_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>

	function js_set_value(str) 
	{  
		var splitArr = str.split("_");
		$("#user_id").val(splitArr[0]);
		$("#txt_name").val(splitArr[1]);
		parent.emailwindow.hide(); 
 	}

	</script>
	</head>
	<body>
		<div align="center" style="width:100%;" >
			<br>
			<form name="searchlcfrm_1" id="searchlcfrm_1" autocomplete="off">
				<?
				$sql="SELECT a.id,a.user_name, a.user_full_name, b.custom_designation from user_passwd a, lib_designation b where a.designation = b.id and a.valid = 1 order by a.user_full_name";
				echo  create_list_view ( "list_view","User Id, User Full Name,Designation", "70,130,140","370","200",0, $sql, "js_set_value", "id,user_full_name", "", 1, "0,0,0", $arr, "user_name,user_full_name,custom_designation", "", 'setFilterGrid("list_view",-1);'); 
				?>
				<input type="hidden" id="user_id" name="user_id">
				<input type="hidden" id="txt_name" name="txt_name">
			</form>
		</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if( $action == 'mrr_details' ) 
{
	//echo $data;die;
	?>
    <tr id="row_1" align="center">
        <td id="sl_1"></td>
        <td id="category_1"></td>
        <td id="group_1"></td>
        <td id="description_1"></td>
        <td id="size_1"></td>
        <td id="subGroup_1"></td>
        <td id="itemNumber_1"></td>
        <td id="itemCode_1"></td>
        <td id="uom_1"></td>
        <td id="woPiReqQnty_1"></td>
        <td id="tdreceiveqnty_1"><input type="text" name="receiveqnty[]" id="receiveqnty_1" class="text_boxes_numeric" style="width:50px;" value="" onBlur="calculate(1);"/></td>
        <td id="rate_1"><input type="text" name="txtRate[]" id="txtRate_1" class="text_boxes_numeric" style="width:40px;" value="" onBlur="calculate(1);"/></td>
        <td id="ile_1"></td>
        <td id="amount_1"></td>
        <td id="prevRcvQnty_1"></td>
        <td id="BalWoPiReqQnty_1"></td>
        <td id="Comments_1"><input type="text" name="txtComments[]" id="txtComments_1" class="text_boxes" style="width:60px;" value="" /></td>
        <td id="consRate_1"></td>
        <td id="bookCurrency_1"></td>
        <td id="warentyExpDate_1"><input type="text" name="txtWarentyExpDate[]" id="txtWarentyExpDate_1" class="datepicker" style="width:60px;" value="" /></td>
        <td id="brand_1"></td>
        <td id="origin_1"></td>
        <td id="model_1"></td>
         <td align="center" id="floor_td_to" class="floor_td_to"><p>
        <? 
        $i=1;
        $argument = "'".$i.'_0'."'";
        echo create_drop_down( "cbo_floor_to_$i", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_room(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'cbo_floor_to');",0,"","","","","","","cbo_floor_to[]" ,"onchange_void"); ?>
        </p></td>
        <td align="center" id="room_td_to"><p>
        <? $argument = "'".$i.'_1'."'";
        echo create_drop_down( "cbo_room_to_$i", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_rack(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'cbo_room_to');",0,"","","","","","","cbo_room_to[]","onchange_void" ); ?>
        </p>
        </td>
        <td align="center" id="rack_td_to"><p>
        <? $argument = "'".$i.'_2'."'";
        echo create_drop_down( "txt_rack_to_$i", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_shelf(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'txt_rack_to');",0,"","","","","","","txt_rack_to[]","onchange_void" ); ?>
        </p></td>
        <td align="center" id="shelf_td_to"><p>
        <? $argument = "'".$i.'_3'."'";
        echo create_drop_down( "txt_shelf_to_$i", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_bin(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'txt_shelf_to');",0,"","","","","","","txt_shelf_to[]","onchange_void" ); ?>
        </p></td>
        <td align="center" id="bin_td_to"><p>
        <? $argument = "'".$i.'_4'."'"; 
        echo create_drop_down( "txt_bin_to_$i", 50,$blank_array,"", 1, "--Select--", 0, "copy_all($argument);",0,"","","","","","","txt_bin_to[]","onchange_void" ); ?>
        </p>
        <input type="hidden" name="updatedtlsid[]" id="updatedtlsid_1" value="" readonly>
        <input type="hidden" name="piWoDtlsId[]" id="piWoDtlsId_1" value="" readonly>
        <input type="hidden" name="previousprodid[]" id="previousprodid_1" value="" readonly>
        </td>
    </tr>
    <?
	exit();
}


if($action=="check_conversion_rate") //Conversion Exchange Rate
{ 
	$data=explode("**",$data);
	if($db_type==0)
	{
		$conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
	}
	else
	{
		$conversion_date=change_date_format($data[1], "d-M-y", "-",1);
	}
	$currency_rate=set_conversion_rate( $data[0], $conversion_date, $data[2] );
	echo "1"."_".$currency_rate;
	exit();	
}


if($action=="floor_list")
{
	$data_ref=explode("__",$data);
	$floor_arr=array();
	$location_cond = "";
	if($data_ref[2]) $location_cond = "and b.location_id='$data_ref[2]'";
	$floor_data=sql_select("select b.floor_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.floor_id and b.store_id='$data_ref[1]' and a.company_id='$data_ref[0]' $location_cond and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc");
	foreach($floor_data as $row)
	{
		$floor_arr[$row[csf('floor_id')]]=$row[csf('floor_room_rack_name')];
	}
	$jsFloor_arr= json_encode($floor_arr);
	echo $jsFloor_arr;
	die();
}

if($action=="room_list")
{
	$data_ref=explode("__",$data); // com_id + "__" + location_id + "__" + store_id + "__" + floor_id;
	$room_arr=array();
	$location_cond = "";
	if($data_ref[1]) $location_cond = "and b.location_id='$data_ref[1]'";
	$room_data=sql_select("select b.room_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id and b.store_id='$data_ref[2]' and a.company_id='$data_ref[0]' and b.floor_id='$data_ref[3]' $location_cond and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	group by b.room_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc");
	foreach($room_data as $row)
	{
		$room_arr[$row[csf('room_id')]]=$row[csf('floor_room_rack_name')];
	}
	$jsRoom_arr= json_encode($room_arr);
	echo $jsRoom_arr;
	die();
}

if($action=="rack_list")
{
	$data_ref=explode("__",$data); // com_id + "__" + location_id + "__" + store_id + "__" + floor_id;
	$rack_arr=array();
	$location_cond = "";
	if($data_ref[1]) $location_cond = "and b.location_id='$data_ref[1]'";
	$rack_data=sql_select("select b.rack_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id and b.store_id='$data_ref[2]' and a.company_id='$data_ref[0]' and b.room_id='$data_ref[3]' $location_cond and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	group by b.rack_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc");
	foreach($rack_data as $row)
	{
		$rack_arr[$row[csf('rack_id')]]=$row[csf('floor_room_rack_name')];
	}
	$jsRack_arr= json_encode($rack_arr);
	echo $jsRack_arr;
	die();
}

if($action=="shelf_list")
{
	$data_ref=explode("__",$data); // com_id + "__" + location_id + "__" + store_id + "__" + floor_id;
	$shelf_arr=array();
	$location_cond = "";
	if($data_ref[1]) $location_cond = "and b.location_id='$data_ref[1]'";
	$shelf_data=sql_select("select b.shelf_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.shelf_id and b.store_id='$data_ref[2]' and a.company_id='$data_ref[0]' and b.rack_id='$data_ref[3]' $location_cond and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	group by b.shelf_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc");
	foreach($shelf_data as $row)
	{
		$shelf_arr[$row[csf('shelf_id')]]=$row[csf('floor_room_rack_name')];
	}
	$jsShelf_arr= json_encode($shelf_arr);
	echo $jsShelf_arr;
	die();
}

if($action=="bin_list")
{
	$data_ref=explode("__",$data); // com_id + "__" + location_id + "__" + store_id + "__" + floor_id;
	$bin_arr=array();
	$location_cond = "";
	if($data_ref[1]) $location_cond = "and b.location_id='$data_ref[1]'";
	$bin_data=sql_select("select b.bin_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.bin_id and b.store_id='$data_ref[2]' and a.company_id='$data_ref[0]' and b.shelf_id='$data_ref[3]' $location_cond and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	group by b.bin_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc");
	foreach($bin_data as $row)
	{
		$bin_arr[$row[csf('bin_id')]]=$row[csf('floor_room_rack_name')];
	}
	$jsBin_arr= json_encode($bin_arr);
	echo $jsBin_arr;
	die();
}


if ($action == "load_drop_down_party")
{
	echo create_drop_down("cbo_party", 142, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data' and b.party_type=91 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
	exit();
}


if($action=="get_library_exchange_rate")
{
	$data_ref=explode("**",$data);
	$exchange_rate=sql_select("select conversion_rate from currency_conversion_rate where currency=$data_ref[0] and COMPANY_ID=$data_ref[1] and status_active=1 and is_deleted=0 order by id desc");
	if($data==1)
	{
		echo "document.getElementById('txt_exchange_rate').value 	= '1';\n";
	}
	else
	{
		echo "document.getElementById('txt_exchange_rate').value 	= '".$exchange_rate[0][csf("conversion_rate")]."';\n";
	}
	exit();
}


if ($action=="wo_pi_popup")
{
	echo load_html_head_contents("WO/PI Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 

	<script>
		var update_id='<? echo $update_id; ?>';
		
		function js_set_value(id,no,data,receive_basis)
		{
			if(update_id!="")
			{
				var response = trim(return_global_ajax_value(update_id, 'duplication_check', '', 'general_item_receive_v2_controller'));
				if(response!="")
				{
					var curr_data=data.split("**");
					var curr_supplier_id=curr_data[0];
					var curr_currency_id=curr_data[1];
					var curr_source=curr_data[2];
					var curr_lc_id=curr_data[4];
					
					var prev_data=response.split("**");
					var prev_supplier_id=prev_data[0];
					var prev_currency_id=prev_data[1];
					var prev_source=prev_data[2];
					var prev_lc_id=prev_data[3];
					
					if(!(curr_supplier_id==prev_supplier_id && curr_currency_id==prev_currency_id && curr_source==prev_source))
					{
						alert("Supplier, Currency and Source Mix not allow in Same Received ID \n");
						//alert("Supplier, Currency and Source Mix not allow in Same Received ID \n"+curr_supplier_id+"=="+prev_supplier_id+"=="+curr_currency_id+"=="+prev_currency_id+"=="+curr_source+"=="+prev_source);
						return;
					}
				}
			}
			//alert("Fuad");return;
			$('#hidden_wo_pi_id').val(id);
			$('#hidden_wo_pi_no').val(no);
			$('#hidden_data').val(data);
			$('#receive_basis').val(receive_basis);
			parent.emailwindow.hide();
		}
	
    </script>

	</head>

	<body onLoad="set_hotkey()">
	<div align="center" style="width:1100px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:990px; margin-left:5px">
		<legend>Enter search words</legend>           
            <table cellpadding="0" cellspacing="0" border="1" rules="all" width="990" class="rpt_table">
                <thead>
                    <th width="150">Receive Basis</th>
                    <th width="130">Receive Purpose</th>
                    <th width="150">Supplier Name</th>
                    <th width="110">GRN</th>
                    <th width="110">WO No</th>
                    <th width="110">PI No</th>
                    <th width="160">WO/PI/GRN Date</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:60px" class="formbutton" />
                        <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
                    	<input type="hidden" name="hidden_wo_pi_id" id="hidden_wo_pi_id" class="text_boxes" value="">  
                        <input type="hidden" name="hidden_wo_pi_no" id="hidden_wo_pi_no" class="text_boxes" value=""> 
                        <input type="hidden" name="booking_without_order" id="booking_without_order" class="text_boxes" value="">
                        <input type="hidden" name="hidden_data" id="hidden_data" class="text_boxes" value="">
                        <input type="hidden" name="receive_basis" id="receive_basis" class="text_boxes" value=""> 
                        <input type="hidden" name="hid_booking_type" id="hid_booking_type" class="text_boxes" value=""> 
                    </th> 
                </thead>
                <tr class="general">
                    <td align="center">	
                    	<? echo create_drop_down("cbo_receive_basis",140,$receive_basis_arr,"",1,"-- Select --",$recieve_basis,"",1,"1,2,19"); ?>
                    </td>
                    <td align="center">	
                    	<? echo create_drop_down("cbo_receive_purpose",120,$yarn_issue_purpose,"",1,"-- Select --",$cbo_receive_purpose,"",1,"2,5,6,7,12,15,16,38,43,46,50,51"); ?>
                    </td>
                    <td align="center" id="supplier_td_id">	
                    	<?
						$sup_cond="";
						
						if(str_replace("'","",$cbo_supplier)>0) $sup_cond=" and a.id=$cbo_supplier"; 
						//echo create_drop_down( "cbo_supplier", 140,"select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company=$cbo_company_id $sup_cond and b.party_type in(4,5) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name",'id,supplier_name', 1, '-- Select Supplier --',0,0);
						echo create_drop_down("cbo_supplier", 142, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company=$cbo_company_id $sup_cond and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
						?>
                    </td>
                    <td align="center">				
                        <input type="text" style="width:100px" class="text_boxes"  name="txt_grn_num" id="txt_grn_num" />	
                    </td>
                    <td align="center">				
                        <input type="text" style="width:100px" class="text_boxes"  name="txt_wo_num" id="txt_wo_num" />	
                    </td>
                    <td align="center">				
                        <input type="text" style="width:100px" class="text_boxes"  name="txt_pi_no" id="txt_pi_no" />	
                    </td> 
                    <td align="center">
                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px">To
					  	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px">
					</td>						
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_wo_num').value+'_'+document.getElementById('cbo_receive_basis').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $cbo_currency_id ?>+'_'+<? echo $cbo_source ?>+'_'+document.getElementById('cbo_supplier').value+'_'+document.getElementById('txt_pi_no').value+'_'+document.getElementById('txt_grn_num').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_receive_purpose').value, 'create_wo_pi_search_list_view', 'search_div', 'general_item_receive_v2_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:60px;" />
                     </td>
                </tr>
                <tr>
                	<td colspan="8" align="center" height="30" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
           </table>
            <div style="margin-top:10px;" id="search_div" align="left"></div> 
		</fieldset>
	</form>
	</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="create_wo_pi_search_list_view")
{
	$data = explode("_",$data);
	//echo $data[1]."jahid";die;
	$wo_num =$data[0];
	$recieve_basis=$data[1];
	$company_id =$data[2];
	$date_form=$data[3];
	$date_to =$data[4];
	$cbo_currency_id =$data[5];
	$cbo_source =$data[6];
	$cbo_supplier =$data[7];
	$pi_num =$data[8];
	$grn_no =$data[9];
	$cbo_year =$data[10];
	$receive_purpose =$data[11];
	//echo $pay_mode.jahid;die;
	
	if($recieve_basis<1){ echo "Please Select Receive Basis.";die;}
	if($wo_num=="" && $pi_num=="" && $grn_no=="" && $date_form=="" && $date_to=="" && $cbo_supplier==0){ echo "Please select date range.";die;}
	
	
	if($date_form!="" && $date_to!="")
	{
		if($db_type==0)
		{
			$date_form=change_date_format($date_form,'yyyy-mm-dd', "-");
			$date_to=change_date_format($date_to,'yyyy-mm-dd', "-");
		}
		else
		{
			$date_form=change_date_format($date_form,'','',1);
			$date_to=change_date_format($date_to,'','',1);
		}
	}
	else
	{
		if($db_type==0){ $year_cond=" and year(a.insert_date)=$cbo_year ";}
		else if($db_type==2){ $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year ";}
	}

	
	//echo $date_form."==".$date_to;die;
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$user_name_arr = return_library_array("select id, user_full_name from user_passwd","id","user_full_name");
	
	if($recieve_basis==1)
	{
		$search_field_cond="";
		if(trim($wo_num)!="")
		{
			$search_field_cond.=" and b.work_order_no like '%$wo_num'";
		}
		
		if(trim($pi_num)!="")
		{
			$search_field_cond.=" and a.pi_number like '$pi_num'";
		}
		
		if($date_form!="" && $date_to!="")
		{
			$search_field_cond.=" and a.pi_date between '$date_form' and '$date_to'";
		}
		
		if($cbo_currency_id>0) $search_field_cond.=" and a.currency_id=$cbo_currency_id";
		if($cbo_source>0) $search_field_cond.=" and a.source=$cbo_source";
		if($cbo_supplier>0) $search_field_cond.=" and a.supplier_id=$cbo_supplier";
		
		$btbLcArr=array();
		$lc_data=sql_select("select a.pi_id, b.id, b.lc_number from com_btb_lc_pi a, com_btb_lc_master_details b where a.status_active=1 and a.is_deleted=0 and a.com_btb_lc_master_details_id=b.id");
		foreach($lc_data as $row)
		{
			$btbLcArr[$row[csf('pi_id')]]=$row[csf('id')]."**".$row[csf('lc_number')];
		}
		
		$approval_status_cond="";
		if($db_type==0)
		{ 
			$approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company_id' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format(date('d-m-Y'),'yyyy-mm-dd')."' and company_id='$company_id')) and page_id=18 and status_active=1 and is_deleted=0";
		}
		else
		{
			$approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company_id' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format(date('d-m-Y'), "", "",1)."' and company_id='$company_id')) and page_id=18 and status_active=1 and is_deleted=0";
		}
		$approval_status=sql_select($approval_status);
		if($approval_status[0][csf('approval_need')]==1)
		{
			$approval_status_cond= "and a.approved = 1";
		}
		
		$sql_receive="SELECT p.ID, a.ID AS PI_ID, a.PI_NUMBER, p.ORDER_QNTY as ORDER_QNTY
		from inv_transaction p, com_pi_master_details a,  com_pi_item_details b  
		where p.PI_WO_BATCH_NO=a.id and a.id=b.pi_id and p.RECEIVE_BASIS=1 and p.item_category=1 and p.transaction_type=1 and a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and p.status_active=1 and p.is_deleted=0 and a.importer_id=$company_id and a.goods_rcv_status<>1 and b.work_order_dtls_id>0 $search_field_cond $approval_status_cond $year_cond
		group by p.id, a.id, a.pi_number, p.ORDER_QNTY";
		//echo $sql_receive;//die;
		$sql_receive_result = sql_select($sql_receive);
		$pi_receive_data=array();$trans_data_check=array();
		foreach($sql_receive_result as $val)
		{
			if($trans_data_check[$val["ID"]]=="")
			{
				$trans_data_check[$val["ID"]]=$val["ID"];
				$pi_receive_data[$val["WO_ID"]]+=$val["ORDER_QNTY"];
			}
		}
		unset($sql_receive_result); 
		//print_r($pi_receive_data);
		$sql = "SELECT a.ID, a.PI_NUMBER, a.SUPPLIER_ID, a.PI_DATE, a.LAST_SHIPMENT_DATE, a.PI_BASIS_ID, a.INTERNAL_FILE_NO, a.CURRENCY_ID, a.SOURCE, a.INSERTED_BY, sum(b.QUANTITY) as QUANTITY  
		from com_pi_master_details a,  com_pi_item_details b  
		where a.id=b.pi_id and a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and a.importer_id=$company_id and a.goods_rcv_status<>1 $search_field_cond $approval_status_cond $year_cond
		group by a.id, a.pi_number, a.supplier_id, a.pi_date, a.last_shipment_date, a.pi_basis_id, a.internal_file_no, a.currency_id, a.source,a.inserted_by 
		order by a.ID desc";
		
		
		//echo $sql;
		$result = sql_select($sql);
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1080" class="rpt_table">
			<thead>
				<tr>
					<th colspan="10"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
				</tr>
				<tr>
					<th width="50">SL</th>
					<th width="135">PI No</th>
					<th width="80">PI Date</th>
					<th width="110">PI Basis</th>               
					<th width="200">Supplier</th>
					<th width="100">Last Shipment Date</th>
					<th width="100">Internal File No</th>
					<th width="80">Currency</th>
					<th width="60">Source</th>
					<th>Insert User</th>
				</tr>
			</thead>
		</table>
		<div style="width:1100px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">	 
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1080" class="rpt_table" id="tbl_list_search">  
			<?
				$i=1;
				foreach ($result as $row)
				{
					$balance_qnty=$row["QUANTITY"]-$pi_receive_data[$row["ID"]];
					if($balance_qnty>0)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						
						$data=$row['SUPPLIER_ID']."**".$row['CURRENCY_ID']."**".$row['SOURCE']; 
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row['ID']; ?>,'<? echo $row['PI_NUMBER']; ?>','<? echo $data; ?>','<? echo $recieve_basis; ?>');"> 
							<td width="50" align="center"><? echo $i; ?></td>
							<td width="135"><p><? echo $row['PI_NUMBER']; ?></p></td>
							<td width="80" align="center"><? echo change_date_format($row['PI_DATE']); ?></td>  
							<td width="110"><? echo $pi_basis[$row['PI_BASIS_ID']]; ?></td>             
							<td width="200"><p><? echo $supplier_arr[$row['SUPPLIER_ID']]; ?>&nbsp;</p></td>
							<td width="100" align="center"><? echo change_date_format($row['LAST_SHIPMENT_DATE']); ?>&nbsp;</td>
							<td width="100"><p><? echo $row['INTERNAL_FILE_NO']; ?></p></td>
							<td width="80"><p><? echo $currency[$row['CURRENCY_ID']]; ?></p></td>
							<td width="60"><p><? echo $source[$row['SOURCE']]; ?></p></td>
							<td ><p><? echo $user_name_arr[$row['INSERTED_BY']]; ?></p></td>
						</tr>
						<?
						$i++;
					}
				}
				?>
			</table>
		</div>
		<?	
	}
	else if($recieve_basis==2)
	{
		/*$search_field_cond="";
		if(trim($wo_num)!="")
		{
			$search_field_cond="and a.WO_NUMBER like '%$wo_num'";
		}
		
		if($date_form!="" && $date_to!="")
		{
			$search_field_cond.=" and a.WO_DATE between '$date_form' and '$date_to'";
		}
		
		if($cbo_currency_id>0) $search_field_cond.=" and a.CURRENCY_ID=$cbo_currency_id";
		if($cbo_source>0) $search_field_cond.=" and a.SOURCE=$cbo_source";
		if($cbo_supplier>0) $search_field_cond.=" and a.SUPPLIER_ID=$cbo_supplier";
		
		*/
		
		if ($receive_purpose == 2 || $receive_purpose == 7 || $receive_purpose == 12 || $receive_purpose == 15 || $receive_purpose == 38 || $receive_purpose == 46 || $receive_purpose == 50 || $receive_purpose == 51 )
		{
			$sql_cond = "";
			if($date_form!="" && $date_to!="")
			{
				$sql_cond.=" and a.booking_date between '$date_form' and '$date_to'";
			}

			if ($wo_num != "") $sql_cond .= " and a.YDW_NO like '%$wo_num'";
			if ($cbo_supplier >0) $sql_cond .= " and a.supplier_id=$cbo_supplier";
			if($cbo_currency_id>0) $sql_cond.=" and a.CURRENCY=$cbo_currency_id";
			if($cbo_source>0) $sql_cond.=" and a.SOURCE=$cbo_source";

			if($receive_purpose == 2){
				$entry_form ="(41,42,114,125,135)";
				$purpose = "";
				$select_purpose = " 2 as SERVICE_TYPE";
				$group_by_service=" ";
			}else{
				$entry_form = "(94,340)";
				$purpose = " and a.SERVICE_TYPE = $receive_purpose";
				$select_purpose = "a.SERVICE_TYPE";
				$group_by_service=" , a.SERVICE_TYPE";
			}

			$sql = "select a.ID, a.YARN_DYEING_PREFIX_NUM as WO_NUMBER_PREFIX_NUM, a.YDW_NO as WO_NUMBER, a.BOOKING_DATE as WO_DATE, a.DELIVERY_DATE as DELIVERY_DATE, a.SUPPLIER_ID, b.JOB_NO, a.ENTRY_FORM, a.BOOKING_WITHOUT_ORDER, a.IS_SALES, a.CURRENCY as CURRENCY_ID, $select_purpose, to_char(a.insert_date,'YYYY') as YEAR, a.INSERTED_BY, a.SOURCE, sum(b.YARN_WO_QTY) as WO_QNTY
			from wo_yarn_dyeing_mst a, WO_YARN_DYEING_DTLS b 
			where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in $entry_form $purpose and a.pay_mode!=2 and a.company_id='$company_id' $sql_cond 
			group by a.ID, a.YARN_DYEING_PREFIX_NUM, a.YDW_NO, a.BOOKING_DATE, a.DELIVERY_DATE, a.SUPPLIER_ID, b.JOB_NO, a.ENTRY_FORM, a.BOOKING_WITHOUT_ORDER, a.IS_SALES, a.CURRENCY $group_by_service, a.insert_date, a.INSERTED_BY, a.SOURCE
			order by a.ID desc";
			
			$sql_receive="SELECT p.ID, a.ID AS WO_ID, a.WO_NUMBER, p.ORDER_QNTY as ORDER_QNTY
			from inv_transaction p, wo_yarn_dyeing_mst a, WO_YARN_DYEING_DTLS b  
			where p.PI_WO_BATCH_NO=a.id and a.id=b.mst_id and p.RECEIVE_BASIS=2 and p.item_category=1 and p.transaction_type=1 and b.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and p.status_active=1 and p.is_deleted=0 and a.COMPANY_NAME=$company_id and a.ENTRY_FORM in $entry_form $sql_cond
			group by p.ID, a.ID, a.WO_NUMBER, p.ORDER_QNTY";
			//echo $sql_receive;//die;
			$sql_receive_result = sql_select($sql_receive);
			//print_r($sql_receive_result);
			$pi_receive_data=array();$trans_data_check=array();
			foreach($sql_receive_result as $val)
			{
				if($trans_data_check[$val["ID"]]=="")
				{
					$trans_data_check[$val["ID"]]=$val["ID"];
					$pi_receive_data[$val["WO_ID"]]+=$val["ORDER_QNTY"];
				}
			}
			unset($sql_receive_result);
		}
		else
		{
			$search_field_cond="";
			if(trim($wo_num)!="")
			{
				$search_field_cond="and a.WO_NUMBER like '%$wo_num'";
			}
			
			if($date_form!="" && $date_to!="")
			{
				$search_field_cond.=" and a.WO_DATE between '$date_form' and '$date_to'";
			}
			
			if($cbo_currency_id>0) $search_field_cond.=" and a.CURRENCY_ID=$cbo_currency_id";
			if($cbo_source>0) $search_field_cond.=" and a.SOURCE=$cbo_source";
			if($cbo_supplier>0) $search_field_cond.=" and a.SUPPLIER_ID=$cbo_supplier";
			
			$sql = "SELECT a.ID, a.WO_NUMBER_PREFIX_NUM, a.WO_NUMBER, a.WO_DATE, a.DELIVERY_DATE, a.CURRENCY_ID, a.SOURCE, a.SUPPLIER_ID, b.JOB_NO, a.ENTRY_FORM, 0 as BOOKING_WITHOUT_ORDER, 0 as IS_SALES, 0 as SERVICE_TYPE, to_char(a.insert_date,'YYYY') as YEAR, a.INSERTED_BY, sum(b.SUPPLIER_ORDER_QUANTITY) as WO_QNTY 
			from WO_NON_ORDER_INFO_MST a, WO_NON_ORDER_INFO_DTLS b 
			where a.id=b.mst_id and a.pay_mode<>2 and a.COMPANY_NAME=$company_id and a.ENTRY_FORM=144 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.ITEM_CATEGORY_ID = 1 $search_field_cond $year_cond $approval_status_cond_main
			group by a.ID, a.WO_NUMBER_PREFIX_NUM, a.WO_NUMBER, a.WO_DATE, a.DELIVERY_DATE, a.CURRENCY_ID, a.SOURCE, a.SUPPLIER_ID, b.JOB_NO, a.INSERT_DATE, a.ENTRY_FORM, a.INSERTED_BY
			order by a.ID desc";
			
			$sql_receive="SELECT p.ID, a.ID AS WO_ID, a.WO_NUMBER, p.ORDER_QNTY as ORDER_QNTY
			from inv_transaction p, WO_NON_ORDER_INFO_MST a, WO_NON_ORDER_INFO_DTLS b  
			where p.PI_WO_BATCH_NO=a.id and a.id=b.mst_id and p.RECEIVE_BASIS=2 and p.item_category=1 and p.transaction_type=1 and b.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and p.status_active=1 and p.is_deleted=0 and a.COMPANY_NAME=$company_id and a.ENTRY_FORM=144 $search_field_cond $year_cond
			group by p.ID, a.ID, a.WO_NUMBER, p.ORDER_QNTY";
			//echo $sql_receive;//die;
			$sql_receive_result = sql_select($sql_receive);
			//print_r($sql_receive_result);
			$pi_receive_data=array();$trans_data_check=array();
			foreach($sql_receive_result as $val)
			{
				if($trans_data_check[$val["ID"]]=="")
				{
					$trans_data_check[$val["ID"]]=$val["ID"];
					$pi_receive_data[$val["WO_ID"]]+=$val["ORDER_QNTY"];
				}
			}
			unset($sql_receive_result);
		}
		
		//print_r($pi_receive_data);
		
		//echo $sql;//die;
		$result= sql_select($sql);
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1080" class="rpt_table">
			<thead>
				<tr>
					<th colspan="10"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
				</tr>
				<tr>
					<th width="50">SL</th>
					<th width="120">WO No</th>
					<th width="100">WO Date</th>               
					<th width="150">Supplier</th>
					<th width="100">Delivary date</th>
					<th width="100">Service Type </th>
                    <th width="100">Source</th>
					<th width="100">Currency</th>
					<th width="120">Job No</th>
					<th>Insert User</th>
				</tr>
			</thead>
		</table>
		<div style="width:1100px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">	 
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1080" class="rpt_table" id="tbl_list_search">  
			<?
				$i=1;
				foreach ($result as $row)
				{
					$balance_qnty=$row["WO_QNTY"]-$pi_receive_data[$row["ID"]];
					//echo $balance_qnty."=".$row["WO_QNTY"]."=".$pi_receive_data[$row["ID"]]."=".$row["WO_NUMBER"]."<br>";
					if($balance_qnty>0)
					{  
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
						
						$data=$row['SUPPLIER_ID']."**".$row['CURRENCY_ID']."**".$row['SOURCE']."**".$row['SERVICE_TYPE']."**".$row['ENTRY_FORM'];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row["ID"]; ?>,'<? echo $row['WO_NUMBER']; ?>','<? echo $data; ?>','<? echo $recieve_basis; ?>');">
							<td width="50" align="center"><? echo $i; ?></td>
							<td width="120" align="center"><p><? echo $row['WO_NUMBER']; ?></p></td>
							<td width="100" align="center"> <p> <? echo change_date_format($row['WO_DATE']); ?> </p> </td>
							<td width="150" align="center"><? echo $supplier_arr[$row['SUPPLIER_ID']]; ?></td>						
							<td width="100" align="center"><p><? echo change_date_format($row['DELIVERY_DATE']); ?></p></td>
                            <td width="100" align="center"><? echo $yarn_issue_purpose[$row['SERVICE_TYPE']]; ?>&nbsp;</td>
							<td width="100" align="center"><? echo $source[$row['SOURCE']]; ?>&nbsp;</td>               
							<td width="100"><p><? echo $currency[$row['CURRENCY_ID']]; ?>&nbsp;</p></td>
							<td width="120" align="center"><? echo $row['JOB_NO']; ?>&nbsp;</td>
							<td ><p><? echo $user_name_arr[$row['INSERTED_BY']]; ?></p></td>
						</tr>
						<?
						$i++;
					}
				}
				?>
			</table>
		</div>
		<?	
	}
	else
	{
		$search_field_cond="";
		if(trim($grn_no)!="")
		{
			$search_field_cond="and a.RECV_NUMBER like '%$grn_no'";
		}
		
		if($date_form!="" && $date_to!="")
		{
			$search_field_cond.=" and a.RECEIVE_DATE between '$date_form' and '$date_to'";
		}
		
		if($cbo_currency_id>0) $search_field_cond.=" and a.CURRENCY_ID=$cbo_currency_id";
		if($cbo_source>0) $search_field_cond.=" and a.SOURCE=$cbo_source";
		if($cbo_supplier>0) $search_field_cond.=" and a.SUPPLIER_ID=$cbo_supplier";
		
		$sql_receive="SELECT p.ID, b.WO_PI_ID AS WO_ID, p.ORDER_QNTY as ORDER_QNTY
		from inv_transaction p, QUARANTINE_PARKING_DTLS b 
		where p.PI_WO_BATCH_NO=b.WO_PI_ID and p.item_category=1 and p.transaction_type=1 and b.ITEM_CATEGORY_ID=1 and b.status_active=1 and b.is_deleted=0 and p.status_active=1 and p.is_deleted=0 and p.COMPANY_ID=$company_id and b.ENTRY_FORM=529";
		//echo $sql_receive;//die;
		$sql_receive_result = sql_select($sql_receive);
		//print_r($sql_receive_result);
		$pi_receive_data=array();$trans_data_check=array();
		foreach($sql_receive_result as $val)
		{
			if($trans_data_check[$val["ID"]]=="")
			{
				$trans_data_check[$val["ID"]]=$val["ID"];
				$pi_receive_data[$val["WO_ID"]]+=$val["ORDER_QNTY"];
			}
		}
		unset($sql_receive_result);
		
		$sql_grn_qc="SELECT p.MST_ID, sum(b.QC_QNTY) as ORDER_QNTY
		from QUARANTINE_PARKING_DTLS p, QUARANTINE_PARKING_DTLS b 
		where p.ID=b.GRN_DTLS_ID and p.ITEM_CATEGORY_ID=1 and b.ITEM_CATEGORY_ID=1 and b.status_active=1 and b.is_deleted=0 and p.status_active=1 and p.is_deleted=0 and p.ENTRY_FORM=529 and b.ENTRY_FORM=530
		group by p.MST_ID";
		//echo $sql_receive;//die;
		$sql_grn_qc_result = sql_select($sql_grn_qc);
		$grn_qc_data=array();
		foreach($sql_grn_qc_result as $val)
		{
			$grn_qc_data[$val["MST_ID"]]+=$val["ORDER_QNTY"];
		}
		unset($sql_grn_qc_result);
		
		//print_r($pi_receive_data);
		if($cbo_receive_purpose>0) $search_field_cond.=" and a.RECEIVE_PURPOSE=$cbo_receive_purpose";
		$sql = "SELECT a.ID, a.RECV_NUMBER_PREFIX_NUM, a.RECV_NUMBER, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.CHALLAN_NO, a.CURRENCY_ID, a.SOURCE, a.SUPPLIER_ID, a.BOOKING_ID, a.BOOKING_NO, to_char(a.insert_date,'YYYY') as YEAR, a.INSERTED_BY, sum(b.PARKING_QUANTITY) as QUANTITY 
		from INV_RECEIVE_MASTER a, QUARANTINE_PARKING_DTLS b 
		where a.id=b.mst_id and a.COMPANY_ID=$company_id and a.ENTRY_FORM=529 and b.ENTRY_FORM=529 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.ITEM_CATEGORY_ID = 1 and b.IS_QC_PASS=1 and a.RECEIVE_PURPOSE=$receive_purpose and a.IS_APPROVED=1 $search_field_cond $year_cond $approval_status_cond_main
		group by a.ID, a.RECV_NUMBER_PREFIX_NUM, a.RECV_NUMBER, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.CHALLAN_NO, a.CURRENCY_ID, a.SOURCE, a.SUPPLIER_ID, a.BOOKING_ID, a.BOOKING_NO, a.INSERT_DATE, a.INSERTED_BY
		order by a.ID desc";
		 //echo $sql;//die;
		$result= sql_select($sql);
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1080" class="rpt_table">
			<thead>
				<tr>
					<th colspan="9"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
				</tr>
				<tr>
					<th width="50">SL</th>
					<th width="120">GRN No</th>
					<th width="100">GRN Date</th>               
					<th width="180">Supplier</th>
					<th width="100">Challan No</th>
					<th width="120">Source</th>
					<th width="120">Currency</th>
					<th width="120">WO/PI</th>
					<th>Insert User</th>
				</tr>
			</thead>
		</table>
		<div style="width:1100px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">	 
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1080" class="rpt_table" id="tbl_list_search">  
			<?
				$i=1;
				foreach ($result as $row)
				{
					$balance_qnty=$grn_qc_data[$row["ID"]]-$pi_receive_data[$row["BOOKING_ID"]];
					//echo $balance_qnty."=".$grn_qc_data[$row["ID"]]."=".$pi_receive_data[$row["BOOKING_ID"]];
					if($balance_qnty>0)
					{  
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
						
						$data=$row['SUPPLIER_ID']."**".$row['CURRENCY_ID']."**".$row['SOURCE']."**".$row['BOOKING_ID']."**".$row['BOOKING_NO'];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row["ID"]; ?>,'<? echo $row['RECV_NUMBER']; ?>','<? echo $data; ?>','<? echo $row['RECEIVE_BASIS']; ?>','<? echo $cbo_receive_purpose; ?>');">
							<td width="50" align="center"><? echo $i; ?></td>
							<td width="120" align="center"><p><? echo $row['RECV_NUMBER']; ?></p></td>
							<td width="100" align="center"> <p> <? echo change_date_format($row['RECEIVE_DATE']); ?> </p> </td>
							<td width="180" align="center"><? echo $supplier_arr[$row['SUPPLIER_ID']]; ?></td>						
							<td width="100" align="center"><p><? echo $row['CHALLAN_NO']; ?></p></td>
							<td width="120" align="center"><? echo $source[$row['SOURCE']]; ?>&nbsp;</td>               
							<td width="120"><p><? echo $currency[$row['CURRENCY_ID']]; ?>&nbsp;</p></td>
							<td width="120" align="center"><? echo $row['BOOKING_NO']; ?>&nbsp;</td>
							<td ><p><? echo $user_name_arr[$row['INSERTED_BY']]; ?></p></td>
						</tr>
						<?
						$i++;
					}
				}
				?>
			</table>
		</div>
		<?	
	}
	exit();
}







if($action=="duplication_check")
{
	$data=explode("**",$data);
	$update_id=$data[0];
	$dtls_id=$data[1];
	
	if($dtls_id=="") $dtls_id_cond=""; else $dtls_id_cond=" and b.id!=$dtls_id";
	
	//$sql="select a.supplier_id, a.currency_id, a.source, a.lc_no from inv_receive_master a, inv_trims_entry_dtls b where a.id=b.mst_id and a.id=$update_id and a.item_category=4 and a.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $dtls_id_cond";
	
	$sql="select a.supplier_id, a.currency_id, a.source, a.lc_no from inv_receive_master a where a.id=$update_id and a.item_category=4 and a.entry_form=24 and a.status_active=1 and a.is_deleted=0";
	$dataArray=sql_select($sql);
	$data=$dataArray[0][csf('supplier_id')]."**".$dataArray[0][csf('currency_id')]."**".$dataArray[0][csf('source')]."**".$dataArray[0][csf('lc_no')];
	echo $data;
	exit();
}

//return product master table id ----------------------------------------//
function return_product_id($yarncount, $composition_one, $composition_two, $percentage_one, $percentage_two, $yarntype, $color, $yarnlot, $prodCode, $company, $supplier, $store, $uom, $yarn_type, $composition, $cbo_receive_purpose, $hdnPayMode)
{

	$composition_one = str_replace("'", "", $composition_one);
	$composition_two = str_replace("'", "", $composition_two);
	$percentage_one = str_replace("'", "", $percentage_one);
	$percentage_two = str_replace("'", "", $percentage_two);
	$yarntype = str_replace("'", "", $yarntype);
	$color = str_replace("'", "", $color);
	$yarncount = str_replace("'", "", $yarncount);
	if ($percentage_one == "") $percentage_one = 0;
	if ($percentage_two == "") $percentage_two = 0;
	$cbo_receive_purpose = str_replace("'", "", $cbo_receive_purpose);
	if($cbo_receive_purpose==2 || $cbo_receive_purpose==12 || $cbo_receive_purpose==15 || $cbo_receive_purpose==38 || $cbo_receive_purpose==43 || $cbo_receive_purpose==46 || $cbo_receive_purpose==50 || $cbo_receive_purpose==51 ) $dyed_type=1; else $dyed_type=2;
	if($cbo_receive_purpose==15) $is_twisted=1; else $is_twisted=0;
	
	//for pay mode
	$payMode = str_replace("'", "", $hdnPayMode);
	$is_within_group = 0;
	if($payMode == 3 || $payMode == 5)
	{
		$is_within_group = 1;
	}

	//NOTE :- Yarn category array ID=1
	$conp2_cond="";
	if($composition_two!="") $conp2_cond=" and yarn_comp_type2nd=$composition_two and yarn_comp_percent2nd=$percentage_two";
	$whereCondition = "yarn_count_id=$yarncount and yarn_comp_type1st=$composition_one and yarn_comp_percent1st=$percentage_one $conp2_cond and yarn_type=$yarntype and color=$color and company_id=$company and supplier_id=$supplier and item_category_id=1 and lot=$yarnlot and status_active=1 and is_deleted=0"; //and store_id=$store
	$prodMSTID = return_field_value("id", "product_details_master", "$whereCondition");
	//return "select id from product_details_master where $whereCondition";die;
	$insertResult = true;
	if ($prodMSTID == false || $prodMSTID == "")
	{
		// new product create here--------------------------//
		$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
		$color_name_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');

		$compositionPart = $composition[$composition_one] . " " . $percentage_one;
		if ($percentage_two != 0) {
			$compositionPart .= " " . $composition[$composition_two] . " " . $percentage_two;
		}

		//$yarn_count.','.$composition.','.$ytype.','.$color;
		$product_name_details = $yarn_count_arr[$yarncount] . " " . $compositionPart . " " . $yarn_type[$yarntype] . " " . $color_name_arr[$color];
		$product_name_details = str_replace(array("\r", "\n"), '', $product_name_details);
		
		$prodMSTID = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
		$field_array = "id,company_id,supplier_id,item_category_id,product_name_details,lot,item_code,unit_of_measure,yarn_count_id,yarn_comp_type1st,yarn_comp_percent1st,yarn_comp_type2nd,yarn_comp_percent2nd,yarn_type,color,dyed_type,inserted_by,insert_date,is_twisted,is_within_group";
		$data_array = "(" . $prodMSTID . "," . $company . "," . $supplier . ",1,'" . $product_name_details . "'," . $yarnlot . "," . $prodCode . "," . $uom . "," . $yarncount . "," . $composition_one . "," . $percentage_one . ",'" . $composition_two . "','" . $percentage_two . "'," . $yarntype . "," . $color . ",'" . $dyed_type . "','" . $user_id . "','" . $pc_date_time . "',".$is_twisted.",".$is_within_group.")";
		//echo $field_array."<br>".$data_array."--".$product_name_details;die;
		$insertResult = false;
		//$insertResult = sql_insert("product_details_master",$field_array,$data_array,1);
	}
	if ($insertResult == true) {
		return $insertResult . "***" . $prodMSTID;
	} else {
		return $insertResult . "***" . $field_array . "***" . $data_array . "***" . $prodMSTID;
	}
}

if($action=="mrr_popup")
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
	<table width="880" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="1">
            <thead>
                <tr>                	 
                    <th width="150">Supplier</th>
                    <th width="150">Search By</th>
                    <th width="250" align="center" id="search_by_td_up">Enter MRR Number</th>
                    <th width="200">Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td align="center">
                        <?  
 						echo create_drop_down( "cbo_supplier", 150, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(1,5,6,7,8,30,36,37,39,92) $supplier_credential_cond  and a.status_active=1 and a.is_deleted=0  group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
                        ?>
                    </td>
                    <td align="center">
                        <?  
                        $search_by = array(1=>'MRR No',2=>'Challan No',3=>'WO',4=>'PI');
						$dd="change_search_event(this.value, '0*0*0*0', '0*0*0)', '../../../') ";
						echo create_drop_down( "cbo_search_by", 120, $search_by,"",0, "--Select--", "",$dd,0 );
                        ?>
                    </td>
                    <td width="" align="center" id="search_by_td">				
                        <input type="text" style="width:230px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td>    
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
                    </td> 
                    <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('cbo_year_selection').value, 'create_mrr_search_list_view', 'search_div', 'general_item_receive_v2_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
                    </td>
            	</tr>
				<tr>                  
					<td align="center" height="40" valign="middle" colspan="5">
						<? echo load_month_buttons(1);  ?>
						<!- Hidden field here-->
						<input type="hidden" id="hidden_recv_number" value="hidden_recv_number" />						
					</td>
				</tr>    
            </tbody>
         </tr>         
        </table>   
        <br> 
        <div align="center" valign="top" id="search_div"> </div> 
        </form>
    </div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="create_mrr_search_list_view")
{
	$ex_data = explode("_",$data);
	$supplier = $ex_data[0];
	$txt_search_by = $ex_data[1];
	$txt_search_common = $ex_data[2];
	$fromDate = $ex_data[3];
	$toDate = $ex_data[4];
	$company = $ex_data[5];
	$year = $ex_data[6];
	 
	$sql_cond=$wo_pi_sql="";
	$basis_cond="";
	if(trim($txt_search_common)!="")
	{
		if(trim($txt_search_by)==1) // for mrr
		{
			$sql_cond .= " and a.recv_number_prefix_num LIKE '$txt_search_common'";	
			
		}
		else if(trim($txt_search_by)==2) // for chllan no
		{
			$sql_cond .= " and a.challan_no LIKE '%$txt_search_common%'";				
 		}
 		else if(trim($txt_search_by)==3)
 		{
 			$year_cond="";
			if($year!="")
			{	
				$year_cond=" and to_char(wo_date,'YYYY') =$year ";
			}
 			$wo_pi_sql = "select id from wo_non_order_info_mst where wo_number LIKE '%$txt_search_common%' $year_cond";
 			$basis_cond=" and a.receive_basis=2";
 		}
 		else
 		{
 			$year_cond="";
			if($year!="")
			{	
				$year_cond=" and to_char(pi_date,'YYYY') =$year ";
			}
 			$wo_pi_sql = "select id from com_pi_master_details where pi_number LIKE '%$txt_search_common%' $year_cond";
 			$basis_cond=" and a.receive_basis=1";
 		}		 
 	}
 	//echo $wo_pi_sql;

 	$booking_cond='';
 	if(!empty($wo_pi_sql))
 	{
 		$cond_res=sql_select($wo_pi_sql);
 		$booking_ids=array();
 		foreach ($cond_res as $row) {
 			array_push($booking_ids, $row[csf('id')]);
 		}
 		array_unique($booking_ids); 		
 		$booking_cond=" and a.booking_id in(".implode(',',$booking_ids).")"; 		
 	} 
 	//echo "<br>".$booking_cond;die;
	$year_cond='';
	
	if( $fromDate!="" && $toDate!="" ) $sql_cond .= " and a.receive_date  between '".change_date_format($fromDate,'yyyy-mm-dd','',-1)."' and '".change_date_format($toDate,'yyyy-mm-dd','',-1)."'";
	if($year!="")
	{	
		if(trim($txt_search_by)==1)
		{
			$year_cond=" and to_char(a.receive_date,'YYYY') =$year ";
		}
		else if(trim($txt_search_by)==2)
		{
			$year_cond=" and to_char(a.receive_date,'YYYY') =$year ";
		}
	}

	if(trim($company)!="") $sql_cond .= " and a.company_id='$company'";
	if(trim($supplier)!=0) $sql_cond .= " and a.supplier_id='$supplier'";
	
	$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, supplier_id FROM user_passwd where id=$user_id");
	$cre_company_id = $userCredential[0][csf('company_id')];
	$cre_supplier_id = $userCredential[0][csf('supplier_id')];
	$cre_store_location_id = $userCredential[0][csf('store_location_id')];
	$cre_item_cate_id = $userCredential[0][csf('item_cate_id')];
	
	$credientian_cond="";
	if($cre_company_id!="") $credientian_cond=" and a.company_id in($cre_company_id)";
	if($cre_supplier_id!="") $credientian_cond.=" and a.supplier_id in($cre_supplier_id)";
	if($cre_store_location_id!="") $credientian_cond.=" and b.store_id in($cre_store_location_id)";
	if($cre_item_cate_id!="") $credientian_cond.=" and b.item_category in($cre_item_cate_id)";
	
	//echo $credientian_cond;die;
	
	$sql = "SELECT a.id as rcv_id, a.recv_number,a.recv_number_prefix_num, a.supplier_id, a.challan_no, c.lc_number, a.receive_date, a.receive_basis, sum(b.order_qnty) as receive_qnty,(select wo_number from wo_non_order_info_mst d where d.id=a.booking_id and a.receive_basis=2) as wo_number, (select pi_number from com_pi_master_details f where f.id=a.booking_id and a.receive_basis=1) as pi_number 
	from inv_transaction b, inv_receive_master a left join com_btb_lc_master_details c on a.lc_no=c.id where a.id=b.mst_id and a.entry_form=20 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.transaction_type=1 $sql_cond  $credientian_cond $booking_cond $basis_cond $year_cond
	group by a.id, b.mst_id, a.recv_number, a.recv_number_prefix_num, a.supplier_id, a.challan_no, c.lc_number, a.receive_date, a.receive_basis, a.booking_id 
	order by b.mst_id desc";
	//echo $sql;
	$supplier_arr = return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$arr=array(1=>$supplier_arr,5=>$receive_basis_arr);
	echo create_list_view("list_view", "MRR No, Supplier Name, Challan No, LC No, Receive Date, Receive Basis,WO Number,PI Number, Receive Qnty","120,120,120,120,120,100,120,80,80","1050","260",0, $sql , "js_set_value", "rcv_id", "", 1, "0,supplier_id,0,0,0,receive_basis,0,0,0", $arr, "recv_number,supplier_id,challan_no,lc_number,receive_date,receive_basis,wo_number,pi_number,receive_qnty", "",'','0,0,0,0,0,0,0,0,2') ;	
	exit();
}	 

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));	
	

	// check MRR Auditing Report is Audited or Not
	if (str_replace("'",'',$txt_mrr_no) != '')
	{
		$is_audited=return_field_value("is_audited","inv_receive_master","id=".str_replace("'",'',$update_id)." and status_active=1 and is_deleted=0","is_audited");
		if($is_audited==1) {
			echo "50**This MRR is Audited. Save, Update and Delete Not Allowed..";
			die;
		}
	}

	// Get all Prod Id and PI WO REQ Dtls ID
	$all_prod_ids="";
	for($i=1;$i<=$tot_row; $i++)
	{
		$all_prod_id   = "prodId".$i;
		$all_prod_ids .= $$all_prod_id.',';
		$pi_wo_req_dtlsid   = "piWoDtlsId".$i;
		$pi_wo_req_dtlsids .= $$pi_wo_req_dtlsid.',';
	}
	$all_prod_ids      = rtrim($all_prod_ids,',');
	$pi_wo_req_dtlsids = rtrim($pi_wo_req_dtlsids,',');

	$sql_prod = "select ID, UNIT_OF_MEASURE, CONVERSION_FACTOR, PRODUCT_NAME_DETAILS, AVG_RATE_PER_UNIT, LAST_PURCHASED_QNTY, CURRENT_STOCK, STOCK_VALUE, AVAILABLE_QNTY from product_details_master where id in($all_prod_ids) and company_id=$cbo_company_id and status_active=1 and is_deleted=0 ";
	$sql_prod_res=sql_select($sql_prod);
	foreach($sql_prod_res as $row)
	{
		$product_data_arr[$row['ID']]['prod_id']=$row['ID'];
		$product_data_arr[$row['ID']]['cons_uom']=$row['UNIT_OF_MEASURE'];
		$product_data_arr[$row['ID']]['conversion_factor']=$row['CONVERSION_FACTOR'];
		$product_data_arr[$row['ID']]['product_name_details']=$row['PRODUCT_NAME_DETAILS'];
		$product_data_arr[$row['ID']]['avg_rate_per_unit']=$row['AVG_RATE_PER_UNIT'];
		$product_data_arr[$row['ID']]['last_purchased_qnty']=$row['LAST_PURCHASED_QNTY'];
		$product_data_arr[$row['ID']]['current_stock']=$row['CURRENT_STOCK'];
		$product_data_arr[$row['ID']]['stock_value']=$row['STOCK_VALUE'];
		$product_data_arr[$row['ID']]['available_qnty']=$row['AVAILABLE_QNTY'];
	}	

	$receive_basis = str_replace("'","",$cbo_receive_basis);
	$wo_pi_req_id  = str_replace("'","",$txt_wo_pi_req_id);

	$prev_entry="select PI_WO_REQ_DTLS_ID, order_qnty as PREV_QUANTITY, pi_wo_batch_no as PI_WO_REQ_ID, PROD_ID
	from inv_transaction where company_id=$cbo_company_id and item_category in(".implode(",",array_flip($general_item_category)).") and transaction_type=1 and receive_basis=$receive_basis and pi_wo_batch_no=$wo_pi_req_id and status_active=1 and is_deleted=0
	group by pi_wo_batch_no,prod_id";
	$prev_entry_result=sql_select($prev_entry);
	$prev_data_arr=array();
	foreach($prev_entry_result as $row)
	{
		$prev_data_arr[$row["PI_WO_REQ_DTLS_ID"]]+=$row["PREV_QUANTITY"];
	}
	
	if ($receive_basis==1) // PI Basis
	{
		$sql = "select b.id as PI_WO_REQ_DTLS_ID, b.quantity as QUANTITY, b.rate as RATE from com_pi_item_details b where b.id in($pi_wo_req_dtlsids) and b.status_active=1 and b.is_deleted=0";
	}
	else if ($receive_basis==2) // Work Order Basis
	{
		$sql = "select b.id as PI_WO_REQ_DTLS_ID, b.supplier_order_quantity as QUANTITY, b.rate as RATE from wo_non_order_info_dtls b where b.id in($pi_wo_req_dtlsids) and b.status_active=1 and b.is_deleted=0";
	}
	else // Requisition Basis
	{
		$sql = "select b.id as PI_WO_REQ_DTLS_ID, b.quantity as QUANTITY, b.rate as RATE from inv_purchase_requisition_dtls b where b.id in($pi_wo_req_dtlsids) and b.status_active=1 and b.is_deleted=0";		
	}

	$sql_res=sql_select($sql);
	$bookingPiReqQty_dtls_arr=array();
	foreach ($sql_res as $row){
		$bookingPiReqQty_dtls_arr[$row['PI_WO_REQ_DTLS_ID']]['qty']=$row['QUANTITY'];
		$bookingPiReqQty_dtls_arr[$row['PI_WO_REQ_DTLS_ID']]['rate']=$row['RATE'];
	}

	for($i=1;$i<=$tot_row; $i++)
	{
		$piWoReq_dtlsid = "piWoDtlsId".$i;
		$piWoReq_qty    = $bookingPiReqQty_dtls_arr[$$piWoReq_dtlsid]['qty'];
		$txtReceiveQty  = "receiveqnty".$i;
		$receiveQty     = $$txtReceiveQty+$prev_data_arr[$$piWoReq_dtlsid];
		$txtpiWoReqRate = "rate".$i;
		$piWoReqRate    = $bookingPiReqQty_dtls_arr[$$piWoReq_dtlsid]['rate'];

		if ( $receiveQty > $piWoReq_qty ){
			echo "40**Receive quantity=$receiveQty can not be greater than WO/PI/Req. quantity=$piWoReq_qty";die;
		}
		if ($receive_basis!=7){
			if ( $$txtpiWoReqRate != $bookingPiReqQty_dtls_arr[$$piWoReq_dtlsid]['rate'] ){
				echo "40**Receive Rate=".$$txtpiWoReqRate." don't match WO/PI Rate=$piWoReqRate";die;
			}
		}
	}


	if ($operation==0)  // Insert Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$txt_challan_date=$txt_bill_date=$txt_gate_entry_date=$txt_addi_rcvd_date="";
		$addi_info_arr = explode("_", str_replace("'","",$txt_addi_info));
		$txt_book_no = $addi_info_arr[0];
		$txt_challan_date = $addi_info_arr[1];
		$txt_bill_no = $addi_info_arr[2];
		$txt_bill_date = $addi_info_arr[3];
		$cbo_purchaser_name = $addi_info_arr[4];
		$cbo_carried_by = $addi_info_arr[5];
		$cbo_qc_check_by = $addi_info_arr[6];
		$cbo_receive_by = $addi_info_arr[7];
		$cbo_gate_entry_by = $addi_info_arr[8];
		$txt_gate_entry_date = $addi_info_arr[9];
		$txt_addi_rcvd_date = $addi_info_arr[10];
		$txt_gate_entry_no = $addi_info_arr[11];
		$txt_store_sl_no = $addi_info_arr[12];
		$booking_without_order=0;
		
		$general_recv_num=''; $general_update_id='';
		if(str_replace("'","",$update_id)=="")
		{
			$year_cond="to_char(insert_date,'YYYY')"; //defined Later
			
			$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con);
			$new_recv_number = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master",$con,1,str_replace("'","",$cbo_company_id),'GIR',20,date("Y",time())));
			$field_array="id, recv_number_prefix, recv_number_prefix_num, recv_number, entry_form, receive_basis, company_id, receive_date, challan_no, challan_date, booking_id, booking_no, booking_without_order, store_id, lc_no, source, supplier_referance, receive_purpose, loan_party, pay_mode, supplier_id, currency_id, exchange_rate, ref_no, is_multi, boe_mushak_challan_no, boe_mushak_challan_date, remarks, store_sl_no, rcvd_book_no, addi_challan_date, bill_no, bill_date, purchaser_name, carried_by, qc_check_by, receive_by, gate_entry_by, gate_entry_date, addi_rcvd_date, gate_entry_no,  inserted_by, insert_date";

			$data_array="(".$id.",'".$new_recv_number[1]."',".$new_recv_number[2].",'".$new_recv_number[0]."',20,".$cbo_receive_basis.",".$cbo_company_id.",".$txt_receive_date.",".$txt_challan_no.",".$txt_challan_date_mst.",".$txt_wo_pi_req_id.",".$txt_wo_pi_req.",".$booking_without_order.",".$cbo_store_name.",".$hidden_lc_id.",".$cbo_source.",".$txt_sup_ref.",".$cbo_receive_purpose.",".$cbo_loan_party.",".$cbo_pay_mode.",".$cbo_supplier.",".$cbo_currency_id.",".$txt_exchange_rate.",".$txt_ref_no.",2,".$txt_boe_mushak_challan_no.",".$txt_boe_mushak_challan_date.",".$txt_remarks.",'".$txt_store_sl_no."','".$txt_book_no."','".$txt_challan_date."','".$txt_bill_no."','".$txt_bill_date."','".$cbo_purchaser_name."','".$cbo_carried_by."','".$cbo_qc_check_by."','".$cbo_receive_by."','".$cbo_gate_entry_by."','".$txt_gate_entry_date."','".$txt_addi_rcvd_date."','".$txt_gate_entry_no."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$general_recv_num=$new_recv_number[0];
			$general_update_id=$id;
		}
		else
		{
			$original_receive_basis=sql_select("select RECEIVE_BASIS, SUPPLIER_ID, SOURCE from inv_receive_master where id=$update_id");
			if( str_replace("'","",$cbo_receive_basis)!=$original_receive_basis[0]['RECEIVE_BASIS'] )
			{
				echo "40**Multiple Receive Basis Not Allow In Same Received ID";
				disconnect($con);die;
			}
			if( str_replace("'","",$cbo_source)!=$original_receive_basis[0]['SOURCE'] )
			{
				echo "40**Multiple Source Not Allow In Same Received ID";
				disconnect($con);die;
			}

			if( str_replace("'","",$cbo_supplier_name)!=$original_receive_basis[0]['SUPPLIER_ID'] )
			{
				echo "40**Multiple Supplier Not Allow In Same Received ID";
				disconnect($con);die;
			}

			$field_array_update="receive_basis*company_id*receive_date*challan_no*challan_date*booking_id*booking_no*booking_without_order*store_id*lc_no*source*supplier_referance*receive_purpose*loan_party*pay_mode*supplier_id*currency_id*exchange_rate*ref_no*boe_mushak_challan_no*boe_mushak_challan_date*remarks*store_sl_no*rcvd_book_no*addi_challan_date*bill_no*bill_date*purchaser_name*carried_by*qc_check_by*receive_by*gate_entry_by*gate_entry_date*addi_rcvd_date*gate_entry_no*updated_by*update_date";

			$data_array_update="".$cbo_receive_basis."*".$cbo_company_id."*".$txt_receive_date."*".$txt_challan_no."*".$txt_challan_date_mst."*".$txt_wo_pi_req_id."*".$txt_wo_pi_req."*".$booking_without_order."*".$cbo_store_name."*".$hidden_lc_id."*".$cbo_source."*".$txt_sup_ref."*".$cbo_receive_purpose."*".$cbo_loan_party."*".$cbo_pay_mode."*".$cbo_supplier."*".$cbo_currency_id."*".$txt_exchange_rate."*".$txt_ref_no."*".$txt_boe_mushak_challan_no."*".$txt_boe_mushak_challan_date."*".$txt_remarks."*'".$txt_store_sl_no."'*'".$txt_book_no."'*'".$txt_challan_date."'*'".$txt_bill_no."'*'".$txt_bill_date."'*'".$cbo_purchaser_name."'*'".$cbo_carried_by."'*'".$cbo_qc_check_by."'*'".$cbo_receive_by."'*'".$cbo_gate_entry_by."'*'".$txt_gate_entry_date."'*'".$txt_addi_rcvd_date."'*'".$txt_gate_entry_no."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."";
			
			$general_recv_num=str_replace("'","",$txt_mrr_no);
			$general_update_id=str_replace("'","",$update_id);
		}

		$data_array_trans="";
		$product_unique_data_arr=array();
		for($i=1;$i<=$tot_row; $i++)
		{
			$category           = "category".$i;
			$group              = "group".$i;
			$description        = "description".$i;
			$size               = "size".$i;
			$itemdescription    = "itemdescription".$i;			
			$subGroup           = "subGroup".$i;
			$itemNumber         = "itemNumber".$i;
			$itemCode           = "itemCode".$i;
			$uom                = "uom".$i;
			$brand              = "brand".$i;
			$origin             = "origin".$i;
			$model              = "model".$i;
			$txtLot             = "txtLot".$i;
			$comments           = "comments".$i;
			$txtWarrentyExpDate = "txtWarrentyExpDate".$i;
			$txtSerial          = "txtSerial".$i;
			$txtSerialQty       = "txtSerialQty".$i;

			$floorID = "floorID".$i;
			$roomID  = "roomID".$i;
			$rackID  = "rackID".$i;
			$shelfID = "shelfID".$i;
			$binID   = "binID".$i;

			$updatedtlsid = "updatedtlsid".$i;
			$piWoDtlsId   = "piWoDtlsId".$i;
			$prodId       = "prodId".$i;
			
			$woPiReqQnty     = "woPiReqQnty".$i;
			$txt_receive_qty = "receiveqnty".$i;
			$rate            = "rate".$i;
			$ile_cost        = "ilePersent".$i; //ile cost = (ile/100)*rate
			$txt_amount      = "amount".$i;
			$woPiBalQnty     = "woPiBalQnty".$i;
			$bookCurrency    = "bookCurrency".$i;
			
			$ile = ($$ile_cost/$$rate)*100; // ile cost to ile
			$cons_uom          = $product_data_arr[$$prodId]['cons_uom'];
			$conversion_factor = $product_data_arr[$$prodId]['conversion_factor'];			
			$exchange_rate = str_replace("'","",$txt_exchange_rate);
			$domestic_rate = return_domestic_rate($$rate,$$ile_cost,$exchange_rate,$conversion_factor);
			$cons_rate     = number_format($domestic_rate,$dec_place[3],".","");
			$con_quantity  = $conversion_factor*$$txt_receive_qty;
			$con_amount    = $cons_rate*$con_quantity;
			$con_ile       = $ile/$conversion_factor; //($ile/$domestic_rate)*100;
			
			if($$ile_cost=="") $ile_cost =0;
			if($cons_uom=="") $cons_uom =0;
			if($con_ile=="") $con_ile =0;
			
			$con_ile_cost = ($$ile_cost*$exchange_rate)/$conversion_factor;
			if($con_ile_cost=="") $con_ile_cost=0;

			$dtlsid = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
			$field_array_trams = "id,mst_id,receive_basis,pi_wo_batch_no,company_id,supplier_id,prod_id,item_category,transaction_type,transaction_date,store_id,order_uom,order_qnty,order_rate,order_ile,order_ile_cost,order_amount,cons_uom,cons_quantity,cons_rate,cons_ile,cons_ile_cost,cons_amount,balance_qnty,balance_amount,floor_id,room,rack,self,bin_box,pi_wo_req_dtls_id,expire_date,remarks,inserted_by,insert_date,batch_lot";
			if($data_array_trans!="") $data_array_trans.=",";
 			$data_array_trans .= "(".$dtlsid.",".$general_update_id.",".$cbo_receive_basis.",".$txt_wo_pi_req_id.",".$cbo_company_id.",".$cbo_supplier.",'".$$prodId."','".$$category."',1,".$txt_receive_date.",".$cbo_store_name.",'".$$uom."','".$$txt_receive_qty."','".$$rate."','".$$ile."','".$$ile_cost."','".$$txt_amount."','".$cons_uom."','".$con_quantity."','".$cons_rate."','".$con_ile."','".$con_ile_cost."','".$con_amount."','".$con_quantity."','".$con_amount."','".$$floorID."','".$$roomID."','".$$rackID."','".$$shelfID."','".$$binID."','".$$piWoDtlsId."','".$$txt_warranty_date."','".$$comments."','".$user_id."','".$pc_date_time."','".$$txtLot."')";

			//product master table data UPDATE START------------------//
			$product_unique_data_arr[$$prodId]=$$prodId;
			$presentStock      = $product_data_arr[$$prodId]['current_stock'];
			$presentStockValue = $product_data_arr[$$prodId]['stock_value'];
			$presentAvgRate    = $product_data_arr[$$prodId]['avg_rate_per_unit'];
			$available_qnty    = $product_data_arr[$$prodId]['available_qnty'];

			$stock_value 	= $domestic_rate*$con_quantity;
			$currentStock   = $presentStock+$con_quantity;
			$available_qnty = $available_qnty+$con_quantity;
			$StockValue=0;		
			$avgRate=$presentAvgRate;
			if ($currentStock != 0) {
				$StockValue	 = $presentStockValue+$stock_value;
				$avgRate	 = $StockValue/$currentStock;
			}

			$field_array_prod_update="avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*available_qnty*updated_by*update_date";
			$updateProdID_array[]=$$prodId;
			$data_array_prod_update[$$prodId]=explode("*",("".number_format($avgRate,$dec_place[3],".","")."*".$con_quantity."*".$currentStock."*".number_format($StockValue,$dec_place[4],".","")."*".$available_qnty."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

			$flag=1;
			if ($$txtSerial != "")
			{				
				$serial_field_array = "id,recv_trans_id,prod_id,serial_no,is_issued,inserted_by,insert_date,serial_qty";
				$expSerial = explode(",",str_replace("'","",$$txtSerial));
				$expSerialqty = explode(",",str_replace("'","",$$txtSerialQty));
				$serial_data_array="";
				for($i=0;$i<count($expSerial);$i++)
				{
					$serialID = return_next_id_by_sequence("INV_SERIAL_NO_DETAILS_PK_SEQ", "inv_serial_no_details", $con);
					if($i>0){ $serial_data_array .=","; }
					$serial_data_array .= "(".$serialID.",".$dtlsid.",'".$$prodId."','".$expSerial[$i]."',0,'".$user_id."','".$pc_date_time."','".$expSerialqty[$i]."')";
				}
				$serial_dtlsrID=sql_insert("inv_serial_no_details",$serial_field_array,$serial_data_array,1);
				if ($serial_dtlsrID) $flag=1; else $flag=0;
			}
			
		}

		//echo "10**insert into inv_transaction (".$field_array_trams.") values ".$data_array_trans;oci_rollback($con);disconnect($con);die;
		$rID=$dtlsrID=$prodUpdate=true;
		if(str_replace("'","",$update_id)=="")
		{
			$rID=sql_insert("inv_receive_master",$field_array,$data_array,1);
		}
		else  	
		{	
			$rID=sql_update("inv_receive_master",$field_array_update,$data_array_update,"id",$update_id,1);
		}

		$dtlsrID = sql_insert("inv_transaction",$field_array_trams,$data_array_trans,1);

		if(count($data_array_prod_update)>0)
		{
			//echo "10**".bulk_update_sql_statement("product_details_master","id",$field_array_prod_update,$data_array_prod_update,$updateProdID_array);oci_rollback($con);die;
			$prodUpdate=execute_query(bulk_update_sql_statement("product_details_master","id",$field_array_prod_update,$data_array_prod_update,$updateProdID_array));
		}
		
		//echo "10**$rID && $dtlsrID && $prodUpdate && $flag";die;
		if($rID && $dtlsrID && $prodUpdate && $flag==1)
		{
			oci_commit($con);
			echo "0**".$general_update_id."**".$general_recv_num."**0";
		}
		else
		{
			mysql_query("ROLLBACK");
			echo "5**0**"."&nbsp;"."**0";
		}
		
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$trims_recv_num=str_replace("'","",$txt_recieved_id);
		$master_id=str_replace("'","",$update_id);
		
		if($master_id<1)
		{
			echo "40**Update Not Allow";
			disconnect($con);die;
		}
		
		
		$prev_grn_dlts_sql=sql_select("select ID, CONS_QUANTITY, CONS_AMOUNT from INV_TRANSACTION where status_active=1 and is_deleted=0 and mst_id=$master_id");
		$prev_grn_dlts_ids=array();$previous_data=array();
		foreach($prev_grn_dlts_sql as $row)
		{
			$prev_grn_dlts_ids[$row["ID"]]=$row["ID"];
			$previous_data[$row["ID"]]["CONS_QUANTITY"]=$row["CONS_QUANTITY"];
			$previous_data[$row["ID"]]["CONS_AMOUNT"]=$row["CONS_AMOUNT"];
		}

		$original_receive_basis=sql_select("select receive_basis, supplier_id, source, qc_check_by from inv_receive_master where id=$master_id");
		if(str_replace("'","",$cbo_receive_basis)!=$original_receive_basis[0][csf('receive_basis')])
		{
			echo "40**Multiple Receive Basis Not Allow In Same Received ID";disconnect($con);die;
		}

		if(str_replace("'","",$cbo_source)!=$original_receive_basis[0][csf('source')])
		{
			echo "40**Multiple Source Not Allow In Same Received ID";disconnect($con);die;
		}
		if(str_replace("'","",$cbo_supplier)!=$original_receive_basis[0][csf('supplier_id')])
		{
			echo "40**Multiple Supplier Not Allow In Same Received ID";disconnect($con);die;
		}
		
		if($original_receive_basis[0][csf('qc_check_by')]>0)
		{
			echo "40**This GRN Already QC Passed";disconnect($con);die;
		}
		
		$field_array_update="receive_basis*receive_purpose*receive_date*challan_no*booking_id*booking_no*emp_id*rcvd_book_no*store_id*loan_party*source*supplier_id*currency_id*exchange_rate*remarks*updated_by*update_date";
			
		$data_array_update=$cbo_receive_basis."*".$cbo_receive_purpose."*".$txt_receive_date."*".$txt_receive_chal_no."*".$txt_wo_pi_id."*".$txt_booking_pi_no."*".$grn_wo_pi_id."*".$grn_wo_pi_no."*".$cbo_store_name."*".$cbo_party."*".$cbo_source."*".$cbo_supplier."*".$cbo_currency_id."*".$txt_exchange_rate."*".$txt_mst_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		
		$is_without_order = return_field_value("entry_form", "wo_yarn_dyeing_mst", " status_active=1 and id=".$txt_wo_pi_id."", "entry_form");
		$is_with_order_yarn_service_work_order = return_field_value("booking_without_order", "wo_yarn_dyeing_mst", " status_active=1 and id=".$txt_wo_pi_id."", "booking_without_order");
		
		
		$sql_prod = sql_select("select ID, AVG_RATE_PER_UNIT, LAST_PURCHASED_QNTY, CURRENT_STOCK, STOCK_VALUE, AVAILABLE_QNTY, ALLOCATED_QNTY from product_details_master where status_active = 1 and is_deleted = 0 and company_id=$cbo_company_id and item_category_id=1");
		
		$product_data = array();
		foreach ($sql_prod as $val)
		{
			$product_data[$val["ID"]]["CURRENT_STOCK"]=$val["CURRENT_STOCK"];
			$product_data[$val["ID"]]["STOCK_VALUE"]=$val["STOCK_VALUE"];
			$product_data[$val["ID"]]["AVG_RATE_PER_UNIT"]=$val["AVG_RATE_PER_UNIT"];
			$product_data[$val["ID"]]["AVAILABLE_QNTY"]=$val["AVAILABLE_QNTY"];
			$product_data[$val["ID"]]["ALLOCATED_QNTY"]=$val["ALLOCATED_QNTY"];
		}
		
		
		$field_array_dtls="id, mst_id, receive_basis, pi_wo_batch_no, pi_wo_req_dtls_id, company_id, supplier_id, prod_id, origin_prod_id, product_code, item_category, transaction_type, transaction_date, store_id, order_uom, order_qnty, order_rate, order_ile, order_ile_cost, order_amount, cons_uom, cons_quantity, cons_rate, cons_avg_rate, dye_charge, cons_ile, cons_ile_cost, cons_amount, balance_qnty, balance_amount, no_of_bags, cone_per_bag, no_loose_cone, weight_per_bag, weight_per_cone, room, rack, self, bin_box, floor_id, remarks, inserted_by, insert_date, status_active, is_deleted, entry_form, grey_quantity";
		$field_array_dtls_update="store_id*order_uom*order_qnty*order_rate*order_ile*order_ile_cost*order_amount*cons_uom*cons_quantity*cons_rate*cons_avg_rate*dye_charge*cons_ile*cons_ile_cost*cons_amount*balance_qnty*balance_amount*no_of_bags*cone_per_bag*no_loose_cone*weight_per_bag*weight_per_cone*room*rack*self*bin_box*floor_id*remarks*updated_by*update_date";
		
		$field_array_prod_update = "brand_supplier*avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*allocated_qnty*available_qnty*updated_by*update_date";
		$presentStock = $presentStockValue = $presentAvgRate = $allocated_qnty = $available_qnty = 0;$data_array_dtls="";$previous_qnty=$previous_amount=0;
		for($i=1;$i<=$tot_row; $i++)
		{
			$count="count".$i;
			$composition="composition".$i;
			$comPersent="comPersent".$i;
			$yarnType="yarnType".$i;
			$color="color".$i;
			$TxtLot="TxtLot".$i;
			$TxtBrand="TxtBrand".$i;
			
			$floorID="floorID".$i;
			$roomID="roomID".$i;
			$rackID="rackID".$i;
			$shelfID="shelfID".$i;
			$binID="binID".$i;
			
			$receiveqnty="receiveqnty".$i;
			$greyqnty="greyqnty".$i;
			$uom="uom".$i;
			$rate="rate".$i;
			
			$avgRate="avgRate".$i;
			$DCharge="DCharge".$i;
			$ilePersent="ilePersent".$i;
			$amount="amount".$i;
			$bookCurrency="bookCurrency".$i;
			
			$woPiBalQnty="woPiBalQnty".$i;
			$overRcvQnty="overRcvQnty".$i;
			$noOfBag="noOfBag".$i;
			$conPerBag="conPerBag".$i;
			$loseCone="loseCone".$i;
			$wetPerBag="wetPerBag".$i;
			$wetPerCon="wetPerCon".$i;
			$productCode="productCode".$i;
			$dtlsRemarks="dtlsRemarks".$i;
			
			$piWoDtlsId="piWoDtlsId".$i;
			$updatedtlsid="updatedtlsid".$i;
			$previousprodid="previousprodid".$i;
			
			if($$receiveqnty > $$woPiBalQnty)
			{
				echo "20**Receive Quantity Not Allow Over Balance Quantity";disconnect($con);die;
			}
			
			$prodMSTID = $$previousprodid;
			$cons_ile=$$ilePersent*$$avgRate;
			
			if($$updatedtlsid>0)
			{
				$updateDtlsID_array[]=$$updatedtlsid;
				$data_array_dtls_update[$$updatedtlsid]=explode("*",("".$cbo_store_name."*'".$$uom."'*'".$$receiveqnty."'*'".$$rate."'*'".$$ilePersent."'*'".$$ilePersent."'*'".$$amount."'*'".$$uom."'*'".$$receiveqnty."'*'".$$avgRate."'*'".$$avgRate."'*'".$$DCharge."'*'".$$ilePersent."'*'".$cons_ile."'*'".$$bookCurrency."'*'".$$receiveqnty."'*'".$$bookCurrency."'*'".$$noOfBag."'*'".$$conPerBag."'*'".$$loseCone."'*'".$$wetPerBag."'*'".$$wetPerCon."'*'".$$roomID."'*'".$$rackID."'*'".$$shelfID."'*'".$$binID."'*'".$$floorID."'*'".$$dtlsRemarks."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				unset($prev_grn_dlts_ids[$$updatedtlsid]);
			}
			else
			{
				$cbo_yarn_count="'".$$count."'";
				$cbocomposition1="'".$$composition."'";
				$cbocomposition2="''";
				$percentage1="'".$$comPersent."'";
				$percentage2="''";
				$cbo_yarn_type="'".$$yarnType."'";
				$color_id="'".$$color."'";
				$txt_yarn_lot="'".$$TxtLot."'";
				$txt_prod_code="'".$$productCode."'";
				$cbo_uom="'".$$uom."'";
				$hdnPayMode=0;
				//echo "10**".$cbocomposition2."=".$expString[0];oci_rollback($con);disconnect($con);die;
				$insertR = true;
				$rtnString = return_product_id($cbo_yarn_count, $cbocomposition1, $cbocomposition2, $percentage1, $percentage2, $cbo_yarn_type, $color_id, $txt_yarn_lot, $txt_prod_code, $cbo_company_id, $cbo_supplier, $cbo_store_name, $cbo_uom, $yarn_type, $composition, $cbo_receive_purpose, $hdnPayMode);
				$expString = explode("***", $rtnString);
				
				if ($expString[0] == true && $expString[0] != "")
				{
					$prodMSTID = $expString[1];
				}
				else
				{
					$field_array_prod_insert = $expString[1];
					$data_array_prod_insert = $expString[2];
					//echo "10**".$expString[0]."=".$expString[0];oci_rollback($con);disconnect($con);die;
					//echo "10**"."insert into product_details_master (".$field_array_prod_insert.") values ".$data_array_prod_insert;oci_rollback($con);disconnect($con);die;
					$insertR = sql_insert("product_details_master", $field_array_prod_insert, $data_array_prod_insert, 0);
					$prodMSTID = $expString[3];
				}
				
				$id_dtls = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$data_array_dtls.="(".$id_dtls.",".$master_id.",".$cbo_receive_basis.",".$txt_wo_pi_id.",'".$$piWoDtlsId."',".$cbo_company_id.",".$cbo_supplier.",".$prodMSTID.",".$prodMSTID.",'".$$productCode."',1,1,".$txt_receive_date.",".$cbo_store_name.",'".$$uom."','".$$receiveqnty."','".$$rate."','".$$ilePersent."','".$$ilePersent."','".$$amount."','".$$uom."','".$$receiveqnty."','".$$avgRate."','".$$avgRate."','".$$DCharge."','".$$ilePersent."','".$cons_ile."','".$$bookCurrency."','".$$receiveqnty."','".$$bookCurrency."','".$$noOfBag."','".$$conPerBag."','".$$loseCone."','".$$wetPerBag."','".$$wetPerCon."','".$$roomID."','".$$rackID."','".$$shelfID."','".$$binID."','".$$floorID."','".$$dtlsRemarks."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0','1','".$$greyqnty."')";
			}
			
			
			$current_stock=$product_data[$prodMSTID]["CURRENT_STOCK"];
			$stock_value=$product_data[$prodMSTID]["STOCK_VALUE"];
			$presentAvgRate=$product_data[$prodMSTID]["AVG_RATE_PER_UNIT"];
			$available_qnty=$product_data[$prodMSTID]["AVAILABLE_QNTY"];
			$allocated_qnty=$product_data[$prodMSTID]["ALLOCATED_QNTY"];
			
			$previous_qnty=$previous_data[$$updatedtlsid]["CONS_QUANTITY"];
			$previous_amount=$previous_data[$$updatedtlsid]["CONS_AMOUNT"];
			
			if ($variable_set_allocation == 1)
			{
				if ((str_replace("'", "", $cbo_receive_purpose) == 2 || str_replace("'", "", $cbo_receive_purpose) == 12 || str_replace("'", "", $cbo_receive_purpose) == 15 || str_replace("'", "", $cbo_receive_purpose) == 38 || str_replace("'", "", $cbo_receive_purpose) == 46 || str_replace("'", "", $cbo_receive_purpose) == 50 || str_replace("'", "", $cbo_receive_purpose) == 51 ) && (str_replace("'", "", $cbo_receive_basis) == 2)) 
				{
					if ( str_replace("'", "", $cbo_receive_purpose) == 2 && ( $is_without_order == 42 || $is_without_order == 114) )
					{
						if($variable_set_smn_allocation == 1)
						{
							$allocated_qnty = $allocated_qnty + ($$receiveqnty-$previous_qnty);
							$available_qnty = $available_qnty;
						}
						else
						{
							$allocated_qnty = $allocated_qnty; 
							$available_qnty = $available_qnty + ($$receiveqnty-$previous_qnty);
						}
					}
					else
					{
						if($is_sales_order == 1 && $is_auto_allocation == 1 && ($is_without_order == 135 || ( $is_without_order == 94 && $is_with_order_yarn_service_work_order==2)))
						{
							$allocated_qnty = $allocated_qnty;
							$available_qnty = $available_qnty + ($$receiveqnty-$previous_qnty);
						}
						else 
						{
							if ( $is_without_order == 94 && $is_with_order_yarn_service_work_order==2 )
							{
								$allocated_qnty = $allocated_qnty;
								$available_qnty = $available_qnty + ($$receiveqnty-$previous_qnty);
							}
							else{
								$allocated_qnty = $allocated_qnty + ($$receiveqnty-$previous_qnty);
								$available_qnty = $available_qnty;
							}
						}
					}
				}
				else
				{
					$allocated_qnty = $allocated_qnty;
					$available_qnty = $available_qnty + ($$receiveqnty-$previous_qnty);
				}
			}
			else
			{
				$allocated_qnty = $allocated_qnty;
				$available_qnty = $available_qnty + ($$receiveqnty-$previous_qnty);
			}
			
			$currentStock = $current_stock + ($$receiveqnty-$previous_qnty);
			$StockValue = $stock_value + ($$bookCurrency-$previous_amount);
			$avgRate=0;
			if($StockValue>0 && $currentStock>0) $avgRate = $StockValue / $currentStock;
			
			$updateProdID_array[]=$prodMSTID;
			$data_array_prod_update[$prodMSTID] = explode("*",("'".$$TxtBrand."'*".number_format($avgRate, $dec_place[3], ".", "")."*".$$receiveqnty."*".$currentStock."*".number_format($StockValue, $dec_place[4], ".", "")."*'" . $allocated_qnty . "'*'" . $available_qnty . "'*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'"));
			
		}
		
		
		
		$rID=$dtlsUpdate=$rID2=$rID3=$prodUpdate=true;
		
		$rID=sql_update("inv_receive_master",$field_array_update,$data_array_update,"id",$master_id,1);
		
		if(count($updateDtlsID_array)>0)
		{
			$dtlsUpdate=execute_query(bulk_update_sql_statement("inv_transaction","id",$field_array_dtls_update,$data_array_dtls_update,$updateDtlsID_array),1);
		}
		
		if($data_array_dtls!="")
		{
			$rID2=sql_insert("inv_transaction",$field_array_dtls,$data_array_dtls,0);
		}
		
		if(count($prev_grn_dlts_ids)>0)
		{
			$rID3=execute_query("update inv_transaction set status_active=0, is_deleted=1, updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where id in(".implode(",",$prev_grn_dlts_ids).")");
		}
		
		if(count($updateProdID_array)>0)
		{
			$prodUpdate=execute_query(bulk_update_sql_statement("product_details_master","id",$field_array_prod_update,$data_array_prod_update,$updateProdID_array),1);
		}
		
		//echo "10**$rID=$dtlsUpdate=$rID2=$rID3=$prodUpdate";oci_rollback($con);disconnect($con);die;
		//oci_rollback($con);check_table_status( $_SESSION['menu_id'],0);disconnect($con);die;
		
		
		
		if($db_type==0)
		{
			if($rID && $dtlsUpdate && $rID2 && $rID3 && $prodUpdate)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'", '', $master_id)."**".str_replace("'", '', $txt_recieved_id)."**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**0**0**1";
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($rID && $dtlsUpdate && $rID2 && $rID3 && $prodUpdate)
			{
				oci_commit($con);  
				echo "1**".str_replace("'", '', $master_id)."**".str_replace("'", '', $txt_recieved_id)."**0";
			}
			else
			{
				oci_rollback($con);
				echo "6**0**0**1";
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Delete Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
	}
}



if ($action=="yarn_receive_popup_search")
{
	echo load_html_head_contents("Yarn Receive Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 

	<script>
	
		function js_set_value(id)
		{
			var ids= id.split("_");
			$('#hidden_recv_id').val(ids[0]);
			$('#hidden_posted_in_account').val(ids[1]);
			parent.emailwindow.hide();
		}
	
    </script>

	</head>

	<body>
	<div align="center" style="width:885px;">
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:883px; margin-left:3px">
			<legend>Enter search words</legend>           
				<table cellpadding="0" cellspacing="0" width="820" class="rpt_table" border="1" rules="all">
					<thead>
						<th>Supplier</th>
						<th>Received Date Range</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="200">Enter Received ID No</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
							<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
							<input type="hidden" name="hidden_recv_id" id="hidden_recv_id" class="text_boxes" value=""> 
							<input type="hidden" name="hidden_posted_in_account" id="hidden_posted_in_account" class="text_boxes" value=""> 
						</th> 
					</thead>
					<tr class="general">
						<td align="center">
							<?
								echo create_drop_down( "cbo_supplier", 150,"select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$cbo_company_id' and b.party_type in(4,5) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name",'id,supplier_name', 1, '-- ALL Supplier --',0);
							?>       
						</td>
						<td align="center">
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">To
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
						</td>
						<td align="center">	
							<?
								$search_by_arr=array(1=>"Received ID",2=>"WO/PI",3=>"Challan No");
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../../') ";							
								echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
						</td>     
						<td align="center" id="search_by_td">				
							<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
						</td> 						
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_supplier').value+'_'+document.getElementById('cbo_year_selection').value, 'create_trims_recv_search_list_view', 'search_div', 'general_item_receive_v2_controller', 'setFilterGrid(\'list_view\',-1);')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
					</tr>
			</table>
			<div style="width:100%; margin-top:5px; margin-left:2px" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="create_trims_recv_search_list_view")
{
	$data = explode("_",$data);
	
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];
	$supplier_id =$data[5];
	$cbo_year =$data[6];
	
	if($supplier_id==0) $supplier_name="%%"; else $supplier_name=$supplier_id;
	$com_arr = return_library_array("select id, company_name from lib_company","id","company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$store_arr = return_library_array("select id, store_name from lib_store_location","id","store_name");
	$user_name_arr = return_library_array("select id, user_name from user_passwd","id","user_name");
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and receive_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and receive_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";	
		}
	}
	else
	{
		if($db_type==0){ $year_cond=" and year(insert_date)=$cbo_year";}
		else if($db_type==2){ $year_cond=" and to_char(insert_date,'YYYY')=$cbo_year";}
	}
	

	if(trim($data[0])!="")
	{
		if($search_by==1)	
			$search_field_cond="and recv_number like '$search_string'";
		else if($search_by==2)
			$search_field_cond="and booking_no like '$search_string'";
		else	
			$search_field_cond="and challan_no like '$search_string'";
	}
	else
	{
		$search_field_cond="";
	}
	
	if($db_type==0){ $year_field="YEAR(insert_date) as year"; }
	else if($db_type==2){ $year_field="to_char(insert_date,'YYYY') as year"; }
	else{ $year_field="";}//defined Later
	
	$sql = "SELECT id, recv_number_prefix_num, $year_field, recv_number, receive_basis, receive_purpose, supplier_id, store_id, source, currency_id, receive_date, challan_no, challan_date, pay_mode, is_posted_account, inserted_by, emp_id, rcvd_book_no from inv_receive_master where entry_form=1 and is_multi=1 and status_active=1 and is_deleted=0 and company_id=$company_id and supplier_id like '$supplier_name' $search_field_cond $date_cond  $year_cond order by id desc"; 
	//echo $sql;
	
	//$arr=array(2=>$receive_basis_arr,3=>$supplier_arr,4=>$store_arr,8=>$currency,9=>$source);
	//echo create_list_view("list_view", "Received No,Year,Receive Basis,Supplier,Store,Receive date,Challan No,Challan Date,Currency,Source", "75,50,105,130,80,75,75,80,60","870","240",0, $sql, "js_set_value", "id", "", 1, "0,0,receive_basis,supplier_id,store_id,0,0,0,currency_id,source", $arr, "recv_number_prefix_num,year,receive_basis,supplier_id,store_id,receive_date,challan_no,challan_date,currency_id,source", "",'','0,0,0,0,0,3,0,3,0,0');
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="970" class="rpt_table">
        <thead>
			<tr>
				<th colspan="12"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
			</tr>
			<tr>
				<th width="40">Sl</th>	
				<th width="75">Received No</th>
				<th width="50">Year</th>
				<th width="105">Receive Basis</th>
				<th width="130">Supplier</th>
				<th width="80">Store</th>
                <th width="80">Receive Purpose</th>
				<th width="75">Receive date</th>
				<th width="75">Challan No</th>
				<th width="60">Currency</th>
				<th width="60">Source</th>
				<th>Insert User</th>
			</tr>
        </thead>
	</table>
    <div style="width:970px; max-height:240px; overflow-y:scroll" id="search_div" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="950" class="rpt_table" id="list_view"> 
        	<tbody>
            </tbody> 
        	<?
            $i=1;
			$result=sql_select($sql);
            foreach($result as $row)
            {
				if($row[csf('emp_id')]) $row[csf('receive_basis')]=19;  
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
        		?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]."_".$row[csf('is_posted_account')]; ?>');"> 
                    <td width="40" align="center"><p><? echo $i; ?></p></td>	
                    <td width="75"><p><? echo $row[csf('recv_number_prefix_num')]; ?>&nbsp;</p></td>
                    <td width="50" align="center"><p><? echo $row[csf('year')]; ?>&nbsp;</p></td>
                    <td width="105"><p><? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?>&nbsp;</p></td>
                    <td width="130" title="<? echo $row[csf('pay_mode')]."==".$row[csf('supplier_id')]; ?>"><p><? if($row[csf('pay_mode')] == 3 || $row[csf('pay_mode')] == 5) echo $com_arr[$row[csf('supplier_id')]]; else echo $supplier_arr[$row[csf('supplier_id')]]; ?>&nbsp;</p></td>
                    <td width="80"><p><? echo $store_arr[$row[csf('store_id')]]; ?>&nbsp;</p></td>
                    <td width="80" align="center"><p><? echo $yarn_issue_purpose[$row[csf('receive_purpose')]]; ?>&nbsp;</p></td>
                    <td width="75" align="center"><p><? if($row[csf('receive_date')]!="" && $row[csf('receive_date')]!="0000-00-00") echo change_date_format($row[csf('receive_date')]); ?>&nbsp;</p></td>
                    <td width="75"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                    <td width="60"><p><? echo $currency[$row[csf('currency_id')]]; ?>&nbsp;</p></td>
                    <td width="60"><p><? echo $source[$row[csf('source')]]; ?>&nbsp;</p></td>
                    <td><p><? echo $user_name_arr[$row[csf('inserted_by')]]; ?>&nbsp;</p></td>
                </tr>
        		<?
            	$i++;
            }
        	?>
        </table>
    </div>
    <?
	exit();
}

if($action=='populate_data_from_trims_recv')
{
	$data_array=sql_select("select id, recv_number, company_id, receive_basis, pay_mode, supplier_id, store_id, source, currency_id, challan_no, receive_date, challan_date, lc_no, exchange_rate, booking_id, booking_no, booking_without_order, receive_purpose, loan_party, remarks, emp_id, rcvd_book_no from inv_receive_master where id='$data'");//, booking_id, booking_no, booking_without_order
	foreach ($data_array as $row)
	{ 
		echo "set_receive_basis(0);\n";
		echo "document.getElementById('txt_recieved_id').value 				= '".$row[csf("recv_number")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		if(trim($row[csf("rcvd_book_no")])!="")
		{
			echo "document.getElementById('cbo_receive_basis').value 			= '19';\n";
			echo "document.getElementById('txt_wo_pi_id').value 				= '".$row[csf("emp_id")]."';\n";
			echo "document.getElementById('txt_booking_pi_no').value 			= '".$row[csf("rcvd_book_no")]."';\n";
			echo "document.getElementById('grn_receive_basis').value 			= '".$row[csf("receive_basis")]."';\n";
			echo "document.getElementById('grn_wo_pi_id').value 				= '".$row[csf("booking_id")]."';\n";
			echo "document.getElementById('grn_wo_pi_no').value 				= '".$row[csf("booking_no")]."';\n";
			
		}
		else
		{
			echo "document.getElementById('cbo_receive_basis').value 			= '".$row[csf("receive_basis")]."';\n";
			echo "document.getElementById('txt_wo_pi_id').value 				= '".$row[csf("booking_id")]."';\n";
			echo "document.getElementById('txt_booking_pi_no').value 				= '".$row[csf("booking_no")]."';\n";
		}
		
		echo "document.getElementById('cbo_receive_purpose').value 			= '".$row[csf("receive_purpose")]."';\n";
		echo "$('#cbo_company_id').attr('disabled','true')".";\n";
		
		echo "document.getElementById('txt_receive_date').value 			= '".change_date_format($row[csf("receive_date")])."';\n";
		echo "document.getElementById('txt_receive_chal_no').value 			= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('txt_mst_remarks').value 			= '".$row[csf("remarks")]."';\n";
		
		//$lc_no=return_field_value("lc_number","com_btb_lc_master_details","id='".$row[csf("lc_no")]."'");
		//echo "document.getElementById('txt_challan_date').value 			= '".change_date_format($row[csf("challan_date")])."';\n";
		//echo "load_room_rack_self_bin('requires/general_item_receive_v2_controller*4', 'store','store_td', '".$row[csf('company_id')]."','"."',this.value);\n";
		//echo "document.getElementById('txt_lc_no').value 					= '".$lc_no."';\n";
		//echo "document.getElementById('lc_id').value 						= '".$row[csf("lc_no")]."';\n";
		echo "load_drop_down( 'requires/general_item_receive_v2_controller', '".$row[csf('company_id')]."', 'load_drop_down_store', 'store_td');\n";
		echo "document.getElementById('cbo_source').value 					= '".$row[csf("source")]."';\n";
		echo "document.getElementById('cbo_store_name').value 				= '".$row[csf("store_id")]."';\n";
		
		//echo "document.getElementById('booking_without_order').value 			= '".$row[csf("booking_without_order")]."';\n";
		echo "$('#cbo_store_name').attr('disabled','true')".";\n";
		//echo "document.getElementById('cbo_pay_mode').value 			= '".$row[csf("pay_mode")]."';\n";
		echo "load_drop_down( 'requires/general_item_receive_v2_controller', '".$row[csf("company_id")]."', 'load_drop_down_supplier', 'supplier' );";
		echo "document.getElementById('cbo_supplier').value 			= '".$row[csf("supplier_id")]."';\n";
		echo "load_drop_down( 'requires/general_item_receive_v2_controller', '".$row[csf("company_id")]."', 'load_drop_down_party', 'loanParty' );";
		echo "document.getElementById('cbo_party').value 			= '".$row[csf("loan_party")]."';\n";
		echo "document.getElementById('cbo_currency_id').value 				= '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value 			= '".$row[csf("exchange_rate")]."';\n";
		echo "document.getElementById('update_id').value 					= '".$row[csf("id")]."';\n";
		
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_trims_receive',1,1);\n";  
		exit();
	}
}

function return_domestic_rate($rate,$ile_cost,$exchange_rate,$conversion_factor)
{
	$rate_ile=$rate+$ile_cost;
	$rate_ile_exchange=$rate_ile*$exchange_rate;
	$doemstic_rate=$rate_ile_exchange/$conversion_factor;
	return $doemstic_rate;	
}

if ($action=="trims_receive_entry_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	 //print_r ($data);
	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name"  );
	$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
	
	$sql="select id, recv_number, receive_basis, receive_date, booking_id, booking_no, challan_no, challan_date, lc_no, source, store_id, supplier_id, currency_id, exchange_rate, booking_without_order, pay_mode from inv_receive_master where id='$data[1]' and status_active=1 and is_deleted=0 and entry_form=24 ";
	//echo $sql;
	$dataArray=sql_select($sql);
	
   ?>
  <div style="width:985px; margin-left:20px;">
    <table width="980" cellspacing="0" align="right" border="0" >
        <tr>
            <td colspan="7" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong>
                <br><b style="font-size:13px">
                <?
                $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
                foreach ($nameArray as $result)
                { 
                    echo $result[csf('plot_no')].', ';  
                    echo $result[csf('level_no')].', ';
                    echo $result[csf('road_no')].', ';  
                    echo $result[csf('block_no')].', '; 
                    echo $result[csf('city')].', '; 
                    echo $result[csf('zip_code')].', ';  
                    echo $result[csf('province')].', '; 
                    echo $country_arr[$result[csf('country_id')]]; 
                    
                }
                ?>
                </b>
            </td>
        </tr>
        
        <tr>
            <td colspan="7" align="center" style="font-size:x-large"><strong><u>Trims Receive Challan</u></strong></center></td>
        </tr>
        <tr>
            <td width="160"><strong>System ID:</strong></td> <td width="175px"><? echo $dataArray[0][csf('recv_number')]; ?></td>
            <td width="120"><strong> Receive Basis :</strong></td><td width="175px" ><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
            <td width="125"><strong>Received Date:</strong></td><td width="175px"><? echo  change_date_format($dataArray[0][csf('receive_date')]); ?></td>
        </tr>
        <tr>
            <!--<td><strong>WO/PI:</strong></td> <td width="175px"><?echo $dataArray[0][csf('booking_no')]; ?></td>-->
            <td><strong>Challan No :</strong></td><td width="175px" ><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td><strong>Challan Date:</strong></td><td width="175px"><? echo  change_date_format($dataArray[0][csf('challan_date')]); ?></td>
            <td><strong>Source:</strong></td><td width="175px"><? echo $source[$dataArray[0][csf('source')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Supplier:</strong></td><td width="175px"><? if($dataArray[0][csf('pay_mode')]==3 || $dataArray[0][csf('pay_mode')]==5) echo $company_library[$dataArray[0][csf('supplier_id')]]; else echo $supplier_library[$dataArray[0][csf('supplier_id')]]; ?></td>
            <td><strong> Currency:</strong></td><td width="175px"><? echo $currency[$dataArray[0][csf('currency_id')]]; ?></td>
        </tr>
    </table>
    <br>
	<div style="width:100%;">
        <table align="right" cellspacing="0" width="980"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="30">SL</th>
                <th width="90" align="center">WO/PI No.</th>
                <th width="90" align="center">Item Group</th>
                <th width="110" align="center">Item Des.</th>
                <th width="70" align="center">Gmts Color</th>
                <th width="70" align="center">Item Color</th>
                <th width="70" align="center">Item Size</th>
                <th width="70" align="center">Buyer Order</th>
                 <th width="70" align="center">Internal Ref. No</th>
                <th width="40" align="center">UOM</th>
                <th width="70" align="center">WO. Qty </th>
                <th width="70" align="center">Curr. Rec. Qty </th>
                <th width="60" align="center">Rate</th>
                <th width="70" align="center">Amount</th>
                <th width="70" align="center">Total Recv. Qty.</th>
                <th width="70" align="center">Balance Qty.</th>
                <th width="50" align="center">Reject Qty</th>
            </thead>
    <?
		$mst_id=$dataArray[0][csf('id')];
		$booking_nos=''; $booking_sam_nos=''; $pi_ids=''; $orderIds='';
		//echo "select booking_no, booking_id, booking_without_order, order_id from inv_trims_entry_dtls where mst_id='$mst_id' and status_active='1' and is_deleted='0'";
		$dtls_data=sql_select("select booking_no, booking_id, booking_without_order, order_id from inv_trims_entry_dtls where mst_id='$mst_id' and status_active='1' and is_deleted='0'");
		foreach($dtls_data as $row)
		{
			$orderIds.=$row[csf('order_id')].",";
			
			if($dataArray[0][csf('receive_basis')]==1)
			{
				$pi_ids.=$row[csf('booking_id')].",";
			}
			else if($dataArray[0][csf('receive_basis')]==12)
			{
				$booking_nos.="'".$row[csf('booking_no')]."',";
			}
			else if($dataArray[0][csf('receive_basis')]==2)
			{
				if($row[csf('booking_without_order')]==1)
				{
					$booking_sam_nos.="'".$row[csf('booking_no')]."',";
				}
				else
				{
					$booking_nos.="'".$row[csf('booking_no')]."',";
				}
			}
		}
		
		$orderIds=chop($orderIds,','); 
		$piArray=array();
		//echo $orderIds.test;
		if($orderIds!="")
		{
			$orderIds=implode(",",array_unique(explode(",",$orderIds)));
			
			$piArray=array();
			$sql="select a.id, a.po_number, a.grouping as internal_ref from wo_po_break_down a where a.id in($orderIds)";
			//echo $sql;
			$po_data=sql_select($sql);
			foreach($po_data as $row)
			{
				
				$piArray[$row[csf('id')]]['po_number']=$row[csf('po_number')];
				$piArray[$row[csf('id')]]['grouping']=$row[csf('internal_ref')];
			}
			
		}
		//echo "<pre>";print_r($piArray);die;
		//echo $dataArray[0][csf('receive_basis')];die;
		if($dataArray[0][csf('receive_basis')]==2)
		{
			
			$recv_wo_data_arr=array();$recv_wo_data_arr_amt=array();
			$sql_recv = "select a.booking_no, b.order_id as po_id, b.item_group_id as item_group, b.item_description, b.gmts_color_id, b.item_color, b.item_size, a.recv_number, sum(c.quantity) as receive_qnty 
			from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c
			where a.id=b.mst_id  and a.booking_no=b.booking_no and b.id=c.dtls_id and b.trans_id=c.trans_id and c.entry_form=24 and a.entry_form=24 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.trans_type=1 and c.po_breakdown_id in($orderIds) 
			group by a.recv_number, a.booking_no, b.item_group_id, b.item_description, b.gmts_color_id, b.item_color, b.item_size, b.order_id";
			//echo $sql_recv;//die;
			$recv_data=sql_select($sql_recv);
			foreach($recv_data as $row)
			{ //pre_cost_fabric_cost_dtls_id
				$po_id_arr=array_unique(explode(",",$row[csf('po_id')]));
				foreach($po_id_arr as $po)
				{
					$recv_wo_data_arr[$row[csf('booking_no')]][$po][$row[csf('item_group')]][$row[csf('item_description')]]['recv_no'].=$row[csf('recv_number')].',';
					$recv_wo_data_arr_amt[$row[csf('recv_number')]][$row[csf('booking_no')]][$po][$row[csf('item_group')]][$row[csf('item_description')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$row[csf('item_size')]]['recv_qty']=$row[csf('receive_qnty')];		
				}
			}
			//echo "<pre>";
			//print_r($recv_wo_data_arr_amt);
			
			
			$booking_nos=chop($booking_nos,','); $booking_sam_nos=chop($booking_sam_nos,',');
			//echo $booking_nos.kok;
			if($booking_nos!="")
			{
				$booking_sam_nos=implode(",",array_unique(explode(",",$booking_sam_nos)));
				//,b.po_break_down_id
				$sql_bookingqty = sql_select("select b.booking_no, sum(c.cons) as wo_qnty, b.trim_group as item_group, c.color_number_id, c.item_color, c.description, c.gmts_sizes, c.item_size 
				from wo_booking_dtls b,wo_trim_book_con_dtls c 
				where b.id=c.wo_trim_booking_dtls_id and b.booking_no=c.booking_no and c.cons>0 and c.status_active=1 and c.is_deleted=0 and b.booking_no in($booking_nos) 
				group by b.booking_no, b.trim_group, c.color_number_id, c.item_color, c.description, c.gmts_sizes, c.item_size");
			}
			
			foreach($sql_bookingqty as $b_qty)
			{
				if($b_qty[csf('color_number_id')]=="") $b_qty[csf('color_number_id')]=0;
				if($b_qty[csf('item_color')]=="") $b_qty[csf('item_color')]=0;
				$booking_qty_arr[$b_qty[csf('booking_no')]][$b_qty[csf('item_group')]][$b_qty[csf('color_number_id')]][$b_qty[csf('item_color')]][$b_qty[csf('description')]][$b_qty[csf('gmts_sizes')]][$b_qty[csf('item_size')]]+=$b_qty[csf('wo_qnty')];
				
			}
			
			if($booking_sam_nos!="")
			{
				$booking_sam_nos=implode(",",array_unique(explode(",",$booking_sam_nos)));
				$sql_bookingqtysam = sql_select("select a.booking_no, 0 as po_break_down_id, sum(a.trim_qty) as wo_qnty,a.trim_group as item_group,a.fabric_color as item_color,a.gmts_color as color_number_id,a.fabric_description as description, a.item_size, a.gmts_size 
				from wo_non_ord_samp_booking_dtls a 
				where a.booking_no in($booking_sam_nos) and a.status_active=1 and a.is_deleted=0 
				group by a.booking_no,b.po_break_down_id, a.trim_group, a.fabric_color, a.gmts_color, a.fabric_description, a.item_size, a.gmts_size ");	
			}
			foreach($sql_bookingqtysam as $b_qty)
			{
				if($b_qty[csf('color_number_id')]=="") $b_qty[csf('color_number_id')]=0;
				if($b_qty[csf('item_color')]=="") $b_qty[csf('item_color')]=0;
				$booking_qty_arr[$b_qty[csf('booking_no')]][$b_qty[csf('item_group')]][$b_qty[csf('color_number_id')]][$b_qty[csf('item_color')]][$b_qty[csf('description')]][$b_qty[csf('gmts_size')]][$b_qty[csf('item_size')]]+=$b_qty[csf('wo_qnty')];
			}
		}
		else if($dataArray[0][csf('receive_basis')]==1)
		{
			$pi_ids=chop($pi_ids,',');
			$sql_bookingqty = sql_select("select a.id, b.item_group, b.item_color, b.color_id as color_number_id, b.item_description as description, c.po_break_down_id, sum(b.quantity) as wo_qnty 
			from com_pi_master_details a, com_pi_item_details b, wo_booking_dtls c 
			where a.id=b.pi_id and b.work_order_dtls_id=c.id and a.id in($pi_ids) and b.status_active=1 and b.is_deleted=0 
			group by a.id, b.item_group, b.item_color, b.color_id, b.item_description, c.po_break_down_id");	
			foreach($sql_bookingqty as $b_qty)
			{
				if($b_qty[csf('color_number_id')]=="") $b_qty[csf('color_number_id')]=0;
				if($b_qty[csf('item_color')]=="") $b_qty[csf('item_color')]=0;
				$booking_qty_arr[$b_qty[csf('id')]][$b_qty[csf('item_group')]][$b_qty[csf('color_number_id')]][$b_qty[csf('item_color')]][$b_qty[csf('description')]]+=$b_qty[csf('wo_qnty')];
			}
		}
		else if($dataArray[0][csf('receive_basis')]==12)
		{ 
			$booking_nos=chop($booking_nos,',');	
			$sql_bookingqty = sql_select("select b.po_break_down_id,c.trim_group as item_group,b.booking_no, sum(b.requirment) as wo_qnty,b.item_color,b.gmts_sizes ,b.description from 
			wo_booking_mst a,wo_trim_book_con_dtls b,wo_booking_dtls c
			 where a.booking_no=b.booking_no and a.supplier_id=147 and a.item_category=4 and c.po_break_down_id=b.po_break_down_id and c.job_no=b.job_no and c.booking_no=b.booking_no and c.booking_type=2  and b.booking_no in($booking_nos) group by b.booking_no,b.po_break_down_id, c.trim_group, b.item_color, b.gmts_sizes, b.description");
			 	
			foreach($sql_bookingqty as $b_qty)
			{
				if($b_qty[csf('gmts_sizes')]=="") $b_qty[csf('gmts_sizes')]=0;
				if($b_qty[csf('item_color')]=="") $b_qty[csf('item_color')]=0;
				$booking_qty_arr[$b_qty[csf('booking_no')]][$b_qty[csf('po_break_down_id')]][$b_qty[csf('item_group')]][$b_qty[csf('gmts_sizes')]][$b_qty[csf('item_color')]][$b_qty[csf('description')]]=$b_qty[csf('wo_qnty')];
			}
			
			
		}
		
		//echo "<pre>";print_r($booking_qty_arr);die;

        $i=1;$total_rec_qty=0; $total_rec_balance_qty=0;
        
		 $sql_dtls="select b.booking_no, b.booking_id, b.booking_without_order, b.item_group_id, b.item_description, b.order_id, b.gmts_color_id, b.item_color, b.item_size, sum(b.cons_qnty) as cons_qnty, b.order_uom, b.cons_uom, sum(b.receive_qnty) as receive_qnty, max(b.rate) as rate, sum(b.amount) as amount, sum(b.reject_receive_qnty) as reject_receive_qnty, b.gmts_size_id 
		from inv_trims_entry_dtls b 
		where b.mst_id='$mst_id' and b.status_active='1' and b.is_deleted='0'
		group by b.booking_no, b.booking_id, b.booking_without_order, b.item_group_id, b.item_description, b.order_id, b.gmts_color_id, b.item_color, b.item_size, b.order_uom, b.cons_uom, b.gmts_size_id";
		
        //echo $sql_dtls;
        $sql_result=sql_select($sql_dtls);
        foreach($sql_result as $row)
        {
			//print_r($booking_qty_arr);
            if ($i%2==0)  
                $bgcolor="#E9F3FF";
            else
                $bgcolor="#FFFFFF";
                
                $order_id_arr=explode(",",$row[csf('order_id')]);
				
				$order_number='';$recv_no_arr='';$grouping_number='';$grouping_number_arr=array();
				//echo "<pre>";print_r($piArray);
				foreach($order_id_arr as $po_id)
				{
					$prev_recv_qty=0;
					//echo $po_id."=".$piArray[$po_id]['po_number'];die;
					$order_number.=$piArray[$po_id]['po_number'].',';
					$grouping_number_arr[$piArray[$po_id]['grouping']]=$piArray[$po_id]['grouping'];
					$recv_no_arr=implode(",",array_unique(explode(",",$recv_wo_data_arr[$row[csf('booking_no')]][$po_id][$row[csf('item_group_id')]][$row[csf('item_description')]]['recv_no'])));
					
					$recv_id_arr=explode(",",$recv_no_arr);
					foreach($recv_id_arr as $recv_id)
					{
						if($recv_id!=$dataArray[0][csf('recv_number')])
						{
							$prev_recv_qty+=$recv_wo_data_arr_amt[$recv_id][$row[csf('booking_no')]][$po_id][$row[csf('item_group_id')]][$row[csf('item_description')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$row[csf('item_size')]]['recv_qty'];
						}
					}
				}
				//echo $prev_recv_qty;
				$order_number=chop($order_number,',');
				//$grouping_number=chop($grouping_number,',');
				$grouping_number=implode(',',$grouping_number_arr);
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>
                    <td title="<?= $row[csf('order_id')]; ?>"><p><? echo $row[csf('booking_no')]; ?></p></td>
                    <td><p><? echo $item_library[$row[csf('item_group_id')]]; ?></p></td>
                    <td><p><? echo $row[csf('item_description')]; ?></p></td>
                    <td><p><? echo $color_library[$row[csf('gmts_color_id')]]; ?></p></td>
                    <td><p><? echo $color_library[$row[csf('item_color')]]; ?></p></td>
                    <td><p><? echo $row[csf('item_size')]; ?></p></td>
                    <td width="170" style="word-break:break-all;"><? echo $order_number; ?></td>
                    <td width="170" style="word-break:break-all;"><? echo $grouping_number; ?></td>
                    <td align="center"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
                    <td align="right" title="<? echo $row[csf('booking_no')]."=".$row[csf('item_group_id')]."=".$row[csf('gmts_color_id')]."=".$row[csf('item_color')]."=".$des_dtls."=".$row[csf('gmts_size_id')]."=".$row[csf('item_size')];?>">
					<?
                        if($row[csf('gmts_size_id')]=="") $row[csf('gmts_size_id')]=0;
                        if($row[csf('gmts_color_id')]=="") $row[csf('gmts_color_id')]=0;
                        if($row[csf('item_color')]=="") $row[csf('item_color')]=0;							
                        $woorder_qty='';
                        $descrip_arr=explode(",",$row[csf('item_description')]);
                        $last_index=end(array_values($descrip_arr));
                        $last_index=str_replace("[","",$last_index);
                        $last_index=str_replace("]","",$last_index);
                        if(trim($last_index)=="BS") $des_dtls=chop($row[csf('item_description')],', [BS]'); else $des_dtls=$row[csf('item_description')];
                        if($dataArray[0][csf('receive_basis')]==1)
                        {
							$woorder_qty = $booking_qty_arr[$row[csf('booking_id')]][$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$des_dtls];
                        }
                        if($dataArray[0][csf('receive_basis')]==12)
                        {
                            $woorder_qty=$booking_qty_arr[$row[csf('booking_no')]][$row[csf('order_id')]][$row[csf('item_group_id')]][$row[csf('gmts_size_id')]][$row[csf('item_color')]][$row[csf('item_description')]];
                        }
                        else
                        {
							$woorder_qty = $booking_qty_arr[$row[csf('booking_no')]][$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$des_dtls][$row[csf('gmts_size_id')]][$row[csf('item_size')]];
                            
                        }
                        $total_woorder_qty+=$woorder_qty;
                        echo number_format($woorder_qty,2,".",""); 
                        $tot_recv_qty=$row[csf('receive_qnty')]+$prev_recv_qty;
                        $tot_recv_balance=$woorder_qty-$tot_recv_qty;//$row[csf('receive_qnty')]+$prev_recv_qty;
                    ?>
                    </td>
                    <td align="right" title="<? echo $des_dtls; ?>"><? echo number_format($row[csf('receive_qnty')],2,".",""); ?></td>
                    <td align="right"><? echo number_format($row[csf('rate')],4,'.',''); ?></td>
                    <td align="right"><? echo number_format($row[csf('amount')],2,'.',''); ?></td>
                    <td align="right"><? echo number_format($tot_recv_qty,2,'.',''); ?></td>
                    <td align="right"><? echo number_format($tot_recv_balance,2,'.',''); ?></td>
                    <td align="right"><? echo number_format($row[csf('reject_receive_qnty')],2,'.',''); ?></td>
                </tr>
            <?
			$i++;
			$tot_rec_qty+=$row[csf('receive_qnty')];
			$tot_amount+=$row[csf('amount')];
			$tot_reject_qty+=$row[csf('reject_receive_qnty')];
			$total_rec_qty+=$tot_recv_qty;
			$total_rec_balance_qty+=$tot_recv_balance;
        }
       ?>
            <tr bgcolor="#dddddd">
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                 <td>&nbsp;</td>

                <td colspan="2" align="right"><b>Total :</b></td>
                <td align="right"><? echo number_format($total_woorder_qty,2,'.',''); ?></td>
                <td align="right"><? echo number_format($tot_rec_qty,2,'.',''); ?></td>
                <td>&nbsp;</td>
                <td align="right"><? echo number_format($tot_amount,2,'.',''); ?></td>
                <td align="right"><? echo number_format($total_rec_qty,2,'.',''); ?></td>
                <td align="right"><? echo number_format($total_rec_balance_qty,2,'.',''); ?></td>
                <td align="right"><? echo number_format($tot_reject_qty,2,'.',''); ?></td>
            </tr>
       </table>
       <br>
       <?
		  echo signature_table(35, $data[0], "980px");
	   ?>
	</div>
  </div>
   <?
  exit();
}

?>