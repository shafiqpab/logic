<?
/*-------------------------------------------- Comments 
Version          : V1
Purpose			 : This form will create Service Booking
Functionality	 :	
JS Functions	 :
Created by		 : Ashraful 
Creation date 	 : 25-04-2015
Requirment Client: 
Requirment By    : 
Requirment type  : 
Requirment       : 
Affected page    : 
Affected Code    :              generate_trim_report('show_trim_booking_report2')
DB Script        : 
Updated by 		 : 
Update date		 : 
QC Performed BY	 :		
QC Date			 :	
Comments		 : From this version oracle conversion is start
Entry From 		 : 162
*/
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
include('../../../includes/class4/class.conditions.php');
include('../../../includes/class4/class.reports.php');
include('../../../includes/class4/class.fabrics.php');
//---------------------------------------------------- Start---------------------------------------------------------------------------
if($action=="check_conversion_rate"){ 
	$data=explode("**",$data);
	if($db_type==0){
		$conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
	}
	else{
		$conversion_date=change_date_format($data[1], "d-M-y", "-",1);
	}
	$currency_rate=set_conversion_rate( $data[0], $conversion_date );
	echo "1"."_".$currency_rate;
	exit();	
}
if($action=="load_drop_down_attention"){
	$supplier_name=return_field_value("contact_person","lib_supplier","id ='".$data."' and is_deleted=0 and status_active=1");
	echo "document.getElementById('txt_attention').value = '".$supplier_name."';\n";
	exit();	
}

if($action=="company_wise_report_button_setting")
{
	extract($_REQUEST);
	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=2 and report_id=49 and is_deleted=0 and status_active=1");
	
	$print_report_format_arr=explode(",",$print_report_format);
	echo "$('#print_booking1').hide();\n";
	echo "$('#print_booking2').hide();\n";
	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			if($id==163){echo "$('#print_booking1').show();\n";}
			if($id==164){echo "$('#print_booking2').show();\n";}	
		}
	}
	else
	{
		echo "$('#print_booking1').show();\n";
		echo "$('#print_booking2').show();\n";
	}
	exit();	
}

if ($action=="fabric_search_popup"){
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	</head>
	<body>
        <div align="center" style="width:100%;" >
            <form name="searchpofrm_1" id="searchpofrm_1">
            <table width="940"  align="center" rules="all">
                <tr>
                <td align="center" width="100%">
                <table  width="940" class="rpt_table" align="center" rules="all">
                    <thead> 
                        <tr>
                            <th colspan="11" align="center"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --",1 ); ?></th>
                        </tr> 
                        <tr>               	 
                            <th width="140">Company</th>
                            <th width="150">Buyer</th>
                            <th width="60">Year</th>
                            <th width="60">Job No</th>
                            <th width="70">Internal Ref</th>
                            <th width="70">File No</th>
                            <th width="70">Style Ref </th>
                            <th width="70">Order No</th>
                            <th width="170" colspan="2">Date Range</th>
                            <th>&nbsp;</th>
                        </tr>           
                    </thead>
                    <tr>
                        <td><? echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "- Select Company -", str_replace("'","",$cbo_company_name), "load_drop_down( 'partial_fabric_booking_controller', this.value, 'load_drop_down_buyer_popup', 'buyer_td' );","1"); ?>
                        </td>
                        <td id="buyer_td">
                        <? echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company=$cbo_company_name and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", str_replace("'","",$cbo_buyer_name), "","1" ); ?>	
                        </td>
                        <td><? echo create_drop_down( "cbo_job_year", 60, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
                        <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:50px"></td>
                        <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:60px"></td>
                        <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:60px"></td>
                        <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:60px"></td>
                        <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:60px"></td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" value=""/></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" value=""/></td> 
                        <td align="center">
                        <input type="hidden" name="cbo_currency" id="cbo_currency" class="text_boxes" style="width:60px" value="<? echo str_replace("'","",$cbo_currency); ?>"  />
                        <input type="hidden" name="cbo_fabric_natu" id="cbo_fabric_natu" class="text_boxes" style="width:60px" value="<? echo str_replace("'","",$cbo_fabric_natu); ?>"  />
                        <input type="hidden" name="cbouom" id="cbouom" class="text_boxes" style="width:60px" value="<? echo str_replace("'","",$cbouom); ?>"  />
                        <input type="hidden" name="cbo_fabric_source" id="cbo_fabric_source" class="text_boxes" style="width:60px" value="<? echo str_replace("'","",$cbo_fabric_source); ?>"  />
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_job_year').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_currency').value+'_'+document.getElementById('cbo_fabric_natu').value+'_'+document.getElementById('cbouom').value+'_'+document.getElementById('cbo_fabric_source').value+'_'+document.getElementById('cbo_string_search_type').value, 'fabric_search_list_view', 'search_div', 'service_booking_aop_urmi_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="11" align="center">
                        <input type="hidden" class="text_boxes" readonly style="width:550px" id="txt_selected_po">
                        <input type="hidden" id="txt_selected_id">
                        <input type="hidden" id="txt_pre_cost_dtls_id">
                        </td>
                    </tr>
                </table>
                </td>
                </tr>
                <tr>
                <td align="center" >
                <input type="button" name="close" onClick="parent.emailwindow.hide();"  class="formbutton" value="Close" style="width:100px" /> 
                </td>
                </tr>
                <tr>
                <td id="search_div" align="center">
                </td>
                </tr>
                <tr>
                <td id="search_div" align="center">
                <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                </td>
                </tr>
            </table>
            </form>
        </div>
	</body> 
    <script>
		function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length; 
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
		
		var selected_id = new Array(); 
		var selected_item=new Array();
		var selected_po=new Array();
		
		function js_set_value( str ) {
			if($("#search"+str).css("display") !='none'){
				toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
				if( jQuery.inArray( $('#txt_id' + str).val(), selected_id ) == -1 ) {
					selected_id.push( $('#txt_individual_id' + str).val() );
					selected_item.push($('#pre_cost_dtls_id' + str).val());
					selected_po.push($('#txt_po_id' + str).val());
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
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="fabric_search_list_view"){
	$data=explode('_',$data);
	$company=$data[0];
	$buyer=$data[1];
	$cbo_job_year=$data[2];
	$job=$data[3];
	$internal_ref=$data[4];
	$file_no=$data[5];
	$style=$data[6];
	$order_search=$data[7];
	$date_from=$data[8];
	$date_to=$data[9];
	$cbo_currency=$data[10];
	$search_category=$data[14];
	
	if ($company!=0) $company_cond=" and a.company_name='$company'"; else { echo "Please Select Company First."; die; }
	if ($buyer!=0)   $buyer_cond=" and a.buyer_name='$buyer'"; else{ echo "Please Select Buyer First."; die; }
	if ($cbo_currency!="")   $currency_cond=" and a.currency_id='$cbo_currency'"; else{ echo "Please Select Currency First."; die; }
	if($db_type==0){
	if ($date_from!="" &&  $date_to!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($date_from, "yyyy-mm-dd", "-")."' and '".change_date_format($date_to, "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}
	else if($db_type==2){
	if ($date_from!="" &&  $date_to!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($date_from, "yyyy-mm-dd", "-",1)."' and '".change_date_format($date_to, "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}
	if($db_type==0) $year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$cbo_job_year";
	if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_job_year";
	
	if ( str_replace("'","",$job)=="" )
	{
		if ( str_replace("'","",$order_search)=="" )
		{
			echo "Please Insert Job or Order First."; die;
		}
	}
 	//&& str_replace("'","",$order_search)==""
	
	
	$job_cond="";
	$order_cond=""; 
	$style_cond="";
	if($search_category==1){
		if (str_replace("'","",$job)!="") $job_cond=" and a.job_no_prefix_num='$job'"; //else  $job_cond=""; 
		if (str_replace("'","",$order_search)!="") $order_cond=" and b.po_number = '$order_search'"; //else  $order_cond=""; 
		if (trim($style)!="") $style_cond=" and a.style_ref_no ='$style'"; //else  $style_cond=""; 
		if (trim($internal_ref) !="") $internal_ref_cond=" and b.grouping = '$internal_ref'"; 
		if (trim($file_no) !="")  $file_no_cond=" and b.file_no='$file_no' "; 
	}
	else if($search_category==2){
		if (str_replace("'","",$job)!="") $job_cond=" and a.job_no_prefix_num like '$job%'"; //else  $job_cond=""; 
		if (str_replace("'","",$order_search)!="") $order_cond=" and b.po_number like '$order_search%'  "; //else  $order_cond=""; 
		if (trim($style)!="") $style_cond=" and a.style_ref_no like '$style%'  "; //else  $style_cond=""; 
		if (trim($internal_ref) !="") $internal_ref_cond=" and b.grouping like '$internal_ref%'"; 
		if (trim($file_no) !="")  $file_no_cond=" and b.file_no like '$file_no%' "; 
	}
	else if($search_category==3){
		if (str_replace("'","",$job)!="") $job_cond=" and a.job_no_prefix_num like '%$job'"; //else  $job_cond=""; 
		if (str_replace("'","",$order_search)!="") $order_cond=" and b.po_number like '%$order_search'  "; //else  $order_cond=""; 
		if (trim($style)!="") $style_cond=" and a.style_ref_no like '%$style'"; //else  $style_cond=""; 
		if (trim($internal_ref) !="")  $internal_ref_cond=" and b.grouping like '%$internal_ref'"; 
		if (trim($file_no) !="")  $file_no_cond=" and b.file_no like '%$file_no' "; 
	}
	else if($search_category==4 || $search_category==0){
		if (str_replace("'","",$job)!="") $job_cond=" and a.job_no_prefix_num like '%$job%'"; //else  $job_cond=""; 
		if (str_replace("'","",$order_search)!="") $order_cond=" and b.po_number like '%$order_search%'  "; //else  $order_cond=""; 
		if (trim($style)!="") $style_cond=" and a.style_ref_no like '%$style%'"; //else  $style_cond=""; 
		if (trim($internal_ref)!="")  $internal_ref_cond=" and b.grouping like '%$internal_ref%'"; 
		if (trim($file_no) !="")  $file_no_cond=" and b.file_no like '%$file_no%' "; 
	}
	
	$sql= 'select a.job_no AS "job_no",b.id AS "id",b.po_number AS "po_number",c.item_number_id AS "item_number_id",d.id AS "pre_cost_dtls_id",d.body_part_id AS "body_part_id",d.construction AS "construction",d.composition AS "composition",d.fab_nature_id AS "fab_nature_id",d.fabric_source AS "fabric_source",d.lib_yarn_count_deter_id AS "lib_yarn_count_deter_id",d.uom AS "uom", d.gsm_weight AS "gsm_weight", min(e.id) AS "eid",f.id AS "fid" from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e ,wo_pre_cost_fab_conv_cost_dtls f where   a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and a.job_no=f.job_no and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and  c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_description   and e.cons !=0 and f.cons_process=35   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 '.$company_cond . $buyer_cond. $year_cond. $job_cond. $internal_ref_cond. $file_no_cond . $style_cond. $order_cond. $shipment_date. $currency_cond." group by a.job_no,b.id,b.po_number,c.item_number_id,d.id,d.body_part_id,d.construction,d.composition,d.fab_nature_id,d.fabric_source,d.lib_yarn_count_deter_id,d.uom,d.gsm_weight,f.id";
	$sql_data=sql_select($sql);
	?>
    <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="80">Job No</th>
                <th width="80">Po No</th>
                <th width="100">Item</th>
                <th width="100">Body Part</th>
                <th width="100">Construction</th>
                <th width="100">Composition</th>
                <th width="70">Gsm</th>
                <th width="80">Fabric Nature</th>
                <th width="70">Fabric Soutce</th>
                <th width="">Uom</th>
            </thead>
     	</table>
     </div>
     <div style="width:1000px; max-height:270px;overflow-y:scroll;" >	 
     <table cellspacing="0" cellpadding="0" border="1" rules="all" width="983" class="rpt_table" id="list_view">
    <?
	$i=1;
	foreach($sql_data as $sql_row){
		?>
        <tr style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
        <td width="30">
		<? echo $i; ?>
        <input type="hidden" name="txt_id" id="txt_id<?php echo $i ?>" value="<? echo $sql_row['eid']; ?>"/>	
        <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $sql_row['fid']; ?>"/>	
        <input type="hidden" name="pre_cost_dtls_id" id="pre_cost_dtls_id<?php echo $i ?>" value="<? echo $sql_row['pre_cost_dtls_id']; ?>"/>	
        <input type="hidden" name="txt_po_id" id="txt_po_id<?php echo $i ?>" value="<? echo $sql_row['id']; ?>"/>	
        </td>
        <td width="80"><? echo $sql_row['job_no']; ?></td>
        <td width="80"><? echo $sql_row['po_number']; ?></td>
        <td width="100"><? echo $garments_item[$sql_row['item_number_id']]; ?></td>
        <td width="100"><? echo $body_part[$sql_row['body_part_id']]; ?></td>
        <td width="100"><? echo $sql_row['construction']; ?></td>
        <td width="100"><? echo $sql_row['composition']; ?></td>
        <td width="70"><? echo $sql_row['gsm_weight']; ?></td>
        <td width="80"><? echo $item_category[$sql_row['fab_nature_id']]; ?></td>
        <td width="70"><? echo $fabric_source[$sql_row['fabric_source']]; ?></td>
        <td width=""><? echo $unit_of_measurement[$sql_row['uom']]; ?></td>
        </tr>
        <?
		$i++;
	}
	?>
    </table>
	</div> 
    <?
	exit();
}

if ($action=="order_search_popup"){
  	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>
	<script>
	 var selected_id = new Array, selected_name = new Array();	
	 function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length-1; 
			tbl_row_count = tbl_row_count;
			for( var i = 1; i <= tbl_row_count; i++ ){
				if($("#tr_"+i).css("display") !='none'){
				document.getElementById("tr_"+i).click();
				}
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			document.getElementById(x).style.backgroundColor = ( newColor == document.getElementById(x).style.backgroundColor )? origColor : newColor;
		}
		
		function js_set_value( str_data,tr_id ) {
			toggle( tr_id, '#FFFFCC');
			var str_all=str_data.split("_");
			var str_po=str_all[1];
			var str=str_all[0];
			if ( document.getElementById('job_no').value!="" && document.getElementById('job_no').value!=str_all[2] ){
				alert('No Job Mix Allowed')
				return;	
			}
			document.getElementById('job_no').value=str_all[2];
				
			if( jQuery.inArray( str , selected_id ) == -1 ) {
				selected_id.push( str );
				selected_name.push( str_po );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			
			var id = '' ; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			$('#po_number_id').val( id );
			$('#po_number').val( name );
		}
    </script>

</head>

<body>
<div align="center" style="width:100%;" >
<?
$booking_month=0;
 if(str_replace("'","",$cbo_booking_month)<10){
	 $booking_month.=str_replace("'","",$cbo_booking_month);
 }
 else{
	$booking_month=str_replace("'","",$cbo_booking_month); 
 }
$start_date="01"."-".$booking_month."-".str_replace("'","",$cbo_booking_year);
$end_date=cal_days_in_month(CAL_GREGORIAN, $booking_month, str_replace("'","",$cbo_booking_year))."-".$booking_month."-".str_replace("'","",$cbo_booking_year);
?>
	<form name="searchpofrm_1" id="searchpofrm_1">
				<table width="900"  align="center" rules="all">
                    <tr>
                        <td align="center" width="100%">
                            <table  width="890" class="rpt_table" align="center" rules="all">
                                <thead>                	 
                                    <th width="150">Company Name</th>
                                    <th width="140">Buyer Name</th>
                                    <th width="60">Year</th>
                                    <th width="100">Job No</th>
                                    <th width="130">Order No</th>
                                    <th width="200">Date Range</th><th><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">Job Without PO</th>           
                                </thead>
                                <tr>
                                    <td> 
                                        <? 
                                            echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", str_replace("'","",$cbo_company_name), "load_drop_down( 'service_booking_aop_urmi_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
                                        ?>
                                    </td>
                                <td id="buyer_td">
									
                                 <?
								 if(str_replace("'","",$cbo_company_name)!=0){
								 	echo create_drop_down( "cbo_buyer_name", 150,"select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='".str_replace("'","",$cbo_company_name)."' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", str_replace("'","",$cbo_buyer_name), "" ); 
								 }
								 else{
								   echo create_drop_down( "cbo_buyer_name", 150, $blank_array, 1, "-- Select Buyer --", str_replace("'","",$cbo_buyer_name), "" );
								 }
                                ?>	
                                </td>
                                 <td>
                                     <? 
							  	echo create_drop_down( "cbo_job_year", 60, $year,"", 1, "-- Select --", date('Y'), "",0 );		
							  ?>
                                    </td>
                                 <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:100px"></td>
                                 <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:130px"></td>
                                <td>
                                  <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:85px" value="<? //echo $start_date; ?>"/>
                                  <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:85px" value="<? //echo $end_date; ?>"/>
                                 </td> 
                                 <td align="center">
                                 <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('cbo_job_year').value, 'create_po_search_list_view', 'search_div', 'service_booking_aop_urmi_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100%;" /></td>
                            </tr>
                          
                            <tr>
                            	<td colspan="7" align="center"><strong>Selected PO Number:</strong> &nbsp;
                                <input type="text" class="text_boxes"  readonly style="width:550px" id="po_number">
                                <input type="hidden" id="po_number_id">
                                <input type="hidden" id="job_no">
                                </td>
                            </tr>
                         </table>
                        
    				</td>
           		</tr>
                
          	
            <tr>
                <td align="center" >
                <input type="button" name="close" onClick="parent.emailwindow.hide();"  class="formbutton" value="Close" style="width:100px" /> 
                </td>
            </tr>
            <tr>
                <td id="search_div" align="center">
                            
                </td>
            </tr>
             <tr>
                <td id="search_div" align="center">
                 <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                </td>
            </tr>
       </table>
	</form>
   </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
  exit();
}

if($action=="create_po_search_list_view"){
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	
	if($db_type==0) $insert_year="SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year";
	if($db_type==2) $insert_year="to_char(a.insert_date,'YYYY') as year";
	if($db_type==0) $year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[7]";
	if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$data[7]";
	
	if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num='$data[5]' "; else  { echo "Please Select Job No"; die; } 
	if (str_replace("'","",$data[6])!="") $order_cond=" and b.po_number like '%$data[6]%'  "; else  $order_cond=""; 
	if($db_type==0){
	if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}
	if($db_type==2){
	if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}
	 
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');

	$arr=array (2=>$comp,3=>$buyer_arr);
	
	if ($data[2]==0){
		 $sql= "select a.job_no_prefix_num,$insert_year, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity,b.id, b.po_number,b.po_quantity,b.shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1  $shipment_date $company $buyer $job_cond $order_cond $year_cond order by a.job_no";  
		echo  create_list_view("list_view", "Job No,Year,Company,Buyer,Style Ref. No,Job Qty.,PO number,PO Qty,Shipment Date", "90,60,60,100,120,90,120,70,80","900","320",0, $sql , "js_set_value", "id,po_number,job_no", "this.id", 1, "0,0,company_name,buyer_name,0,0,0,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,job_quantity,po_number,po_quantity,shipment_date", '','','0,0,0,0,0,1,0,1,3','','');
	}
	else{
		$sql= "select a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no from wo_po_details_master a where a.status_active=1  and a.is_deleted=0 $company $buyer $year_cond order by a.job_no";
		
		echo  create_list_view("list_view", "Job No,Company,Buyer,Style Ref. No", "90,60,50,100,90","710","320",0, $sql , "js_set_value", "id", "", 1, "0,company_name,buyer_name,0,0,0,0", $arr , "job_no_prefix_num,company_name,buyer_name,style_ref_no", '','','0,0,0,0,1,0,2,3','','') ;
	}
} 


if ($action=="populate_order_data_from_search_popup"){
	$data_array=sql_select("select a.job_no,a.company_name,a.buyer_name from wo_po_details_master a, wo_po_break_down b where b.id in (".$data.") and a.job_no=b.job_no_mst");
	foreach ($data_array as $row){
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_name")]."';\n";  
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_name")]."';\n";  
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n";
		echo "load_drop_down( 'requires/service_booking_aop_urmi_controller', '".$row[csf("job_no")]."', 'load_drop_down_fabric_description', 'fabric_description_td' )\n";
	}
}

if ($action=="load_drop_down_buyer"){
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
} 

if ($action=="load_drop_down_fabric_description"){

	$data=explode("_",$data);
	$fabric_description_array=array();
	if($data[1] ==""){
		$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where job_no='$data[0]' and cons_process=35 ");
	}
	else{
		$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where job_no='$data[0]' and status_active=1 and is_deleted=0 and cons_process=35  ");
	}
	
	foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id){
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]!=0){
			
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."'");
			list($fabric_description_row)=$fabric_description;
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")];
			
		}
		
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]==0){
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls 
			where  job_no='$data[0]'");
			foreach( $fabric_description as $fabric_description_row){
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]="All Fabrics  ".$conversion_cost_head_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("cons_process")]];
			}
		}
	}
	echo create_drop_down( "cbo_fabric_description", 650, $fabric_description_array,"", 1, "-- Select --", $selected,"set_process(this.value,'set_process')" );
} 


 
 if($action=="set_process"){
	 $process=return_field_value("cons_process", "wo_pre_cost_fab_conv_cost_dtls", "id=$data");
	 echo $process; die;
	 
 }
 
