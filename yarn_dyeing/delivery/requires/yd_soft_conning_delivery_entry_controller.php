<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header('location:login.php');
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$user_level=$_SESSION['logic_erp']["user_level"];

if($action == 'load_drop_down_location') {
	echo create_drop_down('cbo_location_name', 163, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name", 'id,location_name', 1, '-- Select --', $selected, '');
	exit();
}

if($action=='load_drop_down_buyer') {
	echo create_drop_down('cbo_party_name', 163, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", 'id,company_name', 1, '-- Select Party --', $selected, '');
		exit();
}

if($action == 'sales_no_popup') {
	echo load_html_head_contents('Search Yarn Dyeing Sales Order', '../../../', 1, 0, $unicode);
	extract($_REQUEST);
?>
    <style>
        table.rpt_table tbody td input {
            width: 90%;
        } 
    </style>
	<script>
		permission="<?php echo $permission; ?>";

		function js_set_value(id) {
			document.getElementById('selected_prod_id').value = id;
			parent.salesPopup.hide();
		}
	</script>
</head>
<body>
<div align="center" style="width:100%;" >
    <form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" style="width: 100%;">
            <thead>
                <tr>
                    <th colspan="8"><?php echo create_drop_down('cbo_string_search_type', 163, $string_search_type, '', 1, '-- Searching Type --'); ?></th>
                </tr>
                <tr>
                    <th style="width:20%;">Company Name</th>
                    <th style="width:20%;">Sales order no</th>
                    <th style="width:25%;">Job No</th>
                    <th style="width:25%;">WO No</th>
                    <th style="width:20%;">
                    	<input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width: 90%;" />
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr class="general">
                    <td>
                        <?php echo create_drop_down('cbo_company_name', 163, "select id, company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", 'id,company_name', 1, '-- Select Company --', $data, '', 1); ?>
                    </td>
                    <td>
                        <input class="text_boxes" type="text" name="txt_sales_ord_no" id="txt_sales_ord_no" />
                    </td>
                    <td>
                        <input class="text_boxes" type="text" name="txt_job_no" id="txt_job_no" />
                    </td>
                    <td>
                        <input class="text_boxes" type="text" name="txt_workorder_no" id="txt_workorder_no" />
                    </td>
                    <td align="center">
                        <input type="hidden" id="selected_prod_id">
                        <input type="button" name="btnSearchJob" class="formbutton" value="Show" onClick="show_list_view(document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_sales_ord_no').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_workorder_no').value, 'create_yd_order_list_view', 'search_div', 'yd_soft_conning_delivery_entry_controller', '')" />
                    </td>
                </tr>
                <tr>
                    <td colspan="8" align="center" valign="top" id=""><div id="search_div"></div></td>
                </tr>
            </tbody>
        </table>
    </form>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?php
exit();
}

if($action == 'create_yd_order_list_view') {
    // echo $data;die;
    $data=explode('_', $data);
    $search_type = $data[0];
    $company_id = $data[1];
    $ord_no = $data[2];
    $yd_job = $data[3];
    $wo_no = $data[4];
    $condition = '';

    if($company_id) {
        $condition.=" and a.company_id=$data[1]";
    } else {
        echo "<h3 style='margin-top: 10px;'>Please Select Company First.</h3>"; die;
    }

    if($search_type==0 || $search_type==4) { // no searching type or contents
        if ($yd_job!="") $condition.=" and c.yd_job like '%$yd_job%'";
        if ($ord_no!="") $condition.=" and b.sales_order_no like '%$ord_no%'";
    } else if($search_type==1) { // exact
        if ($yd_job!="") $condition.=" and c.yd_job = '$yd_job'";
        if ($ord_no!="") $condition.=" and b.sales_order_no ='$ord_no'";
    } else if($search_type==2) { // Starts with
        if ($yd_job!="") $condition.=" and c.yd_job like '$yd_job%'";
        if ($ord_no!="") $condition.=" and b.sales_order_no like '$ord_no%'";
    } else if($search_type==3) { // Ends with
        if ($yd_job!="") $condition.=" and c.yd_job like '%$yd_job'";
        if ($ord_no!="") $condition.=" and b.sales_order_no like '%$ord_no'";
    }

    $color_arr = return_library_array('select id, color_name from lib_color where status_active=1 and is_deleted=0', 'id', 'color_name');
    
    /*$sql = "select a.mst_id, a.job_id, b.receive_date, b.yd_job, c.sales_order_no, a.quantity
            from yd_production_dtls a, yd_ord_mst b, yd_ord_dtls c
            where a.status_active=1 and b.status_active=1 and c.status_active=1 $condition and a.job_id=b.id and b.id=c.mst_id
            group by a.mst_id, a.job_id, b.yd_job, c.sales_order_no, b.receive_date, a.quantity";*/

    $sql = "select a.id, b.job_id, a.yd_prod_no, c.yd_job, b.sales_order_no, sum(b.quantity) as prod_qty, c.receive_date
            from yd_production_mst a, yd_production_dtls b, yd_ord_mst c
            where a.status_active = 1 and b.status_active = 1 and c.status_active = 1 $condition and a.entry_form = 397 and a.id = b.mst_id and c.id = b.job_id
            group by a.id, b.job_id, a.yd_prod_no, c.yd_job, b.sales_order_no,c.receive_date order by a.yd_prod_no";

 //echo $sql;

    $arr=array(2=>$color_arr);

    echo create_list_view('list_view', 'Job No,Sales Order No,Production Quantity,Receive Date', '120,120,130,100', 650, 300, 0, $sql, 'js_set_value', 'id', '', 1, '0,0,0,0,0', '', 'yd_job,sales_order_no,prod_qty,receive_date', '', '', '0,0,0,0,0');

    exit();
}

if($action == 'delivery_id_popup') {
    echo load_html_head_contents('Search Yarn Dyeing Sales Order', '../../../', 1, 0, $unicode);
    extract($_REQUEST);
?>
    <style>
        table.rpt_table tbody td input {
            width: 90%;
        }
    </style>
    <script>
        permission="<?php echo $permission; ?>";

        function js_set_value(id) {
            document.getElementById('selected_delivery_id').value = id;
            parent.deliveryPopup.hide();
        }
    </script>
</head>
<body>
<div align="center" style="width:100%;" >
    <form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" style="width: 100%;">
            <thead>
                <tr>
                    <th colspan="8"><?php echo create_drop_down('cbo_string_search_type', 163, $string_search_type, '', 1, '-- Searching Type --'); ?></th>
                </tr>
                <tr>
                    <th style="width:20%;">Company Name</th>
                    <th style="width:20%;">Sales order no</th>
                    <th style="width:25%;">Job No</th>
                    <th style="width:25%;">Delivery ID</th>
                    <th style="width:20%;">
                        <input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width: 90%;" />
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr class="general">
                    <td>
                        <?php echo create_drop_down('cbo_company_name', 163, "select id, company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", 'id,company_name', 1, '-- Select Company --', $data, '', 1); ?>
                    </td>
                    <td>
                        <input class="text_boxes" type="text" name="txt_sales_ord_no" id="txt_sales_ord_no" />
                    </td>
                    <td>
                        <input class="text_boxes" type="text" name="txt_job_no" id="txt_job_no" />
                    </td>
                    <td>
                        <input class="text_boxes" type="text" name="txt_delivery_id" id="txt_delivery_id" />
                    </td>
                    <td align="center">
                        <input type="hidden" id="selected_delivery_id">
                        <input type="button" name="btnSearchJob" class="formbutton" value="Show" onClick="show_list_view(document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_sales_ord_no').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_delivery_id').value, 'create_yd_delivery_list_view', 'search_div', 'yd_soft_conning_delivery_entry_controller', '')" />
                    </td>
                </tr>
                <tr>
                    <td colspan="8" align="center" valign="top" id=""><div id="search_div"></div></td>
                </tr>
            </tbody>
        </table>
    </form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?php
exit();
}

if($action == 'create_yd_delivery_list_view') 
{
    // echo $data;die;
    $data=explode('_', $data);
    $search_type = $data[0];
    $company_id = $data[1];
    $ord_no = $data[2];
    $yd_job = $data[3];
    $delivery_id = $data[4];
    $condition = '';

    if($company_id) {
        $condition.=" and d.company_id=$data[1]";
    } else {
        echo "<h3 style='margin-top: 10px;'>Please Select Company First.</h3>"; die;
    }

    if($search_type==0 || $search_type==4) { // no searching type or contents
        if ($yd_job!="") $condition.=" and c.yd_job like '%$yd_job%'";
        if ($ord_no!="") $condition.=" and b.sales_order_no like '%$ord_no%'";
        if ($delivery_id!="") $condition.=" and a.delivery_id like '%$delivery_id%'";
    } else if($search_type==1) { // exact
        if ($yd_job!="") $condition.=" and c.yd_job = '$yd_job'";
        if ($ord_no!="") $condition.=" and b.sales_order_no ='$ord_no'";
        if ($delivery_id!="") $condition.=" and a.delivery_id = '$delivery_id'";
    } else if($search_type==2) { // Starts with
        if ($yd_job!="") $condition.=" and c.yd_job like '$yd_job%'";
        if ($ord_no!="") $condition.=" and b.sales_order_no like '$ord_no%'";
        if ($delivery_id!="") $condition.=" and a.delivery_id like '$delivery_id%'";
    } else if($search_type==3) { // Ends with
        if ($yd_job!="") $condition.=" and c.yd_job like '%$yd_job'";
        if ($ord_no!="") $condition.=" and b.sales_order_no like '%$ord_no'";
        if ($delivery_id!="") $condition.=" and a.delivery_id like '%$delivery_id'";
    }

    $color_arr = return_library_array('select id, color_name from lib_color where status_active=1 and is_deleted=0', 'id', 'color_name');
    
    /*$sql = "select d.id, a.mst_id, a.job_id, b.receive_date, b.yd_job, c.sales_order_no, d.delivery_id
            from yd_production_dtls a, yd_ord_mst b, yd_ord_dtls c, yd_delivery_mst d, yd_delivery_dtls e
            where a.status_active = 1 and b.status_active = 1 and c.status_active = 1 $condition and a.job_id = b.id and b.id=c.mst_id and e.mst_id = d.id and e.job_dtls_id = c.id and d.entry_form = 400
         and a.sales_order_no = e.sales_order_no
            group by d.id, a.mst_id, a.job_id, b.yd_job, c.sales_order_no, b.receive_date, a.quantity, d.delivery_id";*/

    $sql = "select a.id, a.delivery_id, b.sales_order_no, c.yd_job, c.receive_date
            from yd_delivery_mst a, yd_delivery_dtls b, yd_ord_mst c
            where a.status_active=1 and a.entry_form=400 and a.order_no=c.order_no and a.id=b.mst_id
            order by a.delivery_id desc";
    
    $arr=array(2=>$color_arr);  
    echo create_list_view('list_view', 'Job No,Sales Order No,Delivery ID,Receive Date', '120,120,120,100', 600, 300, 0, $sql, 'js_set_value', 'id', '', 1, '0,0,0,0,0', '', 'yd_job,sales_order_no,delivery_id,receive_date',"yd_soft_conning_delivery_entry_controller", 'setFilterGrid("list_view",-1);', '0,0,0,0,0');
	 
    exit();
}
if($action == 'populate_mst_data_from_search_popup') {
    $data = explode('**', $data);
    $reqType = $data[0];
    $prodId;
    $deliveryMstId;
    $jobDtlsIds='';

    if($reqType == 1) {     // new entry
        $prodId = $data[1];
    } else {                // update mode
        $deliveryMstId = $data[1];
    }
	//echo "select a.job_dtls_id, sum(a.delivery_quantity) as delivery_quantity from yd_delivery_dtls a, yd_delivery_mst b where b.id=a.mst_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0"; die;
	 $prod_qty_arr = return_library_array('select a.job_dtls_id, sum(a.delivery_quantity) as delivery_quantity from yd_delivery_dtls a, yd_delivery_mst b where b.id=a.mst_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form=400 group by a.job_dtls_id', 'job_dtls_id', 'delivery_quantity');
	 
	// print_r($prod_qty_arr); die;

    if($reqType == 1) 
	{    
        $sql = "select b.id, a.company_id, a.location_id, a.party_id, sum(b.quantity) as prod_qty, c.style_ref, c.order_id, c.order_no, a.booking_without_order, a.booking_type
                from yd_production_mst a, yd_production_dtls b, yd_ord_dtls c
                where a.status_active = 1 and b.status_active = 1 and a.id = $prodId and a.id=b.mst_id and b.job_dtls_id = c.id
                group by b.id, a.company_id, a.location_id, a.party_id, b.quantity, c.style_ref, c.order_id, c.order_no, a.booking_without_order, a.booking_type";
    }
	else 
	{
        $sql = "select a.id, a.order_id, a.company_id, a.location_id, a.within_group, a.party_id, a.remarks,d.style_ref, b.job_dtls_id,sum(b.quantity) as prod_qty, c.delivery_id, a.order_id, a.order_no, a.booking_without_order, a.booking_type
        from yd_ord_mst a, yd_production_dtls b, yd_delivery_mst c, yd_ord_dtls d
        where a.status_active=1 and a.is_deleted=0 and c.id=$deliveryMstId and c.entry_form=400  and a.id=b.job_id and a.id=d.mst_id and b.job_dtls_id=d.id
        group by a.id, a.order_id, a.company_id, a.location_id, a.within_group, a.party_id,a.remarks,d.style_ref, c.delivery_id, a.order_id, a.order_no, a.booking_without_order, a.booking_type,b.job_dtls_id";
    }    

    // echo $sql;

    $result = sql_select($sql);

    foreach ($result as $row) {
        $jobDtlsIds .= $row[csf('id')].',';
    }

    if ($reqType == 2) {
        echo "document.getElementById('txt_delivery_id').value = '".$result[0][csf('delivery_id')]."';\n";
    }
    echo "document.getElementById('cbo_company_name').value = '".$result[0][csf('company_id')]."';\n";
    // echo "document.getElementById('hdnOrderId').value = '".$result[0][csf('order_id')]."';\n";
    // echo "load_drop_down('requires/yd_soft_conning_delivery_entry_controller', '".$result[0][csf('company_id')]."', 'load_drop_down_buyer', 'buyer_td');";
    // echo "load_drop_down('requires/yd_soft_conning_delivery_entry_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location', 'location_td' );";
    echo "document.getElementById('cbo_location_name').value = '".$result[0][csf('location_id')]."';\n";
    echo "document.getElementById('cbo_party_name').value = '".$result[0][csf('party_id')]."';\n";
    echo "document.getElementById('txtProductionQty').value = '".$result[0][csf('prod_qty')]."';\n";
	echo "document.getElementById('txt_balance').value = '".($result[0][csf('prod_qty')]-$prod_qty_arr[$result[0][csf('job_dtls_id')]])."';\n";
    echo "document.getElementById('txt_style').value = '".$result[0][csf('style_ref')]."';\n";
    echo "document.getElementById('hdnOrderId').value = '".$result[0][csf('order_id')]."';\n";
    echo "document.getElementById('hdnOrderNo').value = '".$result[0][csf('order_no')]."';\n";
    echo "document.getElementById('hdnBookingWithoutOrder').value = '".$result[0][csf('booking_without_order')]."';\n";
    echo "document.getElementById('hdnBookingType').value = '".$result[0][csf('booking_type')]."';\n";
    echo "document.getElementById('hdnJobDtlsIds').value = '".rtrim($jobDtlsIds, ',')."';\n";
}
if($action == 'populate_mst_data_from_search_popup_backup') {
    $data = explode('**', $data);
    $reqType = $data[0];
    $prodId;
    $deliveryMstId;
    $jobDtlsIds='';

    if($reqType == 1) {     // new entry
        $prodId = $data[1];
    } else {                // update mode
        $deliveryMstId = $data[1];
    }

    if($reqType == 1) {    // new entry
        /*$sql = "select a.id, a.order_id, a.company_id, a.location_id, a.within_group, a.party_id, a.remarks, sum(b.quantity) as prod_qnty, a.order_id, a.order_no, a.booking_without_order, a.booking_type
        from yd_ord_mst a, yd_production_dtls b
        where a.status_active=1 and a.is_deleted=0 and a.id=$prodId and a.id=b.job_id
        group by a.id, a.order_id, a.company_id, a.location_id, a.within_group, a.party_id,a.remarks, a.order_id, a.order_no, a.booking_without_order, a.booking_type";*/
        $sql = "select b.id, a.company_id, a.location_id, a.party_id, sum(b.quantity) as prod_qty, c.style_ref, c.order_id, c.order_no, a.booking_without_order, a.booking_type
                from yd_production_mst a, yd_production_dtls b, yd_ord_dtls c
                where a.status_active = 1 and b.status_active = 1 and a.id = $prodId and a.id=b.mst_id and b.job_dtls_id = c.id
                group by b.id, a.company_id, a.location_id, a.party_id, b.quantity, c.style_ref, c.order_id, c.order_no, a.booking_without_order, a.booking_type";
    } else {
        $sql = "select a.id, a.order_id, a.company_id, a.location_id, a.within_group, a.party_id, a.remarks, sum(b.quantity) as prod_qty, c.delivery_id, a.order_id, a.order_no, a.booking_without_order, a.booking_type
        from yd_ord_mst a, yd_production_dtls b, yd_delivery_mst c
        where a.status_active=1 and a.is_deleted=0 and c.id=$deliveryMstId and a.id=b.job_id
        group by a.id, a.order_id, a.company_id, a.location_id, a.within_group, a.party_id,a.remarks, c.delivery_id, a.order_id, a.order_no, a.booking_without_order, a.booking_type";
    }    

    // echo $sql;

    $result = sql_select($sql);

    foreach ($result as $row) {
        $jobDtlsIds .= $row[csf('id')].',';
    }

    if ($reqType == 2) {
        echo "document.getElementById('txt_delivery_id').value = '".$result[0][csf('delivery_id')]."';\n";
    }
    echo "document.getElementById('cbo_company_name').value = '".$result[0][csf('company_id')]."';\n";
    // echo "document.getElementById('hdnOrderId').value = '".$result[0][csf('order_id')]."';\n";
    // echo "load_drop_down('requires/yd_soft_conning_delivery_entry_controller', '".$result[0][csf('company_id')]."', 'load_drop_down_buyer', 'buyer_td');";
    // echo "load_drop_down('requires/yd_soft_conning_delivery_entry_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location', 'location_td' );";
    echo "document.getElementById('cbo_location_name').value = '".$result[0][csf('location_id')]."';\n";
    echo "document.getElementById('cbo_party_name').value = '".$result[0][csf('party_id')]."';\n";
    echo "document.getElementById('txtProductionQty').value = '".$result[0][csf('prod_qty')]."';\n";
    echo "document.getElementById('txt_style').value = '".$result[0][csf('style_ref')]."';\n";
    echo "document.getElementById('hdnOrderId').value = '".$result[0][csf('order_id')]."';\n";
    echo "document.getElementById('hdnOrderNo').value = '".$result[0][csf('order_no')]."';\n";
    echo "document.getElementById('hdnBookingWithoutOrder').value = '".$result[0][csf('booking_without_order')]."';\n";
    echo "document.getElementById('hdnBookingType').value = '".$result[0][csf('booking_type')]."';\n";
    echo "document.getElementById('hdnJobDtlsIds').value = '".rtrim($jobDtlsIds, ',')."';\n";
}

if($action == 'populate_dtls_data_from_search_popup') 
{
	$data = explode('**', $data);
    $reqType = $data[0];
    // $jobDtlsIds = $data[2];
	$jobId;
    $deliveryMstId;
	$sl = 1;
    $ydBatch_arr;

    if($reqType == 1) {     // new entry
        $jobId = $data[1];
    } else {                // update mode
        $deliveryMstId = $data[1];
    }

    $color_arr = return_library_array('select id, color_name from lib_color where status_active=1 and is_deleted=0', 'id', 'color_name');
   // $count_arr = return_library_array("select id,construction from lib_yarn_count_determina_mst where is_deleted=0 and status_active=1", 'id', 'construction');
	
	$count_arr = return_library_array("select id, yarn_count from  lib_yarn_count where  status_active=1", 'id', 'yarn_count');
    $comp_arr = return_library_array("select id,composition_name from lib_composition_array where is_deleted=0 and status_active=1", 'id', 'composition_name');
	
  /* $sql_del="SELECT a.id, a.mst_id, a.order_id, a.delivery_qty, a.remarks, a.color_size_id, a.bill_status, a.sort_qty, a.reject_qty,a.delivery_status ,a. cutting_number,a.defect_qty from yd_delivery_dtls a, yd_delivery_mst b where b.id=a.mst_id and b.job_no='$jobno' and b.entry_form=400 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
 	$sql_del_res =sql_select($sql_del);
	$updtls_data_arr=array(); $pre_qty_arr=array();
	
	foreach ($sql_del_res as $row)
	{
		if($row[csf("mst_id")]==$update_id)
		{
			$updtls_data_arr[$row[csf("order_id")]][$row[csf("color_size_id")]]['dtlsid']=$row[csf("id")];
			$updtls_data_arr[$row[csf("order_id")]][$row[csf("color_size_id")]]['qty']=$row[csf("delivery_qty")];
			$updtls_data_arr[$row[csf("order_id")]][$row[csf("color_size_id")]]['remarks']=$row[csf("remarks")];
			$updtls_data_arr[$row[csf("order_id")]][$row[csf("color_size_id")]]['bill_status']=$row[csf("bill_status")];
			$updtls_data_arr[$row[csf("order_id")]][$row[csf("color_size_id")]]['sort_qty']=$row[csf("sort_qty")];
			$updtls_data_arr[$row[csf("order_id")]][$row[csf("color_size_id")]]['reject_qty']=$row[csf("reject_qty")];
			$updtls_data_arr[$row[csf("order_id")]][$row[csf("color_size_id")]]['delivery_status']=$row[csf("delivery_status")];
			$updtls_data_arr[$row[csf("order_id")]][$row[csf("color_size_id")]]['cutting_number']=$row[csf("cutting_number")];
			$updtls_data_arr[$row[csf("order_id")]][$row[csf("color_size_id")]]['defect_qty']=$row[csf("defect_qty")];
		}
		else
		{
			$pre_qty_arr[$row[csf("order_id")]][$row[csf("color_size_id")]]['qty']+=$row[csf("delivery_qty")];
		}
	}
	unset($sql_del_res);*/

    if($reqType == 1) 
	{    // new entry
        /*$sql = "select a.company_id, b.id, b.mst_id, b.sales_order_no, b.lot, b.count_id, b.yarn_type_id, b.yarn_composition_id, b.yd_color_id, c.bobbin_type, c.winding_pckg_qty, b.sales_order_id, b.sales_order_no, b.product_id
            from yd_ord_mst a, yd_ord_dtls b, yd_batch_dtls c
            where a.status_active=1 and a.is_deleted=0 and a.id=$jobId and a.id=b.mst_id and a.id=c.yd_job_id";*/

     $sql = "select a.id as prod_dtls_id, a.sales_order_no, b.lot, b.count_id, b.yarn_type_id, b.yarn_composition_id, b.yd_color_id, a.job_dtls_id, a.product_id, b.sales_order_id, a.bobbin_type, a.winding_qty,a.quantity as production_quantity
                from yd_production_dtls a, yd_ord_dtls b
                where a.status_active = 1 and b.status_active = 1 and a.mst_id = $jobId and a.job_dtls_id = b.id";
        /*$sql = "select a.id, b.job_id, a.yd_prod_no, c.yd_job, b.sales_order_no, b.quantity, c.receive_date
            from yd_production_mst a, yd_production_dtls b, yd_ord_mst c
            where a.status_active = 1 and b.status_active = 1 and c.status_active = 1 $condition and a.entry_form = 397 and a.id = b.mst_id and c.id = b.job_id
            group by a.id, b.job_id, a.yd_prod_no, c.yd_job, b.sales_order_no, b.quantity, c.receive_date order by a.yd_prod_no";*/
    } 
	else 
	{    // update mode
        /*$sql = "select a.id, b.mst_id, b.sales_order_no, b.lot, b.count_id, b.yarn_type_id, b.yarn_composition_id, b.yd_color_id, c.bobbin_type, c.winding_pckg_qty, a.delivery_quantity, b.sales_order_id, b.product_id
                from yd_delivery_dtls a, yd_ord_dtls b, yd_batch_dtls c
                where a.status_active=1 and a.is_deleted=0 and a.mst_id=$deliveryMstId and a.job_dtls_id=b.id and c.yd_job_dtls_id=b.id";*/

        $sql = "select a.id as del_dtls_id,a.prod_dtls_id, b.mst_id, b.sales_order_no, b.lot, b.count_id, b.yarn_type_id, b.yarn_composition_id, b.yd_color_id, c.bobbin_type, c.winding_qty, a.delivery_quantity, b.sales_order_id, b.product_id,c.quantity as production_quantity
                from yd_delivery_dtls a, yd_ord_dtls b, yd_production_dtls c
                where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.mst_id=$deliveryMstId and a.job_dtls_id=b.id  and a.entry_form=400 and a.prod_dtls_id=c.id
                group by a.id, b.mst_id, b.sales_order_no, b.lot, b.count_id, b.yarn_type_id, b.yarn_composition_id, b.yd_color_id, c.bobbin_type, c.winding_qty, a.delivery_quantity, b.sales_order_id, b.product_id ,a.prod_dtls_id,c.quantity";
            /*$sql = "select a.id, a.job_dtls_id, b.mst_id, b.id ord_dtls_id, b.sales_order_no, b.lot, b.count_id, b.yarn_type_id, b.yarn_composition_id, b.yd_color_id, a.delivery_quantity, b.sales_order_id, b.product_id
                from yd_delivery_dtls a, yd_ord_dtls b
                where a.status_active=1 and b.status_active=1 and a.mst_id=$deliveryMstId and a.job_dtls_id=b.id";*/
    }

    //echo $sql;

	$result = sql_select($sql);

    // print_r($result);

    // echo "document.getElementById('cbo_company_name').value = '".$result[0][csf('company_id')]."';\n";
    ob_start();
?>

<fieldset>
    <legend style="width: 99%;">Soft Coning Production Delivery entry Details</legend>
    <table cellpadding="0" cellspacing="2" border="0" class="rpt_table" rules="all" id="tbl_dtls_delivery" style="table-layout: fixed; width: 100%;">
        <thead class="form_table_header">
            <th id="sales_order">Sales order no</th>
            <th id="lot_td">Lot</th>
            <th id="count_td">Count</th>
            <th class="must_entry_caption">Yarn Type</th>
            <th id="composition_td" class="must_entry_caption">Yarn Composition</th>
            <th>Y/D Color</th>
            <th class="must_entry_caption">Bobbin Type</th>
            <th class="must_entry_caption" id="order_uom_td">Winding Package Qty(PCS)</th>
            <th class="must_entry_caption">Delivery Qty</th>
        </thead>
        <tbody id="delivery-details-rows">
        <?php
        foreach($result as $row) {
            ?>
            <tr>
                <td>
                    <input name="txtSalesOrder_<?php echo $sl; ?>" id="txtSalesOrder_<?php echo $sl; ?>" type="text" class="text_boxes" placeholder="Double Click" onDblClick="openSalesOrderPopup();" value="<?php echo $row[csf('sales_order_no')]; ?>" readonly style="width: 90%;" />
                </td>
                <td>
                    <input name="txtLot_<?php echo $sl; ?>" id="txtLot_<?php echo $sl; ?>" type="text" class="text_boxes" value="<?php echo $row[csf('lot')]; ?>" readonly style="width: 90%;" />
                </td>

                <td>
                    <input name="txtcount_<?php echo $sl; ?>" id="txtcount_<?php echo $sl; ?>" type="text" class="text_boxes" value="<?php echo $count_arr[$row[csf('count_id')]]; ?>" readonly style="width: 90%;" />
                </td>

                <td>
                    <input name="txtYarnType_<?php echo $sl; ?>" id="txtYarnType_<?php echo $sl; ?>" type="text" class="text_boxes" value="<?php echo $yarn_type[$row[csf('yarn_type_id')]]; ?>" readonly style="width: 90%;" />
                </td>

                <td>
                    <input name="txtComposition_<?php echo $sl; ?>" id="txtComposition_<?php echo $sl; ?>" type="text" class="text_boxes" value="<?php echo $comp_arr[$row[csf('yarn_composition_id')]]; ?>" readonly style="width: 90%;" />
                </td>

                <td>
                    <input name="txtYDcolor_<?php echo $sl; ?>" id="txtYDcolor_<?php echo $sl; ?>" type="text" class="text_boxes" value="<?php echo $color_arr[$row[csf('yd_color_id')]]; ?>" readonly style="width: 90%;" />
                </td>
                <td>
                    <input name="txtBobbinType_<?php echo $sl; ?>" id="txtBobbinType_<?php echo $sl; ?>" class="text_boxes" type="text" value="<?php echo $row[csf('bobbin_type')]; ?>" readonly style="width: 90%;" />
                </td>
                <td>
                    <input name="txtPackageQty_<?php echo $sl; ?>" id="txtPackageQty_<?php echo $sl; ?>" type="text" class="text_boxes_numeric" value="<?php echo $row[csf('winding_qty')]; ?> "readonly style="width: 90%;" />
                </td>
                <td>
                    <input name="txtDeliveryQty_<?php echo $sl; ?>" id="txtDeliveryQty_<?php echo $sl; ?>" type="text" class="text_boxes_numeric" placeholder="<?php echo $row[csf('production_quantity')]; ?>" value="<?php echo $row[csf('delivery_quantity')]; ?>" onBlur="checkPackgQty(this.value,<?php echo $sl;?>)" style="width: 90%;" />
                    <input type="hidden" id="hdnDtlsId_<?php echo $sl; ?>" value="<?php echo $row[csf('job_dtls_id')]; ?>">
                    <input type="hidden" id="hdnProd_dtls_id_<?php echo $sl; ?>" value="<?php echo $row[csf('prod_dtls_id')]; ?>">
                    <input type="hidden" id="hdnDeliveryDtlsId_<?php echo $sl; ?>" value="<?php echo $row[csf('del_dtls_id')]; ?>">
                    <input type="hidden" id="hdnSalesOrderId_<?php echo $sl; ?>" value="<?php echo $row[csf('sales_order_id')]; ?>">
                    <input type="hidden" id="hdnProductId_<?php echo $sl; ?>" value="<?php echo $row[csf('product_id')]; ?>">
                </td>
            </tr>
            <?php
            $sl++;
        }
        ?>
        </tbody>
    </table>
    <table width="80%" cellspacing="2" cellpadding="0" border="0">
        <tr>
            <td align="center" colspan="11" class="button_container">
                <?php
                if($reqType == 1) {
                    echo load_submit_buttons($permission, 'saveUpdateDelete', 0, 0, 'fnResetForm();', 1);
                } else {
                    echo load_submit_buttons($permission, 'saveUpdateDelete', 1, 0, 'fnResetForm();', 1);
                }
                
                ?>
            </td>
        </tr>
    </table>
</fieldset>
<?php
    ob_end_flush();
	exit();
}

if($action=='save_update_delete')
 {
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));

    if ($operation==0) {
        // save here
        $con = connect();
        $flag = 1;
        $add_comma = false;
        $field_array_mst = '';
        $data_array_mst = '';
        $field_array_dtls = '';
        $data_array_dtls = '';
        $entryForm = 400;
        $con = connect();
        $mstId = return_next_id('id', 'yd_delivery_mst', 1);
        $dtlsId = return_next_id('id', 'yd_delivery_dtls', 1);
        $hdnOrderId = str_replace("'", '', $hdnOrderId);
		
		if($db_type==0){ $insert_date_con="and YEAR(insert_date)=".date('Y',time()).""; }
		else if($db_type==2){ $insert_date_con="and TO_CHAR(insert_date,'YYYY')=".date('Y',time()).""; }
		
		 //$new_wo_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'SW', date("Y",time()), 5, "select wo_number_prefix,wo_number_prefix_num from wo_non_order_info_mst where company_name=$cbo_company_name $insert_date_con and entry_form = 484 order by id desc", "wo_number_prefix", "wo_number_prefix_num","" ));

        $new_delivery_id = explode('*', return_mrr_number( str_replace("'", '', $cbo_company_name), '', 'YDPD', date('Y',time()), 5, "select delivery_no_prefix,delivery_no_prefix_num from yd_delivery_mst where entry_form=$entryForm and company_id=$cbo_company_name $insert_date_con order by id desc", 'delivery_no_prefix', 'delivery_no_prefix_num' ));
        
        if($db_type==0) 
		{
            mysql_query("BEGIN");
        }

        $field_array_mst = 'id, entry_form, delivery_id, delivery_no_prefix, delivery_no_prefix_num, company_id, location_id, delivery_date, party_id, remarks, order_id, order_no, booking_without_order, booking_type, inserted_by, insert_date';
        $data_array_mst="(".$mstId.", ".$entryForm.", '".$new_delivery_id[0]."', '".$new_delivery_id[1]."', '".$new_delivery_id[2]."', ".$cbo_company_name.", ".$cbo_location_name.", ".$txt_delivery_date.", ".$cbo_party_name.", ".$txt_remarks.", '".$hdnOrderId."', ".$hdnOrderNo.", ".$hdnBookingWithoutOrder.", ".$hdnBookingType.", ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."')";

        
        $field_array_dtls = 'id, mst_id, job_dtls_id, prod_dtls_id, order_id, order_no, delivery_quantity, sales_order_id, sales_order_no, product_id, entry_form, inserted_by, insert_date';

        for($i=1; $i<=$total_row; $i++)
		 {
            $jobDtlsId = 'hdnDtlsId_'.$i;
            $prod_dtls_id = 'hdnProd_dtls_id_'.$i;
            $quantity = 'txtDeliveryQty_'.$i;
            $salesOrderId = 'hdnSalesOrderId_'.$i;
            $salesOrderNo = 'txtSalesOrder_'.$i;
            $productId = 'hdnProductId_'.$i;

            $data_array_dtls .= $add_comma ? ',' : ''; // if $add_comma is true, add a comma in the end of $data_array_dtls

            $data_array_dtls .= "(".$dtlsId.",".$mstId.",".$$jobDtlsId.",".$$prod_dtls_id.",'".$hdnOrderId."',".$hdnOrderNo.",".$$quantity.",".$$salesOrderId.",".$$salesOrderNo.",".$$productId.",".$entryForm.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

            $add_comma = true; // first entry is done. add a comma for next entries
            $dtlsId++; // increment details id by 1
        }

    //echo "10**insert into yd_delivery_mst(".$field_array_mst.") values ".$data_array_mst; die;
        $rID = sql_insert('yd_delivery_mst', $field_array_mst, $data_array_mst, 0);
        
        $flag = ($flag && $rID);    // return true if $flag is true and mst table insert is successful

        // echo $flag, $rID;die;
      //echo "10**insert into yd_delivery_dtls(".$field_array_dtls.") values ".$data_array_dtls; die;
        $rID2 = sql_insert('yd_delivery_dtls', $field_array_dtls, $data_array_dtls, 0);

        // echo '10**'.$rID;die;

        $flag = ($flag && $rID2);   // return true if $flag is true and dtls table insert is successful

        if($db_type==0) {
            if($flag) {
                mysql_query("COMMIT");              
                echo '0**'.$new_delivery_id[0].'**'.$mstId;
            } else {
                mysql_query("ROLLBACK");
                echo '10**'.$hdnOrderId;
            }
        }
        else if($db_type==2) {
            if($flag) {
                oci_commit($con);
                echo '0**'.$new_delivery_id[0].'**'.$mstId;
            } else {
                oci_rollback($con);
                echo '10**'.$hdnOrderId;
            }
        }

        disconnect($con);
        die;
    }

    else if($operation == 1) {
        // update here
        $flag = 1;
        $id_arr = array();
        $con = connect();
        $hdn_update_id = str_replace("'", '', $hdn_update_id);
        $txt_delivery_id = str_replace("'", '', $txt_delivery_id);

        if($db_type==0) mysql_query("BEGIN");

        $field_array_mst = 'location_id*party_id*remarks*updated_by*update_date';
        $data_array_mst=''.$cbo_location_name.'*'.$cbo_party_name.'*'.$txt_remarks.'*'.$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

        $field_array_dtls = 'delivery_quantity*updated_by*update_date';

        for($i = 1; $i <= $total_row; $i++) {
            $jobDtlsId = 'hdnDtlsId_'.$i;
            $prod_dtls_id = 'hdnProd_dtls_id_'.$i;
            $quantity = 'txtDeliveryQty_'.$i;
            $deliveryDtlsId = 'hdnDeliveryDtlsId_'.$i;
            $data_array_dtls[str_replace("'", '', $$deliveryDtlsId)] = explode('*',(''.$$quantity.'*'.$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
            $id_arr[]=str_replace("'", '', $$deliveryDtlsId);
        }
     
        // echo sql_update('yd_delivery_mst', $field_array_mst, $data_array_mst, 'id', $hdn_update_id, 0);
        $rID = sql_update('yd_delivery_mst', $field_array_mst, $data_array_mst, 'id', $hdn_update_id, 0);

        // echo $rID;die;

        $flag = ($flag && $rID);    // return true if $flag is true and mst table update is successful

        // echo '10**' . bulk_update_sql_statement('yd_delivery_dtls', 'id', $field_array_dtls, $data_array_dtls, $id_arr);die;

        $rID2 = execute_query(bulk_update_sql_statement('yd_delivery_dtls', 'id', $field_array_dtls, $data_array_dtls, $id_arr), 1);

        $flag = ($flag && $rID2);   // return true if $flag is true and dtls table update is successful

        if($db_type==0) {
            if($flag) {
                mysql_query('COMMIT');
                echo '1**'.$txt_delivery_id.'**'.$hdn_update_id;
            } else {
                mysql_query('ROLLBACK');
                echo '6**'.$txt_delivery_id.'**'.$hdn_update_id;
            }
        }
        else if($db_type==2) {
            if($flag) {
                oci_commit($con);
                echo '1**'.$txt_delivery_id.'**'.$hdn_update_id;
            } else {
                oci_rollback($con);
                echo '6**'.$txt_delivery_id.'**'.$hdn_update_id;
            }
        }

        disconnect($con);
        die;
    }

    else if($operation == 2) {
        echo '7**';
    }

    exit();
}

?>