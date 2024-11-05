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
*/
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');

include('../../../includes/class4/class.conditions.php');
include('../../../includes/class4/class.reports.php');
include('../../../includes/class4/class.fabrics.php');
include('../../../includes/class4/class.conversions.php');

//---------------------------------------------------- Start---------------------------------------------------------------------------
if($action=="check_conversion_rate"){
	$data=explode("**",$data);
	if($db_type==0){
		$conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
	}
	else{
		$conversion_date=change_date_format($data[1], "d-M-y", "-",1);
	}
	$currency_rate=set_conversion_rate( $data[0], $conversion_date, $data[2] );
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
	echo "$('#print_booking3').hide();\n";
	echo "$('#print_booking4').hide();\n";
	echo "$('#print_booking5').hide();\n";
	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			if($id==163){echo "$('#print_booking1').show();\n";}
			if($id==164){echo "$('#print_booking2').show();\n";}
			if($id==16){echo "$('#print_booking3').show();\n";}
			if($id==177){echo "$('#print_booking4').show();\n";}
			if($id==288){echo "$('#print_booking5').show();\n";}
		}
	}
	else
	{
		echo "$('#print_booking1').show();\n";
		echo "$('#print_booking2').show();\n";
		echo "$('#print_booking3').show();\n";
		echo "$('#print_booking4').show();\n";
		echo "$('#print_booking5').show();\n";
	}
	exit();
}

if ($action=="load_drop_down_supplier")
{
	$exdata=explode('_',$data);
	if($exdata[0]==5 || $exdata[0]==3)
	{
	   echo create_drop_down( "cbo_supplier_name", 130, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "--Select Company--", $selected, "get_php_form_data( this.value, 'load_drop_down_attention', 'requires/service_booking_aop_urmi_controller');",0,"" );
	}
	else
	{
	   echo create_drop_down( "cbo_supplier_name", 130, "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company=$exdata[1] and b.party_type=25 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name","id,supplier_name", 1, "--Select Supplier--",$selected,"get_php_form_data( this.value, 'load_drop_down_attention', 'requires/service_booking_aop_urmi_controller');",0 );
	}
	exit();
}

