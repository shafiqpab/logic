<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$user_id=$_SESSION['logic_erp']['user_id'];

// Credential condition
$userCredential = sql_select("select unit_id as company_id, item_cate_id, company_location_id, store_location_id from user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$location_credential_id = $userCredential[0][csf('company_location_id')];
$store_credential_id = $userCredential[0][csf('store_location_id')];

if ($company_id !='') {
    $company_credential_cond = "and id in($company_id)";
}
if ($location_credential_id !='') {
    $location_credential_cond = "and id in($location_credential_id)";
}
if ($store_credential_id !='') {
    $store_credential_cond = "and a.id in($store_credential_id)";
}

 //-------------------START ----------------------------------------


if ($action === "load_drop_down_store")
{
	echo create_drop_down( "cbo_from_store", 160, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data and b.category_type=1 $store_credential_cond group by a.id, a.store_name order by a.store_name", "id,store_name", 1, "--Select Store--", "", "clear_lot_dtls()", "");
	exit();
}

if($action=="lot_popup")
{
    extract($_REQUEST);
    echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
    $company=str_replace("'","",$company);
    $store=str_replace("'","",$store);
    ?>
    <script>

        var selected_id = new Array;
        var selected_name = new Array;
        var selected_no = new Array;
        function check_all_data()
        {
            var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
            tbl_row_count = tbl_row_count - 1;
            for( var i = 1; i <= tbl_row_count; i++ )
            {
                var onclickString = $('#tr_' + i).attr('onclick');
                var paramArr = onclickString.split("'");
                var functionParam = paramArr[1];
                //alert(functionParam);return;
                js_set_value( functionParam );
            }
        }

        function toggle( x, origColor )
        {
            var newColor = 'yellow';
            if ( x.style )
            {
                x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
            }
        }

        function js_set_value( strCon )
        {
            var splitSTR = strCon.split("_");
            var str_or = splitSTR[0];
            var selectID = splitSTR[1];
            var selectDESC = splitSTR[2];
            toggle( document.getElementById( 'tr_' + str_or ), '#FFFFCC' );

            if( jQuery.inArray( selectID, selected_id ) == -1 ) {
                selected_id.push( selectID );
                selected_name.push( selectDESC );
                selected_no.push( str_or );
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
            $('#selected_lot_id').val( id );
            $('#txt_selected_lot').val( name );
            $('#selected_lot_no').val( num );
        }

        function change_search_by(val){
            if(val == 1){
                $('#title_change').text('Enter Lot No.');
            }else{
                $('#title_change').text('Enter Product Id');
            }
        }
    </script>

    <div align="center" style="width:100%;">
        <form name="searchreceivefrm_1"  id="searchreceivefrm_1" autocomplete="off">
            <table width="450" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                <thead>
                <tr>
                    <th width="150">Search By</th>
                    <th width="170" id="title_change">Enter Product Id</th>
                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:60px;" /></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td align="center">
                        <?
                        $search_by = array(1=>'Lot No.', 2=>'Product Id');
                        echo create_drop_down("cbo_search_by", 140, $search_by,"", '0',"-- Select --",2,"change_search_by(this.value);");
                        ?>
                    </td>
                    <td align="center">
                        <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes" style="width:150px"/>
                    </td>
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( <?=$company?>+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+<?=$store?>, 'create_transfer_lot_list', 'search_div', 'scrap_transfer_entry_controller', 'setFilterGrid(\'list_view\',-1);selected_lot(\'<?=$selected_lot?>\', \'<?=$selected_lot_id?>\', \'<?=$selected_lot_no?>\');')" style="width:60px;" />
                    </td>
                </tr>
                <table>
                    <tr>
                        <td colspan="6" align="center" valign="top" id=""><div id="search_div"></div></td>
                    </tr>
                </table>
                </tbody>
            </table>
        </form>
        <input type='hidden' id='selected_lot_id' />
        <input type='hidden' id='txt_selected_lot' />
        <input type='hidden' id='selected_lot_no' />
    </div>
    <script language="javascript" type="text/javascript">
        function selected_lot(selected_lot='', selected_lot_id='', selected_lot_no='') {
            if (selected_lot != "") {
                selected_lot_arr = selected_lot.split(",");
                selected_lot_id_arr = selected_lot_id.split(",");
                selected_lot_sl = selected_lot_no.split(",");
                var loan_pary_ref = "";
                for (var k = 0; k < selected_lot_arr.length; k++) {
                    loan_pary_ref = selected_lot_sl[k] + '_' + selected_lot_id_arr[k] + '_' + selected_lot_arr[k];
                    js_set_value(loan_pary_ref);
                }
            }
        }
    </script>

    <?

    exit();
}
if($action=="create_transfer_lot_list")
{
    list($company, $search_by, $search_common, $store) = explode("_", $data);
    $sql_cond = "";
    $sql_cond1 = "";
    if($search_by == 1 && $search_common != ""){
        $sql_cond = " and a.lot = '$search_common'";
        $sql_cond1 = " and a.yarn_lot = '$search_common'";
    }elseif($search_by == 2 && $search_common != ""){
        $sql_cond = " and a.product_id = $search_common";
        $sql_cond1 = " and a.to_prod_id = $search_common";
    }

    $sql = sql_select("select c.id, a.lot, c.product_name_details, c.supplier_id, b.store_id, c.company_id, c.brand, sum(a.receive_qnty) as receive_qnty  from inv_scrap_receive_dtls a, inv_scrap_receive_mst b, product_details_master c where a.mst_id = b.id and a.product_id = c.id and b.company_id = $company and b.store_id=$store and b.item_category_id = 1 $sql_cond and b.status_active = 1 and b.is_deleted = 0 group  by c.id, a.lot, c.product_name_details, c.supplier_id, b.store_id, c.company_id, c.brand order by c.id desc");
    $prod_id_array = array();
    $main_data_arr = [];
    foreach ($sql as $prod_id){
        array_push($prod_id_array, $prod_id[csf('id')]);
        $key = $prod_id[csf('id')]."**".$prod_id[csf('supplier_id')]."**".$prod_id[csf('lot')];
        $main_data_arr[$key]['ID'] = $prod_id[csf('id')];
        $main_data_arr[$key]['LOT'] = $prod_id[csf('lot')];
        $main_data_arr[$key]['SUPPLIER_ID'] = $prod_id[csf('supplier_id')];
        $main_data_arr[$key]['PRODUCT_NAME_DETAILS'] = $prod_id[csf('product_name_details')];
        $main_data_arr[$key]['STORE_ID'] = $prod_id[csf('store_id')];
        $main_data_arr[$key]['COMPANY_ID'] = $prod_id[csf('company_id')];
        $main_data_arr[$key]['BRAND'] = $prod_id[csf('brand')];
        $main_data_arr[$key]['QTY'] += $prod_id[csf('receive_qnty')];
    }
    $sql_prev_transfer_add = sql_select("select  a.to_prod_id, a.transfer_qnty, a.yarn_lot, a.to_store, c.product_name_details, c.supplier_id, c.company_id, c.brand from inv_item_transfer_dtls a, inv_item_transfer_mst b, product_details_master c where a.mst_id = b.id and a.to_prod_id = c.id and b.entry_form = 542 and b.company_id=$company and a.to_store = $store and a.status_active = 1 and a.is_deleted = 0 $sql_cond1");
    $transfer_to = array();
    foreach ($sql_prev_transfer_add as $val){
        $key = $val[csf('to_prod_id')]."**".$val[csf('supplier_id')]."**".$val[csf('yarn_lot')];
        $main_data_arr[$key]['ID'] = $val[csf('to_prod_id')];
        $main_data_arr[$key]['LOT'] = $val[csf('yarn_lot')];
        $main_data_arr[$key]['SUPPLIER_ID'] = $val[csf('supplier_id')];
        $main_data_arr[$key]['PRODUCT_NAME_DETAILS'] = $val[csf('product_name_details')];
        $main_data_arr[$key]['STORE_ID'] = $val[csf('to_store')];
        $main_data_arr[$key]['COMPANY_ID'] = $val[csf('company_id')];
        $main_data_arr[$key]['BRAND'] = $val[csf('brand')];
        $main_data_arr[$key]['QTY'] += $val[csf("transfer_qnty")];
    }
    $company_arr = return_library_array("select id, company_name from lib_company where status_active = 1 and is_deleted = 0", "id", "company_name");
    $supplier_arr = return_library_array("select id, supplier_name from lib_supplier where status_active = 1 and is_deleted = 0", "id", "supplier_name");
    $store_arr = return_library_array("select id, store_name from lib_store_location where status_active = 1 and is_deleted = 0", "id", "store_name");
    $brand_arr = return_library_array("select id, brand_name from lib_brand where status_active = 1 and is_deleted = 0", "id", "brand_name");

    $sql_prev_transfer_minus = sql_select("select a.from_prod_id, a.to_prod_id, a.transfer_qnty, a.yarn_lot, b.from_store_id from inv_item_transfer_dtls a, inv_item_transfer_mst b  where a.mst_id = b.id and b.entry_form = 542 and b.company_id=$company and b.from_store_id = $store and a.status_active = 1 and a.is_deleted = 0");
    $transfer_from = array();
    foreach ($sql_prev_transfer_minus as $val){
        $transfer_from[$val[csf("from_store_id")]][$val[csf("yarn_lot")]][$val[csf("from_prod_id")]] += $val[csf("transfer_qnty")];
    }

    $lot_cond = "";
    if(count($prod_id_array) > 0){
        $lot_uni = array_chunk(array_unique($prod_id_array), 999);
        foreach ($lot_uni as $key => $value){
            if($key == 0){
                $lot_cond .= " and a.prod_id in (".implode(",", $value).") ";
            }else{
                $lot_cond .= " or a.prod_id in (".implode(",", $value).") ";
            }
        }
    }
    $sql_issue = "select b.id, b.lot, b.supplier_id, sum(a.sales_qty) as sales_qty from inv_scrap_sales_dtls a, product_details_master b where a.prod_id = b.id and b.company_id = $company $lot_cond and a.status_active = 1 and a.is_deleted = 0 group by b.id, b.lot, b.supplier_id";
    $sql_issue_data_array = sql_select($sql_issue);

    $scrap_data_issue = array();
    foreach ($sql_issue_data_array as $data){
        $key = $data[csf('id')]."**".$data[csf('supplier_id')]."**".$data[csf('lot')];
        $scrap_data_issue[$key]['issue_qty'] += $data[csf('sales_qty')];
    }
    ?>
    <table width="840" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
        <thead>
        <tr>
            <th width="30">SL</th>
            <th width="80">Product ID</th>
            <th width="120">Company</th>
            <th width="110">Supplier</th>
            <th width="140">Item Details</th>
            <th width="80">Lot</th>
            <th width="90">Brand</th>
            <th width="100">Store</th>
            <th>Stock</th>
        </tr>
        </thead>
    </table>
    <div style="width:840px; overflow-y:scroll; max-height:240px;" id="buyer_list_view" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="825" class="rpt_table" id="list_view">
            <?
            $i=1;
            foreach ($main_data_arr as $row)
            {
                $key = $row['ID']."**".$row['SUPPLIER_ID']."**".$row['LOT'];
                $cur_stock = $row['QTY'] - $scrap_data_issue[$key]['issue_qty'] - $transfer_from[$row['STORE_ID']][$row['LOT']][$row['ID']];
                if($cur_stock > 0){
                    if ($i%2==0) $bgcolor="#E9F3FF";
                    else $bgcolor="#FFFFFF";
                    ?>
                    <tr style="text-decoration:none; cursor:pointer" id="tr_<?=$i?>" onclick="js_set_value('<?= $i; ?>_<?= $row['ID']; ?>_<?=$row['LOT']?>');" bgcolor="<?=$bgcolor?>">
                        <td width="30" align="center"><?= $i; ?></td>
                        <td width="80" align="center"><p><?= $row['ID']; ?></p></td>
                        <td width="120" ><p><?= $company_arr[$row['COMPANY_ID']]; ?></p></td>
                        <td width="110" ><p><?= $supplier_arr[$row['SUPPLIER_ID']]; ?></p></td>
                        <td width="140" ><p><?= $row['PRODUCT_NAME_DETAILS']; ?></p></td>
                        <td width="80" align="center"><p><?= $row['LOT']; ?></p></td>
                        <td width="90"><p><?= $brand_arr[$row['BRAND']]; ?></p></td>
                        <td width="100"><p><?= $store_arr[$row['STORE_ID']]; ?></p></td>
                        <td  align="right"><p><?=number_format($cur_stock, 2)?></p></td>
                    </tr>
                    <?
                    $i++;
                }
            }
            ?>
        </table>
    </div>
    <div class="check_all_container" style="padding-top: 5px;">
        <div style="width:100%">
            <div style="width:50%; float:left" align="left">
                <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
            </div>
            <div style="width:50%; float:left" align="left">
                <input type="button" name="close" id="close"  onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
            </div>
        </div>
    </div>

    <?

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

        if($db_type==0) $year_cond="YEAR(insert_date)";
        else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
        else $year_cond="";//defined Later

        $new_transfer_system_id = explode("*", return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst",$con,1,$cbo_company_id,'STE',542,date("Y",time())));
        $id = return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst", $con);

        $field_array="id, transfer_prefix, transfer_prefix_number, transfer_system_id, transfer_criteria, company_id, from_store_id, transfer_date, challan_no, item_category, remarks, entry_form, inserted_by, insert_date";
        $data_array="(".$id.",'".$new_transfer_system_id[1]."',".$new_transfer_system_id[2].",'".$new_transfer_system_id[0]."',".$cbo_transfer_criteria.",".$cbo_company_id.",".$cbo_from_store.",".$txt_transfer_date.",".$txt_challan_no.",".$cbo_item_category.",".$txt_remarks.",542,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

        $sys_challan_no=$new_transfer_system_id[0];
        $row_id=$id;

		$id_dtls=return_next_id( "id", "inv_item_transfer_dtls", 1);

		$field_array_dtls="id, mst_id, from_prod_id, to_prod_id, from_store, to_store, to_room, to_rack, to_shelf, to_bin_box, item_category, yarn_lot, transfer_qnty, inserted_by, insert_date";

        for ($i=1; $i<=$row_num; $i++)
	    {
			$prod_id = "prod_id_".$i;
			$yarn_lot = "yarn_lot_".$i;
            $trans_qty = "trans_qty_".$i;
            $to_store = "to_store_".$i;
            $cbo_room = "cbo_room_".$i;
            $cbo_rack = "cbo_rack_".$i;
            $cbo_shelf = "cbo_shelf_".$i;
            $cbo_bin = "cbo_bin_".$i;

			if ($i != 1) $data_array_dtls .=",";
			$data_array_dtls .="(".$id_dtls.",".$row_id.",".$$prod_id.",".$$prod_id.",".$cbo_from_store.",".$$to_store.",".$$cbo_room.",".$$cbo_rack.",".$$cbo_shelf.",".$$cbo_bin.",".$cbo_item_category.",".$$yarn_lot.",".$$trans_qty.",".$user_id.",'".$pc_date_time."')";

			$id_dtls = $id_dtls+1;
		}

        $rID=sql_insert("inv_item_transfer_mst",$field_array,$data_array,0);
        if($rID) $flag=1; else $flag=0;

		$rID2=sql_insert("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,0);
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
				echo "5**0**"."&nbsp;"."**0";
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
        $field_array_update="transfer_criteria*transfer_date*challan_no*remarks*updated_by*update_date";
		$data_array_update=$cbo_transfer_criteria."*".$txt_transfer_date."*".$txt_challan_no."*".$txt_remarks."*".$user_id."*'".$pc_date_time."'";

        $sys_challan_no=str_replace("'","",$txt_system_id);
		$row_id=str_replace("'","",$update_id);

        $field_array_dtls="to_store*to_room*to_rack*to_shelf*to_bin_box*transfer_qnty*updated_by*update_date";
        $data_arr_dtls = [];
        $hdn_dtls_id_arr = [];
        for ($i=1; $i<=$row_num; $i++)
        {
            $trans_qty = "trans_qty_".$i;
            $to_store = "to_store_".$i;
            $cbo_room = "cbo_room_".$i;
            $cbo_rack = "cbo_rack_".$i;
            $cbo_shelf = "cbo_shelf_".$i;
            $cbo_bin = "cbo_bin_".$i;
            $dtls_update_id = "dtls_update_id_".$i;
            $aa	=str_replace("'",'',$$dtls_update_id);

            $data_arr_dtls[$aa]=explode("*",($$to_store."*".$$cbo_room."*".$$cbo_rack."*".$$cbo_shelf."*".$$cbo_bin."*".$$trans_qty));
            $hdn_dtls_id_arr[]=$aa;
        }


		$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$row_id,0);
		if($rID) $flag=1; else $flag=0;

        if(count($data_arr_dtls) > 0 && $flag == 1) {
            $rID1 = execute_query(bulk_update_sql_statement( "inv_item_transfer_dtls", "id",$field_array_dtls,$data_arr_dtls,$hdn_dtls_id_arr),1);
            if ($rID1) $flag = 1; else $flag = 0;
        }
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "1**".$sys_challan_no."**".$row_id."**0";
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
				echo "1**".$sys_challan_no."**".$row_id."**0";
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
	else if ($operation==2)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
        $field_array_delete="updated_by*update_date*status_active*is_deleted";
        $data_array_delete="".$user_id."*'".$pc_date_time."'*0*1";

        $sys_challan_no=str_replace("'","",$txt_system_id);
		$row_id=str_replace("'","", $update_id);

        $rID=sql_update("inv_item_transfer_mst",$field_array_delete,$data_array_delete,"id",$row_id,0);
        if($rID) $flag=1; else $flag=0;
        if($flag == 1){
            $rID2=sql_update("inv_item_transfer_dtls",$field_array_delete,$data_array_delete,"mst_id",$row_id,0);
            if($rID2) $flag=1; else $flag=0;
        }
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "2**".$sys_challan_no."**".$row_id."**0";
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "6**0**0**2";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "2**".$sys_challan_no."**".$row_id."**0";
			}
			else
			{
				oci_rollback($con);
				echo "6**0**0**2";
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="show_dtls_listview")
{
    $data_arr = explode('*', $data);
    $supplier_arr = return_library_array("select id, supplier_name from lib_supplier where status_active = 1 and is_deleted = 0", "id", "supplier_name");
    if($data_arr[0] == 1){
        $ex_lot = explode(',', $data_arr[1]);
        $ex_lot = implode(",", $ex_lot);

        $sql_rcv = "select a.id as mst_id, b.id as dtls_id, b.product_id, c.product_name_details, b.lot, c.supplier_id, b.receive_qnty from inv_scrap_receive_mst a, inv_scrap_receive_dtls b, product_details_master c where b.product_id in ($ex_lot) and a.company_id = $data_arr[2] and a.store_id = $data_arr[3] and a.id = b.mst_id and b.product_id = c.id and a.status_active = 1 and a.is_deleted = 0 order by b.id asc";
        $sql_rcv_data_array = sql_select($sql_rcv);
        $scrap_data = array();
        foreach ($sql_rcv_data_array as $data){
            $key = $data[csf('product_id')]."**".$data[csf('supplier_id')]."**".$data[csf('lot')];
            $scrap_data[$key]['product_id'] = $data[csf('product_id')];
            $scrap_data[$key]['product_name'] = $data[csf('product_name_details')];
            $scrap_data[$key]['lot'] = $data[csf('lot')];
            $scrap_data[$key]['supplier_id'] = $data[csf('supplier_id')];
            $scrap_data[$key]['receive_qty'] += $data[csf('receive_qnty')];
        }
        $sql_prev_transfer_add = sql_select("select a.to_prod_id, a.transfer_qnty, a.yarn_lot, a.to_store, c.product_name_details, c.supplier_id from inv_item_transfer_dtls a, inv_item_transfer_mst b, product_details_master c  where a.mst_id = b.id and a.to_prod_id = c.id and b.entry_form = 542 and b.company_id=$data_arr[2] and a.to_store = $data_arr[3] and a.to_prod_id in ($ex_lot) and a.status_active = 1 and a.is_deleted = 0");
        $transfer_to = array();
        foreach ($sql_prev_transfer_add as $val){
            $key = $val[csf('to_prod_id')]."**".$val[csf('supplier_id')]."**".$val[csf('yarn_lot')];
            $scrap_data[$key]['product_id'] = $val[csf('to_prod_id')];
            $scrap_data[$key]['product_name'] = $val[csf('product_name_details')];
            $scrap_data[$key]['lot'] = $val[csf('yarn_lot')];
            $scrap_data[$key]['supplier_id'] = $val[csf('supplier_id')];
            $scrap_data[$key]['receive_qty'] += $val[csf("transfer_qnty")];
        }
        $sql_prev_transfer_minus = sql_select("select a.from_prod_id, a.to_prod_id, a.transfer_qnty, a.yarn_lot, b.from_store_id from inv_item_transfer_dtls a, inv_item_transfer_mst b  where a.mst_id = b.id and b.entry_form = 542 and b.company_id=$data_arr[2] and b.from_store_id = $data_arr[3] and a.status_active = 1 and a.is_deleted = 0");
        $transfer_from = array();
        foreach ($sql_prev_transfer_minus as $val){
            $transfer_from[$val[csf("from_store_id")]][$val[csf("yarn_lot")]][$val[csf("from_prod_id")]] += $val[csf("transfer_qnty")];
        }
        $sql_issue = "select b.id, b.lot, b.supplier_id, sum(a.sales_qty) as sales_qty from inv_scrap_sales_dtls a, product_details_master b where a.prod_id = b.id and b.company_id = $data_arr[2] and a.prod_id in ($ex_lot) and a.status_active = 1 and a.is_deleted = 0 group by b.id, b.lot, b.supplier_id";
        $sql_issue_data_array = sql_select($sql_issue);

        $scrap_data_issue = array();
        foreach ($sql_issue_data_array as $data){
            $key = $data[csf('id')]."**".$data[csf('supplier_id')]."**".$data[csf('lot')];
            $scrap_data_issue[$key]['issue_qty'] += $data[csf('sales_qty')];
        }

        $i=1;
        $totalStock = 0;
        foreach($scrap_data as $key => $row)
        {
            $cur_stock = $row['receive_qty'] - $scrap_data_issue[$key]['issue_qty'] - $transfer_from[$data_arr[3]][$row['lot']][$row['product_id']];
            if($cur_stock > 0){
                if ($i%2==0) $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";
            ?>
                <tr bgcolor="<?= $bgcolor; ?>" style="text-decoration:none;" class="data-row" id="row_<?=$i?>">
                    <td valign="middle" align="center" style="padding: 0px 3px;"><?= $i; ?><input type="hidden" class="text_boxes"  id="dtls_update_id_<?=$i?>" value="" /><input type="hidden" class="text_boxes"  id="prod_id_<?=$i?>" value="<?= $row['product_id']; ?>" /><input type="hidden" class="text_boxes"  id="yarn_lot_<?=$i?>" value="<?= $row['lot']; ?>" /> </td>
                    <td valign="middle" align="center" style="padding: 0px 3px;"><?= $row['product_id']; ?></td>
                    <td valign="middle" style="padding: 0px 3px;"><p><?= $row['product_name'];; ?></p></td>
                    <td valign="middle" align="center" style="padding: 0px 3px;"><?= $row['lot']; ?></td>
                    <td valign="middle" style="padding: 0px 3px;"><p><?=$supplier_arr[$row['supplier_id']]; ?></p></td>
                    <td valign="middle" align="right" style="padding: 0px 3px;"><?=number_format($cur_stock, 2);?><input type="hidden" id="current_stock_<?=$i?>" class="text_boxes" value="<?=$cur_stock?>" /></td>
                    <td valign="middle" align="center"><input type="text" id="trans_qty_<?=$i?>" style="width: 80px; text-align: right;"  name="trans_qty[]" class="text_boxes_numeric" onblur="check_stock(<?=$i?>)"></td>
                    <td valign="middle" align="center"><?= create_drop_down( "to_store_".$i, 105, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data_arr[2] and a.id <> $data_arr[3] and b.category_type=1 $store_credential_cond group by a.id, a.store_name order by a.store_name", "id,store_name", 1, "--Select Store--", "", "room_rack_shelf_bin_reset(4, ".$i.");load_drop_down('requires/scrap_transfer_entry_controller', this.value+'_".$data_arr[2]."_".$i."', 'load_drop_room','room_td_".$i."');", ""); ?></p></td>
                    <td valign="middle" align="center" id="room_td_<?=$i?>"><?=create_drop_down( "cbo_room_".$i, 95, "$blank_array","", 1, "--Select Room--", 0)?></td>
                    <td valign="middle" align="center" id="rack_td_<?=$i?>"><?=create_drop_down( "cbo_rack_".$i, 95, "$blank_array","", 1, "--Select Rack--", 0)?></td>
                    <td valign="middle" align="center" id="shelf_td_<?=$i?>"><?=create_drop_down( "cbo_shelf_".$i, 85, "$blank_array","", 1, "--Select Shelf--", 0)?></td>
                    <td valign="middle" align="center" id="bin_td_<?=$i?>"><?=create_drop_down( "cbo_bin_".$i, 85, "$blank_array","", 1, "--Select Bin--", 0)?></td>
                </tr>
            <?
                $totalStock += $cur_stock;
                $i++;
            }
        }
        ?>
        <tr>
            <td colspan="5" align="right" style="padding: 0px 3px;">
                <strong>Total: </strong>
            </td>
            <td align="right" style="padding: 0px 3px;">
                <strong><?=number_format($totalStock, 2)?></strong>
            </td>
            <td align="center">
                <input type="text" id="trans_total_qty" style="width: 80px; text-align: right; font-weight: bold;" class="text_boxes_numeric" readonly disabled>
            </td>
            <td colspan="5" align="right" style="padding: 0px 3px;">
            </td>
        </tr>
        <?
    }elseif($data_arr[0] == 2){
        $sql_transfer = "select a.id as mst_id, b.id as dtls_id, b.from_prod_id, c.product_name_details, b.yarn_lot, c.supplier_id, b.transfer_qnty, b.to_store, b.to_room, b.to_rack, b.to_shelf, b.to_bin_box from inv_item_transfer_mst a, inv_item_transfer_dtls b, product_details_master c where a.id = $data_arr[1] and a.company_id = $data_arr[2] and a.entry_form = 542 and a.id = b.mst_id and b.from_prod_id = c.id and a.status_active = 1 and a.is_deleted = 0 order by b.id";
        $sql_transfer_data_array = sql_select($sql_transfer);

        $scrap_data = array(); $lot_arr = array();
        foreach ($sql_transfer_data_array as $data){
            $key = $data[csf('from_prod_id')]."**".$data[csf('supplier_id')]."**".$data[csf('yarn_lot')];
            array_push($lot_arr, $data[csf('from_prod_id')]);
            $scrap_data[$key]['product_id'] = $data[csf('from_prod_id')];
            $scrap_data[$key]['product_name'] = $data[csf('product_name_details')];
            $scrap_data[$key]['lot'] = $data[csf('yarn_lot')];
            $scrap_data[$key]['to_store'] = $data[csf('to_store')];
            $scrap_data[$key]['to_room'] = $data[csf('to_room')];
            $scrap_data[$key]['to_rack'] = $data[csf('to_rack')];
            $scrap_data[$key]['to_shelf'] = $data[csf('to_shelf')];
            $scrap_data[$key]['to_bin_box'] = $data[csf('to_bin_box')];
            $scrap_data[$key]['dtls_update_id'] = $data[csf('dtls_id')];
            $scrap_data[$key]['supplier_id'] = $data[csf('supplier_id')];
            $scrap_data[$key]['transfer_qnty'] = $data[csf('transfer_qnty')];

        }
        $lot_cond = ""; $lot_cond1 = "";
        if(count($lot_arr) > 0){
            $lot_uni = array_chunk(array_unique($lot_arr), 999);
            foreach ($lot_uni as $key => $value){
                if($key == 0){
                    $lot_cond .= " and b.product_id in ('".implode("','", $value)."') ";
                    $lot_cond1 .= " and a.prod_id in ('".implode("','", $value)."') ";
                }else{
                    $lot_cond .= " or b.product_id in ('".implode("','", $value)."') ";
                    $lot_cond1 .= " or a.prod_id in ('".implode("','", $value)."') ";
                }
            }
        }
        $sql_rcv = "select a.id as mst_id, b.id as dtls_id, b.product_id, c.product_name_details, b.lot, c.supplier_id, b.receive_qnty from inv_scrap_receive_mst a, inv_scrap_receive_dtls b, product_details_master c where  a.id = b.mst_id $lot_cond and b.product_id = c.id and a.company_id = $data_arr[2] and a.store_id = $data_arr[3] and a.status_active = 1 and a.is_deleted = 0 order by b.id asc";
        $sql_rcv_data_array = sql_select($sql_rcv);

        $scrap_data_rcv = array();
        foreach ($sql_rcv_data_array as $data){
            $key = $data[csf('product_id')]."**".$data[csf('supplier_id')]."**".$data[csf('lot')];
            $scrap_data_rcv[$key]['receive_qty'] += $data[csf('receive_qnty')];
        }
        $sql_issue = "select b.id, b.lot, b.supplier_id, sum(a.sales_qty) as sales_qty from inv_scrap_sales_dtls a, product_details_master b where a.prod_id = b.id and b.company_id = $data_arr[2] and a.status_active = 1 and a.is_deleted = 0 $lot_cond1 group by b.id, b.lot, b.supplier_id";
        $sql_issue_data_array = sql_select($sql_issue);

        $scrap_data_issue = array();
        foreach ($sql_issue_data_array as $data){
            $key = $data[csf('id')]."**".$data[csf('supplier_id')]."**".$data[csf('lot')];
            $scrap_data_issue[$key]['issue_qty'] += $data[csf('sales_qty')];
        }

        $sql_prev_transfer_minus = sql_select("select a.from_prod_id, a.to_prod_id, a.transfer_qnty, a.yarn_lot, b.from_store_id from inv_item_transfer_dtls a, inv_item_transfer_mst b  where a.mst_id = b.id and b.entry_form = 542 and b.company_id=$data_arr[2] and b.id <> $data_arr[1] and b.from_store_id = $data_arr[3] and a.status_active = 1 and a.is_deleted = 0");
        $transfer_from = array();
        foreach ($sql_prev_transfer_minus as $val){
            $transfer_from[$val[csf("from_store_id")]][$val[csf("yarn_lot")]][$val[csf("from_prod_id")]] += $val[csf("transfer_qnty")];
        }
        $sql_prev_transfer_add = sql_select("select a.from_prod_id, a.to_prod_id, a.transfer_qnty, a.yarn_lot, a.to_store from inv_item_transfer_dtls a, inv_item_transfer_mst b  where a.mst_id = b.id and b.entry_form = 542 and b.company_id=$data_arr[2] and b.id <> $data_arr[1] and a.to_store = $data_arr[3] and a.status_active = 1 and a.is_deleted = 0");
        $transfer_to = array();
        foreach ($sql_prev_transfer_add as $val){
            $transfer_to[$val[csf("to_store")]][$val[csf("yarn_lot")]][$val[csf("to_prod_id")]] += $val[csf("transfer_qnty")];
        }

        $i=1;
        $totalStock = 0; $totalTrans = 0;
        foreach($scrap_data as $key => $row)
        {
            if ($i%2==0) $bgcolor="#E9F3FF";
            else $bgcolor="#FFFFFF";
            $cur_stock = $scrap_data_rcv[$key]['receive_qty'] - $scrap_data_issue[$key]['issue_qty'] - $transfer_from[$data_arr[3]][$row['lot']][$row['product_id']] + $transfer_to[$data_arr[3]][$row['lot']][$row['product_id']];
            $totalTrans += $row['transfer_qnty'];
            $store_id = $row['to_store'];
            $room_id=$row['to_room'];
            $rack=$row['to_rack'];
            $shelf=$row['to_shelf'];
            $bin = $row['to_bin_box'];
            ?>
            <tr bgcolor="<?= $bgcolor; ?>" style="text-decoration:none;" class="data-row" id="row_<?=$i?>">
                <td valign="middle" align="center" style="padding: 0px 3px;" title="">
                    <?= $i; ?>
                    <input type="hidden" class="text_boxes"  id="dtls_update_id_<?=$i?>" value="<?=$row['dtls_update_id']?>" />
                    <input type="hidden" class="text_boxes"  id="prod_id_<?=$i?>" value="<?= $row['product_id']; ?>" />
                    <input type="hidden" class="text_boxes"  id="yarn_lot_<?=$i?>" value="<?= $row['lot']; ?>" />
                </td>
                <td valign="middle" align="center" style="padding: 0px 3px;"><?= $row['product_id']; ?></td>
                <td valign="middle" style="padding: 0px 3px;"><p><?= $row['product_name'];; ?></p></td>
                <td valign="middle" align="center" style="padding: 0px 3px;"><?= $row['lot']; ?></td>
                <td valign="middle" style="padding: 0px 3px;"><p><?=$supplier_arr[$row['supplier_id']]; ?></p></td>
                <td valign="middle" align="right" style="padding: 0px 3px;"><?=number_format($cur_stock, 2);?><input type="hidden" id="current_stock_<?=$i?>" class="text_boxes" value="<?=$cur_stock?>" /></td>
                <td valign="middle" align="center"><input type="text" id="trans_qty_<?=$i?>" style="width: 80px; text-align: right;" name="trans_qty[]" class="text_boxes_numeric" onblur="check_stock(<?=$i?>)" value="<?=number_format($row['transfer_qnty'], 2)?>"></td>
                <?
                if($row['to_store'] != ""){
                ?>
                    <td valign="middle" align="center"><? echo create_drop_down( "to_store_".$i, 105, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data_arr[2] and a.id <> $data_arr[3] and b.category_type=1 $store_credential_cond group by a.id, a.store_name order by a.store_name", "id,store_name", 1, "--Select Store--", $row['to_store'], "room_rack_shelf_bin_reset(4, ".$i.");load_drop_down('requires/scrap_transfer_entry_controller', this.value+'_".$data_arr[2]."_".$i."', 'load_drop_room','room_td_".$i."');"); ?></p></td>
                <?
                }else{
                ?>
                    <td valign="middle" align="center"><? echo create_drop_down( "to_store_".$i, 105, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data_arr[2] and a.id <> $data_arr[3] and b.category_type=1 $store_credential_cond group by a.id, a.store_name order by a.store_name", "id,store_name", 1, "--Select Store--", "", "room_rack_shelf_bin_reset(4, ".$i.");load_drop_down('requires/scrap_transfer_entry_controller', this.value+'_".$data_arr[2]."_".$i."', 'load_drop_room','room_td_".$i."');"); ?></p></td>
                 <?
                }
                if($room_id != ""){
                     $company_id = $data_arr[2];
                     $row = $i;
                ?>
                     <td valign="middle" align="center" id="room_td_<?=$i?>"><?=create_drop_down( "cbo_room_".$row, 95, "select b.room_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.room_id and b.store_id=$store_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.room_id,a.floor_room_rack_name order by a.floor_room_rack_name","room_id,floor_room_rack_name", 1, "--Select Room--", $room_id, "room_rack_shelf_bin_reset(3, ".$row.");load_drop_down('requires/scrap_transfer_entry_controller', this.value+'_".$company_id."_".$row."_".$store_id."', 'load_drop_rack','rack_td_".$row."');");?></td>
                <?
                }else{
                ?>
                <td valign="middle" align="center" id="room_td_<?=$i?>"><?=$row['to_room']?><?=$row['to_room']."-else-".create_drop_down( "cbo_room_".$i, 95, "$blank_array","", 1, "--Select Room--", 0)?></td>
                 <?
                 }
                if($rack != ""){
                     $company_id = $data_arr[2];
                     $row = $i;
                ?>
                <td valign="middle" align="center" id="rack_td_<?=$i?>"><?=create_drop_down( "cbo_rack_".$row, 95, "select b.rack_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.rack_id and b.room_id=$room_id and b.store_id=$store_id and a.company_id=$company_id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,a.floor_room_rack_name order by a.floor_room_rack_name","rack_id,floor_room_rack_name", 1, "--Select Rack--", $rack, "room_rack_shelf_bin_reset(2, ".$row.");load_drop_down('requires/scrap_transfer_entry_controller', this.value+'_".$company_id."_".$row."_".$store_id."', 'load_drop_shelf','shelf_td_".$row."');");?></td>
                <?
                }else{
                ?>
                <td valign="middle" align="center" id="rack_td_<?=$i?>"><?=create_drop_down( "cbo_rack_".$i, 95, "$blank_array","", 1, "--Select Rack--", 0)?></td>
                 <?
                 }
                if($shelf != ""){
                     $company_id = $data_arr[2];
                     $row = $i;
                ?>
                <td valign="middle" align="center" id="shelf_td_<?=$i?>"><?=create_drop_down( "cbo_shelf_".$row, 85, "select b.shelf_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.shelf_id and b.rack_id=$rack and b.store_id=$store_id and a.company_id=$company_id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name","shelf_id,floor_room_rack_name", 1, "--Select Shelf--", $shelf, "room_rack_shelf_bin_reset(1, ".$row.");load_drop_down('requires/scrap_transfer_entry_controller', this.value+'_".$company_id."_".$row."_".$store_id."', 'load_drop_bin','bin_td_".$row."');" );?></td>
                <?
                }else{
                ?>
                <td valign="middle" align="center" id="shelf_td_<?=$i?>"><?=create_drop_down( "cbo_shelf_".$i, 85, "$blank_array","", 1, "--Select Shelf--", 0)?></td>
                 <?
                 }
                if($bin != ""){
                     $company_id = $data_arr[2];
                     $row = $i;
                ?>
                <td valign="middle" align="center" id="bin_td_<?=$i?>"><?=create_drop_down( "cbo_bin_".$row, 85, "select b.bin_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.bin_id and b.shelf_id=$shelf and b.store_id=$store_id and a.company_id=$company_id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.bin_id,a.floor_room_rack_name order by a.floor_room_rack_name","bin_id,floor_room_rack_name", 1, "--Select Bin--", $bin, "" );?></td>
                <?
                }else{
                ?>
                <td valign="middle" align="center" id="bin_td_<?=$i?>"><?=create_drop_down( "cbo_bin_".$i, 85, "$blank_array","", 1, "--Select Bin--", 0)?></td>
                <?
                }
                ?>
            </tr>
            <?
            $totalStock += $cur_stock;
            $i++;
        }
        ?>
        <tr>
            <td colspan="5" align="right" style="padding: 0px 3px;">
                <strong>Total: </strong>
            </td>
            <td align="right" style="padding: 0px 3px;">
                <strong><?=number_format($totalStock, 2)?></strong>
            </td>
            <td align="center">
                <input type="text" id="trans_total_qty" value="<?=number_format($totalTrans, 2)?>" style="width: 80px; text-align: right; font-weight: bold;" class="text_boxes_numeric" readonly disabled>
            </td>
            <td colspan="5" align="right" style="padding: 0px 3px;">
            </td>
        </tr>
        <?
    }
	exit();
}

if ($action=="system_popup")
{
	echo load_html_head_contents("System Info Popup", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(str)
		{
            var str_arr = str.split('_');
			$("#hidden_mst_id").val(str_arr[0]);
			$("#hidden_store_id").val(str_arr[1]);
			parent.emailwindow.hide();
		}
	</script>
	</head>
        <div align="center" style="width:100%;">
            <form name="searchreceivefrm_1"  id="searchreceivefrm_1" autocomplete="off">
                <table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                    	<tr>
                            <th width="150">Search By</th>
                            <th width="170">Enter Transfer ID</th>
                            <th width="220">Transfer Date Range</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:60px;" /></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td align="center">
								<?
                                    $search_by = array(1=>'Transfer ID');
                                    echo create_drop_down("cbo_search_by", 140, $search_by,"", '0',"-- Select --",1,"");
                                ?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes_numeric" style="width:150px" placeholder="Transfer ID" />
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:90px" placeholder="From Date">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:90px" placeholder="To Date">
                            </td>
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( <?=$company_id?>+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value, 'create_transfer_search_list_view', 'search_div', 'scrap_transfer_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:60px;" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" align="center" height="40" valign="middle">
								<? echo load_month_buttons(1);  ?>
                                <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="text_boxes" style="width:70px">
                                <input type="hidden" name="hidden_mst_id" id="hidden_store_id" class="text_boxes" style="width:70px">
                            </td>
                        </tr>
                        <table>
	                        <tr>
	                            <td colspan="6" align="center" valign="top" id=""><div id="search_div"></div></td>
	                        </tr>
                        </table>
                    </tbody>
                </table>
            </form>
        </div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_transfer_search_list_view")
{
    list($company_id, $search_by, $date_from, $date_to, $search_common) = explode("_", $data);
    $sql_cond = "";
    if($db_type==0)
    {
        $year_cond= "year(insert_date)";
        if ($date_from != '' &&  $date_to != '')  $sql_cond .= " and transfer_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
    }
    else
    {
        $year_cond= "TO_CHAR(insert_date,'YYYY')";
        if ($date_from != '' &&  $date_to != '') $sql_cond .= " and transfer_date between '".change_date_format($date_from, "", "",1)."' and '".change_date_format($date_to, "", "",1)."'";
    }

    if ($search_common != '') $sql_cond .=" and transfer_prefix_number=$search_common";

    $sql= "select id, transfer_prefix_number, company_id, to_char(transfer_date, 'dd-mm-YYYY') as TRANSFER_DATE, from_store_id, to_char(insert_date, 'YYYY') as YEAR, transfer_criteria, item_category from inv_item_transfer_mst where status_active = 1 and is_deleted = 0 and entry_form = 542 and company_id=$company_id $sql_cond";
    $sql_res=sql_select($sql);

    $company_arr = return_library_array("select id, company_name from lib_company where status_active = 1 and is_deleted = 0", "id", "company_name");
    ?>
    <table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
        <thead>
        <tr>
            <th width="40">SL</th>
            <th width="100">Transfer ID</th>
            <th width="80">Year</th>
            <th width="150">Company</th>
            <th width="100">Transfer Date</th>
            <th width="100">Transfer Criteria</th>
            <th>Item Category</th>
        </tr>
        </thead>
    </table>
    <div style="width:750px; overflow-y:scroll; max-height:220px;" id="buyer_list_view" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="730" class="rpt_table" id="list_view">
            <?
            $i=1;
            foreach ($sql_res as $row)
            {
            if ($i%2==0) $bgcolor="#E9F3FF";
            else $bgcolor="#FFFFFF";
            ?>
            <tr style="text-decoration:none; cursor:pointer" onclick="js_set_value('<?= $row['ID']; ?>_<?=$row['FROM_STORE_ID']?>');" bgcolor="<?=$bgcolor?>">
                <td width="40" align="center"><?= $i; ?></td>
                <td width="100" align="center"><p><?= $row['TRANSFER_PREFIX_NUMBER']; ?></p></td>
                <td width="80" align="center"><p><?= $row['YEAR']; ?></p></td>
                <td width="150"><p><?= $company_arr[$row['COMPANY_ID']]; ?></p></td>
                <td width="100" align="center"><p><?= $row['TRANSFER_DATE']; ?></p></td>
                <td width="100"><p><?= $item_transfer_criteria[$row['TRANSFER_CRITERIA']]; ?></p></td>
                <td><p><?= $item_category[$row['ITEM_CATEGORY']]; ?></p></td>
            <tr>
                <?
                $i++;
                }
                ?>
        </table>
    </div>
    <?
    exit();
}

if ($action=="populate_data_from_mst")
{
    $data = explode('*', $data);
    $sql= "select id, transfer_system_id, company_id, to_char(transfer_date, 'dd-mm-YYYY') as TRANSFER_DATE, from_store_id, transfer_criteria, item_category, challan_no, remarks from inv_item_transfer_mst where status_active = 1 and is_deleted = 0 and entry_form = 542 and company_id=$data[1] and id = $data[0]";
    $nameArray=sql_select($sql);
	foreach ($nameArray as $row)
	{
		echo "document.getElementById('txt_system_id').value 		        = '".$row["TRANSFER_SYSTEM_ID"]."';\n";
		echo "document.getElementById('cbo_company_id').value 		        = '".$row["COMPANY_ID"]."';\n";
		echo "document.getElementById('cbo_transfer_criteria').value 		= '".$row["TRANSFER_CRITERIA"]."';\n";
		echo "document.getElementById('cbo_item_category').value	        = '".$row["ITEM_CATEGORY"]."';\n";
        echo "load_drop_down('requires/scrap_transfer_entry_controller', ".$row["COMPANY_ID"].", 'load_drop_down_store','store_td');\n";
		echo "document.getElementById('txt_remarks').value 			        = '".$row["REMARKS"]."';\n";
		echo "document.getElementById('txt_challan_no').value 			    = '".$row["CHALLAN_NO"]."';\n";
	    echo "document.getElementById('update_id').value                    = '".$row["ID"]."';\n";
        echo "document.getElementById('cbo_from_store').value		        = '".$row["FROM_STORE_ID"]."';\n";

	    echo "$('#cbo_company_id').attr('disabled',true)".";\n";
	    echo "$('#cbo_from_store').attr('disabled',true)".";\n";
	    echo "$('#cbo_item_category').attr('disabled',true)".";\n";
	}
    $sql_lot = return_library_array("select b.from_prod_id, b.yarn_lot from inv_item_transfer_mst a, inv_item_transfer_dtls b where a.id = b.mst_id and a.status_active = 1 and a.is_deleted = 0 and a.entry_form = 542 and a.company_id=$data[1] and a.id = $data[0] order by b.id", "from_prod_id", "yarn_lot");
    echo "document.getElementById('txt_lot_no').value = '".implode(',', $sql_lot)."';\n";
    echo "document.getElementById('selected_lot').value = '".implode(',', array_keys($sql_lot))."';\n";
    echo "$('#txt_lot_no').attr('disabled', true)".";\n";

    exit();
}

if($action == "load_drop_room")
{
    $data = explode("_", $data);
    $company_id = $data[1];
    $store_id = $data[0];
    $row = $data[2];
    echo create_drop_down( "cbo_room_".$row, 95, "select b.room_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.room_id and b.store_id=$store_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.room_id,a.floor_room_rack_name order by a.floor_room_rack_name","room_id,floor_room_rack_name", 1, "--Select Room--", 0, "room_rack_shelf_bin_reset(3, ".$row.");load_drop_down('requires/scrap_transfer_entry_controller', this.value+'_".$company_id."_".$row."_".$store_id."', 'load_drop_rack','rack_td_".$row."');" );
}

if($action == "load_drop_rack")
{
    $data = explode("_", $data);
    $room_id=$data[0];
    $company_id = $data[1];
    $row = $data[2];
    $store_id = $data[3];
    echo create_drop_down( "cbo_rack_".$row, 95, "select b.rack_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.rack_id and b.room_id=$room_id and b.store_id=$store_id and a.company_id=$company_id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,a.floor_room_rack_name order by a.floor_room_rack_name","rack_id,floor_room_rack_name", 1, "--Select Rack--",0, "room_rack_shelf_bin_reset(2, ".$row.");load_drop_down('requires/scrap_transfer_entry_controller', this.value+'_".$company_id."_".$row."_".$store_id."', 'load_drop_shelf','shelf_td_".$row."');");
}

if($action == "load_drop_shelf")
{
    $data = explode("_", $data);
    $rack=$data[0];
    $company_id = $data[1];
    $row = $data[2];
    $store_id = $data[3];
    echo create_drop_down( "cbo_shelf_".$row, 85, "select b.shelf_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.shelf_id and b.rack_id=$rack and b.store_id=$store_id and a.company_id=$company_id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name","shelf_id,floor_room_rack_name", 1, "--Select Shelf--", 0, "room_rack_shelf_bin_reset(1, ".$row.");load_drop_down('requires/scrap_transfer_entry_controller', this.value+'_".$company_id."_".$row."_".$store_id."', 'load_drop_bin','bin_td_".$row."');" );
}

if($action == "load_drop_bin")
{
    $data = explode("_", $data);
    $shelf=$data[0];
    $company_id = $data[1];
    $row = $data[2];
    $store_id = $data[3];
    echo create_drop_down( "cbo_bin_".$row, 85, "select b.bin_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.bin_id and b.shelf_id=$shelf and b.store_id=$store_id and a.company_id=$company_id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.bin_id,a.floor_room_rack_name order by a.floor_room_rack_name","bin_id,floor_room_rack_name", 1, "--Select Bin--", 0, "" );
}

if($action == "load_drop_room_empty")
{
    echo create_drop_down( "cbo_room_".$data, 95, "$blank_array","", 1, "--Select Room--", 0);
}

if($action == "load_drop_rack_empty")
{
   echo create_drop_down( "cbo_rack_".$data, 95, "$blank_array","", 1, "--Select Rack--", 0);
}

if($action == "load_drop_shelf_empty")
{
    echo create_drop_down( "cbo_shelf_".$data, 85, "$blank_array","", 1, "--Select Shelf--", 0);
}

if($action == "load_drop_bin_empty")
{
    echo create_drop_down( "cbo_bin_".$data, 85, "$blank_array","", 1, "--Select Bin--", 0);
}


?>
