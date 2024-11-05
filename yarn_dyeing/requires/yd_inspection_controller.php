<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header('location:login.php');
include('../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$user_level=$_SESSION['logic_erp']["user_level"];

if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	if($data[1]==1) $dropdown_name="cbo_location_name";
	else $dropdown_name="cbo_party_location";

	echo create_drop_down( $dropdown_name, 150, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );
	exit();
}



if ($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);
    //company+'_'+1
	if($data[1]==1) $load_function="fnc_load_party(2,document.getElementById('cbo_within_group').value)";
	else $load_function="";
	//$company_cond
	if($data[1]==1)
	{
		//echo  "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name";
		echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name","id,company_name", 1, "-- Select Company --", $data[2], "$load_function");
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "" );
	}
	exit();
}


if($action == 'sales_no_popup') {
	echo load_html_head_contents('Yarn Dyeing Sales Order Info', '../../', 1, 0, $unicode);
	extract($_REQUEST);
?>
    <style>
        table.rpt_table tbody td input {
            width: 90%;
        }
    </style>
	<script>
		permission="<?php echo $permission; ?>";

        var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();

	function toggle( x, origColor ) {
		var newColor = 'yellow';
		if ( x.style ) {
			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}

	function check_all_data()
	{
		var row_num=$('#list_view tr').length-1;
		for(var i=1;  i<=row_num;  i++)
		{
			if($("#tr_"+i).css("display") != "none")
			{
				$("#tr_"+i).click();
			}
		}
	}

	function js_set_value(id)
	{
		var strs=id.split("__");
        // alert(str[0]+'=='+str)
		toggle( document.getElementById( 'tr_' + strs[0] ), '#FFFFFF' );
		str=strs[1];

		if( jQuery.inArray(  str , selected_id ) == -1 ) {
			selected_id.push( str );
		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == str  ) break;
			}
			selected_id.splice( i, 1 );
		
		}
		var id = '';
		var ddd='';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
			
		}
		id = id.substr( 0, id.length - 1 );
		
		$('#selected_prod_id').val( id );	
	}

		// function js_set_value(id) {
        //     //alert(id);
		// 	document.getElementById('selected_prod_id').value = id;
		// 	parent.salesPopup.hide();
		// }
	</script>
</head>
<body>
<div align="center" style="width:920px;" >
    <form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" style="width: 920px;">
            <thead>
                <tr>
                    <th colspan="8"><?php echo create_drop_down('cbo_string_search_type', 163, $string_search_type, '', 1, '-- Searching Type --'); ?></th>
                </tr>
                <tr>
                    <th style="width:163px">Company Name</th>
                    <th style="width:100px;">Party</th>
                    <th style="width:100px">Job No/Sales order no</th>
                    <th style="width:100px">Yd Job No</th>
                    <th style="width:100px">WO No</th>
                    <th style="width:100px">
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
                        <?php echo create_drop_down('cbo_party_name', 100, "select id, company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", 'id,company_name', 1, '-- Select Company --',"", '', 0); ?>
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
                        <input type="button" name="btnSearchJob" class="formbutton" value="Show" onClick="show_list_view(document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_sales_ord_no').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_workorder_no').value+'_'+document.getElementById('cbo_party_name').value, 'create_yd_order_list_view', 'search_div', 'yd_inspection_controller','setFilterGrid(\'list_view\',-1)')" />
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

