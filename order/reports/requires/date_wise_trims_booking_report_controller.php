<?
/*-------------------------------------------- Comments -----------------------
Purpose         :   This Report Will Create Knit Garments Date Wise Fabric Booking.
Functionality   :
JS Functions    :
Created by      :   Zakaria
Creation date   :   31-07-2019
Updated by      :
Update date     :
QC Performed BY :   Ashique
QC Date         :   31-07-19
Comments        :
*/
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer"){
    echo create_drop_down( "cbo_buyer_id", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company in($data) and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","0","" );
    exit();
}

if ($action == "fabricBooking_popup")
{
	echo load_html_head_contents("WO Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	//if($recieve_basis==1) $width=1045; else $width=1055;
	$width = 1055;
	?>
	
	 <script>
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
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
			var splitSTR = strCon.split("_");
			var str = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			//$('#txt_individual_id' + str).val(splitSTR[1]);
			//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
			
			toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( selectID, selected_id ) == -1 ) {
				selected_id.push( selectID );
				selected_name.push( selectDESC );
				selected_no.push( str );				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == selectID ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				selected_no.splice( i, 1 ); 
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
			$('#hidden_booking_id').val( id );
			$('#hidden_booking_no').val( name ); 
			//$('#txt_selected_no').val( num );
		}
    </script>
    </head>
    <body>
        <div align="center" style="width:600x;">
            <form name="searchwofrm" id="searchwofrm" autocomplete=off>
                <fieldset style="width:600px;">
                    <legend>Enter search words</legend>
                    <table cellpadding="0" cellspacing="0" border="1" rules="all" width="600" class="rpt_table">
                        <thead>
                        	<tr>
								<th colspan="5"><? echo create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                            </tr>
                            <tr>
                                <th width="60">Year</th>
                                <th width="150">Buyer</th>
                                <th width="150">Search By</th>
                                <th id="search_by_td_up" width="140">Enter Booking No</th>
                                <th>
                                    <input type="reset" name="reset" id="reset" value="Reset" style="width:70px" class="formbutton"/>
                                    <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<?=$cbo_company_id?>">
                                    <input type="hidden" name="hidden_booking_id" id="hidden_booking_id" class="text_boxes" value="">
                                    <input type="hidden" name="hidden_booking_no" id="hidden_booking_no" class="text_boxes" value="">
                                    <input type="hidden" name="hidden_booking_type" id="hidden_booking_type" class="text_boxes" value="<?= $cbo_fab_booking_type ?>">
                                </th>
                            </tr>
                        </thead>
                        <tr class="general">
                            <td><?=create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?></td>
                            <td>
                                <?
                                    $search_by_arr = array(1 => "Booking No", 2 => "Buyer Order", 3 => "Job No", 4 => "Style Ref.", 5 => "Internal Ref");
                                    $dd = "change_search_event(this.value, '0*0*0*0*0', '0*0*0*0*0', '../../') ";
    
                                echo create_drop_down("cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($cbo_company_id) $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name", "id,buyer_name", 1, "-- All Buyer --", '', "", $disable); ?>
                            </td>
                            <td><?=create_drop_down("cbo_search_by", 150, $search_by_arr, "", 0, "--Select--", 5, $dd, 0); ?></td>
                            <td id="search_by_td"><input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common"/></td>
                            <td><input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_year').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('hidden_booking_type').value+'_'+'<? echo $txt_booking_no; ?>'+'_'+'<? echo $txt_booking_no_id; ?>', 'create_booking_search_list_view', 'search_div', 'date_wise_trims_booking_report_controller', 'setFilterGrid(\'tbl_list_search\',-1);');" style="width:70px;"/>
                            </td>
                        </tr>
                    </table>
                    
                </fieldset>
            </form>
            <div id="search_div" align="left"></div>
			
        </div>
    </body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if ($action == "create_booking_search_list_view")
{
	$data = explode("_", $data);
	$search_string =trim($data[0]);
	$search_by = $data[1];
	$company_id = $data[2];
	$bookingType = $data[6];
	$buyer_id = $data[3];
	$booking_year = $data[4];
	$string_search_type=str_replace("'","",$data[5]);
	$txt_booking_no_no=str_replace("'","",$data[6]);
	$txt_booking_no_id=str_replace("'","",$data[7]);

	$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');

	//if ($buyer_id == 0) $buyer_id = "%%";
	$buyer_cond='';
	$buyer_cond_s='';
	if(!empty($buyer_id))
	{
		$buyer_cond=" and a.buyer_id  in(".$buyer_id.")";
		$buyer_cond_s=" and s.buyer_id  in(".$buyer_id.")";
	}
	
	$search_field_cond = ""; $search_field_cond_sample = "";
	if($string_search_type==1)
	{
		if (trim($data[0]) != "") {
			if ($search_by == 1) {
				$search_field_cond = "and a.booking_no='$search_string'";
				$search_field_cond_sample = "and s.booking_no_prefix_num='".trim($data[0])."'";
			} 
			else if ($search_by == 2) $search_field_cond = "and b.po_number='$search_string'";
			else if ($search_by == 3) $search_field_cond = "and b.job_no_mst='$search_string'";
			else if ($search_by == 5) $search_field_cond = "and b.grouping='$search_string'";
			else $search_field_cond = "and d.style_ref_no='$search_string'";
		} else {
			$search_field_cond = ""; $search_field_cond_sample = "";
		}
	}
	else if($string_search_type==2)
	{
		if (trim($data[0]) != "") {
			if ($search_by == 1) {
				$search_field_cond = "and a.booking_no like '$search_string%'";
				$search_field_cond_sample = "and s.booking_no_prefix_num='".trim($data[0])."'";
			} 
			else if ($search_by == 2) $search_field_cond = "and b.po_number like '$search_string%'";
			else if ($search_by == 3) $search_field_cond = "and b.job_no_mst like '$search_string%'";
			else if ($search_by == 5) $search_field_cond = "and b.grouping like '$search_string%'";
			else $search_field_cond = "and d.style_ref_no like '$search_string%'";
		} else {
			$search_field_cond = ""; $search_field_cond_sample = "";
		}
	}
	else if($string_search_type==3)
	{
		if (trim($data[0]) != "") {
			if ($search_by == 1) {
				$search_field_cond = "and a.booking_no like '%$search_string'";
				$search_field_cond_sample = "and s.booking_no_prefix_num='".trim($data[0])."'";
			} 
			else if ($search_by == 2) $search_field_cond = "and b.po_number like '%$search_string'";
			else if ($search_by == 3) $search_field_cond = "and b.job_no_mst like '%$search_string'";
			else if ($search_by == 5) $search_field_cond = "and b.grouping like '%$search_string'";
			else $search_field_cond = "and d.style_ref_no like '%$search_string'";
		} else {
			$search_field_cond = ""; $search_field_cond_sample = "";
		}
	}
	else if($string_search_type==4 || $string_search_type==0)
	{
		if (trim($data[0]) != "") {
			if ($search_by == 1) {
				$search_field_cond = "and a.booking_no like '%$search_string%'";
				$search_field_cond_sample = "and s.booking_no_prefix_num='".trim($data[0])."'";
			} 
			else if ($search_by == 2) $search_field_cond = "and b.po_number like '%$search_string%'";
			else if ($search_by == 3) $search_field_cond = "and b.job_no_mst like '%$search_string%'";
			else if ($search_by == 5) $search_field_cond = "and b.grouping like '%$search_string%'";
			else $search_field_cond = "and d.style_ref_no like '%$search_string%'";
		} else {
			$search_field_cond = ""; $search_field_cond_sample = "";
		}
	}
	
	$po_arr = array();
	$po_data = sql_select("select b.id, b.po_number, b.pub_shipment_date, b.grouping, b.file_no, b.po_quantity, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach ($po_data as $row) {
		$po_arr[$row[csf('id')]] = $row[csf('po_number')] . "**" . $row[csf('pub_shipment_date')] . "**" . $row[csf('po_quantity')] . "**" . $row[csf('po_qnty_in_pcs')] . "**" . $row[csf('grouping')];
	}
	$year_cond = "";
	$year_cond_non_order = "";
	$booking_year_condition="";
	$booking_year_non_order_condition="";
	
	
	$year_cond = "to_char(a.insert_date,'YYYY') as year";
	$year_cond_non_order = "to_char(s.insert_date,'YYYY') as year";
	if($booking_year>0)
	{
		$booking_year_condition=" and to_char(a.insert_date,'YYYY')=$booking_year";
		$booking_year_non_order_condition=" and to_char(s.insert_date,'YYYY')=$booking_year";
	}

	$booking_type_cond="";
	if($bookingType==1){
		$booking_type_cond="and a.booking_type=2 and a.is_short=2";
	}
	elseif($bookingType==2){
		$booking_type_cond="and a.booking_type=2 and a.is_short=1";
	}

	$sql = "SELECT a.id, a.booking_no,a.booking_no_prefix_num, a.booking_date, a.buyer_id, a.entry_form, c.po_break_down_id, a.item_category, a.delivery_date, c.job_no as job_no_mst, 0 as type,a.booking_type, a.is_short, $year_cond from wo_booking_mst a, wo_booking_dtls c, wo_po_break_down b, wo_po_details_master d where a.booking_no=c.booking_no and c.po_break_down_id=b.id and d.id=b.job_id and a.company_id in($company_id) $buyer_cond and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $search_field_cond $booking_year_condition $booking_type_cond group by a.id,a.booking_no, a.booking_date, a.buyer_id,a.entry_form,c.po_break_down_id,a.item_category, a.delivery_date, c.job_no,a.insert_date,a.booking_no_prefix_num, a.booking_type,a.is_short order by a.id";
	$result = sql_select($sql);
	$po_id_arr = $booking_arr = array(); $job_ids = "";
	foreach ($result as $value) {
		$po_id_arr[$value[csf("booking_no")]] .= $value[csf("po_break_down_id")] . ",";
		$booking_arr[$value[csf("booking_no")]] = $value[csf("id")] . "**" . $value[csf("booking_no")] . "**" . $value[csf("booking_no_prefix_num")] . "**" . $value[csf("booking_date")] . "**" . $value[csf("buyer_id")] . "**" . $value[csf("item_category")] . "**" . $value[csf("delivery_date")] . "**" . $value[csf("job_no_mst")] . "**" . $value[csf("type")] . "**" . $value[csf("booking_type")] . "**" . $value[csf("is_short")] . "**" . $value[csf("year")]. "**" . $value[csf("entry_form")];
	}
	?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1030" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="60">Booking No</th>
				<th width="70">Type</th>
				<th width="50">Year</th>
				<th width="75">Booking Date</th>
				<th width="60">Buyer</th>
				<th width="88">Item Category</th>
				<th width="75">Delivary date</th>
				<th width="80">Job No</th>
				<th width="70">Order Qty</th>
				<th width="75">Shipment Date</th>
				<th width="130">Order No</th>
				<th width="70">Internal Ref</th>
				<th>File No</th>
			</thead>
		</table>
		<div style="width:1050px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1030" class="rpt_table"
			id="tbl_list_search">
			<?
			$i = 1;
			foreach ($booking_arr as $row) {
				$data = explode("**",$row);
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
	
				$booking_type = '';
				if ($data[9] == 0)  $booking_type = 'Sample Without Order'; else if ($data[9] == 4) $booking_type = 'Sample'; 
				else {
					if ($data[10] == 1) $booking_type = 'Short'; else $booking_type = 'Main';
				}
	
				$po_qnty_in_pcs = '';
				$po_no = '';
				$min_shipment_date = '';
				$internal_ref = '';
				$file_nos = '';
				if ($data[1] != "" && $data[8] == 0) {
					$po_id = explode(",", rtrim($po_id_arr[$data[1]],","));
					foreach ($po_id as $id) {
						$po_data = explode("**", $po_arr[$id]);
						$po_number = $po_data[0];
						$pub_shipment_date = $po_data[1];
						$po_qnty = $po_data[2];
						$poQntyPcs = $po_data[3];
						$grouping = $po_data[4];
						$file_no = $po_data[5];
	
						if ($po_no == "") $po_no = $po_number; else $po_no .= "," . $po_number;
						if ($grouping!= "") {
							if ($internal_ref == "") $internal_ref = $grouping; else $internal_ref .= "," . $grouping;
						}
						if ($file_no != "") {
							if ($file_nos == "") $file_nos = $file_no; else $file_nos .= "," . $file_no;
						}
	
						if ($min_shipment_date == '') {
							$min_shipment_date = $pub_shipment_date;
						} else {
							if ($pub_shipment_date < $min_shipment_date) $min_shipment_date = $pub_shipment_date; else $min_shipment_date = $min_shipment_date;
						}
	
						$po_qnty_in_pcs += $poQntyPcs;
					}
				}
	
				$internal_ref = implode(",", array_unique(explode(",", $internal_ref)));
				$file_nos = implode(",", array_unique(explode(",", $file_nos)));
				?>
				<tr bgcolor="<?=$bgcolor; ?>" id="tr_<?=$i; ?>" style="text-decoration:none; cursor:pointer"
					onClick="js_set_value('<?=$i.'_'.$data[0].'_'.$data[1]; ?>');">
					<td width="30"><?=$i; ?></td>
					<td width="60" align="left" style="word-break:break-all"><? echo $data[2]; ?></td>
					<td width="70" align="center" style="word-break:break-all"><? echo $booking_type; ?></td>
					<td width="50" align="center" style="word-break:break-all"><? echo $data[11]; ?></td>
					<td width="75" align="center" style="word-break:break-all"><? echo change_date_format($data[3]); ?></td>
					<td width="60" style="word-break:break-all"><? echo $buyer_arr[$data[4]]; ?></td>
					<td width="88" style="word-break:break-all"><? echo $item_category[$data[5]]; ?></td>
					<td width="75" align="center" style="word-break:break-all"><? echo change_date_format($data[6]); ?></td>
					<td width="80" style="word-break:break-all"><? echo $data[7]; ?></td>
					<td width="70" align="right" style="word-break:break-all"><? echo $po_qnty_in_pcs; ?></td>
					<td width="75" align="center" style="word-break:break-all"><? echo change_date_format($min_shipment_date); ?></td>
					<td width="130" style="word-break:break-all"><? echo $po_no; ?></td>
					<td width="70" style="word-break:break-all"><? echo $internal_ref; ?></td>
					<td style="word-break:break-all"><? echo $file_nos; ?></td>
				</tr>
				<?
				$i++;
			}
			?>
		</table>
		<div style="width:50%; float:left" align="left">
		<input type="checkbox" name="check_all" id="check_all" onclick="check_all_data()"> Check / Uncheck All
	 </div>
	 <div style="width:50%; float:left" align="left">
		<input type="button" name="close" id="close" onclick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px">
		</div>
	 <script language="javascript" type="text/javascript">
		var style_no='<? echo $txt_booking_no_no;?>';
		var style_id='<? echo $txt_booking_no_id;?>';
		//var style_des='<?// echo $txt_style_ref;?>';
		//alert(style_id);
		if(style_no!="")
		{
			style_no_arr=style_no.split(",");
			style_id_arr=style_id.split(",");
		//	style_des_arr=style_des.split(",");
			var str_ref="";
			for(var k=0;k<style_no_arr.length; k++)
			{
				str_ref=style_no_arr[k]+'_'+style_id_arr[k];
				js_set_value(str_ref);
			}
		}
	</script>
	</div>
	<?
	exit();
}

if($action=="report_setting_variables"){
	$data = explode("__", $data);
	if($data[0] == 87){//booking type main
		$report_id = 26;
	}elseif($data[0] == 178){//booking type short
		$report_id = 15;
	}else{//Unknown business.
		exit();
	}
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data[1]."' and module_id=2 and report_id='".$report_id."' and is_deleted=0 and status_active=1");
	$defaultButtons = explode(",", $print_report_format);
	if($defaultButtons[0] == 28){$action  = "show_trim_booking_report13";}
	if($defaultButtons[0] == 437){$action = "show_trim_booking_report27";}
	if($defaultButtons[0] == 20){$action  = "show_trim_booking_report";}
    if(!empty($action)){
		echo trim($action); 
	}else{
		echo ""; 
	}
	
	exit();
}

