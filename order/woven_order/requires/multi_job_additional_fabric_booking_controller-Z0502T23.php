<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
include('../../../includes/class4/class.conditions.php');
include('../../../includes/class4/class.reports.php');
include('../../../includes/class4/class.fabrics.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');


function signature_table1($report_id, $company, $width, $template_id="", $padding_top = 70,$prepared_by='') {
	if ($template_id != '') {
		$template_id = " and template_id=$template_id ";
	}
	$sql = sql_select("select designation,name,activities,prepared_by from variable_settings_signature where report_id=$report_id and company_id=$company   and status_active=1  $template_id order by sequence_no");


	if($sql[0][csf("prepared_by")]==1){
		list($prepared_by,$activities)=explode('**',$prepared_by);
		$sql_2[100] = array ( DESIGNATION => 'Prepared By' ,NAME => $prepared_by, ACTIVITIES =>$activities, PREPARED_BY => 0 );
		$sql=$sql_2+$sql;
	}

	$count = count($sql);
	$td_width = floor($width / $count);
	$standard_width = $count * 150;
	if ($standard_width > $width) {
		$td_width = 150;
	}
	$no_coloumn_per_tr = floor($width / $td_width);
	$i = 1;
	if ($count == 0) {$message = "<b>Note: This is Software Generated Copy , Signature is not Required.</b>";}
	$signature_data = '<table id="signatureTblId" width="' . $width . '" style="padding-top:' . $padding_top . 'px;"><tr><td width="100%" height="' . $padding_top . '" colspan="' . $count . '">' . $message . '</td></tr><tr>';
	foreach ($sql as $row) {
		$signature_data .= '<td width="' . $td_width . '" align="center" valign="top">
		<strong>' . $row[csf("activities")] . '</strong><br>
		<strong style="text-decoration:overline">' . $row[csf("designation")] . "</strong><br>" . $row[csf("name")] . '</td>';
		if ($i % $no_coloumn_per_tr == 0) {
			$signature_data .= '</tr><tr><td width="100%" height="70" colspan="' . $no_coloumn_per_tr . '"></td></tr>';
		}
		$i++;
	}
	$signature_data .= '</tr></table>';
	return $signature_data;
}


//---------------------------------------------------- Start---------------------------------------------------------------------------
function load_drop_down_supplier($data){
	$data=explode("_",$data);
	$pay_mode_id=$data[0];
	$tag_buyer_id=$data[1];
	$tag_comp_id=$data[2];
	if($pay_mode_id==5 || $pay_mode_id==3){
	   echo create_drop_down( "cbo_supplier_name", 120, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "-Select Company-", "", "get_php_form_data( this.value, 'load_drop_down_attention', 'requires/multi_job_additional_fabric_booking_controller');",0,"" );
	}
	else
	{
		$tag_buyer=return_field_value("tag_buyer as tag_buyer", "lib_supplier_tag_buyer", "tag_buyer=$tag_buyer_id","tag_buyer");
		if($tag_buyer!='')
		{
			$tag_by_buyer=sql_select("SELECT supplier_id from lib_supplier_tag_buyer where tag_buyer = $tag_buyer_id group by supplier_id");
			foreach ($tag_by_buyer as $row) {
				$supplier_arr2[$row[csf('supplier_id')]] = $row[csf('supplier_id')];
			}
			$supplier_string2=implode(',', $supplier_arr2);
			$tag_another_buyer=sql_select("SELECT supplier_id from lib_supplier_tag_buyer where tag_buyer != $tag_buyer_id and supplier_id not in ($supplier_string2) group by supplier_id");
			foreach ($tag_another_buyer as $row) {
				$supplier_arr[$row[csf('supplier_id')]] = $row[csf('supplier_id')];
			}
			//$supplier_string=implode(',', $supplier_arr);
			function where_con_not_in_using_array($arrayData,$dataType=0,$table_coloum){
				$chunk_list_arr=array_chunk($arrayData,999);
				$p=1;
				foreach($chunk_list_arr as $process_arr)
				{
					if($dataType==0){
						if($p==1){$sql .=" and (".$table_coloum." not in(".implode(',',$process_arr).")"; }
						else {$sql .=" or ".$table_coloum." not in(".implode(',',$process_arr).")";}
					}
					else{
						if($p==1){$sql .=" and (".$table_coloum." not in('".implode("','",$process_arr)."')"; }
						else {$sql .=" or ".$table_coloum." not in('".implode("','",$process_arr)."')";}
					}
					$p++;
				}
				
				$sql.=") ";
				return $sql;
			}
			$supplier_string='';
			if(count($supplier_arr))
			{
				$supplier_string=where_con_not_in_using_array($supplier_arr,0,"c.id");
			}
			$tag_buy_supp="select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and b.party_type in (4,5) and a.tag_company='$tag_comp_id' and c.status_active=1 and c.is_deleted=0 $supplier_string group by c.id, c.supplier_name order by c.supplier_name";
			//echo $tag_buy_supp; die;
			//$tag_buy_supp="select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c,lib_supplier_tag_buyer d where c.id=b.supplier_id and a.supplier_id = b.supplier_id and d.supplier_id=c.id and d.supplier_id=a.supplier_id  and d.supplier_id=b.supplier_id and b.party_type  in (4,5) and a.tag_company='$tag_comp_id' and c.status_active=1 and c.is_deleted=0  and d.tag_buyer=$tag_buyer group by c.id, c.supplier_name order by c.supplier_name";
		}
		else
		{
			$tag_buy_supp="select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and b.party_type in (4,5) and a.tag_company='$tag_comp_id' and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name";
		}
		//echo $tag_buy_supp;
		$cbo_supplier_name= create_drop_down( "cbo_supplier_name", 120, $tag_buy_supp,"id,supplier_name", 1, "-Select Supplier-",$selected,"get_php_form_data( this.value, 'load_drop_down_attention', 'requires/multi_job_additional_fabric_booking_controller');","");
	}
	return $cbo_supplier_name;
	exit();
}

if ($action=="send_mail_report_setting_first_select"){
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=2 and report_id=26 and is_deleted=0 and status_active=1");
	echo $print_report_format;
	exit();
}
if ($action=="item_group_uom"){
	//echo "select order_uom from lib_item_group where id ='".$data."' and is_deleted=0 and status_active=1";die;
	$uom_id=return_field_value("order_uom","lib_item_group","id ='".$data."' and is_deleted=0 and status_active=1","order_uom");
	if($uom_id) $uom_id=$uom_id;else $uom_id=0;
	echo $uom_id;
	exit();
}
//


if ($action=="populate_variable_setting_data"){
	$data_array=sql_select("select exeed_budge_qty,exeed_budge_amount,amount_exceed_level from variable_order_tracking where company_name='$data' and item_category_id=4 and variable_list=26 and status_active=1 and is_deleted=0");
	foreach ($data_array as $row){
		echo "document.getElementById('exeed_budge_qty').value = '".$row[csf("exeed_budge_qty")]."';\n";
		echo "document.getElementById('exeed_budge_amount').value = '".$row[csf("exeed_budge_amount")]."';\n";
		echo "document.getElementById('amount_exceed_level').value = '".$row[csf("amount_exceed_level")]."';\n";
	}
//	echo "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data' and b.party_type=4 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name";
	$cbo_supplier_name     = create_drop_down( "cbo_supplier_name", 120, "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data' and b.party_type in (9) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name","id,supplier_name", 1, "-Select Supplier-",$selected,"get_php_form_data( this.value, \'load_drop_down_attention\', \'requires/multi_job_additional_fabric_booking_controller\');","");
	$cbo_buyer_name= create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected,"check_paymode(this.value);","");

	echo "document.getElementById('supplier_td').innerHTML = '".$cbo_supplier_name."';\n";
	echo "document.getElementById('buyer_td').innerHTML = '".$cbo_buyer_name."';\n";
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=2 and report_id=26 and is_deleted=0 and status_active=1");
	echo "document.getElementById('report_ids').value = '".$print_report_format."';\n";
	echo "print_report_button_setting('".$print_report_format."');\n";
	$sql_result=sql_select("select tna_integrated from variable_order_tracking where company_name='$data' and variable_list=14 and status_active=1 and is_deleted=0");
	$maintain_setting=$sql_result[0][csf('tna_integrated')];
	if($maintain_setting==1) {
		echo "document.getElementById('lib_tna_intregrate').value = '1';\n";
	}
	else {
		echo "document.getElementById('lib_tna_intregrate').value = '0';\n";
	}
	exit();
}

if ($action=="load_drop_down_buyer"){
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "check_paymode(this.value);","" );
	exit();
}

if ($action=="load_drop_down_buyer_pop"){
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by  buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","" );
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
	//echo $str_data;
	$buyer_arr=return_library_array("select id, short_name from lib_buyer",'id','short_name');
	$postatus="";
	if(trim($txt_booking_no)!="")
	{
		$sqlBooking=sql_select("Select po_break_down_id from wo_booking_dtls where booking_no='$txt_booking_no' and is_deleted = 0 and status_active=1");
		$pobooking=$sqlBooking[0][csf('po_break_down_id')];
		
		$sql_result=sql_select("select is_confirmed from wo_po_break_down where id='$pobooking'");
		$postatus=$sql_result[0][csf('is_confirmed')];
	}

	?>
	<script>
	var cbo_level='<? echo $cbo_level; ?>';
	var po_job_level=cbo_level;
		
		
		var selected_id = new Array, selected_name = new Array();
		function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count-1;
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

		var selected_id = new Array();
		var selected_item=new Array();
		var selected_po=new Array();

		function js_set_value( str ) {
			if($("#search"+str).css("display") !='none'){
				var postr=$('#txt_po_id' + str).val();
				
				//$sql_row['id'].'_'.$sql_row['is_confirmed'].'_'.$sql_row['fabric_source'].'_'.$maintain_textile; ?>
				var podata=postr.split("_");
				podata[3]=podata[3].replace("'", "");
				//alert( $('#txtpostatus').val()+'_'+podata[1]+'_'+podata[2]+'_'+podata[3] )
				if( $('#txtpostatus').val()!="" && ($('#txtpostatus').val()!=podata[1] && podata[2]==1 && podata[3]==2))
				{
					alert('PO Status Mixing Not Allowed.');
					return;
				}
				else
				{
					document.getElementById('txtpostatus').value=podata[1];
				}
				
				var txt_tna_date_check=$('#txt_tna_date_check' + str).val();
				if(txt_tna_date_check==1)
				{
					alert('TNA process not found for this PO');
				}
				else{
					toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
					if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
						selected_id.push( $('#txt_individual_id' + str).val() );
						selected_item.push($('#pre_cost_dtls_id' + str).val());
						selected_po.push(podata[0]);
					}
					else{
						for( var i = 0; i < selected_id.length; i++ ) {
							if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
						}
						selected_id.splice( i, 1 );
						selected_item.splice( i,1 );
						selected_po.splice( i,1 );
					}
				}
			}
			var id = '';
			var pre_cost_dtls_id='';
			var txt_po_id='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				pre_cost_dtls_id+=selected_item[i]+ ',';
				txt_po_id+=selected_po[i]+ ',';
			}
			id = id.substr( 0, id.length - 1 );
			pre_cost_dtls_id = pre_cost_dtls_id.substr( 0, pre_cost_dtls_id.length - 1 );
			txt_po_id = txt_po_id.substr( 0, txt_po_id.length - 1 );
			$('#txt_selected_id').val( id );
			$('#txt_pre_cost_dtls_id').val( pre_cost_dtls_id );
			$('#txt_selected_po').val( txt_po_id );
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
            <input type="hidden" id="txt_booking" value="" />
            <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
                <table width="850" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                    <thead>
                        <tr>
                            <th width="100">Style Ref</th>
                            <th width="80">Job No</th>
                            <th width="100">Dealing Merchant</th>
                            <th width="80">Int. Ref. No</th>
                            <th width="100">Order No</th>
                            <th width="130" colspan="2">Pub. Ship Date Range</th>
                            <th>&nbsp;
                                <input type="hidden"  style="width:20px" name="txt_garments_nature" id="txt_garments_nature" value="<? echo $garments_nature;?>" />
                                <input type="hidden" name="cbo_booking_month" id="cbo_booking_month" value="<? echo $cbo_booking_month;?>" />
                                <input type="hidden" name="cbo_booking_year" id="cbo_booking_year" value="<? echo $cbo_booking_year;?>" />
                                <input type="hidden" name="cbo_company_name" id="cbo_company_name" value="<? echo $company_id;?>" />
                                <input type="hidden" style="width:20px" name="cbo_buyer_name" id="cbo_buyer_name" value="<? echo $cbo_buyer_name;?>" />
                                <input type="hidden" name="cbo_currency" id="cbo_currency" value="<? echo $cbo_currency;?>" />
                                <input type="hidden" name="txt_booking_date" id="txt_booking_date" value="<? //echo $txt_booking_date;?>" />
                                <input type="hidden" name="cbo_currency_job" id="cbo_currency_job" value="<? echo $cbo_currency_job;?>" />
                                <input type="hidden" style="width:20px" name="cbo_supplier_name" id="cbo_supplier_name" value="<? echo $cbo_supplier_name;?>" /> 
                                <input type="hidden" name="cbo_item_from" id="cbo_item_from" value="<? echo $cbo_item_from;?>" />
                                
                                <input type="hidden" class="text_boxes" readonly style="width:550px" id="txt_selected_po">
                                <input type="hidden" id="txt_selected_id">
                                <input type="hidden" id="txt_pre_cost_dtls_id">
                                <input type="hidden" id="txtpostatus" value="<?=$postatus; ?>">
                            </th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:90px"></td>
                        <td><input name="txt_job" id="txt_job" class="text_boxes" style="width:70px"></td>
                        <td><? echo create_drop_down( "cbo_dealing_merchant", 100, "select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0 group by id, team_member_name order by team_member_name ASC","id,team_member_name", 1, "-Deal. Merchant-", $selected, "" ); ?></td>
                        <td><input name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:70px"></td>
                        <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:90px"></td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From" value="<? echo $from_date; ?>" <? //echo $date_disabled; ?>></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To" value="<? echo $to_date; ?>" <? //echo $date_disabled; ?>></td>
                        <td>
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('cbo_item_from').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_currency').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_job').value+'_'+document.getElementById('txt_ref_no').value+'_'+'<? echo $txt_booking_no; ?>'+'_'+'<? echo $cbo_level; ?>'+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_dealing_merchant').value+'_'+'<? echo $txt_booking_date; ?>'+'_'+'<? echo $cbo_source; ?>'+'_'+'<? echo $cbo_pay_mode; ?>'+'_'+'<? echo $garments_nature; ?>', 'create_fnc_process_data', 'search_div', 'multi_job_additional_fabric_booking_controller','setFilterGrid(\'tbl_list_search\',-1)')" style="width:60px;" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="8" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
                    <tr>
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
	//echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	//echo $data;
	//extract($_REQUEST);
	extract(check_magic_quote_gpc($_REQUEST));
	$data=explode('_',$data);
	$company_id=$data[0];
	$cbo_buyer_name=$data[1];
	$cbo_supplier_name=$data[2];
	$cbo_item_from=$data[3];
	$cbo_year_selection=$data[4];
	$cbo_currency=$data[5];
	
	$txt_style=$data[6];
	$txt_order_search=$data[7];
	$txt_job=$data[8];
	$ref_no=$data[9];
	$booking_no=$data[10];
	$cbo_level=$data[11];
	$fromDate=$data[12];
	$toDate=$data[13];
	$dealing_merchant=$data[14];
	$booking_date=$data[15];
	$cbo_source=$data[16];
	$cbo_pay_mode=$data[17];
	$garments_nature=$data[18];

	if($txt_style == '' && $txt_job == '' && $ref_no == '' && $txt_order_search == '' && $dealing_merchant==0 && $fromDate == '' && $toDate =='')
	{
		echo "<div align='center'><span style='color:red; font-weight:bold; font-size:20px; text-align:center'>Please select any search data.</span></div> ";
		die;
	}

	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";

	$job_year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year_selection";

	if ($txt_style!="") $style_cond=" and a.style_ref_no='$txt_style'"; else $style_cond=$txt_style;
	if ($txt_order_search!="") $order_cond=" and b.po_number='$txt_order_search'"; else $order_cond="";
	if ($ref_no!="") $ref_cond=" and b.grouping='$ref_no'"; else $ref_cond="";
	if ($txt_job!="") $job_cond=" and a.job_no_prefix_num='$txt_job'"; else $job_cond ="";
	if ($dealing_merchant!=0) $dealing_merchant_cond=" and a.dealing_marchant='$dealing_merchant'"; else $dealing_merchant_cond ="";

	$buyer_arr=return_library_array("select id, short_name from lib_buyer",'id','short_name');
	if($db_type==0) $year_field="YEAR(a.insert_date)";
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY')";

	$shipment_date =""; $class_datecond ="";
	if ($fromDate!="" &&  $toDate!="")
	{
		if ($fromDate!="" &&  $toDate!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($fromDate, "yyyy-mm-dd", "-",1)."' and '".change_date_format($toDate, "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";

		if ($fromDate!="" &&  $toDate!="") $class_datecond = "between '".change_date_format($fromDate, "yyyy-mm-dd", "-",1)."' and '".change_date_format($toDate, "yyyy-mm-dd", "-",1)."'"; else $class_datecond ="";
			//$year_field="to_char(a.insert_date,'YYYY')";
	}
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1200" class="rpt_table"  >
        <thead>
        	<th width="30">SL</th>
            <th width="80">Job No</th>
            <th width="80">Po No</th>
            <th width="80">Order Status</th>
            <th width="100">Item</th>
            <th width="100">Body Part</th>
            <th width="100">Color Type</th>
             
            <th width="100">Construction</th>
            <th width="180">Composition</th>
            <th width="70">Gsm</th>
            <th width="80">Fabric Nature</th>
            <th width="70">Fabric Soutce</th>
            <th>UOM</th>
        </thead>
	</table>
	<div style="width:1200px; overflow-y:scroll; max-height:320px;" id="buyer_list_view" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1180" class="rpt_table" id="tbl_list_search" >
        <?
		$cbo_item_from=str_replace("'","",$cbo_item_from);
		
	if($cbo_item_from==1) //Item From Pre Costing....
	{
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
			if(str_replace("'","",$fromDate)!='' && str_replace("'","",$toDate)!=''){
				$condition->pub_shipment_date($class_datecond);
			}
			if(str_replace("'","",$ref_no)!='')
			{
				$condition->grouping("='$ref_no'");
			}

			$condition->init();
			$fabric= new fabric($condition);
			//echo $fabric->getQuery(); die;
			$req_qty_arr=$fabric->getQtyArray_by_orderAndFabriccostid_knitAndwoven_greyAndfinish();
			//$trims= new fabric($condition);
			//$req_amount_arr=$fabric->getAmountArray_by_orderAndPrecostdtlsid();

		  $sql_job="select a.job_no_prefix_num, a.job_no, $year_field  as year, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.id, b.po_number, b.file_no, b.grouping, b.po_quantity as plan_cut from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.company_name=$company_id and b.shiping_status not in(3) $buyer_id_cond $job_cond $dealing_merchant_cond $order_cond $ref_cond $style_cond $job_year_cond $shipment_date ";
			//echo $sql_job; die;
			$sql_jobRes=sql_select($sql_job); $jobData_arr=array(); $tot_rows=0; $poIds=''; $jobNo='';
			foreach($sql_jobRes as $jrow)
			{
				$tot_rows++;
				$poIds.=$jrow[csf('id')].",";
				$jobNo.="'".$jrow[csf('job_no')]."',";
				$jobData_arr[$jrow[csf('id')]]['jobPre']=$jrow[csf('job_no_prefix_num')];
				$jobData_arr[$jrow[csf('id')]]['job_no']=$jrow[csf('job_no')];
				$jobData_arr[$jrow[csf('id')]]['year']=$jrow[csf('year')];
				$jobData_arr[$jrow[csf('id')]]['company_name']=$jrow[csf('company_name')];
				$jobData_arr[$jrow[csf('id')]]['buyer_name']=$jrow[csf('buyer_name')];
				$jobData_arr[$jrow[csf('id')]]['currency_id']=$jrow[csf('currency_id')];
				$jobData_arr[$jrow[csf('id')]]['style_ref_no']=$jrow[csf('style_ref_no')];
				$jobData_arr[$jrow[csf('id')]]['po_number']=$jrow[csf('po_number')];
				$jobData_arr[$jrow[csf('id')]]['file_no']=$jrow[csf('file_no')];
				$jobData_arr[$jrow[csf('id')]]['grouping']=$jrow[csf('grouping')];
				$jobData_arr[$jrow[csf('id')]]['plan_cut']=$jrow[csf('plan_cut')];
			}
			unset($sql_jobRes);

			$poIds=chop($poIds,','); $poIds_bom_cond=""; $poIds_booking_cond=""; $poIds_tna_cond="";
			if($db_type==2 && $tot_rows>1000)
			{
				$poIds_bom_cond=" and (";
				$poIds_booking_cond=" and (";
				$poIds_tna_cond=" and (";
				$poIdsArr=array_chunk(explode(",",$poIds),999);
				foreach($poIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$poIds_bom_cond.=" c.po_break_down_id in($ids) or ";
					$poIds_booking_cond.=" b.po_break_down_id in($ids) or ";
					$poIds_tna_cond.=" po_number_id in($ids) or ";
				}

				$poIds_bom_cond=chop($poIds_bom_cond,'or ');
				$poIds_bom_cond.=")";

				$poIds_booking_cond=chop($poIds_booking_cond,'or ');
				$poIds_booking_cond.=")";
				$poIds_tna_cond=chop($poIds_tna_cond,'or ');
				$poIds_tna_cond.=")";
			}
			else
			{
				$poIds_bom_cond=" and c.po_break_down_id in ($poIds)";
				$poIds_booking_cond=" and b.po_break_down_id in ($poIds)";
				$poIds_tna_cond=" and po_number_id in ($poIds)";
			}

			$jobNos=implode(",",array_filter(array_unique(explode(",",$jobNo))));

			$cu_booking_arr=array();
			$sql_cu_booking=sql_select("select b.pre_cost_fabric_cost_dtls_id, b.po_break_down_id, b.wo_qnty as cu_wo_qnty, b.amount as cu_amount from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $poIds_booking_cond");
			foreach($sql_cu_booking as $rowcu){
				$cu_booking_arr[$rowcu[csf('pre_cost_fabric_cost_dtls_id')]][$rowcu[csf('po_break_down_id')]]['cu_wo_qnty']+=$rowcu[csf('cu_wo_qnty')];
				$cu_booking_arr[$rowcu[csf('pre_cost_fabric_cost_dtls_id')]][$rowcu[csf('po_break_down_id')]]['cu_amount']+=$rowcu[csf('cu_amount')];
				$trimpreIdArr[$rowcu[csf('pre_cost_fabric_cost_dtls_id')]]=$rowcu[csf('pre_cost_fabric_cost_dtls_id')];
			}
			unset($sql_cu_booking);
			//echo $previouse_pre_id=implode(",",$trimpreIdArr);
			

			$sql_supp="select fabric_id from wo_pre_cost_fabric_supplier where job_no in ($jobNos) and supplier_id in($cbo_supplier_name) and is_deleted=0 and status_active=1";
			$sql_suppRes=sql_select( $sql_supp ); $fabric_id="";
			foreach($sql_suppRes as $row)
			{
				$fabric_id.=$row[csf('fabric_id')].",";
			}
			unset($sql_suppRes);
			$fabric_ids=chop($fabric_id,',');
			if($fabric_ids!="") $fabric_idCond="and (b.id in ($fabric_ids) or b.nominated_supp_multi is null)"; else $fabric_idCond=" and (b.nominated_supp_multi is null or b.nominated_supp_multi=0)";
			
			$tnasql=sql_select("select po_number_id,task_finish_date,task_number from tna_process_mst where is_deleted= 0 and status_active=1 $poIds_tna_cond");

			foreach($tnasql as $tnarow){
				$task_finish_date_arr[$tnarow[csf('po_number_id')]][$tnarow[csf('task_number')]]=$tnarow[csf('task_finish_date')];
			}
			unset($tnasql);
				
			$tna_integrated=return_field_value("tna_integrated","variable_order_tracking","company_name='$company_id' and variable_list=14 and status_active=1 and is_deleted=0","tna_integrated");
			//echo $tna_integrated;

			$approval_allow=sql_select("select b.id, b.page_id, b.approval_need, b.allow_partial, b.validate_page,a.setup_date from approval_setup_mst a,approval_setup_dtls b where a.id=b.mst_id and a.company_id='$data[0]' and a.status_active=1 and b.page_id=25 and b.status_active=1 and b.is_deleted=0 order by b.id desc ");
			if($approval_allow[0][csf("approval_need")]==1 && $approval_allow[0][csf("allow_partial")]==1){
				$approval_cond="and a.approved in (1,2,3)";
			}else{
				
				if($approval_allow[0][csf("approval_need")]==2) // Issue Id=26656 for Libas
				{
					$approval_cond="";
				}
				else
				{
				$approval_cond="and a.approved in (1)";
				}
			}
			$source_cond='';
			if(!empty($cbo_source))
			{
				if($cbo_source*1==1)
				{
					$source_cond=" and b.source_id in (1,0)";
				}
				else{
					$source_cond=" and b.source_id in (2,0)";
				}
			}
			
			$sql= 'SELECT a.job_no AS "job_no",  b.id AS "id", b.po_number AS "po_number", b.is_confirmed as "is_confirmed", c.item_number_id AS "item_number_id", d.id AS "pre_cost_dtls_id", d.body_part_id AS "body_part_id", d.construction AS "construction", d.composition AS "composition", d.fab_nature_id AS "fab_nature_id", d.fabric_source AS "fabric_source", d.color_type_id AS "color_type_id", d.lib_yarn_count_deter_id AS "lib_yarn_count_deter_id", d.uom AS "uom", d.gsm_weight AS "gsm_weight", min(e.id) AS "eid" 
			from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_fabric_cost_dtls d, wo_pre_cos_fab_co_avg_con_dtls e 
			where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and e.cons!=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.company_name='.$company_id.' and b.shiping_status not in(3) '.$buyer_id_cond. $job_cond. $dealing_merchant_cond. $order_cond .$ref_cond .$style_cond .$job_year_cond. $shipment_date ." group by a.job_no, b.id, b.po_number, b.is_confirmed, c.item_number_id, d.id, d.color_type_id, d.body_part_id, d.construction, d.composition, d.fab_nature_id, d.fabric_source, d.lib_yarn_count_deter_id, d.uom, d.gsm_weight";

			$i=1; $total_req=0; $total_amount=0;
			//echo $sql; die;
			//if($poIds_bom_cond!='') //Check Need for Shipment Status
			$nameArray=sql_select( $sql );

			if(count($nameArray)>0){
				foreach($nameArray as $sql_row){
					$reqQty=0;
					if($garments_nature==2){
						$reqQty=$req_qty_arr['knit']['grey'][$sql_row['id']][$sql_row['pre_cost_dtls_id']][$sql_row['uom']];
					}
					if($garments_nature==3){
						$reqQty=$req_qty_arr['woven']['grey'][$sql_row['id']][$sql_row['pre_cost_dtls_id']][$sql_row['uom']];
					}
		
					$cuBooking=$cu_booking_data_arr[$sql_row['pre_cost_dtls_id']][$sql_row['id']];
					$balQty=number_format($reqQty-$cuBooking,4,".","");
		
					$tna_date=$tna_data[$sql_row['id']];
					if($maintain_setting==1 && $tna_date=="") $tna_check=1;  else $tna_check=0;
		
					if($balQty >0 ){
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr style="text-decoration:none; cursor:pointer" bgcolor="<?=$bgcolor; ?>" id="search<?=$i; ?>" onClick="js_set_value(<?=$i; ?>);">
							<td width="30"><?=$i; ?>
								<input type="hidden" name="txt_individual_id" id="txt_individual_id<?=$i; ?>" value="<?=$sql_row['eid']; ?>"/>
								<input type="hidden" name="pre_cost_dtls_id" id="pre_cost_dtls_id<?=$i; ?>" value="<?=$sql_row['pre_cost_dtls_id']; ?>"/>
								<input type="hidden" name="txt_po_id" id="txt_po_id<?=$i; ?>" value="'<?=$sql_row['id'].'_'.$sql_row['is_confirmed'].'_'.$sql_row['fabric_source'].'_'.$maintain_textile; ?>'"/>
								<input type="hidden" name="txt_tna_date_check" id="txt_tna_date_check<?=$i; ?>" value="<?=$tna_check; ?>"/>
							</td>
							<td width="80" style="word-break:break-all"><?=$sql_row['job_no']; ?></td>
							<td width="80" style="word-break:break-all"><?=$sql_row['po_number']; ?></td>
							<td width="80" style="word-break:break-all"><?=$order_status[$sql_row['is_confirmed']]; ?></td>
							<td width="100" style="word-break:break-all"><?=$garments_item[$sql_row['item_number_id']]; ?></td>
							<td width="100" style="word-break:break-all"><?=$body_part[$sql_row['body_part_id']]; ?></td>
							<td width="100" style="word-break:break-all"><?=$color_type[$sql_row['color_type_id']]; ?></td>
							<td width="100" style="word-break:break-all"><?=$sql_row['construction']; ?></td>
							<td width="180" style="word-break:break-all"><?=$sql_row['composition']; ?></td>
							<td width="70" style="word-break:break-all"><?=$sql_row['gsm_weight']; ?></td>
							<td width="80" style="word-break:break-all"><?=$item_category[$sql_row['fab_nature_id']]; ?></td>
							<td width="70" style="word-break:break-all"><?=$fabric_source[$sql_row['fabric_source']]; ?></td>
							<td><?=$unit_of_measurement[$sql_row['uom']]; ?></td>
						</tr>
						<?
						$i++;
					}
				}
			}
			else{
				foreach ($fabric_source_arr as $fs_id) {
					if($fs_id != $cbo_fabric_source){
						echo "<span style='font-size:24px; font-weight:bold; color:#FF0000; margin-top:10px'>Mismatch Fabric Source or Related Components with budget</span>";
						exit();
					}
				}
			} //Previous Pre cost Id Check End
	} //Item From *****************End
	else
	{
		$sql_job="select a.job_no_prefix_num, a.job_no, $year_field  as year, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.id, b.po_number, b.file_no, b.grouping, b.po_quantity as plan_cut from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.company_name=$company_id and b.shiping_status not in(3) $buyer_id_cond $job_cond $dealing_merchant_cond $order_cond $ref_cond $style_cond $job_year_cond $shipment_date ";
		//echo $sql_job; 
		$sql_jobRes=sql_select($sql_job); $jobData_arr=array(); $tot_rows=0; $poIds=''; $jobNo='';
		foreach($sql_jobRes as $jrow)
		{
			$tot_rows++;
			$poIds.=$jrow[csf('id')].",";
			$jobNo.="'".$jrow[csf('job_no')]."',";
			$jobData_arr[$jrow[csf('id')]]['jobPre']=$jrow[csf('job_no_prefix_num')];
			$jobData_arr[$jrow[csf('id')]]['job_no']=$jrow[csf('job_no')];
			$jobData_arr[$jrow[csf('id')]]['year']=$jrow[csf('year')];
			$jobData_arr[$jrow[csf('id')]]['company_name']=$jrow[csf('company_name')];
			$jobData_arr[$jrow[csf('id')]]['buyer_name']=$jrow[csf('buyer_name')];
			$jobData_arr[$jrow[csf('id')]]['currency_id']=$jrow[csf('currency_id')];
			$jobData_arr[$jrow[csf('id')]]['style_ref_no']=$jrow[csf('style_ref_no')];
			$jobData_arr[$jrow[csf('id')]]['po_number']=$jrow[csf('po_number')];
			$jobData_arr[$jrow[csf('id')]]['file_no']=$jrow[csf('file_no')];
			$jobData_arr[$jrow[csf('id')]]['grouping']=$jrow[csf('grouping')];
			$jobData_arr[$jrow[csf('id')]]['plan_cut']=$jrow[csf('plan_cut')];
		}
		unset($sql_jobRes);

		$poIds=chop($poIds,','); $poIds_bom_cond=""; $poIds_booking_cond=""; $poIds_tna_cond="";
		if($db_type==2 && $tot_rows>1000)
		{
			$poIds_bom_cond=" and (";
			$poIds_booking_cond=" and (";
			$poIds_tna_cond=" and (";
			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$poIds_bom_cond.=" c.po_break_down_id in($ids) or ";
				$poIds_booking_cond.=" b.po_break_down_id in($ids) or ";
				$poIds_tna_cond.=" po_number_id in($ids) or ";
			}

			$poIds_bom_cond=chop($poIds_bom_cond,'or ');
			$poIds_bom_cond.=")";

			$poIds_booking_cond=chop($poIds_booking_cond,'or ');
			$poIds_booking_cond.=")";
			$poIds_tna_cond=chop($poIds_tna_cond,'or ');
			$poIds_tna_cond.=")";
		}
		else
		{
			$poIds_bom_cond=" and c.po_break_down_id in ($poIds)";
			$poIds_booking_cond=" and b.po_break_down_id in ($poIds)";
			$poIds_tna_cond=" and po_number_id in ($poIds)";
		}

		$jobNos=implode(",",array_filter(array_unique(explode(",",$jobNo))));

		$cu_booking_arr=array();
		$sql_cu_booking=sql_select("select b.pre_cost_fabric_cost_dtls_id, b.po_break_down_id, b.wo_qnty as cu_wo_qnty, b.amount as cu_amount from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $poIds_booking_cond");
        foreach($sql_cu_booking as $rowcu){
            $cu_booking_arr[$rowcu[csf('pre_cost_fabric_cost_dtls_id')]][$rowcu[csf('po_break_down_id')]]['cu_wo_qnty']+=$rowcu[csf('cu_wo_qnty')];
            $cu_booking_arr[$rowcu[csf('pre_cost_fabric_cost_dtls_id')]][$rowcu[csf('po_break_down_id')]]['cu_amount']+=$rowcu[csf('cu_amount')];
			$fabricpreIdArr[$rowcu[csf('pre_cost_fabric_cost_dtls_id')]]=$rowcu[csf('pre_cost_fabric_cost_dtls_id')];
        }
        unset($sql_cu_booking);
		//	echo $previouse_pre_id=implode(",",$trimpreIdArr);
        

		$sql_supp="select trimid from wo_pre_cost_trim_supplier where job_no in ($jobNos) and supplier_id in($cbo_supplier_name) and is_deleted=0 and status_active=1";
		$sql_suppRes=sql_select( $sql_supp ); $trim_id="";
		foreach($sql_suppRes as $row)
		{
			$trim_id.=$row[csf('trimid')].",";
		}
		unset($sql_suppRes);
		$trim_ids=chop($trim_id,',');
		 
			
		$tna_integrated=return_field_value("tna_integrated","variable_order_tracking","company_name='$company_id' and variable_list=14 and status_active=1 and is_deleted=0","tna_integrated");
		//echo $tna_integrated;

		$approval_allow=sql_select("select b.id, b.page_id, b.approval_need, b.allow_partial, b.validate_page,a.setup_date from approval_setup_mst a,approval_setup_dtls b where a.id=b.mst_id and a.company_id='$data[0]' and a.status_active=1 and b.page_id=25 and b.status_active=1 and b.is_deleted=0 order by b.id desc ");
		if($approval_allow[0][csf("approval_need")]==1 && $approval_allow[0][csf("allow_partial")]==1){
			$approval_cond="and a.approved in (1,2,3)";
		}else{
			
			if($approval_allow[0][csf("approval_need")]==2) // Issue Id=26656 for Libas
			{
				$approval_cond="";
			}
			else
			{
			$approval_cond="and a.approved in (1)";
			}
		}
		$source_cond='';
		if(!empty($cbo_source))
		{
			if($cbo_source*1==1)
			{
				$source_cond=" and b.source_id in (1,0)";
			}
			else{
				$source_cond=" and b.source_id in (2,0)";
			}
		}
			
		if($garments_nature==0) $garment_nature_cond=""; else $garment_nature_cond=" and a.garments_nature=$garments_nature";
		
		$sql= 'SELECT a.job_no AS "job_no",  b.id AS "id", b.po_number AS "po_number", b.is_confirmed as "is_confirmed", c.item_number_id AS "item_number_id" 
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_mst d
		where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and b.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.company_name='.$company_id.' and b.shiping_status not in(3) '.$buyer_id_cond. $job_cond. $dealing_merchant_cond. $order_cond .$ref_cond .$style_cond .$job_year_cond. $shipment_date ." group by a.job_no, b.id, b.po_number, b.is_confirmed, c.item_number_id";
		//$garment_nature_cond
        $i=1; $total_req=0; $total_amount=0;
		//echo $sql;
		//if($poIds_bom_cond!='') //Check Need for Shipment Status
		$nameArray=sql_select( $sql );

        foreach ($nameArray as $row)
        {
			if($fabricpreIdArr[$row[csf('wo_pre_cost_trim_cost_dtls')]]=='')
			{
        		if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$poid=$row[csf('po_break_down_id')];
				//else echo "B";
				// echo "B,";
				$cbo_currency_job=$jobData_arr[$poid]['currency_id'];
				$exchange_rate=$row[csf('exchange_rate')];
				if($cbo_currency==$cbo_currency_job){
					$exchange_rate=1;
				}
				$req_qnty=$row[csf('plan_cut')];
				$amount=$row[csf('amount')];
				?>
				<tr style="text-decoration:none; cursor:pointer" bgcolor="<?=$bgcolor; ?>" id="search<?=$i; ?>" onClick="js_set_value(<?=$i; ?>);">
					<td width="30"><?=$i; ?>
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<?=$i; ?>" value="<?=$row['eid']; ?>"/>
						<input type="hidden" name="pre_cost_dtls_id" id="pre_cost_dtls_id<?=$i; ?>" value="<?=$row['pre_cost_dtls_id']; ?>"/>
						<input type="hidden" name="txt_po_id" id="txt_po_id<?=$i; ?>" value="'<?=$row['id'].'_'.$row['is_confirmed'].'_'.$row['fabric_source'].'_'.$maintain_textile; ?>'"/>
						<input type="hidden" name="txt_tna_date_check" id="txt_tna_date_check<?=$i; ?>" value="<?=$tna_check; ?>"/>
					</td>
					<td width="80" style="word-break:break-all"><?=$row['job_no']; ?></td>
					<td width="80" style="word-break:break-all"><?=$row['po_number']; ?></td>
					<td width="80" style="word-break:break-all"><?=$order_status[$row['is_confirmed']]; ?></td>
					<td width="100" style="word-break:break-all"><?=$garments_item[$row['item_number_id']]; ?></td>
					<td width="100" style="word-break:break-all"><?=$body_part[$row['body_part_id']]; ?></td>
					<td width="100" style="word-break:break-all"><?=$color_type[$row['color_type_id']]; ?></td>
					<td width="100" style="word-break:break-all"><?=$row['construction']; ?></td>
					<td width="180" style="word-break:break-all"><?=$row['composition']; ?></td>
					<td width="70" style="word-break:break-all"><?=$row['gsm_weight']; ?></td>
					<td width="80" style="word-break:break-all"><?=$item_category[$row['fab_nature_id']]; ?></td>
					<td width="70" style="word-break:break-all"><?=$fabric_source[$row['fabric_source']]; ?></td>
					<td><?=$unit_of_measurement[$row['uom']]; ?></td>
				</tr>
				<?
				$i++;
				$total_amount+=$amount;
         	}//Previous Pre cost Id Check End
		} 
	} //*******Item From Library End**************
	   
        ?>
        </table>
	</div>
	<table width="1200" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="generate_fabric_booking") 
{
	extract($_REQUEST);
	$txt_order_no_id=str_replace("'","",$txt_selected_po);
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	$fabric_cost_dtls_id=implode(",",array_unique(explode(",",str_replace("'","",$txt_selected_fabric_id))));
	$cbouom=str_replace("'","",$cbouom); 
	$cbo_item_from=str_replace("'","",$cbo_item_from); 
	
	$poidCond=implode(",",$poIdChkArr);
	
	$cu_booking_data_arr=array();
	$sql='select b.pre_cost_fabric_cost_dtls_id AS "pre_cost_fabric_cost_dtls_id", b.po_break_down_id AS "po_break_down_id", b.color_number_id AS "color_number_id", a.id AS "booking_id", a.fin_fab_qnty AS "fin_fab_qnty", a.grey_fab_qnty AS "grey_fab_qnty", a.dia_width AS "dia_width", a.pre_cost_remarks AS "pre_cost_remarks" from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.po_break_down_id=b.po_break_down_id and a.gmts_color_id=b.color_number_id and a.dia_width=b.dia_width and a.po_break_down_id in('.$txt_order_no_id.') and b.cons>0  and a.booking_type=1 and a.status_active=1 and a.is_deleted=0 group by b.pre_cost_fabric_cost_dtls_id, b.po_break_down_id, b.color_number_id, a.id, a.fin_fab_qnty, a.grey_fab_qnty, a.dia_width, a.pre_cost_remarks';
	$dataArray=sql_select($sql);
	foreach($dataArray as $dataArray_row){
		$cu_booking_data_arr[$dataArray_row['pre_cost_fabric_cost_dtls_id']][$dataArray_row['color_number_id']][$dataArray_row['dia_width']][$dataArray_row['pre_cost_remarks']]['cu_booking_qty'][$dataArray_row['po_break_down_id']]+=$dataArray_row['grey_fab_qnty'];
	}
	$condition= new condition();
	if(str_replace("'","",$txt_order_no_id) !=''){
		$condition->po_id("in($txt_order_no_id)");
	}
	$condition->init();
	$fabric= new fabric($condition);
	$req_qty_arr=$fabric->getQtyArray_by_OrderFabriccostidGmtscolorDiaWidthAndRemarks_knitAndwoven_greyAndfinish();
	$req_amount_arr=$fabric->getAmountArray_by_OrderFabriccostidGmtscolorDiaWidthAndRemarks_knitAndwoven_greyAndfinish();
	
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
	//$po_number_arr=return_library_array( "select id,po_number from wo_po_break_down where id in($txt_order_no_id)", "id", "po_number");
	
	$job_sql=sql_select("select a.company_name, a.job_no, b.id, b.po_number, min(c.id) as cid, sum(c.plan_cut_qnty) as plan_cut_qnty from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where b.id in(".$txt_order_no_id.") and b.job_no_mst=a.job_no and b.id=c.po_break_down_id and b.job_no_mst=c.job_no_mst and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 group by a.company_name, a.job_no, b.id, b.po_number, c.color_number_id, c.size_number_id, c.item_number_id");
	 $po_number_arr=array(); $paln_cut_qnty_array=array(); $popaln_cut_qnty_array=array();
	foreach($job_sql as $jrow)
	{
		$po_number_arr[$jrow[csf("id")]]=$jrow[csf("po_number")];
		$paln_cut_qnty_array[$jrow[csf("cid")]]=$jrow[csf("plan_cut_qnty")];
		$popaln_cut_qnty_array[$jrow[csf("id")]]+=$jrow[csf("plan_cut_qnty")];
		$txt_job_no=$jrow[csf("job_no")];
	}
	unset($job_sql);
	$item_ratio_array=return_library_array("select gmts_item_id, set_item_ratio from wo_po_details_mas_set_details where job_no ='$txt_job_no'", "gmts_item_id", "set_item_ratio");
	
	$sql='SELECT a.id AS "id", a.job_no AS "job_no", a.uom AS "uom", a.item_number_id AS "item_number_id", a.body_part_id AS "body_part_id", a.color_type_id AS "color_type_id", a.width_dia_type AS "width_dia_type", a.construction AS "construction", a.composition AS "composition", a.gsm_weight AS "gsm_weight", a.costing_per AS "costing_per", b.po_break_down_id AS "po_break_down_id", b.color_number_id AS "color_number_id", b.dia_width AS "dia_width", b.remarks AS "remarks", c.contrast_color_id AS "contrast_color_id", a.color_size_sensitive AS "color_size_sensitive", d.hs_code as "hs_code" 
	
	from wo_pre_cost_fabric_cost_dtls a join wo_pre_cos_fab_co_avg_con_dtls b on a.id=b.pre_cost_fabric_cost_dtls_id join lib_yarn_count_determina_mst d on d.id=a.lib_yarn_count_deter_id left join wo_pre_cos_fab_co_color_dtls c on  b.pre_cost_fabric_cost_dtls_id=c.pre_cost_fabric_cost_dtls_id and b.color_number_id=c.gmts_color_id   where a.id in('.$fabric_cost_dtls_id.') and b.po_break_down_id in ('.$txt_order_no_id.') and b.cons>0  and a.is_deleted=0 and a.status_active=1 and  d.is_deleted=0 and d.status_active=1 group by a.id, a.job_no, a.uom, a.item_number_id, a.body_part_id, a.color_type_id, a.width_dia_type, a.construction, a.composition, a.gsm_weight, a.costing_per, b.po_break_down_id, b.color_number_id, b.dia_width, b.remarks, c.contrast_color_id, a.color_size_sensitive, d.hs_code';
	//echo $sql; die;
	//echo $txt_order_no_id;
	$sql_data=sql_select($sql);
	
	$powiseCostingPerReqQtyArr=array();
	foreach ($req_qty_arr as $knitwoven=>$knitwovendata)
	{
		foreach ($knitwovendata['grey'] as $poid=>$podata)
		{
			foreach ($podata as $bomid=>$bomdata)
			{
				foreach ($bomdata as $colorid=>$colordata)
				{
					foreach ($colordata as $diawidth=>$diawidthdata)
					{
						foreach ($diawidthdata as $remarks=>$remarksdata)
						{
							foreach ($remarksdata as $uom=>$uomdata)
							{
								$powiseCostingPerReqQtyArr[$bomid][$poid]['greyreqqty']+=$uomdata;
							}
						}
					}
				}
			}
		}
	}
	//print_r($powiseCostingPerReqQtyArr[34805][60978]);
	$job_level_arr=array();
	foreach($sql_data as $sql_row){
		$bom_fabric_dtls_id=$sql_row['id'];
		$poid=$sql_row['po_break_down_id'];
		if($sql_row['color_size_sensitive'] == 3) $item_color=$sql_row['contrast_color_id']; else $item_color=0;
		
		if($item_color== "" || $item_color==0) $item_color=$sql_row['color_number_id'];
		
		if($cbo_fabric_natu==2){
			$req_qty=$req_qty_arr['knit']['grey'][$poid][$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
			$finishreq_qty=$req_qty_arr['knit']['finish'][$poid][$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
			$req_amt=$req_amount_arr['knit']['grey'][$poid][$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
			 if($req_amt>0)
			{
			$rate=$req_amt/$req_qty;
			}
			else $rate=0;
			
			if($req_amt) $req_amt=$req_amt;else $req_amt=0;
			if($finishreq_qty) $finishreq_qty=$finishreq_qty;else $finishreq_qty=0;
			if($req_qty) $req_qty=$req_qty;else $req_qty=0;
		}
		if($cbo_fabric_natu==3){
			$req_qty=$req_qty_arr['woven']['grey'][$poid][$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
			$finishreq_qty=$req_qty_arr['woven']['finish'][$poid][$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
			$req_amt=$req_amount_arr['woven']['grey'][$poid][$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
			 if($req_amt>0)
			{
			$rate=$req_amt/$req_qty;
			}
			else $rate=0;
			 
			if($req_amt) $req_amt=$req_amt;else $req_amt=0;
			if($finishreq_qty) $finishreq_qty=$finishreq_qty;else $finishreq_qty=0;
			if($req_qty) $req_qty=$req_qty;else $req_qty=0;
		}
		$cu_booking_qty=$cu_booking_data_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['cu_booking_qty'][$poid];
		$bal_qty=$req_qty-$cu_booking_qty;
		$bal_amt=$bal_qty*$rate;
		
		if($bal_amt) $bal_amt=$bal_amt;else $bal_amt=0;
		if($bal_qty) $bal_qty=$bal_qty;else $bal_qty=0;
		
		if($sql_row['hs_code']=="") $sql_row['hs_code']="";else $sql_row['hs_code']=$sql_row['hs_code'];
		
		$costing_per=0;
		if($sql_row["costing_per"]==1) $costing_per=12;
		else if($sql_row["costing_per"]==2) $costing_per=1;
		else if($sql_row["costing_per"]==3) $costing_per=24;
		else if($sql_row["costing_per"]==4) $costing_per=36;
		else if($sql_row["costing_per"]==5) $costing_per=48;
		else $costing_per=0;
		
		$itempoWiseReqQty=0;
			
		$itempoWiseReqQty=($powiseCostingPerReqQtyArr[$bom_fabric_dtls_id][$poid]['greyreqqty']/($popaln_cut_qnty_array[$poid]/$item_ratio_array[$sql_row['item_number_id']]))*$costing_per;
		
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['job_no'][$poid]=$sql_row['job_no'];
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['po_id'][$poid]=$poid;
		
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['po_number'][$poid]=$po_number_arr[$poid];
		//=================
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['pre_cost_fabric_cost_dtls_id'][$poid]=$bom_fabric_dtls_id;
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['body_part_id'][$poid]=$sql_row['body_part_id'];
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['construction'][$poid]=$sql_row['construction'];
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['composition'][$poid]=$sql_row['composition'];
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['gsm_weight'][$poid]=$sql_row['gsm_weight'];
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['dia_width'][$poid]=$sql_row['dia_width'];
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['pre_cost_remarks'][$poid]=$sql_row['remarks'];
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['hs_code'][$poid]=$sql_row['hs_code'];
		
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['color_type_id'][$poid]=$sql_row['color_type_id'];
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['width_dia_type'][$poid]=$sql_row['width_dia_type'];
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['uom'][$poid]=$sql_row['uom'];
		//============
		
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['color_number_id'][$poid]=$sql_row['color_number_id'];
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['item_color'][$poid]=$item_color;
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['req_qty'][$poid]=$req_qty;
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['finreq_qty'][$poid]=$finishreq_qty;
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['req_amt'][$poid]=$req_amt;
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['bal_qty'][$poid]=$bal_qty;
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['bal_amt'][$poid]=$bal_amt;
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['rate'][$poid]=$rate;
		$job_level_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['reqqtycostingper'][$poid]=$itempoWiseReqQty;
	}
	//print_r($job_level_arr);
	?>
    <input type="hidden" id="strdata" value='<?=json_encode($job_level_arr); ?>' style="background-color:#CCC"/>
	<table width="1750" class="rpt_table" border="0" rules="all">
        <thead>
            <th width="80">Job No</th>
            <th width="70">Po No</th>
            <th width="100">Body Part</th>
            <th width="80">Color Type</th>
            <th width="80">Dia Width Type</th>
            <th width="100">Construction</th>
            <th width="150">Composition</th>
            <th width="60">Gsm</th>
            <th width="90">Gmts. Color</th>
            <th width="110">Item Color</th>
            <th width="60">Dia</th>
            <th width="80">HS Code</th>
            <th width="80">Process</th>
            <th width="80">Balance Qty</th>
            <th width="80">WO. Qty</th>
            <th width="70">Adj. Qty</th>
            <th width="80">Ac.WO. Qty</th>
            <th width="50">UOM</th>
            <th width="60">Rate</th>
            <th width="100">Amount</th>
            <th>Remark</th>
        </thead>
	</table>
	<table width="1750" class="rpt_table" id="tbl_fabric_booking" border="0" rules="all">
        <tbody>
        <?
        if(str_replace("'","",$cbo_level)==1){
			$i=1;
			foreach($sql_data as $sql_row){
				$bom_fabric_dtls_id=$sql_row['id'];
				$job_no=$sql_row['job_no'];
				$po_break_down_id=$sql_row['po_break_down_id'];
				$body_part_id=$sql_row['body_part_id'];
				$construction=$sql_row['construction'];
				$compositi=$sql_row['composition'];
				$gsm_weight=$sql_row['gsm_weight'];
				$color_type_id=$sql_row['color_type_id'];
				$width_dia_type=$sql_row['width_dia_type'];
				$hscode=$sql_row['hs_code'];
				
				$color_number_id=$sql_row['color_number_id'];
				if($sql_row[csf('color_size_sensitive')] == 3) $item_color=$sql_row['contrast_color_id']; else $item_color=0;
				
				if($item_color== "" || $item_color==0) $item_color=$sql_row['color_number_id'];
				
				$dia_width=$sql_row['dia_width'];
				$pre_cost_remarks=$sql_row['remarks'];
				
				if($cbo_fabric_natu==2){
					$req_qty=$req_qty_arr['knit']['grey'][$sql_row['po_break_down_id']][$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
					$finishreq_qty=$req_qty_arr['knit']['finish'][$sql_row['po_break_down_id']][$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
					$req_amt=$req_amount_arr['knit']['grey'][$sql_row['po_break_down_id']][$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
					$rate=$req_amt/$req_qty;
				}
				if($cbo_fabric_natu==3){
					$req_qty=$req_qty_arr['woven']['grey'][$sql_row['po_break_down_id']][$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
					$finishreq_qty=$req_qty_arr['woven']['finish'][$sql_row['po_break_down_id']][$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
					$req_amt=$req_amount_arr['woven']['grey'][$sql_row['po_break_down_id']][$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']][$sql_row['uom']];
					$rate=$req_amt/$req_qty;
				}
				$cu_booking_qty=$cu_booking_data_arr[$bom_fabric_dtls_id][$sql_row['color_number_id']][$sql_row['dia_width']][$sql_row['remarks']]['cu_booking_qty'][$sql_row['po_break_down_id']];
				$bal_qty=$req_qty-$cu_booking_qty;
				$bal_amt=$bal_qty*$rate;
				
				$costing_per=0;
				if($sql_row["costing_per"]==1) $costing_per=12;
				else if($sql_row["costing_per"]==2) $costing_per=1;
				else if($sql_row["costing_per"]==3) $costing_per=24;
				else if($sql_row["costing_per"]==4) $costing_per=36;
				else if($sql_row["costing_per"]==5) $costing_per=48;
				else $costing_per=0;
				
				$itempoWiseReqQty=0;
					
				$itempoWiseReqQty=($powiseCostingPerReqQtyArr[$bom_fabric_dtls_id][$sql_row['po_break_down_id']]['greyreqqty']/($popaln_cut_qnty_array[$sql_row['po_break_down_id']]/$item_ratio_array[$sql_row['item_number_id']]))*$costing_per;
				
				
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i; ?>">
					<td width="80" style="word-break:break-all"><?=$job_no; ?>
						<input type="hidden" id="txtjob_<?=$i; ?>" value="<?=$job_no; ?>" readonly />
					</td>
					<td width="70" style="word-break: break-all;word-wrap: break-word;"><?=$po_number_arr[$po_break_down_id]; ?>
						<input type="hidden" id="txtpoid_<?=$i; ?>" value="<?=$po_break_down_id; ?>" readonly />
					</td>
					<td width="100" style="word-break:break-all"><?=$body_part[$body_part_id]; ?>
						<input type="hidden" id="txtprecostfabriccostdtlsid_<?=$i; ?>" value="<?=$bom_fabric_dtls_id; ?>" readonly />
						<input type="hidden" id="txtbodypart_<?=$i; ?>" value="<?=$body_part_id; ?>" readonly />
					</td>
					<td width="80" style="word-break:break-all"><?=$color_type[$color_type_id]; ?>
						<input type="hidden" id="txtcolortype_<?=$i; ?>" value="<?=$color_type_id; ?>" readonly />
					</td>
					<td width="80" style="word-break:break-all"><?=$fabric_typee[$width_dia_type]; ?>
						<input type="hidden" id="txtwidthtype_<?=$i; ?>" value="<?=$width_dia_type; ?>" readonly />
					</td>
					<td width="100" style="word-break:break-all"><?=$construction; ?>
						<input type="hidden" id="txtconstruction_<?=$i; ?>" value="<?=$construction; ?>" readonly />
					</td>
					<td width="150" style="word-break:break-all"><?=$compositi; ?>
						<input type="hidden" id="txtcompositi_<?=$i; ?>" value="<?=$compositi; ?>" readonly />
					</td>
					<td width="60" style="word-break:break-all"><?=$gsm_weight; ?>
						<input type="hidden" id="txtgsmweight_<?=$i; ?>" value="<?=$gsm_weight; ?>" readonly />
					</td>
					<td width="90" style="word-break:break-all"><?=$color_library[$color_number_id]; ?>
						<input type="hidden" id="txtgmtcolor_<?=$i; ?>" value="<?=$color_number_id; ?>" readonly />
					</td>
					<td width="110" style="word-break:break-all"><?=$color_library[$item_color]; ?>
						<input type="hidden" id="txtitemcolor_<?=$i; ?>" value="<?=$item_color; ?>" readonly />
					</td>
					<td width="60" style="word-break:break-all"><?=$dia_width; ?>
						<input type="hidden" id="txtdia_<?=$i; ?>" value="<?=$dia_width; ?>" readonly />
					</td>
					<td width="80">
						<input type="text" style="width:80%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txthscode_<?=$i; ?>" value="<?=$hscode; ?>" />
					</td>
					<td width="80" style="word-break:break-all"><?=$pre_cost_remarks; ?>
						<input type="hidden" id="process_<?=$i; ?>" value="<?=$pre_cost_remarks; ?>" readonly />
					</td>
					<td width="80">	
						<input type="text" style="width:80%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtbalqnty_<?=$i; ?>" value="<?=number_format($bal_qty,4,'.',''); ?>" readonly />
						<input type="hidden" id="txtreqqnty_<?=$i; ?>" value="<?=number_format($req_qty,4,'.',''); ?>"  readonly />
						<input type="hidden" id="txtfinreqqnty_<?=$i; ?>" value="<?=number_format($finishreq_qty,4,'.',''); ?>" class="text_boxes_numeric" readonly />
						<input type="hidden" id="cuqnty_<?=$i; ?>" value="<?=number_format($cu_booking_qty,4,'.',''); ?>" readonly />
						<input type="hidden" id="preconskg_<?=$i; ?>" style="width:20px;" value="<?=$itempoWiseReqQty; ?>"/>
					</td>
					<td width="80">
						<input type="text" style="width:90%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoq_<?=$i; ?>" value="<?=number_format($bal_qty,4,'.',''); ?>" onChange="claculate_acwoQty(<?=$i; ?>);" />
						<input type="hidden" style="width:90%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoqprev_<?=$i; ?>" value="<?=number_format($bal_qty,4,'.',''); ?>" class="text_boxes_numeric" readonly />
					</td>
					<td width="70">
						<input type="text" style="width:90%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtadj_<?=$i; ?>" value="<? //=number_format($adj_qty,4,'.',''); ?>" class="text_boxes_numeric"  onChange="claculate_acwoQty(<?=$i; ?>);" />
					</td>
					<td width="80">
						<input type="text" style="width:90%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtacwoq_<?=$i; ?>" value="<?=number_format($bal_qty,4,'.',''); ?>" class="text_boxes_numeric" readonly />
					</td>
					<td width="50" align="center"><?=$unit_of_measurement[$sql_row['uom']]; ?></td>
					<td width="60">
						<input type="text" style="width:70%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<?=$bgcolor; ?>" id="txtrate_<?=$i; ?>" value="<?=number_format($rate,4,'.',''); ?>" class="text_boxes_numeric" onChange="claculate_amount(<?=$i; ?>);" data-pre-cost-rate="<?=number_format($rate,4,'.',''); ?>" data-current-rate="<?=number_format($rate,4,'.',''); ?>" />
					</td>
					<td width="100">
						<input type="text" style="width:90%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<?=$bgcolor; ?>" id="txtamount_<?=$i;?>" value="<?=number_format($bal_amt,4,'.',''); ?>" class="text_boxes_numeric" readonly />
					</td>
					<td>
						<input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<?=$bgcolor; ?>" id="txtremark_<?=$i; ?>" value="" />
						<input type="hidden" id="txtbookingid_<?=$i; ?>" value="" readonly />
					</td>
				</tr>
				<?
				$i++;
			}
        }
        if(str_replace("'","",$cbo_level)==2){
			$i=1;
			foreach($job_level_arr as $precost_id){
				foreach($precost_id as $color_id){
					foreach($color_id as $diawith){
						foreach($diawith as $remarks){
							$job_no=implode(",",array_unique($remarks['job_no']));
							$po_break_down_id=implode(",",array_unique($remarks['po_id']));
							$po_number=implode(",",array_unique($remarks['po_number']));
							$bom_fabric_dtls_id=implode(",",array_unique($remarks['pre_cost_fabric_cost_dtls_id']));
							$body_part_id=implode(",",array_unique($remarks['body_part_id']));
							$construction=implode(",",array_unique($remarks['construction']));
							$compositi=implode(",",array_unique($remarks['composition']));
							$gsm_weight=implode(",",array_unique($remarks['gsm_weight']));
							$color_type_id=implode(",",array_unique($remarks['color_type_id']));
							$width_dia_type=implode(",",array_unique($remarks['width_dia_type']));
							$uom=implode(",",array_unique($remarks['uom']));
							
							$color_number_id=implode(",",array_unique($remarks['color_number_id']));
							$item_color=implode(",",array_unique($remarks['item_color']));
							$dia_width=implode(",",array_unique($remarks['dia_width']));
							$pre_cost_remarks=implode(",",array_unique($remarks['pre_cost_remarks']));
							$hscode=implode(",",array_unique($remarks['hs_code']));
							
							$req_qty=array_sum($remarks['req_qty']);
							$finishreq_qty=array_sum($remarks['finreq_qty']);
							$rate=array_sum($remarks['rate']);
							$req_amt=array_sum($remarks['req_amt']);
							$bal_qty=array_sum($remarks['bal_qty']);
							$bal_amt=array_sum($remarks['bal_amt']);
							$rate=$req_amt/$req_qty;
							$bal_amt=$bal_qty*$rate;
							$cu_booking_qty=array_sum($cu_booking_data_arr[$bom_fabric_dtls_id][$color_number_id][$dia_width][$pre_cost_remarks]['cu_booking_qty']);
							
							//if(number_format($bal_qty,4,'.','') >0){
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i;?>">
                                    <td width="80" style="word-break:break-all"><?=$job_no; ?>
                                    	<input type="hidden" id="txtjob_<?=$i; ?>" value="<?=$job_no; ?>" readonly />
                                    </td>
                                    <td width="70" style="word-break: break-all;word-wrap: break-word;"><a href="#" onClick="setdata('<?=$po_number; ?>');">View</a>
                                        <input type="hidden" id="txtpoid_<?=$i; ?>" value="<?=$po_break_down_id; ?>" readonly />
                                    </td>
                                    <td width="100" style="word-break:break-all"><?=$body_part[$body_part_id]; ?>
                                        <input type="hidden" id="txtprecostfabriccostdtlsid_<?=$i; ?>" value="<?=$bom_fabric_dtls_id; ?>" readonly />
                                        <input type="hidden" id="txtbodypart_<?=$i; ?>" value="<?=$body_part_id; ?>" readonly />
                                    </td>
                                    <td width="80" style="word-break:break-all"><?=$color_type[$color_type_id]; ?>
                                        <input type="hidden" id="txtcolortype_<?=$i; ?>" value="<?=$color_type_id; ?>" readonly />
                                    </td>
                                    <td width="80" style="word-break:break-all"><?=$fabric_typee[$width_dia_type]; ?>
                                        <input type="hidden" id="txtwidthtype_<?=$i; ?>" value="<?=$width_dia_type; ?>" readonly />
                                    </td>
                                    <td width="100" style="word-break:break-all"><?=$construction; ?>
                                        <input type="hidden" id="txtconstruction_<?=$i; ?>" value="<?=$construction; ?>" readonly />
                                    </td>
                                    <td width="150" style="word-break:break-all"><?=$compositi; ?>
                                        <input type="hidden" id="txtcompositi_<?=$i; ?>" value="<?=$compositi; ?>" readonly />
                                    </td>
                                    <td width="60" style="word-break:break-all"><?=$gsm_weight; ?>
                                    	<input type="hidden" id="txtgsmweight_<?=$i; ?>" value="<?=$gsm_weight; ?>" readonly />
                                    </td>
                                    <td width="90" style="word-break:break-all"><?=$color_library[$color_number_id]; ?>
                                    	<input type="hidden" id="txtgmtcolor_<?=$i; ?>" value="<?=$color_number_id; ?>" readonly />
                                    </td>
                                    <td width="110" style="word-break:break-all"><?=$color_library[$item_color]; ?>
                                    	<input type="hidden" id="txtitemcolor_<?=$i; ?>" value="<?=$item_color; ?>" readonly />
                                    </td>
                                    <td width="60" style="word-break:break-all"><?=$dia_width; ?>
                                   		<input type="hidden" id="txtdia_<?=$i; ?>" value="<?=$dia_width; ?>" readonly />
                                    </td>
									<td width="80">
										<input type="text" style="width:80%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right;" id="txthscode_<?=$i; ?>" value="<?=$hscode; ?>" class="text_boxes" />
									</td>
                                    <td width="80" style="word-break:break-all"><?=$pre_cost_remarks; ?>
                                    	<input type="hidden" id="process_<?=$i; ?>" value="<?=$pre_cost_remarks; ?>" readonly />
                                    </td>
                                    <td width="80">	
                                        <input type="text" style="width:80%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right;" id="txtbalqnty_<?=$i; ?>" value="<?=number_format($bal_qty,4,'.',''); ?>" class="text_boxes_numeric" readonly />
                                        <input type="hidden" id="txtreqqnty_<?=$i; ?>" value="<?=number_format($req_qty,4,'.',''); ?>" readonly />
                                        <input type="hidden" id="txtfinreqqnty_<?=$i; ?>" value="<?=number_format($finishreq_qty,4,'.',''); ?>" readonly />
                                        <input type="hidden" id="cuqnty_<?=$i; ?>" value="<?=number_format($cu_booking_qty,4,'.',''); ?>" readonly />
                                        <input type="hidden" id="preconskg_<?=$i; ?>" style="width:20px;" value="" />
                                    </td>
                                    <td width="80">
                                        <input type="text" style="width:80%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:#FFC" id="txtwoq_<?=$i; ?>" value="<?=number_format($bal_qty,4,'.',''); ?>" class="text_boxes_numeric" onChange="claculate_acwoQty(<?=$i; ?>);" />
                                        <input type="hidden" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:#FFC" id="txtwoqprev_<?=$i; ?>" value="<?=number_format($bal_qty,4,'.',''); ?>" readonly />
                                    </td>
                                    <td width="70"><input type="text" style="width:80%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:#FFC" id="txtadj_<?=$i; ?>" value="" class="text_boxes_numeric" onChange="claculate_acwoQty(<?=$i; ?>);" />
                                    </td>
                                    <td width="80"><input type="text"  style="width:80%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:#FFC" id="txtacwoq_<?=$i; ?>" value="<?=number_format($bal_qty,4,'.',''); ?>" class="text_boxes_numeric" readonly /></td>
                                    <td width="50" align="center"><?=$unit_of_measurement[$uom]; ?></td>
                                    <td width="60"><input type="text" style="width:80%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtrate_<?=$i; ?>" value="<?=number_format($rate,4,'.',''); ?>" class="text_boxes_numeric" onChange="claculate_amount(<?=$i; ?>);" data-pre-cost-rate="<?=number_format($rate,4,'.',''); ?>" data-current-rate="<?=number_format($rate,4,'.',''); ?>" /></td>
                                    <td width="100">
                                    	<input type="text" style="width:80%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<?=$bgcolor; ?>" id="txtamount_<?=$i; ?>" value="<?=number_format($bal_amt,4,'.',''); ?>" class="text_boxes_numeric" readonly />
                                    </td>
                                    <td>
                                        <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; background-color:<?=$bgcolor; ?>" id="txtremark_<?=$i;?>" value="" />
                                        <input type="hidden"  id="txtbookingid_<?=$i; ?>" value="" readonly />
                                    </td>
								</tr>
								<?
								$i++;
							//}
						}
					}
				}
			}
        }
        ?>
        </tbody>
	</table>
    <table width="1750" colspan="21" cellspacing="0" class="" border="0">
        <tr>
            <td align="center"class="button_container">
            	<? echo load_submit_buttons( $permission, "fnc_additional_fabric_booking_dtls", 0,0,"reset_form('','booking_list_view','','','')",2); ?>
            </td>
        </tr>
    </table>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<input type='hidden' id='json_data' name="json_data" value='<?=json_encode($job_level_arr); ?>'/>
	<?
	//ISD-22-30488 for Urmi
	 $sql_pre=sql_select("select a.job_id,a.update_by_fabrice from wo_pre_cost_mst a,wo_po_break_down b where a.job_id=b.job_id and  b.id in($txt_order_no_id) and a.update_by_fabrice>0 and a.status_active=1 and b.status_active=1");
		foreach($sql_pre as $row){
			$job_idArr[$row[csf('job_id')]]=$row[csf('job_id')];
		}
		unset($sql_pre);
		$job_ids=implode(",",$job_idArr);
		$con = connect();
		$update_rID1= execute_query( "update wo_pre_cost_mst set update_by_fabrice=0 where job_id in($job_ids) and status_active=1 and is_deleted=0",0);
		oci_commit($con);
	exit();
}

if ($action=="show_trim_booking")
{
	//extract($_REQUEST);
	extract(check_magic_quote_gpc($_REQUEST));
	//$trim_group_library= return_library_array("select id, item_name from lib_item_group",'id','item_name');
	$trms_sql=sql_select("select id,trim_type,item_name from lib_item_group where status_active=1 order by item_name");
	foreach($trms_sql as $row)
	{
		$trim_group_library[$row[csf('id')]]=$row[csf('item_name')];
		$trim_type_arr[$row[csf('id')]]=$row[csf('trim_type')];
	}
	$cbo_pay_mode=str_replace("'","",$cbo_pay_mode);
	$booking_month=0;
	if($cbo_booking_month<10) $booking_month.=$cbo_booking_month;
	else $booking_month=$cbo_booking_month;

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
	
	$cbo_item_from=str_replace("'","",$cbo_item_from);
	
if($cbo_item_from==1)
{
	
	$condition= new condition();

	if(str_replace("'","",$job_no) !=''){
		$condition->job_no("in('$job_no')");
	}
	$condition->init();
	$trims= new trims($condition);
	$req_qty_arr=$trims->getQtyArray_by_orderAndPrecostdtlsid();
	$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsid();
	$reqAmountJobLevelArr=$trims->getAmountArray_by_job();
	
	$powiseCostingPerReqQtyArr=array();
	foreach ($req_amount_arr as $poid=>$podata)
	{
		foreach ($podata as $bomid=>$bomdata)
		{
			$powiseCostingPerReqQtyArr[$bomid][$poid]['amt']+=$bomdata;
		}
	}

	$cu_booking_arr=array();
	$sql_cu_booking=sql_select("select c.job_no,c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id, sum(c.wo_qnty) as cu_wo_qnty, sum(c.amount) as cu_amount from wo_po_details_master a, wo_po_break_down  d , wo_booking_dtls c where a.id=d.job_id and a.job_no=c.job_no and  d.id=c.po_break_down_id and a.company_name=$cbo_company_name and c.pre_cost_fabric_cost_dtls_id=$pre_cost_id and c.status_active=1 and c.is_deleted=0 group by c.job_no, c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id");
	foreach($sql_cu_booking as $row_cu_booking)
	{
		$cu_booking_arr[$row_cu_booking[csf('job_no')]][$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]]['cu_woq'][$row_cu_booking[csf('po_break_down_id')]]=$row_cu_booking[csf('cu_wo_qnty')];
		$cu_booking_arr[$row_cu_booking[csf('job_no')]][$row_cu_booking[csf('pre_cost_fabric_cost_dtls_id')]]['cu_amount'][$row_cu_booking[csf('po_break_down_id')]]=$row_cu_booking[csf('cu_amount')];
	}
	unset($sql_cu_booking);

	$sql="SELECT a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, f.exchange_rate, c.id as wo_pre_cost_trim_cost_dtls, c.trim_group, c.description as description_pre_cost, c.brand_sup_ref as brand_sup_ref_precost, c.country, c.rate, c.amount, d.id as po_id, d.po_number, d.po_quantity as plan_cut, min(e.id) as id, e.po_break_down_id, avg(e.cons) as cons, sum(f.wo_qnty) as cu_woq, sum(f.amount) as cu_amount, f.id as booking_id, f.sensitivity, f.delivery_date, f.description as description, f.brand_supplier as brand_sup_ref, f.hs_code,f.remark

	from wo_po_details_master a, wo_pre_cost_mst b, wo_pre_cost_trim_cost_dtls c, wo_po_break_down d, wo_pre_cost_trim_co_cons_dtls e, wo_booking_dtls f

	where
	a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.job_no=f.job_no and c.id=e.wo_pre_cost_trim_cost_dtls_id and d.id=e.po_break_down_id and e.wo_pre_cost_trim_cost_dtls_id= f.pre_cost_fabric_cost_dtls_id and e.po_break_down_id=f.po_break_down_id and f.booking_type=8 and f.booking_no=$txt_booking_no and f.id in($booking_id) and a.company_name=$cbo_company_name $garment_nature_cond and e.wo_pre_cost_trim_cost_dtls_id=$pre_cost_id and d.is_deleted=0 and d.status_active=1 and f.status_active=1 and f.is_deleted=0

	group by
	a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, f.exchange_rate, c.id, c.trim_group, c.description, c.brand_sup_ref, c.country, c.rate, c.amount, d.id, d.po_number, d.po_quantity, e.po_break_down_id, f.id, f.sensitivity, f.delivery_date, f.description, f.brand_supplier, f.remark,f.hs_code
	order by d.id,c.id";
	//echo $sql;
	$job_and_trimgroup_level=array();
	$i=1;
	$nameArray=sql_select( $sql );
	$poid="";
	foreach ($nameArray as $infr)
	{
		if($poid=="") $poid=$infr[csf('po_id')]; else $poid.=','.$infr[csf('po_id')];
	}
	$delivery_date='';

	if($cbo_level==2)
	{
		foreach ($nameArray as $infr)
		{
			$cbo_currency_job=$infr[csf('currency_id')];
			$exchange_rate=$infr[csf('exchange_rate')];
			if($cbo_currency==$cbo_currency_job) $exchange_rate=1;
			$job_no=$infr[csf('job_no')];
			$pre_cost_trim_id=$infr[csf('wo_pre_cost_trim_cost_dtls')];

			if(!empty($delivery_date))
			{
				$delivery_date=min($delivery_date,$infr[csf('delivery_date')]);
			}
			else
			{
				$delivery_date=$infr[csf('delivery_date')];
			}

			$req_qnty_cons_uom=$req_qty_arr[$infr[csf('po_id')]][$pre_cost_trim_id];
			$req_amount_cons_uom=$req_amount_arr[$infr[csf('po_id')]][$pre_cost_trim_id];
			$rate_cons_uom=$req_amount_cons_uom/$req_qnty_cons_uom;
			$req_qnty_ord_uom=def_number_format($req_qnty_cons_uom/$sql_lib_item_group_array[$infr[csf('trim_group')]][conversion_factor],3,"");
			$rate_ord_uom=def_number_format(($rate_cons_uom*$sql_lib_item_group_array[$infr[csf('trim_group')]][conversion_factor])*$exchange_rate,3,"");
			$req_amount_ord_uom=def_number_format($req_qnty_ord_uom*$rate_ord_uom,3,"");

			$cu_woq=$cu_booking_arr[$job_no][$pre_cost_trim_id]['cu_woq'][$infr[csf('po_id')]];
			$cu_amount=$cu_booking_arr[$job_no][$pre_cost_trim_id]['cu_amount'][$infr[csf('po_id')]];
			$bal_woq=def_number_format($req_qnty_ord_uom-$cu_woq,3,"");
			$amount=def_number_format($rate_ord_uom*$bal_woq,3,"");
			$total_req_amount+=$req_amount;
			$total_cu_amount+=$infr[csf('cu_amount')];
			
			$costing_per=0;
			if($infr[csf('costing_per')]==1) $costing_per=12;
			else if($infr[csf('costing_per')]==2) $costing_per=1;
			else if($infr[csf('costing_per')]==3) $costing_per=24;
			else if($infr[csf('costing_per')]==4) $costing_per=36;
			else if($infr[csf('costing_per')]==5) $costing_per=48;
			else $costing_per=0;
			
			$itempoWiseReqQty=0;
		
			$itempoWiseReqQty=($powiseCostingPerReqQtyArr[$pre_cost_trim_id][$infr[csf('po_id')]]['amt']/$infr[csf('plan_cut')])*$costing_per;
			//if($infr[csf('po_id')]==58499) echo $powiseCostingPerReqQtyArr[$pre_cost_trim_id][$infr[csf('po_id')]]['amt'].'='.$infr[csf('plan_cut')].'='.$costing_per.'<br>';

			$job_and_trimgroup_level[$job_no][$pre_cost_trim_id]['job_no'][$infr[csf('po_id')]]=$job_no;
			$job_and_trimgroup_level[$job_no][$pre_cost_trim_id]['po_id'][$infr[csf('po_id')]]=$infr[csf('po_id')];
			$job_and_trimgroup_level[$job_no][$pre_cost_trim_id]['style_ref_no'][$infr[csf('po_id')]]=$infr[csf('style_ref_no')];
			$job_and_trimgroup_level[$job_no][$pre_cost_trim_id]['company_name'][$infr[csf('company_name')]]=$infr[csf('company_name')];
			$job_and_trimgroup_level[$job_no][$pre_cost_trim_id]['po_number'][$infr[csf('po_id')]]=$infr[csf('po_number')];
			$job_and_trimgroup_level[$job_no][$pre_cost_trim_id]['country'][$infr[csf('po_id')]]=$infr[csf('country')];
			$job_and_trimgroup_level[$job_no][$pre_cost_trim_id]['description'][$infr[csf('po_id')]]=$infr[csf('description')];
			$job_and_trimgroup_level[$job_no][$pre_cost_trim_id]['hs_code'][$infr[csf('po_id')]]=$infr[csf('hs_code')];
			$job_and_trimgroup_level[$job_no][$pre_cost_trim_id]['remark'][$infr[csf('po_id')]]=$infr[csf('remark')];
			$job_and_trimgroup_level[$job_no][$pre_cost_trim_id]['brand_sup_ref'][$infr[csf('po_id')]]=$infr[csf('brand_sup_ref')];
			$job_and_trimgroup_level[$job_no][$pre_cost_trim_id]['trim_group'][$infr[csf('po_id')]]=$infr[csf('trim_group')];
			$job_and_trimgroup_level[$job_no][$pre_cost_trim_id]['trim_group_name'][$infr[csf('po_id')]]=$trim_group_library[$infr[csf('trim_group')]];
			$job_and_trimgroup_level[$job_no][$pre_cost_trim_id]['wo_pre_cost_trim_cost_dtls'][$infr[csf('po_id')]]=$pre_cost_trim_id;

			$job_and_trimgroup_level[$job_no][$pre_cost_trim_id]['req_qnty'][$infr[csf('po_id')]]=$req_qnty_ord_uom;
			$job_and_trimgroup_level[$job_no][$pre_cost_trim_id]['uom'][$infr[csf('po_id')]]=$sql_lib_item_group_array[$infr[csf('trim_group')]][cons_uom];
			$job_and_trimgroup_level[$job_no][$pre_cost_trim_id]['uom_name'][$infr[csf('po_id')]]=$unit_of_measurement[$sql_lib_item_group_array[$infr[csf('trim_group')]][cons_uom]];
			$job_and_trimgroup_level[$job_no][$pre_cost_trim_id]['req_amount'][$infr[csf('po_id')]]=$req_amount_ord_uom;
			$job_and_trimgroup_level[$job_no][$pre_cost_trim_id]['req_amount_cons_uom'][$infr[csf('po_id')]]=$req_amount_cons_uom;

			$job_and_trimgroup_level[$job_no][$pre_cost_trim_id]['cu_woq'][$infr[csf('po_id')]]=$cu_woq;
			$job_and_trimgroup_level[$job_no][$pre_cost_trim_id]['cu_amount'][$infr[csf('po_id')]]=$cu_amount;
			$job_and_trimgroup_level[$job_no][$pre_cost_trim_id]['bal_woq'][$infr[csf('po_id')]]=$bal_woq;
			$job_and_trimgroup_level[$job_no][$pre_cost_trim_id]['exchange_rate'][$infr[csf('po_id')]]=$exchange_rate;
			$job_and_trimgroup_level[$job_no][$pre_cost_trim_id]['rate'][$infr[csf('po_id')]]=$rate_ord_uom;
			$job_and_trimgroup_level[$job_no][$pre_cost_trim_id]['amount'][$infr[csf('po_id')]]=$amount;
			$job_and_trimgroup_level[$job_no][$pre_cost_trim_id]['pre_req_amt'][$infr[csf('po_id')]]=$infr[csf('amount')];
			$job_and_trimgroup_level[$job_no][$pre_cost_trim_id]['txt_delivery_date'][$infr[csf('po_id')]]=$infr[csf('delivery_date')];
			$job_and_trimgroup_level[$job_no][$pre_cost_trim_id]['booking_id'][$infr[csf('po_id')]]=$infr[csf('booking_id')];
			$job_and_trimgroup_level[$job_no][$pre_cost_trim_id]['sensitivity'][$infr[csf('po_id')]]=$infr[csf('sensitivity')];
			$job_and_trimgroup_level[$job_no][$pre_cost_trim_id]['reqqtycostingper'][$infr[csf('po_id')]]=def_number_format($itempoWiseReqQty,5,"");
		}
	}
	//print_r($job_and_trimgroup_level['OG-22-00128'][54087]['reqqtycostingper']);

	$sql_booking=sql_select("select c.job_no, c.pre_cost_fabric_cost_dtls_id, c.po_break_down_id, sum(c.wo_qnty) as wo_qnty, sum(c.amount) as amount from wo_po_details_master a, wo_po_break_down d, wo_booking_dtls c where a.id=d.job_id and a.job_no=c.job_no and  d.id=c.po_break_down_id and c.booking_no=$txt_booking_no and c.pre_cost_fabric_cost_dtls_id=$pre_cost_id  and c.id in($booking_id) and c.booking_type=8 and c.status_active=1 and c.is_deleted=0 group by c.job_no, c.pre_cost_fabric_cost_dtls_id, c.po_break_down_id");
	foreach($sql_booking as $row_booking)
	{
		$job_and_trimgroup_level[$row_booking[csf('job_no')]][$row_booking[csf('pre_cost_fabric_cost_dtls_id')]]['woq'][$row_booking[csf('po_break_down_id')]]=$row_booking[csf('wo_qnty')];
		$job_and_trimgroup_level[$row_booking[csf('job_no')]][$row_booking[csf('pre_cost_fabric_cost_dtls_id')]]['amount'][$row_booking[csf('po_break_down_id')]]=$row_booking[csf('amount')];
	}
	unset($sql_booking);
	?>
    <input type="hidden" id="strdata" value='<?=json_encode($job_and_trimgroup_level); ?>' style="background-color:#CCC"/>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1430" class="rpt_table" >
        <thead>
            <th width="40">SL</th>
            <th width="80">Job No</th>
            <th width="80">Style Ref</th>
            <th width="100">Ord. No</th>
            <th width="100">Trims Group</th>
            <th width="100">HS Code</th>
            <th width="150">Description</th>
            <th width="150">Brand Sup.</th>
            
            <th width="50">UOM</th>
            
            <th width="100">Sensitivity</th>
            <th width="80">WOQ</th>
            <th width="55">Exch.Rate</th>
            <th width="80">Rate</th>
            <th width="80">Amount</th>
            <th width="80">Delv. Date</th>
            <th>Remark</th>
        </thead>
	</table>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1430" class="rpt_table" id="tbl_list_search" >
        <tbody>
        <?
		$pi_array=array();
		$pi_number=sql_select( "Select a.pi_number, b.item_group from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and b.work_order_no=$txt_booking_no and a.item_category_id=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach ($pi_number as $row)
		{
			$pi_array[$row[csf('item_group')]]=$row[csf('pi_number')];
		}
		unset($pi_number);
		$recv_array=array();
		$recv_number=sql_select( "Select a.recv_number, b.item_group_id from inv_receive_master a, inv_trims_entry_dtls b where a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0");
		foreach ($recv_number as $row)
		{
			$recv_array[$row[csf('item_group_id')]]=$row[csf('recv_number')];
		}
		unset($recv_number);

        if($cbo_level==1)
        {
            foreach ($nameArray as $selectResult)
            {
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

                $cbo_currency_job=$selectResult[csf('currency_id')];
                $exchange_rate=$selectResult[csf('exchange_rate')];
                if($cbo_currency==$cbo_currency_job) $exchange_rate=1;
				$po_id=$selectResult[csf('po_id')];

                $req_qnty_cons_uom=$req_qty_arr[$selectResult[csf('po_id')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]];
                $req_amount_cons_uom=$req_amount_arr[$selectResult[csf('po_id')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]];
                $rate_cons_uom=$req_amount_cons_uom/$req_qnty_cons_uom;

                $req_qnty_ord_uom=def_number_format($req_qnty_cons_uom/$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor],3,"");
                $rate_ord_uom=def_number_format(($rate_cons_uom*$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor])*$exchange_rate,3,"");
                $req_amount_ord_uom=def_number_format($req_qnty_ord_uom*$rate_ord_uom,3,"");

                $cu_woq=$cu_booking_arr[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['cu_woq'][$selectResult[csf('po_id')]];
                $cu_amount=$cu_booking_arr[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['cu_amount'][$selectResult[csf('po_id')]];
                $bal_woq=def_number_format($req_qnty_ord_uom,3,"");

                $woq=$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['woq'][$selectResult[csf('po_id')]];
                $amount=$job_and_trimgroup_level[$selectResult[csf('job_no')]][$selectResult[csf('wo_pre_cost_trim_cost_dtls')]]['amount'][$selectResult[csf('po_id')]];
                $rate=$amount/$woq;
                $total_amount+=$amount;
                $reqAmtJobLevelConsUom=$reqAmountJobLevelArr[$selectResult[csf('job_no')]];

				$piNumber=0;
				$pi_number=$pi_array[$trim_group];
				if($pi_number!="") $piNumber=1;

				$recvNumber=0;
				$recv_number=$recv_array[$trim_group];
				if($recv_number) $recvNumber=1;

				$disAbled=0;
				if($recvNumber==1 || $piNumber==1) $disAbled=1; else $disAbled=0;
				if($cbo_company_name<0) $cbo_company_name=0;
					$tna_integrated=return_field_value("tna_integrated","variable_order_tracking","company_name='$cbo_company_name' and variable_list=14 and status_active=1 and is_deleted=0","tna_integrated");
					//echo $cbo_company_name.'X';
					if($tna_integrated==1)
					{
						//echo " $('#txt_delevary_date').attr('disabled',true);\n";
						$deli_date_con="disabled=disabled";
					}
					else
					{
						//echo "$('#txt_delevary_date').attr('disabled',false);\n";
						$deli_date_con="";
					}
					/*$trim_type=$trim_type_arr[$selectResult[csf('trim_group')]];
					//echo $trim_type.'ddd';
					if($trim_type==1) $task_num='70'; else $task_num='71';
					$task_finish_date='';
					$tnasql=sql_select("select po_number_id,task_finish_date from tna_process_mst where task_number=$task_num and po_number_id in($po_id) and is_deleted= 0 and status_active=1");

					foreach($tnasql as $tnarow){
					$task_finish_date_arr[$tnarow[csf('po_number_id')]]=$tnarow[csf('task_finish_date')];
					}
					$task_finish_date=$task_finish_date_arr[$selectResult[csf('po_id')]];
					$sql_tna_lib=sql_select("select b.date_calc, b.day_status from lib_capacity_calc_mst a, lib_capacity_calc_dtls b where a.id=b.mst_id and a.comapny_id=$cbo_company_name and b.date_calc='$task_finish_date' and a.status_active=1 and a.is_deleted=0");

					$date_calc=$sql_tna_lib[0][csf("date_calc")];
					$day_status=$sql_tna_lib[0][csf("day_status")];

					if($day_status==2)
					{
					$task_finish_date=return_field_value("max(b.date_calc) as  date_calc ", " lib_capacity_calc_mst a, lib_capacity_calc_dtls b "," a.id=b.mst_id and a.comapny_id=$cbo_company_name and b.date_calc<'$task_finish_date' and a.status_active=1 and a.is_deleted=0 and b.day_status=1","date_calc");
					}
					else
					{
					$task_finish_date=$task_finish_date;
					}

					//$delivery_date="";
					if($task_finish_date !='')
					{
						$txt_tna_date=change_date_format($task_finish_date,'dd-mm-yyyy','-');
					}
					else
					{
						$txt_tna_date="";
					}*/
					
					$costing_per=0;
				if($selectResult[csf('costing_per')]==1) $costing_per=12;
				else if($selectResult[csf('costing_per')]==2) $costing_per=1;
				else if($selectResult[csf('costing_per')]==3) $costing_per=24;
				else if($selectResult[csf('costing_per')]==4) $costing_per=36;
				else if($selectResult[csf('costing_per')]==5) $costing_per=48;
				else $costing_per=0;
				
				$itempoWiseReqQty=0;
			
				$itempoWiseReqQty=($powiseCostingPerReqQtyArr[$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('po_id')]]['amt']/$selectResult[csf('plan_cut')])*$costing_per;

                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
                    <td width="40"><? echo $i;?></td>
                    <td width="80"><? echo $selectResult[csf('job_no')];?>
                        <input type="hidden" id="txtjob_<? echo $i;?>" value="<? echo $selectResult[csf('job_no')];?>" style="width:30px" class="text_boxes" readonly/>
                    </td>
                      <td width="80"> <p><? echo $selectResult[csf('style_ref_no')];?> </p>
                         
                    </td>
                    <td width="100" style="word-break:break-all"><? echo $selectResult[csf('po_number')];?>
                        <input type="hidden" id="txtbookingid_<? echo $i;?>" value="<? echo $selectResult[csf('booking_id')];?>" readonly/>
                        <input type="hidden" id="txtpoid_<? echo $i;?>" value="<? echo $selectResult[csf('po_id')];?>" readonly/>
                        <input type="hidden" id="txtcountry_<? echo $i;?>"  value="<? echo $selectResult[csf('country')] ?>" readonly />
                    </td>
                    <td width="100"  title="<? echo $sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor];  ?>">
                        <? echo $trim_group_library[$selectResult[csf('trim_group')]];?>
                        <input type="hidden" id="txttrimcostid_<? echo $i;?>" value="<? echo $selectResult[csf('wo_pre_cost_trim_cost_dtls')];?>" readonly/>
                        <input type="hidden" id="txttrimgroup_<? echo $i;?>" value="<? echo $selectResult[csf('trim_group')];?>" readonly/>
                        <input class="text_boxes" name="txtReqAmt_<?=$i;?>" id="txtReqAmt_<?=$i;?>" type="hidden" value="<?=$itempoWiseReqQty; ?>" style="width:30px"/>
						<input id="hiddlabeldtlsdata_<?=$i;?>" type="hidden" value=""/>
                    </td>
					<td width="100">
                        <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<? echo $bgcolor; ?>" id="txthscode_<? echo $i;?>"  value="<? echo $selectResult[csf('hs_code')];?>"  <? if($disAbled){echo "disabled";}else{ echo "";}?> />
                    </td>
                    <td width="150">
                        <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<? echo $bgcolor; ?>" id="txtdesc_<? echo $i;?>"  value="<? echo $selectResult[csf('description')];?>"  <? if($disAbled){echo "disabled";}else{ echo "";}?> />
                    </td>
                    <td width="150">
                        <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<? echo $bgcolor; ?>" id="txtbrandsup_<? echo $i;?>"  value="<? echo $selectResult[csf('brand_sup_ref')];?>"  <? if($disAbled){echo "disabled";}else{ echo "";}?> />
                    </td>
                    <td width="50" align="right">
                        <input type="hidden"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqqnty_<? echo $i;?>" value="<? echo number_format($req_qnty_ord_uom,4,'.','');?>"  readonly  />
                        <input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamount_<? echo $i;?>" value="<? echo number_format($req_amount_ord_uom,4,'.','');?>"  readonly  />
                        <input type="hidden"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamountjoblevelconsuom_<? echo $i;?>" value="<? echo number_format($reqAmtJobLevelConsUom,4,'.','');?>"  readonly  />
                        <input type="hidden"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamountitemlevelconsuom_<? echo $i;?>" value="<? echo number_format($req_amount_cons_uom,4,'.','');?>"  readonly  />
                    
                        <? echo $unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom]];?>
                        <input type="hidden" id="txtuom_<? echo $i;?>" value="<? echo $sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom];?>" readonly />
                    </td>
                    <td width="100" align="right">
                        <input type="hidden"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuwoq_<? echo $i;?>" value="<? echo number_format($cu_woq,4,'.','');?>"  readonly  />
                        <input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuamount_<? echo $i;?>" value="<? echo number_format($cu_amount,4,'.','');?>"  readonly  />
                     
                        <input type="hidden"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtbalwoq_<? echo $i;?>" value="<? echo number_format($bal_woq,4,'.',''); ?>"  readonly  />
                    <? echo create_drop_down( "cbocolorsizesensitive_".$i, 100, $size_color_sensitive,"", 1, "--Select--", $selectResult[csf("sensitivity")], "set_cons_break_down($i),copy_value(this.value,'cbocolorsizesensitive_',$i)",$disAbled,"1,2,3,4" ); ?>
                    </td>
                    <td width="80" align="right">
                        <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoq_<? echo $i;?>" value="<? echo number_format($woq,4,'.','');?>" onClick="open_consumption_popup('requires/multi_job_additional_fabric_booking_controller.php?action=consumption_popup', '<?=$trim_group_library[$selectResult[csf('trim_group')]];?>','txtpoid_<? echo $i;?>',<? echo $i;?>)" readonly />
                    </td>
                    <td width="55" align="right">
                        <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtexchrate_<? echo $i;?>" value="<? echo $exchange_rate;?>" readonly />
                    </td>
                    <td width="80" align="right">
                        <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_<? echo $i;?>" value="<? echo $rate;?>" onChange="calculate_amount(<? echo $i; ?>)" readonly />

                        <input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_precost_<? echo $i;?>" value="<? echo $rate_ord_uom;?>" readonly />
                    </td>
                    <td width="80" align="right">
                        <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtamount_<? echo $i;?>" value="<? echo number_format($amount,4,'.','');?>"  readonly  />
                    </td>
                    <td width="80" align="center">
                        <input type="text"   style="width:90%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:center; background-color:<? echo $bgcolor; ?>" id="txtddate_<? echo $i;?>"  class="datepicker" value="<? echo change_date_format($selectResult[csf('delivery_date')],"dd-mm-yyyy","-"); ?>"  onChange="compare_date(2)"  readonly <? if($disAbled){echo "disabled";}else{ echo "";}?> <? echo $deli_date_con;?>   />
                         <input name="txttnadate_<? echo $i;?>" id="txttnadate_<? echo $i;?>" class="datepicker" type="hidden" value="<? echo $txt_tna_date;?>" style="width:70px;"  readonly/>
                        <input type="hidden" id="consbreckdown_<? echo $i;?>"  value=""/>
                        <input type="hidden" id="jsondata_<? echo $i;?>"  value=""/>
                    </td>
                    <td>
                   	 <input class="text_boxes" type="text" style="width:75px;"  name="txtremark_<? echo $i;?>" id="txtremark_<? echo $i;?>"  value="<? echo $selectResult[csf('remark')];?>"/>
                    </td>
                </tr>
            <?
            $i++;
            }
        }
        else if($cbo_level==2)
        {
            $i=1;
            foreach ($job_and_trimgroup_level as $job_no)
            {
                foreach ($job_no as $wo_pre_cost_trim_cost_dtls)
                {
                    $job_no=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['job_no']));
					$style_ref_no=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['style_ref_no']));
					$remark=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['remark']));
                    $po_number=implode(", ",$wo_pre_cost_trim_cost_dtls['po_number']);
                    $po_id=implode(",",$wo_pre_cost_trim_cost_dtls['po_id']);
					$company_name=implode(",",$wo_pre_cost_trim_cost_dtls['company_name']);
                    $country=implode(",",array_unique(explode(",",implode(",",$wo_pre_cost_trim_cost_dtls['country']))));
                    $description=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['description']));
                    $hs_code=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['hs_code']));
                    $brand_sup_ref=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['brand_sup_ref']));
                    $wo_pre_cost_trim_id=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['wo_pre_cost_trim_cost_dtls']));
					$wo_pre_req_amt=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['pre_req_amt']));

                    $trim_group = implode(",",array_unique($wo_pre_cost_trim_cost_dtls['trim_group']));
                    $uom=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['uom']));
                    $booking_id=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['booking_id']));
                    $sensitivity=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['sensitivity']));
                    //$delivery_date=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['txt_delivery_date']));

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
					$pi_number=$pi_array[$trim_group];
					if($pi_number!="") $piNumber=1;

					$recvNumber=0;
					$recv_number=$recv_array[$trim_group];
					if($recv_number) $recvNumber=1;

					$disAbled=0;
					if($recvNumber==1 || $piNumber==1) $disAbled=1; else $disAbled=0;
					if($cbo_company_name<0) $cbo_company_name=0;
					$tna_integrated=return_field_value("tna_integrated","variable_order_tracking","company_name='$cbo_company_name' and variable_list=14 and status_active=1 and is_deleted=0","tna_integrated");
					//echo $cbo_company_name.'X';
					if($tna_integrated==1)
					{
						//echo " $('#txt_delevary_date').attr('disabled',true);\n";
						$deli_date_con="disabled=disabled";
					}
					else
					{
						//echo "$('#txt_delevary_date').attr('disabled',false);\n";
						$deli_date_con="";
					}
					/*$trim_type=$trim_type_arr[$trim_group];
					//echo $trim_type.'ddd';
					if($trim_type==1) $task_num='70'; else $task_num='71';
					$task_finish_date='';
					//$tnasql=sql_select("select po_number_id,task_finish_date from tna_process_mst where task_number=$task_num and po_number_id in($po_id) and is_deleted= 0 and status_active=1");
					$tnasql=sql_select("select min(po_number_id) as po_number_id,min(task_finish_date) as  task_finish_date from tna_process_mst where task_number=$task_num and po_number_id in($po_id) and is_deleted= 0 and status_active=1 order by task_finish_date ");
					//echo "select min(po_number_id) as po_number_id,min(task_finish_date) as  task_finish_date from tna_process_mst where task_number=$task_num and po_number_id in($po_id) and is_deleted= 0 and status_active=1 order by task_finish_date ";

					foreach($tnasql as $tnarow){
					//$task_finish_date_arr[$tnarow[csf('po_number_id')]]=$tnarow[csf('task_finish_date')];
					$task_finish_date=$tnarow[csf('task_finish_date')];
					}
					//$task_finish_date=$task_finish_date_arr[$po_id];
					$sql_tna_lib=sql_select("select b.date_calc, b.day_status from lib_capacity_calc_mst a, lib_capacity_calc_dtls b where a.id=b.mst_id and a.comapny_id=$cbo_company_name and b.date_calc='$task_finish_date' and a.status_active=1 and a.is_deleted=0");

					$date_calc=$sql_tna_lib[0][csf("date_calc")];
					$day_status=$sql_tna_lib[0][csf("day_status")];

					if($day_status==2)
					{
					$task_finish_date=return_field_value("max(b.date_calc) as  date_calc ", " lib_capacity_calc_mst a, lib_capacity_calc_dtls b "," a.id=b.mst_id and a.comapny_id=$cbo_company_name and b.date_calc<'$task_finish_date' and a.status_active=1 and a.is_deleted=0 and b.day_status=1","date_calc");
					}
					else
					{
					$task_finish_date=$task_finish_date;
					}

					//$delivery_date="";
					if($task_finish_date !='')
					{
						$txt_tna_date=change_date_format($task_finish_date,'dd-mm-yyyy','-');
					}
					else
					{
						$txt_tna_date="";
					}*/
					//echo $disAbled.'=='.$deli_date_con.'='.$txt_tna_date;
					$labeldtlsdata=return_field_value("labeldtlsdata", "wo_booking_dtls", "id=$booking_id");
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
                        <td width="40"><? echo $i;?></td>
                        <td width="80"><? echo $job_no?><input type="hidden" id="txtjob_<? echo $i;?>" value="<? echo $job_no;?>" style="width:30px" class="text_boxes" readonly/></td>
                        <td width="80"> <p><? echo $style_ref_no;?></p></td>
                        
                        <td width="100" style="word-break:break-all"><? echo $po_number; ?>
                            <input type="hidden" id="txtbookingid_<? echo $i;?>" value="<? echo $booking_id; ?>" readonly/>
                            <input type="hidden" id="txtpoid_<? echo $i;?>" value="<? echo $po_id; ?>" readonly/>
                            <input type="hidden" id="txtcountry_<? echo $i;?>"  value="<? echo $country; ?>" readonly />
                        </td>
                        <td width="100"  title="<? echo $sql_lib_item_group_array[$trim_group][conversion_factor]; ?>">
						<a href="javascript:void(0)" onClick="openlabeldtls_popup('<?=$booking_id."_".$trim_group."_".$i; ?>',<?=$i; ?>);"><? echo $trim_group_library[$trim_group];?></a>
                            <input type="hidden" id="txttrimcostid_<? echo $i;?>" value="<? echo $wo_pre_cost_trim_id;?>" readonly/>
                            <input type="hidden" id="txttrimgroup_<? echo $i;?>" value="<? echo $trim_group;?>" readonly/>
                            <input class="text_boxes" name="txtReqAmt_<?=$i;?>" id="txtReqAmt_<?=$i;?>" type="hidden" value="<? //=$wo_pre_req_amt; ?>" style="width:30px"/>
							<input id="hiddlabeldtlsdata_<?=$i;?>" type="hidden" value="<?=$labeldtlsdata;?>"/>
                        </td>
                        <td width="100">
                            <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<? echo $bgcolor; ?>" id="txthscode_<? echo $i;?>"  value="<? echo $hs_code; ?>" <? if($disAbled){echo "disabled";}else{ echo "";}?> />
                        </td>
                        <td width="150">
                            <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<? echo $bgcolor; ?>" id="txtdesc_<? echo $i;?>"  value="<? echo $description; ?>" <? if($disAbled){echo "disabled";}else{ echo "";}?> />
                        </td>
                        <td width="150">
                            <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<? echo $bgcolor; ?>" id="txtbrandsup_<? echo $i;?>"  value="<? echo $brand_sup_ref;?>" <? if($disAbled){echo "disabled";}else{ echo "";}?>  />
                        </td>
                        <td width="50" align="right">
                            <input type="hidden"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqqnty_<? echo $i;?>" value="<? echo number_format($req_qnty_ord_uom,4,'.','');?>"  readonly  />
                            <input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamount_<? echo $i;?>" value="<? echo number_format($req_amount_ord_uom,4,'.','');?>"  readonly  />
                            <input type="hidden"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamountjoblevelconsuom_<? echo $i;?>" value="<? echo number_format($reqAmtJobLevelConsUom,4,'.','');?>"  readonly  />
                            <input type="hidden"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamountitemlevelconsuom_<? echo $i;?>" value="<? echo number_format($req_amount_cons_uom,4,'.','');?>"  readonly  />
                        <?  echo $unit_of_measurement[$uom];?><input type="hidden" id="txtuom_<? echo $i;?>" value="<? echo $uom;?>" readonly /></td>
                        <td width="100" align="right">
                            <input type="hidden"  style="width:100%; height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuwoq_<? echo $i;?>" value="<? echo number_format($cu_woq,4,'.',''); ?>"  readonly  />
                            <input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuamount_<? echo $i;?>" value="<? echo $cu_amount;?>"  readonly  />
                         
                            <input type="hidden"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtbalwoq_<? echo $i;?>" value="<? echo number_format($bal_woq,4,'.','');?>"  readonly  />
                         <?  echo create_drop_down( "cbocolorsizesensitive_".$i, 100, $size_color_sensitive,"", 1, "--Select--", $sensitivity, "set_cons_break_down($i),copy_value(this.value,'cbocolorsizesensitive_',$i)",$disAbled,"1,2,3,4" ); ?>
                        </td>
                        <td width="80" align="right">
                            <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoq_<? echo $i;?>" value="<? echo number_format($woq,4,'.','');?>" onClick="open_consumption_popup('requires/multi_job_additional_fabric_booking_controller.php?action=consumption_popup', '<?=$trim_group_library[$trim_group];?>','txtpoid_<? echo $i;?>',<? echo $i;?>)" readonly />
                        </td>
                        <td width="55" align="right">
                            <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtexchrate_<? echo $i;?>" value="<? echo $exchange_rate;?>" readonly />
                        </td>
                        <td width="80" align="right">
                            <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_<? echo $i;?>" value="<? echo $rate;?>" onChange="calculate_amount(<? echo $i; ?>)" readonly />
                            <input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_precost_<? echo $i;?>" value="<? echo $rate_ord_uom;?>" readonly />
                        </td>
                        <td width="80" align="right">
                            <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtamount_<? echo $i;?>" value="<? echo number_format($amount,4,'.','');?>"  readonly  />
                        </td>
                        <td width="" align="right">
                            <input type="text"   style="width:90%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtddate_<? echo $i;?>"  class="datepicker" onChange="compare_date(2)" value="<? echo change_date_format($delivery_date,"dd-mm-yyyy","-"); ?>"  readonly <? if($disAbled){echo "disabled";}else{ echo "";}?>  <? echo $deli_date_con;?>   />
                            <input name="txttnadate_<? echo $i;?>" id="txttnadate_<? echo $i;?>" class="datepicker" type="hidden" value="<? echo $txt_tna_date;?>" style="width:70px;"  readonly/>
                            <input type="hidden" id="consbreckdown_<? echo $i;?>"  value=""/>
                            <input type="hidden" id="jsondata_<? echo $i;?>"  value=""/>
                        </td>
                         <td>
                   		 <input class="text_boxes" type="text" style="width:75px;"  name="txtremark_<? echo $i;?>" id="txtremark_<? echo $i;?>" value="<? echo $remark;?>"/>
                    </td>
                    </tr>
                    <?
                    $i++;
                }
            }
        }
	} //===========Item From Library end=============
else
{
		$cu_booking_arr=array();
	$sql_cu_booking=sql_select("select a.id as job_id,c.job_no,c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id, sum(c.wo_qnty) as cu_wo_qnty, sum(c.amount) as cu_amount from wo_po_details_master a, wo_po_break_down  d , wo_booking_dtls c where a.id=d.job_id and a.job_no=c.job_no and  d.id=c.po_break_down_id and a.company_name=$cbo_company_name and c.po_break_down_id in($data) and c.entry_form_id=555  and c.status_active=1 and c.is_deleted=0 group by a.id,c.job_no, c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id");
	 
	foreach($sql_cu_booking as $row_cu_booking)
	{
		$cu_booking_arr[$row_cu_booking[csf('job_no')]][$row_cu_booking[csf('job_id')]]['cu_woq'][$row_cu_booking[csf('po_break_down_id')]]=$row_cu_booking[csf('cu_wo_qnty')];
		$cu_booking_arr[$row_cu_booking[csf('job_no')]][$row_cu_booking[csf('job_id')]]['cu_amount'][$row_cu_booking[csf('po_break_down_id')]]=$row_cu_booking[csf('cu_amount')];
	}
	unset($sql_cu_booking);

	$sql="SELECT a.id as job_id,a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, f.exchange_rate, f.trim_group, f.description as description_pre_cost, avg(c.order_rate) as rate, sum(c.order_total) as amount,sum(c.order_quantity) as order_quantity, d.id as po_id, d.po_number, d.po_quantity as plan_cut,  sum(f.wo_qnty) as cu_woq, sum(f.amount) as cu_amount, f.id as booking_id, f.sensitivity, f.delivery_date, f.description as description, f.brand_supplier as brand_sup_ref, f.hs_code,f.remark

	from wo_po_details_master a, wo_pre_cost_mst b, wo_po_color_size_breakdown c, wo_po_break_down d, wo_booking_dtls f

	where
	a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.job_no=f.job_no and d.id=c.po_break_down_id and d.id=f.po_break_down_id and c.po_break_down_id=f.po_break_down_id and f.booking_type=8 and f.booking_no=$txt_booking_no and f.id in($booking_id) and a.company_name=$cbo_company_name $garment_nature_cond  and d.is_deleted=0 and d.status_active=1 and f.status_active=1 and f.is_deleted=0

	group by
	a.id,a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.currency_id, a.style_ref_no, b.costing_per, f.exchange_rate, f.trim_group, f.description, f.brand_supplier,  d.id, d.po_number, d.po_quantity,f.id, f.sensitivity, f.delivery_date, f.description, f.brand_supplier, f.remark,f.hs_code
	order by d.id";
//	echo $sql;
	$job_and_trimgroup_level=array();
	$i=1;
	$nameArray=sql_select( $sql );
	$poid="";
	foreach ($nameArray as $infr)
	{
		if($poid=="") $poid=$infr[csf('po_id')]; else $poid.=','.$infr[csf('po_id')];
	}
	$delivery_date='';

	if($cbo_level==2)
	{
		foreach ($nameArray as $infr)
		{
			$cbo_currency_job=$infr[csf('currency_id')];
			$exchange_rate=$infr[csf('exchange_rate')];
			if($cbo_currency==$cbo_currency_job) $exchange_rate=1;
			$job_no=$infr[csf('job_no')];
			//$job_id=$infr[csf('job_id')];
			$pre_cost_trim_id=$infr[csf('job_id')];

			if(!empty($delivery_date))
			{
				$delivery_date=min($delivery_date,$infr[csf('delivery_date')]);
			}
			else
			{
				$delivery_date=$infr[csf('delivery_date')];
			}

			$req_qnty_cons_uom=$infr[csf('order_quantity')];
			$req_amount_cons_uom=$infr[csf('amount')];
			$rate_cons_uom=$req_amount_cons_uom/$req_qnty_cons_uom;
			$req_qnty_ord_uom=def_number_format($req_qnty_cons_uom/$sql_lib_item_group_array[$infr[csf('trim_group')]][conversion_factor],3,"");
			$rate_ord_uom=def_number_format(($rate_cons_uom*$sql_lib_item_group_array[$infr[csf('trim_group')]][conversion_factor])*$exchange_rate,3,"");
			$req_amount_ord_uom=def_number_format($req_qnty_ord_uom*$rate_ord_uom,3,"");

			$cu_woq=$cu_booking_arr[$job_no][$job_id]['cu_woq'][$infr[csf('po_id')]];
			$cu_amount=$cu_booking_arr[$job_no][$job_id]['cu_amount'][$infr[csf('po_id')]];
			if($cu_woq=="") $cu_woq=0;
			if($cu_amount=="") $cu_amount=0;
			$bal_woq=def_number_format($req_qnty_ord_uom,3,"");
			$amount=def_number_format($infr[csf('cu_amount')],3,"");
			$total_req_amount+=$req_amount;
			$total_cu_amount+=$infr[csf('cu_amount')];
			
			$costing_per=0;
			if($infr[csf('costing_per')]==1) $costing_per=12;
			else if($infr[csf('costing_per')]==2) $costing_per=1;
			else if($infr[csf('costing_per')]==3) $costing_per=24;
			else if($infr[csf('costing_per')]==4) $costing_per=36;
			else if($infr[csf('costing_per')]==5) $costing_per=48;
			else $costing_per=0;
			
			$itempoWiseReqQty=0;
			if($infr[csf('remark')]=="") $infr[csf('remark')]="";
			if($infr[csf('description')]=="") $infr[csf('description')]="";
			if($infr[csf('brand_sup_ref')]=="") $infr[csf('brand_sup_ref')]="";
			if($infr[csf('hs_code')]=="") $infr[csf('hs_code')]="";
			$job_id=$infr[csf('job_id')];
		
			//$itempoWiseReqQty=($powiseCostingPerReqQtyArr[$pre_cost_trim_id][$infr[csf('po_id')]]['amt']/$infr[csf('plan_cut')])*$costing_per;
			//if($infr[csf('po_id')]==58499) echo $powiseCostingPerReqQtyArr[$pre_cost_trim_id][$infr[csf('po_id')]]['amt'].'='.$infr[csf('plan_cut')].'='.$costing_per.'<br>';

			$job_and_trimgroup_level[$job_no]['job_no'][$infr[csf('po_id')]]=$job_no;
			$job_and_trimgroup_level[$job_no]['job_id'][$infr[csf('po_id')]]=$job_id;
			$job_and_trimgroup_level[$job_no]['po_id'][$infr[csf('po_id')]]=$infr[csf('po_id')];
			$job_and_trimgroup_level[$job_no]['style_ref_no'][$infr[csf('po_id')]]=$infr[csf('style_ref_no')];
			$job_and_trimgroup_level[$job_no]['company_name'][$infr[csf('company_name')]]=$infr[csf('company_name')];
			$job_and_trimgroup_level[$job_no]['po_number'][$infr[csf('po_id')]]=$infr[csf('po_number')];
			//$job_and_trimgroup_level[$job_no][$job_id]['country'][$infr[csf('po_id')]]=$infr[csf('country')];
			$job_and_trimgroup_level[$job_no]['description'][$infr[csf('po_id')]]=$infr[csf('description')];
			$job_and_trimgroup_level[$job_no]['hs_code'][$infr[csf('po_id')]]=$infr[csf('hs_code')];
			$job_and_trimgroup_level[$job_no]['remark'][$infr[csf('po_id')]]=$infr[csf('remark')];
			$job_and_trimgroup_level[$job_no]['brand_sup_ref'][$infr[csf('po_id')]]=$infr[csf('brand_sup_ref')];
			$job_and_trimgroup_level[$job_no]['trim_group'][$infr[csf('po_id')]]=$infr[csf('trim_group')];
			$job_and_trimgroup_level[$job_no]['trim_group_name'][$infr[csf('po_id')]]=$trim_group_library[$infr[csf('trim_group')]];
			//$job_and_trimgroup_level[$job_no][$job_id]['wo_pre_cost_trim_cost_dtls'][$infr[csf('po_id')]]=$job_id;

			$job_and_trimgroup_level[$job_no]['req_qnty'][$infr[csf('po_id')]]=$req_qnty_ord_uom;
			$job_and_trimgroup_level[$job_no]['po_qnty'][$infr[csf('po_id')]]=$req_qnty_ord_uom;
			$job_and_trimgroup_level[$job_no]['uom'][$infr[csf('po_id')]]=$sql_lib_item_group_array[$infr[csf('trim_group')]][cons_uom];
			$job_and_trimgroup_level[$job_no]['uom_name'][$infr[csf('po_id')]]=$unit_of_measurement[$sql_lib_item_group_array[$infr[csf('trim_group')]][cons_uom]];
			$job_and_trimgroup_level[$job_no]['req_amount'][$infr[csf('po_id')]]=$req_amount_ord_uom;
			$job_and_trimgroup_level[$job_no]['req_amount_cons_uom'][$infr[csf('po_id')]]=$req_amount_cons_uom;

			$job_and_trimgroup_level[$job_no]['cu_woq'][$infr[csf('po_id')]]=$cu_woq;
			$job_and_trimgroup_level[$job_no]['cu_amount'][$infr[csf('po_id')]]=$cu_amount;
			$job_and_trimgroup_level[$job_no]['bal_woq'][$infr[csf('po_id')]]=$bal_woq;
			$job_and_trimgroup_level[$job_no]['exchange_rate'][$infr[csf('po_id')]]=$exchange_rate;
			$job_and_trimgroup_level[$job_no]['rate'][$infr[csf('po_id')]]=$rate_ord_uom;
			$job_and_trimgroup_level[$job_no]['amount'][$infr[csf('po_id')]]=$amount;
			$job_and_trimgroup_level[$job_no]['pre_req_amt'][$infr[csf('po_id')]]=$infr[csf('amount')];
			$job_and_trimgroup_level[$job_no]['txt_delivery_date'][$infr[csf('po_id')]]=$infr[csf('delivery_date')];
			//echo $infr[csf('booking_id')].'d';
			$job_and_trimgroup_level[$job_no]['booking_id'][$infr[csf('po_id')]]=$infr[csf('booking_id')];
			$job_and_trimgroup_level[$job_no]['sensitivity'][$infr[csf('po_id')]]=$infr[csf('sensitivity')];
			$job_and_trimgroup_level[$job_no]['reqqtycostingper'][$infr[csf('po_id')]]=def_number_format($itempoWiseReqQty,5,"");
		}
	}
	//print_r($job_and_trimgroup_level);
	
	
	$sql_booking=sql_select("select a.id as job_id,c.job_no, c.pre_cost_fabric_cost_dtls_id, c.po_break_down_id, sum(c.wo_qnty) as wo_qnty, sum(c.amount) as amount from wo_po_details_master a, wo_po_break_down d, wo_booking_dtls c where a.id=d.job_id and a.job_no=c.job_no and  d.id=c.po_break_down_id and c.booking_no=$txt_booking_no  and c.id in($booking_id) and c.booking_type=8  and c.entry_form_id=555 and c.status_active=1 and c.is_deleted=0 group by a.id,c.job_no, c.pre_cost_fabric_cost_dtls_id, c.po_break_down_id");
	//echo "select a.id as job_id,c.job_no, c.pre_cost_fabric_cost_dtls_id, c.po_break_down_id, sum(c.wo_qnty) as wo_qnty, sum(c.amount) as amount from wo_po_details_master a, wo_po_break_down d, wo_booking_dtls c where a.id=d.job_id and a.job_no=c.job_no and  d.id=c.po_break_down_id and c.booking_no=$txt_booking_no  and c.id in($booking_id) and c.booking_type=2  and c.entry_form_id=555 and c.status_active=1 and c.is_deleted=0 group by a.id,c.job_no, c.pre_cost_fabric_cost_dtls_id, c.po_break_down_id";
	foreach($sql_booking as $row_booking)
	{
		$job_id=$row_booking[csf('po_break_down_id')];
		$job_and_trimgroup_level[$row_booking[csf('job_no')]]['woq'][$row_booking[csf('po_break_down_id')]]=$row_booking[csf('wo_qnty')];
		$job_and_trimgroup_level[$row_booking[csf('job_no')]]['amount'][$row_booking[csf('po_break_down_id')]]=$row_booking[csf('amount')];
	}
	unset($sql_booking);
	
	?>
	
		  <input type="hidden" id="strdata" value='<?=json_encode($job_and_trimgroup_level); ?>' style="background-color:#CCC"/>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1510" class="rpt_table" >
        <thead>
            <th width="40">SL</th>
            <th width="80">Job No</th>
            <th width="80">Style Ref</th>
            <th width="100">Ord. No</th>
            <th width="100">Trims Group</th>
            <th width="100">HS Code</th>
            <th width="150">Description</th>
            <th width="150">Brand Sup.</th>
            
            <th width="50">UOM</th>
            
            <th width="100">Sensitivity</th>
            <?
            if($cbo_item_from==2)
			{
			?>
            <th width="80">Gmts Qty</th>
            <?
			}
			?>
            <th width="80">WOQ</th>
            <th width="55">Exch.Rate</th>
            <th width="80">Rate</th>
            <th width="80">Amount</th>
            <th width="80">Delv. Date</th>
            <th>Remark</th>
        </thead>
	</table>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1510" class="rpt_table" id="tbl_list_search" >
        <tbody>
		<?
		 if($cbo_level==1)
        {
            foreach ($nameArray as $selectResult)
            {
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

                $cbo_currency_job=$selectResult[csf('currency_id')];
                $exchange_rate=$selectResult[csf('exchange_rate')];
                if($cbo_currency==$cbo_currency_job) $exchange_rate=1;
				$po_id=$selectResult[csf('po_id')];

                $req_qnty_cons_uom=$selectResult[csf('cu_woq')];
                $req_amount_cons_uom=$selectResult[csf('amount')];
                $rate_cons_uom=$req_amount_cons_uom/$req_qnty_cons_uom;

               $req_qnty_ord_uom=def_number_format($req_qnty_cons_uom/$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor],3,"");
               $rate_ord_uom=def_number_format(($rate_cons_uom*$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor])*$exchange_rate,3,"");
               $req_amount_ord_uom=def_number_format($req_qnty_ord_uom*$rate_ord_uom,3,"");

                $cu_woq=$cu_booking_arr[$selectResult[csf('job_no')]][$selectResult[csf('job_id')]]['cu_woq'][$selectResult[csf('po_id')]];
                $cu_amount=$cu_booking_arr[$selectResult[csf('job_no')]][$selectResult[csf('job_id')]]['cu_amount'][$selectResult[csf('po_id')]];
                $bal_woq=def_number_format($req_qnty_ord_uom,3,"");

                $woq=$job_and_trimgroup_level[$selectResult[csf('job_no')]]['woq'][$selectResult[csf('po_id')]];
                $amount=$job_and_trimgroup_level[$selectResult[csf('job_no')]]['amount'][$selectResult[csf('po_id')]];
                $rate=$amount/$woq;
                $total_amount+=$amount;
                $reqAmtJobLevelConsUom=$reqAmountJobLevelArr[$selectResult[csf('job_no')]];

				$piNumber=0;
				$pi_number=$pi_array[$trim_group];
				if($pi_number!="") $piNumber=1;

				$recvNumber=0;
				$recv_number=$recv_array[$trim_group];
				if($recv_number) $recvNumber=1;

				$disAbled=0;
				if($recvNumber==1 || $piNumber==1) $disAbled=1; else $disAbled=0;
				if($cbo_company_name<0) $cbo_company_name=0;
					$tna_integrated=return_field_value("tna_integrated","variable_order_tracking","company_name='$cbo_company_name' and variable_list=14 and status_active=1 and is_deleted=0","tna_integrated");
					//echo $cbo_company_name.'X';
					if($tna_integrated==1)
					{
						//echo " $('#txt_delevary_date').attr('disabled',true);\n";
						$deli_date_con="disabled=disabled";
					}
					else
					{
						//echo "$('#txt_delevary_date').attr('disabled',false);\n";
						$deli_date_con="";
					}
					/*$trim_type=$trim_type_arr[$selectResult[csf('trim_group')]];
					//echo $trim_type.'ddd';
					if($trim_type==1) $task_num='70'; else $task_num='71';
					$task_finish_date='';
					$tnasql=sql_select("select po_number_id,task_finish_date from tna_process_mst where task_number=$task_num and po_number_id in($po_id) and is_deleted= 0 and status_active=1");

					foreach($tnasql as $tnarow){
					$task_finish_date_arr[$tnarow[csf('po_number_id')]]=$tnarow[csf('task_finish_date')];
					}
					$task_finish_date=$task_finish_date_arr[$selectResult[csf('po_id')]];
					$sql_tna_lib=sql_select("select b.date_calc, b.day_status from lib_capacity_calc_mst a, lib_capacity_calc_dtls b where a.id=b.mst_id and a.comapny_id=$cbo_company_name and b.date_calc='$task_finish_date' and a.status_active=1 and a.is_deleted=0");

					$date_calc=$sql_tna_lib[0][csf("date_calc")];
					$day_status=$sql_tna_lib[0][csf("day_status")];

					if($day_status==2)
					{
					$task_finish_date=return_field_value("max(b.date_calc) as  date_calc ", " lib_capacity_calc_mst a, lib_capacity_calc_dtls b "," a.id=b.mst_id and a.comapny_id=$cbo_company_name and b.date_calc<'$task_finish_date' and a.status_active=1 and a.is_deleted=0 and b.day_status=1","date_calc");
					}
					else
					{
					$task_finish_date=$task_finish_date;
					}

					//$delivery_date="";
					if($task_finish_date !='')
					{
						$txt_tna_date=change_date_format($task_finish_date,'dd-mm-yyyy','-');
					}
					else
					{
						$txt_tna_date="";
					}*/
					
					$costing_per=0;
				if($selectResult[csf('costing_per')]==1) $costing_per=12;
				else if($selectResult[csf('costing_per')]==2) $costing_per=1;
				else if($selectResult[csf('costing_per')]==3) $costing_per=24;
				else if($selectResult[csf('costing_per')]==4) $costing_per=36;
				else if($selectResult[csf('costing_per')]==5) $costing_per=48;
				else $costing_per=0;
				
				$itempoWiseReqQty=0;
			
				$po_qty=$selectResult[csf('order_quantity')];//$itempoWiseReqQty=($powiseCostingPerReqQtyArr[$selectResult[csf('wo_pre_cost_trim_cost_dtls')]][$selectResult[csf('po_id')]]['amt']/$selectResult[csf('plan_cut')])*$costing_per;

                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
                    <td width="40"><? echo $i;?></td>
                    <td width="80"><? echo $selectResult[csf('job_no')];?>
                        <input type="hidden" id="txtjob_<? echo $i;?>" value="<? echo $selectResult[csf('job_no')];?>" style="width:30px" class="text_boxes" readonly/>
                    </td>
                      <td width="80"> <p><? echo $selectResult[csf('style_ref_no')];?> </p>
                         
                    </td>
                    <td width="100" style="word-break:break-all"><? echo $selectResult[csf('po_number')];?>
                        <input type="hidden" id="txtbookingid_<? echo $i;?>" value="<? echo $selectResult[csf('booking_id')];?>" readonly/>
                        <input type="hidden" id="txtpoid_<? echo $i;?>" value="<? echo $selectResult[csf('po_id')];?>" readonly/>
                        <input type="hidden" id="txtcountry_<? echo $i;?>"  value="<? echo $selectResult[csf('country')] ?>" readonly />
                    </td>
                    <td width="100"  title="<? echo $sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor];  ?>">
                        <? //echo $trim_group_library[$selectResult[csf('trim_group')]];?>
                        <input type="hidden" id="txttrimcostid_<? echo $i;?>" value="<? //echo $selectResult[csf('job_id')];?>" readonly/>
                       
                        <input class="text_boxes" name="txtReqAmt_<?=$i;?>" id="txtReqAmt_<?=$i;?>" type="hidden" value="<?=$itempoWiseReqQty; ?>" style="width:30px"/>
						<input id="hiddlabeldtlsdata_<?=$i;?>" type="hidden" value=""/>
                         <? echo create_drop_down( "txttrimgroup_".$i, 100, $trim_group_library,"", 1, "--Select--", $selectResult[csf('trim_group')], "copy_value(this.value,'txttrimgroup_',$i)","","" ); ?>
                    </td>
					<td width="100">
                        <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<? echo $bgcolor; ?>" id="txthscode_<? echo $i;?>"  value="<? echo $selectResult[csf('hs_code')];?>"  <? if($disAbled){echo "disabled";}else{ echo "";}?> />
                    </td>
                    <td width="150">
                        <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<? echo $bgcolor; ?>" id="txtdesc_<? echo $i;?>"  value="<? echo $selectResult[csf('description')];?>"  <? if($disAbled){echo "disabled";}else{ echo "";}?> />
                    </td>
                    <td width="150">
                        <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<? echo $bgcolor; ?>" id="txtbrandsup_<? echo $i;?>"  value="<? echo $selectResult[csf('brand_sup_ref')];?>"  <? if($disAbled){echo "disabled";}else{ echo "";}?> />
                    </td>
                    <td width="50" align="right">
                        <input type="hidden"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqqnty_<? echo $i;?>" value="<? echo number_format($req_qnty_ord_uom,4,'.','');?>"  readonly  />
                        <input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamount_<? echo $i;?>" value="<? echo number_format($req_amount_ord_uom,4,'.','');?>"  readonly  />
                        <input type="hidden"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamountjoblevelconsuom_<? echo $i;?>" value="<? echo number_format($reqAmtJobLevelConsUom,4,'.','');?>"  readonly  />
                        <input type="hidden"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamountitemlevelconsuom_<? echo $i;?>" value="<? echo number_format($req_amount_cons_uom,4,'.','');?>"  readonly  />
                    
                        <? //echo $unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom]];?>
                       
                         <? echo create_drop_down( "txtuom_".$i, 50, $unit_of_measurement,"", 1, "--Select--", $sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom], "","","" ); ?>
                    </td>
                    <td width="100" align="right">
                        <input type="hidden"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuwoq_<? echo $i;?>" value="<? echo number_format($cu_woq,4,'.','');?>"  readonly  />
                        <input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuamount_<? echo $i;?>" value="<? echo number_format($cu_amount,4,'.','');?>"  readonly  />
                     
                        <input type="hidden"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtbalwoq_<? echo $i;?>" value="<? echo number_format($bal_woq,4,'.',''); ?>"  readonly  />
                    <? echo create_drop_down( "cbocolorsizesensitive_".$i, 100, $size_color_sensitive,"", 1, "--Select--", $selectResult[csf("sensitivity")], "set_cons_break_down($i),copy_value(this.value,'cbocolorsizesensitive_',$i)",$disAbled,"1,2,3,4" ); ?>
                    </td>
                    <td width="80" align="right">
                        <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#CCC" id="txtgmtsqty_<? echo $i;?>" value="<? echo number_format($po_qty,4,'.','');?>"  readonly />
                    </td>
                     <td width="80" align="right">
                        <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoq_<? echo $i;?>" value="<? echo number_format($woq,4,'.','');?>" onClick="open_consumption_popup('requires/multi_job_additional_fabric_booking_controller.php?action=consumption_popup', '<?=$trim_group_library[$selectResult[csf('trim_group')]];?>','txtpoid_<? echo $i;?>',<? echo $i;?>)" readonly />
                    </td>
                    <td width="55" align="right">
                        <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtexchrate_<? echo $i;?>" value="<? echo $exchange_rate;?>" readonly />
                    </td>
                    <td width="80" align="right">
                        <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_<? echo $i;?>" value="<? echo $rate;?>" onChange="calculate_amount(<? echo $i; ?>)" readonly />

                        <input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_precost_<? echo $i;?>" value="<? echo $rate_ord_uom;?>" readonly />
                    </td>
                    <td width="80" align="right">
                        <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtamount_<? echo $i;?>" value="<? echo number_format($amount,4,'.','');?>"  readonly  />
                    </td>
                    <td width="80" align="center">
                        <input type="text"   style="width:90%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:center; background-color:<? echo $bgcolor; ?>" id="txtddate_<? echo $i;?>"  class="datepicker" value="<? echo change_date_format($selectResult[csf('delivery_date')],"dd-mm-yyyy","-"); ?>"  onChange="compare_date(2)"  readonly <? if($disAbled){echo "disabled";}else{ echo "";}?> <? echo $deli_date_con;?>   />
                         <input name="txttnadate_<? echo $i;?>" id="txttnadate_<? echo $i;?>" class="datepicker" type="hidden" value="<? echo $txt_tna_date;?>" style="width:70px;"  readonly/>
                        <input type="hidden" id="consbreckdown_<? echo $i;?>"  value=""/>
                        <input type="hidden" id="jsondata_<? echo $i;?>"  value=""/>
                    </td>
                    <td>
                   	 <input class="text_boxes" type="text" style="width:75px;"  name="txtremark_<? echo $i;?>" id="txtremark_<? echo $i;?>"  value="<? echo $selectResult[csf('remark')];?>"/>
                    </td>
                </tr>
            <?
            $i++;
            }
        }
        else if($cbo_level==2)
        {
            $i=1;
            foreach ($job_and_trimgroup_level as $wo_pre_cost_trim_cost_dtls)
            {
               // foreach ($job_no as $wo_pre_cost_trim_cost_dtls)
               // {
                    $job_no=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['job_no']));
					$job_id=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['job_id']));
					//echo $job_no.'dd';
					$style_ref_no=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['style_ref_no']));
					$remark=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['remark']));
                    $po_number=implode(", ",$wo_pre_cost_trim_cost_dtls['po_number']);
                    $po_id=implode(",",$wo_pre_cost_trim_cost_dtls['po_id']);
					$company_name=implode(",",$wo_pre_cost_trim_cost_dtls['company_name']);
                    $country=implode(",",array_unique(explode(",",implode(",",$wo_pre_cost_trim_cost_dtls['country']))));
                    $description=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['description']));
                    $hs_code=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['hs_code']));
                    $brand_sup_ref=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['brand_sup_ref']));
                    $wo_pre_cost_trim_id=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['wo_pre_cost_trim_cost_dtls']));
					$wo_pre_req_amt=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['pre_req_amt']));

                    $trim_group = implode(",",array_unique($wo_pre_cost_trim_cost_dtls['trim_group']));
                    $uom=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['uom']));
                    $booking_id=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['booking_id']));
                    $sensitivity=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['sensitivity']));
                    //$delivery_date=implode(",",array_unique($wo_pre_cost_trim_cost_dtls['txt_delivery_date']));

                    $req_qnty_ord_uom=array_sum($wo_pre_cost_trim_cost_dtls['req_qnty']);
					$po_qnty=array_sum($wo_pre_cost_trim_cost_dtls['po_qnty']);
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
					$pi_number=$pi_array[$trim_group];
					if($pi_number!="") $piNumber=1;

					$recvNumber=0;
					$recv_number=$recv_array[$trim_group];
					if($recv_number) $recvNumber=1;

					$disAbled=0;
					if($recvNumber==1 || $piNumber==1) $disAbled=1; else $disAbled=0;
					if($cbo_company_name<0) $cbo_company_name=0;
					$tna_integrated=return_field_value("tna_integrated","variable_order_tracking","company_name='$cbo_company_name' and variable_list=14 and status_active=1 and is_deleted=0","tna_integrated");
					//echo $cbo_company_name.'X';
					if($tna_integrated==1)
					{
						//echo " $('#txt_delevary_date').attr('disabled',true);\n";
						$deli_date_con="disabled=disabled";
					}
					else
					{
						//echo "$('#txt_delevary_date').attr('disabled',false);\n";
						$deli_date_con="";
					}
					/*$trim_type=$trim_type_arr[$trim_group];
					//echo $trim_type.'ddd';
					if($trim_type==1) $task_num='70'; else $task_num='71';
					$task_finish_date='';
					//$tnasql=sql_select("select po_number_id,task_finish_date from tna_process_mst where task_number=$task_num and po_number_id in($po_id) and is_deleted= 0 and status_active=1");
					$tnasql=sql_select("select min(po_number_id) as po_number_id,min(task_finish_date) as  task_finish_date from tna_process_mst where task_number=$task_num and po_number_id in($po_id) and is_deleted= 0 and status_active=1 order by task_finish_date ");
					//echo "select min(po_number_id) as po_number_id,min(task_finish_date) as  task_finish_date from tna_process_mst where task_number=$task_num and po_number_id in($po_id) and is_deleted= 0 and status_active=1 order by task_finish_date ";

					foreach($tnasql as $tnarow){
					//$task_finish_date_arr[$tnarow[csf('po_number_id')]]=$tnarow[csf('task_finish_date')];
					$task_finish_date=$tnarow[csf('task_finish_date')];
					}
					//$task_finish_date=$task_finish_date_arr[$po_id];
					$sql_tna_lib=sql_select("select b.date_calc, b.day_status from lib_capacity_calc_mst a, lib_capacity_calc_dtls b where a.id=b.mst_id and a.comapny_id=$cbo_company_name and b.date_calc='$task_finish_date' and a.status_active=1 and a.is_deleted=0");

					$date_calc=$sql_tna_lib[0][csf("date_calc")];
					$day_status=$sql_tna_lib[0][csf("day_status")];

					if($day_status==2)
					{
					$task_finish_date=return_field_value("max(b.date_calc) as  date_calc ", " lib_capacity_calc_mst a, lib_capacity_calc_dtls b "," a.id=b.mst_id and a.comapny_id=$cbo_company_name and b.date_calc<'$task_finish_date' and a.status_active=1 and a.is_deleted=0 and b.day_status=1","date_calc");
					}
					else
					{
					$task_finish_date=$task_finish_date;
					}

					//$delivery_date="";
					if($task_finish_date !='')
					{
						$txt_tna_date=change_date_format($task_finish_date,'dd-mm-yyyy','-');
					}
					else
					{
						$txt_tna_date="";
					}*/
					//echo $disAbled.'=='.$deli_date_con.'='.$txt_tna_date;
					//$labeldtlsdata=return_field_value("labeldtlsdata", "wo_booking_dtls", "id=$booking_id");
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
                        <td width="40"><? echo $i;?></td>
                        <td width="80"><? echo $job_no?><input type="hidden" id="txtjob_<? echo $i;?>" value="<? echo $job_no;?>" style="width:30px" class="text_boxes" readonly/></td>
                        <td width="80"> <p><? echo $style_ref_no;?></p></td>
                        
                        <td width="100" style="word-break:break-all"><? echo $po_number; ?>
                            <input type="hidden" id="txtbookingid_<? echo $i;?>" value="<? echo $booking_id; ?>" readonly/>
                            <input type="hidden" id="txtpoid_<? echo $i;?>" value="<? echo $po_id; ?>" readonly/>
                            <input type="hidden" id="txtcountry_<? echo $i;?>"  value="<? echo $country; ?>" readonly />
                        </td>
                        <td width="100"  title="<? echo $sql_lib_item_group_array[$trim_group][conversion_factor]; ?>">
						<a href="javascript:void(0)" onClick="openlabeldtls_popup('<?=$booking_id."_".$trim_group."_".$i; ?>',<?=$i; ?>);"><? //echo $trim_group_library[$trim_group];?></a>
                            <input type="hidden" id="txttrimcostid_<? echo $i;?>" value="<? //echo $job_id;?>" readonly/>
                           
                            <input class="text_boxes" name="txtReqAmt_<?=$i;?>" id="txtReqAmt_<?=$i;?>" type="hidden" value="<? //=$wo_pre_req_amt; ?>" style="width:30px"/>
							<input id="hiddlabeldtlsdata_<?=$i;?>" type="hidden" value="<?=$labeldtlsdata;?>"/>
                             <? echo create_drop_down( "txttrimgroup_".$i, 100, $trim_group_library,"", 1, "--Select--", $trim_group, "copy_value(this.value,'txttrimgroup_',$i)","","" ); ?>
                        </td>
                        <td width="100">
                            <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<? echo $bgcolor; ?>" id="txthscode_<? echo $i;?>"  value="<? echo $hs_code; ?>" <? if($disAbled){echo "disabled";}else{ echo "";}?> />
                        </td>
                        <td width="150">
                            <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<? echo $bgcolor; ?>" id="txtdesc_<? echo $i;?>"  value="<? echo $description; ?>" <? if($disAbled){echo "disabled";}else{ echo "";}?> />
                        </td>
                        <td width="150">
                            <input type="text" style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  background-color:<? echo $bgcolor; ?>" id="txtbrandsup_<? echo $i;?>"  value="<? echo $brand_sup_ref;?>" <? if($disAbled){echo "disabled";}else{ echo "";}?>  />
                        </td>
                        <td width="50" align="right">
                            <input type="hidden"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqqnty_<? echo $i;?>" value="<? echo number_format($req_qnty_ord_uom,4,'.','');?>"  readonly  />
                            <input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamount_<? echo $i;?>" value="<? echo number_format($req_amount_ord_uom,4,'.','');?>"  readonly  />
                            <input type="hidden"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamountjoblevelconsuom_<? echo $i;?>" value="<? echo number_format($reqAmtJobLevelConsUom,4,'.','');?>"  readonly  />
                            <input type="hidden"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamountitemlevelconsuom_<? echo $i;?>" value="<? echo number_format($req_amount_cons_uom,4,'.','');?>"  readonly  />
                        <?  //echo $unit_of_measurement[$uom];?><input type="hidden" id="txtuom_<? echo $i;?>" value="<? echo $uom;?>" readonly />
                         <? echo create_drop_down( "txtuom_".$i, 50, $unit_of_measurement,"", 1, "--Select--", $uom, "","","" ); ?>
                        </td>
                        <td width="100" align="right">
                            <input type="hidden"  style="width:100%; height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuwoq_<? echo $i;?>" value="<? echo number_format($cu_woq,4,'.',''); ?>"  readonly  />
                            <input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuamount_<? echo $i;?>" value="<? echo $cu_amount;?>"  readonly  />
                         
                            <input type="hidden"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtbalwoq_<? echo $i;?>" value="<? echo number_format($bal_woq,4,'.','');?>"  readonly  />
                         <?  echo create_drop_down( "cbocolorsizesensitive_".$i, 100, $size_color_sensitive,"", 1, "--Select--", $sensitivity, "copy_value(this.value,'cbocolorsizesensitive_',$i)",$disAbled,"1,2,3,4" ); ?>
                        </td>
                         <td width="80" align="right">
                        <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color: #CCC" id="txtgmtsqty_<? echo $i;?>" value="<? echo number_format($po_qnty,4,'.','');?>"  readonly />
                    </td>
                        <td width="80" align="right">
                            <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoq_<? echo $i;?>" value="<? echo number_format($woq,4,'.','');?>" onClick="open_consumption_popup('requires/multi_job_additional_fabric_booking_controller.php?action=consumption_popup', '<?=$trim_group_library[$trim_group];?>','txtpoid_<? echo $i;?>',<? echo $i;?>)" readonly />
                        </td>
                        <td width="55" align="right">
                            <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtexchrate_<? echo $i;?>" value="<? echo $exchange_rate;?>" readonly />
                        </td>
                        <td width="80" align="right">
                            <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_<? echo $i;?>" value="<? echo $rate;?>" onChange="calculate_amount(<? echo $i; ?>)" readonly />
                            <input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_precost_<? echo $i;?>" value="<? echo $rate_ord_uom;?>" readonly />
                        </td>
                        <td width="80" align="right">
                            <input type="text"  style="width:100%;height:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtamount_<? echo $i;?>" value="<? echo number_format($amount,4,'.','');?>"  readonly  />
                        </td>
                        <td width="" align="right">
                            <input type="text"   style="width:90%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtddate_<? echo $i;?>"  class="datepicker" onChange="compare_date(2)" value="<? echo change_date_format($delivery_date,"dd-mm-yyyy","-"); ?>"  readonly <? if($disAbled){echo "disabled";}else{ echo "";}?>  <? echo $deli_date_con;?>   />
                            <input name="txttnadate_<? echo $i;?>" id="txttnadate_<? echo $i;?>" class="datepicker" type="hidden" value="<? echo $txt_tna_date;?>" style="width:70px;"  readonly/>
                            <input type="hidden" id="consbreckdown_<? echo $i;?>"  value=""/>
                            <input type="hidden" id="jsondata_<? echo $i;?>"  value=""/>
                        </td>
                         <td>
                   		 <input class="text_boxes" type="text" style="width:75px;"  name="txtremark_<? echo $i;?>" id="txtremark_<? echo $i;?>" value="<? echo $remark;?>"/>
                    </td>
                    </tr>
                    <?
                    $i++;
                //}
            }
        }
	}
        ?>
        </tbody>
	</table>
    <?
    if($cbo_item_from==1)
  {
	?>
	<table width="1430" class="rpt_table" border="0" rules="all">
        <tfoot>
            <tr>
                <th width="40">&nbsp;</th>
                <th width="80">&nbsp;</th> 
                <th width="80">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="150">&nbsp;</th>
                <th width="150">&nbsp;</th>
                
                <th width="50">&nbsp;</th>
                 
                <th width="100">&nbsp;</th>
                 
                <th width="80">&nbsp;</th>
                <th width="55">&nbsp;</th>
                <th width="80"><input type="hidden" id="tot_amount" value="<? echo  number_format($total_amount,4,'.',''); ?>" class="text_boxes_numeric" style="width:140px"/></th>
                <th width="80"><input type="hidden" id="tot_amount" value="<? echo  number_format($total_amount,4,'.',''); ?>" class="text_boxes_numeric" style="width:140px"/></th>
                <th width="80"><input type="hidden" id="saved_tot_amount" value="0" style="width:80px; text-align:right" readonly/></th>
                  <th >&nbsp;</th>
            </tr>
        </tfoot>
	</table>
    <?
  }
	?>
     <?
   if($cbo_item_from==2)
  {
	?>
	<table width="1510" class="rpt_table" border="0" rules="all">
        <tfoot>
            <tr>
                <th width="40">&nbsp;</th>
                <th width="80">&nbsp;</th> 
                <th width="80">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="150">&nbsp;</th>
                <th width="150">&nbsp;</th>
                
                <th width="50">&nbsp;</th>
                 
                <th width="100">&nbsp;</th>
                 <?
				if($cbo_item_from==2)
				{
				?>
				<th width="80">&nbsp; </th>
				<?
				}
				?>
                <th width="80">&nbsp;</th>
                <th width="55">&nbsp;</th>
                <th width="80"><input type="hidden" id="tot_amount" value="<? echo  number_format($total_amount,4,'.',''); ?>" class="text_boxes_numeric" style="width:140px"/></th>
                <th width="80"><input type="hidden" id="tot_amount" value="<? echo  number_format($total_amount,4,'.',''); ?>" class="text_boxes_numeric" style="width:140px"/></th>
                <th width="80"><input type="hidden" id="saved_tot_amount" value="0" style="width:80px; text-align:right" readonly/></th>
                  <th >&nbsp;</th>
            </tr>
        </tfoot>
	</table>
    <?
  }
	?>
    <table width="1430" colspan="15" cellspacing="0" class="" border="0">
        <tr>
            <td align="center"class="button_container">
            	<? echo load_submit_buttons( $permission, "fnc_additional_fabric_booking_dtls", 1,0,"reset_form('','booking_list_view','','','')",2); ?>
            </td>
        </tr>
    </table>
	<?
	exit();
}

if($action=="show_fabric_booking_list")
{
	extract($_REQUEST);
	$txt_order_no_id=str_replace("'","",$txt_order_no_id);
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	$pre_cost_fabric_cost_dtls_id=str_replace("'","",$cbo_fabric_description);
	$cbouom=str_replace("'","",$cbouom);
	$cbo_level=str_replace("'","",$cbo_level);
	
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
	
	$companyId=return_field_value("company_id", "wo_booking_mst", "booking_no=".$txt_booking_no." and status_active=1 and is_deleted=0");
	//echo "select company_id from wo_booking_mst where booking_no=".$txt_booking_no." and status_active=1 and is_deleted=0";
	
	$variable_textile_sales_maintain = sql_select("select production_entry from variable_settings_production where company_name='$companyId' and variable_list=66 and status_active=1");
	//echo "select production_entry from variable_settings_production where company_name='$companyId' and variable_list=66 and status_active=1";

	if($variable_textile_sales_maintain[0][csf('production_entry')] ==2) $textile_sales_maintain = 1; else $textile_sales_maintain = 0;
	$textile_sales_maintain = 0;
	
	$job_level_arr=array(); $fabric_description_array=array(); $color_Arr=array(); $Dia_Arr=array();
	$sql='select a.id AS "id",a.job_no AS "job_no", a.po_break_down_id AS "po_break_down_id",a.grey_fab_qnty AS "grey_fab_qnty",a.fin_fab_qnty AS "fin_fab_qnty",a.adjust_qty "adjust_qty",a.rate AS "rate",a.amount AS "amount",a.gmts_color_id AS "gmts_color_id",a.dia_width AS "dia_width", b.id AS "pre_cost_fabric_cost_dtls_id", b.body_part_id AS "body_part_id",b.color_type_id AS "color_type_id",b.fabric_description AS "fabric_description", b.gsm_weight AS "gsm_weight",b.uom AS "uom" from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b  where a.pre_cost_fabric_cost_dtls_id=b.id and a.status_active =1 and a.is_deleted=0  and a.booking_no='.$txt_booking_no.'';
	$dataArray=sql_select($sql); $poidArr=array();
	foreach($dataArray as $sql_row){
		$poidArr[$sql_row['po_break_down_id']]=$sql_row['po_break_down_id'];
	}
	
	$po_number_arr=return_library_array( "select id,po_number from wo_po_break_down where id in (".implode(",",$poidArr).")", "id", "po_number");
	
	foreach($dataArray as $sql_row){
		$job_level_arr[$sql_row['pre_cost_fabric_cost_dtls_id']]['job_no'][$sql_row['id']]=$sql_row['job_no'];
		$job_level_arr[$sql_row['pre_cost_fabric_cost_dtls_id']]['po_id'][$sql_row['id']]=$sql_row['po_break_down_id'];
		$job_level_arr[$sql_row['pre_cost_fabric_cost_dtls_id']]['po_number'][$sql_row['id']]=$po_number_arr[$sql_row['po_break_down_id']];
		$job_level_arr[$sql_row['pre_cost_fabric_cost_dtls_id']]['booking_id'][$sql_row['id']]=$sql_row['id'];
		$job_level_arr[$sql_row['pre_cost_fabric_cost_dtls_id']]['grey_fab_qnty'][$sql_row['id']]+=$sql_row['grey_fab_qnty'];
		
		if($textile_sales_maintain==1)
		{
			$job_level_arr[$sql_row['pre_cost_fabric_cost_dtls_id']]['fin_fab_qnty'][$sql_row['id']]+=$sql_row['grey_fab_qnty']-$sql_row['adjust_qty'];
		}
		else
		{
			$job_level_arr[$sql_row['pre_cost_fabric_cost_dtls_id']]['fin_fab_qnty'][$sql_row['id']]+=$sql_row['fin_fab_qnty']-$sql_row['adjust_qty'];
		}
		$job_level_arr[$sql_row['pre_cost_fabric_cost_dtls_id']]['adjust_qty'][$sql_row['id']]+=$sql_row['adjust_qty'];
		$job_level_arr[$sql_row['pre_cost_fabric_cost_dtls_id']]['amount'][$sql_row['id']]+=$sql_row['amount'];
	
		//$job_level_arr[$sql_row['pre_cost_fabric_cost_dtls_id']]['amount'][$sql_row['id']]+=$sql_row['amount'];
	
		$fabric_description_array[$sql_row['pre_cost_fabric_cost_dtls_id']]=$body_part[$sql_row['body_part_id']].', '.$color_type[$sql_row['color_type_id']].', '.$sql_row['fabric_description'].', '.$sql_row['gsm_weight'].', '.$unit_of_measurement[$sql_row['uom']];
		$color_Arr[$sql_row['pre_cost_fabric_cost_dtls_id']][$sql_row['gmts_color_id']]=$color_library[$sql_row['gmts_color_id']];
		$Dia_Arr[$sql_row['pre_cost_fabric_cost_dtls_id']][$sql_row['dia_width']]=$sql_row['dia_width'];
	}
	?>
	<table width="1200" class="rpt_table" border="0" rules="all">
        <thead>
            <th width="50"></th>
            <th width="80">Job No</th>
            <th width="80">Po Number</th>
            <th width="250">Fab. Description</th>
            <th width="130">Gmts Color</th>
            <th width="70">Dia</th>
            <th width="100">WO. Qty.</th>
            <th width="100">Adj. Qty.</th>
            <th width="100">Ac. Wo. Qty.</th>
            <th width="60">Rate</th>
            <th width="80">Amount</th>
            <th><input type="checkbox" name="chkdeleteall" id="chkdeleteall" value="2" ><a href="#" onClick="deletedata();">Delete All</a></th>
        </thead>
	</table>
	<div style="max-height:200px; overflow-y:scroll; width:1200px"  align="left">
        <table width="1183" class="rpt_table" id="tbl_fabric_booking_list" border="0" rules="all">
            <tbody>
            <? $i=1;
            if($cbo_level==2 || $cbo_level==1){
                $i=1;
                foreach($job_level_arr as $key=>$precost_id){
                    $job_no=implode(",",array_unique($precost_id['job_no']));
                    $po_break_down_id=implode(",",array_unique($precost_id['po_id']));
                    $po_number=implode(",",array_unique($precost_id['po_number']));
                    $grey_fab_qnty=array_sum($precost_id['grey_fab_qnty']);
                    $adjust_qty=array_sum($precost_id['adjust_qty']);
                    $fin_fab_qnty=array_sum($precost_id['fin_fab_qnty']);
                    $booking_id=implode(",",array_unique($precost_id['booking_id']));
                    //$rate=array_sum($precost_id['rate']);
                    $amount=array_sum($precost_id['amount']);
					$rate=0;
					if($amount>0 && $fin_fab_qnty>0){
						$rate=$amount/$fin_fab_qnty;
					}                    
                    ?>
                    <tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i; ?>" >
                        <td width="50" align="center"><a href="#" onClick="set_data('<?=$po_break_down_id; ?>','<?=$po_number; ?>','<?=$key; ?>','<?=$booking_id; ?>');">Edit</a></td>
                        <td width="80"><?=$job_no; ?></td>
                        <td width="80" style="word-break: break-all;word-wrap: break-word;" align="center"><a href="#" onClick="setdata('<?=$po_number; ?>' )">View</a></td>
                        <td width="250" style="word-break: break-all;word-wrap: break-word"><?=$fabric_description_array[$key]; ?></td>
                        <td width="130" style="word-break: break-all;word-wrap: break-word"><?=implode(",",$color_Arr[$key]); ?></td>
                        <td width="70" style="word-break: break-all;word-wrap: break-word" align="center"><?=implode(",",$Dia_Arr[$key]); ?></td>
                        <td width="100" align="right"><?=number_format($grey_fab_qnty,4,'.','');?></td>
                        <td width="100" align="right"><?=number_format($adjust_qty,4,'.','');?></td>
                        <td width="100" align="right"><?=number_format($fin_fab_qnty,4,'.',''); ?></td>
                        <td width="60" align="right"><?=number_format($rate,4,'.',''); ?></td>
                        <td width="80" align="right"><?=number_format($amount,4,'.','');?></td>
                        <td align="center">
                       		<input type="checkbox" name="chkdelete_<?=$i; ?>" id="chkdelete_<?=$i; ?>" value="2" ><input type="hidden" id="txtdelete<?=$i; ?>" name="txtdelete<?=$i; ?>" value="<?=$booking_id?>"/> 
                        </td>
                    </tr>
                    <?
					$i++;
                }
            }
            ?>
            </tbody>
        </table>
	</div>
	<?
	exit();
}

if($action=="check_is_booking_used")
{
	$txt_booking_no="'".$data."'";
	if($data!="")
	{
		$is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no and status_active=1 and is_deleted=0");
		foreach($sql as $row){
			if($row[csf('is_approved')]==3) $is_approved=1; else $is_approved=$row[csf('is_approved')];
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;
		}

		$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id and b.work_order_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0");
		if($pi_number){
			echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
			disconnect($con);die;
		}

		

		/*$sales_order=0;
		$sqls=sql_select("select job_no from fabric_sales_order_mst where sales_booking_no=$txt_booking_no and status_active=1 and is_deleted=0");
		foreach($sqls as $rows){
			$sales_order=$rows[csf('job_no')];
		}
		if($sales_order){
			echo "sal1**".str_replace("'","",$txt_booking_no)."**".$sales_order;
			die;
		}*/

		$receive_mrr=0;
		$sqlre=sql_select("select recv_number from inv_receive_master where booking_no=$txt_booking_no  and status_active=1 and is_deleted=0");
		foreach($sqlre as $rows){
			$receive_mrr=$rows[csf('recv_number')];
		}
		if($receive_mrr){
			echo "rec1**".str_replace("'","",$txt_booking_no)."**".$receive_mrr;
			disconnect($con);die;
		}

		$issue_mrr=0;
		$sqlis=sql_select("select issue_number from inv_issue_master where booking_no=$txt_booking_no  and status_active=1 and is_deleted=0");
		foreach($sqlis as $rows){
			$issue_mrr=$rows[csf('issue_number')];
		}
		if($issue_mrr){
			echo "iss1**".str_replace("'","",$txt_booking_no)."**".$issue_mrr;
			disconnect($con);die;
		}
	}
	exit();
}

if ($action == "consumption_popup")
{
	echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode,'','');
	$color_library=return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$size_library=return_library_array("select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	?>
	<script>
		var str_gmtssizes = [<? echo substr(return_library_autocomplete( "select size_name from lib_size where status_active=1 and is_deleted=0", "size_name" ), 0, -1); ?>];
		var str_diawidth = [<? echo substr(return_library_autocomplete( "select color_name from lib_color where status_active=1 and is_deleted=0", "color_name" ), 0, -1); ?>];

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
				//alert(txtwoq_qty+'='+txtwoq+'='+poreqqty);
				if(round_check==1){
					txtwoq_cal=Math.floor(txtwoq_cal);
					total_txtwoq_cal+=Math.floor(txtwoq_cal);
				}
				$('#qty_'+i).val(txtwoq_cal);
				calculate_requirement(i);
			}
			/* if(round_check==1){
				$('#txtwoq_qty').val(number_format_common(total_txtwoq_cal,5,0));
			} */
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
			//set_sum_value( 'qty_sum', 'qty_');
			calculate_requirement(j);
		}

		function calculate_requirement(i)
		{
			var process_loss_method_id=document.getElementById('process_loss_method_id').value;
			if(process_loss_method_id == ''){
				console.log('if process_loss_method_id setting value 0 calculate requirment set woqny 0 ::'+process_loss_method_id);
			}
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
			set_sum_value( 'woqty_sum', 'woqny_' );
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
			math_operation( des_fil_id, field_id, '+', rowCount,ddd );
		}

		function copy_value(value,field_id,i)
		{
			var itemsizes=document.getElementById('itemsizes_'+i).value;
			var gmtssizesid=document.getElementById('gmtssizesid_'+i).value;
			var pocolorid=document.getElementById('pocolorid_'+i).value;
			var rowCount = $('#tbl_consmption_cost tbody tr').length;
			var copy_basis=$('input[name="copy_basis"]:checked').val()

			for(var j=i; j<=rowCount; j++)
			{
				if(field_id=='des_' || field_id=='brndsup_' || field_id=='itemcolor_' || field_id=='itemsizes_' || field_id=='qty_' || field_id=='excess_' || field_id=='rate_' || field_id=='itemref_'){
					if(copy_basis==0) document.getElementById(field_id+j).value=value;
					if(copy_basis==1)
					{
						if(field_id=='itemcolor_')
						{
							if( pocolorid==document.getElementById('pocolorid_'+j).value) document.getElementById(field_id+j).value=value;
						}
						else
						{
							if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value) document.getElementById(field_id+j).value=value;
						}
					}
					if(copy_basis==2){
						if( pocolorid==document.getElementById('pocolorid_'+j).value) document.getElementById(field_id+j).value=value;
					}
					if(copy_basis==3){
						if( itemsizes==document.getElementById('itemsizes_'+j).value) document.getElementById(field_id+j).value=value;
					}
					if(field_id=='qty_' || field_id=='excess_')
					{
						calculate_requirement(j);
						if(field_id=='qty_') set_sum_value( 'qty_sum', 'qty_' );
					}
					if(field_id=='rate_') calculate_amount(j);
				}
			}
		}

		function calculate_amount(i)
		{
			var rate=(document.getElementById('rate_'+i).value)*1;
			var woqny=(document.getElementById('woqny_'+i).value)*1;
			var amount=number_format_common((rate*woqny),5,0);
			document.getElementById('amount_'+i).value=amount;
			set_sum_value( 'amount_sum', 'amount_' );
			calculate_avg_rate();
		}

		function calculate_avg_rate()
		{
			var woqty_sum=document.getElementById('woqty_sum').value;
			var amount_sum=document.getElementById('amount_sum').value;
			//var avg_rate=number_format_common((amount_sum/woqty_sum),5,0);
			var avg_rate=(amount_sum/woqty_sum);
			document.getElementById('rate_sum').value=avg_rate;
		}

		function js_set_value()
		{
			var reg=/[^a-zA-Z0-9!@#$%^,;.:<>{}?\+|\[\]\- \/]/g;
			var row_num=$('#tbl_consmption_cost tbody tr').length;
			var cons_breck_down="";
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

				var pocolorid=$('#pocolorid_'+i).val();					if($('#pocolorid_'+i).val()=='') pocolorid=0;
				var gmtssizesid=$('#gmtssizesid_'+i).val(); 			if(gmtssizesid=='') gmtssizesid=0;
				var des=trim($('#des_'+i).val()); 						if(des=='') des=0;
				var brndsup=trim($('#brndsup_'+i).val()); 				if(brndsup=='') brndsup=0;
				var itemcolor=$('#itemcolor_'+i).val(); 				if(itemcolor=='') itemcolor=0;
				
				var preitemcolor=$('#preitemcolor_'+i).val(); 				if(preitemcolor=='') preitemcolor=0;
				var preitemsizes=$('#preitemsizes_'+i).val(); 				if(preitemsizes=='') preitemsizes=0;
				
				var itemref=$('#itemref_'+i).val(); 					if(itemref=='') itemref=0;
				var itemsizes=$('#itemsizes_'+i).val(); 				if(itemsizes=='') itemsizes=0;
				var qty=$('#qty_'+i).val(); 							if(qty=='') qty=0;
				var excess=$('#excess_'+i).val(); 						if(excess=='') excess=0;
				var woqny=$('#woqny_'+i).val(); 						if(woqny=='') woqny=0;
				var rate=$('#rate_'+i).val(); 							if(rate=='') rate=0;
				var amount=$('#amount_'+i).val(); 						if(amount=='') amount=0;
				var pcs=$('#pcs_'+i).val(); 							if(pcs=='') pcs=0;
				var colorsizetableid=$('#colorsizetableid_'+i).val(); 	if(colorsizetableid=='')colorsizetableid=0;
				var updateid=$('#updateid_'+i).val(); 					if(updateid=='') updateid=0;
				var reqqty=$('#reqqty_'+i).val(); 						if(reqqty=='') reqqty=0;
				var remarks=$('#remarks_'+i).val(); 					if(remarks=='') remarks=0;
				var poarticle=$('#poarticle_'+i).val(); 				if(poarticle=='') poarticle='no article';

				if(cons_breck_down==""){
					cons_breck_down+=pocolorid+'_'+gmtssizesid+'_'+des+'_'+brndsup+'_'+itemcolor+'_'+itemsizes+'_'+qty+'_'+excess+'_'+woqny+'_'+rate+'_'+amount+'_'+pcs+'_'+colorsizetableid+'_'+reqqty+'_'+poarticle+'_'+itemref+'_'+remarks+'_'+preitemcolor+'_'+preitemsizes;
				}
				else{
					cons_breck_down+="__"+pocolorid+'_'+gmtssizesid+'_'+des+'_'+brndsup+'_'+itemcolor+'_'+itemsizes+'_'+qty+'_'+excess+'_'+woqny+'_'+rate+'_'+amount+'_'+pcs+'_'+colorsizetableid+'_'+reqqty+'_'+poarticle+'_'+itemref+'_'+remarks+'_'+preitemcolor+'_'+preitemsizes;
				}
			}
			document.getElementById('cons_breck_down').value=cons_breck_down;
			parent.emailwindow.hide();
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
       // extract(check_magic_quote_gpc($_REQUEST));
        if($txt_job_no==""){
			$txt_job_no_cond=""; $txt_job_no_cond1="";
        }
        else{
			$txt_job_no_cond ="and a.job_no='$txt_job_no'"; $txt_job_no_cond1 ="and job_no='$txt_job_no'";
        }
        if($txt_country=="") $txt_country_cond=""; else $txt_country_cond ="and c.country_id in ($txt_country)";

        $process_loss_method=return_field_value("process_loss_method", "variable_order_tracking", "company_name=$cbo_company_name  and variable_list=18 and item_category_id=4 and status_active=1 and is_deleted=0");
        $tot_po_qty=0;
        $sql_po_qty=sql_select("select b.id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and  b.id in($txt_po_id)  $txt_country_cond  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty"); //,c.item_number_id
        foreach($sql_po_qty as $sql_po_qty_row){
			$po_qty_arr[$sql_po_qty_row[csf('id')]]=$sql_po_qty_row[csf('order_quantity_set')];
			$tot_po_qty+=$sql_po_qty_row[csf('order_quantity_set')];
        }
		
		 $sql_pre=sql_select("select d.wo_pre_cost_trim_cost_dtls_id as trims_dtls_id,d.color_size_table_id,d.item_ref,d.size_number_id,d.color_number_id,d.item_color_number_id as item_color  from  wo_pre_cost_trim_co_cons_dtls d where   d.po_break_down_id in($txt_po_id)   and d.status_active=1  "); //,c.item_number_id
		// echo "select d.color_size_table_id,d.item_ref  from  wo_pre_cost_trim_co_cons_dtls d where   d.po_break_down_id in($txt_po_id)   and d.status_active=1  ";
		//echo "select d.wo_pre_cost_trim_cost_dtls_id as trims_dtls_id,d.color_size_table_id,d.item_ref,d.size_number_id,d.color_number_id,d.item_color_number_id as item_color  from  wo_pre_cost_trim_co_cons_dtls d where   d.po_break_down_id in($txt_po_id)   and d.status_active=1  ";
        foreach($sql_pre as $row){
			if($row[csf('item_ref')]!='')
			{
			$item_ref_color_size_arr[$row[csf('trims_dtls_id')]][$row[csf('color_size_table_id')]]['item_ref']=$row[csf('item_ref')];
			$item_ref_color_arr[$row[csf('color_number_id')]]['item_ref'].=$row[csf('item_ref')].',';
			$item_ref_size_arr[$row[csf('size_number_id')]]['item_ref'].=$row[csf('item_ref')].',';
			}
			 
        }
		
        ?>
        <div align="center" style="width:1250px;" >
            <fieldset>
                <form id="consumptionform_1" autocomplete="off">
                    <table width="1250" cellspacing="0" class="rpt_table" border="0" id="tbl_consmption_cost" rules="all">
                        <thead>
                        	<tr>
                                <th colspan="16" id="td_sync_msg" style="color:#FF0000"></th>
                            </tr>
                            <tr>
                                <th colspan="16">
                                    <input type="hidden" id="cons_breck_down" name="cons_breck_down" value="" />
                                    <input type="hidden" id="txtwoq" value="<?=$txt_req_quantity;?>"/>
                                    Wo Qty:<input type="text" id="txtwoq_qty" class="text_boxes_numeric" onBlur="poportionate_qty(this.value);" value="<?=$txtwoq; ?>"/>
                                    <input type="radio" name="copy_basis" value="0" <? if(!$txt_update_dtls_id) { echo "checked";} ?>>Copy to All
                                    <input type="radio" name="copy_basis" value="1">Gmts Size Wise
                                    <input type="radio" name="copy_basis" value="2">Gmts Color Wise
                                    <input type="radio" name="copy_basis" value="3">Item Size Wise
                                    <input type="radio" name="copy_basis" value="10" <? if($txt_update_dtls_id) { echo "checked";} ?>>No Copy
									<input type="checkbox" name="round_down" id="round_down" value="" onClick="poportionate_qty();" >Round Down

                                    <input type="hidden" id="process_loss_method_id" name="process_loss_method_id" value="<?=$process_loss_method; ?>"/>
                                    <input type="hidden" id="po_qty" name="po_qty" value="<?=$tot_po_qty; ?>"/>
                                </th>
                            </tr>
                            <tr>
                                <th width="30">SL</th>
                                <th width="80">Article No</th>
                                <th width="90">Gmts. Color</th>
                                <th width="70">Gmts. Size</th>
                                <th width="100">Description</th>
                                <th width="100">Brand/Sup Ref</th>
                                <th width="100">Item Color</th>
                                <th width="100">Item Ref</th>
                                <th width="70">Item Size</th>
                                <th width="70">Wo Qty <input type="checkbox" id="chk_qty" name="chk_qty" value="0" onClick="fnc_qty_blank();"></th>
                                <th width="40">Excess %</th>
                                <th width="70">WO Qty.</th>
                                <th width="100">Rate</th>
                                <th width="100">Amount</th>
                                <th>RMG Qty</th>
								<th width="200">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?
						//echo $txt_trim_group_id.'GGGGGGGGG';
                        $sql_lib_item_group_array=array();$conversion_factor=1;
                        $sql_lib_item_group=sql_select("select id, item_name,conversion_factor,order_uom as cons_uom from lib_item_group where id=$txt_trim_group_id");
                        foreach($sql_lib_item_group as $lrow){
							$sql_lib_item_group_array[$lrow[csf('id')]][item_name]=$lrow[csf('item_name')];
							$sql_lib_item_group_array[$lrow[csf('id')]][conversion_factor]=$lrow[csf('conversion_factor')];
							$sql_lib_item_group_array[$lrow[csf('id')]][cons_uom]=$lrow[csf('cons_uom')];
							$conversion_factor=$lrow[csf('conversion_factor')];
                        }
						//echo $conversion_factor.'=';
						unset($sql_lib_item_group);

                        $booking_data_arr=array();
						if($txt_update_dtls_id=="") $txt_update_dtls_id=0;
                        $booking_data=sql_select("select id, wo_trim_booking_dtls_id, description, brand_supplier, item_color, item_size, cons, process_loss_percent, requirment, rate, amount, pcs, color_size_table_id,item_ref,remarks,bom_item_color,bom_item_size from wo_trim_book_con_dtls where wo_trim_booking_dtls_id in($txt_update_dtls_id) and status_active=1 and is_deleted=0");
						//echo "select id, wo_trim_booking_dtls_id, description, brand_supplier, item_color, item_size, cons, process_loss_percent, requirment, rate, amount, pcs, color_size_table_id,item_ref,remarks,bom_item_color,bom_item_size from wo_trim_book_con_dtls where wo_trim_booking_dtls_id in($txt_update_dtls_id) and status_active=1 and is_deleted=0";

                        foreach($booking_data as $row){
							$booking_data_arr[$row[csf('color_size_table_id')]][id]=$row[csf('id')];
							$booking_data_arr[$row[csf('color_size_table_id')]][description]=$row[csf('description')];
							$booking_data_arr[$row[csf('color_size_table_id')]][brand_supplier]=$row[csf('brand_supplier')];
							$booking_data_arr[$row[csf('color_size_table_id')]][item_color]=$row[csf('item_color')];
							$booking_data_arr[$row[csf('color_size_table_id')]][item_ref]=$row[csf('item_ref')];
							$booking_data_arr[$row[csf('color_size_table_id')]][item_size]=$row[csf('item_size')];
							$booking_data_arr[$row[csf('color_size_table_id')]][bom_item_color]=$row[csf('bom_item_color')];
							$booking_data_arr[$row[csf('color_size_table_id')]][bom_item_size]=$row[csf('bom_item_size')];
							$booking_data_arr[$row[csf('color_size_table_id')]][remarks]=$row[csf('remarks')];
							$booking_data_arr[$row[csf('color_size_table_id')]][cons]+=$row[csf('cons')];
							$booking_data_arr[$row[csf('color_size_table_id')]][process_loss_percent]=$row[csf('process_loss_percent')];
							$booking_data_arr[$row[csf('color_size_table_id')]][requirment]+=$row[csf('requirment')];
							$booking_data_arr[$row[csf('color_size_table_id')]][rate]=$row[csf('rate')];
							$booking_data_arr[$row[csf('color_size_table_id')]][amount]+=$row[csf('amount')];
                        }
						unset($booking_data);

					$cbo_item_from=str_replace("'","",$cbo_item_from);
					$txt_trim_group_id=str_replace("'","",$txt_trim_group_id);

					if($cbo_item_from==1) //Item From: Pre Cost ***********
					{
                        $condition= new condition();
                        if(str_replace("'","",$txt_po_id) !=''){
							$condition->po_id("in($txt_po_id)");
                        }

                        $condition->init();
                        $trims= new trims($condition);
						//echo $trims->getQuery(); die;
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
						
                        $gmt_color_edb=""; $item_color_edb=""; $gmt_size_edb=""; $item_size_edb="";
                        if($cbo_colorsizesensitive==1){
							$req_qty_arr=$trims->getQtyArray_by_orderPrecostdtlsidAndGmtscolor();
							$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidAndGmtscolor();
							$sql="SELECT b.id, b.po_number, b.po_quantity, min(c.id) as color_size_table_id, c.color_number_id, min(c.color_order) as color_order, sum(c.order_quantity) as order_quantity, (sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where  a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id order by b.id, color_order";
							$gmt_size_edb=1; $item_size_edb=1;
                        }
                        else if($cbo_colorsizesensitive==2){

							//$req_qty_arr=$trims->getQtyArray_by_orderPrecostdtlsidAndGmtssize();
							//$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidAndGmtssize();

							$req_qty_arr=$trims->getQtyArray_by_orderPrecostdtlsidGmtssizeAndArticle();
							$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidGmtssizeAndArticle();
						//print_r($req_amount_arr);

							$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.size_number_id,c.article_number,min(c.size_order) as size_order,min(e.item_size) as item_size,sum(c.order_quantity) as order_quantity ,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.size_number_id,c.article_number order by b.id,size_order";
							$gmt_color_edb=1; $item_color_edb=1;
                        }
                        else if($cbo_colorsizesensitive==3){
							$req_qty_arr=$trims->getQtyArray_by_orderPrecostdtlsidAndGmtscolor();
							$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidAndGmtscolor();
							$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.color_number_id,min(c.color_order) as color_order,sum(c.order_quantity) as order_quantity ,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set,min(e.item_color_number_id) as item_color_number_id from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id order by b.id, color_order";
							$gmt_size_edb=1; $item_size_edb=1;
                        }
                        else if($cbo_colorsizesensitive==4){

							//$req_qty_arr=$trims->getQtyArray_by_OrderPrecostdtlsidGmtscolorAndGmtssize();
							//$req_amount_arr=$trims->getAmountArray_by_OrderPrecostdtlsidGmtscolorAndGmtssize();
							//$req_qty_arr=$trims->getQtyArray_by_OrderPrecostdtlsidGmtscolorGmtssizeAndArticle();
							//$req_amount_arr=$trims->getAmountArray_by_OrderPrecostdtlsidGmtscolorGmtssizeAndArticle();


							$req_qty_arr=$trims->getQtyArray_by_OrderPrecostdtlsidGmtscolorGmtssizeAndArticleItemColorItemSize();
							$req_amount_arr=$trims->getAmountArray_by_OrderPrecostdtlsidGmtscolorGmtssizeAndArticleItemColorItemSize();
							//echo $trims->getQuery();

						 $sql="select b.id, b.po_number, b.po_quantity, min(c.id) as color_size_table_id, c.color_number_id, c.size_number_id, c.article_number, min(c.color_order) as color_order, min(c.size_order) as size_order, e.item_size as item_size, sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set,e.item_color_number_id from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id,c.size_number_id,c.article_number,e.item_color_number_id,e.item_size  order by b.id, color_order,size_order";
                        }
                        else{
							$req_qty_arr=$trims->getQtyArray_by_orderAndPrecostdtlsid();
							$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsid();
							 $sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty order by b.id";
                        }

                        $po_color_level_data_arr=array(); $po_size_level_data_arr=array(); $po_no_sen_level_data_arr=array(); $po_color_size_level_data_arr=array();
						
						
                        $data_array=sql_select($sql);
						//echo $sql;
                        if ( count($data_array)>0)
						{
							$i=0;
							foreach( $data_array as $row ){
								$data=explode('_',$data_array_cons[$i]);
								$i++;
								$item_color = $booking_data_arr[$row[csf('color_size_table_id')]][item_color];
								if($item_color==0 || $item_color=="" ) $item_color = $row[csf('color_number_id')];

								$item_size=$booking_data_arr[$row[csf('color_size_table_id')]][item_size];
								if($item_size=='0' || $item_size == "") $item_size=$row[csf('item_size')];

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
									$txtreq_amount=$req_amount_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]];

									$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
									$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['req_amt'][$row[csf('id')]]=$txtreq_amount;
									$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['po_qty'][$row[csf('id')]]=$po_qty;
									$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
									$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['po_id'][$row[csf('id')]]=$row[csf('id')];
									$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];
									$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['color_size_table_id'][$row[csf('id')]]=$row[csf('color_size_table_id')];
									$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['item_color_number_id'][$row[csf('id')]]=$row[csf('item_color_number_id')];
									

									$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_cons'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][cons];
									$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_qty'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][requirment];
									$po_color_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_amt'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][amount];
								}
								else if($cbo_colorsizesensitive==2)
								{
									$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]];
									$txtreq_amount=$req_amount_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]];
									//echo $txtreq_amount.'='.$txt_req_quantity.', ';
									$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
									$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");
									$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
									$po_size_level_data_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['req_amt'][$row[csf('id')]]=$txtreq_amount;
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

									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['req_qty'][$row[csf('id')]]+=$txtwoq_cal;
									$po_color_size_level_data_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['req_amt'][$row[csf('id')]]+=$txtreq_amount;
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
									$txtreq_amount=$req_amount_arr[$row[csf('id')]][$cbo_trim_precost_id];
									$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
									$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");

									$po_no_sen_level_data_arr[$cbo_trim_precost_id]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
									$po_no_sen_level_data_arr[$cbo_trim_precost_id]['req_amt'][$row[csf('id')]]=$txtreq_amount;
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

                        if ( count($data_array)>0 && $cbo_level==1)
						{
							$i=0;
							foreach( $data_array as $row )
							{
								$data=explode('_',$data_array_cons[$i]);

								if($cbo_colorsizesensitive==1 || $cbo_colorsizesensitive==3 ){
									$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]];
									$txtwoq_amt_cal=$req_amount_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]];
									$item_size="";
									//$pre_item_size="";
									$item_color = $booking_data_arr[$row[csf('color_size_table_id')]][item_color];
									$booking_item_color = $booking_data_arr[$row[csf('color_size_table_id')]][bom_item_color];
									if(empty($item_color)) $item_color = $row[csf('item_color_number_id')];
										
									if($item_color>0) $booking_item_color=$item_color;
									else $booking_item_color = $row[csf('item_color_number_id')];
									
									if(($row[csf('item_color_number_id')]=="" || $row[csf('item_color_number_id')]=="0") && ($booking_item_color=='0' || $booking_item_color=="") ) $booking_item_color = $row[csf('color_number_id')];
									$item_color_id=$row[csf('item_color_number_id')];
									
									//$pre_item_color = $row[csf('item_color_number_id')];
								}
								else if($cbo_colorsizesensitive==2){
									$txt_req_quantity = $req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]];
									$txtwoq_amt_cal = $req_amount_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]];
									
									$item_color ="";$pre_item_color ="";
									$item_size=$booking_data_arr[$row[csf('color_size_table_id')]][item_size];
									
									 if($item_size!="") $booking_item_size=$item_size;
									if($item_size=="")  $booking_item_size=$row[csf('item_size')];
									
									if($booking_item_size=='0' || $booking_item_size == "") $item_size=$row[csf('item_size')];
									if($booking_item_size=='0' || $booking_item_size == "") $item_size=$size_library[$row[csf('size_number_id')]];
									
									//$pre_item_size=$row[csf('item_size')];
								}
								else if($cbo_colorsizesensitive==4){
									
									
									$item_color_id=$row[csf('item_color_number_id')];
									$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]];
									$txtwoq_amt_cal=$req_amount_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]];
									$item_color = $booking_data_arr[$row[csf('color_size_table_id')]][item_color];
									//$booking_item_color = $booking_data_arr[$row[csf('color_size_table_id')]][bom_item_color];
									//$booking_item_size = $booking_data_arr[$row[csf('color_size_table_id')]][bom_item_size];
									if(empty($item_color))
									{
										$item_color = $row[csf('item_color_number_id')];
										 
									}
									if($item_color>0) $booking_item_color=$item_color;
									else $booking_item_color = $row[csf('item_color_number_id')];
									
									if(($row[csf('item_color_number_id')]=="" || $row[csf('item_color_number_id')]=="0") && ($booking_item_color=='0' || $booking_item_color=="") ) $booking_item_color = $row[csf('color_number_id')];
									
									$item_size=$booking_data_arr[$row[csf('color_size_table_id')]][item_size];
									 if($item_size!="") $booking_item_size=$item_size;
									if($item_size=="")  $booking_item_size=$row[csf('item_size')];
									
									if($booking_item_size=='0' || $booking_item_size == "") $item_size=$row[csf('item_size')];
									if($booking_item_size=='0' || $booking_item_size == "") $item_size=$size_library[$row[csf('size_number_id')]];
									
									//$pre_item_size=$row[csf('item_size')];
								}
								else if($cbo_colorsizesensitive==0){
									$txt_req_quantity = $req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id];
									$txtwoq_amt_cal = $req_amount_arr[$row[csf('id')]][$cbo_trim_precost_id];
									
									$item_color = $booking_data_arr[$row[csf('color_size_table_id')]][item_color];
									//$booking_item_color = $booking_data_arr[$row[csf('color_size_table_id')]][bom_item_color];
									if($item_color>0) $booking_item_color=$item_color;
									else $booking_item_color = $row[csf('item_color_number_id')];
									
									if(($row[csf('item_color_number_id')]=="" || $row[csf('item_color_number_id')]=="0") && ($booking_item_color=='0' || $booking_item_color=="") ) $booking_item_color = $row[csf('color_number_id')];
									
									$item_size=$booking_data_arr[$row[csf('color_size_table_id')]][item_size];
									//$booking_item_size = $booking_data_arr[$row[csf('color_size_table_id')]][bom_item_size];
									if($item_size=='0' || $item_size == "") $item_size=$row[csf('item_size')];
									if($item_size=='0' || $item_size == "") $item_size=$size_library[$row[csf('size_number_id')]];
									
									if($booking_item_size=='0' || $booking_item_size == "") $booking_item_size=$row[csf('item_size')];
									if($booking_item_size=='0' || $booking_item_size == "") $booking_item_size=$size_library[$row[csf('size_number_id')]];
									
									//$pre_item_size=$row[csf('item_size')];
								}
								$req_qnty_ord_uom = def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
								$txtwoq_cal = def_number_format($req_qnty_ord_uom,5,"");

								$rate=$booking_data_arr[$row[csf('color_size_table_id')]][rate];
								if($rate==0 || $rate=="") $rate=($txtwoq_amt_cal/$txt_req_quantity)*$conversion_factor;//$rate=$txt_avg_price; 20-2047 by Aziz
								
								//$conversion_factor;
								//echo $txtwoq_amt_cal.'='.$txt_req_quantity.'f=';

								$description=$booking_data_arr[$row[csf('color_size_table_id')]][description];
								if($description=="") $description=trim($txt_pre_des);

								$brand_supplier=$booking_data_arr[$row[csf('color_size_table_id')]][brand_supplier];
								if($brand_supplier=="")$brand_supplier=trim($txt_pre_brand_sup);
								
								$pre_item_ref=$item_ref_color_size_arr[$cbo_trim_precost_id][$row[csf('color_size_table_id')]]['item_ref'];
								//echo $pre_item_ref.'='.$row[csf('color_size_table_id')].',';
								$item_ref = $booking_data_arr[$row[csf('color_size_table_id')]][item_ref];
								if($item_ref) $item_ref=$item_ref;else $item_ref=$pre_item_ref;
								$remarks = $booking_data_arr[$row[csf('color_size_table_id')]][remarks];

								if($txtwoq_cal>0)
								{
									$i++;
									?>
                                    <tr id="break_<? echo $i;?>" align="center">
                                        <td><? echo $i;?></td>
                                        <td><input type="text" id="poarticle_<? echo $i;?>" name="poarticle_<? echo $i;?>" class="text_boxes" style="width:70px" value="<? echo $row[csf('article_number')]; ?>"  readonly /></td>
                                        <td>
                                            <input type="text" id="pocolor_<? echo $i;?>" name="pocolor_<? echo $i;?>" class="text_boxes" style="width:80px" value="<? echo $color_library[$row[csf('color_number_id')]]; ?>" <? if($gmt_color_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> readonly/>
                                            <input type="hidden" id="pocolorid_<? echo $i;?>" name="pocolorid_<? echo $i;?>" class="text_boxes" style="width:70px" value="<? echo $row[csf('color_number_id')]; ?>" readonly />
                                            <input type="hidden" id="poid_<? echo $i;?>" name="poid_<? echo $i;?>" class="text_boxes" style="width:70px" value="<? echo $row[csf('id')]; ?>" />
                                            <input type="hidden" id="poqty_<? echo $i;?>" name="poqty_<? echo $i;?>" class="text_boxes" style="width:70px" value="<? echo $po_qty_arr[$row[csf('id')]]; ?>" readonly />
                                            <input type="hidden" id="poreqqty_<? echo $i;?>" name="poreqqty_<? echo $i;?>" class="text_boxes" style="width:70px" value="<? echo $txtwoq_cal; ?>" readonly />
                                        </td>
                                        <td>
                                            <input type="text" id="gmtssizes_<? echo $i;?>"  name="gmtssizes_<? echo $i;?>" class="text_boxes" style="width:60px" value="<? echo $size_library[$row[csf('size_number_id')]]; ?>" <? if($gmt_size_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> readonly/>
                                            <input type="hidden" id="gmtssizesid_<? echo $i;?>"  name="gmtssizesid_<? echo $i;?>" class="text_boxes" style="width:50px" value="<? echo $row[csf('size_number_id')]; ?>" readonly />
                                        </td>
                                        <td><input type="text" id="des_<? echo $i;?>"  name="des_<? echo $i;?>" class="text_boxes" style="width:90px" value="<? echo $description;?>" onChange="copy_value(this.value,'des_',<? echo $i;?>)" <? if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> /></td>
                                        <td><input type="text" id="brndsup_<? echo $i;?>"  name="brndsup_<? echo $i;?>" class="text_boxes" style="width:90px" value="<? echo $brand_supplier; ?>" onChange="copy_value(this.value,'brndsup_',<? echo $i;?>)" <? if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?>/></td>
                                        <td><input type="hidden" id="preitemcolor_<? echo $i;?>" value="<? echo $item_color_id; ?>" name="preitemcolor_<? echo $i;?>" class="text_boxes" style="width:90px"  />
                                        <input type="text" id="itemcolor_<? echo $i;?>" value="<? echo $color_library[$booking_item_color]; ?>" name="itemcolor_<? echo $i;?>" class="text_boxes" style="width:90px" onChange="copy_value(this.value,'itemcolor_',<? echo $i;?>)" <? if($item_color_edb || $piNumber || $recvNumber ){ echo "disabled";}else { echo "";} ?> />
                                        </td>
                                        <td><input type="text" id="itemref_<? echo $i;?>" value="<? echo $item_ref; ?>" name="itemref_<? echo $i;?>" class="text_boxes" style="width:90px" onChange="copy_value(this.value,'itemref_',<? echo $i;?>)" <? if($piNumber || $recvNumber ){ echo "disabled";}else { echo "";} ?> /></td>
                                        
                                        <td><input type="hidden" id="preitemsizes_<? echo $i;?>"  name="preitemsizes_<? echo $i;?>"    class="text_boxes" style="width:60px"   value="<? echo $row[csf('item_size')]; ?>" />
                                        <input type="text" id="itemsizes_<? echo $i;?>"  name="itemsizes_<? echo $i;?>"    class="text_boxes" style="width:60px" onChange="copy_value(this.value,'itemsizes_',<? echo $i;?>)" value="<? echo $booking_item_size; ?>" <? if($item_size_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> /></td>
                                        <td><input type="hidden" id="reqqty_<? echo $i;?>"  name="reqqty_<? echo $i;?>" class="text_boxes_numeric" style="width:60px"    value="<? echo $txtwoq_cal ?>" readonly/>
                                        	<input type="text" id="qty_<? echo $i;?>" onBlur="validate_sum( <? echo $i; ?> )" onChange="set_sum_value( 'qty_sum', 'qty_' );set_sum_value( 'woqty_sum', 'woqny_' );calculate_requirement(<? echo $i;?>);copy_value(this.value,'qty_',<? echo $i;?>)"  name="qty_<? echo $i;?>" class="text_boxes_numeric" style="width:60px"   placeholder="<? echo $txtwoq_cal; ?>" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][cons]; ?>"/>
                                        </td>
                                        <td><input type="text" id="excess_<? echo $i;?>" onBlur="set_sum_value( 'excess_sum', 'excess_' ) "  name="excess_<? echo $i;?>" class="text_boxes_numeric" style="width:30px" onChange="calculate_requirement(<? echo $i;?>);set_sum_value( 'excess_sum', 'excess_' );set_sum_value( 'woqty_sum', 'woqny_' );copy_value(this.value,'excess_',<? echo $i;?>) " value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][process_loss_percent]; ?>" disabled/></td>
                                        <td><input type="text" id="woqny_<? echo $i;?>" onBlur="set_sum_value('woqty_sum', 'woqny_')" onChange="set_sum_value('woqty_sum', 'woqny_')" name="woqny_<? echo $i;?>" class="text_boxes_numeric" style="width:60px" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][requirment]; ?>" readonly /></td>
                                        <td><input type="text" id="rate_<? echo $i;?>"  name="rate_<? echo $i;?>" class="text_boxes_numeric" style="width:90px" onChange="calculate_amount(<? echo $i;?>);set_sum_value( 'amount_sum', 'amount_' );copy_value(this.value,'rate_',<? echo $i;?>) " value="<? echo $rate; ?>" <? if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> /></td>
                                        <td><input type="text" id="amount_<? echo $i;?>" name="amount_<? echo $i;?>" onBlur="set_sum_value( 'amount_sum', 'amount_' )" class="text_boxes_numeric" style="width:90px" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][amount]; ?>" readonly></td>
                                        <td>
                                            <input type="text" id="pcs_<? echo $i;?>"  name="pcs_<? echo $i;?>"  onBlur="set_sum_value( 'pcs_sum', 'pcs_' ) " class="text_boxes_numeric" style="width:50px"  value="<? echo $row[csf('order_quantity')]; ?>" readonly>
                                            <input type="hidden" id="pcsset_<? echo $i;?>"  name="pcsset_<? echo $i;?>"  onBlur="set_sum_value( 'pcs_sum', 'pcs_' ) " class="text_boxes_numeric" style="width:50px"  value="<? echo $row[csf('order_quantity_set')]; ?>" readonly>
                                            <input type="hidden" id="colorsizetableid_<? echo $i;?>"  name="colorsizetableid_<? echo $i;?>" class="text_boxes" style="width:45px" value="<? echo $row[csf('color_size_table_id')]; ?>" />
                                            <input type="hidden" id="updateid_<? echo $i;?>"  name="updateid_<? echo $i;?>" class="text_boxes" style="width:45px" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][id]; ?>" readonly />
                                        </td>
										<td><input type="text" id="remarks_<? echo $i;?>"  name="remarks_<? echo $i;?>" class="text_boxes" style="width:90px" value="<? echo $remarks;?>" onChange="copy_value(this.value,'remarks_',<? echo $i;?>)" />
                                            
                                        </td>
                                    </tr>
								<?
								}
							}
                        }

                        $level_arr=array(); $gmt_color_edb="";  $item_color_edb="";  $gmt_size_edb="";  $item_size_edb="";
                        if($cbo_colorsizesensitive==1){
							$sql="select min(b.id) as id , min(c.id) as color_size_table_id,c.color_number_id,min(c.color_order) as color_order,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.color_number_id order by  color_order";
							$level_arr=$po_color_level_data_arr;
							$gmt_size_edb=1;
							$item_size_edb=1;
                        }
                        else if($cbo_colorsizesensitive==2){
							$sql="select min(b.id) as id , min(c.id) as color_size_table_id,c.size_number_id,c.article_number,min(c.size_order) as size_order,min(e.item_size) as item_size,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.size_number_id,c.article_number order by size_order";
							$level_arr=$po_size_level_data_arr;
							$gmt_color_edb=1; $item_color_edb=1;
                        }
                        else if($cbo_colorsizesensitive==3){
							$sql="SELECT min(b.id) as id, min(c.id) as color_size_table_id,c.color_number_id,min(c.color_order) as color_order,sum(c.order_quantity) as order_quantity, min(e.item_color_number_id) as item_color_number_id  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.color_number_id order by  color_order";
							$level_arr=$po_color_level_data_arr;
							$gmt_size_edb=1; $item_size_edb=1;
                        }
                        else if($cbo_colorsizesensitive==4){
						  $sql="select min(b.id) as id ,min(c.id) as color_size_table_id, c.color_number_id, c.size_number_id, c.article_number, min(c.color_order) as color_order, min(c.size_order) as size_order, e.item_size as item_size, e.item_color_number_id, sum(c.order_quantity) as order_quantity  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.color_number_id,c.size_number_id, c.article_number,e.item_color_number_id,e.item_size order by  color_order,size_order,c.article_number";
							$level_arr=$po_color_size_level_data_arr;
                        }
                        else{
							$sql="select b.job_no_mst,min(b.id) as id , min(c.id) as color_size_table_id,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.id=d.job_id and a.id=e.job_id and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.job_no_mst";
							$level_arr=$po_no_sen_level_data_arr;
                        }
						
                        $data_array=sql_select($sql);
                        if ( count($data_array)>0 && $cbo_level==2){
							$i=0;
							foreach( $data_array as $row ){

								if($cbo_colorsizesensitive==1){
									$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['req_qty']),5,"");
									$txtwoq_amt_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['req_amt']),5,"");;
									$po_qty=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['po_qty']);
									$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['order_quantity_set']);
									$booking_cons=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_cons']),5,"");
									$booking_qty=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_qty']),5,"");
									$booking_amt=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_amt']),5,"");
									
									$item_size="";$item_color_id="";
									//booking_item_size
									$item_color = $booking_data_arr[$row[csf('color_size_table_id')]][item_color];
									//$booking_item_color=$booking_data_arr[$row[csf('color_size_table_id')]][bom_item_color];
									if($item_color==0 || $item_color=="" ) $booking_item_color = $row[csf('color_number_id')];
									
									if($booking_item_color==0 || $booking_item_color=="" ) $booking_item_color = $row[csf('color_number_id')];
									if(($row[csf('item_color_number_id')]=="" || $row[csf('item_color_number_id')]=="0" || $booking_item_color=='0' || $booking_item_color=="") ) $booking_item_color = $row[csf('color_number_id')];
									else $booking_item_color = $row[csf('item_color_number_id')];
									//$pre_item_color = "";
								}
								if($cbo_colorsizesensitive==2){
									$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['req_qty']),5,"");
									$txtwoq_amt_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['req_amt']),5,"");
									//echo $txtwoq_amt_cal.'='.$txtwoq_cal.' , ';
									$po_qty=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_qty']);
									$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity_set']);
									$booking_cons=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_cons']),5,"");
									$booking_qty=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_qty']),5,"");
									$booking_amt=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_amt']),5,"");
									
									$item_size=$booking_data_arr[$row[csf('color_size_table_id')]][item_size];
									//$booking_item_size=$booking_data_arr[$row[csf('color_size_table_id')]][bom_item_size];
									
									 if($item_size!="") $booking_item_size=$item_size;
									if($item_size=="")  $booking_item_size=$row[csf('item_size')];
									
									if($booking_item_size=='0' || $booking_item_size == "") $item_size=$row[csf('item_size')];
									if($booking_item_size=='0' || $booking_item_size == "") $item_size=$size_library[$row[csf('size_number_id')]];
									
									if($booking_item_size=='0' || $booking_item_size == "") $booking_item_size=$row[csf('item_size')];
									if($booking_item_size=='0' || $booking_item_size == "") $booking_item_size=$size_library[$row[csf('size_number_id')]];
									
									$item_color = "";//$pre_item_color = "";$pre_item_size=$row[csf('item_size')];
								}
								if($cbo_colorsizesensitive==3){
									$item_color_id=$row[csf('item_color_number_id')];
									$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['req_qty']),5,"");
									$txtwoq_amt_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['req_amt']),5,"");
									$po_qty=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['po_qty']);
									$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['order_quantity_set']);
									$booking_cons=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_cons']),5,"");
									$booking_qty=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_qty']),5,"");
									$booking_amt=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]]['booking_amt']),5,"");
									
									$item_size="";//$pre_item_size = "";
									$item_color = $booking_data_arr[$row[csf('color_size_table_id')]][item_color];
									//$booking_item_color = $booking_data_arr[$row[csf('color_size_table_id')]][bom_item_color];
									if($item_color==0 || $item_color=="" ) $booking_item_color = $row[csf('item_color_number_id')];
								//	if($item_color==0 || $item_color=="" ) $item_color = $row[csf('color_number_id')];
									
									if($booking_item_color==0 || $booking_item_color=="" ) $booking_item_color = $row[csf('item_color_number_id')];
									if($booking_item_color==0 || $booking_item_color=="" ) $booking_item_color = $row[csf('color_number_id')];
									
									if(($row[csf('item_color_number_id')]=="" || $row[csf('item_color_number_id')]=="0") && ($booking_item_color=='0' || $booking_item_color=="") ) $booking_item_color = $row[csf('color_number_id')];
									else $booking_item_color = $row[csf('item_color_number_id')];
									
									//$pre_item_color = $row[csf('item_color_number_id')];
									
								}
								if($cbo_colorsizesensitive==4){
									$item_color_id=$row[csf('item_color_number_id')];
									if($row[csf('item_color_number_id')]=='' || $row[csf('item_color_number_id')]=='0' || $color_library[$row[csf('item_color_number_id')]]=='' || $color_library[$row[csf('item_color_number_id')]]=='0')  $row[csf('item_color_number_id')]=$row[csf('color_number_id')];
									
								//	if($row[csf('item_color_number_id')]=='' || $row[csf('item_color_number_id')]=='0' || $color_library[$row[csf('item_color_number_id')]]=='' || $color_library[$row[csf('item_color_number_id')]]=='0')  $row[csf('item_color_number_id')]=$row[csf('color_number_id')];
									
								//echo $row[csf('item_color_number_id')].'d';
									 
									$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['req_qty']),5,"");
									$txtwoq_amt_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['req_amt']),5,"");
									$po_qty=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['po_qty']);
									$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['order_quantity_set']);
									$booking_cons=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['booking_cons']),5,"");
									$booking_qty=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['booking_qty']),5,"");
									$booking_amt=def_number_format(array_sum($level_arr[$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['booking_amt']),5,"");
									//$item_color =0;
									$item_color = $booking_data_arr[$row[csf('color_size_table_id')]][item_color];
									//$booking_item_color = $booking_data_arr[$row[csf('color_size_table_id')]][bom_item_color];
									
									if($item_color>0) $booking_item_color=$item_color;
									else $booking_item_color = $row[csf('item_color_number_id')];
									
									if(($row[csf('item_color_number_id')]=="" || $row[csf('item_color_number_id')]=="0") && ($booking_item_color=='0' || $booking_item_color=="") ) $booking_item_color = $row[csf('color_number_id')];
									 
									$item_size=$booking_data_arr[$row[csf('color_size_table_id')]][item_size];
									// echo $row[csf('color_size_table_id')].'='.$item_size.'d';
									 if($item_size!="") $booking_item_size=$item_size;
									if($item_size=="")  $booking_item_size=$row[csf('item_size')];
									
									 
									//if($item_size=='0' || $item_size == "") $item_size=$size_library[$row[csf('size_number_id')]];
									 
									if($booking_item_size=='0' || $booking_item_size == "") $booking_item_size=$row[csf('item_size')];
									if($booking_item_size=='0' || $booking_item_size == "") $booking_item_size=$size_library[$row[csf('size_number_id')]];
									
									//$pre_item_color = $row[csf('item_color_number_id')];
									//$pre_item_size = $row[csf('item_size')];
								}
								if($cbo_colorsizesensitive==0){
									$txtwoq_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id]['req_qty']),5,"");
									$txtwoq_amt_cal =def_number_format(array_sum($level_arr[$cbo_trim_precost_id]['req_amt']),5,"");
									$po_qty=array_sum($level_arr[$cbo_trim_precost_id]['po_qty']);
									$order_quantity_set=array_sum($level_arr[$cbo_trim_precost_id]['order_quantity_set']);
									$booking_cons=def_number_format(array_sum($level_arr[$cbo_trim_precost_id]['booking_cons']),5,"");
									$booking_qty=def_number_format(array_sum($level_arr[$cbo_trim_precost_id]['booking_qty']),5,"");
									$booking_amt=def_number_format(array_sum($level_arr[$cbo_trim_precost_id]['booking_amt']),5,"");
									
									$item_color = $booking_data_arr[$row[csf('color_size_table_id')]][item_color];
									//$booking_item_color = $booking_data_arr[$row[csf('color_size_table_id')]][bom_item_color];
									if($item_color==0 || $item_color=="" ) $booking_item_color = $row[csf('color_number_id')];
									if($booking_item_color==0 || $booking_item_color=="" ) $booking_item_color = $row[csf('color_number_id')];
									if(($row[csf('item_color_number_id')]=="" || $row[csf('item_color_number_id')]=="0") && ($booking_item_color=='0' || $booking_item_color=="") ) $booking_item_color = $row[csf('color_number_id')];
									else $booking_item_color = $row[csf('item_color_number_id')];
									//echo $row[csf('color_number_id')].'d';
									//$pre_item_color = "";$pre_item_size="";
									//$item_color_id="";
								}

								$rate=$booking_data_arr[$row[csf('color_size_table_id')]][rate];
								if(($rate*1)==0)
								{
									if($booking_amt>0) $rate=$booking_amt/$booking_qty; else $rate=($txtwoq_amt_cal/$txtwoq_cal);
								}
								//echo "<pre>".$txtwoq_amt_cal."**".$txtwoq_cal."/".$txtwoq_cal."**".$txtwoq_amt_cal."__".$txtwoq_cal."</pre>";
								//$rate=$txt_avg_price; //20-2047 by aziz


								$description=$booking_data_arr[$row[csf('color_size_table_id')]][description];
								if($description=="") $description=trim($txt_pre_des);
								//echo $description.'='.$txt_pre_des.'<br/>';
								$brand_supplier=$booking_data_arr[$row[csf('color_size_table_id')]][brand_supplier];
								if($brand_supplier=="") $brand_supplier=trim($txt_pre_brand_sup);
								
								$pre_item_ref=$item_ref_color_size_arr[$cbo_trim_precost_id][$row[csf('color_size_table_id')]]['item_ref'];
								//echo $txtwoq_cal.'='.$row[csf('color_size_table_id')].',';
								$item_ref = $booking_data_arr[$row[csf('color_size_table_id')]][item_ref];
								if($item_ref) $item_ref=$item_ref;else $item_ref=$pre_item_ref;
								$remarks = $booking_data_arr[$row[csf('color_size_table_id')]][remarks];

								if($txtwoq_cal>0)
								{
									$i++;
									?>
									<tr id="break_<?=$i; ?>" align="center">
                                        <td><?=$i; ?></td>
                                        <td><input type="text" id="poarticle_<?=$i; ?>" name="poarticle_<?=$i; ?>" class="text_boxes" style="width:70px" value="<?=$row[csf('article_number')]; ?>" readonly />
                                        </td>
                                        <td>
                                            <input type="text" id="pocolor_<?=$i; ?>" name="pocolor_<?=$i; ?>" class="text_boxes" style="width:80px" value="<?=$color_library[$row[csf('color_number_id')]]; ?>" <? if($gmt_color_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> readonly />
                                            <input type="hidden" id="pocolorid_<?=$i; ?>" name="pocolorid_<?=$i; ?>" class="text_boxes" style="width:75px" value="<?=$row[csf('color_number_id')]; ?>" readonly />
                                            <input type="hidden" id="poid_<?=$i;?>" name="poid_<?=$i;?>" class="text_boxes" style="width:75px" value="<?=$row[csf('id')]; ?>" readonly />
                                            <input type="hidden" id="poqty_<?=$i;?>" name="poqty_<?=$i;?>" class="text_boxes" style="width:75px" value="<?=$po_qty; ?>" readonly />
                                            <input type="hidden" id="poreqqty_<?=$i;?>" name="poreqqty_<?=$i;?>" class="text_boxes" style="width:75px" value="<?=$txtwoq_cal; ?>" readonly />
                                        </td>
                                        <td>
                                            <input type="text" id="gmtssizes_<?=$i;?>" name="gmtssizes_<?=$i;?>" class="text_boxes" style="width:60px" value="<?=$size_library[$row[csf('size_number_id')]]; ?>" <? if($gmt_size_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> readonly/>
                                            <input type="hidden" id="gmtssizesid_<?=$i;?>" name="gmtssizesid_<?=$i;?>" class="text_boxes" style="width:60px" value="<?=$row[csf('size_number_id')]; ?>" readonly />
                                        </td>
                                        <td><input type="text" id="des_<?=$i;?>" name="des_<?=$i;?>" class="text_boxes" style="width:90px" value="<?=$description;?>" onChange="copy_value(this.value,'des_',<?=$i;?>)" <? if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> />
                                        </td>
                                        <td><input type="text" id="brndsup_<?=$i;?>" name="brndsup_<?=$i;?>" class="text_boxes" style="width:90px" value="<?=$brand_supplier; ?>" onChange="copy_value(this.value,'brndsup_',<?=$i;?>)" <? if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> />
                                        </td>
                                        <td><input type="hidden" id="preitemcolor_<?=$i;?>" value="<?=$row[csf('item_color_number_id')]; ?>" name="preitemcolor_<?=$i;?>" class="text_boxes" style="width:90px"  />
                                        <input type="text" id="itemcolor_<?=$i;?>" value="<?=$color_library[$booking_item_color]; ?>" name="itemcolor_<?=$i;?>" class="text_boxes" style="width:90px" onChange="copy_value(this.value,'itemcolor_',<?=$i;?>)" <? if($item_color_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> />
                                        </td>
                                         <td><input type="text" id="itemref_<?=$i;?>" value="<?=$item_ref; ?>" name="itemref_<?=$i;?>" class="text_boxes" style="width:90px" onChange="copy_value(this.value,'itemref_',<?=$i;?>);" <? if($item_color_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> /></td>
                                         
                                      <td><input type="hidden" id="preitemsizes_<?=$i;?>" name="preitemsizes_<?=$i;?>" class="text_boxes" style="width:60px"   value="<?=$row[csf('item_size')]; ?>" />
                                        
                                        <input type="text" id="itemsizes_<?=$i;?>" name="itemsizes_<?=$i;?>" class="text_boxes" style="width:60px" onChange="copy_value(this.value,'itemsizes_',<?=$i;?>);" value="<?=$booking_item_size; ?>"  <? if($item_size_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?>/>
                                        </td>
                                        <td><input type="hidden" id="reqqty_<?=$i;?>" name="reqqty_<?=$i;?>" class="text_boxes_numeric" style="width:60px" value="<?=$txtwoq_cal ?>" readonly/>
                                        	<input type="text" id="qty_<?=$i;?>" onBlur="validate_sum(<?=$i; ?>);" onChange="set_sum_value( 'qty_sum', 'qty_' );set_sum_value( 'woqty_sum', 'woqny_' );calculate_requirement(<?=$i;?>);copy_value(this.value,'qty_',<?=$i;?>);" name="qty_<?=$i;?>" class="text_boxes_numeric" style="width:60px" placeholder="<?=$txtwoq_cal; ?>" value="<? if($booking_cons>0){echo $booking_cons;} ?>"/>
                                        </td>
                                        <td>
                                        	<input type="text" id="excess_<?=$i;?>" onBlur="set_sum_value( 'excess_sum', 'excess_');" name="excess_<?=$i;?>" class="text_boxes_numeric" style="width:30px" onChange="calculate_requirement(<?=$i;?>);set_sum_value( 'excess_sum', 'excess_');set_sum_value( 'woqty_sum', 'woqny_'); copy_value(this.value,'excess_',<?=$i;?>);" value="<?=$booking_data_arr[$row[csf('color_size_table_id')]][process_loss_percent]; ?>" disabled/>
                                        </td>
                                        <td><input type="text" id="woqny_<?=$i;?>" onBlur="set_sum_value( 'woqty_sum', 'woqny_');" onChange="set_sum_value('woqty_sum', 'woqny_');"  name="woqny_<?=$i;?>" class="text_boxes_numeric" style="width:60px" value="<? if($booking_qty){echo $booking_qty;} ?>" readonly />
                                        </td>
                                        <td><input type="text" id="rate_<?=$i;?>" name="rate_<?=$i;?>" class="text_boxes_numeric" style="width:90px" onChange="calculate_amount(<?=$i;?>); set_sum_value('amount_sum', 'amount_'); copy_value(this.value,'rate_',<?=$i;?>);" value="<?=$rate; ?>" <? if( $piNumber || $recvNumber ){ echo "disabled";}else { echo "";} ?> />
                                        </td>
                                        <td><input type="text" id="amount_<?=$i;?>" name="amount_<?=$i;?>" onBlur="set_sum_value('amount_sum', 'amount_');" class="text_boxes_numeric" style="width:90px" value="<?=$booking_amt; //$booking_data_arr[$row[csf('color_size_table_id')]][amount]; ?>" readonly>
                                        </td>

                                        <td><input type="text" id="pcs_<?=$i;?>" name="pcs_<?=$i;?>"  onBlur="set_sum_value( 'pcs_sum', 'pcs_' );" class="text_boxes_numeric" style="width:50px"  value="<?=$row[csf('order_quantity')]; ?>" readonly>
                                            <input type="hidden" id="pcsset_<?=$i;?>" name="pcsset_<?=$i;?>"  onBlur="set_sum_value( 'pcs_sum', 'pcs_') " class="text_boxes_numeric" style="width:50px" value="<?=$order_quantity_set; ?>" readonly>
                                            <input type="hidden" id="colorsizetableid_<?=$i;?>" name="colorsizetableid_<?=$i;?>" class="text_boxes" style="width:45px" value="<?=$row[csf('color_size_table_id')]; ?>" />
                                            <input type="hidden" id="updateid_<?=$i;?>" name="updateid_<?=$i;?>" class="text_boxes" style="width:45px" value="<?=$booking_data_arr[$row[csf('color_size_table_id')]][id]; ?>" readonly />
                                        </td>
										<td><input type="text" id="remarks_<?=$i;?>" name="remarks_<?=$i;?>" class="text_boxes" style="width:90px" value="<?=$remarks;?>" onChange="copy_value(this.value,'remarks_',<?=$i;?>);" /></td>
                                        <?
										if($cbo_colorsizesensitive==0)
										{
										?>
                                        
                                        <?
										}
										?>
									</tr>
								<?
								}
							}
                        }
					}
					else 
					{
						
                         
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
						
                        $gmt_color_edb=""; $item_color_edb=""; $gmt_size_edb=""; $item_size_edb="";
                        if($cbo_colorsizesensitive==1){
							//$req_qty_arr=$trims->getQtyArray_by_orderPrecostdtlsidAndGmtscolor();
							//$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidAndGmtscolor();
							 $sql="SELECT a.id as job_id,b.id, b.po_number, b.po_quantity, min(c.id) as color_size_table_id, c.color_number_id, min(c.color_order) as color_order, sum(c.order_quantity) as order_quantity, (sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set,sum(c.order_total) as amount from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where  a.id=b.job_id and a.id=c.job_id  and b.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id)  $txt_country_cond   group by   a.id,b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id order by b.id, color_order";
							$gmt_size_edb=1; $item_size_edb=1;
                        }
                        else if($cbo_colorsizesensitive==2){

							//$req_qty_arr=$trims->getQtyArray_by_orderPrecostdtlsidGmtssizeAndArticle();
							//$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidGmtssizeAndArticle();
						//print_r($req_amount_arr);

							 $sql="select  a.id as job_id,b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.size_number_id, c.size_number_id as item_size,c.article_number,min(c.size_order) as size_order,sum(c.order_quantity) as order_quantity ,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set,sum(c.order_total) as amount from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c  where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id)  $txt_country_cond  group by   a.id,b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.size_number_id,c.article_number order by b.id,size_order";
							$gmt_color_edb=1; $item_color_edb=1;
                        }
                        else if($cbo_colorsizesensitive==3){
							//$req_qty_arr=$trims->getQtyArray_by_orderPrecostdtlsidAndGmtscolor();
							//$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidAndGmtscolor();
							$sql="select  a.id as job_id,b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.color_number_id,min(c.color_order) as color_order,sum(c.order_quantity) as order_quantity ,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set,sum(c.order_total) as amount from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.id=b.job_id and a.id=c.job_id  and b.id=c.po_break_down_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id)  $txt_country_cond  group by  a.id,b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id order by b.id, color_order";
							$gmt_size_edb=1; $item_size_edb=1;
                        }
                        else if($cbo_colorsizesensitive==4){

							//$req_qty_arr=$trims->getQtyArray_by_OrderPrecostdtlsidGmtscolorGmtssizeAndArticleItemColorItemSize();
							//$req_amount_arr=$trims->getAmountArray_by_OrderPrecostdtlsidGmtscolorGmtssizeAndArticleItemColorItemSize();
							//echo $trims->getQuery();

						  $sql="select  a.id as job_id,b.id, b.po_number, b.po_quantity, min(c.id) as color_size_table_id, c.color_number_id,c.color_number_id as item_color_number_id,c.size_number_id as item_size, c.size_number_id, c.article_number, min(c.color_order) as color_order, min(c.size_order) as size_order, sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set,sum(c.order_total) as amount from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.id=b.job_id and a.id=c.job_id  and b.id=c.po_break_down_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id)  $txt_country_cond group by  a.id,b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id,c.size_number_id,c.article_number order by b.id, color_order,size_order";
                        }
                        else{
							//$req_qty_arr=$trims->getQtyArray_by_orderAndPrecostdtlsid();
							//$req_amount_arr=$trims->getAmountArray_by_orderAndPrecostdtlsid();
							$sql="select  a.id as job_id,b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set,sum(c.order_total) as amount  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id)  $txt_country_cond group by a.id, b.id, b.po_number,b.po_quantity,a.total_set_qnty order by b.id";
                        }

                        $po_color_level_data_arr=array(); $po_size_level_data_arr=array(); $po_no_sen_level_data_arr=array(); $po_color_size_level_data_arr=array();
						
						
                        $data_array=sql_select($sql);
						//echo $sql;
						//echo $txt_trim_group_id.'dd';
                        if ( count($data_array)>0)
						{
							$i=0;
							foreach( $data_array as $row ){
								$data=explode('_',$data_array_cons[$i]);
								$i++;
								$job_id=$row[csf('job_id')];
								$item_color = $booking_data_arr[$row[csf('color_size_table_id')]][item_color];
								if($item_color==0 || $item_color=="" ) $item_color = $row[csf('color_number_id')];

								$item_size=$booking_data_arr[$row[csf('color_size_table_id')]][item_size];
								if($item_size=='0' || $item_size == "") $item_size=$row[csf('item_size')];

								$rate=$booking_data_arr[$row[csf('color_size_table_id')]][rate];
								if($rate==0 || $rate=="")$rate=$txt_avg_price;

								$description=$booking_data_arr[$row[csf('color_size_table_id')]][description];
								if($description=="") $description=trim($txt_pre_des);

								$brand_supplier=$booking_data_arr[$row[csf('color_size_table_id')]][brand_supplier];
								if($brand_supplier=="") $brand_supplier=trim($txt_pre_brand_sup);
								if($booking_data_arr[$row[csf('color_size_table_id')]][cons]=='') $booking_data_arr[$row[csf('color_size_table_id')]][cons]=0;
								if($booking_data_arr[$row[csf('color_size_table_id')]][requirment]=='') $booking_data_arr[$row[csf('color_size_table_id')]][requirment]=0;
								if($booking_data_arr[$row[csf('color_size_table_id')]][amount]=='') $booking_data_arr[$row[csf('color_size_table_id')]][amount]=0;
								$po_qty=$row[csf('order_quantity')];
								if($cbo_colorsizesensitive==1 || $cbo_colorsizesensitive==3 )
								{
									$txt_req_quantity=$row[csf('order_quantity')];
									$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
									$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");
									$txtreq_amount=$row[csf('amount')];

									$po_color_level_data_arr[$txt_trim_group_id][$row[csf('color_number_id')]]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
									$po_color_level_data_arr[$txt_trim_group_id][$row[csf('color_number_id')]]['req_amt'][$row[csf('id')]]=$txtreq_amount;
									$po_color_level_data_arr[$txt_trim_group_id][$row[csf('color_number_id')]]['po_qty'][$row[csf('id')]]=$po_qty;
									$po_color_level_data_arr[$txt_trim_group_id][$row[csf('color_number_id')]]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
									$po_color_level_data_arr[$txt_trim_group_id][$row[csf('color_number_id')]]['po_id'][$row[csf('id')]]=$row[csf('id')];
									$po_color_level_data_arr[$txt_trim_group_id][$row[csf('color_number_id')]]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];
									$po_color_level_data_arr[$txt_trim_group_id][$row[csf('color_number_id')]]['color_size_table_id'][$row[csf('id')]]=$row[csf('color_size_table_id')];
									$po_color_level_data_arr[$txt_trim_group_id][$row[csf('color_number_id')]]['item_color_number_id'][$row[csf('id')]]='';
									

									$po_color_level_data_arr[$txt_trim_group_id][$row[csf('color_number_id')]]['booking_cons'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][cons];
									$po_color_level_data_arr[$txt_trim_group_id][$row[csf('color_number_id')]]['booking_qty'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][requirment];
									$po_color_level_data_arr[$txt_trim_group_id][$row[csf('color_number_id')]]['booking_amt'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][amount];
								}
								else if($cbo_colorsizesensitive==2)
								{
									$txt_req_quantity=$row[csf('order_quantity')];
									$txtreq_amount=$row[csf('amount')];
									//echo $txtreq_amount.'='.$txt_req_quantity.', ';
									$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
									$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");
									$po_size_level_data_arr[$txt_trim_group_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
									$po_size_level_data_arr[$txt_trim_group_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['req_amt'][$row[csf('id')]]=$txtreq_amount;
									$po_size_level_data_arr[$txt_trim_group_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_qty'][$row[csf('id')]]=$po_qty;
									$po_size_level_data_arr[$txt_trim_group_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
									$po_size_level_data_arr[$txt_trim_group_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_id'][$row[csf('id')]]=$row[csf('id')];
									$po_size_level_data_arr[$txt_trim_group_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];

									$po_size_level_data_arr[$txt_trim_group_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['color_size_table_id'][$row[csf('id')]]=$row[csf('color_size_table_id')];
									$po_size_level_data_arr[$txt_trim_group_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['article_number'][$row[csf('id')]]=$row[csf('article_number')];

									$po_size_level_data_arr[$txt_trim_group_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_cons'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][cons];
									$po_size_level_data_arr[$txt_trim_group_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_qty'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][requirment];
									$po_size_level_data_arr[$txt_trim_group_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_amt'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][amount];

								}
								else if($cbo_colorsizesensitive==4)
								{
									$txt_req_quantity=$row[csf('order_quantity')];
									$txtreq_amount=$row[csf('amount')];

									// echo "S D<pre>";
									// print_r($req_amount_arr[$row[csf('id')]][$cbo_trim_precost_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]]);
									// echo "</pre>";
									$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
									$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");
									
									
									if($row[csf('item_color_number_id')]=='' || $row[csf('item_color_number_id')]=='0' || $color_library[$row[csf('item_color_number_id')]]=='' || $color_library[$row[csf('item_color_number_id')]]=='0') $row[csf('item_color_number_id')]=$row[csf('color_number_id')];
									
									//echo $row[csf('item_color_number_id')].'='.$row[csf('item_size')].'<br>';
									

									$po_color_size_level_data_arr[$txt_trim_group_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['req_qty'][$row[csf('id')]]+=$txtwoq_cal;
									$po_color_size_level_data_arr[$txt_trim_group_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['req_amt'][$row[csf('id')]]+=$txtreq_amount;
									$po_color_size_level_data_arr[$txt_trim_group_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['po_qty'][$row[csf('id')]]=$po_qty;
									$po_color_size_level_data_arr[$txt_trim_group_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
									$po_color_size_level_data_arr[$txt_trim_group_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['po_id'][$row[csf('id')]]=$row[csf('id')];
									$po_color_size_level_data_arr[$txt_trim_group_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];
									$po_color_size_level_data_arr[$txt_trim_group_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['color_size_table_id'][$row[csf('id')]]=$row[csf('color_size_table_id')];
									$po_color_size_level_data_arr[$txt_trim_group_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['article_number'][$row[csf('id')]]=$row[csf('article_number')];
									$po_color_size_level_data_arr[$txt_trim_group_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['item_size'][$row[csf('id')]]=$row[csf('item_size')];
									$po_color_size_level_data_arr[$txt_trim_group_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['item_color_number_id'][$row[csf('id')]]=$row[csf('item_color_number_id')];

									$po_color_size_level_data_arr[$txt_trim_group_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['booking_cons'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][cons];
									$po_color_size_level_data_arr[$txt_trim_group_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['booking_qty'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][requirment];
									$po_color_size_level_data_arr[$txt_trim_group_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['booking_amt'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][amount];

								}
								else if($cbo_colorsizesensitive==0)
								{
									$txt_req_quantity=$row[csf('order_quantity')];
									$txtreq_amount=$row[csf('amount')];
									//$txt_req_quantity=$req_qty_arr[$row[csf('id')]][$cbo_trim_precost_id];
									//$txtreq_amount=$req_amount_arr[$row[csf('id')]][$cbo_trim_precost_id];
									//$txtreq_amount=$req_amount_arr[$row[csf('id')]][$cbo_trim_precost_id];
									$req_qnty_ord_uom=def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
									$txtwoq_cal =def_number_format($req_qnty_ord_uom,5,"");

									$po_no_sen_level_data_arr[$txt_trim_group_id]['req_qty'][$row[csf('id')]]=$txtwoq_cal;
									$po_no_sen_level_data_arr[$txt_trim_group_id]['req_amt'][$row[csf('id')]]=$txtreq_amount;
									$po_no_sen_level_data_arr[$txt_trim_group_id]['po_qty'][$row[csf('id')]]=$po_qty;
									$po_no_sen_level_data_arr[$txt_trim_group_id]['order_quantity_set'][$row[csf('id')]]=$row[csf('order_quantity_set')];
									$po_no_sen_level_data_arr[$txt_trim_group_id]['po_id'][$row[csf('id')]]=$row[csf('id')];
									$po_no_sen_level_data_arr[$txt_trim_group_id]['order_quantity'][$row[csf('id')]]=$row[csf('order_quantity')];
									$po_no_sen_level_data_arr[$txt_trim_group_id]['color_size_table_id'][$row[csf('id')]]=$row[csf('color_size_table_id')];

									$po_no_sen_level_data_arr[$txt_trim_group_id]['booking_cons'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][cons];
									$po_no_sen_level_data_arr[$txt_trim_group_id]['booking_qty'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][requirment];
									$po_no_sen_level_data_arr[$txt_trim_group_id]['booking_amt'][$row[csf('id')]]=$booking_data_arr[$row[csf('color_size_table_id')]][amount];

								}
							}
                        }

                        if ( count($data_array)>0 && $cbo_level==1)
						{
							$i=0;
							foreach( $data_array as $row )
							{
								$data=explode('_',$data_array_cons[$i]);

								if($cbo_colorsizesensitive==1 || $cbo_colorsizesensitive==3 ){
									
									$txt_req_quantity =def_number_format($row[csf('order_quantity')],5,"");
									$txtwoq_amt_cal =def_number_format($row[csf('amount')],5,"");;
									
									$item_size="";
									//$pre_item_size="";
									$item_color = $booking_data_arr[$row[csf('color_size_table_id')]][item_color];
									$booking_item_color = $booking_data_arr[$row[csf('color_size_table_id')]][bom_item_color];
									if(empty($item_color)) $item_color = $row[csf('item_color_number_id')];
										
									if($item_color>0) $booking_item_color=$item_color;
									else $booking_item_color = $row[csf('item_color_number_id')];
									
									if(($row[csf('item_color_number_id')]=="" || $row[csf('item_color_number_id')]=="0") && ($booking_item_color=='0' || $booking_item_color=="") ) $booking_item_color = $row[csf('color_number_id')];
									$item_color_id=$row[csf('item_color_number_id')];
									
									//$pre_item_color = $row[csf('item_color_number_id')];
								}
								else if($cbo_colorsizesensitive==2){
									$txt_req_quantity =def_number_format($row[csf('order_quantity')],5,"");
									$txtwoq_amt_cal =def_number_format($row[csf('amount')],5,"");;
									
									$item_color ="";$pre_item_color ="";
									$item_size=$booking_data_arr[$row[csf('color_size_table_id')]][item_size];
									
									 if($item_size!="") $booking_item_size=$item_size;
									if($item_size=="")  $booking_item_size=$row[csf('item_size')];
									
									if($booking_item_size=='0' || $booking_item_size == "") $item_size=$size_library[$row[csf('item_size')]];
									if($booking_item_size=='0' || $booking_item_size == "") $item_size=$size_library[$row[csf('size_number_id')]];
									
									//$pre_item_size=$row[csf('item_size')];
								}
								else if($cbo_colorsizesensitive==4){
									
									
									$item_color_id=$row[csf('item_color_number_id')];
									$txt_req_quantity =def_number_format($row[csf('order_quantity')],5,"");
									$txtwoq_amt_cal =def_number_format($row[csf('amount')],5,"");
									$item_color = $booking_data_arr[$row[csf('color_size_table_id')]][item_color];
									//$booking_item_color = $booking_data_arr[$row[csf('color_size_table_id')]][bom_item_color];
									//$booking_item_size = $booking_data_arr[$row[csf('color_size_table_id')]][bom_item_size];
									if(empty($item_color))
									{
										$item_color = $row[csf('item_color_number_id')];
										 
									}
									if($item_color>0) $booking_item_color=$item_color;
									else $booking_item_color = $row[csf('item_color_number_id')];

									
									if(($row[csf('item_color_number_id')]=="" || $row[csf('item_color_number_id')]=="0") && ($booking_item_color=='0' || $booking_item_color=="") ) $booking_item_color = $row[csf('color_number_id')];
									
									$item_size=$booking_data_arr[$row[csf('color_size_table_id')]][item_size];
									 if($item_size!="") $booking_item_size=$item_size;
									if($item_size=="")  $booking_item_size=$row[csf('item_size')];
									
									if($booking_item_size=='0' || $booking_item_size == "") $item_size=$size_library[$row[csf('item_size')]];
									if($booking_item_size=='0' || $booking_item_size == "") $item_size=$size_library[$row[csf('size_number_id')]];
									
									//$pre_item_size=$row[csf('item_size')];
								}
								else if($cbo_colorsizesensitive==0){
									$txt_req_quantity =def_number_format($row[csf('order_quantity')],5,"");
									$txtwoq_amt_cal =def_number_format($row[csf('amount')],5,"");
									
									$item_color = $booking_data_arr[$row[csf('color_size_table_id')]][item_color];
									//$booking_item_color = $booking_data_arr[$row[csf('color_size_table_id')]][bom_item_color];
									if($item_color>0) $booking_item_color=$item_color;
									else $booking_item_color = $row[csf('item_color_number_id')];
									
									if(($row[csf('item_color_number_id')]=="" || $row[csf('item_color_number_id')]=="0") && ($booking_item_color=='0' || $booking_item_color=="") ) $booking_item_color = $row[csf('color_number_id')];
									
									$item_size=$booking_data_arr[$row[csf('color_size_table_id')]][item_size];
									//$booking_item_size = $booking_data_arr[$row[csf('color_size_table_id')]][bom_item_size];
									if($item_size=='0' || $item_size == "") $item_size=$size_library[$row[csf('item_size')]];
									if($item_size=='0' || $item_size == "") $item_size=$size_library[$row[csf('size_number_id')]];
									
									if($booking_item_size=='0' || $booking_item_size == "") $booking_item_size=$row[csf('item_size')];
									if($booking_item_size=='0' || $booking_item_size == "") $booking_item_size=$size_library[$row[csf('size_number_id')]];
									
									//$pre_item_size=$row[csf('item_size')];
								}
								$req_qnty_ord_uom = def_number_format($txt_req_quantity/$sql_lib_item_group_array[$txt_trim_group_id][conversion_factor],5,"");
								$txtwoq_cal = def_number_format($req_qnty_ord_uom,5,"");

								$rate=$booking_data_arr[$row[csf('color_size_table_id')]][rate];
								if($rate==0 || $rate=="") $rate=($txtwoq_amt_cal/$txt_req_quantity)*$conversion_factor;//$rate=$txt_avg_price; 20-2047 by Aziz
								
								//$conversion_factor;
								//echo $txtwoq_amt_cal.'='.$txt_req_quantity.'f=';

								$description=$booking_data_arr[$row[csf('color_size_table_id')]][description];
								if($description=="") $description=trim($txt_pre_des);

								$brand_supplier=$booking_data_arr[$row[csf('color_size_table_id')]][brand_supplier];
								if($brand_supplier=="")$brand_supplier=trim($txt_pre_brand_sup);
								
								$pre_item_ref=$item_ref_color_size_arr[$cbo_trim_precost_id][$row[csf('color_size_table_id')]]['item_ref'];
								//echo $pre_item_ref.'='.$row[csf('color_size_table_id')].',';
								$item_ref = $booking_data_arr[$row[csf('color_size_table_id')]][item_ref];
								if($item_ref) $item_ref=$item_ref;else $item_ref=$pre_item_ref;
								$remarks = $booking_data_arr[$row[csf('color_size_table_id')]][remarks];

								if($txtwoq_cal>0)
								{
									$i++;
									?>
                                    <tr id="break_<? echo $i;?>" align="center">
                                        <td><? echo $i;?></td>
                                        <td><input type="text" id="poarticle_<? echo $i;?>" name="poarticle_<? echo $i;?>" class="text_boxes" style="width:70px" value="<? echo $row[csf('article_number')]; ?>"  readonly /></td>
                                        <td>
                                            <input type="text" id="pocolor_<? echo $i;?>" name="pocolor_<? echo $i;?>" class="text_boxes" style="width:80px" value="<? echo $color_library[$row[csf('color_number_id')]]; ?>" <? if($gmt_color_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> readonly/>
                                            <input type="hidden" id="pocolorid_<? echo $i;?>" name="pocolorid_<? echo $i;?>" class="text_boxes" style="width:70px" value="<? echo $row[csf('color_number_id')]; ?>" readonly />
                                            <input type="hidden" id="poid_<? echo $i;?>" name="poid_<? echo $i;?>" class="text_boxes" style="width:70px" value="<? echo $row[csf('id')]; ?>" />
                                            <input type="hidden" id="poqty_<? echo $i;?>" name="poqty_<? echo $i;?>" class="text_boxes" style="width:70px" value="<? echo $po_qty_arr[$row[csf('id')]]; ?>" readonly />
                                            <input type="hidden" id="poreqqty_<? echo $i;?>" name="poreqqty_<? echo $i;?>" class="text_boxes" style="width:70px" value="<? echo $txtwoq_cal; ?>" readonly />
                                        </td>
                                        <td>
                                            <input type="text" id="gmtssizes_<? echo $i;?>"  name="gmtssizes_<? echo $i;?>" class="text_boxes" style="width:60px" value="<? echo $size_library[$row[csf('size_number_id')]]; ?>" <? if($gmt_size_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> readonly/>
                                            <input type="hidden" id="gmtssizesid_<? echo $i;?>"  name="gmtssizesid_<? echo $i;?>" class="text_boxes" style="width:50px" value="<? echo $row[csf('size_number_id')]; ?>" readonly />
                                        </td>
                                        <td><input type="text" id="des_<? echo $i;?>"  name="des_<? echo $i;?>" class="text_boxes" style="width:90px" value="<? echo $description;?>" onChange="copy_value(this.value,'des_',<? echo $i;?>)" <? if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> /></td>
                                        <td><input type="text" id="brndsup_<? echo $i;?>"  name="brndsup_<? echo $i;?>" class="text_boxes" style="width:90px" value="<? echo $brand_supplier; ?>" onChange="copy_value(this.value,'brndsup_',<? echo $i;?>)" <? if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?>/></td>
                                        <td><input type="hidden" id="preitemcolor_<? echo $i;?>" value="<? echo $item_color_id; ?>" name="preitemcolor_<? echo $i;?>" class="text_boxes" style="width:90px"  />
                                        <input type="text" id="itemcolor_<? echo $i;?>" value="<? echo $color_library[$booking_item_color]; ?>" name="itemcolor_<? echo $i;?>" class="text_boxes" style="width:90px" onChange="copy_value(this.value,'itemcolor_',<? echo $i;?>)" <? if($item_color_edb || $piNumber || $recvNumber ){ echo "disabled";}else { echo "";} ?> />
                                        </td>
                                        <td><input type="text" id="itemref_<? echo $i;?>" value="<? echo $item_ref; ?>" name="itemref_<? echo $i;?>" class="text_boxes" style="width:90px" onChange="copy_value(this.value,'itemref_',<? echo $i;?>)" <? if($piNumber || $recvNumber ){ echo "disabled";}else { echo "";} ?> /></td>
                                        
                                        <td><input type="hidden" id="preitemsizes_<? echo $i;?>"  name="preitemsizes_<? echo $i;?>"    class="text_boxes" style="width:60px"   value="<? echo $row[csf('item_size')]; ?>" />
                                        <input type="text" id="itemsizes_<? echo $i;?>"  name="itemsizes_<? echo $i;?>"    class="text_boxes" style="width:60px" onChange="copy_value(this.value,'itemsizes_',<? echo $i;?>)" value="<? echo $booking_item_size; ?>" <? if($item_size_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> /></td>
                                        <td><input type="hidden" id="reqqty_<? echo $i;?>"  name="reqqty_<? echo $i;?>" class="text_boxes_numeric" style="width:60px"    value="<? echo $txtwoq_cal ?>" readonly/>
                                        	<input type="text" id="qty_<? echo $i;?>" onBlur="validate_sum( <? echo $i; ?> )" onChange="set_sum_value( 'qty_sum', 'qty_' );set_sum_value( 'woqty_sum', 'woqny_' );calculate_requirement(<? echo $i;?>);copy_value(this.value,'qty_',<? echo $i;?>)"  name="qty_<? echo $i;?>" class="text_boxes_numeric" style="width:60px"   placeholder="<? echo $txtwoq_cal; ?>" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][cons]; ?>"/>
                                        </td>
                                        <td><input type="text" id="excess_<? echo $i;?>" onBlur="set_sum_value( 'excess_sum', 'excess_' ) "  name="excess_<? echo $i;?>" class="text_boxes_numeric" style="width:30px" onChange="calculate_requirement(<? echo $i;?>);set_sum_value( 'excess_sum', 'excess_' );set_sum_value( 'woqty_sum', 'woqny_' );copy_value(this.value,'excess_',<? echo $i;?>) " value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][process_loss_percent]; ?>" disabled/></td>
                                        <td><input type="text" id="woqny_<? echo $i;?>" onBlur="set_sum_value('woqty_sum', 'woqny_')" onChange="set_sum_value('woqty_sum', 'woqny_')" name="woqny_<? echo $i;?>" class="text_boxes_numeric" style="width:60px" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][requirment]; ?>" readonly /></td>
                                        <td><input type="text" id="rate_<? echo $i;?>"  name="rate_<? echo $i;?>" class="text_boxes_numeric" style="width:90px" onChange="calculate_amount(<? echo $i;?>);set_sum_value( 'amount_sum', 'amount_' );copy_value(this.value,'rate_',<? echo $i;?>) " value="<? echo $rate; ?>" <? if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> /></td>
                                        <td><input type="text" id="amount_<? echo $i;?>" name="amount_<? echo $i;?>" onBlur="set_sum_value( 'amount_sum', 'amount_' )" class="text_boxes_numeric" style="width:90px" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][amount]; ?>" readonly></td>
                                        <td>
                                            <input type="text" id="pcs_<? echo $i;?>"  name="pcs_<? echo $i;?>"  onBlur="set_sum_value( 'pcs_sum', 'pcs_' ) " class="text_boxes_numeric" style="width:50px"  value="<? echo $row[csf('order_quantity')]; ?>" readonly>
                                            <input type="hidden" id="pcsset_<? echo $i;?>"  name="pcsset_<? echo $i;?>"  onBlur="set_sum_value( 'pcs_sum', 'pcs_' ) " class="text_boxes_numeric" style="width:50px"  value="<? echo $row[csf('order_quantity_set')]; ?>" readonly>
                                            <input type="hidden" id="colorsizetableid_<? echo $i;?>"  name="colorsizetableid_<? echo $i;?>" class="text_boxes" style="width:45px" value="<? echo $row[csf('color_size_table_id')]; ?>" />
                                            <input type="hidden" id="updateid_<? echo $i;?>"  name="updateid_<? echo $i;?>" class="text_boxes" style="width:45px" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][id]; ?>" readonly />
                                        </td>
										<td><input type="text" id="remarks_<? echo $i;?>"  name="remarks_<? echo $i;?>" class="text_boxes" style="width:90px" value="<? echo $remarks;?>" onChange="copy_value(this.value,'remarks_',<? echo $i;?>)" />
                                            
                                        </td>
                                    </tr>
								<?
								}
							}
                        }

                      //  print_r($po_color_level_data_arr);
						$level_arr=array(); $gmt_color_edb="";  $item_color_edb="";  $gmt_size_edb="";  $item_size_edb="";
                        if($cbo_colorsizesensitive==1){
							$sql="select min(b.id) as id , min(c.id) as color_size_table_id,c.color_number_id,min(c.color_order) as color_order,sum(c.order_quantity) as order_quantity,sum(c.order_total) as amount from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond  group by  c.color_number_id order by  color_order";
							
							$level_arr=$po_color_level_data_arr;
							$gmt_size_edb=1;
							$item_size_edb=1;
                        }
                        else if($cbo_colorsizesensitive==2){
							 $sql="select min(b.id) as id , min(c.id) as color_size_table_id,c.size_number_id,c.size_number_id as item_size,c.article_number,min(c.size_order) as size_order,min(c.size_number_id) as item_size,sum(c.order_quantity) as order_quantity,sum(c.order_total) as amount from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c  where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond group by  c.size_number_id,c.article_number order by size_order";
							$level_arr=$po_size_level_data_arr;
							$gmt_color_edb=1; $item_color_edb=1;
                        }
                        else if($cbo_colorsizesensitive==3){
							 $sql="SELECT min(b.id) as id, min(c.id) as color_size_table_id,c.color_number_id,min(c.color_order) as color_order,sum(c.order_quantity) as order_quantity,sum(c.order_total) as amount  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c  where a.id=b.job_id and a.id=c.job_id  and b.id=c.po_break_down_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond  group by  c.color_number_id order by  color_order";
							$level_arr=$po_color_level_data_arr;
							$gmt_size_edb=1; $item_size_edb=1;
                        }
                        else if($cbo_colorsizesensitive==4){
						  $sql="select min(b.id) as id ,min(c.id) as color_size_table_id, c.color_number_id as item_color_number_id,c.size_number_id as item_size,c.color_number_id, c.size_number_id, c.article_number, min(c.color_order) as color_order, min(c.size_order) as size_order, sum(c.order_quantity) as order_quantity,sum(c.order_total) as amount from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.id=b.job_id and a.id=c.job_id and b.job_id=c.job_id  and b.id=c.po_break_down_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id)  $txt_country_cond  group by  c.color_number_id,c.size_number_id, c.article_number order by  color_order,size_order,c.article_number";
							$level_arr=$po_color_size_level_data_arr;
                        }
                        else{
							//$sql="select b.job_no_mst,min(b.id) as id , min(c.id) as color_size_table_id,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.id=d.job_id and a.id=e.job_id and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.job_no_mst";
						 $sql="select b.job_no_mst,min(b.id) as id,min(c.id) as color_size_table_id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set,sum(c.order_total) as amount  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  b.id in($txt_po_id)  $txt_country_cond group by  a.total_set_qnty,b.job_no_mst ";
							$level_arr=$po_no_sen_level_data_arr;
                        }
						// print_r($level_arr);
                        $data_array=sql_select($sql);
                        if ( count($data_array)>0 && $cbo_level==2){
							$i=0;
							foreach( $data_array as $row ){

								if($cbo_colorsizesensitive==1){
									$txtwoq_cal =def_number_format($row[csf('order_quantity')],5,"");
									//echo $txtwoq_cal.'d';
									$txtwoq_amt_cal =def_number_format($row[csf('amount')],5,"");;
									$po_qty=array_sum($level_arr[$txt_trim_group_id][$row[csf('color_number_id')]]['po_qty']);
									$order_quantity_set=array_sum($level_arr[$txt_trim_group_id][$row[csf('color_number_id')]]['order_quantity_set']);
									$booking_cons=def_number_format(array_sum($level_arr[$txt_trim_group_id][$row[csf('color_number_id')]]['booking_cons']),5,"");
									$booking_qty=def_number_format(array_sum($level_arr[$txt_trim_group_id][$row[csf('color_number_id')]]['booking_qty']),5,"");
									$booking_amt=def_number_format(array_sum($level_arr[$txt_trim_group_id][$row[csf('color_number_id')]]['booking_amt']),5,"");
									
									$item_size="";$item_color_id="";
									//booking_item_size
									$item_color = $booking_data_arr[$row[csf('color_size_table_id')]][item_color];
									//$booking_item_color=$booking_data_arr[$row[csf('color_size_table_id')]][bom_item_color];
									if($item_color==0 || $item_color=="" ) $booking_item_color = $row[csf('color_number_id')];
									else $booking_item_color = $item_color;
									
								 
									//$pre_item_color = "";
								}
								if($cbo_colorsizesensitive==2){
									$txtwoq_cal =def_number_format($row[csf('order_quantity')],5,"");
									$txtwoq_amt_cal =def_number_format($row[csf('amount')],5,"");;
									//echo $txtwoq_amt_cal.'='.$txtwoq_cal.' , ';
									$po_qty=array_sum($level_arr[$txt_trim_group_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['po_qty']);
									$order_quantity_set=array_sum($level_arr[$txt_trim_group_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['order_quantity_set']);
									$booking_cons=def_number_format(array_sum($level_arr[$txt_trim_group_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_cons']),5,"");
									$booking_qty=def_number_format(array_sum($level_arr[$txt_trim_group_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_qty']),5,"");
									$booking_amt=def_number_format(array_sum($level_arr[$txt_trim_group_id][$row[csf('size_number_id')]][$row[csf('article_number')]]['booking_amt']),5,"");
									
									$item_size=$booking_data_arr[$row[csf('color_size_table_id')]][item_size];
									//$booking_item_size=$booking_data_arr[$row[csf('color_size_table_id')]][bom_item_size];
									
									 if($item_size!="") $booking_item_size=$item_size;
									if($item_size=="")  $booking_item_size=$size_library[$row[csf('item_size')]];
									
									if($booking_item_size=='0' || $booking_item_size == "") $item_size=$size_library[$row[csf('item_size')]];
									if($booking_item_size=='0' || $booking_item_size == "") $item_size=$size_library[$row[csf('size_number_id')]];
									
									if($booking_item_size=='0' || $booking_item_size == "") $booking_item_size=$size_library[$row[csf('item_size')]];
									if($booking_item_size=='0' || $booking_item_size == "") $booking_item_size=$size_library[$row[csf('size_number_id')]];
									
									$item_color = "";//$pre_item_color = "";$pre_item_size=$row[csf('item_size')];
								}
								if($cbo_colorsizesensitive==3){
									$item_color_id=$row[csf('item_color_number_id')];
									$txtwoq_cal =def_number_format($row[csf('order_quantity')],5,"");
									$txtwoq_amt_cal =def_number_format($row[csf('amount')],5,"");
									
									$po_qty=array_sum($level_arr[$txt_trim_group_id][$row[csf('color_number_id')]]['po_qty']);
									$order_quantity_set=array_sum($level_arr[$txt_trim_group_id][$row[csf('color_number_id')]]['order_quantity_set']);
									$booking_cons=def_number_format(array_sum($level_arr[$txt_trim_group_id][$row[csf('color_number_id')]]['booking_cons']),5,"");
									$booking_qty=def_number_format(array_sum($level_arr[$txt_trim_group_id][$row[csf('color_number_id')]]['booking_qty']),5,"");
									$booking_amt=def_number_format(array_sum($level_arr[$txt_trim_group_id][$row[csf('color_number_id')]]['booking_amt']),5,"");
									
									$item_size="";//$pre_item_size = "";
									$item_color = $booking_data_arr[$row[csf('color_size_table_id')]][item_color];
									//echo $item_color.'DD';
									//$booking_item_color = $booking_data_arr[$row[csf('color_size_table_id')]][bom_item_color];
									if($item_color==0 || $item_color=="" ) $booking_item_color = $row[csf('item_color_number_id')];
								 	else $booking_item_color = $item_color;
								//	echo $item_color.'DD'.$booking_item_color.', ';
								//	if($booking_item_color==0 || $booking_item_color=="" ) $booking_item_color = $row[csf('item_color_number_id')];
									//if($booking_item_color==0 || $booking_item_color=="" ) $booking_item_color = $row[csf('color_number_id')];
									
									//if(($row[csf('item_color_number_id')]=="" || $row[csf('item_color_number_id')]=="0") && ($booking_item_color=='0' || $booking_item_color=="") ) $booking_item_color = $row[csf('color_number_id')];
									//else $booking_item_color = $row[csf('item_color_number_id')];
									
									//$pre_item_color = $row[csf('item_color_number_id')];
									
								}
								if($cbo_colorsizesensitive==4){
									$item_color_id=$row[csf('item_color_number_id')];
									if($row[csf('item_color_number_id')]=='' || $row[csf('item_color_number_id')]=='0' || $color_library[$row[csf('item_color_number_id')]]=='' || $color_library[$row[csf('item_color_number_id')]]=='0')  $row[csf('item_color_number_id')]=$row[csf('color_number_id')];
									
								//	if($row[csf('item_color_number_id')]=='' || $row[csf('item_color_number_id')]=='0' || $color_library[$row[csf('item_color_number_id')]]=='' || $color_library[$row[csf('item_color_number_id')]]=='0')  $row[csf('item_color_number_id')]=$row[csf('color_number_id')];
									
								//echo $row[csf('item_color_number_id')].'d';
								//echo $row[csf('item_color_number_id')].'='.$row[csf('item_size')].',';
									 
									$txtwoq_cal =def_number_format($row[csf('order_quantity')],5,"");
									$txtwoq_amt_cal =def_number_format($row[csf('amount')],5,"");
									
									$po_qty=array_sum($level_arr[$txt_trim_group_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['po_qty']);
									$order_quantity_set=array_sum($level_arr[$txt_trim_group_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['order_quantity_set']);
									$booking_cons=def_number_format(array_sum($level_arr[$txt_trim_group_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['booking_cons']),5,"");
									$booking_qty=def_number_format(array_sum($level_arr[$txt_trim_group_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['booking_qty']),5,"");
									$booking_amt=def_number_format(array_sum($level_arr[$txt_trim_group_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('article_number')]][$row[csf('item_color_number_id')]][$row[csf('item_size')]]['booking_amt']),5,"");
									//$item_color =0;
									$item_color = $booking_data_arr[$row[csf('color_size_table_id')]][item_color];
									//$booking_item_color = $booking_data_arr[$row[csf('color_size_table_id')]][bom_item_color];
									
									if($item_color>0) $booking_item_color=$item_color;
									else $booking_item_color = $row[csf('item_color_number_id')];
									
									if(($row[csf('item_color_number_id')]=="" || $row[csf('item_color_number_id')]=="0") && ($booking_item_color=='0' || $booking_item_color=="") ) $booking_item_color = $row[csf('color_number_id')];
									 
									$item_size=$booking_data_arr[$row[csf('color_size_table_id')]][item_size];
									// echo $row[csf('color_size_table_id')].'='.$item_size.'d';
									 if($item_size!="") $booking_item_size=$item_size;
									if($item_size=="")  $booking_item_size=$size_library[$row[csf('item_size')]];
									
									 
									//if($item_size=='0' || $item_size == "") $item_size=$size_library[$row[csf('size_number_id')]];
									 
									if($booking_item_size=='0' || $booking_item_size == "") $booking_item_size=$size_library[$row[csf('item_size')]];
									if($booking_item_size=='0' || $booking_item_size == "") $booking_item_size=$size_library[$row[csf('size_number_id')]];
									
									//$pre_item_color = $row[csf('item_color_number_id')];
									//$pre_item_size = $row[csf('item_size')];
								}
								if($cbo_colorsizesensitive==0){
									$txtwoq_cal =def_number_format($row[csf('order_quantity')],5,"");
									$txtwoq_amt_cal =def_number_format($row[csf('amount')],5,"");;
									$po_qty=array_sum($level_arr[$txt_trim_group_id]['po_qty']);
									$order_quantity_set=array_sum($level_arr[$txt_trim_group_id]['order_quantity_set']);
									$booking_cons=def_number_format(array_sum($level_arr[$txt_trim_group_id]['booking_cons']),5,"");
									$booking_qty=def_number_format(array_sum($level_arr[$txt_trim_group_id]['booking_qty']),5,"");
									$booking_amt=def_number_format(array_sum($level_arr[$txt_trim_group_id]['booking_amt']),5,"");
									
									$item_color = $booking_data_arr[$row[csf('color_size_table_id')]][item_color];
									//$booking_item_color = $booking_data_arr[$row[csf('color_size_table_id')]][bom_item_color];
									if($item_color==0 || $item_color=="" ) $booking_item_color = $row[csf('color_number_id')];
								 	else $booking_item_color = $item_color;
									//echo $row[csf('color_number_id')].'d';
									//$pre_item_color = "";$pre_item_size="";
									//$item_color_id="";
								}

								$rate=$booking_data_arr[$row[csf('color_size_table_id')]][rate];
								if(($rate*1)==0)
								{
									if($booking_amt>0) $rate=$booking_amt/$booking_qty;// else $rate=($txtwoq_amt_cal/$txtwoq_cal);
								}
								//echo "<pre>".$txtwoq_amt_cal."**".$txtwoq_cal."/".$txtwoq_cal."**".$txtwoq_amt_cal."__".$txtwoq_cal."</pre>";
								//$rate=$txt_avg_price; //20-2047 by aziz


								$description=$booking_data_arr[$row[csf('color_size_table_id')]][description];
								if($description=="") $description=trim($txt_pre_des);
								//echo $description.'='.$txt_pre_des.'<br/>';
								$brand_supplier=$booking_data_arr[$row[csf('color_size_table_id')]][brand_supplier];
								if($brand_supplier=="") $brand_supplier=trim($txt_pre_brand_sup);
								
								$pre_item_ref=$item_ref_color_size_arr[$txt_trim_group_id][$row[csf('color_size_table_id')]]['item_ref'];
								//echo $txtwoq_cal.'='.$row[csf('color_size_table_id')].',';
								$item_ref = $booking_data_arr[$row[csf('color_size_table_id')]][item_ref];
								if($item_ref) $item_ref=$item_ref;else $item_ref=$pre_item_ref;
								$remarks = $booking_data_arr[$row[csf('color_size_table_id')]][remarks];

								if($txtwoq_cal>0)
								{
									$i++;
									?>
									<tr id="break_<?=$i; ?>" align="center">
                                        <td><?=$i; ?></td>
                                        <td><input type="text" id="poarticle_<?=$i; ?>" name="poarticle_<?=$i; ?>" class="text_boxes" style="width:70px" value="<?=$row[csf('article_number')]; ?>" readonly />
                                        </td>
                                        <td>
                                            <input type="text" id="pocolor_<?=$i; ?>" name="pocolor_<?=$i; ?>" class="text_boxes" style="width:80px" value="<?=$color_library[$row[csf('color_number_id')]]; ?>" <? if($gmt_color_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> readonly />
                                            <input type="hidden" id="pocolorid_<?=$i; ?>" name="pocolorid_<?=$i; ?>" class="text_boxes" style="width:75px" value="<?=$row[csf('color_number_id')]; ?>" readonly />
                                            <input type="hidden" id="poid_<?=$i;?>" name="poid_<?=$i;?>" class="text_boxes" style="width:75px" value="<?=$row[csf('id')]; ?>" readonly />
                                            <input type="hidden" id="poqty_<?=$i;?>" name="poqty_<?=$i;?>" class="text_boxes" style="width:75px" value="<?=$po_qty; ?>" readonly />
                                            <input type="hidden" id="poreqqty_<?=$i;?>" name="poreqqty_<?=$i;?>" class="text_boxes" style="width:75px" value="<?=$txtwoq_cal; ?>" readonly />
                                        </td>
                                        <td>
                                            <input type="text" id="gmtssizes_<?=$i;?>" name="gmtssizes_<?=$i;?>" class="text_boxes" style="width:60px" value="<?=$size_library[$row[csf('size_number_id')]]; ?>" <? if($gmt_size_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> readonly/>
                                            <input type="hidden" id="gmtssizesid_<?=$i;?>" name="gmtssizesid_<?=$i;?>" class="text_boxes" style="width:60px" value="<?=$row[csf('size_number_id')]; ?>" readonly />
                                        </td>
                                        <td><input type="text" id="des_<?=$i;?>" name="des_<?=$i;?>" class="text_boxes" style="width:90px" value="<?=$description;?>" onChange="copy_value(this.value,'des_',<?=$i;?>)" <? if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> />
                                        </td>
                                        <td><input type="text" id="brndsup_<?=$i;?>" name="brndsup_<?=$i;?>" class="text_boxes" style="width:90px" value="<?=$brand_supplier; ?>" onChange="copy_value(this.value,'brndsup_',<?=$i;?>)" <? if( $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> />
                                        </td>
                                        <td><input type="hidden" id="preitemcolor_<?=$i;?>" value="<?=$row[csf('item_color_number_id')]; ?>" name="preitemcolor_<?=$i;?>" class="text_boxes" style="width:90px"  />
                                        <input type="text" id="itemcolor_<?=$i;?>" value="<?=$color_library[$booking_item_color]; ?>" name="itemcolor_<?=$i;?>" class="text_boxes" style="width:90px" onChange="copy_value(this.value,'itemcolor_',<?=$i;?>)" <? if($item_color_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> />
                                        </td>
                                         <td><input type="text" id="itemref_<?=$i;?>" value="<?=$item_ref; ?>" name="itemref_<?=$i;?>" class="text_boxes" style="width:90px" onChange="copy_value(this.value,'itemref_',<?=$i;?>);" <? if($item_color_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?> /></td>
                                         
                                      <td><input type="hidden" id="preitemsizes_<?=$i;?>" name="preitemsizes_<?=$i;?>" class="text_boxes" style="width:60px"   value="<?=$row[csf('item_size')]; ?>" />
                                        
                                        <input type="text" id="itemsizes_<?=$i;?>" name="itemsizes_<?=$i;?>" class="text_boxes" style="width:60px" onChange="copy_value(this.value,'itemsizes_',<?=$i;?>);" value="<?=$booking_item_size; ?>"  <? if($item_size_edb || $piNumber || $recvNumber ){ echo  "disabled";}else { echo "";} ?>/>
                                        </td>
                                        <td><input type="hidden" id="reqqty_<?=$i;?>" name="reqqty_<?=$i;?>" class="text_boxes_numeric" style="width:60px" value="<?=$txtwoq_cal ?>" readonly/>
                                        	<input type="text" id="qty_<?=$i;?>" onBlur="validate_sum(<?=$i; ?>);" onChange="set_sum_value( 'qty_sum', 'qty_' );set_sum_value( 'woqty_sum', 'woqny_' );calculate_requirement(<?=$i;?>);copy_value(this.value,'qty_',<?=$i;?>);" name="qty_<?=$i;?>" class="text_boxes_numeric" style="width:60px" placeholder="<?=$txtwoq_cal; ?>" value="<? if($booking_cons>0){echo $booking_cons;} ?>"/>
                                        </td>
                                        <td>
                                        	<input type="text" id="excess_<?=$i;?>" onBlur="set_sum_value( 'excess_sum', 'excess_');" name="excess_<?=$i;?>" class="text_boxes_numeric" style="width:30px" onChange="calculate_requirement(<?=$i;?>);set_sum_value( 'excess_sum', 'excess_');set_sum_value( 'woqty_sum', 'woqny_'); copy_value(this.value,'excess_',<?=$i;?>);" value="<?=$booking_data_arr[$row[csf('color_size_table_id')]][process_loss_percent]; ?>" disabled/>
                                        </td>
                                        <td><input type="text" id="woqny_<?=$i;?>" onBlur="set_sum_value( 'woqty_sum', 'woqny_');" onChange="set_sum_value('woqty_sum', 'woqny_');"  name="woqny_<?=$i;?>" class="text_boxes_numeric" style="width:60px" value="<? if($booking_qty){echo $booking_qty;} ?>" readonly />
                                        </td>
                                        <td><input type="text" id="rate_<?=$i;?>" name="rate_<?=$i;?>" class="text_boxes_numeric" style="width:90px" onChange="calculate_amount(<?=$i;?>); set_sum_value('amount_sum', 'amount_'); copy_value(this.value,'rate_',<?=$i;?>);" value="<?=$rate; ?>" <? if( $piNumber || $recvNumber ){ echo "disabled";}else { echo "";} ?> />
                                        </td>
                                        <td><input type="text" id="amount_<?=$i;?>" name="amount_<?=$i;?>" onBlur="set_sum_value('amount_sum', 'amount_');" class="text_boxes_numeric" style="width:90px" value="<?=$booking_amt; //$booking_data_arr[$row[csf('color_size_table_id')]][amount]; ?>" readonly>
                                        </td>

                                        <td><input type="text" id="pcs_<?=$i;?>" name="pcs_<?=$i;?>"  onBlur="set_sum_value( 'pcs_sum', 'pcs_' );" class="text_boxes_numeric" style="width:50px"  value="<?=$row[csf('order_quantity')]; ?>" readonly>
                                            <input type="hidden" id="pcsset_<?=$i;?>" name="pcsset_<?=$i;?>"  onBlur="set_sum_value( 'pcs_sum', 'pcs_') " class="text_boxes_numeric" style="width:50px" value="<?=$order_quantity_set; ?>" readonly>
                                            <input type="hidden" id="colorsizetableid_<?=$i;?>" name="colorsizetableid_<?=$i;?>" class="text_boxes" style="width:45px" value="<?=$row[csf('color_size_table_id')]; ?>" />
                                            <input type="hidden" id="updateid_<?=$i;?>" name="updateid_<?=$i;?>" class="text_boxes" style="width:45px" value="<?=$booking_data_arr[$row[csf('color_size_table_id')]][id]; ?>" readonly />
                                        </td>
										<td><input type="text" id="remarks_<?=$i;?>" name="remarks_<?=$i;?>" class="text_boxes" style="width:90px" value="<?=$remarks;?>" onChange="copy_value(this.value,'remarks_',<?=$i;?>);" /></td>
                                        <?
										if($cbo_colorsizesensitive==0)
										{
										?>
                                        
                                        <?
										}
										?>
									</tr>
								<?
								}
							}
                        }
					
					} //******Item From: Library End***********
					//print_r($level_arr);
                        ?>
                        </tbody>
                        <tfoot>
                            <tr>
                               <th width="30">&nbsp;</th>
                               <th width="80">&nbsp;</th>
                               <th width="90">&nbsp;</th>
                               <th width="70">&nbsp;</th>
                               <th width="100">&nbsp;</th>
                               <th width="100">&nbsp;</th>
                               <th width="100">&nbsp;</th>
                               <th width="100">&nbsp;</th>
                               <th width="70">&nbsp;</th>
                               <th width="70"><input type="text" id="qty_sum" name="qty_sum" class="text_boxes_numeric" style="width:60px"  readonly></th>
                               <th width="40"><input type="text" id="excess_sum"  name="excess_sum" class="text_boxes_numeric" style="width:30px" readonly></th>
                               <th width="70"><input type="text" id="woqty_sum"  name="woqty_sum" class="text_boxes_numeric" style="width:60px" readonly></th>
                               <th width="100"><input type="text" id="rate_sum"  name="rate_sum" class="text_boxes_numeric" style="width:90px" readonly></th>
                               <th width="100"><input type="text" id="amount_sum" name="amount_sum" class="text_boxes_numeric" style="width:90px" readonly></th>
                               <th><input type="hidden" id="json_data" name="json_data" class="text_boxes_numeric" style="width:50px" value='<?=json_encode($level_arr); ?>' readonly>
                                	<input type="text" id="pcs_sum" name="pcs_sum" class="text_boxes_numeric" style="width:50px" readonly>
                               </th>
                            </tr>
                        </tfoot>
                    </table>
                    <table width="1250" cellspacing="0" class="" border="0" rules="all">
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
		//console.log(wo_qty+'--'+wo_qty_sum);
		//if(wo_qty!=wo_qty_sum)
		if((wo_qty-wo_qty_sum)>1)
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
	$size_library=return_library_array("select id, size_name from lib_size",'id','size_name');
	$data=explode("_",$data);
	$garments_nature=$data[0];
	$cbo_company_name=$data[1];
	$txt_job_no=$data[2];
	$txt_po_id=$data[3];
	$cbo_trim_precost_id=$data[4];
	$txt_trim_group_id=$data[5];
	$txt_update_dtls_id=trim($data[6]);
	$cbo_colorsizesensitive=$data[7];
	$txt_req_quantity=$data[8];
	$txt_avg_price=$data[9];
	$txt_country=$data[10];
	$txt_pre_des=$data[11];
	$txt_pre_brand_sup=$data[12];
	$cbo_level=$data[13];

	if($txt_job_no==""){
		$txt_job_no_cond=""; $txt_job_no_cond1="";
	}
	else{
		$txt_job_no_cond ="and a.job_no='$txt_job_no'"; $txt_job_no_cond1 ="and job_no='$txt_job_no'";
	}

	if($txt_country=="") $txt_country_cond=""; else $txt_country_cond ="and c.country_id in ($txt_country)";

	$process_loss_method=return_field_value("process_loss_method", "variable_order_tracking", "company_name=$cbo_company_name  and variable_list=18 and item_category_id=4 and status_active=1 and is_deleted=0");
	$sql_po_qty=sql_select("select b.id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id in($txt_po_id)  $txt_country_cond  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty"); //,c.item_number_id
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
	if($txt_update_dtls_id=="" || $txt_update_dtls_id==0) $txt_update_dtls_id=0;else $txt_update_dtls_id=$txt_update_dtls_id;
	$booking_data_arr=array();
	$booking_data=sql_select("select id,wo_trim_booking_dtls_id,description,brand_supplier,item_color,item_size,cons,process_loss_percent,requirment,rate, 	amount,pcs,color_size_table_id,item_ref,bom_item_color,bom_item_size  from wo_trim_book_con_dtls where wo_trim_booking_dtls_id in($txt_update_dtls_id) and status_active=1 and is_deleted=0");
	foreach($booking_data as $booking_data_row){
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][id]=$booking_data_row[csf('id')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][description]=$booking_data_row[csf('description')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][brand_supplier]=$booking_data_row[csf('brand_supplier')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][item_color]=$booking_data_row[csf('item_color')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][item_ref]=$booking_data_row[csf('item_ref')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][item_size]=$booking_data_row[csf('item_size')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][book_item_color]=$booking_data_row[csf('bom_item_color')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][book_item_size]=$booking_data_row[csf('bom_item_size')];
		
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][cons]+=$booking_data_row[csf('cons')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][process_loss_percent]=$booking_data_row[csf('process_loss_percent')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][requirment]+=$booking_data_row[csf('requirment')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][rate]=$booking_data_row[csf('rate')];
		$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][amount]+=$booking_data_row[csf('amount')];
	}
	 $sql_pre=sql_select("select d.wo_pre_cost_trim_cost_dtls_id as trims_dtls_id,d.color_size_table_id,d.item_ref,d.size_number_id,d.color_number_id,d.item_color_number_id as item_color  from  wo_pre_cost_trim_co_cons_dtls d where   d.po_break_down_id in($txt_po_id)   and d.status_active=1  "); //,c.item_number_id
		// echo "select d.color_size_table_id,d.item_ref  from  wo_pre_cost_trim_co_cons_dtls d where   d.po_break_down_id in($txt_po_id)   and d.status_active=1  ";
        foreach($sql_pre as $row){
			if($row[csf('item_ref')]!='')
			{
			$item_ref_color_size_arr[$row[csf('trims_dtls_id')]][$row[csf('color_size_table_id')]]['item_ref']=$row[csf('item_ref')];
			$item_ref_color_arr[$row[csf('trims_dtls_id')]][$row[csf('color_number_id')]]['item_ref'].=$row[csf('item_ref')].',';
			$item_ref_size_arr[$row[csf('trims_dtls_id')]][$row[csf('size_number_id')]]['item_ref'].=$row[csf('item_ref')].',';
			}
			 
        }
		unset($sql_pre);
		
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
			$cu_booking_data_arr[$cu_booking_data_row[csf('po_break_down_id')]][$cu_booking_data_row[csf('pre_cost_fabric_cost_dtls_id')]][$cu_booking_data_row[csf('color_number_id')]][$cu_booking_data_row[csf('gmts_sizes')]][$cu_booking_data_row[csf('article_number')]][$cu_booking_data_row[csf('item_color')]][$cu_booking_data_row[csf('item_size')]]+=$cu_booking_data_row[csf('requirment')];
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
		$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.color_number_id,min(c.color_order) as color_order,sum(c.order_quantity) as order_quantity ,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set,min(e.item_color_number_id) as item_color_number_id from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id order by b.id, color_order";
		$gmt_size_edb="disabled";
		$item_size_edb="disabled";
	}
	else if($cbo_colorsizesensitive==4){

		//$req_qty_arr=$trims->getQtyArray_by_OrderPrecostdtlsidGmtscolorAndGmtssize();
		//$req_amount_arr=$trims->getAmountArray_by_OrderPrecostdtlsidGmtscolorAndGmtssize();

		// $req_qty_arr=$trims->getQtyArray_by_OrderPrecostdtlsidGmtscolorGmtssizeAndArticle();
		// $req_amount_arr=$trims->getAmountArray_by_OrderPrecostdtlsidGmtscolorGmtssizeAndArticle();


		 $req_qty_arr=$trims->getQtyArray_by_OrderPrecostdtlsidGmtscolorGmtssizeAndArticleItemColorItemSize();
		 $req_amount_arr=$trims->getAmountArray_by_OrderPrecostdtlsidGmtscolorGmtssizeAndArticleItemColorItemSize();



		 $sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.color_number_id,c.size_number_id,c.article_number,min(c.color_order) as color_order,min(c.size_order) as size_order,e.item_size as item_size,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set,e.item_color_number_id  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c ,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id,c.size_number_id,c.article_number,e.item_color_number_id,e.item_size  order by b.id, color_order,size_order";
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
			$po_qty=$row[csf('order_quantity')];
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
				if($txtwoq_cal) 
				{
				if($row[csf('item_size')]=='' || $row[csf('item_size')]=='0') $row[csf('item_size')]=$size_library[$row[csf('size_number_id')]];
				
				$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
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
			if($cbo_colorsizesensitive==3 || $cbo_colorsizesensitive==4)
			{
				$item_color=$color_library[$row[csf('item_color_number_id')]];
			}
			else
			{
				$item_color=$color_library[$row[csf('color_number_id')]];
			}
			if($item_color=="") $item_color=0;
			
			$pre_item_color=$row[csf('item_color_number_id')];
			$pre_item_size=$row[csf('item_size')];
			
			if($pre_item_color=="") $pre_item_color=0;
			if($pre_item_size=="") $pre_item_size=0;

			$item_size=$row[csf('item_size')];
			
			$item_ref=$item_ref_color_size_arr[$cbo_trim_precost_id][$row[csf('color_size_table_id')]]['item_ref'];
			if($item_ref=="") $item_ref=0;

			if($item_size=="") $item_size=0;
			$excess=0;

			$pcs=$row[csf('order_quantity_set')];
			if($pcs=="") $pcs=0;

			$colorsizetableid=$row[csf('color_size_table_id')];
			if($colorsizetableid=="") $colorsizetableid=0;

			$articleNumber=$row[csf('article_number')];
			if($articleNumber=="") $articleNumber='no article';

			$remarks=$row[csf('remarks')];
			if($remarks=="") $remarks=0;

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
			$remark=0;
			if($txtwoq_cal>0){
				if($cons_breck_down=="")
				{
					$cons_breck_down.=$color_number_id.'_'.$size_number_id.'_'.$description.'_'.$brand_supplier.'_'.$item_color.'_'.$item_size.'_'.$txtwoq_cal.'_'.$excess.'_'.$txtwoq_cal.'_'.$txt_avg_price.'_'.$amount.'_'.$pcs.'_'.$colorsizetableid."_".$txtwoq_cal."_".$articleNumber."_".$item_ref."_".$remark."_".$pre_item_color."_".$pre_item_size;
				}
				else
				{
					$cons_breck_down.="__".$color_number_id.'_'.$size_number_id.'_'.$description.'_'.$brand_supplier.'_'.$item_color.'_'.$item_size.'_'.$txtwoq_cal.'_'.$excess.'_'.$txtwoq_cal.'_'.$txt_avg_price.'_'.$amount.'_'.$pcs.'_'.$colorsizetableid."_".$txtwoq_cal."_".$articleNumber."_".$item_ref."_".$remark."_".$pre_item_color."_".$pre_item_size;
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
		$sql="select min(b.id) as id, min(c.id) as color_size_table_id,c.color_number_id,min(c.color_order) as color_order,sum(c.order_quantity) as order_quantity,min(e.item_color_number_id) as item_color_number_id from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.color_number_id order by  color_order";
		$level_arr=$po_color_level_data_arr;
		$gmt_size_edb="disabled";
		$item_size_edb="disabled";
	}
	else if($cbo_colorsizesensitive==4){
		$sql="select min(b.id) as id ,min(c.id) as color_size_table_id,c.color_number_id,c.size_number_id,c.article_number,min(c.color_order) as color_order,min(c.size_order) as size_order,e.item_size as item_size,sum(c.order_quantity) as order_quantity,e.item_color_number_id from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.id=e.po_break_down_id  and d.id=e.wo_pre_cost_trim_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and  b.id in($txt_po_id)  $txt_country_cond and d.id=$cbo_trim_precost_id group by  c.color_number_id,c.size_number_id,c.article_number,e.item_color_number_id,e.item_size  order by  color_order,size_order";
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


			if($cbo_colorsizesensitive==3 || $cbo_colorsizesensitive==4)
			{
				$item_color=$color_library[$row[csf('item_color_number_id')]];
			}
			else
			{
				$item_color=$color_library[$row[csf('color_number_id')]];
			}

			if($item_color=="") $item_color=0;
			
			//if($item_color>0) $booking_item_color=$item_color;
			//else $booking_item_color = $row[csf('item_color_number_id')];
									
									//if(($row[csf('item_color_number_id')]=="" || $row[csf('item_color_number_id')]=="0") && ($booking_item_color=='0' || $booking_item_color=="") ) $booking_item_color = $row[csf('color_number_id')];
									 
									 
			$pre_item_color=$row[csf('item_color_number_id')];
			$pre_item_size=$row[csf('item_size')];
			 
			if($pre_item_color=="") $pre_item_color=0;
			if($pre_item_size=="") $pre_item_size=0;
			 
			
			$item_ref=$item_ref_color_size_arr[$cbo_trim_precost_id][$row[csf('color_size_table_id')]]['item_ref'];
			if($item_ref=="") $item_ref=0;

			$item_size=$row[csf('item_size')];
			if($item_size=="") $item_size=0;
			$excess=0;

			$pcs=$row[csf('order_quantity_set')];
			if($pcs=="") $pcs=0;

			$colorsizetableid=$row[csf('color_size_table_id')];
			if($colorsizetableid=="") $colorsizetableid=0;

			$articleNumber=$row[csf('article_number')];
			if($articleNumber=="") $articleNumber='no article';
			$remark=0;
			if($txtwoq_cal>0){
				if($cons_breck_down==""){
					$cons_breck_down.=trim($color_number_id).'_'.$size_number_id.'_'.$description.'_'.$brand_supplier.'_'.$item_color.'_'.$item_size.'_'.$txtwoq_cal.'_'.$excess.'_'.$txtwoq_cal.'_'.$txt_avg_price.'_'.$amount.'_'.$pcs.'_'.$colorsizetableid."_".$txtwoq_cal."_".$articleNumber."_".$item_ref."_".$remark."_".$pre_item_color."_".$pre_item_size;

				}
				else{
					$cons_breck_down.="__".trim($color_number_id).'_'.$size_number_id.'_'.$description.'_'.$brand_supplier.'_'.$item_color.'_'.$item_size.'_'.$txtwoq_cal.'_'.$excess.'_'.$txtwoq_cal.'_'.$txt_avg_price.'_'.$amount.'_'.$pcs.'_'.$colorsizetableid."_".$txtwoq_cal."_".$articleNumber."_".$item_ref."_".$remark."_".$pre_item_color."_".$pre_item_size;
				}
			}
		}
		//echo $cons_breck_down;die;
		echo $cons_breck_down."**".json_encode($level_arr); 
	}
}

if($action=="check_conversion_rate")
{
	$data=explode("**",$data);
	if($db_type==0)
	{
		$conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
	}
	else
	{
		$conversion_date=change_date_format($data[1], "d-M-y", "-",1);
	}
	$currency_rate=set_conversion_rate( $data[0], $conversion_date,$data[2] );
	echo "1"."_".$currency_rate;
	exit();
}

if ($action=="save_update_delete"){
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)
	{
		$con = connect();
		
		$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'AFB', date("Y",time()), 5, "select booking_no_prefix, booking_no_prefix_num from wo_booking_mst where company_id=$cbo_company_name and booking_type in(8) and entry_form=608 and to_char(insert_date,'YYYY')=".date('Y',time())." order by id DESC ", "booking_no_prefix", "booking_no_prefix_num" ));
		
		$id=return_next_id( "id", "wo_booking_mst", 1);
		
		$field_array="id, booking_type, entry_form, booking_no_prefix, booking_no_prefix_num, booking_no, company_id, buyer_id, item_category, uom, fabric_source, booking_date, pay_mode, supplier_id, currency_id, exchange_rate, delivery_date, source, attention, delivery_address, tenor, item_from_precost, cbo_level, ready_to_approved, remarks, inserted_by, insert_date, status_active, is_deleted";
		
		$data_array ="(".$id.",8,608,'".$new_booking_no[1]."',".$new_booking_no[2].",'".$new_booking_no[0]."',".$cbo_company_name.",".$cbo_buyer_name.",".$cbo_fabric_natu.",".$cbouom.",".$cbo_fabric_source.",".$txt_booking_date.",".$cbo_pay_mode.",".$cbo_supplier_name.",".$cbo_currency.",".$txt_exchange_rate.",".$txt_delivery_date.",".$cbo_source.",".$txt_attention.",".$txtdelivery_address.",".$txt_tenor.",".$cbo_item_from.",".$cbo_level.",".$cbo_ready_to_approved.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		
		//echo "10**insert into wo_booking_mst (".$field_array.") values ".$data_array; disconnect($con); die;
		
		$rID=sql_insert("wo_booking_mst",$field_array,$data_array,0);
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
		
		$update_id=str_replace("'","",$update_id);
		$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_booking_no");
		if($is_approved==3) $is_approved=1;
	
		if($is_approved==1){
			echo "app1**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;
		}
		
		$pi_number=return_field_value( "pi_number", "com_pi_master_details a, com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
		if($pi_number){
			echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
			disconnect($con);die;
		}
		
		$recv_number=return_field_value( "recv_number", "inv_receive_master a,inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0");
		if($recv_number){
			echo "recv1**".str_replace("'","",$txt_booking_no)."**".$recv_number;
			disconnect($con);die;
		}
		
		$flag=1;
		
		$field_array_up="uom*fabric_source*booking_date*pay_mode*supplier_id*currency_id*exchange_rate*delivery_date*source*attention*delivery_address*tenor* ready_to_approved*remarks*updated_by*update_date*revised_no";

		$data_array_up =$cbouom."*".$cbo_fabric_source."*".$txt_booking_date."*".$cbo_pay_mode."*".$cbo_supplier_name."*".$cbo_currency."*".$txt_exchange_rate."*".$txt_delivery_date."*".$cbo_source."*".$txt_attention."*".$txtdelivery_address."*".$txt_tenor."*".$cbo_ready_to_approved."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*revised_no+1";
		if($data_array_up!='')
		{
			$rID=sql_update("wo_booking_mst",$field_array_up,$data_array_up,"booking_no","".$txt_booking_no."",0);
			if($rID) $flag=1; else $flag=0;
		}

		//echo "10**".$rID_rec."**".$rID; die;
		if($db_type==2 || $db_type==1 ){
			if($rID==1 && $flag==1){
				oci_commit($con);
				echo "1**".str_replace("'","",$txt_booking_no)."**".$update_id;
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_booking_no)."**".$update_id;
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
		
		$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
		if($pi_number){
			echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
			disconnect($con);die;
		}
	
		$recv_number=return_field_value( "recv_number", "inv_receive_master a,inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		if($recv_number){
			echo "recv1**".str_replace("'","",$txt_booking_no)."**".$recv_number;
			disconnect($con);die;
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
			$rID=execute_query("update wo_booking_mst set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where booking_no=$txt_booking_no",0);
			$rID1=execute_query("update wo_booking_dtls set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where  booking_no=$txt_booking_no and status_active=1 and is_deleted=0",0);
		}
		else
		{
			$rID=1;
			$rID1=execute_query("update wo_booking_dtls set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where  booking_no=$txt_booking_no and status_active=1 and is_deleted=0",0);
		}

		if($db_type==2 || $db_type==1 ){
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

if ($action=="save_update_delete_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$con = connect();
	if( check_table_status( $_SESSION['menu_id'], 1 )==0 ){
		echo "15**0";
		 disconnect($con);die;
	}
	
	$booking_id=str_replace("'","",$update_id);
	$cbo_item_from=str_replace("'","",$cbo_item_from);
	$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_booking_no");
	$color_library=return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	if($is_approved==3) $is_approved=1;

	if($is_approved==1){
		echo "app1**".str_replace("'","",$txt_booking_no);
		 disconnect($con);die;
	}

	if ($operation==0)
	{
		$id_dtls=return_next_id( "id", "wo_booking_dtls", 1);
		$field_array1="id, booking_type, entry_form_id, booking_mst_id, booking_no, job_no, pre_cost_fabric_cost_dtls_id, po_break_down_id, body_part, color_type, construction, copmposition, gsm_weight, gmts_color_id, fabric_color_id, dia_width, fin_fab_qnty, adjust_qty, rate, amount, remark, pre_cost_remarks, hs_code, precons, inserted_by, insert_date, status_active, is_deleted";
		$j=1;
		for ($i=1; $i<=$total_row; $i++)
		{
			$txtbookingid="txtbookingid_".$i;
			$txtjob="txtjob_".$i;
			$txtpoid="txtpoid_".$i;
			$txtprecostfabriccostdtlsid="txtprecostfabriccostdtlsid_".$i;
			$txtbodypart="txtbodypart_".$i;
			$txtcolortype="txtcolortype_".$i;
			$txtwidthtype="txtwidthtype_".$i;
			$txtconstruction="txtconstruction_".$i;
			$txtcompositi="txtcompositi_".$i;
			$txtgsmweight="txtgsmweight_".$i;
			$txtgmtcolor="txtgmtcolor_".$i;
			$txtitemcolor="txtitemcolor_".$i;
		    $txtdia="txtdia_".$i;
			$txthscode="txthscode_".$i;
			$process="process_".$i;
			$txtbalqnty="txtbalqnty_".$i;
			$txtreqqnty="txtreqqnty_".$i;
			$txtfinreqqnty="txtfinreqqnty_".$i;
			$cuqnty="cuqnty_".$i;
			$preconskg="preconskg_".$i;
			$txtwoq="txtwoq_".$i;
			$txtwoqprev="txtwoqprev_".$i;
			$txtadj="txtadj_".$i;
			$txtacwoq="txtacwoq_".$i;
			$txtrate="txtrate_".$i;
			$txtamount="txtamount_".$i;
			$txtremark="txtremark_".$i;

			if(str_replace("'","",$$txtwoq)>0){
			if ($j!=1) $data_array1 .=",";
				$data_array1 .="(".$id_dtls.",8,608,".$update_id.",".$txt_booking_no.",".$$txtjob.",".$$txtprecostfabriccostdtlsid.",".$$txtpoid.",".$$txtbodypart.",".$$txtcolortype.",".$$txtconstruction.",".$$txtcompositi.",".$$txtgsmweight.",".$$txtgmtcolor.",".$$txtitemcolor.",".$$txtdia.",".$$txtwoq.",".$$txtadj.",".$$txtrate.",".$$txtamount.",".$$txtremark.",".$$process.",".$$txthscode.",".$$preconskg.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$id_dtls=$id_dtls+1;
				$j++;
			}
			//*********Pre Costing.....End******
		}
		echo "10**insert into wo_booking_dtls (".$field_array1.") values ".$data_array1; check_table_status( $_SESSION['menu_id'],0); disconnect($con); die;
		
		$rID1=sql_insert("wo_booking_dtls",$field_array1,$data_array1,0);

		check_table_status( $_SESSION['menu_id'],0);

		if($db_type==0){
			if($rID1 && $rID2  && $flag){
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
			if($rID1 && $rID2 && $flag){
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
		for ($i=1;$i<=$total_row;$i++)
		{
			$trims_del_qty='';
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
			$txtremark="txtremark_".$i;
			$txtreqamountjoblevelconsuom="txtreqamountjoblevelconsuom_".$i;
			$txtreqamountitemlevelconsuom="txtreqamountitemlevelconsuom_".$i;


			$JoBc=$$txtjob_id;
		if($cbo_item_from==1) //Pre Costing.....
		 {
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
			$txttrimgroupId=str_replace("'","",$$txttrimgroup);
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

			$curr_book_amount_job_level[str_replace("'","",$$txtjob_id)]['req_amount']=$reqAmountJobLevelArr[str_replace("'","",$$txtjob_id)];
			$curr_book_amount_job_level[str_replace("'","",$$txtjob_id)]['cur_amount']+=(str_replace("'","",$$txtamount)/str_replace("'","",$$txtexchrate));
			$curr_book_amount_job_level[str_replace("'","",$$txtjob_id)]['prev_amount']=0;

			$curr_book_amount_job_item_level[str_replace("'","",$$txtjob_id)][$poid][str_replace("'","",$$txttrimcostid)]['req_amount']+=$reqAmountItemLevelArr[$pretrimcostid];
			$curr_book_amount_job_item_level[str_replace("'","",$$txtjob_id)][$poid][str_replace("'","",$$txttrimcostid)]['cur_amount']+=(str_replace("'","",$$txtamount)/str_replace("'","",$$txtexchrate));
			$curr_book_amount_job_item_level[str_replace("'","",$$txtjob_id)][$poid][str_replace("'","",$$txttrimcostid)]['prev_amount']=0;

			$curr_book_qty_job_item_level[str_replace("'","",$$txtjob_id)][$poid][$pretrimcostid]['req_qty']+=$reqQtyItemLevelArr[$pretrimcostid];
			$curr_book_qty_job_item_level[str_replace("'","",$$txtjob_id)][$poid][$pretrimcostid]['cur_qty']+=(str_replace("'","",$$txtreqqnty)*$conversion_factor);
			$curr_book_qty_job_item_level[str_replace("'","",$$txtjob_id)][$poid][$pretrimcostid]['prev_qty']=0;
		 } //Pre Costing....End******.

			$jobArr[$$txtjob_id]=$$txtjob_id;
			$poArr[$poid]=$poid;
			$pre_trim_id_arr[$pretrimcostid]=$pretrimcostid;
			$trimgroupIdArr[$txttrimgroupId]=$txttrimgroupId;
			$des_arr[$$txtdesc]=$$txtdesc;
			$brand_arr[$$txtbrandsup]=$$txtbrandsup;
			$booking_dtls_id_arr[$bookingdtlsid]=$bookingdtlsid;
		}

		$prev_book_amount_job_level=array();
		$prev_book_amount_job_item_level=array();
		//$sql=sql_select("select  id,po_break_down_id as po_id, job_no,pre_cost_fabric_cost_dtls_id,trim_group,wo_qnty,amount,exchange_rate from wo_booking_dtls where job_no in(".implode(",",$jobArr).") and booking_type=2 and is_short=2 and status_active=1 and is_deleted=0");// and po_break_down_id in($poid)
		$sql=sql_select("select b.id ,b.job_no, b.po_break_down_id as po_id, b.pre_cost_fabric_cost_dtls_id, b.trim_group,b.wo_qnty,b.amount,b.exchange_rate from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and b.job_no in(".implode(",",$jobArr).") and b.booking_type=2 and b.is_short=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach($sql as $row){
			$curr_book_amount_job_level[$row[csf('job_no')]]['prev_amount']+=($row[csf('amount')]/$row[csf('exchange_rate')]);

			$prev_book_amount_job_level[$row[csf('id')]]['prev_amount']=($row[csf('amount')]/$row[csf('exchange_rate')]);
			$prev_book_amount_job_level[$row[csf('id')]]['prev_qty']=($row[csf('wo_qnty')]*$sql_lib_item_group_array[$row[csf('trim_group')]]['conversion_factor']);

			$curr_book_amount_job_item_level[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('pre_cost_fabric_cost_dtls_id')]]['prev_amount']+=($row[csf('amount')]/$row[csf('exchange_rate')]);
		    $curr_book_qty_job_item_level[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('pre_cost_fabric_cost_dtls_id')]]['prev_qty']+=($row[csf('wo_qnty')]*$sql_lib_item_group_array[$row[csf('trim_group')]]['conversion_factor']);

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
		if (is_duplicate_field( "booking_no", "wo_booking_dtls", "job_no in(".implode(",",$jobArr).") and po_break_down_id in (".implode(",",$poArr).") and trim_group in(".implode(",",$trimgroupIdArr).") and id not in(".implode(",",$booking_dtls_id_arr).") and booking_type=8 and booking_no=$txt_booking_no and status_active=1 and is_deleted=0 $des_data_cond $brand_data_cond") == 1)
		{
			echo "11**0";
			check_table_status( $_SESSION['menu_id'],0);
			 disconnect($con);die;
		}
		$sql_subcon_trim=sql_select("select b.job_no_mst,a.order_no,b.booking_dtls_id,b.booked_qty,b.item_group from subcon_ord_mst a,subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.order_no=$txt_booking_no and a.order_id is not null and a.entry_form=255 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");
		foreach($sql_subcon_trim as $row)
		{
			$trims_recQtyArr[$row[csf('booking_dtls_id')]]['booked_qty']=$row[csf('booked_qty')];
			$trims_recNoArr[$row[csf('booking_dtls_id')]]=$row[csf('job_no_mst')];
		}
 
 
		$field_array_up1="pre_cost_fabric_cost_dtls_id*pre_req_amt*po_break_down_id*job_no*trim_group*description*brand_supplier*uom*sensitivity*wo_qnty*exchange_rate*rate*amount*delivery_date*country_id_string*hs_code*remark*updated_by*update_date";
		$field_array_up2="id, wo_trim_booking_dtls_id, booking_no, booking_mst_id, job_no, po_break_down_id, color_number_id, gmts_sizes, description, brand_supplier, bom_item_color, bom_item_size, cons, process_loss_percent, requirment, rate, amount, pcs, color_size_table_id, article_number, item_ref, remarks, item_color, item_size";

		$add_comma=0; //echo "10**";
		$id1=return_next_id( "id", "wo_trim_book_con_dtls", 1 );
		$new_array_color=array();
		for ($i=1;$i<=$total_row;$i++)
		{
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
			$txthscode="txthscode_".$i;
			$txtbrandsup="txtbrandsup_".$i;
			$txtreqamount="txtreqamount_".$i;
			$txtremarks="txtremarks_".$i;
			$txtreqamountjoblevelconsuom="txtreqamountjoblevelconsuom_".$i;
			$txtreqamountitemlevelconsuom="txtreqamountitemlevelconsuom_".$i;
			$poid=str_replace("'","",$$txtpoid);
			$pi_number=array();
			$piquantity=0;
			$sqlPi=sql_select("select a.pi_number, b.quantity from  com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id  and b.work_order_no=$txt_booking_no and b.item_group=".$$txttrimgroup." and b.order_id in($poid) and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
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
			
			$ex_book_dtls_id=explode(",",str_replace("'",'',$$txtbookingid));
				$order_trim_recv_qty=0;
				foreach($ex_book_dtls_id as $book_dtls_id)
				{
					$order_trim_recv_qty+=$trims_recQtyArr[$book_dtls_id]['booked_qty'];
					$trims_recNoCheckArr[$book_dtls_id]=$trims_recNoArr[$book_dtls_id];
				}
				
			if($order_trim_recv_qty && str_replace("'","",$$txtwoq) < $order_trim_recv_qty){
				echo "orderFound**".str_replace("'","",$txt_booking_no)."**".implode(",",$trims_recNoCheckArr)."**".$order_trim_recv_qty;
				check_table_status( $_SESSION['menu_id'],0);
				 disconnect($con);die;
			}
			

			$recv_number=array(); $recvquantity=0;
			if(str_replace("'","",$$txtdesc)=="" || str_replace("'","",$$txtdesc)==0) $desc_con=""; else $desc_con=" and a.item_description=".$$txtdesc." ";
			if(str_replace("'","",$$txtpoid)=="" || str_replace("'","",$$txtpoid)==0) $poid_con=""; else $poid_con=" and b.po_breakdown_id in (".str_replace("'","",$$txtpoid).") ";
			/*$sqlRecv=sql_select("select a.recv_number, c.quantity as receive_qnty from inv_receive_master a,inv_trims_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and c.trans_type=1 and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and b.item_group_id=".$$txttrimgroup." and c.po_breakdown_id in($poid) and a.item_category=4 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 $desc_con $poid_con");*/
			
			
			$prev_rcv_sql="select m.recv_number, b.po_breakdown_id, a.item_group_id, a.item_description, a.brand_supplier, a.gmts_color_id, a.item_color, a.gmts_size_id, a.item_size, b.quantity as qnty, a.rate as rate
			from inv_receive_master m, inv_trims_entry_dtls a, order_wise_pro_details b 
			where m.id=a.mst_id and a.id=b.dtls_id and b.trans_type=1 and m.booking_no=a.booking_no and m.booking_no=$txt_booking_no and a.item_group_id=".$$txttrimgroup." and b.po_breakdown_id in($poid) and m.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $desc_con $poid_con";
			//echo "10**".$prev_rcv_sql; check_table_status( $_SESSION['menu_id'],0);die;
			$sqlRecv=sql_select($prev_rcv_sql);
			foreach($sqlRecv as $rowRecv){
				$recv_number[$rowRecv[csf('recv_number')]]=$rowRecv[csf('recv_number')];
				$recvquantity+=$rowRecv[csf('qnty')];
				
				$prev_rcv_data[$rowRecv[csf("po_breakdown_id")]][$rowRecv[csf("item_group_id")]][trim($rowRecv[csf("item_description")])][$rowRecv[csf("brand_supplier")]][$rowRecv[csf("gmts_color_id")]][$rowRecv[csf("item_color")]][$rowRecv[csf("gmts_size_id")]][$rowRecv[csf("item_size")]]["rate"]=$rowRecv[csf("rate")];
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
			$poid=str_replace("'","",$$txtpoid);
			$trimcostid=str_replace("'","",$$txttrimcostid);
			$amt=str_replace("'","",$$txtamount);
			$exRate=str_replace("'","",$$txtexchrate);
		 if($cbo_item_from==1) //Pre Costing.....
		 {
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
				
				$curAmt=number_format($curAmt,2,'.','');
				$reqAmt=number_format($reqAmt,2,'.','');

				//$curAmt=$curr_book_amount_job_level[$job]['prev_amount']+($amt/$exRate);
				if($curAmt<$reqAmt){
					echo "vad1**".str_replace("'","",$txt_booking_no)."**".$i;
					check_table_status( $_SESSION['menu_id'],0);
					 disconnect($con);die;
				}
				$curr_book_amount_job_level[$job]['prev_amount']+=($amt/$exRate);
			}
			if($amount_exceed_level==2){
				$reqAmt=(($curr_book_amount_job_item_level[$job][$poid][$trimcostid]['req_amount']*$exeed_budge_amount)/100)+$curr_book_amount_job_item_level[$job][$poid][$trimcostid]['req_amount'];
				$pre_amt=0;
				$ex_book_dtls_id=explode(",",str_replace("'",'',$$txtbookingid));
				foreach($ex_book_dtls_id as $book_dtls_id)
				{
					$pre_amt+=$prev_book_amount_job_level[$book_dtls_id]['prev_amount'];
				}

				$curAmt=($curr_book_amount_job_item_level[$job][$poid][$trimcostid]['prev_amount']-$pre_amt)+($amt/$exRate);
				$curAmt=number_format($curAmt,2,'.','');
				$reqAmt=number_format($reqAmt,2,'.','');
				
				if($curAmt<$reqAmt){
					echo "vad1**".str_replace("'","",$txt_booking_no)."**".$i."**".$curAmt."**".$reqAmt."**".$curr_book_amount_job_item_level[$job][$poid][$trimcostid]['req_amount'];
					check_table_status( $_SESSION['menu_id'],0);
					 disconnect($con);die;
				}
					//echo "vad1**".str_replace("'","",$txt_booking_no)."**".$i."**".$curAmt."**".$reqAmt."**".$curr_book_amount_job_item_level[$job][$poid][$trimcostid]['req_amount'];
				$curr_book_amount_job_item_level[$job][$poid][$trimcostid]['prev_amount']+=($amt/$exRate);
			}


			if($exceed_qty_level==2){
				$reqQty=(($curr_book_qty_job_item_level[$job][$poid][$trimcostid]['req_qty']*$exeed_budge_qty)/100)+$curr_book_qty_job_item_level[$job][$poid][$trimcostid]['req_qty'];
				$curQty=$curr_book_qty_job_item_level[$job][$poid][$trimcostid]['prev_qty']+($woq*$conversion_factor);
				$curQty=number_format($curQty,2,'.','');
				$reqQty=number_format($reqQty,2,'.','');
				
				 
					if($curQty<$reqQty){
					echo "vad2**".str_replace("'","",$txt_booking_no)."**".$i."**".$curQty."**".$reqQty."**".$curr_book_amount_job_item_level[$job][$poid][$trimcostid]['req_qty'];
					check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);die;
				}
				$curr_book_qty_job_item_level[$job][$poid][$trimcostid]['prev_qty']+=($woq*$conversion_factor);
			}
		 }// Library --pre costing end.....

			if(str_replace("'",'',$$txtbookingid)!=""){
				$id_arr=array();
				$data_array_up1=array();
				$id_arr[]=str_replace("'",'',$$txtbookingid);
				$data_array_up1[str_replace("'",'',$$txtbookingid)] =explode("*",("".$$txttrimcostid."*".$$txtReqAmt."*".$$txtpoid."*".$$txtjob_id."*".$$txttrimgroup."*".$$txtdesc."*".trim($$txtbrandsup)."*".$$txtuom."*".$$cbocolorsizesensitive."*".$$txtwoq."*".$$txtexchrate."*".$$txtrate."*".$$txtamount."*".$$txtddate."*".$$txtcountry."*".$$txthscode."*".$$txtremark."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

				//	CONS break down===============================================================================================
				if(str_replace("'",'',$$consbreckdown) !=''){
					$data_array_up2="";
					$rID_de1=execute_query( "delete from wo_trim_book_con_dtls where  wo_trim_booking_dtls_id =".$$txtbookingid."",0);
					$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
					for($c=0;$c < count($consbreckdown_array);$c++){
						$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
						 $consbreckdownarr[17]=trim(str_replace("'",'',$consbreckdownarr[17]));
						/* if(str_replace("'","",$consbreckdownarr[17])=='0') $consbreckdownarr[17]='';
						if(str_replace("'",'',$consbreckdownarr[17]) !='')
						{
							if (!in_array(str_replace("'","",$consbreckdownarr[17]),$new_array_color, TRUE)){
								$color_id = return_id_lib_common( str_replace("'","",$consbreckdownarr[17]), $color_library, "lib_color", "id,color_name","87");
								$new_array_color[$color_id]=str_replace("'","",$consbreckdownarr[17]);
							}
							else $color_id =  array_search(str_replace("'","",$consbreckdownarr[17]), $new_array_color);
						}
						else $color_id =0;*/
						
						$color_id =$consbreckdownarr[17];
						
						 $consbreckdownarr[4]=trim(str_replace("'",'',$consbreckdownarr[4]));
						 if(str_replace("'","",$consbreckdownarr[4])=='0') $consbreckdownarr[4]='';
						if(str_replace("'",'',$consbreckdownarr[4]) !='')
						{
							if (!in_array(str_replace("'","",$consbreckdownarr[4]),$new_array_color, TRUE)){
								$booking_color_id = return_id_lib_common( str_replace("'","",$consbreckdownarr[4]), $color_library, "lib_color", "id,color_name","555");
								$new_array_color[$booking_color_id]=str_replace("'","",$consbreckdownarr[4]);
							}
							else $booking_color_id =  array_search(str_replace("'","",$consbreckdownarr[4]), $new_array_color);
						}
						else $booking_color_id =0;
						
						$prev_rcvrate=$prev_rcv_data[str_replace("'","",$$txtpoid)][str_replace("'","",$$txttrimgroup)][trim($consbreckdownarr[2])][$consbreckdownarr[3]][$consbreckdownarr[0]][$booking_color_id][$consbreckdownarr[1]][$consbreckdownarr[5]]["rate"];
						if($prev_rcvrate=="") $prev_rcvrate=0;
						//echo $poId.'='.str_replace("'","",$$txttrimgroup).'='.trim($consbreckdownarr[2]).'='.$consbreckdownarr[3].'='.$consbreckdownarr[0].'='.$booking_color_id.'='.$consbreckdownarr[1].'='.$consbreckdownarr[5].'<br>';
						//echo $prev_rcvrate.'='.str_replace("'","",$consbreckdownarr[9]).'<br>';
						
						if($prev_rcvrate>0)
						{
							if($prev_rcvrate>0 && $prev_rcvrate==(str_replace("'","",$consbreckdownarr[9])*1))
							{
								check_table_status( $_SESSION['menu_id'],0);
							}
							else
							{
								echo "recvRate1**".str_replace("'","",$txt_booking_no)."**".implode(",",$recv_number)."**".$prev_rcvrate."**".$consbreckdownarr[9];
								oci_rollback($con);
								check_table_status( $_SESSION['menu_id'],0);
								disconnect($con);die;
							}
						}


						if ($c!=0) $data_array_up2 .=",";
						$data_array_up2 .="(".$id1.",".$$txtbookingid.",".$txt_booking_no.",".$booking_id.",".$$txtjob_id.",".$$txtpoid.",'".$consbreckdownarr[0]."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".trim($consbreckdownarr[3])."','".$color_id."','".$consbreckdownarr[5]."','".$consbreckdownarr[6]."','".$consbreckdownarr[7]."','".$consbreckdownarr[8]."','".$consbreckdownarr[9]."','".$consbreckdownarr[10]."','".$consbreckdownarr[11]."','".$consbreckdownarr[12]."','".$consbreckdownarr[14]."','".$consbreckdownarr[15]."','".$consbreckdownarr[16]."','".$booking_color_id."','".$consbreckdownarr[5]."')";
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
		//check_table_status( $_SESSION['menu_id'],0); disconnect($con);die;
		$rID=execute_query( "update wo_booking_mst set revised_no=revised_no+1 where  booking_no=$txt_booking_no",0);
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0){
			if($rID1 &&  $rID2 && $flag){
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		else if($db_type==2 || $db_type==1 ){
			if($rID1 &&  $rID2 && $flag){
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
			$txtreqqnty="txtreqqnty_".$i;
			//if(str_replace("'","",$cbo_pay_mode)==2){
			$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no and b.item_group=".$$txttrimgroup." and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			    if($pi_number){
				    echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
				     disconnect($con);die;
			    }
			//}else{
				
			if(str_replace("'","",$$txtdesc)=="" || str_replace("'","",$$txtdesc)==0) $desc_con=""; else $desc_con=" and b.item_description=".$$txtdesc." ";
			$recv_number=return_field_value( "recv_number", "inv_receive_master a,inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and b.item_group_id=".$$txttrimgroup." and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0 $desc_con");
			    if($recv_number){
				    echo "recv1**".str_replace("'","",$txt_booking_no)."**".$recv_number;
				     disconnect($con);die;
			    }
			//}
			$bookingdtlsid=str_replace("'","",$$txtbookingid);
			$subcon_job=return_field_value( "subcon_job", "subcon_ord_mst a, subcon_ord_dtls b"," a.entry_form=255 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and b.booking_dtls_id in ($bookingdtlsid) and a.status_active=1 and b.status_active=1");
		    if(!empty($subcon_job)){
			     echo "orderFound**".$subcon_job."**SELECT subcon_job from subcon_ord_mst a, subcon_ord_dtls b where a.entry_form=255 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and b.booking_dtls_id in ($bookingdtlsid) and a.status_active=1 and b.status_active=1";
			     oci_rollback($con);
			     disconnect($con);die;
		    }
		    
			
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
			if($rID1 &&  $rID2 && $flag){
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}

		if($db_type==2 || $db_type==1 ){
			if($rID1 &&  $rID2 && $flag){
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
	$booking_mst_id=str_replace("'","",$update_id);
	$item_from_id=str_replace("'","",$cbo_item_from);
	$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_booking_no");
	if($is_approved==3) $is_approved=1;
	if($is_approved==1){
		echo "app1**".str_replace("'","",$txt_booking_no);
		 disconnect($con);die;
	}
	$strdata=json_decode(str_replace("'","",$strdata));	
	if ($operation==0)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$id_dtls=return_next_id( "id", "wo_booking_dtls", 1);
		$field_array1="id, booking_type, entry_form_id, booking_mst_id, booking_no, job_no, pre_cost_fabric_cost_dtls_id, po_break_down_id, body_part, color_type, construction, copmposition, gsm_weight, gmts_color_id, fabric_color_id, dia_width, fin_fab_qnty, grey_fab_qnty, adjust_qty, rate, amount, remark, pre_cost_remarks, hs_code, precons, inserted_by, insert_date, status_active, is_deleted";
		$j=1;
		for ($i=1; $i<=$total_row; $i++)
		{
			$txtbookingid="txtbookingid_".$i;
			$txtjob="txtjob_".$i;
			$txtpoid="txtpoid_".$i;
			$txtprecostfabriccostdtlsid="txtprecostfabriccostdtlsid_".$i;
			$txtbodypart="txtbodypart_".$i;
			$txtcolortype="txtcolortype_".$i;
			$txtwidthtype="txtwidthtype_".$i;
			$txtconstruction="txtconstruction_".$i;
			$txtcompositi="txtcompositi_".$i;
			$txtgsmweight="txtgsmweight_".$i;
			$txtgmtcolor="txtgmtcolor_".$i;
			$txtitemcolor="txtitemcolor_".$i;
		    $txtdia="txtdia_".$i;
			$txthscode="txthscode_".$i;
			$process="process_".$i;
			$txtbalqnty="txtbalqnty_".$i;
			$txtreqqnty="txtreqqnty_".$i;
			$txtfinreqqnty="txtfinreqqnty_".$i;
			$cuqnty="cuqnty_".$i;
			$preconskg="preconskg_".$i;
			$txtwoq="txtwoq_".$i;
			$txtwoqprev="txtwoqprev_".$i;
			$txtadj="txtadj_".$i;
			$txtacwoq="txtacwoq_".$i;
			$txtrate="txtrate_".$i;
			$txtamount="txtamount_".$i;
			$txtremark="txtremark_".$i;

			if(str_replace("'","",$$txtwoq)>0){
			if ($j!=1) $data_array1 .=",";
				$data_array1 .="(".$id_dtls.",8,608,".$update_id.",".$txt_booking_no.",".$$txtjob.",".$$txtprecostfabriccostdtlsid.",".$$txtpoid.",".$$txtbodypart.",".$$txtcolortype.",".$$txtconstruction.",".$$txtcompositi.",".$$txtgsmweight.",".$$txtgmtcolor.",".$$txtitemcolor.",".$$txtdia.",".$$txtwoq.",".$$txtwoq.",".$$txtadj.",".$$txtrate.",".$$txtamount.",".$$txtremark.",".$$process.",".$$txthscode.",".$$preconskg.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$id_dtls=$id_dtls+1;
				$j++;
			}
			//*********Pre Costing.....End******
		}
		//echo "10**insert into wo_booking_dtls (".$field_array1.") values ".$data_array1; check_table_status( $_SESSION['menu_id'],0); disconnect($con); die;
		
		$rID1=sql_insert("wo_booking_dtls",$field_array1,$data_array1,0);
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0){
			if($rID1){
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
			if($rID1){
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
	if($operation==1){

	}
}

if($action=="delete_dtls_data")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if ($operation==2)
	{
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
		if($is_received_id!='')
		{
			echo "orderFound**".str_replace("'","",$txt_booking_no)."**$is_received_id"; disconnect($con); die;
			//echo "orderFound**".str_replace("'","",$txt_booking_no)."**".implode(",",$trims_recNoCheckArr)."**".$order_trim_recv_qty;
		}
		$delete_cause=str_replace("'","",$delete_cause);
		$delete_cause=str_replace('"','',$delete_cause);
		$delete_cause=str_replace('(','',$delete_cause);
		$delete_cause=str_replace(')','',$delete_cause);

		//$rID1=execute_query( "delete from wo_booking_dtls where  id in (".str_replace("'","",$$txtbookingid).")",0);
		//$rID2=execute_query( "delete from wo_trim_book_con_dtls where  wo_trim_booking_dtls_id in(".str_replace("'","",$$txtbookingid).")",0);
		$flag=1; //echo "10**";
		for ($i=1;$i<=$total_row;$i++){
			//$txttrimcostid="txttrimcostid_".$i;
			//$txttrimgroup="txttrimgroup_".$i;
			//$txtpoid="txtpoid_".$i;
			$txtbookingid="txtbookingid_".$i;
			//$txtdesc="txtdesc_".$i;
			//$txtreqqnty="txtreqqnty_".$i;
			if(str_replace("'","",$$txtbookingid)!="")
			{
			//echo "update wo_booking_dtls set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where id in (".str_replace("'","",$$txtbookingid).") and booking_no=$txt_booking_no";
			//echo "update wo_trim_book_con_dtls set status_active=0,is_deleted=1 where wo_trim_booking_dtls_id in(".str_replace("'","",$$txtbookingid).") and booking_no=$txt_booking_no";
			$rID1=execute_query("update wo_booking_dtls set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where id in (".str_replace("'","",$$txtbookingid).") and booking_no=$txt_booking_no",0);
			$rID2=execute_query("update wo_trim_book_con_dtls set status_active=0,is_deleted=1 where wo_trim_booking_dtls_id in(".str_replace("'","",$$txtbookingid).") and booking_no=$txt_booking_no",0);
			}
		}
		
		//echo "10**".$rID1.'='.$rID2; die;
		
		if($db_type==0){
			if($rID1 &&  $rID2 && $flag){
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		else if($db_type==2 || $db_type==1 ){
			if($rID1 &&  $rID2 && $flag){
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

if ($action=="fabric_booking_popup")
{
	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//extract(check_magic_quote_gpc($_REQUEST));

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
        <div align="center" style="width:1000px;" >
        <input type="hidden" id="txt_booking" value="" />
            <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table width="1000" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                <thead>
                    <tr>
                        <th colspan="11" align="center"><?=create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" ); ?></th>
                    </tr>
                    <tr>
                        <th width="140" class="must_entry_caption">Company Name</th>
                        <th width="130" class="must_entry_caption">Buyer Name</th>
                        <th width="70">Booking No</th>
                        <th width="90">Style Ref.</th>
                        <th width="70">Job No</th>
                        <th width="70">Internal Ref.</th>
                        <th width="80">Order No</th>
                        <th width="130">Supplier Name</th>
                        <th width="120" colspan="2"> Booking Date Range</th>
                        <th><input type="checkbox" value="0" onClick="set_checkvalue();" id="chk_job_wo_po">WO Without PO</th>
                    </tr>
                </thead>
                <tr class="general">
                    <td><?=create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company_id, "load_drop_down( 'multi_job_additional_fabric_booking_controller', this.value, 'load_drop_down_buyer_pop', 'buyer_td' );"); ?></td>
                    <td id="buyer_td"><?=create_drop_down( "cbo_buyer_name", 130, "select id,buyer_name from lib_buyer where status_active =1 and is_deleted=0 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", "", "",0,"" ); ?></td>
                    <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:60px"></td>
                    <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:80px"></td>
                    <td><input name="txt_job" id="txt_job" class="text_boxes" style="width:60px"></td>
                    <td><input name="internal_ref" id="internal_ref" class="text_boxes" style="width:80px"></td>
                    <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:80px"></td>
                    <td><?=create_drop_down( "cbo_supplier_name", 120, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(9) and a.status_active =1 and a.is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "",0 ); ?></td>
                    
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date"></td>
                    <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" placeholder="To Date"></td>
                    <td align="center">
                    	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_job').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('internal_ref').value, 'create_booking_search_list_view', 'search_div', 'multi_job_additional_fabric_booking_controller','setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
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
    </html>
    <?
    exit();
}

if ($action=="create_booking_search_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; disconnect($con); die; }
	if ($data[1]!=0){
		 $buyer=" and c.buyer_name='$data[1]'";
		 $buyer2=" and a.buyer_id='$data[1]'";
	}
	else 
	{
		$buyer=set_user_lavel_filtering(' and c.buyer_name','buyer_id');
		$buyer2=set_user_lavel_filtering(' and a.buyer_id','buyer_id');
	}
	if ($data[2]!=0) $supplier_id=" and a.supplier_id='$data[2]'"; else $supplier_id ="";
	if($db_type==0){
		$booking_year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[5]";
		$job_year_cond=" and SUBSTRING_INDEX(c.insert_date, '-', 1)=$data[5]";
	
		if ($data[3]!="" &&  $data[4]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	else if($db_type==2){
		$booking_year_cond=" and to_char(a.insert_date,'YYYY')=$data[5]";
		$job_year_cond=" and to_char(c.insert_date,'YYYY')=$data[5]";
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
		if (str_replace("'","",$data[10])!="") $job_cond=" and c.job_no_prefix_num like '%$data[10]%' $job_year_cond "; //else  $order_cond="";
	}
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$user_arr=return_library_array("select id,user_full_name from user_passwd","id","user_full_name");

	if($data[11]==0)
	{
		$internal_ref_con='';
		if(!empty($data[12]))
		{
			$internal_ref_con=" and d.grouping like '%$data[12]%'";
		}

		$sql="SELECT a.id, a.pay_mode, a.booking_no_prefix_num, a.inserted_by, a.booking_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, c.style_ref_no, d.po_number from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c, wo_po_break_down d where a.booking_no=b.booking_no and b.job_no=c.job_no and b.job_no=d.job_no_mst and c.id=d.job_id and b.po_break_down_id=d.id and a.booking_type=8 and a.entry_form=608 and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 $company  $buyer  $supplier_id $booking_date $booking_cond $style_cond $order_cond $job_cond $internal_ref_con group by a.id,a.pay_mode, a.booking_no_prefix_num,a.inserted_by, a.booking_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date,c.style_ref_no,d.po_number  order by a.id DESC";
	}
	else
	{
		$internal_ref_con='';
		if(!empty($data[12]))
		{
			$internal_ref_con=" and c.grouping like '%$data[12]%'";
		}

		$sql="SELECT a.id,a.pay_mode, a.job_no, a.inserted_by, a.booking_no_prefix_num, a.booking_no, company_id, a.supplier_id, a.booking_date, a.delivery_date from wo_booking_mst a where a.booking_no not in ( select a.booking_no from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c where a.booking_no=b.booking_no  and b.po_break_down_id=c.id and a.booking_type=8 and a.entry_form=608 and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 $company ".set_user_lavel_filtering(' and a.buyer_id','buyer_id')." $supplier_id $booking_date $booking_cond $job_cond $file_cond $ref_cond $internal_ref_con group by a.booking_no_prefix_num, a.booking_no, company_id, a.supplier_id, a.booking_date, a.delivery_date ) and a.booking_type=8 and a.entry_form=608 and  a.status_active =1 and a.is_deleted=0 $company $buyer2 $supplier_id $booking_date $booking_cond group by a.id, a.pay_mode, a.booking_no_prefix_num,a.inserted_by, a.booking_no, a.job_no, company_id, a.supplier_id, a.booking_date, a.delivery_date order by a.id DESC";
		//".set_user_lavel_filtering(' and a.buyer_id','buyer_id')."
	}
	//echo $sql;
	$booking_data = sql_select($sql);
	if(count($booking_data)>0){
		foreach ($booking_data as $row) {
			if($row[csf("pay_mode")]==3 || $row[csf("pay_mode")]==5)
			{

				$supplier_or_company[$row[csf("id")]]=$comp[$row[csf("supplier_id")]];
			}
			else
			{
				$supplier_or_company[$row[csf("id")]]=$suplier[$row[csf("supplier_id")]];
			}
		}

	}
	/*echo '<pre>';
	print_r($supplier_or_company); die;*/

	if($data[11]==0)
	{
		$arr=array (1=>$comp,2=>$supplier_or_company,7=>$user_arr);
		echo  create_list_view("list_view", "Booking No,Company,Supplier,Booking Date,Delivery Date,Style Ref No,Po Number,Insert User", "60,100,100,70,150,150,120","940","300",0, $sql , "js_set_value", "booking_no", "", 1, "0,company_id,id,0,0,0,0,inserted_by", $arr , "booking_no_prefix_num,company_id,id,booking_date,delivery_date,style_ref_no,po_number,inserted_by", '','','0,0,0,3,3,0,0,0','','');
	}
	else{
		$arr=array (1=>$comp,2=>$supplier_or_company,5=>$user_arr);
		echo  create_list_view("list_view", "Booking No,Company,Supplier,Booking Date,Delivery Date,Insert User", "120,100,100,100,100","700","300",0, $sql , "js_set_value", "booking_no", "", 1, "0,company_id,id,0,0,inserted_by", $arr , "booking_no_prefix_num,company_id,id,booking_date,delivery_date,inserted_by", '','','0,0,0,3,3,0','','');
	}

	exit();
}

if ($action=="populate_data_from_search_popup_booking")
{
	 $sql= "select id, booking_no, company_id, buyer_id, item_category, uom, fabric_source, booking_date, pay_mode, supplier_id, currency_id, exchange_rate, delivery_date, source, attention, delivery_address, tenor, item_from_precost, cbo_level, ready_to_approved, remarks from wo_booking_mst where booking_no='$data' and status_active =1 and is_deleted=0 and booking_type=8 and entry_form=608";

	$data_array=sql_select($sql);
	foreach ($data_array as $row){
		echo "get_php_form_data(".$row[csf("company_id")].", 'populate_variable_setting_data', 'requires/multi_job_additional_fabric_booking_controller' );\n";
		echo "document.getElementById('txt_booking_no').value = '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('update_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";
		
		echo "document.getElementById('cbo_fabric_natu').value = '".$row[csf("item_category")]."';\n";
		echo "document.getElementById('cbouom').value = '".$row[csf("uom")]."';\n";
		echo "document.getElementById('cbo_fabric_source').value = '".$row[csf("fabric_source")]."';\n";
		echo "document.getElementById('txt_booking_date').value = '".change_date_format($row[csf("booking_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('cbo_pay_mode').value = '".$row[csf("pay_mode")]."';\n";
		$paymodeData=$row[csf("pay_mode")].'_'.$row[csf("buyer_id")].'_'.$row[csf("company_id")];
		echo "load_drop_down( 'requires/multi_job_additional_fabric_booking_controller', '".$paymodeData."', 'load_drop_down_supplier', 'supplier_td' );\n";
		echo "document.getElementById('cbo_supplier_name').value = '".$row[csf("supplier_id")]."';\n";
		echo "document.getElementById('cbo_currency').value = '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value = '".$row[csf("exchange_rate")]."';\n";
		echo "document.getElementById('txt_delivery_date').value = '".change_date_format($row[csf("delivery_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('cbo_source').value = '".$row[csf("source")]."';\n";
		echo "document.getElementById('txt_attention').value = '".$row[csf("attention")]."';\n";
		if($row[csf("delivery_address")]!="")
		{
		$d_address=preg_replace('/\s+/', ' ', trim($row[csf("delivery_address")]))."";
		}
		echo "document.getElementById('txtdelivery_address').value = '".($d_address)."';\n";
		echo "document.getElementById('txt_tenor').value = '".$row[csf("tenor")]."';\n";
		echo "document.getElementById('cbo_item_from').value = '".$row[csf("item_from_precost")]."';\n";
		echo "document.getElementById('cbo_level').value = '".$row[csf("cbo_level")]."';\n";
		echo "document.getElementById('cbo_ready_to_approved').value = '".$row[csf("ready_to_approved")]."';\n";
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n";
		
		if($row[csf("is_approved")]==3){
			$is_approved=1;
		}else{
			$is_approved=$row[csf("is_approved")];
		}
		echo "document.getElementById('id_approved_id').value = '".$is_approved."';\n";
		
		echo " $('#cbo_company_name').attr('disabled',true);\n";
		echo " $('#cbo_supplier_name').attr('disabled',true);\n";
		echo " $('#cbo_level').attr('disabled',true);\n";
		echo " $('#cbo_item_from').attr('disabled',true);\n";
		echo " $('#cbo_buyer_name').attr('disabled',true);\n";
		
		//echo "fnc_show_booking_list();\n";

		if($row[csf("is_approved")]==1)
		{
			echo "document.getElementById('app_sms2').innerHTML = 'This booking is approved';\n";
			echo "document.getElementById('txt_un_appv_request').disabled = '".false."';\n";
		}
		elseif($row[csf("is_approved")]==3)
		{
			echo "document.getElementById('app_sms2').innerHTML = 'This booking is partial approved';\n";
			echo "document.getElementById('txt_un_appv_request').disabled = '".false."';\n";
		}
		else
		{
			echo "document.getElementById('app_sms2').innerHTML = '';\n";
			echo "document.getElementById('txt_un_appv_request').disabled = '".true."';\n";
		}
	}
	exit();
}

//================================================report Start=====================================================

if ($action=="unapp_request_popup"){
	$menu_id=$_SESSION['menu_id'];
	$user_id=$_SESSION['logic_erp']['user_id'];

	echo load_html_head_contents("Un Approval Request","../../../", 1, 1, $unicode);
	//extract($_REQUEST);
	extract(check_magic_quote_gpc($_REQUEST));

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
				http.open("POST","multi_job_additional_fabric_booking_controller.php",true);
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
	echo "10**=";die;
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


if($action=="labeldtls_popup")
{
	echo load_html_head_contents("Label Details Pop Up","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $zipperdtls;
	?>
	<script>
		function js_set_value()
		{
			
			var label_break_data="";
			
			
				label_break_data=$('#txtfabrication').val()+'___'+$('#txtcaresymbol').val()+'___'+$('#txtoekotexno').val();
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
					<? list($id,$group_id,$i)=explode("_",$data);
						$labeldtlsdata=return_field_value("labeldtlsdata", "wo_booking_dtls", "id=$id");
						list($fabrication,$caresymbol,$oekotexno)=explode("___",$labeldtlsdata);
                    ?>
                    <tr style="text-decoration:none; cursor:pointer"> 
                        <td align="center"><input type="text" name="txtfabrication" id="txtfabrication" value="<?=$fabrication; ?>" style="width:118px;" class="text_boxes" placeholder="Write"/></td>
                        <td style="word-break:break-all" align="center"><input type="text" name="txtcaresymbol" id="txtcaresymbol" value="<?=$caresymbol; ?>" style="width:118px;" class="text_boxes" placeholder="Write"/></td>
                        <td style="word-break:break-all" align="center"><input type="text" name="txtoekotexno" id="txtoekotexno" value="<?=$oekotexno; ?>" style="width:118px;" class="text_boxes" placeholder="Write"/></td>
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


if($action=="show_fabric_booking_report") // md mamun ashmed sagor-24-09-2022 || issue id=21969
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
		$job_no_str = "'" . implode( "','", $job_no ) . "'";
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
		$nameArray_job=sql_select( "select max(a.update_date) as update_date,b.job_no_mst,b.id,b.pub_shipment_date, b.po_number,b.grouping, b.file_no, sum(b.po_quantity) as po_quantity  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no='$txt_booking_no' and  a.status_active =1 and a.is_deleted=0 group by b.job_no_mst,b.id,b.pub_shipment_date, b.po_number,b.grouping, b.file_no ");
        foreach ($nameArray_job as $result_job){
			$po_no_arr[$result_job[csf('id')]]=$result_job[csf('po_number')];
			$job_file_no[$result_job[csf('id')]]=$result_job[csf('file_no')];
			$job_ref_no[$result_job[csf('id')]]=$result_job[csf('grouping')];
			$pub_shipment_date.=$result_job[csf('pub_shipment_date')].',';
			if($po_idss=='') $po_idss=$result_job[csf('id')];else $po_idss.=",".$result_job[csf('id')];
			$revised_update_date=explode(" ",$result_job[csf('update_date')]);
			$revised_date=strtotime($revised_update_date[0]);

		}
		$sql_job=sql_select( "select b.job_no_mst,b.id as po_id, b.po_quantity as po_quantity  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and  a.status_active =1 and a.is_deleted=0 and  b.status_active =1 and b.is_deleted=0 and b.id in(".$po_idss.") ");
		foreach ($sql_job as $row)
		{
			$job_po_qty_arr[$row[csf('po_id')]]+=$row[csf('po_quantity')];
			$tot_po_quantity+=$row[csf('po_quantity')];
		}

        $nameArray=sql_select( "select a.booking_no, a.pay_mode,a.buyer_id, a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_address, a.delivery_date, a.source, a.remarks, a.revised_no,a.update_date from wo_booking_mst a where a.booking_no='$txt_booking_no' and a.status_active =1 and a.is_deleted=0");
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
			$mst_update_date=explode(" ",$row[csf('update_date')]);
			$mst_revised_date=strtotime($mst_update_date[0]);


			$source_id=$row[csf('source')];
		}
		if($revised_date!="" && $mst_revised_date!="")
		{
			$max_revised_date_time=max($revised_date,$mst_revised_date);
			$max_revised_date=date('d-m-Y',$max_revised_date_time);
		}
		else if($revised_date!="" && $mst_revised_date=="")
		{
			//$max_revised_date=$revised_date;
			$max_revised_date=date('d-m-Y',$revised_date);
		}
		else if($mst_revised_date!="" && $revised_date=="")
		{

			$max_revised_date=date('d-m-Y',$mst_revised_date);
		}
		$main_fabric_approved = sql_select("select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and a.job_no in (".$job_no_str.") and b.entry_form=7 and a.status_active =1 and a.is_deleted=0 order by b.id asc");
	
	ob_start();
	
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
                            <td>  Revised No:&nbsp; <?php echo $revised_no . '&nbsp(Date:' . change_date_format($max_revised_date) . ')'; ?>  </td>
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
						echo $delivery_address;
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
					<td width="110" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;" >:&nbsp;<? echo $remarks; ?></td>
					<td width="130" style="font-size:12px" align="left"><b>Main Fabric Booking 1<sup>st</sup> Approved Date</b></td>
                	<td align="left">:&nbsp;<?  echo change_date_format($main_fabric_approved[0][csf('approved_date')]); ?></td>

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

		$nameArray_job_po=sql_select( "select job_no,po_break_down_id as po_id from wo_booking_dtls  where booking_no='$txt_booking_no' and status_active =1 and is_deleted=0 group by job_no,po_break_down_id order by job_no,po_break_down_id ");
		foreach($nameArray_job_po as $nameArray_job_po_row){
		$nameArray_item=sql_select( "select  pre_cost_fabric_cost_dtls_id,trim_group,po_break_down_id as po_id from wo_booking_dtls  where booking_no='$txt_booking_no' and  status_active =1 and is_deleted=0 and job_no='".$nameArray_job_po_row[csf('job_no')]."'  and po_break_down_id=".$nameArray_job_po_row[csf('po_id')]."   and sensitivity=1 group by pre_cost_fabric_cost_dtls_id,trim_group,po_break_down_id  order by trim_group ");
	    if(count($nameArray_item)>0){

			$ref_nos=$job_ref_no[$nameArray_job_po_row[csf('po_id')]];
			$file_nos=$job_file_no[$nameArray_job_po_row[csf('po_id')]];
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
                <td width="60%" align="left"><strong>As Per Garments Color (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]]; if($ref_nos!='' ) echo " &nbsp;Int Ref.:&nbsp;".$ref_nos;else " "; if($file_nos!='' || $file_nos!=0 ) echo " &nbsp;File No.:&nbsp;".$file_nos;else " "; echo " &nbsp;  Po Qty.:&nbsp;".$po_no_qty; ?></strong></td>
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
            $nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid, b.description,b.article_number, b.brand_supplier,b.item_color,a.gmts_color_id,b.gmts_sizes,b.item_size,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_id')]."  and a.sensitivity=1  and a.trim_group=".$result_item[csf('trim_group')]."  and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id,b.item_color,a.gmts_color_id,b.gmts_sizes,b.item_size, b.description,b.article_number, b.brand_supplier,b.item_color order by bid ");

			// and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]."
			
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
				echo number_format($amount_as_per_gmts_color,2);
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
			$file_nos=$job_file_no[$nameArray_job_po_row[csf('po_id')]];
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
                <td width="60%"><strong>Size Sensitive (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]]; echo "&nbsp;&nbsp;Int Ref.:".$ref_nos; if($file_nos!='' || $file_nos!=0 ) echo " &nbsp;File No.:&nbsp;".$file_nos;else " "; echo "&nbsp;&nbsp; Po Qty..:".$po_no_qty; ?></strong></td>
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
			$nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid, b.description, b.brand_supplier,b.item_size,b.article_number,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and   a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$result_item[csf('po_id')]." and a.sensitivity=2 and a.trim_group=".$result_item[csf('trim_group')]." and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.item_size,b.article_number order by b.article_number,bid");
			// and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]."
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
				echo number_format($amount_as_per_gmts_color,2);
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
			$file_nos=$job_file_no[$nameArray_job_po_row[csf('po_id')]];
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
                <td width="60%"><strong>Contrast Color (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]];echo " &nbsp;Int Ref.:&nbsp;".$ref_nos; if($file_nos!='' || $file_nos!=0 ) echo " &nbsp;File No.:&nbsp;".$file_nos;else " ";echo " &nbsp;Po Qty.:&nbsp;".$po_no_qty;?></strong></td>
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
				$gmtcolor_library=return_library_array( "select b.item_color,b.color_number_id from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$result_item[csf('po_id')]."  and a.sensitivity=3  and a.trim_group=".$result_item[csf('trim_group')]."  and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by trim_group ", "item_color", "color_number_id"  );
				// and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]."
			$nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid,b.description, b.article_number,b.brand_supplier,b.item_color,b.color_number_id,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$result_item[csf('po_id')]." and a.sensitivity=3  and a.trim_group=".$result_item[csf('trim_group')]."  and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.description, b.article_number,b.brand_supplier,b.item_color,b.color_number_id order by bid ");
			// and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]."
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
                $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,2);
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
			$file_nos=$job_file_no[$nameArray_job_po_row[csf('po_id')]];
			$po_no_qty=0;
			$po_no_qty=$job_po_qty_arr[$nameArray_job_po_row[csf('po_id')]];
			$po_delivery_date=$po_delivery_date_arr[$nameArray_job_po_row[csf('po_id')]];

        ?>

        <table border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="14" align="">
                <table width="100%" style="table-layout: fixed;">
                <tr>
                <td width="60%" ><strong>Color & size sensitive (<? echo "Job NO: ".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]] ;echo "&nbsp;&nbsp;Int Ref.:&nbsp;".$ref_nos; if($file_nos!='' || $file_nos!=0 ) echo " &nbsp;File No.:&nbsp;".$file_nos;else " "; echo "&nbsp;&nbsp;Po Qty.:&nbsp;".$po_no_qty;?></strong></td>
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

				$gmtcolor_library=return_library_array( "select b.item_color,b.color_number_id from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.po_break_down_id=".$result_item[csf('po_id')]." and a.sensitivity=4    and a.trim_group=".$result_item[csf('trim_group')]."  and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by trim_group ", "item_color", "color_number_id"  );
	
			 $nameArray_color=sql_select( "select a.pre_cost_fabric_cost_dtls_id,min(b.id) as bid, b.color_number_id as gmt_color, b.item_color as color_number_id,b.item_size,b.gmts_sizes,b.description,b.article_number, b.brand_supplier,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount, min(c.color_order) as color_order, min(c.size_order) as size_order,c.article_number from wo_booking_dtls a, wo_trim_book_con_dtls b,wo_po_color_size_breakdown c  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and b.po_break_down_id=c.po_break_down_id and b.color_number_id=c.color_number_id and b.gmts_sizes=c.size_number_id and  c.id=b.color_size_table_id and a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'   and a.po_break_down_id=".$result_item[csf('po_id')]." and a.trim_group=".$result_item[csf('trim_group')]."  and a.sensitivity=4 and b.requirment !=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.color_number_id, b.item_color,b.item_size,b.gmts_sizes,b.description,b.article_number, b.brand_supplier,c.article_number order by c.article_number,color_order,size_order");



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
					echo number_format($amount_as_per_gmts_color,2);
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
			$file_nos=$job_file_no[$nameArray_job_po_row[csf('po_id')]];
			$po_no_qty=0;
			$po_no_qty=$job_po_qty_arr[$nameArray_job_po_row[csf('po_id')]];
			$po_delivery_date=$po_delivery_date_arr[$nameArray_job_po_row[csf('po_id')]];
        ?>
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="14" align="">
                <table width="100%" style="table-layout: fixed;">
                <tr>
                <td width="60%"><strong>NO sensitive  (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Style NO:".$style_ref[$nameArray_job_po_row[csf('job_no')]];echo " &nbsp;Int Ref.:&nbsp;".$ref_nos; if($file_nos!='' || $file_nos!=0 ) echo " &nbsp;File No.:&nbsp;".$file_nos;else " ";  echo " &nbsp;Po Qty.:&nbsp;".$po_no_qty; ?></strong></td>
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

				$nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id, b.description,b.article_number, b.brand_supplier,b.item_color,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.trim_group=".$result_item[csf('trim_group')]." and a.po_break_down_id=".$result_item[csf('po_id')]."  and a.sensitivity=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.description,b.article_number, b.brand_supplier,b.item_color");

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
						$nameArray_color_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trim_group=". $result_item[csf('trim_group')]." and a.po_break_down_id=". $result_item[csf('po_id')]."  and  b.description='". $result_itemdescription[csf('description')]."' and b.brand_supplier='".$result_itemdescription[csf('brand_supplier')]."' and b.item_color='".$result_itemdescription[csf('item_color')]."'");
						}
						if($db_type==2)
				        {
						$nameArray_color_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trim_group=". $result_item[csf('trim_group')]."  and a.po_break_down_id=". $result_item[csf('po_id')]."  and nvl( b.description,0)=nvl('". $result_itemdescription[csf('description')]."',0) and nvl(b.brand_supplier,0)=nvl('".$result_itemdescription[csf('brand_supplier')]."',0) and nvl(b.item_color,0)=nvl('".$result_itemdescription[csf('item_color')]."',0)");
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
                $amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,2);
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
			
			
			$mailSql = "SELECT c.email_address as EMAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=97 and b.mail_user_setup_id=c.id  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
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
			$subject="Trims Booking Multy Job";
			$header=mailHeader();
			echo sendMailMailer( $to, $subject, $mailBody, $from_mail,$att_file_arr );
			
		}
		else{
			echo $html;
		}
		exit();
}
?>