<?
/*-------------------------------------------- Comments
Version (MySql)          :  V2
Version (Oracle)         :  V1
Converted by             :  Aziz
Purpose			         :  This form will create Trims Booking Multi Job Wise
Functionality	         :
JS Functions	         :
Created by		         :  Aziz
Creation date 	         :  6-12-2018
Requirment Client        :  Sonia
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
include('../../../../includes/common.php');
include('../../../../includes/class4/class.conditions.php');
include('../../../../includes/class4/class.reports.php');
include('../../../../includes/class4/class.trims.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');
//---------------------------------------------------- Start---------------------------------------------------------------------------
function load_drop_down_supplier($data){
	$data=explode("_",$data);
	$pay_mode_id=$data[0];
	$tag_buyer_id=$data[1];
	if($pay_mode_id==5 || $pay_mode_id==3){
	   echo create_drop_down( "cbo_supplier_name", 172, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "-- Select Company --", "", "get_php_form_data( this.value, 'load_drop_down_attention', 'requires/trims_booking_multi_job_controllerurmi');",0,"" );
	}
	else
	{

	$tag_buyer=return_field_value("tag_buyer as tag_buyer", "lib_supplier_tag_buyer", "tag_buyer=$tag_buyer_id","tag_buyer");
	//echo $tag_buyer.'AAA';
	if($tag_buyer!='')
	{
		$tag_buy_supp="select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c,lib_supplier_tag_buyer d where c.id=b.supplier_id and a.supplier_id = b.supplier_id and d.supplier_id=c.id and d.supplier_id=a.supplier_id  and d.supplier_id=b.supplier_id and b.party_type=4 and c.status_active=1 and c.is_deleted=0  and d.tag_buyer=$tag_buyer group by c.id, c.supplier_name order by c.supplier_name";
	}
	else
	{
		$tag_buy_supp="select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and b.party_type=4 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name";
	}

	$cbo_supplier_name= create_drop_down( "cbo_supplier_name", 172, $tag_buy_supp,"id,supplier_name", 1, "--Select Supplier--",$selected,"get_php_form_data( this.value, 'load_drop_down_attention', 'requires/trims_booking_multi_job_controllerurmi');","");
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
	$cbo_buyer_name= create_drop_down( "cbo_buyer_name", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected,"check_paymode(this.value);","");

	echo "document.getElementById('supplier_td').innerHTML = '".$cbo_supplier_name."';\n";
	echo "document.getElementById('buyer_td').innerHTML = '".$cbo_buyer_name."';\n";
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=2 and report_id in(26) and is_deleted=0 and status_active=1");
	echo "document.getElementById('report_ids').value = '".$print_report_format."';\n";
	//echo "print_report_button_setting('".$print_report_format."');\n";
	$print_report_format_arr=explode(",",$print_report_format);
	echo "$('#print_booking1').hide();\n";
	echo "$('#print_booking2').hide();\n";
	echo "$('#print_booking4').hide();\n";

	foreach($print_report_format_arr as $id){
		if($id==67){echo "$('#print_booking1').show();\n";}
		if($id==183){echo "$('#print_booking2').show();\n";}
		if($id==209){echo "$('#print_booking4').show();\n";}

	}


	exit();
}

if ($action=="load_drop_down_buyer"){
	echo create_drop_down( "cbo_buyer_name", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "check_paymode(this.value);","" );
	exit();
}

if ($action=="load_drop_down_buyer_pop"){
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","" );
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
	echo load_html_head_contents("Booking Search","../../../../", 1, 1, $unicode);
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
								select_row=i;
								sp=1;
							}
							else
							{
								select_row+=','+i;
								sp=2;
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
								<th colspan="7" align="center"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
						</tr>
                        <tr>
                            <th width="100">Style Ref </th>
                            <th width="100">Job No </th>
                            <th width="60">Year</th>
                            <th width="100">Int. Ref. No </th>
                            <th width="100">Order No</th>
                            <th width="120">Item Name</th>
                            <th>&nbsp;
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
                        <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:90px"></td>
                        <td><input name="txt_job" id="txt_job" class="text_boxes" style="width:90px"></td>
                        <td><? echo create_drop_down( "cbo_year_selection", 60, $year,"", 1, "-- Select --", date('Y'), "",0 );	?></td>
                        <td><input name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:100px"></td>
                        <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:90px"></td>
                        <td><? echo create_drop_down( "cbo_item", 120, "select a.id,a.item_name from  lib_item_group a where  a.status_active =1 and a.is_deleted=0 and a.item_category=4 order by a.item_name","id,item_name", 1, "-- Select Item Name --", $selected, "",0 ); ?></td>
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('cbo_booking_month').value+'_'+document.getElementById('cbo_booking_year').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_currency').value+'_'+document.getElementById('cbo_currency_job').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_job').value+'_'+document.getElementById('cbo_item').value+'_'+document.getElementById('txt_ref_no').value+'_'+'<? echo $txt_booking_no; ?>'+'_'+'<? echo $cbo_level; ?>'+'_100', 'create_fnc_process_data', 'search_div', 'trims_booking_multi_job_controllerurmi','setFilterGrid(\'tbl_list_search\',-1)')" style="width:60px;" />
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

if ($action=="create_fnc_process_data"){
	//echo load_html_head_contents("Booking Search","../../../../", 1, 1, $unicode);
	//echo $data[6];die;
	/* $data=explode('_',$data);
	echo '<pre>'; */
	//print_r($data);
	$data=explode('_',$data);

	if($data[11]=="" && $data[10]==""  && $data[9]=="" && $data[13]=="" && $data[12]==0)
	{
		//echo "<div style='color:red; font-size:larger' align='center'><b>Please write anyone data in search fields ->Style Ref/Job No/PO/Ref No/Trim Group</b><div>";die;
	}
	$job_cond=""; $order_cond=""; $style_cond="";
	if($data[6]==1)
	{
		if (str_replace("'","",$data[11])!="") $job_cond=" and a.job_no_prefix_num='$data[11]'  $booking_year_cond"; //else  $job_cond="";
		if (str_replace("'","",$data[10])!="") $order_cond=" and d.po_number = '$data[10]'  "; //else  $order_cond="";
		if (trim($data[9])!="") $style_cond=" and a.style_ref_no ='$data[9]'"; //else  $style_cond="";
	}
	else if($data[6]==2)
	{
		if (str_replace("'","",$data[11])!="") $job_cond=" and a.job_no_prefix_num like '$data[11]%'  $booking_year_cond"; //else  $job_cond="";
		if (str_replace("'","",$data[10])!="") $order_cond=" and b.po_number like '$data[10]%'  "; //else  $order_cond="";
		if (trim($data[9])!="") $style_cond=" and a.style_ref_no like '$data[9]%'  "; //else  $style_cond="";
	}
	else if($data[6]==3)
	{
		if (str_replace("'","",$data[11])!="") $job_cond=" and a.job_no_prefix_num like '%$data[11]'  $booking_year_cond"; //else  $job_cond="";
		if (str_replace("'","",$data[10])!="") $order_cond=" and d.po_number like '%$data[10]'  "; //else  $order_cond="";
		if (trim($data[9])!="") $style_cond=" and a.style_ref_no like '%$data[9]'"; //else  $style_cond="";
	}
	else if($data[6]==4 || $data[6]==0)
	{
		if (str_replace("'","",$data[11])!="") $job_cond=" and a.job_no_prefix_num like '%$data[11]%'  $booking_year_cond"; //else  $job_cond="";
		if (str_replace("'","",$data[10])!="") $order_cond=" and d.po_number like  '%$data[10]%'  "; //else  $order_cond="";
		if (trim($data[9])!="") $style_cond=" and a.style_ref_no like '%$data[9]%'"; //else  $style_cond="";
	}

	$company_id=$data[0];
	$cbo_buyer_name=$data[1];
	$cbo_supplier_name=$data[2];
	$cbo_booking_month=$data[3];

	$cbo_booking_year=$data[4];
	$cbo_year_selection=$data[5];
	$cbo_currency=$data[7];
	$cbo_currency_job=$data[8];
	$search_type=$data[6];
	$txt_style=$data[9];
	$txt_order_search=$data[10];
	$txt_job=$data[11];
	$cbo_item=$data[12];
	$ref_no=$data[13];
	$booking_no=$data[14];
	$cbo_level=$data[15];
	$gmts_nature=$data[16];
	if($db_type==0) $booking_year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$cbo_year_selection";
	if($db_type==2) $booking_year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year_selection";
	if ($ref_no!="") $ref_cond=" and d.grouping='$ref_no'"; else $ref_cond="";
	if ($cbo_item!=0) $itemgroup_cond=" and c.trim_group=$cbo_item"; else $itemgroup_cond ="";
	if ($gmts_nature!=0) $gmts_nature_cond=" and b.garments_nature=$gmts_nature"; else $gmts_nature_cond ="";
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
	</head>
	<body>
	<div style="width:1220px;">
	<?
	extract($_REQUEST);
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
        if($db_type==0){
            if ($start_date!="" &&  $end_date!="") $class_datecond = "between '".change_date_format($start_date, "yyyy-mm-dd", "-")."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'"; else $class_datecond ="";

            $year_field="YEAR(a.insert_date) as year";
        }
        else if($db_type==2){
            if ($start_date!="" &&  $end_date!="") $class_datecond = "between '".change_date_format($start_date, "yyyy-mm-dd", "-",1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-",1)."'"; else $class_datecond ="";
            $year_field="to_char(a.insert_date,'YYYY') as year";
        }
        //echo $start_date.'='.$end_date;
        $sql_lib_item_group_array=array();
        $sql_lib_item_group=sql_select("select id, item_name, conversion_factor, order_uom as cons_uom from lib_item_group");
        foreach($sql_lib_item_group as $row_sql_lib_item_group){
            $sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][item_name]=$row_sql_lib_item_group[csf('item_name')];
            $sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][conversion_factor]=$row_sql_lib_item_group[csf('conversion_factor')];
            $sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][cons_uom]=$row_sql_lib_item_group[csf('cons_uom')];
        }
        unset($sql_lib_item_group);

	    $exceed_qty_level=return_field_value("exceed_qty_level", "variable_order_tracking", "company_name=$company_id  and variable_list=26 and status_active=1 and is_deleted=0");
       	if($exceed_qty_level==0 || $exceed_qty_level=="")  $exceed_qty_level=2;else $exceed_qty_level=$exceed_qty_level;


		/*  echo '<pre>';
		print_r($cu_booking_arr);die;*/
         $sql="select a.job_no_prefix_num, a.job_no, $year_field, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id as wo_pre_cost_trim_cost_dtls, c.trim_group, c.description, c.brand_sup_ref, c.rate, d.id as po_id, d.po_number, d.file_no, d.grouping, d.po_quantity as plan_cut, min(e.id) as id, e.po_break_down_id, avg(e.cons) AS cons
        from wo_po_details_master a, wo_pre_cost_mst b, wo_pre_cost_trim_cost_dtls c, wo_po_break_down d, wo_pre_cost_trim_co_cons_dtls e

        where a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and c.id=e.wo_pre_cost_trim_cost_dtls_id and d.id=e.po_break_down_id and a.company_name=$company_id and
        a.buyer_name=$cbo_buyer_name and (c.nominated_supp = $cbo_supplier_name or c.nominated_supp= 0) and b.approved=1 and d.is_deleted=0 and d.status_active=1
        ".$buyer_cond_test." $itemgroup_cond $job_cond $order_cond $ref_cond $style_cond $booking_year_cond $gmts_nature_cond
        group by
        a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, b.exchange_rate, c.id, c.trim_group, c.description, c.brand_sup_ref, c.rate, a.insert_date, d.id, d.po_number, d.file_no, d.grouping, d.po_quantity, e.po_break_down_id order by d.id, c.id DESC";

        $i=1; $total_req=0; $total_amount=0;
       //echo $sql;
        $nameArray=sql_select( $sql );
		foreach ($nameArray as $selectResult)
        {
			$po_idArr[$selectResult[csf('po_id')]]=$selectResult[csf('po_id')];
			$style_idArr[$selectResult[csf('style_ref_no')]]=$selectResult[csf('style_ref_no')];
			$wo_pre_trim_dtlsArr[$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]=$selectResult[csf('wo_pre_cost_trim_cost_dtls')];
		}
		$previ_po_cond_for_in=where_con_using_array($po_idArr,0,"c.po_break_down_id");
		if(count($nameArray)<=0)
		{
			echo "<div style='color:red; font-size:larger' align='center'><b>NO data found please try again</b><div>";die;
		}
		$cu_booking_arr=array();

		$sql_cu_booking=sql_select("select c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id, sum(c.wo_qnty) as cu_wo_qnty, sum(c.amount) as cu_amount from wo_po_details_master a, wo_po_break_down  d, wo_booking_mst b,wo_booking_dtls c where a.job_no=d.job_no_mst and a.job_no=c.job_no and b.booking_no=c.booking_no and  d.id=c.po_break_down_id and a.company_name=$company_id and a.buyer_name=$cbo_buyer_name  and b.booking_type=2  and c.booking_type=2 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.supplier_id=$cbo_supplier_name $previ_po_cond_for_in $job_cond $order_cond $ref_cond $style_cond $booking_year_cond  group by a.job_no, c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id");
        foreach($sql_cu_booking as $row_cu_booking){
            $cu_booking_arr[$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_cu_booking[csf('po_break_down_id')]]['cu_wo_qnty']=$row_cu_booking[csf('cu_wo_qnty')];
            $cu_booking_arr[$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_cu_booking[csf('po_break_down_id')]]['cu_amount']=$row_cu_booking[csf('cu_amount')];
        }
         unset($sql_cu_booking);

		$condition= new condition();
		if($search_type==1)
		{
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
			if(str_replace("'","",$txt_style)!='')
			{
				$condition->style_ref_no("='$txt_style'");
			}
		}
		else if($search_type==2)
		{
			if(str_replace("'","",$company_id) !=''){
				$condition->company_name("=$company_id");
			}
			if(str_replace("'","",$cbo_buyer_name) !=''){
				$condition->buyer_name("=$cbo_buyer_name");
			}
			if(str_replace("'","",trim($ref_no))!=''){
				$inter_ref=str_replace("'","",trim($ref_no));
				$condition->style_ref_no("like '$inter_ref%'");
			}
			if(str_replace("'","",trim($txt_style))!=''){
				$style_ref=str_replace("'","",trim($txt_style));
				$condition->style_ref_no("like '$style_ref%'");
			}
			if(str_replace("'","",$txt_order_search)!='')
			{
				$order_nos=str_replace("'","",$txt_order_search);
				$condition->po_number(" like '$order_nos%'");
			}
			if(str_replace("'","",$txt_job) !=''){
				$condition->job_no_prefix_num(" like '$txt_job%'");
			}
		}
		else if($search_type==3)
		{
			if(str_replace("'","",$company_id) !=''){
				$condition->company_name("=$company_id");
			}
			if(str_replace("'","",$cbo_buyer_name) !=''){
				$condition->buyer_name("=$cbo_buyer_name");
			}
			if(str_replace("'","",trim($ref_no))!=''){
				$inter_ref=str_replace("'","",trim($ref_no));
				$condition->style_ref_no("like '%$inter_ref'");
			}
			if(str_replace("'","",trim($txt_style))!=''){
				$style_ref=str_replace("'","",trim($txt_style));
				$condition->style_ref_no("like '%$style_ref'");
			}
			if(str_replace("'","",$txt_order_search)!='')
			{
				$order_nos=str_replace("'","",$txt_order_search);
				$condition->po_number(" like '%$order_nos'");
			}
			if(str_replace("'","",$txt_job) !=''){
				$condition->job_no_prefix_num(" like '%$txt_job'");
			}
		}
		else if($search_type==4 || $search_type==0)
		{
			if(str_replace("'","",$company_id) !=''){
				$condition->company_name("=$company_id");
			}
			if(str_replace("'","",$cbo_buyer_name) !=''){
				$condition->buyer_name("=$cbo_buyer_name");
			}
			if(str_replace("'","",trim($ref_no))!=''){
				$inter_ref=str_replace("'","",trim($ref_no));
				$condition->style_ref_no("like '%$inter_ref%'");
			}
			if(str_replace("'","",trim($txt_style))!=''){
				$style_ref=str_replace("'","",trim($txt_style));
				$condition->style_ref_no("like '%$style_ref%'");
			}
			if(str_replace("'","",$txt_order_search)!='')
			{
				$order_nos=str_replace("'","",$txt_order_search);
				$condition->po_number(" like '%$order_nos%'");
			}
			if(str_replace("'","",$txt_job) !=''){
				$condition->job_no_prefix_num(" like '%$txt_job%'");
			}
			$poids=implode(",",$po_idArr);
			if($poids!='')
			{
				//echo $poids;die;
				$condition->po_id_in($poids);
			}
		}

        $condition->init();
        $trims= new trims($condition);
		//echo $trims->getQuery(); die;
        $req_qty_arr=$trims->getQtyArray_by_orderAndPrecostdtlsid();
        $req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsid();

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
				 //echo $req_qnty_cons_uom.'='.$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor].'='.$exceed_qty_level.'<br>';
				$req_qnty=def_number_format($req_qnty_cons_uom/$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor],5,"");
				$cu_wo_qnty=$cu_booking_arr[$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('po_id')]]['cu_wo_qnty'];
				$cu_wo_amnt=$cu_booking_arr[$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('po_id')]]['cu_amount'];
				//if($selectResult[csf('job_no_prefix_num')]==513)
					//echo $bal_woq.'='.$cu_wo_qnty.'='.$req_qnty.'<br>';
				$bal_woq=def_number_format($req_qnty-$cu_wo_qnty,5,"");

				$rate=def_number_format(($rate_cons_uom*$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor])*$exchange_rate,5,"");
				$req_amount=def_number_format($req_qnty*$rate,5,"");

				$bal_wom=def_number_format($req_amount-$cu_wo_amnt,5,"");

				$total_req_amount+=$req_amount;
				$total_cu_amount+=$selectResult[csf('cu_amount')];

				$total_req+=$req_qnty;
				$amount=def_number_format($rate*$bal_woq,4,"");
				//if($selectResult[csf('job_no_prefix_num')]==513)
					//echo $bal_woq.'='.$cu_wo_qnty.'='.$exceed_qty_level.'<br>';
				if($bal_woq>0 && ($cu_wo_qnty=="" || $cu_wo_qnty==0) && $exceed_qty_level==2)
				{
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
						<td width="20"><? echo $i;?>
							<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $selectResult[csf('id')]; ?>"/>
							<input type="hidden" name="txt_trim_group_id" id="txt_trim_group_id<?php echo $i ?>" value="<? echo $selectResult[csf('wo_pre_cost_trim_cost_dtls')]; ?>"/>
							<input type="hidden" name="txt_job_no" id="txt_job_no<?php echo $i ?>" value="<? echo $selectResult[csf('job_no')]; ?>"/>
							<input type="hidden" name="txt_po_id" id="txt_po_id<?php echo $i ?>" value="<? echo $selectResult[csf('po_id')]; ?>"/>
                            <input type="hidden" name="hiddtrim_group" id="hiddtrim_group<?php echo $i ?>" value="<? echo $selectResult[csf('trim_group')]; ?>"/>
						</td>
						<td width="50"><p><? echo $buyer_arr [$selectResult[csf('buyer_name')]];?></p></td>
						<td width="50"><p><? echo $selectResult[csf('year')];?></p></td>
						<td width="50"><p><? echo $selectResult[csf('job_no_prefix_num')];?></p></td>
						<td width="60"><p><? echo $selectResult[csf('file_no')];?></p></td>
						<td width="60"><p><? echo $selectResult[csf('grouping')];?></p></td>
						<td width="100"><div style="width:100px; word-wrap:break-word;"><? echo $selectResult[csf('style_ref_no')];?></div></td>
						<td width="100"><p><? echo $selectResult[csf('po_number')];?></p></td>
						<td width="100"><div style="width:100px; word-wrap:break-word;"><? echo $trim_group_library[$selectResult[csf('trim_group')]];?></div></td>
						<td width="130" id="td_item_des<?php echo $i; ?>"><div style="width:130px; word-wrap:break-word;"><? echo $selectResult[csf('description')];?></div></td>
						<td width="70"><div style="width:70px; word-wrap:break-word;"><? echo $selectResult[csf('brand_sup_ref')];?></div></td>
						<td width="70" align="right"><? echo number_format($req_qnty,4); ?></td>
						<td width="45"><? echo $unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom]];?></td>
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
				elseif($bal_woq>=1 && $cu_wo_qnty>0)
				{
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
						<td width="20"><? echo $i;?>
							<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $selectResult[csf('id')]; ?>"/>
							<input type="hidden" name="txt_trim_group_id" id="txt_trim_group_id<?php echo $i ?>" value="<? echo $selectResult[csf('wo_pre_cost_trim_cost_dtls')]; ?>"/>
							<input type="hidden" name="txt_job_no" id="txt_job_no<?php echo $i ?>" value="<? echo $selectResult[csf('job_no')]; ?>"/>
							<input type="hidden" name="txt_po_id" id="txt_po_id<?php echo $i ?>" value="<? echo $selectResult[csf('po_id')]; ?>"/>
                            <input type="hidden" name="hiddtrim_group" id="hiddtrim_group<?php echo $i ?>" value="<? echo $selectResult[csf('trim_group')]; ?>"/>
						</td>
						<td width="50"><p><? echo $buyer_arr [$selectResult[csf('buyer_name')]];?></p></td>
						<td width="50"><p><? echo $selectResult[csf('year')];?></p></td>
						<td width="50"><p><? echo $selectResult[csf('job_no_prefix_num')];?></p></td>
						<td width="60"><p><? echo $selectResult[csf('file_no')];?></p></td>
						<td width="60"><p><? echo $selectResult[csf('grouping')];?></p></td>
						<td width="100"><div style="width:100px; word-wrap:break-word;"><? echo $selectResult[csf('style_ref_no')];?></div></td>
						<td width="100"><p><? echo $selectResult[csf('po_number')];?></p></td>
						<td width="100"><div style="width:100px; word-wrap:break-word;"><? echo $trim_group_library[$selectResult[csf('trim_group')]];?></div></td>
						<td width="130"><div style="width:130px; word-wrap:break-word;"><? echo $selectResult[csf('description')];?></div></td>
						<td width="70"><div style="width:70px; word-wrap:break-word;"><? echo $selectResult[csf('brand_sup_ref')];?></div></td>
						<td width="70" align="right"><? echo number_format($req_qnty,4); ?></td>
						<td width="45"><? echo $unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom]];?></td>
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
				elseif($bal_wom>0  && $exceed_qty_level==1)
				{
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
						<td width="20"><? echo $i;?>
							<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $selectResult[csf('id')]; ?>"/>
							<input type="hidden" name="txt_trim_group_id" id="txt_trim_group_id<?php echo $i ?>" value="<? echo $selectResult[csf('wo_pre_cost_trim_cost_dtls')]; ?>"/>
							<input type="hidden" name="txt_job_no" id="txt_job_no<?php echo $i ?>" value="<? echo $selectResult[csf('job_no')]; ?>"/>
							<input type="hidden" name="txt_po_id" id="txt_po_id<?php echo $i ?>" value="<? echo $selectResult[csf('po_id')]; ?>"/>
                            <input type="hidden" name="hiddtrim_group" id="hiddtrim_group<?php echo $i ?>" value="<? echo $selectResult[csf('trim_group')]; ?>"/>
						</td>
						<td width="50"><p><? echo $buyer_arr [$selectResult[csf('buyer_name')]];?></p></td>
						<td width="50"><p><? echo $selectResult[csf('year')];?></p></td>
						<td width="50"><p><? echo $selectResult[csf('job_no_prefix_num')];?></p></td>
						<td width="60"><p><? echo $selectResult[csf('file_no')];?></p></td>
						<td width="60"><p><? echo $selectResult[csf('grouping')];?></p></td>
						<td width="100"><div style="width:100px; word-wrap:break-word;"><? echo $selectResult[csf('style_ref_no')];?></div></td>
						<td width="100"><p><? echo $selectResult[csf('po_number')];?></p></td>
						<td width="100"><div style="width:100px; word-wrap:break-word;"><? echo $trim_group_library[$selectResult[csf('trim_group')]];?></div></td>
						<td width="130" id="td_item_des<?php echo $i; ?>"><div style="width:130px; word-wrap:break-word;"><? echo $selectResult[csf('description')];?></div></td>
						<td width="70"><div style="width:70px; word-wrap:break-word;"><? echo $selectResult[csf('brand_sup_ref')];?></div></td>
						<td width="70" align="right"><? echo number_format($req_qnty,4); ?></td>
						<td width="45"><? echo $unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom]];?></td>
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
			//}
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
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="generate_fabric_booking"){

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
	$exchange_rate_conversion = set_conversion_rate($cbo_currency, $txt_booking_date);//Conversion Exchance From Lib

	$condition= new condition();
	if(str_replace("'","",$data) !=''){
		$condition->po_id("in($data)");
	}

	$condition->init();
	$trims= new trims($condition);
	$req_qty_arr=$trims->getQtyArray_by_orderAndPrecostdtlsid();
	$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsid();
	$reqAmountJobLevelArr=$trims->getAmountArray_by_job();
	$cu_booking_arr=array();

	$sql_cu_booking=sql_select("select c.job_no,c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id, sum(c.wo_qnty) as cu_wo_qnty, sum(c.amount) as cu_amount from wo_po_details_master a, wo_po_break_down  d , wo_booking_dtls c,wo_booking_mst e  where a.job_no=d.job_no_mst and a.job_no=c.job_no and e.booking_no=c.booking_no and  d.id=c.po_break_down_id and a.company_name=$cbo_company_name $shipment_date and c.status_active=1 and c.is_deleted=0 and c.booking_type=2 and e.booking_type=2  and e.status_active=1 and e.is_deleted=0 and c.pre_cost_fabric_cost_dtls_id in($pre_cost_id) and e.supplier_id=$cbo_supplier_name  group by c.job_no, c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id");

	foreach($sql_cu_booking as $row_cu_booking){
		$cu_booking_arr[$row_cu_booking[csf('job_no')]][$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]]['cu_woq'][$row_cu_booking[csf('po_break_down_id')]] = $row_cu_booking[csf('cu_wo_qnty')];
		$cu_booking_arr[$row_cu_booking[csf('job_no')]][$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]]['cu_amount'][$row_cu_booking[csf('po_break_down_id')]] = $row_cu_booking[csf('cu_amount')];
	}

	 $sql="select
	a.job_no_prefix_num,
	a.job_no,
	a.company_name,
	a.buyer_name,
	a.currency_id,
	a.style_ref_no,
	b.costing_per,
	b.exchange_rate,
	c.id as wo_pre_cost_trim_cost_dtls,
	c.trim_group,
	c.description,
	c.brand_sup_ref,
	c.country,
	c.rate,
	d.id as po_id,
	d.po_number,
	d.po_quantity as plan_cut,
	min(e.id) as id,
	e.po_break_down_id,
	avg(e.cons) as cons

	from
	wo_po_details_master a,
	wo_pre_cost_mst b,
	wo_pre_cost_trim_cost_dtls c,
	wo_po_break_down d,
	wo_pre_cost_trim_co_cons_dtls e

	where
	a.job_no=b.job_no and
	a.job_no=c.job_no and
	a.job_no=d.job_no_mst and
	a.job_no=e.job_no and
	c.id=e.wo_pre_cost_trim_cost_dtls_id and
	d.id=e.po_break_down_id and
	a.company_name=$cbo_company_name   $garment_nature_cond and
	e.id in($param) and
	e.po_break_down_id in($data) and
	c.id in($pre_cost_id) and
	d.is_deleted=0 and
	d.status_active=1

	group by
	a.job_no_prefix_num,
	a.job_no,
	a.company_name,
	a.buyer_name,
	a.currency_id,
	a.style_ref_no,
	b.costing_per,
	b.exchange_rate,
	c.id,
	c.trim_group,
	c.description,
	c.brand_sup_ref,
	c.country,
	c.rate,
	d.id,
	d.po_number,
	d.po_quantity,
	e.po_break_down_id
	order by d.id,c.id";

	$job_and_trimgroup_level=array();
	$i=1;
	$nameArray=sql_select( $sql );
	foreach ($nameArray as $selectResult){
		$cbo_currency_job=$selectResult[csf('currency_id')];
		$exchange_rate=$exchange_rate_conversion;//$selectResult[csf('exchange_rate')];
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
		$amount=def_number_format($rate_ord_uom*$bal_woq,5,"");

	    //$reqAmtJobLevelConsUom=$reqAmountJobLevelArr[$selectResult[csf('job_no')]];
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['job_no'][$selectResult[csf('po_id')]]=$selectResult[csf('job_no')];
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['po_id'][$selectResult[csf('po_id')]]=$selectResult[csf('po_id')];
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['po_number'][$selectResult[csf('po_id')]]=$selectResult[csf('po_number')];
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['country'][$selectResult[csf('po_id')]]=$selectResult[csf('country')];
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['description'][$selectResult[csf('po_id')]]=$selectResult[csf('description')];
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['brand_sup_ref'][$selectResult[csf('po_id')]]=$selectResult[csf('brand_sup_ref')];
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['trim_group'][$selectResult[csf('po_id')]]=$selectResult[csf('trim_group')];
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['trim_group_name'][$selectResult[csf('po_id')]]=$trim_group_library[$selectResult[csf('trim_group')]];
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['wo_pre_cost_trim_cost_dtls'][$selectResult[csf('po_id')]]=$selectResult[csf('wo_pre_cost_trim_cost_dtls')];

		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['req_qnty'][$selectResult[csf('po_id')]]=$req_qnty_ord_uom;
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['uom'][$selectResult[csf('po_id')]]=$sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom];

		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['uom_name'][$selectResult[csf('po_id')]]=$unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom]];

		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['req_amount'][$selectResult[csf('po_id')]]=$req_amount_ord_uom;
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['req_amount_cons_uom'][$selectResult[csf('po_id')]]=$req_amount_cons_uom;
		//$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['req_amount_job_lebel_cons_uom'][$selectResult[csf('po_id')]]=$reqAmtJobLevelConsUom;


		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['cu_woq'][$selectResult[csf('po_id')]]=$cu_woq;
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['cu_amount'][$selectResult[csf('po_id')]]=$cu_amount;
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['bal_woq'][$selectResult[csf('po_id')]]=$bal_woq;
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['exchange_rate'][$selectResult[csf('po_id')]]=$exchange_rate;
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['rate'][$selectResult[csf('po_id')]]=$rate_ord_uom;
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['amount'][$selectResult[csf('po_id')]]=$amount;
		$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['txt_delivery_date'][$selectResult[csf('po_id')]]=$txt_delivery_date;
	}
	?>
	<input type="hidden" id="strdata" value='<? echo json_encode($job_and_trimgroup_level); ?>' style="background-color:#CCC"/>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1400" class="rpt_table" >
	<thead>
	<th width="40">SL</th>
	<th width="80">Job No</th>
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
	<th width="">Delv. Date</th>
	</thead>
	</table>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1400" class="rpt_table" id="tbl_list_search" >
	<tbody>
	<?
	if($cbo_level==1){
	foreach ($nameArray as $selectResult){
	if ($i%2==0)
	$bgcolor="#E9F3FF";
	else
	$bgcolor="#FFFFFF";

	$cbo_currency_job=$selectResult[csf('currency_id')];
	$exchange_rate=$exchange_rate_conversion;//$selectResult[csf('exchange_rate')];
	if($cbo_currency == $cbo_currency_job){
		$exchange_rate=1;
	}

	$req_qnty_cons_uom = $req_qty_arr[$selectResult[csf('po_id')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]];
	$req_amount_cons_uom = $req_amount_arr[$selectResult[csf('po_id')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]];
	$rate_cons_uom = $req_amount_cons_uom/$req_qnty_cons_uom;

	$req_qnty_ord_uom = def_number_format($req_qnty_cons_uom/$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor],5,"");
	$rate_ord_uom = def_number_format(($rate_cons_uom*$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor])*$exchange_rate,5,"");
	$req_amount_ord_uom = def_number_format($req_qnty_ord_uom*$rate_ord_uom,5,"");

	$cu_woq = $cu_booking_arr[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['cu_woq'][$selectResult[csf('po_id')]];
	$cu_amount = $cu_booking_arr[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['cu_amount'][$selectResult[csf('po_id')]];
	$bal_woq = def_number_format($req_qnty_ord_uom-$cu_woq,5,"");
	$amount = def_number_format($bal_woq*$rate_ord_uom,5,"");
	$reqAmtJobLevelConsUom=$reqAmountJobLevelArr[$selectResult[csf('job_no')]];
	?>
	<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
	<td width="40"><? echo $i;?></td>
	<td width="80">
	<? echo $selectResult[csf('job_no')];?>
	<input type="hidden" id="txtjob_<? echo $i;?>" value="<? echo $selectResult[csf('job_no')];?>" style="width:30px" class="text_boxes" readonly/>
	</td>
	<td width="100">
	<? echo $selectResult[csf('po_number')];?>
	<input type="hidden" id="txtbookingid_<? echo $i;?>" value="" readonly/>
	<input type="hidden" id="txtpoid_<? echo $i;?>" value="<? echo $selectResult[csf('po_id')];?>" readonly/>
	<input type="hidden" id="txtcountry_<? echo $i;?>"  value="<? echo $selectResult[csf('country')] ?>" readonly />
	</td>
	<td width="100"  title="<? echo $sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor];  ?>">
	<? echo $trim_group_library[$selectResult[csf('trim_group')]];?>
	<input type="hidden" id="txttrimcostid_<? echo $i;?>" value="<? echo $selectResult[csf('wo_pre_cost_trim_cost_dtls')];?>" readonly/>
	<input type="hidden" id="txttrimgroup_<? echo $i;?>" value="<? echo $selectResult[csf('trim_group')];?>" readonly/>
	</td>
    <td width="150">
	<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<? echo $bgcolor; ?>" id="txtdesc_<? echo $i;?>"  value="<? echo $selectResult[csf('description')];?>"  />
	</td>
    <td width="150">
	<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<? echo $bgcolor; ?>" id="txtbrandsup_<? echo $i;?>"  value="<? echo $selectResult[csf('brand_sup_ref')];?>"  />
	</td>
	<td width="70" align="right">
	<input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqqnty_<? echo $i;?>" value="<? echo number_format($req_qnty_ord_uom,4,'.','');?>"  readonly  />
	<input type="hidden"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamount_<? echo $i;?>" value="<? echo number_format($req_amount_ord_uom,4,'.','');?>"  readonly  />
    <input type="hidden"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamountjoblevelconsuom_<? echo $i;?>" value="<? echo number_format($reqAmtJobLevelConsUom,4,'.','');?>"  readonly  />
    <input type="hidden"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamountitemlevelconsuom_<? echo $i;?>" value="<? echo number_format($req_amount_cons_uom,4,'.','');?>"  readonly  />

	</td>
	<td width="50">
	<? echo $unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom]];?>
	<input type="hidden" id="txtuom_<? echo $i;?>" value="<? echo $sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom];?>" readonly />
	</td>
	<td width="80" align="right">
	<input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuwoq_<? echo $i;?>" value="<? echo number_format($selectResult[csf('cu_woq')],4,'.','');?>"  readonly  />
	<input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuamount_<? echo $i;?>" value="<? echo number_format($selectResult[csf('cu_amount')],4,'.','');?>"  readonly  />
	</td>
	<td width="80" align="right">
	<input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtbalwoq_<? echo $i;?>" value="<? echo number_format($bal_woq,4,'.',''); ?>"  readonly  />
	</td>
	<td width="100" align="right">
	<?  echo create_drop_down( "cbocolorsizesensitive_".$i, 100, $size_color_sensitive,"", 1, "--Select--", "", "set_cons_break_down($i),copy_value(this.value,'cbocolorsizesensitive_',$i)","","1,2,3,4" ); ?>
	</td>
	<td width="80" align="right">

	<input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoq_<? echo $i;?>" value="<? echo number_format($bal_woq,4,'.','');?>" onClick="open_consumption_popup('requires/trims_booking_multi_job_controllerurmi.php?action=consumption_popup', 'Consumtion Entry Form','txtpoid_<? echo $i;?>',<? echo $i;?>)" readonly />
	</td>
	<td width="55" align="right">
	<input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtexchrate_<? echo $i;?>" value="<? echo $exchange_rate;?>" readonly />

	</td>
	<td width="80" align="right">
	<?
	$ratetexcolor="#000000";
	$decimal=explode(".",$rate_ord_uom);
	if(strlen($decimal[1]>6)){
	$ratetexcolor="#F00";
	}
	?>
	<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;   text-align:right; color:<? echo $ratetexcolor; ?>; background-color:<? echo $bgcolor; ?>" id="txtrate_<? echo $i;?>" value="<? echo $rate_ord_uom ;?>" onChange="calculate_amount(<? echo $i; ?>)" readonly />

	<input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_precost_<? echo $i;?>" value="<? echo $rate_ord_uom;?>" readonly />

	</td>
	<td width="80" align="right">
	<input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtamount_<? echo $i;?>" value="<? echo number_format($amount,4,'.','');?>"  readonly  />
	</td>
	<td width="" align="right">
	<input type="text"   style="width:90%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtddate_<? echo $i;?>"  class="datepicker" value="<? echo $txt_delivery_date; ?>"  readonly  />
	<input type="hidden" id="consbreckdown_<? echo $i;?>"  value=""/>
	<input type="hidden" id="jsondata_<? echo $i;?>"  value=""/>
	</td>
	</tr>
	<?
	$i++;
	$total_amount+=$amount;
    $tot_bal_woq+=$bal_woq;
	}
	}
	if($cbo_level==2){
	?>
	<?
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



	$req_qnty_ord_uom=array_sum($wo_pre_cost_trim_cost_dtls['req_qnty']);
	$rate_ord_uom=array_sum($wo_pre_cost_trim_cost_dtls['req_amount'])/array_sum($wo_pre_cost_trim_cost_dtls['req_qnty']);
	$req_amount_ord_uom=array_sum($wo_pre_cost_trim_cost_dtls['req_amount']);
	$req_amount_cons_uom=array_sum($wo_pre_cost_trim_cost_dtls['req_amount_cons_uom']);



	$bal_woq=array_sum($wo_pre_cost_trim_cost_dtls['bal_woq']);
	$amount=array_sum($wo_pre_cost_trim_cost_dtls['amount']);
	$total_amount+=$amount;
    $tot_bal_woq+=$bal_woq;
	$cu_woq=array_sum($wo_pre_cost_trim_cost_dtls['cu_woq']);
	$cu_amount=array_sum($wo_pre_cost_trim_cost_dtls['cu_amount']);

	$reqAmtJobLevelConsUom=$reqAmountJobLevelArr[$job_no];


	?>
	<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
	<td width="40"><? echo $i;?></td>
	<td width="80">
	<? echo $job_no?>
	<input type="hidden" id="txtjob_<? echo $i;?>" value="<? echo $job_no;?>" style="width:30px" class="text_boxes" readonly/>
	</td>
	<td width="100" style="word-wrap:break-word;word-break: break-all">
	<? echo $po_number; ?>
	<input type="hidden" id="txtbookingid_<? echo $i;?>" value="" readonly/>
	<input type="hidden" id="txtpoid_<? echo $i;?>" value="<? echo $po_id; ?>" readonly/>
	<input type="hidden" id="txtcountry_<? echo $i;?>"  value="<? echo $country; ?>" readonly />
	</td>
	<td width="100"  title="<? echo $sql_lib_item_group_array[$trim_group][conversion_factor];  ?>">
	<? echo $trim_group_library[$trim_group];?>
	<input type="hidden" id="txttrimcostid_<? echo $i;?>" value="<? echo $wo_pre_cost_trim_id;?>" readonly/>
	<input type="hidden" id="txttrimgroup_<? echo $i;?>" value="<? echo $trim_group;?>" readonly/>
	</td>
    <td width="150">
	<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<? echo $bgcolor; ?>" id="txtdesc_<? echo $i;?>"  value="<? echo $description; ?>"  />
	</td>
    <td width="150">
	<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<? echo $bgcolor; ?>" id="txtbrandsup_<? echo $i;?>"  value="<? echo $brand_sup_ref;?>"  />
	</td>
	<td width="70" align="right">
	<input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqqnty_<? echo $i;?>" value="<? echo number_format($req_qnty_ord_uom,4,'.','');?>"  readonly  />
	<input type="hidden"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamount_<? echo $i;?>" value="<? echo number_format($req_amount_ord_uom,4,'.','');?>"  readonly  />
    <input type="hidden"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamountjoblevelconsuom_<? echo $i;?>" value="<? echo number_format($reqAmtJobLevelConsUom,4,'.','');?>"  readonly  />
    <input type="hidden"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamountitemlevelconsuom_<? echo $i;?>" value="<? echo number_format($req_amount_cons_uom,4,'.','');?>"  readonly  />


	</td>
	<td width="50">
	<?  echo $unit_of_measurement[$uom];?>
	<input type="hidden" id="txtuom_<? echo $i;?>" value="<? echo $uom;?>" readonly />
	</td>
	<td width="80" align="right">

	<input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuwoq_<? echo $i;?>" value="<? echo number_format($cu_woq,4,'.',''); ?>"  readonly  />
	<input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuamount_<? echo $i;?>" value="<? echo number_format($cu_amount,4,'.','');?>"  readonly  />
	</td>
	<td width="80" align="right">
	<?
	?>
	<input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtbalwoq_<? echo $i;?>" value="<? echo number_format($bal_woq,4,'.','');?>"  readonly  />
	</td>
	<td width="100" align="right">
	<?  echo create_drop_down( "cbocolorsizesensitive_".$i, 100, $size_color_sensitive,"", 1, "--Select--", "", "set_cons_break_down($i),copy_value(this.value,'cbocolorsizesensitive_',$i)","","1,2,3,4" ); ?>
	</td>
	<td width="80" align="right">

	<input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoq_<? echo $i;?>" value="<? echo number_format($bal_woq,4,'.','');?>" onClick="open_consumption_popup('requires/trims_booking_multi_job_controllerurmi.php?action=consumption_popup', 'Consumtion Entry Form','txtpoid_<? echo $i;?>',<? echo $i;?>)" readonly/>
	</td>
	<td width="55" align="right">
	<input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtexchrate_<? echo $i;?>" value="<? echo $exchange_rate;?>" readonly />

	</td>
	<td width="80" align="right">
	<?
	$ratetexcolor="#000000";
	$decimal=explode(".",$rate_ord_uom);

	if(strlen($decimal[1])>6){
	$ratetexcolor="#F00";
	}
	//echo strlen($decimal[1]);
	?>
	<input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; color:<? echo $ratetexcolor;  ?>;  background-color:<? echo $bgcolor; ?>" id="txtrate_<? echo $i;?>" value="<? echo $rate_ord_uom ;?>" onChange="calculate_amount(<? echo $i; ?>)" readonly />

	<input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_precost_<? echo $i;?>" value="<? echo $rate_ord_uom;?>" readonly />

	</td>

	<td width="80" align="right">
	<input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtamount_<? echo $i;?>" value="<? echo number_format($amount,4,'.','');?>"  readonly  />
	</td>
	<td width="" align="right">
	<input type="text"   style="width:90%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtddate_<? echo $i;?>"  class="datepicker" value="<? echo $txt_delivery_date; ?>"  readonly  />
	<input type="hidden" id="consbreckdown_<? echo $i;?>"  value=""/>
	<input type="hidden" id="jsondata_<? echo $i;?>"  value=""/>
	</td>
	</tr>
	<?
	$i++;

	}
	}
	?>
	<?
	}
	?>
	</tbody>
	</table>
	<table width="1400" class="rpt_table" border="0" rules="all">
	<tfoot>
	<tr>
	<th width="40">&nbsp;</th>
	<th width="80"></th>
	<th width="100"></th>
	<th width="100"></th>
    <th width="150"></th>
    <th width="150"></th>
	<th width="70"><? echo $tot_req_qty; ?></th>
	<th width="50"></th>
	<th width="80"><? echo $tot_cu_woq; ?></th>
	<th width="80"><? //echo $tot_bal_woq; ?></th>
	<th width="100" ></th>
	<th width="80"><input type="text" id="tot_woqqty"   class="text_boxes" value="<? echo  $tot_bal_woq; ?>" style="width:70px" readonly /></th>
	<th width="55"></th>
	<th width="80"><input type="hidden" id="tot_amount" value="<? echo  number_format($total_amount,4,'.',''); ?>" class="text_boxes_numeric" style="width:140px"/></th>
	<th width="80"><input type="text" id="totamount"   class="text_boxes" value="<? echo  $total_amount; ?>" style="width:70px" readonly />
	<input type="hidden"  value="<? echo  $total_amount; ?>" style="width:80px" readonly /></th>
	<th width=""><input type="hidden" id="saved_tot_amount" value="0" style="width:80px; text-align:right" readonly/></th>
	</tr>
	</tfoot>
	</table>
    <table width="1100" colspan="14" cellspacing="0" class="" border="0">
    <tr>
    <td align="center"class="button_container">
    <?
    echo load_submit_buttons( $permission, "fnc_trims_booking_dtls", 0,0,"reset_form('','booking_list_view','','','')",2) ;
    ?>
    </td>
    </tr>
    </table>
	<?
	exit();
}