if($action=="generate_aop_booking"){
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
	
	$data=explode("**",$data);
	$fabric_description_id=$data[0];
	$txt_order_no_id=$data[1];
	$txt_booking_no=$data[2];
	$cbo_level=$data[3];
	$conversion_cost_id=$data[4];
	$is_short=$data[5];
	if($fabric_description_id==0){
		echo "<strong>Select Fabric</strong>";
		die;
	}
	
	$fabric_description_array=array();
    $wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where id in($conversion_cost_id)");
	foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id){
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]!=0){
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls  
			where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."'");
			list($fabric_description_row)=$fabric_description;
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")];
		}
	}
	
	$cu_booking_data_arr=array();
	$sql="select a.id,a.pre_cost_fabric_cost_dtls_id,a.artwork_no,a.po_break_down_id,a.color_size_table_id,a.fabric_color_id,a.item_size,a.process,
	sensitivity,a.job_no,booking_no,a.booking_type,a.description,a.uom,a.delivery_date,a.delivery_end_date,a.sensitivity,a.wo_qnty,a.rate,
	a.amount,a.gmts_size,a.gmts_color_id,a.dia_width
	from wo_booking_dtls a  where  a.booking_type=3 and a.process=35 and a.is_short=2  and a.pre_cost_fabric_cost_dtls_id in($data[4]) and a.status_active=1 and a.is_deleted=0 ";
	
	$dataArray=sql_select($sql);
	$z=1; $i=1;
	foreach($dataArray as $row){
		$id=$row[csf("id")];
		$pre_cost_conversion_cost_dtls_id=$row[csf("pre_cost_fabric_cost_dtls_id")];
		$artwork_no=$row[csf("artwork_no")];
		$po_break_down_id=$row[csf("po_break_down_id")];
		$color_size_table_id=$row[csf("color_size_table_id")];
		$fabric_color_id=$row[csf("fabric_color_id")];
		$item_size=$row[csf("item_size")];
        $process=$row[csf("process")];
		$sensitivity=$row[csf("sensitivity")];
        $job_no=$row[csf("job_no")];
		$booking_no=$row[csf("booking_no")];
		$booking_type=$row[csf("booking_type")];
		$description=$row[csf("description")];
		$uom=$row[csf("uom")];
		$delivery_date=$row[csf("delivery_date")];
		$delivery_end_date=$row[csf("delivery_end_date")];
        $wo_qnty=$row[csf("wo_qnty")];
		$rate=$row[csf("rate")];
		$amount=$row[csf("amount")];
		$color_number_id=$row[csf("gmts_color_id")];
		$size_number_id=$row[csf("gmts_size")];
		$dia_width=$row[csf("dia_width")];
		$cu_booking_data_arr[$pre_cost_conversion_cost_dtls_id][$po_break_down_id][$color_number_id][$dia_width]['wo_qnty']+=$wo_qnty;
	}
	
	if($dtls_id==""){
		$dtls_id=0;
	}
	$booking_data_arr=array();
	$sql="select a.id,a.pre_cost_fabric_cost_dtls_id,a.artwork_no,a.po_break_down_id,a.color_size_table_id,a.fabric_color_id,a.item_size,a.process,
	sensitivity,a.job_no,booking_no,a.booking_type,a.description,a.uom,a.delivery_date,a.delivery_end_date,a.sensitivity,a.wo_qnty,a.rate,
	a.amount,a.gmts_size,a.gmts_color_id,a.dia_width
	from wo_booking_dtls a  where  a.booking_type=3 and a.process=35 and 
	a.booking_no='$txt_booking_no' and a.id in ($dtls_id)  and a.pre_cost_fabric_cost_dtls_id in($data[4]) and  a.status_active=1 and a.is_deleted=0 ";
	$dataArray=sql_select($sql);
	$z=1; $i=1;
	foreach($dataArray as $row){
		$id=$row[csf("id")];
		$pre_cost_conversion_cost_dtls_id=$row[csf("pre_cost_fabric_cost_dtls_id")];
		$artwork_no=$row[csf("artwork_no")];
		$po_break_down_id=$row[csf("po_break_down_id")];
		$color_size_table_id=$row[csf("color_size_table_id")];
		$fabric_color_id=$row[csf("fabric_color_id")];
		$item_size=$row[csf("item_size")];
        $process=$row[csf("process")];
		$sensitivity=$row[csf("sensitivity")];
        $job_no=$row[csf("job_no")];
		$booking_no=$row[csf("booking_no")];
		$booking_type=$row[csf("booking_type")];
		$description=$row[csf("description")];
		$uom=$row[csf("uom")];
		$delivery_date=$row[csf("delivery_date")];
		$delivery_end_date=$row[csf("delivery_end_date")];
        $wo_qnty=$row[csf("wo_qnty")];
		$rate=$row[csf("rate")];
		$amount=$row[csf("amount")];
		$color_number_id=$row[csf("gmts_color_id")];
		$size_number_id=$row[csf("gmts_size")];
		$dia_width=$row[csf("dia_width")];
		
		$booking_data_arr[$pre_cost_conversion_cost_dtls_id][$po_break_down_id][$color_number_id][$dia_width]['id']=$id;
		$booking_data_arr[$pre_cost_conversion_cost_dtls_id][$po_break_down_id][$color_number_id][$dia_width]['fabric_description_id']=$pre_cost_conversion_cost_dtls_id;
		$booking_data_arr[$pre_cost_conversion_cost_dtls_id][$po_break_down_id][$color_number_id][$dia_width]['artwork_no']=$artwork_no;
		$booking_data_arr[$pre_cost_conversion_cost_dtls_id][$po_break_down_id][$color_number_id][$dia_width]['fabric_color_id']=$fabric_color_id;
		$booking_data_arr[$pre_cost_conversion_cost_dtls_id][$po_break_down_id][$color_number_id][$dia_width]['item_size']=$item_size;
		$booking_data_arr[$pre_cost_conversion_cost_dtls_id][$po_break_down_id][$color_number_id][$dia_width]['description']=$description;
		$booking_data_arr[$pre_cost_conversion_cost_dtls_id][$po_break_down_id][$color_number_id][$dia_width]['uom']=$uom;
		$booking_data_arr[$pre_cost_conversion_cost_dtls_id][$po_break_down_id][$color_number_id][$dia_width]['delivery_date']=$delivery_date;
		$booking_data_arr[$pre_cost_conversion_cost_dtls_id][$po_break_down_id][$color_number_id][$dia_width]['delivery_end_date']=$delivery_end_date;
		$booking_data_arr[$pre_cost_conversion_cost_dtls_id][$po_break_down_id][$color_number_id][$dia_width]['wo_qnty']=$wo_qnty;
		$booking_data_arr[$pre_cost_conversion_cost_dtls_id][$po_break_down_id][$color_number_id][$dia_width]['rate']=$rate;
		$booking_data_arr[$pre_cost_conversion_cost_dtls_id][$po_break_down_id][$color_number_id][$dia_width]['amount']=$amount;
		$booking_data_arr[$pre_cost_conversion_cost_dtls_id][$po_break_down_id][$color_number_id][$dia_width]['dia_width']=$dia_width;
	}
	
	$condition= new condition();
	if(str_replace("'","",$txt_order_no_id) !=''){
		$condition->po_id("in($txt_order_no_id)");
	}
	$condition->init();
	$fabric= new fabric($condition);
	$req_qty_arr=$fabric->getQtyArray_by_OrderFabriccostidGmtscolorAndDiaWidth_knitAndwoven_greyAndfinish();
	$req_amount_arr=$fabric->getAmountArray_by_OrderFabriccostidGmtscolorAndDiaWidth_knitAndwoven_greyAndfinish();
	
    $sql="select 
	a.job_no,
	b.id as po_break_down_id,
	b.po_number,
	min(c.id)as color_size_table_id,
	c.color_number_id,
	sum(c.plan_cut_qnty) as plan_cut_qnty,
	d.costing_per,
	e.id,
	e.fabric_description,
	e.cons_process,
	e.charge_unit,
	e.amount,
	e.color_break_down,
	e.process_loss,
	f.id as fid,
	f.body_part_id,
	f.color_type_id,
	f.construction,
	f.composition,
	f.gsm_weight,
	f.costing_per,
	f.uom,
	f.fab_nature_id,
	g.dia_width,
	
	CASE f.costing_per 
	WHEN 1 THEN 
	round((AVG(g.requirment)/12)*sum(c.plan_cut_qnty),4) 
	WHEN 2 THEN
	round((AVG(g.requirment)/1)*sum(c.plan_cut_qnty),4)  
	WHEN 3 THEN 
	round((AVG(g.requirment)/24)*sum(c.plan_cut_qnty),4) 
	WHEN 4 THEN 
	round((AVG(g.requirment)/36)*sum(c.plan_cut_qnty),4) 
	WHEN 5 THEN 
	round((AVG(g.requirment)/48)*sum(c.plan_cut_qnty),4) 
	ELSE 0 END as wo_req_qnty 
	
	from 
	wo_po_details_master a, 
	wo_po_break_down b,
	wo_po_color_size_breakdown c,
	wo_pre_cost_mst d,
	wo_pre_cost_fab_conv_cost_dtls e,
	wo_pre_cost_fabric_cost_dtls f,
	wo_pre_cos_fab_co_avg_con_dtls g 
	
	where 
	a.job_no=b.job_no_mst and 
	a.job_no=c.job_no_mst and 
	a.job_no=d.job_no and 
	a.job_no=e.job_no and 
	a.job_no=f.job_no and 
	a.job_no=g.job_no and 
	b.id=c.po_break_down_id and 
	b.id=g.po_break_down_id and 
	c.color_number_id=g.color_number_id and  
	c.size_number_id=g.gmts_sizes and 
	c.item_number_id=f.item_number_id and 
	f.id=g.pre_cost_fabric_cost_dtls_id and 
	e.fabric_description=f.id and 
	e.id in($conversion_cost_id) and 
	b.id in($txt_order_no_id) and 
	a.status_active=1 and 
	a.is_deleted=0  and 
	b.status_active=1 and 
	b.is_deleted=0 and 
	c.status_active=1 and 
	c.is_deleted=0  and 
	d.status_active=1 and 
	d.is_deleted=0 and 
	e.status_active=1 and 
	e.is_deleted=0 and 
	f.status_active=1 and 
	f.is_deleted=0  and 
	g.requirment >0
	
	group by 
	a.job_no,
	b.id,
	b.po_number,
	c.color_number_id,
	d.costing_per,
	e.id,
	e.fabric_description,
	e.cons_process,
	e.charge_unit,
	e.amount,
	e.color_break_down,
	e.process_loss,
	f.id,
	f.body_part_id,
	f.color_type_id,
	f.construction,
	f.composition,
	f.gsm_weight,
	f.costing_per,
	f.uom,
	f.fab_nature_id,
	g.dia_width
	
	order by 
	b.id";
	
	$dataArray=sql_select($sql);
	foreach($dataArray as $row){
		$job_no                      = $row[csf("job_no")];
		$po_number                   = $row[csf("po_number")];
		$po_break_down_id            = $row[csf("po_break_down_id")];
		$pre_cost_conversion_cost_id = $row[csf("id")];
		$body_part_id                = $row[csf("body_part_id")];
		$color_type_id               = $row[csf("color_type_id")];
		$construction                = $row[csf("construction")];
		$composition                 = $row[csf("composition")];
		$gsm_weight                  = $row[csf("gsm_weight")];
        $dia_width                   = $row[csf("dia_width")];
		
		$color_size_table_id         = $row[csf("color_size_table_id")];
		$color_number_id             = $row[csf("color_number_id")];
		$item_color_id               = $row[csf("color_number_id")];
		
		$plan_cut_qnty               = $row[csf("plan_cut_qnty")];
		$wo_req_qnty                 = $row[csf("wo_req_qnty")];
		$process_loss                = $row[csf("process_loss")];
		$charge_unit                 = $row[csf("charge_unit")];
		$uom                         = $row[csf("uom")];
		
		$pre_cost_fabric_cost_dtls_id= $row[csf("fid")];
		$cbo_fabric_natu             = $row[csf("fab_nature_id")];
		if($cbo_fabric_natu==2){
			$req_qty = $req_qty_arr['knit']['grey'][$po_break_down_id][$pre_cost_fabric_cost_dtls_id][$color_number_id][$dia_width][$uom];
			$req_amt = $req_amount_arr['knit']['grey'][$po_break_down_id][$pre_cost_fabric_cost_dtls_id][$color_number_id][$dia_width][$uom];
			$rate = $req_amt/$req_qty;
		}
		if($cbo_fabric_natu==3){
			$req_qty = $req_qty_arr['woven']['grey'][$po_break_down_id][$pre_cost_fabric_cost_dtls_id][$color_number_id][$dia_width][$uom];
			$req_amt = $req_amount_arr['woven']['grey'][$po_break_down_id][$pre_cost_fabric_cost_dtls_id][$color_number_id][$dia_width][$uom];
			$rate=$req_amt/$req_qty;
		}

		
		
		$cu_wo_qnty        = $cu_booking_data_arr[$pre_cost_conversion_cost_id][$po_break_down_id][$color_number_id][$dia_width]['wo_qnty'];
		$woqnty      = 0;
		if($body_part_id == 3){
			$woqnty  = $plan_cut_qnty*2;
			$uom_item     = "1,2";
			$selected_uom = $uom;
		}
		else if($body_part_id==2){
			$woqnty  = $plan_cut_qnty*1;
			$uom_item     = "1,2";
			$selected_uom = $uom;
		}
		else if($body_part_id != 2 || $body_part_id != 3 ){
			$process_loss_qty = $wo_req_qnty * $process_loss / 100;
			$woqnty      = $wo_req_qnty - $process_loss_qty;
			$selected_uom     = $uom;
		}
		
		if($body_part_id==2 || $body_part_id==3){
			$rate   = 0;
			$amount = 0;	
		}
		else{
			$rate   = $charge_unit;
			$amount = $rate*$woqnty;
		}
		
		$blaqnty = $woqnty - $cu_wo_qnty;
		$rate    = $rate;
		$amount  = $amount;
		$uom     = $selected_uom;
			

		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['job_no'][$po_break_down_id]              = $job_no ;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['po_number'][$po_break_down_id]           = $po_number ;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['po_id'][$po_break_down_id]               = $po_break_down_id;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['conversion_cost_id'][$po_break_down_id]  = $pre_cost_conversion_cost_id;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['body_part_id'][$po_break_down_id]        = $body_part_id;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['color_type_id'][$po_break_down_id]       = $color_type_id;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['construction'][$po_break_down_id]        = $construction;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['composition'][$po_break_down_id]         = $composition;

		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['gsm_weight'][$po_break_down_id]          = $gsm_weight;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['dia_width'][$po_break_down_id]           = $dia_width;
		
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['color_size_table_id'][$po_break_down_id] = $color_size_table_id ;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['color_number_id'][$po_break_down_id]     = $color_number_id ;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['plan_cut_qnty'][$po_break_down_id]       = $plan_cut_qnty ;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['req_qnty'][$po_break_down_id]            = $wo_req_qnty;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['cu_wo_qnty'][$po_break_down_id]          = $cu_wo_qnty;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['blaqnty'][$po_break_down_id]             = $blaqnty;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['uom'][$po_break_down_id]                 = $uom;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['rate'][$po_break_down_id]                = $rate;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['amount'][$po_break_down_id]              = $amount;
	}
	?>
			<div id="content_search_panel_<? echo $pre_cost_conversion_cost_id; ?>" style="" class="accord_close">
				<table class="rpt_table" border="1" width="1450" cellpadding="0" cellspacing="0" rules="all" id="tbl_table" style="table-layout: fixed;">
					<thead>
                        <th>Job No</th>
						<th>Po Number</th>
                        <th>Body Part</th>
                        <th>Color Type</th>
                        <th>Construction</th>
                        <th>Composition</th>
                        <th>Gsm</th>
                        <th>Dia</th>
						<th>Gmts. Color</th>
						<th>Item Color</th>
                        <th class="must_entry_caption">Fin Dia</th>
                        <th class="must_entry_caption">Printing Color</th>
                        <th>Artwork No</th>
                        <th>Delivery Start Date</th>
                        <th>Delivery End Date</th>
                        <th>Bla. Qnty</th>
                        <th>WO. Qnty</th>
                        <th>UOM</th>
                        <th>Rate</th>
                        <th>Amount</th>
                        <th>Plan Cut Qnty</th>
					</thead>
					<tbody>
					<?
					$row_check=0;
					if($cbo_level==1){
					$i=1;
					foreach($po_color_level_data_arr as $precost_conversion_cost_id=>$pre_cost_cost_conversion_cost_val){
						foreach($pre_cost_cost_conversion_cost_val as $color_id=>$color_val){
							foreach($color_val as $dia_width_id=>$dia_width_val){
								foreach($dia_width_val['po_id'] as $po_id){
									$job_no                      = $dia_width_val['job_no'][$po_id];
									$po_number                   = $dia_width_val['po_number'][$po_id];
									$po_break_down_id            = $po_id;
									$pre_cost_conversion_cost_id = $precost_conversion_cost_id;
									$body_part_id                = $dia_width_val['body_part_id'][$po_id];
									$color_type_id               = $dia_width_val['color_type_id'][$po_id];
									$construction                = $dia_width_val['construction'][$po_id];
									$composition                 = $dia_width_val['composition'][$po_id];
									$gsm_weight                  = $dia_width_val['gsm_weight'][$po_id];
									$dia_width                   = $dia_width_id;
									
									$color_size_table_id         = $dia_width_val['color_size_table_id'][$po_id];
									$color_number_id             = $dia_width_val['color_number_id'][$po_id];
									$item_color_id               = $dia_width_val['color_number_id'][$po_id];
									$uom                         = $dia_width_val['uom'][$po_id];
									
									
									$plan_cut_qnty               = $dia_width_val['plan_cut_qnty'][$po_id];
									$req_qnty                    = def_number_format($dia_width_val['req_qnty'][$po_id],1,"");
									$blaqnty                     = def_number_format($dia_width_val['blaqnty'][$po_id],1,"");
									$rate                        = def_number_format($dia_width_val['rate'][$po_id],1,"");
									$amount                      = def_number_format($dia_width_val['amount'][$po_id],1,"");
						
						
						if($is_short==2)
						{
							
							if($blaqnty>0)
							{
								
								?>
								<tr align="center">
									<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;">
									<? echo $job_no; ?>
									<input type="hidden" name="job_no_<? echo $i; ?>" id="job_no_<? echo  $i; ?>" value="<? echo $job_no; ?>" style="width:60px;" class="text_boxes" disabled="disabled">
									</td>
									<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;">
									<? echo $po_number; ?>
									<input type="hidden" name="po_id_<? echo $i; ?>" id="po_id_<? echo  $i; ?>" value="<? echo $po_break_down_id; ?>" style="width:60px;" class="text_boxes" disabled="disabled">
									</td>
									<td>
									<? echo $body_part[$body_part_id];?>
									<input type="hidden" name="fabric_description_id_<? echo $i; ?>" id="fabric_description_id_<? echo $i; ?>" value="<? echo $pre_cost_conversion_cost_id; ?>" style="width:60px;" class="text_boxes" disabled="disabled">
									</td>
									<td>
									<? echo $color_type[$color_type_id];?>
									</td>
									<td>
									<? echo $construction;?>
									</td>
									 <td>
									<? echo $composition;?>
									</td>
									<td>
									<? echo $gsm_weight;?>
									</td>
									<td>
									<? echo $dia_width;?>
									 <input type="hidden" name="dia_<? echo $i; ?>" id="dia_<? echo $i; ?>" value="<? echo $dia_width; ?>" class="text_boxes" style="width:60px;" />
									</td>
									
									<td>
									<?  echo $color_library[$color_number_id] ?>
									<input type="hidden" name="gmts_color_id_<? echo $i; ?>" id="gmts_color_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<?  echo $color_number_id;?>"disabled="disabled"/>
		
									<input type="hidden" name="color_size_table_id_<? echo $i; ?>" id="color_size_table_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<?  echo $color_size_table_id;?>" disabled="disabled"/>
									</td>
									<td>
									<input type="text" name="item_color_<? echo $i; ?>" id="item_color_<? echo $i; ?>" style="width:60px;" class="text_boxes" onChange="copy_value()" value="<?  echo $color_library[$item_color_id] ?>"/>
									<input type="hidden" name="item_color_id_<? echo $i; ?>" id="item_color_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<? echo $item_color_id;?>" disabled="disabled"/>
									</td>
									<td>
									<input type="text" name="findia_<? echo $i; ?>" id="findia_<? echo $i; ?>" value="<? echo $dia_width; ?>" class="text_boxes" style="width:60px;" />
									</td>
                                     <td>
                             			<input type="text" name="printing_color_<? echo $i; ?>" id="printing_color_<? echo $i; ?>" style="width:60px;" class="text_boxes" onChange="copy_value()" value="<?  echo $color_library[$item_color_id] ?>"/>
                           				<input type="hidden" name="printing_color_id_<? echo $i; ?>" id="printing_color_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<? echo $item_color_id;?>" disabled="disabled"/>
                           			 </td>
                            
									<td>
									<input type="text" name="artworkno_<? echo $i; ?>" id="artworkno_<? echo $i; ?>" value="<? //echo $artwork_no; ?>" style="width:60px;" class="text_boxes">
									</td>
									<td>
									<input type="text" name="startdate_<? echo $i; ?>" id="startdate_<? echo $i; ?>" value="<? //echo $delivery_date; ?>" style="width:60px;" class="datepicker">
									</td>
									<td>
									<input type="text" name="enddate_<? echo $i; ?>" id="enddate_<? echo $i; ?>" value="<?  //echo $delivery_end_date; ?>" style="width:60px;" class="datepicker">
									</td>
									<td>
									<input type="text" name="txt_blanty_<? echo $i; ?>" id="txt_blanty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $i; ?>,'txt_woqnty');calculate_amount(<? echo $i; ?>)" value="<? echo $blaqnty; ?>"/>
								   <input type="hidden" name="txtreqnty_<? echo $i; ?>" id="txtreqnty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? echo $req_qnty; ?>"/>
		
									</td>
									<td>
									<input type="text" name="txt_woqnty_<? echo $i; ?>" id="txt_woqnty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $i; ?>,'txt_woqnty');calculate_amount(<? echo $i; ?>)" value="<? echo $blaqnty; ?>"/>
									</td>
									  <td>
									<?
									echo create_drop_down("uom_".$i, 50, $unit_of_measurement,"", 1, "--Select--",$uom,"copy_value(".$i.",'uom')",1,"$uom_item");
									?>
									</td>
									<td>
                                    D
									<input type="text" name="txt_rate_<? echo $i; ?>" id="txt_rate_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $i; ?>,'txt_rate');calculate_amount(<? echo $i; ?>)" value="<? echo $rate; ?>">
									</td>
									<td>
									<input type="text" name="txt_amount_<? echo $i; ?>" id="txt_amount_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? echo $amount; ?>" disabled="disabled"/>
									</td>
									<td>
									<input type="text" name="txt_paln_cut_<? echo $i; ?>" id="txt_paln_cut_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? echo  $plan_cut_qnty; ?>" disabled>
									<input type="hidden" name="updateid_<? echo $i; ?>" id="updateid_<? echo $i; ?>" value="<? echo $id; ?>">
									</td>
								</tr>
							<?	
							$i++;
							}
						}
						else
						{
							
							?>
							<tr align="center">
								<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;">
								<? echo $job_no; ?>
								<input type="hidden" name="job_no_<? echo $i; ?>" id="job_no_<? echo  $i; ?>" value="<? echo $job_no; ?>" style="width:60px;" class="text_boxes" disabled="disabled">
								</td>
								<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;">
								<? echo $po_number; ?>
								<input type="hidden" name="po_id_<? echo $i; ?>" id="po_id_<? echo  $i; ?>" value="<? echo $po_break_down_id; ?>" style="width:60px;" class="text_boxes" disabled="disabled">
								</td>
								<td>
								<? echo $body_part[$body_part_id];?>
								<input type="hidden" name="fabric_description_id_<? echo $i; ?>" id="fabric_description_id_<? echo $i; ?>" value="<? echo $pre_cost_conversion_cost_id; ?>" style="width:60px;" class="text_boxes" disabled="disabled">
								</td>
								<td>
								<? echo $color_type[$color_type_id];?>
								</td>
								<td>
								<? echo $construction;?>
								</td>
								 <td>
								<? echo $composition;?>
								</td>
								<td>
								<? echo $gsm_weight;?>
								</td>
								<td>
								<? echo $dia_width;?>
								 <input type="hidden" name="dia_<? echo $i; ?>" id="dia_<? echo $i; ?>" value="<? echo $dia_width; ?>" class="text_boxes" style="width:60px;" />
								</td>
								
								<td>
								<?  echo $color_library[$color_number_id] ?>
								<input type="hidden" name="gmts_color_id_<? echo $i; ?>" id="gmts_color_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<?  echo $color_number_id;?>"disabled="disabled"/>
	
								<input type="hidden" name="color_size_table_id_<? echo $i; ?>" id="color_size_table_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<?  echo $color_size_table_id;?>" disabled="disabled"/>
								</td>
								<td>
								<input type="text" name="item_color_<? echo $i; ?>" id="item_color_<? echo $i; ?>" style="width:60px;" class="text_boxes" onChange="copy_value()" value="<?  echo $color_library[$item_color_id] ?>"/>
								<input type="hidden" name="item_color_id_<? echo $i; ?>" id="item_color_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<? echo $item_color_id;?>" disabled="disabled"/>
								</td>
								<td>
								<input type="text" name="findia_<? echo $i; ?>" id="findia_<? echo $i; ?>" value="<? echo $dia_width; ?>" class="text_boxes" style="width:60px;" />
								</td>
                                 <td>
                             		<input type="text" name="printing_color_<? echo $i; ?>" id="printing_color_<? echo $i; ?>" style="width:60px;" class="text_boxes" onChange="copy_value()" value="<?  echo $color_library[$item_color_id] ?>"/>
                           			<input type="hidden" name="printing_color_id_<? echo $i; ?>" id="printing_color_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<? echo $item_color_id;?>" disabled="disabled"/>
                           		</td>
								<td>
								<input type="text" name="artworkno_<? echo $i; ?>" id="artworkno_<? echo $i; ?>" value="<? //echo $artwork_no; ?>" style="width:60px;" class="text_boxes">
								</td>
								<td>
								<input type="text" name="startdate_<? echo $i; ?>" id="startdate_<? echo $i; ?>" value="<? //echo $delivery_date; ?>" style="width:60px;" class="datepicker">
								</td>
								<td>
								<input type="text" name="enddate_<? echo $i; ?>" id="enddate_<? echo $i; ?>" value="<?  //echo $delivery_end_date; ?>" style="width:60px;" class="datepicker">
								</td>
								<td>
								<input type="text" name="txt_blanty_<? echo $i; ?>" id="txt_blanty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $i; ?>,'txt_woqnty');calculate_amount(<? echo $i; ?>)" value=""/>
							   <input type="hidden" name="txtreqnty_<? echo $i; ?>" id="txtreqnty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric"  value=""/>
								</td>
								<td>
								<input type="text" name="txt_woqnty_<? echo $i; ?>" id="txt_woqnty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $i; ?>,'txt_woqnty');calculate_amount(<? echo $i; ?>)" value=""/>
								</td>
								  <td>
								<?
								echo create_drop_down("uom_".$i, 50, $unit_of_measurement,"", 1, "--Select--",$uom,"copy_value(".$i.",'uom')",1,"$uom_item");
								?>
								</td>
								<td>
								<input type="text" name="txt_rate_<? echo $i; ?>" id="txt_rate_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $i; ?>,'txt_rate');calculate_amount(<? echo $i; ?>)" value="<? echo $rate; ?>">
								</td>
								<td>
								<input type="text" name="txt_amount_<? echo $i; ?>" id="txt_amount_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? echo $amount; ?>" disabled="disabled"/>
								</td>
								<td>
								<input type="text" name="txt_paln_cut_<? echo $i; ?>" id="txt_paln_cut_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? echo  $plan_cut_qnty; ?>" disabled>
								<input type="hidden" name="updateid_<? echo $i; ?>" id="updateid_<? echo $i; ?>" value="<? echo $id; ?>">
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
					if($cbo_level==2){
						$i=1;
						foreach($po_color_level_data_arr as $precost_conversion_cost_id=>$pre_cost_cost_conversion_cost_val){
							foreach($pre_cost_cost_conversion_cost_val as $color_id=>$color_val){
								foreach($color_val as $dia_width_id=>$dia_width_val){
							        $job_no                      = implode(",",array_unique($dia_width_val['job_no']));
									$po_number                   = implode(",",array_unique($dia_width_val['po_number']));
									$po_break_down_id            = implode(",",array_unique($dia_width_val['po_id']));
									$pre_cost_conversion_cost_id = $precost_conversion_cost_id;
									$body_part_id                = implode(",",array_unique($dia_width_val['body_part_id']));
									$color_type_id               = implode(",",array_unique($dia_width_val['color_type_id']));
									$construction                = implode(",",array_unique($dia_width_val['construction']));
									$composition                 = implode(",",array_unique($dia_width_val['composition']));
									$gsm_weight                  = implode(",",array_unique($dia_width_val['gsm_weight']));
									$dia_width                   = $dia_width_id;
									
									$color_size_table_id         = implode(",",array_unique($dia_width_val['color_size_table_id']));
									$color_number_id             = implode(",",array_unique($dia_width_val['color_number_id']));
									$item_color_id               = implode(",",array_unique($dia_width_val['color_number_id']));
									$uom                         = implode(",",array_unique($dia_width_val['uom']));
									
									
									$plan_cut_qnty               = array_sum($dia_width_val['plan_cut_qnty']);
									$req_qnty                    = def_number_format(array_sum($dia_width_val['req_qnty']),1,"");
									$blaqnty                     = def_number_format(array_sum($dia_width_val['blaqnty']),1,"");
									$rate                        = def_number_format(array_sum($dia_width_val['rate']),1,"");
									$amount                      = def_number_format(array_sum($dia_width_val['amount']),1,"");
									//$rate                        = def_number_format($amount/$req_qnty,1,"");
									if($is_short==2)
									{
										
										//echo $amount.'='.$req_qnty.'=='.$rate.', ';
										if($blaqnty>0)
										{
											$row_check++;
											?>
											<tr align="center">
													<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;">
													<? echo $job_no; ?>
													<input type="hidden" name="job_no_<? echo $i; ?>" id="job_no_<? echo  $i; ?>" value="<? echo $job_no; ?>" style="width:60px;" class="text_boxes" disabled="disabled">
													</td>
													<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;">
													<a href="#" onClick="setdata('<? echo $po_number;?>' )">View</a>
													<? //echo $po_number; ?>
													<input type="hidden" name="po_id_<? echo $i; ?>" id="po_id_<? echo  $i; ?>" value="<? echo $po_break_down_id; ?>" style="width:60px;" class="text_boxes" disabled="disabled">
													</td>
													<td>
													<? echo $body_part[$body_part_id];?>
													<input type="hidden" name="fabric_description_id_<? echo $i; ?>" id="fabric_description_id_<? echo $i; ?>" value="<? echo $pre_cost_conversion_cost_id; ?>" style="width:60px;" class="text_boxes" disabled="disabled">
													</td>
													<td>
													<? echo $color_type[$color_type_id];?>
													</td>
													<td>
													<? echo $construction;?>
													</td>
													 <td>
													<? echo $composition;?>
													</td>
													<td>
													<? echo $gsm_weight;?>
													</td>
													<td>
													<? echo $dia_width;?>
													 <input type="hidden" name="dia_<? echo $i; ?>" id="dia_<? echo $i; ?>" value="<? echo $dia_width; ?>" class="text_boxes" style="width:60px;" />
													</td>
													
													<td>
													<?  echo $color_library[$color_number_id] ?>
													<input type="hidden" name="gmts_color_id_<? echo $i; ?>" id="gmts_color_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<?  echo $color_number_id;?>"disabled="disabled"/>
						
													<input type="hidden" name="color_size_table_id_<? echo $i; ?>" id="color_size_table_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<?  echo $color_size_table_id;?>" disabled="disabled"/>
													</td>
													<td>
													<input type="text" name="item_color_<? echo $i; ?>" id="item_color_<? echo $i; ?>" style="width:60px;" class="text_boxes" onChange="copy_value()" value="<?  echo $color_library[$item_color_id] ?>"/>
													<input type="hidden" name="item_color_id_<? echo $i; ?>" id="item_color_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<? echo $item_color_id;?>" disabled="disabled"/>
													</td>
													
												  
												  
													<td>
													<input type="text" name="findia_<? echo $i; ?>" id="findia_<? echo $i; ?>" value="<? echo $dia_width; ?>" class="text_boxes" style="width:60px;" />
													</td>
                                                    <td>
                                                    <input type="text" name="printing_color_<? echo $i; ?>" id="printing_color_<? echo $i; ?>" style="width:60px;" class="text_boxes" onChange="copy_value()" value="<?  echo $color_library[$item_color_id] ?>"/>
                                                    <input type="hidden" name="printing_color_id_<? echo $i; ?>" id="printing_color_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<? echo $item_color_id;?>" disabled="disabled"/>
                                                	</td>
													<td>
													<input type="text" name="artworkno_<? echo $i; ?>" id="artworkno_<? echo $i; ?>" value="<? //echo $artwork_no; ?>" style="width:60px;" class="text_boxes">
													</td>
													<td>
													<input type="text" name="startdate_<? echo $i; ?>" id="startdate_<? echo $i; ?>" value="<? //echo $delivery_date; ?>" style="width:60px;" class="datepicker">
													</td>
													<td>
													<input type="text" name="enddate_<? echo $i; ?>" id="enddate_<? echo $i; ?>" value="<?  //echo $delivery_end_date; ?>" style="width:60px;" class="datepicker">
													</td>
													<td>
													<input type="text" name="txt_blanty_<? echo $i; ?>" id="txt_blanty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $i; ?>,'txt_woqnty');calculate_amount(<? echo $i; ?>)" value="<? echo $blaqnty; ?>"/>
												   <input type="hidden" name="txtreqnty_<? echo $i; ?>" id="txtreqnty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? echo $req_qnty; ?>"/>
						
													</td>
													<td>
													<input type="text" name="txt_woqnty_<? echo $i; ?>" id="txt_woqnty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $i; ?>,'txt_woqnty');calculate_amount(<? echo $i; ?>)" value="<? echo $blaqnty; ?>"/>
													</td>
													  <td>
													<?
													echo create_drop_down("uom_".$i, 50, $unit_of_measurement,"", 1, "--Select--",$uom,"copy_value(".$i.",'uom')",1,"$uom_item");
													?>
													</td>
													<td>
													<input type="text" name="txt_rate_<? echo $i; ?>" id="txt_rate_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $i; ?>,'txt_rate');calculate_amount(<? echo $i; ?>)" value="<? echo $rate; ?>">
													</td>
													<td>
													<input type="text" name="txt_amount_<? echo $i; ?>" id="txt_amount_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? echo $amount; ?>" disabled="disabled"/>
													</td>
													<td>
													<input type="text" name="txt_paln_cut_<? echo $i; ?>" id="txt_paln_cut_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? echo  $plan_cut_qnty; ?>" disabled>
													<input type="hidden" name="updateid_<? echo $i; ?>" id="updateid_<? echo $i; ?>" value="<? echo $id; ?>">
													</td>
												</tr>
												<?
                                                $i++;
											}
											
										
									}
									else
									{
										?>
										<tr align="center">
												<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;">
												<? echo $job_no; ?>
												<input type="hidden" name="job_no_<? echo $i; ?>" id="job_no_<? echo  $i; ?>" value="<? echo $job_no; ?>" style="width:60px;" class="text_boxes" disabled="disabled">
												</td>
												<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;">
												<a href="#" onClick="setdata('<? echo $po_number;?>' )">View</a>
												<? //echo $po_number; ?>
												<input type="hidden" name="po_id_<? echo $i; ?>" id="po_id_<? echo  $i; ?>" value="<? echo $po_break_down_id; ?>" style="width:60px;" class="text_boxes" disabled="disabled">
												</td>
												<td>
												<? echo $body_part[$body_part_id];?>
												<input type="hidden" name="fabric_description_id_<? echo $i; ?>" id="fabric_description_id_<? echo $i; ?>" value="<? echo $pre_cost_conversion_cost_id; ?>" style="width:60px;" class="text_boxes" disabled="disabled">
												</td>
												<td>
												<? echo $color_type[$color_type_id];?>
												</td>
												<td>
												<? echo $construction;?>
												</td>
												 <td>
												<? echo $composition;?>
												</td>
												<td>
												<? echo $gsm_weight;?>
												</td>
												<td>
												<? echo $dia_width;?>
												 <input type="hidden" name="dia_<? echo $i; ?>" id="dia_<? echo $i; ?>" value="<? echo $dia_width; ?>" class="text_boxes" style="width:60px;" />
												</td>
												
												<td>
												<?  echo $color_library[$color_number_id] ?>
												<input type="hidden" name="gmts_color_id_<? echo $i; ?>" id="gmts_color_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<?  echo $color_number_id;?>"disabled="disabled"/>
					
												<input type="hidden" name="color_size_table_id_<? echo $i; ?>" id="color_size_table_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<?  echo $color_size_table_id;?>" disabled="disabled"/>
												</td>
												<td>
												<input type="text" name="item_color_<? echo $i; ?>" id="item_color_<? echo $i; ?>" style="width:60px;" class="text_boxes" onChange="copy_value()" value="<?  echo $color_library[$item_color_id] ?>"/>
												<input type="hidden" name="item_color_id_<? echo $i; ?>" id="item_color_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<? echo $item_color_id;?>" disabled="disabled"/>
												</td>
												
												<td>
												<input type="text" name="findia_<? echo $i; ?>" id="findia_<? echo $i; ?>" value="<? echo $dia_width; ?>" class="text_boxes" style="width:60px;" />
												</td>
                                                 <td>
                                                    <input type="text" name="printing_color_<? echo $i; ?>" id="printing_color_<? echo $i; ?>" style="width:60px;" class="text_boxes" onChange="copy_value()" value="<?  echo $color_library[$item_color_id] ?>"/>
                                                    <input type="hidden" name="printing_color_id_<? echo $i; ?>" id="printing_color_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<? echo $item_color_id;?>" disabled="disabled"/>
                                                </td>
												<td>
												<input type="text" name="artworkno_<? echo $i; ?>" id="artworkno_<? echo $i; ?>" value="<? //echo $artwork_no; ?>" style="width:60px;" class="text_boxes">
												</td>
												<td>
												<input type="text" name="startdate_<? echo $i; ?>" id="startdate_<? echo $i; ?>" value="<? //echo $delivery_date; ?>" style="width:60px;" class="datepicker">
												</td>
												<td>
												<input type="text" name="enddate_<? echo $i; ?>" id="enddate_<? echo $i; ?>" value="<?  //echo $delivery_end_date; ?>" style="width:60px;" class="datepicker">
												</td>
												<td>
												<input type="text" name="txt_blanty_<? echo $i; ?>" id="txt_blanty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $i; ?>,'txt_woqnty');calculate_amount(<? echo $i; ?>)" value=""/>
											   <input type="hidden" name="txtreqnty_<? echo $i; ?>" id="txtreqnty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric"  value=""/>
					
												</td>
												<td>
												<input type="text" name="txt_woqnty_<? echo $i; ?>" id="txt_woqnty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $i; ?>,'txt_woqnty');calculate_amount(<? echo $i; ?>)" value=""/>
												</td>
												  <td>
												<?
												echo create_drop_down("uom_".$i, 50, $unit_of_measurement,"", 1, "--Select--",$uom,"copy_value(".$i.",'uom')",1,"$uom_item");
												?>
												</td>
												<td>
												<input type="text" name="txt_rate_<? echo $i; ?>" id="txt_rate_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $i; ?>,'txt_rate');calculate_amount(<? echo $i; ?>)" value="<? echo $rate; ?>">
												</td>
												<td>
												<input type="text" name="txt_amount_<? echo $i; ?>" id="txt_amount_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? echo $amount; ?>" disabled="disabled"/>
												</td>
												<td>
												<input type="text" name="txt_paln_cut_<? echo $i; ?>" id="txt_paln_cut_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? echo  $plan_cut_qnty; ?>" disabled>
												<input type="hidden" name="updateid_<? echo $i; ?>" id="updateid_<? echo $i; ?>" value="<? echo $id; ?>">
												</td>
											</tr>
											<?
											$i++;
									}
									
					}
					}
					}
						if($row_check==0 && $is_short==2) echo '<p style="font-size:16px; font-weight:bold; color:red; text-align:center">100% Booking Done.</p>';
					}
					?>
					</tbody>
				</table>
                
                <input type='hidden' id='json_data' name="json_data" value='<? echo json_encode($po_color_level_data_arr); ?>'/>
			</div>
		<?
		
	//}
}

if($action=="show_aop_booking")
{
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
	
	$data=explode("**",$data);
	$fabric_description_id=$data[0];
	$txt_order_no_id=$data[1];
	$txt_booking_no=$data[2];
	$cbo_level=$data[3];
	$conversion_cost_id=$data[4];
	$is_shrot=$data[5];
	if($fabric_description_id==0){
		echo "<strong>Select Fabric</strong>";
		die;
    }
	
	$fabric_description_array=array();
    $wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where id in($conversion_cost_id)");
	foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id){
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]!=0){
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls  
			where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."'");
			list($fabric_description_row)=$fabric_description;
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")];
		}
	}
	
	$cu_booking_data_arr=array();
	$sql="select a.id,a.pre_cost_fabric_cost_dtls_id,a.artwork_no,a.po_break_down_id,a.color_size_table_id,a.fabric_color_id,a.item_size,a.process,
	sensitivity,a.job_no,booking_no,a.booking_type,a.description,a.uom,a.delivery_date,a.delivery_end_date,a.sensitivity,a.wo_qnty,a.rate,
	a.amount,a.gmts_size,a.gmts_color_id,a.dia_width,a.printing_color_id
	from wo_booking_dtls a  where  a.booking_type=3 and a.process=35 and a.is_short=2  and a.pre_cost_fabric_cost_dtls_id in($data[4]) and a.status_active=1 and a.is_deleted=0 ";
	$dataArray=sql_select($sql);
	$z=1; $i=1;
	foreach($dataArray as $row){
		$id=$row[csf("id")];
		$pre_cost_conversion_cost_dtls_id=$row[csf("pre_cost_fabric_cost_dtls_id")];
		$artwork_no=$row[csf("artwork_no")];
		$po_break_down_id=$row[csf("po_break_down_id")];
		$color_size_table_id=$row[csf("color_size_table_id")];
		$fabric_color_id=$row[csf("fabric_color_id")];
		$printing_color_id=$row[csf("printing_color_id")];
		$item_size=$row[csf("item_size")];
        $process=$row[csf("process")];
		$sensitivity=$row[csf("sensitivity")];
        $job_no=$row[csf("job_no")];
		$booking_no=$row[csf("booking_no")];
		$booking_type=$row[csf("booking_type")];
		$description=$row[csf("description")];
		$uom=$row[csf("uom")];
		$delivery_date=$row[csf("delivery_date")];
		$delivery_end_date=$row[csf("delivery_end_date")];
        $wo_qnty=$row[csf("wo_qnty")];
		$rate=$row[csf("rate")];
		$amount=$row[csf("amount")];
		$color_number_id=$row[csf("gmts_color_id")];
		$size_number_id=$row[csf("gmts_size")];
		$dia_width=$row[csf("dia_width")];
		$cu_booking_data_arr[$pre_cost_conversion_cost_dtls_id][$po_break_down_id][$color_number_id][$dia_width]['wo_qnty']+=$wo_qnty;
	}
	
	
	$booking_data_arr=array();
	$sql="select a.id,a.pre_cost_fabric_cost_dtls_id,a.artwork_no,a.po_break_down_id,a.color_size_table_id,a.fabric_color_id,a.item_size,a.process,
	a.sensitivity,a.job_no,booking_no,a.booking_type,a.description,a.uom,a.delivery_date,a.delivery_end_date,a.wo_qnty,a.rate,
	a.amount,a.gmts_size,a.gmts_color_id,a.fin_dia,a.dia_width,a.printing_color_id
	from wo_booking_dtls a  where  a.booking_type=3 and a.process=35 and 
	a.booking_no='$txt_booking_no'  and a.pre_cost_fabric_cost_dtls_id=$data[4]  and   a.status_active=1 and a.is_deleted=0 ";
	$dataArray=sql_select($sql);
	$z=1; $i=1;
	foreach($dataArray as $row){
		$id=$row[csf("id")];
		$pre_cost_conversion_cost_dtls_id=$row[csf("pre_cost_fabric_cost_dtls_id")];
		$artwork_no=$row[csf("artwork_no")];
		$po_break_down_id=$row[csf("po_break_down_id")];
		$color_size_table_id=$row[csf("color_size_table_id")];
		$fabric_color_id=$row[csf("fabric_color_id")];
		$printing_color_id=$row[csf("printing_color_id")];
		$item_size=$row[csf("item_size")];
        $process=$row[csf("process")];
		$sensitivity=$row[csf("sensitivity")];
        $job_no=$row[csf("job_no")];
		$booking_no=$row[csf("booking_no")];
		$booking_type=$row[csf("booking_type")];
		$description=$row[csf("description")];
		$uom=$row[csf("uom")];
		$delivery_date=$row[csf("delivery_date")];
		$delivery_end_date=$row[csf("delivery_end_date")];
        $wo_qnty=$row[csf("wo_qnty")];
		$rate=$row[csf("rate")];
		$amount=$row[csf("amount")];
		$color_number_id=$row[csf("gmts_color_id")];
		$size_number_id=$row[csf("gmts_size")];
		$fin_dia=$row[csf("fin_dia")];
		$dia_width=$row[csf("dia_width")];
		$booking_data_arr[$pre_cost_conversion_cost_dtls_id][$po_break_down_id][$color_number_id][$dia_width]['id']=$id;
		$booking_data_arr[$pre_cost_conversion_cost_dtls_id][$po_break_down_id][$color_number_id][$dia_width]['fabric_description_id']=$pre_cost_conversion_cost_dtls_id;
		$booking_data_arr[$pre_cost_conversion_cost_dtls_id][$po_break_down_id][$color_number_id][$dia_width]['artwork_no']=$artwork_no;
		$booking_data_arr[$pre_cost_conversion_cost_dtls_id][$po_break_down_id][$color_number_id][$dia_width]['fabric_color_id']=$fabric_color_id;
		$booking_data_arr[$pre_cost_conversion_cost_dtls_id][$po_break_down_id][$color_number_id][$dia_width]['printing_color_id']=$printing_color_id;
		$booking_data_arr[$pre_cost_conversion_cost_dtls_id][$po_break_down_id][$color_number_id][$dia_width]['item_size']=$item_size;
		$booking_data_arr[$pre_cost_conversion_cost_dtls_id][$po_break_down_id][$color_number_id][$dia_width]['description']=$description;
		$booking_data_arr[$pre_cost_conversion_cost_dtls_id][$po_break_down_id][$color_number_id][$dia_width]['uom']=$uom;
		$booking_data_arr[$pre_cost_conversion_cost_dtls_id][$po_break_down_id][$color_number_id][$dia_width]['delivery_date']=$delivery_date;
		$booking_data_arr[$pre_cost_conversion_cost_dtls_id][$po_break_down_id][$color_number_id][$dia_width]['delivery_end_date']=$delivery_end_date;
		$booking_data_arr[$pre_cost_conversion_cost_dtls_id][$po_break_down_id][$color_number_id][$dia_width]['wo_qnty']+=$wo_qnty;
		$booking_data_arr[$pre_cost_conversion_cost_dtls_id][$po_break_down_id][$color_number_id][$dia_width]['rate']=$rate;
		$booking_data_arr[$pre_cost_conversion_cost_dtls_id][$po_break_down_id][$color_number_id][$dia_width]['amount']+=$amount;
		$booking_data_arr[$pre_cost_conversion_cost_dtls_id][$po_break_down_id][$color_number_id][$dia_width]['fin_dia']=$fin_dia;
	}
	
	$condition= new condition();
	if(str_replace("'","",$txt_order_no_id) !=''){
		$condition->po_id("in($txt_order_no_id)");
	}
	$condition->init();
	$fabric= new fabric($condition);
	
	$req_qty_arr=$fabric->getQtyArray_by_OrderFabriccostidGmtscolorAndDiaWidth_knitAndwoven_greyAndfinish();
	$req_amount_arr=$fabric->getAmountArray_by_OrderFabriccostidGmtscolorAndDiaWidth_knitAndwoven_greyAndfinish();
	
	$sql="select 
	a.job_no,
	b.id as po_break_down_id,
	b.po_number,
	min(c.id)as color_size_table_id,
	c.color_number_id,
	sum(c.plan_cut_qnty) as plan_cut_qnty,
	d.costing_per,
	e.id,
	e.fabric_description,
	e.cons_process,
	e.charge_unit,
	e.amount,
	e.color_break_down,
	e.process_loss,
	f.id as fid,
	f.body_part_id,
	f.color_type_id,
	f.construction,
	f.composition,
	f.gsm_weight,
	f.costing_per,
	f.uom,
	f.fab_nature_id,
	g.dia_width,
	
	CASE f.costing_per 
	WHEN 1 THEN 
	round((AVG(g.requirment)/12)*sum(c.plan_cut_qnty),4) 
	WHEN 2 THEN
	round((AVG(g.requirment)/1)*sum(c.plan_cut_qnty),4)  
	WHEN 3 THEN 
	round((AVG(g.requirment)/24)*sum(c.plan_cut_qnty),4) 
	WHEN 4 THEN 
	round((AVG(g.requirment)/36)*sum(c.plan_cut_qnty),4) 
	WHEN 5 THEN 
	round((AVG(g.requirment)/48)*sum(c.plan_cut_qnty),4) 
	ELSE 0 END as wo_req_qnty 
	
	from 
	wo_po_details_master a, 
	wo_po_break_down b,
	wo_po_color_size_breakdown c,
	wo_pre_cost_mst d,
	wo_pre_cost_fab_conv_cost_dtls e,
	wo_pre_cost_fabric_cost_dtls f,
	wo_pre_cos_fab_co_avg_con_dtls g 
	
	where 
	a.job_no=b.job_no_mst and 
	a.job_no=c.job_no_mst and 
	a.job_no=d.job_no and 
	a.job_no=e.job_no and 
	a.job_no=f.job_no and 
	a.job_no=g.job_no and 
	b.id=c.po_break_down_id and 
	b.id=g.po_break_down_id and 
	c.color_number_id=g.color_number_id and  
	c.size_number_id=g.gmts_sizes and 
	c.item_number_id=f.item_number_id and 
	f.id=g.pre_cost_fabric_cost_dtls_id and 
	e.fabric_description=f.id and 
	e.id in($conversion_cost_id) and 
	b.id in($txt_order_no_id) and 
	a.status_active=1 and 
	a.is_deleted=0  and 
	b.status_active=1 and 
	b.is_deleted=0 and 
	c.status_active=1 and 
	c.is_deleted=0  and 
	d.status_active=1 and 
	d.is_deleted=0 and 
	e.status_active=1 and 
	e.is_deleted=0 and 
	f.status_active=1 and 
	f.is_deleted=0  and 
	g.requirment >0
	
	group by 
	a.job_no,
	b.id,
	b.po_number,
	c.color_number_id,
	d.costing_per,
	e.id,
	e.fabric_description,
	e.cons_process,
	e.charge_unit,
	e.amount,
	e.color_break_down,
	e.process_loss,
	f.id,
	f.body_part_id,
	f.color_type_id,
	f.construction,
	f.composition,
	f.gsm_weight,
	f.costing_per,
	f.uom,
	f.fab_nature_id,
	g.dia_width
	
	order by 
	b.id";
	
	$dataArray=sql_select($sql);
	foreach($dataArray as $row){
		$job_no                      = $row[csf("job_no")];
		$po_number                   = $row[csf("po_number")];
		$po_break_down_id            = $row[csf("po_break_down_id")];
		$pre_cost_conversion_cost_id = $row[csf("id")];
		$body_part_id                = $row[csf("body_part_id")];
		$color_type_id               = $row[csf("color_type_id")];
		$construction                = $row[csf("construction")];
		$composition                 = $row[csf("composition")];
		$gsm_weight                  = $row[csf("gsm_weight")];
        $dia_width                   = $row[csf("dia_width")];
		
		$color_size_table_id         = $row[csf("color_size_table_id")];
		$color_number_id             = $row[csf("color_number_id")];
		
		$plan_cut_qnty               = $row[csf("plan_cut_qnty")];
		$wo_req_qnty                 = $row[csf("wo_req_qnty")];
		$process_loss                = $row[csf("process_loss")];
		$charge_unit                 = $row[csf("charge_unit")];
		$uom                         = $row[csf("uom")];

		$pre_cost_fabric_cost_dtls_id = $row[csf("fid")];
		$cbo_fabric_natu             = $row[csf("fab_nature_id")];
		if($cbo_fabric_natu==2){
			$wo_req_qnty = $req_qty_arr['knit']['grey'][$po_break_down_id][$pre_cost_fabric_cost_dtls_id][$color_number_id][$dia_width][$uom];
		}
		if($cbo_fabric_natu==3){
			$wo_req_qnty = $req_qty_arr['woven']['grey'][$po_break_down_id][$pre_cost_fabric_cost_dtls_id][$color_number_id][$dia_width][$uom];
		}
	
		
		$cu_wo_qnty        = $cu_booking_data_arr[$pre_cost_conversion_cost_id][$po_break_down_id][$color_number_id][$dia_width]['wo_qnty'];
		$woqnty      = 0;
		if($body_part_id == 3){
			$woqnty  = $plan_cut_qnty*2;
			$uom_item     = "1,2";
			$selected_uom = $uom;
		}
		else if($body_part_id==2){
			$woqnty  = $plan_cut_qnty*1;
			$uom_item     = "1,2";
			$selected_uom = $uom;
		}
		else if($body_part_id != 2 || $body_part_id != 3 ){
			$process_loss_qty = $wo_req_qnty * $process_loss / 100;
			$woqnty      = $wo_req_qnty - $process_loss_qty;
			$selected_uom     = $uom;
		}
		
		if($body_part_id==2 || $body_part_id==3){
			$rate   = 0;
			$amount = 0;	
		}
		else{
			$rate   = $charge_unit;
			$amount = $rate*$woqnty;
		}
		
		$blaqnty = $woqnty - $cu_wo_qnty;
		$rate    = $rate;
		$amount  = $amount;
		$uom     = $selected_uom;
		
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['job_no'][$po_break_down_id]              = $job_no ;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['po_number'][$po_break_down_id]           = $po_number ;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['po_id'][$po_break_down_id]               = $po_break_down_id;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['conversion_cost_id'][$po_break_down_id]  = $pre_cost_conversion_cost_id;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['body_part_id'][$po_break_down_id]        = $body_part_id;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['color_type_id'][$po_break_down_id]       = $color_type_id;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['construction'][$po_break_down_id]        = $construction;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['composition'][$po_break_down_id]         = $composition;

		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['gsm_weight'][$po_break_down_id]          = $gsm_weight;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['dia_width'][$po_break_down_id]           = $dia_width;
		
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['color_size_table_id'][$po_break_down_id] = $color_size_table_id ;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['color_number_id'][$po_break_down_id]     = $color_number_id ;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['req_qnty'][$po_break_down_id]            = $wo_req_qnty;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['cu_wo_qnty'][$po_break_down_id]          = $cu_wo_qnty;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['blaqnty'][$po_break_down_id]             = $blaqnty;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['uom'][$po_break_down_id]                 = $uom;
		
		
		
		$id                = $booking_data_arr[$pre_cost_conversion_cost_id][$po_break_down_id][$color_number_id][$dia_width]['id'];
		$artwork_no        = $booking_data_arr[$pre_cost_conversion_cost_id][$po_break_down_id][$color_number_id][$dia_width]['artwork_no'];
		$fabric_color_id   = $booking_data_arr[$pre_cost_conversion_cost_id][$po_break_down_id][$color_number_id][$dia_width]['fabric_color_id'];
		$printing_color_id = $booking_data_arr[$pre_cost_conversion_cost_id][$po_break_down_id][$color_number_id][$dia_width]['printing_color_id'];
		$item_size         = $booking_data_arr[$pre_cost_conversion_cost_id][$po_break_down_id][$color_number_id][$dia_width]['item_size'];
		$fin_dia           = $booking_data_arr[$pre_cost_conversion_cost_id][$po_break_down_id][$color_number_id][$dia_width]['fin_dia'];
		$uom               = $booking_data_arr[$pre_cost_conversion_cost_id][$po_break_down_id][$color_number_id][$dia_width]['uom'];
		$delivery_date     = $booking_data_arr[$pre_cost_conversion_cost_id][$po_break_down_id][$color_number_id][$dia_width]['delivery_date'];
		$delivery_end_date = $booking_data_arr[$pre_cost_conversion_cost_id][$po_break_down_id][$color_number_id][$dia_width]['delivery_end_date'];
		$wo_qnty           = $booking_data_arr[$pre_cost_conversion_cost_id][$po_break_down_id][$color_number_id][$dia_width]['wo_qnty'];
		$rate              = $booking_data_arr[$pre_cost_conversion_cost_id][$po_break_down_id][$color_number_id][$dia_width]['rate'];
		$amount            = $booking_data_arr[$pre_cost_conversion_cost_id][$po_break_down_id][$color_number_id][$dia_width]['amount'];
		
		
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['id'][$po_break_down_id]                  = $id;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['artwork_no'][$po_break_down_id]          = $artwork_no;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['fabric_color_id'][$po_break_down_id]     = $fabric_color_id;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['printing_color_id'][$po_break_down_id]     = $printing_color_id;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['fin_dia'][$po_break_down_id]             = $fin_dia;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['delivery_date'][$po_break_down_id]       = $delivery_date;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['delivery_end_date'][$po_break_down_id]   = $delivery_end_date;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['woqnty'][$po_break_down_id]              = $wo_qnty;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['rate'][$po_break_down_id]                = $rate;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['amount'][$po_break_down_id]              = $amount;
	}
	?>
			<div id="content_search_panel_<? echo $pre_cost_conversion_cost_id; ?>" style="" class="accord_close">
				<table class="rpt_table" border="1" width="1510" cellpadding="0" cellspacing="0" rules="all" id="tbl_table" style="table-layout: fixed;">
					<thead>
                        <th>Job No</th>
						<th>Po Number</th>
                        <th>Body Part</th>
                        <th>Color Type</th>
                        <th>Construction</th>
                        <th>Composition</th>
                        <th>Gsm</th>
                        <th>Dia</th>
						<th>Gmts. Color</th>
						<th>Item Color</th>
                        <th class="must_entry_caption">Fin Dia</th>
                        <th class="must_entry_caption">Printing Color</th>
                        <th>Artwork No</th>
                        <th>Delivery Start Date</th>
                        <th>Delivery End Date</th>
                        <th>Bla. Qnty</th>
                        <th>WO. Qnty</th>
                        <th>UOM</th>
                        <th>Rate</th>
                        <th>Amount</th>
                        <th>Plan Cut Qnty</th>
					</thead>
					<tbody>
					<?
					if($cbo_level==1){
					$i=1;
					foreach($po_color_level_data_arr as $precost_conversion_cost_id=>$pre_cost_cost_conversion_cost_val){
						foreach($pre_cost_cost_conversion_cost_val as $color_id=>$color_val){
							foreach($color_val as $dia_width_id=>$dia_width_val){
								foreach($dia_width_val['po_id'] as $po_id){
									$job_no                      = $dia_width_val['job_no'][$po_id];
									$po_number                   = $dia_width_val['po_number'][$po_id];
									$po_break_down_id            = $po_id;
									$pre_cost_conversion_cost_id = $precost_conversion_cost_id;
									$body_part_id                = $dia_width_val['body_part_id'][$po_id];
									$color_type_id               = $dia_width_val['color_type_id'][$po_id];
									$construction                = $dia_width_val['construction'][$po_id];
									$composition                 = $dia_width_val['composition'][$po_id];
									$gsm_weight                  = $dia_width_val['gsm_weight'][$po_id];
									$dia_width                   = $dia_width_id;
									
									$color_size_table_id         = $dia_width_val['color_size_table_id'][$po_id];
									$color_number_id             = $dia_width_val['color_number_id'][$po_id];
									$uom                         = $dia_width_val['uom'][$po_id];
									
									$plan_cut_qnty               = $dia_width_val['plan_cut_qnty'][$po_id];
									$req_qnty                    = def_number_format($dia_width_val['req_qnty'][$po_id],1,"");
									$blaqnty                     = def_number_format($dia_width_val['blaqnty'][$po_id],1,"");
									
									$booking_id                  = $dia_width_val['id'][$po_id];
									$artwork_no                  = $dia_width_val['artwork_no'][$po_id];
									$item_color_id               = $dia_width_val['fabric_color_id'][$po_id];
									$printing_color_id           = $dia_width_val['printing_color_id'][$po_id];
									$fin_dia                     = $dia_width_val['fin_dia'][$po_id];
									$delivery_date               = $dia_width_val['delivery_date'][$po_id];
									$delivery_end_date           = $dia_width_val['delivery_end_date'][$po_id];
									
									$woqnty                      = def_number_format($dia_width_val['woqnty'][$po_id],1,"");
									$rate                        = def_number_format($dia_width_val['rate'][$po_id],1,"");
									$amount                      = def_number_format($dia_width_val['amount'][$po_id],1,"");
									
						if($woqnty>0){
						?>
                        <tr align="center">
                            <td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;">
                            <? echo $job_no; ?>
                            <input type="hidden" name="job_no_<? echo $i; ?>" id="job_no_<? echo  $i; ?>" value="<? echo $job_no; ?>" style="width:60px;" class="text_boxes" disabled="disabled">
                            </td>
                            <td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;">
                            <? echo $po_number; ?>
                            <input type="hidden" name="po_id_<? echo $i; ?>" id="po_id_<? echo  $i; ?>" value="<? echo $po_break_down_id; ?>" style="width:60px;" class="text_boxes" disabled="disabled">
                            </td>
                            <td>
							<? echo $body_part[$body_part_id];?>
                            <input type="hidden" name="fabric_description_id_<? echo $i; ?>" id="fabric_description_id_<? echo $i; ?>" value="<? echo $pre_cost_conversion_cost_id; ?>" style="width:60px;" class="text_boxes" disabled="disabled">
                            </td>
                            <td>
							<? echo $color_type[$color_type_id];?>
                            </td>
                            <td>
							<? echo $construction;?>
                            </td>
                             <td>
							<? echo $composition;?>
                            </td>
                            <td>
							<? echo $gsm_weight;?>
                            </td>
                            <td>
							<? echo $dia_width;?>
                             <input type="hidden" name="dia_<? echo $i; ?>" id="dia_<? echo $i; ?>" value="<? echo $dia_width; ?>" class="text_boxes" style="width:60px;" />
                            </td>
                            
                            <td>
                            <?  echo $color_library[$color_number_id] ?>
                            <input type="hidden" name="gmts_color_id_<? echo $i; ?>" id="gmts_color_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<?  echo $color_number_id;?>"disabled="disabled"/>

                            <input type="hidden" name="color_size_table_id_<? echo $i; ?>" id="color_size_table_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<?  echo $color_size_table_id;?>" disabled="disabled"/>
                            </td>
                            <td>
                            <input type="text" name="item_color_<? echo $i; ?>" id="item_color_<? echo $i; ?>" style="width:60px;" class="text_boxes" onChange="copy_value()" value="<?  echo $color_library[$item_color_id] ?>"/>
                            <input type="hidden" name="item_color_id_<? echo $i; ?>" id="item_color_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<? echo $item_color_id;?>" disabled="disabled"/>
                            </td>
                            
                          
                          
                            <td>
                            <input type="text" name="findia_<? echo $i; ?>" id="findia_<? echo $i; ?>" value="<? echo $fin_dia; ?>" class="text_boxes" style="width:60px;" />
                            </td>
                             <td>
                             <input type="text" name="printing_color_<? echo $i; ?>" id="printing_color_<? echo $i; ?>" style="width:60px;" class="text_boxes" onChange="copy_value()" value="<?  echo $color_library[$printing_color_id] ?>"/>
                            <input type="hidden" name="printing_color_id_<? echo $i; ?>" id="printing_color_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<? echo $printing_color_id;?>" disabled="disabled"/>
                            </td>
                            <td>
                            <input type="text" name="artworkno_<? echo $i; ?>" id="artworkno_<? echo $i; ?>" value="<? echo $artwork_no; ?>" style="width:60px;" class="text_boxes">
                            </td>
                            <td>
                            <input type="text" name="startdate_<? echo $i; ?>" id="startdate_<? echo $i; ?>" value="<? echo change_date_format($delivery_date,"dd-mm-yyyy","-"); ?>" style="width:60px;" class="datepicker">
                            </td>
                            <td>
                            <input type="text" name="enddate_<? echo $i; ?>" id="enddate_<? echo $i; ?>" value="<? echo change_date_format($delivery_end_date,"dd-mm-yyyy","-"); ?>" style="width:60px;" class="datepicker">
                            </td>
                            <td>
                            <input type="text" name="txt_blanty_<? echo $i; ?>" id="txt_blanty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $i; ?>,'txt_woqnty');calculate_amount(<? echo $i; ?>)" value="<? if($is_shrot==2) echo $blaqnty; else echo ""; ?>"/>
                           <input type="hidden" name="txtreqnty_<? echo $i; ?>" id="txtreqnty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? if($is_shrot==2) echo $req_qnty; else echo ""; ?>"/>

                            </td>
                            <td>
                            <input type="text" name="txt_woqnty_<? echo $i; ?>" id="txt_woqnty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $i; ?>,'txt_woqnty');calculate_amount(<? echo $i; ?>)" value="<? echo $woqnty; ?>"/>
                            </td>
                              <td>
                            <?
                            echo create_drop_down("uom_".$i, 50, $unit_of_measurement,"", 1, "--Select--",$uom,"copy_value(".$i.",'uom')",1,"$uom_item");
                            ?>
                            </td>
                            <td>
                            <input type="text" name="txt_rate_<? echo $i; ?>" id="txt_rate_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $i; ?>,'txt_rate');calculate_amount(<? echo $i; ?>)" value="<? echo $rate; ?>">
                            </td>
                            <td>
                            <input type="text" name="txt_amount_<? echo $i; ?>" id="txt_amount_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? echo $amount; ?>" disabled="disabled"/>
                            </td>
                            <td>
                            <input type="text" name="txt_paln_cut_<? echo $i; ?>" id="txt_paln_cut_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? echo  $row[csf("plan_cut_qnty")]; ?>" disabled>
                            <input type="hidden" name="updateid_<? echo $i; ?>" id="updateid_<? echo $i; ?>" value="<? echo $booking_id; ?>">
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
					if($cbo_level==2){
						$i=1;
						foreach($po_color_level_data_arr as $precost_conversion_cost_id=>$pre_cost_cost_conversion_cost_val){
							foreach($pre_cost_cost_conversion_cost_val as $color_id=>$color_val){
								foreach($color_val as $dia_width_id=>$dia_width_val){
							        $job_no                      = implode(",",array_unique($dia_width_val['job_no']));
									$po_number                   = implode(",",array_unique($dia_width_val['po_number']));
									$po_break_down_id            = implode(",",array_unique($dia_width_val['po_id']));
									$pre_cost_conversion_cost_id = $precost_conversion_cost_id;
									$body_part_id                = implode(",",array_unique($dia_width_val['body_part_id']));
									$color_type_id               = implode(",",array_unique($dia_width_val['color_type_id']));
									$construction                = implode(",",array_unique($dia_width_val['construction']));
									$composition                 = implode(",",array_unique($dia_width_val['composition']));
									$gsm_weight                  = implode(",",array_unique($dia_width_val['gsm_weight']));
									$dia_width                   = $dia_width_id;
									
									$color_size_table_id         = implode(",",array_unique($dia_width_val['color_size_table_id']));
									$color_number_id             = implode(",",array_unique($dia_width_val['color_number_id']));
									$item_color_id               = implode(",",array_unique($dia_width_val['color_number_id']));
									$uom                         = implode(",",array_unique($dia_width_val['uom']));
									$plan_cut_qnty               = array_sum($dia_width_val['plan_cut_qnty']);
									$req_qnty                    = def_number_format(array_sum($dia_width_val['req_qnty']),1,"");
									$blaqnty                     = def_number_format(array_sum($dia_width_val['blaqnty']),1,"");
									
									
									$booking_id                  = implode(",",array_unique($dia_width_val['id']));
									$artwork_no                  = implode(",",array_unique($dia_width_val['artwork_no']));
									$item_color_id               = implode(",",array_unique($dia_width_val['fabric_color_id']));
									$printing_color_id           = implode(",",array_unique($dia_width_val['printing_color_id']));
									$fin_dia                     = implode(",",array_unique($dia_width_val['fin_dia']));
									$delivery_date               = implode(",",array_unique($dia_width_val['delivery_date']));
									$delivery_end_date           = implode(",",array_unique($dia_width_val['delivery_end_date']));
									
									$woqnty                      = def_number_format(array_sum($dia_width_val['woqnty']),1,"");
									$rate                        = def_number_format(array_sum($dia_width_val['rate']),1,"");
									$amount                      = def_number_format(array_sum($dia_width_val['amount']),1,"");
									$rate                        = def_number_format($amount/$woqnty,1,"");
									if($woqnty>0){
					?>
                     <tr align="center">
                            <td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;">
                            <? echo $job_no; ?>
                            <input type="hidden" name="job_no_<? echo $i; ?>" id="job_no_<? echo  $i; ?>" value="<? echo $job_no; ?>" style="width:60px;" class="text_boxes" disabled="disabled">
                            </td>
                            <td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;">
                            <a href="#" onClick="setdata('<? echo $po_number;?>' )">View</a>
                            <? //echo $po_number; ?>
                            <input type="hidden" name="po_id_<? echo $i; ?>" id="po_id_<? echo  $i; ?>" value="<? echo $po_break_down_id; ?>" style="width:60px;" class="text_boxes" disabled="disabled">
                            </td>
                            <td>
							<? echo $body_part[$body_part_id];?>
                            <input type="hidden" name="fabric_description_id_<? echo $i; ?>" id="fabric_description_id_<? echo $i; ?>" value="<? echo $pre_cost_conversion_cost_id; ?>" style="width:60px;" class="text_boxes" disabled="disabled">
                            </td>
                            <td>
							<? echo $color_type[$color_type_id];?>
                            </td>
                            <td>
							<? echo $construction;?>
                            </td>
                             <td>
							<? echo $composition;?>
                            </td>
                            <td>
							<? echo $gsm_weight;?>
                            </td>
                            <td>
							<? echo $dia_width;?>
                             <input type="hidden" name="dia_<? echo $i; ?>" id="dia_<? echo $i; ?>" value="<? echo $dia_width; ?>" class="text_boxes" style="width:60px;" />
                            </td>
                            
                            <td>
                            <?  echo $color_library[$color_number_id] ?>
                            <input type="hidden" name="gmts_color_id_<? echo $i; ?>" id="gmts_color_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<?  echo $color_number_id;?>"disabled="disabled"/>

                            <input type="hidden" name="color_size_table_id_<? echo $i; ?>" id="color_size_table_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<?  echo $color_size_table_id;?>" disabled="disabled"/>
                            </td>
                            <td>
                            <input type="text" name="item_color_<? echo $i; ?>" id="item_color_<? echo $i; ?>" style="width:60px;" class="text_boxes" onChange="copy_value()" value="<?  echo $color_library[$item_color_id] ?>"/>
                            <input type="hidden" name="item_color_id_<? echo $i; ?>" id="item_color_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<? echo $item_color_id;?>" disabled="disabled"/>
                            </td>
                            <td>
                            <input type="text" name="findia_<? echo $i; ?>" id="findia_<? echo $i; ?>" value="<? echo $fin_dia; ?>" class="text_boxes" style="width:60px;" />
                            </td>
                             <td>
                              <input type="text" name="printing_color_<? echo $i; ?>" id="printing_color_<? echo $i; ?>" style="width:60px;" class="text_boxes" onChange="copy_value()" value="<?  echo $color_library[$printing_color_id] ?>"/>
                            <input type="hidden" name="printing_color_id_<? echo $i; ?>" id="printing_color_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<? echo $printing_color_id;?>" disabled="disabled"/>
                            </td>
                            <td>
                            <input type="text" name="artworkno_<? echo $i; ?>" id="artworkno_<? echo $i; ?>" value="<? echo $artwork_no; ?>" style="width:60px;" class="text_boxes">
                            </td>
                            <td>
                            <input type="text" name="startdate_<? echo $i; ?>" id="startdate_<? echo $i; ?>" value="<? echo change_date_format($delivery_date,"dd-mm-yyyy","-"); ?>" style="width:60px;" class="datepicker">
                            </td>
                            <td>
                            <input type="text" name="enddate_<? echo $i; ?>" id="enddate_<? echo $i; ?>" value="<? echo change_date_format($delivery_end_date,"dd-mm-yyyy","-"); ?>" style="width:60px;" class="datepicker">
                            </td>
                            <td>
                            <input type="text" name="txt_blanty_<? echo $i; ?>" id="txt_blanty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $i; ?>,'txt_woqnty');calculate_amount(<? echo $i; ?>)" value="<? if($is_shrot==2) echo $blaqnty; else echo ""; ?>"/>
                           <input type="hidden" name="txtreqnty_<? echo $i; ?>" id="txtreqnty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? if($is_shrot==2) echo $req_qnty; else echo ""; ?>"/>

                            </td>
                            <td>
                            <input type="text" name="txt_woqnty_<? echo $i; ?>" id="txt_woqnty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $i; ?>,'txt_woqnty');calculate_amount(<? echo $i; ?>)" value="<? echo $woqnty; ?>"/>
                            </td>
                            <td>
                            <?
                            echo create_drop_down("uom_".$i, 50, $unit_of_measurement,"", 1, "--Select--",$uom,"copy_value(".$i.",'uom')",1,"$uom_item");
                            ?>
                            </td>
                            <td>
                            <input type="text" name="txt_rate_<? echo $i; ?>" id="txt_rate_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $i; ?>,'txt_rate');calculate_amount(<? echo $i; ?>)" value="<? echo $rate; ?>">
                            </td>
                            <td>
                            <input type="text" name="txt_amount_<? echo $i; ?>" id="txt_amount_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? echo $amount; ?>" disabled="disabled"/>
                            </td>
                            <td>
                            <input type="text" name="txt_paln_cut_<? echo $i; ?>" id="txt_paln_cut_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? echo  $row[csf("plan_cut_qnty")]; ?>" disabled>
                            <input type="hidden" name="updateid_<? echo $i; ?>" id="updateid_<? echo $i; ?>" value="<? echo $booking_id; ?>">
                            </td>
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
                <input type='hidden' id='json_data' name="json_data" value='<? echo json_encode($po_color_level_data_arr); ?>'/>
			</div>
		<?
	//}
}

if ($action=="fabric_detls_list_view"){
	$data=explode("**",$data);
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");

	$po_number_arr=return_library_array( "select id,po_number from wo_po_break_down", "id", "po_number");
	$txt_booking_no="'".$data[0]."'";

	$wo_pre_cost_fab_conv_cost_dtls_id=sql_select('select a.id AS "aid",a.body_part_id AS "body_part_id",a.color_type_id AS "color_type_id",a.fabric_description AS "fabric_description",a.gsm_weight AS "gsm_weight",b.id AS "bid",b.cons_process AS "cons_process",c.id AS "id",c.job_no AS "job_no",c.po_break_down_id AS "po_break_down_id",c.booking_no AS "booking_no",c.pre_cost_fabric_cost_dtls_id AS "pre_cost_fabric_cost_dtls_id",c.dia_width AS "dia_width",c.wo_qnty AS "wo_qnty",c.amount AS "amount",c.gmts_color_id AS "gmts_color_id" from  wo_pre_cost_fabric_cost_dtls a,wo_pre_cost_fab_conv_cost_dtls b,wo_booking_dtls c  where a.job_no=b.job_no and a.job_no=c.job_no and a.id=b.fabric_description and b.id=c.pre_cost_fabric_cost_dtls_id  and c.booking_no='.$txt_booking_no.' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0');
	
	foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $sql_row){
	$job_level_arr[$sql_row['pre_cost_fabric_cost_dtls_id']]['booking_no'][$sql_row['id']]=$sql_row['booking_no'];	
	$job_level_arr[$sql_row['pre_cost_fabric_cost_dtls_id']]['fabric_cost_id'][$sql_row['id']]=$sql_row['aid'];
	$job_level_arr[$sql_row['pre_cost_fabric_cost_dtls_id']]['job_no'][$sql_row['id']]=$sql_row['job_no'];
	$job_level_arr[$sql_row['pre_cost_fabric_cost_dtls_id']]['po_id'][$sql_row['id']]=$sql_row['po_break_down_id'];
	$job_level_arr[$sql_row['pre_cost_fabric_cost_dtls_id']]['po_number'][$sql_row['id']]=$po_number_arr[$sql_row['po_break_down_id']];
	$job_level_arr[$sql_row['pre_cost_fabric_cost_dtls_id']]['booking_id'][$sql_row['id']]=$sql_row['id'];
	$job_level_arr[$sql_row['pre_cost_fabric_cost_dtls_id']]['wo_qnty'][$sql_row['id']]+=$sql_row['wo_qnty'];
	$job_level_arr[$sql_row['pre_cost_fabric_cost_dtls_id']]['amount'][$sql_row['id']]+=$sql_row['amount'];
	$fabric_description_array[$sql_row["pre_cost_fabric_cost_dtls_id"]]=$body_part[$sql_row["body_part_id"]].', '.$color_type[$sql_row["color_type_id"]].', '.$sql_row["fabric_description"].', '.$sql_row["gsm_weight"];
	$color_Arr[$sql_row['pre_cost_fabric_cost_dtls_id']][$sql_row['gmts_color_id']]=$color_library[$sql_row['gmts_color_id']];
	$Dia_Arr[$sql_row['pre_cost_fabric_cost_dtls_id']][$sql_row['dia_width']]=$sql_row['dia_width'];

	}
	?>
    <div id="" style="" class="accord_close">
    
        <table class="rpt_table" border="1" width="1100" cellpadding="0" cellspacing="0" rules="all" id="">
            <thead>
                <th width="50px">Sl</th>
                <th width="50px"></th>
                <th width="100px">Job No</th>
                <th width="100px">Po No</th>
                <th width="350px">Fabric Description</th>
                <th width="100">Gmts Color</th>
                <th width="100">Dia</th>
                <th width="80px">WO. Qnty</th>
                <th width="80">Amount</th>
                 <th width=""></th>
            </thead>
            <tbody>
            <?
            $i=1;
			foreach($job_level_arr as $key=>$precost_id){
			$booking_no=implode(",",array_unique($precost_id['booking_no']));
			$job_no=implode(",",array_unique($precost_id['job_no']));
			$po_break_down_id=implode(",",array_unique($precost_id['po_id']));
			$fabric_cost_id=implode(",",array_unique($precost_id['fabric_cost_id']));
			$po_number=implode(",",array_unique($precost_id['po_number']));
			$wo_qnty=def_number_format(array_sum($precost_id['wo_qnty']),1,"");
			$booking_id=implode(",",array_unique($precost_id['booking_id']));
			$amount=def_number_format(array_sum($precost_id['amount']),1,"");
			$rate=def_number_format($amount/$grey_fab_qnty,1,"");
            ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer" >
                    <td> <? echo $i; ?></td>
                     <td align="center"><a href="#"  onClick="set_data('<? echo $po_break_down_id;  ?>','<? echo $fabric_cost_id; ?>','<? echo $key; ?>','<? echo $booking_id?>')" >Edit</a></td>
                    <td><? echo  $job_no; ?></td>
                    <td align="center"><a href="#" onClick="setdata('<? echo $po_number;?>' )">View</a></td>
                    <td><? echo  $fabric_description_array[$key]; ?></td>
                    <td width="100" style="word-break: break-all;word-wrap: break-word">
					<? echo implode(",",$color_Arr[$key]); ?>
                    </td>
                    <td width="100" style="word-break: break-all;word-wrap: break-word">
                    <? echo implode(",",$Dia_Arr[$key]); ?>
                    </td>
                    <td align="right"><? echo  number_format($wo_qnty,4); ?></td>
                    <td align="right"><? echo  number_format($amount,4); ?></td>
                    <td align="center"><a href="#"  onClick="deletedata('<? echo $po_break_down_id;  ?>','<? echo $fabric_cost_id; ?>','<? echo $key; ?>','<? echo $booking_id?>')" >Delete</a></td>
                </tr>
            <?	
            $i++;
            }
            ?>
            </tbody>
        </table>
    </div>
		<?
}




if ($action=="save_update_delete"){
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if ($operation==0){
		$con = connect();
		if($db_type==0){
			mysql_query("BEGIN");
		}
		if ( check_table_status( $_SESSION['menu_id'], 1 )==0 ){ 
			echo "15**0"; 
			disconnect($con);die;
		}
		$response_booking_no="";
		if($db_type==0){
			$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'SB', date("Y",time()), 5, 
			"select booking_no_prefix, booking_no_prefix_num from wo_booking_mst where company_id=$cbo_company_name and booking_type=3 
			and YEAR(insert_date)=".date('Y',time())." order by booking_no_prefix_num desc ", "booking_no_prefix", "booking_no_prefix_num" ));
		}
		if($db_type==2){
			$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'SB', date("Y",time()), 5,"select booking_no_prefix,
			booking_no_prefix_num from wo_booking_mst where company_id=$cbo_company_name and booking_type=3 and 
			to_char(insert_date,'YYYY')=".date('Y',time())." order by booking_no_prefix_num desc ", "booking_no_prefix", "booking_no_prefix_num" ));
		}
		
		$id=return_next_id( "id", "wo_booking_mst", 1 ) ;
		$field_array="id,booking_type,is_short,booking_month,booking_year,booking_no_prefix,booking_no_prefix_num,booking_no,company_id,buyer_id,
		item_category,supplier_id,currency_id,exchange_rate,booking_date,delivery_date,
		pay_mode,source,attention,process,cbo_level,inserted_by,insert_date";
		$data_array ="(".$id.",3,".$cbo_is_short.",".$cbo_booking_month.",".$cbo_booking_year.",'".$new_booking_no[1]."',".$new_booking_no[2].",'".$new_booking_no[0]."',".$cbo_company_name.",".$cbo_buyer_name.",12,".$cbo_supplier_name.",".$cbo_currency.",".$txt_exchange_rate.",".$txt_booking_date.",".$txt_delivery_date.",".$cbo_pay_mode.",".$cbo_source.",".$txt_attention.",35,".$cbo_level.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		$response_booking_no=$new_booking_no[0];
		$rID=sql_insert("wo_booking_mst",$field_array,$data_array,0);
		
		if($db_type==0){
			if($rID){
				mysql_query("COMMIT");  
				echo "0**".$response_booking_no;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$response_booking_no;
			}
		}
		if($db_type==2 || $db_type==1 ){
			if($rID){
				oci_commit($con);  
				echo "0**".$response_booking_no;
			}
			else{
				oci_rollback($con);  
				echo "10**".$response_booking_no;
			}
		}
		check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
	}
	
	
	else if ($operation==1){
		 $con = connect();
		 $is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no");
		foreach($sql as $row){
			$is_approved=$row[csf('is_approved')];
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;
		}
		$sales_order=0;
		$sqls=sql_select("select job_no from fabric_sales_order_mst where sales_booking_no=$txt_booking_no");
		foreach($sqls as $rows){
			$sales_order=$rows[csf('job_no')];
		}
		if($sales_order){
			echo "sal1**".str_replace("'","",$txt_booking_no)."**".$sales_order;
			disconnect($con);die;
		}
		if(str_replace("'","",$cbo_pay_mode)==2){
			$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no  and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($pi_number){
				echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
				disconnect($con);die;
			}
		}
		 if($db_type==0){
			mysql_query("BEGIN");
		 }
		 $field_array_up="booking_month*booking_year*buyer_id*item_category*supplier_id*currency_id*exchange_rate*booking_date*delivery_date*pay_mode*source*attention*cbo_level*updated_by*update_date";
		 $data_array_up ="".$cbo_booking_month."*".$cbo_booking_year."*".$cbo_buyer_name."*12*".$cbo_supplier_name."*".$cbo_currency."*".$txt_exchange_rate."*".$txt_booking_date."*".$txt_delivery_date."*".$cbo_pay_mode."*".$cbo_source."*".$txt_attention."*".$cbo_level."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		 $rID=sql_update("wo_booking_mst",$field_array_up,$data_array_up,"booking_no","".$txt_booking_no."",0);
		if($db_type==0){
			if($rID){
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		if($db_type==2 || $db_type==1 ){
			if($rID){
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
		$is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no");
		foreach($sql as $row){
			$is_approved=$row[csf('is_approved')];
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;
		}
		$sales_order=0;
		$sqls=sql_select("select job_no from fabric_sales_order_mst where sales_booking_no=$txt_booking_no");
		foreach($sqls as $rows){
			$sales_order=$rows[csf('job_no')];
		}
		if($sales_order){
			echo "sal1**".str_replace("'","",$txt_booking_no)."**".$sales_order;
			disconnect($con);die;
		}
		if(str_replace("'","",$cbo_pay_mode)==2){
			$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no  and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($pi_number){
				echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
			disconnect($con);	die;
			}
		}
		if($db_type==0){
			mysql_query("BEGIN");
		}
		//$rID=execute_query( "delete from wo_booking_mst where  booking_no =$txt_booking_no",0);
		//$rID1=execute_query( "delete from wo_booking_dtls where  booking_no =$txt_booking_no",0);
		$delete_cause=str_replace("'","",$delete_cause);
		$delete_cause=str_replace('"','',$delete_cause);
		$delete_cause=str_replace('(','',$delete_cause);
		$delete_cause=str_replace(')','',$delete_cause);
		
		$rID=execute_query( "update wo_booking_mst set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."'   where  booking_no=$txt_booking_no",0);
		$rID1=execute_query( "update wo_booking_dtls set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."'   where  booking_no=$txt_booking_no",0);
		if($db_type==0){
			if($rID1){
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		
		if($db_type==2 || $db_type==1 ){
			if($rID1){
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
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
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
		 $id_dtls=return_next_id( "id", "wo_booking_dtls", 1 ) ;
		 $field_array1="id,is_short,job_no,pre_cost_fabric_cost_dtls_id,color_size_table_id,artwork_no,po_break_down_id,booking_no,booking_type,fabric_color_id,
         gmts_color_id,printing_color_id,description,uom,process,wo_qnty,rate,amount,delivery_date,delivery_end_date,dia_width,fin_dia,inserted_by,
		 insert_date";
		 $new_array_color=array();
		 for ($i=1;$i<=$row_num;$i++)
		 {
			 $job_no="job_no_".$i;
			 $po_id="po_id_".$i;
			 $fabric_description_id="fabric_description_id_".$i;
			 $color_size_table_id="color_size_table_id_".$i;			 
			 $dia="dia_".$i;
			 $artworkno="artworkno_".$i;
			 $gmts_color_id="gmts_color_id_".$i;
			 $item_color="item_color_".$i;
			 $printing_color="printing_color_".$i;
			 $uom="uom_".$i;
			 $txt_woqnty="txt_woqnty_".$i;
			 $txt_rate="txt_rate_".$i;
			 $txt_amount="txt_amount_".$i;
			 $txt_paln_cut="txt_paln_cut".$i;
			 $updateid="updateid_".$i;
			 $startdate="startdate_".$i;
			 $enddate="enddate_".$i;
			 $findia="findia_".$i;
			 
			
			 
			 
			 $new_array_color=return_library_array( "select a.fabric_color_id,b.id,b.color_name from wo_booking_dtls a, lib_color b 
			 where b.id=a.fabric_color_id and a.pre_cost_fabric_cost_dtls_id=".$$fabric_description_id."", "id", "color_name");
			 
			 /*if (!in_array(str_replace("'","",$$item_color),$new_array_color))
			 {
				  $color_id = return_id( str_replace("'","",$$item_color), $color_library, "lib_color", "id,color_name");  
				  $new_array_color[$color_id]=str_replace("'","",$$item_color);
			 }
			 else 
			 {
				 $color_id =  array_search(str_replace("'","",$$item_color), $new_array_color); 
			 }

			 if (!in_array(str_replace("'","",$$printing_color),$new_array_color))
			 {
				
				  $print_color_id = return_id( str_replace("'","",$$printing_color), $color_library, "lib_color", "id,color_name");  
				  // echo "10**".$print_color_id;die;
				  $new_array_color[$print_color_id]=str_replace("'","",$$printing_color);
			 }
			 else 
			 {
				 $print_color_id =  array_search(str_replace("'","",$$printing_color), $new_array_color); 
			 }*/
			  if(str_replace("'","",$$item_color)!="")
			 {
				 if (!in_array(str_replace("'","",$$item_color),$new_array_color))
				 {
					  $color_id = return_id( str_replace("'","",$$item_color), $color_library, "lib_color", "id,color_name","162");
					  $new_array_color[$color_id]=str_replace("'","",$$item_color);
				 }
				 else $color_id =  array_search(str_replace("'","",$$item_color), $new_array_color);
			 }
			 else $color_id =0;
			 
			 
			 if(str_replace("'","",$$printing_color)!="")
			 {
				 if (!in_array(str_replace("'","",$$printing_color),$new_array_color))
				 {
	
					  $print_color_id = return_id( str_replace("'","",$$printing_color), $color_library, "lib_color", "id,color_name","162");
					  // echo "10**".$print_color_id;die;
					  $new_array_color[$print_color_id]=str_replace("'","",$$printing_color);
				 }
				 else $print_color_id =  array_search(str_replace("'","",$$printing_color), $new_array_color);
			 }
			 else $print_color_id =0;
			 
			 if ($i!=1) $data_array1 .=",";
			 $data_array1 .="(".$id_dtls.",".$cbo_is_short.",".$$job_no.",".$$fabric_description_id.",".$$color_size_table_id.",".$$artworkno.",".$$po_id.",
			 ".$txt_booking_no.",3,".$color_id.",".$$gmts_color_id.",".$print_color_id.",".$$fabric_description_id.",".$$uom.",
			 35,".$$txt_woqnty.",".$$txt_rate.",".$$txt_amount.",".$$startdate.",".$$enddate.",".$$dia.",".$$findia.",
			 ".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		     $id_dtls=$id_dtls+1;
		 }
		// echo "10**INSERT INTO wo_booking_dtls (".$field_array1.") VALUES ".$data_array1;die;
		 $rID=sql_insert("wo_booking_dtls",$field_array1,$data_array1,0);
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "0**";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);  
				echo "0**";
			}
			else
			{
				oci_rollback($con);  
				echo "10**";
			}
		}
		check_table_status( $_SESSION['menu_id'],0); 
		disconnect($con);
		die;
	}
	
	
	else if ($operation==1)   // Update Here
	{
		 $con = connect();
		 $is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no");
		foreach($sql as $row){
			$is_approved=$row[csf('is_approved')];
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;
		}
		$sales_order=0;
		$sqls=sql_select("select job_no from fabric_sales_order_mst where sales_booking_no=$txt_booking_no");
		foreach($sqls as $rows){
			$sales_order=$rows[csf('job_no')];
		}
		if($sales_order){
			echo "sal1**".str_replace("'","",$txt_booking_no)."**".$sales_order;
			disconnect($con);die;
		}
		if(str_replace("'","",$cbo_pay_mode)==2){
			$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no  and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($pi_number){
				echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
			disconnect($con);	die;
			}
		}
		 if($db_type==0){
			mysql_query("BEGIN");
		 }
		 
		 $field_array_up1="artwork_no*color_size_table_id*fabric_color_id*gmts_color_id*printing_color_id*description*uom*wo_qnty*rate*amount*delivery_date*delivery_end_date*dia_width*fin_dia*updated_by*update_date";
		 $new_array_color=array();
		 for ($i=1;$i<=$row_num;$i++){
			 $job_no="job_no_".$i;
			 $po_id="po_id_".$i;
			 $fabric_description_id="fabric_description_id_".$i;
			 $color_size_table_id="color_size_table_id_".$i;			 
			 $dia="dia_".$i;
			 $artworkno="artworkno_".$i;
			 $gmts_color_id="gmts_color_id_".$i;
			 $item_color="item_color_".$i;
			 $printing_color="printing_color_".$i;
			 $uom="uom_".$i;
			 $txt_woqnty="txt_woqnty_".$i;
			 $txt_rate="txt_rate_".$i;
			 $txt_amount="txt_amount_".$i;
			 $txt_paln_cut="txt_paln_cut".$i;
			 $updateid="updateid_".$i;
			 $startdate="startdate_".$i;
			 $enddate="enddate_".$i;
			 $findia="findia_".$i;
			 
		     $new_array_color=return_library_array( "select a.fabric_color_id,b.id,b.color_name from wo_booking_dtls a, lib_color b where b.id=a.fabric_color_id and a.pre_cost_fabric_cost_dtls_id=".$$fabric_description_id."", "id", "color_name"  );
			 
			 if(str_replace("'","",$$item_color)!="")
			 {
				 if (!in_array(str_replace("'","",$$item_color),$new_array_color))
				 {
					  $color_id = return_id( str_replace("'","",$$item_color), $color_library, "lib_color", "id,color_name","162");
					  $new_array_color[$color_id]=str_replace("'","",$$item_color);
				 }
				 else $color_id =  array_search(str_replace("'","",$$item_color), $new_array_color);
			 }
			 else $color_id =0;
			 
			 
			 if(str_replace("'","",$$printing_color)!="")
			 {
				 if (!in_array(str_replace("'","",$$printing_color),$new_array_color))
				 {
	
					  $print_color_id = return_id( str_replace("'","",$$printing_color), $color_library, "lib_color", "id,color_name","162");
					  // echo "10**".$print_color_id;die;
					  $new_array_color[$print_color_id]=str_replace("'","",$$printing_color);
				 }
				 else $print_color_id =  array_search(str_replace("'","",$$printing_color), $new_array_color);
			 }
			 else $print_color_id =0;
			 
			if(str_replace("'",'',$$updateid)!=""){
			$id_arr[]=str_replace("'",'',$$updateid);
			$data_array_up1[str_replace("'",'',$$updateid)] =explode("*",("".$$artworkno."*".$$color_size_table_id."*".$color_id."*".$$gmts_color_id."*".$print_color_id."*".$$fabric_description_id."*".$$uom."*".$$txt_woqnty."*".$$txt_rate."*".$$txt_amount."*".$$startdate."*".$$enddate."*".$$dia."*".$$findia."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
		 }
		 $rID=execute_query(bulk_update_sql_statement( "wo_booking_dtls", "id", $field_array_up1, $data_array_up1, $id_arr ),1);
         check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID==1){
				mysql_query("COMMIT");  
				echo "1**";
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			
			if($rID==1)
			{
				oci_commit($con);  
				echo "1**";
			}
			else{
				oci_rollback($con); 
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	
	
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		$is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no");
		foreach($sql as $row){
			$is_approved=$row[csf('is_approved')];
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;
		}
		$sales_order=0;
		$sqls=sql_select("select job_no from fabric_sales_order_mst where sales_booking_no=$txt_booking_no");
		foreach($sqls as $rows){
			$sales_order=$rows[csf('job_no')];
		}
		if($sales_order){
			echo "sal1**".str_replace("'","",$txt_booking_no)."**".$sales_order;
			disconnect($con);die;
		}
		if(str_replace("'","",$cbo_pay_mode)==2){
			$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no  and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($pi_number){
				echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
				disconnect($con);die;
			}
		}
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$delete_cause=str_replace("'","",$delete_cause);
		$delete_cause=str_replace('"','',$delete_cause);
		$delete_cause=str_replace('(','',$delete_cause);
		$delete_cause=str_replace(')','',$delete_cause);
		for ($i=1;$i<=$row_num;$i++){
			 $fabric_description_id="fabric_description_id_".$i;
			 $updateid="updateid_".$i;
			// $rID=execute_query( "delete from wo_booking_dtls where  pre_cost_fabric_cost_dtls_id in (".str_replace("'","",$$fabric_description_id).")",0);
			 //$rID=execute_query( "update wo_booking_dtls set status_active=0,is_deleted=1,updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."' where  pre_cost_fabric_cost_dtls_id in (".str_replace("'","",$$fabric_description_id).") and booking_no=$txt_booking_no",0);
			 $rID=execute_query( "update wo_booking_dtls set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."' where  id in (".str_replace("'","",$$updateid).") and booking_no=$txt_booking_no",0);


		 }
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "2**";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID){
				oci_commit($con);  
				echo "2**";
			}
			else{
				oci_rollback($con); 
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="save_update_delete_dtls_job_level"){
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$json_data=json_decode(str_replace("'","",$json_data));
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0";disconnect($con); die;}		
		 $id_dtls=return_next_id( "id", "wo_booking_dtls", 1 ) ;
		 $field_array1="id,is_short,job_no,pre_cost_fabric_cost_dtls_id,artwork_no,po_break_down_id,booking_no,booking_type,fabric_color_id,
         gmts_color_id,printing_color_id,description,uom,process,wo_qnty,rate,amount,delivery_date,delivery_end_date,dia_width,fin_dia,inserted_by,
		 insert_date";
		 $j=1;
		 $new_array_color=array();
		 for ($i=1;$i<=$row_num;$i++)
		 {
			 $job_no="job_no_".$i;
			 $po_id="po_id_".$i;
			 $fabric_description_id="fabric_description_id_".$i;
			 $color_size_table_id="color_size_table_id_".$i;			 
			 $dia="dia_".$i;
			 $artworkno="artworkno_".$i;
			 $gmts_color_id="gmts_color_id_".$i;
			 $item_color="item_color_".$i;
			 $printing_color="printing_color_".$i;
			 $uom="uom_".$i;
			 $txt_woqnty="txt_woqnty_".$i;
			 $txt_rate="txt_rate_".$i;
			 $txt_amount="txt_amount_".$i;
			 $txt_paln_cut="txt_paln_cut".$i;
			 $updateid="updateid_".$i;
			 $startdate="startdate_".$i;
			 $enddate="enddate_".$i;
			 $findia="findia_".$i;
			 $txtreqnty="txtreqnty_".$i;
			 
			 $precostid=str_replace("'","",$$fabric_description_id);
			 $colorid=str_replace("'","",$$gmts_color_id);
			 $dia=str_replace("'","",$$dia);
             $reqqnty=str_replace("'","",$$txtreqnty);
			 $woq=str_replace("'","",$$txt_woqnty);
			 $rate=str_replace("'","",$$txt_rate);
			 
			 $new_array_color=return_library_array( "select a.fabric_color_id,b.id,b.color_name from wo_booking_dtls a, lib_color b 
			 where b.id=a.fabric_color_id and a.pre_cost_fabric_cost_dtls_id=".$$fabric_description_id."", "id", "color_name");
			 
			  if(str_replace("'","",$$item_color)!="")
			 {
				 if (!in_array(str_replace("'","",$$item_color),$new_array_color))
				 {
					  $color_id = return_id( str_replace("'","",$$item_color), $color_library, "lib_color", "id,color_name","162");
					  $new_array_color[$color_id]=str_replace("'","",$$item_color);
				 }
				 else $color_id =  array_search(str_replace("'","",$$item_color), $new_array_color);
			 }
			 else $color_id =0;
			 
			 
			 if(str_replace("'","",$$printing_color)!="")
			 {
				 if (!in_array(str_replace("'","",$$printing_color),$new_array_color))
				 {
	
					  $print_color_id = return_id( str_replace("'","",$$printing_color), $color_library, "lib_color", "id,color_name","162");
					  // echo "10**".$print_color_id;die;
					  $new_array_color[$print_color_id]=str_replace("'","",$$printing_color);
				 }
				 else $print_color_id =  array_search(str_replace("'","",$$printing_color), $new_array_color);
			 }
			 else $print_color_id =0;
			 
			 foreach($json_data->$precostid->$colorid->$dia->po_id as $poId)
			 {
				 if($woq>0)
				 {
					 if(str_replace("'","",$cbo_is_short)==2)
					 {
						 $wQty=($json_data->$precostid->$colorid->$dia->req_qnty->$poId/$reqqnty)*$woq;
					 }
					 else
					 {
						 $wQty=$woq;
					 }
					 
					 $amount=$wQty*$rate;
					 if ($j!=1) $data_array1 .=",";
					$data_array1 .="(".$id_dtls.",".$cbo_is_short.",".$$job_no.",".$$fabric_description_id.",".$$artworkno.",".$poId.",".$txt_booking_no.",3,".$color_id.",".$colorid.",".$print_color_id.",".$$fabric_description_id.",".$$uom.",35,".$wQty.",".$$txt_rate.",".$amount.",".$$startdate.",".$$enddate.",'".$dia."',".$$findia.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					$id_dtls=$id_dtls+1;
					$j++;
				 }
			 }
		 }
		 $rID=sql_insert("wo_booking_dtls",$field_array1,$data_array1,0);
		 check_table_status( $_SESSION['menu_id'],0);   
		 if($db_type==0){
			if($rID){
				mysql_query("COMMIT");  
				echo "0**";
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		 }
		
		 if($db_type==2 || $db_type==1 ){
			if($rID){
				oci_commit($con);  
				echo "0**";
			}
			else{
				oci_rollback($con);  
				echo "10**";
			}
		 }
		 disconnect($con);
		 die;
	}
	
	
	else if ($operation==1){
		 $con = connect();
		 $is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no");
		foreach($sql as $row){
			$is_approved=$row[csf('is_approved')];
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;
		}
		$sales_order=0;
		$sqls=sql_select("select job_no from fabric_sales_order_mst where sales_booking_no=$txt_booking_no");
		foreach($sqls as $rows){
			$sales_order=$rows[csf('job_no')];
		}
		if($sales_order){
			echo "sal1**".str_replace("'","",$txt_booking_no)."**".$sales_order;
			disconnect($con);die;
		}
		if(str_replace("'","",$cbo_pay_mode)==2){
			$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no  and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($pi_number){
				echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
				disconnect($con);die;
			}
		}
		 if($db_type==0){
			mysql_query("BEGIN");
		 }
		 
		 $field_array_up1="artwork_no*fabric_color_id*gmts_color_id*printing_color_id*description*uom*wo_qnty*rate*amount*delivery_date*delivery_end_date*dia_width*fin_dia*updated_by*update_date";
		 $new_array_color=array();
		 for ($i=1;$i<=$row_num;$i++){
			 $job_no="job_no_".$i;
			 $po_id="po_id_".$i;
			 $fabric_description_id="fabric_description_id_".$i;
			 $color_size_table_id="color_size_table_id_".$i;			 
			 $dia="dia_".$i;
			 $artworkno="artworkno_".$i;
			 $gmts_color_id="gmts_color_id_".$i;
			 $item_color="item_color_".$i;
			 $printing_color="printing_color_".$i;
			 $uom="uom_".$i;
			 $txt_woqnty="txt_woqnty_".$i;
			 $txt_rate="txt_rate_".$i;
			 $txt_amount="txt_amount_".$i;
			 $txt_paln_cut="txt_paln_cut".$i;
			 $updateid="updateid_".$i;
			 $startdate="startdate_".$i;
			 $enddate="enddate_".$i;
			 $findia="findia_".$i;
			 $txtreqnty="txtreqnty_".$i;
			 
			 $precostid=str_replace("'","",$$fabric_description_id);
			 $colorid=str_replace("'","",$$gmts_color_id);
			 $dia=str_replace("'","",$$dia);
             $reqqnty=str_replace("'","",$$txtreqnty);
			 $woq=str_replace("'","",$$txt_woqnty);
			 $rate=str_replace("'","",$$txt_rate);
			 
		     $new_array_color=return_library_array( "select a.fabric_color_id,b.id,b.color_name from wo_booking_dtls a, lib_color b where b.id=a.fabric_color_id and a.pre_cost_fabric_cost_dtls_id=".$$fabric_description_id."", "id", "color_name");
			 
			  if(str_replace("'","",$$item_color)!="")
			 {
				 if (!in_array(str_replace("'","",$$item_color),$new_array_color))
				 {
					  $color_id = return_id( str_replace("'","",$$item_color), $color_library, "lib_color", "id,color_name","162");
					  $new_array_color[$color_id]=str_replace("'","",$$item_color);
				 }
				 else $color_id =  array_search(str_replace("'","",$$item_color), $new_array_color);
			 }
			 else $color_id =0;
			 
			 
			 if(str_replace("'","",$$printing_color)!="")
			 {
				 if (!in_array(str_replace("'","",$$printing_color),$new_array_color))
				 {
	
					  $print_color_id = return_id( str_replace("'","",$$printing_color), $color_library, "lib_color", "id,color_name","162");
					  // echo "10**".$print_color_id;die;
					  $new_array_color[$print_color_id]=str_replace("'","",$$printing_color);
				 }
				 else $print_color_id =  array_search(str_replace("'","",$$printing_color), $new_array_color);
			 }
			 else $print_color_id =0;
			 
			 foreach($json_data->$precostid->$colorid->$dia->po_id as $poId){
				 if(str_replace("'","",$cbo_is_short)==2)
				 {
					 $wQty=($json_data->$precostid->$colorid->$dia->req_qnty->$poId/$reqqnty)*$woq;
				 }
				 else
				 {
					 $wQty=$woq;
				 }
				 $amount=$wQty*$rate;
				 if(str_replace("'",'',$$updateid)!=""){
					$id_arr[]=str_replace("'",'',$json_data->$precostid->$colorid->$dia->id->$poId);
					$data_array_up1[str_replace("'",'',$json_data->$precostid->$colorid->$dia->id->$poId)] =explode("*",("".$$artworkno."*".$color_id."*".$colorid."*".$printing_color_id."*".$$fabric_description_id."*".$$uom."*".$wQty."*".$rate."*".$amount."*".$$startdate."*".$$enddate."*'".$dia."'*".$$findia."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				 }
			 }
		 }
		 $rID=execute_query(bulk_update_sql_statement( "wo_booking_dtls", "id", $field_array_up1, $data_array_up1, $id_arr ),1);
         check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID==1){
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			
			if($rID==1)
			{
				oci_commit($con);  
				echo "1**".str_replace("'","",$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
			}
			else{
				oci_rollback($con); 
				echo "10**".str_replace("'","",$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
			}
		}
		disconnect($con);
		die;
	}
	
	
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		$is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no");
		foreach($sql as $row){
			$is_approved=$row[csf('is_approved')];
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;
		}
		$sales_order=0;
		$sqls=sql_select("select job_no from fabric_sales_order_mst where sales_booking_no=$txt_booking_no");
		foreach($sqls as $rows){
			$sales_order=$rows[csf('job_no')];
		}
		if($sales_order){
			echo "sal1**".str_replace("'","",$txt_booking_no)."**".$sales_order;
			disconnect($con);die;
		}
		if(str_replace("'","",$cbo_pay_mode)==2){
			$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no  and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($pi_number){
				echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
				disconnect($con);die;
			}
		}
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$delete_cause=str_replace("'","",$delete_cause);
		$delete_cause=str_replace('"','',$delete_cause);
		$delete_cause=str_replace('(','',$delete_cause);
		$delete_cause=str_replace(')','',$delete_cause);
		for ($i=1;$i<=$row_num;$i++){
			 $fabric_description_id="fabric_description_id_".$i;
			 $updateid="updateid_".$i;
			 //$rID=execute_query( "delete from wo_booking_dtls where  pre_cost_fabric_cost_dtls_id in (".str_replace("'","",$$fabric_description_id).")",0);
			 //$rID=execute_query( "update wo_booking_dtls set status_active=0,is_deleted=1,updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."' where  pre_cost_fabric_cost_dtls_id in (".str_replace("'","",$$fabric_description_id).") and booking_no=$txt_booking_no",0);
			 $rID=execute_query( "update wo_booking_dtls set status_active=0,is_deleted=1,delete_cause='$delete_cause',updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."' where  id in (".str_replace("'","",$$updateid).") and booking_no=$txt_booking_no",0);


		 }
		if($db_type==0){
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "2**";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID){
				oci_commit($con);  
				echo "2**";
			}
			else{
				oci_rollback($con); 
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
}
 

if ($action=="service_booking_popup")
{
  	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
?>
     
	<script>
	function set_checkvalue(){
			if(document.getElementById('chk_job_wo_po').value==0) document.getElementById('chk_job_wo_po').value=1;
			else document.getElementById('chk_job_wo_po').value=0;
	}
	function js_set_value(booking_no)
	{
		document.getElementById('selected_booking').value=booking_no;
		parent.emailwindow.hide();
	}
	
    </script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            	<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
                 	<thead>
                        	<th  colspan="6">
                              <?
                              echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --" );
                              ?>
                            </th>
                    </thead>
                    <thead>                	 
                        <th width="150">Company Name</th>
                        <th width="150">Buyer Name</th>
                        <th width="100">Job  No</th>
                        <th width="100">Booking No</th>
                        <th width="200">Date Range</th>
                        <th><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">WO Without Item</th>           
                    </thead>
        			<tr>
                    	<td> <input type="hidden" id="selected_booking">
							<? 
								echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '', "load_drop_down( 'service_booking_aop_urmi_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
							?>
                        </td>
                   	<td id="buyer_td">
                     	<? 
						echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select Buyer --" );
						?>
                    </td>
                     <td>
					  <input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:100px">
					 </td> 
                      <td>
					  <input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:100px">
					 </td> 
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
					  <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 </td> 
            		 <td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('chk_job_wo_po').value, 'create_booking_search_list_view', 'search_div', 'service_booking_aop_urmi_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
        		</tr>
                <tr><td colspan="6"><? echo load_month_buttons(1);  ?></td></tr>
             </table>
    </form>
    <div id="search_div"></div>
   </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if ($action=="create_booking_search_list_view"){
	$data=explode('_',$data);
	if ($data[0]!=0) $company="  a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_id='$data[1]'"; else $buyer="";
	
	if($db_type==0){
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	
	if($db_type==2){
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}
	
	if($data[6]==1){
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num='$data[5]'    "; else  $booking_cond="";
		if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num='$data[4]'  "; else  $job_cond=""; 
	}
	if($data[6]==4 || $data[6]==0){
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[5]%'  $booking_year_cond  "; else  $booking_cond="";
		if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num like '%$data[4]%'  $year_cond  "; else  $job_cond=""; 
	}
	
	if($data[6]==2){
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '$data[5]%'  $booking_year_cond  "; else  $booking_cond="";
		if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num like '$data[4]%'  $year_cond  "; else  $job_cond=""; 
	}
	if($data[6]==3){
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[5]'  $booking_year_cond  "; else  $booking_cond="";
		if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num like '%$data[4]'  $year_cond  "; else  $job_cond=""; 
	}
	
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$po_no=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
	
	$arr=array (2=>$comp,3=>$buyer_arr,5=>$po_no,6=>$item_category,7=>$suplier);
	if($data[7]==1){
	 $sql= "select a.booking_no_prefix_num,a.booking_no,a.booking_date,a.company_id,a.buyer_id,
	a.job_no,a.po_break_down_id,a.item_category,a.fabric_source,a.supplier_id from wo_booking_mst a 
	where $company $buyer $booking_date and a.booking_no not in( select a.booking_no from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c 
	where $company $buyer $booking_date and  a.booking_no=b.booking_no and b.job_no=c.job_no and a.booking_type=3 and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and a.process=35
	    $booking_cond $job_cond 
	group by a.booking_no_prefix_num,a.booking_no,a.booking_date,a.company_id,a.buyer_id,
	c.job_no_prefix_num,b.po_break_down_id,a.item_category,a.fabric_source,a.supplier_id ) and  a.booking_type=3 and  a.status_active=1 and a.is_deleted=0 and a.process=35
	    $booking_cond 
	group by a.booking_no_prefix_num,a.booking_no,a.booking_date,a.company_id,a.buyer_id,
	a.job_no,a.po_break_down_id,a.item_category,a.fabric_source,a.supplier_id
	order by booking_no_prefix_num desc";
		echo  create_list_view("list_view", "Booking No,Booking Date,Company,Buyer,Job No.,PO number,Fabric Nature,Supplier", "100,80,100,100,90,200,80","970","320",0, $sql , "js_set_value", "booking_no", "", 1, "0,0,company_id,buyer_id,0,po_break_down_id,item_category,supplier_id", $arr , "booking_no_prefix_num,booking_date,company_id,buyer_id,job_no_prefix_num,po_break_down_id,item_category,supplier_id", '','','0,3,0,0,0,0,0,0','','');

	}
	if($data[7]==0){
	  $sql= "select a.booking_no_prefix_num,a.booking_no,a.booking_date,a.company_id,a.buyer_id,
	c.job_no_prefix_num,b.po_break_down_id,a.item_category,a.fabric_source,a.supplier_id from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c 
	where $company $buyer $booking_date and  a.booking_no=b.booking_no and b.job_no=c.job_no and a.booking_type=3 and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and a.process=35
	    $booking_cond $job_cond 
	group by a.booking_no_prefix_num,a.booking_no,a.booking_date,a.company_id,a.buyer_id,
	c.job_no_prefix_num,b.po_break_down_id,a.item_category,a.fabric_source,a.supplier_id
	order by booking_no_prefix_num desc"; 
			echo  create_list_view("list_view", "Booking No,Booking Date,Company,Buyer,Job No.,PO number,Fabric Nature,Supplier", "100,80,100,100,90,200,80","970","320",0, $sql , "js_set_value", "booking_no", "", 1, "0,0,company_id,buyer_id,0,po_break_down_id,item_category,supplier_id", $arr , "booking_no_prefix_num,booking_date,company_id,buyer_id,job_no_prefix_num,po_break_down_id,item_category,supplier_id", '','','0,3,0,0,0,0,0,0','','');

	}
	
}

if($action=="terms_condition_popup") {
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	</head>
	<body>
        <div align="center" style="width:100%;">
			<? echo load_freeze_divs ("../../../",$permission);  ?>
            <fieldset>
                <form id="termscondi_1" autocomplete="off">
                    <input type="text" id="txt_booking_no" name="txt_booking_no" value="<? echo str_replace("'","",$txt_booking_no) ?>"/>
                    <table width="650" cellspacing="0" class="rpt_table" border="0" id="tbl_termcondi_details" rules="all">
                        <thead>
                            <tr>
                            <th width="50">Sl</th><th width="530">Terms</th><th ></th>
                            </tr>
                        </thead>
                        <tbody>
							<?
                            $data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no");// quotation_id='$data'
                            if ( count($data_array)>0){
								$i=0;
								foreach( $data_array as $row ){
									$i++;
									?>
									<tr id="settr_1" align="center">
                                        <td>
                                        <? echo $i;?>
                                        </td>
                                        <td>
                                        <input type="text" id="termscondition_<? echo $i;?>"  name="termscondition_<? echo $i;?>" style="width:95%" class="text_boxes" value="<? echo $row[csf('terms')]; ?>"  /> 
                                        </td>
                                        <td> 
                                        <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
                                        <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>);" />
                                        </td>
									</tr>
								<?
								}
                            }
                            else{
								$data_array=sql_select("select id, terms from  lib_terms_condition where is_default=1");
								foreach( $data_array as $row ){
									$i++;
									?>
									<tr id="settr_1" align="center">
                                        <td>
                                        <? echo $i;?>
                                        </td>
                                        <td>
                                        <input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>"  /> 
                                        </td>
                                        <td>
                                        <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
                                        <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> );" />
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
    <script>
	function add_break_down_tr(i){
		var row_num=$('#tbl_termcondi_details tr').length-1;
		if (row_num!=i){
			return false;
		}
		else{
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
		}
	}
	
	function fn_deletebreak_down_tr(rowNo) {   
		var numRow = $('table#tbl_termcondi_details tbody tr').length; 
		if(numRow==rowNo && rowNo!=1){
			$('#tbl_termcondi_details tbody tr:last').remove();
		}
	}
	
	function fnc_fabric_booking_terms_condition( operation ){
		var row_num=$('#tbl_termcondi_details tr').length-1;
		var data_all="";
		for (var i=1; i<=row_num; i++){
			if (form_validation('termscondition_'+i,'Term Condition')==false){
				return;
			}
			data_all=data_all+get_submitted_data_string('txt_booking_no*termscondition_'+i,"../../../",i);
		}
		var data="action=save_update_delete_fabric_booking_terms_condition&operation="+operation+'&total_row='+row_num+data_all;
		http.open("POST","trims_booking_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_booking_terms_condition_reponse;
	}
	
	function fnc_fabric_booking_terms_condition_reponse(){
		if(http.readyState == 4) {
			var reponse=trim(http.responseText).split('**');
			if (reponse[0].length>2) reponse[0]=10;
			if(reponse[0]==0 || reponse[0]==1){
				parent.emailwindow.hide();
			}
		}
	}
	</script>         
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}
if($action=="show_trim_booking_report11111"){
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$deling_marcent_arr=return_library_array("select id,team_member_name from lib_mkt_team_member_info","id","team_member_name");
	$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	?>
	<div style="width:1333px" align="center">       
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black;">
           <tr>
               <td width="100" style="padding:1px"> 
               <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="1250">                                     
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:20px;">
                              <?php      
                                    echo $company_library[$cbo_company_name];
                              ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">  
                            <?

								//echo return_field_value("location_name", "lib_location", "company_id='".$cbo_company_name."'");
                            
							 $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name"); 
                            foreach ($nameArray as $result)
                            { 
                            ?>
                                            <? echo $result[csf('plot_no')]; ?> 
                                            <? echo $result[csf('level_no')]?>
                                            <? echo $result[csf('road_no')]; ?> 
                                            <? echo $result[csf('block_no')];?> 
                                           <? echo $result[csf('city')];?> 
                                            <? echo $result[csf('zip_code')]; ?> 
                                            <?php echo $result[csf('province')];?> 
                                            <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
                                            <? echo $result[csf('email')];?> 
                                            <? echo $result[csf('website')];
                            }
							?>
                               </td> 
                            </tr>
                            <tr>
                            <td align="center" style="font-size:20px">  
                            <strong>Service Booking For AOP</strong>
                             </td> 
                            </tr>
                      </table>
                </td> 
                <td width="250" id="barcode_img_id" > 
               
               </td>      
            </tr>
       </table>
		<?
		$booking_grand_total=0;
		$job_no="";
		$nameArray_job=sql_select( "select distinct b.job_no  from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_no=$txt_booking_no"); 
        foreach ($nameArray_job as $result_job)
        {
			$job_no.=$result_job[csf('job_no')].",";
			
		}
		$team_member_arr=return_library_array("select id,team_member_name from lib_mkt_team_member_info","id","team_member_name");
		$dealing_merchant="";
		$nameArray_merchant=sql_select( "select distinct b.dealing_marchant, b.style_ref_no  from wo_booking_mst a, wo_po_details_master b where a.job_no=b.job_no and a.booking_no=$txt_booking_no"); 
       $style_sting="";
	    foreach ($nameArray_merchant as $result_job)
        {
			$dealing_merchant.=$team_member_arr[$result_job[csf('dealing_marchant')]].",";
			$style_sting.=$result_job[csf('style_ref_no')].",";
		}
		$po_no="";
		$nameArray_job=sql_select( "select distinct b.po_number  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no"); 
        foreach ($nameArray_job as $result_job)
        {
			$po_no.=$result_job[csf('po_number')].",";
		}
        $nameArray=sql_select( "select a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.source  from wo_booking_mst a where  a.booking_no=$txt_booking_no"); 
        foreach ($nameArray as $result)
        {
			$varcode_booking_no=$result[csf('booking_no')];
        ?>
       <table width="90%" style="border:1px solid black;table-layout: fixed;">                    	
            <tr>
                <td colspan="6" valign="top"></td>                             
            </tr>                                                
            <tr>
                <td width="100" style="font-size:12px"><b>Booking No </b>   </td>
                <td width="110">:&nbsp;<? echo $result[csf('booking_no')];?> </td>
                <td width="100" style="font-size:12px"><b>Booking Date</b></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>
                <td width="100"><span style="font-size:12px"><b>Delivery Date</b></span></td>
                <td>:&nbsp;<? echo change_date_format($result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>	
            </tr>
            <tr>
                <td style="font-size:12px"><b>Currency</b></td>
                <td>:&nbsp;<? echo $currency[$result[csf('currency_id')]]; ?></td>
                <td style="font-size:12px"><b>Conversion Rate</b></td>
                <td>:&nbsp;<? echo $result[csf('exchange_rate')]; ?></td>
                <td style="font-size:12px"><b>Source</b></td>
                <td>:&nbsp;<? echo $source[$result[csf('source')]]; ?></td>
            </tr> 
             <tr>
                <td style="font-size:12px"><b>Supplier Name</b>   </td>
                <td >:&nbsp;<? echo $supplier_name_arr[$result[csf('supplier_id')]];?>    </td>
                <td style="font-size:12px"><b>Supplier Address</b></td>
               	<td>:&nbsp;<? echo $supplier_address_arr[$result[csf('supplier_id')]];?></td>
                <td style="font-size:12px"><b>Attention</b></td>
                <td>:&nbsp;<? echo $result[csf('attention')]; ?></td>
            </tr>  
            <tr>
                <td style="font-size:12px"><b>Job No</b>   </td>
                <td>:&nbsp;<?  echo $all_job_arr=rtrim($job_no,','); ?>  </td>
               	<td style="font-size:12px"><b>Style Ref.</b> </td>
                <td  style="font-size:12px;overflow:hidden;text-overflow: ellipsis;white-space: nowrap;">:&nbsp;<? echo rtrim($style_sting,','); ?> </td>
                <td style="font-size:12px"><b>Dealing Merchant</b> </td>
                <td  style="font-size:12px;overflow:hidden;text-overflow: ellipsis;white-space: nowrap;">:&nbsp;<? echo rtrim($dealing_merchant,','); ?> </td>
            </tr> 
             <tr>
                 
               	<td style="font-size:12px"><b>PO No</b> </td>
                <td style="font-size:18px;overflow:hidden;text-overflow: ellipsis;white-space: nowrap;" colspan="5">:&nbsp;<b><? echo rtrim($po_no,", "); ?></b>
                </td>
               
                
            </tr> 
        </table>  
		<?
        }
        ?>
          <!--==============================================AS PER GMTS COLOR START=========================================  -->
		<?
		//========================================
		$fabric_description_array=array();
		//echo "select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where job_no='".rtrim($job_no,", ")."'";
	$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where job_no='".rtrim($job_no,", ")."'");
	foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id)
	{
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]!=0)
		{
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls  where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."'");
			list($fabric_description_row)=$fabric_description;
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")];
		}
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]==0)
		{
			//echo "select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls  where  job_no='$data'";
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls  where  job_no='".rtrim($job_no,", ")."'");
			//list($fabric_description_row)=$fabric_description;
			foreach( $fabric_description as $fabric_description_row)
	        {
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]].=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")].", ";
			
			//$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]="All Fabrics  ".$conversion_cost_head_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("cons_process")]];
			}
		}
		
							
	}
	//print_r($fabric_description_array);
	//=================================================
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1 and process=35 and status_active=1 and is_deleted=0"); 
		//echo "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1"; 
        $nameArray_color=sql_select( "select distinct fabric_color_id from wo_booking_dtls   where  booking_no=$txt_booking_no and sensitivity=1  and process=35 and status_active=1 and is_deleted=0"); 
		if(count($nameArray_color)>0)
		{
        ?>
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="90%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="<? echo count($nameArray_color)+8; ?>" align="">
                <strong>As Per Garments Color</strong>
                </td>
            </tr>
            <tr>
                <td><strong>Sl</strong> </td>
                <td><strong>Service Type</strong> </td>
                <td><strong>Item Description</strong> </td>
                <td><strong>Item Color</strong> </td>
                <?  				
                foreach($nameArray_color  as $result_color)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $color_library[$result_color[csf('fabric_color_id')]];?></strong></td>
                <?	}    ?>				
                <td align="center"><strong>Total</strong></td>
                <td align="center"><strong>UOM</strong></td>
                <td align="center"><strong>Rate</strong></td>
                <td align="center"><strong>Amount</strong></td>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
			//echo "select distinct description,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1 and process=".$result_item[csf('process')]." <br>";
            $nameArray_item_description=sql_select( "select distinct description,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1 and process=".$result_item[csf('process')].""); 
            ?>
            <tr>
                <td rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $conversion_cost_head_array[$result_item[csf('process')]]; ?>
                </td>
                <? 
                $color_tatal=array();
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $item_desctiption_total=0;
                ?>
                <td ><? echo rtrim($fabric_description_array[$result_itemdescription[csf('description')]],", "); ?> </td>
                <td><? //echo $result_itemdescription['brand_supplier']; ?> Booking Qnty </td>
                <?
                foreach($nameArray_color  as $result_color)
                {
                $nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where   booking_no=$txt_booking_no and sensitivity=1 and process=". $result_item[csf('process')]." and description='". $result_itemdescription[csf('description')]."' and fabric_color_id=".$result_color[csf('fabric_color_id')]."and rate='". $result_itemdescription[csf('rate')]."'");                          
                foreach($nameArray_color_size_qnty as $result_color_size_qnty)
                {
                ?>
                <td style="text-align:right">
                <? 
                if($result_color_size_qnty[csf('cons')]!= "")
                {
                echo number_format($result_color_size_qnty[csf('cons')],2);
                $item_desctiption_total+=$result_color_size_qnty[csf('cons')] ;
                if (array_key_exists($result_color[csf('fabric_color_id')], $color_tatal))
                {
                $color_tatal[$result_color[csf('fabric_color_id')]]+=$result_color_size_qnty[csf('cons')];
                }
                else
                {
                $color_tatal[$result_color[csf('fabric_color_id')]]=$result_color_size_qnty[csf('cons')]; 
                }
                }
                else echo "";
                ?>
                </td>
                <?   
                }
                }
                ?>
                
                <td style="text-align:right"><? echo number_format($item_desctiption_total,2); ?></td>
                <td style="text-align:center"><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
                <td style="text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="text-align:right">
                <? 
                $amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="text-align:right" colspan="2"><strong> Item Total</strong></td>
                <?
                foreach($nameArray_color  as $result_color)
                {
                
                ?>
                <td style="text-align:right">
                <?
                if($color_tatal[$result_color[fabric_color_id]] !='')
                {
                echo number_format($color_tatal[$result_color[fabric_color_id]],2);  
                }
                ?>
                </td>
            <?
            }
            ?>
                <td style="text-align:right"><? echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="text-align:right"><? //echo number_format(round($color_total),0); ?></td>
                <td style="text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right"   colspan="<? echo count($nameArray_color)+7; ?>"><strong>Total</strong></td>
                <td  style="text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        
        <!--==============================================AS PER GMTS COLOR END=========================================  -->
        
        <!--==============================================AS PER GMTS SIZE START=========================================  -->
		<?
		 //$nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1"); 
		//echo "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1"; 
       // $nameArray_color=sql_select( "select distinct fabric_color_id from wo_booking_dtls   where  booking_no=$txt_booking_no and sensitivity=1"); 
		
		
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2  and process=35 and status_active=1 and is_deleted=0"); 
        $nameArray_size=sql_select( "select distinct  item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=2  and process=35 and status_active=1 and is_deleted=0");
		if(count($nameArray_size)>0)
		{
        ?>
        
        <table border="0" align="left" cellpadding="0" width="90%" cellspacing="0" >
            <tr>
                <td colspan="<? echo count($nameArray_size)+8; ?>" align="">
                <strong>As Per Garments Size </strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Service Type</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Item size</strong> </td>
                <?  				
                foreach($nameArray_size  as $result_size)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $result_size[csf('gmts_sizes')];?></strong></td>
                <?	}    ?>				
                <td style="border:1px solid black" align="center"><strong>Total</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
			$i++;
            $nameArray_item_description=sql_select( "select distinct description,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2 and process=".$result_item[csf('process')]." and status_active=1 and is_deleted=0 "); 
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i ; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $conversion_cost_head_array[$result_item[csf('process')]]; ?>
                </td>
                <? 
                $color_tatal=array();
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $item_desctiption_total=0;
                ?>

                <td style="border:1px solid black"><? echo $fabric_description_array[$result_itemdescription[csf('description')]]; ?> </td>
                <td style="border:1px solid black"><? //echo $result_itemdescription['brand_supplier']; ?> Booking Qnty  </td>
                <?
					foreach($nameArray_size  as $result_size)
					{
					$nameArray_size_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where   booking_no=$txt_booking_no and sensitivity=2 and process=". $result_item[csf('process')]." and description='". $result_itemdescription[csf('description')]."' and item_size='".$result_size[csf('gmts_sizes')]."' and rate='". $result_itemdescription[csf('rate')]."' and status_active=1 and is_deleted=0");  
	
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
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_as_per_gmts_color = $item_desctiption_total*$result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
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
                if($color_tatal[$result_size[gmts_sizes]] !='')
                {
                echo number_format($color_tatal[$result_size[gmts_sizes]],2);  
                }
                ?>
                </td>
            <?
            }
            ?>
                <td style="border:1px solid black;  text-align:right"><? echo number_format(array_sum($color_tatal),2);  ?></td>
                 <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_size)+7; ?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER SIZE  END=========================================  -->
        
         <!--==============================================AS PER CONTRAST COLOR START=========================================  -->
		<?
		//$nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2"); 
       // $nameArray_size=sql_select( "select distinct  item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=2");
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3  and process=35 and status_active=1 and is_deleted=0 "); 
        $nameArray_color=sql_select( "select distinct fabric_color_id as color_number_id from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=3  and process=35 and status_active=1 and is_deleted=0 "); 
		if(count($nameArray_color)>0)
		{
        ?>
        <table border="0" align="left" cellpadding="0" width="90%" cellspacing="0" >
            <tr>
                <td colspan="<? echo count($nameArray_color)+8; ?>" align="">
                <strong>Contrast Color</strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Service Type</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <?  				
                foreach($nameArray_color  as $result_color)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $color_library[$result_color[csf('color_number_id')]];?></strong></td>
                <?	}    ?>				
                <td style="border:1px solid black" align="center"><strong>Total</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
            $nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and process=".$result_item[csf('process')]." and status_active=1 and is_deleted=0 "); 
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $conversion_cost_head_array[$result_item[csf('process')]]; ?>
                </td>
                <? 
                $color_tatal=array();
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $item_desctiption_total=0;
                ?>
                <td style="border:1px solid black"><? echo $fabric_description_array[$result_itemdescription[csf('description')]]; ?> </td>
                <td style="border:1px solid black"><? //echo $result_itemdescription['brand_supplier']; ?> Booking Qnty  </td>
                <?
                foreach($nameArray_color  as $result_color)
                {
                $nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls    where   booking_no=$txt_booking_no and sensitivity=3 and process=". $result_item[csf('process')]." and description='". $result_itemdescription[csf('description')]."' and fabric_color_id=".$result_color[csf('color_number_id')]." and rate='". $result_itemdescription[csf('rate')]."' and status_active=1 and is_deleted=0 ");                          
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
                <td style="border:1px solid black; text-align:center"><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
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
                <td style="border:1px solid black;text-align:center"></td>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_color)+7; ?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER CONTRAST COLOR END=========================================  -->
        
        <!--==============================================AS PER GMTS Color & SIZE START=========================================  -->
		<?
		//$nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2"); 
       // $nameArray_size=sql_select( "select distinct  item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=2");
	   //$nameArray_color=sql_select( "select distinct fabric_color_id as color_number_id from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=3"); 

        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=4  and process=35 and status_active=1 and is_deleted=0"); 
        $nameArray_size=sql_select( "select distinct item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4  and process=35 and status_active=1 and is_deleted=0");
	    $nameArray_color=sql_select( "select distinct fabric_color_id as color_number_id from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4  and process=35 and status_active=1 and is_deleted=0"); 

		if(count($nameArray_size)>0)
		{
        ?>
        
        <table border="0" align="left" cellpadding="0" width="90%" cellspacing="0" >
            <tr>
                <td colspan="<? echo count($nameArray_size)+8; ?>" align="">
                <strong>Color & size sensitive </strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Service Type</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong></strong> </td>
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <?  				
                foreach($nameArray_size  as $result_size)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $result_size[csf('gmts_sizes')];?></strong></td>
                <?	}    ?>				
                <td style="border:1px solid black" align="center"><strong>Total</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
			$i++;
            $nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=4 and process=".$result_item[csf('process')]." and status_active=1 and is_deleted=0"); 
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo   (count($nameArray_item_description)*count($nameArray_color)); ?>">
                <? echo $i ; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo (count($nameArray_item_description)*count($nameArray_color)); ?>">
                <? echo $conversion_cost_head_array[$result_item[csf('process')]]; ?>
                </td>
                <? 
                $color_tatal=array();
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
					?>
                    <td style="border:1px solid black" rowspan="<? echo count($nameArray_color); ?>"><? echo $fabric_description_array[$result_itemdescription[csf('description')]]; ?> </td>
                    <td style="border:1px solid black" rowspan="<? echo count($nameArray_color); ?>"><? //echo $result_itemdescription['brand_supplier']; ?>Booking Qnty </td>
                    <?
                //$item_desctiption_total=0;
				foreach($nameArray_color as $result_color)
                {
					 $item_desctiption_total=0;
                ?>
                
                <td style="border:1px solid black"><? echo $color_library[$result_color[csf('color_number_id')]]; ?> </td>
                <?
                foreach($nameArray_size  as $result_size)
                {
                $nameArray_size_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4 and process=". $result_item[csf('process')]." and  description='". $result_itemdescription[csf('description')]."' and  item_size='".$result_size[csf('gmts_sizes')]."' and fabric_color_id=".$result_color[csf('color_number_id')]." and rate='". $result_itemdescription[csf('rate')]."' and status_active=1 and is_deleted=0");                          
                foreach($nameArray_size_size_qnty as $result_size_size_qnty)
                {
                ?>
                <td style="border:1px solid black; text-align:right">
                <? 
                if($result_size_size_qnty[csf('cons')]!= "")
                {
                echo number_format($result_size_size_qnty[csf('cons')],2);
                $item_desctiption_total += $result_size_size_qnty[csf('cons')] ;
                if (array_key_exists($result_size[csf('color_number_id')], $color_tatal))
                {
                $color_tatal[$result_size[csf('color_number_id')]]+=$result_size_size_qnty[csf('cons')];
                }
                else
                {
                $color_tatal[$result_size[csf('color_number_id')]]=$result_size_size_qnty[csf('cons')]; 
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
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
			}
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="5"><strong> Item Total</strong></td>
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
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_size)+8; ?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER Color & SIZE  END=========================================  -->
        
        
         <!--==============================================NO NENSITIBITY START=========================================  -->
		<?
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0  and process=35 and status_active=1 and is_deleted=0"); 
        //$nameArray_color=sql_select( "select distinct b.color_number_id from wo_trims_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.sensitivity=1"); 
		$nameArray_color= array();
		if(count($nameArray_item)>0)
		{
        ?>
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="90%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="7" align="">
                <strong>No Sensitivity</strong>
                </td>
            </tr>
            <tr>
                <td><strong>Sl</strong> </td>
                <td><strong>Service Type</strong> </td>
                <td><strong>Item Description</strong> </td>
                <td><strong></strong> </td>
                <td align="center"><strong> Qnty</strong></td>
                <td align="center"><strong>UOM</strong></td>
                <td align="center"><strong>Rate</strong></td>
                <td align="center"><strong>Amount</strong></td>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
				
            $nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 and process=".$result_item[csf('process')]." and status_active=1 and is_deleted=0 "); 
            ?>
            <tr>
                <td rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center"  rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $conversion_cost_head_array[$result_item[csf('process')]]; ?>
                </td>
                <? 
                $color_tatal=0;
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $item_desctiption_total=0;
                ?>
                <td style="border:1px solid black"><? echo $fabric_description_array[$result_itemdescription[csf('description')]]; ?> </td>
                <td style="border:1px solid black"><? //echo $result_itemdescription['brand_supplier']; ?>Booking Qnty  </td>
                <?
                $nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls where    booking_no=$txt_booking_no and sensitivity=0 and process=". $result_item[csf('process')]." and  description='". $result_itemdescription[csf('description')]."' and rate='". $result_itemdescription[csf('rate')]."' and status_active=1 and is_deleted=0");                          
                foreach($nameArray_color_size_qnty as $result_color_size_qnty)
                {
                ?>
                <td style="text-align:right">
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
                
                <td style="text-align:center "><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
                <td style="text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="text-align:right">
                <? 
                $amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="text-align:right" colspan="2"><strong> Item Total</strong></td>
                <td style="text-align:right">
                <?
                if($color_tatal !='')
                {
                echo number_format($color_tatal,2);  
                }
                ?>
                </td>
                <td></td>
                <td style="text-align:right"></td>
                <td style="text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right"  colspan="7"><strong>Total</strong></td>
                <td  style="text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <?
		//print_r($color_tatal);
		}
		?>
        <!--==============================================NO NENSITIBITY END=========================================  -->
        <?
        $mcurrency="";
	   $dcurrency="";
	   if($result[csf('currency_id')]==1)
	   {
		$mcurrency='Taka';
		$dcurrency='Paisa'; 
	   }
	   if($result[csf('currency_id')]==2)

	   {
		$mcurrency='USD';
		$dcurrency='CENTS'; 
	   }
	   if($result[csf('currency_id')]==3)
	   {
		$mcurrency='EURO';
		$dcurrency='CENTS'; 
	   }
		?>
       <br/>
       <table  width="90%" class="rpt_table"  border="1" cellpadding="0" cellspacing="0" rules="all">
       <tr>
                <th width="70%" style="text-align:right">Total Booking Amount</th><td width="30%" style="text-align:right"><? echo number_format($booking_grand_total,4);?></td>
            </tr>
            <tr>
                <th width="70%" style="text-align:right">Total Booking Amount (in word)</th><td width="30%"><? echo number_to_words(def_number_format($booking_grand_total,2,""),$mcurrency, $dcurrency);//echo number_to_words(number_format($booking_grand_total,2),"USD", "CENTS");?></td>
            </tr>
       </table>
          &nbsp;
        <table  width="90%" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
        <thead>
            <tr>
                <th width="3%">Sl</th><th width="97%">Spacial Instruction</th>
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
                $i++;
                ?>
                    <tr id="settr_1" align="" style="border:1px solid black;">
                        <td style="border:1px solid black;">
                        <? echo $i;?>
                        </td>
                        <td style="border:1px solid black;">
                        <? echo $row[csf('terms')]; ?>
                        </td>
                    </tr>
                <?
            }
        }
        else
        {
			$i=0;
        $data_array=sql_select("select id, terms from  lib_terms_condition where is_default=1");// quotation_id='$data'
        foreach( $data_array as $row )
            {
                $i++;
        ?>
        <tr id="settr_1" align="" style="border:1px solid black;">
                        <td style="border:1px solid black;">
                        <? echo $i;?>
                        </td>
                        <td style="border:1px solid black;">
                        <? echo $row[csf('terms')]; ?>
                        </td>
                        
                    </tr>
        <? 
            }
        } 
        ?>
    </tbody>
    </table>
    <br><br>
    <?
    if($show_comment==1)
	{}
	?>
         <br/>
		 <?
            echo signature_table(79, $cbo_company_name, "1113px");
			$style_sting=rtrim($style_sting,',');
			echo "****".custom_file_name($txt_booking_no,$style_sting,$all_job_arr);
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


if($action=="show_trim_booking_report1")
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$deling_marcent_arr=return_library_array("select id,team_member_name from lib_mkt_team_member_info","id","team_member_name");
	$season_arr=return_library_array("select id,season_name from lib_buyer_season where status_active=1","id","season_name");
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );

	?>
	<div style="width:1333px" align="center">       
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black;">
           <tr>
               <td width="100"> 
               <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="1250">                                     
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:20px;">
                              <?php      
                                    echo "<b>$company_library[$cbo_company_name]</b>";
                              ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">  
                            <?
                           // echo return_field_value("location_name", "lib_location", "company_id='".$cbo_company_name."'");
                           
                            $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name"); 
                            foreach ($nameArray as $result)
                            { 
                            ?>
                                            <? echo $result[csf('plot_no')]; ?> 
                                            <? echo $result[csf('level_no')]?>
                                            <? echo $result[csf('road_no')]; ?> 
                                            <? echo $result[csf('block_no')];?> 
                                           <? echo $result[csf('city')];?> 
                                            <? echo $result[csf('zip_code')]; ?> 
                                            <?php echo $result[csf('province')];?> 
                                            <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
                                            <? echo $result[csf('email')];?> 
                                            <? echo $result[csf('website')];

                            }
							?>
                               </td> 
                            </tr>
                            <tr>
                            <td align="center" style="font-size:20px">  
                            <strong>Service Booking For AOP</strong>
                             </td> 
                            </tr>
                      </table>
                </td>       
            </tr>
       </table>
		<?
		//================
		$booking_grand_total=0;
		$booking_grand_qty=0;
		$currency_id="";
		$buyer_string=array();
		$style_owner=array();
		$job_no=array();
		$job_no_in=array();
		$style_ref=array();
		$all_dealing_marcent=array();
		$season=array();
		$season_names='';
		$nameArray_buyer=sql_select( "select  a.style_ref_no, a.job_no, a.style_owner, a.buyer_name, a.dealing_marchant,a.season,a.season_matrix,a.season_buyer_wise   from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no=$txt_booking_no and b.status_active=1 and b.is_deleted=0"); 
        foreach ($nameArray_buyer as $result_buy){
			$buyer_string[$result_buy[csf('buyer_name')]]=$buyer_name_arr[$result_buy[csf('buyer_name')]];
			$style_owner[$result_buy[csf('job_no')]]=$company_library[$result_buy[csf('style_owner')]];
			$job_no[$result_buy[csf('job_no')]]=$result_buy[csf('job_no')];
			$job_no_in[$result_buy[csf('job_no')]]="'".$result_buy[csf('job_no')]."'";
			$style_ref[$result_buy[csf('job_no')]]=$result_buy[csf('style_ref_no')];
			$all_dealing_marcent[$result_buy[csf('job_no')]]=$deling_marcent_arr[$result_buy[csf('dealing_marchant')]];
			$season_matrix=$result_buy[csf('season_matrix')];
			$season_buyer=$result_buy[csf('season_buyer_wise')];
			if($season_matrix!=0 && $season_buyer==0)
			{
				$season_names.=$season_arr[$season_matrix].',';
			}
			else if($season_matrix==0 && $season_buyer!=0)
			{
				$season_names.=$season_arr[$season_buyer].',';
			}
			
			//$season[$result_buy[csf('job_no')]]=$season_arr[$result_buy[csf('season_matrix')]];
		}
		$season_names=rtrim($season_names,',');
		//echo $season_names.'dsd';
		$po_no=array();
		$file_no=array();
		$ref_no=array();
		$po_quantity=array();
		$nameArray_job=sql_select( "select b.id, b.po_number,b.grouping, b.file_no, b.po_quantity  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0"); 
        foreach ($nameArray_job as $result_job){
			$po_no[$result_job[csf('id')]]=$result_job[csf('po_number')];
			$file_no[$result_job[csf('id')]]=$result_job[csf('file_no')];
			$ref_no[$result_job[csf('id')]]=$result_job[csf('grouping')];
			$po_quantity[$result_job[csf('id')]]=$result_job[csf('po_quantity')];
		}
        $nameArray=sql_select( "select a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.source, a.is_short  from wo_booking_mst a where  a.booking_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0");
        foreach ($nameArray as $result){
        ?>
       <table width="100%" style="border:1px solid black;table-layout: fixed; margin-top:1px">                    	
            <tr>
                <td width="100" style="font-size:12px"><b>Booking No </b>   </td>
                <td width="110">:&nbsp;<? echo $result[csf('booking_no')];?> </td>
                <td width="100" style="font-size:12px"><b>Booking Date</b></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>
                <td width="110" align="center"><b>IMAGE</b></td>
                	
            </tr>
            <tr>
                <td width="100"><span style="font-size:12px"><b>Delivery Date</b></span></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>
                <td  width="100" style="font-size:12px"><b>Attention</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('attention')]; ?></td>	
                <td  width="110" rowspan="5" align="center">
				<? 
                $nameArray_imge =sql_select("SELECT image_location,real_file_name FROM common_photo_library where master_tble_id='".$result[csf('booking_no')]."' and file_type=1");
                ?>
            	<table width="310">
                <tr>
                <?
				$img_counter = 0;
                foreach($nameArray_imge as $result_imge)
				{	
				    if($path=="")
                    {
                    $path='../../';
                    }
							
					?>
					<td>
                        <img src="<? echo $path.$result_imge[csf('image_location')]; ?>" width="90" height="100" border="2" />
                       <? 
					   $img=explode('.',$result_imge[csf('real_file_name')]);
					   echo $img[0];
					   ?>
					</td>
					<?
					
					$img_counter++;
				}
				?>
                </tr>
           </table>   
                </td>	
            </tr>
            <tr>
                <td width="100" style="font-size:12px"><b>Currency</b></td>
                <td width="110">:&nbsp;<? $currency_id =$result[csf('currency_id')]; echo $currency[$result[csf('currency_id')]]; ?></td>
                <td  width="100" style="font-size:12px"><b>Conversion Rate</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('exchange_rate')]; ?></td>
                
            </tr> 
             <tr>
                <td  width="100" style="font-size:12px"><b>Source</b></td>
                <td  width="110" >:&nbsp;<? echo $source[$result[csf('source')]]; ?></td>
                <td width="100" style="font-size:12px"><b>Supplier Name</b>   </td>
                <td width="110">:&nbsp;<? echo $supplier_name_arr[$result[csf('supplier_id')]];?>    </td>
            </tr>  
             <tr>
                <td width="100" style="font-size:12px"><b>Supplier Address</b></td>
               	<td width="110">:&nbsp;<? echo $supplier_address_arr[$result[csf('supplier_id')]];?></td>
                <td  style="font-size:12px"><b>Style Ref.</b> </td>
                <td   style="font-size:12px">:&nbsp;<? echo $style_sting=implode(",",array_unique($style_ref)); ?>  </td>
            </tr>  
            <tr>
                <td width="100" style="font-size:12px"><b>Job No</b>   </td>
                <td width="110" >:&nbsp;
				<? 
				echo $job_no=implode(",",$job_no);
				?> 
                </td>
                <td width="100" style="font-size:12px"><b>Buyer Name</b>   </td>
                <td width="110" >:&nbsp;
				<? 
				echo implode(",",array_unique($buyer_string));
				?> 
                </td>
            </tr> 
             <tr>
                 <td>Dealing Merchant</td>
                <td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;">:&nbsp;
				<? 
					echo implode(",",array_unique($all_dealing_marcent));
				?> 
                </td>
               <td>Season</td>
                <td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;">:&nbsp;
				<? 
					echo implode(",",array_unique(explode(",",$season_names)));
				?> 
                </td>
                <td>Booking Type :  <? if($result[csf('is_short')]==2) echo "Main"; else echo "Short"; ?></td>
            </tr> 
            <tr>
                <td style="font-size:12px"><b>PO No</b> </td>
                
                 <td style="font-size:12px;overflow:hidden;text-overflow: ellipsis;white-space: nowrap;" colspan="4">:&nbsp;<b><? echo implode(",",array_unique($po_no));  ?></b></td>
            </tr> 
        </table> 
        <br/> 
		<?
        }
        ?>
          <!--==============================================AS PER GMTS COLOR START=========================================  -->
		<?
		//========================================
		$fabric_description_array=array();
		$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where job_no in(".implode(",",$job_no_in).")");
		foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id){
			if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]!=0){
				$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description,gsm_weight from  wo_pre_cost_fabric_cost_dtls  where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."'");
				list($fabric_description_row)=$fabric_description;
				$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")].', '.$fabric_description_row[csf("gsm_weight")];
			}
			/*if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]==0){
				$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description,gsm_weight from  wo_pre_cost_fabric_cost_dtls  where  job_no='".rtrim($job_no,", ")."'");
				foreach( $fabric_description as $fabric_description_row){
					$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]].=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")].', '.$fabric_description_row[csf("gsm_weight")];
				}
			}*/
		}
	//=================================================
        $nameArray_item=sql_select( "select distinct description from wo_booking_dtls  where booking_no=$txt_booking_no and wo_qnty !=0   and status_active=1 and is_deleted=0 and process=35 and status_active=1 and is_deleted=0 ");
        $nameArray_color=sql_select( "select distinct fabric_color_id,printing_color_id from wo_booking_dtls   where  booking_no=$txt_booking_no and wo_qnty !=0  and status_active=1 and is_deleted=0 and process=35 and status_active=1 and is_deleted=0 "); 
		
       if(count($nameArray_color)>0){
	   foreach($nameArray_item as $result_item){
        ?>
        
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="10" align="">
                <strong><? echo "Fabrication:".rtrim($fabric_description_array[$result_item[csf('description')]],", "); ?> </strong><br/>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>GMT Color</strong> </td>
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <td style="border:1px solid black"><strong>Print Color</strong> </td>
                <td style="border:1px solid black" align="center"><strong>Wo Qty (Kg)</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin Dia</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			 $total_amount_as_per_gmts_color=0;
			 $total_qty_as_per_gmts_color=0;
            $nameArray_item_description=sql_select( "select  fabric_color_id,gmts_color_id,printing_color_id,description,rate,dia_width,sum(wo_qnty) as cons from wo_booking_dtls  where booking_no=$txt_booking_no  and process=35 and description='".$result_item[csf('description')]."' and wo_qnty !=0 and status_active=1 and is_deleted=0  group by fabric_color_id,gmts_color_id,printing_color_id,description,rate,dia_width"); 
                foreach($nameArray_item_description as $result_itemdescription)
                {
               
                ?>
            <tr>
                <td style="border:1px solid black"><? echo $color_library[$result_itemdescription[csf('gmts_color_id')]]; ?>  </td>
                <td style="border:1px solid black"><? echo $color_library[$result_itemdescription[csf('fabric_color_id')]]; ?>  </td>
                 <td style="border:1px solid black"><? echo $color_library[$result_itemdescription[csf('printing_color_id')]]; ?>  </td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('cons')],2); ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('dia_width')]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],2); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? $wo_qnty_sum=$result_itemdescription[csf('cons')];
                $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
				echo number_format($amount_as_per_gmts_color,2);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                $total_qty_as_per_gmts_color+=$wo_qnty_sum;
                ?>
                </td>
            </tr>
				<?
                }
                ?>
            <tr>
            <td colspan="3" align="right"> <strong> Total Qty (kg) </strong></td>
            <td align="right"><? echo number_format($total_qty_as_per_gmts_color,2); ?> </td>

                <td style="border:1px solid black;  text-align:right" colspan="2"><strong>   Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,2);
                $booking_grand_total+=$total_amount_as_per_gmts_color;
                $booking_grand_qty+=$total_qty_as_per_gmts_color;
                ?>
                </td>
            </tr>
            
        </table>
        &nbsp;
        <br/>
        <?
		}
		}
 		?>
        <!--==============================================AS PER GMTS COLOR END=========================================  -->
       &nbsp;
       <?
       $mcurrency="";
	   $dcurrency="";
	   if($result[csf('currency_id')]==1)
	   {
		$mcurrency='Taka';
		$dcurrency='Paisa'; 
	   }
	   if($result[csf('currency_id')]==2)

	   {
		$mcurrency='USD';
		$dcurrency='CENTS'; 
	   }
	   if($result[csf('currency_id')]==3)
	   {
		$mcurrency='EURO';
		$dcurrency='CENTS'; 
	   }
		?>
       <table  width="100%" class="rpt_table" style="border:1px solid black;"   border="1" cellpadding="0" cellspacing="0" rules="all">
       <tr style="border:1px solid black;">
       
                <th width="70%" style="border:1px solid black; text-align:right">Total Wo Qty(Kg) &nbsp;</th><td width="30%" style="border:1px solid black; text-align:left"><? echo number_format($booking_grand_qty,2);?></td>
            </tr>

       <tr style="border:1px solid black;">

                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount</th><td width="30%" style="border:1px solid black; text-align:right"><? echo number_format($booking_grand_total,2);?></td>
            </tr>
            <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount (in word)</th><td width="30%" style="border:1px solid black;"><? echo number_to_words(def_number_format($booking_grand_total,2,""),$mcurrency, $dcurrency);?></td>
            </tr>
       </table>
          &nbsp;
        <?
		   echo get_spacial_instruction($txt_booking_no);
		?>
     <br/>
	 <?
        echo signature_table(79, $cbo_company_name, "1313px");
        echo "****".custom_file_name($txt_booking_no,$style_sting,$job_no);
     ?>
    </div>
<?
}





if($action=="show_trim_booking_report2")
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$deling_marcent_arr=return_library_array("select id,team_member_name from lib_mkt_team_member_info","id","team_member_name");
	$season_arr=return_library_array("select id,season_name from lib_buyer_season where status_active=1","id","season_name");
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	//$job_no_library=return_library_array( "select id,job_no from wo_booking_dtls", "id", "job_no"  );
	$ord_no_library=return_library_array( "select id,po_number from wo_po_break_down", "id", "po_number"  );
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	?>
	<div style="width:1333px" align="center">       
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black;">
           <tr>
               <td width="100"> 
               <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="1250">                                     
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:20px;">
                              <?php      
                                    echo $company_library[$cbo_company_name];
                              ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">  
                            <?
                           // echo return_field_value("location_name", "lib_location", "company_id='".$cbo_company_name."'");
                           
                            $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name"); 
                            foreach ($nameArray as $result)
                            { 
                            ?>
                                            <? echo $result[csf('plot_no')]; ?> 
                                            <? echo $result[csf('level_no')]?>
                                            <? echo $result[csf('road_no')]; ?> 
                                            <? echo $result[csf('block_no')];?> 
                                           <? echo $result[csf('city')];?> 
                                            <? echo $result[csf('zip_code')]; ?> 
                                            <?php echo $result[csf('province')];?> 
                                            <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
                                            <? echo $result[csf('email')];?> 
                                            <? echo $result[csf('website')];
                            }
							?>
                               </td> 
                            </tr>
                            <tr>
                            <td align="center" style="font-size:20px">  
                            <strong>Service Booking For AOP</strong>
                             </td> 
                            </tr>
                      </table>
                </td>       
            </tr>
       </table>
		<?
		/*$booking_grand_total=0;
		$job_no="";
		$style_ref_no="";
		$currency_id="";
		$nameArray_job=sql_select( "select distinct b.job_no,a.buyer_id,c.style_ref_no from wo_booking_mst a, wo_booking_dtls b,wo_po_details_master c where  c.job_no=b.job_no and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no"); 
		

	    $buyer_name=$nameArray_job[0][csf('buyer_id')];
        foreach ($nameArray_job as $result_job){
			$job_no.=$result_job[csf('job_no')].",";
			$style_ref_no.=$result_job[csf('style_ref_no')].",";
		}
		
		
		$po_no=""; $po_id='';
		$po_number_arr=array();
		$nameArray_job=sql_select( "select b.id, b.po_number from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no group by b.id, b.po_number"); 
		
        foreach ($nameArray_job as $result_job){
			$po_no.=$result_job[csf('po_number')].",";
			$po_id.=$result_job[csf('id')].",";
			$po_number_arr[$result_job[csf('id')]]=$result_job[csf('po_number')];
		}*/
		//================
		$booking_grand_total=0;
		$booking_grand_qty=0;
		$currency_id="";
		
		
		$buyer_string=array();
		$style_owner=array();
		$job_no=array();
		$job_no_in=array();
		$style_ref=array();
		$all_dealing_marcent=array();
		$season=array();
		$season_names='';
		$nameArray_buyer=sql_select( "select  a.style_ref_no, a.job_no, a.style_owner, a.buyer_name, a.dealing_marchant,a.season,a.season_matrix,a.season_buyer_wise   from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no=$txt_booking_no"); 
        foreach ($nameArray_buyer as $result_buy){
			$buyer_string[$result_buy[csf('buyer_name')]]=$buyer_name_arr[$result_buy[csf('buyer_name')]];
			$style_owner[$result_buy[csf('job_no')]]=$company_library[$result_buy[csf('style_owner')]];
			$job_no[$result_buy[csf('job_no')]]=$result_buy[csf('job_no')];
			$job_no_in[$result_buy[csf('job_no')]]="'".$result_buy[csf('job_no')]."'";
			$style_ref[$result_buy[csf('job_no')]]=$result_buy[csf('style_ref_no')];
			$all_dealing_marcent[$result_buy[csf('job_no')]]=$deling_marcent_arr[$result_buy[csf('dealing_marchant')]];
			//$season[$result_buy[csf('job_no')]]=$season_arr[$result_buy[csf('season_matrix')]];
			$season_matrix=$result_buy[csf('season_matrix')];
			$season_buyer=$result_buy[csf('season_buyer_wise')];
			if($season_matrix!=0 && $season_buyer==0)
			{
				$season_names.=$season_arr[$season_matrix].',';
			}
			else if($season_matrix==0 && $season_buyer!=0)
			{
				$season_names.=$season_arr[$season_buyer].',';
			}
		}
		 $season_names=rtrim($season_names,',');
		
		$po_no=array();
		$file_no=array();
		$ref_no=array();
		$po_quantity=array();
		
		$nameArray_job=sql_select( "select b.id, b.po_number,b.grouping, b.file_no, b.po_quantity  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no"); 
        foreach ($nameArray_job as $result_job){
			$po_no[$result_job[csf('id')]]=$result_job[csf('po_number')];
			$file_no[$result_job[csf('id')]]=$result_job[csf('file_no')];
			$ref_no[$result_job[csf('id')]]=$result_job[csf('grouping')];
			$po_quantity[$result_job[csf('id')]]=$result_job[csf('po_quantity')];
		}
		
		//===================
		
		
		
		
        $nameArray=sql_select( "select a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.source, a.is_short  from wo_booking_mst a where  a.booking_no=$txt_booking_no");
		
		
        foreach ($nameArray as $result){
        ?>
       <table width="100%" style="border:1px solid black;table-layout: fixed;">                    	
            <tr>
                <td width="100" style="font-size:16px"><b>Booking No </b>   </td>
                <td width="110" style="font-size:16px">:&nbsp;<b><? echo $result[csf('booking_no')];?></b> </td>
                <td width="100" style="font-size:12px"><b>Booking Date</b></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>
                <td width="110" align="center"><b>IMAGE</b></td>
                	
            </tr>
            <tr>
                <td width="100"><span style="font-size:12px"><b>Delivery Date</b></span></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>
                <td  width="100" style="font-size:12px"><b>Attention</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('attention')]; ?></td>	
                <td  width="110" rowspan="5" align="center">
                
                <? 
			$nameArray_imge =sql_select("SELECT image_location,real_file_name FROM common_photo_library where master_tble_id='".$result[csf('booking_no')]."' and file_type=1");
			?>
            
            	<table width="310">
                <tr>
                <?
				$img_counter = 0;
                foreach($nameArray_imge as $result_imge)
				{	
				    if($path=="")
                    {
                    $path='../../';
                    }
							
					?>
					<td>
						<!--<img src="../../<? //echo $result_imge[csf('image_location')]; ?>" width="90" height="100" border="2" />-->
                        <img src="<? echo $path.$result_imge[csf('image_location')]; ?>" width="90" height="100" border="2" />
                       <? 
					   $img=explode('.',$result_imge[csf('real_file_name')]);
					   echo $img[0];
					   ?>
					</td>
					<?
					
					$img_counter++;
				}
				?>
                </tr>
           </table>   
                </td>	
            </tr>
            <tr>
                <td width="100" style="font-size:12px"><b>Currency</b></td>
                <td width="110">:&nbsp;<? $currency_id =$result[csf('currency_id')]; echo $currency[$result[csf('currency_id')]]; ?></td>
                <td  width="100" style="font-size:12px"><b>Conversion Rate</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('exchange_rate')]; ?></td>
                
            </tr> 
             <tr>
                <td  width="100" style="font-size:12px"><b>Source</b></td>
                <td  width="110" >:&nbsp;<? echo $source[$result[csf('source')]]; ?></td>
                <td width="100" style="font-size:16px"><b>Supplier Name</b>   </td>
                <td width="110" style="font-size:16px">:&nbsp;<b><? echo $supplier_name_arr[$result[csf('supplier_id')]];?> </b>   </td>
            </tr>  
             <tr>
                <td width="100" style="font-size:12px"><b>Supplier Address</b></td>
               	<td width="110">:&nbsp;<? echo $supplier_address_arr[$result[csf('supplier_id')]];?></td>
                <td  style="font-size:12px"><b>Style Ref.</b> </td>
                <td   style="font-size:12px">:&nbsp;<? echo $style_sting=implode(",",array_unique($style_ref)); ?>  </td>
            </tr>  
            <tr>
                <td width="100" style="font-size:12px"><b>Job No</b>   </td>
                <td width="110" >:&nbsp;
				<? 
				echo $job_no=implode(",",$job_no);
				?> 
                </td>
                <td width="100" style="font-size:12px"><b>Buyer Name</b>   </td>
                <td width="110" >:&nbsp;
				<? 
				echo implode(",",array_unique($buyer_string));
				?> 
                </td>
            </tr> 
             <tr>
                 <td>Dealing Merchant</td>
                <td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;">:&nbsp;
				<? 
					echo implode(",",array_unique($all_dealing_marcent));
				?> 
                </td>
               <td>Season</td>
                <td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;">:&nbsp;
				<? 
					//echo implode(",",array_unique($season));
					  echo implode(",",array_unique(explode(",",$season_names)));
				?> 
                </td>
                <td>Booking Type :  <? if($result[csf('is_short')]==2) echo "Main"; else echo "Short"; ?></td>
            </tr> 
            <tr>
                <td style="font-size:12px"><b>PO No</b> </td>
                
                 <td style="font-size:12px;overflow:hidden;text-overflow: ellipsis;white-space: nowrap;" colspan="4">:&nbsp;<b><? echo implode(",",array_unique($po_no));  ?></b></td>
            </tr> 
        </table> 
        <br/> 
		<?
        }
        ?>
          <!--==============================================AS PER GMTS COLOR START=========================================  -->
		<?
		//========================================
		$fabric_description_array=array();
		$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where job_no in(".implode(",",$job_no_in).")");
		foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id){
			if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]!=0){
				$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description,gsm_weight from  wo_pre_cost_fabric_cost_dtls  where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."'");
				list($fabric_description_row)=$fabric_description;
				$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")].', '.$fabric_description_row[csf("gsm_weight")];
			}
			/*if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]==0){
				$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description,gsm_weight from  wo_pre_cost_fabric_cost_dtls  where  job_no='".rtrim($job_no,", ")."'");
				foreach( $fabric_description as $fabric_description_row){
					$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]].=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")].', '.$fabric_description_row[csf("gsm_weight")];
				}
			}*/
		}
	//print_r($fabric_description_array);
	//=================================================
        $nameArray_item=sql_select( "select distinct description from wo_booking_dtls  where booking_no=$txt_booking_no and wo_qnty !=0   and status_active=1 and is_deleted=0 and process=35 and status_active=1 and is_deleted=0 ");
        $nameArray_color=sql_select( "select distinct fabric_color_id from wo_booking_dtls   where  booking_no=$txt_booking_no and wo_qnty !=0  and status_active=1 and is_deleted=0 and process=35 and status_active=1 and is_deleted=0 "); 
		
       if(count($nameArray_color)>0){
	   foreach($nameArray_item as $result_item){
        ?>
        
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="9" align="">
                <strong><? echo "Fabrication:".rtrim($fabric_description_array[$result_item[csf('description')]],", "); ?> </strong><br/>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>GMT Color</strong> </td>
                <td style="border:1px solid black"><strong>Item Color</strong> </td> 
                <td style="border:1px solid black"><strong>Print Color</strong> </td>
                <td style="border:1px solid black"><strong>Order No</strong> </td>
                <td style="border:1px solid black"><strong>Job No</strong> </td>
                <td style="border:1px solid black" align="center"><strong>Wo Qty (Kg)</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin Dia</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			 $total_amount_as_per_gmts_color=0;
			 $total_qty_as_per_gmts_color=0;
            $nameArray_item_description=sql_select( "select  fabric_color_id,job_no,gmts_color_id,printing_color_id,po_break_down_id,description,rate,dia_width,sum(wo_qnty) as cons from wo_booking_dtls  where booking_no=$txt_booking_no  and process=35 and description='".$result_item[csf('description')]."' and wo_qnty !=0 and status_active=1 and is_deleted=0  group by fabric_color_id,gmts_color_id,printing_color_id,description,rate,dia_width,job_no,po_break_down_id");//and sensitivity=1 
                foreach($nameArray_item_description as $result_itemdescription)
                {
               
                ?>
            <tr>
                <td style="border:1px solid black"><? echo $color_library[$result_itemdescription[csf('gmts_color_id')]]; ?>  </td>
                <td style="border:1px solid black"><? echo $color_library[$result_itemdescription[csf('fabric_color_id')]]; ?>  </td>
                <td style="border:1px solid black"><? echo $color_library[$result_itemdescription[csf('printing_color_id')]]; ?>  </td>
                <td style="border:1px solid black"><? echo $ord_no_library[$result_itemdescription[csf('po_break_down_id')]]; ?>  </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('job_no')]; ?>  </td><!--job no -->
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('cons')],2); ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('dia_width')]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],2); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? $wo_qnty_sum=$result_itemdescription[csf('cons')];
                $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
				echo number_format($amount_as_per_gmts_color,2);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                $total_qty_as_per_gmts_color+=$wo_qnty_sum;
                ?>
                </td>
            </tr>
				<?
                }
                ?>
            <tr>
            <td colspan="5" align="right"> <strong> Total Qty (kg) </strong></td>
            <td align="right"><? echo number_format($total_qty_as_per_gmts_color,2); ?> </td>

                <td style="border:1px solid black;  text-align:right" colspan="2"><strong>   Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,2);
                $booking_grand_total+=$total_amount_as_per_gmts_color;
                $booking_grand_qty+=$total_qty_as_per_gmts_color;
                ?>
                </td>
            </tr>
            
        </table>
        &nbsp;
        <br/>
        <?
		}
		}
 		?>
        <!--==============================================AS PER GMTS COLOR END=========================================  -->
        <?
		
       $mcurrency="";
	   $dcurrency="";
	   if($result[csf('currency_id')]==1)
	   {
		$mcurrency='Taka';
		$dcurrency='Paisa'; 
	   }
	   if($result[csf('currency_id')]==2)

	   {
		$mcurrency='USD';
		$dcurrency='CENTS'; 
	   }
	   if($result[csf('currency_id')]==3)
	   {
		$mcurrency='EURO';
		$dcurrency='CENTS'; 
	   }
		?>
        <br/>
       <table  width="100%" class="rpt_table" style="border:1px solid black;"   border="1" cellpadding="0" cellspacing="0" rules="all">
       <tr style="border:1px solid black;">
       
                <th width="70%" style="border:1px solid black; text-align:right">Total Wo Qty(Kg) &nbsp;</th><td width="30%" style="border:1px solid black; text-align:left"><? echo number_format($booking_grand_qty,2);?></td>
            </tr>

       <tr style="border:1px solid black;">

                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount</th><td width="30%" style="border:1px solid black; text-align:right"><? echo number_format($booking_grand_total,2);?></td>
            </tr>
            <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount (in word)</th><td width="30%" style="border:1px solid black;"><? echo number_to_words(def_number_format($booking_grand_total,2,""),$mcurrency, $dcurrency);//number_to_words(number_format($booking_grand_total,2),$mcurrency, $dcurrency);?></td>
            </tr>
       </table>
         <br/>
        <?
		   echo get_spacial_instruction($txt_booking_no);
		?>
       <br><br>
        <?
    if($show_comment==1)
	{}
		  ?>
         <br/>
        
		 <?
            echo signature_table(79, $cbo_company_name, "1313px");
			echo "****".custom_file_name($txt_booking_no,$style_sting,$job_no);
         ?>
    </div>
<?
}



