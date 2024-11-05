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

if($action=="get_company_config"){
	$action($data);
}

function get_company_config($data)
{
	global $buyer_cond;
	$cbo_buyer_name= create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ); 
	echo "document.getElementById('buyer_td').innerHTML = '".$cbo_buyer_name."';\n";
	
	exit();
}

//---------------------------------------------------- Start---------------------------------------------------------------------------
function load_drop_down_supplier($data){
	if($data==5 || $data==3){
	   echo create_drop_down( "cbo_supplier_name", 172, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "-- Select Company --", "", "get_php_form_data( this.value, 'load_drop_down_attention', 'requires/short_trims_req_multi_job_controller');",0,"" );
	}
	else
	{
	$cbo_supplier_name= create_drop_down( "cbo_supplier_name", 172, "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id  and b.party_type=4 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name","id,supplier_name", 1, "--Select Supplier--",$selected,"get_php_form_data( this.value, 'load_drop_down_attention', 'requires/short_trims_req_multi_job_controller');","");
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
	$cbo_buyer_name= create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected,"","");

	echo "document.getElementById('buyer_td').innerHTML = '".$cbo_buyer_name."';\n";
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=2 and report_id=57 and is_deleted=0 and status_active=1");
	echo "document.getElementById('report_ids').value = '".$print_report_format."';\n";
	echo "print_report_button_setting('".$print_report_format."');\n";
	//exit();
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
                            <input type="hidden" name="cbo_company_name" id="cbo_company_name" value="<? echo $company_id;?>" />
                            <input type="hidden" style="width:20px" name="cbo_buyer_name" id="cbo_buyer_name" value="<? echo $cbo_buyer_name;?>" />
                        </th>
                    </tr>
                </thead>
                <tr>
                    <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:120px"></td>
                    <td><input name="txt_job" id="txt_job" class="text_boxes" style="width:120px"></td>
                    <td><input name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:100px"></td>
                    <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:150px"></td>
                    <td><?=create_drop_down( "cbo_item", 150, "select a.id,a.item_name from lib_item_group a where a.status_active =1 and a.is_deleted=0 order by a.item_name","id,item_name", 1, "-- Select Item Name --", $selected, "",0 ); ?>	</td>
                    <td align="center">
                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_job').value+'_'+document.getElementById('cbo_item').value+'_'+document.getElementById('txt_ref_no').value, 'create_fnc_process_data', 'search_div', 'short_trims_req_multi_job_controller','setFilterGrid(\'tbl_list_search\',-1)')" style="width:60px;" />
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="6" valign="middle">
                    <? echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );//echo load_month_buttons(1); ?>
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
	extract($_REQUEST);

	$data=explode('_',$data);
	$company_id=$data[0];
	$cbo_buyer_name=$data[1];
	
	$cbo_year_selection=$data[2];
	$txt_style=$data[3];
	$txt_order_search=$data[4];
	$txt_job=$data[5];
	$cbo_item=$data[6];
	$ref_no=$data[7];

	if ($txt_style!="") $style_cond=" and a.style_ref_no='$txt_style'"; else $style_cond=$txt_style;
	if ($txt_order_search!="") $order_cond=" and d.po_number='$txt_order_search'"; else $order_cond="";
	if ($ref_no!="") $ref_cond=" and d.grouping='$ref_no'"; else $ref_cond="";
	if ($txt_job!="") $job_cond=" and a.job_no_prefix_num='$txt_job'"; else $job_cond ="";
	if ($cbo_item!=0) $itemgroup_cond=" and c.trim_group=$cbo_item"; else $itemgroup_cond ="";
	if ($cbo_year_selection!=0) $jobinsert_cond=" and to_char(a.insert_date,'YYYY')='$cbo_year_selection'"; else $jobinsert_cond ="";
	
	$buyer_arr=return_library_array("select id, short_name from lib_buyer",'id','short_name');

	/*if($db_type==0){
		if ($start_date!="" &&  $end_date!="") $shipment_date = "and d.pub_shipment_date between '".change_date_format($start_date, "yyyy-mm-dd", "-")."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}

	if($db_type==2){
		if ($start_date!="" &&  $end_date!="") $shipment_date = "and d.pub_shipment_date between '".change_date_format($start_date, "yyyy-mm-dd", "-",1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}*/
	?>
	<input type="hidden" name="txt_selected_id" id="txt_selected_id" value="" />
	<input type="hidden" name="itemGroup" id="itemGroup" value="" />
	<input type="hidden" name="txt_job_id" id="txt_job_id" value="" />
	<input type="hidden" name="txt_selected_po" id="txt_selected_po" value="" />
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1300" class="rpt_table"  >
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
            <th width="130">Desc.</th>
            <th width="80">Brand/Sup.Ref</th>
            <th width="70">Req. Qnty</th>
            <th width="45">UOM</th>
            <th width="70">CU WOQ</th>
            <th width="70">Bal WOQ</th>
            <th width="45">Exch. Rate</th>
            <th width="50">Rate</th>
            <th>Amount</th>
        </thead>
	</table>
	<div style="width:1300px; overflow-y:scroll; max-height:350px;" id="buyer_list_view">
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1280" class="rpt_table" id="tbl_list_search" >
	<?
	/*if($db_type==0){
		if ($start_date!="" &&  $end_date!="") $class_datecond = "between '".change_date_format($start_date, "yyyy-mm-dd", "-")."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'"; else $class_datecond ="";
	}

	if($db_type==2){
		if ($start_date!="" &&  $end_date!="") $class_datecond = "between '".change_date_format($start_date, "yyyy-mm-dd", "-",1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-",1)."'"; else $class_datecond ="";
	}*/

	$trim_group_library= return_library_array("select id, item_name from lib_item_group",'id','item_name');
	$sql_lib_item_group_array=array();
	$sql_lib_item_group=sql_select("select id, item_name,conversion_factor,order_uom as cons_uom from lib_item_group");
	foreach($sql_lib_item_group as $row_sql_lib_item_group){
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][item_name]=$row_sql_lib_item_group[csf('item_name')];
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][conversion_factor]=$row_sql_lib_item_group[csf('conversion_factor')];
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][cons_uom]=$row_sql_lib_item_group[csf('cons_uom')];
	}
	if($db_type==0) $year_field="YEAR(a.insert_date) as YEAR";
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as YEAR";

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
	$sql_cu_booking=sql_select("select c.PRE_COST_FABRIC_COST_DTLS_ID as BOMDTLSID, c.PO_BREAK_DOWN_ID, sum(c.wo_qnty) as CU_WO_QNTY, sum(c.amount) as CU_AMOUNT from wo_po_details_master a, wo_po_break_down  d, wo_booking_dtls c where a.job_no=d.job_no_mst and a.job_no=c.job_no and a.garments_nature=2 and d.id=c.po_break_down_id and a.company_name=$company_id and a.buyer_name=$cbo_buyer_name and c.status_active=1 and c.is_deleted=0 $job_cond $order_cond $ref_cond $style_cond group by a.job_no, c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id"); //and c.entry_form_id=716
	foreach($sql_cu_booking as $row_cu_booking){
		$cu_booking_arr[$row_cu_booking['BOMDTLSID']][$row_cu_booking['PO_BREAK_DOWN_ID']]['cu_wo_qnty']=$row_cu_booking['CU_WO_QNTY'];
		$cu_booking_arr[$row_cu_booking['BOMDTLSID']][$row_cu_booking['PO_BREAK_DOWN_ID']]['cu_amount']=$row_cu_booking['CU_AMOUNT'];
	}
	
	$approval_allow=sql_select("select b.id, b.page_id, b.approval_need, b.allow_partial, b.validate_page,a.setup_date from approval_setup_mst a, approval_setup_dtls b where a.id=b.mst_id and a.company_id='$company_id' and a.status_active=1 and b.page_id=25 and b.status_active=1 and b.is_deleted=0 order by b.id desc ");
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

	$sql="select A.JOB_NO_PREFIX_NUM, A.JOB_NO, $year_field, A.COMPANY_NAME, A.BUYER_NAME, A.CURRENCY_ID, A.STYLE_REF_NO, B.COSTING_PER, B.EXCHANGE_RATE, C.ID as TRIM_DTLS_ID, C.TRIM_GROUP, C.DESCRIPTION, C.BRAND_SUP_REF, C.NOMINATED_SUPP_MULTI, C.RATE, D.ID AS PO_ID, D.PO_NUMBER, D.FILE_NO, D.GROUPING, D.PO_QUANTITY as PLAN_CUT, min(E.ID) AS ID, E.PO_BREAK_DOWN_ID, avg(E.CONS) AS CONS from wo_po_details_master a, wo_pre_cost_mst b, wo_pre_cost_trim_cost_dtls c, wo_po_break_down d, wo_pre_cost_trim_co_cons_dtls e where a.garments_nature=2 and a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and c.id=e.wo_pre_cost_trim_cost_dtls_id and d.id=e.po_break_down_id and a.company_name=$company_id and a.buyer_name=$cbo_buyer_name and d.is_deleted=0 and d.status_active=1 ".$buyer_cond_test." $itemgroup_cond $job_cond $order_cond $ref_cond $style_cond $approval_cond $jobinsert_cond group by a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id, c.trim_group, c.description, c.nominated_supp_multi, c.brand_sup_ref, c.rate, a.insert_date, d.id, d.po_number, d.file_no, d.grouping, d.po_quantity, e.po_break_down_id order by d.id, c.id";

	//echo $sql; die;
	$i=1; $total_req=0; $total_amount=0;
	$nameArray=sql_select($sql);
	foreach ($nameArray as $row){
		if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		$cbo_currency_job=$row['CURRENCY_ID'];
		$exchange_rate=$row['EXCHANGE_RATE'];
		if($cbo_currency==$cbo_currency_job) $exchange_rate=1;
		$req_qnty_cons_uom=$req_qty_arr[$row['PO_ID']][$row['TRIM_DTLS_ID']];
		$req_amount_cons_uom=$req_amount_arr[$row['PO_ID']][$row['TRIM_DTLS_ID']];
		$rate_cons_uom=$req_amount_cons_uom/$req_qnty_cons_uom;
		
		$req_qnty=def_number_format($req_qnty_cons_uom/$sql_lib_item_group_array[$row['TRIM_GROUP']][conversion_factor],5,"");
		$cu_wo_qnty=$cu_booking_arr[$row['TRIM_DTLS_ID']][$row['PO_ID']]['cu_wo_qnty'];
		$bal_woq=def_number_format($req_qnty-$cu_wo_qnty,5,"");
		
		$rate=def_number_format(($rate_cons_uom*$sql_lib_item_group_array[$row['TRIM_GROUP']][conversion_factor])*$exchange_rate,5,"");
		$req_amount=def_number_format($req_qnty*$rate,5,"");
		
		$total_req+=$req_qnty;
		$amount=def_number_format($rate*$bal_woq,4,"");
		
		//$ig=1; this comment open than permission aziz vai
		//echo $short_fab_validation.'--'.$bal_woq.'--'.$cu_wo_qnty.'<br>';
		if($short_fab_validation==0){
			if($bal_woq <= 0 && ($cu_wo_qnty !="" || $cu_wo_qnty !=0)){
				?>
                <tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i; ?>" onClick="js_set_value(<?=$i; ?>);">
                    <td width="30"><?=$i;?>
                        <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<? echo $row['ID']; ?>"/>
                        <input type="hidden" name="txt_trim_group_id" id="txt_trim_group_id<?php echo $i; ?>" value="<? echo $row['TRIM_DTLS_ID']; ?>"/>
                        <input type="hidden" name="txt_job_no" id="txt_job_no<?php echo $i; ?>" value="<? echo $row['JOB_NO']; ?>"/>
                        <input type="hidden" name="txt_po_id" id="txt_po_id<?php echo $i; ?>" value="<? echo $row['PO_ID']; ?>"/>
                    </td>
                    <td width="50" style="word-break:break-all"><? echo $buyer_arr[$row['BUYER_NAME']];?></td>
                    <td width="50" style="word-break:break-all"><? echo $row['YEAR'];?></td>
                    <td width="50" style="word-break:break-all"><? echo $row['JOB_NO_PREFIX_NUM'];?></td>
                    <td width="60" style="word-break:break-all"><? echo $row['FILE_NO'];?></td>
                    <td width="80" style="word-break:break-all"><? echo $row['GROUPING'];?></td>
                    <td width="100" style="word-break:break-all"><? echo $row['STYLE_REF_NO'];?></td>
                    <td width="100" style="word-break:break-all"><? echo $row['PO_NUMBER'];?></td>
                    
                    <td width="100" style="word-break:break-all"><? echo $trim_group_library[$row['TRIM_GROUP']];?></td>
                    <td width="130" style="word-break:break-all"><? echo $row['DESCRIPTION'];?></td>
                    <td width="80" style="word-break:break-all"><? echo $row['BRAND_SUP_REF'];?></td>
                    <td width="70" align="right"><?=number_format($req_qnty,4); ?></td>
                    <td width="45"><? echo $unit_of_measurement[$sql_lib_item_group_array[$row['TRIM_GROUP']][cons_uom]];?></td>
                    <td width="70" align="right"><? echo def_number_format($cu_wo_qnty,5,"");?></td>
                    <td width="70" align="right"><?=number_format($bal_woq,4); ?></td>
                    <td width="45" align="right" style="word-break:break-all"><?=number_format($exchange_rate,2); ?></td>
                    <td width="50" align="right" style="word-break:break-all"><?=number_format($rate,4); ?></td>
                    <td align="right"><?=number_format($amount,2); ?></td>
                </tr>
				<?
				$i++;
				$total_req_amount+=$req_amount;
				$total_cu_amount+=$row['cu_amount'];
				$total_amount+=$amount;
			}
		}
		else if($short_fab_validation==1){
		//if($bal_woq <= 0 && ($cu_wo_qnty !="" || $cu_wo_qnty !=0)){
			//&& $ig=0
			?>
            <tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i; ?>" onClick="js_set_value(<?=$i; ?>);">
                <td width="30"><?=$i;?>
                    <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<? echo $row['ID']; ?>"/>
                    <input type="hidden" name="txt_trim_group_id" id="txt_trim_group_id<?php echo $i; ?>" value="<? echo $row['TRIM_DTLS_ID']; ?>"/>
                    <input type="hidden" name="txt_job_no" id="txt_job_no<?php echo $i; ?>" value="<? echo $row['JOB_NO']; ?>"/>
                    <input type="hidden" name="txt_po_id" id="txt_po_id<?php echo $i; ?>" value="<? echo $row['PO_ID']; ?>"/>
                </td>
                <td width="50" style="word-break:break-all"><? echo $buyer_arr[$row['BUYER_NAME']];?></td>
                <td width="50" style="word-break:break-all"><? echo $row['YEAR'];?></td>
                <td width="50" style="word-break:break-all"><? echo $row['JOB_NO_PREFIX_NUM'];?></td>
                <td width="60" style="word-break:break-all"><? echo $row['FILE_NO'];?></td>
                <td width="80" style="word-break:break-all"><? echo $row['GROUPING'];?></td>
                <td width="100" style="word-break:break-all"><? echo $row['STYLE_REF_NO'];?></td>
                <td width="100" style="word-break:break-all"><? echo $row['PO_NUMBER'];?></td>
                
                <td width="100" style="word-break:break-all"><? echo $trim_group_library[$row['TRIM_GROUP']];?></td>
                <td width="130" style="word-break:break-all"><? echo $row['DESCRIPTION'];?></td>
                <td width="80" style="word-break:break-all"><? echo $row['BRAND_SUP_REF'];?></td>
                <td width="70" align="right"><?=number_format($req_qnty,4); ?></td>
                <td width="45"><? echo $unit_of_measurement[$sql_lib_item_group_array[$row['TRIM_GROUP']][cons_uom]];?></td>
                <td width="70" align="right"><? echo def_number_format($cu_wo_qnty,5,"");?></td>
                <td width="70" align="right"><?=number_format($bal_woq,4); ?></td>
                <td width="45" align="right" style="word-break:break-all"><?=number_format($exchange_rate,2); ?></td>
                <td width="50" align="right" style="word-break:break-all"><?=number_format($rate,4); ?></td>
                <td align="right"><?=number_format($amount,2); ?></td>
            </tr>
			<?
			$i++;
			$total_req_amount+=$req_amount;
			$total_cu_amount+=$row['cu_amount'];
			$total_amount+=$amount;
			//}
		}
		else if($short_fab_validation==2){			
			if($bal_woq <=1 && $cu_wo_qnty>0){
				?>
				<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i; ?>" onClick="js_set_value(<?=$i; ?>);">
					<td width="30"><?=$i;?>
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<? echo $row['ID']; ?>"/>
						<input type="hidden" name="txt_trim_group_id" id="txt_trim_group_id<?php echo $i; ?>" value="<? echo $row['TRIM_DTLS_ID']; ?>"/>
						<input type="hidden" name="txt_job_no" id="txt_job_no<?php echo $i; ?>" value="<? echo $row['JOB_NO']; ?>"/>
						<input type="hidden" name="txt_po_id" id="txt_po_id<?php echo $i; ?>" value="<? echo $row['PO_ID']; ?>"/>
					</td>
					<td width="50" style="word-break:break-all"><? echo $buyer_arr[$row['BUYER_NAME']];?></td>
					<td width="50" style="word-break:break-all"><? echo $row['YEAR'];?></td>
					<td width="50" style="word-break:break-all"><? echo $row['JOB_NO_PREFIX_NUM'];?></td>
					<td width="60" style="word-break:break-all"><? echo $row['FILE_NO'];?></td>
					<td width="80" style="word-break:break-all"><? echo $row['GROUPING'];?></td>
					<td width="100" style="word-break:break-all"><? echo $row['STYLE_REF_NO'];?></td>
					<td width="100" style="word-break:break-all"><? echo $row['PO_NUMBER'];?></td>
					
					<td width="100" style="word-break:break-all"><? echo $trim_group_library[$row['TRIM_GROUP']];?></td>
					<td width="130" style="word-break:break-all"><? echo $row['DESCRIPTION'];?></td>
					<td width="80" style="word-break:break-all"><? echo $row['BRAND_SUP_REF'];?></td>
					<td width="70" align="right"><?=number_format($req_qnty,4); ?></td>
					<td width="45"><? echo $unit_of_measurement[$sql_lib_item_group_array[$row['TRIM_GROUP']][cons_uom]];?></td>
					<td width="70" align="right"><? echo def_number_format($cu_wo_qnty,5,"");?></td>
					<td width="70" align="right"><?=number_format($bal_woq,4); ?></td>
					<td width="45" align="right" style="word-break:break-all"><?=number_format($exchange_rate,2); ?></td>
					<td width="50" align="right" style="word-break:break-all"><?=number_format($rate,4); ?></td>
					<td align="right"><?=number_format($amount,2); ?></td>
				</tr>
				<?
				$i++;
				$total_req_amount+=$req_amount;
				$total_cu_amount+=$row['cu_amount'];
				$total_amount+=$amount;
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
            <th width="130">&nbsp;</th>
            <th width="80">&nbsp;</th>
            <th width="70" id="value_total_req">&nbsp;</th>
            <th width="45"><input type="hidden" style="width:40px"  id="txt_tot_req_amount" value="<? echo number_format($total_req_amount,2); ?>" /></th>
            <th width="70"><input type="hidden" style="width:40px" id="txt_tot_cu_amount" value="<? echo number_format($total_cu_amount,2); ?>" /></th>
            <th width="70">&nbsp;</th>
            <th width="45">&nbsp;</th>
            <th width="50">&nbsp;</th>
            <th id="value_total_amount"><? echo number_format($total_amount,2); ?></th>
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
	</body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
    <?
	exit();
}

if ($action=="generate_trims_requisition"){
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

	$cu_booking_arr=array(); $cu_reqqty_arr=array();
	$sql_cu_booking=sql_select("select c.job_no, c.pre_cost_fabric_cost_dtls_id, c.entry_form_id, c.po_break_down_id, sum(c.wo_qnty) as cu_wo_qnty, sum(c.amount) as cu_amount from wo_po_details_master a, wo_po_break_down  d, wo_booking_dtls c where a.job_no=d.job_no_mst and a.job_no=c.job_no and  d.id=c.po_break_down_id and a.company_name=$cbo_company_name $shipment_date  and c.status_active=1 and c.is_deleted=0 and c.booking_type in (2,13) group by c.job_no, c.pre_cost_fabric_cost_dtls_id, c.entry_form_id, c.po_break_down_id");//and c.entry_form_id=716
	foreach($sql_cu_booking as $row_cu_booking){
		if($row_cu_booking[csf('job_no')]==716)
		{
			$cu_reqqty_arr[$row_cu_booking[csf('job_no')]][$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]]['cu_woq'][$row_cu_booking[csf('po_break_down_id')]] = $row_cu_booking[csf('cu_wo_qnty')];
			$cu_reqqty_arr[$row_cu_booking[csf('job_no')]][$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]]['cu_amount'][$row_cu_booking[csf('po_break_down_id')]] = $row_cu_booking[csf('cu_amount')];
		}
		else
		{
			$cu_booking_arr[$row_cu_booking[csf('job_no')]][$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]]['cu_woq'][$row_cu_booking[csf('po_break_down_id')]] = $row_cu_booking[csf('cu_wo_qnty')];
			$cu_booking_arr[$row_cu_booking[csf('job_no')]][$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]]['cu_amount'][$row_cu_booking[csf('po_break_down_id')]] = $row_cu_booking[csf('cu_amount')];
		}
	}

	$sql="select A.JOB_NO_PREFIX_NUM, A.JOB_NO, A.COMPANY_NAME, A.BUYER_NAME, A.CURRENCY_ID, A.STYLE_REF_NO, B.COSTING_PER, B.EXCHANGE_RATE, C.ID as TRIMCOSTID, C.TRIM_GROUP, C.DESCRIPTION, C.BRAND_SUP_REF, C.COUNTRY, C.RATE, D.ID as PO_ID, D.PO_NUMBER, D.PO_QUANTITY as PLAN_CUT, min(E.ID) as ID, E.PO_BREAK_DOWN_ID, avg(E.CONS) as CONS

	from wo_po_details_master a, wo_pre_cost_mst b, wo_pre_cost_trim_cost_dtls c, wo_po_break_down d, wo_pre_cost_trim_co_cons_dtls e
	where a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and c.id=e.wo_pre_cost_trim_cost_dtls_id and d.id=e.po_break_down_id and a.company_name='$cbo_company_name' $garment_nature_cond and e.id in($param) and e.po_break_down_id in($data) and c.id in($pre_cost_id) and d.is_deleted=0 and d.status_active=1
	group by a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id, c.trim_group, c.description, c.brand_sup_ref, c.country, c.rate, d.id, d.po_number, d.po_quantity, e.po_break_down_id order by d.id, c.id";

	$job_and_trimgroup_level=array();
	$i=1;
	$nameArray=sql_select( $sql );

	foreach ($nameArray as $jrow){
		$cbo_currency_job=$jrow['CURRENCY_ID'];
		$exchange_rate=$jrow['EXCHANGE_RATE'];
		if($cbo_currency_job==$cbo_currency_job){
			$exchange_rate=1;
		}

		$req_qnty_cons_uom=$req_qty_arr[$jrow['PO_ID']][$jrow['TRIMCOSTID']];
		$req_amount_cons_uom=$req_amount_arr[$jrow['PO_ID']][$jrow['TRIMCOSTID']];
		$rate_cons_uom=$req_amount_cons_uom/$req_qnty_cons_uom;

		$req_qnty_ord_uom=def_number_format($req_qnty_cons_uom/$sql_lib_item_group_array[$jrow['TRIM_GROUP']][conversion_factor],5,"");
		$rate_ord_uom=def_number_format(($rate_cons_uom*$sql_lib_item_group_array[$jrow['TRIM_GROUP']][conversion_factor])*$exchange_rate,5,"");
		$req_amount_ord_uom=def_number_format($req_qnty_ord_uom*$rate_ord_uom,5,"");
		
		$cu_reqsnqty=$cu_reqqty_arr[$jrow['JOB_NO']][$jrow['TRIMCOSTID']]['cu_woq'][$jrow['PO_ID']];
		$cu_reqsnamount=$cu_reqqty_arr[$jrow['JOB_NO']][$jrow['TRIMCOSTID']]['cu_amount'][$jrow['PO_ID']];

		$cu_woq=$cu_booking_arr[$jrow['JOB_NO']][$jrow['TRIMCOSTID']]['cu_woq'][$jrow['PO_ID']];
		$cu_amount=$cu_booking_arr[$jrow['JOB_NO']][$jrow['TRIMCOSTID']]['cu_amount'][$jrow['PO_ID']];

		$bal_woq=def_number_format($req_qnty_ord_uom,5,"");
		$amount=def_number_format($rate_ord_uom*$bal_woq,5,"");

		$job_and_trimgroup_level[$jrow['JOB_NO']][$jrow['TRIMCOSTID']]['job_no'][$jrow['PO_ID']]=$jrow['JOB_NO'];
		$job_and_trimgroup_level[$jrow['JOB_NO']][$jrow['TRIMCOSTID']]['po_id'][$jrow['PO_ID']]=$jrow['PO_ID'];
		$job_and_trimgroup_level[$jrow['JOB_NO']][$jrow['TRIMCOSTID']]['po_number'][$jrow['PO_ID']]=$jrow['PO_NUMBER'];
		$job_and_trimgroup_level[$jrow['JOB_NO']][$jrow['TRIMCOSTID']]['country'][$jrow['PO_ID']]=$jrow['COUNTRY'];
		$job_and_trimgroup_level[$jrow['JOB_NO']][$jrow['TRIMCOSTID']]['description'][$jrow['PO_ID']]=$jrow['DESCRIPTION'];
		$job_and_trimgroup_level[$jrow['JOB_NO']][$jrow['TRIMCOSTID']]['brand_sup_ref'][$jrow['PO_ID']]=$jrow['BRAND_SUP_REF'];
		$job_and_trimgroup_level[$jrow['JOB_NO']][$jrow['TRIMCOSTID']]['trim_group'][$jrow['PO_ID']]=$jrow['TRIM_GROUP'];
		$job_and_trimgroup_level[$jrow['JOB_NO']][$jrow['TRIMCOSTID']]['trim_group_name'][$jrow['PO_ID']]=$trim_group_library[$jrow['TRIM_GROUP']];
		$job_and_trimgroup_level[$jrow['JOB_NO']][$jrow['TRIMCOSTID']]['wo_pre_cost_trim_cost_dtls'][$jrow['PO_ID']]=$jrow['TRIMCOSTID'];

		$job_and_trimgroup_level[$jrow['JOB_NO']][$jrow['TRIMCOSTID']]['req_qnty'][$jrow['PO_ID']]=$req_qnty_ord_uom;
		$job_and_trimgroup_level[$jrow['JOB_NO']][$jrow['TRIMCOSTID']]['uom'][$jrow['PO_ID']]=$sql_lib_item_group_array[$jrow['TRIM_GROUP']][cons_uom];
		$job_and_trimgroup_level[$jrow['JOB_NO']][$jrow['TRIMCOSTID']]['uom_name'][$jrow['PO_ID']]=$unit_of_measurement[$sql_lib_item_group_array[$jrow['TRIM_GROUP']][cons_uom]];
		$job_and_trimgroup_level[$jrow['JOB_NO']][$jrow['TRIMCOSTID']]['req_amount'][$jrow['PO_ID']]=$req_amount_ord_uom;

		$job_and_trimgroup_level[$jrow['JOB_NO']][$jrow['TRIMCOSTID']]['cu_reqsnqty'][$jrow['PO_ID']]=$cu_reqsnqty;
		$job_and_trimgroup_level[$jrow['JOB_NO']][$jrow['TRIMCOSTID']]['cu_reqsnamount'][$jrow['PO_ID']]=$cu_reqsnamount;
		$job_and_trimgroup_level[$jrow['JOB_NO']][$jrow['TRIMCOSTID']]['main_woq'][$jrow['PO_ID']]=$cu_woq;
		$job_and_trimgroup_level[$jrow['JOB_NO']][$jrow['TRIMCOSTID']]['exchange_rate'][$jrow['PO_ID']]=$exchange_rate;
		$job_and_trimgroup_level[$jrow['JOB_NO']][$jrow['TRIMCOSTID']]['rate'][$jrow['PO_ID']]=$rate_ord_uom;
		$job_and_trimgroup_level[$jrow['JOB_NO']][$jrow['TRIMCOSTID']]['amount'][$jrow['PO_ID']]=$amount;
		$job_and_trimgroup_level[$jrow['JOB_NO']][$jrow['TRIMCOSTID']]['txt_delivery_date'][$jrow['PO_ID']]=$txt_delivery_date;
	}
	?>
	<input type="hidden" id="strdata" value='<? echo json_encode($job_and_trimgroup_level); ?>' style="background-color:#CCC"/>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1250" class="rpt_table" >
        <thead>
            <th width="30">SL</th>
            <th width="80">Job No</th>
            <th width="100">Ord. No</th>
            <th width="100">Trims Group</th>
            <th width="150">Description</th>
            <th width="70">Req. Qty</th>
            <th width="50">UOM</th>
            <th width="80">CU Reqsn. Qty</th>
            <th width="80">CU WO Qty</th>
            <th width="100">Sensitivity</th>
            <th width="80" style="color:#2A3FFF">Reqsn. Qty</th>
            <th width="55" style="color:#2A3FFF">Exch.Rate</th>
            <th width="80" style="color:#2A3FFF">Rate</th>
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
				if($cbo_currency_job == $cbo_currency_job){
					$exchange_rate=1;
				}
			
				$req_qnty_cons_uom = $req_qty_arr[$selectResult['PO_ID']][$selectResult['TRIMCOSTID']];
				$req_amount_cons_uom = $req_amount_arr[$selectResult['PO_ID']][$selectResult['TRIMCOSTID']];
				$rate_cons_uom = $req_amount_cons_uom/$req_qnty_cons_uom;
			
				$req_qnty_ord_uom = def_number_format($req_qnty_cons_uom/$sql_lib_item_group_array[$selectResult['TRIM_GROUP']][conversion_factor],5,"");
				$rate_ord_uom = def_number_format(($rate_cons_uom*$sql_lib_item_group_array[$selectResult['TRIM_GROUP']][conversion_factor])*$exchange_rate,5,"");
				$rate_ord_uom=number_format($rate_ord_uom,10,'.','');
				$req_amount_ord_uom = def_number_format($req_qnty_ord_uom*$rate_ord_uom,5,"");
			
				$cu_woq = $cu_booking_arr[$selectResult['JOB_NO']][$selectResult['TRIMCOSTID']]['cu_woq'][$selectResult['PO_ID']];
				$cu_amount = $cu_booking_arr[$selectResult['JOB_NO']][$selectResult['TRIMCOSTID']]['cu_amount'][$selectResult['PO_ID']];
				$bal_woq = def_number_format($req_qnty_ord_uom-$cu_woq,5,"");
				$amount = def_number_format($bal_woq*$rate_ord_uom,5,"");
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
                    <td width="30"><? echo $i;?></td>
                    <td width="80"><? echo $selectResult['JOB_NO'];?>
                    	<input type="hidden" id="txtjob_<? echo $i;?>" value="<? echo $selectResult['JOB_NO'];?>" style="width:30px" class="text_boxes" readonly/>
                    </td>
                    <td width="100"><? echo $selectResult['PO_NUMBER'];?>
                        <input type="hidden" id="txtbookingid_<? echo $i;?>" value="" readonly/>
                        <input type="hidden" id="txtpoid_<? echo $i;?>" value="<? echo $selectResult['PO_ID'];?>" readonly/>
                        <input type="hidden" id="txtcountry_<? echo $i;?>"  value="<? echo $selectResult['COUNTRY'] ?>" readonly />
                        <input type="hidden" id="txtdesc_<? echo $i;?>"  value="<? echo $selectResult['DESCRIPTION'];?>" readonly />
                        <input type="hidden" id="txtbrandsup_<? echo $i;?>"  value="<? echo $selectResult['BRAND_SUP_REF'];?>" readonly />
                    </td>
                    <td width="100" title="<? echo $sql_lib_item_group_array[$selectResult['TRIM_GROUP']][conversion_factor];  ?>">
						<? echo $trim_group_library[$selectResult['TRIM_GROUP']];?>
                        <input type="hidden" id="txttrimcostid_<? echo $i;?>" value="<? echo $selectResult['TRIMCOSTID'];?>" readonly/>
                        <input type="hidden" id="txttrimgroup_<? echo $i;?>" value="<? echo $selectResult['TRIM_GROUP'];?>" readonly/>
                    </td>
                    <td width="150"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<? echo $bgcolor; ?>" id="txtdescid_<? echo $i;?>"  value="<? echo $selectResult['DESCRIPTION'];?>" /></td>
                    <td width="70" align="right">
                        <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqqnty_<? echo $i;?>" value="<? echo number_format($req_qnty_ord_uom,4,'.','');?>"  readonly  />
                        <input type="hidden"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamount_<? echo $i;?>" value="<? echo number_format($req_amount_ord_uom,4,'.','');?>"  readonly  />
                    </td>
                    <td width="50">
						<? echo $unit_of_measurement[$sql_lib_item_group_array[$selectResult['TRIM_GROUP']][cons_uom]];?>
                        <input type="hidden" id="txtuom_<? echo $i;?>" value="<? echo $sql_lib_item_group_array[$selectResult['TRIM_GROUP']][cons_uom];?>" readonly />
                    </td>
                    <td width="80" align="right">
                        <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuwoq_<? echo $i;?>" value="<? echo number_format($selectResult['cu_woq'],4,'.','');?>" readonly  />
                        <input type="hidden" style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuamount_<? echo $i;?>" value="<? echo number_format($selectResult['cu_amount'],4,'.','');?>" readonly  />
                    </td>
                    <td width="80" align="right"><input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtbalwoq_<? echo $i;?>" value="<? echo number_format($bal_woq,4,'.',''); ?>"  readonly  /></td>
                    <td width="100"><?=create_drop_down( "cbocolorsizesensitive_".$i, 100, $size_color_sensitive,"", 1, "--Select--", "", "set_cons_break_down($i), copy_value(this.value,'cbocolorsizesensitive_',$i)","","1,2,3,4" ); ?></td>
                    <td width="80"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoq_<?=$i;?>" value="<? //echo number_format($bal_woq,4,'.','');?>" onClick="open_consumption_popup('requires/short_trims_req_multi_job_controller.php?action=consumption_popup', 'Consumtion Entry Form','txtpoid_<?=$i;?>',<?=$i;?>)" readonly /></td>
                    <td width="55"><input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtexchrate_<? echo $i;?>" value="<? echo $exchange_rate;?>" readonly /></td>
                    <td width="80" align="right">
						<?
                        $ratetexcolor="#000000";
                        $decimal=explode(".",$rate_ord_uom);
                        if(strlen($decimal[1]>6)) $ratetexcolor="#F00";
                        ?>
                        <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;   text-align:right; color:<? echo $ratetexcolor; ?>; background-color:<? echo $bgcolor; ?>" id="txtrate_<? echo $i;?>" value="<? echo $rate_ord_uom ;?>" onChange="calculate_amount(<? echo $i; ?>)" readonly />
                        <input type="hidden" style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_precost_<? echo $i;?>" value="<? echo $rate_ord_uom;?>" readonly />
                    </td>
                    <td width="80"><input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtamount_<? echo $i;?>" value="<? echo number_format($amount,4,'.','');?>"  readonly  /></td>
                    <td>
                        <input type="text"   style="width:90%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtddate_<? echo $i;?>"  class="datepicker" value="<? echo $txt_delivery_date; ?>"  readonly  />
                        <input type="hidden" id="consbreckdown_<? echo $i;?>"  value=""/>
                        <input type="hidden" id="jsondata_<? echo $i;?>"  value=""/>
                    </td>
				</tr>
				<?
				$i++;
			}
        }
        else if($cbo_level==2){
			$i=1;
			foreach ($job_and_trimgroup_level as $job_no=>$jobdata){
				foreach($jobdata as $bom_trim_dtls){
					$job_no=implode(",",array_unique($bom_trim_dtls['job_no']));
					$po_number=implode(",",$bom_trim_dtls['po_number']);
					$po_id=implode(",",$bom_trim_dtls['po_id']);
					$country=implode(",",array_unique(explode(",",implode(",",$bom_trim_dtls['country']))));
					$description=implode(",",array_unique($bom_trim_dtls['description']));
					$brand_sup_ref=implode(",",array_unique($bom_trim_dtls['brand_sup_ref']));
					$wo_pre_cost_trim_id=implode(",",array_unique($bom_trim_dtls['wo_pre_cost_trim_cost_dtls']));
					$trim_group = implode(",",array_unique($bom_trim_dtls['trim_group']));
					$uom=implode(",",array_unique($bom_trim_dtls['uom']));
				
					$req_qnty_ord_uom=array_sum($bom_trim_dtls['req_qnty']);
					$rate_ord_uom=array_sum($bom_trim_dtls['req_amount'])/array_sum($bom_trim_dtls['req_qnty']);
					$req_amount_ord_uom=array_sum($bom_trim_dtls['req_amount']);
				
					$main_woq=array_sum($bom_trim_dtls['main_woq']);
					$amount=array_sum($bom_trim_dtls['amount']);
				
					$cu_reqsnqty=array_sum($bom_trim_dtls['cu_reqsnqty']);
					$cu_amount=array_sum($bom_trim_dtls['cu_reqsnamount']);
				
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
                        <td width="30"><? echo $i;?></td>
                        <td width="80"><? echo $job_no; ?>
                        	<input type="hidden" id="txtjob_<? echo $i;?>" value="<? echo $job_no;?>" style="width:30px" class="text_boxes" readonly/>
                        </td>
                        <td width="100" style="word-wrap:break-word;word-break: break-all"><? echo $po_number; ?>
                            <input type="hidden" id="txtbookingid_<? echo $i;?>" value="" readonly/>
                            <input type="hidden" id="txtpoid_<? echo $i;?>" value="<? echo $po_id; ?>" readonly/>
                            <input type="hidden" id="txtcountry_<? echo $i;?>"  value="<? echo $country; ?>" readonly />
                            <input type="hidden" id="txtdesc_<? echo $i;?>"  value="<? echo $description; ?>" readonly />
                            <input type="hidden" id="txtbrandsup_<? echo $i;?>"  value="<? echo $brand_sup_ref;?>" readonly />
                        </td>
                        <td width="100"  title="<? echo $sql_lib_item_group_array[$trim_group][conversion_factor]; ?>"><? echo $trim_group_library[$trim_group];?>
                            <input type="hidden" id="txttrimcostid_<? echo $i;?>" value="<? echo $wo_pre_cost_trim_id;?>" readonly/>
                            <input type="hidden" id="txttrimgroup_<? echo $i;?>" value="<? echo $trim_group;?>" readonly/>
                        </td>
                        <td width="150"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<? echo $bgcolor; ?>" id="txtdescid_<? echo $i;?>"  value="<? echo $description;?>" /></td>
                        <td width="70">
                            <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqqnty_<? echo $i;?>" value="<? echo number_format($req_qnty_ord_uom,4,'.','');?>"  readonly  />
                            <input type="hidden" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamount_<? echo $i;?>" value="<? echo number_format($req_amount_ord_uom,4,'.','');?>"  readonly  />
                        </td>
                        <td width="50"><? echo $unit_of_measurement[$uom];?><input type="hidden" id="txtuom_<? echo $i;?>" value="<? echo $uom;?>" readonly /></td>
                        <td width="80">
                            <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuwoq_<? echo $i;?>" value="<? echo number_format($cu_reqsnqty,4,'.',''); ?>"  readonly  />
                            <input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuamount_<? echo $i;?>" value="<? echo number_format($cu_reqsnqty,4,'.','');?>"  readonly  />
                        </td>
                        <td width="80"><input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtbalwoq_<? echo $i;?>" value="<? echo number_format($main_woq,4,'.','');?>"  readonly  /></td>
                        <td width="100"><?=create_drop_down( "cbocolorsizesensitive_".$i, 100, $size_color_sensitive,"", 1, "--Select--", "", "set_cons_break_down($i),copy_value(this.value,'cbocolorsizesensitive_',$i)","","1,2,3,4" ); ?></td>
                        <td width="80"><input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoq_<? echo $i;?>" value="<? //echo number_format($bal_woq,4,'.','');?>" onClick="open_consumption_popup('requires/short_trims_req_multi_job_controller.php?action=consumption_popup', 'Consumtion Entry Form','txtpoid_<? echo $i;?>',<? echo $i;?>)" readonly/></td>
                        <td width="55"><input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtexchrate_<? echo $i;?>" value="<? echo $exchange_rate;?>" readonly /></td>
                        <td width="80">
							<?
                            $ratetexcolor="#000000";
                            $decimal=explode(".",$rate_ord_uom);
                        
                            if(strlen($decimal[1])>6) $ratetexcolor="#F00";
                            ?>
                            <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; color:<? echo $ratetexcolor; ?>; background-color:<? echo $bgcolor; ?>" id="txtrate_<? echo $i;?>" value="<? echo $rate_ord_uom ;?>" onChange="calculate_amount(<? echo $i; ?>)" readonly />
                        
                            <input type="hidden" style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_precost_<? echo $i;?>" value="<? echo $rate_ord_uom;?>" readonly />
                        </td>
                        <td width="80"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtamount_<? echo $i;?>" value="<? echo number_format($amount,4,'.','');?>"  readonly  /></td>
                        <td>
                            <input type="text" style="width:90%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtddate_<? echo $i;?>" class="datepicker" value="<? echo $txt_delivery_date; ?>"  readonly  />
                            <input type="hidden" id="consbreckdown_<? echo $i;?>"  value=""/>
                            <input type="hidden" id="jsondata_<? echo $i;?>"  value=""/>
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
                <th width="70"><? echo $tot_req_qty; ?></th>
                <th width="50">&nbsp;</th>
                <th width="80"><? echo $tot_cu_woq; ?></th>
                <th width="80"><? echo $tot_bal_woq; ?></th>
                <th width="100">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="55">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80"><input type="hidden" id="tot_amount" value="<? echo  $total_amount; ?>" style="width:80px" readonly /></th>
                <th><input type="hidden" id="saved_tot_amount" value="0" style="width:80px; text-align:right" readonly/></th>
            </tr>
        </tfoot>
	</table>
    <table width="1250" colspan="15" cellspacing="0" class="" border="0">
        <tr>
            <td align="center"class="button_container">
				<?=load_submit_buttons( $permission, "fnc_short_trims_req_dtls", 0,0,"reset_form('','requisition_list_view','','','')",2); ?>
            </td>
        </tr>
    </table>
	<?
	exit();
}

if ($action=="show_trim_reqsn_list"){
	extract($_REQUEST);
	$trim_group_library= return_library_array("select id, item_name from lib_item_group",'id','item_name');
	
	if($garments_nature==0) $garment_nature_cond=""; else $garment_nature_cond=" and a.garments_nature=$garments_nature";
	
	if ($start_date!="" &&  $end_date!="") $shipment_date = "and d.pub_shipment_date between '".change_date_format($start_date, "yyyy-mm-dd", "-",1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";

	$sql_lib_item_group_array=array();
	$sql_lib_item_group=sql_select("select id, item_name,conversion_factor,order_uom as cons_uom from lib_item_group");
	foreach($sql_lib_item_group as $row_sql_lib_item_group){
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][item_name]=$row_sql_lib_item_group[csf('item_name')];
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][conversion_factor]=$row_sql_lib_item_group[csf('conversion_factor')];
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][cons_uom]=$row_sql_lib_item_group[csf('cons_uom')];
	}

	$sql="select A.JOB_NO_PREFIX_NUM, A.JOB_NO, A.COMPANY_NAME, A.BUYER_NAME, A.CURRENCY_ID, A.STYLE_REF_NO, B.COSTING_PER, B.EXCHANGE_RATE, C.ID AS TRIMCOSTID, C.TRIM_GROUP, G.DESCRIPTION AS DESCRIPTION_DTLS, C.BRAND_SUP_REF, C.COUNTRY, C.RATE, D.ID AS PO_ID, D.PO_NUMBER, D.PO_QUANTITY AS PLAN_CUT, MIN(E.ID) AS ID, E.PO_BREAK_DOWN_ID, AVG(E.CONS) AS CONS, SUM(F.WO_QNTY) AS CU_WOQ, SUM(F.AMOUNT) AS CU_AMOUNT, F.ID AS BOOKING_ID, F.SENSITIVITY, F.DELIVERY_DATE, F.DESCRIPTION AS DESCRIPTION

	from wo_po_details_master a, wo_pre_cost_mst b, wo_pre_cost_trim_cost_dtls c, wo_po_break_down d, wo_pre_cost_trim_co_cons_dtls e, wo_booking_dtls f
	left join wo_trim_book_con_dtls g on g.wo_trim_booking_dtls_id=f.id
	where
	a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.job_no=f.job_no and c.id=e.wo_pre_cost_trim_cost_dtls_id and d.id=e.po_break_down_id and e.wo_pre_cost_trim_cost_dtls_id= f.pre_cost_fabric_cost_dtls_id and e.po_break_down_id=f.po_break_down_id and f.booking_type=13 and f.booking_no=$txt_req_no and a.company_name=$cbo_company_name and d.is_deleted=0 and d.status_active=1 and f.status_active=1 and f.is_deleted=0

	group by a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id, c.trim_group, g.description, c.brand_sup_ref, c.country, c.rate, d.id, d.po_number, d.po_quantity, e.po_break_down_id, f.id, f.sensitivity, f.delivery_date, f.description order by d.id,c.id";
	
	/*$sql="select A.JOB_NO_PREFIX_NUM, A.JOB_NO, A.COMPANY_NAME, A.BUYER_NAME, A.CURRENCY_ID, A.STYLE_REF_NO, B.COSTING_PER, B.EXCHANGE_RATE, C.ID as TRIMCOSTID, C.TRIM_GROUP, C.DESCRIPTION, C.BRAND_SUP_REF, C.COUNTRY, C.RATE, D.ID as PO_ID, D.PO_NUMBER, D.PO_QUANTITY as PLAN_CUT, min(E.ID) as ID, E.PO_BREAK_DOWN_ID, avg(E.CONS) as CONS

	from wo_po_details_master a, wo_pre_cost_mst b, wo_pre_cost_trim_cost_dtls c, wo_po_break_down d, wo_pre_cost_trim_co_cons_dtls e
	where a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and c.id=e.wo_pre_cost_trim_cost_dtls_id and d.id=e.po_break_down_id and a.company_name='$cbo_company_name' $garment_nature_cond and e.id in($param) and e.po_break_down_id in($data) and c.id in($pre_cost_id) and d.is_deleted=0 and d.status_active=1
	group by a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id, c.trim_group, c.description, c.brand_sup_ref, c.country, c.rate, d.id, d.po_number, d.po_quantity, e.po_break_down_id order by d.id, c.id";*/
	//echo $sql; die;
	$job_and_trimgroup_level=array();
	$i=1;
	$nameArray=sql_select( $sql );
	foreach ($nameArray as $row){
		$cbo_currency_job=$row['CURRENCY_ID'];
		$exchange_rate=$row['EXCHANGE_RATE'];
		if($cbo_currency_job==$cbo_currency_job){
			$exchange_rate=1;
		}
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMCOSTID']][$row['SENSITIVITY']][$row['DESCRIPTION']]['job_no'][$row['PO_ID']]=$row['JOB_NO'];
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMCOSTID']][$row['SENSITIVITY']][$row['DESCRIPTION']]['po_id'][$row['PO_ID']]=$row['PO_ID'];
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMCOSTID']][$row['SENSITIVITY']][$row['DESCRIPTION']]['po_number'][$row['PO_ID']]=$row['PO_NUMBER'];
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMCOSTID']][$row['SENSITIVITY']][$row['DESCRIPTION']]['country'][$row['PO_ID']]=$row['COUNTRY'];
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMCOSTID']][$row['SENSITIVITY']][$row['DESCRIPTION']]['description'][$row['PO_ID']]=$row['DESCRIPTION'];
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMCOSTID']][$row['SENSITIVITY']][$row['DESCRIPTION']][$row['TRIMCOSTID']]['brand_sup_ref'][$row['PO_ID']]=$row['BRAND_SUP_REF'];
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMCOSTID']][$row['SENSITIVITY']][$row['DESCRIPTION']]['trim_group'][$row['PO_ID']]=$row['TRIM_GROUP'];
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMCOSTID']][$row['SENSITIVITY']][$row['DESCRIPTION']]['trim_group_name'][$row['PO_ID']]=$trim_group_library[$row['TRIM_GROUP']];
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMCOSTID']][$row['SENSITIVITY']][$row['DESCRIPTION']]['wo_pre_cost_trim_cost_dtls'][$row['PO_ID']]=$row['TRIMCOSTID'];
	
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMCOSTID']][$row['SENSITIVITY']][$row['DESCRIPTION']]['uom'][$row['PO_ID']]=$sql_lib_item_group_array[$row['TRIM_GROUP']][cons_uom];
	
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMCOSTID']][$row['SENSITIVITY']][$row['DESCRIPTION']]['uom_name'][$row['PO_ID']]=$unit_of_measurement[$sql_lib_item_group_array[$row['TRIM_GROUP']][cons_uom]];
	
	
		//$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMCOSTID']][$row['SENSITIVITY']][$row['DESCRIPTION']]['woq'][$row['PO_ID']]=$row['CU_WOQ'];
		//$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMCOSTID']][$row['SENSITIVITY']][$row['DESCRIPTION']]['cu_amount'][$row['PO_ID']]=$row['CU_AMOUNT'];
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMCOSTID']][$row['SENSITIVITY']][$row['DESCRIPTION']]['bal_woq'][$row['PO_ID']]=$bal_woq;
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMCOSTID']][$row['SENSITIVITY']][$row['DESCRIPTION']]['exchange_rate'][$row['PO_ID']]=$exchange_rate;
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMCOSTID']][$row['SENSITIVITY']][$row['DESCRIPTION']]['rate'][$row['PO_ID']]=$rate_ord_uom;
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMCOSTID']][$row['SENSITIVITY']][$row['DESCRIPTION']]['amount'][$row['PO_ID']]=$amount;
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMCOSTID']][$row['SENSITIVITY']][$row['DESCRIPTION']]['delivery_date'][$row['PO_ID']]=$row['DELIVERY_DATE'];
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMCOSTID']][$row['SENSITIVITY']][$row['DESCRIPTION']]['booking_id'][$row['PO_ID']]=$row['BOOKING_ID'];
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMCOSTID']][$row['SENSITIVITY']][$row['DESCRIPTION']]['sensitivity'][$row['PO_ID']]=$row['SENSITIVITY'];
	}

	$sql_booking=sql_select("select C.JOB_NO, C.PRE_COST_FABRIC_COST_DTLS_ID as BOMID, C.PO_BREAK_DOWN_ID, C.DESCRIPTION, C.SENSITIVITY, SUM(C.WO_QNTY) AS WO_QNTY, SUM(C.AMOUNT) AS AMOUNT from wo_po_details_master a, wo_po_break_down  d, wo_booking_dtls c where a.id=d.job_id and a.job_no=c.job_no and d.id=c.po_break_down_id and c.booking_no=$txt_req_no and c.booking_type=13 and c.status_active=1 and c.is_deleted=0 group by c.job_no, c.description,c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id,c.sensitivity");
	foreach($sql_booking as $row_booking){
		$job_and_trimgroup_level[$row_booking['JOB_NO']][$row_booking['BOMID']][$row_booking['SENSITIVITY']][$row_booking['DESCRIPTION']]['woq'][$row_booking['PO_BREAK_DOWN_ID']]=$row_booking['WO_QNTY'];
		$job_and_trimgroup_level[$row_booking['JOB_NO']][$row_booking['BOMID']][$row_booking['SENSITIVITY']][$row_booking['DESCRIPTION']]['amount'][$row_booking['PO_BREAK_DOWN_ID']]=$row_booking['AMOUNT'];
	}
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1100" class="rpt_table" >
        <thead>
            <th width="30">SL</th>
            <th width="100">Job No</th>
            <th width="100">Ord. No</th>
            <th width="100">Trims Group</th>
            <th width="150">Description</th>
            <th width="80">UOM</th>
            <th width="100">Sensitivity</th>
            <th width="80">Reqsn. Qty.</th>
            <th width="80">Exch.Rate</th>
            <th width="80">Rate</th>
            <th width="80">Amount</th>
            <th>Delv. Date</th>
        </thead>
        <tbody>
        <?
        if($cbo_level==1){
			foreach ($nameArray as $selectResult){
				if ($i%2==0) $bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
			
				$cbo_currency_job=$selectResult['CURRENCY_ID'];
				$exchange_rate=$selectResult['EXCHANGE_RATE'];
				if($cbo_currency_job==$cbo_currency_job){
					$exchange_rate=1;
				}
				$woq=def_number_format($job_and_trimgroup_level[$selectResult['JOB_NO']][$selectResult['TRIMCOSTID']][$selectResult['SENSITIVITY']][$selectResult['DESCRIPTION']]['woq'][$selectResult['PO_ID']],5,"");
				$amount=def_number_format($job_and_trimgroup_level[$selectResult['JOB_NO']][$selectResult['TRIMCOSTID']][$selectResult['SENSITIVITY']][$selectResult['DESCRIPTION']]['amount'][$selectResult['PO_ID']],5,"");
				$rate=def_number_format($amount/$woq,5,"");
				$total_amount+=$amount;
				?>
				<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i; ?>" onClick="fnc_show_booking(<?=$selectResult['TRIMCOSTID'];?>,'<?=$selectResult['PO_ID']; ?>','<?=$selectResult['BOOKING_ID']; ?>');">
                    <td width="30"><?=$i;?></td>
                    <td width="100"><?=$selectResult['JOB_NO']; ?></td>
                    <td width="100"><?=$selectResult['PO_NUMBER']; ?></td>
                    <td width="100" title="<?=$sql_lib_item_group_array[$selectResult['TRIM_GROUP']][conversion_factor]; ?>"><?=$trim_group_library[$selectResult['TRIM_GROUP']]; ?></td>
                    <td width="100" title="<?=$selectResult['DESCRIPTION']; ?>"><?=$selectResult['DESCRIPTION']; ?></td>
                    <td width="80"><?=$unit_of_measurement[$sql_lib_item_group_array[$selectResult['TRIM_GROUP']][cons_uom]]; ?></td>
                    <td width="100"><?=$size_color_sensitive[$selectResult["SENSITIVITY"]]; ?> </td>
                    <td width="80" align="right"><?=number_format($woq,4,'.',''); ?></td>
                    <td width="80" align="right"><?=$exchange_rate; ?></td>
                    <td width="80" align="right"><?=number_format($amount/$woq,6,'.',''); ?></td>
                    <td width="80" align="right"><?=number_format($amount,4,'.',''); ?></td>
                    <td><?=change_date_format($selectResult['DELIVERY_DATE'],"dd-mm-yyyy","-"); ?></td>
				</tr>
				<?
				$i++;
			}
        }
    
        if($cbo_level==2){
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
							$delivery_date=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['delivery_date']));
							$woq=def_number_format(array_sum($wo_pre_cost_trim_cost_dtls['woq']),5,"");
							$amount=def_number_format(array_sum($wo_pre_cost_trim_cost_dtls['amount']),5,"");
							$rate=def_number_format($amount/$woq,5,"");
							$total_amount+=$amount;
							//echo $sensitivity."<br/>";
							?>
							<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i; ?>" onClick="fnc_show_booking(<?=$wo_pre_cost_trim_id; ?>,'<?=$po_id; ?>','<?=$booking_id; ?>');">
                                <td width="30"><?=$i;?></td>
                                <td width="100"><?=$job_no; ?></td>
                                <td width="100" style="word-wrap:break-word;word-break: break-all"><?=$po_number; ?></td>
                                <td width="100" title="<?=$sql_lib_item_group_array[$trim_group][conversion_factor]; ?>"><?=$trim_group_library[$trim_group]; ?></td>
                                <td width="100" title="<?=$description; ?>"><?=$description; ?></td>
                                <td width="80"><?=$unit_of_measurement[$uom]; ?></td>
                                <td width="100"><?=$size_color_sensitive[$sensitivity]; ?></td>
                                <td width="80" align="right"><?=number_format($woq,4,'.',''); ?></td>
                                <td width="80" align="right"><?=$exchange_rate; ?></td>
                                <td width="80" align="right"><?=number_format($amount/$woq,4,'.',''); ?></td>
                                <td width="80" align="right"><?=number_format($amount,4,'.',''); ?></td>
                                <td><?=change_date_format($delivery_date,"dd-mm-yyyy","-"); ?></td>
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

