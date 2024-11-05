<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//========== user credential start ========
$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id,store_location_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];

if ($company_id !='') {
    $company_credential_cond = "and comp.id in($company_id)";
}
if ($store_location_id !='') {
    $store_location_credential_cond = "and a.id in($store_location_id)"; 
}

// ========== user credential end ==========

if ($action=="load_room_rack_self_bin")
{
	$explodeData = explode('*', $data);
	$explodeData[11] = 'storeUpdateUptoDisable()';
	$data=implode('*', $explodeData);
	load_room_rack_self_bin("requires/trims_transfer_v2_controller",$data);
}

if ($action=="upto_variable_settings")
{
	extract($_REQUEST);
	echo $variable_inventory=return_field_value("store_method","variable_settings_inventory","company_name='$cbo_company_id' and item_category_id=4 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	exit();
}

/*if ($action=="load_drop_down_store")
{

	echo create_drop_down( "cbo_store_name", 160, "select a.id,a.store_name from lib_store_location a,lib_store_location_category  b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data and b.category_type in (4) $store_location_credential_cond group by a.id,a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", 0, "",0 );  	 
	exit();
}

if ($action=="load_drop_down_store_to")
{
	echo create_drop_down( "cbo_store_name_to", 160, "select a.id,a.store_name from lib_store_location a,lib_store_location_category  b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data and b.category_type in (4) $store_location_credential_cond group by a.id,a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", 0, "",0 );  	 
	exit();
}
*/

if($action == "load_drop_down_to_company_not_selected"){
    $companyIdArr = explode(',',$company_id);
    $companyIdArr = array_map('trim', $companyIdArr);
    unset($companyIdArr[array_search($data, $companyIdArr)]);
    $company_credential_cond = "and comp.id in (".implode(',',$companyIdArr).")";
    //echo "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_credential_cond order by comp.company_name";
    echo create_drop_down( "cbo_company_id_to", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_credential_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "if($('#cbo_company_id').val()*1 == this.value){alert('Same Company Transfer is not allowed!!'); $('#cbo_company_id_to').val('0'); return;}; load_drop_down( 'requires/trims_transfer_v2_controller',this.value, 'load_drop_down_location_to', 'to_location_td' );to_company_on_change(this.value);",1 );
    exit();
}
if($action == "load_drop_down_to_company"){
    $companyIdArr = explode(',',$company_id);
    $companyIdArr = array_map('trim', $companyIdArr);
    $company_credential_cond = "and comp.id in (".implode(',',$companyIdArr).")";
    //echo "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_credential_cond order by comp.company_name";
    echo create_drop_down( "cbo_company_id_to", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_credential_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "if($('#cbo_company_id').val()*1 == this.value){alert('Same Company Transfer is not allowed!!'); $('#cbo_company_id_to').val('0'); return;}; load_drop_down( 'requires/trims_transfer_v2_controller',this.value, 'load_drop_down_location_to', 'to_location_td' );to_company_on_change(this.value);",1 );
    exit();
}
//if($action == "load_drop_down_from_company_not_selected"){
//    $companyIdArr = explode(',',$company_id);
//    $companyIdArr = array_map('trim', $companyIdArr);
//    unset($companyIdArr[array_search($data, $companyIdArr)]);
//    $company_credential_cond = "and comp.id in (".implode(',', $companyIdArr).")";
//    echo create_drop_down( "cbo_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0  $company_credential_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "if (form_validation('cbo_transfer_criteria','Transfer Criteria')==false) return;load_drop_down( 'requires/trims_transfer_v2_controller',this.value, 'load_drop_down_location', 'from_location_td' );company_on_change(this.value);" );
//    exit();
//}

if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_location", 160, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "load_room_rack_self_bin('requires/trims_transfer_v2_controller*4', 'store','from_store_td', $('#cbo_company_id').val(),this.value, '', '', '','', '', '','', '215');");
	//if( $('#cbo_transfer_criteria').val()*1==2 || $('#cbo_transfer_criteria').val()*1==4)  load_room_rack_self_bin('requires/trims_transfer_v2_controller*4*cbo_store_name_to', 'store','to_store_td', $('#cbo_company_id').val(),this.value);
	exit();
}

if ($action=="load_drop_down_location_to")
{
	$data=explode("_",$data);
	if($data[1]==2){
		//echo create_drop_down( "cbo_location_to", 160, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "load_room_rack_self_bin('requires/finish_fabric_transfer_controller*2', 'store','to_store_td', $('#cbo_company_id').val(),this.value);",1 );
	}else{
		echo create_drop_down( "cbo_location_to", 160, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "load_room_rack_self_bin('requires/trims_transfer_v2_controller*4*cbo_store_name_to', 'store','to_store_td', $('#cbo_company_id_to').val(),this.value, '', '', '','', '', '','', '215');" );
	}
	exit();
}

$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
$item_name_arr=return_library_array( "select id,item_name from lib_item_group where item_category=4 and status_active=1 and is_deleted=0 order by item_name",'id','item_name');
$color_arr=return_library_array( "select id,color_name from  lib_color where status_active=1 and is_deleted=0 order by color_name",'id','color_name');