if($action=="save_update_delete_fabric_booking_terms_condition")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if ($operation==0) {
		$con = connect();
		if($db_type==0) {
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

if ($action=="populate_data_from_search_popup"){
	 $sql= "select booking_no,booking_date,company_id,buyer_id,job_no,po_break_down_id,item_category,fabric_source,currency_id,exchange_rate,pay_mode,booking_month,supplier_id,attention,delivery_date,source,booking_year,cbo_level,is_short from wo_booking_mst  where booking_no='$data' and status_active=1 and is_deleted=0"; 
	 $data_array=sql_select($sql);
	 foreach ($data_array as $row){
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";  
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";  
		echo "document.getElementById('txt_booking_no').value = '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('cbo_currency').value = '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value = '".$row[csf("exchange_rate")]."';\n";
		echo "document.getElementById('cbo_pay_mode').value = '".$row[csf("pay_mode")]."';\n";
		echo "document.getElementById('txt_booking_date').value = '".change_date_format($row[csf("booking_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('cbo_booking_month').value = '".$row[csf("booking_month")]."';\n";
		echo "document.getElementById('cbo_supplier_name').value = '".$row[csf("supplier_id")]."';\n";
		echo "document.getElementById('txt_attention').value = '".$row[csf("attention")]."';\n";
		echo "document.getElementById('txt_delivery_date').value = '".change_date_format($row[csf("delivery_date")],'dd-mm-yyyy','-')."';\n";
	    echo "document.getElementById('cbo_source').value = '".$row[csf("source")]."';\n";
		echo "document.getElementById('cbo_booking_year').value = '".$row[csf("booking_year")]."';\n";
		echo "document.getElementById('cbo_level').value = '".$row[csf("cbo_level")]."';\n";
		echo "document.getElementById('cbo_is_short').value = ".$row[csf("is_short")].";\n";
		echo " $('#cbo_company_name').attr('disabled',true);\n"; 
		echo " $('#cbo_supplier_name').attr('disabled',true);\n"; 
		echo " $('#cbo_level').attr('disabled',true);\n"; 
		echo " $('#cbo_buyer_name').attr('disabled',true);\n";
		echo " $('#cbo_is_short').attr('disabled',true);\n";
	 }
}
?>