if($action == 'create_yd_order_list_view') {
    $data=explode('_', $data);
//    print_r($data);
    $search_type = $data[0];
    $company_id = $data[1];
    $ord_no = $data[2];
    $yd_job = $data[3];
    $wo_no = $data[4];
    $party_id = $data[5];
    $condition = '';
    $party = '';

    if($company_id) {
        $condition.=" and a.company_id=$data[1]";
    } else {
        echo "<h3 style='margin-top: 10px;'>Please Select Company First.</h3>"; die;
    }

    if($party_id) {
        $condition.=" and a.party_id=$party_id";  
    }

    if($search_type==0 || $search_type==4) { // no searching type or contents
        if ($yd_job!="") $condition.=" and a.yd_job like '%$yd_job%'";
        if ($ord_no!="") $condition.=" and a.sales_order_no like '%$ord_no%'";
        if ($wo_no!="") $condition.=" and a.order_no like '%$wo_no%'";
    } else if($search_type==1) { // exact
        if ($yd_job!="") $condition.=" and a.yd_job = '$yd_job'";
        if ($ord_no!="") $condition.=" and a.sales_order_no ='$ord_no'";
        if ($wo_no!="") $condition.=" and a.order_no ='$wo_no'";
    } else if($search_type==2) { // Starts with
        if ($yd_job!="") $condition.=" and a.yd_job like '$yd_job%'";
        if ($ord_no!="") $condition.=" and a.sales_order_no like '$ord_no%'";
        if ($wo_no!="") $condition.=" and a.order_no like '$wo_no%'";
    } else if($search_type==3) { // Ends with
        if ($yd_job!="") $condition.=" and a.yd_job like '%$yd_job'";
        if ($ord_no!="") $condition.=" and a.sales_order_no like '%$ord_no'";
        if ($wo_no!="") $condition.=" and a.order_no like '%$wo_no'";
    }
    $color_arr = return_library_array('select id, color_name from lib_color where status_active=1 and is_deleted=0', 'id', 'color_name');

        $sql = "SELECT a.id, b.id as y_dtls_id, c.id as batch_mst_id, d.id as batch_dtls_id, a.order_no, a.yd_job, b.sales_order_no, b.lot, c.batch_number, b.yd_color_id, sum(d.quantity) as quantity,c.batch_date from yd_ord_mst a, yd_ord_dtls b,yd_batch_mst c, yd_batch_dtls d  where a.id=b.mst_id and b.id = d.yd_job_dtls_id and c.id=d.mst_id and  a.status_active=1 and a.status_active=1 and b.status_active=1 $condition $party group by a.id, b.id, c.id, d.id, a.order_no, a.yd_job, b.sales_order_no, b.lot, c.batch_number, b.yd_color_id,c.batch_date order by c.id ";
           
        //    echo $sql; 
        $result = sql_select($sql);
    ?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table" align="left">
    <thead>
            <th width="40">SL No</th>
            <th width="100">WO NO</th>
            <th width="100">YD Job No</th>
            <th width="80">Sales Order No</th>
            <th width="100">Batch Number</th>
            <th width="80">Batch Color</th>
            <th width="100">Yarn Lot</th>
            <th width="100">Batch QTY</th>
            <th width="100">Batch Date</th>
        </thead>
    </table>
    <div style="width:920px; max-height:400px; overflow-y:scroll">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table" id="list_view">
        <?
            $i=1;
            foreach ($result as $row)
            {
                if($i%2==0) $bgcolor="#E9F3FF"; 
                else $bgcolor="#FFFFFF";        
                $string=$row[csf('id')].'_'.$row[csf('y_dtls_id')].'_'.$row[csf('batch_mst_id')].'_'.$row[csf('batch_dtls_id')];          
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i;?>" onClick="js_set_value('<? echo $i.'__'.$string;?>')">
                    <td width="40"  align="center"><? echo $i; ?></td>
                    <td width="100"  align="center"><p><? echo $row[csf('order_no')];?></p></td>
                    <td width="100"  align="center"><p><? echo $row[csf('yd_job')]; ?></p></td>
                    <td width="80"  align="center"><p><? echo $row[csf('sales_order_no')]; ?></p></td>
                    <td width="100" align="center"><p><? echo $row[csf('batch_number')]; ?>&nbsp;</p></td>
                    <td width="80"  align="center"><p><? echo $color_arr[$row[csf('yd_color_id')]];  ?>&nbsp;</p></td>
                    <td width="100"  align="center"><p><? echo $row[csf('lot')];  ?>&nbsp;</p></td>
                    <td width="100"><p><? echo $row[csf('quantity')]; ?></p></td>                    
                    <td width="100"><p><? echo change_date_format($row[csf("batch_date")])  ?></p></td>                    
                </tr>
                <?
                $i++;		
            } 
            ?>    
        </table>
        <table>
              <div class="check_all_container">
		         <div style="width:20%; float:left" align="left">
		           <input type="checkbox" name="check_all" id="check_all" onclick="check_all_data()"> Check / Uncheck All
		         </div>
		      <div style="width:70%; float:left" align="center">
                    
						<input type="button" name="close" onClick="parent.salesPopup.hide();" class="formbutton" value="Close" style="width:100px" />
                  
		      </div>
		   </div>    
        </table>
     </div>
  
    <?
    exit();  

}