if ($action=="report_generate")
{
    extract($_REQUEST);
    $cbo_company_id=str_replace("'","",$cbo_company_id);
    $cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
    $cbo_booking_type=str_replace("'","",$cbo_fab_booking_type);
    $txt_date_from=str_replace("'","",$txt_date_from);
    $txt_date_to=str_replace("'","",$txt_date_to);
    $txt_booking_no_id=str_replace("'","",$txt_booking_no_id);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$based_on=str_replace("'","",$cbo_based_on);

    $companyArr = return_library_array("select id,company_name from lib_company ","id","company_name");
    $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
	$colorArr = return_library_array( "select id, color_name from lib_color", "id", "color_name");
    $supplierArr = return_library_array("select a.id,a.supplier_name from lib_supplier a,lib_supplier_tag_company b,lib_company c where a.id=b.supplier_id and c.id=b.tag_company and a.status_active =1 and a.is_deleted=0 and c.core_business not in(3) group by a.id,a.supplier_name","id","supplier_name");
	$lib_user=return_library_array("select id,user_full_name from user_passwd","id","user_full_name");
	$sub_deptArr = return_library_array("select id,sub_department_name from lib_pro_sub_deparatment where  status_active =1 and is_deleted=0 order by sub_department_name","id","sub_department_name");
    $is_short = array(1=>'Short',2=>'Main');
    $fabric_booking_type = array(87 => "Main", 178 => "Short" , 272 => "Woven Main");
	
	if($type==0)//show Button
	{
			if($cbo_booking_type == 1) $type_cond = " and e.booking_type=2 and e.is_short=2 and e.entry_form in (87,272)";
			if($cbo_booking_type == 2) $type_cond = " and e.booking_type=2 and e.is_short=1";
			if($cbo_company_id) $company_cond=" and e.company_id in($cbo_company_id)"; else $company_cond="";
			if($cbo_buyer_id) $buyerCond=" and e.buyer_id in($cbo_buyer_id)"; else $buyerCond="";
			if($based_on==1){
				if($txt_date_from!="" && $txt_date_to!="") $booking_date_cond="and e.booking_date between '$txt_date_from' and '$txt_date_to'"; else $booking_date_cond="";
			}
			else{
				if($txt_date_from!="" && $txt_date_to!="") $booking_date_cond="and g.pub_shipment_date between '$txt_date_from' and '$txt_date_to'"; else $booking_date_cond="";
			}
			
	
			if($txt_booking_no_id!='')
			{
				if ($txt_booking_no_id=="") $bookingCond=""; else $bookingCond=" and e.id in ( $txt_booking_no_id )";
			}
			else
			{
				if ($txt_booking_no=="") $bookingCond=""; else $bookingCond=" and e.booking_no like '%$txt_booking_no%'";
			}

		   $get_booking = "Select e.booking_no, e.booking_date,e.company_id, a.insert_date, e.update_date, e.is_approved, e.is_short, e.short_booking_type, e.buyer_id, a.job_no, a.style_ref_no, a.client_id, e.item_category, e.fabric_source, e.source, e.inserted_by, e.supplier_id, sum(f.grey_fab_qnty) as booking_qty, sum(f.wo_qnty) as trims_qty, f.responsible_person, f.reason, sum(f.amount) as booking_amount, g.grouping, e.entry_form, nvl(e.po_break_down_id,f.po_break_down_id) as po_break_down_id,a.product_dept,g.shipment_date from wo_booking_dtls f join wo_booking_mst e on f.booking_no=e.booking_no join wo_po_details_master a on a.job_no = f.job_no join wo_booking_mst e on f.booking_no=e.booking_no join wo_po_break_down g on f.po_break_down_id=g.id where f.status_active=1 and f.is_deleted=0 and a.is_deleted=0 and a.status_active=1 and e.is_deleted=0 and e.status_active=1 and g.is_deleted=0 and g.status_active=1 and e.booking_type=2 $company_cond $buyerCond $type_cond $booking_date_cond $bookingCond  group by e.booking_no, e.booking_date, e.is_short, e.short_booking_type, e.buyer_id, a.job_no, e.update_date, e.is_approved, a.style_ref_no, a.client_id, e.item_category,e.company_id, e.fabric_source, e.source, e.inserted_by, e.supplier_id, f.responsible_person, f.reason,e.entry_form, a.insert_date, e.po_break_down_id, f.po_break_down_id, g.grouping, a.product_dept,g.shipment_date  order by e.booking_date asc";
		//echo $get_booking;die;
		$sql_data=sql_select($get_booking);
		$booking_date_arr =array();
		$po_break_down_ids="";
		foreach ($sql_data as $value) {
			$booking_date_arr[$value[csf('booking_no')]]['booking_no'] = $value[csf('booking_no')];
			$booking_date_arr[$value[csf('booking_no')]]['update_date'] = $value[csf('update_date')];
			$booking_date_arr[$value[csf('booking_no')]]['company_id'] = $value[csf('company_id')];
			$booking_date_arr[$value[csf('booking_no')]]['insert_date'] = $value[csf('insert_date')];
			$booking_date_arr[$value[csf('booking_no')]]['is_approved'] = $value[csf('is_approved')];
			$booking_date_arr[$value[csf('booking_no')]]['booking_date'] = $value[csf('booking_date')];
			$booking_date_arr[$value[csf('booking_no')]]['shipment_date'] = $value[csf('shipment_date')];
			$booking_date_arr[$value[csf('booking_no')]]['booking_type'] = $value[csf('booking_type')];
			$booking_date_arr[$value[csf('booking_no')]]['trims_qty'] = $value[csf('trims_qty')];
			$booking_date_arr[$value[csf('booking_no')]]['short_booking_type'] = $value[csf('short_booking_type')];
			$booking_date_arr[$value[csf('booking_no')]]['short_booking_type'] = $value[csf('short_booking_type')];
			$booking_date_arr[$value[csf('booking_no')]]['buyer_id'] = $value[csf('buyer_id')];
			$booking_date_arr[$value[csf('booking_no')]]['grouping'] = $value[csf('grouping')];
			$booking_date_arr[$value[csf('booking_no')]]['style_ref_no'] = $value[csf('style_ref_no')];
			$booking_date_arr[$value[csf('booking_no')]]['client_id'] = $value[csf('client_id')];
			$booking_date_arr[$value[csf('booking_no')]]['job_no'].=$value[csf('job_no')].',';
			$booking_date_arr[$value[csf('booking_no')]]['item_category'] = $value[csf('item_category')];
			$booking_date_arr[$value[csf('booking_no')]]['fabric_source'] = $value[csf('fabric_source')];
			$booking_date_arr[$value[csf('booking_no')]]['source'] = $value[csf('source')];
			$booking_date_arr[$value[csf('booking_no')]]['inserted_by'] = $value[csf('inserted_by')];
			$booking_date_arr[$value[csf('booking_no')]]['supplier_id'] = $value[csf('supplier_id')];
			$booking_date_arr[$value[csf('booking_no')]]['entry_form'] = $value[csf('entry_form')];
			$booking_date_arr[$value[csf('booking_no')]]['product_dept'] = $product_dept[$value[csf('product_dept')]];
			$booking_date_arr[$value[csf('booking_no')]]['po_break_down_id'] = $value[csf('po_break_down_id')];
			$booking_date_arr[$value[csf('booking_no')]]['booking_amount'] += $value[csf('booking_amount')];
		}


	
		ob_start();
		?>
		<div align="center">
			<table width="1400px" cellspacing="0">
				<tr class="form_caption" style="border:none;">
					<td colspan="15" align="center" style="border:none;font-size:14px; font-weight:bold" ><? echo $report_title; ?></td>
				</tr>
				<tr style="border:none;">
					<td colspan="15" align="center" style="border:none; font-size:16px; font-weight:bold"><? echo $companyArr[$cbo_company_id]; ?></td>
				</tr>
			</table>
			<table width="1400px" cellspacing="0" border="1" class="rpt_table" rules="all">
				<thead>
					<tr style="font-size:13px">
						<th style="word-wrap: break-word;" width="30">SL.</th>
						<th style="word-wrap: break-word;" width="100">Company</th>
						<th style="word-wrap: break-word;" width="100">Booking Type</th>
						<th style="word-wrap: break-word;" width="100">Trims Wo No</th>
						<th style="word-wrap: break-word;" width="70">Trims Wo Insert Date</th>
						<th style="word-wrap: break-word;" width="100">Trims Wo Qty</th>
						<th style="word-wrap: break-word;" width="100">IR/IB</th>
						<th style="word-wrap: break-word;" width="100">Job No</th>
						<th style="word-wrap: break-word;" width="100">Shipment Date</th>
						<th style="word-wrap: break-word;" width="100">Buyer</th>
						<th style="word-wrap: break-word;" width="100">Style Ref.</th>
						<th style="word-wrap: break-word;" width="100">Product Dept.</th>
						<th style="word-wrap: break-word;" width="100">Trims Supplier</th>
						<th style="word-wrap: break-word;" width="100">Source</th>
						<th style="word-wrap: break-word;" width="100">User Name</th>

					 </tr>
				</thead>
			</table>
			 <div style="width:1420px; max-height:300px; overflow-y:scroll" id="scroll_body">
			<table width="1400px" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_body">
				<tbody>
					<? $i=1; $tot_rows=0;
					$total_booking_qty_kg =0;
					foreach ($booking_date_arr as $row) {
						//echo $row[12]; die;
						$jobNos=implode(",",array_filter(array_unique(explode(",",$row['job_no']))));
						$booking_qty_kg = 0; $booking_qty_mtr=0; $booking_qty_yds = 0;
						 $total_booking_amount = $row['booking_amount'];
		
						 if($row['entry_form'] == ''){
							$entry_form = 0;
						 }
						 else{
							$entry_form = $row['entry_form'];
						 }
						$is_approved=$row['is_approved'];						
						
						?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
							<td align="center" style="word-wrap: break-word;" width="30"><? echo $i; ?></td>
							<td align="center" style="word-wrap: break-word;" width="100"><? echo $companyArr[$row['company_id']]; ?></td>
							<td align="center" style="word-wrap: break-word;" width="100"><? echo $fabric_booking_type[$entry_form]; ?></td>
							<td align="center" style="word-wrap: break-word;" width="100" title="<? echo $is_approved;?>">
						 		<a href="#" onclick='trims_order_report(<?=$entry_form;?>,"<?=$row["booking_no"];?>",<?=$row["company_id"];?>);'><? echo $row['booking_no']; ?></a>
							</td>
							<td align="center" style="word-wrap: break-word;" width="70" title="<? echo $row['booking_date'];?>"><? echo change_date_format($row['booking_date'], "d-M-y", "-", 1); ?></td>
							<td align="right" style="word-wrap: break-word;" width="100"><? echo fn_number_format($row['trims_qty'],2); ?></td>
							<td align="center" style="word-wrap: break-word;" width="100"><? echo $row['grouping']; ?></td>
							<td align="center" style="word-wrap: break-word;" width="100"><? echo $jobNos; ?></td>
							<td align="center" style="word-wrap: break-word;" width="100" title="<? echo $row['shipment_date'];?>"><? echo change_date_format($row['shipment_date'], "d-M-y", "-", 1); ?></td>
							<td align="center" style="word-wrap: break-word;" width="100"><? echo $buyerArr[$row['buyer_id']]; ?></td>
							<td align="center" style="word-wrap: break-word;" width="100"><? echo $row['style_ref_no']; ?></td>
							<td align="center" style="word-wrap: break-word;" width="100"><? echo $row['product_dept']; ?></td>
							<td align="center" style="word-wrap: break-word;" width="100"><? echo $supplierArr[$row['supplier_id']]; ?></td>
							<td align="center" style="word-wrap: break-word;" width="100"><? echo $source[$row['source']]; ?></td>
							<td align="center" style="word-wrap: break-word;" width="100"><? echo $lib_user[$row['inserted_by']]; ?></td>
						</tr>
		
					  <? $i++; $tot_rows++;$total_booking_qty_kg+=$row['trims_qty'];
					} ?>
				</tbody>
			</table>
            </div>
			<table width="1400px" cellspacing="0" border="1" class="rpt_table" rules="all" id="report_table_footer" >
				<tfoot>
					<tr style="font-size:13px">
						<th bgcolor= "#A0A6AC" width="30"></th>
						<th bgcolor= "#A0A6AC" width="100"></th>
						<th bgcolor= "#A0A6AC" width="100"></th>
						<th bgcolor= "#A0A6AC" width="100"></th>
						<th bgcolor= "#A0A6AC" width="70"></th>					
						<th bgcolor= "#A0A6AC" style="word-wrap: break-word;" width="100" id="total_booking_qty_kg" align="right"><? echo number_format($total_booking_qty_kg,2); ?></th>
						<th bgcolor= "#A0A6AC" width="100"></th>
						<th bgcolor= "#A0A6AC" width="100"></th>
						<th bgcolor= "#A0A6AC" width="100"></th>
						<th bgcolor= "#A0A6AC" width="100"></th>
						<th bgcolor= "#A0A6AC" width="100"></th>
						<th bgcolor= "#A0A6AC" width="100"></th>
						<th bgcolor= "#A0A6AC" width="100"></th>
						<th bgcolor= "#A0A6AC" width="100"></th>
						<th bgcolor= "#A0A6AC" width="100"></th>
					</tr>
				</tfoot>
			</table>
		</div>
		<?
	}
    foreach (glob("$user_id*.xls") as $filename)
    {
        if( @filemtime($filename) < (time()-$seconds_old) )
        @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc,ob_get_contents());
    $filename=$user_id."_".$name.".xls";
    echo "$total_data####$filename####$tot_rows####$type";
    exit();
}
