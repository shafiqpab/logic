<?
/*-------------------------------------------- Comments
Version (MySql)          :  V2
Version (Oracle)         :  V1
Converted by             :  Aziz
Purpose			         :  This form will create Trims Booking Multi Job Wise
Functionality	         :
JS Functions	         :
Created by		         :  Monzu
Creation date 	         :  17-1-2016
Requirment Client        :  Urmi
Requirment By            :
Requirment type          :
Requirment               :
Affected page            :
Affected Code            :
DB Script                :
Updated by 		         :
Update date		         :
QC Performed BY	         :
QC Date			         :
Comments		         :
*/
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
include('../../../includes/class4/class.conditions.php');
include('../../../includes/class4/class.reports.php');
include('../../../includes/class4/class.trims.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];
$permission=$_SESSION['page_permission'];
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');
//---------------------------------------------------- Start---------------------------------------------------------------------------
function load_drop_down_supplier($data){
	if($data==5 || $data==3){
	   echo create_drop_down( "cbo_supplier_name", 172, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "-- Select Company --", "", "get_php_form_data( this.value, 'load_drop_down_attention', 'requires/trims_booking_multi_job_controllerurmi');",0,"" );
	}
	else
	{
	$cbo_supplier_name= create_drop_down( "cbo_supplier_name", 172, "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and b.party_type=4 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name","id,supplier_name", 1, "--Select Supplier--",$selected,"get_php_form_data( this.value, 'load_drop_down_attention', 'requires/trims_booking_multi_job_controllerurmi');","");
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
	$cbo_supplier_name     = create_drop_down( "cbo_supplier_name", 172, "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data' and b.party_type=4 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name","id,supplier_name", 1, "--Select Supplier--",$selected,"get_php_form_data( this.value, \'load_drop_down_attention\', \'requires/trims_booking_multi_job_controllerurmi\');","");
	$cbo_buyer_name= create_drop_down( "cbo_buyer_name", 172, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected,"","");

	echo "document.getElementById('supplier_td').innerHTML = '".$cbo_supplier_name."';\n";
	echo "document.getElementById('buyer_td').innerHTML = '".$cbo_buyer_name."';\n";
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=2 and report_id in(219) and is_deleted=0 and status_active=1");
	echo "document.getElementById('report_ids').value = '".$print_report_format."';\n";
	echo "print_report_button_setting('".$print_report_format."');\n";
	exit();
}

if ($action=="load_drop_down_buyer"){
	echo create_drop_down( "cbo_buyer_name", 172, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "","" );
	exit();
}

if ($action=="load_drop_down_buyer_pop"){
	if($data != 0)
	{
		echo create_drop_down( "cbo_buyer_name", 130, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
		exit();
	}
	else{
		echo create_drop_down( "cbo_buyer_name", 130, "SELECT buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
		exit();
	}
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

if($action=="job_po_level_validation")
{
	//echo $data;die;
	$data=explode("***", $data);
	$cbo_level=$data[0];
	$txt_order_no_id=$data[1];
	 
	$jobNo=return_field_value( "job_no_mst", "wo_po_break_down"," id in(".$txt_order_no_id.") and status_active=1 and is_deleted=0");
	
	$sqlChk=sql_select("select a.cbo_level from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and b.status_active=1 and b.job_no='$jobNo' and a.booking_type=2 and a.cbo_level>0 group by a.cbo_level");
	//echo "select a.cbo_level from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and b.status_active=1 and b.po_break_down_id in(".$txt_order_no_id.") and a.booking_type=2 and a.cbo_level>0 group by a.cbo_level";die;
	 
	foreach($sqlChk as $row){
		$previ_cbo_level=$row[csf('cbo_level')];
		if($previ_cbo_level!=$cbo_level)
		{
			$msg="Job/PO Level mix not allowed.";
			echo "100**".$msg;
			die;
		}
	}
}

if ($action=="fnc_process_data"){
	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $str_data;
	$buyer_arr=return_library_array("select id, short_name from lib_buyer",'id','short_name');
	$trim_group_library= return_library_array("select id, item_name from lib_item_group",'id','item_name');
	?>
	<script>
	var cbo_level='<? echo $cbo_level; ?>';
	var po_job_level=cbo_level;
		function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count-1;
			//alert(tbl_row_count)
			if(document.getElementById('check_all').checked==true)
			{
				po_job_level=1;
			}
			else if(document.getElementById('check_all').checked==false)
			{
				po_job_level=cbo_level;
			}
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}

		function toggle( x, origColor ) {
			//alert(x+'_'+origColor)
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
				var select_row=0; var sp=1;
				if(po_job_level==1)
				{
					var select_row= str;
					sp=1;
				}
				else if(po_job_level==2)
				{
					var tbl_length =$('#tbl_list_search tr').length-1;
					var select_str=$('#txt_job_no' + str).val()+'_'+$('#hiddtrim_group' + str).val()+'_'+$('#td_item_des' + str).text();
				
					for(var i=1; i<=tbl_length; i++)
					{
						var string=$('#txt_job_no' + i).val()+'_'+$('#hiddtrim_group' + i).val()+'_'+$('#td_item_des' + i).text();
						if(select_str==string)
						{
							//alert(select_str+'='+string);
							if(select_row==0)
							{
								select_row=i; sp=1;
							}
							else
							{
								select_row+=','+i; sp=2;
							}
						}
					}
				}
				var exrow = new Array();
				if(sp==2) { exrow=select_row.split(','); var countrow=exrow.length; }
				else countrow=1;
				//alert(select_row)

				//alert(exrow)
				for(var m=0; m<countrow; m++)
				{
					if(sp==2) exrow[m]=exrow[m];
					else exrow[m]=select_row;
					//alert(exrow[m])
					toggle( document.getElementById( 'search' + exrow[m] ), '#FFFFCC' );
					if( jQuery.inArray( $('#txt_individual_id' + exrow[m]).val(), selected_id ) == -1 ) {
						selected_id.push( $('#txt_individual_id' + exrow[m]).val() );
						selected_name.push($('#txt_job_no' + exrow[m]).val());
						selected_item.push($('#txt_trim_group_id' + exrow[m]).val());
						selected_po.push($('#txt_po_id' + exrow[m]).val());
					}
					else{
						for( var i = 0; i < selected_id.length; i++ ) {
							if( selected_id[i] == $('#txt_individual_id' + exrow[m]).val() ) break;
						}
						selected_id.splice( i, 1 );
						selected_name.splice( i,1 );
						selected_item.splice( i,1 );
						selected_po.splice( i,1 );
					}
				}
				var id = ''; var job = ''; var txt_trim_group_id=''; var txt_po_id='';
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
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
            <input type="hidden" id="txt_booking" value="" />
            <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
                <table width="750" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                    <thead>
                        <tr>
                            <th width="100">Style Ref </th>
                            <th width="100">Job No </th>
                            <th width="60">Year</th>
                            <th width="100">Int. Ref. No </th>
                            <th width="100">Order No</th>
                            <th width="120">Item Name</th>
                            <th>&nbsp;
                                <input type="hidden" id="txt_garments_nature" value="<? echo $garments_nature;?>" />
                                <input type="hidden" id="cbo_booking_month" value="<? echo $cbo_booking_month;?>" />
                                <input type="hidden" id="cbo_booking_year" value="<? echo $cbo_booking_year;?>" />
                                <input type="hidden" id="cbo_company_name" value="<? echo $company_id;?>" />
                                <input type="hidden" id="cbo_buyer_name" value="<? echo $cbo_buyer_name;?>" />
                                <input type="hidden" id="cbo_currency" value="<? echo $cbo_currency;?>" />
                                <input type="hidden" id="cbo_currency_job" value="<? echo $cbo_currency_job;?>" />
                                <input type="hidden" id="cbo_supplier_name" value="<? echo $cbo_supplier_name;?>" />
                                <input type="hidden" id="cbo_material_source" value="<? echo $cbo_material_source;?>" />
                            </th>
                        </tr>
                    </thead>
                    <tr>
                        <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:90px"></td>
                        <td><input name="txt_job" id="txt_job" class="text_boxes" style="width:90px"></td>
                        <td><? echo create_drop_down( "cbo_year_selection", 60, $year,"", 1, "-- Select --", date('Y'), "",0 );	?></td>
                        <td><input name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:100px"></td>
                        <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:90px"></td>
                        <td><? echo create_drop_down( "cbo_item", 120, "select a.id,a.item_name from  lib_item_group a where  a.status_active =1 and a.is_deleted=0 and a.item_category=4 order by a.item_name","id,item_name", 1, "-- Select Item Name --", $selected, "",0 ); ?></td>
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('cbo_booking_month').value+'_'+document.getElementById('cbo_booking_year').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_currency').value+'_'+document.getElementById('cbo_currency_job').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_job').value+'_'+document.getElementById('cbo_item').value+'_'+document.getElementById('txt_ref_no').value+'_'+document.getElementById('cbo_material_source').value+'_'+'<? echo $txt_booking_no; ?>'+'_'+'<? echo $cbo_source; ?>', 'create_fnc_process_data', 'search_div', 'trims_booking_multi_job_controllerurmi','setFilterGrid(\'tbl_list_search\',-1)')" style="width:60px;" />
                        </td>
                    </tr>
                </table>
            </form>
        </div>
        <div id="search_div"></div>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="create_fnc_process_data")
{
	$data=explode('_',$data);

	$company_id=$data[0];
	$cbo_buyer_name=$data[1];
	$cbo_supplier_name=$data[2];
	$cbo_booking_month=$data[3];

	$cbo_booking_year=$data[4];
	$cbo_year_selection=$data[5];
	$cbo_currency=$data[6];
	$cbo_currency_job=$data[7];

	$txt_style=$data[8];
	$txt_order_search=$data[9];
	$txt_job=$data[10];
	$cbo_item=$data[11];
	$ref_no=$data[12];
	$material_source=$data[13];
	$booking_no=$data[14];
	$cbo_source=$data[15];

	//echo $txt_order_search; die;
	if ($txt_style!="") $style_cond=" and a.style_ref_no='$txt_style'"; else $style_cond=$txt_style;
	if ($txt_order_search!="") $order_cond=" and b.po_number='$txt_order_search'"; else $order_cond="";
	if ($txt_order_search!="") $order_cond2=" and c.po_number='$txt_order_search'"; else $order_cond2="";
	if ($ref_no!="") $ref_cond=" and b.grouping='$ref_no'"; else $ref_cond="";
	if ($txt_job!="") $job_cond=" and a.job_no_prefix_num='$txt_job'"; else $job_cond ="";
	if ($cbo_item!=0) $itemgroup_cond=" and c.trim_group=$cbo_item"; else $itemgroup_cond ="";
	if ($cbo_item!=0) $itemgroup_cond2=" and d.trim_group=$cbo_item"; else $itemgroup_cond2 ="";
	if ($cbo_item!=0) $itemgroup_cond3=" and trim_group=$cbo_item"; else $itemgroup_cond3 ="";
	//echo $itemgroup_cond;die;
	//echo $itemgroup_cond3;

	$buyer_arr=return_library_array("select id, short_name from lib_buyer",'id','short_name');
	//$trim_group_library= return_library_array("select id, item_name from lib_item_group",'id','item_name');
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

		var selected_id = new Array(); var selected_name = new Array(); var selected_item=new Array(); var selected_po=new Array();

		function js_set_value( str )
		{
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
			var id = ''; var job = ''; var txt_trim_group_id=''; var txt_po_id='';
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
	</head>
	<body>
	<div style="width:1220px;">
	<?
	extract($_REQUEST);
	$booking_month=0;
	if($cbo_booking_month<10) $booking_month.=$cbo_booking_month; else $booking_month=$cbo_booking_month;

	$start_date=$cbo_booking_year."-".$booking_month."-01";
	$end_date=$cbo_booking_year."-".$booking_month."-".cal_days_in_month(CAL_GREGORIAN, $booking_month, $cbo_booking_year);

	if($db_type==0){
		if ($start_date!="" &&  $end_date!="") $shipment_date = "and d.pub_shipment_date between '".change_date_format($start_date, "yyyy-mm-dd", "-")."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}
	else if($db_type==2){
		if ($start_date!="" &&  $end_date!="") $shipment_date = "and d.pub_shipment_date between '".change_date_format($start_date, "yyyy-mm-dd", "-",1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}
	?>
	<input type="hidden" name="txt_selected_id" id="txt_selected_id" value="" />
	<input type="hidden" name="itemGroup" id="itemGroup" value="" />
	<input type="hidden" name="txt_job_id" id="txt_job_id" value="" />
	<input type="hidden" name="txt_selected_po" id="txt_selected_po" value="" />

	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1200" class="rpt_table"  >
        <thead>
            <th width="20">SL</th>
            <th width="50">Buyer</th>
            <th width="50">Year</th>
            <th width="50">Job No</th>
            <th width="60">File No</th>
            <th width="60">Ref. No</th>
            <th width="100">Style No</th>
            <th width="100">Ord. No</th>
            <th width="100">Trim Group</th>
            <th width="130">Desc.</th>
            <th width="70">Brand/Sup.Ref</th>
            <th width="70">Req. Qty</th>
            <th width="45">UOM</th>
            <th width="70">CU WOQ</th>
            <th width="70">Bal WOQ</th>
            <th width="45">Exch. Rate</th>
            <th width="40">Rate</th>
            <th>Amount</th>
        </thead>
	</table>
	<div style="width:1220px; overflow-y:scroll; max-height:350px;" id="buyer_list_view" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1200" class="rpt_table" id="tbl_list_search" >
        <?
		 $isUsedSourchingPosCostSheet=return_field_value("copy_quotation", "variable_order_tracking", "company_name=$data[0] and variable_list=79 and status_active=1 and is_deleted=0");
		if($isUsedSourchingPosCostSheet==1) $pageIdCond=36; else $pageIdCond=37;
		$sql=sql_select("select b.approval_need, b.validate_page, b.allow_partial from approval_setup_mst a, approval_setup_dtls b where a.id=b.mst_id and a.company_id='$data[0]' and b.page_id in ($pageIdCond) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.setup_date");
		//echo "select b.page_id, b.approval_need, b.validate_page, b.allow_partial from approval_setup_mst a, approval_setup_dtls b where a.id=b.mst_id and a.company_id='$data[0]' and b.page_id in (36,37) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.setup_date asc";//die;
		$app_nessity=2; $validate_page=0; $allow_partial=2;
		foreach($sql as $row){
			$app_nessity=$row[csf('approval_need')];
			$validate_page=$row[csf('validate_page')];
			$allow_partial=$row[csf('allow_partial')];
		}
		//echo $isSourching.'='.$app_nessity;
		$sourcingAppCond=""; //Dont HIde Issue id ISD-21-04462
		$bomAppCond=""; //Dont HIde Issue id ISD-21-15488
		if($isUsedSourchingPosCostSheet==1)
		{
			if($app_nessity==1)
			{
				 if($allow_partial==1) $sourcingAppCond=" and c.sourcing_approved in (1,3)";
				 else $sourcingAppCond=" and c.sourcing_approved=1";
			} else $sourcingAppCond="";
		}
		else
		{
			if($app_nessity==1)
			{
				 if($allow_partial==1) $bomAppCond=" and c.approved in (1,3)";
				 else $bomAppCond=" and c.approved in (1)";
			}else $bomAppCond="";
		}
		//echo $isUsedSourchingPosCostSheet.'='.$bomAppCond;
		
        if($db_type==0){
            if ($start_date!="" &&  $end_date!="") $class_datecond = "between '".change_date_format($start_date, "yyyy-mm-dd", "-")."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'"; else $class_datecond ="";

            $year_field="YEAR(a.insert_date) as year";
            if ($cbo_year_selection!="") $year_cond=" and YEAR(a.insert_date)='$cbo_year_selection'"; else $year_cond ="";
        }
        else if($db_type==2){
            if ($start_date!="" &&  $end_date!="") $class_datecond = "between '".change_date_format($start_date, "yyyy-mm-dd", "-",1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-",1)."'"; else $class_datecond ="";
            $year_field="to_char(a.insert_date,'YYYY') as year";
            if ($cbo_year_selection!="") $year_cond=" and to_char(a.insert_date,'YYYY')='$cbo_year_selection'"; else $year_cond ="";
        }
	 $sql_vari_lib="select item_category_id,variable_list, excut_source  from variable_order_tracking where company_name=".$company_id." and item_category_id=4  and variable_list=72 and status_active=1"; 
	$result_vari_lib=sql_select($sql_vari_lib);
	$source_from=1;//$woven_category_id=0;
	foreach($result_vari_lib as $row)
	{
		if($row[csf('excut_source')]>0)
		{
			$source_from=$row[csf('excut_source')];
		}
	}
	unset($result_vari_lib);
	$exceed_qty_level=return_field_value("exceed_qty_level", "variable_order_tracking", "company_name=$company_id  and variable_list=26 and status_active=1 and is_deleted=0");
	if( $exceed_qty_level==0 || $exceed_qty_level==2 || $exceed_qty_level=="") $exceed_qty_level=2;else $exceed_qty_level=$exceed_qty_level;
	

    	$job_no_sql_arr=sql_select("SELECT a.job_no,a.id, c.id as po_id from wo_po_details_master a join wo_pre_cost_mst b on a.id=b.job_id join wo_po_break_down c on a.id=c.job_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.garments_nature=3 and c.shiping_status not in(3) and a.company_name=$company_id and a.buyer_name=$cbo_buyer_name $job_cond $style_cond $order_cond2 $year_cond group by a.job_no,a.id, c.id");
		foreach ($job_no_sql_arr as $row) {
			$job_no_arr[$row[csf('id')]] = $row[csf('id')];
			$job_no_txt_arr[$row[csf('job_no')]] = $row[csf('job_no')];
			$jobpoid_arr[$row[csf('po_id')]]=$row[csf('po_id')];
		}
		$jobids = "'" . implode( "','", $job_no_arr ) . "'";
		$job_no_txt = "'" . implode( "','", $job_no_txt_arr ) . "'";
		if ($job_no_txt!="") $trim_job_cond=" and job_no in ($job_no_txt)"; else $trim_job_cond ="";

		$poIds_bom_cond=where_con_using_array($jobpoid_arr,0,"b.id");
		
		if($source_from==2) //Sourcing from Lib///////////Issue Id=19634, Windy
		{
			$trim_id_sql = sql_select("SELECT id from wo_pre_cost_trim_cost_dtls where (sourcing_nominated_supp is null or sourcing_nominated_supp='0' )  and status_active=1 and is_deleted=0 $itemgroup_cond3 $trim_job_cond");
		}
		else
		{
		$trim_id_sql = sql_select("SELECT id from wo_pre_cost_trim_cost_dtls where (nominated_supp_multi is null or nominated_supp_multi='0' )  and status_active=1 and is_deleted=0 $itemgroup_cond3 $trim_job_cond");
		}
		foreach ($trim_id_sql as $row) {
			$trimid_arr[$row[csf('id')]] = $row[csf('id')];
		}
		if($source_from==2) //Sourcing from 
		{
			$sql_supp="select trimid from wo_pre_cost_trim_sup_sourcing where job_id in ($jobids) and supplier_id in($cbo_supplier_name) and is_deleted=0 and status_active=1";
		}
		else
		{
			$sql_supp="select trimid from wo_pre_cost_trim_supplier where job_id in ($jobids) and supplier_id in($cbo_supplier_name) and is_deleted=0 and status_active=1";
		}
		//echo $sql_supp; die;
	    $sql_suppRes=sql_select( $sql_supp ); $trim_id="";
	    foreach($sql_suppRes as $row)
	    {
	      $trimid_arr[$row[csf('trimid')]] = $row[csf('trimid')];
	    }
	    unset($sql_suppRes);
	    if($source_from==2) //Sourcing from 
		{
			if($db_type==2)
			{
				if(count($trimid_arr)>0) $trim_idCond=where_con_using_array($trimid_arr,0,"d.id"); else $trim_idCond=" and (d.sourcing_nominated_supp is null or d.sourcing_nominated_supp='0')";
			}
			else
			{
				if(count($trimid_arr)>0) $trim_idCond=where_con_using_array($trimid_arr,0,"d.id"); else $trim_idCond=" and d.sourcing_nominated_supp=''";
			}
		}
		else
		{
			if($db_type==2)
			{
				if(count($trimid_arr)>0) $trim_idCond=where_con_using_array($trimid_arr,0,"d.id"); else $trim_idCond=" and (d.nominated_supp_multi is null or d.nominated_supp_multi='0')";
			}
			else
			{
				if(count($trimid_arr)>0) $trim_idCond=where_con_using_array($trimid_arr,0,"d.id"); else $trim_idCond=" and d.nominated_supp_multi=''";
			}
		}
		//echo $trim_idCond; die;

        $lib_item_group_arr=array();
        $sql_lib_item_group=sql_select("select id, item_name, conversion_factor, order_uom as cons_uom from lib_item_group");
        foreach($sql_lib_item_group as $itemrow){
            $lib_item_group_arr[$itemrow[csf('id')]][item_name]=$itemrow[csf('item_name')];
            $lib_item_group_arr[$itemrow[csf('id')]][conversion_factor]=$itemrow[csf('conversion_factor')];
            $lib_item_group_arr[$itemrow[csf('id')]][cons_uom]=$itemrow[csf('cons_uom')];
        }
        unset($sql_lib_item_group);
		

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
            $condition->po_number("=$txt_order_search");
        }
        if(str_replace("'","",$ref_no)!='')
        {
            $condition->grouping("='$ref_no'");
        }

        $condition->init();
        $trims= new trims($condition);
        $req_qty_arr=$trims->getQtyArray_by_orderAndPrecostdtlsid();
		//echo $source_from.'ss';
		if($source_from==2) //Sourcing
		{
		  $req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidSourcing();
		}
		else
		{
      	  $req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsid();
		}
        /*$req_amount_arr=$trims->getAmountArray_by_jobAndPrecostdtlsid_consAndTotcons();
		$req_qty_arr=$trims->getQtyArray_by_jobAndPrecostdtlsid_consAndTotcons();*/

        $cu_booking_arr=array();
		$sql_cu_booking=sql_select("SELECT c.pre_cost_fabric_cost_dtls_id, c.po_break_down_id, sum(c.wo_qnty) as cu_wo_qnty, sum(c.amount) as cu_amount from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c, wo_booking_mst d where a.job_no=b.job_no_mst and a.job_no=c.job_no and b.id=c.po_break_down_id and d.booking_no=c.booking_no and a.garments_nature=3 and a.company_name=$company_id and a.buyer_name=$cbo_buyer_name  and c.booking_type=2 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $job_cond $order_cond $ref_cond $style_cond $itemgroup_cond group by a.job_no, c.pre_cost_fabric_cost_dtls_id, c.po_break_down_id");
		
        foreach($sql_cu_booking as $row_cu_booking){
            $cu_booking_arr[$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_cu_booking[csf('po_break_down_id')]]['cu_wo_qnty']=$row_cu_booking[csf('cu_wo_qnty')];
            $cu_booking_arr[$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_cu_booking[csf('po_break_down_id')]]['cu_amount']=$row_cu_booking[csf('cu_amount')];
        }
        unset($sql_cu_booking);

        $source_cond='';
		if(!empty($cbo_source))
		{
			if($cbo_source*1==1)
			{
				$source_cond=" and d.source_id in (1,0)";
			}
			else{
				$source_cond=" and d.source_id in (2,0)";
			}
		}
		// $trim_idCond 
		 $sql_name="SELECT a.job_no_prefix_num, a.job_no, $year_field, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.id as po_id, b.po_number, b.file_no, b.grouping, b.po_quantity as plan_cut, c.costing_per, c.exchange_rate, d.id as trim_cost_id, d.trim_group, d.description, d.brand_sup_ref, d.rate, min(e.id) as id, e.po_break_down_id, avg(e.cons) AS cons from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c, wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.id=b.job_id and a.id=c.job_id and b.job_id=c.job_id and c.job_id=d.job_id and d.id=e.wo_pre_cost_trim_cost_dtls_id and b.id=e.po_break_down_id and a.garments_nature=3 and d.is_deleted=0 and d.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.company_name=$company_id  and d.material_source=$material_source $job_cond $poIds_bom_cond $itemgroup_cond2 $ref_cond $style_cond $year_cond $bomAppCond $trim_idCond $sourcingAppCond $source_cond group by a.job_no_prefix_num, a.job_no, a.insert_date, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.id, b.po_number, b.file_no, b.grouping, b.po_quantity, c.costing_per, c.exchange_rate, d.id, d.trim_group, d.description, d.brand_sup_ref, d.rate, e.po_break_down_id order by b.id, d.id";
		 //$order_cond
		$nameArray=sql_select($sql_name);
	
	 	//echo $sql_name;
		//echo count($nameArray);
        $i=1; $total_req=0; $total_amount=0;
        foreach ($nameArray as $selectResult)
        {
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$cbo_currency_job=$selectResult[csf('currency_id')];
			$exchange_rate=$selectResult[csf('exchange_rate')];
			if($cbo_currency==$cbo_currency_job){
				$exchange_rate=1;
			}
			$req_qnty_cons_uom=$req_qty_arr[$selectResult[csf('po_id')]][$selectResult[csf('trim_cost_id')]];

			$req_amount_cons_uom=$req_amount_arr[$selectResult[csf('po_id')]][$selectResult[csf('trim_cost_id')]];

			$rate_cons_uom=$req_amount_cons_uom/$req_qnty_cons_uom;

			$req_qnty=def_number_format($req_qnty_cons_uom/$lib_item_group_arr[$selectResult[csf('trim_group')]][conversion_factor],5,"");
			$cu_wo_qnty=def_number_format($cu_booking_arr[$selectResult[csf('trim_cost_id')]][$selectResult[csf('po_id')]]['cu_wo_qnty'],5,"");
			$cu_wo_amnt=def_number_format($cu_booking_arr[$selectResult[csf('trim_cost_id')]][$selectResult[csf('po_id')]]['cu_amount'],5,"");
			$bal_woq=def_number_format($req_qnty-$cu_wo_qnty,5,"");

			$rate=def_number_format(($rate_cons_uom*$lib_item_group_arr[$selectResult[csf('trim_group')]][conversion_factor])*$exchange_rate,5,"");
			$req_amount=def_number_format($req_qnty*$rate,5,"");
			$bal_wom=$req_amount-$cu_wo_amnt;

			$total_req_amount+=$req_amount;
			$total_cu_amount+=$selectResult[csf('cu_amount')];

			$total_req+=$req_qnty;
			$amount=def_number_format($rate*$bal_woq,4,"");
			//echo $req_qnty.'='.$cu_wo_qnty.'<br>';
			if($bal_woq>0 && ($cu_wo_qnty=="" || $cu_wo_qnty==0)  && $exceed_qty_level==2)
			{
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
					<td width="20"><? echo $i;?>
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $selectResult[csf('id')]; ?>"/>
						<input type="hidden" name="txt_trim_group_id" id="txt_trim_group_id<?php echo $i ?>" value="<? echo $selectResult[csf('trim_cost_id')]; ?>"/>
						<input type="hidden" name="txt_job_no" id="txt_job_no<?php echo $i ?>" value="<? echo $selectResult[csf('job_no')]; ?>"/>
						<input type="hidden" name="txt_po_id" id="txt_po_id<?php echo $i ?>" value="<? echo $selectResult[csf('po_id')]; ?>"/>
						<input type="hidden" name="hiddtrim_group" id="hiddtrim_group<?php echo $i ?>" value="<?=$selectResult[csf('trim_group')];?>"/>
					</td>
					<td width="50"><p><? echo $buyer_arr [$selectResult[csf('buyer_name')]];?></p></td>
					<td width="50"><p><? echo $selectResult[csf('year')];?></p></td>
					<td width="50"><p><? echo $selectResult[csf('job_no_prefix_num')];?></p></td>
					<td width="60"><p><? echo $selectResult[csf('file_no')];?></p></td>
					<td width="60"><p><? echo $selectResult[csf('grouping')];?></p></td>
					<td width="100"><div style="width:100px; word-wrap:break-word;"><? echo $selectResult[csf('style_ref_no')];?></div></td>
					<td width="100"><p><? echo $selectResult[csf('po_number')];?></p></td>
					<td width="100"><div style="width:100px; word-wrap:break-word;"><? echo $lib_item_group_arr[$selectResult[csf('trim_group')]][item_name];?></div></td>
					<td width="130" id="td_item_des<?php echo $i; ?>"><div style="width:130px; word-wrap:break-word;"><? echo $selectResult[csf('description')];?></div></td>
					<td width="70"><div style="width:70px; word-wrap:break-word;"><? echo $selectResult[csf('brand_sup_ref')];?></div></td>
					<td width="70" align="right"><? echo number_format($req_qnty,4); ?></td>
					<td width="45"><? echo $unit_of_measurement[$lib_item_group_arr[$selectResult[csf('trim_group')]][cons_uom]];?></td>
					<td width="70" align="right"><? echo def_number_format($cu_wo_qnty,5,"");?></td>
					<td width="70" align="right"><? echo number_format($bal_woq,4); ?></td>
					<td width="45" align="right"><p><? echo number_format($exchange_rate,2); ?></p></td>
					<td width="40" align="right"><p><? echo number_format($rate,4); ?></p></td>
					<td align="right"><? echo number_format($amount,2); ?></td>
				</tr>
				<?
				$i++;
				$total_amount+=$amount;
			}
			else if($bal_woq>0 && $cu_wo_qnty>0)//>=1
			{
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
					<td width="20"><? echo $i;?>
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $selectResult[csf('id')]; ?>"/>
						<input type="hidden" name="txt_trim_group_id" id="txt_trim_group_id<?php echo $i ?>" value="<? echo $selectResult[csf('trim_cost_id')]; ?>"/>
						<input type="hidden" name="txt_job_no" id="txt_job_no<?php echo $i ?>" value="<? echo $selectResult[csf('job_no')]; ?>"/>
						<input type="hidden" name="txt_po_id" id="txt_po_id<?php echo $i ?>" value="<? echo $selectResult[csf('po_id')]; ?>"/>
						<input type="hidden" name="hiddtrim_group" id="hiddtrim_group<?php echo $i ?>" value="<?=$selectResult[csf('trim_group')];?>"/>
					</td>
					<td width="50"><p><? echo $buyer_arr [$selectResult[csf('buyer_name')]];?></p></td>
					<td width="50"><p><? echo $selectResult[csf('year')];?></p></td>
					<td width="50"><p><? echo $selectResult[csf('job_no_prefix_num')];?></p></td>
					<td width="60"><p><? echo $selectResult[csf('file_no')];?></p></td>
					<td width="60"><p><? echo $selectResult[csf('grouping')];?></p></td>
					<td width="100"><div style="width:100px; word-wrap:break-word;"><? echo $selectResult[csf('style_ref_no')];?></div></td>
					<td width="100"><p><? echo $selectResult[csf('po_number')];?></p></td>
					<td width="100"><div style="width:100px; word-wrap:break-word;"><? echo $lib_item_group_arr[$selectResult[csf('trim_group')]][item_name];?></div></td>
					<td width="130" id="td_item_des<?php echo $i; ?>"><div style="width:130px; word-wrap:break-word;"><? echo $selectResult[csf('description')];?></div></td>
					<td width="70"><div style="width:70px; word-wrap:break-word;"><? echo $selectResult[csf('brand_sup_ref')];?></div></td>
					<td width="70" align="right"><? echo number_format($req_qnty,4); ?></td>
					<td width="45"><? echo $unit_of_measurement[$lib_item_group_arr[$selectResult[csf('trim_group')]][cons_uom]];?></td>
					<td width="70" align="right"><? echo def_number_format($cu_wo_qnty,5,"");?></td>
					<td width="70" align="right"><? echo number_format($bal_woq,4); ?></td>
					<td width="45" align="right"><p><? echo number_format($exchange_rate,2); ?></p></td>
					<td width="40" align="right"><p><? echo number_format($rate,4); ?></p></td>
					<td align="right"><? echo number_format($amount,2); ?></td>
				</tr>
				<?
				$i++;
				$total_amount+=$amount;
			}
			else if($bal_wom>0  && $exceed_qty_level==1)
			{
				//echo $bal_wom.'='.$exceed_qty_level;die;
				?>
				<tr bgcolor="<?=$bgcolor;?>" style="text-decoration:none; cursor:pointer" id="search<?=$i;?>" onClick="js_set_value(<?=$i;?>)">
					<td width="20"><?=$i;?>
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $selectResult[csf('id')]; ?>"/>
						<input type="hidden" name="txt_trim_group_id" id="txt_trim_group_id<?php echo $i ?>" value="<? echo $selectResult[csf('trim_cost_id')]; ?>"/>
						<input type="hidden" name="txt_job_no" id="txt_job_no<?php echo $i ?>" value="<? echo $selectResult[csf('job_no')]; ?>"/>
						<input type="hidden" name="txt_po_id" id="txt_po_id<?php echo $i ?>" value="<? echo $selectResult[csf('po_id')]; ?>"/>
                        <input type="hidden" name="hiddtrim_group" id="hiddtrim_group<?php echo $i ?>" value="<?=$selectResult[csf('trim_group')];?>"/>
					</td>
					<td width="50"><p><? echo $buyer_arr [$selectResult[csf('buyer_name')]];?></p></td>
					<td width="50"><p><? echo $selectResult[csf('year')];?></p></td>
					<td width="50"><p><? echo $selectResult[csf('job_no_prefix_num')];?></p></td>
					<td width="60"><p><? echo $selectResult[csf('file_no')];?></p></td>
					<td width="60"><p><? echo $selectResult[csf('grouping')];?></p></td>
					<td width="100"><div style="width:100px; word-wrap:break-word;"><? echo $selectResult[csf('style_ref_no')];?></div></td>
					<td width="100"><p><? echo $selectResult[csf('po_number')];?></p></td>
					<td width="100"><div style="width:100px; word-wrap:break-word;"><? echo $lib_item_group_arr[$selectResult[csf('trim_group')]][item_name];?></div></td>
					<td width="130" id="td_item_des<?php echo $i; ?>"><div style="width:130px; word-wrap:break-word;"><? echo $selectResult[csf('description')];?></div></td>
					<td width="70"><div style="width:70px; word-wrap:break-word;"><? echo $selectResult[csf('brand_sup_ref')];?></div></td>
					<td width="70" align="right"><? echo number_format($req_qnty,4); ?></td>
					<td width="45"><? echo $unit_of_measurement[$lib_item_group_arr[$selectResult[csf('trim_group')]][cons_uom]];?></td>
					<td width="70" align="right"><? echo def_number_format($cu_wo_qnty,5,"");?></td>
					<td width="70" align="right"><? echo number_format($bal_woq,4); ?></td>
					<td width="45" align="right"><p><? echo number_format($exchange_rate,2); ?></p></td>
					<td width="40" align="right"><p><? echo number_format($rate,4); ?></p></td>
					<td align="right"><? echo number_format($amount,2); ?></td>
				</tr>
				<?
				$i++;
				$total_amount+=$amount;
			}
			else if($bal_woq>0  && $cbo_material_source==3)
			{
				//echo $bal_wom.'='.$exceed_qty_level;die;
				?>
				<tr bgcolor="<?=$bgcolor;?>" style="text-decoration:none; cursor:pointer" id="search<?=$i;?>" onClick="js_set_value(<?=$i;?>)">
					<td width="20"><?=$i;?>
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $selectResult[csf('id')]; ?>"/>
						<input type="hidden" name="txt_trim_group_id" id="txt_trim_group_id<?php echo $i ?>" value="<? echo $selectResult[csf('trim_cost_id')]; ?>"/>
						<input type="hidden" name="txt_job_no" id="txt_job_no<?php echo $i ?>" value="<? echo $selectResult[csf('job_no')]; ?>"/>
						<input type="hidden" name="txt_po_id" id="txt_po_id<?php echo $i ?>" value="<? echo $selectResult[csf('po_id')]; ?>"/>
                        <input type="hidden" name="hiddtrim_group" id="hiddtrim_group<?php echo $i ?>" value="<?=$selectResult[csf('trim_group')];?>"/>
                        
					</td>
					<td width="50"><p><? echo $buyer_arr [$selectResult[csf('buyer_name')]];?></p></td>
					<td width="50"><p><? echo $selectResult[csf('year')];?></p></td>
					<td width="50"><p><? echo $selectResult[csf('job_no_prefix_num')];?></p></td>
					<td width="60"><p><? echo $selectResult[csf('file_no')];?></p></td>
					<td width="60"><p><? echo $selectResult[csf('grouping')];?></p></td>
					<td width="100"><div style="width:100px; word-wrap:break-word;"><? echo $selectResult[csf('style_ref_no')];?></div></td>
					<td width="100"><p><? echo $selectResult[csf('po_number')];?></p></td>
					<td width="100"><div style="width:100px; word-wrap:break-word;"><? echo $lib_item_group_arr[$selectResult[csf('trim_group')]][item_name];?></div></td>
					<td width="130" id="td_item_des<?php echo $i; ?>"><div style="width:130px; word-wrap:break-word;"><? echo $selectResult[csf('description')];?></div></td>
					<td width="70"><div style="width:70px; word-wrap:break-word;"><? echo $selectResult[csf('brand_sup_ref')];?></div></td>
					<td width="70" align="right"><? echo number_format($req_qnty,4); ?></td>
					<td width="45"><? echo $unit_of_measurement[$lib_item_group_arr[$selectResult[csf('trim_group')]][cons_uom]];?></td>
					<td width="70" align="right"><? echo def_number_format($cu_wo_qnty,5,"");?></td>
					<td width="70" align="right"><? echo number_format($bal_woq,4); ?></td>
					<td width="45" align="right"><p><? echo number_format($exchange_rate,2); ?></p></td>
					<td width="40" align="right"><p><? echo number_format($rate,4); ?></p></td>
					<td align="right"><? echo number_format($amount,2); ?></td>
				</tr>
				<?
				$i++;
				$total_amount+=$amount;
			}
        }
        ?>
        </table>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1200" class="rpt_table">
        	<tfoot>
                <th width="20">&nbsp;</th>
                <th width="50">&nbsp;</th>
                <th width="50">&nbsp;</th>
                <th width="50">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="130">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="70" id="value_total_req"></th>
                <th width="45"><input type="hidden" style="width:40px"  id="txt_tot_req_amount" value="<? echo number_format($total_req_amount,2); ?>" /></th>
                <th width="70"><input type="hidden" style="width:40px" id="txt_tot_cu_amount" value="<? echo number_format($total_cu_amount,2); ?>" /></th>
                <th width="70">&nbsp;</th>
                <th width="45">&nbsp;</th>
                <th width="40">&nbsp;</th>
                <th id="value_total_amount"><? echo number_format($total_amount,2); ?></th>
            </tfoot>
        </table>
	</div>
	<table width="790" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
	<script>
		var tableFilters = {
			col_operation: {
				id: ["value_total_amount"],
				col: [17],
				operation: ["sum"],
				write_method: ["innerHTML"]
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

if ($action=="generate_fabric_booking")
{
	extract($_REQUEST);
	$trim_group_library= return_library_array("select id, item_name from lib_item_group",'id','item_name');
	$booking_month=0;
	if($cbo_booking_month<10) $booking_month.=$cbo_booking_month; else $booking_month=$cbo_booking_month;
	
	if($garments_nature==0) $garment_nature_cond=""; else $garment_nature_cond=" and a.garments_nature=$garments_nature";
	
	$sql_vari_lib="select item_category_id,variable_list,excut_source  from variable_order_tracking where company_name=".$cbo_company_name." and item_category_id=4  and variable_list=72 and status_active=1"; 
	//echo "select item_category_id,variable_list,excut_source  from variable_order_tracking where company_name=".$cbo_company_name." and item_category_id=4  and variable_list=72 and status_active=1";
	$result_vari_lib=sql_select($sql_vari_lib);
	$source_from=1;//$woven_category_id=0;
	foreach($result_vari_lib as $row)
	{
		if($row[csf('excut_source')]>0)
		{
			$source_from=$row[csf('excut_source')];
		}
	}
	$start_date=$cbo_booking_year."-".$booking_month."-01";
	$end_date=$cbo_booking_year."-".$booking_month."-".cal_days_in_month(CAL_GREGORIAN, $booking_month, $cbo_booking_year);
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
	//echo $source_from.'D';
	$condition->init();
	$trims= new trims($condition);
	$req_qty_arr=$trims->getQtyArray_by_orderAndPrecostdtlsid();
	if($source_from==2) //Sourcing Budget pAGE	
	{
		$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidSourcing();
		$reqAmountJobLevelArr=$trims->getAmountArray_by_jobSourcing();
	}
	else
	{
		$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsid();
		$reqAmountJobLevelArr=$trims->getAmountArray_by_job();
	}
	/*$req_amount_arr=$trims->getAmountArray_by_jobAndPrecostdtlsid_consAndTotcons();
	$req_qty_arr=$trims->getQtyArray_by_jobAndPrecostdtlsid_consAndTotcons();*/
	
	$cu_booking_arr=array();
	$sql_cu_booking=sql_select("select c.job_no,c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id, sum(c.wo_qnty) as cu_wo_qnty, sum(c.amount) as cu_amount from wo_po_details_master a, wo_po_break_down  d , wo_booking_dtls c,wo_booking_mst e where a.job_no=d.job_no_mst and a.job_no=c.job_no  and c.booking_no=e.booking_no and  d.id=c.po_break_down_id and a.company_name=$cbo_company_name $shipment_date and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0   group by c.job_no, c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id");
	foreach($sql_cu_booking as $row_cu_booking){
		$cu_booking_arr[$row_cu_booking[csf('job_no')]][$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]]['cu_woq'][$row_cu_booking[csf('po_break_down_id')]] = $row_cu_booking[csf('cu_wo_qnty')];
		$cu_booking_arr[$row_cu_booking[csf('job_no')]][$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]]['cu_amount'][$row_cu_booking[csf('po_break_down_id')]] = $row_cu_booking[csf('cu_amount')];
	}
	unset($sql_cu_booking);

	 $sql="SELECT a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.currency_id,	a.style_ref_no,	b.costing_per, b.exchange_rate,	c.id as wo_pre_cost_trim_cost_dtls,	c.trim_group, c.description, c.brand_sup_ref, c.country, c.rate,c.amount, d.id as po_id,	d.po_number, d.po_quantity as plan_cut,	min(e.id) as id, e.po_break_down_id, avg(e.cons) as cons from wo_po_details_master a, wo_pre_cost_mst b, wo_pre_cost_trim_cost_dtls c,	wo_po_break_down d,	wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no and a.job_no=c.job_no and a.job_no=d.job_no_mst and a.job_no=e.job_no and c.id=e.wo_pre_cost_trim_cost_dtls_id and d.id=e.po_break_down_id and a.company_name=$cbo_company_name $garment_nature_cond and e.id in($param) and e.po_break_down_id in($data) and c.id in($pre_cost_id) and d.is_deleted=0 and d.status_active=1 group by a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id, c.trim_group, c.description, c.brand_sup_ref, c.country,c.amount, c.rate, d.id, d.po_number, d.po_quantity, e.po_break_down_id order by d.id,c.id";
	$job_and_trimgroup_level=array();
	$i=1;
	$nameArray=sql_select( $sql );

	foreach ($nameArray as $selectResult){
		$job_no=$selectResult[csf('job_no')];
		$trim_cost_dtls_id=$selectResult[csf('wo_pre_cost_trim_cost_dtls')];
		$po_id=$selectResult[csf('po_id')];
		
		$cbo_currency_job=$selectResult[csf('currency_id')];
		$exchange_rate=$selectResult[csf('exchange_rate')];
		if($cbo_currency==$cbo_currency_job) $exchange_rate=1;

		$req_qnty_cons_uom=$req_qty_arr[$po_id][$trim_cost_dtls_id];
		$req_amount_cons_uom=$req_amount_arr[$po_id][$trim_cost_dtls_id];
		$rate_cons_uom=$req_amount_cons_uom/$req_qnty_cons_uom;

		$req_qnty_ord_uom=def_number_format($req_qnty_cons_uom/$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor],5,"");
		$rate_ord_uom=def_number_format(($rate_cons_uom*$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor])*$exchange_rate,5,"");
		$req_amount_ord_uom=def_number_format($req_qnty_ord_uom*$rate_ord_uom,5,"");

		$cu_woq=$cu_booking_arr[$job_no][$trim_cost_dtls_id]['cu_woq'][$po_id];
		$cu_amount=$cu_booking_arr[$job_no][$trim_cost_dtls_id]['cu_amount'][$po_id];

		$bal_woq=def_number_format($req_qnty_ord_uom-$cu_woq,5,"");
		$amount=def_number_format($rate_ord_uom*$bal_woq,5,"");

	    //$reqAmtJobLevelConsUom=$reqAmountJobLevelArr[$job_no];

		$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['job_no'][$po_id]=$job_no;
		$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['po_id'][$po_id]=$po_id;
		$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['style_ref_no'][$po_id]=$selectResult[csf('style_ref_no')];
		$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['po_number'][$po_id]=$selectResult[csf('po_number')];
		$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['country'][$po_id]=$selectResult[csf('country')];
		$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['description'][$po_id]=$selectResult[csf('description')];
		$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['brand_sup_ref'][$po_id]=$selectResult[csf('brand_sup_ref')];
		$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['trim_group'][$po_id]=$selectResult[csf('trim_group')];
		$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['trim_group_name'][$po_id]=$trim_group_library[$selectResult[csf('trim_group')]];
		$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['wo_pre_cost_trim_cost_dtls'][$po_id]=$trim_cost_dtls_id;

		$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['req_qnty'][$po_id]=$req_qnty_ord_uom;
		$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['uom'][$po_id]=$sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom];

		$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['uom_name'][$po_id]=$unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom]];

		$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['req_amount'][$po_id]=$req_amount_ord_uom;
		$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['req_amount_cons_uom'][$po_id]=$req_amount_cons_uom;
		$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['pre_req_amt'][$po_id]=$selectResult[csf('amount')];
		//echo $selectResult[csf('amount')].'DS';
		//$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['pre_req_amt'][$po_id]=$row[csf('amount')];
		//$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['req_amount_job_lebel_cons_uom'][$po_id]=$reqAmtJobLevelConsUom;

		$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['cu_woq'][$po_id]=$cu_woq;
		$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['cu_amount'][$po_id]=$cu_amount;
		$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['bal_woq'][$po_id]=$bal_woq;
		$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['exchange_rate'][$po_id]=$exchange_rate;
		$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['rate'][$po_id]=$rate_ord_uom;
		$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['amount'][$po_id]=$amount;
		$job_and_trimgroup_level[$job_no][$trim_cost_dtls_id]['txt_delivery_date'][$po_id]=$txt_delivery_date;
		$trim_group_arr[$selectResult[csf('trim_group')]]=$selectResult[csf('trim_group')];
	}
	?>
	<input type="hidden" id="strdata" value='<?=json_encode($job_and_trimgroup_level); ?>' style="background-color:#CCC"/>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1500" class="rpt_table" >
        <thead>
            <th width="30">SL</th>
            <th width="80">Job No</th>
            <th width="100">Style Ref</th>
			<th width="150">Order No</th>
            <th width="100">Trims Group</th>
            <th width="150">Description</th>
            <th width="100">Brand Sup.</th>
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
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1500" class="rpt_table" id="tbl_list_search" >
        <tbody>
        <?
        $item_group_id_str=implode(", ", $trim_group_arr);
        $trims_rate_sql=sql_select("SELECT a.supplier_id, a.item_group_id, a.rate, a.effective_from, b.brand_supplier, b.item_description from lib_supplier_wise_rate a  join (select item_details_id,supplier_id,max(effective_from) as effectivedate from lib_supplier_wise_rate where supplier_id=$supplier_id group by supplier_id,item_details_id) last_rate on last_rate.effectivedate = a.effective_from join product_details_master b on a.prod_id=b.id where a.is_deleted=0 and a.entry_form=482 and a.item_category_id=4 and a.item_group_id in ($item_group_id_str) and a.supplier_id=$supplier_id and b.entry_form=24 group by  a.supplier_id, a.item_group_id, a.rate, a.effective_from, b.brand_supplier, b.item_description");
        foreach ($trims_rate_sql as $row) {
            $cs_supplier_rate[$row[csf('supplier_id')]][$row[csf('item_group_id')]]['rate']=$row[csf('rate')];
            $cs_supplier_rate[$row[csf('supplier_id')]][$row[csf('item_group_id')]]['brand_supplier']=$row[csf('brand_supplier')];
            $cs_supplier_rate[$row[csf('supplier_id')]][$row[csf('item_group_id')]]['item_description']=$row[csf('item_description')];
        }
		unset($trims_rate_sql);
        /*echo '<pre>';
        print_r($cs_supplier_rate); die;*/
        $source_disabled="";
        if($cbo_level==1){
            foreach ($nameArray as $selectResult)
            {
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            
                $cbo_currency_job=$selectResult[csf('currency_id')];
                $exchange_rate=$selectResult[csf('exchange_rate')];
                if($cbo_currency == $cbo_currency_job){
                    $exchange_rate=1;
                }
            
                $req_qnty_cons_uom = $req_qty_arr[$selectResult[csf('po_id')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]];
                $req_amount_cons_uom = $req_amount_arr[$selectResult[csf('po_id')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]];
                $rate_cons_uom = $req_amount_cons_uom/$req_qnty_cons_uom;
            
                $req_qnty_ord_uom = def_number_format($req_qnty_cons_uom/$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor],5,"");
                
                if($source_from==4)
                {
                    $rate_ord_uom=$cs_supplier_rate[$supplier_id][$selectResult[csf('trim_group')]]['rate'];
                    $source_disabled="disabled";
                    if($rate_ord_uom=='')
                    {
                        $rate_ord_uom = def_number_format(($rate_cons_uom*$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor])*$exchange_rate,5,"");
                        $source_disabled="";
                    }
                }
                else{
                    $rate_ord_uom = def_number_format(($rate_cons_uom*$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor])*$exchange_rate,5,"");
                    $source_disabled="";
                }
                $req_amount_ord_uom = def_number_format($req_qnty_ord_uom*$rate_ord_uom,5,"");
            
                $cu_woq = $cu_booking_arr[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['cu_woq'][$selectResult[csf('po_id')]];
                $cu_amount = $cu_booking_arr[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['cu_amount'][$selectResult[csf('po_id')]];
                $bal_woq = def_number_format($req_qnty_ord_uom-$cu_woq,5,"");
                $amount = def_number_format($bal_woq*$rate_ord_uom,5,"");
                $reqAmtJobLevelConsUom=$reqAmountJobLevelArr[$selectResult[csf('job_no')]];
                ?>
                <tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i;?>" onClick="change_color('search<?=$i; ?>','<?=$bgcolor; ?>');">
                    <td width="30"><?=$i;?></td>
                    <td width="80"><?=$selectResult[csf('job_no')];?>
                        <input type="hidden" id="txtjob_<?=$i;?>" value="<?=$selectResult[csf('job_no')];?>" style="width:30px" class="text_boxes" readonly/>
                    </td>
					<td width="100">
                          <p>  <? echo $selectResult[csf('style_ref_no')];?> </p>
                        </td>
                    <td width="150"><?=$selectResult[csf('po_number')];?>
                        <input type="hidden" id="txtbookingid_<?=$i;?>" value="" readonly/>
                        <input type="hidden" id="txtpoid_<?=$i;?>" value="<?=$selectResult[csf('po_id')];?>" readonly/>
                        <input type="hidden" id="txtcountry_<?=$i;?>" value="<?=$selectResult[csf('country')] ?>" readonly />
                    </td>
                    <td width="100" title="<?=$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor]; ?>">
                        <?=$trim_group_library[$selectResult[csf('trim_group')]];?>
                        <input type="hidden" id="txttrimcostid_<?=$i;?>" value="<?=$selectResult[csf('wo_pre_cost_trim_cost_dtls')];?>" readonly/>
                        <input type="hidden" id="txttrimgroup_<?=$i;?>" value="<?=$selectResult[csf('trim_group')];?>" readonly/>
                        <input type="hidden" id="txtReqAmt_<?=$i;?>" value="<?=$selectResult[csf('amount')];?>"/>
                        <input id="hiddlabeldtlsdata_<?=$i;?>" type="hidden" value=""/>
                    </td>
                    <td width="150"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<?=$bgcolor; ?>" id="txtdesc_<?=$i;?>" value="<?=$selectResult[csf('description')];?>" <?=$source_disabled ?> /></td>
                    <td width="100"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<? echo $bgcolor; ?>" id="txtbrandsup_<?=$i;?>" value="<?=$selectResult[csf('brand_sup_ref')];?>" <?=$source_disabled ?> /></td>
                    <td width="70" align="right">
                        <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqqnty_<?=$i;?>" value="<?=number_format($req_qnty_ord_uom,4,'.','');?>"  readonly  />
                        <input type="hidden" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamount_<?=$i;?>" value="<?=number_format($req_amount_ord_uom,4,'.','');?>"  readonly  />
                        <input type="hidden" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamountjoblevelconsuom_<?=$i;?>" value="<?=number_format($reqAmtJobLevelConsUom,4,'.','');?>"  readonly  />
                        <input type="hidden" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamountitemlevelconsuom_<?=$i;?>" value="<?=number_format($req_amount_cons_uom,4,'.','');?>"  readonly  />
                    </td>
                    <td width="50"><?=$unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom]]; ?>
                        <input type="hidden" id="txtuom_<?=$i;?>" value="<?=$sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom];?>" readonly />
                    </td>
                    <td width="80" align="right">
                        <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<?=$bgcolor; ?>" id="txtcuwoq_<?=$i;?>" value="<?=number_format($selectResult[csf('cu_woq')],4,'.','');?>"  readonly  />
                        <input type="hidden" style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<?=$bgcolor; ?>" id="txtcuamount_<?=$i;?>" value="<?=number_format($selectResult[csf('cu_amount')],4,'.','');?>"  readonly  />
                    </td>
                    <td width="80" align="right"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<?=$bgcolor; ?>" id="txtbalwoq_<?=$i;?>" value="<?=number_format($bal_woq,4,'.',''); ?>" readonly />
                    </td>
                    <td width="100" align="right"><?=create_drop_down( "cbocolorsizesensitive_".$i, 100, $size_color_sensitive,"", 1, "--Select--", "", "set_cons_break_down($i),copy_value(this.value,'cbocolorsizesensitive_',$i)","","1,2,3,4" ); ?></td>
                    <td width="80" align="right"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:#FFC" id="txtwoq_<?=$i;?>" value="<?=number_format($bal_woq,4,'.',''); ?>" onClick="open_consumption_popup('requires/trims_booking_multi_job_controllerurmi.php?action=consumption_popup', 'Consumtion Entry Form','txtpoid_<?=$i;?>',<?=$i;?>);"readonly /></td>
                    <td width="55" align="right"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<?=$bgcolor; ?>" id="txtexchrate_<?=$i;?>" value="<?=$exchange_rate;?>" readonly /></td>
                    <td width="80" align="right">
                        <?
                        $ratetexcolor="#000000";
                        $decimal=explode(".",$rate_ord_uom);
                        if(strlen($decimal[1]>6)){
                            $ratetexcolor="#F00";
                        }
                        ?>
                        <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;   text-align:right; color:<?=$ratetexcolor; ?>; background-color:<?=$bgcolor; ?>" id="txtrate_<?=$i;?>" value="<?=$rate_ord_uom ;?>" onChange="calculate_amount(<?=$i; ?>);" readonly />
                        <input type="hidden" id="txtrate_precost_<?=$i;?>" value="<?=$rate_ord_uom;?>" readonly />
                    </td>
                    <td width="80" align="right"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<?=$bgcolor; ?>" id="txtamount_<?=$i;?>" value="<?=number_format($amount,4,'.','');?>" readonly /></td>
                    <td width="" align="right">
                        <input type="text" style="width:90%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<?=$bgcolor; ?>" id="txtddate_<?=$i;?>"  class="datepicker" value="<?=$txt_delivery_date; ?>"  readonly  />
                        <input type="hidden" id="consbreckdown_<?=$i;?>"  value=""/>
                        <input type="hidden" id="jsondata_<?=$i;?>"  value=""/>
                    </td>
                </tr>
                <?
                $i++;
            }
        }
    
        if($cbo_level==2)
        {
            $i=1;
            foreach ($job_and_trimgroup_level as $job_no)
            {
                foreach ($job_no as $wo_pre_cost_trim_cost_dtls)
                {
                    $job_no=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['job_no']));
					$style_ref_no=implode(", ",$wo_pre_cost_trim_cost_dtls['style_ref_no']);
                    $po_number=implode(",",$wo_pre_cost_trim_cost_dtls['po_number']);
                    $po_id=implode(",",$wo_pre_cost_trim_cost_dtls['po_id']);
                    $country=implode(",",array_unique(explode(",",implode(",",$wo_pre_cost_trim_cost_dtls['country']))));
                    $description=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['description']));
                    $brand_sup_ref=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['brand_sup_ref']));
                    $wo_pre_cost_trim_id=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['wo_pre_cost_trim_cost_dtls']));
                    $trim_group = implode(",",array_unique($wo_pre_cost_trim_cost_dtls['trim_group']));
                    $uom=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['uom']));
                     //$wo_pre_req_amt=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['pre_req_amt']));
                
                    $wo_pre_req_amt=array_sum($wo_pre_cost_trim_cost_dtls['pre_req_amt']);
                    $req_qnty_ord_uom=array_sum($wo_pre_cost_trim_cost_dtls['req_qnty']);
                    if($source_from==4)
                    {
                        $rate_ord_uom=$cs_supplier_rate[$supplier_id][$trim_group]['rate'];
                        $source_disabled="disabled";
                        if($rate_ord_uom=='')
                        {
                            $rate_ord_uom=array_sum($wo_pre_cost_trim_cost_dtls['req_amount'])/array_sum($wo_pre_cost_trim_cost_dtls['req_qnty']);
                            $source_disabled="";
                        }
                    }
                    else{
                        $rate_ord_uom=array_sum($wo_pre_cost_trim_cost_dtls['req_amount'])/array_sum($wo_pre_cost_trim_cost_dtls['req_qnty']);
                        $source_disabled="";
                    }
                    
                    $req_amount_ord_uom=array_sum($wo_pre_cost_trim_cost_dtls['req_amount']);
                    $req_amount_cons_uom=array_sum($wo_pre_cost_trim_cost_dtls['req_amount_cons_uom']);
                
                    $bal_woq=array_sum($wo_pre_cost_trim_cost_dtls['bal_woq']);
                    $amount=array_sum($wo_pre_cost_trim_cost_dtls['amount']);
                
                    $cu_woq=array_sum($wo_pre_cost_trim_cost_dtls['cu_woq']);
                    $cu_amount=array_sum($wo_pre_cost_trim_cost_dtls['cu_amount']);
                
                    $reqAmtJobLevelConsUom=$reqAmountJobLevelArr[$job_no];
                    ?>
                    <tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i;?>" onClick="change_color('search<?=$i; ?>','<?=$bgcolor; ?>');">
                        <td width="30"><?=$i;?></td>
                        <td width="80"><?=$job_no; ?>
                            <input type="hidden" id="txtjob_<?=$i;?>" value="<?=$job_no;?>" style="width:30px" class="text_boxes" readonly/>
                        </td>
						<td width="100">
                          <p>  <?=$style_ref_no;?></p>
                        </td>
                        <td width="150" style="word-wrap:break-word;word-break: break-all"><?=$po_number; ?>
                            <input type="hidden" id="txtbookingid_<?=$i;?>" value="" readonly/>
                            <input type="hidden" id="txtpoid_<?=$i;?>" value="<?=$po_id; ?>" readonly/>
                            <input type="hidden" id="txtcountry_<?=$i;?>" value="<?=$country; ?>" readonly />
                        </td>
                        <td width="100" title="<?=$sql_lib_item_group_array[$trim_group][conversion_factor]; ?>">
                            <a href="javascript:void(0)" onClick="openlabeldtls_popup('<?=$trim_group."__".$i; ?>');"><?=$trim_group_library[$trim_group]; ?></a>
                            <input type="hidden" id="txttrimcostid_<?=$i;?>" value="<?=$wo_pre_cost_trim_id;?>" readonly/>
                            <input type="hidden" id="txttrimgroup_<?=$i;?>" value="<?=$trim_group;?>" readonly/>
                            <input id="txtReqAmt_<?=$i;?>" type="hidden" value="<?=$wo_pre_req_amt; ?>"/>
                            <input id="hiddlabeldtlsdata_<?=$i;?>" type="hidden" value=""/>
                        </td>
                        <td width="150"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<?=$bgcolor; ?>" id="txtdesc_<?=$i;?>"  value="<?=$description; ?>" <?=$source_disabled; ?> /></td>
                        <td width="100"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<?=$bgcolor; ?>" id="txtbrandsup_<?=$i;?>" value="<?=$brand_sup_ref;?>" <?=$source_disabled ?> /></td>
                        <td width="70" align="right">
                            <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqqnty_<? echo $i;?>" value="<? echo number_format($req_qnty_ord_uom,4,'.','');?>"  readonly  />
                            <input type="hidden"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamount_<? echo $i;?>" value="<? echo number_format($req_amount_ord_uom,4,'.','');?>"  readonly  />
                            <input type="hidden"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamountjoblevelconsuom_<? echo $i;?>" value="<? echo number_format($reqAmtJobLevelConsUom,4,'.','');?>"  readonly  />
                            <input type="hidden"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamountitemlevelconsuom_<? echo $i;?>" value="<? echo number_format($req_amount_cons_uom,4,'.','');?>"  readonly  />
                        </td>
                        <td width="50"><?=$unit_of_measurement[$uom]; ?><input type="hidden" id="txtuom_<?=$i;?>" value="<?=$uom;?>" readonly /></td>
                        <td width="80" align="right">
                            <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuwoq_<?=$i;?>" value="<?=number_format($cu_woq,4,'.',''); ?>" readonly />
                            <input type="hidden" style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuamount_<?=$i;?>" value="<?=number_format($cu_amount,4,'.','');?>" readonly />
                        </td>
                        <td width="80" align="right"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtbalwoq_<?=$i;?>" value="<?=number_format($bal_woq,4,'.','');?>" readonly /></td>
                        <td width="100" align="right"><?=create_drop_down( "cbocolorsizesensitive_".$i, 100, $size_color_sensitive,"", 1, "--Select--", "", "set_cons_break_down($i),copy_value(this.value,'cbocolorsizesensitive_',$i)","","1,2,3,4" ); ?></td>
                        <td width="80" align="right"><input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:#FFC" id="txtwoq_<?=$i;?>" value="<?=number_format($bal_woq,4,'.','');?>" onClick="open_consumption_popup('requires/trims_booking_multi_job_controllerurmi.php?action=consumption_popup', 'Consumtion Entry Form','txtpoid_<?=$i;?>',<?=$i;?>);" readonly/></td>
                        <td width="55" align="right"><input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtexchrate_<?=$i;?>" value="<?=$exchange_rate;?>" readonly /></td>
                        <td width="80" align="right">
                            <?
                            $ratetexcolor="#000000";
                            $decimal=explode(".",$rate_ord_uom);
                        
                            if(strlen($decimal[1])>6){
                                $ratetexcolor="#F00";
                            }
                            //echo strlen($decimal[1]);
                            ?>
                            <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; color:<?=$ratetexcolor;  ?>;  background-color:<?=$bgcolor; ?>" id="txtrate_<?=$i;?>" value="<?=$rate_ord_uom;?>" onChange="calculate_amount(<?=$i; ?>);" readonly />
                            <input type="hidden"  id="txtrate_precost_<?=$i;?>" value="<?=$rate_ord_uom;?>" readonly />
                        </td>
                        <td width="80" align="right">
                            <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<?=$bgcolor; ?>" id="txtamount_<?=$i;?>" value="<?=number_format($amount,4,'.',''); ?>" readonly />
                        </td>
                        <td align="right">
                            <input type="text" style="width:90%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:left; background-color:<?=$bgcolor; ?>" id="txtddate_<?=$i;?>" class="datepicker" value="<?=$txt_delivery_date; ?>" readonly />
                            <input type="hidden" id="consbreckdown_<?=$i;?>" value=""/>
                            <input type="hidden" id="jsondata_<?=$i;?>" value=""/>
                        </td>
                    </tr>
                    <?
                    $i++;
                    $total_amount+=$amount;
                    $tot_bal_woq+=$bal_woq;
                }
            }
        }
        ?>
        </tbody>
	</table>
	<table width="1500" class="rpt_table" border="0" rules="all">
        <tfoot>
            <tr>
                <th width="30">&nbsp;</th>
                <th width="80">&nbsp;</th>
				<th width="100">&nbsp;</th>
                <th width="150">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="150">&nbsp;</th>
                <th width="100">Total :</th>
                <th width="70"><?=number_format($tot_req_qty,4,'.',''); ?></th>
                <th width="50">&nbsp;</th>
                <th width="80"><?=number_format($tot_cu_woq,4,'.',''); ?></th>
                <th width="80">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="80" id="tot_woqty"><?=number_format($tot_bal_woq,4,'.',''); ?></th>
                <th width="55">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80" id="totamount"><?=number_format($total_amount,4,'.',''); ?></th>
                <th>
                    <input type="hidden" id="saved_tot_amount" value="0" style="width:80px; text-align:right" readonly/>
                    <input type="hidden" id="tot_amount" value="<?=$total_amount; ?>" style="width:80px" readonly />
                </th>
            </tr>
        </tfoot>
	</table>
    <table width="1100" colspan="14" cellspacing="0" class="" border="0">
        <tr>
            <td align="center"class="button_container"><?=load_submit_buttons( $permission, "fnc_trims_booking_dtls", 0,0,"reset_form('','booking_list_view','','','')",2); ?></td>
        </tr>
    </table>
	<?
	exit();
}

if ($action=="show_trim_booking")
{
	extract($_REQUEST);
	$trim_group_library= return_library_array("select id, item_name from lib_item_group",'id','item_name');
	$booking_month=0;
	if($cbo_booking_month<10){
		$booking_month.=$cbo_booking_month;
	}
	else{
		$booking_month=$cbo_booking_month;
	}
	if($garments_nature==0) $garment_nature_cond="";
	else $garment_nature_cond=" and a.garments_nature=$garments_nature";

	$start_date=$cbo_booking_year."-".$booking_month."-01";
	$end_date=$cbo_booking_year."-".$booking_month."-".cal_days_in_month(CAL_GREGORIAN, $booking_month, $cbo_booking_year);
	if($db_type==0){
		if ($start_date!="" &&  $end_date!="") $shipment_date = "and d.pub_shipment_date between '".change_date_format($start_date, "yyyy-mm-dd", "-")."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}
	else if($db_type==2){
		if ($start_date!="" &&  $end_date!="") $shipment_date = "and d.pub_shipment_date between '".change_date_format($start_date, "yyyy-mm-dd", "-",1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}

	$sql_lib_item_group_array=array();
	$sql_lib_item_group=sql_select("select id, item_name,conversion_factor,order_uom as cons_uom from lib_item_group");
	foreach($sql_lib_item_group as $row_sql_lib_item_group){
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][item_name]=$row_sql_lib_item_group[csf('item_name')];
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][conversion_factor]=$row_sql_lib_item_group[csf('conversion_factor')];
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][cons_uom]=$row_sql_lib_item_group[csf('cons_uom')];
	}
	unset($sql_lib_item_group);
	$condition= new condition();
	if(str_replace("'","",$job_no) !=''){
		$condition->job_no("in('$job_no')");
	}
	$condition->init();
	$trims= new trims($condition);
	$req_qty_arr=$trims->getQtyArray_by_orderAndPrecostdtlsid();
	$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsid();
	/*$req_amount_arr=$trims->getAmountArray_by_jobAndPrecostdtlsid_consAndTotcons();
	$req_qty_arr=$trims->getQtyArray_by_jobAndPrecostdtlsid_consAndTotcons();*/
	$reqAmountJobLevelArr=$trims->getAmountArray_by_job();


	$cu_booking_arr=array();
	$sql_cu_booking=sql_select("SELECT c.job_no,c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id, sum(c.wo_qnty) as cu_wo_qnty, sum(c.amount) as cu_amount from wo_po_details_master a, wo_po_break_down  d , wo_booking_dtls c where a.job_no=d.job_no_mst and a.job_no=c.job_no and  d.id=c.po_break_down_id and a.company_name=$cbo_company_name and c.pre_cost_fabric_cost_dtls_id=$pre_cost_id and c.status_active=1 and c.is_deleted=0 group by c.job_no, c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id");
	foreach($sql_cu_booking as $row_cu_booking){
		$cu_booking_arr[$row_cu_booking[csf('job_no')]][$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]]['cu_woq'][$row_cu_booking[csf('po_break_down_id')]]=$row_cu_booking[csf('cu_wo_qnty')];
		$cu_booking_arr[$row_cu_booking[csf('job_no')]][$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]]['cu_amount'][$row_cu_booking[csf('po_break_down_id')]]=$row_cu_booking[csf('cu_amount')];
	}
	unset($sql_cu_booking);

	$sql="SELECT a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id as wo_pre_cost_trim_cost_dtls, c.trim_group, c.description as description_pre_cost, c.brand_sup_ref as brand_sup_ref_precost, c.country, c.rate,c.amount, d.id as po_id, d.po_number, d.po_quantity as plan_cut, min(e.id) as id, e.po_break_down_id, avg(e.cons) as cons, sum(f.wo_qnty) as cu_woq, sum(f.amount) as cu_amount, f.id as booking_id, f.sensitivity, f.delivery_date, f.description as description, f.brand_supplier as brand_sup_ref, f.labeldtlsdata from wo_po_details_master a, wo_pre_cost_mst b, wo_pre_cost_trim_cost_dtls c, wo_po_break_down d, wo_pre_cost_trim_co_cons_dtls e, wo_booking_dtls f where a.job_no=b.job_no and a.job_no=c.job_no and a.job_no=d.job_no_mst and a.job_no=e.job_no and a.job_no=f.job_no and c.id=e.wo_pre_cost_trim_cost_dtls_id and d.id=e.po_break_down_id and e.wo_pre_cost_trim_cost_dtls_id= f.pre_cost_fabric_cost_dtls_id and e.po_break_down_id=f.po_break_down_id and f.booking_type=2 and f.booking_no=$txt_booking_no and f.id in($booking_id) and a.company_name=$cbo_company_name $garment_nature_cond and e.wo_pre_cost_trim_cost_dtls_id=$pre_cost_id and d.is_deleted=0 and d.status_active=1 and f.status_active=1 and f.is_deleted=0 group by a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id, c.trim_group, c.description, c.brand_sup_ref, c.country, c.rate, d.id, d.po_number, d.po_quantity, e.po_break_down_id, f.id, f.sensitivity, f.delivery_date, f.description, f.brand_supplier, f.labeldtlsdata, c.amount order by d.id, c.id";

	$job_and_trimgroup_level=array();
	$i=1;
	$nameArray=sql_select( $sql );
	foreach ($nameArray as $infr)
	{
		$cbo_currency_job=$infr[csf('currency_id')];
		$exchange_rate=$infr[csf('exchange_rate')];
		if($cbo_currency==$cbo_currency_job) $exchange_rate=1;

		$pre_cost_trim_id=$infr[csf('wo_pre_cost_trim_cost_dtls')];

		$req_qnty_cons_uom=$req_qty_arr[$infr[csf('po_id')]][$pre_cost_trim_id];
		$req_amount_cons_uom=$req_amount_arr[$infr[csf('po_id')]][$pre_cost_trim_id];
		/*$req_qnty_cons_uom=$req_qty_arr[$infr[csf('job_no')]][$pre_cost_trim_id]['totcons'];
		$req_amount_cons_uom=$req_amount_arr[$infr[csf('job_no')]][$pre_cost_trim_id]['totcons'];*/
		$rate_cons_uom=$req_amount_cons_uom/$req_qnty_cons_uom;

		$req_qnty_ord_uom=def_number_format($req_qnty_cons_uom/$sql_lib_item_group_array[$infr[csf('trim_group')]][conversion_factor],5,"");
		$rate_ord_uom=def_number_format(($rate_cons_uom*$sql_lib_item_group_array[$infr[csf('trim_group')]][conversion_factor])*$exchange_rate,5,"");
		$req_amount_ord_uom=def_number_format($req_qnty_ord_uom*$rate_ord_uom,5,"");

		$cu_woq=$cu_booking_arr[$infr[csf('job_no')]][$pre_cost_trim_id]['cu_woq'][$infr[csf('po_id')]];
		$cu_amount=$cu_booking_arr[$infr[csf('job_no')]][$pre_cost_trim_id]['cu_amount'][$infr[csf('po_id')]];

		$bal_woq=def_number_format($req_qnty_ord_uom-$cu_woq,5,"");
		$amount=def_number_format($rate_ord_uom*$bal_woq,5,"");

		$total_req_amount+=$req_amount;
		$total_cu_amount+=$infr[csf('cu_amount')];

		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_trim_id]['job_no'][$infr[csf('po_id')]]=$infr[csf('job_no')];
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_trim_id]['po_id'][$infr[csf('po_id')]]=$infr[csf('po_id')];
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_trim_id]['po_number'][$infr[csf('po_id')]]=$infr[csf('po_number')];
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_trim_id]['country'][$infr[csf('po_id')]]=$infr[csf('country')];
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_trim_id]['style_ref_no'][$infr[csf('po_id')]]=$infr[csf('style_ref_no')];
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_trim_id]['description'][$infr[csf('po_id')]]=$infr[csf('description')];
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_trim_id]['brand_sup_ref'][$infr[csf('po_id')]]=$infr[csf('brand_sup_ref')];
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_trim_id]['trim_group'][$infr[csf('po_id')]]=$infr[csf('trim_group')];
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_trim_id]['trim_group_name'][$infr[csf('po_id')]]=$trim_group_library[$infr[csf('trim_group')]];
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_trim_id]['wo_pre_cost_trim_cost_dtls'][$infr[csf('po_id')]]=$pre_cost_trim_id;

		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_trim_id]['req_qnty'][$infr[csf('po_id')]]=$req_qnty_ord_uom;
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_trim_id]['uom'][$infr[csf('po_id')]]=$sql_lib_item_group_array[$infr[csf('trim_group')]][cons_uom];

		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_trim_id]['uom_name'][$infr[csf('po_id')]]=$unit_of_measurement[$sql_lib_item_group_array[$infr[csf('trim_group')]][cons_uom]];

		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_trim_id]['req_amount'][$infr[csf('po_id')]]=$req_amount_ord_uom;
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_trim_id]['req_amount_cons_uom'][$infr[csf('po_id')]]=$req_amount_cons_uom;

		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_trim_id]['cu_woq'][$infr[csf('po_id')]]=$cu_woq;
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_trim_id]['cu_amount'][$infr[csf('po_id')]]=$cu_amount;
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_trim_id]['bal_woq'][$infr[csf('po_id')]]=$bal_woq;
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_trim_id]['exchange_rate'][$infr[csf('po_id')]]=$exchange_rate;
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_trim_id]['rate'][$infr[csf('po_id')]]=$rate_ord_uom;
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_trim_id]['amount'][$infr[csf('po_id')]]=$amount;
		$job_and_trimgroup_level[$job_no][$pre_cost_trim_id]['pre_req_amt'][$infr[csf('po_id')]]=$infr[csf('amount')];
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_trim_id]['txt_delivery_date'][$infr[csf('po_id')]]=$infr[csf('delivery_date')];
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_trim_id]['booking_id'][$infr[csf('po_id')]]=$infr[csf('booking_id')];
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_trim_id]['sensitivity'][$infr[csf('po_id')]]=$infr[csf('sensitivity')];
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_trim_id]['labeldtlsdata'][$infr[csf('po_id')]]=$infr[csf('labeldtlsdata')];
		$trim_group_arr[$infr[csf('trim_group')]]=$infr[csf('trim_group')];
	}

	$sql_booking=sql_select("SELECT c.job_no, c.pre_cost_fabric_cost_dtls_id, c.po_break_down_id, sum(c.wo_qnty) as wo_qnty, sum(c.amount) as amount from wo_po_details_master a, wo_po_break_down  d, wo_booking_dtls c where a.job_no=d.job_no_mst and a.job_no=c.job_no and  d.id=c.po_break_down_id and c.booking_no=$txt_booking_no and c.pre_cost_fabric_cost_dtls_id=$pre_cost_id  and c.booking_type=2 and c.status_active=1 and c.is_deleted=0 group by c.job_no, c.pre_cost_fabric_cost_dtls_id, c.po_break_down_id"); //c.id in($booking_id) and
	foreach($sql_booking as $row_booking){
		$job_and_trimgroup_level[$row_booking[csf('job_no')]][$row_booking[csf('pre_cost_fabric_cost_dtls_id')]]['woq'][$row_booking[csf('po_break_down_id')]]=$row_booking[csf('wo_qnty')];
		$job_and_trimgroup_level[$row_booking[csf('job_no')]][$row_booking[csf('pre_cost_fabric_cost_dtls_id')]]['amount'][$row_booking[csf('po_break_down_id')]]=$row_booking[csf('amount')];
	}
	?>

    <input type="hidden" id="strdata" value='<? echo json_encode($job_and_trimgroup_level); ?>' style="background-color:#CCC"/>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1500" class="rpt_table" >
        <thead>
            <th width="40">SL</th>
            <th width="80">Job No</th>
			<th width="100">Style Ref</th>
            <th width="100">Ord. No</th>
            <th width="100">Trims Group</th>
            <th width="150">Description</th>
            <th width="150">Brand Sup.</th>
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
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1500" class="rpt_table" id="tbl_list_search" >
        <tbody>
        <?
	        $sql_vari_lib="select item_category_id,variable_list,excut_source  from variable_order_tracking where company_name=".$cbo_company_name." and item_category_id=4  and variable_list=72 and status_active=1";
		$result_vari_lib=sql_select($sql_vari_lib);
		$source_from=1;
		foreach($result_vari_lib as $row)
		{
			if($row[csf('excut_source')]>0)
			{
				$source_from=$row[csf('excut_source')];
			}
		}
		$item_group_id_str=implode(", ", $trim_group_arr);
		$trims_rate_sql=sql_select("SELECT a.supplier_id, a.item_group_id, a.rate, a.effective_from, b.brand_supplier, b.item_description from lib_supplier_wise_rate a  join (select item_details_id,supplier_id,max(effective_from) as effectivedate from lib_supplier_wise_rate where supplier_id=$supplier_id group by supplier_id,item_details_id) last_rate on last_rate.effectivedate = a.effective_from join product_details_master b on a.prod_id=b.id where a.is_deleted=0 and a.entry_form=482 and a.item_category_id=4 and a.item_group_id in ($item_group_id_str) and a.supplier_id=$supplier_id and b.entry_form=24 group by  a.supplier_id, a.item_group_id, a.rate, a.effective_from, b.brand_supplier, b.item_description");
		foreach ($trims_rate_sql as $row) {
			$cs_supplier_rate[$row[csf('supplier_id')]][$row[csf('item_group_id')]]['rate']=$row[csf('rate')];
			$cs_supplier_rate[$row[csf('supplier_id')]][$row[csf('item_group_id')]]['brand_supplier']=$row[csf('brand_supplier')];
			$cs_supplier_rate[$row[csf('supplier_id')]][$row[csf('item_group_id')]]['item_description']=$row[csf('item_description')];
		}
		/*echo '<pre>';
		print_r($cs_supplier_rate); die;*/
		$tot_req_qty=0;$total_amount=0;
        if($cbo_level==1)
        {
            foreach ($nameArray as $selectResult)
            {
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

                $cbo_currency_job=$selectResult[csf('currency_id')];
                $exchange_rate=$selectResult[csf('exchange_rate')];
                if($cbo_currency==$cbo_currency_job){
                    $exchange_rate=1;
                }

                $req_qnty_cons_uom=$req_qty_arr[$selectResult[csf('po_id')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]];
                $req_amount_cons_uom=$req_amount_arr[$selectResult[csf('po_id')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]];
                $rate_cons_uom=$req_amount_cons_uom/$req_qnty_cons_uom;

                $req_qnty_ord_uom=def_number_format($req_qnty_cons_uom/$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor],5,"");
                $rate_ord_uom=def_number_format(($rate_cons_uom*$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor])*$exchange_rate,5,"");
                $req_amount_ord_uom=def_number_format($req_qnty_ord_uom*$rate_ord_uom,5,"");

                $cu_woq=$cu_booking_arr[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['cu_woq'][$selectResult[csf('po_id')]];
                $cu_amount=$cu_booking_arr[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['cu_amount'][$selectResult[csf('po_id')]];

                $bal_woq=def_number_format($req_qnty_ord_uom-$cu_woq,5,"");

                $woq=$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['woq'][$selectResult[csf('po_id')]];
                $amount=$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['amount'][$selectResult[csf('po_id')]];
                $rate=$amount/$woq;
                $total_amount+=$amount;
				$tot_req_qty+=$woq;
                $reqAmtJobLevelConsUom=$reqAmountJobLevelArr[$selectResult[csf('job_no')]];

				$piNumber=0;
				$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no and b.item_group='".$trim_group."' and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
				if($pi_number) $piNumber=1;
				
				$recvNumber=0;
				$recv_number=return_field_value( "recv_number", "inv_receive_master a,inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and b.item_group_id='".$trim_group."' and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
				if($recv_number) $recvNumber=1;
				
				//$disAbled=1;
				$disAbled=0;					
				if($source_from==4)
				{
					$rate_ord_uom=$cs_supplier_rate[$supplier_id][$trim_group]['rate'];
					$disAbled=1;
					if($rate_ord_uom=='') $disAbled=0;
				}
				else $disAbled=0;
				
				if($disAbled==0){
					if($recvNumber==1 || $piNumber==1) $disAbled=1; else $disAbled=0;
				}
                ?>
                <tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i;?>">
                    <td width="40"><?=$i;?></td>
                    <td width="80"><?=$selectResult[csf('job_no')];?>
                        <input type="hidden" id="txtjob_<?=$i;?>" value="<?=$selectResult[csf('job_no')];?>" style="width:30px" class="text_boxes" readonly/>
                    </td>
					<td width="100"><?=$selectResult[csf('style_ref_no')];?>
                    <td width="100"><?=$selectResult[csf('po_number')];?>
                        <input type="hidden" id="txtbookingid_<?=$i;?>" value="<?=$selectResult[csf('booking_id')];?>" readonly/>
                        <input type="hidden" id="txtpoid_<?=$i;?>" value="<?=$selectResult[csf('po_id')];?>" readonly/>
                        <input type="hidden" id="txtcountry_<?=$i;?>" value="<?=$selectResult[csf('country')] ?>" readonly />
                    </td>
                    <td width="100" title="<?=$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor];  ?>">
                        <?=$trim_group_library[$selectResult[csf('trim_group')]];?>
                        <input type="hidden" id="txttrimcostid_<?=$i;?>" value="<?=$selectResult[csf('wo_pre_cost_trim_cost_dtls')];?>" readonly/>
                        <input type="hidden" id="txttrimgroup_<?=$i;?>" value="<?=$selectResult[csf('trim_group')];?>" readonly/>
                        <input class="text_boxes" name="txtReqAmt_<?=$i;?>" id="txtReqAmt_<?=$i;?>" type="hidden" value="<?=$selectResult[csf('amount')]; ?>"/>
                        <input id="hiddlabeldtlsdata_<?=$i;?>" type="hidden" value=""/>
                    </td>
                    <td width="150">
                        <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<?=$bgcolor; ?>" id="txtdesc_<?=$i;?>" value="<?=$selectResult[csf('description')];?>" <? if($disAbled){echo "disabled";}else{ echo "";}?> />
                    </td>
                    <td width="150">
                        <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<? echo $bgcolor; ?>" id="txtbrandsup_<? echo $i;?>"  value="<? echo $selectResult[csf('brand_sup_ref')];?>"  <? if($disAbled){echo "disabled";}else{ echo "";}?> />
                    </td>
                    <td width="70" align="right">
                        <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqqnty_<? echo $i;?>" value="<? echo number_format($req_qnty_ord_uom,4,'.','');?>"  readonly  />
                        <input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamount_<? echo $i;?>" value="<? echo number_format($req_amount_ord_uom,4,'.','');?>"  readonly  />
                        <input type="hidden"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamountjoblevelconsuom_<? echo $i;?>" value="<? echo number_format($reqAmtJobLevelConsUom,4,'.','');?>"  readonly  />
                        <input type="hidden"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamountitemlevelconsuom_<? echo $i;?>" value="<? echo number_format($req_amount_cons_uom,4,'.','');?>"  readonly  />
                    </td>
                    <td width="50">
                        <? echo $unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom]];?>
                        <input type="hidden" id="txtuom_<? echo $i;?>" value="<? echo $sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom];?>" readonly />
                    </td>
                    <td width="80" align="right">
                        <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuwoq_<? echo $i;?>" value="<? echo number_format($cu_woq,4,'.','');?>"  readonly  />
                        <input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuamount_<? echo $i;?>" value="<? echo number_format($cu_amount,4,'.','');?>"  readonly  />
                    </td>
                    <td width="80" align="right">
                        <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtbalwoq_<? echo $i;?>" value="<? echo number_format($bal_woq,4,'.',''); ?>"  readonly  />
                    </td>
                    <td width="100" align="right"><? echo create_drop_down( "cbocolorsizesensitive_".$i, 100, $size_color_sensitive,"", 1, "--Select--", $selectResult[csf("sensitivity")], "set_cons_break_down($i),copy_value(this.value,'cbocolorsizesensitive_',$i)",$disAbled,"1,2,3,4" ); ?>
                    </td>
                    <td width="80" align="right">
                        <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoq_<? echo $i;?>" value="<? echo number_format($woq,4,'.','');?>" onClick="open_consumption_popup('requires/trims_booking_multi_job_controllerurmi.php?action=consumption_popup', 'Consumtion Entry Form','txtpoid_<? echo $i;?>',<? echo $i;?>)" readonly />
                    </td>
                    <td width="55" align="right">
                        <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtexchrate_<? echo $i;?>" value="<? echo $exchange_rate;?>" readonly />
                    </td>
                    <td width="80" align="right">
                        <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_<? echo $i;?>" value="<? echo number_format($rate,4,'.','');?>" onChange="calculate_amount(<? echo $i; ?>)" readonly />

                        <input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_precost_<? echo $i;?>" value="<? echo $rate_ord_uom;?>" readonly />
                    </td>
                    <td width="80" align="right">
                        <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtamount_<? echo $i;?>" value="<? echo number_format($amount,4,'.','');?>"  readonly  />
                    </td>
                    <td width="" align="right">
                        <input type="text"   style="width:90%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtddate_<? echo $i;?>"  class="datepicker" value="<? echo change_date_format($selectResult[csf('delivery_date')],"dd-mm-yyyy","-"); ?>"  readonly <? if($disAbled){echo "disabled";}else{ echo "";}?>  />
                        <input type="hidden" id="consbreckdown_<? echo $i;?>"  value=""/>
                        <input type="hidden" id="jsondata_<? echo $i;?>"  value=""/>
                    </td>
                </tr>
            <?
            $i++;
			
            }
        }
        if($cbo_level==2)
        {
            $i=1;
            foreach ($job_and_trimgroup_level as $job_no)
            {
                foreach ($job_no as $wo_pre_cost_trim_cost_dtls)
                {
                    $job_no=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['job_no']));
					$style_ref_no=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['style_ref_no']));
                    $po_number=implode(",",$wo_pre_cost_trim_cost_dtls['po_number']);
                    $po_id=implode(",",$wo_pre_cost_trim_cost_dtls['po_id']);
                    $country=implode(",",array_unique(explode(",",implode(",",$wo_pre_cost_trim_cost_dtls['country']))));
                    $description=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['description']));
                    $brand_sup_ref=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['brand_sup_ref']));
                    $wo_pre_cost_trim_id=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['wo_pre_cost_trim_cost_dtls']));
                    $wo_pre_req_amt=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['pre_req_amt']));
                    $trim_group = implode(",",array_unique($wo_pre_cost_trim_cost_dtls['trim_group']));
                    $uom=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['uom']));
                    $booking_id=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['booking_id']));
                    $sensitivity=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['sensitivity']));
                    $delivery_date=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['txt_delivery_date']));

                    $req_qnty_ord_uom=array_sum($wo_pre_cost_trim_cost_dtls['req_qnty']);
                    $rate_ord_uom=array_sum($wo_pre_cost_trim_cost_dtls['req_amount'])/array_sum($wo_pre_cost_trim_cost_dtls['req_qnty']);
                    $req_amount_ord_uom=array_sum($wo_pre_cost_trim_cost_dtls['req_amount']);
                    $req_amount_cons_uom=array_sum($wo_pre_cost_trim_cost_dtls['req_amount_cons_uom']);


                    $bal_woq=array_sum($wo_pre_cost_trim_cost_dtls['bal_woq']);
                    $cu_woq=array_sum($wo_pre_cost_trim_cost_dtls['cu_woq']);
                    $cu_amount=array_sum($wo_pre_cost_trim_cost_dtls['cu_amount']);

                    $woq=array_sum($wo_pre_cost_trim_cost_dtls['woq']);
                    $amount=array_sum($wo_pre_cost_trim_cost_dtls['amount']);
                    $rate=$amount/$woq;
                    $total_amount+=$amount;
					$tot_req_qty+=$woq;
                    $reqAmtJobLevelConsUom=$reqAmountJobLevelArr[$job_no];
					

					$piNumber=0;
					$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no and b.item_group='".$trim_group."' and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
					if($pi_number) $piNumber=1;
					
					$recvNumber=0;
					$recv_number=return_field_value( "recv_number", "inv_receive_master a,inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and b.item_group_id='".$trim_group."' and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
					if($recv_number) $recvNumber=1;
					
					$disAbled=0;					
					if($source_from==4)
					{
						$rate_ord_uom=$cs_supplier_rate[$supplier_id][$trim_group]['rate'];
						$disAbled=1;
						if($rate_ord_uom=='')
						{
							$disAbled=0;
						}
					}
					else{
						$disAbled=0;
					}
					if($disAbled==0)
					{
						if($recvNumber==1 || $piNumber==1) $disAbled=1; else $disAbled=0;
					}
					
                    ?>
                    <tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i;?>">
                        <td width="40"><?=$i;?></td>
                        <td width="80"><?=$job_no?><input type="hidden" id="txtjob_<?=$i;?>" value="<?=$job_no;?>" style="width:30px" class="text_boxes" readonly/></td>
						<td width="100"> <p><? echo $style_ref_no;?></p></td>
                        <td width="100" style="word-wrap:break-word;word-break: break-all"><?=$po_number; ?>
                            <input type="hidden" id="txtbookingid_<?=$i;?>" value="<?=$booking_id; ?>" readonly/>
                            <input type="hidden" id="txtpoid_<?=$i;?>" value="<?=$po_id; ?>" readonly/>
                            <input type="hidden" id="txtcountry_<?=$i;?>"  value="<?=$country; ?>" readonly />
                        </td>
                        <td width="100"  title="<?=$sql_lib_item_group_array[$trim_group][conversion_factor];  ?>">
                            <a href="javascript:void(0)" onClick="openlabeldtls_popup('<?=$trim_group."__".$i; ?>');"><?=$trim_group_library[$trim_group]; ?></a>
                            <input type="hidden" id="txttrimcostid_<?=$i;?>" value="<?=$wo_pre_cost_trim_id;?>" readonly/>
                            <input type="hidden" id="txttrimgroup_<?=$i;?>" value="<?=$trim_group;?>" readonly/>
                            <input type="hidden" name="txtReqAmt_<?=$i;?>" id="txtReqAmt_<?=$i;?>"  value="<?=$wo_pre_req_amt; ?>"/>
                            <input id="hiddlabeldtlsdata_<?=$i;?>" type="hidden" value=""/>
                        </td>
                        <td width="150">
                            <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<?=$bgcolor; ?>" id="txtdesc_<?=$i;?>"  value="<?=$description; ?>" <? if($disAbled){echo "disabled";}else{ echo "";}?> />
                        </td>
                        <td width="150">
                            <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<?=$bgcolor; ?>" id="txtbrandsup_<?=$i;?>"  value="<?=$brand_sup_ref;?>" <? if($disAbled){echo "disabled";}else{ echo "";}?>  />
                        </td>
                        <td width="70" align="right">
                            <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtreqqnty_<?=$i;?>" value="<?=number_format($req_qnty_ord_uom,4,'.','');?>"  readonly  />
                            <input type="hidden" id="txtreqamount_<?=$i;?>" value="<?=number_format($req_amount_ord_uom,4,'.','');?>"  readonly  />
                            <input type="hidden" id="txtreqamountjoblevelconsuom_<?=$i;?>" value="<?=number_format($reqAmtJobLevelConsUom,4,'.','');?>"  readonly  />
                            <input type="hidden" id="txtreqamountitemlevelconsuom_<?=$i;?>" value="<?=number_format($req_amount_cons_uom,4,'.','');?>"  readonly  />
                        </td>
                        <td width="50"><?=$unit_of_measurement[$uom];?><input type="hidden" id="txtuom_<?=$i;?>" value="<?=$uom;?>" readonly /></td>
                        <td width="80" align="right">
                            <input type="text" style="width:100%; height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<?=$bgcolor; ?>" id="txtcuwoq_<?=$i;?>" value="<?=number_format($cu_woq,4,'.',''); ?>"  readonly  />
                            <input type="hidden" id="txtcuamount_<?=$i;?>" value="<?=$cu_amount;?>"  readonly  />
                        </td>
                        <td width="80" align="right">
                            <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<?=$bgcolor; ?>" id="txtbalwoq_<?=$i;?>" value="<?=number_format($bal_woq,4,'.','');?>"  readonly  />
                        </td>
                        <td width="100" align="right"><?  echo create_drop_down( "cbocolorsizesensitive_".$i, 100, $size_color_sensitive,"", 1, "--Select--", $sensitivity, "set_cons_break_down($i),copy_value(this.value,'cbocolorsizesensitive_',$i)",$disAbled,"1,2,3,4" ); ?>
                        </td>
                        <td width="80" align="right">
                            <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoq_<?=$i;?>" value="<?=number_format($woq,4,'.','');?>" onClick="open_consumption_popup('requires/trims_booking_multi_job_controllerurmi.php?action=consumption_popup', 'Consumtion Entry Form','txtpoid_<?=$i;?>',<?=$i;?>)" readonly />
                        </td>
                        <td width="55" align="right">
                            <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<?=$bgcolor; ?>" id="txtexchrate_<?=$i;?>" value="<?=$exchange_rate;?>" readonly />
                        </td>
                        <td width="80" align="right">
                            <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<?=$bgcolor; ?>" id="txtrate_<?=$i;?>" value="<?=number_format($rate,4,'.','');?>" onChange="calculate_amount(<?=$i; ?>)" readonly />
                            <input type="hidden" style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<?=$bgcolor; ?>" id="txtrate_precost_<?=$i;?>" value="<?=$rate_ord_uom;?>" readonly />
                        </td>
                        <td width="80" align="right">
                            <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<?=$bgcolor; ?>" id="txtamount_<?=$i;?>" value="<?=number_format($amount,4,'.','');?>"  readonly  />
                        </td>
                        <td align="right">
                            <input type="text" style="width:90%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<?=$bgcolor; ?>" id="txtddate_<?=$i;?>"  class="datepicker" value="<?=change_date_format($delivery_date,"dd-mm-yyyy","-"); ?>"  readonly <? if($disAbled){echo "disabled";}else{ echo "";}?>   />
                            <input type="hidden" id="consbreckdown_<?=$i;?>"  value=""/>
                            <input type="hidden" id="jsondata_<?=$i;?>"  value=""/>
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
	<table width="1500" class="rpt_table" border="0" rules="all">
        <tfoot>
            <tr>
                <th width="40">&nbsp;</th>
                <th width="80">&nbsp;</th>
				<th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="150">&nbsp;</th>
                <th width="150">&nbsp;</th>
                <th width="70"><? //echo $tot_req_qty; ?></th>
                <th width="50">&nbsp;</th>
                <th width="80"><? echo $tot_cu_woq; ?></th>
                <th width="80"><? echo $tot_bal_woq; ?></th>
                <th width="100">Total </th>
                <th width="80" id="tot_woqty"><? echo number_format($tot_req_qty,4,'.',''); ?></th>
                <th width="55">&nbsp;</th>
                <th width="80"><input type="hidden" id="tot_amount" value="<? echo  number_format($total_amount,4,'.',''); ?>" class="text_boxes_numeric" style="width:140px"/></th>
                <th width="80"  id="totamount"><input type="hidden" id="tot_amount" value="<? echo  number_format($total_amount,4,'.',''); ?>" class="text_boxes_numeric" style="width:140px"/><? echo  number_format($total_amount,4,'.',''); ?></th>
               
                <th><input type="hidden" id="saved_tot_amount" value="0" style="width:80px; text-align:right" readonly/></th>
            </tr>
        </tfoot>
	</table>
    <table width="1100" colspan="14" cellspacing="0" class="" border="0">
        <tr>
            <td align="center" class="button_container">
            	<? echo load_submit_buttons( $permission, "fnc_trims_booking_dtls", 1,0,"reset_form('','booking_list_view','','','')",2); ?>
            </td>
        </tr>
    </table>
	<?
	exit();
}

if ($action=="show_trim_booking_list-bkup"){
	extract($_REQUEST);
	$trim_group_library= return_library_array("select id, item_name from lib_item_group",'id','item_name');
	$booking_month=0;
	if($cbo_booking_month<10){
		$booking_month.=$cbo_booking_month;
	}
	else{
		$booking_month=$cbo_booking_month;
	}
	if($garments_nature==0){
		$garment_nature_cond="";
	}
	else{
		$garment_nature_cond=" and a.garments_nature=$garments_nature";
	}

	$start_date=$cbo_booking_year."-".$booking_month."-01";
	$end_date=$cbo_booking_year."-".$booking_month."-".cal_days_in_month(CAL_GREGORIAN, $booking_month, $cbo_booking_year);
	if($db_type==0){
		if ($start_date!="" &&  $end_date!="") $shipment_date = "and d.pub_shipment_date between '".change_date_format($start_date, "yyyy-mm-dd", "-")."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}

	if($db_type==2){
		if ($start_date!="" &&  $end_date!="") $shipment_date = "and d.pub_shipment_date between '".change_date_format($start_date, "yyyy-mm-dd", "-",1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}

	$sql_lib_item_group_array=array();
	$sql_lib_item_group=sql_select("select id, item_name,conversion_factor,order_uom as cons_uom from lib_item_group");
	foreach($sql_lib_item_group as $row_sql_lib_item_group){
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][item_name]=$row_sql_lib_item_group[csf('item_name')];
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][conversion_factor]=$row_sql_lib_item_group[csf('conversion_factor')];
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][cons_uom]=$row_sql_lib_item_group[csf('cons_uom')];
	}

	$sql="SELECT a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id as wo_pre_cost_trim_cost_dtls, c.trim_group, c.description as description_pre_cost, c.brand_sup_ref as brand_sup_ref_precost, c.country, c.rate, d.id as po_id, d.po_number, d.po_quantity as plan_cut, min(e.id) as id, e.po_break_down_id, avg(e.cons) as cons, sum(f.wo_qnty) as cu_woq, sum(f.amount) as cu_amount, f.id as booking_id, f.sensitivity, f.delivery_date, f.description as description, f.brand_supplier as brand_sup_ref from wo_po_details_master a, wo_pre_cost_mst b, wo_pre_cost_trim_cost_dtls c, wo_po_break_down d, wo_pre_cost_trim_co_cons_dtls e, wo_booking_dtls f where a.job_no=b.job_no and a.job_no=c.job_no and a.job_no=d.job_no_mst and a.job_no=e.job_no and a.job_no=f.job_no and c.id=e.wo_pre_cost_trim_cost_dtls_id and d.id=e.po_break_down_id and e.wo_pre_cost_trim_cost_dtls_id= f.pre_cost_fabric_cost_dtls_id and e.po_break_down_id=f.po_break_down_id and f.booking_type=2 and f.booking_no=$txt_booking_no and a.company_name=$cbo_company_name   $garment_nature_cond and d.is_deleted=0 and d.status_active=1 and f.status_active=1 and f.is_deleted=0 group by a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id, c.trim_group, c.description, c.brand_sup_ref, c.country, c.rate, d.id, d.po_number, d.po_quantity, e.po_break_down_id, f.id, f.sensitivity, f.delivery_date, f.description, f.brand_supplier order by d.id, c.id";
	//echo $sql; die;

	$job_and_trimgroup_level=array();
	$i=1;
	$nameArray=sql_select( $sql );
	$job_and_trimgroup_level_attr = array('job_no','po_id','po_number','country','description','brand_sup_ref','trim_group','wo_pre_cost_trim_cost_dtls','booking_id','sensitivity','delivery_date');
	foreach ($nameArray as $selectResult){
	$cbo_currency_job=$selectResult[csf('currency_id')];
	$exchange_rate=$selectResult[csf('exchange_rate')];
	if($cbo_currency==$cbo_currency_job){
		$exchange_rate=1;
	}
	$brand_sup_ref = str_replace(' ', '', $selectResult[csf('brand_sup_ref')]);
	$description = str_replace(' ', '', $selectResult[csf('description')]);
	foreach ($job_and_trimgroup_level_attr as $attr) {
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$description][$brand_sup_ref][$selectResult[csf('po_id')]][$attr]=$selectResult[csf($attr)];
	}

	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$description][$brand_sup_ref][$selectResult[csf('po_id')]]['trim_group_name']=$trim_group_library[$selectResult[csf('trim_group')]];

	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$description][$brand_sup_ref][$selectResult[csf('po_id')]]['uom']=$sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom];

	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$description][$brand_sup_ref][$selectResult[csf('po_id')]]['uom_name']=$unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom]];
	}


	$sql_booking=sql_select("select c.job_no,c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id,c.sensitivity, c.description,c.brand_supplier,c.wo_qnty as wo_qnty, c.amount as amount from wo_po_details_master a, wo_po_break_down  d , wo_booking_dtls c where a.job_no=d.job_no_mst and a.job_no=c.job_no and  d.id=c.po_break_down_id and c.booking_no=$txt_booking_no and c.booking_type=2 and c.status_active=1 and c.is_deleted=0");
	foreach($sql_booking as $row_booking){
		$brand_sup_ref = str_replace(' ', '', $row_booking[csf('brand_supplier')]);
		$description = str_replace(' ', '', $row_booking[csf('description')]);
		$job_and_trimgroup_level[$row_booking[csf('job_no')]][$row_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_booking[csf('sensitivity')]][$description][$brand_sup_ref][$row_booking[csf('po_break_down_id')]]['woq']+=$row_booking[csf('wo_qnty')];
		$job_and_trimgroup_level[$row_booking[csf('job_no')]][$row_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_booking[csf('sensitivity')]][$description][$brand_sup_ref][$row_booking[csf('po_break_down_id')]]['amount']+=$row_booking[csf('amount')];
		$job_and_trimgroup_level[$row_booking[csf('job_no')]][$row_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_booking[csf('sensitivity')]][$description][$brand_sup_ref][$row_booking[csf('po_break_down_id')]]['description']= "'".$row_booking[csf('description')]."'";
		$job_and_trimgroup_level[$row_booking[csf('job_no')]][$row_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_booking[csf('sensitivity')]][$description][$brand_sup_ref][$row_booking[csf('po_break_down_id')]]['brand_sup_ref']= "'".$row_booking[csf('brand_supplier')]."'";
	}

	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1250" class="rpt_table">
	<thead>
	<th width="40">SL</th>
	<th width="100">Job No</th>
	<th width="100">Ord. No</th>
	<th width="100">Trims Group</th>
    <th width="150">Description</th>
    <th width="150">Brand Sup.</th>
	<th width="80">UOM</th>
	<th width="100">Sensitivity</th>
	<th width="80">WOQ</th>
	<th width="80">Exch.Rate</th>
	<th width="80">Rate</th>
	<th width="80">Amount</th>
	<th width="">Delv. Date</th>
	</thead>
	<tbody id="save_list">
	<?
	$total_woq = 0;
	$total_amount = 0;
	if($cbo_level==1){
		foreach ($nameArray as $selectResult){
		if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

		$cbo_currency_job=$selectResult[csf('currency_id')];
		$exchange_rate=$selectResult[csf('exchange_rate')];
		if($cbo_currency==$cbo_currency_job){
			$exchange_rate=1;
		}
		$brand_sup_ref = str_replace(' ', '', $selectResult[csf('brand_sup_ref')]);
		$description = str_replace(' ', '', $selectResult[csf('description')]);
		$woq=def_number_format($job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$description][$brand_sup_ref][$selectResult[csf('po_id')]]['woq'],5,"");
		$amount=def_number_format($job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$description][$brand_sup_ref][$selectResult[csf('po_id')]]['amount'],5,"");
		$rate=def_number_format($amount/$woq,5,"");
		//$total_amount+=$amount;

		?>
		<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="fnc_show_booking(<? echo $selectResult[csf('wo_pre_cost_trim_cost_dtls')];?>,'<? echo $selectResult[csf('po_id')]; ?>','<? echo $selectResult[csf('booking_id')];?>','<? echo $selectResult[csf('job_no')];?>')">
		<td width="40"><? echo $i;?></td>
		<td width="100">
		<? echo $selectResult[csf('job_no')];?>
		</td>
		<td width="100">
		<? echo $selectResult[csf('po_number')];?>
		</td>
		<td width="100"  title="<? echo $sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor];  ?>">
		<? echo $trim_group_library[$selectResult[csf('trim_group')]];?>
		</td>
		<td width="150" >
		<? echo $selectResult[csf('description')];?>
		</td>
	    <td width="150" >
		<? echo $selectResult[csf('brand_sup_ref')];?>
		</td>
		<td width="80">
		<? echo $unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom]];?>
		</td>
		<td width="100" align="right">
	    <? echo $size_color_sensitive[$selectResult[csf("sensitivity")]];?>
		</td>
		<td width="80" align="right">
		<? echo number_format($woq,4,'.',''); $total_woq+=$woq;?>
		</td>
		<td width="80" align="right">
	    <? echo $exchange_rate;?>
		</td>
		<td width="80" align="right">
	    <? echo number_format($rate,4,'.','');?>
		</td>
		<td width="80" align="right">
	    <? echo number_format($amount,4,'.',''); $total_amount += $amount;?>
		</td>
		<td width="" align="right">
	    <? echo change_date_format($selectResult[csf('delivery_date')],"dd-mm-yyyy","-"); ?>
		</td>
		</tr>
		<?
		$i++;
		}
	}
	if($cbo_level==2){
		$i=1;
		foreach ($job_and_trimgroup_level as $wo_pre_cost_trim_cost_dtls){
			foreach ($wo_pre_cost_trim_cost_dtls as $sen){
				foreach ($sen as $desc){
					foreach ($desc as $brandsup){
						foreach ($brandsup as $po_id){
							echo '<pre>';
							print_r($po_id); die;
							$job_no=implode(",",array_unique($po_id['job_no']));
							$po_number=implode(",",$po_id['po_number']);
							$po_id=implode(",",$po_id['po_id']);
							$country=implode(",",array_unique(explode(",",implode(",",$po_id['country']))));
							$description=implode(",",array_unique($po_id['description']));
							$brand_sup_ref=implode(",",array_unique($po_id['brand_sup_ref']));
							$wo_pre_cost_trim_id=implode(",",array_unique($po_id['wo_pre_cost_trim_cost_dtls']));
							$trim_group = implode(",",array_unique($po_id['trim_group']));
							$uom=implode(",",array_unique($po_id['uom']));
							$booking_id=implode(",",array_unique($po_id['booking_id']));
							$sensitivity=implode(",",array_unique($po_id['sensitivity']));
							$delivery_date=implode(",",array_unique($po_id['txt_delivery_date']));
							$woq=def_number_format(array_sum($po_id['woq']),5,"");
							$amount=def_number_format(array_sum($po_id['amount']),5,"");
							$rate=def_number_format($amount/$woq,5,"");
							//$total_amount+=$amount;
							echo $job_no;
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="fnc_show_booking(<? echo $wo_pre_cost_trim_id;?>,'<? echo $po_id; ?>','<? echo $booking_id; ?>','<? echo $job_no; ?>')">
							<td width="40"><? echo $i;?></td>
							<td width="100">
							<? echo $job_no?>
							</td>
							<td width="100" style="word-wrap:break-word;word-break: break-all">
							<? echo $po_number; ?>
							</td>
							<td width="100"  title="<? echo $sql_lib_item_group_array[$trim_group][conversion_factor];  ?>">
							<? echo $trim_group_library[$trim_group];?>
							</td>
							<td width="150" >
							<? echo $description;?>
							</td>
						    <td width="150" >
							<? echo $brand_sup_ref;?>
							</td>
							<td width="80">
							<?  echo $unit_of_measurement[$uom];?>
							</td>
							<td width="100" align="right">
						    <? echo $size_color_sensitive[$sensitivity];?>
							</td>
							<td width="80" align="right">
							<? echo number_format($woq,4,'.',''); $total_woq +=$woq;?>
							</td>
							<td width="80" align="right">
						    <? echo $exchange_rate;?>
							</td>
							<td width="80" align="right">
						    <? echo number_format($rate,4,'.','');?>
							</td>
							<td width="80" align="right">
						    <? echo number_format($amount,4,'.',''); $total_amount +=$amount; ?>
							</td>
							<td width="" align="right">
						    <? echo change_date_format($delivery_date,"dd-mm-yyyy","-"); ?>
							</td>
							</tr>
							<?
							$i++;
						}
					}
				}
			}
		}
	}
	?>
	<tr>
		<td colspan="8" align="right"><strong>Total WOQ</strong></td>
		<td align="right"><? echo number_format($total_woq,2,'.',''); ?></td>
		<td colspan="2" align="right"><strong>Total Amount</strong></td>
		<td align="right"><? echo number_format($total_amount,2,'.',''); ?></td>
		<td>&nbsp;</td>
	</tr>
	</tbody>
	</table>
	<?
	exit();
}

if ($action=="show_trim_booking_list"){
	extract($_REQUEST);
	$trim_group_library= return_library_array("select id, item_name from lib_item_group",'id','item_name');
	$booking_month=0;
	if($cbo_booking_month<10){
		$booking_month.=$cbo_booking_month;
	}
	else{
		$booking_month=$cbo_booking_month;
	}
	if($garments_nature==0){
		$garment_nature_cond="";
	}
	else{
		$garment_nature_cond=" and a.garments_nature=$garments_nature";
	}

	$start_date=$cbo_booking_year."-".$booking_month."-01";
	$end_date=$cbo_booking_year."-".$booking_month."-".cal_days_in_month(CAL_GREGORIAN, $booking_month, $cbo_booking_year);
	if($db_type==0){
		if ($start_date!="" &&  $end_date!="") $shipment_date = "and d.pub_shipment_date between '".change_date_format($start_date, "yyyy-mm-dd", "-")."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}

	if($db_type==2){
		if ($start_date!="" &&  $end_date!="") $shipment_date = "and d.pub_shipment_date between '".change_date_format($start_date, "yyyy-mm-dd", "-",1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}

	$sql_lib_item_group_array=array();
	$sql_lib_item_group=sql_select("select id, item_name,conversion_factor,order_uom as cons_uom from lib_item_group");
	foreach($sql_lib_item_group as $row_sql_lib_item_group){
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][item_name]=$row_sql_lib_item_group[csf('item_name')];
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][conversion_factor]=$row_sql_lib_item_group[csf('conversion_factor')];
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][cons_uom]=$row_sql_lib_item_group[csf('cons_uom')];
	}

	$sql="select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id as wo_pre_cost_trim_cost_dtls, c.trim_group, c.description as description_pre_cost, c.brand_sup_ref as brand_sup_ref_precost, c.country, c.rate, d.id as po_id, d.po_number, d.po_quantity as plan_cut, min(e.id) as id, e.po_break_down_id, avg(e.cons) as cons, sum(f.wo_qnty) as cu_woq, sum(f.amount) as cu_amount, f.id as booking_id, f.sensitivity, f.delivery_date, f.description as description, f.brand_supplier as brand_sup_ref

	from
	wo_po_details_master a, wo_pre_cost_mst b, wo_pre_cost_trim_cost_dtls c, wo_po_break_down d, wo_pre_cost_trim_co_cons_dtls e, wo_booking_dtls f

	where
	a.job_no=b.job_no and a.job_no=c.job_no and a.job_no=d.job_no_mst and a.job_no=e.job_no and a.job_no=f.job_no and c.id=e.wo_pre_cost_trim_cost_dtls_id and d.id=e.po_break_down_id and e.wo_pre_cost_trim_cost_dtls_id= f.pre_cost_fabric_cost_dtls_id and e.po_break_down_id=f.po_break_down_id and f.booking_type=2 and f.booking_no=$txt_booking_no and a.company_name=$cbo_company_name   $garment_nature_cond and d.is_deleted=0 and d.status_active=1 and f.status_active=1 and f.is_deleted=0

	group by a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id, c.trim_group, c.description, c.brand_sup_ref, c.country, c.rate, d.id, d.po_number, d.po_quantity, e.po_break_down_id, f.id, f.sensitivity, f.delivery_date, f.description, f.brand_supplier
	order by d.id, c.id";
	//echo "$sql";die;

	$job_and_trimgroup_level=array();
	$i=1;
	$nameArray=sql_select( $sql );
	foreach ($nameArray as $selectResult){
	$cbo_currency_job=$selectResult[csf('currency_id')];
	$exchange_rate=$selectResult[csf('exchange_rate')];
	if($cbo_currency==$cbo_currency_job){
		$exchange_rate=1;
	}
	$brand_sup_ref = str_replace(' ', '', $selectResult[csf('brand_sup_ref')]);
	$description = str_replace(' ', '', $selectResult[csf('description')]);
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$description][$brand_sup_ref]['job_no'][$selectResult[csf('po_id')]]=$selectResult[csf('job_no')];
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$description][$brand_sup_ref]['po_id'][$selectResult[csf('po_id')]]=$selectResult[csf('po_id')];
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$description][$brand_sup_ref]['po_number'][$selectResult[csf('po_id')]]=$selectResult[csf('po_number')];
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$description][$brand_sup_ref]['country'][$selectResult[csf('po_id')]]=$selectResult[csf('country')];
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$description][$brand_sup_ref]['style_ref_no'][$selectResult[csf('po_id')]]=$selectResult[csf('style_ref_no')];
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$description][$brand_sup_ref]['description'][$selectResult[csf('po_id')]]=$selectResult[csf('description')];

 $job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$description][$brand_sup_ref][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['brand_sup_ref'][$selectResult[csf('po_id')]]=$selectResult[csf('brand_sup_ref')];

	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$description][$brand_sup_ref]['trim_group'][$selectResult[csf('po_id')]]=$selectResult[csf('trim_group')];
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$description][$brand_sup_ref]['trim_group_name'][$selectResult[csf('po_id')]]=$trim_group_library[$selectResult[csf('trim_group')]];
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$description][$brand_sup_ref]['wo_pre_cost_trim_cost_dtls'][$selectResult[csf('po_id')]]=$selectResult[csf('wo_pre_cost_trim_cost_dtls')];

	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$description][$brand_sup_ref]['uom'][$selectResult[csf('po_id')]]=$sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom];

	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$description][$brand_sup_ref]['uom_name'][$selectResult[csf('po_id')]]=$unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom]];


	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$description][$brand_sup_ref]['cu_woq'][$selectResult[csf('po_id')]]=$cu_woq;
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$description][$brand_sup_ref]['cu_amount'][$selectResult[csf('po_id')]]=$cu_amount;
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$description][$brand_sup_ref]['bal_woq'][$selectResult[csf('po_id')]]=$bal_woq;
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$description][$brand_sup_ref]['exchange_rate'][$selectResult[csf('po_id')]]=$exchange_rate;
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$description][$brand_sup_ref]['rate'][$selectResult[csf('po_id')]]=$rate_ord_uom;
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$description][$brand_sup_ref]['txt_delivery_date'][$selectResult[csf('po_id')]]=$selectResult[csf('delivery_date')];
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$description][$brand_sup_ref]['booking_id'][$selectResult[csf('po_id')]]=$selectResult[csf('booking_id')];
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$description][$brand_sup_ref]['sensitivity'][$selectResult[csf('po_id')]]=$selectResult[csf('sensitivity')];
	}
	$sql_booking=sql_select("select c.job_no,c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id,c.sensitivity, c.description,c.brand_supplier,b.requirment as wo_qnty, b.amount as amount from wo_po_details_master a, wo_po_break_down  d , wo_booking_dtls c,wo_trim_book_con_dtls b where a.id=d.job_id and a.job_no=c.job_no 
	 and  d.id=c.po_break_down_id and c.id=b.wo_trim_booking_dtls_id and d.id=b.po_break_down_id and b.job_no=a.job_no and c.booking_no=$txt_booking_no and c.booking_type=2 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0");

	foreach($sql_booking as $row_booking){
		$brand_sup_ref = str_replace(' ', '', $row_booking[csf('brand_supplier')]);
		$description = str_replace(' ', '', $row_booking[csf('description')]);
		$job_and_trimgroup_level[$row_booking[csf('job_no')]][$row_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_booking[csf('sensitivity')]][$description][$brand_sup_ref]['woq'][$row_booking[csf('po_break_down_id')]]+=$row_booking[csf('wo_qnty')];
		$job_and_trimgroup_level[$row_booking[csf('job_no')]][$row_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_booking[csf('sensitivity')]][$description][$brand_sup_ref]['amount'][$row_booking[csf('po_break_down_id')]]+=$row_booking[csf('amount')];

		$job_and_trimgroup_level[$row_booking[csf('job_no')]][$row_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_booking[csf('sensitivity')]][$description][$brand_sup_ref]['description'][$row_booking[csf('po_break_down_id')]]=$row_booking[csf('description')];
		$job_and_trimgroup_level[$row_booking[csf('job_no')]][$row_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_booking[csf('sensitivity')]][$description][$brand_sup_ref]['brand_sup_ref'][$row_booking[csf('po_break_down_id')]]=$row_booking[csf('brand_supplier')];
	}
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1350" class="rpt_table">
	<thead>
	<th width="40">SL</th>
	<th width="100">Job No</th>
	<th width="100">Style Ref</th>
	<th width="100">Ord. No</th>
	<th width="100">Trims Group</th>
    <th width="150">Description</th>
    <th width="150">Brand Sup.</th>
	<th width="80">UOM</th>
	<th width="100">Sensitivity</th>
	<th width="80">WOQ</th>
	<th width="80">Exch.Rate</th>
	<th width="80">Rate</th>
	<th width="80">Amount</th>
	<th width="">Delv. Date</th>
	</thead>
	<tbody id="save_list">
	<?
	$total_woq = 0;
	$total_amount = 0;
	if($cbo_level==1){
	foreach ($nameArray as $selectResult){
	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

	$cbo_currency_job=$selectResult[csf('currency_id')];
	$exchange_rate=$selectResult[csf('exchange_rate')];
	if($cbo_currency==$cbo_currency_job){
		$exchange_rate=1;
	}
	$brand_sup_ref = str_replace(' ', '', $selectResult[csf('brand_sup_ref')]);
	$description = str_replace(' ', '', $selectResult[csf('description')]);
	$woq=def_number_format($job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$description][$brand_sup_ref]['woq'][$selectResult[csf('po_id')]],5,"");
	$amount=def_number_format($job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$description][$brand_sup_ref]['amount'][$selectResult[csf('po_id')]],5,"");
	$rate=def_number_format($amount/$woq,5,"");
	//$total_amount+=$amount;

	?>
	<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="fnc_show_booking(<? echo $selectResult[csf('wo_pre_cost_trim_cost_dtls')];?>,'<? echo $selectResult[csf('po_id')]; ?>','<? echo $selectResult[csf('booking_id')];?>','<? echo $selectResult[csf('job_no')];?>')">
	<td width="40"><? echo $i;?></td>
	<td width="100">
	<? echo $selectResult[csf('job_no')];?>
	</td>
    <td width="80"> <p><? echo $selectResult[csf('style_ref_no')];?> </p>
    </td>
	<td width="100">
	<? echo $selectResult[csf('po_number')];?>
	</td>
	<td width="100"  title="<? echo $sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor];  ?>">
	<? echo $trim_group_library[$selectResult[csf('trim_group')]];?>
	</td>
	<td width="150" >
	<? echo $selectResult[csf('description')];?>
	</td>
    <td width="150" >
	<? echo $selectResult[csf('brand_sup_ref')];?>
	</td>
	<td width="80">
	<? echo $unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom]];?>
	</td>
	<td width="100" align="right">
    <? echo $size_color_sensitive[$selectResult[csf("sensitivity")]];?>
	</td>
	<td width="80" align="right">
	<? echo number_format($woq,4,'.',''); $total_woq += $woq;?>
	</td>
	<td width="80" align="right">
    <? echo $exchange_rate;?>
	</td>
	<td width="80" align="right">
    <? echo number_format($rate,4,'.','');?>
	</td>
	<td width="80" align="right">
    <? echo number_format($amount,4,'.',''); $total_amount += $amount ?>
	</td>
	<td width="" align="right">
    <? echo change_date_format($selectResult[csf('delivery_date')],"dd-mm-yyyy","-"); ?>
	</td>
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
	foreach ($desc as $brandsup){
	foreach ($brandsup as $wo_pre_cost_trim_cost_dtls){
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
	$style_ref_no=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['style_ref_no']));
	//$total_amount+=$amount;

	?>
	<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="fnc_show_booking(<? echo $wo_pre_cost_trim_id;?>,'<? echo $po_id; ?>','<? echo $booking_id; ?>','<? echo $job_no; ?>')">
	<td width="40"><? echo $i;?></td>
	<td width="100">
	<? echo $job_no?>
	</td>
	<td width="100" >
	<? echo $style_ref_no;?>
	</td>
	<td width="100" style="word-wrap:break-word;word-break: break-all">
	<? echo $po_number; ?>
	</td>
	<td width="100"  title="<? echo $sql_lib_item_group_array[$trim_group][conversion_factor];  ?>">
	<? echo $trim_group_library[$trim_group];?>
	</td>
	<td width="150" >
	<? echo $description;?>
	</td>
    <td width="150" >
	<? echo $brand_sup_ref;?>
	</td>
	<td width="80">
	<?  echo $unit_of_measurement[$uom];?>
	</td>
	<td width="100" align="right">
    <? echo $size_color_sensitive[$sensitivity];?>
	</td>
	<td width="80" align="right">
	<? echo number_format($woq,4,'.',''); $total_woq +=$woq; ?>
	</td>
	<td width="80" align="right">
    <? echo $exchange_rate;?>
	</td>
	<td width="80" align="right">
    <? echo number_format($rate,4,'.','');?>
	</td>
	<td width="80" align="right">
    <? echo number_format($amount,4,'.',''); $total_amount +=$amount; ?>
	</td>
	<td width="" align="right">
    <? echo change_date_format($delivery_date,"dd-mm-yyyy","-"); ?>
	</td>
	</tr>
	<?
	$i++;
	}
	}
	}
	}
	}
	}
	?>
	<tr>
		<td colspan="9" align="right"><strong>Total WOQ</strong></td>
		<td align="right"><? echo number_format($total_woq,4,'.',''); ?></td>
		<td colspan="2" align="right"><strong>Total Amount</strong></td>
		<td align="right"><? echo number_format($total_amount,4,'.',''); ?></td>
		<td>&nbsp;</td>
	</tr>
	</tbody>
	</table>
	<?
	exit();
}

if ($action == "consumption_popup")
{
	echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode,'','');
	$color_library=return_library_array("select id, color_name from lib_color", "id", "color_name");
	$size_library=return_library_array("select id, size_name from lib_size", "id", "size_name");
	?>
	<script>
		var str_gmtssizes = [<? echo substr(return_library_autocomplete( "select size_name from lib_size", "size_name"  ), 0, -1); ?>];
		var str_diawidth = [<? echo substr(return_library_autocomplete( "select color_name from lib_color", "color_name"  ), 0, -1); ?>];
		function poportionate_qty_091022(qty)
		{
			var txtwoq=document.getElementById('txtwoq').value;
			var txtwoq_qty=document.getElementById('txtwoq_qty').value*1;
			var rowCount = $('#tbl_consmption_cost tbody tr').length;
			for(var i=1; i<=rowCount; i++){
				var poreqqty=$('#poreqqty_'+i).val();
				console.log(txtwoq_qty+'--'+txtwoq+'--'+poreqqty);
				var txtwoq_cal =number_format_common((txtwoq_qty/txtwoq) * (poreqqty),5,0);
				$('#qty_'+i).val(txtwoq_cal);
				calculate_requirement(i);
			}
			set_sum_value( 'qty_sum', 'qty_');
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
		function poportionate_qty(qty)
		{
			var round_check=0;
			var total_txtwoq_cal=0;
			if ($('#round_down').is(":checked"))
			{
			   round_check=1;
			}
			var txtwoq=document.getElementById('txtwoq').value*1;
			var txtwoq_qty=document.getElementById('txtwoq_qty').value*1;
			var rowCount = $('#tbl_consmption_cost tbody tr').length;
			for(var i=1; i<=rowCount; i++){
				var poreqqty=$('#poreqqty_'+i).val()*1;
				var txtwoq_cal =number_format_common((txtwoq_qty/txtwoq) * (poreqqty),5,0);
				if(round_check==1){
					txtwoq_cal=Math.floor(txtwoq_cal);
					total_txtwoq_cal+=Math.floor(txtwoq_cal);
				}
				$('#qty_'+i).val(txtwoq_cal);
				calculate_requirement(i);
			}
			set_sum_value( 'qty_sum', 'qty_');
			if(round_check!=1){
				var j=i-1;
				var qty_sum=document.getElementById('qty_sum').value*1;
				if(qty_sum >txtwoq_qty ){
					$('#qty_'+j).val(number_format_common(txtwoq_cal*1-(qty_sum-txtwoq_qty),5,0));				
				}
				else if(qty_sum < txtwoq_qty ){
					$('#qty_'+j).val(number_format_common((txtwoq_cal*1) +(txtwoq_qty - qty_sum),5,0));				
				}
				else{
					$('#qty_'+j).val(number_format_common(txtwoq_cal,5,0));
				}
			}
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
			set_sum_value( 'woqty_sum', 'woqny_' )
			calculate_amount(i);
		}

		function set_sum_value(des_fil_id,field_id)
		{
			if(des_fil_id=='qty_sum') var ddd={dec_type:5,comma:0,currency:0};
			if(des_fil_id=='excess_sum') var ddd={dec_type:5,comma:0,currency:0};
			if(des_fil_id=='woqty_sum') var ddd={dec_type:5,comma:0,currency:0};
			if(des_fil_id=='amount_sum') var ddd={dec_type:5,comma:0,currency:0};
			if(des_fil_id=='pcs_sum') var ddd={dec_type:6,comma:0};
			var rowCount = $('#tbl_consmption_cost tbody tr').length;
			//alert(rowCount);
			if(rowCount>0)
			{
				math_operation( des_fil_id, field_id, '+', rowCount,ddd );
			}
		}

		function copy_value(value,field_id,i)
		{
			var gmtssizesid=document.getElementById('gmtssizesid_'+i).value;
			var pocolorid=document.getElementById('pocolorid_'+i).value;
			var rowCount = $('#tbl_consmption_cost tbody tr').length;
			var copy_basis=$('input[name="copy_basis"]:checked').val()

			for(var j=i; j<=rowCount; j++)
			{
				if(field_id=='des_' || field_id=='brndsup_' || field_id=='itemcolor_' || field_id=='txtzipperdtls_' || field_id=='itemsizes_' || field_id=='qty_' || field_id=='excess_' || field_id=='rate_')
				{
					if(copy_basis==0) document.getElementById(field_id+j).value=value;
					if(copy_basis==1)
					{
						if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value){
							document.getElementById(field_id+j).value=value;
						}
					}
					if(copy_basis==2){
						if( pocolorid==document.getElementById('pocolorid_'+j).value){
							document.getElementById(field_id+j).value=value;
						}
					}
					
					if(field_id=='qty_' || field_id=='excess_' || field_id=='rate_')
					{
						calculate_requirement(j);
						set_sum_value( 'qty_sum', 'qty_'  );
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
			set_sum_value( 'amount_sum', 'amount_' );
			calculate_avg_rate()
		}

		function calculate_avg_rate(){
			var woqty_sum=document.getElementById('woqty_sum').value;
			var amount_sum=document.getElementById('amount_sum').value;
			//var avg_rate=number_format_common((amount_sum/woqty_sum),5,0);
			var avg_rate=number_format((amount_sum/woqty_sum),6,'.','');
			document.getElementById('rate_sum').value=avg_rate;
		}

		function js_set_value(){
			var reg=/[^a-zA-Z0-9!@#$%^,;.:<>{}?\+|\[\]\- \/]/g;
			var row_num=$('#tbl_consmption_cost tbody tr').length;
			var cons_breck_down="";var desc_arr = [];
			for(var i=1; i<=row_num; i++){
				var txtdescription=$('#des_'+i).val();
				var txtsupref=$('#brndsup_'+i).val();
				
				var lowdesc = $('#des_'+i).val().toLowerCase();
			
				
				
				if(txtdescription.match(reg)){
					alert("Your Description Can not Have any thing other than a-zA-Z0-9!@#$%^,;.:<>{}?+|[]/- ");
					//release_freezing();
					$('#des_'+i).css('background-color', 'red');
					return;
				}
				
				if (jQuery.inArray($('#des_'+i).val().toLowerCase(), desc_arr) == -1)
				{
					desc_arr.push(lowdesc);
				}else{
					alert("Description not allow Duplicate value. ");
					return;
				}
				
				if(txtsupref.match(reg)){
					alert("Your Brand Sup. Ref Can not Have any thing other than a-zA-Z0-9!@#$%^,;.:<>{}?+|[]/- ");
					$('#brndsup_'+i).css('background-color', 'red');
					//release_freezing();
					return;
				}

				var pocolorid=$('#pocolorid_'+i).val(); if(pocolorid=='') pocolorid=0;
				var gmtssizesid=$('#gmtssizesid_'+i).val(); if(gmtssizesid=='') gmtssizesid=0;
				var des=trim($('#des_'+i).val()); if(des=='') des=0;
				var brndsup=trim($('#brndsup_'+i).val()); if(brndsup=='') brndsup=0;
				var itemcolor=$('#itemcolor_'+i).val(); if(itemcolor=='') itemcolor=0;
				var itemsizes=$('#itemsizes_'+i).val(); if(itemsizes=='') itemsizes=0;
				var qty=$('#qty_'+i).val(); if(qty=='') qty=0;
				var excess=$('#excess_'+i).val(); if(excess=='') excess=0;
				var woqny=$('#woqny_'+i).val(); if(woqny=='') woqny=0;
				var rate=$('#rate_'+i).val(); if(rate=='') rate=0;
				var amount=$('#amount_'+i).val(); if(amount=='') amount=0;
				var pcs=$('#pcs_'+i).val(); if(pcs=='') pcs=0;
				var colorsizetableid=$('#colorsizetableid_'+i).val(); if(colorsizetableid=='')colorsizetableid=0;
				var updateid=$('#updateid_'+i).val(); if(updateid=='') updateid=0;
				var reqqty=$('#reqqty_'+i).val(); if(reqqty=='') reqqty=0;
				var poarticle=$('#poarticle_'+i).val(); if(poarticle=='') poarticle='no article';
				var zipperdtls=$('#txtzipperdtls_'+i).val(); if(zipperdtls=='') zipperdtls=0;
				var zipperdtls=$('#txtzipperdtls_'+i).val(); if(zipperdtls=='') zipperdtls=0;
				var moqty=$('#txtmoqny_'+i).val(); if(moqty=='') moqty=0;
				var ppsampleqty=$('#txtppsampleqty_'+i).val(); if(ppsampleqty=='') ppsampleqty=0;

				if(cons_breck_down==""){
					cons_breck_down+=pocolorid+'_'+gmtssizesid+'_'+des+'_'+brndsup+'_'+itemcolor+'_'+itemsizes+'_'+qty+'_'+excess+'_'+woqny+'_'+rate+'_'+amount+'_'+pcs+'_'+colorsizetableid+'_'+reqqty+'_'+poarticle+'_'+moqty+'_'+ppsampleqty+'_'+zipperdtls;
				}
				else{
					cons_breck_down+="__"+pocolorid+'_'+gmtssizesid+'_'+des+'_'+brndsup+'_'+itemcolor+'_'+itemsizes+'_'+qty+'_'+excess+'_'+woqny+'_'+rate+'_'+amount+'_'+pcs+'_'+colorsizetableid+'_'+reqqty+'_'+poarticle+'_'+moqty+'_'+ppsampleqty+'_'+zipperdtls;
				}
			}
			document.getElementById('cons_breck_down').value=cons_breck_down;
			parent.emailwindow.hide();
		}
		
		function fnc_zipper_details_popup(inc)
		{
			var zipperdtls=document.getElementById('txtzipperdtls_'+inc).value;
			var page_link='trims_booking_multi_job_controllerurmi.php?action=zipper_details_popup&inc='+inc+'&zipperdtls='+zipperdtls;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Zipper Details Pop Up', 'width=450px,height=300px,center=1,resize=1,scrolling=0','../../')
			emailwindow.onclose=function()
			{
				var zipperdtls=this.contentDoc.getElementById("hdn_zipperdata").value;
				document.getElementById('txtzipperdtls_'+inc).value=zipperdtls;
				copy_value(zipperdtls,'txtzipperdtls_',inc);
			}	
		}
		
		function add_break_down_tr( i )
		{
			var row_num=$('#tbl_consmption_cost tbody tr').length;
			if (row_num!=i){
				return false;
			}
			else{
				i++;
				$("#tbl_consmption_cost tbody tr:last").clone().find("input").each(function() {
					$(this).attr({
						'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i;},
						//'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i;},
						'value': function(_, value) { return value }
					});
				}).end().appendTo("#tbl_consmption_cost tbody");
		
				$('#tbl_consmption_cost tbody tr:last td:eq(0)').text(i);
				$("#tbl_consmption_cost tbody tr:last").removeAttr('id').attr('id','break_'+i);
				
				$('#des_'+i).val("");
				$('#qty_'+i).val("");
				$('#woqny_'+i).val("");
				$('#txtmoqny_'+i).val("");
				$('#txtppsampleqty_'+i).val("");
				$('#amount_'+i).val("");
				
				$('#des_'+i).removeAttr("onChange").attr("onChange","copy_value(this.value,'des_',"+i+");");
				$('#brndsup_'+i).removeAttr("onChange").attr("onChange","copy_value(this.value,'brndsup_',"+i+");");
				$('#itemcolor_'+i).removeAttr("onChange").attr("onChange","copy_value(this.value,'itemcolor_',"+i+");");
				$('#txtzipperdtls_'+i).removeAttr("onChange").attr("onChange","copy_value(this.value,'txtzipperdtls_',"+i+");");
				$('#itemsizes_'+i).removeAttr("onChange").attr("onChange","copy_value(this.value,'itemsizes_',"+i+");");
				$('#qty_'+i).removeAttr("onChange").attr("onChange","set_sum_value('qty_sum', 'qty_'); set_sum_value('woqty_sum', 'woqny_'); calculate_requirement("+i+"); copy_value(this.value,'qty_',"+i+");");
				
				//$('#qty_'+i).removeAttr("onBlur").attr("onBlur","validate_sum("+i+");");
				$('#excess_'+i).removeAttr("onChange").attr("onChange","calculate_requirement("+i+"); set_sum_value('excess_sum', 'excess_'); set_sum_value('woqty_sum','woqny_');  copy_value(this.value,'excess_',"+i+");");
				$('#rate_'+i).removeAttr("onChange").attr("onChange","calculate_amount("+i+"); set_sum_value('amount_sum', 'amount_'); copy_value(this.value,'rate_',"+i+");");
				
				$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
				$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'this');");
			}
			set_sum_value( 'qty_sum', 'qty_' );
			set_sum_value( 'woqty_sum', 'woqny_' );
			set_sum_value( 'amount_sum', 'amount_' );
			set_sum_value( 'pcs_sum', 'pcs_' );
		}
		
		function fn_deletebreak_down_tr(rowNo,tr)
		{
			if(rowNo!=1)
			{
				var r=confirm("Do you want to delete this row?.\n If yes press OK \n or press Cancel." );
				if(r==false)
				{
					return;
				}
				//var permission_array=permission.split("_");
				//var updateid=$('#updateidlabtest_'+rowNo).val();
				var r=confirm("Are you sure?")
				if(r==true)
				{
					var index=rowNo-1
					//var index = $(tr).closest("tr").index();
					$("table#tbl_consmption_cost tbody tr:eq("+index+")").remove();
					var numRow = $('table#tbl_consmption_cost tbody tr').length;
					for(i = 1; i <= numRow; i++){
						var index2=i-1;
						$("#tbl_consmption_cost tbody tr:eq("+index2+")").find("input").each(function() {
							$(this).attr({
								'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
							});
						});
						
						$("#tbl_consmption_cost tbody tr:eq("+index2+")").each(function(){
							$(this).find('td:first').html(i);
							//$("#tbl_consmption_cost tbody tr").removeAttr('id').attr('id','break_'+i);
							$("#tbl_consmption_cost tbody tr:eq("+i+")").removeAttr('id').attr('id','break_'+i);
							$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
							$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'this');");
						});
					}
				}
				else
				{
					return;
				}
			}
		}
		function fnc_qty_blank()
		{
			var rowCount = $('#tbl_consmption_cost tbody tr').length;
			if(document.getElementById('chk_qty').checked==true)
			{
				document.getElementById('chk_qty').value=1;
				for(var i=1; i<=rowCount; i++){
					$('#qty_'+i).val('');
					calculate_requirement(i);
					calculate_amount(i);
				}
				set_sum_value( 'qty_sum', 'qty_' );
				set_sum_value( 'woqty_sum', 'woqny_' );
			}
			else if(document.getElementById('chk_qty').checked==false)
			{
				document.getElementById('chk_qty').value=0;
				for(var i=1; i<=rowCount; i++){
					$('#qty_'+i).val('');
					calculate_requirement(i);
					calculate_amount(i);
				}
				set_sum_value( 'qty_sum', 'qty_' );
				set_sum_value( 'woqty_sum', 'woqny_' );
			}
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
        
        $process_loss_method=return_field_value("process_loss_method", "variable_order_tracking", "company_name=$cbo_company_name  and variable_list=18 and item_category_id=4 and status_active=1 and is_deleted=0");
        $tot_po_qty=0;
        $sql_po_qty=sql_select("select b.id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id in($txt_po_id)  $txt_country_cond  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty"); //,c.item_number_id
        foreach($sql_po_qty as $sql_po_qty_row){
			$po_qty_arr[$sql_po_qty_row[csf('id')]]=$sql_po_qty_row[csf('order_quantity_set')];
			$tot_po_qty+=$sql_po_qty_row[csf('order_quantity_set')];
        }
		unset($sql_po_qty);
        ?>
        <div align="center" style="width:1230px;" >
            <fieldset>
                <form id="consumptionform_1" autocomplete="off">
                    <table width="1230" cellspacing="0" class="rpt_table" border="0" id="tbl_consmption_cost" rules="all">
                        <thead>
                        	<tr>
                                <th colspan="18" id="td_sync_msg" style="color:#FF0000"></th>
                            </tr>
                            <tr>
                                <th colspan="18">
                                    <input type="hidden" id="cons_breck_down" name="cons_breck_down" value="" />
                                    <input type="hidden" id="txtwoq" value="<?=$txt_req_quantity;?>"/>
                                    Wo Qty:<input type="text" id="txtwoq_qty" class="text_boxes_numeric" onBlur="poportionate_qty(this.value)" value="<?=$txtwoq; ?>"/>
                                    <input type="radio" name="copy_basis" value="0" <? if(!$txt_update_dtls_id) { echo "checked";} ?> >Copy to All
                                    <input type="radio" name="copy_basis" value="1">Gmts Size Wise
                                    <input type="radio" name="copy_basis" value="2">Gmts Color Wise
                                    <input type="radio" name="copy_basis" value="10" <? if($txt_update_dtls_id) { echo "checked";} ?> >No Copy
									<input type="checkbox" name="round_down" id="round_down" value="" onClick="poportionate_qty();" >Round Down
                                    <input type="hidden" id="process_loss_method_id" name="process_loss_method_id" value="<?=$process_loss_method; ?>"/>
                                    <input type="hidden" id="po_qty" name="po_qty" value="<?=$tot_po_qty; ?>"/>
                                </th>
                            </tr>
                            <tr>
                                <th width="20">SL</th>
                                <th width="70">Article No</th>
                                <th width="80">Gmts. Color</th>
                                <th width="60">Gmts. Size</th>
                                <th width="100">Description</th>
                                <th width="80">Brand/Sup Ref</th>
                                <th width="80">Item Color</th>
                                <th width="70">Zipper Dtls.</th>
                                <th width="60">Item Size</th>
                                <th width="70">Wo Qty. <input type="checkbox" id="chk_qty" name="chk_qty" value="0" onClick="fnc_qty_blank();"></th>
                                <th width="40">Excess %</th>
                                <th width="70">WO Qty. </th>
                                <th width="50" title="Minimum Order Qry.">MOQ</th>
                                <th width="50">PP Sample</th>
                                <th width="90">Rate</th>
                                <th width="80">Amount</th>
                                <th width="50">RMG Qty.</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?
                        $sql_lib_item_group_array=array(); 
                        $sql_lib_item_group=sql_select("select id, item_name, conversion_factor, order_uom as cons_uom, is_zipper from lib_item_group where id='$txt_trim_group_id'");
                        foreach($sql_lib_item_group as $row_lib){
							$sql_lib_item_group_array[$row_lib[csf('id')]][item_name]=$row_lib[csf('item_name')];
							$sql_lib_item_group_array[$row_lib[csf('id')]][conversion_factor]=$row_lib[csf('conversion_factor')];
							$sql_lib_item_group_array[$row_lib[csf('id')]][cons_uom]=$row_lib[csf('cons_uom')];
							$sql_lib_item_group_array[$row_lib[csf('id')]][is_zipper]=$row_lib[csf('is_zipper')];
                        }
						unset($sql_lib_item_group);

                        $booking_data_arr=array();
						if($txt_update_dtls_id=="") $txt_update_dtls_id=0;
						if($cbo_colorsizesensitive!=0)
						{
							$booking_data=sql_select("select id, wo_trim_booking_dtls_id, description, brand_supplier, item_color, item_size, cons, process_loss_percent, requirment, rate, amount, pcs, color_size_table_id, zipper_break_down, moq, pp_sample from wo_trim_book_con_dtls where wo_trim_booking_dtls_id in($txt_update_dtls_id) and status_active=1 and is_deleted=0");
							foreach($booking_data as $row){
								$booking_data_arr[$row[csf('color_size_table_id')]][id]=$row[csf('id')];
								$booking_data_arr[$row[csf('color_size_table_id')]][description]=$row[csf('description')];
								$booking_data_arr[$row[csf('color_size_table_id')]][brand_supplier]=$row[csf('brand_supplier')];
								$booking_data_arr[$row[csf('color_size_table_id')]][item_color]=$row[csf('item_color')];
								$booking_data_arr[$row[csf('color_size_table_id')]][item_size]=$row[csf('item_size')];
								$booking_data_arr[$row[csf('color_size_table_id')]][zipper_break_down]=$row[csf('zipper_break_down')];
	
								$booking_data_arr[$row[csf('color_size_table_id')]][cons]+=$row[csf('cons')];
								$booking_data_arr[$row[csf('color_size_table_id')]][process_loss_percent]=$row[csf('process_loss_percent')];
								$booking_data_arr[$row[csf('color_size_table_id')]][requirment]+=$row[csf('requirment')];
								$booking_data_arr[$row[csf('color_size_table_id')]][rate]=$row[csf('rate')];
								$booking_data_arr[$row[csf('color_size_table_id')]][amount]+=$row[csf('amount')];
								$booking_data_arr[$row[csf('color_size_table_id')]][moq]+=$row[csf('moq')];
								$booking_data_arr[$row[csf('color_size_table_id')]][ppsample]+=$row[csf('pp_sample')];
							}
							unset($booking_data);
						}
						else
						{
							$booking_data=sql_select("select min(id) as mid, min(wo_trim_booking_dtls_id) as wo_trim_booking_dtls_id, description, brand_supplier, item_color, item_size, sum(cons) as cons, process_loss_percent, sum(requirment) as requirment, rate, sum(amount) as amount, sum(pcs) as pcs, min(color_size_table_id) as color_size_table_id, zipper_break_down, moq as moq, pp_sample as pp_sample from wo_trim_book_con_dtls where wo_trim_booking_dtls_id in($txt_update_dtls_id) and status_active=1 and is_deleted=0 group by description, brand_supplier, item_color, item_size, process_loss_percent, rate, zipper_break_down, moq, pp_sample order by mid ASC");
							
							//echo "select min(id) as id, min(wo_trim_booking_dtls_id) as wo_trim_booking_dtls_id, description, brand_supplier, item_color, item_size, cons, process_loss_percent, requirment, rate, amount, pcs, min(color_size_table_id) as color_size_table_id, zipper_break_down from wo_trim_book_con_dtls where wo_trim_booking_dtls_id in($txt_update_dtls_id) and status_active=1 and is_deleted=0 group by description, brand_supplier, item_color, item_size, cons, process_loss_percent, requirment, rate, amount, pcs, zipper_break_down";
							//echo count($booking_data);
							$nosensbookdataArr=$booking_data;
							foreach($booking_data as $row){
								
								$booking_data_arr[$row[csf('description')]][$row[csf('brand_supplier')]][$row[csf('item_color')]][$row[csf('item_size')]][id]=$row[csf('mid')];
								//$booking_data_arr[$row[csf('color_size_table_id')]][description]=$row[csf('description')];
								//$booking_data_arr[$row[csf('color_size_table_id')]][brand_supplier]=$row[csf('brand_supplier')];
								//$booking_data_arr[$row[csf('color_size_table_id')]][item_color]=$row[csf('item_color')];
								//$booking_data_arr[$row[csf('color_size_table_id')]][item_size]=$row[csf('item_size')];
								$booking_data_arr[$row[csf('color_size_table_id')]][zipper_break_down]=$row[csf('zipper_break_down')];
	
								$booking_data_arr[$row[csf('color_size_table_id')]][cons]+=$row[csf('cons')];
								$booking_data_arr[$row[csf('color_size_table_id')]][process_loss_percent]=$row[csf('process_loss_percent')];
								$booking_data_arr[$row[csf('color_size_table_id')]][requirment]+=$row[csf('requirment')];
								$booking_data_arr[$row[csf('color_size_table_id')]][rate]=$row[csf('rate')];
								$booking_data_arr[$row[csf('color_size_table_id')]][amount]+=$row[csf('amount')];
							}
							unset($booking_data);
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
							$sql="select b.id, b.po_number, b.po_quantity, min(c.id) as color_size_table_id, c.color_number_id, min(c.color_order) as color_order, sum(c.order_quantity) as order_quantity, (sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set, d.trim_group from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where  a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number, b.po_quantity, a.total_set_qnty, c.color_number_id, d.trim_group order by b.id, color_order";
							$gmt_size_edb=1; $item_size_edb=1;
                        }
                        else if($cbo_colorsizesensitive==2){

							//$req_qty_arr=$trims->getQtyArray_by_orderPrecostdtlsidAndGmtssize();
							//$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidAndGmtssize();

							$req_qty_arr=$trims->getQtyArray_by_orderPrecostdtlsidGmtssizeAndArticle();
							$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidGmtssizeAndArticle();

							$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.size_number_id,c.article_number,min(c.size_order) as size_order,min(e.item_size) as item_size,sum(c.order_quantity) as order_quantity, (sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set, d.trim_group from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id) $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number, b.po_quantity, a.total_set_qnty, c.size_number_id, c.article_number, d.trim_group order by b.id,size_order";
							$gmt_color_edb=1; $item_color_edb=1;
                        }
                        else if($cbo_colorsizesensitive==3){
							$req_qty_arr=$trims->getQtyArray_by_orderPrecostdtlsidAndGmtscolor();
							$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidAndGmtscolor();
							$sql="select b.id, b.po_number, b.po_quantity,min(c.id) as color_size_table_id, c.color_number_id, min(c.color_order) as color_order, sum(c.order_quantity) as order_quantity, (sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number, b.po_quantity, a.total_set_qnty, c.color_number_id order by b.id, color_order";
							$gmt_size_edb=1; $item_size_edb=1;
                        }
                        else if($cbo_colorsizesensitive==4){
							//$req_qty_arr=$trims->getQtyArray_by_OrderPrecostdtlsidGmtscolorGmtssizeAndArticle();
							//$req_amount_arr=$trims->getAmountArray_by_OrderPrecostdtlsidGmtscolorGmtssizeAndArticle();
							
							$req_qty_arr=$trims->getQtyArray_by_OrderPrecostdtlsidGmtscolorGmtssizeAndArticleItemColorItemSize();
							$req_amount_arr=$trims->getAmountArray_by_OrderPrecostdtlsidGmtscolorGmtssizeAndArticleItemColorItemSize();
							//print_r($req_qty_arr);

							//$sql="select b.id, b.po_number, b.po_quantity,min(c.id) as color_size_table_id, c.color_number_id, c.size_number_id, c.article_number, min(c.color_order) as color_order, min(c.size_order) as size_order, min(e.item_size) as item_size, sum(c.order_quantity) as order_quantity, (sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id,c.size_number_id,c.article_number  order by b.id, color_order,size_order";
							
							$sql="select b.id, b.po_number, b.po_quantity, min(c.id) as color_size_table_id, c.color_number_id, c.size_number_id, c.article_number, min(c.color_order) as color_order, min(c.size_order) as size_order, e.item_size as item_size, sum(c.order_quantity) as order_quantity, (sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set, e.item_color_number_id from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and b.id=c.po_break_down_id and b.id=e.po_break_down_id and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.id in ($txt_po_id) $txt_country_cond and d.id=$cbo_trim_precost_id group by b.id, b.po_number, b.po_quantity, a.total_set_qnty, c.color_number_id, c.size_number_id, c.article_number, e.item_color_number_id, e.item_size order by b.id, color_order, size_order";
                        }
                        else{
							$req_qty_arr=$trims->getQtyArray_by_orderAndPrecostdtlsid();
							$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsid();
							$sql="select b.id, b.po_number, b.po_quantity, min(c.id) as color_size_table_id, sum(c.order_quantity) as order_quantity, (sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.id in($txt_po_id) $txt_country_cond and d.id=$cbo_trim_precost_id group by b.id, b.po_number,b.po_quantity,a.total_set_qnty order by b.id";
                        }

							
                        $po_color_level_data_arr=array(); $po_size_level_data_arr=array();

                        $po_no_sen_level_data_arr=array(); $po_color_size_level_data_arr=array();
                        $data_array=sql_select($sql);
                        if ( count($data_array)>0){
							$i=0;
							foreach( $data_array as $row ){
								$data=explode('_',$data_array_cons[$i]);
								$i++;
								$item_color = $booking_data_arr[$row[csf('color_size_table_id')]][item_color];
								if($item_color==0 || $item_color=="" ) $item_color = $row[csf('color_number_id')];

								$item_size=$booking_data_arr[$row[csf('color_size_table_id')]][item_size];
								if($item_size==0 || $item_size == "") $item_size=$row[csf('item_size')];

								$rate=$booking_data_arr[$row[csf('color_size_table_id')]][rate];
								if($rate==0 || $rate=="")$rate=$txt_avg_price;

								$description=$booking_data_arr[$row[csf('color_size_table_id')]][description];
								if($description=="") $description=trim($txt_pre_des);

								$brand_supplier=$booking_data_arr[$row[csf('color_size_table_id')]][brand_supplier];
								if($brand_supplier=="") $brand_supplier=trim($txt_pre_brand_sup);

								if($cbo_colorsizesensitive==1 || $cbo_colorsizesensitive==3 )
								{
									$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]];
									$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
									$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");

									$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
									$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['po_qty'][$row[csf('id')]]=$po_qty;
									$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
									$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['po_id'][$row[csf('id')]]=$row[csf('id')];
									$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];
									$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['color_size_table_id'][$row[csf('id')]]=$row[csf('color_size_table_id')];
									if($cbo_colorsizesensitive==3)
									{
										$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['item_color_number_id'][$row[csf('id')]]=$row[csf('item_color_number_id')];
									}

									$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_cons'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][cons];
									$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_qty'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][requirment];
									$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_amt'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][amount];
								}
								else if($cbo_colorsizesensitive==2)
								{
									$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]];
									$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
									$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");
									$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
									$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_qty'][$row[csf('id')]]=$po_qty;
									$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
									$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_id'][$row[csf('id')]]=$row[csf('id')];
									$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];

									$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['color_size_table_id'][$row[csf('id')]]=$row[csf('color_size_table_id')];
									$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['article_number'][$row[csf('id')]]=$row[csf('article_number')];

									$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_cons'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][cons];
									$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_qty'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][requirment];
									$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_amt'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][amount];

								}
								else if($cbo_colorsizesensitive==4)
								{
									$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]];
									
									$txtreq_amount=$req_amount_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]];
									

									// echo "<pre>";
									// print_r($req_amount_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]);
									// echo "</pre>";
									$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
									$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");
									
									if($row[csf('item_color_number_id')]=='' || $row[csf('item_color_number_id')]=='0' || $color_library[$row[csf('item_color_number_id')]]=='' || $color_library[$row[csf('item_color_number_id')]]=='0') $row[csf('item_color_number_id')]=$row[csf('color_number_id')];
									//echo $row[csf('item_color_number_id')].'<br>';

									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['po_qty'][$row[csf('id')]]=$po_qty;
									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['po_id'][$row[csf('id')]]=$row[csf('id')];
									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];
									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['color_size_table_id'][$row[csf('id')]]=$row[csf('color_size_table_id')];
									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['article_number'][$row[csf('id')]]=$row[csf('article_number')];
									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['item_size'][$row[csf('id')]]=$row[csf('item_size')];
									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['item_color_number_id'][$row[csf('id')]]=$row[csf('item_color_number_id')];

									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['booking_cons'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][cons];
									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['booking_qty'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][requirment];
									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['booking_amt'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][amount];

								}
								else if($cbo_colorsizesensitive==0)
								{
									$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id];
									$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
									$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");

									$po_no_sen_level_data_arr[$cbo_trim_precost_id]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
									$po_no_sen_level_data_arr[$cbo_trim_precost_id]['po_qty'][$row[csf('id')]]=$po_qty;
									$po_no_sen_level_data_arr[$cbo_trim_precost_id]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
									$po_no_sen_level_data_arr[$cbo_trim_precost_id]['po_id'][$row[csf('id')]]=$row[csf('id')];
									$po_no_sen_level_data_arr[$cbo_trim_precost_id]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];
									$po_no_sen_level_data_arr[$cbo_trim_precost_id]['color_size_table_id'][$row[csf('id')]]=$row[csf('color_size_table_id')];

									$po_no_sen_level_data_arr[$cbo_trim_precost_id]['booking_cons'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][cons];
									$po_no_sen_level_data_arr[$cbo_trim_precost_id]['booking_qty'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][requirment];
									$po_no_sen_level_data_arr[$cbo_trim_precost_id]['booking_amt'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][amount];
								}
							}
							
                        }
						$piNumber=0;

						$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no='$txt_booking_no' and b.item_group='".$txt_trim_group_id."' and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
						if($pi_number) $piNumber=1;
						
						$recvNumber=0;
						$recv_number=return_field_value( "recv_number", "inv_receive_master a,inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no='$txt_booking_no' and b.item_group_id='".$txt_trim_group_id."' and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
						if($recv_number) $recvNumber=1;
						
						//echo $piNumber."PI8888888";

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
									$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]];
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
								//if($item_size==0 || $item_size == "") $item_size=$size_library[$row[csf('size_number_id')]];
								if($item_size=='0' || $item_size == "") $item_size=$row[csf('item_size')];
								if($item_size=='0' || $item_size == "") $item_size=$size_library[$row[csf('size_number_id')]];

								$rate=$booking_data_arr[$row[csf('color_size_table_id')]][rate];
								if($rate==0 || $rate=="") $rate=$txt_avg_price;

								/*$description=$booking_data_arr[$row[csf('color_size_table_id')]][description];
								if($description=="") $description=trim($txt_pre_des);*/
								$description=trim($txt_pre_des);
								$brand_supplier=$booking_data_arr[$row[csf('color_size_table_id')]][brand_supplier];
								if($brand_supplier=="") $brand_supplier=trim($txt_pre_brand_sup);
								
								$moq=$booking_data_arr[$row[csf('color_size_table_id')]][moq];
								$ppsample=$booking_data_arr[$row[csf('color_size_table_id')]][ppsample];
								
								$is_zipper=$sql_lib_item_group_array[$txt_trim_group_id][is_zipper];
								
								$zipper_break_down=$booking_data_arr[$row[csf('color_size_table_id')]][zipper_break_down];
								
								if($txtwoq_cal>0){
									
									$i++;
									if($is_zipper==1) $fncZipper="fnc_zipper_details_popup(".$i.");"; else $fncZipper="";
									?>
									<tr id="break_<?=$i;?>" align="center">
                                        <td><?=$i;?></td>
                                        <td><input type="text" id="poarticle_<?=$i;?>" name="poarticle_<?=$i;?>" class="text_boxes" style="width:58px" value="<?=$row[csf('article_number')]; ?>"  readonly /></td>
                                        <td>
                                            <input type="text" id="pocolor_<?=$i;?>"  name="pocolor_<?=$i;?>" class="text_boxes" style="width:68px" value="<? echo $color_library[$row[csf('color_number_id')]]; ?>" <? if($gmt_color_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> readonly/>
                                            <input type="hidden" id="pocolorid_<?=$i;?>" name="pocolorid_<?=$i;?>" class="text_boxes" style="width:50px" value="<?=$row[csf('color_number_id')]; ?>" readonly />
                                            <input type="hidden" id="poid_<?=$i;?>" name="poid_<?=$i;?>" class="text_boxes" style="width:50px" value="<?=$row[csf('id')]; ?>" />
                                            <input type="hidden" id="poqty_<?=$i;?>" name="poqty_<?=$i;?>" class="text_boxes" style="width:50px" value="<?=$po_qty_arr[$row[csf('id')]]; ?>" readonly />
                                            <input type="hidden" id="poreqqty_<?=$i;?>" name="poreqqty_<?=$i;?>" class="text_boxes" style="width:50px" value="<?=$txtwoq_cal; ?>" readonly />
                                        </td>
                                        <td>
                                            <input type="text" id="gmtssizes_<? echo $i;?>"  name="gmtssizes_<? echo $i;?>" class="text_boxes" style="width:48px" value="<? echo $size_library[$row[csf('size_number_id')]]; ?>" <? if($gmt_size_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> readonly/>
                                            <input type="hidden" id="gmtssizesid_<? echo $i;?>"  name="gmtssizesid_<? echo $i;?>" class="text_boxes" style="width:40px" value="<? echo $row[csf('size_number_id')]; ?>" readonly />
                                        </td>
                                        <td><input type="text" id="des_<? echo $i;?>"  name="des_<? echo $i;?>" class="text_boxes" style="width:88px" value="<? echo $description;?>" onChange="copy_value(this.value,'des_',<? echo $i;?>)" <? if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> />
                                        </td>
                                        <td><input type="text" id="brndsup_<? echo $i;?>"  name="brndsup_<? echo $i;?>" class="text_boxes" style="width:68px" value="<? echo $brand_supplier; ?>" onChange="copy_value(this.value,'brndsup_',<? echo $i;?>)" <? if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?>/>
                                        </td>
                                        <td><input type="text" id="itemcolor_<?=$i;?>" value="<?=$color_library[$item_color]; ?>" name="itemcolor_<?=$i;?>" class="text_boxes" style="width:68px" onChange="copy_value(this.value,'itemcolor_',<?=$i;?>)" <? if($item_color_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> />
                                        </td>
                                        <td><input type="text" id="txtzipperdtls_<?=$i;?>" value="<?=$zipper_break_down; ?>" name="txtzipperdtls_<?=$i;?>" class="text_boxes" style="width:58px" onChange="copy_value(this.value,'txtzipperdtls_',<?=$i;?>);" onDblClick="<?=$fncZipper; ?>" readonly placeholder="Browse" />
                                        </td>
                                        <td><input type="text" id="itemsizes_<? echo $i;?>"  name="itemsizes_<? echo $i;?>"    class="text_boxes" style="width:48px" onChange="copy_value(this.value,'itemsizes_',<? echo $i;?>)" value="<? echo $item_size; ?>" <? if($item_size_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> />
                                        </td>
                                        <td><input type="hidden" id="reqqty_<? echo $i;?>"  name="reqqty_<? echo $i;?>" class="text_boxes_numeric" style="width:40px"    value="<? echo $txtwoq_cal ?>" readonly/>
                                        	<input type="text" id="qty_<? echo $i;?>" onBlur="validate_sum( <? echo $i; ?> )" onChange="set_sum_value( 'qty_sum', 'qty_' );set_sum_value( 'woqty_sum', 'woqny_' );calculate_requirement(<? echo $i;?>);copy_value(this.value,'qty_',<? echo $i;?>)"  name="qty_<? echo $i;?>" class="text_boxes_numeric" style="width:58px"   placeholder="<? echo $txtwoq_cal; ?>" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][cons]; ?>"/>
                                        </td>
                                        <td>
                                        	<input type="text" id="excess_<? echo $i;?>" onBlur="set_sum_value( 'excess_sum', 'excess_' ) "  name="excess_<? echo $i;?>" class="text_boxes_numeric" style="width:28px" onChange="calculate_requirement(<? echo $i;?>);set_sum_value( 'excess_sum', 'excess_' );set_sum_value( 'woqty_sum', 'woqny_' );copy_value(this.value,'excess_',<? echo $i;?>) " value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][process_loss_percent]; ?>" disabled/>
                                        </td>
                                        <td>
                                        	<input type="text" id="woqny_<?=$i;?>" onBlur="set_sum_value( 'woqty_sum', 'woqny_' )" onChange="set_sum_value( 'woqty_sum', 'woqny_' )" name="woqny_<?=$i;?>" class="text_boxes_numeric" style="width:58px" value="<?=$booking_data_arr[$row[csf('color_size_table_id')]][requirment]; ?>" readonly /></td>
                                        <td><input type="text" id="txtmoqny_<?=$i;?>" name="txtmoqny_<?=$i;?>" class="text_boxes_numeric" style="width:38px" value="<? if($moq){echo $moq;} ?>" /></td>
                                        <td><input type="text" id="txtppsampleqty_<?=$i;?>" name="txtppsampleqty_<?=$i;?>" class="text_boxes_numeric" style="width:38px" value="<? if($ppsample){echo $ppsample;} ?>" /></td>
                                        <td>
                                        	<input type="text" id="rate_<?=$i;?>"  name="rate_<?=$i;?>" class="text_boxes_numeric" style="width:78px" onChange="calculate_amount(<?=$i;?>);set_sum_value( 'amount_sum', 'amount_' );copy_value(this.value,'rate_',<?=$i;?>) " value="<?=number_format($rate, 4,'.',''); ?>" <? if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> />
                                        </td>
                                        <td>
                                        	<input type="text" id="amount_<?=$i;?>"  name="amount_<?=$i;?>"  onBlur="set_sum_value( 'amount_sum', 'amount_' ) " class="text_boxes_numeric" style="width:68px" value="<?=number_format($booking_data_arr[$row[csf('color_size_table_id')]][amount], 4,'.',''); ?>" readonly>
                                        </td>
                                        <td>
                                            <input type="text" id="pcs_<? echo $i;?>"  name="pcs_<? echo $i;?>"  onBlur="set_sum_value( 'pcs_sum', 'pcs_' ) " class="text_boxes_numeric" style="width:38px"  value="<? echo $row[csf('order_quantity')]; ?>" readonly>
                                            <input type="hidden" id="pcsset_<? echo $i;?>"  name="pcsset_<? echo $i;?>"  onBlur="set_sum_value( 'pcs_sum', 'pcs_' ) " class="text_boxes_numeric" style="width:30px"  value="<? echo $row[csf('order_quantity_set')]; ?>" readonly>
                                            <input type="hidden" id="colorsizetableid_<? echo $i;?>"  name="colorsizetableid_<? echo $i;?>" class="text_boxes" style="width:25px" value="<? echo $row[csf('color_size_table_id')]; ?>" />
                                            <input type="hidden" id="updateid_<? echo $i;?>"  name="updateid_<? echo $i;?>" class="text_boxes" style="width:25px" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][id]; ?>" readonly />
                                        </td>
                                        <?
										if($cbo_colorsizesensitive==0){
										?>
                                        <td>
                                        <input type="button" id="increase_<?=$i; ?>" style="width:25px" class="formbutton" value="+" onClick="add_break_down_tr(<?=$i; ?>);"  <? if($item_color_edb || $piNumber || $recvNumber ){ echo "disabled";}else { echo "";} ?> />
                                    <input type="button" id="decrease_<?=$i; ?>" style="width:25px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<?=$i; ?>,'this');" <? if($item_color_edb || $piNumber || $recvNumber ){ echo "disabled";}else { echo "";} ?> />
                                    	</td>
                                        <?
										}
										?>
									</tr>
								<?
								}
							}
                        }

                        $level_arr=array(); $gmt_color_edb=""; $item_color_edb=""; $gmt_size_edb=""; $item_size_edb="";
                        if($cbo_colorsizesensitive==1){
							$sql="select min(b.id) as id , min(c.id) as color_size_table_id,c.color_number_id,min(c.color_order) as color_order,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.color_number_id order by  color_order";
							$level_arr=$po_color_level_data_arr;
							$gmt_size_edb=1;
							$item_size_edb=1;
                        }
                        else if($cbo_colorsizesensitive==2){
							$sql="select min(b.id) as id , min(c.id) as color_size_table_id,c.size_number_id,c.article_number,min(c.size_order) as size_order,min(e.item_size) as item_size,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.size_number_id,c.article_number order by size_order";
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
						// echo $sql="select min(b.id) as id ,min(c.id) as color_size_table_id,c.color_number_id,c.size_number_id,c.article_number,min(c.color_order) as color_order,min(c.size_order) as size_order,min(e.item_size) as item_size,sum(c.order_quantity) as order_quantity  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  and  b.id in($txt_po_id) $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.color_number_id,c.size_number_id, c.article_number order by  color_order,size_order,c.article_number";
						 
						$sql="select min(b.id) as id, min(c.id) as color_size_table_id, c.color_number_id, c.size_number_id, c.article_number, min(c.color_order) as color_order, min(c.size_order) as size_order, e.item_size as item_size, e.item_color_number_id, sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.id in($txt_po_id) $txt_country_cond and d.id=$cbo_trim_precost_id group by c.color_number_id, c.size_number_id, c.article_number, e.item_color_number_id, e.item_size order by color_order,size_order, c.article_number";
							$level_arr=$po_color_size_level_data_arr;
                        }
                        else{
							$sql="select b.job_no_mst,min(b.id) as id , min(c.id) as color_size_table_id,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.id in($txt_po_id) $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.job_no_mst";
							$level_arr=$po_no_sen_level_data_arr;
                        }

                        $data_array=sql_select($sql);
                        if ( count($data_array)>0 && $cbo_level==2){
							$i=0;
							if(count($nosensbookdataArr)>0 && $cbo_colorsizesensitive==0) {$data_array=$nosensbookdataArr;}
				
							foreach( $data_array as $row ){

								if($cbo_colorsizesensitive==1){
									$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['req_qty']),5,"");
									$po_qty=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['po_qty']);
									$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['order_quantity_set']);
									$booking_cons=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_cons']),5,"");
									$booking_qty=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_qty']),5,"");
									$booking_amt=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_amt']),5,"");
									$item_size="";
									$item_color = $booking_data_arr[$row[csf('color_size_table_id')]][item_color];
								if($item_color==0 || $item_color=="" ) $item_color = $row[csf('color_number_id')];

								}
								if($cbo_colorsizesensitive==2){
									$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['req_qty']),5,"");
									$po_qty=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_qty']);
									$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity_set']);
									$booking_cons=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_cons']),5,"");
									$booking_qty=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_qty']),5,"");
									$booking_amt=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_amt']),5,"");
									$item_size=$booking_data_arr[$row[csf('color_size_table_id')]][item_size];
									if($item_size=='0' || $item_size == "") $item_size=$row[csf('item_size')];
									if($item_size=='0' || $item_size == "") $item_size=$size_library[$row[csf('size_number_id')]];
									$item_color = "";
								}
								if($cbo_colorsizesensitive==3){
									$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['req_qty']),5,"");
									$po_qty=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['po_qty']);
									$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['order_quantity_set']);
									$booking_cons=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_cons']),5,"");
									$booking_qty=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_qty']),5,"");
									$booking_amt=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_amt']),5,"");
									$item_size="";
									$item_color = $booking_data_arr[$row[csf('color_size_table_id')]][item_color];
									if($item_color==0 || $item_color=="" ) $item_color = $row[csf('color_number_id')];
								}
								if($cbo_colorsizesensitive==4){
									if($row[csf('item_color_number_id')]=='' || $row[csf('item_color_number_id')]=='0' || $color_library[$row[csf('item_color_number_id')]]=='' || $color_library[$row[csf('item_color_number_id')]]=='0') $row[csf('item_color_number_id')]=$row[csf('color_number_id')];
									
									$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['req_qty']),5,"");
									$po_qty=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['po_qty']);
									$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['order_quantity_set']);
									$booking_cons=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['booking_cons']),4,"");
									$booking_qty=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['booking_qty']),4,"");
									$booking_amt=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['booking_amt']),5,"");
									$item_color = $booking_data_arr[$row[csf('color_size_table_id')]][item_color];
									if($item_color==0 || $item_color=="" ) $item_color = $row[csf('color_number_id')];
									if($color_library[$item_color]=='0' || $color_library[$item_color]=="" || $item_color=='0' || $item_color=="") $item_color = $row[csf('color_number_id')];
									
									$item_size=$booking_data_arr[$row[csf('color_size_table_id')]][item_size];
									if($item_size=='0' || $item_size == "") $item_size=$row[csf('item_size')];
									if($item_size=='0' || $item_size == "") $item_size=$size_library[$row[csf('size_number_id')]];
								}
								if($cbo_colorsizesensitive==0){
									$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id]['req_qty']),5,"");
									$po_qty=array_sum($level_arr[$cbo_trim_precost_id]['po_qty']);
									$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id]['order_quantity_set']);
									$booking_cons=def_number_format(array_sum($level_arr[$cbo_trim_precost_id]['booking_cons']),5,"");
									$booking_qty=def_number_format(array_sum($level_arr[$cbo_trim_precost_id]['booking_qty']),5,"");
									$booking_amt=def_number_format(array_sum($level_arr[$cbo_trim_precost_id]['booking_amt']),5,"");
									$item_color = $booking_data_arr[$row[csf('color_size_table_id')]][item_color];
									if($item_color==0 || $item_color=="" ) $item_color = $row[csf('color_number_id')];
									$booking_qty=$booking_cons=$rate=$booking_amt=$moq=$ppsample=0;
									if(count($nosensbookdataArr)>0)
									{
										$description=$row[csf('description')];
										$brand_supplier=$row[csf('brand_supplier')];
										$item_color=$row[csf('item_color')];
										$zipper_break_down=$row[csf('zipper_break_down')];
										$item_size=$row[csf('item_size')];
										$booking_cons=$row[csf('cons')];
										$booking_qty=$row[csf('requirment')];
										$row[csf('order_quantity')]=$row[csf('pcs')];
										$rate=$row[csf('rate')];
										$booking_amt=$row[csf('amount')];
										$moq=$row[csf('moq')];
										$ppsample=$row[csf('pp_sample')];
									}
								}
								if(count($nosensbookdataArr)==0 || $cbo_colorsizesensitive!=0)
								{
									$rate=$booking_data_arr[$row[csf('color_size_table_id')]][rate]; //issue id 20426 dont off
									if(($rate*1)>0) $rate=$rate;
									else if($booking_amt>0) $rate=$booking_amt/$booking_qty;
									else $rate=$txt_avg_price;
									$rate=number_format($rate,6,'.','');
								}
								
								if($cbo_colorsizesensitive!=0)
								{
									$description=$booking_data_arr[$row[csf('color_size_table_id')]][description];
									if($description=="") $description=trim($txt_pre_des);
									//$description=trim($txt_pre_des);
									$brand_supplier=$booking_data_arr[$row[csf('color_size_table_id')]][brand_supplier];
									if($brand_supplier=="") $brand_supplier=trim($txt_pre_brand_sup);
									
									$is_zipper=$sql_lib_item_group_array[$txt_trim_group_id][is_zipper];
									$zipper_break_down=$booking_data_arr[$row[csf('color_size_table_id')]][zipper_break_down];
									
									$moq=$booking_data_arr[$row[csf('color_size_table_id')]][moq];
									$ppsample=$booking_data_arr[$row[csf('color_size_table_id')]][ppsample];
								}

								if($txtwoq_cal>0){
									$i++;
									if($is_zipper==1) $fncZipper="fnc_zipper_details_popup(".$i.");"; else $fncZipper="";
									?>
									<tr id="break_<?=$i;?>" align="center">
                                        <td><?=$i;?></td>
                                        <td><input type="text" id="poarticle_<?=$i;?>" name="poarticle_<?=$i;?>" class="text_boxes" style="width:58px" value="<?=$row[csf('article_number')]; ?>" readonly /></td>
                                        <td>
                                            <input type="text" id="pocolor_<?=$i;?>" name="pocolor_<?=$i;?>" class="text_boxes" style="width:68px" value="<?=$color_library[$row[csf('color_number_id')]]; ?>" <? if($gmt_color_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> readonly />
                                            <input type="hidden" id="pocolorid_<?=$i;?>" name="pocolorid_<?=$i;?>" class="text_boxes" style="width:55px" value="<?=$row[csf('color_number_id')]; ?>" readonly />
                                            <input type="hidden" id="poid_<?=$i;?>" name="poid_<?=$i;?>" class="text_boxes" style="width:55px" value="<?=$row[csf('id')]; ?>" readonly />
                                            <input type="hidden" id="poqty_<?=$i;?>" name="poqty_<?=$i;?>" class="text_boxes" style="width:55px" value="<?=$po_qty; ?>" readonly />
                                            <input type="hidden" id="poreqqty_<?=$i;?>" name="poreqqty_<?=$i;?>" class="text_boxes" style="width:55px" value="<?=$txtwoq_cal; ?>" readonly />
                                        </td>
                                        <td>
                                            <input type="text" id="gmtssizes_<?=$i; ?>" name="gmtssizes_<?=$i; ?>" class="text_boxes" style="width:48px" value="<?=$size_library[$row[csf('size_number_id')]]; ?>" <? if($gmt_size_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> readonly/>
                                            <input type="hidden" id="gmtssizesid_<?=$i; ?>" name="gmtssizesid_<?=$i;?>" class="text_boxes" style="width:40px" value="<?=$row[csf('size_number_id')]; ?>" readonly />
                                        </td>
                                        <td><input type="text" id="des_<?=$i;?>" name="des_<?=$i;?>" class="text_boxes" style="width:88px" value="<?=$description;?>" onChange="copy_value(this.value,'des_',<?=$i;?>);" <? if( $piNumber || $recvNumber ){ echo "disabled";} else { echo "";} ?> /></td>
                                        <td><input type="text" id="brndsup_<?=$i;?>" name="brndsup_<?=$i; ?>" class="text_boxes" style="width:68px" value="<?=$brand_supplier; ?>" onChange="copy_value(this.value,'brndsup_',<?=$i;?>);" <? if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> /></td>
                                        <td><input type="text" id="itemcolor_<?=$i;?>" value="<?=$color_library[$item_color]; ?>" name="itemcolor_<?=$i;?>" class="text_boxes" style="width:68px" onChange="copy_value(this.value,'itemcolor_',<?=$i;?>);" <? if($item_color_edb || $piNumber || $recvNumber ){ echo "disabled";}else { echo "";} ?> /></td>
                                        <td><input type="text" id="txtzipperdtls_<?=$i;?>" value="<?=$zipper_break_down; ?>" name="txtzipperdtls_<?=$i;?>" class="text_boxes" style="width:58px" onChange="copy_value(this.value,'txtzipperdtls_',<?=$i; ?>);" onDblClick="<?=$fncZipper; ?>" readonly placeholder="Browse" /></td>
                                        <td><input type="text" id="itemsizes_<?=$i;?>" name="itemsizes_<?=$i;?>" class="text_boxes" style="width:48px" onChange="copy_value(this.value,'itemsizes_',<?=$i;?>);" value="<?=$item_size; ?>" <? if($item_size_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?>/>
                                        </td>
                                        <td><input type="hidden" id="reqqty_<?=$i;?>" name="reqqty_<?=$i;?>" class="text_boxes_numeric" style="width:40px" value="<?=$txtwoq_cal; ?>" readonly/>
                                        	<input type="text" id="qty_<?=$i;?>" onChange="set_sum_value('qty_sum', 'qty_'); set_sum_value( 'woqty_sum', 'woqny_'); calculate_requirement(<?=$i;?>); copy_value(this.value,'qty_',<?=$i;?>);" name="qty_<?=$i;?>" class="text_boxes_numeric" style="width:58px" placeholder="<?=$txtwoq_cal; ?>" value="<? if($booking_cons>0){echo $booking_cons;} ?>"/><!-- onBlur="validate_sum(<?//=$i; ?>);"-->
                                        </td>
                                        <td>
                                        	<input type="text" id="excess_<?=$i;?>" name="excess_<?=$i;?>" class="text_boxes_numeric" style="width:28px" onChange="calculate_requirement(<?=$i;?>); set_sum_value( 'excess_sum', 'excess_' ); set_sum_value( 'woqty_sum', 'woqny_' ); copy_value(this.value,'excess_',<?=$i;?>);" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][process_loss_percent]; ?>" disabled/>
                                        </td>
                                        <td><input type="text" id="woqny_<?=$i;?>" onBlur="set_sum_value('woqty_sum', 'woqny_');" onChange="set_sum_value('woqty_sum','woqny_');" name="woqny_<?=$i;?>" class="text_boxes_numeric" style="width:58px" value="<? if($booking_qty){echo $booking_qty;} ?>" readonly /></td>
                                        <td><input type="text" id="txtmoqny_<?=$i;?>" name="txtmoqny_<?=$i;?>" class="text_boxes_numeric" style="width:38px" value="<? if($moq){echo $moq;} ?>" /></td>
                                        <td><input type="text" id="txtppsampleqty_<?=$i;?>" name="txtppsampleqty_<?=$i;?>" class="text_boxes_numeric" style="width:38px" value="<? if($ppsample){echo $ppsample;} ?>" /></td>
                                        <td><input type="text" id="rate_<?=$i;?>" name="rate_<?=$i;?>" class="text_boxes_numeric" style="width:78px" onChange="calculate_amount(<?=$i; ?>);set_sum_value('amount_sum', 'amount_'); copy_value(this.value,'rate_',<?=$i; ?>);" value="<?=number_format($rate, 4,'.',''); ?>" <? if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> /></td>
                                     
										<td><input type="text" id="amount_<?=$i;?>" name="amount_<?=$i;?>" onBlur="set_sum_value( 'amount_sum', 'amount_')" class="text_boxes_numeric" style="width:68px" value="<?=number_format($booking_amt, 4,'.',''); ?>" readonly></td>

                                        <td><input type="text" id="pcs_<?=$i;?>" name="pcs_<?=$i;?>" onBlur="set_sum_value('pcs_sum', 'pcs_');" class="text_boxes_numeric" style="width:48px" value="<?=$row[csf('order_quantity')]; ?>" readonly>
                                            <input type="hidden" id="pcsset_<?=$i;?>" name="pcsset_<?=$i;?>" onBlur="set_sum_value('pcs_sum', 'pcs_');" class="text_boxes_numeric" style="width:30px"  value="<?=$order_quantity_set; ?>" readonly>
                                            <input type="hidden" id="colorsizetableid_<?=$i;?>" name="colorsizetableid_<?=$i; ?>" class="text_boxes" style="width:35px" value="<?=$row[csf('color_size_table_id')]; ?>" />
                                            <input type="hidden" id="updateid_<?=$i;?>" name="updateid_<?=$i;?>" class="text_boxes" style="width:35px" value="<?=$booking_data_arr[$row[csf('color_size_table_id')]][id]; ?>" readonly />
                                        </td>
                                        <?
										if($cbo_colorsizesensitive==0){
										?>
                                        <td>
                                        <input type="button" id="increase_<?=$i; ?>" style="width:25px" class="formbutton" value="+" onClick="add_break_down_tr(<?=$i; ?>);"  <? if($item_color_edb || $piNumber || $recvNumber ){ echo "disabled";}else { echo "";} ?> />
                                    <input type="button" id="decrease_<?=$i; ?>" style="width:25px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<?=$i; ?>,'this');" <? if($item_color_edb || $piNumber || $recvNumber ){ echo "disabled";}else { echo "";} ?> />
                                    	</td>
                                        <?
										}
										?>
									</tr>
								<?
								}
							}
                        }
                        ?>
                        </tbody>
                        <tfoot>
                            <tr>
                               <th width="20">&nbsp;</th>
                               <th width="70">&nbsp;</th>
                               <th width="80">&nbsp;</th>
                               <th width="60">&nbsp;</th>
                               <th width="100">&nbsp;</th>
                               <th width="80">&nbsp;</th>
                               <th width="80">&nbsp;</th>
                               <th width="70">&nbsp;</th>
                               <th width="60">&nbsp;</th>
                               <th width="70"><input type="text" id="qty_sum" name="qty_sum" class="text_boxes_numeric" style="width:58px" readonly></th>
                               <th width="40"><input type="text" id="excess_sum"  name="excess_sum" class="text_boxes_numeric" style="width:28px" readonly></th>
                               <th width="70"><input type="text" id="woqty_sum"  name="woqty_sum" class="text_boxes_numeric" style="width:58px" readonly></th>
                               <th width="50">&nbsp;</th>
                               <th width="50">&nbsp;</th>
                               <th width="90"><input type="text" id="rate_sum"  name="rate_sum" class="text_boxes_numeric" style="width:78px" readonly></th>
                               <th width="80"><input type="text" id="amount_sum" name="amount_sum" class="text_boxes_numeric" style="width:68px" readonly></th>
                               <th><input type="hidden" id="json_data" name="json_data" class="text_boxes_numeric" style="width:48px" value='<?=json_encode($level_arr); ?>' readonly>
                                	<input type="text" id="pcs_sum" name="pcs_sum" class="text_boxes_numeric" style="width:48px" readonly>
                                </th>
                                <th>&nbsp;</th>
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

if($action=="zipper_details_popup")
{
	echo load_html_head_contents("Zipper Details Pop Up","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $zipperdtls;
	?>
	<script>
		function js_set_value()
		{
			var numRow = $('table#tblzipper tbody tr').length;
			var zipper_break_data="";
			
			for(var i=1; i<=numRow; i++)
			{
				var zipid=$('#txtzipid_'+i).val(); if(zipid=='') zipid=0;
				var zipcolor=$('#txtzipcolor_'+i).val(); if(zipcolor=='') zipcolor=0;
				
				if(zipper_break_data=="") zipper_break_data=zipid+'$!'+zipcolor; else zipper_break_data+="$!$"+zipid+'$!'+zipcolor;
			}
			document.getElementById('hdn_zipperdata').value=zipper_break_data;
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
    <div align="center">
        <form>
            <input type="hidden" id="hdn_zipperdata" name="hdn_zipperdata" />
            <table width="400" class="rpt_table" border="1" rules="all" cellpadding="0" cellspacing="0" id="tblzipper">
                <thead>
                	<tr>
                    	<th colspan="3">Zipper Item Color Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="100">Item/Parts</th>
                        <th>Parts Color</th>
                    </tr>
                </thead>
                <tbody>
                		<?	$i=1;
							$exzipperdtls=explode("$!$",$zipperdtls);
							$zipdtlsArr=array();
							foreach($exzipperdtls as $exzipdtlsrow)
							{
								$exzipdtls=explode("$!",$exzipdtlsrow);
								$zipdtlsArr[$exzipdtls[0]]=$exzipdtls[1];
							}
							//print_r($zipdtlsArr);
							foreach($zipper_color_parts_arr as $zipid=>$zipval)
							{
								if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
                                <tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer"> 
                                	<td align="center"><?=$i; ?></td>
                                    <td style="word-break:break-all" align="center"><?=$zipval; ?></td>
                                    <td style="word-break:break-all" align="center">
                                        <input type="text" name="txtzipcolor_<?=$i; ?>" id="txtzipcolor_<?=$i; ?>" value="<?=$zipdtlsArr[$zipid]; ?>" style="width:180px;" class="text_boxes" placeholder="Write"/>
                                        <input type="hidden" name="txtzipid_<?=$i; ?>" id="txtzipid_<?=$i; ?>" value="<?=$zipid; ?>" style="width:60px;" class="text_boxes"/>
                                    </td>
                                </tr>
                                <?
								$i++;
							}
						?>
                </tbody>
                <tfoot>
                    <tr>
                        <td align="center" class="button_container" colspan="3"><input type="button" class="formbutton" value="Close" onClick="js_set_value();"/> </td>
                    </tr>
                </tfoot>
            </table>
        </form>
    </div>
    </body>
    <?
	exit();
}

if ($action=="set_cons_break_down")
{
	$color_library=return_library_array("select id, color_name from lib_color", "id", "color_name");
	$data=explode("_",$data);
	$garments_nature=$data[0];
	$cbo_company_name=$data[1];
	$txt_job_no=$data[2];
	$txt_po_id=trim($data[3]);
	$cbo_trim_precost_id=$data[4];
	$txt_trim_group_id=$data[5];
	$txt_update_dtls_id=$data[6];
	$cbo_colorsizesensitive=$data[7];
	$txt_req_quantity=$data[8];
	$txt_avg_price=$data[9];
	$txt_country=trim($data[10]);
	$txt_pre_des=$data[11];
	$txt_pre_brand_sup=$data[12];
	$cbo_level=$data[13];

	if($txt_job_no==""){
		$txt_job_no_cond="";
		$txt_job_no_cond1="";
	}
	else{
		$txt_job_no_cond ="and a.job_no='$txt_job_no'";
		$txt_job_no_cond1 ="and job_no='$txt_job_no'";
	}

	if($txt_country=="") $txt_country_cond=""; else $txt_country_cond ="and c.country_id in ($txt_country)";

	$process_loss_method=return_field_value("process_loss_method", "variable_order_tracking", "company_name=$cbo_company_name  and variable_list=18 and item_category_id=4 and status_active=1 and is_deleted=0");
	//echo "10**select b.id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id in($txt_po_id) $txt_country_cond  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty";die;
	$sql_po_qty=sql_select("select b.id, sum(c.order_quantity) as order_quantity, (sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id in($txt_po_id) $txt_country_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty"); //,c.item_number_id
	$tot_po_qty=0;
	foreach($sql_po_qty as$sql_po_qty_row){
		$po_qty_arr[$sql_po_qty_row[csf('id')]]=$sql_po_qty_row[csf('order_quantity_set')];
		$tot_po_qty+=$sql_po_qty_row[csf('order_quantity_set')];
	}
	$sql_lib_item_group_array=array();
	$sql_lib_item_group=sql_select("select id, item_name, conversion_factor, order_uom as cons_uom from lib_item_group");
	foreach($sql_lib_item_group as $row_sql_lib_item_group){
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][item_name]=$row_sql_lib_item_group[csf('item_name')];
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][conversion_factor]=$row_sql_lib_item_group[csf('conversion_factor')];
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][cons_uom]=$row_sql_lib_item_group[csf('cons_uom')];
	}

	if($txt_update_dtls_id=="" || $txt_update_dtls_id==0) $txt_update_dtls_id=0;else $txt_update_dtls_id=$txt_update_dtls_id;
	$booking_data_arr=array();
	$booking_data=sql_select("select id,wo_trim_booking_dtls_id,description,brand_supplier,item_color,item_size,cons,process_loss_percent,requirment,rate, 	amount,pcs,color_size_table_id  from wo_trim_book_con_dtls where wo_trim_booking_dtls_id in($txt_update_dtls_id) and status_active=1 and is_deleted=0");
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
	$cu_booking_data=sql_select("select a.pre_cost_fabric_cost_dtls_id,b.id,b.wo_trim_booking_dtls_id,b.po_break_down_id,b.color_number_id,b.gmts_sizes,b.requirment,b.article_number  from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id=b.wo_trim_booking_dtls_id and b.po_break_down_id in($txt_po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id not in($txt_update_dtls_id)");
	foreach($cu_booking_data as $cu_booking_data_row){
		if($cbo_colorsizesensitive==1 || $cbo_colorsizesensitive==3 ){
			$cu_booking_data_arr[$cu_booking_data_row[csf('po_break_down_id')]][$cu_booking_data_row[csf('pre_cost_fabric_cost_dtls_id')]][$cu_booking_data_row[csf('color_number_id')]]+=$cu_booking_data_row[csf('requirment')];
		}
		if($cbo_colorsizesensitive==2 ){
			$cu_booking_data_arr[$cu_booking_data_row[csf('po_break_down_id')]][$cu_booking_data_row[csf('pre_cost_fabric_cost_dtls_id')]][$cu_booking_data_row[csf('gmts_sizes')]][$cu_booking_data_row[csf('article_number')]]+=$cu_booking_data_row[csf('requirment')];
		}
		if($cbo_colorsizesensitive==4 ){
			$cu_booking_data_arr[$cu_booking_data_row[csf('po_break_down_id')]][$cu_booking_data_row[csf('pre_cost_fabric_cost_dtls_id')]][$cu_booking_data_row[csf('color_number_id')]][$cu_booking_data_row[csf('gmts_sizes')]][$cu_booking_data_row[csf('article_number')]]+=$cu_booking_data_row[csf('requirment')];
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
		 $sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.color_number_id,min(c.color_order) as color_order,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id order by b.id, color_order";
		$gmt_size_edb="disabled";
		$item_size_edb="disabled";
	}
	else if($cbo_colorsizesensitive==2){
	    //$req_qty_arr=$trims->getQtyArray_by_orderPrecostdtlsidAndGmtssize();
		//$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidAndGmtssize();
		$req_qty_arr=$trims->getQtyArray_by_orderPrecostdtlsidGmtssizeAndArticle();
		$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidGmtssizeAndArticle();
		$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.size_number_id,c.article_number,min(c.size_order) as size_order,min(e.item_size) as item_size,sum(c.order_quantity) as order_quantity ,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.size_number_id,c.article_number order by b.id,size_order";
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

		//$req_qty_arr=$trims->getQtyArray_by_OrderPrecostdtlsidGmtscolorAndGmtssize();
		//$req_amount_arr=$trims->getAmountArray_by_OrderPrecostdtlsidGmtscolorAndGmtssize();

		//$req_qty_arr=$trims->getQtyArray_by_OrderPrecostdtlsidGmtscolorGmtssizeAndArticle();
		//$req_amount_arr=$trims->getAmountArray_by_OrderPrecostdtlsidGmtscolorGmtssizeAndArticle();
		
		$req_qty_arr=$trims->getQtyArray_by_OrderPrecostdtlsidGmtscolorGmtssizeAndArticleItemColorItemSize();
		$req_amount_arr=$trims->getAmountArray_by_OrderPrecostdtlsidGmtscolorGmtssizeAndArticleItemColorItemSize();

		$sql="select b.id, b.po_number, b.po_quantity, min(c.id) as color_size_table_id, c.color_number_id, c.size_number_id, c.article_number ,min(c.color_order) as color_order, min(c.size_order) as size_order, e.item_size as item_size, sum(c.order_quantity) as order_quantity, (sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set, e.item_color_number_id from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and b.id=c.po_break_down_id and b.id=e.po_break_down_id and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.id in($txt_po_id) $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number, b.po_quantity, a.total_set_qnty, c.color_number_id, c.size_number_id, c.article_number, e.item_color_number_id, e.item_size order by b.id, color_order, size_order";
	}
	else{
		$req_qty_arr=$trims->getQtyArray_by_orderAndPrecostdtlsid();
	    $req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsid();
		$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty order by b.id";
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
			if($item_color=="") $item_color=0;

			$item_size=$row[csf('item_size')];
			if($item_size=="") $item_size=0;
			$excess=0;
			$pcs=$row[csf('order_quantity_set')];
			if($pcs=="") $pcs=0;

			$colorsizetableid=$row[csf('color_size_table_id')];
			if($colorsizetableid=="") $colorsizetableid=0;

			$articleNumber=$row[csf('article_number')];
			if($articleNumber=="") $articleNumber='no article';

			if($cbo_colorsizesensitive==1 || $cbo_colorsizesensitive==3 ){
				$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]];
				$req_qnty_ord_uom = def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
				$cu_qnty_ord_uom = def_number_format($cu_booking_data_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]],5,"");
				//$req_qnty_ord_uom = $req_qnty_ord_uom - $cu_qnty_ord_uom;
				//$txtwoq_cal = def_number_format($req_qnty_ord_uom,5,"");
				$req_qnty_ordUom = def_number_format((($data[14]/$data[8])*$req_qnty_ord_uom),5,"");
				$txtwoq_cal = def_number_format($req_qnty_ordUom,5,"");
				$amount = def_number_format($txtwoq_cal*$txt_avg_price,5,"");

				$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
				$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['po_qty'][$row[csf('id')]]=$po_qty;
				$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
				$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['po_id'][$row[csf('id')]]=$row[csf('id')];
				$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];
				$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['color_size_table_id'][$row[csf('id')]]=$row[csf('color_size_table_id')];
				
				if($cbo_colorsizesensitive==3)
				{
					$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['item_color_number_id'][$row[csf('id')]]=$row[csf('item_color_number_id')];
				}

				$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['amount'][$row[csf('id')]]=$amount;
			}
			else if($cbo_colorsizesensitive==2){
				$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]];
				$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
				$cu_qnty_ord_uom = def_number_format($cu_booking_data_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]],5,"");
				//$req_qnty_ord_uom = $req_qnty_ord_uom - $cu_qnty_ord_uom;
				//$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");
				$req_qnty_ordUom = def_number_format((($data[14]/$data[8])*$req_qnty_ord_uom),5,"");
				$txtwoq_cal = def_number_format($req_qnty_ordUom,5,"");
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");

				$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
				$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_qty'][$row[csf('id')]]=$po_qty;
				$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
				$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_id'][$row[csf('id')]]=$row[csf('id')];
				$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];
				$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['color_size_table_id'][$row[csf('id')]]=$row[csf('color_size_table_id')];

				$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['amount'][$row[csf('id')]]=$amount;
			}
			else if($cbo_colorsizesensitive==4){
				$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]];
				$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
				$cu_qnty_ord_uom = def_number_format($cu_booking_data_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]],5,"");
				//$req_qnty_ord_uom = $req_qnty_ord_uom - $cu_qnty_ord_uom;
				//$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");
				$req_qnty_ordUom = def_number_format((($data[14]/$data[8])*$req_qnty_ord_uom),5,"");
				$txtwoq_cal = def_number_format($req_qnty_ordUom,5,"");
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
				
				if($row[csf('item_size')]=='' || $row[csf('item_size')]=='0') $row[csf('item_size')]=$size_library[$row[csf('size_number_id')]];
				if($row[csf('item_color_number_id')]=='' || $row[csf('item_color_number_id')]=='0' || $color_library[$row[csf('item_color_number_id')]]=='' || $color_library[$row[csf('item_color_number_id')]]=='0') $row[csf('item_color_number_id')]=$row[csf('color_number_id')];

				$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
				$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['po_qty'][$row[csf('id')]]=$po_qty;
				$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
				$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['po_id'][$row[csf('id')]]=$row[csf('id')];
				$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];

				$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['color_size_table_id'][$row[csf('id')]]=$row[csf('color_size_table_id')];
				$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['item_color_number_id'][$row[csf('id')]]=$row[csf('item_color_number_id')];

				$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['amount'][$row[csf('id')]]=$amount;
			}
			else if($cbo_colorsizesensitive==0){
				$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id];
				$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
				$cu_qnty_ord_uom = def_number_format($cu_booking_data_arr[$row[csf('id')]][$cbo_trim_precost_id],5,"");
				//$req_qnty_ord_uom = $req_qnty_ord_uom - $cu_qnty_ord_uom;
				//$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");
				$req_qnty_ordUom = def_number_format((($data[14]/$data[8])*$req_qnty_ord_uom),5,"");
				$txtwoq_cal = def_number_format($req_qnty_ordUom,5,"");
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");

				$po_no_sen_level_data_arr[$cbo_trim_precost_id]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
				$po_no_sen_level_data_arr[$cbo_trim_precost_id]['po_qty'][$row[csf('id')]]=$po_qty;
				$po_no_sen_level_data_arr[$cbo_trim_precost_id]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
				$po_no_sen_level_data_arr[$cbo_trim_precost_id]['po_id'][$row[csf('id')]]=$row[csf('id')];
				$po_no_sen_level_data_arr[$cbo_trim_precost_id]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];
				$po_no_sen_level_data_arr[$cbo_trim_precost_id]['color_size_table_id'][$row[csf('id')]]=$row[csf('color_size_table_id')];

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

			$articleNumber=$row[csf('article_number')];
			if($articleNumber=="") $articleNumber='no article';

			if($cbo_colorsizesensitive==1 || $cbo_colorsizesensitive==3 ){
				$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]];
				$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
				//$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");
				$req_qnty_ordUom = def_number_format((($data[14]/$data[8])*$req_qnty_ord_uom),5,"");
				$txtwoq_cal = def_number_format($req_qnty_ordUom,5,"");
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
			}

			else if($cbo_colorsizesensitive==2){
				$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]];
				$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
				//$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");
				$req_qnty_ordUom = def_number_format((($data[14]/$data[8])*$req_qnty_ord_uom),5,"");
				$txtwoq_cal = def_number_format($req_qnty_ordUom,5,"");
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
			}
			else if($cbo_colorsizesensitive==4){
				$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]];
				$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
				//$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");
				$req_qnty_ordUom = def_number_format((($data[14]/$data[8])*$req_qnty_ord_uom),5,"");
				$txtwoq_cal = def_number_format($req_qnty_ordUom,5,"");
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
			}
			else if($cbo_colorsizesensitive==0){
				$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id];
				$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
				//$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");
				$req_qnty_ordUom = def_number_format((($data[14]/$data[8])*$req_qnty_ord_uom),5,"");
				$txtwoq_cal = def_number_format($req_qnty_ordUom,5,"");
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
			}
			if($txtwoq_cal>0){
				if($cons_breck_down=="")
				{
					$cons_breck_down.=$color_number_id.'_'.$size_number_id.'_'.$description.'_'.$brand_supplier.'_'.$item_color.'_'.$item_size.'_'.$txtwoq_cal.'_'.$excess.'_'.$txtwoq_cal.'_'.$txt_avg_price.'_'.$amount.'_'.$pcs.'_'.$colorsizetableid."_".$txtwoq_cal."_".$articleNumber;
				}
				else
				{
					$cons_breck_down.="__".$color_number_id.'_'.$size_number_id.'_'.$description.'_'.$brand_supplier.'_'.$item_color.'_'.$item_size.'_'.$txtwoq_cal.'_'.$excess.'_'.$txtwoq_cal.'_'.$txt_avg_price.'_'.$amount.'_'.$pcs.'_'.$colorsizetableid."_".$txtwoq_cal."_".$articleNumber;
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
		$sql="select min(b.id) as id , min(c.id) as color_size_table_id,c.size_number_id,c.article_number,min(c.size_order) as size_order,min(e.item_size) as item_size,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.size_number_id,c.article_number order by size_order";
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
		
		$sql="select min(b.id) as id, min(c.id) as color_size_table_id, c.color_number_id, c.size_number_id, c.article_number, min(c.color_order) as color_order, min(c.size_order) as size_order, e.item_size as item_size, sum(c.order_quantity) as order_quantity, e.item_color_number_id from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and b.id=c.po_break_down_id and b.id=e.po_break_down_id and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.id in($txt_po_id) $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.color_number_id, c.size_number_id, c.article_number, e.item_color_number_id, e.item_size order by color_order, size_order";
		$level_arr=$po_color_size_level_data_arr;
	}
	else{
		  $sql="select b.job_no_mst,min(b.id) as id , min(c.id) as color_size_table_id,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.job_no_mst";
		$level_arr=$po_no_sen_level_data_arr;
	}
	$data_array=sql_select($sql);

	$cons_breck_down="";
	if ( count($data_array)>0 && $cbo_level==2)
	{
		$i=0;
		foreach( $data_array as $row )
		{
			if($row[csf('item_size')]=='' || $row[csf('item_size')]=='0') $row[csf('item_size')]=$size_library[$row[csf('size_number_id')]];
			if($cbo_colorsizesensitive==1){
				$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['req_qty']),5,"");
				$po_qty=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['po_qty']);
				$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['order_quantity_set']);
				$booking_qty=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_qty']),5,"");
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
			}
			if($cbo_colorsizesensitive==2){
				$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['req_qty']),5,"");
				$po_qty=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_qty']);
				$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity_set']);
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
			}
			if($cbo_colorsizesensitive==3){
				$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['req_qty']),5,"");
				$po_qty=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['po_qty']);
				$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['order_quantity_set']);
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
			}
			if($cbo_colorsizesensitive==4){
				if($row[csf('item_color_number_id')]=='' || $row[csf('item_color_number_id')]=='0' || $color_library[$row[csf('item_color_number_id')]]=='' || $color_library[$row[csf('item_color_number_id')]]=='0') $row[csf('item_color_number_id')]=$row[csf('color_number_id')];
				$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['req_qty']),5,"");
				$po_qty=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['po_qty']);
				$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['order_quantity_set']);
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

			$articleNumber=$row[csf('article_number')];
			if($articleNumber=="") $articleNumber='no article';
			
			if($cbo_colorsizesensitive==3 || $cbo_colorsizesensitive==4)
			{
				$item_color=$color_library[$row[csf('item_color_number_id')]];
			}
			else
			{
				$item_color=$color_library[$row[csf('color_number_id')]];
			}

			if($item_color=="") $item_color=0;

			if($txtwoq_cal>0){
				if($cons_breck_down==""){
					$cons_breck_down.=trim($color_number_id).'_'.$size_number_id.'_'.$description.'_'.$brand_supplier.'_'.$item_color.'_'.$item_size.'_'.$txtwoq_cal.'_'.$excess.'_'.$txtwoq_cal.'_'.$txt_avg_price.'_'.$amount.'_'.$pcs.'_'.$colorsizetableid."_".$txtwoq_cal."_".$articleNumber;
				}
				else{
					$cons_breck_down.="__".trim($color_number_id).'_'.$size_number_id.'_'.$description.'_'.$brand_supplier.'_'.$item_color.'_'.$item_size.'_'.$txtwoq_cal.'_'.$excess.'_'.$txtwoq_cal.'_'.$txt_avg_price.'_'.$amount.'_'.$pcs.'_'.$colorsizetableid."_".$txtwoq_cal."_".$articleNumber;
				}
			}
		}
		echo $cons_breck_down."**".json_encode($level_arr);
	}
}

if ($action=="save_update_delete"){
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$location_id=str_replace("'","",$delivery_address);
	$str_rep=array("/", "&", "*", "(", ")", "=","'",",",'"','#');
	
	$sql_loc=sql_select("select a.id,a.address from lib_location a, lib_company b where b.id=a.company_id and a.address is not null  and   a.status_active =1 and a.is_deleted=0  and   b.status_active =1 and b.is_deleted=0 and a.id in($location_id) order by a.id");
	foreach($sql_loc as $row)
	{
		$deliveryaddress=str_replace($str_rep,' ',str_replace("'","",$row[csf("address")]));
		$loc_addressArr.=$deliveryaddress.'__';
	}
	$loc_addressArr=rtrim($loc_addressArr,"__");
	$delivery_addressArr=explode("__",$loc_addressArr);
	$delivery_address=implode(": ",$delivery_addressArr);

	if ($operation==0){
		$con = connect();
		if($db_type==0) mysql_query("BEGIN");
		
		if($db_type==0) $date_cond=" YEAR(insert_date)";
		else if($db_type==2) $date_cond="to_char(insert_date,'YYYY')";
		
		$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'TB', date("Y",time()), 5, "select booking_no_prefix, booking_no_prefix_num from wo_booking_mst where company_id=$cbo_company_name and booking_type in(2) and $date_cond=".date('Y',time())." order by id DESC ", "booking_no_prefix", "booking_no_prefix_num" ));
		
		$id=return_next_id( "id", "wo_booking_mst", 1);
		$field_array="id, booking_type, is_short, booking_month, booking_year, booking_no_prefix, booking_no_prefix_num, booking_no, company_id, buyer_id, item_category, supplier_id, currency_id, booking_date, delivery_date, pay_mode, source, fabric_source, attention, remarks, item_from_precost, entry_form, cbo_level, ready_to_approved, inserted_by, insert_date, delivery_address, location_id_address, pay_term, tenor, status_active, is_deleted";
		$data_array ="(".$id.",2,2,".$cbo_booking_month.",".$cbo_booking_year.",'".$new_booking_no[1]."',".$new_booking_no[2].",'".$new_booking_no[0]."',".$cbo_company_name.",".$cbo_buyer_name.",4,".$cbo_supplier_name.",".$cbo_currency.",".$txt_booking_date.",".$txt_delivery_date.",".$cbo_pay_mode.",".$cbo_source.",".$cbo_material_source.",".$txt_attention.",".$txt_remarks.",1,272,".$cbo_level.",".$cbo_ready_to_approved.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$delivery_address."','".$location_id."','".str_replace("'", "", $cbo_payterm_id)."','".str_replace("'", "", $txt_tenor)."',1,0)";
		//echo "10**insert into wo_booking_mst (".$field_array.") values ".$data_array."";die;
		$rID=sql_insert("wo_booking_mst",$field_array,$data_array,0);
		if($db_type==0){
			if($rID){
				mysql_query("COMMIT");
				echo "0**".$new_booking_no[0]."**".$id;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_booking_no[0]."**".$id;
			}
		}
		else if($db_type==2 || $db_type==1 ){
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
		$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_booking_no");
		if($is_approved==1){
			echo "app1**".str_replace("'","",$txt_booking_no);
			die;
		}
		$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
		if($pi_number){
			echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
			die;
		}
		$recv_number=return_field_value( "recv_number", "inv_receive_master a,inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no  and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
		if($recv_number){
			echo "recv1**".str_replace("'","",$txt_booking_no)."**".$recv_number;
			die;
		}

		$field_array_up="booking_month*booking_year*supplier_id*currency_id*booking_date*delivery_date*pay_mode*source*fabric_source*attention*remarks*item_from_precost*cbo_level*ready_to_approved*updated_by*update_date*revised_no*delivery_address*location_id_address*pay_term*tenor";

		$data_array_up ="".$cbo_booking_month."*".$cbo_booking_year."*".$cbo_supplier_name."*".$cbo_currency."*".$txt_booking_date."*".$txt_delivery_date."*".$cbo_pay_mode."*".$cbo_source."*".$cbo_material_source."*".$txt_attention."*".$txt_remarks."*1*".$cbo_level."*".$cbo_ready_to_approved."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'1'*'".$delivery_address."'*'".$location_id."'*'".str_replace("'", "", $cbo_payterm_id)."'*'".str_replace("'", "", $txt_tenor)."'";
		$rID=sql_update("wo_booking_mst",$field_array_up,$data_array_up,"booking_no","".$txt_booking_no."",0);

		if($db_type==0){
			if($rID){
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$txt_booking_no)."**".$booking_mst_id;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_booking_no)."**".$booking_mst_id;
			}
		}
		else if($db_type==2 || $db_type==1 ){
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
		if($db_type==0){
			mysql_query("BEGIN");
		}
		$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_booking_no");
		if($is_approved==1){
			echo "app1**".str_replace("'","",$txt_booking_no);
			die;
		}
		$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no  and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
		if($pi_number){
			echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
			die;
		}
		$recv_number=return_field_value( "recv_number", "inv_receive_master a,inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no  and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
		if($recv_number){
			echo "recv1**".str_replace("'","",$txt_booking_no)."**".$recv_number;
			die;
		}

		$delete_cause=str_replace("'","",$delete_cause);
		$delete_cause=str_replace('"','',$delete_cause);
		$delete_cause=str_replace('(','',$delete_cause);
		$delete_cause=str_replace(')','',$delete_cause);

		//$rID=execute_query( "delete from wo_booking_mst where  booking_no =".$txt_booking_no."",0);
		//$rID1=execute_query( "delete from wo_booking_dtls where  booking_no =".$txt_booking_no."",0);
		//$rID2=execute_query( "delete from wo_trim_book_con_dtls where  booking_no =".$txt_booking_no."",0);

		$rID=execute_query("update wo_booking_mst set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where booking_no=$txt_booking_no",0);
		$rID1=execute_query("update wo_booking_dtls set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where booking_no=$txt_booking_no",0);
		$rID1=execute_query("update wo_trim_book_con_dtls set status_active=0,is_deleted=1 where  booking_no=$txt_booking_no",0);

		if($db_type==0){
			if($rID){
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		else if($db_type==2 || $db_type==1 ){
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

if ($action=="save_update_delete_dtls"){
	$color_library=return_library_array("select id, color_name from lib_color", "id", "color_name");
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$booking_mst_id=str_replace("'","",$booking_mst_id);
	$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_booking_no");
	if($is_approved==1){
		echo "app1**".str_replace("'","",$txt_booking_no);
		disconnect($con);	die;
	}

	$exeed_budge_qty=0; $exeed_budge_amount=0; $amount_exceed_level=0;
	$data_array=sql_select("select exeed_budge_qty, exeed_budge_amount, amount_exceed_level from variable_order_tracking where company_name='$cbo_company_name' and item_category_id=4 and variable_list=26 and status_active=1 and is_deleted=0");
	foreach ($data_array as $row){
		$exeed_budge_qty=$row[csf("exeed_budge_qty")];
		$exeed_budge_amount=$row[csf("exeed_budge_amount")];
		$amount_exceed_level=$row[csf("amount_exceed_level")];
	}
 
	if ($operation==0){
		$curr_book_amount_job_level=array(); $curr_book_amount_job_item_level=array(); $jobArr=array(); $poArr=array(); $pre_trim_id_arr=array(); $des_data=""; $brand_data="";
		for ($i=1;$i<=$total_row;$i++){
			$txttrimcostid="txttrimcostid_".$i;
			$txtReqAmt="txtReqAmt_".$i;
			$txtpoid="txtpoid_".$i;
			$txttrimgroup="txttrimgroup_".$i;
			$txtwoq="txtwoq_".$i;
			$txtexchrate="txtexchrate_".$i;
			$txtrate="txtrate_".$i;
			$txtamount="txtamount_".$i;
			$txtbookingid="txtbookingid_".$i;
			$txtjob_id="txtjob_".$i;
			$txtreqqnty="txtreqqnty_".$i;
			$txtdesc="txtdesc_".$i;
		    $txtbrandsup="txtbrandsup_".$i;
			$txtreqamount="txtreqamount_".$i;
			$txtreqamountjoblevelconsuom="txtreqamountjoblevelconsuom_".$i;
			$txtreqamountitemlevelconsuom="txtreqamountitemlevelconsuom_".$i;

			$pretrimcostid=str_replace("'","",$$txttrimcostid);
			$poid=str_replace("'","",$$txtpoid);

			$curr_book_amount_job_level[$poid]['req_amount']=str_replace("'","",$$txtreqamountjoblevelconsuom);
			$curr_book_amount_job_level[$poid]['cur_amount']+=(str_replace("'","",$$txtamount)/str_replace("'","",$$txtexchrate));
			$curr_book_amount_job_level[$poid]['prev_amount']=0;

			$curr_book_amount_job_item_level[$poid][str_replace("'","",$$txttrimcostid)]['req_amount']+=str_replace("'","",$$txtreqamountitemlevelconsuom);
			$curr_book_amount_job_item_level[$poid][str_replace("'","",$$txttrimcostid)]['cur_amount']+=(str_replace("'","",$$txtamount)/str_replace("'","",$$txtexchrate));
			$curr_book_amount_job_item_level[$poid][str_replace("'","",$$txttrimcostid)]['prev_amount']=0;
			$jobArr[$$txtjob_id]=$$txtjob_id;
			$poArr[$poid]=$poid;
			$pre_trim_id_arr[$pretrimcostid]=$pretrimcostid;
			$des_data=$$txtdesc;
			$brand_data=$$txtbrandsup;
		}
		//echo "vad1**";
		//print_r($curr_book_amount_job_item_level);
		$prev_book_amount_job_level=array();
		$prev_book_amount_job_item_level=array();
		$sql=sql_select("select b.job_no, b.po_break_down_id, b.pre_cost_fabric_cost_dtls_id, b.trim_group,b.wo_qnty,b.amount,b.exchange_rate from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and b.job_no in(".implode(",",$jobArr).") and b.booking_type=2 and b.is_short=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach($sql as $row){
			$curr_book_amount_job_level[$row[csf('po_break_down_id')]]['prev_amount']+=($row[csf('amount')]/$row[csf('exchange_rate')]);
			$curr_book_amount_job_item_level[$row[csf('po_break_down_id')]][$row[csf('pre_cost_fabric_cost_dtls_id')]]['prev_amount']+=($row[csf('amount')]/$row[csf('exchange_rate')]);
			$curr_book_qty_job_item_level[$row[csf('po_break_down_id')]][$row[csf('pre_cost_fabric_cost_dtls_id')]]['prev_qty']+=($row[csf('wo_qnty')]*$sql_lib_item_group_array[$row[csf('trim_group')]]['conversion_factor']);
		}
		$con = connect();
		if($db_type==0){
			mysql_query("BEGIN");
		}
		if( check_table_status( $_SESSION['menu_id'], 1 )==0 ){
			echo "15**0";
			disconnect($con);die;
		}

		if($des_data!="") $des_data_cond="and description=$des_data"; else $des_data_cond="";
		if($brand_data!="") $brand_data_cond="and brand_supplier=$brand_data"; else $brand_data_cond="";

		if (is_duplicate_field( "booking_no", "wo_booking_dtls", "job_no in(".implode(",",$jobArr).") and po_break_down_id in (".implode(",",$poArr).") and booking_type=2 and is_short=2 and booking_no=$txt_booking_no and status_active=1 and is_deleted=0 $des_data_cond $brand_data_cond") == 1)
		{
			echo "11**0";
			check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);die;
		}

		$id_dtls=return_next_id( "id", "wo_booking_dtls", 1 ) ;
		$field_array1="id, pre_cost_fabric_cost_dtls_id, pre_req_amt, po_break_down_id, job_no, booking_no, booking_mst_id, booking_type, is_short, trim_group,description,brand_supplier, uom, sensitivity, wo_qnty, exchange_rate, rate, amount, delivery_date, country_id_string, inserted_by, insert_date, status_active, is_deleted";

		$field_array2="id, wo_trim_booking_dtls_id, booking_no, booking_mst_id, job_no, po_break_down_id, color_number_id, gmts_sizes, description, brand_supplier, item_color, item_size, cons, process_loss_percent, requirment, rate, amount, pcs, color_size_table_id, article_number, moq, pp_sample, zipper_break_down, status_active, is_deleted";

		$add_comma=0;
		$id1=return_next_id( "id", "wo_trim_book_con_dtls", 1 );
		$new_array_color=array();
		for ($i=1;$i<=$total_row;$i++){
			$txttrimcostid="txttrimcostid_".$i;
			$txtReqAmt="txtReqAmt_".$i;
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

			$txtdesc="txtdesc_".$i;
			$txtbrandsup="txtbrandsup_".$i;
			$txtreqamount="txtreqamount_".$i;
			$txtreqamountjoblevelconsuom="txtreqamountjoblevelconsuom_".$i;
			$txtreqamountitemlevelconsuom="txtreqamountitemlevelconsuom_".$i;

			$job=str_replace("'","",$$txtjob_id);
			$po_id=str_replace("'","",$$txtpoid);
			$trimcostid=str_replace("'","",$$txttrimcostid);
			$amt=str_replace("'","",$$txtamount);
			$exRate=str_replace("'","",$$txtexchrate);
			$uom_id=str_replace("'","",$$txtuom);
			//==============================
			if($amount_exceed_level==1){
				$reqAmt=(($curr_book_amount_job_level[$po_id]['req_amount']*$exeed_budge_amount)/100)+$curr_book_amount_job_level[$po_id]['req_amount'];
				$curAmt=$curr_book_amount_job_level[$po_id]['prev_amount']+($amt/$exRate);
				if(round($curAmt)>round($reqAmt)){
					echo "vad1**".str_replace("'","",$txt_booking_no)."**".$i;
					check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);die;
				}
				$curr_book_amount_job_level[$po_id]['prev_amount']+=($amt/$exRate);
			}
			if($amount_exceed_level==2){
				$reqAmt=(($curr_book_amount_job_item_level[$po_id][$trimcostid]['req_amount']*$exeed_budge_amount)/100)+$curr_book_amount_job_item_level[$po_id][$trimcostid]['req_amount'];
				$curAmt=$curr_book_amount_job_item_level[$po_id][$trimcostid]['prev_amount']+($amt/$exRate);
				$curAmt=number_format($curAmt,3,'.','');
				$reqAmt=number_format($reqAmt,3,'.','');
				
				if(round($curAmt)>round($reqAmt)){
					echo "vad1**".str_replace("'","",$txt_booking_no)."**".$i."**".$curAmt."**".$reqAmt."**".$curr_book_amount_job_item_level[$po_id][$trimcostid]['req_amount'];
					check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);die;
				}
				$curr_book_amount_job_item_level[$po_id][$trimcostid]['prev_amount']+=($amt/$exRate);
			}
			if($exceed_qty_level==2){
				$reqQty=(($curr_book_qty_job_item_level[$po_id][$trimcostid]['req_qty']*$exeed_budge_qty)/100)+$curr_book_qty_job_item_level[$po_id][$trimcostid]['req_qty'];
				$curQty=$curr_book_qty_job_item_level[$po_id][$trimcostid]['prev_qty']+($woq*$conversion_factor);
				if(($curQty-$reqQty)>1){
					echo "vad2**".str_replace("'","",$txt_booking_no)."**".$i."**".$curQty."**".$reqQty."**".$curr_book_qty_job_item_level[$po_id][$trimcostid]['req_qty'];
					check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);die;
				}
				$curr_book_qty_job_item_level[$po_id][$trimcostid]['prev_qty']+=($woq*$conversion_factor);
			}
			//===========================

			$data_array1 ="(".$id_dtls.",".$$txttrimcostid.",".$$txtReqAmt.",".$$txtpoid.",".$$txtjob_id.",".$txt_booking_no.",".$booking_mst_id.",2,2,".$$txttrimgroup.",".$$txtdesc.",".trim($$txtbrandsup).",".$$txtuom.",".$$cbocolorsizesensitive.",".$$txtwoq.",".$$txtexchrate.",".$$txtrate.",".$$txtamount.",".$$txtddate.",".$$txtcountry.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";

			//	CONS break down===============================================================================================
			if(str_replace("'",'',$$consbreckdown) !=''){
				$data_array2="";
				$rID_de1=execute_query( "delete from wo_trim_book_con_dtls where  wo_trim_booking_dtls_id =".$$txtbookingid."",0);
				$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
				for($c=0;$c < count($consbreckdown_array);$c++){
					$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
					if(str_replace("'","",$consbreckdownarr[4])=='0') $consbreckdownarr[4]='';
					if(str_replace("'","",$consbreckdownarr[4]) !="")
					{
					    if (!in_array(str_replace("'","",$consbreckdownarr[4]),$new_array_color, TRUE))
					    {
					        $color_id = return_id( str_replace("'","",$consbreckdownarr[4]), $color_library, "lib_color", "id,color_name","272");
					        $new_array_color[$color_id]=str_replace("'","",$consbreckdownarr[4]);
					    }
					    else $color_id =  array_search(str_replace("'","",$consbreckdownarr[4]), $new_array_color);
					}
					else $color_id=0;
					

					if ($c!=0) $data_array2 .=",";
					$data_array2 .="(".$id1.",".$id_dtls.",".$txt_booking_no.",".$booking_mst_id.",".$$txtjob_id.",".$$txtpoid.",'".$consbreckdownarr[0]."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".trim($consbreckdownarr[3])."','".$color_id."','".$consbreckdownarr[5]."','".$consbreckdownarr[6]."','".$consbreckdownarr[7]."','".$consbreckdownarr[8]."','".$consbreckdownarr[9]."','".$consbreckdownarr[10]."','".$consbreckdownarr[11]."','".$consbreckdownarr[12]."','".$consbreckdownarr[14]."','".$consbreckdownarr[15]."','".$consbreckdownarr[16]."','".$consbreckdownarr[17]."',1,0)";
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
		$curr_book_amount_job_level=array();
		$curr_book_amount_job_item_level=array();
		$jobArr=array(); $poArr=array(); $pre_trim_id_arr=array(); $des_data=""; $brand_data="";
		for ($i=1;$i<=$total_row;$i++){
			$txttrimcostid="txttrimcostid_".$i;
			$txtReqAmt="txtReqAmt_".$i;
			$txtpoid="txtpoid_".$i;
			$txttrimgroup="txttrimgroup_".$i;
			//$txtuom="txtuom_".$i;
			//$cbocolorsizesensitive="cbocolorsizesensitive_".$i;
			$txtwoq="txtwoq_".$i;
			$txtexchrate="txtexchrate_".$i;
			$txtrate="txtrate_".$i;
			$txtamount="txtamount_".$i;
			//$txtddate="txtddate_".$i;
			//$consbreckdown="consbreckdown_".$i;
			$txtbookingid="txtbookingid_".$i;
			//$txtcountry="txtcountry_".$i;
			$txtjob_id="txtjob_".$i;
			$txtreqqnty="txtreqqnty_".$i;
			//$jsondata="jsondata_".$i;
			$txtdesc="txtdesc_".$i;
		    $txtbrandsup="txtbrandsup_".$i;
			$txtreqamount="txtreqamount_".$i;
			$txtreqamountjoblevelconsuom="txtreqamountjoblevelconsuom_".$i;
			$txtreqamountitemlevelconsuom="txtreqamountitemlevelconsuom_".$i;

			$pretrimcostid=str_replace("'","",$$txttrimcostid);
			$poid=str_replace("'","",$$txtpoid);

			$curr_book_amount_job_level[$poid]['req_amount']=str_replace("'","",$$txtreqamountjoblevelconsuom);
			$curr_book_amount_job_level[$poid]['cur_amount']+=(str_replace("'","",$$txtamount)/str_replace("'","",$$txtexchrate));
			$curr_book_amount_job_level[$poid]['prev_amount']=0;

			$curr_book_amount_job_item_level[$poid][str_replace("'","",$$txttrimcostid)]['req_amount']+=str_replace("'","",$$txtreqamountitemlevelconsuom);
			$curr_book_amount_job_item_level[$poid][str_replace("'","",$$txttrimcostid)]['cur_amount']+=(str_replace("'","",$$txtamount)/str_replace("'","",$$txtexchrate));
			$curr_book_amount_job_item_level[$poid][str_replace("'","",$$txttrimcostid)]['prev_amount']=0;
			$jobArr[$$txtjob_id]=$$txtjob_id;
			$poArr[$poid]=$poid;
			$pre_trim_id_arr[$pretrimcostid]=$pretrimcostid;
			$des_data=$$txtdesc;
			$brand_data=$$txtbrandsup;
		}

		$prev_book_amount_job_level=array();
		$prev_book_amount_job_item_level=array();
		//$sql=sql_select("select  id, job_no, po_break_down_id, pre_cost_fabric_cost_dtls_id, trim_group, wo_qnty, amount, exchange_rate from wo_booking_dtls where job_no in(".implode(",",$jobArr).") and booking_type=2 and is_short=2 and status_active=1 and is_deleted=0");// and po_break_down_id in($poid)
			$sql=sql_select("select b.id,b.job_no, b.po_break_down_id, b.pre_cost_fabric_cost_dtls_id, b.trim_group,b.wo_qnty,b.amount,b.exchange_rate from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and b.job_no in(".implode(",",$jobArr).") and b.booking_type=2 and b.is_short=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach($sql as $row){
			$curr_book_amount_job_level[$row[csf('po_break_down_id')]]['prev_amount']+=($row[csf('amount')]/$row[csf('exchange_rate')]);

			$prev_book_amount_job_level[$row[csf('id')]]['prev_amount']=($row[csf('amount')]/$row[csf('exchange_rate')]);
			$prev_book_amount_job_level[$row[csf('id')]]['prev_qty']=($row[csf('wo_qnty')]*$sql_lib_item_group_array[$row[csf('trim_group')]]['conversion_factor']);

			$curr_book_amount_job_item_level[$row[csf('po_break_down_id')]][$row[csf('pre_cost_fabric_cost_dtls_id')]]['prev_amount']+=($row[csf('amount')]/$row[csf('exchange_rate')]);
		    $curr_book_qty_job_item_level[$row[csf('po_break_down_id')]][$row[csf('pre_cost_fabric_cost_dtls_id')]]['prev_qty']+=($row[csf('wo_qnty')]*$sql_lib_item_group_array[$row[csf('trim_group')]]['conversion_factor']);
		}

	$con = connect();
	if($db_type==0){
	mysql_query("BEGIN");
	}
	if( check_table_status( $_SESSION['menu_id'], 1 )==0 ){
		echo "15**1";
		disconnect($con);die;
	}

	if($des_data!="") $des_data_cond="and description=$des_data"; else $des_data_cond="";
	if($brand_data!="") $brand_data_cond="and brand_supplier=$brand_data"; else $brand_data_cond="";
	if (is_duplicate_field( "booking_no", "wo_booking_dtls", "job_no in(".implode(",",$jobArr).") and po_break_down_id in (".implode(",",$poArr).") and booking_type=2 and is_short=2 and booking_no=$txt_booking_no and status_active=1 and is_deleted=0 $des_data_cond $brand_data_cond") == 1)
	{
		echo "11**0";
		check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);die;
	}
	$field_array_up1="pre_cost_fabric_cost_dtls_id*pre_req_amt*po_break_down_id*job_no*booking_no*trim_group*description*brand_supplier*uom*sensitivity*wo_qnty*exchange_rate*rate*amount*delivery_date*country_id_string*updated_by*update_date";
	$field_array_up2="id, wo_trim_booking_dtls_id, booking_no, booking_mst_id, job_no, po_break_down_id, color_number_id, gmts_sizes, description, brand_supplier, item_color, item_size, cons, process_loss_percent, requirment, rate, amount, pcs, color_size_table_id, article_number, moq, pp_sample, zipper_break_down, status_active, is_deleted";

	$add_comma=0;
	$id1=return_next_id( "id", "wo_trim_book_con_dtls", 1 );
	$new_array_color=array();
	for ($i=1;$i<=$total_row;$i++){
		$txttrimcostid="txttrimcostid_".$i;
		$txtReqAmt="txtReqAmt_".$i;
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

		$txtdesc="txtdesc_".$i;
		$txtbrandsup="txtbrandsup_".$i;
		$txtreqamount="txtreqamount_".$i;
		$txtreqamountjoblevelconsuom="txtreqamountjoblevelconsuom_".$i;
		$txtreqamountitemlevelconsuom="txtreqamountitemlevelconsuom_".$i;

			$pi_number=array(); $piquantity=0;
			$sqlPi=sql_select("select a.pi_number, b.quantity from  com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id  and b.work_order_no=$txt_booking_no and b.item_group=".$$txttrimgroup." and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			foreach($sqlPi as $rowPi){
				$pi_number[$rowPi[csf('pi_number')]]=$rowPi[csf('pi_number')];
				$piquantity+=$rowPi[csf('quantity')];
			}
			$uom_id=str_replace("'","",$$txtuom);
		    if($piquantity && str_replace("'","",$$txtwoq) < $piquantity){
			    echo "pi1**".str_replace("'","",$txt_booking_no)."**".implode(",",$pi_number)."**".$piquantity;
				check_table_status( $_SESSION['menu_id'],0);
			   disconnect($con); die;
		    }

			$recv_number=array(); $recvquantity=0;
			$sqlRecv=sql_select("select a.recv_number, b.receive_qnty from  inv_receive_master a,inv_trims_entry_dtls b where a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and b.item_group_id=".$$txttrimgroup." and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			foreach($sqlRecv as $rowRecv){
				$recv_number[$rowRecv[csf('recv_number')]]=$rowRecv[csf('recv_number')];
				$recvquantity+=$rowRecv[csf('receive_qnty')];
			}
		    if($recvquantity && str_replace("'","",$$txtwoq) < $recvquantity){
			    echo "recv1**".str_replace("'","",$txt_booking_no)."**".implode(",",$recv_number)."**".$recvquantity;
				check_table_status( $_SESSION['menu_id'],0);
			   disconnect($con); die;
		    }
			$job=str_replace("'","",$$txtjob_id);
			$po_id=str_replace("'","",$$txtpoid);
			$trimcostid=str_replace("'","",$$txttrimcostid);
			$amt=str_replace("'","",$$txtamount);
			$exRate=str_replace("'","",$$txtexchrate);
			//==============================
			if($amount_exceed_level==1){
				$reqAmt=(($curr_book_amount_job_level[$po_id]['req_amount']*$exeed_budge_amount)/100)+$curr_book_amount_job_level[$po_id]['req_amount'];

				$pre_amt=0;
				$ex_book_dtls_id=explode(",",str_replace("'",'',$$txtbookingid));
				foreach($ex_book_dtls_id as $book_dtls_id)
				{
					$pre_amt+=$prev_book_amount_job_level[$book_dtls_id]['prev_amount'];
				}
				$curAmt=($curr_book_amount_job_level[$po_id]['prev_amount']-$pre_amt)+($amt/$exRate);

				//$curAmt=$curr_book_amount_job_level[$po_id]['prev_amount']+($amt/$exRate);
				if(($curAmt-$reqAmt)>1){
					echo "vad1**".str_replace("'","",$txt_booking_no)."**".$i;
					check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);die;
				}
				$curr_book_amount_job_level[$po_id]['prev_amount']+=($amt/$exRate);
			}
			if($amount_exceed_level==2){
				$reqAmt=(($curr_book_amount_job_item_level[$po_id][$trimcostid]['req_amount']*$exeed_budge_amount)/100)+$curr_book_amount_job_item_level[$po_id][$trimcostid]['req_amount'];
				$pre_amt=0;
				$ex_book_dtls_id=explode(",",str_replace("'",'',$$txtbookingid));
				foreach($ex_book_dtls_id as $book_dtls_id)
				{
					$pre_amt+=$prev_book_amount_job_level[$book_dtls_id]['prev_amount'];
				}

				$curAmt=($curr_book_amount_job_item_level[$po_id][$trimcostid]['prev_amount']-$pre_amt)+($amt/$exRate);
				$curAmt=number_format($curAmt,3,'.','');
				$reqAmt=number_format($reqAmt,3,'.','');
				if(($curAmt-$reqAmt)>1){
					echo "vad1**".str_replace("'","",$txt_booking_no)."**".$i."**".$curAmt."**".$reqAmt."**".$curr_book_amount_job_item_level[$po_id][$trimcostid]['req_amount'];
					check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);die;
				}
				$curr_book_amount_job_item_level[$po_id][$trimcostid]['prev_amount']+=($amt/$exRate);
			}

			if($exceed_qty_level==2){
				$reqQty=(($curr_book_qty_job_item_level[$po_id][$trimcostid]['req_qty']*$exeed_budge_qty)/100)+$curr_book_qty_job_item_level[$po_id][$trimcostid]['req_qty'];
				$curQty=$curr_book_qty_job_item_level[$po_id][$trimcostid]['prev_qty']+($woq*$conversion_factor);
				if(($curQty-$reqQty)>1){
					echo "vad2**".str_replace("'","",$txt_booking_no)."**".$i."**".$curQty."**".$reqQty."**".$curr_book_amount_job_item_level[$po_id][$trimcostid]['req_qty'];
					check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);die;
				}
				$curr_book_qty_job_item_level[$po_id][$trimcostid]['prev_qty']+=($woq*$conversion_factor);
			}
			//===========================

		if(str_replace("'",'',$$txtbookingid)!=""){
			$id_arr=array();
			$data_array_up1=array();
			$id_arr[]=str_replace("'",'',$$txtbookingid);
			$data_array_up1[str_replace("'",'',$$txtbookingid)] =explode("*",("".$$txttrimcostid."*".$$txtReqAmt."*".$$txtpoid."*".$$txtjob_id."*".$txt_booking_no."*".$$txttrimgroup."*".$$txtdesc."*".trim($$txtbrandsup)."*".$$txtuom."*".$$cbocolorsizesensitive."*".$$txtwoq."*".$$txtexchrate."*".$$txtrate."*".$$txtamount."*".$$txtddate."*".$$txtcountry."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

			//	CONS break down===============================================================================================
			if(str_replace("'",'',$$consbreckdown) !=''){
				$data_array_up2="";
				$rID_de1=execute_query( "delete from wo_trim_book_con_dtls where  wo_trim_booking_dtls_id =".$$txtbookingid."",0);
				$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
				for($c=0;$c < count($consbreckdown_array);$c++){
					$consbreckdownarr=explode('_',$consbreckdown_array[$c]);

					/*if (!in_array(str_replace("'","",$consbreckdownarr[4]),$new_array_color)){
						$color_id = return_id( str_replace("'","",$consbreckdownarr[4]), $color_library, "lib_color", "id,color_name");
						$new_array_color[$color_id]=str_replace("'","",$consbreckdownarr[4]);
					}
					else{
						$color_id =  array_search(str_replace("'","",$consbreckdownarr[4]), $new_array_color);
					}*/
					if(str_replace("'","",$consbreckdownarr[4]) =='0') $consbreckdownarr[4]='';
					if(str_replace("'","",$consbreckdownarr[4]) !="")
					{
					    if (!in_array(str_replace("'","",$consbreckdownarr[4]),$new_array_color, TRUE))
					    {
					        $color_id = return_id( str_replace("'","",$consbreckdownarr[4]), $color_library, "lib_color", "id,color_name","272");
					        $new_array_color[$color_id]=str_replace("'","",$consbreckdownarr[4]);
					    }
					    else $color_id =  array_search(str_replace("'","",$consbreckdownarr[4]), $new_array_color);
					}
					else
					{
					    $color_id=0;
					}

					if ($c!=0) $data_array_up2 .=",";
					$data_array_up2 .="(".$id1.",".$$txtbookingid.",".$txt_booking_no.",".$booking_mst_id.",".$$txtjob_id.",".$$txtpoid.",'".$consbreckdownarr[0]."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".trim($consbreckdownarr[3])."','".$color_id."','".$consbreckdownarr[5]."','".$consbreckdownarr[6]."','".$consbreckdownarr[7]."','".$consbreckdownarr[8]."','".$consbreckdownarr[9]."','".$consbreckdownarr[10]."','".$consbreckdownarr[11]."','".$consbreckdownarr[12]."','".$consbreckdownarr[14]."','".$consbreckdownarr[15]."','".$consbreckdownarr[16]."','".$consbreckdownarr[17]."',1,0)";
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
				   disconnect($con); die;
			    }
			//}else{
				$recv_number=return_field_value( "recv_number", "inv_receive_master a,inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and b.item_group_id=".$$txttrimgroup." and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			    if($recv_number){
				    echo "recv1**".str_replace("'","",$txt_booking_no)."**".$recv_number;
				   disconnect($con); die;
			    }
			//}
			$delete_cause=str_replace("'","",$delete_cause);
			$delete_cause=str_replace('"','',$delete_cause);
			$delete_cause=str_replace('(','',$delete_cause);
			$delete_cause=str_replace(')','',$delete_cause);

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

if ($action=="save_update_delete_dtls_job_level"){
	$color_library=return_library_array("select id, color_name from lib_color", "id", "color_name");
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$booking_mst_id=str_replace("'","",$booking_mst_id);
	$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_booking_no");
	if($is_approved==1){
			echo "app1**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;
	}

	$sql_lib_item_group_array=array();
	$sql_lib_item_group=sql_select("select id, item_name,conversion_factor,order_uom as cons_uom from lib_item_group");
	foreach($sql_lib_item_group as $row_sql_lib_item_group){
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]]['conversion_factor']=$row_sql_lib_item_group[csf('conversion_factor')];
	}
	//	$uom_id_arr=array(1=>'Pcs',31=>'Set',50=>'Roll',51=>'Coil',52=>'Cone',53=>'Bag',54=>'Box',55=>'Drum',56=>'Bottle',57=>'Pack',59=>'Can',62=>'Lachi',65=>'Packet',66=>'Pol',67=>'Book',74=>'Bundle',78=>'Cylinder',80=>'Sheet');
	//$uom_id_arr=array(1,31,50,51,52,53,54,55,56,57,59,62,65,66,67,74,78,80);// For Round Down

	$exeed_budge_qty=0;
	$exeed_budge_amount=0;
	$amount_exceed_level=0;
	$exceed_qty_level=0;
	$data_array=sql_select("select exeed_budge_qty,exeed_budge_amount,amount_exceed_level,exceed_qty_level from variable_order_tracking where company_name='$cbo_company_name' and item_category_id=4 and variable_list=26 and status_active=1 and is_deleted=0");
	foreach ($data_array as $row){
		$exeed_budge_qty=$row[csf("exeed_budge_qty")];
		$exeed_budge_amount=$row[csf("exeed_budge_amount")];
		$amount_exceed_level=$row[csf("amount_exceed_level")];
		$exceed_qty_level = $row[csf("exceed_qty_level")];
	}
	$sql_vari_lib="select item_category_id,variable_list,excut_source  from variable_order_tracking where company_name=".$cbo_company_name." and item_category_id=4  and variable_list=72 and status_active=1"; 
	 
	$result_vari_lib=sql_select($sql_vari_lib);
	$source_from=1;//$woven_category_id=0;
	foreach($result_vari_lib as $row)
	{
		if($row[csf('excut_source')]>0)
		{
		$source_from=$row[csf('excut_source')];
		}
	}
	
	$strdata=json_decode(str_replace("'","",$strdata));
	if ($operation==0)
	{
		$curr_book_amount_job_level=array();
		$curr_book_amount_job_item_level=array();
		$jobArr=array(); $poArr=array(); $pre_trim_id_arr=array(); $des_arr=array(); $brand_arr=array();

		for ($i=1;$i<=$total_row;$i++){
			$txttrimcostid="txttrimcostid_".$i;
			$txtReqAmt="txtReqAmt_".$i;
			$txtpoid="txtpoid_".$i;
			$txttrimgroup="txttrimgroup_".$i;
			$hiddlabeldtlsdata="hiddlabeldtlsdata_".$i;
			$txtwoq="txtwoq_".$i;
			$txtexchrate="txtexchrate_".$i;
			$txtrate="txtrate_".$i;
			$txtamount="txtamount_".$i;
			$txtbookingid="txtbookingid_".$i;
			$txtjob_id="txtjob_".$i;
			$txtreqqnty="txtreqqnty_".$i;
			$txtdesc="txtdesc_".$i;
		    $txtbrandsup="txtbrandsup_".$i;
			$txtreqamount="txtreqamount_".$i;
			$txtreqamountjoblevelconsuom="txtreqamountjoblevelconsuom_".$i;
			$txtreqamountitemlevelconsuom="txtreqamountitemlevelconsuom_".$i;
				//=====================
			$JoBc=$$txtjob_id;
			$condition= new condition();
			if(str_replace("'","",$$txtjob_id) !=''){
				$condition->job_no("=$JoBc");
			}

			$condition->init();
			$trims= new trims($condition);
			//$req_qty_arr=$trims->getQtyArray_by_orderAndPrecostdtlsid();
			//$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsid();
			if($source_from==2) //Sourcing from Lib
			{
				$reqAmountJobLevelArr=$trims->getAmountArray_by_jobSourcing();
  				$reqQtyItemLevelArr=$trims->getQtyArray_by_precostdtlsid();
				$reqAmountItemLevelArr=$trims->getAmountArray_precostdtlsidSourcing();
			}
			else
			{
				$reqAmountJobLevelArr=$trims->getAmountArray_by_job();
  				$reqQtyItemLevelArr=$trims->getQtyArray_by_precostdtlsid();
				$reqAmountItemLevelArr=$trims->getAmountArray_precostdtlsid();
			}
			
			//echo $trims->getQuery(); die;
			/*echo '<pre>';
			print_r($reqAmountJobLevelArr); die;*/
			//====================
			$pretrimcostid=str_replace("'","",$$txttrimcostid);
			$poid=str_replace("'","",$$txtpoid);

			$curr_book_qty_job_item_level[str_replace("'","",$$txtjob_id)][$pretrimcostid]['req_qty']+=$reqQtyItemLevelArr[$pretrimcostid];
			$curr_book_qty_job_item_level[str_replace("'","",$$txtjob_id)][$pretrimcostid]['cur_qty']+=(str_replace("'","",$$txtreqqnty)*$conversion_factor);
			$curr_book_qty_job_item_level[str_replace("'","",$$txtjob_id)][$pretrimcostid]['prev_qty']=0;

			$curr_book_amount_job_level[str_replace("'","",$$txtjob_id)]['req_amount']=$reqAmountJobLevelArr[str_replace("'","",$$txtjob_id)];
			$curr_book_amount_job_level[str_replace("'","",$$txtjob_id)]['cur_amount']+=(str_replace("'","",$$txtamount)/str_replace("'","",$$txtexchrate));
			$curr_book_amount_job_level[str_replace("'","",$$txtjob_id)]['prev_amount']=0;

			$curr_book_amount_job_item_level[str_replace("'","",$$txtjob_id)][$pretrimcostid]['req_amount']+=$reqAmountItemLevelArr[$pretrimcostid];
			$curr_book_amount_job_item_level[str_replace("'","",$$txtjob_id)][$pretrimcostid]['cur_amount']+=(str_replace("'","",$$txtamount)/str_replace("'","",$$txtexchrate));
			$curr_book_amount_job_item_level[str_replace("'","",$$txtjob_id)][$pretrimcostid]['prev_amount']=0;

			$jobArr[$$txtjob_id]=$$txtjob_id;
			$poArr[$poid]=$poid;
			$pre_trim_id_arr[$pretrimcostid]=$pretrimcostid;
			if(str_replace("'","",$$txtdesc) != ''){
				$des_arr[$$txtdesc]=$$txtdesc;
			}
			if(str_replace("'","",$$txtbrandsup) != ''){
				$brand_arr[$$txtbrandsup]=$$txtbrandsup;
			}

		}
		$prev_book_amount_job_level=array();
		$prev_book_amount_job_item_level=array();
		//$sql=sql_select("select id, job_no, pre_cost_fabric_cost_dtls_id, trim_group, wo_qnty, amount, exchange_rate from wo_booking_dtls where job_no in(".implode(",",$jobArr).") and booking_type=2 and is_short=2 and status_active=1 and is_deleted=0"); //and booking_no !=$txt_booking_no
		$sql=sql_select("select b.id, b.job_no, b.pre_cost_fabric_cost_dtls_id, b.trim_group, b.wo_qnty, b.amount, b.exchange_rate from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and b.job_no in(".implode(",",$jobArr).") and b.booking_type=2 and b.is_short=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach($sql as $row){
			$curr_book_amount_job_level[$row[csf('job_no')]]['prev_amount']+=($row[csf('amount')]/$row[csf('exchange_rate')]);

			$prev_book_amount_job_level[$row[csf('id')]]['prev_amount']=($row[csf('amount')]/$row[csf('exchange_rate')]);
			$prev_book_amount_job_level[$row[csf('id')]]['prev_qty']=($row[csf('wo_qnty')]*$sql_lib_item_group_array[$row[csf('trim_group')]]['conversion_factor']);

			$curr_book_amount_job_item_level[$row[csf('job_no')]][$row[csf('pre_cost_fabric_cost_dtls_id')]]['prev_amount']+=($row[csf('amount')]/$row[csf('exchange_rate')]);

			$curr_book_qty_job_item_level[$row[csf('job_no')]][$row[csf('pre_cost_fabric_cost_dtls_id')]]['prev_qty']+=($row[csf('wo_qnty')]*$sql_lib_item_group_array[$row[csf('trim_group')]]['conversion_factor']);
		}

		$con = connect();
		if($db_type==0){
			mysql_query("BEGIN");
		}
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ){
			echo "15**0";
			disconnect($con);die;
		}

		if(count($des_arr)>0) $des_data_cond="and description in(".implode(",",$des_arr).")"; else $des_data_cond="";
		if(count($brand_arr)>0) $brand_data_cond="and brand_supplier in(".implode(",",$brand_arr).")"; else $brand_data_cond="";
		//echo "10**select booking_no from wo_booking_dtls where job_no in(".implode(",",$jobArr).") and po_break_down_id in (".implode(",",$poArr).") and pre_cost_fabric_cost_dtls_id in(".implode(",",$pre_trim_id_arr).") and booking_type=2 and is_short=2 and booking_no=$txt_booking_no and status_active=1 and is_deleted=0 $des_data_cond $brand_data_cond";check_table_status( $_SESSION['menu_id'],0); die;
		if (is_duplicate_field( "booking_no", "wo_booking_dtls", "job_no in(".implode(",",$jobArr).") and po_break_down_id in (".implode(",",$poArr).") and pre_cost_fabric_cost_dtls_id in(".implode(",",$pre_trim_id_arr).") and booking_type=2 and is_short=2 and booking_no=$txt_booking_no and status_active=1 and is_deleted=0 $des_data_cond $brand_data_cond") == 1)
		{
			check_table_status( $_SESSION['menu_id'],0);
			echo "dup";
			disconnect($con);die;
		}
		$id_dtls=return_next_id( "id", "wo_booking_dtls", 1);
		$field_array1="id, pre_cost_fabric_cost_dtls_id, pre_req_amt, po_break_down_id, job_no, booking_no, booking_mst_id, booking_type, is_short, trim_group, description, brand_supplier, uom, sensitivity, wo_qnty, exchange_rate, rate, amount, delivery_date, country_id_string, labeldtlsdata, inserted_by, insert_date, status_active, is_deleted";
		
		$field_array2="id, wo_trim_booking_dtls_id, booking_no, booking_mst_id, job_no, po_break_down_id, color_number_id, gmts_sizes, description, brand_supplier, item_color, item_size, cons, process_loss_percent, requirment, rate, amount, pcs, color_size_table_id, article_number, moq, pp_sample, zipper_break_down, status_active, is_deleted";
		$add_comma=0;
		$id1=return_next_id( "id", "wo_trim_book_con_dtls", 1 );
		$new_array_color=array(); //echo "10**";
		for ($i=1;$i<=$total_row;$i++){
			$txttrimcostid="txttrimcostid_".$i;
			$txtReqAmt="txtReqAmt_".$i;
			$txtpoid="txtpoid_".$i;
			$txttrimgroup="txttrimgroup_".$i;
			$hiddlabeldtlsdata="hiddlabeldtlsdata_".$i;
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
			$txtreqqnty="txtreqqnty_".$i;
			$jsondata="jsondata_".$i;

			$txtdesc="txtdesc_".$i;
		    $txtbrandsup="txtbrandsup_".$i;
			$txtreqamount="txtreqamount_".$i;
			$txtreqamountjoblevelconsuom="txtreqamountjoblevelconsuom_".$i;
			$txtreqamountitemlevelconsuom="txtreqamountitemlevelconsuom_".$i;

			$jsonarr=json_decode(str_replace("'","",$$jsondata));
			$uom_id=str_replace("'","",$$txtuom);
			$job=str_replace("'","",$$txtjob_id);
			$trimcostid=str_replace("'","",$$txttrimcostid);
			$reqqnty=str_replace("'","",$$txtreqqnty);
			$woq=str_replace("'","",$$txtwoq);
			$rate=str_replace("'","",$$txtrate);
			$amt=str_replace("'","",$$txtamount);
			$exRate=str_replace("'","",$$txtexchrate);
			$trim_group=str_replace("'","",$$txttrimgroup);

			$conversion_factor=$sql_lib_item_group_array[$trim_group]['conversion_factor'];

			//==============================
			$reqAmt=$curAmt=0;
			if($amount_exceed_level==1){
				$reqAmt=(($curr_book_amount_job_level[$job]['req_amount']*$exeed_budge_amount)/100)+$curr_book_amount_job_level[$job]['req_amount'];
				$curAmt=$curr_book_amount_job_level[$job]['prev_amount']+$curr_book_amount_job_level[$job]['cur_amount'];
				$reqAmut=$curAmut=0;
				$reqAmut=number_format($reqAmt,0,'.','');
				$curAmut=number_format($curAmt,0,'.','');
				if(($curAmt-$reqAmt)>1){
					echo "vad1**".str_replace("'","",$txt_booking_no)."**".$i."**".$curAmut."**".$reqAmut;
					check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);die;
				}
			}
			if($amount_exceed_level==2){
				$reqAmt=(($curr_book_amount_job_item_level[$job][$trimcostid]['req_amount']*$exeed_budge_amount)/100)+$curr_book_amount_job_item_level[$job][$trimcostid]['req_amount'];
				$curAmt=$curr_book_amount_job_item_level[$job][$trimcostid]['prev_amount']+$curr_book_amount_job_item_level[$job][$trimcostid]['cur_amount'];
					if(($curAmt-$reqAmt)>1){
					echo "vad1**".str_replace("'","",$txt_booking_no)."**".$i."**".$curAmt."**".$reqAmt."**".$curr_book_amount_job_item_level[$job][$trimcostid]['req_amount'];
					check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);die;
				}
			}

			if($exceed_qty_level==2){

				$reqQty=(($curr_book_qty_job_item_level[$job][$trimcostid]['req_qty']*$exeed_budge_qty)/100)+$curr_book_qty_job_item_level[$job][$trimcostid]['req_qty'];
				$curQty=$curr_book_qty_job_item_level[$job][$trimcostid]['prev_qty']+($woq*$conversion_factor);
					$tot_bal_qty=$curQty-$reqQty;
					if(($tot_bal_qty)>1){
					echo "vad2**".str_replace("'","",$txt_booking_no)."**".$i."**".$curQty."**".$reqQty."**".$curr_book_qty_job_item_level[$job][$trimcostid]['req_qty'];
					check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);die;
				}
				$curr_book_qty_job_item_level[$job][$trimcostid]['prev_qty']+=($woq*$conversion_factor);
			}
			//===========================
			$po_wise_wqqty=array(); $po_wise_total_wqqty=0;
			foreach($strdata->$job->$trimcostid->po_id as $poId){
				$wqQty=($strdata->$job->$trimcostid->req_qnty->$poId/$reqqnty)*$woq;
				$po_wise_wqqty[$poId]= number_format($wqQty,4,'.','');
				$po_wise_total_wqqty+=number_format($wqQty,4,'.','');
				if(str_replace("'",'',$$consbreckdown) !=''){
					$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
					for($c=0;$c < count($consbreckdown_array);$c++){
						$consbreckdownarr=array();
						$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
						$gmc=$consbreckdownarr[0];
						$gms=$consbreckdownarr[1];
						$art=$consbreckdownarr[14];
						if(str_replace("'","",$$cbocolorsizesensitive)==0){
							$bQty=($jsonarr->$trimcostid->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[6];
							$bwqQty=($jsonarr->$trimcostid->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[8];
							$nosencivity_qty[$trimcostid][$consbreckdownarr[2]][$poId]['bQty']=number_format($bQty,4,'.','');
							$nosencivity_qty[$trimcostid][$consbreckdownarr[2]][$poId]['bwqQty']=number_format($bwqQty,4,'.','');
							$nosencivity_qty[$trimcostid][$consbreckdownarr[2]][$poId]['breq']=$consbreckdownarr[6];
							$nosencivity_qty[$trimcostid][$consbreckdownarr[2]][$poId]['bwreq']=$consbreckdownarr[8];
						}
					}

				}
			}
			/* echo '10**<pre>';
			print_r($nosencivity_qty); die; */	
			foreach($nosencivity_qty as $trim_costid=>$trimdata){
				foreach($trimdata as $deccription=>$podata){
					foreach($podata as $poid=>$data)
					{
						$bQty_total[$trim_costid][$deccription]+=$data['bQty'];
						$bwqQty_total[$trim_costid][$deccription]+=$data['bwqQty'];
						$bqQty_req[$trim_costid][$deccription]=$data['breq'];
						$bwQty_req[$trim_costid][$deccription]=$data['bwreq'];
					}					
				}				
			}
			
			foreach($nosencivity_qty as $trimcost_id=>$desdata){
				foreach($desdata as $des=>$podata){
					//$last_po_id=array_key_last($nosencivity_qty[$trimcost_id][$des]);
					$keys = array_keys($nosencivity_qty[$trimcost_id][$des]);
					$last_po_id = end($keys);
					$trim_total=$bQty_total[$trimcost_id][$des];
					$trim_total_req=$bqQty_req[$trimcost_id][$des];
					//echo "10**".$trim_total.'--'.$trim_total_req.'--'.array_key_last($nosencivity_qty[$trimcostid][$des]); die;
					if($trim_total<$trim_total_req){
						$fraction_qty=number_format($trim_total_req-$trim_total,4,'.','');						
						$nosencivity_qty[$trimcost_id][$des][$last_po_id]['bQty']+=$fraction_qty;
					}
					else{
						$fraction_qty=number_format($trim_total-$trim_total_req,4,'.','');						
						$nosencivity_qty[$trimcost_id][$des][$last_po_id]['bQty']-=$fraction_qty;
					}
					$fraction_qtybwqQty=0;
					$trim_btotal=$bwqQty_total[$trimcost_id][$des];
					$trim_total_bwreq=$bwQty_req[$trimcost_id][$des];
					if($trim_btotal<$trim_total_bwreq){
						$fraction_qtybwqQty=number_format($trim_total_bwreq-$trim_btotal,4,'.','');						
						$nosencivity_qty[$trimcost_id][$des][$last_po_id]['bwqQty']+=$fraction_qtybwqQty;
					}
					else{
						$fraction_qtybwqQty=number_format($trim_btotal-$trim_total_bwreq,4,'.','');						
						$nosencivity_qty[$trimcost_id][$des][$last_po_id]['bwqQty']-=$fraction_qtybwqQty;
					}
				}
			}
			
			if($po_wise_total_wqqty<$woq){
				$fraction_qty=0;
				//$last_po_id_dtls=array_key_last($po_wise_wqqty);
				$keys = array_keys($po_wise_wqqty);
				$last_po_id_dtls = end($keys);
				$fraction_qty=$woq-$po_wise_total_wqqty;
				$po_wise_wqqty[$last_po_id_dtls]+=number_format($fraction_qty,4,'.','');
			}
			else{
				$fraction_qty=0;
				//$last_po_id_dtls=array_key_last($po_wise_wqqty);
				$keys = array_keys($po_wise_wqqty);
				$last_po_id_dtls = end($keys);
				$fraction_qty=$po_wise_total_wqqty-$woq;
				$po_wise_wqqty[$last_po_id_dtls]-=number_format($fraction_qty,4,'.','');
			}
			/* echo "10**<pre>";
			print_r($po_wise_wqqty); die; */
			//echo "10**";
			foreach($strdata->$job->$trimcostid->po_id as $poId){
				//$wqQty=($strdata->$job->$trimcostid->req_qnty->$poId/$reqqnty)*$woq;
				//$wqQty=number_format($wqQty,4,'.','');
				$wqQty=$po_wise_wqqty[$poId];
				$amount=$wqQty*$rate;
				$data_array1 ="(".$id_dtls.",".$$txttrimcostid.",".$$txtReqAmt.",".$poId.",".$$txtjob_id.",".$txt_booking_no.",".$booking_mst_id.",2,2,".$$txttrimgroup.",".$$txtdesc.",".trim($$txtbrandsup).",".$$txtuom.",".$$cbocolorsizesensitive.",".$wqQty.",".$$txtexchrate.",".$$txtrate.",".$amount.",".$$txtddate.",".$$txtcountry.",".$$hiddlabeldtlsdata.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				//echo "10**INSERT INTO wo_booking_dtls ($field_array1) values $data_array1"; echo "<br>";
				$rID1=sql_insert("wo_booking_dtls",$field_array1,$data_array1,0);

				//	CONS break down===============================================================================================
				if(str_replace("'",'',$$consbreckdown) !=''){
					$rID_de1=execute_query( "delete from wo_trim_book_con_dtls where  wo_trim_booking_dtls_id =".$$txtbookingid."",0);
					$consbreckdown_array=array();
					$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
					$d=0;
					for($c=0;$c < count($consbreckdown_array);$c++){
						$consbreckdownarr=array();
						$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
						$consbreckdownarr[4]=trim(str_replace("'",'',$consbreckdownarr[4]));
						if(str_replace("'","",$consbreckdownarr[4])=='0') $consbreckdownarr[4]='';
						if(str_replace("'","",$consbreckdownarr[4]) !="")
						{
						    if (!in_array(str_replace("'","",$consbreckdownarr[4]),$new_array_color, TRUE))
						    {
						        $color_id = return_id( str_replace("'","",$consbreckdownarr[4]), $color_library, "lib_color", "id,color_name","272");
						        $new_array_color[$color_id]=str_replace("'","",$consbreckdownarr[4]);
						    }
						    else $color_id =  array_search(str_replace("'","",$consbreckdownarr[4]), $new_array_color);
						}
						else $color_id=0;
						
						//echo $consbreckdownarr[4]."=".$color_id."=".$itemsize.'<br>';

						$gmc=$consbreckdownarr[0];
						$gms=$consbreckdownarr[1];
						//if($color_id==$gmc) $itemcolor=$gmc; else 
						$itemcolor=$color_id;
						$itemsize=str_replace("'", "", $consbreckdownarr[5]);
						$art=$consbreckdownarr[14];
						
						$colorSizeTableId=$jsonarr->$trimcostid->$gmc->$gms->$art;
						//echo '10**<pre>'.print_r($colorSizeTableId); check_table_status( $_SESSION['menu_id'],0); die;
						
						if(str_replace("'","",$$cbocolorsizesensitive)==1 || str_replace("'","",$$cbocolorsizesensitive)==3){
							$bQty=($jsonarr->$trimcostid->$gmc->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[6];
							$bwqQty=($jsonarr->$trimcostid->$gmc->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[8];
							//echo "10**".$bQty.'--'.$bwqQty.'--'.$jsonarr->$trimcostid->$gmc->req_qty->$poId.'-'.$consbreckdownarr[13].'-'.$consbreckdownarr[6].'--'.$gmc.'--'.$trimcostid; die;
							$order_qty=$jsonarr->$trimcostid->$gmc->order_quantity->$poId;
							$colorSizeTableId=$jsonarr->$trimcostid->$gmc->color_size_table_id->$poId;
						}
						if(str_replace("'","",$$cbocolorsizesensitive)==2){
							$bQty=($jsonarr->$trimcostid->$gms->$art->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[6];
							$bwqQty=($jsonarr->$trimcostid->$gms->$art->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[8];
							$order_qty=$jsonarr->$trimcostid->$gms->$art->order_quantity->$poId;
							$colorSizeTableId=$jsonarr->$trimcostid->$gms->$art->color_size_table_id->$poId;
						}
						if(str_replace("'","",$$cbocolorsizesensitive)==4){
							$bQty=($jsonarr->$trimcostid->$gmc->$gms->$art->$gmc->$itemsize->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[6];
							$bwqQty=($jsonarr->$trimcostid->$gmc->$gms->$art->$gmc->$itemsize->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[8];
							$order_qty=$jsonarr->$trimcostid->$gmc->$gms->$art->$gmc->$itemsize->order_quantity->$poId;
							$colorSizeTableId=$jsonarr->$trimcostid->$gmc->$gms->$art->$gmc->$itemsize->color_size_table_id->$poId;
						}
						if(str_replace("'","",$$cbocolorsizesensitive)==0){
							//$bQty=($jsonarr->$trimcostid->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[6];
							//$bwqQty=($jsonarr->$trimcostid->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[8];
							$bQty=$nosencivity_qty[$trimcostid][$consbreckdownarr[2]][$poId]['bQty'];
							$bwqQty=$nosencivity_qty[$trimcostid][$consbreckdownarr[2]][$poId]['bwqQty'];
							$order_qty=$jsonarr->$trimcostid->order_quantity->$poId;
							$colorSizeTableId=$jsonarr->$trimcostid->color_size_table_id->$poId;
						}

						$bamount=$bwqQty*$consbreckdownarr[9];
						$bwqQty=number_format($bwqQty,4,'.','');
						$bQty=number_format($bQty,4,'.','');
						//echo $bQty.'='.$jsonarr->$trimcostid->$gmc->$gms->$art->$gmc->$itemsize->req_qty->$poId.'='.$consbreckdownarr[13].'='.$consbreckdownarr[6].'<br>';	
						if ($d!=0){
							$data_array2 .=",";
						}
						$data_array2 ="(".$id1.",".$id_dtls.",".$txt_booking_no.",".$booking_mst_id.",".$$txtjob_id.",".$poId.",'".$consbreckdownarr[0]."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".trim($consbreckdownarr[3])."','".$color_id."','".$consbreckdownarr[5]."','".$bQty."','".$consbreckdownarr[7]."','".$bwqQty."','".$consbreckdownarr[9]."','".$bamount."','".$order_qty."','".$colorSizeTableId."','".$consbreckdownarr[14]."','".$consbreckdownarr[15]."','".$consbreckdownarr[16]."','".$consbreckdownarr[17]."',1,0)";
						$id1=$id1+1;
						$add_comma++;
						$d++;
						//echo "10**INSERT INTO wo_trim_book_con_dtls ($field_array2) values $data_array2"; echo "<br>";
						$rID2=sql_insert("wo_trim_book_con_dtls",$field_array2,$data_array2,0);
					}
				}//CONS break down end==============================================================================================
				$id_dtls=$id_dtls+1;
			}
		}
		//die;
		//check_table_status( $_SESSION['menu_id'],0); die;
		//echo "10**".$rID1.'=='.$rID2; check_table_status( $_SESSION['menu_id'],0);die;
		check_table_status( $_SESSION['menu_id'],0);
		
		if($db_type==0)
		{
			if($rID1 && $rID2){
				mysql_query("COMMIT");
				echo "0**".$new_booking_no[0];
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_booking_no[0];
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
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
		$curr_book_amount_job_level=array();
		$curr_book_amount_job_item_level=array();
		$jobArr=array(); $poArr=array(); $pre_trim_id_arr=array(); $des_arr=array(); $brand_arr=array(); $booking_dtls_id_arr=array();

		for ($i=1;$i<=$total_row;$i++){
			$txttrimcostid="txttrimcostid_".$i;
			$txtReqAmt="txtReqAmt_".$i;
			$txtpoid="txtpoid_".$i;
			$txttrimgroup="txttrimgroup_".$i;
			$hiddlabeldtlsdata="hiddlabeldtlsdata_".$i;
			//$txtuom="txtuom_".$i;
			//$cbocolorsizesensitive="cbocolorsizesensitive_".$i;
			$txtwoq="txtwoq_".$i;
			$txtexchrate="txtexchrate_".$i;
			$txtrate="txtrate_".$i;
			$txtamount="txtamount_".$i;
			//$txtddate="txtddate_".$i;
			//$consbreckdown="consbreckdown_".$i;
			$txtbookingid="txtbookingid_".$i;
			//$txtcountry="txtcountry_".$i;
			$txtjob_id="txtjob_".$i;
			$txtreqqnty="txtreqqnty_".$i;

			//$jsondata="jsondata_".$i;
			$txtdesc="txtdesc_".$i;
		    $txtbrandsup="txtbrandsup_".$i;
			$txtreqamount="txtreqamount_".$i;
			$txtreqamountjoblevelconsuom="txtreqamountjoblevelconsuom_".$i;
			$txtreqamountitemlevelconsuom="txtreqamountitemlevelconsuom_".$i;
				//=====================
			$JoBc=$$txtjob_id;
			$condition= new condition();

			if(str_replace("'","",$$txtjob_id) !=''){
				$condition->job_no("=$JoBc");
			}

			$condition->init();
			$trims= new trims($condition);
			//$req_qty_arr=$trims->getQtyArray_by_orderAndPrecostdtlsid();
			//$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsid();
			
			if($source_from==2) //Sourcing from Lib
			{
				$reqAmountJobLevelArr=$trims->getAmountArray_by_jobSourcing();
  				$reqQtyItemLevelArr=$trims->getQtyArray_by_precostdtlsid();
				$reqAmountItemLevelArr=$trims->getAmountArray_precostdtlsidSourcing();
			}
			else
			{
				$reqAmountJobLevelArr=$trims->getAmountArray_by_job();
  				$reqQtyItemLevelArr=$trims->getQtyArray_by_precostdtlsid();
				$reqAmountItemLevelArr=$trims->getAmountArray_precostdtlsid();
			}
			
			//$reqQtyItemLevelArr=$trims->getQtyArray_by_precostdtlsid();
			//$reqAmountItemLevelArr=$trims->getAmountArray_precostdtlsid();
			//$reqAmountJobLevelArr=$trims->getAmountArray_by_job();
			
			//====================
			$pretrimcostid=str_replace("'","",$$txttrimcostid);
			$poid=str_replace("'","",$$txtpoid);
			$bookingdtlsid=str_replace("'","",$$txtbookingid);

			$conversion_factor=$sql_lib_item_group_array[str_replace("'","",$$txttrimgroup)]['conversion_factor'];

			$curr_book_qty_job_item_level[str_replace("'","",$$txtjob_id)][$pretrimcostid]['req_qty']+=$reqQtyItemLevelArr[$pretrimcostid];
			$curr_book_qty_job_item_level[str_replace("'","",$$txtjob_id)][$pretrimcostid]['cur_qty']+=(str_replace("'","",$$txtreqqnty)*$conversion_factor);
			$curr_book_qty_job_item_level[str_replace("'","",$$txtjob_id)][$pretrimcostid]['prev_qty']=0;

			$curr_book_amount_job_level[str_replace("'","",$$txtjob_id)]['req_amount']=$reqAmountJobLevelArr[str_replace("'","",$$txtjob_id)];
			$curr_book_amount_job_level[str_replace("'","",$$txtjob_id)]['cur_amount']+=(str_replace("'","",$$txtamount)/str_replace("'","",$$txtexchrate));
			$curr_book_amount_job_level[str_replace("'","",$$txtjob_id)]['prev_amount']=0;

			$curr_book_amount_job_item_level[str_replace("'","",$$txtjob_id)][$pretrimcostid]['req_amount']+=$reqAmountItemLevelArr[$pretrimcostid];
			$curr_book_amount_job_item_level[str_replace("'","",$$txtjob_id)][$pretrimcostid]['cur_amount']+=(str_replace("'","",$$txtamount)/str_replace("'","",$$txtexchrate));
			$curr_book_amount_job_item_level[str_replace("'","",$$txtjob_id)][$pretrimcostid]['prev_amount']=0;

			$jobArr[$$txtjob_id]=$$txtjob_id;
			$poArr[$poid]=$poid;
			$pre_trim_id_arr[$pretrimcostid]=$pretrimcostid;

			if(str_replace("'","",$$txtdesc) != ''){
				$des_arr[$$txtdesc]=$$txtdesc;
			}
			if(str_replace("'","",$$txtbrandsup) != ''){
				$brand_arr[$$txtbrandsup]=$$txtbrandsup;
			}

			$booking_dtls_id_arr[$bookingdtlsid]=$bookingdtlsid;
		}

		$prev_book_amount_job_level=array();
		$prev_book_amount_job_item_level=array();
		//$sql=sql_select("select id, job_no, pre_cost_fabric_cost_dtls_id, trim_group, wo_qnty, amount, exchange_rate from wo_booking_dtls where job_no in(".implode(",",$jobArr).") and booking_type=2 and is_short=2 and status_active=1 and is_deleted=0"); //and booking_no !=$txt_booking_no
		$sql=sql_select("select b.id, b.job_no, b.pre_cost_fabric_cost_dtls_id, b.trim_group, b.wo_qnty, b.amount, b.exchange_rate from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and b.job_no in(".implode(",",$jobArr).") and b.booking_type=2 and b.is_short=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach($sql as $row){
			$curr_book_amount_job_level[$row[csf('job_no')]]['prev_amount']+=($row[csf('amount')]/$row[csf('exchange_rate')]);

			$prev_book_amount_job_level[$row[csf('id')]]['prev_amount']=($row[csf('amount')]/$row[csf('exchange_rate')]);
			$prev_book_amount_job_level[$row[csf('id')]]['prev_qty']=($row[csf('wo_qnty')]*$sql_lib_item_group_array[$row[csf('trim_group')]]['conversion_factor']);

			$curr_book_amount_job_item_level[$row[csf('job_no')]][$row[csf('pre_cost_fabric_cost_dtls_id')]]['prev_amount']+=($row[csf('amount')]/$row[csf('exchange_rate')]);

			$curr_book_qty_job_item_level[$row[csf('job_no')]][$row[csf('pre_cost_fabric_cost_dtls_id')]]['prev_qty']+=($row[csf('wo_qnty')]*$sql_lib_item_group_array[$row[csf('trim_group')]]['conversion_factor']);
		}

		$con = connect();
		if($db_type==0){
			mysql_query("BEGIN");
		}
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ){
			echo "15**1";
			disconnect($con);die;
		}

		if(count($des_arr)>0) $des_data_cond="and description in(".implode(",",$des_arr).")"; else $des_data_cond="";
		if(count($brand_arr)>0) $brand_data_cond="and brand_supplier in(".implode(",",$brand_arr).")"; else $brand_data_cond="";
		if (is_duplicate_field( "booking_no", "wo_booking_dtls", "job_no in(".implode(",",$jobArr).") and po_break_down_id in (".implode(",",$poArr).") and pre_cost_fabric_cost_dtls_id in(".implode(",",$pre_trim_id_arr).") and id not in(".implode(",",$booking_dtls_id_arr).") and booking_type=2 and is_short=2 and booking_no=$txt_booking_no and status_active=1 and is_deleted=0 $des_data_cond $brand_data_cond") == 1)
		{
			check_table_status( $_SESSION['menu_id'],0);
			echo "dup";
			disconnect($con);die;
		}
		$field_array_up1="pre_cost_fabric_cost_dtls_id*pre_req_amt*po_break_down_id*job_no*booking_no*trim_group*description*brand_supplier*uom*sensitivity*wo_qnty*exchange_rate*rate*amount*delivery_date*country_id_string*labeldtlsdata*updated_by*update_date";
		$field_array_up2="id, wo_trim_booking_dtls_id, booking_no, booking_mst_id, job_no, po_break_down_id, color_number_id, gmts_sizes, description, brand_supplier, item_color, item_size, cons, process_loss_percent, requirment, rate, amount, pcs, color_size_table_id, article_number, moq, pp_sample, zipper_break_down, status_active, is_deleted";
		$add_comma=0;
		$id1=return_next_id( "id", "wo_trim_book_con_dtls", 1 );
		$new_array_color=array();
		for ($i=1;$i<=$total_row;$i++){
			$txttrimcostid="txttrimcostid_".$i;
			$txtReqAmt="txtReqAmt_".$i;
			$txtpoid="txtpoid_".$i;
			$txttrimgroup="txttrimgroup_".$i;
			$hiddlabeldtlsdata="hiddlabeldtlsdata_".$i;
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
			$txtreqqnty="txtreqqnty_".$i;
			$jsondata="jsondata_".$i;
			$txtdesc="txtdesc_".$i;
		    $txtbrandsup="txtbrandsup_".$i;
			$txtreqamount="txtreqamount_".$i;
			$txtreqamountjoblevelconsuom="txtreqamountjoblevelconsuom_".$i;
			$txtreqamountitemlevelconsuom="txtreqamountitemlevelconsuom_".$i;
			$jsonarr=json_decode(str_replace("'","",$$jsondata));

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
			   disconnect($con); die;
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
			  disconnect($con);  die;
			}
			$job=str_replace("'","",$$txtjob_id);
			$trimcostid=str_replace("'","",$$txttrimcostid);
			$reqqnty=str_replace("'","",$$txtreqqnty);
			$woq=str_replace("'","",$$txtwoq);
			$rate=str_replace("'","",$$txtrate);

			$amt=str_replace("'","",$$txtamount);
			$exRate=str_replace("'","",$$txtexchrate);

			$uom_id=str_replace("'","",$$txtuom);

			//==============================
			if($amount_exceed_level==1){
				$reqAmt=(($curr_book_amount_job_level[$job]['req_amount']*$exeed_budge_amount)/100)+$curr_book_amount_job_level[$job]['req_amount'];

				$pre_amt=0;
				$ex_book_dtls_id=explode(",",str_replace("'",'',$$txtbookingid));
				foreach($ex_book_dtls_id as $book_dtls_id)
				{
					$pre_amt+=$prev_book_amount_job_level[$book_dtls_id]['prev_amount'];
				}
				$curAmt=($curr_book_amount_job_level[$job]['prev_amount']-$pre_amt)+($amt/$exRate);
				check_table_status( $_SESSION['menu_id'],0);
				if(($curAmt-$reqAmt)>1){
					echo "vad1**".str_replace("'","",$txt_booking_no)."**".$i;
					check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);die;
				}
				$curr_book_amount_job_level[$job]['prev_amount']+=($amt/$exRate);
			}
			if($amount_exceed_level==2){
				$reqAmt=(($curr_book_amount_job_item_level[$job][$trimcostid]['req_amount']*$exeed_budge_amount)/100)+$curr_book_amount_job_item_level[$job][$trimcostid]['req_amount'];
				$pre_amt=0;
				$ex_book_dtls_id=explode(",",str_replace("'",'',$$txtbookingid));
				foreach($ex_book_dtls_id as $book_dtls_id)
				{
					$pre_amt+=$prev_book_amount_job_level[$book_dtls_id]['prev_amount'];
				}
				$curAmt=($curr_book_amount_job_item_level[$job][$trimcostid]['prev_amount']-$pre_amt)+($amt/$exRate);
				if(($curAmt-$reqAmt)>1){
					echo "vad1**".str_replace("'","",$txt_booking_no)."**".$i."**".$curAmt."**".$reqAmt."**".$curr_book_amount_job_item_level[$job][$trimcostid]['req_amount'];
					check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);die;
				}
				$curr_book_amount_job_item_level[$job][$trimcostid]['prev_amount']+=($amt/$exRate);
			}

			if($exceed_qty_level==2){
				$reqQty=(($curr_book_qty_job_item_level[$job][$trimcostid]['req_qty']*$exeed_budge_qty)/100)+$curr_book_qty_job_item_level[$job][$trimcostid]['req_qty'];

				$pre_qty=0;
				$ex_book_dtls_id=explode(",",str_replace("'",'',$$txtbookingid));
				foreach($ex_book_dtls_id as $book_dtls_id)
				{
					$pre_qty+=$prev_book_amount_job_level[$book_dtls_id]['prev_qty'];
				}
				$curQty=($curr_book_qty_job_item_level[$job][$trimcostid]['prev_qty']-$pre_qty)+($woq*$conversion_factor);
					if(($curQty-$reqQty)>1){
					echo "vad2**".str_replace("'","",$txt_booking_no)."**".$i."**".$curQty."**".$reqQty."**".$curr_book_amount_job_item_level[$job][$trimcostid]['req_qty'];
					check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);die;
				}
				$curr_book_qty_job_item_level[$job][$trimcostid]['prev_qty']+=($woq*$conversion_factor);
			}
			//===========================

			$po_wise_wqqty=array(); $po_wise_total_wqqty=0;
			foreach($strdata->$job->$trimcostid->po_id as $poId){
				$wqQty=($strdata->$job->$trimcostid->req_qnty->$poId/$reqqnty)*$woq;
				$po_wise_wqqty[$poId]= number_format($wqQty,4,'.','');
				$po_wise_total_wqqty+=number_format($wqQty,4,'.','');
				if(str_replace("'",'',$$consbreckdown) !=''){
					$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
					for($c=0;$c < count($consbreckdown_array);$c++){
						$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
						$gmc=$consbreckdownarr[0];
						$gms=$consbreckdownarr[1];
						$art=$consbreckdownarr[14];
						if(str_replace("'","",$$cbocolorsizesensitive)==0){
							$bQty=($jsonarr->$trimcostid->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[6];
							$bwqQty=($jsonarr->$trimcostid->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[8];
							$nosencivity_qty[$trimcostid][$consbreckdownarr[2]][$poId]['bQty']=number_format($bQty,4,'.','');
							$nosencivity_qty[$trimcostid][$consbreckdownarr[2]][$poId]['bwqQty']=number_format($bwqQty,4,'.','');
							$nosencivity_qty[$trimcostid][$consbreckdownarr[2]][$poId]['breq']=$consbreckdownarr[6];
							$nosencivity_qty[$trimcostid][$consbreckdownarr[2]][$poId]['bwreq']=$consbreckdownarr[8];
						}
					}

				}
			}
			/* echo '10**<pre>';
			print_r($nosencivity_qty); die; */	
			foreach($nosencivity_qty as $trim_costid=>$trimdata){
				foreach($trimdata as $deccription=>$podata){
					foreach($podata as $poid=>$data)
					{
						$bQty_total[$trim_costid][$deccription]+=$data['bQty'];
						$bwqQty_total[$trim_costid][$deccription]+=$data['bwqQty'];
						$bqQty_req[$trim_costid][$deccription]=$data['breq'];
						$bwQty_req[$trim_costid][$deccription]=$data['bwreq'];
					}					
				}				
			}
			
			foreach($nosencivity_qty as $trimcost_id=>$desdata){
				foreach($desdata as $des=>$podata){
					//$last_po_id=array_key_last($nosencivity_qty[$trimcost_id][$des]);
					$keys = array_keys($nosencivity_qty[$trimcost_id][$des]);
					$last_po_id = end($keys);
					$trim_total=$bQty_total[$trimcost_id][$des];
					$trim_total_req=$bqQty_req[$trimcost_id][$des];
					//echo "10**".$trim_total.'--'.$trim_total_req.'--'.array_key_last($nosencivity_qty[$trimcostid][$des]); die;
					if($trim_total<$trim_total_req){
						$fraction_qty=number_format($trim_total_req-$trim_total,4,'.','');						
						$nosencivity_qty[$trimcost_id][$des][$last_po_id]['bQty']+=$fraction_qty;
					}
					else{
						$fraction_qty=number_format($trim_total-$trim_total_req,4,'.','');						
						$nosencivity_qty[$trimcost_id][$des][$last_po_id]['bQty']-=$fraction_qty;
					}
					$fraction_qtybwqQty=0;
					$trim_btotal=$bwqQty_total[$trimcost_id][$des];
					$trim_total_bwreq=$bwQty_req[$trimcost_id][$des];
					if($trim_btotal<$trim_total_bwreq){
						$fraction_qtybwqQty=number_format($trim_total_bwreq-$trim_btotal,4,'.','');						
						$nosencivity_qty[$trimcost_id][$des][$last_po_id]['bwqQty']+=$fraction_qtybwqQty;
					}
					else{
						$fraction_qtybwqQty=number_format($trim_btotal-$trim_total_bwreq,4,'.','');						
						$nosencivity_qty[$trimcost_id][$des][$last_po_id]['bwqQty']-=$fraction_qtybwqQty;
					}
				}
			}
			
			if($po_wise_total_wqqty<$woq){
				$fraction_qty=0;
				//$last_po_id_dtls=array_key_last($po_wise_wqqty);
				$keys = array_keys($po_wise_wqqty);
				$last_po_id_dtls = end($keys);
				$fraction_qty=$woq-$po_wise_total_wqqty;
				$po_wise_wqqty[$last_po_id_dtls]+=number_format($fraction_qty,4,'.','');
			}
			else{
				$fraction_qty=0;
				//$last_po_id_dtls=array_key_last($po_wise_wqqty);
				$keys = array_keys($po_wise_wqqty);
				$last_po_id_dtls = end($keys);
				$fraction_qty=$po_wise_total_wqqty-$woq;
				$po_wise_wqqty[$last_po_id_dtls]-=number_format($fraction_qty,4,'.','');
			}
			
			if(str_replace("'",'',$$txtbookingid)!=""){
				foreach($strdata->$job->$trimcostid->po_id as $poId){
					//$wqQty=($strdata->$job->$trimcostid->req_qnty->$poId/$reqqnty)*$woq;
					//$wqQty=number_format($wqQty,4,'.','');
					$wqQty=$po_wise_wqqty[$poId];
					$amount=$wqQty*$rate;
					$id_arr=array();
					$data_array_up1=array();
					$id_arr[]=str_replace("'",'',$strdata->$job->$trimcostid->booking_id->$poId);
					$data_array_up1[str_replace("'",'',$strdata->$job->$trimcostid->booking_id->$poId)] =explode("*",("".$$txttrimcostid."*".$$txtReqAmt."*".$poId."*".$$txtjob_id."*".$txt_booking_no."*".$$txttrimgroup."*".$$txtdesc."*".trim($$txtbrandsup)."*".$$txtuom."*".$$cbocolorsizesensitive."*".$wqQty."*".$$txtexchrate."*".$$txtrate."*".$amount."*".$$txtddate."*".$$txtcountry."*".$$hiddlabeldtlsdata."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					if($data_array_up1 !=""){
						$rID1=execute_query(bulk_update_sql_statement( "wo_booking_dtls", "id", $field_array_up1, $data_array_up1, $id_arr ));
					}
					//	CONS break down===============================================================================================
					$rID2=1;
					if(str_replace("'",'',$$consbreckdown) !=''){
						$rID_de1=execute_query( "delete from wo_trim_book_con_dtls where  wo_trim_booking_dtls_id =".$strdata->$job->$trimcostid->booking_id->$poId."",0);
						$consbreckdown_array=array();
						$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
						$d=0;
						for($c=0;$c < count($consbreckdown_array);$c++){
							$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
							if(str_replace("'","",$consbreckdownarr[4])=='0') $consbreckdownarr[4]='';
							if(str_replace("'","",$consbreckdownarr[4]) !="")
							{
							    if (!in_array(str_replace("'","",$consbreckdownarr[4]),$new_array_color, TRUE))
							    {
							        $color_id = return_id( str_replace("'","",$consbreckdownarr[4]), $color_library, "lib_color", "id,color_name","272");
							        $new_array_color[$color_id]=str_replace("'","",$consbreckdownarr[4]);
							    }
							    else $color_id =  array_search(str_replace("'","",$consbreckdownarr[4]), $new_array_color);
							}
							else $color_id=0;
							
							$gmc=$consbreckdownarr[0];
							$gms=$consbreckdownarr[1];
							$itemcolor=$color_id;
							$itemsize=str_replace("'", "", $consbreckdownarr[5]);
							$art=$consbreckdownarr[14];

							if(str_replace("'","",$$cbocolorsizesensitive)==1 || str_replace("'","",$$cbocolorsizesensitive)==3){
								$bQty=($jsonarr->$trimcostid->$gmc->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[6];
								$bwqQty=($jsonarr->$trimcostid->$gmc->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[8];
								$order_qty=$jsonarr->$trimcostid->$gmc->order_quantity->$poId;
								$colorSizeTableId=$jsonarr->$trimcostid->$gmc->color_size_table_id->$poId;
							}
							if(str_replace("'","",$$cbocolorsizesensitive)==2){
								$bQty=($jsonarr->$trimcostid->$gms->$art->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[6];
								$bwqQty=($jsonarr->$trimcostid->$gms->$art->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[8];
								$order_qty=$jsonarr->$trimcostid->$gms->$art->order_quantity->$poId;
								$colorSizeTableId=$jsonarr->$trimcostid->$gms->$art->color_size_table_id->$poId;
							}
							if(str_replace("'","",$$cbocolorsizesensitive)==4){
								$bQty=($jsonarr->$trimcostid->$gmc->$gms->$art->$gmc->$itemsize->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[6];
								$bwqQty=($jsonarr->$trimcostid->$gmc->$gms->$art->$gmc->$itemsize->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[8];
								$order_qty=$jsonarr->$trimcostid->$gmc->$gms->$art->$gmc->$itemsize->order_quantity->$poId;
								$colorSizeTableId=$jsonarr->$trimcostid->$gmc->$gms->$art->$gmc->$itemsize->color_size_table_id->$poId;
							}
							if(str_replace("'","",$$cbocolorsizesensitive)==0){
								//$bQty=($jsonarr->$trimcostid->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[6];
								//$bwqQty=($jsonarr->$trimcostid->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[8];
								$bQty=$nosencivity_qty[$trimcostid][$consbreckdownarr[2]][$poId]['bQty'];
								$bwqQty=$nosencivity_qty[$trimcostid][$consbreckdownarr[2]][$poId]['bwqQty'];
								$order_qty=$jsonarr->$trimcostid->order_quantity->$poId;
								$colorSizeTableId=$jsonarr->$trimcostid->color_size_table_id->$poId;
							}
							$bQty=number_format($bQty,4,'.','');
							$bwqQty=number_format($bwqQty,4,'.','');
							$bamount=$bwqQty*$consbreckdownarr[9];
							
							if ($d!=0) $data_array2 .=",";
							$data_array2 ="(".$id1.",".$strdata->$job->$trimcostid->booking_id->$poId.",".$txt_booking_no.",".$booking_mst_id.",".$$txtjob_id.",".$poId.",'".$consbreckdownarr[0]."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".trim($consbreckdownarr[3])."','".$color_id."','".$consbreckdownarr[5]."','".$bQty."','".$consbreckdownarr[7]."','".$bwqQty."','".$consbreckdownarr[9]."','".$bamount."','".$order_qty."','".$colorSizeTableId."','".$consbreckdownarr[14]."','".$consbreckdownarr[15]."','".$consbreckdownarr[16]."','".$consbreckdownarr[17]."',1,0)";
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
		if($db_type==0){
			if($rID1 && $rID2){
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		else if($db_type==2 || $db_type==1 ){
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
	else if ($operation==2){
		$con = connect();
		if($db_type==0){
			mysql_query("BEGIN");
		}
		for ($i=1;$i<=$total_row;$i++){
			$txttrimcostid="txttrimcostid_".$i;
			$txttrimgroup="txttrimgroup_".$i;
			$txtpoid="txtpoid_".$i;
			$txtbookingid="txtbookingid_".$i;

			$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no and b.item_group=".$$txttrimgroup." and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($pi_number){
				echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number."**0";
			   disconnect($con); die;
			}
			$recv_number=return_field_value( "recv_number", "inv_receive_master a,inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and b.item_group_id=".$$txttrimgroup." and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($recv_number){
				echo "recv1**".str_replace("'","",$txt_booking_no)."**".$recv_number."**0";
			   disconnect($con); die;
			}
			$delete_cause=str_replace("'","",$delete_cause);
			$delete_cause=str_replace('"','',$delete_cause);
			$delete_cause=str_replace('(','',$delete_cause);
			$delete_cause=str_replace(')','',$delete_cause);
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

if ($action=="trims_booking_popup"){
	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $company_id.'DDD';
	?>
	<script>
		function set_checkvalue(){
			if(document.getElementById('chk_job_wo_po').value==0){
				document.getElementById('chk_job_wo_po').value=1;
				document.getElementById('txt_style').value="";
				document.getElementById('txt_job').value="";
				document.getElementById('txt_order_search').value="";
				$('#txt_style').attr('disabled',true);
				$('#txt_job').attr('disabled',true);
				$('#txt_order_search').attr('disabled',true);
			}
			else {
				document.getElementById('chk_job_wo_po').value=0;
				$('#txt_style').attr('disabled',false);
				$('#txt_job').attr('disabled',false);
				$('#txt_order_search').attr('disabled',false);
				}
		}
		function js_set_value( str_data ){
			document.getElementById('txt_booking').value=str_data;
			parent.emailwindow.hide();
		}
	</script>
	</head>
    <body>
        <div align="center" style="width:930px;" >
        <input type="hidden" id="txt_booking" value="" />
            <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table width="930" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                <thead>
                <?
                if($company_id) $disabled="1";else $disabled="";
				?>
                    <tr>
                        <th colspan="11" align="center"><?=create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" ); ?></th>
                    </tr>
                    <tr>
                        <th width="140" class="must_entry_caption">Company Name</th>
                        <th width="130" class="must_entry_caption">Buyer Name</th>
                        <th width="90">Style Ref </th>
                        <th width="70">Job No </th>
                        <th width="80">Order No</th>
                        <th width="130">Supplier Name</th>
                        <th width="70">Booking No</th>
                        <th colspan="2" width="130"> Booking Date Range</th>
                        <th width="70"><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">WO Without PO</th>
                    </tr>
                </thead>
                <tr class="general">
                    <td><?=create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", '', "load_drop_down( 'trims_booking_multi_job_controllerurmi', this.value, 'load_drop_down_buyer_pop', 'buyer_td' );",$disabled); ?></td>

                    <td id="buyer_td"><?=create_drop_down( "cbo_buyer_name", 130, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );  ?></td>
                    <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:80px"></td>
                    <td><input name="txt_job" id="txt_job" class="text_boxes" style="width:60px"></td>
                    <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:80px"></td>
                    <td><?=create_drop_down( "cbo_supplier_name", 130, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=4 and a.status_active =1 and a.is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "",0 ); ?></td>
                    <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:60px"></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date"></td>
                    <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date"></td>
                    <td align="center">
                    	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_job').value+'_'+document.getElementById('chk_job_wo_po').value, 'create_booking_search_list_view', 'search_div', 'trims_booking_multi_job_controllerurmi','setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
                    </td>
                </tr>
                <tr class="general">
                    <td align="center" valign="middle" colspan="11" >
						<?=load_month_buttons(1); ?>
                    </td>
                </tr>
            </table>
            <div id="search_div"></div>
            </form>
        </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
     <script type="text/javascript">
		$("#cbo_company_mst").val(<?=$company_id; ?>);
		load_drop_down( 'trims_booking_multi_job_controllerurmi', $("#cbo_company_mst").val(), 'load_drop_down_buyer_pop', 'buyer_td' );
		$("#cbo_buyer_name").val(<?=$buyer_id; ?>);
	</script>
    </html>
    <?
    exit();
}

if ($action=="create_booking_search_list_view"){
	$data=explode('_',$data);
	if($data[0]==0 && $data[1]==0)
	{
		echo "<span style='color:red; font-weight:bold; font-size:20px; text-align:center'>Please select company or buyer first.";
		die;
	}
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else $company="";
	if($data[11]==0){
		if ($data[1]!=0) $buyer=" and c.buyer_name='$data[1]'"; else $buyer=set_user_lavel_filtering(' and c.buyer_name','buyer_id');
		}
	else{
		if ($data[1]!=0) $buyer=" and a.buyer_id='$data[1]'"; else $buyer=set_user_lavel_filtering(' and a.buyer_id','buyer_id');
		}
	if ($data[2]!=0) $supplier_id=" and a.supplier_id='$data[2]'"; else $supplier_id ="";
	if($db_type==0){
	$booking_year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[5]";
		if ($data[3]!="" &&  $data[4]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $booking_date ="";

	}
	if($db_type==2){
	$booking_year_cond=" and to_char(a.insert_date,'YYYY')=$data[5]";
		if ($data[3]!="" &&  $data[4]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";

	}

	if($data[7]==1){
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num ='$data[6]'   "; else $booking_cond="";
		if (trim($data[8])!="") $style_cond=" and c.style_ref_no ='$data[8]'";
		if (str_replace("'","",$data[9])!="") $order_cond=" and d.po_number = '$data[9]'  "; //else  $order_cond="";
		if (str_replace("'","",$data[10])!="") $job_cond=" and c.job_no_prefix_num = '$data[10]'  "; //else  $order_cond="";
	}
	if($data[7]==2){
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '$data[6]%'  $booking_year_cond  "; else $booking_cond="";
		if (trim($data[8])!="") $style_cond=" and c.style_ref_no like '$data[8]%'  "; //else  $style_cond="";
		if (str_replace("'","",$data[9])!="") $order_cond=" and d.po_number like '$data[9]%'  "; //else  $order_cond="";
		if (str_replace("'","",$data[10])!="") $job_cond=" and c.job_no_prefix_num like '$data[10]%'  "; //else  $order_cond="";
	}
	if($data[7]==3){
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[6]'  $booking_year_cond  "; else $booking_cond="";
		if (trim($data[8])!="") $style_cond=" and c.style_ref_no like '%$data[8]'"; //else  $style_cond="";
		if (str_replace("'","",$data[9])!="") $order_cond=" and d.po_number like '%$data[9]'  "; //else  $order_cond="";
		if (str_replace("'","",$data[10])!="") $job_cond=" and c.job_no_prefix_num like '%$data[10]'  "; //else  $order_cond="";
	}
	if($data[7]==4 || $data[7]==0){
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[6]%'  $booking_year_cond  "; else $booking_cond="";
		if (trim($data[8])!="") $style_cond=" and c.style_ref_no like '%$data[8]%'"; //else  $style_cond="";
		if (str_replace("'","",$data[9])!="") $order_cond=" and d.po_number like '%$data[9]%'  "; //else  $order_cond="";
		if (str_replace("'","",$data[10])!="") $job_cond=" and c.job_no_prefix_num like '%$data[10]%'  "; //else  $order_cond="";
	}
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$user_arr=return_library_array("select id,user_full_name from user_passwd","id","user_full_name");
	$approved=array(0=>"No",1=>"Yes");
	$is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$arr=array (1=>$comp,2=>$suplier,7=>$approved,8=>$is_ready,9=>$user_arr);
	if($data[11]==0)
	{
		//echo $sql;
		  $sql="select min(a.id) as id, a.booking_no_prefix_num,a.inserted_by, a.booking_no,a.is_approved,a.ready_to_approved,a.company_id,  (case 
             when a.pay_mode in (3,5)  then ( select C.COMPANY_NAME from lib_company c  where c.id=A.SUPPLIER_ID ) 
             else  (select D.SUPPLIER_NAME from lib_supplier d  where d.id=A.SUPPLIER_ID)
            end
            ) as supplier, a.booking_date, a.delivery_date,c.style_ref_no,d.po_number from wo_booking_mst a,wo_booking_dtls b, wo_po_details_master c,wo_po_break_down d  where a.booking_no=b.booking_no and b.job_no=c.job_no and b.job_no=d.job_no_mst and b.po_break_down_id=d.id and a.booking_type=2 and a.entry_form=272
	and  a.status_active =1 and a.is_deleted=0  and  b.status_active =1 and b.is_deleted=0 $company  $buyer  $supplier_id $booking_date $booking_cond $style_cond $order_cond $job_cond group by a.booking_no_prefix_num,a.inserted_by, a.booking_no,a.is_approved,a.ready_to_approved,a.company_id, a.supplier_id, a.booking_date, a.delivery_date,c.style_ref_no,d.po_number,a.pay_mode  order by id DESC";


		echo  create_list_view("list_view", "Booking No,Company,Supplier,Booking Date,Delivery Date,Style Ref No,Po Number,Approved,Ready to App,Insert User", "60,60,150,80,80,150,120,60,80,100","1080","350",0, $sql , "js_set_value", "booking_no", "", 1, "0,company_id,0,0,0,0,0,is_approved,ready_to_approved,inserted_by", $arr , "booking_no_prefix_num,company_id,supplier,booking_date,delivery_date,style_ref_no,po_number,is_approved,ready_to_approved,inserted_by", '','','0,0,0,3,3,0,0,0','','');

	}
	else
	{
		$sql="select min(a.id) as id,a.job_no,a.inserted_by,a.booking_no_prefix_num, a.booking_no,company_id,(case 
             when a.pay_mode in (3,5)  then ( select C.COMPANY_NAME from lib_company c  where c.id=A.SUPPLIER_ID ) 
             else  (select D.SUPPLIER_NAME from lib_supplier d  where d.id=A.SUPPLIER_ID)
            end
            ) as supplier,a.booking_date,a.delivery_date from wo_booking_mst a  where NOT EXISTS (SELECT booking_no FROM wo_booking_dtls b WHERE a.booking_no=b.booking_no and b.status_active=1 and b.is_deleted=0) and a.booking_type=2 and a.entry_form=272 and  a.status_active =1 and a.is_deleted=0 $company $buyer $supplier_id $booking_date $booking_cond group by a.booking_no_prefix_num, a.inserted_by, a.booking_no, a.job_no, company_id, a.supplier_id, a.booking_date, a.delivery_date,a.pay_mode order by id DESC";
		


		$arr=array (1=>$comp,2=>$suplier,5=>$user_arr);
		echo  create_list_view("list_view", "Booking No,Company,Supplier,Booking Date,Delivery Date,Insert User", "120,100,100,100,100","700","300",0, $sql , "js_set_value", "booking_no", "", 1, "0,company_id,0,0,0,inserted_by", $arr , "booking_no_prefix_num,company_id,supplier,booking_date,delivery_date,inserted_by", '','','0,0,0,3,3,0','','');
	}
	exit();
}

if($action=="terms_condition_popup"){
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

			data_all=data_all+get_submitted_data_string('txt_booking_no*termscondition_'+i,"");
		}
		var data="action=save_update_delete_fabric_booking_terms_condition&operation="+operation+'&total_row='+row_num+data_all;
		http.open("POST","trims_booking_multi_job_controllerurmi.php",true);
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
<input type="hidden" id="txt_booking_no" name="txt_booking_no" value="<? echo str_replace("'","",$txt_booking_no) ?>"/>
        	<form id="termscondi_1" autocomplete="off">
            <table width="650" cellspacing="0" class="rpt_table" border="0" id="tbl_termcondi_details" rules="all">
                	<thead>
                    	<tr>
                        	<th width="50">Sl</th><th width="530">Terms</th><th ></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no");// quotation_id='$data'
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
					//$data_array=sql_select("select id, terms from  lib_terms_condition where is_default=1");// quotation_id='$data'
                                        $data_array=sql_select("select id, terms from  lib_terms_condition where is_default=1 and page_id = 87");// quotation_id='$data'
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
exit();
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

		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con);die;}
		 $id=return_next_id( "id", "wo_booking_terms_condition", 1 ) ;
		 $field_array="id,booking_no,terms";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $termscondition="termscondition_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$txt_booking_no.",".$$termscondition.")";
			$id=$id+1;
		 }
		$rID_de3=execute_query( "delete from wo_booking_terms_condition where  booking_no =".$txt_booking_no."",0);

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
	 $sql= "select id,booking_no,booking_date,company_id,buyer_id, 	currency_id,exchange_rate,pay_mode,booking_month,supplier_id,attention,remarks,item_from_precost,delivery_date,source,booking_year,is_approved,cbo_level,ready_to_approved,fabric_source,delivery_address,location_id_address,pay_term,tenor from wo_booking_mst  where booking_no='$data' and  status_active =1 and is_deleted=0";
	$data_array=sql_select($sql);
	foreach ($data_array as $row){
		//$delivery_address = str_replace("\n", "\\n", $row[csf("delivery_address")]);
		$location_id_address=$row[csf("location_id_address")];
		if($location_id_address) $location_id_address=$location_id_address;else $location_id_address="";
        //echo "document.getElementById('delivery_address').value = '".$delivery_address."';\n";
		//echo "set_multiselect('delivery_address','0','0','','0');\n";
		echo "set_multiselect('delivery_address','0','1','" . $location_id_address . "','0');\n";
		echo "get_php_form_data(".$row[csf("company_id")].", 'populate_variable_setting_data', 'requires/trims_booking_multi_job_controllerurmi' );\n";
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('txt_booking_no').value = '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('booking_mst_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_currency').value = '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('cbo_pay_mode').value = '".$row[csf("pay_mode")]."';\n";
		echo "load_drop_down( 'requires/trims_booking_multi_job_controllerurmi', '".$row[csf("pay_mode")]."', 'load_drop_down_supplier', 'supplier_td' );\n";
		echo "document.getElementById('txt_booking_date').value = '".change_date_format($row[csf("booking_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('cbo_booking_month').value = '".$row[csf("booking_month")]."';\n";
		echo "document.getElementById('cbo_supplier_name').value = '".$row[csf("supplier_id")]."';\n";
		echo "document.getElementById('txt_attention').value = '".$row[csf("attention")]."';\n";
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('txt_delivery_date').value = '".change_date_format($row[csf("delivery_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('cbo_source').value = '".$row[csf("source")]."';\n";
        echo "document.getElementById('cbo_material_source').value = '".$row[csf("fabric_source")]."';\n";
		echo "document.getElementById('cbo_booking_year').value = '".$row[csf("booking_year")]."';\n";
		echo "document.getElementById('id_approved_id').value = '".$row[csf("is_approved")]."';\n";
		echo "document.getElementById('cbo_level').value = '".$row[csf("cbo_level")]."';\n";
		echo "document.getElementById('cbo_payterm_id').value = '".$row[csf("pay_term")]."';\n";
		echo "document.getElementById('txt_tenor').value = '".$row[csf("tenor")]."';\n";
		echo "document.getElementById('cbo_ready_to_approved').value = '".$row[csf("ready_to_approved")]."';\n";
		echo " $('#cbo_company_name').attr('disabled',true);\n";
		echo " $('#cbo_supplier_name').attr('disabled',true);\n";
		echo " $('#cbo_level').attr('disabled',true);\n";
		echo " $('#cbo_buyer_name').attr('disabled',true);\n";
		echo "fnc_show_booking_list();\n";

		if($row[csf("is_approved")]==1){
			echo "document.getElementById('app_sms2').innerHTML = 'This booking is approved';\n";
			echo "document.getElementById('txt_un_appv_request').disabled = '".false."';\n";
		}
		else if($row[csf("is_approved")]==3){
			echo "document.getElementById('app_sms2').innerHTML = 'This booking is partial approved';\n";
			echo "document.getElementById('txt_un_appv_request').disabled = '".false."';\n";
		}
		else{
			echo "document.getElementById('app_sms2').innerHTML = '';\n";
			echo "document.getElementById('txt_un_appv_request').disabled = '".true."';\n";
		}
		$wo_id=$row[csf('id')];
		$sql_cause="select MAX(id) as id from fabric_booking_approval_cause where page_id='336' and entry_form=8 and user_id='$user_id' and booking_id='$wo_id'  and status_active=1 and is_deleted=0";
        //echo $sql_cause; //die;
	    $nameArray_cause=sql_select($sql_cause);
	    if(count($nameArray_cause)>0){
	      foreach($nameArray_cause as $arow)
	      {
	        $app_cause1=return_field_value("approval_cause", "fabric_booking_approval_cause", "id='".$arow[csf("id")]."' and status_active=1 and is_deleted=0");
	        $app_cause = str_replace(array("\r", "\n"), ' ', $app_cause1);
	        echo "document.getElementById('txt_refusing').value = '".$app_cause."';\n";
	      }
	    }
	}
}

//================================================report Start=====================================================
if($action=="show_trim_booking_report")
{

	extract($_REQUEST);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$id_approved_id=str_replace("'","",$id_approved_id);
	$report_type=str_replace("'","",$report_type);
	$show_comment=str_replace("'","",$show_comment);

	$color_library=return_library_array("select id, color_name from lib_color", "id", "color_name");
	$size_library=return_library_array("select id, size_name from  lib_size", "id", "size_name");
	$company_library=return_library_array("select id, company_name from lib_company", "id", "company_name");
	$trim_group_library= return_library_array("select id, item_name from lib_item_group",'id','item_name');
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');

	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$order_uom_arr=return_library_array("select id,order_uom  from lib_item_group","id","order_uom");
	$deling_marcent_arr=return_library_array("select id,team_member_name from lib_mkt_team_member_info","id","team_member_name");
	$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");

	$nameArray_approved=sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no='$txt_booking_no' and b.entry_form=8 and  a.status_active =1 and a.is_deleted=0");
	list($nameArray_approved_row)=$nameArray_approved;
	//$revised_no_data=sql_select("select a.revised_no  from wo_booking_mst a where  a.booking_no=$txt_booking_no and  a.status_active =1 and a.is_deleted=0");
	//list($revised_no_row)=$revised_no_data;
		$booking_grand_total=0;
		$currency_id="";


		$buyer_string=array();
		$style_owner=array();
		$job_no=array();
		$style_ref=array();
		$all_dealing_marcent=array();
		$season=array();
		$order_repeat_no=array();
		$po_id_arr=array();

		$nameArray_buyer=sql_select( "select  a.style_ref_no, a.job_no, a.style_owner, a.buyer_name, a.dealing_marchant, a.season, a.season_matrix, a.season_buyer_wise, a.order_repeat_no, b.po_break_down_id from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no='$txt_booking_no' and b.status_active =1 and b.is_deleted=0");
        foreach ($nameArray_buyer as $result_buy){
			$buyer_string[$result_buy[csf('buyer_name')]]=$buyer_name_arr[$result_buy[csf('buyer_name')]];
			$style_owner[$result_buy[csf('job_no')]]=$company_library[$result_buy[csf('style_owner')]];
			$job_no[$result_buy[csf('job_no')]]=$result_buy[csf('job_no')];
			$style_ref[$result_buy[csf('job_no')]]=$result_buy[csf('style_ref_no')];
			$all_dealing_marcent[$result_buy[csf('job_no')]]=$deling_marcent_arr[$result_buy[csf('dealing_marchant')]];
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
		$job_no=implode(",",$job_no);
		$seasons_names=rtrim($seasons_name,',');

		$seasons_names=implode(",",array_unique(explode(",",$seasons_names)));
		$poid_arr=array_unique($po_id_arr);

		$order_rept_no=rtrim($order_rept_no,',');
		$order_rept_no=implode(",",array_unique(explode(",",$order_rept_no)));

		$po_no=array();
		$file_no=array();
		$ref_no=array();
		$po_quantity=array();
		$pub_shipment_date='';$int_ref_no='';$tot_po_quantity=0;$po_idss='';
		$nameArray_job=sql_select( "select b.job_no_mst,b.id,b.pub_shipment_date, b.po_number,b.grouping, b.file_no, sum(b.po_quantity) as po_quantity  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no='$txt_booking_no' and  a.status_active =1 and a.is_deleted=0 group by b.job_no_mst,b.id,b.pub_shipment_date, b.po_number,b.grouping, b.file_no ");
        foreach ($nameArray_job as $result_job){
			$po_no[$result_job[csf('job_no_mst')]][$result_job[csf('id')]]=$result_job[csf('po_number')];
			$file_no[$result_job[csf('id')]]=$result_job[csf('file_no')];
			$ref_no[$result_job[csf('id')]]=$result_job[csf('grouping')];
			$po_quantity[$result_job[csf('id')]]=$result_job[csf('po_quantity')];
		//	$tot_po_quantity+=$result_job[csf('po_quantity')];
			$job_ref_no[$result_job[csf('job_no_mst')]].=$result_job[csf('grouping')].',';
			$po_no_arr[$result_job[csf('job_no_mst')]]['po_id'].=$result_job[csf('id')].',';
			$pub_shipment_date.=$result_job[csf('pub_shipment_date')].',';
			$int_ref_no.=$result_job[csf('grouping')].',';
			if($po_idss=='') $po_idss=$result_job[csf('id')];else $po_idss.=",".$result_job[csf('id')];

		}
		$sql_job=sql_select( "select b.job_no_mst,b.id as po_id, b.po_quantity as po_quantity  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and  a.status_active =1 and a.is_deleted=0 and  b.status_active =1 and b.is_deleted=0 and b.id in(".$po_idss.") ");
		foreach ($sql_job as $row)
		{
			$job_po_qty_arr[$row[csf('job_no_mst')]][$row[csf('po_id')]]+=$row[csf('po_quantity')];
			$tot_po_quantity+=$row[csf('po_quantity')];
		}

        $nameArray=sql_select( "select a.booking_no, a.pay_mode,a.buyer_id, a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.source, a.remarks, a.revised_no from wo_booking_mst a where a.booking_no='$txt_booking_no' and a.status_active =1 and a.is_deleted=0");
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
		}
	?>
	<html>
	<head>
	  <style type="text/css" media="print">
	        @media print {
	        thead {display: table-header-group;}
	    }
		@media print {
				  #page_break_div {
					page-break-before: always;
				  }
		}
				.footer_signature {
					position: fixed;
					height: auto;
					bottom:0;
					width:100%;

					}
				@media print {
					table {
						page-break-inside: avoid;
					}
				}
				@media all {
	  			#page_break_div   { display: none; }
				}

	</style>
	</head>


	<?php ob_start();?>
	<div style="width:1333px" align="center">

   <table width="1333px" cellpadding="0" cellspacing="0" style="border:0px solid black" >
   <thead>
       <table width="100%" cellpadding="0" cellspacing="0" style="border:0px solid black">
           <tr>
               <td width="20px">
                    <table width="100%" cellpadding="0" cellspacing="0" style="border:0px solid black">
                    <tr>
                    <td width="50" >
					   <? if($report_type==1)
                       {
                           if($link == 1)
                           {
                       ?>
                                <img  src='../../../<? echo $imge_arr[$cbo_company_name]; ?>' height='30%' width='50%' />

                       <?
                           }
                           else
                           {
                       ?>
                                <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='30%' width='50%' />
                       <?	}
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
                    echo $company_library[$cbo_company_name]; ?>
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
                         <?php echo $report_title; ?>
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
                            <td>  Revised No:&nbsp; <?php echo $revised_no; ?>  </td>
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
						 ?>
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
					<td  width="110" >:&nbsp;<? echo $buyer_name_arr[$buyer_id]; ?></td>
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
					<td width="110">:&nbsp;<? echo $tot_po_quantity; ?></td>
                     <td style="font-size:12px" ><b>Delivery To </b>   </td>
					<td style="">:&nbsp;
					<?
						//echo $attention;
					?>
					</td>

				</tr>
                <tr>

                    <td width="100" style="font-size:12px"><b>Season</b> </td>
					<td width="110">:&nbsp;<? echo $seasons_names; ?></td>

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
                    <td  style="font-size:12px"><b>Pay mode</b></td>
                	<td>:&nbsp;<? echo $pay_mode[$pay_mode_id];?></td>

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
				</tr>
				<tr>
					<td width="100" style="font-size:12px"><b>Remarks</b>  </td>
					<td width="110" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;" colspan="5">:&nbsp;<? echo $remarks; ?></td>

				</tr>
				</table>
    		</thead>
            <tbody>
            <!-- <div id="page_break_div">

            </div>-->


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
		$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls  where booking_no='$txt_booking_no' and  status_active =1 and is_deleted=0 and job_no='".$nameArray_job_po_row[csf('job_no')]."'   and sensitivity=1 group by pre_cost_fabric_cost_dtls_id,trim_group  order by trim_group ");
	    if(count($nameArray_item)>0){
			$po_ids=rtrim($po_no_arr[$nameArray_job_po_row[csf('job_no')]]['po_id']);
			$po_ids=array_unique(explode(",",$po_ids));
			$ref_nos=rtrim($job_ref_no[$nameArray_job_po_row[csf('job_no')]],',');
			$ref_nos=implode(",",array_unique(explode(",",$ref_nos)));

			$po_no_qty=0;
			$job_no=$nameArray_job_po_row[csf('job_no')];
			foreach($po_ids as $poid)
			{
				$po_no_qty+=$job_po_qty_arr[$job_no][$poid];//$po_quantity[$poid];
			}
        ?>
        &nbsp;
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >

            <tr>
                <td colspan="11" align="">
                <table width="100%" style="table-layout: fixed;">
                <tr>
                <td width="60%" align="left"><strong>As Per Garments Color (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]]; if($ref_nos!='' ) echo " &nbsp;Int Ref.:&nbsp;".$ref_nos;else " "; echo " &nbsp;  Po Qty.:&nbsp;".$po_no_qty; ?></strong></td>
                <td width="40%" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
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
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
                <? if($show_comment==1) {?>
                <td style="border:1px solid black" align="center"><strong>Remarks</strong></td>
                <? }?>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item){
			$i++;
            $nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid, b.description, b.brand_supplier,b.item_color,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=1  and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_color order by bid ");
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
				$uom_id=$order_uom_arr[$result_item[csf('trim_group')]];
				/*if($uom_id==1 || $uom_id==52 || $uom_id==54 || $uom_id==50 || $uom_id==51 || $uom_id==53 || $uom_id==54 || $uom_id==55 || $uom_id==56 || $uom_id==57 || $uom_id==31 || $uom_id==59 || $uom_id==62 || $uom_id==65 || $uom_id==66 || $uom_id==67 || $uom_id==74 || $uom_id==78 || $uom_id==80 )
					{
						echo number_format($result_itemdescription[csf('cons')],0);
					}
					else
					{
						echo number_format($result_itemdescription[csf('cons')],4);
					}*/

				echo number_format($result_itemdescription[csf('cons')],4);
				$item_desctiption_total += $result_itemdescription[csf('cons')] ;
				?>
                </td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_text=$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
				$wo_rate=number_format($result_itemdescription[csf('rate')],4,'.','');
                $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$wo_rate;
				echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
				$trims_remark=$trims_remark_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]]['remark'];
                ?>
                </td>
                  <? if($show_comment==1) {?>
                 <td style="border:1px solid black; text-align:right"><p><? echo $trims_remark; ?> </p></td>
                 <? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="4"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold;"><?
				/*if($uom_id==1 || $uom_id==52 || $uom_id==54 || $uom_id==50 || $uom_id==51 || $uom_id==53 || $uom_id==54 || $uom_id==55 || $uom_id==56 || $uom_id==57 || $uom_id==31 || $uom_id==59 || $uom_id==62 || $uom_id==65 || $uom_id==66 || $uom_id==67 || $uom_id==74 || $uom_id==78 || $uom_id==80 )
					{
						echo number_format($item_desctiption_total ,0);
					}
					else
					{
						echo number_format($item_desctiption_total ,4);
					}*/
					echo number_format($item_desctiption_total ,4);
				 ?></td>
                <td style="border:1px solid black;  text-align:center"><? echo $uom_text; ?></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
                  <? if($show_comment==1) {?>
                <td>&nbsp;  </td>
                <? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="9"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
                 <? if($show_comment==1) {?>
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
		$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls  where booking_no='$txt_booking_no' and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=2 and  status_active =1 and  is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group  order by trim_group ");
		if(count($nameArray_item)>0)
		{
			$po_ids=rtrim($po_no_arr[$nameArray_job_po_row[csf('job_no')]]['po_id']);
			$po_ids=array_unique(explode(",",$po_ids));
			$ref_nos=rtrim($job_ref_no[$nameArray_job_po_row[csf('job_no')]],',');
			$ref_nos=implode(",",array_unique(explode(",",$ref_nos)));
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
                <td width="60%"><strong>Size Sensitive (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]]; echo "&nbsp;&nbsp;Int Ref.:".$ref_nos; echo "&nbsp;&nbsp; Po Qty..:".$po_no_qty; ?></strong></td>
                <td width="40%" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;margin-left:210px; font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
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
                <td align="center" style="border:1px solid black"><strong>Item Size</strong></td>
                <td align="center" style="border:1px solid black"><strong>Qty per Unit</strong></td>
                <td style="border:1px solid black" align="center"><strong>WO Qty.</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
                 <? if($show_comment==1) {?>
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
			/*	$uom_id=$order_uom_arr[$result_item[csf('trim_group')]];
					if($uom_id==1 || $uom_id==52 || $uom_id==54 || $uom_id==50 || $uom_id==51 || $uom_id==53 || $uom_id==54 || $uom_id==55 || $uom_id==56 || $uom_id==57 || $uom_id==31 || $uom_id==59 || $uom_id==62 || $uom_id==65 || $uom_id==66 || $uom_id==67 || $uom_id==74 || $uom_id==78 || $uom_id==80 )
					{
						 echo number_format($result_itemdescription[csf('cons')],0);
					}
					else
					{
						 echo number_format($result_itemdescription[csf('cons')],4);
					}*/
					  echo number_format($result_itemdescription[csf('cons')],4);

                $item_desctiption_total += $result_itemdescription[csf('cons')] ;
				?>
                </td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_text=$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">

                <?
				$wo_rate=number_format($result_itemdescription[csf('rate')],4,'.','');
                $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$wo_rate;
				echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
				$trims_remark=$trims_remark_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]]['remark'];
                ?>
                </td>
                 <? if($show_comment==1) {?>
                <td style="border:1px solid black; text-align:right"><p><? echo $trims_remark; ?> </p></td>
                <? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="5"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold"><?
			/*	if($uom_id==1 || $uom_id==52 || $uom_id==54 || $uom_id==50 || $uom_id==51 || $uom_id==53 || $uom_id==54 || $uom_id==55 || $uom_id==56 || $uom_id==57 || $uom_id==31 || $uom_id==59 || $uom_id==62 || $uom_id==65 || $uom_id==66 || $uom_id==67 || $uom_id==74 || $uom_id==78 || $uom_id==80 )
					{
						 echo number_format($item_desctiption_total,0);
					}
					else
					{
						echo number_format($item_desctiption_total,4);
					}*/
					echo number_format($item_desctiption_total,4);
				 ?></td>
                 <td style="border:1px solid black;  text-align:center"><? echo $uom_text; ?></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
                 <? if($show_comment==1) {?>
                <td>&nbsp; </td>
                <? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="10"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
                 <? if($show_comment==1) {?>
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
		 $nameArray_item=sql_select( "select pre_cost_fabric_cost_dtls_id, trim_group from wo_booking_dtls  where booking_no='$txt_booking_no' and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=3 and  status_active =1 and is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group order by trim_group ");
		if(count($nameArray_item)>0)
		{
			$po_ids=rtrim($po_no_arr[$nameArray_job_po_row[csf('job_no')]]['po_id']);
			$po_ids=array_unique(explode(",",$po_ids));
			$ref_nos=rtrim($job_ref_no[$nameArray_job_po_row[csf('job_no')]],',');
			$ref_nos=implode(",",array_unique(explode(",",$ref_nos)));

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
                <td width="60%"><strong>Contrast Color (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]];echo " &nbsp;Int Ref.:&nbsp;".$ref_nos; echo " &nbsp;Po Qty.:&nbsp;".$po_no_qty; ?></strong></td>
                <td width="40%" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap; font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
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
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
                 <? if($show_comment==1) {?>
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
                <td style="border:1px solid black"><? if($result_itemdescription[csf('brand_supplier')]){echo $result_itemdescription[csf('brand_supplier')];} ?> </td>
                <td style="border:1px solid black; text-align:left">
               <? echo $color_library[$result_itemdescription[csf('item_color')]]; ?>
                </td>
                <td style="border:1px solid black; text-align:left">
               <?
			   echo $color_library[$result_itemdescription[csf('color_number_id')]];
			   ?>
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
				/*$uom_id=$order_uom_arr[$result_item[csf('trim_group')]];
				if($uom_id==1 || $uom_id==52 || $uom_id==54 || $uom_id==50 || $uom_id==51 || $uom_id==53 || $uom_id==54 || $uom_id==55 || $uom_id==56 || $uom_id==57 || $uom_id==31 || $uom_id==59 || $uom_id==62 || $uom_id==65 || $uom_id==66 || $uom_id==67 || $uom_id==74 || $uom_id==78 || $uom_id==80 )
				{
					echo number_format($result_itemdescription[csf('cons')],0);
				}
				else
				{
					echo number_format($result_itemdescription[csf('cons')],4);
				}*/
				echo number_format($result_itemdescription[csf('cons')],4);
                $item_desctiption_total += $result_itemdescription[csf('cons')] ;
				?>
                </td>
                 <td style="border:1px solid black; text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_text=$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
				$wo_rate=number_format($result_itemdescription[csf('rate')],4,'.','');
                $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$wo_rate;echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
				$trims_remark=$trims_remark_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]]['remark'];
                ?>
                </td>
                 <? if($show_comment==1) {?>
                <td style="border:1px solid black; text-align:right"><p><? echo $trims_remark; ?></p> </td>
                <? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="5"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold;"><?
			/*	if($uom_id==1 || $uom_id==52 || $uom_id==54 || $uom_id==50 || $uom_id==51 || $uom_id==53 || $uom_id==54 || $uom_id==55 || $uom_id==56 || $uom_id==57 || $uom_id==31 || $uom_id==59 || $uom_id==62 || $uom_id==65 || $uom_id==66 || $uom_id==67 || $uom_id==74 || $uom_id==78 || $uom_id==80 )
				{
					echo number_format($item_desctiption_total,0);
				}
				else
				{
					echo number_format($item_desctiption_total,4);
				}*/
				echo number_format($item_desctiption_total,4);
				 ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $uom_text; ?></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
                 <? if($show_comment==1) {?>
                <td>&nbsp;  </td>
                <? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="10"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
                 <? if($show_comment==1) {?>
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
		$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls  where booking_no='$txt_booking_no' and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=4 and  status_active =1 and is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group order by trim_group");
	   if(count($nameArray_item)>0)
		{
			$po_ids=rtrim($po_no_arr[$nameArray_job_po_row[csf('job_no')]]['po_id']);
			$po_ids=array_unique(explode(",",$po_ids));

			$ref_nos=rtrim($job_ref_no[$nameArray_job_po_row[csf('job_no')]],',');
			$ref_nos=implode(",",array_unique(explode(",",$ref_nos)));
			$po_no_qty=0;
			$job_no=$nameArray_job_po_row[csf('job_no')];
			foreach($po_ids as $poid)
			{
				$po_no_qty+=$job_po_qty_arr[$job_no][$poid];
			}
        ?>

        <table border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="14" align="">
                <table width="100%" style="table-layout: fixed;">
                <tr>
                <td width="60%" ><strong>Color & size sensitive (<? echo "Job NO: ".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]] ;echo "&nbsp;&nbsp;Int Ref.:&nbsp;".$ref_nos; echo "&nbsp;&nbsp;Po Qty.:&nbsp;".$po_no_qty;?></strong></td>
                <td width="40%" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap; font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
                </tr>
                </table>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group </strong> </td>


                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Brand/Supplier Ref.</strong> </td>
                <td style="border:1px solid black"><strong>Article No.</strong> </td>
                 <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <td style="border:1px solid black; width:170px;"><strong>Gmts Color</strong> </td>
                <td style="border:1px solid black;"><strong>Gmts Size</strong> </td>

                <td align="center" style="border:1px solid black"><strong>Item Size</strong></td>
                <td align="center" style="border:1px solid black"><strong>Qty per Unit</strong></td>
                <td style="border:1px solid black" align="center"><strong>WO Qty.</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
                 <? if($show_comment==1) {?>
                <td style="border:1px solid black" align="center"><strong>Remarks</strong></td>
                <? } ?>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
				$gmtcolor_library=return_library_array( "select b.item_color,b.color_number_id from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.sensitivity=4    and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by trim_group ", "item_color", "color_number_id"  );

			 $nameArray_color=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid, b.color_number_id as gmt_color, b.item_color as color_number_id,b.item_size,b.gmts_sizes,b.description, b.brand_supplier,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount, min(c.color_order) as color_order, min(c.size_order) as size_order,c.article_number from wo_booking_dtls a, wo_trim_book_con_dtls b,wo_po_color_size_breakdown c  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and b.po_break_down_id=c.po_break_down_id and b.color_number_id=c.color_number_id and b.gmts_sizes=c.size_number_id and  c.id=b.color_size_table_id and a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and a.sensitivity=4 and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.color_number_id, b.item_color,b.item_size,b.gmts_sizes,b.description, b.brand_supplier,c.article_number order by c.article_number,color_order,size_order");



            $nameArray_item_description=sql_select( "select distinct uom from wo_booking_dtls  where booking_no='$txt_booking_no' and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=4 and trim_group=".$result_item[csf('trim_group')]." and pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and  status_active =1 and is_deleted=0 order by trim_group ");
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
					?>

					<td style="border:1px solid black" ><? if($result_color[csf('description')]){echo $result_color[csf('description')];} ?> </td>
					<td style="border:1px solid black" ><? if($result_color[csf('brand_supplier')]){echo $result_color[csf('brand_supplier')]; }?> </td>
                    <td style="border:1px solid black">
					<?
					if($result_color[csf('article_number')]!="no article"){
						echo $result_color[csf('article_number')];
					}else{
						echo "-";
					}
					?>
                    </td>
                    <td style="border:1px solid black"><? echo $color_library[$result_color[csf('color_number_id')]]; ?> </td>
                    <td style="border:1px solid black"><? echo $color_library[$result_color[csf('gmt_color')]];?> </td>
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
				/*	$uom_id=$order_uom_arr[$order_uom_arr[$result_item[csf('trim_group')]]];
					if($uom_id==1 || $uom_id==52 || $uom_id==54 || $uom_id==50 || $uom_id==51 || $uom_id==53 || $uom_id==54 || $uom_id==55 || $uom_id==56 || $uom_id==57 || $uom_id==31 || $uom_id==59 || $uom_id==62 || $uom_id==65 || $uom_id==66 || $uom_id==67 || $uom_id==74 || $uom_id==78 || $uom_id==80 )
					{
						echo number_format($result_color[csf('cons')],0);
					}
					else
					{
						echo number_format($result_color[csf('cons')],4);
					}*/
					echo number_format($result_color[csf('cons')],4);

					$item_desctiption_total += $result_color[csf('cons')] ;
					?>
                    </td>
					<td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_text=$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]];  ?></td>
					<td style="border:1px solid black; text-align:right">
					<?
					echo number_format($result_color[csf('rate')],4);
					?>
                     </td>
					<td style="border:1px solid black; text-align:right">
					<?
					$amount_as_per_gmts_color =$result_color[csf('amount')];
					echo number_format($amount_as_per_gmts_color,4);
					$total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
					$trims_remark=$trims_remark_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]]['remark'];
					?>
					</td>
                     <? if($show_comment==1) {?>
                    <td style="border:1px solid black;text-align:center"><? echo $trims_remark; ?> </td>
                    <? } ?>
				</tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="10"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold;"><?
			/*	if($uom_id==1 || $uom_id==52 || $uom_id==54 || $uom_id==50 || $uom_id==51 || $uom_id==53 || $uom_id==54 || $uom_id==55 || $uom_id==56 || $uom_id==57 || $uom_id==31 || $uom_id==59 || $uom_id==62 || $uom_id==65 || $uom_id==66 || $uom_id==67 || $uom_id==74 || $uom_id==78 || $uom_id==80 )
					{
						echo number_format($item_desctiption_total,0);
					}
					else
					{
						echo number_format($item_desctiption_total,4);
					}
					*/
				echo number_format($item_desctiption_total,4);  ?></td>
                <td style="border:1px solid black;  text-align:center"><? echo $uom_text; ?></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
                 <? if($show_comment==1) {?>
                <td>&nbsp;  </td>
                <? } ?>
            </tr>
            <?
            }

            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="13"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
                 <? if($show_comment==1) {?>
                <td>&nbsp;  </td>
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
		$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls  where booking_no='$txt_booking_no' and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=0 and  status_active =1 and is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group order by trim_group ");
		if(count($nameArray_item)>0)
		{
			$po_ids=rtrim($po_no_arr[$nameArray_job_po_row[csf('job_no')]]['po_id']);
			$po_ids=array_unique(explode(",",$po_ids));
			$ref_nos=rtrim($job_ref_no[$nameArray_job_po_row[csf('job_no')]],',');
			$ref_nos=implode(",",array_unique(explode(",",$ref_nos)));
			$po_no_qty=0;
			$job_no=$nameArray_job_po_row[csf('job_no')];
			foreach($po_ids as $poid)
			{
				//echo $poid.', ';
				$po_no_qty+=$job_po_qty_arr[$job_no][$poid];
			}

			//$po_quantity[$result_job[csf('id')]];
        ?>
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="14" align="">
                <table width="100%" style="table-layout: fixed;">
                <tr>
                <td width="60%"><strong>NO sensitive  (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]];echo " &nbsp;Int Ref.:&nbsp;".$ref_nos;  echo " &nbsp;Po Qty.:&nbsp;".$po_no_qty; ?></strong></td>
                <td width="40%" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap; font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
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
                 <td align="center" style="border:1px solid black"><strong>Qty per Unit</strong></td>
                <td align="center" style="border:1px solid black"><strong> Qnty</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
                 <? if($show_comment==1) {?>
                 <td style="border:1px solid black" align="center"><strong>Remarks</strong></td>
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
                <td style="border:1px solid black"><? echo $color_library[$result_itemdescription[csf('item_color')]]; ?> </td>
                <td style="border:1px solid black; text-align:left">
               <?
			   $trims_remark=$trims_remark_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]]['remark'];
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
						$nameArray_color_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trim_group=". $result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and  b.description='". $result_itemdescription[csf('description')]."' and b.brand_supplier='".$result_itemdescription[csf('brand_supplier')]."' and b.item_color='".$result_itemdescription[csf('item_color')]."'");
						}
						if($db_type==2)
				        {
						$nameArray_color_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trim_group=". $result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and nvl( b.description,0)=nvl('". $result_itemdescription[csf('description')]."',0) and nvl(b.brand_supplier,0)=nvl('".$result_itemdescription[csf('brand_supplier')]."',0) and nvl(b.item_color,0)=nvl('".$result_itemdescription[csf('item_color')]."',0)");
						}

                foreach($nameArray_color_size_qnty as $result_color_size_qnty)
                {
                ?>
                <td style="border:1px solid black; text-align:right">
                <?
				//$uom_arr=array(1,52,50,51,53,54,55,56,57,31,59,62,65,66,67,74,78,80);
				//print_r($uom_arr);
				$uom_id=$order_uom_arr[$result_item[csf('trim_group')]];
				//echo $order_uom_id.'DD';
                if($result_color_size_qnty[csf('cons')]!= "")
                {

					/*	if($uom_id==1 || $uom_id==52 || $uom_id==54 || $uom_id==50 || $uom_id==51 || $uom_id==53 || $uom_id==54 || $uom_id==55 || $uom_id==56 || $uom_id==57 || $uom_id==31 || $uom_id==59 || $uom_id==62 || $uom_id==65 || $uom_id==66 || $uom_id==67 || $uom_id==74 || $uom_id==78 || $uom_id==80 )
						{
							 echo number_format($result_color_size_qnty[csf('cons')],0);
						}
						else
						{
							echo number_format($result_color_size_qnty[csf('cons')],4);
						}*/
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
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('amount')]/$item_desctiption_total,4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
				$wo_rate=number_format($result_itemdescription[csf('rate')],4,'.','');
                $amount_as_per_gmts_color = $result_itemdescription[csf('amount')];//$item_desctiption_total*$wo_rate;
				echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
                 <? if($show_comment==1) {?>
                 <td style="border:1px solid black; text-align:right"><? echo $trims_remark; ?> </td>
                 <? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="4"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold;">
                <?
                if($color_tatal !='')
                {
					/*	if($uom_id==1 || $uom_id==52 || $uom_id==54 || $uom_id==50 || $uom_id==51 || $uom_id==53 || $uom_id==54 || $uom_id==55 || $uom_id==56 || $uom_id==57 || $uom_id==31 || $uom_id==59 || $uom_id==62 || $uom_id==65 || $uom_id==66 || $uom_id==67 || $uom_id==74 || $uom_id==78 || $uom_id==80 )
						{
						 echo number_format($color_tatal,0);
						}
						else
						{
							 echo number_format($color_tatal,4);
						}*/
						 echo number_format($color_tatal,4);
                }
                ?>
                </td>
                <td style="border:1px solid black;  text-align:center"><? echo $uom_text; ?></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
                 <? if($show_comment==1) {?>
                <td>&nbsp;  </td>
                <? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="9"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
                 <? if($show_comment==1) {?>
                <td>&nbsp;  </td>
                <? } ?>
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
       <br/>
       <table width="100%" style="margin-top:1px">
       <tr>
       <td>
       <table width="100%" class="rpt_table"  border="1" cellpadding="0" cellspacing="0" rules="all">
            <tr style="border:1px solid black;">
                <td width="30%" style="border:1px solid black; text-align:left">Total Booking Amount</td>
                <td width="70%" style="border:1px solid black; text-align:left"><? echo number_format($booking_grand_total,2);?></td>
            </tr>
            <tr style="border:1px solid black;">
                <td width="30%" style="border:1px solid black; text-align:left">Total Booking Amount (in word)</td>
                <td width="70%" style="border:1px solid black;"><? echo number_to_words(def_number_format($booking_grand_total,2,""),$mcurrency, $dcurrency);?></td>
            </tr>
       </table>
       </td>
       </tr>
       </table>
         <br/>
        <table width="100%">
        <tr>
        <td width="49%">
        <?
        	echo get_spacial_instruction($txt_booking_no);
        ?>
    	</td>
    <td width="2%"></td>
    <?
	 $data_array=sql_select("select b.approved_by,b.approved_no, b.approved_date, c.user_full_name from  wo_booking_mst a , approval_history b, user_passwd c where a.id=b.mst_id and b.approved_by=c.id and a.booking_no='$txt_booking_no' and b.entry_form=8 and  a.status_active =1 and a.is_deleted=0");
	?>
    <td width="49%" valign="top">
        <table  width="100%" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">

            <tr style="border:1px solid black;">
                <td colspan="3" style="border:1px solid black;">Approval Status</td>
                </tr>
                <tr style="border:1px solid black;">
                <td width="3%" style="border:1px solid black;">Sl</td><td width="50%" style="border:1px solid black;">Name</td><td width="27%" style="border:1px solid black;">Approval Date</td><td width="20%" style="border:1px solid black;">Approval No</td>
                </tr>


            <?
			$i;
			foreach($data_array as $row){
			?>
            <tr style="border:1px solid black;">
                <td width="3%" style="border:1px solid black;"><? echo $i;?></td><td width="50%" style="border:1px solid black;"><? echo $row[csf('user_full_name')];?></td><td width="27%" style="border:1px solid black;"><? echo change_date_format($row[csf('approved_date')],"dd-mm-yyyy","-");?></td><td width="20%" style="border:1px solid black;"><? echo $row[csf('approved_no')];?></td>
                </tr>
                <?
				$i++;
			}
				?>

        </table>
    </td>
    </tr>
    </table>

   	 </tbody>
     </table>

    </div>
    <div class="footer_signature" style="margin-top:-5px;">
         <?
          echo signature_table(132, $cbo_company_name, "850px",1);
		 ?>
     </div>
	
	  <? 
	$emailBody=ob_get_contents();
	//ob_clean();
	if($is_mail_send==1){
		$req_approved=return_field_value("id", "ELECTRONIC_APPROVAL_SETUP", "PAGE_ID = 336 and is_deleted=0");
		$is_approved=return_field_value("IS_APPROVED", "WO_BOOKING_MST", "STATUS_ACTIVE = 1 and is_deleted=0 and booking_no='$txt_booking_no'");
		if($req_approved && $is_approved==1){
			$emailBody.="<h1 style='border:1px sloid #0ff;'>Approved</h1>";
		}
		elseif($req_approved && $is_approved==0){
			$emailBody.="<h1 style='border:1px sloid #0ff;'>Draft</h1>";
		}
		
		$mailArr=array();
		if($mail_id!=''){$mailArr[]=$mail_id;}
		if($supplier_mail_arr[$supplier_id]!=''){$mailArr[]=$supplier_mail_arr[$supplier_id];}
		
		
		$to=implode(',',$mailArr);
		$subject="Trims Booking Auto Mail";
		
		if($to!=""){
			require '../../../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
			require_once('../../../auto_mail/setting/mail_setting.php');
			$header=mailHeader();
			sendMailMailer( $to, $subject, $emailBody );
		}
	}
?>
        <br>
      <div id="page_break_div">
   	 </div>
     <br>
    <div>

		<?
        	//echo signature_table(2, $cbo_company_name, "1330px");
			echo "****".custom_file_name($txt_booking_no,$style_sting,$job_no);
        ?>
    </div>

	<?
    if($link == 1){
        ?>
        <script type="text/javascript" src="../../../js/jquery.js"></script>
        <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
    <?
    }else {
        ?>
         <script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
        <?
    }
        ?>
    <script>
    fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');

    </script>
  </html>
	<?
	exit();
}

if($action=="show_trim_booking_report2")
{

	extract($_REQUEST);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$id_approved_id=str_replace("'","",$id_approved_id);
	$report_type=str_replace("'","",$report_type);
	$show_comment=str_replace("'","",$show_comment);

	$color_library=return_library_array("select id, color_name from lib_color", "id", "color_name");
	$size_library=return_library_array("select id, size_name from  lib_size", "id", "size_name");
	$company_library=return_library_array("select id, company_name from lib_company", "id", "company_name");
	$trim_group_library= return_library_array("select id, item_name from lib_item_group",'id','item_name');
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');

	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$order_uom_arr=return_library_array("select id,order_uom  from lib_item_group","id","order_uom");
	$deling_marcent_arr=return_library_array("select id,team_member_name from lib_mkt_team_member_info","id","team_member_name");
	$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");

	$nameArray_approved=sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no='$txt_booking_no' and b.entry_form=8 and  a.status_active =1 and a.is_deleted=0");
	list($nameArray_approved_row)=$nameArray_approved;
	//$revised_no_data=sql_select("select a.revised_no  from wo_booking_mst a where  a.booking_no=$txt_booking_no and  a.status_active =1 and a.is_deleted=0");
	//list($revised_no_row)=$revised_no_data;
		$booking_grand_total=0;
		$currency_id="";


		$buyer_string=array();
		$style_owner=array();
		$job_no=array();
		$style_ref=array();
		$all_dealing_marcent=array();
		$season=array();
		$order_repeat_no=array();
		$po_id_arr=array();

		$nameArray_buyer=sql_select( "select  a.style_ref_no, a.job_no, a.style_owner, a.buyer_name, a.dealing_marchant, a.season, a.season_matrix, a.season_buyer_wise, a.order_repeat_no, b.po_break_down_id from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no='$txt_booking_no' and b.status_active =1 and b.is_deleted=0");
        foreach ($nameArray_buyer as $result_buy){
			$buyer_string[$result_buy[csf('buyer_name')]]=$buyer_name_arr[$result_buy[csf('buyer_name')]];
			$style_owner[$result_buy[csf('job_no')]]=$company_library[$result_buy[csf('style_owner')]];
			$job_no[$result_buy[csf('job_no')]]=$result_buy[csf('job_no')];
			$style_ref[$result_buy[csf('job_no')]]=$result_buy[csf('style_ref_no')];
			$all_dealing_marcent[$result_buy[csf('job_no')]]=$deling_marcent_arr[$result_buy[csf('dealing_marchant')]];
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
		$job_no=implode(",",$job_no);
		$seasons_names=rtrim($seasons_name,',');

		$seasons_names=implode(",",array_unique(explode(",",$seasons_names)));
		$poid_arr=array_unique($po_id_arr);

		$order_rept_no=rtrim($order_rept_no,',');
		$order_rept_no=implode(",",array_unique(explode(",",$order_rept_no)));

		$po_no=array();
		$file_no=array();
		$ref_no=array();
		$po_quantity=array();
		$pub_shipment_date='';$int_ref_no='';$tot_po_quantity=0;$po_idss='';
		$nameArray_job=sql_select( "select b.job_no_mst,b.id,b.pub_shipment_date, b.po_number,b.grouping, b.file_no, sum(b.po_quantity) as po_quantity  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no='$txt_booking_no' and  a.status_active =1 and a.is_deleted=0 group by b.job_no_mst,b.id,b.pub_shipment_date, b.po_number,b.grouping, b.file_no ");
        foreach ($nameArray_job as $result_job){
			$po_no[$result_job[csf('job_no_mst')]][$result_job[csf('id')]]=$result_job[csf('po_number')];
			$file_no[$result_job[csf('id')]]=$result_job[csf('file_no')];
			$ref_no[$result_job[csf('id')]]=$result_job[csf('grouping')];
			$po_quantity[$result_job[csf('id')]]=$result_job[csf('po_quantity')];
		//	$tot_po_quantity+=$result_job[csf('po_quantity')];
			$job_ref_no[$result_job[csf('job_no_mst')]].=$result_job[csf('grouping')].',';
			$po_no_arr[$result_job[csf('job_no_mst')]]['po_id'].=$result_job[csf('id')].',';
			$pub_shipment_date.=$result_job[csf('pub_shipment_date')].',';
			$int_ref_no.=$result_job[csf('grouping')].',';
			if($po_idss=='') $po_idss=$result_job[csf('id')];else $po_idss.=",".$result_job[csf('id')];

		}
		$sql_job=sql_select( "select b.job_no_mst,b.id as po_id, b.po_quantity as po_quantity  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and  a.status_active =1 and a.is_deleted=0 and  b.status_active =1 and b.is_deleted=0 and b.id in(".$po_idss.") ");
		foreach ($sql_job as $row)
		{
			$job_po_qty_arr[$row[csf('job_no_mst')]][$row[csf('po_id')]]+=$row[csf('po_quantity')];
			$tot_po_quantity+=$row[csf('po_quantity')];
		}

        $nameArray=sql_select( "select a.booking_no, a.pay_mode,a.buyer_id, a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.source, a.remarks, a.revised_no, a.delivery_address from wo_booking_mst a where a.booking_no='$txt_booking_no' and a.status_active =1 and a.is_deleted=0");
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
			$delivery_address=$row[csf('delivery_address')];
			$revised_no=$row[csf('revised_no')];
			$source_id=$row[csf('source')];
		}

	ob_start();
	?>
	<html>
	<head>
	  <style type="text/css" media="print">

		 @media print
	        {
	            tbody {
	                page-break-inside: avoid;
					tbody {display: table-row-group;}
	            }
	            thead {
	                display: table-header-group;
					page-break-before: always;

	            }
	        }
	/*	@media print {
				  #page_break_div {
					page-break-before: always;
				  }
		}
			*/	.footer_signature {
					position: fixed;
					height: auto;
					bottom:0;
					width:100%;

					}
				@media print {
					table {
						page-break-inside: avoid;
					}
				}
				/*@media all {
	  			#page_break_div   { display: none; }
				}

				@media screen {
				thead { display: block; }
				tfoot { display: block; }
				}*/

	</style>
	</head>
   	<table width="1333px"  cellpadding="0" cellspacing="0" style="border:0px solid black" >
    <thead>
    <tr>
    <th>
       	 	<table width="100%"  cellpadding="0" cellspacing="0" style="border:0px solid black">
       		<tr>
                <td width="20px">
                    <table width="100%" cellpadding="0" cellspacing="0" style="border:0px solid black">
                    <tr>
                    <td width="50" >
                       <? if($report_type==1)
                       {
                           if($link == 1)
                           {
                       ?>
                                <img  src='../../../<? echo $imge_arr[$cbo_company_name]; ?>' height='30%' width='50%' />

                       <?
                           }
                           else
                           {
                       ?>
                                <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='30%' width='50%' />
                       <?	}
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
                    echo $company_library[$cbo_company_name]; ?>
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
                         <?php echo $report_title; ?>
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
                            <td>  Revised No:&nbsp; <?php echo $revised_no; ?>  </td>
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
                         ?>
                      </table>

                     </td>
                   </tr>
                   </table>
                </td>
        	</tr>
            <tr>
            <td>

            	<table width="100%"  style="border:0px solid black;table-layout: fixed;">
                <tr>
                    <td colspan="6" valign="top"> </td>
                </tr>
                <tr>
                    <td width="100" style="font-size:18px" align="left"><span><b>To, </b></span>  </td>
                    <td width="110" colspan="5" style="font-size:18px">&nbsp;<span></span></td>
                </tr>
            	<tr>
                    <td width="210" colspan="2" align="left" style="font-size:18px">&nbsp; <b>
                    <?
                    if($pay_mode_id==5 || $pay_mode_id==3){
                        echo $company_library[$supplier_id];
                    }
                    else{
                        echo $supplier_name_arr[$supplier_id];
                    }
                    ?></b>
                    </td>
                    <td  width="100" style="font-size:12px"><b>Buyer</b></td>
                    <td  width="110" align="left">:&nbsp;<? echo $buyer_name_arr[$buyer_id]; ?></td>
                    <td width="100" style="font-size:12px"><b>Delivery Date</b></td>
                    <td width="110" align="left">:&nbsp;<?  echo change_date_format($delivery_date); ?></td>
           	   </tr>
              <tr>
                <td width="100" colspan="2" rowspan="2" align="left" style="font-size:18px">Address :&nbsp;
                <b>
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
                <td width="100" style="font-size:12px"><b>PO Qty.</b> </td>
                <td width="110" align="left">:&nbsp;<? echo $tot_po_quantity; ?></td>
                <td style="font-size:12px" ><b>Delivery To </b> </td>
                <td style="" align="left">:&nbsp;
                <?
                    //echo $attention;
                ?>
                </td>
             </tr>
            <tr>
                <td width="100" style="font-size:12px"><b>Season</b> </td>
                <td width="110" align="left">:&nbsp;<? echo $seasons_names; ?></td>
                <td width="100" style="font-size:12px"><b>Currency</b></td>
                <td width="110" align="left">:&nbsp;<?  echo $currency[$currency_id]; ?></td>
            </tr>
            <tr>
                <td width="100" style="font-size:12px" align="left"><b>Attention </b>   </td>
                <td align="left" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;">:&nbsp;
                <?
                    echo $attention;
                ?>
                </td>
                <td width="100" style="font-size:12px"><b>Order Repeat </b> </td>
                <td width="110" align="left">:&nbsp;<? echo $order_rept_no; ?></td>
                <td  style="font-size:12px"><b>Pay mode</b></td>
                <td align="left">:&nbsp;<? echo $pay_mode[$pay_mode_id];?></td>
            </tr>
             <tr>
                <td width="100" style="font-size:12px" align="left"><b>Source</b></td>
                <td align="left" >:&nbsp;<? echo $source[$source_id];?></td>
                <td style="font-size:12px"><b>Dealing Merchant</b></td>
                <td align="left" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;">:&nbsp;
                <?
                    echo implode(",",array_unique($all_dealing_marcent));
                ?>
                </td>
            </tr>
            <tr>
	        	 <td width="100" style="font-size:12px" align="left"><b>Delivery Address</b> </td>
	            <td  align="left">:&nbsp; <? echo $delivery_address;?></td>
                <td style="font-size:12px"><b>Remarks</b>  </td>
                <td align="left" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;" colspan="3">:&nbsp;<? echo $remarks; ?></td>
            </tr>
            </table>
            </td>
            </tr>
          </table>
    </th>
    </tr>
    </thead>
     <tbody>
          <!--==============================================AS PER GMTS COLOR START=========================================  -->
		<?

				$details_data_arr=sql_select("select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid, b.description, b.brand_supplier,b.item_size,b.article_number,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount ,sum(b.moq) as moq,sum(pp_sample) as pp_sample ,a.sensitivity,a.job_no	from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and a.booking_no='$txt_booking_no'  	and b.requirment !=0 and a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_size,b.article_number,a.sensitivity,a.job_no order by bid ");

				foreach($details_data_arr as $val){
						$sensitivity_arr[$val[csf('job_no')]][$val[csf('sensitivity')]]['moq']+=$val[csf('moq')];
						$sensitivity_arr[$val[csf('job_no')]][$val[csf('sensitivity')]]['pp_sample']+=$val[csf('pp_sample')];
				}
				
				// echo "<pre>";
				// print_r($sensitivity_arr);




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
		$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls  where booking_no='$txt_booking_no' and  status_active =1 and is_deleted=0 and job_no='".$nameArray_job_po_row[csf('job_no')]."'   and sensitivity=1 group by pre_cost_fabric_cost_dtls_id,trim_group  order by trim_group ");
	    if(count($nameArray_item)>0){
			$po_ids=rtrim($po_no_arr[$nameArray_job_po_row[csf('job_no')]]['po_id']);
			$po_ids=array_unique(explode(",",$po_ids));
			$ref_nos=rtrim($job_ref_no[$nameArray_job_po_row[csf('job_no')]],',');
			$ref_nos=implode(",",array_unique(explode(",",$ref_nos)));

			$po_no_qty=0;
			$job_no=$nameArray_job_po_row[csf('job_no')];
			foreach($po_ids as $poid)
			{
				$po_no_qty+=$job_po_qty_arr[$job_no][$poid];//$po_quantity[$poid];
			}

			$moq=$sensitivity_arr[$job_no][1]['moq'];
			$pp_sample=$sensitivity_arr[$job_no][1]['pp_sample'];
        ?>

        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="11" align="">
                <table width="100%" style="table-layout: fixed;">
                <tr>
                <td width="60%" align="left"><strong>As Per Garments Color (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]]; if($ref_nos!='' ) echo " &nbsp;Int Ref.:&nbsp;".$ref_nos;else " "; echo " &nbsp;  Po Qty.:&nbsp;".$po_no_qty; ?></strong></td>
                <td width="40%" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
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
				<?
				if($moq>0){?>  
					<td style="border:1px solid black" align="center"><strong>MOQ</strong></td>
				<?}
				if($pp_sample>0){?>
                <td style="border:1px solid black" align="center"><strong>PP Sample</strong></td>
				<?}?>

                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
                <? if($show_comment==1) {?>
                <td style="border:1px solid black" align="center"><strong>Remarks</strong></td>
                <? }?>
            </tr>
            <?
			$i=0;$m=0;$p=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item){
			$i++;
            $nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid, b.description, b.brand_supplier,b.item_color,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount,sum(b.moq) as moq,sum(pp_sample) as pp_sample  from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=1  and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_color order by bid ");

			
			
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
				$item_desctiption_total += $result_itemdescription[csf('cons')] ;
				?>
                </td>
					<?
				if($moq>0){?>  
				<td style="border:1px solid black; text-align:right"><?=number_format($result_itemdescription[csf('moq')],4,'.','');?></td>
			   		<?}
				if($pp_sample>0){?>
				<td style="border:1px solid black; text-align:right"><?=number_format($result_itemdescription[csf('pp_sample')],4,'.','');?></td>
			   		<?}?>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_text=$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
				$wo_rate=number_format($result_itemdescription[csf('rate')],4,'.','');
                $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$wo_rate;
				echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
				$trims_remark=$trims_remark_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]]['remark'];
                ?>
                </td>
                  <? if($show_comment==1) {?>
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
				<? if($moq>0){	$m=1;?>  
				<td style="border:1px solid black" align="center"><strong></strong></td>
				<?}	if($pp_sample>0){$p=1;?>
                <td style="border:1px solid black" align="center"><strong></strong></td>
				<?}?>

                <td style="border:1px solid black;  text-align:center"><? echo $uom_text; ?></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
                  <? if($show_comment==1) {?>
                <td>&nbsp;  </td>
                <? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<?=17+$m+$p;?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
                 <? if($show_comment==1) {?>
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
		$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls  where booking_no='$txt_booking_no' and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=2 and  status_active =1 and  is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group  order by trim_group ");
		
		if(count($nameArray_item)>0)
		{
			$po_ids=rtrim($po_no_arr[$nameArray_job_po_row[csf('job_no')]]['po_id']);
			$po_ids=array_unique(explode(",",$po_ids));
			$ref_nos=rtrim($job_ref_no[$nameArray_job_po_row[csf('job_no')]],',');
			$ref_nos=implode(",",array_unique(explode(",",$ref_nos)));
			$po_no_qty=0;
			$job_no=$nameArray_job_po_row[csf('job_no')];
			foreach($po_ids as $poid)
			{
				$po_no_qty+=$job_po_qty_arr[$job_no][$poid];
			}

			$moq=$sensitivity_arr[$job_no][2]['moq'];
			$pp_sample=$sensitivity_arr[$job_no][2]['pp_sample'];
        ?>

        <table border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="12" align="">
                 <table width="100%" style="table-layout: fixed;">
                <tr>
                <td width="60%"><strong>Size Sensitive (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]]; echo "&nbsp;&nbsp;Int Ref.:".$ref_nos; echo "&nbsp;&nbsp; Po Qty..:".$po_no_qty; ?></strong></td>
                <td width="40%" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;margin-left:210px; font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
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
                <td align="center" style="border:1px solid black"><strong>Item Size</strong></td>
                <td align="center" style="border:1px solid black"><strong>Qty per Unit</strong></td>
                <td style="border:1px solid black" align="center"><strong>WO Qty.</strong></td>	
				<?
				if($moq>0){?>
				<td style="border:1px solid black" align="center"><strong>MOQ</strong></td>
				<?}
				if($pp_sample>0){?>
				<td style="border:1px solid black" align="center"><strong>PP Sample</strong></td>
				<?}?>

                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
                 <? if($show_comment==1) {?>
                <td style="border:1px solid black" align="center"><strong>Remarks</strong></td>
                <? } ?>
            </tr>
            <?
			$i=0;$m=0;$p=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
			$i++;
			$nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid, b.description, b.brand_supplier,b.item_size,b.article_number,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount,sum(b.moq) as moq,sum(pp_sample) as pp_sample  from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and   a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.sensitivity=2 and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_size,b.article_number order by bid");
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
                <td style="border:1px solid black">
				<?
				if($result_itemdescription[csf('article_number')]!="no article"){
					echo $result_itemdescription[csf('article_number')];
				}else{
					echo "-";
				}
				?>
                </td>
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
				
				<?
				if($moq>0){?>  
				<td style="border:1px solid black; text-align:right"><?=number_format($result_itemdescription[csf('moq')],4,'.','');?></td>
				<?}
				if($pp_sample>0){?>
				<td style="border:1px solid black; text-align:right"><?=number_format($result_itemdescription[csf('pp_sample')],4,'.','');?></td>
				<?}?>

                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_text=$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">

                <?
				$wo_rate=number_format($result_itemdescription[csf('rate')],4,'.','');
                $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$wo_rate;
				echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
				$trims_remark=$trims_remark_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]]['remark'];
                ?>
                </td>
                 <? if($show_comment==1) {?>
                <td style="border:1px solid black; text-align:right"><p><? echo $trims_remark; ?> </p></td>
                <? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="5"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold"><?

					echo number_format($item_desctiption_total,4);
				 ?></td>
				 <? if($moq>0){	$m=1;?>  
				<td style="border:1px solid black" align="center"><strong></strong></td>
				<?}	if($pp_sample>0){$p=1;?>
                <td style="border:1px solid black" align="center"><strong></strong></td>
				<?}?>
                 <td style="border:1px solid black;  text-align:center"><? echo $uom_text; ?></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
                 <? if($show_comment==1) {?>
                <td>&nbsp; </td>
                <? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<?=10+$m+$p;?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
                 <? if($show_comment==1) {?>
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
		 $nameArray_item=sql_select( "select pre_cost_fabric_cost_dtls_id, trim_group from wo_booking_dtls  where booking_no='$txt_booking_no' and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=3 and  status_active =1 and is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group order by trim_group ");
		if(count($nameArray_item)>0)
		{
			$po_ids=rtrim($po_no_arr[$nameArray_job_po_row[csf('job_no')]]['po_id']);
			$po_ids=array_unique(explode(",",$po_ids));
			$ref_nos=rtrim($job_ref_no[$nameArray_job_po_row[csf('job_no')]],',');
			$ref_nos=implode(",",array_unique(explode(",",$ref_nos)));

			$po_no_qty=0;
			$job_no=$nameArray_job_po_row[csf('job_no')];
			foreach($po_ids as $poid)
			{
				$po_no_qty+=$job_po_qty_arr[$job_no][$poid];
			}
			
		$moq=$sensitivity_arr[$job_no][3]['moq'];
		$pp_sample=$sensitivity_arr[$job_no][3]['pp_sample'];
        ?>
        <table border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="12" align="">
                <table width="100%" style="table-layout: fixed;">
                <tr>
                <td width="60%"><strong>Contrast Color (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]];echo " &nbsp;Int Ref.:&nbsp;".$ref_nos; echo " &nbsp;Po Qty.:&nbsp;".$po_no_qty; ?></strong></td>
                <td width="40%" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap; font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
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
				<?
				if($moq>0){?>
				<td style="border:1px solid black" align="center"><strong>MOQ</strong></td>
				<?}
				if($pp_sample>0){?>
				<td style="border:1px solid black" align="center"><strong>PP Sample</strong></td>
				<?}?>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
                 <? if($show_comment==1) {?>
                <td style="border:1px solid black" align="center"><strong>Remarks</strong></td>
                <? } ?>
            </tr>
            <?
			$i=0;$m=0;$p=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
				$gmtcolor_library=return_library_array( "select b.item_color,b.color_number_id from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=3  and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by trim_group ", "item_color", "color_number_id"  );

			$nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid,b.description, b.brand_supplier,b.item_color,b.color_number_id,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount,sum(b.moq) as moq,sum(pp_sample) as pp_sample  from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.sensitivity=3  and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_color,b.color_number_id order by bid ");
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
                <td style="border:1px solid black; text-align:left">
               <? echo $color_library[$result_itemdescription[csf('item_color')]]; ?>
                </td>
                <td style="border:1px solid black; text-align:left">
               <?
			   echo $color_library[$result_itemdescription[csf('color_number_id')]];
			   ?>
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
				
				<?
				if($moq>0){?>  
				<td style="border:1px solid black; text-align:right"><?=number_format($result_itemdescription[csf('moq')],4,'.','');?></td>
				<?}
				if($pp_sample>0){?>
				<td style="border:1px solid black; text-align:right"><?=number_format($result_itemdescription[csf('pp_sample')],4,'.','');?></td>
				<?}?>
                 <td style="border:1px solid black; text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_text=$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
                $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
				$trims_remark=$trims_remark_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]]['remark'];
                ?>
                </td>
                 <? if($show_comment==1) {?>
                <td style="border:1px solid black; text-align:right"><p><? echo $trims_remark; ?></p> </td>
                <? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="5"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold;"><?
				echo number_format($item_desctiption_total,4);
				 ?></td>
				 <? if($moq>0){	$m=1;?>  
				<td style="border:1px solid black" align="center"><strong></strong></td>
				<?}	if($pp_sample>0){$p=1;?>
                <td style="border:1px solid black" align="center"><strong></strong></td>
				<?}?>
                <td style="border:1px solid black;text-align:center"><? echo $uom_text; ?></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
                 <? if($show_comment==1) {?>
                <td>&nbsp;  </td>
                <? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<?=10+$m+$p;?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
                 <? if($show_comment==1) {?>
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
		$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls  where booking_no='$txt_booking_no' and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=4 and  status_active =1 and is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group order by trim_group");
	   if(count($nameArray_item)>0)
		{
			$po_ids=rtrim($po_no_arr[$nameArray_job_po_row[csf('job_no')]]['po_id']);
			$po_ids=array_unique(explode(",",$po_ids));

			$ref_nos=rtrim($job_ref_no[$nameArray_job_po_row[csf('job_no')]],',');
			$ref_nos=implode(",",array_unique(explode(",",$ref_nos)));
			$po_no_qty=0;
			$job_no=$nameArray_job_po_row[csf('job_no')];
			foreach($po_ids as $poid)
			{
				$po_no_qty+=$job_po_qty_arr[$job_no][$poid];
			}
			$moq=$sensitivity_arr[$job_no][4]['moq'];
			$pp_sample=$sensitivity_arr[$job_no][4]['pp_sample'];
        ?>

        <table border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="14" align="">
                <table width="100%" style="table-layout: fixed;">
                <tr>
                <td width="60%" ><strong>Color & size sensitive (<? echo "Job NO: ".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]] ;echo "&nbsp;&nbsp;Int Ref.:&nbsp;".$ref_nos; echo "&nbsp;&nbsp;Po Qty.:&nbsp;".$po_no_qty;?></strong></td>
                <td width="40%" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap; font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
                </tr>
                </table>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group </strong> </td>


                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Brand/Supplier Ref.</strong> </td>
                <td style="border:1px solid black"><strong>Article No.</strong> </td>
                 <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <td style="border:1px solid black; width:170px;"><strong>Gmts Color</strong> </td>
                <td style="border:1px solid black;"><strong>Gmts Size</strong> </td>

                <td align="center" style="border:1px solid black"><strong>Item Size</strong></td>
                <td align="center" style="border:1px solid black"><strong>Qty per Unit</strong></td>
                <td style="border:1px solid black" align="center"><strong>WO Qty.</strong></td>
				<?
				if($moq>0){?>
				<td style="border:1px solid black" align="center"><strong>MOQ</strong></td>
				<?}
				if($pp_sample>0){?>
				<td style="border:1px solid black" align="center"><strong>PP Sample</strong></td>
				<?}?>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
                 <? if($show_comment==1) {?>
                <td style="border:1px solid black" align="center"><strong>Remarks</strong></td>
                <? } ?>
            </tr>
            <?
			$i=0;$m=0;$p=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
				$gmtcolor_library=return_library_array( "select b.item_color,b.color_number_id from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.sensitivity=4    and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by trim_group ", "item_color", "color_number_id"  );

			 $nameArray_color=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid, b.color_number_id as gmt_color, b.item_color as color_number_id,b.item_size,b.gmts_sizes,b.description, b.brand_supplier,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount, min(c.color_order) as color_order, min(c.size_order) as size_order,c.article_number,sum(b.moq) as moq,sum(pp_sample) as pp_sample  from wo_booking_dtls a, wo_trim_book_con_dtls b,wo_po_color_size_breakdown c  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and b.po_break_down_id=c.po_break_down_id and b.color_number_id=c.color_number_id and b.gmts_sizes=c.size_number_id and  c.id=b.color_size_table_id and a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and a.sensitivity=4 and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.color_number_id, b.item_color,b.item_size,b.gmts_sizes,b.description, b.brand_supplier,c.article_number order by c.article_number,color_order,size_order");



            $nameArray_item_description=sql_select( "select distinct uom from wo_booking_dtls  where booking_no='$txt_booking_no' and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=4 and trim_group=".$result_item[csf('trim_group')]." and pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and  status_active =1 and is_deleted=0 order by trim_group ");
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
					?>

					<td style="border:1px solid black" ><? if($result_color[csf('description')]){echo $result_color[csf('description')];} ?> </td>
					<td style="border:1px solid black" ><? if($result_color[csf('brand_supplier')]){echo $result_color[csf('brand_supplier')]; }?> </td>
                    <td style="border:1px solid black">
					<?
					if($result_color[csf('article_number')]!="no article"){
						echo $result_color[csf('article_number')];
					}else{
						echo "-";
					}
					?>
                    </td>
                    <td style="border:1px solid black"><? echo $color_library[$result_color[csf('color_number_id')]]; ?> </td>
                    <td style="border:1px solid black"><? echo $color_library[$result_color[csf('gmt_color')]];?> </td>
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
					echo number_format($result_color[csf('cons')],3);
					$item_desctiption_total +=number_format($result_color[csf('cons')],3) ;
					?>
                    </td>
					<?
					if($moq>0){?>  
					<td style="border:1px solid black; text-align:right"><?=number_format($result_color[csf('moq')],3,'.','');?></td>
					<?}
					if($pp_sample>0){?>
					<td style="border:1px solid black; text-align:right"><?=number_format($result_color[csf('pp_sample')],3,'.','');?></td>
					<?}?>
					<td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_text=$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]];  ?></td>
					<td style="border:1px solid black; text-align:right">
					<?
					echo number_format($result_color[csf('rate')],3);
					?>
                     </td>
					<td style="border:1px solid black; text-align:right">
					<?
					$amount_as_per_gmts_color =$result_color[csf('amount')];
					echo number_format($amount_as_per_gmts_color,3);
					$total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
					$trims_remark=$trims_remark_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]]['remark'];
					?>
					</td>
                     <? if($show_comment==1) {?>
                    <td style="border:1px solid black;text-align:center"><? echo $trims_remark; ?> </td>
                    <? } ?>
				</tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="10"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold;"><?
				echo $item_desctiption_total;  ?></td>
				 <? if($moq>0){	$m=1;?>  
				<td style="border:1px solid black" align="center"><strong></strong></td>
				<?}	if($pp_sample>0){$p=1;?>
                <td style="border:1px solid black" align="center"><strong></strong></td>
				<?}?>
                <td style="border:1px solid black;  text-align:center"><? echo $uom_text; ?></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
                 <? if($show_comment==1) {?>
                <td>&nbsp;  </td>
                <? } ?>
            </tr>
            <?
            }

            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<?=13+$m+$p;?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
                 <? if($show_comment==1) {?>
                <td>&nbsp;  </td>
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
		$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls  where booking_no='$txt_booking_no' and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=0 and  status_active =1 and is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group order by trim_group ");
		if(count($nameArray_item)>0)
		{
			$po_ids=rtrim($po_no_arr[$nameArray_job_po_row[csf('job_no')]]['po_id']);
			$po_ids=array_unique(explode(",",$po_ids));
			$ref_nos=rtrim($job_ref_no[$nameArray_job_po_row[csf('job_no')]],',');
			$ref_nos=implode(",",array_unique(explode(",",$ref_nos)));
			$po_no_qty=0;
			$job_no=$nameArray_job_po_row[csf('job_no')];
			foreach($po_ids as $poid)
			{
				//echo $poid.', ';
				$po_no_qty+=$job_po_qty_arr[$job_no][$poid];
			}

			$moq=$sensitivity_arr[$job_no][0]['moq'];
			$pp_sample=$sensitivity_arr[$job_no][0]['pp_sample'];
			//$po_quantity[$result_job[csf('id')]];
        ?>
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="14" align="">
                    <table width="100%" style="table-layout: fixed;">
                    <tr>
                    <td width="60%"><strong>NO sensitive  (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]];echo " &nbsp;Int Ref.:&nbsp;".$ref_nos;  echo " &nbsp;Po Qty.:&nbsp;".$po_no_qty; ?></strong></td>
                    <td width="40%" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap; font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
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
                <td align="center" style="border:1px solid black"><strong>Qty per Unit</strong></td>
                <td align="center" style="border:1px solid black"><strong> Qnty</strong></td>
				<?
				if($moq>0){?>
				<td style="border:1px solid black" align="center"><strong>MOQ</strong></td>
				<?}
				if($pp_sample>0){?>
				<td style="border:1px solid black" align="center"><strong>PP Sample</strong></td>
				<?}?>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
                 <? if($show_comment==1) {?>
                 <td style="border:1px solid black" align="center"><strong>Remarks</strong></td>
                 <? } ?>
            </tr>
            <?
			$i=0;$m=0;$p=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;

				$nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_color,avg(b.rate) as rate, sum(b.amount) as amount,sum(b.moq) as moq,sum(pp_sample) as pp_sample,b.id  from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and a.sensitivity=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_color,b.id order by b.id");
				
				$item_wise_detail=array();
				foreach($nameArray_item_description as $val){
					
					$item_wise_detail[$result_item[csf('trim_group')]][$val[csf('description')]]['description']=$val[csf('description')];
					$item_wise_detail[$result_item[csf('trim_group')]][$val[csf('description')]]['rate']=$val[csf('rate')];
					$item_wise_detail[$result_item[csf('trim_group')]][$val[csf('description')]]['amount']+=$val[csf('amount')];
					$item_wise_detail[$result_item[csf('trim_group')]][$val[csf('description')]]['moq']=$val[csf('moq')];
					$item_wise_detail[$result_item[csf('trim_group')]][$val[csf('description')]]['pp_sample']=$val[csf('pp_sample')];
					$item_wise_detail[$result_item[csf('trim_group')]][$val[csf('description')]]['brand_supplier']=$val[csf('brand_supplier')];
					$item_wise_detail[$result_item[csf('trim_group')]][$val[csf('description')]]['pre_cost_fabric_cost_dtls_id']=$val[csf('pre_cost_fabric_cost_dtls_id')];
					$item_wise_detail[$result_item[csf('trim_group')]][$val[csf('description')]]['item_color']=$val[csf('item_color')];

				}
				
				?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($item_wise_detail[$result_item[csf('trim_group')]])+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($item_wise_detail[$result_item[csf('trim_group')]])+1; ?>">

                <?
				echo $trim_group_library[$result_item[csf('trim_group')]]."<br/>";
				echo implode(",",$booking_country_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][0]);

				?>
                </td>
                <?
                $color_tatal=0;
                $total_amount_as_per_gmts_color=0;
                foreach($item_wise_detail[$result_item[csf('trim_group')]] as $result_itemdescription)
                {
                $item_desctiption_total=0;
                ?>
                <td style="border:1px solid black"><? if($result_itemdescription['description']){ echo $result_itemdescription['description'];} ?> </td>
                <td style="border:1px solid black"><? if($result_itemdescription[csf('brand_supplier')]){echo $result_itemdescription[csf('brand_supplier')];} ?> </td>
                <td style="border:1px solid black"><? echo $color_library[$result_itemdescription[csf('item_color')]]; ?> </td>
                <td style="border:1px solid black; text-align:left">
               <?
			   $trims_remark=$trims_remark_arr[$result_itemdescription['pre_cost_fabric_cost_dtls_id']]['remark'];
			   $calUom=$trims_qtyPerUnit_arr[$result_itemdescription['pre_cost_fabric_cost_dtls_id']][2];
			   $calQty=explode("_",$trims_qtyPerUnit_arr[$result_itemdescription['pre_cost_fabric_cost_dtls_id']][1]);
			   if($calUom && end($calQty)){
				   echo "1".$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]."=".prev($calQty)." ".$calUom;
			   }
			   ?>
                </td>
                <?
				if($db_type==0)
				{
				$nameArray_color_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trim_group=". $result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and  b.description='". $result_itemdescription['description']."' and b.brand_supplier='".$result_itemdescription['brand_supplier']."' and b.item_color='".$result_itemdescription['item_color']."'");
				}
				if($db_type==2)
				{
				$nameArray_color_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trim_group=". $result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and nvl( b.description,0)=nvl('". $result_itemdescription['description']."',0) and nvl(b.brand_supplier,0)=nvl('".$result_itemdescription['brand_supplier']."',0) and nvl(b.item_color,0)=nvl('".$result_itemdescription['item_color']."',0)");
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

				<?
				if($moq>0){?>  
				<td style="border:1px solid black; text-align:right"><?=number_format($result_itemdescription['moq'],4,'.','');?></td>
				<?}
				if($pp_sample>0){?>
				<td style="border:1px solid black; text-align:right"><?=number_format($result_itemdescription['pp_sample'],4,'.','');?></td>
				<?}?>

                <td style="border:1px solid black; text-align:center "><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_text=$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription['amount']/$item_desctiption_total,4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
				$wo_rate=number_format($result_itemdescription['rate'],4,'.','');
                $amount_as_per_gmts_color = $result_itemdescription['amount'];//$item_desctiption_total* $wo_rate;
				echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
                 <? if($show_comment==1) {?>
                 <td style="border:1px solid black; text-align:right"><? echo $trims_remark; ?> </td>
                 <? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="4"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold;">
                <?
                if($color_tatal !='')
                {

						 echo number_format($color_tatal,4);
                }
                ?>
                </td>
				<? if($moq>0){	$m=1;?>  
				<td style="border:1px solid black" align="center"><strong></strong></td>
				<?}	if($pp_sample>0){$p=1;?>
                <td style="border:1px solid black" align="center"><strong></strong></td>
				<?}?>
                <td style="border:1px solid black;  text-align:center"><? echo $uom_text; ?></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
                 <? if($show_comment==1) {?>
                <td>&nbsp;  </td>
                <? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<?=9+$m+$p;?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
                 <? if($show_comment==1) {?>
                <td>&nbsp;  </td>
                <? } ?>
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
       <br/>
       <table width="100%" style="margin-top:1px">
           <tr>
           <td>
           <table width="100%" class="rpt_table"  border="1" cellpadding="0" cellspacing="0" rules="all">
                <tr style="border:1px solid black;">
                    <td width="30%" style="border:1px solid black; text-align:left">Total Booking Amount</td>
                    <td width="70%" style="border:1px solid black; text-align:left"><? echo number_format($booking_grand_total,2);?></td>
                </tr>
                <tr style="border:1px solid black;">
                    <td width="30%" style="border:1px solid black; text-align:left">Total Booking Amount (in word)</td>
                    <td width="70%" style="border:1px solid black;"><? echo number_to_words(def_number_format($booking_grand_total,2,""),$mcurrency, $dcurrency);?></td>
                </tr>
           </table>
           </td>
           </tr>
       </table>
         <br/>
            <table width="100%">
                <tr>
                <td width="49%">
                <?
                    echo get_spacial_instruction($txt_booking_no);
                ?>
                </td>
                <td width="2%"></td>
            <?
             $data_array=sql_select("select b.approved_by,b.approved_no, b.approved_date, c.user_full_name from  wo_booking_mst a , approval_history b, user_passwd c where a.id=b.mst_id and b.approved_by=c.id and a.booking_no='$txt_booking_no' and b.entry_form=8 and  a.status_active =1 and a.is_deleted=0");
            ?>
                <td width="49%" valign="top">
                    <table  width="100%" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">

                        <tr style="border:1px solid black;">
                            <td colspan="3" style="border:1px solid black;">Approval Status</td>
                            </tr>
                            <tr style="border:1px solid black;">
                            <td width="3%" style="border:1px solid black;">Sl</td><td width="50%" style="border:1px solid black;">Name</td><td width="27%" style="border:1px solid black;">Approval Date</td><td width="20%" style="border:1px solid black;">Approval No</td>
                            </tr>


                        <?
                        $i;
                        foreach($data_array as $row){
                        ?>
                        <tr style="border:1px solid black;">
                            <td width="3%" style="border:1px solid black;"><? echo $i;?></td><td width="50%" style="border:1px solid black;"><? echo $row[csf('user_full_name')];?></td><td width="27%" style="border:1px solid black;"><? echo change_date_format($row[csf('approved_date')],"dd-mm-yyyy","-");?></td><td width="20%" style="border:1px solid black;"><? echo $row[csf('approved_no')];?></td>
                            </tr>
                            <?
                            $i++;
                        }
                            ?>

                    </table>
            </td>
            </tr>
        </table>

   	 </tbody>
     </table>
     <!--class="footer_signature"-->
    <div  style="margin-top:-50px;">
         <?
          echo signature_table(132, $cbo_company_name, "1333px",1);
		 ?>
   </div>
   <? 
	$emailBody=ob_get_contents();
	//ob_clean();
	if($is_mail_send==1){
		$req_approved=return_field_value("id", "ELECTRONIC_APPROVAL_SETUP", "PAGE_ID = 336 and is_deleted=0");
		$is_approved=return_field_value("IS_APPROVED", "WO_BOOKING_MST", "STATUS_ACTIVE = 1 and is_deleted=0 and booking_no='$txt_booking_no'");
		if($req_approved && $is_approved==1){
			$emailBody.="<h1 style='border:1px sloid #0ff;'>Approved</h1>";
		}
		elseif($req_approved && $is_approved==0){
			$emailBody.="<h1 style='border:1px sloid #0ff;'>Draft</h1>";
		}
		
	
		$mailArr=array();
		if($mail_id!=''){$mailArr[]=$mail_id;}
		if($supplier_mail_arr[$supplier_id]!=''){$mailArr[]=$supplier_mail_arr[$supplier_id];}
		
		
		$to=implode(',',$mailArr);
		$subject="Trims Booking Auto Mail";
		
		if($to!=""){
			require '../../../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
			require_once('../../../auto_mail/setting/mail_setting.php');
			$header=mailHeader();
			sendMailMailer( $to, $subject, $emailBody );
		}
	}
?>
      <div id="page_break_div">
   	 </div>
    <div>

		<?
        	//echo signature_table(2, $cbo_company_name, "1330px");
			//echo "****".custom_file_name($txt_booking_no,$style_sting,$job_no);
        ?>
    </div>

  </html>
	<?
	$user_id=$_SESSION['logic_erp']['user_id'];
	$report_cat=100;
	$html = ob_get_contents();
		ob_clean();
		//$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
		foreach (glob("tb*.xls") as $filename) {
		//if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename="tb".$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc, $html);
		echo "$filename****$html****$report_cat";
	//exit();
}

if($action=="show_trim_booking_report5")
{

	extract($_REQUEST);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$id_approved_id=str_replace("'","",$id_approved_id);
	$report_type=str_replace("'","",$report_type);
	$show_comment=str_replace("'","",$show_comment);

	$color_library=return_library_array("select id, color_name from lib_color", "id", "color_name");
	$size_library=return_library_array("select id, size_name from  lib_size", "id", "size_name");
	$company_library=return_library_array("select id, company_name from lib_company", "id", "company_name");
	$trim_group_library= return_library_array("select id, item_name from lib_item_group",'id','item_name');
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');

	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$order_uom_arr=return_library_array("select id,order_uom  from lib_item_group","id","order_uom");
	$deling_marcent_arr=return_library_array("select id,team_member_name from lib_mkt_team_member_info","id","team_member_name");
	$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");

	$nameArray_approved=sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no='$txt_booking_no' and b.entry_form=8 and  a.status_active =1 and a.is_deleted=0");
	list($nameArray_approved_row)=$nameArray_approved;
	//$revised_no_data=sql_select("select a.revised_no  from wo_booking_mst a where  a.booking_no=$txt_booking_no and  a.status_active =1 and a.is_deleted=0");
	//list($revised_no_row)=$revised_no_data;
		$booking_grand_total=0;
		$currency_id="";


		$buyer_string=array();
		$style_owner=array();
		$job_no=array();
		$style_ref=array();
		$all_dealing_marcent=array();
		$season=array();
		$order_repeat_no=array();
		$po_id_arr=array();

		$nameArray_buyer=sql_select( "select  a.style_ref_no, a.job_no, a.style_owner, a.buyer_name, a.dealing_marchant, a.season, a.season_matrix, a.season_buyer_wise, a.order_repeat_no, b.po_break_down_id from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no='$txt_booking_no' and b.status_active =1 and b.is_deleted=0");
        foreach ($nameArray_buyer as $result_buy){
			$buyer_string[$result_buy[csf('buyer_name')]]=$buyer_name_arr[$result_buy[csf('buyer_name')]];
			$style_owner[$result_buy[csf('job_no')]]=$company_library[$result_buy[csf('style_owner')]];
			$job_no[$result_buy[csf('job_no')]]=$result_buy[csf('job_no')];
			$style_ref[$result_buy[csf('job_no')]]=$result_buy[csf('style_ref_no')];
			$all_dealing_marcent[$result_buy[csf('job_no')]]=$deling_marcent_arr[$result_buy[csf('dealing_marchant')]];
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
		$job_no=implode(",",$job_no);
		$seasons_names=rtrim($seasons_name,',');

		$seasons_names=implode(",",array_unique(explode(",",$seasons_names)));
		$poid_arr=array_unique($po_id_arr);

		$order_rept_no=rtrim($order_rept_no,',');
		$order_rept_no=implode(",",array_unique(explode(",",$order_rept_no)));

		$po_no=array();
		$file_no=array();
		$ref_no=array();
		$po_quantity=array();
		$pub_shipment_date='';$int_ref_no='';$tot_po_quantity=0;$po_idss='';
		$nameArray_job=sql_select( "select b.job_no_mst,b.id,b.pub_shipment_date, b.po_number,b.grouping, b.file_no, sum(b.po_quantity) as po_quantity  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no='$txt_booking_no' and  a.status_active =1 and a.is_deleted=0 group by b.job_no_mst,b.id,b.pub_shipment_date, b.po_number,b.grouping, b.file_no ");
        foreach ($nameArray_job as $result_job){
			$po_no[$result_job[csf('job_no_mst')]][$result_job[csf('id')]]=$result_job[csf('po_number')];
			$file_no[$result_job[csf('id')]]=$result_job[csf('file_no')];
			$ref_no[$result_job[csf('id')]]=$result_job[csf('grouping')];
			$po_quantity[$result_job[csf('id')]]=$result_job[csf('po_quantity')];
		//	$tot_po_quantity+=$result_job[csf('po_quantity')];
			$job_ref_no[$result_job[csf('job_no_mst')]].=$result_job[csf('grouping')].',';
			$po_no_arr[$result_job[csf('job_no_mst')]]['po_id'].=$result_job[csf('id')].',';
			$pub_shipment_date.=$result_job[csf('pub_shipment_date')].',';
			$int_ref_no.=$result_job[csf('grouping')].',';
			if($po_idss=='') $po_idss=$result_job[csf('id')];else $po_idss.=",".$result_job[csf('id')];

		}
		$sql_job=sql_select( "select b.job_no_mst,b.id as po_id, b.po_quantity as po_quantity  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and  a.status_active =1 and a.is_deleted=0 and  b.status_active =1 and b.is_deleted=0 and b.id in(".$po_idss.") ");
		foreach ($sql_job as $row)
		{
			$job_po_qty_arr[$row[csf('job_no_mst')]][$row[csf('po_id')]]+=$row[csf('po_quantity')];
			$tot_po_quantity+=$row[csf('po_quantity')];
		}

        $nameArray=sql_select( "select a.booking_no, a.pay_mode,a.buyer_id, a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.source, a.remarks, a.revised_no, a.delivery_address from wo_booking_mst a where a.booking_no='$txt_booking_no' and a.status_active =1 and a.is_deleted=0");
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
			$delivery_address=$row[csf('delivery_address')];
			$revised_no=$row[csf('revised_no')];
			$source_id=$row[csf('source')];
		}

	ob_start();
	?>
	<html>
	<head>
	  <style type="text/css" media="print">

		 @media print
	        {
	            tbody {
	                page-break-inside: avoid;
					tbody {display: table-row-group;}
	            }
	            thead {
	                display: table-header-group;
					page-break-before: always;

	            }
	        }
	/*	@media print {
				  #page_break_div {
					page-break-before: always;
				  }
		}
			*/	.footer_signature {
					position: fixed;
					height: auto;
					bottom:0;
					width:100%;

					}
				@media print {
					table {
						page-break-inside: avoid;
					}
				}
				/*@media all {
	  			#page_break_div   { display: none; }
				}

				@media screen {
				thead { display: block; }
				tfoot { display: block; }
				}*/

	</style>
	</head>
   	<table width="1333px"  cellpadding="0" cellspacing="0" style="border:0px solid black" >
    <thead>
    <tr>
    <th>
       	 	<table width="100%"  cellpadding="0" cellspacing="0" style="border:0px solid black">
       		<tr>
                <td width="20px">
                    <table width="100%" cellpadding="0" cellspacing="0" style="border:0px solid black">
                    <tr>
                    <td width="50" >
                       <? if($report_type==1)
                       {
                           if($link == 1)
                           {
                       ?>
                                <img  src='../../../<? echo $imge_arr[$cbo_company_name]; ?>' height='30%' width='50%' />

                       <?
                           }
                           else
                           {
                       ?>
                                <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='30%' width='50%' />
                       <?	}
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
                    echo $company_library[$cbo_company_name]; ?>
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
                         <?php echo $report_title; ?>
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
                            <td>  Revised No:&nbsp; <?php echo $revised_no; ?>  </td>
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
                         ?>
                      </table>

                     </td>
                   </tr>
                   </table>
                </td>
        	</tr>
            <tr>
            <td>

            	<table width="100%"  style="border:0px solid black;table-layout: fixed;">
                <tr>
                    <td colspan="6" valign="top"> </td>
                </tr>
                <tr>
                    <td width="100" style="font-size:18px" align="left"><span><b>To, </b></span>  </td>
                    <td width="110" colspan="5" style="font-size:18px">&nbsp;<span></span></td>
                </tr>
            	<tr>
                    <td width="210" colspan="2" align="left" style="font-size:18px">&nbsp; <b>
                    <?
                    if($pay_mode_id==5 || $pay_mode_id==3){
                        echo $company_library[$supplier_id];
                    }
                    else{
                        echo $supplier_name_arr[$supplier_id];
                    }
                    ?></b>
                    </td>
                    <td  width="100" style="font-size:12px"><b>Buyer</b></td>
                    <td  width="110" align="left">:&nbsp;<? echo $buyer_name_arr[$buyer_id]; ?></td>
                    <td width="100" style="font-size:12px"><b>Delivery Date</b></td>
                    <td width="110" align="left">:&nbsp;<?  echo change_date_format($delivery_date); ?></td>
           	   </tr>
              <tr>
                <td width="100" colspan="2" rowspan="2" align="left" style="font-size:18px">Address :&nbsp;
                <b>
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
                <td width="100" style="font-size:12px"><b>PO Qty.</b> </td>
                <td width="110" align="left">:&nbsp;<? echo $tot_po_quantity; ?></td>
                <td style="font-size:12px" ><b>Delivery To </b> </td>
                <td style="" align="left">:&nbsp;
                <?
                    //echo $attention;
                ?>
                </td>
             </tr>
            <tr>
                <td width="100" style="font-size:12px"><b>Season</b> </td>
                <td width="110" align="left">:&nbsp;<? echo $seasons_names; ?></td>
                <td width="100" style="font-size:12px"><b>Currency</b></td>
                <td width="110" align="left">:&nbsp;<?  echo $currency[$currency_id]; ?></td>
            </tr>
            <tr>
                <td width="100" style="font-size:12px" align="left"><b>Attention </b>   </td>
                <td align="left" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;">:&nbsp;
                <?
                    echo $attention;
                ?>
                </td>
                <td width="100" style="font-size:12px"><b>Order Repeat </b> </td>
                <td width="110" align="left">:&nbsp;<? echo $order_rept_no; ?></td>
                <td  style="font-size:12px"><b>Pay mode</b></td>
                <td align="left">:&nbsp;<? echo $pay_mode[$pay_mode_id];?></td>
            </tr>
             <tr>
                <td width="100" style="font-size:12px" align="left"><b>Source</b></td>
                <td align="left" >:&nbsp;<? echo $source[$source_id];?></td>
                <td style="font-size:12px"><b>Dealing Merchant</b></td>
                <td align="left" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;">:&nbsp;
                <?
                    echo implode(",",array_unique($all_dealing_marcent));
                ?>
                </td>
            </tr>
            <tr>
	        	 <td width="100" style="font-size:12px" align="left"><b>Delivery Address</b> </td>
	            <td  align="left">:&nbsp; <? echo $delivery_address;?></td>
                <td style="font-size:12px"><b>Remarks</b>  </td>
                <td align="left" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;" colspan="3">:&nbsp;<? echo $remarks; ?></td>
            </tr>
            </table>
            </td>
            </tr>
          </table>
    </th>
    </tr>
    </thead>
     <tbody>
          <!--==============================================AS PER GMTS COLOR START=========================================  -->
		<?

				$details_data_arr=sql_select("select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid, b.description, b.brand_supplier,b.item_size,b.article_number,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount ,sum(b.moq) as moq,sum(pp_sample) as pp_sample ,a.sensitivity,a.job_no	from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and a.booking_no='$txt_booking_no'  	and b.requirment !=0 and a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_size,b.article_number,a.sensitivity,a.job_no order by bid ");

				foreach($details_data_arr as $val){
						$sensitivity_arr[$val[csf('job_no')]][$val[csf('sensitivity')]]['moq']+=$val[csf('moq')];
						$sensitivity_arr[$val[csf('job_no')]][$val[csf('sensitivity')]]['pp_sample']+=$val[csf('pp_sample')];
				}
				
				// echo "<pre>";
				// print_r($sensitivity_arr);




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
		$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls  where booking_no='$txt_booking_no' and  status_active =1 and is_deleted=0 and job_no='".$nameArray_job_po_row[csf('job_no')]."'   and sensitivity=1 group by pre_cost_fabric_cost_dtls_id,trim_group  order by trim_group ");
	    if(count($nameArray_item)>0){
			$po_ids=rtrim($po_no_arr[$nameArray_job_po_row[csf('job_no')]]['po_id']);
			$po_ids=array_unique(explode(",",$po_ids));
			$ref_nos=rtrim($job_ref_no[$nameArray_job_po_row[csf('job_no')]],',');
			$ref_nos=implode(",",array_unique(explode(",",$ref_nos)));

			$po_no_qty=0;
			$job_no=$nameArray_job_po_row[csf('job_no')];
			foreach($po_ids as $poid)
			{
				$po_no_qty+=$job_po_qty_arr[$job_no][$poid];//$po_quantity[$poid];
			}

			$moq=$sensitivity_arr[$job_no][1]['moq'];
			$pp_sample=$sensitivity_arr[$job_no][1]['pp_sample'];
        ?>

        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="13" align="">
                <table width="100%" style="table-layout: fixed;">
                <tr>
                <td width="60%" align="left"><strong>As Per Garments Color (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]]; if($ref_nos!='' ) echo " &nbsp;Int Ref.:&nbsp;".$ref_nos;else " "; echo " &nbsp;  Po Qty.:&nbsp;".$po_no_qty; ?></strong></td>
                <td width="40%" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
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
				<td align="center" style="border:1px solid black"><strong>Cons</strong></td>
				<td align="center" style="border:1px solid black"><strong>Ex%</strong></td>
                <td align="center" style="border:1px solid black"><strong>Qty per Unit</strong></td>
                <td style="border:1px solid black" align="center"><strong>WO Qty</strong></td>
				<?
				if($moq>0){?>  
					<td style="border:1px solid black" align="center"><strong>MOQ</strong></td>
				<?}
				if($pp_sample>0){?>
                <td style="border:1px solid black" align="center"><strong>PP Sample</strong></td>
				<?}?>

                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
                <? if($show_comment==1) {?>
                <td style="border:1px solid black" align="center"><strong>Remarks</strong></td>
                <? }?>
            </tr>
            <?
			$i=0;$m=0;$p=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item){
			$i++;
            $nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid, b.description, b.brand_supplier,b.item_color,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount,sum(b.moq) as moq,sum(pp_sample) as pp_sample,c.tot_cons,c.ex_per,c.trim_group,c.id  from wo_booking_dtls a,  wo_trim_book_con_dtls b ,wo_pre_cost_trim_cost_dtls c where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and a.trim_group=c.trim_group and c.id=a.pre_cost_fabric_cost_dtls_id and  a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=1  and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_color,c.tot_cons,c.ex_per,c.trim_group,c.id order by bid ");

			
			
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
				<td style="border:1px solid black; text-align:right" ><? echo number_format($result_itemdescription[csf('tot_cons')],6); ?> </td>
				<td style="border:1px solid black; text-align:center">
               <? echo $result_itemdescription[csf('ex_per')]; ?>
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
					<?
				if($moq>0){?>  
				<td style="border:1px solid black; text-align:right"><?=number_format($result_itemdescription[csf('moq')],4,'.','');?></td>
			   		<? }
				if($pp_sample>0){ ?>
				<td style="border:1px solid black; text-align:right"><?=number_format($result_itemdescription[csf('pp_sample')],4,'.','');?></td>
			   		<? } ?>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_text=$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
				$wo_rate=number_format($result_itemdescription[csf('rate')],4,'.','');
                $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$wo_rate;
				echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
				$trims_remark=$trims_remark_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]]['remark'];
                ?>
                </td>
                  <? if($show_comment==1) {?>
                 <td style="border:1px solid black; text-align:right"><p><? echo $trims_remark; ?> </p></td>
                 <? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="6"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold;"><?

					echo number_format($item_desctiption_total ,4);
				 ?></td>
				<? if($moq>0){	$m=1;?>  
				<td style="border:1px solid black" align="center"><strong></strong></td>
				<? }	if($pp_sample>0){$p=1;?>
                <td style="border:1px solid black" align="center"><strong></strong></td>
				<? } ?>

                <td style="border:1px solid black;  text-align:center"><? echo $uom_text; ?></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
                  <? if($show_comment==1) {?>
                <td>&nbsp;  </td>
                <? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<?=11+$m+$p;?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
                 <? if($show_comment==1) {?>
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
		$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls  where booking_no='$txt_booking_no' and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=2 and  status_active =1 and  is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group  order by trim_group ");
		
		if(count($nameArray_item)>0)
		{
			$po_ids=rtrim($po_no_arr[$nameArray_job_po_row[csf('job_no')]]['po_id']);
			$po_ids=array_unique(explode(",",$po_ids));
			$ref_nos=rtrim($job_ref_no[$nameArray_job_po_row[csf('job_no')]],',');
			$ref_nos=implode(",",array_unique(explode(",",$ref_nos)));
			$po_no_qty=0;
			$job_no=$nameArray_job_po_row[csf('job_no')];
			foreach($po_ids as $poid)
			{
				$po_no_qty+=$job_po_qty_arr[$job_no][$poid];
			}

			$moq=$sensitivity_arr[$job_no][2]['moq'];
			$pp_sample=$sensitivity_arr[$job_no][2]['pp_sample'];
        ?>

        <table border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="12" align="">
                 <table width="100%" style="table-layout: fixed;">
                <tr>
                <td width="60%"><strong>Size Sensitive (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]]; echo "&nbsp;&nbsp;Int Ref.:".$ref_nos; echo "&nbsp;&nbsp; Po Qty..:".$po_no_qty; ?></strong></td>
                <td width="40%" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;margin-left:210px; font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
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
                <td align="center" style="border:1px solid black"><strong>Item Size</strong></td>
				<td align="center" style="border:1px solid black"><strong>Cons</strong></td>
				<td align="center" style="border:1px solid black"><strong>Ex%</strong></td>
                <td align="center" style="border:1px solid black"><strong>Qty per Unit</strong></td>
                <td style="border:1px solid black" align="center"><strong>WO Qty.</strong></td>	
				<?
				if($moq>0){?>
				<td style="border:1px solid black" align="center"><strong>MOQ</strong></td>
				<? }
				if($pp_sample>0){?>
				<td style="border:1px solid black" align="center"><strong>PP Sample</strong></td>
				<? } ?>

                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
                 <? if($show_comment==1) {?>
                <td style="border:1px solid black" align="center"><strong>Remarks</strong></td>
                <? } ?>
            </tr>
            <?
			$i=0;$m=0;$p=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
			$i++;
			$nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid, b.description, b.brand_supplier,b.item_size,b.article_number,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount,sum(b.moq) as moq,sum(pp_sample) as pp_sample,c.tot_cons,c.ex_per,c.trim_group,c.id  from wo_booking_dtls a,  wo_trim_book_con_dtls b,wo_pre_cost_trim_cost_dtls c where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and a.trim_group=c.trim_group and c.id=a.pre_cost_fabric_cost_dtls_id and  a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.sensitivity=2 and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_size,b.article_number,c.tot_cons,c.ex_per,c.trim_group,c.id order by bid");
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
                <td style="border:1px solid black">
				<?
				if($result_itemdescription[csf('article_number')]!="no article"){
					echo $result_itemdescription[csf('article_number')];
				}else{
					echo "-";
				}
				?>
                </td>
                <td style="border:1px solid black; text-align:left">
              <? echo $result_itemdescription[csf('item_size')];?>
                </td>
				<td style="border:1px solid black; text-align:right" ><? echo number_format($result_itemdescription[csf('tot_cons')],6); ?> </td>
                <td style="border:1px solid black; text-align:center"><? if($result_itemdescription[csf('ex_per')]){echo $result_itemdescription[csf('ex_per')];} ?> </td>
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
				
				<?
				if($moq>0){?>  
				<td style="border:1px solid black; text-align:right"><?=number_format($result_itemdescription[csf('moq')],4,'.','');?></td>
				<? }
				if($pp_sample>0){?>
				<td style="border:1px solid black; text-align:right"><?=number_format($result_itemdescription[csf('pp_sample')],4,'.','');?></td>
				<? } ?>

                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_text=$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">

                <?
				$wo_rate=number_format($result_itemdescription[csf('rate')],4,'.','');
                $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$wo_rate;
				echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
				$trims_remark=$trims_remark_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]]['remark'];
                ?>
                </td>
                 <? if($show_comment==1) {?>
                <td style="border:1px solid black; text-align:right"><p><? echo $trims_remark; ?> </p></td>
                <? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="7"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold"><?

					echo number_format($item_desctiption_total,4);
				 ?></td>
				 <? if($moq>0){	$m=1;?>  
				<td style="border:1px solid black" align="center"><strong></strong></td>
				<? }	if($pp_sample>0){$p=1;?>
                <td style="border:1px solid black" align="center"><strong></strong></td>
				<? } ?>
                 <td style="border:1px solid black;  text-align:center"><? echo $uom_text; ?></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
                 <? if($show_comment==1) {?>
                <td>&nbsp; </td>
                <? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<?=12+$m+$p;?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
                 <? if($show_comment==1) {?>
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
		 $nameArray_item=sql_select( "select pre_cost_fabric_cost_dtls_id, trim_group from wo_booking_dtls  where booking_no='$txt_booking_no' and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=3 and  status_active =1 and is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group order by trim_group ");
		if(count($nameArray_item)>0)
		{
			$po_ids=rtrim($po_no_arr[$nameArray_job_po_row[csf('job_no')]]['po_id']);
			$po_ids=array_unique(explode(",",$po_ids));
			$ref_nos=rtrim($job_ref_no[$nameArray_job_po_row[csf('job_no')]],',');
			$ref_nos=implode(",",array_unique(explode(",",$ref_nos)));

			$po_no_qty=0;
			$job_no=$nameArray_job_po_row[csf('job_no')];
			foreach($po_ids as $poid)
			{
				$po_no_qty+=$job_po_qty_arr[$job_no][$poid];
			}
			
		$moq=$sensitivity_arr[$job_no][3]['moq'];
		$pp_sample=$sensitivity_arr[$job_no][3]['pp_sample'];
        ?>
        <table border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="14" align="">
                <table width="100%" style="table-layout: fixed;">
                <tr>
                <td width="60%"><strong>Contrast Color (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]];echo " &nbsp;Int Ref.:&nbsp;".$ref_nos; echo " &nbsp;Po Qty.:&nbsp;".$po_no_qty; ?></strong></td>
                <td width="40%" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap; font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
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
				<td align="center" style="border:1px solid black"><strong>Cons</strong></td>
				<td align="center" style="border:1px solid black"><strong>Ex%</strong></td>
                <td align="center" style="border:1px solid black"><strong>Qty per Unit</strong></td>
                <td style="border:1px solid black" align="center"><strong>WO Qty.</strong></td>
				<?
				if($moq>0){?>
				<td style="border:1px solid black" align="center"><strong>MOQ</strong></td>
				<? }
				if($pp_sample>0){?>
				<td style="border:1px solid black" align="center"><strong>PP Sample</strong></td>
				<? } ?>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
                 <? if($show_comment==1) {?>
                <td style="border:1px solid black" align="center"><strong>Remarks</strong></td>
                <? } ?>
            </tr>
            <?
			$i=0;$m=0;$p=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
				$gmtcolor_library=return_library_array( "select b.item_color,b.color_number_id from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=3  and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by trim_group ", "item_color", "color_number_id"  );

			$nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid,b.description, b.brand_supplier,b.item_color,b.color_number_id,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount,sum(b.moq) as moq,sum(pp_sample) as pp_sample,c.tot_cons,c.ex_per,c.trim_group,c.id  from wo_booking_dtls a,  wo_trim_book_con_dtls b,wo_pre_cost_trim_cost_dtls c  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and a.trim_group=c.trim_group and c.id=a.pre_cost_fabric_cost_dtls_id and  a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.sensitivity=3  and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_color,b.color_number_id,c.tot_cons,c.ex_per,c.trim_group,c.id order by bid ");
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
                <td style="border:1px solid black; text-align:left">
               <? echo $color_library[$result_itemdescription[csf('item_color')]]; ?>
                </td>
                <td style="border:1px solid black; text-align:left">
               <?
			   echo $color_library[$result_itemdescription[csf('color_number_id')]];
			   ?>
                </td>
				<td style="border:1px solid black; text-align:right" ><? echo number_format($result_itemdescription[csf('tot_cons')],6); ?> </td>
				<td style="border:1px solid black; text-align:center" ><? echo $result_itemdescription[csf('ex_per')]; ?> </td>
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
				
				<?
				if($moq>0){?>  
				<td style="border:1px solid black; text-align:right"><?=number_format($result_itemdescription[csf('moq')],4,'.','');?></td>
				<? }
				if($pp_sample>0){?>
				<td style="border:1px solid black; text-align:right"><?=number_format($result_itemdescription[csf('pp_sample')],4,'.','');?></td>
				<? } ?>
                 <td style="border:1px solid black; text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_text=$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
                $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
				$trims_remark=$trims_remark_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]]['remark'];
                ?>
                </td>
                 <? if($show_comment==1) {?>
                <td style="border:1px solid black; text-align:right"><p><? echo $trims_remark; ?></p> </td>
                <? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="7"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold;"><?
				echo number_format($item_desctiption_total,4);
				 ?></td>
				 <? if($moq>0){	$m=1;?>  
				<td style="border:1px solid black" align="center"><strong></strong></td>
				<? }	if($pp_sample>0){$p=1;?>
                <td style="border:1px solid black" align="center"><strong></strong></td>
				<? } ?>
                <td style="border:1px solid black;text-align:center"><? echo $uom_text; ?></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
                 <? if($show_comment==1) {?>
                <td>&nbsp;  </td>
                <? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<?=12+$m+$p;?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
                 <? if($show_comment==1) {?>
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
		$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls  where booking_no='$txt_booking_no' and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=4 and  status_active =1 and is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group order by trim_group");
	   if(count($nameArray_item)>0)
		{
			$po_ids=rtrim($po_no_arr[$nameArray_job_po_row[csf('job_no')]]['po_id']);
			$po_ids=array_unique(explode(",",$po_ids));

			$ref_nos=rtrim($job_ref_no[$nameArray_job_po_row[csf('job_no')]],',');
			$ref_nos=implode(",",array_unique(explode(",",$ref_nos)));
			$po_no_qty=0;
			$job_no=$nameArray_job_po_row[csf('job_no')];
			foreach($po_ids as $poid)
			{
				$po_no_qty+=$job_po_qty_arr[$job_no][$poid];
			}
			$moq=$sensitivity_arr[$job_no][4]['moq'];
			$pp_sample=$sensitivity_arr[$job_no][4]['pp_sample'];
        ?>

        <table border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="16" align="">
                <table width="100%" style="table-layout: fixed;">
                <tr>
                <td width="60%" ><strong>Color & size sensitive (<? echo "Job NO: ".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]] ;echo "&nbsp;&nbsp;Int Ref.:&nbsp;".$ref_nos; echo "&nbsp;&nbsp;Po Qty.:&nbsp;".$po_no_qty;?></strong></td>
                <td width="40%" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap; font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
                </tr>
                </table>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group </strong> </td>


                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Brand/Supplier Ref.</strong> </td>
                <td style="border:1px solid black"><strong>Article No.</strong> </td>
                 <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <td style="border:1px solid black; width:170px;"><strong>Gmts Color</strong> </td>
                <td style="border:1px solid black;"><strong>Gmts Size</strong> </td>
                <td align="center" style="border:1px solid black"><strong>Item Size</strong></td>
				<td align="center" style="border:1px solid black"><strong>Cons</strong></td>
				<td align="center" style="border:1px solid black"><strong>Ex%</strong></td>
                <td align="center" style="border:1px solid black"><strong>Qty per Unit</strong></td>
                <td style="border:1px solid black" align="center"><strong>WO Qty.</strong></td>
				<?
				if($moq>0){?>
				<td style="border:1px solid black" align="center"><strong>MOQ</strong></td>
				<? }
				if($pp_sample>0){?>
				<td style="border:1px solid black" align="center"><strong>PP Sample</strong></td>
				<? } ?>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
                 <? if($show_comment==1) {?>
                <td style="border:1px solid black" align="center"><strong>Remarks</strong></td>
                <? } ?>
            </tr>
            <?
			$i=0;$m=0;$p=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
				$gmtcolor_library=return_library_array( "select b.item_color,b.color_number_id from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.sensitivity=4    and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by trim_group ", "item_color", "color_number_id"  );

			 $nameArray_color=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid, b.color_number_id as gmt_color, b.item_color as color_number_id,b.item_size,b.gmts_sizes,b.description, b.brand_supplier,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount, min(c.color_order) as color_order, min(c.size_order) as size_order,c.article_number,sum(b.moq) as moq,sum(pp_sample) as pp_sample,d.tot_cons,d.ex_per,d.trim_group,d.id  from wo_booking_dtls a, wo_trim_book_con_dtls b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and b.po_break_down_id=c.po_break_down_id and b.color_number_id=c.color_number_id and b.gmts_sizes=c.size_number_id and  c.id=b.color_size_table_id and a.trim_group=d.trim_group and d.id=a.pre_cost_fabric_cost_dtls_id and a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and a.sensitivity=4 and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.color_number_id, b.item_color,b.item_size,b.gmts_sizes,b.description, b.brand_supplier,c.article_number,d.tot_cons,d.ex_per,d.trim_group,d.id order by c.article_number,color_order,size_order");



            $nameArray_item_description=sql_select( "select distinct uom from wo_booking_dtls  where booking_no='$txt_booking_no' and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=4 and trim_group=".$result_item[csf('trim_group')]." and pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and  status_active =1 and is_deleted=0 order by trim_group ");
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
					?>

					<td style="border:1px solid black" ><? if($result_color[csf('description')]){echo $result_color[csf('description')];} ?> </td>
					<td style="border:1px solid black" ><? if($result_color[csf('brand_supplier')]){echo $result_color[csf('brand_supplier')]; }?> </td>
                    <td style="border:1px solid black">
					<?
					if($result_color[csf('article_number')]!="no article"){
						echo $result_color[csf('article_number')];
					}else{
						echo "-";
					}
					?>
                    </td>
                    <td style="border:1px solid black"><? echo $color_library[$result_color[csf('color_number_id')]]; ?> </td>
                    <td style="border:1px solid black"><? echo $color_library[$result_color[csf('gmt_color')]];?> </td>
                    <td style="border:1px solid black; text-align:left">
					<? echo $size_library[$result_color[csf('gmts_sizes')]]; ?>
					</td>
					<td style="border:1px solid black; text-align:left">
					<? echo $result_color[csf('item_size')]; ?>
					</td>
					<td style="border:1px solid black; text-align:right" ><? echo number_format($result_color[csf('tot_cons')],6); ?> </td>
					<td style="border:1px solid black; text-align:center" ><? echo $result_color[csf('ex_per')]; ?> </td>
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
					echo number_format($result_color[csf('cons')],3);
					$item_desctiption_total += $result_color[csf('cons')] ;
					?>
                    </td>
					<?
					//echo $moq.'='.$pp_sample.'<br>';
					if($moq>0){?>  
					<td style="border:1px solid black; text-align:right"><?=number_format($result_color[csf('moq')],4,'.','');?></td>
					<? }
					if($pp_sample>0){?>
					<td style="border:1px solid black; text-align:right;"><?=number_format($result_color[csf('pp_sample')],4,'.','');?></td>
					<? } ?>
					<td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_text=$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]];  ?></td>
					<td style="border:1px solid black; text-align:right">
					<?
					echo number_format($result_color[csf('rate')],4);
					?>
                     </td>
					<td style="border:1px solid black; text-align:right">
					<?
					$amount_as_per_gmts_color =$result_color[csf('amount')];
					echo number_format($amount_as_per_gmts_color,4);
					$total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
					$trims_remark=$trims_remark_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]]['remark'];
					?>
					</td>
                     <? if($show_comment==1) {?>
                    <td style="border:1px solid black;text-align:center"><? echo $trims_remark; ?> </td>
                    <? } ?>
				</tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="12"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold;"><?
				echo number_format($item_desctiption_total,4);  ?></td>
				 <? if($moq>0){	$m=1;?>  
				<td style="border:1px solid black" align="center"><strong></strong></td>
				<? }	if($pp_sample>0){$p=1;?>
                <td style="border:1px solid black" align="center"><strong></strong></td>
				<? } ?>
                <td style="border:1px solid black;  text-align:center"><? echo $uom_text; ?></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
                 <? if($show_comment==1) {?>
                <td>&nbsp;  </td>
                <? } ?>
            </tr>
            <?
            }

            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<?=15+$m+$p;?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
                 <? if($show_comment==1) {?>
                <td>&nbsp;  </td>
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
		$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls  where booking_no='$txt_booking_no' and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=0 and  status_active =1 and is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group order by trim_group ");
		if(count($nameArray_item)>0)
		{
			$po_ids=rtrim($po_no_arr[$nameArray_job_po_row[csf('job_no')]]['po_id']);
			$po_ids=array_unique(explode(",",$po_ids));
			$ref_nos=rtrim($job_ref_no[$nameArray_job_po_row[csf('job_no')]],',');
			$ref_nos=implode(",",array_unique(explode(",",$ref_nos)));
			$po_no_qty=0;
			$job_no=$nameArray_job_po_row[csf('job_no')];
			foreach($po_ids as $poid)
			{
				//echo $poid.', ';
				$po_no_qty+=$job_po_qty_arr[$job_no][$poid];
			}

			$moq=$sensitivity_arr[$job_no][0]['moq'];
			$pp_sample=$sensitivity_arr[$job_no][0]['pp_sample'];
			//$po_quantity[$result_job[csf('id')]];
        ?>
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="16" align="">
                    <table width="100%" style="table-layout: fixed;">
                    <tr>
                    <td width="60%"><strong>NO sensitive  (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]];echo " &nbsp;Int Ref.:&nbsp;".$ref_nos;  echo " &nbsp;Po Qty.:&nbsp;".$po_no_qty; ?></strong></td>
                    <td width="40%" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap; font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
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
				<td align="center" style="border:1px solid black"><strong>Cons</strong></td>
				<td align="center" style="border:1px solid black"><strong>Ex%</strong></td>
                <td align="center" style="border:1px solid black"><strong>Qty per Unit</strong></td>
                <td align="center" style="border:1px solid black"><strong> Qnty</strong></td>
				<?
				if($moq>0){?>
				<td style="border:1px solid black" align="center"><strong>MOQ</strong></td>
				<? }
				if($pp_sample>0){?>
				<td style="border:1px solid black" align="center"><strong>PP Sample</strong></td>
				<? } ?>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
                 <? if($show_comment==1) {?>
                 <td style="border:1px solid black" align="center"><strong>Remarks</strong></td>
                 <? } ?>
            </tr>
            <?
			$i=0;$m=0;$p=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;

				$nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_color,avg(b.rate) as rate, sum(b.amount) as amount,sum(b.moq) as moq,sum(pp_sample) as pp_sample,b.id,c.tot_cons,c.ex_per,c.trim_group,c.id  from wo_booking_dtls a, wo_trim_book_con_dtls b,wo_pre_cost_trim_cost_dtls c where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and a.trim_group=c.trim_group and c.id=a.pre_cost_fabric_cost_dtls_id and a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and a.sensitivity=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_color,b.id,c.tot_cons,c.ex_per,c.trim_group,c.id order by b.id");
			
				
				$item_wise_detail=array();
				foreach($nameArray_item_description as $val){
					
					$item_wise_detail[$result_item[csf('trim_group')]][$val[csf('description')]]['description']=$val[csf('description')];
					$item_wise_detail[$result_item[csf('trim_group')]][$val[csf('description')]]['rate']=$val[csf('rate')];
					$item_wise_detail[$result_item[csf('trim_group')]][$val[csf('description')]]['amount']+=$val[csf('amount')];
					$item_wise_detail[$result_item[csf('trim_group')]][$val[csf('description')]]['moq']=$val[csf('moq')];
					$item_wise_detail[$result_item[csf('trim_group')]][$val[csf('description')]]['pp_sample']=$val[csf('pp_sample')];
					$item_wise_detail[$result_item[csf('trim_group')]][$val[csf('description')]]['brand_supplier']=$val[csf('brand_supplier')];
					$item_wise_detail[$result_item[csf('trim_group')]][$val[csf('description')]]['pre_cost_fabric_cost_dtls_id']=$val[csf('pre_cost_fabric_cost_dtls_id')];
					$item_wise_detail[$result_item[csf('trim_group')]][$val[csf('description')]]['item_color']=$val[csf('item_color')];
					$item_wise_detail[$result_item[csf('trim_group')]][$val[csf('description')]]['tot_cons']=$val[csf('tot_cons')];
					$item_wise_detail[$result_item[csf('trim_group')]][$val[csf('description')]]['ex_per']=$val[csf('ex_per')];

				}
				
				?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($item_wise_detail[$result_item[csf('trim_group')]])+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($item_wise_detail[$result_item[csf('trim_group')]])+1; ?>">

                <?
				echo $trim_group_library[$result_item[csf('trim_group')]]."<br/>";
				echo implode(",",$booking_country_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][0]);

				?>
                </td>
                <?
                $color_tatal=0;
                $total_amount_as_per_gmts_color=0;
                foreach($item_wise_detail[$result_item[csf('trim_group')]] as $result_itemdescription)
                {
                $item_desctiption_total=0;
                ?>
                <td style="border:1px solid black"><? if($result_itemdescription['description']){ echo $result_itemdescription['description'];} ?> </td>
                <td style="border:1px solid black"><? if($result_itemdescription['brand_supplier']){echo $result_itemdescription['brand_supplier'];} ?> </td>
                <td style="border:1px solid black"><? echo $color_library[$result_itemdescription['item_color']]; ?> </td>
				<td style="border:1px solid black; text-align:right" ><? echo number_format($result_itemdescription['tot_cons'],6); ?> </td>
				<td style="border:1px solid black; text-align:center" ><? echo $result_itemdescription['ex_per']; ?> </td>
                <td style="border:1px solid black; text-align:left">
               <?
			   $trims_remark=$trims_remark_arr[$result_itemdescription['pre_cost_fabric_cost_dtls_id']]['remark'];
			   $calUom=$trims_qtyPerUnit_arr[$result_itemdescription['pre_cost_fabric_cost_dtls_id']][2];
			   $calQty=explode("_",$trims_qtyPerUnit_arr[$result_itemdescription['pre_cost_fabric_cost_dtls_id']][1]);
			   if($calUom && end($calQty)){
				   echo "1".$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]."=".prev($calQty)." ".$calUom;
			   }
			   ?>
                </td>
                <?
				if($db_type==2)
				{
				$nameArray_color_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trim_group=". $result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and nvl( b.description,0)=nvl('". $result_itemdescription['description']."',0) and nvl(b.brand_supplier,0)=nvl('".$result_itemdescription['brand_supplier']."',0) and nvl(b.item_color,0)=nvl('".$result_itemdescription['item_color']."',0)");
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

				<?
				if($moq>0){?>  
				<td style="border:1px solid black; text-align:right"><?=number_format($result_itemdescription['moq'],4,'.','');?></td>
				<? }
				if($pp_sample>0){?>
				<td style="border:1px solid black; text-align:right"><?=number_format($result_itemdescription['pp_sample'],4,'.','');?></td>
				<? } ?>

                <td style="border:1px solid black; text-align:center "><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_text=$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription['amount']/$item_desctiption_total,4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
				$wo_rate=number_format($result_itemdescription['rate'],4,'.','');
                $amount_as_per_gmts_color = $result_itemdescription['amount'];//$item_desctiption_total* $wo_rate;
				echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
                 <? if($show_comment==1) {?>
                 <td style="border:1px solid black; text-align:right"><? echo $trims_remark; ?> </td>
                 <? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="6"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold;">
                <?
                if($color_tatal !='')
                {

						 echo number_format($color_tatal,4);
                }
                ?>
                </td>
				<? if($moq>0){	$m=1;?>  
				<td style="border:1px solid black" align="center"><strong></strong></td>
				<? }	if($pp_sample>0){$p=1;?>
                <td style="border:1px solid black" align="center"><strong></strong></td>
				<? } ?>
                <td style="border:1px solid black;  text-align:center"><? echo $uom_text; ?></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
                 <? if($show_comment==1) {?>
                <td>&nbsp;  </td>
                <? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<?=11+$m+$p;?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
                 <? if($show_comment==1) {?>
                <td>&nbsp;  </td>
                <? } ?>
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
       <br/>
       <table width="100%" style="margin-top:1px">
           <tr>
           <td>
           <table width="100%" class="rpt_table"  border="1" cellpadding="0" cellspacing="0" rules="all">
                <tr style="border:1px solid black;">
                    <td width="30%" style="border:1px solid black; text-align:left">Total Booking Amount</td>
                    <td width="70%" style="border:1px solid black; text-align:left"><? echo number_format($booking_grand_total,2);?></td>
                </tr>
                <tr style="border:1px solid black;">
                    <td width="30%" style="border:1px solid black; text-align:left">Total Booking Amount (in word)</td>
                    <td width="70%" style="border:1px solid black;"><? echo number_to_words(def_number_format($booking_grand_total,2,""),$mcurrency, $dcurrency);?></td>
                </tr>
           </table>
           </td>
           </tr>
       </table>
         <br/>
            <table width="100%">
                <tr>
                <td width="49%">
                <?
                    echo get_spacial_instruction($txt_booking_no);
                ?>
                </td>
                <td width="2%"></td>
            <?
             $data_array=sql_select("select b.approved_by,b.approved_no, b.approved_date, c.user_full_name from  wo_booking_mst a , approval_history b, user_passwd c where a.id=b.mst_id and b.approved_by=c.id and a.booking_no='$txt_booking_no' and b.entry_form=8 and  a.status_active =1 and a.is_deleted=0");
            ?>
                <td width="49%" valign="top">
                    <table  width="100%" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">

                        <tr style="border:1px solid black;">
                            <td colspan="3" style="border:1px solid black;">Approval Status</td>
                            </tr>
                            <tr style="border:1px solid black;">
                            <td width="3%" style="border:1px solid black;">Sl</td><td width="50%" style="border:1px solid black;">Name</td><td width="27%" style="border:1px solid black;">Approval Date</td><td width="20%" style="border:1px solid black;">Approval No</td>
                            </tr>


                        <?
                        $i;
                        foreach($data_array as $row){
                        ?>
                        <tr style="border:1px solid black;">
                            <td width="3%" style="border:1px solid black;"><? echo $i;?></td><td width="50%" style="border:1px solid black;"><? echo $row[csf('user_full_name')];?></td><td width="27%" style="border:1px solid black;"><? echo change_date_format($row[csf('approved_date')],"dd-mm-yyyy","-");?></td><td width="20%" style="border:1px solid black;"><? echo $row[csf('approved_no')];?></td>
                            </tr>
                            <?
                            $i++;
                        }
                            ?>

                    </table>
            </td>
            </tr>
        </table>

   	 </tbody>
     </table>
     <!--class="footer_signature"-->
    <div  style="margin-top:-50px;">
         <?
          echo signature_table(132, $cbo_company_name, "1333px",1);
		 ?>
   </div>
   <? 
	$emailBody=ob_get_contents();
	//ob_clean();
	if($is_mail_send==1){
		$req_approved=return_field_value("id", "ELECTRONIC_APPROVAL_SETUP", "PAGE_ID = 336 and is_deleted=0");
		$is_approved=return_field_value("IS_APPROVED", "WO_BOOKING_MST", "STATUS_ACTIVE = 1 and is_deleted=0 and booking_no='$txt_booking_no'");
		if($req_approved && $is_approved==1){
			$emailBody.="<h1 style='border:1px sloid #0ff;'>Approved</h1>";
		}
		elseif($req_approved && $is_approved==0){
			$emailBody.="<h1 style='border:1px sloid #0ff;'>Draft</h1>";
		}
		
	
		$mailArr=array();
		if($mail_id!=''){$mailArr[]=$mail_id;}
		if($supplier_mail_arr[$supplier_id]!=''){$mailArr[]=$supplier_mail_arr[$supplier_id];}
		
		
		$to=implode(',',$mailArr);
		$subject="Trims Booking Auto Mail";
		
		if($to!=""){
			require '../../../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
			require_once('../../../auto_mail/setting/mail_setting.php');
			$header=mailHeader();
			sendMailMailer( $to, $subject, $emailBody );
		}
	}
?>
      <div id="page_break_div">
   	 </div>
    <div>

		<?
        	//echo signature_table(2, $cbo_company_name, "1330px");
			//echo "****".custom_file_name($txt_booking_no,$style_sting,$job_no);
        ?>
    </div>

  </html>
	<?
	$user_id=$_SESSION['logic_erp']['user_id'];
	$report_cat=100;
	$html = ob_get_contents();
		ob_clean();
		//$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
		foreach (glob("tb*.xls") as $filename) {
		//if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename="tb".$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc, $html);
		echo "$filename****$html****$report_cat";
	//exit();
}


if($action=="show_trim_booking_report_wg")
{

	extract($_REQUEST);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$id_approved_id=str_replace("'","",$id_approved_id);
	$report_type=str_replace("'","",$report_type);
	$show_comment=str_replace("'","",$show_comment);

	$color_library=return_library_array("select id, color_name from lib_color", "id", "color_name");
	$size_library=return_library_array("select id, size_name from  lib_size", "id", "size_name");
	$company_library=return_library_array("select id, company_name from lib_company", "id", "company_name");
	$trim_group_library= return_library_array("select id, item_name from lib_item_group",'id','item_name');
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$contact_no_arr=return_library_array( "select id,contact_no from   lib_supplier",'id','contact_no');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$order_uom_arr=return_library_array("select id,order_uom  from lib_item_group","id","order_uom");
	$deling_marcent_arr=return_library_array("select id,team_member_name from lib_mkt_team_member_info","id","team_member_name");
	$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
	$tin_arr=return_library_array( "SELECT id,tin_number from lib_company where status_active=1 and is_deleted=0 and id='$cbo_company_name'", "id", "tin_number");
	$bin_arr=return_library_array( "SELECT id,bin_no from lib_company where status_active=1 and is_deleted=0 and id='$cbo_company_name'", "id", "bin_no");
	$location_name_arr=return_library_array( "select id,address from lib_location",'id','address');

	$nameArray_approved=sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no='$txt_booking_no' and b.entry_form=8 and  a.status_active =1 and a.is_deleted=0");
	list($nameArray_approved_row)=$nameArray_approved;
	//$revised_no_data=sql_select("select a.revised_no  from wo_booking_mst a where  a.booking_no=$txt_booking_no and  a.status_active =1 and a.is_deleted=0");
	//list($revised_no_row)=$revised_no_data;
		$booking_grand_total=0;
		$currency_id="";


		$buyer_string=array();
		$style_owner=array();
		$job_no=array();
		$style_ref=array();
		$all_dealing_marcent=array();
		$season=array();
		$order_repeat_no=array();
		$po_id_arr=array();

		$nameArray_buyer=sql_select( "select  a.style_ref_no, a.job_no, a.style_owner, a.buyer_name, a.dealing_marchant, a.season, a.season_matrix, a.season_buyer_wise, a.order_repeat_no, b.po_break_down_id from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no='$txt_booking_no' and b.status_active =1 and b.is_deleted=0");
        foreach ($nameArray_buyer as $result_buy){
			$buyer_string[$result_buy[csf('buyer_name')]]=$buyer_name_arr[$result_buy[csf('buyer_name')]];
			$style_owner[$result_buy[csf('job_no')]]=$company_library[$result_buy[csf('style_owner')]];
			$job_no[$result_buy[csf('job_no')]]=$result_buy[csf('job_no')];
			$style_ref[$result_buy[csf('job_no')]]=$result_buy[csf('style_ref_no')];
			$all_dealing_marcent[$result_buy[csf('job_no')]]=$deling_marcent_arr[$result_buy[csf('dealing_marchant')]];
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
		$job_no=implode(",",$job_no);
		$seasons_names=rtrim($seasons_name,',');

		$seasons_names=implode(",",array_unique(explode(",",$seasons_names)));
		$poid_arr=array_unique($po_id_arr);

		$order_rept_no=rtrim($order_rept_no,',');
		$order_rept_no=implode(",",array_unique(explode(",",$order_rept_no)));

		$po_no=array();
		$file_no=array();
		$ref_no=array();
		$po_quantity=array();
		$pub_shipment_date='';$int_ref_no='';$tot_po_quantity=0;$po_idss='';
		$nameArray_job=sql_select( "select b.job_no_mst,b.id,b.pub_shipment_date, b.po_number,b.grouping, b.file_no, sum(b.po_quantity) as po_quantity  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no='$txt_booking_no' and  a.status_active =1 and a.is_deleted=0 group by b.job_no_mst,b.id,b.pub_shipment_date, b.po_number,b.grouping, b.file_no ");
        foreach ($nameArray_job as $result_job){
			$po_no[$result_job[csf('job_no_mst')]][$result_job[csf('id')]]=$result_job[csf('po_number')];
			$file_no[$result_job[csf('id')]]=$result_job[csf('file_no')];
			$ref_no[$result_job[csf('id')]]=$result_job[csf('grouping')];
			$po_quantity[$result_job[csf('id')]]=$result_job[csf('po_quantity')];
		//	$tot_po_quantity+=$result_job[csf('po_quantity')];
			$job_ref_no[$result_job[csf('job_no_mst')]].=$result_job[csf('grouping')].',';
			$po_no_arr[$result_job[csf('job_no_mst')]]['po_id'].=$result_job[csf('id')].',';
			$pub_shipment_date.=$result_job[csf('pub_shipment_date')].',';
			$int_ref_no.=$result_job[csf('grouping')].',';
			if($po_idss=='') $po_idss=$result_job[csf('id')];else $po_idss.=",".$result_job[csf('id')];

		}
		$sql_job=sql_select( "select b.job_no_mst,b.id as po_id, b.po_quantity as po_quantity  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and  a.status_active =1 and a.is_deleted=0 and  b.status_active =1 and b.is_deleted=0 and b.id in(".$po_idss.") ");
		foreach ($sql_job as $row)
		{
			$job_po_qty_arr[$row[csf('job_no_mst')]][$row[csf('po_id')]]+=$row[csf('po_quantity')];
			$tot_po_quantity+=$row[csf('po_quantity')];
		}

        $nameArray=sql_select( "select a.booking_no,a.company_id, a.pay_mode,a.buyer_id, a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.source, a.remarks, a.revised_no, a.delivery_address,a.pay_term,a.tenor  from wo_booking_mst a where a.booking_no='$txt_booking_no' and a.status_active =1 and a.is_deleted=0");
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
			$payterm=$row[csf('pay_term')];
			$tenor_day= $row[csf("tenor")];
			$delivery_address=$row[csf('delivery_address')];
			$revised_no=$row[csf('revised_no')];
			$source_id=$row[csf('source')];
			$company_id=$row[csf('company_id')];
		}

	ob_start();
	?>
	<html>
	<head>
	  <style type="text/css" media="print">

		 @media print
	        {
	            tbody {
	                page-break-inside: avoid;
					tbody {display: table-row-group;}
	            }
	            thead {
	                display: table-header-group;
					page-break-before: always;

	            }
	        }
	/*	@media print {
				  #page_break_div {
					page-break-before: always;
				  }
		}
			*/	.footer_signature {
					position: fixed;
					height: auto;
					bottom:0;
					width:100%;

					}
				@media print {
					table {
						page-break-inside: avoid;
					}
				}
				/*@media all {
	  			#page_break_div   { display: none; }
				}

				@media screen {
				thead { display: block; }
				tfoot { display: block; }
				}*/

	</style>
	</head>
   	<table width="1333px"  cellpadding="0" cellspacing="0" style="border:0px solid black" >
    <thead>
 
       	 	<table width="100%"  cellpadding="0" cellspacing="0" style="border:0px solid black">
       		<tr>
                <td width="20px">
                    <table width="100%" cellpadding="0" cellspacing="0" style="border:0px solid black">
                    <tr>
                    <td width="150" >
                       <? if($report_type==1)
                       {
                           if($link == 1)
                           {
                       ?>
                                <img  src='../../../<? echo $imge_arr[$cbo_company_name]; ?>' height='60' width='90' />

                       <?
                           }
                           else
                           {
                       ?>
                                <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='60' width='90' />
                       <?	}
                       }
                       else
                       { ?>
                         <img  src='../<? echo $imge_arr[$cbo_company_name]; ?>' height='60' width='90' />
                      <? }
                       ?>
                   </td>
                   <td align="center">
				   <?
                            $nameArray=sql_select( "select a.id,a.group_id,b.id,b.group_name from lib_company a,lib_group b where a.group_id=b.id and  a.id=$cbo_company_name");
                            foreach ($nameArray as $group){
								$group_name=$group[csf('group_name')];
							}	
                            ?>
						<b style="font-size:25px;"><? echo $group_name; ?></b>
						<br/>
						<b style="font-size:20px;">Purchase Order</b>
                    </td>
                   </tr>
                   </table>
                </td>
        	</tr>
            <tr>
            <td>

            	<table width="100%"  style="border:0px solid black;table-layout: fixed;">
				<td align="left" style="width: 48%">
				<table width="700" cellpadding="0" cellspacing="0" style="border:0px solid black">
                      	<tr><b style="font-size:20px;"><? echo $company_library[$cbo_company_name]; ?></b></tr>
						<tr>
							<td width="50"><b>Address:</b></td>
                            <td width="300">
								<? echo return_field_value("address", "lib_location", "company_id='".$cbo_company_name."'"); ?>
							</td>
						</tr>
						<tr>
							<td><b>BIN:</b></td>
                            <td><?echo $bin_arr[$company_id]; ?></td>
						</tr>
						<tr>
							<td><b>TIN:</b></td>
                            <td><? echo $tin_arr[$company_id]; ?></td>
						</tr>						
						<tr>
							<td><b>Supplier Name:</b></td>
                            <td><b><? echo $supplier_name_arr[$supplier_id];?></b></td>
						</tr>
						<tr>
							<td><b>Address:</b></td>
                            <td><? echo $supplier_address_arr[$supplier_id];?></td>
						</tr>
						<tr>
							<td><b>Attention:<b></td>
							<td align="left" valign="top" rowspan="2" ><? 
									$attn= explode(",",$attention);
									foreach($attn as $value){
										echo "<div class='paddingtbl'>".$value."</div>";
									}
									?>
							</td>
						</tr>
						<tr>
							<td><b>Contact NO:</b></td>
							
						</tr>
						<tr>
							<td><b>Delivery Address:</b></td>
							<td><? echo $delivery_address;?></td>
						</tr>
					</table>
                    </td>
                    <td align="right"  style="width: 30%">
                        <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" rules="all" width="400">
							<tr>
								<td width="100"><b>Purchase Type:</b></td>
								<td width="130"><? echo $source[$source_id];?></td>
							</tr>
							<tr>
								<td><b>PO Type:</b></td>
								<td>Trims</td>
							</tr>
							<tr>
								<td><b>PO Number:</b></td>
								<td><?php echo $varcode_booking_no; ?></td>
							</tr>
							<tr>
								<td><b>PO Date:<b></td>
								<td><?php echo $booking_date; ?></td>
							</tr>
                            <tr>
                            	<td><b>Delivery Date:</b></td>
                                <td><?  echo $delivery_date; ?></td>
                            </tr>
                            <tr>
                                <td><b>Buyer:</b></td>
                                <td><? echo $buyer_name_arr[$buyer_id]; ?></td>
                            </tr>
							<tr>
								<td><b>Payment Terms:</b></td>
								<td><? 
									if($payterm==2)
									{
										echo "LC ".$tenor_day." Days";
									}
									else
									{
										echo $pay_term[$payterm]; 
									}
									?>
								</td>
							</tr>
							<tr>
								<td><b>Currency:</b></td>
								<td><?  echo $currency[$currency_id]; ?></td>
							</tr>
                        </table>
                    </td>
            </table>
            </td>
            </tr>
          </table>
  
    </thead>
     <tbody>
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
		$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls  where booking_no='$txt_booking_no' and  status_active =1 and is_deleted=0 and job_no='".$nameArray_job_po_row[csf('job_no')]."'   and sensitivity=1 group by pre_cost_fabric_cost_dtls_id,trim_group  order by trim_group ");
	    if(count($nameArray_item)>0){
			$po_ids=rtrim($po_no_arr[$nameArray_job_po_row[csf('job_no')]]['po_id']);
			$po_ids=array_unique(explode(",",$po_ids));
			$ref_nos=rtrim($job_ref_no[$nameArray_job_po_row[csf('job_no')]],',');
			$ref_nos=implode(",",array_unique(explode(",",$ref_nos)));

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
                <td width="60%" align="left"><strong>As Per Garments Color (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]];  ?></strong></td>
                <td width="40%" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
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
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
                <? if($show_comment==1) {?>
                <td style="border:1px solid black" align="center"><strong>Remarks</strong></td>
                <? }?>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item){
			$i++;
            $nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid, b.description, b.brand_supplier,b.item_color,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=1  and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_color order by bid ");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center"  style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <?
				echo $trim_group_library[$result_item[csf('trim_group')]]."<br/>";
				// echo implode(",",$booking_country_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][1]);
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
				$item_desctiption_total += $result_itemdescription[csf('cons')] ;
				?>
                </td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_text=$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
				$wo_rate=number_format($result_itemdescription[csf('rate')],4,'.','');
                $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$wo_rate;
				echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
				$trims_remark=$trims_remark_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]]['remark'];
                ?>
                </td>
                  <? if($show_comment==1) {?>
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
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right">$
                <?
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
                  <? if($show_comment==1) {?>
                <td>&nbsp;  </td>
                <? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
				
                <td align="right" style="border-left-style: hidden;border-bottom-style: hidden;"  colspan="9"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right">$<?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
                 <? if($show_comment==1) {?>
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
		$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls  where booking_no='$txt_booking_no' and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=2 and  status_active =1 and  is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group  order by trim_group ");
		if(count($nameArray_item)>0)
		{
			$po_ids=rtrim($po_no_arr[$nameArray_job_po_row[csf('job_no')]]['po_id']);
			$po_ids=array_unique(explode(",",$po_ids));
			$ref_nos=rtrim($job_ref_no[$nameArray_job_po_row[csf('job_no')]],',');
			$ref_nos=implode(",",array_unique(explode(",",$ref_nos)));
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
                <td width="60%"><strong>Size Sensitive (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]]; ?></strong></td>
                <td width="40%" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;margin-left:210px; font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
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
                <td align="center" style="border:1px solid black"><strong>Item Size</strong></td>
                <td align="center" style="border:1px solid black"><strong>Qty per Unit</strong></td>
                <td style="border:1px solid black" align="center"><strong>WO Qty.</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
                 <? if($show_comment==1) {?>
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
				// echo implode(",",$booking_country_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][2]);
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
                <td style="border:1px solid black">
				<?
				if($result_itemdescription[csf('article_number')]!="no article"){
					echo $result_itemdescription[csf('article_number')];
				}else{
					echo "-";
				}
				?>
                </td>
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
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">

                <?
				$wo_rate=number_format($result_itemdescription[csf('rate')],4,'.','');
                $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$wo_rate;
				echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
				$trims_remark=$trims_remark_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]]['remark'];
                ?>
                </td>
                 <? if($show_comment==1) {?>
                <td style="border:1px solid black; text-align:right"><p><? echo $trims_remark; ?> </p></td>
                <? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="5"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold"><?

					echo number_format($item_desctiption_total,4);
				 ?></td>
                 <td style="border:1px solid black;  text-align:center"><? echo $uom_text; ?></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right">$
                <?
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
                 <? if($show_comment==1) {?>
                <td>&nbsp; </td>
                <? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border-left-style: hidden;border-bottom-style: hidden;"  colspan="10"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right">$<?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
                 <? if($show_comment==1) {?>
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
		 $nameArray_item=sql_select( "select pre_cost_fabric_cost_dtls_id, trim_group from wo_booking_dtls  where booking_no='$txt_booking_no' and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=3 and  status_active =1 and is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group order by trim_group ");
		if(count($nameArray_item)>0)
		{
			$po_ids=rtrim($po_no_arr[$nameArray_job_po_row[csf('job_no')]]['po_id']);
			$po_ids=array_unique(explode(",",$po_ids));
			$ref_nos=rtrim($job_ref_no[$nameArray_job_po_row[csf('job_no')]],',');
			$ref_nos=implode(",",array_unique(explode(",",$ref_nos)));

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
                <td width="60%"><strong>Contrast Color (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]]; ?></strong></td>
                <td width="40%" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap; font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
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
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
                 <? if($show_comment==1) {?>
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
				// echo implode(",",$booking_country_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][3]);
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
               <? echo $color_library[$result_itemdescription[csf('item_color')]]; ?>
                </td>
                <td style="border:1px solid black; text-align:left">
               <?
			   echo $color_library[$result_itemdescription[csf('color_number_id')]];
			   ?>
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
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
                $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
				$trims_remark=$trims_remark_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]]['remark'];
                ?>
                </td>
                 <? if($show_comment==1) {?>
                <td style="border:1px solid black; text-align:right"><p><? echo $trims_remark; ?></p> </td>
                <? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="5"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold;"><?
				echo number_format($item_desctiption_total,4);
				 ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $uom_text; ?></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right">$
                <?
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
                 <? if($show_comment==1) {?>
                <td>&nbsp;  </td>
                <? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border-left-style: hidden;border-bottom-style: hidden;"  colspan="10"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right">$<?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
                 <? if($show_comment==1) {?>
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
		$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls  where booking_no='$txt_booking_no' and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=4 and  status_active =1 and is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group order by trim_group");
	   if(count($nameArray_item)>0)
		{
			$po_ids=rtrim($po_no_arr[$nameArray_job_po_row[csf('job_no')]]['po_id']);
			$po_ids=array_unique(explode(",",$po_ids));

			$ref_nos=rtrim($job_ref_no[$nameArray_job_po_row[csf('job_no')]],',');
			$ref_nos=implode(",",array_unique(explode(",",$ref_nos)));
			$po_no_qty=0;
			$job_no=$nameArray_job_po_row[csf('job_no')];
			foreach($po_ids as $poid)
			{
				$po_no_qty+=$job_po_qty_arr[$job_no][$poid];
			}
        ?>

        <table border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="14" align="">
                <table width="100%" style="table-layout: fixed;">
                <tr>
                <td width="60%" ><strong>Color & size sensitive (<? echo "Job NO: ".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]] ;?></strong></td>
                <td width="40%" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap; font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
                </tr>
                </table>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group </strong> </td>


                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Brand/Supplier Ref.</strong> </td>
                <td style="border:1px solid black"><strong>Article No.</strong> </td>
                 <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <td style="border:1px solid black; width:170px;"><strong>Gmts Color</strong> </td>
                <td style="border:1px solid black;"><strong>Gmts Size</strong> </td>

                <td align="center" style="border:1px solid black"><strong>Item Size</strong></td>
                <td align="center" style="border:1px solid black"><strong>Qty per Unit</strong></td>
                <td style="border:1px solid black" align="center"><strong>WO Qty.</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
                 <? if($show_comment==1) {?>
                <td style="border:1px solid black" align="center"><strong>Remarks</strong></td>
                <? } ?>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
				$gmtcolor_library=return_library_array( "select b.item_color,b.color_number_id from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.sensitivity=4    and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by trim_group ", "item_color", "color_number_id"  );

			 $nameArray_color=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid, b.color_number_id as gmt_color, b.item_color as color_number_id,b.item_size,b.gmts_sizes,b.description, b.brand_supplier,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount, min(c.color_order) as color_order, min(c.size_order) as size_order,c.article_number from wo_booking_dtls a, wo_trim_book_con_dtls b,wo_po_color_size_breakdown c  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and b.po_break_down_id=c.po_break_down_id and b.color_number_id=c.color_number_id and b.gmts_sizes=c.size_number_id and  c.id=b.color_size_table_id and a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and a.sensitivity=4 and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.color_number_id, b.item_color,b.item_size,b.gmts_sizes,b.description, b.brand_supplier,c.article_number order by c.article_number,color_order,size_order");



            $nameArray_item_description=sql_select( "select distinct uom from wo_booking_dtls  where booking_no='$txt_booking_no' and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=4 and trim_group=".$result_item[csf('trim_group')]." and pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and  status_active =1 and is_deleted=0 order by trim_group ");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo   (count($nameArray_item_description)*count($nameArray_color)); ?>">
                <? echo $i ; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo (count($nameArray_item_description)*count($nameArray_color)); ?>">
                <?
				echo $trim_group_library[$result_item[csf('trim_group')]]."<br/>";
				// echo implode(",",$booking_country_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][4]);
				?>
                </td>
                <?
				$item_desctiption_total=0;
                $total_amount_as_per_gmts_color=0;
				foreach($nameArray_color as $result_color)
                {
					?>

					<td style="border:1px solid black" ><? if($result_color[csf('description')]){echo $result_color[csf('description')];} ?> </td>
					<td style="border:1px solid black" ><? if($result_color[csf('brand_supplier')]){echo $result_color[csf('brand_supplier')]; }?> </td>
                    <td style="border:1px solid black">
					<?
					if($result_color[csf('article_number')]!="no article"){
						echo $result_color[csf('article_number')];
					}else{
						echo "-";
					}
					?>
                    </td>
                    <td style="border:1px solid black"><? echo $color_library[$result_color[csf('color_number_id')]]; ?> </td>
                    <td style="border:1px solid black"><? echo $color_library[$result_color[csf('gmt_color')]];?> </td>
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
					<td style="border:1px solid black; text-align:right">
					<?
					echo number_format($result_color[csf('rate')],4);
					?>
                     </td>
					<td style="border:1px solid black; text-align:right">
					<?
					$amount_as_per_gmts_color =$result_color[csf('amount')];
					echo number_format($amount_as_per_gmts_color,4);
					$total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
					$trims_remark=$trims_remark_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]]['remark'];
					?>
					</td>
                     <? if($show_comment==1) {?>
                    <td style="border:1px solid black;text-align:center"><? echo $trims_remark; ?> </td>
                    <? } ?>
				</tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="10"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold;"><?
				echo number_format($item_desctiption_total,4);  ?></td>
                <td style="border:1px solid black;  text-align:center"><? echo $uom_text; ?></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right">$
                <?
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
                 <? if($show_comment==1) {?>
                <td>&nbsp;  </td>
                <? } ?>
            </tr>
            <?
            }

            ?>
            <tr>
                <td align="right" style="border-left-style: hidden;border-bottom-style: hidden;"  colspan="13"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right">$<?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
                 <? if($show_comment==1) {?>
                <td>&nbsp;  </td>
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
		$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls  where booking_no='$txt_booking_no' and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=0 and  status_active =1 and is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group order by trim_group ");
		if(count($nameArray_item)>0)
		{
			$po_ids=rtrim($po_no_arr[$nameArray_job_po_row[csf('job_no')]]['po_id']);
			$po_ids=array_unique(explode(",",$po_ids));
			$ref_nos=rtrim($job_ref_no[$nameArray_job_po_row[csf('job_no')]],',');
			$ref_nos=implode(",",array_unique(explode(",",$ref_nos)));
			$po_no_qty=0;
			$job_no=$nameArray_job_po_row[csf('job_no')];
			foreach($po_ids as $poid)
			{
				//echo $poid.', ';
				$po_no_qty+=$job_po_qty_arr[$job_no][$poid];
			}

			//$po_quantity[$result_job[csf('id')]];
        ?>
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="14" align="">
                    <table width="100%" style="table-layout: fixed;">
                    <tr>
                    <td width="60%"><strong>NO sensitive  (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]]; ?></strong></td>
                    <td width="40%" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap; font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
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
                <td align="center" style="border:1px solid black"><strong>Qty per Unit</strong></td>
                <td align="center" style="border:1px solid black"><strong> Qnty</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
                 <? if($show_comment==1) {?>
                 <td style="border:1px solid black" align="center"><strong>Remarks</strong></td>
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
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">

                <?
				echo $trim_group_library[$result_item[csf('trim_group')]]."<br/>";
				// echo implode(",",$booking_country_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][0]);

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
                <td style="border:1px solid black"><? echo $color_library[$result_itemdescription[csf('item_color')]]; ?> </td>
                <td style="border:1px solid black; text-align:left">
               <?
			   $trims_remark=$trims_remark_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]]['remark'];
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
						$nameArray_color_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trim_group=". $result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and  b.description='". $result_itemdescription[csf('description')]."' and b.brand_supplier='".$result_itemdescription[csf('brand_supplier')]."' and b.item_color='".$result_itemdescription[csf('item_color')]."'");
						}
						if($db_type==2)
				        {
						$nameArray_color_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trim_group=". $result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and nvl( b.description,0)=nvl('". $result_itemdescription[csf('description')]."',0) and nvl(b.brand_supplier,0)=nvl('".$result_itemdescription[csf('brand_supplier')]."',0) and nvl(b.item_color,0)=nvl('".$result_itemdescription[csf('item_color')]."',0)");
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
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('amount')]/$item_desctiption_total,4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
				$wo_rate=number_format($result_itemdescription[csf('rate')],4,'.','');
                $amount_as_per_gmts_color = $result_itemdescription[csf('amount')];//$item_desctiption_total* $wo_rate;
				echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
                 <? if($show_comment==1) {?>
                 <td style="border:1px solid black; text-align:right"><? echo $trims_remark; ?> </td>
                 <? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="4"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold;">
                <?
                if($color_tatal !='')
                {

						 echo number_format($color_tatal,4);
                }
                ?>
                </td>
                <td style="border:1px solid black;  text-align:center"><? echo $uom_text; ?></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right">$
                <?
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
                 <? if($show_comment==1) {?>
                <td>&nbsp;  </td>
                <? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border-bottom-style: hidden;"  colspan="9"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right">$<?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
                 <? if($show_comment==1) {?>
                <td>&nbsp;  </td>
                <? } ?>
            </tr>
        </table>
        <?
		}
		?>
		 
			<?
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
		$dcurrency='Cents';
	   }
	   if($currency_id==3)
	   {
			$mcurrency='EURO';
			$dcurrency='CENTS';
		}
	   	   ?>


       
           <table width="100%" class="rpt_table"  border="1" cellpadding="0" cellspacing="0" rules="all">
		   <tr >
                <td style="border-top-style: hidden;" align="right"><b>Grand Total</b></td>
                <td width="116" align="right"><b>$<?=number_format($booking_grand_total,2); ?></b></td>
                <td width="125" >&nbsp;</td>
            </tr>
			</table>
			<table width="100%" class="rpt_table"  border="1" cellpadding="0" cellspacing="0" rules="all">
				
                <tr style="border:1px solid black;">
                    <td width="100%" style="border:1px solid black; text-align:left">Amount in word:&nbsp;<? echo $mcurrency,"&nbsp;",number_to_words(def_number_format($booking_grand_total,2,""),"", $dcurrency);?></td>
                </tr>
           </table>

         <br/>
		 <table width="100%" style="margin-top:1px">
           <tr>
           <td>
           <table width="100%" class="rpt_table"  border="1" cellpadding="0" cellspacing="0" rules="all">
                <tr style="border:1px solid black;">
				<?
					$data_array=sql_select("select id, remarks from  wo_booking_mst where booking_no='$txt_booking_no'");
					//echo "select id, remarks from  wo_booking_mst where booking_no=$txt_booking_no";
					foreach( $data_array as $row )
					{
						$remarks=$row[csf('remarks')];
					}
					?>
                    <td width="100%" style="border:1px solid black; text-align:left">Special Comments:&nbsp;<? echo $remarks; ?>
                </tr>
           </table>
           </td>
           </tr>
       </table>
	   <br>
	   <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <th width="97%" align="left"><u><strong style="font-size:16px">Terms & Condition</strong></u></th>
                </tr>

            <?
            $data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no='$txt_booking_no'");// quotation_id='$data'
            if ( count($data_array)>0)
            {
				$i=1;
                foreach( $data_array as $row )
                {
                    ?>
                    <tr>
                        <td><? echo $i++;?>.<span><?echo $row[csf('terms')];?></td>
                    </tr>
                    <?
                }
            }
            ?>
    </table>

   	 </tbody>
     </table>
     <!--class="footer_signature"-->
    <div  style="margin-top:-50px;">
         <?
          echo signature_table(132, $cbo_company_name, "1333px",1);
		 ?>
   </div>
   <? 
	$emailBody=ob_get_contents();
	//ob_clean();
	if($is_mail_send==1){
		$req_approved=return_field_value("id", "ELECTRONIC_APPROVAL_SETUP", "PAGE_ID = 336 and is_deleted=0");
		$is_approved=return_field_value("IS_APPROVED", "WO_BOOKING_MST", "STATUS_ACTIVE = 1 and is_deleted=0 and booking_no='$txt_booking_no'");
		if($req_approved && $is_approved==1){
			$emailBody.="<h1 style='border:1px sloid #0ff;'>Approved</h1>";
		}
		elseif($req_approved && $is_approved==0){
			$emailBody.="<h1 style='border:1px sloid #0ff;'>Draft</h1>";
		}
		
	
		$mailArr=array();
		if($mail_id!=''){$mailArr[]=$mail_id;}
		if($supplier_mail_arr[$supplier_id]!=''){$mailArr[]=$supplier_mail_arr[$supplier_id];}
		
		
		$to=implode(',',$mailArr);
		$subject="Trims Booking Auto Mail";
		
		if($to!=""){
			require '../../../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
			require_once('../../../auto_mail/setting/mail_setting.php');
			$header=mailHeader();
			sendMailMailer( $to, $subject, $emailBody );
		}
	}
?>
      <div id="page_break_div">
   	 </div>
    <div>

		<?
        	//echo signature_table(2, $cbo_company_name, "1330px");
			//echo "****".custom_file_name($txt_booking_no,$style_sting,$job_no);
        ?>
    </div>

  </html>
	<?
	$user_id=$_SESSION['logic_erp']['user_id'];
	$report_cat=100;
	$html = ob_get_contents();
		ob_clean();
		//$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
		foreach (glob("tb*.xls") as $filename) {
		//if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename="tb".$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc, $html);
		echo "$filename****$html****$report_cat";
	//exit();
}

if($action=="print_t")
{
	extract($_REQUEST);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$id_approved_id=str_replace("'","",$id_approved_id);
	$report_type=str_replace("'","",$report_type);
	$show_comment=str_replace("'","",$show_comment);
	$buyer_name=str_replace("'","",$cbo_buyer_name);

	$color_library=return_library_array("select id, color_name from lib_color", "id", "color_name");
	$size_library=return_library_array("select id, size_name from  lib_size", "id", "size_name");
	$company_library=return_library_array("select id, company_name from lib_company", "id", "company_name");
	$trim_group_library= return_library_array("select id, item_name from lib_item_group",'id','item_name');
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	//$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	//$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	
	$supplier_sql_result=sql_select("select ID,SUPPLIER_NAME,ADDRESS_1,EMAIL from lib_supplier and status_active =1 and  is_deleted=0");
	foreach($supplier_sql_result as $rows){
		$supplier_name_arr[$rows[ID]]=$rows[SUPPLIER_NAME];
		$supplier_address_arr[$rows[ID]]=$rows[ADDRESS_1];
		$supplier_mail_arr[$rows[ID]]=$rows[EMAIL];
	}

	
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$order_uom_arr=return_library_array("select id,order_uom  from lib_item_group","id","order_uom");

	$sql_mrcht=sql_select("SELECT team_member_name,id, member_contact_no,team_member_email from lib_mkt_team_member_info ");
	$marchant_data=array();
	foreach ($sql_mrcht as $row) 
	{
		$marchant_data[$row[csf('id')]]['team_member_name']=$row[csf('team_member_name')];
		$marchant_data[$row[csf('id')]]['member_contact_no']=$row[csf('member_contact_no')];
		$marchant_data[$row[csf('id')]]['team_member_email']=$row[csf('team_member_email')];
	}

	$deling_marcent_arr=return_library_array("select id,team_member_name from lib_mkt_team_member_info","id","team_member_name");

	$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");

	$nameArray_approved=sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no='$txt_booking_no' and b.entry_form=8 and  a.status_active =1 and a.is_deleted=0");
	list($nameArray_approved_row)=$nameArray_approved;
	//$revised_no_data=sql_select("select a.revised_no  from wo_booking_mst a where  a.booking_no=$txt_booking_no and  a.status_active =1 and a.is_deleted=0");
	//list($revised_no_row)=$revised_no_data;
		$booking_grand_total=0;
		$currency_id="";


		$buyer_string=array();
		$style_owner=array();
		$job_no=array();
		$style_ref=array();
		$all_dealing_marcent=array();
		$season=array();
		$order_repeat_no=array();
		$po_id_arr=array();
		$job_data_arr=array();

		$nameArray_buyer=sql_select( "select  a.style_ref_no, a.job_no, a.style_owner, a.buyer_name, a.dealing_marchant, a.season, a.season_matrix, a.season_buyer_wise, a.order_repeat_no, b.po_break_down_id,a.gmts_item_id ,a.client_id from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no='$txt_booking_no' and b.status_active =1 and b.is_deleted=0");
        foreach ($nameArray_buyer as $result_buy){
			$buyer_string[$result_buy[csf('buyer_name')]]=$buyer_name_arr[$result_buy[csf('buyer_name')]];
			$style_owner[$result_buy[csf('job_no')]]=$company_library[$result_buy[csf('style_owner')]];
			$job_no[$result_buy[csf('job_no')]]=$result_buy[csf('job_no')];
			$style_ref[$result_buy[csf('job_no')]]=$result_buy[csf('style_ref_no')];
			$job_data_arr['gmts_item_id'][$result_buy[csf('job_no')]]=$result_buy[csf('gmts_item_id')];
			$job_data_arr['dealing_marchant'][$result_buy[csf('job_no')]]= $marchant_data[$result_buy[csf('dealing_marchant')]]['team_member_name'];
			$job_data_arr['member_contact_no'][$result_buy[csf('job_no')]]= $marchant_data[$result_buy[csf('dealing_marchant')]]['member_contact_no'];
			$job_data_arr['dealing_marchant_email'][$result_buy[csf('job_no')]]= $marchant_data[$result_buy[csf('dealing_marchant')]]['team_member_email'];
			$job_data_arr['client'][$result_buy[csf('job_no')]]=$result_buy[csf('client_id')];
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
		$job_no=implode(",",$job_no);
		$seasons_names=rtrim($seasons_name,',');

		$seasons_names=implode(",",array_unique(explode(",",$seasons_names)));
		$poid_arr=array_unique($po_id_arr);

		$order_rept_no=rtrim($order_rept_no,',');
		$order_rept_no=implode(",",array_unique(explode(",",$order_rept_no)));

		$dealing_marchant=implode(",",array_unique($job_data_arr['dealing_marchant']));
		$dealing_marchant_email=implode(",",array_unique($job_data_arr['dealing_marchant_email']));
		$member_contact_no=implode(",",array_unique($job_data_arr['member_contact_no']));
		$team_member_email=implode(",",array_unique($job_data_arr['dealing_marchant_email']));
		$gmts_item_id=implode(",",array_unique($job_data_arr['gmts_item_id']));
		$client_id= implode(",",array_unique($job_data_arr['client']));

		$po_no=array();
		$file_no=array();
		$ref_no=array();
		$po_quantity=array();
		$pub_shipment_date='';$int_ref_no='';$tot_po_quantity=0;$po_idss='';
		$nameArray_job=sql_select( "select b.job_no_mst,b.id,b.pub_shipment_date, b.po_number,b.grouping, b.file_no, sum(b.po_quantity) as po_quantity  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no='$txt_booking_no' and  a.status_active =1 and a.is_deleted=0 group by b.job_no_mst,b.id,b.pub_shipment_date, b.po_number,b.grouping, b.file_no ");
        foreach ($nameArray_job as $result_job){
			$po_no[$result_job[csf('job_no_mst')]][$result_job[csf('id')]]=$result_job[csf('po_number')];
			$file_no[$result_job[csf('id')]]=$result_job[csf('file_no')];
			$ref_no[$result_job[csf('id')]]=$result_job[csf('grouping')];
			$po_quantity[$result_job[csf('id')]]=$result_job[csf('po_quantity')];
		//	$tot_po_quantity+=$result_job[csf('po_quantity')];
			$job_ref_no[$result_job[csf('job_no_mst')]].=$result_job[csf('grouping')].',';
			$po_no_arr[$result_job[csf('job_no_mst')]]['po_id'].=$result_job[csf('id')].',';
			$pub_shipment_date.=$result_job[csf('pub_shipment_date')].',';
			$int_ref_no.=$result_job[csf('grouping')].',';
			if($po_idss=='') $po_idss=$result_job[csf('id')];else $po_idss.=",".$result_job[csf('id')];

		}
		$sql_job=sql_select( "select b.job_no_mst,b.id as po_id, b.po_quantity as po_quantity  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and  a.status_active =1 and a.is_deleted=0 and  b.status_active =1 and b.is_deleted=0 and b.id in(".$po_idss.") ");
		foreach ($sql_job as $row)
		{
			$job_po_qty_arr[$row[csf('job_no_mst')]][$row[csf('po_id')]]+=$row[csf('po_quantity')];
			$tot_po_quantity+=$row[csf('po_quantity')];
		}

        $nameArray=sql_select( "select a.booking_no, a.pay_mode,a.buyer_id, a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.source, a.remarks, a.revised_no, a.delivery_address from wo_booking_mst a where a.booking_no='$txt_booking_no' and a.status_active =1 and a.is_deleted=0");
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
			$delivery_address=$row[csf('delivery_address')];
			$revised_no=$row[csf('revised_no')];
			$source_id=$row[csf('source')];
		}

	ob_start();
	?>
	<html>
	<head>
	  <style type="text/css" media="print">

		/* @media print
	        {
	            tbody {
	                page-break-inside: avoid;
					tbody {display: table-row-group;}
	            }
	            thead {
	                display: table-header-group;
					page-break-before: always;

	            }
	        }*/
	/*	@media print {
				  #page_break_div {
					page-break-before: always;
				  }
		}
			*/	/*.footer_signature {
					position: fixed;
					height: auto;
					bottom:0;
					width:100%;

					}
				@media print {
					table {
						page-break-inside: avoid;
					}
				}*/
				/*@media all {
	  			#page_break_div   { display: none; }
				}

				@media screen {
				thead { display: block; }
				tfoot { display: block; }
				}*/

	</style>
	</head>
   	<table width="1333px"  cellpadding="0" cellspacing="0" style="border:0px solid black" >
    <thead>
    <tr>
        <th>
		<? $nameArray=sql_select( "select a.buyer_id, a.booking_no, a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.po_break_down_id, a.colar_excess_percent, a.cuff_excess_percent, a.delivery_date, a.is_apply_last_update, a.fabric_source, a.rmg_process_breakdown, a.insert_date, a.update_date, a.uom, a.remarks, a.pay_mode, a.fabric_composition, a.delivery_address, a.pay_mode, a.currency_id, a.item_category from wo_booking_mst a where a.booking_no='$txt_booking_no' and a.buyer_id=$buyer_name and a.status_active =1 and a.is_deleted=0 ");
    
        $supplier_id=$nameArray[0][csf('supplier_id')];
        $sql_sup=sql_select("SELECT SUPPLIER_NAME,SHORT_NAME,CONTACT_NO,ADDRESS_1,email from lib_supplier where status_active=1 and is_deleted=0 and id=$supplier_id ");
        $address="";
        ?>
        <table style="table-layout: fixed;width: 1300px; " >
            <tr>
                <td style="text-align: center;">
                    <span style=" font-size:20px; font-weight:bold"><? echo $company_library[$cbo_company_name]; ?></span><br>
                    <?
                        $nameArray2=sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
                        foreach ($nameArray2 as $result)
                        {
                            echo $address=$result[csf('plot_no')].' '.$result[csf('level_no')].' '.$result[csf('road_no')].' '.$result[csf('block_no')].' '.$result[csf('city')].' '.$result[csf('zip_code')].' '.$result[csf('province')].' '.$country_arr[$result[csf('country_id')]]; 
                        }
                        ?>
                        <br>
                    <span style="font-size:16px; font-weight:bold">PURCHASE ORDER</span>
                </td>
            </tr>
        </table>
        <?
        $booking_no='';
        foreach ($nameArray as $result) {
            $currency_id=$result[csf('currency_id')];
            $booking_date=$result[csf('update_date')];
            if($booking_date=="" || $booking_date=="0000-00-00 00:00:00"){
                $booking_date=$result[csf('insert_date')];
            }
            $booking_no=$result[csf('booking_no')];
         ?>
            <table style="width: 1300px;">
                <tr>
                    <td align="left" style="width: 48%">
                        <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" rules="all" width="100%">
                            <tr>
                                <td>Order No</td>
                                <td><? echo $result[csf('booking_no')];?></td>
                            </tr>
                            <tr>
                                <td>Order Date</td>
                                <td><? echo change_date_format($result[csf('booking_date')]);?></td>
                            </tr>
                        </table>
                    </td>
                    <td style="width: 4%"></td>
                    <td align="right"  style="width: 48%">
                        <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" rules="all" width="100%">
                            <tr>
                                <td>Delivery Date</td>
                                <td><? echo change_date_format($result[csf('delivery_date')]);?></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="left" style="width: 48%">
                        <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" rules="all" width="100%" >
                            <tr><th colspan="2"><b>SUPPLIER</b></th></tr>
                            <tr>
                                <td width="120">Supplier Name</td>
                                <td><? echo $sql_sup[0][csf('SUPPLIER_NAME')];?></td>
                            </tr>
                            <tr>
                                <td>Supplier Code</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Attention</td>
                                <td><? echo $result[csf('attention')];?></td>
                            </tr>
                            <tr>
                                <td>Address</td>
                                <td><? echo $sql_sup[0][csf('ADDRESS_1')];?></td>
                            </tr>
                            
                            <tr>
                                <td>Contact No</td>
                                <td><? echo $sql_sup[0][csf('CONTACT_NO')];?></td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td><? echo $sql_sup[0][csf('email')];?></td>
                            </tr>
                        </table>
                    </td>
                    <td style="width: 4%"></td>
                    <td align="right" style="width: 48%">
                        <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" rules="all" width="100%" >
                            <caption><b>BUYER</b></caption>
                            <tr >
                                <td width="160">Purchaser Name</td>
                                <td><? echo $company_library[$cbo_company_name];?></td>
                            </tr>
                            <tr>
                                <td>Contact Person</td>
                                <td><? echo $dealing_marchant; ?></td>
                            </tr>
                            <tr>
                                <td>Contact No</td>
                                <td><? echo $member_contact_no;?></td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td><? echo $team_member_email;?></td>
                            </tr>
                            <tr>
                                <td>Buyer/Agent Name</td>
                                <td> <? $buyer_name_str=""; if($client_id!=0) $buyer_name_str=$buyer_name_arr[$result[csf('buyer_id')]]."-".$buyer_name_arr[$client_id]; else $buyer_name_str=$buyer_name_arr[$result[csf('buyer_id')]]; echo $buyer_name_str; ?></td>
                            </tr>
                            <tr>
                                <td>Garments Item</td>
                                <td>
                                    <?
                                        $gmts_item_name="";
                                        $gmts_item=explode(',',$gmts_item_id);
                                        for($g=0;$g<=count($gmts_item); $g++)
                                        {
                                            $gmts_item_name.= $garments_item[$gmts_item[$g]].",";
                                        }
                                        echo rtrim($gmts_item_name,',');
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        <? } ?>
        </th>
    </tr>
    </thead>
    <tbody>
          
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
        //==============================================NO SENSITIBITY START=========================================
		$sql_no_sen="SELECT sum(c.requirment) qnty, b.job_no_mst as job_no, b.po_number, a.pre_cost_fabric_cost_dtls_id, a.trim_group, c.item_color, c.item_size, c.gmts_sizes, c.zipper_break_down, avg(c.rate) as rate, sum(c.amount) amount, a.uom, a.description
			         
			   FROM wo_booking_dtls a, wo_po_break_down b, wo_trim_book_con_dtls c
			   WHERE a.po_break_down_id = b.id and a.id= c.wo_trim_booking_dtls_id and a.booking_no='$txt_booking_no' and a.status_active = 1 and a.is_deleted = 0 and c.requirment>0 and a.sensitivity = 0 and b.status_active=1 and c.status_active=1
			GROUP BY b.job_no_mst, b.po_number, a.pre_cost_fabric_cost_dtls_id, a.trim_group, c.item_color, c.item_size, c.gmts_sizes, c.zipper_break_down, a.uom, a.description";
		$nameArray_item=sql_select($sql_no_sen);
		if(count($nameArray_item)>0)//1$!Red$!$2$!Black$!$3$!Yellow$!$4$!White
		{
			$po_wise_data=array(); $isZipColor=0;
			foreach ($nameArray_item as $row)
			{
				if($row[csf('zipper_break_down')]==0) $row[csf('zipper_break_down')]="";
				if($row[csf('zipper_break_down')]!="") $isZipColor=1;
				$exzipperdata=explode("$!$",$row[csf('zipper_break_down')]);
				
				$extapcolor=explode("$!",$exzipperdata[0]);
				$exteethcolor=explode("$!",$exzipperdata[1]);
				$exslidercolor=explode("$!",$exzipperdata[2]);
				$expullcolor=explode("$!",$exzipperdata[3]);
				
				$tapcolor=$extapcolor[1];
				$teethcolor=$exteethcolor[1];
				$slidercolor=$exslidercolor[1];
				$pullcolor=$expullcolor[1];
				
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('description')]]['trim_group']=$row[csf('trim_group')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('description')]]['item_color'].=$color_library[$row[csf('item_color')]]."***";
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('description')]]['item_size'].=$row[csf('item_size')]."***";
				
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('description')]]['tapcolor'].=$tapcolor."***";
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('description')]]['teethcolor'].=$teethcolor."***";
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('description')]]['slidercolor'].=$slidercolor."***";
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('description')]]['pullcolor'].=$pullcolor."***";
				
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('description')]]['gmts_sizes'].=$size_library[$row[csf('gmts_sizes')]]."***";
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('description')]]['rate']=$row[csf('rate')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('description')]]['amount']+=$row[csf('amount')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('description')]]['qnty']+=$row[csf('qnty')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('description')]]['uom'].=$unit_of_measurement[$row[csf('uom')]]."***";
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('description')]]['description']=$row[csf('description')];
			}
			$job_span_arr=array(); $po_span_arr=array(); $trim_group_span_arr=array();
			foreach ($po_wise_data as $job_no => $job_data) 
			{
				$span=0;
				foreach ($job_data as $po_id => $po_data) 
				{
					$po_span=0;
					foreach ($po_data as $item_grou => $item_data) 
					{
						$trim_grop_span=0;
						foreach ($item_data as $desp => $desp_data) 
						{
							$span++; $po_span++; $trim_grop_span++;
						}

						$trim_group_span_arr[$job_no][$po_id][$item_grou]=$trim_grop_span;
					}
					$po_span_arr[$job_no][$po_id]=$po_span;
				}
				$job_span_arr[$job_no]=$span;
			}
			//$po_quantity[$result_job[csf('id')]];
        ?>
        <tr>
        <td>
        <table width="1300px" style="margin-top: 10px;">
        	<tr>
        		<th align="left" >No sensitive</th>
        	</tr>
        </table>
        <table  border="1" align="left" class="rpt_table"  cellpadding="0" width="1300px" cellspacing="0" rules="all" >
        	<thead>
        		<tr>
        			<th width="90">Job</th>
        			<th width="100">PO</th>
        			<th width="100">Style Ref No.</th>
        			<th width="100">ITEM</th>
        			<th width="100">Item Description</th>
        			<th width="60">Gmts Size </th>
        			<th width="90">Color </th>
                    <? if($isZipColor==1) { ?>
                    <th width="60">Tape Color</th>
                    <th width="60">Teeth Color</th>
                    <th width="60">Slider Color</th>
                    <th width="60">Pull Color</th>
                    <? } ?>
        			<th width="60">Measurement<br>/Count</th>
        			<th width="70">Qty</th>
        			<th width="50">Unit</th>
        			<th width="50">Rate</th>
        			<th width="80">Total Amount</th>
        			<th>Remarks</th>
        		</tr>
        	</thead>
        	<tbody>
        		<?php 
        			$qnty=0; $amount=0; $tdcols=0;
        			foreach ($po_wise_data as $job_no => $job_data) 
        			{
        				$job_span=0;
        				foreach ($job_data as $po_number => $po_data) 
        				{
        					$po_span=0;
        					foreach ($po_data as $item_group => $item_data) 
        					{
        						$tgroup_span=0;
        						foreach ($item_data as $desp => $desp_data) 
								{
		        					?>
		        					<tr>
		        						<?php if ($job_span==0): ?>
		        						<td rowspan="<? echo $job_span_arr[$job_no];?>" style="word-break:break-all"><?php echo $job_no; $job_span;$job_span++;?></td>
		        						<?php endif ?>
		        						<?php if ($po_span==0): ?>
		        							<td rowspan="<? echo $po_span_arr[$job_no][$po_number];?>" style="word-break:break-all"><?php echo $po_number;$po_span++; ?></td>
		        							<td rowspan="<? echo $po_span_arr[$job_no][$po_number];?>" style="word-break:break-all"><?php echo $style_ref[$job_no]; ?></td>
		        						<?php endif ?>
		        						<?php if ($tgroup_span==0): ?>
		        						<td rowspan="<? echo $trim_group_span_arr[$job_no][$po_number][$item_group];?>" style="word-break:break-all"><?php echo $trim_group_library[$desp_data['trim_group']];$tgroup_span++; ?></td>
		        						<?php endif ?>
		        						<td style="word-break:break-all"><?php echo $desp; ?></td>
		        						<td style="word-break:break-all"><?php echo implode(",", array_unique(explode("***", chop($desp_data['gmts_sizes'],"***")))); ?></td>
		        						<td style="word-break:break-all"><?php echo implode(",", array_unique(explode("***", chop($desp_data['item_color'],"***")))); ?></td>
                                        <? if($isZipColor==1) { 
										$tdcols=4;
										?>
                                        <td style="word-break:break-all"><?php echo implode(",", array_unique(explode("***", chop($desp_data['tapcolor'],"***")))); ?></td>
                                        <td style="word-break:break-all"><?php echo implode(",", array_unique(explode("***", chop($desp_data['teethcolor'],"***")))); ?></td>
                                        <td style="word-break:break-all"><?php echo implode(",", array_unique(explode("***", chop($desp_data['slidercolor'],"***")))); ?></td>
                                        <td style="word-break:break-all"><?php echo implode(",", array_unique(explode("***", chop($desp_data['pullcolor'],"***")))); ?></td>
                                        <? } ?>
		        						<td style="word-break:break-all"><?php echo implode(",", array_unique(explode("***", chop($desp_data['item_size'],"***")))); ?></td>
                                        
		        						<td align="right"><?php echo number_format($desp_data['qnty'],4); ?></td>
		        						<td align="center"><?php echo implode(",", array_unique(explode("***", chop($desp_data['uom'],"***")))); ?></td>
		        						<td align="right" title="<?=$desp_data['rate'];?>"><?php echo  fn_number_format($desp_data['amount']/$desp_data['qnty'],4); ?></td>
		        						<td align="right"><?php echo  number_format($desp_data['amount'],4); ?></td>
		        						<td></td>
		        					</tr>
		        					<?
		        					$qnty+=$desp_data['qnty'];
		        					$amount+=$desp_data['amount'];
		        				}
	        				}
        				}
        			}

        		 ?>
        	</tbody>
        	<tfoot>
        		<tr>
        			<td colspan="<?=8+$tdcols; ?>" align="right">Total</td>
        			<td align="right"><?php echo number_format($qnty,4); ?></td>
        			<td></td>
        			<td></td>
        			<td align="right"><?php echo number_format($amount,4); ?></td>
        			<td></td>
        		</tr>
        	</tfoot>
        </table>
        </td>
        </tr>
        <?
		}
        //==============================================NO NENSITIBITY END=========================================  
        //==============================================SIZE SENSITIBITY START=========================================  
		
		$sql_no_sen="SELECT sum(c.requirment) qnty, b.job_no_mst as job_no, b.po_number, a.pre_cost_fabric_cost_dtls_id, a.trim_group, c.item_color, c.item_size, c.gmts_sizes, c.zipper_break_down, avg(c.rate) as rate, sum(c.amount) amount, a.uom, a.description
			   FROM wo_booking_dtls a, wo_po_break_down b,wo_trim_book_con_dtls c
			   WHERE a.po_break_down_id = b.id and a.id= c.wo_trim_booking_dtls_id and a.booking_no = '$txt_booking_no' and a.status_active = 1 and a.is_deleted = 0 and c.requirment>0 and a.sensitivity = 2 and b.status_active=1 and c.status_active=1
			GROUP BY b.job_no_mst, b.po_number, a.pre_cost_fabric_cost_dtls_id, a.trim_group, c.item_color, c.item_size, c.gmts_sizes, c.zipper_break_down, a.uom, a.description";
		//echo $sql_no_sen;
		$nameArray_item=sql_select($sql_no_sen); $isZipColor=0;
		if(count($nameArray_item)>0)
		{
			$po_wise_data=array();
			foreach ($nameArray_item as $row)
			{
				if($row[csf('zipper_break_down')]==0) $row[csf('zipper_break_down')]="";
				if($row[csf('zipper_break_down')]!="") $isZipColor=1;
				$exzipperdata=explode("$!$",$row[csf('zipper_break_down')]);
				
				$extapcolor=explode("$!",$exzipperdata[0]);
				$exteethcolor=explode("$!",$exzipperdata[1]);
				$exslidercolor=explode("$!",$exzipperdata[2]);
				$expullcolor=explode("$!",$exzipperdata[3]);
				
				$tapcolor=$extapcolor[1];
				$teethcolor=$exteethcolor[1];
				$slidercolor=$exslidercolor[1];
				$pullcolor=$expullcolor[1];
				
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_size')]][$row[csf('description')]]['trim_group']=$row[csf('trim_group')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_size')]][$row[csf('description')]]['item_color'].=$color_library[$row[csf('item_color')]]."***";
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_size')]][$row[csf('description')]]['item_size']=$row[csf('item_size')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_size')]][$row[csf('description')]]['gmts_sizes'].=$size_library[$row[csf('gmts_sizes')]]."***";
				
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_size')]][$row[csf('description')]]['tapcolor'].=$tapcolor."***";
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_size')]][$row[csf('description')]]['teethcolor'].=$teethcolor."***";
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_size')]][$row[csf('description')]]['slidercolor'].=$slidercolor."***";
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_size')]][$row[csf('description')]]['pullcolor'].=$pullcolor."***";
				
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_size')]][$row[csf('description')]]['rate']=$row[csf('rate')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_size')]][$row[csf('description')]]['amount']+=$row[csf('amount')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_size')]][$row[csf('description')]]['qnty']+=$row[csf('qnty')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_size')]][$row[csf('description')]]['uom'].=$unit_of_measurement[$row[csf('uom')]]."***";
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_size')]][$row[csf('description')]]['description']=$row[csf('description')];
			}
			$job_span_arr=array(); $po_span_arr=array(); $item_span_arr=array(); $size_span_arr=array();
			foreach ($po_wise_data as $job_no => $job_data) 
			{
				$span=0;
				foreach ($job_data as $po_id => $po_data) 
				{
					$po_span=0;
					foreach ($po_data as $item_group => $item_data) 
					{
						$item_span=0;
						foreach ($item_data as $size_id => $size_data) 
						{
							$size_span=0;
							foreach ($size_data as $desp => $desp_data) 
							{
								$span++; $po_span++; $item_span++; $size_span++;
							}
							$size_span_arr[$job_no][$po_id][$item_group][$size_id]=$size_span;
						}
						$item_span_arr[$job_no][$po_id][$item_group]=$item_span;
					}
					$po_span_arr[$job_no][$po_id]=$po_span;
				}
				$job_span_arr[$job_no]=$span;
			}
			//$po_quantity[$result_job[csf('id')]];
        ?>
        <tr>
        <td>
        <table width="1300px" style="margin-top: 10px;">
        	<tr>
        		<th align="left" width="300">Size Sensitive</th>
        	</tr>
        </table>
        <table  border="1" align="left" class="rpt_table"  cellpadding="0" width="1300px" cellspacing="0" rules="all" >
        	<thead>
        		<tr>
        			<th width="90">Job no.</th>
        			<th width="100">PO</th>
        			<th width="100">Style Ref No.</th>
        			<th width="100">ITEM</th>
        			<th width="100">Item Description</th>
        			<th width="60">Gmts Size </th>
        			<th width="90">Color</th>
                    <? if($isZipColor==1) { ?>
                    <th width="60">Tape Color</th>
                    <th width="60">Teeth Color</th>
                    <th width="60">Slider Color</th>
                    <th width="60">Pull Color</th>
                    <? } ?>
        			<th width="70">Measurement<br>/Count</th>
        			<th width="80">Qty </th>
        			<th width="50">Unit </th>
        			<th width="50">Rate </th>
        			<th width="80">Total Amount </th>
        			<th>Remarks </th>
        		</tr>
        	</thead>
        	<tbody>
        		<?php 
        			$qnty=0; $amount=0; $tdcols=0;
        			foreach ($po_wise_data as $job_no => $job_data) 
        			{
        				$job_span=0;
        				foreach ($job_data as $po_number => $po_data) 
        				{
        					$po_span=0;
        					foreach ($po_data as $item_group => $item_data) 
							{
								$item_span=0;
	        					foreach ($item_data as $size_id => $size_data) 
	        					{
	        						$size_span=0;
	        						foreach ($size_data as $desp => $desp_data) 
									{
			        					?>
			        					<tr>
			        						<?php if ($job_span==0): ?>
			        						<td rowspan="<? echo $job_span_arr[$job_no];?>" style="word-break:break-all"><?php echo $job_no;$job_span++; ?></td>
			        						<?php endif ?>
			        						<?php if ($po_span==0): ?>
			        							<td rowspan="<? echo $po_span_arr[$job_no][$po_number];?>" style="word-break:break-all"><?php echo $po_number;$po_span++; ?></td>
				        						<td rowspan="<? echo $po_span_arr[$job_no][$po_number];?>" style="word-break:break-all"><?php echo $style_ref[$job_no]; ?></td>
				        					<?php endif ?>
				        					<?php if ($item_span==0): ?>
				        						<td rowspan="<?=$item_span_arr[$job_no][$po_number][$item_group];?>" style="word-break:break-all"><?=$trim_group_library[$desp_data['trim_group']];$item_span++; ?></td>
				        					<?php endif ?>
			        						<td style="word-break:break-all"><?=$desp; ?></td>
			        						<td align="center" style="word-break:break-all"><?=implode(",",array_unique(explode("***",chop($desp_data['gmts_sizes'],"***")))); ?></td>
			        						<td align="center" style="word-break:break-all"><?=implode(",",array_unique(explode("***",chop($desp_data['item_color'],"***")))); ?></td>
                                            <? if($isZipColor==1) { 
											$tdcols=4;
											?>
                                            <td align="center" style="word-break:break-all"><?=implode(",", array_unique(explode("***",chop($desp_data['tapcolor'],"***")))); ?></td>
                                            <td align="center" style="word-break:break-all"><?=implode(",",array_unique(explode("***",chop($desp_data['teethcolor'],"***")))); ?></td>
                                            <td align="center" style="word-break:break-all"><?=implode(",",array_unique(explode("***",chop($desp_data['slidercolor'],"***")))); ?></td>
                                            <td align="center" style="word-break:break-all"><?=implode(",",array_unique(explode("***",chop($desp_data['pullcolor'],"***")))); ?></td>
                                            <? } ?>
			        						<?php if ($size_span==0): ?>
			        						<td align="center" rowspan="<?=$size_span_arr[$job_no][$po_id][$item_group][$size_id];?>"><?=$size_id;$size_span++; ?></td>
			        						<?php endif ?>
			        						<td align="right"><?=number_format($desp_data['qnty'],4); ?></td>
			        						<td align="center"><?=implode(",", array_unique(explode("***", chop($desp_data['uom'],"***")))); ?></td>
			        						<td align="right" title="<?=$desp_data['rate']?>"><?=fn_number_format($desp_data['amount']/$desp_data['qnty'],4); ?></td>
			        						<td align="right"><?=number_format($desp_data['amount'],4); ?></td>
			        						<td>&nbsp;</td>
			        					</tr>
			        					<?
			        					$qnty+=$desp_data['qnty'];
			        					$amount+=$desp_data['amount'];
			        				}
		        				}
		        			}
        				}
        			}
        		 ?>
        	</tbody>
        	<tfoot>
        		<tr>
        			<td colspan="<?=8+$tdcols; ?>" align="right">Total</td>
        			<td align="right"><?=number_format($qnty,4); ?></td>
        			<td>&nbsp;</td>
        			<td>&nbsp;</td>
        			<td align="right"><?=number_format($amount,4); ?></td>
        			<td>&nbsp;</td>
        		</tr>
        	</tfoot>
        </table>
        </td>
        </tr>
        <?
		}
        //==============================================Size Sensitive END=========================================  
        //==============================================As per Gmts. Color START=========================================  
		$sql_no_sen=" SELECT sum(c.requirment) qnty, b.job_no_mst as job_no, b.po_number, a.pre_cost_fabric_cost_dtls_id, a.trim_group, c.item_color, c.item_size, c.gmts_sizes, c.zipper_break_down, avg(c.rate) as rate, sum(c.amount) amount, a.uom, a.description
			         
			   FROM wo_booking_dtls a, wo_po_break_down b,wo_trim_book_con_dtls c
			   WHERE a.po_break_down_id = b.id and a.id= c.wo_trim_booking_dtls_id and a.booking_no = '$txt_booking_no' and a.status_active = 1 and a.is_deleted = 0 and c.requirment>0 and a.sensitivity = 1 and b.status_active=1 and c.status_active=1
			GROUP BY b.job_no_mst, b.po_number, a.pre_cost_fabric_cost_dtls_id, a.trim_group, c.item_color, c.item_size, c.gmts_sizes, c.zipper_break_down, a.uom, a.description";
		$nameArray_item=sql_select($sql_no_sen);
		if(count($nameArray_item)>0)
		{
			$po_wise_data=array();  $isZipColor=0;
			foreach ($nameArray_item as $row)
			{
				if($row[csf('zipper_break_down')]==0) $row[csf('zipper_break_down')]="";
				if($row[csf('zipper_break_down')]!="") $isZipColor=1;
				$exzipperdata=explode("$!$",$row[csf('zipper_break_down')]);
				
				$extapcolor=explode("$!",$exzipperdata[0]);
				$exteethcolor=explode("$!",$exzipperdata[1]);
				$exslidercolor=explode("$!",$exzipperdata[2]);
				$expullcolor=explode("$!",$exzipperdata[3]);
				
				$tapcolor=$extapcolor[1];
				$teethcolor=$exteethcolor[1];
				$slidercolor=$exslidercolor[1];
				$pullcolor=$expullcolor[1];
				
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('description')]]['trim_group']=$row[csf('trim_group')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('description')]]['item_color']=$row[csf('item_color')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('description')]]['item_size'].=$row[csf('item_size')]."***";
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('description')]]['gmts_sizes'].=$size_library[$row[csf('gmts_sizes')]]."***";
				
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('description')]]['tapcolor'].=$tapcolor."***";
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('description')]]['teethcolor'].=$teethcolor."***";
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('description')]]['slidercolor'].=$slidercolor."***";
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('description')]]['pullcolor'].=$pullcolor."***";
				
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('description')]]['rate']=$row[csf('rate')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('description')]]['amount']+=$row[csf('amount')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('description')]]['qnty']+=$row[csf('qnty')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('description')]]['uom'].=$unit_of_measurement[$row[csf('uom')]]."***";
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('description')]]['description']=$row[csf('description')];
			}
			
			$job_span_arr=array(); $po_span_arr=array(); $item_span_arr=array(); $color_span_arr=array();
			foreach ($po_wise_data as $job_no => $job_data) 
			{
				$span=0;
				foreach ($job_data as $po_id => $po_data) 
				{
					$po_span=0;
					foreach ($po_data as $item_group => $item_data) 
					{
						$item_span=0;
						foreach ($item_data as $color_id => $color_data) 
						{
							$color_span=0;
							foreach ($color_data as $desp => $desp_data) 
							{
								$span++; $po_span++; $item_span++; $color_span++;
							}
							$color_span_arr[$job_no][$po_id][$item_group][$color_id]=$color_span;
						}
						$item_span_arr[$job_no][$po_id][$item_group]=$item_span;
					}
					$po_span_arr[$job_no][$po_id]=$po_span;
				}
				$job_span_arr[$job_no]=$span;
			}

			//$po_quantity[$result_job[csf('id')]];
        ?>
        <tr>
        <td>
        <table width="1300px" style="margin-top: 10px;">
        	<tr>
        		<th align="left" width="300">As Per Garments Color</th>
        		
        	</tr>
        </table>
        <table  border="1" align="left" class="rpt_table"  cellpadding="0" width="1300px" cellspacing="0" rules="all" >
        	<thead>
        		<tr>
        			<th width="90">Job no.</th>
        			<th width="100">PO</th>
        			<th width="100">Style Ref No.</th>
        			<th width="100">ITEM</th>
        			<th width="100">Item Description</th>
        			<th width="60"> Gmts Size </th>
        			<th width="90">Color </th>
                    <? if($isZipColor==1) { ?>
                    <th width="60">Tape Color</th>
                    <th width="60">Teeth Color</th>
                    <th width="60">Slider Color</th>
                    <th width="60">Pull Color</th>
                    <? } ?>
        			<th width="60">Measurement<br>/Count </th>
        			<th width="70">Qty </th>
        			<th width="50">Unit </th>
        			<th width="50">Rate </th>
        			<th width="80">Total Amount </th>
        			<th>Remarks </th>
        		</tr>
        	</thead>
        	<tbody>
        		<?php 
        			$qnty=0; $amount=0; $tdcols=0;
        			foreach ($po_wise_data as $job_no => $job_data) 
        			{
        				$job_span=0;
        				foreach ($job_data as $po_number => $po_data) 
        				{
        					$po_span=0;
        					foreach ($po_data as $item_group => $item_data) 
							{
								$item_span=0;
	        					foreach ($item_data as $color_id => $color_data) 
	        					{
	        						$color_span=0;
	        						foreach ($color_data as $desp => $desp_data) 
									{
			        					?>
			        					<tr>
			        						<?php if ($job_span==0): ?>
			        						<td rowspan="<?=$job_span_arr[$job_no];?>" style="word-break:break-all"><?=$job_no; $job_span++;?></td>
			        						<?php endif ?>
			        						<?php if ($po_span==0): ?>
				        						<td rowspan="<?=$po_span_arr[$job_no][$po_number];?>" style="word-break:break-all"><?=$po_number; ?></td>
				        						<td rowspan="<?=$po_span_arr[$job_no][$po_number];?>" style="word-break:break-all"><?=$style_ref[$job_no];$po_span++; ?></td>
				        					<?php endif ?>
				        					<?php if ($item_span==0): ?>
				        						<td rowspan="<?=$item_span_arr[$job_no][$po_number][$item_group];?>" style="word-break:break-all"><?=$trim_group_library[$desp_data['trim_group']];$item_span++; ?></td>
				        					<?php endif ?>
			        						<td style="word-break:break-all"><?=$desp; ?></td>
			        						<td align="center" style="word-break:break-all"><?=implode(",", array_unique(explode("***", chop($desp_data['gmts_sizes'],"***")))); ?></td>
			        						<?php if ($color_span==0): ?>
			        						<td align="center" rowspan="<?=$color_span_arr[$job_no][$po_id][$item_group][$color_id];?>" style="word-break:break-all"><?=$color_library[$color_id];$color_span++; ?></td>
			        						<?php endif ?>
                                            <? if($isZipColor==1) { 
											$tdcols=4;
											?>
                                            <td style="word-break:break-all"><?=implode(",", array_unique(explode("***", chop($desp_data['tapcolor'],"***")))); ?></td>
                                            <td style="word-break:break-all"><?=implode(",", array_unique(explode("***", chop($desp_data['teethcolor'],"***")))); ?></td>
                                            <td style="word-break:break-all"><?=implode(",", array_unique(explode("***", chop($desp_data['slidercolor'],"***")))); ?></td>
                                            <td style="word-break:break-all"><?=implode(",", array_unique(explode("***", chop($desp_data['pullcolor'],"***")))); ?></td>
                                            <? } ?>
			        						<td align="center" style="word-break:break-all"><?=implode(",", array_unique(explode("***", chop($desp_data['item_size'],"***")))); ?></td>
			        						<td align="right"><?=number_format($desp_data['qnty'],4); ?></td>
			        						<td align="center"><?=implode(",", array_unique(explode("***", chop($desp_data['uom'],"***")))); ?></td>
			        						<td align="right" title="<?=$desp_data['rate'];?>"><?=fn_number_format($desp_data['amount']/$desp_data['qnty'],4); ?></td>
			        						<td align="right"><?=number_format($desp_data['amount'],4); ?></td>
			        						<td></td>
			        					</tr>
			        					<?
			        					$qnty+=$desp_data['qnty'];
			        					$amount+=$desp_data['amount'];
			        				}
		        				}
		        			}
        				}
        			}
        		 ?>
        	</tbody>
        	<tfoot>
        		<tr>
        			<td colspan="<?=8+$tdcols; ?>" align="right">Total</td>
        			<td align="right"><?php echo number_format($qnty,4); ?></td>
        			<td></td>
        			<td></td>
        			<td align="right"><?php echo number_format($amount,4); ?></td>
        			<td></td>
        		</tr>
        	</tfoot>
        </table>
        </td>
        </tr>
        <?
		}
        //==============================================As Per Garments Color End=========================================  
        //==============================================Color And Size Sensitive Start=========================================  
		$sql_no_sen="SELECT sum(d.requirment) qnty, b.job_no_mst as job_no, b.po_number, a.pre_cost_fabric_cost_dtls_id, a.trim_group, d.item_color, d.item_size, d.gmts_sizes, d.zipper_break_down, avg(d.rate) as rate, sum(d.amount) amount, a.uom, a.description, d.color_number_id
			         
			   FROM wo_booking_dtls a, wo_po_break_down b ,wo_trim_book_con_dtls d
			   WHERE a.po_break_down_id = b.id and a.id = d.wo_trim_booking_dtls_id and b.job_no_mst=d.job_no and a.booking_no = '$txt_booking_no' and a.status_active = 1 and a.is_deleted = 0 and d.requirment>0 and a.sensitivity = 4 and b.status_active=1 and d.status_active=1
			       
			GROUP BY b.job_no_mst, b.po_number, a.pre_cost_fabric_cost_dtls_id, a.trim_group, d.item_color, d.item_size, d.gmts_sizes, d.zipper_break_down, a.uom, a.description, d.color_number_id";
		//echo $sql_no_sen;
		$nameArray_item=sql_select($sql_no_sen);
		if(count($nameArray_item)>0)
		{
			$po_wise_data=array(); $isZipColor=0;
			foreach ($nameArray_item as $row)
			{
				if($row[csf('zipper_break_down')]==0) $row[csf('zipper_break_down')]="";
				if($row[csf('zipper_break_down')]!="") $isZipColor=1;
				$exzipperdata=explode("$!$",$row[csf('zipper_break_down')]);
				
				$extapcolor=explode("$!",$exzipperdata[0]);
				$exteethcolor=explode("$!",$exzipperdata[1]);
				$exslidercolor=explode("$!",$exzipperdata[2]);
				$expullcolor=explode("$!",$exzipperdata[3]);
				
				$tapcolor=$extapcolor[1];
				$teethcolor=$exteethcolor[1];
				$slidercolor=$exslidercolor[1];
				$pullcolor=$expullcolor[1];
				
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('item_size')]][$row[csf('description')]]['trim_group']=$row[csf('trim_group')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('item_size')]][$row[csf('description')]]['item_color']=$row[csf('item_color')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('item_size')]][$row[csf('description')]]['item_size']=$row[csf('item_size')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('item_size')]][$row[csf('description')]]['gmts_sizes'].=$size_library[$row[csf('gmts_sizes')]]."***";
				
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('item_size')]][$row[csf('description')]]['tapcolor'].=$tapcolor."***";
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('item_size')]][$row[csf('description')]]['teethcolor'].=$teethcolor."***";
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('item_size')]][$row[csf('description')]]['slidercolor'].=$slidercolor."***";
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('item_size')]][$row[csf('description')]]['pullcolor'].=$pullcolor."***";
				
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('item_size')]][$row[csf('description')]]['gmts_color'].=$color_library[$row[csf('color_number_id')]]."***";
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('item_size')]][$row[csf('description')]]['rate']=$row[csf('rate')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('item_size')]][$row[csf('description')]]['amount']+=$row[csf('amount')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('item_size')]][$row[csf('description')]]['qnty']+=$row[csf('qnty')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('item_size')]][$row[csf('description')]]['uom'].=$unit_of_measurement[$row[csf('uom')]]."***";
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('item_size')]][$row[csf('description')]]['description']=$row[csf('description')];
			}
			$job_span_arr=array(); $po_span_arr=array(); $item_span_arr=array(); $color_span_arr=array(); $size_span_arr=array();
			foreach ($po_wise_data as $job_no => $job_data) 
			{
				$span=0;
				foreach ($job_data as $po_id => $po_data) 
				{
					$po_span=0;
					foreach ($po_data as $item_group => $item_data) 
					{
						$item_span=0;
						foreach ($item_data as $color_id => $color_data) 
						{
							$color_span=0;
							foreach ($color_data as $size_id => $size_data) 
							{
								$size_span=0;
								foreach ($size_data as $desp => $desp_data) 
								{
									$span++; $po_span++; $item_span++; $color_span++; $size_span++;
								}
								$size_span_arr[$job_no][$po_id][$item_group][$color_id][$size_id]=$size_span;
							}
							$color_span_arr[$job_no][$po_id][$item_group][$color_id]=$color_span;
						}
						$item_span_arr[$job_no][$po_id][$item_group]=$item_span;
					}
					$po_span_arr[$job_no][$po_id]=$po_span;
				}
				$job_span_arr[$job_no]=$span;
			}
			

			//$po_quantity[$result_job[csf('id')]];
        ?>
        <tr>
        <td>
        <table width="1300px" style="margin-top: 10px;">
        	<tr>
        		<th align="left" width="300">Color And Size Sensitive</th>
        		<th >Item Size</th>
        	</tr>
        </table>
        <table  border="1" align="left" class="rpt_table"  cellpadding="0" width="1300px" cellspacing="0" rules="all" >
        	<thead>
        		<tr>
        			<th width="90">Job No.</th>
        			<th width="100">PO</th>
        			<th width="100">Style Ref No.</th>
        			<th width="100">ITEM</th>
        			<th width="100">Item Description</th>
        			<th width="60"> Gmts Color</th>
        			<th width="90"> Gmts Size</th>
        			<th width="90">Item Color</th>
                    <? if($isZipColor==1) { ?>
                    <th width="60">Tape Color</th>
                    <th width="60">Teeth Color</th>
                    <th width="60">Slider Color</th>
                    <th width="60">Pull Color</th>
                    <? } ?>
        			<th width="60">Measurement<br>/Count</th>
        			<th width="70">Qty</th>
        			<th width="50">Unit</th>
        			<th width="50">Rate</th>
        			<th width="80">Total Amount</th>
        			<th>Remarks </th>
        		</tr>
        	</thead>

        	<tbody>
        		<?php 
        			$qnty=0; $amount=0; $tdcols=0;
        			foreach ($po_wise_data as $job_no => $job_data) 
        			{
        				$job_span=0;
        				foreach ($job_data as $po_number => $po_data) 
        				{
        					$po_span=0;
        					foreach ($po_data as $item_group => $item_data) 
        					{
        						$item_span=0;
        						foreach ($item_data as $color_id => $color_data) 
								{
									$color_span=0;
									foreach ($color_data as $size_id => $size_data) 
									{
										$size_span=0;
										foreach ($size_data as $desp => $desp_data) 
										{
				        					?>
				        					<tr>
				        						<?php if ($job_span==0): ?>
				        						<td style="word-break:break-all" rowspan="<?=$job_span_arr[$job_no];?>"><?=$job_no; $job_span++;?></td>
				        						<?php endif ?>
				        						<?php if ($po_span==0): ?>
				        							<td style="word-break:break-all" rowspan="<?=$po_span_arr[$job_no][$po_number];?>"><?=$po_number;$po_span++; ?></td>
					        						<td style="word-break:break-all" rowspan="<?=$po_span_arr[$job_no][$po_number];?>"><?=$style_ref[$job_no]; ?></td>
					        					<?php endif ?>
					        					<?php if ($item_span==0): ?>
					        						<td style="word-break:break-all" rowspan="<?=$item_span_arr[$job_no][$po_number][$item_group];?>"><?=$trim_group_library[$desp_data['trim_group']];$item_span++; ?></td>
				        						<?php endif ?>
				        						<td style="word-break:break-all"><?=$desp; ?></td>
				        						
				        						<td style="word-break:break-all"><?=implode(",", array_unique(explode("***", chop($desp_data['gmts_color'],"***")))); ?></td>
				        						<td style="word-break:break-all"><?=implode(",", array_unique(explode("***", chop($desp_data['gmts_sizes'],"***")))); ?></td>

				        						<?php if ($color_span==0): ?>
				        						<td style="word-break:break-all" rowspan="<?=$color_span_arr[$job_no][$po_id][$item_group][$color_id];?>"><?=$color_library[$desp_data['item_color']];$color_span++; ?></td>
                                                
				        						<?php endif ?>
				        						<?php if ($size_span==0): 
												$sizeSpan=$size_span_arr[$job_no][$po_id][$item_group][$color_id][$size_id];
												if($isZipColor==1) { 
													$tdcols=4;
												?>
                                                <td style="word-break:break-all" rowspan="<?=$sizeSpan;?>"><?=implode(",", array_unique(explode("***", chop($desp_data['tapcolor'],"***")))); ?></td>
                                                <td style="word-break:break-all" rowspan="<?=$sizeSpan;?>"><?=implode(",", array_unique(explode("***", chop($desp_data['teethcolor'],"***")))); ?></td>
                                                <td style="word-break:break-all" rowspan="<?=$sizeSpan;?>"><?=implode(",", array_unique(explode("***", chop($desp_data['slidercolor'],"***")))); ?></td>
                                                <td style="word-break:break-all" rowspan="<?=$sizeSpan;?>"><?=implode(",", array_unique(explode("***", chop($desp_data['pullcolor'],"***")))); ?></td>
                                                <? } ?>
                                                <td style="word-break:break-all" rowspan="<?=$sizeSpan; ?>"><?=$desp_data['item_size'];$size_span++; ?></td>
                                                
				        						<?php endif ?>
				        						<td align="right"><?=number_format($desp_data['qnty'],4); ?></td>
				        						<td align="center"><?=implode(",", array_unique(explode("***", chop($desp_data['uom'],"***")))); ?></td>
				        						<td align="right" title="<?=$desp_data['rate'];?>"><?=number_format($desp_data['amount']/$desp_data['qnty'],4); ?></td>
				        						<td align="right"><?=number_format($desp_data['amount'],4); ?></td>
				        						<td></td>
				        					</tr>
				        					<?
				        					$qnty+=$desp_data['qnty'];
				        					$amount+=$desp_data['amount'];
			        					}
			        				}
			        			}
        					}
        				}
        			}
        		 ?>
        	</tbody>
        	<tfoot>
        		<tr>
        			<td colspan="<?=9+$tdcols; ?>" align="right">Total</td>
        			<td align="right"><?=number_format($qnty,4); ?></td>
        			<td></td>
        			<td></td>
        			<td align="right"><?=number_format($amount,4); ?></td>
        			<td></td>
        		</tr>
        	</tfoot>
        </table>
        </td>
        </tr>
        <?
		}
       // ==============================================Color And Size Sensitive End=========================================  
       // ==============================================Contrast Color Start=========================================  
		$sql_no_sen="SELECT sum(c.requirment) qnty, b.job_no_mst as job_no, b.po_number, a.pre_cost_fabric_cost_dtls_id, a.trim_group, c.item_color, c.item_size, c.gmts_sizes, c.zipper_break_down, avg(c.rate) as rate, sum(c.amount) amount, a.uom, a.description
			         
			   FROM wo_booking_dtls a, wo_po_break_down b,wo_trim_book_con_dtls c
			   WHERE a.po_break_down_id = b.id and a.id= c.wo_trim_booking_dtls_id and a.booking_no = '$txt_booking_no' and a.status_active = 1 and a.is_deleted = 0 and c.requirment>0 and a.sensitivity = 3 and b.status_active=1 and c.status_active=1
			GROUP BY b.job_no_mst, b.po_number, a.pre_cost_fabric_cost_dtls_id, a.trim_group, c.item_color, c.item_size, c.gmts_sizes, c.zipper_break_down, a.uom, a.description";
		$nameArray_item=sql_select($sql_no_sen);
		if(count($nameArray_item)>0)
		{
			$po_wise_data=array(); $isZipColor=0;
			foreach ($nameArray_item as $row)
			{
				if($row[csf('zipper_break_down')]==0) $row[csf('zipper_break_down')]="";
				if($row[csf('zipper_break_down')]!="") $isZipColor=1;
				$exzipperdata=explode("$!$",$row[csf('zipper_break_down')]);
				
				$extapcolor=explode("$!",$exzipperdata[0]);
				$exteethcolor=explode("$!",$exzipperdata[1]);
				$exslidercolor=explode("$!",$exzipperdata[2]);
				$expullcolor=explode("$!",$exzipperdata[3]);
				
				$tapcolor=$extapcolor[1];
				$teethcolor=$exteethcolor[1];
				$slidercolor=$exslidercolor[1];
				$pullcolor=$expullcolor[1];
				
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('description')]]['trim_group']=$row[csf('trim_group')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('description')]]['item_color']=$row[csf('item_color')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('description')]]['item_size'].=$row[csf('item_size')]."***";
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('description')]]['gmts_sizes'].=$size_library[$row[csf('gmts_sizes')]]."***";
				
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('description')]]['tapcolor'].=$tapcolor."***";
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('description')]]['teethcolor'].=$teethcolor."***";
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('description')]]['slidercolor'].=$slidercolor."***";
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('description')]]['pullcolor'].=$pullcolor."***";
				
				
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('description')]]['rate']=$row[csf('rate')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('description')]]['amount']+=$row[csf('amount')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('description')]]['qnty']+=$row[csf('qnty')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('description')]]['uom'].=$unit_of_measurement[$row[csf('uom')]]."***";
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('description')]]['description']=$row[csf('description')];
			}
			$job_span_arr=array(); $po_span_arr=array(); $item_span_arr=array(); $color_span_arr=array();
			foreach ($po_wise_data as $job_no => $job_data) 
			{
				$span=0;
				foreach ($job_data as $po_id => $po_data) 
				{
					$po_span=0;
					foreach ($po_data as $item_group => $item_data) 
					{
						$item_span=0;
						foreach ($item_data as $color_id => $color_data) 
						{
							$color_span=0;
							foreach ($color_data as $desp => $desp_data) 
							{
								$span++; $po_span++; $item_span++; $color_span++;
							}
							$color_span_arr[$job_no][$po_id][$item_group][$color_id]=$color_span;
						}
						$item_span_arr[$job_no][$po_id][$item_group]=$item_span;
					}
					$po_span_arr[$job_no][$po_id]=$po_span;
				}
				$job_span_arr[$job_no]=$span;
			}
			//$po_quantity[$result_job[csf('id')]];
        ?>
        <tr>
        <td>
        <table width="1300px" style="margin-top: 10px;">
        	<tr>
        		<th align="left" width="300">Contrast Color</th>
        		
        	</tr>
        </table>
        <table  border="1" align="left" class="rpt_table"  cellpadding="0" width="1300px" cellspacing="0" rules="all" >
        	<thead>
        		<tr>
        			<th width="90">Job no.</th>
        			<th width="100">PO</th>
        			<th width="100">Style Ref No.</th>
        			<th width="100">ITEM</th>
        			<th width="100">Item Description</th>
        			<th width="60"> Gmts Size </th>
        			<th width="90">Color </th>
                    <? if($isZipColor==1) { ?>
                    <th width="60">Tape Color</th>
                    <th width="60">Teeth Color</th>
                    <th width="60">Slider Color</th>
                    <th width="60">Pull Color</th>
                    <? } ?>
        			<th width="60">Measurement<br>/Count </th>
        			<th width="70">Qty </th>
        			<th width="50">Unit </th>
        			<th width="50">Rate </th>
        			<th width="80">Total Amount </th>
        			<th>Remarks </th>
        		</tr>
        	</thead>
        	<tbody>
        		<?php 
        			$qnty=0; $amount=0; $tdcols=0;
        			foreach ($po_wise_data as $job_no => $job_data) 
        			{
        				$job_span=0;
        				foreach ($job_data as $po_number => $po_data) 
        				{
        					$po_span=0;
        					foreach ($po_data as $item_group => $item_data) 
							{
								$item_span=0;
	        					foreach ($item_data as $color_id => $color_data) 
	        					{
	        						$color_span=0;
	        						foreach ($color_data as $desp => $desp_data) 
									{
			        					?>
			        					<tr>
			        						<?php if ($job_span==0): ?>
			        						<td style="word-break:break-all" rowspan="<?=$job_span_arr[$job_no];?>"><?=$job_no; $job_span++;?></td>
			        						<?php endif ?>
			        						<?php if ($po_span==0): ?>
				        						<td style="word-break:break-all" rowspan="<?=$po_span_arr[$job_no][$po_number];?>"><?=$po_number; ?></td>
				        						<td style="word-break:break-all" rowspan="<?=$po_span_arr[$job_no][$po_number];?>"><?=$style_ref[$job_no];$po_span++; ?></td>
				        					<?php endif ?>
				        					<?php if ($item_span==0): ?>
				        						<td style="word-break:break-all" rowspan="<?=$item_span_arr[$job_no][$po_number][$item_group];?>"><?=$trim_group_library[$desp_data['trim_group']];$item_span++; ?></td>
				        					<?php endif ?>
			        						<td style="word-break:break-all"><?=$desp; ?></td>
			        						<td style="word-break:break-all" ><?=implode(",", array_unique(explode("***", chop($desp_data['gmts_sizes'],"***")))); ?></td>
			        						<?php if ($color_span==0): ?>
			        						<td style="word-break:break-all" rowspan="<?=$color_span_arr[$job_no][$po_id][$item_group][$color_id];?>"><?=$color_library[$desp_data['item_color']];$color_span++; ?></td>
			        						<?php endif ?>
                                            <?  if($isZipColor==1) { 
													$tdcols=4; ?>
                                            <td style="word-break:break-all"><?=implode(",", array_unique(explode("***", chop($desp_data['tapcolor'],"***")))); ?></td>
                                            <td style="word-break:break-all"><?=implode(",", array_unique(explode("***", chop($desp_data['teethcolor'],"***")))); ?></td>
                                            <td style="word-break:break-all"><?=implode(",", array_unique(explode("***", chop($desp_data['slidercolor'],"***")))); ?></td>
                                            <td style="word-break:break-all"><?=implode(",", array_unique(explode("***", chop($desp_data['pullcolor'],"***")))); ?></td>
                                            <? } ?>
			        						<td style="word-break:break-all"><?=implode(",", array_unique(explode("***", chop($desp_data['item_size'],"***")))); ?></td>
			        						<td align="right"><?php echo number_format($desp_data['qnty'],4); ?></td>
			        						<td align="center"><?php echo implode(",", array_unique(explode("***", chop($desp_data['uom'],"***")))); ?></td>
			        						<td align="right" title="<?=$desp_data['rate']?>"><?php echo  fn_number_format($desp_data['amount']/$desp_data['qnty'],4); ?></td>
			        						<td align="right"><?php echo  number_format($desp_data['amount'],4); ?></td>
			        						<td></td>
			        					</tr>
			        					<?
			        					$qnty+=$desp_data['qnty'];
			        					$amount+=$desp_data['amount'];
			        				}
		        				}
		        			}
        				}
        			}

        		 ?>
        	</tbody>
        	<tfoot>
        		<tr>
        			<td colspan="<?=8+$tdcols; ?>" align="right">Total</td>
        			<td align="right"><?php echo number_format($qnty,4); ?></td>
        			<td></td>
        			<td></td>
        			<td align="right"><?php echo number_format($amount,4); ?></td>
        			<td></td>
        		</tr>
        	</tfoot>
        </table>
        </td>
        </tr>
        <?
		}
		?>
        <tr>
        <td>
        <!--==============================================Contrast Color End=========================================  -->
        <table border="1" align="left" cellpadding="0" style="width: 1300px;margin-top: 10px;" cellspacing="0" rules="all" >
         	<tr>
         		<th colspan="7" style="justify-content: left;text-align: left;">Sample Requirements</th>
         	</tr>
         	<tr>
         		<th>Discription</th>
         		<th>Color</th>
         		<th>Measurrment/ Count</th>
         		<th>Quantity</th>
         		<th>Sample Delivery date</th>
         		<th>Remarks</th>
         	</tr>
         	<tr>
         		<td rowspan="2">&nbsp;</td>
         		<td>&nbsp;</td>
         		<td>&nbsp;</td>
         		<td>&nbsp;</td>
         		<td>&nbsp;</td>
         		<td>&nbsp;</td>
         	</tr>
         	<tr>
         		<td>&nbsp;</td>
         		<td>&nbsp;</td>
         		<td>&nbsp;</td>
         		<td>&nbsp;</td>
         		<td>&nbsp;</td>
         	</tr>
        </table>
        <br>
        <table style="width: 1300px;">
            <tr>
                <td width="40%">
                    <?=get_spacial_instruction($txt_booking_no); ?>
                </td>
                <td width="20%"></td>
                <td width="40%" valign="top">
                    <table width="100%" >
                        <tr align="left">
							<td><strong>Shipping Mark as below.</strong></td>
						</tr>
						<tr align="left">
							<td>Company Name : <? echo $company_library[$cbo_company_name]; ?></td>
						</tr>
						<tr align="left">
							<td>Address : <? echo $address; ?></td>
						</tr>
						<tr align="left">
							<td>Item Details : </td>
						</tr>
						<tr align="left">
							<td>Color : </td>
						</tr>
						<tr align="left">
							<td>Roll No : ____ of ____</td>
						</tr>
						<tr align="left">
							<td>Quantity : </td>
						</tr>
						<tr align="left">
							<td>Net Weight : </td>
						</tr>
						<tr align="left">
							<td>Gross Weight : </td>
						</tr>
                    </table>
            	</td>
            </tr>
        </table>
   	 </tbody>
     </table>
     <br>
	<h4>Note: Please mention all above description, terms and conditions in the PI</h4>
    <div  style="margin-top:-50px;">
         <?=signature_table(132, $cbo_company_name, "1300px",1); ?>
   </div>
<? 
	$emailBody=ob_get_contents();
	//ob_clean();
	if($is_mail_send==1){
		$req_approved=return_field_value("id", "ELECTRONIC_APPROVAL_SETUP", "PAGE_ID = 336 and is_deleted=0");
		$is_approved=return_field_value("IS_APPROVED", "WO_BOOKING_MST", "STATUS_ACTIVE = 1 and is_deleted=0 and booking_no='$txt_booking_no'");
		if($req_approved && $is_approved==1){
			$emailBody.="<h1 style='border:1px sloid #0ff;'>Approved</h1>";
		}
		elseif($req_approved && $is_approved==0){
			$emailBody.="<h1 style='border:1px sloid #0ff;'>Draft</h1>";
		}
		
	
		$mailArr=array();
		if($mail_id!=''){$mailArr[]=$mail_id;}
		if($supplier_mail_arr[$supplier_id]!=''){$mailArr[]=$supplier_mail_arr[$supplier_id];}
		
		
		$to=implode(',',$mailArr);
		$subject="Trims Booking Auto Mail";
		
		if($to!=""){
			require '../../../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
			require_once('../../../auto_mail/setting/mail_setting.php');
			$header=mailHeader();
			sendMailMailer( $to, $subject, $emailBody );
		}
	}
	?>
    <!--<div id="page_break_div"></div>-->
    <div>
		<?
        	//echo signature_table(2, $cbo_company_name, "1330px");
			//echo "****".custom_file_name($txt_booking_no,$style_sting,$job_no);
        ?>
    </div>
  </html>
	<?
	$user_id=$_SESSION['logic_erp']['user_id'];
	$report_cat=100;
	$html = ob_get_contents();
	ob_clean();
	//$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
	foreach (glob("tb*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename="tb".$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "****$html****$report_cat";
	//exit();
}

if($action=="print_t7")
{
	extract($_REQUEST);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$id_approved_id=str_replace("'","",$id_approved_id);
	$report_type=str_replace("'","",$report_type);
	$show_comment=str_replace("'","",$show_comment);

	$color_library=return_library_array("select id, color_name from lib_color", "id", "color_name");
	$size_library=return_library_array("select id, size_name from  lib_size", "id", "size_name");
	$company_library=return_library_array("select id, company_name from lib_company", "id", "company_name");
	$trim_group_library= return_library_array("select id, item_name from lib_item_group",'id','item_name');
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	//$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	//$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	
	$supplier_sql_result=sql_select("select ID,SUPPLIER_NAME,ADDRESS_1,EMAIL from lib_supplier and status_active =1 and  is_deleted=0");
	foreach($supplier_sql_result as $rows){
		$supplier_name_arr[$rows[ID]]=$rows[SUPPLIER_NAME];
		$supplier_address_arr[$rows[ID]]=$rows[ADDRESS_1];
		$supplier_mail_arr[$rows[ID]]=$rows[EMAIL];
	}

	
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$order_uom_arr=return_library_array("select id,order_uom  from lib_item_group","id","order_uom");

	$sql_mrcht=sql_select("SELECT team_member_name,id, member_contact_no,team_member_email from lib_mkt_team_member_info ");
	$marchant_data=array();
	foreach ($sql_mrcht as $row) 
	{
		$marchant_data[$row[csf('id')]]['team_member_name']=$row[csf('team_member_name')];
		$marchant_data[$row[csf('id')]]['member_contact_no']=$row[csf('member_contact_no')];
		$marchant_data[$row[csf('id')]]['team_member_email']=$row[csf('team_member_email')];
	}

	$deling_marcent_arr=return_library_array("select id,team_member_name from lib_mkt_team_member_info","id","team_member_name");

	$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");

	$nameArray_approved=sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no='$txt_booking_no' and b.entry_form=8 and  a.status_active =1 and a.is_deleted=0");
	list($nameArray_approved_row)=$nameArray_approved;
	//$revised_no_data=sql_select("select a.revised_no  from wo_booking_mst a where  a.booking_no=$txt_booking_no and  a.status_active =1 and a.is_deleted=0");
	//list($revised_no_row)=$revised_no_data;
		$booking_grand_total=0;
		$currency_id="";

		$buyer_string=array(); $style_owner=array(); $job_no=array(); $style_ref=array();
		$all_dealing_marcent=array();
		$season=array();
		$order_repeat_no=array();
		$po_id_arr=array();
		$job_data_arr=array();

		$nameArray_buyer=sql_select( "select  a.style_ref_no, a.job_no, a.style_owner, a.buyer_name, a.dealing_marchant, a.season, a.season_matrix, a.season_buyer_wise, a.order_repeat_no, b.po_break_down_id,a.gmts_item_id ,a.client_id from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no='$txt_booking_no' and b.status_active =1 and b.is_deleted=0");
        foreach ($nameArray_buyer as $result_buy){
			$buyer_string[$result_buy[csf('buyer_name')]]=$buyer_name_arr[$result_buy[csf('buyer_name')]];
			$style_owner[$result_buy[csf('job_no')]]=$company_library[$result_buy[csf('style_owner')]];
			$job_no[$result_buy[csf('job_no')]]=$result_buy[csf('job_no')];
			$style_ref[$result_buy[csf('job_no')]]=$result_buy[csf('style_ref_no')];
			$job_data_arr['gmts_item_id'][$result_buy[csf('job_no')]]=$result_buy[csf('gmts_item_id')];
			$job_data_arr['dealing_marchant'][$result_buy[csf('job_no')]]= $marchant_data[$result_buy[csf('dealing_marchant')]]['team_member_name'];
			$job_data_arr['member_contact_no'][$result_buy[csf('job_no')]]= $marchant_data[$result_buy[csf('dealing_marchant')]]['member_contact_no'];
			$job_data_arr['dealing_marchant_email'][$result_buy[csf('job_no')]]= $marchant_data[$result_buy[csf('dealing_marchant')]]['team_member_email'];
			$job_data_arr['client'][$result_buy[csf('job_no')]]=$result_buy[csf('client_id')];
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
		$job_no=implode(",",$job_no);
		$seasons_names=rtrim($seasons_name,',');

		$seasons_names=implode(",",array_unique(explode(",",$seasons_names)));
		$poid_arr=array_unique($po_id_arr);

		$order_rept_no=rtrim($order_rept_no,',');
		$order_rept_no=implode(",",array_unique(explode(",",$order_rept_no)));

		$dealing_marchant=implode(",",array_unique($job_data_arr['dealing_marchant']));
		$dealing_marchant_email=implode(",",array_unique($job_data_arr['dealing_marchant_email']));
		$member_contact_no=implode(",",array_unique($job_data_arr['member_contact_no']));
		$team_member_email=implode(",",array_unique($job_data_arr['dealing_marchant_email']));
		$gmts_item_id=implode(",",array_unique($job_data_arr['gmts_item_id']));
		$client_id= implode(",",array_unique($job_data_arr['client']));

		$po_no=array();
		$file_no=array();
		$ref_no=array();
		$po_quantity=array();
		$pub_shipment_date='';$int_ref_no='';$tot_po_quantity=0;$po_idss='';
		$nameArray_job=sql_select( "select b.job_no_mst,b.id,b.pub_shipment_date, b.po_number,b.grouping, b.file_no, sum(b.po_quantity) as po_quantity  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no='$txt_booking_no' and  a.status_active =1 and a.is_deleted=0 group by b.job_no_mst,b.id,b.pub_shipment_date, b.po_number,b.grouping, b.file_no ");
        foreach ($nameArray_job as $result_job){
			$po_no[$result_job[csf('job_no_mst')]][$result_job[csf('id')]]=$result_job[csf('po_number')];
			$file_no[$result_job[csf('id')]]=$result_job[csf('file_no')];
			$ref_no[$result_job[csf('id')]]=$result_job[csf('grouping')];
			$po_quantity[$result_job[csf('id')]]=$result_job[csf('po_quantity')];
		//	$tot_po_quantity+=$result_job[csf('po_quantity')];
			$job_ref_no[$result_job[csf('job_no_mst')]].=$result_job[csf('grouping')].',';
			$po_no_arr[$result_job[csf('job_no_mst')]]['po_id'].=$result_job[csf('id')].',';
			$pub_shipment_date.=$result_job[csf('pub_shipment_date')].',';
			$int_ref_no.=$result_job[csf('grouping')].',';
			if($po_idss=='') $po_idss=$result_job[csf('id')];else $po_idss.=",".$result_job[csf('id')];

		}
		$sql_job=sql_select( "select b.job_no_mst,b.id as po_id, b.po_quantity as po_quantity  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and  a.status_active =1 and a.is_deleted=0 and  b.status_active =1 and b.is_deleted=0 and b.id in(".$po_idss.") ");
		foreach ($sql_job as $row)
		{
			$job_po_qty_arr[$row[csf('job_no_mst')]][$row[csf('po_id')]]+=$row[csf('po_quantity')];
			$tot_po_quantity+=$row[csf('po_quantity')];
		}

        $nameArray=sql_select( "select a.booking_no, a.pay_mode,a.buyer_id, a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.source, a.remarks, a.revised_no, a.delivery_address from wo_booking_mst a where a.booking_no='$txt_booking_no' and a.status_active =1 and a.is_deleted=0");
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
			$delivery_address=$row[csf('delivery_address')];
			$revised_no=$row[csf('revised_no')];
			$source_id=$row[csf('source')];
		}

	ob_start();
	?>
	<html>
	<head>
	  <style type="text/css" media="print">

		 @media print
	        {
	            tbody {
	                page-break-inside: avoid;
					tbody {display: table-row-group;}
	            }
	            thead {
	                display: table-header-group;
					page-break-before: always;

	            }
	        }
	/*	@media print {
				  #page_break_div {
					page-break-before: always;
				  }
		}
			*/	.footer_signature {
					position: fixed;
					height: auto;
					bottom:0;
					width:100%;

					}
				@media print {
					table {
						page-break-inside: avoid;
					}
				}
				/*@media all {
	  			#page_break_div   { display: none; }
				}

				@media screen {
				thead { display: block; }
				tfoot { display: block; }
				}*/

	</style>
	</head>
   	<table width="1333px"  cellpadding="0" cellspacing="0" style="border:0px solid black" >
    <thead>
    <tr>
    <th>
    	<?
       	 	$nameArray=sql_select( "select a.buyer_id,a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.po_break_down_id,a.colar_excess_percent,a.cuff_excess_percent,a.delivery_date,a.is_apply_last_update,a.fabric_source,a.rmg_process_breakdown,a.insert_date,a.update_date,a.uom,a.remarks,a.pay_mode,a.fabric_composition,a.delivery_address, a.pay_mode, a.currency_id,a.item_category from wo_booking_mst a  where   a.booking_no='$txt_booking_no' and a.status_active =1 and a.is_deleted=0 ");

	$supplier_id=$nameArray[0][csf('supplier_id')];
	$sql_sup=sql_select("SELECT SUPPLIER_NAME,SHORT_NAME,CONTACT_NO,ADDRESS_1,email from lib_supplier where status_active=1 and is_deleted=0 and id=$supplier_id ");
	$address="";
    ?>
	<table style="table-layout: fixed;width: 1300px; " >
		<tr>
			
			<td style="text-align: center;">
				<span style=" font-size:20px; font-weight:bold"><? echo $company_library[$cbo_company_name]; ?></span><br>
				<?
                            $nameArray2=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
                            foreach ($nameArray2 as $result)
                            {
                            	?>
                                <? echo $address=$result[csf('plot_no')].' '.$result[csf('level_no')].' '.$result[csf('road_no')].' '.$result[csf('block_no')].' '.$result[csf('city')].' '.$result[csf('zip_code')].' '.$result[csf('province')].' '.$country_arr[$result[csf('country_id')]]; 
                                /*

                                ?>

                                <br>
                                Email Address: <? echo $result[csf('email')];?>
                                Website: <? echo $result[csf('website')].'<br>';
                                */
                            }
                            ?>
                            <br>
				<span style="font-size:16px; font-weight:bold">PURCHASE ORDER</span>
			</td>
			
		</tr>
	</table>
	<?

	$booking_no='';

	 foreach ($nameArray as $result) {
		$currency_id=$result[csf('currency_id')];
		$booking_date=$result[csf('update_date')];
			if($booking_date=="" || $booking_date=="0000-00-00 00:00:00"){
			$booking_date=$result[csf('insert_date')];
			}
			$booking_no=$result[csf('booking_no')];
	 ?>
	 <table style="width: 1300px;">
	 		<tr>
		 		<td align="left" style="width: 48%">
		 			<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" rules="all" width="100%">
		 				<tr>
		 					<td>Order No</td>
		 					<td><? echo $result[csf('booking_no')];?></td>
		 				</tr>
		 				<tr>
		 					<td>Order Date</td>
		 					<td><? echo change_date_format($result[csf('booking_date')]);?></td>
		 				</tr>
		 			</table>
		 			
		 			
		 			
		 			
		 		</td>
		 		<td  style="width: 4%"></td>
		 		<td align="right"  style="width: 48%">
		 			<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" rules="all" width="100%">
		 				<tr>
		 					<td>Delivery Date</td>
		 					<td><? echo change_date_format($result[csf('delivery_date')]);?></td>
		 				</tr>
		 				<tr>
		 					<td>&nbsp;</td>
		 					<td>&nbsp;</td>
		 				</tr>
		 				
		 			</table>
		 			
		 			
		 		</td>
		 	</tr>
		 	<tr>
		 		<td align="left" style="width: 48%">
			 		<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" rules="all" width="100%" >
			 				
			 				<tr><th colspan="2"><b>SUPPLIER</b></th></tr>
			 				<tr>
			 					<td width="120">Supplier Name</td>
			 					<td><? echo $sql_sup[0][csf('SUPPLIER_NAME')];?></td>
			 				</tr>
			 				<tr>
			 					<td>Supplier Code</td>
			 					<td></td>
			 				</tr>
			 				<tr>
			 					<td>Attention</td>
			 					<td><? echo $result[csf('attention')];?></td>
			 				</tr>
			 				<tr>
			 					<td>Address</td>
			 					<td><? echo $sql_sup[0][csf('ADDRESS_1')];?></td>
			 				</tr>
			 				
			 				<tr>
			 					<td>Contact No</td>
			 					<td><? echo $sql_sup[0][csf('CONTACT_NO')];?></td>
			 				</tr>
			 				<tr>
			 					<td>Email</td>
			 					<td><? echo $sql_sup[0][csf('email')];?></td>
			 				</tr>
			 			</table>
		 		</td>
		 			<td  style="width: 4%"></td>
		 			<td  align="right"  style="width: 48%">
		 				<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" rules="all" width="100%" >
			 				<caption><b>BUYER</b></caption>
			 				<tr >
			 					<td width="160">Purchaser Name</td>
			 					<td><? echo $company_library[$cbo_company_name];?></td>
			 				</tr>
			 				<tr>
			 					<td>Contact Person</td>
			 					<td><? echo $dealing_marchant; ?></td>
			 				</tr>
			 				<tr>
			 					<td>Contact No</td>
			 					<td><? echo $member_contact_no;?></td>
			 				</tr>
			 				<tr>
			 					<td>Email</td>
			 					<td><? echo $team_member_email;?></td>
			 				</tr>
			 				<tr>
			 					<td>Buyer/Agent Name</td>
			 					<td> <? $buyer_name_str=""; if($client_id!=0) $buyer_name_str=$buyer_name_arr[$result[csf('buyer_id')]]."-".$buyer_name_arr[$client_id]; else $buyer_name_str=$buyer_name_arr[$result[csf('buyer_id')]]; echo $buyer_name_str; ?></td>
			 				</tr>
			 				<tr>
			 					<td>Garments Item</td>
			 					<td>
			 						
			 							<?
								            $gmts_item_name="";
											$gmts_item=explode(',',$gmts_item_id);
											for($g=0;$g<=count($gmts_item); $g++)
											{
											$gmts_item_name.= $garments_item[$gmts_item[$g]].",";
											}
											echo rtrim($gmts_item_name,',');
										?>
										
									
								</td>
			 				</tr>
			 			</table>
		 			</td>
		 	</tr>
	 </table>
   
    <? } ?>
    </th>
    </tr>
    </thead>
     <tbody>
          
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

		


			?>



      


         <!--==============================================NO SENSITIBITY START=========================================  -->
		<?
		$sql_no_sen="
			  SELECT 
			         sum(c.requirment) qnty,
			       	 b.job_no_mst as job_no,
			         b.po_number,
			         a.pre_cost_fabric_cost_dtls_id,
			         a.trim_group,
			        
			         c.item_color,
			         c.item_size,
			         c.gmts_sizes,
			         avg(c.rate) as rate ,
			         sum(c.amount) amount,
			         a.uom,
			         a.description
			     
			         
			    FROM wo_booking_dtls a, wo_po_break_down b,wo_trim_book_con_dtls c
			   WHERE     a.po_break_down_id = b.id
			         and a.id= c.wo_trim_booking_dtls_id
			         and a.booking_no = '$txt_booking_no'
			         and a.status_active = 1
			         and a.is_deleted = 0
			         and c.requirment>0
			         and a.sensitivity = 0
			         and b.status_active=1 
			         and c.status_active=1
			GROUP BY b.job_no_mst,
			         b.po_number,
			         a.pre_cost_fabric_cost_dtls_id,
			         a.trim_group,
			        
			         c.item_color,
			         c.item_size,
			         c.gmts_sizes,
			         a.uom,
			         a.description";
		$nameArray_item=sql_select($sql_no_sen);
		if(count($nameArray_item)>0)
		{

			$po_wise_data=array();
			foreach ($nameArray_item as $row)
			{
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]]['trim_group']=$row[csf('trim_group')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]]['item_color'].=$color_library[$row[csf('item_color')]]."***";
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]]['item_size'].=$row[csf('item_size')]."***";
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]]['gmts_sizes'].=$size_library[$row[csf('gmts_sizes')]]."***";
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]]['rate']=$row[csf('rate')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]]['amount']+=$row[csf('amount')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]]['qnty']+=$row[csf('qnty')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]]['uom'].=$unit_of_measurement[$row[csf('uom')]]."***";
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]]['description'].=$row[csf('description')]."***";
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]]['pre_cost_fabric_cost_dtls_id']=$row[csf('pre_cost_fabric_cost_dtls_id')];
			}
			$job_span_arr=array();
			$po_span_arr=array();
			foreach ($po_wise_data as $job_no => $job_data) 
			{
				$span=0;
				foreach ($job_data as $po_id => $po_data) 
				{
					$po_span=0;
					$img_span=0;
					foreach ($po_data as $item_grou => $item_data) 
					{
						$span++;
						$po_span++;
						$img_span++;
					}
					$po_span_arr[$job_no][$po_id]=$po_span;
					$img_span_arr[$job_no][$po_id]=$img_span;
				}
				$job_span_arr[$job_no]=$span;

			}
			

			//$po_quantity[$result_job[csf('id')]];
        ?>
        <table width="1200" style="margin-top: 10px;">
        	<tr>
        		<th align="left" >No sensitive</th>
        		
        	</tr>
        </table>
        <table  border="1" align="left" class="rpt_table"  cellpadding="0" width="1300" cellspacing="0" rules="all" >
        	<thead>
        		<tr>
        			<th width="120">Job no.</th>
        			<th width="100">PO</th>
        			<th width="100">Style Ref No.</th>
        			<th width="130">ITEM</th>
					<th width="100">Item Description</th>
					<th width="130"> Images </th>        			
        			<th width="90"> Gmts Size </th>
        			<th width="90">Color </th>
        			<th width="80">Measurement<br>/Count </th>
        			<th width="80">Qty </th>
        			<th width="80">Unit </th>
        			
        		
        			<th>Remarks </th>
        		</tr>
        	</thead>

        	<tbody>
        		<?php 
        			$qnty=0;
        			$amount=0;
					$image_check_array=array();
					$z=1;
        			foreach ($po_wise_data as $job_no => $job_data) 
        			{
        				$job_span=0;
        				foreach ($job_data as $po_number => $po_data) 
        				{
        					$po_span=0;
        					foreach ($po_data as $item_group => $item_data) 
        					{
        						
        					
	        					?>
	        					<tr>
	        						<?php if ($job_span==0): ?>
	        						<td rowspan="<? echo $job_span_arr[$job_no];?>"><?php echo $job_no; $job_span;$job_span++;?></td>
	        							
	        						<?php endif ?>

	        						<?php if ($po_span==0): ?>
	        							<td rowspan="<? echo $po_span_arr[$job_no][$po_number];?>"><?php echo $po_number;$po_span++; ?></td>
	        						
	        							<td rowspan="<? echo $po_span_arr[$job_no][$po_number];?>"><?php echo $style_ref[$job_no]; ?></td>
	        						<?php endif ?>

	        						
	        						<td ><?php echo $trim_group_library[$item_data['trim_group']]; ?></td>
									<td ><?php echo implode(",", array_unique(explode("***", chop($item_data['description'],"***")))); ?></td>
								
									<td>
									<?
									$item_imge_arr=sql_select("select master_tble_id,image_location from   common_photo_library where form_name='pre_cost_trimsv2' and file_type=1 and master_tble_id='".$item_data['pre_cost_fabric_cost_dtls_id']."' ");
									?>
									<p>
								<? 
									//	print_r($item_imge_arr);
			
									foreach($item_imge_arr as $row)
									{
										$po_number_chk=$po_number;
												if (!in_array($po_number_chk,$image_check_array))
													{ $z++;
														$image_check_array[]=$po_number_chk;
														$image_location=$row[csf('image_location')];
														?>
														<img  src='../../<? echo $row[csf('image_location')]; ?>' height='35px' width='110px' />&nbsp;
													<? 
													}
													else
													{
														$image_location='';
													}
										}?>
									&nbsp;
									</td>	        					        						
	        						<td><?php echo implode(",", array_unique(explode("***", chop($item_data['gmts_sizes'],"***")))); ?></td>
	        						<td><?php echo implode(",", array_unique(explode("***", chop($item_data['item_color'],"***")))); ?></td>
	        						<td><?php echo implode(",", array_unique(explode("***", chop($item_data['item_size'],"***")))); ?></td>
	        						<td align="right"><?php echo number_format($item_data['qnty'],4); ?></td>
	        						<td align="center"><?php echo implode(",", array_unique(explode("***", chop($item_data['uom'],"***")))); ?></td>
	        						<td></td>
	        					</tr>
	        					<?
	        					$qnty+=$item_data['qnty'];
	        					$amount+=$item_data['amount'];
	        				}
        				}
        			}

        		 ?>
        	</tbody>
        	<tfoot>
        		<tr>
        			<td colspan="9" align="right">Total</td>
        			<td align="right"><?php echo number_format($qnty,4); ?></td>
        			<td></td>
        			
        		</tr>
        	</tfoot>
        </table>
       
        <?
		}
		
		?>
        <!--==============================================NO NENSITIBITY END=========================================  -->


         <!--==============================================SIZE SENSITIBITY START=========================================  -->
		<?
		$sql_no_sen="
			  SELECT 
			         sum(c.requirment) qnty,
			       	 b.job_no_mst as job_no,
			         b.po_number,
			         a.pre_cost_fabric_cost_dtls_id,
			         a.trim_group,
			        
			         c.item_color,
			         c.item_size,
			         c.gmts_sizes,
			         avg(c.rate) as rate ,
			         sum(c.amount) amount,
			         a.uom,
			         a.description
			     
			         
			    FROM wo_booking_dtls a, wo_po_break_down b,wo_trim_book_con_dtls c
			   WHERE     a.po_break_down_id = b.id
			         and a.id= c.wo_trim_booking_dtls_id
			         and a.booking_no = '$txt_booking_no'
			         and a.status_active = 1
			         and a.is_deleted = 0
			         and c.requirment>0
			         and a.sensitivity = 2
			         and b.status_active=1 
			         and c.status_active=1
			GROUP BY b.job_no_mst,
			         b.po_number,
			         a.pre_cost_fabric_cost_dtls_id,
			         a.trim_group,
			        
			         c.item_color,
			         c.item_size,
			         c.gmts_sizes,
			         a.uom,
			         a.description";
		//echo $sql_no_sen;
		$nameArray_item=sql_select($sql_no_sen);
		if(count($nameArray_item)>0)
		{

			$po_wise_data=array();
			foreach ($nameArray_item as $row)
			{
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_size')]]['trim_group']=$row[csf('trim_group')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_size')]]['item_color'].=$color_library[$row[csf('item_color')]]."***";
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_size')]]['item_size']=$row[csf('item_size')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_size')]]['gmts_sizes'].=$size_library[$row[csf('gmts_sizes')]]."***";
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_size')]]['rate']=$row[csf('rate')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_size')]]['pre_cost_fabric_cost_dtls_id']=$row[csf('pre_cost_fabric_cost_dtls_id')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_size')]]['amount']+=$row[csf('amount')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_size')]]['qnty']+=$row[csf('qnty')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_size')]]['uom'].=$unit_of_measurement[$row[csf('uom')]]."***";
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_size')]]['description'].=$row[csf('description')]."***";
			}
			$job_span_arr=array();
			$po_span_arr=array();
			$item_span_arr=array();
			foreach ($po_wise_data as $job_no => $job_data) 
			{
				$span=0;
				foreach ($job_data as $po_id => $po_data) 
				{
					$po_span=0;
					foreach ($po_data as $item_group => $item_data) 
					{
						$item_span=0;
						foreach ($item_data as $size_id => $size_data) 
						{
							$span++;
							$po_span++;
							$item_span++;

						}
						$item_span_arr[$job_no][$po_id][$item_group]=$item_span;
					}
					$po_span_arr[$job_no][$po_id]=$po_span;
				}
				$job_span_arr[$job_no]=$span;

			}
			

			//$po_quantity[$result_job[csf('id')]];
        ?>
        <table width="1200" style="margin-top: 10px;">
        	<tr>
        		<th align="left" width="300">Size sensitive</th>
        		
        	</tr>
        </table>
        <table  border="1" align="left" class="rpt_table"  cellpadding="0" width="1300" cellspacing="0" rules="all" >
        	<thead>
        		<tr>
        			<th width="120">Job no.</th>
        			<th width="100">PO</th>
        			<th width="100">Style Ref No.</th>
        			<th width="100">ITEM</th>
					<th width="100">Item Description </th>
					<th width="100"> Images</th>
        		
        			<th width="90"> Gmts Size </th>
        			<th width="90">Color </th>
        			<th width="80">Measurement<br>/Count </th>
        			<th width="80">Qty </th>
        			<th width="80">Unit </th>
        			
        			<th>Remarks </th>
        		</tr>
        	</thead>

        	<tbody>
        		<?php 
        			$qnty=0;
        			$amount=0;
					$image_check_array=array();
					$z=1;
        			foreach ($po_wise_data as $job_no => $job_data) 
        			{
        				$job_span=0;
        				foreach ($job_data as $po_number => $po_data) 
        				{
        					$po_span=0;
        					foreach ($po_data as $item_group => $item_data) 
							{
								$item_span=0;
	        					foreach ($item_data as $size_id => $size_data) 
	        					{
	        						
	        					
		        					?>
		        					<tr>
		        						<?php if ($job_span==0): ?>
		        						<td rowspan="<? echo $job_span_arr[$job_no];?>"><?php echo $job_no;$job_span++; ?></td>
		        							
		        						<?php endif ?>
		        						<?php if ($po_span==0): ?>
		        							<td rowspan="<? echo $po_span_arr[$job_no][$po_number];?>"><?php echo $po_number;$po_span++; ?></td>
		        						
			        						<td rowspan="<? echo $po_span_arr[$job_no][$po_number];?>"><?php echo $style_ref[$job_no]; ?></td>
			        					<?php endif ?>

			        					<?php if ($item_span==0): ?>
			        						<td rowspan="<? echo $item_span_arr[$job_no][$po_number][$item_group];?>"><?php echo $trim_group_library[$size_data['trim_group']]; ?></td>
											
			        						<td rowspan="<? echo $item_span_arr[$job_no][$po_number][$item_group];?>"><?php echo implode(",", array_unique(explode("***", chop($size_data['description'],"***"))));$item_span++; ?></td>
			        					<?php endif ?>
			        						
										<td>
										
											<?
												$item_imge_arr=sql_select("select master_tble_id,image_location from   common_photo_library where form_name='pre_cost_trimsv2' and file_type=1 and master_tble_id='".$size_data['pre_cost_fabric_cost_dtls_id']."' ");
												?>
												<p>
											<?	foreach($item_imge_arr as $row)
											{
												$batch_no=$po_number;
												if (!in_array($batch_no,$image_check_array))
													{ $z++;


														$image_check_array[]=$batch_no;
														$image_location=$row[csf('image_location')];
														
														?>
														<img  src='../<? echo $image_location; ?>' height='35px' width='110px' />&nbsp;
													<? 
													}
													else
													{
														$image_location='';
													}

												}
													?>
																					
										</td>
		        					
		        						
		        						<td align="center"><?php echo implode(",", array_unique(explode("***", chop($size_data['gmts_sizes'],"***")))); ?></td>
		        						<td align="center"><?php echo implode(",", array_unique(explode("***", chop($size_data['item_color'],"***")))); ?></td>
		        						<td align="center"><?php echo $size_data['item_size']; ?></td>
		        						<td align="right"><?php echo number_format($size_data['qnty'],4); ?></td>
		        						<td align="center"><?php echo implode(",", array_unique(explode("***", chop($size_data['uom'],"***")))); ?></td>
		        					
		        						<td></td>
		        					</tr>
		        					<?
		        					$qnty+=$size_data['qnty'];
		        					$amount+=$size_data['amount'];
		        				}
		        			}
        				}
        			}

        		 ?>
        	</tbody>
        	<tfoot>
        		<tr>
        			<td colspan="9" align="right">Total</td>
        			<td align="right"><?php echo number_format($qnty,4); ?></td>
        			<td></td>
        		</tr>
        	</tfoot>
        </table>
       
        <?
		}
		
		?>
        <!--==============================================Size Sensitive END=========================================  -->




         <!--==============================================As per Gmts. Color START=========================================  -->
		<?
		$sql_no_sen="
			  SELECT 
			         sum(c.requirment) qnty,
			       	 b.job_no_mst as job_no,
			         b.po_number,
			         a.pre_cost_fabric_cost_dtls_id,
			         a.trim_group,
			        
			         c.item_color,
			         c.item_size,
			         c.gmts_sizes,
			         avg(c.rate) as rate ,
			         sum(c.amount) amount,
			         a.uom,
			         a.description
			     
			         
			    FROM wo_booking_dtls a, wo_po_break_down b,wo_trim_book_con_dtls c
			   WHERE     a.po_break_down_id = b.id
			         and a.id= c.wo_trim_booking_dtls_id
			         and a.booking_no = '$txt_booking_no'
			         and a.status_active = 1
			         and a.is_deleted = 0
			         and c.requirment>0
			         and a.sensitivity = 1
			         and b.status_active=1 
			         and c.status_active=1
			GROUP BY b.job_no_mst,
			         b.po_number,
			         a.pre_cost_fabric_cost_dtls_id,
			         a.trim_group,
			        	
			         c.item_color,
			         c.item_size,
			         c.gmts_sizes,
			         a.uom,
			         a.description";
		$nameArray_item=sql_select($sql_no_sen);
		if(count($nameArray_item)>0)
		{

			$po_wise_data=array();
			foreach ($nameArray_item as $row)
			{
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]]['trim_group']=$row[csf('trim_group')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]]['item_color']=$row[csf('item_color')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]]['item_size'].=$row[csf('item_size')]."***";
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]]['gmts_sizes'].=$size_library[$row[csf('gmts_sizes')]]."***";
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]]['rate']=$row[csf('rate')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]]['pre_cost_fabric_cost_dtls_id']=$row[csf('pre_cost_fabric_cost_dtls_id')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]]['amount']+=$row[csf('amount')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]]['qnty']+=$row[csf('qnty')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]]['uom'].=$unit_of_measurement[$row[csf('uom')]]."***";
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]]['description'].=$row[csf('description')]."***";
			}
			$job_span_arr=array();
			$po_span_arr=array();
			$item_span_arr=array();
			foreach ($po_wise_data as $job_no => $job_data) 
			{
				$span=0;
				foreach ($job_data as $po_id => $po_data) 
				{
					$po_span=0;
					foreach ($po_data as $item_group => $item_data) 
					{
						$item_span=0;
						foreach ($item_data as $color_id => $color_data) 
						{
							
							
							$span++;
							$po_span++;
							$item_span++;

							
						}
						$item_span_arr[$job_no][$po_id][$item_group]=$item_span;
					}
					$po_span_arr[$job_no][$po_id]=$po_span;
				}
				$job_span_arr[$job_no]=$span;

			}
			

			//$po_quantity[$result_job[csf('id')]];
        ?>
        <table width="1200" style="margin-top: 10px;">
        	<tr>
        		<th align="left" width="300">As Per Garments Color</th>
        		
        	</tr>
        </table>
        <table  border="1" align="left" class="rpt_table"  cellpadding="0" width="1300" cellspacing="0" rules="all" >
        	<thead>
        		<tr>
        			<th width="120">Job no.</th>
        			<th width="100">PO</th>
        			<th width="100">Style Ref No.</th>
        			<th width="100">ITEM</th>
					<th width="100">Item Description</th>
					<th width="100">Images</th>        		
        			<th width="90"> Gmts Size </th>
        			<th width="90">Color </th>
        			<th width="80">Measurement<br>/Count </th>
        			<th width="80">Qty </th>
        			<th width="80">Unit </th>
        			<th>Remarks </th>
        		</tr>
        	</thead>

        	<tbody>
        		<?php 
        			$qnty=0;
        			$amount=0;
					$image_check_array=array();
					$z=1;
        			foreach ($po_wise_data as $job_no => $job_data) 
        			{
        				$job_span=0;
        				foreach ($job_data as $po_number => $po_data) 
        				{
        					$po_span=0;
        					foreach ($po_data as $item_group => $item_data) 
							{
								$item_span=0;
	        					foreach ($item_data as $color_id => $color_data) 
	        					{
	        						
		        					?>
		        					<tr>
		        						<?php if ($job_span==0): ?>
		        						<td rowspan="<? echo $job_span_arr[$job_no];?>"><?php echo $job_no; $job_span++;?></td>
		        							
		        						<?php endif ?>

		        						<?php if ($po_span==0): ?>
			        						<td rowspan="<? echo $po_span_arr[$job_no][$po_number];?>"><?php echo $po_number; ?></td>
			        						<td rowspan="<? echo $po_span_arr[$job_no][$po_number];?>"><?php echo $style_ref[$job_no];$po_span++; ?></td>
			        					<?php endif ?>
			        					<?php if ($item_span==0): ?>
			        						<td rowspan="<? echo $item_span_arr[$job_no][$po_number][$item_group];?>"><?php echo $trim_group_library[$color_data['trim_group']]; ?></td>
			        						<td rowspan="<? echo $item_span_arr[$job_no][$po_number][$item_group];?>"><?php echo implode(",", array_unique(explode("***", chop($color_data['description'],"***"))));$item_span++; ?></td>
			        					<?php endif ?>
		        						<td>
										
										
											<?
												$item_imge_arr=sql_select("select master_tble_id,image_location from   common_photo_library where form_name='pre_cost_trimsv2' and file_type=1 and master_tble_id='".$color_data['pre_cost_fabric_cost_dtls_id']."' ");
												?>
												<p>
											<?	foreach($item_imge_arr as $row)
											{
												$batch_no=$po_number;
												if (!in_array($batch_no,$image_check_array))
													{ $z++;
														$image_check_array[]=$batch_no;
														$image_location=$row[csf('image_location')];
														?>
														<img  src='../<? echo $image_location; ?>' height='35px' width='110px' />&nbsp;
													<? 
													}
													else
													{
														$image_location='';
													}


												}
                      						 ?>
										
										</td>
		        						
		        						<td align="center"><?php echo implode(",", array_unique(explode("***", chop($color_data['gmts_sizes'],"***")))); ?></td>
		        						<td align="center"><?php echo $color_library[$color_data['item_color']]; ?></td>
		        						<td align="center"><?php echo implode(",", array_unique(explode("***", chop($color_data['item_size'],"***")))); ?></td>
		        						<td align="right"><?php echo number_format($color_data['qnty'],4); ?></td>
		        						<td align="center"><?php echo implode(",", array_unique(explode("***", chop($color_data['uom'],"***")))); ?></td>	  		
		        						<td></td>
		        					</tr>
		        					<?
		        					$qnty+=$color_data['qnty'];
		        					$amount+=$color_data['amount'];
		        				}
		        			}
        				}
        			}

        		 ?>
        	</tbody>
        	<tfoot>
        		<tr>
        			<td colspan="9" align="right">Total</td>
        			<td align="right"><?php echo number_format($qnty,4); ?></td>
        			<td></td>
        		
        			
        		</tr>
        	</tfoot>
        </table>
       
        <?
		}
		
		?>
        <!--==============================================As Per Garments Color End=========================================  -->




         <!--==============================================Color And Size Sensitive Start=========================================  -->
		<?
		$sql_no_sen="
			  SELECT 
			         sum(d.requirment) qnty,
			       	 b.job_no_mst as job_no,
			         b.po_number,
			         a.pre_cost_fabric_cost_dtls_id,
			         a.trim_group,
			        
			         d.item_color,
			         d.item_size,
			         d.gmts_sizes,
			         avg(d.rate) as rate ,
			         sum(d.amount) amount,
			         a.uom,
			         a.description,
			         d.color_number_id
			     
			         
			    FROM wo_booking_dtls a, wo_po_break_down b ,wo_trim_book_con_dtls d
			   WHERE     a.po_break_down_id = b.id
			          and a.id = d.wo_trim_booking_dtls_id
         			 and b.job_no_mst=d.job_no
			        
			         and a.booking_no = '$txt_booking_no'
			         and a.status_active = 1
			         and a.is_deleted = 0
			         and d.requirment>0
			        
			         and a.sensitivity = 4
			         and b.status_active=1 
			         and d.status_active=1
			       
			GROUP BY b.job_no_mst,
			         b.po_number,
			         a.pre_cost_fabric_cost_dtls_id,
			         a.trim_group,
			        
			         d.item_color,
			         d.item_size,
			         d.gmts_sizes,
			         a.uom,
			         a.description,
			         d.color_number_id";
		//echo $sql_no_sen;
		$nameArray_item=sql_select($sql_no_sen);
		if(count($nameArray_item)>0)
		{

			$po_wise_data=array();
			foreach ($nameArray_item as $row)
			{
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('item_size')]][$row[csf('gmts_sizes')]]['trim_group']=$row[csf('trim_group')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('item_size')]][$row[csf('gmts_sizes')]]['item_color']=$row[csf('item_color')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('item_size')]][$row[csf('gmts_sizes')]]['item_size']=$row[csf('item_size')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('item_size')]][$row[csf('gmts_sizes')]]['pre_cost_fabric_cost_dtls_id']=$row[csf('pre_cost_fabric_cost_dtls_id')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('item_size')]][$row[csf('gmts_sizes')]]['gmts_sizes']=$size_library[$row[csf('gmts_sizes')]];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('item_size')]][$row[csf('gmts_sizes')]]['gmts_color'].=$color_library[$row[csf('color_number_id')]]."***";
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('item_size')]][$row[csf('gmts_sizes')]]['rate']=$row[csf('rate')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('item_size')]][$row[csf('gmts_sizes')]]['amount']+=$row[csf('amount')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('item_size')]][$row[csf('gmts_sizes')]]['qnty']+=$row[csf('qnty')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('item_size')]][$row[csf('gmts_sizes')]]['uom'].=$unit_of_measurement[$row[csf('uom')]]."***";
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('item_color')]][$row[csf('item_size')]][$row[csf('gmts_sizes')]]['description'].=$row[csf('description')]."***";
				
			}
			$job_span_arr=array();
			$po_span_arr=array();
			$item_span_arr=array();
			foreach ($po_wise_data as $job_no => $job_data) 
			{
				$span=0;
				foreach ($job_data as $po_id => $po_data) 
				{
					$po_span=0;
					foreach ($po_data as $item_group => $item_data) 
					{
						$item_span=0;
						foreach ($item_data as $color_id => $color_data) 
						{
							
							foreach ($color_data as $size_id => $size_data_arr) {
								foreach ($size_data_arr as $sizeid => $size_data) {
								$span++;
								$po_span++;
								$item_span++;
								}
							}
						}
						$item_span_arr[$job_no][$po_id][$item_group]=$item_span;
					}
					$po_span_arr[$job_no][$po_id]=$po_span;
				}
				$job_span_arr[$job_no]=$span;

			}
			

			//$po_quantity[$result_job[csf('id')]];
        ?>
        <table width="1200" style="margin-top: 10px;">
        	<tr>
        		<th align="left" width="300">Color And Size Sensitive</th>
        		<th >Item Size</th>
        	</tr>
        </table>
        <table  border="1" align="left" class="rpt_table"  cellpadding="0" width="1300" cellspacing="0" rules="all" >
        	<thead>
        		<tr>
        			<th width="120">Job no.</th>
        			<th width="100">PO</th>
        			<th width="100">Style Ref No.</th>
        			<th width="100">ITEM</th>
					<th width="100">Item Description</th>
					<th width="100">Images</th>        		
        			<th width="90"> Gmts Color </th>
        			<th width="90"> Gmts Size </th>
        			<th width="90">Item Color </th>
        			<th width="80">Measurement<br>/Count </th>
        			<th width="80">Qty </th>
        			<th width="80">Unit </th>        		
        			<th>Remarks </th>
        		</tr>
        	</thead>

        	<tbody>
        		<?php 
        			$qnty=0;
        			$amount=0;
					$image_check_array=array();
					$z=1;
        			foreach ($po_wise_data as $job_no => $job_data) 
        			{
        				$job_span=0;
        				foreach ($job_data as $po_number => $po_data) 
        				{
        					$po_span=0;
        					
        					foreach ($po_data as $item_group => $item_data) 
        					{
        						$item_span=0;
        						foreach ($item_data as $color_id => $color_data) 
								{
									
									foreach ($color_data as $size_id => $size_data_arr) 
									{
										
									   foreach($size_data_arr as $sizeid => $size_data){

										
								
			        					?>
			        					<tr>
			        						<?php if ($job_span==0): ?>
			        						<td rowspan="<? echo $job_span_arr[$job_no];?>"><?php echo $job_no; $job_span++;?></td>
			        							
			        						<?php endif ?>

			        						<?php if ($po_span==0): ?>
			        								
			        							<td rowspan="<? echo $po_span_arr[$job_no][$po_number];?>"><?php echo $po_number;$po_span++; ?></td>
				        						<td rowspan="<? echo $po_span_arr[$job_no][$po_number];?>"><?php echo $style_ref[$job_no]; ?></td>
				        					<?php endif ?>
				        					<?php if ($item_span==0): ?>
				        						<td rowspan="<? echo $item_span_arr[$job_no][$po_number][$item_group];?>"><?php echo $trim_group_library[$size_data['trim_group']];$item_span++; ?></td>
				        						<td rowspan="<? echo $item_span_arr[$job_no][$po_number][$item_group];?>"><?php echo implode(",", array_unique(explode("***", chop($size_data['description'],"***")))); ?></td>
			        						<?php endif ?>
										
			        						<td>
											<?
												$item_imge_arr=sql_select("select master_tble_id,image_location from   common_photo_library where form_name='pre_cost_trimsv2' and file_type=1 and master_tble_id='".$size_data['pre_cost_fabric_cost_dtls_id']."' ");
												?>
												<p>
											<?	foreach($item_imge_arr as $row)
											{
												$batch_no=$po_number;
												if (!in_array($batch_no,$image_check_array))
													{ $z++;


														$image_check_array[]=$batch_no;
														$image_location=$row[csf('image_location')];
														
														?>
														<img  src='../<? echo $image_location; ?>' height='35px' width='110px' />&nbsp;
													<? 
													}
													else
													{
														$image_location='';
													}

												}
                      						 ?>
										
											
											
											</td>
											
			        						<td><?php echo implode(",", array_unique(explode("***", chop($size_data['gmts_color'],"***")))); ?></td>
			        						<td align="center"><?php echo $size_data['gmts_sizes']; ?></td>
			        						<td align="center"><?php echo $color_library[$size_data['item_color']]; ?></td>
			        						<td align="center"><?php echo $size_data['item_size']; ?></td>
			        						<td align="right"><?php echo number_format($size_data['qnty'],4); ?></td>
			        						<td align="center"><?php echo implode(",", array_unique(explode("***", chop($size_data['uom'],"***")))); ?></td>
			        						
			        						<td></td>
			        					</tr>
			        					<?
			        					$qnty+=$size_data['qnty'];
			        					$amount+=$size_data['amount'];
										}
			        				}
			        			}
        					}
        				}
        			}

        		 ?>
        	</tbody>
        	<tfoot>
        		<tr>
        			<td colspan="10" align="right">Total</td>
        			<td align="right"><?php echo number_format($qnty,4); ?></td>
        			<td></td>
        		
        			
        		</tr>
        	</tfoot>
        </table>
       
        <?
		}
		
		?>
        <!--==============================================Color And Size Sensitive End=========================================  -->



         <!--==============================================Contrast Color Start=========================================  -->

		 
		<?
		$sql_no_sen="
			  SELECT 
			         sum(c.requirment) qnty,
			       	 b.job_no_mst as job_no,
			         b.po_number,
			         a.pre_cost_fabric_cost_dtls_id,
			         a.trim_group,
			        
			         c.item_color,
			         c.item_size,
			         c.gmts_sizes,
					 c.color_number_id,
			         avg(c.rate) as rate ,
			         sum(c.amount) amount,
			         a.uom,
			         a.description
			     
			         
			    FROM wo_booking_dtls a, wo_po_break_down b,wo_trim_book_con_dtls c
			   WHERE     a.po_break_down_id = b.id
			         and a.id= c.wo_trim_booking_dtls_id
			         and a.booking_no = '$txt_booking_no'
			         and a.status_active = 1
			         and a.is_deleted = 0
			         and c.requirment>0
			         and a.sensitivity = 3
			         and b.status_active=1 
			         and c.status_active=1
			GROUP BY b.job_no_mst,
			         b.po_number,
			         a.pre_cost_fabric_cost_dtls_id,
			         a.trim_group,
			        
			         c.item_color,
			         c.item_size,
			         c.gmts_sizes,
					 c.color_number_id,
			         a.uom,
			         a.description";
		$nameArray_item=sql_select($sql_no_sen);
		if(count($nameArray_item)>0)
		{

			$po_wise_data=array();
			foreach ($nameArray_item as $row)
			{
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('color_number_id')]]['trim_group']=$row[csf('trim_group')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('color_number_id')]]['item_color']=$row[csf('item_color')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('color_number_id')]]['item_size'].=$row[csf('item_size')]."***";
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('color_number_id')]]['gmts_sizes'].=$size_library[$row[csf('gmts_sizes')]]."***";
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('color_number_id')]]['rate']=$row[csf('rate')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('color_number_id')]]['color_number_id']=$row[csf('color_number_id')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('color_number_id')]]['amount']+=$row[csf('amount')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('color_number_id')]]['qnty']+=$row[csf('qnty')];
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('color_number_id')]]['uom'].=$unit_of_measurement[$row[csf('uom')]]."***";
				$po_wise_data[$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('trim_group')]][$row[csf('color_number_id')]]['description'].=$row[csf('description')]."***";
			}
			$job_span_arr=array();
			$po_span_arr=array();
			$item_span_arr=array();
			foreach ($po_wise_data as $job_no => $job_data) 
			{
				$span=0;
				foreach ($job_data as $po_id => $po_data) 
				{
					$po_span=0;
					foreach ($po_data as $item_group => $item_data) 
					{
						$item_span=0;
						foreach ($item_data as $color_id => $color_data) 
						{
							
							
							$span++;
							$po_span++;
							$item_span++;

							
						}
						$item_span_arr[$job_no][$po_id][$item_group]=$item_span;
					}
					$po_span_arr[$job_no][$po_id]=$po_span;
				}
				$job_span_arr[$job_no]=$span;

			}
			

			//$po_quantity[$result_job[csf('id')]];
        ?>
        <table width="1200" style="margin-top: 10px;">
        	<tr>
        		<th align="left" width="300">Contrast Color</th>
        		
        	</tr>
        </table>
        <table  border="1" align="left" class="rpt_table"  cellpadding="0" width="1300" cellspacing="0" rules="all" >
        	<thead>
        		<tr>
        			<th width="120">Job no.</th>
        			<th width="100">PO</th>
        			<th width="100">Style Ref No.</th>
        			<th width="100">ITEM</th>
					
        			<th width="100">Item Description</th>
					<th width="90"> Gmts Color </th>
        			<th width="90"> Gmts Size </th>
        			<th width="90">Color </th>
        			<th width="80">Measurement<br>/Count </th>
        			<th width="80">Qty </th>
        			<th width="80">Unit </th>
        			<th>Remarks </th>
        		</tr>
        	</thead>

        	<tbody>
        		<?php 
        			$qnty=0;
        			$amount=0;
        			foreach ($po_wise_data as $job_no => $job_data) 
        			{
        				$job_span=0;
        				foreach ($job_data as $po_number => $po_data) 
        				{
        					$po_span=0;
        					foreach ($po_data as $item_group => $item_data) 
							{
								$item_span=0;
	        					foreach ($item_data as $color_id => $color_data) 
	        					{
	        						
		        					?>
		        					<tr>
		        						<?php if ($job_span==0): ?>
		        						<td rowspan="<? echo $job_span_arr[$job_no];?>"><?php echo $job_no; $job_span++;?></td>
		        							
		        						<?php endif ?>
										
		        						<?php if ($po_span==0): ?>
			        						<td rowspan="<? echo $po_span_arr[$job_no][$po_number];?>"><?php echo $po_number; ?></td>
			        						<td rowspan="<? echo $po_span_arr[$job_no][$po_number];?>"><?php echo $style_ref[$job_no];$po_span++; ?></td>
			        					<?php endif ?>
										
			        					<?php if ($item_span==0): ?>
			        						<td rowspan="<? echo $item_span_arr[$job_no][$po_number][$item_group];?>"><?php echo $trim_group_library[$color_data['trim_group']]; ?></td>
											
			        						<td rowspan="<? echo $item_span_arr[$job_no][$po_number][$item_group];?>"><?php echo implode(",", array_unique(explode("***", chop($color_data['description'],"***"))));$item_span++; ?></td>
			        					<?php endif ?>
		        					
										<td><?=$color_library[$color_data['color_number_id']];?></td>
		        						<td align="center"><?php echo implode(",", array_unique(explode("***", chop($color_data['gmts_sizes'],"***")))); ?></td>
		        						<td align="center"><?php echo $color_library[$color_data['item_color']]; ?></td>
		        						<td align="center"><?php echo implode(",", array_unique(explode("***", chop($color_data['item_size'],"***")))); ?></td>
		        						<td align="right"><?php echo number_format($color_data['qnty'],4); ?></td>
		        						<td align="center"><?php echo implode(",", array_unique(explode("***", chop($color_data['uom'],"***")))); ?></td>
		        					
		        						<td></td>
		        					</tr>
		        					<?
		        					$qnty+=$color_data['qnty'];
		        					$amount+=$color_data['amount'];
		        				}
		        			}
        				}
        			}

        		 ?>
        	</tbody>
        	<tfoot>
        		<tr>
        			<td colspan="10" align="right">Total</td>
        			<td align="right"><?php echo number_format($qnty,4); ?></td>
        			<td></td>
        			<td></td>
        		
        		</tr>
        	</tfoot>
        </table>
       
        <?
		}
		
		?>
      


        <!--==============================================Contrast Color End=========================================  -->



    <!--==============================================Summery START=========================================  -->
	<?
	
		$sql_no_sen="
			  SELECT 
			         sum(c.requirment) qnty,
			       	 b.job_no_mst as job_no,
			         b.po_number,
			         a.pre_cost_fabric_cost_dtls_id,
			         a.trim_group,
			        
			         c.item_color,
			         c.item_size,
			         c.gmts_sizes,
			         avg(c.rate) as rate ,
			         sum(c.amount) amount,
			         a.uom,
			         a.description
			     
			         
			    FROM wo_booking_dtls a, wo_po_break_down b,wo_trim_book_con_dtls c
			   WHERE     a.po_break_down_id = b.id
			         and a.id= c.wo_trim_booking_dtls_id
			         and a.booking_no = '$txt_booking_no'
			         and a.status_active = 1
			         and a.is_deleted = 0
			         and c.requirment>0
			         and a.sensitivity in (0,1,2,3,4)
			         and b.status_active=1 
			         and c.status_active=1
			GROUP BY b.job_no_mst,
			         b.po_number,
			         a.pre_cost_fabric_cost_dtls_id,
			         a.trim_group,
			        
			         c.item_color,
			         c.item_size,
			         c.gmts_sizes,
			         a.uom,
			         a.description";
		//echo $sql_no_sen;
		$nameArray_item=sql_select($sql_no_sen);
		$summery_arr='';
		
		if(count($nameArray_item)>0)
		{

			$po_wise_data=array();
			$old_job=0;
			foreach ($nameArray_item as $row)
			{
				$summery_arr_style .=$style_ref[$row[csf('job_no')]]."***";
				$summery_arr_trim_group .=$trim_group_library[$row[csf('trim_group')]]."***";
				$po_wise_data[$row[csf('item_size')]]['trim_group']=$row[csf('trim_group')];
				$po_wise_data[$row[csf('item_size')]]['item_color'].=$color_library[$row[csf('item_color')]]."***";
				$po_wise_data[$row[csf('item_size')]]['item_size']=$row[csf('item_size')];
				$po_wise_data[$row[csf('item_size')]]['gmts_sizes'].=$size_library[$row[csf('gmts_sizes')]]."***";
				$po_wise_data[$row[csf('item_size')]]['rate']=$row[csf('rate')];
				$po_wise_data[$row[csf('item_size')]]['amount']+=$row[csf('amount')];
				$po_wise_data[$row[csf('item_size')]]['qnty']+=$row[csf('qnty')];
				$po_wise_data[$row[csf('item_size')]]['uom'].=$unit_of_measurement[$row[csf('uom')]]."***";
				$po_wise_data[$row[csf('item_size')]]['description'].=$row[csf('description')]."***";
				
			}
        ?>
		<br>
        <!-- <table width="1200" style="margin-top: 10px;">
        	<tr>
        		<th align="left" width="300">Total Summery</th>
        		
        	</tr>
        </table> -->
        <!-- <table  border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
        	<thead>
        		<tr>
        			<th width="100" align="center">SL.</th>
        			<th width="100" align="center">Style Ref No.</th>
					<th width="100" align="center"> Items</th>
					<th width="200" align="center"> Images</th>
        			<th width="100" align="center">Color</th>
					<?
					$i=1;
					foreach ($po_wise_data as $size_id => $size_data) 
	        					{
 						$size=array_unique(explode("***", chop($size_data['gmts_sizes'],"***")));
						
						 foreach($size as $row){
							$i++;
							
						 }
						 
						 
						 }?>
        			 <th width="80" colspan="<?=$i-1;?>">SIZE  GRADING </th>
        			<th width="80">  </th>
        			<th width="80"> </th>
        			
        			
        		</tr>
        	</thead>
			
        	<tbody>
						<tr>	
							<td rowspan="3">1</td>			        				
							<td rowspan="3"><?echo implode(",", array_unique(explode("***", chop($summery_arr_style,"***"))));?></td>									
							<td rowspan="3"><?echo implode(",", array_unique(explode("***", chop($summery_arr_trim_group,"***"))));?></td>     				
							<td rowspan="3"></td>
							<td rowspan="3"></td>		
							<?
							foreach ($po_wise_data as $size_id => $size_data){
								$size=array_unique(explode("***", chop($size_data['gmts_sizes'],"***")));
								
								foreach($size as $row){	?>
									<td align="center"><?php echo $row; ?></td>
								<?}								
								}?>
							<td align="right"></td>
							<td align="center"></td>
					</tr>
					<tr>		        
						<?$qnty=0;
						foreach ($po_wise_data as $size_id => $size_data) 
							{
							$size=array_unique(explode("***", chop($size_data['gmts_sizes'],"***")));
								?>
						<td align="center" colspan="<?= count($size);?>"><?php echo $size_data['item_size']; ?></td>
									<?	$qnty+=$size_data['qnty'];
									
									}?>
						<td align="right"></td>
						<td align="center"></td>
					</tr>

					<tr>		        	
					<?foreach ($po_wise_data as $size_id => $size_data) 
						{
						$size=array_unique(explode("***", chop($size_data['gmts_sizes'],"***")));
							?>
						<td align="center" colspan="<?= count($size);?>"><?php echo $size_data['qnty']; ?></td>
						<?
						}?>
						<td align="right"><?php //echo number_format($qnty,4); ?></td>
						<td align="center"></td>
		           </tr> 
        	</tbody>
        </table> -->
        <?
		}
		?>
        <!--==============================================Summery END=========================================  -->
         <br>
            <table style="width: 1300px;">
                <tr>
                <td width="40%">
                <?
                    echo get_spacial_instruction($txt_booking_no);
                ?>
                </td>
                <td width="20%"></td>
           
                <td width="40%" valign="top">
                    <table  width="100%" >

                        <tr align="left">
						<td><strong>Shipping Mark as below.</strong></td>
						</tr>
						<tr align="left">
							<td>Company Name : <? echo $company_library[$cbo_company_name]; ?></td>
						</tr>
						<tr align="left">
							<td>Address : <? echo $address; ?></td>
						</tr>
						<tr align="left">
							<td>Item Details : </td>
						</tr>
						<tr align="left">
							<td>Color : </td>
						</tr>
						<tr align="left">
							<td>Roll No : ____ of ____</td>
						</tr>
						<tr align="left">
							<td>Quantity : </td>
						</tr>
						<tr align="left">
							<td>Net Weight : </td>
						</tr>
						<tr align="left">
							<td>Gross Weight : </td>
							
						</tr>

                    </table>
            </td>
            </tr>
        </table>

   	 </tbody>
     </table>
     <br>
	<h4>Note: Please mention all above description, terms and conditions in the PI</h4>
    <div  style="margin-top:-50px;">
         <?
          echo signature_table(132, $cbo_company_name, "1300px",1);
		 ?>
   </div>
   
<? 
	$emailBody=ob_get_contents();
	//ob_clean();
	if($is_mail_send==1){
		$req_approved=return_field_value("id", "ELECTRONIC_APPROVAL_SETUP", "PAGE_ID = 336 and is_deleted=0");
		$is_approved=return_field_value("IS_APPROVED", "WO_BOOKING_MST", "STATUS_ACTIVE = 1 and is_deleted=0 and booking_no='$txt_booking_no'");
		if($req_approved && $is_approved==1){
			$emailBody.="<h1 style='border:1px sloid #0ff;'>Approved</h1>";
		}
		elseif($req_approved && $is_approved==0){
			$emailBody.="<h1 style='border:1px sloid #0ff;'>Draft</h1>";
		}
		
	
		$mailArr=array();
		if($mail_id!=''){$mailArr[]=$mail_id;}
		if($supplier_mail_arr[$supplier_id]!=''){$mailArr[]=$supplier_mail_arr[$supplier_id];}
		
		
		$to=implode(',',$mailArr);
		$subject="Trims Booking Auto Mail";
		
		if($to!=""){
			require '../../../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
			require_once('../../../auto_mail/setting/mail_setting.php');
			$header=mailHeader();
			sendMailMailer( $to, $subject, $emailBody );
		}
	}
?>
   
   
      <div id="page_break_div">
   	 </div>
    <div>

		<?
        	//echo signature_table(2, $cbo_company_name, "1330px");
			//echo "****".custom_file_name($txt_booking_no,$style_sting,$job_no);
        ?>
    </div>

  </html>
	<?
	$user_id=$_SESSION['logic_erp']['user_id'];
	$report_cat=100;
	$html = ob_get_contents();
		ob_clean();
		//$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
		foreach (glob("tb*.xls") as $filename) {
		//if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename="tb".$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc, $html);
		echo "****$html****$report_cat";
	//exit();
}

if($action=="show_trim_booking_report4") //Zakaria joy
{
	extract($_REQUEST);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$id_approved_id=str_replace("'","",$id_approved_id);
	$report_type=str_replace("'","",$report_type);
	$show_comment=str_replace("'","",$show_comment);
	$cbo_level=str_replace("'","",$cbo_level);
	$level_arr = array(1=>"PO Level",2=>"Job Level");
	$color_library=return_library_array("select id, color_name from lib_color", "id", "color_name");
	$size_library=return_library_array("select id, size_name from  lib_size", "id", "size_name");
	$company_library=return_library_array("select id, company_name from lib_company", "id", "company_name");
	$trim_group_library= return_library_array("select id, item_name from lib_item_group",'id','item_name');
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$marchentr_email = return_library_array("select id,team_member_email from lib_mkt_team_member_info ","id","team_member_email");
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$pro_sub_dept_array=return_library_array( "select id,sub_department_name from lib_pro_sub_deparatment",'id','sub_department_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$order_uom_arr=return_library_array("select id,order_uom  from lib_item_group","id","order_uom");
	$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$cbo_company_name'","image_location");
	$address = "";
	$add_info = "";
	$nameArray=sql_select("select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id=$cbo_company_name");
	if($nameArray[0][csf('plot_no')] != ''){
		$address.=$nameArray[0][csf('plot_no')].',';
	}
	if($nameArray[0][csf('level_no')] != ''){
		$address.=$nameArray[0][csf('level_no')].',';
	}
	if($nameArray[0][csf('road_no')] != ''){
		$address.=$nameArray[0][csf('road_no')].',';
	}
	if($nameArray[0][csf('block_no')] != ''){
		$address.=$nameArray[0][csf('block_no')].'<br>';
	}
	if($nameArray[0][csf('city')] != ''){
		$address.=$nameArray[0][csf('city')].',';
	}
	if($nameArray[0][csf('zip_code')] != 0 && $nameArray[0][csf('zip_code')] != ''){
		$address.='-'.$nameArray[0][csf('zip_code')].',';
	}
	if($nameArray[0][csf('province')] != ''){
		$address.=$nameArray[0][csf('province')].','.$country_arr[$nameArray[0][csf('country_id')]];
	}
	if($nameArray[0][csf('email')] != ''){
		$add_info.='Email: '.$nameArray[0][csf('email')];
	}
	if($nameArray[0][csf('website')] != ''){
		$add_info.=', Website: '.$nameArray[0][csf('website')];
	}
	/* Second Table Data */
	$po_booking_info=sql_select( "SELECT  a.style_ref_no,a.style_description, a.job_no, a.style_owner, a.buyer_name, a.client_id, a.dealing_marchant, a.season, a.season_matrix, a.total_set_qnty, a.product_dept, a.product_code, a.pro_sub_dep, a.gmts_item_id, a.order_repeat_no, a.qlty_label, c.internal_ref from wo_po_details_master a join wo_booking_dtls b on a.job_no=b.job_no left join wo_order_entry_internal_ref c on a.job_no=c.job_no where b.booking_no='$txt_booking_no' and b.status_active =1 and b.is_deleted=0");
	$job_data_arr=array();
	foreach ($po_booking_info as $result_buy){
		$job_data_attr = array('job_no','total_set_qnty','product_dept','product_code','pro_sub_dep','gmts_item_id','style_ref_no','style_description','dealing_marchant','season_matrix','order_repeat_no','qlty_label','client_id','internal_ref');
		foreach ($job_data_attr as $attr) {
			$job_data_arr[$result_buy[csf('job_no')]][$attr] = $result_buy[csf($attr)];
			$job_data_header_arr[$attr][] = $result_buy[csf($attr)];
			if($attr == 'product_dept'){
				$job_data_arr[$result_buy[csf('job_no')]][$attr]=$product_dept[$result_buy[csf($attr)]];
			}
			if($attr == 'pro_sub_dep'){
				$job_data_arr[$result_buy[csf('job_no')]][$attr]=$pro_sub_dept_array[$result_buy[csf($attr)]];
			}
			if($attr == 'dealing_marchant'){
				$job_data_arr[$attr][$result_buy[csf('job_no')]]=$marchentrArr[$result_buy[csf($attr)]];
			}
			if($attr == 'season_matrix'){
				$job_data_arr[$result_buy[csf('job_no')]][$attr]=$season_arr[$result_buy[csf($attr)]];
			}
			if($attr == 'qlty_label'){
				$job_data_arr[$result_buy[csf('job_no')]][$attr]=$quality_label[$result_buy[csf($attr)]];
			}
		}
		$job_data_arr['job_no_in'][$result_buy[csf('job_no')]]="'".$result_buy[csf('job_no')]."'";
		$job_data_arr['dealing_marchant_email'][$result_buy[csf('job_no')]]=$marchentr_email[$result_buy[csf('dealing_marchant')]];
	}
	/*echo '<pre>';
	print_r($job_data_arr); die;*/
	$job_no= implode(", ",array_unique($job_data_arr['job_no']));
	$job_no_in= implode(", ",array_unique($job_data_arr['job_no_in']));
	$product_depertment=implode(", ",array_unique($job_data_arr['product_dept']));
	$product_code=implode(", ",array_unique($job_data_arr['product_code']));
	$pro_sub_dep=implode(", ",array_unique($job_data_arr['pro_sub_dep']));
	//$gmts_item_id=implode(", ",array_unique($job_data_arr['gmts_item_id']));
	$gmts_item_id=implode(", ",array_unique($job_data_header_arr['gmts_item_id']));
	$grouping=implode(", ",array_unique($job_data_header_arr['internal_ref']));
	$style_sting=implode(", ",array_unique($job_data_arr['style_ref_no']));
	$style_description=implode(", ",array_unique($job_data_arr['style_description']));
	$dealing_marchant=implode(", ",array_unique($job_data_arr['dealing_marchant']));
	$dealing_marchant_email=implode(", ",array_unique($job_data_arr['dealing_marchant_email']));
	$season_matrix=implode(", ",array_unique($job_data_arr['season_matrix']));
	$order_repeat_no= implode(",",array_unique($job_data_arr['order_repeat_no']));
	$qlty_label= implode(", ",array_unique($job_data_arr['qlty_label']));
	$client_id= implode(", ",array_unique($job_data_arr['client_id']));

	//echo $job_no; die;

	$booking_master_info=sql_select( "select a.buyer_id, a.booking_no, a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.po_break_down_id, a.colar_excess_percent, a.cuff_excess_percent, a.delivery_date, a.is_apply_last_update, a.fabric_source, a.rmg_process_breakdown, a.insert_date, a.update_date, a.uom, a.remarks, a.pay_mode, a.fabric_composition,a.source,a.delivery_address from wo_booking_mst a where a.booking_no='$txt_booking_no' and a.status_active =1 and a.is_deleted=0 ");
	$booking_grand_total=0;
	$currency_id="";
	ob_start();

	?>
	<html>
	<head>
	</head>

	<table style="border:1px solid black; table-layout: fixed; " width="100%">
		<tr>
			<td rowspan="5" width="20%"><?php if ($image_location != '') {?> <img src="../../<? echo $image_location; ?>" height="70" width="100"></td><?php }?>
		</tr>
		<tr>
			<td style="font-size:20px; text-align: center;" width="60%"><strong><? echo $company_library[$cbo_company_name]; ?></strong></td>
		</tr>
		<tr>
			<td style="font-size:16px; text-align: center;"><?php echo $address; ?>	</td>		
			<td rowspan="4" style="text-align: right; font-size:16px; font-weight: bold;" width="20%">Booking No. :<? echo $booking_master_info[0][csf('booking_no')];?><? echo "(".$fabric_source[$booking_master_info[0][csf('fabric_source')]].")"?>
			</td>
		</tr>
		<tr>
			<td style="font-size:16px; text-align: center;"><?php echo $add_info ?></td>
		</tr>
		<tr>
			<td style="text-align: center; font-size:16px; text-decoration: underline; font-weight: bold;">Main Trims Purchase Order</td>
		</tr>
	</table>
	<?
	foreach ($booking_master_info as $result) {
		$currency_id=$result[csf('currency_id')];
		$booking_date=$result[csf('update_date')];
			if($booking_date=="" || $booking_date=="0000-00-00 00:00:00"){
			$booking_date=$result[csf('insert_date')];
			}
	 	?>
		<table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" style="margin-top: 5px; margin-bottom: 5px" >
	        <tr>
	        	<th width="175" style="text-align: left">Supplier Name </th>
	            <td width="175" ><?
					if($result[csf('pay_mode')]==5){
					echo $company_library[$result[csf('supplier_id')]];
					}
					else{
					echo $supplier_name_arr[$result[csf('supplier_id')]];
					}
				?>
				</td>
				<th width="175" style="text-align: left">Dealing Merchant </th>
	            <td width="175" ><? echo $dealing_marchant; ?></td>

	            <th width="175" style="text-align: left">Buyer/Agent Name</th>
	            <td width="175"><? $buyer_name_str=""; if($client_id!=0) $buyer_name_str=$buyer_name_arr[$result[csf('buyer_id')]]."-".$buyer_name_arr[$client_id]; else $buyer_name_str=$buyer_name_arr[$result[csf('buyer_id')]]; echo $buyer_name_str; ?></td>
	        </tr>
	        <tr>
	        	<th style="text-align: left">Attention </th>
	            <td><? echo $result[csf('attention')]; ?></td>
	            <th style="text-align: left">Merchant E-Mail id </th>
	            <td><? echo $dealing_marchant_email ?></td>
	            <th style="text-align: left">Garments Item </th>
	            <td><?
		            $gmts_item_name="";
					$gmts_item=explode(',',$gmts_item_id);
					for($g=0;$g<=count($gmts_item); $g++)
					{
					$gmts_item_name.= $garments_item[$gmts_item[$g]].",";
					}
					echo rtrim($gmts_item_name,',');
				?>
				</td>
	        </tr>
	        <tr>
	            <th style="text-align: left">Booking Date </th>
	            <td><? echo change_date_format($booking_date,'dd-mm-yyyy','-','');?></td>
	            <th style="text-align: left">Trims ETD </th>
	            <td><? echo change_date_format( $result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>
	            <th style="text-align: left">Pay Mode</th>
	            <td><?echo $pay_mode[$result[csf('pay_mode')]]; ?></td>
	        </tr>
	        <tr>
	        	<th style="text-align: left">Remarks </th>
	            <td colspan="5"><? echo $result[csf('remarks')]?></td>
	        </tr>
	        <tr>
	        	<?
	        	$delivery_address=explode("\n",$result[csf('delivery_address')]);
	        	?>
	        	<th style="text-align: left">Delivery Address </th>
	            <td colspan="5"><? if(count($delivery_address)>0){
	            	foreach ($delivery_address as $key => $value) { ?>
	            	<? echo $value ?><br>
	            	<? }
	            } ?></td>
	        </tr>
	    </table>
		<?
	}?>
	<?
	$booking_country_arr=array();
	$nameArray_booking_country=sql_select( "select pre_cost_fabric_cost_dtls_id,sensitivity,country_id_string from wo_booking_dtls  where booking_no='$txt_booking_no' and  status_active =1 and is_deleted=0");
	foreach($nameArray_booking_country as $nameArray_booking_country_row){
		$country_id_string=explode(",",$nameArray_booking_country_row[csf('country_id_string')]);
		$tocu=count($country_id_string);
		for($cu=0;$cu<$tocu;$cu++){
			$booking_country_arr[$nameArray_booking_country_row[csf('pre_cost_fabric_cost_dtls_id')]][$nameArray_booking_country_row[csf('sensitivity')]][$country_id_string[$cu]]=$country_arr[$country_id_string[$cu]];
		}
	}

	$no_sensitive_arr = array();
	$color_size_arr = array();
	$contrast_color_arr = array();
	$size_sensitive_arr = array();
	$as_per_gmts_color_arr = array();
	?>
	<!--===================================AS PER GMTS COLOR START===========================  -->
	<?
		$as_per_gmts_color = sql_select("SELECT b.job_no_mst, a.pre_cost_fabric_cost_dtls_id, e.id, b.pub_shipment_date, b.po_number, b.grouping, b.file_no, sum(b.po_quantity) as po_quantity, a.trim_group, c.cons_uom, e.brand_supplier as brand_sup_ref, e.description, e.rate, e.amount, d.style_ref_no, e.item_color,c.calculatorstring,c.remark, f.cal_parameter,sum(e.requirment) as booking_cons ,e.color_number_id ,e.item_size, e.gmts_sizes ,e.article_number from wo_booking_dtls a, wo_po_break_down b , wo_pre_cost_trim_cost_dtls c , wo_po_details_master d, wo_trim_book_con_dtls e, lib_item_group f where a.id= e.wo_trim_booking_dtls_id and a.booking_no=e.booking_no and a.po_break_down_id=b.id and a.booking_no='$txt_booking_no' and  a.status_active =1 and a.is_deleted=0 and a.trim_group = f.id and a.pre_cost_fabric_cost_dtls_id = c.id and a.job_no=d.job_no  and a.sensitivity=1 and e.requirment !=0 and e.status_active=1 and  e.is_deleted=0 group by b.job_no_mst, e.id, b.pub_shipment_date, b.po_number,b.grouping, b.file_no ,a.pre_cost_fabric_cost_dtls_id, a.trim_group, c.cons_uom, e.brand_supplier, e.description, e.rate, e.amount, d.style_ref_no, e.item_color,c.calculatorstring,c.remark,f.cal_parameter ,e.color_number_id,e.item_size,e.gmts_sizes,e.article_number order by b.po_number");
		if(count($as_per_gmts_color)>0){
			foreach ($as_per_gmts_color as $key => $value) {
				$gmts_color_attr =array('grouping','style_ref_no');
				foreach ($gmts_color_attr as $attr) {
					$as_per_gmts_color_arr[$value[csf('job_no_mst')]][$attr] = $value[csf($attr)];
				}
				$sub_gmts_color_attr = array('id', 'pub_shipment_date', 'grouping', 'file_no', 'po_quantity', 'trim_group', 'cons_uom', 'brand_sup_ref', 'description', 'rate', 'amount','item_color','calculatorstring','remark','cal_parameter','booking_cons','color_number_id','gmts_sizes','item_size','article_number','po_number');
				foreach ($sub_gmts_color_attr as $attr) {
					$as_per_gmts_color_arr[$value[csf('job_no_mst')]]['trim_groups'][$value[csf('trim_group')]][$value[csf('id')]][$attr] = $value[csf($attr)];
				}
			}
		}


	?>
    <!--===================================AS PER GMTS COLOR END=============================  -->
    <!--==================================Size Sensitive START===============================  -->
	<?
		$size_sensitive = sql_select("SELECT b.job_no_mst, a.pre_cost_fabric_cost_dtls_id, e.id, b.pub_shipment_date, b.po_number, b.grouping, b.file_no, sum(b.po_quantity) as po_quantity, a.trim_group, c.cons_uom, e.brand_supplier as brand_sup_ref, e.description, e.rate, e.amount, d.style_ref_no, e.item_color,c.calculatorstring,c.remark, f.cal_parameter,sum(e.requirment) as booking_cons ,e.color_number_id ,e.item_size, e.gmts_sizes ,e.article_number from wo_booking_dtls a, wo_po_break_down b , wo_pre_cost_trim_cost_dtls c , wo_po_details_master d, wo_trim_book_con_dtls e, lib_item_group f where a.id= e.wo_trim_booking_dtls_id and a.booking_no=e.booking_no and a.po_break_down_id=b.id and a.booking_no='$txt_booking_no' and  a.status_active =1 and a.is_deleted=0 and a.trim_group = f.id and a.pre_cost_fabric_cost_dtls_id = c.id and a.job_no=d.job_no  and a.sensitivity=2 and e.requirment !=0 and e.status_active=1 and  e.is_deleted=0 group by b.job_no_mst, e.id, b.pub_shipment_date, b.po_number,b.grouping, b.file_no ,a.pre_cost_fabric_cost_dtls_id, a.trim_group, c.cons_uom, e.brand_supplier, e.description, e.rate, e.amount, d.style_ref_no, e.item_color,c.calculatorstring,c.remark,f.cal_parameter ,e.color_number_id,e.item_size,e.gmts_sizes,e.article_number order by e.id,b.po_number");

		if(count($size_sensitive)>0){
			foreach ($size_sensitive as $key => $value) {
				$size_sensitive_attr =array('grouping','style_ref_no');
				foreach ($size_sensitive_attr as $attr) {
					$size_sensitive_arr[$value[csf('job_no_mst')]][$attr] = $value[csf($attr)];
				}
				$sub_size_sensitive_attr = array('id', 'pub_shipment_date', 'grouping', 'file_no', 'po_quantity', 'trim_group', 'cons_uom', 'brand_sup_ref', 'description', 'rate', 'amount','item_color','calculatorstring','remark','cal_parameter','booking_cons','color_number_id','gmts_sizes','item_size','article_number','po_number');
				foreach ($sub_size_sensitive_attr as $attr) {
					$size_sensitive_arr[$value[csf('job_no_mst')]]['trim_groups'][$value[csf('trim_group')]][$value[csf('id')]][$attr] = $value[csf($attr)];
				}
			}
		}

	?>
    <!--==============================Size Sensitive END====================================  -->
    <!--==============================AS PER CONTRAST COLOR START===========================  -->
	<?
		$contrast_color = sql_select("SELECT b.job_no_mst, a.pre_cost_fabric_cost_dtls_id, e.id, b.pub_shipment_date, b.po_number, b.grouping, b.file_no, sum(b.po_quantity) as po_quantity, a.trim_group, c.cons_uom, e.brand_supplier as brand_sup_ref, e.description, e.rate, e.amount, d.style_ref_no, e.item_color,c.calculatorstring,c.remark, f.cal_parameter,sum(e.requirment) as booking_cons ,e.color_number_id ,e.item_size, e.gmts_sizes from wo_booking_dtls a, wo_po_break_down b , wo_pre_cost_trim_cost_dtls c , wo_po_details_master d, wo_trim_book_con_dtls e, lib_item_group f where a.id= e.wo_trim_booking_dtls_id and a.booking_no=e.booking_no and a.po_break_down_id=b.id and a.booking_no='$txt_booking_no' and  a.status_active =1 and a.is_deleted=0 and a.trim_group = f.id and a.pre_cost_fabric_cost_dtls_id = c.id and a.job_no=d.job_no  and a.sensitivity=3 and e.requirment !=0 and e.status_active=1 and  e.is_deleted=0 group by b.job_no_mst, e.id, b.pub_shipment_date, b.po_number,b.grouping, b.file_no ,a.pre_cost_fabric_cost_dtls_id, a.trim_group, c.cons_uom, e.brand_supplier, e.description, e.rate, e.amount, d.style_ref_no, e.item_color,c.calculatorstring,c.remark,f.cal_parameter ,e.color_number_id,e.item_size,e.gmts_sizes order by b.po_number");

		if(count($contrast_color)>0){
			foreach ($contrast_color as $key => $value) {
				$contrast_color_attr =array('grouping','style_ref_no');
				foreach ($contrast_color_attr as $attr) {
					$contrast_color_arr[$value[csf('job_no_mst')]][$attr] = $value[csf($attr)];
				}
				$sub_contrast_color_attr = array('id', 'pub_shipment_date', 'grouping', 'file_no', 'po_quantity', 'trim_group', 'cons_uom', 'brand_sup_ref', 'description', 'rate', 'amount','item_color','calculatorstring','remark','cal_parameter','booking_cons','color_number_id','gmts_sizes','item_size','po_number');
				foreach ($sub_contrast_color_attr as $attr) {
					$contrast_color_arr[$value[csf('job_no_mst')]]['trim_groups'][$value[csf('trim_group')]][$value[csf('id')]][$attr] = $value[csf($attr)];
				}
			}
		}
		//echo "<pre>".print_r($size_sensitive_arr, true); die;
	?>
    <!--=============================AS PER CONTRAST COLOR ==================================  -->
    <!--=============================AS PER GMTS Color & SIZE START==========================  -->
	<?
		$color_size=sql_select("SELECT b.job_no_mst, a.pre_cost_fabric_cost_dtls_id, e.id, b.pub_shipment_date, b.po_number, b.grouping, b.file_no, sum(b.po_quantity) as po_quantity, a.trim_group, c.cons_uom, e.brand_supplier as brand_sup_ref, e.description, e.rate, e.amount, d.style_ref_no, e.item_color,c.calculatorstring,c.remark, f.cal_parameter,sum(e.requirment) as booking_cons ,min(g.color_order) as color_order, min(g.size_order) as size_order, g.article_number, e.color_number_id ,e.item_size, e.gmts_sizes from wo_booking_dtls a, wo_po_break_down b , wo_pre_cost_trim_cost_dtls c , wo_po_details_master d, wo_trim_book_con_dtls e, lib_item_group f ,wo_po_color_size_breakdown g where a.id= e.wo_trim_booking_dtls_id and a.booking_no=e.booking_no and a.po_break_down_id=b.id and a.booking_no='$txt_booking_no' and  a.status_active =1 and a.is_deleted=0 and a.trim_group = f.id and a.pre_cost_fabric_cost_dtls_id = c.id and a.job_no=d.job_no and  e.po_break_down_id=g.po_break_down_id and  e.color_number_id=g.color_number_id and e.gmts_sizes=g.size_number_id and  g.id=e.color_size_table_id and a.sensitivity=4 and e.requirment !=0 and e.status_active=1 and  e.is_deleted=0 group by b.job_no_mst, e.id, b.pub_shipment_date, b.po_number,b.grouping, b.file_no ,a.pre_cost_fabric_cost_dtls_id, a.trim_group, c.cons_uom, e.brand_supplier, e.description, e.rate, e.amount, d.style_ref_no,e.item_color,c.calculatorstring,c.remark,f.cal_parameter ,g.article_number,e.color_number_id,e.item_size,e.gmts_sizes order by e.id");

		if(count($color_size)>0){
			foreach ($color_size as $key => $value) {
				$color_size_attr =array('grouping','style_ref_no');
				foreach ($color_size_attr as $attr) {
					$color_size_arr[$value[csf('job_no_mst')]][$attr] = $value[csf($attr)];
				}
				$sub_color_size_attr = array('id', 'pub_shipment_date', 'grouping', 'file_no', 'po_quantity', 'trim_group', 'cons_uom', 'brand_sup_ref', 'description', 'rate', 'amount','item_color','calculatorstring','remark','cal_parameter','booking_cons','article_number','color_number_id','gmts_sizes','item_size','po_number');
				foreach ($sub_color_size_attr as $attr) {
					$color_size_arr[$value[csf('job_no_mst')]]['trim_groups'][$value[csf('trim_group')]][$value[csf('id')]][$attr] = $value[csf($attr)];
				}
			}
		}
   	?>
    <!--===================================AS PER Color & SIZE ==============================  -->
    <!--===================================NO SENSITIBITY START===============================  -->
	<?
		$no_sensitive_item=sql_select("SELECT a.pre_cost_fabric_cost_dtls_id, a.trim_group, b.job_no_mst,b.id,b.pub_shipment_date, b.po_number,b.grouping, b.file_no, sum(b.po_quantity) as po_quantity,  c.cons_uom, e.brand_supplier as brand_sup_ref, e.description, c.calculatorstring,c.remark, d.style_ref_no ,e.item_color, sum(e.requirment) as booking_cons, e.rate, e.amount, f.cal_parameter from wo_booking_dtls a, wo_po_break_down b , wo_pre_cost_trim_cost_dtls c , wo_po_details_master d,wo_trim_book_con_dtls e,lib_item_group f where a.id= e.wo_trim_booking_dtls_id and a.booking_no=e.booking_no and a.po_break_down_id=b.id and a.booking_no='$txt_booking_no' and  a.status_active =1 and a.is_deleted=0 and a.trim_group = f.id and a.pre_cost_fabric_cost_dtls_id = c.id and a.job_no=d.job_no and a.sensitivity=0 group by b.job_no_mst, b.id, b.pub_shipment_date, b.po_number,b.grouping, b.file_no ,a.pre_cost_fabric_cost_dtls_id, a.trim_group, c.cons_uom, e.brand_supplier, e.description, e.rate, e.amount, d.style_ref_no,e.item_color,c.calculatorstring,c.remark,f.cal_parameter order by b.po_number");

		if(count($no_sensitive_item)>0)
		{
			foreach ($no_sensitive_item as $key => $value) {
				$sensitive_attr =array('grouping','style_ref_no');
				foreach ($sensitive_attr as $attr) {
					$no_sensitive_arr[$value[csf('job_no_mst')]][$attr] = $value[csf($attr)];
				}
				$trim_cost_attr = array('id', 'pub_shipment_date', 'grouping', 'file_no', 'po_quantity', 'trim_group', 'cons_uom', 'brand_sup_ref', 'description', 'rate', 'amount','item_color','calculatorstring','remark','cal_parameter','booking_cons','po_number');
				foreach ($trim_cost_attr as $attr) {
					$no_sensitive_arr[$value[csf('job_no_mst')]]['trim_cost_dtls'][$value[csf('description')]][$value[csf('pre_cost_fabric_cost_dtls_id')]][$value[csf('po_number')]][$attr] = $value[csf($attr)];
				}
			}
		}
		/*echo '<pre>';
		print_r($no_sensitive_arr); die;*/

	$cal_parameter_arr = array(1=>"Mtr",2=>"Pcs",3=>"Pcs",4=>"Pcs",5=>"Yds",6=>"Yds",7=>"Pcs",8=>"Yds");
	if(count($as_per_gmts_color_arr) > 0){
		foreach ($as_per_gmts_color_arr as $job_no => $data_arr) { ?>
			<table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" style="margin-bottom: 10px" >
				<tr>
				<? $header ='As Per Garments Color (Job No: '.$job_no.') Style NO: '.$data_arr['style_ref_no'].' Int Ref.:'.$data_arr['grouping']; ?>
                <td colspan="<? echo ($show_comment==1 ? '11':'10') ?>"><strong><? echo $header ?></strong></td>
            	</tr>
            	<tr>
                <th>Sl</th>
                <th>Item Group</th>
                <th>Item Description</th>
                <th>Brand/Supplier Ref.</th>
                <th>Item Color</th>
                <th>Po. No.</th>
                <th>Po. Qty</th>
                <th>Qty per Unit</th>
                <th>WO Qty.</th>
                <th>UOM</th>
                <th>Rate</th>
                <th>Amount</th>
                <? if($show_comment==1) {?>
                <th>Remarks</th>
                <? } ?>
            </tr><?
            $i=1;
            $po_qty = '';
            $po_amount = '';
            foreach ($data_arr['trim_groups'] as $group_id=>$group_data) { ?>
            	<tr>
        			<td rowspan="<? echo count($group_data) ?>"><? echo $i; ?></td>
        			<td rowspan="<? echo count($group_data) ?>"><? echo $trim_group_library[$group_id];?> </td>
            	<?
            	$group_qty='';
            	$group_amount = '';
            	foreach ($group_data as $data) {
            		$calQty=explode("_",$data['calculatorstring']);
				   	if($data['cal_parameter'] && end($calQty)){
					   $per_unit= "1".$unit_of_measurement[$order_uom_arr[$data['trim_group']]]."=".$calQty[2]." ".$cal_parameter_arr[$data['cal_parameter']];
				    }
				    else{
				    	$per_unit = '';
				    }
					$wo_rate=number_format($data['rate'],4,'.','');
					$amount = $wo_rate*$data['booking_cons'];
					?>
        			<td><? echo $data['description']; ?></td>
        			<td><? echo $data['brand_sup_ref']; ?></td>
        			<td><? echo $color_library[$data['item_color']]; ?></td>
        			<td><? echo $data['po_number']; ?></td>
        			<td><? echo $data['po_quantity']; ?></td>
        			<td><? echo $per_unit; ?></td>
        			<td><? echo number_format($data['booking_cons'],4) ?></td>
        			<td><? echo $unit_of_measurement[$order_uom_arr[$data['trim_group']]] ?></td>
        			<td title="rate=<? echo $data['rate'];?>"><? echo number_format($data['rate'],4); ?></td>
        			<td><? echo number_format($amount,4); ?></td>
        			<? if($show_comment==1) {?>
        			<td><? echo $data['remark'] ?></td>
        			<? } ?>
            	</tr>
					<?
					$group_qty += $data['booking_cons'];
            		$group_amount += $amount;
            	}
            	$i++; ?>
            	<tr>
		         	<th colspan="8" style="text-align: right;">Item Qty.</th>
		         	<th><? echo number_format($group_qty,4); $po_qty += $group_qty; ?></th>
		         	<th colspan="2">Item Amount</th>
		         	<th><? echo number_format($group_amount,4); $po_amount += $group_amount; ?></th>
		         	<? if($show_comment==1) {?>
        			<td>&nbsp;</td>
        			<? } ?>
		        </tr>
            	<?
            }
            ?>
            <tr>
	         	<th colspan="8" style="text-align: right;">Total Qty.</th>
	         	<th><? echo number_format($po_qty,4) ?></th>
	         	<th colspan="2">Total Amount</th>
	         	<th><? echo number_format($po_amount,4); $booking_grand_total+=$po_amount;?></th>
	         	<? if($show_comment==1) {?>
        			<td>&nbsp;</td>
        		<? } ?>
	        </tr>
			</table><?
		}
	}
	if(count($size_sensitive_arr) > 0){
		foreach ($size_sensitive_arr as $job_no => $data_arr) {
			//foreach ($po_no_arr as $po_number => $data_arr) { ?>
				<table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" style="margin-bottom: 10px" >
					<tr>
					<? $header ='Size Sensitive (Job No: '.$job_no.') Style NO: '.$data_arr['style_ref_no'].' Int Ref.:'.$data_arr['grouping']; ?>
	                <td colspan="<? echo ($show_comment==1 ? '12':'11') ?>"><strong><? echo $header ?></strong></td>
	            	</tr>
	            	<tr>
	                <th>Sl</th>
	                <th>Item Group</th>
	                <th>Item Description</th>
	                <th>Brand/Supplier Ref.</th>
	                <th>Article No</th>
	                <th>Item Size</th>
	                <th>Po. No.</th>
	                <th>Po. Qty.</th>
	                <th>Qty per Unit</th>
	                <th>WO Qty.</th>
	                <th>UOM</th>
	                <th>Rate</th>
	                <th>Amount</th>
	                <? if($show_comment==1) {?>
	                <th>Remarks</th>
	                <? } ?>
	            </tr><?
	            $i=1;
	            $po_qty = '';
	            $po_amount = '';
	            foreach ($data_arr['trim_groups'] as $group_id=>$group_data) { ?>
	            	<tr>
            			<td rowspan="<? echo count($group_data) ?>"><? echo $i; ?></td>
            			<td rowspan="<? echo count($group_data) ?>"><? echo $trim_group_library[$group_id];?> </td>
	            	<?
	            	$group_qty='';
	            	$group_amount = '';
	            	foreach ($group_data as $data) {
	            		$calQty=explode("_",$data['calculatorstring']);
					   	if($data['cal_parameter'] && end($calQty)){
						   $per_unit= "1".$unit_of_measurement[$order_uom_arr[$data['trim_group']]]."=".$calQty[2]." ".$cal_parameter_arr[$data['cal_parameter']];
					    }
					    else{
					    	$per_unit = '';
					    }
						$wo_rate=number_format($data['rate'],4,'.','');
						$amount = $wo_rate*$data['booking_cons'];
						?>
            			<td><? echo $data['description']; ?></td>
            			<td><? echo $data['brand_sup_ref']; ?></td>
            			<td><? echo ($result_color[csf('article_number')]!="no article" ? ' - ':$result_color[csf('article_number')]); ?></td>
            			<td><? echo $data['item_size']; ?></td>
            			<td><? echo $data['po_number']; ?></td>
            			<td><? echo $data['po_quantity']; ?></td>
            			<td><? echo $per_unit; ?></td>
            			<td><? echo number_format($data['booking_cons'],4) ?></td>
            			<td><? echo $unit_of_measurement[$order_uom_arr[$data['trim_group']]] ?></td>
            			<td><? echo number_format($data['rate'],4); ?></td>
            			<td><? echo number_format($amount,4); ?></td>
            			<? if($show_comment==1) {?>
            			<td><? echo $data['remark'] ?></td>
            			<? } ?>
	            	</tr>
						<?
						$group_qty += $data['booking_cons'];
	            		$group_amount += $amount;
	            	}
	            	$i++; ?>
	            	<tr>
			         	<th colspan="9" style="text-align: right;">Item Qty.</th>
			         	<th><? echo number_format($group_qty,4); $po_qty += $group_qty; ?></th>
			         	<th colspan="2">Item Amount</th>
			         	<th><? echo number_format($group_amount,4); $po_amount += $group_amount; ?></th>
			         	<? if($show_comment==1) {?>
            			<td>&nbsp;</td>
            			<? } ?>
			        </tr>
	            	<?
	            }
	            ?>
	            <tr>
		         	<th colspan="9" style="text-align: right;">Total Qty.</th>
		         	<th><? echo number_format($po_qty,4) ?></th>
		         	<th colspan="2">Total Amount</th>
		         	<th><? echo number_format($po_amount,4); $booking_grand_total+=$po_amount;?></th>
		         	<? if($show_comment==1) {?>
            			<td>&nbsp;</td>
            		<? } ?>
		        </tr>
				</table><?
			//}
		}
	}
	if(count($contrast_color_arr) > 0){
		foreach ($contrast_color_arr as $job_no => $data_arr) {
			//foreach ($po_no_arr as $po_number => $data_arr) { ?>
				<table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" style="margin-bottom: 10px" >
					<tr>
					<? $header ='Contrast Color (Job No: '.$job_no.') Style NO: '.$data_arr['style_ref_no'].' Int Ref.:'.$data_arr['grouping']; ?>
	                <td colspan="<? echo ($show_comment==1 ? '12':'11') ?>"><strong><? echo $header ?></strong></td>
	            	</tr>
	            	<tr>
	                <th>Sl</th>
	                <th>Item Group</th>
	                <th>Item Description</th>
	                <th>Brand/Supplier Ref.</th>
	                <th>Item Color</th>
	                <th>Gmts Color</th>
	                <th>Po. No.</th>
	                <th>Po. Qty</th>
	                <th>Qty per Unit</th>
	                <th>WO Qty.</th>
	                <th>UOM</th>
	                <th>Rate</th>
	                <th>Amount</th>
	                <? if($show_comment==1) {?>
	                <th>Remarks</th>
	                <? } ?>
	            </tr><?
	            $i=1;
	            $po_qty = '';
	            $po_amount = '';
	            foreach ($data_arr['trim_groups'] as $group_id=>$group_data) { ?>
	            	<tr>
            			<td rowspan="<? echo count($group_data) ?>"><? echo $i; ?></td>
            			<td rowspan="<? echo count($group_data) ?>"><? echo $trim_group_library[$group_id];?> </td>
	            	<?
	            	$group_qty='';
	            	$group_amount = '';
	            	foreach ($group_data as $data) {
	            		$calQty=explode("_",$data['calculatorstring']);
					   	if($data['cal_parameter'] && end($calQty)){
						   $per_unit= "1".$unit_of_measurement[$order_uom_arr[$data['trim_group']]]."=".$calQty[2]." ".$cal_parameter_arr[$data['cal_parameter']];
					    }
					    else{
					    	$per_unit = '';
					    }
						$wo_rate=number_format($data['rate'],4,'.','');
						$amount =$wo_rate*$data['booking_cons'];
						?>
            			<td><? echo $data['description']; ?></td>
            			<td><? echo $data['brand_sup_ref']; ?></td>
            			<td><? echo $color_library[$data['item_color']]; ?></td>
                        <td><? echo $color_library[$data['color_number_id']]; ?></td>
            			<td><? echo $data['po_number'] ?></td>
            			<td><? echo $data['po_quantity'] ?></td>
            			<td><? echo $per_unit; ?></td>
            			<td><? echo number_format($data['booking_cons'],4) ?></td>
            			<td><? echo $unit_of_measurement[$order_uom_arr[$data['trim_group']]] ?></td>
            			<td><? echo number_format($data['rate'],4); ?></td>
            			<td><? echo number_format($amount,4); ?></td>
            			<? if($show_comment==1) {?>
            			<td><? echo $data['remark'] ?></td>
            			<? } ?>
	            	</tr>
						<?
						$group_qty += $data['booking_cons'];
	            		$group_amount += $amount;
	            	}
	            	$i++; ?>
	            	<tr>
			         	<th colspan="9" style="text-align: right;">Item Qty.</th>
			         	<th><? echo number_format($group_qty,4); $po_qty += $group_qty; ?></th>
			         	<th colspan="2">Item Amount</th>
			         	<th><? echo number_format($group_amount,4); $po_amount += $group_amount; ?></th>
			         	<? if($show_comment==1) {?>
            			<td>&nbsp;</td>
            			<? } ?>
			        </tr>
	            	<?
	            }
	            ?>
	            <tr>
		         	<th colspan="9" style="text-align: right;">Total Qty.</th>
		         	<th><? echo number_format($po_qty,4) ?></th>
		         	<th colspan="2">Total Amount</th>
		         	<th><? echo number_format($po_amount,4); $booking_grand_total+=$po_amount;?></th>
		         	<? if($show_comment==1) {?>
            			<td>&nbsp;</td>
            		<? } ?>
		        </tr>
				</table><?
			//}
		}
	}
	if(count($color_size_arr) > 0){
		foreach ($color_size_arr as $job_no => $data_arr) {
			//foreach ($po_no_arr as $po_number => $data_arr) { ?>
				<table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" style="margin-bottom: 10px" >
					<tr>
					<? $header ='Color & size sensitive (Job No: '.$job_no.') Style NO: '.$data_arr['style_ref_no'].' Int Ref.:'.$data_arr['grouping']; ?>
	                <td colspan="<? echo ($show_comment==1 ? '15':'14') ?>"><strong><? echo $header ?></strong></td>
	            	</tr>
	            	<tr>
	                <th>Sl</th>
	                <th>Item Group</th>
	                <th>Item Description</th>
	                <th>Brand/Supplier Ref.</th>
	                <th>Article No.</th>	                
	                <th>Gmts Color</th>
	                <th>Item Color</th>
	                <th>Gmts Size</th>
	                <th>Item Size</th>
	                <th>Po. No.</th>
	                <th>Po. Qty</th>
	                <th>Qty per Unit</th>
	                <th>WO Qty.</th>
	                <th>UOM</th>
	                <th>Rate</th>
	                <th>Amount</th>
	                <? if($show_comment==1) {?>
	                <th>Remarks</th>
	                <? } ?>
	            </tr><?
	            $i=1;
	            $po_qty = '';
	            $po_amount = '';
	            foreach ($data_arr['trim_groups'] as $group_id=>$group_data) { ?>
	            	<tr>
            			<td rowspan="<? echo count($group_data) ?>"><? echo $i; ?></td>
            			<td rowspan="<? echo count($group_data) ?>"><? echo $trim_group_library[$group_id];?> </td>
	            	<?
	            	$group_qty='';
	            	$group_amount = '';
	            	foreach ($group_data as $data) {
	            		$calQty=explode("_",$data['calculatorstring']);
					   	if($data['cal_parameter'] && end($calQty)){
						   $per_unit= "1".$unit_of_measurement[$order_uom_arr[$data['trim_group']]]."=".$calQty[2]." ".$cal_parameter_arr[$data['cal_parameter']];
					    }
					    else{
					    	$per_unit = '';
					    }
						$wo_rate=number_format($data['rate'],4,'.','');
						$amount = $wo_rate*$data['booking_cons'];
						?>
            			<td><? echo $data['description']; ?></td>
            			<td><? echo $data['brand_sup_ref']; ?></td>
            			<td><? echo ($result_color[csf('article_number')]!="no article" ? '-':$result_color[csf('article_number')]); ?></td>            			            			
            			<td><? echo $color_library[$data['color_number_id']]; ?></td>
            			<td><? echo $color_library[$data['item_color']]; ?></td>
            			<td><? echo $size_library[$data['gmts_sizes']] ?></td>
            			<td><? echo $data['item_size'] ?></td>
            			<td><? echo $data['po_number'] ?></td>
            			<td><? echo $data['po_quantity'] ?></td>
            			<td><? echo $per_unit; ?></td>
            			<td><? echo number_format($data['booking_cons'],4) ?></td>
            			<td><? echo $unit_of_measurement[$order_uom_arr[$data['trim_group']]] ?></td>
            			<td><? echo number_format($data['rate'],4); ?></td>
            			<td><? echo number_format($amount,4); ?></td>
            			<? if($show_comment==1) {?>
            			<td><? echo $data['remark'] ?></td>
            			<? } ?>
	            	</tr>
						<?
						$group_qty += $data['booking_cons'];
	            		$group_amount += $amount;
	            	}
	            	$i++; ?>
	            	<tr>
			         	<th colspan="12" style="text-align: right;">Item Qty.</th>
			         	<th><? echo number_format($group_qty,4); $po_qty += $group_qty; ?></th>
			         	<th colspan="2">Item Amount</th>
			         	<th><? echo number_format($group_amount,4); $po_amount += $group_amount; ?></th>
			         	<? if($show_comment==1) {?>
            			<td>&nbsp;</td>
            			<? } ?>
			        </tr>
	            	<?
	            }
	            ?>
	            <tr>
		         	<th colspan="12" style="text-align: right;">Total Qty.</th>
		         	<th><? echo number_format($po_qty,4) ?></th>
		         	<th colspan="2">Total Amount</th>
		         	<th><? echo number_format($po_amount,4); $booking_grand_total+=$po_amount;?></th>
		         	<? if($show_comment==1) {?>
            			<td>&nbsp;</td>
            		<? } ?>
		        </tr>
				</table><?
			//}
		}
	}
	if(count($no_sensitive_arr) > 0){
		foreach ($no_sensitive_arr as $job_no => $data_arr) {
		//foreach ($po_no_arr as $po_number => $data_arr) { ?>
			<table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" style="margin-bottom: 10px" >
				<tr>
					<? $header ='NO sensitive (Job No: '.$job_no.') Style NO: '.$data_arr['style_ref_no'].' Int Ref.:'.$data_arr['grouping']; ?>
	                <td colspan="11"><strong><? echo $header ?></strong></td>
	            </tr>
	            <tr>
	                <th>Sl</th>
	                <th>Item Group</th>
	                <th>Item Description</th>
	                <th>Brand/Supplier Ref.</th>
	                <th>Item Color</th>
	                <th>Po. No.</th>
	                <th>Po. Qty</th>
	                <th>Qty per Unit</th>
	                <th>Qnty</th>
	                <th>UOM</th>
	                <th>Rate</th>
	                <th>Amount</th>
	                <? if($show_comment==1) {?>
	                <th>Remarks</th>
	                <? } ?>
	            </tr>
	            <? $i=1;
	            $po_total_amount='';
	    		$po_total_qty = '';
	            foreach ($data_arr['trim_cost_dtls'] as $pre_cost_description) {
	            	foreach ($pre_cost_description as $key => $pre_cost_data) {
	            		foreach ($pre_cost_data as $po_number => $data) {
	            			$calQty=explode("_",$data['calculatorstring']);
		                   	if($data['cal_parameter'] && end($calQty)){
		                       $per_unit= "1".$unit_of_measurement[$order_uom_arr[$data['trim_group']]]."=".$calQty[2]." ".$cal_parameter_arr[$data['cal_parameter']];
		                    }
		                    else{
		                        $per_unit = '';
		                    }
							$wo_rate=number_format($data['rate'],4,'.','');
		                    $amount = $wo_rate*$data['booking_cons'];
		                    ?>
		                    <tr>
		                        <td><? echo $i; ?></td>
		                        <td><? echo $trim_group_library[$data['trim_group']];?> </td>
		                        <td><? echo $data['description']; ?></td>
		                        <td><? echo $data['brand_sup_ref']; ?></td>
		                        <td><? echo $color_library[$data['item_color']]; ?></td>
		                        <td><? echo $data['po_number'] ?></td>
		                        <td><? echo $data['po_quantity'] ?></td>
		                        <td><? echo $per_unit; ?></td>
		                        <td><? echo number_format($data['booking_cons'],4) ?></td>
		                        <td><? echo $unit_of_measurement[$order_uom_arr[$data['trim_group']]] ?></td>
		                        <td><? echo number_format($data['rate'],4); ?></td>
								<td  title="<?=$wo_rate."*".$data['booking_cons'];?>"><? echo number_format($data['amount'],4); ?></td>
		                        <? if($show_comment==1) {?>
		                        <td><? echo $data['remark'] ?></td>
		                        <? } ?>
		                    </tr>
			                <? $i++;
			                $po_total_amount += $amount;
			                $po_total_qty += $data['booking_cons'];
			            }
			        }
	          	}
	         ?>
	         <tr>
	         	<th colspan="8" style="text-align: right;">Total Qty.</th>
	         	<th><? echo number_format($po_total_qty,4) ?></th>
	         	<th colspan="2">Total Amount</th>
	         	<th><? echo number_format($po_total_amount,4); $booking_grand_total+=$po_total_amount; ?></th>
	         </tr>
			</table>
		 <?
		 //}
		}
	}

 ?>
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
    <table width="100%" style="margin-top:1px">
       <tr>
       <td>
       <table width="100%" class="rpt_table"  border="1" cellpadding="0" cellspacing="0" rules="all">
            <tr style="border:1px solid black;">
                <td width="30%" style="border:1px solid black; text-align:left">Total Booking Amount</td>
                <td width="70%" style="border:1px solid black; text-align:left"><? echo number_format($booking_grand_total,2);?></td>
            </tr>
            <tr style="border:1px solid black;">
                <td width="30%" style="border:1px solid black; text-align:left">Total Booking Amount (in word)</td>
                <td width="70%" style="border:1px solid black;"><? echo number_to_words(def_number_format($booking_grand_total,2,""),$mcurrency, $dcurrency);?></td>
            </tr>
       </table>
       </td>
       </tr>
    </table>
    <br/>
    <table width="100%">
        <tr>
        <td width="49%">
        <?
            echo get_spacial_instruction($txt_booking_no);
        ?>
        </td>
        <td width="2%"></td>
    <?
     $data_array=sql_select("select b.approved_by,b.approved_no, b.approved_date, c.user_full_name from  wo_booking_mst a , approval_history b, user_passwd c where a.id=b.mst_id and b.approved_by=c.id and a.booking_no='$txt_booking_no' and b.entry_form=8 and  a.status_active =1 and a.is_deleted=0");
    ?>
        <td width="49%" valign="top">
            <table  width="100%" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">

                <tr style="border:1px solid black;">
                    <td colspan="3" style="border:1px solid black;">Approval Status</td>
                    </tr>
                    <tr style="border:1px solid black;">
                    <td width="3%" style="border:1px solid black;">Sl</td><td width="50%" style="border:1px solid black;">Name</td><td width="27%" style="border:1px solid black;">Approval Date</td><td width="20%" style="border:1px solid black;">Approval No</td>
                    </tr>


                <?
                $i;
                foreach($data_array as $row){
                ?>
                <tr style="border:1px solid black;">
                    <td width="3%" style="border:1px solid black;"><? echo $i;?></td><td width="50%" style="border:1px solid black;"><? echo $row[csf('user_full_name')];?></td><td width="27%" style="border:1px solid black;"><? echo change_date_format($row[csf('approved_date')],"dd-mm-yyyy","-");?></td><td width="20%" style="border:1px solid black;"><? echo $row[csf('approved_no')];?></td>
                    </tr>
                    <?
                    $i++;
                }
                    ?>

            </table>
    </td>
    </tr>
	</table>
   	 </tbody>
     </table>
     <!--class="footer_signature"-->
    <div  style="margin-top:-50px;">
         <?
          echo signature_table(132, $cbo_company_name, "1333px",1);
		 ?>
   </div>
   <? 
	$emailBody=ob_get_contents();
	//ob_clean();
	if($is_mail_send==1){
		$req_approved=return_field_value("id", "ELECTRONIC_APPROVAL_SETUP", "PAGE_ID = 336 and is_deleted=0");
		$is_approved=return_field_value("IS_APPROVED", "WO_BOOKING_MST", "STATUS_ACTIVE = 1 and is_deleted=0 and booking_no='$txt_booking_no'");
		if($req_approved && $is_approved==1){
			$emailBody.="<h1 style='border:1px sloid #0ff;'>Approved</h1>";
		}
		elseif($req_approved && $is_approved==0){
			$emailBody.="<h1 style='border:1px sloid #0ff;'>Draft</h1>";
		}
		
	
		$mailArr=array();
		if($mail_id!=''){$mailArr[]=$mail_id;}
		if($supplier_mail_arr[$supplier_id]!=''){$mailArr[]=$supplier_mail_arr[$supplier_id];}
		
		
		$to=implode(',',$mailArr);
		$subject="Trims Booking Auto Mail";
		
		if($to!=""){
			require '../../../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
			require_once('../../../auto_mail/setting/mail_setting.php');
			$header=mailHeader();
			sendMailMailer( $to, $subject, $emailBody );
		}
	}
?>



      <div id="page_break_div">
   	 </div>
    <div>
		<?
			//echo "****".custom_file_name($txt_booking_no,$style_sting,$job_no);
        ?>
    </div>

  </html>
	<?

	$user_id=$_SESSION['logic_erp']['user_id'];
	$report_cat=100;
	$html = ob_get_contents();
		ob_clean();
		//$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
		foreach (glob("tb*.xls") as $filename) {
		//if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename="tb".$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc, $html);
		echo "$filename****$html****$report_cat";
		//exit();
}

if($action=="show_trim_booking_report9") // Aziz-20-10-2020
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
	
	//$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	//$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
 
	$supplier_sql_result=sql_select("select ID,SUPPLIER_NAME,ADDRESS_1,EMAIL from lib_supplier and status_active =1 and  is_deleted=0");
	foreach($supplier_sql_result as $rows){
		$supplier_name_arr[$rows[ID]]=$rows[SUPPLIER_NAME];
		$supplier_address_arr[$rows[ID]]=$rows[ADDRESS_1];
		$supplier_mail_arr[$rows[ID]]=$rows[EMAIL];
	}
	
	
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$order_uom_arr=return_library_array("select id,order_uom  from lib_item_group","id","order_uom");
	$deling_marcent_arr=return_library_array("select id,team_member_name from lib_mkt_team_member_info","id","team_member_name");
	$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");

	$nameArray_approved=sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no='$txt_booking_no' and b.entry_form=8 and  a.status_active =1 and a.is_deleted=0");
	list($nameArray_approved_row)=$nameArray_approved;
	//$revised_no_data=sql_select("select a.revised_no  from wo_booking_mst a where  a.booking_no=$txt_booking_no and  a.status_active =1 and a.is_deleted=0");
	//list($revised_no_row)=$revised_no_data;
		$booking_grand_total=0;
		$currency_id="";


		$buyer_string=array();
		$style_owner=array();
		$job_no=array();
		$style_ref=array();
		$all_dealing_marcent=array();
		$season=array();
		$order_repeat_no=array();
		$po_id_arr=array();

		$nameArray_buyer=sql_select( "select  a.style_ref_no, a.job_no, a.style_owner, a.buyer_name, a.dealing_marchant, a.season, a.season_matrix, a.season_buyer_wise, a.order_repeat_no, b.po_break_down_id from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no='$txt_booking_no' and b.status_active =1 and b.is_deleted=0");
        foreach ($nameArray_buyer as $result_buy){
			$buyer_string[$result_buy[csf('buyer_name')]]=$buyer_name_arr[$result_buy[csf('buyer_name')]];
			$style_owner[$result_buy[csf('job_no')]]=$company_library[$result_buy[csf('style_owner')]];
			$job_no[$result_buy[csf('job_no')]]=$result_buy[csf('job_no')];
			$style_ref[$result_buy[csf('job_no')]]=$result_buy[csf('style_ref_no')];
			$all_dealing_marcent[$result_buy[csf('job_no')]]=$deling_marcent_arr[$result_buy[csf('dealing_marchant')]];
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
		$job_no=implode(",",$job_no);
		$seasons_names=rtrim($seasons_name,',');

		$seasons_names=implode(",",array_unique(explode(",",$seasons_names)));
		$poid_arr=array_unique($po_id_arr);
		//$txt_order_no_id=array_unique($po_id_arr);
		$order_rept_no=rtrim($order_rept_no,',');
		$order_rept_no=implode(",",array_unique(explode(",",$order_rept_no)));

		$po_no=array();
		$file_no=array();
		$ref_no=array();
		$po_quantity=array();
		$pub_shipment_date='';$int_ref_no='';$tot_po_quantity=0;$po_idss='';
		$nameArray_job=sql_select( "select b.job_no_mst,b.id,b.pub_shipment_date, b.po_number,b.grouping, b.file_no, sum(b.po_quantity) as po_quantity  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no='$txt_booking_no' and  a.status_active =1 and a.is_deleted=0 group by b.job_no_mst,b.id,b.pub_shipment_date, b.po_number,b.grouping, b.file_no ");
        foreach ($nameArray_job as $result_job){
			$po_no[$result_job[csf('job_no_mst')]][$result_job[csf('id')]]=$result_job[csf('po_number')];
			$po_shipdate_arr[$result_job[csf('job_no_mst')]].=change_date_format($result_job[csf('pub_shipment_date')]).',';
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
		$sql_job=sql_select( "select b.job_no_mst,b.id as po_id, b.po_quantity as po_quantity  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and  a.status_active =1 and a.is_deleted=0 and  b.status_active =1 and b.is_deleted=0 and b.id in(".$po_idss.") ");
		foreach ($sql_job as $row)
		{
			$job_po_qty_arr[$row[csf('job_no_mst')]][$row[csf('po_id')]]+=$row[csf('po_quantity')];
			$tot_po_quantity+=$row[csf('po_quantity')];
		}

        $nameArray=sql_select( "select a.booking_no, a.pay_mode,a.buyer_id, a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.source, a.remarks, a.revised_no from wo_booking_mst a where a.booking_no='$txt_booking_no' and a.status_active =1 and a.is_deleted=0");
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
		}
	?>
<html>
<head>
  <style type="text/css" media="print">
   table { page-break-inside:auto }
   /*
        @media print {
        thead {display: table-header-group;}
    }
	@media print {
			  #page_break_div {
				page-break-before: always;
			  }
	}
			.footer_signature {
				position: fixed;
				height: auto;
				bottom:0;
				width:100%;

				}
			@media print {
				table {
					page-break-inside: avoid;
				}
			}
			@media all {
  			#page_break_div   { display: none; }
			}*/

</style>
</head>
  
<? ob_start();?>

	<div style="width:1333px" align="center">

   <table width="1333px" cellpadding="0" cellspacing="0" style="border:0px solid black; font-family:'Arial Narrow';" >

	<thead>
       <table width="100%" cellpadding="0" cellspacing="0" style="border:0px solid black;font-family:'Arial Narrow';">
           <tr>
               <td width="20px">
                    <table width="100%" cellpadding="0" cellspacing="0" style="border:0px solid black">
                    <tr>
                    <td width="50" >
					   <? if($report_type==1)
                       {
                           if($link == 1)
                           {
                       ?>
                                <img  src='../../../<? echo $imge_arr[$cbo_company_name]; ?>' height='30%' width='50%' />

                       <?
                           }
                           else
                           {
                       ?>
                                <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='30%' width='50%' />
                       <?	}
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

                   <b style="font-size:25px;font-family:'Arial Narrow';"> <?
                    echo $company_library[$cbo_company_name]; ?>
                    </b>
                    <br>
                    <label style="font-family:'Arial Narrow';">
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
                         <?php echo $report_title; ?>
                        </b>
                    </td>
                     <td width="10px" align="center" style="font-size:20px;font-family:'Arial Narrow';">
                      <table width="80%" align="right" cellpadding="0" cellspacing="0" style="border:0px solid black">
                      	<tr>
                            <td width="80">  Booking No:&nbsp; <?php echo $varcode_booking_no; ?>  </td>
                        </tr>
                        <tr>
                            <td>  Booking Date:&nbsp; <?php echo change_date_format($booking_date); ?>  </td>
                        </tr>
						 <tr>
                            <td>  Delivery Date:&nbsp; <?php echo change_date_format($booking_date); ?>  </td>
                        </tr>
                        <?
                        if($revised_no>0)
						{
						?>
                        <tr>
                            <td>  Revised No:&nbsp; <?php echo $revised_no; ?>  </td>
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
						 ?>
                      </table>

                     </td>
                   </tr>
                   </table>
               </td>
            </tr>
       </table>

		   <table width="100%" style="border:0px solid black;table-layout: fixed;font-family:'Arial Narrow';">
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
                    <td  width="100" style="font-size:12px"><b>&nbsp;</b></td>
					<td  width="110" >&nbsp</td>
                    <td width="100" style="font-size:12px">&nbsp;</td>
					<td width="110">&nbsp;</td>
				</tr>
				<tr>

					<td width="110" colspan="2" rowspan="2" style="font-size:18px;font-family:'Arial Narrow';">Address :&nbsp;
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
					<td width="100" style="font-size:17px">&nbsp;   </td>
					<td width="110">&nbsp</td>
                     <td style="font-size:17px" >&nbsp;   </td>
					<td style="">&nbsp;
					<?
						//echo $attention;
					?>
					</td>

				</tr>
                <tr>

                    <td width="100" style="font-size:17px"><b>&nbsp;</b> </td>
					<td width="110">&nbsp;</td>

                    <td  style="margin-right:150px;" width="280"  colspan="2">
					<p style="margin-left:75px;font-size:17px;"><b>Dealing Marchant : </b>&nbsp;&nbsp;&nbsp;<? echo implode(",",array_unique($all_dealing_marcent));?></p></td>


                </tr>
                <tr>
                    <td style="font-size:17px" ><b>Attention </b>   </td>
					<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;">:&nbsp;
					<?
						echo $attention;
					?>
					</td>
                    <td width="100" style="font-size:17px">&nbsp; </td>
					<td width="110">&nbsp;</td>
                   <td  width="280" style="font-size:17px;" colspan="2">
				   <p style="margin-left:75px;font-size:17px;"><b>Buyer:</b>&nbsp;&nbsp;&nbsp;<? echo $buyer_name_arr[$buyer_id]; ?></p></td>


                </tr>
				 <tr>
                    <td style="font-size:17px" ><b>Pay Mode </b>   </td>
					<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;font-size:17px">:&nbsp;
					<?
						echo $pay_mode[$pay_mode_id];
					?>
					</td>
                    <td width="100" style="font-size:17px">&nbsp;</td>
					<td width="110">&nbsp;</td>
                    <td  style="font-size:17px" align="right">&nbsp;</td>
                	<td>&nbsp;<? //echo implode(",",array_unique($all_dealing_marcent));?></td>

                </tr>
				<tr>
                    <td style="font-size:17px" ><b>Currency </b>   </td>
					<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;font-size:17px">:&nbsp;
					<?
						echo $currency[$currency_id];
					?>
					</td>
                    <td width="100" style="font-size:17px">&nbsp;</td>
					<td width="110">&nbsp;</td>
                    <td  width="100" style="font-size:17px" align="right">&nbsp;</td>
					<td  width="110" >&nbsp;<? //echo $buyer_name_arr[$buyer_id]; ?></td>

                </tr>

				 <tr>
                    <td style="font-size:17px"><b>Source</b></td>
                	<td>:&nbsp;<? echo $source[$source_id];?></td>
                    <td style="font-size:17px"><b>&nbsp;</b></td>
					<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;font-size:17px">:&nbsp;
					<?
						//echo implode(",",array_unique($all_dealing_marcent));
					?>
					</td>
				</tr>
				<tr>
					<td width="100" style="font-size:17px"><b>Remarks</b>  </td>
					<td width="110" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;font-size:17px" colspan="5">:&nbsp;<? echo $remarks; ?></td>

				</tr>
				</table>
    		</thead>
            <tbody>
            <!-- <div id="page_break_div">

            </div>-->


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
		$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls  where booking_no='$txt_booking_no' and  status_active =1 and is_deleted=0 and job_no='".$nameArray_job_po_row[csf('job_no')]."'   and sensitivity=1 group by pre_cost_fabric_cost_dtls_id,trim_group  order by trim_group ");
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
			$shipdate=rtrim($po_shipdate_arr[$nameArray_job_po_row[csf('job_no')]],',');
			$shipdates=implode(",",array_unique(explode(",",$shipdate)));
        ?>
        &nbsp;
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
        	<thead>
            <tr>
                <td colspan="12" align="">
                <table width="100%" style="table-layout: fixed;">
                <tr>
                <td width="60%" align="left"><strong>As Per Garments Color (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]]; if($ref_nos!='' ) echo " &nbsp;Int Ref.:&nbsp;".$ref_nos;else " ";if($file_nos!='' || $file_nos!=0 ) echo " &nbsp;File No.:&nbsp;".$file_nos;else " "; echo " &nbsp;  Po Qty.:&nbsp;".$po_no_qty; ?></strong></td>
                <td width="40%" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>

                </tr>
                </table>
                </td>
            </tr>
            
            <tr>
                <td style="border:1px solid black"  width="20"> <strong>Sl</strong> </td>
                <td style="border:1px solid black" ><strong>Item Group</strong> </td>
				 <td style="border:1px solid black" width="120"><strong>Item Image</strong> </td>
                <td style="border:1px solid black"><strong>Item Desc.</strong> </td>
                <td style="border:1px solid black"><strong>Brand/Supp. Ref.</strong> </td>
                <td align="center" style="border:1px solid black"><strong>Item Color</strong></td>
                <td align="center" style="border:1px solid black"><strong>Qty per Unit</strong></td>
                <td style="border:1px solid black" align="right"  width="80"><strong>WO Qty</strong></td>
                <td style="border:1px solid black" align="center"  width="50"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="right"  width="50"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="right"  width="80"><strong>Amount</strong></td>
                <? if($show_comment==1) {?>
                <td style="border:1px solid black" align="center"><strong>Remarks</strong></td>
                <? }?>
            </tr>
            </thead>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=$grand_total_as_per_gmts_color_qty=0;
            foreach($nameArray_item as $result_item){
			$i++;
            $nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid, b.description, b.brand_supplier,b.item_color,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=1  and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_color order by bid ");
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
				 <td align="left"  style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <?
				$item_imge_arr=sql_select("select master_tble_id,image_location from   common_photo_library where form_name='pre_cost_trimsv2' and file_type=1 and master_tble_id='".$result_item[csf('pre_cost_fabric_cost_dtls_id')]."' ");
				?>
				 <p>
			<?	foreach($item_imge_arr as $row)
			   {
				 if($report_type==1)
                       {
                           if($link == 1)
                           {
                       ?>
                               <img  src='../../../<? echo $row[csf('image_location')]; ?>' height='35px' width='110px' />  &nbsp;

                       <?
                           }
                           else
                           {
                       ?>
                               <img  src='../../<? echo $row[csf('image_location')]; ?>' height='35px' width='110px' />&nbsp;
                       <?	}
                       }
                       else
                       { ?>
                           <img  src='../<? echo $row[csf('image_location')]; ?>' height='35px' width='110px' />&nbsp;
                      <? }

				  }
                       ?>
					   </p>
                </td>
                <?
				$item_desctiption_total=0;
                $total_amount_as_per_gmts_color=$total_qty_as_per_gmts_color=0;$x=1;
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
				$item_desctiption_total += $result_itemdescription[csf('cons')] ;
				?>
                </td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_text=$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
                $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
				echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
				  $total_qty_as_per_gmts_color+=$result_itemdescription[csf('cons')];
				$trims_remark=$trims_remark_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]]['remark'];
                ?>
                </td>
                  <? if($show_comment==1) {
				   if($x==1) {
				  ?>
                 <td style="border:1px solid black; text-align: center" rowspan="<? echo count($nameArray_item_description); ?>"><p><? echo $trims_remark; ?> </p></td>
                 <? }  }?>
            </tr>
            <?
			$x++;
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="4"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold;"><?

					echo number_format($item_desctiption_total ,4);
				 ?></td>
                <td style="border:1px solid black;  text-align:center"><? echo $uom_text; ?></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
				$grand_total_as_per_gmts_color_qty+=$total_qty_as_per_gmts_color;
                ?>
                </td>
                  <? //if($show_comment==1) {?>
                <!--<td rowspan="<? //echo count($nameArray_item_description)+1; ?>">&nbsp;  </td>-->
                <? //} ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="7"><strong>Total</strong></td>
                 <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color_qty,2); ?></td>
                <td  style="border:1px solid black;  text-align:right"><? // echo number_format($grand_total_as_per_gmts_color_qty,2); ?></td>
                 <td align="right" style="border:1px solid black" ><strong></strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
                 <? if($show_comment==1) {?>
                <td rowspan="<? //echo count($nameArray_item_description)+1; ?>">&nbsp; </td>
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
		$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls  where booking_no='$txt_booking_no' and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=2 and  status_active =1 and  is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group  order by trim_group ");
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
        	<thead>
            <tr>
                <td colspan="14" align="">
                 <table width="100%" style="table-layout: fixed;">
                <tr>
                <td width="60%"><strong>Size Sensitive (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]]; echo "&nbsp;&nbsp;Int Ref.:".$ref_nos; if($file_nos!='' || $file_nos!=0 ) echo " &nbsp;File No.:&nbsp;".$file_nos;else " ";echo "&nbsp;&nbsp; Po Qty..:".$po_no_qty; ?></strong></td>
                <td width="40%" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;margin-left:210px; font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
                </tr>
                </table>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black" width="20"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group </strong> </td>
				<td style="border:1px solid black"  width="120"><strong>Item Image</strong> </td>
                <td style="border:1px solid black"><strong>Item Desc</strong> </td>
                <td style="border:1px solid black"><strong>Brand/Supp. Ref.</strong> </td>
                <td align="center" style="border:1px solid black"><strong>Article No</strong></td>
				<td align="center" style="border:1px solid black"><strong>Gmt Size</strong></td>
                <td align="center" style="border:1px solid black"><strong>Item Size/EAN Code</strong></td>
                <td align="center" style="border:1px solid black"  width="50"><strong>Qty per Unit</strong></td>
                <td style="border:1px solid black" align="center" width="80"><strong>WO Qty.</strong></td>
                <td style="border:1px solid black" align="center" width="50"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center" width="50"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center" width="80"><strong>Amount</strong></td>
                 <? if($show_comment==1) {?>
                <td style="border:1px solid black" align="center"><strong>Remarks</strong></td>
                <? } ?>
            </tr>
            </thead>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=$grand_total_as_per_gmts_color_qty=0;
            foreach($nameArray_item as $result_item)
            {
			$i++;
			$nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid, b.description, b.brand_supplier,b.item_size,b.gmts_sizes,b.article_number,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and   a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.sensitivity=2 and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_size,b.article_number,b.gmts_sizes order by bid");
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
                <?
				$item_imge_arr=sql_select("select master_tble_id,image_location from   common_photo_library where form_name='pre_cost_trimsv2' and file_type=1 and master_tble_id='".$result_item[csf('pre_cost_fabric_cost_dtls_id')]."' ");
				?>
				 <p>
			<?	foreach($item_imge_arr as $row)
			   {
				 if($report_type==1)
                       {
                           if($link == 1)
                           {
                       ?>
                               <img  src='../../../<? echo $row[csf('image_location')]; ?>' height='35px' width='110px' />  &nbsp;

                       <?
                           }
                           else
                           {
                       ?>
                               <img  src='../../<? echo $row[csf('image_location')]; ?>' height='35px' width='110px' />&nbsp;
                       <?	}
                       }
                       else
                       { ?>
                           <img  src='../<? echo $row[csf('image_location')]; ?>' height='35px' width='110px' />&nbsp;
                      <? }

				  }
                       ?>
					   </p>
                </td>
                <?
                $item_desctiption_total=0;
                $total_amount_as_per_gmts_color=$total_qty_as_per_gmts_color=0;$k=1;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                ?>
                <td style="border:1px solid black"><? if($result_itemdescription[csf('description')]){ echo $result_itemdescription[csf('description')];}?> </td>
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
                <td style="border:1px solid black; text-align:left">
              <? 	echo $size_library[$result_itemdescription[csf('gmts_sizes')]];?>
                </td>
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
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
                $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
				echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
				 $total_qty_as_per_gmts_color+=$result_itemdescription[csf('cons')];
				$trims_remark=$trims_remark_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]]['remark'];
                ?>
                </td>
                 <? if($show_comment==1) {
				  if($k==1)
				  {
				 ?>
                <td style="border:1px solid black; text-align:center" rowspan="<? echo count($nameArray_item_description); ?>"><p><? echo $trims_remark; ?> </p></td>
                <?
				  }
				} ?>
            </tr>
            <?
				$k++;
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="6"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold"><?

					echo number_format($item_desctiption_total,4);
				 ?></td>
                 <td style="border:1px solid black;  text-align:center"><? echo $uom_text; ?></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
				$grand_total_as_per_gmts_color_qty+=$total_qty_as_per_gmts_color;
                ?>
                </td>

                 <?  if($show_comment==1) {?>
                <td >&nbsp; </td>
                <? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="9"><strong>Total</strong></td>
				  <td align="right"><?  echo number_format($grand_total_as_per_gmts_color_qty,2);?> </td>
                   <td align="right"><?  //echo number_format($grand_total_as_per_gmts_color_qty,2);?> </td>
                    <td align="right"><?  //echo number_format($grand_total_as_per_gmts_color_qty,2);?> </td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>

                 <? if($show_comment==1) {?>
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
		 $nameArray_item=sql_select( "select pre_cost_fabric_cost_dtls_id, trim_group from wo_booking_dtls  where booking_no='$txt_booking_no' and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=3 and  status_active =1 and is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group order by trim_group ");
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
        	<thead>
            <tr>
                <td colspan="13" align="">
                <table width="100%" style="table-layout: fixed;">
                <tr>
                <td width="60%"><strong>Contrast Color (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]];echo " &nbsp;Int Ref.:&nbsp;".$ref_nos;if($file_nos!='' || $file_nos!=0 ) echo " &nbsp;File No.:&nbsp;".$file_nos;else " "; echo " &nbsp;Po Qty.:&nbsp;".$po_no_qty; ?></strong></td>
                <td width="40%" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap; font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
                </tr>
                </table>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black" width="20"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
				 <td style="border:1px solid black"  width="120"><strong>Item Image</strong> </td>
                <td style="border:1px solid black"><strong>Item Desc.</strong> </td>
                <td style="border:1px solid black"><strong>Brand/Supp. Ref.</strong> </td>
                <td align="center" style="border:1px solid black"><strong>Item Color</strong></td>
                <td align="center" style="border:1px solid black"><strong>Gmts Color</strong></td>
                <td align="center" style="border:1px solid black"  width="50"><strong>Qty per Unit</strong></td>
                <td style="border:1px solid black" align="center"  width="80"><strong>WO Qty.</strong></td>
                <td style="border:1px solid black" align="center"  width="50"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"  width="50"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"  width="80"><strong>Amount</strong></td>
                 <? if($show_comment==1) {?>
                <td style="border:1px solid black" align="center"><strong>Remarks</strong></td>
                <? } ?>
            </tr>
            </thead>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=$grand_total_as_per_gmts_color_qty=0;
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
				 <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
               <?
				$item_imge_arr=sql_select("select master_tble_id,image_location from   common_photo_library where form_name='pre_cost_trimsv2' and file_type=1 and master_tble_id='".$result_item[csf('pre_cost_fabric_cost_dtls_id')]."' ");
				?>
				 <p>
			<?	foreach($item_imge_arr as $row)
			   {
				 if($report_type==1)
                       {
                           if($link == 1)
                           {
                       ?>
                               <img  src='../../../<? echo $row[csf('image_location')]; ?>' height='35px' width='110px' />  &nbsp;

                       <?
                           }
                           else
                           {
                       ?>
                               <img  src='../../<? echo $row[csf('image_location')]; ?>' height='35px' width='110px' />&nbsp;
                       <?	}
                       }
                       else
                       { ?>
                           <img  src='../<? echo $row[csf('image_location')]; ?>' height='35px' width='110px' />&nbsp;
                      <? }

				  }
                       ?>
					   </p>
                </td>
                <?
                $item_desctiption_total=0;
                $total_amount_as_per_gmts_color=$total_as_per_gmts_color_qty=0;$m=1;
                foreach($nameArray_item_description as $result_itemdescription)
                {

                ?>
                <td style="border:1px solid black"><? if($result_itemdescription[csf('description')]){ echo $result_itemdescription[csf('description')];}?> </td>
                <td style="border:1px solid black"><? if($result_itemdescription[csf('brand_supplier')]){echo $result_itemdescription[csf('brand_supplier')];} ?> </td>
                <td style="border:1px solid black; text-align:left">
               <? echo $color_library[$result_itemdescription[csf('item_color')]]; ?>
                </td>
                <td style="border:1px solid black; text-align:left">
               <?
			   echo $color_library[$result_itemdescription[csf('color_number_id')]];
			   ?>
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
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
                $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
				$total_as_per_gmts_color_qty+= $result_itemdescription[csf('cons')];
				$trims_remark=$trims_remark_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]]['remark'];
                ?>
                </td>
                 <? if($show_comment==1) {
				 if($m==1)
				 {
				 ?>
                <td style="border:1px solid black; text-align:center" rowspan="<? echo count($nameArray_item_description); ?>"><p><? echo $trims_remark; ?></p> </td>
                <?
				}
				} ?>
            </tr>
            <?
			$m++;
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="5"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold;"><?

				echo number_format($item_desctiption_total,4);
				 ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $uom_text; ?></td>
                <td style="border:1px solid black; text-align:right"></td>

                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
				  $grand_total_as_per_gmts_color_qty+=$total_as_per_gmts_color_qty;
                ?>
                </td>
                 <? if($show_comment==1) {?>
                <td>&nbsp;  </td>
                <? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="10"><strong>Total</strong></td>
				  <td align="right"><?  echo number_format($grand_total_as_per_gmts_color_qty,2);?>  </td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>

                 <? if($show_comment==1) {?>
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
		$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls  where booking_no='$txt_booking_no' and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=4 and  status_active =1 and is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group order by trim_group");
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

            <thead>
            <tr>
                <td colspan="15" align="">
                <table width="100%" style="table-layout: fixed;">
                <tr>
                <td width="60%" ><strong>Color & size sensitive (<? echo "Job NO: ".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]] ;echo "&nbsp;&nbsp;Int Ref.:&nbsp;".$ref_nos;if($file_nos!='' || $file_nos!=0 ) echo " &nbsp;File No.:&nbsp;".$file_nos;else " "; echo "&nbsp;&nbsp;Po Qty.:&nbsp;".$po_no_qty;?></strong></td>
                <td width="40%" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap; font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
                </tr>
                </table>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"  width="20"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group </strong> </td>
				<td style="border:1px solid black"  width="120"><strong>Item Image </strong> </td>


                <td style="border:1px solid black"><strong>Item Desc.</strong> </td>
                <td style="border:1px solid black"><strong>Brand/Supp. Ref.</strong> </td>
                <td style="border:1px solid black"><strong>Article No.</strong> </td>
                 <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <td style="border:1px solid black; width:170px;"><strong>Gmts Color</strong> </td>
                <td style="border:1px solid black;"><strong>Gmts Size</strong> </td>

                <td align="center" style="border:1px solid black"><strong>Item Size/EAN Code</strong></td>
                <td align="center" style="border:1px solid black"  width="50"><strong>Qty per Unit</strong></td>
                <td style="border:1px solid black" align="center"  width="80"><strong>WO Qty.</strong></td>
                <td style="border:1px solid black" align="center"  width="50"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"  width="50"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"  width="50"><strong>Amount</strong></td>
                 <? if($show_comment==1) {?>
                <td style="border:1px solid black" align="center"><strong>Remarks</strong></td>
                <? } ?>
            </tr>
            </thead>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=$grand_total_as_per_gmts_color_qty=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
				$gmtcolor_library=return_library_array( "select b.item_color,b.color_number_id from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.sensitivity=4    and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by trim_group ", "item_color", "color_number_id"  );

			 $nameArray_color=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid, b.color_number_id as gmt_color, b.item_color as color_number_id,b.item_size,b.gmts_sizes,b.description, b.brand_supplier,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount, min(c.color_order) as color_order, min(c.size_order) as size_order,c.article_number from wo_booking_dtls a, wo_trim_book_con_dtls b,wo_po_color_size_breakdown c  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and b.po_break_down_id=c.po_break_down_id and b.color_number_id=c.color_number_id and b.gmts_sizes=c.size_number_id and  c.id=b.color_size_table_id and a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and a.sensitivity=4 and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.color_number_id, b.item_color,b.item_size,b.gmts_sizes,b.description, b.brand_supplier,c.article_number order by c.article_number,color_order,size_order");



            $nameArray_item_description=sql_select( "select distinct uom from wo_booking_dtls  where booking_no='$txt_booking_no' and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=4 and trim_group=".$result_item[csf('trim_group')]." and pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and  status_active =1 and is_deleted=0 order by trim_group ");
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
				 <?
				$item_imge_arr=sql_select("select master_tble_id,image_location from   common_photo_library where form_name='pre_cost_trimsv2' and file_type=1 and master_tble_id='".$result_item[csf('pre_cost_fabric_cost_dtls_id')]."' ");
				?>
				 <p>
			<?	foreach($item_imge_arr as $row)
			   {
				 if($report_type==1)
                       {
                           if($link == 1)
                           {
                       ?>
                               <img  src='../../../<? echo $row[csf('image_location')]; ?>' height='35px' width='110px' />  &nbsp;

                       <?
                           }
                           else
                           {
                       ?>
                               <img  src='../../<? echo $row[csf('image_location')]; ?>' height='35px' width='110px' />&nbsp;
                       <?	}
                       }
                       else
                       { ?>
                           <img  src='../<? echo $row[csf('image_location')]; ?>' height='35px' width='110px' />&nbsp;
                      <? }

				  }
                       ?>
					   </p>
					   </td>

                <?
				$item_desctiption_total=0;$n=1;
                $total_amount_as_per_gmts_color=$total_qty_as_per_gmts_color=0;
				foreach($nameArray_color as $result_color)
                {
					?>

					<td style="border:1px solid black" ><? if($result_color[csf('description')]){echo $result_color[csf('description')];} ?> </td>
					<td style="border:1px solid black" ><? if($result_color[csf('brand_supplier')]){echo $result_color[csf('brand_supplier')]; }?> </td>
                    <td style="border:1px solid black">
					<?
					if($result_color[csf('article_number')]!="no article"){
						echo $result_color[csf('article_number')];
					}else{
						echo "-";
					}
					?>
                    </td>
                    <td style="border:1px solid black"><? echo $color_library[$result_color[csf('color_number_id')]]; ?> </td>
                    <td style="border:1px solid black"><? echo $color_library[$result_color[csf('gmt_color')]];?> </td>
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
					<td style="border:1px solid black; text-align:right">
					<?
					echo number_format($result_color[csf('rate')],4);
					?>
                     </td>
					<td style="border:1px solid black; text-align:right">
					<?
					$amount_as_per_gmts_color =$result_color[csf('amount')];
					echo number_format($amount_as_per_gmts_color,4);
					$total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
					$total_qty_as_per_gmts_color+=$result_color[csf('cons')];
					$trims_remark=$trims_remark_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]]['remark'];
					?>
					</td>
                     <? if($show_comment==1) {
					 if($n==1) {
					 ?>
                    <td style="border:1px solid black;text-align:center" rowspan="<? echo (count($nameArray_item_description)*count($nameArray_color)); ?>"><? echo $trims_remark; ?> </td>
                    <?
					 }
					} ?>
				</tr>
            <?
			$n++;
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="10"><strong> Item Total</strong></td>
				<td>&nbsp;  </td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold;"><?

				echo number_format($item_desctiption_total,4);  ?></td>
                <td style="border:1px solid black;  text-align:center"><? echo $uom_text; ?></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
				$grand_total_as_per_gmts_color_qty+=$total_qty_as_per_gmts_color;
                ?>
                </td>
                 <? if($show_comment==1) {?>
                <td>&nbsp;  </td>
                <? } ?>
            </tr>
            <?
            }
//grand_total_as_per_gmts_color_qty
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="11"><strong>Total</strong></td>
				<td align="right"><? echo number_format($grand_total_as_per_gmts_color_qty,2);?> </td>
                <td><? //echo number_format($grand_total_as_per_gmts_color_qty,2);?> </td>
                <td><? //echo number_format($grand_total_as_per_gmts_color_qty,2);?> </td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
                 <? if($show_comment==1) {?>
                <td>&nbsp;  </td>
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
		$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls  where booking_no='$txt_booking_no' and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=0 and  status_active =1 and is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group order by trim_group ");
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
        ?>
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
        	<thead>
            <tr>
                <td colspan="15" align="">
                <table width="100%" style="table-layout: fixed;">
                <tr>
                <td width="60%"><strong>NO sensitive  (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]];echo " &nbsp;Int Ref.:&nbsp;".$ref_nos; if($file_nos!='' || $file_nos!=0 ) echo " &nbsp;File No.:&nbsp;".$file_nos;else " "; echo " &nbsp;Po Qty.:&nbsp;".$po_no_qty; ?></strong></td>
                <td width="40%" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap; font-weight:bold;">Po No: <? echo implode(",",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
                </tr>
                </table>
                 </td>
            </tr>
            <tr>
                <td style="border:1px solid black"  width="20"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
				<td style="border:1px solid black"  width="120"><strong>Item Image</strong> </td>
                <td style="border:1px solid black"><strong>Item Desc.</strong> </td>
                <td style="border:1px solid black"><strong>Brand/Supp. Ref.</strong> </td>
                 <td style="border:1px solid black"><strong>Item Color</strong> </td>
                 <td align="center" style="border:1px solid black"  width="50"><strong>Qty per Unit</strong></td>
                <td align="center" style="border:1px solid black"  width="80"><strong> Qnty</strong></td>
                <td style="border:1px solid black" align="center"  width="50"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"  width="50"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"  width="80"><strong>Amount</strong></td>
                 <? if($show_comment==1) {?>
                 <td style="border:1px solid black" align="center"><strong>Remarks</strong></td>
                 <? } ?>
            </tr>
            </thead>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=$grand_total_as_per_gmts_color_qty=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;

				$nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_color,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and a.sensitivity=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_color");

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
 <?
				$item_imge_arr=sql_select("select master_tble_id,image_location from   common_photo_library where form_name='pre_cost_trimsv2' and file_type=1 and master_tble_id='".$result_item[csf('pre_cost_fabric_cost_dtls_id')]."' ");
				?>
				 <p>
			<?	foreach($item_imge_arr as $row)
			   {
				 if($report_type==1)
                       {
                           if($link == 1)
                           {
                       ?>
                               <img  src='../../../<? echo $row[csf('image_location')]; ?>' height='35px' width='110px' />  &nbsp;

                       <?
                           }
                           else
                           {
                       ?>
                               <img  src='../../<? echo $row[csf('image_location')]; ?>' height='35px' width='110px' />&nbsp;
                       <?	}
                       }
                       else
                       { ?>
                           <img  src='../<? echo $row[csf('image_location')]; ?>' height='35px' width='110px' />&nbsp;
                      <? }

				  }
                       ?>
					   </p>
                </td>
                <?
                $color_tatal=0;$p=1;
                $total_amount_as_per_gmts_color=$total_qty_as_per_gmts_color=0;$j=1;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $item_desctiption_total=0;
                ?>
                <td style="border:1px solid black"><? if($result_itemdescription[csf('description')]){ echo $result_itemdescription[csf('description')];} ?> </td>
                <td style="border:1px solid black"><? if($result_itemdescription[csf('brand_supplier')]){echo $result_itemdescription[csf('brand_supplier')];} ?> </td>
                <td style="border:1px solid black"><? echo $color_library[$result_itemdescription[csf('item_color')]]; ?> </td>
                <td style="border:1px solid black; text-align:left">
               <?
			   $trims_remark=$trims_remark_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]]['remark'];
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
						$nameArray_color_size_qnty=sql_select("select sum(b.cons) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trim_group=". $result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and  b.description='". $result_itemdescription[csf('description')]."' and b.brand_supplier='".$result_itemdescription[csf('brand_supplier')]."' and b.item_color='".$result_itemdescription[csf('item_color')]."'");
						}
						if($db_type==2)
				        {
						$nameArray_color_size_qnty=sql_select("select sum(b.cons) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trim_group=". $result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and nvl( b.description,0)=nvl('". $result_itemdescription[csf('description')]."',0) and nvl(b.brand_supplier,0)=nvl('".$result_itemdescription[csf('brand_supplier')]."',0) and nvl(b.item_color,0)=nvl('".$result_itemdescription[csf('item_color')]."',0)");
						}

                foreach($nameArray_color_size_qnty as $result_color_size_qnty)
                {
                ?>
                <td style="border:1px solid black; text-align:right">
                <?
				//$uom_arr=array(1,52,50,51,53,54,55,56,57,31,59,62,65,66,67,74,78,80);
				//print_r($uom_arr);
				$uom_id=$order_uom_arr[$result_item[csf('trim_group')]];
				//echo $order_uom_id.'DD';
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
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
                $amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
				 $total_qty_as_per_gmts_color+=$item_desctiption_total;
                ?>
                </td>
                 <? if($show_comment==1) {
				 	if($p==1)
				  	{
				 ?>
                 <td style="border:1px solid black; text-align:center" rowspan="<? echo count($nameArray_item_description); ?>"><? echo $trims_remark; ?> </td>
                 <?
				 	}
				 	} ?>
            </tr>
            <?
				$p++;
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="4"><strong> Item Total</strong></td>

                <td style="border:1px solid black;  text-align:right; font-weight:bold;">
                <?
                if($color_tatal !='')
                {

						 echo number_format($color_tatal,4);
                }
                ?>
                </td>
                <td style="border:1px solid black;  text-align:center"><? echo $uom_text; ?></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
				$grand_total_as_per_gmts_color_qty+=$total_qty_as_per_gmts_color;
                ?>
                </td>
                 <? if($show_comment==1) {?>
                <td>&nbsp;  </td>
                <? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="7"><strong>Total</strong></td>

                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color_qty,2); ?></td>
                <td align="right" style="border:1px solid black"><strong></strong></td>
				<td>&nbsp;  </td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
                 <? if($show_comment==1) {?>
                <td>&nbsp;  </td>
                <? } ?>
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
       <br/>
       <table width="100%" style="margin-top:1px">
       <tr>
       <td>
       <table width="100%" class="rpt_table"  border="1" cellpadding="0" cellspacing="0" rules="all">
            <tr style="border:1px solid black;">
                <td width="30%" style="border:1px solid black; text-align:left"><b>Total Booking Amount</b></td>
                <td width="70%" style="border:1px solid black; text-align:left"><b><? echo number_format($booking_grand_total,2);?></b></td>
            </tr>
            <tr style="border:1px solid black;">
                <td width="30%" style="border:1px solid black; text-align:left"><b>Total Booking Amount (in word)</b></td>
                <td width="70%" style="border:1px solid black;"><b><? echo number_to_words(def_number_format($booking_grand_total,2,""),$mcurrency, $dcurrency);?></b></td>
            </tr>
       </table>
       </td>
       </tr>
       </table>
         <br/>
        <table width="100%">
        <tr>
        <td width="49%">
        <?
        	echo get_spacial_instruction($txt_booking_no);
        ?>
    	</td>
    <td width="2%"></td>
    <?
	$lib_designation_arr=return_library_array(" select id,custom_designation from lib_designation","id","custom_designation");
	$user_lib_designation_arr=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
	$user_lib_name_arr=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");

	$mst_id=return_field_value("id as mst_id","wo_booking_mst","booking_no='$txt_booking_no'","mst_id");
	//echo $mst_id.'ssD';
	//and b.un_approved_date is null
	 $approve_data_array=sql_select("select b.approved_by,min(b.approved_date) as approved_date from   approval_history b where b.mst_id=$mst_id and b.entry_form=8  group by  b.approved_by order by b.approved_by asc");
	// echo "select b.approved_by,min(b.approved_date) as approved_date from   approval_history b where b.mst_id=$mst_id and b.entry_form=8  group by  b.approved_by order by b.approved_by asc";
	 $unapprove_data_array=sql_select("select b.approved_by,b.approved_date,b.un_approved_reason,b.un_approved_date,b.approved_no from   approval_history b where b.mst_id=$mst_id and b.entry_form=8  order by b.approved_date,b.approved_by");

	?>
    <td width="49%" valign="top">
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
			$sql_unapproved=sql_select("select booking_id,approval_cause from fabric_booking_approval_cause where  entry_form=8 and approval_type=2 and is_deleted=0 and status_active=1 and booking_id=$mst_id");
			$unapproved_request_arr=array();
			foreach($sql_unapproved as $rowu)
			{
			$unapproved_request_arr[$rowu[csf('booking_id')]]=$rowu[csf('approval_cause')];
			}
 		?>
       <table  width="850" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
            <tr style="border:1px solid black;">
                <th colspan="6" style="border:1px solid black;">Approval/Un Approval History</th>
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

   	 </tbody>
     </table>

    </div> <!--class="footer_signature"-->
   
   <div><? echo signature_table(132, $cbo_company_name, "1330px",1); ?></div>
    
<? 
	$emailBody=ob_get_contents();
	//ob_clean();
	if($is_mail_send==1){
		$req_approved=return_field_value("id", "ELECTRONIC_APPROVAL_SETUP", "PAGE_ID = 336 and is_deleted=0");
		$is_approved=return_field_value("IS_APPROVED", "WO_BOOKING_MST", "STATUS_ACTIVE = 1 and is_deleted=0 and booking_no='$txt_booking_no'");
		if($req_approved && $is_approved==1){
			$emailBody.="<h1 style='border:1px sloid #0ff;'>Approved</h1>";
		}
		elseif($req_approved && $is_approved==0){
			$emailBody.="<h1 style='border:1px sloid #0ff;'>Draft</h1>";
		}
		
		
		$mailArr=array();
		if($mail_id!=''){$mailArr[]=$mail_id;}
		if($supplier_mail_arr[$supplier_id]!=''){$mailArr[]=$supplier_mail_arr[$supplier_id];}
		
		
		$to=implode(',',$mailArr);
		$subject="Trims Booking Auto Mail";
		
		if($to!=""){
			require '../../../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
			require_once('../../../auto_mail/setting/mail_setting.php');
			$header=mailHeader();
			sendMailMailer( $to, $subject, $emailBody );
		}
	}
?>
    
        
        <br>
      <div id="page_break_div"></div>
     <br>
    <div>

		<?
        	//echo signature_table(2, $cbo_company_name, "1330px");
			echo "****".custom_file_name($txt_booking_no,$style_sting,$job_no);
        ?>
    </div>

<?
    if($link == 1){
        ?>
        <script type="text/javascript" src="../../../js/jquery.js"></script>
        <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
    <?
    }else {
        ?>
         <script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
        <?
    }
        ?>
    <script>
    fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');

    </script>
  </html>
<?
exit();
} //End

if($action=="download_file")
{
	extract($_REQUEST);
	set_time_limit(0);
	$file_path=$_REQUEST['filename'];
	download_start($file_path, ''.$_REQUEST['filename'].'', 'text/plain');
}

if ($action=="unapp_request_popup"){
	$menu_id=$_SESSION['menu_id'];
	$user_id=$_SESSION['logic_erp']['user_id'];

	echo load_html_head_contents("Un Approval Request","../../../", 1, 1, $unicode);
	extract($_REQUEST);

	$data_all=explode('_',$data);
	$booking_no=$data_all[0];
	$unapp_request=$data_all[1];

	$wo_id=return_field_value("id", "wo_booking_mst", "booking_no='$booking_no' and status_active=1 and is_deleted=0");
	if($unapp_request=="")
	{
		 $sql_request="select MAX(id) as id from fabric_booking_approval_cause where page_id='$menu_id' and entry_form=8 and user_id='$user_id' and booking_id='$wo_id' and approval_type=2 and status_active=1 and is_deleted=0";


		$nameArray_request=sql_select($sql_request);
		foreach($nameArray_request as $row)
		{
			$unapp_request=return_field_value("approval_cause", "fabric_booking_approval_cause", "id='".$row[csf('id')]."' and status_active=1 and is_deleted=0");
		}
	}
	?>
    <script>

		$( document ).ready(function() {
			document.getElementById("unappv_request").value='<? echo $unapp_request; ?>';
		});

		var permission='<? echo $permission; ?>';

		function fnc_appv_entry(operation)
		{
			var unappv_request = $('#unappv_request').val();

			if (form_validation('unappv_request','Un Approval Request')==false)
			{
				if (unappv_request=='')
				{
					alert("Please write request.");
				}
				return;
			}
			else
			{

				var data="action=save_update_delete_unappv_request&operation="+operation+get_submitted_data_string('unappv_request*wo_id*page_id*user_id',"../../../");
				//alert (data);return;
				freeze_window(operation);
				http.open("POST","trims_booking_multi_job_controllerurmi.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange=fnc_appv_entry_Reply_info;
			}
		}

		function fnc_appv_entry_Reply_info()
		{
			if(http.readyState == 4)
			{
				var reponse=trim(http.responseText).split('**');
				show_msg(reponse[0]);

				set_button_status(1, permission, 'fnc_appv_entry',1);
				release_freezing();

			}
		}

		function fnc_close()
		{
			unappv_request= $("#unappv_request").val();

			document.getElementById('hidden_appv_cause').value=unappv_request;

			parent.emailwindow.hide();
		}

    </script>
    <body>
		<div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../../",$permission,1); ?>
        <form name="size_1" id="size_1">
			<fieldset style="width:450px;">
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="size_tbl" >
                <tr id="row_1">
                    <td width="150" align="center" >
                    	<textarea name="unappv_request" id="unappv_request" class="text_area" style="width:430px; height:100px;" maxlength="500" title="Maximum 500 Character"></textarea>
                        <Input type="hidden" name="wo_id" class="text_boxes" ID="wo_id" value="<? echo $wo_id; ?>" style="width:30px" />
                        <Input type="hidden" name="page_id" class="text_boxes" ID="page_id" value="<? echo $menu_id; ?>" style="width:30px" />
                        <Input type="hidden" name="user_id" class="text_boxes" ID="user_id" value="<? echo $user_id; ?>" style="width:30px" />
                    </td>
                </tr>
            </table>

            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="" >

                <tr>
                    <td align="center" class="button_container">
                        <?
                            if($id_up!='')
                            {
                                echo load_submit_buttons($permission, "fnc_appv_entry", 1,0,"reset_form('size_1','','','','','');",1);
                            }
                            else
                            {
                                echo load_submit_buttons($permission, "fnc_appv_entry", 0,0,"reset_form('size_1','','','','','');",1);
                            }
                        ?>
                        <input type="hidden" name="hidden_appv_cause" id="hidden_appv_cause" class="text_boxes /">

                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                    </td>
                </tr>
            </table>
            </fieldset>
            </form>
        </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if ($action=="save_update_delete_unappv_request"){

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$approved_no=return_field_value("MAX(approved_no)","approval_history","entry_form=8 and mst_id=$wo_id");

		$unapproved_request=return_field_value("id","fabric_booking_approval_cause","page_id=$page_id and entry_form=8 and user_id=$user_id and booking_id=$wo_id and approval_type=2 and approval_no=$approved_no");

		if($unapproved_request=="")
		{

			$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

			$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
			$data_array="(".$id_mst.",".$page_id.",8,".$user_id.",".$wo_id." ,2,".$approved_no.",".$unappv_request.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,0);

			if($db_type==0)
			{
				if($rID )
				{
					mysql_query("COMMIT");
					echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$unappv_request)."**".str_replace("'","",$user_id);
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".$rID;
				}
			}
			if($db_type==2)
			{
				if($rID )
				{
					oci_commit($con);
					echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$unappv_request)."**".str_replace("'","",$user_id);
				}
				else
				{
					oci_rollback($con);
					echo "10**".$rID;
				}
			}
			if($db_type==1 )
			{

				echo "0**".$rID."**".$wo_id;
			}
			disconnect($con);
			die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}

			$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
			$data_array="".$page_id."*8*".$user_id."*".$wo_id."*2*".$approved_no."*".$unappv_request."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

			 $rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id","".$unapproved_request."",0);

			if($db_type==0)
			{
				if($rID )
				{
					mysql_query("COMMIT");
					echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$unappv_request)."**".str_replace("'","",$user_id);
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".$rID;
				}
			}

			if($db_type==2)
			{
				if($rID )
				{
					oci_commit($con);
					echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$unappv_request)."**".str_replace("'","",$user_id);
				}
				else
				{
					oci_rollback($con);
					echo "10**".$rID;
				}
			}
			if($db_type==1 )
			{
				echo "1**".$rID."**".str_replace("'","",$wo_id);
			}
			disconnect($con);
			die;
		}
	}
	if ($operation==1)  // Update Here
	{

	}
}

if ($action=="get_first_selected_print_report"){
	
	list($company_id,$mail_id)=explode('**',$data);
	
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$company_id."' and module_id=2 and report_id=219 and is_deleted=0 and status_active=1");
	$print_report_format_arr=explode(',',$print_report_format);
	$button_id=$print_report_format_arr[0];
		if($button_id==183){
			echo "generate_trim_report('show_trim_booking_report2',1,'".$mail_id."',1);";
		}
        elseif($button_id==67){
            echo "generate_trim_report('show_trim_booking_report',1,'".$mail_id."',1);";
        }
        elseif($button_id==177){
            echo "generate_trim_report('show_trim_booking_report4',1,'".$mail_id."',1);";
        }
		elseif($button_id==235){
            echo "generate_trim_report('show_trim_booking_report9',1,'".$mail_id."',1);";
        }
		elseif($button_id==774){
            echo "generate_trim_report('show_trim_booking_report_wg',1,'".$mail_id."',1);";
        }
		elseif($button_id==85){
            echo "generate_trim_report('print_t',1,'".$mail_id."',1);";
        }
		elseif($button_id==746){
            echo "generate_trim_report('print_t7',1,'".$mail_id."',1);";
        }
	exit();
}

if($action=="labeldtls_popup")
{
	echo load_html_head_contents("Label Details Pop Up","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $zipperdtls;
	?>
	<script>
		function js_set_value()
		{
			var numRow = $('table#tblzipper tbody tr').length;
			var label_break_data="";
			
			for(var i=1; i<=numRow; i++)
			{
				label_break_data=$('#txtfabrication'+i).val()+'$!'+$('#txtcaresymbol'+i).val()+'$!'+$('#txtoekotexno'+i).val();
			}
			document.getElementById('hidd_dtlsdata').value=label_break_data;
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
    <div align="center">
        <form>
            <input type="hidden" id="hidd_dtlsdata" name="hidd_dtlsdata" />
            <table width="400" class="rpt_table" border="1" rules="all" cellpadding="0" cellspacing="0" id="tbllabel">
                <thead>
                    <tr>
                        <th width="130">Fabrication</th>
                        <th width="130">Care Symbol</th>
                        <th>Oekotex No.</th>
                    </tr>
                </thead>
                <tbody>
					<? $exlabeldtls=explode("$!$",$labeldtls);
                    ?>
                    <tr style="text-decoration:none; cursor:pointer"> 
                        <td align="center"><input type="text" name="txtfabrication" id="txtfabrication" value="<?=$exlabeldtls[0]; ?>" style="width:118px;" class="text_boxes" placeholder="Write"/></td>
                        <td style="word-break:break-all" align="center"><input type="text" name="txtcaresymbol" id="txtcaresymbol" value="<?=$exlabeldtls[1]; ?>" style="width:118px;" class="text_boxes" placeholder="Write"/></td>
                        <td style="word-break:break-all" align="center"><input type="text" name="txtoekotexno" id="txtoekotexno" value="<?=$exlabeldtls[2]; ?>" style="width:118px;" class="text_boxes" placeholder="Write"/></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td align="center" class="button_container" colspan="3"><input type="button" class="formbutton" value="Close" onClick="js_set_value();"/> </td>
                    </tr>
                </tfoot>
            </table>
        </form>
    </div>
    </body>
    <?
	exit();
}


?>