if($action == 'system_id_popup') {
    echo load_html_head_contents('Search Yarn Dyeing Sales Order', '../../', 1, 0, $unicode);
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
            document.getElementById('selected_mst_id').value = id;
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
                    <th style="width:20%;">Job No/Sales order no</th>
                    <th style="width:25%;">System ID</th>
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
                    <td style="display: none;">
                        <input class="text_boxes" type="text" name="txt_job_no" id="txt_job_no" />
                    </td>
                    <td>
                        <input class="text_boxes" type="text" name="txt_system_id" id="txt_system_id" />
                    </td>
                    <td align="center">
                        <input type="hidden" id="selected_mst_id">
                        <input type="button" name="btnSearchJob" class="formbutton" value="Show" onClick="show_list_view(document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_sales_ord_no').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_system_id').value, 'create_yd_imspection_list_view', 'search_div', 'yd_inspection_controller', '')" />
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

if($action == 'create_yd_imspection_list_view') {
    // echo $data;die;
    $data=explode('_', $data);
    $search_type = $data[0];
    $company_id = $data[1];
    $ord_no = $data[2];
    $yd_job = $data[3];
    $system_id = $data[4];
    $condition = '';

    if($company_id) {
        $condition.=" and d.company_id=$data[1]";
    } else {
        echo "<h3 style='margin-top: 10px;'>Please Select Company First.</h3>"; die;
    }

    if($search_type==0 || $search_type==4) { // no searching type or contents
        //if ($yd_job!="") $condition.=" and c.yd_job like '%$yd_job%'";
        if ($ord_no!="") $condition.=" and c.sales_order_no like '%$ord_no%'";
        if ($system_id!="") $condition.=" and a.sys_number like '%$system_id%'";
    } else if($search_type==1) { // exact
        //if ($yd_job!="") $condition.=" and c.yd_job = '$yd_job'";
        if ($ord_no!="") $condition.=" and c.sales_order_no ='$ord_no'";
        if ($system_id!="") $condition.=" and a.sys_number = '$system_id'";
    } else if($search_type==2) { // Starts with
        //if ($yd_job!="") $condition.=" and c.yd_job like '$yd_job%'";
        if ($ord_no!="") $condition.=" and c.sales_order_no like '$ord_no%'";
        if ($system_id!="") $condition.=" and a.sys_number like '$system_id%'";
    } else if($search_type==3) { // Ends with
       // if ($yd_job!="") $condition.=" and c.yd_job like '%$yd_job'";
        if ($ord_no!="") $condition.=" and c.sales_order_no like '%$ord_no'";
        if ($system_id!="") $condition.=" and a.sys_number like '%$system_id'";
    }

    $color_arr = return_library_array('select id, color_name from lib_color where status_active=1 and is_deleted=0', 'id', 'color_name');

    /*$sql = "select d.id, a.mst_id, a.job_id, b.receive_date, b.yd_job, c.sales_order_no, d.delivery_id
            from yd_production_dtls a, yd_ord_mst b, yd_ord_dtls c, yd_delivery_mst d, yd_delivery_dtls e
            where a.status_active = 1 and b.status_active = 1 and c.status_active = 1 $condition and a.job_id = b.id and b.id=c.mst_id and e.mst_id = d.id and e.job_dtls_id = c.id and d.entry_form = 400
         and a.sales_order_no = e.sales_order_no
            group by d.id, a.mst_id, a.job_id, b.yd_job, c.sales_order_no, b.receive_date, a.quantity, d.delivery_id";*/

    $sql = "select a.id, a.sys_number,a.inspection_date,a.remarks,c.sales_order_no from yd_inspection_mst a, yd_inspection_dtls b, yd_ord_dtls c  where a.entry_form=443 and a.id=b.mst_id and b.job_dtls_id=c.id and a.status_active=1 and a.company_id=$company_id";

        /*$sql = "select c.id as ins_dtls_id, a.sales_order_no,b.style_ref, b.lot, b.count_id, b.yarn_type_id, b.yarn_composition_id, b.yd_color_id, a.job_dtls_id, a.product_id, b.sales_order_id, a.bobbin_type, a.winding_qty , c.winding_cone_qty, c.production_qty, c.inspection_status, c.cause
        from yd_production_dtls a, yd_ord_dtls b, yd_inspection_dtls c
        where a.status_active = 1 and b.status_active = 1 and c.mst_id = $insMstId and a.job_dtls_id = b.id c.job_dtls_id=a.job_dtls_id and c.sales_order_id=b.sales_order_id";

            $field_array_mst = 'id, entry_form, sys_number, sys_number_prefix, sys_number_prefix_num,  company_id, within_group, party_id, location_id, party_location_id, inspection_date, remarks, inserted_by, insert_date';
        $data_array_mst="(".$mstId.", ".$entryForm.", '".$new_system_id[0]."', '".$new_system_id[1]."', '".$new_system_id[2]."', ".$cbo_company_name.", ".$cbo_within_group.", ".$cbo_party_name.", ".$cbo_location_name.", ".$cbo_party_location.", '".$txt_inspection_date."', '".$txt_remarks."', ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."')";

        $field_array_dtls = 'id, mst_id, sales_order_id, job_dtls_id, winding_cone_qty, production_qty, inspection_status, cause, inserted_by, insert_date';*/

    // echo $sql;

    $arr=array(2=>$color_arr);

    echo create_list_view('list_view', 'System ID,Sales Order No,Inspection date', '150,150', 500, 300, 0, $sql, 'js_set_value', 'id', '', 1, '0,0,0', '', 'sys_number,sales_order_no,inspection_date', '', '', '0,0,0');

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
        $mstId = $data[1];
    }

    if($reqType == 1) 
	{    // new entry
        /*$sql = "select a.id, a.order_id, a.company_id, a.location_id, a.within_group, a.party_id, a.remarks, sum(b.quantity) as prod_qnty, a.order_id, a.order_no, a.booking_without_order, a.booking_type
        from yd_ord_mst a, yd_production_dtls b
        where a.status_active=1 and a.is_deleted=0 and a.id=$prodId and a.id=b.job_id
        group by a.id, a.order_id, a.company_id, a.location_id, a.within_group, a.party_id,a.remarks, a.order_id, a.order_no, a.booking_without_order, a.booking_type";*/
        $sql = "select c.id, a.company_id, a.location_id, a.party_id, sum(b.quantity) as prod_qty, c.style_ref, c.order_id, c.order_no, a.booking_without_order, a.booking_type
                from yd_production_mst a, yd_production_dtls b, yd_ord_dtls c
                where a.status_active = 1 and b.status_active = 1 and a.id = $prodId and a.entry_form=442 and a.id=b.mst_id and b.job_dtls_id = c.id
                group by c.id, a.company_id, a.location_id, a.party_id, b.quantity, c.style_ref, c.order_id, c.order_no, a.booking_without_order, a.booking_type";
    }
	 else 
	{
        $sql = "select id, entry_form, sys_number, sys_number_prefix, sys_number_prefix_num, company_id, within_group, party_id, location_id, party_location_id, inspection_date, remarks
        from yd_inspection_mst where status_active=1 and is_deleted=0 and id=$mstId ";
    }

     // echo $sql;

    $result = sql_select($sql);

    /*foreach ($result as $row) {
        $jobDtlsIds .= $row[csf('id')].',';
    }*/

    if ($reqType == 2) {
        echo "document.getElementById('txt_system_id').value = '".$result[0][csf('sys_number')]."';\n";
    }
    echo "document.getElementById('cbo_company_name').value = '".$result[0][csf('company_id')]."';\n";
    echo "document.getElementById('cbo_within_group').value = '".$result[0][csf('within_group')]."';\n";
	echo "document.getElementById('cbo_location_name').value = '".$result[0][csf('location_id')]."';\n";
    // echo "load_drop_down('requires/yd_inspection_controller', '".$result[0][csf('company_id')]."', 'load_drop_down_buyer', 'buyer_td');";
    // echo "load_drop_down('requires/yd_inspection_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location', 'location_td' );";
    //echo "document.getElementById('cbo_location_name').value = '".$result[0][csf('location_id')]."';\n";
	// echo "load_drop_down( 'requires/yd_order_entry_controller',".$row[csf("team_leader")]."+'_'+".$row[csf("company_id")].", 'load_drop_down_member', 'team_member_td');\n"; 
	 
	//echo "load_drop_down( 'requires/yd_inspection_controller', ".$result[0][csf('company_id')]."+'_'+".$result[0][csf('within_group')].", 'load_drop_down_buyer', 'buyer_td' );\n";
    echo "document.getElementById('cbo_party_name').value = '".$result[0][csf('party_id')]."';\n";
	echo "load_drop_down( 'requires/yd_inspection_controller',".$result[0][csf('party_id')]."+'_'+2, 'load_drop_down_location', 'party_location_td' );\n";
    echo "document.getElementById('cbo_party_location').value = '".$result[0][csf('party_location_id')]."';\n";
    echo "document.getElementById('txt_inspection_date').value = '".$result[0][csf('inspection_date')]."';\n";
    echo "document.getElementById('txt_remarks').value = '".$result[0][csf('remarks')]."';\n";
    echo "document.getElementById('hdn_update_id').value = '".$result[0][csf('id')]."';\n";
}

