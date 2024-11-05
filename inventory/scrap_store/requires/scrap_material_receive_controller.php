<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$user_id=$_SESSION['logic_erp']['user_id'];
/**
 * User Credential start
 */
$userCredential = sql_select("select unit_id as company_id, item_cate_id, company_location_id, store_location_id from user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$location_credential_id = $userCredential[0][csf('company_location_id')];
$store_credential_id = $userCredential[0][csf('store_location_id')];

if ($company_id !='') {
    $company_credential_cond = "and comp.id in($company_id)";
}
if ($location_credential_id !='') {
    $location_credential_cond = "and id in($location_credential_id)";
}
if ($store_credential_id !='') {
    $store_credential_cond = "and a.id in($store_credential_id)";
}
//============= User credential end ==================
 //-------------------START --------------------------

if($action == "load_drop_down_location")
{
	$data = explode('_',$data); 
    echo create_drop_down( "cbo_location", 170, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data[0]' $location_credential_cond order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/scrap_material_receive_controller', this.value+'_'+$data[0]+'_'+$data[1], 'load_drop_down_store', 'store_td');load_drop_down( 'requires/scrap_material_receive_controller', this.value+'_'+$data[2], 'load_drop_down_floor','folor_td');",0 );
	exit();
}

if ($action=="load_drop_down_store")
{
	$data = explode('_',$data);
	$category = '';
	if(!empty($data[2])){
		$category = " and b.category_type=$data[2]";
	}
 	echo create_drop_down( "cbo_store", 170, "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data[1]  and a.location_id =$data[0]   $store_credential_cond $category group by a.id,a.store_name order by a.store_name","id,store_name",1, "-- Select Store --", "", 0, "", 1 );
	exit();
}

if( $action=="load_drop_down_buyer" )
{	
	echo create_drop_down( "cbo_buyer", 170, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );  
	exit();
}

// select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count

if($action=="load_drop_down_supplier")
{
	if($data)$tag_company_con=" and c.tag_company=$data[0]"; else $tag_company_con="";
	echo create_drop_down("cbo_supplier", 170, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_party_type b,lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id $tag_company_con and a.status_active=1 and b.party_type=21 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
	exit();
}

if ($action=="load_drop_down_from_store")
{  // $data = explode("_", $data);
 	echo create_drop_down( "cbo_from_store", 170, "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and b.category_type=$data $store_credential_cond group by a.id,a.store_name order by a.store_name","id,store_name",1, "-- Select Store --", "", "", "", 1 );
	exit();
}

if ($action=="load_drop_down_floor")
{
	$data = explode("_", $data);
		$location=$data[0];
		$company_id = $data[1];
		$sql="SELECT b.floor_id, a.floor_room_rack_name || ' - I' as floor_room_rack_name
		FROM lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b
		WHERE a.floor_room_rack_id = b.floor_id AND b.location_id = '$location' AND a.company_id = '$company_id' AND a.status_active = 1
		AND a.is_deleted = 0  AND b.status_active = 1 AND b.is_deleted = 0 GROUP BY b.floor_id, a.floor_room_rack_name
		UNION ALL
		SELECT id as floor_id, floor_name || ' - P ' as floor_room_rack_name
		FROM lib_prod_floor WHERE location_id = '$location' AND company_id = '$company_id' GROUP BY id, floor_name";

	echo create_drop_down( "cbo_folor_name", 170, $sql,"floor_id,floor_room_rack_name", 1, "--Select Floor--", 0, "",0 );
}

if ($action=="load_drop_down_division")
{
	$sql="select id,division_name from lib_division where company_id=$data and is_deleted=0  and status_active=1";
	$result=sql_select($sql);
	$selected=0;
	if (count($result)==1) {
		$selected=$result[0][csf('id')];
	}
	
	echo create_drop_down( "cbo_division_name", 170,$sql,"id,division_name", 1, "-- Select --", 0, "load_drop_down( 'requires/scrap_material_receive_controller', this.value, 'load_drop_down_department','department_td');" );
	die;
}

if ($action=="load_drop_down_department")
{
	echo create_drop_down( "cbo_department_name", 170,"select id,department_name from lib_department where division_id=$data and is_deleted=0 and status_active=1","id,department_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/scrap_material_receive_controller', this.value, 'load_drop_down_section','section_td');" );
	die;
}


if ($action=="load_drop_down_section")
{
	// echo $data;
	//echo "select id,section_name from lib_section where department_id=$data and is_deleted=0 and status_active=1","id,section_name";
	if ($data != ''){
		echo create_drop_down( "cbo_section_name", 170,"select id,section_name from lib_section where department_id=$data and is_deleted=0 and status_active=1","id,section_name", 1, "-- Select --", $selected, "" );
	} else {
		echo create_drop_down( "cbo_section_name", 170,$blank_array,"", 1, "-- Select --", $selected, "" );
	}
	die;
}

if ($action=="load_drop_down_uom_____")
{
	$data = explode('_',$data);
	//echo "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data[1] group by a.id,a.store_name order by a.store_name";
 	echo create_drop_down( "cbo_uom", 160, "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data[1] group by a.id,a.store_name order by a.store_name","id,store_name","", 1, "-- Select Store --", 0, "", 1 );
	exit();
}

if($action=="check_conversion_rate") //Conversion Exchange Rate
{ 
	$data=explode("**",$data);
	if($db_type==0){
		$conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
	} else {
		$conversion_date=change_date_format($data[1], "d-M-y", "-",1);
	}
	$currency_rate=set_conversion_rate( $data[0], $conversion_date );
	echo "1"."_".$currency_rate;
	exit();	
}

if ($action=="item_description_popup")
{
	echo load_html_head_contents("Item Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
    ?>
	<script type="text/javascript">
		function js_set_value(id)
		{
			//alert ("Hellow"+id);return;
			$('#hidden_prod_description').val(id);
			parent.emailwindow.hide();
		}
    
    	$(function(){
    		var tableFilters = { }
	    	setFilterGrid("html_search",-1,tableFilters);
    	});
    </script>
    </head>
    <body>
		<form style="text-align: center;" action="" name="item_descrption_form " id="item_descrption_form">
			<input type="hidden" name="hidden_prod_description" id="hidden_prod_description" />
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" >
				<thead>
					<tr>
						<th>&nbsp</th>
						<th colspan="2">Transaction Date Range</th>
						<th>&nbsp</th>
					</tr>                    
					<tr>
						<th width="150">System ID</th>
						<th width="150" style="text-align:right;"><span style="padding-right:50px;">From Date</span></th>
						<th width="150" style="text-align:left;"><span style="padding-left:50px;">To Date</span></th>
						
						<th width="150">
						<input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('item_descrption_form','search_div','','','','');"></th>
					</tr>                    
				</thead>
				<tbody>
					<tr>
						<td align="center">
							<input type="text" style="width:150px" class="text_boxes"  name="txt_system_id" id="txt_system_id" />
						</td>
						
						<td align="right">
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"  placeholder="From Date" style="width: 150px;" />
						</td>
						<td align="left">
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker"  placeholder="To Date"  style="width: 150px;"/>
						</td>
						
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company_id; ?>+'_'+<? echo $cbo_category_id; ?>+'_'+<? echo $cbo_from_store_name; ?>+'_'+<? echo $cbo_receive_basis; ?>+'_'+document.getElementById('txt_system_id').value+'_'+<? echo $cbo_store; ?>+'_'+document.getElementById('cbo_year_selection').value, 'create_item_description_search_list_view', 'search_div', 'scrap_material_receive_controller', 'setFilterGrid(\'list_view\',0)');" style="width:100px; margin: 0 auto;">
						</td>
					</tr>
					<tr>
						<td colspan="4"><? echo load_month_buttons(1);  ?></td>
					</tr>
				</tbody>
			</table>
			<br>
			<div valign="top" id="search_div" align="left"></div>
		</form>
		<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<?
	exit();
}

if($action=="create_item_description_search_list_view")
{
    $data=explode('_',$data);
	//echo '<pre>';print_r($data);
	$item_category_id = $data[3];
	//$cbo_store = $data[7];
	$year = $data[8];
	$receive_basis = $data[5];
	$company_cond=$category_cond=$store_cond="";
	if ($data[2]!=0) $company_cond=" and a.company_id='$data[2]'";
	if ($data[3]!=0) $category_cond=" and a.item_category='$data[3]'";
	if ($data[4]!=0) $store_cond=" and a.store_id='$data[4]'";

	$brand_arr=return_library_array("select id, brand_name from lib_brand where status_active=1",'id','brand_name');
	$store_arr=return_library_array("select id, store_name from lib_store_location where status_active=1",'id','store_name');
	$color_arr=return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$item_group_arr=return_library_array("select id, item_name from lib_item_group where status_active=1",'id','item_name');
	$body_part_array=return_library_array("select id, body_part_full_name from lib_body_part where status_active=1",'id','body_part_full_name');
	if ($item_category_id==2) $batch_arr = return_library_array( "select id, batch_no from pro_batch_create_mst where status_active=1",'id','batch_no');
	$previous_scrap_received_arr=return_library_array("select trans_id, id from inv_scrap_receive_dtls where status_active=1 and is_deleted=0 and trans_id is not null",'trans_id','id');

	/*if (!empty($previous_scrap_received_arr))
    {     
        $previous_scrap_received_cond = '';
        if($db_type==2 && count($previous_scrap_received_arr)>999)
        {
            $scrap_receivedIds = array_keys($previous_scrap_received_arr);
            $previous_scrap_received_cond = ' and (';

            $scrap_receivedIdArr = array_chunk($scrap_receivedIds,999);
            foreach($scrap_receivedIdArr as $ids)
            {
                $ids = implode(',',$ids);
                $previous_scrap_received_cond .= " a.id not in($ids) and ";
            }
            
            $previous_scrap_received_cond = rtrim($previous_scrap_received_cond,'and ');
            $previous_scrap_received_cond .= ')';
        }
        else
        {
            $scrap_receivedIds = implode(',',array_keys($previous_scrap_received_arr));
            $previous_scrap_received_cond = " and a.id not in ($scrap_receivedIds) ";
        }
    }  */
	
	$transaction_date_cond ="";
	if($db_type==0)
	{
		if ($data[2]!='' &&  $data[3]!='') $transaction_date_cond = "and a.transaction_date between '".change_date_format($data[0],'yyyy-mm-dd')."' and '".change_date_format($data[1],'yyyy-mm-dd')."'";
	}
	else
	{
		if ($data[0]!='' &&  $data[1]!=''){
			$transaction_date_cond = "and a.transaction_date between '".change_date_format($data[0], "", "",1)."' and '".change_date_format($data[1], "", "",1)."'";
		}else if($year){
			$transaction_date_cond = " and TO_CHAR(a.transaction_date, 'YYYY') = $year";
		}
	}
	//echo $transaction_date_cond; exit();
	if ($item_category_id==5 || $item_category_id==6 || $item_category_id==7 || $item_category_id==23)
	{
		$selet_lot_cond="a.batch_lot";
	} else $selet_lot_cond="b.lot";

   
    $system_id_cond="";
	if($receive_basis == 1) //scrap receive basis -> Receive-Reject
	{
		if ($data[6]!=0) $system_id_cond=" and c.recv_number_prefix_num='$data[6]'";
		$transaction_type_cond = "  and a.transaction_type in(1,4,5)";	
		$reject_qnty_cond = " and a.cons_reject_qnty > 0";
		$sql= "SELECT a.id as TRANS_ID, a.mst_id as MST_ID, a.store_id as STORE_ID, a.transaction_date as TRANSACTION_DATE, a.cons_reject_qnty as CONS_QNTY, a.transaction_type as TRANSACTION_TYPE, a.body_part_id as BODY_PART_ID, a.pi_wo_batch_no as PI_WO_BATCH_NO, $selet_lot_cond as LOT, b.id as PROD_ID, b.item_group_id as ITEM_GROUP_ID, b.item_category_id as ITEM_CATEGORY_ID, b.product_name_details as PRODUCT_NAME_DETAILS, b.unit_of_measure as UNIT_OF_MEASURE, b.current_stock as CURRENT_STOCK, b.color as COLOR, b.item_color as ITEM_COLOR, b.gsm as GSM, b.brand as BRAND, b.dia_width as DIA_WIDTH, b.item_size as ITEM_SIZE, b.avg_rate_per_unit as AVG_RATE_PER_UNIT, c.recv_number as SYSTEM_ID
		from inv_receive_master c, inv_transaction a, product_details_master b
		where c.id=a.mst_id and a.prod_id=b.id $transaction_type_cond $company_cond $system_id_cond $category_cond $transaction_date_cond $store_cond $reject_qnty_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 		
		order by c.recv_number desc";  //b.LOT,
	} 
	elseif ($receive_basis == 2) //scrap receive basis -> Issue Damage
	{
		if ($data[6]!=0) $system_id_cond=" and c.issue_number_prefix_num='$data[6]'";
		$transaction_type_cond = "  and a.transaction_type in(2,3,6) ";
		$reject_qnty_cond = " and a.cons_quantity > 0";

		$sql= "SELECT a.id as TRANS_ID, a.mst_id as MST_ID, a.store_id as STORE_ID, a.transaction_date as TRANSACTION_DATE, a.cons_quantity as CONS_QNTY, a.transaction_type as TRANSACTION_TYPE, a.body_part_id as BODY_PART_ID, a.pi_wo_batch_no as PI_WO_BATCH_NO, $selet_lot_cond as LOT, b.id as PROD_ID, b.item_group_id as ITEM_GROUP_ID, b.item_category_id as ITEM_CATEGORY_ID, b.product_name_details as PRODUCT_NAME_DETAILS, b.unit_of_measure as UNIT_OF_MEASURE, b.current_stock as CURRENT_STOCK, b.color as COLOR, b.item_color as ITEM_COLOR, b.gsm as GSM, b.brand as BRAND, b.dia_width as DIA_WIDTH, b.item_size as ITEM_SIZE, b.avg_rate_per_unit as AVG_RATE_PER_UNIT, c.issue_number as SYSTEM_ID
		from inv_issue_master c, inv_transaction a, product_details_master b
		where c.id=a.mst_id and a.prod_id=b.id  $transaction_type_cond $company_cond $system_id_cond $category_cond  $transaction_date_cond $store_cond $reject_qnty_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by c.issue_number desc";
		//echo $sql; exit();
	} 
	elseif ($receive_basis == 3) //scrap receive basis -> Issue Scrap store
	{
		if ($data[6]!=0) $system_id_cond=" and c.issue_number_prefix_num='$data[6]'";
		$transaction_type_cond = "  and a.transaction_type in(2,3)";
		$reject_qnty_cond = " and a.cons_quantity > 0";

		$sql= "SELECT a.id as TRANS_ID, a.mst_id as MST_ID, a.store_id as STORE_ID, a.transaction_date as TRANSACTION_DATE, a.cons_quantity as CONS_QNTY, a.transaction_type as TRANSACTION_TYPE, a.body_part_id as BODY_PART_ID, a.pi_wo_batch_no as PI_WO_BATCH_NO, $selet_lot_cond as LOT, b.id as PROD_ID, b.item_group_id as ITEM_GROUP_ID, b.item_category_id as ITEM_CATEGORY_ID, b.product_name_details as PRODUCT_NAME_DETAILS, b.unit_of_measure as UNIT_OF_MEASURE, b.current_stock as CURRENT_STOCK, b.color as COLOR, b.item_color as ITEM_COLOR, b.gsm as GSM, b.brand as BRAND, b.dia_width as DIA_WIDTH, b.item_size as ITEM_SIZE, b.avg_rate_per_unit as AVG_RATE_PER_UNIT, c.issue_number as SYSTEM_ID
		from inv_issue_master c, inv_transaction a, product_details_master b
		where c.id=a.mst_id and a.prod_id=b.id and c.issue_purpose=62 $transaction_type_cond $company_cond $system_id_cond $category_cond  $transaction_date_cond $store_cond $reject_qnty_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by c.issue_number desc ";
	}   
	// echo $sql;die;
	$main_data_arr=array();
	$prod_id_arr=array();
	$description_resutl = sql_select($sql);
	foreach ($description_resutl as $row) 
	{
		if (!array_key_exists($row["TRANS_ID"], $previous_scrap_received_arr))
		{
			$main_data_arr[$row["TRANS_ID"]]['TRANS_ID']=$row["TRANS_ID"];
			$main_data_arr[$row["TRANS_ID"]]['ITEM_CATEGORY_ID']=$row["ITEM_CATEGORY_ID"];
			$main_data_arr[$row["TRANS_ID"]]['PI_WO_BATCH_NO']=$row["PI_WO_BATCH_NO"];
			$main_data_arr[$row["TRANS_ID"]]['LOT']=$row["LOT"];
			$main_data_arr[$row["TRANS_ID"]]['MST_ID']=$row["MST_ID"];
			$main_data_arr[$row["TRANS_ID"]]['PROD_ID']=$row["PROD_ID"];
			$main_data_arr[$row["TRANS_ID"]]['SYSTEM_ID']=$row["SYSTEM_ID"];
			$main_data_arr[$row["TRANS_ID"]]['PRODUCT_NAME_DETAILS']=$row["PRODUCT_NAME_DETAILS"];
			$main_data_arr[$row["TRANS_ID"]]['STORE_ID']=$row["STORE_ID"];
			$main_data_arr[$row["TRANS_ID"]]['UNIT_OF_MEASURE']=$row["UNIT_OF_MEASURE"];
			$main_data_arr[$row["TRANS_ID"]]['COLOR']=$row["COLOR"];
			$main_data_arr[$row["TRANS_ID"]]['GSM']=$row["GSM"];
			$main_data_arr[$row["TRANS_ID"]]['BRAND']=$row["BRAND"];
			$main_data_arr[$row["TRANS_ID"]]['TRANSACTION_DATE']=$row["TRANSACTION_DATE"];
			$main_data_arr[$row["TRANS_ID"]]['CONS_QNTY']=$row["CONS_QNTY"];

			$prod_id_arr[$row["PROD_ID"]]=$row["PROD_ID"];
		}		
	}
	// echo '<pre>';print_r($prod_id_arr);
	// die;

	// ============================== data store to gbl table ==================================
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from=1 and ENTRY_FORM=52");
	oci_commit($con);
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 52, 1, $prod_id_arr, $empty_arr);//PROD_ID

	$int_ref_sql = "SELECT b.grouping as ref, o.prod_id
	from wo_po_break_down b, order_wise_pro_details o, gbl_temp_engine tmp
	where b.id=o.po_breakdown_id and o.entry_form in (7,37) and o.trans_id <> 0 and tmp.entry_form=52 and tmp.ref_from=1 and tmp.user_id = $user_id and o.prod_id = tmp.ref_val";
	// echo $int_ref_sql;
	$int_ref_result = sql_select($int_ref_sql);
	$int_ref_arr = array();
	foreach ($int_ref_result as $row) 
	{
		$int_ref_arr[$row["PROD_ID"]] = $row["REF"];
	}
	// echo '<pre>';print_r($int_ref_arr);


	// =================================== delete data ========================================
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from=1 and ENTRY_FORM=48");
	oci_commit($con);
	disconnect($con);

	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1120" class="rpt_table" id="html_search">
		<thead>
			<tr>
				<th width="40" align="center">SL</th>
				<th width="80" align="center">Product Id</th>
				<th width="100" align="center">System ID</th>
				<th width="70" align="center">Ref No</th>
				<th width="100" align="center">Item Category</th>
				<th width="150" align="center">Description</th>
				<th width="100" align="center">Store</th>
				<th width="80" align="center">Lot/Batch</th>
				<th width="60" align="center">UOM</th>
				<th width="70" align="center">Color</th>
				<th width="60" align="center">GSM</th>
				<th width="70" align="center">Brand</th>
				<th width="80" align="center">Transaction Date</th>
				<th align="right">Qty</th>
			</tr>
		</thead>
		<tbody id="list_view">
		    <?			
			$i=1;
			foreach ($main_data_arr as $trans_id => $row) 
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if ($row["ITEM_CATEGORY_ID"] == 2) {
					$lot_batch = $batch_arr[$row["PI_WO_BATCH_NO"]];
				} else {
					$lot_batch = $row["LOT"];
				}
				$avg_rate_per_unit=number_format($row["AVG_RATE_PER_UNIT"],2,'.','');
				?>
				<tr bgcolor="<?= $bgcolor; ?>" style="cursor:pointer;" onClick='js_set_value("<? echo $row["MST_ID"]."__".$row["TRANS_ID"]."__".$row["PROD_ID"]."__".$int_ref_arr[$row["PROD_ID"]]."__".$row["PRODUCT_NAME_DETAILS"]; ?>")' title="<? echo $row["TRANS_ID"]; ?>">
					<td width="40" align="center"><?= $i;?></td>
					<td width="80" align="center"><p><?= $row["PROD_ID"]; ?></p></td>
					<td width="100" align="center"><?= $row["SYSTEM_ID"]; ?></td>
					<td width="70" align="center"><?= $int_ref_arr[$row["PROD_ID"]]; ?></td>
					<td width="100" align="center"><p><?= $item_category[$row["ITEM_CATEGORY_ID"]]; ?></p></td>
					<td width="150" align="center"><p><?= $row["PRODUCT_NAME_DETAILS"]; ?></p></td>
					<td width="100" align="center"><p><?= $store_arr[$row["STORE_ID"]]; ?></p></td>
					<td width="80" align="center" style="word-wrap: break-all;"><p><?= $lot_batch; ?></p></td>
					<td width="60" align="center"><p><?= $unit_of_measurement[$row["UNIT_OF_MEASURE"]]; ?></p></td>
					<td width="70" align="center"><p><?= $color_arr[$row["COLOR"]]; ?></p></td>
					<td width="60" align="center"><p><?= $row["GSM"]; ?></p></td>
					<td width="70" align="center"><p><?= $brand_arr[$row["BRAND"]]; ?></p></td>
					<td width="80" align="center"><p><?= change_date_format($row["TRANSACTION_DATE"]); ?></p></td>
					<td align="right"><p><?= $row["CONS_QNTY"]; ?></p></td>
				</tr>
				<?
				$i++;
			}
		    ?>
		</tbody>
	</table>
	<?
	exit();
}

