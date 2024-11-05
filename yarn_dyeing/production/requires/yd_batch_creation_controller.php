<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];

/************************************ Start*************************************************/

if ($action=='load_drop_down_location') {
    echo create_drop_down( 'cbo_location_name', 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down('requires/yd_batch_creation_controller', document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_machine', 'machine_td' );" ); 
}

if ($action=='load_drop_down_machine') {
    $data= explode('_', $data);

    if($data[0]==0 || $data[1]==0) {
        echo create_drop_down('cbo_machine', 150, $blank_array, '', 1, '-- Select Machine --', $selected, '');
    }
    else {
        if($db_type==2) {
            echo create_drop_down('cbo_machine', 150, "select id,machine_no || '-' || brand as machine_name from lib_machine_name where company_id='$data[0]' and location_id='$data[1]' and status_active=1 and is_deleted=0 and is_locked=0 and category_id=36 order by seq_no", 'id,machine_name', 1, '-- Select Machine --', $selected, '','');
        }
        else if($db_type==0) {
            echo create_drop_down('cbo_machine', 150, "select id,concat(machine_no,'-',brand) as machine_name from lib_machine_name where company_id='$data[0]' and location_id='$data[1]' and status_active=1 and is_deleted=0 and is_locked=0 and category_id=36 order by seq_no", 'id,machine_name', 1, '-- Select Machine --', $selected, '','');
        }
    }
}

if ($action == "batch_no_creation") 
{
	
	$batch_no_creation = '';
 	$sql = sql_select("select variable_list, batch_no_creation, batch_maintained,yd_batch_no_creation from variable_settings_production where company_name=$data and variable_list in (24) and status_active=1 and is_deleted=0");
	foreach ($sql as $row) 
	{
 	  $batch_no_creation = $row[csf('yd_batch_no_creation')];
 	}
 	if ($batch_no_creation != 1) $batch_no_creation = 0;
 	echo "document.getElementById('batch_no_creation').value 				= '" . $batch_no_creation . "';\n";
	echo "$('#txtBatchNo').val('');\n";
	echo "$('#update_id').val('');\n";
	if ($batch_no_creation == 1) {
		echo "$('#txtBatchNo').attr('readonly','readonly');\n";
	} else {
		echo "$('#txtBatchNo').removeAttr('readonly','readonly');\n";
	}

	exit();
}


if ($action == 'batch_color_popup') 
{
    echo load_html_head_contents('Job Popup Info', '../../../', 1, 0, $unicode, '', '');
?>
<style>
    tr.selected-row {
        background: yellow;
    }
    #tbl_yd_list td {
        text-align: center;
        word-wrap: break-word;
    }
</style>
<script>
    var selectedIdArr = new Array();
    var selectedDtlsId = null;
    var selectedMstId = null;
    var selectedColorId = null;

    window.onload = function() {
        document.getElementById('cbo_company_name').removeAttribute('disabled');
    }

    function setBlank() {
        selectedIdArr = new Array();
        selectedDtlsId = null;
        selectedMstId = null;
        selectedColorId = null;
    }

    function js_set_value(data) {
        var data = data.split('**');
        var mstId = data[0];
        var orderId = data[1];
        var rowId = data[2];
        var ydJob = data[3];
        var colorId = data[4];

        manageValues(orderId, rowId, ydJob, colorId, mstId);
    }

    function closePopup() {
        var idStr = selectedIdArr.join('_');
        document.getElementById('hdnYdMstId').value = selectedMstId;
        document.getElementById('hdnYdDtlsIds').value = idStr;
        
        parent.colorPopup.hide();
    }

    function manageValues(orderId, rowId, jobId, colorId, mstId) {
        if(selectedDtlsId != null && selectedDtlsId != jobId) {
            alert('Select same job');
            return;
        }

        if(selectedColorId != null && selectedColorId != colorId) {
            alert('Select same color');
            return;
        }

        selectedDtlsId = jobId;
        selectedColorId = colorId;
        selectedMstId = mstId;

        manageIdArr(orderId, rowId);

        // toggleColor(rowId);
    }

    function toggleColor(rowId)
	 {
        var row = document.getElementById(rowId);

        row.classList.toggle('selected-row');
    }

    function manageIdArr(orderId, rowId) 
	{
        // check if this yd already in the array
        var index = selectedIdArr.indexOf(orderId);

        // push to the array if not in the array
        if(index == -1) 
		{
            selectedIdArr.push(orderId);
        } 
		else 
		{
            // remove from the array if found
            selectedIdArr.splice(index, 1);
        }

        // if array is empty, jobid and colorid should be empty too
        if(!selectedIdArr.length)
		 {
            selectedDtlsId = null;
            selectedColorId = null;
            selectedMstId = null;
        }
        // console.log(selectedIdArr);
        // console.log(selectedIdArr.length);
        // selectedIdArr.push(orderId);
        toggleColor(rowId);
    }

</script>
</head>
<body>
<div align="center" style="width:100%;" >
    <form name="batchColorForm" id="batchColorForm" autocomplete="off">
        <table width="580" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead> 
                <tr>
                    <th colspan="8"><?php echo create_drop_down('cbo_search_type', 140, $string_search_type, '', 1, '-- Searching Type --'); ?></th>
                </tr>
                <tr>
                    <th width="140" class="must_entry_caption">Company Name</th>
                    <th width="140" class="must_entry_caption">Within Group</th>
                    <th width="140">YD Job No</th>
                    <th width="140">WO No</th>
                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width: 80px" /></th>
                </tr>
            </thead>
            <tbody>
                <tr class="general">
                    <td>
                        <?php
                        echo create_drop_down('cbo_company_name', 140, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name", 'id,company_name', 1, '-- Select Company --', $data, '', 1); ?>
                    </td>
                    <td>
                        <?php echo create_drop_down( "cbo_within_group", 150, $yes_no,"", 0, "-- Select --", 0, "fnc_load_party(1,this.value);fnc_load_wo(this.value);" ); ?>
                    </td>
                    <td>
                        <input class="text_boxes" type="text" name="txt_job_no" id="txt_job_no" style="width: 140px;">
                    </td>
                    <td>
                        <input class="text_boxes" type="text" name="txt_workorder_no" id="txt_workorder_no" style="width: 140px;">
                    </td>
                    <td align="center">
                        <input type="hidden" id="selected_job"><?php $data=explode('_',$data); ?>
                        <input type="hidden" id="hdnYdMstId">
                        <input type="hidden" id="hdnYdDtlsIds">
                        <input type="button" name="showBtn" class="formbutton" value="Show" onClick="show_list_view(document.getElementById('cbo_search_type').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_workorder_no').value+'_'+document.getElementById('cbo_within_group').value, 'create_batch_creation_list_view', 'search_div', 'yd_batch_creation_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width: 80px;" />
                    </td>
                </tr>
            </tbody>
        </table>    
        </form>
        <div id="search_div"></div>
    </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?php
exit();
}

if($action == 'batch_no_popup') 
{
    echo load_html_head_contents('Job Popup Info', '../../../', 1, 0, $unicode, '', '');
    ?>
    <script>
        window.onload = function() {
            document.getElementById('cbo_company_name').removeAttribute('disabled');
        }

        function js_set_value(id) {
            document.getElementById('hdnBatchMstId').value = id;
            parent.batchPopup.hide();
        }
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
        <form name="batchColorForm" id="batchColorForm" autocomplete="off">
            <table width="650" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                <thead> 
                    <tr>
                        <th colspan="8"><?php echo create_drop_down('cbo_search_type', 140, $string_search_type, '', 1, '-- Searching Type --'); ?></th>
                    </tr>
                    <tr>
                        <th width="140" class="must_entry_caption">Company Name</th>
                        <th width="140">Batch Serial No</th>
                        <th width="140">Batch Number</th>
                        <th width="140">Job No/Sales order no</th>
                        <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width: 80px" /></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="general">
                        <td>
                            <?php
                            echo create_drop_down('cbo_company_name', 140, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name", 'id,company_name', 1, '-- Select Company --', $data, '', 1); ?>
                        </td>
                        <td>
                            <input class="text_boxes" type="text" name="txt_batch_no" id="txt_batch_no" style="width: 140px;">
                        </td>
                        <td>
                            <input class="text_boxes" type="text" name="txt_batch_num" id="txt_batch_num" style="width: 140px;">
                        </td>
                        <td>
                            <input class="text_boxes" type="text" name="txt_job_no" id="txt_job_no" style="width: 140px;">
                        </td>
                        <td align="center">
                            <input type="hidden" id="hdnBatchMstId">
                            <input type="button" name="showBtn" class="formbutton" value="Show" onClick="show_list_view(document.getElementById('cbo_search_type').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_batch_no').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_batch_num').value, 'create_batch_search_list_view', 'search_div', 'yd_batch_creation_controller', 'setFilterGrid(\'list_view\',-1)')" style="width: 80px;" />
                        </td>
                    </tr>
                </tbody>
            </table>
            </form>
            <div id="search_div" style="margin-top: 10px;"></div>
        </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
<?php
    exit();
}

if($action == 'create_batch_search_list_view') {
    // echo $data;die;
    $data=explode('_',$data);
    $search_type = $data[0];
    $condition = '';
    $color_arr = return_library_array('select id, color_name from lib_color where status_active=1 and is_deleted=0', 'id', 'color_name');
    $party_arr = return_library_array('select id, other_party_name from lib_other_party where status_active=1 and is_deleted=0', 'id', 'other_party_name');
    // echo $data[0];die;

    if($data[1]) {
        $condition.=" and a.company_id=$data[1]";
    } else {
        echo "<h3 style='margin-top: 10px;'>Please Select Company First.</h3>"; die;
    }

    // $list_arr = array(3 => $color_arr);

    if($data[0]==0 || $data[0]==4) { // no searching type or contents
        if ($data[2]!="") $condition.=" and a.yd_batch_id like '%$data[2]%'";
        if ($data[3]!="") $condition.=" and b.yd_job like '%$data[3]%'";
        if ($data[4]!="") $condition.=" and a.batch_number like '%$data[4]%'";
    } else if($data[0]==1) { // exact
        if ($data[2]!="") $condition.=" and a.yd_batch_id = '$data[2]'";
        if ($data[3]!="") $condition.=" and b.yd_job ='$data[3]'";
        if ($data[4]!="") $condition.=" and a.batch_number = '$data[4]'";
    } else if($data[0]==2) { // Starts with
        if ($data[2]!="") $condition.=" and a.yd_batch_id like '$data[2]%'";
        if ($data[3]!="") $condition.=" and b.yd_job like '$data[3]%'";
        if ($data[4]!="") $condition.=" and a.batch_number like '$data[4]%'";
    } else if($data[0]==3) { // Ends with
        if ($data[2]!="") $condition.=" and a.yd_batch_id like '%$data[2]'";
        if ($data[3]!="") $condition.=" and b.yd_job like '%$data[3]'";
        if ($data[4]!="") $condition.=" and a.batch_number like '%$data[4]'";
    }

    $sql = "select a.id, a.yd_batch_id, a.batch_number, b.yd_job
            from yd_batch_mst a, yd_ord_mst b
            where a.is_deleted=0 and a.status_active=1 $condition and a.yd_job_id=b.id order by id DESC";

    // echo $sql;

    echo create_list_view('list_view', 'Batch Serial No, Batch Number,YD Job', '140,140', '500', '300', 0, $sql, 'js_set_value', 'id', '', 1, '0,0,0', '', 'yd_batch_id,batch_number,yd_job', '','', '0,0,0,0');

    exit();

}

if($action == 'create_batch_creation_list_view') {
    // echo $data;die;
    $data=explode('_',$data);
    $search_type = $data[0];
    $condition = '';
    $color_arr = return_library_array('select id, color_name from lib_color where status_active=1 and is_deleted=0', 'id', 'color_name');
    //$party_arr = return_library_array('select id, other_party_name from lib_other_party where status_active=1 and is_deleted=0', 'id', 'other_party_name');
    if($data[4]==1)
    {
        $party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
        // $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    }
    else
    {
        
        $party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    }
    // echo $data[0];die;

    if($data[1]) {
        $condition.=" and a.company_id=$data[1]";
    } else {
        echo "<h3 style='margin-top: 10px;'>Please Select Company First.</h3>"; die;
    }

    if($data[4]) {
        $condition.=" and a.within_group=$data[4]";
    } else {
        echo "<h3 style='margin-top: 10px;'>Please Select Within Group First.</h3>"; die;
    }
    
    // $list_arr = array(3 => $color_arr);

    if($data[0]==0 || $data[0]==4) { // no searching type or contents
        if ($data[2]!="") $condition.=" and a.yd_job like '%$data[2]%'";
        if ($data[3]!="") $condition.=" and a.order_no like '%$data[3]%'";
    } else if($data[0]==1) { // exact
        if ($data[2]!="") $condition.=" and a.yd_job = '$data[2]'";
        if ($data[3]!="") $condition.=" and a.order_no ='$data[3]'";
    } else if($data[0]==2) { // Starts with
        if ($data[2]!="") $condition.=" and a.yd_job like '$data[2]%'";
        if ($data[3]!="") $condition.=" and a.order_no like '$data[3]%'";
    } else if($data[0]==3) { // Ends with
        if ($data[2]!="") $condition.=" and a.yd_job like '%$data[2]'";
        if ($data[3]!="") $condition.=" and a.order_no like '%$data[3]'";
    }

    $sql_ord = "select b.id, a.id as mst_id, a.yd_job, b.order_no, b.yd_color_id, a.party_id
                from yd_ord_mst a, yd_ord_dtls b
                where a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $condition and a.id = b.mst_id  
                order by id DESC";

    // echo $sql_ord;

    $sql_ord_res = sql_select($sql_ord);

    foreach($sql_ord_res as $row) {
        $ord_arr[$row[csf('id')]]['mst_id'] = $row[csf('mst_id')];
        $ord_arr[$row[csf('id')]]['order_id'] = $row[csf('id')];
        $ord_arr[$row[csf('id')]]['yd_job'] = $row[csf('yd_job')];
        $ord_arr[$row[csf('id')]]['order_no'] = $row[csf('order_no')];
        $ord_arr[$row[csf('id')]]['party_id'] = $row[csf('party_id')];
        $ord_arr[$row[csf('id')]]['yd_color_id'] = $row[csf('yd_color_id')];
    }
    // echo "<pre>"; print_r($ord_arr);die;

    $sql_soft_coning = sql_select("SELECT a.DELIVERY_QUANTITY, a.JOB_DTLS_ID as id from yd_delivery_dtls a, yd_ord_dtls b where a.JOB_DTLS_ID=b.id and a.ENTRY_FORM=400 and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0");
    foreach($sql_soft_coning as $row) {
        // $ord_arr[$row[csf('id')]]['delivery_quantity'] += $row[csf('delivery_quantity')];
        $soft_cone_arr[$row[csf('id')]]['delivery_quantity'] += $row[csf('delivery_quantity')];
    }

    // $sql_soft_coning = sql_select("SELECT QUANTITY as soft_con_qty,JOB_DTLS_ID as id from yd_production_dtls where ENTRY_FORM=397 and STATUS_ACTIVE=1 and IS_DELETED=0");
    $sql_batch = sql_select("SELECT QUANTITY,YD_JOB_DTLS_ID as id from yd_batch_dtls a, yd_ord_dtls b where a.YD_JOB_DTLS_ID=b.id and a.ENTRY_FORM=398 and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 and a.IS_DELETED=0");
    foreach($sql_batch as $row) {
        // $ord_arr[$row[csf('id')]]['quantity'] += $row[csf('quantity')];
        $prev_batch_arr[$row[csf('id')]]['quantity'] += $row[csf('quantity')];
    }

    // echo "<pre>";print_r($ord_arr);die;  
    
    ob_start();
?>
<table rules="all" class="rpt_table" width="98%" cellspacing="0" cellpadding="0" border="1">
    <thead>
        <tr>
            <th width="5%">SL</th>
            <th width="23%">YD Job No</th>
            <th width="10%">WO No</th>
            <th width="20%">Party</th>
            <th width="12%">Color</th>
            <th width="10%">Soft Coning Prod. Qty</th>
            <th width="10%">Prev. Batch Qty</th>
            <th width="">Balance Batch Qty</th>
        </tr>
    </thead>
</table>
<div style="width:98%; margin: 0 auto;max-height:350px;overflow-y:scroll" id="tbl_yd_list">
    <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list_search" style="width: 100%;">
        <tbody>
    <?php
    $sl = 1;
    foreach ($ord_arr as $row) 
	{
        if($sl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
        $rowId = 'tr_'.$sl;
        $mstId = $row['mst_id'];
        $data = $mstId.'**'.$row['order_id'].'**'.$rowId.'**'.$row['yd_job'].'**'.$row['yd_color_id'];
        // echo "<pre>";print_r($row);echo $row['order_id'];die; 
        ?>
        
        <tr bgcolor="<?php echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="<?php echo $rowId; ?>" onClick="js_set_value('<?php echo $data; ?>')">
            <td width="5%"><?php echo $sl; ?></td>
            <td width="23%"><?php echo $row['yd_job']; ?></td>
            <td width="10%"><?php echo $row['order_no']; ?></td>
            <td width="20%"><?php echo $party_arr[$row['party_id']]; ?></td>
            <td width="12%"><?php echo $color_arr[$row['yd_color_id']]; ?></td>
            <td width="10%"><?php echo number_format($soft_cone_arr[$row['order_id']]['delivery_quantity'],2); ?></td>
            <td width="10%"><?php echo number_format($prev_batch_arr[$row['order_id']]['quantity'],2); ?></td>
            <td width=""><?php echo number_format($soft_cone_arr[$row['order_id']]['delivery_quantity']-$prev_batch_arr[$row['order_id']]['quantity'],2); ?></td>
        </tr>
        <?php
        $sl++;
    }
    ?>
        </tbody>
    </table>
</div>
    <div style="margin-top: 10px;">
        <input type="button" class="formbutton" id="close" style="width:80px" onClick="closePopup();" value="Close">
    </div>
    <?php    
    ob_end_flush();
    exit();
}

if($action == 'populate_mst_data_from_search_popup') {
    $data = explode('**', $data);
    $sql;
    $reqType = $data[0];
    $mstId = $data[1];
    $ordDtlsId = $reqType == 1 ? explode('_', $data[2]) : '';

    // $dtlsIdArr = explode('_', $mstId);

    $color_arr = return_library_array('select id, color_name from lib_color where status_active=1 and is_deleted=0', 'id', 'color_name');

    if($reqType == 1) {
        $sql = "select a.company_id, a.order_id, a.order_no, a.booking_without_order, a.booking_type, b.id as dtls_id, a.yd_job, b.yd_color_id,b.item_color_id from yd_ord_mst a, yd_ord_dtls b
            where a.is_deleted=0 and a.status_active=1 and b.id=$ordDtlsId[0] and a.id=b.mst_id";
    }
    else {
        $sql = "select id, yd_job_id, order_id, order_no, booking_without_order, booking_type, yd_batch_id, company_id, location_id, batch_color_id, batch_color_range, batch_number, batch_against, batch_weight, extention_no, duration_req, process_id, machine_id, batch_date, remarks
            from yd_batch_mst
            where id=$mstId";
    }

    // echo $sql;

    $result = sql_select($sql);

    if($reqType == 1) 
	{
        echo "document.getElementById('txtBatchColor').value = '".$color_arr[$result[0][csf('yd_color_id')]]."';\n";
        echo "document.getElementById('cbo_company_name').value = '".$result[0][csf('company_id')]."';\n";
        echo "document.getElementById('hdnJobMstId').value = '".$mstId."';\n";
        echo "document.getElementById('hdnJobDtlsId').value = '".$result[0][csf('dtls_id')]."';\n";
        echo "document.getElementById('hdnColorId').value = '".$result[0][csf('yd_color_id')]."';\n";
        echo "document.getElementById('hdnOrderId').value = '".$result[0][csf('order_id')]."';\n";
        echo "document.getElementById('hdnOrderNo').value = '".$result[0][csf('order_no')]."';\n";
        echo "document.getElementById('hdnBookingWithoutOrd').value = '".$result[0][csf('booking_without_order')]."';\n";
        echo "document.getElementById('hdnBookingType').value = '".$result[0][csf('booking_type')]."';\n";

       // echo "document.getElementById('txtBatchSerialNo').value = '';\n";
        //echo "document.getElementById('cbo_batch_against').value = '0';\n";
        echo "document.getElementById('txtColorRange').value = '".$result[0][csf('item_color_id')]."';\n";
       // echo "document.getElementById('txtBatchNo').value = '';\n";
       // echo "document.getElementById('txtBatchWeight').value = '';\n";
        //echo "document.getElementById('txtExtnNo').value = '';\n";
       // echo "document.getElementById('txtBatchDate').value = '';\n";
      //  echo "document.getElementById('cbo_process').value = '0';\n";
       // echo "document.getElementById('txtDurationReq').value = '';\n";
       // echo "document.getElementById('cbo_location_name').value = '0';\n";
       // echo "document.getElementById('cbo_machine').value = '0';\n";
        //echo "document.getElementById('txtRemarks').value = '';\n";
    } 
	else 
	{
        echo "document.getElementById('txtBatchSerialNo').value = '".$result[0][csf('yd_batch_id')]."';\n";
        echo "document.getElementById('txtBatchColor').value = '".$color_arr[$result[0][csf('batch_color_id')]]."';\n";
        echo "document.getElementById('cbo_company_name').value = '".$result[0][csf('company_id')]."';\n";
        echo "document.getElementById('cbo_batch_against').value = '".$result[0][csf('batch_against')]."';\n";
        echo "document.getElementById('txtColorRange').value = '".$result[0][csf('batch_color_range')]."';\n";
        echo "document.getElementById('txtBatchNo').value = '".$result[0][csf('batch_number')]."';\n";
        echo "document.getElementById('txtBatchWeight').value = '".$result[0][csf('batch_weight')]."';\n";
        echo "document.getElementById('txtExtnNo').value = '".$result[0][csf('extention_no')]."';\n";
        echo "document.getElementById('txtBatchDate').value = '".change_date_format($result[0][csf('batch_date')], "dd-mm-yyyy", "-")."';\n";
        echo "document.getElementById('cbo_process').value = '".$result[0][csf('process_id')]."';\n";
        echo "document.getElementById('txtDurationReq').value = '".$result[0][csf('duration_req')]."';\n";
        echo "load_drop_down('requires/yd_batch_creation_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location', 'location_td' );";
        echo "document.getElementById('cbo_location_name').value = '".$result[0][csf('location_id')]."';\n";
        echo "load_drop_down('requires/yd_batch_creation_controller', document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_location_name').value, 'load_drop_down_machine', 'machine_td' );";
        echo "document.getElementById('cbo_machine').value = '".$result[0][csf('machine_id')]."';\n";
        echo "document.getElementById('txtRemarks').value = '".$result[0][csf('remarks')]."';\n";

        echo "document.getElementById('hdnJobMstId').value = '".$mstId."';\n";
        echo "document.getElementById('hdnColorId').value = '".$result[0][csf('batch_color_id')]."';\n";
        echo "document.getElementById('hdnUpdateId').value = '".$mstId."';\n";
        echo "document.getElementById('hdnOrderId').value = '".$result[0][csf('order_id')]."';\n";
    }
    
    exit();
}

if($action == 'populate_dtls_data_from_search_popup') {
    $data = explode('**', $data);
    $reqType = $data[0];
    $sl = 1;
    $dtlsIds;
    $dtlsIdArr;
    $dtlsIdStr;
    $sql;
    $batchMstId;

	if ($reqType == 1)
	{
		$dtlsIds = $data[1];
		$dtlsIdArr = explode('_', $dtlsIds);
		$dtlsIdStr = implode(',', $dtlsIdArr);
	} 
	else 
	{
		$batchMstId = $data[1];
	}
	
	$count_arr = return_library_array("Select id, yarn_count from  lib_yarn_count where  status_active=1", 'id', 'yarn_count');
	$comp_arr = return_library_array("select id,composition_name from lib_composition_array where is_deleted=0 and status_active=1", 'id', 'composition_name');
	
 	

		if ($reqType == 1) 
		{    // new entry
			// die("1");
            // $sql = "select id,id as job_dtls_id ,order_id, order_no, style_ref, lot, count_id, yarn_type_id, yarn_composition_id, sales_order_id, 
			// sales_order_no,product_id
			// from yd_ord_dtls
			// where id in ($dtlsIdStr) and status_active=1 and is_deleted=0";
            $sql= "SELECT a.id,b.id as job_dtls_id, a.order_id, a.bobbin_type, a.winding_qty, a.quantity, b.sales_order_no, b.style_ref, b.lot, b.count_id,b.yarn_type_id, b.yarn_composition_id
            FROM yd_production_dtls a, yd_ord_dtls b
            where b.id in ($dtlsIdStr) and b.id=a.job_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
            // echo $sql;
		} 
		else 
		{    // update mode
            // die('2');
			$sql = "select a.id,b.id as job_dtls_id, a.order_id, a.bobbin_type, a.winding_pckg_qty, a.quantity, b.sales_order_no, b.style_ref, b.lot, 
			b.count_id,b.yarn_type_id, b.yarn_composition_id
			from yd_batch_dtls a, yd_ord_dtls b
			where a.mst_id=$batchMstId and a.status_active=1 and a.yd_job_dtls_id=b.id and a.is_deleted=0";

            
		}
		$result = sql_select($sql);
		
		 
		 
		 
		 
		 
		 if ($reqType == 1) 
		{    // new entry
			$batch_sql = "select a.id,b.id as job_dtls_id, a.order_id, a.bobbin_type, a.winding_pckg_qty, a.quantity, b.sales_order_no, b.style_ref, b.lot, 
		b.count_id, b.yarn_type_id, b.yarn_composition_id
		from yd_batch_dtls a, yd_ord_dtls b
		where   a.status_active=1 and a.yd_job_dtls_id=b.id and a.is_deleted=0";
		} 
		else 
		{    // update mode
			$batch_sql = "select a.id,b.id as job_dtls_id, a.order_id, a.bobbin_type, a.winding_pckg_qty, a.quantity, b.sales_order_no, b.style_ref, b.lot, 
		b.count_id, b.yarn_type_id, b.yarn_composition_id
		from yd_batch_dtls a, yd_ord_dtls b
		where a.mst_id!=$batchMstId and a.status_active=1 and a.yd_job_dtls_id=b.id and a.is_deleted=0";
		}
		 
 		 $prodData=sql_select($batch_sql);
 		$prev_batch_data_arr=array(); //$recipe_prod_id_arr=array(); $product_data_arr=array();
		foreach ($prodData as $row)
		{
			$prev_batch_data_arr[$row[csf('job_dtls_id')]]['batch_quantity']+=$row[csf('quantity')];
		}
		
		unset($prodData);
	 
	
	    //$job_dtls_ids=implode(",",array_unique(explode(",",chop($result[0][csf('job_dtls_id')],","))));
		$production_sql= "select b.job_dtls_id,sum(quantity) as total_production   from yd_production_mst a, yd_production_dtls b where a.id=b.mst_id and a.entry_form=397 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by b.job_dtls_id"; 
		$production_result = sql_select($production_sql);
		$total_production_arr=array();
		foreach( $production_result as $row )
		{
			$total_production_arr[$row[csf('job_dtls_id')]]['total_production']+=$row[csf('total_production')];
		}
		
		// echo $sql;die;

   

    ob_start();
?>
<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
    <thead>
        <th>Sales order no.</th>
        <th>Style No.</th>
        <th>Lot</th>
        <th>Count</th>
        <th>Yarn Type</th>
        <th>Yarn Composition</th>
        <th>Bobbin Type</th>
        <th>Winding Package Qty</th>
        <th>Batch QTY</th>
    </thead>
    <tbody id="batch-rows">
<?php
  $total_production_qty==0;
    foreach ($result as $row) 
	{
		  $total_production_qty=$total_production_arr[$row[csf('job_dtls_id')]]['total_production'];
		  $preBatch_qty=$prev_batch_data_arr[$row[csf('job_dtls_id')]]['batch_quantity'];
		
?>
        <tr class="general">
            <td>
                <input type="text" readonly class="text_boxes" id="txtOrderNo_<?php echo $sl; ?>" value="<?php echo $row[csf('sales_order_no')]; ?>">
            </td>
            <td>
                <input type="text" readonly class="text_boxes" id="txtOrderNo_<?php echo $sl; ?>" value="<?php echo $row[csf('style_ref')]; ?>">
            </td>
            <td>
                <input type="text" readonly class="text_boxes" id="txtLot_<?php echo $sl; ?>"  value="<?php echo $row[csf('lot')]; ?>">
            </td>
            <td>
                <input type="text" readonly class="text_boxes" id="txtCount_<?php echo $sl; ?>" value="<?php echo $count_arr[$row[csf('count_id')]]; ?>">
            </td>
            <td>
                <input type="text" readonly class="text_boxes" id="txtYarnType_<?php echo $sl; ?>" value="<?php echo $yarn_type[$row[csf('yarn_type_id')]]; ?>">
            </td>
            <td>
                <input type="text" readonly class="text_boxes" id="txtYarnComp_<?php echo $sl; ?>" value="<?php echo $comp_arr[$row[csf('yarn_composition_id')]]; ?>">
            </td>
            <td>
                <input type="text" readonly class="text_boxes" id="txtBobbin_<?php echo $sl; ?>" value="<?php echo $row[csf('bobbin_type')]; ?>">
            </td>
            <td>
                <input type="text" class="text_boxes_numeric" id="txtWindingPack_<?php echo $sl; ?>" value="<?php echo $row[csf('winding_pckg_qty')]; ?>">
            </td>
            <td>
                <input type="text" class="text_boxes_numeric" id="txtBatchQty_<?php echo $sl; ?>" onKeyUp="calculateBatchQty(<?php echo $sl; ?>);" value="<?php echo $row[csf('quantity')]; ?>">
                <input type="hidden" id="hdnDtlsId_<?php echo $sl; ?>" value="<?php echo $row[csf('job_dtls_id')]; ?>">
                <input type="hidden" id="hdnOrderId_<?php echo $sl; ?>" value="<?php echo $row[csf('order_id')]; ?>">
                <input type="hidden" id="hdnOrderNo_<?php echo $sl; ?>" value="<?php echo $row[csf('order_no')]; ?>">
                <input type="hidden" id="hdnSalesOrderId_<?php echo $sl; ?>" value="<?php echo $row[csf('sales_order_id')]; ?>">
                <input type="hidden" id="hdnSalesOrderNo_<?php echo $sl; ?>" value="<?php echo $row[csf('sales_order_no')]; ?>">
                <input type="hidden" id="hdnProductId_<?php echo $sl; ?>" value="<?php echo $row[csf('product_id')]; ?>">
                <input type="hidden" id="hdnProducqty_<?php echo $sl; ?>" value="<?php echo $total_production_qty; ?>">
                <input type="hidden" id="hdnPrebatchqty_<?php echo $sl; ?>" value="<?php echo $preBatch_qty; ?>">
            </td>
        </tr>
<?php
    $sl++;
    }
?>
        <tfoot class="tbl_bottom">
            <tr>
                <td colspan="8" align="right">Sum: </td>
                <td>
                    <input type="text" class="text_boxes_numeric" id="txtTotBatchQty" readonly>
                </td>
            </tr>
            <tr style="margin-top: 10px;">
                <td colspan="9" style="text-align: center;">
                    <?php
                        echo load_submit_buttons($permission, 'saveUpdateDelete', 0, 0, '', 1);
                        // // load_submit_buttons($permission, $sub_func, $is_update, $is_show_print, $refresh_function, $btn_id, $is_show_approve)
                    ?>
                    <input type="button" id="Print_1" value="Y/D Batch Card" class="formbutton" onClick="fnc_yd_batch_creation(1)" style="width: 110px;">
                    <input type="button" id="Print_2" value="Y/D Batch Card 2" class="formbutton" onClick="fnc_yd_batch_creation(2)" style="width: 120px;">
                    <input type="button" id="Print_3" value="Y/D Batch Card 3" class="formbutton" onClick="fnc_yd_batch_creation(3)" style="width: 120px;">
                    <br>
                </td>
            </tr>
        </tfoot>
    </tbody>
</table>
<?php
    ob_end_flush();
    exit();
}

if($action == 'save_update_delete') 
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));

    if ($operation==0) 
	{
		
        // save here
        $con = connect();
        $flag = 1;
        $add_comma = false;
        $field_array_mst = '';
        $data_array_mst = '';
        $field_array_dtls = '';
        $data_array_dtls = '';
        $entryForm = 398;
        $con = connect();
        $mstId = return_next_id('id', 'yd_batch_mst', 1);
        $dtlsId = return_next_id('id', 'yd_batch_dtls', 1);
		
		if($db_type==0){ $insert_date_con="and YEAR(insert_date)=".date('Y',time()).""; }
		else if($db_type==2){ $insert_date_con="and TO_CHAR(insert_date,'YYYY')=".date('Y',time()).""; }

        $new_batch_id = explode('*',return_mrr_number( str_replace("'", '', $cbo_company_name), '', 'YDBC', date('Y',time()), 5, "select yd_batch_id_prefix,yd_batch_id_prefix_num from yd_batch_mst where entry_form=$entryForm and company_id=$cbo_company_name $insert_date_con order by id desc", 'yd_batch_id_prefix', 'yd_batch_id_prefix_num' ));
		   if (str_replace("'", "", $txt_ext_no) != "" || $db_type == 0)
			{
				$extention_no_cond  = "extention_no=$txtExtnNo";
			
			}
			else 
			{
				$extention_no_cond  = "extention_no is null";
			}
			
        
        if($db_type==0) 
		{
            mysql_query("BEGIN");
        }
		
		
			
			 //$company=str_replace("'", '', $cbo_company_name);
			// $batch_no_creation=return_field_value("yd_batch_no_creation","variable_settings_production","company_name='$company' and variable_list=24 and is_deleted=0 and status_active=1");
			$batch_no_creation 	= str_replace("'", "", $batch_no_creation);
	
			if ($batch_no_creation == 1)
			{
				//$txt_batch_number = "'" . $id . "'";
				$txtBatchNo = "'" .$new_batch_id[0]. "'";
			}
			else
			{
				/*
				|--------------------------------------------------------------------------
				| pro_batch_create_mst
				| duplicate checking
				|--------------------------------------------------------------------------
				|
				*/
				 if (is_duplicate_field("batch_number", "yd_batch_mst", "batch_number=$txtBatchNo and $extention_no_cond and status_active=1 and is_deleted=0") == 1)
				{
					echo "11**0";
					disconnect($con);
					die;
				} 
				$txtBatchNo = $txtBatchNo;
			}
			
		 // echo "10**".$txtBatchNo; die;

        $field_array_mst = 'id, entry_form, order_id, yd_job_id, yd_batch_id, yd_batch_id_prefix, yd_batch_id_prefix_num, company_id, location_id, batch_number, batch_color_id, batch_against, batch_color_range, batch_weight, extention_no, batch_date, process_id, duration_req, machine_id, remarks, booking_without_order, booking_type, inserted_by, insert_date';
        $data_array_mst="(".$mstId.",".$entryForm.",".$hdnOrderId.",".$hdnJobMstId.",'".$new_batch_id[0]."', '".$new_batch_id[1]."', '".$new_batch_id[2]."', ".$cbo_company_name.", ".$cbo_location_name.", ".$txtBatchNo.", ".$hdnColorId.", ".$cbo_batch_against.", ".$txtColorRange.", ".$txtBatchWeight.", ".$txtExtnNo.", ".$txtBatchDate.", ".$cbo_process.", ".$txtDurationReq.", ".$cbo_machine.", ".$txtRemarks.", ".$hdnBookingWithoutOrd.", ".$hdnBookingType.", ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."')";

        // echo "10**insert into yd_batch_mst(".$field_array_mst.") values ".$data_array_mst; die;
        $field_array_dtls = 'id, mst_id, entry_form, yd_job_id, yd_job_dtls_id, order_id, quantity, bobbin_type, winding_pckg_qty, order_no, sales_order_id, sales_order_no, product_id, inserted_by, insert_date';
        
        for($i=1; $i<=$total_row; $i++) 
		{
            $bobbin_type = 'txtBobbin_'.$i;
            $winding_pack_qty = 'txtWindingPack_'.$i;
            $batch_qty = 'txtBatchQty_'.$i;
            $jobDtlsId = 'hdnDtlsId_'.$i;
            $orderNo = 'hdnOrderNo_'.$i;
            $salesOrdId = 'hdnSalesOrderId_'.$i;
            $salesOrdNo = 'hdnSalesOrderNo_'.$i;
            $productId = 'hdnProductId_'.$i;

            $data_array_dtls .= $add_comma ? ',' : ''; // if $add_comma is true, add a comma in the end of $data_array_dtls

            $data_array_dtls .= "(".$dtlsId.",".$mstId.",".$entryForm.",".$hdnJobMstId.",".$$jobDtlsId.",".$hdnOrderId.",".$$batch_qty.",".$$bobbin_type.",".$$winding_pack_qty.",".$$orderNo.",".$$salesOrdId.",".$$salesOrdNo.",".$$productId.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

            $add_comma = true; // first entry is done. add a comma for next entries
            $dtlsId++; // increment details id by 1
        }

        // echo "10**insert into yd_batch_dtls (".$field_array_dtls.") values ".$data_array_dtls; die;
        $rID = sql_insert('yd_batch_mst', $field_array_mst, $data_array_mst, 0);
        
        $flag = ($flag && $rID);    // return true if $flag is true and mst table insert is successful

        // echo $flag, $rID;die;
        // echo "10**insert into yd_batch_dtls(".$field_array_dtls.") values ".$data_array_dtls.""; die;
        $rID2 = sql_insert('yd_batch_dtls', $field_array_dtls, $data_array_dtls, 0);

        $flag = ($flag && $rID2);   // return true if $flag is true and dtls table insert is successful

        if($db_type==0) {
            if($flag) {
                mysql_query("COMMIT");              
                echo "0**".$new_batch_id[0]."**".$mstId."**".str_replace("'", "", $txtBatchNo);
            } else {
                mysql_query("ROLLBACK");
                echo "10**".$hdnMstJobId;
            }
        }
        else if($db_type==2) {
            if($flag) {
                oci_commit($con);
                echo "0**".$new_batch_id[0]."**".$mstId."**".str_replace("'", "", $txtBatchNo);;
            } else {
                oci_rollback($con);
                echo "10**".$hdnMstJobId;
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
        $hdnUpdateId = str_replace("'", '', $hdnUpdateId);
        $txtBatchSerialNo = str_replace("'", '', $txtBatchSerialNo);
		$batch_no_creation 	= str_replace("'", "", $batch_no_creation);
		$company=str_replace("'", '', $cbo_company_name);
		if ($batch_no_creation != 1)
			{
				
				 if (is_duplicate_field("batch_number", "yd_batch_mst", "batch_number=$txtBatchNo and $extention_no_cond and id<>$hdnUpdateId and status_active=1 and is_deleted=0") == 1)
				{
					echo "11**0";
					disconnect($con);
					die;
				} 
			}


        if($db_type==0) mysql_query("BEGIN");

        $field_array_mst= 'location_id*batch_color_id*batch_against*batch_color_range*batch_number*batch_weight*extention_no*batch_date* process_id*duration_req*machine_id*remarks*updated_by*update_date';

        $data_array_mst=''.$cbo_location_name.'*'.$hdnColorId.'*'.$cbo_batch_against.'*'.$txtColorRange.'*'.$txtBatchNo.'*'.$txtBatchWeight.'*'.$txtExtnNo.'*'.$txtBatchDate.'*'.$cbo_process.'*'.$txtDurationReq.'*'.$cbo_machine.'*'.$txtRemarks.'*'.$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

        $field_array_dtls = 'quantity*bobbin_type*winding_pckg_qty*updated_by*update_date';

        for($i = 1; $i <= $total_row; $i++) {

            $bobbin_type = 'txtBobbin_'.$i;
            $winding_pack_qty = 'txtWindingPack_'.$i;
            $batch_qty = 'txtBatchQty_'.$i;
            $jobDtlsId = 'hdnDtlsId_'.$i;
            $orderId = 'hdnOrderId_'.$i;

            $data_array_dtls[str_replace("'", '', $$jobDtlsId)] = explode('*',(''.$$batch_qty.'*'.$$bobbin_type.'*'.$$winding_pack_qty.'*'.$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
            $id_arr[]=str_replace("'", '', $$jobDtlsId);
        }
     
        // echo sql_update('yd_batch_mst', $field_array_mst, $data_array_mst, 'id', $hdnUpdateId, 0);die;

        $rID = sql_update('yd_batch_mst', $field_array_mst, $data_array_mst, 'id', $hdnUpdateId, 0);

        // echo $rID;die;

        $flag = ($flag && $rID);    // return true if $flag is true and mst table update is successful

        // echo '10**' . bulk_update_sql_statement('yd_batch_dtls', 'id', $field_array_dtls, $data_array_dtls, $id_arr);die;

        $rID2 = execute_query(bulk_update_sql_statement('yd_batch_dtls', 'id', $field_array_dtls, $data_array_dtls, $id_arr), 1);

        $flag = ($flag && $rID2);   // return true if $flag is true and dtls table update is successful

        if($db_type==0) {
            if($flag) {
                mysql_query('COMMIT');
                echo '1**'.$txtBatchSerialNo.'**'.$hdnUpdateId."**".str_replace("'", "", $txtBatchNo);;
            } else {
                mysql_query('ROLLBACK');
                echo '10**'.$txtBatchSerialNo.'**'.$hdnUpdateId."**".str_replace("'", "", $txtBatchNo);;
            }
        }
        else if($db_type==2) {
            if($flag) {
                oci_commit($con);
                echo '1**'.$txtBatchSerialNo.'**'.$hdnUpdateId."**".str_replace("'", "", $txtBatchNo);;
            } else {
                oci_rollback($con);
                echo '10**'.$txtBatchSerialNo.'**'.$hdnUpdateId."**".str_replace("'", "", $txtBatchNo);;
            }
        }

        disconnect($con);
        die;
    }

    else if($operation == 2) {
        echo '7**';
    }
}

if($action == 'yd_batch_creation_print') 
{
    //echo load_html_head_contents("yd batch creation print", "../../", 1, 1,'','','');
    extract($_REQUEST);
    $data = explode('*', $data);

    $company_id = $data[0];
    $batch_id = $data[1];

    $print_id = $data[3];

    if ($print_id==1) {
        
        $company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
        $location = return_field_value("location_name", "lib_location", "company_id=$company_id");
        $address = return_field_value("address", "lib_location", "company_id=$company_id");
        $imge_arr = return_library_array("select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1", 'master_tble_id', 'image_location');
        $color_arr = return_library_array('select id, color_name from lib_color where status_active=1 and is_deleted=0', 'id', 'color_name');
        $machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
        $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
       $count_arr = return_library_array("Select id, yarn_count from  lib_yarn_count where  status_active=1", 'id', 'yarn_count');
        $comp_arr = return_library_array("select id,composition_name from lib_composition_array where is_deleted=0 and status_active=1", 'id', 'composition_name');


        $batcth_arr = sql_select("select id, yd_job_id, yd_batch_id, batch_color_id, batch_weight, order_id, batch_date, machine_id from yd_batch_mst where id =$batch_id and status_active=1 and is_deleted=0");

        $batcth_details_arr = sql_select("select sum(winding_pckg_qty) as winding_pckg_qty from yd_batch_dtls where mst_id =$batch_id and status_active=1 and is_deleted=0 group by mst_id");

        $batcth_count_type_arr = sql_select("select a.id, a.order_id, a.bobbin_type, a.winding_pckg_qty, a.quantity, b.sales_order_no, b.style_ref, b.lot, b.count_id, b.yarn_type_id, b.yarn_composition_id
            from yd_batch_dtls a, yd_ord_dtls b
            where a.mst_id=$batch_id and a.status_active=1 and a.yd_job_dtls_id=b.id and a.is_deleted=0");

        $yd_order_id= $batcth_arr[0][csf("order_id")];
        
        $yd_order_arr = sql_select("select id, ydw_no from wo_yarn_dyeing_mst where id =$yd_order_id");

        $yd_job_arr = sql_select("select id, job_no, job_no_id from wo_yarn_dyeing_dtls where  mst_id =$yd_order_id");

        $job_no_id = $yd_job_arr[0][csf("job_no_id")];

        $buyer_style_arr= sql_select("select id, job_no, buyer_name, style_ref_no_prev from wo_po_details_master where  id =$job_no_id");

        ?>
        <div id="table_row" style="width:900px">
            <table width="900" align="right">
                <tr class="form_caption">
                    <td width="50" lign="left">
                        <img style="margin-bottom: -200px;" src='../../<? echo $imge_arr[$company_id]; ?>' height='80' width='150'/>
                    </td>
                </tr>
            </table>
            <table width="900" align="right">
                <tr class="form_caption">
                    <td colspan="6" align="center" style="font-size:20px">
                        <strong><? echo $company_library[$company_id]; ?></strong>
                    </td>
                </tr>
                <tr class="form_caption">
                    <td colspan="6" align="center" style="font-size:14px">
                        <?
                        $nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
                        foreach ($nameArray as $result) {
                            ?>
                            <? echo $result[csf('plot_no')]; ?>
                            <? echo $result[csf('level_no')] ?>
                            <? echo $result[csf('road_no')]; ?>
                            <? echo $result[csf('block_no')]; ?>
                            <? echo $result[csf('city')]; ?>
                            <? echo $result[csf('zip_code')]; ?>
                            <?php echo $result[csf('province')]; ?>
                            <? echo $country_arr[$result[csf('country_id')]]; ?><br>
                            <? echo $result[csf('email')]; ?>
                            <? echo $result[csf('website')];
                        }
                        ?>
                        <br>
                        <strong style="text-decoration: underline;">(Yarn Dyeing Unit)</strong>
                        
                    </td>
                </tr>
                <tr>
                    <td colspan="6" align="center"><strong style="border: 1px solid black;">PROCESS ROUTE CARD</strong></td>
                </tr>
                <tr>
                    <td colspan="5" >&nbsp;</td>
                    <td id="barcode_img_id" align="right"></td>
                </tr>
                <tr>
                    <td colspan="6" align="right">Batch date: <?php echo $batcth_arr[0][csf("batch_date")];?></td>
                </tr>
            </table>
            <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="900">
                <tr>
                    <td height="30" width="150">W/O No.</td>
                    <td height="30" width="150">&nbsp;<?php  echo $yd_order_arr[0][csf("ydw_no")];?></td>
                    <td height="30" width="150">Job No.</td>
                    <td height="30" width="150" >&nbsp;<?php echo $yd_job_arr[0][csf("job_no")];?></td>
                    <td height="30" width="150">Style</td>
                    <td height="30" width="150" >&nbsp;<?php echo $buyer_style_arr[0][csf("style_ref_no_prev")];?></td>
                </tr>
            </table>
            <br>
            <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="900">
                <tr>
                    <td height="30" width="150">Buyer Name</td>
                    <td height="30" >&nbsp;<?php echo $buyer_arr[$buyer_style_arr[0][csf("buyer_name")]];?></td>
                    <td height="30" width="200">M/C No</td>
                    <td height="30">&nbsp;<?php echo $machine_arr[$batcth_arr[0][csf("machine_id")]];?></td>
                </tr>
            </table>
            <br>
            <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="900">
                <tr>
                    <td height="30" width="150">Shade Name</td>
                    <td height="30" >&nbsp;<?php  echo $color_arr[$batcth_arr[0][csf("batch_color_id")]];?></td>
                    <td height="30" width="150">Batch No</td>
                    <td height="30">&nbsp;<?php  echo $batcth_arr[0][csf("yd_batch_id")];?></td>
                </tr>
            </table>
            <br>
            <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="900">
                <tr>
                    <td height="30" width="150">Batch Weight</td>
                    <td height="30" >&nbsp;<?php  echo $batcth_arr[0][csf("batch_weight")];?></td>
                    <td height="30" width="200">No. of Package</td>
                    <td height="30">&nbsp;<?php echo $batcth_details_arr[0][csf("winding_pckg_qty")]?></td>
                </tr>
            </table>
            <br>
            <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="900">
                <?php
                    foreach ($batcth_count_type_arr as $data) {
                ?>
                <tr>
                    <td height="30" width="150">Yarn Count and Type</td>
                    <td height="30" colspan="5"><?php echo $count_arr[$data[csf("count_id")]];?>, <?php echo $yarn_type[$data[csf("yarn_type_id")]];?>,  <?php echo $comp_arr[$data[csf("yarn_composition_id")]];?></td>
                </tr>
                <?php }?>
            </table>
            <br>
            <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="900">
                <tr>
                    <td height="30" width="400">Batch Prepared By</td>
                    <td height="30"></td>
                </tr>
                <tr>
                    <td height="30" width="400">Bleach Completed By</td>
                    <td height="30"></td>
                </tr>
                <tr>
                    <td height="30" width="400">Dyeing Completed By</td>
                    <td height="30"></td>
                </tr>
                <tr>
                    <td height="30" width="400">Wash Completed By</td>
                    <td height="30"></td>
                </tr>
                <tr>
                    <td height="30" width="400">Finishing By( F/S pH5.5 and Need Good Result</td>
                    <td height="30"></td>
                </tr>
                <tr>
                    <td height="30" width="400">Dyed Yarn Sample for Hard Winding</td>
                    <td height="30"></td>
                </tr>
                <tr>
                    <td height="30"  width="400">Hard Winding By</td>
                    <td height="30" ></td>
                </tr>
                <tr>
                    <td height="30" width="400">Quality Check By</td>
                    <td height="30" ></td>
                </tr>
                <tr>
                    <td height="30"  width="400">Packing By</td>
                    <td height="30" ></td>
                </tr>
            </table>
            <br>
            <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="900">
                <tr>
                    <td height="30" width="200">Match To</td>
                    <td height="30"></td>
                    <td height="30" width="200">No. of Cone</td>
                    <td height="30"></td>
                </tr>
            </table>
            <br>
            <table class="rpt_table"  cellpadding="0" cellspacing="0" rules="all" width="900">
                <tr>
                    <td width="400">
                        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="400">
                            <tr>
                                <td align="center">
                                    Remarks or Comment's                
                                </td>
                            </tr>
                            <tr>
                                <td height="260" align="center">
                                                 
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td width="200">  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td width="300">
                        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="300">
                            <tr>
                                <td height="40" align="left">
                                    Are you Checked?              
                                </td>
                            </tr>
                            <tr>
                                <td height="40" align="left">
                                    Shade                
                                </td>
                            </tr>
                            <tr>
                                <td height="40" align="left">
                                    Fastness        
                                </td>
                            </tr>
                            <tr>
                                <td height="40" align="left">
                                    Leveiness             
                                </td>
                            </tr>
                            <tr>
                                <td height="40" align="left">
                                    Finishing pH             
                                </td>
                            </tr>
                            <tr>
                                <td height="40" align="center">
                                    Batch OK By                
                                </td>
                            </tr>
                            <tr>
                                <td height="40" align="center">
                                    Production Authority Signature              
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <br>
            <table class="rpt_table"  cellpadding="0" cellspacing="0" rules="all" width="900">
                <tr>
                    <td>
                        Hydro and Dryer is highly prohibition without authorized signature.
                    </td>
                </tr>
            </table>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
        </div>
        <script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
        <script>
            function generateBarcode(valuess) {
                var value = valuess;//$("#barcodeValue").val();
                //alert(value)
                var btype = 'code39';//$("input[name=btype]:checked").val();
                var renderer = 'bmp';// $("input[name=renderer]:checked").val();

                var settings = {
                    output: renderer,
                    bgColor: '#FFFFFF',
                    color: '#000000',
                    barWidth: 1,
                    barHeight: 30,
                    moduleSize: 5,
                    posX: 10,
                    posY: 20,
                    addQuietZone: 1
                };
                //$("#barcode_img_id").html('11');
                value = {code: value, rect: false};

                $("#barcode_img_id").show().barcode(value, btype, settings);
            }
            generateBarcode('<?php  echo $batcth_arr[0][csf("yd_batch_id")];?>');
        </script>

        <?php
    }
}
if($action == 'yd_batch_creation_print_2') 
{
    //echo load_html_head_contents("yd batch creation print", "../../", 1, 1,'','','');
    extract($_REQUEST);
    $data = explode('*', $data);

    $company_id = $data[0];
    $batch_id = $data[1];

    // $print_id = $data[3];

        $company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
        $location = return_field_value("location_name", "lib_location", "company_id=$company_id");
        $address = return_field_value("address", "lib_location", "company_id=$company_id");
        $imge_arr = return_library_array("select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1", 'master_tble_id', 'image_location');
        $color_arr = return_library_array('select id, color_name from lib_color where status_active=1 and is_deleted=0', 'id', 'color_name');
        $machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
        $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
       $count_arr = return_library_array("Select id, yarn_count from  lib_yarn_count where  status_active=1", 'id', 'yarn_count');
        $comp_arr = return_library_array("select id,composition_name from lib_composition_array where is_deleted=0 and status_active=1", 'id', 'composition_name');


        $batcth_arr = sql_select("select id, yd_job_id, yd_batch_id, batch_color_id, batch_weight, order_id, batch_date, machine_id from yd_batch_mst where id =$batch_id and status_active=1 and is_deleted=0");

        $batcth_details_arr = sql_select("select sum(winding_pckg_qty) as winding_pckg_qty from yd_batch_dtls where mst_id =$batch_id and status_active=1 and is_deleted=0 group by mst_id");

        $batcth_count_type_arr = sql_select("select a.id, a.order_id, a.bobbin_type, a.winding_pckg_qty, a.quantity, b.sales_order_no, b.style_ref, b.lot, b.count_id, b.yarn_type_id, b.yarn_composition_id, b.uom
            from yd_batch_dtls a, yd_ord_dtls b
            where a.mst_id=$batch_id and a.status_active=1 and a.yd_job_dtls_id=b.id and a.is_deleted=0");

        $yd_order_id= $batcth_arr[0][csf("order_id")];
        
        $yd_order_arr = sql_select("select id, ydw_no from wo_yarn_dyeing_mst where id =$yd_order_id");

        $yd_job_arr = sql_select("select id, job_no, job_no_id from wo_yarn_dyeing_dtls where  mst_id =$yd_order_id");

        $job_no_id = $yd_job_arr[0][csf("job_no_id")];

        $buyer_style_arr= sql_select("select id, job_no, buyer_name, style_ref_no_prev from wo_po_details_master where  id =$job_no_id");

        ?>
        <style>
            table, tr, td{
                padding-left: 5px;
            }
        </style>
        <div id="table_row" style="width:900px">
            <table width="900" align="right">
                <tr class="form_caption">
                    <td width="50" lign="left">
                        <img style="margin-bottom: -200px;" src='../../<? echo $imge_arr[$company_id]; ?>' height='80' width='150'/>
                    </td>
                </tr>
            </table>
            <table width="900" align="right">
                <tr class="form_caption">
                    <td colspan="6" align="center" style="font-size:20px">
                        <strong><? echo $company_library[$company_id]; ?></strong>
                    </td>
                </tr>
                <tr class="form_caption">
                    <td colspan="6" align="center" style="font-size:14px">
                        <?
                        $nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
                        foreach ($nameArray as $result) {
                            ?>
                            <? echo $result[csf('plot_no')]; ?>
                            <? echo $result[csf('level_no')] ?>
                            <? echo $result[csf('road_no')]; ?>
                            <? echo $result[csf('block_no')]; ?>
                            <? echo $result[csf('city')]; ?>
                            <? echo $result[csf('zip_code')]; ?>
                            <?php echo $result[csf('province')]; ?>
                            <? echo $country_arr[$result[csf('country_id')]]; ?><br>
                            <? echo $result[csf('email')]; ?>
                            <? echo $result[csf('website')];
                        }
                        ?>
                        <br>
                        <strong style="text-decoration: underline;">(Yarn Dyeing Unit)</strong>
                        
                    </td>
                </tr>
                <tr>
                    <td colspan="6" align="center"><strong style="border: 1px solid black;">PROCESS ROUTE CARD</strong></td>
                </tr>
                <tr>
                    <td colspan="5" >&nbsp;</td>
                    <td id="barcode_img_id" align="right"></td>
                </tr>
                <tr>
                    <td colspan="6" align="right">Batch date: <?php echo $batcth_arr[0][csf("batch_date")];?></td>
                </tr>
            </table>
            <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="900">
                <tr>
                    <td height="30" width="150">W/O No.</td>
                    <td height="30" width="150">&nbsp;<?php  echo $yd_order_arr[0][csf("ydw_no")];?></td>
                    <td height="30" width="150">Job No.</td>
                    <td height="30" width="150" >&nbsp;<?php echo $yd_job_arr[0][csf("job_no")];?></td>
                    <td height="30" width="150">Style</td>
                    <td height="30" width="150" >&nbsp;<?php echo $buyer_style_arr[0][csf("style_ref_no_prev")];?></td>
                </tr>
            </table>
            <br>
            <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="900">
                <tr>
                    <td height="30" width="150">Buyer Name</td>
                    <td height="30" >&nbsp;<?php echo $buyer_arr[$buyer_style_arr[0][csf("buyer_name")]];?></td>
                    <td height="30" width="200">M/C No</td>
                    <td height="30">&nbsp;<?php echo $machine_arr[$batcth_arr[0][csf("machine_id")]];?></td>
                </tr>
            </table>
            <br>
            <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="900">
                <tr>
                    <td height="30" width="150">Shade Name</td>
                    <td height="30" >&nbsp;<?php  echo $color_arr[$batcth_arr[0][csf("batch_color_id")]];?></td>
                    <td height="30" width="150">Batch No</td>
                    <td height="30">&nbsp;<?php  echo $batcth_arr[0][csf("yd_batch_id")];?></td>
                </tr>
            </table>
            <br>
            <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="900">
                <tr>
                    <td height="30" width="150">Batch Weight</td>
                    <td height="30"width="150" >&nbsp;<?php  echo $batcth_arr[0][csf("batch_weight")];?></td>
                    <td height="30" width="100">UOM</td>
                    <td height="30" width="100"><?=$unit_of_measurement[$batcth_count_type_arr[0]["UOM"]];?></td>
                    <td height="30" width="200">No. of Package</td>
                    <td height="30" width="200">&nbsp;<?php echo $batcth_details_arr[0][csf("winding_pckg_qty")]?></td>
                </tr>
            </table>
            <br>
            <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="900">
                <?php
                    foreach ($batcth_count_type_arr as $data) {
                ?>
                <tr>
                    <td height="30" width="150">Yarn Count and Type</td>
                    <td height="30" colspan="5"><?php echo $count_arr[$data[csf("count_id")]];?>, <?php echo $yarn_type[$data[csf("yarn_type_id")]];?>,  <?php echo $comp_arr[$data[csf("yarn_composition_id")]];?></td>
                </tr>
                <?php }?>
            </table>
            <br>
            <table class="rpt_table" cellpadding="0" cellspacing="0" width="900">
                <tr>
                    <td style="vertical-align:top">
                        <table class="rpt_table" cellpadding="0" cellspacing="0" width="450">
                            <tr>
                                <td>
                                    <table border="1" cellpadding="0" cellspacing="0" rules="all">
                                    <tr>
                                        <td width="110" height="30">Lot No</td>
                                        <td width="120" style="padding-top:10px"><?=$batcth_count_type_arr[0]["LOT"];?></td>
                                        <td width="110">Finished Weight</td>
                                        <td width="110"></td> 
                                    </tr>
                                    <tr>
                                        <td height="30">Total Cone</td>
                                        <td></td>
                                        <td>Process Loss</td>
                                        <td></td> 
                                    </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr>
                                <td>
                                    <table border="1" cellpadding="0" cellspacing="0" rules="all" width="450">
                                        <tr>
                                            <td width="100" height="30" colspan=4><strong>Match With</strong></td>   
                                        </tr>
                                        <tr>
                                            <td width="100" height="30"><strong>Process</strong></td>
                                            <td width="200" colspan="2"><strong>Description</strong></td>
                                            <td width="100"><strong>Date</strong></td>
                                        </tr>
                                        <tr>
                                            <td width="100" height="30">Process 1</td>
                                            <td width="200" colspan="2">Soft Winding</td>
                                            <td width="100">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td width="100" height="30">Process 2</td>
                                            <td width="200" colspan="2">Dyeing & Wash Finishing</td>
                                            <td width="100">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td width="100" height="30">Process 3</td>
                                            <td width="200" colspan="2">Hydro & Dryer</td>
                                            <td width="100">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td width="100" height="30">Process 4</td>
                                            <td width="200" colspan="2">Hard Winding & Packing</td>
                                            <td width="100">&nbsp;</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            
                            
                        </table>   
                    </td>
                    <td width="20">&nbsp;</td>
                    <td style="vertical-align: bottom;">
                        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="440">
                            <tr>
                                <td height="30" width="200">Batch Prepared By</td>
                                <td height="30"></td>
                            </tr>
                            <tr>
                                <td height="30" width="200">Bleach Completed By</td>
                                <td height="30"></td>
                            </tr>
                            <tr>
                                <td height="30" width="200">Dyeing Completed By</td>
                                <td height="30"></td>
                            </tr>
                            <tr>
                                <td height="30" width="200">Wash Completed By</td>
                                <td height="30"></td>
                            </tr>
                            <tr>
                                <td height="30" width="200">Finishing By( F/S pH5.5 and Need Good Result</td>
                                <td height="30"></td>
                            </tr>
                            <tr>
                                <td height="30" width="200">Dyed Yarn Sample for Hard Winding</td>
                                <td height="30"></td>
                            </tr>
                            <tr>
                                <td height="30"  width="200">Hard Winding By</td>
                                <td height="30" ></td>
                            </tr>
                            <tr>
                                <td height="30" width="200">Quality Check By</td>
                                <td height="30" ></td>
                            </tr>
                            <tr>
                                <td height="30"  width="200">Packing By</td>
                                <td height="30" ></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <br>
            <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="900">
                <tr>
                    <td height="30" width="200">Match To</td>
                    <td height="30"></td>
                    <td height="30" width="200">No. of Cone</td>
                    <td height="30"></td>
                </tr>
            </table>
            <br>
            <table class="rpt_table"  width="900">
                <tr>
                    <td width="300">
                        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="300">
                            <tr>
                                <td align="center">
                                    Remarks or Comment's                
                                </td>
                            </tr>
                            <tr>
                                <td height="260" align="center">
                                                 
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td width="15">&nbsp;</td>
                    <td width="320" style="vertical-align:top">  
                        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="320">
                            <tr>
                                <td height="40" width="80"></td>
                                <td align="center" width="80">Name</td>
                                <td align="center" width="80">Time</td>
                                <td align="center" width="80">Total Hrs</td>
                            </tr>
                            <tr>
                                <td height="40" align="center">Load</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td height="40" align="center">Unload</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </table>
                    </td>
                    <td width="15" style="border-style:none">&nbsp;</td>
                    <td width="250">
                        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="250">
                            <tr>
                                <td height="40" align="left">
                                    Are you Checked?              
                                </td>
                                <td width="125">&nbsp;</td>
                            </tr>
                            <tr>
                                <td height="40" align="left">
                                    Shade                
                                </td>
                                <td width="125">&nbsp;</td>
                            </tr>
                            <tr>
                                <td height="40" align="left">
                                    Fastness        
                                </td>
                                <td width="125">&nbsp;</td>
                            </tr>
                            <tr>
                                <td height="40" align="left">
                                    Leveiness             
                                </td>
                                <td width="125">&nbsp;</td>
                            </tr>
                            <tr>
                                <td height="40" align="left">
                                    Finishing pH             
                                </td>
                                <td width="125">&nbsp;</td>
                            </tr>
                            <tr>
                                <td height="40" align="center" colspan=2>
                                    Batch OK By                
                                </td>
                            </tr>
                            <tr>
                                <td height="40" align="center" colspan=2>
                                    Production Authority Signature              
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <br>
            <table class="rpt_table"  cellpadding="0" cellspacing="0" rules="all" width="900">
                <tr>
                    <td>
                        Hydro and Dryer is highly prohibition without authorized signature.
                    </td>
                </tr>
            </table>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
        </div>
        <script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
        <script>
            function generateBarcode(valuess) {
                var value = valuess;//$("#barcodeValue").val();
                //alert(value)
                var btype = 'code39';//$("input[name=btype]:checked").val();
                var renderer = 'bmp';// $("input[name=renderer]:checked").val();

                var settings = {
                    output: renderer,
                    bgColor: '#FFFFFF',
                    color: '#000000',
                    barWidth: 1,
                    barHeight: 30,
                    moduleSize: 5,
                    posX: 10,
                    posY: 20,
                    addQuietZone: 1
                };
                //$("#barcode_img_id").html('11');
                value = {code: value, rect: false};

                $("#barcode_img_id").show().barcode(value, btype, settings);
            }
            generateBarcode('<?php  echo $batcth_arr[0][csf("yd_batch_id")];?>');
        </script>

        <?php
    
}
if($action == 'yd_batch_creation_print_3') 
{
    //echo load_html_head_contents("yd batch creation print", "../../", 1, 1,'','','');
    extract($_REQUEST);
    $data = explode('*', $data);

    $company_id = $data[0];
    $batch_id = $data[1];

    // $print_id = $data[3];

        $company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
        $location = return_field_value("location_name", "lib_location", "company_id=$company_id");
        $address = return_field_value("address", "lib_location", "company_id=$company_id");
        $imge_arr = return_library_array("select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1", 'master_tble_id', 'image_location');
        $color_arr = return_library_array('select id, color_name from lib_color where status_active=1 and is_deleted=0', 'id', 'color_name');
        $machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
        $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
       $count_arr = return_library_array("Select id, yarn_count from  lib_yarn_count where  status_active=1", 'id', 'yarn_count');
        $comp_arr = return_library_array("select id,composition_name from lib_composition_array where is_deleted=0 and status_active=1", 'id', 'composition_name');

        
        
        
        $batch_arr = sql_select("select a.id, a.yd_job_id, a.yd_batch_id, a.batch_color_id, a.batch_weight, a.order_id, a.batch_date, a.machine_id,a.REMARKS,a.BATCH_NUMBER, b.WITHIN_GROUP, b.ORDER_NO, b.PARTY_ID from yd_batch_mst a, yd_ord_mst b where a.id =$batch_id and a.YD_JOB_ID=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

        if($batch_arr[0]['WITHIN_GROUP']==1)
        {
            $party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
            // $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
        }
        else
        {
            
            $party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
        }

        $batcth_details_arr = sql_select("select sum(winding_pckg_qty) as winding_pckg_qty from yd_batch_dtls where mst_id =$batch_id and status_active=1 and is_deleted=0 group by mst_id");

        $batch_count_type_arr = sql_select("select a.id, a.order_id, a.bobbin_type, a.winding_pckg_qty, a.quantity, b.sales_order_no, b.style_ref, b.lot, b.count_id, b.yarn_type_id, b.yarn_composition_id, b.uom
            from yd_batch_dtls a, yd_ord_dtls b
            where a.mst_id=$batch_id and a.status_active=1 and a.yd_job_dtls_id=b.id and a.is_deleted=0");

        $yd_order_id= $batch_arr[0][csf("order_id")];
        
        $yd_order_arr = sql_select("select id, ydw_no from wo_yarn_dyeing_mst where id =$yd_order_id");

        $yd_job_arr = sql_select("select id, job_no, job_no_id from wo_yarn_dyeing_dtls where  mst_id =$yd_order_id");

        $job_no_id = $yd_job_arr[0][csf("job_no_id")];

        $buyer_style_arr= sql_select("select id, job_no, buyer_name, style_ref_no_prev from wo_po_details_master where  id =$job_no_id");

        ?>
        <style>
            table, tr, td{
                padding-left: 5px;
            }
        </style>
        <div id="table_row" style="width:900px">
            <table width="900" align="right">
                <tr class="form_caption">
                    <td width="50" lign="left">
                        <img style="margin-bottom: -200px;" src='../../<? echo $imge_arr[$company_id]; ?>' height='80' width='150'/>
                    </td>
                </tr>
            </table>
            <table width="900" align="right">
                <tr class="form_caption">
                    <td colspan="6" align="center" style="font-size:20px">
                        <strong><? echo $company_library[$company_id]; ?></strong>
                    </td>
                </tr>
                <tr class="form_caption">
                    <td colspan="6" align="center" style="font-size:14px">
                        <?
                        $nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
                        foreach ($nameArray as $result) {
                            ?>
                            <? echo $result[csf('plot_no')]; ?>
                            <? echo $result[csf('level_no')] ?>
                            <? echo $result[csf('road_no')]; ?>
                            <? echo $result[csf('block_no')]; ?>
                            <? echo $result[csf('city')]; ?>
                            <? echo $result[csf('zip_code')]; ?>
                            <?php echo $result[csf('province')]; ?>
                            <? echo $country_arr[$result[csf('country_id')]]; ?><br>
                            <? //echo $result[csf('email')]; ?>
                            <? //echo $result[csf('website')];
                        }
                        ?>
                        <br>
                        
                        
                    </td>
                </tr>
                <tr>
                    <td colspan="6" align="center"><u><strong style="border: 1px solid black;">Internal Order Template</strong></u></td>
                </tr>
                
            </table>
            <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="900">
                <tr>
                    <td width="20%"><strong>Machine ID.</strong></td>
                    <td width="30%"><?php echo $machine_arr[$batch_arr[0][csf("machine_id")]];?></td>
                    <td width="20%"><strong>Date</strong></td>
                    <td width="30%"><?php echo $batch_arr[0][csf("batch_date")];?></td>
                </tr>
                <tr>
                    <td><strong>Customer Name</strong></td>
                    <td><?php echo $party_arr[$batch_arr[0]['PARTY_ID']];?></td>
                    <td><strong>Internal Order No.</strong></td>
                    <td><?php echo $batch_arr[0]['ORDER_NO']; ?></td>
                </tr>
                <tr>
                    <td><strong>Yarn Count</strong></td>
                    <td><?=$batch_count_type_arr[0]["LOT"];?></td>
                    <td><strong>Colour Name</strong></td>
                    <td><?php  echo $color_arr[$batch_arr[0][csf("batch_color_id")]];?></td>
                </tr>
                <tr>
                    <td><strong>Yarn Type/Composition</strong></td>
                    <td><?=$yarn_type[$batch_count_type_arr[0]["YARN_TYPE_ID"]]." ".$composition[$batch_count_type_arr[0]["YARN_COMPOSITION_ID"]];?></td>
                    <td><strong>Quantity (Weight)</strong></td>
                    <td><?=$batch_count_type_arr[0]["QUANTITY"]."Kg";?></td>
                </tr>
                <tr>
                    <td><strong>Yarn Lot No.</strong></td>
                    <td><?=$batch_count_type_arr[0]["LOT"];?></td>
                    <td><strong>Number of Package</strong></td>
                    <td><?=$batch_count_type_arr[0]["WINDING_PCKG_QTY"];?></td>
                </tr>
                <tr>
                    <td><strong>Batch Number</strong></td>
                    <td><?php echo $batch_arr[0][csf("BATCH_NUMBER")];?></td>
                    <td><strong>Article Description</strong></td>
                    <td align="center"><strong>Yarn</strong></td>
                </tr>
                <tr>
                    <td><strong>Delivery Note</strong></td>
                    <td colspan="2"><?php echo $batch_arr[0][csf("REMARKS")];?></td>
                </tr>
            </table>

            <br>
            <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="900" style="text-align:center">
                <tr bg-color="light-grey">
                    <td colspan=4><strong>PROCESSES</strong></td>   
                </tr>
                <tr>
                    <td width="20%"><strong>Process</strong></td>
                    <td width="50%"><strong>Description</strong></td>
                    <td width="30%"><strong>Date & Signature</strong></td>
                </tr>
                <tr>
                    <td>Process 1</td>
                    <td>Soft Winding</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>Process 2</td>
                    <td>Batch-Preparation</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>Process 3</td>
                    <td>Scouring & Bleaching</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>Process 4</td>
                    <td>Dyeing,Wash & Finishing</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>Process 5</td>
                    <td>Shade Checking</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>Process 6</td>
                    <td>Hydro Extracking</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>Process 7</td>
                    <td>Drying</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>Process 8</td>
                    <td>Hard Winding</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>Process 9</td>
                    <td>Packing</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>Process 9</td>
                    <td>Delivery</td>
                    <td>&nbsp;</td>
                </tr>
            </table>
                           

        </div>
        <script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
        <script>
            function generateBarcode(valuess) {
                var value = valuess;//$("#barcodeValue").val();
                //alert(value)
                var btype = 'code39';//$("input[name=btype]:checked").val();
                var renderer = 'bmp';// $("input[name=renderer]:checked").val();

                var settings = {
                    output: renderer,
                    bgColor: '#FFFFFF',
                    color: '#000000',
                    barWidth: 1,
                    barHeight: 30,
                    moduleSize: 5,
                    posX: 10,
                    posY: 20,
                    addQuietZone: 1
                };
                //$("#barcode_img_id").html('11');
                value = {code: value, rect: false};

                $("#barcode_img_id").show().barcode(value, btype, settings);
            }
            generateBarcode('<?php  echo $batcth_arr[0][csf("yd_batch_id")];?>');
        </script>

        <?php
    
}


?>