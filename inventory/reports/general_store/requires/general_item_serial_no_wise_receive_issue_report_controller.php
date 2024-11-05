<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if ($_SESSION['logic_erp']['user_id'] == "") {
    header("location:login.php");
    die;
}
$permission = $_SESSION['page_permission'];

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
//--------------------------------------------------------------------------------------------
if ($action == "load_drop_down_location") {
    echo create_drop_down("cbo_location_id", 120, "select id,location_name from lib_location where company_id in($data) and status_active =1 and is_deleted=0 order by location_name", "id,location_name", 1, "-- All --", 0, "set_multiselect('cbo_location_id','0','0','','0')");
    exit();
}
if ($action == "load_drop_down_supplier") {
    echo create_drop_down("cbo_supplier_name", 130, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b,lib_supplier_tag_company c where  a.id=b.supplier_id and a.id=c.supplier_id and  c.tag_company in($data) and b.party_type in (1,2,3,4,5,9,6,7,8,39,90,91,92,93,94,96) and a.status_active=1 and a.is_deleted=0 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select Supplier--", "", "");
    // echo "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b,lib_supplier_tag_company c where  a.id=b.supplier_id and a.id=c.supplier_id and  c.tag_company in($data) and b.party_type in (1,2,3,4,5,9,6,7,8,39,90,91,92,93,94,96) and a.status_active=1 and a.is_deleted=0 group by a.id,a.supplier_name order by a.supplier_name";
}

if ($action == "load_drop_down_store") {
    echo create_drop_down("cbo_store_id", 120, "select id,store_name from lib_store_location where company_id in($data) and status_active =1 and is_deleted=0 order by store_name", "id,store_name", 1, "-- All --", 0, "");
    exit();
}

if($action == "challan_popup")
{
    echo load_html_head_contents("Challan Info","../../../../", 1, 1, '','','');
    extract($_REQUEST);

    ?>
    <script>
        function js_set_value(id)
        {
            document.getElementById('selected_id').value=id;
            parent.emailwindow.hide();
        }
    </script>
    <input type="hidden" name="selected_id" id="selected_id">
    </head>
    <fieldset style="width:620px;">
        <legend>Challan Details</legend>       
        <table width="620" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
            <thead>
            <tr>
                <th width="40">SL</th>
                <th width="100">Challan No.</th>
                <th width="120">MRR No.</th>
                <th width="70">MRR Date</th>
                <th width="120">Req./WO. Number</th>
                <th >Supplier Name</th>
            </tr>
            </thead>
        </table>
        <div style="width:620px; overflow-y:scroll; max-height:325px" id="scroll_body">
            <table width="600" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body" align="left">
                <?
                $i = 1;
                $cbo_company_name=str_replace("'","",$cbo_company_name);
                $cbo_supplier_name = str_replace("'", "", $cbo_supplier_name);
                if (!empty($cbo_supplier_name)) $search_conds .=" and a.supplier_id='$cbo_supplier_name'";
    
                $supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");

                $sql_mrr = "SELECT a.ID, a.COMPANY_ID, a.CHALLAN_NO, a.BOOKING_NO, a.supplier_id, a.RECEIVE_DATE, a.RECV_NUMBER
                from inv_receive_master a, inv_transaction b where a.id = b.mst_id and a.CHALLAN_NO is not null and  a.company_id in($cbo_company_name) $search_conds and b.transaction_type=1 and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.ENTRY_FORM=20
                group by  a.ID, a.COMPANY_ID, a.CHALLAN_NO, a.BOOKING_NO, a.supplier_id, a.RECEIVE_DATE, a.RECV_NUMBER order by a.ID desc";
                // echo $sql_mrr;
                $result=sql_select($sql_mrr);

                $selected_id_arr=explode(",",$selected_mrr_id);
                foreach ($result as $row)
                {
                    if ($i % 2 == 0)
                        $bgcolor = "#E9F3FF";
                    else
                        $bgcolor = "#FFFFFF";

                   
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value('<? echo $row[csf('id')].'_'.$row[csf('recv_number')]; ?>')" style="text-decoration:none; cursor:pointer">
                        <td width="40" align="center"><? echo $i;?></td>
                        <td width="100" align="center"><p><? echo $row[csf("challan_no")]; ?></p></td>
                        <td width="120" align="center"><p><? echo $row[csf("recv_number")]; ?></p></td>
                        <td width="70" align="center"><p><? echo change_date_format($row[csf("receive_date")]); ?></p></td>
                        <td width="120" align="center"><p><? echo $row[csf("booking_no")]; ?></p></td>
                        <td align="center"><p><? echo $supplier_arr[$row[csf("supplier_id")]]; ?></p></td>
                    </tr>
                    <?
                    $i++;
                }
                ?>
            </table>
        </div>
        <table width="390" cellspacing="0" cellpadding="0" style="border:none" align="center">
            <tr>
                <td align="center" height="30" valign="bottom">
                   
                    
                </td>
            </tr>
        </table>
    </fieldset>
    <script type="text/javascript">
        setFilterGrid('table_body',-1);
        set_all();
    </script>
    <?
    exit;
}

if ($action == "group_popup") {
    extract($_REQUEST);
    echo load_html_head_contents("Popup Info", "../../../../", 1, 1, $unicode);
?>
    <script>
        var selected_id = new Array;
        var selected_name = new Array;
        var selected_no = new Array;

        function check_all_data() {
            var tbl_row_count = document.getElementById('list_view').rows.length;
            tbl_row_count = tbl_row_count - 0;
            for (var i = 1; i <= tbl_row_count; i++) {
                var onclickString = $('#tr_' + i).attr('onclick');
                var paramArr = onclickString.split("'");
                var functionParam = paramArr[1];
                js_set_value(functionParam);

            }
        }

        function toggle(x, origColor) {
            var newColor = 'yellow';
            if (x.style) {
                x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
            }
        }

        function js_set_value(strCon) {
            //alert(strCon);
            var splitSTR = strCon.split("_");
            var str_or = splitSTR[0];
            var selectID = splitSTR[1];
            var selectDESC = splitSTR[2];
            //$('#txt_individual_id' + str).val(splitSTR[1]);
            //$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
            if ($('#tr_' + str_or).css("display") != 'none') {
                toggle(document.getElementById('tr_' + str_or), '#FFFFCC');

                if (jQuery.inArray(selectID, selected_id) == -1) {
                    selected_id.push(selectID);
                    selected_name.push(selectDESC);
                    selected_no.push(str_or);
                } else {
                    for (var i = 0; i < selected_id.length; i++) {
                        if (selected_id[i] == selectID) break;
                    }
                    selected_id.splice(i, 1);
                    selected_name.splice(i, 1);
                    selected_no.splice(i, 1);
                }
            }
            var id = '';
            var name = '';
            var job = '';
            var num = '';
            for (var i = 0; i < selected_id.length; i++) {
                id += selected_id[i] + ',';
                name += selected_name[i] + ',';
                num += selected_no[i] + ',';
            }
            id = id.substr(0, id.length - 1);
            name = name.substr(0, name.length - 1);
            num = num.substr(0, num.length - 1);
          
            $('#txt_selected_id').val(id);
            $('#txt_selected').val(name);
            $('#txt_selected_no').val(num);
        }
    </script>
    <?

    $sql = "select c.item_name,c.id from  lib_item_group c where   c.status_active=1 and c.is_deleted=0 group by c.id, c.item_name order by c.item_name";
    //echo $sql; die;
    echo create_list_view("list_view", "Item Name", "150", "350", "310", 0, $sql, "js_set_value", "id,item_name", "", 1, "0", $arr, "item_name", "", "setFilterGrid('list_view',-1)", "0", "", 1);
    echo "<input type='hidden' id='txt_selected_id' />";
    echo "<input type='hidden' id='txt_selected' />";
    echo "<input type='hidden' id='txt_selected_no' />";
    exit();
}
//report generated here--------------------//

if ($action == "generate_report") {

    extract($_REQUEST);
    //echo $_REQUEST;
    $cbo_company_name = str_replace("'", "", $cbo_company_name);
    // $cbo_location_id = str_replace("'", "", $cbo_location_id);
    $cbo_year = str_replace("'", "", $cbo_year);
    $cbo_supplier_name = str_replace("'", "", $cbo_supplier_name);
    $cbo_item_category_id = str_replace("'", "", $cbo_item_category_id);
    $cbo_item_group_id = str_replace("'", "", $cbo_item_group_id);
    $txt_item_account_id = str_replace("'", "", $txt_item_account_id);
    $txt_req_no = str_replace("'", "", $txt_req_no);
    $txt_mrr_number = str_replace("'", "", $txt_mrr_number);
    $cbo_store_id = str_replace("'", "", $cbo_store_id);
    $txt_serial_no = str_replace("'", "", $txt_serial_no);
    $report_type = str_replace("'", "", $report_type);
    $txt_date_from=str_replace("'","",$txt_date_from);
    $txt_date_to=str_replace("'","",$txt_date_to);
    $txt_mrr_id	= str_replace("'","",$txt_mrr_id);
    $companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
    $supllier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
    $itemgroupArr = return_library_array("select id,item_name from lib_item_group where status_active=1 and is_deleted=0", "id", "item_name");
    $store_name_arr = return_library_array("select id, store_name from  lib_store_location", "id", "store_name");
    //$category_arr="select id,actual_category_name, category_id, short_name from lib_item_category_list order by category_id";
    $category_arr = return_library_array("select CATEGORY_ID,actual_category_name from lib_item_category_list where status_active=1 and is_deleted=0", "CATEGORY_ID", "actual_category_name");
    $location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
    if ($report_type == 1)
  {

        $company_cond = "";
        $serial_no_con = "";
        $search_conds = "";
        $mrr_no_con = "";
        if ($txt_serial_no) $serial_no_con = " and d.serial_no='$txt_serial_no'";
        // if ($txt_mrr_number!='') 
        // {
        //     $mrr_no_con .= " and  a.recv_number_prefix_num='$txt_mrr_number'";
        //     $search_conds .= " and a.recv_number in ($txt_mrr_number) ";
        // }
        if($txt_mrr_id != '')
        {
                $search_conds .= " and a.id in ($txt_mrr_id)";
        }
        else
        {
              if($txt_mrr_number)
              {
                $search_conds .= " and a.recv_number_prefix_num ='$txt_mrr_number'";   
              } 
        }
    //   echo $cbo_location_id;die;
       if ( $cbo_location_id >0) $loacation_conds= " and e.LOCATION_ID in ($cbo_location_id) ";
        if ( $cbo_store_id != '') $search_conds .= " and a.store_id in ($cbo_store_id) ";
        if (!empty($cbo_supplier_name)) $search_conds .=" and a.supplier_id='$cbo_supplier_name'";
        if ($txt_req_no != '') $search_conds= " and a.booking_no like '%$txt_req_no'";
        if ( $txt_item_account_id != '') $search_conds .= " and b.prod_id in ($txt_item_account_id) ";
       
        
        if ($cbo_company_name != '') $company_cond .= " and a.company_id in ($cbo_company_name) ";
        if (!empty($cbo_item_category_id)) $search_conds .= " and c.item_category_id='$cbo_item_category_id' ";
        if ($cbo_item_group_id != '') $search_conds .= " and c.item_group_id in ($cbo_item_group_id) ";
        if ($txt_date_from!="" && $txt_date_to!="")
        {
            if($db_type==0)
            {
            if($txt_date_from!="" && $txt_date_to!="") $search_conds.=" and a.receive_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."' ";
            }
            else
            {
            if($txt_date_from!="" && $txt_date_to!="") $search_conds.="and a.receive_date between '".change_date_format($txt_date_from,'','',-1)."' and '".change_date_format($txt_date_to,'','',-1)."' "; 
            }
        }

        // if($db_type==0) {
		// 	if($cbo_year!=0) $cbo_year_cond=" and year(a.receive_date)=$cbo_year"; else $cbo_year_cond="";
		// 	$select_date=" year(a.receive_date)";
		// }
		// else if($db_type==2) {
		// 	if($cbo_year!=0) $cbo_year_cond=" and to_char(a.receive_date,'YYYY')=$cbo_year"; else $cbo_year_cond="";
		// 	$select_date=" to_char(a.receive_date,'YYYY')";
		// }

        if($cbo_year!=0)
	{
		if($db_type==0)
		{
			$cbo_year_cond=" and year(a.receive_date)='$cbo_year'";
		}
		else
		{
			$cbo_year_cond=" and to_char(a.receive_date,'YYYY')='$cbo_year'";
		}
	}
	else {$cbo_year_cond="";}
    //   $sql = "SELECT a.company_id,a.recv_number,a.receive_date,a.supplier_id,a.challan_no,a.store_id, a.receive_basis, a.booking_no, b.order_uom, b.expire_date,b.order_qnty,c.item_category_id, c.item_group_id,c.item_description     AS item_description, d.serial_no,$select_date as year
    //     FROM inv_receive_master  a, inv_transaction  b,product_details_master  c, inv_serial_no_details d, INV_PURCHASE_REQUISITION_MST e WHERE a.id = b.mst_id AND b.prod_id = c.id and e.id=a.BOOKING_ID AND c.id = d.prod_id and b.id=d.RECV_TRANS_ID and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $loacation_conds $company_cond $serial_no_con  $search_conds $cbo_year_cond
    //     union all
    //     SELECT a.company_id,a.recv_number,a.receive_date,a.supplier_id,a.challan_no,a.store_id, a.receive_basis, a.booking_no, b.order_uom, b.expire_date,b.order_qnty,c.item_category_id, c.item_group_id,c.item_description     AS item_description, d.serial_no,$select_date as year
    //     FROM inv_receive_master  a, inv_transaction  b,product_details_master  c, inv_serial_no_details d, wo_non_order_info_mst e WHERE a.id = b.mst_id AND b.prod_id = c.id and e.id=a.BOOKING_ID AND c.id = d.prod_id and b.id=d.RECV_TRANS_ID and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $loacation_conds $company_cond $serial_no_con  $search_conds $cbo_year_cond
    //     ";
      $sql="SELECT a.company_id, a.recv_number, a.receive_date, a.supplier_id, a.challan_no, a.store_id, a.receive_basis, a.booking_no, b.order_uom, b.expire_date, b.order_qnty, c.item_category_id, c.item_group_id, c.item_description AS item_description, d.SERIAL_NO   FROM inv_receive_master a 
        INNER JOIN inv_transaction b ON a.id = b.mst_id 
        INNER JOIN product_details_master c ON b.prod_id = c.id
        -- LEFT JOIN inv_serial_no_details d ON c.id = d.prod_id 
        LEFT JOIN inv_serial_no_details d ON b.id = d.RECV_TRANS_ID
        LEFT JOIN  inv_purchase_requisition_mst e  on e.id=a.BOOKING_ID
        WHERE  a.status_active = 1  AND b.status_active = 1 AND c.status_active IN (1, 3)  AND a.entry_form = 20  $loacation_conds $company_cond $serial_no_con  $search_conds $cbo_year_cond  and b.TRANSACTION_TYPE IN(1)
        UNION all
        SELECT a.company_id, a.recv_number, a.receive_date, a.supplier_id, a.challan_no, a.store_id, a.receive_basis, a.booking_no, b.order_uom, b.expire_date, b.order_qnty, c.item_category_id, c.item_group_id, c.item_description AS item_description, d.SERIAL_NO FROM inv_receive_master a 
        INNER JOIN inv_transaction b ON a.id = b.mst_id 
        INNER JOIN product_details_master c ON b.prod_id = c.id
        -- LEFT JOIN inv_serial_no_details d ON c.id = d.prod_id 
        LEFT JOIN inv_serial_no_details d ON b.id = d.RECV_TRANS_ID
        LEFT JOIN wo_non_order_info_mst e  on e.id=a.BOOKING_ID
        WHERE  a.status_active = 1  AND b.status_active = 1 AND c.status_active IN (1, 3)  AND a.entry_form = 20  $loacation_conds $company_cond $serial_no_con  $search_conds $cbo_year_cond  and b.TRANSACTION_TYPE IN(1)
        
        ";
        $result = sql_select($sql);
         //echo $sql;
        // echo "<pre>";
        //     print_r($result);
        $dataarray = array();
        $order_arr=array();
        foreach ($result as $row) {
            //$dataarray[$row['company_id']]['company_id'] = $row['company_id'];
            
            $index = $row[csf("expire_date")] ."**".$row[csf("serial_no")]."**".$row[csf("item_description")];
            $dataarray[$row[csf("company_id")]][$row[csf("recv_number")]][$row[csf("item_description")]][$index]["company_id"] = $row[csf("company_id")];
            $dataarray[$row[csf("company_id")]][$row[csf("recv_number")]][$row[csf("item_description")]][$index]["recv_number"] = $row[csf("recv_number")];
            $dataarray[$row[csf("company_id")]][$row[csf("recv_number")]][$row[csf("item_description")]][$index]["receive_date"] = $row[csf("receive_date")];
            $dataarray[$row[csf("company_id")]][$row[csf("recv_number")]][$row[csf("item_description")]][$index]["supplier_id"] = $row[csf("supplier_id")];
            $dataarray[$row[csf("company_id")]][$row[csf("recv_number")]][$row[csf("item_description")]][$index]["challan_no"] = $row[csf("challan_no")];
            $dataarray[$row[csf("company_id")]][$row[csf("recv_number")]][$row[csf("item_description")]][$index]["store_id"] = $row[csf("store_id")];
            $dataarray[$row[csf("company_id")]][$row[csf("recv_number")]][$row[csf("item_description")]][$index]["receive_basis"] = $row[csf("receive_basis")];
            $dataarray[$row[csf("company_id")]][$row[csf("recv_number")]][$row[csf("item_description")]][$index]["booking_no"] = $row[csf("booking_no")];
            $dataarray[$row[csf("company_id")]][$row[csf("recv_number")]][$row[csf("item_description")]][$index]["item_category_id"] = $row[csf("item_category_id")];
            $dataarray[$row[csf("company_id")]][$row[csf("recv_number")]][$row[csf("item_description")]][$index]["item_group_id"] = $row[csf("item_group_id")];
            $dataarray[$row[csf("company_id")]][$row[csf("recv_number")]][$row[csf("item_description")]][$index]["item_description"] = $row[csf("item_description")];
            $dataarray[$row[csf("company_id")]][$row[csf("recv_number")]][$row[csf("item_description")]][$index]["order_uom"] = $row[csf("order_uom")];
            $dataarray[$row[csf("company_id")]][$row[csf("recv_number")]][$row[csf("item_description")]][$index]["expire_date"]= $row[csf("expire_date")] ;
            // $dataarray[$row[csf("company_id")]][$row[csf("recv_number")]][$row[csf("item_description")]][$index]["order_qnty"]= $row[csf("order_qnty")] ;
            $dataarray[$row[csf("company_id")]][$row[csf("recv_number")]][$row[csf("item_description")]][$index]["serial_no"]= $row[csf("serial_no")] ;
          
        }

        $order_sql="SELECT a.company_id, a.recv_number, b.order_qnty,c.item_description AS item_description FROM inv_receive_master  a, inv_transaction b,product_details_master c  WHERE   a.status_active = 1 AND b.status_active = 1 AND a.id = b.mst_id AND b.prod_id = c.id AND a.entry_form = 20  AND a.company_id IN ($cbo_company_name) $search_conds $company_cond $cbo_year_cond ";
        
        $main_order_sql=sql_select($order_sql);
        $order_arr=array();

        foreach($main_order_sql as $row)
        {
            $order_arr[$row[csf("company_id")]][$row[csf("recv_number")]][$row[csf("item_description")]]["order_qnty"] += $row[csf("order_qnty")];
        }
        //  echo "<pre>";
        // print_r($order_arr);
        ob_start();
    ?>
        <div style="width:1870px; margin-left:10px;" align="left">
            <table width="1850" cellpadding="0" cellspacing="0" style="visibility:hidden; border:none" id="caption">
                <tr>
                    <td align="center" width="100%" colspan="19" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
                </tr>
            </table>
            <table width="1850" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="100">Company Name</th>
                        <th width="80">MRR No</th>
                        <th width="70">MRR Date</th>
                        <th width="100">Supplier Name</th>
                        <th width="80">Challan No</th>
                        <th width="70">Store Name</th>
                        <th width="100">Recieve Basis</th>
                        <th width="90">Req No</th>
                        <th width="80">WO No</th>
                        <th width="100">Item Category</th>
                        <th width="100">Item Group</th>
                        <th width="100">Item Description</th>
                        <th width="50">UOM</th>
                        <th width="50">MRR Qnty</th>
                        <th width="100">Warrenty Exp. Date</th>
                        <th  width="70">Serial No</th>
                    </tr>
                </thead>
            </table>
            <div style="width:1870px; overflow-y:scroll; max-height:350px; overflow-x:hidden;" id="scroll_body" align="left">
                <table width="1850" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_bodyy">
                    <tbody>
                        <?
                        $i = 1;
                        // echo "<pre>";
                        // print_r($dataarray);die;
                        $company_level_span = array();
                        $recv_level_span = array();
                        $desc_level_span = array();
                        foreach ($dataarray as $comapany => $company_id_arr)
                        {
                            foreach ($company_id_arr as $recv_number => $receive_data_arr)
                            {
                                foreach($receive_data_arr as $DescData=>$DescArr){
                                    foreach( $DescArr as $index => $row)
                                    {                             
                                        $recv_level_span[$comapany][$recv_number]++;
                                        $company_level_span[$comapany]++;
                                        $desc_level_span[$comapany][$recv_number][$DescData]++;

                                    }
                                }
                            }
                            
                        }

                        // print_r($company_level_span);
                        // print_r($desc_level_span);


                        foreach ($dataarray as $comapany => $company_id_arr)
                        {
                            $comp_span = 0;
                            foreach ($company_id_arr as $recv_number => $receiveDataArr)
                            {
                                $recv_span = 0;
                                foreach($receiveDataArr as $descData=>$descArr){
                                    $DesSpan=0;
                                    foreach( $descArr as $index => $row)
                                    {
                                        if ($i % 2 == 0)
                                            $bgcolor = "#E9F3FF";
                                        else
                                            $bgcolor = "#FFFFFF";

                                            // echo $descData;
                                        ?>
                                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr<? echo $i; ?>">
                                            
                                            <? 
                                            if($comp_span== 0)
                                            {
                                                ?>
                                            <td width="30" align="center" rowspan="<?=$company_level_span[$comapany];?>" ><? echo $i; ?></td>
                                                <?
                                            }
                                        ?>
                                            <? 
                                            if($comp_span == 0)
                                            {
                                                ?>
                                                <td width="100"  rowspan="<?=$company_level_span[$comapany];?>"><? echo  $companyArr[$row['company_id']]; ?></td>
                                                <?
                                            }
                                        ?>

                                        <? 
                                            if($recv_span == 0)
                                            {
                                                ?>
                                                <td width="80"  rowspan="<?=$recv_level_span[$comapany][$recv_number];?>">
                                                    <p><? echo $row["recv_number"] ?>&nbsp;</p>
                                                </td>
                                                <?
                                            }
                                            ?>
                                            <? 
                                            if($recv_span == 0)
                                            {
                                                ?>
                                                <td width="70" align="center" rowspan="<?=$recv_level_span[$comapany][$recv_number];?>">
                                                <p><? echo change_date_format($row["receive_date"]); ?>&nbsp;</p>
                                            </td>
                                                <?
                                            }
                                            ?>
                                            <?
                                            if($recv_span == 0)
                                            {
                                                ?>
                                                <td width="100" align="center" rowspan="<?=$recv_level_span[$comapany][$recv_number];?>">
                                                <p><? echo   $supllier_arr[$row["supplier_id"]] ?>&nbsp;</p>
                                            </td>
                                                <?
                                            }
                                            ?>
                                            <?
                                            if($recv_span == 0)
                                            {
                                                ?>
                                                <td width="80" align="center" rowspan="<?=$recv_level_span[$comapany][$recv_number];?>">
                                                <p><? echo $row["challan_no"]; ?>&nbsp;</p>
                                            </td>
                                                <?
                                            }
                                            ?>
                                            <?
                                            if($recv_span == 0)
                                            {
                                                ?>
                                                <td width="70" align="center" rowspan="<?=$recv_level_span[$comapany][$recv_number];?>">
                                                <p><? echo $store_name_arr[$row["store_id"]]; ?>&nbsp;</p>
                                            </td>
                                                <?
                                            }
                                            ?>
                                            <?
                                            if($recv_span == 0)
                                            {
                                                ?>
                                                <td width="100" align="center" rowspan="<?=$recv_level_span[$comapany][$recv_number];?>">
                                                <p><? echo $receive_basis_arr[$row["receive_basis"]]; ?>&nbsp;</p>
                                            </td>
                                                <?
                                            }
                                            ?>
                                            <?
                                            if($recv_span == 0)
                                            {
                                                ?>
                                                <td width="90" align="center" rowspan="<?=$recv_level_span[$comapany][$recv_number];?>">
                                                <p><?  if( $row["receive_basis"]==7)
                                                {
                                                    echo $row["booking_no"];
                                                }
                                                else
                                                {
                                                    echo "";;
                                                }
                                                ?>&nbsp;</p>
                                            </td>
                                                <?
                                            }
                                            ?>
                                            <?
                                            if($recv_span == 0)
                                            {
                                                ?>
                                                <td width="80" align="center" rowspan="<?=$recv_level_span[$comapany][$recv_number];?>">
                                                <p><? 
                                                if( $row["receive_basis"]==2)
                                                {
                                                    echo $row["booking_no"];
                                                }
                                                else
                                                {
                                                    echo "";;
                                                }?>&nbsp;</p>
                                            </td>
                                                <?
                                            }
                                            ?>
                                            <?
                                            if($DesSpan == 0)
                                            {
                                                ?>
                                            <td width="100" align="right"  rowspan="<?=$desc_level_span[$comapany][$recv_number][$descData];?>"><? echo $category_arr[$row["item_category_id"]] ?></a></td>
                                                <?
                                            }
                                            ?>
                                            <?
                                            if($DesSpan == 0)
                                            {
                                                ?>
                                            <td width="100" align="right"  rowspan="<?=$desc_level_span[$comapany][$recv_number][$descData];?>"><? echo  $itemgroupArr[$row["item_group_id"]]; ?></a></td>
                                                <?
                                            }

                                            if($DesSpan==0){
                                            ?>                                          
                                            <td width="100" align="right" rowspan="<?=$desc_level_span[$comapany][$recv_number][$descData];?>"  ><? echo $row["item_description"] ?></td>
                                            <?}?>
                                            <?
                                            if($DesSpan==0){
                                            ?>   
                                            
                                            <td width="50" align="right"  rowspan="<?=$desc_level_span[$comapany][$recv_number][$descData];?>"><? echo $unit_of_measurement[$row["order_uom"]]; ?></td>
                                            <?}?>
                                            <?
                                            if($DesSpan==0){
                                            ?>   
                                            
                                            <td width="50" align="right" rowspan="<?=$desc_level_span[$comapany][$recv_number][$descData];?>" ><? echo $order_arr[$comapany][$recv_number][$descData]['order_qnty']; ?></td>
                                            <?}?>
                                        
                                            <td width="100" align="right"> <? echo change_date_format($row['expire_date']);?></td>
                                            <td align="center" width="70" > <?echo $row['serial_no'];?></td>
                                        </tr>
                                        <?
                                        $i++;
                                        $comp_span++;
                                        $recv_span++;
                                        $DesSpan++;
                                    }
                                }
                            }
                        }

                        ?>
                    </tbody>
                </table>
            </div>
            <table width="1850" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <tfoot>
                    <tr>
                        <th width="30">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="90">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    <?
  }

    if ($report_type == 2) 
    {
        $company_cond = "";
        $serial_no_con = "";
        $mrr_no_con = "";
        
        if ( $cbo_location_id != '') $location_conds .= " and b.location_id in ($cbo_location_id) ";
        if ( $cbo_store_id != '') $search_conds .= " and b.store_id in ($cbo_store_id) ";
        if (!empty($cbo_supplier_name)) $search_conds .=" and a.supplier_id='$cbo_supplier_name'";
        // if($txt_req_no != '') $search_conds=" and a.req_no='$txt_req_no'";
        if ($txt_req_no != '') $search_conds= " and a.issue_number like '%$txt_req_no'";
        if ($txt_serial_no) $serial_no_con = " and d.serial_no='$txt_serial_no'";
        if ($cbo_company_name != '') $company_cond .= " and a.company_id in ($cbo_company_name) ";
        if ($cbo_item_group_id != '') $search_conds .= " and c.item_group_id in ($cbo_item_group_id) ";
        if ( $txt_item_account_id != '') $search_conds .= " and b.prod_id in ($txt_item_account_id) ";
        if (!empty($cbo_item_category_id)) $search_conds .= " and c.item_category_id='$cbo_item_category_id' ";
        // if ($txt_mrr_number) $mrr_no_con = " and  a.issue_number_prefix_num='$txt_mrr_number'";
        if($txt_mrr_id != '')
        {
                $search_conds .= " and a.id in ($txt_mrr_id)";
        }
        else
        {
               if($txt_mrr_number!="")
               {
                $search_conds .= " and a.issue_number_prefix_num ='$txt_mrr_number'";  
               }  
        }
        if($txt_date_from!="" && $txt_date_to!="")
        {
            if($db_type==0)
            {
            if($txt_date_from!="" && $txt_date_to!="") $search_conds.=" and a.issue_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."' ";
            }
            else
            {
            if($txt_date_from!="" && $txt_date_to!="") $search_conds.="and a.issue_date between '".change_date_format($txt_date_from,'','',-1)."' and '".change_date_format($txt_date_to,'','',-1)."' "; 
            }
        }

        if($cbo_year!=0)
        {
            if($db_type==0)
            {
                $cbo_year_cond=" and year(a.issue_date)='$cbo_year'";
            }
            else
            {
                $cbo_year_cond=" and to_char(a.issue_date,'YYYY')='$cbo_year'";
            }
        }
        else {$cbo_year_cond="";}
       
        $sql = "SELECT a.company_id,a.issue_number,a.issue_date,a.supplier_id,a.challan_no,b.store_id, a.issue_basis, a.req_no, b.cons_uom, b.expire_date,b.cons_quantity,c.item_category_id, c.item_group_id,c.item_description     AS item_description, d.serial_no,a.issue_purpose,a.knit_dye_company
        FROM inv_issue_master  a, inv_transaction  b,product_details_master  c,inv_serial_no_details d   WHERE a.id = b.mst_id AND b.prod_id = c.id AND c.id = d.prod_id  and b.id=d.ISSUE_TRANS_ID and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $company_cond $serial_no_con  $search_conds $cbo_year_cond $location_conds order by a.issue_date desc";
         //echo $sql;
        $result = sql_select($sql);
        // echo "<pre>";
        //     print_r($result);
        $dataarray = array();
        foreach ($result as $row) {
            //$dataarray[$row['company_id']]['company_id'] = $row['company_id'];
            $index = $row[csf("expire_date")] ."**".$row[csf("serial_no")]."**".$row[csf("item_description")];
            $dataarray[$row[csf("company_id")]][$row[csf("issue_number")]][$row[csf("item_description")]][$index]["company_id"] = $row[csf("company_id")];
            $dataarray[$row[csf("company_id")]][$row[csf("issue_number")]][$row[csf("item_description")]][$index]["issue_number"] = $row[csf("issue_number")];
            $dataarray[$row[csf("company_id")]][$row[csf("issue_number")]][$row[csf("item_description")]][$index]["issue_date"] = $row[csf("issue_date")];
            $dataarray[$row[csf("company_id")]][$row[csf("issue_number")]][$row[csf("item_description")]][$index]["supplier_id"] = $row[csf("supplier_id")];
            $dataarray[$row[csf("company_id")]][$row[csf("issue_number")]][$row[csf("item_description")]][$index]["challan_no"] = $row[csf("challan_no")];
            $dataarray[$row[csf("company_id")]][$row[csf("issue_number")]][$row[csf("item_description")]][$index]["store_id"] = $row[csf("store_id")];
            $dataarray[$row[csf("company_id")]][$row[csf("issue_number")]][$row[csf("item_description")]][$index]["issue_basis"] = $row[csf("issue_basis")];
            $dataarray[$row[csf("company_id")]][$row[csf("issue_number")]][$row[csf("item_description")]][$index]["req_no"] = $row[csf("req_no")];
            $dataarray[$row[csf("company_id")]][$row[csf("issue_number")]][$row[csf("item_description")]][$index]["booking_no"] = $row[csf("booking_no")];
            $dataarray[$row[csf("company_id")]][$row[csf("issue_number")]][$row[csf("item_description")]][$index]["item_category_id"] = $row[csf("item_category_id")];
            $dataarray[$row[csf("company_id")]][$row[csf("issue_number")]][$row[csf("item_description")]][$index]["item_group_id"] = $row[csf("item_group_id")];
            $dataarray[$row[csf("company_id")]][$row[csf("issue_number")]][$row[csf("item_description")]][$index]["item_description"] = $row[csf("item_description")];
            $dataarray[$row[csf("company_id")]][$row[csf("issue_number")]][$row[csf("item_description")]][$index]["cons_uom"] = $row[csf("cons_uom")];
            $dataarray[$row[csf("company_id")]][$row[csf("issue_number")]][$row[csf("item_description")]][$index]["cons_quantity"] = $row[csf("cons_quantity")];
            $dataarray[$row[csf("company_id")]][$row[csf("issue_number")]][$row[csf("item_description")]][$index]["expire_date"]= $row[csf("expire_date")] ;
            $dataarray[$row[csf("company_id")]][$row[csf("issue_number")]][$row[csf("item_description")]][$index]["serial_no"]= $row[csf("serial_no")] ;
            $dataarray[$row[csf("company_id")]][$row[csf("issue_number")]][$row[csf("item_description")]][$index]["issue_purpose"]= $row[csf("issue_purpose")] ;
            $dataarray[$row[csf("company_id")]][$row[csf("issue_number")]][$row[csf("item_description")]][$index]["knit_dye_company"]= $row[csf("knit_dye_company")] ;
        }
        //  echo "<pre>";
        // print_r($dataarray);

        ob_start();
     ?>
        <div style="width:1870px; margin-left:10px;" align="left">
            <table width="1850" cellpadding="0" cellspacing="0" style="visibility:hidden; border:none" id="caption">
                <tr>
                    <td align="center" width="100%" colspan="19" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
                </tr>
            </table>
            <table width="1850" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="80">Company Name</th>
                        <th width="80">Trans. Ref.</th>
                        <th width="80">Trans. Date</th>
                        <th width="80">Issue Req. No.</th>
                        <th width="80">Issue Purpose</th>
                        <th width="70">Issue To</th>
                        <th width="80">Store Name</th>
                        <th width="80">Item Category</th>
                        <th width="80">Item Group</th>
                        <th width="80">Item Description</th>
                        <th width="50">UOM</th>
                        <th width="50">Issue Qnty</th>
                        <th width="60">Serial No</th>
                    </tr>
                </thead>
            </table>
            <div style="width:1870px; overflow-y:scroll; max-height:350px; overflow-x:hidden;" id="scroll_body" align="left">
                <table width="1850" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_bodyy">
                    <tbody>
                    <?
                        $i = 1;
                        // echo "<pre>";
                        // print_r($dataarray);die;
                        $company_level_span = array();
                        $recv_level_span = array();
                        $desc_level_span = array();
                        foreach ($dataarray as $comapany => $company_id_arr)
                        {
                            foreach ($company_id_arr as $recv_number => $receive_data_arr)
                            {
                                foreach($receive_data_arr as $DescData=>$DescArr){
                                    foreach( $DescArr as $index => $row)
                                    {                             
                                        $recv_level_span[$comapany][$recv_number]++;
                                        $company_level_span[$comapany]++;
                                        $desc_level_span[$comapany][$recv_number][$DescData]++;

                                    }
                                }
                            }
                            
                        }

                        // print_r($company_level_span);
                        // print_r($desc_level_span);
                        foreach ($dataarray as $comapany => $company_id_arr)
                        {
                            $comp_span = 0;
                            foreach ($company_id_arr as $recv_number => $receiveDataArr)
                            {
                                $recv_span = 0;
                                foreach($receiveDataArr as $descData=>$descArr){
                                    $DesSpan=0;
                                    foreach( $descArr as $index => $row)
                                    {
                                        if ($i % 2 == 0)
                                            $bgcolor = "#E9F3FF";
                                        else
                                            $bgcolor = "#FFFFFF";
                                        ?>
                                       <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr<? echo $i; ?>">
                                          
                                          <? 
                                          if($comp_span== 0)
                                          {
                                              ?>
                                             <td width="30" align="center" rowspan="<?=$company_level_span[$comapany];?>" ><? echo $i; ?></td>
                                              <?
                                          }
                                         ?>
                                          <? 
                                          if($comp_span == 0)
                                          {
                                              ?>
                                              <td width="80"  rowspan="<?=$company_level_span[$comapany];?>"><? echo  $companyArr[$row['company_id']]; ?></td>
                                              <?
                                          }
                                         ?>
  
                                         <? 
                                          if($recv_span == 0)
                                          {
                                              ?>
                                              <td width="80"  rowspan="<?=$recv_level_span[$comapany][$recv_number];?>">
                                                  <p><? echo $row["issue_number"] ?>&nbsp;</p>
                                              </td>
                                              <?
                                          }
                                          ?>
                                          <? 
                                          if($recv_span == 0)
                                          {
                                              ?>
                                              <td width="80" align="center" rowspan="<?=$recv_level_span[$comapany][$recv_number];?>">
                                              <p><? echo change_date_format($row["issue_date"]); ?>&nbsp;</p>
                                          </td>
                                              <?
                                          }
                                          ?>
                                           <?
                                            if($recv_span == 0)
                                          {
                                              ?>
                                              <td width="80" align="center" rowspan="<?=$recv_level_span[$comapany][$recv_number];?>">
                                              <p><? echo  $row["req_no"];; ?>&nbsp;</p>
                                          </td>
                                              <?
                                          }
                                          ?>
                                         
                                           
                                           <?
                                            if($recv_span == 0)
                                          {
                                              ?>
                                              <td width="80" align="center" rowspan="<?=$recv_level_span[$comapany][$recv_number];?>">
                                              <p><? echo $general_issue_purpose[$row['issue_purpose']]; ?>&nbsp;</p>
                                          </td>
                                              <?
                                          }
                                          ?>
                                            <?
                                            if($recv_span == 0)
                                          {
                                              ?>
                                              <td width="70" align="center" rowspan="<?=$recv_level_span[$comapany][$recv_number];?>">
                                              <p><? echo $companyArr[$row['knit_dye_company']];; ?>&nbsp;</p>
                                          </td>
                                              <?
                                          }
                                          ?>
                                          
                                           <?
                                           if($DesSpan == 0)
                                          {
                                              ?>
                                              <td width="80" align="center" rowspan="<?=$desc_level_span[$comapany][$recv_number][$descData];?>">
                                              <p><? echo $store_name_arr[$row["store_id"]]; ?>&nbsp;</p>
                                          </td>
                                              <?
                                          }
                                          ?>
                                            <?
                                            if($DesSpan == 0)
                                            {
                                                ?>
                                            <td width="80" align="right" rowspan="<?=$desc_level_span[$comapany][$recv_number][$descData];?>"><? echo $category_arr[$row["item_category_id"]] ?></a></td>
                                                <?
                                            }
                                            ?>
                                            <?
                                            if($DesSpan == 0)
                                            {
                                                ?>
                                            <td width="80" align="right"  rowspan="<?=$desc_level_span[$comapany][$recv_number][$descData];?>"><? echo  $itemgroupArr[$row["item_group_id"]]; ?></a></td>
                                                <?
                                            }

                                            if($DesSpan==0){
                                            ?>                                          
                                            <td width="80" align="right" rowspan="<?=$desc_level_span[$comapany][$recv_number][$descData];?>"  ><? echo $row["item_description"] ?></td>
                                            <?}?>
                                            <?
                                            if($DesSpan==0){
                                            ?>   
                                            
                                            <td width="50" align="right"  rowspan="<?=$desc_level_span[$comapany][$recv_number][$descData];?>"><? echo $unit_of_measurement[$row["cons_uom"]]; ?></td>
                                            <?}?>
                                            <?
                                            if($DesSpan==0){
                                            ?>   
                                            
                                            <td width="50" align="right" rowspan="<?=$desc_level_span[$comapany][$recv_number][$descData];?>" ><? echo $row["cons_quantity"] ?></td>
                                            <?}?>
                                            <td align="center" width="60" > <?echo $row['serial_no'];?></td>
                                        </tr>
                                        <?
                                        $i++;
                                        $comp_span++;
                                        $recv_span++;
                                        $DesSpan++;
                                    }
                                }
                            }
                        }

                        ?>
                      
                    </tbody>
                </table>
            </div>
            <table width="1850" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <tfoot>
                    <tr>
                        <th width="30">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="80">&nbsp;</th> 
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
        </div>
      <?
     }
   exit();
}




?>