if($action=="show_product_listview")
{
	$ex_data = explode("**",$data);
	$company_id = $ex_data[0];
	$cbo_category_id = $ex_data[1];
	$mst_id = $ex_data[2];
	$receive_basis=$ex_data[3];
	$ref_no = $ex_data[4];
	
	//$system_id = $data[6];
	$company_cond=$category_cond="";
	if ($company_id!=0) $company_cond=" and a.company_id=$company_id";
	if ($cbo_category_id!=0) $category_cond=" and a.item_category=$cbo_category_id";
	$previous_scrap_received_arr=return_library_array("select trans_id, id from inv_scrap_receive_dtls where status_active=1 and is_deleted=0 and trans_id is not null",'trans_id','id');
	
   
	if($receive_basis == 1) //scrap receive basis -> Receive-Reject
	{
		$transaction_type_cond = "  and a.transaction_type in(1,4,5)";	
		$reject_qnty_cond = " and a.cons_reject_qnty > 0";
		$sql= "SELECT a.id as TRANS_ID, a.mst_id as MST_ID, a.store_id as STORE_ID, a.transaction_date as TRANSACTION_DATE, a.cons_reject_qnty as CONS_QNTY, a.transaction_type as TRANSACTION_TYPE, a.body_part_id as BODY_PART_ID, a.pi_wo_batch_no as PI_WO_BATCH_NO, b.id as PROD_ID, b.item_group_id as ITEM_GROUP_ID, b.item_category_id as ITEM_CATEGORY_ID, b.product_name_details as PRODUCT_NAME_DETAILS, b.unit_of_measure as UNIT_OF_MEASURE, b.current_stock as CURRENT_STOCK, b.color as COLOR, b.item_color as ITEM_COLOR, b.gsm as GSM, b.brand as BRAND, b.dia_width as DIA_WIDTH, b.item_size as ITEM_SIZE, b.avg_rate_per_unit as AVG_RATE_PER_UNIT, c.recv_number as SYSTEM_ID
		from inv_receive_master c, inv_transaction a, product_details_master b
		where c.id=a.mst_id and a.prod_id=b.id and a.mst_id=$mst_id $transaction_type_cond $company_cond $category_cond $reject_qnty_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
		order by b.id desc";  //b.LOT,
	} 
	elseif ($receive_basis == 2) //scrap receive basis -> Issue Damage
	{
		$transaction_type_cond = "  and a.transaction_type in(2,3,6) ";
		$reject_qnty_cond = " and a.cons_quantity > 0";

		$sql= "SELECT a.id as TRANS_ID, a.mst_id as MST_ID, a.store_id as STORE_ID, a.transaction_date as TRANSACTION_DATE, a.cons_quantity as CONS_QNTY, a.transaction_type as TRANSACTION_TYPE, a.body_part_id as BODY_PART_ID, a.pi_wo_batch_no as PI_WO_BATCH_NO, b.id as PROD_ID, b.item_group_id as ITEM_GROUP_ID, b.item_category_id as ITEM_CATEGORY_ID, b.product_name_details as PRODUCT_NAME_DETAILS, b.unit_of_measure as UNIT_OF_MEASURE, b.current_stock as CURRENT_STOCK, b.color as COLOR, b.item_color as ITEM_COLOR, b.gsm as GSM, b.brand as BRAND, b.dia_width as DIA_WIDTH, b.item_size as ITEM_SIZE, b.avg_rate_per_unit as AVG_RATE_PER_UNIT, c.issue_number as SYSTEM_ID
		from inv_issue_master c, inv_transaction a, product_details_master b
		where c.id=a.mst_id and a.prod_id=b.id and a.mst_id=$mst_id $transaction_type_cond $company_cond $category_cond $reject_qnty_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
		order by b.id desc";
	} 
	elseif ($receive_basis == 3) //scrap receive basis -> Issue Scrap store
	{
		$transaction_type_cond = "  and a.transaction_type in(2,3,6)";
		$reject_qnty_cond = " and a.cons_quantity > 0";

		$sql= "SELECT a.id as TRANS_ID, a.mst_id as MST_ID, a.store_id as STORE_ID, a.transaction_date as TRANSACTION_DATE, a.cons_quantity as CONS_QNTY, a.transaction_type as TRANSACTION_TYPE, a.body_part_id as BODY_PART_ID, a.pi_wo_batch_no as PI_WO_BATCH_NO, b.id as PROD_ID, b.item_group_id as ITEM_GROUP_ID, b.item_category_id as ITEM_CATEGORY_ID, b.product_name_details as PRODUCT_NAME_DETAILS, b.unit_of_measure as UNIT_OF_MEASURE, b.current_stock as CURRENT_STOCK, b.color as COLOR, b.item_color as ITEM_COLOR, b.gsm as GSM, b.brand as BRAND, b.dia_width as DIA_WIDTH, b.item_size as ITEM_SIZE, b.avg_rate_per_unit as AVG_RATE_PER_UNIT, c.issue_number as SYSTEM_ID
		from inv_issue_master c, inv_transaction a, product_details_master b
		where c.id=a.mst_id and a.prod_id=b.id and c.issue_purpose=62 and a.mst_id=$mst_id $transaction_type_cond $company_cond $category_cond $reject_qnty_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
		order by b.id desc";
	}   

	$sql_res = sql_select($sql);
	$main_data_arr=array();
	foreach ($sql_res as $row) 
	{
		if (!array_key_exists($row["TRANS_ID"], $previous_scrap_received_arr))
		{
			$main_data_arr[$row["TRANS_ID"]]['TRANS_ID']=$row["TRANS_ID"];
			$main_data_arr[$row["TRANS_ID"]]['PROD_ID']=$row["PROD_ID"];
			$main_data_arr[$row["TRANS_ID"]]['PRODUCT_NAME_DETAILS']=$row["PRODUCT_NAME_DETAILS"];
			$main_data_arr[$row["TRANS_ID"]]['CONS_QNTY']=$row["CONS_QNTY"];	
		}
	}
	$i=1;
	?>

    	<table class="rpt_table" border="1" cellpadding="2" cellspacing="0" rules="all" width="290">
        	<thead>
                <tr>
                    <th width="20">SL</th>
                    <th width="200" title="Product Description">Prod Desc</th>
                    <th title="Balance Quantity">Bal Qnty</th>
                </tr>
            </thead>
            <tbody>
            	<? 
            	foreach ($main_data_arr as $trans_id => $row)
            	{
					if ($i%2==0)$bgcolor="#E9F3FF";
					else $bgcolor="#FFFFFF";
					?>
                	<tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $row["TRANS_ID"]."**".$row["PROD_ID"]."**".$receive_basis."**".$ref_no; ?>","populate_data_from_product_list_view","requires/scrap_material_receive_controller")' style="cursor:pointer" >
                		<td align="center"><? echo $i; ?></td>
                    	<td><p><? echo $row["PRODUCT_NAME_DETAILS"]; ?></p></td>
                        <td align="right"><? echo $row["CONS_QNTY"]; ?></td>
                    </tr>
                	<? 
                	$i++; 
                }
                ?>
            </tbody>
        </table>
     </fieldset>
	<?
	exit();
}