if($action == 'populate_dtls_data_from_search_popup')
 {
	$data = explode('**', $data);
    // print_r($data );
    $reqType=$data[0];
    $all_datas="";$all_data="";
    $all_data=$data[1];
    $all_datas=explode(",",$all_data);
    foreach($all_datas as $row){
        list($ord_mst,$ord_dtls,$batch_id,$batch_dtls_id)=explode("_",$row);
        $dtlsIdArrMst[$ord_mst]=$ord_mst;
        $dtlsIdArrDtls[$ord_dtls]=$ord_dtls;
        $dtlsIdArrBatchID[$batch_id]=$batch_id;
        $dtlsIdArrBatch_dtls[$batch_dtls_id]=$batch_dtls_id;

    }

     if($reqType == 1) {     // new entry
          $all_data=$data[1];
          $all_datas=explode(",",$all_data);
        foreach($all_datas as $row){
            list($ord_mst,$ord_dtls,$batch_id,$batch_dtls_id)=explode("_",$row);
            $dtlsIdArrMst[$ord_mst]=$ord_mst;
            $dtlsIdArrDtls[$ord_dtls]=$ord_dtls;
            $dtlsIdArrBatchID[$batch_id]=$batch_id;
            $dtlsIdArrBatch_dtls[$batch_dtls_id]=$batch_dtls_id;

        }
    } else {                // update mode
        $insMstId = $data[1];
    }


    // if($reqType == 1) {     // new entry
    //     $jobId = $data[1];
    // } else {                // update mode
    //     $insMstId = $data[1];
    // }

    $comp_arr = return_library_array("select id,composition_name from lib_composition_array where is_deleted=0 and status_active=1", 'id', 'composition_name');
 	$color_arr = return_library_array("select id,color_name from lib_color", "id", "color_name");
	$count_arr = return_library_array("Select id, yarn_count from  lib_yarn_count where  status_active=1", 'id', 'yarn_count');
	
    // if($reqType == 1) 
    // {    // new entry
    //     $sql = "select a.id as prod_dtls_id, a.sales_order_no,b.style_ref, b.lot, b.count_id, b.yarn_type_id, b.yarn_composition_id, b.yd_color_id, a.job_dtls_id, a.product_id, b.sales_order_id, a.bobbin_type, a.winding_qty
    //     from yd_production_dtls a, yd_ord_dtls b
    //     where a.status_active = 1 and b.status_active = 1 and a.mst_id = $jobId and a.job_dtls_id = b.id";
    // } 
    // else 
    // {    
    //     $sql = "select c.id as ins_dtls_id, b.sales_order_no,b.style_ref, b.lot, b.count_id, b.yarn_type_id, b.yarn_composition_id, b.yd_color_id, b. id as job_dtls_id, b.sales_order_id, c.winding_cone_qty, c.production_qty, c.inspection_status, c.cause
    //     from yd_ord_dtls b, yd_inspection_dtls c
    //     where  b.status_active = 1 and c.mst_id = $insMstId and c.job_dtls_id=b.id and c.sales_order_id=b.sales_order_id";
    // }

    if( $reqType==1){

        $sql = "SELECT a.id, a.order_no, a.yd_job, b.sales_order_no, b.lot, b.style_ref, b.yarn_type_id, b.count_id, b.yarn_composition_id, c.batch_number, b.yd_color_id,e.job_dtls_id, b.sales_order_id
        from yd_ord_mst a, yd_ord_dtls b left join yd_production_dtls e on e.job_dtls_id = b.id, yd_batch_mst c, yd_batch_dtls d
        where a.id=b.mst_id and b.order_id=c.order_id and c.id=d.mst_id and  a.status_active=1 and a.status_active=1 and b.status_active=1   and a.id in(".implode(',',$dtlsIdArrMst).") and b.id in(".implode(',',$dtlsIdArrDtls).") and c.id in(".implode(',',$dtlsIdArrBatchID).") and d.id in(".implode(',',$dtlsIdArrBatch_dtls).")  
        group by a.id, a.order_no, a.yd_job, b.sales_order_no, b.lot, b.style_ref, b.yarn_type_id, b.count_id, b.yarn_composition_id, c.batch_number, b.yd_color_id, e.job_dtls_id, b.sales_order_id";
    }else{
        $sql = "select c.id as ins_dtls_id, b.sales_order_no,b.style_ref, b.lot, b.count_id, b.yarn_type_id, b.yarn_composition_id, b. id as job_dtls_id, b.sales_order_id, c.winding_cone_qty, c.production_qty, c.inspection_status, c.cause, c.batch_number,c.batch_colour_id as yd_color_id
        from yd_ord_dtls b, yd_inspection_dtls c
        where  b.status_active = 1 and c.mst_id = $insMstId and c.job_dtls_id=b.id and c.sales_order_id=b.sales_order_id";
    }
    // echo $sql;
	$result = sql_select($sql);
    ?>
        <?php
        foreach($result as $row) {
            ?>
            <tr>
                <td>
                    <input name="txtSalesOrder[]" id="txtSalesOrder_<?php echo $sl; ?>" type="text" class="text_boxes" placeholder="Double Click" onDblClick="openSalesOrderPopup();" value="<?php echo $row[csf('sales_order_no')]; ?>" readonly style="width:117px" />
                </td>
                <td>
                    <input name="txtBatchNumber[]" id="txtBatchNumber_<?php echo $sl; ?>" type="text" class="text_boxes" placeholder="Display" value="<?php echo $row[csf('batch_number')]; ?>" readonly style="width:87px"/>
                </td>
                <td>
                    <input name="txtBatchColour[]" id="txtBatchColour_<?php echo $sl; ?>" type="text" class="text_boxes" placeholder="Display" value="<?php echo $row[csf('yd_color_id')]; ?>" readonly style="width:87px"/>
                </td>
                <td>
                    <input name="txtstyle[]" id="txtstyle_<?php echo $sl; ?>" type="text" class="text_boxes" placeholder="Display" value="<?php echo $row[csf('style_ref')]; ?>" readonly style="width:87px"/>
                </td>
                <td>
                    <input name="txtLot[]" id="txtLot_<?php echo $sl; ?>" type="text" class="text_boxes" placeholder="Display" value="<?php echo $row[csf('lot')]; ?>" readonly style="width:67px"/>
                </td>

                <td>
                    <input name="txtcount[]" id="txtcount_<?php echo $sl; ?>" type="text" class="text_boxes" placeholder="Display" value="<?php echo $count_arr[$row[csf('count_id')]]; ?>" readonly style="width:67px"/>
                </td>

                <td>
                    <input name="txtYarnType[]" id="txtYarnType_<?php echo $sl; ?>" type="text" class="text_boxes" placeholder="Display" value="<?php echo $yarn_type[$row[csf('yarn_type_id')]]; ?>" style="width:67px"readonly />
                </td>

                <td>
                    <input name="txtComposition[]" id="txtComposition_<?php echo $sl; ?>" type="text" class="text_boxes" readonly placeholder="Display" value="<?php echo $comp_arr[$row[csf('yarn_composition_id')]]; ?>" style="width:107px"/>
                </td>

                <td>
                    <input name="txtYDcolor[]" id="txtYDcolor_<?php echo $sl; ?>" type="text" class="text_boxes" readonly placeholder="Display" value="<?php echo $color_arr[$row[csf('yd_color_id')]]; ?>" style="width:67px" />
                </td>
                <td>
                    <input name="txtTension[]" id="txtTension_<?php echo $sl; ?>" class="text_boxes" type="text"  placeholder="Display" value="<?php //echo $row[csf('style_ref')]; ?>" readonly style="width:67px" />
                </td>
                <td>
                    <input name="txtWindConeQty[]" id="txtWindConeQty_<?php echo $sl; ?>" type="text"  class="text_boxes_numeric" value="<?php echo $row[csf('winding_cone_qty')]; ?>" onClick="" placeholder="Write" readonly style="width:67px" />
                </td>
                <td>
                    <input name="txtProductQty[]" id="txtProductQty_<?php echo $sl; ?>" type="text"  class="text_boxes_numeric" value="<?php echo $row[csf('production_qty')]; ?>" placeholder="Write" style="width:67px" />
                </td>
                <td>
                    <input name="txtInspectionSts[]" id="txtInspectionSts_<?php echo $sl; ?>" type="text"  class="text_boxes_numeric" value="<?php echo $row[csf('inspection_status')]; ?>" style="width:67px" />
                </td>
                <td>
                    <input name="txtCause[]" id="txtCause_<?php echo $sl; ?>" type="text"  class="text_boxes" style="width:67px" value="<?php echo $row[csf('cause')]; ?>" />
                    <input name="txtUpDtlsId[]" id="txtUpDtlsId_<?php echo $sl; ?>" type="hidden" value="<?php echo $row[csf('ins_dtls_id')]; ?>" />
                    <input name="txtJobDtlsId[]" id="txtJobDtlsId_<?php echo $sl; ?>" type="hidden" value="<?php echo $row[csf('job_dtls_id')]; ?>"/>
                    <input name="txtSalesOrdId[]" id="txtSalesOrdId_<?php echo $sl; ?>" type="hidden" value="<?php echo $row[csf('sales_order_id')]; ?>"/>
                    <input name="txtColorId[]" id="txtColorId_<?php echo $sl; ?>" type="hidden" value="<?php echo $row[csf('yd_color_id')]; ?>"/>
                </td>
            </tr>
            <?php
            $sl++;
        }

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
        $entryForm = 443;
        $con = connect();
        $mstId = return_next_id('id', 'yd_inspection_mst', 1);
        $dtlsId = return_next_id('id', 'yd_inspection_dtls', 1);
		 if($db_type==0) $insert_date_con="and YEAR(insert_date)=".date('Y',time())."";
        else if($db_type==2) $insert_date_con="and TO_CHAR(insert_date,'YYYY')=".date('Y',time())."";
        //$hdnOrderId = str_replace("'", '', $hdnOrderId);
  $new_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name),'', 'YDI' , date("Y",time()), 5, "select id,sys_number_prefix,sys_number_prefix_num from yd_inspection_mst where company_id=$cbo_company_name and entry_form=443 $insert_date_con order by id desc ", "sys_number_prefix", "sys_number_prefix_num" ));
  
 // echo "10**";
 // echo "select id,sys_no_prefix,sys_no_prefix_num from yd_inspection_mst where company_id=$cbo_company_name and entry_form=443 $insert_date_con order by id desc";
  //print_r($new_system_id); die;
       // $new_system_id = explode('*', return_mrr_number( str_replace("'", '', $cbo_company_name), '', 'YDI', date('Y',time()), 5, "select sys_no_prefix,sys_no_prefix_num from yd_inspection_mst where entry_form=443 and company_id=$cbo_company_name order by id desc", 'sys_no_prefix', 'sys_no_prefix_num' ));
        //$txt_inspection_date=change_date_format($txt_inspection_date);
        $txt_inspection_date=change_date_format($txt_inspection_date, "", "",1);

        if($db_type==0) {
            mysql_query("BEGIN");
        }
        $field_array_mst = 'id, entry_form, sys_number, sys_number_prefix, sys_number_prefix_num,  company_id, within_group, party_id, location_id, party_location_id, inspection_date, remarks, inserted_by, insert_date';
        $data_array_mst="(".$mstId.", ".$entryForm.", '".$new_system_id[0]."', '".$new_system_id[1]."', '".$new_system_id[2]."', ".$cbo_company_name.", ".$cbo_within_group.", ".$cbo_party_name.", ".$cbo_location_name.", ".$cbo_party_location.", '".$txt_inspection_date."', '".$txt_remarks."', ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."')";

        $field_array_dtls = 'id, mst_id, sales_order_id, job_dtls_id, winding_cone_qty, production_qty, inspection_status, batch_number, batch_colour_id, cause, inserted_by, insert_date';
        //data_all += "&txtSalesOrder_" + j + "='" + txtSalesOrder + "'&txtstyle_" + j + "='" + txtstyle+ "'&txtLot_" + j + "='" + txtLot+ "'&txtcount_" + j + "='" + txtcount + "'&txtYarnType_" + j + "='" + txtYarnType + "'&txtComposition_" + j + "='" + txtComposition + "'&txtYDcolor_" + j + "='" + txtYDcolor  + "'&txtTension_" + j + "='" + txtTension + "'&txtWindConeQty_" + j + "='" + txtWindConeQty + "'&txtProductQty_" + j + "='" + txtProductQty + "'&txtInspectionSts_" + j + "='" + txtInspectionSts + "'&txtCause_" + j + "='" + txtCause + "'&txtUpDtlsId_" + j + "='" + txtUpDtlsId + "'&txtJobDtlsId_" + j + "='" + txtJobDtlsId + "'&txtSalesOrdId_" + j + "='" + txtSalesOrdId + "'&txtColorId_" + j + "='" + txtColorId + "'";
        for($i=1; $i<=$total_row; $i++) {
            $txtSalesOrdId      = 'txtSalesOrdId_'.$i;
            $txtJobDtlsId       = 'txtJobDtlsId_'.$i;
            $txtWindConeQty     = 'txtWindConeQty_'.$i;
            $txtProductQty      = 'txtProductQty_'.$i;
            $txtInspectionSts   = 'txtInspectionSts_'.$i;
            $txtCause           = 'txtCause_'.$i;
            $txtBatchNumber     = 'txtBatchNumber_'.$i;
            $txtBatchColour     = 'txtBatchColour_'.$i;
            
            $data_array_dtls .= $add_comma ? ',' : ''; // if $add_comma is true, add a comma in the end of $data_array_dtls

            $data_array_dtls .= "(".$dtlsId.",".$mstId.",".$$txtSalesOrdId.",".$$txtJobDtlsId.",".$$txtWindConeQty.",".$$txtProductQty.",".$$txtInspectionSts.",".$$txtBatchNumber.",".$$txtBatchColour.",".$$txtCause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

            $add_comma = true; // first entry is done. add a comma for next entries
            $dtlsId++; // increment details id by 1
        }

        //echo "10**insert into yd_inspection_mst(".$field_array_mst.") values ".$data_array_mst; die;
        $rID = sql_insert('yd_inspection_mst', $field_array_mst, $data_array_mst, 0);

        $flag = ($flag && $rID);    // return true if $flag is true and mst table insert is successful

        // echo $flag, $rID;die;
        // echo "10**insert into yd_inspection_dtls(".$field_array_dtls.") values ".$data_array_dtls; die;
        $rID2 = sql_insert('yd_inspection_dtls', $field_array_dtls, $data_array_dtls, 0);

        //echo '10**'.$rID.'**'.$rID2;die;

        $flag = ($flag && $rID2);   // return true if $flag is true and dtls table insert is successful

        if($db_type==0) {
            if($flag) {
                mysql_query("COMMIT");
                echo '0**'.$new_system_id[0].'**'.$mstId;
            } else {
                mysql_query("ROLLBACK");
                echo '10**'.$hdnOrderId;
            }
        }
        else if($db_type==2) {
            if($flag) {
                oci_commit($con);
                echo '0**'.$new_system_id[0].'**'.$mstId;
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
        $txt_system_id = str_replace("'", '', $txt_system_id);

        if($db_type==0) mysql_query("BEGIN");
        $txt_inspection_date=change_date_format($txt_inspection_date, "", "",1);
        $field_array_mst = 'location_id*party_id*inspection_date*remarks*updated_by*update_date';
        $data_array_mst="".$cbo_location_name."*".$cbo_party_name."*'".$txt_inspection_date."'*'".$txt_remarks."'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'";

        $field_array_dtls = 'sales_order_id*job_dtls_id*winding_cone_qty*production_qty*inspection_status*cause*updated_by*update_date';

        for($i = 1; $i <= $total_row; $i++) {
            $txtSalesOrdId      = 'txtSalesOrdId_'.$i;
            $txtJobDtlsId       = 'txtJobDtlsId_'.$i;
            $txtWindConeQty     = 'txtWindConeQty_'.$i;
            $txtProductQty      = 'txtProductQty_'.$i;
            $txtInspectionSts   = 'txtInspectionSts_'.$i;
            $txtCause           = 'txtCause_'.$i;
            $txtUpDtlsId        = 'txtUpDtlsId_'.$i;

            $data_array_dtls[str_replace("'", '', $$txtUpDtlsId)] =explode("*",("".$$txtSalesOrdId."*".$$txtJobDtlsId."*".$$txtWindConeQty."*".$$txtProductQty."*".$$txtInspectionSts."*".$$txtCause."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
            $id_arr[]=str_replace("'", '', $$txtUpDtlsId);
        }

        // echo sql_update('yd_delivery_mst', $field_array_mst, $data_array_mst, 'id', $hdn_update_id, 0);
        $rID = sql_update('yd_inspection_mst', $field_array_mst, $data_array_mst, 'id', $hdn_update_id, 0);

        // echo $rID;die;

        $flag = ($flag && $rID);    // return true if $flag is true and mst table update is successful

        //echo '10**' . bulk_update_sql_statement('yd_inspection_dtls', 'id', $field_array_dtls, $data_array_dtls, $id_arr);die;

        $rID2 = execute_query(bulk_update_sql_statement('yd_inspection_dtls', 'id', $field_array_dtls, $data_array_dtls, $id_arr), 1);

        $flag = ($flag && $rID2);   // return true if $flag is true and dtls table update is successful
        //echo '10**'.$rID.'**'.$rID2;die;
        if($db_type==0) {
            if($flag) {
                mysql_query('COMMIT');
                echo '1**'.$txt_system_id.'**'.$hdn_update_id;
            } else {
                mysql_query('ROLLBACK');
                echo '6**'.$txt_system_id.'**'.$hdn_update_id;
            }
        }
        else if($db_type==2) {
            if($flag) {
                oci_commit($con);
                echo '1**'.$txt_system_id.'**'.$hdn_update_id;
            } else {
                oci_rollback($con);
                echo '6**'.$txt_system_id.'**'.$hdn_update_id;
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

function sql_updates($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$commit)
{

    $strQuery = "UPDATE ".$strTable." SET ";
    $arrUpdateFields=explode("*",$arrUpdateFields);
    $arrUpdateValues=explode("*",$arrUpdateValues);

    if(count($arrUpdateFields)!=count($arrUpdateValues)){
        return "0";
    }

    if(is_array($arrUpdateFields))
    {
        $arrayUpdate = array_combine($arrUpdateFields,$arrUpdateValues);
        $Arraysize = count($arrayUpdate);
        $i = 1;
        foreach($arrayUpdate as $key=>$value):
            $strQuery .= ($i != $Arraysize)? $key."=".$value.", ":$key."=".$value;
            $i++;
        endforeach;
    }
    else
    {
        $strQuery .= $arrUpdateFields."=".$arrUpdateValues;
    }
    $strQuery .=" WHERE ";

    $arrRefFields=explode("*",$arrRefFields);
    $arrRefValues=explode("*",$arrRefValues);
    if(is_array($arrRefFields))
    {
        $arrayRef = array_combine($arrRefFields,$arrRefValues);
        $Arraysize = count($arrayRef);
        $i = 1;
        foreach($arrayRef as $key=>$value):
            $strQuery .= ($i != $Arraysize)? $key."=".$value." AND ":$key."=".$value."";
            $i++;
        endforeach;
    }
    else
    {
        $strQuery .= $arrRefFields."=".$arrRefValues."";
    }
    return $strQuery ;
    global $con;
    if( strpos($strQuery, "WHERE")==false)  return "0";
    $stid =  oci_parse($con, $strQuery);
    $exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
    if ($exestd)
        return "1";
    else
        return "0";

    die;
    if ( $commit==1 )
    {
        if (!oci_error($stid))
        {
            oci_commit($con);
            return "1";
        }
        else
        {
            oci_rollback($con);
            return "10";
        }
    }
    else
        return 1;
    die;
}

?>