if ($action=="show_trim_booking"){
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
	$reqAmountJobLevelArr=$trims->getAmountArray_by_job();


	$cu_booking_arr=array();
	$sql_cu_booking=sql_select("select c.job_no,c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id, sum(c.wo_qnty) as cu_wo_qnty, sum(c.amount) as cu_amount from wo_po_details_master a, wo_po_break_down  d , wo_booking_dtls c where a.job_no=d.job_no_mst and a.job_no=c.job_no and  d.id=c.po_break_down_id and a.company_name=$cbo_company_name and c.pre_cost_fabric_cost_dtls_id=$pre_cost_id and c.status_active=1 and c.is_deleted=0 group by c.job_no, c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id");
	foreach($sql_cu_booking as $row_cu_booking){
		$cu_booking_arr[$row_cu_booking[csf('job_no')]][$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]]['cu_woq'][$row_cu_booking[csf('po_break_down_id')]]=$row_cu_booking[csf('cu_wo_qnty')];
		$cu_booking_arr[$row_cu_booking[csf('job_no')]][$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]]['cu_amount'][$row_cu_booking[csf('po_break_down_id')]]=$row_cu_booking[csf('cu_amount')];
	}
	unset($sql_cu_booking);

	$sql="select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, f.exchange_rate, c.id as wo_pre_cost_trim_cost_dtls, c.trim_group, c.description as description_pre_cost, c.brand_sup_ref as brand_sup_ref_precost, c.country, c.rate, d.id as po_id, d.po_number, d.po_quantity as plan_cut, min(e.id) as id, e.po_break_down_id, avg(e.cons) as cons, sum(f.wo_qnty) as cu_woq, sum(f.amount) as cu_amount, f.id as booking_id, f.sensitivity, f.delivery_date, f.description as description, f.brand_supplier as brand_sup_ref

	from wo_po_details_master a, wo_pre_cost_mst b, wo_pre_cost_trim_cost_dtls c, wo_po_break_down d, wo_pre_cost_trim_co_cons_dtls e, wo_booking_dtls f

	where
	a.job_no=b.job_no and a.job_no=c.job_no and a.job_no=d.job_no_mst and a.job_no=e.job_no and a.job_no=f.job_no and c.id=e.wo_pre_cost_trim_cost_dtls_id and d.id=e.po_break_down_id and e.wo_pre_cost_trim_cost_dtls_id= f.pre_cost_fabric_cost_dtls_id and e.po_break_down_id=f.po_break_down_id and f.booking_type=2 and f.booking_no=$txt_booking_no and f.id in($booking_id) and a.company_name=$cbo_company_name $garment_nature_cond and e.wo_pre_cost_trim_cost_dtls_id=$pre_cost_id and d.is_deleted=0 and d.status_active=1 and f.status_active=1 and f.is_deleted=0

	group by
	a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, f.exchange_rate, c.id, c.trim_group, c.description, c.brand_sup_ref, c.country, c.rate, d.id, d.po_number, d.po_quantity, e.po_break_down_id, f.id, f.sensitivity, f.delivery_date, f.description, f.brand_supplier
	order by d.id,c.id";
	//echo $sql;
	$job_and_trimgroup_level=array();
	$i=1;
	$nameArray=sql_select( $sql );
	foreach ($nameArray as $infr)
	{
		$cbo_currency_job=$infr[csf('currency_id')];
		$exchange_rate=$infr[csf('exchange_rate')];
		if($cbo_currency==$cbo_currency_job){
			$exchange_rate=1;
		}

		$pre_cost_trim_id=$infr[csf('wo_pre_cost_trim_cost_dtls')];

		$req_qnty_cons_uom=$req_qty_arr[$infr[csf('po_id')]][$pre_cost_trim_id];
		$req_amount_cons_uom=$req_amount_arr[$infr[csf('po_id')]][$pre_cost_trim_id];
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
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_trim_id]['style_ref_no'][$infr[csf('po_id')]]=$infr[csf('style_ref_no')];
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_trim_id]['po_id'][$infr[csf('po_id')]]=$infr[csf('po_id')];
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_trim_id]['po_number'][$infr[csf('po_id')]]=$infr[csf('po_number')];
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_trim_id]['country'][$infr[csf('po_id')]]=$infr[csf('country')];
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
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_trim_id]['txt_delivery_date'][$infr[csf('po_id')]]=$infr[csf('delivery_date')];
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_trim_id]['booking_id'][$infr[csf('po_id')]]=$infr[csf('booking_id')];
		$job_and_trimgroup_level[$infr[csf('job_no')]][$pre_cost_trim_id]['sensitivity'][$infr[csf('po_id')]]=$infr[csf('sensitivity')];
	}

	$sql_booking=sql_select("select c.job_no, c.pre_cost_fabric_cost_dtls_id, c.po_break_down_id, sum(c.wo_qnty) as wo_qnty, sum(c.amount) as amount from wo_po_details_master a, wo_po_break_down  d, wo_booking_dtls c where a.job_no=d.job_no_mst and a.job_no=c.job_no and  d.id=c.po_break_down_id and c.booking_no=$txt_booking_no and c.pre_cost_fabric_cost_dtls_id=$pre_cost_id  and c.id in($booking_id) and c.booking_type=2 and c.status_active=1 and c.is_deleted=0 group by c.job_no, c.pre_cost_fabric_cost_dtls_id, c.po_break_down_id");
	foreach($sql_booking as $row_booking){
		$job_and_trimgroup_level[$row_booking[csf('job_no')]][$row_booking[csf('pre_cost_fabric_cost_dtls_id')]]['woq'][$row_booking[csf('po_break_down_id')]]=$row_booking[csf('wo_qnty')];
		$job_and_trimgroup_level[$row_booking[csf('job_no')]][$row_booking[csf('pre_cost_fabric_cost_dtls_id')]]['amount'][$row_booking[csf('po_break_down_id')]]=$row_booking[csf('amount')];
		//$job_and_trimgroup_level[$row_booking[csf('job_no')]][$row_booking[csf('pre_cost_fabric_cost_dtls_id')]]['booking_id'][$row_booking[csf('po_break_down_id')]]=$row_booking[csf('booking_id')];

	}
	?>

    <input type="hidden" id="strdata" value='<? echo json_encode($job_and_trimgroup_level); ?>' style="background-color:#CCC"/>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1480" class="rpt_table" >
        <thead>
            <th width="40">SL</th>
            <th width="80">Job No</th>
			<th width="80">Style Ref.</th>
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
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1480" class="rpt_table" id="tbl_list_search" >
        <tbody>
        <?
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
                $reqAmtJobLevelConsUom=$reqAmountJobLevelArr[$selectResult[csf('job_no')]];

				$piNumber=0;
				$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no and b.item_group='".$trim_group."' and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
				if($pi_number){
				$piNumber=1;
				}
				$recvNumber=0;
				$recv_number=return_field_value( "recv_number", "inv_receive_master a,inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and b.item_group_id='".$trim_group."' and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
				if($recv_number){
				$recvNumber=1;
				}
				$disAbled=0;
				if($recvNumber==1 || $piNumber==1){
					$disAbled=1;
				}else{
					$disAbled=0;
				}
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
                    <td width="40"><? echo $i;?></td>
                    <td width="80"><? echo $selectResult[csf('job_no')];?>
                        <input type="hidden" id="txtjob_<? echo $i;?>" value="<? echo $selectResult[csf('job_no')];?>" style="width:30px" class="text_boxes" readonly/>
                    </td>
					<td width="80"><? echo $selectResult[csf('style_ref_no')];?>
                        <input type="hidden" id="style_ref_no_<? echo $i;?>" value="<? echo $selectResult[csf('style_ref_no')];?>" style="width:30px" class="text_boxes" readonly/>
                    </td>
                    <td width="100"><? echo $selectResult[csf('po_number')];?>
                        <input type="hidden" id="txtbookingid_<? echo $i;?>" value="<? echo $selectResult[csf('booking_id')];?>" readonly/>
                        <input type="hidden" id="txtpoid_<? echo $i;?>" value="<? echo $selectResult[csf('po_id')];?>" readonly/>
                        <input type="hidden" id="txtcountry_<? echo $i;?>"  value="<? echo $selectResult[csf('country')] ?>" readonly />
                    </td>
                    <td width="100"  title="<? echo $sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor];  ?>">
                        <? echo $trim_group_library[$selectResult[csf('trim_group')]];?>
                        <input type="hidden" id="txttrimcostid_<? echo $i;?>" value="<? echo $selectResult[csf('wo_pre_cost_trim_cost_dtls')];?>" readonly/>
                        <input type="hidden" id="txttrimgroup_<? echo $i;?>" value="<? echo $selectResult[csf('trim_group')];?>" readonly/>
                    </td>
                    <td width="150">
                        <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<? echo $bgcolor; ?>" id="txtdesc_<? echo $i;?>"  value="<? echo $selectResult[csf('description')];?>"  <? if($disAbled){echo "disabled";}else{ echo "";}?> />
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
                    $reqAmtJobLevelConsUom=$reqAmountJobLevelArr[$job_no];

					$piNumber=0;
					$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no and b.item_group='".$trim_group."' and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
					if($pi_number){
					$piNumber=1;
					}
					$recvNumber=0;
					$recv_number=return_field_value( "recv_number", "inv_receive_master a,inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and b.item_group_id='".$trim_group."' and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
					if($recv_number){
					$recvNumber=1;
					}
					$disAbled=0;
					if($recvNumber==1 || $piNumber==1){
						$disAbled=1;
					}else{
						$disAbled=0;
					}
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
                        <td width="40"><? echo $i;?></td>
                        <td width="80"><? echo $job_no?><input type="hidden" id="txtjob_<? echo $i;?>" value="<? echo $job_no;?>" style="width:30px" class="text_boxes" readonly/></td>
						<td width="80"><? echo $style_ref_no?><input type="hidden" id="style_ref_no_<? echo $i;?>" value="<? echo $style_ref_no;?>" style="width:30px" class="text_boxes" readonly/></td>
                        <td width="100" style="word-wrap:break-word;word-break: break-all"><? echo $po_number; ?>
                            <input type="hidden" id="txtbookingid_<? echo $i;?>" value="<? echo $booking_id; ?>" readonly/>
                            <input type="hidden" id="txtpoid_<? echo $i;?>" value="<? echo $po_id; ?>" readonly/>
                            <input type="hidden" id="txtcountry_<? echo $i;?>"  value="<? echo $country; ?>" readonly />
                        </td>
                        <td width="100"  title="<? echo $sql_lib_item_group_array[$trim_group][conversion_factor];  ?>">
                            <? echo $trim_group_library[$trim_group];?>
                            <input type="hidden" id="txttrimcostid_<? echo $i;?>" value="<? echo $wo_pre_cost_trim_id;?>" readonly/>
                            <input type="hidden" id="txttrimgroup_<? echo $i;?>" value="<? echo $trim_group;?>" readonly/>
                        </td>
                        <td width="150">
                            <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<? echo $bgcolor; ?>" id="txtdesc_<? echo $i;?>"  value="<? echo $description; ?>" <? if($disAbled){echo "disabled";}else{ echo "";}?> />
                        </td>
                        <td width="150">
                            <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<? echo $bgcolor; ?>" id="txtbrandsup_<? echo $i;?>"  value="<? echo $brand_sup_ref;?>" <? if($disAbled){echo "disabled";}else{ echo "";}?>  />
                        </td>
                        <td width="70" align="right">
                            <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqqnty_<? echo $i;?>" value="<? echo number_format($req_qnty_ord_uom,4,'.','');?>"  readonly  />
                            <input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamount_<? echo $i;?>" value="<? echo number_format($req_amount_ord_uom,4,'.','');?>"  readonly  />
                            <input type="hidden"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamountjoblevelconsuom_<? echo $i;?>" value="<? echo number_format($reqAmtJobLevelConsUom,4,'.','');?>"  readonly  />
                            <input type="hidden"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamountitemlevelconsuom_<? echo $i;?>" value="<? echo number_format($req_amount_cons_uom,4,'.','');?>"  readonly  />
                        </td>
                        <td width="50"><?  echo $unit_of_measurement[$uom];?><input type="hidden" id="txtuom_<? echo $i;?>" value="<? echo $uom;?>" readonly /></td>
                        <td width="80" align="right">
                            <input type="text"  style="width:100%; height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuwoq_<? echo $i;?>" value="<? echo number_format($cu_woq,4,'.',''); ?>"  readonly  />
                            <input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuamount_<? echo $i;?>" value="<? echo $cu_amount;?>"  readonly  />
                        </td>
                        <td width="80" align="right">
                            <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtbalwoq_<? echo $i;?>" value="<? echo number_format($bal_woq,4,'.','');?>"  readonly  />
                        </td>
                        <td width="100" align="right"><?  echo create_drop_down( "cbocolorsizesensitive_".$i, 100, $size_color_sensitive,"", 1, "--Select--", $sensitivity, "set_cons_break_down($i),copy_value(this.value,'cbocolorsizesensitive_',$i)",$disAbled,"1,2,3,4" ); ?>
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
                            <input type="text"   style="width:90%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtddate_<? echo $i;?>"  class="datepicker" value="<? echo change_date_format($delivery_date,"dd-mm-yyyy","-"); ?>"  readonly <? if($disAbled){echo "disabled";}else{ echo "";}?>   />
                            <input type="hidden" id="consbreckdown_<? echo $i;?>"  value=""/>
                            <input type="hidden" id="jsondata_<? echo $i;?>"  value=""/>
                        </td>
                    </tr>
                    <?
                    $i++;
					$tot_amount+=$amount;
    				$tot_woq+=$woq;
                }
            }
        }
        ?>
        </tbody>
	</table>
	<table width="1480" class="rpt_table" border="0" rules="all">
        <tfoot>
            <tr>
                <th width="40">&nbsp;</th>
                <th width="80">&nbsp;</th>
				<th width="80">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="150">&nbsp;</th>
                <th width="150">&nbsp;</th>
                <th width="70"><? echo $tot_req_qty; ?></th>
                <th width="50">&nbsp;</th>
                <th width="80"><? echo $tot_cu_woq; ?></th>
                <th width="80"><? //echo $tot_bal_woq; ?></th>
                <th width="100">&nbsp;</th>
                <th width="80"><input type="text" id="tot_woqqty"   class="text_boxes" value="<? echo  $tot_woq; ?>" style="width:70px" readonly /></th>
                <th width="55">&nbsp;</th>
                <th width="80"><input type="hidden" id="tot_amount" value="<? echo  number_format($total_amount,4,'.',''); ?>" class="text_boxes_numeric" style="width:140px"/></th>
                <th width="80" id=""><input type="text" id="totamount"   class="text_boxes" value="<? echo  number_format($total_amount,4); ?>" style="width:70px" readonly /> <input type="hidden"  value="<? echo  number_format($total_amount,4,'.',''); ?>" class="text_boxes_numeric" style="width:140px"/><? //number_format($tot_amount,4,'.',''); ?></th>
                <th><input type="hidden" id="saved_tot_amount" value="0" style="width:80px; text-align:right" readonly/></th>
            </tr>
        </tfoot>
	</table>
    <table width="1100" colspan="14" cellspacing="0" class="" border="0">
        <tr>
            <td align="center"class="button_container">
            	<? echo load_submit_buttons( $permission, "fnc_trims_booking_dtls", 1,0,"reset_form('','booking_list_view','','','')",2); ?>
            </td>
        </tr>
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

	 $sql="SELECT a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, f.exchange_rate, c.id as wo_pre_cost_trim_cost_dtls, c.trim_group, c.description as description_pre_cost, c.brand_sup_ref as brand_sup_ref_precost, c.country, c.rate, d.id as po_id, d.po_number, d.po_quantity as plan_cut, min(e.id) as id, e.po_break_down_id, avg(e.cons) as cons, sum(f.wo_qnty) as cu_woq, sum(f.amount) as cu_amount, f.id as booking_id, f.sensitivity, f.delivery_date, f.description as description, f.brand_supplier as brand_sup_ref

	from
	wo_po_details_master a, wo_pre_cost_mst b, wo_pre_cost_trim_cost_dtls c, wo_po_break_down d, wo_pre_cost_trim_co_cons_dtls e, wo_booking_dtls f

	where
	a.job_no=b.job_no and a.job_no=c.job_no and a.job_no=d.job_no_mst and a.job_no=e.job_no and a.job_no=f.job_no and c.id=e.wo_pre_cost_trim_cost_dtls_id and d.id=e.po_break_down_id and e.wo_pre_cost_trim_cost_dtls_id= f.pre_cost_fabric_cost_dtls_id and e.po_break_down_id=f.po_break_down_id and f.booking_type=2 and f.booking_no=$txt_booking_no and a.company_name=$cbo_company_name   $garment_nature_cond and d.is_deleted=0 and d.status_active=1 and f.status_active=1 and f.is_deleted=0

	group by a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, f.exchange_rate, c.id, c.trim_group, c.description, c.brand_sup_ref, c.country, c.rate, d.id, d.po_number, d.po_quantity, e.po_break_down_id, f.id, f.sensitivity, f.delivery_date, f.description, f.brand_supplier
	order by d.id, c.id";

	$job_and_trimgroup_level=array();
	$i=1;
	$nameArray=sql_select( $sql );
	foreach ($nameArray as $selectResult){
	$cbo_currency_job=$selectResult[csf('currency_id')];
	$exchange_rate=$selectResult[csf('exchange_rate')];
	if($cbo_currency==$cbo_currency_job){
		$exchange_rate=1;
	}
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('description')]][$selectResult[csf('brand_sup_ref')]]['job_no'][$selectResult[csf('po_id')]]=$selectResult[csf('job_no')];

	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('description')]][$selectResult[csf('brand_sup_ref')]]['style_ref_no'][$selectResult[csf('po_id')]]=$selectResult[csf('style_ref_no')];

	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('description')]][$selectResult[csf('brand_sup_ref')]]['po_id'][$selectResult[csf('po_id')]]=$selectResult[csf('po_id')];
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('description')]][$selectResult[csf('brand_sup_ref')]]['po_number'][$selectResult[csf('po_id')]]=$selectResult[csf('po_number')];
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('description')]][$selectResult[csf('brand_sup_ref')]]['country'][$selectResult[csf('po_id')]]=$selectResult[csf('country')];

	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('description')]][$selectResult[csf('brand_sup_ref')]]['description'][$selectResult[csf('po_id')]]=$selectResult[csf('description')];

	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('description')]][$selectResult[csf('brand_sup_ref')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['brand_sup_ref'][$selectResult[csf('po_id')]]=$selectResult[csf('brand_sup_ref')];

	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('description')]][$selectResult[csf('brand_sup_ref')]]['trim_group'][$selectResult[csf('po_id')]]=$selectResult[csf('trim_group')];
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('description')]][$selectResult[csf('brand_sup_ref')]]['trim_group_name'][$selectResult[csf('po_id')]]=$trim_group_library[$selectResult[csf('trim_group')]];
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('description')]][$selectResult[csf('brand_sup_ref')]]['wo_pre_cost_trim_cost_dtls'][$selectResult[csf('po_id')]]=$selectResult[csf('wo_pre_cost_trim_cost_dtls')];

	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('description')]][$selectResult[csf('brand_sup_ref')]]['uom'][$selectResult[csf('po_id')]]=$sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom];

	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('description')]][$selectResult[csf('brand_sup_ref')]]['uom_name'][$selectResult[csf('po_id')]]=$unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom]];


	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('description')]][$selectResult[csf('brand_sup_ref')]]['cu_woq'][$selectResult[csf('po_id')]]=$cu_woq;
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('description')]][$selectResult[csf('brand_sup_ref')]]['cu_amount'][$selectResult[csf('po_id')]]=$cu_amount;
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('description')]][$selectResult[csf('brand_sup_ref')]]['bal_woq'][$selectResult[csf('po_id')]]=$bal_woq;
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('description')]][$selectResult[csf('brand_sup_ref')]]['exchange_rate'][$selectResult[csf('po_id')]]=$exchange_rate;
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('description')]][$selectResult[csf('brand_sup_ref')]]['rate'][$selectResult[csf('po_id')]]=$rate_ord_uom;
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('description')]][$selectResult[csf('brand_sup_ref')]]['txt_delivery_date'][$selectResult[csf('po_id')]]=$selectResult[csf('delivery_date')];

	//$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('description')]][$selectResult[csf('brand_sup_ref')]]['booking_id'][$selectResult[csf('po_id')]]=$selectResult[csf('booking_id')];
	$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('description')]][$selectResult[csf('brand_sup_ref')]]['sensitivity'][$selectResult[csf('po_id')]]=$selectResult[csf('sensitivity')];
	}
	$sql_booking=sql_select("select c.job_no,c.pre_cost_fabric_cost_dtls_id, c.id as booking_id, c.po_break_down_id,c.sensitivity, c.description,c.brand_supplier,c.wo_qnty as wo_qnty, c.amount as amount from wo_po_details_master a, wo_po_break_down  d , wo_booking_dtls c where a.job_no=d.job_no_mst and a.job_no=c.job_no and  d.id=c.po_break_down_id and c.booking_no=$txt_booking_no and c.booking_type=2 and c.status_active=1 and c.is_deleted=0");
	foreach($sql_booking as $row_booking){
		$job_and_trimgroup_level[$row_booking[csf('job_no')]][$row_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_booking[csf('sensitivity')]][$row_booking[csf('description')]][$row_booking[csf('brand_supplier')]]['woq'][$row_booking[csf('po_break_down_id')]]+=$row_booking[csf('wo_qnty')];
		$job_and_trimgroup_level[$row_booking[csf('job_no')]][$row_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_booking[csf('sensitivity')]][$row_booking[csf('description')]][$row_booking[csf('brand_supplier')]]['amount'][$row_booking[csf('po_break_down_id')]]+=$row_booking[csf('amount')];

		$job_and_trimgroup_level[$row_booking[csf('job_no')]][$row_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_booking[csf('sensitivity')]][$row_booking[csf('description')]][$row_booking[csf('brand_supplier')]]['description'][$row_booking[csf('po_break_down_id')]]=$row_booking[csf('description')];
		$job_and_trimgroup_level[$row_booking[csf('job_no')]][$row_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_booking[csf('sensitivity')]][$row_booking[csf('description')]][$row_booking[csf('brand_supplier')]]['brand_sup_ref'][$row_booking[csf('po_break_down_id')]]=$row_booking[csf('brand_supplier')];
				$job_and_trimgroup_level[$row_booking[csf('job_no')]][$row_booking[csf('pre_cost_fabric_cost_dtls_id')]][$row_booking[csf('sensitivity')]][$row_booking[csf('description')]][$row_booking[csf('brand_supplier')]]['booking_id'][$row_booking[csf('po_break_down_id')]]=$row_booking[csf('booking_id')];

	}
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1350" class="rpt_table">
	<thead>
	<th width="40">SL</th>
	<th width="100">Job No</th>
	<th width="100">Style Ref.</th>
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
	if($cbo_level==1){
		$total_amount=$total_woq=0;
	foreach ($nameArray as $selectResult){
	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

	$cbo_currency_job=$selectResult[csf('currency_id')];
	$exchange_rate=$selectResult[csf('exchange_rate')];
	if($cbo_currency==$cbo_currency_job){
		$exchange_rate=1;
	}
	$woq=def_number_format($job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('description')]][$selectResult[csf('brand_supplier')]]['woq'][$selectResult[csf('po_id')]],5,"");
	$amount=def_number_format($job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('sensitivity')]][$selectResult[csf('description')]][$selectResult[csf('brand_supplier')]]['amount'][$selectResult[csf('po_id')]],5,"");
	$rate=def_number_format($amount/$woq,5,"");
	$total_amount+=$amount;

	?>
	<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="fnc_show_booking(<? echo $selectResult[csf('wo_pre_cost_trim_cost_dtls')];?>,'<? echo $selectResult[csf('po_id')]; ?>','<? echo $selectResult[csf('booking_id')];?>','<? echo $selectResult[csf('job_no')];?>')">
	<td width="40"><? echo $i;?></td>
	<td width="100">
	<? echo $selectResult[csf('job_no')];?>
	</td>
	<td width="100">
	<? echo $selectResult[csf('style_ref_no')];?>
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
	<? echo number_format($woq,4,'.','');$total_woq +=$woq;?>
	</td>
	<td width="80" align="right">
    <? echo $exchange_rate;?>
	</td>
	<td width="80" align="right">
    <? echo number_format($rate,4,'.','');?>
	</td>
	<td width="80" align="right">
    <? echo number_format($amount,4,'.','');$total_amt +=$amount;?>
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
	$i=1;$total_amount=$total_woq=0;
	foreach ($job_and_trimgroup_level as $job_no){
	foreach ($job_no as $sen){
	foreach ($sen as $desc){
	foreach ($desc as $brandsup){
	foreach ($brandsup as $wo_pre_cost_trim_cost_dtls){
	$job_no=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['job_no']));
	$style_ref_no=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['style_ref_no']));
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

	?>
	<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="fnc_show_booking(<? echo $wo_pre_cost_trim_id;?>,'<? echo $po_id; ?>','<? echo $booking_id; ?>','<? echo $job_no; ?>')">
	<td width="40"><? echo $i;?></td>
	<td width="100">
	<? echo $job_no?>
	</td>
	<td width="100">
	<? echo $style_ref_no?>
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
	<? echo number_format($woq,4,'.','');$total_woq +=$woq;?>
	</td>
	<td width="80" align="right">
    <? echo $exchange_rate;?>
	</td>
	<td width="80" align="right">
    <? echo number_format($rate,4,'.','');?>
	</td>
	<td width="80" align="right">
    <? echo number_format($amount,4,'.','');$total_amt +=$amount;?>
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
		<td align="right"><? echo number_format($total_woq,6,'.',''); ?></td>
		<td colspan="2" align="right"><strong>Total Amount</strong></td>
		<td align="right"><? echo number_format($total_amt,6,'.',''); ?></td>
		<td>&nbsp;</td>
    </tr>
	</tbody>
	</table>
	<?
	exit();
}