if($action=="show_product_listview_aftersave")
{
	$ex_data = explode("**",$data);
	$company_id = $ex_data[0];
	$cbo_category_id = $ex_data[1];
	$scrap_mst_id = $ex_data[2];
	$receive_basis=$ex_data[3];
	$ref_no=$ex_data[4];

	$company_cond= $category_cond="";
	if ($company_id!=0) $company_cond=" and a.company_id=$company_id";
	if ($cbo_category_id!=0) $category_cond=" and a.item_category=$cbo_category_id";

	$sql_trans=sql_select("select a.MST_ID, a.ID from inv_transaction a, inv_scrap_receive_dtls b where a.id=b.trans_id and b.mst_id=$scrap_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach ($sql_trans as $row) {
		$mst_id.=$row["MST_ID"].',';
	}
	$mst_id=rtrim($mst_id,',');

	$previous_scrap_received_arr=return_library_array("select trans_id, id from inv_scrap_receive_dtls where status_active=1 and is_deleted=0 and trans_id is not null",'trans_id','id'); 
    
 
	if($receive_basis == 1) //scrap receive basis -> Receive-Reject
	{
		$transaction_type_cond = "  and a.transaction_type in(1,4,5)";	
		$reject_qnty_cond = " and a.cons_reject_qnty > 0";
		$sql= "SELECT a.id as TRANS_ID, a.mst_id as MST_ID, a.store_id as STORE_ID, a.transaction_date as TRANSACTION_DATE, a.cons_reject_qnty as CONS_QNTY, a.transaction_type as TRANSACTION_TYPE, a.body_part_id as BODY_PART_ID, a.pi_wo_batch_no as PI_WO_BATCH_NO, b.id as PROD_ID, b.item_group_id as ITEM_GROUP_ID, b.item_category_id as ITEM_CATEGORY_ID, b.product_name_details as PRODUCT_NAME_DETAILS, b.unit_of_measure as UNIT_OF_MEASURE, b.current_stock as CURRENT_STOCK, b.color as COLOR, b.item_color as ITEM_COLOR, b.gsm as GSM, b.brand as BRAND, b.dia_width as DIA_WIDTH, b.item_size as ITEM_SIZE, b.avg_rate_per_unit as AVG_RATE_PER_UNIT, c.recv_number as SYSTEM_ID
		from inv_receive_master c, inv_transaction a, product_details_master b
		where c.id=a.mst_id and a.prod_id=b.id and a.mst_id in($mst_id) $transaction_type_cond $company_cond $category_cond $reject_qnty_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.id desc";  //b.LOT,
	} 
	elseif ($receive_basis == 2) //scrap receive basis -> Issue Damage
	{
		$transaction_type_cond = "  and a.transaction_type in(2,3,6) ";
		$reject_qnty_cond = " and a.cons_quantity > 0";

		$sql= "SELECT a.id as TRANS_ID, a.mst_id as MST_ID, a.store_id as STORE_ID, a.transaction_date as TRANSACTION_DATE, a.cons_quantity as CONS_QNTY, a.transaction_type as TRANSACTION_TYPE, a.body_part_id as BODY_PART_ID, a.pi_wo_batch_no as PI_WO_BATCH_NO, b.id as PROD_ID, b.item_group_id as ITEM_GROUP_ID, b.item_category_id as ITEM_CATEGORY_ID, b.product_name_details as PRODUCT_NAME_DETAILS, b.unit_of_measure as UNIT_OF_MEASURE, b.current_stock as CURRENT_STOCK, b.color as COLOR, b.item_color as ITEM_COLOR, b.gsm as GSM, b.brand as BRAND, b.dia_width as DIA_WIDTH, b.item_size as ITEM_SIZE, b.avg_rate_per_unit as AVG_RATE_PER_UNIT, c.issue_number as SYSTEM_ID
		from inv_issue_master c, inv_transaction a, product_details_master b
		where c.id=a.mst_id and a.prod_id=b.id and c.issue_purpose=26 and a.mst_id in($mst_id) $transaction_type_cond $company_cond $category_cond $reject_qnty_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by b.id desc";
	} 
	elseif ($receive_basis == 3) //scrap receive basis -> Issue Scrap store
	{
		$transaction_type_cond = "  and a.transaction_type in(2,3,6)";
		$reject_qnty_cond = " and a.cons_quantity > 0";

		$sql= "SELECT a.id as TRANS_ID, a.mst_id as MST_ID, a.store_id as STORE_ID, a.transaction_date as TRANSACTION_DATE, a.cons_quantity as CONS_QNTY, a.transaction_type as TRANSACTION_TYPE, a.body_part_id as BODY_PART_ID, a.pi_wo_batch_no as PI_WO_BATCH_NO, b.id as PROD_ID, b.item_group_id as ITEM_GROUP_ID, b.item_category_id as ITEM_CATEGORY_ID, b.product_name_details as PRODUCT_NAME_DETAILS, b.unit_of_measure as UNIT_OF_MEASURE, b.current_stock as CURRENT_STOCK, b.color as COLOR, b.item_color as ITEM_COLOR, b.gsm as GSM, b.brand as BRAND, b.dia_width as DIA_WIDTH, b.item_size as ITEM_SIZE, b.avg_rate_per_unit as AVG_RATE_PER_UNIT, c.issue_number as SYSTEM_ID
		from inv_issue_master c, inv_transaction a, product_details_master b
		where c.id=a.mst_id and a.prod_id=b.id and c.issue_purpose=31 and a.mst_id in($mst_id) $transaction_type_cond $company_cond $category_cond $reject_qnty_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by b.id desc";
	}   

	$sql_res = sql_select($sql);
	$main_data_arr=array();
	foreach ($sql_res as $row) 
	{
		if (!array_key_exists($row["TRANS_ID"], $previous_scrap_received_arr))
		{
			$main_data_arr[$row["TRANS_ID"]]['TRANS_ID']=$row["TRANS_ID"];
			$main_data_arr[$row["TRANS_ID"]]['PROD_ID']=$row["PROD_ID"];
			$main_data_arr[$row["TRANS_ID"]]['PRODUCT_NAME_DETAILS']=$row["PRODUCT_NAME_DETAILS"];
			$main_data_arr[$row["TRANS_ID"]]['CONS_QNTY']=$row["CONS_QNTY"];	
		}
	}
	$i=1;
	?>

    	<table class="rpt_table" border="1" cellpadding="2" cellspacing="0" rules="all" width="290">
        	<thead>
                <tr>
                    <th width="20">SL</th>
                    <th width="200" title="Product Description">Prod Desc</th>
                    <th title="Balance Quantity">Bal Qnty</th>
                </tr>
            </thead>
            <tbody>
            	<? 
            	foreach ($main_data_arr as $trans_id => $row)
            	{
					if ($i%2==0)$bgcolor="#E9F3FF";
					else $bgcolor="#FFFFFF";
					?>
                	<tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $row["TRANS_ID"]."**".$row["PROD_ID"]."**".$receive_basis."**".$ref_no; ?>","populate_data_from_product_list_view","requires/scrap_material_receive_controller")' style="cursor:pointer" >
                		<td align="center"><? echo $i; ?></td>
                    	<td><p><? echo $row["PRODUCT_NAME_DETAILS"]; ?></p></td>
                        <td align="right"><? echo $row["CONS_QNTY"]; ?></td>
                    </tr>
                	<? 
                	$i++; 
                }
                ?>
            </tbody>
        </table>
     </fieldset>
	<?
	exit();
}