if ($action=="fabric_search_popup")
{
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
                    <td align="center" valign="middle" colspan="11"><? echo load_month_buttons(1); ?></td>
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

if ($action=="fabric_search_list_view")
{
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
	if ($buyer!=0) $buyer_cond=" and a.buyer_name='$buyer'"; else{ echo "Please Select Buyer First."; die; }
	if ($cbo_currency!="") $currency_cond=" and a.currency_id='$cbo_currency'"; else{ echo "Please Select Currency First."; die; }
	if($db_type==0){
		if ($date_from!="" &&  $date_to!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($date_from, "yyyy-mm-dd", "-")."' and '".change_date_format($date_to, "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}
	else if($db_type==2){
		if ($date_from!="" &&  $date_to!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($date_from, "yyyy-mm-dd", "-",1)."' and '".change_date_format($date_to, "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}
	if($db_type==0) $year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$cbo_job_year";
	if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_job_year";

	if ( str_replace("'","",$job)=="" && str_replace("'","",$order_search)=="" && str_replace("'","",$file_no)=="" && str_replace("'","",$internal_ref)=="" && str_replace("'","",$style)=="" && $date_from=="" &&  $date_to=="")
	{
		echo "Please Insert Job, Internal Ref, File No, Style Ref, Order No or Date Range First."; die;
	}
 	//&& str_replace("'","",$order_search)==""

	$job_cond=""; $order_cond=""; $style_cond="";
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
	
	$approval_allow = sql_select("select b.id, b.page_id, b.approval_need, b.allow_partial, b.validate_page,a.setup_date from approval_setup_mst a,approval_setup_dtls b 
	where a.id=b.mst_id and a.company_id='$company' and a.status_active=1 and b.page_id=25 and b.status_active=1 and b.is_deleted=0 order by b.id desc ");

	if ($approval_allow[0][csf("approval_need")] == 1 && $approval_allow[0][csf("allow_partial")] == 1)
		$approval_cond = "and g.approved in (1,3)";
	else if ($approval_allow[0][csf("approval_need")] == 1 && $approval_allow[0][csf("allow_partial")] == 2)
		$approval_cond = "and g.approved in (1)";
	else if ($approval_allow[0][csf("approval_need")] == 1 && $approval_allow[0][csf("allow_partial")] == 0)
		$approval_cond = "and g.approved in (1,3)";
	else $approval_cond = "";

	 $sql= 'select a.job_no AS "job_no", b.id AS "id", b.po_number AS "po_number", c.item_number_id AS "item_number_id", d.id AS "pre_cost_dtls_id", d.body_part_id AS "body_part_id", d.construction AS "construction", d.composition AS "composition", d.fab_nature_id AS "fab_nature_id", d.fabric_source AS "fabric_source", d.lib_yarn_count_deter_id AS "lib_yarn_count_deter_id", d.uom AS "uom", d.gsm_weight AS "gsm_weight", min(e.id) AS "eid", f.id AS "fid"   from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_fabric_cost_dtls d, wo_pre_cos_fab_co_avg_con_dtls e, wo_pre_cost_fab_conv_cost_dtls f, wo_pre_cost_mst g  where a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_description and a.job_no=g.job_no and  e.cons !=0 
	  and f.cons_process=35 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 '.$company_cond . $buyer_cond. $year_cond. $job_cond. $internal_ref_cond. $file_no_cond . $style_cond. $order_cond. $shipment_date. $approval_cond ." group by a.job_no, b.id, b.po_number, c.item_number_id, d.id, d.body_part_id, d.construction, d.composition, d.fab_nature_id, d.fabric_source, d.lib_yarn_count_deter_id, d.uom, d.gsm_weight, f.id";
	$sql_data=sql_select($sql);//. $currency_cond
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
     <table cellspacing="0" cellpadding="0" border="1" rules="all" width="980" class="rpt_table" id="list_view">
    <?
	$i=1;
	foreach($sql_data as $sql_row)
	{


	
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
            <td><? echo $unit_of_measurement[$sql_row['uom']]; ?></td>
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

if ($action=="populate_order_data_from_search_popup")
{
	$data_array=sql_select("select a.job_no,a.company_name,a.buyer_name from wo_po_details_master a, wo_po_break_down b where b.id in (".$data.") and a.job_no=b.job_no_mst");
	foreach ($data_array as $row){
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_name")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_name")]."';\n";
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n";
		echo "load_drop_down( 'requires/service_booking_aop_urmi_controller', '".$row[csf("job_no")]."', 'load_drop_down_fabric_description', 'fabric_description_td' )\n";
	}
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_fabric_description")
{
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
	exit();
}

if($action=="set_process"){
	 $process=return_field_value("cons_process", "wo_pre_cost_fab_conv_cost_dtls", "id=$data");
	 echo $process; die;
}

if($action=="generate_aop_booking")
{
	$color_library=return_library_array( "select id,color_name from lib_color where status_active =1 and is_deleted=0", "id", "color_name"  );
	$size_library=return_library_array( "select id,size_name from lib_size where status_active =1 and is_deleted=0", "id", "size_name"  );

	$data=explode("**",$data);
	$fabric_description_id=$data[0];
	$txt_order_no_id=$data[1];
	$txt_booking_no=$data[2];
	$cbo_level=$data[3];
	$conversion_cost_id=$data[4];
	$is_short=$data[5];
	$currency_id=$data[6];

	if($fabric_description_id==0){
		echo "<strong>Select Fabric</strong>";
		die;
	}
	$sql_vari_aop=sql_select("select b.fabric_source_aop_id from variable_order_tracking b, wo_booking_mst a  where b.company_name=a.company_id and a.booking_type=3 and a.process=35 and a.booking_no='$txt_booking_no' and b.variable_list=59 group by b.fabric_source_aop_id");
	$fabric_source_aop_id='';
	foreach( $sql_vari_aop as $row)
	{
		$fabric_source_aop_id=$row[csf("fabric_source_aop_id")];
	}
	if($fabric_source_aop_id==0 || $fabric_source_aop_id=='') $fabric_source_aop_id=0;else $fabric_source_aop_id=$fabric_source_aop_id;
	//echo $fabric_source_aop_id.'DDD';
	$fabric_description_array=array(); $str_job="";
   /* $wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id, job_no, fabric_description, cons_process from wo_pre_cost_fab_conv_cost_dtls where id in($conversion_cost_id)");
	foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_conv_id)
	{
		$str_job.=$row_conv_id[csf("job_no")].',';
		if($row_conv_id[csf("fabric_description")]!=0)
		{
			$fabric_description=sql_select("select id, body_part_id, color_type_id, fabric_description from  wo_pre_cost_fabric_cost_dtls where  id='".$row_conv_id[csf("fabric_description")]."'");
			list($fab_des_row)=$fabric_description;
			$fabric_description_array[$row_conv_id[csf("id")]]=$body_part[$fab_des_row[csf("body_part_id")]].', '.$color_type[$fab_des_row[csf("color_type_id")]].', '.$fab_des_row[csf("fabric_description")];
		}
	}*/

	$sql_fabric="select a.id, a.job_no, b.body_part_id, b.color_type_id, b.fabric_description from wo_pre_cost_fab_conv_cost_dtls a, wo_pre_cost_fabric_cost_dtls b where a.id in($conversion_cost_id) and b.id=a.fabric_description and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";

	$sql_fabric_res=sql_select($sql_fabric);
	foreach ($sql_fabric_res as $crow)
	{
		$str_job.="'".$crow[csf("job_no")]."'".',';
		$fabric_description_array[$crow[csf("id")]]=$body_part[$crow[csf("body_part_id")]].', '.$color_type[$crow[csf("color_type_id")]].', '.$crow[csf("fabric_description")];
	}
	unset($sql_fabric_res);

	$str_job=chop($str_job,',');
	$job_wise_currency_exrate_arr=array();
	$currency_exrate_sql="select a.job_no, a.exchange_rate, b.currency_id from wo_pre_cost_mst a, wo_po_details_master b where a.job_no=b.job_no and a.job_no in ($str_job) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$currency_exrate_sql_res=sql_select($currency_exrate_sql);
	foreach ($currency_exrate_sql_res as $cerow)
	{
		$job_wise_currency_exrate_arr[$cerow[csf("job_no")]]['exrate']=$cerow[csf("exchange_rate")];
		$job_wise_currency_exrate_arr[$cerow[csf("job_no")]]['currency']=$cerow[csf("currency_id")];
	}
	unset($currency_exrate_sql_res);

	$cu_booking_data_arr=array();
	$sql="select a.id, a.pre_cost_fabric_cost_dtls_id, a.artwork_no, a.po_break_down_id, a.color_size_table_id, a.fabric_color_id, a.item_size, a.process, sensitivity, a.job_no, booking_no, a.booking_type, a.description, a.uom, a.delivery_date, a.delivery_end_date, a.sensitivity, a.wo_qnty, a.rate, a.amount, a.gmts_size, a.gmts_color_id, a.dia_width from wo_booking_dtls a where a.booking_type=3 and a.process=35 and a.is_short=2  and a.pre_cost_fabric_cost_dtls_id in($data[4]) and a.status_active=1 and a.is_deleted=0 ";

	$dataArray=sql_select($sql);
	$z=1; $i=1;
	foreach($dataArray as $row)
	{
		$pre_cost_conversion_cost_dtls_id=$row[csf("pre_cost_fabric_cost_dtls_id")];
		$po_break_down_id=$row[csf("po_break_down_id")];
        $wo_qnty=$row[csf("wo_qnty")];
		$rate=$row[csf("rate")];
		$amount=$row[csf("amount")];
		$color_number_id=$row[csf("gmts_color_id")];
		$dia_width=$row[csf("dia_width")];
		$cu_booking_data_arr[$pre_cost_conversion_cost_dtls_id][$po_break_down_id][$color_number_id][$dia_width]['wo_qnty']+=$wo_qnty;
		$cu_booking_data_arr[$pre_cost_conversion_cost_dtls_id][$po_break_down_id][$color_number_id][$dia_width]['amount']+=$amount;
	}

	if($dtls_id=="") $dtls_id=0;

	$booking_data_arr=array();
	$sql="select a.id, a.pre_cost_fabric_cost_dtls_id, a.artwork_no, a.po_break_down_id, a.color_size_table_id, a.fabric_color_id, a.item_size, a.process, sensitivity, a.job_no, booking_no, a.booking_type, a.description, a.uom, a.delivery_date, a.delivery_end_date, a.sensitivity, a.wo_qnty, a.rate, a.amount, a.gmts_size, a.gmts_color_id, a.dia_width,a.aop_mc_type,a.aop_type from wo_booking_dtls a  where  a.booking_type=3 and a.process=35 and a.booking_no='$txt_booking_no' and a.id in ($dtls_id)  and a.pre_cost_fabric_cost_dtls_id in($data[4]) and  a.status_active=1 and a.is_deleted=0 ";
	$dataArray=sql_select($sql);
	$z=1; $i=1;
	foreach($dataArray as $row){
		$id=$row[csf("id")];
		$pre_cost_conversion_cost_dtls_id=$row[csf("pre_cost_fabric_cost_dtls_id")];
		$artwork_no=$row[csf("artwork_no")];
		$aop_mc_type=$row[csf("aop_mc_type")];
		$aop_type=$row[csf("aop_type")];
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
		$booking_data_arr[$pre_cost_conversion_cost_dtls_id][$po_break_down_id][$color_number_id][$dia_width]['aop_mc_type']=$aop_mc_type;
		$booking_data_arr[$pre_cost_conversion_cost_dtls_id][$po_break_down_id][$color_number_id][$dia_width]['aop_type']=$aop_type;
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
	//$conversion= new conversion($condition);
	$fabric= new fabric($condition);
	$req_qty_arr=$fabric->getQtyArray_by_OrderFabriccostidGmtscolorAndDiaWidth_knitAndwoven_greyAndfinish();
	$req_amount_arr=$fabric->getAmountArray_by_OrderFabriccostidGmtscolorAndDiaWidth_knitAndwoven_greyAndfinish();
	//$conversion= new conversion($condition);
	//echo $conversion->getQuery();
	//$req_qty_arr=$conversion->getQtyArray_by_ConversionidOrderColorDiaWidthAndUom();
	//$req_amount_arr=$conversion->getAmountArray_by_ConversionidOrderColorDiaWidthAndUom();
	// echo "<pre>";
	// print_r($req_amount_arr)."<br>";


    $sql="select a.job_no, b.id as po_break_down_id, b.po_number, min(c.id)as color_size_table_id, c.color_number_id, sum(c.plan_cut_qnty) as plan_cut_qnty, d.costing_per, e.id, e.fabric_description, e.cons_process, e.charge_unit, e.amount,(a.job_quantity*e.avg_req_qnty)*e.charge_unit as amt, e.color_break_down, e.process_loss, f.id as fid,f.color_break_down as color_breakdown, f.body_part_id, f.color_type_id, f.construction, f.composition, f.gsm_weight, f.costing_per, f.uom, f.fab_nature_id, g.dia_width,

	CASE f.costing_per
	WHEN 1 THEN round((AVG(g.requirment)/12)*sum(c.plan_cut_qnty),4)
	WHEN 2 THEN round((AVG(g.requirment)/1)*sum(c.plan_cut_qnty),4)
	WHEN 3 THEN round((AVG(g.requirment)/24)*sum(c.plan_cut_qnty),4)
	WHEN 4 THEN round((AVG(g.requirment)/36)*sum(c.plan_cut_qnty),4)
	WHEN 5 THEN round((AVG(g.requirment)/48)*sum(c.plan_cut_qnty),4)
	ELSE 0 END as wo_req_qnty,f.fabric_source,a.job_quantity,e.avg_req_qnty

	from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_mst d, wo_pre_cost_fab_conv_cost_dtls e, wo_pre_cost_fabric_cost_dtls f, wo_pre_cos_fab_co_avg_con_dtls g

	where
	  a.id=b.job_id and 
   a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id and a.id=g.job_id and b.id=c.po_break_down_id and b.id=g.po_break_down_id and c.color_number_id=g.color_number_id and c.size_number_id=g.gmts_sizes and c.item_number_id=f.item_number_id and f.id=g.pre_cost_fabric_cost_dtls_id and e.fabric_description=f.id and e.id in($conversion_cost_id) and b.id in($txt_order_no_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0  and g.requirment >0

	group by a.job_no, b.id, b.po_number, c.color_number_id, d.costing_per, e.id, e.fabric_description, e.cons_process, e.charge_unit, e.amount, e.color_break_down, e.process_loss, f.id, f.body_part_id,f.color_break_down, f.color_type_id, f.construction, f.composition, f.gsm_weight, f.costing_per, f.uom, f.fab_nature_id, g.dia_width,f.fabric_source,a.job_quantity,e.avg_req_qnty
	order by b.id ASC";

	//echo $sql; //die;

	$dataArray=sql_select($sql);
	foreach($dataArray as $row)
	{
		$job_no                      = $row[csf("job_no")];
		$po_number                   = $row[csf("po_number")];
		$po_break_down_id            = $row[csf("po_break_down_id")];
		$pre_cost_conversion_cost_id = $row[csf("id")];
		$conv_cost_id = $row[csf("id")];
		$body_part_id                = $row[csf("body_part_id")];
		$color_type_id               = $row[csf("color_type_id")];
		$construction                = $row[csf("construction")];
		$composition                 = $row[csf("composition")];
		$color_breakdown                 = $row[csf("color_breakdown")];
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
		$fabric_source               = $row[csf("fabric_source")];
		$amt                         = $row[csf("amt")];

		$pre_cost_fabric_cost_dtls_id= $row[csf("fid")];
		$cbo_fabric_natu             = $row[csf("fab_nature_id")];
		$budget_rate=0;
		
		//$req_qty = $req_qty_arr[$conv_cost_id][$po_break_down_id][$color_number_id][$dia_width][$uom];
		//$req_amt = $req_amount_arr[$conv_cost_id][$po_break_down_id][$color_number_id][$dia_width][$uom];
		if($cbo_fabric_natu==2){
			if($fabric_source_aop_id==1 || $fabric_source_aop_id==0) //Grey
			{
				$req_qty = $req_qty_arr['knit']['grey'][$po_break_down_id][$pre_cost_fabric_cost_dtls_id][$color_number_id][$dia_width][$uom];
				$req_amt = $req_amount_arr['knit']['grey'][$po_break_down_id][$pre_cost_fabric_cost_dtls_id][$color_number_id][$dia_width][$uom];
			}
			else
			{
				$req_qty = $req_qty_arr['knit']['finish'][$po_break_down_id][$pre_cost_fabric_cost_dtls_id][$color_number_id][$dia_width][$uom];
				$req_amt = $req_amount_arr['knit']['finish'][$po_break_down_id][$pre_cost_fabric_cost_dtls_id][$color_number_id][$dia_width][$uom];
			}
		}
		if($cbo_fabric_natu==3){
			if($fabric_source_aop_id==1 || $fabric_source_aop_id==0) //Grey
			{
				$req_qty = $req_qty_arr['woven']['grey'][$po_break_down_id][$pre_cost_fabric_cost_dtls_id][$color_number_id][$dia_width][$uom];
				$req_amt = $req_amount_arr['woven']['grey'][$po_break_down_id][$pre_cost_fabric_cost_dtls_id][$color_number_id][$dia_width][$uom];
			}
			else
			{
				$req_qty = $req_qty_arr['woven']['finish'][$po_break_down_id][$pre_cost_fabric_cost_dtls_id][$color_number_id][$dia_width][$uom];
				$req_amt = $req_amount_arr['woven']['finish'][$po_break_down_id][$pre_cost_fabric_cost_dtls_id][$color_number_id][$dia_width][$uom];
			}
		}
		$req_avg_rate=$req_amt/$req_qty;
		if($job_wise_currency_exrate_arr[$job_no]['currency']!=$currency_id)
		{
			if($currency_id==1) $budget_rate=$charge_unit*$job_wise_currency_exrate_arr[$job_no]['exrate'];
			else $budget_rate=$charge_unit;
			 //echo $charge_unit.'='.$job_wise_currency_exrate_arr[$job_no]['exrate'].',';
		} 
		else $budget_rate=$req_avg_rate;
		
	//	$req_amt=$req_qty*($req_avg_rate*$budget_rate);
		if($fabric_source==1){
			$req_amt=$amt;
		}else{
			$req_amt=$req_qty*$budget_rate;
		}
		
		
	//	 echo $req_qty.'='.$req_amt.'<br>';

		$cu_wo_qnty        = $cu_booking_data_arr[$pre_cost_conversion_cost_id][$po_break_down_id][$color_number_id][$dia_width]['wo_qnty'];
		$cu_wo_amount      = $cu_booking_data_arr[$pre_cost_conversion_cost_id][$po_break_down_id][$color_number_id][$dia_width]['amount'];
		$woqnty      = 0;
		if($body_part_id == 3)
		{
			$woqnty  = $plan_cut_qnty*2;
			$uom_item     = "1,2";
			$selected_uom = $uom;
		}
		else if($body_part_id==2)
		{
			// $woqnty  = $plan_cut_qnty*1;
			$woqnty  = $req_qty*1;
			$uom_item     = "1,2,12,23,27";
			$selected_uom = $uom;
		}
		else if($body_part_id != 2 || $body_part_id != 3 )
		{
			//$process_loss_qty = $wo_req_qnty * $process_loss / 100;
			//$woqnty      = $wo_req_qnty - $process_loss_qty;

			$process_loss_qty = $req_qty * $process_loss / 100;
			//$woqnty      = $req_qty - $process_loss_qty;
			$selected_uom     = $uom;
			$woqnty      = $req_qty;
			//echo $woqnty.'=='.$process_loss_qty.'X';
		}

		if($body_part_id==2 || $body_part_id==3)
		{
			$rate   = 0; $amount = 0; $bamount=0;
		}
		else{
			$rate   = $budget_rate;
			$amount = $rate*$woqnty;


		}
		if($fabric_source==1){
			$budget_amt=$charge_unit*$woqnty;
		}else{
			$budget_amt=$budget_rate*$woqnty;
		}
	
		// echo $woqnty.'=='.$body_part_id.'A';
		$blaqnty = $woqnty - $cu_wo_qnty;

		$blaamount = $amount - $cu_wo_amount;
		$rate    = $rate;
		$amount  = $amount;
		$uom     = $selected_uom;

		$dtls_id=$booking_data_arr[$pre_cost_conversion_cost_id][$po_break_down_id][$color_number_id][$dia_width]['id'];
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['id'][$po_break_down_id]              = $dtls_id ;

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
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['req_qnty'][$po_break_down_id]            = $req_qty;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['amount'][$po_break_down_id]            = $req_amt;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['cu_wo_qnty'][$po_break_down_id]          = $cu_wo_qnty;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['cu_wo_amt'][$po_break_down_id]           = $cu_wo_amount;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['blaqnty'][$po_break_down_id]             = $blaqnty;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['blaamount'][$po_break_down_id]           = $blaamount;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['uom'][$po_break_down_id]                 = $uom;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['rate'][$po_break_down_id]                = $rate;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['charge_unit'][$po_break_down_id]                = $charge_unit;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['budget_amt'][$po_break_down_id]          = $budget_amt;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['color_breakdown'][$po_break_down_id]     = $color_breakdown;
	}
	?>
    <div id="content_search_panel_<? echo $pre_cost_conversion_cost_id; ?>" style="" class="accord_close">
        <table class="rpt_table" border="1" width="1680" cellpadding="0" cellspacing="0" rules="all" id="tbl_table" style="table-layout: fixed;">
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
				<th>AOP M/C Type</th>
				<th>AOP Type</th>
                <th>Delivery Start Date</th>
                <th>Delivery End Date</th>
                <th>Bla. Qnty</th>
                <th>WO. Qnty</th>
                <th>UOM</th>
                <th>Rate</th>
                <th>Amount</th>
                <th>Plan Cut Qnty</th>
                <th>Add Image</th>
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
								$budget_amt                  = def_number_format($dia_width_val['budget_amt'][$po_id],1,"");
								$wo_cu_wo_qnty               = def_number_format($dia_width_val['cu_wo_qnty'][$po_id],1,"");
								$blaamount               	 = def_number_format($dia_width_val['blaamount'][$po_id],1,"");

								if($is_short==2)
								{
									if($blaqnty>0)
									{
										?>
										<tr align="center">
											<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"><? echo $job_no; ?>
												<input type="hidden" name="job_no_<? echo $i; ?>" id="job_no_<? echo  $i; ?>" value="<? echo $job_no; ?>" style="width:60px;" class="text_boxes" disabled="disabled"></td>
											<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"><? echo $po_number; ?>
												<input type="hidden" name="po_id_<? echo $i; ?>" id="po_id_<? echo  $i; ?>" value="<? echo $po_break_down_id; ?>" style="width:60px;" class="text_boxes" disabled="disabled"></td>
											<td><? echo $body_part[$body_part_id];?>
												<input type="hidden" name="fabric_description_id_<? echo $i; ?>" id="fabric_description_id_<? echo $i; ?>" value="<? echo $pre_cost_conversion_cost_id; ?>" style="width:60px;" class="text_boxes" disabled="disabled"></td>
											<td><? echo $color_type[$color_type_id];?></td>
											<td><? echo $construction;?></td>
											<td><? echo $composition;?></td>
											<td><? echo $gsm_weight;?></td>
											<td><? echo $dia_width;?>
												<input type="hidden" name="dia_<? echo $i; ?>" id="dia_<? echo $i; ?>" value="<? echo $dia_width; ?>" class="text_boxes" style="width:60px;" /></td>
											<td><?  echo $color_library[$color_number_id] ?>
                                                <input type="hidden" name="gmts_color_id_<? echo $i; ?>" id="gmts_color_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<?  echo $color_number_id;?>"disabled="disabled"/>
                                                <input type="hidden" name="color_size_table_id_<? echo $i; ?>" id="color_size_table_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<?  echo $color_size_table_id;?>" disabled="disabled"/>
											</td>
											<td>
                                                <input type="text" name="item_color_<? echo $i; ?>" id="item_color_<? echo $i; ?>" style="width:60px;" class="text_boxes" onChange="copy_value()" value="<?  echo $color_library[$item_color_id] ?>"/>
                                                <input type="hidden" name="item_color_id_<? echo $i; ?>" id="item_color_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<? echo $item_color_id;?>" disabled="disabled"/>
											</td>
											<td><input type="text" name="findia_<? echo $i; ?>" id="findia_<? echo $i; ?>" value="<? echo $dia_width; ?>" class="text_boxes" style="width:60px;" /></td>
											<td>
												<input type="text" name="printing_color_<? echo $i; ?>" id="printing_color_<? echo $i; ?>" style="width:60px;" class="text_boxes" onChange="copy_value()" value="<?  echo $color_library[$item_color_id] ?>"/>
												<input type="hidden" name="printing_color_id_<? echo $i; ?>" id="printing_color_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<? echo $item_color_id;?>" disabled="disabled"/>
											</td>
											<td><input type="text" name="artworkno_<? echo $i; ?>" id="artworkno_<? echo $i; ?>" value="<? //echo $artwork_no; ?>" style="width:60px;" class="text_boxes"></td>
											<td><? echo create_drop_down("aop_mc_type_".$i, 60, $aop_mc_typeArr,"", 1, "--Select--","","",0); ?></td>
											<td><? echo create_drop_down("aop_type_".$i, 60, $print_type,"", 1, "--Select--","","",0); ?></td>
											<td><input type="text" name="startdate_<? echo $i; ?>" id="startdate_<? echo $i; ?>" value="<? //echo $delivery_date; ?>" style="width:60px;" class="datepicker"></td>
											<td><input type="text" name="enddate_<? echo $i; ?>" id="enddate_<? echo $i; ?>" value="<?  //echo $delivery_end_date; ?>" style="width:60px;" class="datepicker"></td>
											<td>
                                                <input type="text" name="txt_blanty_<? echo $i; ?>" id="txt_blanty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $i; ?>,'txt_woqnty');calculate_amount(<? echo $i; ?>)" value="<? echo $blaqnty; ?>"/>
                                                <input type="hidden" name="txtreqnty_<? echo $i; ?>" id="txtreqnty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? echo $req_qnty; ?>"/>   			    <input type="hidden" name="txt_prev_woqnty_<? echo $i; ?>" id="txt_prev_woqnty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? if($is_shrot==2) echo $wo_cu_wo_qnty; else echo ""; ?>"/>
											</td>
											<td><input type="text" name="txt_woqnty_<? echo $i; ?>" id="txt_woqnty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $i; ?>,'txt_woqnty');calculate_amount(<? echo $i; ?>)" value="<? echo $blaqnty; ?>"/></td>
											<td><? echo create_drop_down("uom_".$i, 50, $unit_of_measurement,"", 1, "--Select--",$uom,"copy_value(".$i.",'uom')",1,"$uom_item"); ?></td>
											<td><input type="text" name="txt_rate_<? echo $i; ?>" id="txt_rate_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $i; ?>,'txt_rate'); calculate_amount(<? echo $i; ?>)" value="<? echo $rate; ?>"></td>
											<td><input type="text" name="txt_amount_<? echo $i; ?>" reqamount="<? echo $blaamount; ?>" id="txt_amount_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? echo $amount; ?>" examt="<? echo $budget_amt; ?>" disabled="disabled"/></td>
											<td>
                                                <input type="text" name="txt_paln_cut_<? echo $i; ?>" id="txt_paln_cut_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? echo  $plan_cut_qnty; ?>" disabled>
                                                <input type="hidden" name="updateid_<? echo $i; ?>" id="updateid_<? echo $i; ?>" value="<? echo $id; ?>">
											</td>
                                            <td>
                                        	<input type="button" class="image_uploader" id="uploader" style="width:60px" value="ADD Image" onClick="fnc_file_upload(<? echo $i;?>);">                                    	 </td>
										</tr>
									<?
									$i++;
									}
								}
								else
								{
									?>
									<tr align="center">
										<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"><? echo $job_no; ?>
											<input type="hidden" name="job_no_<? echo $i; ?>" id="job_no_<? echo  $i; ?>" value="<? echo $job_no; ?>" style="width:60px;" class="text_boxes" disabled="disabled"></td>
										<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"><? echo $po_number; ?>
											<input type="hidden" name="po_id_<? echo $i; ?>" id="po_id_<? echo  $i; ?>" value="<? echo $po_break_down_id; ?>" style="width:60px;" class="text_boxes" disabled="disabled"></td>
										<td><? echo $body_part[$body_part_id];?>
											<input type="hidden" name="fabric_description_id_<? echo $i; ?>" id="fabric_description_id_<? echo $i; ?>" value="<? echo $pre_cost_conversion_cost_id; ?>" style="width:60px;" class="text_boxes" disabled="disabled"></td>
										<td><? echo $color_type[$color_type_id];?></td>
										<td><? echo $construction;?></td>
										<td><? echo $composition;?></td>
										<td><? echo $gsm_weight;?></td>
										<td><? echo $dia_width;?>
											<input type="hidden" name="dia_<? echo $i; ?>" id="dia_<? echo $i; ?>" value="<? echo $dia_width; ?>" class="text_boxes" style="width:60px;" /></td>

										<td><? echo $color_library[$color_number_id] ?>
                                            <input type="hidden" name="gmts_color_id_<? echo $i; ?>" id="gmts_color_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<?  echo $color_number_id;?>"disabled="disabled"/>
                                            <input type="hidden" name="color_size_table_id_<? echo $i; ?>" id="color_size_table_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<?  echo $color_size_table_id;?>" disabled="disabled"/>
										</td>
										<td>
                                            <input type="text" name="item_color_<? echo $i; ?>" id="item_color_<? echo $i; ?>" style="width:60px;" class="text_boxes" onChange="copy_value()" value="<?  echo $color_library[$item_color_id] ?>"/>
                                            <input type="hidden" name="item_color_id_<? echo $i; ?>" id="item_color_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<? echo $item_color_id;?>" disabled="disabled"/>
										</td>
										<td><input type="text" name="findia_<? echo $i; ?>" id="findia_<? echo $i; ?>" value="<? echo $dia_width; ?>" class="text_boxes" style="width:60px;" /></td>
										<td>
											<input type="text" name="printing_color_<? echo $i; ?>" id="printing_color_<? echo $i; ?>" style="width:60px;" class="text_boxes" onChange="copy_value()" value="<?  echo $color_library[$item_color_id] ?>"/>
											<input type="hidden" name="printing_color_id_<? echo $i; ?>" id="printing_color_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<? echo $item_color_id;?>" disabled="disabled"/>
										</td>
										<td><input type="text" name="artworkno_<? echo $i; ?>" id="artworkno_<? echo $i; ?>" value="<? //echo $artwork_no; ?>" style="width:60px;" class="text_boxes"></td>
										<td><? echo create_drop_down("aop_mc_type_".$i, 60, $aop_mc_typeArr,"", 1, "--Select--","","",0); ?></td>
										<td><? echo create_drop_down("aop_type_".$i, 60, $print_type,"", 1, "--Select--","","",0); ?></td>
										<td><input type="text" name="startdate_<? echo $i; ?>" id="startdate_<? echo $i; ?>" value="<? //echo $delivery_date; ?>" style="width:60px;" class="datepicker"></td>
										<td><input type="text" name="enddate_<? echo $i; ?>" id="enddate_<? echo $i; ?>" value="<?  //echo $delivery_end_date; ?>" style="width:60px;" class="datepicker"></td>
										<td>
                                            <input type="text" name="txt_blanty_<? echo $i; ?>" id="txt_blanty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $i; ?>,'txt_woqnty');calculate_amount(<? echo $i; ?>)" value=""/>
                                           <input type="hidden" name="txtreqnty_<? echo $i; ?>" id="txtreqnty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric"  value=""/>
                                           <input type="hidden" name="txt_prev_woqnty_<? echo $i; ?>" id="txt_prev_woqnty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? //if($is_shrot==2) echo $wo_cu_wo_qnty; else echo ""; ?>"/>
										</td>
										<td><input type="text" name="txt_woqnty_<? echo $i; ?>" id="txt_woqnty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $i; ?>,'txt_woqnty');calculate_amount(<? echo $i; ?>)" value=""/></td>
										<td><? echo create_drop_down("uom_".$i, 50, $unit_of_measurement,"", 1, "--Select--",$uom,"copy_value(".$i.",'uom')",1,"$uom_item"); ?></td>
										<td><input type="text" name="txt_rate_<? echo $i; ?>" id="txt_rate_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $i; ?>,'txt_rate');calculate_amount(<? echo $i; ?>)" value="<? echo $rate; ?>"></td>
										<td><input type="text" name="txt_amount_<? echo $i; ?>" id="txt_amount_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? //echo $amount; ?>" disabled="disabled"/></td>
										<td>
                                            <input /**/type="text" name="txt_paln_cut_<? echo $i; ?>" id="txt_paln_cut_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? echo  $plan_cut_qnty; ?>" disabled>
                                            <input type="hidden" name="updateid_<? echo $i; ?>" id="updateid_<? echo $i; ?>" value="<? echo $id; ?>">
										</td>
                                         <td>
                                        	<input type="button" class="image_uploader" id="uploader" style="width:60px" value="ADD Image" onClick="fnc_file_upload(<? echo $i;?>);">                                    </td>
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
							$color_breakdown            = implode("__",array_unique($dia_width_val['color_breakdown']));
						
							
                            $uom                         = implode(",",array_unique($dia_width_val['uom']));
							$charge_unit                 = implode(",",array_unique($dia_width_val['charge_unit']));

							
					
								foreach(array_unique($dia_width_val['color_breakdown']) as $val){
									$colordata=explode("__",$val);
								}

								foreach($colordata as $vals){
									$cdata=explode("_",$vals);
									$colorbreakdown[$cdata[0]]=$cdata[2];

								}
							



                            $plan_cut_qnty               = array_sum($dia_width_val['plan_cut_qnty']);
                            $req_qnty                    = def_number_format(array_sum($dia_width_val['req_qnty']),1,"");
                            $blaqnty                     = def_number_format(array_sum($dia_width_val['blaqnty']),1,"");
                          //  $rate                        = def_number_format(array_sum($dia_width_val['rate']),1,"");

                            $amount                      = def_number_format(array_sum($dia_width_val['amount']),1,"");
							
							$budget_amt                  = def_number_format(array_sum($dia_width_val['budget_amt']),1,"");
                            $wo_cu_wo_qnty               = def_number_format(array_sum($dia_width_val['cu_wo_qnty']),1,"");
							$wo_cu_wo_amt              = def_number_format(array_sum($dia_width_val['cu_wo_amt']),1,"");

							$blaamount               	 = def_number_format(array_sum($dia_width_val['blaamount']),1,"");
							$rate                        = def_number_format(array_sum($dia_width_val['amount'])/array_sum($dia_width_val['req_qnty']),5,"");
                            //$rate                        = def_number_format($amount/$req_qnty,1,"");
							$namt=$blaqnty*$charge_unit;
							// $namt=$blaqnty*$rate;
                            if($is_short==2)
                            {
                              //  echo $amount.'='.$req_qnty.'=='.$rate.'<br> ';
                                if($blaqnty>0)
                                {
                                    $row_check++;
                                    ?>
                                    <tr align="center">
                                        <td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"><? echo $job_no; ?>
                                        	<input type="hidden" name="job_no_<? echo $i; ?>" id="job_no_<? echo  $i; ?>" value="<? echo $job_no; ?>" style="width:60px;" class="text_boxes" disabled="disabled"></td>
                                        <td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;">
                                            <a href="#" onClick="setdata('<? echo $po_number;?>' )">View</a>
                                            <? //echo $po_number; ?>
                                            <input type="hidden" name="po_id_<? echo $i; ?>" id="po_id_<? echo  $i; ?>" value="<? echo $po_break_down_id; ?>" style="width:60px;" class="text_boxes" disabled="disabled">
                                        </td>
                                        <td>
											<? echo $body_part[$body_part_id];?>
                                            <input type="hidden" name="fabric_description_id_<? echo $i; ?>" id="fabric_description_id_<? echo $i; ?>" value="<? echo $pre_cost_conversion_cost_id; ?>" style="width:60px;" class="text_boxes" disabled="disabled">
                                        </td>
                                        <td><? echo $color_type[$color_type_id];?></td>
                                        <td><? echo $construction;?></td>
                                        <td><? echo $composition;?></td>
                                        <td><? echo $gsm_weight;?></td>
                                        <td><? echo $dia_width;?>
                                        	<input type="hidden" name="dia_<? echo $i; ?>" id="dia_<? echo $i; ?>" value="<? echo $dia_width; ?>" class="text_boxes" style="width:60px;" /></td>
                                        <td><?  echo $color_library[$color_number_id] ?>
                                            <input type="hidden" name="gmts_color_id_<? echo $i; ?>" id="gmts_color_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<?  echo $color_number_id;?>"disabled="disabled"/>
                                            <input type="hidden" name="color_size_table_id_<? echo $i; ?>" id="color_size_table_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<?  echo $color_size_table_id;?>" disabled="disabled"/>
                                        </td>
                                        <td>

										<?
										
										if($color_type_id==5){
										?>
                                            <input type="text" name="item_color_<? echo $i; ?>" id="item_color_<? echo $i; ?>" style="width:60px;" class="text_boxes" onChange="copy_value()" value="<?  echo $colorbreakdown[$color_number_id]; ?>"/>
                                            <input type="hidden" name="item_color_id_<? echo $i; ?>" id="item_color_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<? echo $item_color_id;?>" disabled="disabled"/>
										<?}else{?>
											<input type="text" name="item_color_<? echo $i; ?>" id="item_color_<? echo $i; ?>" style="width:60px;" class="text_boxes" onChange="copy_value()" value="<?  echo $color_library[$item_color_id]; ?>"/>
                                            <input type="hidden" name="item_color_id_<? echo $i; ?>" id="item_color_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<? echo $item_color_id;?>" disabled="disabled"/>
											<?}?>
                                        </td>
                                        <td><input type="text" name="findia_<? echo $i; ?>" id="findia_<? echo $i; ?>" value="<? echo $dia_width; ?>" class="text_boxes" style="width:60px;" /></td>
                                        <td>
                                            <input type="text" name="printing_color_<? echo $i; ?>" id="printing_color_<? echo $i; ?>" style="width:60px;" class="text_boxes" onChange="copy_value()" value="<?  echo $color_library[$item_color_id] ?>"/>
                                            <input type="hidden" name="printing_color_id_<? echo $i; ?>" id="printing_color_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<? echo $item_color_id;?>" disabled="disabled"/>
                                        </td>
                                        <td><input type="text" name="artworkno_<? echo $i; ?>" id="artworkno_<? echo $i; ?>" value="<? //echo $artwork_no; ?>" style="width:60px;" class="text_boxes"></td>
										<td><? echo create_drop_down("aop_mc_type_".$i, 60, $aop_mc_typeArr,"", 1, "--Select--","","",0); ?></td>
										<td><? echo create_drop_down("aop_type_".$i, 60, $print_type,"", 1, "--Select--","","",0); ?></td>
										<td><input type="text" name="startdate_<? echo $i; ?>" id="startdate_<? echo $i; ?>" value="<? //echo $delivery_date; ?>" style="width:60px;" class="datepicker"></td>
                                        <td><input type="text" name="enddate_<? echo $i; ?>" id="enddate_<? echo $i; ?>" value="<?  //echo $delivery_end_date; ?>" style="width:60px;" class="datepicker"></td>
                                        <td>
                                            <input type="text" name="txt_blanty_<? echo $i; ?>" id="txt_blanty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $i; ?>,'txt_woqnty');calculate_amount(<? echo $i; ?>)" value="<? echo $blaqnty; ?>"/>
                                            <input type="hidden" name="txtreqnty_<? echo $i; ?>" id="txtreqnty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? echo $req_qnty; ?>"/>
                                            <input type="hidden" name="txt_prev_woqnty_<? echo $i; ?>" id="txt_prev_woqnty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<?  echo $wo_cu_wo_qnty; ?>"/>
                                        </td>
                                        <td><input type="text" name="txt_woqnty_<? echo $i; ?>" id="txt_woqnty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $i; ?>,'txt_woqnty');calculate_amount(<? echo $i; ?>)" value="<? echo $blaqnty; ?>"/></td>
                                        <td><? echo create_drop_down("uom_".$i, 50, $unit_of_measurement,"", 1, "--Select--",$uom,"copy_value(".$i.",'uom')",1,"$uom_item"); ?></td>
                                        <td><input type="text" name="txt_rate_<? echo $i; ?>" id="txt_rate_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $i; ?>,'txt_rate'); calculate_amount(<? echo $i; ?>)" value="<? echo $charge_unit; ?>" ></td>
                                        <td><input type="text" name="txt_amount_<? echo $i; ?>" id="txt_amount_<? echo $i; ?>" reqamount="<? echo $amount; ?>" curamt="0" totamt="<? echo $wo_cu_wo_amt; ?>" style="width:60px;" class="text_boxes_numeric" value="<? echo $namt; ?>" disabled="disabled" examt="<? echo $budget_amt; ?>"/></td>
                                        <td>
                                            <input type="text" name="txt_paln_cut_<? echo $i; ?>" id="txt_paln_cut_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? echo  $plan_cut_qnty; ?>" disabled>
                                            <input type="hidden" name="updateid_<? echo $i; ?>" id="updateid_<? echo $i; ?>" value="<? echo $id; ?>">
                                        </td>
                                         <td>
                                        	<input type="button" class="image_uploader" id="uploader" style="width:60px" value="ADD Image" onClick="fnc_file_upload(<? echo $i;?>);">                                    </td>
                                    </tr>
                                    <?
                                    $i++;
                                }
                            }
                            else
                            {
                                ?>
                                <tr align="center">
                                    <td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"><? echo $job_no; ?>
                                    	<input type="hidden" name="job_no_<? echo $i; ?>" id="job_no_<? echo  $i; ?>" value="<? echo $job_no; ?>" style="width:60px;" class="text_boxes" disabled="disabled"></td>
                                    <td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;">
                                        <a href="#" onClick="setdata('<? echo $po_number;?>' )">View</a>
                                        <? //echo $po_number; ?>
                                        <input type="hidden" name="po_id_<? echo $i; ?>" id="po_id_<? echo  $i; ?>" value="<? echo $po_break_down_id; ?>" style="width:60px;" class="text_boxes" disabled="disabled">
                                    </td>
                                    <td>
										<? echo $body_part[$body_part_id];?>
                                        <input type="hidden" name="fabric_description_id_<? echo $i; ?>" id="fabric_description_id_<? echo $i; ?>" value="<? echo $pre_cost_conversion_cost_id; ?>" style="width:60px;" class="text_boxes" disabled="disabled">
                                    </td>
                                    <td><? echo $color_type[$color_type_id];?></td>
                                    <td><? echo $construction;?></td>
                                    <td><? echo $composition;?></td>
                                    <td><? echo $gsm_weight;?></td>
                                    <td>
										<? echo $dia_width;?>
                                        <input type="hidden" name="dia_<? echo $i; ?>" id="dia_<? echo $i; ?>" value="<? echo $dia_width; ?>" class="text_boxes" style="width:60px;" />
                                    </td>
                                    <td>
										<? echo $color_library[$color_number_id] ?>
                                        <input type="hidden" name="gmts_color_id_<? echo $i; ?>" id="gmts_color_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<?  echo $color_number_id;?>"disabled="disabled"/>
                                        <input type="hidden" name="color_size_table_id_<? echo $i; ?>" id="color_size_table_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<?  echo $color_size_table_id;?>" disabled="disabled"/>
                                    </td>
                                    <td>
                                        <input type="text" name="item_color_<? echo $i; ?>" id="item_color_<? echo $i; ?>" style="width:60px;" class="text_boxes" onChange="copy_value()" value="<?  echo $color_library[$item_color_id] ?>"/>
                                        <input type="hidden" name="item_color_id_<? echo $i; ?>" id="item_color_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<? echo $item_color_id;?>" disabled="disabled"/>
                                    </td>
                                    <td><input type="text" name="findia_<? echo $i; ?>" id="findia_<? echo $i; ?>" value="<? echo $dia_width; ?>" class="text_boxes" style="width:60px;" /></td>
                                    <td>
                                        <input type="text" name="printing_color_<? echo $i; ?>" id="printing_color_<? echo $i; ?>" style="width:60px;" class="text_boxes" onChange="copy_value()" value="<?  echo $color_library[$item_color_id] ?>"/>
                                        <input type="hidden" name="printing_color_id_<? echo $i; ?>" id="printing_color_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<? echo $item_color_id;?>" disabled="disabled"/>
                                    </td>
                                    <td><input type="text" name="artworkno_<? echo $i; ?>" id="artworkno_<? echo $i; ?>" value="<? //echo $artwork_no; ?>" style="width:60px;" class="text_boxes"></td>
									<td><? echo create_drop_down("aop_mc_type_".$i, 60, $aop_mc_typeArr,"", 1, "--Select--","","",0); ?></td>
									<td><? echo create_drop_down("aop_type_".$i, 60, $print_type,"", 1, "--Select--","","",0); ?></td>
									<td><input type="text" name="startdate_<? echo $i; ?>" id="startdate_<? echo $i; ?>" value="<? //echo $delivery_date; ?>" style="width:60px;" class="datepicker"></td>
                                    <td><input type="text" name="enddate_<? echo $i; ?>" id="enddate_<? echo $i; ?>" value="<?  //echo $delivery_end_date; ?>" style="width:60px;" class="datepicker"></td>
                                    <td>
                                        <input type="text" name="txt_blanty_<? echo $i; ?>" id="txt_blanty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $i; ?>,'txt_woqnty');calculate_amount(<? echo $i; ?>)" value=""/>
                                       <input type="hidden" name="txtreqnty_<? echo $i; ?>" id="txtreqnty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? echo $req_qnty; ?>"/>
                                       <input type="hidden" name="txt_prev_woqnty_<? echo $i; ?>" id="txt_prev_woqnty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric"  value=""/>
                                    </td>
                                    <td title="Req=<? echo $req_qnty; ?>"><input type="text" name="txt_woqnty_<? echo $i; ?>" id="txt_woqnty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $i; ?>,'txt_woqnty');calculate_amount(<? echo $i; ?>)" value=""/></td>
                                    <td><? echo create_drop_down("uom_".$i, 50, $unit_of_measurement,"", 1, "--Select--",$uom,"copy_value(".$i.",'uom')",1,"$uom_item"); ?></td>
                                    <td><input type="text" name="txt_rate_<? echo $i; ?>" id="txt_rate_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $i; ?>,'txt_rate');calculate_amount(<? echo $i; ?>)" value="<? echo $rate; ?>"></td>
                                    <td><input type="text" name="txt_amount_<? echo $i; ?>" id="txt_amount_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? //echo $amount; ?>" disabled="disabled"/></td>
                                    <td>
                                        <input type="text" name="txt_paln_cut_<? echo $i; ?>" id="txt_paln_cut_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? echo  $plan_cut_qnty; ?>" disabled>
                                        <input type="hidden" name="updateid_<? echo $i; ?>" id="updateid_<? echo $i; ?>" value="<? echo $id; ?>">
                                    </td>
                                     <td>
                                        <input type="button" class="image_uploader" id="uploader" style="width:60px" value="ADD Image" onClick="fnc_file_upload(<? echo $i;?>);">                                </td>
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
        <input type='hidden' id='json_data' name='json_data' value='<? echo json_encode($po_color_level_data_arr); ?>'/>
    </div>
	<?
	exit();
}

if($action=="show_aop_booking1")
{
	$color_library=return_library_array( "select id,color_name from lib_color where status_active =1 and is_deleted=0", "id", "color_name"  );
	$size_library=return_library_array( "select id,size_name from lib_size where status_active =1 and is_deleted=0", "id", "size_name"  );

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

	 $sql_vari_aop=sql_select("select b.fabric_source_aop_id from variable_order_tracking b,wo_booking_mst a  where b.company_name=a.company_id and a.booking_type=3 and a.process=35 and
	a.booking_no='$txt_booking_no' and b.variable_list=59 group by b.fabric_source_aop_id");
	$fabric_source_aop_id='';
	foreach( $sql_vari_aop as $row)
	{
		$fabric_source_aop_id=$row[csf("fabric_source_aop_id")];
	}
	if($fabric_source_aop_id==0 || $fabric_source_aop_id=='') $fabric_source_aop_id=0;else $fabric_source_aop_id=$fabric_source_aop_id;

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
	a.amount,a.gmts_size,a.gmts_color_id,a.dia_width,a.printing_color_id,a.aop_mc_type,a.aop_type
	from wo_booking_dtls a  where  a.booking_type=3 and a.process=35 and a.is_short=2  and a.pre_cost_fabric_cost_dtls_id in($data[4]) and a.status_active=1 and a.is_deleted=0 ";
	$dataArray=sql_select($sql);
	$z=1; $i=1;
	foreach($dataArray as $row){
		$id=$row[csf("id")];
		$pre_cost_conversion_cost_dtls_id=$row[csf("pre_cost_fabric_cost_dtls_id")];
		$artwork_no=$row[csf("artwork_no")];
		$aop_mc_type=$row[csf("aop_mc_type")];
		$aop_type=$row[csf("aop_type")];
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
		$cu_booking_data_arr[$pre_cost_conversion_cost_dtls_id][$po_break_down_id][$color_number_id][$dia_width]['amount']+=$amount;
	}
	//print_r($cu_booking_data_arr[1850]);

	$booking_data_arr=array();
	$sql="select a.id, a.pre_cost_fabric_cost_dtls_id, a.artwork_no, a.po_break_down_id, a.color_size_table_id, a.fabric_color_id, a.item_size, a.process,
	a.sensitivity, a.job_no, booking_no, a.booking_type, a.description, a.uom, a.delivery_date, a.delivery_end_date, a.wo_qnty, a.rate,
	a.amount, a.gmts_size, a.gmts_color_id, a.fin_dia, a.dia_width, a.printing_color_id,a.aop_mc_type,a.aop_type
	from wo_booking_dtls a  where  a.booking_type=3 and a.process=35 and
	a.booking_no='$txt_booking_no'  and a.pre_cost_fabric_cost_dtls_id=$data[4]  and   a.status_active=1 and a.is_deleted=0 ";
	$dataArray=sql_select($sql);
	$z=1; $i=1;
	foreach($dataArray as $row){
		$id=$row[csf("id")];
		$pre_cost_conversion_cost_dtls_id=$row[csf("pre_cost_fabric_cost_dtls_id")];
		$artwork_no=$row[csf("artwork_no")];
		$aop_mc_type=$row[csf("aop_mc_type")];
		$aop_type=$row[csf("aop_type")];
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
	//Issue ID=7561, As Per Rasel vai

	/*$fabric= new fabric($condition);

	$req_qty_arr=$fabric->getQtyArray_by_OrderFabriccostidGmtscolorAndDiaWidth_knitAndwoven_greyAndfinish();
	$req_amount_arr=$fabric->getAmountArray_by_OrderFabriccostidGmtscolorAndDiaWidth_knitAndwoven_greyAndfinish();*/

	$req_qty_arr=$conversion->getQtyArray_by_ConversionidOrderColorDiaWidthAndUom();
	$req_amount_arr=$conversion->getAmountArray_by_ConversionidOrderColorDiaWidthAndUom();

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
		$conv_cost_id = $row[csf("id")];
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
		/*if($cbo_fabric_natu==2){
			$wo_req_qnty = $req_qty_arr['knit']['grey'][$po_break_down_id][$pre_cost_fabric_cost_dtls_id][$color_number_id][$dia_width][$uom];
			$wo_reqAmount = $req_amount_arr['knit']['grey'][$po_break_down_id][$pre_cost_fabric_cost_dtls_id][$color_number_id][$dia_width][$uom];
		}
		if($cbo_fabric_natu==3){
			$wo_req_qnty = $req_qty_arr['woven']['grey'][$po_break_down_id][$pre_cost_fabric_cost_dtls_id][$color_number_id][$dia_width][$uom];
			$wo_reqAmount = $req_amount_arr['woven']['grey'][$po_break_down_id][$pre_cost_fabric_cost_dtls_id][$color_number_id][$dia_width][$uom];
		}*/
		//echo $po_break_down_id.'='.$pre_cost_fabric_cost_dtls_id.'='.$color_number_id.'='.$dia_width.'='.$uom;
		/*if($cbo_fabric_natu==2){

			if($fabric_source_aop_id==1 || $fabric_source_aop_id==0) //Grey
			{
				$req_qty = $req_qty_arr['knit']['grey'][$po_break_down_id][$pre_cost_fabric_cost_dtls_id][$color_number_id][$dia_width][$uom];
				$req_amt = $req_amount_arr['knit']['grey'][$po_break_down_id][$pre_cost_fabric_cost_dtls_id][$color_number_id][$dia_width][$uom];
			}
			else
			{
				$req_qty = $req_qty_arr['knit']['finish'][$po_break_down_id][$pre_cost_fabric_cost_dtls_id][$color_number_id][$dia_width][$uom];
				$req_amt = $req_amount_arr['knit']['finish'][$po_break_down_id][$pre_cost_fabric_cost_dtls_id][$color_number_id][$dia_width][$uom];
			}

			//$rate = $req_amt/$req_qty;
		}
		if($cbo_fabric_natu==3){
		if($fabric_source_aop_id==1 || $fabric_source_aop_id==0) //Grey
			{

				$req_qty = $req_qty_arr['woven']['grey'][$po_break_down_id][$pre_cost_fabric_cost_dtls_id][$color_number_id][$dia_width][$uom];
				$req_amt = $req_amount_arr['woven']['grey'][$po_break_down_id][$pre_cost_fabric_cost_dtls_id][$color_number_id][$dia_width][$uom];
			}
			else
			{
				$req_qty = $req_qty_arr['woven']['finish'][$po_break_down_id][$pre_cost_fabric_cost_dtls_id][$color_number_id][$dia_width][$uom];
				$req_amt = $req_amount_arr['woven']['finish'][$po_break_down_id][$pre_cost_fabric_cost_dtls_id][$color_number_id][$dia_width][$uom];
			}
				//$rate=$req_amt/$req_qty;
		}*/
		//$wo_req_amount = $req_amount_arr['knit']['grey'][$po_break_down_id][$pre_cost_fabric_cost_dtls_id][$color_number_id][$dia_width][$uom];
		$req_qty = $req_qty_arr[$conv_cost_id][$po_break_down_id][$color_number_id][$dia_width][$uom];
		$req_amt = $req_amount_arr[$conv_cost_id][$po_break_down_id][$color_number_id][$dia_width][$uom];

		$cu_wo_qnty        = $cu_booking_data_arr[$pre_cost_conversion_cost_id][$po_break_down_id][$color_number_id][$dia_width]['wo_qnty'];
		$cu_wo_amount      = $cu_booking_data_arr[$pre_cost_conversion_cost_id][$po_break_down_id][$color_number_id][$dia_width]['amount'];
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
			//$woqnty      = $wo_req_qnty - $process_loss_qty;
			$woqnty      = $wo_req_qnty;
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
		$blaamount = ($req_qty*$charge_unit);//$amount-$cu_wo_amount;
		//$blaamount =$amount-$cu_wo_amount;
		$rate    = $rate;
		$amount  = $amount;
		$uom     = $selected_uom;
		//echo $blaamount.'=';
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
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['cu_wo_amt'][$po_break_down_id]          	= $cu_wo_amount;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['blaqnty'][$po_break_down_id]             = $blaqnty;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['blaamount'][$po_break_down_id]           = $blaamount;
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

                                $blaamount                   = def_number_format($dia_width_val['blaamount'][$po_id],1,"");

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
                                $wo_cu_wo_qnty               = def_number_format($dia_width_val['cu_wo_qnty'][$po_id],1,"");

                                if($woqnty>0){
                                    ?>
                                    <tr align="center">
                                        <td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"><? echo $job_no; ?>
                                            <input type="hidden" name="job_no_<? echo $i; ?>" id="job_no_<? echo  $i; ?>" value="<? echo $job_no; ?>" style="width:60px;" class="text_boxes" disabled="disabled"></td>
                                        <td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"><? echo $po_number; ?>
                                            <input type="hidden" name="po_id_<? echo $i; ?>" id="po_id_<? echo  $i; ?>" value="<? echo $po_break_down_id; ?>" style="width:60px;" class="text_boxes" disabled="disabled"></td>
                                        <td><? echo $body_part[$body_part_id];?>
                                            <input type="hidden" name="fabric_description_id_<? echo $i; ?>" id="fabric_description_id_<? echo $i; ?>" value="<? echo $pre_cost_conversion_cost_id; ?>" style="width:60px;" class="text_boxes" disabled="disabled"></td>
                                        <td><? echo $color_type[$color_type_id];?></td>
                                        <td><? echo $construction;?></td>
                                        <td><? echo $composition;?></td>
                                        <td><? echo $gsm_weight;?></td>
                                        <td><? echo $dia_width;?>
                                            <input type="hidden" name="dia_<? echo $i; ?>" id="dia_<? echo $i; ?>" value="<? echo $dia_width; ?>" class="text_boxes" style="width:60px;" /></td>
                                        <td><? echo $color_library[$color_number_id] ?>
                                            <input type="hidden" name="gmts_color_id_<? echo $i; ?>" id="gmts_color_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<?  echo $color_number_id;?>"disabled="disabled"/>
                                            <input type="hidden" name="color_size_table_id_<? echo $i; ?>" id="color_size_table_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<?  echo $color_size_table_id;?>" disabled="disabled"/>
                                        </td>
                                        <td>
                                            <input type="text" name="item_color_<? echo $i; ?>" id="item_color_<? echo $i; ?>" style="width:60px;" class="text_boxes" onChange="copy_value()" value="<?  echo $color_library[$item_color_id] ?>"/>
                                            <input type="hidden" name="item_color_id_<? echo $i; ?>" id="item_color_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<? echo $item_color_id;?>" disabled="disabled"/>
                                        </td>
                                        <td><input type="text" name="findia_<? echo $i; ?>" id="findia_<? echo $i; ?>" value="<? echo $fin_dia; ?>" class="text_boxes" style="width:60px;" /></td>
                                        <td>
                                            <input type="text" name="printing_color_<? echo $i; ?>" id="printing_color_<? echo $i; ?>" style="width:60px;" class="text_boxes" onChange="copy_value()" value="<?  echo $color_library[$printing_color_id] ?>"/>
                                            <input type="hidden" name="printing_color_id_<? echo $i; ?>" id="printing_color_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<? echo $printing_color_id;?>" disabled="disabled"/>
                                        </td>
                                        <td><input type="text" name="artworkno_<? echo $i; ?>" id="artworkno_<? echo $i; ?>" value="<? echo $artwork_no; ?>" style="width:60px;" class="text_boxes"></td>
                                        <td><input type="text" name="startdate_<? echo $i; ?>" id="startdate_<? echo $i; ?>" value="<? echo change_date_format($delivery_date,"dd-mm-yyyy","-"); ?>" style="width:60px;" class="datepicker"></td>
                                        <td><input type="text" name="enddate_<? echo $i; ?>" id="enddate_<? echo $i; ?>" value="<? echo change_date_format($delivery_end_date,"dd-mm-yyyy","-"); ?>" style="width:60px;" class="datepicker"></td>
                                        <td>
                                            <input type="text" name="txt_blanty_<? echo $i; ?>" id="txt_blanty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $i; ?>,'txt_woqnty');calculate_amount(<? echo $i; ?>)" value="<? if($is_shrot==2) echo $blaqnty; else echo ""; ?>"/>
                                            <input type="hidden" name="txtreqnty_<? echo $i; ?>" id="txtreqnty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? if($is_shrot==2) echo $req_qnty; else echo ""; ?>"/>
                                            <input type="hidden" name="txt_prev_woqnty_<? echo $i; ?>" id="txt_prev_woqnty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? if($is_shrot==2) echo $wo_cu_wo_qnty; else echo ""; ?>"/>
                                        </td>
                                        <td><input type="text" name="txt_woqnty_<? echo $i; ?>" id="txt_woqnty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $i; ?>,'txt_woqnty');calculate_amount(<? echo $i; ?>)" value="<? echo $woqnty; ?>"/></td>
                                        <td><? echo create_drop_down("uom_".$i, 50, $unit_of_measurement,"", 1, "--Select--",$uom,"copy_value(".$i.",'uom')",1,"$uom_item"); ?></td>
                                        <td><input type="text" name="txt_rate_<? echo $i; ?>" id="txt_rate_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $i; ?>,'txt_rate');calculate_amount(<? echo $i; ?>)" value="<? echo $rate; ?>"></td>
                                        <td><input type="text" name="txt_amount_<? echo $i; ?>" id="txt_amount_<? echo $i; ?>" reqamount="<? echo $blaamount; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? echo $amount; ?>" disabled="disabled"/></td>
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

                            $blaamount                   = def_number_format(array_sum($dia_width_val['blaamount']),1,"");


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
                            $wo_cu_wo_qnty               = def_number_format(array_sum($dia_width_val['cu_wo_qnty']),1,"");
							$cu_wo_amt               		= def_number_format(array_sum($dia_width_val['cu_wo_amt']),1,"");
                            $rate                        = def_number_format($amount/$woqnty,1,"");

                            if($woqnty>0){
                                ?>
                                <tr align="center">
                                    <td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"><? echo $job_no; ?>
                                        <input type="hidden" name="job_no_<? echo $i; ?>" id="job_no_<? echo  $i; ?>" value="<? echo $job_no; ?>" style="width:60px;" class="text_boxes" disabled="disabled"></td>
                                    <td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;">
                                        <a href="#" onClick="setdata('<? echo $po_number;?>' )">View</a>
                                        <? //echo $po_number; ?>
                                        <input type="hidden" name="po_id_<? echo $i; ?>" id="po_id_<? echo  $i; ?>" value="<? echo $po_break_down_id; ?>" style="width:60px;" class="text_boxes" disabled="disabled">
                                    </td>
                                    <td>
                                        <? echo $body_part[$body_part_id];?>
                                        <input type="hidden" name="fabric_description_id_<? echo $i; ?>" id="fabric_description_id_<? echo $i; ?>" value="<? echo $pre_cost_conversion_cost_id; ?>" style="width:60px;" class="text_boxes" disabled="disabled">
                                    </td>
                                    <td><? echo $color_type[$color_type_id];?></td>
                                    <td><? echo $construction;?></td>
                                    <td><? echo $composition;?></td>
                                    <td><? echo $gsm_weight;?></td>
                                    <td><? echo $dia_width;?>
                                        <input type="hidden" name="dia_<? echo $i; ?>" id="dia_<? echo $i; ?>" value="<? echo $dia_width; ?>" class="text_boxes" style="width:60px;" /></td>
                                    <td><?  echo $color_library[$color_number_id] ?>
                                        <input type="hidden" name="gmts_color_id_<? echo $i; ?>" id="gmts_color_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<?  echo $color_number_id;?>"disabled="disabled"/>
                                        <input type="hidden" name="color_size_table_id_<? echo $i; ?>" id="color_size_table_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<?  echo $color_size_table_id;?>" disabled="disabled"/>
                                    </td>
                                    <td>
                                        <input type="text" name="item_color_<? echo $i; ?>" id="item_color_<? echo $i; ?>" style="width:60px;" class="text_boxes" onChange="copy_value()" value="<?  echo $color_library[$item_color_id] ?>"/>
                                        <input type="hidden" name="item_color_id_<? echo $i; ?>" id="item_color_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<? echo $item_color_id;?>" disabled="disabled"/>
                                    </td>
                                    <td><input type="text" name="findia_<? echo $i; ?>" id="findia_<? echo $i; ?>" value="<? echo $fin_dia; ?>" class="text_boxes" style="width:60px;" /></td>
                                    <td>
                                        <input type="text" name="printing_color_<? echo $i; ?>" id="printing_color_<? echo $i; ?>" style="width:60px;" class="text_boxes" onChange="copy_value()" value="<?  echo $color_library[$printing_color_id] ?>"/>
                                        <input type="hidden" name="printing_color_id_<? echo $i; ?>" id="printing_color_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<? echo $printing_color_id;?>" disabled="disabled"/>
                                    </td>
                                    <td><input type="text" name="artworkno_<? echo $i; ?>" id="artworkno_<? echo $i; ?>" value="<? echo $artwork_no; ?>" style="width:60px;" class="text_boxes"></td>
                                    <td><input type="text" name="startdate_<? echo $i; ?>" id="startdate_<? echo $i; ?>" value="<? echo change_date_format($delivery_date,"dd-mm-yyyy","-"); ?>" style="width:60px;" class="datepicker"></td>
                                    <td><input type="text" name="enddate_<? echo $i; ?>" id="enddate_<? echo $i; ?>" value="<? echo change_date_format($delivery_end_date,"dd-mm-yyyy","-"); ?>" style="width:60px;" class="datepicker"></td>
                                    <td>
                                        <input type="text" name="txt_blanty_<? echo $i; ?>" id="txt_blanty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $i; ?>,'txt_woqnty');calculate_amount(<? echo $i; ?>)" value="<? if($is_shrot==2) echo $blaqnty; else echo ""; ?>"/>
                                        <input type="hidden" name="txtreqnty_<? echo $i; ?>" id="txtreqnty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? echo $req_qnty;//if($is_shrot==2) echo $req_qnty; else echo ""; ?>"/>
                                        <input type="hidden" name="txt_prev_woqnty_<? echo $i; ?>" id="txt_prev_woqnty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? if($is_shrot==2) echo $wo_cu_wo_qnty; else echo ""; ?>"/>
                                    </td>
                                    <td><input type="text" name="txt_woqnty_<? echo $i; ?>" id="txt_woqnty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $i; ?>,'txt_woqnty');calculate_amount(<? echo $i; ?>)" value="<? echo $woqnty; ?>"/></td>
                                    <td><? echo create_drop_down("uom_".$i, 50, $unit_of_measurement,"", 1, "--Select--",$uom,"copy_value(".$i.",'uom')",1,"$uom_item"); ?></td>
                                    <td><input type="text" name="txt_rate_<? echo $i; ?>" id="txt_rate_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $i; ?>,'txt_rate');calculate_amount(<? echo $i; ?>)" value="<? echo $rate; ?>"></td>
                                    <td><input type="text" name="txt_amount_<? echo $i; ?>" id="txt_amount_<? echo $i; ?>" reqamount="<? echo $blaamount; ?>" curamt="<? echo $amount; ?>" totamt="<? echo $cu_wo_amt; ?>" style="width:60px;" class="text_boxes_numeric" value="<? echo $amount; ?>" disabled="disabled"/></td>
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
	exit();
}

if($action=="show_aop_booking")
{
	$color_library=return_library_array( "select id,color_name from lib_color where status_active =1 and is_deleted=0", "id", "color_name"  );
	$size_library=return_library_array( "select id,size_name from lib_size where status_active =1 and is_deleted=0", "id", "size_name"  );

	$data=explode("**",$data);
	$fabric_description_id=$data[0];
	$txt_order_no_id=$data[1];
	$txt_booking_no=$data[2];
	$cbo_level=$data[3];
	$conversion_cost_id=$data[4];
	$is_shrot=$data[5];
	$currency_id=$data[6];
	if($fabric_description_id==0){
		echo "<strong>Select Fabric</strong>";
		die;
    }

	 $sql_vari_aop=sql_select("select b.fabric_source_aop_id from variable_order_tracking b,wo_booking_mst a  where b.company_name=a.company_id and a.booking_type=3 and a.process=35 and
	a.booking_no='$txt_booking_no' and b.variable_list=59 group by b.fabric_source_aop_id");
	$fabric_source_aop_id=''; $str_job="";
	foreach( $sql_vari_aop as $row)
	{
		$fabric_source_aop_id=$row[csf("fabric_source_aop_id")];
	}
	if($fabric_source_aop_id==0 || $fabric_source_aop_id=='') $fabric_source_aop_id=0; else $fabric_source_aop_id=$fabric_source_aop_id;

	$fabric_description_array=array();
   /* $wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where id in($conversion_cost_id)");
	foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id){
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]!=0){
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls
			where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."'");
			list($fabric_description_row)=$fabric_description;
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")];
		}
	}*/

	$sql_fabric="select a.id, a.job_no, b.body_part_id, b.color_type_id, b.fabric_description from wo_pre_cost_fab_conv_cost_dtls a, wo_pre_cost_fabric_cost_dtls b where a.id in($conversion_cost_id) and b.id=a.fabric_description and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";

	$sql_fabric_res=sql_select($sql_fabric);
	foreach ($sql_fabric_res as $crow)
	{
		$str_job.="'".$crow[csf("job_no")]."'".',';
		$fabric_description_array[$crow[csf("id")]]=$body_part[$crow[csf("body_part_id")]].', '.$color_type[$crow[csf("color_type_id")]].', '.$crow[csf("fabric_description")];
	}
	unset($sql_fabric_res);

	$str_job=chop($str_job,',');
	$job_wise_currency_exrate_arr=array();
	$currency_exrate_sql="select a.job_no, a.exchange_rate, b.currency_id from wo_pre_cost_mst a, wo_po_details_master b where a.job_no=b.job_no and a.job_no in ($str_job) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$currency_exrate_sql_res=sql_select($currency_exrate_sql);
	foreach ($currency_exrate_sql_res as $cerow)
	{
		$job_wise_currency_exrate_arr[$cerow[csf("job_no")]]['exrate']=$cerow[csf("exchange_rate")];
		$job_wise_currency_exrate_arr[$cerow[csf("job_no")]]['currency']=$cerow[csf("currency_id")];
	}
	unset($currency_exrate_sql_res);

	$cu_booking_data_arr=array();
	$sql="select a.id, a.pre_cost_fabric_cost_dtls_id, a.artwork_no, a.po_break_down_id, a.color_size_table_id, a.fabric_color_id, a.item_size, a.process, sensitivity, a.job_no, booking_no, a.booking_type, a.description, a.uom, a.delivery_date, a.delivery_end_date, a.sensitivity, a.wo_qnty, a.rate, a.amount, a.gmts_size, a.gmts_color_id, a.dia_width, a.printing_color_id,a.aop_mc_type,a.aop_type from wo_booking_dtls a  where  a.booking_type=3 and a.process=35 and a.is_short=2  and a.pre_cost_fabric_cost_dtls_id in($data[4]) and a.status_active=1 and a.is_deleted=0 ";

	$dataArray=sql_select($sql);
	$z=1; $i=1;
	foreach($dataArray as $row)
	{
		$id=$row[csf("id")];
		$pre_cost_conversion_cost_dtls_id=$row[csf("pre_cost_fabric_cost_dtls_id")];
		$artwork_no=$row[csf("artwork_no")];
		$aop_mc_type=$row[csf("aop_mc_type")];
		$aop_type=$row[csf("aop_type")];
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
		$cu_booking_data_arr[$pre_cost_conversion_cost_dtls_id][$po_break_down_id][$color_number_id][$dia_width]['amount']+=$amount;
	}
	//print_r($cu_booking_data_arr[1850]);

	$booking_data_arr=array();
	$sql="select a.id, a.pre_cost_fabric_cost_dtls_id, a.artwork_no, a.po_break_down_id, a.color_size_table_id, a.fabric_color_id, a.item_size, a.process, a.sensitivity, a.job_no, booking_no, a.booking_type, a.description, a.uom, a.delivery_date, a.delivery_end_date, a.wo_qnty, a.rate, a.amount, a.gmts_size, a.gmts_color_id, a.fin_dia, a.dia_width, a.printing_color_id,a.aop_mc_type,a.aop_type from wo_booking_dtls a  where  a.booking_type=3 and a.process=35 and a.booking_no='$txt_booking_no'  and a.pre_cost_fabric_cost_dtls_id=$data[4]  and   a.status_active=1 and a.is_deleted=0 ";
	$dataArray=sql_select($sql);
	$z=1; $i=1;
	foreach($dataArray as $row){
		$id=$row[csf("id")];
		$pre_cost_conversion_cost_dtls_id=$row[csf("pre_cost_fabric_cost_dtls_id")];
		$artwork_no=$row[csf("artwork_no")];
		$aop_mc_type=$row[csf("aop_mc_type")];
		$aop_type=$row[csf("aop_type")];
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
		//echo $pre_cost_conversion_cost_dtls_id.'=='.$po_break_down_id.'=='.$color_number_id.'=='.$dia_width; die;
		$booking_data_arr[$pre_cost_conversion_cost_dtls_id][$po_break_down_id][$color_number_id][$dia_width]['id']=$id;
		$booking_data_arr[$pre_cost_conversion_cost_dtls_id][$po_break_down_id][$color_number_id][$dia_width]['fabric_description_id']=$pre_cost_conversion_cost_dtls_id;
		$booking_data_arr[$pre_cost_conversion_cost_dtls_id][$po_break_down_id][$color_number_id][$dia_width]['artwork_no']=$artwork_no;
		$booking_data_arr[$pre_cost_conversion_cost_dtls_id][$po_break_down_id][$color_number_id][$dia_width]['aop_mc_type']=$aop_mc_type;
		$booking_data_arr[$pre_cost_conversion_cost_dtls_id][$po_break_down_id][$color_number_id][$dia_width]['aop_type']=$aop_type;
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
	/*echo '<pre>';
	print_r($booking_data_arr); die;*/

	$condition= new condition();
	if(str_replace("'","",$txt_order_no_id) !=''){
		$condition->po_id("in($txt_order_no_id)");
	}
	$condition->init();
	$fabric= new fabric($condition);
	$req_qty_arr=$fabric->getQtyArray_by_OrderFabriccostidGmtscolorAndDiaWidth_knitAndwoven_greyAndfinish();
	$req_amount_arr=$fabric->getAmountArray_by_OrderFabriccostidGmtscolorAndDiaWidth_knitAndwoven_greyAndfinish();
	//Issue ID=7561 , As Per Rasel Vai
	//$conversion= new conversion($condition);
	//$req_qty_arr=$conversion->getQtyArray_by_ConversionidOrderColorDiaWidthAndUom();
	//$req_amount_arr=$conversion->getAmountArray_by_ConversionidOrderColorDiaWidthAndUom();
  //print_r($req_amount_arr);

	$sql="select a.job_no, b.id as po_break_down_id, b.po_number, min(c.id)as color_size_table_id, c.color_number_id, sum(c.plan_cut_qnty) as plan_cut_qnty, d.costing_per, e.id, e.fabric_description, e.cons_process, e.charge_unit, e.amount, e.color_break_down, e.process_loss, f.id as fid, f.body_part_id, f.color_type_id, f.construction, f.composition, f.gsm_weight, f.costing_per, f.uom, f.fab_nature_id, g.dia_width,

	CASE f.costing_per
	WHEN 1 THEN round((AVG(g.requirment)/12)*sum(c.plan_cut_qnty),4)
	WHEN 2 THEN round((AVG(g.requirment)/1)*sum(c.plan_cut_qnty),4)
	WHEN 3 THEN round((AVG(g.requirment)/24)*sum(c.plan_cut_qnty),4)
	WHEN 4 THEN round((AVG(g.requirment)/36)*sum(c.plan_cut_qnty),4)
	WHEN 5 THEN round((AVG(g.requirment)/48)*sum(c.plan_cut_qnty),4)
	ELSE 0 END as wo_req_qnty

	from
	wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_mst d, wo_pre_cost_fab_conv_cost_dtls e, wo_pre_cost_fabric_cost_dtls f, wo_pre_cos_fab_co_avg_con_dtls g

	where
	a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and a.job_no=f.job_no and a.job_no=g.job_no and b.id=c.po_break_down_id and b.id=g.po_break_down_id and c.color_number_id=g.color_number_id and c.size_number_id=g.gmts_sizes and c.item_number_id=f.item_number_id and f.id=g.pre_cost_fabric_cost_dtls_id and e.fabric_description=f.id and e.id in($conversion_cost_id) and b.id in($txt_order_no_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and g.requirment >0

	group by
	a.job_no, b.id, b.po_number, c.color_number_id, d.costing_per, e.id, e.fabric_description, e.cons_process, e.charge_unit, e.amount, e.color_break_down, e.process_loss, f.id, f.body_part_id, f.color_type_id, f.construction, f.composition, f.gsm_weight, f.costing_per, f.uom, f.fab_nature_id, g.dia_width
	order by b.id";
   //echo $sql;
	$dataArray=sql_select($sql);
	foreach($dataArray as $row){
		$job_no                      = $row[csf("job_no")];
		$po_number                   = $row[csf("po_number")];
		$po_break_down_id            = $row[csf("po_break_down_id")];
		$pre_cost_conversion_cost_id = $row[csf("id")];
		$conv_cost_id = $row[csf("id")];
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
		
		//$req_qty = $req_qty_arr[$conv_cost_id][$po_break_down_id][$color_number_id][$dia_width][$uom];
		//$req_amt = $req_amount_arr[$conv_cost_id][$po_break_down_id][$color_number_id][$dia_width][$uom];
		

		/*if($cbo_fabric_natu==2){
			$wo_req_qnty = $req_qty_arr['knit']['grey'][$po_break_down_id][$pre_cost_fabric_cost_dtls_id][$color_number_id][$dia_width][$uom];
			$wo_reqAmount = $req_amount_arr['knit']['grey'][$po_break_down_id][$pre_cost_fabric_cost_dtls_id][$color_number_id][$dia_width][$uom];
		}
		if($cbo_fabric_natu==3){
			$wo_req_qnty = $req_qty_arr['woven']['grey'][$po_break_down_id][$pre_cost_fabric_cost_dtls_id][$color_number_id][$dia_width][$uom];
			$wo_reqAmount = $req_amount_arr['woven']['grey'][$po_break_down_id][$pre_cost_fabric_cost_dtls_id][$color_number_id][$dia_width][$uom];
		}*/
		//echo $po_break_down_id.'='.$pre_cost_fabric_cost_dtls_id.'='.$color_number_id.'='.$dia_width.'='.$uom; $conv_cost_id
		if($cbo_fabric_natu==2){

			if($fabric_source_aop_id==1 || $fabric_source_aop_id==0) //Grey
			{
				$req_qty = $req_qty_arr['knit']['grey'][$po_break_down_id][$pre_cost_fabric_cost_dtls_id][$color_number_id][$dia_width][$uom];
				$req_amt = $req_amount_arr['knit']['grey'][$po_break_down_id][$pre_cost_fabric_cost_dtls_id][$color_number_id][$dia_width][$uom];
			}
			else
			{
				$req_qty = $req_qty_arr['knit']['finish'][$po_break_down_id][$pre_cost_fabric_cost_dtls_id][$color_number_id][$dia_width][$uom];
				$req_amt = $req_amount_arr['knit']['finish'][$po_break_down_id][$pre_cost_fabric_cost_dtls_id][$color_number_id][$dia_width][$uom];
			}

		}
		if($cbo_fabric_natu==3){
			if($fabric_source_aop_id==1 || $fabric_source_aop_id==0) //Grey
			{

				$req_qty = $req_qty_arr['woven']['grey'][$po_break_down_id][$pre_cost_fabric_cost_dtls_id][$color_number_id][$dia_width][$uom];
				$req_amt = $req_amount_arr['woven']['grey'][$po_break_down_id][$pre_cost_fabric_cost_dtls_id][$color_number_id][$dia_width][$uom];
			}
			else
			{
				$req_qty = $req_qty_arr['woven']['finish'][$po_break_down_id][$pre_cost_fabric_cost_dtls_id][$color_number_id][$dia_width][$uom];
				$req_amt = $req_amount_arr['woven']['finish'][$po_break_down_id][$pre_cost_fabric_cost_dtls_id][$color_number_id][$dia_width][$uom];
			}
				//$rate=$req_amt/$req_qty;
		}
		
		$req_avg_rate=$req_amt/$req_qty;

		$budget_rate=0;
		if($job_wise_currency_exrate_arr[$job_no]['currency']!=$currency_id)
		{
			if($currency_id==1) $budget_rate=$charge_unit*$job_wise_currency_exrate_arr[$job_no]['exrate'];
			else $budget_rate=$charge_unit;
			// echo $charge_unit.'='.$budget_rate.'<br>';
		} 
		else $budget_rate=$req_avg_rate;
		
		
		$req_amt=$req_qty*$budget_rate;
		
		$wo_req_qnty=0;
		$wo_req_qnty=$req_qty;

		$cu_wo_qnty        = $cu_booking_data_arr[$pre_cost_conversion_cost_id][$po_break_down_id][$color_number_id][$dia_width]['wo_qnty'];
		$cu_wo_amount      = $cu_booking_data_arr[$pre_cost_conversion_cost_id][$po_break_down_id][$color_number_id][$dia_width]['amount'];
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
			//$woqnty      = $wo_req_qnty - $process_loss_qty;
			$woqnty      = $wo_req_qnty;
			$selected_uom     = $uom;
		}

		if($body_part_id==2 || $body_part_id==3){
			$rate   = 0;
			$amount = 0;
			$bamount=0;
		}
		else{
			$rate   = $budget_rate;
			$amount = $rate*$woqnty;
		}
		$budget_amt=$budget_rate*$woqnty;

		$blaqnty = $woqnty - $cu_wo_qnty;
		$blaamount = ($req_qty*$budget_rate);//$amount-$cu_wo_amount;
		//$blaamount =$amount-$cu_wo_amount;
		$rate    = $rate;
		$amount  = $amount;
		$uom     = $selected_uom;
		//echo $blaamount.'=';
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
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['cu_wo_amt'][$po_break_down_id]          	= $cu_wo_amount;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['blaqnty'][$po_break_down_id]             = $blaqnty;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['blaamount'][$po_break_down_id]           = $blaamount;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['uom'][$po_break_down_id]                 = $uom;
		

		$id                = $booking_data_arr[$pre_cost_conversion_cost_id][$po_break_down_id][$color_number_id][$dia_width]['id'];
		$artwork_no        = $booking_data_arr[$pre_cost_conversion_cost_id][$po_break_down_id][$color_number_id][$dia_width]['artwork_no'];
		$aop_mc_type        = $booking_data_arr[$pre_cost_conversion_cost_id][$po_break_down_id][$color_number_id][$dia_width]['aop_mc_type'];
		$aop_type        = $booking_data_arr[$pre_cost_conversion_cost_id][$po_break_down_id][$color_number_id][$dia_width]['aop_type'];
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
		
		if($job_wise_currency_exrate_arr[$job_no]['currency']!=$currency_id)
		{
		$rate=$budget_rate;	
		$amount=$wo_qnty*$rate;
		}
		else
		{
			$rate=$rate;
		}

		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['id'][$po_break_down_id]                  = $id;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['artwork_no'][$po_break_down_id]          = $artwork_no;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['aop_mc_type'][$po_break_down_id]         = $aop_mc_type;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['aop_type'][$po_break_down_id]            = $aop_type;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['fabric_color_id'][$po_break_down_id]     = $fabric_color_id;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['printing_color_id'][$po_break_down_id]     = $printing_color_id;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['fin_dia'][$po_break_down_id]             = $fin_dia;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['delivery_date'][$po_break_down_id]       = $delivery_date;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['delivery_end_date'][$po_break_down_id]   = $delivery_end_date;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['woqnty'][$po_break_down_id]              = $wo_qnty;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['rate'][$po_break_down_id]                = $rate;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['charge_unit'][$po_break_down_id]                = $charge_unit;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['amount'][$po_break_down_id]              = $amount;
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['budget_amt'][$po_break_down_id]          = $budget_amt;
	}
	?>
    <div id="content_search_panel_<? echo $pre_cost_conversion_cost_id; ?>" style="" class="accord_close">
        <table class="rpt_table" border="1" width="1680" cellpadding="0" cellspacing="0" rules="all" id="tbl_table" style="table-layout: fixed;">
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
				<th>AOP M/C Type</th>
				<th>AOP Type</th>
                <th>Delivery Start Date</th>
                <th>Delivery End Date</th>
                <th>Bla. Qnty</th>
                <th>WO. Qnty</th>
                <th>UOM</th>
                <th>Rate</th>
                <th>Amount</th>
                <th>Plan Cut Qnty</th>
                <th>Image</th>
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

                                $blaamount                   = def_number_format($dia_width_val['blaamount'][$po_id],5,"");

                                $booking_id                  = $dia_width_val['id'][$po_id];
                                $artwork_no                  = $dia_width_val['artwork_no'][$po_id];
								$aop_mc_type                 = $dia_width_val['aop_mc_type'][$po_id];
								$aop_type                    = $dia_width_val['aop_type'][$po_id];
								
                                $item_color_id               = $dia_width_val['fabric_color_id'][$po_id];
                                $printing_color_id           = $dia_width_val['printing_color_id'][$po_id];
                                $fin_dia                     = $dia_width_val['fin_dia'][$po_id];
                                $delivery_date               = $dia_width_val['delivery_date'][$po_id];
                                $delivery_end_date           = $dia_width_val['delivery_end_date'][$po_id];

                                $woqnty                      = def_number_format($dia_width_val['woqnty'][$po_id],1,"");
                                $rate                        = def_number_format($dia_width_val['rate'][$po_id],5,"");
                                $amount                      = def_number_format($dia_width_val['amount'][$po_id],1,"");
								$budget_amt                  = def_number_format($dia_width_val['budget_amt'][$po_id],5,"");
                                $wo_cu_wo_qnty               = def_number_format($dia_width_val['cu_wo_qnty'][$po_id],1,"");

                                if($woqnty>0){
                                    ?>
                                    <tr align="center">
                                        <td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"><? echo $job_no; ?>
                                            <input type="hidden" name="job_no_<? echo $i; ?>" id="job_no_<? echo  $i; ?>" value="<? echo $job_no; ?>" style="width:60px;" class="text_boxes" disabled="disabled"></td>
                                        <td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"><? echo $po_number; ?>
                                            <input type="hidden" name="po_id_<? echo $i; ?>" id="po_id_<? echo  $i; ?>" value="<? echo $po_break_down_id; ?>" style="width:60px;" class="text_boxes" disabled="disabled"></td>
                                        <td><? echo $body_part[$body_part_id];?>
                                            <input type="hidden" name="fabric_description_id_<? echo $i; ?>" id="fabric_description_id_<? echo $i; ?>" value="<? echo $pre_cost_conversion_cost_id; ?>" style="width:60px;" class="text_boxes" disabled="disabled"></td>
                                        <td><? echo $color_type[$color_type_id];?></td>
                                        <td><? echo $construction;?></td>
                                        <td><? echo $composition;?></td>
                                        <td><? echo $gsm_weight;?></td>
                                        <td><? echo $dia_width;?>
                                            <input type="hidden" name="dia_<? echo $i; ?>" id="dia_<? echo $i; ?>" value="<? echo $dia_width; ?>" class="text_boxes" style="width:60px;" /></td>
                                        <td><? echo $color_library[$color_number_id] ?>
                                            <input type="hidden" name="gmts_color_id_<? echo $i; ?>" id="gmts_color_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<?  echo $color_number_id;?>"disabled="disabled"/>
                                            <input type="hidden" name="color_size_table_id_<? echo $i; ?>" id="color_size_table_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<?  echo $color_size_table_id;?>" disabled="disabled"/>
                                        </td>
                                        <td>
                                            <input type="text" name="item_color_<? echo $i; ?>" id="item_color_<? echo $i; ?>" style="width:60px;" class="text_boxes" onChange="copy_value()" value="<?  echo $color_library[$item_color_id] ?>"/>
                                            <input type="hidden" name="item_color_id_<? echo $i; ?>" id="item_color_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<? echo $item_color_id;?>" disabled="disabled"/>
                                        </td>
                                        <td><input type="text" name="findia_<? echo $i; ?>" id="findia_<? echo $i; ?>" value="<? echo $fin_dia; ?>" class="text_boxes" style="width:60px;" /></td>
                                        <td>
                                            <input type="text" name="printing_color_<? echo $i; ?>" id="printing_color_<? echo $i; ?>" style="width:60px;" class="text_boxes" onChange="copy_value()" value="<?  echo $color_library[$printing_color_id] ?>"/>
                                            <input type="hidden" name="printing_color_id_<? echo $i; ?>" id="printing_color_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<? echo $printing_color_id;?>" disabled="disabled"/>
                                        </td>
                                        <td><input type="text" name="artworkno_<? echo $i; ?>" id="artworkno_<? echo $i; ?>" value="<? echo $artwork_no; ?>" style="width:60px;" class="text_boxes"></td>
                                        <td><? echo create_drop_down("aop_mc_type_".$i, 60, $aop_mc_typeArr,"", 1, "--Select--",$aop_mc_type,"",0); ?></td>
										<td><? echo create_drop_down("aop_type_".$i,60, $print_type,"", 1, "--Select--",$aop_type,"",0); ?></td>
										<td><input type="text" name="startdate_<? echo $i; ?>" id="startdate_<? echo $i; ?>" value="<? echo change_date_format($delivery_date,"dd-mm-yyyy","-"); ?>" style="width:60px;" class="datepicker"></td>
                                        <td><input type="text" name="enddate_<? echo $i; ?>" id="enddate_<? echo $i; ?>" value="<? echo change_date_format($delivery_end_date,"dd-mm-yyyy","-"); ?>" style="width:60px;" class="datepicker"></td>
                                        <td>
                                            <input type="text" name="txt_blanty_<? echo $i; ?>" id="txt_blanty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $i; ?>,'txt_woqnty');calculate_amount(<? echo $i; ?>)" value="<? if($is_shrot==2) echo $blaqnty; else echo ""; ?>"/>
                                            <input type="hidden" name="txtreqnty_<? echo $i; ?>" id="txtreqnty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? if($is_shrot==2) echo $req_qnty; else echo ""; ?>"/>
                                            <input type="hidden" name="txt_prev_woqnty_<? echo $i; ?>" id="txt_prev_woqnty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? if($is_shrot==2) echo $wo_cu_wo_qnty; else echo ""; ?>"/>
                                        </td>
                                        <td><input type="text" name="txt_woqnty_<? echo $i; ?>" id="txt_woqnty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $i; ?>,'txt_woqnty');calculate_amount(<? echo $i; ?>)" value="<? echo $woqnty; ?>"/></td>
                                        <td><? echo create_drop_down("uom_".$i, 50, $unit_of_measurement,"", 1, "--Select--",$uom,"copy_value(".$i.",'uom')",1,"$uom_item"); ?></td>
                                        <td><input type="text" name="txt_rate_<? echo $i; ?>" id="txt_rate_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $i; ?>,'txt_rate');calculate_amount(<? echo $i; ?>)" value="<? echo $rate; ?>"></td>
                                        <td><input type="text" name="txt_amount_<? echo $i; ?>" id="txt_amount_<? echo $i; ?>" reqamount="<? echo $blaamount; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? echo $amount; ?>" examt="<? echo $budget_amt; ?>" disabled="disabled"/></td>
                                        <td>
                                            <input type="text" name="txt_paln_cut_<? echo $i; ?>" id="txt_paln_cut_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? echo  $row[csf("plan_cut_qnty")]; ?>" disabled>
                                            <input type="hidden" name="updateid_<? echo $i; ?>" id="updateid_<? echo $i; ?>" value="<? echo $booking_id; ?>">
                                        </td>
                                        <td>
                                        	<input type="button" class="image_uploader" id="uploader" style="width:60px" value="ADD Image" onClick="fnc_file_upload(<? echo $i;?>);">
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
                // echo "<pre>";
                // print_r($po_color_level_data_arr); die;
                foreach($po_color_level_data_arr as $precost_conversion_cost_id=>$pre_cost_cost_conversion_cost_val){
                    foreach($pre_cost_cost_conversion_cost_val as $color_id=>$color_val){
                        foreach($color_val as $dia_width_id=>$dia_width_val){
                            	/*echo "<pre>";
                            	print_r($dia_width_val['po_id']); die;*/
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

                            $blaamount                   = def_number_format(array_sum($dia_width_val['blaamount']),5,"");


                            $booking_id                  = implode(",",array_unique($dia_width_val['id']));
                            $artwork_no                  = implode(",",array_unique($dia_width_val['artwork_no']));
							$aop_mc_type                  = implode(",",array_unique($dia_width_val['aop_mc_type']));
							$aop_type                  = implode(",",array_unique($dia_width_val['aop_type']));
                            $item_color_id               = implode(",",array_unique($dia_width_val['fabric_color_id']));
                            $printing_color_id           = implode(",",array_unique($dia_width_val['printing_color_id']));
                            $fin_dia                     = implode(",",array_unique($dia_width_val['fin_dia']));
                            $delivery_date               = implode(",",array_unique($dia_width_val['delivery_date']));
                            $delivery_end_date           = implode(",",array_unique($dia_width_val['delivery_end_date']));

                            $woqnty                      = def_number_format(array_sum($dia_width_val['woqnty']),1,"");
                            $rate                        = def_number_format(array_sum($dia_width_val['rate']),5,"");
                            $amount                      = def_number_format(array_sum($dia_width_val['amount']),5,"");
							$budget_amt                  = def_number_format(array_sum($dia_width_val['budget_amt']),1,"");
                            $wo_cu_wo_qnty               = def_number_format(array_sum($dia_width_val['cu_wo_qnty']),1,"");
							$cu_wo_amt               	 = def_number_format(array_sum($dia_width_val['cu_wo_amt']),1,"");
							$charge_unit                        = def_number_format(array_sum($dia_width_val['charge_unit']),5,"");
                            $rate                        = def_number_format($amount/$woqnty,5,"");
							    	/*$job_no                      = $dia_width_val['job_no'][$po_id];
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

                                $blaamount                   = def_number_format($dia_width_val['blaamount'][$po_id],1,"");$booking_id                  = $dia_width_val['id'][$po_id];
                                $artwork_no                  = $dia_width_val['artwork_no'][$po_id];
                                $item_color_id               = $dia_width_val['fabric_color_id'][$po_id];
                                $printing_color_id           = $dia_width_val['printing_color_id'][$po_id];
                                $fin_dia                     = $dia_width_val['fin_dia'][$po_id];
                                $delivery_date               = $dia_width_val['delivery_date'][$po_id];
                                $delivery_end_date           = $dia_width_val['delivery_end_date'][$po_id];
                            $woqnty                      = def_number_format(array_sum($dia_width_val['woqnty']),1,"");
                            $rate                        = def_number_format(array_sum($dia_width_val['rate']),1,"");
                            $amount                      = def_number_format(array_sum($dia_width_val['amount']),1,"");
							$budget_amt                  = def_number_format(array_sum($dia_width_val['budget_amt']),1,"");
                            $wo_cu_wo_qnty               = def_number_format(array_sum($dia_width_val['cu_wo_qnty']),1,"");
							$cu_wo_amt               	 = def_number_format(array_sum($dia_width_val['cu_wo_amt']),1,"");
                            $rate                        = def_number_format($amount/$woqnty,1,"");*/

                            if($woqnty>0){
                                ?>
                                <tr align="center">
                                    <td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"><? echo $job_no; ?>
                                        <input type="hidden" name="job_no_<? echo $i; ?>" id="job_no_<? echo  $i; ?>" value="<? echo $job_no; ?>" style="width:60px;" class="text_boxes" disabled="disabled"></td>
                                    <td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;">
                                        <a href="#" onClick="setdata('<? echo $po_number;?>' )">View</a>
                                        <? //echo $po_number; ?>
                                        <input type="hidden" name="po_id_<? echo $i; ?>" id="po_id_<? echo  $i; ?>" value="<? echo $po_break_down_id; ?>" style="width:60px;" class="text_boxes" disabled="disabled">
                                    </td>
                                    <td>
                                        <? echo $body_part[$body_part_id];?>
                                        <input type="hidden" name="fabric_description_id_<? echo $i; ?>" id="fabric_description_id_<? echo $i; ?>" value="<? echo $pre_cost_conversion_cost_id; ?>" style="width:60px;" class="text_boxes" disabled="disabled">
                                    </td>
                                    <td><? echo $color_type[$color_type_id];?></td>
                                    <td><? echo $construction;?></td>
                                    <td><? echo $composition;?></td>
                                    <td><? echo $gsm_weight;?></td>
                                    <td><? echo $dia_width;?>
                                        <input type="hidden" name="dia_<? echo $i; ?>" id="dia_<? echo $i; ?>" value="<? echo $dia_width; ?>" class="text_boxes" style="width:60px;" /></td>
                                    <td><?  echo $color_library[$color_number_id] ?>
                                        <input type="hidden" name="gmts_color_id_<? echo $i; ?>" id="gmts_color_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<?  echo $color_number_id;?>"disabled="disabled"/>
                                        <input type="hidden" name="color_size_table_id_<? echo $i; ?>" id="color_size_table_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<?  echo $color_size_table_id;?>" disabled="disabled"/>
                                    </td>
                                    <td>
                                        <input type="text" name="item_color_<? echo $i; ?>" id="item_color_<? echo $i; ?>" style="width:60px;" class="text_boxes" onChange="copy_value()" value="<?  echo $color_library[$item_color_id] ?>"/>
                                        <input type="hidden" name="item_color_id_<? echo $i; ?>" id="item_color_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<? echo $item_color_id;?>" disabled="disabled"/>
                                    </td>
                                    <td><input type="text" name="findia_<? echo $i; ?>" id="findia_<? echo $i; ?>" value="<? echo $fin_dia; ?>" class="text_boxes" style="width:60px;" /></td>
                                    <td>
                                        <input type="text" name="printing_color_<? echo $i; ?>" id="printing_color_<? echo $i; ?>" style="width:60px;" class="text_boxes" onChange="copy_value()" value="<?  echo $color_library[$printing_color_id] ?>"/>
                                        <input type="hidden" name="printing_color_id_<? echo $i; ?>" id="printing_color_id_<? echo $i; ?>" style="width:60px;" class="text_boxes"  value="<? echo $printing_color_id;?>" disabled="disabled"/>
                                    </td>
                                    <td><input type="text" name="artworkno_<? echo $i; ?>" id="artworkno_<? echo $i; ?>" value="<? echo $artwork_no; ?>" style="width:60px;" class="text_boxes"></td>
									<td><? echo create_drop_down("aop_mc_type_".$i, 60, $aop_mc_typeArr,"", 1, "--Select--",$aop_mc_type,"",0); ?></td>
									<td><? echo create_drop_down("aop_type_".$i, 60, $print_type,"", 1, "--Select--",$aop_type,"",0); ?></td>
                                    <td><input type="text" name="startdate_<? echo $i; ?>" id="startdate_<? echo $i; ?>" value="<? echo change_date_format($delivery_date,"dd-mm-yyyy","-"); ?>" style="width:60px;" class="datepicker"></td>
                                    <td><input type="text" name="enddate_<? echo $i; ?>" id="enddate_<? echo $i; ?>" value="<? echo change_date_format($delivery_end_date,"dd-mm-yyyy","-"); ?>" style="width:60px;" class="datepicker"></td>
                                    <td>
                                        <input type="text" name="txt_blanty_<? echo $i; ?>" id="txt_blanty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $i; ?>,'txt_woqnty');calculate_amount(<? echo $i; ?>)" value="<? if($is_shrot==2) echo $blaqnty; else echo ""; ?>"/>
                                        <input type="hidden" name="txtreqnty_<? echo $i; ?>" id="txtreqnty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? echo $req_qnty;//if($is_shrot==2) echo $req_qnty; else echo ""; ?>"/>
                                        <input type="hidden" name="txt_prev_woqnty_<? echo $i; ?>" id="txt_prev_woqnty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? if($is_shrot==2) echo $wo_cu_wo_qnty; else echo ""; ?>"/>
                                    </td>
                                    <td><input type="text" name="txt_woqnty_<? echo $i; ?>" id="txt_woqnty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $i; ?>,'txt_woqnty');calculate_amount(<? echo $i; ?>)" value="<? echo $woqnty; ?>"/></td>
                                    <td><? echo create_drop_down("uom_".$i, 50, $unit_of_measurement,"", 1, "--Select--",$uom,"copy_value(".$i.",'uom')",1,"$uom_item"); ?></td>
                                    <td><input type="text" name="txt_rate_<? echo $i; ?>" id="txt_rate_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $i; ?>,'txt_rate');calculate_amount(<? echo $i; ?>)" value="<? echo $charge_unit; ?>"></td>
                                    <td><input type="text" name="txt_amount_<? echo $i; ?>" id="txt_amount_<? echo $i; ?>" reqamount="<? echo $blaamount; ?>" curamt="<? echo $amount; ?>" totamt="<? echo $cu_wo_amt; ?>" style="width:60px;" class="text_boxes_numeric" value="<? echo $amount; ?>" disabled="disabled" examt="<? echo $budget_amt; ?>" /></td>
                                    <td>
                                    <input type="text" name="txt_paln_cut_<? echo $i; ?>" id="txt_paln_cut_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? echo  $row[csf("plan_cut_qnty")]; ?>" disabled>
                                    <input type="hidden" name="updateid_<? echo $i; ?>" id="updateid_<? echo $i; ?>" value="<? echo $booking_id; ?>">
                                    </td>
                                    <td>
                                        	<input type="button" class="image_uploader" id="uploader" style="width:60px" value="ADD Image" onClick="fnc_file_upload(<? echo $i;?>);">
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
	exit();
}

if ($action=="fabric_detls_list_view"){
	$data=explode("**",$data);
	$color_library=return_library_array( "select id,color_name from lib_color where status_active =1 and is_deleted=0", "id", "color_name");

	$po_number_arr=return_library_array( "select id,po_number from wo_po_break_down", "id", "po_number");
	$txt_booking_no="'".$data[0]."'";

	$wo_pre_cost_fab_conv_cost_dtls_id=sql_select('select a.id AS "aid",a.body_part_id AS "body_part_id",a.color_type_id AS "color_type_id",a.fabric_description AS "fabric_description",a.gsm_weight AS "gsm_weight",b.id AS "bid",b.cons_process AS "cons_process",c.id AS "id",c.job_no AS "job_no",c.po_break_down_id AS "po_break_down_id",c.booking_no AS "booking_no",c.pre_cost_fabric_cost_dtls_id AS "pre_cost_fabric_cost_dtls_id",c.dia_width AS "dia_width",c.wo_qnty AS "wo_qnty",c.amount AS "amount",c.gmts_color_id AS "gmts_color_id" from  wo_pre_cost_fabric_cost_dtls a,wo_pre_cost_fab_conv_cost_dtls b,wo_booking_dtls c  where a.job_no=b.job_no and a.job_no=c.job_no and a.id=b.fabric_description and b.id=c.pre_cost_fabric_cost_dtls_id  and c.booking_no='.$txt_booking_no.' and  c.process=35 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0');



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
		exit();
}

if ($action=="save_update_delete"){
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$ready_to_approved = str_replace(",", "", $cbo_ready_to_approved);

	if(str_replace("'","",$txt_booking_no)!='')
	{
		$sql=sql_select("select embellishment_job, subcon_job from subcon_ord_mst where order_no=$txt_booking_no and status_active=1 and is_deleted=0");
		foreach($sql as $row){

			if($row[csf('embellishment_job')]=="") $row[csf('embellishment_job')]=$row[csf('subcon_job')];
			$lock_another_process=$row[csf('embellishment_job')];
		}
		if($lock_another_process!=''){
			echo "lockAnotherProcess**".$lock_another_process;
			disconnect($con);die;
		}
	}

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
		$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'ASB', date("Y",time()), 5,"select id, booking_no_prefix, booking_no_prefix_num from wo_booking_mst where company_id=$cbo_company_name and booking_type=3 and entry_form=162 and to_char(insert_date,'YYYY')=".date('Y',time())." order by id desc ", "booking_no_prefix", "booking_no_prefix_num" ));

		$id=return_next_id( "id", "wo_booking_mst", 1 ) ;
		$field_array="id, booking_type, is_short, booking_month, booking_year, booking_no_prefix, booking_no_prefix_num, booking_no, entry_form, company_id, buyer_id, item_category, supplier_id, currency_id, exchange_rate, booking_date, delivery_date, pay_mode, source, attention, tenor, process, cbo_level, tagged_booking_no, ready_to_approved,delivery_to,remarks, inserted_by, insert_date, status_active, is_deleted";
		$data_array ="(".$id.",3,".$cbo_is_short.",".$cbo_booking_month.",".$cbo_booking_year.",'".$new_booking_no[1]."',".$new_booking_no[2].",'".$new_booking_no[0]."',162,".$cbo_company_name.",".$cbo_buyer_name.",12,".$cbo_supplier_name.",".$cbo_currency.",".$txt_exchange_rate.",".$txt_booking_date.",".$txt_delivery_date.",".$cbo_pay_mode.",".$cbo_source.",".$txt_attention.",".$txt_tenor.",35,".$cbo_level.",".$txt_fab_booking.",".$ready_to_approved.",".$txt_delivery_to.",".$txt_remark.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		$response_booking_no=$new_booking_no[0];
		$rID=sql_insert("wo_booking_mst",$field_array,$data_array,0);

		if($db_type==0){
			if($rID){
				mysql_query("COMMIT");
				echo "0**".$response_booking_no."**".$id;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$response_booking_no."**".$id;
			}
		}
		else if($db_type==2 || $db_type==1 ){
			if($rID){
				oci_commit($con);
				echo "0**".$response_booking_no."**".$id;
			}
			else{
				oci_rollback($con);
				echo "10**".$response_booking_no."**".$id;
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
		$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no  and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
		if($pi_number){
			echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
			disconnect($con);die;
		}

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
		$recv_number=return_field_value( "a.recv_number as recv_number", "inv_receive_mas_batchroll a,pro_grey_batch_dtls b"," a.id=b.mst_id  and b.booking_no=$txt_booking_no and a.entry_form in(91,92) and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0","recv_number");
		if($recv_number){
			echo "recv_no**".str_replace("'","",$txt_booking_no)."**".$recv_number;
			die;
		}
		 if($db_type==0){
			mysql_query("BEGIN");
			
		 }
		 $field_array_up="booking_month*booking_year*buyer_id*item_category*supplier_id*currency_id*exchange_rate*booking_date*delivery_date*pay_mode*source*attention*tenor*cbo_level*tagged_booking_no*ready_to_approved*delivery_to*remarks*updated_by*update_date";
		 $data_array_up ="".$cbo_booking_month."*".$cbo_booking_year."*".$cbo_buyer_name."*12*".$cbo_supplier_name."*".$cbo_currency."*".$txt_exchange_rate."*".$txt_booking_date."*".$txt_delivery_date."*".$cbo_pay_mode."*".$cbo_source."*".$txt_attention."*".$txt_tenor."*".$cbo_level."*".$txt_fab_booking."*".$ready_to_approved."*".$txt_delivery_to."*".$txt_remark."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
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
		else if($db_type==2 || $db_type==1 ){
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
		$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no  and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
		if($pi_number){
			echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
			disconnect($con);die;
		}
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
		
		$recv_number=return_field_value( "a.recv_number as recv_number", "inv_receive_mas_batchroll a,pro_grey_batch_dtls b"," a.id=b.mst_id  and b.booking_no=$txt_booking_no and a.entry_form in(91,92) and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0","recv_number");
		if($recv_number){
			echo "recv_no**".str_replace("'","",$txt_booking_no)."**".$recv_number;
			disconnect($con);die;
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
		else if($db_type==2 || $db_type==1 ){
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
	$color_library=return_library_array( "select id,color_name from lib_color where status_active =1 and is_deleted=0", "id", "color_name"  );
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if(str_replace("'","",$txt_booking_no)!='')
	{
		$sql=sql_select("select embellishment_job, subcon_job from subcon_ord_mst where order_no=$txt_booking_no and status_active=1 and is_deleted=0");
		foreach($sql as $row){

			if($row[csf('embellishment_job')]=="") $row[csf('embellishment_job')]=$row[csf('subcon_job')];
			$lock_another_process=$row[csf('embellishment_job')];
		}
		if($lock_another_process!=''){
			echo "lockAnotherProcess**".$lock_another_process;
			disconnect($con);die;
		}
	}

	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0";disconnect($con); die;}
		 $id_dtls=return_next_id( "id", "wo_booking_dtls", 1 ) ;
		 $field_array1="id, booking_mst_id, is_short, job_no, pre_cost_fabric_cost_dtls_id, entry_form_id, color_size_table_id, artwork_no, aop_mc_type, aop_type, po_break_down_id, booking_no, booking_type, fabric_color_id, gmts_color_id, printing_color_id, description, uom, process, wo_qnty, rate, amount, delivery_date, delivery_end_date, dia_width, fin_dia, inserted_by, insert_date, status_active, is_deleted";
		 $new_array_color=array();
		 for ($i=1;$i<=$row_num;$i++)
		 {
			 $job_no="job_no_".$i;
			 $po_id="po_id_".$i;
			 $fabric_description_id="fabric_description_id_".$i;
			 $color_size_table_id="color_size_table_id_".$i;
			 $dia="dia_".$i;
			 $artworkno="artworkno_".$i;			 
 			 $aop_mc_type="aop_mc_type_".$i;
 			 $aop_type="aop_type_".$i;
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
			 $data_array1 .="(".$id_dtls.",".$update_id.",".$cbo_is_short.",".$$job_no.",".$$fabric_description_id.",162,".$$color_size_table_id.",".$$artworkno.",".$$aop_mc_type.",".$$aop_type.",".$$po_id.",".$txt_booking_no.",3,".$color_id.",".$$gmts_color_id.",".$print_color_id.",".$$fabric_description_id.",".$$uom.",35,".$$txt_woqnty.",".$$txt_rate.",".$$txt_amount.",".$$startdate.",".$$enddate.",".$$dia.",".$$findia.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
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
		else if($db_type==2 || $db_type==1 )
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
		$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no  and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
		if($pi_number){
			echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
			disconnect($con);die;
		}

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
		
		$recv_number=return_field_value( "a.recv_number as recv_number", "inv_receive_mas_batchroll a,pro_grey_batch_dtls b"," a.id=b.mst_id  and b.booking_no=$txt_booking_no and a.entry_form in(91,92) and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0","recv_number");
		if($recv_number){
			echo "recv_no**".str_replace("'","",$txt_booking_no)."**".$recv_number;
			disconnect($con);die;
		}
		//========Issue to Fin Process Found====================
		$updtlsid="";
		for ($i=1;$i<=$row_num;$i++)
		{
			$updateid="updateid_".$i;
			if( trim(str_replace("'",'',$$updateid))!="")
			{
				if($updtlsid=="") $updtlsid=str_replace("'",'',$$updateid); else $updtlsid.=','.str_replace("'",'',$$updateid);
			}
		}
		
		$updtlsid=implode(",",array_filter(array_unique(explode(",",$updtlsid))));
		$dtlsidCond="";
		if($updtlsid!="") $dtlsidCond="and b.booking_dtls_id in ($updtlsid)";
		$issueTofinProcess_mrr=0;
		$sqlissueFinProcess=sql_select("select a.entry_form,a.recv_number,a.receive_basis,
		b.batch_issue_qty,
		b.booking_dtls_id from inv_receive_mas_batchroll a, pro_grey_batch_dtls b where a.id=b.mst_id and b.booking_no=$txt_booking_no  and a.entry_form in(554,91) and a.status_active=1 $dtlsidCond and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		//and a.receive_basis=2
		//echo "issFinPrcess**select a.recv_number from inv_receive_mas_batchroll a, pro_grey_batch_dtls b where a.id=b.mst_id and b.booking_no=$txt_booking_no and a.receive_basis=2 and a.entry_form=91 and a.status_active=1 $dtlsidCond and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";die;
		$tot_batch_issue_qty=0;$issueTofinProcess_mrr="";
		foreach($sqlissueFinProcess as $rows){
			if($rows[csf('entry_form')]==91)
			{
			$issueTofinProcess_mrr.=$rows[csf('recv_number')].',';
			$tot_batch_issue_qtyArr[$rows[csf('booking_dtls_id')]]+=$rows[csf('batch_issue_qty')];
			}
			else
			{
			$issueTofinProcess_mrr.=$rows[csf('recv_number')].',';
			$tot_batch_issue_ret_qtyArr[$rows[csf('booking_dtls_id')]]+=$rows[csf('batch_issue_qty')];
			}
		}
		//print_r($tot_batch_issue_qtyArr);
		//echo "10**=A";die;
		//For Validation Check //-----issue Id=18501
			 for ($i=1;$i<=$row_num;$i++)
			 {
				 $po_id="po_id_".$i;
			//	 $tot_woqnty+=str_replace("'","",$$txt_woqnty);
				 $updateid="updateid_".$i;
				 $txt_woqnty="txt_woqnty_".$i;
				 $updateId=str_replace("'",'',$$updateid);
				 $woqnty_chk=str_replace("'",'',$$txt_woqnty);
				if(trim($updateId)!="")
				{
					  $tot_batch_issue_qty=$tot_batch_issue_qtyArr[$updateId];
					  $tot_batch_issue_ret_qty=$tot_batch_issue_ret_qtyArr[$updateId];
					  $tot_batch_issue_bal_qty=$tot_batch_issue_qty-$tot_batch_issue_ret_qty;
					  if($tot_batch_issue_bal_qty>0 && ($tot_batch_issue_bal_qty>$woqnty_chk)) // $tot_batch_issue_bal_qty>$woqnty_chk ||
					  {
						 $issueTofinProcess_noAll=rtrim($issueTofinProcess_mrr,',');
						  $issue_mrr_no=implode(",",array_unique(explode(",", $issueTofinProcess_noAll)));
						  $msg="You can revised up to issue qty.";
						  echo "issFinPrcess**".str_replace("'","",$txt_booking_no)."**".$issue_mrr_no.'**'.$msg.'**'.$tot_batch_issue_qty.'='.$tot_batch_issue_ret_qty.'='.$woqnty_chk;
							disconnect($con);die;
					  }
				}
			 }
			 //========End===Issue to Fin Process Found====================
		
		 if($db_type==0){
			mysql_query("BEGIN");
		 }

		 $field_array_up1="artwork_no*color_size_table_id*fabric_color_id*gmts_color_id*printing_color_id*aop_mc_type*aop_type*description*uom*wo_qnty*rate*amount*delivery_date*delivery_end_date*dia_width*fin_dia*updated_by*update_date";
		 $new_array_color=array();
		 for ($i=1;$i<=$row_num;$i++){
			 $job_no="job_no_".$i;
			 $po_id="po_id_".$i;
			 $fabric_description_id="fabric_description_id_".$i;
			 $color_size_table_id="color_size_table_id_".$i;
			 $dia="dia_".$i;
			 $artworkno="artworkno_".$i;			 
 			 $aop_mc_type="aop_mc_type_".$i;
 			 $aop_type="aop_type_".$i;
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
				 if (!in_array(str_replace("'","",$$item_color),$new_array_color)){
					  $color_id = return_id( str_replace("'","",$$item_color), $color_library, "lib_color", "id,color_name","162");
					  $new_array_color[$color_id]=str_replace("'","",$$item_color);
				 }
				 else $color_id =  array_search(str_replace("'","",$$item_color), $new_array_color);
			 }
			 else $color_id = 0;
			 
			 if(str_replace("'","",$$printing_color)!="")
			 {
				 if (!in_array(str_replace("'","",$$printing_color),$new_array_color))
				 {
					  $print_color_id = return_id( str_replace("'","",$$printing_color), $color_library, "lib_color", "id,color_name","162");
					  $new_array_color[$print_color_id]=str_replace("'","",$$printing_color);
				 }
				 else $print_color_id =  array_search(str_replace("'","",$$printing_color), $new_array_color);
			 }
			 else $print_color_id =0;

			if(str_replace("'",'',$$updateid)!=""){
			$id_arr[]=str_replace("'",'',$$updateid);
			$data_array_up1[str_replace("'",'',$$updateid)] =explode("*",("".$$artworkno."*".$$color_size_table_id."*".$color_id."*".$$gmts_color_id."*".$print_color_id."*".$$aop_mc_type."*".$$aop_type."*".$$fabric_description_id."*".$$uom."*".$$txt_woqnty."*".$$txt_rate."*".$$txt_amount."*".$$startdate."*".$$enddate."*".$$dia."*".$$findia."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
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
		else if($db_type==2 || $db_type==1 )
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
		$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no  and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
		if($pi_number){
			echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
			disconnect($con);die;
		}

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
		$recv_number=return_field_value( "a.recv_number as recv_number", "inv_receive_mas_batchroll a,pro_grey_batch_dtls b"," a.id=b.mst_id  and b.booking_no=$txt_booking_no and a.entry_form in(91,92) and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0","recv_number");
		if($recv_number){
			echo "recv_no**".str_replace("'","",$txt_booking_no)."**".$recv_number;
			disconnect($con);die;
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
		else if($db_type==2 || $db_type==1 )
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
	$color_library=return_library_array( "select id,color_name from lib_color where status_active =1 and is_deleted=0", "id", "color_name"  );
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$json_data=json_decode(str_replace("'","",$json_data));
	/*echo '10**<pre>';
	print_r($json_data); die;*/

	if(str_replace("'","",$txt_booking_no)!='')
	{
		$sql=sql_select("select embellishment_job, subcon_job from subcon_ord_mst where order_no=$txt_booking_no and status_active=1 and is_deleted=0");
		foreach($sql as $row){

			if($row[csf('embellishment_job')]=="") $row[csf('embellishment_job')]=$row[csf('subcon_job')];
			$lock_another_process=$row[csf('embellishment_job')];
		}
		if($lock_another_process!=''){
			//echo "lockAnotherProcess**".$lock_another_process;
			//disconnect($con);die;
		}
	}

	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		 $id_dtls=return_next_id( "id", "wo_booking_dtls", 1 ) ;
		 $field_array1="id, booking_mst_id, is_short, job_no, pre_cost_fabric_cost_dtls_id, entry_form_id, artwork_no,aop_mc_type,aop_type, po_break_down_id, booking_no, booking_type, fabric_color_id, gmts_color_id, printing_color_id, description, uom,process, wo_qnty, rate, amount, delivery_date, delivery_end_date, dia_width, fin_dia,inserted_by, insert_date, status_active, is_deleted";
		 //echo "10**";die;
		 $j=1;
		 $new_array_color=array();
		 for ($i=1;$i<=$row_num;$i++)
		 {
			 $job_no="job_no_".$i;
			 $po_ids="po_id_".$i;
			 $fabric_description_id="fabric_description_id_".$i;
			 $color_size_table_id="color_size_table_id_".$i;
			 $dia="dia_".$i;
			 $artworkno="artworkno_".$i;
			 $aop_mc_type="aop_mc_type_".$i;
			 $aop_type="aop_type_".$i;
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
			 $dia_wid=str_replace("'","",$$dia);//Do Not use Zero(0) Dia.
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
					  $new_array_color[$print_color_id]=str_replace("'","",$$printing_color);
				 }
				 else $print_color_id =  array_search(str_replace("'","",$$printing_color), $new_array_color);
			 }
			 else $print_color_id = 0;
			/*foreach($json_data as $key=>$precostdata)
			{
			 	foreach($precostdata as $precostid=>$color_data)
			 	{
			 		foreach($color_data as $keycolorid=>$dia_data)
			 		{
			 			foreach($dia_data as $dia_id=>$podata)
			 			{
			 				foreach($podata as $poId=>$val)
			 				{
			 					if($woq>0)
								{
									$wQty=($dia_data->req_qnty->$poId/$reqqnty)*$woq;
									$amount=$wQty*$rate;
									if ($j!=1) $data_array1 .=",";
									$data_array1 .="(".$id_dtls.",".$cbo_is_short.",".$$job_no.",".$$fabric_description_id.",".$$artworkno.",".$poId.",".$txt_booking_no.",3,".$color_id.",".$colorid.",".$print_color_id.",".$$fabric_description_id.",".$$uom.",35,".$wQty.",".$$txt_rate.",".$amount.",".$$startdate.",".$$enddate.",'".$dia_wid."',".$$findia.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
									$id_dtls=$id_dtls+1;
									$j++;
								}
			 				}
			 			}
			 		}
			 	}
			}*/
			foreach($json_data->$precostid->$colorid->$dia_wid->po_id as $poId)
			 {
				 if($woq>0)
				 {
					 $wQty=($json_data->$precostid->$colorid->$dia_wid->req_qnty->$poId/$reqqnty)*$woq;
					// echo  $wQty.'<br/>';
					 $amount=$wQty*$rate;
					 if ($j!=1) $data_array1 .=",";
					$data_array1 .="(".$id_dtls.",".$update_id.",".$cbo_is_short.",".$$job_no.",".$$fabric_description_id.",162,".$$artworkno.",".$$aop_mc_type.",".$$aop_type.",".$poId.",".$txt_booking_no.",3,".$color_id.",".$colorid.",".$print_color_id.",".$$fabric_description_id.",".$$uom.",35,".$wQty.",".$$txt_rate.",".$amount.",".$$startdate.",".$$enddate.",'".$dia_wid."',".$$findia.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					$id_dtls=$id_dtls+1;
					$j++;
				 }
			 }
		 }

		//echo "10**insert into wo_booking_dtls (".$field_array1.") values ".$data_array1;die;
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
		 else if($db_type==2 || $db_type==1 ){
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
		$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no  and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
		if($pi_number){
			//echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
			//disconnect($con);die;
		}

		$receive_mrr=0;
		$sqlre=sql_select("select recv_number from inv_receive_master where booking_no=$txt_booking_no  and status_active=1 and is_deleted=0");
		foreach($sqlre as $rows){
			$receive_mrr=$rows[csf('recv_number')];
		}
		if($receive_mrr){
			//echo "rec1**".str_replace("'","",$txt_booking_no)."**".$receive_mrr;
			//disconnect($con);die;
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
		$recv_number=return_field_value( "a.recv_number as recv_number", "inv_receive_mas_batchroll a,pro_grey_batch_dtls b"," a.id=b.mst_id  and b.booking_no=$txt_booking_no and a.entry_form in(91,92) and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0","recv_number");
		if($recv_number){
			echo "recv_no**".str_replace("'","",$txt_booking_no)."**".$recv_number;
			disconnect($con);die;
		}

		 if($db_type==0){
			mysql_query("BEGIN");
		 }
		//  print_r($json_data);
		//	echo "10**";
		 $field_array_up1="artwork_no*fabric_color_id*gmts_color_id*printing_color_id*aop_mc_type*aop_type*description*uom*wo_qnty*rate*amount*delivery_date*delivery_end_date*dia_width*fin_dia*updated_by*update_date";
		 $new_array_color=array();
		 for ($i=1;$i<=$row_num;$i++){
			 $job_no="job_no_".$i;
			 $po_id="po_id_".$i;
			 $fabric_description_id="fabric_description_id_".$i;
			 $color_size_table_id="color_size_table_id_".$i;
			 $dia="dia_".$i;
			 $artworkno="artworkno_".$i;			 
 			 $aop_mc_type="aop_mc_type_".$i;
 			 $aop_type="aop_type_".$i;
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
			 $dia_wid=str_replace("'","",$$dia);//Do Not use Zero(0) Dia.
             $reqqnty=str_replace("'","",$$txtreqnty);
			 $woq=str_replace("'","",$$txt_woqnty);
			 $rate=str_replace("'","",$$txt_rate);

		     $new_array_color=return_library_array( "select a.fabric_color_id,b.id,b.color_name from wo_booking_dtls a, lib_color b where b.id=a.fabric_color_id and a.pre_cost_fabric_cost_dtls_id=".$$fabric_description_id."", "id", "color_name");
			 
			 if(str_replace("'","",$$item_color)!="")
			 {
				 if (!in_array(str_replace("'","",$$item_color),$new_array_color)){
					  $color_id = return_id( str_replace("'","",$$item_color), $color_library, "lib_color", "id,color_name","162");
					  $new_array_color[$color_id]=str_replace("'","",$$item_color);
				 }
				 else $color_id =  array_search(str_replace("'","",$$item_color), $new_array_color);
			 }
			 else $color_id =0;
			 
			 if(str_replace("'","",$$printing_color)!="")
			 {
				 if (!in_array(str_replace("'","",$$printing_color),$new_array_color)){
					  $printing_color_id = return_id( str_replace("'","",$$printing_color), $color_library, "lib_color", "id,color_name","162");
					  $new_array_color[$printing_color_id]=str_replace("'","",$$printing_color);
				 }
				 else $printing_color_id =  array_search(str_replace("'","",$$printing_color), $new_array_color);
			 }
			 else $printing_color_id = 0;

			/* foreach($json_data as $key=>$precostdata)
			{
			 	foreach($precostdata as $precostid=>$color_data)
			 	{
			 		foreach($color_data as $keycolorid=>$dia_data)
			 		{
			 			foreach($dia_data as $dia_id=>$podata)
			 			{
			 				foreach($podata as $poId=>$val)
			 				{
			 					if($woq>0)
								{
									$wQty=($dia_data->req_qnty->$poId/$reqqnty)*$woq;
									$amount=$wQty*$rate;
									if ($j!=1) $data_array1 .=",";
									$data_array1 .="(".$id_dtls.",".$cbo_is_short.",".$$job_no.",".$$fabric_description_id.",".$$artworkno.",".$poId.",".$txt_booking_no.",3,".$color_id.",".$colorid.",".$print_color_id.",".$$fabric_description_id.",".$$uom.",35,".$wQty.",".$$txt_rate.",".$amount.",".$$startdate.",".$$enddate.",'".$dia_wid."',".$$findia.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
									$id_dtls=$id_dtls+1;
									$j++;
									if(str_replace("'",'',$$updateid)!=""){
										$id_arr[]=$poId;
										$data_array_up1[$poId] =explode("*",("".$$artworkno."*".$color_id."*".$colorid."*".$printing_color_id."*".$$fabric_description_id."*".$$uom."*".$wQty."*".$rate."*".$amount."*".$$startdate."*".$$enddate."*'".$dia_wid."'*".$$findia."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
									 }
								}
			 				}
			 			}
			 		}
			 	}
			}*/


			 foreach($json_data->$precostid->$colorid->$dia_wid->po_id as $poId){
				$wQty=($json_data->$precostid->$colorid->$dia_wid->req_qnty->$poId/$reqqnty)*$woq;
				 $amount=$wQty*$rate;
				 if(str_replace("'",'',$$updateid)!=""){
					$id_arr[]=str_replace("'",'',$json_data->$precostid->$colorid->$dia_wid->id->$poId);
					$data_array_up1[str_replace("'",'',$json_data->$precostid->$colorid->$dia_wid->id->$poId)] =explode("*",("".$$artworkno."*".$color_id."*".$colorid."*".$printing_color_id."*".$$aop_mc_type."*".$$aop_type."*".$$fabric_description_id."*".$$uom."*".$wQty."*".$rate."*".$amount."*".$$startdate."*".$$enddate."*'".$dia_wid."'*".$$findia."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				 }
			 }
		 }

		 //echo "10**".bulk_update_sql_statement( "wo_booking_dtls", "id", $field_array_up1, $data_array_up1, $id_arr ); die;
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
		else if($db_type==2 || $db_type==1 )
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
		$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no  and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
		if($pi_number){
			echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
			disconnect($con);die;
		}

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
		$recv_number=return_field_value( "a.recv_number as recv_number", "inv_receive_mas_batchroll a,pro_grey_batch_dtls b"," a.id=b.mst_id  and b.booking_no=$txt_booking_no and a.entry_form in(91,92) and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0","recv_number");
		if($recv_number){
			echo "recv_no**".str_replace("'","",$txt_booking_no)."**".$recv_number;
			disconnect($con);die;
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
		else if($db_type==2 || $db_type==1 )
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
	extract($_REQUEST);
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
            <table width="100%" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
                <thead>
                    <tr>
                        <th colspan="11"><? echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --" ); ?></th>
                    </tr>
                    <tr>
                        <th width="150">Company Name</th>
                        <th width="150">Buyer Name</th>
                        <th width="80">Booking No</th>
                        <th width="80">Job No</th>
                        <th width="80">File No</th>
                        <th width="80">Internal Ref.</th>
                        <th width="80">Style Ref </th>
                        <th width="80">Order No</th>
                        <th width="130" colspan="2">Booking Date Range</th>
                        <th><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">WO Without Item</th>
                    </tr>
                </thead>
                <tr class="general">
                    <td>
                        <input type="hidden" id="selected_booking">
                        <? echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company_id, "load_drop_down( 'service_booking_aop_urmi_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );",0); ?>
                    </td>
                    <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select Buyer --" ); ?></td>
                    <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px"></td>
                    <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"></td>
                    <td><input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('chk_job_wo_po').value, 'create_booking_search_list_view', 'search_div', 'service_booking_aop_urmi_controller','setFilterGrid(\'tbl_po_list\',-1)')" style="width:80px;" /></td>
                </tr>
                <tr>
                    <td align="center" valign="middle" colspan="11"><? echo load_month_buttons(1); ?></td>
                </tr>
            </table>
            <div id="search_div"></div>
        </form>
        </div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script type="text/javascript">
		$("#cbo_company_mst").val(<? echo $company?>);
		load_drop_down( 'service_booking_aop_urmi_controller', $("#cbo_company_mst").val(), 'load_drop_down_buyer', 'buyer_td' );
	</script>
	</html>
	<?
	exit();
}

if ($action=="service_booking_popup1")
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
                        <th colspan="6">
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
        			<tr class="general">
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
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('chk_job_wo_po').value, 'create_booking_search_list_view', 'search_div', 'service_booking_aop_urmi_controller','setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" /></td>
        		</tr>
                <tr><td align="center" colspan="6"><? echo load_month_buttons(1);  ?></td></tr>
             </table>
    </form>
    <div id="search_div"></div>
   </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action=="create_booking_search_list_view")
{
	//echo load_html_head_contents("Booking PopUp","../../../", 1, 1, $unicode,'','');
	$data=explode('_',$data);
	if ($data[0]!=0) $company="  a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_id='$data[1]'"; else $buyer="";

	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
		$year_cond=" and SUBSTRING_INDEX(b.insert_date, '-', 1)=$data[7]";
	}
	else if($db_type==2)
	{
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
		$year_cond=" and to_char(b.insert_date,'YYYY')=$data[7]";
	}

	if($db_type==0){$booking_year_cond=" and year(a.booking_date)=".$data[7]."";}
	else{$booking_year_cond=" and to_char(a.booking_date,'YYYY')=".$data[7]."";}




	if($data[6]==1){
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num='$data[5]'"; else  $booking_cond="";
		if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num='$data[4]'"; else $job_cond="";
		if (trim($data[10])!="") $style_cond=" and c.style_ref_no ='$data[10]'"; else $style_cond="";
		if (str_replace("'","",$data[11])!="") $order_cond=" and d.po_number = '$data[11]' "; else $order_cond="";
	}
	else if($data[6]==4 || $data[6]==0){
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[5]%'  "; else  $booking_cond="";
		if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num like '%$data[4]%' $year_cond "; else  $job_cond="";
		if (trim($data[10])!="") $style_cond=" and c.style_ref_no like '%$data[10]%'"; else $style_cond="";
		if (str_replace("'","",$data[11])!="") $order_cond=" and d.po_number like '%$data[11]%'"; else $order_cond="";
	}
	else if($data[6]==2)
	{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '$data[5]%'  "; else  $booking_cond="";
		if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num like '$data[4]%' $year_cond "; else  $job_cond="";
		if (trim($data[10])!="") $style_cond=" and c.style_ref_no like'$data[10]%'"; else $style_cond="";
		if (str_replace("'","",$data[11])!="") $order_cond=" and d.po_number like '$data[11]%'"; else $order_cond="";
	}
	else if($data[6]==3)
	{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[5]'"; else  $booking_cond="";
		if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num like '%$data[4]' $year_cond "; else  $job_cond="";
		if (trim($data[10])!="") $style_cond=" and c.style_ref_no like '%$data[10]'"; else $style_cond="";
		if (str_replace("'","",$data[11])!="") $order_cond=" and d.po_number like '%$data[11]'"; else $order_cond="";
	}

	$file_no = str_replace("'","",$data[8]);
	$internal_ref = str_replace("'","",$data[9]);
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and d.file_no='".trim($file_no)."' ";
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and d.grouping='".trim($internal_ref)."' ";

	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');

	$arr=array (2=>$comp,3=>$buyer_arr,8=>$item_category,9=>$suplier);
	if($data[12]==1)
	{
	    $sql= "select a.pay_mode, a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.supplier_id from wo_booking_mst a
	where $company $buyer $booking_date and a.booking_no not in( select a.booking_no from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c, wo_po_break_down d
	where $company $buyer $booking_date and a.id=b.booking_mst_id and b.job_no=c.job_no and c.job_no=d.job_no_mst and a.booking_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.process=35 $booking_cond $booking_year_cond $job_cond $style_cond $order_cond $file_no_cond $internal_ref_cond
	group by a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, c.job_no_prefix_num, b.po_break_down_id, a.item_category, a.fabric_source, a.supplier_id ) and a.booking_type=3 and  a.status_active=1 and a.is_deleted=0 and a.process=35 $booking_cond 
	group by a.pay_mode, a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.supplier_id order by a.id desc";
	}
	else if($data[12]==0)
	{
	    $sql= "SELECT a.pay_mode, a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, c.job_no_prefix_num, a.item_category, a.fabric_source, a.supplier_id, d.po_number, d.file_no, d.grouping from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c, wo_po_break_down d 	where $company $buyer $booking_date and  a.id=b.booking_mst_id and b.job_no=c.job_no and c.job_no=d.job_no_mst and d.id=b.po_break_down_id and a.booking_type=3 and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and a.process=35 $booking_cond  $booking_year_cond $job_cond $style_cond $order_cond $file_no_cond $internal_ref_cond group by a.pay_mode, a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, c.job_no_prefix_num, a.item_category, a.fabric_source, a.supplier_id, d.po_number, d.file_no, d.grouping order by a.id desc";
	}
	//echo $sql;
	$result=sql_select($sql);
	?>
	<div style="width:1000px;">
     	<table cellspacing="0" cellpadding="0" border="1" align="left" rules="all" width="980" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="60">Booking No</th>
                <th width="70">Booking Date</th>
                <th width="100">Company</th>
                <th width="100">Buyer</th>
                <th width="100">Job No</th>
                <th width="130">PO number</th>
                <th width="90">Internal Ref.</th>
                <th width="90">File</th>
                <th width="100">Fabric Nature</th>
                <th>Supplier</th>
            </thead>
     	</table>
    </div>
    <div style="width:1000px; max-height:240px;overflow-y:scroll;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="980" class="rpt_table" id="tbl_po_list">
	    <?
	    $i=1;
	    foreach($result as $row)
	    {
			if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	     	$supp=($row[csf("pay_mode")]==3 || $row[csf("pay_mode")]==5)? $comp[$row[csf("supplier_id")]]: $suplier[$row[csf("supplier_id")]];
	     	$booking_no="'".$row[csf("booking_no")]."'";
	     	?>
	     	<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<? echo  $row[csf("id")] ;?>);" >
				<td width="30" align="center"><?php echo $i; ?></td>
				<td width="60" align="center"><?php echo $row[csf("booking_no_prefix_num")]; ?></td>
				<td width="70"><?php echo change_date_format($row[csf("booking_date")]); ?></td>

				<td width="100" style="word-break:break-all"><?php echo $comp[$row[csf("company_id")]]; ?></td>
				<td width="100" style="word-break:break-all"><?php echo $buyer_arr[$row[csf("buyer_id")]]; ?></td>
                <td width="100" style="word-break:break-all"><?php echo $row[csf("job_no_prefix_num")]; ?></td>
                <td width="130" style="word-break:break-all"><?php echo $row[csf("po_number")]; ?></td>
                <td width="90" style="word-break:break-all"><?php echo $row[csf("grouping")]; ?></td>
                <td width="90" style="word-break:break-all"><?php echo $row[csf("file_no")]; ?></td>
                <td width="100" style="word-break:break-all"><?php echo $item_category[$row[csf("item_category")]]; ?></td>
                <td style="word-break:break-all"><?php echo $supp; ?></td>
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
if($action=="show_trim_booking_report4") 
{

	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	//$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library",'master_tble_id','image_location');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	//$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$color_library=return_library_array( "select id,color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name");
	$lib_body_part=return_library_array( "select id,body_part_full_name from lib_body_part  where status_active=1 and is_deleted=0", "id", "body_part_full_name");
	//$fabric_color_sql=sql_select("SELECT master_tble_id, image_location from common_photo_library where is_deleted=0 and file_type=1 ");
	// $fabric_ima_arr=return_library_array( "select master_tble_id,image_location from common_photo_library  where is_deleted=0 and file_type=1 and form_name='aop_v2' ", "master_tble_id", "image_location");
	$fabric_ima_lib=return_library_array( "select master_tble_id,image_location from common_photo_library  where is_deleted=0 and file_type=1 and form_name='aop_v2' ", "master_tble_id", "image_location");
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$trim_group= return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	
	$path=($path=='')?'../../':$path;

	// echo "<pre>";
	// print_r($fabric_ima_lib);
	foreach($fabric_ima_lib as $img_id=>$row){

			$img_id_arr=explode(",",$img_id);
			foreach($img_id_arr as $val){
				$fabric_ima_arr[$val]=$row;

			}
	}

	// echo "<pre>";
	// print_r($fabric_ima_arr);
	?>
	<div style="width:1150px" align="left">
       <table width="100%" cellpadding="0" cellspacing="0" style="border:0px solid black;margin:5px; font-size:16px; font-family:'Arial Narrow';" >
           <tr>
               <td width="100">
               <img  src='<? echo $path.$imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="">
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:20px;">
                              <?php
								echo $company_library[$cbo_company_name];
								?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px;">
                            <?
                            $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website,contact_no from lib_company where id=$cbo_company_name");
                            foreach ($nameArray as $result)
                            {
                            ?>
                                             <? echo $result[csf('plot_no')]; ?>
                                            <? echo $result[csf('level_no')]?>
                                            <? echo $result[csf('road_no')]; ?>
                                            <? echo $result[csf('block_no')];?>
                                            <? echo $result[csf('city')];?>
                                            <? echo $result[csf('zip_code')]; ?>
                                            <?php echo $result[csf('province')]; ?>
                                            <? echo $country_arr[$result[csf('country_id')]]; ?><br>
                                            Email Address: <? echo $result[csf('email')];?>
                                            Website No: <? echo $result[csf('website')];

									$email=$result[csf('email')];
									$contact_no=$result[csf('contact_no')];
									$website=$result[csf('website')];
									$city=$result[csf('city')];
									$road_no=$result[csf('road_no')];
									$block_no=$result[csf('block_no')];
									$com_add=$result[csf('plot_no')].' '.$result[csf('level_no')].' '.$result[csf('road_no')].' '.$result[csf('block_no')].' '.$city.' '.$result[csf('zip_code')];
                            }
                            ?>
                               </td>
                            </tr>
                            <tr>
                            <td align="center" style="font-size:20px">
                            <strong>Service Booking For AOP:<? echo str_replace("'","",$txt_booking_no); ?></strong>
                             </td>
                            </tr>
                      </table>
                </td>
            </tr>
       </table>
		<?
		$nameArray_job=sql_select(" SELECT b.id as po_id,b.po_number,a.dia_width,a.fabric_color_id,a.fin_gsm,a.gmts_color_id,a.fabric_color_id,a.job_no,a.description,a.program_no,a.lib_composition,a.delivery_date, a.delivery_end_date,a.pre_cost_fabric_cost_dtls_id as fab_dtls_id,a.sensitivity,a.rate,a.uom,a.amount,	a.wo_qnty, d.body_part_id, e.style_ref_no , e.id as job_id, d.fabric_description, d.color_type_id, d.id as fabric_id, d.lib_yarn_count_deter_id ,d.gsm_weight ,a.artwork_no,a.printing_color_id,a.id from wo_booking_dtls a join wo_po_break_down b on a.po_break_down_id=b.id join wo_pre_cost_fab_conv_cost_dtls c on c.id=a.pre_cost_fabric_cost_dtls_id join 
		wo_pre_cost_fabric_cost_dtls d on c.fabric_description=d.id join wo_po_details_master e on e.id=b.job_id where 
		a.booking_no=$txt_booking_no  and a.status_active=1 group by b.id ,b.po_number, a.dia_width, a.fabric_color_id, a.fin_gsm, a.gmts_color_id, a.fabric_color_id, a.job_no, a.description, a.program_no, a.lib_composition, a.delivery_date, a.delivery_end_date, a.pre_cost_fabric_cost_dtls_id , a.sensitivity, a.rate, a.uom, a.amount, a.wo_qnty, d.body_part_id, e.style_ref_no,e.id,d.fabric_description, d.color_type_id, d.id, d.lib_yarn_count_deter_id,d.gsm_weight,a.artwork_no,a.printing_color_id,a.id");
		
		$fabric_atribute_arr=array('body_part_id','fabric_description','dia_width','fin_gsm','color_type_id','gsm_weight');
		$fabric_color_attr=array('fabric_color_id','uom','rate','fabric_id','artwork_no','printing_color_id','id');
		$fabric_color_summary_attr=array('fabric_description');
		
		foreach($nameArray_job as $row){
	
				$job_po_arr[$row[csf('job_id')]][$row[csf('po_number')]]=$row[csf('po_number')];

			 $main_data_arr[$row[csf('job_id')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('dia_width')]][$row[csf('fabric_color_id')]]['wo_qnty']+=$row[csf('wo_qnty')];
			 $main_data_arr[$row[csf('job_id')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('dia_width')]][$row[csf('fabric_color_id')]]['artwork_no']=$row[csf('artwork_no')];		
			 $main_data_arr[$row[csf('job_id')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('dia_width')]][$row[csf('fabric_color_id')]]['printing_color_id']=$row[csf('printing_color_id')];
			 $main_data_arr[$row[csf('job_id')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('dia_width')]][$row[csf('fabric_color_id')]]['uom']=$row[csf('uom')];		
			 $main_data_arr[$row[csf('job_id')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('dia_width')]][$row[csf('fabric_color_id')]]['rate']=$row[csf('rate')];	
			 $main_data_arr[$row[csf('job_id')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('dia_width')]][$row[csf('fabric_color_id')]]['id']=$row[csf('id')];	

			 $main_data_arr[$row[csf('job_id')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('dia_width')]][$row[csf('fabric_color_id')]]['color_type_id']=$row[csf('color_type_id')];	
			 $main_data_arr[$row[csf('job_id')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('dia_width')]][$row[csf('fabric_color_id')]]['gsm_weight']=$row[csf('gsm_weight')];
			 $main_data_arr[$row[csf('job_id')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('dia_width')]][$row[csf('fabric_color_id')]]['job_no']=$row[csf('job_no')];	
			 $main_data_arr[$row[csf('job_id')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('dia_width')]][$row[csf('fabric_color_id')]]['style_ref']=$row[csf('style_ref_no')];	
			 $main_data_arr[$row[csf('job_id')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('dia_width')]][$row[csf('fabric_color_id')]]['amount']+=$row[csf('amount')];	
			
			 $fabric_color_summary[$row[csf('fabric_description')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('fabric_color_id')]]['color_type_id']=$row[csf('color_type_id')];
			 $fabric_color_summary[$row[csf('fabric_description')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('fabric_color_id')]]['wo_qnty']+=$row[csf('wo_qnty')];
			 $fabric_color_summary[$row[csf('fabric_description')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('fabric_color_id')]]['printing_color_id']=$row[csf('printing_color_id')];

				
			 			
		}

		// echo "<pre>";
		// print_r($fabric_color_summary);

	
		// 	echo "<pre>";
		// print_r($job_wise_rowspan);
		$suppliar_data=sql_select("SELECT id, contact_no, email,web_site, address_1, address_2, address_3, address_4 from lib_supplier where status_active=1 and is_deleted=0");
		foreach($suppliar_data as $row){
			$supplier_address_arr[$row[csf('id')]]['address']=$row[csf('address_1')].' '.$row[csf('address_2')].' '.$row[csf('address_3')].' '.$row[csf('address_4')];
			$supplier_address_arr[$row[csf('id')]]['contact']=$row[csf('contact_no')];
			$supplier_address_arr[$row[csf('id')]]['email']=$row[csf('email')];
			$supplier_address_arr[$row[csf('id')]]['website']=$row[csf('web_site')];
		}
        $nameArray=sql_select( "select a.booking_no,a.booking_date,a.pay_mode,a.remarks,a.buyer_id,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.source, a.delivery_to, a.attention, a.tenor from wo_booking_mst a where  a.booking_no=$txt_booking_no");
		

        foreach ($nameArray as $result)
        {
			$varcode_booking_no=$result[csf('booking_no')];
			$pay_mode=$result[csf('pay_mode')];$supplier_id=$result[csf('supplier_id')];
			$supp_address=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website,contact_no from lib_company where id=$supplier_id");
			if($pay_mode==5 || $pay_mode==3){

				$com_supp=$company_library[$supplier_id];
				// $suplier_address=$com_add.'<br> '.$email.'<br> '.$website;
				$suplier_address=$supp_address[0][csf('plot_no')]."-".$supp_address[0][csf('level_no')].",".$supp_address[0][csf('road_no')].",".$supp_address[0][csf('block_no')]."<br>".$supp_address[0][csf('city')].",".$supp_address[0][csf('zip_code')].",".$country_arr[$supp_address[0][csf('country_id')]]."<br>Email Address:".$supp_address[0][csf('email')]."<br>".$supp_address[0][csf('website')];
				
			}
			else{

				$com_supp=$supplier_name_arr[$supplier_id];
				$suplier_address=$supplier_address_arr[$supplier_id]['address'].'<br>TEL:'.$supplier_address_arr[$supplier_id]['contact'].'<br>Email:'.$supplier_address_arr[$supplier_id]['email'].'<br>'.$supplier_address_arr[$supplier_id]['website'];
			}
			$currency_id=$result[csf('currency_id')];

        ?>
		<div style="width:1150px;">
       	<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
            <tr>
                <th colspan="6" valign="top" align="center">Beneficiary</th>
				<th colspan="6" valign="top" align="center">Consignee</th>
            </tr>
			<tr>
                <td width="50%" style="font-size:16px" colspan="6" align="left">
				<strong><? echo $com_supp;?></strong><br>
				<? echo $suplier_address;?>
				</td>
				<td width="50%" style="font-size:16px" colspan="6" align="left">
				<strong><? echo $company_library[$cbo_company_name];?></strong><br>
				<? echo $com_add;?><br>			
				<? echo "TEL:".$contact_no;  ?><br>
				<? echo "Email:".$email;  ?><br>
				<? echo $website;  ?><br>
				</td>

			</tr>
			<tr>
				<th align="left">Issue Date</th>
				<td colspan="2">&nbsp;<? echo change_date_format($result[csf('booking_date')]);?></td>
				<th align="left">Delivery Date</th>
				<td colspan="2">&nbsp;<? echo change_date_format($result[csf('delivery_date')]);?></td>
				
				<th align="left">Buyer</th>
				<td colspan="2">&nbsp;<? echo $buyer_name_arr[$result[csf('buyer_id')]];?></td>
				<th align="left">Tenor</th>
				<td colspan="2">&nbsp;<? echo $result[csf('tenor')];?></td>
			</tr>
			<tr>
				<th align="left">Delivery Address</th>
				<td colspan="8">&nbsp;<? echo $result[csf('delivery_to')];?></td>
				<th align="left">Contact Person</th>
				<td colspan="2">&nbsp;<? echo $result[csf('attention')];?></td>
			</tr>
			<tr>
				<th align="left">Remarks</th>
				<td colspan="11">&nbsp;<? echo $result[csf('remarks')];?></td>
			</tr>			
        </table>
		<?
        }
        ?>
		<br>
		<br>
		<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
			<tr>
				<th width="100">Job No</th>
				<th width="100">Style Ref</th>
				<th width="100">PO NO</th>
				<th width="150">Body Part</th>
				<th width="250">Fab Description</th>
				<th width="100">Color Type</th>
				<th width="80">GSM</th>
				<th width="60">Fab Dia</th>
			
				<th width="160">Fab Color</th>
				<th width="160">Printing Color</th>
				<th width="60">Artwork No</th>
				<th width="60">Fab Design Image</th>
				<th width="60">Finish Fab Qty</th>
				<th width="60">UOM</th>
				<? if($show_comment==1) {?>
				<th width="60">Rate</th>
			
				<th width="100">Amount</th>
				<? }?>
			</tr>
			<?  

				foreach($main_data_arr as $job_id=>$job_data){
					$job_rowspan=0;
					foreach($job_data as $body_part_id=>$body_part_data){
						$body_rowspan=0;
						foreach($body_part_data as $desc_id=>$gsm_data){
							$desc_rowspan=0;
							foreach($gsm_data as $dia_id=>$dia_data){
								$dia_rowspan=0;
								foreach($dia_data as $color_id=>$row){

									$job_rowspan++;
									$body_rowspan++;
									$desc_rowspan++;
									$dia_rowspan++;

								}
								$job_id_arr[$job_id]=$job_rowspan;
								$body_id_arr[$job_id][$body_part_id]=$body_rowspan;
								$desc_id_arr[$job_id][$body_part_id][$desc_id]=$desc_rowspan;
								$dia_id_arr[$job_id][$body_part_id][$desc_id][$dia_id]=$dia_rowspan;
							}

						}
					}
				}


			foreach($main_data_arr as $job_id=>$job_data){
				$j=1;
				foreach($job_data as $body_part_id=>$body_part_data){
					$b=1;
					foreach($body_part_data as $desc_id=>$gsm_data){
						$fab=1;
						foreach($gsm_data as $dia_id=>$dia_data){
							$d=1;
							foreach($dia_data as $color_id=>$row){

								// print_r($job_po_arr);
					 ?>
					<tr>
						<?
						if($j==1){?>
						<td rowspan="<?=$job_id_arr[$job_id];?>"><?= $row['job_no']  ?></td>
						<td rowspan="<?=$job_id_arr[$job_id];?>"><?= $row['style_ref']  ?></td>
						<td rowspan="<?=$job_id_arr[$job_id];?>"><?= implode(", ", $job_po_arr[$job_id])  ?></td>
						<?}?>
						
						<? 
						if($b==1){?>
						<td rowspan="<?=$body_id_arr[$job_id][$body_part_id];?>"><?=$lib_body_part[$body_part_id];?></td>	
						
						<?}
							if($fab==1){
							?>

							<td rowspan="<?=$desc_id_arr[$job_id][$body_part_id][$desc_id];?>"><?= $desc_id  ?></td>							
							<td rowspan="<?=$desc_id_arr[$job_id][$body_part_id][$desc_id];?>"><?= $color_type[$row['color_type_id']]  ?></td>
							<td rowspan="<?=$desc_id_arr[$job_id][$body_part_id][$desc_id];?>"><?= $row['gsm_weight'];  ?></td>
							<?
							}
							
							if($d==1){
								?>								
							<td rowspan="<?=$dia_id_arr[$job_id][$body_part_id][$desc_id][$dia_id];?>"><?=$dia_id;  ?></td>
							<?}?>
							

							<td><?= $color_library[$color_id]  ?></td>
							<td><?= $color_library[$row['printing_color_id']]  ?></td>
							<td><?= $row['artwork_no']  ?></td>
							<td title="<?=$fabric_ima_arr[$row['id']]."/master id=".$row['id'];?>"><img  src='<? echo $path.$fabric_ima_arr[$row['id']]; ?>' height='50' width='110' /></td>
							<td align="right"><?= number_format($row['wo_qnty'],2)  ?></td>
							<td><?= $unit_of_measurement[$row['uom']]  ?></td>
							<? if($show_comment==1) {?>
							<td align="right"><?= $row['rate']  ?></td>
							
							<td align="right"><?= number_format($row['amount'],2)  ?></td>
							<? }?>
							<? 
							$colortr++;
							$color_wise_qty+=$row['wo_qnty'];
							$color_wise_amount+=$row['amount'];
							$color_wise_grand_qty+=$row['wo_qnty'];
							$color_wise_grand_amount+=$row['amount'];
							
						
							
							$j++;$d++;$fab++;$b++;$d++;
						
						?>						
					</tr>
				<? } 	}	}	}}

				if($currency_id==1){$paysa_sent="Paisa";} else if($currency_id==2){$paysa_sent="CENTS";}
			?>
			<tr>
				<th colspan="12" align="right">Grand Total</th>
				<th align="right"><?= number_format($color_wise_grand_qty,2) ?></th>
				<th></th>
				<? if($show_comment==1) {?>
				<th></th>
				
				<th align="right"><?= number_format($color_wise_grand_amount,2) ?></th>
				<? }?>
			</tr>
			<tr>
				<th colspan="10" align="right">Total Booking Amount (in word)</th>
				<th colspan="4" align="left"><? echo number_to_words(def_number_format($color_wise_grand_amount,2,""),$currency[$currency_id],$paysa_sent); ?></th>
			</tr>
		</table>
		<br><br>
		<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
			<tr><th colspan="7" align="center">Fab Description & Color Wise Summary</th></tr>
			<tr>
				<th>Fabrication</th>
				<th>Color Type</th>
				<th>GSM</th>
				<th>Dia</th>
				<th>Fab. Color</th>
				<th>Printing Color</th>
				<th>Fin. Fab. Qty</th>
			</tr>
			<? 
			
		
			foreach($fabric_color_summary as $desc_id=>$desc_data){
				$desc_row=0;
				foreach($desc_data as $gsm_id=>$gsm_data){
					foreach($gsm_data as $dia_id=>$dia_data){
						$dia_row=0;
						foreach($dia_data as $color_id=>$color_data){
							$desc_row++;
							$dia_row++;
						}
						$desc_id_arr[$desc_id]=$desc_row;
						$dia_id_arr[$desc_id][$dia_id]=$dia_row;
					}
				}
			}
					
			foreach($fabric_color_summary as $desc_id=>$desc_data){
				$tr=1;
				foreach($desc_data as $gsm_id=>$gsm_data){
					foreach($gsm_data as $dia_id=>$dia_data){
						$dtr=1;
						foreach($dia_data as $color_id=>$color_summ){
				?>
				<tr>
					<?
						if($tr==1){?>
					<td rowspan="<?=$desc_id_arr[$desc_id];?>"><?= $desc_id;  ?></td>
					<td rowspan="<?=$desc_id_arr[$desc_id];?>"><?= $color_type[$color_summ['color_type_id']]  ?></td>
					<td rowspan="<?=$desc_id_arr[$desc_id];?>"><?= $gsm_id  ?></td>
				
					<? 
						}
						if($dtr==1){
						?>
					<td rowspan="<?=$dia_id_arr[$desc_id][$dia_id];?>"><?= $dia_id  ?></td>
					<?
						}
					
						
					?>
						<td align="right"><?= $color_library[$color_id]  ?></td>
						<td align="right"><?= $color_library[$color_summ['printing_color_id']]  ?></td>
						<td align="right"><?= number_format($color_summ['wo_qnty'],2)  ?></td>
					<? 
					$colorsummtr++;$tr++;$dtr++;
					$color_wise_qty_summ+=$color_summ['wo_qnty'];
					
				?>
				</tr>
				
				<?
			}}}
			?>
			<tr>
					<th colspan="6" align="right">Fabric Total :</th>
					<th align="right"><?= number_format($color_wise_qty_summ,2)  ?></th>
				</tr><?
		}
			?>
		</table>
         

       	<table  width="100%" class="rpt_table" style="margin:5px;font-size:16px;font-family:'Arial Narrow';"   border="1" cellpadding="0" cellspacing="0">
          <tr>
          <td><? echo get_spacial_instruction($txt_booking_no); ?></td>
          </tr>
        </table>
		 <?
            echo signature_table(79, $cbo_company_name, "1113px");
         ?>
    </div>

	<?
}

if($action=="show_trim_booking_report5") // md mamun =>12-06-2022=>ISD:11977
{

	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	//$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library",'master_tble_id','image_location');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1 and master_tble_id='$cbo_company_name'",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	//$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$color_library=return_library_array( "select id,color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name");
	$lib_body_part=return_library_array( "select id,body_part_full_name from lib_body_part  where status_active=1 and is_deleted=0", "id", "body_part_full_name");
	//$fabric_color_sql=sql_select("SELECT master_tble_id, image_location from common_photo_library where is_deleted=0 and file_type=1 ");
	// $fabric_ima_arr=return_library_array( "select master_tble_id,image_location from common_photo_library  where is_deleted=0 and file_type=1 and form_name='aop_v2' ", "master_tble_id", "image_location");
	$fabric_ima_lib=return_library_array( "select master_tble_id,image_location from common_photo_library  where is_deleted=0 and file_type=1 and form_name='aop_v2' ", "master_tble_id", "image_location");
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$trim_group= return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand",'id','brand_name');
	$dealing_marchant=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
	$team_leader=return_library_array( "select id, team_leader_name from lib_marketing_team",'id','team_leader_name');

	
	$path=($path=='')?'../../../':$path;

	// echo "<pre>";
	// print_r($fabric_ima_lib);
	foreach($fabric_ima_lib as $img_id=>$row){

			$img_id_arr=explode(",",$img_id);
			foreach($img_id_arr as $val){
				$fabric_ima_arr[$val]=$row;

			}
	}

		//============================================================================================================================
		$nameArray_approved = sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=8 and  a.status_active =1 and a.is_deleted=0");
	list($nameArray_approved_row) = $nameArray_approved;
	$booking_grand_total = 0;
	$currency_id = "";
	$buyer_string = array();
	$style_owner = array();
	$job_no = array();
	$style_ref = array();
	$all_dealing_marcent = array();
	$season = array();
	$order_repeat_no = array();
	$po_id_arr = array();
	$booking_style_ref= array();
	$nameArray_buyer = sql_select("select  a.style_ref_no, a.job_no,a.order_uom, a.style_owner, a.buyer_name, a.dealing_marchant, a.season, a.season_matrix, a.season_buyer_wise, a.order_repeat_no, b.po_break_down_id,a.team_leader, a.factory_marchant,a.JOB_NO_PREFIX_NUM,a.brand_id from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no=$txt_booking_no and b.status_active =1 and b.is_deleted=0");
	foreach ($nameArray_buyer as $result_buy) {
		$buyer_string[$result_buy[csf('buyer_name')]] = $buyer_name_arr[$result_buy[csf('buyer_name')]];
		$style_owner[$result_buy[csf('job_no')]] = $company_library[$result_buy[csf('style_owner')]];
		$job_no[$result_buy[csf('job_no')]] = $result_buy[csf('job_no')];
		$job_num[$result_buy[csf('job_no')]] = $result_buy[csf('job_no')];
		$job_uom_no[$result_buy[csf('job_no')]] = $unit_of_measurement[$result_buy[csf('order_uom')]];
		$style_ref[$result_buy[csf('job_no')]] = $result_buy[csf('style_ref_no')];
		$all_dealing_marcent[$result_buy[csf('job_no')]] = $dealing_marchant[$result_buy[csf('dealing_marchant')]];

		$job_no_arr[$txt_booking_no][$result_buy[csf('job_no')]]=$result_buy[csf('job_no')];
		$job_prefix_arr[$txt_booking_no][$result_buy[csf('JOB_NO_PREFIX_NUM')]]=$result_buy[csf('JOB_NO_PREFIX_NUM')];
		$booking_style_ref[$txt_booking_no][$result_buy[csf('style_ref_no')]]=$result_buy[csf('style_ref_no')];
		$booking_brand_arr[$txt_booking_no][$brand_arr[$result_buy[csf('brand_id')]]]=$brand_arr[$result_buy[csf('brand_id')]];
		$dealing_merchant_list[$txt_booking_no][$result_buy[csf('dealing_marchant')]]=$dealing_marchant[$result_buy[csf('dealing_marchant')]];
		$factory_merchant_list[$txt_booking_no][$result_buy[csf('factory_marchant')]]=$deling_marcent_arr[$result_buy[csf('factory_marchant')]];
		$team_leader_list[$txt_booking_no][$result_buy[csf('team_leader')]]=$team_leader[$result_buy[csf('team_leader')]];


		$season_matrix = $result_buy[csf('season_matrix')];
		$season_buyer_wise = $result_buy[csf('season_buyer_wise')];
		if ($season_matrix != 0 && $season_buyer_wise == 0) {
			$season_matrix_con = $season_matrix;
		} else if ($season_buyer_wise != 0 && $season_matrix == 0) {
			$season_matrix_con = $season_buyer_wise;
		}
		$seasons_name .= $season_arr[$season_matrix_con] . ',';
		$order_rept_no .= $result_buy[csf('order_repeat_no')] . ',';
		$order_repeat_no[$result_buy[csf('order_repeat_no')]] = $result_buy[csf('order_repeat_no')];

		$po_id_arr[$result_buy[csf('po_break_down_id')]] = $result_buy[csf('po_break_down_id')];
	}
	$style_sting = implode(",", array_unique($style_ref));
	$job_no = implode(",", $job_no);
	$seasons_names = rtrim($seasons_name, ',');

	$seasons_names = implode(",", array_unique(explode(",", $seasons_names)));
	$poid_arr = array_unique($po_id_arr);

	$order_rept_no = rtrim($order_rept_no, ',');
	$order_rept_no = implode(",", array_unique(explode(",", $order_rept_no)));

	$po_no = array();
	$file_no = array();
	$ref_no = array();
	$po_quantity = array();
	$pub_shipment_date = '';
	$int_ref_no = '';
	$tot_po_quantity = 0;
	$po_idss = '';
	$nameArray_job = sql_select("select b.job_no_mst,b.id,b.pub_shipment_date, b.po_number,b.grouping, b.file_no, sum(b.po_quantity) as po_quantity  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no and  a.status_active =1 and a.is_deleted=0 group by b.job_no_mst,b.id,b.pub_shipment_date, b.po_number,b.grouping, b.file_no ");
	foreach ($nameArray_job as $result_job) {
		$po_no[$result_job[csf('job_no_mst')]][$result_job[csf('id')]] = $result_job[csf('po_number')];
		$file_no[$result_job[csf('id')]] = $result_job[csf('file_no')];
		$ref_no[$result_job[csf('id')]] = $result_job[csf('grouping')];
		$po_quantity[$result_job[csf('id')]] = $result_job[csf('po_quantity')];
		$job_ref_no[$result_job[csf('job_no_mst')]] .= $result_job[csf('grouping')] . ',';
		$po_no_arr[$result_job[csf('job_no_mst')]]['po_id'] .= $result_job[csf('id')] . ',';
		$pub_shipment_date .= $result_job[csf('pub_shipment_date')] . ',';
		$int_ref_no .= $result_job[csf('grouping')] . ',';
		if ($po_idss == '') $po_idss = $result_job[csf('id')];
		else $po_idss .= "," . $result_job[csf('id')];
		$job_nos .= "'" . $result_job[csf('job_no_mst')] . "'" . ',';
	}
	$job_nos = rtrim($job_nos, ",");
	$sql_job = sql_select("select b.job_no_mst,b.id as po_id, b.po_quantity as po_quantity  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and  a.status_active =1 and a.is_deleted=0 and  b.status_active =1 and b.is_deleted=0 and b.id in(" . $po_idss . ") ");
	foreach ($sql_job as $row) {
		$job_po_qty_arr[$row[csf('job_no_mst')]][$row[csf('po_id')]] += $row[csf('po_quantity')];
		$tot_po_quantity += $row[csf('po_quantity')];
	}

	
	$nameArray = sql_select("select a.booking_no, a.pay_mode,a.buyer_id, a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.source, a.remarks, a.revised_no,a.delivery_to from wo_booking_mst a where a.booking_no=$txt_booking_no and a.status_active =1 and a.is_deleted=0");
	foreach ($nameArray as $row) {
		$varcode_booking_no = $row[csf('booking_no')];
		$booking_date = $row[csf('booking_date')];
		$delivery_date = $row[csf('delivery_date')];
		$pay_mode_id = $row[csf('pay_mode')];
		$supplier_id = $row[csf('supplier_id')];
		$currency_id = $row[csf('currency_id')];
		$buyer_id = $row[csf('buyer_id')];
		$exchange_rate = $row[csf('exchange_rate')];
		$attention = $row[csf('attention')];
		$remarks = $row[csf('remarks')];
		$revised_no = $row[csf('revised_no')];
		$source_id = $row[csf('source')];
		$delivery_add= $row[csf('delivery_to')];
	}
	$jobNos = implode(",", array_unique(explode(",", $job_nos)));
	

	$nameArray_item = sql_select("select  a.pre_cost_fabric_cost_dtls_id,c.emb_name from wo_booking_dtls a, wo_pre_cost_embe_cost_dtls c  where a.pre_cost_fabric_cost_dtls_id=c.id and  a.booking_no=$txt_booking_no and  a.status_active =1 and a.is_deleted=0 and a.job_no in ( $jobNos )    group by a.pre_cost_fabric_cost_dtls_id,c.emb_name  order by c.emb_name ");
			$sub_embl="";
			$e=1;
			foreach ($nameArray_item as $result_item) {
				
				if($e==1){
				$sub_embl =$emblishment_name_array[$result_item[csf('emb_name')]];
				$e++;
				}else{
					$sub_embl .=",".$emblishment_name_array[$result_item[csf('emb_name')]];
				}


			}
			$internal_ref_arr=array();
		$po_dtls_data=sql_select("SELECT job_no_mst,grouping from wo_po_break_down where job_no_mst in ($jobNos) and status_active=1 and is_deleted=0");
			foreach ($po_dtls_data as $row) {
				$internal_ref_arr[$row[csf('job_no_mst')]]['grouping'][] =$row[csf('grouping')];
			}

		$mcurrency = "";
			$dcurrency = "";
			if ($currency_id == 1) {
				$mcurrency = 'Taka';
				$dcurrency = 'Paisa';
			}
			if ($currency_id == 2) {
				$mcurrency = 'USD';
				$dcurrency = 'CENTS';
			}
			if ($currency_id == 3) {
				$mcurrency = 'EURO';
				$dcurrency = 'CENTS';
			}

		//==========================================================================================================================

	// echo "<pre>";
	// print_r($fabric_ima_arr);
	$suppliar_data=sql_select("SELECT id, contact_no, email,web_site, address_1, address_2, address_3, address_4 from lib_supplier where status_active=1 and is_deleted=0");
	foreach($suppliar_data as $row){
		$supplier_address_arr[$row[csf('id')]]['address']=$row[csf('address_1')].' '.$row[csf('address_2')].' '.$row[csf('address_3')].' '.$row[csf('address_4')];
		$supplier_address_arr[$row[csf('id')]]['contact']=$row[csf('contact_no')];
		$supplier_address_arr[$row[csf('id')]]['email']=$row[csf('email')];
		$supplier_address_arr[$row[csf('id')]]['website']=$row[csf('web_site')];
	}
	?>
	<div style="width:1150px" align="left">
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
							<img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='40%' width='50%' />
						<? }
						?></td>		
					<td width="200px" colspan="2" align="center"><b>AUKO-TEX GROUP</b></td>		  
					<td  colspan="2" align="center"><b>M&M DEPARTMENT</b></td>
					<td  colspan="2" align="center"><b>Service Booking For AOP Work Order<hr>(CODE: MMD/M&M/DMF-09)</b></td>
					<td   align="center"><b>BOOKING DATE :<?php echo change_date_format($booking_date); ?></b> </td>		   
					</tr>
				</table>
				<table border="1" align="left" class="rpt_table container"  cellpadding="0" width="100%" cellspacing="0" rules="all" style="padding-bottom: 10px">
				<tr>
				   <td colspan="2" align="left"><b>Factory Name: <?=$company_library[$cbo_company_name];?></b> </td>				   
				   <td  colspan="2" align="left"><b>SUB: <? echo $sub_embl;;?>  Work Order of All Over Print</b></td>	
				   <td  colspan="2" align="left"><b>Booking No:</b></td>	
				   <td colspan="2"  align="left"><b><?=str_replace("'","",$txt_booking_no);?></b> </td>						   
				</tr>
				<tr>
				   <td colspan="8" align="left"><b>Head Office: </b>
				   House # 103, Northern Road, Baridhara DOHS, Dhaka. Tel: +88-02-8413580, Fax: +88-02-8413579
					 
					 </td>				   
				   					   
				</tr>
				<tr>
				   <td colspan="8" align="left"><b>Factory:</b> <?
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
				   <td width="120" align="left"> <b>To :</b> </td>
				
				   <td width="350" colspan="3" align="left"> <b><?
                    if($pay_mode_id==5 || $pay_mode_id==3){
                        echo $company_library[$supplier_id];
                    }
                    else{
                        echo $supplier_name_arr[$supplier_id];
                    }
                    ?></b></td>
			  	    <td width="100" colspan="2" align="left"> <b>Buyer’s Name:</b></td>	
				   <td width="200" colspan="2" align="left"><b><? echo $buyer_name_arr[$buyer_id]; ?></b></td>	
				 
				</tr>
				<tr>
				   <td width="100" align="left"><b>  Attn.  :</b></td>
				   <td width="150" colspan="3" align="left"><b><? echo $attention;     ?></b></td>
				   <td width="100" colspan="2" align="left"><b>Brand  :</b></td>	
				   <td width="200" colspan="2" align="left"><b><?=implode(",",$booking_brand_arr[$txt_booking_no]);?> </b></td>	
				 
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
                	<td align="left"><b>Delivery Date   :</b></td>	
				   <td align="left"><b><?= change_date_format($delivery_date,'dd-mm-yyyy','-') ;?></b></td>	
				   <td  colspan="5"  align="left"><b>Remarks : <?= $remarks ;?></b></td>
				</tr>
          	</table>

			
		<?
		
		$nameArray_job=sql_select(" SELECT b.id as po_id,b.po_number,a.dia_width,a.fabric_color_id,a.fin_gsm,a.gmts_color_id,a.fabric_color_id,a.job_no,a.description,a.program_no,a.lib_composition,a.delivery_date, a.delivery_end_date,a.pre_cost_fabric_cost_dtls_id as fab_dtls_id,a.sensitivity,a.rate,a.uom,a.amount,	a.wo_qnty,b.grouping, d.body_part_id, e.style_ref_no , e.id as job_id, d.fabric_description, d.color_type_id, d.id as fabric_id, d.lib_yarn_count_deter_id ,d.gsm_weight ,a.artwork_no,a.printing_color_id,a.id,a.aop_mc_type,a.aop_type  from wo_booking_dtls a join wo_po_break_down b on a.po_break_down_id=b.id join wo_pre_cost_fab_conv_cost_dtls c on c.id=a.pre_cost_fabric_cost_dtls_id join 
		wo_pre_cost_fabric_cost_dtls d on c.fabric_description=d.id join wo_po_details_master e on e.id=b.job_id where 
		a.booking_no=$txt_booking_no  and a.status_active=1 group by b.id ,b.po_number, a.dia_width, a.fabric_color_id, a.fin_gsm, a.gmts_color_id, a.fabric_color_id, a.job_no, a.description, a.program_no, a.lib_composition, a.delivery_date, a.delivery_end_date, a.pre_cost_fabric_cost_dtls_id , a.sensitivity, a.rate, a.uom, a.amount, a.wo_qnty,b.grouping, d.body_part_id, e.style_ref_no,e.id,d.fabric_description, d.color_type_id, d.id, d.lib_yarn_count_deter_id,d.gsm_weight,a.artwork_no,a.printing_color_id,a.id,a.aop_mc_type,a.aop_type ");
		
		$fabric_atribute_arr=array('body_part_id','fabric_description','dia_width','fin_gsm','color_type_id','gsm_weight');
		$fabric_color_attr=array('fabric_color_id','uom','rate','fabric_id','artwork_no','printing_color_id','id');
		$fabric_color_summary_attr=array('fabric_description');
		
		foreach($nameArray_job as $row){
	
				$job_po_arr[$row[csf('job_id')]][$row[csf('po_number')]]=$row[csf('po_number')];

			 $main_data_arr[$row[csf('job_id')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('dia_width')]][$row[csf('printing_color_id')]]['wo_qnty']+=$row[csf('wo_qnty')];
			 $main_data_arr[$row[csf('job_id')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('dia_width')]][$row[csf('printing_color_id')]]['artwork_no']=$row[csf('artwork_no')];		
			 $main_data_arr[$row[csf('job_id')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('dia_width')]][$row[csf('printing_color_id')]]['fabric_color_id']=$row[csf('fabric_color_id')];
			 $main_data_arr[$row[csf('job_id')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('dia_width')]][$row[csf('printing_color_id')]]['uom']=$row[csf('uom')];		
			 $main_data_arr[$row[csf('job_id')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('dia_width')]][$row[csf('printing_color_id')]]['rate']=$row[csf('rate')];	
			 $main_data_arr[$row[csf('job_id')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('dia_width')]][$row[csf('printing_color_id')]]['id']=$row[csf('id')];	

			 $main_data_arr[$row[csf('job_id')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('dia_width')]][$row[csf('printing_color_id')]]['color_type_id']=$row[csf('color_type_id')];	
			 $main_data_arr[$row[csf('job_id')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('dia_width')]][$row[csf('printing_color_id')]]['gsm_weight']=$row[csf('gsm_weight')];
			 $main_data_arr[$row[csf('job_id')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('dia_width')]][$row[csf('printing_color_id')]]['job_no']=$row[csf('job_no')];	
			 $main_data_arr[$row[csf('job_id')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('dia_width')]][$row[csf('printing_color_id')]]['grouping']=$row[csf('grouping')];	
			 $main_data_arr[$row[csf('job_id')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('dia_width')]][$row[csf('printing_color_id')]]['style_ref']=$row[csf('style_ref_no')];	
			 $main_data_arr[$row[csf('job_id')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('dia_width')]][$row[csf('printing_color_id')]]['amount']+=$row[csf('amount')];	

			 $main_data_arr[$row[csf('job_id')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('dia_width')]][$row[csf('printing_color_id')]]['aop_mc_type']=$row[csf('aop_mc_type')];	
			 $main_data_arr[$row[csf('job_id')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('dia_width')]][$row[csf('printing_color_id')]]['aop_type']=$row[csf('aop_type')];	
			

				
			 			
		}

		// echo "<pre>";
		// print_r($fabric_color_summary);

	
		// 	echo "<pre>";
		// print_r($job_wise_rowspan);
	
		?>
		<br>
		<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
			<tr>
				<th width="100">Job No/Int Ref</th>
				<th width="100">Style Ref</th>
				<th width="100">PO NO</th>
				<th width="150">Body Part</th>
				<th width="250">Fab Description</th>
				<th width="100">Color Type</th>
				<th width="80">GSM</th>
				<th width="60">Fab Dia</th>
				<th width="160">Printing Color</th>
				<th width="160">Fab Color</th>
				
				<th width="60">Artwork No</th>
				<th width="60">Fab Design Image</th>
				<th width="80">AOP M/C Type</th>
				<th width="80">AOP Type</th>
				<th width="60">Finish Fab Qty</th>
				<th width="60">UOM</th>
				<th width="60">Rate</th>
				<th width="100">Amount</th>

			</tr>
			<?  

				foreach($main_data_arr as $job_id=>$job_data){
					$job_rowspan=0;
					foreach($job_data as $body_part_id=>$body_part_data){
						$body_rowspan=0;
						foreach($body_part_data as $desc_id=>$gsm_data){
							$desc_rowspan=0;
							foreach($gsm_data as $dia_id=>$dia_data){
								$dia_rowspan=0;
								foreach($dia_data as $color_id=>$row){

									$job_rowspan++;
									$body_rowspan++;
									$desc_rowspan++;
									$dia_rowspan++;

								}
								$job_id_arr[$job_id]=$job_rowspan;
								$body_id_arr[$job_id][$body_part_id]=$body_rowspan;
								$desc_id_arr[$job_id][$body_part_id][$desc_id]=$desc_rowspan;
								$dia_id_arr[$job_id][$body_part_id][$desc_id][$dia_id]=$dia_rowspan;
							}

						}
					}
				}


			foreach($main_data_arr as $job_id=>$job_data){
				$j=1;
				foreach($job_data as $body_part_id=>$body_part_data){
					$b=1;
					foreach($body_part_data as $desc_id=>$gsm_data){
						$fab=1;
						foreach($gsm_data as $dia_id=>$dia_data){
							$d=1;
							foreach($dia_data as $color_id=>$row){

								// print_r($job_po_arr);
					 ?>
					<tr>
						<?
						if($j==1){?>
						<td rowspan="<?=$job_id_arr[$job_id];?>"><?= $row['job_no']  ?>/<br><? echo implode(",", array_unique($internal_ref_arr[$row['job_no']]['grouping']))?></td>
						<td rowspan="<?=$job_id_arr[$job_id];?>"><?= $row['style_ref']  ?></td>
						<td rowspan="<?=$job_id_arr[$job_id];?>"><?= implode(", ", $job_po_arr[$job_id])  ?></td>
						<?}?>
						
						<? 
						if($b==1){?>
						<td rowspan="<?=$body_id_arr[$job_id][$body_part_id];?>"><?=$lib_body_part[$body_part_id];?></td>	
						
						<?}
							if($fab==1){
							?>

							<td rowspan="<?=$desc_id_arr[$job_id][$body_part_id][$desc_id];?>"><?= $desc_id  ?></td>							
							<td rowspan="<?=$desc_id_arr[$job_id][$body_part_id][$desc_id];?>"><?= $color_type[$row['color_type_id']]  ?></td>
							<td rowspan="<?=$desc_id_arr[$job_id][$body_part_id][$desc_id];?>"><?= $row['gsm_weight'];  ?></td>
							<?
							}
							
							if($d==1){
								?>								
							<td rowspan="<?=$dia_id_arr[$job_id][$body_part_id][$desc_id][$dia_id];?>"><?=$dia_id;  ?></td>
							<?}?>
							

							<td><?= $color_library[$color_id]  ?></td>
							<td><?= $color_library[$row['fabric_color_id']]  ?></td>
							<td><?= $row['artwork_no']  ?></td>
							<td title="<?=$fabric_ima_arr[$row['id']]."/master id=".$row['id'];?>"><img  src='<? echo $path.$fabric_ima_arr[$row['id']]; ?>' height='50' width='110' /></td>
							<td align="right"><?=$aop_mc_typeArr[$row['aop_mc_type']];  ?></td>
							<td align="right"><?=$print_type[$row['aop_type']];  ?></td>
							<td align="right"><?= number_format($row['wo_qnty'],2)  ?></td>
							<td><?= $unit_of_measurement[$row['uom']]  ?></td>
							<td align="right"><?= $row['rate']  ?></td>
							<td align="right"><?= number_format($row['amount'],2)  ?></td>
							
							<? 
							$colortr++;
							$color_wise_qty+=$row['wo_qnty'];
							$color_wise_amount+=$row['amount'];
							$color_wise_grand_qty+=$row['wo_qnty'];
							$color_wise_grand_amount+=$row['amount'];
							
						
							
							$j++;$d++;$fab++;$b++;$d++;
						
						?>						
					</tr>
				<? } 	}	}	}}

				if($currency_id==1){$paysa_sent="Paisa";} else if($currency_id==2){$paysa_sent="CENTS";}
			?>
			<tr>
				<th colspan="14" align="right">Grand Total</th>
				<th align="right"><?= number_format($color_wise_grand_qty,2) ?></th>
				<th></th>
				<th></th>
				<th align="right"><?= number_format($color_wise_grand_amount,2) ?></th>
			</tr>
			<tr>
				<th colspan="10" align="right">Total Booking Amount (in word)</th>
				<th colspan="4" align="left"><? echo number_to_words(def_number_format($color_wise_grand_amount,2,""),$currency[$currency_id],$paysa_sent); ?></th>
			</tr>
		</table>
		<br><br>
	
         

       	<table  width="100%" class="rpt_table" style="margin:5px;font-size:16px;font-family:'Arial Narrow';"   border="0" cellpadding="0" cellspacing="0">
          <tr>
          <td>
			<?
		
		//echo "select id, terms from  wo_booking_terms_condition where   booking_no='" . str_replace("'", "", $mst_id) . "' $entry_form_con   order by id";
		$data_array = sql_select("select id, terms from  wo_booking_terms_condition where   booking_no=$txt_booking_no    order by id asc");
		$tot_row=count($data_array)/2;
		//echo $tot_row;
		$k=1;
		foreach($data_array as $row)
		{
			if($k<=$tot_row)
			{
			$term_bookingArr[$row[csf('id')]]['terms']=$row[csf('terms')];
			}
			else
				{
				$other_term_bookingArr[$row[csf('id')]]['terms']=$row[csf('terms')];	
				}
				$k++;
			}
			
			if (count($data_array) > 0) {
				?>
				<table align="left"  width="<?=$width;?>" align="center"   border="0" cellpadding="0" cellspacing="0" >
				<tr>
				<td valign="top">
				
				<table   width="573" class="rpt_table"   align="center"  border="1" cellpadding="0" cellspacing="0" rules="all">
				<thead>
					<tr style="border:1px solid black;">
					<th width="3%" style="border:1px solid black;">Sl</th>
					<th width="45%" style="border:1px solid black;">Special Instruction</th>
					</tr>
				</thead>
				<tbody>
				<?
				
					//print_r($term_bookingArr);
				$sl=1;
						foreach ($term_bookingArr as $term=>$row) {
							?>
							<tr id="settr_1" align="" style="border:1px solid black;">
							<td align="center" style="border:1px solid black;text-align:center"><?=$sl;?></td>
						   <td style="border:1px solid black; font-weight:bold"><?=$row['terms'];?></td>
							<?
							$sl++;
							}
						
				?>
			</tbody>
			</table>
			</td>
			<!--1st part end-->
			<?
			$sl2=$sl;
			if (count($other_term_bookingArr) > 0) {
			?>
				<td valign="top">
					<table  width="573" class="rpt_table"   align="center"  border="1" cellpadding="0" cellspacing="0" rules="all">
				<thead>
					<tr style="border:1px solid black;">
					<th width="3%" style="border:1px solid black;" >Sl</th>
					<th width="45%" style="border:1px solid black;">Special Instruction</th>
					</tr>
				</thead>
				<tbody>
				<?
						foreach ($other_term_bookingArr as $term2=>$row2) {
							?>
							<tr id="settr_2" align="" style="border:1px solid black;">
							<td align="center" style="border:1px solid black; text-align:center"><?=$sl2;?></td>
						   <td style="border:1px solid black; font-weight:bold"><?=$row2['terms'];?></td>
							<?
							$sl2++;
							}
						
				?>
			</tbody>
			</table>
			
				</td> 
				<?
			}
				?>   
			</tr>
			</table>
			<?
		}
			?>	

		
		</td>
          </tr>
        </table>
		 <?
            echo signature_table(79, $cbo_company_name, "1113px");
         ?>
    </div>

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
	$color_library=return_library_array( "select id,color_name from lib_color where status_active =1 and is_deleted=0", "id", "color_name"  );
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
                                            <?php echo $result[csf('province')]; ?>
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
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from  lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from lib_supplier",'id','address_1');
	$company_address_arr=return_library_array( "select id,city from lib_company",'id','city');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$deling_marcent_arr=return_library_array("select id,team_member_name from lib_mkt_team_member_info","id","team_member_name");
	$season_arr=return_library_array("select id,season_name from lib_buyer_season where status_active=1","id","season_name");
	$color_library=return_library_array( "select id,color_name from lib_color where status_active =1 and is_deleted=0", "id", "color_name"  );
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$comp_short=return_library_array( "select id, company_name from lib_company",'id','company_name');

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

                            $nameArray=sql_select( "select plot_no,level_no,bin_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
                            foreach ($nameArray as $result)
                            {
                            ?>
                                <? echo $result[csf('plot_no')]; ?>
                                <? echo $result[csf('level_no')]?>
                                <? echo $result[csf('road_no')]; ?>
                                <? echo $result[csf('block_no')];?>
                               <? echo $result[csf('city')];?>
                                <? echo $result[csf('zip_code')]; ?>
                                <?php echo $result[csf('province')]; ?>
                                <? echo $country_arr[$result[csf('country_id')]]; ?><br>
                                <? echo $result[csf('email')];?>
                                <? echo $result[csf('website')];
								if($result[csf('bin_no')]!='') echo "<br> BIN: ".$result[csf('bin_no')];

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
			$job_nos.="'".$result_buy[csf('job_no')]."',";

			//$season[$result_buy[csf('job_no')]]=$season_arr[$result_buy[csf('season_matrix')]];
		}
		$job_nos=rtrim($job_nos,',');
		$season_names=rtrim($season_names,',');
		//echo $season_names.'dsd';
		$po_no=array();
		$job_no_arr=array();
		$file_no=array();
		$ref_no=array();
		$po_quantity=array();
		$nameArray_job=sql_select( "select b.id, b.po_number, b.job_no_mst as job_no, b.grouping, b.file_no, b.po_quantity  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0");
        foreach ($nameArray_job as $result_job){
			$po_no[$result_job[csf('id')]]=$result_job[csf('po_number')];
			$job_no_arr[$result_job[csf('id')]]=$result_job[csf('job_no')];
			$file_no[$result_job[csf('id')]]=$result_job[csf('file_no')];
			$ref_no[$result_job[csf('id')]]=$result_job[csf('grouping')];
			$po_quantity[$result_job[csf('id')]]=$result_job[csf('po_quantity')];
		}

        // 
		$txt_fab_booking=str_replace("'","",$txt_fab_booking);
		if($txt_fab_booking !==''){
			
			$sql_2="SELECT a.pay_mode, a.company_id,a.tagged_booking_no, a.booking_no, a.booking_date, a.supplier_id, a.process, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.source, a.is_short,a.entry_form,a.booking_type
			from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c, wo_po_break_down d
			where a.company_id='6' and a.booking_no=b.booking_no and b.job_no=c.job_no and c.job_no=d.job_no_mst and d.id=b.po_break_down_id and a.booking_type in (1,3,4) and  a.booking_no='$txt_fab_booking'
			and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
			group by a.pay_mode, a.company_id,a.tagged_booking_no, a.booking_no, a.booking_date, a.supplier_id, a.process, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.source, a.is_short,a.entry_form,a.booking_type";

		}

		  $fab_booking_arr=sql_select($sql_2);
		$sql="SELECT a.pay_mode, a.company_id,a.tagged_booking_no, a.booking_no, a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.source, a.is_short,a.entry_form,a.booking_type  from wo_booking_mst a where  a.booking_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0";
		
		//echo  $sql_2;

	    $nameArray=sql_select($sql);
        //$nameArray=sql_select( "select a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.source, a.is_short  from wo_booking_mst a where  a.booking_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0");
	    // SELECT a.pay_mode, a.company_id, a.booking_no, a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.source, a.is_short  from wo_booking_mst a where  a.booking_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0 group by a.pay_mode, a.company_id, a.booking_no, a.booking_no, a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.source, a.is_short
        foreach ($nameArray as $result){
        	if($result[csf("pay_mode")]==1 || $result[csf("pay_mode")]==2){
				$supp=$supplier_name_arr[$result[csf("supplier_id")]];
				$supp_adds=$supplier_address_arr[$result[csf("supplier_id")]];
			}else{
				$supp=$comp_short[$result[csf("supplier_id")]];
				$supp_adds=$company_address_arr[$result[csf("supplier_id")]];
			}
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
                <!-- <td  width="100" style="font-size:12px"><b>Conversion Rate</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('exchange_rate')]; ?></td> -->

            </tr>
             <tr>
                <td  width="100" style="font-size:12px"><b>Source</b></td>
                <td  width="110" >:&nbsp;<? echo $source[$result[csf('source')]]; ?></td>
                <td width="100" style="font-size:12px"><b>Supplier Name</b>   </td>
                <td width="110">:&nbsp;<? echo $supp; ?></td>
            </tr>
             <tr>
                <td width="100" style="font-size:12px"><b>Supplier Address</b></td>
               	<td width="110">:&nbsp;
               	<?
               		echo $supp_adds;
               	?></td>
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
                <td>Booking Type :  <? 
						foreach ($fab_booking_arr as $results){
							
								if($results[csf('booking_type')]==1 && $results[csf('entry_form')]==108){ 
									echo "Partial";}
								elseif($results[csf('is_short')]==1 && $results[csf('booking_type')]==1 && $results[csf('entry_form')]==88){
									echo "Short "; 
								}elseif($results[csf('booking_type')]==1 && $results[csf('entry_form')]==118){
									echo "Main  "; 
								}elseif($results[csf('booking_type')]==4 && $results[csf('is_short')]==2){
									echo "Sample ";
								}
						}
				
				?></td>
            </tr>
            <tr>
                <td style="font-size:12px"><b>PO No</b> </td>

                 <td style="font-size:12px;overflow:hidden;text-overflow: ellipsis;white-space: nowrap;" colspan="2">:&nbsp;<b><? echo implode(",",array_unique($po_no));  ?></b></td>
                 <td style="font-size:12px;overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"><b>Internal Ref:&nbsp;<? echo implode(",",array_unique($ref_no));  ?></b></td>
                 <td>Fabric Booking :  <?=$result[csf('tagged_booking_no')]; ?></td>
            </tr>
            <tr>
            	<td>Pay Mode</td>
                <td>:&nbsp; <?=$pay_mode[$result[csf("pay_mode")]]; ?></td>
            	<td style="font-size:12px;overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"><b>File No:&nbsp;<? echo implode(",",array_unique($file_no));  ?></b></td>
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
				$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description,gsm_weight,uom from  wo_pre_cost_fabric_cost_dtls  where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."'");
				list($fabric_description_row)=$fabric_description;

				$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")].', '.$fabric_description_row[csf("gsm_weight")];
					$fabric_description_uom_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$unit_of_measurement[$fabric_description_row[csf("uom")]];
			}
			/*if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]==0){
				$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description,gsm_weight from  wo_pre_cost_fabric_cost_dtls  where  job_no='".rtrim($job_no,", ")."'");
				foreach( $fabric_description as $fabric_description_row){
					$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]].=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")].', '.$fabric_description_row[csf("gsm_weight")];
				}
			}*/
		}
	//=================================================

	$sql_article="SELECT po_break_down_id,color_number_id,size_mst_id,article_number
				  FROM WO_PO_COLOR_SIZE_BREAKDOWN
				 WHERE is_deleted = 0 
				 AND JOB_NO_MST in(".implode(",",$job_no_in).")";
	//echo $sql_article;		
	$result_article=sql_select($sql_article);

	$article_data=array();
	foreach ($result_article as $row) 
	{
		$article_data[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]].=$row[csf('article_number')].",";
	}

				


	if($db_type==0) $group_concat="group_concat( distinct id,',') AS booking_dtls_id";
	else if($db_type==2)  $group_concat="listagg(cast(id as varchar2(4000)),',') within group (order by id) AS booking_dtls_id";
				$nameArray_item_imge =sql_select("SELECT master_tble_id,image_location,real_file_name FROM common_photo_library where form_name='aop_v2'  and file_type=1");
			 foreach($nameArray_item_imge as $row)
                {
					$ids=explode(",", $row[csf('master_tble_id')]);
                	foreach ($ids as $key => $value) 
                	{
                		$item_img_arr[$value]=$row[csf('image_location')];
                	}
				}

				
        $nameArray_item=sql_select( "select distinct description from wo_booking_dtls  where booking_no=$txt_booking_no and wo_qnty !=0   and status_active=1 and is_deleted=0 and process=35 and status_active=1 and is_deleted=0 ");
        $nameArray_color=sql_select( "select distinct fabric_color_id,printing_color_id from wo_booking_dtls   where  booking_no=$txt_booking_no and wo_qnty !=0  and status_active=1 and is_deleted=0 and process=35 and status_active=1 and is_deleted=0 ");
		//$k=1;//$tot_row=count($nameArray_color);
       if(count($nameArray_color)>0){
	   foreach($nameArray_item as $result_item){
        ?>

        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="11" align="">
                <strong><? echo "Fabrication:".rtrim($fabric_description_array[$result_item[csf('description')]],", ");
				$fab_uom=$fabric_description_uom_array[$result_item[csf('description')]];
				 ?> </strong><br/>
                </td>
            </tr>
            <tr>
               
                <td style="border:1px solid black"><strong>GMT Color</strong> </td>
                <td style="border:1px solid black"><strong>Article No</strong> </td>
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <td style="border:1px solid black"><strong>Print Color</strong> </td>
                <td style="border:1px solid black" align="center"><strong>Wo Qty (<? echo $fab_uom;?>)</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin Dia</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
                 <td style="border:1px solid black"><strong>Image</strong> </td>
            </tr>
            <?
			
			
			 $total_amount_as_per_gmts_color=0;
			 $total_qty_as_per_gmts_color=0;
            $nameArray_item_description=sql_select( "select  $group_concat,fabric_color_id,gmts_color_id,printing_color_id,description,rate,dia_width,po_break_down_id,sum(wo_qnty) as cons from wo_booking_dtls  where booking_no=$txt_booking_no  and process=35 and description='".$result_item[csf('description')]."' and wo_qnty !=0 and status_active=1 and is_deleted=0  group by fabric_color_id,gmts_color_id,printing_color_id,description,rate,dia_width,po_break_down_id");
			$k=1;$tot_row=count($nameArray_item_description);
                foreach($nameArray_item_description as $result_itemdescription)
                {
					$booking_dtls_ids=array_unique(explode(",",$result_itemdescription[csf('booking_dtls_id')]));
					
								
								
			                ?>
			            <tr>
			           
					                <td style="border:1px solid black"><? echo $color_library[$result_itemdescription[csf('gmts_color_id')]]; ?>  </td>

					                <?php 

					                	$articles=explode(",", rtrim($article_data[$result_itemdescription[csf('po_break_down_id')]][$result_itemdescription[csf('gmts_color_id')]],","));
					                	$articles=array_unique($articles);
					                	$article_text=implode($articles, ",");

					                 ?>

					                <td style="border:1px solid black"><? echo $article_text; ?>  </td>

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
					                 <?
					           
									?>
					                <td  style="border:1px solid black">
					                <?
					               		 foreach($booking_dtls_ids as $bid)
										{
											$item_img=$item_img_arr[$bid];
												 
							                ?>
							                <img src="../../<? echo $item_img; ?>"  width="90px" height="80px" border="0" /><? //echo $item_img; ?>  
							                <?
										}
									?>
					                </td>
					                <?
								
								$k++;
									?>
			            </tr>
							<?
                }
                ?>
            <tr>
            <td colspan="4" align="right"> <strong> Total Qty (<? echo $fab_uom;?>) </strong></td>
            <td align="right"><? echo number_format($total_qty_as_per_gmts_color,2); ?> </td>

                <td style="border:1px solid black;  text-align:right" colspan="2"><strong>   Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right">
                <?
                echo number_format($total_amount_as_per_gmts_color,2);
                $booking_grand_total+=$total_amount_as_per_gmts_color;
                $booking_grand_qty+=$total_qty_as_per_gmts_color;
                ?>
                </td>
                <td> </td>
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

                <th width="70%" style="border:1px solid black; text-align:right">Total Wo Qty(<? echo $fab_uom;?>) &nbsp;</th><td width="30%" style="border:1px solid black; text-align:left"><? echo number_format($booking_grand_qty,2);?></td>
            </tr>

       <tr style="border:1px solid black;">

                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount</th><td width="30%" style="border:1px solid black; text-align:left" align="left"><? echo number_format($booking_grand_total,2);?></td>
            </tr>
            <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:left">Total Booking Amount (in word)</th><td width="30%" style="border:1px solid black;" align="left"><? echo number_to_words(def_number_format($booking_grand_total,2,""),$mcurrency, $dcurrency);?></td>
            </tr>
       </table>
          &nbsp;
        <?
		   echo get_spacial_instruction($txt_booking_no);
		?>
    <br/>

	 <?
    if($show_comment==1)
	{
		$condition= new condition();
		if($job_nos !=''){
			$condition->job_no("in ($job_nos)");
		}
		$condition->init();
		$conversion= new conversion($condition);
		//echo $conversion->getQuery();
		//$convQty=$conversion->getQtyArray_by_orderAndProcess();
		$convAmt=$conversion->getAmountArray_by_orderAndProcess();
		//print_r($convAmt);
		?>
    	<table border="0" cellpadding="0" cellspacing="0"  width="100%" class="rpt_table"  style="border:1px solid black;" >
            <tr> <td style="border:1px solid black;" colspan="9" align="center"><b> Comments</b> </td></tr>
            <tr style="border:1px solid black;" align="center">
                <th style="border:1px solid black;" width="5%">SL</th>
                <th style="border:1px solid black;" width="15%">Job No</th>
                <th style="border:1px solid black;" width="15%">PO No</th>
                <th style="border:1px solid black;" width="10%">Ship Date</th>
                <th style="border:1px solid black;" width="10%">Pre-Cost/Budget Value</th>
                <th style="border:1px solid black;" width="10%">WO Value</th>
               
                <th style="border:1px solid black;" width="10%">Balance</th>
                <th style="border:1px solid black;" width=""> Comments </th>
            </tr>
            <tbody>
                <?
				$pre_cost_currency_arr=return_library_array( "select job_no,currency_id from  wo_po_details_master", "job_no", "currency_id");
				$pre_cost_exchange_rate_arr=return_library_array( "select job_no,exchange_rate from   wo_pre_cost_mst", "job_no", "exchange_rate");			
				$pre_cost_item_id_arr=return_library_array( "select id,item_number_id from wo_pre_cost_fabric_cost_dtls", "id", "item_number_id");
				$ship_date_arr=return_library_array( "select id,pub_shipment_date from wo_po_break_down", "id", "pub_shipment_date");
				$gmtsitem_ratio_sql=sql_select("select job_no,gmts_item_id,set_item_ratio from wo_po_details_mas_set_details");

				$gmtsitem_ratio_array=array();
				foreach($gmtsitem_ratio_sql as $gmtsitem_ratio_sql_row) {
				    $gmtsitem_ratio_array[$gmtsitem_ratio_sql_row[csf('job_no')]][$gmtsitem_ratio_sql_row[csf('gmts_item_id')]]=$gmtsitem_ratio_sql_row[csf('set_item_ratio')];	
				}

				$po_qty_arr=array();$aop_data_arr=array();
				$aop_booking_array=array();$aop_booking_data=array();
				$sql_wo=sql_select("select b.po_break_down_id as po_id, b.booking_no, a.exchange_rate, b.pre_cost_fabric_cost_dtls_id as fab_dtls_id, sum(b.amount) as amount from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.booking_type=3 and a.item_category=12 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 group by  b.po_break_down_id, b.booking_no,b.pre_cost_fabric_cost_dtls_id,a.exchange_rate");
				foreach($sql_wo as $row)
				{ //pre_cost_fabric_cost_dtls_id
					$aop_booking_array[$row[csf('booking_no')]][$row[csf('po_id')]]['amount']=$row[csf('amount')];
					$aop_booking_array[$row[csf('booking_no')]][$row[csf('po_id')]]['exchange_rate']=$row[csf('exchange_rate')];
				}
						
				if($db_type==0) $group_concat="group_concat( distinct booking_no,',') AS booking_no";
				else if($db_type==2)  $group_concat="listagg(cast(booking_no as varchar2(4000)),',') within group (order by booking_no) AS booking_no";
					
					
				$wo_book=sql_select("select po_break_down_id, pre_cost_fabric_cost_dtls_id as fab_dtls_id, $group_concat, sum(amount) as amount from wo_booking_dtls where booking_type=3 and status_active=1 and is_deleted=0 group by po_break_down_id,pre_cost_fabric_cost_dtls_id");
				foreach($wo_book as $row)
				{ //pre_cost_fabric_cost_dtls_id
					$aop_booking_data[$row[csf('po_break_down_id')]][$row[csf('fab_dtls_id')]]['booking_no']=$row[csf('booking_no')];
				}
						
				//$sql_po_qty=sql_select("select b.id as po_id,b.pub_shipment_date,sum(b.plan_cut) as order_quantity,(sum(b.plan_cut)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst   and a.is_deleted=0  and a.status_active=1 group by b.id,a.total_set_qnty,b.pub_shipment_date");
				$sql_po_qty=sql_select("select a.job_no_mst as job, a.item_number_id, sum(a.plan_cut_qnty) as plan_cut_qnty  from wo_po_color_size_breakdown a where a.is_deleted=0  and a.status_active=1 group by a.job_no_mst,a.item_number_id");
				foreach( $sql_po_qty as $row) {
					$po_qty_arr[$row[csf("job")]][$row[csf("item_number_id")]]['order_quantity']=$row[csf("plan_cut_qnty")];
				}

				$pre_cost=sql_select("select job_no, sum(charge_unit) as charge_unit,sum(amount) AS aop_cost, sum(avg_req_qnty) as avg_req_qnty from wo_pre_cost_fab_conv_cost_dtls where cons_process=35 and status_active=1 and is_deleted=0 group by job_no");
				foreach($pre_cost as $row)
				{ 
					$aop_data_arr[$row[csf('job_no')]]['aop']=$row[csf('aop_cost')];
					$aop_data_arr[$row[csf('job_no')]]['avg_req_qnty']=$row[csf('avg_req_qnty')];
					$aop_data_arr[$row[csf('job_no')]]['unit']=$row[csf('charge_unit')];	
				}
				
				$i=1; 
				$total_balance_aop=$tot_aop_cost=$tot_pre_cost=0;
				if($db_type==0) {
					$group_concat="group_concat(c.fabric_description ) as  pre_cost_fabric_cost_dtls_id,group_concat(c.id ) as  conv_cost_dtls_id ";	
				} else	{
					$group_concat="listagg(c.fabric_description ,',') within group (order by c.fabric_description) AS pre_cost_fabric_cost_dtls_id, listagg(c.id ,',') within group (order by c.id) AS conv_cost_dtls_id";
				}
				
				
				$sql_aop="select $group_concat, b.po_break_down_id as po_id, a.job_no,sum(b.amount) as amount from wo_booking_mst a, wo_booking_dtls b,wo_pre_cost_fab_conv_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.booking_no=$txt_booking_no and a.booking_type=3 and a.item_category=12 and b.amount>0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by b.po_break_down_id,a.job_no  order by b.po_break_down_id";
				
				//echo $sql_aop;					
                $nameArray=sql_select( $sql_aop );
					
				//print_r($nameArray);
                foreach ($nameArray as $selectResult)
                {					
					//$costing_per=return_field_value("costing_per", "wo_pre_cost_mst", "job_no='".$selectResult[csf('job_no')]."'");
					$costing_per=return_field_value("costing_per", "wo_pre_cost_mst", "job_no='".$selectResult[csf('job_no')]."'");
					if ($costing_per==1)	$costing_per_qty=12;
					else if ($costing_per==2) $costing_per_qty=1;
					else if ($costing_per==3) $costing_per_qty=24;
					else if ($costing_per==4) $costing_per_qty=36;
					else if ($costing_per==5) $costing_per_qty=48;
					
					$pre_cost_item=array_unique(explode(",",$selectResult[csf('pre_cost_fabric_cost_dtls_id')]));
					//print_r($pre_cost_item);
					$po_qty=0;$booking_data='';
					foreach($pre_cost_item as $item)
					{
						$set_ratio=$gmtsitem_ratio_array[$selectResult[csf('job_no')]][$pre_cost_item_id_arr[$item]];
						$po_qty+=$po_qty_arr[$selectResult[csf('job_no')]][$pre_cost_item_id_arr[$item]]['order_quantity'];
					}
					$conv_cost_dtls_id=array_unique(explode(",",$selectResult[csf('conv_cost_dtls_id')]));
					//print_r($pre_cost_item);
					$booking_data='';
					foreach($conv_cost_dtls_id as $ids)
					{
							
						if($booking_data!='' ) $booking_data.=",".$aop_booking_data[$selectResult[csf('po_id')]][$ids]['booking_no'];
						else $booking_data=$aop_booking_data[$selectResult[csf('po_id')]][$ids]['booking_no'];
					}

					$booking_amount=0;
					$exchaned_rate=0;
					$booking_data=array_unique(explode(",",$booking_data));
					foreach($booking_data as $book_no)  //Cumulative value ---Aziz--
					{
						if($book_no!=str_replace("'","",$txt_booking_no))
						{
							$booking_amount=$aop_booking_array[$book_no][$selectResult[csf('po_id')]]['amount'];
							$exchaned_rate=$aop_booking_array[$book_no][$selectResult[csf('po_id')]]['exchange_rate'];
						}
					}				
					
					//echo $booking_amount.'='.$exchaned_rate;
					//print_r($booking_data);					
					$pre_cost_currence_id=$pre_cost_currency_arr[$selectResult[csf('job_no')]];
					$pre_cost_exchange_rate=$pre_cost_exchange_rate_arr[$selectResult[csf('job_no')]];
					$wo_currence_id=$result[csf('currency_id')];
					if($pre_cost_currence_id==$wo_currence_id)//USD=2,TK=1
					{
						 $aop_charge=($selectResult[csf("amount")]/1)+($booking_amount/1);
					}
					/*else if($wo_currence_id==2 && $pre_cost_currence_id==1 ) 
					{
						 $aop_charge=$selectResult[csf("amount")]*$result[csf('exchange_rate')];
					}*/
					else if($wo_currence_id==1) 
					{
						 $aop_charge=($selectResult[csf("amount")]/$pre_cost_exchange_rate)+($booking_amount/$pre_cost_exchange_rate);
					}					
					
					$tot_per_ratio=$costing_per_qty*$set_ratio;					
					//$pre_cost_aop=(($aop_data_arr[$selectResult[csf('job_no')]]['avg_req_qnty']/$tot_per_ratio)*$po_qty)*$aop_data_arr[$selectResult[csf('job_no')]]['unit'];
					$pre_cost_aop=array_sum($convAmt[$selectResult[csf('po_id')]][35]);
					//$aop_charge=$selectResult[csf("amount")]/$result[csf('exchange_rate')];
					$ship_date=$ship_date_arr[$selectResult[csf("po_id")]];					
					//echo $aop_data_arr[$selectResult[csf('job_no')]]['aop'].'=>>'.$tot_per_ratio.'=='.$po_qty;
					//$all_job_arr[]=$selectResult[csf('job_no')];
					//echo "Jahid";						
	                ?>
                    <tr>
	                    <td style="border:1px solid black;" width="5%"><? echo $i; ?></td>
	                    <td style="border:1px solid black;" width="15%"><? echo $job_no_arr[$selectResult[csf('po_id')]];?></td>
	                    <td style="border:1px solid black;" width="15%"><? echo $po_no[$selectResult[csf('po_id')]];?></td>
	                    <td style="border:1px solid black;" width="10%" align="right"><? echo change_date_format($ship_date);?></td>
	                    <td style="border:1px solid black;" width="10%" align="right" title="<? echo $aop_data_arr[$selectResult[csf('job_no')]]['avg_req_qnty']."##".$tot_per_ratio ."##".$po_qty."##".$aop_data_arr[$selectResult[csf('job_no')]]['unit'];  ?> ">
	                        <? echo number_format($pre_cost_aop,2); ?>
	                    </td>
	                    <td style="border:1px solid black;" width="10%" align="right"><? echo number_format($aop_charge,2); ?></td>  
	                    <td style="border:1px solid black;" width="" align="right">
	                        <? $tot_balance=$pre_cost_aop-$aop_charge; echo number_format($tot_balance,2); ?>
	                    </td>
	                    <td style="border:1px solid black;" width="">
	                    <? 
						if ($pre_cost_aop>$aop_charge) echo "Less Booking";
						else if ($pre_cost_aop<$aop_charge) echo "Over Booking";
						else if ($pre_cost_aop==$aop_charge) echo "As Per";
						else echo "";
						?>
	                    </td>
                    </tr>
				   	<?
				  	$tot_pre_cost+=$pre_cost_aop;
				  	$tot_aop_cost+=$aop_charge;
					$total_balance_aop+=$tot_balance;
				    $i++;
				}
                ?>
			</tbody>
	        <tfoot>
	            <tr>
	                <td style="border:1px solid black;" colspan="4" align="right">  <b>Total</b></td>
	                <td style="border:1px solid black;" align="right"> <b><? echo number_format($tot_pre_cost,2); ?></b></td>
	                <td style="border:1px solid black;"  align="right"><b> <? echo number_format($tot_aop_cost,2); ?> </b></td>
	                <td style="border:1px solid black;"  align="right"><b> <? echo number_format($total_balance_aop,2); ?></b> </td>
	                <td style="border:1px solid black;">&nbsp;  </td>
	             </tr>
	        </tfoot>
	    </table>
    	<?
	}
    echo signature_table(79, $cbo_company_name, "1313px");
    echo "****".custom_file_name($txt_booking_no,$style_sting,$job_no);
    ?>
    </div>
	<?
	exit();
}

if($action=="show_trim_booking_report2")
{
	extract($_REQUEST);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$company_address_arr=return_library_array( "select id,city from lib_company",'id','city');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$deling_marcent_arr=return_library_array("select id,team_member_name from lib_mkt_team_member_info","id","team_member_name");
	$season_arr=return_library_array("select id,season_name from lib_buyer_season where status_active=1","id","season_name");
	$color_library=return_library_array( "select id,color_name from lib_color where status_active =1 and is_deleted=0", "id", "color_name"  );
	//$job_no_library=return_library_array( "select id,job_no from wo_booking_dtls", "id", "job_no"  );
	$ord_no_library=return_library_array( "select id,po_number from wo_po_break_down", "id", "po_number"  );
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$comp_short=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
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
                                            <?php echo $result[csf('province')]; ?>
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
			$job_nos.="'".$result_buy[csf('job_no')]."',";
		}
		$job_nos=rtrim($job_nos,',');
		$season_names=rtrim($season_names,',');

		$po_no=array();
		$job_no_arr=array();
		$file_no=array();
		$ref_no=array();
		$po_quantity=array();

		$nameArray_job=sql_select( "select b.id, b.po_number, b.job_no_mst as job_no, b.grouping, b.file_no, b.po_quantity  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no");
        foreach ($nameArray_job as $result_job){
			$po_no[$result_job[csf('id')]]=$result_job[csf('po_number')];
			$job_no_arr[$result_job[csf('id')]]=$result_job[csf('job_no')];
			$file_no[$result_job[csf('id')]]=$result_job[csf('file_no')];
			$ref_no[$result_job[csf('id')]]=$result_job[csf('grouping')];
			$po_quantity[$result_job[csf('id')]]=$result_job[csf('po_quantity')];
		}

		//===================
		$txt_fab_booking=str_replace("'","",$txt_fab_booking);
		if($txt_fab_booking !==''){
			
			$sql_2="SELECT a.pay_mode, a.company_id,a.tagged_booking_no, a.booking_no, a.booking_date, a.supplier_id, a.process, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.source, a.is_short,a.entry_form,a.booking_type
			from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c, wo_po_break_down d
			where a.booking_no=b.booking_no and b.job_no=c.job_no and c.job_no=d.job_no_mst and d.id=b.po_break_down_id and a.booking_type in (1,3,4) and  a.booking_no='$txt_fab_booking'
			and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
			group by a.pay_mode, a.company_id,a.tagged_booking_no, a.booking_no, a.booking_date, a.supplier_id, a.process, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.source, a.is_short,a.entry_form,a.booking_type";

		}

		  $fab_booking_arr=sql_select($sql_2);
		$sql="SELECT a.pay_mode, a.company_id,a.tagged_booking_no, a.booking_no, a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.source, a.is_short,a.entry_form,a.booking_type  from wo_booking_mst a where  a.booking_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0";
		



        $nameArray=sql_select( "select a.pay_mode, a.company_id,a.tagged_booking_no, a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.source, a.is_short  from wo_booking_mst a where  a.booking_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0");


        foreach ($nameArray as $result){
        	if($result[csf("pay_mode")]==1 || $result[csf("pay_mode")]==2){
				$supp=$supplier_name_arr[$result[csf("supplier_id")]];
				$supp_adds=$supplier_address_arr[$result[csf("supplier_id")]];
			}else{
				$supp=$comp_short[$result[csf("supplier_id")]];
				$supp_adds=$company_address_arr[$result[csf("supplier_id")]];
			}
        ?>
       <table width="100%" style="border:1px solid black;table-layout: fixed;">
            <tr>
                <td width="100" style="font-size:16px"><b>Booking No </b>   </td>
                <td width="110" style="font-size:16px">:&nbsp;<b><? echo $result[csf('booking_no')];?></b> </td>
                <td width="100" style="font-size:12px"><b>Booking Date</b></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>
                <td width="110" align="center"><b></b></td>

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
                <td width="110" style="font-size:16px">:&nbsp;<b><? echo $supp;?> </b>   </td>
            </tr>
             <tr>
                <td width="100" style="font-size:12px"><b>Supplier Address</b></td>
               	<td width="110">:&nbsp;<? echo $supp_adds;?></td>
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
                <td style="font-size:12px;overflow:hidden;text-overflow: ellipsis;white-space: nowrap;" colspan="">:&nbsp;<b><?=implode(",",array_unique($po_no));  ?></b></td>
                <td >Pay Mode </td><td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;">:&nbsp;<?=$pay_mode[$result[csf("pay_mode")]]; ?></td> 
            	<td>Fabric Booking :  <?=$result[csf('tagged_booking_no')]; ?></td>
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
	if($db_type==0) $group_concat="group_concat( distinct id,',') AS booking_dtls_id";
	else if($db_type==2)  $group_concat="listagg(cast(id as varchar2(4000)),',') within group (order by id) AS booking_dtls_id";
	$nameArray_item_imge =sql_select("SELECT master_tble_id,image_location,real_file_name FROM common_photo_library where form_name='aop_v2'  and file_type=1");
	foreach($nameArray_item_imge as $row)
	{
		$ids=explode(",", $row[csf('master_tble_id')]);
    	foreach ($ids as $key => $value) 
    	{
    		$item_img_arr[$value]=$row[csf('image_location')];
    	}
	}
				
        $nameArray_item=sql_select( "select distinct description from wo_booking_dtls  where booking_no=$txt_booking_no and wo_qnty>0   and status_active=1 and is_deleted=0 and process=35 and status_active=1 and is_deleted=0 ");
        $nameArray_color=sql_select( "select distinct fabric_color_id from wo_booking_dtls   where  booking_no=$txt_booking_no and wo_qnty>0  and status_active=1 and is_deleted=0 and process=35 and status_active=1 and is_deleted=0 ");
//$k=1;$tot_row=count($nameArray_color);
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
                <td style="border:1px solid black" align="center"><strong>WO Qty</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin Dia</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
                <td style="border:1px solid black" align="center"><strong>Image</strong></td>
            </tr>
            <?
			 $total_amount_as_per_gmts_color=0;
			 $total_qty_as_per_gmts_color=0;
            $nameArray_item_description=sql_select( "select  $group_concat,fabric_color_id,job_no,gmts_color_id,printing_color_id,po_break_down_id,description,rate,dia_width,sum(wo_qnty) as cons from wo_booking_dtls  where booking_no=$txt_booking_no  and process=35 and description='".$result_item[csf('description')]."' and wo_qnty>0 and status_active=1 and is_deleted=0  group by fabric_color_id,gmts_color_id,printing_color_id,description,rate,dia_width,job_no,po_break_down_id");//and sensitivity=1
			$k=1;$tot_row=count($nameArray_item_description);
                foreach($nameArray_item_description as $result_itemdescription)
                {
			$booking_dtls_ids=array_unique(explode(",",$result_itemdescription[csf('booking_dtls_id')]));
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
                 
					<td  style="border:1px solid black">
					<?
						 foreach($booking_dtls_ids as $bid)
						{
							$item_img=$item_img_arr[$bid];
						 
						?>
					<img src="../../<? echo $item_img; ?>"  width="90px" height="80px" border="0" /><? //echo $item_img; ?>  
					<?
						}
					?>
					</td>
					<?
				
				$k++;
				?>
            </tr>
				<?
                }
                ?>
            <tr>
            <td colspan="5" align="right"> <strong> Total Qty  </strong></td>
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

                <th width="70%" style="border:1px solid black; text-align:right">Total WO Qty &nbsp;</th><td width="30%" style="border:1px solid black; text-align:left"><? echo number_format($booking_grand_qty,2);?></td>
            </tr>

       <tr style="border:1px solid black;">

                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount</th><td width="30%" style="border:1px solid black; text-align:left"><? echo number_format($booking_grand_total,2);?></td>
            </tr>
            <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount (in word)</th><td width="30%" style="border:1px solid black;"><? echo number_to_words(def_number_format($booking_grand_total,2,""),$mcurrency, $dcurrency);//number_to_words(number_format($booking_grand_total,2),$mcurrency, $dcurrency);?></td>
            </tr>
       </table>
         <br/>
		 <table width="100%" class="rpt_table" style="border:1px solid black;"   border="1" cellpadding="0" cellspacing="0" rules="all">
		 <caption align="center"><b>Image View</b> </caption>
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
					<td valign="top" width="90" style="word-wrap:break-word;width:90px;">
					<p style="word-wrap:break-word; width:90px">
						<!--<img src="../../<? //echo $result_imge[csf('image_location')]; ?>" width="90" height="100" border="2" />-->
                        <img src="<? echo $path.$result_imge[csf('image_location')]; ?>" width="110" height="100" border="2" />
                       <?
					   $img=explode('.',$result_imge[csf('real_file_name')]);
					 	echo $img[0];
					   ?>
					   </p>
					</td>
					<?

					$img_counter++;
				}
				?>
                </tr>
           </table>
		   <br>
        <?
		   echo get_spacial_instruction($txt_booking_no);
		?>
       <br><br>
        <?
    if($show_comment==1)
	{
		$condition= new condition();
		if($job_nos !=''){
			$condition->job_no("=$job_nos");
		}
		$condition->init();
		$conversion= new conversion($condition);
		//echo $conversion->getQuery();
		//$convQty=$conversion->getQtyArray_by_orderAndProcess();
		$convAmt=$conversion->getAmountArray_by_orderAndProcess();
		//print_r($convAmt);
		?>
    	<table border="0" cellpadding="0" cellspacing="0"  width="100%" class="rpt_table"  style="border:1px solid black;" >
            <tr> <td style="border:1px solid black;" colspan="9" align="center"><b> Comments</b> </td></tr>
            <tr style="border:1px solid black;" align="center">
                <th style="border:1px solid black;" width="5%">SL</th>
                <th style="border:1px solid black;" width="15%">Job No</th>
                <th style="border:1px solid black;" width="15%">PO No</th>
                <th style="border:1px solid black;" width="10%">Ship Date</th>
                <th style="border:1px solid black;" width="10%">Pre-Cost/Budget Value</th>
                <th style="border:1px solid black;" width="10%">WO Value</th>
               
                <th style="border:1px solid black;" width="10%">Balance</th>
                <th style="border:1px solid black;" width=""> Comments </th>
            </tr>
	       <tbody>
	       		<?
				$pre_cost_currency_arr=return_library_array( "select job_no,currency_id from  wo_po_details_master", "job_no", "currency_id");
				$pre_cost_exchange_rate_arr=return_library_array( "select job_no,exchange_rate from   wo_pre_cost_mst", "job_no", "exchange_rate");				
				$pre_cost_item_id_arr=return_library_array( "select id,item_number_id from wo_pre_cost_fabric_cost_dtls", "id", "item_number_id");
				$ship_date_arr=return_library_array( "select id,pub_shipment_date from wo_po_break_down", "id", "pub_shipment_date");
				$gmtsitem_ratio_sql=sql_select("select job_no,gmts_item_id,set_item_ratio from wo_po_details_mas_set_details  ");// where job_no ='FAL-14-01157'
				$gmtsitem_ratio_array=array();
				foreach($gmtsitem_ratio_sql as $gmtsitem_ratio_sql_row) {
					$gmtsitem_ratio_array[$gmtsitem_ratio_sql_row[csf('job_no')]][$gmtsitem_ratio_sql_row[csf('gmts_item_id')]]=$gmtsitem_ratio_sql_row[csf('set_item_ratio')];	
				}

				$po_qty_arr=array();$aop_data_arr=array();
				$aop_booking_array=array();$aop_booking_data=array();
				$sql_wo=sql_select("select b.po_break_down_id as po_id, b.booking_no, a.exchange_rate, b.pre_cost_fabric_cost_dtls_id as fab_dtls_id, sum(b.amount) as amount from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and b.booking_type=3 and a.item_category=12 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 group by  b.po_break_down_id, b.booking_no,b.pre_cost_fabric_cost_dtls_id,a.exchange_rate");
				foreach($sql_wo as $row)
				{ //pre_cost_fabric_cost_dtls_id
					$aop_booking_array[$row[csf('booking_no')]][$row[csf('po_id')]]['amount']=$row[csf('amount')];
					$aop_booking_array[$row[csf('booking_no')]][$row[csf('po_id')]]['exchange_rate']=$row[csf('exchange_rate')];
				}
					
				if($db_type==0) $group_concat="group_concat( distinct booking_no,',') AS booking_no";
				else if($db_type==2)  $group_concat="listagg(cast(booking_no as varchar2(4000)),',') within group (order by booking_no) AS booking_no";				
				
				$wo_book=sql_select("select po_break_down_id,pre_cost_fabric_cost_dtls_id as fab_dtls_id,$group_concat,sum(amount) as amount  from wo_booking_dtls where 
				 booking_type=3 and status_active=1 and is_deleted=0 group by po_break_down_id,pre_cost_fabric_cost_dtls_id");
				foreach($wo_book as $row) { //pre_cost_fabric_cost_dtls_id
					$aop_booking_data[$row[csf('po_break_down_id')]][$row[csf('fab_dtls_id')]]['booking_no']=$row[csf('booking_no')];
				}
					
				//$sql_po_qty=sql_select("select b.id as po_id,b.pub_shipment_date,sum(b.plan_cut) as order_quantity,(sum(b.plan_cut)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst   and a.is_deleted=0  and a.status_active=1 group by b.id,a.total_set_qnty,b.pub_shipment_date");
				$sql_po_qty=sql_select("select a.job_no_mst as job, a.item_number_id, sum(a.plan_cut_qnty) as plan_cut_qnty  from wo_po_color_size_breakdown a  where     a.is_deleted=0  and a.status_active=1 group by a.job_no_mst,a.item_number_id");
				foreach( $sql_po_qty as $row) {
					$po_qty_arr[$row[csf("job")]][$row[csf("item_number_id")]]['order_quantity']=$row[csf("plan_cut_qnty")];
				}

				$pre_cost=sql_select("select job_no,sum(charge_unit) as charge_unit,sum(amount) AS aop_cost, sum(avg_req_qnty) as avg_req_qnty from wo_pre_cost_fab_conv_cost_dtls where cons_process=35 and status_active=1 and is_deleted=0 group by job_no");
				foreach($pre_cost as $row)
				{ 
					$aop_data_arr[$row[csf('job_no')]]['aop']=$row[csf('aop_cost')];
					$aop_data_arr[$row[csf('job_no')]]['avg_req_qnty']=$row[csf('avg_req_qnty')];
					$aop_data_arr[$row[csf('job_no')]]['unit']=$row[csf('charge_unit')];	
				}
				
				$i=1; $total_balance_aop=0;
				$tot_aop_cost=0;$tot_pre_cost=0;
				if($db_type==0) {
					$group_concat="group_concat(c.fabric_description ) as  pre_cost_fabric_cost_dtls_id,group_concat(c.id ) as  conv_cost_dtls_id ";	
				} else {
					$group_concat="listagg(c.fabric_description ,',') within group (order by c.fabric_description) AS pre_cost_fabric_cost_dtls_id, listagg(c.id ,',') within group (order by c.id) AS conv_cost_dtls_id";
				}
			
			
				$sql_aop="select $group_concat,b.po_break_down_id as po_id,a.job_no,sum(b.amount) as amount from wo_booking_mst a, wo_booking_dtls b,wo_pre_cost_fab_conv_cost_dtls c    where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id  and a.booking_no=$txt_booking_no and a.booking_type=3 and a.item_category=12 and b.amount>0 and  a.status_active=1  and a.is_deleted=0 and c.status_active=1  and c.is_deleted=0  and b.status_active=1  and b.is_deleted=0   group by b.po_break_down_id,a.job_no  order by b.po_break_down_id";
			
				//echo $sql_aop;
				
                $nameArray=sql_select( $sql_aop );
					
				//print_r($nameArray);
                foreach ($nameArray as $selectResult)
                {					
					//$costing_per=return_field_value("costing_per", "wo_pre_cost_mst", "job_no='".$selectResult[csf('job_no')]."'");
					$costing_per=return_field_value("costing_per", "wo_pre_cost_mst", "job_no='".$selectResult[csf('job_no')]."'");
					if ($costing_per==1) $costing_per_qty=12;
					else if($costing_per==2) $costing_per_qty=1;
					else if($costing_per==3) $costing_per_qty=24;
					else if($costing_per==4) $costing_per_qty=36;
					else if($costing_per==5) $costing_per_qty=48;
					
					$pre_cost_item=array_unique(explode(",",$selectResult[csf('pre_cost_fabric_cost_dtls_id')]));
					//print_r($pre_cost_item);
					$po_qty=0;$booking_data='';
					foreach($pre_cost_item as $item)
					{
						$set_ratio=$gmtsitem_ratio_array[$selectResult[csf('job_no')]][$pre_cost_item_id_arr[$item]];
						$po_qty+=$po_qty_arr[$selectResult[csf('job_no')]][$pre_cost_item_id_arr[$item]]['order_quantity'];
					}
					$conv_cost_dtls_id=array_unique(explode(",",$selectResult[csf('conv_cost_dtls_id')]));
					//print_r($pre_cost_item);
					$booking_data='';
					foreach($conv_cost_dtls_id as $ids)
					{							
						if($booking_data!='' ) $booking_data.=",".$aop_booking_data[$selectResult[csf('po_id')]][$ids]['booking_no'];
						else $booking_data=$aop_booking_data[$selectResult[csf('po_id')]][$ids]['booking_no'];
					}

					$booking_amount=0;
					$exchaned_rate=0;
					$booking_data=array_unique(explode(",",$booking_data));
					foreach($booking_data as $book_no)  //Cumulative value ---Aziz--
					{
						if($book_no!=str_replace("'","",$txt_booking_no))
						{
							$booking_amount=$aop_booking_array[$book_no][$selectResult[csf('po_id')]]['amount'];
							$exchaned_rate=$aop_booking_array[$book_no][$selectResult[csf('po_id')]]['exchange_rate'];
						}
					}					
					
					//echo $booking_amount.'='.$exchaned_rate;
					//print_r($booking_data);					
					$pre_cost_currence_id=$pre_cost_currency_arr[$selectResult[csf('job_no')]];
					$pre_cost_exchange_rate=$pre_cost_exchange_rate_arr[$selectResult[csf('job_no')]];
					$wo_currence_id=$result[csf('currency_id')];
					if($pre_cost_currence_id==$wo_currence_id)//USD=2,TK=1
					{
						$aop_charge=($selectResult[csf("amount")]/1)+($booking_amount/1);
					}
					/*else if($wo_currence_id==2 && $pre_cost_currence_id==1 ) 
					{
						 $aop_charge=$selectResult[csf("amount")]*$result[csf('exchange_rate')];
					}*/
					else if($wo_currence_id==1 && $pre_cost_currence_id==2 ) 
					{
						 $aop_charge=($selectResult[csf("amount")]/$pre_cost_exchange_rate)+($booking_amount/$pre_cost_exchange_rate);
					}					
					
					$tot_per_ratio=$costing_per_qty*$set_ratio;					
					//$pre_cost_aop=(($aop_data_arr[$selectResult[csf('job_no')]]['avg_req_qnty']/$tot_per_ratio)*$po_qty)*$aop_data_arr[$selectResult[csf('job_no')]]['unit'];
					$pre_cost_aop=array_sum($convAmt[$selectResult[csf('po_id')]][35]);
					//$aop_charge=$selectResult[csf("amount")]/$result[csf('exchange_rate')];
					$ship_date=$ship_date_arr[$selectResult[csf("po_id")]];					
					//echo $aop_data_arr[$selectResult[csf('job_no')]]['aop'].'=>>'.$tot_per_ratio.'=='.$po_qty;
					//$all_job_arr[]=$selectResult[csf('job_no')];
					//echo "Jahid";					
	   				?>
                    <tr>
	                    <td style="border:1px solid black;" width="5%"><? echo $i;?></td>
	                    <td style="border:1px solid black;" width="15%">
						<? echo $job_no_arr[$selectResult[csf('po_id')]];?> 
	                    </td>
	                    <td style="border:1px solid black;" width="15%">
						<? echo $po_no[$selectResult[csf('po_id')]];?> 
	                    </td>
	                    <td style="border:1px solid black;" width="10%" align="right">
						<? echo change_date_format($ship_date);?> 
	                    
	                    </td>
	                    <td style="border:1px solid black;" width="10%" align="right" title="<? echo $aop_data_arr[$selectResult[csf('job_no')]]['avg_req_qnty']."##".$tot_per_ratio ."##".$po_qty."##".$aop_data_arr[$selectResult[csf('job_no')]]['unit'];  ?> ">
	                    <? echo number_format($pre_cost_aop,2); ?>
	                    </td>
	                    <td style="border:1px solid black;" width="10%" align="right">
	                    <? echo number_format($aop_charge,2); ?>
	                    </td>	                  
	                    <td style="border:1px solid black;" width="" align="right">
	                         <? $tot_balance=$pre_cost_aop-$aop_charge; echo number_format($tot_balance,2); ?>
	                    </td>
	                    <td style="border:1px solid black;" width="">
	                    <? 
						if ($pre_cost_aop>$aop_charge) echo "Less Booking";
						else if ($pre_cost_aop<$aop_charge) echo "Over Booking";
						else if ($pre_cost_aop==$aop_charge) echo "As Per";
						else echo "";
						?>
	                    </td>
                    </tr>
				    <?
				  	$tot_pre_cost+=$pre_cost_aop;
				  	$tot_aop_cost+=$aop_charge;
					$total_balance_aop+=$tot_balance;
				    $i++;
				}
			    ?>
	        </tbody>
	        <tfoot>
	            <tr>
	                <td style="border:1px solid black;" colspan="4" align="right">  <b>Total</b></td>
	                <td style="border:1px solid black;" align="right"> <b><? echo number_format($tot_pre_cost,2); ?></b></td>
	                <td style="border:1px solid black;"  align="right"><b> <? echo number_format($tot_aop_cost,2); ?> </b></td>
	                <td style="border:1px solid black;"  align="right"><b> <? echo number_format($total_balance_aop,2); ?></b> </td>
	                <td style="border:1px solid black;">&nbsp;  </td>
	             </tr>
	        </tfoot>
    	</table>
    	<?
	}
    echo signature_table(79, $cbo_company_name, "1313px");
	echo "****".custom_file_name($txt_booking_no,$style_sting,$job_no);
    ?>
    </div>
    <?
}

if($action=="show_trim_booking_report3")
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$company_address_arr=return_library_array( "select id,city from lib_company",'id','city');
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
                                            <?php echo $result[csf('province')]; ?>
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
			if($result[csf("pay_mode")]==1 || $result[csf("pay_mode")]==2){
				$supp=$supplier_name_arr[$result[csf("supplier_id")]];
				$supp_adds=$supplier_address_arr[$result[csf("supplier_id")]];
			}else{
				$supp=$company_library[$result[csf("supplier_id")]];
				$supp_adds=$company_address_arr[$result[csf("supplier_id")]];
			}
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
                <td width="110">:&nbsp;<? echo $supp;?>    </td>
            </tr>
             <tr>
                <td width="100" style="font-size:12px"><b>Supplier Address</b></td>
               	<td width="110">:&nbsp;<? echo $supp_adds;?></td>
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
				$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description,gsm_weight,uom from  wo_pre_cost_fabric_cost_dtls  where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."'");
				list($fabric_description_row)=$fabric_description;

				$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")].', '.$fabric_description_row[csf("gsm_weight")];
					$fabric_description_uom_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$unit_of_measurement[$fabric_description_row[csf("uom")]];
			}
			/*if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]==0){
				$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description,gsm_weight from  wo_pre_cost_fabric_cost_dtls  where  job_no='".rtrim($job_no,", ")."'");
				foreach( $fabric_description as $fabric_description_row){
					$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]].=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")].', '.$fabric_description_row[csf("gsm_weight")];
				}
			}*/
		}
	//=================================================
       // $nameArray_item=sql_select( "select distinct description from wo_booking_dtls  where booking_no=$txt_booking_no and wo_qnty !=0   and status_active=1 and is_deleted=0 and process=35 and status_active=1 and is_deleted=0 ");
	    $nameArray_item=sql_select( "select distinct description from wo_booking_dtls b,WO_PRE_COST_FAB_CONV_COST_DTLS a where b.pre_cost_fabric_cost_dtls_id=a.id 
and b.booking_no=$txt_booking_no and b.wo_qnty !=0 and b.status_active=1 and b.is_deleted=0 and b.process=35 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 ");

        $nameArray_color=sql_select( "select distinct fabric_color_id,printing_color_id from wo_booking_dtls   where  booking_no=$txt_booking_no and wo_qnty !=0  and status_active=1 and is_deleted=0 and process=35 and status_active=1 and is_deleted=0 ");

       if(count($nameArray_color)>0){
	   foreach($nameArray_item as $result_item){
        ?>

        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="10" align="">
                <strong><? echo "Fabrication:".rtrim($fabric_description_array[$result_item[csf('description')]],", ");
				$fab_uom=$fabric_description_uom_array[$result_item[csf('description')]];
				 ?> </strong><br/>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>GMT Color</strong> </td>
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <td style="border:1px solid black"><strong>Print Color</strong> </td>
                <td style="border:1px solid black" align="center"><strong>Wo Qty (<? echo $fab_uom;?>)</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin Dia</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			 $total_amount_as_per_gmts_color=0;
			 $total_qty_as_per_gmts_color=0;
            $nameArray_item_description=sql_select( "select  fabric_color_id,gmts_color_id,printing_color_id,description,rate,dia_width,sum(wo_qnty) as cons ,fin_dia from wo_booking_dtls  where booking_no=$txt_booking_no  and process=35 and description='".$result_item[csf('description')]."' and wo_qnty !=0 and status_active=1 and is_deleted=0  group by fabric_color_id,gmts_color_id,printing_color_id,description,rate,dia_width,fin_dia");
                foreach($nameArray_item_description as $result_itemdescription)
                {

                ?>
            <tr>
                <td style="border:1px solid black"><? echo $color_library[$result_itemdescription[csf('gmts_color_id')]]; ?>  </td>
                <td style="border:1px solid black"><? echo $color_library[$result_itemdescription[csf('fabric_color_id')]]; ?>  </td>
                 <td style="border:1px solid black"><? echo $color_library[$result_itemdescription[csf('printing_color_id')]]; ?>  </td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('cons')],2); ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('fin_dia')]; ?></td>
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
            <td colspan="3" align="right"> <strong> Total Qty (<? echo $fab_uom;?>) </strong></td>
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

                <th width="70%" style="border:1px solid black; text-align:right">Total Wo Qty(<? echo $fab_uom;?>) &nbsp;</th><td width="30%" style="border:1px solid black; text-align:left"><? echo number_format($booking_grand_qty,2);?></td>
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

if ($action=="populate_data_from_search_popup")
{
	 $sql= "select id,booking_no,booking_date,company_id,buyer_id,job_no,po_break_down_id,item_category,fabric_source,currency_id,exchange_rate,pay_mode,booking_month,ready_to_approved,supplier_id,attention,tenor,delivery_date,source,booking_year,cbo_level,is_short,is_approved,tagged_booking_no,delivery_to,
	 remarks from wo_booking_mst  where id='$data' and status_active=1 and is_deleted=0";
	 $data_array=sql_select($sql);
	 foreach ($data_array as $row){
		$sup=$row[csf("pay_mode")]."_".$row[csf("company_id")];
		echo "document.getElementById('cbo_company_name').value 	 = '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value 		 = '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('txt_booking_no').value 		 = '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('txt_delivery_to').value 		 = '".$row[csf("delivery_to")]."';\n";
		echo "document.getElementById('txt_remark').value 		 = '".$row[csf("remarks")]."';\n";
		
	   echo "document.getElementById('txt_fab_booking').value 		 = '".$row[csf("tagged_booking_no")]."';\n";
		echo "document.getElementById('cbo_currency').value 		 = '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('update_id').value 		 = '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value 	 = '".$row[csf("exchange_rate")]."';\n";
		echo "document.getElementById('cbo_pay_mode').value 		 = '".$row[csf("pay_mode")]."';\n";
		echo "document.getElementById('txt_booking_date').value 	 = '".change_date_format($row[csf("booking_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('cbo_booking_month').value 	 = '".$row[csf("booking_month")]."';\n";

		echo "load_drop_down( 'requires/service_booking_aop_urmi_controller', '$sup', 'load_drop_down_supplier', 'supplier_td' );\n";

		echo "document.getElementById('cbo_supplier_name').value 	 = '".$row[csf("supplier_id")]."';\n";
		echo "document.getElementById('txt_attention').value 		 = '".$row[csf("attention")]."';\n";
		echo "document.getElementById('txt_tenor').value 		 = '".$row[csf("tenor")]."';\n";
		echo "document.getElementById('txt_delivery_date').value 	 = '".change_date_format($row[csf("delivery_date")],'dd-mm-yyyy','-')."';\n";
	    echo "document.getElementById('cbo_source').value 			 = '".$row[csf("source")]."';\n";
		echo "document.getElementById('cbo_booking_year').value 	 = '".$row[csf("booking_year")]."';\n";
		echo "document.getElementById('cbo_level').value 			 = '".$row[csf("cbo_level")]."';\n";
	  	echo "document.getElementById('cbo_ready_to_approved').value = '".$row[csf("ready_to_approved")]."';\n";
		echo "document.getElementById('cbo_is_short').value 		 = '".$row[csf("is_short")]."';\n";
		echo "$('#cbo_company_name').attr('disabled',true);\n";
		echo "$('#cbo_supplier_name').attr('disabled',true);\n";
		echo "$('#cbo_level').attr('disabled',true);\n";
		echo "$('#cbo_buyer_name').attr('disabled',true);\n";
		echo "$('#cbo_is_short').attr('disabled',true);\n";

		if($row[csf("is_approved")]==3){
		  $is_approved=1;
		}
		else
		{
			$is_approved=$row[csf("is_approved")];
		}
		  // echo "document.getElementById('is_approved').value          		= '".$is_approved."';\n";

		if($is_approved==1)
		{
			echo "$('#approved').text('This booking is approved.');\n";
			// echo "document.getElementById('txt_un_appv_request').disabled = '".false."';\n";
		}
		else
		{
			echo "$('#approved').text('');\n";
			// echo "document.getElementById('txt_un_appv_request').disabled = '".true."';\n";
		}

	 }
}
if ($action=="fabric_booking_popup")
{
	//echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	//extract($_REQUEST);
	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
 	?>
	<script>
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
            <table width="100%" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
                <thead>
                    <tr>
                        <th colspan="11">
                        <input type="hidden" id="cbo_search_category">
                        </th>
                    </tr>
                    <tr>
                        <th width="150">Company Name</th>
                        <th width="150">Buyer Name</th>
                        <th width="80">Booking No</th>
                        <th width="80">Job No</th>
                        <th width="80">File No</th>
                        <th width="80">Internal Ref.</th>
                        <th width="80">Style Ref </th>
                        <th width="80">Order No</th>
                        <th width="130" colspan="2">Date Range</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tr class="general">
                    <td>
                        <input type="hidden" id="selected_booking">
                       
                        <? 
						//echo "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name";
						$sql="select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) and id=$company order by company_name";

						$sql_buyer="select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company='$company' and buy.id=$buyer and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name";
						//echo $sql;
						echo create_drop_down( "cbo_company_mst", 150,$sql ,"id,company_name",1, "-- Select Company --", '', "load_drop_down( 'fabric_booking_urmi_controller', this.value, 'load_drop_down_buyer_popup', 'buyer_td' );",1); ?>
                    </td>
                    <td ><? echo create_drop_down( "cbo_buyer_name", 150, $sql_buyer,"id,buyer_name", 1, "-- Select Buyer --",$buyer,"",1 ); ?></td>
                    <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px"></td>
                    <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"></td>
                    <td align="center">
                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_order_search').value, 'create_booking_search_list_view2', 'search_div', 'service_booking_aop_urmi_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
                </tr>
                <tr>
                    <td align="center" valign="middle" colspan="11">

                    <?
						echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );
						echo load_month_buttons();
                    ?>
                    </td>
                </tr>
            </table>
            <div id="search_div"></div>
        </form>
        </div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script type="text/javascript">
		$("#cbo_company_mst").val(<? echo $company?>);
		load_drop_down( 'service_booking_aop_urmi_controller', $("#cbo_company_mst").val(), 'load_drop_down_buyer_popup', 'buyer_td' );
	</script>
	</html>
	<?
	exit();
}

if ($action=="create_booking_search_list_view2")
{
	$data=explode('_',$data);
	//echo "SSSSSSSS";die;
	
	if ($data[0]!=0) $company="  a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_id='$data[1]'"; else $buyer="";
	if($db_type==0) $year_cond=" and SUBSTRING_INDEX(b.insert_date, '-', 1)=$data[5]";
	if($db_type==2) $year_cond=" and to_char(b.insert_date,'YYYY')=$data[5]";
	if($db_type==0) $booking_year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[5]";
	if($db_type==2) $booking_year_cond=" and to_char(a.insert_date,'YYYY')=$data[5]";
	if($data[7]==1){
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num='$data[6]'    "; else  $booking_cond="";
		if (str_replace("'","",$data[4])!="") $job_cond=" and b.job_no_prefix_num='$data[4]'  "; else  $job_cond="";
		if (trim($data[10])!="") $style_cond=" and b.style_ref_no ='$data[10]'";
		if (str_replace("'","",$data[11])!="") $order_cond=" and c.po_number = '$data[11]'  ";
	}
	if($data[7]==2){
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '$data[6]%'  $booking_year_cond  "; else  $booking_cond="";
		if (str_replace("'","",$data[4])!="") $job_cond=" and b.job_no_prefix_num like '$data[4]%'  $year_cond  "; else  $job_cond="";
		if (trim($data[10])!="") $style_cond=" and b.style_ref_no like '$data[10]%'";
		if (str_replace("'","",$data[11])!="") $order_cond=" and c.po_number like '$data[11]%'  ";
	}

	if($data[7]==3){
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[6]'  $booking_year_cond  "; else  $booking_cond="";
		if (str_replace("'","",$data[4])!="") $job_cond=" and b.job_no_prefix_num like '%$data[4]'  $year_cond  "; else  $job_cond="";
		if (trim($data[10])!="") $style_cond=" and b.style_ref_no like'%$data[10]'";
		if (str_replace("'","",$data[11])!="") $order_cond=" and c.po_number like '%$data[11]'  ";
	}
	if($data[7]==4 || $data[7]==0){
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[6]%'  $booking_year_cond  "; else  $booking_cond="";
		if (str_replace("'","",$data[4])!="") $job_cond=" and b.job_no_prefix_num like '%$data[4]%'  $year_cond  "; else  $job_cond="";
		if (trim($data[10])!="") $style_cond=" and b.style_ref_no like '%$data[10]%'";
		if (str_replace("'","",$data[11])!="") $order_cond=" and c.po_number like '%$data[11]%'  ";
	}

	$file_no = str_replace("'","",$data[8]);
	$internal_ref = str_replace("'","",$data[9]);
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and c.file_no='".trim($file_no)."' ";
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and c.grouping='".trim($internal_ref)."' ";

	if($db_type==0){
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	if($db_type==2){
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}
	$po_number=return_library_array( "select id,po_number from wo_po_break_down", "id", "po_number");
	$po_array=array();
	$job_prefix_num=array();
	$sql_po= sql_select("select a.booking_no, a.po_break_down_id, a.job_no from wo_booking_mst a where $company $buyer $booking_date and a.booking_type=1 and a.is_short=2 and   a.status_active=1 and a.is_deleted=0 order by a.booking_no");
	foreach($sql_po as $row){
		$po_id=explode(",",$row[csf("po_break_down_id")]);
		$job_prefix_arr=explode("-",$row[csf("job_no")]);
		$po_number_string="";
		foreach($po_id as $key=> $value ){
			$po_number_string.=$po_number[$value].",";
		}
		$po_array[$row[csf("po_break_down_id")]]=rtrim($po_number_string,",");
		$job_prefix_num[$row[csf("job_no")]]=ltrim($job_prefix_arr[2],0);
	}

	$approved=array(0=>"No",1=>"Yes",2=>"No",3=>"Yes");
	$is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$arr=array (2=>$comp,3=>$buyer_arr,4=>$job_prefix_num,6=>$garments_item,7=>$po_array,10=>$item_category,11=>$fabric_source,12=>$suplier,13=>$approved,14=>$is_ready);

	//  $sql= "select a.id, a.booking_no_prefix_num, c.file_no, c.grouping, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.pay_mode, d.gmts_item_id, a.po_break_down_id, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.booking_no, a.ready_to_approved, b.style_ref_no from wo_booking_mst a, wo_po_details_master b, wo_po_break_down c, wo_po_details_mas_set_details d where $company $buyer $job_cond $booking_date $booking_cond  $file_no_cond  $internal_ref_cond $style_cond $order_cond ". set_user_lavel_filtering(' and a.buyer_id','buyer_id')." and a.job_no=b.job_no and a.job_no=c.job_no_mst and b.job_no=c.job_no_mst and b.job_no=d.job_no and a.booking_type=1 and a.is_short=2 and a.status_active=1 and a.is_deleted=0 and a.entry_form=118 group by a.id, a.booking_no_prefix_num, c.file_no, c.grouping, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.pay_mode, d.gmts_item_id, a.po_break_down_id, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.booking_no, a.ready_to_approved, b.style_ref_no order by a.id DESC";

	 $sql="select min(a.id) as id, a.booking_no_prefix_num, a.pay_mode,b.job_no, a.booking_no, a.company_id, a.buyer_id, a.booking_date, a.delivery_date, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved, c.gmts_item_id, c.job_no_prefix_num, c.style_ref_no, d.po_number, d.grouping, d.file_no from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c, wo_po_break_down d where $company $buyer $job_cond $booking_date $booking_cond  $file_no_cond  $internal_ref_cond $style_cond $order_cond ". set_user_lavel_filtering(' and a.buyer_id','buyer_id')." and a.booking_no=b.booking_no and b.job_no=c.job_no and b.job_no=d.job_no_mst and b.po_break_down_id=d.id and a.booking_type in (1,4)  and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by a.booking_no_prefix_num, a.pay_mode, a.booking_no, a.company_id, a.buyer_id, a.booking_date, a.delivery_date, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved, c.job_no_prefix_num, c.gmts_item_id, c.style_ref_no, d.po_number, d.grouping, d.file_no,b.job_no order by id DESC";
	?>
    <table width="1160" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="60">Booking No</th>
                <th width="60">Booking Date</th>
                <th width="80">Buyer</th>
                <th width="60">Job No</th>
                <th width="90">Style Ref.</th>
                <th width="90">Gmts Item </th>
                <th width="100">PO number</th>
                <th width="80">Internal Ref</th>
                <th width="80">File No</th>
                <th width="80">Fabric Nature</th>
                <th width="80">Fabric Source</th>
                <th width="50">Pay Mode</th>
                <th width="50">Supplier</th>
                <th width="50">Approved</th>
                <th>Ready to Approved</th>
            </tr>
        </thead>
    </table>
    <div style="max-height:300px; overflow-y:scroll; width:1160px" >
        <table width="1140" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" id="list_view">
            <tbody>
            <?
            $sl=1;
            $data=sql_select($sql);
            foreach($data as $row)
            {
				if ($sl%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row[csf("booking_no")]?>')" style="cursor:pointer">
                    <td width="30"><? echo $sl; ?></td>
                    <td width="60"><? echo $row[csf("booking_no_prefix_num")];?></td>
                    <td width="60"><? echo change_date_format($row[csf("booking_date")],"dd-mm-yyyy","-"); ?></td>
                    <td width="80" style="word-break:break-all"><? echo $buyer_arr[$row[csf("buyer_id")]];?></td>
                    <td width="60"><? echo $job_prefix_num[$row[csf("job_no")]];?></td>
                    <td width="90" style="word-break:break-all"><? echo $row[csf("style_ref_no")]; ?></td>
                    <td width="90" style="word-break:break-all"><? echo $garments_item[$row[csf("gmts_item_id")]];?> </td>
                    <td width="100" style="word-wrap: break-word;word-break: break-all;"><? echo $po_array[$row[csf("po_break_down_id")]];?></td>
                    <td width="80" style="word-break:break-all"><? echo $row[csf("grouping")];?></td>
                    <td width="80" style="word-break:break-all"><? echo $row[csf("file_no")];?></td>
                    <td width="80" style="word-break:break-all"><? echo $item_category[$row[csf("item_category")]];?></td>
                    <td width="80" style="word-break:break-all"><? echo $fabric_source[$row[csf("fabric_source")]];?></td>
                    <td width="50" style="word-break:break-all"><? echo $pay_mode[$row[csf("pay_mode")]];?></td>
                    <td width="50" style="word-break:break-all">
                    <?
                    if($row[csf("pay_mode")]==3 || $row[csf("pay_mode")]==5) echo $comp[$row[csf("supplier_id")]]; else echo $suplier[$row[csf("supplier_id")]];
                    ?>
                    </td>
                    <td width="50"><? echo $approved[$row[csf("is_approved")]];?></td>
                    <td><? echo $is_ready[$row[csf("ready_to_approved")]];?></td>
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
if ($action=="load_drop_down_buyer_popup"){
	
	$sql="select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company='$data'  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name";
	//echo $sql;
	echo create_drop_down( "cbo_buyer_name", 150, $sql,"id,buyer_name", 1, "-- Select Buyer --", $selected, "","0","" );
	exit();
}
?>