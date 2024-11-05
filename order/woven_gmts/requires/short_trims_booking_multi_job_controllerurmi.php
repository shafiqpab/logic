<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
include('../../../includes/class4/class.conditions.php');
include('../../../includes/class4/class.reports.php');
include('../../../includes/class4/class.trims.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');
//---------------------------------------------------- Start---------------------------------------------------------------------------
function load_drop_down_supplier($data){
	if($data==5 || $data==3){
		$cbo_supplier_name=create_drop_down( "cbo_supplier_name", 130, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "-- Select Company --", "", "get_php_form_data( this.value, 'load_drop_down_attention', 'requires/short_trims_booking_multi_job_controllerurmi');",0,"" );
	}
	else
	{
		$cbo_supplier_name= create_drop_down( "cbo_supplier_name", 130, "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id  and b.party_type=4 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name","id,supplier_name", 1, "--Select Supplier--",$selected,"get_php_form_data( this.value, 'load_drop_down_attention', 'requires/short_trims_booking_multi_job_controllerurmi');","");
	}
	return $cbo_supplier_name;
	exit();
}

if ($action=="populate_variable_setting_data"){
	$data_array=sql_select("select exeed_budge_qty,exeed_budge_amount,amount_exceed_level from variable_order_tracking where company_name='$data' and item_category_id=4 and variable_list=26 and status_active=1 and is_deleted=0");
	foreach ($data_array as $row){
		echo "document.getElementById('exeed_budge_qty').value = '".$row[csf("exeed_budge_qty")]."';\n";
		echo "document.getElementById('exeed_budge_amount').value = '".$row[csf("exeed_budge_amount")]."';\n";
		echo "document.getElementById('amount_exceed_level').value = '".$row[csf("amount_exceed_level")]."';\n";
	}
	$cbo_supplier_name     = create_drop_down( "cbo_supplier_name", 130, "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data' and b.party_type=4 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name","id,supplier_name", 1, "--Select Supplier--",$selected,"get_php_form_data( this.value, \'load_drop_down_attention\', \'requires/short_trims_booking_multi_job_controllerurmi\');","");
	$cbo_buyer_name= create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected,"","");

	echo "document.getElementById('supplier_td').innerHTML = '".$cbo_supplier_name."';\n";
	echo "document.getElementById('buyer_td').innerHTML = '".$cbo_buyer_name."';\n";
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=2 and report_id=57 and is_deleted=0 and status_active=1");
	echo "document.getElementById('report_ids').value = '".$print_report_format."';\n";
	echo "print_report_button_setting('".$print_report_format."');\n";
}

if ($action=="load_drop_down_buyer"){
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "","" );
	exit();
}

if ($action=="load_drop_down_supplier"){
	echo $action($data);
	exit();
}

if($action=="load_drop_down_attention"){
	$supplier_name=return_field_value("contact_person","lib_supplier","id ='".$data."' and is_deleted=0 and status_active=1");
	echo "document.getElementById('txt_attention').value = '".$supplier_name."';\n";
	exit();
}

if ($action=="requisition_popup")
{
	echo load_html_head_contents("Short Trims Requisition Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
	function set_checkvalue(){
			if(document.getElementById('chk_job_wo_po').value==0) document.getElementById('chk_job_wo_po').value=1;
			else document.getElementById('chk_job_wo_po').value=0;
	}
	function js_set_value( str_data ){
		document.getElementById('txt_booking').value=str_data;
		parent.emailwindow.hide();
	}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
            <input type="hidden" id="txt_booking" value="" />
            <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
                <table width="760" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                    <thead>
                        <tr>
                            <th colspan="9"><?=create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" ); ?></th>
                        </tr>
                        <tr>
                            <th width="130" class="must_entry_caption">Buyer Name</th>
                            <th width="100">Style Ref </th>
                            <th width="80">Job No </th>
                            <th width="80">Internal Ref.</th>
                            <th width="80">Order No</th>
                            <th width="70">Requisition No</th>
                            <th width="120" colspan="2"> Requisition Date Range</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td id="buyer_td"><?=create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- Select Buyer --", $selected, "","" ); ?></td>
                        <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:100px"></td>
                        <td><input name="txt_job" id="txt_job" class="text_boxes" style="width:70px"></td>
                        <td><input name="internal_ref" id="internal_ref" class="text_boxes" style="width:80px"></td>
                        <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:80px"></td>
                        <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:70px"></td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px"></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px"></td>
                        <td align="center">
                        	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( '<? echo $company_id; ?>'+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_job').value+'_'+document.getElementById('internal_ref').value, 'create_reqsn_search_list_view', 'search_div', 'short_trims_booking_multi_job_controllerurmi','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                        </td>
                    </tr>
                    <tr>
                        <td align="center" colspan="9" valign="middle">
                        <?=load_month_buttons(1); ?>
                        </td>
                    </tr>
                </table>
               <div id="search_div"></div>
            </form>
        </div>
	</body>
    <script>
		load_drop_down('short_trims_req_multi_job_controller', <?=$company_id; ?>, 'load_drop_down_buyer', 'buyer_td');
		document.getElementById('cbo_buyer_name').value=<?=$buyer_id; ?>;
		$("#cbo_buyer_name").attr("disabled",true);
    </script>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="create_reqsn_search_list_view")
{
	$data=explode('_',$data);
	//print_r($data);die;
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and c.buyer_name='$data[1]'"; else $buyer=set_user_lavel_filtering(' and c.buyer_name','buyer_id');
	$booking_year_cond=" and to_char(a.insert_date,'YYYY')=$data[4]";
	if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	

	if($data[6]==1){
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num ='$data[5]'   "; else $booking_cond="";
		if (trim($data[7])!="") $style_cond=" and c.style_ref_no ='$data[7]'";
		if (str_replace("'","",$data[8])!="") $order_cond=" and d.po_number = '$data[8]'  "; //else  $order_cond="";
		if (str_replace("'","",$data[9])!="") $job_cond=" and c.job_no_prefix_num = '$data[9]'  "; //else  $order_cond="";
	}
	if($data[6]==2){
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '$data[5]%'  $booking_year_cond  "; else $booking_cond="";
		if (trim($data[7])!="") $style_cond=" and c.style_ref_no like '$data[7]%'  "; //else  $style_cond="";
		if (str_replace("'","",$data[8])!="") $order_cond=" and d.po_number like '$data[8]%'  "; //else  $order_cond="";
		if (str_replace("'","",$data[9])!="") $job_cond=" and c.job_no_prefix_num like '$data[9]%'  "; //else  $order_cond="";
	}
	if($data[6]==3){
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[5]'  $booking_year_cond  "; else $booking_cond="";
		if (trim($data[7])!="") $style_cond=" and c.style_ref_no like '%$data[7]'"; //else  $style_cond="";
		if (str_replace("'","",$data[8])!="") $order_cond=" and d.po_number like '%$data[8]'  "; //else  $order_cond="";
		if (str_replace("'","",$data[9])!="") $job_cond=" and c.job_no_prefix_num like '%$data[9]'  "; //else  $order_cond="";
	}
	if($data[6]==4 || $data[6]==0){
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[5]%'  $booking_year_cond  "; else $booking_cond="";
		if (trim($data[7])!="") $style_cond=" and c.style_ref_no like '%$data[7]%'"; //else  $style_cond="";
		if (str_replace("'","",$data[8])!="") $order_cond=" and d.po_number like '%$data[8]%'  "; //else  $order_cond="";
		if (str_replace("'","",$data[9])!="") $job_cond=" and c.job_no_prefix_num like '%$data[9]%'  "; //else  $order_cond="";
	}
	
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$departmentArr=return_library_array( "select id, department_name from lib_department",'id','department_name');
	
	$approval_allow=sql_select("select b.id, b.page_id, b.approval_need, b.allow_partial, b.validate_page,a.setup_date from approval_setup_mst a, approval_setup_dtls b where a.id=b.mst_id and a.company_id='$data[0]' and a.status_active=1 and b.page_id=65 and b.status_active=1 and b.is_deleted=0 order by b.id desc ");
	if($approval_allow[0][csf("approval_need")]==1 && $approval_allow[0][csf("allow_partial")]==1){
		$approval_cond="and a.is_approved in (1)";
	}
	else if($approval_allow[0][csf("approval_need")]==1 && $approval_allow[0][csf("allow_partial")]==0){
		$approval_cond="and a.is_approved in (1)";
	}
	else if($approval_allow[0][csf("approval_need")]==1 && $approval_allow[0][csf("allow_partial")]==2){
		$approval_cond="and a.is_approved in (1,3)";
	}
	else{
		$approval_cond="";
	}
	
	//echo $data[10];
	
	$internal_ref_con='';
	if(!empty($data[10]))
	{
		$internal_ref_con=" and d.grouping like '%$data[10]%'";
	}
	$sql="select a.id, a.booking_no_prefix_num, a.booking_no, a.company_id, a.booking_date, a.delivery_date, a.responsible_dept, c.style_ref_no, d.po_number, a.pay_mode from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c, wo_po_break_down d where a.booking_no=b.booking_no and b.job_no=c.job_no and b.job_no=d.job_no_mst and b.po_break_down_id=d.id and a.booking_type=13 and a.entry_form=717 and a.is_short=1
	and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 $company $buyer $booking_date $booking_cond $style_cond $order_cond $job_cond $internal_ref_con $approval_cond group by a.id, a.booking_no_prefix_num, a.booking_no, a.company_id, a.booking_date, a.delivery_date, a.responsible_dept, c.style_ref_no, d.po_number, a.pay_mode order by a.id DESC";
	
	//echo $sql;
	?>
	<div width="900">
	   <table width="900" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="80">Reqsn. No</th>
                <th width="100">Company</th>
                <th width="140">Responsible Dept.</th>
                <th width="100">Reqsn. Date</th>
                <th width="100">Delivery Date</th>
				<?php
				if($data[10]==0)
				{?>
                <th width="150">Style Ref No</th>
                <th width="">PO NO</th>
				<? } ?>
            </tr>
        </thead>
		<tbody id="id="list_view"">
           
            <?
            $sl=1;
            $result=sql_select($sql);
            foreach($result as $row)
            {
				if ($sl%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$respDept="";
				$exresdept=explode(",",$row[csf("responsible_dept")]);
				foreach($exresdept as $respdeptid)
				{
					if($respDept=="") $respDept=$departmentArr[$respdeptid]; else $respDept.=','.$departmentArr[$respdeptid];
				}
				
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row[csf("booking_no")].'_'.$row[csf("id")]; ?>');" style="cursor:pointer">
                    <td width="30" align="center"><? echo $sl; ?></td>
                    <td width="80"><? echo $row[csf("booking_no_prefix_num")];?></td>
					<td width="100" style="word-break:break-all"><?=$comp[$row[csf("company_id")]];?></td>
					<td width="140" style="word-break:break-all"><?=$respDept; ?></td>
                    <td width="100"><? echo change_date_format($row[csf("booking_date")],"dd-mm-yyyy","-"); ?></td>
					<td width="100"><? echo change_date_format($row[csf("delivery_date")],"dd-mm-yyyy","-"); ?></td>
					<? if($data[10]==0) { ?>
					<td width="150" style="word-break:break-all"><? echo $row[csf("style_ref_no")]; ?> </td>
					<td width="" style="word-break:break-all"><? echo $row[csf("po_number")]; ?> </td>
				  <? } ?>
				</tr>
				<?
				$sl++;
            }
            ?>
		</tbody>
        </table>
    </div>
	<?
	exit();
}

if ($action=="populate_data_from_search_popup_requisition"){
	
	$sql= "select id, company_id, buyer_id, booking_no, booking_date, cbo_level, responsible_dept, responsible_person, division_id, ready_to_approved, reason, remarks, short_booking_available from wo_booking_mst where booking_no='$data' and status_active=1 and is_deleted=0";
	$data_array=sql_select($sql);
	
	$company_id=$data_array[0][csf("company_id")];
	foreach ($data_array as $row){
		echo "get_php_form_data(".$row[csf("company_id")].", 'populate_variable_setting_data', 'requires/short_trims_booking_multi_job_controllerurmi' );\n";
		echo "document.getElementById('txt_reqsn_no').value = '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('txt_reqsn_id').value = '".$row[csf("id")]."';\n";
		
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";
		//echo "document.getElementById('txt_req_date').value = '".change_date_format($row[csf("booking_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('cbo_level').value = '".$row[csf("cbo_level")]."';\n";
		echo "set_multiselect('cbo_responsible_dept','0','1','".$row[csf("responsible_dept")]."','0');\n";
		//echo "document.getElementById('cbo_responsible_dept').value = '".$row[csf("responsible_dept")]."';\n";
		echo "document.getElementById('txt_responsible_person').value = '".$row[csf("responsible_person")]."';\n";
		echo "document.getElementById('cbo_division_id').value = '".$row[csf("division_id")]."';\n";
		echo "document.getElementById('cbo_ready_to_approved').value = '".$row[csf("ready_to_approved")]."';\n";
		echo "document.getElementById('txt_reason').value = '".$row[csf("reason")]."';\n";
		echo "document.getElementById('txt_remark').value = '".$row[csf("remarks")]."';\n";
		
	}
	
	$sqlbookingdtls="select ID, PRE_COST_FABRIC_COST_DTLS_ID, JOB_NO, TRIM_GROUP, PO_BREAK_DOWN_ID from WO_BOOKING_DTLS where booking_no='$data' and status_active=1 and is_deleted=0";
	$sqlbookingRes=sql_select($sqlbookingdtls);
	$dtlsdataArr=array();
	foreach($sqlbookingRes as $drow)
	{
		$dtlsdataArr['bomid'][$drow["PRE_COST_FABRIC_COST_DTLS_ID"]]=$drow["PRE_COST_FABRIC_COST_DTLS_ID"];
		$dtlsdataArr['bookingdtlsid'][$drow["ID"]]=$drow["ID"];
		$dtlsdataArr['trimgroup'][$drow["TRIM_GROUP"]]=$drow["TRIM_GROUP"];
		$dtlsdataArr['poid'][$drow["PO_BREAK_DOWN_ID"]]=$drow["PO_BREAK_DOWN_ID"];
	}
	echo "fnc_generate_booking('".implode(",",$dtlsdataArr['bookingdtlsid'])."','".implode(",",$dtlsdataArr['poid'])."','".implode(",",$dtlsdataArr['bomid'])."',$company_id);\n";
	exit();
}

if ($action=="fnc_process_data"){
	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);

	$buyer_arr=return_library_array("select id, short_name from lib_buyer",'id','short_name');
	$trim_group_library= return_library_array("select id, item_name from lib_item_group",'id','item_name');
	?>
    <script>
		function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function onlyUnique(value, index, self) {
			return self.indexOf(value) === index;
		}

		var selected_id = new Array();
		var selected_name = new Array();
		var selected_item=new Array();
		var selected_po=new Array();

		function js_set_value( str ) {
			if($("#search"+str).css("display") !='none'){
				toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
				if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
					selected_id.push( $('#txt_individual_id' + str).val() );
					selected_name.push($('#txt_job_no' + str).val());
					selected_item.push($('#txt_trim_group_id' + str).val());
					selected_po.push($('#txt_po_id' + str).val());
				}
				else{
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i,1 );
					selected_item.splice( i,1 );
					selected_po.splice( i,1 );
				}
			}
			var id = '';
			var job = '';
			var txt_trim_group_id='';
			var txt_po_id='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				job += selected_name[i] + ',';
				txt_trim_group_id+=selected_item[i]+ ',';
				txt_po_id+=selected_po[i]+ ',';
			}
			id = id.substr( 0, id.length - 1 );
			job = job.substr( 0, job.length - 1 );
			txt_trim_group_id = txt_trim_group_id.substr( 0, txt_trim_group_id.length - 1 );
			txt_po_id = txt_po_id.substr( 0, txt_po_id.length - 1 );
			$('#txt_selected_id').val( id );
			$('#txt_job_id').val( job );
			$('#itemGroup').val( txt_trim_group_id );
			$('#txt_selected_po').val( txt_po_id );
		}

    </script>
	<div align="center" style="width:100%;" >
        <input type="hidden" id="txt_booking" value="" />
        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table width="750" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                <thead>
                    <tr>
                        <th width="120">Style Ref </th>
                        <th width="120">Job No </th>
                        <th width="100">Int. Ref. No </th>
                        <th width="100">Order No</th>
                        <th width="150">Item Name</th>
                        <th width="80">&nbsp;
                            <input type="hidden"  style="width:20px" name="txt_garments_nature" id="txt_garments_nature" value="<? echo $garments_nature;?>" />
                            <input type="hidden" name="cbo_booking_month" id="cbo_booking_month" value="<? echo $cbo_booking_month;?>" />
                            <input type="hidden" name="cbo_booking_year" id="cbo_booking_year" value="<? echo $cbo_booking_year;?>" />
                            <input type="hidden" name="cbo_company_name" id="cbo_company_name" value="<? echo $company_id;?>" />
                            <input type="hidden" style="width:20px" name="cbo_buyer_name" id="cbo_buyer_name" value="<? echo $cbo_buyer_name;?>" />
    
                            <input type="hidden" name="cbo_currency" id="cbo_currency" value="<? echo $cbo_currency;?>" />
                            <input type="hidden" name="cbo_currency_job" id="cbo_currency_job" value="<? echo $cbo_currency_job;?>" />
                            <input type="hidden" style="width:20px" name="cbo_supplier_name" id="cbo_supplier_name" value="<? echo $cbo_supplier_name;?>" />
                        </th>
                    </tr>
                </thead>
                <tr>
                    <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:120px"></td>
                    <td><input name="txt_job" id="txt_job" class="text_boxes" style="width:120px"></td>
                    <td><input name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:100px"></td>
                    <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:150px"></td>
                    <td><?=create_drop_down( "cbo_item", 150, "select a.id,a.item_name from  lib_item_group a where  a.status_active =1 and a.is_deleted=0 order by a.item_name","id,item_name", 1, "-- Select Item Name --", $selected, "",0 ); ?>	</td>
                    <td align="center">
                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('cbo_booking_month').value+'_'+document.getElementById('cbo_booking_year').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_currency').value+'_'+document.getElementById('cbo_currency_job').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_job').value+'_'+document.getElementById('cbo_item').value+'_'+document.getElementById('txt_ref_no').value+'_'+'<? echo $cbo_pay_mode; ?>', 'create_fnc_process_data', 'search_div', 'short_trims_booking_multi_job_controllerurmi','setFilterGrid(\'tbl_list_search\',-1)')" style="width:60px;" />
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="6" valign="middle">
                    <?
                    echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );
                   // echo load_month_buttons();
                    ?>
                    </td>
                </tr>
            </table>
            <div id="search_div"></div>
            </form>
        </div>
	   </div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="create_fnc_process_data"){
	?>
	<div style="width:1295px;" align="center" >
	<?
	extract($_REQUEST);

	$data=explode('_',$data);
	$company_id=$data[0];
	$cbo_buyer_name=$data[1];
	$cbo_supplier_name=$data[2];
	$cbo_booking_month=$data[3];

	$cbo_booking_year=$data[4];
	$cbo_year_selection=$data[5];
	$cbo_currency=$data[6];
	$cbo_currency_job=$data[7];
	//echo $company_id.'='.$cbo_buyer_name;
	$txt_style=$data[8];
	$txt_order_search=$data[9];
	 $txt_job=$data[10];
	$cbo_item=$data[11];
	$ref_no=$data[12];
	$cbo_pay_mode=$data[13];

	if ($txt_style!="") $style_cond=" and a.style_ref_no='$txt_style'"; else $style_cond=$txt_style;
	if ($txt_order_search!="") $order_cond=" and d.po_number='$txt_order_search'"; else $order_cond="";
	if ($ref_no!="") $ref_cond=" and d.grouping='$ref_no'"; else $ref_cond="";
	if ($txt_job!="") $job_cond=" and a.job_no_prefix_num='$txt_job'"; else $job_cond ="";
	if ($cbo_item!=0) $itemgroup_cond=" and c.trim_group=$cbo_item"; else $itemgroup_cond ="";

	$booking_month=0;
	if($cbo_booking_month<10){
		$booking_month.=$cbo_booking_month;
	}
	else{
		$booking_month=$cbo_booking_month;
	}

	$start_date=$cbo_booking_year."-".$booking_month."-01";
	$end_date=$cbo_booking_year."-".$booking_month."-".cal_days_in_month(CAL_GREGORIAN, $booking_month, $cbo_booking_year);

	if($db_type==0){
		if ($start_date!="" &&  $end_date!="") $shipment_date = "and d.pub_shipment_date between '".change_date_format($start_date, "yyyy-mm-dd", "-")."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}

	if($db_type==2){
		if ($start_date!="" &&  $end_date!="") $shipment_date = "and d.pub_shipment_date between '".change_date_format($start_date, "yyyy-mm-dd", "-",1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}
	?>
	<input type="hidden" name="txt_selected_id" id="txt_selected_id" value="" />
	<input type="hidden" name="itemGroup" id="itemGroup" value="" />
	<input type="hidden" name="txt_job_id" id="txt_job_id" value="" />
	<input type="hidden" name="txt_selected_po" id="txt_selected_po" value="" />
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1280" class="rpt_table"  >
        <thead>
            <th width="30">SL</th>
            <th width="50">Buyer</th>
            <th width="50">Year</th>
            <th width="50">Job No</th>
            <th width="60">File No</th>
            <th width="80">Ref. No</th>
            <th width="100">Style No</th>
            <th width="100">Ord. No</th>
            <th width="100">Trim Group</th>
            <th width="">Desc.</th>
            <th width="80">Brand/Sup.Ref</th>
            <th width="70">Req. Qnty</th>
            <th width="45">UOM</th>
            <th width="70">CU WOQ</th>
            <th width="70">Bal WOQ</th>
            <th width="45">Exch. Rate</th>
            <th width="40">Rate</th>
            <th width="70">Amount</th>
        </thead>
	</table>
	<div style="width:1300px; overflow-y:scroll; max-height:350px;" id="buyer_list_view">
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1280" class="rpt_table" id="tbl_list_search" >
	<?
	if($db_type==0){
		if ($start_date!="" &&  $end_date!="") $class_datecond = "between '".change_date_format($start_date, "yyyy-mm-dd", "-")."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'"; else $class_datecond ="";
	}

	if($db_type==2){
		if ($start_date!="" &&  $end_date!="") $class_datecond = "between '".change_date_format($start_date, "yyyy-mm-dd", "-",1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-",1)."'"; else $class_datecond ="";
	}

	$trim_group_library= return_library_array("select id, item_name from lib_item_group",'id','item_name');
	$sql_lib_item_group_array=array();
	$sql_lib_item_group=sql_select("select id, item_name,conversion_factor,order_uom as cons_uom from lib_item_group");
	foreach($sql_lib_item_group as $row_sql_lib_item_group){
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][item_name]=$row_sql_lib_item_group[csf('item_name')];
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][conversion_factor]=$row_sql_lib_item_group[csf('conversion_factor')];
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][cons_uom]=$row_sql_lib_item_group[csf('cons_uom')];
	}
	if($db_type==0) $year_field="YEAR(a.insert_date) as year";
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";

	 $condition= new condition();
	if(str_replace("'","",$company_id) !=''){
		$condition->company_name("=$company_id");
	}
	if(str_replace("'","",$cbo_buyer_name) !=''){
		$condition->buyer_name("=$cbo_buyer_name");
	}
	if(str_replace("'","",$txt_job) !=''){
		$condition->job_no_prefix_num("=$txt_job");
	}
	 if(str_replace("'","",$txt_order_search)!='')
			 {
				$condition->po_number("='$txt_order_search'");
			 }
	  if(str_replace("'","",$ref_no)!='')
	 {
		$condition->grouping("='$ref_no'");
	 }
	$condition->init();
	$trims= new trims($condition);
	//echo $trims->getQuery();
	//die;
	$req_qty_arr=$trims->getQtyArray_by_orderAndPrecostdtlsid();
	$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsid();
	//echo $cbo_buyer_name;die;
	$cu_booking_arr=array();
	$sql_cu_booking=sql_select("select c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id, sum(c.wo_qnty) as cu_wo_qnty, sum(c.amount) as cu_amount from wo_po_details_master a, wo_po_break_down  d , wo_booking_dtls c where a.garments_nature=3 and a.job_no=d.job_no_mst and a.job_no=c.job_no and  d.id=c.po_break_down_id and a.company_name=$company_id and a.buyer_name=$cbo_buyer_name  and c.status_active=1 and c.is_deleted=0 $job_cond $order_cond $ref_cond $style_cond group by a.job_no, c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id");
	foreach($sql_cu_booking as $row_cu_booking){
		$cu_booking_arr[$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_cu_booking[csf('po_break_down_id')]]['cu_wo_qnty']=$row_cu_booking[csf('cu_wo_qnty')];
		$cu_booking_arr[$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_cu_booking[csf('po_break_down_id')]]['cu_amount']=$row_cu_booking[csf('cu_amount')];
	}
	
	$approval_allow=sql_select("select b.id, b.page_id, b.approval_need, b.allow_partial, b.validate_page,a.setup_date from approval_setup_mst a,approval_setup_dtls b where a.id=b.mst_id and a.company_id='$company_id' and a.status_active=1 and b.page_id=37 and b.status_active=1 and b.is_deleted=0 order by b.id desc ");
	if($approval_allow[0][csf("approval_need")]==1 && $approval_allow[0][csf("allow_partial")]==1){
		$approval_cond="and b.approved in (1)";
	}
	else if($approval_allow[0][csf("approval_need")]==1 && $approval_allow[0][csf("allow_partial")]==0){
		$approval_cond="and b.approved in (1)";
	}
	else if($approval_allow[0][csf("approval_need")]==1 && $approval_allow[0][csf("allow_partial")]==2){
		$approval_cond="and b.approved in (1,3)";
	}
	else{
		$approval_cond="";
	}

	$nameArray=sql_select( "select editable,id from  variable_order_tracking where company_name='$company_id' and variable_list=94 order by id" );
	$short_fab_validation=$nameArray[0][csf('editable')];

	
	/*and
	(c.nominated_supp_multi = '$cbo_supplier_name' or c.nominated_supp_multi= 0)*/

	$sql="select a.job_no_prefix_num, a.job_no, $year_field, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id as wo_pre_cost_trim_cost_dtls, c.trim_group, c.description, c.brand_sup_ref, c.nominated_supp_multi, c.rate, d.id as po_id, d.po_number, d.file_no, d.grouping, d.po_quantity as plan_cut, min(e.id) as id, e.po_break_down_id, avg(e.cons) AS cons from wo_po_details_master a, wo_pre_cost_mst b, wo_pre_cost_trim_cost_dtls c, wo_po_break_down d, wo_pre_cost_trim_co_cons_dtls e where a.garments_nature=3 and a.job_no=b.job_no and a.job_no=c.job_no and a.job_no=d.job_no_mst and a.job_no=e.job_no and c.id=e.wo_pre_cost_trim_cost_dtls_id and d.id=e.po_break_down_id and a.company_name=$company_id and a.buyer_name=$cbo_buyer_name and d.is_deleted=0 and d.status_active=1 ".$buyer_cond_test." $itemgroup_cond $job_cond $order_cond $ref_cond $style_cond $approval_cond group by a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id, c.trim_group, c.description, c.nominated_supp_multi, c.brand_sup_ref, c.rate, a.insert_date, d.id, d.po_number, d.file_no, d.grouping, d.po_quantity, e.po_break_down_id order by d.id,c.id";

	//echo $sql;die;
	$i=1; $total_req=0; $total_amount=0;
	$nameArray=sql_select( $sql );
	foreach ($nameArray as $selectResult){
		if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		$supplier_arr = explode("_", $selectResult[csf('nominated_supp_multi')]);			 
		$supplier_arr_data=array();
		if(count($supplier_arr) >0)
		{
			if($cbo_pay_mode==3 || $cbo_pay_mode==5){
				$comsupplierdata_arr = explode(",", $supplier_arr[1]);
				if(count($comsupplierdata_arr) >0)
				{
					foreach ($comsupplierdata_arr as $value) {
						$supplier_arr_data[$value]=$value;
					}
				}
			}
			else{
				$supplierdata_arr = explode(",", $supplier_arr[0]);
				if(count($supplierdata_arr) >0)
				{
					foreach ($supplierdata_arr as $value) {
						$supplier_arr_data[$value]=$value;
					}
				}
			}				
		}
		
		if(array_key_exists($cbo_supplier_name, $supplier_arr_data) || $selectResult[csf('nominated_supp_multi')] =='' || $selectResult[csf('nominated_supp_multi')] ==0)
		{  
			$cbo_currency_job=$selectResult[csf('currency_id')];
			$exchange_rate=$selectResult[csf('exchange_rate')];
			if($cbo_currency==$cbo_currency_job) $exchange_rate=1;
			//echo $cbo_supplier_name.'='.$selectResult[csf('nominated_supp_multi')].', ';
			$req_qnty_cons_uom=$req_qty_arr[$selectResult[csf('po_id')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]];
			$req_amount_cons_uom=$req_amount_arr[$selectResult[csf('po_id')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]];
			$rate_cons_uom=$req_amount_cons_uom/$req_qnty_cons_uom;
			
			$req_qnty=def_number_format($req_qnty_cons_uom/$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor],5,"");
			$cu_wo_qnty=$cu_booking_arr[$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('po_id')]]['cu_wo_qnty'];
			$bal_woq=def_number_format($req_qnty-$cu_wo_qnty,5,"");
			
			$rate=def_number_format(($rate_cons_uom*$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor])*$exchange_rate,5,"");
			$req_amount=def_number_format($req_qnty*$rate,5,"");
			
			$total_req_amount+=$req_amount;
			$total_cu_amount+=$selectResult[csf('cu_amount')];
			
			$total_req+=$req_qnty;
			$amount=def_number_format($rate*$bal_woq,4,"");
			$total_amount+=$amount;
			//$ig=1; this comment open than permission aziz vai
			//echo $bal_woq.'--'.$cu_wo_qnty.'<br>';
			if($short_fab_validation==0){
				if($bal_woq <= 0 && ($cu_wo_qnty !="" || $cu_wo_qnty !=0)){
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
						<td width="30"><? echo $i;?>
							<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $selectResult[csf('id')]; ?>"/>
							<input type="hidden" name="txt_trim_group_id" id="txt_trim_group_id<?php echo $i ?>" value="<? echo $selectResult[csf('wo_pre_cost_trim_cost_dtls')]; ?>"/>
							<input type="hidden" name="txt_job_no" id="txt_job_no<?php echo $i ?>" value="<? echo $selectResult[csf('job_no')]; ?>"/>
							<input type="hidden" name="txt_po_id" id="txt_po_id<?php echo $i ?>" value="<? echo $selectResult[csf('po_id')]; ?>"/>
						</td>
						<td width="50" style="word-break:break-all"><? echo $buyer_arr [$selectResult[csf('buyer_name')]];?></td>
						<td width="50" style="word-break:break-all"><? echo $selectResult[csf('year')];?></td>
						<td width="50" style="word-break:break-all"><? echo $selectResult[csf('job_no_prefix_num')];?></td>
						<td width="60" style="word-break:break-all"><? echo $selectResult[csf('file_no')];?></td>
						<td width="80" style="word-break:break-all"><? echo $selectResult[csf('grouping')];?></td>
						<td width="100" style="word-break:break-all"><? echo $selectResult[csf('style_ref_no')];?></td>
						<td width="100" style="word-break:break-all"><? echo $selectResult[csf('po_number')];?></td>
						
						<td width="100"  style="word-break:break-all"><? echo $trim_group_library[$selectResult[csf('trim_group')]];?></td>
						<td width=""  style="word-break:break-all"><? echo $selectResult[csf('description')];?></td>
						<td width="80"  style="word-break:break-all"><? echo $selectResult[csf('brand_sup_ref')];?></td>
						<td width="70" align="right"><?=number_format($req_qnty,4); ?></td>
						<td width="45"><? echo $unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom]];?></td>
						<td width="70" align="right"><? echo def_number_format($cu_wo_qnty,5,"");?></td>
						<td width="70" align="right"><?=number_format($bal_woq,4); ?></td>
						<td width="45" align="right" style="word-break:break-all"><?=number_format($exchange_rate,2); ?></td>
						<td width="40" align="right" style="word-break:break-all"><?=number_format($rate,4); ?></td>
						<td width="70" align="right"><?=number_format($amount,2); ?></td>
					</tr>
					<?
					$i++;
				}
			}
			if($short_fab_validation==1){
				//if($bal_woq <= 0 && ($cu_wo_qnty !="" || $cu_wo_qnty !=0)){
					//&& $ig=0
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
						<td width="30"><? echo $i;?>
							<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $selectResult[csf('id')]; ?>"/>
							<input type="hidden" name="txt_trim_group_id" id="txt_trim_group_id<?php echo $i ?>" value="<? echo $selectResult[csf('wo_pre_cost_trim_cost_dtls')]; ?>"/>
							<input type="hidden" name="txt_job_no" id="txt_job_no<?php echo $i ?>" value="<? echo $selectResult[csf('job_no')]; ?>"/>
							<input type="hidden" name="txt_po_id" id="txt_po_id<?php echo $i ?>" value="<? echo $selectResult[csf('po_id')]; ?>"/>
						</td>
						<td width="50" style="word-break:break-all"><? echo $buyer_arr [$selectResult[csf('buyer_name')]];?></td>
						<td width="50" style="word-break:break-all"><? echo $selectResult[csf('year')];?></td>
						<td width="50" style="word-break:break-all"><? echo $selectResult[csf('job_no_prefix_num')];?></td>
						<td width="60" style="word-break:break-all"><? echo $selectResult[csf('file_no')];?></td>
						<td width="80" style="word-break:break-all"><? echo $selectResult[csf('grouping')];?></td>
						<td width="100" style="word-break:break-all"><? echo $selectResult[csf('style_ref_no')];?></td>
						<td width="100" style="word-break:break-all"><? echo $selectResult[csf('po_number')];?></td>
						
						<td width="100"  style="word-break:break-all"><? echo $trim_group_library[$selectResult[csf('trim_group')]];?></td>
						<td width=""  style="word-break:break-all"><? echo $selectResult[csf('description')];?></td>
						<td width="80"  style="word-break:break-all"><? echo $selectResult[csf('brand_sup_ref')];?></td>
						<td width="70" align="right"><?=number_format($req_qnty,4); ?></td>
						<td width="45"><? echo $unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom]];?></td>
						<td width="70" align="right"><? echo def_number_format($cu_wo_qnty,5,"");?></td>
						<td width="70" align="right"><?=number_format($bal_woq,4); ?></td>
						<td width="45" align="right" style="word-break:break-all"><?=number_format($exchange_rate,2); ?></td>
						<td width="40" align="right" style="word-break:break-all"><?=number_format($rate,4); ?></td>
						<td width="70" align="right"><?=number_format($amount,2); ?></td>
					</tr>
					<?
					$i++;
				//}
			}
			if($short_fab_validation==2){			
				if($bal_woq <=1 && $cu_wo_qnty>0){
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
						<td width="30"><? echo $i;?>
							<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $selectResult[csf('id')]; ?>"/>
							<input type="hidden" name="txt_trim_group_id" id="txt_trim_group_id<?php echo $i ?>" value="<? echo $selectResult[csf('wo_pre_cost_trim_cost_dtls')]; ?>"/>
							<input type="hidden" name="txt_job_no" id="txt_job_no<?php echo $i ?>" value="<? echo $selectResult[csf('job_no')]; ?>"/>
							<input type="hidden" name="txt_po_id" id="txt_po_id<?php echo $i ?>" value="<? echo $selectResult[csf('po_id')]; ?>"/>
						</td>
						<td width="50" style="word-break:break-all"><? echo $buyer_arr [$selectResult[csf('buyer_name')]];?></td>
						<td width="50" style="word-break:break-all"><? echo $selectResult[csf('year')];?></td>
						<td width="50" style="word-break:break-all"><? echo $selectResult[csf('job_no_prefix_num')];?></td>
						<td width="60" style="word-break:break-all"><? echo $selectResult[csf('file_no')];?></td>
						<td width="80" style="word-break:break-all"><? echo $selectResult[csf('grouping')];?></td>
						<td width="100" style="word-break:break-all"><? echo $selectResult[csf('style_ref_no')];?></td>
						<td width="100" style="word-break:break-all"><? echo $selectResult[csf('po_number')];?></td>
						
						<td width="100" style="word-break:break-all"><? echo $trim_group_library[$selectResult[csf('trim_group')]];?></td>
						<td width="" style="word-break:break-all"><? echo $selectResult[csf('description')];?></td>
						<td width="80" style="word-break:break-all"><? echo $selectResult[csf('brand_sup_ref')];?></td>
						<td width="70" align="right"><?=number_format($req_qnty,4); ?></td>
						<td width="45"><? echo $unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom]];?></td>
						<td width="70" align="right"><? echo def_number_format($cu_wo_qnty,5,"");?></td>
						<td width="70" align="right"><?=number_format($bal_woq,4); ?></td>
						<td width="45" align="right" style="word-break:break-all"><?=number_format($exchange_rate,2); ?></td>
						<td width="40" align="right" style="word-break:break-all"><?=number_format($rate,4); ?></td>
						<td width="70" align="right"><?=number_format($amount,2); ?></td>
					</tr>
					<?
					$i++;
				}				
			}
		}
	}//Supplier chk
	?>
        <tfoot>
            <th width="30">&nbsp;</th>
            <th width="50">&nbsp;</th>
            <th width="50">&nbsp;</th>
            <th width="50">&nbsp;</th>
            <th width="60">&nbsp;</th>
            <th width="80">&nbsp;</th>
            <th width="100">&nbsp;</th>
            <th width="100">&nbsp;</th>
            <th width="100">&nbsp;</th>
            <th width="">&nbsp;</th>
            <th width="80">&nbsp;</th>
            <th width="70" id="value_total_req">&nbsp;</th>
            <th width="45"><input type="hidden" style="width:40px"  id="txt_tot_req_amount" value="<? echo number_format($total_req_amount,2); ?>" /></th>
            <th width="70"><input type="hidden" style="width:40px" id="txt_tot_cu_amount" value="<? echo number_format($total_cu_amount,2); ?>" /></th>
            <th width="70">&nbsp;</th>
            <th width="45">&nbsp;</th>
            <th width="40">&nbsp;</th>
            <th width="70" id="value_total_amount"><? echo number_format($total_amount,2); ?></th>
        </tfoot>
	</table>
	</div>
	<table width="1280" cellspacing="0" cellpadding="0" style="border:none" align="center">
        <tr>
            <td align="center" valign="bottom">
                <div style="width:100%">
                    <div style="width:50%; float:left" align="left">
                    	<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data();" /> Check / Uncheck All
                    </div>
                    <div style="width:50%; float:left" align="left">
                    	<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                    </div>
                </div>
            </td>
        </tr>
	</table>
	<script>
	var tableFilters = {
		col_operation: {
			id: ["value_total_req","value_total_amount"],
			col: [11,17],
			operation: ["sum","sum"],
			write_method: ["innerHTML","innerHTML"]
		}
	}
	setFilterGrid('tbl_list_search',-1,tableFilters)
	</script>
	</div>
	</div>
	</body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
    <?
	exit();
}

if ($action=="generate_fabric_booking"){
	extract($_REQUEST);
	$trim_group_library= return_library_array("select id, item_name from lib_item_group",'id','item_name');
	
	if($garments_nature==0) $garment_nature_cond=""; else $garment_nature_cond=" and a.garments_nature=$garments_nature";

	$param=implode(",",array_unique(explode(",",str_replace("'","",$param))));
	$data=implode(",",array_unique(explode(",",str_replace("'","",$data))));
	$pre_cost_id=implode(",",array_unique(explode(",",str_replace("'","",$pre_cost_id))));

	$sql_lib_item_group_array=array();
	$sql_lib_item_group=sql_select("select id, item_name,conversion_factor,order_uom as cons_uom from lib_item_group");
	foreach($sql_lib_item_group as $row_sql_lib_item_group){
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][item_name]=$row_sql_lib_item_group[csf('item_name')];
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][conversion_factor]=$row_sql_lib_item_group[csf('conversion_factor')];
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][cons_uom]=$row_sql_lib_item_group[csf('cons_uom')];
	}

	
	$condition= new condition();
	if(str_replace("'","",$data) !=''){
		$condition->po_id("in($data)");
	}

	$condition->init();
	$trims= new trims($condition);
	$req_qty_arr=$trims->getQtyArray_by_orderAndPrecostdtlsid();
	$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsid();

	
	$sql_cu_booking=sql_select("select C.JOB_NO, C.PRE_COST_FABRIC_COST_DTLS_ID as BOMID, C.PO_BREAK_DOWN_ID, c.ENTRY_FORM_ID, SUM(C.WO_QNTY) AS CU_WO_QNTY, SUM(C.AMOUNT) AS CU_AMOUNT from wo_po_details_master a, wo_po_break_down d, wo_booking_dtls c where a.job_no=d.job_no_mst and a.job_no=c.job_no and d.id=c.po_break_down_id and a.company_name=$cbo_company_name and c.pre_cost_fabric_cost_dtls_id in ($pre_cost_id) and c.status_active=1 and c.is_deleted=0 group by c.job_no, c.pre_cost_fabric_cost_dtls_id, c.po_break_down_id, c.entry_form_id");
	
	$cu_booking_arr=array(); $cu_reqqty_arr=array();
	foreach($sql_cu_booking as $row_cu_booking){
		if($row_cu_booking['ENTRY_FORM_ID']==717)
		{
			$cu_reqqty_arr[$row_cu_booking['JOB_NO']][$row_cu_booking['BOMID']]['cu_woq'][$row_cu_booking['PO_BREAK_DOWN_ID']] = $row_cu_booking['CU_WO_QNTY'];
			$cu_reqqty_arr[$row_cu_booking['JOB_NO']][$row_cu_booking['BOMID']]['cu_amount'][$row_cu_booking['PO_BREAK_DOWN_ID']] = $row_cu_booking['CU_AMOUNT'];
		}
		else
		{
			$cu_booking_arr[$row_cu_booking['JOB_NO']][$row_cu_booking['BOMID']]['cu_woq'][$row_cu_booking['PO_BREAK_DOWN_ID']] = $row_cu_booking['CU_WO_QNTY'];
			$cu_booking_arr[$row_cu_booking['JOB_NO']][$row_cu_booking['BOMID']]['cu_amount'][$row_cu_booking['PO_BREAK_DOWN_ID']] = $row_cu_booking['CU_AMOUNT'];
		}
	}
	
	if($basis_id==2)
	{
		$sql="select A.JOB_NO_PREFIX_NUM, A.JOB_NO, A.COMPANY_NAME, A.BUYER_NAME, A.CURRENCY_ID, A.STYLE_REF_NO, B.COSTING_PER, B.EXCHANGE_RATE, C.ID AS TRIMSBOMID, C.TRIM_GROUP, C.DESCRIPTION, C.BRAND_SUP_REF, C.COUNTRY, C.RATE, D.ID AS PO_ID, D.PO_NUMBER, D.PO_QUANTITY AS PLAN_CUT, MIN(E.ID) AS ID, AVG(E.CONS) AS CONS,
		
		0 AS CU_WOQ, 0 CU_AMOUNT, '' AS BOOKING_ID, 0 as SENSITIVITY, '' as DELIVERY_DATE
		
		from wo_po_details_master a, wo_pre_cost_mst b, wo_pre_cost_trim_cost_dtls c, wo_po_break_down d, wo_pre_cost_trim_co_cons_dtls e
		where a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and c.id=e.wo_pre_cost_trim_cost_dtls_id and d.id=e.po_break_down_id and a.company_name=$cbo_company_name $garment_nature_cond and e.id in($param) and e.po_break_down_id in($data) and c.id in($pre_cost_id) and d.is_deleted=0 and d.status_active=1
		group by a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id, c.trim_group, c.description, c.brand_sup_ref, c.country, c.rate, d.id, d.po_number, d.po_quantity order by d.id, c.id";
	}
	else
	{
		$sql="select A.JOB_NO_PREFIX_NUM, A.JOB_NO, A.COMPANY_NAME, A.BUYER_NAME, A.CURRENCY_ID, A.STYLE_REF_NO, B.COSTING_PER, B.EXCHANGE_RATE, C.ID AS TRIMSBOMID, C.TRIM_GROUP, C.BRAND_SUP_REF, C.COUNTRY, C.RATE, D.ID AS PO_ID, D.PO_NUMBER, D.PO_QUANTITY AS PLAN_CUT, MIN(E.ID) AS ID, AVG(E.CONS) AS CONS, SUM(g.REQUIRMENT) AS CU_WOQ, SUM(g.AMOUNT) AS CU_AMOUNT, F.ID AS BOOKING_ID, F.SENSITIVITY, F.DELIVERY_DATE, G.DESCRIPTION
	
		from wo_po_details_master a, wo_pre_cost_mst b, wo_pre_cost_trim_cost_dtls c, wo_po_break_down d, wo_pre_cost_trim_co_cons_dtls e, wo_booking_dtls f
		left join wo_trim_book_con_dtls g on g.wo_trim_booking_dtls_id=f.id
		where a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.job_no=f.job_no and c.id=e.wo_pre_cost_trim_cost_dtls_id and d.id=e.po_break_down_id and e.wo_pre_cost_trim_cost_dtls_id= f.pre_cost_fabric_cost_dtls_id and e.po_break_down_id=f.po_break_down_id and f.booking_type=13 and f.booking_no='$txt_reqsn_no' and
		f.id in($param) and a.company_name=$cbo_company_name and e.wo_pre_cost_trim_cost_dtls_id in ($pre_cost_id) and d.is_deleted=0 and d.status_active=1 and f.status_active=1 and f.is_deleted=0
	
		group by a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id, c.trim_group, c.brand_sup_ref, c.country, c.rate, d.id, d.po_number, d.po_quantity, e.po_break_down_id, f.id, f.sensitivity, f.delivery_date, g.description
		order by d.id, c.id";
	}
	
	//echo $sql; die;

	$job_and_trimgroup_level=array(); $i=1;
	$nameArray=sql_select( $sql );
	foreach ($nameArray as $row){
		$cbo_currency_job=$row['CURRENCY_ID'];
		$exchange_rate=$row['EXCHANGE_RATE'];
		if($cbo_currency==$cbo_currency_job){
			$exchange_rate=1;
		}

		$req_qnty_cons_uom=$req_qty_arr[$row['PO_ID']][$row['TRIMSBOMID']];
		$req_amount_cons_uom=$req_amount_arr[$row['PO_ID']][$row['TRIMSBOMID']];
		$rate_cons_uom=$req_amount_cons_uom/$req_qnty_cons_uom;

		$req_qnty_ord_uom=def_number_format($req_qnty_cons_uom/$sql_lib_item_group_array[$row['TRIM_GROUP']][conversion_factor],5,"");
		$rate_ord_uom=def_number_format(($rate_cons_uom*$sql_lib_item_group_array[$row['TRIM_GROUP']][conversion_factor])*$exchange_rate,5,"");
		$req_amount_ord_uom=def_number_format($req_qnty_ord_uom*$rate_ord_uom,5,"");

		$cu_woq=$cu_booking_arr[$row['JOB_NO']][$row['TRIMSBOMID']]['cu_woq'][$row['PO_ID']];
		$cu_amount=$cu_booking_arr[$row['JOB_NO']][$row['TRIMSBOMID']]['cu_amount'][$row['PO_ID']];
		
		$reqsn_qty=$reqsn_amount=0;
		if($basis_id==1)
		{
			$reqsn_qty=$cu_reqqty_arr[$row['JOB_NO']][$row['TRIMSBOMID']]['cu_woq'][$row['PO_ID']];
			$reqsn_amount=$cu_reqqty_arr[$row['JOB_NO']][$row['TRIMSBOMID']]['cu_amount'][$row['PO_ID']];
			$txt_delivery_date=change_date_format($row['DELIVERY_DATE']);
		}

		$bal_woq=def_number_format($req_qnty_ord_uom-$cu_woq,5,"");
		$amount=def_number_format($rate_ord_uom*$bal_woq,5,"");
		if($row['COUNTRY']=="") $row['COUNTRY']=0;
		if($row['BRAND_SUP_REF']=="") $row['BRAND_SUP_REF']=0;

		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMSBOMID']]['job_no'][$row['PO_ID']]=$row['JOB_NO'];
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMSBOMID']]['po_id'][$row['PO_ID']]=$row['PO_ID'];
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMSBOMID']]['po_number'][$row['PO_ID']]=$row['PO_NUMBER'];
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMSBOMID']]['country'][$row['PO_ID']]=$row['COUNTRY'];
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMSBOMID']]['description'][$row['PO_ID']]=$row['DESCRIPTION'];
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMSBOMID']]['brand_sup_ref'][$row['PO_ID']]=$row['BRAND_SUP_REF'];
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMSBOMID']]['trim_group'][$row['PO_ID']]=$row['TRIM_GROUP'];
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMSBOMID']]['trim_group_name'][$row['PO_ID']]=$trim_group_library[$row['TRIM_GROUP']];
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMSBOMID']]['wo_pre_cost_trim_cost_dtls'][$row['PO_ID']]=$row['TRIMSBOMID'];

		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMSBOMID']]['req_qnty'][$row['PO_ID']]=$req_qnty_ord_uom;
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMSBOMID']]['uom'][$row['PO_ID']]=$sql_lib_item_group_array[$row['TRIM_GROUP']][cons_uom];
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMSBOMID']]['uom_name'][$row['PO_ID']]=$unit_of_measurement[$sql_lib_item_group_array[$row['TRIM_GROUP']][cons_uom]];
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMSBOMID']]['req_amount'][$row['PO_ID']]=$req_amount_ord_uom;

		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMSBOMID']]['cu_woq'][$row['PO_ID']]=$cu_woq;
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMSBOMID']]['cu_amount'][$row['PO_ID']]=$cu_amount;
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMSBOMID']]['bal_woq'][$row['PO_ID']]=$bal_woq;
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMSBOMID']]['exchange_rate'][$row['PO_ID']]=$exchange_rate;
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMSBOMID']]['rate'][$row['PO_ID']]=$rate_ord_uom;
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMSBOMID']]['amount'][$row['PO_ID']]=$amount;
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMSBOMID']]['txt_delivery_date'][$row['PO_ID']]=$txt_delivery_date;
		
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMSBOMID']]['reqsn_qty'][$row['PO_ID']]=$reqsn_qty;
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMSBOMID']]['reqsn_amount'][$row['PO_ID']]=$reqsn_amount;
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMSBOMID']]['sensitivity'][$row['PO_ID']]=$row['SENSITIVITY'];
	}
	//echo "<pr>";
	//print_r($job_and_trimgroup_level['OG-23-01735'][76440]); die;
	?>
	<input type="hidden" id="strdata" value='<?=json_encode($job_and_trimgroup_level); ?>' style="background-color:#CCC"/>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1250" class="rpt_table" >
        <thead>
            <th width="30">SL</th>
            <th width="80">Job No</th>
            <th width="100">Ord. No</th>
            <th width="100">Trims Group</th>
            <th width="150">Description</th>
            <th width="70">Req. Qty</th>
            <th width="50">UOM</th>
            <th width="80">CU WO Qty</th>
            <th width="80">Bal WO Qty</th>
            <th width="100">Sensitivity</th>
            <th width="80">WO Qty</th>
            <th width="55">Exch.Rate</th>
            <th width="80">Rate</th>
            <th width="80">Amount</th>
            <th>Delv. Date</th>
        </thead>
	</table>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1250" class="rpt_table" id="tbl_list_search" >
        <tbody>
        <?
        if($cbo_level==1){
			foreach ($nameArray as $selectResult){
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$cbo_currency_job=$selectResult['CURRENCY_ID'];
				$exchange_rate=$selectResult['EXCHANGE_RATE'];
				if($cbo_currency == $cbo_currency_job){
					$exchange_rate=1;
				}
			
				$req_qnty_cons_uom = $req_qty_arr[$selectResult['PO_ID']][$selectResult['TRIMSBOMID']];
				$req_amount_cons_uom = $req_amount_arr[$selectResult['PO_ID']][$selectResult['TRIMSBOMID']];
				$rate_cons_uom = $req_amount_cons_uom/$req_qnty_cons_uom;
			
				$req_qnty_ord_uom = def_number_format($req_qnty_cons_uom/$sql_lib_item_group_array[$selectResult['TRIM_GROUP']][conversion_factor],5,"");
				$rate_ord_uom = def_number_format(($rate_cons_uom*$sql_lib_item_group_array[$selectResult['TRIM_GROUP']][conversion_factor])*$exchange_rate,5,"");
				$rate_ord_uom=number_format($rate_ord_uom,10,'.','');
				$req_amount_ord_uom = def_number_format($req_qnty_ord_uom*$rate_ord_uom,5,"");
			
				$cu_woq = $cu_booking_arr[$selectResult['JOB_NO']][$selectResult['TRIMSBOMID']]['cu_woq'][$selectResult['PO_ID']];
				$cu_amount = $cu_booking_arr[$selectResult['JOB_NO']][$selectResult['TRIMSBOMID']]['cu_amount'][$selectResult['PO_ID']];
				
				$bal_woq = def_number_format($req_qnty_ord_uom-$cu_woq,5,"");
				$amount = def_number_format($bal_woq*$rate_ord_uom,5,"");
				
				$reqsn_qty=$reqsn_amount=0;
				if($basis_id==1)
				{
					$txt_delivery_date=$selectResult['DELIVERY_DATE'];
					$reqsn_qty = $cu_reqqty_arr[$selectResult['JOB_NO']][$selectResult['TRIMSBOMID']]['cu_woq'][$selectResult['PO_ID']];
					$reqsn_amount = $cu_reqqty_arr[$selectResult['JOB_NO']][$selectResult['TRIMSBOMID']]['cu_amount'][$selectResult['PO_ID']];
					
					$amount =$reqsn_amount;
				}
				?>
				<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i; ?>">
                    <td width="30" align="center"><?=$i; ?></td>
                    <td width="80"><?=$selectResult['JOB_NO']; ?><input type="hidden" id="txtjob_<?=$i; ?>" value="<?=$selectResult['JOB_NO'];?>" style="width:30px"/></td>
                    <td width="100"><?=$selectResult['PO_NUMBER']; ?>
                        <input type="hidden" id="txtbookingid_<?=$i; ?>" readonly/>
                        <input type="hidden" id="txtpoid_<?=$i; ?>" value="<?=$selectResult['PO_ID'];?>" readonly/>
                        <input type="hidden" id="txtcountry_<?=$i; ?>" value="<?=$selectResult['COUNTRY'] ?>" readonly />
                        <input type="hidden" id="txtdesc_<?=$i; ?>" value="<?=$selectResult['DESCRIPTION'];?>" readonly />
                        <input type="hidden" id="txtbrandsup_<?=$i; ?>" value="<?=$selectResult['BRAND_SUP_REF'];?>" readonly />
                    </td>
                    <td width="100" title="<?=$sql_lib_item_group_array[$selectResult['TRIM_GROUP']][conversion_factor]; ?>"><?=$trim_group_library[$selectResult['TRIM_GROUP']]; ?>
                        <input type="hidden" id="txttrimcostid_<?=$i; ?>" value="<?=$selectResult['TRIMSBOMID']; ?>" readonly/>
                        <input type="hidden" id="txttrimgroup_<?=$i; ?>" value="<?=$selectResult['TRIM_GROUP']; ?>" readonly/>
                    </td>
                    <td width="150"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; background-color:<?=$bgcolor; ?>" id="txtdescid_<?=$i; ?>" value="<?=$selectResult['DESCRIPTION'];?>" /></td>
                    <td width="70" align="right">
                        <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtreqqnty_<?=$i; ?>" value="<?=number_format($req_qnty_ord_uom,4,'.',''); ?>" readonly />
                        <input type="hidden" id="txtreqamount_<?=$i; ?>" value="<?=number_format($req_amount_ord_uom,4,'.',''); ?>" />
                    </td>
                    <td width="50"><?=$unit_of_measurement[$sql_lib_item_group_array[$selectResult['TRIM_GROUP']][cons_uom]]; ?>
                    	<input type="hidden" id="txtuom_<?=$i; ?>" value="<?=$sql_lib_item_group_array[$selectResult['TRIM_GROUP']][cons_uom]; ?>" />
                    </td>
                    <td width="80" align="right">
                        <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<?=$bgcolor; ?>" id="txtcuwoq_<?=$i; ?>" value="<?=number_format($selectResult['cu_woq'],4,'.',''); ?>" readonly />
                        <input type="hidden" id="txtcuamount_<?=$i; ?>" value="<?=number_format($selectResult['cu_amount'],4,'.',''); ?>" readonly />
                    </td>
                    <td width="80" align="right"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtbalwoq_<?=$i; ?>" value="<?=number_format($bal_woq,4,'.',''); ?>" readonly /></td>
                    <td width="100" align="right"><?=create_drop_down( "cbocolorsizesensitive_".$i, 100, $size_color_sensitive,"", 1, "--Select--", $selectResult['SENSITIVITY'], "set_cons_break_down($i), copy_value(this.value,'cbocolorsizesensitive_',$i)","","1,2,3,4" ); ?></td>
                    <td width="80" align="right"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoq_<?=$i; ?>" value="<?=number_format($reqsn_qty,4,'.','');?>" onClick="open_consumption_popup('requires/short_trims_booking_multi_job_controllerurmi.php?action=consumption_popup', 'Consumtion Entry Form','txtpoid_<?=$i; ?>',<?=$i; ?>);" readonly /> </td>
                    <td width="55" align="right"><input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtexchrate_<?=$i; ?>" value="<?=$exchange_rate; ?>" readonly /> </td>
                    <td width="80" align="right">
						<?
                        $ratetexcolor="#000000";
                        $decimal=explode(".",$rate_ord_uom);
                        if(strlen($decimal[1]>6)) $ratetexcolor="#F00";
                        ?>
                        <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; color:<?=$ratetexcolor; ?>; background-color:<?=$bgcolor; ?>" id="txtrate_<?=$i; ?>" value="<?=$rate_ord_uom; ?>" onChange="calculate_amount(<?=$i; ?>);" readonly />
                        <input type="hidden" id="txtrate_precost_<?=$i; ?>" value="<?=$rate_ord_uom; ?>" />
                    </td>
                    <td width="80" align="right"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtamount_<?=$i; ?>" value="<?=number_format($amount,4,'.',''); ?>" readonly /></td>
                    <td>
                        <input type="text" style="width:90%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtddate_<?=$i; ?>" class="datepicker" value="<?=$txt_delivery_date; ?>" readonly />
                        <input type="hidden" id="consbreckdown_<?=$i; ?>" value=""/>
                        <input type="hidden" id="jsondata_<?=$i; ?>" value=""/>
                    </td>
				</tr>
				<?
				$i++;
			}
        }
		
        if($cbo_level==2){
			$i=1;
			foreach ($job_and_trimgroup_level as $job_no){
				foreach ($job_no as $wo_pre_cost_trim_cost_dtls){
					
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					$job_no=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['job_no']));
					$po_number=implode(",",$wo_pre_cost_trim_cost_dtls['po_number']);
					$po_id=implode(",",$wo_pre_cost_trim_cost_dtls['po_id']);
					$country=implode(",",array_unique(explode(",",implode(",",$wo_pre_cost_trim_cost_dtls['country']))));
					$description=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['description']));
					$brand_sup_ref=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['brand_sup_ref']));
					$wo_pre_cost_trim_id=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['wo_pre_cost_trim_cost_dtls']));
					$trim_group = implode(",",array_unique($wo_pre_cost_trim_cost_dtls['trim_group']));
					$uom=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['uom']));
					
				
					$req_qnty_ord_uom=array_sum($wo_pre_cost_trim_cost_dtls['req_qnty']);
					$rate_ord_uom=array_sum($wo_pre_cost_trim_cost_dtls['req_amount'])/array_sum($wo_pre_cost_trim_cost_dtls['req_qnty']);
					$req_amount_ord_uom=array_sum($wo_pre_cost_trim_cost_dtls['req_amount']);
				
					$bal_woq=array_sum($wo_pre_cost_trim_cost_dtls['bal_woq']);
					$amount=array_sum($wo_pre_cost_trim_cost_dtls['amount']);
				
					$cu_woq=array_sum($wo_pre_cost_trim_cost_dtls['cu_woq']);
					$cu_amount=array_sum($wo_pre_cost_trim_cost_dtls['cu_amount']);
					
					$reqsn_qty=$reqsn_amount=$sensitivity=0;
					if($basis_id==1)
					{
						$sensitivity=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['sensitivity']));
						$txt_delivery_date=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['txt_delivery_date']));
						$reqsn_qty=array_sum($wo_pre_cost_trim_cost_dtls['reqsn_qty']);
						$reqsn_amount=array_sum($wo_pre_cost_trim_cost_dtls['reqsn_amount']);
						
						$amount=$reqsn_amount;
					}
				
					?>
					<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i; ?>">
                        <td width="30" align="center"><?=$i; ?></td>
                        <td width="80"><?=$job_no; ?>
                        	<input type="hidden" id="txtjob_<?=$i; ?>" value="<?=$job_no; ?>" class="text_boxes" />
                        </td>
                        <td width="100" style="word-wrap:break-word;word-break: break-all"><?=$po_number; ?>
                            <input type="hidden" id="txtbookingid_<?=$i; ?>" value="" readonly/>
                            <input type="hidden" id="txtpoid_<?=$i; ?>" value="<?=$po_id; ?>" readonly/>
                            <input type="hidden" id="txtcountry_<?=$i; ?>" value="<?=$country; ?>" readonly />
                            <input type="hidden" id="txtdesc_<?=$i; ?>" value="<?=$description; ?>" readonly />
                            <input type="hidden" id="txtbrandsup_<?=$i;?>" value="<?=$brand_sup_ref; ?>" readonly />
                        </td>
                        <td width="100" title="<?=$sql_lib_item_group_array[$trim_group][conversion_factor]; ?>"><?=$trim_group_library[$trim_group]; ?>
                            <input type="hidden" id="txttrimcostid_<?=$i; ?>" value="<?=$wo_pre_cost_trim_id; ?>" readonly/>
                            <input type="hidden" id="txttrimgroup_<?=$i; ?>" value="<?=$trim_group; ?>" readonly/>
                        </td>
                        <td width="150"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; background-color:<?=$bgcolor; ?>" id="txtdescid_<?=$i; ?>" value="<?=$description; ?>" />
                        </td>
                        <td width="70" align="right">
                            <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtreqqnty_<?=$i; ?>" value="<?=number_format($req_qnty_ord_uom,4,'.',''); ?>" readonly />
                            <input type="hidden" id="txtreqamount_<?=$i; ?>" value="<?=number_format($req_amount_ord_uom,4,'.',''); ?>" />
                        </td>
                        <td width="50"><?=$unit_of_measurement[$uom]; ?><input type="hidden" id="txtuom_<?=$i; ?>" value="<?=$uom; ?>" /></td>
                        <td width="80" align="right">
                            <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtcuwoq_<?=$i; ?>" value="<?=number_format($cu_woq,4,'.',''); ?>" readonly />
                            <input type="hidden" id="txtcuamount_<?=$i; ?>" value="<?=number_format($cu_amount,4,'.',''); ?>" />
                        </td>
                        <td width="80" align="right"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtbalwoq_<?=$i; ?>" value="<?=number_format($bal_woq,4,'.',''); ?>" readonly /></td>
                        <td width="100" align="right"><?=create_drop_down( "cbocolorsizesensitive_".$i, 100, $size_color_sensitive,"", 1, "--Select--", $sensitivity, "set_cons_break_down($i),copy_value(this.value,'cbocolorsizesensitive_',$i)","","1,2,3,4" ); ?></td>
                        <td width="80" align="right"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:#FFC" id="txtwoq_<?=$i; ?>" value="<?=number_format($reqsn_qty,4,'.',''); ?>" onClick="open_consumption_popup( 'requires/short_trims_booking_multi_job_controllerurmi.php?action=consumption_popup', 'Consumtion Entry Form','txtpoid_<?=$i; ?>',<?=$i; ?>);" readonly/></td>
                        <td width="55" align="right"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtexchrate_<?=$i; ?>" value="<?=$exchange_rate; ?>" readonly /></td>
                        <td width="80" align="right">
							<?
                            $ratetexcolor="#000000";
                            $decimal=explode(".",$rate_ord_uom);
                            if(strlen($decimal[1])>6) $ratetexcolor="#F00";
                            //echo strlen($decimal[1]);
                            ?>
                            <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; color:<?=$ratetexcolor;  ?>;  background-color:<?=$bgcolor; ?>" id="txtrate_<?=$i; ?>" value="<?=$rate_ord_uom; ?>" onChange="calculate_amount(<?=$i; ?>);" readonly />
                            <input type="hidden" id="txtrate_precost_<?=$i; ?>" value="<?=$rate_ord_uom; ?>" />
                        </td>
                        <td width="80" align="right"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtamount_<?=$i; ?>" value="<?=number_format($amount,4,'.',''); ?>" readonly />
                        </td>
                        <td>
                            <input type="text" style="width:90%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtddate_<?=$i; ?>" class="datepicker" value="<?=$txt_delivery_date; ?>" readonly />
                            <input type="hidden" id="consbreckdown_<?=$i; ?>" value=""/>
                            <input type="hidden" id="jsondata_<?=$i; ?>" value=""/>
                        </td>
					</tr>
					<?
					$i++;
				}
			}
        }
        ?>
        </tbody>
	</table>
	<table width="1250" class="rpt_table" border="0" rules="all">
        <tfoot>
            <tr>
                <th width="30">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="150">&nbsp;</th>
                <th width="70"><?=$tot_req_qty; ?></th>
                <th width="50">&nbsp;</th>
                <th width="80"><?=$tot_cu_woq; ?></th>
                <th width="80"><?=$tot_bal_woq; ?></th>
                <th width="100">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="55">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80"><input type="hidden" id="tot_amount" value="<?=$total_amount; ?>" style="width:80px" readonly /></th>
                <th><input type="hidden" id="saved_tot_amount" value="0" style="width:80px; text-align:right" readonly/></th>
            </tr>
        </tfoot>
	</table>
    <table width="1250" colspan="15" cellspacing="0" class="" border="0">
        <tr>
            <td align="center"class="button_container">
           		<?=load_submit_buttons( $permission, "fnc_trims_booking_dtls", 0,0,"reset_form('','booking_list_view','','','')",2); ?>
            </td>
        </tr>
    </table>
	<?
	exit();
}

if ($action=="show_trim_booking"){
	extract($_REQUEST);
	$trim_group_library= return_library_array("select id, item_name from lib_item_group",'id','item_name');
	
	if($garments_nature==0) $garment_nature_cond=""; else $garment_nature_cond=" and a.garments_nature=$garments_nature";
	
	$sql_lib_item_group_array=array();
	$sql_lib_item_group=sql_select("select id, item_name, conversion_factor, order_uom as cons_uom from lib_item_group");
	foreach($sql_lib_item_group as $row_sql_lib_item_group){
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][item_name]=$row_sql_lib_item_group[csf('item_name')];
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][conversion_factor]=$row_sql_lib_item_group[csf('conversion_factor')];
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][cons_uom]=$row_sql_lib_item_group[csf('cons_uom')];
	}

	$condition= new condition();
	if(str_replace("'","",$data) !=''){
		$condition->po_id("in($data)");
	}
	$condition->init();
	$trims= new trims($condition);
	$req_qty_arr=$trims->getQtyArray_by_orderAndPrecostdtlsid();
	$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsid();

	$cu_booking_arr=array();
	$sql_cu_booking=sql_select("select C.JOB_NO, C.PRE_COST_FABRIC_COST_DTLS_ID as BOMID, C.PO_BREAK_DOWN_ID, SUM(C.WO_QNTY) AS CU_WO_QNTY, SUM(C.AMOUNT) AS CU_AMOUNT from wo_po_details_master a, wo_po_break_down  d, wo_booking_dtls c where a.job_no=d.job_no_mst and a.job_no=c.job_no and d.id=c.po_break_down_id and a.company_name=$cbo_company_name and c.pre_cost_fabric_cost_dtls_id=$pre_cost_id and c.entry_form_id!=717 and c.status_active=1 and c.is_deleted=0 group by c.job_no, c.pre_cost_fabric_cost_dtls_id, c.po_break_down_id");
	foreach($sql_cu_booking as $row_cu_booking){
		$cu_booking_arr[$row_cu_booking['JOB_NO']][$row_cu_booking['BOMID']]['cu_woq'][$row_cu_booking['PO_BREAK_DOWN_ID']]=$row_cu_booking['CU_WO_QNTY'];
		$cu_booking_arr[$row_cu_booking['JOB_NO']][$row_cu_booking['BOMID']]['cu_amount'][$row_cu_booking['PO_BREAK_DOWN_ID']]=$row_cu_booking['CU_AMOUNT'];
	}

	$sql="select A.JOB_NO_PREFIX_NUM, A.JOB_NO, A.COMPANY_NAME, A.BUYER_NAME, A.CURRENCY_ID, A.STYLE_REF_NO, B.COSTING_PER, B.EXCHANGE_RATE, C.ID AS TRIMSBOMID, C.TRIM_GROUP, C.DESCRIPTION AS DESCRIPTION_PRE_COST, C.BRAND_SUP_REF, C.COUNTRY, C.RATE, D.ID AS PO_ID, D.PO_NUMBER, D.PO_QUANTITY AS PLAN_CUT, MIN(E.ID) AS ID, AVG(E.CONS) AS CONS, SUM(F.WO_QNTY) AS CU_WOQ, SUM(F.AMOUNT) AS CU_AMOUNT, F.ID AS BOOKING_ID, F.SENSITIVITY, F.DELIVERY_DATE, G.DESCRIPTION

	from wo_po_details_master a, wo_pre_cost_mst b, wo_pre_cost_trim_cost_dtls c, wo_po_break_down d, wo_pre_cost_trim_co_cons_dtls e, wo_booking_dtls f left join wo_trim_book_con_dtls g on g.wo_trim_booking_dtls_id=f.id
	where a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id and c.id=e.wo_pre_cost_trim_cost_dtls_id and d.id=e.po_break_down_id and e.wo_pre_cost_trim_cost_dtls_id= f.pre_cost_fabric_cost_dtls_id and e.po_break_down_id=f.po_break_down_id and f.booking_type=2 and f.booking_no=$txt_booking_no and f.id in($booking_id) and a.company_name=$cbo_company_name $garment_nature_cond and e.wo_pre_cost_trim_cost_dtls_id=$pre_cost_id and d.is_deleted=0 and d.status_active=1 and f.status_active=1 and f.is_deleted=0

	group by a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id, c.trim_group, c.description, c.brand_sup_ref, c.country, c.rate, d.id, d.po_number, d.po_quantity, f.id, f.sensitivity, f.delivery_date, g.description order by d.id, c.id";

	$i=1; $job_and_trimgroup_level=array();
	$nameArray=sql_select( $sql );
	foreach ($nameArray as $row){
		$cbo_currency_job=$row['CURRENCY_ID'];
		$exchange_rate=$row['EXCHANGE_RATE'];
		if($cbo_currency==$cbo_currency_job){
			$exchange_rate=1;
		}
	
		$req_qnty_cons_uom=$req_qty_arr[$row['PO_ID']][$row['TRIMSBOMID']];
		$req_amount_cons_uom=$req_amount_arr[$row['PO_ID']][$row['TRIMSBOMID']];
		$rate_cons_uom=$req_amount_cons_uom/$req_qnty_cons_uom;
	
		$req_qnty_ord_uom=def_number_format($req_qnty_cons_uom/$sql_lib_item_group_array[$row['TRIM_GROUP']][conversion_factor],5,"");
		$rate_ord_uom=def_number_format(($rate_cons_uom*$sql_lib_item_group_array[$row['TRIM_GROUP']][conversion_factor])*$exchange_rate,5,"");
		$req_amount_ord_uom=def_number_format($req_qnty_ord_uom*$rate_ord_uom,5,"");
	
		$cu_woq=$cu_booking_arr[$row['JOB_NO']][$row['TRIMSBOMID']]['cu_woq'][$row['PO_ID']];
		$cu_amount=$cu_booking_arr[$row['JOB_NO']][$row['TRIMSBOMID']]['cu_amount'][$row['PO_ID']];
	
		$bal_woq=def_number_format($req_qnty_ord_uom-$cu_woq,5,"");
		$amount=def_number_format($rate_ord_uom*$bal_woq,5,"");
	
		$total_req_amount+=$req_amount;
		$total_cu_amount+=$row['CU_AMOUNT'];
	
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMSBOMID']]['job_no'][$row['PO_ID']]=$row['JOB_NO'];
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMSBOMID']]['po_id'][$row['PO_ID']]=$row['PO_ID'];
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMSBOMID']]['po_number'][$row['PO_ID']]=$row['PO_NUMBER'];
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMSBOMID']]['country'][$row['PO_ID']]=$row['COUNTRY'];
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMSBOMID']]['description'][$row['PO_ID']]=$row['DESCRIPTION'];
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMSBOMID']]['brand_sup_ref'][$row['PO_ID']]=$row['BRAND_SUP_REF'];
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMSBOMID']]['trim_group'][$row['PO_ID']]=$row['TRIM_GROUP'];
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMSBOMID']]['trim_group_name'][$row['PO_ID']]=$trim_group_library[$row['TRIM_GROUP']];
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMSBOMID']]['wo_pre_cost_trim_cost_dtls'][$row['PO_ID']]=$row['TRIMSBOMID'];
	
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMSBOMID']]['req_qnty'][$row['PO_ID']]=$req_qnty_ord_uom;
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMSBOMID']]['uom'][$row['PO_ID']]=$sql_lib_item_group_array[$row['TRIM_GROUP']][cons_uom];
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMSBOMID']]['uom_name'][$row['PO_ID']]=$unit_of_measurement[$sql_lib_item_group_array[$row['TRIM_GROUP']][cons_uom]];
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMSBOMID']]['req_amount'][$row['PO_ID']]=$req_amount_ord_uom;
	
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMSBOMID']]['cu_woq'][$row['PO_ID']]=$cu_woq;
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMSBOMID']]['cu_amount'][$row['PO_ID']]=$cu_amount;
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMSBOMID']]['bal_woq'][$row['PO_ID']]=$bal_woq;
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMSBOMID']]['exchange_rate'][$row['PO_ID']]=$exchange_rate;
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMSBOMID']]['rate'][$row['PO_ID']]=$rate_ord_uom;
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMSBOMID']]['amount'][$row['PO_ID']]=$amount;
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMSBOMID']]['txt_delivery_date'][$row['PO_ID']]=$row['DELIVERY_DATE'];
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMSBOMID']]['booking_id'][$row['PO_ID']]=$row['BOOKING_ID'];
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMSBOMID']]['sensitivity'][$row['PO_ID']]=$row['SENSITIVITY'];
	}

	$sql_booking=sql_select("select C.JOB_NO, C.PRE_COST_FABRIC_COST_DTLS_ID as BOMTRIMSID, C.PO_BREAK_DOWN_ID, SUM(C.WO_QNTY) AS WO_QNTY, SUM(C.AMOUNT) AS AMOUNT from wo_po_details_master a, wo_po_break_down  d, wo_booking_dtls c where a.job_no=d.job_no_mst and a.job_no=c.job_no and  d.id=c.po_break_down_id and c.booking_no=$txt_booking_no and c.pre_cost_fabric_cost_dtls_id=$pre_cost_id and c.id in($booking_id) and c.booking_type=2 and c.status_active=1 and c.is_deleted=0 group by c.job_no, c.pre_cost_fabric_cost_dtls_id, c.po_break_down_id");
	foreach($sql_booking as $row_booking){
		$job_and_trimgroup_level[$row_booking['JOB_NO']][$row_booking['BOMTRIMSID']]['woq'][$row_booking['PO_BREAK_DOWN_ID']]=$row_booking['WO_QNTY'];
		$job_and_trimgroup_level[$row_booking['JOB_NO']][$row_booking['BOMTRIMSID']]['amount'][$row_booking['PO_BREAK_DOWN_ID']]=$row_booking['AMOUNT'];
	}
	?>

    <input type="hidden" id="strdata" value='<?=json_encode($job_and_trimgroup_level); ?>' style="background-color:#CCC"/>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1250" class="rpt_table" >
        <thead>
            <th width="30">SL</th>
            <th width="80">Job No</th>
            <th width="100">Ord. No</th>
            <th width="100">Trims Group</th>
            <th width="150">Description</th>
            <th width="70">Req. Qnty</th>
            <th width="50">UOM</th>
            <th width="80">CU WOQ</th>
            <th width="80">Bal WOQ</th>
            <th width="100">Sensitivity</th>
            <th width="80">WOQ</th>
            <th width="55">Exch.Rate</th>
            <th width="80">Rate</th>
            <th width="80">Amount</th>
            <th>Delv. Date</th>
        </thead>
	</table>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1250" class="rpt_table" id="tbl_list_search" >
        <tbody>
        <?
        if($cbo_level==1){
			foreach ($nameArray as $selectResult){
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			
				$cbo_currency_job=$selectResult['CURRENCY_ID'];
				$exchange_rate=$selectResult['EXCHANGE_RATE'];
				if($cbo_currency==$cbo_currency_job){
					$exchange_rate=1;
				}
			
				$req_qnty_cons_uom=$req_qty_arr[$selectResult['PO_ID']][$selectResult['TRIMSBOMID']];
				$req_amount_cons_uom=$req_amount_arr[$selectResult['PO_ID']][$selectResult['TRIMSBOMID']];
				$rate_cons_uom=$req_amount_cons_uom/$req_qnty_cons_uom;
			
				$req_qnty_ord_uom=def_number_format($req_qnty_cons_uom/$sql_lib_item_group_array[$selectResult['TRIM_GROUP']][conversion_factor],5,"");
				$rate_ord_uom=def_number_format(($rate_cons_uom*$sql_lib_item_group_array[$selectResult['TRIM_GROUP']][conversion_factor])*$exchange_rate,5,"");
				$req_amount_ord_uom=def_number_format($req_qnty_ord_uom*$rate_ord_uom,5,"");
			
				$cu_woq=$cu_booking_arr[$selectResult['JOB_NO']][$selectResult['TRIMSBOMID']]['cu_woq'][$selectResult['PO_ID']];
				$cu_amount=$cu_booking_arr[$selectResult['JOB_NO']][$selectResult['TRIMSBOMID']]['cu_amount'][$selectResult['PO_ID']];
			
				$bal_woq=def_number_format($req_qnty_ord_uom-$cu_woq,5,"");
			
			
				$woq=$job_and_trimgroup_level[$selectResult['JOB_NO']][$selectResult['TRIMSBOMID']]['woq'][$selectResult['PO_ID']];
				$amount=$job_and_trimgroup_level[$selectResult['JOB_NO']][$selectResult['TRIMSBOMID']]['amount'][$selectResult['PO_ID']];
				$rate=$amount/$woq;
				$total_amount+=$amount;
				?>
				<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i; ?>">
                    <td width="30" align="center"><?=$i;?></td>
                    <td width="80"><?=$selectResult['JOB_NO']; ?>
                    	<input type="hidden" id="txtjob_<?=$i; ?>" value="<?=$selectResult['JOB_NO']; ?>" style="width:30px" />
                    </td>
                    <td width="100"><?=$selectResult['PO_NUMBER']; ?>
                        <input type="hidden" id="txtbookingid_<?=$i; ?>" value="<?=$selectResult['BOOKING_ID']; ?>" readonly/>
                        <input type="hidden" id="txtpoid_<?=$i; ?>" value="<?=$selectResult['PO_ID']; ?>" readonly/>
                        <input type="hidden" id="txtcountry_<?=$i; ?>" value="<?=$selectResult['COUNTRY']; ?>" readonly />
                        <input type="hidden" id="txtdesc_<?=$i; ?>" value="<?=$selectResult['DESCRIPTION_PRE_COST'];?>" readonly />
                        <input type="hidden" id="txtbrandsup_<?=$i; ?>" value="<?=$selectResult['BRAND_SUP_REF']; ?>" readonly />
                    </td>
                    <td width="100" title="<?=$sql_lib_item_group_array[$selectResult['TRIM_GROUP']][conversion_factor]; ?>"><?=$trim_group_library[$selectResult['TRIM_GROUP']]; ?>
                        <input type="hidden" id="txttrimcostid_<?=$i; ?>" value="<?=$selectResult['TRIMSBOMID']; ?>" readonly/>
                        <input type="hidden" id="txttrimgroup_<?=$i; ?>" value="<?=$selectResult['TRIM_GROUP']; ?>" readonly/>
                    </td>
                    <td width="150"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; background-color:<?=$bgcolor; ?>" id="txtdescid_<?=$i; ?>" value="<?=$selectResult['DESCRIPTION']; ?>" <? if($disAbled){echo "disabled";}else{ echo "";}?> /></td>
                
                    <td width="70" align="right">
                        <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtreqqnty_<?=$i; ?>" value="<?=number_format($req_qnty_ord_uom,4,'.',''); ?>" readonly />
                        <input type="hidden" id="txtreqamount_<?=$i; ?>" value="<?=number_format($req_amount_ord_uom,4,'.',''); ?>" />
                    </td>
                    <td width="50"><?=$unit_of_measurement[$sql_lib_item_group_array[$selectResult['TRIM_GROUP']][cons_uom]]; ?>
                    	<input type="hidden" id="txtuom_<?=$i; ?>" value="<?=$sql_lib_item_group_array[$selectResult['TRIM_GROUP']][cons_uom]; ?>" readonly />
                    </td>
                    <td width="80" align="right">
                        <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtcuwoq_<?=$i; ?>" value="<?=number_format($cu_woq,4,'.',''); ?>" readonly />
                        <input type="hidden" id="txtcuamount_<?=$i; ?>" value="<?=number_format($cu_amount,4,'.',''); ?>" />
                    </td>
                    <td width="80" align="right"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtbalwoq_<?=$i; ?>" value="<?=number_format($bal_woq,4,'.',''); ?>" readonly /></td>
                    <td width="100"><?=create_drop_down( "cbocolorsizesensitive_".$i, 100, $size_color_sensitive,"", 1, "--Select--", $selectResult["SENSITIVITY"], "set_cons_break_down($i), copy_value(this.value,'cbocolorsizesensitive_',$i);","","1,2,3,4" ); ?></td>
                    <td width="80" align="right"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:#FFC" id="txtwoq_<?=$i; ?>" value="<?=number_format($woq,4,'.',''); ?>" onClick="open_consumption_popup( 'requires/short_trims_booking_multi_job_controllerurmi.php?action=consumption_popup', 'Consumtion Entry Form','txtpoid_<?=$i; ?>',<?=$i; ?>);" readonly />
                    </td>
                    <td width="55" align="right"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtexchrate_<?=$i; ?>" value="<?=$exchange_rate; ?>" readonly /></td>
                    <td width="80" align="right">
                        <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<?=$bgcolor; ?>" id="txtrate_<?=$i; ?>" value="<?=number_format($rate,4,'.',''); ?>" onChange="calculate_amount(<?=$i; ?>);" readonly />
                        <input type="hidden" id="txtrate_precost_<?=$i; ?>" value="<?=$rate_ord_uom; ?>" />
                    </td>
                    <td width="80" align="right"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<?=$bgcolor; ?>" id="txtamount_<?=$i; ?>" value="<?=number_format($amount,4,'.',''); ?>" readonly />
                    </td>
                    <td>
                        <input type="text" style="width:90%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtddate_<?=$i; ?>" class="datepicker" value="<?=change_date_format($selectResult['delivery_date'],"dd-mm-yyyy","-"); ?>" readonly />
                        <input type="hidden" id="consbreckdown_<?=$i; ?>" value=""/>
                        <input type="hidden" id="jsondata_<?=$i; ?>" value=""/>
                    </td>
				</tr>
				<?
				$i++;
			}
        }
    
        if($cbo_level==2){
			$i=1;
			foreach ($job_and_trimgroup_level as $job_no){
				foreach ($job_no as $wo_pre_cost_trim_cost_dtls){
					$job_no=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['job_no']));
					$po_number=implode(",",$wo_pre_cost_trim_cost_dtls['po_number']);
					$po_id=implode(",",$wo_pre_cost_trim_cost_dtls['po_id']);
					$country=implode(",",array_unique(explode(",",implode(",",$wo_pre_cost_trim_cost_dtls['country']))));
					$description=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['description']));
					$brand_sup_ref=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['brand_sup_ref']));
					$wo_pre_cost_trim_id=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['wo_pre_cost_trim_cost_dtls']));
					$trim_group = implode(",",array_unique($wo_pre_cost_trim_cost_dtls['trim_group']));
					$uom=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['uom']));
					$booking_id=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['booking_id']));
					$sensitivity=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['sensitivity']));
					$delivery_date=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['txt_delivery_date']));
				
					$req_qnty_ord_uom=array_sum($wo_pre_cost_trim_cost_dtls['req_qnty']);
					$rate_ord_uom=array_sum($wo_pre_cost_trim_cost_dtls['req_amount'])/array_sum($wo_pre_cost_trim_cost_dtls['req_qnty']);
					$req_amount_ord_uom=array_sum($wo_pre_cost_trim_cost_dtls['req_amount']);
				
					$bal_woq=array_sum($wo_pre_cost_trim_cost_dtls['bal_woq']);
					$cu_woq=array_sum($wo_pre_cost_trim_cost_dtls['cu_woq']);
					$cu_amount=array_sum($wo_pre_cost_trim_cost_dtls['cu_amount']);
				
					$woq=array_sum($wo_pre_cost_trim_cost_dtls['woq']);
					$amount=array_sum($wo_pre_cost_trim_cost_dtls['amount']);
					$rate=$amount/$woq;
					$total_amount+=$amount;
					?>
					<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i; ?>">
                        <td width="30" align="center"><?=$i; ?></td>
                        <td width="80"><?=$job_no; ?>
                        	<input type="hidden" id="txtjob_<?=$i; ?>" value="<?=$job_no; ?>" style="width:30px"/>
                        </td>
                        <td width="100" style="word-wrap:break-word;word-break: break-all"><?=$po_number; ?>
                            <input type="hidden" id="txtbookingid_<?=$i; ?>" value="<?=$booking_id; ?>" readonly/>
                            <input type="hidden" id="txtpoid_<?=$i; ?>" value="<?=$po_id; ?>" readonly/>
                            <input type="hidden" id="txtcountry_<?=$i; ?>" value="<?=$country; ?>" readonly />
                            <input type="hidden" id="txtdesc_<?=$i; ?>" value="<?=$description; ?>" readonly />
                            <input type="hidden" id="txtbrandsup_<?=$i; ?>" value="<?=$brand_sup_ref;?>" readonly />
                        </td>
                        <td width="100" title="<?=$sql_lib_item_group_array[$trim_group][conversion_factor]; ?>"><?=$trim_group_library[$trim_group]; ?>
                            <input type="hidden" id="txttrimcostid_<?=$i; ?>" value="<?=$wo_pre_cost_trim_id; ?>" readonly/>
                            <input type="hidden" id="txttrimgroup_<?=$i; ?>" value="<?=$trim_group; ?>" readonly/>
                        </td>
                        <td width="150"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; background-color:<?=$bgcolor; ?>" id="txtdescid_<?=$i; ?>" value="<?=$description; ?>" <? if($disAbled){echo "disabled";}else{ echo "";}?> /></td>
                        <td width="70" align="right">
                            <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtreqqnty_<?=$i; ?>" value="<?=number_format($req_qnty_ord_uom,4,'.',''); ?>" readonly />
                            <input type="hidden" id="txtreqamount_<?=$i; ?>" value="<?=number_format($req_amount_ord_uom,4,'.',''); ?>" />
                        </td>
                        <td width="50"><?=$unit_of_measurement[$uom]; ?><input type="hidden" id="txtuom_<?=$i; ?>" value="<?=$uom; ?>" readonly /></td>
                        <td width="80" align="right">
                            <input type="text" style="width:100%; height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtcuwoq_<?=$i; ?>" value="<?=number_format($cu_woq,4,'.',''); ?>" readonly />
                            <input type="hidden" id="txtcuamount_<?=$i; ?>" value="<?=$cu_amount;?>" />
                        </td>
                        <td width="80" align="right"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtbalwoq_<?=$i; ?>" value="<?=number_format($bal_woq,4,'.',''); ?>" readonly /></td>
                        <td width="100"><?=create_drop_down( "cbocolorsizesensitive_".$i, 100, $size_color_sensitive,"", 1, "--Select--", $sensitivity, "set_cons_break_down($i);, copy_value(this.value,'cbocolorsizesensitive_',$i);","","1,2,3,4" ); ?></td>
                        <td width="80" align="right"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:#FFC" id="txtwoq_<?=$i;?>" value="<?=number_format($woq,4,'.',''); ?>" onClick="open_consumption_popup( 'requires/short_trims_booking_multi_job_controllerurmi.php?action=consumption_popup', 'Consumtion Entry Form','txtpoid_<?=$i; ?>',<?=$i; ?>);" readonly /></td>
                        <td width="55" align="right"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtexchrate_<?=$i; ?>" value="<?=$exchange_rate; ?>" readonly /></td>
                        <td width="80" align="right">
                            <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtrate_<?=$i; ?>" value="<?=number_format($rate,4,'.',''); ?>" onChange="calculate_amount(<?=$i; ?>);" readonly/>
                            <input type="hidden" id="txtrate_precost_<?=$i; ?>" value="<?=$rate_ord_uom; ?>" />
                        </td>
                        <td width="80" align="right"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtamount_<?=$i; ?>" value="<?=number_format($amount,4,'.',''); ?>" readonly /></td>
                        <td>
                            <input type="text" style="width:90%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtddate_<?=$i; ?>" class="datepicker" value="<?=change_date_format($delivery_date,"dd-mm-yyyy","-"); ?>" readonly />
                            <input type="hidden" id="consbreckdown_<?=$i; ?>" value=""/>
                            <input type="hidden" id="jsondata_<?=$i; ?>" value=""/>
                        </td>
					</tr>
					<?
					$i++;
				}
			}
        }
        ?>
        </tbody>
	</table>
	<table width="1250" class="rpt_table" border="0" rules="all">
        <tfoot>
            <tr>
                <th width="30">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="150">&nbsp;</th>
                <th width="70"><?=$tot_req_qty; ?></th>
                <th width="50">&nbsp;</th>
                <th width="80"><?=$tot_cu_woq; ?></th>
                <th width="80"><?=$tot_bal_woq; ?></th>
                <th width="100">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="55">&nbsp;</th>
                <th width="80"><input type="hidden" id="tot_amount" value="<?=number_format($total_amount,4,'.',''); ?>" class="text_boxes_numeric" style="width:70px"/><? //echo  $total_amount; ?></th>
                <th width="80"><input type="hidden" id="tot_amount" value="<?=number_format($total_amount,4,'.',''); ?>" class="text_boxes_numeric" style="width:70px"/><? //echo  $total_amount; ?></th>
                <th><input type="hidden" id="saved_tot_amount" value="0" style="width:80px; text-align:right" readonly/></th>
            </tr>
        </tfoot>
	</table>
    <table width="1250" colspan="15" cellspacing="0" class="" border="0">
        <tr>
            <td align="center"class="button_container">
           		<?=load_submit_buttons( $permission, "fnc_trims_booking_dtls", 1,0,"reset_form('','booking_list_view','','','')",2); ?>
            </td>
        </tr>
    </table>
	<?
	exit();
}

if ($action=="show_trim_booking_list"){
	extract($_REQUEST);
	$trim_group_library= return_library_array("select id, item_name from lib_item_group",'id','item_name');
	
	if($garments_nature==0) $garment_nature_cond=""; else $garment_nature_cond=" and a.garments_nature=$garments_nature";

	$sql_lib_item_group_array=array();
	$sql_lib_item_group=sql_select("select id, item_name,conversion_factor,order_uom as cons_uom from lib_item_group");
	foreach($sql_lib_item_group as $row_sql_lib_item_group){
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][item_name]=$row_sql_lib_item_group[csf('item_name')];
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][conversion_factor]=$row_sql_lib_item_group[csf('conversion_factor')];
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][cons_uom]=$row_sql_lib_item_group[csf('cons_uom')];
	}

	$sql="select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id as wo_pre_cost_trim_cost_dtls, c.trim_group, g.description as description_dtls, c.brand_sup_ref, c.country, c.rate, d.id as po_id, d.po_number, d.po_quantity as plan_cut, min(e.id) as id, e.po_break_down_id, avg(e.cons) as cons, sum(f.wo_qnty) as cu_woq, sum(f.amount) as cu_amount, f.id as booking_id, f.sensitivity, f.delivery_date, f.description as description

	from wo_po_details_master a, wo_pre_cost_mst b, wo_pre_cost_trim_cost_dtls c, wo_po_break_down d, wo_pre_cost_trim_co_cons_dtls e, wo_booking_dtls f left join wo_trim_book_con_dtls g on g.wo_trim_booking_dtls_id=f.id
	where a.job_no=b.job_no and a.job_no=c.job_no and a.job_no=d.job_no_mst and a.job_no=e.job_no and a.job_no=f.job_no and c.id=e.wo_pre_cost_trim_cost_dtls_id and d.id=e.po_break_down_id and e.wo_pre_cost_trim_cost_dtls_id= f.pre_cost_fabric_cost_dtls_id and e.po_break_down_id=f.po_break_down_id and f.booking_type=2 and f.booking_no=$txt_booking_no and a.company_name=$cbo_company_name   $garment_nature_cond and d.is_deleted=0 and d.status_active=1 and f.status_active=1 and f.is_deleted=0

	group by a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id, c.trim_group, g.description, c.brand_sup_ref, c.country, c.rate, d.id, d.po_number, d.po_quantity, e.po_break_down_id, f.id, f.sensitivity, f.delivery_date, f.description order by d.id, c.id";

	$i=1; $job_and_trimgroup_level=array();
	$nameArray=sql_select( $sql );
	foreach ($nameArray as $selectResult){
		$cbo_currency_job=$selectResult[csf('currency_id')];
		$exchange_rate=$selectResult[csf('exchange_rate')];
		if($cbo_currency==$cbo_currency_job){
			$exchange_rate=1;
		}
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('description')]]['job_no'][$selectResult[csf('po_id')]]=$selectResult[csf('job_no')];
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('description')]]['po_id'][$selectResult[csf('po_id')]]=$selectResult[csf('po_id')];
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('description')]]['po_number'][$selectResult[csf('po_id')]]=$selectResult[csf('po_number')];
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('description')]]['country'][$selectResult[csf('po_id')]]=$selectResult[csf('country')];
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('description')]]['description'][$selectResult[csf('po_id')]]=$selectResult[csf('description')];
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('description')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['brand_sup_ref'][$selectResult[csf('po_id')]]=$selectResult[csf('brand_sup_ref')];
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('description')]]['trim_group'][$selectResult[csf('po_id')]]=$selectResult[csf('trim_group')];
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('description')]]['trim_group_name'][$selectResult[csf('po_id')]]=$trim_group_library[$selectResult[csf('trim_group')]];
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('description')]]['wo_pre_cost_trim_cost_dtls'][$selectResult[csf('po_id')]]=$selectResult[csf('wo_pre_cost_trim_cost_dtls')];
	
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('description')]]['uom'][$selectResult[csf('po_id')]]=$sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom];
	
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('description')]]['uom_name'][$selectResult[csf('po_id')]]=$unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom]];
	
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('description')]]['cu_woq'][$selectResult[csf('po_id')]]=$cu_woq;
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('description')]]['cu_amount'][$selectResult[csf('po_id')]]=$cu_amount;
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('description')]]['bal_woq'][$selectResult[csf('po_id')]]=$bal_woq;
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('description')]]['exchange_rate'][$selectResult[csf('po_id')]]=$exchange_rate;
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('description')]]['rate'][$selectResult[csf('po_id')]]=$rate_ord_uom;
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('description')]]['amount'][$selectResult[csf('po_id')]]=$amount;
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('description')]]['txt_delivery_date'][$selectResult[csf('po_id')]]=$selectResult[csf('delivery_date')];
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('description')]]['booking_id'][$selectResult[csf('po_id')]]=$selectResult[csf('booking_id')];
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('description')]]['sensitivity'][$selectResult[csf('po_id')]]=$selectResult[csf('sensitivity')];
	}
	
	$sql_booking=sql_select("select c.job_no,c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id,c.description,c.sensitivity, sum(c.wo_qnty) as wo_qnty, sum(c.amount) as amount from wo_po_details_master a, wo_po_break_down  d , wo_booking_dtls c where a.job_no=d.job_no_mst and a.job_no=c.job_no and  d.id=c.po_break_down_id and c.booking_no=$txt_booking_no and c.booking_type=2 and c.status_active=1 and c.is_deleted=0 group by c.job_no, c.description,c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id,c.sensitivity");
	
	foreach($sql_booking as $row_booking){
			$job_and_trimgroup_level[$row_booking[csf('job_no')]][$row_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_booking[csf('sensitivity')]][$row_booking[csf('description')]]['woq'][$row_booking[csf('po_break_down_id')]]=$row_booking[csf('wo_qnty')];
			$job_and_trimgroup_level[$row_booking[csf('job_no')]][$row_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_booking[csf('sensitivity')]][$row_booking[csf('description')]]['amount'][$row_booking[csf('po_break_down_id')]]=$row_booking[csf('amount')];
	}
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1100" class="rpt_table" >
        <thead>
            <th width="40">SL</th>
            <th width="100">Job No</th>
            <th width="100">Ord. No</th>
            <th width="100">Trims Group</th>
            <th width="150">Description</th>
            <th width="80">UOM</th>
            <th width="100">Sensitivity</th>
            <th width="80">WOQ</th>
            <th width="80">Exch.Rate</th>
            <th width="80">Rate</th>
            <th width="80">Amount</th>
            <th width="">Delv. Date</th>
        </thead>
        <tbody>
        <?
		
        if(str_replace("'","",$cbo_level)==1){
			foreach ($nameArray as $selectResult){
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			
				$cbo_currency_job=$selectResult[csf('currency_id')];
				$exchange_rate=$selectResult[csf('exchange_rate')];
				if($cbo_currency==$cbo_currency_job){
					$exchange_rate=1;
				}
				$woq=def_number_format($job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('description')]]['woq'][$selectResult[csf('po_id')]],5,"");
				$amount=def_number_format($job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('description')]]['amount'][$selectResult[csf('po_id')]],5,"");
				$rate=def_number_format($amount/$woq,5,"");
				$total_amount+=$amount;
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="fnc_show_booking(<? echo $selectResult[csf('wo_pre_cost_trim_cost_dtls')];?>,'<? echo $selectResult[csf('po_id')]; ?>','<? echo $selectResult[csf('booking_id')];?>')">
                    <td width="40"><? echo $i;?></td>
                    <td width="100"><? echo $selectResult[csf('job_no')];?></td>
                    <td width="100"><? echo $selectResult[csf('po_number')];?></td>
                    <td width="100" title="<? echo $sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor]; ?>"><? echo $trim_group_library[$selectResult[csf('trim_group')]];?></td>
                    <td width="100" title="<? echo $selectResult[csf('description')]; ?>"><? echo $selectResult[csf('description')];?></td>
                    <td width="80"><? echo $unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom]];?> </td>
                    <td width="100" align="right"><? echo $size_color_sensitive[$selectResult[csf("sensitivity")]]; ?></td>
                    <td width="80" align="right"><? echo number_format($woq,4,'.','');?></td>
                    <td width="80" align="right"><? echo $exchange_rate;?></td>
                    <td width="80" align="right"><? echo number_format($amount/$woq,4,'.','');?></td>
                    <td width="80" align="right"><? echo number_format($amount,4,'.','');?></td>
                    <td align="right"><? echo change_date_format($selectResult[csf('delivery_date')],"dd-mm-yyyy","-"); ?></td>
				</tr>
				<?
				$i++;
			}
        }
    
        if(str_replace("'","",$cbo_level)==2){
			$i=1;
			foreach ($job_and_trimgroup_level as $job_no){
				foreach ($job_no as $sen){
					foreach ($sen as $desc){
						foreach ($desc as $wo_pre_cost_trim_cost_dtls){
							$job_no=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['job_no']));
							$po_number=implode(",",$wo_pre_cost_trim_cost_dtls['po_number']);
							$po_id=implode(",",$wo_pre_cost_trim_cost_dtls['po_id']);
							$country=implode(",",array_unique(explode(",",implode(",",$wo_pre_cost_trim_cost_dtls['country']))));
							$description=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['description']));
							$brand_sup_ref=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['brand_sup_ref']));
							$wo_pre_cost_trim_id=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['wo_pre_cost_trim_cost_dtls']));
							$trim_group = implode(",",array_unique($wo_pre_cost_trim_cost_dtls['trim_group']));
							$uom=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['uom']));
							$booking_id=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['booking_id']));
							$sensitivity=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['sensitivity']));
							$delivery_date=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['txt_delivery_date']));
							$woq=def_number_format(array_sum($wo_pre_cost_trim_cost_dtls['woq']),5,"");
							$amount=def_number_format(array_sum($wo_pre_cost_trim_cost_dtls['amount']),5,"");
							$rate=def_number_format($amount/$woq,5,"");
							$total_amount+=$amount;
							//echo $sensitivity."<br/>";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="fnc_show_booking(<? echo $wo_pre_cost_trim_id;?>,'<? echo $po_id; ?>','<? echo $booking_id; ?>')">
                                <td width="40"><? echo $i;?></td>
                                <td width="100"> <? echo $job_no?></td>
                                <td width="100" style="word-wrap:break-word;word-break: break-all"><? echo $po_number; ?></td>
                                <td width="100"  title="<? echo $sql_lib_item_group_array[$trim_group][conversion_factor]; ?>"><? echo $trim_group_library[$trim_group];?></td>
                                <td width="100"  title="<? echo $description; ?>"><? echo $description;?></td>
                                
                                <td width="80"><?  echo $unit_of_measurement[$uom];?></td>
                                <td width="100" align="right"><? echo $size_color_sensitive[$sensitivity];?></td>
                                <td width="80" align="right"><? echo number_format($woq,4,'.','');?></td>
                                <td width="80" align="right"><? echo $exchange_rate;?></td>
                                <td width="80" align="right"><? echo number_format($amount/$woq,4,'.','');?></td>
                                <td width="80" align="right"><? echo number_format($amount,4,'.','');?></td>
                                <td align="right"><? echo change_date_format($delivery_date,"dd-mm-yyyy","-"); ?></td>
							</tr>
							<?
							$i++;
						}
					}
				}
			}
        }
        ?>
        </tbody>
	</table>
	<?
	exit();
}

if ($action == "consumption_popup"){
	echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode,'','');
	$color_library=return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$size_library=return_library_array("select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	?>
	<script>
	var str_gmtssizes = [<? echo substr(return_library_autocomplete( "select size_name from  lib_size where status_active=1 and is_deleted=0", "size_name"  ), 0, -1); ?>];
	var str_diawidth = [<? echo substr(return_library_autocomplete( "select color_name from lib_color where status_active=1 and is_deleted=0", "color_name"  ), 0, -1); ?>];
	function poportionate_qty_old(qty){
		var po_qty=document.getElementById('po_qty').value;
		var txtwoq_qty=document.getElementById('txtwoq_qty').value;
		var rowCount = $('#tbl_consmption_cost tbody tr').length;
		for(var i=1; i<=rowCount; i++){
			var pcs=$('#pcsset_'+i).val();
			var txtwoq_cal =number_format_common((txtwoq_qty/po_qty) * (pcs),5,0);
			$('#qty_'+i).val(txtwoq_cal);
			calculate_requirement(i)
		}
		set_sum_value( 'qty_sum', 'qty_' )
	}

	function poportionate_qty(qty){
		var txtwoq=document.getElementById('txtwoq').value;
		var txtwoq_qty=document.getElementById('txtwoq_qty').value*1;
		var rowCount = $('#tbl_consmption_cost tbody tr').length;
		for(var i=1; i<=rowCount; i++){
			var poreqqty=$('#poreqqty_'+i).val();
			var txtwoq_cal =number_format_common((txtwoq_qty/txtwoq) * (poreqqty),5,0);
			$('#qty_'+i).val(txtwoq_cal);
			calculate_requirement(i)
		}
		set_sum_value( 'qty_sum', 'qty_')
		var j=i-1;
		var qty_sum=document.getElementById('qty_sum').value*1;
		if(qty_sum >txtwoq_qty ){
			$('#qty_'+j).val(number_format_common(txtwoq_cal*1-(qty_sum-txtwoq_qty),5,0))
		}
		else if(qty_sum < txtwoq_qty ){
			$('#qty_'+j).val(number_format_common((txtwoq_cal*1) +(txtwoq_qty - qty_sum),5,0))
		}
		else{
			$('#qty_'+j).val(number_format_common(txtwoq_cal,5,0));
		}
		set_sum_value( 'qty_sum', 'qty_');
		calculate_requirement(j)
	}

	function calculate_requirement(i){
		var process_loss_method_id=document.getElementById('process_loss_method_id').value;
		var cons=(document.getElementById('qty_'+i).value)*1;
		var processloss=(document.getElementById('excess_'+i).value)*1;
		var WastageQty='';
		if(process_loss_method_id==1){
			WastageQty=cons+cons*(processloss/100);
		}
		else if(process_loss_method_id==2){
			var devided_val = 1-(processloss/100);
			var WastageQty=parseFloat(cons/devided_val);
		}
		else{
			WastageQty=0;
		}
		WastageQty= number_format_common( WastageQty, 5, 0) ;
		document.getElementById('woqny_'+i).value= WastageQty;
		set_sum_value( 'woqty_sum', 'woqny_' )
		calculate_amount(i);
	}

	function set_sum_value(des_fil_id,field_id){
		if(des_fil_id=='qty_sum'){
			var ddd={dec_type:5,comma:0,currency:0};
		}

		if(des_fil_id=='excess_sum'){
			var ddd={dec_type:5,comma:0,currency:0};
		}

		if(des_fil_id=='woqty_sum'){
			var ddd={dec_type:5,comma:0,currency:0};
		}

		if(des_fil_id=='amount_sum'){
			var ddd={dec_type:5,comma:0,currency:0};
		}

		if(des_fil_id=='pcs_sum'){
			var ddd={dec_type:6,comma:0};
		}
		var rowCount = $('#tbl_consmption_cost tbody tr').length;
		math_operation( des_fil_id, field_id, '+', rowCount,ddd );
	}

	function copy_value(value,field_id,i){
		var gmtssizesid=document.getElementById('gmtssizesid_'+i).value;
		var pocolorid=document.getElementById('pocolorid_'+i).value;
		var rowCount = $('#tbl_consmption_cost tbody tr').length;
		var copy_basis=$('input[name="copy_basis"]:checked').val()

		for(var j=i; j<=rowCount; j++){
			if(field_id=='des_'){
				if(copy_basis==0){
					document.getElementById(field_id+j).value=value;
				}
				if(copy_basis==1){
					if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value){
						document.getElementById(field_id+j).value=value;
					}
				}
				if(copy_basis==2){
					if( pocolorid==document.getElementById('pocolorid_'+j).value){
						document.getElementById(field_id+j).value=value;
					}
				}
			}
			if(field_id=='brndsup_'){
				if(copy_basis==0){
					document.getElementById(field_id+j).value=value;
				}
				if(copy_basis==1){
					if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value){
						document.getElementById(field_id+j).value=value;
					}
				}
				if(copy_basis==2){
					if( pocolorid==document.getElementById('pocolorid_'+j).value){
						document.getElementById(field_id+j).value=value;
					}
				}
			}
			if(field_id=='itemcolor_'){
				if(copy_basis==0){
					document.getElementById(field_id+j).value=value;
				}
				if(copy_basis==1){
					if( pocolorid==document.getElementById('pocolorid_'+j).value){
						document.getElementById(field_id+j).value=value;
					}
				}
				if(copy_basis==2){
					if( pocolorid==document.getElementById('pocolorid_'+j).value){
						document.getElementById(field_id+j).value=value;
					}
				}
			}

			if(field_id=='itemsizes_'){
				if(copy_basis==0){
					document.getElementById(field_id+j).value=value;
				}
				if(copy_basis==1)
				{
					if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value){
						document.getElementById(field_id+j).value=value;
					}
				}
				if(copy_basis==2)
				{
					if( pocolorid==document.getElementById('pocolorid_'+j).value){
						document.getElementById(field_id+j).value=value;
					}
				}
			}
			if(field_id=='qty_'){
				if(copy_basis==0){
					document.getElementById(field_id+j).value=value;
					calculate_requirement(j)
					set_sum_value( 'qty_sum', 'qty_'  );
				}
				if(copy_basis==1){
					if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value){
						document.getElementById(field_id+j).value=value;
						calculate_requirement(j)
						set_sum_value( 'qty_sum', 'qty_'  );
					}
				}
				if(copy_basis==2){
					if( pocolorid==document.getElementById('pocolorid_'+j).value){
						document.getElementById(field_id+j).value=value;
						calculate_requirement(j)
						set_sum_value( 'qty_sum', 'qty_'  );
					}
				}
			}
			if(field_id=='excess_'){
				if(copy_basis==0){
					document.getElementById(field_id+j).value=value;
					calculate_requirement(j)
				}
				if(copy_basis==1){
					if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value){
						document.getElementById(field_id+j).value=value;
						calculate_requirement(j)
					}
				}
				if(copy_basis==2){
					if( pocolorid==document.getElementById('pocolorid_'+j).value){
						document.getElementById(field_id+j).value=value;
						calculate_requirement(j)
					}
				}
			}
			if(field_id=='rate_'){
				if(copy_basis==0){
					document.getElementById(field_id+j).value=value;
					calculate_amount(j)
				}
				if(copy_basis==1){
					if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value){
						document.getElementById(field_id+j).value=value;
						calculate_amount(j)
					}
				}
				if(copy_basis==2){
					if( pocolorid==document.getElementById('pocolorid_'+j).value){
						document.getElementById(field_id+j).value=value;
						calculate_amount(j)
					}
				}
			}
		}
	}

	function calculate_amount(i){
		var rate=(document.getElementById('rate_'+i).value)*1;
		var woqny=(document.getElementById('woqny_'+i).value)*1;
		var amount=number_format_common((rate*woqny),5,0);
		document.getElementById('amount_'+i).value=amount;
		set_sum_value( 'amount_sum', 'amount_' );
		calculate_avg_rate()
	}

	function calculate_avg_rate(){
		var woqty_sum=document.getElementById('woqty_sum').value;
		var amount_sum=document.getElementById('amount_sum').value;
		var avg_rate=number_format_common((amount_sum/woqty_sum),3,0);
		document.getElementById('rate_sum').value=avg_rate;
	}

	function js_set_value(){
		var row_num=$('#tbl_consmption_cost tbody tr').length;
		var cons_breck_down="";
		for(var i=1; i<=row_num; i++){
			var pocolorid=$('#pocolorid_'+i).val()
			if(pocolorid=='') pocolorid=0;

			var gmtssizesid=$('#gmtssizesid_'+i).val()
			if(gmtssizesid=='') gmtssizesid=0;

			var des=$('#des_'+i).val()
			if(des=='') des=0;

			var brndsup=$('#brndsup_'+i).val();
			if(brndsup=='') brndsup=0;

			var itemcolor=$('#itemcolor_'+i).val()
			if(itemcolor=='') itemcolor=0;

			var itemsizes=$('#itemsizes_'+i).val()
			if(itemsizes=='') itemsizes=0;

			var qty=$('#qty_'+i).val()
			if(qty=='') qty=0;

			var excess=$('#excess_'+i).val()
			if(excess=='') excess=0;

			var woqny=$('#woqny_'+i).val()
			if(woqny=='') woqny=0;

			var rate=$('#rate_'+i).val()
			if(rate=='') rate=0;

			var amount=$('#amount_'+i).val()
			if(amount=='') amount=0;

			var pcs=$('#pcs_'+i).val()
			if(pcs=='') pcs=0;

			var colorsizetableid=$('#colorsizetableid_'+i).val()
			if(colorsizetableid=='') colorsizetableid=0;

			var updateid=$('#updateid_'+i).val()
			if(updateid=='') updateid=0;

			var reqqty=$('#reqqty_'+i).val()
			if(reqqty=='') reqqty=0;
			
			var poarticle=$('#poarticle_'+i).val(); 
			if(poarticle=='') poarticle='no article';

			if(cons_breck_down==""){
				cons_breck_down+=pocolorid+'_'+gmtssizesid+'_'+des+'_'+brndsup+'_'+itemcolor+'_'+itemsizes+'_'+qty+'_'+excess+'_'+woqny+'_'+rate+'_'+amount+'_'+pcs+'_'+colorsizetableid+'_'+reqqty+'_'+poarticle;
			}
			else{
				cons_breck_down+="__"+pocolorid+'_'+gmtssizesid+'_'+des+'_'+brndsup+'_'+itemcolor+'_'+itemsizes+'_'+qty+'_'+excess+'_'+woqny+'_'+rate+'_'+amount+'_'+pcs+'_'+colorsizetableid+'_'+reqqty+'_'+poarticle;
			}
		}
		document.getElementById('cons_breck_down').value=cons_breck_down;
		parent.emailwindow.hide();
	}
	</script>
	</head>
	<body>
		<?
        extract($_REQUEST);
        if($txt_job_no==""){
			$txt_job_no_cond=""; $txt_job_no_cond1="";
        }
        else{
			$txt_job_no_cond ="and a.job_no='$txt_job_no'"; $txt_job_no_cond1 ="and job_no='$txt_job_no'";
        }
        if($txt_country=="" || $txt_country==0) $txt_country_cond=""; else $txt_country_cond ="and c.country_id in ($txt_country)";
        
		$booking_no=str_replace("'","",$txt_booking_no);
		$reqsn_no=str_replace("'","",$txt_reqsn_no);
		
        $process_loss_method=return_field_value("process_loss_method", "variable_order_tracking", "company_name=$cbo_company_name  and variable_list=18 and item_category_id=4 and status_active=1 and is_deleted=0");
        $tot_po_qty=0;
        $sql_po_qty=sql_select("select b.id, sum(c.order_quantity) as order_quantity, (sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id in($txt_po_id) $txt_country_cond  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id, a.total_set_qnty"); //,c.item_number_id
		//echo "select b.id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id in($txt_po_id)  $txt_country_cond  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty"; die;
        foreach($sql_po_qty as$sql_po_qty_row){
			$po_qty_arr[$sql_po_qty_row[csf('id')]]=$sql_po_qty_row[csf('order_quantity_set')];
			$tot_po_qty+=$sql_po_qty_row[csf('order_quantity_set')];
        }
		//echo $txt_pre_des.'DDDD';
        ?>
        <div align="center" style="width:1150px;" >
            <fieldset>
                <form id="consumptionform_1" autocomplete="off">
                    <table width="1150" cellspacing="0" class="rpt_table" border="0" id="tbl_consmption_cost" rules="all">
                        <thead>
                        	<tr>
                                <th colspan="14" id="td_sync_msg" style="color:#FF0000"></th>
                            </tr>
                            <tr>
                                <th colspan="14">
                                    <input type="hidden" id="cons_breck_down" name="cons_breck_down" value="" />
                                    <input type="hidden" id="txtwoq" value="<?=$txt_req_quantity; ?>"/>
                                    Wo Qty:<input type="text" id="txtwoq_qty" class="text_boxes_numeric" onBlur="poportionate_qty(this.value);" value="<?=$txtwoq; ?>"/>
                                    <input type="radio" name="copy_basis" value="0" checked>Copy to All
                                    <input type="radio" name="copy_basis" value="1">Gmts Size Wise
                                    <input type="radio" name="copy_basis" value="2">Gmts Color Wise
                                    <input type="radio" name="copy_basis" value="10">No Copy
                                    <input type="hidden" id="process_loss_method_id" name="process_loss_method_id" value="<?=$process_loss_method; ?>"/>
                                    <input type="hidden" id="po_qty" name="po_qty" value="<?=$tot_po_qty; ?>"/>
                                    <input type="hidden" id="desc_id" name="desc_id" />
                                </th>
                            </tr>
                            <tr>
                                <th width="30">SL</th>
                                <th width="100">Article No.</th>
                                <th width="100">Gmts. Color</th>
                                <th width="70">Gmts. Size</th>
                                <th width="100">Description</th>
                                <th width="100">Brand/ Sup Ref</th>
                                <th width="100">Item Color</th>
                                <th width="80">Item Size</th>
                                <th width="70"> Wo Qty</th>
                                <th width="40">Excess %</th>
                                <th width="70">WO Qty.</th>
                                <th width="120">Rate</th>
                                <th width="100">Amount</th>
                                <th>RMG Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?
                        $sql_lib_item_group_array=array();
                        $sql_lib_item_group=sql_select("select id, item_name,conversion_factor,order_uom as cons_uom from lib_item_group");
                        foreach($sql_lib_item_group as $row_sql_lib_item_group){
							$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][item_name]=$row_sql_lib_item_group[csf('item_name')];
							$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][conversion_factor]=$row_sql_lib_item_group[csf('conversion_factor')];
							$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][cons_uom]=$row_sql_lib_item_group[csf('cons_uom')];
                        }

						if($txt_update_dtls_id!='' ) $txt_update_dtlsCond="and wo_trim_booking_dtls_id in ($txt_update_dtls_id)";else $txt_update_dtlsCond="";

                        $booking_data_arr=array();
                        $booking_data=sql_select("select id, wo_trim_booking_dtls_id, description, brand_supplier, item_color, item_size, cons, process_loss_percent, requirment, rate, amount, pcs, color_size_table_id from wo_trim_book_con_dtls where status_active=1 and is_deleted=0 and booking_no='$booking_no' $txt_job_no_cond1 $txt_update_dtlsCond");
						if(count($booking_data)<1)
						{
							$booking_data=sql_select("select id, wo_trim_booking_dtls_id, description, brand_supplier, item_color, item_size, cons, process_loss_percent, requirment, rate, amount, pcs, color_size_table_id from wo_trim_book_con_dtls where status_active=1 and is_deleted=0 and booking_no='$reqsn_no' $txt_job_no_cond1 $txt_update_dtlsCond");
						}
                        foreach($booking_data as $booking_data_row){
							$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][id]=$booking_data_row[csf('id')];
							$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][description]=$booking_data_row[csf('description')];
							$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][brand_supplier]=$booking_data_row[csf('brand_supplier')];
							$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][item_color]=$booking_data_row[csf('item_color')];
							$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][item_size]=$booking_data_row[csf('item_size')];

							$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][cons]+=$booking_data_row[csf('cons')];
							$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][process_loss_percent]=$booking_data_row[csf('process_loss_percent')];
							$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][requirment]+=$booking_data_row[csf('requirment')];
							$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][rate]=$booking_data_row[csf('rate')];
							$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][amount]+=$booking_data_row[csf('amount')];
                        }
                        $condition= new condition();
                        if(str_replace("'","",$txt_po_id) !=''){
							$condition->po_id("in($txt_po_id)");
                        }

                        $condition->init();
                        $trims= new trims($condition);

                        $gmt_color_edb=""; $item_color_edb=""; $gmt_size_edb=""; $item_size_edb="";
                        if($cbo_colorsizesensitive==1){
							$req_qty_arr=$trims->getQtyArray_by_orderPrecostdtlsidAndGmtscolor();
							$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidAndGmtscolor();
							$sql="select b.id, b.po_number, b.po_quantity, min(c.id) as color_size_table_id, c.color_number_id, min(c.color_order) as color_order, sum(c.order_quantity) as order_quantity, (sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where  a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.id in($txt_po_id) $txt_country_cond and d.id=$cbo_trim_precost_id group by b.id, b.po_number, b.po_quantity, a.total_set_qnty, c.color_number_id order by b.id, color_order"; 
							$gmt_size_edb=1;
							$item_size_edb=1;
                        }
                        else if($cbo_colorsizesensitive==2){
							//$req_qty_arr=$trims->getQtyArray_by_orderPrecostdtlsidAndGmtssize();
							//$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidAndGmtssize();
							$req_qty_arr=$trims->getQtyArray_by_orderPrecostdtlsidGmtssizeAndArticle();
							$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidGmtssizeAndArticle();
							$sql="select b.id, b.po_number, b.po_quantity,min(c.id) as color_size_table_id, c.size_number_id, c.article_number, min(c.size_order) as size_order, min(e.item_size) as item_size, sum(c.order_quantity) as order_quantity, (sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number, b.po_quantity, a.total_set_qnty, c.article_number, c.size_number_id order by b.id, size_order";
							$gmt_color_edb=1;
							$item_color_edb=1;
                        }
                        else if($cbo_colorsizesensitive==3){
							$req_qty_arr=$trims->getQtyArray_by_orderPrecostdtlsidAndGmtscolor();
							$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidAndGmtscolor();
							$sql="select b.id, b.po_number, b.po_quantity, min(c.id) as color_size_table_id, c.color_number_id, min(c.color_order) as color_order, sum(c.order_quantity) as order_quantity, (sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number, b.po_quantity, a.total_set_qnty, c.color_number_id order by b.id, color_order";
							$gmt_size_edb=1;
							$item_size_edb=1;
                        }
                        else if($cbo_colorsizesensitive==4){
							//$req_qty_arr=$trims->getQtyArray_by_OrderPrecostdtlsidGmtscolorAndGmtssize();
							//$req_amount_arr=$trims->getAmountArray_by_OrderPrecostdtlsidGmtscolorAndGmtssize();
							$req_qty_arr=$trims->getQtyArray_by_OrderPrecostdtlsidGmtscolorGmtssizeAndArticle();
							$req_amount_arr=$trims->getAmountArray_by_OrderPrecostdtlsidGmtscolorGmtssizeAndArticle();
							$sql="select b.id, b.po_number, b.po_quantity, min(c.id) as color_size_table_id, c.color_number_id, c.article_number, c.size_number_id, min(c.color_order) as color_order, min(c.size_order) as size_order, min(e.item_size) as item_size, sum(c.order_quantity) as order_quantity, (sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id) $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number, b.po_quantity, a.total_set_qnty, c.color_number_id, c.article_number, c.size_number_id  order by b.id, color_order, size_order";
                        }
                        else{
							$req_qty_arr=$trims->getQtyArray_by_orderAndPrecostdtlsid();
							$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsid();
							$sql="select b.id, b.po_number, b.po_quantity, min(c.id) as color_size_table_id, sum(c.order_quantity) as order_quantity, (sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.id in($txt_po_id) $txt_country_cond and d.id=$cbo_trim_precost_id group by b.id, b.po_number, b.po_quantity, a.total_set_qnty order by b.id";
                        }
						//echo $sql;
                        $po_color_level_data_arr=array(); $po_size_level_data_arr=array(); $po_no_sen_level_data_arr=array(); $po_color_size_level_data_arr=array();
                        $data_array=sql_select($sql);
                        if(count($data_array)>0){
							$i=0;
							foreach( $data_array as $row ){
								$data=explode('_',$data_array_cons[$i]);
								$i++;
								$item_color = $booking_data_arr[$row[csf('color_size_table_id')]][item_color];
								if($item_color==0 || $item_color=="" ) $item_color = $row[csf('color_number_id')];
								
								$item_size=$booking_data_arr[$row[csf('color_size_table_id')]][item_size];
								if($item_size==0 || $item_size == "") $item_size=$row[csf('item_size')];

								$rate=$booking_data_arr[$row[csf('color_size_table_id')]][rate];
								if($rate==0 || $rate=="") $rate=$txt_avg_price;

								$description=$booking_data_arr[$row[csf('color_size_table_id')]][description];

								if($description=="") $description=$txt_pre_des;

								//echo $description.'='.$txt_pre_des;
								$brand_supplier=$booking_data_arr[$row[csf('color_size_table_id')]][brand_supplier];
								if($brand_supplier=="") $brand_supplier=$txt_pre_brand_sup;
								$po_qty=$row[csf('order_quantity')];
								
								if($cbo_colorsizesensitive==1 || $cbo_colorsizesensitive==3 ){

									$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]];
									$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
									$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");

									$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
									$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['po_qty'][$row[csf('id')]]=$po_qty;
									$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
									$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['po_id'][$row[csf('id')]]=$row[csf('id')];
									$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];
									$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_cons'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][cons];
									$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_qty'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][requirment];
								}
								else if($cbo_colorsizesensitive==2){
									$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]];
									$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
									$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");
									$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
									$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_qty'][$row[csf('id')]]=$po_qty;
									$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
									$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_id'][$row[csf('id')]]=$row[csf('id')];
									$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];
									$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_cons'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][cons];
									$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_qty'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][requirment];
								}
								else if($cbo_colorsizesensitive==4){
									$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]];
									$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
									$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");

									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_qty'][$row[csf('id')]]=$po_qty;
									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_id'][$row[csf('id')]]=$row[csf('id')];
									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];
									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_cons'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][cons];
									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_qty'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][requirment];
								}
								else if($cbo_colorsizesensitive==0){
									$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id];
									$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
									$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");

									$po_no_sen_level_data_arr[$cbo_trim_precost_id]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
									$po_no_sen_level_data_arr[$cbo_trim_precost_id]['po_qty'][$row[csf('id')]]=$po_qty;
									$po_no_sen_level_data_arr[$cbo_trim_precost_id]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
									$po_no_sen_level_data_arr[$cbo_trim_precost_id]['po_id'][$row[csf('id')]]=$row[csf('id')];
									$po_no_sen_level_data_arr[$cbo_trim_precost_id]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];
									$po_no_sen_level_data_arr[$cbo_trim_precost_id]['booking_cons'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][cons];
									$po_no_sen_level_data_arr[$cbo_trim_precost_id]['booking_qty'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][requirment];
								}
							}
                        }
						/*echo "<pre>";
						print_r($po_no_sen_level_data_arr); die;*/

						$piNumber=0;
						$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no='$txt_booking_no' and b.item_group='".$txt_trim_group_id."' and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
						if($pi_number) $piNumber=1;
						
						$recvNumber=0;
						$recv_number=return_field_value( "recv_number", "inv_receive_master a,inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no='$txt_booking_no' and b.item_group_id='".$txt_trim_group_id."' and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
						if($recv_number) $recvNumber=1;

                        if ( count($data_array)>0 && $cbo_level==1){
							$i=0;
							foreach( $data_array as $row ){
								$data=explode('_',$data_array_cons[$i]);

								if($cbo_colorsizesensitive==1 || $cbo_colorsizesensitive==3 ){
									$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]];
									$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
									$txtwoq_cal = def_number_format($req_qnty_ord_uom,5,"");
								}
								else if($cbo_colorsizesensitive==2){
									$txt_req_quantity = $req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]];
									$req_qnty_ord_uom = def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
									$txtwoq_cal = def_number_format($req_qnty_ord_uom,5,"");
								}
								else if($cbo_colorsizesensitive==4){
									$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]];
									$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
									$txtwoq_cal = def_number_format($req_qnty_ord_uom,5,"");
								}
								else if($cbo_colorsizesensitive==0){
									$txt_req_quantity = $req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id];
									$req_qnty_ord_uom = def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
									$txtwoq_cal = def_number_format($req_qnty_ord_uom,5,"");
								}


								$item_color = $booking_data_arr[$row[csf('color_size_table_id')]][item_color];
								if($item_color==0 || $item_color=="" ) $item_color = $row[csf('color_number_id')];

								$item_size=$booking_data_arr[$row[csf('color_size_table_id')]][item_size];
								if($item_size==0 || $item_size == "") $item_size=$size_library[$row[csf('size_number_id')]];
								
								$rate=$booking_data_arr[$row[csf('color_size_table_id')]][rate];
								if($rate==0 || $rate=="") $rate=$txt_avg_price;
								
								$description=$booking_data_arr[$row[csf('color_size_table_id')]][description];
								if($description=="") $description=$txt_pre_des;

								$brand_supplier=$booking_data_arr[$row[csf('color_size_table_id')]][brand_supplier];
								if($brand_supplier=="") $brand_supplier=$txt_pre_brand_sup;
								
								if($txtwoq_cal>0){
									$i++;
									?>
                                    <tr id="break_<?=$i; ?>" align="center">
                                        <td align="center"><?=$i; ?></td>
                                        <td><input type="text" id="poarticle_<?=$i; ?>" name="poarticle_<?=$i; ?>" class="text_boxes" style="width:88px" value="<?=$row[csf('article_number')]; ?>" readonly /></td>
                                        
                                        <td>
                                            <input type="text" id="pocolor_<?=$i; ?>" name="pocolor_<?=$i; ?>" class="text_boxes" style="width:88px" value="<?=$color_library[$row[csf('color_number_id')]]; ?>" <? if($gmt_color_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> readonly/>
                                            <input type="hidden" id="pocolorid_<?=$i; ?>" style="width:70px" value="<?=$row[csf('color_number_id')]; ?>" />
                                            <input type="hidden" id="poid_<?=$i; ?>" style="width:70px" value="<?=$row[csf('id')]; ?>" />
                                            <input type="hidden" id="poqty_<?=$i; ?>" style="width:70px" value="<?=$po_qty_arr[$row[csf('id')]]; ?>" />
                                            <input type="hidden" id="poreqqty_<?=$i; ?>" style="width:70px" value="<?=$txtwoq_cal; ?>" />
                                        </td>
                                        <td>
                                            <input type="text" id="gmtssizes_<?=$i; ?>" name="gmtssizes_<?=$i; ?>" class="text_boxes" style="width:58px" value="<?=$size_library[$row[csf('size_number_id')]]; ?>" <? if($gmt_size_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> readonly />
                                            <input type="hidden" id="gmtssizesid_<?=$i; ?>" style="width:50px" value="<?=$row[csf('size_number_id')]; ?>" />
                                        </td>
                                        <td><input type="text" id="des_<?=$i; ?>" name="des_<?=$i; ?>" class="text_boxes" style="width:88px" value="<?=$description; ?>" onChange="copy_value(this.value,'des_',<?=$i; ?>);" <? if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> /></td>
                                        <td><input type="text" id="brndsup_<?=$i; ?>" name="brndsup_<?=$i; ?>" class="text_boxes" style="width:88px" value="<?=$brand_supplier; ?>" onChange="copy_value(this.value,'brndsup_',<?=$i; ?>);" <? if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> /></td>
                                        <td><input type="text" id="itemcolor_<?=$i; ?>" value="<?=$color_library[$item_color]; ?>" name="itemcolor_<?=$i; ?>" class="text_boxes" style="width:88px" onChange="copy_value(this.value,'itemcolor_',<?=$i; ?>);" <? if($item_color_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> /></td>
                                        <td><input type="text" id="itemsizes_<?=$i; ?>" name="itemsizes_<?=$i; ?>" class="text_boxes" style="width:68px" onChange="copy_value(this.value,'itemsizes_',<?=$i; ?>);" value="<?=$item_size; ?>" <? if($item_size_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> /></td>
                                        <td>
                                            <input type="hidden" id="reqqty_<?=$i; ?>" style="width:50px" value="<?=$txtwoq_cal; ?>" />
                                            <input type="text" id="qty_<?=$i; ?>" onBlur="validate_sum(<?=$i; ?>);" onChange="set_sum_value( 'qty_sum', 'qty_'); set_sum_value( 'woqty_sum', 'woqny_'); calculate_requirement(<?=$i; ?>); copy_value(this.value,'qty_',<?=$i; ?>);" name="qty_<?=$i; ?>" class="text_boxes_numeric" style="width:58px" placeholder="<?=$txtwoq_cal; ?>" value="<?=$booking_data_arr[$row[csf('color_size_table_id')]][cons]; ?>"/>
                                        </td>
                                        <td><input type="text" id="excess_<?=$i; ?>" onBlur="set_sum_value('excess_sum','excess_');" name="excess_<?=$i; ?>" class="text_boxes_numeric" style="width:28px" onChange="calculate_requirement(<?=$i; ?>); set_sum_value('excess_sum', 'excess_'); set_sum_value('woqty_sum','woqny_'); copy_value(this.value,'excess_',<?=$i; ?>);" value="<?=$booking_data_arr[$row[csf('color_size_table_id')]][process_loss_percent]; ?>" disabled/></td>
                                        <td><input type="text" id="woqny_<?=$i; ?>" onBlur="set_sum_value('woqty_sum','woqny_');" onChange="set_sum_value('woqty_sum','woqny_');"  name="woqny_<?=$i; ?>" class="text_boxes_numeric" style="width:58px" value="<?=$booking_data_arr[$row[csf('color_size_table_id')]][requirment]; ?>" readonly /></td>
                                        <td><input type="text" id="rate_<?=$i; ?>" name="rate_<?=$i; ?>" class="text_boxes_numeric" style="width:108px" onChange="calculate_amount(<?=$i; ?>); set_sum_value('amount_sum','amount_'); copy_value(this.value,'rate_',<?=$i; ?>);" value="<?=$rate; ?>" <? if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> /></td>
                                        <td><input type="text" id="amount_<?=$i; ?>" name="amount_<?=$i; ?>" onBlur="set_sum_value('amount_sum','amount_');" class="text_boxes_numeric" style="width:88px" value="<?=$booking_data_arr[$row[csf('color_size_table_id')]][amount]; ?>" readonly /></td>
                                        <td>
                                            <input type="text" id="pcs_<?=$i; ?>" name="pcs_<?=$i; ?>" onBlur="set_sum_value('pcs_sum','pcs_');" class="text_boxes_numeric" style="width:50px" value="<?=$row[csf('order_quantity')]; ?>" readonly>
                                            <input type="hidden" id="pcsset_<?=$i;?>" name="pcsset_<?=$i;?>" onBlur="set_sum_value('pcs_sum','pcs_');" class="text_boxes_numeric" style="width:50px" value="<?=$row[csf('order_quantity_set')]; ?>" readonly>
                                            <input type="hidden" id="colorsizetableid_<?=$i;?>" name="colorsizetableid_<?=$i; ?>" class="text_boxes" style="width:85px" value="<?=$row[csf('color_size_table_id')]; ?>" readonly />
                                            <input type="hidden" id="updateid_<?=$i; ?>" name="updateid_<?=$i; ?>" class="text_boxes" style="width:85px" value="<?=$booking_data_arr[$row[csf('color_size_table_id')]][id]; ?>" readonly />
                                        </td>
                                    </tr>
                                <?
                                }
                            }
                        }
                        
                        $level_arr=array(); $gmt_color_edb=""; $item_color_edb=""; $gmt_size_edb=""; $item_size_edb="";
                        if($cbo_colorsizesensitive==1){
							$sql="select min(b.id) as id, min(c.id) as color_size_table_id, c.color_number_id, min(c.color_order) as color_order, sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by c.color_number_id order by color_order";
							$level_arr=$po_color_level_data_arr;
							$gmt_size_edb=1;
							$item_size_edb=1;
                        }
                        else if($cbo_colorsizesensitive==2){
							$sql="select min(b.id) as id, min(c.id) as color_size_table_id, c.article_number, c.size_number_id, min(c.size_order) as size_order, min(e.item_size) as item_size, sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.id in($txt_po_id) $txt_country_cond and d.id=$cbo_trim_precost_id group by c.size_number_id, c.article_number order by size_order";
							$level_arr=$po_size_level_data_arr;
							$gmt_color_edb=1;
							$item_color_edb=1;
                        }
                        else if($cbo_colorsizesensitive==3){
							$sql="select min(b.id) as id, min(c.id) as color_size_table_id, c.color_number_id, min(c.color_order) as color_order, sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id) $txt_country_cond and d.id=$cbo_trim_precost_id group by c.color_number_id order by color_order";
							$level_arr=$po_color_level_data_arr;
							$gmt_size_edb=1;
							$item_size_edb=1;
                        }
                        else if($cbo_colorsizesensitive==4){
							$sql="select min(b.id) as id, min(c.id) as color_size_table_id, c.color_number_id, c.size_number_id, c.article_number, min(c.color_order) as color_order, min(c.size_order) as size_order, min(e.item_size) as item_size, sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.id in($txt_po_id) $txt_country_cond and d.id=$cbo_trim_precost_id group by c.color_number_id, c.article_number, c.size_number_id  order by  color_order, size_order";
							$level_arr=$po_color_size_level_data_arr;
                        }
                        else{
							$sql="select b.job_no_mst, min(b.id) as id, min(c.id) as color_size_table_id, sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.id in($txt_po_id) $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.job_no_mst";
							$level_arr=$po_no_sen_level_data_arr;
                        }
                        $data_array=sql_select($sql);
                        if ( count($data_array)>0 && $cbo_level==2){
							$i=0;
							foreach( $data_array as $row ){

								if($cbo_colorsizesensitive==1){
									$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['req_qty']),5,"");
									$po_qty=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['po_qty']);
									$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['order_quantity_set']);
									$booking_cons=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_cons']),5,"");
									$booking_qty=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_qty']),5,"");
								}
								if($cbo_colorsizesensitive==2){
									$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['req_qty']),5,"");
									$po_qty=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_qty']);
									$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity_set']);
									$booking_cons=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_cons']),5,"");
									$booking_qty=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_qty']),5,"");
								}
								if($cbo_colorsizesensitive==3){
									$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['req_qty']),5,"");
									$po_qty=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['po_qty']);
									$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['order_quantity_set']);
									$booking_cons=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_cons']),5,"");
									$booking_qty=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_qty']),5,"");
								}
								if($cbo_colorsizesensitive==4){
									$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['req_qty']),5,"");
									$po_qty=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_qty']);
									$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity_set']);
									$booking_cons=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_cons']),5,"");
									$booking_qty=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_qty']),5,"");
								}
								if($cbo_colorsizesensitive==0){
									$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id]['req_qty']),5,"");
									$po_qty=array_sum($level_arr[$cbo_trim_precost_id]['po_qty']);
									$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id]['order_quantity_set']);
									$booking_cons=def_number_format(array_sum($level_arr[$cbo_trim_precost_id]['booking_cons']),5,"");
									$booking_qty=def_number_format(array_sum($level_arr[$cbo_trim_precost_id]['booking_qty']),5,"");
								}

								$item_color = $booking_data_arr[$row[csf('color_size_table_id')]][item_color];
								if($item_color==0 || $item_color=="" ) $item_color = $row[csf('color_number_id')];

								$item_size=$booking_data_arr[$row[csf('color_size_table_id')]][item_size];
								if($item_size==0 || $item_size == "") $item_size=$size_library[$row[csf('size_number_id')]];

								$rate=$booking_data_arr[$row[csf('color_size_table_id')]][rate];
								if($rate==0 || $rate=="") $rate=$txt_avg_price;

								$description=$booking_data_arr[$row[csf('color_size_table_id')]][description];
								//echo $description.'='.$txt_pre_des.'<br/>';
								if($description=="") $description=$txt_pre_des;

								$brand_supplier=$booking_data_arr[$row[csf('color_size_table_id')]][brand_supplier];
								if($brand_supplier=="") $brand_supplier=$txt_pre_brand_sup;
								
								if($txtwoq_cal>0){
									$i++;
									?>
									<tr id="break_<?=$i; ?>" align="center">
                                        <td align="center"><?=$i; ?></td>
                                        <td><input type="text" id="poarticle_<?=$i; ?>" name="poarticle_<?=$i; ?>" class="text_boxes" style="width:88px" value="<?=$row[csf('article_number')]; ?>" readonly /></td>
                                        <td>
                                            <input type="text" id="pocolor_<?=$i; ?>" name="pocolor_<?=$i; ?>" class="text_boxes" style="width:88px" value="<?=$color_library[$row[csf('color_number_id')]]; ?>" <? if($gmt_color_edb || $piNumber || $recvNumber){ echo "disabled";}else { echo "";} ?> readonly/>
                                            <input type="hidden" id="pocolorid_<?=$i; ?>" style="width:70px" value="<?=$row[csf('color_number_id')]; ?>" />
                                            <input type="hidden" id="poid_<?=$i; ?>" style="width:70px" value="<?=$row[csf('id')]; ?>" />
                                            <input type="hidden" id="poqty_<?=$i; ?>" style="width:70px" value="<?=$po_qty; ?>" />
                                            <input type="hidden" id="poreqqty_<?=$i; ?>" style="width:70px" value="<?=$txtwoq_cal; ?>" />
                                        </td>
                                        <td>
                                            <input type="text" id="gmtssizes_<?=$i; ?>" name="gmtssizes_<?=$i; ?>" class="text_boxes" style="width:58px" value="<?=$size_library[$row[csf('size_number_id')]]; ?>" <? if($gmt_size_edb || $piNumber || $recvNumber ){ echo "disabled";}else { echo "";} ?>/>
                                            <input type="hidden" id="gmtssizesid_<?=$i; ?>" style="width:50px" value="<?=$row[csf('size_number_id')]; ?>" />
                                        </td>
                                        <td><input type="text" id="des_<?=$i; ?>" name="des_<?=$i; ?>" class="text_boxes" style="width:88px" value="<?=$description; ?>" onChange="copy_value(this.value,'des_',<?=$i; ?>);" <? if( $piNumber || $recvNumber ){ echo "disabled";}else { echo "";} ?> /></td>
                                        <td><input type="text" id="brndsup_<?=$i; ?>" name="brndsup_<?=$i; ?>" class="text_boxes" style="width:88px" value="<?=$brand_supplier; ?>" onChange="copy_value(this.value,'brndsup_',<?=$i; ?>);" <? if( $piNumber || $recvNumber ){ echo "disabled";}else { echo "";} ?> /></td>
                                        <td><input type="text" id="itemcolor_<?=$i; ?>" value="<?=$color_library[$item_color]; ?>" name="itemcolor_<?=$i; ?>" class="text_boxes" style="width:88px" onChange="copy_value(this.value,'itemcolor_',<?=$i; ?>);" <? if($item_color_edb || $piNumber || $recvNumber ){ echo "disabled";}else { echo "";} ?> /></td>
                                        <td><input type="text" id="itemsizes_<?=$i; ?>" name="itemsizes_<?=$i; ?>" class="text_boxes" style="width:68px" onChange="copy_value(this.value,'itemsizes_',<?=$i; ?>);" value="<?=$item_size; ?>" <? if($item_size_edb || $piNumber || $recvNumber ){ echo "disabled";}else { echo "";} ?> /></td>
                                        <td>
                                            <input type="hidden" id="reqqty_<?=$i; ?>" name="reqqty_<?=$i; ?>" style="width:58px" value="<?=$txtwoq_cal; ?>" readonly/>
                                            <input type="text" id="qty_<?=$i; ?>" onBlur="validate_sum(<?=$i; ?>);" onChange="set_sum_value('qty_sum','qty_'); set_sum_value( 'woqty_sum','woqny_'); calculate_requirement(<?=$i; ?>); copy_value(this.value,'qty_',<?=$i; ?>);" name="qty_<?=$i; ?>" class="text_boxes_numeric" style="width:58px" placeholder="<?=$txtwoq_cal; ?>" value="<? if($booking_cons>0){ echo $booking_cons;} ?>"/>
                                        </td>
                                        <td><input type="text" id="excess_<?=$i; ?>" onBlur="set_sum_value('excess_sum','excess_');" name="excess_<?=$i; ?>" class="text_boxes_numeric" style="width:28px" onChange="calculate_requirement(<?=$i; ?>); set_sum_value('excess_sum','excess_'); set_sum_value('woqty_sum','woqny_'); copy_value(this.value,'excess_',<?=$i; ?>);" value="<?=$booking_data_arr[$row[csf('color_size_table_id')]][process_loss_percent]; ?>" disabled/></td>
                                        <td><input type="text" id="woqny_<?=$i; ?>" onBlur="set_sum_value('woqty_sum','woqny_');" onChange="set_sum_value('woqty_sum','woqny_');"  name="woqny_<?=$i; ?>" class="text_boxes_numeric" style="width:58px" value="<? if($booking_qty){echo $booking_qty;} ?>" readonly /></td>
                                        <td><input type="text" id="rate_<?=$i; ?>" name="rate_<?=$i; ?>" class="text_boxes_numeric" style="width:108px" onChange="calculate_amount(<?=$i; ?>); set_sum_value('amount_sum','amount_'); copy_value(this.value,'rate_',<?=$i; ?>);" value="<?=$rate; ?>" <? if( $piNumber || $recvNumber ){ echo "disabled";}else { echo "";} ?> /></td>
                                        <td><input type="text" id="amount_<?=$i; ?>" name="amount_<?=$i; ?>" onBlur="set_sum_value('amount_sum','amount_');" class="text_boxes_numeric" style="width:88px" value="<?=$booking_data_arr[$row[csf('color_size_table_id')]][amount]; ?>" readonly /></td>
                                        <td>
                                            <input type="text" id="pcs_<?=$i; ?>" name="pcs_<?=$i; ?>" onBlur="set_sum_value('pcs_sum','pcs_');" class="text_boxes_numeric" style="width:50px" value="<?=$row[csf('order_quantity')]; ?>" readonly>
                                            <input type="hidden" id="pcsset_<?=$i; ?>" onBlur="set_sum_value('pcs_sum','pcs_');" style="width:50px" value="<?=$order_quantity_set; ?>" readonly>
                                            <input type="hidden" id="colorsizetableid_<?=$i; ?>" style="width:50px" value="<?=$row[csf('color_size_table_id')]; ?>" readonly />
                                            <input type="hidden" id="updateid_<?=$i; ?>" style="width:50px" value="<?=$booking_data_arr[$row[csf('color_size_table_id')]][id]; ?>" readonly />
                                        </td>
									</tr>
								<?
								}
							}
                        }
                        ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th width="30">&nbsp;</th>
                                <th width="100">&nbsp;</th>
                                <th width="100">&nbsp;</th>
                                <th width="70">&nbsp;</th>
                                <th width="100">&nbsp;</th>
                                <th width="100">&nbsp;</th>
                                <th width="100">&nbsp;</th>
                                <th width="80">&nbsp;</th>
                                <th width="70"><input type="text" id="qty_sum" name="qty_sum" class="text_boxes_numeric" style="width:58px" readonly></th>
                                <th width="40"><input type="text" id="excess_sum"  name="excess_sum" class="text_boxes_numeric" style="width:28px" readonly></th>
                                <th width="70"><input type="text" id="woqty_sum"  name="woqty_sum" class="text_boxes_numeric" style="width:58px" readonly></th>
                                <th width="120"><input type="text" id="rate_sum"  name="rate_sum" class="text_boxes_numeric" style="width:108px" readonly></th>
                                <th width="100"><input type="text" id="amount_sum" name="amount_sum" class="text_boxes_numeric" style="width:88px" readonly></th>
                                <th>
                                    <input type="hidden" id="json_data" name="json_data" style="width:50px" value='<?=json_encode($level_arr); ?>' readonly>
                                    <input type="text" id="pcs_sum" name="pcs_sum" class="text_boxes_numeric" style="width:50px" readonly>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                    <table width="1150" cellspacing="0" class="" border="0" rules="all">
                        <tr>
                            <td align="center" width="100%"> <input type="button" class="formbutton" value="Close" onClick="js_set_value();"/> </td>
                        </tr>
                    </table>
                </form>
            </fieldset>
        </div>
	</body>
	<script>
	$("input[type=text]").focus(function() {
	   $(this).select();
	});
	<?
	if($txt_update_dtls_id==""){
		?>
		poportionate_qty(<? echo $txtwoq; ?>);
		<?
	}
	?>
	set_sum_value( 'qty_sum', 'qty_' );
	set_sum_value( 'woqty_sum', 'woqny_' );
	set_sum_value( 'amount_sum', 'amount_' );
	set_sum_value( 'pcs_sum', 'pcs_' );
	calculate_avg_rate();

	var wo_qty=$('#txtwoq_qty').val()*1;

	var wo_qty_sum=$('#qty_sum').val()*1;

	if(wo_qty!=wo_qty_sum)
	{
		$('#td_sync_msg').html("Booking Info not synchronized with order entry and pre-costing. order entry or pre-costing has updated after booking entry.");
	}
	</script>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
    exit();
}

if ($action=="set_cons_break_down"){
	$color_library=return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$data=explode("_",$data);
	$garments_nature=$data[0];
	$cbo_company_name=$data[1];
	$txt_job_no=$data[2];
	$txt_po_id=$data[3];
	$cbo_trim_precost_id=$data[4];
	$txt_trim_group_id=$data[5];
	$txt_update_dtls_id=$data[6];
	$cbo_colorsizesensitive=$data[7];
	$txt_req_quantity=$data[8];
	$txt_avg_price=$data[9];
	$txt_country=$data[10];
	$txt_pre_des=$data[11];
	$txt_pre_brand_sup=$data[12];
	$cbo_level=$data[13];
	$cbo_basis_id=$data[15];
	$txt_reqsn_no=$data[16];

	if($txt_job_no==""){
		$txt_job_no_cond=""; $txt_job_no_cond1="";
	}
	else{
		$txt_job_no_cond ="and a.job_no='$txt_job_no'"; $txt_job_no_cond1 ="and job_no='$txt_job_no'";
	}

	if($txt_country=="") $txt_country_cond=""; else $txt_country_cond ="and c.country_id in ($txt_country)";

	$process_loss_method=return_field_value("process_loss_method", "variable_order_tracking", "company_name=$cbo_company_name  and variable_list=18 and item_category_id=4 and status_active=1 and is_deleted=0");
	
	$sql_po_qty=sql_select("select b.id,sum(c.order_quantity) as order_quantity, (sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id in($txt_po_id)  $txt_country_cond  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty"); //,c.item_number_id
	$tot_po_qty=0;
	foreach($sql_po_qty as $sql_po_qty_row){
		$po_qty_arr[$sql_po_qty_row[csf('id')]]=$sql_po_qty_row[csf('order_quantity_set')];
		$tot_po_qty+=$sql_po_qty_row[csf('order_quantity_set')];
	}
	$sql_lib_item_group_array=array();
	$sql_lib_item_group=sql_select("select id, item_name,conversion_factor,order_uom as cons_uom from lib_item_group");
	foreach($sql_lib_item_group as $row_sql_lib_item_group){
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][item_name]=$row_sql_lib_item_group[csf('item_name')];
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][conversion_factor]=$row_sql_lib_item_group[csf('conversion_factor')];
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][cons_uom]=$row_sql_lib_item_group[csf('cons_uom')];
	}

	$booking_data_arr=array();
	$booking_data=sql_select("select id, wo_trim_booking_dtls_id, description, brand_supplier, item_color, item_size, cons, process_loss_percent, requirment, rate, 	amount, pcs, color_size_table_id from wo_trim_book_con_dtls where wo_trim_booking_dtls_id in($txt_update_dtls_id) and status_active=1 and is_deleted=0");
	foreach($booking_data as $booking_data_row){
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][id]=$booking_data_row[csf('id')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][description]=$booking_data_row[csf('description')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][brand_supplier]=$booking_data_row[csf('brand_supplier')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][item_color]=$booking_data_row[csf('item_color')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][item_size]=$booking_data_row[csf('item_size')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][cons]+=$booking_data_row[csf('cons')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][process_loss_percent]=$booking_data_row[csf('process_loss_percent')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][requirment]+=$booking_data_row[csf('requirment')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][rate]=$booking_data_row[csf('rate')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][amount]+=$booking_data_row[csf('amount')];
	}
		$cu_booking_data_arr=array();
	$cu_booking_data=sql_select("select a.pre_cost_fabric_cost_dtls_id, b.id, b.wo_trim_booking_dtls_id, b.po_break_down_id, b.color_number_id, b.gmts_sizes, b.requirment from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id=b.wo_trim_booking_dtls_id and b.po_break_down_id in($txt_po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");
	foreach($cu_booking_data as $cu_booking_data_row){
		if($cbo_colorsizesensitive==1 || $cbo_colorsizesensitive==3 ){
			$cu_booking_data_arr[$cu_booking_data_row[csf('po_break_down_id')]][$cu_booking_data_row[csf('pre_cost_fabric_cost_dtls_id')]][$cu_booking_data_row[csf('color_number_id')]]+=$cu_booking_data_row[csf('requirment')];
		}
		if($cbo_colorsizesensitive==2 ){
			$cu_booking_data_arr[$cu_booking_data_row[csf('po_break_down_id')]][$cu_booking_data_row[csf('pre_cost_fabric_cost_dtls_id')]][$cu_booking_data_row[csf('gmts_sizes')]]+=$cu_booking_data_row[csf('requirment')];
		}
		if($cbo_colorsizesensitive==4 ){
			$cu_booking_data_arr[$cu_booking_data_row[csf('po_break_down_id')]][$cu_booking_data_row[csf('pre_cost_fabric_cost_dtls_id')]][$cu_booking_data_row[csf('color_number_id')]][$cu_booking_data_row[csf('gmts_sizes')]]+=$cu_booking_data_row[csf('requirment')];
		}
		if($cbo_colorsizesensitive==0 ){
			$cu_booking_data_arr[$cu_booking_data_row[csf('po_break_down_id')]][$cu_booking_data_row[csf('pre_cost_fabric_cost_dtls_id')]]+=$cu_booking_data_row[csf('requirment')];
		}
	}

	$condition= new condition();
	if(str_replace("'","",$txt_po_id) !=''){
			$condition->po_id("in($txt_po_id)");
	}

	$condition->init();
	$trims= new trims($condition);
	$gmt_color_edb="";
	$item_color_edb="";
	$gmt_size_edb="";
	$item_size_edb="";
	if($cbo_colorsizesensitive==1){
		$req_qty_arr=$trims->getQtyArray_by_orderPrecostdtlsidAndGmtscolor();
		$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidAndGmtscolor();
		 $sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.color_number_id,min(c.color_order) as color_order,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id order by b.id, color_order";
		$gmt_size_edb="disabled";
		$item_size_edb="disabled";
	}
	else if($cbo_colorsizesensitive==2){
	    $req_qty_arr=$trims->getQtyArray_by_orderPrecostdtlsidAndGmtssize();
		$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidAndGmtssize();
		$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.size_number_id,min(c.size_order) as size_order,min(e.item_size) as item_size,sum(c.order_quantity) as order_quantity ,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.size_number_id order by b.id,size_order";
		$gmt_color_edb="disabled";
		$item_color_edb="disabled";
	}
	else if($cbo_colorsizesensitive==3){
		$req_qty_arr=$trims->getQtyArray_by_orderPrecostdtlsidAndGmtscolor();
		$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidAndGmtscolor();
		$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.color_number_id,min(c.color_order) as color_order,sum(c.order_quantity) as order_quantity ,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id order by b.id, color_order";
		$gmt_size_edb="disabled";
		$item_size_edb="disabled";
	}
	else if($cbo_colorsizesensitive==4){

		$req_qty_arr=$trims->getQtyArray_by_OrderPrecostdtlsidGmtscolorAndGmtssize();
		$req_amount_arr=$trims->getAmountArray_by_OrderPrecostdtlsidGmtscolorAndGmtssize();

		 $sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.color_number_id,c.size_number_id,min(c.color_order) as color_order,min(c.size_order) as size_order,min(e.item_size) as item_size,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id,c.size_number_id  order by b.id, color_order,size_order";
	}
	else{
		$req_qty_arr=$trims->getQtyArray_by_orderAndPrecostdtlsid();
	    $req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsid();
		$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id) $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty order by b.id";
	}

	$data_array=sql_select($sql);
	if ( count($data_array)>0)
	{
		$i=0;
		foreach( $data_array as $row )
		{
			$color_number_id=$row[csf('color_number_id')];
			if($color_number_id=="") $color_number_id=0;

			$size_number_id=$row[csf('size_number_id')];
			if($size_number_id=="") $size_number_id=0;

			$description=$txt_pre_des;
			if($description=="") $description=0;

			$brand_supplier=$txt_pre_brand_sup;
			if($brand_supplier=="") $brand_supplier=0;

			$item_color=$color_library[$row[csf('color_number_id')]];
			if($item_color=="")$item_color=0;

			$item_size=$row[csf('item_size')];
			if($item_size=="")$item_size=0;
			
			$excess=0;
			$pcs=$row[csf('order_quantity_set')];
			if($pcs=="") $pcs=0;

			$colorsizetableid=$row[csf('color_size_table_id')];
			if($colorsizetableid=="") $colorsizetableid=0;
			
			if($cbo_colorsizesensitive==1 || $cbo_colorsizesensitive==3 ){
				$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]];
				$req_qnty_ord_uom = def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
				$cu_qnty_ord_uom = def_number_format($cu_booking_data_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]],5,"");
				$req_qnty_ord_uom = $req_qnty_ord_uom - $cu_qnty_ord_uom;
				$txtwoq_cal = def_number_format($req_qnty_ord_uom,5,"");
				$amount = def_number_format($txtwoq_cal*$txt_avg_price,5,"");
	
				$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
				$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['po_qty'][$row[csf('id')]]=$po_qty;
				$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
				$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['po_id'][$row[csf('id')]]=$row[csf('id')];
				$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];
				$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['amount'][$row[csf('id')]]=$amount;
			}
			else if($cbo_colorsizesensitive==2){
				$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('size_number_id')]];
				$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
				$cu_qnty_ord_uom = def_number_format($cu_booking_data_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('size_number_id')]],5,"");
				$req_qnty_ord_uom = $req_qnty_ord_uom - $cu_qnty_ord_uom;
				$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
	
				$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
				$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]]['po_qty'][$row[csf('id')]]=$po_qty;
				$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
				$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]]['po_id'][$row[csf('id')]]=$row[csf('id')];
				$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];
				$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]]['amount'][$row[csf('id')]]=$amount;
			}
			else if($cbo_colorsizesensitive==4){
				$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]];
				$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
				$cu_qnty_ord_uom = def_number_format($cu_booking_data_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]],5,"");
				$req_qnty_ord_uom = $req_qnty_ord_uom - $cu_qnty_ord_uom;
				$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
	
				$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
				$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['po_qty'][$row[csf('id')]]=$po_qty;
				$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
				$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['po_id'][$row[csf('id')]]=$row[csf('id')];
				$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];
				$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['amount'][$row[csf('id')]]=$amount;
			}
			else if($cbo_colorsizesensitive==0){
				$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id];
				$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
				$cu_qnty_ord_uom = def_number_format($cu_booking_data_arr[$row[csf('id')]][$cbo_trim_precost_id],5,"");
				//$req_qnty_ord_uom = $req_qnty_ord_uom - $cu_qnty_ord_uom;
				$req_qnty_ordUom = def_number_format((($data[14]/$data[8])*$req_qnty_ord_uom),5,"");
				//$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");
				$txtwoq_cal = def_number_format($req_qnty_ordUom,5,"");
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
	
				$po_no_sen_level_data_arr[$cbo_trim_precost_id]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
				$po_no_sen_level_data_arr[$cbo_trim_precost_id]['po_qty'][$row[csf('id')]]=$po_qty;
				$po_no_sen_level_data_arr[$cbo_trim_precost_id]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
				$po_no_sen_level_data_arr[$cbo_trim_precost_id]['po_id'][$row[csf('id')]]=$row[csf('id')];
				$po_no_sen_level_data_arr[$cbo_trim_precost_id]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];
				$po_no_sen_level_data_arr[$cbo_trim_precost_id]['amount'][$row[csf('id')]]=$amount;
			}
		}
	}


	$cons_breck_down="";
	if ( count($data_array)>0 && $cbo_level==1)
	{
		$i=0;
		foreach( $data_array as $row )
		{
			$color_number_id=$row[csf('color_number_id')];
			if($color_number_id=="") $color_number_id=0;

			$size_number_id=$row[csf('size_number_id')];
			if($size_number_id=="") $size_number_id=0;

			$description=$txt_pre_des;
			if($description=="") $description=0;

			$brand_supplier=$txt_pre_brand_sup;
			if($brand_supplier=="") $brand_supplier=0;

			$item_color=$color_library[$row[csf('color_number_id')]];
			if($item_color=="") $item_color=0;

			$item_size=$row[csf('item_size')];
			if($item_size=="") $item_size=0;
			
			$excess=0;

			$pcs=$row[csf('order_quantity_set')];
			if($pcs=="") $pcs=0;

			$colorsizetableid=$row[csf('color_size_table_id')];
			if($colorsizetableid=="") $colorsizetableid=0;
			
			if($cbo_colorsizesensitive==1 || $cbo_colorsizesensitive==3 ){
				$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]];
				$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
				$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
			}
			else if($cbo_colorsizesensitive==2){
				$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('size_number_id')]];
				$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
				$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
			}
			else if($cbo_colorsizesensitive==4){
				$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]];
				$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
				$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
			}
			else if($cbo_colorsizesensitive==0){
				$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id];
				$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
				$req_qnty_ordUom = def_number_format((($data[14]/$data[8])*$req_qnty_ord_uom),5,"");
				//$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");
				$txtwoq_cal =def_number_format($req_qnty_ordUom,5,"");
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
			}
			if($txtwoq_cal>0){
				if($cons_breck_down=="")
				{
					$cons_breck_down.=$color_number_id.'_'.$size_number_id.'_'.$description.'_'.$brand_supplier.'_'.$item_color.'_'.$item_size.'_'.$txtwoq_cal.'_'.$excess.'_'.$txtwoq_cal.'_'.$txt_avg_price.'_'.$amount.'_'.$pcs.'_'.$colorsizetableid."_".$txtwoq_cal;
				}
				else
				{
					$cons_breck_down.="__".$color_number_id.'_'.$size_number_id.'_'.$description.'_'.$brand_supplier.'_'.$item_color.'_'.$item_size.'_'.$txtwoq_cal.'_'.$excess.'_'.$txtwoq_cal.'_'.$txt_avg_price.'_'.$amount.'_'.$pcs.'_'.$colorsizetableid."_".$txtwoq_cal;
				}
			}
		}
		echo $cons_breck_down;
	}
	$level_arr=array();
	$gmt_color_edb=""; $item_color_edb=""; $gmt_size_edb=""; $item_size_edb="";
	if($cbo_colorsizesensitive==1){
		$sql="select min(b.id) as id,  min(c.id) as color_size_table_id, c.color_number_id, min(c.color_order) as color_order, sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.id in($txt_po_id) $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.color_number_id order by  color_order";
		$level_arr=$po_color_level_data_arr;
		$gmt_size_edb="disabled";
		$item_size_edb="disabled";
	}
	else if($cbo_colorsizesensitive==2){
		$sql="select min(b.id) as id, min(c.id) as color_size_table_id, c.size_number_id, min(c.size_order) as size_order, min(e.item_size) as item_size, sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.size_number_id order by size_order";
		$level_arr=$po_size_level_data_arr;
		$gmt_color_edb="disabled";
		$item_color_edb="disabled";
	}
	else if($cbo_colorsizesensitive==3){
		$sql="select min(b.id) as id, min(c.id) as color_size_table_id, c.color_number_id, min(c.color_order) as color_order, sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  and  b.id in($txt_po_id) $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.color_number_id order by color_order";
		$level_arr=$po_color_level_data_arr;
		$gmt_size_edb="disabled";
		$item_size_edb="disabled";
	}
	else if($cbo_colorsizesensitive==4){
		 $sql="select min(b.id) as id, min(c.id) as color_size_table_id, c.color_number_id, c.size_number_id, min(c.color_order) as color_order, min(c.size_order) as size_order, min(e.item_size) as item_size, min(e.item_size) as item_size, sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.id in($txt_po_id) $txt_country_cond and d.id=$cbo_trim_precost_id group by c.color_number_id, c.size_number_id order by color_order, size_order";
		$level_arr=$po_color_size_level_data_arr;
	}
	else{
		 $sql="select b.job_no_mst, min(b.id) as id, min(c.id) as color_size_table_id, sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.id in($txt_po_id) $txt_country_cond and d.id=$cbo_trim_precost_id group by b.job_no_mst";
		 $level_arr=$po_no_sen_level_data_arr;
	}
	$data_array=sql_select($sql);

	$cons_breck_down="";
	if(count($data_array)>0 && $cbo_level==2)
	{
		$i=0;
		foreach( $data_array as $row )
		{
			if($cbo_colorsizesensitive==1){
				$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['req_qty']),5,"");
				$po_qty=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['po_qty']);
				$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['order_quantity_set']);
				$booking_qty=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_qty']),5,"");
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
			}
			if($cbo_colorsizesensitive==2){
				$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]]['req_qty']),5,"");
				$po_qty=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]]['po_qty']);
				$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]]['order_quantity_set']);
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
			}
			if($cbo_colorsizesensitive==3){
				$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['req_qty']),5,"");
				$po_qty=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['po_qty']);
				$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['order_quantity_set']);
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
			}
			if($cbo_colorsizesensitive==4){
				$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['req_qty']),5,"");
				$po_qty=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['po_qty']);
				$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['order_quantity_set']);
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
			}
			if($cbo_colorsizesensitive==0){
				$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id]['req_qty']),5,"");
				$po_qty=array_sum($level_arr[$cbo_trim_precost_id]['po_qty']);
				$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id]['order_quantity_set']);
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
			}
			$color_number_id=$row[csf('color_number_id')];
			if($color_number_id=="") $color_number_id=0;

			$size_number_id=$row[csf('size_number_id')];
			if($size_number_id=="") $size_number_id=0;

			$description=$txt_pre_des;
			if($description=="") $description=0;

			$brand_supplier=$txt_pre_brand_sup;
			if($brand_supplier=="") $brand_supplier=0;

			$item_color=$color_library[$row[csf('color_number_id')]];
			if($item_color=="") $item_color=0;

			$item_size=$row[csf('item_size')];
			if($item_size=="") $item_size=0;
			
			$excess=0;
			$pcs=$row[csf('order_quantity_set')];
			if($pcs=="") $pcs=0;

			$colorsizetableid=$row[csf('color_size_table_id')];
			if($colorsizetableid=="") $colorsizetableid=0;

			if($txtwoq_cal>0){
				if($cons_breck_down==""){
					$cons_breck_down.=$color_number_id.'_'.$size_number_id.'_'.$description.'_'.$brand_supplier.'_'.$item_color.'_'.$item_size.'_'.$txtwoq_cal.'_'.$excess.'_'.$txtwoq_cal.'_'.$txt_avg_price.'_'.$amount.'_'.$pcs.'_'.$colorsizetableid."_".$txtwoq_cal;
				}
				else{
					$cons_breck_down.="__".$color_number_id.'_'.$size_number_id.'_'.$description.'_'.$brand_supplier.'_'.$item_color.'_'.$item_size.'_'.$txtwoq_cal.'_'.$excess.'_'.$txtwoq_cal.'_'.$txt_avg_price.'_'.$amount.'_'.$pcs.'_'.$colorsizetableid."_".$txtwoq_cal;
				}
			}
		}
		echo $cons_breck_down."**".json_encode($level_arr)."**".$txtwoq_cal;
	}
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$sql_variable="select short_booking_available,variable_list from variable_order_tracking where variable_list=100 and company_name=$cbo_company_name and status_active=1 and is_deleted=0";
    //echo $sql_variable;	die;
	$nameArray=sql_select($sql_variable);
	//$app_cause_arr=array();
	foreach($nameArray as $row)
	{
		$variable_short_booking=$row[csf('short_booking_available')];
	}
	
    if($variable_ready_to_approve != 1) $variable_ready_to_approve=0;
	if ($operation==0){
		$con = connect();
		
		$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'TB', date("Y",time()), 5, "select booking_no_prefix, booking_no_prefix_num from wo_booking_mst where company_id=$cbo_company_name and booking_type=2 and to_char(insert_date,'YYYY')=".date('Y',time())." order by id desc", "booking_no_prefix", "booking_no_prefix_num" ));
		
		$id=return_next_id( "id", "wo_booking_mst", 1);
		$field_array="id, booking_type, is_short, item_category, entry_form, item_from_precost, booking_no_prefix, booking_no_prefix_num, booking_no, company_id, buyer_id, booking_date, delivery_date, booking_basis, requisition_id, requisition_no, source, currency_id, pay_mode, supplier_id, tenor, division_id, responsible_dept, responsible_person, ready_to_approved, cbo_level, reason, attention, delivery_address, remarks, short_booking_available, inserted_by, insert_date, status_active, is_deleted";
		
		$data_array ="(".$id.",2,1,4,273,1,'".$new_booking_no[1]."',".$new_booking_no[2].",'".$new_booking_no[0]."',".$cbo_company_name.",".$cbo_buyer_name.",".$txt_booking_date.",".$txt_delivery_date.",".$cbo_basis_id.",".$txt_reqsn_id.",".$txt_reqsn_no.",".$cbo_source.",".$cbo_currency.",".$cbo_pay_mode.",".$cbo_supplier_name.",".$txt_tenor.",".$cbo_division_id.",".$cbo_responsible_dept.",".$txt_responsible_person.",".$cbo_ready_to_approved.",".$cbo_level.",".$txt_reason.",".$txt_attention.",".$txtdelivery_address.",".$txt_remark.",'".$variable_short_booking."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";

		$rID=sql_insert("wo_booking_mst",$field_array,$data_array,0);
		//echo "10**".$rID;  echo "INSERT INTO wo_booking_mst (".$field_array.") VALUES ".$data_array; oci_rollback($con); disconnect($con); die;
		if($db_type==2 || $db_type==1 ){
			if($rID){
				oci_commit($con);
				echo "0**".$new_booking_no[0]."**".$id;
			}
			else{
				oci_rollback($con);
				echo "10**".$new_booking_no[0]."**".$id;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1){
		$con = connect();
		
		$booking_mst_id=str_replace("'","",$booking_mst_id);
		$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_booking_no");
		if($is_approved==3){
			$is_approved=1;
		}
		if($is_approved==1){
			echo "app1**".str_replace("'","",$txt_booking_no);
			 disconnect($con);die;
		}

		$sql_variable="select short_booking_available,variable_list from variable_order_tracking where variable_list=100 and company_name=$cbo_company_name and status_active=1 and is_deleted=0";
		//echo $sql_variable;	die;
		$nameArray=sql_select($sql_variable);
		//$app_cause_arr=array();
		foreach($nameArray as $row)
		{
			$variable_short_booking=$row[csf('short_booking_available')];
		}
		$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no  and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
		if($pi_number){
			echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
			 disconnect($con);die;
		}
		$recv_number=return_field_value( "recv_number", "inv_receive_master a,inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no  and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
		if($recv_number){
			echo "recv1**".str_replace("'","",$txt_booking_no)."**".$recv_number;
			 disconnect($con);die;
		}
		
		$field_array_up="booking_date*delivery_date*booking_basis*requisition_id*requisition_no*source*currency_id*pay_mode*supplier_id*tenor*division_id*responsible_dept*responsible_person*ready_to_approved*reason*attention*delivery_address*remarks*short_booking_available*updated_by*update_date*revised_no";

		$data_array_up ="".$txt_booking_date."*".$txt_delivery_date."*".$cbo_basis_id."*".$txt_reqsn_id."*".$txt_reqsn_no."*".$cbo_source."*".$cbo_currency."*".$cbo_pay_mode."*".$cbo_supplier_name."*".$txt_tenor."*".$cbo_division_id."*".$cbo_responsible_dept."*".$txt_responsible_person."*".$cbo_ready_to_approved."*".$txt_reason."*".$txt_attention."*".$txtdelivery_address."*".$txt_remark."*'".$variable_short_booking."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*revised_no+1";

		$rID=sql_update("wo_booking_mst",$field_array_up,$data_array_up,"booking_no","".$txt_booking_no."",0);
		//echo "10**".$rID; oci_rollback($con); disconnect($con); die;
		if($db_type==2 || $db_type==1 ){
			if($rID){
				oci_commit($con);
				echo "1**".str_replace("'","",$txt_booking_no)."**".$booking_mst_id;
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_booking_no)."**".$booking_mst_id;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2){
		$con = connect();
		
		$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_booking_no");
		if($is_approved==3){
			$is_approved=1;
		}
		if($is_approved==1){
			echo "app1**".str_replace("'","",$txt_booking_no);
			 disconnect($con);die;
		}
		
		$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no  and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
		if($pi_number){
			echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
			 disconnect($con);die;
		}
	
		$recv_number=return_field_value( "recv_number", "inv_receive_master a,inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no  and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
		if($recv_number){
			echo "recv1**".str_replace("'","",$txt_booking_no)."**".$recv_number;
			 disconnect($con);die;
		}
		
		$is_received_id=return_field_value( "subcon_job", "subcon_ord_mst","order_no=$txt_booking_no and order_id is not null and entry_form=255 and status_active=1 and is_deleted=0");
		//echo "10** select id from subcon_ord_mst where order_no=$txt_booking_no and order_id is not null and entry_form=255".$is_received_id; die;
		$rID_rec=1;
		if(!empty($is_received_id))
		{
			echo "orderFound**$is_received_id";
			oci_rollback($con);
			disconnect($con); die;
		}

		$delete_cause=str_replace("'","",$delete_cause);
		$delete_cause=str_replace('"','',$delete_cause);
		$delete_cause=str_replace('(','',$delete_cause);
		$delete_cause=str_replace(')','',$delete_cause);

		//$rID=execute_query( "delete from wo_booking_mst where  booking_no =".$txt_booking_no."",0);
		//$rID1=execute_query( "delete from wo_booking_dtls where  booking_no =".$txt_booking_no."",0);
		//$rID2=execute_query( "delete from wo_trim_book_con_dtls where  booking_no =".$txt_booking_no."",0);
		$rID=execute_query( "update wo_booking_mst set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where  booking_no=$txt_booking_no",0);
		$rID1=execute_query( "update wo_booking_dtls set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where  booking_no=$txt_booking_no",0);
		$rID1=execute_query( "update wo_trim_book_con_dtls set status_active=0,is_deleted=1 where  booking_no=$txt_booking_no",0);
		if($db_type==2 || $db_type==1 ){
			if($rID){
				oci_commit($con);
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="save_update_delete_dtls")
{
	$color_library=return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$booking_mst_id=str_replace("'","",$booking_mst_id);
	$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_booking_no");
	if($is_approved==3){
		$is_approved=1;
	}
	if($is_approved==1){
		echo "app1**".str_replace("'","",$txt_booking_no);
		 disconnect($con);die;
	}

	if ($operation==0){
		$con = connect();
		if($db_type==0){
			mysql_query("BEGIN");
		}
		if( check_table_status( $_SESSION['menu_id'], 1 )==0 ){
			echo "15**0";
			 disconnect($con);die;
		}
		$id_dtls=return_next_id( "id", "wo_booking_dtls", 1 ) ;
		$field_array1="id, pre_cost_fabric_cost_dtls_id, po_break_down_id, job_no, booking_no, booking_mst_id, booking_type, is_short, trim_group, uom, sensitivity, wo_qnty, exchange_rate, rate, amount, delivery_date,description, country_id_string, inserted_by, insert_date";

		$field_array2="id,wo_trim_booking_dtls_id,booking_no,booking_mst_id,job_no,po_break_down_id,color_number_id,gmts_sizes,description,brand_supplier,item_color,item_size,cons, process_loss_percent, requirment, rate, amount, pcs, color_size_table_id,article_number";

		$add_comma=0;
		$id1=return_next_id( "id", "wo_trim_book_con_dtls", 1 );
		$new_array_color=array();
		for ($i=1;$i<=$total_row;$i++){
			$txttrimcostid="txttrimcostid_".$i;
			$txtpoid="txtpoid_".$i;
			$txttrimgroup="txttrimgroup_".$i;
			$txtuom="txtuom_".$i;
			$cbocolorsizesensitive="cbocolorsizesensitive_".$i;
			$txtwoq="txtwoq_".$i;
			$txtexchrate="txtexchrate_".$i;
			$txtrate="txtrate_".$i;
			$txtamount="txtamount_".$i;
			$txtddate="txtddate_".$i;
			$consbreckdown="consbreckdown_".$i;
			$txtbookingid="txtbookingid_".$i;
			$txtcountry="txtcountry_".$i;
			$txtjob_id="txtjob_".$i;
			$txtdescid="txtdescid_".$i;

			$data_array1 ="(".$id_dtls.",".$$txttrimcostid.",".$$txtpoid.",".$$txtjob_id.",".$txt_booking_no.",".$booking_mst_id.",2,1,".$$txttrimgroup.",".$$txtuom.",".$$cbocolorsizesensitive.",".$$txtwoq.",".$$txtexchrate.",".$$txtrate.",".$$txtamount.",".$$txtddate.",".$$txtdescid.",".$$txtcountry.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			//	CONS break down===============================================================================================
			if(str_replace("'",'',$$consbreckdown) !=''){
				$data_array2="";
				$rID_de1=execute_query( "delete from wo_trim_book_con_dtls where  wo_trim_booking_dtls_id =".$$txtbookingid."",0);
				$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
				for($c=0;$c < count($consbreckdown_array);$c++){
					$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
					if(str_replace("'","",$consbreckdownarr[4])!="")
					{
						if (!in_array(str_replace("'","",$consbreckdownarr[4]),$new_array_color)){
							$color_id = return_id( str_replace("'","",$consbreckdownarr[4]), $color_library, "lib_color", "id,color_name","273");
							$new_array_color[$color_id]=str_replace("'","",$consbreckdownarr[4]);
						}
						else $color_id =  array_search(str_replace("'","",$consbreckdownarr[4]), $new_array_color);
					}
					else $color_id =0;
					if ($c!=0) $data_array2 .=",";
					$data_array2 .="(".$id1.",".$id_dtls.",".$txt_booking_no.",".$booking_mst_id.",".$$txtjob_id.",".$$txtpoid.",'".$consbreckdownarr[0]."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$color_id."','".$consbreckdownarr[5]."','".$consbreckdownarr[6]."','".$consbreckdownarr[7]."','".$consbreckdownarr[8]."','".$consbreckdownarr[9]."','".$consbreckdownarr[10]."','".$consbreckdownarr[11]."','".$consbreckdownarr[12]."','".$consbreckdownarr[14]."')";
					$id1=$id1+1;
					$add_comma++;
				}
			}
			//CONS break down end===============================================================================================
			$rID1=sql_insert("wo_booking_dtls",$field_array1,$data_array1,0);
			$rID2=1;
			if($data_array2 !=""){
				$rID2=sql_insert("wo_trim_book_con_dtls",$field_array2,$data_array2,1);
			}
			$id_dtls=$id_dtls+1;
		}
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0){
			if($rID1 && $rID2){
				mysql_query("COMMIT");
				echo "0**".$new_booking_no[0];
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_booking_no[0];
			}
		}
		else if($db_type==2 || $db_type==1 ){
			if($rID1 && $rID2){
				oci_commit($con);
				echo "0**".$new_booking_no[0];
			}
			else{
				oci_rollback($con);
				echo "10**".$new_booking_no[0];
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)
	{
	$con = connect();
	$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_booking_no");
	if($is_approved==3){
		$is_approved=1;
	}
	if($is_approved==1){
		echo "app1**".str_replace("'","",$txt_booking_no);
		 disconnect($con);die;
	}


	if($db_type==0){
	mysql_query("BEGIN");
	}
	if( check_table_status( $_SESSION['menu_id'], 1 )==0 ){
		echo "15**1";
		 disconnect($con);die;
	}
	$field_array_up1="pre_cost_fabric_cost_dtls_id*po_break_down_id*job_no*booking_no*trim_group*uom*sensitivity*wo_qnty*exchange_rate*rate*amount*delivery_date*description*country_id_string*updated_by*update_date";
	$field_array_up2="id,wo_trim_booking_dtls_id,booking_no,booking_mst_id,job_no,po_break_down_id,color_number_id,gmts_sizes,description,brand_supplier,item_color,item_size,cons, process_loss_percent,requirment,rate,amount,pcs,color_size_table_id,article_number";

	$add_comma=0;
	$id1=return_next_id( "id", "wo_trim_book_con_dtls", 1 );
	$new_array_color=array();
	for ($i=1;$i<=$total_row;$i++){
		$txttrimcostid="txttrimcostid_".$i;
		$txtpoid="txtpoid_".$i;
		$txttrimgroup="txttrimgroup_".$i;
		$txtuom="txtuom_".$i;
		$cbocolorsizesensitive="cbocolorsizesensitive_".$i;
		$txtwoq="txtwoq_".$i;
		$txtexchrate="txtexchrate_".$i;
		$txtrate="txtrate_".$i;
		$txtamount="txtamount_".$i;
		$txtddate="txtddate_".$i;
		$consbreckdown="consbreckdown_".$i;
		$txtbookingid="txtbookingid_".$i;
		$txtcountry="txtcountry_".$i;
		$txtjob_id="txtjob_".$i;
		$txtdescid="txtdescid_".$i;
		$pi_number=array();
		$piquantity=0;
		$sqlPi=sql_select("select a.pi_number, b.quantity from  com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id  and b.work_order_no=$txt_booking_no and b.item_group=".$$txttrimgroup." and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
		foreach($sqlPi as $rowPi){
			$pi_number[$rowPi[csf('pi_number')]]=$rowPi[csf('pi_number')];
			$piquantity+=$rowPi[csf('quantity')];
		}

		if($piquantity && str_replace("'","",$$txtwoq) < $piquantity){
			echo "pi1**".str_replace("'","",$txt_booking_no)."**".implode(",",$pi_number)."**".$piquantity;
			check_table_status( $_SESSION['menu_id'],0);
			 disconnect($con);die;
		}

		$recv_number=array();
		$recvquantity=0;
		$sqlRecv=sql_select("select a.recv_number, b.receive_qnty from  inv_receive_master a,inv_trims_entry_dtls b where a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and b.item_group_id=".$$txttrimgroup." and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
		foreach($sqlRecv as $rowRecv){
			$recv_number[$rowRecv[csf('recv_number')]]=$rowRecv[csf('recv_number')];
			$recvquantity+=$rowRecv[csf('receive_qnty')];
		}
		if($recvquantity && str_replace("'","",$$txtwoq) < $recvquantity){
			echo "recv1**".str_replace("'","",$txt_booking_no)."**".implode(",",$recv_number)."**".$recvquantity;
			check_table_status( $_SESSION['menu_id'],0);
		 disconnect($con);	die;
		}

		if(str_replace("'",'',$$txtbookingid)!=""){
			$id_arr=array();
			$data_array_up1=array();
			$id_arr[]=str_replace("'",'',$$txtbookingid);
			$data_array_up1[str_replace("'",'',$$txtbookingid)] =explode("*",("".$$txttrimcostid."*".$$txtpoid."*".$$txtjob_id."*".$txt_booking_no."*".$$txttrimgroup."*".$$txtuom."*".$$cbocolorsizesensitive."*".$$txtwoq."*".$$txtexchrate."*".$$txtrate."*".$$txtamount."*".$$txtddate."*".$$txtdescid."*".$$txtcountry."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

			//	CONS break down===============================================================================================
			if(str_replace("'",'',$$consbreckdown) !=''){
				$data_array_up2="";
				$rID_de1=execute_query( "delete from wo_trim_book_con_dtls where  wo_trim_booking_dtls_id =".$$txtbookingid."",0);
				$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
				for($c=0;$c < count($consbreckdown_array);$c++){
					$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
					if(str_replace("'","",$consbreckdownarr[4])!="")
					{
						if (!in_array(str_replace("'","",$consbreckdownarr[4]),$new_array_color)){
							$color_id = return_id( str_replace("'","",$consbreckdownarr[4]), $color_library, "lib_color", "id,color_name","273");
							$new_array_color[$color_id]=str_replace("'","",$consbreckdownarr[4]);
						}
						else $color_id =  array_search(str_replace("'","",$consbreckdownarr[4]), $new_array_color);
					}
					else $color_id =0;
					if ($c!=0) $data_array_up2 .=",";
					$data_array_up2 .="(".$id1.",".$$txtbookingid.",".$txt_booking_no.",".$booking_mst_id.",".$$txtjob_id.",".$$txtpoid.",'".$consbreckdownarr[0]."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$color_id."','".$consbreckdownarr[5]."','".$consbreckdownarr[6]."','".$consbreckdownarr[7]."','".$consbreckdownarr[8]."','".$consbreckdownarr[9]."','".$consbreckdownarr[10]."','".$consbreckdownarr[11]."','".$consbreckdownarr[12]."','".$consbreckdownarr[14]."')";
					$id1=$id1+1;
					$add_comma++;
				}
			}
			//CONS break down end===============================================================================================
			if($data_array_up1 !="")
			{
			$rID1=execute_query(bulk_update_sql_statement( "wo_booking_dtls", "id", $field_array_up1, $data_array_up1, $id_arr ));
			}
		}
		$rID2=1;
		if($data_array_up2 !="")
		{
		$rID2=sql_insert("wo_trim_book_con_dtls",$field_array_up2,$data_array_up2,1);
		}
	}
		$rID=execute_query( "update wo_booking_mst set revised_no=revised_no+1 where  booking_no=$txt_booking_no",0);
	check_table_status( $_SESSION['menu_id'],0);
	if($db_type==0){
		if($rID1 &&  $rID2){
			mysql_query("COMMIT");
			echo "1**".str_replace("'","",$txt_booking_no);
		}
		else{
			mysql_query("ROLLBACK");
			echo "10**".str_replace("'","",$txt_booking_no);
		}
	}
	else if($db_type==2 || $db_type==1 ){
		if($rID1 &&  $rID2){
			oci_commit($con);
			echo "1**".str_replace("'","",$txt_booking_no);
		}
		else{
			oci_rollback($con);
			echo "10**".str_replace("'","",$txt_booking_no);
		}
	}
	disconnect($con);
	die;
	}

	else if ($operation==2){
		$con = connect();
		$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_booking_no");
		if($is_approved==3){
			$is_approved=1;
		}
		if($is_approved==1){
			echo "app1**".str_replace("'","",$txt_booking_no);
			 disconnect($con);die;
		}
		if($db_type==0){
			mysql_query("BEGIN");
		}
		for ($i=1;$i<=$total_row;$i++){
			$txttrimcostid="txttrimcostid_".$i;
			$txttrimgroup="txttrimgroup_".$i;
			$txtpoid="txtpoid_".$i;
			$txtbookingid="txtbookingid_".$i;
			//if(str_replace("'","",$cbo_pay_mode)==2){
			$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no and b.item_group=".$$txttrimgroup." and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			    if($pi_number){
				    echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
				     disconnect($con);die;
			    }
			//}else{
				$recv_number=return_field_value( "recv_number", "inv_receive_master a,inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and b.item_group_id=".$$txttrimgroup." and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			    if($recv_number){
				    echo "recv1**".str_replace("'","",$txt_booking_no)."**".$recv_number;
				   disconnect($con);  die;
			    }
			//}
			$bookingdtlsid=str_replace("'","",$$txtbookingid);
			$subcon_job=return_field_value( "subcon_job", "subcon_ord_mst a, subcon_ord_dtls b"," a.entry_form=255 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and b.booking_dtls_id in ($bookingdtlsid) and a.status_active=1 and b.status_active=1");
		    if(!empty($subcon_job)){
			     echo "orderFound**".$subcon_job."**SELECT subcon_job from subcon_ord_mst a, subcon_ord_dtls b where a.entry_form=255 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and b.booking_dtls_id in ($bookingdtlsid) and a.status_active=1 and b.status_active=1";
			     oci_rollback($con);
			     disconnect($con);die;
		    }
			$delete_cause=str_replace("'","",$delete_cause);
			$delete_cause=str_replace('"','',$delete_cause);
			$delete_cause=str_replace('(','',$delete_cause);
			$delete_cause=str_replace(')','',$delete_cause);

			//$rID1=execute_query( "delete from wo_booking_dtls where  id in (".str_replace("'","",$$txtbookingid).")",0);
			//$rID2=execute_query( "delete from wo_trim_book_con_dtls where  wo_trim_booking_dtls_id in(".str_replace("'","",$$txtbookingid).")",0);
			$rID1=execute_query( "update wo_booking_dtls set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."'   where  id in (".str_replace("'","",$$txtbookingid).") and booking_no=$txt_booking_no",0);
		    $rID2=execute_query( "update wo_trim_book_con_dtls set status_active=0,is_deleted=1 where  wo_trim_booking_dtls_id in(".str_replace("'","",$$txtbookingid).") and booking_no=$txt_booking_no",0);
		}
		if($db_type==0){
			if($rID1 &&  $rID2){
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}

		if($db_type==2 || $db_type==1 ){
			if($rID1 &&  $rID2){
				oci_commit($con);
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="save_update_delete_dtls_job_level")
{
	$color_library=return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$booking_mst_id=str_replace("'","",$booking_mst_id);
	$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_booking_no");
	if($is_approved==3){
		$is_approved=1;
	}
	if($is_approved==1){
		echo "app1**".str_replace("'","",$txt_booking_no);
		disconnect($con);die;
	}
	
	$strdata=json_decode(str_replace("'","",$strdata));
	//echo "10**"; print_r($strdata); die;
	if ($operation==0){
		$con = connect();
		$new_array_color=array(); $newColorArr=array(); $jobArr=array(); $poArr=array(); $pre_trim_id_arr=array(); $des_arr=array(); $brand_arr=array();$itemColorArr=array();
		for ($i=1; $i<=$total_row; $i++){
			$cbocolorsizesensitive="cbocolorsizesensitive_".$i;
			$consbreckdown="consbreckdown_".$i;
			$txtjob_id="txtjob_".$i;
			$txtpoid="txtpoid_".$i;
			$txttrimcostid="txttrimcostid_".$i;
			$txtdescid="txtdescid_".$i;
			
			$poid=str_replace("'","",$$txtpoid);
			$pretrimcostid=str_replace("'","",$$txttrimcostid);
			
			
			$jobArr[$$txtjob_id]=$$txtjob_id;
			$poArr[$poid]=$poid;
			$pre_trim_id_arr[$pretrimcostid]=$pretrimcostid;
			$des_arr[$$txtdesc]=$$txtdesc;
			//$brand_arr[$$txtbrandsup]=$$txtbrandsup;
			
			if(str_replace("'",'',$$consbreckdown) !=''){
				$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
				for($c=0; $c< count($consbreckdown_array); $c++){
					$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
					$consbreckdownarr[4]=trim(str_replace("'",'',$consbreckdownarr[4]));
					if(str_replace("'","",$consbreckdownarr[4])=='0') $consbreckdownarr[4]='';
					if(str_replace("'","",$consbreckdownarr[4])!="")
					{
						if (!in_array(str_replace("'","",$consbreckdownarr[4]),$new_array_color)){
							$color_id = return_id( str_replace("'","",$consbreckdownarr[4]), $color_library, "lib_color", "id,color_name","273");
							$newColorArr[str_replace("'","",$consbreckdownarr[4])]=$color_id;
						}
						else $newColorArr[str_replace("'","",$consbreckdownarr[4])]=$color_id;
					}
					else $newColorArr[str_replace("'","",$consbreckdownarr[4])]=0;
				}
			}
		}
		
		if(check_table_status( $_SESSION['menu_id'], 1 )==0 ){
			echo "15**0";
			disconnect($con);die;
		}
		
		if(str_replace("'","",implode(",",$des_arr))!="") $des_data_cond="and description in(".implode(",",$des_arr).")"; else $des_data_cond="  and description is null";
		//if(str_replace("'","",implode(",",$brand_arr))!="") $brand_data_cond="and brand_supplier in(".implode(",",$brand_arr).")"; else $brand_data_cond="  and brand_supplier is null";
		if (is_duplicate_field( "booking_no", "wo_booking_dtls", "job_no in(".implode(",",$jobArr).") and po_break_down_id in (".implode(",",$poArr).") and pre_cost_fabric_cost_dtls_id in(".implode(",",$pre_trim_id_arr).") and booking_type=2 and is_short=1 and booking_no=$txt_booking_no and status_active=1 and is_deleted=0 $des_data_cond $brand_data_cond") == 1)
		{
			check_table_status( $_SESSION['menu_id'],0);
			echo "11**0";
			 disconnect($con);die;
		}
		
		$id_dtls=return_next_id( "id", "wo_booking_dtls", 1);
		$field_array1="id, pre_cost_fabric_cost_dtls_id, po_break_down_id, job_no, booking_no, booking_mst_id, booking_type, is_short, entry_form_id, trim_group, uom, sensitivity, wo_qnty, exchange_rate, rate, amount, delivery_date, description, country_id_string, inserted_by, insert_date";
		
		$field_array2="id, wo_trim_booking_dtls_id, booking_no, booking_mst_id, job_no, po_break_down_id, color_number_id, gmts_sizes, description, brand_supplier, item_color, item_size, cons, process_loss_percent, requirment, rate, amount, pcs, color_size_table_id, article_number";
		$add_comma=0; $data_array2='';
		$id1=return_next_id("id", "wo_trim_book_con_dtls", 1);
		$rID1=$rID_de1=$rID2=$flag=1;
		for ($i=1; $i<=$total_row; $i++){
			$txttrimcostid="txttrimcostid_".$i;
			$txtpoid="txtpoid_".$i;
			$txttrimgroup="txttrimgroup_".$i;
			$txtuom="txtuom_".$i;
			$cbocolorsizesensitive="cbocolorsizesensitive_".$i;
			$txtwoq="txtwoq_".$i;
			$txtexchrate="txtexchrate_".$i;
			$txtrate="txtrate_".$i;
			$txtamount="txtamount_".$i;
			$txtddate="txtddate_".$i;
			$consbreckdown="consbreckdown_".$i;
			$txtbookingid="txtbookingid_".$i;
			$txtcountry="txtcountry_".$i;
			$txtjob_id="txtjob_".$i;
			$txtdescid="txtdescid_".$i;
			$txtreqqnty="txtreqqnty_".$i;
			$jsondata="jsondata_".$i;
			
			$jsonarr=json_decode(str_replace("'","",$$jsondata));
			
			if(str_replace("'","",$$txtddate)!='') $txtdlvdate=date("d-M-Y",strtotime(str_replace("'","",$$txtddate))); else $txtdlvdate="";

			$job=str_replace("'","",$$txtjob_id);
			$trimcostid=str_replace("'","",$$txttrimcostid);
			$reqqnty=str_replace("'","",$$txtreqqnty);
			$woq=str_replace("'","",$$txtwoq);
			$rate=str_replace("'","",$$txtrate);

			foreach($strdata->$job->$trimcostid->po_id as $poId){
				$wqQty=($strdata->$job->$trimcostid->req_qnty->$poId/$reqqnty)*$woq;
				
				$amount=$wqQty*$rate;
				$data_array1 ="(".$id_dtls.",".$$txttrimcostid.",".$poId.",".$$txtjob_id.",".$txt_booking_no.",".$booking_mst_id.",2,1,273,".$$txttrimgroup.",".$$txtuom.",".$$cbocolorsizesensitive.",".$wqQty.",".$$txtexchrate.",".$$txtrate.",".$amount.",'".$txtdlvdate."',".$$txtdescid.",".$$txtcountry.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				$rID1=sql_insert("wo_booking_dtls",$field_array1,$data_array1,0);
				if($rID1==1 && $flag==1) $flag=1; else $flag=0;

				//	CONS break down===============================================================================================
				if(str_replace("'",'',$$consbreckdown)!=''){
					$rID_de1=execute_query( "delete from wo_trim_book_con_dtls where  wo_trim_booking_dtls_id =".$$txtbookingid."",0);
					if($rID_de1==1 && $flag==1) $flag=1; else $flag=0;
					$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
					$d=0;
					for($c=0; $c < count($consbreckdown_array); $c++){
						$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
						
						$color_id=$newColorArr[str_replace("'","",$consbreckdownarr[4])];

						$gmc=$consbreckdownarr[0];
						$gms=$consbreckdownarr[1];
						//if($gms==0) $gms="";
						$art=$consbreckdownarr[14];
						
						if(str_replace("'","",$$cbocolorsizesensitive)==1 || str_replace("'","",$$cbocolorsizesensitive)==3){
							$bQty=($jsonarr->$trimcostid->$gmc->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[6];
							$bwqQty=($jsonarr->$trimcostid->$gmc->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[8];
							$order_qty=$jsonarr->$trimcostid->$gmc->order_quantity->$poId;
						}
						if(str_replace("'","",$$cbocolorsizesensitive)==2){
							$bQty=($jsonarr->$trimcostid->$gms->$art->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[6];
							$bwqQty=($jsonarr->$trimcostid->$gms->$art->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[8];
							$order_qty=$jsonarr->$trimcostid->$gms->$art->order_quantity->$poId;
						}
						if(str_replace("'","",$$cbocolorsizesensitive)==4){
							$bQty=($jsonarr->$trimcostid->$gmc->$gms->$art->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[6];
							$bwqQty=($jsonarr->$trimcostid->$gmc->$gms->$art->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[8];
							$order_qty=$jsonarr->$trimcostid->$gmc->$gms->$art->order_quantity->$poId;
						}
						if(str_replace("'","",$$cbocolorsizesensitive)==0){
							$bQty=($jsonarr->$trimcostid->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[6];
							$bwqQty=($jsonarr->$trimcostid->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[8];
							$order_qty=$jsonarr->$trimcostid->order_quantity->$poId;
						}
						
						$bamount=$bwqQty*$consbreckdownarr[9];
						/*if ($d!=0){
							$data_array2 .=",";
						}*/
						
						$data_array2 ="(".$id1.",".$id_dtls.",".$txt_booking_no.",".$booking_mst_id.",".$$txtjob_id.",".$poId.",'".$consbreckdownarr[0]."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$color_id."','".$consbreckdownarr[5]."','".$bQty."','".$consbreckdownarr[7]."','".$bwqQty."','".$consbreckdownarr[9]."','".$bamount."','".$order_qty."','".$consbreckdownarr[12]."','".$consbreckdownarr[14]."')";
						$id1=$id1+1;
						$add_comma++;
						$d++;
						$rID2=sql_insert("wo_trim_book_con_dtls",$field_array2,$data_array2,0);
						if($rID2==1 && $flag==1) $flag=1; else $flag=0;
					}
				}//CONS break down end==============================================================================================
				$id_dtls=$id_dtls+1;
			}
		}
		
		//echo "10**".$rID1.'='.$rID_de1.'='.$rID2.'='.$flag; oci_rollback($con); check_table_status( $_SESSION['menu_id'],0); disconnect($con); die;
		
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==2 || $db_type==1 )
		{
			if($flag==1){
				oci_commit($con);
				echo "0**".str_replace("'","",$txt_booking_no);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)
	{
		$con = connect();
		$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_booking_no");
		if($is_approved==3){
			$is_approved=1;
		}
		if($is_approved==1){
			echo "app1**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;
		}
		
		if(check_table_status( $_SESSION['menu_id'], 1 )==0 ){
			echo "15**1";
			disconnect($con);die;
		}
		
		$new_array_color=array(); $newColorArr=array(); $jobArr=array(); $poArr=array(); $pre_trim_id_arr=array(); $des_arr=array(); $brand_arr=array(); $booking_dtls_id_arr=array();
		for ($i=1; $i<=$total_row; $i++){
			$cbocolorsizesensitive="cbocolorsizesensitive_".$i;
			$consbreckdown="consbreckdown_".$i;
			
			$txtjob_id="txtjob_".$i;
			$txtpoid="txtpoid_".$i;
			$txttrimcostid="txttrimcostid_".$i;
			$txtdescid="txtdescid_".$i;
			$txtbookingid="txtbookingid_".$i;
			
			$poid=str_replace("'","",$$txtpoid);
			$pretrimcostid=str_replace("'","",$$txttrimcostid);
			$bookingdtlsid=str_replace("'","",$$txtbookingid);
			
			$jobArr[$$txtjob_id]=$$txtjob_id;
			$poArr[$poid]=$poid;
			$pre_trim_id_arr[$pretrimcostid]=$pretrimcostid;
			$des_arr[$$txtdesc]=$$txtdesc;
			//$brand_arr[$$txtbrandsup]=$$txtbrandsup;
			$booking_dtls_id_arr[$bookingdtlsid]=$bookingdtlsid;
			
			if(str_replace("'",'',$$consbreckdown) !=''){
				$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
				for($c=0; $c< count($consbreckdown_array); $c++){
					$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
					$consbreckdownarr[4]=trim(str_replace("'",'',$consbreckdownarr[4]));
					if(str_replace("'","",$consbreckdownarr[4])=='0') $consbreckdownarr[4]='';
					if(str_replace("'","",$consbreckdownarr[4])!="")
					{
						if (!in_array(str_replace("'","",$consbreckdownarr[4]),$new_array_color)){
							$color_id = return_id( str_replace("'","",$consbreckdownarr[4]), $color_library, "lib_color", "id,color_name","273");
							$newColorArr[str_replace("'","",$consbreckdownarr[4])]=$color_id;
						}
						else $newColorArr[str_replace("'","",$consbreckdownarr[4])]=$color_id;
					}
					else $newColorArr[str_replace("'","",$consbreckdownarr[4])]=0;
				}
			}
		}
		
		if(str_replace("'","",implode(",",$des_arr))!="") $des_data_cond="and description in(".implode(",",$des_arr).")"; else $des_data_cond="  and description is null";
		//if(str_replace("'","",implode(",",$brand_arr))!="") $brand_data_cond="and brand_supplier in(".implode(",",$brand_arr).")"; else $brand_data_cond="  and brand_supplier is null";
		if (is_duplicate_field( "booking_no", "wo_booking_dtls", "job_no in(".implode(",",$jobArr).") and po_break_down_id in (".implode(",",$poArr).") and pre_cost_fabric_cost_dtls_id in(".implode(",",$pre_trim_id_arr).") and id not in(".implode(",",$booking_dtls_id_arr).") and booking_type=2 and is_short=1 and booking_no=$txt_booking_no and status_active=1 and is_deleted=0 $des_data_cond $brand_data_cond") == 1)
		{
			check_table_status( $_SESSION['menu_id'],0);
			echo "11**0";
			disconnect($con);die;
		}
		
		$field_array_up1="pre_cost_fabric_cost_dtls_id*po_break_down_id*job_no*booking_no*trim_group*uom*sensitivity*wo_qnty*exchange_rate*rate*amount*delivery_date*description*country_id_string*updated_by*update_date";
		$field_array_up2="id, wo_trim_booking_dtls_id, booking_no, booking_mst_id, job_no, po_break_down_id, color_number_id, gmts_sizes, description, brand_supplier, item_color, item_size, cons, process_loss_percent, requirment, rate, amount, pcs, color_size_table_id, article_number";
		$add_comma=0;
		$id1=return_next_id( "id", "wo_trim_book_con_dtls", 1 );
		$new_array_color=array();
		for ($i=1;$i<=$total_row;$i++){
			$txttrimcostid="txttrimcostid_".$i;
			$txtpoid="txtpoid_".$i;
			$txttrimgroup="txttrimgroup_".$i;
			$txtuom="txtuom_".$i;
			$cbocolorsizesensitive="cbocolorsizesensitive_".$i;
			$txtwoq="txtwoq_".$i;
			$txtexchrate="txtexchrate_".$i;
			$txtrate="txtrate_".$i;
			$txtamount="txtamount_".$i;
			$txtddate="txtddate_".$i;
			$consbreckdown="consbreckdown_".$i;
			$txtbookingid="txtbookingid_".$i;
			$txtcountry="txtcountry_".$i;
			$txtjob_id="txtjob_".$i;
			$txtdescid="txtdescid_".$i;
			$txtreqqnty="txtreqqnty_".$i;
			$jsondata="jsondata_".$i;
			$jsonarr=json_decode(str_replace("'","",$$jsondata));
			
			if(str_replace("'","",$$txtddate)!='') $txtdlvdate=date("d-M-Y",strtotime(str_replace("'","",$$txtddate))); else $txtdlvdate="";
			/*$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no and b.item_group=".$$txttrimgroup." and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($pi_number){
				echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
				die;
			}
			$recv_number=return_field_value( "recv_number", "inv_receive_master a,inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and b.item_group_id=".$$txttrimgroup." and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($recv_number){
				echo "recv1**".str_replace("'","",$txt_booking_no)."**".$recv_number;
				die;
			}*/
			$pi_number=array();
			$piquantity=0;
			$sqlPi=sql_select("select a.pi_number, b.quantity from  com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id  and b.work_order_no=$txt_booking_no and b.item_group=".$$txttrimgroup." and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			foreach($sqlPi as $rowPi){
				$pi_number[$rowPi[csf('pi_number')]]=$rowPi[csf('pi_number')];
				$piquantity+=$rowPi[csf('quantity')];
			}

			if($piquantity && str_replace("'","",$$txtwoq) < $piquantity){
				echo "pi1**".str_replace("'","",$txt_booking_no)."**".implode(",",$pi_number)."**".$piquantity;
				check_table_status( $_SESSION['menu_id'],0);
			 disconnect($con);	die;
			}

			$recv_number=array();
			$recvquantity=0;
			$sqlRecv=sql_select("select a.recv_number, b.receive_qnty from  inv_receive_master a,inv_trims_entry_dtls b where a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and b.item_group_id=".$$txttrimgroup." and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			foreach($sqlRecv as $rowRecv){
				$recv_number[$rowRecv[csf('recv_number')]]=$rowRecv[csf('recv_number')];
				$recvquantity+=$rowRecv[csf('receive_qnty')];
			}
			if($recvquantity && str_replace("'","",$$txtwoq) < $recvquantity){
				echo "recv1**".str_replace("'","",$txt_booking_no)."**".implode(",",$recv_number)."**".$recvquantity;
				check_table_status( $_SESSION['menu_id'],0);
				 disconnect($con);die;
			}

			$job=str_replace("'","",$$txtjob_id);
			$trimcostid=str_replace("'","",$$txttrimcostid);
			$reqqnty=str_replace("'","",$$txtreqqnty);
			$woq=str_replace("'","",$$txtwoq);
			$rate=str_replace("'","",$$txtrate);

			if(str_replace("'",'',$$txtbookingid)!=""){
				foreach($strdata->$job->$trimcostid->po_id as $poId){
					$wqQty=($strdata->$job->$trimcostid->req_qnty->$poId/$reqqnty)*$woq;
					$amount=$wqQty*$rate;
					$id_arr=array();
					$data_array_up1=array();
					$id_arr[]=str_replace("'",'',$strdata->$job->$trimcostid->booking_id->$poId);
					$data_array_up1[str_replace("'",'',$strdata->$job->$trimcostid->booking_id->$poId)] =explode("*",("".$$txttrimcostid."*".$poId."*".$$txtjob_id."*".$txt_booking_no."*".$$txttrimgroup."*".$$txtuom."*".$$cbocolorsizesensitive."*".$wqQty."*".$$txtexchrate."*".$$txtrate."*".$amount."*'".$txtdlvdate."'*".$$txtdescid."*".$$txtcountry."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					if($data_array_up1 !=""){
						$rID1=execute_query(bulk_update_sql_statement( "wo_booking_dtls", "id", $field_array_up1, $data_array_up1, $id_arr ));
					}
					//	CONS break down===============================================================================================
					$rID2=1;
					if(str_replace("'",'',$$consbreckdown) !=''){
						$rID_de1=execute_query( "delete from wo_trim_book_con_dtls where  wo_trim_booking_dtls_id =".$strdata->$job->$trimcostid->booking_id->$poId."",0);
						$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
						$d=0;
						for($c=0;$c < count($consbreckdown_array);$c++){
							$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
							$color_id =$newColorArr[str_replace("'","",$consbreckdownarr[4])];
							$gmc=$consbreckdownarr[0];
							$gms=$consbreckdownarr[1];
							$art=$consbreckdownarr[14];
							if(str_replace("'","",$$cbocolorsizesensitive)==1 || str_replace("'","",$$cbocolorsizesensitive)==3){
								$bQty=($jsonarr->$trimcostid->$gmc->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[6];
								$bwqQty=($jsonarr->$trimcostid->$gmc->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[8];
								$order_qty=$jsonarr->$trimcostid->$gmc->order_quantity->$poId;
							}
							if(str_replace("'","",$$cbocolorsizesensitive)==2){
								$bQty=($jsonarr->$trimcostid->$gms->$art->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[6];
								$bwqQty=($jsonarr->$trimcostid->$gms->$art->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[8];
								$order_qty=$jsonarr->$trimcostid->$gms->$art->order_quantity->$poId;
							}
							if(str_replace("'","",$$cbocolorsizesensitive)==4){
								$bQty=($jsonarr->$trimcostid->$gmc->$gms->$art->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[6];
								$bwqQty=($jsonarr->$trimcostid->$gmc->$gms->$art->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[8];
								$order_qty=$jsonarr->$trimcostid->$gmc->$gms->$art->order_quantity->$poId;
							}
							if(str_replace("'","",$$cbocolorsizesensitive)==0){
								$bQty=($jsonarr->$trimcostid->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[6];
								$bwqQty=($jsonarr->$trimcostid->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[8];
								$order_qty=$jsonarr->$trimcostid->order_quantity->$poId;
							}

							$bamount=$bwqQty*$consbreckdownarr[9];
							if ($d!=0) $data_array2 .=",";
							$data_array2 ="(".$id1.",".$strdata->$job->$trimcostid->booking_id->$poId.",".$txt_booking_no.",".$booking_mst_id.",".$$txtjob_id.",".$poId.",'".$consbreckdownarr[0]."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$color_id."','".$consbreckdownarr[5]."','".$bQty."','".$consbreckdownarr[7]."','".$bwqQty."','".$consbreckdownarr[9]."','".$bamount."','".$order_qty."','".$consbreckdownarr[12]."','".$consbreckdownarr[14]."')";
							$id1=$id1+1;
							$add_comma++;
							$d++;
							$rID2=sql_insert("wo_trim_book_con_dtls",$field_array_up2,$data_array2,0);
						}
					}//CONS break down end==============================================================================================
				}
			}
		}
		$rID=execute_query( "update wo_booking_mst set revised_no=revised_no+1 where  booking_no=$txt_booking_no",0);
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==2 || $db_type==1 ){
			if($rID1 && $rID2){
				oci_commit($con);
				echo "1**".str_replace("'","",$txt_booking_no);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)
	{
		$con = connect();
		$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_booking_no");
		if($is_approved==3){
			$is_approved=1;
		}
		if($is_approved==1){
				echo "app1**".str_replace("'","",$txt_booking_no);
				 disconnect($con);die;
		}
		if($db_type==0){
			mysql_query("BEGIN");
		}
		for ($i=1;$i<=$total_row;$i++){
			$txttrimcostid="txttrimcostid_".$i;
			$txttrimgroup="txttrimgroup_".$i;
			$txtpoid="txtpoid_".$i;
			$txtbookingid="txtbookingid_".$i;

			//if(str_replace("'","",$cbo_pay_mode)==2){
			$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no and b.item_group=".$$txttrimgroup." and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			    if($pi_number){
				    echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
				     disconnect($con);die;
			    }
			//}else{
				$recv_number=return_field_value( "recv_number", "inv_receive_master a,inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and b.item_group_id=".$$txttrimgroup." and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			    if($recv_number){
				    echo "recv1**".str_replace("'","",$txt_booking_no)."**".$recv_number;
				    disconnect($con); die;
			    }
			//}
			$bookingdtlsid=str_replace("'","",$$txtbookingid);
			$subcon_job=return_field_value( "subcon_job", "subcon_ord_mst a, subcon_ord_dtls b"," a.entry_form=255 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and b.booking_dtls_id in ($bookingdtlsid) and a.status_active=1 and b.status_active=1");
		    if(!empty($subcon_job)){
			     echo "orderFound**".$subcon_job."**SELECT subcon_job from subcon_ord_mst a, subcon_ord_dtls b where a.entry_form=255 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and b.booking_dtls_id in ($bookingdtlsid) and a.status_active=1 and b.status_active=1";
			     oci_rollback($con);
			     disconnect($con);die;
		    }
		    
			$delete_cause=str_replace("'","",$delete_cause);
			$delete_cause=str_replace('"','',$delete_cause);
			$delete_cause=str_replace('(','',$delete_cause);
			$delete_cause=str_replace(')','',$delete_cause);
			//$rID1=execute_query( "delete from wo_booking_dtls where  id in (".str_replace("'","",$$txtbookingid).")",0);
			//$rID2=execute_query( "delete from wo_trim_book_con_dtls where  wo_trim_booking_dtls_id in(".str_replace("'","",$$txtbookingid).")",0);
			$rID1=execute_query( "update wo_booking_dtls set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."'   where  id in (".str_replace("'","",$$txtbookingid).") and booking_no=$txt_booking_no",0);
		    $rID2=execute_query( "update wo_trim_book_con_dtls set status_active=0,is_deleted=1 where  wo_trim_booking_dtls_id in(".str_replace("'","",$$txtbookingid).") and booking_no=$txt_booking_no",0);
		}

		if($db_type==0){
			if($rID1 &&  $rID2){
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		else if($db_type==2 || $db_type==1 ){
			if($rID1 &&  $rID2){
				oci_commit($con);
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="trims_booking_popup")
{
	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
	function set_checkvalue(){
			if(document.getElementById('chk_job_wo_po').value==0) document.getElementById('chk_job_wo_po').value=1;
			else document.getElementById('chk_job_wo_po').value=0;
	}
	function js_set_value( str_data ){
		document.getElementById('txt_booking').value=str_data;
		parent.emailwindow.hide();
	}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
            <input type="hidden" id="txt_booking" value="" />
            <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
                <table width="1000" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                    <thead>
                        <tr>
                            <th colspan="11">
                            <?
                            echo create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" );
                            ?>
                            </th>
                        </tr>
                        <tr>
                            <th width="130" class="must_entry_caption">Company Name</th>
                            <th width="130" class="must_entry_caption">Buyer Name</th>
                            <th width="100">Style Ref </th>
                            <th width="70">Job No </th>
                            <th width="80">Internal Ref.</th>
                            <th width="80">Order No</th>
                            <th width="130">Supplier Name</th>
                            <th width="70">Booking No</th>
                            <th width="120" colspan="2"> Booking Date Range</th>
                            <th><input type="checkbox" value="0" onClick="set_checkvalue();" id="chk_job_wo_po">WO Without PO</th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td><?=create_drop_down( "cbo_company_mst", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", '', "load_drop_down( 'short_trims_booking_multi_job_controllerurmi', this.value, 'load_drop_down_buyer', 'buyer_td' );"); ?></td>
                        <td id="buyer_td"><?=create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- Select Buyer --", $selected, "","" ); ?></td>
                        <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:88px"></td>
                        <td><input name="txt_job" id="txt_job" class="text_boxes" style="width:60px"></td>
                        <td><input name="internal_ref" id="internal_ref" class="text_boxes" style="width:70px"></td>
                        <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:70px"></td>
                        <td><?=create_drop_down( "cbo_supplier_name", 130, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=4 and a.status_active =1 and a.is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "",0 ); ?></td>
                        <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:60px"></td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px"></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px"></td>
                        <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_job').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('internal_ref').value, 'create_booking_search_list_view', 'search_div', 'short_trims_booking_multi_job_controllerurmi','setFilterGrid(\'list_view\',-1)')" style="width:90px;" />
                        </td>
                    </tr>
                    <tr>
                        <td align="center" colspan="11" valign="middle">
                        	<?=load_month_buttons(1); ?>
                        </td>
                    </tr>
                </table>
               <div id="search_div"></div>
            </form>
        </div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="create_booking_search_list_view")
{
	$data=explode('_',$data);
	//print_r($data);die;
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and c.buyer_name='$data[1]'"; else $buyer=set_user_lavel_filtering(' and c.buyer_name','buyer_id');
	if ($data[2]!=0) $supplier_id=" and a.supplier_id='$data[2]'"; else $supplier_id ="";
	
	if($data[3]!="" &&  $data[4]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	
	$booking_year_cond=" and to_char(a.insert_date,'YYYY')=$data[5]";
	if($data[7]==1){
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num ='$data[6]'"; else $booking_cond="";
		if (trim($data[8])!="") $style_cond=" and c.style_ref_no ='$data[8]'";
		if (str_replace("'","",$data[9])!="") $order_cond=" and d.po_number = '$data[9]' "; //else  $order_cond="";
		if (str_replace("'","",$data[10])!="") $job_cond=" and c.job_no_prefix_num = '$data[10]' "; //else  $order_cond="";
	}
	if($data[7]==2){
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '$data[6]%'"; else $booking_cond="";
		if (trim($data[8])!="") $style_cond=" and c.style_ref_no like '$data[8]%' "; //else  $style_cond="";
		if (str_replace("'","",$data[9])!="") $order_cond=" and d.po_number like '$data[9]%' "; //else  $order_cond="";
		if (str_replace("'","",$data[10])!="") $job_cond=" and c.job_no_prefix_num like '$data[10]%' "; //else  $order_cond="";
	}
	if($data[7]==3){
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[6]'"; else $booking_cond="";
		if (trim($data[8])!="") $style_cond=" and c.style_ref_no like '%$data[8]'"; //else  $style_cond="";
		if (str_replace("'","",$data[9])!="") $order_cond=" and d.po_number like '%$data[9]'"; //else  $order_cond="";
		if (str_replace("'","",$data[10])!="") $job_cond=" and c.job_no_prefix_num like '%$data[10]'"; //else  $order_cond="";
	}
	if($data[7]==4 || $data[7]==0){
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[6]%'"; else $booking_cond="";
		if (trim($data[8])!="") $style_cond=" and c.style_ref_no like '%$data[8]%'"; //else  $style_cond="";
		if (str_replace("'","",$data[9])!="") $order_cond=" and d.po_number like '%$data[9]%'"; //else  $order_cond="";
		if (str_replace("'","",$data[10])!="") $job_cond=" and c.job_no_prefix_num like '%$data[10]%'"; //else  $order_cond="";
	}
	
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$arr=array (1=>$comp,2=>$suplier);
	if($data[11]==0)
	{
		$internal_ref_con='';
		if(!empty($data[12]))
		{
			$internal_ref_con=" and d.grouping like '%$data[12]%'";
		}
		 $sql="select a.id, a.booking_no_prefix_num, a.booking_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date,c.style_ref_no,d.po_number,a.pay_mode from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c, wo_po_break_down d  where a.id=b.booking_mst_id and b.job_no=c.job_no and b.po_break_down_id=d.id and c.id=d.job_id and a.booking_type=2 and a.entry_form=273 and a.is_short=1
		and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 $company $buyer $supplier_id $booking_date $booking_cond $style_cond $order_cond $job_cond $internal_ref_con $booking_year_cond group by a.id, a.booking_no_prefix_num, a.booking_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date,c.style_ref_no,d.po_number,a.pay_mode  order by a.id DESC";
	}
	else
	{
		$internal_ref_con='';
		if(!empty($data[12]))
		{
			$internal_ref_con=" and c.grouping like '%$data[12]%'";
		}
		$sql="select a.id, a.job_no, a.booking_no_prefix_num, a.booking_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.pay_mode from wo_booking_mst a where a.id not in (select a.id from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c where a.id=b.booking_mst_id and b.po_break_down_id=c.id and a.booking_type=2 and a.is_short=1 and a.entry_form=273 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $internal_ref_con $company ".set_user_lavel_filtering(' and a.buyer_id','buyer_id')." $supplier_id $booking_date $booking_cond $job_cond $file_cond $ref_cond $booking_year_cond group by a.id) and a.booking_type=2 and a.is_short=1 and a.entry_form=273 and a.status_active=1 and a.is_deleted=0 $company ".set_user_lavel_filtering(' and a.buyer_id','buyer_id')." $supplier_id $booking_date $booking_cond $booking_year_cond group by a.id, a.booking_no_prefix_num, a.booking_no, a.job_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.pay_mode order by a.id DESC";
	}
	 //echo $sql; die;
	?>
    <div width="900">
    	<table width="900" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
            <thead>
                <tr>
                    <th width="30">SL</th>
                    <th width="120">Booking No</th>
                    <th width="100">Company</th>
                    <th width="100">Supplier</th>
                    <th width="100">Booking Date</th>
                    <th width="100">Delivery Date</th>
                    <?php
                    if($data[11]==0)
                    { ?>
                        <th width="150">Style Ref No</th>
                        <th width="">PO number</th>
                    <? } ?>
                </tr>
            </thead>
            <tbody id="list_view">
            <?
            $sl=1;
            $result=sql_select($sql);
            foreach($result as $row)
            {
				if ($sl%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row[csf("booking_no")]; ?>');" style="cursor:pointer">
                    <td width="30" align="center"><? echo $sl; ?></td>
                    <td width="120"><? echo $row[csf("booking_no_prefix_num")];?></td>
                    <td width="100" style="word-break:break-all"><? echo $comp[$row[csf("company_id")]];?></td>
                    <td width="100" style="word-break:break-all">
                    <? if($row[csf("pay_mode")]==3 || $row[csf("pay_mode")]==5) echo $comp[$row[csf("supplier_id")]]; else echo $suplier[$row[csf("supplier_id")]]; ?>
                    </td>
                    <td width="100"><? echo change_date_format($row[csf("booking_date")],"dd-mm-yyyy","-"); ?></td>
                    <td width="100"><? echo change_date_format($row[csf("delivery_date")],"dd-mm-yyyy","-"); ?></td>
                    <? if($data[11]==0)   {?>
                        <td width="150" style="word-break:break-all"><? echo $row[csf("style_ref_no")]; ?> </td>
                        <td width="" style="word-break:break-all"><? echo $row[csf("po_number")]; ?> </td>
                    <? } ?>
				</tr>
				<?
				$sl++;
            }
            ?>
            </tbody>
        </table>
    </div>
	<?
	exit();
}

if ($action=="populate_data_from_search_popup_booking"){
	$sql= "select id, booking_no, company_id, buyer_id, booking_date, delivery_date, booking_basis, requisition_id, requisition_no, source, currency_id, pay_mode, supplier_id, tenor, division_id, responsible_dept, responsible_person, ready_to_approved, cbo_level, reason, attention, delivery_address, remarks, is_approved from wo_booking_mst where booking_no='$data' and status_active=1 and is_deleted=0";
	$data_array=sql_select($sql);
	
	foreach ($data_array as $row){
		echo "get_php_form_data(".$row[csf("company_id")].", 'populate_variable_setting_data', 'requires/short_trims_booking_multi_job_controllerurmi' );\n";
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('txt_booking_no').value = '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('booking_mst_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_booking_date').value = '".change_date_format($row[csf("booking_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('txt_delivery_date').value = '".change_date_format($row[csf("delivery_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('cbo_basis_id').value = '".$row[csf("booking_basis")]."';\n";
		echo "document.getElementById('txt_reqsn_id').value = '".$row[csf("requisition_id")]."';\n";
		echo "document.getElementById('txt_reqsn_no').value = '".$row[csf("requisition_no")]."';\n";
		echo "document.getElementById('cbo_source').value = '".$row[csf("source")]."';\n";
		
		echo "document.getElementById('cbo_currency').value = '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('cbo_pay_mode').value = '".$row[csf("pay_mode")]."';\n";
		echo "load_drop_down('requires/short_trims_booking_multi_job_controllerurmi', '".$row[csf("pay_mode")]."', 'load_drop_down_supplier', 'supplier_td');\n";
		echo "document.getElementById('cbo_supplier_name').value = '".$row[csf("supplier_id")]."';\n";
		echo "document.getElementById('txt_tenor').value = '".$row[csf("tenor")]."';\n";
		
		echo "document.getElementById('cbo_division_id').value = '".$row[csf("division_id")]."';\n";
		echo "set_multiselect('cbo_responsible_dept','0','1','".$row[csf("responsible_dept")]."','0');\n";
		echo "document.getElementById('txt_responsible_person').value = '".$row[csf("responsible_person")]."';\n";
		echo "document.getElementById('cbo_ready_to_approved').value = '".$row[csf("ready_to_approved")]."';\n";
		echo "document.getElementById('cbo_level').value = '".$row[csf("cbo_level")]."';\n";
		echo "document.getElementById('txt_reason').value = '".$row[csf("reason")]."';\n";
		
		echo "document.getElementById('txt_attention').value = '".$row[csf("attention")]."';\n";
		echo "document.getElementById('txtdelivery_address').value = '".$row[csf("delivery_address")]."';\n";
		echo "document.getElementById('txt_remark').value = '".$row[csf("remarks")]."';\n";
		
		if($row[csf("is_approved")]==3){
			$is_approved=3;
		}else{
			$is_approved=$row[csf("is_approved")];
		}
		echo "document.getElementById('id_approved_id').value = '".$is_approved."';\n";
		
		echo " $('#cbo_company_name').attr('disabled',true);\n";
		echo " $('#cbo_supplier_name').attr('disabled',true);\n";
		echo " $('#cbo_level').attr('disabled',true);\n";
		echo " $('#cbo_buyer_name').attr('disabled',true);\n";
		echo "fnc_booking_basis('".$row[csf("booking_basis")]."');\n";
		echo "fnc_show_booking_list();\n";
		
		

		if($is_approved==1){
			//echo "document.getElementById('app_sms').innerHTML = 'This booking is approved';\n";
			echo "document.getElementById('app_sms2').innerHTML = 'This booking is approved';\n";
		}
		else if($is_approved==3){
			//echo "document.getElementById('app_sms').innerHTML = 'This booking is approved';\n";
			echo "document.getElementById('app_sms2').innerHTML = 'This booking is partial approved';\n";
		}
		else{
			//echo "document.getElementById('app_sms').innerHTML = '';\n";
			echo "document.getElementById('app_sms2').innerHTML = '';\n";
		}
	}
	exit();
}

//================================================report Start=====================================================
if($action=="show_trim_booking_report2"){
	extract($_REQUEST);
	
	$color_library=return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$company_library=return_library_array("select id, company_name from lib_company", "id", "company_name");
	$trim_group_library= return_library_array("select id, item_name from lib_item_group",'id','item_name');
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$order_uom_arr=return_library_array("select id,order_uom  from lib_item_group","id","order_uom");
	$deling_marcent_arr=return_library_array("select id,team_member_name from lib_mkt_team_member_info","id","team_member_name");
	$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
	$size_library=return_library_array("select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	$report_type=str_replace("'","",$report_type);
	//echo $report_type.'dfdfdfd';
	 $nameArray=sql_select( "select a.booking_no,a.is_approved, a.pay_mode,a.buyer_id, a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.source, a.remarks, a.revised_no,a.update_date from wo_booking_mst a where a.booking_no=$txt_booking_no and a.status_active =1 and a.is_deleted=0");
		foreach( $nameArray as $row)
		{
			$varcode_booking_no=$row[csf('booking_no')];
			$booking_date=$row[csf('booking_date')];
			$delivery_date=$row[csf('delivery_date')];
			$pay_mode_id=$row[csf('pay_mode')];
			$supplier_id=$row[csf('supplier_id')];
			$currency_id=$row[csf('currency_id')];
			$buyer_id=$row[csf('buyer_id')];
			$exchange_rate=$row[csf('exchange_rate')];
			$attention=$row[csf('attention')];
			$remarks=$row[csf('remarks')];
			$revised_no=$row[csf('revised_no')];
			$source_id=$row[csf('source')];
			$is_approved=$row[csf('is_approved')];
			$revised_update_date=explode(" ",$row[csf('update_date')]);
			$mst_revised_date_time=strtotime($revised_update_date[0]);
			$mst_revised_date_arr=$revised_update_date[0];
		}


		$buyer_string=array();
		$style_owner=array();
		$job_no=array();
		$style_ref=array();
		$all_dealing_marcent=array();
		$season=array();

		$nameArray_buyer=sql_select( "select  b.update_date,a.style_ref_no, a.job_no, a.style_owner, a.buyer_name, a.dealing_marchant,a.season,a.season_matrix   from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no=$txt_booking_no and b.status_active=1 and b.is_deleted=0");
        foreach ($nameArray_buyer as $result_buy){
			$buyer_string[$result_buy[csf('buyer_name')]]=$buyer_name_arr[$result_buy[csf('buyer_name')]];
			$style_owner[$result_buy[csf('job_no')]]=$company_library[$result_buy[csf('style_owner')]];
			$job_no[$result_buy[csf('job_no')]]=$result_buy[csf('job_no')];
			$style_ref[$result_buy[csf('job_no')]]=$result_buy[csf('style_ref_no')];
			$all_dealing_marcent[$result_buy[csf('job_no')]]=$deling_marcent_arr[$result_buy[csf('dealing_marchant')]];
			$season[$result_buy[csf('job_no')]]=$season_arr[$result_buy[csf('season_matrix')]];
			$revised_update_date=explode(" ",$result_buy[csf('update_date')]);
			$revised_date_time=strtotime($revised_update_date[0]);
			$revised_date_arr=$revised_update_date[0];
		}
		
		if($revised_date_time!="" && $mst_revised_date_time!="")
		{
			$max_revised_date_time=max($revised_date_time,$mst_revised_date_time);
			$max_revised_date=date('d-m-Y',$max_revised_date_time);
		}
		else if($revised_date_time!="" && $mst_revised_date_time=="")
		{
			$max_revised_date=date('d-m-Y',$revised_date_time);
		}
		else if($mst_revised_date_time!="" && $revised_date_time=="")
		{
			$max_revised_date=date('d-m-Y',$mst_revised_date_time);
		}
		
		
		$style_sting=implode(",",array_unique($style_ref));
		$job_no=implode(",",$job_no);

		$po_no=array();
		$file_no=array();
		$ref_no=array();
		$po_quantity=array();

		$nameArray_job=sql_select( "select b.job_no_mst, b.id, b.po_number, b.grouping, b.file_no, b.po_quantity  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0");
        foreach ($nameArray_job as $result_job){
			$po_no[$result_job[csf('job_no_mst')]][$result_job[csf('id')]]=$result_job[csf('po_number')];
			$file_no[$result_job[csf('id')]]=$result_job[csf('file_no')];
			$ref_no[$result_job[csf('id')]]=$result_job[csf('grouping')];
			$po_quantity[$result_job[csf('id')]]=$result_job[csf('po_quantity')];

			$ref_no_arr[$result_job[csf('job_no_mst')]]['ref'].=$result_job[csf('grouping')].',';
			$ref_no_arr[$result_job[csf('job_no_mst')]]['file'].=$result_job[csf('file_no')].',';
		}
		
	if($is_approved==1)
	{
	$msg="This booking is approved";	
	}
	else if($is_approved==3)
	{
	$msg="This booking is partial approved";	
	}
	else $msg="";

	?>
	<div style="width:1333px" align="center">

        <table width="100%" cellpadding="0" cellspacing="0" style="border:0px solid black">
            <tr>
            <td width="20px">
            <table width="100%" cellpadding="0" cellspacing="0" style="border:0px solid black">
            <tr>
            <td width="50" >
            <? if($report_type==1)
            {
            ?>
            <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='30%' width='50%'/>

            <?
            }
            else
            { ?>
            <img  src='../<? echo $imge_arr[$cbo_company_name]; ?>' height='30%' width='50%' />
            <? }
            ?>
            </td>
            <td width="40px" align="center">
            &nbsp;  &nbsp;  &nbsp;
            </td>
            <td width="30px"   align="center">

            <b style="font-size:25px;"> <?
            echo $company_library[$cbo_company_name];
            ?>
            </b>
            <br>
            <label>
            <?
            $nameArray=sql_select( "select id,plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
            foreach ($nameArray as $result){
            ?>
            <?  if($result[csf('plot_no')]!='') echo $result[csf('plot_no')]; else echo '';?> &nbsp;
            <? echo $result[csf('level_no')];?> &nbsp;
            <? echo $result[csf('road_no')]; ?>  &nbsp;
            <? echo $result[csf('block_no')];?>  &nbsp;
            <? echo $result[csf('city')];?>  &nbsp;
            <? echo $result[csf('zip_code')]; ?>  &nbsp;
            <?php echo $result[csf('province')]; ?>  &nbsp;
            <? echo $country_arr[$result[csf('country_id')]]; ?> &nbsp;<br/>
            <? echo $result[csf('email')];?>  &nbsp;
            <? echo $result[csf('website')];
            if($result[csf('plot_no')]!='')
            {
            $plot_no=$result[csf('plot_no')];
            }
            if($result[csf('level_no')]!='')
            {
            $level_no=$result[csf('level_no')];
            }
            if($result[csf('road_no')]!='')
            {
            $road_no=$result[csf('road_no')];
            }
            if($result[csf('block_no')]!='')
            {
            $block_no=$result[csf('block_no')];
            }
            if($result[csf('city')]!='')
            {
            $city=$result[csf('city')];
            }
            $company_address[$result[csf('id')]]=$plot_no.'&nbsp'.$level_no.'&nbsp'.$road_no.'&nbsp'.$block_no.'&nbsp'.$city;
            }
            ?>
            </label>
            <br/>
            <b style="font-size:20px;">
            <?
            if(str_replace("'","",$cbo_isshort)==2){
            $isshort="";
            }
            if(str_replace("'","",$cbo_isshort)==1){
            $isshort="[Short]";
            }
            if ($report_title !=""){
            echo $report_title." ".$isshort;
            }
            else{
            echo "Main Trims Booking ".$isshort;
            }

            ?>
            </b>
            </td>
            <td width="10px" align="center" style="font-size:20px;">
            <table width="80%" align="right" cellpadding="0" cellspacing="0" style="border:0px solid black">
            <tr>
            <td width="80">  Booking No:&nbsp; <?php echo $varcode_booking_no; ?>  </td>
            </tr>
            <tr>
            <td>  Booking Date:&nbsp; <?php echo change_date_format($booking_date); ?>  </td>
            </tr>
            <?
            if($revised_no>0)
            {
            ?>
            <tr>
            <td>  Revised No:&nbsp; <?php echo $revised_no.'&nbsp(Date:'.change_date_format($max_revised_date).')'; ?>  </td>
            </tr>
            <?
            }
            if(str_replace("'","",$id_approved_id) ==1)
            {
            ?>
            <tr>
            <td>Approved Status :&nbsp;  <? if(str_replace("'","",$id_approved_id) ==1){ echo "(Approved)";}else{echo "";}; ?> </td>
            </tr>
            <?
            }
			
			//$msg
            ?>
             <tr>
            <td style="color: #F00"><? echo $msg; ?> </td>
            </tr>
            </table>

            </td>
            </tr>
            </table>
            </td>
            </tr>
        </table>
        <table width="100%" style="border:0px solid black;table-layout: fixed;">
            <tr>
            <td colspan="6" valign="top"></td>
            </tr>
            <tr>
            <td width="100" style="font-size:18px"><span><b>To, </b></span>  </td>
            <td width="110" colspan="5" style="font-size:18px">&nbsp;<span></span></td>
            </tr>
            <tr>

            <td width="210" colspan="2" style="font-size:18px">&nbsp; <b>
            <?
            if($pay_mode_id==5 || $pay_mode_id==3){
            echo $company_library[$supplier_id];
            }
            else{
            echo $supplier_name_arr[$supplier_id];
            }
            ?></b>
            </td>
            <td  width="100" style="font-size:12px"><b>Buyer.</b></td>
            <td  width="110" >:&nbsp;<? echo implode(",",array_unique($buyer_string));; ?></td>
            <td width="100" style="font-size:12px"><b>Delivery Date</b></td>
            <td width="110">:&nbsp;<?  echo change_date_format($delivery_date); ?></td>
            </tr>
            <tr>

            <td width="110" colspan="2" rowspan="2" style="font-size:18px">Address :&nbsp;
            <?
            if($pay_mode_id==5 || $pay_mode_id==3){
            $address=$company_address[$supplier_id];
            }
            else{
            $address=$supplier_address_arr[$supplier_id];
            }
            echo $address;
            ?></b>
            </td>
            <td width="100" style="font-size:12px"><b>PO Qty.</b>   </td>
            <td width="110">:&nbsp;<? echo  array_sum($po_quantity); ?></td>
            <td style="font-size:12px" ><b>Delivery To </b>   </td>
            <td style="">:&nbsp;
            <?
            //echo $attention;
            ?>
            </td>

            </tr>
            <tr>

            <td width="100" style="font-size:12px"><b>Season</b> </td>
            <td width="110">:&nbsp;<? echo implode(",",array_unique($season)); ?></td>

            <td width="100" style="font-size:12px"><b>Currency</b></td>
            <td width="110">:&nbsp;<?  echo $currency[$currency_id]; ?></td>

            </tr>
            <tr>
            <td style="font-size:12px" ><b>Attention </b>   </td>
            <td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;">:&nbsp;
            <?
            echo $attention;
            ?>
            </td>
            <td width="100" style="font-size:12px"><b>Order Repeat </b> </td>
            <td width="110">:&nbsp;<? echo $order_rept_no; ?></td>
              <td style="font-size:12px"><b>Conversion Rate</b></td>
            <td>:&nbsp;
            <?
            echo $exchange_rate;
            ?>
            </td>



            </tr>

            <tr>
            <td style="font-size:12px"><b>Source</b></td>
            <td>:&nbsp;<? echo $source[$source_id];?></td>
            <td style="font-size:12px"><b>Dealing Merchant</b></td>
            <td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;">:&nbsp;
            <?
            echo implode(",",array_unique($all_dealing_marcent));
            ?>
            </td>
          	<td  style="font-size:12px"><b>Pay mode</b></td>
            <td>:&nbsp;<? echo $pay_mode[$pay_mode_id];?></td>
            </tr>
            <tr>
            <td width="100" style="font-size:12px"><b>Remarks</b>  </td>
            <td width="110" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;" colspan="5">:&nbsp;<? echo $remarks; ?></td>

            </tr>
        </table>


		<?

		$booking_grand_total=0;

        ?>
          <!--==============================================AS PER GMTS COLOR START=========================================  -->
		<?
		$precost_arr=array();
		$trims_qtyPerUnit_arr=array();
		$precost_sql=sql_select("select a.id, a.job_no,a.trim_group,a.calculatorstring, c.cal_parameter from wo_pre_cost_trim_cost_dtls a,wo_booking_dtls b, lib_item_group c where a.job_no=b.job_no and a.trim_group=b.trim_group and a.trim_group=c.id and b.booking_no=$txt_booking_no and a.id=b.pre_cost_fabric_cost_dtls_id and b.status_active=1 and b.is_deleted=0");
        $calUom="";
		foreach($precost_sql as $precost_row){
			if($precost_row[csf('cal_parameter')]==1){
			   $calUom="Mtr";
			   $trims_qtyPerUnit_arr[$precost_row[csf('id')]][1]=$precost_row[csf('calculatorstring')];
			   $trims_qtyPerUnit_arr[$precost_row[csf('id')]][2]=$calUom;
			}
			else if($precost_row[csf('cal_parameter')]==2){
			   $calUom="Pcs";
			}
			else if($precost_row[csf('cal_parameter')]==3){
			   $calUom="Pcs";
			}
			else if($precost_row[csf('cal_parameter')]==4){
			   $calUom="Pcs";
			}
			else if($precost_row[csf('cal_parameter')]==5){
			   $calUom="Yds";
			   $trims_qtyPerUnit_arr[$precost_row[csf('id')]][1]=$precost_row[csf('calculatorstring')];
			   $trims_qtyPerUnit_arr[$precost_row[csf('id')]][2]=$calUom;
			}
			else if($precost_row[csf('cal_parameter')]==6){
			   $calUom="Yds";
			   $trims_qtyPerUnit_arr[$precost_row[csf('id')]][1]=$precost_row[csf('calculatorstring')];
			   $trims_qtyPerUnit_arr[$precost_row[csf('id')]][2]=$calUom;
			}
			else if($precost_row[csf('cal_parameter')]==7){
			   $calUom="Pcs";
			   $trims_qtyPerUnit_arr[$precost_row[csf('id')]][1]=$precost_row[csf('calculatorstring')];
			   $trims_qtyPerUnit_arr[$precost_row[csf('id')]][2]=$calUom;
			}
			else if($precost_row[csf('cal_parameter')]==8){
			   $calUom="Yds";
			   $trims_qtyPerUnit_arr[$precost_row[csf('id')]][1]=$precost_row[csf('calculatorstring')];
			   $trims_qtyPerUnit_arr[$precost_row[csf('id')]][2]=$calUom;
			}
			else{
				$calUom=0;
			}
			//$precost_arr[$precost_row[csf('job_no')]][$precost_row[csf('trim_group')]]['calculatorstring']=$precost_row[csf('calculatorstring')];
			//$precost_arr[$precost_row[csf('job_no')]][$precost_row[csf('trim_group')]]['cal_parameter_uom']=$calUom;

			//$trims_qtyPerUnit_arr[$precost_row[csf('id')]][1]=$precost_row[csf('calculatorstring')];
			//$trims_qtyPerUnit_arr[$precost_row[csf('id')]][2]=$calUom;
		}

		if($show_comment==0){
			$showComment=2;
		}

		$booking_country_arr=array();
		$nameArray_booking_country=sql_select( "select pre_cost_fabric_cost_dtls_id,sensitivity,country_id_string from wo_booking_dtls  where booking_no=$txt_booking_no and status_active=1 and is_deleted=0");
		foreach($nameArray_booking_country as $nameArray_booking_country_row){
			$country_id_string=explode(",",$nameArray_booking_country_row[csf('country_id_string')]);
			$tocu=count($country_id_string);
			for($cu=0;$cu<$tocu;$cu++){
				$booking_country_arr[$nameArray_booking_country_row[csf('pre_cost_fabric_cost_dtls_id')]][$nameArray_booking_country_row[csf('sensitivity')]][$country_id_string[$cu]]=$country_arr[$country_id_string[$cu]];
			}
		}
	
		$nameArray_job_po=sql_select( "select job_no from wo_booking_dtls  where booking_no=$txt_booking_no and status_active=1 and is_deleted=0 group by job_no order by job_no ");
		foreach($nameArray_job_po as $nameArray_job_po_row){
		$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."'   and sensitivity=1 and status_active=1 and is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group  order by trim_group ");
		
	    if(count($nameArray_item)>0){
		$fileRefNos="";
		$refNo=rtrim($ref_no_arr[$nameArray_job_po_row[csf('job_no')]]['ref'],',');
		$refNo=implode(", ",array_unique(explode(",",$refNo)));
		if($refNo!='') $fileRefNos=", &nbsp; Internal. Ref.: ".$refNo;

		
		$fileNo=rtrim($ref_no_arr[$nameArray_job_po_row[csf('job_no')]]['file'],',');
		$fileNo=implode(", ",array_unique(explode(",",$fileNo)));
		if($fileNo!='') $fileRefNos.=", &nbsp; File No.: ".$fileNo;
	
		
		
		
        ?>
        &nbsp;
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >

            <tr>
                <td colspan="9" align="">
                <table width="100%" style="table-layout: fixed;">
	                <tr>
		                <td ><strong>As Per Garments Color [<?="Job NO:".$nameArray_job_po_row[csf('job_no')].']'." Style Ref.:".$style_ref[$nameArray_job_po_row[csf('job_no')]].$fileRefNos; ?></strong>,&nbsp;&nbsp;<span style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap; font-weight:bold;">Po No: <?=implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?>
			            	</span>
		            	</td>
	                </tr>
                </table>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Brand/Supplier Ref.</strong> </td>
                <td align="center" style="border:1px solid black"><strong>Item Color</strong></td>
                <td align="center" style="border:1px solid black"><strong>Qty per Unit</strong></td>
                <td style="border:1px solid black" align="center"><strong>WO Qty</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
				<?
				if($show_comment==1){?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
				<td style="border:1px solid black" align="center"><strong>Amount</strong></td>
				<?}?>
               
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item){
			$i++;
            $nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid, b.description, b.brand_supplier,b.item_color,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=1  and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_color order by bid ");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center"  style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <?
				echo $trim_group_library[$result_item[csf('trim_group')]]."<br/>";
				echo implode(",",$booking_country_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][1]);
				?>
                </td>
                <?
				$item_desctiption_total=0;
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription){
                ?>
                <td style="border:1px solid black"><?  if($result_itemdescription[csf('description')]){ echo $result_itemdescription[csf('description')];} ?> </td>
                <td style="border:1px solid black"><? if($result_itemdescription[csf('brand_supplier')]){echo $result_itemdescription[csf('brand_supplier')];} ?> </td>
                <td style="border:1px solid black; text-align:left">
               <? echo $color_library[$result_itemdescription[csf('item_color')]]; ?>
                </td>
                <td style="border:1px solid black; text-align:left">
               <?
			   $calUom=$trims_qtyPerUnit_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]][2];
			   $calQty=explode("_",$trims_qtyPerUnit_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]][1]);
			   if($calUom && end($calQty)){
				   echo "1".$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]."=".prev($calQty)." ".$calUom;
			   }
			   ?>
                </td>
                <td style="border:1px solid black; text-align:right">
				<?
				echo number_format($result_itemdescription[csf('cons')],4);
				//echo $result_itemdescription[csf('cons')];
				$item_desctiption_total += $result_itemdescription[csf('cons')] ;
				?>
                </td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_text=$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
				<?
				if($show_comment==1){?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
				<td style="border:1px solid black; text-align:right">
                <?
                $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
				echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
				<?}?>
                
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="4"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold;"><? echo number_format($item_desctiption_total,4);  ?></td>
                <td style="border:1px solid black;  text-align:center"><? echo $uom_text; ?></td>
				<?
				if($show_comment==1){?>
                <td style="border:1px solid black; text-align:right"></td>
				<td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
				<?}?>
                
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<?=(9-$showComment);?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER GMTS COLOR END=========================================  -->


        <!--==============================================Size Sensitive START=========================================  -->
		<?
		$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=2 and status_active=1 and is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group  order by trim_group ");
		if(count($nameArray_item)>0)
		{
		$fileRefNos="";
		$refNo=rtrim($ref_no_arr[$nameArray_job_po_row[csf('job_no')]]['ref'],',');
		$refNo=implode(", ",array_unique(explode(",",$refNo)));
		if($refNo!='') $fileRefNos=", &nbsp; Internal. Ref.: ".$refNo;
		
		$fileNo=rtrim($ref_no_arr[$nameArray_job_po_row[csf('job_no')]]['file'],',');
		$fileNo=implode(", ",array_unique(explode(",",$fileNo)));
		if($fileNo!='') $fileRefNos.=", &nbsp; File No.: ".$fileNo;
        ?>

        <table border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="11" align="">
                 <table width="100%" style="table-layout: fixed;">
                <tr>
                <td ><strong>Size Sensitive (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo " Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]].$fileRefNos; ?></strong>, &nbsp;&nbsp;<span style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap; font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></span>
                </td>
                </tr>
                </table>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Brand/Supplier Ref.</strong> </td>
                <td align="center" style="border:1px solid black"><strong>Item Size</strong></td>
				<td align="center" style="border:1px solid black"><strong>GMTS Size</strong></td>
                <td align="center" style="border:1px solid black"><strong>Article No.</strong></td>
                <td style="border:1px solid black" align="center"><strong>Qty per Unit</strong></td>
                <td style="border:1px solid black" align="center"><strong>WO Qty.</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
				<?
				if($show_comment==1){?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
				<td style="border:1px solid black" align="center"><strong>Amount</strong></td>
				<?}?>
               
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
			$i++;
			$nameArray_item_description=sql_select( "SELECT a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid, b.description, b.brand_supplier,b.item_size,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount,b.article_number, b.gmts_sizes as gmt_size_id  
				from wo_booking_dtls a,  wo_trim_book_con_dtls b 
				where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and   a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.sensitivity=2 and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
				group by a.pre_cost_fabric_cost_dtls_id, b.description,b.article_number, b.brand_supplier,b.item_size, b.gmts_sizes order by bid");

			$article_number_data=sql_select( "SELECT c.size_number_id, c.article_number, c.job_no_mst, c.po_break_down_id
			from wo_booking_dtls a, wo_trim_book_con_dtls b, wo_po_color_size_breakdown c
			where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.sensitivity=2 and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and a.sensitivity=2 and c.job_no_mst=a.job_no  and c.job_no_mst=b.job_no and b.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
			group by c.size_number_id, c.article_number, c.job_no_mst, c.po_break_down_id");
			$article_numberArr=array();
			foreach ($article_number_data as $value)
			{
			    //$article_numberArr[$value[csf('po_break_down_id')]][$value[csf('size_number_id')]]['article_number'].=$value[csf('article_number')].',';
			    $article_numberArr[$value[csf('size_number_id')]]['article_number'].=$value[csf('article_number')].',';
			}
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i ; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <?
				echo $trim_group_library[$result_item[csf('trim_group')]]."<br/>";
				echo implode(",",$booking_country_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][2]);
				?>
                </td>
                <?
                $item_desctiption_total=0;
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {

                ?>
                <td style="border:1px solid black"><? if($result_itemdescription[csf('description')]){ echo $result_itemdescription[csf('description')];}?> </td>
                <td style="border:1px solid black"><? if($result_itemdescription[csf('brand_supplier')]){echo $result_itemdescription[csf('brand_supplier')];} ?> </td>
                
                <td style="border:1px solid black; text-align:left">
              	<? echo $result_itemdescription[csf('item_size')];?>
                </td>
				<td style="border:1px solid black; text-align:left">
              	<? echo $size_library[$result_itemdescription[csf('gmt_size_id')]];?>
                </td>
                <td style="border:1px solid black"><?php 
                	$art_no=rtrim($article_numberArr[$result_itemdescription[csf('gmt_size_id')]]['article_number'],',');
                	//$art_nos=implode(",",array_unique(explode(",",$art_no)));

                	$art_no_arr=array_unique(explode(",",$art_no));    
				    $art_nos ="";
				    foreach($art_no_arr as $key => $values)
				    {
				        if($values!='no article')
				        {
				            if ($art_nos=="") 
				            {
				                $art_nos.= $values;
				            }
				            else 
				            {
				                $art_nos.= ','.$values;
				            }
				        }
				    }
                	echo $art_nos;
					//if($result_itemdescription[csf('article_number')]!="no article"){
					//echo $result_itemdescription[csf('article_number')]; } else { echo "-"; } ?> 
				</td>
                <td style="border:1px solid black; text-align:left">
               <?
			   $calUom=$trims_qtyPerUnit_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]][2];
			   $calQty=explode("_",$trims_qtyPerUnit_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]][1]);
			   if($calUom && end($calQty)){
				   echo "1".$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]."=".prev($calQty)." ".$calUom;
			   }
			   ?>
                </td>


                <td style="border:1px solid black; text-align:right">
				 <?
				 echo number_format($result_itemdescription[csf('cons')],4);
				 //echo $result_itemdescription[csf('cons')];
                $item_desctiption_total += $result_itemdescription[csf('cons')] ;
				?>
                </td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_text=$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
				<?
				if($show_comment==1){?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
				<td style="border:1px solid black; text-align:right">

				<?
				$amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
				echo number_format($amount_as_per_gmts_color,4);
				$total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
				?>
				</td>
				<?}?>
               
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="6"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold"><? echo number_format($item_desctiption_total,4);  ?></td>
                 <td style="border:1px solid black;  text-align:center"><? echo $uom_text; ?></td>
				 <?
				if($show_comment==1){?>
                <td style="border:1px solid black; text-align:right"></td>
				<td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
				<?}?>
               
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<?=(11-$showComment);?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <br/>
        <?
		}
		?>

        <!--==============================================Size Sensitive END=========================================  -->

         <!--==============================================AS PER CONTRAST COLOR START=========================================  -->
		<?
		 $nameArray_item=sql_select( "select pre_cost_fabric_cost_dtls_id, trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=3  and status_active=1 and is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group order by trim_group ");
		if(count($nameArray_item)>0)
		{
		$fileRefNos="";
		$refNo=rtrim($ref_no_arr[$nameArray_job_po_row[csf('job_no')]]['ref'],',');
		$refNo=implode(", ",array_unique(explode(",",$refNo)));
		if($refNo!='') $fileRefNos=", &nbsp; Internal. Ref.: ".$refNo;
		
		$fileNo=rtrim($ref_no_arr[$nameArray_job_po_row[csf('job_no')]]['file'],',');
		$fileNo=implode(", ",array_unique(explode(",",$fileNo)));
		if($fileNo!='') $fileRefNos.=", &nbsp; File No.: ".$fileNo;
        ?>
        <table border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="10" align="">
                <table width="100%" style="table-layout: fixed;">
	                <tr>
		                <td ><strong>Contrast Color (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]].$fileRefNos; ?></strong>, &nbsp;&nbsp;<span style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap; font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></span>
		                </td>
	                </tr>
                </table>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Brand/Supplier Ref.</strong> </td>
                <td align="center" style="border:1px solid black"><strong>Item Color</strong></td>
                <td align="center" style="border:1px solid black"><strong>Gmts Color</strong></td>
                <td align="center" style="border:1px solid black"><strong>Qty per Unit</strong></td>
                <td style="border:1px solid black" align="center"><strong>WO Qty.</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
				 <?
				if($show_comment==1){?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
				<td style="border:1px solid black" align="center"><strong>Amount</strong></td>
				<?}?>
               
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
				$gmtcolor_library=return_library_array( "select b.item_color,b.color_number_id from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=3  and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by trim_group ", "item_color", "color_number_id"  );

			$nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid,b.description, b.brand_supplier,b.item_color,b.color_number_id,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.sensitivity=3  and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_color,b.color_number_id order by bid ");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <?
				echo $trim_group_library[$result_item[csf('trim_group')]]."<br/>";
				echo implode(",",$booking_country_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][3]);
				?>
                </td>
                <?
                $item_desctiption_total=0;
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {

                ?>
                <td style="border:1px solid black"><? if($result_itemdescription[csf('description')]){ echo $result_itemdescription[csf('description')];}?> </td>
                <td style="border:1px solid black"><? if($result_itemdescription[csf('brand_supplier')]){echo $result_itemdescription[csf('brand_supplier')];} ?> </td>
                <td style="border:1px solid black; text-align:right">
               <? echo $color_library[$result_itemdescription[csf('item_color')]]; ?>
                </td>
                <td style="border:1px solid black; text-align:left">
               <?
			   echo $color_library[$result_itemdescription[csf('color_number_id')]];
			   ?>
                </td>
               <td style="border:1px solid black; text-align:left">
               <?
			  // $calUom=$precost_arr[$nameArray_job_po_row[csf('job_no')]][$result_item[csf('trim_group')]]['cal_parameter_uom'];
			   //$calQty=explode("_",$precost_arr[$nameArray_job_po_row[csf('job_no')]][$result_item[csf('trim_group')]]['calculatorstring']);

			   $calUom=$trims_qtyPerUnit_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]][2];
			   $calQty=explode("_",$trims_qtyPerUnit_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]][1]);
			  // print_r($calQty);
			   if($calUom && end($calQty)){
				   echo "1".$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]."=".prev($calQty)." ".$calUom;
			   }
			   ?>
                </td>

                <td style="border:1px solid black; text-align:right">
				<?
				echo number_format($result_itemdescription[csf('cons')],4);
				//echo $result_itemdescription[csf('cons')];
                $item_desctiption_total += $result_itemdescription[csf('cons')] ;
				?>
                </td>
                 <td style="border:1px solid black; text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_text=$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
				  <?
				if($show_comment==1){?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
				<td style="border:1px solid black; text-align:right">
                <?
                $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
				<?}?>
               
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="5"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold;"><? echo number_format($item_desctiption_total,4);  ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $uom_text; ?></td>
				 <?
				if($show_comment==1){?>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>
				<td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
				<?}?>
               
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<?=(10-$showComment);?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <br/>
        <?
		}
		?>

        <!--==============================================AS PER CONTRAST COLOR END=========================================  -->

        <!--==============================================AS PER GMTS Color & SIZE START=========================================  -->
		<?
		$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=4 and status_active=1 and is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group order by trim_group");
	   if(count($nameArray_item)>0)
		{
		$fileRefNos="";
		$refNo=rtrim($ref_no_arr[$nameArray_job_po_row[csf('job_no')]]['ref'],',');
		$refNo=implode(", ",array_unique(explode(",",$refNo)));
		if($refNo!='') $fileRefNos=", &nbsp; Internal. Ref.: ".$refNo;
		
		$fileNo=rtrim($ref_no_arr[$nameArray_job_po_row[csf('job_no')]]['file'],',');
		$fileNo=implode(", ",array_unique(explode(",",$fileNo)));
		if($fileNo!='') $fileRefNos.=", &nbsp; File No.: ".$fileNo;
        ?>

        <table border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="13" align="">
                <table width="100%" style="table-layout: fixed;">
                <tr>
                <td ><strong>Color & size sensitive (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo " Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]].$fileRefNos; ?></strong>, &nbsp;&nbsp;<span style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap; font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></span></td>
                </tr>
                </table>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>


                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Brand/Supplier Ref.</strong> </td>
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <td style="border:1px solid black; width:170px;"><strong>Gmts Color</strong> </td>

                <td align="center" style="border:1px solid black"><strong>Item Size</strong></td>
				<td align="center" style="border:1px solid black"><strong>GMTS Size</strong></td>
                <td align="center" style="border:1px solid black"><strong>Article No.</strong></td>
                <td align="center" style="border:1px solid black"><strong>Qty per Unit</strong></td>
                <td style="border:1px solid black" align="center"><strong>WO Qty.</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
				 <?
				if($show_comment==1){?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
				<td style="border:1px solid black" align="center"><strong>Amount</strong></td>
					<?}?>
              
            </tr>
            <?
			 $nameArray_color_arr=sql_select( "SELECT a.trim_group,b.description,a.pre_cost_fabric_cost_dtls_id as f_dtlsid, b.item_color as item_color, b.item_size,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount,b.article_number,b.gmts_sizes from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=4 and b.requirment !=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by  a.trim_group,b.description,a.pre_cost_fabric_cost_dtls_id,b.article_number,b.item_color,b.item_size,b.gmts_sizes order by b.item_size");

			foreach($nameArray_color_arr as $row)
            {
				$color_size_dtls_arr[$row[csf('f_dtlsid')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('description')]][$row[csf('item_size')]][$row[csf('gmts_sizes')]]['cons']=$row[csf('cons')];
				$color_size_dtls_arr[$row[csf('f_dtlsid')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('description')]][$row[csf('item_size')]][$row[csf('gmts_sizes')]]['amount']=$row[csf('amount')];
				$color_size_dtls_arr[$row[csf('f_dtlsid')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('description')]][$row[csf('item_size')]][$row[csf('gmts_sizes')]]['article_number']=$row[csf('article_number')];
			}
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
				$gmtcolor_library=return_library_array( "select b.item_color,b.color_number_id from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.sensitivity=4    and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by trim_group ", "item_color", "color_number_id"  );

			 //$nameArray_color=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid, b.color_number_id as gmt_color, b.item_color as color_number_id,b.item_size,b.description, b.brand_supplier,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and a.sensitivity=4 and b.requirment !=0 group by a.pre_cost_fabric_cost_dtls_id, b.color_number_id, b.item_color,b.item_size,b.description, b.brand_supplier order by bid ");
			 $nameArray_color=sql_select( "SELECT a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid, b.color_number_id as gmt_color, b.item_color as color_number_id,b.item_size,b.gmts_sizes,b.description, b.brand_supplier,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount, min(c.color_order) as color_order, min(c.size_order) as size_order, c.article_number 
			 from wo_booking_dtls a, wo_trim_book_con_dtls b,wo_po_color_size_breakdown c  
			 where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and b.po_break_down_id=c.po_break_down_id and b.color_number_id=c.color_number_id and b.gmts_sizes=c.size_number_id  and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and a.sensitivity=4 and b.requirment !=0 and c.id=b.color_size_table_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
			 group by a.pre_cost_fabric_cost_dtls_id, b.color_number_id, b.item_color,b.item_size,b.gmts_sizes,b.description, b.brand_supplier, c.article_number order by color_order,size_order"); //and  c.id=b.color_size_table_id

            $nameArray_item_description=sql_select( "select distinct uom from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=4 and trim_group=".$result_item[csf('trim_group')]." and pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and status_active=1 and is_deleted=0 order by trim_group ");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo   (count($nameArray_item_description)*count($nameArray_color)); ?>">
                <? echo $i ; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo (count($nameArray_item_description)*count($nameArray_color)); ?>">
                <?
				echo $trim_group_library[$result_item[csf('trim_group')]]."<br/>";
				echo implode(",",$booking_country_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][4]);
				?>
                </td>
                <?



				$item_desctiption_total=0;
                $total_amount_as_per_gmts_color=0;
				foreach($nameArray_color as $result_color)
                {
					$item_cons=$color_size_dtls_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][$result_item[csf('trim_group')]][$result_color[csf('color_number_id')]][$result_color[csf('description')]][$result_color[csf('item_size')]][$result_color[csf('gmts_sizes')]]['cons'];
					$item_amount=$color_size_dtls_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][$result_item[csf('trim_group')]][$result_color[csf('color_number_id')]][$result_color[csf('description')]][$result_color[csf('item_size')]][$result_color[csf('gmts_sizes')]]['amount'];
					$article_number=$color_size_dtls_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][$result_item[csf('trim_group')]][$result_color[csf('color_number_id')]][$result_color[csf('description')]][$result_color[csf('item_size')]][$result_color[csf('gmts_sizes')]]['article_number'];
					?>

					<td style="border:1px solid black" ><? if($result_color[csf('description')]){echo $result_color[csf('description')];} ?> </td>
					<td style="border:1px solid black" ><? if($result_color[csf('brand_supplier')]){echo $result_color[csf('brand_supplier')]; }?> </td>
                    <td style="border:1px solid black"><? echo $color_library[$result_color[csf('color_number_id')]]; ?> </td>
					
                    <td style="border:1px solid black"><? echo $color_library[$result_color[csf('gmt_color')]]; //echo $color_library[$gmtcolor_library[$result_color[csf('color_number_id')]]]; ?> </td>
					<td style="border:1px solid black; text-align:left">
					<? echo $result_color[csf('item_size')]; ?>
					</td>
					<td style="border:1px solid black; text-align:left">
					<? echo $size_library[$result_color[csf('gmts_sizes')]]; ?>
					</td>
					<td style="border:1px solid black; text-align:left">
					<? if ($result_color[csf('article_number')]!='no article') {
						echo $result_color[csf('article_number')];
					}  ?>
					</td>
                    <td style="border:1px solid black; text-align:left">
				   <?
                   //$calUom=$precost_arr[$nameArray_job_po_row[csf('job_no')]][$result_item[csf('trim_group')]]['cal_parameter_uom'];
				   //$calQty=explode("_",$precost_arr[$nameArray_job_po_row[csf('job_no')]][$result_item[csf('trim_group')]]['calculatorstring']);

				   $calUom=$trims_qtyPerUnit_arr[$result_color[csf('pre_cost_fabric_cost_dtls_id')]][2];
			       $calQty=explode("_",$trims_qtyPerUnit_arr[$result_color[csf('pre_cost_fabric_cost_dtls_id')]][1]);
				   if($calUom && end($calQty)){
					   echo "1".$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]."=".prev($calQty)." ".$calUom;
				   }
                   ?>
                    </td>

					<td style="border:1px solid black; text-align:right">
					<?
					//echo number_format($result_itemdescription[csf('cons')],4);
					echo  number_format($item_cons,4);
					$item_desctiption_total += $item_cons ;
					//echo number_format($item_desctiption_total,2);
					?>
                    </td>
					<td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_text=$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]];  ?></td>
					 <?
					if($show_comment==1){?>
					<td style="border:1px solid black; text-align:right">
					<?
					echo number_format($result_color[csf('rate')],4);
					/*$rate =$result_color[csf('amount')]/$result_color[csf('cons')];
					echo number_format($rate,4); */
					?>
                     </td>
					 <td style="border:1px solid black; text-align:right">
					<?
					//$amount_as_per_gmts_color = $item_desctiption_total*  $result_color[csf('rate')];
					$amount_as_per_gmts_color =$item_amount;
					echo number_format($amount_as_per_gmts_color,4);
					$total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
					?>
					</td>
					 	<?}?>
					
				</tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="10"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold;"><? echo number_format($item_desctiption_total,4);  ?></td>
                <td style="border:1px solid black;  text-align:center"><? echo $uom_text; ?></td>
				 <?
				if($show_comment==1){?>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>
				<td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
					<?}?>
               
            </tr>
            <?
            }

            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<?=(13-$showComment);?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <br/>
       <?
		}
	   ?>
        <!--==============================================AS PER Color & SIZE  END=========================================  -->



         <!--==============================================NO NENSITIBITY START=========================================  -->
		<?
       //$nameArray_item=sql_select( "select distinct trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and sensitivity=0 order by trim_group ");
		$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=0 and status_active=1 and is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group order by trim_group ");
        //$nameArray_color=sql_select( "select distinct b.color_number_id from wo_trims_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.sensitivity=1");
		//$nameArray_color= array();
		if(count($nameArray_item)>0)
		{
		$fileRefNos="";
		$refNo=rtrim($ref_no_arr[$nameArray_job_po_row[csf('job_no')]]['ref'],',');
		$refNo=implode(", ",array_unique(explode(",",$refNo)));
		if($refNo!='') $fileRefNos=", &nbsp; Internal. Ref.: ".$refNo;
		
		$fileNo=rtrim($ref_no_arr[$nameArray_job_po_row[csf('job_no')]]['file'],',');
		$fileNo=implode(", ",array_unique(explode(",",$fileNo)));
		if($fileNo!='') $fileRefNos.=", &nbsp; File No.: ".$fileNo;
        ?>
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="8" align="">
                <table width="100%" style="table-layout: fixed;">
                <tr>
                <td ><strong>NO sensitive (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]]; ?></strong>,&nbsp;&nbsp;<span style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap; font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]).$fileRefNos; ?></span></td>
                </tr>
                </table>
                 </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
               
                <td style="border:1px solid black"><strong>Brand/Supplier Ref.</strong> </td>
                <td align="center" style="border:1px solid black"><strong>Article No</strong></td>
                 <td style="border:1px solid black"><strong>Item Color</strong> </td>
                 <td align="center" style="border:1px solid black"><strong>Qty per Unit</strong></td>
                <td align="center" style="border:1px solid black"><strong> Qnty</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
				 <?
				if($show_comment==1){?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
				<td style="border:1px solid black" align="center"><strong>Amount</strong></td>
				<?}?>
             
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;

				$nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_color,avg(b.rate) as rate, sum(b.amount) as amount,b.article_number from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and a.sensitivity=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.description, b.article_number,b.brand_supplier,b.item_color");

            //$nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 and trim_group=".$result_item[csf('trim_group')]." order by trim_group ");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">

                <?
				echo $trim_group_library[$result_item[csf('trim_group')]]."<br/>";
				echo implode(",",$booking_country_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][0]);

				?>
                </td>
                <?
                $color_tatal=0;
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $item_desctiption_total=0;
                ?>
                <td style="border:1px solid black"><? if($result_itemdescription[csf('description')]){ echo $result_itemdescription[csf('description')];} ?> </td>
                <td style="border:1px solid black"><? if($result_itemdescription[csf('brand_supplier')]){echo $result_itemdescription[csf('brand_supplier')];} ?> </td>
                <td style="border:1px solid black">
				<?
				if($result_itemdescription[csf('article_number')]!="no article"){
					echo $result_itemdescription[csf('article_number')];
				}else{
					echo "-";
				}
				?>
                </td>
                
                <td style="border:1px solid black"><? echo $color_library[$result_itemdescription[csf('item_color')]]; ?> </td>
                <td style="border:1px solid black; text-align:left">
               <?
			   //$calUom=$precost_arr[$nameArray_job_po_row[csf('job_no')]][$result_item[csf('trim_group')]]['cal_parameter_uom'];
			   //$calQty=explode("_",$precost_arr[$nameArray_job_po_row[csf('job_no')]][$result_item[csf('trim_group')]]['calculatorstring']);

			   $calUom=$trims_qtyPerUnit_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]][2];
			   $calQty=explode("_",$trims_qtyPerUnit_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]][1]);
			   if($calUom && end($calQty)){
				   echo "1".$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]."=".prev($calQty)." ".$calUom;
			   }
			   ?>
                </td>
                <?
				if($db_type==0)
				        {
						$nameArray_color_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.trim_group=". $result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and  b.description='". $result_itemdescription[csf('description')]."' and b.brand_supplier='".$result_itemdescription[csf('brand_supplier')]."' and b.item_color='".$result_itemdescription[csf('item_color')]."'");
						}
						if($db_type==2)
				        {
						$nameArray_color_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trim_group=". $result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and nvl( b.description,0)=nvl('". $result_itemdescription[csf('description')]."',0) and nvl(b.brand_supplier,0)=nvl('".$result_itemdescription[csf('brand_supplier')]."',0) and nvl(b.item_color,0)=nvl('".$result_itemdescription[csf('item_color')]."',0)");

						}

                foreach($nameArray_color_size_qnty as $result_color_size_qnty)
                {
                ?>
                <td style="border:1px solid black; text-align:right">
                 <?
                if($result_color_size_qnty[csf('cons')]!= "")
                {
                echo number_format($result_color_size_qnty[csf('cons')],4);
                $item_desctiption_total += $result_color_size_qnty[csf('cons')] ;
                $color_tatal+=$result_color_size_qnty[csf('cons')];
                }
                else echo "";
                ?>
                </td>
                <?
                }
                ?>

                <td style="border:1px solid black; text-align:center "><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_text=$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
				 <?
				if($show_comment==1){?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
				<td style="border:1px solid black; text-align:right">
                <?
                $amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
				<?}?>
                
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="5"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold;">
                <?
                if($color_tatal !='')
                {
                echo number_format($color_tatal,4);
                }
                ?>
                </td>
                <td style="border:1px solid black;  text-align:center"><? echo $uom_text; ?></td>
				 <?
				if($show_comment==1){?>
                <td style="border:1px solid black; text-align:right"></td>
				<td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
				<?}?>
                
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<?=(10-$showComment);?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <?
		//print_r($color_tatal);
		}
		}
		?>
        <!--==============================================NO NENSITIBITY END=========================================  -->
       &nbsp;
       <?
       $mcurrency="";
	   $dcurrency="";
	   if($currency_id==1)
	   {
		$mcurrency='Taka';
		$dcurrency='Paisa';
	   }
	   if($currency_id==2)
	   {
		$mcurrency='USD';
		$dcurrency='CENTS';
	   }
	   if($currency_id==3)
	   {
		$mcurrency='EURO';
		$dcurrency='CENTS';
	   }
	   ?>
       <table align="left"  width="100%" class="rpt_table"  border="1" cellpadding="0" cellspacing="0" rules="all">
       <tr style="border:1px solid black;">
                <th width="30%" style="border:1px solid black; text-align:left">Total Booking Amount</th><td width="70%" style="border:1px solid black; text-align:left"><? echo number_format($booking_grand_total,2);?></td>
            </tr>
            <tr style="border:1px solid black;">
                <th width="30%" style="border:1px solid black; text-align:left">Total Booking Amount (in word)</th><td width="70%" style="border:1px solid black;"><? echo number_to_words(def_number_format($booking_grand_total,2,""),$mcurrency, $dcurrency);?></td>
            </tr>
       </table>
         <br/> <br/>
        <table width="100%">
        <tr>

        <td width="49%">
	        <?
	        	echo get_spacial_instruction($txt_booking_no,'');//,273
	        ?>
    	</td>

    <td width="2%"></td>
      <?

	$lib_designation_arr=return_library_array(" select id,custom_designation from lib_designation","id","custom_designation");
	$user_lib_designation_arr=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
	$user_lib_name_arr=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");

	$mst_id=return_field_value("id as mst_id","wo_booking_mst","booking_no=$txt_booking_no","mst_id");
	 $approve_data_array=sql_select("select b.approved_by,min(b.approved_date) as approved_date from   approval_history b where b.mst_id=$mst_id and b.entry_form=8  group by  b.approved_by order by b.approved_by asc");
	 $unapprove_data_array=sql_select("select b.approved_by,b.approved_date,b.un_approved_reason,b.un_approved_date,b.approved_no from   approval_history b where b.mst_id=$mst_id and b.entry_form=8  order by b.approved_date,b.approved_by");


	?>
    <td width="49%">
     <br/> <br/>
  		<?
          if(count($approve_data_array)>0)
			{
 		?>
       <table  width="850" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
            <tr style="border:1px solid black;">
                <th colspan="5" style="border:1px solid black;">Approval Status</th>
                </tr>
                <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th>
				<th width="40%" style="border:1px solid black;">Name</th>
				<th width="30%" style="border:1px solid black;">Designation</th>
				<th width="27%" style="border:1px solid black;">Approval Date</th>

                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($approve_data_array as $row){
			?>
            <tr style="border:1px solid black;">
                <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
				<td width="40%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
				<td width="30%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
				<td width="27%" style="border:1px solid black;text-align:center"><? echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')]));?></td>

                </tr>
                <?
				$i++;
			}
				?>
            </tbody>
        </table>
		<?
		}
		?>
		<br>
		<?
		if(count($unapprove_data_array)>0)
		{
			//and approval_type=0
			$sql_unapproved=sql_select("select booking_id,approval_cause from fabric_booking_approval_cause where  entry_form=8  and is_deleted=0 and status_active=1 and booking_id=$mst_id");


			$unapproved_request_arr=array();
			foreach($sql_unapproved as $rowu)
			{
				if($rowu[csf('approval_cause')]!='')
				{
					$unapproved_request_arr[$rowu[csf('booking_id')]]=$rowu[csf('approval_cause')];
				}
			}
 		?>
       <table  width="850" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
            <tr style="border:1px solid black;">
                <th colspan="6" style="border:1px solid black;">Approval/Un Approval Status</th>
                </tr>
                <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th>
				<th width="30%" style="border:1px solid black;">Name</th>
				<th width="20%" style="border:1px solid black;">Designation</th>
				<th width="5%" style="border:1px solid black;">Approval Status</th>
				<th width="20%" style="border:1px solid black;">Reason For Un Approval</th>
				<th width="22%" style="border:1px solid black;"> Date</th>

                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($unapprove_data_array as $row){

			?>
            <tr style="border:1px solid black;">
                <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
				<td width="30%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
				<td width="20%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
				<td width="5%" style="border:1px solid black; text-align:center"><? echo 'Yes';?></td>
				<td width="20%" style="border:1px solid black;"><? echo '';?></td>
				<td width="22%" style="border:1px solid black;text-align:center"><? if($row[csf('approved_date')]!="") echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')])); else echo "";?></td>
             </tr>
				<?
				$i++;
				$un_approved_date= explode(" ",$row[csf('un_approved_date')]);
				$un_approved_date=$un_approved_date[0];
				if($db_type==0) //Mysql
				{
					if($un_approved_date=="" || $un_approved_date=="0000-00-00") $un_approved_date="";else $un_approved_date=$un_approved_date;
				}
				else
				{
					if($un_approved_date=="") $un_approved_date="";else $un_approved_date=$un_approved_date;
				}

				if($un_approved_date!="")
				{
				?>
			<tr style="border:1px solid black;">
                <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
				<td width="30%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
				<td width="20%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
				<td width="5%" style="border:1px solid black;text-align:center;"><? echo 'No';?></td>
				<td width="20%" style="border:1px solid black;text-align:center"><? echo $unapproved_request_arr[$mst_id];?></td>
				<td width="22%" style="border:1px solid black;text-align:center"><? if($row[csf('un_approved_date')]!="") echo date ("d-m-Y h:i:s",strtotime($row[csf('un_approved_date')])); else echo "";?></td>
              </tr>

                <?
				$i++;
				}

			}
				?>
            </tbody>
        </table>
		<?
		}
		?>

    </td>
    </tr>
    </table>

    </div>
     <br/>
    <div>
		<?
        	echo signature_table(161, $cbo_company_name, "1330px");
			echo "****".custom_file_name($txt_booking_no,$style_sting,$job_no);
        ?>
    </div>

     <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
    fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');

    </script>
	<?
	exit();
}

if($action=="show_trim_booking_report3"){
	extract($_REQUEST);
	$color_library=return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$company_library=return_library_array("select id, company_name from lib_company", "id", "company_name");
	$trim_group_library= return_library_array("select id, item_name from lib_item_group",'id','item_name');
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$trims_imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='pre_cost_trimsv3' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$order_uom_arr=return_library_array("select id,order_uom  from lib_item_group","id","order_uom");
	$deling_marcent_arr=return_library_array("select id,team_member_name from lib_mkt_team_member_info","id","team_member_name");
	$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
	$size_library=return_library_array("select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	$report_type=str_replace("'","",$report_type);

	$supplier_data=sql_select("select id,address_1,supplier_name,email,contact_no from lib_supplier");
	foreach($supplier_data as $rows){
		$supplier_arr[$rows[csf('id')]]=$rows;
	}
	$pay_mode_id="";
	//echo $report_type.'dfdfdfd';
	 $nameArray=sql_select( "select a.booking_no,a.is_approved, a.pay_mode,a.buyer_id, a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.source, a.remarks, a.revised_no,a.update_date,a.delivery_address,a.tenor from wo_booking_mst a where a.booking_no=$txt_booking_no and a.status_active =1 and a.is_deleted=0");
		foreach( $nameArray as $row)
		{
			$varcode_booking_no=$row[csf('booking_no')];
			$booking_date=$row[csf('booking_date')];
			$delivery_date=$row[csf('delivery_date')];
			$pay_mode_id=$row[csf('pay_mode')];
			$supplier_id=$row[csf('supplier_id')];
			$currency_id=$row[csf('currency_id')];
			$buyer_id=$row[csf('buyer_id')];
			$exchange_rate=$row[csf('exchange_rate')];
			$attention=$row[csf('attention')];
			$remarks=$row[csf('remarks')];
			$revised_no=$row[csf('revised_no')];
			$source_id=$row[csf('source')];
			$is_approved=$row[csf('is_approved')];
			$delivery_address=$row[csf('delivery_address')];
			$tenor=$row[csf('tenor')];
			$revised_update_date=explode(" ",$row[csf('update_date')]);
			$mst_revised_date_time=strtotime($revised_update_date[0]);
			$mst_revised_date_arr=$revised_update_date[0];
		}


		$buyer_string=array();
		$style_owner=array();
		$job_no=array();
		$style_ref=array();
		$all_dealing_marcent=array();
		$season=array();

		$nameArray_buyer=sql_select( "select  b.update_date,a.style_ref_no, a.job_no, a.style_owner, a.buyer_name, a.dealing_marchant,a.season,a.season_matrix   from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no=$txt_booking_no and b.status_active=1 and b.is_deleted=0");
        foreach ($nameArray_buyer as $result_buy){
			$buyer_string[$result_buy[csf('buyer_name')]]=$buyer_name_arr[$result_buy[csf('buyer_name')]];
			$style_owner[$result_buy[csf('job_no')]]=$company_library[$result_buy[csf('style_owner')]];
			$job_no[$result_buy[csf('job_no')]]=$result_buy[csf('job_no')];
			$style_ref[$result_buy[csf('job_no')]]=$result_buy[csf('style_ref_no')];
			$all_dealing_marcent[$result_buy[csf('job_no')]]=$deling_marcent_arr[$result_buy[csf('dealing_marchant')]];
			$season[$result_buy[csf('job_no')]]=$season_arr[$result_buy[csf('season_matrix')]];
			$revised_update_date=explode(" ",$result_buy[csf('update_date')]);
			$revised_date_time=strtotime($revised_update_date[0]);
			$revised_date_arr=$revised_update_date[0];
		}
		
		if($revised_date_time!="" && $mst_revised_date_time!="")
		{
			$max_revised_date_time=max($revised_date_time,$mst_revised_date_time);
			$max_revised_date=date('d-m-Y',$max_revised_date_time);
		}
		else if($revised_date_time!="" && $mst_revised_date_time=="")
		{
			$max_revised_date=date('d-m-Y',$revised_date_time);
		}
		else if($mst_revised_date_time!="" && $revised_date_time=="")
		{
			$max_revised_date=date('d-m-Y',$mst_revised_date_time);
		}
		
		
		$style_sting=implode(",",array_unique($style_ref));
		$job_no=implode(",",$job_no);

		$po_no=array();
		$file_no=array();
		$ref_no=array();
		$po_quantity=array();

		$nameArray_job=sql_select( "select b.job_no_mst, b.id, b.po_number, b.grouping, b.file_no, b.po_quantity  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0");
        foreach ($nameArray_job as $result_job){
			$po_no[$result_job[csf('job_no_mst')]][$result_job[csf('id')]]=$result_job[csf('po_number')];
			$file_no[$result_job[csf('id')]]=$result_job[csf('file_no')];
			$ref_no[$result_job[csf('id')]]=$result_job[csf('grouping')];
			$po_quantity[$result_job[csf('id')]]=$result_job[csf('po_quantity')];

			$ref_no_arr[$result_job[csf('job_no_mst')]]['ref'].=$result_job[csf('grouping')].',';
			$ref_no_arr[$result_job[csf('job_no_mst')]]['file'].=$result_job[csf('file_no')].',';
		}

	if($is_approved==1)
	{
	$msg="This booking is approved";	
	}
	else if($is_approved==3)
	{
	$msg="This booking is partial approved";	
	}
	else $msg="";
	?>
	<div style="width:1333px" align="center">

        <table width="100%" border="1" cellpadding="0" cellspacing="0" style="border:0px solid black">
            <tr>
            <td width="20px">
            <table width="100%" cellpadding="0" cellspacing="0" style="border:0px solid black">
            <tr>
            <td width="50" >
            <? if($report_type==1)
            {
            ?>
            <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='30%' width='50%'/>

            <?
            }
            else
            { ?>
            <img  src='../<? echo $imge_arr[$cbo_company_name]; ?>' height='30%' width='50%' />
            <? }
            ?>
            </td>
            <td width="40px" align="center">
            &nbsp;  &nbsp;  &nbsp;
            </td>
            <td width="30px"   align="center">

            <b style="font-size:25px;"> <?
            echo $company_library[$cbo_company_name];
            ?>
            </b>
            <br>
            <label>
            <?
            $nameArray=sql_select( "select id,plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website,contact_no from lib_company where id=$cbo_company_name");
			$company_address=array();
			foreach ($nameArray as $result){
            ?>
            <?  if($result[csf('plot_no')]!='') echo $result[csf('plot_no')]; else echo '';?> &nbsp;
            <? echo $result[csf('level_no')];?> &nbsp;
            <? echo $result[csf('road_no')]; ?>  &nbsp;
            <? echo $result[csf('block_no')];?>  &nbsp;
            <? echo $result[csf('city')];?>  &nbsp;
            <? echo $result[csf('zip_code')]; ?>  &nbsp;
            <?php echo $result[csf('province')]; ?>  &nbsp;
            <? echo $country_arr[$result[csf('country_id')]]; ?> &nbsp;<br/>
            <? echo $result[csf('email')];?>  &nbsp;
            <? echo $result[csf('website')];
           
            }

			$nameArray2=sql_select( "select id,plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website,contact_no from lib_company ");
			$company_address=array();
			foreach ($nameArray2 as $result){
				if($result[csf('plot_no')]!='')
					{
					$plot_no=$result[csf('plot_no')];
					}
					if($result[csf('level_no')]!='')
					{
					$level_no=$result[csf('level_no')];
					}
					if($result[csf('road_no')]!='')
					{
					$road_no=$result[csf('road_no')];
					}
					if($result[csf('block_no')]!='')
					{
					$block_no=$result[csf('block_no')];
					}
					if($result[csf('city')]!='')
					{
					$city=$result[csf('city')];
					}
					$company_address[$result[csf('id')]]['address']=$plot_no.'&nbsp'.$level_no.'&nbsp'.$road_no.'&nbsp'.$block_no.'&nbsp'.$city;
					$company_address[$result[csf('id')]]['tel']=$result[csf('contact_no')];
					$company_address[$result[csf('id')]]['email']=$result[csf('email')];
			}

            ?>
            </label>
            <br/>
            <b style="font-size:20px;">
            &nbsp;
            </b>
            </td>
            <td width="100px" align="center" style="font-size:20px;">&nbsp;</td>
            </tr>
            </table>
            </td>
            </tr>
        </table>
      
		<table border="1" align="left" class="rpt_table container"  cellpadding="0" width="100%" cellspacing="0" rules="all" style="padding-bottom: 10px">
		
			<tr>
       			<td colspan="8" width="600" align="center" ><b>Short Trims Purchase Order-<?php echo $varcode_booking_no; ?></b></td>       			
       		</tr>
			<tr>
       			<td colspan="4" width="600"><b>Beneficiary:</b></td>
       			<td colspan="4" width="600"><b>Consignee:</b></td>
       		</tr>
			<tr>
       			<td colspan="4"><span style="font-weight: bold;"><?
                    if($pay_mode_id==5 || $pay_mode_id==3){
                        echo $company_library[$supplier_id];
                    }
                    else{
						echo $supplier_arr[$supplier_id][csf('SUPPLIER_NAME')];
                    }
                    ?></span><br><?
	                if($pay_mode_id==5 || $pay_mode_id==3){
	                    echo $company_address[$supplier_id]['address'];
	                }
	                else{
						echo $supplier_arr[$supplier_id][csf('ADDRESS_1')];
	                }
                ?><br>TEL# <?
	                if($pay_mode_id==5 || $pay_mode_id==3){
	                    echo $company_address[$supplier_id]['tel'];
	                }
	                else{
						echo $supplier_arr[$supplier_id][csf('CONTACT_NO')];
	                }
                ?><br>E-mail: <?
	                if($pay_mode_id==5 || $pay_mode_id==3){
	                    echo $company_address[$supplier_id]['email'];
	                }
	                else{
						echo $supplier_arr[$supplier_id][csf('EMAIL')];
	                }
                ?></td>
       			<td colspan="4"><span style="font-weight: bold;"><?
                    echo $company_library[$cbo_company_name]; ?></span><br><? 
					foreach ($nameArray as $result){
						?>
						<?  if($result[csf('plot_no')]!='') echo $result[csf('plot_no')]; else echo '';?> &nbsp;
						<? echo $result[csf('level_no')];?> &nbsp;
						<? echo $result[csf('road_no')]; ?>  &nbsp;
						<? echo $result[csf('block_no')];?>  &nbsp;
						<? echo $result[csf('city')];?>  &nbsp;
						<? echo $result[csf('zip_code')]; ?>  &nbsp;
						<?php echo $result[csf('province')]; ?>  &nbsp;
						<? echo $country_arr[$result[csf('country_id')]]; ?> &nbsp;<br/>
						
						<? echo $result[csf('website')];
						if($result[csf('plot_no')]!='')
						{
						$plot_no=$result[csf('plot_no')];
						}
						if($result[csf('level_no')]!='')
						{
						$level_no=$result[csf('level_no')];
						}
						if($result[csf('road_no')]!='')
						{
						$road_no=$result[csf('road_no')];
						}
						if($result[csf('block_no')]!='')
						{
						$block_no=$result[csf('block_no')];
						}
						if($result[csf('city')]!='')
						{
						$city=$result[csf('city')];
						}
						$company_address[$result[csf('id')]]=$plot_no.'&nbsp'.$level_no.'&nbsp'.$road_no.'&nbsp'.$block_no.'&nbsp'.$city;
						}
						?><br>TEL# <? echo $result[csf('contact_no')];?><br>E-Mail: <? echo $result[csf('email')];?>  &nbsp;</td>
       		</tr>
			<tr>
				<td style="font-size:12px" ><b>Issue Date </b>   </td>
				<td width="110">:&nbsp;<?  echo change_date_format($booking_date); ?></td>
				<td style="font-size:12px" ><b>Delivery date </b>   </td>
				<td width="110">:&nbsp;<?  echo change_date_format($delivery_date); ?></td>			
				<td style="font-size:12px" ><b>Contact Person </b>   </td>
           		<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;">:&nbsp;  <?  echo $attention; ?> </td>
				<td style="font-size:12px"><b>Buyer.</b></td>
				<td>:&nbsp;<? echo implode(",",array_unique($buyer_string));; ?></td>
			</tr>
			<tr>
				<td style="font-size:12px" ><b>Delivery To </b>   </td>
				<td style="font-size:12px" colspan="5">:&nbsp;<? echo $delivery_address;?> </td>			
				<td style="font-size:12px"><b>Tenor</b></td>
				<td>:&nbsp;<? echo $tenor; ?></td>
			</tr>
			<tr>
				<td style="font-size:12px" ><b>Remarks </b>   </td>
				<td style="" colspan="7">:&nbsp;  <? echo $remarks; ?></td>
				
			</tr>
		</table>


		<?

		$booking_grand_total=0;

        ?>
          <!--==============================================AS PER GMTS COLOR START=========================================  -->
		<?
		$precost_arr=array();
		$trims_qtyPerUnit_arr=array();
		$precost_sql=sql_select("select a.id, a.job_no,a.trim_group,a.calculatorstring, c.cal_parameter from wo_pre_cost_trim_cost_dtls a,wo_booking_dtls b, lib_item_group c where a.job_no=b.job_no and a.trim_group=b.trim_group and a.trim_group=c.id and b.booking_no=$txt_booking_no and a.id=b.pre_cost_fabric_cost_dtls_id and b.status_active=1 and b.is_deleted=0");

		

		
        $calUom="";
		foreach($precost_sql as $precost_row){
			if($precost_row[csf('cal_parameter')]==1){
			   $calUom="Mtr";
			   $trims_qtyPerUnit_arr[$precost_row[csf('id')]][1]=$precost_row[csf('calculatorstring')];
			   $trims_qtyPerUnit_arr[$precost_row[csf('id')]][2]=$calUom;
			}
			else if($precost_row[csf('cal_parameter')]==2){
			   $calUom="Pcs";
			}
			else if($precost_row[csf('cal_parameter')]==3){
			   $calUom="Pcs";
			}
			else if($precost_row[csf('cal_parameter')]==4){
			   $calUom="Pcs";
			}
			else if($precost_row[csf('cal_parameter')]==5){
			   $calUom="Yds";
			   $trims_qtyPerUnit_arr[$precost_row[csf('id')]][1]=$precost_row[csf('calculatorstring')];
			   $trims_qtyPerUnit_arr[$precost_row[csf('id')]][2]=$calUom;
			}
			else if($precost_row[csf('cal_parameter')]==6){
			   $calUom="Yds";
			   $trims_qtyPerUnit_arr[$precost_row[csf('id')]][1]=$precost_row[csf('calculatorstring')];
			   $trims_qtyPerUnit_arr[$precost_row[csf('id')]][2]=$calUom;
			}
			else if($precost_row[csf('cal_parameter')]==7){
			   $calUom="Pcs";
			   $trims_qtyPerUnit_arr[$precost_row[csf('id')]][1]=$precost_row[csf('calculatorstring')];
			   $trims_qtyPerUnit_arr[$precost_row[csf('id')]][2]=$calUom;
			}
			else if($precost_row[csf('cal_parameter')]==8){
			   $calUom="Yds";
			   $trims_qtyPerUnit_arr[$precost_row[csf('id')]][1]=$precost_row[csf('calculatorstring')];
			   $trims_qtyPerUnit_arr[$precost_row[csf('id')]][2]=$calUom;
			}
			else{
				$calUom=0;
			}
			//$precost_arr[$precost_row[csf('job_no')]][$precost_row[csf('trim_group')]]['calculatorstring']=$precost_row[csf('calculatorstring')];
			//$precost_arr[$precost_row[csf('job_no')]][$precost_row[csf('trim_group')]]['cal_parameter_uom']=$calUom;

			//$trims_qtyPerUnit_arr[$precost_row[csf('id')]][1]=$precost_row[csf('calculatorstring')];
			//$trims_qtyPerUnit_arr[$precost_row[csf('id')]][2]=$calUom;
		}
		$booking_country_arr=array();
		$nameArray_booking_country=sql_select( "select pre_cost_fabric_cost_dtls_id,sensitivity,country_id_string from wo_booking_dtls  where booking_no=$txt_booking_no and status_active=1 and is_deleted=0");
		foreach($nameArray_booking_country as $nameArray_booking_country_row){
			$country_id_string=explode(",",$nameArray_booking_country_row[csf('country_id_string')]);
			$tocu=count($country_id_string);
			for($cu=0;$cu<$tocu;$cu++){
				$booking_country_arr[$nameArray_booking_country_row[csf('pre_cost_fabric_cost_dtls_id')]][$nameArray_booking_country_row[csf('sensitivity')]][$country_id_string[$cu]]=$country_arr[$country_id_string[$cu]];
			}
		}

		$nameArray_job_po=sql_select( "select job_no from wo_booking_dtls  where booking_no=$txt_booking_no and status_active=1 and is_deleted=0 group by job_no order by job_no ");
		foreach($nameArray_job_po as $nameArray_job_po_row){
		$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."'   and sensitivity=1 and status_active=1 and is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group  order by trim_group ");
	    if(count($nameArray_item)>0){
		$fileRefNos="";
		$refNo=rtrim($ref_no_arr[$nameArray_job_po_row[csf('job_no')]]['ref'],',');
		$refNo=implode(", ",array_unique(explode(",",$refNo)));
		if($refNo!='') $fileRefNos=", &nbsp; Internal. Ref.: ".$refNo;

		
		$fileNo=rtrim($ref_no_arr[$nameArray_job_po_row[csf('job_no')]]['file'],',');
		$fileNo=implode(", ",array_unique(explode(",",$fileNo)));
		if($fileNo!='') $fileRefNos.=", &nbsp; File No.: ".$fileNo;
        ?>
        &nbsp;
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >

            <tr>
                <td colspan="10" align="">
                <table width="100%" style="table-layout: fixed;">
	                <tr>
		                <td ><strong>As Per Garments Color [<?="Job NO:".$nameArray_job_po_row[csf('job_no')].']'." Style Ref.:".$style_ref[$nameArray_job_po_row[csf('job_no')]].$fileRefNos; ?></strong>,&nbsp;&nbsp;<span style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap; font-weight:bold;">Po No: <?=implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?>
			            	</span>
		            	</td>
	                </tr>
                </table>
                </td>
            </tr>
			<!-- $trims_imge_arr -->
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
				<td style="border:1px solid black"><strong>Images</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Brand/Supplier Ref.</strong> </td>
                <td align="center" style="border:1px solid black"><strong>Item Color</strong></td>
                <td align="center" style="border:1px solid black"><strong>Qty per Unit</strong></td>
                <td style="border:1px solid black" align="center"><strong>WO Qty</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
				<?php
					if($show_comment==1){
					?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
				 <?}?>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item){
			$i++;
            $nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid, b.description, b.brand_supplier,b.item_color,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=1  and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_color order by bid ");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center"  style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <?
				echo $trim_group_library[$result_item[csf('trim_group')]]."<br/>";
				echo implode(",",$booking_country_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][1]);
				?>
                </td>
				<td align="center"  style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
               
				<img  src='../../<? echo $trims_imge_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]]; ?>' height='50%' width='70%' />
				
                </td>
                <?
				$item_desctiption_total=0;
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription){
                ?>
                <td style="border:1px solid black"><?  if($result_itemdescription[csf('description')]){ echo $result_itemdescription[csf('description')];} ?> </td>
                <td style="border:1px solid black"><? if($result_itemdescription[csf('brand_supplier')]){echo $result_itemdescription[csf('brand_supplier')];} ?> </td>
                <td style="border:1px solid black; text-align:left">
               <? echo $color_library[$result_itemdescription[csf('item_color')]]; ?>
                </td>
                <td style="border:1px solid black; text-align:left">
               <?
			   $calUom=$trims_qtyPerUnit_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]][2];
			   $calQty=explode("_",$trims_qtyPerUnit_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]][1]);
			   if($calUom && end($calQty)){
				   echo "1".$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]."=".prev($calQty)." ".$calUom;
			   }
			   ?>
                </td>
                <td style="border:1px solid black; text-align:right">
				<?
				//echo number_format($result_itemdescription[csf('cons')],4);
				echo number_format($result_itemdescription[csf('cons')],4);
				$item_desctiption_total += $result_itemdescription[csf('cons')] ;
				$trims_item_arr[$result_item[csf('trim_group')]][$result_itemdescription[csf('description')]][$order_uom_arr[$result_item[csf('trim_group')]]]['cons']+=$result_itemdescription[csf('cons')];
				$trims_item_arr[$result_item[csf('trim_group')]][$result_itemdescription[csf('description')]][$order_uom_arr[$result_item[csf('trim_group')]]]['amount']+=$result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
				?>
                </td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_text=$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
              
				<?php
					if($show_comment==1){
					?>
				<td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
                $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
				echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
				<?}?>
				
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="4"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold;"><? echo number_format($item_desctiption_total,4);  ?></td>
                <td style="border:1px solid black;  text-align:center"><? echo $uom_text; ?></td>
				<?php
					if($show_comment==1){
					?>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
				 <?}?>
            </tr>
            <?
            }
			if($show_comment==1){
            ?>
				<tr>
					<td align="right" style="border:1px solid black"  colspan="10"><strong>Total</strong></td>				
					<td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>				
				</tr>
			<?}?>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER GMTS COLOR END=========================================  -->


        <!--==============================================Size Sensitive START=========================================  -->
		<?
		$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=2 and status_active=1 and is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group  order by trim_group ");
		if(count($nameArray_item)>0)
		{
		$fileRefNos="";
		$refNo=rtrim($ref_no_arr[$nameArray_job_po_row[csf('job_no')]]['ref'],',');
		$refNo=implode(", ",array_unique(explode(",",$refNo)));
		if($refNo!='') $fileRefNos=", &nbsp; Internal. Ref.: ".$refNo;
		
		$fileNo=rtrim($ref_no_arr[$nameArray_job_po_row[csf('job_no')]]['file'],',');
		$fileNo=implode(", ",array_unique(explode(",",$fileNo)));
		if($fileNo!='') $fileRefNos.=", &nbsp; File No.: ".$fileNo;
        ?>

        <table border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="12" align="">
                 <table width="100%" style="table-layout: fixed;">
                <tr>
                <td ><strong>Size Sensitive (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo " Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]].$fileRefNos; ?></strong>, &nbsp;&nbsp;<span style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap; font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></span>
                </td>
                </tr>
                </table>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
				<td style="border:1px solid black"><strong>Images</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Brand/Supplier Ref.</strong> </td>
                <td align="center" style="border:1px solid black"><strong>Item Size</strong></td>
				<td align="center" style="border:1px solid black"><strong>GMTS Size</strong></td>
                <td align="center" style="border:1px solid black"><strong>Article No.</strong></td>
                <td style="border:1px solid black" align="center"><strong>Qty per Unit</strong></td>
                <td style="border:1px solid black" align="center"><strong>WO Qty.</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
				<?php
					if($show_comment==1){
					?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
				 <?}?>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
			$i++;
			$nameArray_item_description=sql_select( "SELECT a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid, b.description, b.brand_supplier,b.item_size,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount,b.article_number, b.gmts_sizes as gmt_size_id, b.po_break_down_id  
				from wo_booking_dtls a,  wo_trim_book_con_dtls b 
				where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and   a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.sensitivity=2 and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
				group by a.pre_cost_fabric_cost_dtls_id, b.description,b.article_number, b.brand_supplier,b.item_size, b.gmts_sizes, b.po_break_down_id order by bid");

			$article_number_data=sql_select( "SELECT c.size_number_id, c.article_number, c.job_no_mst, c.po_break_down_id
			from wo_booking_dtls a, wo_trim_book_con_dtls b, wo_po_color_size_breakdown c
			where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.sensitivity=2 and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and a.sensitivity=2 and c.job_no_mst=a.job_no  and c.job_no_mst=b.job_no and b.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
			group by c.size_number_id, c.article_number, c.job_no_mst, c.po_break_down_id");
			$article_numberArr=array();
			foreach ($article_number_data as $value)
			{
			    $article_numberArr[$value[csf('po_break_down_id')]][$value[csf('size_number_id')]]['article_number'].=$value[csf('article_number')].',';
			}
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i ; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <?
				echo $trim_group_library[$result_item[csf('trim_group')]]."<br/>";
				echo implode(",",$booking_country_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][2]);
				?>
                </td>
				<td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
               
				<img  src='../../<? echo $trims_imge_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]]; ?>' height='50%' width='70%' />
                </td>
                <?
                $item_desctiption_total=0;
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {

                ?>
                <td style="border:1px solid black"><? if($result_itemdescription[csf('description')]){ echo $result_itemdescription[csf('description')];}?> </td>
                <td style="border:1px solid black"><? if($result_itemdescription[csf('brand_supplier')]){echo $result_itemdescription[csf('brand_supplier')];} ?> </td>
                
                <td style="border:1px solid black; text-align:left">
              	<? echo $result_itemdescription[csf('item_size')];?>
                </td>
				<td style="border:1px solid black; text-align:left">
              	<? echo $size_library[$result_itemdescription[csf('gmt_size_id')]];?>
                </td>
                <td style="border:1px solid black"><?php 
                	$art_no=rtrim($article_numberArr[$result_itemdescription[csf('po_break_down_id')]][$result_itemdescription[csf('gmt_size_id')]]['article_number'],',');
                	//$art_nos=implode(",",array_unique(explode(",",$art_no)));

                	$art_no_arr=array_unique(explode(",",$art_no));    
				    $art_nos ="";
				    foreach($art_no_arr as $key => $values)
				    {
				        if($values!='no article')
				        {
				            if ($art_nos=="") 
				            {
				                $art_nos.= $values;
				            }
				            else 
				            {
				                $art_nos.= ','.$values;
				            }
				        }
				    }
                	echo $art_nos;
					//if($result_itemdescription[csf('article_number')]!="no article"){
					//echo $result_itemdescription[csf('article_number')]; } else { echo "-"; } ?> 
				</td>
                <td style="border:1px solid black; text-align:left">
               <?
			   $calUom=$trims_qtyPerUnit_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]][2];
			   $calQty=explode("_",$trims_qtyPerUnit_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]][1]);
			   if($calUom && end($calQty)){
				   echo "1".$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]."=".prev($calQty)." ".$calUom;
			   }
			   ?>
                </td>


                <td style="border:1px solid black; text-align:right">
				 <?
				 echo number_format($result_itemdescription[csf('cons')],4);
                $item_desctiption_total += $result_itemdescription[csf('cons')] ;
				$trims_item_arr[$result_item[csf('trim_group')]][$result_itemdescription[csf('description')]][$order_uom_arr[$result_item[csf('trim_group')]]]['cons']+=$result_itemdescription[csf('cons')];
				$trims_item_arr[$result_item[csf('trim_group')]][$result_itemdescription[csf('description')]][$order_uom_arr[$result_item[csf('trim_group')]]]['amount']+=$result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];

				?>
                </td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_text=$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
				<?php
				if($show_comment==1){
				?>
				<td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">

                <?
                $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
				echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
			    <?}?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="6"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold"><? echo number_format($item_desctiption_total,4);  ?></td>
                 <td style="border:1px solid black;  text-align:center"><? echo $uom_text; ?></td>
				 <?php
					if($show_comment==1){
					?>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
				 <?}?>
            </tr>
            <?
            }
			if($show_comment==1){
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="12"><strong>Total</strong></td>	
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>				 
            </tr>
			<?}?>
        </table>
        <br/>
        <?
		}
		?>

        <!--==============================================Size Sensitive END=========================================  -->

         <!--==============================================AS PER CONTRAST COLOR START=========================================  -->
		<?
		 $nameArray_item=sql_select( "select pre_cost_fabric_cost_dtls_id, trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=3  and status_active=1 and is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group order by trim_group ");
		if(count($nameArray_item)>0)
		{
		$fileRefNos="";
		$refNo=rtrim($ref_no_arr[$nameArray_job_po_row[csf('job_no')]]['ref'],',');
		$refNo=implode(", ",array_unique(explode(",",$refNo)));
		if($refNo!='') $fileRefNos=", &nbsp; Internal. Ref.: ".$refNo;
		
		$fileNo=rtrim($ref_no_arr[$nameArray_job_po_row[csf('job_no')]]['file'],',');
		$fileNo=implode(", ",array_unique(explode(",",$fileNo)));
		if($fileNo!='') $fileRefNos.=", &nbsp; File No.: ".$fileNo;
        ?>
        <table border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="12" align="">
                <table width="100%" style="table-layout: fixed;">
	                <tr>
		                <td ><strong>Contrast Color (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]].$fileRefNos; ?></strong>, &nbsp;&nbsp;<span style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap; font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></span>
		                </td>
	                </tr>
                </table>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
				<td style="border:1px solid black"><strong>Images</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Brand/Supplier Ref.</strong> </td>
                <td align="center" style="border:1px solid black"><strong>Item Color</strong></td>
                <td align="center" style="border:1px solid black"><strong>Gmts Color</strong></td>
                <td align="center" style="border:1px solid black"><strong>Qty per Unit</strong></td>
                <td style="border:1px solid black" align="center"><strong>WO Qty.</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
				<?php
					if($show_comment==1){
					?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
				 <?}?>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
				$gmtcolor_library=return_library_array( "select b.item_color,b.color_number_id from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=3  and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by trim_group ", "item_color", "color_number_id"  );

			$nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid,b.description, b.brand_supplier,b.item_color,b.color_number_id,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.sensitivity=3  and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_color,b.color_number_id order by bid ");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <?
				echo $trim_group_library[$result_item[csf('trim_group')]]."<br/>";
				echo implode(",",$booking_country_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][3]);
				?>
                </td>
				<td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
				<img  src='../../<? echo $trims_imge_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]]; ?>' height='50%' width='70%' />
                </td>
                <?
                $item_desctiption_total=0;
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {

                ?>
                <td style="border:1px solid black"><? if($result_itemdescription[csf('description')]){ echo $result_itemdescription[csf('description')];}?> </td>
                <td style="border:1px solid black"><? if($result_itemdescription[csf('brand_supplier')]){echo $result_itemdescription[csf('brand_supplier')];} ?> </td>
                <td style="border:1px solid black; text-align:right">
               <? echo $color_library[$result_itemdescription[csf('item_color')]]; ?>
                </td>
                <td style="border:1px solid black; text-align:left">
               <?
			   echo $color_library[$result_itemdescription[csf('color_number_id')]];
			   ?>
                </td>
               <td style="border:1px solid black; text-align:left">
               <?
			  // $calUom=$precost_arr[$nameArray_job_po_row[csf('job_no')]][$result_item[csf('trim_group')]]['cal_parameter_uom'];
			   //$calQty=explode("_",$precost_arr[$nameArray_job_po_row[csf('job_no')]][$result_item[csf('trim_group')]]['calculatorstring']);

			   $calUom=$trims_qtyPerUnit_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]][2];
			   $calQty=explode("_",$trims_qtyPerUnit_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]][1]);
			  // print_r($calQty);
			   if($calUom && end($calQty)){
				   echo "1".$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]."=".prev($calQty)." ".$calUom;
			   }
			   ?>
                </td>

                <td style="border:1px solid black; text-align:right">
				<?
				echo number_format($result_itemdescription[csf('cons')],4);
                $item_desctiption_total += $result_itemdescription[csf('cons')] ;
				$trims_item_arr[$result_item[csf('trim_group')]][$result_itemdescription[csf('description')]][$order_uom_arr[$result_item[csf('trim_group')]]]['cons']+=$result_itemdescription[csf('cons')];
				$trims_item_arr[$result_item[csf('trim_group')]][$result_itemdescription[csf('description')]][$order_uom_arr[$result_item[csf('trim_group')]]]['amount']+=$result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
				?>
                </td>
                 <td style="border:1px solid black; text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_text=$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
               
				 <?php
					if($show_comment==1){
					?>
				 <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
                $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
			 <?}?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="5"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold;"><? echo number_format($item_desctiption_total,4);  ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $uom_text; ?></td>
				<?php
					if($show_comment==1){
					?>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
				 <?}?>
            </tr>
            <?
            }
			if($show_comment==1){
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="11"><strong>Total</strong></td>			
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>				 
            </tr>
			<?}?>
        </table>
        <br/>
        <?
		}
		?>

        <!--==============================================AS PER CONTRAST COLOR END=========================================  -->

        <!--==============================================AS PER GMTS Color & SIZE START=========================================  -->
		<?
		$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=4 and status_active=1 and is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group order by trim_group");
	   if(count($nameArray_item)>0)
		{
		$fileRefNos="";
		$refNo=rtrim($ref_no_arr[$nameArray_job_po_row[csf('job_no')]]['ref'],',');
		$refNo=implode(", ",array_unique(explode(",",$refNo)));
		if($refNo!='') $fileRefNos=", &nbsp; Internal. Ref.: ".$refNo;
		
		$fileNo=rtrim($ref_no_arr[$nameArray_job_po_row[csf('job_no')]]['file'],',');
		$fileNo=implode(", ",array_unique(explode(",",$fileNo)));
		if($fileNo!='') $fileRefNos.=", &nbsp; File No.: ".$fileNo;
        ?>

        <table border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="14" align="">
                <table width="100%" style="table-layout: fixed;">
                <tr>
                <td ><strong>Color & size sensitive (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo " Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]].$fileRefNos; ?></strong>, &nbsp;&nbsp;<span style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap; font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></span></td>
                </tr>
                </table>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
				<td style="border:1px solid black"><strong>Image</strong> </td>

                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Brand/Supplier Ref.</strong> </td>
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <td style="border:1px solid black; width:170px;"><strong>Gmts Color</strong> </td>

                <td align="center" style="border:1px solid black"><strong>Item Size</strong></td>
				<td align="center" style="border:1px solid black"><strong>GMTS Size</strong></td>
                <td align="center" style="border:1px solid black"><strong>Article No.</strong></td>
                <td align="center" style="border:1px solid black"><strong>Qty per Unit</strong></td>
                <td style="border:1px solid black" align="center"><strong>WO Qty.</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
				<?php
					if($show_comment==1){
					?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
				 <?}?>
            </tr>
            <?
			 $nameArray_color_arr=sql_select( "SELECT a.trim_group,b.description,a.pre_cost_fabric_cost_dtls_id as f_dtlsid, b.item_color as item_color, b.item_size,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount,b.article_number from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=4 and b.requirment !=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by  a.trim_group,b.description,a.pre_cost_fabric_cost_dtls_id,b.article_number,b.item_color,b.item_size order by b.item_size");

			foreach($nameArray_color_arr as $row)
            {
				$color_size_dtls_arr[$row[csf('f_dtlsid')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('description')]][$row[csf('item_size')]]['cons']=$row[csf('cons')];
				$color_size_dtls_arr[$row[csf('f_dtlsid')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('description')]][$row[csf('item_size')]]['amount']=$row[csf('amount')];
				$color_size_dtls_arr[$row[csf('f_dtlsid')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('description')]][$row[csf('item_size')]]['article_number']=$row[csf('article_number')];
			}
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
				$gmtcolor_library=return_library_array( "select b.item_color,b.color_number_id from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.sensitivity=4    and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by trim_group ", "item_color", "color_number_id"  );

			 //$nameArray_color=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid, b.color_number_id as gmt_color, b.item_color as color_number_id,b.item_size,b.description, b.brand_supplier,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and a.sensitivity=4 and b.requirment !=0 group by a.pre_cost_fabric_cost_dtls_id, b.color_number_id, b.item_color,b.item_size,b.description, b.brand_supplier order by bid ");
			 $nameArray_color=sql_select( "SELECT a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid, b.color_number_id as gmt_color, b.item_color as color_number_id,b.item_size,b.gmts_sizes,b.description, b.brand_supplier,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount, min(c.color_order) as color_order, min(c.size_order) as size_order, c.article_number 
			 from wo_booking_dtls a, wo_trim_book_con_dtls b,wo_po_color_size_breakdown c  
			 where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and b.po_break_down_id=c.po_break_down_id and b.color_number_id=c.color_number_id and b.gmts_sizes=c.size_number_id  and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and a.sensitivity=4 and b.requirment !=0 and c.id=b.color_size_table_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
			 group by a.pre_cost_fabric_cost_dtls_id, b.color_number_id, b.item_color,b.item_size,b.gmts_sizes,b.description, b.brand_supplier, c.article_number order by color_order,size_order"); //and  c.id=b.color_size_table_id

            $nameArray_item_description=sql_select( "select distinct uom from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=4 and trim_group=".$result_item[csf('trim_group')]." and pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and status_active=1 and is_deleted=0 order by trim_group ");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo   (count($nameArray_item_description)*count($nameArray_color)); ?>">
                <? echo $i ; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo (count($nameArray_item_description)*count($nameArray_color)); ?>">
                <?
				echo $trim_group_library[$result_item[csf('trim_group')]]."<br/>";
				echo implode(",",$booking_country_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][4]);
				?>
                </td>
				<td align="center" style="border:1px solid black" rowspan="<? echo (count($nameArray_item_description)*count($nameArray_color)); ?>">
				<img  src='../../<? echo $trims_imge_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]]; ?>' height='50%' width='70%' />
                </td>
                <?



				$item_desctiption_total=0;
                $total_amount_as_per_gmts_color=0;
				foreach($nameArray_color as $result_color)
                {
					$item_cons=$color_size_dtls_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][$result_item[csf('trim_group')]][$result_color[csf('color_number_id')]][$result_color[csf('description')]][$result_color[csf('item_size')]]['cons'];
					$item_amount=$color_size_dtls_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][$result_item[csf('trim_group')]][$result_color[csf('color_number_id')]][$result_color[csf('description')]][$result_color[csf('item_size')]]['amount'];
					$article_number=$color_size_dtls_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][$result_item[csf('trim_group')]][$result_color[csf('color_number_id')]][$result_color[csf('description')]][$result_color[csf('item_size')]]['article_number'];
					?>

					<td style="border:1px solid black" ><? if($result_color[csf('description')]){echo $result_color[csf('description')];} ?> </td>
					<td style="border:1px solid black" ><? if($result_color[csf('brand_supplier')]){echo $result_color[csf('brand_supplier')]; }?> </td>
                    <td style="border:1px solid black"><? echo $color_library[$result_color[csf('color_number_id')]]; ?> </td>
					
                    <td style="border:1px solid black"><? echo $color_library[$result_color[csf('gmt_color')]]; //echo $color_library[$gmtcolor_library[$result_color[csf('color_number_id')]]]; ?> </td>
					<td style="border:1px solid black; text-align:left">
					<? echo $result_color[csf('item_size')]; ?>
					</td>
					<td style="border:1px solid black; text-align:left">
					<? echo $size_library[$result_color[csf('gmts_sizes')]]; ?>
					</td>
					<td style="border:1px solid black; text-align:left">
					<? if ($result_color[csf('article_number')]!='no article') {
						echo $result_color[csf('article_number')];
					}  ?>
					</td>
                    <td style="border:1px solid black; text-align:left">
				   <?
                   //$calUom=$precost_arr[$nameArray_job_po_row[csf('job_no')]][$result_item[csf('trim_group')]]['cal_parameter_uom'];
				   //$calQty=explode("_",$precost_arr[$nameArray_job_po_row[csf('job_no')]][$result_item[csf('trim_group')]]['calculatorstring']);

				   $calUom=$trims_qtyPerUnit_arr[$result_color[csf('pre_cost_fabric_cost_dtls_id')]][2];
			       $calQty=explode("_",$trims_qtyPerUnit_arr[$result_color[csf('pre_cost_fabric_cost_dtls_id')]][1]);
				   if($calUom && end($calQty)){
					   echo "1".$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]."=".prev($calQty)." ".$calUom;
				   }
                   ?>
                    </td>

					<td style="border:1px solid black; text-align:right">
					<?
					echo  number_format($item_cons,4);
					$trims_item_arr[$result_item[csf('trim_group')]][$result_color[csf('description')]][$order_uom_arr[$result_item[csf('trim_group')]]]['cons']+=$item_cons;
					$trims_item_arr[$result_item[csf('trim_group')]][$result_color[csf('description')]][$order_uom_arr[$result_item[csf('trim_group')]]]['amount']+=$item_amount;
					$item_desctiption_total += $item_cons ;
					//echo number_format($item_desctiption_total,2);
					?>
                    </td>
					<td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_text=$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]];  ?></td>
					<?php
					if($show_comment==1){
					?>
					<td style="border:1px solid black; text-align:right">
					<?
					echo number_format($result_color[csf('rate')],4);
					/*$rate =$result_color[csf('amount')]/$result_color[csf('cons')];
					echo number_format($rate,4); */
					?>
                     </td>
					<td style="border:1px solid black; text-align:right">
					<?
					//$amount_as_per_gmts_color = $item_desctiption_total*  $result_color[csf('rate')];
					$amount_as_per_gmts_color =$item_amount;
					echo number_format($amount_as_per_gmts_color,4);
					$total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
					?>
					</td>
					 <?}?>
				</tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="11"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold;"><? echo number_format($item_desctiption_total,4);  ?></td>
                <td style="border:1px solid black;  text-align:center"><? echo $uom_text; ?></td>
				<?php
					if($show_comment==1){
					?>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
				 <?}?>
            </tr>
            <?
            }
			if($show_comment==1){
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="14"><strong>Total</strong></td>				
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>				
            </tr>
			<?}?>
        </table>
        <br/>
       <?
		}
	   ?>
        <!--==============================================AS PER Color & SIZE  END=========================================  -->



         <!--==============================================NO NENSITIBITY START=========================================  -->
		<?
       //$nameArray_item=sql_select( "select distinct trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and sensitivity=0 order by trim_group ");
		$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=0 and status_active=1 and is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group order by trim_group ");
        //$nameArray_color=sql_select( "select distinct b.color_number_id from wo_trims_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.sensitivity=1");
		//$nameArray_color= array();
		if(count($nameArray_item)>0)
		{
		$fileRefNos="";
		$refNo=rtrim($ref_no_arr[$nameArray_job_po_row[csf('job_no')]]['ref'],',');
		$refNo=implode(", ",array_unique(explode(",",$refNo)));
		if($refNo!='') $fileRefNos=", &nbsp; Internal. Ref.: ".$refNo;
		
		$fileNo=rtrim($ref_no_arr[$nameArray_job_po_row[csf('job_no')]]['file'],',');
		$fileNo=implode(", ",array_unique(explode(",",$fileNo)));
		if($fileNo!='') $fileRefNos.=", &nbsp; File No.: ".$fileNo;
        ?>
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="9" align="">
                <table width="100%" style="table-layout: fixed;">
                <tr>
                <td ><strong>NO sensitive (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]]; ?></strong>,&nbsp;&nbsp;<span style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap; font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]).$fileRefNos; ?></span></td>
                </tr>
                </table>
                 </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
				<td style="border:1px solid black"><strong>Image</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
               
                <td style="border:1px solid black"><strong>Brand/Supplier Ref.</strong> </td>
                <td align="center" style="border:1px solid black"><strong>Article No</strong></td>
                 <td style="border:1px solid black"><strong>Item Color</strong> </td>
                 <td align="center" style="border:1px solid black"><strong>Qty per Unit</strong></td>
                <td align="center" style="border:1px solid black"><strong> Qnty</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
					<?php
					if($show_comment==1){
					?>
      	          <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                  <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
				  <?}?>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;

				$nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_color,avg(b.rate) as rate, sum(b.amount) as amount,b.article_number from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and a.sensitivity=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.description, b.article_number,b.brand_supplier,b.item_color");

            //$nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 and trim_group=".$result_item[csf('trim_group')]." order by trim_group ");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">

                <?
				echo $trim_group_library[$result_item[csf('trim_group')]]."<br/>";
				echo implode(",",$booking_country_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][0]);

				?>
                </td>
				<td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">

				<img  src='../../<? echo $trims_imge_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]]; ?>' height='50%' width='70%' />
				</td>
                <?
                $color_tatal=0;
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $item_desctiption_total=0;
                ?>
                <td style="border:1px solid black"><? if($result_itemdescription[csf('description')]){ echo $result_itemdescription[csf('description')];} ?> </td>
                <td style="border:1px solid black"><? if($result_itemdescription[csf('brand_supplier')]){echo $result_itemdescription[csf('brand_supplier')];} ?> </td>
                <td style="border:1px solid black">
				<?
				if($result_itemdescription[csf('article_number')]!="no article"){
					echo $result_itemdescription[csf('article_number')];
				}else{
					echo "-";
				}
				?>
                </td>
                
                <td style="border:1px solid black"><? echo $color_library[$result_itemdescription[csf('item_color')]]; ?> </td>
                <td style="border:1px solid black; text-align:left">
               <?
			   //$calUom=$precost_arr[$nameArray_job_po_row[csf('job_no')]][$result_item[csf('trim_group')]]['cal_parameter_uom'];
			   //$calQty=explode("_",$precost_arr[$nameArray_job_po_row[csf('job_no')]][$result_item[csf('trim_group')]]['calculatorstring']);

			   $calUom=$trims_qtyPerUnit_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]][2];
			   $calQty=explode("_",$trims_qtyPerUnit_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]][1]);
			   if($calUom && end($calQty)){
				   echo "1".$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]."=".prev($calQty)." ".$calUom;
			   }
			   ?>
                </td>
                <?
				if($db_type==0)
				        {
						$nameArray_color_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.trim_group=". $result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and  b.description='". $result_itemdescription[csf('description')]."' and b.brand_supplier='".$result_itemdescription[csf('brand_supplier')]."' and b.item_color='".$result_itemdescription[csf('item_color')]."'");
						}
						if($db_type==2)
				        {
						$nameArray_color_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trim_group=". $result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and nvl( b.description,0)=nvl('". $result_itemdescription[csf('description')]."',0) and nvl(b.brand_supplier,0)=nvl('".$result_itemdescription[csf('brand_supplier')]."',0) and nvl(b.item_color,0)=nvl('".$result_itemdescription[csf('item_color')]."',0)");

						}

                foreach($nameArray_color_size_qnty as $result_color_size_qnty)
                {
                ?>
                <td style="border:1px solid black; text-align:right">
                 <?
                if($result_color_size_qnty[csf('cons')]!= "")
                {
                echo number_format($result_color_size_qnty[csf('cons')],4);
                $item_desctiption_total += $result_color_size_qnty[csf('cons')] ;
                $color_tatal+=$result_color_size_qnty[csf('cons')];
				$trims_item_arr[$result_item[csf('trim_group')]][$result_itemdescription[csf('description')]][$order_uom_arr[$result_item[csf('trim_group')]]]['cons']+=$result_color_size_qnty[csf('cons')];
				$trims_item_arr[$result_item[csf('trim_group')]][$result_itemdescription[csf('description')]][$order_uom_arr[$result_item[csf('trim_group')]]]['amount']+=$result_color_size_qnty[csf('cons')]*$result_itemdescription[csf('rate')];
                }
                else echo "";
                ?>
                </td>
                <?
                }
                ?>

                <td style="border:1px solid black; text-align:center "><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_text=$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
					<?php
					if($show_comment==1){
					?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
                $amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
 				<?}?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="5"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold;">
                <?
                if($color_tatal !='')
                {
                echo number_format($color_tatal,4);
                }
                ?>
                </td>
                <td style="border:1px solid black;  text-align:center"><? echo $uom_text; ?></td>
				<?php
					if($show_comment==1){
					?>
                <td style="border:1px solid black; text-align:right"></td>

                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
				 <?}?>
            </tr>
            <?
            }
			if($show_comment==1){
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="11"><strong>Total</strong></td>				
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>					 
            </tr>
			<?}?>
        </table>
        <?
		//print_r($color_tatal);
		}
		}
		?>
			<br>
		<table border="1" align="left" class="rpt_table"  cellpadding="0" width="50%" cellspacing="0" rules="all" >
				<tr>
					<td style="border:1px solid black" align="center" colspan="6"><strong>Summary Report</strong> </td>
						
					</tr>
					<tr>
					<td style="border:1px solid black" align="center"><strong>Sl</strong> </td>
						<td style="border:1px solid black" align="center"><strong>Item Group</strong> </td>
						<td style="border:1px solid black" align="center"><strong>Description</strong> </td>
						<td style="border:1px solid black" align="center"><strong>Order Uom</strong> </td>
						
						<td style="border:1px solid black" align="center"><strong>Total Qnty</strong> </td>
						<? if($show_comment==1) {?>
						<td style="border:1px solid black" align="center"><strong>Total Amount</strong> </td>
						<? } ?>
					</tr>
					<?
					// print_r($trims_item_arr);
					$t=1;$total_item_cons=$total_item_amount=0;
					foreach($trims_item_arr as $trim_id=>$trim_data )
					{
						foreach($trim_data as $trimdesc =>$trim_desc )
						{
							foreach($trim_desc as $uom_id=>$val )
							{
					?>
					<tr>
						<td style="border:1px solid black"> <? echo $t; ?> </td>
							<td align="center" style="border:1px solid black"> <? echo $trim_group_library[$trim_id];?> </td>
							<td align="center" style="border:1px solid black"> <? echo $trimdesc;?> </td>
							<td align="center" style="border:1px solid black"> <? echo $unit_of_measurement[$uom_id];?> </td>
							<td align="right" style="border:1px solid black"> <? echo number_format($val['cons'],0);?> </td>
							<? if($show_comment==1) {?>
							<td align="right" style="border:1px solid black"> <? echo number_format($val['amount'],2);?> </td>
							<? } ?>
						</tr>
						<?
							$t++;
							$total_item_cons+=$val['cons'];
							$total_item_amount+=$val['amount'];
							}
					    	}
						}
						?>
					<tr>
					<? if($show_comment==1) {?>
					<td colspan="5" align="right"> <b>Total</b> </td>
					
					<td align="right"><b> <? echo number_format($total_item_amount,2);?></b> </td>
					<? } ?>
					</tr>

		</table>
        <!--==============================================NO NENSITIBITY END=========================================  -->
       &nbsp;
       <?
       $mcurrency="";
	   $dcurrency="";
	   if($currency_id==1)
	   {
		$mcurrency='Taka';
		$dcurrency='Paisa';
	   }
	   if($currency_id==2)
	   {
		$mcurrency='USD';
		$dcurrency='CENTS';
	   }
	   if($currency_id==3)
	   {
		$mcurrency='EURO';
		$dcurrency='CENTS';
	   }
	   ?>
       <table align="left"  width="100%" class="rpt_table"  border="1" cellpadding="0" cellspacing="0" rules="all">
       <tr style="border:1px solid black;">
                <th width="30%" style="border:1px solid black; text-align:left">Total Booking Amount</th><td width="70%" style="border:1px solid black; text-align:left"><? echo number_format($booking_grand_total,2);?></td>
            </tr>
            <tr style="border:1px solid black;">
                <th width="30%" style="border:1px solid black; text-align:left">Total Booking Amount (in word)</th><td width="70%" style="border:1px solid black;"><? echo number_to_words(def_number_format($booking_grand_total,2,""),$mcurrency, $dcurrency);?></td>
            </tr>
       </table>
         <br/> <br/>
        <table width="100%">
        <tr>

        <td width="49%">
	        <?
	        	echo get_spacial_instruction($txt_booking_no,'');//,273
	        ?>
    	</td>

    <td width="2%"></td>
      <?

	$lib_designation_arr=return_library_array(" select id,custom_designation from lib_designation","id","custom_designation");
	$user_lib_designation_arr=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
	$user_lib_name_arr=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");

	$mst_id=return_field_value("id as mst_id","wo_booking_mst","booking_no=$txt_booking_no","mst_id");
	 $approve_data_array=sql_select("select b.approved_by,min(b.approved_date) as approved_date from   approval_history b where b.mst_id=$mst_id and b.entry_form=8  group by  b.approved_by order by b.approved_by asc");
	 $unapprove_data_array=sql_select("select b.approved_by,b.approved_date,b.un_approved_reason,b.un_approved_date,b.approved_no from   approval_history b where b.mst_id=$mst_id and b.entry_form=8  order by b.approved_date,b.approved_by");


	?>
    <td width="49%">
     <br/> <br/>
  		<?
          if(count($approve_data_array)>0)
			{
 		?>
       <table  width="850" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
            <tr style="border:1px solid black;">
                <th colspan="5" style="border:1px solid black;">Approval Status</th>
                </tr>
                <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th>
				<th width="40%" style="border:1px solid black;">Name</th>
				<th width="30%" style="border:1px solid black;">Designation</th>
				<th width="27%" style="border:1px solid black;">Approval Date</th>

                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($approve_data_array as $row){
			?>
            <tr style="border:1px solid black;">
                <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
				<td width="40%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
				<td width="30%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
				<td width="27%" style="border:1px solid black;text-align:center"><? echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')]));?></td>

                </tr>
                <?
				$i++;
			}
				?>
            </tbody>
        </table>
		<?
		}
		?>
		<br>
		<?
		if(count($unapprove_data_array)>0)
		{
			//and approval_type=0
			$sql_unapproved=sql_select("select booking_id,approval_cause from fabric_booking_approval_cause where  entry_form=8  and is_deleted=0 and status_active=1 and booking_id=$mst_id");


			$unapproved_request_arr=array();
			foreach($sql_unapproved as $rowu)
			{
				if($rowu[csf('approval_cause')]!='')
				{
					$unapproved_request_arr[$rowu[csf('booking_id')]]=$rowu[csf('approval_cause')];
				}
			}
 		?>
       <table  width="850" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
            <tr style="border:1px solid black;">
                <th colspan="6" style="border:1px solid black;">Approval/Un Approval Status</th>
                </tr>
                <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th>
				<th width="30%" style="border:1px solid black;">Name</th>
				<th width="20%" style="border:1px solid black;">Designation</th>
				<th width="5%" style="border:1px solid black;">Approval Status</th>
				<th width="20%" style="border:1px solid black;">Reason For Un Approval</th>
				<th width="22%" style="border:1px solid black;"> Date</th>

                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($unapprove_data_array as $row){

			?>
            <tr style="border:1px solid black;">
                <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
				<td width="30%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
				<td width="20%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
				<td width="5%" style="border:1px solid black; text-align:center"><? echo 'Yes';?></td>
				<td width="20%" style="border:1px solid black;"><? echo '';?></td>
				<td width="22%" style="border:1px solid black;text-align:center"><? if($row[csf('approved_date')]!="") echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')])); else echo "";?></td>
             </tr>
				<?
				$i++;
				$un_approved_date= explode(" ",$row[csf('un_approved_date')]);
				$un_approved_date=$un_approved_date[0];
				if($db_type==0) //Mysql
				{
					if($un_approved_date=="" || $un_approved_date=="0000-00-00") $un_approved_date="";else $un_approved_date=$un_approved_date;
				}
				else
				{
					if($un_approved_date=="") $un_approved_date="";else $un_approved_date=$un_approved_date;
				}

				if($un_approved_date!="")
				{
				?>
			<tr style="border:1px solid black;">
                <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
				<td width="30%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
				<td width="20%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
				<td width="5%" style="border:1px solid black;text-align:center;"><? echo 'No';?></td>
				<td width="20%" style="border:1px solid black;text-align:center"><? echo $unapproved_request_arr[$mst_id];?></td>
				<td width="22%" style="border:1px solid black;text-align:center"><? if($row[csf('un_approved_date')]!="") echo date ("d-m-Y h:i:s",strtotime($row[csf('un_approved_date')])); else echo "";?></td>
              </tr>

                <?
				$i++;
				}

			}
				?>
            </tbody>
        </table>
		<?
		}
		?>

    </td>
    </tr>
    </table>

    </div>
     <br/>
    <div>
		<?
        	echo signature_table(161, $cbo_company_name, "1330px");
			echo "****".custom_file_name($txt_booking_no,$style_sting,$job_no);
        ?>
    </div>

     <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
    fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');

    </script>
	<?
	exit();
}

if($action=="show_trim_booking_report4")
{
	//extract($_REQUEST);
	extract(check_magic_quote_gpc($_REQUEST));
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$id_approved_id=str_replace("'","",$id_approved_id);
	$report_type=str_replace("'","",$report_type);
	$show_comment=str_replace("'","",$show_comment);

	$color_library=return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$size_library=return_library_array("select id, size_name from  lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	$company_library=return_library_array("select id, company_name from lib_company", "id", "company_name");
	$trim_group_library= return_library_array("select id, item_name from lib_item_group",'id','item_name');
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');

	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$order_uom_arr=return_library_array("select id,order_uom  from lib_item_group","id","order_uom");
	$deling_marcent_arr=return_library_array("select id,team_member_name from lib_mkt_team_member_info","id","team_member_name");
	$team_leader_arr=return_library_array("select id,team_leader_name from lib_marketing_team","id","team_leader_name");
	$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
	$lib_user=return_library_array("select id,user_full_name from user_passwd","id","user_full_name");

	$nameArray_approved=sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no='$txt_booking_no' and b.entry_form=8 and  a.status_active =1 and a.is_deleted=0");
	list($nameArray_approved_row)=$nameArray_approved;
	//$revised_no_data=sql_select("select a.revised_no  from wo_booking_mst a where  a.booking_no=$txt_booking_no and  a.status_active =1 and a.is_deleted=0");
	//list($revised_no_row)=$revised_no_data;
		$booking_grand_total=0; $currency_id="";

		$buyer_string=array(); $style_owner=array(); $job_no=array(); $style_ref=array(); $all_dealing_marcent=array(); $season=array(); $order_repeat_no=array(); $po_id_arr=array();$job_no_arr=array();

		$nameArray_buyer=sql_select( "select  a.style_ref_no, a.job_no, a.style_owner, a.buyer_name, a.dealing_marchant, a.factory_marchant, a.season, a.season_matrix, a.season_buyer_wise, a.order_repeat_no, b.po_break_down_id,a.JOB_NO_PREFIX_NUM,a.team_leader,a.style_description from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no='$txt_booking_no' and b.status_active =1 and b.is_deleted=0");

        foreach ($nameArray_buyer as $result_buy){
			$buyer_string[$result_buy[csf('buyer_name')]]=$buyer_name_arr[$result_buy[csf('buyer_name')]];
			$style_owner[$result_buy[csf('job_no')]]=$company_library[$result_buy[csf('style_owner')]];
			$job_no[$result_buy[csf('job_no')]]=$result_buy[csf('job_no')];
			$style_ref[$result_buy[csf('job_no')]]=$result_buy[csf('style_ref_no')];
			$style_desc[$result_buy[csf('job_no')]]=$result_buy[csf('style_description')];
			$all_dealing_marcent[$result_buy[csf('job_no')]]=$deling_marcent_arr[$result_buy[csf('dealing_marchant')]];
			$job_prefix_arr[$txt_booking_no][$result_buy[csf('JOB_NO_PREFIX_NUM')]]=$result_buy[csf('JOB_NO_PREFIX_NUM')];
			$job_no_arr[$txt_booking_no][$result_buy[csf('job_no')]]=$result_buy[csf('job_no')];
			$booking_style_ref[$txt_booking_no][$result_buy[csf('style_ref_no')]]=$result_buy[csf('style_ref_no')];
			$dealing_merchant_list[$txt_booking_no][$result_buy[csf('dealing_marchant')]]=$deling_marcent_arr[$result_buy[csf('dealing_marchant')]];
			$factory_merchant_list[$txt_booking_no][$result_buy[csf('factory_marchant')]]=$deling_marcent_arr[$result_buy[csf('factory_marchant')]];
			$team_leader_list[$txt_booking_no][$result_buy[csf('team_leader')]]=$team_leader_arr[$result_buy[csf('team_leader')]];
			//$season[$result_buy[csf('job_no')]]=$season_arr[$result_buy[csf('season_matrix')]];
			$season_matrix=$result_buy[csf('season_matrix')];
			$season_buyer_wise=$result_buy[csf('season_buyer_wise')];
			if($season_matrix!=0 && $season_buyer_wise==0 )
			{
				$season_matrix_con=$season_matrix;
			}
			else if($season_buyer_wise!=0 && $season_matrix==0)
			{
				$season_matrix_con=$season_buyer_wise;
			}
			$seasons_name.=$season_arr[$season_matrix_con].',';
			$order_rept_no.=$result_buy[csf('order_repeat_no')].',';
			$order_repeat_no[$result_buy[csf('order_repeat_no')]]=$result_buy[csf('order_repeat_no')];

			$po_id_arr[$result_buy[csf('po_break_down_id')]]=$result_buy[csf('po_break_down_id')];
		}
		$style_sting=implode(",",array_unique($style_ref));
		$job_no_str = "'" . implode( "','", $job_no ) . "'";
		$job_no=implode(",",$job_no);
		$seasons_names=rtrim($seasons_name,',');

		$seasons_names=implode(",",array_unique(explode(",",$seasons_names)));
		$poid_arr=array_unique($po_id_arr);

		$order_rept_no=rtrim($order_rept_no,',');
		$order_rept_no=implode(",",array_unique(explode(",",$order_rept_no)));
		$main_fabric_approved = sql_select("select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and a.job_no in (".$job_no_str.") and b.entry_form=7 and a.status_active =1 and a.is_deleted=0 order by b.id asc");


		$po_no=array(); $file_no=array(); $ref_no=array(); $po_quantity=array();
		$pub_shipment_date='';$int_ref_no='';$tot_po_quantity=0;$po_idss='';
		$nameArray_job=sql_select( "select b.job_no_mst,b.id,b.pub_shipment_date, b.po_number,b.grouping, b.file_no, sum(b.po_quantity) as po_quantity  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no='$txt_booking_no' and  a.status_active =1 and a.is_deleted=0 group by b.job_no_mst,b.id,b.pub_shipment_date, b.po_number,b.grouping, b.file_no ");
        foreach ($nameArray_job as $result_job){
			$po_no[$result_job[csf('job_no_mst')]][$result_job[csf('id')]]=$result_job[csf('po_number')];
			$file_no[$result_job[csf('id')]]=$result_job[csf('file_no')];
			$ref_no[$result_job[csf('id')]]=$result_job[csf('grouping')];
			$po_quantity[$result_job[csf('id')]]=$result_job[csf('po_quantity')];
		//	$tot_po_quantity+=$result_job[csf('po_quantity')];
			$job_ref_no[$result_job[csf('job_no_mst')]].=$result_job[csf('grouping')].',';
			$job_file_no[$result_job[csf('job_no_mst')]].=$result_job[csf('file_no')].',';
			$po_no_arr[$result_job[csf('job_no_mst')]]['po_id'].=$result_job[csf('id')].',';
			$pub_shipment_date.=$result_job[csf('pub_shipment_date')].',';
			$int_ref_no.=$result_job[csf('grouping')].',';
			if($po_idss=='') $po_idss=$result_job[csf('id')];else $po_idss.=",".$result_job[csf('id')];

		}
			//echo $po_idss.'DDDDDDDDD';
		$sql_job=sql_select( "select b.job_no_mst,b.id as po_id, b.po_quantity as po_quantity  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and  a.status_active =1 and a.is_deleted=0 and  b.status_active =1 and b.is_deleted=0 and b.id in(".$po_idss.") ");
		foreach ($sql_job as $row)
		{
			$job_po_qty_arr[$row[csf('job_no_mst')]][$row[csf('po_id')]]+=$row[csf('po_quantity')];
			$tot_po_quantity+=$row[csf('po_quantity')];
		}

        $nameArray=sql_select( "select a.booking_no, a.is_approved,a.pay_mode,a.buyer_id, a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.source, a.remarks, a.revised_no,a.inserted_by,a.insert_date,a.DELIVERY_ADDRESS from wo_booking_mst a where a.booking_no='$txt_booking_no' and a.status_active =1 and a.is_deleted=0");
		foreach( $nameArray as $row)
		{
			$varcode_booking_no=$row[csf('booking_no')];
			$booking_date=$row[csf('booking_date')];
			$delivery_date=$row[csf('delivery_date')];
			$pay_mode_id=$row[csf('pay_mode')];
			$supplier_id=$row[csf('supplier_id')];
			$currency_id=$row[csf('currency_id')];
			$buyer_id=$row[csf('buyer_id')];
			$exchange_rate=$row[csf('exchange_rate')];
			$attention=$row[csf('attention')];
			$remarks=$row[csf('remarks')];
			$revised_no=$row[csf('revised_no')];
			$source_id=$row[csf('source')];
			$is_approved=$row[csf('is_approved')];
			$delivery_add=$row[csf('DELIVERY_ADDRESS')];
		}
		$approved_array=sql_select("select b.approved_by,b.approved_no, b.approved_date, c.user_full_name, b.id from  wo_booking_mst a , approval_history b, user_passwd c where a.id=b.mst_id and b.approved_by=c.id and a.booking_no='$txt_booking_no' and b.entry_form=8 and  a.status_active =1 and a.is_deleted=0 order by b.id asc");

	 $sql_date_update=sql_select("SELECT a.updated_by, a.booking_no,a.update_date
				  FROM wo_booking_dtls a, wo_booking_dtls b
				 WHERE a.booking_no = b.booking_no AND a.update_date >= b.update_date
				 and a.booking_no='$txt_booking_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.update_date desc");
	 $sql_date_insert=sql_select("SELECT a.inserted_by, a.booking_no,a.insert_date
						  FROM wo_booking_dtls a, wo_booking_dtls b
						 WHERE a.booking_no = b.booking_no AND a.insert_date <= b.insert_date
						 and a.booking_no='$txt_booking_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.insert_date");
	

	
	?>
	    <table border="1" align="left" class="rpt_table container" cellpadding="0" width="100%" cellspacing="0" rules="all" style="padding-bottom: 10px">
   			<tr>
			   <td width="150px"  style="border-right:0" align="left"><? if($report_type==1)
                   {
                       if($link == 1)

                       {
                   ?>
                            <img  src='../../../<? echo $imge_arr[$cbo_company_name]; ?>' height='40%' width='50%' />

                   <?
                       }
                       else
                       {
                   ?>
                            <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='40%' width='50%' />
                   <?	}
                   }
                   else
                   { ?>
                     <img  src='../<? echo $imge_arr[$cbo_company_name]; ?>' height='40%' width='50%' />
                  <? }
                   ?></td>		
				   
		<?	$group_lib=sql_select("select b.group_name,b.address,a.company_name from lib_company a,lib_group b where a.group_id=b.id and a.id=$cbo_company_name");?>
		
		
			   <td width="200px" colspan="2" align="left"><b><?=$group_lib[0][csf('group_name')];?></b></td>		  
			   <td  colspan="2" align="left"><b>M&M DEPARTMENT</b></td>
			   <td  colspan="2" align="left"><b>PURCHASE ORDER <br>(CODE: MMD/M&M/DMF-09)</b></td>
			   <td   align="left"><b>BOOKING DATE :<?php echo change_date_format($booking_date); ?></b> </td>		   
			</tr>
		</table>
				<table border="1" align="left" class="rpt_table container"  cellpadding="0" width="100%" cellspacing="0" rules="all" style="padding-bottom: 10px">
				<tr>
				   <td colspan="2" align="left"><b>Factory Name: <?=$company_library[$cbo_company_name];?></b> </td>				   
				   <td  colspan="2" align="left"><b>SUB: Accessories Purchase Order</b></td>	
				   <td  colspan="2" align="left"><b>Booking No:</b></td>	
				   <td colspan="2"  align="left"><b><?=$txt_booking_no;?></b> </td>						   
				</tr>
				<tr>
				   <td width="100" colspan="8" align="left"><b>Head Office: </b>
				   <!-- House # 103, Northern Road, Baridhara DOHS, Dhaka. Tel:8413580, Fax: 8413579 -->
				   <?=$group_lib[0][csf('address')];?>
					 
					 </td>				   
				   					   
				</tr>
				<tr>
				   <td width="100" colspan="8" align="left"><b>Factory:</b> <?
                            $nameArray=sql_select( "select id,plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
                            foreach ($nameArray as $result){
                            ?>
                              <?  if($result[csf('plot_no')]!='') echo $result[csf('plot_no')]; else echo '';?> &nbsp;
                                <? echo $result[csf('level_no')];?> &nbsp;
                                 <? echo $result[csf('road_no')]; ?>  &nbsp;
                                <? echo $result[csf('block_no')];?>  &nbsp;
                                <? echo $result[csf('city')];?>  &nbsp;
                                <? echo $result[csf('zip_code')]; ?>  &nbsp;
                                 <?php echo $result[csf('province')]; ?>  &nbsp;
                                <? echo $country_arr[$result[csf('country_id')]]; ?> &nbsp;<br/>
                                <? echo $result[csf('email')];?>  &nbsp;
                                <? echo $result[csf('website')];
                                if($result[csf('plot_no')]!='')
                                {
                                    $plot_no=$result[csf('plot_no')];
                                }
                                if($result[csf('level_no')]!='')
                                {
                                    $level_no=$result[csf('level_no')];
                                }
                                if($result[csf('road_no')]!='')
                                {
                                    $road_no=$result[csf('road_no')];
                                }
                                if($result[csf('block_no')]!='')
                                {
                                    $block_no=$result[csf('block_no')];
                                }
                                if($result[csf('city')]!='')
                                {
                                    $city=$result[csf('city')];
                                }
                                $company_address[$result[csf('id')]]=$plot_no.'&nbsp'.$level_no.'&nbsp'.$road_no.'&nbsp'.$block_no.'&nbsp'.$city;
                            }
                        ?></td>		   				  
				</tr>
				<tr>
				   <td width="100" align="left"> <b>To :</b> </td>
				
				   <td width="350" colspan="3" align="left"> <b><?
                    if($pay_mode_id==5 || $pay_mode_id==3){
                        echo $company_library[$supplier_id];
                    }
                    else{
                        echo $supplier_name_arr[$supplier_id];
                    }
                    ?></b></td>
				 <td width="100" colspan="2"align="left"><b>Job No. :</b> </td>	
				 <td width="200" colspan="2" align="left"><b><?=implode(",",$job_no_arr[$txt_booking_no]);?></b></td>	
				 
				</tr>
				<tr>
				   <td width="100" align="left"><b>  Attn.  :</b></td>
				   <td width="150" colspan="3" align="left"><b><? echo $attention;     ?></b></td>
				   <td width="150" colspan="2" align="left"> <b>Buyer’s Name:</b></td>	
				   <td width="200" colspan="2" align="left"><b><? echo $buyer_name_arr[$buyer_id]; ?></b></td>	
				 
				</tr>
				<tr>
				   <td width="100" align="left"><b> Team Leader  :</b></td>
				   <td width="350" colspan="3" align="left"><b><?=implode(",",$team_leader_list[$txt_booking_no]);?></b></td>
				   <td width="100" colspan="2" align="left"><b>Style Ref  :</b></td>	
				   <td width="200" colspan="2" align="left"><b><?=implode(",",$booking_style_ref[$txt_booking_no]);?> </b></td>	
				</tr>
				<tr>
				   <td width="100" align="left"><b>Dealing Merchant  :</b></td>
				   <td width="350" colspan="3" align="left"><b><?=implode(",",$dealing_merchant_list[$txt_booking_no]);?> </b></td>
				   <td width="100" colspan="2" align="left"><b>Delivery Place   :</b></td>	
				   <td width="200" colspan="2" align="left"><b><?=$delivery_add;?> </b></td>	
				</tr>
				<tr>
				  
				   <td width="100"  align="left"><b>Factory Merchant  :</b></td>
				   <td width="200" colspan="3" align="left"><b><?=implode(",",$factory_merchant_list[$txt_booking_no]);?></b></td>
				   <td width="100" colspan="2" align="left"><b>Delivery Date   :</b></td>	
				   <td width="200" colspan="2" align="left"><b><?= change_date_format($delivery_date,'dd-mm-yyyy','-') ;?> </b></td>	
				</tr>

          	</table>


          <!--==============================================AS PER GMTS COLOR START=========================================  -->
		<?

		$precost_arr=array();
		$trims_qtyPerUnit_arr=array();

		$precost_sql=sql_select("select a.id, a.job_no,a.trim_group,a.calculatorstring,a.remark, c.cal_parameter from wo_pre_cost_trim_cost_dtls a,wo_booking_dtls b, lib_item_group c where a.job_no=b.job_no and a.trim_group=b.trim_group and a.trim_group=c.id and b.booking_no='$txt_booking_no' and a.id=b.pre_cost_fabric_cost_dtls_id and  b.status_active =1 and b.is_deleted=0");
        $calUom="";
		foreach($precost_sql as $precost_row){
			if($precost_row[csf('cal_parameter')]==1){
			   $calUom="Mtr";
			   $trims_qtyPerUnit_arr[$precost_row[csf('id')]][1]=$precost_row[csf('calculatorstring')];
			   $trims_qtyPerUnit_arr[$precost_row[csf('id')]][2]=$calUom;
			}
			else if($precost_row[csf('cal_parameter')]==2){
			   $calUom="Pcs";
			}
			else if($precost_row[csf('cal_parameter')]==3){
			   $calUom="Pcs";
			}
			else if($precost_row[csf('cal_parameter')]==4){
			   $calUom="Pcs";
			}
			else if($precost_row[csf('cal_parameter')]==5){
			   $calUom="Yds";
			   $trims_qtyPerUnit_arr[$precost_row[csf('id')]][1]=$precost_row[csf('calculatorstring')];
			   $trims_qtyPerUnit_arr[$precost_row[csf('id')]][2]=$calUom;
			}
			else if($precost_row[csf('cal_parameter')]==6){
			   $calUom="Yds";
			   $trims_qtyPerUnit_arr[$precost_row[csf('id')]][1]=$precost_row[csf('calculatorstring')];
			   $trims_qtyPerUnit_arr[$precost_row[csf('id')]][2]=$calUom;
			}
			else if($precost_row[csf('cal_parameter')]==7){
			   $calUom="Pcs";
			   $trims_qtyPerUnit_arr[$precost_row[csf('id')]][1]=$precost_row[csf('calculatorstring')];
			   $trims_qtyPerUnit_arr[$precost_row[csf('id')]][2]=$calUom;
			}
			else if($precost_row[csf('cal_parameter')]==8){
			   $calUom="Yds";
			   $trims_qtyPerUnit_arr[$precost_row[csf('id')]][1]=$precost_row[csf('calculatorstring')];
			   $trims_qtyPerUnit_arr[$precost_row[csf('id')]][2]=$calUom;
			}
			else{
				$calUom=0;
			}
			 $trims_remark_arr[$precost_row[csf('id')]]['remark']=$precost_row[csf('remark')];
		}
		$booking_country_arr=array();
		$nameArray_booking_country=sql_select( "select pre_cost_fabric_cost_dtls_id,sensitivity,country_id_string from wo_booking_dtls  where booking_no='$txt_booking_no' and  status_active =1 and is_deleted=0");
		foreach($nameArray_booking_country as $nameArray_booking_country_row){
			$country_id_string=explode(",",$nameArray_booking_country_row[csf('country_id_string')]);
			$tocu=count($country_id_string);
			for($cu=0;$cu<$tocu;$cu++){
				$booking_country_arr[$nameArray_booking_country_row[csf('pre_cost_fabric_cost_dtls_id')]][$nameArray_booking_country_row[csf('sensitivity')]][$country_id_string[$cu]]=$country_arr[$country_id_string[$cu]];
			}
		}

		$nameArray_job_po=sql_select( "select job_no from wo_booking_dtls  where booking_no='$txt_booking_no' and status_active =1 and is_deleted=0 group by job_no order by job_no ");
		foreach($nameArray_job_po as $nameArray_job_po_row){
		//$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls  where booking_no='$txt_booking_no' and  status_active =1 and is_deleted=0 and job_no='".$nameArray_job_po_row[csf('job_no')]."'   and sensitivity=1 group by pre_cost_fabric_cost_dtls_id,trim_group  order by trim_group ");
		$nameArray_item=sql_select("SELECT a.pre_cost_fabric_cost_dtls_id, a.trim_group from wo_booking_dtls a join lib_item_group b on b.id=a.trim_group where a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=1 and  a.status_active =1 and a.is_deleted=0 and b.rate_cal_parameter not in (2,14) group by a.pre_cost_fabric_cost_dtls_id, a.trim_group order by a.trim_group");
	    if(count($nameArray_item)>0){
			$po_ids=rtrim($po_no_arr[$nameArray_job_po_row[csf('job_no')]]['po_id']);
			$po_ids=array_unique(explode(",",$po_ids));
			$ref_nos=rtrim($job_ref_no[$nameArray_job_po_row[csf('job_no')]],',');
			$ref_nos=implode(",",array_unique(explode(",",$ref_nos)));
			$file_nos=rtrim($job_file_no[$nameArray_job_po_row[csf('job_no')]],',');
			$file_nos=implode(",",array_unique(explode(",",$file_nos)));
			$po_no_qty=0;
			$job_no=$nameArray_job_po_row[csf('job_no')];
			foreach($po_ids as $poid)
			{
				$po_no_qty+=$job_po_qty_arr[$job_no][$poid];//$po_quantity[$poid]; 
			}
        ?>

        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="11" align="">
                <table width="100%" style="table-layout: fixed;">
                <tr>
                <td width="60%" align="left"><strong>As Per Garments Color (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style Desc:".$style_desc[$nameArray_job_po_row[csf('job_no')]]; if($file_nos!='' || $file_nos!=0 ) ; echo " &nbsp;  Po Qty.:&nbsp;".$po_no_qty; ?></strong></td>
                <td width="40%" style="word-break:break-all; font-weight:bold;">Po No: <? echo implode(", ",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
                </tr>
                </table>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
             
                <td align="center" style="border:1px solid black"><strong>Item Color</strong></td>
                <td align="center" style="border:1px solid black"><strong>Qty per Unit</strong></td>
                <td style="border:1px solid black" align="center"><strong>Qty</strong></td>
                <td style="border:1px solid black" align="center"><strong>UNIT</strong></td>
				  <? if($show_comment==1) {?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>

                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
				<td style="border:1px solid black" align="center"><strong>Currency</strong></td>

                <td style="border:1px solid black" align="center"><strong>Remarks</strong></td>
                <? }?>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item){
			$i++;
            $nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid, b.description, b.brand_supplier,b.item_color,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.rate*b.requirment) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=1  and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_color order by bid ");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center"  style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <?
				echo $trim_group_library[$result_item[csf('trim_group')]]."<br/>";
				echo implode(",",$booking_country_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][1]);
				?>
                </td>
                <?
				$item_desctiption_total=0;
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription){
                ?>
                <td style="border:1px solid black"><?  if($result_itemdescription[csf('description')]){ echo $result_itemdescription[csf('description')];} ?> </td>

                <td style="border:1px solid black; text-align:left">
               <? echo $color_library[$result_itemdescription[csf('item_color')]]; ?>
                </td>
                <td style="border:1px solid black; text-align:left">
               <?
			   $calUom=$trims_qtyPerUnit_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]][2];
			   $calQty=explode("_",$trims_qtyPerUnit_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]][1]);
			   if($calUom && end($calQty)){
				   echo "1".$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]."=".prev($calQty)." ".$calUom;
			   }
			   ?>
                </td>
                <td style="border:1px solid black; text-align:right">
				<?

				echo number_format($result_itemdescription[csf('cons')],4);
				$item_desctiption_total += $result_itemdescription[csf('cons')] ;
				?>
                </td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_text=$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
				 <? if($show_comment==1) {?>
                <td style="border:1px solid black; text-align:right" title="Avg Rate"><? echo number_format( $result_itemdescription[csf('amount')]/$result_itemdescription[csf('cons')],4); ?> </td>

                <td style="border:1px solid black; text-align:right">
                <?
                $amount_as_per_gmts_color = $result_itemdescription[csf('amount')];
				// $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
				echo number_format($amount_as_per_gmts_color,2);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
				$trims_remark=$trims_remark_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]]['remark'];
                ?>
                </td>
				<td style="border:1px solid black; text-align:right"><p><? echo $currency[$currency_id]; ?> </p></td>
                 <td style="border:1px solid black; text-align:right"><p><? echo $trims_remark; ?> </p></td>
                 <? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="4"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold;"><?

					echo number_format($item_desctiption_total ,4);
				 ?></td>
                <td style="border:1px solid black;  text-align:center"><? echo $uom_text; ?></td>
				 <? if($show_comment==1) {?>
                <td style="border:1px solid black; text-align:right"></td>
				<td style="border:1px solid black; text-align:right"></td>

                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>

                <td>&nbsp;  </td>
                <? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="8"><strong>Total</strong></td>
				  <? if($show_comment==1) {?>
				 <td> </td>

                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>

                <td>&nbsp; </td>
				<td>&nbsp; </td>
                <? } ?>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER GMTS COLOR END=========================================  -->


        <!--==============================================Size Sensitive START=========================================  -->
		<?
		//$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls  where booking_no='$txt_booking_no' and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=2 and  status_active =1 and  is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group  order by trim_group ");
		$nameArray_item=sql_select("SELECT a.pre_cost_fabric_cost_dtls_id, a.trim_group from wo_booking_dtls a join lib_item_group b on b.id=a.trim_group where a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=2 and  a.status_active =1 and a.is_deleted=0 and b.rate_cal_parameter not in (2,14) group by a.pre_cost_fabric_cost_dtls_id, a.trim_group order by a.trim_group");
		if(count($nameArray_item)>0)
		{
			$po_ids=rtrim($po_no_arr[$nameArray_job_po_row[csf('job_no')]]['po_id']);
			$po_ids=array_unique(explode(",",$po_ids));
			$ref_nos=rtrim($job_ref_no[$nameArray_job_po_row[csf('job_no')]],',');
			$ref_nos=implode(",",array_unique(explode(",",$ref_nos)));
			$file_nos=rtrim($job_file_no[$nameArray_job_po_row[csf('job_no')]],',');
			$file_nos=implode(",",array_unique(explode(",",$file_nos)));

			$po_no_qty=0;
			$job_no=$nameArray_job_po_row[csf('job_no')];
			foreach($po_ids as $poid)
			{
				$po_no_qty+=$job_po_qty_arr[$job_no][$poid];
			}
        ?>

        <table border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="11" align="">
                 <table width="100%" style="table-layout: fixed;">
                <tr>
                <td width="60%"><strong>Size Sensitive (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style Desc:".$style_desc[$nameArray_job_po_row[csf('job_no')]]; echo "&nbsp;&nbsp; Po Qty..:".$po_no_qty; ?></strong></td>
                <td width="40%" style="word-break:break-all; font-weight:bold;">Po No: <? echo implode(", ",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
                </tr>
                </table>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>              
                <td align="center" style="border:1px solid black"><strong>Item Size</strong></td>
                <td align="center" style="border:1px solid black"><strong>Qty per Unit</strong></td>
                <td style="border:1px solid black" align="center"><strong>Qty.</strong></td>
                <td style="border:1px solid black" align="center"><strong>UNIT</strong></td>
				 <? if($show_comment==1) {?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>

                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
				<td style="border:1px solid black" align="center"><strong>Currency</strong></td>

                <td style="border:1px solid black" align="center"><strong>Remarks</strong></td>
                <? } ?>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
			$i++;
			$nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid, b.description, b.brand_supplier,b.item_size,b.article_number,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and   a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.sensitivity=2 and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_size,b.article_number order by bid");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i ; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <?
				echo $trim_group_library[$result_item[csf('trim_group')]]."<br/>";
				echo implode(",",$booking_country_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][2]);
				?>
                </td>
                <?
                $item_desctiption_total=0;
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {

                ?>
                <td style="border:1px solid black"><? if($result_itemdescription[csf('description')]){ echo $result_itemdescription[csf('description')];}?> </td>             
                <td style="border:1px solid black; text-align:left">
              <? echo $result_itemdescription[csf('item_size')];?>
                </td>
                <td style="border:1px solid black; text-align:left">
               <?
			   $calUom=$trims_qtyPerUnit_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]][2];
			   $calQty=explode("_",$trims_qtyPerUnit_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]][1]);
			   if($calUom && end($calQty)){
				   echo "1".$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]."=".prev($calQty)." ".$calUom;
			   }
			   ?>
                </td>


                <td style="border:1px solid black; text-align:right">
				<?

					  echo number_format($result_itemdescription[csf('cons')],4);

                $item_desctiption_total += $result_itemdescription[csf('cons')] ;
				?>
                </td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_text=$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
				<? if($show_comment==1) {?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>

                <td style="border:1px solid black; text-align:right">

                <?
                $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
				echo number_format($amount_as_per_gmts_color,2);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
				$trims_remark=$trims_remark_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]]['remark'];
                ?>
                </td>
				<td style="border:1px solid black; text-align:right"><p><? echo $currency[$currency_id]; ?> </p></td>
                <td style="border:1px solid black; text-align:right"><p><? echo $trims_remark; ?> </p></td>
                <? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="3"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold"><?

					echo number_format($item_desctiption_total,4);
				 ?></td>
                 <td style="border:1px solid black;  text-align:center"><? echo $uom_text; ?></td>
				 <? if($show_comment==1) {?>
                <td style="border:1px solid black; text-align:right"></td>
		

                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>

                <td>&nbsp; </td>
				<td>&nbsp; </td>
                <? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="7"><strong>Total</strong></td>
				 <? if($show_comment==1) {?>
					<td> </td>

                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>

                <td>&nbsp; </td>
				<td>&nbsp; </td>
                <? } ?>
            </tr>
        </table>
        <br/>
        <?
		}
		?>

        <!--==============================================Size Sensitive END=========================================  -->

         <!--==============================================AS PER CONTRAST COLOR START=========================================  -->
		<?
		 //$nameArray_item=sql_select( "select pre_cost_fabric_cost_dtls_id, trim_group from wo_booking_dtls  where booking_no='$txt_booking_no' and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=3 and  status_active =1 and is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group order by trim_group ");
		$nameArray_item=sql_select("SELECT a.pre_cost_fabric_cost_dtls_id, a.trim_group from wo_booking_dtls a join lib_item_group b on b.id=a.trim_group where a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=3 and  a.status_active =1 and a.is_deleted=0 and b.rate_cal_parameter not in (2,14) group by a.pre_cost_fabric_cost_dtls_id, a.trim_group order by a.trim_group");
		if(count($nameArray_item)>0)
		{
			$po_ids=rtrim($po_no_arr[$nameArray_job_po_row[csf('job_no')]]['po_id']);
			$po_ids=array_unique(explode(",",$po_ids));
			$ref_nos=rtrim($job_ref_no[$nameArray_job_po_row[csf('job_no')]],',');
			$ref_nos=implode(",",array_unique(explode(",",$ref_nos)));
			$file_nos=rtrim($job_file_no[$nameArray_job_po_row[csf('job_no')]],',');
			$file_nos=implode(",",array_unique(explode(",",$file_nos)));

			$po_no_qty=0;
			$job_no=$nameArray_job_po_row[csf('job_no')];
			foreach($po_ids as $poid)
			{
				$po_no_qty+=$job_po_qty_arr[$job_no][$poid];
			}
        ?>
        <table border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="12" align="">
                <table width="100%" style="table-layout: fixed;">
                <tr>
                <td width="60%"><strong>Contrast Color (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style Desc:".$style_desc[$nameArray_job_po_row[csf('job_no')]];echo " &nbsp;&nbsp;Po Qty.:&nbsp;".$po_no_qty; ?></strong></td>
                <td width="40%" style="word-break:break-all; font-weight:bold;">Po No: <? echo implode(", ",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
                </tr>
                </table>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>

                <td style="border:1px solid black"><strong>Item Description</strong> </td>
				<td align="center" style="border:1px solid black"><strong>Gmts Color</strong></td>
                <td align="center" style="border:1px solid black"><strong>Item Color</strong></td>
               
                <td align="center" style="border:1px solid black"><strong>Qty per Unit</strong></td>
                <td style="border:1px solid black" align="center"><strong>Qty.</strong></td>
                <td style="border:1px solid black" align="center"><strong>UNIT</strong></td>
				 <? if($show_comment==1) {?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>

                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
				<td style="border:1px solid black" align="center"><strong>Currency</strong></td>

                <td style="border:1px solid black" align="center"><strong>Remarks</strong></td>
                <? } ?>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
				$gmtcolor_library=return_library_array( "select b.item_color,b.color_number_id from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=3  and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by trim_group ", "item_color", "color_number_id"  );

			$nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid,b.description, b.brand_supplier,b.item_color,b.color_number_id,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.sensitivity=3  and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_color,b.color_number_id order by bid ");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <?
				echo $trim_group_library[$result_item[csf('trim_group')]]."<br/>";
				echo implode(",",$booking_country_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][3]);
				?>
                </td>
                <?
                $item_desctiption_total=0;
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {

                ?>
                <td style="border:1px solid black"><? if($result_itemdescription[csf('description')]){ echo $result_itemdescription[csf('description')];}?> </td>

                <td style="border:1px solid black; text-align:left">
               <?
			   echo $color_library[$result_itemdescription[csf('color_number_id')]];
			   ?>
                </td>
				<td style="border:1px solid black; text-align:left">
               <? echo $color_library[$result_itemdescription[csf('item_color')]]; ?>
                </td>
              
               <td style="border:1px solid black; text-align:left">
               <?
			   $calUom=$trims_qtyPerUnit_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]][2];
			   $calQty=explode("_",$trims_qtyPerUnit_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]][1]);
			   if($calUom && end($calQty)){
				   echo "1".$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]."=".prev($calQty)." ".$calUom;
			   }
			   ?>
                </td>

                <td style="border:1px solid black; text-align:right">
				<?
				echo number_format($result_itemdescription[csf('cons')],4);
                $item_desctiption_total += $result_itemdescription[csf('cons')] ;
				?>
                </td>
                 <td style="border:1px solid black; text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_text=$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
				 <? if($show_comment==1) {?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>

                <td style="border:1px solid black; text-align:right">
                <?
                $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,2);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
				$trims_remark=$trims_remark_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]]['remark'];
                ?>
                </td>
				<td style="border:1px solid black; text-align:right"><p><? echo $currency[$currency_id]; ?></p> </td>
                <td style="border:1px solid black; text-align:right"><p><? echo $trims_remark; ?></p> </td>
                <? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="4"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold;"><?
				echo number_format($item_desctiption_total,4);
				 ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $uom_text; ?></td>
				 <? if($show_comment==1) {?>
                <td style="border:1px solid black; text-align:right"></td>

                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>

                <td>&nbsp;  </td>
				<td>&nbsp;  </td>
                <? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="8"><strong>Total</strong></td>
				<? if($show_comment==1) {?>
				<td> </td>

                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>

                <td>&nbsp;  </td>
				<td>&nbsp;  </td>
                <? } ?>
            </tr>
        </table>
        <br/>
        <?
		}
		?>

        <!--==============================================AS PER CONTRAST COLOR END=========================================  -->

        <!--==============================================AS PER GMTS Color & SIZE START=========================================  -->
		<?
		//$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls  where booking_no='$txt_booking_no' and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=4 and  status_active =1 and is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group order by trim_group");
		$nameArray_item=sql_select("SELECT a.pre_cost_fabric_cost_dtls_id, a.trim_group from wo_booking_dtls a join lib_item_group b on b.id=a.trim_group where a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=4 and  a.status_active =1 and a.is_deleted=0 and b.rate_cal_parameter not in (2,14) group by a.pre_cost_fabric_cost_dtls_id, a.trim_group order by a.trim_group");
	   if(count($nameArray_item)>0)
		{
			$po_ids=rtrim($po_no_arr[$nameArray_job_po_row[csf('job_no')]]['po_id']);
			$po_ids=array_unique(explode(",",$po_ids));

			$ref_nos=rtrim($job_ref_no[$nameArray_job_po_row[csf('job_no')]],',');
			$ref_nos=implode(",",array_unique(explode(",",$ref_nos)));
			$file_nos=rtrim($job_file_no[$nameArray_job_po_row[csf('job_no')]],',');
			$file_nos=implode(",",array_unique(explode(",",$file_nos)));
			$po_no_qty=0;
			$job_no=$nameArray_job_po_row[csf('job_no')];
			foreach($po_ids as $poid)
			{
				$po_no_qty+=$job_po_qty_arr[$job_no][$poid];
			}
        ?>

        <table border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="13" align="">
                <table width="100%" style="table-layout: fixed;">
                <tr>
                <td width="60%" ><strong>Color & size sensitive (<? echo "Job NO: ".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style Desc:".$style_desc[$nameArray_job_po_row[csf('job_no')]] ;echo "&nbsp;&nbsp;&nbsp;&nbsp;Po Qty.:&nbsp;".$po_no_qty;?></strong></td>
                <td width="40%" style="word-break:break-all; font-weight:bold;">Po No: <? echo implode(", ",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
                </tr>
                </table>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group </strong> </td>


                <td style="border:1px solid black"><strong>Item Description</strong> </td>
				<td style="border:1px solid black; width:170px;"><strong>Gmts Color</strong> </td>
                 <td style="border:1px solid black"><strong>Item Color</strong> </td>
          
                <td style="border:1px solid black;"><strong>Gmts Size</strong> </td>

                <td align="center" style="border:1px solid black"><strong>Item Size</strong></td>
                <td align="center" style="border:1px solid black"><strong>Qty per Unit</strong></td>
                <td style="border:1px solid black" align="center"><strong>Qty.</strong></td>
                <td style="border:1px solid black" align="center"><strong>UNIT</strong></td>
				<? if($show_comment==1) {?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>

                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
				<td style="border:1px solid black" align="center"><strong>Currency</strong></td>

                <td style="border:1px solid black" align="center"><strong>Remarks</strong></td>
                <? } ?>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;

            foreach($nameArray_item as $result_item)
            {

				$gmtcolor_library=return_library_array( "select b.item_color,b.color_number_id from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.sensitivity=4    and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by trim_group ", "item_color", "color_number_id"  );

			 $nameArray_color=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid, b.color_number_id as gmt_color, b.item_color as color_number_id,b.item_size,b.gmts_sizes,b.description, b.brand_supplier,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount, min(c.color_order) as color_order, min(c.size_order) as size_order,c.article_number from wo_booking_dtls a, wo_trim_book_con_dtls b,wo_po_color_size_breakdown c  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and b.po_break_down_id=c.po_break_down_id and b.color_number_id=c.color_number_id and b.gmts_sizes=c.size_number_id and  c.id=b.color_size_table_id and a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and a.sensitivity=4 and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.color_number_id, b.item_color,b.item_size,b.gmts_sizes,b.description, b.brand_supplier,c.article_number order by c.article_number,color_order,size_order");



            $nameArray_item_description=sql_select( "select distinct uom from wo_booking_dtls  where booking_no='$txt_booking_no' and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=4 and trim_group=".$result_item[csf('trim_group')]." and pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and  status_active =1 and is_deleted=0 order by trim_group ");

            if(count($nameArray_color)>0){
            	$i++; ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo   (count($nameArray_item_description)*count($nameArray_color)); ?>">
                <? echo $i ; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo (count($nameArray_item_description)*count($nameArray_color)); ?>">
                <?
				echo $trim_group_library[$result_item[csf('trim_group')]]."<br/>";
				echo implode(",",$booking_country_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][4]);
				?>
                </td>
                <?
				$item_desctiption_total=0;
                $total_amount_as_per_gmts_color=0;
                /*echo '<pre>';
            	print_r($nameArray_color);*/

				foreach($nameArray_color as $result_color)
                {
					?>

					<td style="border:1px solid black" ><? if($result_color[csf('description')]){echo $result_color[csf('description')];} ?> </td>
					<td style="border:1px solid black"><? echo $color_library[$result_color[csf('gmt_color')]];?> </td>
                    <td style="border:1px solid black"><? echo $color_library[$result_color[csf('color_number_id')]]; ?> </td>
                   
                    <td style="border:1px solid black; text-align:left">
					<? echo $size_library[$result_color[csf('gmts_sizes')]]; ?>
					</td>
					<td style="border:1px solid black; text-align:left">
					<? echo $result_color[csf('item_size')]; ?>
					</td>
                    <td style="border:1px solid black; text-align:left">
				   <?
				   $calUom=$trims_qtyPerUnit_arr[$result_color[csf('pre_cost_fabric_cost_dtls_id')]][2];
			       $calQty=explode("_",$trims_qtyPerUnit_arr[$result_color[csf('pre_cost_fabric_cost_dtls_id')]][1]);
				   if($calUom && end($calQty)){
					   echo "1".$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]."=".prev($calQty)." ".$calUom;
				   }
                   ?>
                    </td>

					<td style="border:1px solid black; text-align:right">
					<?
					echo number_format($result_color[csf('cons')],4);
					$item_desctiption_total += $result_color[csf('cons')] ;
					?>
                    </td>
					<td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_text=$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]];  ?></td>
					<? if($show_comment==1) {?>
					<td style="border:1px solid black; text-align:right">
					<?
					echo number_format($result_color[csf('rate')],4);
					?>
                     </td>

					<td style="border:1px solid black; text-align:right">
					<?
					$amount_as_per_gmts_color =$result_color[csf('amount')];
					echo number_format($amount_as_per_gmts_color,2);
					$total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
					$trims_remark=$trims_remark_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]]['remark'];
					?>
					</td>
					<td style="border:1px solid black;text-align:center"><? echo $currency[$currency_id]; ?> </td>
                    <td style="border:1px solid black;text-align:center"><? echo $trims_remark; ?> </td>
                    <? } ?>
				</tr>
            	<?
            	}
            	?>
            	<tr>
	                <td style="border:1px solid black;  text-align:right" colspan="8"><strong> Item Total</strong></td>
	                <td style="border:1px solid black;  text-align:right; font-weight:bold;"><?
					echo number_format($item_desctiption_total,4);  ?></td>
	                <td style="border:1px solid black;  text-align:center"><? echo $uom_text; ?></td>
					<? if($show_comment==1) {?>
	                <td style="border:1px solid black; text-align:right"></td>

	                <td style="border:1px solid black; text-align:right">
	                <?
	                echo number_format($total_amount_as_per_gmts_color,2);
	                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
	                ?>
	                </td>

	                <td>&nbsp;  </td>
					<td>&nbsp;  </td>
	                <? } ?>
            	</tr>
            <?
            }
        	}

            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<? echo ($show_comment==1) ? 11 : 9; ?>"><strong>Total</strong></td>
				<? if($show_comment==1) {?>

                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
				<td>&nbsp;  </td>
                <td>&nbsp;  </td>
                <? } else{ ?>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
                <? } ?>
            </tr>
        </table>
        <br/>
       <?
		}
	   ?>
        <!--==============================================AS PER Color & SIZE  END=========================================  -->

         <!--==============================================NO NENSITIBITY START=========================================  -->
		<?
		$nameArray_item=sql_select("SELECT a.pre_cost_fabric_cost_dtls_id, a.trim_group, b.rate_cal_parameter from wo_booking_dtls a join lib_item_group b on b.id=a.trim_group where a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=0 and  a.status_active =1 and a.is_deleted=0 and b.rate_cal_parameter not in (2,14) group by a.pre_cost_fabric_cost_dtls_id, a.trim_group, b.rate_cal_parameter order by a.trim_group");
		if(count($nameArray_item)>0)
		{
			$po_ids=rtrim($po_no_arr[$nameArray_job_po_row[csf('job_no')]]['po_id']);
			$po_ids=array_unique(explode(",",$po_ids));
			$ref_nos=rtrim($job_ref_no[$nameArray_job_po_row[csf('job_no')]],',');
			$ref_nos=implode(",",array_unique(explode(",",$ref_nos)));
			$file_nos=rtrim($job_file_no[$nameArray_job_po_row[csf('job_no')]],',');
			$file_nos=implode(",",array_unique(explode(",",$file_nos)));
			$po_no_qty=0;
			$job_no=$nameArray_job_po_row[csf('job_no')];
			foreach($po_ids as $poid)
			{
				//echo $poid.', ';
				$po_no_qty+=$job_po_qty_arr[$job_no][$poid];
			} 

			//$po_quantity[$result_job[csf('id')]];
			if($show_comment==1) $tdColspan=6; else $tdColspan=3;
        ?>
        <table border="1" align="left" class="rpt_table" cellpadding="0" width="1320" cellspacing="0" style="table-layout: fixed;"  rules="all" >
            <tr>
                <td colspan="5" width="645" style="word-break:break-all; "><strong>NO sensitive  (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style Desc:".$style_desc[$nameArray_job_po_row[csf('job_no')]]; echo " &nbsp;Po Qty.:&nbsp;".$po_no_qty; ?></strong></td>
                <td width="530" colspan="<?=$tdColspan;?>" style="word-break:break-all; font-weight:bold;">Po No: <? echo implode(", ",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
            </tr>
            <tr>
                <td width="25" style="border:1px solid black"><strong>Sl</strong> </td>
                <td width="120" style="border:1px solid black"><strong>Item Group</strong> </td>
                <td width="200" style="border:1px solid black"><strong>Item Description</strong> </td>
                <td width="150" style="border:1px solid black"><strong>Item Color</strong> </td>
                <td width="80" align="center" style="border:1px solid black"><strong>Qty per Unit</strong></td>
                <td width="100" align="center" style="border:1px solid black"><strong>Qty</strong></td>
                <td width="80" style="border:1px solid black" align="center"><strong>UOM</strong></td>
				<? if($show_comment==1) {?>
                <td width="80" style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td width="100" style="border:1px solid black" align="center"><strong>Amount</strong></td>
				<td width="220" style="border:1px solid black" align="center"><strong>Currency</strong></td>
                <td width="220" style="border:1px solid black" align="center"><strong>Remarks</strong></td>
                 <? } ?>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
				$nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_color,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and a.sensitivity=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_color");
				?>
				<tr>
					<td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>"><? echo $i; ?></td>
					<td align="center" style="word-break:break-all; border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
						<?
						echo $trim_group_library[$result_item[csf('trim_group')]]."<br/>";
						echo implode(",",$booking_country_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][0]);
						?>
					</td>
					<?
					$color_tatal=0;
					$total_amount_as_per_gmts_color=0;
					foreach($nameArray_item_description as $result_itemdescription)
					{
						$item_desctiption_total=0;
						?>
						<td style="word-break:break-all; border:1px solid black"><? if($result_itemdescription[csf('description')]){ echo $result_itemdescription[csf('description')];} ?> </td>
					
						<td style="word-break:break-all; border:1px solid black"><? echo $color_library[$result_itemdescription[csf('item_color')]]; ?> </td>
						<td style="word-break:break-all; border:1px solid black; text-align:left">
						<?
						$trims_remark=$trims_remark_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]]['remark'];
						$calUom=$trims_qtyPerUnit_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]][2];
						$calQty=explode("_",$trims_qtyPerUnit_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]][1]);
						if($calUom && end($calQty)){
						echo "1".$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]."=".prev($calQty)." ".$calUom;
						?>
						</td>
						<?
					}

					if($db_type==0)
					{
					 $nameArray_color_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trim_group=". $result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and  b.description='". $result_itemdescription[csf('description')]."' and b.brand_supplier='".$result_itemdescription[csf('brand_supplier')]."' and b.item_color='".$result_itemdescription[csf('item_color')]."'");
					}
					if($db_type==2)
					{
						$nameArray_color_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trim_group=". $result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and nvl( b.description,0)=nvl('". $result_itemdescription[csf('description')]."',0) and nvl(b.brand_supplier,0)=nvl('".$result_itemdescription[csf('brand_supplier')]."',0) and nvl(b.item_color,0)=nvl('".$result_itemdescription[csf('item_color')]."',0)");
					}

					foreach($nameArray_color_size_qnty as $result_color_size_qnty)
					{
						?>
						<td style="word-break:break-all; border:1px solid black; text-align:right">
						<?
						if($result_color_size_qnty[csf('cons')]!= "")
						{
							echo number_format($result_color_size_qnty[csf('cons')],4);
							$item_desctiption_total += $result_color_size_qnty[csf('cons')] ;
							$color_tatal+=$result_color_size_qnty[csf('cons')];
						}
						else echo "";
						?>
						</td>
						<?
					}
					?>
					<td style="word-break:break-all; border:1px solid black; text-align:center "><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_text=$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
					<? if($show_comment==1)
					{
						?>
						<td style="word-break:break-all; border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
						<td style="word-break:break-all; border:1px solid black; text-align:right">
						<?
							$amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,2);
							$total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
						?>
						</td>
						<td style="word-break:break-all; border:1px solid black; text-align:right"><? echo $currency[$currency_id]; ?> </td>
						<td style="word-break:break-all; border:1px solid black; text-align:right"><? echo $trims_remark; ?> </td>
					<? } ?>
				</tr>
				<?
				}
				?>
				<tr>
					<td style="border:1px solid black;  text-align:right" colspan="3"><strong> Item Total</strong></td>
					<td style="border:1px solid black;  text-align:right; font-weight:bold;"><? if($color_tatal !='') echo number_format($color_tatal,4); ?></td>
					<td style="border:1px solid black;  text-align:center"><? echo $uom_text; ?></td>
					<? if($show_comment==1)
					{?>
						<td style="border:1px solid black; text-align:right"></td>
						<td style="border:1px solid black; text-align:right">
						<?
						echo number_format($total_amount_as_per_gmts_color,2);
						$grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
						?>
						</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					<? } ?>
				</tr>
				<?
				}
				?>
				<tr>
					<td align="right" style="border:1px solid black"  colspan="7"><strong>Total</strong></td>
					<? if($show_comment==1) {?>
					<td>&nbsp;</td>
					<td style="border:1px solid black;  text-align:right"><? echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
        <? } ?>
        </table>
        <?
		}

			$carton_nameArray_item=sql_select("SELECT a.pre_cost_fabric_cost_dtls_id, a.trim_group, b.rate_cal_parameter from wo_booking_dtls a join lib_item_group b on b.id=a.trim_group where a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'   and  a.status_active =1 and a.is_deleted=0 and b.rate_cal_parameter in (2,14) group by a.pre_cost_fabric_cost_dtls_id, a.trim_group, b.rate_cal_parameter order by a.trim_group");
			
			//and a.sensitivity=0
			if(count($carton_nameArray_item)>0)
			{
				$po_ids=rtrim($po_no_arr[$nameArray_job_po_row[csf('job_no')]]['po_id']);
				$po_ids=array_unique(explode(",",$po_ids));
				$ref_nos=rtrim($job_ref_no[$nameArray_job_po_row[csf('job_no')]],',');
				$ref_nos=implode(",",array_unique(explode(",",$ref_nos)));
				$file_nos=rtrim($job_file_no[$nameArray_job_po_row[csf('job_no')]],',');
				$file_nos=implode(",",array_unique(explode(",",$file_nos)));
				$po_no_qty=0;
				$job_no=$nameArray_job_po_row[csf('job_no')];
				foreach($po_ids as $poid)
				{
					$po_no_qty+=$job_po_qty_arr[$job_no][$poid];
				}
		        ?>
		        <table border="1" align="left" class="rpt_table" cellpadding="0" width="1320" cellspacing="0" style="margin-top: 10px"  rules="all" >
		            <tr>
		                <td colspan="5" width="645" style="word-break:break-all; "><strong>Carton Details  (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style Desc:".$style_desc[$nameArray_job_po_row[csf('job_no')]]; echo " &nbsp;Po Qty.:&nbsp;".$po_no_qty; ?></strong></td>
		                <td width="530" colspan="10" style="word-break:break-all; font-weight:bold;">Po No: <? echo implode(", ",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
		            </tr>
		            <tr>
		                <td width="30" style="border:1px solid black"><strong>Sl</strong> </td>
		                <td width="90" style="border:1px solid black"><strong>Item Group</strong> </td>
		                <td width="80" style="border:1px solid black"><strong>Item Description</strong> </td>
		                <td width="90" style="border:1px solid black" colspan="3"><strong>Mesurmant (LXWXH)- CM</strong> </td>
		                <td width="80" align="center" style="border:1px solid black"><strong>SQM/Pcs</strong></td>
		                <td width="80" align="center" style="border:1px solid black"><strong>WO Qty</strong></td>
		                <td width="80" style="border:1px solid black" align="center"><strong>UOM</strong></td>
		                <td width="80" style="border:1px solid black" align="center"><strong>Rate</strong></td>
		                <td width="80" style="border:1px solid black" align="center"><strong>Ttl Sqm</strong></td>
		                <td width="80" style="border:1px solid black" align="center"><strong>Rate/Sqm</strong></td>
		                <td width="80" style="border:1px solid black" align="center"><strong>Amount</strong></td>
						<td width="80" style="border:1px solid black" align="center"><strong>Currency</strong></td>
		                <td width="120" style="border:1px solid black" align="center"><strong>Remarks</strong></td>
		            </tr>
		            <?
					$i=0;
		            $grand_total_as_per_gmts_color=0;
		            foreach($carton_nameArray_item as $cartondata)
		            {
						$i++;
						$cartonarray_item_description=sql_select( "SELECT a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_color,avg(b.rate) as rate, sum(b.amount) as amount, c.rate_cal_data from wo_booking_dtls a, wo_trim_book_con_dtls b, wo_pre_cost_trim_co_cons_dtls c where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and c.wo_pre_cost_trim_cost_dtls_id=a.pre_cost_fabric_cost_dtls_id  and a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.trim_group=".$cartondata[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$cartondata[csf('pre_cost_fabric_cost_dtls_id')]." and a.sensitivity=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_color, c.rate_cal_data");
						
						?>
						<tr>
							<td style="border:1px solid black" rowspan="<? echo count($cartonarray_item_description)+1; ?>"><? echo $i; ?></td>
							<td align="center" style="word-break:break-all; border:1px solid black" rowspan="<? echo count($cartonarray_item_description)+1; ?>">
								<?
								echo $trim_group_library[$cartondata[csf('trim_group')]]."<br/>";
								echo implode(",",$booking_country_arr[$cartondata[csf('pre_cost_fabric_cost_dtls_id')]][0]);
								?>
							</td>
							<?
							$color_tatal=0;
							$total_amount_as_per_gmts_color=0;
							foreach($cartonarray_item_description as $cartonresult)
							{
								$item_desctiption_total=0;
								$rate_cal_arr=explode("~~",$cartonresult[csf('rate_cal_data')]);
								$sqmpcs=0;
								if($cartondata[csf('rate_cal_parameter')]==2){
									$sqmpcs=($rate_cal_arr[0]+$rate_cal_arr[1]+6)*($rate_cal_arr[1]+$rate_cal_arr[2]+3)/5000;
								}
								if($cartondata[csf('rate_cal_parameter')]==14){
									$sqmpcs=($rate_cal_arr[0]*$rate_cal_arr[1])/10000;
								}
								?>
								<td style="word-break:break-all; border:1px solid black"><? if($cartonresult[csf('description')]){ echo $cartonresult[csf('description')];} ?> </td>
							
								<td style="word-break:break-all; border:1px solid black" width="30"><? echo $rate_cal_arr[0]; ?> </td>
								<td style="word-break:break-all; border:1px solid black" width="30"><? echo $rate_cal_arr[1]; ?> </td>
								<td style="word-break:break-all; border:1px solid black;" width="30">
								<? echo $rate_cal_arr[2];?></td>
								<td style="word-break:break-all; border:1px solid black; text-align:left"><? echo number_format($sqmpcs,4);?></td>
								<?

							if($db_type==0)
							{
							 $nameArray_color_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trim_group=". $cartondata[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$cartondata[csf('pre_cost_fabric_cost_dtls_id')]." and  b.description='". $cartonresult[csf('description')]."' and b.brand_supplier='".$cartonresult[csf('brand_supplier')]."' and b.item_color='".$cartonresult[csf('item_color')]."'");
							}
							if($db_type==2)
							{
								$nameArray_color_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b,wo_pre_cost_trim_co_cons_dtls c  where a.id= b.wo_trim_booking_dtls_id  and a.pre_cost_fabric_cost_dtls_id= c.wo_pre_cost_trim_cost_dtls_id and b.color_size_table_id=c.color_size_table_id and a.po_break_down_id=c.po_break_down_id and b.po_break_down_id=c.po_break_down_id  and a.po_break_down_id=c.po_break_down_id  and a.booking_no=b.booking_no and a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and a.trim_group=". $cartondata[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$cartondata[csf('pre_cost_fabric_cost_dtls_id')]." and nvl( b.description,0)=nvl('". $cartonresult[csf('description')]."',0) and nvl(b.brand_supplier,0)=nvl('".$cartonresult[csf('brand_supplier')]."',0) and nvl(b.item_color,0)=nvl('".$cartonresult[csf('item_color')]."',0) and nvl(c.rate_cal_data,0)=nvl('".$cartonresult[csf('rate_cal_data')]."',0)");
								 
								 
							}

							foreach($nameArray_color_size_qnty as $result_color_size_qnty)
							{
								?>
								<td style="word-break:break-all; border:1px solid black; text-align:right">
								<?
								$ttl_sqm=0;
								if($result_color_size_qnty[csf('cons')]!= "")
								{
									echo number_format($result_color_size_qnty[csf('cons')],4);
									$item_desctiption_total += $result_color_size_qnty[csf('cons')] ;
									$color_tatal+=$result_color_size_qnty[csf('cons')];
									$ttl_sqm=$result_color_size_qnty[csf('cons')]*$sqmpcs;
								}
								else echo "";
								?>
								</td>
								<?
							}
							?>
							<td style="word-break:break-all; border:1px solid black; text-align:center "><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_text=$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
							<td style="word-break:break-all; border:1px solid black; text-align:right"><? echo number_format($sqmpcs*$rate_cal_arr[3],4); ?> </td>
							<td style="word-break:break-all; border:1px solid black; text-align:right"><? echo number_format($ttl_sqm,4); ?> </td>
							<td style="word-break:break-all; border:1px solid black; text-align:right"><? echo number_format($rate_cal_arr[3],4); ?> </td>
							<td style="word-break:break-all; border:1px solid black; text-align:right">
							<?
								$amount_as_per_gmts_color = $ttl_sqm* $rate_cal_arr[3];
								echo number_format($amount_as_per_gmts_color,2);
								$total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
							?>
							</td>
							<td style="word-break:break-all; border:1px solid black; text-align:right"><? echo $currency[$currency_id]; ?> </td>
							<td style="word-break:break-all; border:1px solid black; text-align:right"><? echo $trims_remark; ?> </td>
						</tr>
						<?
						}
						?>
						<tr>
							<td style="border:1px solid black;  text-align:right" colspan="5"><strong> Item Total</strong></td>
							<td style="border:1px solid black;  text-align:right; font-weight:bold;"><? if($color_tatal !='') echo number_format($color_tatal,4); ?></td>
							<td style="border:1px solid black;  text-align:center"><? echo $uom_text; ?></td>
							<td style="border:1px solid black; text-align:right"></td>
							<td style="border:1px solid black; text-align:right"></td>
							<td style="border:1px solid black; text-align:right">
							<?
							echo number_format($total_amount_as_per_gmts_color,2);
							$grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
							?>
							</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<?
						}
						?>
						<tr>
							<td align="right" style="border:1px solid black"  colspan="7"><strong>Total</strong></td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td style="border:1px solid black;  text-align:right"><? echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
		        
		        </table>
		        <?
			}
		}
		?>
        <!--==============================================NO NENSITIBITY END=========================================  -->

		<?
       $mcurrency="";
	   $dcurrency="";
	   if($currency_id==1)
	   {
		$mcurrency='Taka';
		$dcurrency='Paisa';
	   }
	   if($currency_id==2)
	   {
		$mcurrency='USD';
		$dcurrency='CENTS';
	   }
	   if($currency_id==3)
	   {
		$mcurrency='EURO';
		$dcurrency='CENTS';
	   }
	   ?>

    
           <table border="1" align="left" class="rpt_table" cellpadding="0" width="100%" cellspacing="0" rules="all" style="margin-top: 10px">
              	<tr style="border:1px solid black;">
                    <td width="30%" style="border:1px solid black; text-align:left">Total Booking Amount</td>
                    <td width="70%" style="border:1px solid black;"><? echo number_format($booking_grand_total,2);?></td>
                </tr>
                <tr style="border:1px solid black;">
                    <td width="30%" style="border:1px solid black; text-align:left">Total Booking Amount (in word)</td>
                    <td width="70%" style="border:1px solid black;"><? echo number_to_words(def_number_format($booking_grand_total,2,""),$mcurrency, $dcurrency);?></td>
                </tr>
           </table>
         <br/>
		 <table border="1" align="left" class="rpt_table" cellpadding="0" width="100%" cellspacing="0" rules="all" style="margin-top: 10px">
            <tr>
                <td  style="word-break:break-all"><? echo get_spacial_instruction($txt_booking_no); ?></td>            
                
            </tr>
        </table>
	  <br>
        <?
        //------------------------------ Query for TNA start-----------------------------------

	//------------------------------ Query for TNA end-----------------------------------
		?>
    <div  style="margin-top:-50px;">
         <?
          echo signature_table(132, $cbo_company_name, "1320px",1);
		 ?>
   </div>
     <!-- <div id="page_break_div"></div> -->
    <div>
		<?
        	//echo signature_table(2, $cbo_company_name, "1330px");
			echo "****".custom_file_name($txt_booking_no,$style_sting,$job_no);
        ?>
    </div>
	<?
    exit();
}

if($action=="show_trim_booking_report5")
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$order_uom_arr=return_library_array("select id,order_uom  from lib_item_group","id","order_uom");
	$company_library=return_library_array("select id, company_name from lib_company", "id", "company_name");
	//$order_file_no_arr=return_library_array("select po_number,file_no  from wo_po_break_down","po_number","file_no");
	//$order_ref_no_arr=return_library_array("select po_number,grouping  from wo_po_break_down","po_number","grouping");
	$trim_group_library= return_library_array("select id, item_name from lib_item_group",'id','item_name');
	$color_library=return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	//$po_qnty_tot=return_field_value( "sum(po_quantity)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	
	$show_comment=str_replace("'","",$show_comment);
	
	ob_start();
	?>
	<div style="width:1333px" align="center">
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black">
           <tr>
               <td width="100">
              <? if($link==1){?>
               <img  src='../../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               <? }else{?>
               <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               <? }?>
               </td>
               <td width="1000">
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        
						 <tr>
						<td style="font-size:20px; text-align: center;" width="60%"><strong><? echo $company_library[$cbo_company_name]; ?></strong></td>
						</tr>
                        <tr>
                            <td align="center" style="font-size:14px">
                            <?
                            $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
                            foreach ($nameArray as $result)
                            {
                            				?>
                                            Plot No: <? echo $result[csf('plot_no')]; ?>
                                            Level No: <? echo $result[csf('level_no')]?>
                                            Road No: <? echo $result[csf('road_no')]; ?>
                                            Block No: <? echo $result[csf('block_no')];?>
                                            City No: <? echo $result[csf('city')];?>
                                            Zip Code: <? echo $result[csf('zip_code')]; ?>
                                            Province No: <?php echo $result[csf('province')]; ?>
                                            Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
                                            Email Address: <? echo $result[csf('email')];?>
                                            Website No: <? echo $result[csf('website')];
                            }
                                            ?>
                               </td>
                            </tr>
                            <tr>
                            <td align="center" style="font-size:20px">
                                 <strong>
								<?
								if(str_replace("'","",$cbo_isshort)==2)
								{
								$isshort="";
								}
								if(str_replace("'","",$cbo_isshort)==1)
								{
								$isshort="[Short]";
								}
								if ($report_title !="")
								{
									echo $report_title." ".$isshort;
								}
								else
								{
									echo "Main Trims Booking ".$isshort;
								}
								?>
                                &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <font style="color:#F00">
								<? if(str_replace("'","",$id_approved_id) ==1){ echo "(Approved)";}else{echo "";}; ?>
                                </font>
                                </strong>
                             </td>
                            </tr>
                      </table>
                </td>
                 <td width="250" id="barcode_img_id">

               </td>
            </tr>
       </table>
		<?

		$booking_grand_total=0;
		$job_no="";
		$currency_id="";
		$nameArray_job=sql_select( "select distinct b.job_no  from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_no=$txt_booking_no  and a.status_active=1 and b.status_active=1");
        foreach ($nameArray_job as $result_job)
        {

			$job_no.=$result_job[csf('job_no')].", ";
		}
		$buyer_string="";

		$nameArray_buyer=sql_select( "select distinct a.buyer_name  from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no=$txt_booking_no  and a.status_active=1 and b.status_active=1");
        foreach ($nameArray_buyer as $result_buy)
        {
			$buyer_string.=$buyer_name_arr[$result_buy[csf('buyer_name')]].",";
		}

		$po_no="";
		$po_number=array();
		$nameArray_job=sql_select( "select b.id, b.po_number,b.file_no,b.grouping from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no  and a.status_active=1 and b.status_active=1  group by b.id, b.po_number,b.file_no,b.grouping");
        foreach ($nameArray_job as $result_job)
        {
			$po_no.=$result_job[csf('po_number')].", ";
			$po_number[$result_job[csf('id')]]=$result_job[csf('po_number')];
			$po_idArr[$result_job[csf('id')]]=$result_job[csf('id')];
			
			$order_file_no_arr[$result_job[csf('po_number')]]=$result_job[csf('file_no')];
			$order_ref_no_arr[$result_job[csf('po_number')]]=$result_job[csf('grouping')];
		}
		$style_ref="";
		$nameArray_style=sql_select( "select distinct a.style_ref_no  from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no=$txt_booking_no  and a.status_active=1 and b.status_active=1");
        foreach ($nameArray_style as $result_style)
        {
			$style_ref.=$result_style[csf('style_ref_no')].", ";
		}
        $nameArray=sql_select( "select a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.source,a.remarks,a.pay_mode,a.delivery_address, a.inserted_by from wo_booking_mst a where  a.booking_no=$txt_booking_no");
		$inserted_by=$nameArray[0]['INSERTED_BY'];
        foreach ($nameArray as $result)
        {
			$varcode_booking_no=$result[csf('booking_no')];
        ?>
       <table width="100%" style="border:1px solid black">
            <tr>
                <td colspan="6" valign="top"></td>
            </tr>
            <tr>
                <td width="100" style="font-size:12px"><b>Booking No </b>   </td>
                <td width="110">:&nbsp;<? echo $result[csf('booking_no')];?> </td>
                <td width="100" style="font-size:12px"><b>Booking Date</b></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>
                <td width="100"><span style="font-size:16px"><b>Delivery Date</b></span></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>
            </tr>
            <tr>
                <td width="100" style="font-size:12px"><b>Currency</b></td>
                <td width="110">:&nbsp;<? $currency_id=$result[csf('currency_id')]; echo $currency[$result[csf('currency_id')]]; ?></td>
                <td  width="100" style="font-size:12px"><b>Conversion Rate</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('exchange_rate')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Source</b></td>
                <td  width="110" >:&nbsp;<? echo $source[$result[csf('source')]]; ?></td>
            </tr>
            <tr>
                <td width="100" style="font-size:12px"><b>Supplier Name</b>   </td>
                <td width="110">:&nbsp;<?
				if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3){
					echo $company_library[$result[csf('supplier_id')]];
					}
					else{
					echo $supplier_name_arr[$result[csf('supplier_id')]];
					}
				//echo $supplier_name_arr[$result[csf('supplier_id')]];?>    </td>
                <td width="100" style="font-size:12px"><b>Supplier Address</b></td>
               	<td   width="110">:&nbsp;<? echo $supplier_address_arr[$result[csf('supplier_id')]];?></td>
				<td width="100" style="font-size:12px"><b>Delivery Address</b></td>
               	<td  width="110">:&nbsp;<? echo $result[csf('delivery_address')]; ?></td>


            </tr>
            <tr>
                <td width="100" style="font-size:12px"><b>Buyer</b>   </td>
                <td width="110">:&nbsp;
				<?
				echo rtrim($buyer_string,", ");
				?>
                </td>
                <td width="110" style="font-size:12px"><b>Style</b> </td>
                <td  width="100" colspan="3">:&nbsp;<? echo rtrim($style_ref,", "); ?> </td>


            </tr>
            <tr>
                <td width="100" style="font-size:12px"><b>Job No</b>   </td>
                <td width="110">:&nbsp;
				<?
				echo rtrim($job_no,", ");
				?>
                </td>

               	<td width="110" style="font-size:12px"><b>PO No</b> </td>
                <td  width="100" style="font-size:12px" colspan="3">:&nbsp;<? echo rtrim($po_no,", "); ?> </td>
				</tr>
           
        </table>
        <br/>
		<?
        }
        ?>
          <!--==============================================AS PER GMTS COLOR START=========================================  -->
		<?

		$nameArray_job_po=sql_select( "select a.job_no,a.po_break_down_id,sum(distinct b.po_quantity) as po_quantity from wo_booking_dtls a, wo_po_break_down b  where a.booking_no=$txt_booking_no and a.po_break_down_id=b.id and b.status_active=1 and b.is_deleted=0  and a.status_active=1 group by a.job_no,a.po_break_down_id order by a.job_no,a.po_break_down_id ");
		foreach($nameArray_job_po as $nameArray_job_po_row)
		{
			$poIds=$nameArray_job_po_row[csf('po_break_down_id')];

        $nameArray_item=sql_select( "select distinct trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]."  and sensitivity=1 and status_active=1 order by trim_group ");
        $nameArray_color=sql_select( "select  b.color_number_id, min(b.color_size_table_id) as  color_size_table_id from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=1 and a.status_active=1 group by b.color_number_id order by color_size_table_id");
		//echo "select  b.color_number_id, min(b.color_size_table_id) as  color_size_table_id from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=1 group by b.color_number_id order by color_size_table_id";
		if(count($nameArray_color)>0)
		{
        ?>
        &nbsp;
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
           <tr>
                <td colspan="<? echo count($nameArray_color)+8; ?>" align="">
                <strong><? echo "Job NO:".$nameArray_job_po_row[csf('job_no')]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO No:" .$po_number[$nameArray_job_po_row[csf('po_break_down_id')]]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO Qty:" .$nameArray_job_po_row[csf('po_quantity')];?></strong>
                </td>
            </tr>
            <tr>
                <td colspan="<? echo count($nameArray_color)+8; ?>" align="">
                <strong>As Per Garments Color</strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Brand Supplier</strong> </td>
                <?
                foreach($nameArray_color  as $result_color)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $color_library[$result_color[csf('color_number_id')]];?></strong></td>
                <?	}    ?>
                <td style="border:1px solid black" align="center"><strong>Total</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
				<? if($show_comment==1) {?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
				<? } ?>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
            $nameArray_item_description=sql_select( "select b.description, b.brand_supplier,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=1 and a.status_active=1 and a.trim_group=".$result_item[csf('trim_group')]." group by b.description, b.brand_supplier order by trim_group ");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $trim_group_library[$result_item[csf('trim_group')]]; ?>
                </td>
                <?
                $color_tatal=array();
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $item_desctiption_total=0;
                ?>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('description')]; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('brand_supplier')]; ?> </td>
                <?
                foreach($nameArray_color  as $result_color)
                {
					if($db_type==0)
					{

                $nameArray_color_size_qnty=sql_select( "select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=1 and a.status_active=1 and a.trim_group=". $result_item[csf('trim_group')]." and b.description='". $result_itemdescription[csf('description')]."' and b.brand_supplier='". $result_itemdescription[csf('brand_supplier')]."' and b.color_number_id=".$result_color[csf('color_number_id')]."");
					}
					if($db_type==2)
					{

              $nameArray_color_size_qnty=sql_select( "select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=1 and a.status_active=1 and a.trim_group=". $result_item[csf('trim_group')]." and nvl(b.description,0)=nvl('". $result_itemdescription[csf('description')]."',0) and nvl(b.brand_supplier,0)=nvl('". $result_itemdescription[csf('brand_supplier')]."',0) and nvl(b.color_number_id,0)=nvl(".$result_color[csf('color_number_id')].",0)");
					}

                foreach($nameArray_color_size_qnty as $result_color_size_qnty)
                {
                ?>
                <td style="border:1px solid black; text-align:right">
                <?
                if($result_color_size_qnty[csf('cons')]!= "")
                {
					echo number_format($result_color_size_qnty[csf('cons')],2);
					$item_desctiption_total += $result_color_size_qnty[csf('cons')] ;
					if (array_key_exists($result_color[csf('color_number_id')], $color_tatal))
					{
						$color_tatal[$result_color[csf('color_number_id')]]+=$result_color_size_qnty[csf('cons')];
					}
					else
					{
						$color_tatal[$result_color[csf('color_number_id')]]=$result_color_size_qnty[csf('cons')];
					}
                }
                else echo "";
                ?>
                </td>
                <?
                }
                }
                ?>

                <td style="border:1px solid black; text-align:right"><? echo number_format($item_desctiption_total,2); ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
				<? 
				$amount_as_per_gmts_color = $item_desctiption_total*$result_itemdescription[csf('rate')];
				$item_wise_summery[$result_item[csf('trim_group')]][$poIds]+=$amount_as_per_gmts_color;
				$total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
				?>
				<? if($show_comment==1) {?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($amount_as_per_gmts_color,2);
                
                ?>
                </td>
				<? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="2"><strong> Item Total</strong></td>
                <?
                foreach($nameArray_color  as $result_color)
                {

                ?>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($color_tatal[$result_color[csf('color_number_id')]] !='')
                {
                echo number_format($color_tatal[$result_color[csf('color_number_id')]],2);
                }
                ?>
                </td>
            <?
            }
            ?>
                <td style="border:1px solid black;  text-align:right"><? echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
				<? if($show_comment==1) {?>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
				<? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
			<? if($show_comment==1) {?>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_color)+7; ?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
				<? } ?>
            </tr>
        </table>
        <br/>
        <?

		}
		?>
        <!--==============================================AS PER GMTS COLOR END=========================================  -->

        <!--==============================================AS PER GMTS SIZE START=========================================  -->
		<?
        $nameArray_item=sql_select( "select distinct trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and sensitivity=2 and status_active=1 order by trim_group ");
        $nameArray_size=sql_select( "select  b.item_size  as gmts_sizes, min(b.color_size_table_id) as color_size_table_id from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.sensitivity=2 and a.status_active=1 and b.status_active=1 group by b.item_size order by color_size_table_id");
		if(count($nameArray_item)>0)
		{
        ?>

        <table border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="<? echo count($nameArray_size)+8; ?>" align="">
                <strong><? echo "Job NO:".$nameArray_job_po_row[csf('job_no')]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO No:" .$po_number[$nameArray_job_po_row[csf('po_break_down_id')]]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO Qty:" .$nameArray_job_po_row[csf('po_quantity')];?></strong>
                </td>
            </tr>
            <tr>
                <td colspan="<? echo count($nameArray_size)+8; ?>" align="">
                <strong>As Per Garments Size </strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Brand Supplier</strong> </td>
                <?
                foreach($nameArray_size  as $result_size)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $result_size[csf('gmts_sizes')];?></strong></td>
                <?	}    ?>
                <td style="border:1px solid black" align="center"><strong>Total</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
				<? if($show_comment==1) {?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
				<? } ?>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_size=0;
            foreach($nameArray_item as $result_item)
            {
			$i++;
            //$nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2 and trim_group=".$result_item[csf('trim_group')]." order by trim_group ");
			$nameArray_item_description=sql_select( "select b.description, b.brand_supplier,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=2 and a.status_active=1 and a.trim_group=".$result_item[csf('trim_group')]." group by b.description, b.brand_supplier");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i ; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $trim_group_library[$result_item[csf('trim_group')]]; ?>
                </td>
                <?
                $size_tatal=array();
               $total_amount_as_per_gmts_color=0;$total_amount_as_per_gmts_size=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $item_desctiption_total=0;
                ?>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('description')]; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('brand_supplier')]; ?> </td>
                <?
                foreach($nameArray_size  as $result_size)
                {
					if($db_type==0)
					{
						//echo "select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=2 and a.trim_group=". $result_item[csf('trim_group')]." and b.description='". $result_itemdescription[csf('description')]."' and b.brand_supplier='". $result_itemdescription[csf('brand_supplier')]."' and b.item_size='".$result_size[csf('gmts_sizes')]."'";
                $nameArray_size_size_qnty=sql_select( "select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=2 and a.status_active=1 and a.trim_group=". $result_item[csf('trim_group')]." and b.description='". $result_itemdescription[csf('description')]."' and b.brand_supplier='". $result_itemdescription[csf('brand_supplier')]."' and b.item_size='".$result_size[csf('gmts_sizes')]."'");
					}
					if($db_type==2)
					{
                $nameArray_size_size_qnty=sql_select( "select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=2 and a.status_active=1 and a.trim_group=". $result_item[csf('trim_group')]." and nvl(b.description,0)=nvl('". $result_itemdescription[csf('description')]."',0) and nvl(b.brand_supplier,0)=nvl('". $result_itemdescription[csf('brand_supplier')]."',0) and nvl(b.item_size,0)=nvl('".$result_size[csf('gmts_sizes')]."',0)");
					}
                foreach($nameArray_size_size_qnty as $result_size_size_qnty)
                {
                ?>
                <td style="border:1px solid black; text-align:right">
                <?
                if($result_size_size_qnty[csf('cons')]!= "")
                {
					echo number_format($result_size_size_qnty[csf('cons')],2);
					$item_desctiption_total += $result_size_size_qnty[csf('cons')] ;
					if (array_key_exists($result_size[csf('gmts_sizes')], $size_tatal))
					{
						$size_tatal[$result_size[csf('gmts_sizes')]]+=$result_size_size_qnty[csf('cons')];
					}
					else
					{
						$size_tatal[$result_size[csf('gmts_sizes')]]=$result_size_size_qnty[csf('cons')];
					}
                }
                else echo "";
                ?>
                </td>
                <?
                }
                }
                ?>

                <td style="border:1px solid black; text-align:right"><? echo number_format($item_desctiption_total,2); ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
				<? if($show_comment==1) {?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">

                <?
                $amount_as_per_gmts_size = $item_desctiption_total*$result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_size,2);
				$item_wise_summery[$result_item[csf('trim_group')]][$poIds]+=$amount_as_per_gmts_size;
                $total_amount_as_per_gmts_size+=$amount_as_per_gmts_size;
                ?>
                </td>
				<? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="2"><strong> Item Total</strong></td>
                <?
                foreach($nameArray_size  as $result_size)
                {

                ?>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($size_tatal[$result_size[csf('gmts_sizes')]] !='')
                {
                	echo number_format($size_tatal[$result_size[csf('gmts_sizes')]],2);
                }
                ?>
                </td>
            <?
            }
            ?>
                <td style="border:1px solid black;  text-align:right"><? echo number_format(array_sum($size_tatal),2);  ?></td>
                 <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
				 <? if($show_comment==1) {?>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_size,2);
                $grand_total_as_per_gmts_size+=$total_amount_as_per_gmts_size;
                ?>
                </td>
				<? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
			<? if($show_comment==1) {?>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_size)+7; ?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_size,2); $booking_grand_total+=$grand_total_as_per_gmts_size; ?></td>
				<? } ?>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER SIZE  END=========================================  -->

         <!--==============================================AS PER CONTRAST COLOR START=========================================  -->
		<?
		$gmtcolor_library=return_library_array( "select b.item_color,b.color_number_id from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.sensitivity=3 and a.status_active=1", "item_color", "color_number_id"  );

        $nameArray_item=sql_select( "select distinct trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and sensitivity=3 and status_active=1 order by trim_group ");

        $nameArray_color=sql_select( "select  b.item_color as color_number_id, b.color_number_id as gmts_color, min(b.color_size_table_id) as  color_size_table_id from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=3 and a.status_active=1 and b.cons >0 group by b.item_color ,b.color_number_id order by color_size_table_id ");
		if(count($nameArray_color)>0)
		{
        ?>
        <table border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
           <tr>
                <td colspan="<? echo count($nameArray_color)+9; ?>" align="">
                <strong><? echo "Job NO:".$nameArray_job_po_row[csf('job_no')]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO No:" .$po_number[$nameArray_job_po_row[csf('po_break_down_id')]]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO Qty:" .$nameArray_job_po_row[csf('po_quantity')];?></strong>
                </td>
            </tr>
            <tr>
                <td colspan="<? echo count($nameArray_color)+9; ?>" align="">
                <strong>Contrast Color</strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black" rowspan="2"><strong>Sl</strong> </td>
                <td style="border:1px solid black" rowspan="2"><strong>Item Group</strong> </td>
                <td style="border:1px solid black" rowspan="2"><strong>Item Description</strong> </td>
                <td style="border:1px solid black" rowspan="2"><strong>Brand Supplier</strong> </td>
                <td style="border:1px solid black"><strong>Gmts Color</strong> </td>
                <?
                foreach($nameArray_color  as $result_color)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $color_library[$result_color[csf('gmts_color')]];?></strong></td>
                <?	}    ?>
                <td style="border:1px solid black" align="center" rowspan="2"><strong>Total</strong></td>
                <td style="border:1px solid black" align="center" rowspan="2"><strong>UOM</strong></td>
				<? if($show_comment==1) {?>
                <td style="border:1px solid black" align="center" rowspan="2"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center" rowspan="2"><strong>Amount</strong></td>
				<? } ?>
            </tr>
             <tr>
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <?
                foreach($nameArray_color  as $result_color)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $color_library[$result_color[csf('color_number_id')]];?></strong></td>
                <?	}    ?>

            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
            //$nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and trim_group=".$result_item[csf('trim_group')]." order by trim_group ");
			$nameArray_item_description=sql_select( "select b.description, b.brand_supplier,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=3 and a.status_active=1 and a.trim_group=".$result_item[csf('trim_group')]." group by b.description, b.brand_supplier order by trim_group ");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $trim_group_library[$result_item[csf('trim_group')]]; ?>
                </td>
                <?
                $color_tatal=array();
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $item_desctiption_total=0;
                ?>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('description')]; ?> </td>
                <td style="border:1px solid black" colspan="2"><? echo $result_itemdescription[csf('brand_supplier')]; ?> </td>
                <?
                foreach($nameArray_color  as $result_color)
                {
					if($db_type==0)
				    {
                $nameArray_color_size_qnty=sql_select( "select sum(b.cons) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=3 and a.status_active=1 and a.trim_group=". $result_item[csf('trim_group')]." and b.description='". $result_itemdescription[csf('description')]."' and b.brand_supplier='". $result_itemdescription[csf('brand_supplier')]."' and b.item_color=".$result_color[csf('color_number_id')]." and b.color_number_id=".$result_color[csf('gmts_color')]."");
					}
					if($db_type==2)
				    {
                $nameArray_color_size_qnty=sql_select( "select sum(b.cons) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=3 and a.status_active=1 and a.trim_group=". $result_item[csf('trim_group')]." and nvl(b.description,0)=nvl('". $result_itemdescription[csf('description')]."',0) and nvl(b.brand_supplier,0)=nvl('". $result_itemdescription[csf('brand_supplier')]."',0) and nvl(b.item_color,0)=nvl(".$result_color[csf('color_number_id')].",0) and nvl(b.color_number_id,0)=nvl(".$result_color[csf('gmts_color')].",0)");
					}
                foreach($nameArray_color_size_qnty as $result_color_size_qnty)
                {
                ?>
                <td style="border:1px solid black; text-align:right">
                <?
                if($result_color_size_qnty[csf('cons')]!= "")
                {
					echo number_format($result_color_size_qnty[csf('cons')],2);
					$item_desctiption_total += $result_color_size_qnty[csf('cons')] ;

					if (array_key_exists($result_color[csf('color_number_id')], $color_tatal))
					{
						$color_tatal[$result_color[csf('color_number_id')]][$result_color[csf('gmts_color')]]+=$result_color_size_qnty[csf('cons')];
					}
					else
					{
						$color_tatal[$result_color[csf('color_number_id')]][$result_color[csf('gmts_color')]]=$result_color_size_qnty[csf('cons')];
					}
                }
                else echo "";
                ?>
                </td>
                <?
                }
                }
                ?>

                <td style="border:1px solid black; text-align:right"><? echo number_format($item_desctiption_total,2); ?></td>
                 <td style="border:1px solid black; text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
				 <? if($show_comment==1) {?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
                $amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,2);
				$item_wise_summery[$result_item[csf('trim_group')]][$poIds]+=$amount_as_per_gmts_color;
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
				<? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="3"><strong> Item Total</strong></td>
                <?
				$item_total=0;
                foreach($nameArray_color  as $result_color)
                {

                ?>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($color_tatal[$result_color[csf('color_number_id')]][$result_color[csf('gmts_color')]] !='')
                {
                echo number_format($color_tatal[$result_color[csf('color_number_id')]][$result_color[csf('gmts_color')]],2);
				$item_total+=$color_tatal[$result_color[csf('color_number_id')]][$result_color[csf('gmts_color')]];
                }
                ?>
                </td>
            <?
            }
            ?>
                <td style="border:1px solid black;  text-align:right"><? echo number_format($item_total,2);  ?></td>
                <td style="border:1px solid black;text-align:center"></td>
				<? if($show_comment==1) {?>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,2);
                
                ?>
                </td>
				<? } ?>
				<?
				$grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
				?>
            </tr>
            <?
            }
            ?>
            <tr>
			<? if($show_comment==1) {?>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_color)+8; ?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2);  ?></td>
				<? } ?>
				<?
				$booking_grand_total+=$grand_total_as_per_gmts_color;
				?>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER CONTRAST COLOR END=========================================  -->

        <!--==============================================AS PER GMTS Color & SIZE START=========================================  -->
		<?
        $nameArray_item=sql_select( "select distinct trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and sensitivity=4 and status_active=1  and  status_active=1  order by trim_group ");
        $nameArray_size=sql_select( "select  b.item_size  as gmts_sizes, min(b.color_size_table_id) as color_size_table_id  from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]."  and a.status_active=1 and b.status_active=1 and a.sensitivity=4 group by b.item_size order by color_size_table_id");


		if(count($nameArray_size)>0)
		{
      	  ?>

        <table border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
             <tr>
                <td colspan="<? echo count($nameArray_size)+10;?>" align="">
                <strong><? echo "Job NO:".$nameArray_job_po_row[csf('job_no')]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO No:" .$po_number[$nameArray_job_po_row[csf('po_break_down_id')]]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO Qty:" .$nameArray_job_po_row[csf('po_quantity')];?></strong>
                </td>
            </tr>
            <tr>
                <td colspan="<? echo count($nameArray_size)+10; ?>" align="">
                <strong>Color & size sensitive </strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>

                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                 <td style="border:1px solid black"><strong>Gmts Color</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Brand Supplier</strong> </td>
                <?
                foreach($nameArray_size  as $result_size)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $result_size[csf('gmts_sizes')];?></strong></td>
                <?	}    ?>
                <td style="border:1px solid black" align="center"><strong>Total</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
				<? if($show_comment==1) {?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
				<? } ?>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
			$i++;
			$gmtcolor_library=return_library_array( "select b.item_color,b.color_number_id from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.trim_group=".$result_item[csf('trim_group')]." and a.sensitivity=4  and a.status_active=1 and b.status_active=1", "item_color", "color_number_id"  );
			 /*$nameArray_color=sql_select( "select distinct b.item_color as color_number_id,b.description, b.brand_supplier from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.trim_group=".$result_item[csf('trim_group')]." and a.sensitivity=4"); */
			 $nameArray_color=sql_select( "select  b.item_color,b.color_number_id,b.description, b.brand_supplier, min(b.color_size_table_id) as  color_size_table_id,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.trim_group=".$result_item[csf('trim_group')]." and a.sensitivity=4  and a.status_active=1 and b.status_active=1 group by b.item_color,b.color_number_id,b.description, b.brand_supplier order by color_size_table_id");

            $nameArray_item_description=sql_select( "select distinct trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and sensitivity=4 and trim_group=".$result_item[csf('trim_group')]."  and status_active=1  order by trim_group ");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo   (count($nameArray_item_description)*count($nameArray_color)); ?>">
                <? echo $i ; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo (count($nameArray_item_description)*count($nameArray_color)); ?>">
                <? echo $trim_group_library[$result_item[csf('trim_group')]]; ?>
                </td>
                <?


                $color_tatal=array();
                $total_amount_as_per_gmts_color=0;
				foreach($nameArray_color as $result_color)
                {
					$item_desctiption_total=0;
					?>
					<td style="border:1px solid black"><? echo $color_library[$result_color[csf('item_color')]]; ?> </td>
                    <td style="border:1px solid black"><? echo $color_library[$result_color[csf('color_number_id')]]; ?> </td>
					<td style="border:1px solid black" ><? echo $result_color[csf('description')]; ?> </td>
					<td style="border:1px solid black" ><? echo $result_color[csf('brand_supplier')]; ?> </td>
					<?
					foreach($nameArray_size  as $result_size)
					{
						if($db_type==0)
				        {
						$nameArray_size_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=4 and a.trim_group=". $result_item[csf('trim_group')]." and  b.description='". $result_color[csf('description')]."' and b.brand_supplier='".$result_color[csf('brand_supplier')]."'  and b.item_size='".$result_size[csf('gmts_sizes')]."' and b.item_color=".$result_color[csf('item_color')]." and b.color_number_id=".$result_color[csf('color_number_id')]."  and a.status_active=1 and b.status_active=1");
						}
						if($db_type==2)
				        {
						$nameArray_size_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=4 and a.trim_group=". $result_item[csf('trim_group')]." and nvl( b.description,0)=nvl('". $result_color[csf('description')]."',0) and nvl(b.brand_supplier,0)=nvl('".$result_color[csf('brand_supplier')]."',0)  and nvl(b.item_size,0)=nvl('".$result_size[csf('gmts_sizes')]."',0) and nvl(b.item_color,0)=nvl(".$result_color[csf('item_color')].",0) and nvl(b.color_number_id,0)=nvl(".$result_color[csf('color_number_id')].",0)  and a.status_active=1 and b.status_active=1");
						}
						foreach($nameArray_size_size_qnty as $result_size_size_qnty)
						{
							?>
							<td style="border:1px solid black; text-align:right">
							<?
							if($result_size_size_qnty[csf('cons')]!= "")
							{
								echo number_format($result_size_size_qnty[csf('cons')],2);
								$item_desctiption_total += $result_size_size_qnty[csf('cons')] ;

								if (array_key_exists($result_size[csf('gmts_sizes')], $color_tatal))
								{
									$color_tatal[$result_size[csf('gmts_sizes')]]+=$result_size_size_qnty[csf('cons')];
								}
								else
								{
									$color_tatal[$result_size[csf('gmts_sizes')]]=$result_size_size_qnty[csf('cons')];
								}
							}
							else echo "";
							?>
							</td>
							<?
						}
					}
					?>
					<td style="border:1px solid black; text-align:right"><? echo number_format($item_desctiption_total,2); ?></td>
					<td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
					<?
					$rate =$result_color[csf('amount')]/$item_desctiption_total;
					$amount_as_per_gmts_color =$result_color[csf('amount')];
					$item_wise_summery[$result_item[csf('trim_group')]][$poIds]+=$amount_as_per_gmts_color;
					$total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
					?>
					<? if($show_comment==1) {?>
					<td style="border:1px solid black; text-align:right">
					<?
					
					echo number_format($rate,2);
					?>
                     </td>
					<td style="border:1px solid black; text-align:right">
					<?
					//$amount_as_per_gmts_color = $item_desctiption_total*  $result_color[csf('rate')];
					
					echo number_format($amount_as_per_gmts_color,2);
					
					?>
					</td>
					<? } ?>
				</tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="6"><strong> Item Total</strong></td>
                <?
                foreach($nameArray_size  as $result_size)
                {

                ?>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($color_tatal[$result_size[csf('gmts_sizes')]] !='')
                {
                echo number_format($color_tatal[$result_size[csf('gmts_sizes')]],2);
                }
                ?>
                </td>
            <?
            }
            ?>
                <td style="border:1px solid black;  text-align:right"><? echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
				<? if($show_comment==1) {?>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,2);
                
                ?>
                </td>
				<? } ?>
				<?
				$grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
				?>
            </tr>
            <?
            }

            ?>
            <tr>
			<? if($show_comment==1) {?>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_size)+9; ?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2);  ?></td>
				<? } ?>
				<?
				$booking_grand_total+=$grand_total_as_per_gmts_color;
				?>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER Color & SIZE  END=========================================  -->


         <!--==============================================NO NENSITIBITY START=========================================  -->
		<?
        $nameArray_item=sql_select( "select distinct trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and sensitivity=0 and status_active=1 order by trim_group ");
		
        //$nameArray_color=sql_select( "select distinct b.color_number_id from wo_trims_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.sensitivity=1");
		//$nameArray_color= array();
		if(count($nameArray_item)>0)
		{
        ?>
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="8" align="">
                <strong><? echo "Job NO:".$nameArray_job_po_row[csf('job_no')]."&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO No:" .$po_number[$nameArray_job_po_row[csf('po_break_down_id')]]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO Qty:" .$nameArray_job_po_row[csf('po_quantity')];?></strong>
                </td>
            </tr>
            <tr>
                <td colspan="8" align="">
                <strong>No Sensitivity</strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Brand Supplier</strong> </td>
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <td align="center" style="border:1px solid black"><strong> Qnty</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
				<? if($show_comment==1) {?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
				<? } ?>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;

				$nameArray_item_description=sql_select( "select  b.description, b.brand_supplier,b.item_color,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.status_active=1 and a.trim_group=".$result_item[csf('trim_group')]." and a.sensitivity=0 group by b.description, b.brand_supplier,b.item_color");

            //$nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 and trim_group=".$result_item[csf('trim_group')]." order by trim_group ");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">

                <? echo $trim_group_library[$result_item[csf('trim_group')]]; ?>
                </td>
                <?
                $color_tatal=0;
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $item_desctiption_total=0;
                ?>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('description')]; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('brand_supplier')]; ?> </td>
                 <td style="border:1px solid black"><? echo $color_library[$result_itemdescription[csf('item_color')]]; ?> </td>
                <?
                //$nameArray_color_size_qnty=sql_select( "select sum(b.cons) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.sensitivity=0 and a.trim_group=". $result_item['trim_group']." and a.description='". $result_itemdescription['description']."'");
				/*if($db_type==0)
				{
				$nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls     where    booking_no=$txt_booking_no and sensitivity=0 and trim_group=". $result_item[csf('trim_group')]." and description='". $result_itemdescription[csf('description')]."'");
				}

				if($db_type==2)
				{
					echo "select sum(wo_qnty) as cons from wo_booking_dtls     where    booking_no=$txt_booking_no and sensitivity=0 and trim_group=". $result_item[csf('trim_group')]." and nvl(description,0)=nvl('". $result_itemdescription[csf('description')]."',0)";
				$nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls     where    booking_no=$txt_booking_no and sensitivity=0 and trim_group=". $result_item[csf('trim_group')]." and nvl(description,0)=nvl('". $result_itemdescription[csf('description')]."',0)");
				}*/

				if($db_type==0)
				{
				$nameArray_color_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=0 and a.trim_group=". $result_item[csf('trim_group')]."  and a.status_active=1 and  b.description='". $result_itemdescription[csf('description')]."' and b.brand_supplier='".$result_itemdescription[csf('brand_supplier')]."' and b.item_color='".$result_itemdescription[csf('item_color')]."'");
				}
				if($db_type==2)
				{
				$nameArray_color_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=0 and a.trim_group=". $result_item[csf('trim_group')]." and a.status_active=1 and nvl( b.description,0)=nvl('". $result_itemdescription[csf('description')]."',0) and nvl(b.brand_supplier,0)=nvl('".$result_itemdescription[csf('brand_supplier')]."',0) and nvl(b.item_color,0)=nvl('".$result_itemdescription[csf('item_color')]."',0)");
				}

                foreach($nameArray_color_size_qnty as $result_color_size_qnty)
                {
                ?>
                <td style="border:1px solid black; text-align:right">
                <?
                if($result_color_size_qnty[csf('cons')]!= "")
                {
                echo number_format($result_color_size_qnty[csf('cons')],2);
                $item_desctiption_total += $result_color_size_qnty[csf('cons')] ;
                $color_tatal+=$result_color_size_qnty[csf('cons')];
                }
                else echo "";
                ?>
                </td>
                <?
                }
                ?>

                <td style="border:1px solid black; text-align:center "><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
				<? if($show_comment==1) {?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
                $amount_as_per_gmts_color = $item_desctiption_total*$result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,2);
				$item_wise_summery[$result_item[csf('trim_group')]][$poIds]+=$amount_as_per_gmts_color;
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
				<? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="3"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($color_tatal !='')
                {
                echo number_format($color_tatal,2);
                }
                ?>
                </td>
                <td style="border:1px solid black;"></td>
				<? if($show_comment==1) {?>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
				<? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
			<? if($show_comment==1) {?>
                <td align="right" style="border:1px solid black"  colspan="8"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
				<? } ?>
            </tr>
        </table>
        <?
		//print_r($color_tatal);
		}
		}

		?>
        <!--==============================================NO NENSITIBITY END=========================================  -->
       &nbsp;
       <?
       $mcurrency="";
	   $dcurrency="";
	   if($currency_id==1)
	   {
		$mcurrency='Taka';
		$dcurrency='Paisa';
	   }
	   if($currency_id==2)
	   {
		$mcurrency='USD';
		$dcurrency='CENTS';
	   }
	   if($currency_id==3)
	   {
		$mcurrency='EURO';
		$dcurrency='CENTS';
	   }
	   ?>
	   <? if($show_comment==1) {?>
       <table  width="100%" class="rpt_table"  border="1" cellpadding="0" cellspacing="0" rules="all">
       
	   <tr style="border:1px solid black;">
	
                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount</th><td width="30%" style="border:1px solid black; text-align:right"><? echo number_format($booking_grand_total,2);?></td>
			
            </tr>
            <tr style="border:1px solid black;">
		
                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount (in word)</th><td width="30%" style="border:1px solid black;"><? echo number_to_words(def_number_format($booking_grand_total,2,""),$mcurrency, $dcurrency);?></td>
			
            </tr>
			
       </table>
	   <? } ?>
          &nbsp;
        <table width="100%">
        <tr>
        <td width="49%">
		

         <? echo get_spacial_instruction($txt_booking_no);?>
    </td>
    <td width="2%"></td>

    <td width="49%">
    <?
	//if($show_comment==1)
	if(str_replace("'","",$show_comment)==1)
	{
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" >
                <thead>
                    <th width="40">SL</th>
                    <th width="150">Item</th>
                    <th width="150">PO No</th>
                    <th width="150">Pre-Cost Value</th>
                    <th width="">WO Value </th>

                </thead>
       <tbody>
       <?
					 $po_id=implode(",",$po_idArr);

					 $condition= new condition();
					 if($po_id!='' || $po_id!=0)
					 {
						 $condition->po_id("in($po_id)");
					 }
					 $condition->init();
					 $trim= new trims($condition);
					 //echo $trim->getQuery();die;
					 $trims_costing_arr=$trim->getAmountArray_by_orderAndItemid();
 


					$sql_lib_item_group_array=array();
					$sql_lib_item_group=sql_select("select id, item_name,conversion_factor,order_uom as cons_uom from lib_item_group");
					foreach($sql_lib_item_group as $row_sql_lib_item_group)
					{
						$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][item_name]=$row_sql_lib_item_group[csf('item_name')];
						$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][conversion_factor]=$row_sql_lib_item_group[csf('conversion_factor')];
						$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][cons_uom]=$row_sql_lib_item_group[csf('cons_uom')];
					}
					$sql_po_qty=sql_select("select sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id in($po_id)  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty");
						//list($sql_po_qty_row)=$sql_po_qty;
						//$po_qty=$sql_po_qty_row[csf('order_quantity_set')];
						$po_qty=0;
						foreach($sql_po_qty as $row)
						{
							$po_qty+=$row[csf('order_quantity_set')];
						}
						unset($sql_po_qty);
						$sql_cons_data=sql_select("select a.id,a.rate,b.cons from wo_pre_cost_trim_cost_dtls a , wo_pre_cost_trim_co_cons_dtls b where a.id=b.wo_pre_cost_trim_cost_dtls_id and   b.po_break_down_id in($po_id)  and a.is_deleted=0  and a.status_active=1");
						//list($sql_cons_data_row)=$sql_cons_data;
						foreach($sql_cons_data as $row)
						{
							$pre_trims_arr[$row[csf('id')]]['cons']+=$row[csf('cons')];
							$pre_trims_arr[$row[csf('id')]]['rate']=$row[csf('rate')];
						}
						unset($sql_cons_data);
						$sql_cu_woq=sql_select("select pre_cost_fabric_cost_dtls_id,(amount) as amount  from wo_booking_dtls where po_break_down_id in($po_id)    and  booking_type=2 and status_active=1 and is_deleted=0");
						 foreach($sql_cu_woq as $row)
						{
							$wo_trims_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]]['amount']+=$row[csf('amount')];
							 
						}
						unset($sql_cu_woq);
						

					$sql="select job_no,po_break_down_id,pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls where booking_no=$txt_booking_no and status_active=1 and is_deleted=0 group by job_no,po_break_down_id,pre_cost_fabric_cost_dtls_id,trim_group";
					
					$exchange_rate=return_field_value("exchange_rate", " wo_booking_dtls", "booking_no=".$txt_booking_no." and exchange_rate>0 and status_active=1 ");
					$i=1;
					$total_amount=0;
                    $nameArray=sql_select( $sql );
                  foreach ($nameArray as $selectResult)
                    {
						$costing_per=return_field_value("costing_per", "wo_pre_cost_mst", "job_no='".$selectResult[csf('job_no')]."'");

						if($costing_per==1)
						{
							$costing_per_qty=12;
						}
						else if($costing_per==2)
						{
							$costing_per_qty=1;
						}
						else if($costing_per==3)
						{
							$costing_per_qty=24;
						}
						else if($costing_per==4)
						{
							$costing_per_qty=36;
						}
						else if($costing_per==5)
						{
							$costing_per_qty=48;
						}

						/*$sql_po_qty=sql_select("select sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id='".$selectResult[csf('po_break_down_id')]."'  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty");
						list($sql_po_qty_row)=$sql_po_qty;
						$po_qty=$sql_po_qty_row[csf('order_quantity_set')];*/

						/*$sql_cons_data=sql_select("select a.rate,b.cons from wo_pre_cost_trim_cost_dtls a , wo_pre_cost_trim_co_cons_dtls b where a.id=b.wo_pre_cost_trim_cost_dtls_id and  a.id='".$selectResult[csf('pre_cost_fabric_cost_dtls_id')]."' and b.po_break_down_id='".$selectResult[csf('po_break_down_id')]."' and a.is_deleted=0  and a.status_active=1");
						list($sql_cons_data_row)=$sql_cons_data;
						$pre_cons=$sql_cons_data_row[csf('cons')];
						$pre_rate=$sql_cons_data_row[csf('rate')];*/
						
						$pre_cons=$pre_trims_arr[$selectResult[csf('pre_cost_fabric_cost_dtls_id')]]['cons'];
						$pre_rate=$pre_trims_arr[$selectResult[csf('pre_cost_fabric_cost_dtls_id')]]['rate'];
						
						
						$pre_req_qnty=def_number_format(($pre_cons*($po_qty/$costing_per_qty))/$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor],5,"");
						// $pre_amount=$pre_req_qnty*$pre_rate;

						/*$sql_cu_woq=sql_select("select sum(amount) as amount  from wo_booking_dtls where po_break_down_id='".$selectResult[csf('po_break_down_id')]."' and pre_cost_fabric_cost_dtls_id='".$selectResult[csf('pre_cost_fabric_cost_dtls_id')]."'  and  booking_type=2 and status_active=1 and is_deleted=0");
						list($sql_cu_woq_row)=$sql_cu_woq;*/
						//$cu_woq_amount=$sql_cu_woq_row[csf('amount')];
						$cu_woq_amount=$wo_trims_arr[$selectResult[csf('pre_cost_fabric_cost_dtls_id')]]['amount'];

						$pre_amount=$trims_costing_arr[$selectResult[csf('po_break_down_id')]][$selectResult[csf('trim_group')]];
						if($pre_amount==0 || $pre_amount==""){
							$pre_amount=$cu_woq_amount;
						}
	   ?>
                    <tr>
                    <td width="40"><? echo $i;?></td>
                    <td width="150">
					<? echo $trim_group_library[$selectResult[csf('trim_group')]];?>
                    </td>
                    <td width="150">
					<? echo $po_number[$selectResult[csf('po_break_down_id')]];?>
                    </td>
                    <td width="150" align="right">
                     <? echo number_format($pre_amount,4); ?>
                    </td>
                    <td width="" align="right">
                     <? echo  number_format($item_wise_summery[$selectResult[csf('trim_group')]][$selectResult[csf('po_break_down_id')]],5);;// number_format($cu_woq_amount/$exchange_rate,5);?>
                    </td>

                    </tr>
	   <?
	   $i++;
					}
       ?>
	</tbody>
    </table>
    <?
	}
	?>
    </td>
    </tr>
    </table>

    </div>
    <div>
		<?
			$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
			echo signature_table(161, $cbo_company_name, "1330px",'','',$user_lib_name[$inserted_by]);
        ?>
    </div>
    <?

	if($link==1)
	{?>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<?
	}
	else
	{
	?>
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<? }?>

	<script>
	fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
	</script>
	<?
	
	$html = ob_get_contents();
	ob_clean();
	list($is_mail_send,$mail,$mail_body)=explode('___',$mail_send_data);
	if($is_mail_send==1){
		require_once('../../../mailer/class.phpmailer.php');
		require_once('../../../auto_mail/setting/mail_setting.php');
		$mailBody = preg_replace("/<img[^>]+\>/i", " ", $html)."<br>".$mail_body; 
			
		$mailToArr=array();
		$mailSql = "select b.EMAIL  from wo_booking_mst a,LIB_SUPPLIER b where b.id=a.supplier_id and a.booking_no='$txt_booking_no'";
		//echo $mailSql;die;
		$mailSqlRes=sql_select($mailSql);
		foreach($mailSqlRes as $rows){
			if($rows[EMAIL]){$mailToArr[]=$rows[EMAIL];}
		}
		
		
		$mailSql = "SELECT c.email_address as EMAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=98 and b.mail_user_setup_id=c.id  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
		//echo $mailSql;die;
		$mailSqlRes=sql_select($mailSql);
		foreach($mailSqlRes as $rows){
			if($rows[EMAIL]){$mailToArr[]=$rows[EMAIL];}
		}
		
		if($mail!=''){$mailToArr[]=$mail;}

		//Un-approve request mail......................................................
		$user_id=$_SESSION['logic_erp']['user_id'];
		$process_id=return_field_value("id", "wo_booking_mst", "BOOKING_NO='".str_replace("'","",$txt_booking_no)."'");
		$approved_no=return_field_value("MAX(approved_no) as approved_no","approval_history","entry_form=8 and mst_id=$process_id","approved_no");
		$unapproved_request=return_field_value("APPROVAL_CAUSE","fabric_booking_approval_cause","entry_form=8 and user_id=$user_id and booking_id=$process_id and approval_type=2 and approval_no='$approved_no'");//page_id=$page_id and
		
		if($unapproved_request){
			$mailToArr=array();
			if($msil_address){$mailToArr[]=$msil_address;}
			$final_app_user_mail=return_field_value("USER_EMAIL","user_passwd","id in(select APPROVED_BY from APPROVAL_HISTORY where id in(select max(id) from APPROVAL_HISTORY where mst_id=$process_id and ENTRY_FORM=8 and CURRENT_APPROVAL_STATUS=1))");
			$mailToArr[]= $final_app_user_mail;
		}
		$mailBody=$mail_body."<br>".$unapproved_request."<br><br>".$mailBody;
		//......................................................Un-approve request mail;


		$to=implode(',',$mailToArr);
		$subject="  Trims Booking Multi Job ";
		$header=mailHeader();
		echo sendMailMailer( $to, $subject, $mailBody, $from_mail,$att_file_arr );
		
	}
	else{
		echo $html;
	}
	exit();

}

?>