if($action=="populate_data_from_product_list_view")
{
	$ex_data=explode('**',$data);
	// echo "<pre>";print_r($ex_data);
	$trans_id=$ex_data[0];
	$prod_id=$ex_data[1];
	$receive_basis=$ex_data[2];
	$ref_no = $ex_data[3];
	$item_group_arr=return_library_array("select id, item_name from lib_item_group where status_active=1",'id','item_name');
	$body_part_arr=return_library_array("select id, body_part_full_name from lib_body_part where status_active=1",'id','body_part_full_name');
	$store_arr=return_library_array("select id, store_name from lib_store_location where status_active=1",'id','store_name');
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer where status_active=1",'id','buyer_name');

	if ($item_category_id==2) $batch_arr = return_library_array( "select id, batch_no from pro_batch_create_mst where status_active=1",'id','batch_no');
	$color_arr=return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');

	if ($item_category_id==5 || $item_category_id==6 || $item_category_id==7 || $item_category_id==23)
	{
		$selet_lot_cond="a.batch_lot";
	} else $selet_lot_cond="b.lot";
	
	if ($receive_basis==1) //scrap receive basis -> Receive-Reject
	{
		$transaction_type_cond = "  and a.transaction_type in(1,4,5)";	
		$reject_qnty_cond = " and a.cons_reject_qnty > 0";
		$sql= "SELECT a.id as TRANS_ID, a.mst_id as MST_ID, a.store_id as STORE_ID, a.transaction_date as TRANSACTION_DATE, a.cons_reject_qnty as CONS_QNTY, a.transaction_type as TRANSACTION_TYPE, a.body_part_id as BODY_PART_ID, a.pi_wo_batch_no as PI_WO_BATCH_NO, b.id as PROD_ID, b.item_group_id as ITEM_GROUP_ID, b.item_category_id as ITEM_CATEGORY_ID, b.product_name_details as PRODUCT_NAME_DETAILS, b.unit_of_measure as UNIT_OF_MEASURE, b.current_stock as CURRENT_STOCK, b.color as COLOR, b.item_color as ITEM_COLOR, b.gsm as GSM, b.brand as BRAND, b.dia_width as DIA_WIDTH, b.item_size as ITEM_SIZE, b.avg_rate_per_unit as AVG_RATE_PER_UNIT, c.recv_number as SYSTEM_ID, $selet_lot_cond as LOT, c.buyer_id as BUYER_ID
		from inv_receive_master c, inv_transaction a, product_details_master b
		where c.id=a.mst_id and a.prod_id=b.id and a.id=$trans_id $transaction_type_cond $company $category_cond $reject_qnty_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.id desc";
	}
	else if ($receive_basis==2) //scrap receive basis -> Issue Damage  
	{
		$transaction_type_cond = "  and a.transaction_type in(2,3,6) ";
		$reject_qnty_cond = " and a.cons_quantity > 0";
		$sql= "SELECT a.id as TRANS_ID, a.mst_id as MST_ID, a.store_id as STORE_ID, a.transaction_date as TRANSACTION_DATE, a.cons_quantity as CONS_QNTY, a.transaction_type as TRANSACTION_TYPE, a.body_part_id as BODY_PART_ID, a.pi_wo_batch_no as PI_WO_BATCH_NO, b.id as PROD_ID, b.item_group_id as ITEM_GROUP_ID, b.item_category_id as ITEM_CATEGORY_ID, b.product_name_details as PRODUCT_NAME_DETAILS, b.unit_of_measure as UNIT_OF_MEASURE, b.current_stock as CURRENT_STOCK, b.color as COLOR, b.item_color as ITEM_COLOR, b.gsm as GSM, b.brand as BRAND, b.dia_width as DIA_WIDTH, b.item_size as ITEM_SIZE, a.cons_rate as AVG_RATE_PER_UNIT, C.ISSUE_NUMBER as SYSTEM_ID, $selet_lot_cond as LOT, c.buyer_id as BUYER_ID
		from inv_issue_master c, inv_transaction a, product_details_master b
		where c.id=a.mst_id and a.prod_id=b.id  and a.id=$trans_id $transaction_type_cond $company $category_cond $reject_qnty_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.id desc";
	}
	if ($receive_basis==3) //scrap receive basis -> Issue Scrap store
	{
		$transaction_type_cond = "  and a.transaction_type in(2,3,6)";
		$reject_qnty_cond = " and a.cons_quantity > 0";
		$sql= "SELECT a.id as TRANS_ID, a.mst_id as MST_ID, a.store_id as STORE_ID, a.transaction_date as TRANSACTION_DATE, a.cons_quantity as CONS_QNTY, a.transaction_type as TRANSACTION_TYPE, a.body_part_id as BODY_PART_ID, a.pi_wo_batch_no as PI_WO_BATCH_NO, b.id as PROD_ID, b.item_group_id as ITEM_GROUP_ID, b.item_category_id as ITEM_CATEGORY_ID, b.product_name_details as PRODUCT_NAME_DETAILS, b.unit_of_measure as UNIT_OF_MEASURE, b.current_stock as CURRENT_STOCK, b.color as COLOR, b.item_color as ITEM_COLOR, b.gsm as GSM, b.brand as BRAND, b.dia_width as DIA_WIDTH, b.item_size as ITEM_SIZE, b.avg_rate_per_unit as AVG_RATE_PER_UNIT, c.issue_number as SYSTEM_ID, $selet_lot_cond as LOT, c.buyer_id as BUYER_ID
		from inv_issue_master c, inv_transaction a, product_details_master b
		where c.id=a.mst_id and a.prod_id=b.id and c.issue_purpose=62 and a.id=$trans_id $transaction_type_cond $company $category_cond $reject_qnty_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.id desc";
	}
	
	// echo $sql;
	$nameArray=sql_select( $sql );
	foreach ($nameArray as $row)
	{
		$txt_exchange_rate=1;
		$avg_rate_per_unit=number_format($row["AVG_RATE_PER_UNIT"],2,'.','');
        $amount = number_format($row["CONS_QNTY"]*$avg_rate_per_unit,'','',1);
        $bookCurrency = number_format($amount*$txt_exchange_rate,'','',1);

        if ($row["ITEM_CATEGORY_ID"] == 2) {
			$lot_batch = $batch_arr[$row["PI_WO_BATCH_NO"]];
		} else {
			$lot_batch = $row["LOT"];
		}

        echo "$('#txt_amount').val($amount);\n";
        echo "$('#txt_book_currency').val($bookCurrency);\n";

		echo "document.getElementById('txt_item_desc').value	 = '".$row["PRODUCT_NAME_DETAILS"]."';\n";
		echo "document.getElementById('hidden_pord_id').value	 = '".$row["PROD_ID"]."';\n";
		echo "document.getElementById('txt_trans_id').value	 = '".$row["TRANS_ID"]."';\n";
		echo "document.getElementById('cbo_item_group').value	 = '".$item_group_arr[$row["ITEM_GROUP_ID"]]."';\n";
		echo "document.getElementById('hidd_item_group_id').value	 = '".$row["ITEM_GROUP_ID"]."';\n";
		echo "document.getElementById('body_part').value		 = '".$body_part_arr[$row["BODY_PART_ID"]]."';\n";
		echo "document.getElementById('hidd_body_part_id').value = '".$row["BODY_PART_ID"]."';\n";
		echo "document.getElementById('txt_gsm').value		     = '".$row["GSM"]."';\n";
		echo "document.getElementById('dia_width').value		 = '".$row["DIA_WIDTH"]."';\n";
		echo "document.getElementById('txt_lot').value	         = '".$lot_batch."';\n";
		echo "document.getElementById('txt_remarks').value		 = '".$row["REMARKS"]."';\n";
		echo "document.getElementById('txt_receive_qty').value	 = '".$row["CONS_QNTY"]."';\n";
		echo "document.getElementById('hdn_receive_qty').value	 = '".$row["CONS_QNTY"]."';\n";
		echo "document.getElementById('txt_rate').value	         = '".$avg_rate_per_unit."';\n";
		echo "document.getElementById('txt_amount').value		 = '".$row["AMOUNT"]."';\n";
		echo "document.getElementById('cbo_uom').value	         = '".$row["UNIT_OF_MEASURE"]."';\n";
		echo "document.getElementById('txt_book_currency').value = '".$row["BOOK_CURRENCY"]."';\n";
		//echo "document.getElementById('dia_width_type').value	 = '".$row["dia_type"]."';\n";
		echo "document.getElementById('txt_color').value		 = '".$color_arr[$row["COLOR"]]."';\n";
		echo "document.getElementById('txt_color_id').value		 = '".$row["COLOR"]."';\n";
		echo "document.getElementById('txt_no_of_bags').value	 = '".$row["NO_OF_BAGS"]."';\n";
		//echo "document.getElementById('update_id').value		 = '".$row["ID"]."';\n";
		//echo "document.getElementById('update_dtls_id').value	 = '".$row["DTLS_ID"]."';\n";
		//echo "document.getElementById('cbo_from_store').value = '".$row["FROM_STORE_ID"]."';\n";
		echo "document.getElementById('cbo_from_store').value = '".$row["STORE_ID"]."';\n"; 
		echo "document.getElementById('txt_item_size').value = '".$row["ITEM_SIZE"]."';\n";
		echo "document.getElementById('txt_count').value = '".$row["COUNT"]."';\n";
		// echo "document.getElementById('txt_trans_ref').value = '".$row["TRANS_REF"]."';\n";
		echo "document.getElementById('cbo_material_placement').value = '".$row["MATERIAL_PLACEMENT"]."';\n";
		echo "document.getElementById('cbo_buyer').value = '".$row["BUYER_ID"]."';\n";
		echo "document.getElementById('cbo_supplier').value = '".$row["SUPPLIER_ID"]."';\n";
		echo "document.getElementById('txt_trans_ref').value = '".$row["SYSTEM_ID"]."';\n";
		echo "document.getElementById('txt_transaction_byer').value = '".$buyer_arr[$row["BUYER_ID"]]."';\n";
		echo "document.getElementById('txt_int_ref').value = '".$ref_no."';\n";

		echo "$('#body_part').prop('disabled',true);\n";
		echo "$('#txt_gsm').prop('disabled',true);\n";
		echo "$('#dia_width').prop('disabled',true);\n";
		//echo "$('#dia_width_type').prop('disabled',true);\n";
		//echo "$('#txt_item_desc').prop('disabled',true);\n";
		echo "$('#txt_receive_qty').prop('disabled',true);\n";
		echo "$('#cbo_location').prop('disabled',true);\n";
		echo "$('#cbo_store').prop('disabled',true);\n";
		echo "$('#cbo_from_store').prop('disabled',true);\n";
		echo "$('#txt_receive_date').prop('disabled',true);\n";
		echo "$('#cbo_item_group').prop('disabled',true);\n";
		echo "$('#txt_color').prop('disabled',true);\n";

		echo "set_button_status(0,'".$_SESSION['page_permission']."', 'fnc_scrap_material_receive_entry',1,1);\n";
	}
	exit();
}

