<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------

//library array-------------------
$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
$countryArr = return_library_array("select id,country_name from lib_country where status_active=1 and is_deleted=0","id","country_name");
$location_details = sql_select("select id,company_name,plot_no,level_no,road_no,block_no,city,zip_code,country_id from lib_company where status_active=1 and is_deleted=0");
$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
//$supplier_address = sql_select("SELECT id, address_1, address_2, address_3, address_4 from lib_supplier where status_active=1 and is_deleted=0");
$item_group_arr=return_library_array( "select id, item_name from  lib_item_group",'id','item_name');
//$item_description_arr=return_library_array( "select id, item_description from  product_details_master where status_active=1 and is_deleted=0",'id','item_description');

if ($action=="load_drop_down_supplier")
{
    // (1,6,7,8,90,92)
	echo create_drop_down( "cbo_supplier_name", 130, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b,lib_supplier_tag_company c where  a.id=b.supplier_id and a.id=c.supplier_id and  c.tag_company=$data and b.party_type in (1,2,3,4,5,9,6,7,8,39,90,91,92,93,94,96) and a.status_active=1 and a.is_deleted=0 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select Supplier--", "", "" );

}
//Group search------------------------------//
if($action=="group_popup")
{
    extract($_REQUEST);
    echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
    ?>
    <script>

        var selected_id = new Array;
        var selected_name = new Array;
        var selected_no = new Array;
        function check_all_data() {
            var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
            tbl_row_count = tbl_row_count - 0;
            for( var i = 1; i <= tbl_row_count; i++ ) {
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
            var str_or = splitSTR[0];
            var selectID = splitSTR[1];
            var selectDESC = splitSTR[2];
            //$('#txt_individual_id' + str).val(splitSTR[1]);
            //$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
            if($('#tr_' + str_or).css("display") !='none')
            {
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
    </script>
    <?
    if($cbo_item_category != '') $cat_cond=" and c.item_category in($cbo_item_category)"; else $cat_cond="";
    $sql = "select c.item_name,c.id from  lib_item_group c where   c.status_active=1 and c.is_deleted=0 $cat_cond group by c.id, c.item_name order by c.item_name";
    //echo $sql; die;
    echo create_list_view("list_view", "Item Name","150","350","310",0, $sql , "js_set_value", "id,item_name", "", 1, "0", $arr, "item_name", "","setFilterGrid('list_view',-1)","0","",1) ;
    echo "<input type='hidden' id='txt_selected_id' />";
    echo "<input type='hidden' id='txt_selected' />";
    echo "<input type='hidden' id='txt_selected_no' />";
    exit();
}


//Item Description search------------------------------//
if($action=="item_description_popup")
{
    extract($_REQUEST);
    echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
    ?>
    <script>

        var selected_id = new Array;
        var selected_name = new Array;
        var selected_no = new Array;
        function check_all_data() {
            var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
            tbl_row_count = tbl_row_count - 0;
            for( var i = 1; i <= tbl_row_count; i++ ) {
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
            var str_or = splitSTR[0];
            var selectID = splitSTR[1];
            var selectDESC = splitSTR[2];
            //$('#txt_individual_id' + str).val(splitSTR[1]);
            //$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
            if($('#tr_' + str_or).css("display") !='none')
            {
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
    </script>
    <?
    if($cbo_item_category!= '') $cat_cond .=" and item_category_id in($cbo_item_category)"; else $cat_cond .= "";
    if($cbo_item_group != '') $cat_cond .=" and item_group_id in($cbo_item_group)"; else $cat_cond .= "";
    if($company != 0) $cat_cond .=" and company_id  = $company"; else $cat_cond .= "";
    $sql = "SELECT trim(item_description) as item_description, id, item_code from  product_details_master where item_description is not null and status_active = 1 and is_deleted = 0 $cat_cond order by trim(item_description)";
    //    echo $sql; die;
    echo create_list_view("list_view", "Item Description,Item Code","250,100","450","310",0, $sql , "js_set_value", "id,item_description", "", 1, "0", $arr, "item_description,item_code", "","setFilterGrid('list_view',-1)","0","",1) ;
    echo "<input type='hidden' id='txt_selected_id' />";
    echo "<input type='hidden' id='txt_selected' />";
    echo "<input type='hidden' id='txt_selected_no' />";
    exit();
}

//report generated here--------------------//

if($action=="generate_report")
{

	extract($_REQUEST);
	$location = array();

	foreach ($location_details as  $value)
	{
		$location[$value[csf("id")]]["id"] = $value[csf("id")];
		$location[$value[csf("id")]]["company_name"] = $value[csf("company_name")];
		$location[$value[csf("id")]]["plot_no"] = $value[csf("plot_no")];
		$location[$value[csf("id")]]["block_no"] = $value[csf("block_no")];
		$location[$value[csf("id")]]["level_no"] = $value[csf("level_no")];
		$location[$value[csf("id")]]["road_no"] = $value[csf("road_no")];
		$location[$value[csf("id")]]["city"] = $value[csf("city")];
		$location[$value[csf("id")]]["zip_code"] = $value[csf("zip_code")];
		$location[$value[csf("id")]]["country_id"] = $value[csf("country_id")];
	}


	$search_cond="";

    if($cbo_supplier_name){
		$search_cond .= " and a.supplier_id  = $cbo_supplier_name";
	}

    if($cbo_item_category != ""){
        $search_cond .= " and c.item_category_id in ($cbo_item_category)";
    }

    if($cbo_item_group != ""){
        $search_cond.=" and c.item_group_id  in ($cbo_item_group)";
    }

    if($cbo_item_description != ""){
        $search_cond.=" and c.id in ($cbo_item_description)";
    }

	if($txt_mrr_number != ""){
		$search_cond.=" and a.recv_number Like '%$txt_mrr_number%'";
	}

	if($db_type==2)
	{
		if( $txt_date_from != "" && $txt_date_to != "" ){
            $search_cond .=" and a.receive_date between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
        }else{
            $search_cond .=" and extract(year from a.insert_date) = $cbo_year";
        }
	}
	if($db_type==0)
	{
		if( $txt_date_from != "" && $txt_date_to != "" ){
            $search_cond.= "  and a.receive_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
        }else{
            $search_cond .=" and extract(year from a.insert_date) = $cbo_year";
        }

	}
    if($txt_mrr_number == '' && $txt_wo_number != ''){
        $search_cond .= " and a.BOOKING_NO LIKE '%$txt_wo_number' and a.receive_basis = 2";
    }
    if($txt_wo_number == '' && $txt_pi_number != ''){
        $search_cond .= " and a.BOOKING_NO LIKE '%$txt_pi_number' and a.receive_basis = 1";
    }
    if($txt_rcv_challan_number != ''){
        $search_cond .= " and a.challan_no LIKE '%$txt_rcv_challan_number%'";
    }

	$sql="SELECT a.id as receive_id, a.company_id, a.recv_number, a.receive_date, a.challan_no, a.challan_date, a.addi_challan_date, a.booking_id, a.booking_no, a.supplier_id, a.receive_basis, a.bill_no, a.bill_date, a.entry_form, b.item_category, b.order_qnty,b.order_rate,b.order_amount, c.id as prod_id, c.product_name_details, c.unit_of_measure, c.item_group_id, c.item_category_id, c.item_description, c.item_size, c.item_code,b.id as tr_id, c.lot
	from inv_receive_master a, inv_transaction b, product_details_master c
	where a.id = b.mst_id and b.prod_id = c.id and a.company_id = $cbo_company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted = 0 and c.status_active=1 $search_cond and b.transaction_type=1 order by a.recv_number, c.item_category_id, c.item_group_id ASC";
    // echo $sql;
    $pi_id = array();
    $booking_id=$wo_id_arr = array();
    $result = sql_select($sql);
    foreach ($result as $piBookingID){
        if($piBookingID[csf('receive_basis')] == 1){
            array_push($pi_id, $piBookingID[csf('booking_id')]);
            $wo_id_arr[$piBookingID["BOOKING_ID"]]=$piBookingID["BOOKING_ID"];
        }elseif($piBookingID[csf('receive_basis')] == 2){
            array_push($booking_id, $piBookingID[csf('booking_id')]);
            $wo_id_arr[$piBookingID["BOOKING_ID"]]=$piBookingID["BOOKING_ID"];
        }
    }
    $all_pi_id = array_chunk(array_unique($pi_id),999);
    $pi_id_cond="";
    foreach($all_pi_id as $val){
        $ids = implode(",",$val);
        if($pi_id_cond == ""){
            $pi_id_cond.=" and (a.pi_id in ($ids) ";
        }else{
            $pi_id_cond.=" or  a.pi_id in  ( $ids) ";
        }
    }
    $pi_id_cond.=")";

    $all_workorder_id = array_chunk(array_unique($booking_id),999);
    $booking_id_cond="";
    foreach($all_workorder_id as $val){
        $ids = implode(",",$val);
        if($booking_id_cond == ""){
            $booking_id_cond.=" and (a.work_order_id in ($ids) ";
        }else{
            $booking_id_cond.=" or  a.work_order_id in  ( $ids) ";
        }
    }
    $booking_id_cond.=")";

    $pi_array = return_library_array("select a.pi_id, a.work_order_no from com_pi_item_details a, com_pi_master_details b  where a.pi_id = b.id and b.pi_basis_id = 1 $pi_id_cond group by a.pi_id, a.work_order_no", "pi_id", "work_order_no");
    // $booking_array = return_library_array("select a.work_order_id, b.pi_number from com_pi_item_details a, com_pi_master_details b where a.pi_id = b.id and b.pi_basis_id = 1 $booking_id_cond group by a.work_order_id, b.pi_number", "work_order_id", "pi_number");

    $con = connect();
	$rid=execute_query("delete from GBL_TEMP_ENGINE where entry_form=143 and user_id=$user_id");
	if($rid) oci_commit($con);
	 $wo_pi_arr=array();
	if(!empty($wo_id_arr))
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 143, 1, $wo_id_arr,$empty_arr);
		$sql_result =sql_select("SELECT a.PI_NUMBER, a.SUPPLIER_ID, b.ITEM_GROUP, b.WORK_ORDER_ID, e.LC_NUMBER 
		from  COM_PI_ITEM_DETAILS b, gbl_temp_engine c, 
		COM_PI_MASTER_DETAILS a left join com_btb_lc_pi d on a.id=d.pi_id
		left join com_btb_lc_master_details e on d.com_btb_lc_master_details_id=e.id 
		where a.id=b.PI_ID and b.WORK_ORDER_ID=c.ref_val and c.entry_form=143 and c.ref_from=1 and c.user_id= $user_id and a.IMPORTER_ID=$cbo_company_name and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0");
		foreach($sql_result as $row){
			$wo_pi_arr[$row["WORK_ORDER_ID"]]["LC_NUMBER"]=$row["LC_NUMBER"];
			$wo_pi_arr[$row["WORK_ORDER_ID"]]["PI_NUMBER"]=$row["PI_NUMBER"];
		}
	}

    ob_start();
		?>
        <style>
            .wrd_brk{word-break: break-all;word-wrap: break-word;}
        </style>
		<div style="width: 1780px; margin: 15px 0;" id="scroll_body">
        	<table width="98%" border="0" align = "left">
            	<tr class="form_caption" style="border:none;">
                    <td colspan="14" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?> </td>
                </tr>
                <tr style="border:none;">
                    <td colspan="14" align="center" style="border:none; font-size:14px;">
                    <h1>Company Name : <? echo $companyArr[str_replace("'","",$cbo_company_name)]; ?></h1>
                    </td>
                </tr>
                <tr style="border:none;">
                    <td colspan="14" align="center" style="border:none;font-size:12px; font-weight:bold">
                        <h1>Date : <? echo $txt_date_from.' to '.$txt_date_to; ?></h1>
                    </td>
                </tr>

            </table>

            	<table width="100%" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1"  align = "left">
                    <thead>
                        <tr>
                            <th colspan="18"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
                        </tr>
                        <tr>
                            <th width="30">Sl No</th>
                            <th width="130">Supplier Name</th>
                            <th width="110">Item Category</th>
                            <th width="110">Item Group</th>
                            <th width="130">Item Description</th>
                            <th width="80">Lot No</th>
                            <th width="90">Item Code</th>
                            <th width="110">Receive No</th>
                            <th width="90">Receive Date</th>
                            <th width="100">Receive Qty</th>
                            <th width="100">Receive Rate</th>
                            <th width="100">Receive Value</th>
                            <th width="70">UOM</th>
                            <th width="100">Work Order No</th>
                            <th width="90">PI No</th>
                            <th width="90">LC No</th>
                            <th width="100">Receive Challan No</th>
                            <th>Challan Date</th>
                        </tr>
                    </thead>
                </table>

                <div style="width: 1820px; overflow-y: scroll; max-height: 350px;" id="democlass">
                <table width="1780" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" style="float: left;" id="mrr_details_tbl_body">
                <!-- <table> -->
                <tbody>
                <?
                if(count($result) > 0)
                {
                    $i = 1;
                    foreach ($result  as $rcv_id => $receiv_arr)
                    {
                      ?>
                        <tr onclick="change_color('tr_<? echo $i;?>','')" id = "tr_<? echo $i;?>">
                            <td width="30" align="center" class="wrd_brk"><?=$i?></td>
                            <td width="130" align="left" class="wrd_brk"><? echo $supplierArr[$receiv_arr[csf('supplier_id')]]; ?></td>
                            <td width="110" align="left" class="wrd_brk"> <? echo $item_category[$receiv_arr[csf('item_category_id')]];?></td>
                            <td width="110" align="left" class="wrd_brk"><? echo $item_group_arr[$receiv_arr[csf('item_group_id')]];?> </td>
                            <?
                            if($receiv_arr[csf('item_description')] == "")
                            {
                                ?>
                                <td width="130" align="left" class="wrd_brk"> <? echo $receiv_arr[csf('product_name_details')];?></td>
                                <?
                            }else
                            {
                                ?>
                                <td width="130" align="left" class="wrd_brk"> <? echo $receiv_arr[csf('item_description')];?></td>
                                <?
                            }
                            ?>
                            <td width="80" align="center" class="wrd_brk"> <? echo $receiv_arr[csf('lot')];?></td>
                            <td width="90" align="center" class="wrd_brk"> <? echo $receiv_arr[csf('item_code')];?></td>
                            <td width="110" align="center" class="wrd_brk"><? echo  $receiv_arr[csf('recv_number')];?> </td>
                            <td width="90" align="center" class="wrd_brk"><? echo change_date_format($receiv_arr[csf('receive_date')]);?> </td>
                            <td width="100" align="right" class="wrd_brk"> <? echo number_format($receiv_arr[csf('order_qnty')],2);?></td>
                            <td width="100" align="right" class="wrd_brk"> <? echo number_format($receiv_arr[csf('order_rate')],2);?></td>
                            <td width="100" align="right" class="wrd_brk"> <? echo number_format($receiv_arr[csf('order_amount')],2);?></td>
                            <td width="70" align="center" class="wrd_brk"> <? echo $unit_of_measurement[$receiv_arr[csf('unit_of_measure')]];?></td>
                            <?
                            if($receiv_arr[csf('receive_basis')] == 1)
                            {
                                ?>
                                <td width="100" align="center" class="wrd_brk"> <?=$pi_array[$receiv_arr[csf('booking_id')]] ?></td>
                                <td width="90" align="center" class="wrd_brk"> <?=$receiv_arr[csf('booking_no')]?></td>
                                <td width="90" align="center" class="wrd_brk"> <?=$wo_pi_arr[$receiv_arr["BOOKING_ID"]]["LC_NUMBER"]?></td>
                                <?
                            }elseif ($receiv_arr[csf('receive_basis')] == 2 || $receiv_arr[csf('receive_basis')] == 14)
                            {
                                ?>
                                <td width="100" align="center" class="wrd_brk"> <?=$receiv_arr[csf('booking_no')] ?></td>
                                <td width="90" align="center" class="wrd_brk"> <?=$wo_pi_arr[$receiv_arr["BOOKING_ID"]]["PI_NUMBER"]; //$booking_array[$receiv_arr[csf('booking_id')]]?></td>
                                <td width="90" align="center" class="wrd_brk"> <?=$wo_pi_arr[$receiv_arr["BOOKING_ID"]]["LC_NUMBER"]?></td>
                                <?
                            }else{
                            ?>
                                <td width="100" align="center" > </td>
                                <td width="90" align="center"></td>
                                <td width="90" align="center"></td>
                            <?
                            }
                            ?>
                            <td width="100" align="center" class="wrd_brk"> <? echo $receiv_arr[csf('challan_no')];?></td>
                            <td align="center" class="wrd_brk"> <? echo $receiv_arr[csf('challan_date')];?></td>
                        </tr>
                        <?
                        $i++;
                    }
                }else{
                ?>
                    <tr>
                       <td align="center" colspan="14">No record found!</td>
                    </tr>
                <?
                }
                $r_id6=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form=143");
                oci_commit($con);
                disconnect($con);
                    ?>
                </tbody>
            </table>
			</div>
		</div>

    <?
	$html = ob_get_contents();
	ob_clean();
	//$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename";
	exit();

}

?>