if ($action=="order_popup")
{
	echo load_html_head_contents("Order Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 
	<script>
		function js_set_value(data)
		{
			$('#order_id').val(data);
			parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
	<div align="center" style="width:995px;">
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:970px;">
			<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" width="950" class="rpt_table" border="1" rules="all">
					<thead>
						<th>Buyer Name</th>
						<th>Job No</th>
						<th>Order No</th>
						<th>Style Ref</th>
						<th>Internal Ref</th>
						<th width="230">Shipment Date Range</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
							<input type="hidden" name="order_id" id="order_id" class="text_boxes" value="">
						</th>
					</thead>
					<tr class="general">
						<td>
							<?
								echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",$data[0] );
							?>
						</td>
						<td>
							<input type="text" style="width:110px;" class="text_boxes" name="txt_job_no_fil" id="txt_job_no_fil" />
						</td>
						<td>
							<input type="text" style="width:110px;" class="text_boxes" name="txt_order_no" id="txt_order_no" />
						</td>
						<td>
							<input type="text" style="width:110px;" class="text_boxes" name="txt_style_ref" id="txt_style_ref" />
						</td>
						<td>                    
							<input type="text" style="width:110px;" class="text_boxes" name="txt_internal_ref" id="txt_internal_ref" />
						</td>
						<td>
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" readonly>
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" readonly>
						</td>
						<td>
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_order_no').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $type; ?>'+'_'+<? echo $cbo_company_id_to;?>+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_job_no_fil').value+'_'+document.getElementById('txt_style_ref').value+'_'+document.getElementById('cbo_year_selection').value, 'create_po_search_list_view', 'search_div', 'trims_transfer_v2_controller', 'setFilterGrid(\'list_view\',-1);')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td colspan="7" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
					</tr>
				</table>
				<div style="margin-top:10px" id="search_div" align="left"></div> 
			</fieldset>
		</form>
	</div>    
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=='create_po_search_list_view')
{
	$data=explode('_',$data);

	if ($data[0]==0) $buyer="%%"; else $buyer=$data[0];
	$search_string="%".trim($data[1])."%";
	//$internal_ref="%".trim($data[7])."%";
	$internal_ref=trim($data[7]);
	$company_id=$data[2];
	if ($data[3]!="" &&  $data[4]!="")
	{
		if($db_type==0)
		{
			$shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3],'','',1)."' and '".change_date_format($data[4],'','',1)."'";
		}
        $cbo_year_selection = "";
	}
	else{
        $shipment_date ="";
        $cbo_year_selection = " and to_char(b.insert_date, 'YYYY') = '$data[10]'";
    }
	$type=$data[5]; 
	$cbo_company_id_to=$data[6];
	if($type=="from") $company_cond=" and a.company_name=$company_id "; else $company_cond=" and a.company_name=$cbo_company_id_to";

	$company_arr = return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name" );
    $buyer_arr = return_library_array( "select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0", "id", "buyer_name" );
    $jobNoCond = $data[8] != '' ? " and a.job_no_prefix_num like '%$data[8]'" : '';
    $styleRefCond = $data[9] != '' ? " and a.STYLE_REF_NO ='$data[9]'" : '';
	//$arr=array (2=>$company_arr,3=>$buyer_arr);
	if($db_type==0) $year_field="YEAR(a.insert_date) as YEAR"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as YEAR";
	else $year_field="";//defined Later
	
	if($type=="from") $status_cond=" and b.status_active in(1,3)"; else $status_cond=" and b.status_active=1";
	
	if(str_replace("'","",$internal_ref) !="") $internal_ref_cond=" and b.grouping like '%$internal_ref%'";
	// echo $internal_ref_cond;die;
	$sql= "SELECT a.JOB_NO_PREFIX_NUM, $year_field, a.JOB_NO, a.COMPANY_NAME, a.BUYER_NAME, a.STYLE_REF_NO, a.JOB_QUANTITY, b.GROUPING, b.ID, b.PO_NUMBER, b.PO_QUANTITY, b.pub_shipment_date as SHIPMENT_DATE, c.ITEM_REF
	from wo_po_details_master a, wo_po_break_down b left join wo_trim_book_con_dtls c on b.id = c.po_break_down_id
	where a.job_no=b.job_no_mst and a.buyer_name like '$buyer' and b.po_number like '$search_string' and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $cbo_year_selection $jobNoCond $styleRefCond $company_cond $status_cond $shipment_date $internal_ref_cond order by b.id, b.pub_shipment_date";
	$sql_res=sql_select($sql); 
	// echo $sql;die;
	//echo create_list_view("list_view", "Job No,Internal Ref,Year,Company,Buyer Name,Style Ref. No,Job Qty.,PO number,PO Quantity,Shipment Date", "70,70,60,70,80,120,90,110,90,80","920","200",0, $sql , "js_set_value", "id", "", 1, "0,0,company_name,buyer_name,0,0,0,0", $arr , "job_no_prefix_num,grouping,year,company_name,buyer_name,style_ref_no,job_quantity,po_number,po_quantity,shipment_date", "",'','0,0,0,0,0,1,0,1,3');
	?>
	<table border="1" class="rpt_table" rules="all" width="1050" cellpadding="0" cellspacing="0" align="left">
		<thead>
            <tr>
                <th colspan="12"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
            </tr>

            <tr>
                <th width="30">SL</th>
                <th width="70">Job No</th>
                <th width="80">Internal Ref</th>
                <th width="50">Year</th>
                <th width="120">Company</th>
                <th width="120">Buyer Name</th>
                <th width="120">Style Ref No</th>
                <th width="100">Article No.</th>
                <th width="100">PO number</th>
                <th width="80">PO Quantity</th>              
                <th width="80">Job Qty</th>               
                <th>Shipment Date</th>
            </tr>
		</thead>				
    </table>
    <div style="width:1070px; max-height:420px; overflow-y:scroll;">
	    <table id="list_view" width="1050" border="1" rules="all" class="rpt_table" align="left">
	    	<?
	    	$i=1;
	    	foreach ($sql_res as $row) 
	    	{
	    		?>
		    	<tr onClick="js_set_value(<?= $row['ID']; ?>)" style="text-decoration:none; cursor:pointer">
	                <td width="30" align="center"><?= $i; ?></td>
	                <td width="70"><p><?= $row['JOB_NO_PREFIX_NUM']; ?></p></td>
	                <td width="80"><p><?= $row['GROUPING']; ?></p></td>
	                <td width="50" align="center"><p><?= $row['YEAR']; ?></p></td>
	                <td width="120"><p><?= $company_arr[$row['COMPANY_NAME']]; ?></p></td>
	                <td width="120"><p><?= $buyer_arr[$row['BUYER_NAME']]; ?></p></td>
	                <td width="120"><p><?= $row['STYLE_REF_NO']; ?></p></td>
	                <td width="100"><p><?= $row['ITEM_REF']; ?></p></td>
	                <td width="100"><p><?= $row['PO_NUMBER']; ?></p></td>
	                <td width="80" align="right"><p><?= $row['PO_QUANTITY']; ?></p></td>
	                <td width="80" align="right"><p><?= $row['JOB_QUANTITY']; ?></p></td>
	                <td align="center"><p><?= change_date_format($row['SHIPMENT_DATE']); ?></p></td>
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

if($action=='populate_data_from_order')
{
	$data=explode("**",$data);
	$po_id=$data[0];
	$which_order=$data[1];
	$transfer_criteria=$data[2];
	//$data_array=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$po_id");
	
	
	if($which_order=='from')
	{
		$data_array=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$po_id");
	}
	else
	{
		$data_array=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$po_id");
	}
	
	
	
	foreach ($data_array as $row)
	{ 
		$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
		foreach($gmts_item_id as $item_id)
		{
			if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
		}
		echo "document.getElementById('txt_".$which_order."_order_id').value 			= '".$po_id."';\n";
		echo "document.getElementById('txt_".$which_order."_order_no').value 			= '".$row[csf("po_number")]."';\n";
		echo "document.getElementById('txt_".$which_order."_po_qnty').value 			= '".$row[csf("po_quantity")]."';\n";
		echo "document.getElementById('cbo_".$which_order."_buyer_name').value 			= '".$row[csf("buyer_name")]."';\n";
		echo "document.getElementById('txt_".$which_order."_style_ref').value 			= '".$row[csf("style_ref_no")]."';\n";
		echo "document.getElementById('txt_".$which_order."_job_no').value 				= '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('txt_".$which_order."_gmts_item').value 			= '".$gmts_item."';\n";
		echo "document.getElementById('txt_".$which_order."_shipment_date').value 		= '".change_date_format($row[csf("shipment_date")])."';\n";

		if ($transfer_criteria==2) 
		{
			$which_order="to";
			echo "document.getElementById('txt_".$which_order."_order_id').value 			= '".$po_id."';\n";
			echo "document.getElementById('txt_".$which_order."_order_no').value 			= '".$row[csf("po_number")]."';\n";
			echo "document.getElementById('txt_".$which_order."_po_qnty').value 			= '".$row[csf("po_quantity")]."';\n";
			echo "document.getElementById('cbo_".$which_order."_buyer_name').value 			= '".$row[csf("buyer_name")]."';\n";
			echo "document.getElementById('txt_".$which_order."_style_ref').value 			= '".$row[csf("style_ref_no")]."';\n";
			echo "document.getElementById('txt_".$which_order."_job_no').value 				= '".$row[csf("job_no")]."';\n";
			echo "document.getElementById('txt_".$which_order."_gmts_item').value 			= '".$gmts_item."';\n";
			echo "document.getElementById('txt_".$which_order."_shipment_date').value 		= '".change_date_format($row[csf("shipment_date")])."';\n";
		}

		exit();
	}
}

if($action=="load_drop_down_item_desc")
{
	$item_description=array();
	$sql="select a.id, a.product_name_details from product_details_master a, order_wise_pro_details b where a.id=b.prod_id and b.po_breakdown_id=$data and b.entry_form in(24,78,112,12) and b.trans_type in(1,5) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0";
	$dataArray=sql_select($sql);	
	foreach($dataArray as $row)
	{
		$item_description[$row[csf('id')]]=$row[csf('product_name_details')];
	}
	echo create_drop_down( "cbo_item_desc", 403, $item_description,'', 1, "--Select Item Description--",'0','','1');  
	exit();
}
if($action=="show_ile_load_uom")
{
	$data=explode("_",$data);
	$uom=$trim_group_arr[$data[0]]['uom'];
	echo "document.getElementById('cbo_uom').value 	= '".$data[1]."';\n";
	exit();	
}

if($action=="show_dtls_list_view")
{
			
	$data_ref=explode("__",$data);
	//print_r($data);die;
	$order_id=$data_ref[0];
	$store_id=$data_ref[1];
	
	$store_name_arr = return_library_array("select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and b.category_type in(4) and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id","store_name");

	$floor_room_rack_arr = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst where status_active=1 and is_deleted=0 group by floor_room_rack_id, floor_room_rack_name order by floor_room_rack_name","floor_room_rack_id","floor_room_rack_name");

	$item_group_sql=sql_select("select a.id, b.conversion_factor, b.order_uom from product_details_master a, lib_item_group b where a.item_group_id=b.id and a.item_category_id=4 and a.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach($item_group_sql as $row)
	{
		$conversion_factor[$row[csf("id")]]=$row[csf("conversion_factor")];
		$group_order_uom[$row[csf("id")]]=$row[csf("order_uom")];
	}
	unset($item_group_sql);				
				
	$sql_trim = "select b.po_breakdown_id, a.id, c.store_id, c.floor_id, c.room, c.rack, c.self, c.bin_box, 
	sum(case when b.entry_form in(25) and b.trans_type in(2)  then b.quantity else 0 end) as issue_qty,
	sum(case when b.entry_form in(73) and b.trans_type in(4)  then b.quantity else 0 end) as issue_return_qty,
	sum(case when b.entry_form in(49)  and b.trans_type in(3) then b.quantity else 0 end) as recv_return_qty,
	sum(case when b.entry_form in(78,112) and b.trans_type in(6)  then b.quantity else 0 end) as item_transfer_issue,
	sum(case when b.entry_form in(78,112)  and b.trans_type in(5) then b.quantity else 0 end) as item_transfer_receive
	from product_details_master a, order_wise_pro_details b, inv_transaction c
	where a.id=b.prod_id and b.trans_id=c.id and a.id=c.prod_id and a.item_category_id=4 and c.item_category=4 and a.entry_form=24 and b.entry_form in(25,49,73,78,112) and b.trans_type in(2,3,4,6) and c.transaction_type in(2,3,4,6) and b.po_breakdown_id in($order_id)  and c.store_id in($store_id) and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 group by b.po_breakdown_id, a.id, c.store_id, c.floor_id, c.room, c.rack, c.self, c.bin_box";	
	//echo $sql_trim;//die;
	
	$data_array=sql_select($sql_trim);
	$trims_qty_array=array();
	foreach($data_array as $row)
	{
		$trims_qty_array[$row[csf('id')]][$row[csf('po_breakdown_id')]][$row[csf('store_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]['issue_qty']=$row[csf('issue_qty')]*$conversion_factor[$row[csf('id')]];
		$trims_qty_array[$row[csf('id')]][$row[csf('po_breakdown_id')]][$row[csf('store_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]['issue_return_qty']=$row[csf('issue_return_qty')]*$conversion_factor[$row[csf('id')]];
		$trims_qty_array[$row[csf('id')]][$row[csf('po_breakdown_id')]][$row[csf('store_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]['recv_return_qty']=$row[csf('recv_return_qty')]*$conversion_factor[$row[csf('id')]];
		$trims_qty_array[$row[csf('id')]][$row[csf('po_breakdown_id')]][$row[csf('store_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]['item_transfer_issue']=$row[csf('item_transfer_issue')]*$conversion_factor[$row[csf('id')]];
	}			
	 

	$sql_order_rate="select c.id as trans_id, b.po_breakdown_id, b.prod_id, c.cons_quantity, c.cons_amount
	from product_details_master a, order_wise_pro_details b, inv_transaction c
	where a.id=b.prod_id and b.trans_id=c.id and a.item_category_id='4' and a.entry_form=24 and b.trans_type in(1,5) and b.entry_form in(24,78,112) and b.po_breakdown_id in ($order_id) and c.store_id=$store_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.current_stock>0";
	//echo $sql_order_rate;die;
	$sql_order_rate_result=sql_select($sql_order_rate);
	$order_item_data=array();
	foreach($sql_order_rate_result as $row)
	{
		if($trans_id_check[$row[csf("trans_id")]]=="")
		{
			$trans_id_check[$row[csf("trans_id")]]=$row[csf("trans_id")];
			$order_item_data[$row[csf("prod_id")]]["cons_quantity"]+=$row[csf("cons_quantity")];
			$order_item_data[$row[csf("prod_id")]]["cons_amount"]+=$row[csf("cons_amount")];
		}
	}
	
	$sql = "SELECT a.id, a.product_name_details, b.po_breakdown_id as po_id, a.item_group_id, a.unit_of_measure, a.item_color, a.item_size, sum(b.quantity) as recv_qty, max(c.order_uom) as order_uom, c.store_id, c.floor_id,c.room, c.rack, c.self, c.bin_box 
	from product_details_master a 
	left join order_wise_pro_details b on a.id=b.prod_id 
	left join inv_transaction c on b.trans_id=c.id 
	where a.id=b.prod_id and b.trans_id=c.id and a.item_category_id=4 and c.item_category=4 and a.entry_form=24 and b.entry_form in(24,78,112) and b.trans_type in(1,5) and c.transaction_type in(1,5) and b.po_breakdown_id in($order_id) and c.store_id=$store_id and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 
	group by b.po_breakdown_id, a.id, a.item_group_id, a.product_name_details, a.unit_of_measure, a.item_color, a.item_size, c.store_id, c.floor_id,c.room, c.rack, c.self, c.bin_box 
	order by a.item_group_id";	
	//echo $sql;
	$data_array=sql_select($sql);
	?>
    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="1020">
    	<thead>
            <tr>
                <th colspan="12"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
            </tr>
            <tr>
                <th width="40">Prod Id</th>
                <th width="160">Item Description</th>
                <th width="100">Item Size</th>
                <th width="60">UOM</th>
                <th width="100">Item Color</th>
                <th width="80">Current Stock</th>
                <th width="80">Store</th>
                <th width="80">Floor</th>
                <th width="80">Room</th>
                <th width="80">Rack</th>
                <th width="80">Self</th>
                <th width="">Bin/Box</th>
            </tr>
        </thead>
    </table>
    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="1020" id="tbl_list_search">
        <tbody>
            <? 
            $i=1;
            foreach($data_array as $row)
            {  
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				//$receive_qnty=$row[csf('recv_qty')]*$conversion_factor[$row[csf('id')]];
				$receive_qnty=$row[csf('recv_qty')]*$conversion_factor[$row[csf('id')]];
				$issue_qty=$trims_qty_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('store_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]['issue_qty'];
				$issue_return_qty=$trims_qty_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('store_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]['issue_return_qty'];
				$recv_return_qty=$trims_qty_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('store_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]['recv_return_qty'];
				$transfer_out_qty=$trims_qty_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('store_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]['item_transfer_issue'];
				
				$order_uom=$row[csf('order_uom')];
				if($order_uom=="")
				{
					$order_uom=$group_order_uom[$row[csf("id")]];
				}
				
				$order_rate=($order_item_data[$row[csf("id")]]["cons_amount"]/$order_item_data[$row[csf("id")]]["cons_quantity"]);
			 
				$current_stock_qty=($receive_qnty+$issue_return_qty)-($issue_qty+$recv_return_qty+$transfer_out_qty);
				if($current_stock_qty>0)
				{
					?>

					<tr bgcolor="<? echo $bgcolor; ?>" onClick='set_form_data("<? echo $row[csf('id')]."**".$row[csf('product_name_details')]."**".$row[csf('item_group_id')]."**".$row[csf('unit_of_measure')]."**".$current_stock_qty."**".$row[csf("store_id")]."**".$row[csf("floor_id")]."**".$row[csf("room")]."**".$row[csf("rack")]."**".$row[csf("self")]."**".$row[csf("bin_box")]."**".$order_rate."**".$color_arr[$row[csf('item_color')]]."**".$row[csf('item_size')]; ?>");change_color("tr_<? echo $i; ?>","<? echo $bgcolor;?>");' id="tr_<? echo $i; ?>" style="cursor:pointer">
						<td width="40"><p><? echo $row[csf('id')]; ?>&nbsp;</p></td>
                        <td width="160"><p><? echo $row[csf('product_name_details')]; ?>&nbsp;</p></td>
						<td width="100"><p>&nbsp;<? echo $row[csf('item_size')]; ?></p></td>
						<td width="60" align="center"><p>&nbsp;<? echo $unit_of_measurement[$row[csf('unit_of_measure')]]; ?></p></td>
						<td width="100"><p>&nbsp;<? echo $color_arr[$row[csf('item_color')]]; ?></p></td>
                        <td align="right" width="80"><p>&nbsp;<? echo number_format($current_stock_qty,4);//number_format ?></p></td>
                        <td width="80"><p><? echo $store_name_arr[$row[csf("store_id")]]; ?></p></td>
		                <td width="80"><p><? echo $floor_room_rack_arr[$row[csf("floor_id")]]; ?></p></td>
		                <td width="80"><p><? echo $floor_room_rack_arr[$row[csf("room")]]; ?></p></td>
		                <td width="80"><p><? echo $floor_room_rack_arr[$row[csf("rack")]]; ?></p></td>
		                <td width="80"><p><? echo $floor_room_rack_arr[$row[csf("self")]]; ?></p></td>
		                <td ><p><? echo $floor_room_rack_arr[$row[csf("bin_box")]]; ?></p></td>
					</tr>
					<? 
					$i++; 
				}
            } 
            ?>
        </tbody>
    </table>
	<?
	exit();
}

if($action=='populate_floor_room_rack_shelf_bin')
{
	// print_r($data);die; 
	$data_ref=explode("**",$data);
	$from_company_id=$data_ref[0];
	$from_store_id=$data_ref[1];
	$floor_id=$data_ref[2];
	$room=$data_ref[3];
	$rack=$data_ref[4];
	$self=$data_ref[5];
	$bin=$data_ref[6];
	$from_location=$data_ref[7];
	//echo $floor_id.'='.$room.'='.$rack.'='.$self.'='.$bin;die;

		echo "load_room_rack_self_bin('requires/trims_transfer_v2_controller*4', 'store','from_store_td', '".$from_company_id."','".$from_location."',this.value, '', '','', '', '','', '215');\n";
		echo "document.getElementById('cbo_store_name').value 				= '".$from_store_id."';\n";

	//if($floor_id !=0)
	//{
		echo "load_room_rack_self_bin('requires/trims_transfer_v2_controller*4', 'floor','floor_td', '".$from_company_id."','".$from_location."','".$from_store_id."',this.value);\n";
		echo "document.getElementById('cbo_floor').value 				= '".$floor_id."';\n";
	//}
	//if($room !=0)
	//{
		echo "load_room_rack_self_bin('requires/trims_transfer_v2_controller*4', 'room','room_td', '".$from_company_id."','".$from_location."','".$from_store_id."','".$floor_id."',this.value);\n";
		echo "document.getElementById('cbo_room').value 				= '".$room."';\n";
	//}
	//if($rack !=0)
	//{
		echo "load_room_rack_self_bin('requires/trims_transfer_v2_controller*4', 'rack','rack_td', '".$from_company_id."','".$from_location."','".$from_store_id."','".$floor_id."','".$room."',this.value);\n";
		echo "document.getElementById('txt_rack').value 				= '".$rack."';\n";
	//}
	//if($self !=0)
	//{
		echo "load_room_rack_self_bin('requires/trims_transfer_v2_controller*4', 'shelf','shelf_td', '".$from_company_id."','".$from_location."','".$from_store_id."','".$floor_id."','".$room."','".$rack."',this.value);\n";
		echo "document.getElementById('txt_shelf').value 				= '".$self."';\n";
	//}

	//if($bin !=0)
	//{
		echo "load_room_rack_self_bin('requires/trims_transfer_v2_controller*4', 'bin','bin_td', '".$from_company_id."','"."','".$from_store_id."','".$floor_id."','".$room."','".$rack."','".$self."',this.value);\n";
		echo "document.getElementById('cbo_bin').value 					= '".$bin."';\n";
	//}
}

if ($action=="orderToorderTransfer_popup")
{
	echo load_html_head_contents("Order To Order Transfer Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?> 

	<script>
		
		function js_set_value(data)
		{
			var id = data.split("_");
			$('#transfer_id').val(id[0]);
			$('#hidden_posted_in_account').val(id[1]);
			parent.emailwindow.hide();
		}
        function createContentSearch(id) {
            var data = ["Exact","Starts with","Ends with","Contents"];
            var appender = '';
            appender += '<select name="cbo_string_search_type" id="cbo_string_search_type" class="combo_boxes " style="width:130px" onchange="">';
            appender += '<option data-attr="" value="0">-- Searching Type --</option>';
            $.each(data, function (index, val){
                if(index == 3){
                    appender += '<option data-attr="" value="'+(index+1)+'" selected>'+val+'</option>';
                }else{
                    appender += '<option data-attr="" value="'+(index+1)+'">'+val+'</option>';
                }
            });
            appender += '</select>';
            $('#'+id).find('thead').prepend('<tr><th colspan="8">'+appender+'</th></tr>');
        }
	
    </script>

</head>

<body>
<div align="center" style="width:800px;">
	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:800px;margin-left:10px">
        <legend>Enter search words</legend>
            <table cellpadding="0" cellspacing="0" width="800" class="rpt_table">
                <thead>
                    <th width="200">Search By</th>
                    <th width="200" id="search_by_td_up">Please Enter Transfer ID</th>
                    <th width="250">Date Range</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
                        <input type="hidden" name="transfer_id" id="transfer_id" class="text_boxes" value="">
                        <input type="hidden" name="hidden_posted_in_account" id="hidden_posted_in_account" class="text_boxes" value="">
                    </th>
                </thead>
                <tr class="general">
                    <td>
						<?
							$search_by_arr=array(1=>"Transfer ID",2=>"Challan No.");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../../') ";
							echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                        ?>
                    </td>
                    <td id="search_by_td">
                        <input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                    </td>
                    <td>
                        <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px">To
                        <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px">
                    </td>
                    <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+<? echo $cbo_transfer_criteria; ?>, 'create_transfer_search_list_view', 'search_div', 'trims_transfer_v2_controller', 'setFilterGrid(\'tbl_list_search\',-1); createContentSearch(\'rpt_tabletbl_list_search\');')" style="width:100px;" />
                    </td>
                </tr>
                <tr>
                	<td colspan="4" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
            </table>
        	<div style="margin-top:10px" id="search_div"></div> 
		</fieldset>
	</form>
</div>    
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=='create_transfer_search_list_view')
{
	$data = explode("_",$data);
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$company_id =$data[2];
	$date_form=$data[3];
	$date_to =$data[4];
	$year_id=$data[5];
    $transferCriteria = $data[6];
	
	if($db_type==0)
	{
		$date_form=change_date_format($date_form,'yyyy-mm-dd');
		$date_to=change_date_format($date_to,'yyyy-mm-dd');
	}
	else
	{
		$date_form=change_date_format($date_form,'','',1);
		$date_to=change_date_format($date_to,'','',1);
	}
	
	if($date_form!="" && $date_to!="") $date_cond=" and transfer_date between '$date_form' and '$date_to'";
	
	//echo $date_form."=".$date_to."=".$year_id;die;
	
	
	if($search_by==1)
		$search_field="transfer_system_id";	
	else
		$search_field="challan_no";
	
	if($db_type==0) $year_field="YEAR(insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	if($year_id>0)
	{
		if($db_type==0)
		{
			$year_condition=" and YEAR(insert_date)='$year_id'";
		}
		else
		{
			$year_condition=" and to_char(insert_date,'YYYY')='$year_id'";
		}
	}
	
 	$sql="select id, transfer_prefix_number, transfer_system_id, $year_field, challan_no, company_id, transfer_date, transfer_criteria, item_category, is_posted_account from inv_item_transfer_mst where item_category=4 and company_id=$company_id and $search_field like '$search_string' and transfer_criteria in(1,2,4) and status_active=1 and transfer_criteria = $transferCriteria and is_deleted=0 $date_cond $year_condition order by id desc";
 	// echo $sql;
	
	$arr=array(3=>$company_arr,5=>$item_transfer_criteria,6=>$item_category);

	echo  create_list_view("tbl_list_search", "Transfer ID,Year,Challan No,Company,Transfer Date,Transfer Criteria,Item Category", "80,70,100,110,90,120","760","250",0, $sql, "js_set_value", "id,is_posted_account", "", 1, "0,0,0,company_id,0,transfer_criteria,item_category", $arr, "transfer_prefix_number,year,challan_no,company_id,transfer_date,transfer_criteria,item_category", '','','0,0,0,0,3,0,0');
	
	exit();
}

if($action=='populate_data_from_transfer_master')
{
	$data_array=sql_select("SELECT a.transfer_system_id, a.challan_no, a.company_id, a.to_company, a.transfer_criteria, a.location_id, a.to_location_id, a.transfer_date, a.item_category, a.from_order_id,a.to_order_id, a.from_store_id, a.to_store_id,b.from_store,b.to_store,b.floor_id,b.room,b.rack,b.shelf,b.bin_box,b.to_floor_id,b.to_room,b.to_rack,b.to_shelf,b.to_bin_box
	from inv_item_transfer_mst a,inv_item_transfer_dtls b 
	where a.id='$data' and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	
	$company_id=$data_array[0][csf("company_id")];
	$variable_inventory_sql=sql_select("select store_method, rack_balance from variable_settings_inventory  where company_name=$company_id and item_category_id=4 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	$store_method=$variable_inventory_sql[0][csf("store_method")];

	foreach ($data_array as $row)
	{
		$to_company = (str_replace("'", "", $row[csf("to_company")]) == 0 ) ? $to_company = $row[csf("company_id")] : $to_company = $row[csf("to_company")];
		
		echo "document.getElementById('update_id').value 					= '".$data."';\n";
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("transfer_system_id")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_company_id_to').value 			= '".$to_company."';\n";
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('store_update_upto').value 			= '".$store_method."';\n";

		echo "document.getElementById('cbo_transfer_criteria').value 		= '".$row[csf("transfer_criteria")]."';\n";
		echo "document.getElementById('cbo_location').value 				= '".$row[csf("location_id")]."';\n";
		

		if ( str_replace("'", "", $row[csf("transfer_criteria")]) == 1 ) 
		{
			echo "load_drop_down( 'requires/trims_transfer_v2_controller',$to_company, 'load_drop_down_location_to', 'to_location_td' );\n";
		}
		echo "document.getElementById('cbo_location_to').value 				= '".$row[csf("to_location_id")]."';\n";


		echo "load_room_rack_self_bin('requires/trims_transfer_v2_controller*4', 'store','from_store_td', '".$row[csf('company_id')]."','"."',this.value, '', '','', '', '','', '215');\n";
		echo "document.getElementById('cbo_store_name').value 				= '".$row[csf("from_store")]."';\n";
		echo "load_room_rack_self_bin('requires/trims_transfer_v2_controller*4', 'floor','floor_td', '".$row[csf('company_id')]."','"."','".$row[csf('from_store')]."',this.value);\n";
		echo "document.getElementById('cbo_floor').value 				= '".$row[csf("floor_id")]."';\n";
		echo "load_room_rack_self_bin('requires/trims_transfer_v2_controller*4', 'room','room_td', '".$row[csf('company_id')]."','"."','".$row[csf('from_store')]."','".$row[csf('floor_id')]."',this.value);\n";
		echo "document.getElementById('cbo_room').value 				= '".$row[csf("room")]."';\n";
		echo "load_room_rack_self_bin('requires/trims_transfer_v2_controller*4', 'rack','rack_td', '".$row[csf('company_id')]."','"."','".$row[csf('from_store')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."',this.value);\n";
		echo "document.getElementById('txt_rack').value 				= '".$row[csf("rack")]."';\n";
		echo "load_room_rack_self_bin('requires/trims_transfer_v2_controller*4', 'shelf','shelf_td', '".$row[csf('company_id')]."','"."','".$row[csf('from_store')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."',this.value);\n";
		echo "document.getElementById('txt_shelf').value 				= '".$row[csf("shelf")]."';\n";
		echo "load_room_rack_self_bin('requires/trims_transfer_v2_controller*4', 'bin','bin_td', '".$row[csf('company_id')]."','"."','".$row[csf('from_store')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."','".$row[csf('shelf')]."',this.value);\n";
		echo "document.getElementById('cbo_bin').value 				= '".$row[csf("bin_box")]."';\n";


		echo "load_room_rack_self_bin('requires/trims_transfer_v2_controller*4*cbo_store_name_to', 'store','to_store_td', '".$to_company."','"."',this.value, '', '','', '', '','', '215');\n";
		echo "document.getElementById('cbo_store_name_to').value 				= '".$row[csf("to_store")]."';\n";

		echo "load_room_rack_self_bin('requires/trims_transfer_v2_controller*4*cbo_floor_to', 'floor','floor_td_to', '".$to_company."','"."','".$row[csf('to_store')]."',this.value);\n";
		echo "document.getElementById('cbo_floor_to').value 			= '".$row[csf("to_floor_id")]."';\n";
		echo "load_room_rack_self_bin('requires/trims_transfer_v2_controller*4*cbo_room_to', 'room','room_td_to', '".$to_company."','"."','".$row[csf('to_store')]."','".$row[csf('to_floor_id')]."',this.value);\n";
		echo "document.getElementById('cbo_room_to').value 				= '".$row[csf("to_room")]."';\n";
		echo "load_room_rack_self_bin('requires/trims_transfer_v2_controller*4*txt_rack_to', 'rack','rack_td_to', '".$to_company."','"."','".$row[csf('to_store')]."','".$row[csf('to_floor_id')]."','".$row[csf('to_room')]."',this.value);\n";
		echo "document.getElementById('txt_rack_to').value 				= '".$row[csf("to_rack")]."';\n";
		echo "load_room_rack_self_bin('requires/trims_transfer_v2_controller*4*txt_shelf_to', 'shelf','shelf_td_to', '".$to_company."','"."','".$row[csf('to_store')]."','".$row[csf('to_floor_id')]."','".$row[csf('to_room')]."','".$row[csf('to_rack')]."',this.value);\n";
		echo "document.getElementById('txt_shelf_to').value 			= '".$row[csf("to_shelf")]."';\n";
		echo "load_room_rack_self_bin('requires/trims_transfer_v2_controller*4*cbo_bin_to', 'bin','bin_td_to', '".$to_company."','"."','".$row[csf('to_store')]."','".$row[csf('to_floor_id')]."','".$row[csf('to_room')]."','".$row[csf('to_rack')]."','".$row[csf('to_shelf')]."',this.value);\n";
		echo "document.getElementById('cbo_bin_to').value 			= '".$row[csf("to_bin_box")]."';\n";

		echo "$('#cbo_transfer_criteria').attr('disabled','disabled');\n";
		echo "$('#cbo_company_id_to').attr('disabled','disabled');\n";
		echo "$('#cbo_location').attr('disabled','disabled');\n";
		echo "$('#cbo_location_to').attr('disabled','disabled');\n";

		echo "$('#cbo_store_name').attr('disabled','disabled');\n";
		echo "$('#cbo_floor').attr('disabled','disabled');\n";
		echo "$('#cbo_room').attr('disabled','disabled');\n";
		echo "$('#txt_rack').attr('disabled','disabled');\n";
		echo "$('#txt_shelf').attr('disabled','disabled');\n";
		echo "$('#cbo_bin').attr('disabled','disabled');\n";


		echo "$('#cbo_store_name_to').attr('disabled','disabled');\n";
		/*echo "$('#cbo_floor_to').attr('disabled','disabled');\n";
		echo "$('#cbo_room_to').attr('disabled','disabled');\n";
		echo "$('#txt_rack_to').attr('disabled','disabled');\n";
		echo "$('#txt_shelf_to').attr('disabled','disabled');\n";
		echo "$('#cbo_bin_to').attr('disabled','disabled');\n";*/
		//echo "document.getElementById('cbo_store_name').value 				= '".$row[csf("from_store_id")]."';\n";
		//echo "document.getElementById('cbo_store_name_to').value 				= '".$row[csf("to_store_id")]."';\n";

		echo "document.getElementById('txt_transfer_date').value 			= '".change_date_format($row[csf("transfer_date")])."';\n";
		
		echo "get_php_form_data('".$row[csf("from_order_id")]."**from'".",'populate_data_from_order','requires/trims_transfer_v2_controller');\n";
		echo "get_php_form_data('".$row[csf("to_order_id")]."**to'".",'populate_data_from_order','requires/trims_transfer_v2_controller');\n";
		echo "$('#cbo_company_id').attr('disabled','disabled');\n";
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_trims_transfer_entry',1,1);\n"; 
		exit();
	}
}

if($action=="show_transfer_listview")
{
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=4 and status_active=1 and is_deleted=0","id","product_name_details");
	
	$sql="select id, from_prod_id, transfer_qnty, item_category, uom from inv_item_transfer_dtls where mst_id='$data' and status_active = '1' and is_deleted = '0'";
	
	$arr=array(0=>$item_category,1=>$product_arr,3=>$unit_of_measurement);
	 
	echo  create_list_view("list_view", "Item Category,Item Description,Transfered Qnty,UOM", "120,250,100","650","200",0, $sql, "get_php_form_data", "id", "'populate_transfer_details_form_data'", 0, "item_category,from_prod_id,0,uom", $arr, "item_category,from_prod_id,transfer_qnty,uom", "requires/trims_transfer_v2_controller",'','0,0,2,0');
	exit();
}

if($action=='populate_transfer_details_form_data')
{
	
	//$data_array=sql_select("select transfer_system_id, challan_no, company_id, transfer_date, item_category, from_order_id,to_order_id, from_store_id, to_store_id from inv_item_transfer_mst where id='$data' and status_active=1 and is_deleted=0");
	
	$data_array=sql_select("SELECT a.from_order_id, a.to_order_id, a.from_store_id, a.to_store_id, b.id, b.mst_id, b.item_group, b.from_prod_id, b.to_prod_id, b.transfer_qnty, b.item_category, b.uom, b.remarks from inv_item_transfer_mst a, inv_item_transfer_dtls b 
	where a.id=b.mst_id and b.id='$data'");
	foreach ($data_array as $row)
	{ 
		
		//echo "select from_order_id from inv_item_transfer_mst where id=".$row[csf('mst_id')]." and  status_active=1 and is_deleted=0 ";
		
		
		echo "document.getElementById('update_dtls_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_item_desc').value 				= '".$row[csf("from_prod_id")]."';\n";
		echo "document.getElementById('txt_transfer_qnty').value 			= '".$row[csf("transfer_qnty")]."';\n";
		echo "document.getElementById('hidden_transfer_qnty').value 		= '".$row[csf("transfer_qnty")]."';\n";
		echo "document.getElementById('cbo_item_category').value 			= '".$row[csf("item_category")]."';\n";
		echo "document.getElementById('txt_item_id').value 					= '".$row[csf("item_group")]."';\n";
		echo "document.getElementById('cbo_uom').value 						= '".$row[csf("uom")]."';\n";
		echo "document.getElementById('txt_remarks').value 					= '".$row[csf("remarks")]."';\n";
		
		
		/* $sql_sk =sql_select( "select 
		sum(case when b.entry_form in(24) then b.quantity else 0 end) as recv_qty,
		sum(case when b.entry_form in(25) then b.quantity else 0 end) as issue_qty
		from product_details_master a, order_wise_pro_details b, inv_transaction c
		where  
		a.id=b.prod_id and b.trans_id=c.id and a.item_category_id=4 and b.entry_form in(24,25) and b.po_breakdown_id=".$cond_po_id." and b.prod_id='".$row[csf("from_prod_id")]."'  and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0");	*/
				
		
		$conversion_factor=return_field_value("b.conversion_factor as conversion_factor","product_details_master a, lib_item_group b","a.item_group_id=b.id and a.item_category_id=4 and a.entry_form=24 and a.id='".$row[csf("from_prod_id")]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","conversion_factor");
		
		$sql_trim = sql_select("SELECT sum((case when b.entry_form in(24,73,78,112) and b.trans_type in(1,4,5) then b.quantity else 0 end)-(case when b.entry_form in(25,49,78,112) and b.trans_type in(2,3,6) then b.quantity else 0 end)) as balance_qnty, e.color_name, d.size_name 
		from product_details_master a left join order_wise_pro_details b on a.id=b.prod_id left join inv_transaction c on b.trans_id=c.id left join lib_size d on d.id = a.gmts_size left join lib_color e on e.id = a.item_color
		where a.item_category_id=4 and c.item_category=4 and b.entry_form in(24,25,78,73,49,112) and b.trans_type in(1,2,3,4,5,6) and c.transaction_type in(1,2,3,4,5,6) and c.status_active=1  and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.po_breakdown_id=".$row[csf("from_order_id")]." and b.prod_id='".$row[csf("from_prod_id")]."' and c.store_id ='".$row[csf("from_store_id")]."' group by e.color_name, d.size_name  ");

		$curr_stock=($sql_trim[0][csf('balance_qnty')]*$conversion_factor)+$row[csf("transfer_qnty")];
		echo "document.getElementById('txt_current_stock').value 			= '".$curr_stock."';\n";
        echo "document.getElementById('txt_item_color').value 			= '".$sql_trim[0][csf("color_name")]."';\n";
        echo "document.getElementById('txt_item_size').value 			= '".$sql_trim[0][csf("size_name")]."';\n";

        $sql_trans=sql_select("SELECT trans_id from order_wise_pro_details where dtls_id=".$row[csf('id')]." and entry_form in(78,112) and trans_type in(5,6) and status_active=1 and is_deleted=0 order by trans_type DESC");
		echo "document.getElementById('update_trans_issue_id').value 		= '".$sql_trans[0][csf("trans_id")]."';\n";
		echo "document.getElementById('update_trans_recv_id').value 		= '".$sql_trans[1][csf("trans_id")]."';\n";

		echo "document.getElementById('previous_from_prod_id').value 	= '".$row[csf("from_prod_id")]."';\n";
		echo "document.getElementById('previous_to_prod_id').value 		= '".$row[csf("to_prod_id")]."';\n";

		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_trims_transfer_entry',1,1);\n"; 
		
		exit();
	}
}

//data save update delete here------------------------------//
if($action=="save_update_delete")
{	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
        

	$update_trans_issue_id=str_replace("'","",$update_trans_issue_id);
	$update_trans_recv_id=str_replace("'","",$update_trans_recv_id); 
	$transfer_criteria=str_replace("'","",$cbo_transfer_criteria); 
	
	$up_tr_cond="";
	if($update_trans_issue_id >0 && $update_trans_recv_id >0)
	{
		$up_tr_cond=" and id not in($update_trans_issue_id,$update_trans_recv_id)";
		$trans_sql=sql_select("select sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as bal
		from inv_transaction where status_active=1 and is_deleted=0 and prod_id=$cbo_item_desc and store_id=$cbo_store_name_to $up_tr_cond");
		$stockQnty=$trans_sql[0][csf("bal")]*1;
		$trnsQnty=str_replace("'","",$txt_transfer_qnty);
		if($stockQnty < 0)
		{
			 echo "20**Transfer Quantity Not Allow Over Stock Quantity.";disconnect($con);die;
		}
	}
	
	$trans_sql=sql_select("select sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as bal
	from inv_transaction where status_active=1 and is_deleted=0 and prod_id=$cbo_item_desc and store_id=$cbo_store_name $up_tr_cond");
	$stockQnty=$trans_sql[0][csf("bal")]*1;
	$trnsQnty=str_replace("'","",$txt_transfer_qnty);
	if($trnsQnty > $stockQnty)
	{
		 echo "20**Transfer Quantity Not Allow Over Stock Quantity.";disconnect($con);die;
	}
	
	if ($transfer_criteria==4) 
	{
		$entry_form_no=78; // order to order
		$prefix_no="TSOTOTE";
	}
	else{
		$entry_form_no=112; // Company to Company and Store to Store
		$prefix_no="TTE";
	}
	
	if($operation!=2) 
	{
		//------------Check Transfer In Date with last Transaction Date-----------------
		$is_update_cond_for_rcv = ($operation==1)? " and id not in ( $update_trans_recv_id , $update_trans_issue_id ) ": "";
		$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$cbo_item_desc and store_id=$cbo_store_name_to $is_update_cond_for_rcv and status_active = 1", "max_date");      
		if($max_recv_date != "")
		{
			$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
			$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));
			if ($transfer_date < $max_recv_date) 
			{
			  echo "20**Transfer in Date Can not Be Less Than Last Transaction Date Of This Item";
				die;
			}
		}
		
		//------------Check Transfer Out Date with last Receive Date--------------------
		$is_update_cond_for_iss = ($operation==1)? " and id <> $update_trans_recv_id ": "";
		$max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$cbo_item_desc and store_id = $cbo_store_name and transaction_type in (1,4,5) $is_update_cond_for_iss and status_active = 1 and is_deleted=0", "max_date");      
		if($max_issue_date != "")
		{
			$max_issue_date = date("Y-m-d", strtotime($max_issue_date));
			$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));
			if ($transfer_date < $max_issue_date) 
			{
			   echo "20**Transfer Out Date Can not Be Less Than Last Receive Date Of This Item";
				die;
			}
		}
	}
	
	if(str_replace("'","",$update_id)!="")
	{

		$is_acknowledge = return_field_value("b.id id", "inv_item_transfer_mst a,inv_item_trans_acknowledgement b", "a.id=b.challan_id and  a.id=$update_id and a.status_active=1 and a.is_acknowledge=1", "id");
		if($is_acknowledge != "" )
		{
			echo "20**Update not allowed. This Transfer Challan is already Acknowledged.\nAcknowledge System ID = $is_acknowledge";
			disconnect($con);die;
		}
	}
        
	$conversion_factor=return_field_value("b.conversion_factor as conversion_factor","product_details_master a, lib_item_group b","a.item_group_id=b.id and a.item_category_id=4 and a.entry_form=24 and a.id=$cbo_item_desc and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","conversion_factor");
	$rate=return_field_value("avg_rate_per_unit","product_details_master","id=".$cbo_item_desc." and status_active=1 and is_deleted=0","avg_rate_per_unit");
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		$variable_auto_rcv = return_field_value("auto_transfer_rcv", "variable_settings_inventory", " company_name=$cbo_company_id and item_category_id=4 and status_active=1 and variable_list= 27", "auto_transfer_rcv");

		if($variable_auto_rcv == "")
		{
			$variable_auto_rcv = 1; // if auto receive yes(1), then no need to acknowledgement
		}
		//echo "10**".$variable_auto_rcv; die;
		
		$transfer_recv_num=''; $transfer_update_id='';
		$sql_budge_check=sql_select("select a.id from wo_pre_cost_trim_cost_dtls a, wo_pre_cost_trim_co_cons_dtls b where a.id=b.wo_pre_cost_trim_cost_dtls_id and a.trim_group=$txt_item_id and b.po_break_down_id=$txt_from_order_id");
		if(count($sql_budge_check)<1)
		{
			echo "11**This Item Not Found In Budget";
			disconnect($con);
			die;
		}
		
		$sqlCon="";	
		$store_update_upto=str_replace("'","",$store_update_upto);
		if($store_update_upto > 1)
		{
			if($store_update_upto==6)
			{
				if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
				if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and c.room=$cbo_room" ;}
				if(str_replace("'","",$txt_rack)!=0){$sqlCon.= " and c.rack=$txt_rack" ;}
				if(str_replace("'","",$txt_shelf)!=0){$sqlCon.= " and c.self=$txt_shelf" ;}
				if(str_replace("'","",$cbo_bin)!=0){$sqlCon.= " and c.bin_box=$cbo_bin" ;}
			}
			else if($store_update_upto==5)
			{
				if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
				if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and c.room=$cbo_room" ;}
				if(str_replace("'","",$txt_rack)!=0){$sqlCon.= " and c.rack=$txt_rack" ;}
				if(str_replace("'","",$txt_shelf)!=0){$sqlCon.= " and c.self=$txt_shelf" ;}
			}
			else if($store_update_upto==4)
			{
				if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
				if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and c.room=$cbo_room" ;}
				if(str_replace("'","",$txt_rack)!=0){$sqlCon.= " and c.rack=$txt_rack" ;}
			}
			else if($store_update_upto==3)
			{
				if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
				if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and c.room=$cbo_room" ;}
			}
			else if($store_update_upto==2)
			{
				if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
			}
		}
		
		$sql_trim = sql_select("select sum((case when b.entry_form in(24,73,78,112) and b.trans_type in(1,4,5) then b.quantity else 0 end)-(case when b.entry_form in(25,49,78,112) and b.trans_type in(2,3,6) then b.quantity else 0 end)) as balance, sum((case when b.entry_form in(24,73,78,112) and b.trans_type in(1,4,5)  then b.order_amount else 0 end)-(case when b.entry_form in(25,49,78,112) and b.trans_type in(2,3,6) then b.order_amount else 0 end)) as balance_amt 
		from order_wise_pro_details b, inv_transaction c
		where  b.trans_id=c.id and c.item_category=4 and c.company_id=$cbo_company_id and c.store_id =$cbo_store_name $sqlCon and b.po_breakdown_id =$txt_from_order_id and b.prod_id=$cbo_item_desc  and c.status_active=1  and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		$trim_stock=$sql_trim[0][csf("balance")];
		$trim_stock_amt=$sql_trim[0][csf("balance_amt")];
		$trim_ord_rate=0;
		if($trim_stock_amt!=0 && $trim_stock!=0) $trim_ord_rate=$trim_stock_amt/$trim_stock;
		
		if($conversion_factor>0) $trim_stock=$trim_stock*$conversion_factor; 
		
		
		if(str_replace("'","",$txt_transfer_qnty)>$trim_stock)
		{
			echo "11**Transfer Quantity Not Allow Over Stock.";
			disconnect($con);
			die;
		}

		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later
		
			$id = return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst", $con);
			$new_transfer_system_id = explode("*", return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst",$con,1,$cbo_company_id,$prefix_no,$entry_form_no,date("Y",time()) ));
		
			//$id=return_next_id( "id", "inv_item_transfer_mst", 1 ) ;
			
			$field_array="id, transfer_prefix, transfer_prefix_number, transfer_system_id, company_id, challan_no, transfer_date, transfer_criteria, to_company, location_id, to_location_id,entry_form, from_order_id, to_order_id, from_store_id, to_store_id, item_category, inserted_by, insert_date";
			
			$data_array="(".$id.",'".$new_transfer_system_id[1]."',".$new_transfer_system_id[2].",'".$new_transfer_system_id[0]."',".$cbo_company_id.",".$txt_challan_no.",".$txt_transfer_date.",".$cbo_transfer_criteria.",".$cbo_company_id_to.",".$cbo_location.",".$cbo_location_to.",".$entry_form_no.",".$txt_from_order_id.",".$txt_to_order_id.",".$cbo_store_name.",".$cbo_store_name_to.",".$cbo_item_category.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			// echo "10**insert into inv_item_transfer_mst (".$field_array.") values ".$data_array;die;
			$transfer_recv_num=$new_transfer_system_id[0];
			$transfer_update_id=$id;
		}
		else
		{
			$field_array_update="challan_no*transfer_date*from_order_id*to_order_id*updated_by*update_date";
			$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$txt_from_order_id."*".$txt_to_order_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			$transfer_recv_num=str_replace("'","",$txt_system_id);
			$transfer_update_id=str_replace("'","",$update_id);

			if($variable_auto_rcv == 2) // if auto receive yes(1), then no need to acknowledgement
			{
				//echo "10**fail=2";die;
				$pre_saved_store=sql_select("select a.id, b.from_store, b.to_store from inv_item_transfer_mst a, inv_item_transfer_dtls b where a.id = b.mst_id and a.entry_form in(78,112) and a.id = $transfer_update_id and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 group by a.id, b.from_store, b.to_store");

				if( ($pre_saved_store[0][csf("from_store")]  !=str_replace("'", "", $cbo_store_name)) || ($pre_saved_store[0][csf("to_store")]  !=str_replace("'", "", $cbo_store_name_to)) )
				{
					echo "20**Duplicate From Store and To Store is not allowed in same MRR";
					disconnect($con);
					die;
				}
			}
		}
		
		$amount=str_replace("'","",$txt_transfer_qnty)*str_replace("'","",$txt_rate);
		
		//$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;
		$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);		
		$field_array_trans="id, mst_id,transaction_criteria, company_id, prod_id, item_category, transaction_type, transaction_date, store_id,floor_id,room,rack,self,bin_box, order_id, cons_uom, cons_quantity, cons_rate, cons_amount, inserted_by, insert_date";

		//$id_dtls=return_next_id( "id", "inv_item_transfer_dtls", 1 ) ;
		$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
		$field_array_dtls="id, mst_id, from_prod_id, to_prod_id, from_store,floor_id,room,rack,shelf,bin_box, to_store,to_floor_id,to_room,to_rack,to_shelf,to_bin_box, item_category, item_group, transfer_qnty, rate, transfer_value, uom, remarks, inserted_by, insert_date";

		$field_array_dtls_ac="id, mst_id, dtls_id, is_acknowledge, from_prod_id, to_prod_id, from_store, floor_id, room, rack, shelf, bin_box, to_store, to_floor_id, to_room, to_rack, to_shelf, to_bin_box, item_category, item_group, transfer_qnty, rate, transfer_value, uom, remarks, inserted_by, insert_date";
		
		if(str_replace("'","",$cbo_transfer_criteria)==1) // add from trims transfer page
		{
			$data_prod=sql_select("select supplier_id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id=$cbo_item_desc and status_active=1 and is_deleted=0");
			
			$presentStock=$data_prod[0][csf('current_stock')]-str_replace("'","",$txt_transfer_qnty);
			$presentAvgRate=$data_prod[0][csf('avg_rate_per_unit')];
			$presentStockValue=0;
			if ($presentStock != 0){				
				$presentStockValue=$presentStock*$presentAvgRate;
			}			
			
			$field_array_prodUpdate="avg_rate_per_unit*last_issued_qnty*current_stock*stock_value*updated_by*update_date";
			$data_array_prodUpdate=$presentAvgRate."*".$txt_transfer_qnty."*".$presentStock."*".$presentStockValue."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			$supplier_id=$data_prod[0][csf('supplier_id')];
			$item_desc = return_field_value("product_name_details", "product_details_master", "id=$cbo_item_desc and status_active=1 and is_deleted=0", "product_name_details");  

			$row_prod=sql_select("select id, current_stock, avg_rate_per_unit from product_details_master where company_id=$cbo_company_id_to and item_category_id=$cbo_item_category  and trim(product_name_details)=trim($item_desc) and status_active=1 and is_deleted=0");
			
			if(count($row_prod)>0) // $product_id // Already found Product
			{
				$product_id=$row_prod[0][csf('id')];
				$stock_qnty=$row_prod[0][csf('current_stock')];	
				$avg_rate_per_unit=$row_prod[0][csf('avg_rate_per_unit')];			

				$curr_stock_qnty=$stock_qnty+str_replace("'", '',$txt_transfer_qnty);
				$stock_value=0;
				if ($curr_stock_qnty != 0){					
					$stock_value=$curr_stock_qnty*$avg_rate_per_unit;
				}
				
				if($variable_auto_rcv==1)
				{
					$field_array_prod_update="avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*updated_by*update_date";
					$data_array_prod_update=$avg_rate_per_unit."*".$txt_transfer_qnty."*".$curr_stock_qnty."*".$stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				}
				
			}
			else // Create new product_id here
			{
				//$product_id=return_next_id( "id", "product_details_master", 1 ) ;
				$product_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
				$curr_stock_qnty=str_replace("'","",$txt_transfer_qnty);				
				
				if($variable_auto_rcv==1)
				{
					$avg_rate_per_unit=$data_prod[0][csf('avg_rate_per_unit')];
					$stock_value=0;
					if ($curr_stock_qnty != 0){						
						$stock_value=$curr_stock_qnty*$avg_rate_per_unit;
					}	
					$sql_prod_insert="insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id,sub_group_code,sub_group_name,item_group_id,item_description, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, entry_form, inserted_by, insert_date) 
						select	
					'$product_id', $cbo_company_id_to, supplier_id, item_category_id, detarmination_id,sub_group_code,sub_group_name,item_group_id,item_description, trim(product_name_details), lot, item_code, unit_of_measure, avg_rate_per_unit, $txt_transfer_qnty, $curr_stock_qnty, $stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, entry_form, inserted_by, insert_date from product_details_master where id=$cbo_item_desc";					
					
				}
				else
				{
					$sql_prod_insert="insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id,sub_group_code,sub_group_name,item_group_id,item_description, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, entry_form, inserted_by, insert_date) 
					select	
					'$product_id', $cbo_company_id_to, supplier_id, item_category_id, detarmination_id,sub_group_code,sub_group_name,item_group_id,item_description, trim(product_name_details), lot, item_code, unit_of_measure, avg_rate_per_unit, 0, 0, 0, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, entry_form, inserted_by, insert_date from product_details_master where id=$cbo_item_desc";
				}
			}
			
	         //------------Check Transfer Out Date with last Receive Date-----------------
	        $max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$cbo_item_desc and store_id=$cbo_store_name and transaction_type in (1,4,5) and status_active=1 and is_deleted=0", "max_date");      
	        if($max_recv_date != "")
	        {
	            $max_recv_date = date("Y-m-d", strtotime($max_recv_date));
	            $transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));
	            if ($transfer_date < $max_recv_date) 
	            {
	                echo "35**Transfer Out Date Can not Be Less Than Last Receive Date Of This Lot";
	                disconnect($con);die;
	            }
	        }

	        //------------Check Transfer In Date with last Transaction Date--------------------
	        $max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$product_id and store_id=$cbo_store_name_to and status_active=1 and is_deleted=0", "max_date");      
	        if($max_issue_date != "")
	        {
	            $max_issue_date = date("Y-m-d", strtotime($max_issue_date));
	            $transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));
	            if ($transfer_date < $max_issue_date) 
	            {
	                echo "35**Transfer In Date Can not Be Less Than Last Transaction Date Of This Lot";
	                disconnect($con);die;
	            }
	        }
			
			$data_array_trans="(".$id_trans.",".$transfer_update_id.",".$cbo_transfer_criteria.",".$cbo_company_id.",".$cbo_item_desc.",".$cbo_item_category.",6,".$txt_transfer_date.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$txt_from_order_id.",".$cbo_uom.",".$txt_transfer_qnty.",".$txt_rate.",".$amount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			//$id_trans_recv=$id_trans+1;
			$id_trans_recv=0;
			if($variable_auto_rcv==1)
			{
				$id_trans_recv=return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$data_array_trans.=",(".$id_trans_recv.",".$transfer_update_id.",".$cbo_transfer_criteria.",".$cbo_company_id_to.",".$product_id.",".$cbo_item_category.",5,".$txt_transfer_date.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$txt_to_order_id.",".$cbo_uom.",".$txt_transfer_qnty.",".$txt_rate.",".$amount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}
			
			$data_array_dtls="(".$id_dtls.",".$transfer_update_id.",".$cbo_item_desc.",".$product_id.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_item_category.",".$txt_item_id.",".$txt_transfer_qnty.",".$txt_rate.",'".$amount."',".$cbo_uom.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			
			if($variable_auto_rcv==2) // acknowledgement_dtls_table
			{
				$id_dtls_ac = return_next_id_by_sequence("INV_ITEM_TRANS_DTLS_AC_PK_SEQ", "inv_item_transfer_dtls_ac", $con);
				$data_array_dtls_ac="(".$id_dtls_ac.",".$transfer_update_id.",".$id_dtls.",0,".$cbo_item_desc.",".$product_id.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_item_category.",".$txt_item_id.",".$txt_transfer_qnty.",".$txt_rate.",".$amount.",".$cbo_uom.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}
			
			// Proportion and order wise stock start for company to company transfer==================
			$color_id=return_field_value("color","product_details_master","id=$cbo_item_desc");			
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			
			$order_trans_qnty=(str_replace("'","",$txt_transfer_qnty)/$conversion_factor);
			if($order_trans_qnty=="") $order_trans_qnty=0;
			$order_amount=str_replace("'","",$order_trans_qnty)*$trim_ord_rate;
			
			$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, order_rate, order_amount, inserted_by, insert_date";
			//From order
			$data_array_prop="(".$id_prop.",".$id_trans.",6,".$entry_form_no.",".$id_dtls.",".$txt_from_order_id.",".$cbo_item_desc.",'".$color_id."',".$order_trans_qnty.",'".$trim_ord_rate."','".$order_amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			if($variable_auto_rcv==1)
			{
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				$data_array_prop.=",(".$id_prop.",".$id_trans_recv.",5,".$entry_form_no.",".$id_dtls.",".$txt_to_order_id.",".$cbo_item_desc.",'".$color_id."',".$order_trans_qnty.",'".$trim_ord_rate."','".$order_amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
										
			}

			
		}
		else // order to order and store to store transfer
		{
			//------------Check Transfer Out Date with last Receive Date-----------------
	        $max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$cbo_item_desc and store_id=$cbo_store_name and transaction_type in (1,4,5) and status_active=1 and is_deleted=0", "max_date");      
	        if($max_recv_date != "")
	        {
	            $max_recv_date = date("Y-m-d", strtotime($max_recv_date));
	            $transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));
	            if ($transfer_date < $max_recv_date) 
	            {
	                echo "35**Transfer Out Date Can not Be Less Than Last Receive Date Of This Lot";
	                disconnect($con);die;
	            }
	        }

	        //------------Check Transfer In Date with last Transaction Date--------------------
	        $max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$cbo_item_desc and store_id=$cbo_store_name_to and status_active=1 and is_deleted=0", "max_date");      
	        if($max_issue_date != "")
	        {
	            $max_issue_date = date("Y-m-d", strtotime($max_issue_date));
	            $transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));
	            if ($transfer_date < $max_issue_date) 
	            {
	                echo "35**Transfer In Date Can not Be Less Than Last Transaction Date Of This Lot";
	                disconnect($con);die;
	            }
	        }

			$data_array_trans="(".$id_trans.",".$transfer_update_id.",".$cbo_transfer_criteria.",".$cbo_company_id.",".$cbo_item_desc.",".$cbo_item_category.",6,".$txt_transfer_date.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$txt_from_order_id.",".$cbo_uom.",".$txt_transfer_qnty.",".$txt_rate.",'".$amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
						
			//$id_trans_recv=$id_trans+1;
			$id_trans_recv=0;
			if($variable_auto_rcv==1)
			{
				$id_trans_recv=return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);;
				$data_array_trans.=",(".$id_trans_recv.",".$transfer_update_id.",".$cbo_transfer_criteria.",".$cbo_company_id_to.",".$cbo_item_desc.",".$cbo_item_category.",5,".$txt_transfer_date.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$txt_to_order_id.",".$cbo_uom.",".$txt_transfer_qnty.",".$txt_rate.",'".$amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}
										
			$data_array_dtls="(".$id_dtls.",".$transfer_update_id.",".$cbo_item_desc.",".$cbo_item_desc.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_item_category.",".$txt_item_id.",".$txt_transfer_qnty.",".$txt_rate.",'".$amount."',".$cbo_uom.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
						
			if($variable_auto_rcv==2) // Here ack dtls
			{
				$id_dtls_ac = return_next_id_by_sequence("INV_ITEM_TRANS_DTLS_AC_PK_SEQ", "inv_item_transfer_dtls_ac", $con);
				$data_array_dtls_ac="(".$id_dtls_ac.",".$transfer_update_id.",".$id_dtls.",0,".$cbo_item_desc.",".$cbo_item_desc.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_item_category.",".$txt_item_id.",".$txt_transfer_qnty.",".$txt_rate.",".$amount.",".$cbo_uom.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				//echo "10**insert into inv_item_transfer_dtls_ac (".$field_array_dtls_ac.") values ".$data_array_dtls_ac;die;
			}

			// Proportion and order wise stock start for order to order and store to store
			$color_id=return_field_value("color","product_details_master","id=$cbo_item_desc");			
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, order_rate, order_amount, inserted_by, insert_date";
			$order_trans_qnty=(str_replace("'","",$txt_transfer_qnty)/$conversion_factor);
			if($order_trans_qnty=="") $order_trans_qnty=0;
			$order_amount=str_replace("'","",$order_trans_qnty)*$trim_ord_rate;	
			//From order
			$data_array_prop="(".$id_prop.",".$id_trans.",6,".$entry_form_no.",".$id_dtls.",".$txt_from_order_id.",".$cbo_item_desc.",'".$color_id."',".$order_trans_qnty.",'".$trim_ord_rate."','".$order_amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			if($variable_auto_rcv==1)
			{
				$data_array_prop.=",(".$id_prop.",".$id_trans_recv.",5,".$entry_form_no.",".$id_dtls.",".$txt_to_order_id.",".$cbo_item_desc.",'".$color_id."',".$order_trans_qnty.",'".$trim_ord_rate."','".$order_amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
										
			}
		}
		
		
		$rID=$rID2=$rID3=$rID4=$prodUpdate=$prod=$rID5=true;
		
		if(str_replace("'","",$update_id)=="")
		{
			$rID=sql_insert("inv_item_transfer_mst",$field_array,$data_array,0);
		}
		else
		{
			$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		}
		// echo "insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die;
		$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0); 
		// echo "insert into inv_item_transfer_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		$rID3=sql_insert("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,0); 
		//echo "insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;
		$rID4=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,1);
		
		// new added start
		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			$prodUpdate=sql_update("product_details_master",$field_array_prodUpdate,$data_array_prodUpdate,"id",$cbo_item_desc,1);
			if(count($row_prod)>0)
			{
				if($variable_auto_rcv==1)
				{
					$prod=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
				}
			}
			else
			{
				$prod=execute_query($sql_prod_insert,0);
			}
		}

		if($variable_auto_rcv==2) // inv_item_transfer_dtls_ac
		{
			// echo "10**insert into inv_item_transfer_dtls_ac (".$field_array_dtls_ac.") values ".$data_array_dtls_ac;die;
			$rID5=sql_insert("inv_item_transfer_dtls_ac",$field_array_dtls_ac,$data_array_dtls_ac,0);
		}		
		
		//echo "10**$rID=$rID2=$rID3=$rID4=$prodUpdate=$prod=$rID5";oci_rollback($con);disconnect($con);die;
		
		 
		$txt_from_order_id=str_replace("'","",$txt_from_order_id);
		//echo $flag;die;
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID4 && $prodUpdate && $prod && $rID5)
			{
				mysql_query("COMMIT");  
				echo "0**".$transfer_update_id."**".$transfer_recv_num."**".$txt_from_order_id."**".str_replace("'","",$cbo_store_name);
			}
			else
			{
				mysql_query("ROLLBACK"); 

				echo "5**0**"."&nbsp;"."**".$txt_from_order_id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3 && $rID4 && $prodUpdate && $prod && $rID5)
			{
				oci_commit($con);   
				echo "0**".$transfer_update_id."**".$transfer_recv_num."**".$txt_from_order_id."**".str_replace("'","",$cbo_store_name);
			}
			else
			{
				oci_rollback($con);
				echo "5**0**"."&nbsp;"."**".$txt_from_order_id;
			}
		}
		
		disconnect($con);
		die;
	}
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();		
		if($db_type==0)	{ mysql_query("BEGIN"); }

		$variable_auto_rcv = return_field_value("auto_transfer_rcv", "variable_settings_inventory", " company_name=$cbo_company_id and item_category_id=4 and status_active=1 and variable_list= 27", "auto_transfer_rcv");

		if($variable_auto_rcv == "")
		{
			$variable_auto_rcv = 1; // if auto receive yes(1), then no need to acknowledgement
		}
		
		if ($variable_auto_rcv == 1) 
		{
			$all_trans_id=$update_trans_issue_id.",".$update_trans_recv_id;
		}
		else{
			$all_trans_id=$update_trans_issue_id;
		}
		
		
		$sqlCon="";	
		$store_update_upto=str_replace("'","",$store_update_upto);
		if($store_update_upto > 1)
		{
			if($store_update_upto==6)
			{
				if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
				if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and c.room=$cbo_room" ;}
				if(str_replace("'","",$txt_rack)!=0){$sqlCon.= " and c.rack=$txt_rack" ;}
				if(str_replace("'","",$txt_shelf)!=0){$sqlCon.= " and c.self=$txt_shelf" ;}
				if(str_replace("'","",$cbo_bin)!=0){$sqlCon.= " and c.bin_box=$cbo_bin" ;}
			}
			else if($store_update_upto==5)
			{
				if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
				if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and c.room=$cbo_room" ;}
				if(str_replace("'","",$txt_rack)!=0){$sqlCon.= " and c.rack=$txt_rack" ;}
				if(str_replace("'","",$txt_shelf)!=0){$sqlCon.= " and c.self=$txt_shelf" ;}
			}
			else if($store_update_upto==4)
			{
				if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
				if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and c.room=$cbo_room" ;}
				if(str_replace("'","",$txt_rack)!=0){$sqlCon.= " and c.rack=$txt_rack" ;}
			}
			else if($store_update_upto==3)
			{
				if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
				if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and c.room=$cbo_room" ;}
			}
			else if($store_update_upto==2)
			{
				if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
			}
		}

		$sql_trim = sql_select("select sum((case when b.entry_form in(24,73,78,112) and b.trans_type in(1,4,5) then b.quantity else 0 end)-(case when b.entry_form in(25,49,78,112) and b.trans_type in(2,3,6) then b.quantity else 0 end)) as balance, sum((case when b.entry_form in(24,73,78,112) and b.trans_type in(1,4,5) then b.order_amount else 0 end)-(case when b.entry_form in(25,49,78,112) and b.trans_type in(2,3,6) then b.order_amount else 0 end)) as balance_amt 
		from order_wise_pro_details b, inv_transaction c
		where  b.trans_id=c.id and c.item_category=4 and c.company_id=$cbo_company_id and c.store_id =$cbo_store_name $sqlCon and b.po_breakdown_id =$txt_from_order_id and b.prod_id=$cbo_item_desc  and c.status_active=1 and c.id not in($all_trans_id) and  b.trans_id not in($all_trans_id) and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		$trim_stock=$sql_trim[0][csf("balance")];
		$trim_stock_amt=$sql_trim[0][csf("balance_amt")];
		$trim_ord_rate=0;
		if($trim_stock_amt!=0 && $trim_stock!=0) $trim_ord_rate=$trim_stock_amt/$trim_stock;
		if($conversion_factor>0) $trim_stock=$trim_stock*$conversion_factor; 		
		// echo "11**$trim_stock*$conversion_factor";die;
		if(str_replace("'","",$txt_transfer_qnty)>$trim_stock)
		{
			echo "11**Transfer Quantity Not Allow Over Stock.";
			disconnect($con);
			die;
		}
		
		$field_array_update="challan_no*transfer_date*from_order_id*to_order_id*updated_by*update_date";
		$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$txt_from_order_id."*".$txt_to_order_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		/*$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0;*/
		
		$field_array_trans="prod_id*transaction_date*order_id*cons_uom*cons_quantity*cons_rate*cons_amount*store_id*floor_id*room*rack*self*bin_box*updated_by*update_date";

		$field_array_dtls="from_prod_id*to_prod_id*item_group*to_store*to_floor_id*to_room*to_rack*to_shelf*to_bin_box*transfer_qnty*rate*transfer_value*uom*remarks*updated_by*update_date";

		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			$updateProdID_array=array();
			// $field_array_adjust="current_stock*avg_rate_per_unit*stock_value";
			$field_array_adjust="current_stock*avg_rate_per_unit*stock_value*updated_by*update_date";
			
			$stock_from=sql_select("select current_stock, avg_rate_per_unit from product_details_master where id=$previous_from_prod_id and status_active=1 and is_deleted=0");
			$adjust_curr_stock_from=$stock_from[0][csf('current_stock')]+str_replace("'", '',$hidden_transfer_qnty);
			$cur_st_rate_from=$stock_from[0][csf('avg_rate_per_unit')];
			$cur_st_value_from=0;
			if ($adjust_curr_stock_from != 0){				
				$cur_st_value_from=$adjust_curr_stock_from*$cur_st_rate_from;
			}			
			
			$previous_from_prod_id=str_replace("'","",$previous_from_prod_id);
			$updateProdID_array[]=$previous_from_prod_id; 
			
			//$data_array_adjust[$previous_from_prod_id]=explode("*",("".$adjust_curr_stock_from."*".$cur_st_rate_from."*".$cur_st_value_from));
			$data_array_adjust[$previous_from_prod_id]=explode("*",("".$adjust_curr_stock_from."*".$cur_st_rate_from."*".$cur_st_value_from."*".$user_id."*'".$pc_date_time."'"));
			
			$stock_to=sql_select("select current_stock, avg_rate_per_unit from product_details_master where id=$previous_to_prod_id and status_active=1 and is_deleted=0");
			$adjust_curr_stock_to=$stock_to[0][csf('current_stock')]-str_replace("'", '',$hidden_transfer_qnty);

			if($variable_auto_rcv == 1 ) // if auto receive Yes(1), then no need to acknowledgement
			{
				$cur_st_rate_to=$stock_to[0][csf('avg_rate_per_unit')];
				$cur_st_value_to=0;
				if ($adjust_curr_stock_to != 0){					
					$cur_st_value_to=$adjust_curr_stock_to*$cur_st_rate_to;	
				}
						
				$previous_to_prod_id=str_replace("'","",$previous_to_prod_id);
				$updateProdID_array[]=$previous_to_prod_id; 			
				$data_array_adjust[$previous_to_prod_id]=explode("*",("".$adjust_curr_stock_to."*".$cur_st_rate_to."*".$cur_st_value_to."*".$user_id."*'".$pc_date_time."'"));
			}
			
			$data_prod=sql_select("select supplier_id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id=$cbo_item_desc and status_active=1 and is_deleted=0");
			
			$presentStock=$data_prod[0][csf('current_stock')]-str_replace("'","",$txt_transfer_qnty);
			$presentAvgRate=$data_prod[0][csf('avg_rate_per_unit')];
			$presentStockValue=0;
			if ($presentStock != 0){				
				$presentStockValue=$presentStock*$presentAvgRate;
			}			
			
			$field_array_prodUpdate="avg_rate_per_unit*last_issued_qnty*current_stock*stock_value*updated_by*update_date";
			$data_array_prodUpdate=$presentAvgRate."*".$txt_transfer_qnty."*".$presentStock."*".$presentStockValue."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			
			$supplier_id=$data_prod[0][csf('supplier_id')];
			$item_desc = return_field_value("product_name_details", "product_details_master", "id=$cbo_item_desc and status_active=1 and is_deleted=0", "product_name_details");  
			$row_prod=sql_select("SELECT id, current_stock, avg_rate_per_unit from product_details_master where company_id=$cbo_company_id_to and item_category_id=$cbo_item_category  and product_name_details=trim($item_desc) and status_active=1 and is_deleted=0");
		
			if(count($row_prod)>0) // Found previous product
			{
				$product_id=$row_prod[0][csf('id')];
				$stock_qnty=$row_prod[0][csf('current_stock')];				
	
				$curr_stock_qnty=$stock_qnty+str_replace("'", '',$txt_transfer_qnty);
				$avg_rate_per_unit=$row_prod[0][csf('avg_rate_per_unit')];
				$stock_value=0;
				if ($curr_stock_qnty != 0){					
					$stock_value=$curr_stock_qnty*$avg_rate_per_unit;
				}				
				
				if($variable_auto_rcv == 1 )
				{
					$field_array_prod_update="avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*updated_by*update_date";
					$data_array_prod_update=$avg_rate_per_unit."*".$txt_transfer_qnty."*".$curr_stock_qnty."*".$stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				}
			}
			else  // Create new product
			{
				//$product_id=return_next_id( "id", "product_details_master", 1 ) ;
				$product_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
				$curr_stock_qnty=str_replace("'","",$txt_transfer_qnty);				
				
				if($variable_auto_rcv == 1 ) // if auto receive Yes(1), then no need to acknowledgement
				{
					$avg_rate_per_unit=$data_prod[0][csf('avg_rate_per_unit')];
					$stock_value=0;
					if ($curr_stock_qnty != 0){						
						$stock_value=$curr_stock_qnty*$avg_rate_per_unit;
					}	
					$sql_prod_insert="insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id,sub_group_code,sub_group_name,item_group_id,item_description, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, inserted_by, insert_date) 
						select	
					'$product_id', $cbo_company_id_to, supplier_id, item_category_id, detarmination_id,sub_group_code,sub_group_name,item_group_id,item_description, trim(product_name_details), lot, item_code, unit_of_measure, avg_rate_per_unit, $txt_transfer_qnty, $curr_stock_qnty, $stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, inserted_by, insert_date from product_details_master where id=$cbo_item_desc";					
					
				}
				else
				{
					$sql_prod_insert="insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id,sub_group_code,sub_group_name,item_group_id,item_description, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, inserted_by, insert_date) 
					select	
					'$product_id', $cbo_company_id_to, supplier_id, item_category_id, detarmination_id,sub_group_code,sub_group_name,item_group_id,item_description, trim(product_name_details), lot, item_code, unit_of_measure, avg_rate_per_unit, 0, 0, 0, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, inserted_by, insert_date from product_details_master where id=$cbo_item_desc";
				}
				
			}
			
			 //------------Check Transfer Out Date with last Receive Date-----------------
            $max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$cbo_item_desc and store_id=$cbo_store_name and transaction_type in (1,4,5) and id <> $update_trans_recv_id and status_active=1 and is_deleted=0", "max_date");      
            if($max_recv_date != "")
            {
                $max_recv_date = date("Y-m-d", strtotime($max_recv_date));
                $transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));
                if ($transfer_date < $max_recv_date) 
                {
                    echo "35**Transfer Out Date Can not Be Less Than Last Receive Date Of This Lot";
                    disconnect($con);die;
                }
            }

            //------------Check Transfer In Date with last Transaction Date--------------------
            $max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$product_id and store_id=$cbo_store_name_to and id not in ($update_trans_issue_id,$update_trans_recv_id) and status_active=1 and is_deleted=0", "max_date");      
            if($max_issue_date != "")
            {
                $max_issue_date = date("Y-m-d", strtotime($max_issue_date));
                $transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));
                if ($transfer_date < $max_issue_date) 
                {
                    echo "35**Transfer In Date Can not Be Less Than Last Transaction Date Of This Lot";
                    disconnect($con);die;
                }
            }
            			
			$update_trans_issue_id=str_replace("'","",$update_trans_issue_id);
			$update_trans_recv_id=str_replace("'","",$update_trans_recv_id); 

			$amount=str_replace("'","",$txt_transfer_qnty)*str_replace("'","",$txt_rate);

			$updateTransID_array[]=$update_trans_issue_id; 
			$updateTransID_data[$update_trans_issue_id]=explode("*",("".$cbo_item_desc."*".$txt_transfer_date."*".$txt_from_order_id."*".$cbo_uom."*".$txt_transfer_qnty."*".$txt_rate."*'".$amount."'*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_bin."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

			if($variable_auto_rcv == 1 )
			{
				$updateTransID_array[]=$update_trans_recv_id; 
				$updateTransID_data[$update_trans_recv_id]=explode("*",("".$product_id."*".$txt_transfer_date."*".$txt_to_order_id."*".$cbo_uom."*".$txt_transfer_qnty."*".$txt_rate."*'".$amount."'*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$cbo_bin_to."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
			// print_r($updateTransID_data);die;	

			$data_array_dtls=$cbo_item_desc."*".$product_id."*".$txt_item_id."*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$cbo_bin_to."*".$txt_transfer_qnty."*".$txt_rate."*'".$amount."'*".$cbo_uom."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			


			// =================================== Start =========================================
			// Proportion and order stock table update start Company to company criteria
			$color_id=return_field_value("color","product_details_master","id=$cbo_item_desc");
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, order_rate, order_amount, inserted_by, insert_date";
			$order_trans_qnty=(str_replace("'","",$txt_transfer_qnty)/$conversion_factor);
			if($order_trans_qnty=="") $order_trans_qnty=0;
			$order_amount=str_replace("'","",$order_trans_qnty)*$trim_ord_rate;
			
			// From order update start
			$data_array_prop="(".$id_prop.",".$update_trans_issue_id.",6,".$entry_form_no.",".$update_dtls_id.",".$txt_from_order_id.",".$cbo_item_desc.",'".$color_id."',".$order_trans_qnty.",'".$trim_ord_rate."','".$order_amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			// to order update start
			if($variable_auto_rcv == 1 ) // if auto receive Yes(1), then no need to acknowledgement
			{
				$id_prop=return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				$data_array_prop.=",(".$id_prop.",".$update_trans_recv_id.",5,".$entry_form_no.",".$update_dtls_id.",".$txt_to_order_id.",".$product_id.",'".$color_id."',".$order_trans_qnty.",'".$trim_ord_rate."','".$order_amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			} // to order update end

			// =================================== End =========================================
		}
		else // order to order and store to store update here
		{
			//------------Check Transfer Out Date with last Receive Date-----------------
            $max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$cbo_item_desc and store_id=$cbo_store_name and transaction_type in (1,4,5) and id <> $update_trans_recv_id and status_active=1 and is_deleted=0", "max_date");      
            if($max_recv_date != "")
            {
                $max_recv_date = date("Y-m-d", strtotime($max_recv_date));
                $transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));
                if ($transfer_date < $max_recv_date) 
                {
                    echo "35**Transfer Out Date Can not Be Less Than Last Receive Date Of This Lot";
                    disconnect($con);die;
                }
            }

            //------------Check Transfer In Date with last Transaction Date--------------------
            $max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$cbo_item_desc and store_id=$cbo_store_name_to and id not in ($update_trans_issue_id,$update_trans_recv_id) and status_active=1 and is_deleted=0", "max_date");      
            if($max_issue_date != "")
            {
                $max_issue_date = date("Y-m-d", strtotime($max_issue_date));
                $transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));
                if ($transfer_date < $max_issue_date) 
                {
                    echo "35**Transfer In Date Can not Be Less Than Last Transaction Date Of This Lot";
                    disconnect($con);die;
                }
            }

			$updateTransID_array=array();
			$update_trans_issue_id=str_replace("'","",$update_trans_issue_id);
			$update_trans_recv_id=str_replace("'","",$update_trans_recv_id); 
			 
			$amount=str_replace("'","",$txt_transfer_qnty)*str_replace("'","",$txt_rate);
			
			$updateTransID_array[]=$update_trans_issue_id; 
			$updateTransID_data[$update_trans_issue_id]=explode("*",("".$cbo_item_desc."*".$txt_transfer_date."*".$txt_from_order_id."*".$cbo_uom."*".$txt_transfer_qnty."*".$txt_rate."*'".$amount."'*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_bin."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			
			if($variable_auto_rcv == 1 ) // if auto receive Yes(1), then no need to acknowledgement
			{
				$updateTransID_array[]=$update_trans_recv_id; 
				$updateTransID_data[$update_trans_recv_id]=explode("*",("".$cbo_item_desc."*".$txt_transfer_date."*".$txt_to_order_id."*".$cbo_uom."*".$txt_transfer_qnty."*".$txt_rate."*'".$amount."'*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$cbo_bin_to."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}		
			//print_r($updateTransID_data);die;			

			$data_array_dtls=$cbo_item_desc."*".$cbo_item_desc."*".$txt_item_id."*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$cbo_bin_to."*".$txt_transfer_qnty."*".$txt_rate."*'".$amount."'*".$cbo_uom."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			//print_r($data_array_dtls);


			// =================================== Start =========================================
			// Here for order to order and store to store Transfer Criteria -- update Start====================
			// Proportion and order stock table update start
			$color_id=return_field_value("color","product_details_master","id=$cbo_item_desc");
			//$id_prop = return_next_id( "id", "order_wise_pro_details", 1 );
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, order_rate, order_amount, inserted_by, insert_date";
			
			$order_trans_qnty=(str_replace("'","",$txt_transfer_qnty)/$conversion_factor);
			if($order_trans_qnty=="") $order_trans_qnty=0;
			$order_amount=str_replace("'","",$order_trans_qnty)*$trim_ord_rate;
			
			// From order update start
			$data_array_prop="(".$id_prop.",".$update_trans_issue_id.",6,".$entry_form_no.",".$update_dtls_id.",".$txt_from_order_id.",".$cbo_item_desc.",'".$color_id."',".$order_trans_qnty.",'".$trim_ord_rate."','".$order_amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			if($variable_auto_rcv == 1 ) // if auto receive Yes(1), then no need to acknowledgement
			{
				$id_prop=return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				$data_array_prop.=",(".$id_prop.",".$update_trans_recv_id.",5,".$entry_form_no.",".$update_dtls_id.",".$txt_to_order_id.",".$cbo_item_desc.",'".$color_id."',".$order_trans_qnty.",'".$trim_ord_rate."','".$order_amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
			}
			//-----------------------------------LIFO/FIFO End-----------------------------------\\	
		}		
		
		$rID=$rID2=$rID3=$query=$prodUpdate_adjust=$prodUpdate=$prod=$rID4=$rID5=true;

		$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		//echo "10**".bulk_update_sql_statement("inv_transaction","id",$field_array_trans,$updateTransID_data,$updateTransID_array);die;
		$rID2=execute_query(bulk_update_sql_statement("inv_transaction","id",$field_array_trans,$updateTransID_data,$updateTransID_array));
		$rID3=sql_update("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,"id",$update_dtls_id,1);
		$query = execute_query("DELETE FROM order_wise_pro_details WHERE dtls_id=$update_dtls_id and entry_form=$entry_form_no");
		//echo "insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;

		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			$prodUpdate_adjust=execute_query(bulk_update_sql_statement("product_details_master","id",$field_array_adjust,$data_array_adjust,$updateProdID_array));
			$prodUpdate=sql_update("product_details_master",$field_array_prodUpdate,$data_array_prodUpdate,"id",$cbo_item_desc,1);
			if(count($row_prod)>0)
			{
				$prod=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
			}
			else
			{
				$prod=execute_query($sql_prod_insert,0);
			}				
		}

		if($variable_auto_rcv==2) //acknowledgement details table update, 
		{
			$rID5=sql_update("inv_item_transfer_dtls_ac",$field_array_dtls,$data_array_dtls,"dtls_id",$update_dtls_id,1);
		}

		$rID4=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,1);
		
		//echo "10**$rID=$rID2=$rID3=$query=$prodUpdate_adjust=$prodUpdate=$prod=$rID4=$rID5";oci_rollback($con);die;
		 
		$txt_from_order_id=str_replace("'","",$txt_from_order_id);
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $query && $prodUpdate_adjust && $prodUpdate && $prod && $rID4 && $rID5)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id)."**".$txt_from_order_id."**".str_replace("'","",$cbo_store_name);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**0**"."&nbsp;"."**1"."**".$txt_from_order_id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3 && $query && $prodUpdate_adjust && $prodUpdate && $prod && $rID4 && $rID5)
			{
				oci_commit($con);   
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id)."**".$txt_from_order_id."**".str_replace("'","",$cbo_store_name);
			}
			else
			{
				oci_rollback($con);
				echo "6**0**"."&nbsp;"."**1";
			}
		}	
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
		
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		$update_id=str_replace("'","",$update_id);
		$previous_prod_id=str_replace("'","",$cbo_item_desc);
		$update_dtls_id=str_replace("'","",$update_dtls_id);
		$update_trans_issue_id=str_replace("'","",$update_trans_issue_id);
		$update_trans_recv_id=str_replace("'","",$update_trans_recv_id);
		$all_trans_id=$update_trans_issue_id.",".$update_trans_recv_id;
		$txt_to_order_id=str_replace("'","",$txt_to_order_id);
		
		//echo "10**$update_id=$previous_prod_id=$update_dtls_id=$update_trans_issue_id=$update_trans_recv_id";die;
		if($update_id>0 && $previous_prod_id>0 && $update_dtls_id>0 && $update_trans_issue_id>0 && $update_trans_recv_id>0)
		{
			
			$store_stock=sql_select("select sum((case when b.trans_type in(1,4,5) then b.quantity else 0 end)-(case when b.trans_type in(2,3,6) then b.quantity else 0 end)) as store_stock_qnty 
			from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.prod_id=$previous_prod_id and a.store_id=$cbo_store_name_to and b.po_breakdown_id=$txt_to_order_id and b.entry_form in(24,25,49,73,78,112) and a.status_active=1 and b.status_active=1");
			$store_stock_qnty=$store_stock[0][csf("store_stock_qnty")];
			
			if($store_stock_qnty <= 0)
			{
				echo "20**Order Store Wise Stock Less Then Zero, \n Please Delete Next Issue Or Receive Return, \n More Information Please See Order Wise Trims Receive Issue And Stock.";
				disconnect($con);die;
			}
			
			
			$row_propotionate=sql_select( "select id, po_breakdown_id, quantity, order_rate, order_amount, trans_type
			from order_wise_pro_details where trans_id in($all_trans_id) and status_active=1 and is_deleted=0" );
			$propotionate_data=array();
			foreach($row_propotionate as $row)
			{
				$all_order_id.=$row[csf("po_breakdown_id")].",";
				if($row[csf("trans_type")]==6)
				{
					$propotionate_data[$row[csf("po_breakdown_id")]]["quantity_issue"]+=$row[csf("quantity")];
					$propotionate_data[$row[csf("po_breakdown_id")]]["order_amount_issue"]+=$row[csf("order_amount")];
				}
				else
				{
					$propotionate_data[$row[csf("po_breakdown_id")]]["quantity_rcv"]+=$row[csf("quantity")];
					$propotionate_data[$row[csf("po_breakdown_id")]]["order_amount_rcv"]+=$row[csf("order_amount")];
				}
				
			}
			$all_order_id=chop($all_order_id,",");
			$field_array_prod_ord_update="avg_rate*stock_quantity*stock_amount*updated_by*update_date";
			if($all_order_id!="")
			{
				$prod_order_stock=sql_select("select id, po_breakdown_id, stock_quantity, stock_amount 
				from order_wise_stock where prod_id=$previous_prod_id and po_breakdown_id in($all_order_id) and status_active=1 and is_deleted=0 ");
				foreach($prod_order_stock as $row)
				{
					
					if($propotionate_data[$row[csf("po_breakdown_id")]]["quantity_issue"]>0)
					{
						$current_stock_qnty=$row[csf('stock_quantity')]+$propotionate_data[$row[csf("po_breakdown_id")]]["quantity_issue"];
						$current_stock_value=$row[csf('stock_amount')]+$propotionate_data[$row[csf("po_breakdown_id")]]["order_amount_issue"];
					}
					if($propotionate_data[$row[csf("po_breakdown_id")]]["quantity_rcv"]>0)
					{
						$current_stock_qnty=$row[csf('stock_quantity')]-$propotionate_data[$row[csf("po_breakdown_id")]]["quantity_rcv"];
						$current_stock_value=$row[csf('stock_amount')]-$propotionate_data[$row[csf("po_breakdown_id")]]["order_amount_rcv"];
					}
					
					if($current_stock_value>0 && $current_stock_qnty>0)
					{
						$current_avg_rate=number_format($current_stock_value/$current_stock_qnty,$dec_place[3],'.','');
					}
					else
					{
						$current_avg_rate=0;
					}
					
					
					$ord_prod_id_arr[]=$row[csf('id')];
					$data_array_prod_ord_update[$row[csf('id')]]=explode("*",("".$current_avg_rate."*".$current_stock_qnty."*".$current_stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				}
			}
			
			
			$field_arr="status_active*is_deleted*updated_by*update_date";
			$data_arr="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$rID=$rID2=$rID3=$ordProdUpdate=true;
			
			if(count($ord_prod_id_arr)>0)
			{
				$ordProdUpdate=execute_query(bulk_update_sql_statement("order_wise_stock","id",$field_array_prod_ord_update,$data_array_prod_ord_update,$ord_prod_id_arr));
			}
			$rID=execute_query("update inv_transaction set status_active=0, is_deleted=1, updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where id in($all_trans_id)");
			$rID2=sql_update("inv_item_transfer_dtls",$field_arr,$data_arr,"id",$update_dtls_id,1);
			$rID3=execute_query("update order_wise_pro_details set status_active=0, is_deleted=1, updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where trans_id in($all_trans_id)");
			
			
			//echo "10** $rID && $rID2 && $rID3 && $ordProdUpdate";oci_rollback($con);disconnect($con);die;
			if($db_type==0)
			{
				if($rID && $rID2 && $rID3 && $ordProdUpdate)
				{
					mysql_query("COMMIT");  
					echo "2**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id)."**0";
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "7**0**0**1";
				}
			}
	
			if($db_type==2 || $db_type==1 )
			{
				if($rID && $rID2 && $rID3 && $ordProdUpdate)
				{
					oci_commit($con);  
					echo "2**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id)."**0";
				}
				else
				{
					oci_rollback($con);
					echo "7**0**0**1";
				}
			}
			//check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);
			die;
		}
	}
}

if ($action=="trims_store_order_to_order_transfer_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	$sql="select id, transfer_system_id, transfer_date, challan_no, from_order_id, to_order_id, item_category, transfer_criteria, to_company, from_store_id, to_store_id
	from inv_item_transfer_mst where id='$data[1]' and company_id='$data[0]'";
	$dataArray=sql_select($sql);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$store_name_arr = return_library_array("select a.id, a.store_name from lib_store_location a", 'id', 'store_name');
	
	$transfer_criteria = $dataArray[0][csf('transfer_criteria')];
	if( $transfer_criteria == 1 )
	{
		// echo $data[3];
		$to_po_array=array();
		$sql_po=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, b.po_number, b.po_quantity, b.pub_shipment_date, b.id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$data[3]'");
		foreach($sql_po as $row_po)
		{
			$to_po_array[$row_po[csf('id')]]['no']=$row_po[csf('po_number')];
			$to_po_array[$row_po[csf('id')]]['job']=$row_po[csf('job_no')];
			$to_po_array[$row_po[csf('id')]]['buyer']=$row_po[csf('buyer_name')];
			$to_po_array[$row_po[csf('id')]]['qnty']=$row_po[csf('po_quantity')];
			$to_po_array[$row_po[csf('id')]]['date']=$row_po[csf('pub_shipment_date')];
			$to_po_array[$row_po[csf('id')]]['style']=$row_po[csf('style_ref_no')];
		}
	}
	$po_array=array();
	$sql_po=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, b.po_number, b.po_quantity, b.pub_shipment_date, b.id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$data[0]'");
	foreach($sql_po as $row_po)
	{
		$po_array[$row_po[csf('id')]]['no']=$row_po[csf('po_number')];
		$po_array[$row_po[csf('id')]]['job']=$row_po[csf('job_no')];
		$po_array[$row_po[csf('id')]]['buyer']=$row_po[csf('buyer_name')];
		$po_array[$row_po[csf('id')]]['qnty']=$row_po[csf('po_quantity')];
		$po_array[$row_po[csf('id')]]['date']=$row_po[csf('pub_shipment_date')];
		$po_array[$row_po[csf('id')]]['style']=$row_po[csf('style_ref_no')];
	}
	
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=4 and status_active=1 and is_deleted=0","id","product_name_details");
	?>
	<div style="width:930px;">
	    <table width="900" cellspacing="0" align="right">
	        <tr>
	            <td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	        	<td colspan="6" align="center" style="font-size:14px">  
					<?
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


					}
	                ?> 
	            </td>  
	        </tr>
	        <tr>
	            <td colspan="6" align="center" style="font-size:x-large"><strong><u><? echo $data[2]; ?> Report</u></strong></td>
	        </tr>
	        <tr>
	        	<td width="125"><strong>Transfer ID :</strong></td><td width="200"><? echo $dataArray[0][csf('transfer_system_id')]; ?></td>
	            <td width="125"><strong>Transfer Date:</strong></td><td width="185"><? echo change_date_format($dataArray[0][csf('transfer_date')]); ?></td>
	            <td width="125"><strong>Challan No.:</strong></td><td width="140"><? echo $dataArray[0][csf('challan_no')]; ?></td>
	        </tr>
	        <tr>
	            <td><strong>From Company:</strong></td> <td width="200"><? echo $company_library[$data[0]]; ?></td>
	            <td><strong>From order No:</strong></td> <td width="185"><? echo $po_array[$dataArray[0][csf('from_order_id')]]['no']; ?></td>
	            <td><strong>From ord Qnty:</strong></td> <td width="140"><? echo $po_array[$dataArray[0][csf('from_order_id')]]['qnty']; ?></td>
	        </tr>
	        <tr>	            
	            <td><strong>From ord Buyer:</strong></td> <td width="200"><? echo $buyer_library[$po_array[$dataArray[0][csf('from_order_id')]]['buyer']]; ?></td>
	            <td><strong>From Style Ref.:</strong></td> <td width="185"><? echo $po_array[$dataArray[0][csf('from_order_id')]]['style']; ?></td>
	            <td><strong>From Job No:</strong></td> <td width="140"><? echo $po_array[$dataArray[0][csf('from_order_id')]]['job']; ?></td>

	        </tr>
	        <tr>	            
	            <td><strong>From Ship. Date:</strong></td> <td width="200"><? echo change_date_format($po_array[$dataArray[0][csf('from_order_id')]]['date']); ?></td>
	            <td><strong>From Store:</strong></td><td width="325" colspan="2"><? echo $store_name_arr[$dataArray[0][csf('from_store_id')]]; ?></td>
	        </tr>
	        <tr>
	            <td><strong>To Company:</strong></td> <td width="200"><? echo $company_library[$data[3]]; ?></td>
	            <td><strong>To order No:</strong></td> <td width="185"><? echo ($transfer_criteria == 1) ? $to_po_array[$dataArray[0][csf('to_order_id')]]['no'] : $po_array[$dataArray[0][csf('to_order_id')]]['no']; // echo $po_array[$dataArray[0][csf('to_order_id')]]['no']; ?></td>
	            <td><strong>To ord Qnty:</strong></td> <td width="140"><? echo ($transfer_criteria == 1) ? $to_po_array[$dataArray[0][csf('to_order_id')]]['qnty'] : $po_array[$dataArray[0][csf('to_order_id')]]['qnty'] ; //echo $po_array[$dataArray[0][csf('to_order_id')]]['qnty']; ?></td>
	        </tr>
	        <tr>	            
	            <td><strong>To ord Buyer:</strong></td> <td width="200"><? echo ($transfer_criteria == 1) ? $buyer_library[$to_po_array[$dataArray[0][csf('to_order_id')]]['buyer']] : $buyer_library[$po_array[$dataArray[0][csf('to_order_id')]]['buyer']] ; //echo $buyer_library[$po_array[$dataArray[0][csf('to_order_id')]]['buyer']]; ?></td>
	            <td><strong>To Style Ref.:</strong></td> <td width="185"><? echo ($transfer_criteria == 1) ? $to_po_array[$dataArray[0][csf('to_order_id')]]['style'] : $po_array[$dataArray[0][csf('to_order_id')]]['style'] ; //echo $po_array[$dataArray[0][csf('to_order_id')]]['style']; ?></td>
	            <td><strong>To Job No:</strong></td> <td width="140"><? echo ($transfer_criteria == 1) ? $to_po_array[$dataArray[0][csf('to_order_id')]]['job'] : $po_array[$dataArray[0][csf('to_order_id')]]['job'] ; //echo $po_array[$dataArray[0][csf('to_order_id')]]['job']; ?></td>
	        </tr>
	        <tr>	            
	            <td><strong>To Ship. Date:</strong></td> <td width="200"><? echo ($transfer_criteria == 1) ? change_date_format($to_po_array[$dataArray[0][csf('to_order_id')]]['date']) : change_date_format($po_array[$dataArray[0][csf('to_order_id')]]['date']) ; //echo change_date_format($po_array[$dataArray[0][csf('to_order_id')]]['date']); ?></td>
	            <td><strong>To Store:</strong></td><td width="325" colspan="2"><? echo $store_name_arr[$dataArray[0][csf('to_store_id')]]; ?>
	        </tr>
	    </table>
	    <br>
	    <div style="width:100%;">
		    <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" style="margin-top: 20px;">
		        <thead bgcolor="#dddddd" align="center">
		            <th width="30">SL</th>
		            <th width="120" >Item Category</th>
		            <th width="200" >Item Description</th>
                    <th width="120" >Item Color</th>
                    <th width="120" >Item Size</th>
		            <th width="70" >UOM</th>
		            <th width="100" >Transfered Qnty</th>
		        </thead>
		        <tbody> 
		   
					<?
					$sql_dtls="select a.id, a.item_category, a.item_group, a.from_prod_id, b.PRODUCT_NAME_DETAILS, c.COLOR_NAME, d.SIZE_NAME,  a.transfer_qnty, a.uom from inv_item_transfer_dtls a left join product_details_master b on b.id = a.FROM_PROD_ID left join lib_color c on c.id = b.ITEM_COLOR left join lib_size d on d.id = b.GMTS_SIZE where a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0";
//					echo $sql_dtls;
                    $sql_result= sql_select($sql_dtls);
					$i=1;
					foreach($sql_result as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						$transfer_qnty=$row[csf('transfer_qnty')];
						$transfer_qnty_sum += $transfer_qnty;
						
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
			                <td align="center"><? echo $i; ?></td>
			                <td><? echo $item_category[$row[csf("item_category")]]; ?></td>
			                <td><? echo $row[csf("product_name_details")]; ?></td>
                            <td><? echo $row[csf("color_name")]; ?></td>
                            <td><? echo $row[csf("size_name")]; ?></td>
			                <td align="center"><? echo $unit_of_measurement[$row[csf("uom")]]; ?></td>
			                <td align="right"><? echo number_format($row[csf("transfer_qnty")],2); ?></td>
						</tr>
						<? $i++; 
					} ?>
			    </tbody>
		        <tfoot>
		            <tr>
		                <td colspan="6" align="right"><strong>Total :</strong></td>
		                <td align="right"><?php echo number_format($transfer_qnty_sum,2); ?></td>
		            </tr>                           
		        </tfoot>
		    </table>
	        <br>
			 <?
	            echo signature_table(266, $data[0], "900px");
	         ?>
	    </div>
	</div>   
 	<?
 	exit();	
}

if ($action=="trims_store_order_to_order_transfer_print2")
{
	extract($_REQUEST);
	$data=explode('*',$data);	
	$cbo_company_id = $data[0];
	$mst_id = $data[1];
	$cbo_company_id_to = $data[3];

	$sql="select ID, TRANSFER_SYSTEM_ID, TRANSFER_DATE, CHALLAN_NO, FROM_ORDER_ID, TO_ORDER_ID, ITEM_CATEGORY, TRANSFER_CRITERIA, TO_COMPANY, FROM_STORE_ID, TO_STORE_ID
	from inv_item_transfer_mst where id='$mst_id' and company_id='$cbo_company_id'";
	$dataArray=sql_select($sql);

	$company_library=return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_library=return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$store_library=return_library_array("select id, store_name from  lib_store_location", "id", "store_name");
	$country_arr=return_library_array("select id, country_name from  lib_country", "id", "country_name");
	$item_group_arr = return_library_array("select id, item_name from lib_item_group", "id", "item_name");
	?>
	<style type="text/css">
		hr{margin: 0px;}
		.wrd_brk{word-break: break-all;}
	</style>
	<div style="width:930px;">
	    <table width="900" cellspacing="0" align="right">
	        <tr>
	            <td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_library[$cbo_company_id]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	        	<td colspan="6" align="center" style="font-size:14px">  
					<?
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


					}
	                ?> 
	            </td>  
	        </tr>
	        <tr>
	            <td colspan="6" align="center" style="font-size:x-large"><strong><u><? echo $data[2]; ?> Report</u></strong></td>
	        </tr>
	    </table>
	        <table cellspacing="0" width="800" align="center" border="1" rules="all" class="">
	        <tr>
	        	<td width="125"><strong>Transfer ID:</strong></td><td width="175px"><? echo $dataArray[0]['TRANSFER_SYSTEM_ID']; ?></td>
	            <td width="125"><strong>Transfer Criteria:</strong></td><td width="175px"><? echo $item_transfer_criteria[$dataArray[0]['TRANSFER_CRITERIA']]; ?></td>
	            <td width="125"><strong>Item Category:</strong></td><td width="175px"><? echo $item_category[$dataArray[0]['ITEM_CATEGORY']]; ?></td>
	        </tr>
	        <tr>
	            <td><strong>Transfer Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0]['TRANSFER_DATE']); ?></td>
	            <td><strong>To Company:</strong></td><td width="175px"><? echo $company_library[$cbo_company_id_to]; ?></td>
	            <td><strong>Challan No:</strong></td><td width="175px"><? echo $dataArray[0]['CHALLAN_NO']; ?></td>
	        </tr>
	    </table>
	    <br>
	    <div style="width:100%;">
		    <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table">
		        <thead bgcolor="#dddddd" align="center">
		            <th width="50">SL</th>
		            <th width="160">From Referance</th>
		            <th width="160">To Referance</th>
		            <th width="100">Item Group</th>
		            <th width="140">Item Description</th>
		            <th width="70">UOM</th>
		            <th width="100">Transfered Qnty</th>
		            <th width="100">Remarks</th>
		        </thead>
		        <tbody>		   
					<?
					$sql_dtls="SELECT a.FROM_ORDER_ID, a.TO_ORDER_ID, b.FROM_STORE, b.TO_STORE, b.FROM_PROD_ID, b.TO_PROD_ID, sum(b.transfer_qnty) as TRANSFER_QNTY, b.UOM, b.ITEM_GROUP, b.REMARKS
					from inv_item_transfer_mst a, inv_item_transfer_dtls b 
					where a.id=b.mst_id and a.id='$mst_id' and a.company_id='$cbo_company_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
					group by a.from_order_id, a.to_order_id, b.from_store, b.to_store, b.from_prod_id, b.to_prod_id, b.uom, b.item_group, b.remarks 
					order by b.from_prod_id";			
					$sql_result= sql_select($sql_dtls);

					$po_id_array=array();
					$prod_id_array=array();
			        foreach ($sql_result as $row) 
			        {        
			        	$po_id_array[]=$row['FROM_ORDER_ID'].','.$row['TO_ORDER_ID'];
			        	$prod_id_array[]=$row['FROM_PROD_ID'].','.$row['TO_PROD_ID'];
			        }
			        $poIds = implode(",",array_unique($po_id_array));
			        $prodIds = implode(",",array_unique($prod_id_array));

			        $sql_order="SELECT a.ID, b.BUYER_NAME, b.STYLE_REF_NO, b.JOB_NO, a.PO_NUMBER from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted=0 and a.id in($poIds)";
			        $sql_order_res = sql_select($sql_order);
			        $order_array=array();
			        foreach ($sql_order_res as $row) {
			        	$order_array[$row['ID']]['BUYER_NAME']=$buyer_library[$row['BUYER_NAME']];
			        	$order_array[$row['ID']]['STYLE_REF_NO']=$row['STYLE_REF_NO'];
			        	$order_array[$row['ID']]['JOB_NO']=$row['JOB_NO'];
			        	$order_array[$row['ID']]['PO_NUMBER']=$row['PO_NUMBER'];
			        }

			        $product_arr = return_library_array("select id, product_name_details from product_details_master where id in($prodIds) and item_category_id=4 and status_active=1 and is_deleted=0","id","product_name_details");

					$i=1;
					foreach($sql_result as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
			                <td align="center"><?= $i; ?></td>
			                <td width="160" class="wrd_brk">Store:<? echo $store_library[$row['FROM_STORE']]; ?><hr>Job:<?= $order_array[$row['FROM_ORDER_ID']]['JOB_NO']; ?><hr>Style:<?= $order_array[$row['FROM_ORDER_ID']]['STYLE_REF_NO']; ?><hr>Buyer:<?= $order_array[$row['FROM_ORDER_ID']]['BUYER_NAME']; ?><hr>Order No:<?= $order_array[$row['FROM_ORDER_ID']]['PO_NUMBER']; ?></td>
			                <td width="160" class="wrd_brk">Store:<? echo $store_library[$row['FROM_STORE']]; ?><hr>Job:<?= $order_array[$row['TO_ORDER_ID']]['JOB_NO']; ?><hr>Style:<?= $order_array[$row['TO_ORDER_ID']]['STYLE_REF_NO']; ?><hr>Buyer:<?= $order_array[$row['TO_ORDER_ID']]['BUYER_NAME']; ?><hr>Order No:<?= $order_array[$row['TO_ORDER_ID']]['PO_NUMBER']; ?></td>
			                <td width="100" class="wrd_brk"><?= $item_group_arr[$row['ITEM_GROUP']]; ?></td>
			                <td width="140" class="wrd_brk"><?= $product_arr[$row['FROM_PROD_ID']]; ?></td>
			                <td width="70" class="wrd_brk" align="center"><?= $unit_of_measurement[$row['UOM']]; ?></td>
			                <td width="100" class="wrd_brk" align="right"><?= number_format($row['TRANSFER_QNTY'],2); ?></td>
			                <td class="wrd_brk" align="center"><?= $row['REMARKS']; ?></td>
						</tr>
						<? 
						$i++; 
						$tot_transfer_qnty+=$row['TRANSFER_QNTY'];
					} 
					?>
			    </tbody>
		        <tfoot>
		            <tr>
		                <td colspan="6" align="right"><strong>Total :</strong></td>
		                <td align="right"><?= number_format($tot_transfer_qnty,2); ?></td>
		                <td></td>
		            </tr>                           
		        </tfoot>
		    </table>
	        <br>
			 <?
	            //echo signature_table(24, $data[0], "900px");
	         ?>
	    </div>
	</div>   
 	<?
 	exit();
}
?>