// Receive Basis Independent Search Panel Start
if ($action=="item_description_independent_popup")
{
	echo load_html_head_contents("Item Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
    ?>
	<script type="text/javascript">
		function js_set_value(id)
		{
			//alert (id);return;
			$('#hidden_prod_description').val(id);
			parent.emailwindow.hide();
		}
    
    	$(function(){
    		var tableFilters = { }
	    	setFilterGrid("html_search",-1,tableFilters);
    	});
    </script>
    </head>
    <body>
		<form action="" name="item_descrption_form " id="item_descrption_form">
			<input type="hidden" name="hidden_prod_description" id="hidden_prod_description" />
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table" >
				<thead>
					<tr>
						<th colspan="6">Item Description Pop Up</th>
						<th>&nbsp</th>
					</tr>                    
					<tr>
						<th>Company</th>
						<th>Item Category</th>
						<th>Item Group</th>
						<th>Item Description</th>
						<th>Product Id</th>
						<th>Item Size</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
						</th>
					</tr>                   
				</thead>
				<tbody>
					<tr>
						<td>
						    <? 
								echo create_drop_down( "cbo_company_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--- Select Company ---", $company_id,"",1);
                            ?> 
						</td>
						<td>
						   <? 
								$item_cate_array = return_library_array("select CATEGORY_ID,SHORT_NAME from lib_item_category_list where status_active=1 and is_deleted=0 $category_credential_cond  order by SHORT_NAME", "CATEGORY_ID", "SHORT_NAME");
								echo create_drop_down( "cbo_category_id", 170, $item_cate_array,"",1, "-- Select Item --", $cbo_category_id,"",1);
                            ?>
						</td>
						<td>
						   <? 
								echo create_drop_down( "cbo_item_group", 160, "select id,item_name,item_category from lib_item_group where item_category=$cbo_category_id and status_active=1 and is_deleted=0 order by item_name", "id,item_name,item_category", 1, "-- Select --", $selected);
                            ?>
						</td>
						<td>
						<input name="txt_item_desc" id="txt_item_desc" class="text_boxes" type="text" style="width:100px;"/>
						</td>
						<td>
						   <input name="txt_item_size" id="txt_item_size" class="text_boxes" type="text" style="width:90px;" />    
						</td>
						<td>
						   <input name="txt_prod_id" id="txt_prod_id" class="text_boxes" type="text" style="width:90px;" /> 
						</td>

						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view (<? echo $company_id; ?>+'_'+<? echo $cbo_category_id; ?>+'_'+<? echo $cbo_from_store_name; ?>+'_'+<? echo $cbo_receive_basis; ?>+'_'+document.getElementById('cbo_item_group').value+'_'+document.getElementById('txt_item_desc').value+'_'+document.getElementById('txt_item_size').value+'_'+document.getElementById('txt_prod_id').value, 'create_scrap_receive_independent_search_list_view', 'search_div', 'scrap_material_receive_controller', 'setFilterGrid(\'list_view\',0)');" style="width:80px; margin: 0 auto;">
						</td>
					</tr>
				</tbody>
			</table>
			<br>
			<div valign="top" id="search_div" align="left"></div>
		</form>
		<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<?
	exit();
}

if($action=="create_scrap_receive_independent_search_list_view")
{
	$ex_data = explode("_",$data);
	// var_dump($ex_data);
	// echo '<pre>';print_r($data);
	$company = $ex_data[0];
	$cbo_item_cat = $ex_data[1];
	$cbo_item_group = $ex_data[4];
	$txt_item_description = trim($ex_data[5]);
	$txt_prod_id = trim($ex_data[6]);
	$txt_item_size = trim($ex_data[7]);

	$sql_cond = '';
	if(trim($txt_item_description) != '') $sql_cond= " and product_name_details LIKE '%$txt_item_description%'";
	if(trim($cbo_item_group) !=0) $sql_cond= " and ITEM_GROUP_ID='$cbo_item_group'";
	if(trim($txt_prod_id) != '') $sql_cond= " and id LIKE '%$txt_prod_id%'"; 
	if(trim($txt_item_size) != '') $sql_cond= " and item_size LIKE '%$txt_item_size%'";
	
 	$sql = "SELECT id as ID, item_category_id as ITEM_CATEGORY_ID, item_group_id as ITEM_GROUP_ID, sub_group_name as SUB_GROUP_NAME, item_description as PRODUCT_NAME_DETAILS, item_code as ITEM_CODE, item_number as ITEM_NUMBER, item_size as ITEM_SIZE, unit_of_measure as UNIT_OF_MEASURE, current_stock as CURRENT_STOCK, color as COLOR, item_color as ITEM_COLOR, gsm as GSM, brand as BRAND, dia_width as DIA_WIDTH, avg_rate_per_unit as AVG_RATE_PER_UNIT from product_details_master where company_id=$company and item_category_id=$cbo_item_cat and item_description is not null $sql_cond and status_active=1 and is_deleted=0";

	$description_resutl = sql_select($sql);

	//print_r($iss_ids);die;
	$batch_ids =  implode(",", $batch_ids_array);
	$item_category=return_library_array("select category_id, actual_category_name from lib_item_category_list",'category_id','actual_category_name');
	$item_group_arr=return_library_array("select id, item_name from lib_item_group",'id','item_name');
	$color_arr     = return_library_array("select id, color_name from lib_color where status_active=1","id","color_name");
	//$receive_ids_arr = return_library_array( "select id, recv_number from inv_receive_master where id in($rcv_ids)",'id','recv_number');
	//$issue_ids_arr = return_library_array( "select id, issue_number from inv_issue_master where id in($iss_ids)",'id','issue_number');
	//$batch_arr = return_library_array( "select id, batch_no from PRO_BATCH_CREATE_MST where id in($batch_ids)",'id','BATCH_NO');
	//$issue_purpose_arr = return_library_array( "select id, issue_purpose from inv_issue_master where id in($iss_ids)",'id','issue_purpose');
	$sys_challan_no_array = $receive_ids_arr;
	foreach ($issue_ids_arr as $key => $value) {
		array_push($sys_challan_no_array, $key, $value);
	}
	//print_r($sys_challan_no_array);//die;
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" id="html_search">
		<thead>
			<tr>
				<th width="40" align="center">SL</th>
				<th width="80" align="center">Product Id</th>
				<th width="100" align="center">Category</th>
				<th width="100" align="center">Group</th>
				<th width="100" align="center">Sub Group</th>
				<th width="150" align="center">Description</th>
				<th width="80" align="center">Item Size</th>
			</tr>
		</thead>
		<tbody id="list_view">
		    <?			
			$i=1;
			foreach ($description_resutl as $value) 
			{
				?>
				<tr bgcolor="<?= $bgcolor; ?>" style="cursor:pointer;" onClick='js_set_value("<? echo $value["PRODUCT_NAME_DETAILS"].'__'.$lot_batch.'__'.$value["UNIT_OF_MEASURE"].'__'.$color_arr[$value["COLOR"]].'__'.$item_group_arr[$value["ITEM_GROUP_ID"]].'__'.$value["ID"].'__'.$value["COLOR"].'__'.$value["ITEM_GROUP_ID"].'__'.$value["CONS_QNTY"].'__'.$value["TRANS_ID"].'__'.$sys_challan_no_array[$value["MST_ID"]].'__'.$value["MST_ID"].'__'.$value["GSM"].'__'.$value["DIA_WIDTH"].'__'.$body_part_val.'__'.$value["STORE_ID"].'__'.$avg_rate_per_unit.'__'.$value["ISSUE_NUMBER"].'__'.$value["SUB_GROUP_NAME"].'__'.$value[ITEM_SIZE];?>")'>
					<td width="40" align="center"><?= $i;?></td>
					<td width="80" align="center"><p><?= $value["ID"]; ?></p></td>
					<td width="100" align="center"><p><?= $item_category[$value["ITEM_CATEGORY_ID"]]; ?></p></td>
					<td width="100" align="center"><?= $item_group_arr[$value["ITEM_GROUP_ID"]]; ?></td>
					<td width="100" align="center"><?= $value["SUB_GROUP_NAME"] ?></td>
					<td width="150" align="center"><p><?= $value["PRODUCT_NAME_DETAILS"]; ?></p></td>
					<td width="80" align="center" style="word-wrap: break-all;"><p><?= $value["ITEM_SIZE"]; ?></p></td>
				</tr>
				<?
				$i++;
			}
		    ?>
		</tbody>
	</table>
	<?
	exit();
}
// Receive Basis Independent Search Panel End

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	// echo "<pre>";
	// print_r($process);die;
	//($update_id == "") ? $update_id = $txt_system_id : $update_id = $update_id;
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)";
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later

			$new_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'SRE', date("Y",time()), 5, "select receive_no_prefix, receive_no_prefix_num from inv_scrap_receive_mst where company_id=$cbo_company_name and status_active=1 and is_deleted=0 and $year_cond=".date('Y',time())." order by id desc ", "receive_no_prefix", "receive_no_prefix_num"));

			$id=return_next_id( "id", "inv_scrap_receive_mst", 1);
			$field_array="id, receive_no_prefix,receive_no_prefix_num,sys_receive_no,company_id,item_category_id,location,store_id,from_store_id,entry_form,receive_date,receive_basis,currency,exchange_rate,challan_no,mst_id,remarks,from_floor,for_division,for_department,for_section,inserted_by,insert_date,status_active,is_deleted";
			// *cbo_folor_name*cbo_division_name*cbo_department_name*cbo_section_name

			$data_array="(".$id.",'".$new_system_id[1]."',".$new_system_id[2].",'".$new_system_id[0]."',".$cbo_company_name.",".$cbo_category_id.",".$cbo_location.",".$cbo_store.",".$cbo_from_store.",0,".$txt_receive_date.",".$cbo_receive_basis.",".$cbo_currency.",".$txt_exchange_rate.",".$txt_system_challan_no.",".$txt_mst_id.",".$txt_remarks_mst.",".$cbo_folor_name.",".$cbo_division_name.",".$cbo_department_name.",".$cbo_section_name.",".$user_id.",'".$pc_date_time."',1,0)";
			//echo $data_array;die;cbo_purpose

			// echo "10**insert into inv_scrap_receive_mst (".$field_array.") values ".$data_array;die;
			/*$rID=sql_insert("inv_issue_master",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0; */

			$sys_challan_no=$new_system_id[0];
			$row_id=$id;
		}
		else
		{
			$field_array_update="item_category_id*receive_basis*currency*exchange_rate*remarks*updated_by*update_date";
			$data_array_update=$cbo_category_id."*".$cbo_receive_basis."*".$cbo_currency."*".$txt_exchange_rate.'*'.$txt_remarks_mst."*".$user_id."*'".$pc_date_time."'";

			/*$rID=sql_update("inv_issue_master",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0; */

			$sys_challan_no=str_replace("'","",$txt_system_no);
			$row_id=str_replace("'","",$update_id);
		}

		
		$id_dtls=return_next_id( "id", "inv_scrap_receive_dtls", 1);

		$field_array_dtls="id, mst_id, item_group_id, product_id, trans_id, receive_qnty, rate, amount, uom, color, remarks, body_part,body_part_id, lot, gsm, dia, book_currency, no_of_bags, from_store_id, trans_ref, material_placement, buyer_id, supplier_id, item_size, count, inserted_by, insert_date, status_active, is_deleted";

		$data_array_dtls="(".$id_dtls.",".$row_id.",".$hidd_item_group_id.",".$hidden_pord_id.",".$txt_trans_id.",".$txt_receive_qty.",".$txt_rate.",".$txt_amount.",".$cbo_uom.",".$txt_color_id.",".$txt_remarks.",".$body_part.",".$hidd_body_part_id.",".$txt_lot.",".$txt_gsm.",".$dia_width.",".$txt_book_currency.",".$txt_no_of_bags.",".$hidden_store_from_id.",".$txt_trans_ref.",".$cbo_material_placement.",".$cbo_buyer.",".$cbo_supplier.",".$txt_item_size.",".$txt_count.",".$user_id.",'".$pc_date_time."',1,0)";

		// echo "10**insert into inv_scrap_receive_dtls (".$field_array_dtls.") values ".$data_array_dtls; die;
		//echo $rID = sql_insertss("inv_scrap_receive_mst",$field_array,$data_array,0);die;
		if(str_replace("'","",$update_id)=="")
		{
			$rID=sql_insert("inv_scrap_receive_mst",$field_array,$data_array,0);			
		}
		else
		{
			//echo $a = sql_updatess("inv_scrap_receive_mst",$field_array_update,$data_array_update,"id",$row_id,1); die;
			$rID=sql_update("inv_scrap_receive_mst",$field_array_update,$data_array_update,"id",$row_id,1);
			
		}
		//echo $a = sql_insertss("inv_scrap_receive_mst",$field_array,$data_array,0); die;
		$rID2=sql_insert("inv_scrap_receive_dtls",$field_array_dtls,$data_array_dtls,0);
		//echo "10**".$rID."**".$rID2;die;
		if($rID) $flag=1; else $flag=0;
		if($flag==1)
		{
			if($rID2) $flag=1; else $flag=0;
		}
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "0**".$sys_challan_no."**".$row_id."**0";
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "5**0**"."&nbsp;"."**0";
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".$sys_challan_no."**".$row_id."**0";
			}
			else
			{
				oci_rollback($con);
				echo "5**".$rID."**".$rID2;
			}
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
		//echo "select id from inv_scrap_receive_dtls where id = $update_dtls_id and product_id='$hidden_pord_id' and trans_id='$txt_trans_id'  and status_active=1 and is_deleted=0 ";die;
		//$update_dtls_id=return_field_value("a.id as id"," inv_scrap_receive_dtls a","a.mst_id=$txt_system_id and a.product_id=$hidden_pord_id and a.trans_id=$txt_trans_id  and a.status_active=1 and a.is_deleted=0 ","id");
		//echo $update_dtls_id;die;

		$field_array_update_dtls="rate*item_size*material_placement*buyer_id*supplier_id*amount*book_currency*no_of_bags*remarks*trans_ref*updated_by*update_date";
		$data_array_update_dtls=$txt_rate."*".$txt_item_size."*".$cbo_material_placement."*".$cbo_buyer."*".$cbo_supplier."*".$txt_amount."*".$txt_book_currency."*".$txt_no_of_bags."*".$txt_remarks."*".$txt_trans_ref."*".$user_id."*'".$pc_date_time."'";

		$sys_challan_no=str_replace("'","",$txt_system_no);
		$row_id=str_replace("'","",$update_id);
		$update_dtls_id=str_replace("'","",$update_dtls_id);


		// echo "10**". sql_update("inv_scrap_receive_dtls", $field_array_update_dtls, $data_array_update_dtls, "id", $update_dtls_id, 0, 1);die;
		$rID1=sql_update("inv_scrap_receive_dtls",$field_array_update_dtls,$data_array_update_dtls,"id",$update_dtls_id,0);
		// echo "10**".$rID1;die;
		if($rID1) $flag=1; else $flag=0;

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "1**".$sys_challan_no."**".$row_id."**".$update_dtls_id;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "6**0**0**1";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".$sys_challan_no."**".$row_id."**".$update_dtls_id;
			}
			else
			{
				oci_rollback($con);
				echo "6**0**0**1";
			}
		}
		disconnect($con);
		die;
	}
	else if($operation ==2)
	{
		$con = connect(); 
		if($db_type==0)	{ mysql_query("BEGIN"); }
		// master table delete here---------------------------------------
		$mst_id = str_replace("'","",$update_id);
		$dtls_ids_arr = return_library_array( "select id, mst_id from inv_scrap_receive_dtls where mst_id in($mst_id)",'id','mst_id');
		//print_r($dtls_ids_arr);die(" with teddy bear");
		if($mst_id=="" || $mst_id==0)
		{ 
			echo "16**Delete not allowed. Problem occurred"; die;
		}
		else 
		{
			$update_id = str_replace("'","",$update_id);
			//$product_id = str_replace("'","",$current_prod_id);
			if( str_replace("'","",$update_id) == "" )
			{
				echo "16**Delete not allowed. Problem occurred";disconnect($con); die;
			}

			$sys_challan_no=str_replace("'","",$txt_system_no);
			//$row_id=str_replace("'","",$update_id);
			$row_id=str_replace("'","",$txt_system_id);
				
			$field_array_master="updated_by*update_date";
			$data_array_master="".$user_id."*'".$pc_date_time."'";

			$field_array_trans="updated_by*update_date*status_active*is_deleted";
			$data_array_trans="".$user_id."*'".$pc_date_time."'*0*1";
			
			if(count($dtls_ids_arr) > 1){
				$rID=sql_update("inv_scrap_receive_mst",$field_array_master,$data_array_master,"id",$update_id,1);
				$rID2=sql_update("inv_scrap_receive_dtls",$field_array_trans,$data_array_trans,"id",$update_dtls_id,1);
			}else{
				$rID=sql_update("inv_scrap_receive_mst",$field_array_trans,$data_array_trans,"id",$update_id,1);
				$rID2=sql_update("inv_scrap_receive_dtls",$field_array_trans,$data_array_trans,"mst_id",$update_id,1);
			}
			
		}

		if($db_type==0)
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");  
				echo "2**".$sys_challan_no."**".$row_id."**".$update_dtls_id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$sys_challan_no."**".$row_id."**".$update_dtls_id;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			
			if($rID && $rID2)
			{
				oci_commit($con);   
				echo "2**".$sys_challan_no."**".$row_id."**".$update_dtls_id;
			}
			else
			{
				oci_rollback($con);  
				echo "10**".$sys_challan_no."**".$row_id."**".$update_dtls_id;
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="show_dtls_list_view")
{
	$data=explode('_',$data);
	$sys_id = $data[0];
	$company = $data[1];

	$supplier_arr  = return_library_array("select id, short_name from lib_supplier","id","short_name");
	$buyer_lib_arr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0", 'id', 'buyer_name');
	$item_group_arr=return_library_array("select id, item_name from lib_item_group",'id','item_name');
	$material_placement_arr = array(1=>'Top Floor', 2=>'Bulding Side', 3=>'Old Store', 4=>'Tin Shade');

	$sql_list_view = "SELECT a.id as ID, b.mst_id as MST_ID, sum(b.receive_qnty) as RECEIVE_QNTY, b.rate as RATE, sum(b.amount) as AMOUNT, b.remarks as REMARKS, b.body_part as BODY_PART, b.dia_type as DIA_TYPE, b.book_currency as BOOK_CURRENCY, b.no_of_bags as NO_OF_BAGS, b.trans_id as TRANS_ID, b.item_group_id as ITEM_GROUP_ID, c.id as PRODUCT_ID, c.product_name_details as PRODUCT_NAME_DETAILS, c.unit_of_measure as UNIT_OF_MEASURE, c.color as COLOR, b.lot as LOT, c.gsm as GSM, c.dia_width as DIA_WIDTH, b.trans_ref as TRANS_REF, b.material_placement as MATERIAL_PLACEMENT, b.buyer_id as BUYER_ID, b.supplier_id as SUPPLIER_ID,d.cons_rate
	from  inv_scrap_receive_mst a, inv_scrap_receive_dtls b, product_details_master c, inv_transaction d 
	where a.id=b.mst_id and b.product_id=c.id and b.trans_id=d.id and a.id='$sys_id' and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
	group by a.id, b.mst_id, b.rate, b.remarks, b.body_part, b.dia_type, b.book_currency, b.no_of_bags, b.trans_id, b.item_group_id, c.id, c.product_name_details, c.unit_of_measure, c.color, b.lot, c.gsm, c.dia_width, B.TRANS_REF, B.MATERIAL_PLACEMENT, B.BUYER_ID, B.SUPPLIER_ID,d.cons_rate";
	// echo $sql_list_view;
	$sqlResult =sql_select($sql_list_view);

	$prod_id_arr = [];
	foreach($sqlResult as $row){
		$prod_id_arr[$row['PRODUCT_ID']] = $row['PRODUCT_ID'];
	}
	// echo "<pre>"; print_r($prod_id_arr); die;

	// ============================== data store to gbl table ==================================
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from=1 and ENTRY_FORM=52");
	oci_commit($con);
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 52, 1, $prod_id_arr, $empty_arr);//PROD_ID
	
	$int_ref_sql = "SELECT b.grouping as ref, o.prod_id
	from wo_po_break_down b, order_wise_pro_details o, gbl_temp_engine tmp
	where b.id=o.po_breakdown_id and o.entry_form in (7,37) and o.trans_id <> 0 and tmp.entry_form=52 and tmp.ref_from=1 and tmp.user_id = $user_id and o.prod_id = tmp.ref_val";
	// echo $int_ref_sql;
	$int_ref_result = sql_select($int_ref_sql);
	$int_ref_arr = array();
	foreach ($int_ref_result as $row) 
	{
		$int_ref_arr[$row["PROD_ID"]] = $row["REF"];
	}
	// echo '<pre>';print_r($int_ref_arr);
	
	
	// =================================== delete data ========================================
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from=1 and ENTRY_FORM=52");
	oci_commit($con);
	disconnect($con);

	?>
	<div>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1300" class="rpt_table"  align="left">
            <thead>
                <th width="30">SL</th>
                <th width="50">Prod. ID</th>
                <th width="80">Group</th>
                <th width="150">Item Description</th>
                <th width="80">Lot</th>
                <th width="50">UOM</th>
                <th width="80">Receive Qty</th>
                <th width="70">Rate</th>
                <th width="70">Amount</th>
                <th width="100">Transaction Ref</th>
                <th width="80">Int. Ref</th>
                <th width="100">Metarial Placement</th>
                <th width="100">Buyer</th>
                <th width="100">Supplier</th>
                <th>Remarks</th>
            </thead>
		</table>
	<div style="width:max-height:180px;" id="scrap_list_view" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1300" class="rpt_table">
		<?
			$i=1;
			foreach($sqlResult as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
                <tr bgcolor="<?= $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data('<? echo $row['ID'].'__'.$row['PRODUCT_ID'].'__'.$int_ref_arr[$row['PRODUCT_ID']]; ?>','populate_scrap_dtls_form_data','requires/scrap_material_receive_controller');" >
                    <td width="30" align="center"><?= $i; ?></td>
                    <td width="50" align="center"><p><?= $row['PRODUCT_ID']; ?></p></td>
                    <td width="80" align="right"><p><?= $item_group_arr[$row['ITEM_GROUP_ID']]; ?></p></td>
                    <td width="150"><p><?= $row['PRODUCT_NAME_DETAILS']; ?></p></td>
                    <td width="80" align="center"><p><?= $row['LOT']; ?></p></td>
                    <td width="50" align="center"><p><?= $unit_of_measurement[$row['UNIT_OF_MEASURE']]; ?></p></td>
                    <td width="80" align="right"><p><?= $row['RECEIVE_QNTY']; ?></p></td>
                    <td width="70" align="right"><p><?= number_format($row['CONS_RATE'],2); ?></p></td>
                    <td width="70" align="right"><p><?= number_format($row['AMOUNT'],2); ?></p></td>
                    <td width="100" align="center"><p><?php echo $row['TRANS_REF']; ?></p></td>
                    <td width="80" align="center"><p><?php echo $int_ref_arr[$row['PRODUCT_ID']]; ?></p></td>
                    <td width="100"><p><?php echo $material_placement_arr[$row['MATERIAL_PLACEMENT']]; ?></p></td>
                    <td width="100"><p><?php echo $buyer_lib_arr[$row['BUYER_ID']]; ?></p></td>
                    <td width="100"><p><?php echo $supplier_arr[$row['SUPPLIER_ID']]; ?></p></td>
                    <td align="center"><p><?= $row['REMARKS']; ?></p></td>
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

if ($action=="populate_scrap_dtls_form_data")
{
	$data=explode('__',$data);
	// echo "<pre>"; print_r($data); die;
	$color_arr=return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$item_group_arr=return_library_array("select id, item_name from lib_item_group",'id','item_name');
	$sql_name_array = "SELECT a.id as ID, b.id as DTLS_ID, b.mst_id as MST_ID, b.receive_qnty as RECEIVE_QNTY, b.rate as RATE, b.amount as AMOUNT, b.from_store_id as FROM_STORE_ID, b.remarks as REMARKS, b.body_part as BODY_PART, b.dia_type as DIA_TYPE, b.book_currency as BOOK_CURRENCY, b.no_of_bags as NO_OF_BAGS, c.item_group_id as ITEM_GROUP_ID, c.id as PRODUCT_ID, c.product_name_details as PRODUCT_NAME_DETAILS, c.unit_of_measure as UNIT_OF_MEASURE, c.color as COLOR, b.lot as LOT, c.gsm as GSM, c.dia_width as DIA_WIDTH, b.count as COUNT, b.item_size as ITEM_SIZE, b.buyer_id as BUYER_ID, b.supplier_id as SUPPLIER_ID, b.material_placement as MATERIAL_PLACEMENT, b.trans_ref as TRANS_REF, a.receive_basis as RECEIVE_BASIS,d.cons_rate
	from inv_scrap_receive_mst a, inv_scrap_receive_dtls b, product_details_master c, inv_transaction d 
	where a.id=b.mst_id and b.product_id=c.id and b.trans_id=d.id and a.id='$data[0]' and b.product_id='$data[1]' and b.status_active=1 and b.status_active=1 and a.status_active=1 and c.status_active=1 and c.is_deleted=0 order by id desc";
	// echo $sql_name_array;
	$nameArray=sql_select( $sql_name_array);
	foreach ($nameArray as $row)
	{
		$rate = floatval($row["CONS_RATE"]);
		$txt_rate=number_format($rate,2,'.','');
		echo "document.getElementById('txt_item_desc').value	 = '".$row["PRODUCT_NAME_DETAILS"]."';\n";
		echo "document.getElementById('cbo_item_group').value	 = '".$item_group_arr[$row["ITEM_GROUP_ID"]]."';\n";
		echo "document.getElementById('hidd_item_group_id').value	 = '".$row["ITEM_GROUP_ID"]."';\n";
		echo "document.getElementById('body_part').value		 = '".$row["BODY_PART"]."';\n";
		echo "document.getElementById('hidd_body_part_id').value = '".$row["BODY_PART_ID"]."';\n";
		echo "document.getElementById('txt_gsm').value		     = '".$row["GSM"]."';\n";
		echo "document.getElementById('dia_width').value		 = '".$row["DIA_WIDTH"]."';\n";
		echo "document.getElementById('txt_lot').value	         = '".$row["LOT"]."';\n";
		echo "document.getElementById('txt_remarks').value		 = '".$row["REMARKS"]."';\n";
		echo "document.getElementById('txt_receive_qty').value	 = '".$row["RECEIVE_QNTY"]."';\n";
		echo "document.getElementById('hdn_receive_qty').value	 = '".$row["RECEIVE_QNTY"]."';\n";
		echo "document.getElementById('txt_rate').value	         = '".$txt_rate."';\n";
		echo "document.getElementById('txt_int_ref').value	         = '".$data[2]."';\n";
		echo "document.getElementById('txt_amount').value		 = '".$row["AMOUNT"]."';\n";
		echo "document.getElementById('cbo_uom').value	         = '".$row["UNIT_OF_MEASURE"]."';\n";
		echo "document.getElementById('txt_book_currency').value = '".$row["BOOK_CURRENCY"]."';\n";
		//echo "document.getElementById('dia_width_type').value	 = '".$row["dia_type"]."';\n";
		echo "document.getElementById('txt_color').value		 = '".$color_arr[$row["COLOR"]]."';\n";
		echo "document.getElementById('txt_color_id').value		 = '".$row["COLOR"]."';\n";
		echo "document.getElementById('txt_no_of_bags').value	 = '".$row["NO_OF_BAGS"]."';\n";
		echo "document.getElementById('update_id').value		 = '".$row["ID"]."';\n";
		echo "document.getElementById('update_dtls_id').value	 = '".$row["DTLS_ID"]."';\n";
		echo "document.getElementById('hidden_store_from_id').value = '".$row["FROM_STORE_ID"]."';\n";
		echo "document.getElementById('txt_item_size').value = '".$row["ITEM_SIZE"]."';\n";
		echo "document.getElementById('txt_count').value = '".$row["COUNT"]."';\n";
		echo "document.getElementById('txt_trans_ref').value = '".$row["TRANS_REF"]."';\n";
		echo "document.getElementById('cbo_material_placement').value = '".$row["MATERIAL_PLACEMENT"]."';\n";
		echo "document.getElementById('cbo_buyer').value = '".$row["BUYER_ID"]."';\n";
		echo "document.getElementById('cbo_supplier').value = '".$row["SUPPLIER_ID"]."';\n";

		echo "$('#body_part').prop('disabled',true);\n";
		echo "$('#txt_gsm').prop('disabled',true);\n";
		echo "$('#dia_width').prop('disabled',true);\n";
		//echo "$('#dia_width_type').prop('disabled',true);\n";
		//echo "$('#txt_item_desc').prop('disabled',true);\n";
		echo "$('#txt_receive_qty').prop('disabled',true);\n";
		echo "$('#cbo_location').prop('disabled',true);\n";
		echo "$('#cbo_store').prop('disabled',true);\n";
		echo "$('#txt_receive_date').prop('disabled',true);\n";
		echo "$('#cbo_item_group').prop('disabled',true);\n";
		echo "$('#txt_color').prop('disabled',true);\n";

		if($row["RECEIVE_BASIS"] == 4) {
			echo "$('#txt_trans_ref').removeAttr('readonly');\n";
		}

		echo "set_button_status(1,'".$_SESSION['page_permission']."', 'fnc_scrap_material_receive_entry',1,1);\n";
	}
	exit();
}

if ($action=="populate_scrap_master_form_data")
{
	$sql_name_array = "SELECT a.id as ID, a.company_id as COMPANY_ID, a.location as LOCATION, a.store_id as STORE_ID, a.from_store_id as FROM_STORE_ID, a.receive_date as RECEIVE_DATE, a.receive_basis as RECEIVE_BASIS, a.item_category_id as ITEM_CATEGORY_ID, a.remarks as REMARKS, max(b.product_id) as PRODUCT_ID, max(b.trans_id) as TRANS_ID,a.from_floor,a.for_division,a.for_department,a.for_section, b.buyer_id
	from inv_scrap_receive_mst a, inv_scrap_receive_dtls b, product_details_master c	
	where a.id=b.mst_id and b.product_id=c.id and b.mst_id='$data' and b.status_active=1 and b.status_active=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
	group by a.id, a.company_id, a.location, a.store_id, a.from_store_id, a.receive_date, a.receive_basis, a.item_category_id, a.remarks,a.from_floor,a.for_division,a.for_department,a.for_section, b.buyer_id";
	// echo $sql_name_array; die;

	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer where status_active=1",'id','buyer_name');

	$nameArray=sql_select( $sql_name_array);
	foreach ($nameArray as $row)
	{
		echo "load_drop_down( 'requires/scrap_material_receive_controller', '".$row["LOCATION"]."_".$row["COMPANY_ID"]."_".$row["ITEM_CATEGORY_ID"]."', 'load_drop_down_store', 'store_td');\n";

		echo "document.getElementById('cbo_location').value		= '".$row["LOCATION"]."';\n";
		echo "document.getElementById('cbo_store').value		= '".$row["STORE_ID"]."';\n";
		echo "document.getElementById('cbo_from_store').value	= '".$row["FROM_STORE_ID"]."';\n";
		echo "document.getElementById('txt_trans_id').value		= '".$row["TRANS_ID"]."';\n";
		echo "document.getElementById('hidden_pord_id').value	= '".$row["PRODUCT_ID"]."';\n";
		echo "document.getElementById('txt_receive_date').value	= '".change_date_format($row["RECEIVE_DATE"],"yyyy-mm-dd")."';\n";
		echo "document.getElementById('cbo_receive_basis').value= '".$row["RECEIVE_BASIS"]."';\n";
		echo "document.getElementById('update_id').value        = '".$row["ID"]."';\n";
		echo "document.getElementById('update_dtls_id').value   = '".$row["DTLS_ID"]."';\n";
		echo "document.getElementById('txt_remarks_mst').value   = '".$row["REMARKS"]."';\n";

		echo "load_drop_down( 'requires/scrap_material_receive_controller', document.getElementById('cbo_location').value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_floor','folor_td');\n";
		echo "document.getElementById('cbo_folor_name').value   = '".$row["FROM_FLOOR"]."';\n";

		echo "load_drop_down( 'requires/scrap_material_receive_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_division','division_td');\n";
		 echo "document.getElementById('cbo_division_name').value   = '".$row["FOR_DIVISION"]."';\n";

		echo "load_drop_down( 'requires/scrap_material_receive_controller', document.getElementById('cbo_division_name').value, 'load_drop_down_department','department_td');\n";
		echo "document.getElementById('cbo_department_name').value   = '".$row["FOR_DEPARTMENT"]."';\n";

		echo "load_drop_down( 'requires/scrap_material_receive_controller', document.getElementById('cbo_department_name').value, 'load_drop_down_section','section_td');\n";
		echo "document.getElementById('cbo_section_name').value   = '".$row["FOR_SECTION"]."';\n";
		echo "document.getElementById('txt_transaction_byer').value   = '".$buyer_arr[$row["BUYER_ID"]]."';\n";


		echo "$('#body_part').prop('disabled',true);\n";
		echo "$('#txt_gsm').prop('disabled',true);\n";
		echo "$('#dia_width').prop('disabled',true);\n";
		//echo "$('#dia_width_type').prop('disabled',true);\n";
		//echo "$('#txt_item_desc').prop('disabled',true);\n";
		echo "$('#cbo_location').prop('disabled',true);\n";
		echo "$('#cbo_store').prop('disabled',true);\n";
		echo "$('#cbo_from_store').prop('disabled',true);\n";
		echo "$('#txt_receive_date').prop('disabled',true);\n";
		echo "$('#cbo_receive_basis').prop('disabled',true);\n";
		echo "$('#cbo_category_id').prop('disabled',true);\n";
		echo "$('#cbo_company_name').prop('disabled',true);\n";
		echo "$('#cbo_item_group').prop('disabled',true);\n";
		echo "$('#txt_remarks_mst').prop('disabled',true);\n";
		echo "$('#cbo_folor_name').prop('disabled',true);\n";
		echo "$('#cbo_division_name').prop('disabled',true);\n";
		echo "$('#cbo_department_name').prop('disabled',true);\n";
		echo "$('#cbo_section_name').prop('disabled',true);\n";

		echo "set_button_status(1,'".$_SESSION['page_permission']."', 'fnc_scrap_material_receive_entry',1,1);\n";
	}
	exit();
}

if($action=="mrr_popup")
{
	echo load_html_head_contents("MRR Popup", "../../../", 1, 1,'','','');
	extract($_REQUEST);
    ?>
	<script>
		function js_set_value(id,sys_num)
		{
			//alert (id);
			$('#hidden_system_id').val(id);
			$('#hidden_system_no').val(sys_num);
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
	<form action="" name="mrr_form1" id="item_descrption_form">
	<input type="hidden" name="hidden_system_id" id="hidden_system_id" />
    <input type="hidden" name="hidden_system_no" id="hidden_system_no" />
	<?
		($company != 0) ? $diabled=1 : $diabled=0;
		($cbo_category_id != 0) ? $diabled_cate=1 : $diabled_cate=0;
		?>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table" >
			<thead>
				<tr>
					<th width="170">Company</th>
					<th width="170">Item Category</th>
					<th width="150">System ID</th>
					<th width="150" colspan="2">Receive Date Range</th>
					<th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" onClick="reset_form('mrr_form1','mrr_search_div','','','','');"></th>
				</tr>                    
			</thead>
			<tbody>
				<tr>
					<td>
						<? 
							echo create_drop_down( "cbo_company_name", 170, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 order by company_name","id,company_name", 1, "-- Select Company --", $company, "",$diabled );
						?>
					</td>
					<td>
						<? 
							echo create_drop_down( "cbo_category_id", 170, $item_category,"",1, "-- Select Item --", $cbo_category_id, "", $diabled_cate, "");
						?>
					</td>
					<td align="center">
						<input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes_numeric"  placeholder="System ID" />
					</td>
					<td align="center">
						<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"  placeholder="From Date" />
					</td>
					<td align="center">
						<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker"  placeholder="To Date" />
					</td>
					<td align="center">
						<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company;?>+'_'+<? echo $cbo_category_id;?>+'_'+document.getElementById('txt_search_common').value, 'create_mrr_scrap_receive_search_list_view', 'mrr_search_div', 'scrap_material_receive_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;">
					</td>
				</tr>
				<tr>
					<td colspan="5"><? echo load_month_buttons(1);  ?></td>
				</tr>
			</tbody>
		</table>
		<br>
		<div valign="top" id="mrr_search_div" align="center"></div>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<?
	exit();
}

if($action=="create_mrr_scrap_receive_search_list_view")
{
	$data=explode('_',$data);
	$location_result = sql_select("select id as ID, location_name as LOCATION_NAME from lib_location where company_id=$data[2] and status_active=1 and is_deleted=0");
	$receive_scrap_arra = array("-- Select Basis","Receive-Reject","Issue-Damage", "Issue-Scrape Store");
	foreach ($location_result as $value) {
		$location_arr[$value['id']] = $value['location_name'];
	}
    //print_r($data);
    $company_cond=$category_cond=$search_cond='';
	if ($data[2]!=0) $company_cond=" and a.company_id='$data[2]'";
	if ($data[3]!=0) $category_cond=" and a.item_category_id='$data[3]'";
	if ($data[4]!=0) $search_cond=" and a.receive_no_prefix_num='$data[4]'";

	$receive_date_cond='';
	if($db_type==0)
	{
		if ($data[2]!='' &&  $data[3]!='') $receive_date_cond = "and a.receive_date between '".change_date_format($data[0],'yyyy-mm-dd')."' and '".change_date_format($data[1],'yyyy-mm-dd')."'";
	}
	else
	{
		if ($data[0]!='' &&  $data[1]!='') $receive_date_cond = "and a.receive_date between '".change_date_format($data[0], "", "",1)."' and '".change_date_format($data[1], "", "",1)."'";
	}

	
    $sql= "SELECT a.id as ID, a.sys_receive_no as SYS_RECEIVE_NO, a.company_id as COMPANY_ID, a.item_category_id as ITEM_CATEGORY_ID, a.location as LOCATION, a.store_id as STORE_ID, a.receive_date as RECEIVE_DATE, a.receive_basis as RECEIVE_BASIS, sum(b.receive_qnty) as RECEIVE_QNTY, b.product_id as PRODUCT_ID, b.lot as LOT
    from inv_scrap_receive_mst a, inv_scrap_receive_dtls b
    where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0  and b.is_deleted =0  and b.status_active=1 $company_cond $category_cond $receive_date_cond $search_cond 
    group by a.id, a.sys_receive_no, a.company_id, a.item_category_id, a.location, a.store_id, a.receive_date, a.receive_basis, b.product_id, b.lot";
	//echo $sql;
	$description_resutl = sql_select($sql);

	?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="870" class="rpt_table" id="html_search" align="left">
			<thead>
				<tr>
					<th width="40" align="center">SL</th>
					<th width="120" align="center">Receive Number</th>
					<th width="100" align="center">Product Id</th>
					<th width="120" align="center">Item Category</th>
					<th width="120" align="center">Lot/Batch</th>
					<th width="120" align="center">Location</th>
					<!-- <th width="80" align="center">Store</th> -->
					<th width="80" align="center">receive_date</th>
					<th align="center">receive_basis</th>
				</tr>
			</thead>
			<tbody>
				<?
					$i=1;
					foreach ($description_resutl as $value) 
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<?= $bgcolor; ?>" style="cursor:pointer;" onClick='js_set_value("<?= $value["ID"]; ?>","<?= $value["SYS_RECEIVE_NO"]; ?>")'>
							<td width="40" align="center"><?= $i; ?></td>
							<td width="120" align="center"><p><?= $value["SYS_RECEIVE_NO"]; ?></p></td>
							<td width="100" align="center"><p><?= $value["PRODUCT_ID"]; ?></p></td>
							<td width="120" align="center"><p><?= $item_category[$value["ITEM_CATEGORY_ID"]]; ?></p></td>
							<td width="120" align="center" style="word-wrap: break-all;"><p><?= $value["LOT"]; ?></p></td>
							<td width="120" align="center"><p><?= $location_arr[$value["LOCATION"]]; ?></p></td>
							<!-- <td align="center"><p><? //echo $value["store_id"]; ?></p></td> -->
							<td width="80" align="center"><p><?= change_date_format($value["RECEIVE_DATE"],'yyyy-mm-dd'); ?></p></td>
							<td align="center"><p><?= $receive_scrap_arra[$value["RECEIVE_BASIS"]]; ?></p></td>
						</tr>
						<?
						$i++;
					}
				?>
			</tbody>
		</table>
	<?
	exit();
}

if($action=='scrap_material_receive_print')
{
	list($company_id,$location_id,$sys_id,$sys_no,$report_title)=explode('*',$data);

	$company_library=return_library_array("select id, company_name from lib_company where status_active=1 and is_deleted=0 and id=$company_id", 'id', 'company_name');
	$item_cat_array = return_library_array("select category_id,short_name from lib_item_category_list where status_active=1 and is_deleted=0 $category_credential_cond  order by short_name", 'category_id', 'short_name');
	$receive_scrap_arr = array(1=>'Receive-Reject', 2=>'Issue-Damage', 3=>'Issue-Scrape Store', 4=>'Independent');
	$store_arr = return_library_array("select id, store_name from lib_store_location where status_active=1 and is_deleted=0", 'id', 'store_name');
	$supplier_arr  = return_library_array("select id, short_name from lib_supplier","id","short_name");
	$item_group_arr= return_library_array("select id, item_name from lib_item_group","id","item_name");
	$buyer_lib_arr=return_library_array("select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0", "id", "buyer_name");
	$item_group_arr=return_library_array("select id, item_name from lib_item_group where status_active=1 and is_deleted=0", "id", "item_name");
	$material_placement_arr = array(1=>'Top Floor', 2=>'Bulding Side', 3=>'Old Store', 4=>'Tin Shade');
	 
	$sql_receive = "select a.id, b.id as dtls_id, sum(b.receive_qnty) as receive_qnty, b.rate, sum(b.amount) as amount, b.remarks, b.book_currency, b.item_group_id, c.id as prod_id, c.product_name_details, c.unit_of_measure, b.lot, b.trans_ref, b.material_placement, b.buyer_id, b.supplier_id, a.sys_receive_no, a.item_category_id, a.receive_basis, a.store_id, a.from_store_id, a.receive_date, a.exchange_rate, a.currency, a.remarks as mst_remarks
		from inv_scrap_receive_mst a, inv_scrap_receive_dtls b, product_details_master c
		where a.id=b.mst_id and b.product_id = c.id and b.mst_id=$sys_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1
		group by a.id, b.id, b.rate, b.remarks, b.body_part, b.dia_type, b.book_currency, b.no_of_bags, b.trans_id, b.item_group_id, c.id, c.product_name_details, c.unit_of_measure, c.color, b.lot, c.gsm, c.dia_width, b.trans_ref, b.material_placement, b.buyer_id, b.supplier_id, a.sys_receive_no, a.item_category_id, a.receive_basis, a.store_id, a.from_store_id, a.receive_date, a.exchange_rate, a.currency, a.remarks";

	$sql_receive_res=sql_select($sql_receive);

	$com_dtls = fnc_company_location_address($company_id, $location_id, 2);
	?>
	<div>
	    <table cellspacing="0" align="center">
	        <tr>
	            <td align="center"><h1><?php echo $com_dtls[0]; ?></h1></td>
	        </tr>
	        <tr>
	        	<td align="center"><strong><?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
					foreach ($nameArray as $result)
					{
					?>
						 <? echo $result[csf('plot_no')]; ?>
						 <? if($result[csf('level_no')]!="") echo ",".$result[csf('level_no')]?>
						 <? if($result[csf('road_no')]!="") echo ",".$result[csf('road_no')]; ?>
						 <? if($result[csf('block_no')]!="") echo ",".$result[csf('block_no')];?>
						 <? if($result[csf('city')]!="") echo ",".$result[csf('city')];?>
						 <? if($result[csf('zip_code')]!="") echo ",".$result[csf('zip_code')]; ?>
						 <? if($result[csf('province')]!="") echo ",".$result[csf('province')];?>
						 <? if($result[csf('country_id')]!="") echo ",".$country_arr[$result[csf('country_id')]]; ?><br>
						 Email:<? if($result[csf('email')]!="") echo $result[csf('email')].",";?>
						 Website:<? if($result[csf('website')]!="") echo $result[csf('website')];

						 //echo $com_dtls[1]; 
					}
                ?> </strong></td>
	        </tr>
	    </table>

	    <table cellspacing="0" style="width: 90%; margin: 20px auto;">
	    	<tr>
	    		<td><strong>System ID:</strong></td>
	    		<td><?php echo $sql_receive_res[0][csf('sys_receive_no')]; ?></td>

	    		<td></td>

	    		<td><strong>Item Category:</strong></td>
	    		<td><?php echo $item_cat_array[$sql_receive_res[0][csf('item_category_id')]]; ?></td>

	    		<td></td>

	    		<td><strong>Receive Basis:</strong></td>
	    		<td><?php echo $receive_scrap_arr[$sql_receive_res[0][csf('receive_basis')]]; ?></td>

	    		<td></td>
	    		<td></td>
	    	</tr>
	    	<tr>
	    		<td><strong>Store Name:</strong></td>
	    		<td><?php echo $store_arr[$sql_receive_res[0][csf('store_id')]]; ?></td>

	    		<td></td>

	    		<td><strong>Receive Date:</strong></td>
	    		<td><?php echo change_date_format($sql_receive_res[0][csf('receive_date')]); ?></td>

	    		<td></td>

	    		<td><strong>Exchange Rate:</strong></td>
	    		<td><?php echo $sql_receive_res[0][csf('exchange_rate')]; ?></td>

	    		<td><strong>Currency:</strong></td>
	    		<td><?php echo $currency[$sql_receive_res[0][csf('currency')]]; ?></td>
	    	</tr>
	    	<tr>
	    		<td><strong>From Store Name:</strong></td>
	    		<td ><?php echo $store_arr[$sql_receive_res[0][csf('from_store_id')]]; ?></td>
	    		<td></td>
	    		<td><strong>Remarks:</strong></td>
	    		<td colspan="9"><?php echo $sql_receive_res[0][csf('mst_remarks')]; ?></td>
	    	</tr>
	    </table>
	</div>

	<div>
		<table cellspacing="0" border="1" rules="all" class="rpt_table" align="center" style="margin-top: 10px; width: 90%;">
	        <thead bgcolor="#dddddd">
	            <th width="30">SL</th>
	            <th width="100">Prod. ID</th>
	            <th width="100">Group</th>
	            <th width="200">Item Description</th>
	            <th width="100">Lot</th>
	            <th width="60">UOM</th>
	            <th width="80">Receive Qty</th>
	            <th width="80">Rate</th>
	            <th width="80">Amount</th>
	            <th width="80">Transaction Ref</th>
	            <th width="80">Metarial Placement</th>
	            <th width="80">Buyer</th>
	            <th width="80">Supplier</th>
	            <th>Remarks</th>
	        </thead>
	        <tbody>
	        	<?php
					$i=1;
					$total_qty=0;
					foreach($sql_receive_res as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
			            <tr bgcolor="<?php echo $bgcolor; ?>">
			            	<td><?php echo $i; ?></td>
			            	<td><?php echo $row[csf('prod_id')]; ?></td>
			            	<td><?php echo $item_group_arr[$row[csf('item_group_id')]]; ?></td>
			            	<td><?php echo $row[csf('product_name_details')]; ?></td>
			            	<td align="center"><?php echo $row[csf('lot')]; ?></td>
			            	<td><?php echo $unit_of_measurement[$row[csf('unit_of_measure')]]; ?></td>
			            	<td><?php echo $row[csf('receive_qnty')]; ?></td>
			            	<td><?php echo number_format($row[csf('rate')], 2); ?></td>
			            	<td><?php echo number_format($row[csf('amount')], 2); ?></td>
			            	<td><?php echo $row[csf('trans_ref')]; ?></td>
			            	<td><?php echo $material_placement_arr[$row[csf('material_placement')]]; ?></td>
			            	<td><?php echo $buyer_lib_arr[$row[csf('buyer_id')]]; ?></td>
			            	<td><?php echo $supplier_arr[$row[csf('supplier_id')]]; ?></td>
			            	<td><?php echo $row[csf('remarks')]; ?></td>
			           	</tr>
				<?php
					$i++;
					$total_qty+=$row[csf('receive_qnty')];
			       	}
				?>
	        </tbody>
	        <tfoot>
	        	<tr>
	            	
	            	<td colspan="6" align="right">Total:</td>
	            	<td align="right"><?php echo number_format($total_qty,2); ?></td>
	            	<td>&nbsp;</td>
	            	<td>&nbsp;</td>
	            	<td>&nbsp;</td>
	            	<td>&nbsp;</td>
	            	<td>&nbsp;</td>
	            	<td>&nbsp;</td>
	            	<td>&nbsp;</td>
	           	</tr>
	        </tfoot>
	    </table>
	    <br>
       <?
		  echo signature_table(225, $company_id, "1160px");
	   ?>
	</div>
	<?php
    
	exit();
}

?>
