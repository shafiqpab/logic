<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, supplier_id, company_location_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$supplier_id = $userCredential[0][csf('supplier_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
$company_location_id = $userCredential[0][csf('company_location_id')];


if ($company_id !='') {
    $company_credential_cond = " and comp.id in($company_id)";
}
if ($store_location_id !='') {
    $store_location_credential_cond = " and a.id in($store_location_id)"; 
}
if ($supplier_id !='') {
    $supplier_credential_cond = " and a.id in($supplier_id)";
}

if ($company_location_id !='') {
    $location_credential_cond = " and id in($company_location_id)";
}

if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_name", 132, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data' and b.category_type=4 and a.status_active=1 and a.is_deleted=0 $store_location_credential_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "" );
	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 132, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id='$data' $location_credential_cond order by location_name","id,location_name", 1, "-- Select Location --", $selected, "",0 );     	 
}
if ($action=="load_drop_down_sewing_location")
{
	echo create_drop_down( "cbo_sewing_location_name", 132, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id='$data' $location_credential_cond order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/trims_issue_multi_ref_controller', this.value+'**'+document.getElementById('cbo_sewing_company').value+'**'+document.getElementById('cbo_sewing_source').value+'**'+document.getElementById('cbo_issue_purpose').value, 'load_drop_down_sewing_floor_unit', 'floor_unit_td' );",0 );     	 
}

if ($action=="load_drop_down_sewing_floor_unit")
{
	$data=explode("**",$data);
	if($data[3]==36) $prod_source=5;
	else if($data[3]==37) $prod_source=3;
	else if($data[3]==41) $prod_source=1;
	else if($data[3]==42) $prod_source=11;
	else $prod_source='1,3,5,11';
	$location_cond="";
	if($data[0]>0) $location_cond=" and location_id=$data[0]";

	echo create_drop_down( "cbo_floor_unit_name", 132, "select id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0 and company_id=$data[1] and location_id=$data[0] and production_process in($prod_source) order by floor_name","id,floor_name", 1, "-- Select Floor/Unit --", $selected, "",0 );  	 
}


if ($action=="sewing_floor_list")
{
	$data_ref=explode("__sep__",$data);
	if($data_ref[1]==36) $prod_source=5; //sewing
	else if($data_ref[1]==37) $prod_source=3; //Dyeing
	else if($data_ref[1]==41) $prod_source=1; //Cutting
	else if($data_ref[1]==42) $prod_source=4; //Finishing
	else $prod_source='1,3,4,11';

	$sewing_floor_data=sql_select("select id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0 and company_id=$data_ref[0] and production_process in($prod_source) order by floor_name");
	foreach($sewing_floor_data as $row)
	{
		$sewing_floor_arr[$row[csf('id')]]=$row[csf('floor_name')];
	}
	$jsSewingFloor_arr= json_encode($sewing_floor_arr);
	echo $jsSewingFloor_arr;
	die();
}


if ($action=="floor_list")
{
	$data_ref=explode("__",$data);	
	$floor_data=sql_select("select id, line_name from lib_sewing_line where is_deleted=0 and status_active=1 and company_name=".$data_ref[0]." and floor_name=".$data_ref[1]." and floor_name!=0  order by line_name");
	foreach($floor_data as $row)
	{
		$floor_arr[$row[csf('id')]]=$row[csf('line_name')];
	}
	$jsFloor_arr= json_encode($floor_arr);
	echo $jsFloor_arr;
	die();
}

if ($action == "load_drop_down_knitting_com") {
	$data = explode("_", $data);
	$company_id = $data[1];
	//$company_id
	if ($data[0] == 1) 
	{
		echo create_drop_down("cbo_sewing_company", 132, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--Select Sewing Company--", $company_id, "load_drop_down( 'requires/trims_issue_multi_ref_controller', this.value, 'load_drop_down_sewing_location', 'location_sewing_td' );", "");
	} 
	else if ($data[0] == 3) 
	{
		echo create_drop_down("cbo_sewing_company", 132, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=4 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select Sewing Company--", 0, "");
	} 
	else 
	{
		echo create_drop_down("cbo_sewing_company", 132, $blank_array, "", 1, "--Select Sewing Company--", 0, "load_location();");
	}
	exit();
}




$trim_group_arr =array();
$data_array=sql_select("select id, item_name, trim_uom, conversion_factor from lib_item_group");
foreach($data_array as $row)
{
	$trim_group_arr[$row[csf('id')]]['name']=$row[csf('item_name')];
	$trim_group_arr[$row[csf('id')]]['uom']=$row[csf('trim_uom')];
	$trim_group_arr[$row[csf('id')]]['conversion_factor']=$row[csf('conversion_factor')];
}


if($action=="create_itemDesc_search_list_view")
{
	$data=explode("**",$data);
	$po_id=$data[0];
	$prod_id=str_replace("'","",$data[1]);
	$store_id=$data[2];
	$prod_cond="";
	if($prod_id) $prod_cond=" and b.prod_id in($prod_id)";
	$cumilite_issue_sql=sql_select("select b.prod_id, b.po_breakdown_id, (case when b.trans_type in (2) then  b.quantity else 0 end) as issue_qnty, (case when b.trans_type in (4) then  b.quantity else 0 end) as issue_rtn_qnty, (case when b.trans_type in (3) then  b.quantity else 0 end) as rcv_rtn_qnty, (case when b.trans_type in (6) then  b.quantity else 0 end) as trans_out_qnty, c.floor_id as floor, c.room, c.rack, c.self, c.bin_box    
	from order_wise_pro_details b, inv_transaction c  
	where b.trans_id=c.id and b.status_active=1 and b.is_deleted=0 and b.entry_form in(25,49,73,78,112) and b.trans_type in (2,3,4,6) and c.transaction_type in (2,3,4,6) and b.po_breakdown_id in ($data[0]) and c.store_id=$store_id $prod_cond");
	$cumilite_issue_data=array();
	foreach($cumilite_issue_sql as $row)
	{
		$cumilite_issue_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]][$row[csf("floor")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["issue_qnty"]+=$row[csf("issue_qnty")]-$row[csf("issue_rtn_qnty")];
		$cumilite_issue_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]][$row[csf("floor")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["rcv_rtn_qnty"]+=$row[csf("rcv_rtn_qnty")];
		$cumilite_issue_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]][$row[csf("floor")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["trans_out_qnty"]+=$row[csf("trans_out_qnty")];
	}
	
	
	//$po_no_arr = return_library_array("select id, po_number from wo_po_break_down where id in($data[0])","id","po_number");

	
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$size_arr = return_library_array("select id, size_name from lib_size where status_active=1 and is_deleted=0","id","size_name");
	
	$sql = "SELECT a.id, a.company_id, a.item_group_id, a.item_description, a.product_name_details, a.brand_supplier, a.color, a.gmts_size, a.item_color, a.item_size, a.current_stock, a.unit_of_measure, b.po_breakdown_id, c.floor_id as floor, c.room, c.rack, c.self, c.bin_box, sum(b.quantity) as recv_qty, d.grouping, d.po_number ,e.JOB_NO,e.STYLE_REF_NO 
	from product_details_master a, order_wise_pro_details b, inv_transaction c, wo_po_break_down d,WO_PO_DETAILS_MASTER e
	where a.id=b.prod_id and b.trans_id=c.id and b.po_breakdown_id=d.id and d.JOB_ID = e.id and a.item_category_id='4' and a.entry_form=24 and b.trans_type in(1,4,5) and b.entry_form in(24,73,78,112) and b.po_breakdown_id in ($data[0]) and c.store_id=$store_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and a.current_stock>0 
	group by a.id, a.company_id, a.item_group_id, a.item_description, a.product_name_details, a.brand_supplier, a.color, a.gmts_size, a.item_color, a.item_size, a.current_stock, a.unit_of_measure, b.po_breakdown_id, c.floor_id, c.room, c.rack, c.self, c.bin_box, d.grouping, d.po_number,e.JOB_NO,e.STYLE_REF_NO
	order by a.item_group_id";//, c.floor_id, c.room, c.rack, c.self, c.bin_box
	//echo $sql;die;
	$result = sql_select($sql);
	
	$booking_sql = "select b.po_break_down_id, b.sensitivity, b.trim_group, b.uom, c.description, c.color_number_id as color_id, c.item_color, c.gmts_sizes as size_id, c.item_size, c.brand_supplier, c.rate, c.cons as book_qnty, c.item_ref, c.DESCRIPTION  
	from wo_booking_dtls b, wo_trim_book_con_dtls c, wo_po_break_down d 
	where d.id=c.po_break_down_id and b.id=c.wo_trim_booking_dtls_id  and c.cons>0 and b.status_active=1 and b.is_deleted=0 and d.id in ($po_id)
	order by b.trim_group, b.uom, c.description, c.color_number_id, c.item_color, c.gmts_sizes, c.item_size, c.brand_supplier, c.rate, c.cons";
	//echo $sql;die;
	$booking_result=sql_select($booking_sql);
	$booking_pi_data=array();
	foreach($booking_result as $row)
	{
		$trim_key=$row[csf("po_break_down_id")]."__".$row[csf("trim_group")]."__".$row[csf("color_id")]."__".$row[csf("item_color")]."__".$row[csf("size_id")]."__".$row[csf("item_size")]."__".$row["DESCRIPTION"]."__".$row[csf("brand_supplier")];

		$booking_pi_data[$trim_key]["item_ref"]=$row[csf("item_ref")];
	}

    $poDataArr = sql_select("select PO_BREAK_DOWN_ID, ARTICLE_NUMBER, SIZE_NUMBER_ID, COLOR_NUMBER_ID from wo_po_color_size_breakdown where po_break_down_id in ($po_id) group by PO_BREAK_DOWN_ID, ARTICLE_NUMBER, SIZE_NUMBER_ID, COLOR_NUMBER_ID");
    $po_arr = array();
    foreach ($poDataArr as $k => $v){
        $po_arr[$v['PO_BREAK_DOWN_ID']."*".$v['COLOR_NUMBER_ID']."*".$v['SIZE_NUMBER_ID']] = $v['ARTICLE_NUMBER'];
    }
	$all_rcv_prod_id="";$prod_check=array();
	foreach($result as $row)
	{
		if($prod_check[$row[csf("id")]]=="")
		{
			$prod_check[$row[csf("id")]]=$row[csf("id")];
			$all_rcv_prod_id.=$row[csf("id")].",";
		}
		
	}
	$all_rcv_prod_id=chop($all_rcv_prod_id,",");
	if($all_rcv_prod_id=="") $all_rcv_prod_id=0;
	
	
	$conversion_fac_sql=sql_select("select a.ID, b.CONVERSION_FACTOR from product_details_master a, lib_item_group b where a.item_group_id=b.id and a.id in($all_rcv_prod_id)");
	$conversion_fac_arr=array();
	foreach($conversion_fac_sql as $val)
	{
		$conversion_fac_arr[$val["ID"]]=$val["CONVERSION_FACTOR"];
	}
	
	$sql_trim = sql_select("select b.PO_BREAKDOWN_ID, b.PROD_ID, c.FLOOR_ID, c.ROOM, c.RACK, c.SELF, c.BIN_BOX, sum((case when b.entry_form in(24,73,78,112) and b.trans_type in(1,4,5) then b.quantity else 0 end)-(case when b.entry_form in(25,49,78,112) and b.trans_type in(2,3,6) then b.quantity else 0 end)) as BALANCE 
	from order_wise_pro_details b, inv_transaction c
	where b.trans_id=c.id and c.store_id=$store_id and c.prod_id in($all_rcv_prod_id) and c.item_category=4 and b.po_breakdown_id in($po_id) and c.status_active=1  and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	group by b.po_breakdown_id, b.prod_id, c.FLOOR_ID, c.ROOM, c.RACK, c.SELF, c.BIN_BOX");//
	$order_prev_data=array();
	foreach($sql_trim as $row)
	{
		$store_wise_stock[$row["PROD_ID"]][$row["PO_BREAKDOWN_ID"]][$row["FLOOR_ID"]][$row["ROOM"]][$row["RACK"]][$row["SELF"]][$row["BIN_BOX"]]=$row["BALANCE"]*$conversion_fac_arr[$row["PROD_ID"]];
	}
	
	
	
	$floor_sql_res=sql_select("select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and company_id=".$result[0][csf('company_id')]." order by floor_name");
	foreach($floor_sql_res as $row)
	{
		$floor_arr[$row[csf('id')]] =$row[csf('floor_name')];
	}
    $lib_floor_room_rack_self = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst where company_id=".$result[0][csf('company_id')],"floor_room_rack_id","floor_room_rack_name");
    
	$line_sql_res=sql_select("select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and company_id=".$result[0][csf('company_id')]." and floor_name!=0  order by line_name");
	foreach($line_sql_res as $row)
	{
		$line_arr[$row[csf('id')]] =$row[csf('line_name')];
	}
	
	//echo "<pre>".count($result);die;

	$prod_line_arr=return_library_array("select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and company_name=".$result[0][csf('company_id')]." and floor_name!=0  order by line_name","id","line_name");

    $i=1;
    foreach ($result as $row)
    {
		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		$conversion_factor=$trim_group_arr[$row[csf('item_group_id')]]['conversion_factor'];
		$cu_issue=$balance=0;
		
		$current_stock=$store_wise_stock[$row[csf('id')]][$row[csf('po_breakdown_id')]][$row[csf("floor")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]];
		$cu_issue=$cumilite_issue_data[$row[csf('id')]][$row[csf('po_breakdown_id')]][$row[csf("floor")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["issue_qnty"]*$conversion_factor;
		$receive_qnty=(($row[csf('recv_qty')]-$cumilite_issue_data[$row[csf('id')]][$row[csf('po_breakdown_id')]][$row[csf("floor")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["rcv_rtn_qnty"]-$cumilite_issue_data[$row[csf('id')]][$row[csf('po_breakdown_id')]][$row[csf("floor")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["trans_out_qnty"])*$conversion_factor);
		$balance=$receive_qnty-$cu_issue;
		//if($i==1) $load_dorp_down_fnc="load_drop_down( 'requires/trims_issue_multi_ref_controller', this.value+'_'+".$row[csf('company_id')].", 'load_drop_down_sewing_line', 'td_line_1' );"; else $load_dorp_down_fnc="";
		//$load_dorp_down_fnc="load_drop_down( 'requires/trims_issue_multi_ref_controller', this.value+'_'+".$row[csf('company_id')]."+'_'+".$i.", 'load_drop_down_sewing_line', 'td_line_".$i."');";

		$trim_key=$row[csf("po_breakdown_id")]."__".$row[csf("item_group_id")]."__".$row[csf("color")]."__".$row[csf("item_color")]."__".$row[csf("gmts_size")]."__".$row[csf("item_size")]."__".$row[csf("item_description")]."__".$row[csf("brand_supplier")];
		?>
        <tbody> 
        <?
		if(number_format($current_stock,4,'.','')>0)
		{
			?>
            <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick='change_color("tr_<? echo $i; ?>","<? echo $bgcolor;?>");' id="tr_<? echo $i; ?>" >
                <td width="120" id="po_no_<? echo $i; ?>" title="<? echo $row[csf('po_breakdown_id')];?>" style="word-break:break-all"><? echo $row[csf('po_number')]; ?></td>

				<td width="80" id="job_no_<? echo $i; ?>" title="<? echo $row[csf('po_breakdown_id')];?>" style="word-break:break-all"><? echo $row['JOB_NO']; ?></td>
				<td width="100" id="style_no_<? echo $i; ?>" title="<? echo $row[csf('po_breakdown_id')];?>" style="word-break:break-all"><? echo $row['STYLE_REF_NO']; ?></td>


				<td width="100" id="internal_ref_<? echo $i; ?>" title="<? echo $row[csf('po_breakdown_id')];?>" style="word-break:break-all"><? echo $row[csf('grouping')]; ?></td>
				<td width="80" id="article_no_<? echo $i; ?>" title="<? echo $row[csf('po_breakdown_id')];?>" style="word-break:break-all"><? echo $booking_pi_data[$trim_key]["item_ref"];//$po_arr[$row[csf('po_breakdown_id')]."*".$row[csf('color')]."*".$row[csf('gmts_size')]]; ?></td>
                <td width="100" id="item_group_<? echo $i; ?>" title="<? echo $row[csf('item_group_id')];?>" style="word-break:break-all"><? echo $trim_group_arr[$row[csf('item_group_id')]]['name']; ?></td>
                <td width="50" id="prod_id_<? echo $i; ?>" align="center" title="<? echo $row[csf('id')];?>" style="word-break:break-all"><? echo $row[csf('id')];?></td>
                <td width="140" id="item_descrip_<? echo $i; ?>" title="<? echo $row[csf('id')];?>" style="word-break:break-all"><? echo $row[csf('item_description')]; ?></td>  
                <td width="100" id="brand_supp_<? echo $i; ?>" title="<? echo $row[csf('brand_supplier')];?>" style="word-break:break-all"><? echo $row[csf('brand_supplier')]; ?></td>
				<td width="80" id="gmtcolor_<? echo $i; ?>" title="<? echo $row[csf('color')];?>" style="word-break:break-all"><? echo $color_arr[$row[csf('color')]]; ?></td>

                <td width="80" id="gmtsize_<? echo $i; ?>" align="center" title="<? echo $row[csf('gmts_size')];?>" style="word-break:break-all"><? echo $size_arr[$row[csf('gmts_size')]]; ?></td>
                <td width="80" id="item_color_<? echo $i; ?>" title="<? echo $row[csf('item_color')];?>" style="word-break:break-all"><? echo $color_arr[$row[csf('item_color')]]; ?><input type="hidden" name="gmtcolor[]" id="gmtcolor_<? echo $i; ?>" value="<? echo $row[csf('color')]; ?>" /></td>
                <td width="80" id="item_size_<? echo $i; ?>" align="center" title="<? echo $row[csf('item_size')];?>" style="word-break:break-all"><? echo $row[csf('item_size')]; ?><input type="hidden" name="gmtsize[]" id="gmtsize_<? echo $i; ?>" value="<? echo $row[csf('gmts_size')]; ?>" /></td>
                <td width="80" id="uom_<? echo $i; ?>" align="center" title="<? echo $row[csf('unit_of_measure')];?>" style="word-break:break-all"><? echo $unit_of_measurement[$row[csf('unit_of_measure')]]; ?></td>
                <td width="75" id="tdissueqnty_<? echo $i; ?>" align="center">
                <input type="text" name="issueqnty[]" id="issueqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" value="<? echo number_format($current_stock,4,'.',''); ?>" onBlur="calculate(<? echo $i; ?>);" placeholder="<? echo number_format($current_stock,4,'.','');?>" /> 
                </td>
                <td width="75" id="tdreceiveqnty_<? echo $i; ?>" align="center" title="<? echo "Total Receive Qnty + Total Transfer Qnty"; ?>">
                <input type="text" name="receiveqnty[]" id="receiveqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" value="<? echo number_format($receive_qnty,4,'.',''); ?>"  readonly disabled />
                </td>
                <td width="75" id="tdcuissue_<? echo $i; ?>" align="center"><input type="text" name="cuissue[]" id="cuissue_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" value="<? echo number_format($cu_issue,4,'.',''); ?>"  readonly disabled /></td>
                <td width="75" id="tdyettoissue_<? echo $i; ?>" align="center"><input class="text_boxes_numeric"  name="yettoissue[]" id="yettoissue_<? echo $i; ?>" value="<? echo number_format($current_stock,4,'.',''); ?>" type="text" style="width:60px;" readonly disabled /></td>
                <td width="60" id="td_floor_<? echo $i; ?>">
                <?
                //echo create_drop_down( "cbofloor_".$i, 100, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and company_id=".$row[csf('company_id')]." order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected,"fn_floor($i);load_drop_down( 'requires/trims_issue_multi_ref_controller', this.value+'_'+".$row[csf('company_id')]."+'_'+".$i.", 'load_drop_down_sewing_line', 'td_line_".$i."');",0,"","","","","","","cbofloor[]"); 
                echo create_drop_down( "cbofloor_".$i, 60, $floor_arr,"", 1, "-- Select Floor --", $selected,"fn_floor($i);",0,"","","","","","","cbofloor[]"); 
                ?>
                </td>
				<td width="60" id="td_cboRecFloor_<? echo $i; ?>" align="center" title="<? echo $row[csf('floor')]; ?>">
					<? echo $lib_floor_room_rack_self[$row[csf('floor')]]; ?>
					<? //echo create_drop_down( "cboRecFloor_".$i, 60,$lib_floor_room_rack_self,"", 1, "--Select--",$row[csf('floor')],"",1,"","","","","","","cboRecFloor[]"); ?>
				</td>
				<td width="60" id="td_cboRecRoom_<? echo $i; ?>" align="center" title="<? echo $row[csf('room')]; ?>">
					<? echo $lib_floor_room_rack_self[$row[csf('room')]]; ?>
					<? //echo create_drop_down( "cboRecRoom_".$i, 60,$lib_floor_room_rack_self,"", 1, "--Select--", $row[csf('room')], "",1,"","","","","","","cboRecRoom[]" ); ?>
				</td>
				<td width="60" id="td_cboRecRack_<? echo $i; ?>" align="center" title="<? echo $row[csf('rack')]; ?>">
					<? echo $lib_floor_room_rack_self[$row[csf('rack')]]; ?>
					<? //echo create_drop_down( "cboRecRack_".$i, 60,$lib_floor_room_rack_self,"", 1, "--Select--", $row[csf('rack')], "",1,"","","","","","","cboRecRack[]" ); ?>
				</td>
				<td width="60" id="td_cboRecShelf_<? echo $i; ?>" align="center" title="<? echo $row[csf('self')]; ?>">
					<? echo $lib_floor_room_rack_self[$row[csf('self')]]; ?>
					<? //echo create_drop_down( "cboRecShelf_".$i, 60,$lib_floor_room_rack_self,"", 1, "--Select--", $row[csf('self')], "",1,"","","","","","","cboRecShelf[]" ); ?>
				</td>
				<td width="60" id="td_cboRecBin_<? echo $i; ?>" align="center" title="<? echo $row[csf('bin_box')]; ?>">
					<? echo $lib_floor_room_rack_self[$row[csf('bin_box')]]; ?>
					<? //echo create_drop_down( "cboRecBin_".$i, 60,$lib_floor_room_rack_self,"", 1, "--Select--", $row[csf('bin_box')], "",1,"","","","","","","cboRecBin[]" ); ?>
				</td>
                <td width="100" id="td_line_<? echo $i; ?>">
                <?
                echo create_drop_down( "cboline_".$i, 100, $prod_line_arr,"", 1, "-- Select --", $selected,"fn_line($i);",0,"","","","","","","cboline[]"); 
                //echo create_drop_down( "cboline_".$i, 100, $blank_array,"", 1, "-- Select --", $selected,"fn_line($i);",0,"","","","","","","cboline[]");
                ?>
                </td>
                <td width="100" id="tdglobalstock_<? echo $i; ?>" align="center" title="<?= "Global Stock = ".$row[csf('current_stock')]?>"><input class="text_boxes_numeric"  name="globalstock[]" id="globalstock_<? echo $i; ?>" value="<? echo number_format($current_stock,4,'.',''); ?>" type="text" style="width:70px;" readonly />
                <input type="hidden" name="updatedtlsid[]" id="updatedtlsid_<? echo $i; ?>" value="" readonly>
                <input type="hidden" name="updatetransid[]" id="updatetransid_<? echo $i; ?>" value="" readonly>
                <input type="hidden" name="previousprodid[]" id="previousprodid_<? echo $i; ?>" value="" readonly>
                </td>
            </tr>
            <?
            $i++;
		}
		?>
        </tbody>
        <?
    }
	exit();
}

if($action=="create_itemDesc_search_list_view_req")
{
	$data=explode("**",$data);
	$po_id=str_replace("'","",$data[0]);
	$prod_id=str_replace("'","",$data[1]);
	$store_id=str_replace("'","",$data[2]);
	$req_id=str_replace("'","",$data[3]);
	$prod_cond="";
	if($prod_id) $prod_cond=" and b.prod_id in($prod_id)";
	
	
	//$po_no_arr = return_library_array("select id, po_number from wo_po_break_down where id in($data[0])","id","po_number");

	
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$size_arr = return_library_array("select id, size_name from lib_size where status_active=1 and is_deleted=0","id","size_name");
	
	$sql = "SELECT a.id, a.company_id, a.item_group_id, a.item_description, a.product_name_details, a.brand_supplier, a.color, a.gmts_size, a.item_color, a.item_size, a.current_stock, a.unit_of_measure, b.po_breakdown_id, c.floor_id as floor, c.room, c.rack, c.self, c.bin_box, sum(b.quantity) as recv_qty, d.grouping, d.po_number ,e.JOB_NO,e.STYLE_REF_NO 
	from product_details_master a, order_wise_pro_details b, inv_transaction c, wo_po_break_down d,wo_po_details_master e
	where a.id=b.prod_id and b.trans_id=c.id and b.po_breakdown_id=d.id and d.job_id = e.id and a.item_category_id='4' and a.entry_form=24 and b.trans_type in(1,4,5) and b.entry_form in(24,73,78,112) and b.po_breakdown_id in ($data[0]) and c.store_id=$store_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.current_stock>0 
	group by a.id, a.company_id, a.item_group_id, a.item_description, a.product_name_details, a.brand_supplier, a.color, a.gmts_size, a.item_color, a.item_size, a.current_stock, a.unit_of_measure, b.po_breakdown_id, c.floor_id, c.room, c.rack, c.self, c.bin_box, d.grouping, d.po_number,e.JOB_NO,e.STYLE_REF_NO 
	order by a.item_group_id";//, c.floor_id, c.room, c.rack, c.self, c.bin_box
	//echo $sql;die;
	$result = sql_select($sql);

	
	$booking_sql = "select b.po_break_down_id, b.sensitivity, b.trim_group, b.uom, c.description, c.color_number_id as color_id, c.item_color, c.gmts_sizes as size_id, c.item_size, c.brand_supplier, c.rate, c.cons as book_qnty, c.item_ref, c.DESCRIPTION  
	from wo_booking_dtls b, wo_trim_book_con_dtls c, wo_po_break_down d 
	where d.id=c.po_break_down_id and b.id=c.wo_trim_booking_dtls_id  and c.cons>0 and b.status_active=1 and b.is_deleted=0 and d.id in ($po_id)
	order by b.trim_group, b.uom, c.description, c.color_number_id, c.item_color, c.gmts_sizes, c.item_size, c.brand_supplier, c.rate, c.cons";
	//echo $sql;die;
	$booking_result=sql_select($booking_sql);
	$booking_pi_data=array();
	foreach($booking_result as $row)
	{
		$trim_key=$row[csf("po_break_down_id")]."__".$row[csf("trim_group")]."__".$row[csf("color_id")]."__".$row[csf("item_color")]."__".$row[csf("size_id")]."__".$row[csf("item_size")]."__".$row["DESCRIPTION"]."__".$row[csf("brand_supplier")];

		$booking_pi_data[$trim_key]["item_ref"]=$row[csf("item_ref")];
	}

    $poDataArr = sql_select("select PO_BREAK_DOWN_ID, ARTICLE_NUMBER, SIZE_NUMBER_ID, COLOR_NUMBER_ID from wo_po_color_size_breakdown where po_break_down_id in ($po_id) group by PO_BREAK_DOWN_ID, ARTICLE_NUMBER, SIZE_NUMBER_ID, COLOR_NUMBER_ID");
    $po_arr = array();
    foreach ($poDataArr as $k => $v){
        $po_arr[$v['PO_BREAK_DOWN_ID']."*".$v['COLOR_NUMBER_ID']."*".$v['SIZE_NUMBER_ID']] = $v['ARTICLE_NUMBER'];
    }
	$all_rcv_prod_id="";$prod_check=array();
	foreach($result as $row)
	{
		if($prod_check[$row[csf("id")]]=="")
		{
			$prod_check[$row[csf("id")]]=$row[csf("id")];
			$all_rcv_prod_id.=$row[csf("id")].",";
		}
		
	}
	$all_rcv_prod_id=chop($all_rcv_prod_id,",");
	if($all_rcv_prod_id=="") $all_rcv_prod_id=0;
	
	/* $store_stock_sql=sql_select("select prod_id, sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as balance_qnty from inv_transaction where status_active=1 and is_deleted=0 and store_id=$store_id and prod_id in($all_rcv_prod_id) group by prod_id");
	$store_wise_stock=array();
	foreach($store_stock_sql as $row)
	{
		$store_wise_stock[$row[csf("prod_id")]]=$row[csf("balance_qnty")];
	} */
	
	
	$conversion_fac_sql=sql_select("select a.ID, b.CONVERSION_FACTOR from product_details_master a, lib_item_group b where a.item_group_id=b.id and a.id in($all_rcv_prod_id)");
	$conversion_fac_arr=array();
	foreach($conversion_fac_sql as $val)
	{
		$conversion_fac_arr[$val["ID"]]=$val["CONVERSION_FACTOR"];
	}
	
	$req_sql=sql_select("select b.PO_ID, b.PRODUCT_ID, b.REQSN_QTY  from READY_TO_SEWING_REQSN b where b.MST_ID=$req_id and b.status_active=1 and b.is_deleted=0");
	$req_data_arr=array();
	foreach($req_sql as $row)
	{
		$req_data_arr[$row["PRODUCT_ID"]][$row["PO_ID"]]+=$row["REQSN_QTY"];
	}
	unset($req_sql);
	
	$prev_req_sql="select a.BOOKING_ID, b.PROD_ID, c.PO_BREAKDOWN_ID , sum(c.QUANTITY) as QUANTITY 
	from INV_ISSUE_MASTER a, INV_TRANSACTION b, ORDER_WISE_PRO_DETAILS c 
	where a.id=b.mst_id and b.id=c.trans_id and a.entry_form=25 and c.entry_form=25 and a.ISSUE_BASIS=3 and b.transaction_type=2 and c.trans_type=2 and b.item_category=4 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.BOOKING_ID=$req_id
	group by a.BOOKING_ID, b.PROD_ID, c.PO_BREAKDOWN_ID";
	$prev_req_result=sql_select($prev_req_sql);
	$prev_issue_data=array();
	foreach($prev_req_result as $val)
	{
		$prev_issue_data[$val["PROD_ID"]][$val["PO_BREAKDOWN_ID"]]+=$val["QUANTITY"]*$conversion_fac_arr[$val["PROD_ID"]];
	}
	unset($prev_req_result);
	
	$sql_trim = sql_select("select b.PO_BREAKDOWN_ID, b.PROD_ID, c.FLOOR_ID, c.ROOM, c.RACK, c.SELF, c.BIN_BOX, sum((case when b.entry_form in(24,73,78,112) and b.trans_type in(1,4,5) then b.quantity else 0 end)-(case when b.entry_form in(25,49,78,112) and b.trans_type in(2,3,6) then b.quantity else 0 end)) as BALANCE 
	from order_wise_pro_details b, inv_transaction c
	where b.trans_id=c.id and c.store_id=$store_id and c.prod_id in($all_rcv_prod_id) and c.item_category=4 and b.po_breakdown_id in($po_id) and c.status_active=1  and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	group by b.po_breakdown_id, b.prod_id, c.FLOOR_ID, c.ROOM, c.RACK, c.SELF, c.BIN_BOX");//
	$order_prev_data=array();
	foreach($sql_trim as $row)
	{
		$store_wise_stock[$row["PROD_ID"]][$row["PO_BREAKDOWN_ID"]][$row["FLOOR_ID"]][$row["ROOM"]][$row["RACK"]][$row["SELF"]][$row["BIN_BOX"]]=$row["BALANCE"]*$conversion_fac_arr[$row["PROD_ID"]];
	}
	
	
	
	$floor_sql_res=sql_select("select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and company_id=".$result[0][csf('company_id')]." order by floor_name");
	foreach($floor_sql_res as $row)
	{
		$floor_arr[$row[csf('id')]] =$row[csf('floor_name')];
	}
    $lib_floor_room_rack_self = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst where company_id=".$result[0][csf('company_id')],"floor_room_rack_id","floor_room_rack_name");
    
	$line_sql_res=sql_select("select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and company_id=".$result[0][csf('company_id')]." and floor_name!=0  order by line_name");
	foreach($line_sql_res as $row)
	{
		$line_arr[$row[csf('id')]] =$row[csf('line_name')];
	}
	
	//echo "<pre>".count($result);die;

	$prod_line_arr=return_library_array("select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and company_name=".$result[0][csf('company_id')]." and floor_name!=0  order by line_name","id","line_name");

    $i=1;
    foreach ($result as $row)
    {
		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		$conversion_factor=$trim_group_arr[$row[csf('item_group_id')]]['conversion_factor'];
		$req_balance=0;
		$req_balance=$req_data_arr[$row[csf('id')]][$row[csf('po_breakdown_id')]]-$prev_issue_data[$row[csf('id')]][$row[csf('po_breakdown_id')]];
		
		$current_stock=$store_wise_stock[$row[csf('id')]][$row[csf('po_breakdown_id')]][$row[csf("floor")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]];

		$trim_key=$row[csf("po_breakdown_id")]."__".$row[csf("item_group_id")]."__".$row[csf("color")]."__".$row[csf("item_color")]."__".$row[csf("gmts_size")]."__".$row[csf("item_size")]."__".$row[csf("item_description")]."__".$row[csf("brand_supplier")];
		?>
        <tbody> 
        <?
		if(number_format($current_stock,4,'.','')>0 && number_format($req_balance,4,'.','')>0)
		{
			?>
            <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick='change_color("tr_<? echo $i; ?>","<? echo $bgcolor;?>");' id="tr_<? echo $i; ?>" >
                <td width="120" id="po_no_<? echo $i; ?>" title="<? echo $row[csf('po_breakdown_id')];?>" style="word-break:break-all"><? echo $row[csf('po_number')]; ?></td>

				<td width="80" id="job_no_<? echo $i; ?>" title="<? echo $row[csf('po_breakdown_id')];?>" style="word-break:break-all"><? echo $row['JOB_NO']; ?></td>
				<td width="100" id="style_no_<? echo $i; ?>" title="<? echo $row[csf('po_breakdown_id')];?>" style="word-break:break-all"><? echo $row['STYLE_REF_NO']; ?></td>



				<td width="100" id="internal_ref_<? echo $i; ?>" title="<? echo $row[csf('po_breakdown_id')];?>" style="word-break:break-all"><? echo $row[csf('grouping')]; ?></td>
				<td width="80" id="article_no_<? echo $i; ?>" title="<? echo $row[csf('po_breakdown_id')];?>" style="word-break:break-all"><? echo $booking_pi_data[$trim_key]["item_ref"];//$po_arr[$row[csf('po_breakdown_id')]."*".$row[csf('color')]."*".$row[csf('gmts_size')]]; ?></td>
                <td width="100" id="item_group_<? echo $i; ?>" title="<? echo $row[csf('item_group_id')];?>" style="word-break:break-all"><? echo $trim_group_arr[$row[csf('item_group_id')]]['name']; ?></td>
                <td width="50" id="prod_id_<? echo $i; ?>" align="center" title="<? echo $row[csf('id')];?>" style="word-break:break-all"><? echo $row[csf('id')];?></td>
                <td width="140" id="item_descrip_<? echo $i; ?>" title="<? echo $row[csf('id')];?>" style="word-break:break-all"><? echo $row[csf('item_description')]; ?></td>  
                <td width="100" id="brand_supp_<? echo $i; ?>" title="<? echo $row[csf('brand_supplier')];?>" style="word-break:break-all"><? echo $row[csf('brand_supplier')]; ?></td>
				<td width="80" id="gmtcolor_<? echo $i; ?>" title="<? echo $row[csf('color')];?>" style="word-break:break-all"><? echo $color_arr[$row[csf('color')]]; ?></td>

                <td width="80" id="gmtsize_<? echo $i; ?>" align="center" title="<? echo $row[csf('gmts_size')];?>" style="word-break:break-all"><? echo $size_arr[$row[csf('gmts_size')]]; ?></td>
                <td width="80" id="item_color_<? echo $i; ?>" title="<? echo $row[csf('item_color')];?>" style="word-break:break-all"><? echo $color_arr[$row[csf('item_color')]]; ?><input type="hidden" name="gmtcolor[]" id="gmtcolor_<? echo $i; ?>" value="<? echo $row[csf('color')]; ?>" /></td>
                <td width="80" id="item_size_<? echo $i; ?>" align="center" title="<? echo $row[csf('item_size')];?>" style="word-break:break-all"><? echo $row[csf('item_size')]; ?><input type="hidden" name="gmtsize[]" id="gmtsize_<? echo $i; ?>" value="<? echo $row[csf('gmts_size')]; ?>" /></td>
                <td width="80" id="uom_<? echo $i; ?>" align="center" title="<? echo $row[csf('unit_of_measure')];?>" style="word-break:break-all"><? echo $unit_of_measurement[$row[csf('unit_of_measure')]]; ?></td>
                <td width="75" id="tdissueqnty_<? echo $i; ?>" align="center">
                <input type="text" name="issueqnty[]" id="issueqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" value="<? echo number_format($req_balance,4,'.',''); ?>" onBlur="calculate(<? echo $i; ?>);" placeholder="<? echo number_format($req_balance,4,'.','');?>" /> 
                </td>
                <td width="75" id="tdreceiveqnty_<? echo $i; ?>" align="center" title="<? echo "Total Receive Qnty + Total Transfer Qnty"; ?>">
                <input type="text" name="receiveqnty[]" id="receiveqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" value="<? echo number_format($req_data_arr[$row[csf('id')]][$row[csf('po_breakdown_id')]],4,'.',''); ?>"  readonly disabled />
                </td>
                <td width="75" id="tdcuissue_<? echo $i; ?>" align="center"><input type="text" name="cuissue[]" id="cuissue_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" value="<? echo number_format($prev_issue_data[$row[csf('id')]][$row[csf('po_breakdown_id')]],4,'.',''); ?>"  readonly disabled /></td>
                <td width="75" id="tdyettoissue_<? echo $i; ?>" align="center"><input class="text_boxes_numeric"  name="yettoissue[]" id="yettoissue_<? echo $i; ?>" value="<? echo number_format($req_balance,4,'.',''); ?>" type="text" style="width:60px;" readonly disabled /></td>
                <td width="60" id="td_floor_<? echo $i; ?>">
                <?
                //echo create_drop_down( "cbofloor_".$i, 100, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and company_id=".$row[csf('company_id')]." order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected,"fn_floor($i);load_drop_down( 'requires/trims_issue_multi_ref_controller', this.value+'_'+".$row[csf('company_id')]."+'_'+".$i.", 'load_drop_down_sewing_line', 'td_line_".$i."');",0,"","","","","","","cbofloor[]"); 
                echo create_drop_down( "cbofloor_".$i, 60, $floor_arr,"", 1, "-- Select Floor --", $selected,"fn_floor($i);",0,"","","","","","","cbofloor[]"); 
                ?>
                </td>
				<td width="60" id="td_cboRecFloor_<? echo $i; ?>" align="center" title="<? echo $row[csf('floor')]; ?>">
					<? echo $lib_floor_room_rack_self[$row[csf('floor')]]; ?>
					<? //echo create_drop_down( "cboRecFloor_".$i, 60,$lib_floor_room_rack_self,"", 1, "--Select--",$row[csf('floor')],"",1,"","","","","","","cboRecFloor[]"); ?>
				</td>
				<td width="60" id="td_cboRecRoom_<? echo $i; ?>" align="center" title="<? echo $row[csf('room')]; ?>">
					<? echo $lib_floor_room_rack_self[$row[csf('room')]]; ?>
					<? //echo create_drop_down( "cboRecRoom_".$i, 60,$lib_floor_room_rack_self,"", 1, "--Select--", $row[csf('room')], "",1,"","","","","","","cboRecRoom[]" ); ?>
				</td>
				<td width="60" id="td_cboRecRack_<? echo $i; ?>" align="center" title="<? echo $row[csf('rack')]; ?>">
					<? echo $lib_floor_room_rack_self[$row[csf('rack')]]; ?>
					<? //echo create_drop_down( "cboRecRack_".$i, 60,$lib_floor_room_rack_self,"", 1, "--Select--", $row[csf('rack')], "",1,"","","","","","","cboRecRack[]" ); ?>
				</td>
				<td width="60" id="td_cboRecShelf_<? echo $i; ?>" align="center" title="<? echo $row[csf('self')]; ?>">
					<? echo $lib_floor_room_rack_self[$row[csf('self')]]; ?>
					<? //echo create_drop_down( "cboRecShelf_".$i, 60,$lib_floor_room_rack_self,"", 1, "--Select--", $row[csf('self')], "",1,"","","","","","","cboRecShelf[]" ); ?>
				</td>
				<td width="60" id="td_cboRecBin_<? echo $i; ?>" align="center" title="<? echo $row[csf('bin_box')]; ?>">
					<? echo $lib_floor_room_rack_self[$row[csf('bin_box')]]; ?>
					<? //echo create_drop_down( "cboRecBin_".$i, 60,$lib_floor_room_rack_self,"", 1, "--Select--", $row[csf('bin_box')], "",1,"","","","","","","cboRecBin[]" ); ?>
				</td>
                <td width="100" id="td_line_<? echo $i; ?>">
                <?
                echo create_drop_down( "cboline_".$i, 100, $prod_line_arr,"", 1, "-- Select --", $selected,"fn_line($i);",0,"","","","","","","cboline[]"); 
                //echo create_drop_down( "cboline_".$i, 100, $blank_array,"", 1, "-- Select --", $selected,"fn_line($i);",0,"","","","","","","cboline[]");
                ?>
                </td>
                <td width="100" id="tdglobalstock_<? echo $i; ?>" align="center" title="<?= "Global Stock = ".$row[csf('current_stock')]?>"><input class="text_boxes_numeric"  name="globalstock[]" id="globalstock_<? echo $i; ?>" value="<? echo number_format($current_stock,4,'.',''); ?>" type="text" style="width:70px;" readonly />
                <input type="hidden" name="updatedtlsid[]" id="updatedtlsid_<? echo $i; ?>" value="" readonly>
                <input type="hidden" name="updatetransid[]" id="updatetransid_<? echo $i; ?>" value="" readonly>
                <input type="hidden" name="previousprodid[]" id="previousprodid_<? echo $i; ?>" value="" readonly>
                </td>
            </tr>
            <?
            $i++;
		}
		?>
        </tbody>
        <?
    }
	exit();
}


if($action=="create_itemDesc_search_list_view_update")
{
	$data=explode("**",$data);
	$mst_id=$data[0];
	$store_id=$data[2];
	$issue_basis=$data[3];
	$req_id=$data[4];
	
	$sql = "SELECT a.id as prod_id, a.company_id, c.id as dtls_id, c.trans_id, a.item_group_id, a.item_description, a.product_name_details, a.brand_supplier, a.color, a.gmts_size, a.item_color, a.item_size, a.current_stock, a.unit_of_measure, b.id as prop_id, b.po_breakdown_id, b.quantity as order_issue_qnty, c.issue_qnty as item_iss_qty, c.rate, c.amount, c.floor_id, c.sewing_line, d.floor_id as floor, d.room, d.rack, d.self, d.bin_box, e.grouping, c.item_description, c.gmts_color_id, c.gmts_size_id, a.item_description as description,f.JOB_NO,f.STYLE_REF_NO 
	from product_details_master a, order_wise_pro_details b, inv_trims_issue_dtls c, inv_transaction d, wo_po_break_down e,WO_PO_DETAILS_MASTER f
	where b.po_breakdown_id=e.id and  d.id=c.trans_id and a.id=b.prod_id and b.trans_id=c.trans_id and b.dtls_id=c.id and e.JOB_ID = f.id  and a.item_category_id='4' and a.entry_form=24 and b.trans_type in(2) and b.entry_form in(25) and c.mst_id=$mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by b.id";
	//echo $sql;die;
	$result = sql_select($sql);
	$all_po_id_arr=array();$all_prod_id_arr=array();
	foreach($result as $row)
	{
		$all_po_id_arr[$row[csf("po_breakdown_id")]]=$row[csf("po_breakdown_id")];
		$all_prod_id_arr[$row[csf("prod_id")]]=$row[csf("prod_id")];
	}
	$all_po_id=implode(",",$all_po_id_arr);
	$all_prod_id=implode(",",$all_prod_id_arr);
	
	$cumilite_issue_sql=sql_select("select b.prod_id, b.po_breakdown_id, (case when b.trans_type in (2) then  b.quantity else 0 end) as issue_qnty, (case when b.trans_type in (4) then  b.quantity else 0 end) as issue_rtn_qnty, (case when b.trans_type in (3) then  b.quantity else 0 end) as rcv_rtn_qnty, (case when b.trans_type in (6) then  b.quantity else 0 end) as trans_out_qnty, c.floor_id as floor, c.room, c.rack, c.self, c.bin_box   
	from order_wise_pro_details b, inv_transaction c  
	where b.trans_id=c.id and b.status_active=1 and b.is_deleted=0 and b.entry_form in(25,49,73,78,112) and b.trans_type in (2,3,4,6) and c.transaction_type in (2,3,4,6) and b.po_breakdown_id in ($all_po_id) and c.prod_id in($all_prod_id) and c.store_id=$store_id and (case when c.transaction_type=2 then c.mst_id end)<>$mst_id");
	$cumilite_issue_data=array();
	foreach($cumilite_issue_sql as $row)
	{
		$cumilite_issue_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]][$row[csf("floor")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["issue_qnty"]+=$row[csf("issue_qnty")]-$row[csf("issue_rtn_qnty")];
		$cumilite_issue_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]][$row[csf("floor")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["rcv_rtn_qnty"]+=$row[csf("rcv_rtn_qnty")];
		$cumilite_issue_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]][$row[csf("floor")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["trans_out_qnty"]+=$row[csf("trans_out_qnty")];
	}
	
	$conversion_fac_sql=sql_select("select a.ID, b.CONVERSION_FACTOR from product_details_master a, lib_item_group b where a.item_group_id=b.id and a.id in($all_prod_id)");
	$conversion_fac_arr=array();
	foreach($conversion_fac_sql as $val)
	{
		$conversion_fac_arr[$val["ID"]]=$val["CONVERSION_FACTOR"];
	}
	$sql_trim = sql_select("select b.PO_BREAKDOWN_ID, b.PROD_ID, c.FLOOR_ID, c.ROOM, c.RACK, c.SELF, c.BIN_BOX, sum((case when b.entry_form in(24,73,78,112) and b.trans_type in(1,4,5) then b.quantity else 0 end)-(case when b.entry_form in(25,49,78,112) and b.trans_type in(2,3,6) then b.quantity else 0 end)) as BALANCE 
	from order_wise_pro_details b, inv_transaction c
	where b.trans_id=c.id and c.store_id=$store_id and c.prod_id in($all_prod_id) and c.item_category=4 and b.po_breakdown_id in($all_po_id) and c.status_active=1  and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0  
	group by b.po_breakdown_id, b.prod_id, c.FLOOR_ID, c.ROOM, c.RACK, c.SELF, c.BIN_BOX");//, c.FLOOR_ID, c.ROOM, c.RACK, c.SELF, c.BIN_BOX , c.floor_id as floor, c.room, c.rack, c.self, c.bin_box
	$order_prev_data=array();
	foreach($sql_trim as $row)
	{
		$store_wise_stock[$row["PROD_ID"]][$row["PO_BREAKDOWN_ID"]][$row["FLOOR_ID"]][$row["ROOM"]][$row["RACK"]][$row["SELF"]][$row["BIN_BOX"]]=$row["BALANCE"]*$conversion_fac_arr[$row["PROD_ID"]];
	}
	//echo '<pre>';print_r($store_wise_stock);
	if($issue_basis==3)
	{
		$req_sql=sql_select("select b.PO_ID, b.PRODUCT_ID, b.REQSN_QTY  from READY_TO_SEWING_REQSN b where b.MST_ID=$req_id and b.status_active=1 and b.is_deleted=0");
		$req_data_arr=array();
		foreach($req_sql as $row)
		{
			$req_data_arr[$row["PRODUCT_ID"]][$row["PO_ID"]]+=$row["REQSN_QTY"];
		}
		unset($req_sql);
	}
	else
	{
		$previous_rcv_sql = sql_select("select a.id, b.po_breakdown_id, b.quantity as recv_qty, c.floor_id as floor, c.room, c.rack, c.self, c.bin_box   
		from product_details_master a, order_wise_pro_details b, inv_transaction c
		where a.id=b.prod_id and b.trans_id=c.id and a.item_category_id='4' and a.entry_form=24 and b.trans_type in(1,5) and b.entry_form in(24,78,112) and b.po_breakdown_id in ($all_po_id) and c.prod_id in($all_prod_id) and c.store_id=$store_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.item_group_id");
		$previous_rcv_data=array();$prod_check=array();
		foreach($previous_rcv_sql as $row)
		{
			$previous_rcv_data[$row[csf("id")]][$row[csf("po_breakdown_id")]][$row[csf("floor")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]+=$row[csf("recv_qty")];
		}
	}
	
	
	

    $booking_sql = "select b.po_break_down_id, b.sensitivity, b.trim_group, b.uom, c.description, c.color_number_id as color_id, c.item_color, c.gmts_sizes as size_id, c.item_size, c.brand_supplier, c.rate, c.cons as book_qnty, c.item_ref, c.DESCRIPTION  
	from wo_booking_dtls b, wo_trim_book_con_dtls c, wo_po_break_down d 
	where d.id=c.po_break_down_id and b.id=c.wo_trim_booking_dtls_id  and c.cons>0 and b.status_active=1 and b.is_deleted=0 and d.id in ($all_po_id)
	order by b.trim_group, b.uom, c.description, c.color_number_id, c.item_color, c.gmts_sizes, c.item_size, c.brand_supplier, c.rate, c.cons";
	//echo $sql;die;
	$booking_result=sql_select($booking_sql);
	$booking_pi_data=array();
	foreach($booking_result as $row)
	{
		$trim_key=$row[csf("po_break_down_id")]."__".$row[csf("trim_group")]."__".$row[csf("color_id")]."__".$row[csf("item_color")]."__".$row[csf("size_id")]."__".$row[csf("item_size")]."__".$row["DESCRIPTION"]."__".$row[csf("brand_supplier")];
		$booking_pi_data[$trim_key]["item_ref"]=$row[csf("item_ref")];
	}

	$lib_floor_room_rack_self = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst","floor_room_rack_id","floor_room_rack_name");
	//$po_no_arr = return_library_array("select id, po_number from wo_po_break_down where id in($all_po_id)","id","po_number");
    $poDataArr = sql_select("select a.PO_BREAK_DOWN_ID, a.ARTICLE_NUMBER, a.SIZE_NUMBER_ID, a.COLOR_NUMBER_ID, b.PO_NUMBER from wo_po_color_size_breakdown a, wo_po_break_down b 
	where a.po_break_down_id=b.id and a.po_break_down_id in ($all_po_id) group by a.PO_BREAK_DOWN_ID, a.ARTICLE_NUMBER, a.SIZE_NUMBER_ID, a.COLOR_NUMBER_ID, b.PO_NUMBER");
    $po_arr = array();
    foreach ($poDataArr as $k => $v){
        $po_arr[$v['PO_BREAK_DOWN_ID']."*".$v['COLOR_NUMBER_ID']."*".$v['SIZE_NUMBER_ID']] = $v['ARTICLE_NUMBER'];
		$po_no_arr[$v['PO_BREAK_DOWN_ID']]=$v['PO_NUMBER'];
    }
    $color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$size_arr = return_library_array("select id, size_name from lib_size where status_active=1 and is_deleted=0","id","size_name");
	
	//echo "<pre>";print_r($previous_rcv_data);echo "<pre>";print_r($cumilite_issue_data);die;


	$cbo_prod_floor_sql = return_library_array("select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and company_id=".$result[0][csf('company_id')]." order by floor_name","id","floor_name");

	$prod_line_arr=return_library_array("select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and company_name=".$result[0][csf('company_id')]." and floor_name!=0  order by line_name","id","line_name");
    $i=1;
	?>
    <tbody>
    <?
    foreach ($result as $row)
    {
		$cu_issue=$balance=0;
		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		$conversion_factor=$trim_group_arr[$row[csf('item_group_id')]]['conversion_factor'];
		$current_issue=$row[csf('order_issue_qnty')]*$conversion_factor;
		//$current_stock=$row[csf('current_stock')]+$current_issue;
		$current_stock=$store_wise_stock[$row[csf('prod_id')]][$row[csf('po_breakdown_id')]][$row[csf("floor")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]+$current_issue;
		//$current_stock=$store_wise_stock[$row[csf('prod_id')]][$row[csf('po_breakdown_id')]][$row[csf("floor")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]];
		$cu_issue=$cumilite_issue_data[$row[csf('prod_id')]][$row[csf('po_breakdown_id')]][$row[csf("floor")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["issue_qnty"]*$conversion_factor;
		
		if($issue_basis==3)
		{
			$receive_qnty=$req_data_arr[$row[csf('prod_id')]][$row[csf('po_breakdown_id')]];
		}
		else
		{
			$receive_qnty=(($previous_rcv_data[$row[csf('prod_id')]][$row[csf('po_breakdown_id')]][$row[csf("floor")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]-$cumilite_issue_data[$row[csf('prod_id')]][$row[csf('po_breakdown_id')]][$row[csf("floor")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["rcv_rtn_qnty"]-$cumilite_issue_data[$row[csf('prod_id')]][$row[csf('po_breakdown_id')]][$row[csf("floor")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]][$row[csf("bin_box")]]["trans_out_qnty"])*$conversion_factor);
		}
		
		//$balance=$receive_qnty-$cu_issue;
		$balance=$receive_qnty-$cu_issue;

		$trim_key=$row[csf("po_breakdown_id")]."__".$row[csf("item_group_id")]."__".$row[csf("gmts_color_id")]."__".$row[csf("item_color")]."__".$row[csf("gmts_size_id")]."__".$row[csf("item_size")]."__".$row[csf("description")]."__".$row[csf("brand_supplier")];
		?>
        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick='change_color("tr_<? echo $i; ?>","<? echo $bgcolor;?>");' id="tr_<? echo $i; ?>" >
			<td width="120" id="po_no_<? echo $i; ?>" title="<? echo $row[csf('po_breakdown_id')];?>" style="word-break:break-all"><? echo $po_no_arr[$row[csf('po_breakdown_id')]]; ?></td>

			<td width="80" id="job_no_<? echo $i; ?>" title="<? echo $row[csf('po_breakdown_id')];?>" style="word-break:break-all"><? echo $row['JOB_NO']; ?></td>
			<td width="100" id="style_no_<? echo $i; ?>" title="<? echo $row[csf('po_breakdown_id')];?>" style="word-break:break-all"><? echo $row['STYLE_REF_NO']; ?></td>


			<td width="100" id="internal_ref_<? echo $i; ?>" title="<? echo $row[csf('po_breakdown_id')];?>" style="word-break:break-all"><? echo $row[csf('grouping')]; ?></td>

			<td width="80" align="center" id="article_no_<? echo $i; ?>" title="<? echo $row[csf('po_breakdown_id')];?>" style="word-break:break-all"><? echo $booking_pi_data[$trim_key]["item_ref"];//$po_arr[$row[csf('po_breakdown_id')]."*".$row[csf('color')]."*".$row[csf('gmts_size')]]; ?></td>
            <td  width="100" id="item_group_<? echo $i; ?>" title="<? echo $row[csf('item_group_id')];?>" style="word-break:break-all"><? echo $trim_group_arr[$row[csf('item_group_id')]]['name']; ?></td>
            <td width="50" id="prod_id_<? echo $i; ?>" align="center" title="<? echo $row[csf('prod_id')];?>" style="word-break:break-all"><? echo $row[csf('prod_id')];?></td>
			<td width="140" id="item_descrip_<? echo $i; ?>" title="<? echo $row[csf('prod_id')];?>" style="word-break:break-all"><? echo $row[csf('item_description')]; ?></td>  
			<td width="100" id="brand_supp_<? echo $i; ?>" title="<? echo $row[csf('brand_supplier')];?>" style="word-break:break-all"><? echo $row[csf('brand_supplier')]; ?></td>			
			<td width="80" id="gmtcolor_<? echo $i; ?>" title="<? echo $row[csf('color')];?>" style="word-break:break-all"><? echo $color_arr[$row[csf('color')]]; ?></td>
			<td width="80" id="gmtsize_<? echo $i; ?>" align="center" title="<? echo $row[csf('gmts_size')];?>" style="word-break:break-all"><? echo $size_arr[$row[csf('gmts_size')]]; ?></td>
			<td width="80" id="item_color_<? echo $i; ?>" title="<? echo $row[csf('item_color')];?>" style="word-break:break-all"><? echo $color_arr[$row[csf('item_color')]]; ?></td>
			<td width="80" id="item_size_<? echo $i; ?>" align="center" title="<? echo $row[csf('item_size')];?>" style="word-break:break-all"><? echo $row[csf('item_size')]; ?></td>			
			<td width="80" id="uom_<? echo $i; ?>" align="center" title="<? echo $row[csf('unit_of_measure')];?>" style="word-break:break-all"><? echo $unit_of_measurement[$row[csf('unit_of_measure')]]; ?></td>
			<td width="75" id="tdissueqnty_<? echo $i; ?>" align="center"><input type="text" name="issueqnty[]" id="issueqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" value="<? echo number_format($current_issue,4,'.',''); ?>"  onBlur="calculate(<? echo $i; ?>);" placeholder="<? echo number_format($current_issue,4,'.',''); ?>"/></td>
			<td width="75" id="tdreceiveqnty_<? echo $i; ?>" align="center" title="<? echo "Total Receive Qnty + Total Transfer Qnty"; ?>"><input type="text" name="receiveqnty[]" id="receiveqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" value="<? echo number_format($receive_qnty,4,'.',''); ?>"  readonly disabled /></td>
			<td width="75" id="tdcuissue_<? echo $i; ?>" align="center"><input type="text" name="cuissue[]" id="cuissue_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" value="<? echo number_format($cu_issue,4,'.',''); ?>"  readonly disabled /></td>
			<td width="75" id="tdyettoissue_<? echo $i; ?>" align="center"><input class="text_boxes_numeric"  name="yettoissue[]" id="yettoissue_<? echo $i; ?>" value="<? echo number_format($current_stock,4,'.',''); ?>" type="text" style="width:60px;" readonly disabled /></td>
            <td width="60" id="td_floor_<? echo $i; ?>">
			<?
            echo create_drop_down( "cbofloor_".$i, 60, $cbo_prod_floor_sql,"", 1, "-- Select Floor --", $row[csf('floor_id')],"fn_floor($i);",0,"","","","","","","cbofloor[]"); 
            ?>
			<td width="60" id="td_cboRecFloor_<? echo $i; ?>" align="center" title="<? echo $row[csf('floor')]; ?>">
				<? echo $lib_floor_room_rack_self[$row[csf('floor')]]; ?>
                <? //echo create_drop_down( "cboRecFloor_".$i, 60,$lib_floor_room_rack_self,"", 1, "--Select--",$row[csf('floor')],"",1,"","","","","","","cboRecFloor[]"); ?>
            </td>
            <td width="60" id="td_cboRecRoom_<? echo $i; ?>" align="center" title="<? echo $row[csf('room')]; ?>">
                <? echo $lib_floor_room_rack_self[$row[csf('room')]]; ?>
                <? //echo create_drop_down( "cboRecRoom_".$i, 60,$lib_floor_room_rack_self,"", 1, "--Select--", $row[csf('room')], "",1,"","","","","","","cboRecRoom[]" ); ?>
            </td>
            <td width="60" id="td_cboRecRack_<? echo $i; ?>" align="center" title="<? echo $row[csf('rack')]; ?>">
                <? echo $lib_floor_room_rack_self[$row[csf('rack')]]; ?>
                <? //echo create_drop_down( "cboRecRack_".$i, 60,$lib_floor_room_rack_self,"", 1, "--Select--", $row[csf('rack')], "",1,"","","","","","","cboRecRack[]" ); ?>
            </td>
            <td width="60" id="td_cboRecShelf_<? echo $i; ?>" align="center" title="<? echo $row[csf('self')]; ?>">
                <? echo $lib_floor_room_rack_self[$row[csf('self')]]; ?>
                <? //echo create_drop_down( "cboRecShelf_".$i, 60,$lib_floor_room_rack_self,"", 1, "--Select--", $row[csf('self')], "",1,"","","","","","","cboRecShelf[]" ); ?>
            </td>
            <td width="60" id="td_cboRecBin_<? echo $i; ?>" align="center" title="<? echo $row[csf('bin_box')]; ?>">
                <? echo $lib_floor_room_rack_self[$row[csf('bin_box')]]; ?>
                <? //echo create_drop_down( "cboRecBin_".$i, 60,$lib_floor_room_rack_self,"", 1, "--Select--", $row[csf('bin_box')], "",1,"","","","","","","cboRecBin[]" ); ?>
            </td>
            <td width="100" id="td_line_<? echo $i; ?>">
            <?
            echo create_drop_down( "cboline_".$i, 100, $prod_line_arr,"", 1, "-- Select --", $row[csf('sewing_line')],"fn_line($i);",0,"","","","","","","cboline[]"); 
            ?>
            </td>
            <td width="100" id="tdglobalstock_<? echo $i; ?>" align="center" title="<?= "Global Stock = ".$row[csf('current_stock')];?>" ><input class="text_boxes_numeric"  name="globalstock[]" id="globalstock_<? echo $i; ?>" value="<? echo number_format($current_stock,4,'.',''); ?>" type="text" style="width:70px;" readonly disabled />
            <input type="hidden" name="updatedtlsid[]" id="updatedtlsid_<? echo $i; ?>" value="<? echo $row[csf('dtls_id')]; ?>" readonly>
            <input type="hidden" name="updatetransid[]" id="updatetransid_<? echo $i; ?>" value="<? echo $row[csf('trans_id')]; ?>" readonly>
            <input type="hidden" name="previousprodid[]" id="previousprodid_<? echo $i; ?>" value="<? echo $row[csf('prod_id')]; ?>" readonly>
            </td>
		</tr>
		<?
		$i++;
    }
	?>
    </tbody>
    <?
	
	exit();
}

if($action=="create_itemDesc_search_list_view_on_booking")
{
	$rack_shelf_array=array();
	$dataArray=sql_select("select a.prod_id, a.rack_no, a.self_no from inv_goods_placement a, inv_receive_master b where a.mst_id=b.id and a.entry_form=24 and b.entry_form=24 and b.booking_id=$data and b.booking_without_order=1 and b.receive_basis=2 and b.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.prod_id, a.rack_no, a.self_no");
	foreach($dataArray as $row)
	{
		$rack_shelf_array[$row[csf('prod_id')]].=$row[csf('rack_no')]."**".$row[csf('self_no')].",";
	}
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");

	$sql = "select a.id, a.item_group_id, a.product_name_details, a.brand_supplier, a.color, a.gmts_size, a.item_color, a.item_size, a.current_stock, sum(b.receive_qnty) as recv_qty from inv_receive_master r, product_details_master a, inv_trims_entry_dtls b where r.id=b.mst_id and a.id=b.prod_id and a.item_category_id=4 and a.entry_form=24 and r.entry_form=24 and r.booking_id=$data and r.booking_without_order=1 and r.receive_basis=2 and r.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.current_stock>0 
	group by a.id, a.item_group_id, a.product_name_details, a.brand_supplier, a.color, a.gmts_size, a.current_stock, a.item_color, a.item_size order by a.item_group_id";
	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="490" class="rpt_table" id="tbl_list_search">
		<thead>
			<th width="40">Prod. ID</th>
			<th width="70">Item Group</th>
			<th width="100">Item Desc.</th>               
			<th width="60">Item Color</th>
			<th width="50">Item Size</th>
			<th width="50">Rack</th>
            <th width="50">Shelf</th>
			<th>Recv. Qty.</th>
		</thead>
		<?
		$i=1;
		foreach ($result as $row)
		{  
			$rack_shelf=explode(",",substr($rack_shelf_array[$row[csf('id')]],0,-1));
			//print_r($rack_shelf);
			foreach($rack_shelf as $value)
			{
				$value=explode("**",$value);
				$rack=$value[0];
				$shelf=$value[1];
				
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			
				$data=$row[csf('id')]."**".$row[csf('item_group_id')]."**".$row[csf('product_name_details')]."**".$color_arr[$row[csf('item_color')]]."**".$row[csf('color')]."**".$row[csf('item_size')]."**".$row[csf('gmts_size')]."**".$row[csf('brand_supplier')]."**".$trim_group_arr[$row[csf('item_group_id')]]['uom']."**".$rack."**".$shelf."**".$row[csf('item_color')]."**".$row[csf('current_stock')]; 
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick='set_form_data("<? echo $data ?>");change_color("tr_<? echo $i; ?>","<? echo $bgcolor;?>");' id="tr_<? echo $i; ?>" > 
					<td width="40"><? echo $row[csf('id')]; ?></td>
					<td width="70"><p><? echo $trim_group_arr[$row[csf('item_group_id')]]['name']; ?></p></td>  
					<td width="100"><p><? echo $row[csf('product_name_details')]; ?></p></td>             
					<td width="60"><p><? echo $color_arr[$row[csf('item_color')]]; ?>&nbsp;</p></td>
					<td width="50"><p><? echo $row[csf('item_size')]; ?>&nbsp;</p></td>
					<td width="50"><p><? echo $rack; ?>&nbsp;</p></td>
					<td width="50"><p><? echo $shelf; ?>&nbsp;</p></td>
					<td align="right"><? echo number_format($row[csf('recv_qty')],2,'.',''); ?></td>
				</tr>
			<?
			$i++;
			}
		}
		?>
	</table>
<?	
exit();
}

if ($action=="po_search_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);  

	?> 
	<script> 
		
		function fn_show_check()
		{			
			show_list_view ( $('#txt_job_no').val()+'_'+$('#txt_order_no').val()+'_'+<? echo $cbo_company_id; ?>+'_'+$('#cbo_buyer_name').val()+'_'+'<? echo $all_po_id; ?>'+'_'+$('#cbo_year').val()+'_'+$('#txt_style_ref_no').val()+'_'+$('#txt_internal_ref_no').val(), 'create_po_search_list_view', 'search_div', 'trims_issue_multi_ref_controller', 'setFilterGrid(\'tbl_list_search\',-1);hidden_field_reset();');
			set_all();
		}

		
		var selected_id = new Array(); var selected_name = new Array(); var buyer_id=''; var buyer_name='';
		
		function check_all_data() 
		 {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) 
			{
				if($('#search'+i).css('display')!='none')
				{
					js_set_value( i );
				}
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function set_all()
		{
			var old=document.getElementById('txt_po_row_id').value; 
			if(old!="")
			{   
				old=old.split(",");
				for(var k=0; k<old.length; k++)
				{   
					js_set_value( old[k] ) 
				} 
			}
		}
		
		function js_set_value( str ) 
		{
			var color=document.getElementById('search' + str ).style.backgroundColor;
			var txt_buyer=$('#txt_buyer' + str).val();
			
			//if(color!='yellow' && selected_id.length>0 && $('#txt_buyer' + str).css('display') != 'none')
			if(color!='yellow' && selected_id.length>0 && $('#txt_buyer' + str).is(':visible'))
			{
				if(buyer_name=="")
				{
					buyer_name=txt_buyer;
				}
				else if(buyer_name*1!=txt_buyer*1)
				{
					alert("Buyer Mix Not Allowed");
					return;
				}
			}
			
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			
			buyer_id=$('#txt_buyer' + str).val();

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hidden_order_id').val(id);
			$('#hidden_order_no').val(name);
			$('#hide_buyer').val(buyer_id);
			
		}
		
		function hidden_field_reset()
		{
			$('#hidden_order_id').val('');
			$('#hidden_order_no').val( '' );
			$('#hide_buyer').val( '' );
			selected_id = new Array();
			selected_name = new Array();
		}
		
		function fn_empty_job_order(str)
		{
			if(str==1)
			{
				$('#txt_order_no').val('');
			}
			else
			{
				$('#txt_job_no').val( '' );
			}
		}
		
    </script>

</head>
<body>
	<div align="center">
        <form name="searchdescfrm" id="searchdescfrm" autocomplete=off>
            <fieldset style="width:780px;margin-left:5px">
                <input type="hidden" name="hidden_order_id" id="hidden_order_id" class="text_boxes" value="">
                <input type="hidden" name="hidden_order_no" id="hidden_order_no" class="text_boxes" value="">
                <input type="hidden" name="hide_buyer" id="hide_buyer" class="text_boxes" value="">
                <table cellpadding="0" cellspacing="0" width="630" class="rpt_table" border="1" rules="all">
                    <thead>
                        <th width="180">Buyer</th>
                        <th width="120">Job Year</th>
                        <th width="100">Job No</th>
                        <th width="100">Order No</th>
                        <th width="100">Style Ref</th>
                        <th width="100">Internal Ref</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                            <input type="hidden" name="po_id" id="po_id" value="">
                        </th> 
                    </thead>
                    <tr class="general">
                        <td align="center">
                            <?
                                echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $buyer_name, "","" ); 
                            ?>       
                        </td>
                        <td>
                        	<?
								echo create_drop_down( "cbo_year", 100, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
							?>
                        </td>
                        <td align="center">
                        	<input type="text" style="width:100px" class="text_boxes"  name="txt_job_no" id="txt_job_no" placeholder="Write Job No" onKeyDown="fn_empty_job_order(1)" />	
                        </td>                 
                        <td align="center">				
                            <input type="text" style="width:100px" class="text_boxes"  name="txt_order_no" id="txt_order_no" placeholder="Write Order No" onKeyDown="fn_empty_job_order(2)" />	
                        </td> 
                        <td align="center">				
                            <input type="text" style="width:100px" class="text_boxes" name="txt_style_ref_no" id="txt_style_ref_no" placeholder="Write" />	
                        </td>	
						<td align="center">				
                            <input type="text" style="width:100px" class="text_boxes" name="txt_internal_ref_no" id="txt_internal_ref_no" placeholder="Write" />	
                        </td>				
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="fn_show_check()" style="width:100px;" />
                        </td>
                    </tr>
                </table>
            <div id="search_div" style="margin-top:10px"></div>
            </fieldset>
        </form>
    </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_po_search_list_view")
{
	$data = explode("_",$data);
	$job_no=trim($data[0]);
	$order_no=trim($data[1]);
	$company_id =$data[2];
	$buyer_id =$data[3];
	$all_po_id=$data[4];
	$job_year=$data[5];
	$style_ref_no=$data[6];
	$internal_ref_no=$data[7];
	//print_r($data) ;
	//echo $job_year;die;
	
	$sql_cond="";
	if($job_no!="") $sql_cond.=" and a.job_no like '%$job_no%'";
	if($order_no!="") $sql_cond.=" and b.po_number like '%$order_no%'";
	if($style_ref_no!="") $sql_cond.=" and a.style_ref_no like '%$style_ref_no%'";
	if($internal_ref_no!="") $sql_cond.=" and b.grouping like '%$internal_ref_no%'";
	if($all_po_id!="") $sql_cond.=" and b.id in($all_po_id)";
	if($buyer_id >0) $sql_cond.=" and a.buyer_name=$buyer_id";
	if($job_year>0)
	{
		if($db_type==0)
		{
			$year_cond=" and YEAR(a.insert_date)=$job_year";
		} 
		else 
		{
			$year_cond=" and to_char(a.insert_date,'YYYY')=$job_year";
		}
	}
	if($db_type==0) $year_field="YEAR(a.insert_date) as year,"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year,";
	else $year_field="";//defined Later
	
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer","id","buyer_name");
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand",'id','brand_name');
	$season_arr=return_library_array( "select id, season_name from lib_buyer_season",'id','season_name');
	if($job_no!="")
	{
		if($db_type==0)
		{
			$sql = "select a.job_no_prefix_num, a.job_no, a.style_ref_no, a.buyer_name, a.order_uom, $year_field group_concat(b.id) as id, group_concat(b.po_number) as po_number, sum(a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, max(b.pub_shipment_date) as pub_shipment_date, a.season_buyer_wise, a.season_year, a.brand_id, b.grouping
			from wo_po_details_master a, wo_po_break_down b 
			where a.id=b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id $sql_cond  $year_cond
			group by a.job_no_prefix_num, a.job_no, a.style_ref_no, a.buyer_name, a.order_uom, a.insert_date, a.season_buyer_wise, a.season_year, a.brand_id,b.grouping";
		}
		else
		{
			// $sql = "select a.job_no_prefix_num, a.job_no, a.style_ref_no, a.buyer_name, a.order_uom, $year_field listagg(cast(b.id as varchar(4000)),',') within group(order by b.id) as id, listagg(cast( b.po_number as varchar(4000)),',') within group(order by b.id) as po_number, sum(a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, max(b.pub_shipment_date) as  pub_shipment_date, a.season_buyer_wise, a.season_year, a.brand_id , b.grouping
			// from wo_po_details_master a, wo_po_break_down b 
			// where a.id=b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id $sql_cond  $year_cond
			// group by a.job_no_prefix_num, a.job_no, a.style_ref_no, a.buyer_name, a.order_uom, a.insert_date, a.season_buyer_wise, a.season_year, a.brand_id,b.grouping";

			$sql = "select a.job_no_prefix_num, a.job_no, a.style_ref_no, a.buyer_name, a.order_uom, $year_field b.id as id,b.po_number as po_number, sum(a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, max(b.pub_shipment_date) as  pub_shipment_date, a.season_buyer_wise, a.season_year, a.brand_id , b.grouping
			from wo_po_details_master a, wo_po_break_down b 
			where a.id=b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id $sql_cond  $year_cond
			group by a.job_no_prefix_num, a.job_no, a.style_ref_no, a.buyer_name, a.order_uom, a.insert_date, a.season_buyer_wise, a.season_year, a.brand_id,b.grouping,b.id,b.po_number ";

		}
		
	}
	else
	{
		$sql = "select a.job_no_prefix_num, a.job_no, a.style_ref_no, a.buyer_name, a.order_uom, $year_field b.id, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date, a.season_buyer_wise, a.season_year, a.brand_id,b.grouping
		from wo_po_details_master a, wo_po_break_down b 
		where a.id=b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id $sql_cond $year_cond";
	}
	
	//echo $sql;
	?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="940" class="rpt_table" align="left">
            <thead>
                <th width="30">SL</th>
                <th width="100">Buyer</th>
                <th width="40">Year</th>
                <th width="50">Job No</th>
                <th width="100">Int Ref</th>
                <th width="100">Style Ref.</th>
                <th width="120">PO No</th>
                <th width="80">PO Quantity</th>
                <th width="50">UOM</th>
                <th width="80">Shipment Date</th>
                <th width="60">Brand</th>
                <th width="60">Season</th>
                <th>Season Year</th>
            </thead>
        </table>
        <div style="width:960px; overflow-y:scroll; max-height:240px;" id="buyer_list_view">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="940" class="rpt_table" id="tbl_list_search" >
            <?
				$i=1; $po_row_id='';
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $selectResult)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
						
					if(in_array($selectResult[csf('id')],$hidden_po_id)) 
					{
						if($po_row_id=="") $po_row_id=$i; else $po_row_id.=",".$i;
					}
							
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
                        <td width="30" align="center">
                            <? echo $i; ?>
                            <input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $i ?>" value="<? echo $selectResult[csf('id')]; ?>"/>
                            <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $selectResult[csf('po_number')]; ?>"/>
                            <input type="hidden" name="txt_buyer" id="txt_buyer<?php echo $i ?>" value="<? echo $selectResult[csf('buyer_name')]; ?>"/>
                        </td>
                        <td width="100"><p><? echo $buyer_arr[$selectResult[csf('buyer_name')]]; ?></p></td>	
                        <td align="center" width="40"><p><? echo $selectResult[csf('year')]; ?></p></td>
                        <td align="center" width="50"><p><? echo $selectResult[csf('job_no_prefix_num')]; ?></p></td>
                        <td width="100"><p><? echo $selectResult[csf('grouping')]; ?></p></td>
                        <td width="100"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
                        <td width="120"><p><? echo $selectResult[csf('po_number')]; ?></p></td>
                        <td width="80" align="right"><? echo $selectResult[csf('po_qnty_in_pcs')]; ?>&nbsp;</td> 
                        <td width="50" align="center"><p><? echo $unit_of_measurement[$selectResult[csf('order_uom')]]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($selectResult[csf('pub_shipment_date')]); ?></td>	
                        <td width="60" align="center"><? echo $brand_arr[$selectResult[csf('brand_id')]]; ?></td>	
                        <td width="60" align="center"><? echo $season_arr[$selectResult[csf('season_buyer_wise')]]; ?></td>	
                        <td align="center"><? echo $year[$selectResult[csf('season_year')]]; ?></td>                        
                    </tr>
                    <?
                    $i++;
				}
			?>
				<input type="hidden" name="txt_po_row_id" id="txt_po_row_id" value="<? echo $po_row_id; ?>"/>
            </table>
        </div>
         <table width="620" cellspacing="0" cellpadding="0" style="border:none" align="center">
            <tr>
                <td align="center" height="30" valign="bottom">
                    <div style="width:100%"> 
                        <div style="width:50%; float:left" align="left">
                            <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                        </div>
                        <div style="width:50%; float:left" align="left">
                            <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                        </div>
                    </div>
                </td>
            </tr>
        </table>
	</div>           
<?
	
exit();
}

if ($action=="booking_search_popup")
{
    echo load_html_head_contents("Sample Trims Booking Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);  
	?> 
	<script> 
		function js_set_value( id, name, buyer_id, po_id, product_id, po_number ) 
		{
			$('#hidden_booking_id').val(id);
			$('#hidden_booking_no').val(name);
			$('#hide_buyer').val(buyer_id);
			$('#hidden_po_id').val(po_id);
			$('#hidden_prod_id').val(product_id);
			$('#hidden_po_no').val(po_number);
			parent.emailwindow.hide();
		}
    </script>
</head>
<body>
	<div align="center">
        <form name="searchdescfrm" id="searchdescfrm" autocomplete=off>
            <fieldset style="width:780px;margin-left:5px">
                <input type="hidden" name="hidden_booking_id" id="hidden_booking_id" class="text_boxes" value="">
                <input type="hidden" name="hidden_booking_no" id="hidden_booking_no" class="text_boxes" value="">
                <input type="hidden" name="hide_buyer" id="hide_buyer" class="text_boxes" value="">
                <input type="hidden" name="hidden_po_id" id="hidden_po_id" class="text_boxes" value="">
                <input type="hidden" name="hidden_po_no" id="hidden_po_no" class="text_boxes" value="">
                <input type="hidden" name="hidden_prod_id" id="hidden_prod_id" class="text_boxes" value="">
                <table cellpadding="0" cellspacing="0" width="760" class="rpt_table" border="1" rules="all">
                    <thead>
                        <th>Buyer</th>
                        <th>Booking/Requisition No</th>
                        <th>Date Range</th>
                        <th><input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" /></th> 
                    </thead>
                    <tr class="general">
                        <td align="center">
                            <?
                                echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $buyer_name, "","" ); 
                            ?>       
                        </td>
                      	<td align="center">				
                            <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                        </td>      
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px">To
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px">
                        </td>            
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $cbo_company_id; ?>+'_'+<? echo $cbo_basis; ?>, 'create_trims_booking_search_list_view', 'search_div', 'trims_issue_multi_ref_controller', 'setFilterGrid(\'list_view\',-1);')" style="width:100px;" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </table>
            <div id="search_div" style="margin-top:10px"></div>
            </fieldset>
        </form>
    </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_trims_booking_search_list_view")
{
	$data = explode("_",$data);
	$buyer_id =$data[0];
	$search_string="%".trim($data[1]);
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];
	$cbo_basis =$data[5];
	if($cbo_basis==2)
	{
		if(str_replace("'","",$buyer_id)==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond=" and buyer_id=$buyer_id";
		}
		
		if($start_date!="" && $end_date!="")
		{
			if($db_type==0)
			{
				$date_cond="and booking_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
			}
			else
			{
				$date_cond="and booking_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";	
			}
		}
		else
		{
			$date_cond="";
		}
	
		if($db_type==0) $year_field="YEAR(insert_date)"; 
		else if($db_type==2) $year_field="to_char(insert_date,'YYYY')";
		else $year_field="";//defined Later
		
		$sql = "select id, buyer_id, booking_no_prefix_num, booking_no, booking_date, delivery_date, currency_id, source, supplier_id, $year_field as year FROM wo_non_ord_samp_booking_mst WHERE company_id=$company_id and status_active =1 and is_deleted=0 and item_category=4 and booking_no like '$search_string' $buyer_id_cond $date_cond order by id";
		//echo $sql;
		$result = sql_select($sql);
		$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
		$buyer_arr = return_library_array("select id, buyer_name from lib_buyer","id","buyer_name");
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="772" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="80">Booking No</th>
				<th width="60">Year</th>
				<th width="110">Buyer</th>
				<th width="90">Booking Date</th>               
				<th width="130">Supplier</th>
				<th width="90">Delivary date</th>
				<th width="80">Source</th>
				<th>Currency</th>
			</thead>
		</table>
		<div style="width:772px; max-height:250px; overflow-y:scroll" id="list_container_batch" align="left">	 
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table" id="list_view">  
			<?
				$i=1;
				foreach ($result as $row)
				{  
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('booking_no')]; ?>','<? echo $row[csf('buyer_id')]; ?>');"> 
						<td width="30"><? echo $i; ?></td>
						<td width="80"><p>&nbsp;&nbsp;<? echo $row[csf('booking_no_prefix_num')]; ?></p></td>
						<td width="60" align="center"><? echo $row[csf('year')]; ?></td>
						<td width="110"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
						<td width="90" align="center"><? echo change_date_format($row[csf('booking_date')]); ?>&nbsp;</td>               
						<td width="130"><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></p></td>
						<td width="90" align="center"><? echo change_date_format($row[csf('delivery_date')]); ?>&nbsp;</td>
						<td width="80"><p><? echo $source[$row[csf('source')]]; ?></p></td>
						<td><p><? echo $currency[$row[csf('currency_id')]]; ?></p></td>
					</tr>
				<?
				$i++;
				}
				?>
			</table>
		</div>
		<?
	}
	else
	{

		if(str_replace("'","",$buyer_id)==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1){
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and c.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
				}else{
				$buyer_id_cond="";
			}
		}else{
			$buyer_id_cond=" and c.buyer_name=$buyer_id";
		}

		if($start_date!="" && $end_date!=""){
			if($db_type==0){
				$date_cond="and a.READY_SEWING_DATE between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
				$date_cond_req="and a.REQUISITION_DATE between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
			}else{
				$date_cond="and a.READY_SEWING_DATE between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";	
				$date_cond_req="and a.REQUISITION_DATE between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
				
			}
		}else{
			$date_cond="";
		}
		//$po_sql="select a.JOB_NO, a.STYLE_REF_NO, a.BUYER_NAME, b.ID as PO_ID, b.PO_NUMBER, b.PO_QUANTITY, (a.TOTAL_SET_QNTY*b.PO_QUANTITY) as PO_QNTY_IN_PCS, d.ID as PRODUCT_ID, d.ITEM_GROUP_ID, d.ITEM_DESCRIPTION, d.ITEM_COLOR, d.ITEM_SIZE, d.UNIT_OF_MEASURE, c.ID as DTLS_ID, c.CONS as RCV_QNTY, c.STOCK_QNTY as STOCK_QNTY, c.REQSN_QTY from wo_po_details_master a, wo_po_break_down b, ready_to_sewing_reqsn c, product_details_master d where a.job_no=b.job_no_mst and b.id=c.po_id and c.product_id=d.id and c.entry_form in(357) and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.mst_id=$update_id";
		$req_sql="SELECT a.ID, a.SEW_NUMBER as REQ_NO, a.COMPANY_ID, a.READY_SEWING_DATE as REQUISITION_DATE, c.BUYER_NAME, listagg(cast(b.PO_ID as varchar(4000)),',') within group(order by b.PO_ID) as PO_ID, listagg(cast(d.PO_NUMBER as varchar(4000)),',') within group(order by d.PO_NUMBER) as PO_NUMBER, listagg(cast(b.PRODUCT_ID as varchar(4000)),',') within group(order by b.PRODUCT_ID) as PRODUCT_ID, sum(b.REQSN_QTY) as REQSN_QTY, 1 as TYPE
		from  READY_TO_SEWING_MST a, READY_TO_SEWING_REQSN b, WO_PO_DETAILS_MASTER c, WO_PO_BREAK_DOWN d 
		WHERE a.id=b.mst_id and c.job_no=d.job_no_mst and d.id=b.po_id and a.ID like '$search_string' and a.COMPANY_ID=$company_id $buyer_id_cond $date_cond and b.ENTRY_FORM in(357,377) and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and c.STATUS_ACTIVE=1 and c.IS_DELETED=0 and d.STATUS_ACTIVE=1 and d.IS_DELETED=0 
		group by a.id, a.SEW_NUMBER, a.company_id, a.READY_SEWING_DATE, c.buyer_name
		union all
		select a.ID, a.REQ_NO as REQ_NO, a.COMPANY_ID, a.REQUISITION_DATE as REQUISITION_DATE, c.BUYER_NAME, listagg(cast(b.PO_ID as varchar(4000)),',') within group(order by b.PO_ID) as PO_ID, listagg(cast(d.PO_NUMBER as varchar(4000)),',') within group(order by d.PO_NUMBER) as PO_NUMBER, listagg(cast(b.PRODUCT_ID as varchar(4000)),',') within group(order by b.PRODUCT_ID) as PRODUCT_ID, sum(b.REQSN_QTY) as REQSN_QTY, 2 as TYPE
		from READY_TO_SEWING_REQSN_MST a, READY_TO_SEWING_REQSN b, WO_PO_DETAILS_MASTER c, WO_PO_BREAK_DOWN d 
		where a.id=b.mst_id and c.job_no=d.job_no_mst and d.id=b.po_id and a.req_no like '$search_string' and a.COMPANY_ID=$company_id $buyer_id_cond $date_cond_req and b.ENTRY_FORM in(357,377) and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and c.STATUS_ACTIVE=1 and c.IS_DELETED=0 and d.STATUS_ACTIVE=1 and d.IS_DELETED=0
		group by a.id, a.REQ_NO, a.company_id, a.REQUISITION_DATE, c.buyer_name
		order by ID desc";
		//echo $req_sql;
		$result=sql_select($req_sql);
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="772" class="rpt_table">
			<thead>
				<th width="50">SL</th>
				<th width="250">Buyer</th>
				<th width="200">Requisition No</th>
				<th>Requisition Date</th>
			</thead>
		</table>
		<div style="width:772px; max-height:250px; overflow-y:scroll" id="list_container_batch" align="left">	 
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table" id="list_view">  
			<?
				$i=1;
				$buyer_arr = return_library_array("SELECT buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name",'id','buyer_name');
				$prev_req_sql="select a.BOOKING_NO, sum(b.CONS_QUANTITY) as CONS_QUANTITY from INV_ISSUE_MASTER a, INV_TRANSACTION b where a.id=b.mst_id and a.entry_form=25 and a.ISSUE_BASIS=3 and b.transaction_type=2 and b.item_category=4 and a.status_active=1 and b.status_active=1 and a.company_id=$company_id
				group by a.BOOKING_NO";
				$prev_req_result=sql_select($prev_req_sql);
				$prev_req_data=array();
				foreach($prev_req_result as $val)
				{
					$prev_req_data[$val["BOOKING_NO"]]=$val["CONS_QUANTITY"];
				}
				//echo $prev_req_sql;
				foreach ($result as $row)
				{
					$bal_qnty=$row['REQSN_QTY']-$prev_req_data[$row["REQ_NO"]];
					if($bal_qnty>0)
					{  
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row['ID']; ?>,'<? echo $row['REQ_NO']; ?>','<? echo $row['BUYER_NAME']; ?>','<? echo $row['PO_ID']; ?>','<? echo $row['PRODUCT_ID']; ?>','<? echo $row['PO_NUMBER']; ?>');">
							<td width="50" align="center"><? echo $i; ?></td>
							<td width="250"><p>&nbsp;<? echo $buyer_arr[$row['BUYER_NAME']]; ?></p></td>
							<td width="200" align="center"><p>&nbsp;<? echo $row['REQ_NO']; ?></p></td>
							<td align="center" title="<? echo $row['REQSN_QTY']."=". $bal_qnty;?>"><? echo change_date_format($row[csf('REQUISITION_DATE')]); ?>&nbsp;</td>               
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


if ($action=="get_trim_cum_info")
{
	$data=explode("**",$data);
	$po_id=$data[0];
	$prod_id=$data[1];
	
	$stockData=sql_select("select a.current_stock, b.conversion_factor from product_details_master a, lib_item_group b where a.item_group_id=b.id and a.id=$prod_id");
	$current_stock=$stockData[0][csf('current_stock')];
	$conversion_factor=$stockData[0][csf('conversion_factor')];
	if($conversion_factor<=0) $conversion_factor=1;
	
	
	$dataArray=sql_select("select sum(case when trans_type in(1,4,5) then quantity end) as recv_qnty, sum(case when trans_type in(2,3,6) then quantity end) as issue_qnty from order_wise_pro_details where po_breakdown_id in($po_id) and prod_id='$prod_id' and status_active=1 and is_deleted=0 and entry_form in(24,25,49,73,78,112)");
	
	$recv_qnty=$dataArray[0][csf('recv_qnty')]*$conversion_factor;
    $yet_to_issue = ($recv_qnty-($dataArray[0][csf('issue_qnty')]*$conversion_factor));
	

    echo "$('#txt_received_qnty').val(".number_format(($recv_qnty),2,".","").");\n";
    echo "$('#txt_cumulative_issued').val('".number_format(($dataArray[0][csf('issue_qnty')]*$conversion_factor),2,".","")."');\n";
    echo "$('#txt_yet_to_issue').val('".number_format(($yet_to_issue),2,".","")."');\n";
	echo "$('#txt_global_stock').val('".number_format(($current_stock),2,".","")."');\n";
	exit();
}

if ($action=="get_trim_cum_info_for_trims_booking")
{
	$data=explode("**",$data);
	$txt_booking_id=$data[0];
	$prod_id=$data[1];
	
	
	$stockData=sql_select("select a.current_stock, b.conversion_factor from product_details_master a, lib_item_group b where a.item_group_id=b.id and a.id=$prod_id");
	$current_stock=$stockData[0][csf('current_stock')];
	$conversion_factor=$stockData[0][csf('conversion_factor')];
	if($conversion_factor<=0) $conversion_factor=1;

	$recv_qnty=return_field_value("sum(b.receive_qnty) as recv_qnty","inv_receive_master a, inv_trims_entry_dtls b","a.id=b.mst_id and a.receive_basis=2 and entry_form=24 and a.item_category=4 and a.booking_id=$txt_booking_id and a.booking_without_order=1 and b.prod_id=$prod_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","recv_qnty");
	$recv_qnty=$recv_qnty*$conversion_factor;
	
	$iss_qnty=return_field_value("sum(b.issue_qnty) as iss_qnty","inv_issue_master a, inv_trims_issue_dtls b","a.id=b.mst_id and a.issue_basis=2 and entry_form=25 and item_category=4 and booking_id=$txt_booking_id and b.prod_id=$prod_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","iss_qnty");
	
    $yet_to_issue = $recv_qnty-$iss_qnty;
	
    echo "$('#txt_received_qnty').val(".$recv_qnty.");\n";
    echo "$('#txt_cumulative_issued').val('".$iss_qnty."');\n";
    echo "$('#txt_yet_to_issue').val('".$yet_to_issue."');\n";
	echo "$('#txt_global_stock').val('".$current_stock."');\n";
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$trims_issue_num=''; $trims_update_id='';
		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later
			
			$id = return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master", $con);
			$new_system_id = explode("*", return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master",$con,1,$cbo_company_id,'TIE',25,date("Y",time()) ));
			
			$field_array="id, issue_number_prefix, issue_number_prefix_num,issue_number, issue_purpose, entry_form, item_category, company_id, issue_basis, booking_id, booking_no, issue_date, challan_no, store_id, knit_dye_source, knit_dye_company, location_id, floor_id, remarks, extra_status, is_multi, inserted_by, insert_date";
			
			$data_array="(".$id.",'".$new_system_id[1]."',".$new_system_id[2].",'".$new_system_id[0]."',".$cbo_issue_purpose.",25,4,".$cbo_company_id.",".$cbo_basis.",".$txt_booking_id.",".$txt_booking_no.",".$txt_issue_date.",".$txt_issue_chal_no.",".$cbo_store_name.",".$cbo_sewing_source.",".$cbo_sewing_company.",".$cbo_sewing_location_name.",".$cbo_floor_unit_name.",".$txt_remarks.",".$cbo_extra_status.",'1',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$trims_issue_num=$new_system_id[0];
			$trims_update_id=$id;
		}
		else
		{
			$field_array_update="issue_purpose*issue_basis*booking_id*booking_no*issue_date*challan_no*store_id*knit_dye_source*knit_dye_company*location_id*floor_id*remarks*extra_status*updated_by*update_date";
			$data_array_update=$cbo_issue_purpose."*".$cbo_basis."*".$txt_booking_id."*".$txt_booking_no."*".$txt_issue_date."*".$txt_issue_chal_no."*".$cbo_store_name."*".$cbo_sewing_source."*".$cbo_sewing_company."*".$cbo_sewing_location_name."*".$cbo_floor_unit_name."*".$txt_remarks."*".$cbo_extra_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			
			$trims_issue_num=str_replace("'","",$txt_system_id);
			$trims_update_id=str_replace("'","",$update_id);
		}
		
		
		$item_wise_data=array();$item_order_qnty=array();$all_prod_id=$all_po_id=$all_item_group_id="";
		for($i=1;$i<=$tot_row; $i++)
		{
			$po_id="po_id".$i;
			$prod_id="prod_id".$i;
			$cboitemgroup="cboitemgroup".$i;
			$itemdescription="itemdescription".$i;
			$brandSupref="brandSupref".$i;
			$itemcolorid="itemcolorid".$i;
			$itemsizeid="itemsizeid".$i;
			$gmtscolorid="gmtscolorid".$i;
			$gmtssizeId="gmtssizeId".$i;
			$cbouom="cbouom".$i;
			$issueqnty="issueqnty".$i;
			$receiveqnty="receiveqnty".$i;
			$cuissue="cuissue".$i;
			$yettoissue="yettoissue".$i;
			$cbofloor="cbofloor".$i;
			$cboline="cboline".$i;
			$globalstock="globalstock".$i;
			$updatedtlsid="updatedtlsid".$i;
			$updatetransid="updatetransid".$i;
			$previousprodid="previousprodid".$i;
			$cboRecFloor="cboRecFloor".$i;
			$cboRecRoom="cboRecRoom".$i;
			$cboRecRack="cboRecRack".$i;
			$cboRecShelf="cboRecShelf".$i;
			$cboRecBin="cboRecBin".$i;
			
			
			$all_item_group_id.=$$cboitemgroup.",";
			$all_prod_id.=$$prod_id.",";
			$all_po_id.=$$po_id.",";
			
			$item_wise_data[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]['cbouom']=$$cbouom;
			$item_wise_data[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]['cboitemgroup']=$$cboitemgroup;
			$item_wise_data[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]['itemdescription']=$$itemdescription;
			$item_wise_data[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]['brandSupref']=$$brandSupref;
			$item_wise_data[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]['itemcolorid']=$$itemcolorid;
			$item_wise_data[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]['itemsizeid']=$$itemsizeid;
			$item_wise_data[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]['gmtscolorid']=$$gmtscolorid;
			$item_wise_data[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]['gmtssizeId']=$$gmtssizeId;
			$item_wise_data[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]['cbofloor']=$$cbofloor;
			$item_wise_data[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]['cboRecFloor']=$$cboRecFloor;
			$item_wise_data[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]['cboRecRoom']=$$cboRecRoom;
			$item_wise_data[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]['cboRecRack']=$$cboRecRack;
			$item_wise_data[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]['cboRecShelf']=$$cboRecShelf;
			$item_wise_data[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]['cboRecBin']=$$cboRecBin;
			$item_wise_data[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]['cboline']=$$cboline;
			$item_wise_data[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]['po_id'].=$$po_id.",";
			
			$item_wise_data[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]['issueqnty']+=$$issueqnty;
			$item_wise_data[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]['receiveqnty']+=$$receiveqnty;
			$item_wise_data[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]['cuissue']+=$$cuissue;
			$item_wise_data[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]['yettoissue']+=$$yettoissue;
			$item_wise_data[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]['globalstock']+=$$globalstock;
			
			$conversion_fac=$trim_group_arr[$$cboitemgroup]['conversion_factor'];
			$item_order_qnty[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin][$$po_id]+=($$issueqnty/$conversion_fac);
			
		}
		
		
		
		$all_prod_id=chop($all_prod_id,",");
		if($all_prod_id=="") $all_prod_id=0;
		$all_item_group_id=chop($all_item_group_id,",");
		if($all_item_group_id=="") $all_item_group_id=0;
		$all_po_id=chop($all_po_id,",");
		
		$sql_trim = sql_select("select b.PO_BREAKDOWN_ID, b.PROD_ID, c.FLOOR_ID, c.ROOM, c.RACK, c.SELF, c.BIN_BOX, sum((case when b.entry_form in(24,73,78,112) and b.trans_type in(1,4,5) then b.quantity else 0 end)-(case when b.entry_form in(25,49,78,112) and b.trans_type in(2,3,6) then b.quantity else 0 end)) as BALANCE, sum((case when b.entry_form in(24,73,78,112) and b.trans_type in(1,4,5) then b.ORDER_AMOUNT else 0 end)-(case when b.entry_form in(25,49,78,112) and b.trans_type in(2,3,6) then b.ORDER_AMOUNT else 0 end)) as BALANCE_AMT 
		from order_wise_pro_details b, inv_transaction c
		where b.trans_id=c.id and b.prod_id in(".implode(',',array_unique(explode(',',$all_prod_id))).") and c.item_category=4 and c.company_id=$cbo_company_id and c.store_id =$cbo_store_name and b.po_breakdown_id in(".implode(',',array_unique(explode(',',$all_po_id))).") and c.status_active=1  and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
		group by b.po_breakdown_id, b.prod_id, c.FLOOR_ID, c.ROOM, c.RACK, c.SELF, c.BIN_BOX");
		$order_prev_data=array();
		foreach($sql_trim as $row)
		{
			$order_prev_data[$row["PROD_ID"]][$row["FLOOR_ID"]][$row["ROOM"]][$row["RACK"]][$row["SELF"]][$row["BIN_BOX"]][$row["PO_BREAKDOWN_ID"]]["BALANCE"]=$row["BALANCE"];
			$order_prev_data[$row["PROD_ID"]][$row["FLOOR_ID"]][$row["ROOM"]][$row["RACK"]][$row["SELF"]][$row["BIN_BOX"]][$row["PO_BREAKDOWN_ID"]]["BALANCE_AMT"]=$row["BALANCE_AMT"];
		}
		
		//echo "10**";print_r($order_prev_data);disconnect($con);die;
		
		$prod_sql=sql_select("select a.ID AS PROD_ID, a.ITEM_GROUP_ID, a.ITEM_DESCRIPTION, a.CURRENT_STOCK, a.AVG_RATE_PER_UNIT, a.STOCK_VALUE, b.FLOOR_ID, b.ROOM, b.RACK, b.SELF, b.BIN_BOX, sum((case when b.transaction_type in(1,4,5) then b.cons_quantity else 0 end)-(case when b.transaction_type in(2,3,6) then b.cons_quantity else 0 end)) as STORE_STOCK 
		from product_details_master a, inv_transaction b 
		where a.id=b.prod_id and b.store_id=$cbo_store_name and a.company_id=$cbo_company_id and a.item_category_id=4 and b.company_id=$cbo_company_id and b.item_category=4 and a.entry_form=24 and a.id in($all_prod_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
		group by a.id , a.item_group_id, a.item_description, a.current_stock, a.avg_rate_per_unit, a.stock_value, b.FLOOR_ID, b.ROOM, b.RACK, b.SELF, b.BIN_BOX");
		$prod_store_data=array();$prod_stock_data=array();
		foreach($prod_sql as $row)
		{
			$prod_store_data[$row["PROD_ID"]][$row["FLOOR_ID"]][$row["ROOM"]][$row["RACK"]][$row["SELF"]][$row["BIN_BOX"]]["prod_id"]=$row["PROD_ID"];
			$prod_store_data[$row["PROD_ID"]][$row["FLOOR_ID"]][$row["ROOM"]][$row["RACK"]][$row["SELF"]][$row["BIN_BOX"]]["current_stock"]=$row["CURRENT_STOCK"];
			$prod_store_data[$row["PROD_ID"]][$row["FLOOR_ID"]][$row["ROOM"]][$row["RACK"]][$row["SELF"]][$row["BIN_BOX"]]["avg_rate_per_unit"]=$row["AVG_RATE_PER_UNIT"];
			$prod_store_data[$row["PROD_ID"]][$row["FLOOR_ID"]][$row["ROOM"]][$row["RACK"]][$row["SELF"]][$row["BIN_BOX"]]["stock_value"]=$row["STOCK_VALUE"];
			$prod_store_data[$row["PROD_ID"]][$row["FLOOR_ID"]][$row["ROOM"]][$row["RACK"]][$row["SELF"]][$row["BIN_BOX"]]["store_stock"]=$row["STORE_STOCK"];
			
			$prod_stock_data[$row["PROD_ID"]]["stock_value"]=$row["STOCK_VALUE"];
			$prod_stock_data[$row["PROD_ID"]]["stock_qnty"]=$row["CURRENT_STOCK"];
			$prod_stock_data[$row["PROD_ID"]]["avg_rate_per_unit"]=$row["AVG_RATE_PER_UNIT"];
		}
		
		//$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;
		//$id_dtls=return_next_id( "id", "inv_trims_issue_dtls", 1 ) ;
		//$id_prop = return_next_id( "id", "order_wise_pro_details", 1 );
		
		
		$field_array_trans="id, mst_id, company_id, receive_basis, pi_wo_batch_no, prod_id, item_category, transaction_type, transaction_date, cons_uom, cons_quantity, cons_rate, cons_amount, issue_challan_no, store_id, inserted_by, insert_date,floor_id, room, rack, self, bin_box";
		$field_array_dtls="id, mst_id, trans_id, prod_id, item_group_id, item_description, brand_supplier, uom, issue_qnty, rate, amount, order_id, item_order_id, gmts_color_id, gmts_size_id, item_color_id, item_size, floor_id, sewing_line, inserted_by, insert_date";		
		
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, order_rate, order_amount, inserted_by, insert_date";
		
		$data_array_prop=$data_array_trans=$data_array_dtls="";//$prod_val
		$product_unique_data_arr=array();
		// echo "20**<pre>";print_r($item_wise_data);die;
		foreach($item_wise_data as $prod_id=>$prod_data)
		{
			foreach($prod_data as $floor_id=>$floor_data)
			{
				foreach($floor_data as $room_id=>$room_data)
				{
					foreach($room_data as $rack_id=>$rack_data)
					{
						foreach($rack_data as $self_id=>$self_data)
						{
							foreach($self_data as $bin_id=>$prod_val)
							{
								$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
								$id_dtls = return_next_id_by_sequence("INV_TRIMS_ISSUE_DTLS_PK_SEQ", "inv_trims_issue_dtls", $con);
								
								$txt_issue_qnty=$prod_val["issueqnty"]*1;
								$cons_rate=$prod_store_data[$prod_id][$floor_id][$room_id][$rack_id][$self_id][$bin_id]["avg_rate_per_unit"];
								$issue_stock_value = $cons_rate*$txt_issue_qnty;
								$store_stock=$prod_store_data[$prod_id][$floor_id][$room_id][$rack_id][$self_id][$bin_id]["store_stock"]*1;
								$stock_qnty=$prod_store_data[$prod_id][$floor_id][$room_id][$rack_id][$self_id][$bin_id]["current_stock"]*1;
								$avg_rate=$prod_store_data[$prod_id][$floor_id][$room_id][$rack_id][$self_id][$bin_id]["avg_rate_per_unit"];
								
								
								if(number_format($txt_issue_qnty,4,'.','')>number_format($store_stock,4,'.',''))
								{
									if(abs(number_format($txt_issue_qnty,4,'.','')-number_format($store_stock,4,'.',''))<1)
									{
										$txt_issue_qnty=number_format($store_stock,4,'.','');
									}
									else
									{
										echo "50**Issue Quantity Exceeds The Global Current Stock Quantity. prod_id= $prod_id = $txt_issue_qnty = $store_stock";
										oci_rollback($con);disconnect($con);die;
									}
								}
								
								$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$prod_id and transaction_type in (1,4,5)", "max_date");      
								if($max_recv_date != "")
								{
									$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
									$issue_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_issue_date)));
									if ($issue_date < $max_recv_date) 
									{
										echo "50**Issue Date Can not Be Less Than Last Receive Date Of This Item";
										oci_rollback($con);disconnect($con);die;
									}
								}
								
								if($data_array_trans!="") $data_array_trans.=", ";
								$data_array_trans.="(".$id_trans.",".$trims_update_id.",".$cbo_company_id.",".$cbo_basis.",".$txt_booking_id.",".$prod_id.",4,2,".$txt_issue_date.",'".$prod_val["cbouom"]."',".$txt_issue_qnty.",".$cons_rate.",".$issue_stock_value.",".$txt_issue_chal_no.",".$cbo_store_name.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$prod_val["cboRecFloor"]."','".$prod_val["cboRecRoom"]."','".$prod_val["cboRecRack"]."','".$prod_val["cboRecShelf"]."','".$prod_val["cboRecBin"]."')";
								
								if($data_array_dtls!="") $data_array_dtls.=", ";
								$data_array_dtls.="(".$id_dtls.",".$trims_update_id.",".$id_trans.",".$prod_id.",'".$prod_val["cboitemgroup"]."','".$prod_val["itemdescription"]."','".$prod_val["brandSupref"]."','".$prod_val["cbouom"]."',".$txt_issue_qnty.",".$cons_rate.",".$issue_stock_value.",'".chop($prod_val["po_id"],",")."','".chop($prod_val["po_id"],",")."','".$prod_val["gmtscolorid"]."','".$prod_val["gmtssizeId"]."','".$prod_val["itemcolorid"]."','".$prod_val["itemsizeid"]."','".$prod_val["cbofloor"]."','".$prod_val["cboline"]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";						
								
								
								
								if($item_unique_check[$prod_id]=="")
								{
									$item_unique_check[$prod_id]=$prod_id;
									$runtime_tot_issue=0;
								}								
								$runtime_tot_issue+=$txt_issue_qnty;
								$product_unique_data_arr[$prod_id]["id"]=$prod_id;
								$product_unique_data_arr[$prod_id]["issue_qnty"]=$runtime_tot_issue;								
								
								
								//echo "10**<pre>";print_r($item_order_qnty);die;
								foreach($item_order_qnty[$prod_id][$floor_id][$room_id][$rack_id][$self_id][$bin_id] as $order_id=>$order_qnty)
								{
									$trim_stock=$order_prev_data[$prod_id][$floor_id][$room_id][$rack_id][$self_id][$bin_id][$order_id]["BALANCE"];
									$trim_stock_amt=$order_prev_data[$prod_id][$floor_id][$room_id][$rack_id][$self_id][$bin_id][$order_id]["BALANCE_AMT"];
									$trim_ord_rate=0;
									if($trim_stock!=0 && $trim_stock_amt!=0) $trim_ord_rate=$trim_stock_amt/$trim_stock;
									$issue_qnty=$order_qnty*1;
									if(number_format($issue_qnty,4,'.','')>number_format($trim_stock,4,'.',''))
									{
										echo "50**Issue Quantity Not Allow Over Order Stock. prod id= $prod_id, order id= $order_id = ".number_format($issue_qnty,4,'.','')."=".number_format($trim_stock,4,'.','')."= ".$issue_qnty."=".$trim_stock;
										oci_rollback($con);disconnect($con); die;
									}
									
									
									$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
									$order_amount=$issue_qnty*$trim_ord_rate;
									
									if($data_array_prop!="") $data_array_prop.=", ";
									$data_array_prop.="(".$id_prop.",".$id_trans.",2,25,".$id_dtls.",".$order_id.",".$prod_id.",".$issue_qnty.",'".$trim_ord_rate."','".$order_amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
								}
								//$id_trans++;$id_dtls++;
							}
						}
						
					}
				}
			}
			
		}
		
		
		$rID=$rID2=$rID3=$prodUpdate=$rID4=$ordProdUpdate=$ordProdUpdate=$rID4=true;
		if(str_replace("'","",$update_id)=="")
		{
			$rID=sql_insert("inv_issue_master",$field_array,$data_array,0);
		}
		else
		{
			$rID=sql_update("inv_issue_master",$field_array_update,$data_array_update,"id",$update_id,1);
		}
		$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		$rID3=sql_insert("inv_trims_issue_dtls",$field_array_dtls,$data_array_dtls,0);
		if($data_array_prop!="")
		{
			$rID4=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
		}
		
		$field_array_prod_update= "last_issued_qnty*current_stock*stock_value*updated_by*update_date";
		foreach($product_unique_data_arr as $prod_id=>$prod_val)
		{
			$issue_qnty=$prod_val["issue_qnty"];
			$issue_value=$prod_val["issue_qnty"]*$prod_stock_data[$prod_id]["avg_rate_per_unit"];
			$currentStock=$prod_stock_data[$prod_id]["stock_qnty"]-$prod_val["issue_qnty"];
			$StockValue=$prod_stock_data[$prod_id]["stock_value"]-$issue_value;
			$updateProdID_array[]=$prod_id;
			$data_array_prod_update[$prod_id]=explode("*",("".$issue_qnty."*".$currentStock."*".$StockValue."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		}
		
		
		if(count($data_array_prod_update)>0)
		{
			$prodUpdate=execute_query(bulk_update_sql_statement("product_details_master","id",$field_array_prod_update,$data_array_prod_update,$updateProdID_array),1);
		}
		
		//echo "10** $rID=$rID2=$rID3=$prodUpdate=$rID4=$ordProdUpdate";oci_rollback($con);disconnect($con);die;
		
				
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $prodUpdate && $rID4)
			{
				mysql_query("COMMIT");  
				echo "0**".$trims_update_id."**".$trims_issue_num."**0"."**".$all_po_id."**".str_replace("'","",$cbo_store_name);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**"."&nbsp;"."**0"."**0";
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3 && $prodUpdate && $rID4)
			{
				oci_commit($con); 
				echo "0**".$trims_update_id."**".$trims_issue_num."**0"."**".$all_po_id."**".str_replace("'","",$cbo_store_name);
			}
			else
			{
				oci_rollback($con);
				echo "5**0**"."&nbsp;"."**0"."**0";
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
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		$field_array_update="issue_purpose*issue_basis*booking_id*booking_no*issue_date*challan_no*store_id*knit_dye_source*knit_dye_company*location_id*floor_id*remarks*extra_status*updated_by*update_date";
		$data_array_update=$cbo_issue_purpose."*".$cbo_basis."*".$txt_booking_id."*".$txt_booking_no."*".$txt_issue_date."*".$txt_issue_chal_no."*".$cbo_store_name."*".$cbo_sewing_source."*".$cbo_sewing_company."*".$cbo_sewing_location_name."*".$cbo_floor_unit_name."*".$txt_remarks."*".$cbo_extra_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		
		$item_wise_data=array();$item_order_qnty=array();$all_prod_id=$all_po_id=$all_item_group_id="";
		for($i=1;$i<=$tot_row; $i++)
		{
			$po_id="po_id".$i;
			$prod_id="prod_id".$i;
			$cboitemgroup="cboitemgroup".$i;
			$itemdescription="itemdescription".$i;
			$brandSupref="brandSupref".$i;
			$itemcolorid="itemcolorid".$i;
			$itemsizeid="itemsizeid".$i;
			$gmtscolorid="gmtscolorid".$i;
			$gmtssizeId="gmtssizeId".$i;
			$cbouom="cbouom".$i;
			$issueqnty="issueqnty".$i;
			$receiveqnty="receiveqnty".$i;
			$cuissue="cuissue".$i;
			$yettoissue="yettoissue".$i;
			$cbofloor="cbofloor".$i;
			$cboline="cboline".$i;
			$globalstock="globalstock".$i;
			$updatedtlsid="updatedtlsid".$i;
			$updatetransid="updatetransid".$i;
			$previousprodid="previousprodid".$i;
			$cboRecFloor="cboRecFloor".$i;
			$cboRecRoom="cboRecRoom".$i;
			$cboRecRack="cboRecRack".$i;
			$cboRecShelf="cboRecShelf".$i;
			$cboRecBin="cboRecBin".$i;
			
			
			$all_item_group_id.=$$cboitemgroup.",";
			$all_prod_id.=$$prod_id.",";
			$all_po_id.=$$po_id.",";
			$all_trans_id.=$$updatetransid.",";
			//[$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]
			$item_wise_data[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]['cbouom']=$$cbouom;
			$item_wise_data[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]['cboitemgroup']=$$cboitemgroup;
			$item_wise_data[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]['itemdescription']=$$itemdescription;
			$item_wise_data[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]['brandSupref']=$$brandSupref;
			$item_wise_data[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]['itemcolorid']=$$itemcolorid;
			$item_wise_data[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]['itemsizeid']=$$itemsizeid;
			$item_wise_data[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]['gmtscolorid']=$$gmtscolorid;
			$item_wise_data[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]['gmtssizeId']=$$gmtssizeId;
			$item_wise_data[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]['cbofloor']=$$cbofloor;
			$item_wise_data[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]['cboline']=$$cboline;
			$item_wise_data[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]['updatedtlsid']=$$updatedtlsid;
			$item_wise_data[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]['updatetransid']=$$updatetransid;
			$item_wise_data[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]['previousprodid']=$$previousprodid;
			$item_wise_data[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]['cboRecFloor']=$$cboRecFloor;
			$item_wise_data[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]['cboRecRoom']=$$cboRecRoom;
			$item_wise_data[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]['cboRecRack']=$$cboRecRack;
			$item_wise_data[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]['cboRecShelf']=$$cboRecShelf;
			$item_wise_data[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]['cboRecBin']=$$cboRecBin;
			$item_wise_data[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]['po_id'].=$$po_id.",";
			
			$item_wise_data[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]['issueqnty']+=$$issueqnty;
			$item_wise_data[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]['receiveqnty']+=$$receiveqnty;
			$item_wise_data[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]['cuissue']+=$$cuissue;
			$item_wise_data[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]['yettoissue']+=$$yettoissue;
			$item_wise_data[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin]['globalstock']+=$$globalstock;
			
			$conversion_fac=$trim_group_arr[$$cboitemgroup]['conversion_factor'];
			$item_order_qnty[$$prod_id][$$cboRecFloor][$$cboRecRoom][$$cboRecRack][$$cboRecShelf][$$cboRecBin][$$po_id]+=($$issueqnty/$conversion_fac);
			
		}
		
		
		$all_prod_id=chop($all_prod_id,",");
		if($all_prod_id=="") $all_prod_id=0;
		$all_item_group_id=chop($all_item_group_id,",");
		if($all_item_group_id=="") $all_item_group_id=0;
		$all_po_id=chop($all_po_id,",");
		$all_trans_id=chop($all_trans_id,",");
		
		$sql_trim = sql_select("select b.PO_BREAKDOWN_ID, b.PROD_ID, c.FLOOR_ID, c.ROOM, c.RACK, c.SELF, c.BIN_BOX, sum((case when b.entry_form in(24,73,78,112) and b.trans_type in(1,4,5) then b.quantity else 0 end)-(case when b.entry_form in(25,49,78,112) and b.trans_type in(2,3,6) then b.quantity else 0 end)) as BALANCE, sum((case when b.entry_form in(24,73,78,112) and b.trans_type in(1,4,5) then b.ORDER_AMOUNT else 0 end)-(case when b.entry_form in(25,49,78,112) and b.trans_type in(2,3,6) then b.ORDER_AMOUNT else 0 end)) as BALANCE_AMT 
		from order_wise_pro_details b, inv_transaction c
		where b.trans_id=c.id and b.prod_id in($all_prod_id) and c.item_category=4 and c.company_id=$cbo_company_id and c.store_id =$cbo_store_name and b.po_breakdown_id in($all_po_id) and trans_id not in($all_trans_id) and c.status_active=1  and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
		group by b.PO_BREAKDOWN_ID, b.PROD_ID, c.FLOOR_ID, c.ROOM, c.RACK, c.SELF, c.BIN_BOX");
		$order_prev_data=array();
		foreach($sql_trim as $row)
		{
			$order_prev_data[$row["PROD_ID"]][$row["FLOOR_ID"]][$row["ROOM"]][$row["RACK"]][$row["SELF"]][$row["BIN_BOX"]][$row["PO_BREAKDOWN_ID"]]["BALANCE"]=$row["BALANCE"];
			$order_prev_data[$row["PROD_ID"]][$row["FLOOR_ID"]][$row["ROOM"]][$row["RACK"]][$row["SELF"]][$row["BIN_BOX"]][$row["PO_BREAKDOWN_ID"]]["BALANCE_AMT"]=$row["BALANCE_AMT"];
		}
		
		$prev_data_sql=sql_select("select b.ID, b.TRANS_ID, b.PROD_ID, b.ISSUE_QNTY, c.FLOOR_ID, c.ROOM, c.RACK, c.SELF, c.BIN_BOX 
		from inv_trims_issue_dtls b, inv_transaction c 
		where b.TRANS_ID=c.id and b.mst_id=$update_id and c.transaction_type=2 and b.status_active=1 and c.status_active=1");
		$prev_data=array();$prev_dtls_id=array();$prev_trans_arr=array();
		foreach($prev_data_sql as $row)
		{
			$prev_data[$row["PROD_ID"]]["qnty"]+=$row["ISSUE_QNTY"];
			$prev_issue_data[$row["PROD_ID"]][$row["FLOOR_ID"]][$row["ROOM"]][$row["RACK"]][$row["SELF"]][$row["BIN_BOX"]]["qnty"]+=$row["ISSUE_QNTY"];
			$prev_dtls_id[$row["ID"]]=$row["ID"];
			$prev_trans_arr[$row["ID"]]["trans_id"]=$row["TRANS_ID"];
		}
		
		//echo "10**";print_r($prev_issue_data);disconnect($con);die;
		
		$prod_sql=sql_select("select a.ID AS PROD_ID, a.ITEM_GROUP_ID, a.ITEM_DESCRIPTION, a.CURRENT_STOCK, a.AVG_RATE_PER_UNIT, a.STOCK_VALUE, b.FLOOR_ID, b.ROOM, b.RACK, b.SELF, b.BIN_BOX, sum((case when b.transaction_type in(1,4,5) then b.cons_quantity else 0 end)-(case when b.transaction_type in(2,3,6) then b.cons_quantity else 0 end)) as STORE_STOCK 
		from product_details_master a, inv_transaction b 
		where a.id=b.prod_id and b.store_id=$cbo_store_name and a.company_id=$cbo_company_id and a.item_category_id=4 and b.company_id=$cbo_company_id and b.item_category=4 and a.entry_form=24 and a.id in($all_prod_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
		group by a.id , a.item_group_id, a.item_description, a.current_stock, a.avg_rate_per_unit, a.stock_value, b.FLOOR_ID, b.ROOM, b.RACK, b.SELF, b.BIN_BOX");
		$prod_store_data=array();$prod_stock_data=array();
		foreach($prod_sql as $row)
		{
			$prod_store_data[$row["PROD_ID"]][$row["FLOOR_ID"]][$row["ROOM"]][$row["RACK"]][$row["SELF"]][$row["BIN_BOX"]]["prod_id"]=$row["PROD_ID"];
			$prod_store_data[$row["PROD_ID"]][$row["FLOOR_ID"]][$row["ROOM"]][$row["RACK"]][$row["SELF"]][$row["BIN_BOX"]]["current_stock"]=$row["CURRENT_STOCK"];
			$prod_store_data[$row["PROD_ID"]][$row["FLOOR_ID"]][$row["ROOM"]][$row["RACK"]][$row["SELF"]][$row["BIN_BOX"]]["avg_rate_per_unit"]=$row["AVG_RATE_PER_UNIT"];
			$prod_store_data[$row["PROD_ID"]][$row["FLOOR_ID"]][$row["ROOM"]][$row["RACK"]][$row["SELF"]][$row["BIN_BOX"]]["stock_value"]=$row["STOCK_VALUE"];
			$prod_store_data[$row["PROD_ID"]][$row["FLOOR_ID"]][$row["ROOM"]][$row["RACK"]][$row["SELF"]][$row["BIN_BOX"]]["store_stock"]=$row["STORE_STOCK"];
			
			$prod_stock_data[$row["PROD_ID"]]["stock_value"]=$row["STOCK_VALUE"];
			$prod_stock_data[$row["PROD_ID"]]["stock_qnty"]=$row["CURRENT_STOCK"];
			$prod_stock_data[$row["PROD_ID"]]["avg_rate_per_unit"]=$row["AVG_RATE_PER_UNIT"];
		}
		
		//echo "10**";print_r($prod_data);check_table_status( $_SESSION['menu_id'],0);disconnect($con);die;
		
		$field_array_trans_update="receive_basis*pi_wo_batch_no*prod_id*transaction_date*cons_uom*cons_quantity*cons_rate*cons_amount*issue_challan_no*store_id*updated_by*update_date*floor_id*room*rack*self*bin_box";
		$field_array_dtls_update="prod_id*item_group_id*item_description*brand_supplier*uom*issue_qnty*rate*amount*gmts_color_id*gmts_size_id*item_color_id*item_size*floor_id*sewing_line*updated_by*update_date";
		
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, order_rate, order_amount, inserted_by, insert_date";
		//$id_prop = return_next_id( "id", "order_wise_pro_details", 1 );
		$data_array_prop=$data_array_trans=$data_array_dtls="";$current_dtls_id=array();
		foreach($item_wise_data as $prod_id=>$prod_data)
		{
			foreach($prod_data as $floor_id=>$floor_data)
			{
				foreach($floor_data as $room_id=>$room_data)
				{
					foreach($room_data as $rack_id=>$rack_data)
					{
						foreach($rack_data as $self_id=>$self_data)
						{
							foreach($self_data as $bin_id=>$prod_val)
							{
								if($prod_val["updatetransid"]>0 && $prod_val["updatedtlsid"]>0)
								{
									$current_dtls_id[$prod_val["updatedtlsid"]]=$prod_val["updatedtlsid"];
									$txt_issue_qnty=$prod_val["issueqnty"]*1;
									$cons_rate=$prod_store_data[$prod_id][$floor_id][$room_id][$rack_id][$self_id][$bin_id]["avg_rate_per_unit"];
									$issue_stock_value = $cons_rate*$txt_issue_qnty;
									$store_stock=$prod_store_data[$prod_id][$floor_id][$room_id][$rack_id][$self_id][$bin_id]["store_stock"]*1+$prev_issue_data[$prod_id][$floor_id][$room_id][$rack_id][$self_id][$bin_id]["qnty"];
									$stock_qnty=$prod_store_data[$prod_id][$floor_id][$room_id][$rack_id][$self_id][$bin_id]["current_stock"]*1+$prev_issue_data[$prod_id][$floor_id][$room_id][$rack_id][$self_id][$bin_id]["qnty"];
									$avg_rate=$prod_store_data[$prod_id][$floor_id][$room_id][$rack_id][$self_id][$bin_id]["avg_rate_per_unit"];
									//if(number_format($txt_issue_qnty,4,'.','')>number_format($store_stock,4,'.',''))
//									{
//										echo "50**Issue Quantity Exceeds The Global Current Stock Quantity";
//										oci_rollback($con);disconnect($con);die;
//									}

									if(number_format($txt_issue_qnty,4,'.','')>number_format($store_stock,4,'.',''))
									{
										if(abs(number_format($txt_issue_qnty,4,'.','')-number_format($store_stock,4,'.',''))<1)
										{
											$txt_issue_qnty=number_format($store_stock,4,'.','');
										}
										else
										{
											echo "50**Issue Quantity Exceeds The Global Current Stock Quantity. prod_id= $prod_id = $txt_issue_qnty = $store_stock";
											oci_rollback($con);disconnect($con);die;
										}
									}
									
									$max_recv_trans = return_field_value("max(id) as max_id", "inv_transaction", "prod_id=$prod_id and transaction_type in (1,4,5)", "max_id");
									if($max_recv_trans > $prod_val["updatetransid"])
									{
										echo "50**Next Transaction Found";
										oci_rollback($con);disconnect($con);die;
									}
									
									$updateTransId_array[]=$prod_val["updatetransid"];
									$data_array_trans_update[$prod_val["updatetransid"]]=explode("*",("".$cbo_basis."*".$txt_booking_id."*'".$prod_id."'*".$txt_issue_date."*'".$prod_val["cbouom"]."'*'".$txt_issue_qnty."'*'".$cons_rate."'*'".$issue_stock_value."'*".$txt_issue_chal_no."*".$cbo_store_name."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'".$prod_val["cboRecFloor"]."'*'".$prod_val["cboRecRoom"]."'*'".$prod_val["cboRecRack"]."'*'".$prod_val["cboRecShelf"]."'*'".$prod_val["cboRecBin"]."'"));
									
									$updateDtlsId_array[]=$prod_val["updatedtlsid"];
									$data_array_dtls_update[$prod_val["updatedtlsid"]]=explode("*",("'".$prod_id."'*'".$prod_val["cboitemgroup"]."'*'".$prod_val["itemdescription"]."'*'".$prod_val["brandSupref"]."'*'".$prod_val["cbouom"]."'*'".$txt_issue_qnty."'*'".$cons_rate."'*'".$issue_stock_value."'*'".$prod_val["gmtscolorid"]."'*'".$prod_val["gmtssizeId"]."'*'".$prod_val["itemcolorid"]."'*'".$prod_val["itemsizeid"]."'*'".$prod_val["cbofloor"]."'*'".$prod_val["cboline"]."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
									
									//product master table data UPDATE START----------------------//
									
									if($item_unique_check[$prod_id]=="")
									{
										$item_unique_check[$prod_id]=$prod_id;
										$runtime_tot_issue=0;
									}								
									$runtime_tot_issue+=$txt_issue_qnty;
									$product_unique_data_arr[$prod_id]["id"]=$prod_id;
									$product_unique_data_arr[$prod_id]["issue_qnty"]=$runtime_tot_issue;
									
									foreach($item_order_qnty[$prod_id][$floor_id][$room_id][$rack_id][$self_id][$bin_id] as $order_id=>$order_qnty)
									{
										$trim_stock=$order_prev_data[$prod_id][$floor_id][$room_id][$rack_id][$self_id][$bin_id][$order_id]["BALANCE"];
										$trim_stock_amt=$order_prev_data[$prod_id][$floor_id][$room_id][$rack_id][$self_id][$bin_id][$order_id]["BALANCE_AMT"];
										$trim_ord_rate=0;
										if($trim_stock!=0 && $trim_stock_amt!=0) $trim_ord_rate=$trim_stock_amt/$trim_stock;
										$issue_qnty=$order_qnty*1;
										if(number_format($issue_qnty,4,'.','')>number_format($trim_stock,4,'.',''))
										{
											echo "17**Issue Quantity Not Allow Over Order Stock.";
											oci_rollback($con);disconnect($con);die;
										}
										
										$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
										$order_amount=$issue_qnty*$trim_ord_rate;
										
										if($data_array_prop!="") $data_array_prop.=", ";
										$data_array_prop.="(".$id_prop.",".$prod_val["updatetransid"].",2,25,".$prod_val["updatedtlsid"].",".$order_id.",".$prod_id.",".$issue_qnty.",'".$trim_ord_rate."','".$order_amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
										
										$all_order_id.=$order_id.",";
										
									}
								}
							}
						}
					}
				}
			}
		}
		$Inactive_dtls_id=$Inactive_trans_id="";
		$inactive_dtls_id_arr=array_diff($prev_dtls_id,$current_dtls_id);
		foreach($inactive_dtls_id_arr as $dtls_id)
		{
			$Inactive_dtls_id.=$dtls_id.",";
			$Inactive_trans_id.=$prev_trans_arr[$dtls_id]["trans_id"].",";
			if($all_trans_id !="") $all_trans_id.=",".$prev_trans_arr[$dtls_id]["trans_id"];
			else  $all_trans_id.=$prev_trans_arr[$dtls_id]["trans_id"].",";
			
			$row_prod_trans=sql_select( "select id, prod_id, cons_quantity, cons_amount from inv_transaction where id=".$prev_trans_arr[$dtls_id]["trans_id"]."" );
			$prod_data_sql=sql_select( "select id, avg_rate_per_unit, current_stock, stock_value from product_details_master where id=".$row_prod_trans[0][csf("prod_id")]."");
			$stock_qnty=$prod_data_sql[0][csf("current_stock")];
			$stock_amount=$prod_data_sql[0][csf("stock_value")];
			$currentStock   = $stock_qnty+$row_prod_trans[0][csf("cons_quantity")];	
			$updateProdID_array[]=$row_prod_trans[0][csf("prod_id")];	
			$StockValue	 	=0;	
			if ($currentStock != 0){
				$StockValue	 	= $currentStock+$row_prod_trans[0][csf("cons_amount")];				
			}

			$field_array_prod_update= "last_issued_qnty*current_stock*stock_value*updated_by*update_date";
			$data_array_prod_update[$row_prod_trans[0][csf("prod_id")]]=explode("*",("".$row_prod_trans[0][csf("cons_quantity")]."*".$currentStock."*".$StockValue."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			
			
			$sql_prop_prev=sql_select( "select id, prod_id, po_breakdown_id, quantity, order_amount from order_wise_pro_details where trans_id=".$prev_trans_arr[$dtls_id]["trans_id"]." and dtls_id=".$dtls_id." and status_active=1 and is_deleted=0" );
			foreach($sql_prop_prev as $row)
			{
				$row_prod_order=sql_select( "select id, stock_quantity, avg_rate, stock_amount from order_wise_stock where prod_id=".$row[csf("prod_id")]." and po_breakdown_id=".$row[csf("po_breakdown_id")]." and status_active=1 and is_deleted=0" );
				$item_order_id=$row_prod_order[0][csf("id")];
				$prev_item_order_stock=$row_prod_order[0][csf("stock_quantity")]+$row[csf("quantity")];
				$prev_item_order_amount=$row_prod_order[0][csf("stock_amount")]+$row[csf("order_amount")];
				$ord_prod_id_arr[]=$item_order_id;
				$data_array_ord_prod_update[$item_order_id]=explode("*",("".$sql_prop_prev[0][csf("quantity")]."*".$prev_item_order_stock."*".$prev_item_order_amount."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				
			}
			
		}
		
		$Inactive_dtls_id=chop($Inactive_dtls_id,",");
		$Inactive_trans_id=chop($Inactive_trans_id,",");
		$all_trans_id=chop($all_trans_id,",");
		
		
		//echo "10**$all_trans_id";die;
		
		$rID=$transUpdate=$dtlsUpdate=$prodUpdate=$delete_prop=$rID4=$delete_dtls=$delete_trans=$ordProdUpdate=true;
		$rID=sql_update("inv_issue_master",$field_array_update,$data_array_update,"id",$update_id,1);
		// echo "10**$rID";die;
		if($Inactive_dtls_id!="")
		{
			$delete_dtls=execute_query("update inv_trims_issue_dtls set status_active=0, is_deleted=1, updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where id in($Inactive_dtls_id)");
		}
		if($Inactive_trans_id!="")
		{
			$delete_trans=execute_query("update inv_transaction set status_active=0, is_deleted=1, updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where id in($Inactive_trans_id)");
		}
		
		if(count($data_array_trans_update)>0)
		{
			$transUpdate=execute_query(bulk_update_sql_statement("inv_transaction","id",$field_array_trans_update,$data_array_trans_update,$updateTransId_array));
		}
		//echo "10**".$transUpdate;oci_rollback($con);check_table_status( $_SESSION['menu_id'],0);disconnect($con);die;
		
		if(count($data_array_dtls_update)>0)
		{
			$dtlsUpdate=execute_query(bulk_update_sql_statement(" inv_trims_issue_dtls","id",$field_array_dtls_update,$data_array_dtls_update,$updateDtlsId_array));
		}		
		
		//echo "insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;	
		if($data_array_prop!="")
		{
			$delete_prop=execute_query( "delete from order_wise_pro_details where trans_id in($all_trans_id) and entry_form=25",0);
			$rID4=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
		}
		
		$field_array_prod_update= "last_issued_qnty*current_stock*stock_value*updated_by*update_date";
		foreach($product_unique_data_arr as $prod_id=>$prod_val)
		{
			$issue_qnty=$prod_val["issue_qnty"];
			$issue_value=$prod_val["issue_qnty"]*$prod_stock_data[$prod_id]["avg_rate_per_unit"];
			$prev_isue_qnty=$prev_data[$prod_id]["qnty"];
			$prev_isue_value=$prev_data[$prod_id]["qnty"]*$prod_stock_data[$prod_id]["avg_rate_per_unit"];
			$currentStock=(($prod_stock_data[$prod_id]["stock_qnty"]+$prev_isue_qnty)-$prod_val["issue_qnty"]);
			$StockValue=(($prod_stock_data[$prod_id]["stock_value"]+$prev_isue_value)-$issue_value);
			$updateProdID_array[]=$prod_id;
			$data_array_prod_update[$prod_id]=explode("*",("".$issue_qnty."*".$currentStock."*".$StockValue."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		}
		
		if(count($data_array_prod_update)>0)
		{
			$prodUpdate=execute_query(bulk_update_sql_statement("product_details_master","id",$field_array_prod_update,$data_array_prod_update,$updateProdID_array));
		}
		
		//echo "10** $rID=$transUpdate=$dtlsUpdate=$prodUpdate=$delete_prop=$rID4=$delete_dtls=$delete_trans=$ordProdUpdate";oci_rollback($con);die;
		if($db_type==0)
		{
			if($rID && $transUpdate && $dtlsUpdate && $prodUpdate && $delete_prop && $rID4 && $delete_dtls && $delete_trans && $ordProdUpdate)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_id)."**0"."**".$all_order_ids."**".str_replace("'","",$cbo_store_name);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**0**0**1"."**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $transUpdate && $dtlsUpdate && $prodUpdate && $delete_prop && $rID4 && $delete_dtls && $delete_trans && $ordProdUpdate)
			{
				oci_commit($con); 
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_id)."**0"."**".$all_order_ids."**".str_replace("'","",$cbo_store_name);
			}
			else
			{
				oci_rollback($con);
				echo "6**0**0**1"."**0";
			}
		}
		disconnect($con);
		die;
	}
}


if ($action=="trims_issue_popup_search")
{
	echo load_html_head_contents("Trims Issue Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?> 

	<script>
	
		function js_set_value(data)
		{
			var id = data.split("_");
			$('#hidden_issue_id').val(id[0]);
			$('#hidden_posted_in_account').val(id[1]);
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>
<div align="center" style="width:780px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:775px; margin-left:5px">
		<legend>Enter search words</legend>           
            <table cellpadding="2" cellspacing="0" width="770" class="rpt_table" rules="all" border="1">
                <thead>
                    <th>Store</th>
                    <th>Issue Date Range</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="180">Enter Issue ID No</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
                    	<input type="hidden" name="hidden_issue_id" id="hidden_issue_id" class="text_boxes" value="">  
                        <input type="hidden" name="hidden_posted_in_account" id="hidden_posted_in_account" class="text_boxes" value="">  
                    </th> 
                </thead>
                <tr>
                    <td align="center">
                    	<?
							echo create_drop_down("cbo_store_name", 130, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$cbo_company_id' and b.category_type=4 and a.status_active=1 and a.is_deleted=0 $store_location_credential_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1, "-- All store --", 0, "" );
						?>       
                    </td>
                    <td align="center">
                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">To
					  	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					</td>
                    <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Issue ID",2=>"Challan No");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../../') ";							
							echo create_drop_down( "cbo_search_by", 120, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                    </td>     
                    <td align="center" id="search_by_td">				
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 						
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_store_name').value+'_'+document.getElementById('cbo_year_selection').value, 'create_trims_issue_search_list_view', 'search_div', 'trims_issue_multi_ref_controller', 'setFilterGrid(\'list_view\',-1);')" style="width:100px;" />
                     </td>
                </tr>
                <tr>
                	<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
           </table>
           <div style="margin-top:8px;" id="search_div" align="left"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_trims_issue_search_list_view")
{
	$data = explode("_",$data);
	
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];
	$store_id =$data[5];
	$year_id =$data[6];
	
	if($store_id<1 && $start_date=="" && $end_date=="" && trim($data[0])=="")
	{
		echo "Please Select Date Range.";die;
	}
	
	if($store_id==0) $store_name=""; else $store_name="and store_id=$store_id";
	
	$trims_issue_basis=array(1=>"With Order",2=>"Without Order",3=>"Requisition");
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and issue_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and issue_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";	
		}
	}
	else
	{
		$date_cond="";
	}
	
	if(trim($data[0])!="")
	{
		if($search_by==1)	
			$search_field_cond="and issue_number_prefix_num='$data[0]'";
		else	
			$search_field_cond="and challan_no like '$search_string'";
	}
	else
	{
		$search_field_cond="";
	}
	
	if($db_type==0) $year_field="YEAR(insert_date)"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY')";
	else $year_field="";//defined Later
	$year_condition="";
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
	
	$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, supplier_id, company_location_id FROM user_passwd where id=$user_id");
	$cre_company_id = $userCredential[0][csf('company_id')];
	$cre_supplier_id = $userCredential[0][csf('supplier_id')];
	$cre_store_location_id = $userCredential[0][csf('store_location_id')];
	$cre_company_location_id = $userCredential[0][csf('company_location_id')];
	
	if ($cre_company_id !='') {
		$company_credential_cond = " and company_id in($cre_company_id)";
	}
	if ($cre_store_location_id !='') {
		$cre_store_location_id=$cre_store_location_id.",0";
		$store_location_credential_cond = " and store_id in($cre_store_location_id)"; 
	}
	
	/*if ($cre_company_location_id !='') {
		$cre_company_location_id=$cre_company_location_id.",0";
		$location_credential_cond = " and location_id in($cre_company_location_id)";
	}*/
	
	$sql = "select id, issue_number_prefix_num, $year_field as year, issue_number, challan_no, store_id, location_id, issue_date, booking_no, issue_basis, is_posted_account from inv_issue_master where entry_form=25 and status_active=1 and is_deleted=0 and is_multi=1 and company_id=$company_id $store_name  $search_field_cond $date_cond $company_credential_cond $store_location_credential_cond $year_condition order by id"; 
	//echo $sql;
	$store_arr = return_library_array("select id, store_name from lib_store_location","id","store_name");
	$location_arr = return_library_array("select id, location_name from lib_location","id","location_name");
	$arr=array(2=>$trims_issue_basis,4=>$store_arr,5=>$location_arr);
	
	echo create_list_view("list_view", "Issue ID,Year,Issue Basis,Challan No,Store,Location,Issue date", "70,70,100,100,130,130","770","240",0, $sql, "js_set_value", "id,is_posted_account", "", 1, "0,0,issue_basis,0,store_id,location_id,0", $arr, "issue_number_prefix_num,year,issue_basis,challan_no,store_id,location_id,issue_date", "",'','0,0,0,0,0,0,3');
	
	exit();
}

if($action=='populate_data_from_trims_issue')
{
	$po_num_sql=sql_select("select c.id, c.po_number from inv_trims_issue_dtls a, order_wise_pro_details b, wo_po_break_down c where a.id=b.dtls_id and a.trans_id=b.trans_id and b.entry_form=25 and b.po_breakdown_id=c.id and a.mst_id=$data");
	$po_num_arr=array();
	foreach($po_num_sql as $row)
	{
		$po_num_arr[$row[csf("id")]]=$row[csf("po_number")];
	}
	
	$data_array=sql_select("select id, company_id, issue_basis, issue_purpose, booking_id, booking_no, issue_number, challan_no, store_id, issue_date, knit_dye_source, knit_dye_company, location_id, floor_id, remarks, extra_status from inv_issue_master where id=$data");
	$order_check=array();$all_order_id="";$all_order_no="";
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("issue_number")]."';\n";
		echo "document.getElementById('cbo_issue_purpose').value 			= '".$row[csf("issue_purpose")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_basis').value 					= '".$row[csf("issue_basis")]."';\n";
		echo "$('#cbo_company_id').attr('disabled','true')".";\n";
		echo "enable_disable();\n";
		
		echo "document.getElementById('cbo_sewing_source').value 			= '".$row[csf("knit_dye_source")]."';\n";
		echo "document.getElementById('cbo_sewing_company').value 		    = '".$row[csf("knit_dye_company")]."';\n";
		echo "load_location();\n";
		echo "document.getElementById('cbo_sewing_location_name').value 	= '".$row[csf("location_id")]."';\n";
		echo "document.getElementById('txt_issue_date').value 				= '".change_date_format($row[csf("issue_date")])."';\n";
		echo "document.getElementById('txt_booking_no').value 				= '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('txt_booking_id').value 				= '".$row[csf("booking_id")]."';\n";
		echo "document.getElementById('txt_issue_chal_no').value 			= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('cbo_store_name').value 				= '".$row[csf("store_id")]."';\n";
		echo "document.getElementById('txt_remarks').value 					= '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('cbo_extra_status').value 			= '".$row[csf("extra_status")]."';\n";
		echo "document.getElementById('update_id').value 					= '".$row[csf("id")]."';\n";
		echo "changeHeader(".$row[csf("issue_basis")].");\n";
		if($row[csf("knit_dye_source")]==1)
			$location_com=$row[csf("knit_dye_company")];
		else
			$location_com=$row[csf("company_id")];
		echo "load_drop_down( 'requires/trims_issue_multi_ref_controller', $location_com, 'load_drop_down_sewing_location', 'location_sewing_td' );\n";
		$floor_unit=$row[csf('location_id')].'**'.$row[csf('knit_dye_company')].'**'.$row[csf('knit_dye_source')].'**'.$row[csf('issue_purpose')];
		echo "load_drop_down( 'requires/trims_issue_multi_ref_controller', $floor_unit, 'load_drop_down_sewing_floor_unit', 'floor_unit_td' );\n";
		
		
		$dtls_sql=sql_select("select order_id from inv_trims_issue_dtls where mst_id='".$row[csf("id")]."' and status_active=1");
		foreach($dtls_sql as $row)
		{
			$order_id_arr=explode(",",$row[csf("order_id")]);
			foreach($order_id_arr as $order_id)
			{
				$order_check=array();$all_order_id="";
				if($order_check[$order_id]=="")
				{
					$order_check[$order_id]=$order_id;
					$all_order_id.=$order_id.",";
					$all_order_no.=$po_num_arr[$order_id].",";
				}
			}
		}
		
		$all_order_id=implode(",",array_unique(explode(",",chop($all_order_id,","))));
		$all_order_no=implode(",",array_unique(explode(",",chop($all_order_no,","))));
		echo "document.getElementById('all_po_id').value 					= '".$all_order_id."';\n";
		echo "document.getElementById('txt_buyer_order').value 				= '".$all_order_no."';\n";
		
		exit();
	}
}

if($action=="show_trims_listview")
{
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$sql="select id, item_group_id, item_description, brand_supplier, issue_qnty, item_color_id, item_size, uom, order_id, item_order_id from inv_trims_issue_dtls where mst_id='$data' and status_active = '1' and is_deleted = '0'";
	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="670" class="rpt_table">
		<thead>
			<th width="70">Item Group</th>
			<th width="120">Item Description</th>               
			<th width="70">Item Color</th>
			<th width="50">Item Size</th>
			<th width="70">Supp Ref</th>
			<th width="40">UOM</th>
            <th width="80">Issue Qnty</th>
            <th>Buyer Order</th>
		</thead>
	</table>
	<div style="width:687px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="670" class="rpt_table" id="tbl_list_search_dtls">  
		<?
			$i=1;
			foreach ($result as $row)
			{  
				if ($i%2==0)  
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";
				
				$order_no="";
				if($row[csf("order_id")]!="")
				{
					if($db_type==0)
					{
						$order_no=return_field_value("group_concat(po_number) as po_no","wo_po_break_down","id in (".$row[csf("item_order_id")].")","po_no");	
					}
					else
					{
						$order_no=return_field_value("LISTAGG(po_number, ',') WITHIN GROUP (ORDER BY id) as po_no","wo_po_break_down","id in (".$row[csf("item_order_id")].")","po_no");		
					}
				}
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data(<? echo $row[csf('id')]; ?>,'populate_trims_details_form_data', 'requires/trims_issue_multi_ref_controller');"> 
					<td width="70"><p><? echo $trim_group_arr[$row[csf('item_group_id')]]['name']; ?></p></td>  
					<td width="120"><p><? echo $row[csf('item_description')]; ?></p></td>             
					<td width="70"><p><? echo $color_arr[$row[csf('item_color_id')]]; ?></p></td>
					<td width="50"><p><? echo $row[csf('item_size')]; ?></p></td>
					<td width="70"><p><? echo $row[csf('brand_supplier')]; ?></p></td>
                    <td width="40"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
					<td align="right" width="80"><? echo number_format($row[csf('issue_qnty')],2); ?></td>

                    <td><p><? echo $order_no; ?>&nbsp;</p></td>
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


if($action=='populate_trims_details_form_data')
{
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$issue_mst=sql_select("select a.issue_basis, a.booking_id from inv_issue_master a, inv_trims_issue_dtls b where a.id=b.mst_id and b.id='$data'");
	$issue_basis=$issue_mst[0][csf('issue_basis')];
	$booking_id=$issue_mst[0][csf('booking_id')];
	$data_array=sql_select("select b.id, b.trans_id, b.prod_id, b.item_group_id, b.item_description, b.brand_supplier, b.rack_no, b.shelf_no, b.issue_qnty, b.gmts_color_id, b.gmts_size_id, b.uom, b.order_id, b.item_order_id, b.item_order_id, b.item_color_id, b.item_size, b.floor_id, b.sewing_line, a.location_id, a.store_id, a.knit_dye_source, a.knit_dye_company, a.issue_purpose, b.rate as cons_rate from inv_trims_issue_dtls b,inv_issue_master a  where b.id='$data' and a.id=b.mst_id");
	foreach ($data_array as $row)
	{ 
		
		$conversion_fac=return_field_value("b.conversion_factor as conversion_factor","product_details_master a, lib_item_group b "," a.item_group_id=b.id and a.id=".$row[csf("prod_id")]."","conversion_factor");
		
		echo "document.getElementById('cbo_item_group').value 				= '".$row[csf("item_group_id")]."';\n";
		echo "document.getElementById('cbo_uom').value 						= '".$row[csf("uom")]."';\n";
		echo "document.getElementById('txt_item_description').value 		= '".$row[csf("item_description")]."';\n";
		echo "document.getElementById('txt_issue_qnty').value 				= '".number_format($row[csf("issue_qnty")],2,'.','')."';\n";
		echo "document.getElementById('hidden_issue_qnty').value 			= '".number_format($row[csf("issue_qnty")],2,'.','')."';\n";
		echo "document.getElementById('txt_brand_supref').value 			= '".$row[csf("brand_supplier")]."';\n";
		echo "document.getElementById('txt_rack').value 					= '".$row[csf("rack_no")]."';\n";
		echo "document.getElementById('txt_shelf').value 					= '".$row[csf("shelf_no")]."';\n";
		echo "document.getElementById('txt_item_color').value 				= '".$color_arr[$row[csf("item_color_id")]]."';\n";
		echo "document.getElementById('txt_item_color_id').value 			= '".$row[csf("item_color_id")]."';\n";
		echo "document.getElementById('gmts_color_id').value 				= '".$row[csf("gmts_color_id")]."';\n";
		echo "document.getElementById('txt_item_size').value 				= '".$row[csf("item_size")]."';\n";
		echo "document.getElementById('gmts_size_id').value 				= '".$row[csf("gmts_size_id")]."';\n"; 
		echo "document.getElementById('all_po_id').value 					= '".$row[csf("order_id")]."';\n";
		echo "document.getElementById('selected_po_id').value 				= '".$row[csf("item_order_id")]."';\n";
		echo "document.getElementById('hidden_prod_id').value 				= '".$row[csf("prod_id")]."';\n";
		echo "document.getElementById('previous_prod_id').value 			= '".$row[csf("prod_id")]."';\n";
		echo "document.getElementById('update_dtls_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('update_trans_id').value 				= '".$row[csf("trans_id")]."';\n";
		echo "document.getElementById('txt_conversion_faction').value 		= '".$conversion_fac."';\n";
		echo "document.getElementById('txt_cons_rate').value 				= '".number_format($row[csf("cons_rate")],4,'.','')."';\n";
		
		echo "document.getElementById('cbo_floor').value 					= '".$row[csf("floor_id")]."';\n";
		echo "document.getElementById('cbo_sewing_line').value 				= '".$row[csf("sewing_line")]."';\n";
		
		
		
		if($issue_basis==2)
		{
			$order_no="";
			$buyer_name=return_field_value("buyer_id","wo_non_ord_samp_booking_mst","id='$booking_id'");
			
			echo "get_php_form_data('".$booking_id."'+'**'+".$row[csf("prod_id")].",'get_trim_cum_info_for_trims_booking','requires/trims_issue_multi_ref_controller')".";\n";
			//echo "show_list_view('".$booking_id."','create_itemDesc_search_list_view_on_booking','list_fabric_desc_container','requires/trims_issue_multi_ref_controller','');\n";
		}
		else
		{
			if($db_type==0)
			{
				$order_data=sql_select("select group_concat(a.po_number) as po_no, group_concat(distinct(b.buyer_name)) as buyer_name from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.id in(".$row[csf("item_order_id")].")");
			}
			else
			{
				$order_data=sql_select("select LISTAGG(cast(a.po_number as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as po_no, LISTAGG(b.buyer_name, ',') WITHIN GROUP (ORDER BY b.id) as buyer_name from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.id in(".$row[csf("item_order_id")].")");
			}
			
			$order_no=implode(",",array_unique(explode(",",$order_data[0][csf('po_no')])));//$order_data[0][csf('po_no')];
			$buyer_name=implode(",",array_unique(explode(",",$order_data[0][csf('buyer_name')])));//$order_data[0][csf('buyer_name')];
			
			echo "get_php_form_data('".$row[csf("order_id")]."'+'**'+".$row[csf("prod_id")].", 'get_trim_cum_info', 'requires/trims_issue_multi_ref_controller')".";\n";
			echo "show_list_view('".$row[csf("order_id")]."'+'**'+".$row[csf("prod_id")]."+'**'+".$row[csf("store_id")].", 'create_itemDesc_search_list_view','list_fabric_desc_container','requires/trims_issue_multi_ref_controller','setFilterGrid(\'tbl_list_search\',-1);');\n";
			//echo "setFilterGrid('tbl_list_search',0);\n";
		}
		
		echo "document.getElementById('txt_buyer_order').value 				= '".$order_no."';\n";

		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_trims_issue',1,1);\n";  
		exit();
	}
}


if ($action=="trims_issue_entry_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	
	$buyer_short=return_library_array( "select id,short_name from lib_buyer", "id","short_name"  );
	$store_library=return_library_array( "select id,store_name from lib_store_location", "id","store_name"  );
	$company_arr = return_library_array("select id, company_name from lib_company","id","company_name");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$rack_self_bin = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst","floor_room_rack_id","floor_room_rack_name");
	
	$floor_arr = return_library_array("select id, floor_name from lib_prod_floor","id","floor_name");
	$sewing_line_arr = return_library_array("select id,line_name from lib_sewing_line","id","line_name");
	
	$sql="select id, issue_number, issue_date, challan_no,issue_purpose, store_id, knit_dye_source, knit_dye_company, location_id, remarks from inv_issue_master where id='$data[1]' and status_active=1 and is_deleted=0 and entry_form=25 ";
	//echo $sql;
	$dataArray=sql_select($sql);
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
?>
<div style="width:1100px; " align="center">
    <table width="1100" cellspacing="0" align="right" border="0" style="margin-bottom:10px">
        <tr>
            <td rowspan="2" colspan="2">
            	<img src="../../../<? echo $image_location; ?>" height="70" width="200" style="float:left;">
            </td>
            <td colspan="4" align="center" style="font-size:xx-large;">
            	<strong  style="float:left; margin-left:32px;"><? echo $company_arr[$data[0]]; ?></strong>
        	</td>
        </tr>
        <tr class="form_caption">
            <td colspan="4" align="left" style="padding-left:65px;">
				<?
					$address='';
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
					foreach($nameArray as $result)
					{ 
						$address=$result[csf('plot_no')];
						if($address=="") $address=$result[csf('road_no')]; else $address.=", ".$result[csf('road_no')];
						if($address=="") $address=$result[csf('block_no')]; else $address.=", ".$result[csf('block_no')];
						if($address=="") $address=$result[csf('city')]; else $address.=", ".$result[csf('city')];
                    }
					echo $address;
					$location='';
					if($dataArray[0][csf('knit_dye_source')]==1)
					{
						$caption="Location";
						$issueTo=$company_arr[$dataArray[0][csf('knit_dye_company')]];
						$location=return_field_value("location_name","lib_location","id='".$dataArray[0][csf('location_id')]."'");
					}
					else
					{
						$caption="Address";
						$supplierData=sql_select("select address_1, address_2, supplier_name from lib_supplier where id='".$dataArray[0][csf('knit_dye_company')]."'");
						$issueTo=$supplierData[0][csf('supplier_name')];
						$location=$supplierData[0][csf('address_1')];
						if($location=="") $location=$supplierData[0][csf('address_2')]; else $location.=", ".$supplierData[0][csf('address_2')];
					}
                ?> 
            </td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:x-large"><strong><u>Accessories Issue Challan</u></strong></center></td>
        </tr>
        <tr>
            <td width="100"><strong>Issue No:</strong></td> <td width="200"><? echo $dataArray[0][csf('issue_number')]; ?></td>
            <td width="100"><strong>Issue Date :</strong></td><td width="200"><? echo  change_date_format($dataArray[0][csf('issue_date')]); ?></td>
            <td width="100"><strong>Issue Purpose :</strong></td><td width="200"><? echo  $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Challan No :</strong></td><td width="175px" ><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td><strong>Store Name :</strong></td> <td><? echo $store_library[$dataArray[0][csf('store_id')]]; ?></td>
            <td><strong>Source :</strong></td><td><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Issue To :</strong></td><td><? echo $issueTo; ?></td>
            <td><strong><? echo $caption; ?> :</strong></td><td colspan="3"><? echo $location; ?></td>
        </tr>
        <tr>
           <td colspan="2" id="barcode_img_id"></td><td><strong>Remarks :</strong></td><td colspan="3"><? echo $dataArray[0][csf('remarks')]; ?></td>
        </tr>
    </table>
    <br>
	<div style="width:100%;">
        <table align="right" cellspacing="0" width="1100" border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="30">SL</th>
                <th width="120" align="center">Item Group</th>
                <th width="140" align="center">Item Des.</th>
                <th width="70" align="center">Item Color</th>
                <th width="70" align="center">Job No.</th>
                <th width="70" align="center">Buyer</th>
                <th width="70" align="center">Style Ref.</th>
                <th width="150" align="center">Buyer Order</th>
                <th width="70" align="center">Internal Ref</th>
                <th width="150" align="center">Article No.</th>
                <th width="60" align="center">UOM </th>
                <th width="70" align="center">Item Size</th>
                <th width="80" align="center">Issue Qty</th>                
                <th width="70" align="center">Floor</th>
                <th width="80" align="center">Sewing Line</th>              
                <th width="70" align="center">Rack</th>
                <th width="70" align="center">Self</th>
                <th width="70" align="center">Bin</th>
            </thead>
			<?
                $i=1; 
                $mst_id=$dataArray[0][csf('id')];
				//$sql_dtls="select b.id, b.item_group_id, b.item_description, b.gmts_color_id, b.gmts_size_id, b.order_id, b.uom, b.issue_qnty, c.room_no, c.rack_no, c.self_no, c.box_bin_no, c.ctn_no, c.ctn_qnty from inv_trims_issue_dtls b left join inv_goods_placement c on b.id=c.dtls_id and c.entry_form=25 where b.mst_id='$mst_id' and b.status_active='1' and b.is_deleted='0'";
				$sql_dtls="SELECT a.id, a.item_group_id, d.item_description, d.brand_supplier, a.item_color_id, a.item_size, a.order_id, a.item_order_id, a.uom, a.issue_qnty, a.sewing_line, a.floor_id, b.rack, b.bin_box, b.self, d.item_group_id, a.gmts_color_id, d.item_color, a.gmts_size_id, d.item_size 
				from inv_trims_issue_dtls a, inv_transaction b, product_details_master d 
				where a.mst_id='$mst_id' and a.trans_id=b.id and d.id=b.prod_id and a.status_active='1' and b.status_active='1' and d.status_active='1' and a.is_deleted='0'";
                //echo $sql_dtls;
                $sql_result=sql_select($sql_dtls);
	           	$all_po_id="";
     			foreach($sql_result as $row)
				{
					$all_po_id.=$row[csf("order_id")].",";
				}
				
			   $all_po_id=chop($all_po_id,",");

			   $booking_sql = "select b.po_break_down_id, b.sensitivity, b.trim_group, b.uom, c.description, c.color_number_id as color_id, c.item_color, c.gmts_sizes as size_id, c.item_size, c.brand_supplier, c.rate, c.cons as book_qnty, c.item_ref 
			   from wo_booking_dtls b, wo_trim_book_con_dtls c, wo_po_break_down d where d.id=c.po_break_down_id and b.id=c.wo_trim_booking_dtls_id  and c.cons>0 and b.status_active=1 and b.is_deleted=0 and d.id in ($all_po_id)";
				//echo $booking_sql;die;
				$booking_result=sql_select($booking_sql);
				$booking_pi_data=array();
				foreach($booking_result as $row)
				{
					$trim_key=$row[csf("po_break_down_id")]."__".$row[csf("trim_group")]."__".$row[csf("color_id")]."__".$row[csf("item_color")]."__".$row[csf("size_id")]."__".$row[csf("item_size")]."__".$row[csf("description")]."__".$row[csf("brand_supplier")];
			
					$booking_pi_data[$trim_key]=$row[csf("item_ref")];
				}

				//echo "<pre>";print_r($booking_pi_data);die;

                
                foreach($sql_result as $row)
                {
					
					// $trim_key=$row[csf("po_breakdown_id")]."__".$row[csf("item_group_id")]."__".$row[csf("gmts_color_id")]."__".$row[csf("item_color")]."__".$row[csf("gmts_size_id")]."__".$row[csf("item_size")];, a.item_description, a.brand_supplier
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        
                    $order_no=$row[csf('item_order_id')];
					if($db_type==0)
					{
                    	//$po_number = return_field_value("group_concat(po_number) as po_number","wo_po_break_down"," id in ($order_no)","po_number");
						$job_data=sql_select("select 
							group_concat(DISTINCT a.job_no) as job_no,
							group_concat(DISTINCT a.style_ref_no) as style_ref_no,
							group_concat(DISTINCT a.buyer_name) as buyer_name,
							group_concat(DISTINCT a.grouping) as grouping,
							group_concat(b.po_number) as po_number
						 from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and b.id in ($order_no) group by a.job_no,a.style_ref_no,a.buyer_name,b.grouping");
					
						$job_no=$job_data[0][csf('job_no')];
						$style_ref_no=$job_data[0][csf('style_ref_no')];
						$grouping=$job_data[0][csf('grouping')];
						$buyer=$job_data[0][csf('buyer_name')];
						$buyer_name='';
						foreach(explode(',',$buyer) as $buyer_id){
							if($buyer_name)$buyer_name=$buyer_short[$buyer_id].',';	else $buyer_name=$buyer_short[$buyer_id];
						}
						$po_no=$job_data[0][csf('po_no')];
					}
					else
					{
						//$po_number = return_field_value("LISTAGG(po_number, ',') WITHIN GROUP (ORDER BY id) as po_number","wo_po_break_down"," id in ($order_no)","po_number");
						$job_data=sql_select("select 
						LISTAGG(cast(a.job_no as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as job_no,
						LISTAGG(cast(a.style_ref_no as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as style_ref_no,
						LISTAGG(cast(a.buyer_name as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as buyer_name,
						LISTAGG(cast(b.po_number as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.id) as po_no,LISTAGG(cast(b.grouping as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.id) as grouping
						
						 from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and b.id in ($order_no) group by a.job_no,a.style_ref_no,a.buyer_name");
						$job_no=implode(',',array_unique(explode(',',$job_data[0][csf('job_no')])));
						$style_ref_no=implode(',',array_unique(explode(',',$job_data[0][csf('style_ref_no')])));
						$grouping=implode(',',array_unique(explode(',',$job_data[0][csf('grouping')])));
						$buyer=array_unique(explode(',',$job_data[0][csf('buyer_name')]));
						
						$buyer_name='';
						foreach($buyer as $buyer_id){
							if($buyer_name)$buyer_name=$buyer_short[$buyer_id].',';	else $buyer_name=$buyer_short[$buyer_id];
						}
						
						
						$po_no=implode(',',array_unique(explode(',',$job_data[0][csf('po_no')])));
						foreach($job_data as $int_ref_row){
							$int_ref .= $int_ref_row[csf('grouping')].",";
							$job_no .= $int_ref_row[csf('job_no')].",";
							$style_ref_no .= $int_ref_row[csf('style_ref_no')].",";
							$po_no_arr .= $int_ref_row[csf('po_no')].",";
						}
						$all_int_ref = ltrim(implode(",", array_unique(explode(",", chop($int_ref, ",")))), ',');
						$all_job_no = ltrim(implode(",", array_unique(explode(",", chop($job_no, ",")))), ',');
						$all_style_ref_no = ltrim(implode(",", array_unique(explode(",", chop($style_ref_no, ",")))), ',');
						$all_po_no = ltrim(implode(",", array_unique(explode(",", chop($po_no_arr, ",")))), ',');


					}
					
					$order_id_arr=array_unique(explode(",",$row[csf('item_order_id')]));
					$article_no="";
					foreach($order_id_arr as $po_id)
					{
						$trim_key=$po_id."__".$row[csf("item_group_id")]."__".$row[csf("gmts_color_id")]."__".$row[csf("item_color")]."__".$row[csf("gmts_size_id")]."__".$row[csf("item_size")]."__".$row[csf("item_description")]."__".$row[csf("brand_supplier")];
						$article_no.=$booking_pi_data[$trim_key].",";
					}
					$article_no=chop($article_no,",");
					
                	?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td><? echo $i; ?></td>
                        <td style="word-break:break-all;"><? echo $trim_group_arr[$row[csf('item_group_id')]]['name']; ?></td>
                        <td style="word-break:break-all;"><? echo $row[csf('item_description')]; ?></td>
                        <td style="word-break:break-all;"><? echo $color_arr[$row[csf('item_color_id')]]; ?></td>
                        <td style="word-break:break-all;"><? echo $all_job_no ; //$job_no; ?></td>
                        <td style="word-break:break-all;"><? echo $buyer_name; ?></td>
                        <td style="word-break:break-all;"><? echo $all_style_ref_no ; //$style_ref_no; ?></td>
                        <td  style="word-break:break-all;"><? echo $all_po_no; // $po_no; ?></td>
                        <td align="center" style="word-break:break-all;"><? echo $all_int_ref; ?></td>
                        <td align="center" style="word-break:break-all;"><? echo $article_no; ?></td>
                        <td align="center" style="word-break:break-all;"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                        <td align="center" style="word-break:break-all;"><? echo $row[csf('item_size')]; ?></td>
                        <td align="right"><? echo number_format($row[csf('issue_qnty')],2,'.',''); ?></td>
                        <td align="center" style="word-break:break-all;"><? echo $floor_arr[$row[csf('floor_id')]]; ?></td>
                        <td align="right" style="word-break:break-all;"><? echo $sewing_line_arr[$row[csf('sewing_line')]]; ?></td>                       
                        <td align="center" style="word-break:break-all;"><? echo $rack_self_bin[$row[csf('rack')]]; ?></td>
                        <td align="center" style="word-break:break-all;"><? echo $rack_self_bin[$row[csf('self')]]; ?></td>
                        <td align="center" style="word-break:break-all;"><? echo $rack_self_bin[$row[csf('bin_box')]]; ?></td>
                    </tr>
                	<?
                    $i++;
                }
			?>
		   </table>
           <br>
           <?
          	 echo signature_table(36, $data[0], "1000px");
		   ?>
		</div>
   </div>
    <script type="text/javascript" src="../../../js/jquery.js"></script>
    <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
    <script>

	function generateBarcode( valuess ){
		   
			var value = valuess;//$("#barcodeValue").val();
		 // alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			 
			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 30,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			 value = {code:value, rect: false};
			
			$("#barcode_img_id").show().barcode(value, btype, settings);
		  
		} 
  
	 generateBarcode('<? echo $data[2]; ?>');
	 
	 
	 </script>
<?
exit();
}

//for Urmi
if ($action=="trims_issue_entry_print2")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	
	$buyer_short=return_library_array( "select id,short_name from lib_buyer", "id","short_name"  );
	$store_library=return_library_array( "select id,store_name from lib_store_location", "id","store_name"  );
	$company_arr = return_library_array("select id, company_name from lib_company","id","company_name");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$po_number_arr = return_library_array("select id, po_number from wo_po_break_down","id","po_number");
	$floor_arr = return_library_array("select id, floor_name from lib_prod_floor","id","floor_name");
	$sewing_line_arr = return_library_array("select id,line_name from lib_sewing_line","id","line_name");
	
	$sql="select id, issue_number, issue_date, 	issue_basis, challan_no,issue_purpose, store_id, knit_dye_source, knit_dye_company, location_id, remarks from inv_issue_master where id='$data[1]' and status_active=1 and is_deleted=0 and entry_form=25 ";
	//echo $sql;
	
	$dataArray=sql_select($sql);
	$issue_basis=$dataArray[0][csf('issue_basis')];
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
?>
<div style="width:930px;">
    <table width="900" cellspacing="0" align="right" border="0" style="margin-bottom:10px">
        <tr>
            <td rowspan="2" colspan="2" >
           	 	<img src="../../../<? echo $image_location; ?>" height="70" width="200" style="float:left;">
           	</td>
           	<td colspan="4" align="center" style="font-size:xx-large;">
            	<strong style="float:left; margin-left:18px;"><? echo $company_arr[$data[0]]; ?></strong>
        	</td>
        </tr>
        <tr class="form_caption">
            <td colspan="4" align="left" style="padding-left:50px;">
				<?
					$address='';
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
					foreach($nameArray as $result)
					{ 
						$address=$result[csf('plot_no')];
						if($address=="") $address=$result[csf('road_no')]; else $address.=", ".$result[csf('road_no')];
						if($address=="") $address=$result[csf('block_no')]; else $address.=", ".$result[csf('block_no')];
						if($address=="") $address=$result[csf('city')]; else $address.=", ".$result[csf('city')];
                    }
					echo $address;
					$location='';
					if($dataArray[0][csf('knit_dye_source')]==1)
					{
						$caption="Location";
						$issueTo=$company_arr[$dataArray[0][csf('knit_dye_company')]];
						$location=return_field_value("location_name","lib_location","id='".$dataArray[0][csf('location_id')]."'");
					}
					else
					{
						$caption="Address";
						$supplierData=sql_select("select address_1, address_2, supplier_name from lib_supplier where id='".$dataArray[0][csf('knit_dye_company')]."'");
						$issueTo=$supplierData[0][csf('supplier_name')];
						$location=$supplierData[0][csf('address_1')];
						if($location=="") $location=$supplierData[0][csf('address_2')]; else $location.=", ".$supplierData[0][csf('address_2')];
					}
                ?> 
            </td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:x-large"><strong><u>Accessories Issue Challan</u></strong></center></td>
        </tr>
        <tr>
            <td width="90"><strong>Issue No:</strong></td> <td width="175px"><? echo $dataArray[0][csf('issue_number')]; ?></td>
            <td width="90"><strong>Issue Date :</strong></td><td width="175px"><? echo  change_date_format($dataArray[0][csf('issue_date')]); ?></td>
            <td width="95"><strong>Issue Purpose :</strong></td><td width="175px"><? echo  $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Challan No :</strong></td><td width="175px" ><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td><strong>Store Name :</strong></td> <td><? echo $store_library[$dataArray[0][csf('store_id')]]; ?></td>
            <td><strong>Source :</strong></td><td><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Issue To :</strong></td><td><? echo $issueTo; ?></td>
            <td><strong><? echo $caption; ?> :</strong></td><td colspan="3"><? echo $location; ?></td>
        </tr>
        <tr>
           <td colspan="2" id="barcode_img_id"></td><td><strong>Remarks :</strong></td><td colspan="3"><? echo $dataArray[0][csf('remarks')]; ?></td>
        </tr>
    </table>
    <br>
	<div style="width:100%;">
        <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="30">SL</th>
                <th width="120" align="center">Item Group</th>
                <th width="140" align="center">Item Des.</th>
                <th width="70" align="center">Item Color</th>
                <th width="70" align="center">Job No.</th>
                <th width="70" align="center">Buyer</th>
                <th width="70" align="center">Style Ref.</th>
                <th width="120" align="center">Buyer Order</th>
                <th width="60" align="center">UOM </th>
                <th width="70" align="center">Item Size</th>
                <th width="80" align="center">Issue Qty</th>
                <th width="70" align="center">Floor</th>
                <th width="80" align="center">Sewing Line</th>
                <th width="70" align="center">Rack</th>
                <th width="70" align="center">Self</th>
            </thead>
			<?
                $i=1; 
                $mst_id=$dataArray[0][csf('id')];
                //$sql_dtls="select b.id, b.item_group_id, b.item_description, b.gmts_color_id, b.gmts_size_id, b.order_id, b.uom, b.issue_qnty, c.room_no, c.rack_no, c.self_no, c.box_bin_no, c.ctn_no, c.ctn_qnty from inv_trims_issue_dtls b left join inv_goods_placement c on b.id=c.dtls_id and c.entry_form=25 where b.mst_id='$mst_id' and b.status_active='1' and b.is_deleted='0'";
				if($issue_basis==2)//Without Order
				{
				 $sql_dtls="select id, item_group_id, item_description, item_color_id, item_size, order_id, uom, issue_qnty, rack_no, shelf_no,sewing_line,floor_id from inv_trims_issue_dtls where mst_id='$mst_id' and status_active='1' and is_deleted='0'";
                //echo $sql_dtls;
                $sql_result=sql_select($sql_dtls);
				}
				else if($issue_basis==1)
				{
					$sql_dtls="select b.id, b.item_group_id, b.item_description, b.item_color_id, b.item_size, b.order_id, b.uom, c.po_breakdown_id as po_id,c.quantity as issue_qnty, b.rack_no, b.shelf_no,b.sewing_line,b.floor_id from inv_trims_issue_dtls b,order_wise_pro_details c where c.dtls_id=b.id and c.prod_id=b.prod_id and c.entry_form=25 and  b.mst_id='$mst_id' and b.status_active='1' and b.is_deleted='0'";
				 $sql_result=sql_select($sql_dtls);
				}
                
                foreach($sql_result as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        
                    $order_no=$row[csf('order_id')];
					if($db_type==0)
					{
                    	//$po_number = return_field_value("group_concat(po_number) as po_number","wo_po_break_down"," id in ($order_no)","po_number");
						$job_data=sql_select("select 
							group_concat(DISTINCT a.job_no) as job_no,
							group_concat(DISTINCT a.style_ref_no) as style_ref_no,
							group_concat(DISTINCT a.buyer_name) as buyer_name
							
						 from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and b.id in ($order_no) group by a.job_no,a.style_ref_no,a.buyer_name");
					
						$job_no=$job_data[0][csf('job_no')];
						$style_ref_no=$job_data[0][csf('style_ref_no')];
						$buyer=$job_data[0][csf('buyer_name')];
						$buyer_name='';
						foreach(explode(',',$buyer) as $buyer_id){
							if($buyer_name)$buyer_name=$buyer_short[$buyer_id].',';	else $buyer_name=$buyer_short[$buyer_id];
						}
						//$po_no=$job_data[0][csf('po_no')];
					}
					else
					{
						//$po_number = return_field_value("LISTAGG(po_number, ',') WITHIN GROUP (ORDER BY id) as po_number","wo_po_break_down"," id in ($order_no)","po_number");
						$job_data=sql_select("select 
						LISTAGG(cast(a.job_no as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as job_no,
						LISTAGG(cast(a.style_ref_no as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as style_ref_no,
						LISTAGG(cast(a.buyer_name as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as buyer_name
						
						
						 from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and b.id in ($order_no) group by a.job_no,a.style_ref_no,a.buyer_name");
						$job_no=implode(',',array_unique(explode(',',$job_data[0][csf('job_no')])));
						$style_ref_no=implode(',',array_unique(explode(',',$job_data[0][csf('style_ref_no')])));
						$buyer=array_unique(explode(',',$job_data[0][csf('buyer_name')]));
						
						$buyer_name='';
						foreach($buyer as $buyer_id){
							if($buyer_name)$buyer_name=$buyer_short[$buyer_id].',';	else $buyer_name=$buyer_short[$buyer_id];
						}
						
						
						//$po_no=implode(',',array_unique(explode(',',$job_data[0][csf('po_no')])));
					}
					
					
                ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td><? echo $i; ?></td>
                        <td><p><? echo $trim_group_arr[$row[csf('item_group_id')]]['name']; ?></p></td>
                        <td><p><? echo $row[csf('item_description')]; ?></p></td>
                        <td><p><? echo $color_arr[$row[csf('item_color_id')]]; ?></p></td>
                        <td><p><? echo $job_no; ?></p></td>
                        <td><p><? echo $buyer_name; ?></p></td>
                        <td><p><? echo $style_ref_no; ?></p></td>
                        <td><p><? echo $po_number_arr [$row[csf('po_id')]]; ?></p></td>
                        <td align="center"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                        <td align="center"><? echo $row[csf('item_size')]; ?></td>
                        <td align="right"><? echo number_format($row[csf('issue_qnty')],2,'.',''); ?></td>
                        <td align="center"><? echo $floor_arr[$row[csf('floor_id')]]; ?></td>
               			<td align="right"><? echo $sewing_line_arr[$row[csf('sewing_line')]]; ?></td>
                        <td align="center"><p><? echo $row[csf('rack_no')]; ?></p></td>
                        <td align="center"><p><? echo $row[csf('shelf_no')]; ?></p></td>
                    </tr>
                <?
                    $i++;
                }
			?>
		   </table>
           <br>
           <?
          	 echo signature_table(36, $data[0], "900px");
		   ?>
		</div>
   </div>
    <script type="text/javascript" src="../../../js/jquery.js"></script>
    <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
    <script>

	function generateBarcode( valuess ){
		   
			var value = valuess;//$("#barcodeValue").val();
		 // alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			 
			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 30,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			 value = {code:value, rect: false};
			
			$("#barcode_img_id").show().barcode(value, btype, settings);
		  
		} 
  
	 generateBarcode('<? echo $data[2]; ?>');
	 
	 
	 </script>
<?
exit();
}
?>