if ($action=="show_trims_requisition"){
	extract($_REQUEST);
	$trim_group_library= return_library_array("select id, item_name from lib_item_group",'id','item_name');
	
	if($garments_nature==0) $garment_nature_cond=""; else $garment_nature_cond=" and a.garments_nature=$garments_nature";

	if ($start_date!="" &&  $end_date!="") $shipment_date = "and d.pub_shipment_date between '".change_date_format($start_date, "yyyy-mm-dd", "-",1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";

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

	$cu_booking_arr=array();
	$sql_cu_booking=sql_select("select c.job_no, c.pre_cost_fabric_cost_dtls_id, c.po_break_down_id, sum(c.wo_qnty) as cu_wo_qnty, sum(c.amount) as cu_amount from wo_po_details_master a, wo_po_break_down  d, wo_booking_dtls c where a.job_no=d.job_no_mst and a.job_no=c.job_no and  d.id=c.po_break_down_id and a.company_name=$cbo_company_name and c.pre_cost_fabric_cost_dtls_id=$pre_cost_id and c.status_active=1 and c.is_deleted=0 group by c.job_no, c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id");
	foreach($sql_cu_booking as $row_cu_booking){
		$cu_booking_arr[$row_cu_booking[csf('job_no')]][$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]]['cu_woq'][$row_cu_booking[csf('po_break_down_id')]]=$row_cu_booking[csf('cu_wo_qnty')];
		$cu_booking_arr[$row_cu_booking[csf('job_no')]][$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]]['cu_amount'][$row_cu_booking[csf('po_break_down_id')]]=$row_cu_booking[csf('cu_amount')];
	}
	
	$cu_booking_arr=array(); $cu_reqqty_arr=array();
	$sql_cu_booking=sql_select("select c.job_no, c.pre_cost_fabric_cost_dtls_id, c.entry_form_id, c.po_break_down_id, sum(c.wo_qnty) as cu_wo_qnty, sum(c.amount) as cu_amount from wo_po_details_master a, wo_po_break_down  d, wo_booking_dtls c where a.job_no=d.job_no_mst and a.job_no=c.job_no and  d.id=c.po_break_down_id and a.company_name=$cbo_company_name $shipment_date and c.status_active=1 and c.is_deleted=0 and c.booking_type in (2,13) group by c.job_no, c.pre_cost_fabric_cost_dtls_id, c.entry_form_id, c.po_break_down_id");//and c.entry_form_id=716
	foreach($sql_cu_booking as $row_cu_booking){
		if($row_cu_booking[csf('entry_form_id')]==716)
		{
			$cu_reqqty_arr[$row_cu_booking[csf('job_no')]][$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]]['cu_woq'][$row_cu_booking[csf('po_break_down_id')]] = $row_cu_booking[csf('cu_wo_qnty')];
			$cu_reqqty_arr[$row_cu_booking[csf('job_no')]][$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]]['cu_amount'][$row_cu_booking[csf('po_break_down_id')]] = $row_cu_booking[csf('cu_amount')];
		}
		else
		{
			$cu_booking_arr[$row_cu_booking[csf('job_no')]][$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]]['cu_woq'][$row_cu_booking[csf('po_break_down_id')]] = $row_cu_booking[csf('cu_wo_qnty')];
			$cu_booking_arr[$row_cu_booking[csf('job_no')]][$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]]['cu_amount'][$row_cu_booking[csf('po_break_down_id')]] = $row_cu_booking[csf('cu_amount')];
		}
	}

	$sql="select A.JOB_NO_PREFIX_NUM, A.JOB_NO, A.COMPANY_NAME, A.BUYER_NAME, A.CURRENCY_ID, A.STYLE_REF_NO, B.COSTING_PER, B.EXCHANGE_RATE, C.ID AS TRIMCOSTID, C.TRIM_GROUP, C.DESCRIPTION AS DESCRIPTION_PRE_COST, C.BRAND_SUP_REF, C.COUNTRY, C.RATE, D.ID AS PO_ID, D.PO_NUMBER, D.PO_QUANTITY AS PLAN_CUT, MIN(E.ID) AS ID, AVG(E.CONS) AS CONS, SUM(g.REQUIRMENT) AS CU_WOQ, SUM(g.AMOUNT) AS CU_AMOUNT, F.ID AS BOOKING_ID, F.SENSITIVITY, F.DELIVERY_DATE, G.DESCRIPTION

	from wo_po_details_master a, wo_pre_cost_mst b, wo_pre_cost_trim_cost_dtls c, wo_po_break_down d, wo_pre_cost_trim_co_cons_dtls e, wo_booking_dtls f
	left join wo_trim_book_con_dtls g on g.wo_trim_booking_dtls_id=f.id
	where
	a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.job_no=f.job_no and c.id=e.wo_pre_cost_trim_cost_dtls_id and d.id=e.po_break_down_id and e.wo_pre_cost_trim_cost_dtls_id= f.pre_cost_fabric_cost_dtls_id and e.po_break_down_id=f.po_break_down_id and f.booking_type=13 and f.booking_no=$txt_req_no and
	f.id in($booking_id) and a.company_name=$cbo_company_name and e.wo_pre_cost_trim_cost_dtls_id=$pre_cost_id and d.is_deleted=0 and d.status_active=1 and f.status_active=1 and f.is_deleted=0

	group by a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id, c.trim_group, c.description, c.brand_sup_ref, c.country, c.rate, d.id, d.po_number, d.po_quantity, e.po_break_down_id, f.id, f.sensitivity, f.delivery_date, g.description
	order by d.id, c.id";

	$job_and_trimgroup_level=array();
	$i=1;
	$nameArray=sql_select( $sql );
	foreach ($nameArray as $row){
		$cbo_currency_job=$row['CURRENCY_ID'];
		$exchange_rate=$row['EXCHANGE_RATE'];
		if($cbo_currency_job==$cbo_currency_job){
			$exchange_rate=1;
		}
	
		$req_qnty_cons_uom=$req_qty_arr[$row['PO_ID']][$row['TRIMCOSTID']];
		$req_amount_cons_uom=$req_amount_arr[$row['PO_ID']][$row['TRIMCOSTID']];
		$rate_cons_uom=$req_amount_cons_uom/$req_qnty_cons_uom;
	
		$req_qnty_ord_uom=def_number_format($req_qnty_cons_uom/$sql_lib_item_group_array[$row['TRIM_GROUP']][conversion_factor],5,"");
		$rate_ord_uom=def_number_format(($rate_cons_uom*$sql_lib_item_group_array[$row['TRIM_GROUP']][conversion_factor])*$exchange_rate,5,"");
		$req_amount_ord_uom=def_number_format($req_qnty_ord_uom*$rate_ord_uom,5,"");
	
		$cu_reqsnq=$cu_reqqty_arr[$row['JOB_NO']][$row['TRIMCOSTID']]['cu_woq'][$row['PO_ID']];
		$cu_reqsnamount=$cu_reqqty_arr[$row['JOB_NO']][$row['TRIMCOSTID']]['cu_amount'][$row['PO_ID']];
		
		$cu_woq=$cu_booking_arr[$row['JOB_NO']][$row['TRIMCOSTID']]['cu_woq'][$row['PO_ID']];
		$cu_amount=$cu_booking_arr[$row['JOB_NO']][$row['TRIMCOSTID']]['cu_amount'][$row['PO_ID']];
	
		$bal_woq=def_number_format($req_qnty_ord_uom-$cu_woq,5,"");
		$amount=def_number_format($rate_ord_uom*$bal_woq,5,"");
	
		$total_req_amount+=$req_amount;
		$total_cu_amount+=$row['CU_AMOUNT'];
	
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMCOSTID']]['job_no'][$row['PO_ID']]=$row['JOB_NO'];
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMCOSTID']]['po_id'][$row['PO_ID']]=$row['PO_ID'];
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMCOSTID']]['po_number'][$row['PO_ID']]=$row['PO_NUMBER'];
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMCOSTID']]['country'][$row['PO_ID']]=$row['COUNTRY'];
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMCOSTID']]['description'][$row['PO_ID']]=$row['DESCRIPTION'];
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMCOSTID']]['brand_sup_ref'][$row['PO_ID']]=$row['BRAND_SUP_REF'];
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMCOSTID']]['trim_group'][$row['PO_ID']]=$row['TRIM_GROUP'];
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMCOSTID']]['trim_group_name'][$row['PO_ID']]=$trim_group_library[$row['TRIM_GROUP']];
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMCOSTID']]['wo_pre_cost_trim_cost_dtls'][$row['PO_ID']]=$row['TRIMCOSTID'];
	
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMCOSTID']]['req_qnty'][$row['PO_ID']]=$req_qnty_ord_uom;
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMCOSTID']]['uom'][$row['PO_ID']]=$sql_lib_item_group_array[$row['TRIM_GROUP']][cons_uom];
	
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMCOSTID']]['uom_name'][$row['PO_ID']]=$unit_of_measurement[$sql_lib_item_group_array[$row['TRIM_GROUP']][cons_uom]];
	
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMCOSTID']]['req_amount'][$row['PO_ID']]=$req_amount_ord_uom;
		
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMCOSTID']]['cu_reqsnq'][$row['PO_ID']]=$cu_reqsnq;
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMCOSTID']]['cu_reqsnamt'][$row['PO_ID']]=$cu_reqsnamount;
	
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMCOSTID']]['cu_woq'][$row['PO_ID']]=$cu_woq;
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMCOSTID']]['cu_amount'][$row['PO_ID']]=$cu_amount;
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMCOSTID']]['bal_woq'][$row['PO_ID']]=$bal_woq;
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMCOSTID']]['exchange_rate'][$row['PO_ID']]=$exchange_rate;
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMCOSTID']]['rate'][$row['PO_ID']]=$rate_ord_uom;
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMCOSTID']]['amount'][$row['PO_ID']]=$amount;
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMCOSTID']]['txt_delivery_date'][$row['PO_ID']]=$row['DELIVERY_DATE'];
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMCOSTID']]['booking_id'][$row['PO_ID']]=$row['BOOKING_ID'];
		$job_and_trimgroup_level[$row['JOB_NO']][$row['TRIMCOSTID']]['sensitivity'][$row['PO_ID']]=$row['SENSITIVITY'];
	}

	$sql_booking=sql_select("select c.job_no, c.pre_cost_fabric_cost_dtls_id, c.po_break_down_id, sum(c.wo_qnty) as wo_qnty, sum(c.amount) as amount from wo_po_details_master a, wo_po_break_down  d, wo_booking_dtls c where a.job_no=d.job_no_mst and a.job_no=c.job_no and  d.id=c.po_break_down_id and c.booking_no=$txt_req_no and c.pre_cost_fabric_cost_dtls_id=$pre_cost_id  and c.id in($booking_id) and c.booking_type=13 and c.status_active=1 and c.is_deleted=0 group by c.job_no, c.pre_cost_fabric_cost_dtls_id, c.po_break_down_id");
	foreach($sql_booking as $row_booking){
		$job_and_trimgroup_level[$row_booking[csf('job_no')]][$row_booking[csf('pre_cost_fabric_cost_dtls_id')]]['woq'][$row_booking[csf('po_break_down_id')]]=$row_booking[csf('wo_qnty')];
		$job_and_trimgroup_level[$row_booking[csf('job_no')]][$row_booking[csf('pre_cost_fabric_cost_dtls_id')]]['amount'][$row_booking[csf('po_break_down_id')]]=$row_booking[csf('amount')];
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
            <th width="80">CU Reqsn. Qty</th>
            <th width="80">CU WO Qty</th>
            <th width="100">Sensitivity</th>
            <th width="80" style="color:#2A3FFF">Reqsn. Qty</th>
            <th width="55" style="color:#2A3FFF">Exch.Rate</th>
            <th width="80" style="color:#2A3FFF">Rate</th>
            <th width="80">Amount</th>
            <th>Delv. Date</th>
        </thead>
    </table>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1250" class="rpt_table" id="tbl_list_search" >
        <tbody>
        <?
        if($cbo_level==1){
			foreach($nameArray as $selectResult){
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			
				$cbo_currency_job=$selectResult['CURRENCY_ID'];
				$exchange_rate=$selectResult['EXCHANGE_RATE'];
				if($cbo_currency_job==$cbo_currency_job){
					$exchange_rate=1;
				}
			
				$req_qnty_cons_uom=$req_qty_arr[$selectResult['PO_ID']][$selectResult['TRIMCOSTID']];
				$req_amount_cons_uom=$req_amount_arr[$selectResult['PO_ID']][$selectResult['TRIMCOSTID']];
				$rate_cons_uom=$req_amount_cons_uom/$req_qnty_cons_uom;
			
				$req_qnty_ord_uom=def_number_format($req_qnty_cons_uom/$sql_lib_item_group_array[$selectResult['TRIM_GROUP']][conversion_factor],5,"");
				$rate_ord_uom=def_number_format(($rate_cons_uom*$sql_lib_item_group_array[$selectResult['TRIM_GROUP']][conversion_factor])*$exchange_rate,5,"");
				$req_amount_ord_uom=def_number_format($req_qnty_ord_uom*$rate_ord_uom,5,"");
				
				$cu_reqsnq=$cu_reqqty_arr[$selectResult['JOB_NO']][$selectResult['TRIMCOSTID']]['cu_reqsnq'][$selectResult['PO_ID']];
			
				$cu_woq=$cu_booking_arr[$selectResult['JOB_NO']][$selectResult['TRIMCOSTID']]['cu_woq'][$selectResult['PO_ID']];
				$cu_amount=$cu_booking_arr[$selectResult['JOB_NO']][$selectResult['TRIMCOSTID']]['cu_amount'][$selectResult['PO_ID']];
			
				$bal_woq=def_number_format($req_qnty_ord_uom-$cu_woq,5,"");
			
			
				$woq=$job_and_trimgroup_level[$selectResult['JOB_NO']][$selectResult['TRIMCOSTID']]['woq'][$selectResult['PO_ID']];
				$amount=$job_and_trimgroup_level[$selectResult['JOB_NO']][$selectResult['TRIMCOSTID']]['amount'][$selectResult['PO_ID']];
				$rate=$amount/$woq;
				$total_amount+=$amount;
				?>
				<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i; ?>">
                    <td width="30"><?=$i;?></td>
                    <td width="80"><?=$selectResult['JOB_NO']; ?>
                    	<input type="hidden" id="txtjob_<?=$i; ?>" value="<?=$selectResult['JOB_NO']; ?>" style="width:30px"/>
                    </td>
                    <td width="100"><?=$selectResult['PO_NUMBER']; ?>
                        <input type="hidden" id="txtbookingid_<?=$i; ?>" value="<?=$selectResult['BOOKING_ID']; ?>" readonly/>
                        <input type="hidden" id="txtpoid_<?=$i; ?>" value="<?=$selectResult['PO_ID']; ?>" readonly/>
                        <input type="hidden" id="txtcountry_<?=$i; ?>" value="<?=$selectResult['COUNTRY']; ?>" readonly />
                        <input type="hidden" id="txtdesc_<?=$i; ?>" value="<?=$selectResult['DESCRIPTION_PRE_COST']; ?>" readonly />
                        <input type="hidden" id="txtbrandsup_<?=$i; ?>" value="<?=$selectResult['BRAND_SUP_REF']; ?>" readonly />
                    </td>
                    <td width="100" title="<?=$sql_lib_item_group_array[$selectResult['TRIM_GROUP']][conversion_factor]; ?>"><?=$trim_group_library[$selectResult['TRIM_GROUP']]; ?>
                        <input type="hidden" id="txttrimcostid_<?=$i; ?>" value="<?=$selectResult['TRIMCOSTID']; ?>" readonly/>
                        <input type="hidden" id="txttrimgroup_<?=$i; ?>" value="<?=$selectResult['TRIM_GROUP']; ?>" readonly/>
                    </td>
                    <td width="150"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; background-color:<?=$bgcolor; ?>" id="txtdescid_<?=$i; ?>" value="<?=$selectResult['DESCRIPTION'];?>" <? if($disAbled){echo "disabled";}else{ echo "";}?> /></td>
                    <td width="70" align="right">
                        <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtreqqnty_<?=$i; ?>" value="<?=number_format($req_qnty_ord_uom,4,'.',''); ?>" readonly  />
                        <input type="hidden" id="txtreqamount_<?=$i; ?>" value="<?=number_format($req_amount_ord_uom,4,'.',''); ?>"  readonly />
                    </td>
                    <td width="50"><?=$unit_of_measurement[$sql_lib_item_group_array[$selectResult['TRIM_GROUP']][cons_uom]]; ?>
                    	<input type="hidden" id="txtuom_<?=$i; ?>" value="<?=$sql_lib_item_group_array[$selectResult['TRIM_GROUP']][cons_uom]; ?>" readonly />
                    </td>
                    <td width="80" align="right">
                        <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtcuwoq_<?=$i; ?>" value="<?=number_format($cu_reqsnq,4,'.',''); ?>"  readonly  />
                        <input type="hidden" id="txtcuamount_<?=$i; ?>" value="<?=number_format($cu_amount,4,'.',''); ?>" readonly />
                    </td>
                    <td width="80" align="right"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<?=$bgcolor; ?>" id="txtbalwoq_<?=$i;?>" value="<?=number_format($cu_woq,4,'.',''); ?>" readonly /></td>
                    <td width="100" align="right"><?=create_drop_down( "cbocolorsizesensitive_".$i, 100, $size_color_sensitive,"", 1, "--Select--", $selectResult['SENSITIVITY'], "set_cons_break_down($i),copy_value(this.value,'cbocolorsizesensitive_',$i)","","1,2,3,4" ); ?></td>
                    <td width="80" align="right">
                        <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoq_<?=$i; ?>" value="<?=number_format($woq,4,'.',''); ?>" onClick="open_consumption_popup('requires/short_trims_req_multi_job_controller.php?action=consumption_popup', 'Consumtion Entry Form','txtpoid_<?=$i; ?>',<?=$i; ?>);" readonly />
                    </td>
                    <td width="55" align="right">
                    <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtexchrate_<?=$i; ?>" value="<?=$exchange_rate; ?>" readonly />
                
                    </td>
                    <td width="80" align="right">
                        <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<?=$bgcolor; ?>" id="txtrate_<?=$i; ?>" value="<?=number_format($rate,4,'.',''); ?>" onChange="calculate_amount(<?=$i; ?>);" readonly />
                        <input type="hidden" style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<?=$bgcolor; ?>" id="txtrate_precost_<?=$i; ?>" value="<?=$rate_ord_uom; ?>" readonly />
                    </td>
                    <td width="80" align="right"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<?=$bgcolor; ?>" id="txtamount_<?=$i; ?>" value="<?=number_format($amount,4,'.',''); ?>" readonly /></td>
                    <td>
                        <input type="text" style="width:90%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<?=$bgcolor; ?>" id="txtddate_<?=$i; ?>" class="datepicker" value="<?=change_date_format($selectResult['DELIVERY_DATE'],"dd-mm-yyyy","-"); ?>" readonly />
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
					
					$cu_reqsnqty=array_sum($wo_pre_cost_trim_cost_dtls['cu_reqsnq']);
					$cu_reqsnamount=array_sum($wo_pre_cost_trim_cost_dtls['cu_reqsnamt']);
				
					$bal_woq=array_sum($wo_pre_cost_trim_cost_dtls['bal_woq']);
					$cu_woq=array_sum($wo_pre_cost_trim_cost_dtls['cu_woq']);
					$cu_amount=array_sum($wo_pre_cost_trim_cost_dtls['cu_amount']);
				
					$woq=array_sum($wo_pre_cost_trim_cost_dtls['woq']);
					$amount=array_sum($wo_pre_cost_trim_cost_dtls['amount']);
					$rate=$amount/$woq;
					$total_amount+=$amount;
					?>
					<tr bgcolor="<?= $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i; ?>">
                        <td width="30"><?=$i; ?></td>
                        <td width="80"><?=$job_no; ?>
                            <input type="hidden" id="txtjob_<?=$i; ?>" value="<?=$job_no; ?>" style="width:30px" />
                        </td>
                        <td width="100" style="word-wrap:break-word;word-break: break-all"><?=$po_number; ?>
                            <input type="hidden" id="txtbookingid_<?=$i; ?>" value="<?=$booking_id; ?>" readonly/>
                            <input type="hidden" id="txtpoid_<?=$i; ?>" value="<?=$po_id; ?>" readonly/>
                            <input type="hidden" id="txtcountry_<?=$i; ?>"  value="<?=$country; ?>" readonly />
                            <input type="hidden" id="txtdesc_<?=$i; ?>"  value="<?=$description; ?>" readonly />
                            <input type="hidden" id="txtbrandsup_<?=$i; ?>"  value="<?=$brand_sup_ref;?>" readonly />
                        </td>
                        <td width="100" title="<?=$sql_lib_item_group_array[$trim_group][conversion_factor]; ?>"><?=$trim_group_library[$trim_group]; ?>
                            <input type="hidden" id="txttrimcostid_<?=$i; ?>" value="<?=$wo_pre_cost_trim_id; ?>" readonly/>
                            <input type="hidden" id="txttrimgroup_<?=$i; ?>" value="<?=$trim_group; ?>" readonly/>
                        </td>
                        <td width="150"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<?=$bgcolor; ?>" id="txtdescid_<?=$i; ?>" value="<?=$description; ?>" <? if($disAbled){echo "disabled";}else{ echo "";}?> /></td>
                        <td width="70" align="right">
                            <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtreqqnty_<?=$i;?>" value="<?=number_format($req_qnty_ord_uom,4,'.','');?>"  readonly  />
                            <input type="hidden" style="width:100%;" id="txtreqamount_<?=$i; ?>" value="<?=number_format($req_amount_ord_uom,4,'.',''); ?>" readonly  />
                        </td>
                        <td width="50"><?=$unit_of_measurement[$uom]; ?><input type="hidden" id="txtuom_<?=$i; ?>" value="<?=$uom;?>" readonly /></td>
                        <td width="80" align="right">
                            <input type="text" style="width:100%; height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<?=$bgcolor; ?>" id="txtcuwoq_<?=$i; ?>" value="<?=number_format($cu_reqsnqty,4,'.',''); ?>" readonly />
                            <input type="hidden" id="txtcuamount_<?=$i;?>" value="<?=$cu_reqsnamount; ?>" readonly />
                        </td>
                        <td width="80" align="right"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtbalwoq_<?=$i; ?>" value="<?=number_format($cu_woq,4,'.',''); ?>" readonly /></td>
                        <td width="100" align="right"><?=create_drop_down( "cbocolorsizesensitive_".$i, 100, $size_color_sensitive,"", 1, "--Select--", $sensitivity, "set_cons_break_down($i),copy_value(this.value,'cbocolorsizesensitive_',$i)","","1,2,3,4" ); ?></td>
                        <td width="80" align="right"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:#FFC" id="txtwoq_<?=$i; ?>" value="<?=number_format($woq,4,'.',''); ?>" onClick="open_consumption_popup('requires/short_trims_req_multi_job_controller.php?action=consumption_popup', 'Consumtion Entry Form','txtpoid_<?=$i; ?>',<?=$i; ?>);" readonly /></td>
                        <td width="55" align="right"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtexchrate_<?=$i; ?>" value="<?=$exchange_rate; ?>" readonly /></td>
                        <td width="80" align="right">
                            <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtrate_<?=$i; ?>" value="<?=number_format($rate,4,'.',''); ?>" onChange="calculate_amount(<?=$i; ?>);" readonly/>
                            <input type="hidden" style="width:100%;" id="txtrate_precost_<?=$i; ?>" value="<?=$rate_ord_uom; ?>" readonly />
                        </td>
                        <td width="80" align="right"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtamount_<?=$i;?>" value="<?=number_format($amount,4,'.',''); ?>" readonly />
                        </td>
                        <td>
                            <input type="text" style="width:90%; font-family:Verdana, Geneva, sans-serif; font-size:11px; background-color:<?=$bgcolor; ?>" id="txtddate_<?=$i; ?>" class="datepicker" value="<?=change_date_format($delivery_date,"dd-mm-yyyy","-"); ?>" readonly  />
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
                <th width="70"><?=number_format($tot_req_qty,4,'.',''); ?></th>
                <th width="50">&nbsp;</th>
                <th width="80"><?=number_format($tot_cu_woq,4,'.',''); ?></th>
                <th width="80"><?=number_format($tot_bal_woq,4,'.',''); ?></th>
                <th width="100">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="55">&nbsp;</th>
                <th width="80"><input type="hidden" id="tot_amount" value="<?=number_format($total_amount,4,'.',''); ?>" class="text_boxes_numeric" style="width:140px"/><? //echo  $total_amount; ?></th>
                <th width="80"><input type="hidden" id="tot_amount" value="<?=number_format($total_amount,4,'.',''); ?>" class="text_boxes_numeric" style="width:140px"/><? //echo  $total_amount; ?></th>
                <th width=""><input type="hidden" id="saved_tot_amount" value="0" style="width:80px; text-align:right" readonly/></th>
            </tr>
        </tfoot>
    </table>
    <table width="1250" colspan="15" cellspacing="0" class="" border="0">
        <tr>
            <td align="center"class="button_container">
            	<?=load_submit_buttons( $permission, "fnc_short_trims_req_dtls", 1,0,"reset_form('','booking_list_view','','','')",2); ?>
            </td>
        </tr>
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
		set_sum_value( 'qty_sum', 'qty_');
	}

	function poportionate_qty(qty){
		var txtwoq=document.getElementById('txtwoq').value;
		var txtwoq_qty=document.getElementById('txtwoq_qty').value*1;
		var rowCount = $('#tbl_consmption_cost tbody tr').length;
		for(var i=1; i<=rowCount; i++){
			var poreqqty=$('#poreqqty_'+i).val();
			var txtwoq_cal =number_format_common((txtwoq_qty/txtwoq) * (poreqqty),5,0);
			$('#qty_'+i).val(txtwoq_cal);
			calculate_requirement(i);
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
		calculate_requirement(j);
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
		set_sum_value( 'woqty_sum', 'woqny_');
		calculate_amount(i);
	}

	function set_sum_value(des_fil_id,field_id){
		if(des_fil_id=='qty_sum'){ var ddd={dec_type:5,comma:0,currency:0}; }
		if(des_fil_id=='excess_sum'){ var ddd={dec_type:5,comma:0,currency:0}; }
		if(des_fil_id=='woqty_sum'){ var ddd={dec_type:5,comma:0,currency:0}; }
		if(des_fil_id=='amount_sum'){ var ddd={dec_type:5,comma:0,currency:0}; }
		if(des_fil_id=='pcs_sum'){ var ddd={dec_type:6,comma:0}; }
		var rowCount = $('#tbl_consmption_cost tbody tr').length;
		math_operation( des_fil_id, field_id, '+', rowCount,ddd);
	}

	function copy_value(value,field_id,i){
		var gmtssizesid=document.getElementById('gmtssizesid_'+i).value;
		var pocolorid=document.getElementById('pocolorid_'+i).value;
		var rowCount = $('#tbl_consmption_cost tbody tr').length;
		var copy_basis=$('input[name="copy_basis"]:checked').val()

		for(var j=i; j<=rowCount; j++){
			if(field_id=='des_' || field_id=='brndsup_' || field_id=='itemcolor_' || field_id=='itemsizes_'){
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
			
			if(field_id=='qty_' || field_id=='excess_' || field_id=='rate_'){
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
				
				if(field_id=='qty_' || field_id=='excess_')
				{
					calculate_requirement(j);
					set_sum_value('qty_sum','qty_');
				}
				if(field_id=='rate_')
				{
					calculate_amount(j);
				}
			}
		}
	}

	function calculate_amount(i){
		var rate=(document.getElementById('rate_'+i).value)*1;
		var woqny=(document.getElementById('woqny_'+i).value)*1;
		var amount=number_format_common((rate*woqny),5,0);
		document.getElementById('amount_'+i).value=amount;
		set_sum_value('amount_sum', 'amount_');
		calculate_avg_rate();
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
		
        if($txt_country=="") $txt_country_cond=""; else $txt_country_cond ="and c.country_id in ($txt_country)";
        
		$booking_no=str_replace("'","",$txt_req_no);
		
        $process_loss_method=return_field_value("process_loss_method", "variable_order_tracking", "company_name=$cbo_company_name  and variable_list=18 and item_category_id=4 and status_active=1 and is_deleted=0");
        $tot_po_qty=0;
        $sql_po_qty=sql_select("select b.id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id in($txt_po_id)  $txt_country_cond  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty"); //,c.item_number_id
        foreach($sql_po_qty as$sql_po_qty_row){
			$po_qty_arr[$sql_po_qty_row[csf('id')]]=$sql_po_qty_row[csf('order_quantity_set')];
			$tot_po_qty+=$sql_po_qty_row[csf('order_quantity_set')];
        }
		//echo $txt_pre_des.'DDDD';
        ?>
        <div align="center" style="width:1180px;" >
            <fieldset>
                <form id="consumptionform_1" autocomplete="off">
                    <table width="1180" cellspacing="0" class="rpt_table" border="0" id="tbl_consmption_cost" rules="all">
                        <thead>
                        	<tr>
                                <th colspan="14" id="td_sync_msg" style="color:#FF0000"></th>
                            </tr>
                            <tr>
                                <th width="40" colspan="14">
                                <input type="hidden" id="cons_breck_down" name="cons_breck_down" value="" />
                                <input type="hidden" id="txtwoq" value="<? echo $txt_req_quantity;?>"/>
                                Reqsn. Qty:<input type="text" id="txtwoq_qty" class="text_boxes_numeric" onBlur="poportionate_qty(this.value)" value="<? echo $txtwoq; ?>"/>
                                <input type="radio" name="copy_basis" value="0" checked>Copy to All
                                <input type="radio" name="copy_basis" value="1">Gmts Size Wise
                                <input type="radio" name="copy_basis" value="2">Gmts Color Wise
                                <input type="radio" name="copy_basis" value="10">No Copy
                                <input type="hidden" id="process_loss_method_id" name="process_loss_method_id" value="<? echo $process_loss_method; ?>"/>
                                <input type="hidden" id="po_qty" name="po_qty" value="<? echo $tot_po_qty; ?>"/>
								<input type="hidden" id="desc_id" name="desc_id" />
                                </th>
                            </tr>
                            <tr>
                                <th width="30">SL</th>
                                <th width="100">Article No.</th>
                                <th width="100">Gmts. Color</th>
                                <th width="70">Gmts. Size</th>
                                <th width="100">Description</th>
                                <th width="80">Brand/Sup Ref</th>
                                <th width="100">Item Color</th>
                                <th width="80">Item Size</th>
                                <th width="70">Reqsn. Qty</th>
                                <th width="50">Excess %</th>
                                <th width="70">Reqsn. Qty.</th>
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
                        $booking_data=sql_select("select id, wo_trim_booking_dtls_id, description, brand_supplier, item_color, item_size, cons,process_loss_percent, requirment, rate, amount, pcs, color_size_table_id from wo_trim_book_con_dtls where status_active=1 and is_deleted=0 and booking_no='$booking_no' $txt_job_no_cond1 $txt_update_dtlsCond");
						//echo "select id, wo_trim_booking_dtls_id, description, brand_supplier, item_color, item_size, cons,process_loss_percent, requirment, rate, amount, pcs, color_size_table_id from wo_trim_book_con_dtls where status_active=1 and is_deleted=0 and booking_no='$booking_no' $txt_job_no_cond1 $txt_update_dtlsCond";
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
							$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.color_number_id,min(c.color_order) as color_order, sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where  a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id order by b.id, color_order"; 
							$gmt_size_edb=1;
							$item_size_edb=1;
                        }
                        else if($cbo_colorsizesensitive==2){
							//$req_qty_arr=$trims->getQtyArray_by_orderPrecostdtlsidAndGmtssize();
							//$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidAndGmtssize();
							$req_qty_arr=$trims->getQtyArray_by_orderPrecostdtlsidGmtssizeAndArticle();
							$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidGmtssizeAndArticle();
							$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.size_number_id,c.article_number,min(c.size_order) as size_order,min(e.item_size) as item_size,sum(c.order_quantity) as order_quantity ,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.article_number,c.size_number_id order by b.id,size_order";
							$gmt_color_edb=1;
							$item_color_edb=1;
                        }
                        else if($cbo_colorsizesensitive==3){
							$req_qty_arr=$trims->getQtyArray_by_orderPrecostdtlsidAndGmtscolor();
							$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidAndGmtscolor();
							$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.color_number_id,min(c.color_order) as color_order,sum(c.order_quantity) as order_quantity ,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id order by b.id, color_order";
							$gmt_size_edb=1;
							$item_size_edb=1;
                        }
                        else if($cbo_colorsizesensitive==4){
							//$req_qty_arr=$trims->getQtyArray_by_OrderPrecostdtlsidGmtscolorAndGmtssize();
							//$req_amount_arr=$trims->getAmountArray_by_OrderPrecostdtlsidGmtscolorAndGmtssize();
							$req_qty_arr=$trims->getQtyArray_by_OrderPrecostdtlsidGmtscolorGmtssizeAndArticle();
							$req_amount_arr=$trims->getAmountArray_by_OrderPrecostdtlsidGmtscolorGmtssizeAndArticle();
							$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.color_number_id,c.article_number,c.size_number_id,min(c.color_order) as color_order,min(c.size_order) as size_order,min(e.item_size) as item_size,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id,c.article_number,c.size_number_id  order by b.id, color_order,size_order";
                        }
                        else{
							$req_qty_arr=$trims->getQtyArray_by_orderAndPrecostdtlsid();
							$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsid();
							$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty order by b.id";
                        }
						//echo $sql;

                        $po_color_level_data_arr=array(); $po_size_level_data_arr=array(); $po_no_sen_level_data_arr=array();  $po_color_size_level_data_arr=array();
                        $data_array=sql_select($sql);
                        if ( count($data_array)>0){
							$i=0;
							foreach( $data_array as $row ){
								$data=explode('_',$data_array_cons[$i]);
								$i++;
								$item_color = $booking_data_arr[$row[csf('color_size_table_id')]][item_color];
								if($item_color==0 || $item_color=="" ){
									$item_color = $row[csf('color_number_id')];
								}
								$item_size=$booking_data_arr[$row[csf('color_size_table_id')]][item_size];
								if($item_size==0 || $item_size == ""){
									$item_size=$row[csf('item_size')];
								}

								$rate=$booking_data_arr[$row[csf('color_size_table_id')]][rate];
								if($rate==0 || $rate==""){
									$rate=$txt_avg_price;
								}

								$description=$booking_data_arr[$row[csf('color_size_table_id')]][description];

								if($description==""){
									$description=$txt_pre_des;
								}

								//echo $description.'='.$txt_pre_des;
								$brand_supplier=$booking_data_arr[$row[csf('color_size_table_id')]][brand_supplier];
								if($brand_supplier==""){
									$brand_supplier=$txt_pre_brand_sup;
								}
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

						$piNumber=0;
						$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no='$txt_req_no' and b.item_group='".$txt_trim_group_id."' and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
						if($pi_number){
						$piNumber=1;
						}
						$recvNumber=0;
						$recv_number=return_field_value( "recv_number", "inv_receive_master a,inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no='$txt_req_no' and b.item_group_id='".$txt_trim_group_id."' and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
						if($recv_number){
						$recvNumber=1;
						}

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
								if($item_color==0 || $item_color=="" ){
									$item_color = $row[csf('color_number_id')];
								}

								$item_size=$booking_data_arr[$row[csf('color_size_table_id')]][item_size];
								if($item_size==0 || $item_size == ""){
									$item_size=$size_library[$row[csf('size_number_id')]];
								}
								$rate=$booking_data_arr[$row[csf('color_size_table_id')]][rate];
								if($rate==0 || $rate==""){
									$rate=$txt_avg_price;
								}
								$description=$booking_data_arr[$row[csf('color_size_table_id')]][description];
								if($description==""){
									$description=$txt_pre_des;
								}

								$brand_supplier=$booking_data_arr[$row[csf('color_size_table_id')]][brand_supplier];
								if($brand_supplier==""){
									$brand_supplier=$txt_pre_brand_sup;
								}
								if($txtwoq_cal>0){
									$i++;
									?>
									<tr id="break_<?=$i; ?>" align="center">
                                        <td><?=$i; ?></td>
                                        <td><input type="text" id="poarticle_<?=$i; ?>" name="poarticle_<?=$i; ?>" class="text_boxes" style="width:87px" value="<?=$row[csf('article_number')]; ?>"  readonly /></td>
    
                                        <td>
                                            <input type="text" id="pocolor_<?=$i;?>" name="pocolor_<?=$i; ?>" class="text_boxes" style="width:87px" value="<?=$color_library[$row[csf('color_number_id')]]; ?>" <? if($gmt_color_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> readonly/>
                                            <input type="hidden" id="pocolorid_<?=$i; ?>" name="pocolorid_<?=$i; ?>" style="width:85px" value="<?=$row[csf('color_number_id')]; ?>" readonly />
                                            <input type="hidden" id="poid_<?=$i;?>" name="poid_<?=$i; ?>" style="width:87px" value="<?=$row[csf('id')]; ?>" />
                                            <input type="hidden" id="poqty_<?=$i;?>" name="poqty_<?=$i; ?>" style="width:87px" value="<?=$po_qty_arr[$row[csf('id')]]; ?>" readonly />
                                            <input type="hidden" id="poreqqty_<?=$i; ?>" name="poreqqty_<?=$i; ?>" style="width:87px" value="<?=$txtwoq_cal; ?>" readonly />
                                        </td>
                                        <td>
                                            <input type="text" id="gmtssizes_<?=$i; ?>" name="gmtssizes_<?=$i; ?>" class="text_boxes" style="width:58px" value="<?=$size_library[$row[csf('size_number_id')]]; ?>" <? if($gmt_size_edb || $piNumber || $recvNumber ){ echo "disabled";}else { echo "";} ?> readonly />
                                            <input type="hidden" id="gmtssizesid_<?=$i;?>" name="gmtssizesid_<?=$i;?>" style="width:55px" value="<?=$row[csf('size_number_id')]; ?>" readonly/>
                                        </td>
                                        <td><input type="text" id="des_<?=$i; ?>" name="des_<?=$i; ?>" class="text_boxes" style="width:87px" value="<?=$description; ?>" onChange="copy_value(this.value,'des_',<?=$i; ?>);" <? if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> /></td>
                                        <td><input type="text" id="brndsup_<?=$i;?>" name="brndsup_<?=$i;?>" class="text_boxes" style="width:78px" value="<?=$brand_supplier; ?>" onChange="copy_value(this.value,'brndsup_',<?=$i; ?>);" <? if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> /></td>
                                        <td><input type="text" id="itemcolor_<?=$i; ?>" value="<?=$color_library[$item_color]; ?>" name="itemcolor_<?=$i;?>" class="text_boxes" style="width:87px" onChange="copy_value(this.value,'itemcolor_',<?=$i;?>);"  <? if($item_color_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> /></td>
                                        <td>
                                        <input type="text" id="itemsizes_<?=$i;?>" name="itemsizes_<?=$i; ?>" class="text_boxes" style="width:58px" onChange="copy_value(this.value,'itemsizes_',<?=$i; ?>);" value="<?=$item_size; ?>" <? if($item_size_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> /></td>
                                        <td>
                                            <input type="hidden" id="reqqty_<?=$i; ?>" name="reqqty_<?=$i; ?>" style="width:55px" value="<?=$txtwoq_cal ?>" readonly/>
                                            <input type="text" id="qty_<?=$i; ?>" onBlur="validate_sum(<?=$i; ?>);" onChange="set_sum_value('qty_sum','qty_'); set_sum_value('woqty_sum', 'woqny_'); calculate_requirement(<?=$i; ?>); copy_value(this.value,'qty_',<?=$i; ?>);" name="qty_<?=$i; ?>" class="text_boxes_numeric" style="width:58px" placeholder="<?=$txtwoq_cal; ?>" value="<?=$booking_data_arr[$row[csf('color_size_table_id')]][cons]; ?>"/>
                                        </td>
                                        <td><input type="text" id="excess_<?=$i;?>" onBlur="set_sum_value('excess_sum','excess_');" name="excess_<?=$i; ?>" class="text_boxes_numeric" style="width:38px" onChange="calculate_requirement(<?=$i; ?>); set_sum_value('excess_sum','excess_'); set_sum_value('woqty_sum','woqny_'); copy_value(this.value,'excess_',<?=$i; ?>);" value="<?=$booking_data_arr[$row[csf('color_size_table_id')]][process_loss_percent]; ?>" disabled/></td>
                                        <td><input type="text" id="woqny_<?=$i; ?>" onBlur="set_sum_value('woqty_sum','woqny_');" onChange="set_sum_value('woqty_sum','woqny_')" name="woqny_<?=$i;?>" class="text_boxes_numeric" style="width:58px" value="<?=$booking_data_arr[$row[csf('color_size_table_id')]][requirment]; ?>" readonly /></td>
                                        <td><input type="text" id="rate_<?=$i; ?>" name="rate_<?=$i; ?>" class="text_boxes_numeric" style="width:108px" onChange="calculate_amount(<?=$i; ?>); set_sum_value('amount_sum','amount_'); copy_value(this.value,'rate_',<?=$i; ?>);" value="<?=$rate; ?>" <? if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> /></td>
                                        <td><input type="text" id="amount_<?=$i; ?>" name="amount_<?=$i; ?>" onBlur="set_sum_value('amount_sum','amount_');" class="text_boxes_numeric" style="width:100px" value="<?=$booking_data_arr[$row[csf('color_size_table_id')]][amount]; ?>" readonly /></td>
                                        <td>
                                            <input type="text" id="pcs_<?=$i; ?>" name="pcs_<?=$i; ?>" onBlur="set_sum_value('pcs_sum','pcs_');" class="text_boxes_numeric" style="width:80px" value="<?=$row[csf('order_quantity')]; ?>" readonly>
                                            <input type="hidden" id="pcsset_<?=$i; ?>" name="pcsset_<?=$i; ?>" onBlur="set_sum_value('pcs_sum','pcs_');" style="width:50px" value="<?=$row[csf('order_quantity_set')]; ?>" readonly>
                                            <input type="hidden" id="colorsizetableid_<?=$i; ?>" name="colorsizetableid_<?=$i; ?>" style="width:65px" value="<?=$row[csf('color_size_table_id')]; ?>" readonly />
                                            <input type="hidden" id="updateid_<?=$i;?>" name="updateid_<?=$i;?>" class="text_boxes" style="width:65px" value="<?=$booking_data_arr[$row[csf('color_size_table_id')]][id]; ?>" readonly />
                                        </td>
									</tr>
								<?
								}
							}
                        }
                        
                        $level_arr=array();
                        $gmt_color_edb="";
                        $item_color_edb="";
                        $gmt_size_edb="";
                        $item_size_edb="";
                        if($cbo_colorsizesensitive==1){
							$sql="select min(b.id) as id , min(c.id) as color_size_table_id,c.color_number_id,min(c.color_order) as color_order,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.color_number_id order by  color_order";
							$level_arr=$po_color_level_data_arr;
							$gmt_size_edb=1;
							$item_size_edb=1;
                        }
                        else if($cbo_colorsizesensitive==2){
							$sql="select min(b.id) as id , min(c.id) as color_size_table_id,c.article_number,c.size_number_id,min(c.size_order) as size_order,min(e.item_size) as item_size,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.size_number_id,c.article_number order by size_order";
							$level_arr=$po_size_level_data_arr;
							$gmt_color_edb=1;
							$item_color_edb=1;
                        }
                        else if($cbo_colorsizesensitive==3){
							$sql="select min(b.id) as id, min(c.id) as color_size_table_id,c.color_number_id,min(c.color_order) as color_order,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.color_number_id order by  color_order";
							$level_arr=$po_color_level_data_arr;
							$gmt_size_edb=1;
							$item_size_edb=1;
                        }
                        else if($cbo_colorsizesensitive==4){
							$sql="select min(b.id) as id ,min(c.id) as color_size_table_id,c.color_number_id,c.size_number_id,c.article_number,min(c.color_order) as color_order,min(c.size_order) as size_order,min(e.item_size) as item_size,sum(c.order_quantity) as order_quantity  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.color_number_id,c.article_number,c.size_number_id  order by  color_order,size_order";
							$level_arr=$po_color_size_level_data_arr;
                        }
                        else{
							$sql="select b.job_no_mst,min(b.id) as id , min(c.id) as color_size_table_id,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.job_no_mst";
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
								if($item_color==0 || $item_color=="" ){
									$item_color = $row[csf('color_number_id')];
								}

								$item_size=$booking_data_arr[$row[csf('color_size_table_id')]][item_size];
								if($item_size==0 || $item_size == ""){
									$item_size=$size_library[$row[csf('size_number_id')]];
								}

								$rate=$booking_data_arr[$row[csf('color_size_table_id')]][rate];
								if($rate==0 || $rate==""){
									$rate=$txt_avg_price;
								}

								$description=$booking_data_arr[$row[csf('color_size_table_id')]][description];
								//echo $description.'='.$txt_pre_des.'<br/>';
								if($description==""){
									$description=$txt_pre_des;
								}

								$brand_supplier=$booking_data_arr[$row[csf('color_size_table_id')]][brand_supplier];
								if($brand_supplier==""){
									$brand_supplier=$txt_pre_brand_sup;
								}
								if($txtwoq_cal>0){
									$i++;
								?>
									<tr id="break_<?=$i; ?>" align="center">
                                        <td><?=$i; ?></td>
                                        <td><input type="text" id="poarticle_<?=$i; ?>" name="poarticle_<?=$i; ?>" class="text_boxes" style="width:87px" value="<?=$row[csf('article_number')]; ?>"  readonly /></td>
                                        <td>
                                            <input type="text" id="pocolor_<?=$i; ?>" name="pocolor_<?=$i; ?>" class="text_boxes" style="width:87px" value="<?=$color_library[$row[csf('color_number_id')]]; ?>"  <? if($gmt_color_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> readonly/>
                                            <input type="hidden" id="pocolorid_<?=$i; ?>" name="pocolorid_<?=$i; ?>" style="width:85px" value="<?=$row[csf('color_number_id')]; ?>" readonly />
                                            <input type="hidden" id="poid_<?=$i; ?>" name="poid_<?=$i; ?>" style="width:85px" value="<?=$row[csf('id')]; ?>" readonly />
                                            <input type="hidden" id="poqty_<?=$i; ?>" name="poqty_<?=$i; ?>" style="width:85px" value="<?=$po_qty; ?>" />
                                            <input type="hidden" id="poreqqty_<?=$i; ?>" name="poreqqty_<?=$i; ?>" style="width:85px" value="<?=$txtwoq_cal; ?>" readonly />
                                        </td>
                                        <td>
                                            <input type="text" id="gmtssizes_<?=$i; ?>" name="gmtssizes_<?=$i; ?>" class="text_boxes" style="width:58px" value="<?=$size_library[$row[csf('size_number_id')]]; ?>" <? if($gmt_size_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?>/>
                                            <input type="hidden" id="gmtssizesid_<?=$i;?>" name="gmtssizesid_<?=$i;?>" style="width:50px" value="<?=$row[csf('size_number_id')]; ?>" readonly />
                                        </td>
                                        <td><input type="text" id="des_<?=$i; ?>" name="des_<?=$i; ?>" class="text_boxes" style="width:87px" value="<?=$description;?>" onChange="copy_value(this.value,'des_',<?=$i;?>)" <? if( $piNumber || $recvNumber ){ echo "disabled";}else { echo "";} ?> /></td>
                                        <td><input type="text" id="brndsup_<?=$i; ?>" name="brndsup_<?=$i; ?>" class="text_boxes" style="width:67px" value="<?=$brand_supplier; ?>" onChange="copy_value(this.value,'brndsup_',<?=$i;?>)" <? if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> /></td>
                                        <td><input type="text" id="itemcolor_<?=$i;?>"  value="<?=$color_library[$item_color]; ?>" name="itemcolor_<?=$i;?>" class="text_boxes" style="width:87px" onChange="copy_value(this.value,'itemcolor_',<?=$i;?>);" <? if($item_color_edb || $piNumber || $recvNumber ){ echo "disabled";}else { echo "";} ?> /></td>
                                        <td><input type="text" id="itemsizes_<?=$i; ?>" name="itemsizes_<?=$i; ?>" class="text_boxes" style="width:58px" onChange="copy_value(this.value,'itemsizes_',<?=$i; ?>);" value="<?=$item_size; ?>"  <? if($item_size_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> /></td>
                                        <td>
                                            <input type="hidden" id="reqqty_<?=$i; ?>" name="reqqty_<?=$i; ?>" style="width:50px" value="<?=$txtwoq_cal ?>" readonly/>
                                            <input type="text" id="qty_<?=$i; ?>" onBlur="validate_sum(<?=$i; ?>);" onChange="set_sum_value('qty_sum', 'qty_'); set_sum_value('woqty_sum','woqny_'); calculate_requirement(<?=$i; ?>); copy_value(this.value,'qty_',<?=$i; ?>);" name="qty_<?=$i; ?>" class="text_boxes_numeric" style="width:58px" placeholder="<?=$txtwoq_cal; ?>" value="<? if($booking_cons>0){echo $booking_cons;} ?>"/>
                                        </td>
                                        <td><input type="text" id="excess_<?=$i;?>" onBlur="set_sum_value('excess_sum','excess_');" name="excess_<?=$i; ?>" class="text_boxes_numeric" style="width:38px" onChange="calculate_requirement(<?=$i; ?>); set_sum_value('excess_sum','excess_'); set_sum_value('woqty_sum','woqny_'); copy_value(this.value,'excess_',<?=$i; ?>);" value="<?=$booking_data_arr[$row[csf('color_size_table_id')]][process_loss_percent]; ?>" disabled/></td>
                                        <td><input type="text" id="woqny_<?=$i;?>" onBlur="set_sum_value('woqty_sum','woqny_');" onChange="set_sum_value('woqty_sum','woqny_');" name="woqny_<?=$i; ?>" class="text_boxes_numeric" style="width:70px" value="<?  if($booking_qty){echo $booking_qty;} ?>" readonly /></td>
                                        <td><input type="text" id="rate_<?=$i; ?>" name="rate_<?=$i; ?>" class="text_boxes_numeric" style="width:108px" onChange="calculate_amount(<?=$i; ?>); set_sum_value('amount_sum','amount_'); copy_value(this.value,'rate_',<?=$i; ?>);" value="<?=$rate; ?>" <? if($piNumber || $recvNumber){ echo "disabled";}else { echo "";} ?> /></td>
                                        <td><input type="text" id="amount_<?=$i; ?>" name="amount_<?=$i; ?>" onBlur="set_sum_value('amount_sum','amount_');" class="text_boxes_numeric" style="width:87px"  value="<?=$booking_data_arr[$row[csf('color_size_table_id')]][amount]; ?>" readonly /></td>
                                        <td>
                                            <input type="text" id="pcs_<?=$i; ?>" name="pcs_<?=$i; ?>" onBlur="set_sum_value('pcs_sum','pcs_');" class="text_boxes_numeric" style="width:80px" value="<?=$row[csf('order_quantity')]; ?>" readonly>
                                            <input type="hidden" id="pcsset_<?=$i; ?>" name="pcsset_<?=$i; ?>" onBlur="set_sum_value('pcs_sum','pcs_');" style="width:50px" value="<?=$order_quantity_set; ?>" readonly>
                                            <input type="hidden" id="colorsizetableid_<?=$i; ?>" name="colorsizetableid_<?=$i; ?>" style="width:55px" value="<?=$row[csf('color_size_table_id')]; ?>" readonly />
                                            <input type="hidden" id="updateid_<?=$i; ?>" name="updateid_<?=$i; ?>" style="width:55px" value="<?=$booking_data_arr[$row[csf('color_size_table_id')]][id]; ?>" readonly />
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
                               <th width="80">&nbsp;</th>
                               <th width="100">&nbsp;</th>
                               <th width="80">&nbsp;</th>
                               <th width="70"><input type="text" id="qty_sum" name="qty_sum" class="text_boxes_numeric" style="width:58px"  readonly></th>
                               <th width="50"><input type="text" id="excess_sum"  name="excess_sum" class="text_boxes_numeric" style="width:38px" readonly></th>
                               <th width="70"><input type="text" id="woqty_sum"  name="woqty_sum" class="text_boxes_numeric" style="width:58px" readonly></th>
                               <th width="120"><input type="text" id="rate_sum"  name="rate_sum" class="text_boxes_numeric" style="width:108px" readonly></th>
                               <th width="100"><input type="text" id="amount_sum" name="amount_sum" class="text_boxes_numeric" style="width:87px" readonly></th>
                               <th>
                                   <input type="hidden" id="json_data" name="json_data" style="width:50px" value='<?=json_encode($level_arr); ?>' readonly>
                                   <input type="text" id="pcs_sum" name="pcs_sum" class="text_boxes_numeric" style="width:80px" readonly>
                               </th>
                            </tr>
                        </tfoot>
                    </table>

                    <table width="1180" cellspacing="0" class="" border="0" rules="all">
                        <tr>
                            <td align="center" width="100%"> <input type="button" class="formbutton" value="Close" onClick="js_set_value()"/> </td>
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
		poportionate_qty(<?=$txtwoq; ?>);
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
		$('#td_sync_msg').html("Requisition Info not synchronized with order entry and pre-costing. order entry or pre-costing has updated after Requisition entry.");
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
	$txt_pre_des=$data[10];
	$txt_pre_brand_sup=$data[11];
	$cbo_level=$data[12];

	if($txt_job_no==""){
		$txt_job_no_cond=""; $txt_job_no_cond1="";
	}
	else{
		$txt_job_no_cond ="and a.job_no='$txt_job_no'"; $txt_job_no_cond1 ="and job_no='$txt_job_no'";
	}

	//if($txt_country=="") $txt_country_cond=""; else $txt_country_cond ="and c.country_id in ($txt_country)";

	$process_loss_method=return_field_value("process_loss_method", "variable_order_tracking", "company_name=$cbo_company_name and variable_list=18 and item_category_id=4 and status_active=1 and is_deleted=0");
	$sql_po_qty=sql_select("select b.id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id in($txt_po_id)  $txt_country_cond  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty"); //,c.item_number_id
	$tot_po_qty=0;
	foreach($sql_po_qty as $sql_po_qty_row){
		$po_qty_arr[$sql_po_qty_row[csf('id')]]=$sql_po_qty_row[csf('order_quantity_set')];
		$tot_po_qty+=$sql_po_qty_row[csf('order_quantity_set')];
	}
	$sql_lib_item_group_array=array();
	$sql_lib_item_group=sql_select("select id, item_name,conversion_factor, order_uom as cons_uom from lib_item_group");
	foreach($sql_lib_item_group as $row_sql_lib_item_group){
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][item_name]=$row_sql_lib_item_group[csf('item_name')];
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][conversion_factor]=$row_sql_lib_item_group[csf('conversion_factor')];
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][cons_uom]=$row_sql_lib_item_group[csf('cons_uom')];
	}

	$booking_data_arr=array();
	$booking_data=sql_select("select id, wo_trim_booking_dtls_id, description, brand_supplier, item_color, item_size, cons,process_loss_percent, requirment, rate, amount, pcs, color_size_table_id from wo_trim_book_con_dtls where wo_trim_booking_dtls_id in($txt_update_dtls_id) and status_active=1 and is_deleted=0");
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
	$cu_booking_data=sql_select("select a.pre_cost_fabric_cost_dtls_id, b.id, b.wo_trim_booking_dtls_id, b.po_break_down_id, b.color_number_id, b.gmts_sizes, b.requirment from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id=b.wo_trim_booking_dtls_id and b.po_break_down_id in($txt_po_id) and a.entry_form_id=716 and a.status_active=1 and a.is_deleted=00 and b.status_active=1 and b.is_deleted=0 ");
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
	$gmt_color_edb=""; $item_color_edb=""; $gmt_size_edb=""; $item_size_edb="";
	if($cbo_colorsizesensitive==1){
		$req_qty_arr=$trims->getQtyArray_by_orderPrecostdtlsidAndGmtscolor();
		$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidAndGmtscolor();
		 $sql="select b.id, b.po_number, b.po_quantity, min(c.id) as color_size_table_id, c.color_number_id, min(c.color_order) as color_order, sum(c.order_quantity) as order_quantity, (sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and b.id=c.po_break_down_id and b.id=e.po_break_down_id and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.id in($txt_po_id) $txt_country_cond and d.id=$cbo_trim_precost_id group by b.id, b.po_number, b.po_quantity, a.total_set_qnty, c.color_number_id order by b.id, color_order";
		$gmt_size_edb="disabled";
		$item_size_edb="disabled";
	}
	else if($cbo_colorsizesensitive==2){
	    $req_qty_arr=$trims->getQtyArray_by_orderPrecostdtlsidAndGmtssize();
		$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidAndGmtssize();
		$sql="select b.id, b.po_number, b.po_quantity, min(c.id) as color_size_table_id, c.size_number_id, min(c.size_order) as size_order, min(e.item_size) as item_size, sum(c.order_quantity) as order_quantity, (sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.id in($txt_po_id) $txt_country_cond and d.id=$cbo_trim_precost_id group by b.id, b.po_number, b.po_quantity, a.total_set_qnty, c.size_number_id order by b.id, size_order";
		$gmt_color_edb="disabled";
		$item_color_edb="disabled";
	}
	else if($cbo_colorsizesensitive==3){
		$req_qty_arr=$trims->getQtyArray_by_orderPrecostdtlsidAndGmtscolor();
		$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidAndGmtscolor();
		$sql="select b.id, b.po_number, b.po_quantity, min(c.id) as color_size_table_id, c.color_number_id, min(c.color_order) as color_order, sum(c.order_quantity) as order_quantity, (sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and b.id=c.po_break_down_id and b.id=e.po_break_down_id and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.id in($txt_po_id) $txt_country_cond and d.id=$cbo_trim_precost_id group by b.id, b.po_number, b.po_quantity, a.total_set_qnty, c.color_number_id order by b.id, color_order";
		$gmt_size_edb="disabled";
		$item_size_edb="disabled";
	}
	else if($cbo_colorsizesensitive==4){

		$req_qty_arr=$trims->getQtyArray_by_OrderPrecostdtlsidGmtscolorAndGmtssize();
		$req_amount_arr=$trims->getAmountArray_by_OrderPrecostdtlsidGmtscolorAndGmtssize();

		 $sql="select b.id, b.po_number, b.po_quantity,min(c.id) as color_size_table_id, c.color_number_id, c.size_number_id, min(c.color_order) as color_order, min(c.size_order) as size_order, min(e.item_size) as item_size, sum(c.order_quantity) as order_quantity, (sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.id in($txt_po_id) $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number, b.po_quantity, a.total_set_qnty, c.color_number_id, c.size_number_id order by b.id, color_order, size_order";
	}
	else{
		$req_qty_arr=$trims->getQtyArray_by_orderAndPrecostdtlsid();
	    $req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsid();
		$sql="select b.id, b.po_number, b.po_quantity, min(c.id) as color_size_table_id, sum(c.order_quantity) as order_quantity, (sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id) $txt_country_cond and d.id=$cbo_trim_precost_id group by b.id, b.po_number, b.po_quantity, a.total_set_qnty order by b.id";
	}
	//echo $sql;
	//print_r($req_qty_arr);
	$data_array=sql_select($sql);
	if(count($data_array)>0)
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
				//echo $data[14].'='.$data[8].'='.$req_qnty_ord_uom.'<br>';
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
		$sql="select min(b.id) as id , min(c.id) as color_size_table_id,c.color_number_id,min(c.color_order) as color_order,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.color_number_id order by  color_order";
		$level_arr=$po_color_level_data_arr;
		$gmt_size_edb="disabled";
		$item_size_edb="disabled";
	}
	else if($cbo_colorsizesensitive==2){
		$sql="select min(b.id) as id , min(c.id) as color_size_table_id,c.size_number_id,min(c.size_order) as size_order,min(e.item_size) as item_size,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.size_number_id order by size_order";
		$level_arr=$po_size_level_data_arr;
		$gmt_color_edb="disabled";
		$item_color_edb="disabled";
	}
	else if($cbo_colorsizesensitive==3){
		$sql="select min(b.id) as id, min(c.id) as color_size_table_id,c.color_number_id,min(c.color_order) as color_order,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.color_number_id order by  color_order";
		$level_arr=$po_color_level_data_arr;
		$gmt_size_edb="disabled";
		$item_size_edb="disabled";
	}
	else if($cbo_colorsizesensitive==4){
		$sql="select min(b.id) as id ,min(c.id) as color_size_table_id,c.color_number_id,c.size_number_id,min(c.color_order) as color_order,min(c.size_order) as size_order,min(e.item_size) as item_size,min(e.item_size) as item_size,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.color_number_id,c.size_number_id  order by  color_order,size_order";
		$level_arr=$po_color_size_level_data_arr;
	}
	else{
		$sql="select b.job_no_mst,min(b.id) as id , min(c.id) as color_size_table_id,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.job_no_mst";
		$level_arr=$po_no_sen_level_data_arr;
	}
	$data_array=sql_select($sql);
	//print_r($level_arr);
	$cons_breck_down="";
	if ( count($data_array)>0 && $cbo_level==2)
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
	
	$shortBookingno=return_field_value( "booking_no", "wo_booking_mst","requisition_no=$txt_reqsn_no and status_active=1 and is_deleted=0");
	if($shortBookingno){
		echo "shortBookingno**".str_replace("'","",$txt_reqsn_no)."**".$shortBookingno;
		disconnect($con);die;
	}
	
	if ($operation==0){
		$con = connect();
		if($db_type==0) mysql_query("BEGIN");
		
		$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'KSTR', date("Y",time()), 5, "select booking_no_prefix, booking_no_prefix_num from wo_booking_mst where company_id=$cbo_company_name and booking_type=13 and entry_form=716 and to_char(insert_date,'YYYY')=".date('Y',time())." order by id desc", "booking_no_prefix", "booking_no_prefix_num"));
		
		$id=return_next_id("id", "wo_booking_mst", 1) ;
		
		$field_array="id, booking_type, is_short, item_category, entry_form, item_from_precost, booking_no_prefix, booking_no_prefix_num, booking_no, company_id, buyer_id, booking_date, cbo_level, responsible_dept, responsible_person, division_id, ready_to_approved, reason, remarks, short_booking_available, inserted_by, insert_date, status_active, is_deleted";
		$data_array ="(".$id.",13,1,4,716,1,'".$new_booking_no[1]."',".$new_booking_no[2].",'".$new_booking_no[0]."',".$cbo_company_name.",".$cbo_buyer_name.",".$txt_req_date.",".$cbo_level.",".$cbo_responsible_dept.",".$cbo_responsible_person.",".$cbo_division_id.",".$cbo_ready_to_approved.",".$txt_reason.",".$txt_remarks.",'".$variable_short_booking."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";

		$rID=sql_insert("wo_booking_mst",$field_array,$data_array,0);
		//echo "10**".$rID; oci_rollback($con); disconnect($con); die;
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
		if($db_type==0){
			mysql_query("BEGIN");
		}
		$booking_mst_id=str_replace("'","",$booking_mst_id);
		$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_req_no");
		if($is_approved==3){
			$is_approved=1;
		}
		if($is_approved==1){
			echo "app1**".str_replace("'","",$txt_req_no);
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
		$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_req_no  and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
		if($pi_number){
			echo "pi1**".str_replace("'","",$txt_req_no)."**".$pi_number;
			 disconnect($con);die;
		}
		
		$recv_number=return_field_value( "recv_number", "inv_receive_master a,inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_req_no  and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
		if($recv_number){
			echo "recv1**".str_replace("'","",$txt_req_no)."**".$recv_number;
			 disconnect($con);die;
		}
		
		$field_array_up="buyer_id*booking_date*responsible_dept*responsible_person*division_id*ready_to_approved*reason*remarks*updated_by*update_date";

		$data_array_up ="".$cbo_buyer_name."*".$txt_req_date."*".$cbo_responsible_dept."*".$cbo_responsible_person."*".$cbo_division_id."*".$cbo_ready_to_approved."*".$txt_reason."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$rID=sql_update("wo_booking_mst",$field_array_up,$data_array_up,"booking_no","".$txt_req_no."",0);
		//echo "10**".$rID; oci_rollback($con); disconnect($con); die;
		if($db_type==2 || $db_type==1 ){
			if($rID){
				oci_commit($con);
				echo "1**".str_replace("'","",$txt_req_no)."**".$booking_mst_id;
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_req_no)."**".$booking_mst_id;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2){
		$con = connect();
		if($db_type==0){
			mysql_query("BEGIN");
		}
		$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_req_no");
		if($is_approved==3){
			$is_approved=1;
		}
		if($is_approved==1){
			echo "app1**".str_replace("'","",$txt_req_no);
			 disconnect($con);die;
		}
		$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_req_no  and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
		if($pi_number){
			echo "pi1**".str_replace("'","",$txt_req_no)."**".$pi_number;
			 disconnect($con);die;
		}
		
		$recv_number=return_field_value( "recv_number", "inv_receive_master a,inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_req_no  and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
		if($recv_number){
			echo "recv1**".str_replace("'","",$txt_req_no)."**".$recv_number;
			 disconnect($con);die;
		}
		$is_received_id=return_field_value( "subcon_job", "subcon_ord_mst","order_no=$txt_req_no and order_id is not null and entry_form=255 and status_active=1 and is_deleted=0");
		//echo "10** select id from subcon_ord_mst where order_no=$txt_req_no and order_id is not null and entry_form=255".$is_received_id; die;
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

		//$rID=execute_query( "delete from wo_booking_mst where  booking_no =".$txt_req_no."",0);
		//$rID1=execute_query( "delete from wo_booking_dtls where  booking_no =".$txt_req_no."",0);
		//$rID2=execute_query( "delete from wo_trim_book_con_dtls where  booking_no =".$txt_req_no."",0);
		$flag=1;
		$rID=execute_query( "update wo_booking_mst set status_active=0, is_deleted=1, delete_cause='$delete_cause', updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where booking_no=$txt_req_no",0);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		
		$rID1=execute_query("update wo_booking_dtls set status_active=0, is_deleted=1, delete_cause='$delete_cause', updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where booking_no=$txt_req_no",0);
		if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		
		$rID2=execute_query("update wo_trim_book_con_dtls set status_active=0, is_deleted=1 where booking_no=$txt_req_no",0);
		if($rID2==1 && $flag==1) $flag=1; else $flag=0;
		
		//echo "10**".$rID.'='.$rID1.'='.$rID2.'='.$flag; oci_rollback($con); disconnect($con); die;
		
		if($db_type==2 || $db_type==1 ){
			if($flag==1){
				oci_commit($con);
				echo "2**".str_replace("'","",$txt_req_no);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_req_no);
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
	$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_req_no");
	if($is_approved==3){
		$is_approved=1;
	}
	if($is_approved==1){
		echo "app1**".str_replace("'","",$txt_req_no);
		 disconnect($con);die;
	}
	
	$shortBookingno=return_field_value( "booking_no", "wo_booking_mst","requisition_no=$txt_reqsn_no and status_active=1 and is_deleted=0");
	if($shortBookingno){
		echo "shortBookingno**".str_replace("'","",$txt_reqsn_no)."**".$shortBookingno;
		disconnect($con);die;
	}

	if ($operation==0){
		$con = connect();
		if( check_table_status( $_SESSION['menu_id'], 1 )==0 ){
			echo "15**0";
			disconnect($con);die;
		}
		
		$new_array_color=array(); $newColorArr=array();
		for ($i=1;$i<=$total_row;$i++){
			$cbocolorsizesensitive="cbocolorsizesensitive_".$i;
			$txtwoq="txtwoq_".$i;
			$consbreckdown="consbreckdown_".$i;
			
			if(str_replace("'",'',$$consbreckdown) !=''){
				$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
				for($c=0;$c < count($consbreckdown_array);$c++){
					$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
					if(str_replace("'","",$consbreckdownarr[4])!="")
					{
						if (!in_array(str_replace("'","",$consbreckdownarr[4]),$new_array_color)){
							$color_id = return_id( str_replace("'","",$consbreckdownarr[4]), $color_library, "lib_color", "id,color_name","716");
							$newColorArr[str_replace("'","",$consbreckdownarr[4])]=$color_id;
						}
						else $newColorArr[str_replace("'","",$consbreckdownarr[4])]=$color_id;
					}
					else $newColorArr[str_replace("'","",$consbreckdownarr[4])]=0;
				}
			}
		}
		
		$id_dtls=return_next_id( "id", "wo_booking_dtls", 1) ;
		$field_array1="id, pre_cost_fabric_cost_dtls_id, po_break_down_id, job_no, booking_no, booking_mst_id, booking_type, is_short, entry_form_id, trim_group, uom, sensitivity, wo_qnty, exchange_rate, rate, amount, delivery_date, description, country_id_string, inserted_by, insert_date, status_active, is_deleted";

		$field_array2="id, wo_trim_booking_dtls_id, booking_no, booking_mst_id, job_no, po_break_down_id, color_number_id, gmts_sizes, description, brand_supplier, item_color, item_size, cons, process_loss_percent, requirment, rate, amount, pcs, color_size_table_id, article_number, status_active, is_deleted";

		$add_comma=0; $flag=1;
		$id1=return_next_id( "id", "wo_trim_book_con_dtls", 1 );
		//echo "10**";
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
			
			if(str_replace("'","",$$txtddate)!='') $txtdlvdate=date("d-M-Y",strtotime(str_replace("'","",$$txtddate))); else $txtdlvdate="";

			$data_array1 ="(".$id_dtls.",".$$txttrimcostid.",".$$txtpoid.",".$$txtjob_id.",".$txt_req_no.",".$booking_mst_id.",13,1,716,".$$txttrimgroup.",".$$txtuom.",".$$cbocolorsizesensitive.",".$$txtwoq.",".$$txtexchrate.",".$$txtrate.",".$$txtamount.",'".$txtdlvdate."',".$$txtdescid.",".$$txtcountry.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";

			//	CONS break down===============================================================================================
			if(str_replace("'",'',$$consbreckdown) !=''){
				$data_array2="";
				$rID_de1=execute_query("delete from wo_trim_book_con_dtls where  wo_trim_booking_dtls_id =".$$txtbookingid."",0);
				if($rID_de1==1 && $flag==1) $flag=1; else $flag=0;
				$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
				for($c=0;$c < count($consbreckdown_array);$c++){
					$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
					$color_id =$newColorArr[str_replace("'","",$consbreckdownarr[4])];
					if ($c!=0) $data_array2 .=",";
					$data_array2 .="(".$id1.",".$id_dtls.",".$txt_req_no.",".$booking_mst_id.",".$$txtjob_id.",".$$txtpoid.",'".$consbreckdownarr[0]."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$color_id."','".$consbreckdownarr[5]."','".$consbreckdownarr[6]."','".$consbreckdownarr[7]."','".$consbreckdownarr[8]."','".$consbreckdownarr[9]."','".$consbreckdownarr[10]."','".$consbreckdownarr[11]."','".$consbreckdownarr[12]."','".$consbreckdownarr[14]."',1,0)";
					$id1=$id1+1;
					$add_comma++;
				}
			}
			//CONS break down end===============================================================================================
			$rID1=sql_insert("wo_booking_dtls",$field_array1,$data_array1,0);
			//echo "INSERT INTO wo_booking_dtls (".$field_array1.") VALUES ".$data_array1; oci_rollback($con); check_table_status( $_SESSION['menu_id'],0); disconnect($con); die;
			
			if($rID1==1 && $flag==1) $flag=1; else $flag=0;
			$rID2=1;
			if($data_array2 !=""){
				$rID2=sql_insert("wo_trim_book_con_dtls",$field_array2,$data_array2,1);
				if($rID2==1 && $flag==1) $flag=1; else $flag=0;
			}
			$id_dtls=$id_dtls+1;
		}
		
		//echo "10**".$rID1.'='.$rID2.'='.$rID_de1.'='.$flag; oci_rollback($con); check_table_status( $_SESSION['menu_id'],0); disconnect($con); die;
		
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==2 || $db_type==1 ){
			if($flag==1){
				oci_commit($con);
				echo "0**".$txt_req_no;
			}
			else{
				oci_rollback($con);
				echo "10**".$txt_req_no;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)
	{
		$con = connect();
		$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_req_no");
		if($is_approved==3){
			$is_approved=1;
		}
		if($is_approved==1){
			echo "app1**".str_replace("'","",$txt_req_no);
			 disconnect($con);die;
		}
	
		if( check_table_status( $_SESSION['menu_id'], 1 )==0 ){
			echo "15**1";
			 disconnect($con);die;
		}
		
		$new_array_color=array(); $newColorArr=array();
		for ($i=1;$i<=$total_row;$i++){
			$cbocolorsizesensitive="cbocolorsizesensitive_".$i;
			$txtwoq="txtwoq_".$i;
			$consbreckdown="consbreckdown_".$i;
			
			if(str_replace("'",'',$$consbreckdown) !=''){
				$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
				for($c=0;$c < count($consbreckdown_array);$c++){
					$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
					if(str_replace("'","",$consbreckdownarr[4])!="")
					{
						if (!in_array(str_replace("'","",$consbreckdownarr[4]),$new_array_color)){
							$color_id = return_id( str_replace("'","",$consbreckdownarr[4]), $color_library, "lib_color", "id,color_name","716");
							$newColorArr[str_replace("'","",$consbreckdownarr[4])]=$color_id;
						}
						else $newColorArr[str_replace("'","",$consbreckdownarr[4])]=$color_id;
					}
					else $newColorArr[str_replace("'","",$consbreckdownarr[4])]=0;
				}
			}
		}
		
		$field_array_up1="pre_cost_fabric_cost_dtls_id*po_break_down_id*job_no*booking_no*trim_group*uom*sensitivity*wo_qnty*exchange_rate*rate*amount*delivery_date*description*country_id_string*updated_by*update_date";
		$field_array_up2="id, wo_trim_booking_dtls_id, booking_no, booking_mst_id, job_no, po_break_down_id, color_number_id, gmts_sizes, description, brand_supplier, item_color, item_size, cons, process_loss_percent, requirment, rate, amount, pcs, color_size_table_id, article_number, status_active, is_deleted";
	
		$add_comma=0; $flag=1;
		$id1=return_next_id( "id", "wo_trim_book_con_dtls", 1 );
		
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
			$pi_number=array(); $piquantity=0;
			
			if(str_replace("'","",$$txtddate)!='') $txtdlvdate=date("d-M-Y",strtotime(str_replace("'","",$$txtddate))); else $txtdlvdate="";
			
			$sqlPi=sql_select("select a.pi_number, b.quantity from  com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id  and b.work_order_no=$txt_req_no and b.item_group=".$$txttrimgroup." and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			foreach($sqlPi as $rowPi){
				$pi_number[$rowPi[csf('pi_number')]]=$rowPi[csf('pi_number')];
				$piquantity+=$rowPi[csf('quantity')];
			}
	
			if($piquantity && str_replace("'","",$$txtwoq) < $piquantity){
				echo "pi1**".str_replace("'","",$txt_req_no)."**".implode(",",$pi_number)."**".$piquantity;
				check_table_status( $_SESSION['menu_id'],0);
				disconnect($con);die;
			}
	
			$recv_number=array(); $recvquantity=0;
			$sqlRecv=sql_select("select a.recv_number, b.receive_qnty from  inv_receive_master a,inv_trims_entry_dtls b where a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_req_no and b.item_group_id=".$$txttrimgroup." and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			foreach($sqlRecv as $rowRecv){
				$recv_number[$rowRecv[csf('recv_number')]]=$rowRecv[csf('recv_number')];
				$recvquantity+=$rowRecv[csf('receive_qnty')];
			}
			
			if($recvquantity && str_replace("'","",$$txtwoq) < $recvquantity){
				echo "recv1**".str_replace("'","",$txt_req_no)."**".implode(",",$recv_number)."**".$recvquantity;
				check_table_status($_SESSION['menu_id'],0);
			 	disconnect($con); die;
			}
			//echo "10**".$$txtddate; check_table_status($_SESSION['menu_id'],0); disconnect($con); die;
			if(str_replace("'",'',$$txtbookingid)!=""){
				$id_arr=array();
				$data_array_up1=array();
				$id_arr[]=str_replace("'",'',$$txtbookingid);
				$data_array_up1[str_replace("'",'',$$txtbookingid)] =explode("*",("".$$txttrimcostid."*".$$txtpoid."*".$$txtjob_id."*".$txt_req_no."*".$$txttrimgroup."*".$$txtuom."*".$$cbocolorsizesensitive."*".$$txtwoq."*".$$txtexchrate."*".$$txtrate."*".$$txtamount."*'".$txtdlvdate."'*".$$txtdescid."*".$$txtcountry."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
	
				//	CONS break down===============================================================================================
				if(str_replace("'",'',$$consbreckdown) !=''){
					$data_array_up2="";
					$rID_de1=execute_query( "delete from wo_trim_book_con_dtls where  wo_trim_booking_dtls_id =".$$txtbookingid."",0);
					if($rID_de1==1 && $flag==1) $flag=1; else $flag=0;
					
					$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
					for($c=0;$c < count($consbreckdown_array);$c++){
						$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
						
						$color_id =$newColorArr[str_replace("'","",$consbreckdownarr[4])];
						if ($c!=0) $data_array_up2 .=",";
						$data_array_up2 .="(".$id1.",".$$txtbookingid.",".$txt_req_no.",".$booking_mst_id.",".$$txtjob_id.",".$$txtpoid.",'".$consbreckdownarr[0]."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$color_id."','".$consbreckdownarr[5]."','".$consbreckdownarr[6]."','".$consbreckdownarr[7]."','".$consbreckdownarr[8]."','".$consbreckdownarr[9]."','".$consbreckdownarr[10]."','".$consbreckdownarr[11]."','".$consbreckdownarr[12]."','".$consbreckdownarr[14]."',1,0)";
						$id1=$id1+1;
						$add_comma++;
					}
				}
				//CONS break down end===============================================================================================
				if($data_array_up1 !="")
				{
					$rID1=execute_query(bulk_update_sql_statement( "wo_booking_dtls", "id", $field_array_up1, $data_array_up1, $id_arr ));
					if($rID1==1 && $flag==1) $flag=1; else $flag=0;
				}
			}
			$rID2=1;
			if($data_array_up2 !="")
			{
				$rID2=sql_insert("wo_trim_book_con_dtls",$field_array_up2,$data_array_up2,1);
				if($rID2==1 && $flag==1) $flag=1; else $flag=0;
			}
		}
		
		//echo "10**".$rID1.'='.$rID2.'='.$rID_de1.'='.$flag; oci_rollback($con); check_table_status( $_SESSION['menu_id'],0); disconnect($con); die;
		//$rID=execute_query( "update wo_booking_mst set revised_no=revised_no+1 where  booking_no=$txt_req_no",0);
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==2 || $db_type==1 ){
			if($flag==1){
				oci_commit($con);
				echo "1**".str_replace("'","",$txt_req_no);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_req_no);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2){
		$con = connect();
		$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_req_no");
		if($is_approved==3){
			$is_approved=1;
		}
		if($is_approved==1){
			echo "app1**".str_replace("'","",$txt_req_no);
			 disconnect($con);die;
		}
		
		for ($i=1;$i<=$total_row;$i++){
			$txttrimcostid="txttrimcostid_".$i;
			$txttrimgroup="txttrimgroup_".$i;
			$txtpoid="txtpoid_".$i;
			$txtbookingid="txtbookingid_".$i;
			
			$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id and b.work_order_no=$txt_req_no and b.item_group=".$$txttrimgroup." and a.item_category_id=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
			if($pi_number){
				echo "pi1**".str_replace("'","",$txt_req_no)."**".$pi_number;
				disconnect($con);die;
			}
			
			$recv_number=return_field_value( "recv_number", "inv_receive_master a,inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_req_no and b.item_group_id=".$$txttrimgroup." and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($recv_number){
				echo "recv1**".str_replace("'","",$txt_req_no)."**".$recv_number;
			    disconnect($con);  die;
			}
			
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
			$rID1=execute_query( "update wo_booking_dtls set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."'   where  id in (".str_replace("'","",$$txtbookingid).") and booking_no=$txt_req_no",0);
		    $rID2=execute_query( "update wo_trim_book_con_dtls set status_active=0,is_deleted=1 where  wo_trim_booking_dtls_id in(".str_replace("'","",$$txtbookingid).") and booking_no=$txt_req_no",0);
		}

		if($db_type==2 || $db_type==1 ){
			if($rID1 &&  $rID2){
				oci_commit($con);
				echo "2**".str_replace("'","",$txt_req_no);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_req_no);
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
	$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_req_no");
	if($is_approved==3){
		$is_approved=1;
	}
	if($is_approved==1){
			echo "app1**".str_replace("'","",$txt_req_no);
			 disconnect($con);die;
	}
	
	$shortBookingno=return_field_value( "booking_no", "wo_booking_mst","requisition_no=$txt_reqsn_no and status_active=1 and is_deleted=0");
	if($shortBookingno){
		echo "shortBookingno**".str_replace("'","",$txt_reqsn_no)."**".$shortBookingno;
		disconnect($con);die;
	}
	
	$strdata=json_decode(str_replace("'","",$strdata));
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
							$color_id = return_id( str_replace("'","",$consbreckdownarr[4]), $color_library, "lib_color", "id,color_name","716");
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
		if (is_duplicate_field( "booking_no", "wo_booking_dtls", "job_no in(".implode(",",$jobArr).") and po_break_down_id in (".implode(",",$poArr).") and pre_cost_fabric_cost_dtls_id in(".implode(",",$pre_trim_id_arr).") and booking_type=13 and is_short=1 and booking_no=$txt_req_no and status_active=1 and is_deleted=0 $des_data_cond $brand_data_cond") == 1)
		{
			check_table_status( $_SESSION['menu_id'],0);
			echo "11**0";
			disconnect($con);die;
		}
		$id_dtls=return_next_id("id", "wo_booking_dtls", 1);
		$field_array1="id, pre_cost_fabric_cost_dtls_id, po_break_down_id, job_no, booking_no, booking_mst_id, booking_type, is_short, entry_form_id, trim_group, uom, sensitivity, wo_qnty, exchange_rate, rate, amount, delivery_date,description, country_id_string, inserted_by, insert_date";
		
		$field_array2="id, wo_trim_booking_dtls_id, booking_no, booking_mst_id, job_no, po_break_down_id, color_number_id, gmts_sizes, description, brand_supplier, item_color, item_size, cons, process_loss_percent, requirment, rate, amount, pcs, color_size_table_id, article_number";
		$add_comma=0;
		$id1=return_next_id("id", "wo_trim_book_con_dtls", 1);
		$new_array_color=array(); $flag=1;
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

			$job=str_replace("'","",$$txtjob_id);
			$trimcostid=str_replace("'","",$$txttrimcostid);
			$reqqnty=str_replace("'","",$$txtreqqnty);
			$woq=str_replace("'","",$$txtwoq);
			$rate=str_replace("'","",$$txtrate);

			foreach($strdata->$job->$trimcostid->po_id as $poId){
				$wqQty=($strdata->$job->$trimcostid->req_qnty->$poId/$reqqnty)*$woq;
				$amount=$wqQty*$rate;
				$data_array1 ="(".$id_dtls.",".$$txttrimcostid.",".$poId.",".$$txtjob_id.",".$txt_req_no.",".$booking_mst_id.",13,1,716,".$$txttrimgroup.",".$$txtuom.",".$$cbocolorsizesensitive.",".$wqQty.",".$$txtexchrate.",".$$txtrate.",".$amount.",'".$txtdlvdate."',".$$txtdescid.",".$$txtcountry.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$rID1=sql_insert("wo_booking_dtls",$field_array1,$data_array1,0);
				//echo "10**INSERT INTO wo_booking_dtls (".$field_array1.") VALUES ".$data_array1; oci_rollback($con); check_table_status( $_SESSION['menu_id'],0); disconnect($con); die;
				if($rID1==1 && $flag==1) $flag=1; else $flag=0;

				//	CONS break down===============================================================================================
				if(str_replace("'",'',$$consbreckdown) !=''){
					$rID_de1=execute_query( "delete from wo_trim_book_con_dtls where  wo_trim_booking_dtls_id =".$$txtbookingid."",0);
					if($rID_de1==1 && $flag==1) $flag=1; else $flag=0;
					$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
					$d=0;
					for($c=0;$c < count($consbreckdown_array);$c++){
						$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
						$color_id=$newColorArr[str_replace("'","",$consbreckdownarr[4])];

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
						if ($d!=0){
							$data_array2 .=",";
						}
						$data_array2 ="(".$id1.",".$id_dtls.",".$txt_req_no.",".$booking_mst_id.",".$$txtjob_id.",".$poId.",'".$consbreckdownarr[0]."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$color_id."','".$consbreckdownarr[5]."','".$bQty."','".$consbreckdownarr[7]."','".$bwqQty."','".$consbreckdownarr[9]."','".$bamount."','".$order_qty."','".$consbreckdownarr[12]."','".$consbreckdownarr[14]."')";
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
		$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_req_no");
		if($is_approved==3){
			$is_approved=1;
		}
		if($is_approved==1){
			echo "app1**".str_replace("'","",$txt_req_no);
			disconnect($con);die;
		}
		
		if(check_table_status( $_SESSION['menu_id'], 1)==0){
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
			$txttrimgroup="txttrimgroup_".$i;
			$txtwoq="txtwoq_".$i;
			
			$poid=str_replace("'","",$$txtpoid);
			$pretrimcostid=str_replace("'","",$$txttrimcostid);
			$bookingdtlsid=str_replace("'","",$$txtbookingid);
			
			$jobArr[$$txtjob_id]=$$txtjob_id;
			$poArr[$poid]=$poid;
			$pre_trim_id_arr[$pretrimcostid]=$pretrimcostid;
			$des_arr[$$txtdesc]=$$txtdesc;
			//$brand_arr[$$txtbrandsup]=$$txtbrandsup;
			$booking_dtls_id_arr[$bookingdtlsid]=$bookingdtlsid;
			
			/*$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_req_no and b.item_group=".$$txttrimgroup." and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($pi_number){
				echo "pi1**".str_replace("'","",$txt_req_no)."**".$pi_number;
				die;
			}
			$recv_number=return_field_value( "recv_number", "inv_receive_master a,inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_req_no and b.item_group_id=".$$txttrimgroup." and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($recv_number){
				echo "recv1**".str_replace("'","",$txt_req_no)."**".$recv_number;
				die;
			}*/
			$pi_number=array();
			$piquantity=0;
			$sqlPi=sql_select("select a.pi_number, b.quantity from  com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id  and b.work_order_no=$txt_req_no and b.item_group=".$$txttrimgroup." and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			foreach($sqlPi as $rowPi){
				$pi_number[$rowPi[csf('pi_number')]]=$rowPi[csf('pi_number')];
				$piquantity+=$rowPi[csf('quantity')];
			}

			if($piquantity && str_replace("'","",$$txtwoq) < $piquantity){
				echo "pi1**".str_replace("'","",$txt_req_no)."**".implode(",",$pi_number)."**".$piquantity;
				check_table_status( $_SESSION['menu_id'],0);
			 	disconnect($con);	die;
			}

			$recv_number=array();
			$recvquantity=0;
			$sqlRecv=sql_select("select a.recv_number, b.receive_qnty from  inv_receive_master a,inv_trims_entry_dtls b where a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_req_no and b.item_group_id=".$$txttrimgroup." and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			foreach($sqlRecv as $rowRecv){
				$recv_number[$rowRecv[csf('recv_number')]]=$rowRecv[csf('recv_number')];
				$recvquantity+=$rowRecv[csf('receive_qnty')];
			}
			if($recvquantity && str_replace("'","",$$txtwoq) < $recvquantity){
				echo "recv1**".str_replace("'","",$txt_req_no)."**".implode(",",$recv_number)."**".$recvquantity;
				check_table_status( $_SESSION['menu_id'],0);
				disconnect($con);die;
			}
			
			if(str_replace("'",'',$$consbreckdown) !=''){
				$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
				for($c=0; $c< count($consbreckdown_array); $c++){
					$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
					$consbreckdownarr[4]=trim(str_replace("'",'',$consbreckdownarr[4]));
					if(str_replace("'","",$consbreckdownarr[4])=='0') $consbreckdownarr[4]='';
					if(str_replace("'","",$consbreckdownarr[4])!="")
					{
						if (!in_array(str_replace("'","",$consbreckdownarr[4]),$new_array_color)){
							$color_id = return_id( str_replace("'","",$consbreckdownarr[4]), $color_library, "lib_color", "id,color_name","716");
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
		if (is_duplicate_field( "booking_no", "wo_booking_dtls", "job_no in(".implode(",",$jobArr).") and po_break_down_id in (".implode(",",$poArr).") and pre_cost_fabric_cost_dtls_id in(".implode(",",$pre_trim_id_arr).") and id not in(".implode(",",$booking_dtls_id_arr).") and booking_type=13 and is_short=1 and booking_no=$txt_req_no and status_active=1 and is_deleted=0 $des_data_cond $brand_data_cond") == 1)
		{
			check_table_status( $_SESSION['menu_id'],0);
			echo "11**0";
			disconnect($con);die;
		}
		
		$field_array_up1="pre_cost_fabric_cost_dtls_id*po_break_down_id*job_no*booking_no*trim_group*uom*sensitivity*wo_qnty*exchange_rate*rate*amount*delivery_date*description*country_id_string*updated_by*update_date";
		$field_array_up2="id, wo_trim_booking_dtls_id, booking_no, booking_mst_id, job_no, po_break_down_id, color_number_id, gmts_sizes, description, brand_supplier, item_color, item_size, cons, process_loss_percent, requirment, rate, amount, pcs, color_size_table_id, article_number";
		$add_comma=0;
		$id1=return_next_id("id", "wo_trim_book_con_dtls", 1); $flag=1; //echo "10**";
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
					$data_array_up1[str_replace("'",'',$strdata->$job->$trimcostid->booking_id->$poId)] =explode("*",("".$$txttrimcostid."*".$poId."*".$$txtjob_id."*".$txt_req_no."*".$$txttrimgroup."*".$$txtuom."*".$$cbocolorsizesensitive."*".$wqQty."*".$$txtexchrate."*".$$txtrate."*".$amount."*'".$txtdlvdate."'*".$$txtdescid."*".$$txtcountry."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					
					if($data_array_up1 !=""){
						//echo bulk_update_sql_statement( "wo_booking_dtls", "id", $field_array_up1, $data_array_up1, $id_arr); oci_rollback($con); check_table_status( $_SESSION['menu_id'],0); disconnect($con); die;
						$rID1=execute_query(bulk_update_sql_statement( "wo_booking_dtls", "id", $field_array_up1, $data_array_up1, $id_arr ));
						if($rID1==1 && $flag==1) $flag=1; else $flag=0;
					}
					//	CONS break down===============================================================================================
					$rID2=1;
					if(str_replace("'",'',$$consbreckdown) !=''){
						$rID_de1=execute_query( "delete from wo_trim_book_con_dtls where  wo_trim_booking_dtls_id =".$strdata->$job->$trimcostid->booking_id->$poId."",0);
						
						if($rID_de1==1 && $flag==1) $flag=1; else $flag=0;
						
						$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
						$d=0;
						for($c=0;$c < count($consbreckdown_array);$c++){
							$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
							$color_id =$itemcolorArr[str_replace("'","",$consbreckdownarr[4])];
							
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
							$data_array2 ="(".$id1.",".$strdata->$job->$trimcostid->booking_id->$poId.",".$txt_req_no.",".$booking_mst_id.",".$$txtjob_id.",".$poId.",'".$consbreckdownarr[0]."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$color_id."','".$consbreckdownarr[5]."','".$bQty."','".$consbreckdownarr[7]."','".$bwqQty."','".$consbreckdownarr[9]."','".$bamount."','".$order_qty."','".$consbreckdownarr[12]."','".$consbreckdownarr[14]."')";
							$id1=$id1+1;
							$add_comma++;
							$d++;
							$rID2=sql_insert("wo_trim_book_con_dtls",$field_array_up2,$data_array2,0);
							if($rID2==1 && $flag==1) $flag=1; else $flag=0;
						}
					}//CONS break down end==============================================================================================
				}
			}
		}
		
		//echo "10**".$rID1.'='.$rID_de1.'='.$rID2.'='.$flag; oci_rollback($con); check_table_status( $_SESSION['menu_id'],0); disconnect($con); die;
		//$rID=execute_query( "update wo_booking_mst set revised_no=revised_no+1 where  booking_no=$txt_req_no",0);
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==2 || $db_type==1 ){
			if($flag==1){
				oci_commit($con);
				echo "1**".str_replace("'","",$txt_req_no);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_req_no);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)
	{
		$con = connect();
		$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_req_no");
		if($is_approved==3){
			$is_approved=1;
		}
		if($is_approved==1){
			echo "app1**".str_replace("'","",$txt_req_no);
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

			$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_req_no and b.item_group=".$$txttrimgroup." and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($pi_number){
				echo "pi1**".str_replace("'","",$txt_req_no)."**".$pi_number;
				 disconnect($con);die;
			}
			$recv_number=return_field_value( "recv_number", "inv_receive_master a,inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_req_no and b.item_group_id=".$$txttrimgroup." and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($recv_number){
				echo "recv1**".str_replace("'","",$txt_req_no)."**".$recv_number;
				disconnect($con); die;
			}
			
		    
			$delete_cause=str_replace("'","",$delete_cause);
			$delete_cause=str_replace('"','',$delete_cause);
			$delete_cause=str_replace('(','',$delete_cause);
			$delete_cause=str_replace(')','',$delete_cause);
			//$rID1=execute_query( "delete from wo_booking_dtls where  id in (".str_replace("'","",$$txtbookingid).")",0);
			//$rID2=execute_query( "delete from wo_trim_book_con_dtls where  wo_trim_booking_dtls_id in(".str_replace("'","",$$txtbookingid).")",0);
			$rID1=execute_query("update wo_booking_dtls set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where  id in (".str_replace("'","",$$txtbookingid).") and booking_no=$txt_req_no",0);
		    $rID2=execute_query("update wo_trim_book_con_dtls set status_active=0,is_deleted=1 where  wo_trim_booking_dtls_id in(".str_replace("'","",$$txtbookingid).") and booking_no=$txt_req_no",0);
		}

		if($db_type==2 || $db_type==1 ){
			if($rID1 &&  $rID2){
				oci_commit($con);
				echo "2**".str_replace("'","",$txt_req_no);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_req_no);
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="trims_requisition_popup")
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
                <table width="950" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                    <thead>
                        <tr>
                            <th colspan="10">
                            <?
                            echo create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" );
                            ?>
                            </th>
                        </tr>
                        <tr>
                            <th width="130" class="must_entry_caption">Company Name</th>
                            <th width="130" class="must_entry_caption">Buyer Name</th>
                            <th width="100">Style Ref </th>
                            <th width="80">Job No </th>
                            <th width="80">Internal Ref.</th>
                            <th width="80">Order No</th>
                            <th width="70">Requisition No</th>
                            <th width="120" colspan="2"> Requisition Date Range</th>
                            <th><input type="checkbox" value="0" onClick="set_checkvalue();" id="chk_job_wo_po">Reqsn. Without PO</th> </th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td><?=create_drop_down( "cbo_company_mst", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", '', "load_drop_down( 'short_trims_req_multi_job_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );"); ?> </td>
                        <td id="buyer_td"><?=create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- Select Buyer --", $selected, "","" ); ?></td>
                        <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:100px"></td>
                        <td><input name="txt_job" id="txt_job" class="text_boxes" style="width:70px"></td>
                        <td><input name="internal_ref" id="internal_ref" class="text_boxes" style="width:80px"></td>
                        <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:80px"></td>
                        <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:70px"></td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px"></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px"></td>
                        <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_job').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('internal_ref').value, 'create_reqsn_search_list_view', 'search_div', 'short_trims_req_multi_job_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                        </td>
                    </tr>
                    <tr>
                        <td align="center" colspan="10" valign="middle">
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
	//echo $data[10];
	if($data[10]==0)
	{
		$internal_ref_con='';
		if(!empty($data[11]))
		{
			$internal_ref_con=" and d.grouping like '%$data[11]%'";
		}
		 $sql="select a.id, a.booking_no_prefix_num, a.booking_no, a.company_id, a.booking_date, a.delivery_date, a.responsible_dept, c.style_ref_no, d.po_number, a.pay_mode from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c, wo_po_break_down d where a.booking_no=b.booking_no and b.job_no=c.job_no and b.job_no=d.job_no_mst and b.po_break_down_id=d.id and a.booking_type=13 and a.entry_form=716 and a.is_short=1
		and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 $company $buyer  $booking_date $booking_cond $style_cond $order_cond $job_cond $internal_ref_con group by a.id, a.booking_no_prefix_num, a.booking_no, a.company_id, a.booking_date, a.delivery_date, a.responsible_dept, c.style_ref_no, d.po_number, a.pay_mode order by a.id DESC";
	}
	else
	{
		$internal_ref_con='';
		if(!empty($data[11]))
		{
			$internal_ref_con=" and c.grouping like '%$data[11]%'";
		}
		$sql="select a.id, a.job_no, a.booking_no_prefix_num, a.booking_no, company_id, a.booking_date, a.delivery_date, a.responsible_dept from wo_booking_mst a where  a.booking_no not in ( select a.booking_no from  wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c where a.booking_no=b.booking_no and b.po_break_down_id=c.id and  a.booking_type=13 and a.entry_form=716 and a.is_short=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $internal_ref_con  $company  ".set_user_lavel_filtering(' and a.buyer_id','buyer_id')." $supplier_id $booking_date $booking_cond $job_cond $file_cond $ref_cond group by a.booking_no_prefix_num, a.booking_no, a.company_id, a.booking_date, a.delivery_date) and a.booking_type=13 and a.entry_form=716 and a.status_active=1 and a.is_deleted=0 $company ".set_user_lavel_filtering(' and a.buyer_id','buyer_id')." $booking_date $booking_cond and a.is_short=1 group by a.id, a.booking_no_prefix_num, a.booking_no, a.job_no, a.company_id, a.booking_date, a.delivery_date, a.responsible_dept order by a.id DESC";
	}
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
				<tr bgcolor="<?=$bgcolor; ?>" onClick="js_set_value('<?=$row[csf("booking_no")]; ?>');" style="cursor:pointer">
                    <td width="30" align="center"><?=$sl; ?></td>
                    <td width="80"><?=$row[csf("booking_no_prefix_num")]; ?></td>
					<td width="100" style="word-break:break-all"><?=$comp[$row[csf("company_id")]];?></td>
					<td width="140" style="word-break:break-all"><?=$respDept; ?></td>
                    <td width="100"><?=change_date_format($row[csf("booking_date")],"dd-mm-yyyy","-"); ?></td>
					<td width="100"><?=change_date_format($row[csf("delivery_date")],"dd-mm-yyyy","-"); ?></td>
					<? if($data[10]==0) { ?>
					<td width="150" style="word-break:break-all"><?=$row[csf("style_ref_no")]; ?> </td>
					<td style="word-break:break-all"><?=$row[csf("po_number")]; ?> </td>
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

if($action=="terms_condition_popup")
{
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$_SESSION['page_permission']=$permission;
?>
	<script>
	var permission='<? echo $permission; ?>';
function add_break_down_tr(i)
 {
	var row_num=$('#tbl_termcondi_details tr').length-1;
	if (row_num!=i)
	{
		return false;
	}
	else
	{
		i++;

		 $("#tbl_termcondi_details tr:last").clone().find("input,select").each(function() {
			$(this).attr({
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  'name': function(_, name) { return name + i },
			  'value': function(_, value) { return value }
			});
		  }).end().appendTo("#tbl_termcondi_details");
		 $('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
		  $('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
		  $('#termscondition_'+i).val("");
		  $('#sltd_'+i).val(i);
		  //$('#sl_td').i
		  //alert(i)
		  //document.getElementById('sltd_'+i).innerHTML=i;
	}

}

function fn_deletebreak_down_tr(rowNo)
{


		var numRow = $('table#tbl_termcondi_details tbody tr').length;
		if(numRow==rowNo && rowNo!=1)
		{
			$('#tbl_termcondi_details tbody tr:last').remove();
		}

}

function fnc_fabric_booking_terms_condition( operation )
{
	    var row_num=$('#tbl_termcondi_details tr').length-1;
		var data_all="";
		for (var i=1; i<=row_num; i++)
		{

			if (form_validation('termscondition_'+i,'Term Condition')==false)
			{
				return;
			}

			data_all=data_all+get_submitted_data_string('txt_req_no*termscondition_'+i,"");
		}
		var data="action=save_update_delete_fabric_booking_terms_condition&operation="+operation+'&total_row='+row_num+data_all;
		//freeze_window(operation);
		http.open("POST","short_trims_req_multi_job_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_booking_terms_condition_reponse;
}

function fnc_fabric_booking_terms_condition_reponse()
{

	if(http.readyState == 4)
	{
	    var reponse=trim(http.responseText).split('**');
			if (reponse[0].length>2) reponse[0]=10;
			if(reponse[0]==0 || reponse[0]==1)
			{
				parent.emailwindow.hide();
			}
	}
}
    </script>

</head>

<body>
<div align="center" style="width:100%;" >
<? echo load_freeze_divs ("../../../",$permission);  ?>
<fieldset>
<input type="hidden" id="txt_req_no" name="txt_req_no" value="<? echo str_replace("'","",$txt_req_no) ?>"/>
        	<form id="termscondi_1" autocomplete="off">



            <table width="650" cellspacing="0" class="rpt_table" border="0" id="tbl_termcondi_details" rules="all">
                	<thead>
                    	<tr>
                        	<th width="50">Sl</th><th width="530">Terms</th><th ></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_req_no");// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							if ($i%2==0)
							$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
							$i++;
							?>
                            	<tr id="settr_1" align="center" bgcolor="<? echo $bgcolor;  ?>">
                                    <td >
                                    <? //echo $i;?>
                                    <input type="text" id="sltd_<? echo $i;?>"   name="sltd_<? echo $i;?>" style="width:100%;background-color:<? echo $bgcolor;  ?>"  value="<? echo $i; ?>"  readonly />
                                    </td>
                                    <td>
                                    <input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>"  />
                                    </td>
                                    <td>
                                    <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
                                    <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>);" />
                                    </td>
                                </tr>
                            <?
						}
					}
					else
					{
					$data_array=sql_select("select id, terms from  lib_terms_condition where is_default=1");// quotation_id='$data'
					foreach( $data_array as $row )
						{
							if ($i%2==0)
							$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
							$i++;
					?>
                    <tr id="settr_1" align="center" bgcolor="<? echo $bgcolor;  ?>">
                                    <td >
                                    <? // echo $i;?>
                                    <input type="text" id="sltd_<? echo $i;?>"   name="sltd_<? echo $i;?>" style="width:100%; background-color:<? echo $bgcolor;  ?>"  value="<? echo $i; ?>"  readonly />
                                    </td>
                                    <td>
                                    <input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>"  />
                                    </td>
                                    <td>
                                    <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
                                    <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> );" />
                                    </td>
                                </tr>
                    <?
						}
					}
					?>
                </tbody>
                </table>

                <table width="650" cellspacing="0" class="" border="0">
                	<tr>
                        <td align="center" height="15" width="100%"> </td>
                    </tr>
                	<tr>
                        <td align="center" width="100%" class="button_container">
						        <?
									echo load_submit_buttons( $permission, "fnc_fabric_booking_terms_condition", 0,0 ,"reset_form('termscondi_1','','','','')",1) ;
									?>
                        </td>
                    </tr>
                </table>
            </form>
        </fieldset>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="save_update_delete_fabric_booking_terms_condition")
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

		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con); die;}
		 $id=return_next_id( "id", "wo_booking_terms_condition", 1 ) ;
		 $field_array="id,booking_no,terms";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $termscondition="termscondition_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$txt_req_no.",".$$termscondition.")";
			$id=$id+1;
		 }
		// echo  $data_array;
		$rID_de3=execute_query( "delete from wo_booking_terms_condition where  booking_no =".$txt_req_no."",0);

		 $rID=sql_insert("wo_booking_terms_condition",$field_array,$data_array,1);
		 check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "0**".$new_booking_no[0];
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_booking_no[0];
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
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
}

if ($action=="populate_data_from_search_popup_booking"){
	$job_no="";
	$sql= "select id, booking_no, company_id, buyer_id, booking_date, cbo_level, responsible_dept, responsible_person, division_id, ready_to_approved, reason, remarks, short_booking_available, is_approved from wo_booking_mst where booking_no='$data' and status_active=1 and is_deleted=0";
	$data_array=sql_select($sql);
	foreach ($data_array as $row){
		echo "get_php_form_data(".$row[csf("company_id")].", 'populate_variable_setting_data', 'requires/short_trims_req_multi_job_controller' );\n";
		echo "document.getElementById('txt_req_no').value = '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('booking_mst_id').value = '".$row[csf("id")]."';\n";
		
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('txt_req_date').value = '".change_date_format($row[csf("booking_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('cbo_level').value = '".$row[csf("cbo_level")]."';\n";
		echo "set_multiselect('cbo_responsible_dept','0','1','".$row[csf("responsible_dept")]."','0');\n";
		//echo "document.getElementById('cbo_responsible_dept').value = '".$row[csf("responsible_dept")]."';\n";
		echo "document.getElementById('cbo_responsible_person').value = '".$row[csf("responsible_person")]."';\n";
		echo "document.getElementById('cbo_division_id').value = '".$row[csf("division_id")]."';\n";
		echo "document.getElementById('cbo_ready_to_approved').value = '".$row[csf("ready_to_approved")]."';\n";
		echo "document.getElementById('txt_reason').value = '".$row[csf("reason")]."';\n";
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n";
		
		if($row[csf("is_approved")]==3){
			$is_approved=3;
		}else{
			$is_approved=$row[csf("is_approved")];
		}
		echo "document.getElementById('id_approved_id').value = '".$is_approved."';\n";
		
		echo " $('#cbo_company_name').attr('disabled',true);\n";
		echo " $('#cbo_level').attr('disabled',true);\n";
		echo " $('#cbo_buyer_name').attr('disabled',true);\n";
		echo "fnc_show_booking_list();\n";

		if($is_approved==1){
			//echo "document.getElementById('app_sms').innerHTML = 'This booking is approved';\n";
			echo "document.getElementById('app_sms2').innerHTML = 'This Requisition is Approved';\n";
		}
		else if($is_approved==3){
			//echo "document.getElementById('app_sms').innerHTML = 'This booking is approved';\n";
			echo "document.getElementById('app_sms2').innerHTML = 'This Requisition is Partial Approved';\n";
		}
		else{
			//echo "document.getElementById('app_sms').innerHTML = '';\n";
			echo "document.getElementById('app_sms2').innerHTML = '';\n";
		}
	}
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
	 $nameArray=sql_select( "select a.booking_no,a.is_approved, a.pay_mode,a.buyer_id, a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.source, a.remarks, a.revised_no,a.update_date from wo_booking_mst a where a.booking_no=$txt_req_no and a.status_active =1 and a.is_deleted=0");
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

		$buyer_string=array(); $style_owner=array(); $job_no=array();
		$style_ref=array();
		$all_dealing_marcent=array();
		$season=array();

		$nameArray_buyer=sql_select( "select  b.update_date,a.style_ref_no, a.job_no, a.style_owner, a.buyer_name, a.dealing_marchant,a.season,a.season_matrix   from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no=$txt_req_no and b.status_active=1 and b.is_deleted=0");
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

		$nameArray_job=sql_select( "select b.job_no_mst, b.id, b.po_number, b.grouping, b.file_no, b.po_quantity  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_req_no and a.status_active=1 and a.is_deleted=0");
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
		$precost_sql=sql_select("select a.id, a.job_no,a.trim_group,a.calculatorstring, c.cal_parameter from wo_pre_cost_trim_cost_dtls a,wo_booking_dtls b, lib_item_group c where a.job_no=b.job_no and a.trim_group=b.trim_group and a.trim_group=c.id and b.booking_no=$txt_req_no and a.id=b.pre_cost_fabric_cost_dtls_id and b.status_active=1 and b.is_deleted=0");
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
		$nameArray_booking_country=sql_select( "select pre_cost_fabric_cost_dtls_id,sensitivity,country_id_string from wo_booking_dtls  where booking_no=$txt_req_no and status_active=1 and is_deleted=0");
		foreach($nameArray_booking_country as $nameArray_booking_country_row){
			$country_id_string=explode(",",$nameArray_booking_country_row[csf('country_id_string')]);
			$tocu=count($country_id_string);
			for($cu=0;$cu<$tocu;$cu++){
				$booking_country_arr[$nameArray_booking_country_row[csf('pre_cost_fabric_cost_dtls_id')]][$nameArray_booking_country_row[csf('sensitivity')]][$country_id_string[$cu]]=$country_arr[$country_id_string[$cu]];
			}
		}
	
		$nameArray_job_po=sql_select( "select job_no from wo_booking_dtls  where booking_no=$txt_req_no and status_active=1 and is_deleted=0 group by job_no order by job_no ");
		foreach($nameArray_job_po as $nameArray_job_po_row){
		$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls  where booking_no=$txt_req_no and job_no='".$nameArray_job_po_row[csf('job_no')]."'   and sensitivity=1 and status_active=1 and is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group  order by trim_group ");
		
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
            $nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid, b.description, b.brand_supplier,b.item_color,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no=$txt_req_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=1  and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_color order by bid ");
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
		$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls  where booking_no=$txt_req_no and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=2 and status_active=1 and is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group  order by trim_group ");
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
				where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and   a.booking_no=$txt_req_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.sensitivity=2 and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
				group by a.pre_cost_fabric_cost_dtls_id, b.description,b.article_number, b.brand_supplier,b.item_size, b.gmts_sizes order by bid");

			$article_number_data=sql_select( "SELECT c.size_number_id, c.article_number, c.job_no_mst, c.po_break_down_id
			from wo_booking_dtls a, wo_trim_book_con_dtls b, wo_po_color_size_breakdown c
			where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no=$txt_req_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.sensitivity=2 and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and a.sensitivity=2 and c.job_no_mst=a.job_no  and c.job_no_mst=b.job_no and b.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
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
		 $nameArray_item=sql_select( "select pre_cost_fabric_cost_dtls_id, trim_group from wo_booking_dtls  where booking_no=$txt_req_no and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=3  and status_active=1 and is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group order by trim_group ");
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
				$gmtcolor_library=return_library_array( "select b.item_color,b.color_number_id from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no=$txt_req_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=3  and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by trim_group ", "item_color", "color_number_id"  );

			$nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid,b.description, b.brand_supplier,b.item_color,b.color_number_id,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no=$txt_req_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.sensitivity=3  and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_color,b.color_number_id order by bid ");
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
		$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls  where booking_no=$txt_req_no and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=4 and status_active=1 and is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group order by trim_group");
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
			 $nameArray_color_arr=sql_select( "SELECT a.trim_group,b.description,a.pre_cost_fabric_cost_dtls_id as f_dtlsid, b.item_color as item_color, b.item_size,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount,b.article_number,b.gmts_sizes from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_req_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=4 and b.requirment !=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by  a.trim_group,b.description,a.pre_cost_fabric_cost_dtls_id,b.article_number,b.item_color,b.item_size,b.gmts_sizes order by b.item_size");

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
				$gmtcolor_library=return_library_array( "select b.item_color,b.color_number_id from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no=$txt_req_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.sensitivity=4    and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by trim_group ", "item_color", "color_number_id"  );

			 //$nameArray_color=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid, b.color_number_id as gmt_color, b.item_color as color_number_id,b.item_size,b.description, b.brand_supplier,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and a.booking_no=$txt_req_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and a.sensitivity=4 and b.requirment !=0 group by a.pre_cost_fabric_cost_dtls_id, b.color_number_id, b.item_color,b.item_size,b.description, b.brand_supplier order by bid ");
			 $nameArray_color=sql_select( "SELECT a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid, b.color_number_id as gmt_color, b.item_color as color_number_id,b.item_size,b.gmts_sizes,b.description, b.brand_supplier,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount, min(c.color_order) as color_order, min(c.size_order) as size_order, c.article_number 
			 from wo_booking_dtls a, wo_trim_book_con_dtls b,wo_po_color_size_breakdown c  
			 where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and b.po_break_down_id=c.po_break_down_id and b.color_number_id=c.color_number_id and b.gmts_sizes=c.size_number_id  and a.booking_no=$txt_req_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and a.sensitivity=4 and b.requirment !=0 and c.id=b.color_size_table_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
			 group by a.pre_cost_fabric_cost_dtls_id, b.color_number_id, b.item_color,b.item_size,b.gmts_sizes,b.description, b.brand_supplier, c.article_number order by color_order,size_order"); //and  c.id=b.color_size_table_id

            $nameArray_item_description=sql_select( "select distinct uom from wo_booking_dtls  where booking_no=$txt_req_no and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=4 and trim_group=".$result_item[csf('trim_group')]." and pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and status_active=1 and is_deleted=0 order by trim_group ");
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
       //$nameArray_item=sql_select( "select distinct trim_group from wo_booking_dtls  where booking_no=$txt_req_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and sensitivity=0 order by trim_group ");
		$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls  where booking_no=$txt_req_no and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=0 and status_active=1 and is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group order by trim_group ");
        //$nameArray_color=sql_select( "select distinct b.color_number_id from wo_trims_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_req_no and a.sensitivity=1");
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

				$nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_color,avg(b.rate) as rate, sum(b.amount) as amount,b.article_number from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_req_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and a.sensitivity=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.description, b.article_number,b.brand_supplier,b.item_color");

            //$nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_req_no and sensitivity=0 and trim_group=".$result_item[csf('trim_group')]." order by trim_group ");
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
						$nameArray_color_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_req_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.trim_group=". $result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and  b.description='". $result_itemdescription[csf('description')]."' and b.brand_supplier='".$result_itemdescription[csf('brand_supplier')]."' and b.item_color='".$result_itemdescription[csf('item_color')]."'");
						}
						if($db_type==2)
				        {
						$nameArray_color_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no=$txt_req_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trim_group=". $result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and nvl( b.description,0)=nvl('". $result_itemdescription[csf('description')]."',0) and nvl(b.brand_supplier,0)=nvl('".$result_itemdescription[csf('brand_supplier')]."',0) and nvl(b.item_color,0)=nvl('".$result_itemdescription[csf('item_color')]."',0)");

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
	        	echo get_spacial_instruction($txt_req_no,'');
	        ?>
    	</td>

    <td width="2%"></td>
      <?

	$lib_designation_arr=return_library_array(" select id,custom_designation from lib_designation","id","custom_designation");
	$user_lib_designation_arr=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
	$user_lib_name_arr=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");

	$mst_id=return_field_value("id as mst_id","wo_booking_mst","booking_no=$txt_req_no","mst_id");
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
			echo "****".custom_file_name($txt_req_no,$style_sting,$job_no);
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
	 $nameArray=sql_select( "select a.booking_no,a.is_approved, a.pay_mode,a.buyer_id, a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.source, a.remarks, a.revised_no,a.update_date,a.delivery_address,a.tenor from wo_booking_mst a where a.booking_no=$txt_req_no and a.status_active =1 and a.is_deleted=0");
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

		$nameArray_buyer=sql_select( "select  b.update_date,a.style_ref_no, a.job_no, a.style_owner, a.buyer_name, a.dealing_marchant,a.season,a.season_matrix   from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no=$txt_req_no and b.status_active=1 and b.is_deleted=0");
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

		$nameArray_job=sql_select( "select b.job_no_mst, b.id, b.po_number, b.grouping, b.file_no, b.po_quantity  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_req_no and a.status_active=1 and a.is_deleted=0");
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
		$precost_sql=sql_select("select a.id, a.job_no,a.trim_group,a.calculatorstring, c.cal_parameter from wo_pre_cost_trim_cost_dtls a,wo_booking_dtls b, lib_item_group c where a.job_no=b.job_no and a.trim_group=b.trim_group and a.trim_group=c.id and b.booking_no=$txt_req_no and a.id=b.pre_cost_fabric_cost_dtls_id and b.status_active=1 and b.is_deleted=0");

		

		
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
		$nameArray_booking_country=sql_select( "select pre_cost_fabric_cost_dtls_id,sensitivity,country_id_string from wo_booking_dtls  where booking_no=$txt_req_no and status_active=1 and is_deleted=0");
		foreach($nameArray_booking_country as $nameArray_booking_country_row){
			$country_id_string=explode(",",$nameArray_booking_country_row[csf('country_id_string')]);
			$tocu=count($country_id_string);
			for($cu=0;$cu<$tocu;$cu++){
				$booking_country_arr[$nameArray_booking_country_row[csf('pre_cost_fabric_cost_dtls_id')]][$nameArray_booking_country_row[csf('sensitivity')]][$country_id_string[$cu]]=$country_arr[$country_id_string[$cu]];
			}
		}

		$nameArray_job_po=sql_select( "select job_no from wo_booking_dtls  where booking_no=$txt_req_no and status_active=1 and is_deleted=0 group by job_no order by job_no ");
		foreach($nameArray_job_po as $nameArray_job_po_row){
		$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls  where booking_no=$txt_req_no and job_no='".$nameArray_job_po_row[csf('job_no')]."'   and sensitivity=1 and status_active=1 and is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group  order by trim_group ");
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
            $nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid, b.description, b.brand_supplier,b.item_color,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no=$txt_req_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=1  and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_color order by bid ");
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
		$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls  where booking_no=$txt_req_no and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=2 and status_active=1 and is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group  order by trim_group ");
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
				where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and   a.booking_no=$txt_req_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.sensitivity=2 and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
				group by a.pre_cost_fabric_cost_dtls_id, b.description,b.article_number, b.brand_supplier,b.item_size, b.gmts_sizes, b.po_break_down_id order by bid");

			$article_number_data=sql_select( "SELECT c.size_number_id, c.article_number, c.job_no_mst, c.po_break_down_id
			from wo_booking_dtls a, wo_trim_book_con_dtls b, wo_po_color_size_breakdown c
			where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no=$txt_req_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.sensitivity=2 and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and a.sensitivity=2 and c.job_no_mst=a.job_no  and c.job_no_mst=b.job_no and b.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
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
		 $nameArray_item=sql_select( "select pre_cost_fabric_cost_dtls_id, trim_group from wo_booking_dtls  where booking_no=$txt_req_no and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=3  and status_active=1 and is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group order by trim_group ");
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
				$gmtcolor_library=return_library_array( "select b.item_color,b.color_number_id from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no=$txt_req_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=3  and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by trim_group ", "item_color", "color_number_id"  );

			$nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid,b.description, b.brand_supplier,b.item_color,b.color_number_id,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no=$txt_req_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.sensitivity=3  and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_color,b.color_number_id order by bid ");
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
		$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls  where booking_no=$txt_req_no and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=4 and status_active=1 and is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group order by trim_group");
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
			 $nameArray_color_arr=sql_select( "SELECT a.trim_group,b.description,a.pre_cost_fabric_cost_dtls_id as f_dtlsid, b.item_color as item_color, b.item_size,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount,b.article_number from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_req_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=4 and b.requirment !=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by  a.trim_group,b.description,a.pre_cost_fabric_cost_dtls_id,b.article_number,b.item_color,b.item_size order by b.item_size");

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
				$gmtcolor_library=return_library_array( "select b.item_color,b.color_number_id from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no=$txt_req_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.sensitivity=4    and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by trim_group ", "item_color", "color_number_id"  );

			 //$nameArray_color=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid, b.color_number_id as gmt_color, b.item_color as color_number_id,b.item_size,b.description, b.brand_supplier,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and a.booking_no=$txt_req_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and a.sensitivity=4 and b.requirment !=0 group by a.pre_cost_fabric_cost_dtls_id, b.color_number_id, b.item_color,b.item_size,b.description, b.brand_supplier order by bid ");
			 $nameArray_color=sql_select( "SELECT a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid, b.color_number_id as gmt_color, b.item_color as color_number_id,b.item_size,b.gmts_sizes,b.description, b.brand_supplier,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount, min(c.color_order) as color_order, min(c.size_order) as size_order, c.article_number 
			 from wo_booking_dtls a, wo_trim_book_con_dtls b,wo_po_color_size_breakdown c  
			 where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and b.po_break_down_id=c.po_break_down_id and b.color_number_id=c.color_number_id and b.gmts_sizes=c.size_number_id  and a.booking_no=$txt_req_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and a.sensitivity=4 and b.requirment !=0 and c.id=b.color_size_table_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
			 group by a.pre_cost_fabric_cost_dtls_id, b.color_number_id, b.item_color,b.item_size,b.gmts_sizes,b.description, b.brand_supplier, c.article_number order by color_order,size_order"); //and  c.id=b.color_size_table_id

            $nameArray_item_description=sql_select( "select distinct uom from wo_booking_dtls  where booking_no=$txt_req_no and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=4 and trim_group=".$result_item[csf('trim_group')]." and pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and status_active=1 and is_deleted=0 order by trim_group ");
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
       //$nameArray_item=sql_select( "select distinct trim_group from wo_booking_dtls  where booking_no=$txt_req_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and sensitivity=0 order by trim_group ");
		$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls  where booking_no=$txt_req_no and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=0 and status_active=1 and is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group order by trim_group ");
        //$nameArray_color=sql_select( "select distinct b.color_number_id from wo_trims_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_req_no and a.sensitivity=1");
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

				$nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_color,avg(b.rate) as rate, sum(b.amount) as amount,b.article_number from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_req_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and a.sensitivity=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.description, b.article_number,b.brand_supplier,b.item_color");

            //$nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_req_no and sensitivity=0 and trim_group=".$result_item[csf('trim_group')]." order by trim_group ");
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
						$nameArray_color_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_req_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.trim_group=". $result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and  b.description='". $result_itemdescription[csf('description')]."' and b.brand_supplier='".$result_itemdescription[csf('brand_supplier')]."' and b.item_color='".$result_itemdescription[csf('item_color')]."'");
						}
						if($db_type==2)
				        {
						$nameArray_color_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no=$txt_req_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trim_group=". $result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and nvl( b.description,0)=nvl('". $result_itemdescription[csf('description')]."',0) and nvl(b.brand_supplier,0)=nvl('".$result_itemdescription[csf('brand_supplier')]."',0) and nvl(b.item_color,0)=nvl('".$result_itemdescription[csf('item_color')]."',0)");

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
	        	echo get_spacial_instruction($txt_req_no,'');
	        ?>
    	</td>

    <td width="2%"></td>
      <?

	$lib_designation_arr=return_library_array(" select id,custom_designation from lib_designation","id","custom_designation");
	$user_lib_designation_arr=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
	$user_lib_name_arr=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");

	$mst_id=return_field_value("id as mst_id","wo_booking_mst","booking_no=$txt_req_no","mst_id");
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
			echo "****".custom_file_name($txt_req_no,$style_sting,$job_no);
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
?>