if ($action == "consumption_popup")
{
	echo load_html_head_contents("Consumption Entry","../../../../", 1, 1, $unicode,'','');
	$color_library=return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$size_library=return_library_array("select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	?>
	<script>
		var str_gmtssizes = [<? echo substr(return_library_autocomplete( "select size_name from  lib_size where status_active=1 and is_deleted=0", "size_name"  ), 0, -1); ?>];
		var str_diawidth = [<? echo substr(return_library_autocomplete( "select color_name from lib_color where status_active=1 and is_deleted=0", "color_name"  ), 0, -1); ?>];
		function poportionate_qty(qty)
		{
			var round_check=0;
			if ($('#round_down').is(":checked"))
			{
			   round_check=1;
			}
			var txtwoq=document.getElementById('txtwoq').value;
			var txtwoq_qty=document.getElementById('txtwoq_qty').value*1;
			var rowCount = $('#tbl_consmption_cost tbody tr').length;
			for(var i=1; i<=rowCount; i++){
				var poreqqty=$('#poreqqty_'+i).val();
				var txtwoq_cal =number_format_common((txtwoq_qty/txtwoq) * (poreqqty),5,0);
				if(round_check==1){
					txtwoq_cal=Math.floor(txtwoq_cal);
				}
				$('#qty_'+i).val(txtwoq_cal);
				calculate_requirement(i)
			}
			set_sum_value( 'qty_sum', 'qty_')
			if(round_check!=1){
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
		function set_sum_value(des_fil_id,field_id)
		{
			if(des_fil_id=='qty_sum') var ddd={dec_type:5,comma:0,currency:0};
			if(des_fil_id=='excess_sum') var ddd={dec_type:5,comma:0,currency:0};
			if(des_fil_id=='woqty_sum') var ddd={dec_type:5,comma:0,currency:0};
			if(des_fil_id=='amount_sum') var ddd={dec_type:6,comma:0,currency:0};
			if(des_fil_id=='pcs_sum') var ddd={dec_type:6,comma:0};
			var rowCount = $('#tbl_consmption_cost tbody tr').length;
			math_operation( des_fil_id, field_id, '+', rowCount,ddd );
		}
		function copy_value(value,field_id,i)
		{
			var gmtssizesid=document.getElementById('gmtssizesid_'+i).value;
			var pocolorid=document.getElementById('pocolorid_'+i).value;
			var rowCount = $('#tbl_consmption_cost tbody tr').length;
			var copy_basis=$('input[name="copy_basis"]:checked').val()

			for(var j=i; j<=rowCount; j++)
			{
				if(field_id=='des_'){
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
			var amount=number_format_common((rate*woqny),6,0);
			document.getElementById('amount_'+i).value=amount;
			set_sum_value( 'amount_sum', 'amount_' );
			calculate_avg_rate()
		}
		function calculate_avg_rate(){
			var woqty_sum=document.getElementById('woqty_sum').value;
			var amount_sum=document.getElementById('amount_sum').value;
			var avg_rate=number_format_common((amount_sum/woqty_sum),6,0);
			document.getElementById('rate_sum').value=avg_rate;
		}
		function js_set_value(){
			var cbo_colorsizesensitive=$('#cbo_colorsizesensitive').val();
			var reg=/[^a-zA-Z0-9!@#$%^,;.:<>{}?\+|\[\]\- \/]/g;
			var row_num=$('#tbl_consmption_cost tbody tr').length;
			var cons_breck_down="";
			var po_item_chk_arr=new Array();
			for(var i=1; i<=row_num; i++){
				var txtdescription=$('#des_'+i).val();
				var txtsupref=$('#brndsup_'+i).val();
				//alert(txtdescription.match(reg))
				if(txtdescription.match(reg)){
					alert("Your Description Can not Have any thing other than a-zA-Z0-9!@#$%^,;.:<>{}?+|[]/- ");
					//release_freezing();
					$('#des_'+i).css('background-color', 'red');
					return;
				}
				if(txtsupref.match(reg)){
					alert("Your Brand Sup. Ref Can not Have any thing other than a-zA-Z0-9!@#$%^,;.:<>{}?+|[]/- ");
					$('#brndsup_'+i).css('background-color', 'red');
					//release_freezing();
					return;
				}

				var pocolorid=$('#pocolorid_'+i).val()
				if(pocolorid=='') pocolorid=0;

				var gmtssizesid=$('#gmtssizesid_'+i).val()
				if(gmtssizesid=='') gmtssizesid=0;

				var des=trim($('#des_'+i).val())
				if(des=='') des=0;

				var brndsup=trim($('#brndsup_'+i).val());
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
				if(colorsizetableid=='')colorsizetableid=0;

				var updateid=$('#updateid_'+i).val()
				if(updateid=='') updateid=0;

				var reqqty=$('#reqqty_'+i).val()
				if(reqqty=='') reqqty=0;

				var poarticle=$('#poarticle_'+i).val()
				if(poarticle=='') poarticle='no article';

				if(cbo_colorsizesensitive==0){
					if(itemsizes!=0) var itemsize_str=itemsizes.toLowerCase();
					if(des!=0) var des_str=des.toLowerCase();
					po_item_chk_arr.push(des_str+'#'+itemsize_str);
					if(hasDuplicates(po_item_chk_arr)) {
						alert('Error: you have duplicates values !');
						release_freezing();
						return;
					}
				}


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
		function add_break_down_tr(i){
			i++;
			$("#tbl_consmption_cost_dtls tr:last").clone().find("input,select").each(function() {
			$(this).attr({
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  'name': function(_, name) { return name + i },
			  'value': function(_, value) { return value }
			});

		  }).end().appendTo("#tbl_consmption_cost_dtls");
		  $('#qty_'+i).removeAttr("onChange").attr("onChange","set_sum_value( 'qty_sum', 'qty_' );set_sum_value( 'woqty_sum', 'woqny_' );calculate_requirement( "+i+");copy_value(this.value,'qty_', "+i+")");
		  $('#excess_'+i).removeAttr("onChange").attr("onChange","calculate_requirement("+i+");set_sum_value( 'excess_sum', 'excess_' );set_sum_value( 'woqty_sum', 'woqny_' );copy_value(this.value,'excess_',"+i+")");
		  $('#excess_'+i).removeAttr("onChange").attr("onChange","calculate_amount("+i+");set_sum_value( 'amount_sum', 'amount_' );copy_value(this.value,'rate_',"+i+")");
		  $('#rate_'+i).removeAttr("onChange").attr("onChange","calculate_amount("+i+");set_sum_value( 'amount_sum', 'amount_' );copy_value(this.value,'rate_',"+i+")");
		  $('#increasesensitivity_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
		  $('#decreasesensitivity_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",this);");

		  $('#qty_'+i).val("");
		  $('#excess_'+i).val("");
		  $('#woqny_'+i).val("");
		  //$('#rate_'+i).val("");
		  $('#amount_'+i).val("");

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
				var r=confirm("Are you sure?")
				if(r==true)
				{
					var index=rowNo-1
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
							$("#tbl_consmption_cost tbody tr:eq("+i+")").removeAttr('id').attr('id','break_'+i);
							$('#increasetrim_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
							$('#decreasetrim_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'this');");
						});
					}
				}
				else
				{
					return;
				}
			}
		}
		function hasDuplicates(arr) {
			var counts = [];
			for (var i = 0; i <= arr.length; i++) {
				if (counts[arr[i]] === undefined)
				{
					counts[arr[i]] = 1;
				}
				else
				{
				return true;
				}
			}
			return false;
		}
	</script>
	</head>
	<body>
		<?
        extract($_REQUEST);
        if($txt_job_no==""){
			$txt_job_no_cond="";
			$txt_job_no_cond1="";
        }
        else{
			$txt_job_no_cond ="and a.job_no='$txt_job_no'";
			$txt_job_no_cond1 ="and job_no='$txt_job_no'";
        }
        if($txt_country==""){
			$txt_country_cond="";
        }
        else{
			$txt_country_cond ="and c.country_id in ($txt_country)";
        }
        $process_loss_method=return_field_value("process_loss_method", "variable_order_tracking", "company_name=$cbo_company_name  and variable_list=18 and item_category_id=4 and status_active=1 and is_deleted=0");
        $tot_po_qty=0;
        $sql_po_qty=sql_select("select b.id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id in($txt_po_id)  $txt_country_cond  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty"); //,c.item_number_id
        foreach($sql_po_qty as$sql_po_qty_row){
			$po_qty_arr[$sql_po_qty_row[csf('id')]]=$sql_po_qty_row[csf('order_quantity_set')];
			$tot_po_qty+=$sql_po_qty_row[csf('order_quantity_set')];
        }
		$colspan=14;
		if($cbo_colorsizesensitive==0){ $colspan=15;}
        ?>
        <div align="center" style="width:1250px;" >
            <fieldset>
                <form id="consumptionform_1" autocomplete="off">
                    <table width="1250" cellspacing="0" class="rpt_table" border="0" id="tbl_consmption_cost" rules="all">
                        <thead>
                        	<tr>
                                <th colspan="<?= $colspan ?>" id="td_sync_msg" style="color:#FF0000"></th>
                            </tr>
                            <tr>
                                <th colspan="<?= $colspan ?>">
                                    <input type="hidden" id="cons_breck_down" name="cons_breck_down" value="" />
                                    <input type="hidden" id="txtwoq" value="<? echo $txt_req_quantity;?>"/>
                                    <input type="hidden" id="cbo_colorsizesensitive" value="<? echo $cbo_colorsizesensitive;?>"/>
                                    Wo Qty:<input type="text" id="txtwoq_qty" class="text_boxes_numeric" onBlur="poportionate_qty(this.value)" value="<? echo $txtwoq; ?>"/>
                                    <input type="radio" name="copy_basis" value="0" <? if(!$txt_update_dtls_id) { echo "checked";} ?>>Copy to All
                                    <input type="radio" name="copy_basis" value="1">Gmts Size Wise
                                    <input type="radio" name="copy_basis" value="2">Gmts Color Wise
                                    <input type="radio" name="copy_basis" value="10" <? if($txt_update_dtls_id) { echo "checked";} ?>>No Copy
									<input type="checkbox" name="round_down" id="round_down" value="" onClick="poportionate_qty();" >Round Down
                                    <input type="hidden" id="process_loss_method_id" name="process_loss_method_id" value="<? echo $process_loss_method; ?>"/>
                                    <input type="hidden" id="po_qty" name="po_qty" value="<? echo $tot_po_qty; ?>"/>
                                </th>
                            </tr>
                            <tr>
                                <th width="40">SL</th><th  width="100">Article No</th><th  width="100">Gmts. Color</th><th  width="70">Gmts. sizes</th><th  width="100">Description</th><th  width="100">Brand/Sup Ref</th><th  width="100">Item Color</th><th width="80">Item Sizes</th><th width="70"> Wo Qty</th><th width="40">Excess %</th><th width="70">WO Qty.</th><th width="60">Rate</th><th width="60">Amount</th><th width="60">RMG Qnty</th><? if($cbo_colorsizesensitive==0){ ?><th width="">&nbsp;</th> <? } ?>

                            </tr>
                        </thead>
                        <tbody id="tbl_consmption_cost_dtls">
                        <?
                        $sql_lib_item_group_array=array();
                        $sql_lib_item_group=sql_select("select id, item_name,conversion_factor,order_uom as cons_uom from lib_item_group");
                        foreach($sql_lib_item_group as $row_sql_lib_item_group){
							$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][item_name]=$row_sql_lib_item_group[csf('item_name')];
							$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][conversion_factor]=$row_sql_lib_item_group[csf('conversion_factor')];
							$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][cons_uom]=$row_sql_lib_item_group[csf('cons_uom')];
                        }

                        $booking_data_arr=array();
						if($txt_update_dtls_id==""){
							$txt_update_dtls_id=0;
						}
						if($cbo_colorsizesensitive!=0)
						{
							$booking_data=sql_select("select id,wo_trim_booking_dtls_id,description,brand_supplier,item_color,item_size,cons,process_loss_percent,requirment,rate, 	amount,pcs,color_size_table_id  from wo_trim_book_con_dtls where wo_trim_booking_dtls_id in($txt_update_dtls_id) and status_active=1 and is_deleted=0");
							foreach($booking_data as $row){
								$booking_data_arr[$row[csf('color_size_table_id')]]['id']=$row[csf('id')];
								$booking_data_arr[$row[csf('color_size_table_id')]]['description']=$row[csf('description')];
								$booking_data_arr[$row[csf('color_size_table_id')]]['brand_supplier']=$row[csf('brand_supplier')];
								$booking_data_arr[$row[csf('color_size_table_id')]]['item_color']=$row[csf('item_color')];
								$booking_data_arr[$row[csf('color_size_table_id')]]['item_size']=$row[csf('item_size')];

								$booking_data_arr[$row[csf('color_size_table_id')]]['cons']+=$row[csf('cons')];
								$booking_data_arr[$row[csf('color_size_table_id')]]['process_loss_percent']=$row[csf('process_loss_percent')];
								$booking_data_arr[$row[csf('color_size_table_id')]]['requirment']+=$row[csf('requirment')];
								$booking_data_arr[$row[csf('color_size_table_id')]]['rate']=$row[csf('rate')];
								$booking_data_arr[$row[csf('color_size_table_id')]]['amount']+=$row[csf('amount')];
							}
						}
						else{
							$booking_data=sql_select("select min(id) as mid, min(wo_trim_booking_dtls_id) as wo_trim_booking_dtls_id, description, brand_supplier, item_color, item_size,bom_item_size,bom_item_color, sum(cons) as cons, process_loss_percent, sum(requirment) as requirment, rate, sum(amount) as amount, sum(pcs) as pcs, min(color_size_table_id) as color_size_table_id, zipper_break_down,remarks, moq as moq, pp_sample as pp_sample from wo_trim_book_con_dtls where wo_trim_booking_dtls_id in($txt_update_dtls_id) and status_active=1 and is_deleted=0 group by description, brand_supplier, item_color, item_size,bom_item_size,bom_item_color, process_loss_percent, rate, zipper_break_down,remarks, moq, pp_sample order by mid ASC");
							$nosensbookdataArr=$booking_data;
							foreach($booking_data as $row){

								$booking_data_arr[$row[csf('description')]][$row[csf('brand_supplier')]][$row[csf('item_color')]][$row[csf('item_size')]][id]=$row[csf('mid')];
								$booking_data_arr[$row[csf('color_size_table_id')]]['description']=$row[csf('description')];
								$booking_data_arr[$row[csf('color_size_table_id')]]['brand_supplier']=$row[csf('brand_supplier')];
								$booking_data_arr[$row[csf('color_size_table_id')]]['remarks']=$row[csf('remarks')];
								$booking_data_arr[$row[csf('color_size_table_id')]]['item_color']=$row[csf('item_color')];
								$booking_data_arr[$row[csf('color_size_table_id')]]['item_size']=$row[csf('item_size')];
								$booking_data_arr[$row[csf('color_size_table_id')]]['zipper_break_down']=$row[csf('zipper_break_down')];
								$booking_data_arr[$row[csf('color_size_table_id')]]['bom_item_color']=$row[csf('bom_item_color')];
								$booking_data_arr[$row[csf('color_size_table_id')]]['bom_item_size']=$row[csf('bom_item_size')];

								$booking_data_arr[$row[csf('color_size_table_id')]]['cons']+=$row[csf('cons')];
								$booking_data_arr[$row[csf('color_size_table_id')]]['process_loss_percent']=$row[csf('process_loss_percent')];
								$booking_data_arr[$row[csf('color_size_table_id')]]['requirment']+=$row[csf('requirment')];
								$booking_data_arr[$row[csf('color_size_table_id')]]['rate']=$row[csf('rate')];
								$booking_data_arr[$row[csf('color_size_table_id')]]['amount']+=$row[csf('amount')];
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
							$sql="select b.id, b.po_number, b.po_quantity, min(c.id) as color_size_table_id, c.color_number_id, min(c.color_order) as color_order, sum(c.order_quantity) as order_quantity, (sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where  a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id order by b.id, color_order";
							$gmt_size_edb=1;
							$item_size_edb=1;
                        }
                        else if($cbo_colorsizesensitive==2){
							$req_qty_arr=$trims->getQtyArray_by_orderPrecostdtlsidGmtssizeAndArticle();
							$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidGmtssizeAndArticle();

							$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.size_number_id,c.article_number,min(c.size_order) as size_order,min(e.item_size) as item_size,sum(c.order_quantity) as order_quantity ,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.size_number_id,c.article_number order by b.id,size_order";
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
							$req_qty_arr=$trims->getQtyArray_by_OrderPrecostdtlsidGmtscolorGmtssizeAndArticle();
							$req_amount_arr=$trims->getAmountArray_by_OrderPrecostdtlsidGmtscolorGmtssizeAndArticle();

						 	$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.color_number_id,c.size_number_id,c.article_number,min(c.color_order) as color_order,min(c.size_order) as size_order,min(e.item_size) as item_size,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id,c.size_number_id,c.article_number  order by b.id, color_order,size_order";
                        }
                        else{
							$req_qty_arr=$trims->getQtyArray_by_orderAndPrecostdtlsid();
							$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsid();
							$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty order by b.id";
                        }

                        $po_color_level_data_arr=array();
                        $po_size_level_data_arr=array();

                        $po_no_sen_level_data_arr=array();
                        $po_color_size_level_data_arr=array();
                        $data_array=sql_select($sql);
						//echo $sql; die;
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
									$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]];
									$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
									$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");

									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_qty'][$row[csf('id')]]=$po_qty;
									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_id'][$row[csf('id')]]=$row[csf('id')];
									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];
									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['color_size_table_id'][$row[csf('id')]]=$row[csf('color_size_table_id')];
									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['article_number'][$row[csf('id')]]=$row[csf('article_number')];

									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_cons'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][cons];
									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_qty'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][requirment];
									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_amt'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][amount];

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
						if($pi_number){
							$piNumber=1;
						}
						$recvNumber=0;
						$recv_number=return_field_value( "recv_number", "inv_receive_master a,inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no='$txt_booking_no' and b.item_group_id='".$txt_trim_group_id."' and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
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
								if($item_color==0 || $item_color=="" ) $item_color = $row[csf('color_number_id')];

								$item_size=$booking_data_arr[$row[csf('color_size_table_id')]][item_size];
								if($item_size==0 || $item_size == "") $item_size=$size_library[$row[csf('size_number_id')]];

								$rate=$booking_data_arr[$row[csf('color_size_table_id')]][rate];
								if($rate==0 || $rate=="") $rate=$txt_avg_price;

								$description=$booking_data_arr[$row[csf('color_size_table_id')]][description];
								if($description=="") $description=trim($txt_pre_des);

								$brand_supplier=$booking_data_arr[$row[csf('color_size_table_id')]][brand_supplier];
								if($brand_supplier=="")$brand_supplier=trim($txt_pre_brand_sup);

								if($txtwoq_cal>0){
									$i++;
								?>
									<tr id="break_1" align="center">
                                        <td>DD<? echo $i;?></td>
                                        <td><input type="text" id="poarticle_<? echo $i;?>"  name="poarticle_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $row[csf('article_number')]; ?>"  readonly /></td>
                                        <td>
                                            <input type="text" id="pocolor_<? echo $i;?>"  name="pocolor_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $color_library[$row[csf('color_number_id')]]; ?>" <? if($gmt_color_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> readonly/>
                                            <input type="hidden" id="pocolorid_<? echo $i;?>"  name="pocolorid_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $row[csf('color_number_id')]; ?>" readonly />
                                            <input type="hidden" id="poid_<? echo $i;?>"  name="poid_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $row[csf('id')]; ?>" />
                                            <input type="hidden" id="poqty_<? echo $i;?>"  name="poqty_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $po_qty_arr[$row[csf('id')]]; ?>" readonly />
                                            <input type="hidden" id="poreqqty_<? echo $i;?>"  name="poreqqty_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $txtwoq_cal; ?>" readonly />
                                        </td>
                                        <td>
                                            <input type="text" id="gmtssizes_<? echo $i;?>"  name="gmtssizes_<? echo $i;?>" class="text_boxes" style="width:70px" value="<? echo $size_library[$row[csf('size_number_id')]]; ?>" <? if($gmt_size_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> readonly/>
                                            <input type="hidden" id="gmtssizesid_<? echo $i;?>"  name="gmtssizesid_<? echo $i;?>" class="text_boxes" style="width:70px" value="<? echo $row[csf('size_number_id')]; ?>" readonly />
                                        </td>
                                        <td><input type="text" id="des_<? echo $i;?>"  name="des_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $description;?>" onChange="copy_value(this.value,'des_',<? echo $i;?>)" <? if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> />
                                        </td>
                                        <td><input type="text" id="brndsup_<? echo $i;?>"  name="brndsup_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $brand_supplier; ?>" onChange="copy_value(this.value,'brndsup_',<? echo $i;?>)" <? if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?>/>
                                        </td>
                                        <td><input type="text" id="itemcolor_<? echo $i;?>"  value="<? echo $color_library[$item_color]; ?>"  name="itemcolor_<? echo $i;?>"  class="text_boxes" style="width:100px" onChange="copy_value(this.value,'itemcolor_',<? echo $i;?>)" <? if($item_color_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> />
                                        </td>
                                        <td><input type="text" id="itemsizes_<? echo $i;?>"  name="itemsizes_<? echo $i;?>"    class="text_boxes" style="width:80px" onChange="copy_value(this.value,'itemsizes_',<? echo $i;?>)" value="<? echo $item_size; ?>" <? if($item_size_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> />
                                        </td>
                                        <td><input type="hidden" id="reqqty_<? echo $i;?>"  name="reqqty_<? echo $i;?>" class="text_boxes_numeric" style="width:70px"    value="<? echo $txtwoq_cal ?>" readonly/>
                                        	<input type="text" id="qty_<? echo $i;?>" onChange="set_sum_value( 'qty_sum', 'qty_' );set_sum_value( 'woqty_sum', 'woqny_' );calculate_requirement(<? echo $i;?>);copy_value(this.value,'qty_',<? echo $i;?>)"  name="qty_<? echo $i;?>" class="text_boxes_numeric" style="width:70px"   placeholder="<? echo $txtwoq_cal; ?>" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][cons]; ?>"/>
                                        </td>
                                        <td>
                                        	<input type="text" id="excess_<? echo $i;?>" onBlur="set_sum_value( 'excess_sum', 'excess_' ) "  name="excess_<? echo $i;?>" class="text_boxes_numeric" style="width:40px" onChange="calculate_requirement(<? echo $i;?>);set_sum_value( 'excess_sum', 'excess_' );set_sum_value( 'woqty_sum', 'woqny_' );copy_value(this.value,'excess_',<? echo $i;?>) " value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][process_loss_percent]; ?>" disabled/>
                                        </td>
                                        <td>
                                        	<input type="text" id="woqny_<? echo $i;?>" onBlur="set_sum_value( 'woqty_sum', 'woqny_' )" onChange="set_sum_value( 'woqty_sum', 'woqny_' )" name="woqny_<? echo $i;?>" class="text_boxes_numeric" style="width:70px" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][requirment]; ?>" readonly />
                                        </td>
                                        <td>
                                        	<input type="text" id="rate_<? echo $i;?>"  name="rate_<? echo $i;?>" class="text_boxes_numeric" style="width:60px" onChange="calculate_amount(<? echo $i;?>);set_sum_value( 'amount_sum', 'amount_' );copy_value(this.value,'rate_',<? echo $i;?>) " value="<? echo $rate; ?>" <? if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> />
                                        </td>
                                        <td>
                                        	<input type="text" id="amount_<? echo $i;?>"  name="amount_<? echo $i;?>"  onBlur="set_sum_value( 'amount_sum', 'amount_' ) " class="text_boxes_numeric" style="width:60px" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][amount]; ?>" readonly>
                                        </td>
                                        <td>
                                            <input type="text" id="pcs_<? echo $i;?>"  name="pcs_<? echo $i;?>"  onBlur="set_sum_value( 'pcs_sum', 'pcs_' ) " class="text_boxes_numeric" style="width:50px"  value="<? echo $row[csf('order_quantity')]; ?>" readonly>
                                            <input type="hidden" id="pcsset_<? echo $i;?>"  name="pcsset_<? echo $i;?>"  onBlur="set_sum_value( 'pcs_sum', 'pcs_' ) " class="text_boxes_numeric" style="width:50px"  value="<? echo $row[csf('order_quantity_set')]; ?>" readonly>
                                            <input type="hidden" id="colorsizetableid_<? echo $i;?>"  name="colorsizetableid_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $row[csf('color_size_table_id')]; ?>" />
                                            <input type="hidden" id="updateid_<? echo $i;?>"  name="updateid_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][id]; ?>" readonly />
                                        </td>
										<? if($cbo_colorsizesensitive==0){ ?>
											<td>
												<input type="button" id="increasesensitivity_<?=$i; ?>" style="width:20px" class="formbutton" value="+" onClick="add_break_down_tr(<?=$i; ?>);" <?=$txt_disabled; ?> />
												<input type="button" id="decreasesensitivity_<?=$i; ?>" style="width:20px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<?=$i; ?> ,this);" <?=$txt_disabled; ?> />
											</td>
											<? } ?>
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
						 $sql="select min(b.id) as id ,min(c.id) as color_size_table_id,c.color_number_id,c.size_number_id,c.article_number,min(c.color_order) as color_order,min(c.size_order) as size_order,min(e.item_size) as item_size,sum(c.order_quantity) as order_quantity  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.color_number_id,c.size_number_id, c.article_number order by  color_order,size_order,c.article_number";
							$level_arr=$po_color_size_level_data_arr;
                        }
                        else{
							$sql="select b.job_no_mst,min(b.id) as id , min(c.id) as color_size_table_id,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.job_no_mst";
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

								}
								if($cbo_colorsizesensitive==2){
									$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['req_qty']),5,"");
									$po_qty=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_qty']);
									$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity_set']);
									$booking_cons=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_cons']),5,"");
									$booking_qty=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_qty']),5,"");
									$booking_amt=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_amt']),5,"");
								}
								if($cbo_colorsizesensitive==3){
									$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['req_qty']),5,"");
									$po_qty=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['po_qty']);
									$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['order_quantity_set']);
									$booking_cons=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_cons']),5,"");
									$booking_qty=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_qty']),5,"");
									$booking_amt=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_amt']),5,"");
								}
								if($cbo_colorsizesensitive==4){
									$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['req_qty']),5,"");
									$po_qty=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_qty']);
									$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity_set']);
									$booking_cons=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_cons']),5,"");
									$booking_qty=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_qty']),5,"");
									$booking_amt=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_amt']),5,"");
								}
								if($cbo_colorsizesensitive==0){
									/* $txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id]['req_qty']),5,"");
									$po_qty=array_sum($level_arr[$cbo_trim_precost_id]['po_qty']);
									$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id]['order_quantity_set']);
									$booking_cons=def_number_format(array_sum($level_arr[$cbo_trim_precost_id]['booking_cons']),5,"");
									$booking_qty=def_number_format(array_sum($level_arr[$cbo_trim_precost_id]['booking_qty']),5,"");
									$booking_amt=def_number_format(array_sum($level_arr[$cbo_trim_precost_id]['booking_amt']),5,""); */
									$item_color_id=$row[csf('item_color_number_id')];
									if($row[csf('item_color_number_id')]=='' || $row[csf('item_color_number_id')]=='0' || $color_library[$row[csf('item_color_number_id')]]=='' || $color_library[$row[csf('item_color_number_id')]]=='0')  $row[csf('item_color_number_id')]=$row[csf('color_number_id')];
									$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id]['req_qty']),8,"");
									$txtwoq_amt_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id]['req_amt']),5,"");
									$po_qty=array_sum($level_arr[$cbo_trim_precost_id]['po_qty']);
									$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id]['order_quantity_set']);
									$booking_cons=def_number_format(array_sum($level_arr[$cbo_trim_precost_id]['booking_cons']),8,"");
									$booking_qty=def_number_format(array_sum($level_arr[$cbo_trim_precost_id]['booking_qty']),8,"");
									$booking_amt=def_number_format(array_sum($level_arr[$cbo_trim_precost_id]['booking_amt']),8,"");
									$item_color = $booking_data_arr[$row[csf('color_size_table_id')]]['item_color'];
									if($item_color>0) $booking_item_color=$item_color;
									else $booking_item_color = $row[csf('item_color_number_id')];

									if(($row[csf('item_color_number_id')]=="" || $row[csf('item_color_number_id')]=="0") && ($booking_item_color=='0' || $booking_item_color=="") ) $booking_item_color = $row[csf('color_number_id')];

									//echo  $row[csf('item_color_number_id')].'='.$item_color.'='.$booking_item_color.'<br>';

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
										$remarks=$row[csf('remarks')];
									}
								}

								if(count($nosensbookdataArr)==0 || $cbo_colorsizesensitive!=0){
									$item_color = $booking_data_arr[$row[csf('color_size_table_id')]][item_color];
									if($item_color==0 || $item_color=="" ) $item_color = $row[csf('color_number_id')];

									$item_size=$booking_data_arr[$row[csf('color_size_table_id')]][item_size];
									if($item_size=='0' || $item_size == "") $item_size=$size_library[$row[csf('size_number_id')]];
									if($booking_amt>0)
									{
										$rate=$booking_amt/$booking_qty;
									}
									else
									{
										$rate=$txt_avg_price;
									}
									$rate=number_format($rate,6,'.','');

									$description=$booking_data_arr[$row[csf('color_size_table_id')]][description];
									if($description=="") $description=trim($txt_pre_des);
									$brand_supplier=$booking_data_arr[$row[csf('color_size_table_id')]][brand_supplier];
									if($brand_supplier=="") $brand_supplier=trim($txt_pre_brand_sup);
								}
								if($txtwoq_cal>0){
									$i++;
								?>
									<tr id="break_1" align="center">
                                        <td><? echo $i;?></td>
                                        <td><input type="text" id="poarticle_<? echo $i;?>"  name="poarticle_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $row[csf('article_number')]; ?>" readonly />
                                        </td>
                                        <td>
                                            <input type="text" id="pocolor_<? echo $i;?>"  name="pocolor_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $color_library[$row[csf('color_number_id')]]; ?>" <? if($gmt_color_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> readonly />
                                            <input type="hidden" id="pocolorid_<? echo $i;?>"  name="pocolorid_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $row[csf('color_number_id')]; ?>" readonly />
                                            <input type="hidden" id="poid_<? echo $i;?>"  name="poid_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $row[csf('id')]; ?>" readonly />
                                            <input type="hidden" id="poqty_<? echo $i;?>"  name="poqty_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $po_qty; ?>" readonly />
                                            <input type="hidden" id="poreqqty_<? echo $i;?>"  name="poreqqty_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $txtwoq_cal; ?>" readonly />
                                        </td>
                                        <td>
                                            <input type="text" id="gmtssizes_<? echo $i;?>"  name="gmtssizes_<? echo $i;?>" class="text_boxes" style="width:70px" value="<? echo $size_library[$row[csf('size_number_id')]]; ?>" <? if($gmt_size_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> readonly/>
                                            <input type="hidden" id="gmtssizesid_<? echo $i;?>"  name="gmtssizesid_<? echo $i;?>" class="text_boxes" style="width:70px" value="<? echo $row[csf('size_number_id')]; ?>" readonly />
                                        </td>
                                        <td><input type="text" id="des_<? echo $i;?>"  name="des_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $description;?>" onChange="copy_value(this.value,'des_',<? echo $i;?>)" <? if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> />
                                        </td>
                                        <td><input type="text" id="brndsup_<? echo $i;?>"  name="brndsup_<? echo $i;?>" class="text_boxes" style="width:100px" value="<? echo $brand_supplier; ?>" onChange="copy_value(this.value,'brndsup_',<? echo $i;?>)" <? if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> />
                                        </td>
                                        <td><input type="text" id="itemcolor_<? echo $i;?>"  value="<? echo $color_library[$item_color]; ?>" name="itemcolor_<? echo $i;?>" class="text_boxes" style="width:100px" onChange="copy_value(this.value,'itemcolor_',<? echo $i;?>)"   <? if($item_color_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> />
                                        </td>
                                        <td><input type="text" id="itemsizes_<? echo $i;?>"  name="itemsizes_<? echo $i;?>" class="text_boxes" style="width:70px" onChange="copy_value(this.value,'itemsizes_',<? echo $i;?>)" value="<? echo $item_size; ?>"  <? if($item_size_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?>/>
                                        </td>
                                        <td><input type="hidden" id="reqqty_<? echo $i;?>"  name="reqqty_<? echo $i;?>" class="text_boxes_numeric" style="width:70px"    value="<? echo $txtwoq_cal ?>" readonly/>

                                        	<input type="text" id="qty_<? echo $i;?>" onChange="set_sum_value( 'qty_sum', 'qty_' );set_sum_value( 'woqty_sum', 'woqny_' );calculate_requirement(<? echo $i;?>);copy_value(this.value,'qty_',<? echo $i;?>)"  name="qty_<? echo $i;?>" class="text_boxes_numeric" style="width:80px"   placeholder="<? echo $txtwoq_cal; ?>" value="<? if($booking_cons>0){echo $booking_cons;} ?>"/>
                                        </td>
                                        <td>
                                        	<input type="text" id="excess_<? echo $i;?>" onBlur="set_sum_value( 'excess_sum', 'excess_' ) "  name="excess_<? echo $i;?>" class="text_boxes_numeric" style="width:40px" onChange="calculate_requirement(<? echo $i;?>);set_sum_value( 'excess_sum', 'excess_' );set_sum_value( 'woqty_sum', 'woqny_' );copy_value(this.value,'excess_',<? echo $i;?>) " value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]]['process_loss_percent']; ?>" disabled/>
                                        </td>
                                        <td><input type="text" id="woqny_<? echo $i;?>" onBlur="set_sum_value( 'woqty_sum', 'woqny_' ) " onChange="set_sum_value( 'woqty_sum', 'woqny_' )"  name="woqny_<? echo $i;?>" class="text_boxes_numeric" style="width:70px" value="<?  if($booking_qty){echo $booking_qty;} ?>" readonly />
                                        </td>
                                        <td><input type="text" id="rate_<? echo $i;?>"  name="rate_<? echo $i;?>" class="text_boxes_numeric" style="width:60px" onChange="calculate_amount(<? echo $i;?>);set_sum_value( 'amount_sum', 'amount_' );copy_value(this.value,'rate_',<? echo $i;?>) " value="<? echo $rate; ?>" <? if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> />
                                        </td>
                                        <td><input type="text" id="amount_<? echo $i;?>"  name="amount_<? echo $i;?>"  onBlur="set_sum_value( 'amount_sum', 'amount_' ) " class="text_boxes_numeric" style="width:60px"  value="<? echo $booking_amt; //$booking_data_arr[$row[csf('color_size_table_id')]][amount]; ?>" readonly>
                                        </td>

                                        <td><input type="text" id="pcs_<? echo $i;?>"  name="pcs_<? echo $i;?>"  onBlur="set_sum_value( 'pcs_sum', 'pcs_' ) " class="text_boxes_numeric" style="width:50px"  value="<? echo $row[csf('order_quantity')]; ?>" readonly>
                                            <input type="hidden" id="pcsset_<? echo $i;?>"  name="pcsset_<? echo $i;?>"  onBlur="set_sum_value( 'pcs_sum', 'pcs_' ) " class="text_boxes_numeric" style="width:50px"  value="<? echo $order_quantity_set; ?>" readonly>
                                            <input type="hidden" id="colorsizetableid_<? echo $i;?>"  name="colorsizetableid_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $row[csf('color_size_table_id')]; ?>" />
                                            <input type="hidden" id="updateid_<? echo $i;?>"  name="updateid_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][id]; ?>" readonly />
                                        </td>
										<? if($cbo_colorsizesensitive==0){ ?>
											<td>
												<input type="button" id="increasesensitivity_<?=$i; ?>" style="width:20px" class="formbutton" value="+" onClick="add_break_down_tr(<?=$i; ?>);" <? if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> />
												<input type="button" id="decreasesensitivity_<?=$i; ?>" style="width:20px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<?=$i; ?>,'this');" <? if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> />
											</td> <? } ?>
									</tr>
								<?
								}
							}
                        }
                        ?>
                        </tbody>
                        <tfoot>
                            <tr>
                               <th width="40">&nbsp;</th><th width="100">&nbsp;</th><th width="100">&nbsp;</th><th width="70">&nbsp;</th><th width="100">&nbsp;</th><th width="100">&nbsp;</th><th width="100">&nbsp;</th>
                                <th width="80">&nbsp;</th>
                                <th width="70"><input type="text" id="qty_sum" name="qty_sum" class="text_boxes_numeric" style="width:70px"  readonly></th>
                                <th width="40"><input type="text" id="excess_sum"  name="excess_sum" class="text_boxes_numeric" style="width:40px" readonly></th>
                                <th width="70"><input type="text" id="woqty_sum"  name="woqty_sum" class="text_boxes_numeric" style="width:70px" readonly></th>
                                <th width="40"><input type="text" id="rate_sum"  name="rate_sum" class="text_boxes_numeric" style="width:60px" readonly></th>
                                <th width="50"><input type="text" id="amount_sum" name="amount_sum" class="text_boxes_numeric" style="width:60px" readonly></th>
                                <th><input type="hidden" id="json_data" name="json_data" class="text_boxes_numeric" style="width:50px" value='<? echo json_encode($level_arr); ?>' readonly>
                                	<input type="text" id="pcs_sum" name="pcs_sum" class="text_boxes_numeric" style="width:50px" readonly>
                                </th>
								<? if($cbo_colorsizesensitive==0){ ?><th></th> <? } ?>
                            </tr>
                        </tfoot>
                    </table>
                    <table width="1250" cellspacing="0" class="" border="0" rules="all">
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
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
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
	$sql_po_qty=sql_select("select b.id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id in($txt_po_id)  $txt_country_cond  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty"); //,c.item_number_id
	$tot_po_qty=0;
	foreach($sql_po_qty as$sql_po_qty_row){
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

		$req_qty_arr=$trims->getQtyArray_by_OrderPrecostdtlsidGmtscolorGmtssizeAndArticle();
		$req_amount_arr=$trims->getAmountArray_by_OrderPrecostdtlsidGmtscolorGmtssizeAndArticle();

		 $sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.color_number_id,c.size_number_id,c.article_number,min(c.color_order) as color_order,min(c.size_order) as size_order,min(e.item_size) as item_size,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id,c.size_number_id,c.article_number  order by b.id, color_order,size_order";
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
				$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]];
				$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
				$cu_qnty_ord_uom = def_number_format($cu_booking_data_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]],5,"");
				//$req_qnty_ord_uom = $req_qnty_ord_uom - $cu_qnty_ord_uom;
				//$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");
				$req_qnty_ordUom = def_number_format((($data[14]/$data[8])*$req_qnty_ord_uom),5,"");
				$txtwoq_cal = def_number_format($req_qnty_ordUom,5,"");
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");

				$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
				$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_qty'][$row[csf('id')]]=$po_qty;
				$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
				$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_id'][$row[csf('id')]]=$row[csf('id')];
				$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];

				$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['color_size_table_id'][$row[csf('id')]]=$row[csf('color_size_table_id')];

				$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['amount'][$row[csf('id')]]=$amount;
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
				$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]];
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
	$gmt_color_edb="";
	$item_color_edb="";
	$gmt_size_edb="";
	$item_size_edb="";
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
		$sql="select min(b.id) as id ,min(c.id) as color_size_table_id,c.color_number_id,c.size_number_id,c.article_number,min(c.color_order) as color_order,min(c.size_order) as size_order,min(e.item_size) as item_size,min(e.item_size) as item_size,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.color_number_id,c.size_number_id,c.article_number  order by  color_order,size_order";
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
				$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['req_qty']),5,"");
				$po_qty=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_qty']);
				$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity_set']);
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
	if(str_replace("'","",$txt_booking_no)!='')
	{
		$sql=sql_select("select a.trims_del,b.delevery_qty  from trims_delivery_mst a, trims_delivery_dtls b where a.id=b.mst_id and b.order_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach($sql as $row)
		{
			$trims_del_no.=$row[csf('trims_del')].",";
			$trims_del_qty +=$row[csf('delevery_qty')];
		}
		$book_qty=return_field_value( "sum(b.wo_qnty) as wo_qnty", "wo_booking_mst a,wo_booking_dtls b","a.booking_no=b.booking_no and a.company_id=$cbo_company_name and a.booking_type in (2,5) and a.booking_no=$txt_booking_no","wo_qnty");
		//echo "10**".$trims_del_qty."**".$book_qty; die;
		if($book_qty > 0)
		{
			if( $book_qty <= $trims_del_qty  )
			{
				$trims_del_no=implode(", ",array_unique(explode(",",chop($trims_del_no,','))));
				echo "delQtyExeed**".$trims_del_no;
				 disconnect($con);die;
			}
		}
	}

	if ($operation==0){
		$con = connect();
		if($db_type==0){
		mysql_query("BEGIN");
		}
		if($db_type==0){
			$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'TB', date("Y",time()), 5, "select booking_no_prefix, booking_no_prefix_num from wo_booking_mst where company_id=$cbo_company_name and booking_type in(2,5) and YEAR(insert_date)=".date('Y',time())." order by id desc ", "booking_no_prefix", "booking_no_prefix_num" ));
		}
		else if($db_type==2){
			$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'TB', date("Y",time()), 5, "select booking_no_prefix, booking_no_prefix_num from wo_booking_mst where company_id=$cbo_company_name and booking_type in(2,5) and to_char(insert_date,'YYYY')=".date('Y',time())." order by id desc ", "booking_no_prefix", "booking_no_prefix_num" ));
		}
		$id=return_next_id( "id", "wo_booking_mst", 1 ) ;
		$field_array="id,booking_type,is_short,booking_month,booking_year,booking_no_prefix,booking_no_prefix_num,booking_no,company_id,buyer_id, 	item_category,supplier_id,currency_id,booking_date,delivery_date,pay_mode,source,fabric_source,attention,remarks,item_from_precost,entry_form,garments_nature,cbo_level,ready_to_approved,inserted_by,insert_date";
		$data_array ="(".$id.",2,2,".$cbo_booking_month.",".$cbo_booking_year.",'".$new_booking_no[1]."',".$new_booking_no[2].",'".$new_booking_no[0]."',".$cbo_company_name.",".$cbo_buyer_name.",4,".$cbo_supplier_name.",".$cbo_currency.",".$txt_booking_date.",".$txt_delivery_date.",".$cbo_pay_mode.",".$cbo_source.",".$cbo_material_source.",".$txt_attention.",".$txt_remarks.",1,252,".$garments_nature.",".$cbo_level.",".$cbo_ready_to_approved.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
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
		elseif($db_type==2 || $db_type==1 ){
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
		if($is_approved==3){
			$is_approved=1;
		}
		if($is_approved==1){
			echo "app1**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;
		}
		//if(str_replace("'","",$cbo_pay_mode)==2){
		$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($pi_number){
				echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
				disconnect($con);die;
			}
	/*	}else{*/
			$recv_number=return_field_value( "recv_number", "inv_receive_master a,inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no  and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($recv_number){
				echo "recv1**".str_replace("'","",$txt_booking_no)."**".$recv_number;
				disconnect($con);die;
			}
		//}

		$flag=1;
		$is_received_id=return_field_value( "id", "subcon_ord_mst","order_no=$txt_booking_no and order_id is not null and entry_form=255 and status_active=1 and is_deleted=0");
		//echo "10** select id from subcon_ord_mst where order_no=$txt_booking_no and order_id is not null and entry_form=255".$is_received_id; die;
		if($is_received_id!='')
		{
			$field_array_rec_up="is_apply_last_update*updated_by*update_date";
			$data_array_rec_up ="2*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$rID_rec=sql_update("subcon_ord_mst",$field_array_rec_up,$data_array_rec_up,"id","".$is_received_id."",0);
			if($rID_rec) $flag=1; else $flag=0;
		}

		$field_array_up="booking_month*booking_year*supplier_id*currency_id*booking_date*delivery_date*pay_mode*source*fabric_source*attention*remarks*item_from_precost*cbo_level*ready_to_approved*updated_by*update_date*revised_no";

		$data_array_up ="".$cbo_booking_month."*".$cbo_booking_year."*".$cbo_supplier_name."*".$cbo_currency."*".$txt_booking_date."*".$txt_delivery_date."*".$cbo_pay_mode."*".$cbo_source."*".$cbo_material_source."*".$txt_attention."*".$txt_remarks."*1*".$cbo_level."*".$cbo_ready_to_approved."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*revised_no+1";
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
		if($is_approved==3){
			$is_approved=1;
		}
		if($is_approved==1){
			echo "app1**".str_replace("'","",$txt_booking_no);
			 disconnect($con);die;
		}
		//if(str_replace("'","",$cbo_pay_mode)==2){
		$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no  and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($pi_number){
				echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
				 disconnect($con);die;
			}
		/*}else{*/
			$recv_number=return_field_value( "recv_number", "inv_receive_master a,inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no  and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($recv_number){
				echo "recv1**".str_replace("'","",$txt_booking_no)."**".$recv_number;
				 disconnect($con);die;
			}
		//}
		$is_received_id=return_field_value( "id", "subcon_ord_mst","order_no=$txt_booking_no and order_id is not null and entry_form=255 and status_active=1 and is_deleted=0");
		//echo "10** select id from subcon_ord_mst where order_no=$txt_booking_no and order_id is not null and entry_form=255".$is_received_id; die;
		$rID_rec=1;
		if($is_received_id!='')
		{
			echo "orderFound**"; disconnect($con); die;
		}

		$delete_cause=str_replace("'","",$delete_cause);
		$delete_cause=str_replace('"','',$delete_cause);
		$delete_cause=str_replace('(','',$delete_cause);
		$delete_cause=str_replace(')','',$delete_cause);

		//$rID=execute_query( "delete from wo_booking_mst where  booking_no =".$txt_booking_no."",0);
		//$rID1=execute_query( "delete from wo_booking_dtls where  booking_no =".$txt_booking_no."",0);
		//$rID2=execute_query( "delete from wo_trim_book_con_dtls where  booking_no =".$txt_booking_no."",0);
		if(str_replace("'","",$delete_type)==1)
		{
			$rID=execute_query( "update wo_booking_mst set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."'   where  booking_no=$txt_booking_no",0);
			$rID1=execute_query( "update wo_booking_dtls set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."'   where  booking_no=$txt_booking_no",0);
			$rID1=execute_query( "update wo_trim_book_con_dtls set status_active=0,is_deleted=1 where  booking_no=$txt_booking_no",0);
		}
		else
		{
			$rID=1;
			$rID1=execute_query( "update wo_booking_dtls set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."'   where  booking_no=$txt_booking_no",0);
			$rID1=execute_query( "update wo_trim_book_con_dtls set status_active=0,is_deleted=1 where  booking_no=$txt_booking_no",0);
		}

		if($db_type==0){
			if($rID  && $rID1){
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		else if($db_type==2 || $db_type==1 ){
			if($rID  && $rID1){
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
	$color_library=return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$booking_mst_id= str_replace("'","",$booking_mst_id);
	$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_booking_no");
	if($is_approved==3){
		$is_approved=1;
	}
	if($is_approved==1){
		echo "app1**".str_replace("'","",$txt_booking_no);
		disconnect($con);die;
	}

	if(str_replace("'","",$txt_booking_no)!='' )
	{
		$sql=sql_select("select a.trims_del,b.booking_dtls_id,b.delevery_qty  from trims_delivery_mst a, trims_delivery_dtls b where a.id=b.mst_id and b.order_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		$delivery_qty_arr=array();
		foreach($sql as $row)
		{
			$delivery_qty_arr[$row[csf('booking_dtls_id')]]['delevery_qty'] +=$row[csf('delevery_qty')];
			$delivery_qty_arr[$row[csf('booking_dtls_id')]]['trims_del'] .=$row[csf('trims_del')].",";
		}
	}

	$flag=1;
	$is_received_id=return_field_value( "id", "subcon_ord_mst","order_no=$txt_booking_no and order_id is not null and entry_form=255 and status_active=1 and is_deleted=0");
	//echo "10** select id from subcon_ord_mst where order_no=$txt_booking_no and order_id is not null and entry_form=255".$is_received_id; die;
	if($is_received_id!='')
	{
		$field_array_rec_up="is_apply_last_update*updated_by*update_date";
		$data_array_rec_up ="2*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID_rec=sql_update("subcon_ord_mst",$field_array_rec_up,$data_array_rec_up,"id","".$is_received_id."",0);
		if($rID_rec) $flag=1; else $flag=0;
	}

	$sql_lib_item_group_array=array();
	$sql_lib_item_group=sql_select("select id, item_name,conversion_factor,order_uom as cons_uom from lib_item_group");
	foreach($sql_lib_item_group as $row_sql_lib_item_group){
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]]['conversion_factor']=$row_sql_lib_item_group[csf('conversion_factor')];
	}

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

	if ($operation==0){
		$curr_book_amount_job_level=array();
		$curr_book_amount_job_item_level=array();
		$jobArr=array(); $poArr=array(); $pre_trim_id_arr=array(); $des_data=""; $brand_data="";
		for ($i=1;$i<=$total_row;$i++){
			$txttrimcostid="txttrimcostid_".$i;
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

			$JoBc=$$txtjob_id;
			$condition= new condition();
			if(str_replace("'","",$$txtjob_id) !=''){
				$condition->job_no("=$JoBc");
			}

			$condition->init();
			$trims= new trims($condition);

			$pretrimcostid=str_replace("'","",$$txttrimcostid);
			$poid=str_replace("'","",$$txtpoid);
			$bookingdtlsid=str_replace("'","",$$txtbookingid);
			$trims_del_qty=$delivery_qty_arr[$bookingdtlsid]['delevery_qty'];
			if($trims_del_qty>0)
			{
				if( str_replace("'","",$$txtreqqnty) <= $trims_del_qty)
				{
					$trims_del_no=$delivery_qty_arr[$bookingdtlsid]['trims_del'];
					$trims_del_no=implode(", ",array_unique(explode(",",chop($trims_del_no,','))));
					echo "delQtyExeed**".$trims_del_no;
					disconnect($con);die;
				}
			}

			$conversion_factor=$sql_lib_item_group_array[str_replace("'","",$$txttrimgroup)]['conversion_factor'];

			$reqQtyItemLevelArr=$trims->getQtyArray_by_precostdtlsid();
			$reqAmountItemLevelArr=$trims->getAmountArray_precostdtlsid();
			$reqAmountJobLevelArr=$trims->getAmountArray_by_job();

			$curr_book_amount_job_level[str_replace("'","",$$txtjob_id)]['req_amount']=$reqAmountJobLevelArr[str_replace("'","",$$txtjob_id)];
			$curr_book_amount_job_level[str_replace("'","",$$txtjob_id)]['cur_amount']+=(str_replace("'","",$$txtamount)/str_replace("'","",$$txtexchrate));
			$curr_book_amount_job_level[str_replace("'","",$$txtjob_id)]['prev_amount']=0;

			$curr_book_amount_job_item_level[str_replace("'","",$$txtjob_id)][str_replace("'","",$$txttrimcostid)]['req_amount']+=$reqAmountItemLevelArr[$pretrimcostid];
			$curr_book_amount_job_item_level[str_replace("'","",$$txtjob_id)][str_replace("'","",$$txttrimcostid)]['cur_amount']+=(str_replace("'","",$$txtamount)/str_replace("'","",$$txtexchrate));
			$curr_book_amount_job_item_level[str_replace("'","",$$txtjob_id)][str_replace("'","",$$txttrimcostid)]['prev_amount']=0;

			$curr_book_qty_job_item_level[str_replace("'","",$$txtjob_id)][$pretrimcostid]['req_qty']+=$reqQtyItemLevelArr[$pretrimcostid];
			$curr_book_qty_job_item_level[str_replace("'","",$$txtjob_id)][$pretrimcostid]['cur_qty']+=(str_replace("'","",$$txtreqqnty)*$conversion_factor);
			$curr_book_qty_job_item_level[str_replace("'","",$$txtjob_id)][$pretrimcostid]['prev_qty']=0;


			$jobArr[$$txtjob_id]=$$txtjob_id;
			$poArr[$poid]=$poid;
			$pre_trim_id_arr[$pretrimcostid]=$pretrimcostid;
			$des_arr[$$txtdesc]=$$txtdesc;
			$brand_arr[$$txtbrandsup]=$$txtbrandsup;
			$booking_dtls_id_arr[$bookingdtlsid]=$bookingdtlsid;
		}


		$prev_book_amount_job_level=array();
		$prev_book_amount_job_item_level=array();
		$sql=sql_select("select  job_no, pre_cost_fabric_cost_dtls_id, trim_group, wo_qnty, amount, exchange_rate from wo_booking_dtls where job_no in(".implode(",",$jobArr).") and booking_type=2 and is_short=2 and po_break_down_id in($poid)  and status_active=1 and is_deleted=0");
		foreach($sql as $row){
			$curr_book_amount_job_level[$row[csf('job_no')]]['prev_amount']+=($row[csf('amount')]/$row[csf('exchange_rate')]);
			$curr_book_amount_job_item_level[$row[csf('job_no')]][$row[csf('pre_cost_fabric_cost_dtls_id')]]['prev_amount']+=($row[csf('amount')]/$row[csf('exchange_rate')]);
			$curr_book_qty_job_item_level[$row[csf('job_no')]][$row[csf('pre_cost_fabric_cost_dtls_id')]]['prev_qty']+=($row[csf('wo_qnty')]*$sql_lib_item_group_array[$row[csf('trim_group')]]['conversion_factor']);
		}

		$con = connect();
		if($db_type==0){
			mysql_query("BEGIN");
		}
		if( check_table_status( $_SESSION['menu_id'], 1 )==0 ){
			echo "15**0";
			disconnect($con);die;
		}

		if(str_replace("'","",implode(",",$des_arr))!="") $des_data_cond="and description in(".implode(",",$des_arr).")"; else $des_data_cond="";
		if(str_replace("'","",implode(",",$brand_arr))!="") $brand_data_cond="and brand_supplier in(".implode(",",$brand_arr).")"; else $brand_data_cond="";

		//if (is_duplicate_field( "booking_no", "wo_booking_dtls", "job_no in(".implode(",",$jobArr).") and booking_type=2 and is_short=2 and booking_no=$txt_booking_no and status_active=1 and is_deleted=0 $des_data_cond $brand_data_cond") == 1)
		if (is_duplicate_field( "booking_no", "wo_booking_dtls", "job_no in(".implode(",",$jobArr).") and po_break_down_id in (".implode(",",$poArr).") and pre_cost_fabric_cost_dtls_id in(".implode(",",$pre_trim_id_arr).") and booking_type=2 and is_short=2 and booking_no=$txt_booking_no and status_active=1 and is_deleted=0 $des_data_cond $brand_data_cond") == 1)// and id not in(".implode(",",$booking_dtls_id_arr).")
		{
			echo "11**0";
			check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);
			die;
		}

		$id_dtls=return_next_id( "id", "wo_booking_dtls", 1 ) ;
		$field_array1="id, pre_cost_fabric_cost_dtls_id, po_break_down_id, job_no, booking_no, booking_mst_id, booking_type, is_short, trim_group,description,brand_supplier, uom, sensitivity, wo_qnty, exchange_rate, rate, amount, delivery_date, country_id_string,entry_form_id, inserted_by, insert_date";

		$field_array2="id,wo_trim_booking_dtls_id,booking_no, booking_mst_id, job_no,po_break_down_id,color_number_id,gmts_sizes,description,brand_supplier,item_color,item_size,cons, process_loss_percent, requirment, rate, amount, pcs, color_size_table_id, article_number";

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

			$txtdesc="txtdesc_".$i;
			$txtbrandsup="txtbrandsup_".$i;
			$txtreqamount="txtreqamount_".$i;
			$txtreqamountjoblevelconsuom="txtreqamountjoblevelconsuom_".$i;
			$txtreqamountitemlevelconsuom="txtreqamountitemlevelconsuom_".$i;

			$pi_number=array();
			$piquantity=0;
			$sqlPi=sql_select("select a.pi_number, b.quantity from  com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id  and b.work_order_no=$txt_booking_no and b.item_group=".$$txttrimgroup." and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			foreach($sqlPi as $rowPi){
				$pi_number[$rowPi[csf('pi_number')]]=$rowPi[csf('pi_number')];
				$piquantity+=$rowPi[csf('quantity')];
			}
			$uom_id=str_replace("'","",$$txtuom);
			if($piquantity && str_replace("'","",$$txtwoq) < $piquantity){
				echo "pi1**".str_replace("'","",$txt_booking_no)."**".implode(",",$pi_number)."**".$piquantity;
				check_table_status( $_SESSION['menu_id'],0);
				disconnect($con);die;
			}

			$recv_number=array();
			$recvquantity=0;
			if(str_replace("'","",$$txtdesc)=="" || str_replace("'","",$$txtdesc)==0) $desc_con=""; else $desc_con=" and b.item_description=".$$txtdesc." ";
			if(str_replace("'","",$$txtpoid)=="" || str_replace("'","",$$txtpoid)==0) $poid_con=""; else $poid_con=" and c.po_breakdown_id in (".str_replace("'","",$$txtpoid).") ";
			$sqlRecv=sql_select("select a.recv_number, c.quantity as receive_qnty from  inv_receive_master a,inv_trims_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and c.trans_type=1 and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and b.item_group_id=".$$txttrimgroup." and a.item_category=4 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 $desc_con $poid_con");
			foreach($sqlRecv as $rowRecv){
				$recv_number[$rowRecv[csf('recv_number')]]=$rowRecv[csf('recv_number')];
				$recvquantity+=$rowRecv[csf('receive_qnty')];
			}

			if($recvquantity>0)
			{
				if($recvquantity>0 && $recvquantity<=str_replace("'","",$$txtwoq))
				{
					check_table_status( $_SESSION['menu_id'],0);
				}
				else
				{
					echo "recv1**".str_replace("'","",$txt_booking_no)."**".implode(",",$recv_number)."**".$recvquantity;
					check_table_status( $_SESSION['menu_id'],0);
					 disconnect($con);die;
				}
			}

			$job=str_replace("'","",$$txtjob_id);
			$trimcostid=str_replace("'","",$$txttrimcostid);
			$amt=str_replace("'","",$$txtamount);
			$exRate=str_replace("'","",$$txtexchrate);
			$uom_id=str_replace("'","",$$txtuom);
			$conversion_factor=$sql_lib_item_group_array[str_replace("'","",$$txttrimgroup)]['conversion_factor'];

			if($amount_exceed_level==1){
				$reqAmt=(($curr_book_amount_job_level[$job]['req_amount']*$exeed_budge_amount)/100)+$curr_book_amount_job_level[$job]['req_amount'];
				$curAmt=$curr_book_amount_job_level[$job]['prev_amount']+($amt/$exRate);
				if(($curAmt-$reqAmt)>1){
					echo "vad1**".str_replace("'","",$txt_booking_no)."**".$i;
					check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);die;
				}
				$curr_book_amount_job_level[$job]['prev_amount']+=($amt/$exRate);
			}

			if($amount_exceed_level==2){
				$reqAmt=(($curr_book_amount_job_item_level[$job][$trimcostid]['req_amount']*$exeed_budge_amount)/100)+$curr_book_amount_job_item_level[$job][$trimcostid]['req_amount'];
				$curAmt=$curr_book_amount_job_item_level[$job][$trimcostid]['prev_amount']+($amt/$exRate);
				if(($curAmt-$reqAmt)>1){
					echo "vad1**".str_replace("'","",$txt_booking_no)."**".$i."**".$curAmt."**".$reqAmt."**".$curr_book_amount_job_item_level[$job][$trimcostid]['req_amount'];
					check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);die;
				}
				$curr_book_amount_job_item_level[$job][$trimcostid]['prev_amount']+=($amt/$exRate);
			}

			if($exceed_qty_level==2){
				$reqQty=(($curr_book_qty_job_item_level[$job][$trimcostid]['req_qty']*$exeed_budge_qty)/100)+$curr_book_qty_job_item_level[$job][$trimcostid]['req_qty'];
				$curQty=$curr_book_qty_job_item_level[$job][$trimcostid]['prev_qty']+($woq*$conversion_factor);
				if(($curQty-$reqQty)>1){
					echo "vad2**".str_replace("'","",$txt_booking_no)."**".$i."**".$curQty."**".$reqQty."**".$curr_book_qty_job_item_level[$job][$trimcostid]['req_qty'];
					check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);die;
				}
				$curr_book_qty_job_item_level[$job][$trimcostid]['prev_qty']+=($woq*$conversion_factor);
			}


			$data_array1 ="(".$id_dtls.",".$$txttrimcostid.",".$$txtpoid.",".$$txtjob_id.",".$txt_booking_no.",".$booking_mst_id.",2,2,".$$txttrimgroup.",".$$txtdesc.",".trim($$txtbrandsup).",".$$txtuom.",".$$cbocolorsizesensitive.",".$$txtwoq.",".$$txtexchrate.",".$$txtrate.",".$$txtamount.",".$$txtddate.",".$$txtcountry.",252,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

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
							$color_id = return_id( str_replace("'","",$consbreckdownarr[4]), $color_library, "lib_color", "id,color_name","252");
							$new_array_color[$color_id]=str_replace("'","",$consbreckdownarr[4]);
						}
						else $color_id =  array_search(str_replace("'","",$consbreckdownarr[4]), $new_array_color);
					}
					else $color_id =0;
					if ($c!=0) $data_array2 .=",";
					$data_array2 .="(".$id1.",".$id_dtls.",".$txt_booking_no.",".$booking_mst_id.",".$$txtjob_id.",".$$txtpoid.",'".$consbreckdownarr[0]."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".trim($consbreckdownarr[3])."','".$color_id."','".$consbreckdownarr[5]."','".$consbreckdownarr[6]."','".$consbreckdownarr[7]."','".$consbreckdownarr[8]."','".$consbreckdownarr[9]."','".$consbreckdownarr[10]."','".$consbreckdownarr[11]."','".$consbreckdownarr[12]."','".$consbreckdownarr[14]."')";
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
		if($db_type==2 || $db_type==1 ){
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

			$JoBc=$$txtjob_id;
			$condition= new condition();

			if(str_replace("'","",$$txtjob_id) !=''){
				$condition->job_no("=$JoBc");
			}

			$condition->init();
			$trims= new trims($condition);
			//$req_qty_arr=$trims->getQtyArray_by_orderAndPrecostdtlsid();
			//$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsid();

			$reqQtyItemLevelArr=$trims->getQtyArray_by_precostdtlsid();
			$reqAmountItemLevelArr=$trims->getAmountArray_precostdtlsid();

			$reqAmountJobLevelArr=$trims->getAmountArray_by_job();

			$pretrimcostid=str_replace("'","",$$txttrimcostid);
			$poid=str_replace("'","",$$txtpoid);
			$bookingdtlsid=str_replace("'","",$$txtbookingid);

			$conversion_factor=$sql_lib_item_group_array[str_replace("'","",$$txttrimgroup)]['conversion_factor'];


			$curr_book_amount_job_level[str_replace("'","",$$txtjob_id)]['req_amount']=$reqAmountJobLevelArr[str_replace("'","",$$txtjob_id)];
			$curr_book_amount_job_level[str_replace("'","",$$txtjob_id)]['cur_amount']+=(str_replace("'","",$$txtamount)/str_replace("'","",$$txtexchrate));
			$curr_book_amount_job_level[str_replace("'","",$$txtjob_id)]['prev_amount']=0;

			$curr_book_amount_job_item_level[str_replace("'","",$$txtjob_id)][str_replace("'","",$$txttrimcostid)]['req_amount']+=$reqAmountItemLevelArr[$pretrimcostid];
			$curr_book_amount_job_item_level[str_replace("'","",$$txtjob_id)][str_replace("'","",$$txttrimcostid)]['cur_amount']+=(str_replace("'","",$$txtamount)/str_replace("'","",$$txtexchrate));
			$curr_book_amount_job_item_level[str_replace("'","",$$txtjob_id)][str_replace("'","",$$txttrimcostid)]['prev_amount']=0;

			$curr_book_qty_job_item_level[str_replace("'","",$$txtjob_id)][$pretrimcostid]['req_qty']+=$reqQtyItemLevelArr[$pretrimcostid];
			$curr_book_qty_job_item_level[str_replace("'","",$$txtjob_id)][$pretrimcostid]['cur_qty']+=(str_replace("'","",$$txtreqqnty)*$conversion_factor);
			$curr_book_qty_job_item_level[str_replace("'","",$$txtjob_id)][$pretrimcostid]['prev_qty']=0;


			$jobArr[$$txtjob_id]=$$txtjob_id;
			$poArr[$poid]=$poid;
			$pre_trim_id_arr[$pretrimcostid]=$pretrimcostid;
			$des_arr[$$txtdesc]=$$txtdesc;
			$brand_arr[$$txtbrandsup]=$$txtbrandsup;
			$booking_dtls_id_arr[$bookingdtlsid]=$bookingdtlsid;
		}

		$prev_book_amount_job_level=array();
		$prev_book_amount_job_item_level=array();
		$sql=sql_select("select  job_no,pre_cost_fabric_cost_dtls_id,trim_group,wo_qnty,amount,exchange_rate from wo_booking_dtls where job_no in(".implode(",",$jobArr).") and booking_type=2 and is_short=2 and booking_no !=$txt_booking_no and po_break_down_id in($poid)  and status_active=1 and is_deleted=0");
		foreach($sql as $row){
			$curr_book_amount_job_level[$row[csf('job_no')]]['prev_amount']+=($row[csf('amount')]/$row[csf('exchange_rate')]);
			$curr_book_amount_job_item_level[$row[csf('job_no')]][$row[csf('pre_cost_fabric_cost_dtls_id')]]['prev_amount']+=($row[csf('amount')]/$row[csf('exchange_rate')]);
		    $curr_book_qty_job_item_level[$row[csf('job_no')]][$row[csf('pre_cost_fabric_cost_dtls_id')]]['prev_qty']+=($row[csf('wo_qnty')]*$sql_lib_item_group_array[$row[csf('trim_group')]]['conversion_factor']);

		}
		$con = connect();
		if($db_type==0){
		mysql_query("BEGIN");
		}
		if( check_table_status( $_SESSION['menu_id'], 1 )==0 ){
			echo "15**1";
			disconnect($con);die;
		}

		if(str_replace("'","",implode(",",$des_arr))!="") $des_data_cond="and description in(".implode(",",$des_arr).")"; else $des_data_cond="";
		if(str_replace("'","",implode(",",$brand_arr))!="") $brand_data_cond="and brand_supplier in(".implode(",",$brand_arr).")"; else $brand_data_cond="";

		//if (is_duplicate_field( "booking_no", "wo_booking_dtls", "job_no in(".implode(",",$jobArr).") and booking_type=2 and is_short=2 and booking_no=$txt_booking_no and status_active=1 and is_deleted=0 $des_data_cond $brand_data_cond") == 1)
		if (is_duplicate_field( "booking_no", "wo_booking_dtls", "job_no in(".implode(",",$jobArr).") and po_break_down_id in (".implode(",",$poArr).") and pre_cost_fabric_cost_dtls_id in(".implode(",",$pre_trim_id_arr).") and id not in(".implode(",",$booking_dtls_id_arr).") and booking_type=2 and is_short=2 and booking_no=$txt_booking_no and status_active=1 and is_deleted=0 $des_data_cond $brand_data_cond") == 1)
		{
			echo "11**0";
			check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);die;
		}
		$field_array_up1="pre_cost_fabric_cost_dtls_id*po_break_down_id*job_no*booking_no*trim_group*description*brand_supplier*uom*sensitivity*wo_qnty*exchange_rate*rate*amount*delivery_date*country_id_string*updated_by*update_date";
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

			$txtdesc="txtdesc_".$i;
			$txtbrandsup="txtbrandsup_".$i;
			$txtreqamount="txtreqamount_".$i;
			$txtreqamountjoblevelconsuom="txtreqamountjoblevelconsuom_".$i;
			$txtreqamountitemlevelconsuom="txtreqamountitemlevelconsuom_".$i;

			$pi_number=array();
			$piquantity=0;
			$sqlPi=sql_select("select a.pi_number, b.quantity from  com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id  and b.work_order_no=$txt_booking_no and b.item_group=".$$txttrimgroup." and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			foreach($sqlPi as $rowPi){
				$pi_number[$rowPi[csf('pi_number')]]=$rowPi[csf('pi_number')];
				$piquantity+=$rowPi[csf('quantity')];
			}
			$uom_id=str_replace("'","",$$txtuom);
			if($piquantity && str_replace("'","",$$txtwoq) < $piquantity){
				echo "pi1**".str_replace("'","",$txt_booking_no)."**".implode(",",$pi_number)."**".$piquantity;
				check_table_status( $_SESSION['menu_id'],0);

				 disconnect($con);die;
			}

			$recv_number=array();
			$recvquantity=0;
			if(str_replace("'","",$$txtdesc)=="" || str_replace("'","",$$txtdesc)==0) $desc_con=""; else $desc_con=" and b.item_description=".$$txtdesc." ";
			if(str_replace("'","",$$txtpoid)=="" || str_replace("'","",$$txtpoid)==0) $poid_con=""; else $poid_con=" and c.po_breakdown_id in (".str_replace("'","",$$txtpoid).") ";
			$sqlRecv=sql_select("select a.recv_number, c.quantity as receive_qnty from  inv_receive_master a,inv_trims_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and c.trans_type=1 and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and b.item_group_id=".$$txttrimgroup." and a.item_category=4 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 $desc_con $poid_con");
			foreach($sqlRecv as $rowRecv){
				$recv_number[$rowRecv[csf('recv_number')]]=$rowRecv[csf('recv_number')];
				$recvquantity+=$rowRecv[csf('receive_qnty')];
			}

			if($recvquantity>0)
			{
				if($recvquantity>0 && $recvquantity<=str_replace("'","",$$txtwoq))
				{
					check_table_status( $_SESSION['menu_id'],0);
				}
				else
				{
					echo "recv1**".str_replace("'","",$txt_booking_no)."**".implode(",",$recv_number)."**".$recvquantity;
					check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);die;
				}
			}
			$job=str_replace("'","",$$txtjob_id);
			$trimcostid=str_replace("'","",$$txttrimcostid);
			$amt=str_replace("'","",$$txtamount);
			$exRate=str_replace("'","",$$txtexchrate);
			//==============================
			if($amount_exceed_level==1){
				$reqAmt=(($curr_book_amount_job_level[$job]['req_amount']*$exeed_budge_amount)/100)+$curr_book_amount_job_level[$job]['req_amount'];
				$curAmt=$curr_book_amount_job_level[$job]['prev_amount']+($amt/$exRate);
				if(($curAmt-$reqAmt)>1){
					echo "vad1**".str_replace("'","",$txt_booking_no)."**".$i;
					check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);die;
				}
				$curr_book_amount_job_level[$job]['prev_amount']+=($amt/$exRate);
			}
			if($amount_exceed_level==2){
				$reqAmt=(($curr_book_amount_job_item_level[$job][$trimcostid]['req_amount']*$exeed_budge_amount)/100)+$curr_book_amount_job_item_level[$job][$trimcostid]['req_amount'];
				$curAmt=$curr_book_amount_job_item_level[$job][$trimcostid]['prev_amount']+($amt/$exRate);
				if(($curAmt-$reqAmt)>1){
					echo "vad1**".str_replace("'","",$txt_booking_no)."**".$i."**".$curAmt."**".$reqAmt."**".$curr_book_amount_job_item_level[$job][$trimcostid]['req_amount'];
					check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);die;
				}
				$curr_book_amount_job_item_level[$job][$trimcostid]['prev_amount']+=($amt/$exRate);
			}

			if($exceed_qty_level==2){
				$reqQty=(($curr_book_qty_job_item_level[$job][$trimcostid]['req_qty']*$exeed_budge_qty)/100)+$curr_book_qty_job_item_level[$job][$trimcostid]['req_qty'];
				$curQty=$curr_book_qty_job_item_level[$job][$trimcostid]['prev_qty']+($woq*$conversion_factor);
				if(($curQty-$reqQty)>1){
					echo "vad2**".str_replace("'","",$txt_booking_no)."**".$i."**".$curQty."**".$reqQty."**".$curr_book_amount_job_item_level[$job][$trimcostid]['req_qty'];
					check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);die;
				}
				$curr_book_qty_job_item_level[$job][$trimcostid]['prev_qty']+=($woq*$conversion_factor);
			}

			if(str_replace("'",'',$$txtbookingid)!=""){
				$id_arr=array();
				$data_array_up1=array();
				$id_arr[]=str_replace("'",'',$$txtbookingid);
				$data_array_up1[str_replace("'",'',$$txtbookingid)] =explode("*",("".$$txttrimcostid."*".$$txtpoid."*".$$txtjob_id."*".$txt_booking_no."*".$$txttrimgroup."*".$$txtdesc."*".trim($$txtbrandsup)."*".$$txtuom."*".$$cbocolorsizesensitive."*".$$txtwoq."*".$$txtexchrate."*".$$txtrate."*".$$txtamount."*".$$txtddate."*".$$txtcountry."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

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
								$color_id = return_id( str_replace("'","",$consbreckdownarr[4]), $color_library, "lib_color", "id,color_name","252");
								$new_array_color[$color_id]=str_replace("'","",$consbreckdownarr[4]);
							}
							else $color_id =  array_search(str_replace("'","",$consbreckdownarr[4]), $new_array_color);
						}
						else $color_id =0;


						if ($c!=0) $data_array_up2 .=",";
						$data_array_up2 .="(".$id1.",".$$txtbookingid.",".$txt_booking_no.",".$booking_mst_id.",".$$txtjob_id.",".$$txtpoid.",'".$consbreckdownarr[0]."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".trim($consbreckdownarr[3])."','".$color_id."','".$consbreckdownarr[5]."','".$consbreckdownarr[6]."','".$consbreckdownarr[7]."','".$consbreckdownarr[8]."','".$consbreckdownarr[9]."','".$consbreckdownarr[10]."','".$consbreckdownarr[11]."','".$consbreckdownarr[12]."','".$consbreckdownarr[14]."')";
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

		if($db_type==2 || $db_type==1 ){
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
	else if ($operation==2)
	{
		$con = connect();
		if($db_type==0){
			mysql_query("BEGIN");
		}

		for ($i=1;$i<=$total_row;$i++){
			$txttrimcostid="txttrimcostid_".$i;
			$txttrimgroup="txttrimgroup_".$i;
			$txtpoid="txtpoid_".$i;
			$txtbookingid="txtbookingid_".$i;
			$txtdesc="txtdesc_".$i;
			$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no and b.item_group=".$$txttrimgroup." and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($pi_number){
				echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
				 disconnect($con);die;
			}

			if(str_replace("'","",$$txtdesc)=="" || str_replace("'","",$$txtdesc)==0) $desc_con=""; else $desc_con=" and b.item_description=".$$txtdesc." ";
			$recv_number=return_field_value( "recv_number", "inv_receive_master a,inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and b.item_group_id=".$$txttrimgroup." and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0 $desc_con");
			if($recv_number){
				echo "recv1**".str_replace("'","",$txt_booking_no)."**".$recv_number;
				 disconnect($con);die;
			}
			$bookingdtlsid=str_replace("'","",$$txtbookingid);
			$trims_del_qty=$delivery_qty_arr[$bookingdtlsid]['delevery_qty'];
			if($trims_del_qty>0)
			{
				if( str_replace("'","",$$txtreqqnty) <= $trims_del_qty)
				{
					$trims_del_no=$delivery_qty_arr[$bookingdtlsid]['trims_del'];
					$trims_del_no=implode(", ",array_unique(explode(",",chop($trims_del_no,','))));
					echo "delQtyExeed**".$trims_del_no;
					 disconnect($con);die;
				}
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

if ($action=="save_update_delete_dtls_job_level"){

	$color_library=return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$booking_mst_id=str_replace("'","",$booking_mst_id);
	$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_booking_no and status_active=1");
	if($is_approved==3){
		$is_approved=1;
	}
	if($is_approved==1){
		echo "app1**".str_replace("'","",$txt_booking_no);
		disconnect($con);die;
	}

	if(str_replace("'","",$txt_booking_no)!='' )
	{
		$sql=sql_select("select a.trims_del,b.booking_dtls_id,b.delevery_qty  from trims_delivery_mst a, trims_delivery_dtls b where a.id=b.mst_id and b.order_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		$delivery_qty_arr=array();
		foreach($sql as $row)
		{
			$delivery_qty_arr[$row[csf('booking_dtls_id')]]['delevery_qty'] +=$row[csf('delevery_qty')];
			$delivery_qty_arr[$row[csf('booking_dtls_id')]]['trims_del'] .=$row[csf('trims_del')].",";
		}
	}

	$sql_lib_item_group_array=array();
	$sql_lib_item_group=sql_select("select id, item_name,conversion_factor,order_uom as cons_uom from lib_item_group");
	foreach($sql_lib_item_group as $row_sql_lib_item_group){
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]]['conversion_factor']=$row_sql_lib_item_group[csf('conversion_factor')];
	}

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

	$strdata=json_decode(str_replace("'","",$strdata));

	if ($operation==0){
		$curr_book_amount_job_level=array();
		$curr_book_amount_job_item_level=array();
		$jobArr=array();
		$poArr=array();
		$pre_trim_id_arr=array();
		$des_arr=array();
		$brand_arr=array();

		for ($i=1;$i<=$total_row;$i++){
			$txttrimcostid="txttrimcostid_".$i;
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
			$JoBc=$$txtjob_id;
			$condition= new condition();
			if(str_replace("'","",$$txtjob_id) !=''){
				$condition->job_no("=$JoBc");
			}

			$condition->init();
			$trims= new trims($condition);

			//$req_qty_arr=$trims->getQtyArray_by_orderAndPrecostdtlsid();
			//$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsid();

			$reqQtyItemLevelArr=$trims->getQtyArray_by_precostdtlsid();
			$reqAmountItemLevelArr=$trims->getAmountArray_precostdtlsid();

			$reqAmountJobLevelArr=$trims->getAmountArray_by_job();
			//$reqQtyJobLevelArr=$trims->getQtyArray_by_job();


			$pretrimcostid=str_replace("'","",$$txttrimcostid);
			$poid=str_replace("'","",$$txtpoid);


			$conversion_factor=$sql_lib_item_group_array[str_replace("'","",$$txttrimgroup)]['conversion_factor'];

			//$curr_book_qty_job_level[str_replace("'","",$$txtjob_id)]['req_qty']=$reqQtyJobLevelArr[str_replace("'","",$$txtjob_id)];
			//$curr_book_qty_job_level[str_replace("'","",$$txtjob_id)]['cur_qty']+=(str_replace("'","",$$txtreqqnty)*$conversion_factor);
			//$curr_book_qty_job_level[str_replace("'","",$$txtjob_id)]['prev_qty']=0;

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
			$des_arr[$$txtdesc]=$$txtdesc;
			$brand_arr[$$txtbrandsup]=$$txtbrandsup;
		}


		$prev_book_amount_job_level=array();
		$prev_book_amount_job_item_level=array();
		//$sql=sql_select("select  job_no,pre_cost_fabric_cost_dtls_id,trim_group,wo_qnty,amount,exchange_rate from wo_booking_dtls where job_no in(".implode(",",$jobArr).") and booking_type=2 and is_short=2 and status_active=1 and is_deleted=0");
		$sql=sql_select("select  b.job_no,b.pre_cost_fabric_cost_dtls_id,b.trim_group,b.wo_qnty,b.amount,b.exchange_rate from wo_booking_dtls b,wo_booking_mst a where a.booking_no=b.booking_no and b.job_no in(".implode(",",$jobArr).") and b.booking_type=2 and b.is_short=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach($sql as $row){
			$curr_book_amount_job_level[$row[csf('job_no')]]['prev_amount']+=($row[csf('amount')]/$row[csf('exchange_rate')]);
			$curr_book_amount_job_item_level[$row[csf('job_no')]][$row[csf('pre_cost_fabric_cost_dtls_id')]]['prev_amount']+=($row[csf('amount')]/$row[csf('exchange_rate')]);
			$curr_book_qty_job_item_level[$row[csf('job_no')]][$row[csf('pre_cost_fabric_cost_dtls_id')]]['prev_qty']+=($row[csf('wo_qnty')]*$sql_lib_item_group_array[$row[csf('trim_group')]]['conversion_factor']);
		}


		$con = connect();
		if($db_type==0){
			mysql_query("BEGIN");
		}
		if  (check_table_status( $_SESSION['menu_id'], 1 )==0 ){
			echo "15**0";
			disconnect($con);die;
		}

		if(str_replace("'","",implode(",",$des_arr))!="") $des_data_cond="and description in(".implode(",",$des_arr).")"; else $des_data_cond="";
		if(str_replace("'","",implode(",",$brand_arr))!="") $brand_data_cond="and brand_supplier in(".implode(",",$brand_arr).")"; else $brand_data_cond="";
		//echo "10**select booking_no from wo_booking_dtls where job_no in(".implode(",",$jobArr).")  and po_break_down_id in (".implode(",",$poArr).") and pre_cost_fabric_cost_dtls_id in(".implode(",",$pre_trim_id_arr).") and booking_type=2 and is_short=2 and booking_no=$txt_booking_no and status_active=1 and is_deleted=0 $des_data_cond $brand_data_cond";die;
		if (is_duplicate_field( "booking_no", "wo_booking_dtls", "job_no in(".implode(",",$jobArr).") and po_break_down_id in (".implode(",",$poArr).") and pre_cost_fabric_cost_dtls_id in(".implode(",",$pre_trim_id_arr).") and booking_type=2 and is_short=2 and booking_no=$txt_booking_no and status_active=1 and is_deleted=0 $des_data_cond $brand_data_cond") == 1)
		{
			check_table_status( $_SESSION['menu_id'],0);
			echo "11**0";
			disconnect($con);die;
		}

		$id_dtls=return_next_id( "id", "wo_booking_dtls", 1 ) ;
		$field_array1="id, pre_cost_fabric_cost_dtls_id, po_break_down_id, job_no, booking_no, booking_mst_id, booking_type, is_short, trim_group,description,brand_supplier, uom, sensitivity, wo_qnty, exchange_rate, rate, amount, delivery_date, country_id_string,entry_form_id, inserted_by, insert_date";
		$field_array2="id,wo_trim_booking_dtls_id,booking_no, booking_mst_id, job_no,po_break_down_id,color_number_id,gmts_sizes,description,brand_supplier,item_color,item_size,cons, process_loss_percent, requirment, rate, amount, pcs,color_size_table_id,article_number";
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
			$conversion_factor=$sql_lib_item_group_array[str_replace("'","",$$txttrimgroup)]['conversion_factor'];

			//==============================
			if($amount_exceed_level==1){
				$reqAmt=(($curr_book_amount_job_level[$job]['req_amount']*$exeed_budge_amount)/100)+$curr_book_amount_job_level[$job]['req_amount'];
				$curAmt=$curr_book_amount_job_level[$job]['prev_amount']+($amt/$exRate);
				if(($curAmt-$reqAmt)>1){
					echo "vad1**".str_replace("'","",$txt_booking_no)."**".$i;
					check_table_status( $_SESSION['menu_id'],0);
				disconnect($con);	die;
				}
				$curr_book_amount_job_level[$job]['prev_amount']+=($amt/$exRate);
			}
			if($amount_exceed_level==2){
				$reqAmt=(($curr_book_amount_job_item_level[$job][$trimcostid]['req_amount']*$exeed_budge_amount)/100)+$curr_book_amount_job_item_level[$job][$trimcostid]['req_amount'];
				$curAmt=$curr_book_amount_job_item_level[$job][$trimcostid]['prev_amount']+($amt/$exRate);
				if(($curAmt-$reqAmt)>1){
					echo "vad1**".str_replace("'","",$txt_booking_no)."**".$i."**".$curAmt."**".$reqAmt."**".$curr_book_amount_job_item_level[$job][$trimcostid]['req_amount'];
					check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);die;
				}
				$curr_book_amount_job_item_level[$job][$trimcostid]['prev_amount']+=($amt/$exRate);
			}

			if($exceed_qty_level==2){
				$reqQty=(($curr_book_qty_job_item_level[$job][$trimcostid]['req_qty']*$exeed_budge_qty)/100)+$curr_book_qty_job_item_level[$job][$trimcostid]['req_qty'];
				$curQty=$curr_book_qty_job_item_level[$job][$trimcostid]['prev_qty']+($woq*$conversion_factor);
				if(($curQty-$reqQty)>1){
					echo "vad2**".str_replace("'","",$txt_booking_no)."**".$i."**".$curQty."**".$reqQty."**".$curr_book_qty_job_item_level[$job][$trimcostid]['req_qty'];
					check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);die;
				}
				$curr_book_qty_job_item_level[$job][$trimcostid]['prev_qty']+=($woq*$conversion_factor);
			}

			//===========================

			foreach($strdata->$job->$trimcostid->po_id as $poId){
				$wqQty=($strdata->$job->$trimcostid->req_qnty->$poId/$reqqnty)*$woq;
				$wqQty=number_format($wqQty,4,'.','');
				$amount=$wqQty*$rate;
				$data_array1 ="(".$id_dtls.",".$$txttrimcostid.",".$poId.",".$$txtjob_id.",".$txt_booking_no.",".$booking_mst_id.",2,2,".$$txttrimgroup.",".$$txtdesc.",".trim($$txtbrandsup).",".$$txtuom.",".$$cbocolorsizesensitive.",".$wqQty.",".$$txtexchrate.",".$$txtrate.",".$amount.",".$$txtddate.",".$$txtcountry.",252,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$rID1=sql_insert("wo_booking_dtls",$field_array1,$data_array1,0);
				//	CONS break down===============================================================================================
				if(str_replace("'",'',$$consbreckdown) !=''){
					$rID_de1=execute_query( "delete from wo_trim_book_con_dtls where  wo_trim_booking_dtls_id =".$$txtbookingid."",0);
					$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
					$d=0;
					for($c=0;$c < count($consbreckdown_array);$c++){
						$consbreckdownarr=explode('_',$consbreckdown_array[$c]);

						if(str_replace("'","",$consbreckdownarr[4])!="")
						{
							if (!in_array(str_replace("'","",$consbreckdownarr[4]),$new_array_color)){
								$color_id = return_id( str_replace("'","",$consbreckdownarr[4]), $color_library, "lib_color", "id,color_name","252");
								$new_array_color[$color_id]=str_replace("'","",$consbreckdownarr[4]);
							}
							else $color_id =  array_search(str_replace("'","",$consbreckdownarr[4]), $new_array_color);
						}
						else $color_id =0;

						$gmc=$consbreckdownarr[0];
						$gms=$consbreckdownarr[1];
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
							$bQty=($jsonarr->$trimcostid->$gmc->$gms->$art->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[6];
							$bwqQty=($jsonarr->$trimcostid->$gmc->$gms->$art->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[8];
							$order_qty=$jsonarr->$trimcostid->$gmc->$gms->$art->order_quantity->$poId;
							$colorSizeTableId=$jsonarr->$trimcostid->$gmc->$gms->$art->color_size_table_id->$poId;
						}
						if(str_replace("'","",$$cbocolorsizesensitive)==0){
							$bQty=($jsonarr->$trimcostid->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[6];
							$bwqQty=($jsonarr->$trimcostid->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[8];
							$order_qty=$jsonarr->$trimcostid->order_quantity->$poId;
							$colorSizeTableId=$jsonarr->$trimcostid->color_size_table_id->$poId;
						}
						$bwqQty=number_format($bwqQty,4,'.','');
						$bQty=number_format($bQty,4,'.','');
						$bamount=$bwqQty*$consbreckdownarr[9];
						if ($d!=0){
							$data_array2 .=",";
						}
						$data_array2 ="(".$id1.",".$id_dtls.",".$txt_booking_no.",".$booking_mst_id.",".$$txtjob_id.",".$poId.",'".$consbreckdownarr[0]."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".trim($consbreckdownarr[3])."','".$color_id."','".$consbreckdownarr[5]."','".$bQty."','".$consbreckdownarr[7]."','".$bwqQty."','".$consbreckdownarr[9]."','".$bamount."','".$order_qty."','".$colorSizeTableId."','".$consbreckdownarr[14]."')";
						$id1=$id1+1;
						$add_comma++;
						$d++;
						$rID2=sql_insert("wo_trim_book_con_dtls",$field_array2,$data_array2,0);
					}
				}//CONS break down end==============================================================================================
				$id_dtls=$id_dtls+1;
			}
		}
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

		if($db_type==2 || $db_type==1 )
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
			$JoBc=$$txtjob_id;
			$condition= new condition();

			if(str_replace("'","",$$txtjob_id) !=''){
				$condition->job_no("=$JoBc");
			}

			$condition->init();
			$trims= new trims($condition);
			//$req_qty_arr=$trims->getQtyArray_by_orderAndPrecostdtlsid();
			//$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsid();

			$reqQtyItemLevelArr=$trims->getQtyArray_by_precostdtlsid();
			$reqAmountItemLevelArr=$trims->getAmountArray_precostdtlsid();

			$reqAmountJobLevelArr=$trims->getAmountArray_by_job();

			$pretrimcostid=str_replace("'","",$$txttrimcostid);
			$poid=str_replace("'","",$$txtpoid);
			$bookingdtlsid=str_replace("'","",$$txtbookingid);
			$trims_del_qty=$delivery_qty_arr[$bookingdtlsid]['delevery_qty'];
			if($trims_del_qty>0)
			{
				if( str_replace("'","",$$txtreqqnty) <= $trims_del_qty)
				{
					$trims_del_no=$delivery_qty_arr[$bookingdtlsid]['trims_del'];
					$trims_del_no=implode(", ",array_unique(explode(",",chop($trims_del_no,','))));
					echo "delQtyExeed**".$trims_del_no;
					 disconnect($con);die;
				}
			}

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
			$des_arr[$$txtdesc]=$$txtdesc;
			$brand_arr[$$txtbrandsup]=$$txtbrandsup;
			$booking_dtls_id_arr[$bookingdtlsid]=$bookingdtlsid;
		}

		$prev_book_amount_job_level=array();
		$prev_book_amount_job_item_level=array();
		//$sql=sql_select("select  job_no,pre_cost_fabric_cost_dtls_id,trim_group,wo_qnty,amount,exchange_rate from wo_booking_dtls where job_no in(".implode(",",$jobArr).") and booking_type=2 and is_short=2 and booking_no !=$txt_booking_no and status_active=1 and is_deleted=0");
		$sql=sql_select("select  b.job_no,b.pre_cost_fabric_cost_dtls_id,b.trim_group,b.wo_qnty,b.amount,b.exchange_rate from wo_booking_dtls b,wo_booking_mst a where a.booking_no=b.booking_no and b.job_no in(".implode(",",$jobArr).") and b.booking_type=2 and b.is_short=2 and b.booking_no !=$txt_booking_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0");
		foreach($sql as $row){
			$curr_book_amount_job_level[$row[csf('job_no')]]['prev_amount']+=($row[csf('amount')]/$row[csf('exchange_rate')]);
			$curr_book_amount_job_item_level[$row[csf('job_no')]][$row[csf('pre_cost_fabric_cost_dtls_id')]]['prev_amount']+=($row[csf('amount')]/$row[csf('exchange_rate')]);
			$curr_book_qty_job_item_level[$row[csf('job_no')]][$row[csf('pre_cost_fabric_cost_dtls_id')]]['prev_qty']+=($row[csf('wo_qnty')]*$sql_lib_item_group_array[$row[csf('trim_group')]]['conversion_factor']);

		}

		/*echo "0**";
		echo "select  job_no,pre_cost_fabric_cost_dtls_id,trim_group,wo_qnty,amount from wo_booking_dtls where job_no in(".implode(",",$jobArr).") and booking_type=2 and is_short=2 and status_active=1 and is_deleted=0";
		print_r($curr_book_amount_job_level);
		die;*/

		$con = connect();
		if($db_type==0){
			mysql_query("BEGIN");
		}
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ){
			echo "15**1";
		disconnect($con);	die;
		}

		if(str_replace("'","",implode(",",$des_arr))!="") $des_data_cond="and description in(".implode(",",$des_arr).")"; else $des_data_cond="";
		if(str_replace("'","",implode(",",$brand_arr))!="") $brand_data_cond="and brand_supplier in(".implode(",",$brand_arr).")"; else $brand_data_cond="";

		//echo "10**"."select booking_no from wo_booking_dtls where job_no in(".implode(",",$jobArr).") and po_break_down_id in (".implode(",",$poArr).") and pre_cost_fabric_cost_dtls_id in(".implode(",",$pre_trim_id_arr).") and id not in(".implode(",",$booking_dtls_id_arr).") and booking_type=2 and is_short=2 and booking_no=$txt_booking_no and status_active=1 and is_deleted=0 $des_data_cond $brand_data_cond"; die;
		//echo "10**select booking_no from wo_booking_dtls where job_no in(".implode(",",$jobArr).") and pre_cost_fabric_cost_dtls_id in(".implode(",",$pre_trim_id_arr).") and booking_type=2 and is_short=2 and booking_no=$txt_booking_no and status_active=1 and is_deleted=0 $des_data_cond $brand_data_cond"; die;
		if (is_duplicate_field( "booking_no", "wo_booking_dtls", "job_no in(".implode(",",$jobArr).") and po_break_down_id in (".implode(",",$poArr).") and pre_cost_fabric_cost_dtls_id in(".implode(",",$pre_trim_id_arr).") and id not in(".implode(",",$booking_dtls_id_arr).") and booking_type=2 and is_short=2 and booking_no=$txt_booking_no and status_active=1 and is_deleted=0 $des_data_cond $brand_data_cond") == 1)
		{
			check_table_status( $_SESSION['menu_id'],0);
			echo "11**0";
			disconnect($con);die;
		}
		$field_array_up1="pre_cost_fabric_cost_dtls_id*po_break_down_id*job_no*booking_no*trim_group*description*brand_supplier*uom*sensitivity*wo_qnty*exchange_rate*rate*amount*delivery_date*country_id_string*updated_by*update_date";
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
				 disconnect($con);die;
			}

			$recv_number=array();
			$recvquantity=0;
			if(str_replace("'","",$$txtdesc)=="" || str_replace("'","",$$txtdesc)==0) $desc_con=""; else $desc_con=" and b.item_description=".$$txtdesc." ";
			if(str_replace("'","",$$txtpoid)=="" || str_replace("'","",$$txtpoid)==0) $poid_con=""; else $poid_con=" and c.po_breakdown_id in (".str_replace("'","",$$txtpoid).") ";
			$sqlRecv=sql_select("select a.recv_number, c.quantity as receive_qnty from  inv_receive_master a,inv_trims_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and c.trans_type=1 and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and b.item_group_id=".$$txttrimgroup." and a.item_category=4 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 $desc_con $poid_con");
			foreach($sqlRecv as $rowRecv){
				$recv_number[$rowRecv[csf('recv_number')]]=$rowRecv[csf('recv_number')];
				$recvquantity+=$rowRecv[csf('receive_qnty')];
			}

			if($recvquantity>0)
			{
				if($recvquantity>0 && $recvquantity<=str_replace("'","",$$txtwoq))
				{
					check_table_status( $_SESSION['menu_id'],0);
				}
				else
				{
					echo "recv1**".str_replace("'","",$txt_booking_no)."**".implode(",",$recv_number)."**".$recvquantity;
					check_table_status( $_SESSION['menu_id'],0);
					 disconnect($con);die;
				}
			}
			$job=str_replace("'","",$$txtjob_id);
			$trimcostid=str_replace("'","",$$txttrimcostid);
			$reqqnty=str_replace("'","",$$txtreqqnty);
			$woq=str_replace("'","",$$txtwoq);
			$rate=str_replace("'","",$$txtrate);

			$amt=str_replace("'","",$$txtamount);
			$exRate=str_replace("'","",$$txtexchrate);

			$uom_id=str_replace("'","",$$txtuom);
			$conversion_factor=$sql_lib_item_group_array[str_replace("'","",$$txttrimgroup)]['conversion_factor'];


			//==============================
			if($amount_exceed_level==1){
				$reqAmt=(($curr_book_amount_job_level[$job]['req_amount']*$exeed_budge_amount)/100)+$curr_book_amount_job_level[$job]['req_amount'];
				$curAmt=$curr_book_amount_job_level[$job]['prev_amount']+($amt/$exRate);
				if(($curAmt-$reqAmt)>1){
					echo "vad1**".str_replace("'","",$txt_booking_no)."**".$i;
					check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);die;
				}
				$curr_book_amount_job_level[$job]['prev_amount']+=($amt/$exRate);
			}
			if($amount_exceed_level==2){
				$reqAmt=(($curr_book_amount_job_item_level[$job][$trimcostid]['req_amount']*$exeed_budge_amount)/100)+$curr_book_amount_job_item_level[$job][$trimcostid]['req_amount'];
				$curAmt=$curr_book_amount_job_item_level[$job][$trimcostid]['prev_amount']+($amt/$exRate);
				if(($curAmt-$reqAmt)>1){
					echo "vad1**".str_replace("'","",$txt_booking_no)."**".$i."**".$curAmt."**".$reqAmt."**".$curr_book_amount_job_item_level[$job][$trimcostid]['req_amount'];
					check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);die;
				}
				$curr_book_amount_job_item_level[$job][$trimcostid]['prev_amount']+=($amt/$exRate);
			}

			if($exceed_qty_level==2){
				$reqQty=(($curr_book_qty_job_item_level[$job][$trimcostid]['req_qty']*$exeed_budge_qty)/100)+$curr_book_qty_job_item_level[$job][$trimcostid]['req_qty'];
				$curQty=$curr_book_qty_job_item_level[$job][$trimcostid]['prev_qty']+($woq*$conversion_factor);
				if(($curQty-$reqQty)>1){
					echo "vad2**".str_replace("'","",$txt_booking_no)."**".$i."**".$curQty."**".$reqQty."**".$curr_book_amount_job_item_level[$job][$trimcostid]['req_qty'];
					check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);die;
				}
				$curr_book_qty_job_item_level[$job][$trimcostid]['prev_qty']+=($woq*$conversion_factor);
			}
			//===========================

			if(str_replace("'",'',$$txtbookingid)!=""){
				foreach($strdata->$job->$trimcostid->po_id as $poId){
					$wqQty=($strdata->$job->$trimcostid->req_qnty->$poId/$reqqnty)*$woq;
					$wqQty=number_format($wqQty,4,'.','');
					/*if(in_array($uom_id,$uom_id_arr))
					{
						$trim_wqQty=floor($wqQty);
					}
					else
					{
						$trim_wqQty=$wqQty;
					}*/
					$amount=$wqQty*$rate;
					$id_arr=array();
					$data_array_up1=array();
					$id_arr[]=str_replace("'",'',$strdata->$job->$trimcostid->booking_id->$poId);
					$data_array_up1[str_replace("'",'',$strdata->$job->$trimcostid->booking_id->$poId)] =explode("*",("".$$txttrimcostid."*".$poId."*".$$txtjob_id."*".$txt_booking_no."*".$$txttrimgroup."*".$$txtdesc."*".trim($$txtbrandsup)."*".$$txtuom."*".$$cbocolorsizesensitive."*".$wqQty."*".$$txtexchrate."*".$$txtrate."*".$amount."*".$$txtddate."*".$$txtcountry."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
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
							if(str_replace("'","",$consbreckdownarr[4])!="")
							{
								if (!in_array(str_replace("'","",$consbreckdownarr[4]),$new_array_color)){
									$color_id = return_id( str_replace("'","",$consbreckdownarr[4]), $color_library, "lib_color", "id,color_name","252");
									$new_array_color[$color_id]=str_replace("'","",$consbreckdownarr[4]);
								}
								else $color_id =  array_search(str_replace("'","",$consbreckdownarr[4]), $new_array_color);
							}
							else $color_id =0;
							$gmc=$consbreckdownarr[0];
							$gms=$consbreckdownarr[1];
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
								$bQty=($jsonarr->$trimcostid->$gmc->$gms->$art->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[6];
								$bwqQty=($jsonarr->$trimcostid->$gmc->$gms->$art->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[8];
								$order_qty=$jsonarr->$trimcostid->$gmc->$gms->$art->order_quantity->$poId;
								$colorSizeTableId=$jsonarr->$trimcostid->$gmc->$gms->$art->color_size_table_id->$poId;
							}
							if(str_replace("'","",$$cbocolorsizesensitive)==0){
								$bQty=($jsonarr->$trimcostid->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[6];
								$bwqQty=($jsonarr->$trimcostid->req_qty->$poId/$consbreckdownarr[13])*$consbreckdownarr[8];
								$order_qty=$jsonarr->$trimcostid->order_quantity->$poId;
								$colorSizeTableId=$jsonarr->$trimcostid->color_size_table_id->$poId;
							}
							/*if(in_array($uom_id,$uom_id_arr))
							{
								$trim_bQty=floor($bQty);
								$trim_bwqQty=floor($bwqQty);
							}
							else
							{
								$trim_bQty=$bQty;
								$trim_bwqQty=$bwqQty;
							}*/
							$bQty=number_format($bQty,4,'.','');
							$bwqQty=number_format($bwqQty,4,'.','');
							$bamount=$bwqQty*$consbreckdownarr[9];
							if ($d!=0) $data_array2 .=",";
							$data_array2 ="(".$id1.",".$strdata->$job->$trimcostid->booking_id->$poId.",".$txt_booking_no.",".$booking_mst_id.",".$$txtjob_id.",".$poId.",'".$consbreckdownarr[0]."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".trim($consbreckdownarr[3])."','".$color_id."','".$consbreckdownarr[5]."','".$bQty."','".$consbreckdownarr[7]."','".$bwqQty."','".$consbreckdownarr[9]."','".$bamount."','".$order_qty."','".$colorSizeTableId."','".$consbreckdownarr[14]."')";
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
			$txtdesc="txtdesc_".$i;

			$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no and b.item_group=".$$txttrimgroup." and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($pi_number){
				echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number."**0";
			   disconnect($con);  die;
			}
			if(str_replace("'","",$$txtdesc)=="" || str_replace("'","",$$txtdesc)==0) $desc_con=""; else $desc_con=" and b.item_description=".$$txtdesc." ";
			$recv_number=return_field_value( "recv_number", "inv_receive_master a,inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and b.item_group_id=".$$txttrimgroup." $desc_con and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($recv_number){
				echo "recv1**".str_replace("'","",$txt_booking_no)."**".$recv_number."**0";
				disconnect($con); die;
			}

			$bookingdtlsid=str_replace("'","",$$txtbookingid);
			$trims_del_qty=$delivery_qty_arr[$bookingdtlsid]['delevery_qty'];
			if($trims_del_qty>0)
			{
				if( str_replace("'","",$$txtreqqnty) <= $trims_del_qty)
				{
					$trims_del_no=$delivery_qty_arr[$bookingdtlsid]['trims_del'];
					$trims_del_no=implode(", ",array_unique(explode(",",chop($trims_del_no,','))));
					echo "delQtyExeed**".$trims_del_no;
					 disconnect($con);die;
				}
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

if ($action=="trims_booking_popup")
{
	echo load_html_head_contents("Booking Search","../../../../", 1, 1, $unicode);
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
        <div align="center" style="width:930px;" >
        <input type="hidden" id="txt_booking" value="" />
            <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table width="930" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                <thead>
                    <tr>
                        <th colspan="11" align="center"><? echo create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" ); ?></th>
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
                        <th><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">WO Without PO</th>
                    </tr>
                </thead>
                <tr class="general">
                    <td><? echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '', "load_drop_down( 'trims_booking_multi_job_controllerurmi', this.value, 'load_drop_down_buyer_pop', 'buyer_td' );"); ?></td>
                    <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- Select Buyer --", $selected, "","" ); ?></td>
                    <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:80px"></td>
                    <td><input name="txt_job" id="txt_job" class="text_boxes" style="width:60px"></td>
                    <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:80px"></td>
                    <td><? echo create_drop_down( "cbo_supplier_name", 130, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=4 and a.status_active =1 and a.is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "",0 ); ?></td>
                    <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:60px"></td>
                    <td>
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date">
                    </td>
                    <td>
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date">
                    </td>
                    <td align="center">
                    	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_job').value+'_'+document.getElementById('chk_job_wo_po').value, 'create_booking_search_list_view', 'search_div', 'trims_booking_multi_job_controllerurmi','setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
                    </td>
                </tr>
                <tr class="general">
                    <td align="center" valign="middle" colspan="11" >
						<? echo load_month_buttons(1); ?>
                    </td>
                </tr>
            </table>
            <div id="search_div"></div>
            </form>
        </div>
    </body>
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if ($action=="create_booking_search_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and c.buyer_name='$data[1]'"; else $buyer=set_user_lavel_filtering(' and c.buyer_name','buyer_id');
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
	$arr=array (1=>$comp,2=>$suplier,7=>$user_arr);
	if($data[11]==0)
	{
		$sql="select a.id, a.booking_no_prefix_num,a.inserted_by, a.booking_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date,c.style_ref_no,d.po_number from wo_booking_mst a,wo_booking_dtls b, wo_po_details_master c,wo_po_break_down d  where a.booking_no=b.booking_no and b.job_no=c.job_no and b.job_no=d.job_no_mst and b.po_break_down_id=d.id and a.booking_type=2 and a.entry_form=252 and a.is_short=2
	and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 $company  $buyer  $supplier_id $booking_date $booking_cond $style_cond $order_cond $job_cond group by a.id, a.booking_no_prefix_num,a.inserted_by, a.booking_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date,c.style_ref_no,d.po_number  order by a.id DESC";

		echo  create_list_view("list_view", "Booking No,Company,Supplier,Booking Date,Delivery Date,Style Ref No,Po Number,Insert User", "60,100,100,70,150,150,120","940","300",0, $sql , "js_set_value", "booking_no", "", 1, "0,company_id,supplier_id,0,0,0,0,inserted_by", $arr , "booking_no_prefix_num,company_id,supplier_id,booking_date,delivery_date,style_ref_no,po_number,inserted_by", '','','0,0,0,3,3,0,0,0','','');
	}
	else
	{
		$sql="select a.id, a.job_no, a.inserted_by, a.booking_no_prefix_num, a.booking_no, company_id, a.supplier_id, a.booking_date, a.delivery_date from wo_booking_mst a where  a.booking_no not in ( select a.booking_no from  wo_booking_mst a , wo_booking_dtls b, wo_po_break_down c where a.booking_no=b.booking_no  and b.po_break_down_id=c.id and  a.booking_type=2 and a.entry_form=252 and  a.status_active =1 and a.is_deleted=0  and  b.status_active =1 and b.is_deleted=0 $company ".set_user_lavel_filtering(' and a.buyer_id','buyer_id')." $supplier_id $booking_date $booking_cond $job_cond $file_cond $ref_cond group by a.booking_no_prefix_num, a.booking_no, company_id, a.supplier_id, a.booking_date, a.delivery_date ) and a.booking_type=2 and a.is_short=2 and a.entry_form=252 and  a.status_active =1 and a.is_deleted=0 $company   ".set_user_lavel_filtering(' and a.buyer_id','buyer_id')."  $supplier_id $booking_date $booking_cond group by a.id, a.booking_no_prefix_num,a.inserted_by, a.booking_no,a.job_no,company_id,a.supplier_id,a.booking_date,a.delivery_date order by a.id DESC";
		$arr=array (1=>$comp,2=>$suplier,5=>$user_arr);
		echo  create_list_view("list_view", "Booking No,Company,Supplier,Booking Date,Delivery Date,Insert User", "120,100,100,100,100","700","300",0, $sql , "js_set_value", "booking_no", "", 1, "0,company_id,supplier_id,0,0,inserted_by", $arr , "booking_no_prefix_num,company_id,supplier_id,booking_date,delivery_date,inserted_by", '','','0,0,0,3,3,0','','');
	}
	exit();
}

if($action=="terms_condition_popup"){
	echo load_html_head_contents("Order Search","../../../../", 1, 1, $unicode);
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
<? echo load_freeze_divs ("../../../../",$permission);  ?>
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
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
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

		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0";disconnect($con); die;}
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
	 $sql= "select id,booking_no,booking_date,company_id,buyer_id, 	currency_id,exchange_rate,pay_mode,booking_month,supplier_id,attention,remarks,item_from_precost,delivery_date,source,booking_year,is_approved,cbo_level,ready_to_approved,fabric_source from wo_booking_mst  where booking_no='$data' and  status_active =1 and is_deleted=0";
	$data_array=sql_select($sql);
	foreach ($data_array as $row){
		echo "get_php_form_data(".$row[csf("company_id")].", 'populate_variable_setting_data', 'requires/trims_booking_multi_job_controllerurmi' );\n";
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('booking_mst_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_booking_no').value = '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('cbo_currency').value = '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('cbo_pay_mode').value = '".$row[csf("pay_mode")]."';\n";
		$paymodeData=$row[csf("pay_mode")].'_'.$row[csf("buyer_id")];
		echo "load_drop_down( 'requires/trims_booking_multi_job_controllerurmi', '".$paymodeData."', 'load_drop_down_supplier', 'supplier_td' );\n";
		echo "document.getElementById('txt_booking_date').value = '".change_date_format($row[csf("booking_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('cbo_booking_month').value = '".$row[csf("booking_month")]."';\n";
		echo "document.getElementById('cbo_supplier_name').value = '".$row[csf("supplier_id")]."';\n";
		echo "document.getElementById('txt_attention').value = '".$row[csf("attention")]."';\n";
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('txt_delivery_date').value = '".change_date_format($row[csf("delivery_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('cbo_source').value = '".$row[csf("source")]."';\n";
        echo "document.getElementById('cbo_material_source').value = '".$row[csf("fabric_source")]."';\n";
		echo "document.getElementById('cbo_booking_year').value = '".$row[csf("booking_year")]."';\n";
		if($row[csf("is_approved")]==3){
			$is_approved=1;
		}else{
			$is_approved=$row[csf("is_approved")];
		}
		echo "document.getElementById('id_approved_id').value = '".$is_approved."';\n";
		echo "document.getElementById('cbo_level').value = '".$row[csf("cbo_level")]."';\n";
		echo "document.getElementById('cbo_ready_to_approved').value = '".$row[csf("ready_to_approved")]."';\n";
		echo " $('#cbo_company_name').attr('disabled',true);\n";
		echo " $('#cbo_supplier_name').attr('disabled',true);\n";
		echo " $('#cbo_level').attr('disabled',true);\n";
		echo " $('#cbo_buyer_name').attr('disabled',true);\n";
		echo "fnc_show_booking_list();\n";

		if($is_approved==1){
			echo "document.getElementById('app_sms2').innerHTML = 'This booking is approved';\n";
			echo "document.getElementById('txt_un_appv_request').disabled = '".false."';\n";
		}
		else{
			echo "document.getElementById('app_sms2').innerHTML = '';\n";
			echo "document.getElementById('txt_un_appv_request').disabled = '".true."';\n";
		}
	}
}

//================================================report Start=====================================================


if($action=="show_trim_booking_report2") // Buck Up
{

	extract($_REQUEST);
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
                                <img  src='../../../../<? echo $imge_arr[$cbo_company_name]; ?>' height='30%' width='50%' />

                       <?
                           }
                           else
                           {
                       ?>
                                <img  src='../../../<? echo $imge_arr[$cbo_company_name]; ?>' height='30%' width='50%' />
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
                                 <?php echo $result[csf('province')];?>  &nbsp;
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
			$shipdate=rtrim($po_shipdate_arr[$nameArray_job_po_row[csf('job_no')]],',');
			$shipdates=implode(",",array_unique(explode(",",$shipdate)));
        ?>
        &nbsp;
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >

            <tr>
                <td colspan="12" align="">
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
                <td style="border:1px solid black"><strong>Image</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>

                <td style="border:1px solid black"><strong>Brand/Supplier Ref.</strong> </td>
                <td align="center" style="border:1px solid black"><strong>Item Color</strong></td>
                <td align="center" style="border:1px solid black"><strong>Qty per Unit</strong></td>
                <td style="border:1px solid black" align="center"><strong>WO Qty</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount </strong></td>
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

			$sql_img = "select id,master_tble_id,image_location
				from common_photo_library
				where master_tble_id='".$result_item[csf('pre_cost_fabric_cost_dtls_id')]."' ";
				$data_array_img=sql_select($sql_img);
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
                 <td style="border:1px solid black"  rowspan="<? echo count($nameArray_item_description)+1; ?>" width="80"><img  src='../../../<? echo $data_array_img[0][csf("image_location")]; ?>' height='70' width='80' /> </td>
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
                $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
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
                <td  style="border:1px solid black;  text-align:right"><?  //echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
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
                <td colspan="13" align="">
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
                 <td style="border:1px solid black"><strong>Image</strong> </td>
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
             $sql_img = "select id,master_tble_id,image_location
				from common_photo_library
				where master_tble_id='".$result_item[csf('pre_cost_fabric_cost_dtls_id')]."' ";
				$data_array_img=sql_select($sql_img);
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
                 <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>"  width="80"><img  src='../../../<? echo $data_array_img[0][csf("image_location")]; ?>' height='70' width='80' /> </td>
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
                $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
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
                <td  style="border:1px solid black;  text-align:right"><?  //echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
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
                <td colspan="13" align="">
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
                 <td style="border:1px solid black"><strong>Image</strong> </td>
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

		   $sql_img = "select id,master_tble_id,image_location
				from common_photo_library
				where master_tble_id='".$result_item[csf('pre_cost_fabric_cost_dtls_id')]."' ";
				$data_array_img=sql_select($sql_img);
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
                <td  width="80" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>"><img  src='../../../<? echo $data_array_img[0][csf("image_location")]; ?>' height='70' width='80' /> </td>
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
                <td align="right" style="border:1px solid black"  colspan="11"><strong>Total</strong></td>
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
                <td colspan="15" align="">
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
                <td style="border:1px solid black"><strong>Image</strong> </td>

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
			$sql_img_color = "select id,master_tble_id,image_location
				from common_photo_library
				where master_tble_id='".$result_item[csf('pre_cost_fabric_cost_dtls_id')]."' ";
				$data_array_img_color=sql_select($sql_img_color);
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
                 <td style="border:1px solid black" rowspan="<? echo (count($nameArray_item_description)*count($nameArray_color)); ?>"  width="80"><img  src='../../../<? echo $data_array_img_color[0][csf("image_location")]; ?>' height='70' width='80' /> </td>
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
                <td style="border:1px solid black;  text-align:right" colspan="11"><strong> Item Total</strong></td>
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
                <td align="right" style="border:1px solid black"  colspan="14"><strong>Total</strong></td>
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
                <td colspan="15" align="">
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
                 <td style="border:1px solid black"><strong>Image</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>

                <td style="border:1px solid black"><strong>Brand/Supplier Ref.</strong> </td>
                 <td style="border:1px solid black"><strong>Item Color</strong> </td>
                 <td align="center" style="border:1px solid black"><strong>Qty per Unit</strong></td>
                <td align="center" style="border:1px solid black"><strong> Qnty</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount </strong></td>
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

				$sql_img = "select id,master_tble_id,image_location
				from common_photo_library
				where master_tble_id='".$result_item[csf('pre_cost_fabric_cost_dtls_id')]."' ";
				$data_array_img=sql_select($sql_img);

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
                  <td width="80" style="border:1px solid black"  rowspan="<? echo count($nameArray_item_description)+1; ?>"><img  src='../../../<? echo $data_array_img[0][csf("image_location")]; ?>' height='60' width='80' /> </td>
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
						$nameArray_color_size_qnty=sql_select("select sum(b.cons) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trim_group=". $result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and  b.description='". $result_itemdescription[csf('description')]."' and b.brand_supplier='".$result_itemdescription[csf('brand_supplier')]."' and b.item_color='".$result_itemdescription[csf('item_color')]."'");
						}
						if($db_type==2)
				        {
						$nameArray_color_size_qnty=sql_select("select sum(b.cons) as cons, sum(b.amount) AS amount  from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trim_group=". $result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and nvl( b.description,0)=nvl('". $result_itemdescription[csf('description')]."',0) and nvl(b.brand_supplier,0)=nvl('".$result_itemdescription[csf('brand_supplier')]."',0) and nvl(b.item_color,0)=nvl('".$result_itemdescription[csf('item_color')]."',0)");
						}
				$item_desctiption_amount_total = 0;
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
					$item_desctiption_amount_total += $result_color_size_qnty[csf('amount')];
					$color_tatal+=$result_color_size_qnty[csf('cons')];
                }
                else echo "";
                ?>
                </td>
                <?
                }
				$rate_as_per_gmts_color = $item_desctiption_amount_total/$item_desctiption_total;
                ?>

                <td style="border:1px solid black; text-align:center "><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_text=$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($rate_as_per_gmts_color,4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <?
                //$amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];

				//echo number_format($amount_as_per_gmts_color,4);
				echo number_format($item_desctiption_amount_total,4);
                $total_amount_as_per_gmts_color+=$item_desctiption_amount_total;
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
                //echo number_format($total_amount_as_per_gmts_color,2);
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
    <div >
         <?
          echo signature_table(132, $cbo_company_name, "1330px",1);

		 ?>
      	</div>
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
        <script type="text/javascript" src="../../../../js/jquery.js"></script>
        <script type="text/javascript" src="../../../../js/jquerybarcode.js"></script>
    <?
    }else {
        ?>
         <script type="text/javascript" src="../../../js/jquery.js"></script>
        <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
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

if($action=="show_trim_booking_report3")
{

	extract($_REQUEST);
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
			//echo $po_idss.'DDDDDDDDD';
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


   	<table width="1333px"  cellpadding="0" cellspacing="0" style="border:0px solid black" >
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
                                <img  src='../../../../<? echo $imge_arr[$cbo_company_name]; ?>' height='30%' width='50%' />

                       <?
                           }
                           else
                           {
                       ?>
                                <img  src='../../../<? echo $imge_arr[$cbo_company_name]; ?>' height='30%' width='50%' />
                       <?	}
                       }
                       else
                       { ?>
                         <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='30%' width='50%' />
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
                                 <?php echo $result[csf('province')];?>  &nbsp;
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
                <td width="100" style="font-size:12px" align="left"><b>Remarks</b>  </td>
                <td align="left" width="110" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;" colspan="5">:&nbsp;<? echo $remarks; ?></td>
            </tr>
            </table>
            </td>
            </tr>
          </table>
    </th>
    </tr>
     <tr>
     <td>
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
				  <? if($show_comment==1) {?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>

                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>

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
				echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
				$trims_remark=$trims_remark_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]]['remark'];
                ?>
                </td>

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
				 <? if($show_comment==1) {?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>

                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>

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
				echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
				$trims_remark=$trims_remark_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]]['remark'];
                ?>
                </td>

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
				 <? if($show_comment==1) {?>
                <td style="border:1px solid black; text-align:right"></td>

                <td style="border:1px solid black; text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>

                <td>&nbsp; </td>
                <? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="9"><strong>Total</strong></td>
				 <? if($show_comment==1) {?>
					<td> </td>

                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>

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
				 <? if($show_comment==1) {?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>

                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>

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
				echo number_format($result_itemdescription[csf('cons')],4);
                $item_desctiption_total += $result_itemdescription[csf('cons')] ;
				?>
                </td>
                 <td style="border:1px solid black; text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_text=$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
				 <? if($show_comment==1) {?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>

                <td style="border:1px solid black; text-align:right">
                <?
                $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
				$trims_remark=$trims_remark_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]]['remark'];
                ?>
                </td>

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
				 <? if($show_comment==1) {?>
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
                <td align="right" style="border:1px solid black"  colspan="9"><strong>Total</strong></td>
				<? if($show_comment==1) {?>
				<td> </td>

                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>

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
				<? if($show_comment==1) {?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>

                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>

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
					echo number_format($amount_as_per_gmts_color,4);
					$total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
					$trims_remark=$trims_remark_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]]['remark'];
					?>
					</td>

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
				<? if($show_comment==1) {?>
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
                <td align="right" style="border:1px solid black"  colspan="13"><strong>Total</strong></td>
				<? if($show_comment==1) {?>
					<td> </td>

                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>

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
				<? if($show_comment==1) {?>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>

                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>

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
					<? if($show_comment==1) {?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>

                <td style="border:1px solid black; text-align:right">
                <?
                $amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>

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
				<? if($show_comment==1) {?>
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

                <td>&nbsp;  </td>
                <? } ?>
            </tr>
        </table>
        <?
		}
		}
		?>
        </td>
         </tr>
     </table>
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
                            <td width="3%" style="border:1px solid black;"><? echo $i;?></td><td width="50%" style="border:1px solid black;"><? echo $row[csf('user_full_name')];?></td><td width="27%" style="border:1px solid black;"><? echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')])); ;?></td><td width="20%" style="border:1px solid black;"><? echo $row[csf('approved_no')];?></td>
                            </tr>
                            <?
                            $i++;
                        }
                            ?>

                    </table>
            </td>
            </tr>
        </table>
		 <br>
	  <br>
        <?
        //------------------------------ Query for TNA start-----------------------------------
			$sql_job_color=sql_select( "select c.color_type_id,b.id as po_id  from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_fabric_cost_dtls c where a.job_no=b.job_no_mst and  a.job_no=c.job_no and  b.job_no_mst=c.job_no  and  a.status_active =1 and a.is_deleted=0 and  b.status_active =1 and b.is_deleted=0 and b.id in(".$po_idss.") ");

			foreach ($sql_job_color as $row)
			{
				$po_color_type_arr[$row[csf('po_id')]]=$row[csf('color_type')];

			}


				$po_num_arr=return_library_array("select id,po_number from wo_po_break_down where id in($po_idss)",'id','po_number');
				$tna_start_sql=sql_select( "select id,po_number_id,

								(case when task_number=73 then task_start_date else null end) as finishing_start_date,
								(case when task_number=73 then task_finish_date else null end) as finishing_end_date,
								(case when task_number=179 then task_start_date else null end) as aop_finishing_start_date,
								(case when task_number=179 then task_finish_date else null end) as aop_finishing_end_date,
								(case when task_number=180 then task_start_date else null end) as yd_finishing_start_date,
								(case when task_number=180 then task_finish_date else null end) as yd_finishing_end_date
								from tna_process_mst
								where status_active=1 and po_number_id in($po_idss)");

				$tna_fab_start=$tna_knit_start=$tna_dyeing_start=$tna_fin_start=$tna_cut_start=$tna_sewin_start=$tna_exfact_start="";
				$tna_date_task_arr=array();
				foreach($tna_start_sql as $row)
				{

					if($row[csf("finishing_start_date")]!="" && $row[csf("finishing_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['finishing_start_date']=$row[csf("finishing_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['finishing_end_date']=$row[csf("finishing_end_date")];
					}
					if($row[csf("aop_finishing_start_date")]!="" && $row[csf("aop_finishing_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['aop_finishing_start_date']=$row[csf("aop_finishing_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['aop_finishing_end_date']=$row[csf("aop_finishing_end_date")];
					}
					if($row[csf("yd_finishing_start_date")]!="" && $row[csf("yd_finishing_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['yd_finishing_start_date']=$row[csf("yd_finishing_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['yd_finishing_end_date']=$row[csf("yd_finishing_end_date")];
					}

				}

	//------------------------------ Query for TNA end-----------------------------------
		?>
        <fieldset id="div_size_color_matrix" style="max-width:700;">
        <legend>TNA Information</legend>
        <!--<span style="font-size:180; font-weight:bold;"></span>-->
        <table width="100%" style="border:1px solid black;font-size:12px" border="1" cellpadding="2" cellspacing="0" rules="all">
            <tr>
            	<td rowspan="2" align="center" width="100" valign="top">SL</td>
            	<td width="280" rowspan="2"  align="center" valign="top"><b>Order No</b></td>

                <td colspan="2" align="center" valign="top"><b>Finishing Fabric In Solid</b></td>
                <td colspan="2" align="center" valign="top"><b>Finishing Fabric In AOP </b></td>
                <td colspan="2" align="center" valign="top"><b>Finishing Fabric In YD </b></td>

            </tr>
            <tr>

                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>


            </tr>
            <?
			$i=1;
			foreach($tna_date_task_arr as $order_id=>$row)
			{
				?>
                <tr>
                	<td width="100"><? echo $i; ?></td>
                    <td><? echo $po_num_arr[$order_id]; ?></td>

                    <td align="center"><? echo change_date_format($row['finishing_start_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['finishing_end_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['aop_finishing_start_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['aop_finishing_end_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['yd_finishing_start_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['yd_finishing_end_date']); ?></td>

                </tr>
                <?
				$i++;
			}
			?>

        </table>
        </fieldset>



     <!--class="footer_signature"-->
    <div  style="margin-top:-50px;">
         <?
          echo signature_table(132, $cbo_company_name, "1333px",1);
		 ?>
   </div>
      <div id="page_break_div">
   	 </div>
    <div>

		<?
        	//echo signature_table(2, $cbo_company_name, "1330px");
			echo "****".custom_file_name($txt_booking_no,$style_sting,$job_no);
        ?>
    </div>

<?
exit();
}

if($action=="show_trim_booking_report4") // Aziz-5-5-2018
{

	extract($_REQUEST);
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
	$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");

	$nameArray_approved=sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no='$txt_booking_no' and b.entry_form=8 and  a.status_active =1 and a.is_deleted=0");
	list($nameArray_approved_row)=$nameArray_approved;
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
			$po_no_arr[$result_job[csf('id')]]=$result_job[csf('po_number')];
			$job_file_no[$result_job[csf('id')]]=$result_job[csf('file_no')];
			$job_ref_no[$result_job[csf('id')]]=$result_job[csf('grouping')];
			$pub_shipment_date.=$result_job[csf('pub_shipment_date')].',';
			if($po_idss=='') $po_idss=$result_job[csf('id')];else $po_idss.=",".$result_job[csf('id')];

		}
		$sql_job=sql_select( "select b.job_no_mst,b.id as po_id, b.po_quantity as po_quantity  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and  a.status_active =1 and a.is_deleted=0 and  b.status_active =1 and b.is_deleted=0 and b.id in(".$po_idss.") ");
		foreach ($sql_job as $row)
		{
			$job_po_qty_arr[$row[csf('po_id')]]+=$row[csf('po_quantity')];
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


</style>
</head>


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
                                <img  src='../../../../<? echo $imge_arr[$cbo_company_name]; ?>' height='30%' width='50%' />

                       <?
                           }
                           else
                           {
                       ?>
                                <img  src='../../../<? echo $imge_arr[$cbo_company_name]; ?>' height='30%' width='50%' />
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
                                 <?php echo $result[csf('province')];?>  &nbsp;
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
		$nameArray_booking_country=sql_select( "select pre_cost_fabric_cost_dtls_id,po_break_down_id as po_id,sensitivity,country_id_string,delivery_date from wo_booking_dtls  where booking_no='$txt_booking_no' and  status_active =1 and is_deleted=0");
		foreach($nameArray_booking_country as $nameArray_booking_country_row){

			$po_delivery_date_arr[$nameArray_booking_country_row[csf('po_id')]]=$nameArray_booking_country_row[csf('delivery_date')];

			$country_id_string=explode(",",$nameArray_booking_country_row[csf('country_id_string')]);
			$tocu=count($country_id_string);
			for($cu=0;$cu<$tocu;$cu++){
				$booking_country_arr[$nameArray_booking_country_row[csf('pre_cost_fabric_cost_dtls_id')]][$nameArray_booking_country_row[csf('sensitivity')]][$country_id_string[$cu]]=$country_arr[$country_id_string[$cu]];
			}
		}

		$nameArray_job_po=sql_select( "select job_no,po_break_down_id as po_id from wo_booking_dtls  where booking_no='$txt_booking_no' and status_active =1 and is_deleted=0 group by job_no,po_break_down_id order by job_no ");
		foreach($nameArray_job_po as $nameArray_job_po_row){
		$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group,po_break_down_id as po_id from wo_booking_dtls  where booking_no='$txt_booking_no' and  status_active =1 and is_deleted=0 and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and po_break_down_id=".$nameArray_job_po_row[csf('po_id')]."   and sensitivity=1 group by pre_cost_fabric_cost_dtls_id,trim_group,po_break_down_id  order by trim_group ");
	    if(count($nameArray_item)>0){

			$ref_nos=$job_ref_no[$nameArray_job_po_row[csf('po_id')]];
			$po_no_qty=0;
			$job_no=$nameArray_job_po_row[csf('job_no')];
			$po_no_qty=$job_po_qty_arr[$nameArray_job_po_row[csf('po_id')]];
			$po_delivery_date=$po_delivery_date_arr[$nameArray_job_po_row[csf('po_id')]];
        ?>
        &nbsp;
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >

            <tr>
                <td colspan="9" align="">
                <table width="100%" style="table-layout: fixed;">
                <tr>
                <td width="60%" align="left"><strong>As Per Garments Color (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]]; if($ref_nos!='' ) echo " &nbsp;Int Ref.:&nbsp;".$ref_nos;else " "; echo " &nbsp;  Po Qty.:&nbsp;".$po_no_qty; ?></strong></td>
                <td width="40%" style="font-weight:bold;">Po No: <? echo $po_no_arr[$nameArray_job_po_row[csf('po_id')]].' &nbsp;&nbsp;&nbsp; '; echo "&nbsp;&nbsp;Delivery Date:&nbsp;".change_date_format($po_delivery_date); ?></td>
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
            $nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid, b.description,b.article_number, b.brand_supplier,b.item_color,a.gmts_color_id,b.gmts_sizes,b.item_size,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_id')]."  and a.sensitivity=1  and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id,b.item_color,a.gmts_color_id,b.gmts_sizes,b.item_size, b.description,b.article_number, b.brand_supplier,b.item_color order by bid ");
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
                <td  style="border:1px solid black;  text-align:right"><strong><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></strong></td>
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
		$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group,po_break_down_id as po_id  from wo_booking_dtls  where booking_no='$txt_booking_no' and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and po_break_down_id=".$nameArray_job_po_row[csf('po_id')]." and sensitivity=2 and  status_active =1 and  is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group,po_break_down_id  order by trim_group ");
		if(count($nameArray_item)>0)
		{

			$ref_nos=$job_ref_no[$nameArray_job_po_row[csf('po_id')]];
			$po_no_qty=0;
			$job_no=$nameArray_job_po_row[csf('job_no')];

			$po_no_qty=$job_po_qty_arr[$nameArray_job_po_row[csf('po_id')]];
			$po_delivery_date=$po_delivery_date_arr[$nameArray_job_po_row[csf('po_id')]];

        ?>

        <table border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="12" align="">
                 <table width="100%" style="table-layout: fixed;">
                <tr>
                <td width="60%"><strong>Size Sensitive (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]]; echo "&nbsp;&nbsp;Int Ref.:".$ref_nos; echo "&nbsp;&nbsp; Po Qty..:".$po_no_qty; ?></strong></td>
                <td width="40%" style="margin-left:210px; font-weight:bold;">Po No: <? echo $po_no_arr[$nameArray_job_po_row[csf('po_id')]].'&nbsp; &nbsp; ';echo "&nbsp;Delivery Date:&nbsp;".change_date_format($po_delivery_date);  ?></td>
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
			$i++;//
			$nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid, b.description, b.brand_supplier,b.item_size,b.article_number,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and   a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$result_item[csf('po_id')]." and a.sensitivity=2 and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_size,b.article_number order by b.article_number,b.item_size,bid");
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
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_text=$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">

                <?
                $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
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
                <td  style="border:1px solid black;  text-align:right"><strong><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></strong></td>
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
		 $nameArray_item=sql_select( "select pre_cost_fabric_cost_dtls_id, trim_group,po_break_down_id as po_id from wo_booking_dtls  where booking_no='$txt_booking_no' and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and po_break_down_id=".$nameArray_job_po_row[csf('po_id')]."  and sensitivity=3 and  status_active =1 and is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group,po_break_down_id order by trim_group ");
		if(count($nameArray_item)>0)
		{

			$ref_nos=$job_ref_no[$nameArray_job_po_row[csf('po_id')]];
			$po_no_qty=0;
			//$job_no=$nameArray_job_po_row[csf('job_no')];
			$po_no_qty=$job_po_qty_arr[$nameArray_job_po_row[csf('po_id')]];
			$po_delivery_date=$po_delivery_date_arr[$nameArray_job_po_row[csf('po_id')]];

        ?>
        <table border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="11" align="">
                <table width="100%" style="table-layout: fixed;">
                <tr>
                <td width="60%"><strong>Contrast Color (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]];echo " &nbsp;Int Ref.:&nbsp;".$ref_nos; echo " &nbsp;Po Qty.:&nbsp;".$po_no_qty;?></strong></td>
                <td width="40%" style="font-weight:bold;">Po No: <? echo $po_no_arr[$nameArray_job_po_row[csf('po_id')]]; echo "&nbsp;&nbsp;&nbsp;Delivery Date:&nbsp;".change_date_format($po_delivery_date); ?></td>
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
				$gmtcolor_library=return_library_array( "select b.item_color,b.color_number_id from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$result_item[csf('po_id')]."  and a.sensitivity=3  and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by trim_group ", "item_color", "color_number_id"  );

			$nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid,b.description, b.article_number,b.brand_supplier,b.item_color,b.color_number_id,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$result_item[csf('po_id')]." and a.sensitivity=3  and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.description, b.article_number,b.brand_supplier,b.item_color,b.color_number_id order by bid ");
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
                <td  style="border:1px solid black;  text-align:right"><strong><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></strong></td>
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
		$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group,po_break_down_id as po_id from wo_booking_dtls  where booking_no='$txt_booking_no' and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_id')]." and sensitivity=4 and  status_active =1 and is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group,po_break_down_id order by po_break_down_id,trim_group");
	   if(count($nameArray_item)>0)
		{


			$ref_nos=$job_ref_no[$nameArray_job_po_row[csf('po_id')]];
			$po_no_qty=0;
			$po_no_qty=$job_po_qty_arr[$nameArray_job_po_row[csf('po_id')]];
			$po_delivery_date=$po_delivery_date_arr[$nameArray_job_po_row[csf('po_id')]];

        ?>

        <table border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="14" align="">
                <table width="100%" style="table-layout: fixed;">
                <tr>
                <td width="60%" ><strong>Color & size sensitive (<? echo "Job NO: ".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]] ;echo "&nbsp;&nbsp;Int Ref.:&nbsp;".$ref_nos; echo "&nbsp;&nbsp;Po Qty.:&nbsp;".$po_no_qty;?></strong></td>
                <td width="40%" style="font-weight:bold;">Po No: <? echo $po_no_arr[$nameArray_job_po_row[csf('po_id')]];echo "&nbsp;&nbsp;&nbsp;Delivery Date:&nbsp;".change_date_format($po_delivery_date); ?></td>
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

				$gmtcolor_library=return_library_array( "select b.item_color,b.color_number_id from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.po_break_down_id=".$result_item[csf('po_id')]." and a.sensitivity=4    and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by trim_group ", "item_color", "color_number_id"  );

			 $nameArray_color=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid, b.color_number_id as gmt_color, b.item_color as color_number_id,b.item_size,b.gmts_sizes,b.description,b.article_number, b.brand_supplier,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount, min(c.color_order) as color_order, min(c.size_order) as size_order,c.article_number from wo_booking_dtls a, wo_trim_book_con_dtls b,wo_po_color_size_breakdown c  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and b.po_break_down_id=c.po_break_down_id and b.color_number_id=c.color_number_id and b.gmts_sizes=c.size_number_id and  c.id=b.color_size_table_id and a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'   and a.po_break_down_id=".$result_item[csf('po_id')]." and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and a.sensitivity=4 and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.color_number_id, b.item_color,b.item_size,b.gmts_sizes,b.description,b.article_number, b.brand_supplier,c.article_number order by c.article_number,color_order,size_order");



            $nameArray_item_description=sql_select( "select distinct uom from wo_booking_dtls  where booking_no='$txt_booking_no' and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and sensitivity=4 and trim_group=".$result_item[csf('trim_group')]."  and po_break_down_id=".$result_item[csf('po_id')]."  and pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and  status_active =1 and is_deleted=0 order by trim_group ");
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
                <td  style="border:1px solid black;  text-align:right"><strong><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></strong></td>
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
		$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group,po_break_down_id as po_id from wo_booking_dtls  where booking_no='$txt_booking_no' and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_id')]." and sensitivity=0 and  status_active =1 and is_deleted=0 group by pre_cost_fabric_cost_dtls_id,trim_group,po_break_down_id order by trim_group ");
		if(count($nameArray_item)>0)
		{
			$ref_nos=$job_ref_no[$nameArray_job_po_row[csf('po_id')]];
			$po_no_qty=0;
			$po_no_qty=$job_po_qty_arr[$nameArray_job_po_row[csf('po_id')]];
			$po_delivery_date=$po_delivery_date_arr[$nameArray_job_po_row[csf('po_id')]];
        ?>
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="14" align="">
                <table width="100%" style="table-layout: fixed;">
                <tr>
                <td width="60%"><strong>NO sensitive  (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]];echo " &nbsp;Int Ref.:&nbsp;".$ref_nos;  echo " &nbsp;Po Qty.:&nbsp;".$po_no_qty; ?></strong></td>
                <td width="40%" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap; font-weight:bold;">Po No: <? echo $po_no_arr[$nameArray_job_po_row[csf('po_id')]];echo "&nbsp;&nbsp;&nbsp;Delivery Date:&nbsp;".change_date_format($po_delivery_date); ?></td>
                </tr>
                </table>
                 </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Brand/Supplier Ref.</strong> </td>
				<td style="border:1px solid black"><strong>Article No.</strong> </td>
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

				$nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id, b.description,b.article_number, b.brand_supplier,b.item_color,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.trim_group=".$result_item[csf('trim_group')]." and a.po_break_down_id=".$result_item[csf('po_id')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and a.sensitivity=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.description,b.article_number, b.brand_supplier,b.item_color");

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
				 <td style="border:1px solid black"><?
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
						$nameArray_color_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trim_group=". $result_item[csf('trim_group')]." and a.po_break_down_id=". $result_item[csf('po_id')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and  b.description='". $result_itemdescription[csf('description')]."' and b.brand_supplier='".$result_itemdescription[csf('brand_supplier')]."' and b.item_color='".$result_itemdescription[csf('item_color')]."'");
						}
						if($db_type==2)
				        {
						$nameArray_color_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trim_group=". $result_item[csf('trim_group')]."  and a.po_break_down_id=". $result_item[csf('po_id')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and nvl( b.description,0)=nvl('". $result_itemdescription[csf('description')]."',0) and nvl(b.brand_supplier,0)=nvl('".$result_itemdescription[csf('brand_supplier')]."',0) and nvl(b.item_color,0)=nvl('".$result_itemdescription[csf('item_color')]."',0)");
						}

                foreach($nameArray_color_size_qnty as $result_color_size_qnty)
                {
                ?>
                <td style="border:1px solid black; text-align:right">
                <?
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
                <td  style="border:1px solid black;  text-align:right"><strong><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></strong></td>
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
                <td width="70%" style="border:1px solid black; text-align:left"><strong><? echo number_format($booking_grand_total,2);?></strong></td>
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

    </div> <!--class="footer_signature"-->
    <div  style="margin-top:-5px;">
         <?
          echo signature_table(132, $cbo_company_name, "1330px",1);
		 ?>
      	</div>
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
        <script type="text/javascript" src="../../../../js/jquery.js"></script>
        <script type="text/javascript" src="../../../../js/jquerybarcode.js"></script>
    <?
    }else {
        ?>
         <script type="text/javascript" src="../../../js/jquery.js"></script>
        <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
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


if ($action=="unapp_request_popup"){
	$menu_id=$_SESSION['menu_id'];
	$user_id=$_SESSION['logic_erp']['user_id'];

	echo load_html_head_contents("Un Approval Request","../../../../", 1, 1, $unicode);
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

				var data="action=save_update_delete_unappv_request&operation="+operation+get_submitted_data_string('unappv_request*wo_id*page_id*user_id',"../../../../");
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
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
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
?>