<?
/*-------------------------------------------- Comments
Version          : V1
Purpose			 : This form will create Fabric Issue For AOP
Functionality	 :
JS Functions	 :
Created by		 : K.M Nazim Uddin
Creation date 	 : 10-01-2021
Requirment Client:
Requirment By    :
Requirment type  :
Requirment       :
Affected page    :
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
include('../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');

include('../../includes/class4/class.conditions.php');
include('../../includes/class4/class.reports.php');
include('../../includes/class4/class.fabrics.php');
include('../../includes/class4/class.conversions.php');

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

if ($action=="load_drop_down_supplier")
{
	echo create_drop_down( "cbo_supplier_name", 172, "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company=$data and b.party_type=25 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name","id,supplier_name", 1, "--Select Supplier--",$selected,"",0 );
	/*$exdata=explode('_',$data);
	if($exdata[0]==5 || $exdata[0]==3)
	{
	   echo create_drop_down( "cbo_supplier_name", 172, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "--Select Company--", $selected, "get_php_form_data( this.value, 'load_drop_down_attention', 'requires/fabric_issue_for_aop_entry_controller');",0,"" );
	}
	else
	{

	   echo create_drop_down( "cbo_supplier_name", 172, "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company=$exdata[1] and b.party_type=25 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name","id,supplier_name", 1, "--Select Supplier--",$selected,"get_php_form_data( this.value, 'load_drop_down_attention', 'requires/fabric_issue_for_aop_entry_controller');",0 );
	}*/
	exit();
}


if($action=="return_supplier_address")
{
	$address=return_field_value( "address_1","lib_supplier","id='$data'");
	echo $address;
	exit();	
}

if ($action=="fabric_search_popup")
{
	echo load_html_head_contents("Order Search","../../", 1, 1, $unicode);
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
			                        <tr style="display: none;">
			                        <tr>
			                            <th colspan="11" align="center"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --",1 ); ?></th>
			                        </tr>
			                        <tr>
			                            <th width="140">PO Company Name</th>
			                            <th width="80">Within Group</th>
			                            <th width="120">Buyer Name</th>
			                            <th width="120">Sub Con Supplier</th>
			                            <th width="70">Search By</th>
			                            <th width="70">Please Enter</th>
			                            <th width="170" colspan="2">Batch Date Range</th>
			                            <th>&nbsp;</th>
			                        </tr>
			                    </thead>
			                    <tr>
			                        <td><? echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "- Select Company -", str_replace("'","",$cbo_company_name), "load_drop_down( 'partial_fabric_booking_controller', this.value, 'load_drop_down_buyer_popup', 'buyer_td' );","1"); ?>
			                        </td>
			                        <td><?php echo create_drop_down( "cbo_within_group", 80, $yes_no,"", 0, "--  --",str_replace("'","",$within_group), "","1" ); ?></td>
			                        <td id="buyer_td">
			                        <? echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company=$cbo_company_name and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", str_replace("'","",$buyer_id), "","1" ); ?>
			                        </td>
			                        <td><? echo create_drop_down( "cbo_supplier_name", 120, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=25 and   a.status_active =1 and a.is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", str_replace("'","",$cbo_supplier_name), "",1 ); ?> </td>
			                        <td>
									<?
			                            $search_by_arr=array(1=>"Batch no",2=>"FSO No",3=>"Fabric booking no",4=>"Style reference no");
			                            echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'',0 );
			                        ?>
			                   		</td>
			                   		<td><input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" /></td>
			                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" value=""/></td>
			                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" value=""/></td>
			                        <td align="center">
			                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $sales_order_type;?>, 'fabric_search_list_view', 'search_div', 'fabric_issue_for_aop_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" /> 
			                        </td>
			                    </tr>
			                    <tr>
			                    	<td align="center" valign="middle" colspan="9"><? echo load_month_buttons(1); ?>
			                    		<input type="hidden" class="text_boxes" readonly style="width:550px" id="txtSoDtlsId">
			                        	<input type="hidden" id="txtPreCostDtlsId">
			                        	<input type="hidden" id="txtBatchDtlsId">
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
					
					selected_id.push( $('#txt_so_dtls_id' + str).val() );
					selected_item.push($('#pre_cost_dtls_id' + str).val());
					selected_po.push($('#txt_batch_dtls_id' + str).val());
				}
				else{
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == $('#txt_so_dtls_id' + str).val() ) break;
					}
					selected_id.splice( i, 1 );
					selected_item.splice( i,1 );
					selected_po.splice( i,1 );
				}
			}
			var id = '';
			var pre_cost_dtls_id='';
			var txt_batch_dtls_id='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				pre_cost_dtls_id+=selected_item[i]+ ',';
				txt_batch_dtls_id+=selected_po[i]+ ',';
			}
			id = id.substr( 0, id.length - 1 );
			pre_cost_dtls_id = pre_cost_dtls_id.substr( 0, pre_cost_dtls_id.length - 1 );
			txt_batch_dtls_id = txt_batch_dtls_id.substr( 0, txt_batch_dtls_id.length - 1 );
			$('#txtSoDtlsId').val( id );
			$('#txtPreCostDtlsId').val( pre_cost_dtls_id );
			$('#txtBatchDtlsId').val( txt_batch_dtls_id );
		}
	</script>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="fabric_search_list_view")
{
	$data=explode('_',$data);
	$company=$data[0];
	$buyer=$data[1];
	$cbo_within_group=$data[2];
	$supplier_name=$data[3];

	$search_category=$data[4];
	$search_string=$data[5];
	$date_from=$data[6];
	$date_to=$data[7];
	$without_order=$data[8];
	
	if ($company!=0)
	{
		if($cbo_within_group==1 ){
			if ($without_order !=1){
				$company_cond=" and f.company_id='$company'";
				if ($supplier_name!=0) $supplier_cond=" and f.supplier_id='$supplier_name'"; else $supplier_cond='';
			}
			else{
				$company_cond=" and f.company_id='$company'";
			}
			if ($buyer!=0) $buyer_cond=" and f.buyer_id='$buyer'"; else $buyer_cond='';
		}
		else{
			$company_cond=" and c.company_id='$company'";
			if ($buyer!=0) $buyer_cond=" and c.buyer_id='$buyer'"; else $buyer_cond='';
		}
	}
	else 
	{ 
		echo "Please Select Company First."; die;
	}
	 
	//if ($data[1]!=0) $buyer=" and f.buyer_id='$data[1]'"; else $buyer="";
	//if ($data[4]!=0) $supplier=" and f.supplier_id='$data[4]'"; else $supplier="";


	//if ($company!=0) $company_cond=" and c.company_id='$company'"; else { echo "Please Select Company First."; die; }
	
	
	if ($cbo_within_group!=0) $within_group_cond=" and c.within_group='$cbo_within_group'"; else $within_group_cond='';
	//if ($cbo_currency!="") $currency_cond=" and a.currency_id='$cbo_currency'"; else{ echo "Please Select Currency First."; die; }
	if($db_type==0){
		if ($date_from!="" &&  $date_to!="") $batch_date = "and e.batch_date between '".change_date_format($date_from, "yyyy-mm-dd", "-")."' and '".change_date_format($date_to, "yyyy-mm-dd", "-")."'"; else $batch_date ="";
	}
	else if($db_type==2){
		if ($date_from!="" &&  $date_to!="") $batch_date = "and e.batch_date between '".change_date_format($date_from, "yyyy-mm-dd", "-",1)."' and '".change_date_format($date_to, "yyyy-mm-dd", "-",1)."'"; else $batch_date ="";
	}
	if($db_type==0) $year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$cbo_job_year";
	if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_job_year";
	if ($search_string!=''){
		if($search_category==1) $search_cond=" and e.batch_no like '%$search_string%'";
		if($search_category==2) $search_cond=" and c.job_no like '%$search_string%'";
		if($search_category==3) $search_cond=" and c.sales_booking_no like '%$search_string%'";
		if($search_category==4) $search_cond=" and c.style_ref_no like '%$search_string%'";
	}
	//,d.id as batch_dtls_id
	if($cbo_within_group==1 ){
		if($without_order==0)
		{
			$sql= "select f.id,f.company_id, f.buyer_id,a.job_no,c.style_ref_no,c.job_no as fso_number, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, b.color_id, b.body_part_id , b.fabric_desc, b.gsm_weight, b.dia, sum(d.batch_qnty) as batch_weight ,b.id as so_dtls_id,e.id as batch_id from 
			wo_pre_cost_fabric_cost_dtls a, fabric_sales_order_dtls b, fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e, wo_booking_mst f , product_details_master g where a.id=b.pre_cost_fabric_cost_dtls_id and b.mst_id=c.id and c.id=d.po_id and d.mst_id=e.id and e.color_id=b.color_id and f.tagged_booking_no = c.sales_booking_no and d.prod_id=g.id and g.item_description=b.fabric_desc and f.booking_type=3 and f.process=35 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1  $company_cond $buyer_cond $within_group_cond $batch_date $supplier_cond $search_cond group by  f.id,f.company_id, f.buyer_id,a.job_no,c.style_ref_no,c.job_no, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, b.color_id, b.body_part_id , b.fabric_desc, b.gsm_weight, b.dia,b.id,e.id order by f.id desc";
		}else{
			$sql= "select c.id,f.company_id, f.buyer_id,c.style_ref_no,c.job_no as fso_number, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, b.color_id, b.body_part_id , b.fabric_desc, b.gsm_weight, b.dia, d.batch_qnty as batch_weight ,b.id as so_dtls_id,e.id as batch_id from 
			 fabric_sales_order_dtls b, fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e, wo_non_ord_samp_booking_mst f  where  b.mst_id=c.id and c.id=d.po_id and d.mst_id=e.id and f.booking_no = c.sales_booking_no and e.color_id=b.color_id and f.booking_type=4 and f.pay_mode=5 and f.supplier_id=c.company_id  and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 $company_cond $buyer_cond $within_group_cond $batch_date $supplier_cond $search_cond group by  c.id,f.company_id, f.buyer_id,c.style_ref_no,c.job_no, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, b.color_id, b.body_part_id , b.fabric_desc, b.gsm_weight, b.dia,b.id,e.id, d.batch_qnty order by c.id desc";
		}
	}else{
		$sql= "select c.id,c.company_id, c.buyer_id,c.style_ref_no,c.job_no as fso_number, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, b.color_id, b.body_part_id , b.fabric_desc, b.gsm_weight, b.dia, sum(d.batch_qnty) as batch_weight ,b.id as so_dtls_id,e.id as batch_id   from 
		 fabric_sales_order_dtls b, fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e , product_details_master  g where  b.mst_id=c.id and c.id=d.po_id and d.mst_id=e.id and e.color_id=b.color_id and d.prod_id=g.id  and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1  $company_cond $buyer_cond $within_group_cond $batch_date $search_cond group by  c.id,c.company_id, c.buyer_id,c.style_ref_no,c.job_no, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, b.color_id, b.body_part_id , b.fabric_desc, b.gsm_weight, b.dia,b.id,e.id order by c.id desc";
		 // $supplier_cond  and g.item_description=b.fabric_desc
	}
	
	//echo $sql;

	//pro_batch_create_dtls -> po_id id ->fabric_sales_order_mst   wo_pre_cost_fabric_cost_dtls -> id  pre_cost_fabric_cost_dtls_id-> fabric_sales_order_dtls
	$sql_data=sql_select($sql);//. $currency_cond
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$color_library=return_library_array("select id,color_name from lib_color", "id", "color_name");
	?>
    <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1400" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="150">PO Company Name</th>
                <th width="150">PO Buyer Name</th>
                <th width="100">Style Ref. No</th>
                <th width="100">FSO No.</th>
                <th width="100">Fab. Booking No.</th>
                <th width="100">Batch No</th>
                <th width="70">Ext. No</th>
                <th width="70">Fab. Color</th>
                <th width="80">Body Part</th>
                <th width="200">Fab. Description</th>
                <th width="70">GSM</th>
                <th width="70">DIA</th>
                <th width="">Batch Wgt.</th>
            </thead>
     	</table>
     </div>
     <div style="width:1400px; max-height:270px;overflow-y:scroll;" >
     <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1380" class="rpt_table" id="list_view">
    <?
	$i=1;
	foreach($sql_data as $sql_row)
	{
		?>
        <tr style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
            <td width="30"><? echo $i; ?>
                <input type="hidden" name="txt_so_dtls_id" id="txt_so_dtls_id<?php echo $i ?>" value="<? echo $sql_row[csf('so_dtls_id')]; ?>"/>
                <input type="hidden" name="txt_pre_cost_dtls_id" id="txt_pre_cost_dtls_id<?php echo $i ?>" value="<? echo $sql_row[csf('pre_cost_dtls_id')]; ?>"/>
                <input type="hidden" name="txt_batch_dtls_id" id="txt_batch_dtls_id<?php echo $i ?>" value="<? echo $sql_row[csf('batch_id')]; ?>"/>
                <input type="hidden" name="txt_batch_dtls_id" id="txt_batch_dtls_id<?php echo $i ?>" value="<? echo $sql_row[csf('batch_dtls_id')]; ?>"/>
            </td>
            <td width="150"><p><? echo $company_library[$sql_row[csf('company_id')]]; ?></p></td>
            <td width="150"><p><? echo $buyer_arr[$sql_row[csf('buyer_id')]]; ?></p></td>
            <td width="100"><p><? echo $sql_row[csf('style_ref_no')]; ?></p></td>
            <td width="100"><p><? echo $sql_row[csf('fso_number')]; ?></p></td>
            <td width="100"><p><? echo $sql_row[csf('sales_booking_no')]; ?></p></td>
            <td width="100"><p><? echo $sql_row[csf('batch_no')]; ?></p></td>
            <td width="70"><p><? echo $sql_row[csf('extention_no')]; ?></p></td>
            <td width="70"><p><? echo $color_library[$sql_row[csf('color_id')]]; ?></p></td>
            <td width="80"><p><? echo $body_part[$sql_row[csf('body_part_id')]]; ?></p></td>
            <td width="200"><p><? echo $sql_row[csf('fabric_desc')]; ?></p></td>
            <td width="70"><p><? echo $sql_row[csf('gsm_weight')]; ?></p></td>
            <td width="70"><p><? echo $sql_row[csf('dia')]; ?></p></td>
            <td width=""><p><? echo $sql_row[csf('batch_weight')]; ?></p></td>
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
		echo "load_drop_down( 'requires/fabric_issue_for_aop_entry_controller', '".$row[csf("job_no")]."', 'load_drop_down_fabric_description', 'fabric_description_td' )\n";
	}
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 172 , "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
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

if ($action=="generate_aop_booking")
{
	$data=explode('**',$data);
	$is_update=$data[0];
	if($is_update==2){
		$mst_id=$data[1];
	}else{
		$txtSoDtlsId=$data[1];
	}
	$txtPreCostDtlsId=$data[2];
	$txtBatchMstId=$data[3];
	$txt_order_no=$data[4];
	$within_group=$data[5];
	$without_order=$data[6];
	$fab_booking=$data[7];
	
	if($is_update==1) // save
	{
		if($within_group==1){
			if($without_order==0)
			{
				$sql= "select f.id,f.company_id, f.buyer_id,a.job_no,c.style_ref_no,c.job_no as fso_number, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, b.color_id, b.body_part_id , b.fabric_desc, b.gsm_weight, b.dia, sum(d.roll_no) as number_of_roll ,sum(d.batch_qnty) as batch_weight ,b.id as so_dtls_id,e.id as batch_id from 
				wo_pre_cost_fabric_cost_dtls a, fabric_sales_order_dtls b, fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e, wo_booking_mst f , product_details_master  g where a.id=b.pre_cost_fabric_cost_dtls_id and b.mst_id=c.id and c.id=d.po_id and d.mst_id=e.id and e.color_id=b.color_id and f.tagged_booking_no = c.sales_booking_no and d.prod_id=g.id and g.item_description=b.fabric_desc and f.booking_type=3 and f.process=35 and f.booking_no='$txt_order_no' and b.id in ($txtSoDtlsId)  and e.id in ($txtBatchMstId) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 group by  f.id,f.company_id, f.buyer_id,a.job_no,c.style_ref_no,c.job_no, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, b.color_id, b.body_part_id , b.fabric_desc, b.gsm_weight, b.dia ,b.id,e.id order by b.id desc";
			}else
			{
				$sql= "select f.id,f.company_id, f.buyer_id,c.style_ref_no,c.job_no as fso_number, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, b.color_id, b.body_part_id , b.fabric_desc, b.gsm_weight, b.dia ,d.batch_qnty as batch_weight ,b.id as so_dtls_id,e.id as batch_id from 
				 fabric_sales_order_dtls b, fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e, wo_non_ord_samp_booking_mst f , product_details_master  g where b.mst_id=c.id and c.id=d.po_id and d.mst_id=e.id and e.color_id=b.color_id  and f.booking_no = c.sales_booking_no and f.booking_type=4 and f.pay_mode=5 and f.supplier_id=c.company_id  and d.prod_id=g.id and f.booking_no='$fab_booking' and b.id in ($txtSoDtlsId)  and e.id in ($txtBatchMstId) and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 group by  f.id,f.company_id, f.buyer_id,c.style_ref_no,c.job_no, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, b.color_id, b.body_part_id , b.fabric_desc, b.gsm_weight, b.dia ,b.id,e.id,d.batch_qnty order by b.id desc";
				 // and g.item_description=b.fabric_desc

			}
		}
		else
		{
			$sql= "select c.id,c.company_id, c.buyer_id,c.style_ref_no,c.job_no as fso_number, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, b.color_id, b.body_part_id , b.fabric_desc, b.gsm_weight, b.dia, sum(d.roll_no) as number_of_roll ,sum(d.batch_qnty) as batch_weight ,b.id as so_dtls_id,e.id as batch_id from 
		fabric_sales_order_dtls b, fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e , product_details_master  g where  b.mst_id=c.id and c.id=d.po_id and d.mst_id=e.id and e.color_id=b.color_id and d.prod_id=g.id  and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and b.id in ($txtSoDtlsId)  and e.id in ($txtBatchMstId) group by  c.id,c.company_id, c.buyer_id,c.style_ref_no,c.job_no, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, b.color_id, b.body_part_id , b.fabric_desc, b.gsm_weight, b.dia ,b.id,e.id order by c.id desc";
		//and g.item_description=b.fabric_desc
		}
	}
	else
	{ 
		$sql= "select c.id,c.company_id, c.buyer_id,c.style_ref_no,c.job_no as fso_number, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, b.color_id, b.body_part_id , b.fabric_desc, b.gsm_weight, b.dia ,b.id as so_dtls_id,e.id as batch_id,g.id as dtls_id, g.process_type_id, g.quantity, g.rate, g.amount, g.number_of_roll, d.batch_qnty as batch_weight, g.remark  from 
		fabric_sales_order_dtls b, fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e, wo_fabric_aop_dtls g ,product_details_master  h where b.mst_id=c.id and c.id=d.po_id and d.mst_id=e.id and g.sales_order_dtls_id=b.id and g.batch_id=e.id and e.color_id=b.color_id and d.prod_id=h.id and g.mst_id=$mst_id and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and g.status_active=1 and g.is_deleted=0 group by  c.id,c.company_id, c.buyer_id,c.style_ref_no,c.job_no, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, b.color_id, b.body_part_id , b.fabric_desc, b.gsm_weight, b.dia ,b.id,e.id,g.id, g.process_type_id, g.quantity, g.rate, g.amount, g.number_of_roll, g.remark, d.batch_qnty order by b.id desc";
		//and h.item_description=b.fabric_desc 
	}
	$prev_qty_sql= "select b.id as so_dtls_id,e.id as batch_id, g.quantity from 
		 fabric_sales_order_dtls b, fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e, wo_fabric_aop_dtls g where  b.mst_id=c.id and c.id=d.po_id and d.mst_id=e.id and g.sales_order_dtls_id=b.id and f.id=g.order_id and g.batch_id=e.id and e.color_id=b.color_id  and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and g.status_active=1 and g.is_deleted=0 group by b.id,e.id,g.quantity";
	$prev_qty_sqlArray=sql_select($prev_qty_sql); $prev_issue_qty_arr=array();
	foreach ($prev_qty_sqlArray as  $row) 
	{
		//echo $mstIDS.'=='.$ids.'=='.$book_con_dtls_ids.'++'; 
		$prev_issue_qty_arr[$row[csf("batch_id")]][$row[csf("so_dtls_id")]]["quantity"] +=$row[csf("quantity")];
	}

	echo $sql;

	//pro_batch_create_dtls -> po_id id ->fabric_sales_order_mst   wo_pre_cost_fabric_cost_dtls -> id  pre_cost_fabric_cost_dtls_id-> fabric_sales_order_dtls
	$sql_data=sql_select($sql);//. $currency_cond
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$color_library=return_library_array("select id,color_name from lib_color", "id", "color_name");
	
	$i=1;
	foreach($sql_data as $sql_row)
	{
		$prev_issue_qty=$balance=0;
		$prev_issue_qty=$prev_issue_qty_arr[$sql_row[csf("batch_id")]][$sql_row[csf("so_dtls_id")]]["quantity"];
		if($is_update==1){
			$balance=$sql_row[csf('batch_weight')]-$prev_issue_qty;
		}else{
			$balance=($sql_row[csf('batch_weight')]-$prev_issue_qty)+$sql_row[csf('quantity')];
		}
		$number_of_roll='';
		if($is_update!=1)
		{
			$number_of_roll=$sql_row[csf('number_of_roll')];
		}

		?>
		<tr align="center" id="row_<? echo $i;?>">
			<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"><? echo $i; ?></td>
			<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"><? echo $buyer_arr[$sql_row[csf('buyer_id')]]; ?></td>
			<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"><? echo $sql_row[csf('style_ref_no')]; ?></td>
			<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"><? echo $sql_row[csf('fso_number')]; ?></td>
			<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"><? echo $sql_row[csf('sales_booking_no')]; ?></td>
			<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"><? echo $sql_row[csf('batch_no')]; ?></td>
			<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"><? echo $color_library[$sql_row[csf('color_id')]]; ?></td>
			<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"><? echo $body_part[$sql_row[csf('body_part_id')]]; ?></td>
			<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"><? echo $sql_row[csf('fabric_desc')]; ?></td>
			<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"><? echo $sql_row[csf('gsm_weight')]; ?></td>
			<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"><? echo $sql_row[csf('dia')]; ?></td>
			<td width="120" ><? 
			asort($emblishment_print_type);
			echo create_drop_down( "cboProcessType_".$i, 120, $emblishment_print_type,"", 1, "-- Select --",$sql_row[csf('process_type_id')],'','','','','','','','',"cboProcessType[]"); ?></td>
			<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"><? echo $sql_row[csf('batch_weight')]; ?></td>
			<td><input type="text" name="txtWoqnty[]" id="txtWoqnty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="calculate_amount(<? echo $i; ?>)" value="<? echo $sql_row[csf('quantity')]; ?>"; placeholder="<? echo $balance; ?>" /></td>
			<td><input type="text" name="txtRate[]" id="txtRate_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="calculate_amount(<? echo $i; ?>)" value="<? echo $sql_row[csf('rate')]; ?>";></td>
			<td><input type="text" name="txtAmount[]" id="txtAmount_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" value="<? echo number_format($sql_row[csf('amount')],4,'.',''); ?>";  disabled="disabled"/>

			<td><input type="text" name="txtNumberRoll[]"  id="txtNumberRoll_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" value="<? echo $number_of_roll; ?>" /></td>
			<td><input type="text" name="txtRemarks[]"  id="txtRemarks_<? echo $i; ?>" style="width:60px;" class="text_boxes" value="<? echo $sql_row[csf('remark')]; ?>";/></td>
			<td><input type="button" id="decreaseset_<? echo $k;?>" name="decreaseset[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deletebreak_down_tr(<? echo $i;?>,'tbl_dtls_emb','row_');"  <? echo $disabled; ?>  />
			
			<input type="hidden" id="soDtlsId_<? echo $k;?>" name="soDtlsId[]"  value="<? echo $sql_row[csf('so_dtls_id')]; ?>"  />
			<input type="hidden" id="batchDtlsId_<? echo $k;?>" name="batchDtlsId[]"  value="<? echo $sql_row[csf('batch_dtls_id')]; ?>"  />
			<input type="hidden" id="hiddenid_<? echo $k;?>" name="hiddenid[]"  value="<? echo $sql_row[csf('dtls_id')]; ?>"  /></td>
			<input type="hidden" id="batchId_<? echo $k;?>" name="batchId[]"  value="<? echo $sql_row[csf('batch_id')]; ?>"  /></td>
		</tr>
		<?
		$i++;
	}
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
	if($fabric_source_aop_id==0 || $fabric_source_aop_id=='') $fabric_source_aop_id=0;else $fabric_source_aop_id=$fabric_source_aop_id;

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
	$sql="select a.id, a.pre_cost_fabric_cost_dtls_id, a.artwork_no, a.po_break_down_id, a.color_size_table_id, a.fabric_color_id, a.item_size, a.process, sensitivity, a.job_no, booking_no, a.booking_type, a.description, a.uom, a.delivery_date, a.delivery_end_date, a.sensitivity, a.wo_qnty, a.rate, a.amount, a.gmts_size, a.gmts_color_id, a.dia_width, a.printing_color_id from wo_booking_dtls a  where  a.booking_type=3 and a.process=35 and a.is_short=2  and a.pre_cost_fabric_cost_dtls_id in($data[4]) and a.status_active=1 and a.is_deleted=0 ";
	$dataArray=sql_select($sql);
	$z=1; $i=1;
	foreach($dataArray as $row)
	{
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
		$cu_booking_data_arr[$pre_cost_conversion_cost_dtls_id][$po_break_down_id][$color_number_id][$dia_width]['amount']+=$amount;
	}
	//print_r($cu_booking_data_arr[1850]);

	$booking_data_arr=array();
	$sql="select a.id, a.pre_cost_fabric_cost_dtls_id, a.artwork_no, a.po_break_down_id, a.color_size_table_id, a.fabric_color_id, a.item_size, a.process, a.sensitivity, a.job_no, booking_no, a.booking_type, a.description, a.uom, a.delivery_date, a.delivery_end_date, a.wo_qnty, a.rate, a.amount, a.gmts_size, a.gmts_color_id, a.fin_dia, a.dia_width, a.printing_color_id from wo_booking_dtls a  where  a.booking_type=3 and a.process=35 and a.booking_no='$txt_booking_no'  and a.pre_cost_fabric_cost_dtls_id=$data[4]  and   a.status_active=1 and a.is_deleted=0 ";
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
		//echo $pre_cost_conversion_cost_dtls_id.'=='.$po_break_down_id.'=='.$color_number_id.'=='.$dia_width; die;
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
	/*echo '<pre>';
	print_r($booking_data_arr); die;*/

	$condition= new condition();
	if(str_replace("'","",$txt_order_no_id) !=''){
		$condition->po_id("in($txt_order_no_id)");
	}
	$condition->init();
	//$fabric= new fabric($condition);
	//$req_qty_arr=$fabric->getQtyArray_by_OrderFabriccostidGmtscolorAndDiaWidth_knitAndwoven_greyAndfinish();
	//$req_amount_arr=$fabric->getAmountArray_by_OrderFabriccostidGmtscolorAndDiaWidth_knitAndwoven_greyAndfinish();
	//Issue ID=7561 , As Per Rasel Vai
	$conversion= new conversion($condition);
	$req_qty_arr=$conversion->getQtyArray_by_ConversionidOrderColorDiaWidthAndUom();
	$req_amount_arr=$conversion->getAmountArray_by_ConversionidOrderColorDiaWidthAndUom();


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

		$budget_rate=0;
		if($job_wise_currency_exrate_arr[$job_no]['currency']!=$currency_id)
		{
			if($currency_id==1) $budget_rate=$charge_unit*$job_wise_currency_exrate_arr[$job_no]['exrate'];
			else $budget_rate=$charge_unit;
		} else $budget_rate=$charge_unit;

		/*if($cbo_fabric_natu==2){
			$wo_req_qnty = $req_qty_arr['knit']['grey'][$po_break_down_id][$pre_cost_fabric_cost_dtls_id][$color_number_id][$dia_width][$uom];
			$wo_reqAmount = $req_amount_arr['knit']['grey'][$po_break_down_id][$pre_cost_fabric_cost_dtls_id][$color_number_id][$dia_width][$uom];
		}
		if($cbo_fabric_natu==3){
			$wo_req_qnty = $req_qty_arr['woven']['grey'][$po_break_down_id][$pre_cost_fabric_cost_dtls_id][$color_number_id][$dia_width][$uom];
			$wo_reqAmount = $req_amount_arr['woven']['grey'][$po_break_down_id][$pre_cost_fabric_cost_dtls_id][$color_number_id][$dia_width][$uom];
		}*/
		//echo $po_break_down_id.'='.$pre_cost_fabric_cost_dtls_id.'='.$color_number_id.'='.$dia_width.'='.$uom; $conv_cost_id
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
			$woqnty      = $wo_req_qnty - $process_loss_qty;
			$selected_uom     = $uom;
		}

		if($body_part_id==2 || $body_part_id==3){
			$rate   = 0;
			$amount = 0;
			$bamount=0;
		}
		else{
			$rate   = $charge_unit;
			$amount = $rate*$woqnty;
		}
		$budget_amt=$budget_rate*$woqnty;

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
		$po_color_level_data_arr[$pre_cost_conversion_cost_id][$color_number_id][$dia_width]['budget_amt'][$po_break_down_id]          = $budget_amt;
	}
	?>
    <div id="content_search_panel_<? echo $pre_cost_conversion_cost_id; ?>" style="" class="accord_close">
        <table class="rpt_table" border="1" width="1560" cellpadding="0" cellspacing="0" rules="all" id="tbl_table" style="table-layout: fixed;">
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
								$budget_amt                  = def_number_format($dia_width_val['budget_amt'][$po_id],1,"");
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
                /*echo "<pre>";
                print_r($po_color_level_data_arr); die;*/
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
							$budget_amt                  = def_number_format(array_sum($dia_width_val['budget_amt']),1,"");
                            $wo_cu_wo_qnty               = def_number_format(array_sum($dia_width_val['cu_wo_qnty']),1,"");
							$cu_wo_amt               	 = def_number_format(array_sum($dia_width_val['cu_wo_amt']),1,"");
                            $rate                        = def_number_format($amount/$woqnty,1,"");
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
		exit();
}

if ($action=="save_update_delete"){
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$ready_to_approved = str_replace(",", "", $cbo_ready_to_approved);

	/*if(str_replace("'","",$txt_booking_no)!='')
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
	}*/

	if ($operation==0){
		$con = connect();
		if($db_type==0){
			mysql_query("BEGIN");
		}

		$response_booking_no="";
		if($db_type==0){
			$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'FIFA', date("Y",time()), 5, "select id, sys_no_prefix, sys_no_prefix_num from wo_fabric_aop_mst where company_id=$cbo_company_name and entry_form=462 and YEAR(insert_date)=".date('Y',time())." order by id desc ", "sys_no_prefix", "sys_no_prefix_num" ));
		}
		else if($db_type==2){
			$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'FIFA', date("Y",time()), 5,"select id, sys_no_prefix, sys_no_prefix_num from wo_fabric_aop_mst where company_id=$cbo_company_name and entry_form=462 and to_char(insert_date,'YYYY')=".date('Y',time())." order by id desc ", "sys_no_prefix", "sys_no_prefix_num" ));
		}

		$id=return_next_id( "id", "wo_fabric_aop_mst", 1 ) ;
		$field_array="id, entry_form, sys_no_prefix, sys_no_prefix_num, sys_no, sys_date, delivery_date, company_id, supplier_id, supplier_address, issue_purpose, source, order_id, order_no, attention, vehical_no, driver_name, dl_no, transport, mobile_no, gate_pass_no, is_short, remark,buyer_id, within_group, lc_company_name, fab_booking_no,order_type, inserted_by, insert_date"; 

		$data_array ="(".$id.", 462 ,'".$new_booking_no[1]."',".$new_booking_no[2].",'".$new_booking_no[0]."',".$txt_issue_date.",".$txt_delivery_date.",".$cbo_company_name.",".$cbo_supplier_name.",".$txt_party_location.",".$cbo_issue_purpose.",".$cbo_source.",".$hid_order_id.",".$txt_order_no.",".$txt_attention.",".$txt_vehical_no.",".$txt_driver_name.",".$txt_dl_no.",".$txt_transport.",".$txt_cell_no.",".$txt_gate_pass_no.",".$cbo_is_short.",".$txt_remarks.",".$buyer_id.",".$within_group.",".$cbo_lc_company_name.",".$hid_fab_booking.",".$sales_order_type.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

		$response_system_no=$new_booking_no[0];
		//echo "10**INSERT INTO wo_fabric_aop_mst (".$field_array.") VALUES ".$data_array; die;
		$rID=sql_insert("wo_fabric_aop_mst",$field_array,$data_array,1);
		//$rID=sql_insert("wo_fabric_aop_mst",$field_array,$data_array,0);
		//echo "10**".$rID; die;
		if($db_type==0)
		{
			if($rID==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$response_system_no."**".$id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$response_system_no."**".$id;
			}
		}
		else if($db_type==2)
		{
			if($rID==1)
			{
				oci_commit($con);
				echo "0**".$response_system_no."**".$id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$response_system_no."**".$id;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1){
		$con = connect();
		$is_approved=0;
		
		if($db_type==0){
		mysql_query("BEGIN");
		}

		$field_array_up="sys_date*delivery_date*supplier_id*supplier_address*issue_purpose*source*order_id*order_no*attention*vehical_no*driver_name*dl_no*transport*mobile_no*gate_pass_no*is_short*remark*updated_by*update_date";

		$data_array_up ="".$txt_issue_date."*".$txt_delivery_date."*".$cbo_supplier_name."*".$txt_party_location."*".$cbo_issue_purpose."*".$cbo_source."*".$hid_order_id."*".$txt_order_no."*".$txt_attention."*".$txt_vehical_no."*".$txt_driver_name."*".$txt_dl_no."*".$txt_transport."*".$txt_cell_no."*".$txt_gate_pass_no."*".$cbo_is_short."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$rID=sql_update("wo_fabric_aop_mst",$field_array_up,$data_array_up,"id","".$update_id."",0);
		
		if($db_type==0){
			if($rID){
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$txt_system_id).'**'.str_replace("'","",$update_id);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_system_id).'**'.str_replace("'","",$update_id);
			}
		}
		else if($db_type==2 || $db_type==1 ){
			if($rID){
				oci_commit($con);
				echo "1**".str_replace("'","",$txt_system_id).'**'.str_replace("'","",$update_id);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_system_id).'**'.str_replace("'","",$update_id);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2){
		/*$con = connect();
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
		die;*/
	}
}


if ($action=="save_update_delete_dtls")
{
	$color_library=return_library_array( "select id,color_name from lib_color where status_active =1 and is_deleted=0", "id", "color_name"  );
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	/*if(str_replace("'","",$txt_booking_no)!='')
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
	}*/

	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}


		$id_dtls=return_next_id( "id", "wo_fabric_aop_dtls", 1 ) ;
		$field_array1=" id, mst_id, order_id, batch_id, sales_order_dtls_id, batch_dtls_id, process_type_id, quantity, rate, amount, number_of_roll, remark, inserted_by, insert_date";

		//$update_id=1; 
		$add_commaa=0;
		for($i=1; $i<=$total_row; $i++)
		{			
			$cboProcessType		= "cboProcessType_".$i; 
			$txtWoqnty			= "txtWoqnty_".$i;
			$txtRate			= "txtRate_".$i;
			$txtAmount			= "txtAmount_".$i;
			$txtNumberRoll		= "txtNumberRoll_".$i;
			$txtRemarks			= "txtRemarks_".$i;
			$soDtlsId			= "soDtlsId_".$i;
			$batchDtlsId		= "batchDtlsId_".$i;
			$batchId			= "batchId_".$i;
			$hiddenid			= "hiddenid_".$i; 
			
			if ($add_commaa!=0) $data_array1 .=","; $add_comma=0;
			$data_array1 .="(".$id_dtls.",".$update_id.",".$hid_order_id.",".$$batchId.",".$$soDtlsId.",".$$batchDtlsId.",".$$cboProcessType.",".str_replace(",",'',$$txtWoqnty).",".str_replace(",",'',$$txtRate).",".str_replace(",",'',$$txtAmount).",".$$txtNumberRoll.",".$$txtRemarks.",'".$user_id."','".$pc_date_time."')";
			$id_dtls=$id_dtls+1; $add_commaa++;
			//echo "10**INSERT INTO subcon_ord_breakdown (".$field_array3.") VALUES ".$data_array3; die;			
		}
		//echo "10**INSERT INTO wo_fabric_aop_dtls (".$field_array1.") VALUES ".$data_array1;die;
		$rID=sql_insert("wo_fabric_aop_dtls",$field_array1,$data_array1,0);
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");
				echo "0**".str_replace("'",'',$update_id);
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
				echo "0**".str_replace("'",'',$update_id);
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
		
		$field_array_up1="order_id*batch_id*sales_order_dtls_id*batch_dtls_id*process_type_id*quantity*rate*amount*number_of_roll*remark*status_active*is_deleted*updated_by*update_date";
		//$update_id=1; 
		$add_commaa=0;
		for($i=1; $i<=$total_row; $i++)
		{			
			$cboProcessType		= "cboProcessType_".$i; 
			$txtWoqnty			= "txtWoqnty_".$i;
			$txtRate			= "txtRate_".$i;
			$txtAmount			= "txtAmount_".$i;
			$txtNumberRoll		= "txtNumberRoll_".$i;
			$txtRemarks			= "txtRemarks_".$i;
			$soDtlsId			= "soDtlsId_".$i;
			$batchDtlsId		= "batchDtlsId_".$i;
			$batchId			= "batchId_".$i;
			$updateDtlsId		= "hiddenid_".$i; 

			if(str_replace("'",'',$$updateDtlsId)!=""){
				$id_arr[]=str_replace("'",'',$$updateDtlsId);
				$data_array_up1[str_replace("'",'',$$updateDtlsId)] =explode("*",("".$hid_order_id."*".$$batchId."*".$$soDtlsId."*".$$batchDtlsId."*".$$cboProcessType."*".str_replace(",",'',$$txtWoqnty)."*".str_replace(",",'',$$txtRate)."*".str_replace(",",'',$$txtAmount)."*".$$txtNumberRoll."*".$$txtRemarks."*1*0*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
		}

		$field_array_status = "updated_by*update_date*status_active*is_deleted";
		$data_array_status = $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*0*1";
		$rID = sql_multirow_update("wo_fabric_aop_dtls", $field_array_status, $data_array_status, "mst_id", $update_id, 1);

		$rID1=execute_query(bulk_update_sql_statement( "wo_fabric_aop_dtls", "id", $field_array_up1, $data_array_up1, $id_arr ),1);
        //check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID==1 && $rID1==1){
				mysql_query("COMMIT");
				echo "1**".str_replace("'",'',$update_id);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{


			if($rID==1 && $rID1==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$update_id);
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
		/*$con = connect();
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
		die;*/
	}
}

if ($action=="issue_popup")
{
	echo load_html_head_contents("Issue Search","../../", 1, 1, $unicode);
	extract($_REQUEST);
 	?>
	<script>
	function set_checkvalue(){
		if(document.getElementById('chk_job_wo_po').value==0) document.getElementById('chk_job_wo_po').value=1;
		else document.getElementById('chk_job_wo_po').value=0;
	}
	function js_set_value(wo_no,id)
	{
		document.getElementById('selected_booking').value=wo_no;
		document.getElementById('selected_job').value=id;
		parent.emailwindow.hide();
	}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
           <table width="100%" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
                <thead>
                    <tr style="display: none;">
                        <th colspan="11"><? echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --" ); ?></th>
                    </tr>
                    <tr>
                        <th width="150">Company Name</th>
                        <th width="150">Buyer Name</th>
                        <th width="100">Search By</th>
                        <th width="80">Please Enter</th>
                        <th width="130" colspan="2">Issue Date Range</th>
                        <th><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">WO Without Item</th>
                    </tr>
                </thead>
                <tr class="general">
                    <td>
                        <input type="hidden" id="selected_booking">
                        <input type="hidden" id="selected_job">
                        <input type="hidden" id="within_group">
                        <? echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", '', "load_drop_down( 'fabric_issue_for_aop_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'fabric_issue_for_aop_entry_controller', this.value, 'load_drop_down_supplier', 'supplier_td' );",0); ?>
                    </td>
                    <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select Buyer --" ); ?></td>
                    <td>
                    <?
                        $search_by_arr=array(1=>"Issue ID",2=>"Batch no",3=>"FSO No",4=>"Fabric booking no",5=>"Style reference no");
                        echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'',0 );
                    ?>
                    </td>
                    <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:67px" placeholder="Enter Full Prefix"></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px"></td>
                    <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"></td>
                    <td><input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('chk_job_wo_po').value, 'create_issue_search_list_view', 'search_div', 'fabric_issue_for_aop_entry_controller','setFilterGrid(\'tbl_po_list\',-1)')" style="width:80px;" /></td>
                </tr>
                <tr>
                    <td align="center" valign="middle" colspan="11"><? echo load_month_buttons(1); ?></td>
                </tr>
            </table>
            <div id="search_div"></div>
        </form>
        </div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	<script type="text/javascript">
		$("#cbo_company_mst").val(<? echo $cbo_company_id?>);
		//load_drop_down( 'fabric_issue_for_aop_entry_controller', $("#cbo_company_mst").val(), 'load_drop_down_buyer', 'buyer_td' );
	</script>
	</html>
	<?
	exit();
}

if ($action=="create_issue_search_list_view")
{
	//echo load_html_head_contents("Booking PopUp","../../", 1, 1, $unicode,'','');
	$data=explode('_',$data);

	if ($data[0]!=0) $company=" and h.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and c.buyer_id='$data[1]'"; else $buyer="";
	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $sys_date  = "and h.sys_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $sys_date ="";
		//$year_cond=" and SUBSTRING_INDEX(b.insert_date, '-', 1)=$data[7]";
	}
	else if($db_type==2)
	{
		if ($data[2]!="" &&  $data[3]!="") $sys_date  = "and h.sys_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $sys_date ="";
		//$year_cond=" and to_char(b.insert_date,'YYYY')=$data[7]";
	}
	//$search_by_arr=array(1=>"Receive ID",2=>"Batch no",3=>"FSO No",4=>"Fabric booking no",5=>"Style reference no");
	//if ($data[4]!=0) $supplier=" and h.supplier_id='$data[4]'"; else $supplier="";
	//echo $data[4];
	if ($data[4]!=''){
		if($data[6]==1) $search_cond=" and h.sys_no like '%$data[4]%'";
		if($data[6]==2) $search_cond=" and e.batch_no like '%$data[4]%'";
		if($data[6]==3) $search_cond=" and c.job_no like '%$data[4]%'";
		if($data[6]==4) $search_cond=" and c.sales_booking_no like '%$data[4]%'";
		if($data[6]==5) $search_cond=" and c.style_ref_no like '%$data[4]%'";
	}

	//if ($data[8]!=0) $within_group_cond=" and c.within_group='$data[8]'"; else $within_group_cond="";

	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	//$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	//$booking_cond $style_cond $buyer $supplier $within_group_cond
	
	if($data[7]==1)
	{
		$sql= "select c.company_id, h.sys_no,h.order_no,h.id as issue_id,h.supplier_id,h.within_group from 
		fabric_sales_order_dtls b, fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e, wo_fabric_aop_mst h where  h.entry_form=462 and h.fab_booking_no= c.sales_booking_no and b.mst_id=c.id and c.id=d.po_id and d.mst_id=e.id and e.color_id=b.color_id and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 $company $buyer $sys_date $search_cond group by  c.company_id , h.sys_no,h.order_no,h.id ,h.supplier_id,h.within_group order by h.id desc";

	}else{
		$sql= "select c.company_id, g.quantity , h.sys_no,h.order_no,h.id as issue_id,h.supplier_id,h.within_group from 
		 fabric_sales_order_dtls b, fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e, wo_fabric_aop_dtls g, wo_fabric_aop_mst h where h.id=g.mst_id and b.mst_id=c.id and c.id=d.po_id and d.mst_id=e.id and g.sales_order_dtls_id=b.id and g.batch_id=e.id and e.color_id=b.color_id and h.entry_form=462 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and g.status_active=1 and g.is_deleted=0 $company $buyer $sys_date $search_cond group by  c.company_id, h.sys_no,h.order_no,h.id,h.supplier_id,h.within_group, g.quantity   order by h.id desc";
	}
	//echo $sql;
	$result=sql_select($sql);
	$issue_arr=array();
	foreach( $result as $row){
		$issue_arr[$row[csf("company_id")]][$row[csf("issue_id")]][$row[csf("sys_no")]][$row[csf("supplier_id")]]['quantity']+=$row[csf("quantity")];
		$issue_arr[$row[csf("company_id")]][$row[csf("issue_id")]][$row[csf("sys_no")]][$row[csf("supplier_id")]]['order_no']=$row[csf("order_no")];
		$issue_arr[$row[csf("company_id")]][$row[csf("issue_id")]][$row[csf("sys_no")]][$row[csf("supplier_id")]]['within_group']=$row[csf("within_group")];
	}
	?>
	<div style="width:700px;">
     	<table cellspacing="0" cellpadding="0" align="left" rules="all" width="680" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="70">Within Group</th>
                <th width="160">PO Company Name</th>
                <th width="120">Issue No</th>
                <th width="160">Supplier</th>
                <th>Issue qty.</th>
            </thead>
     	</table>
    </div>
    <div style="width:700px; max-height:240px;overflow-y:scroll;">
        <table cellspacing="0" cellpadding="0" rules="all" width="680" class="rpt_table" id="tbl_po_list">
	    <?
	    $i=1;
	    foreach($issue_arr as $company_id=>$company_id_data)
	    {
	    	foreach($company_id_data as $issue_id=>$issue_id_data)
		    {
		    	foreach($issue_id_data as $sys_no=>$sys_no_data)
			    {
				    foreach($sys_no_data as $supplier_id=>$row)
				    {
						if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				     	//$booking_no="'".$row[csf("booking_no")]."'";
				     	?>
				     	<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row['order_no'];?>' , '<? echo $issue_id;?>')" >
							<td width="30" align="center"><?php echo $i; ?></td>
							<td width="70" style="word-break:break-all"><?php echo $yes_no[$row['within_group']]; ?></td>
							<td width="160" style="word-break:break-all"><?php echo $comp[$company_id]; ?></td>
							<td width="120" style="word-break:break-all"><?php echo $sys_no; ?></td>
							<td width="160" style="word-break:break-all"><?php echo $supplier_name_arr[$supplier_id]; ?></td>
							<td style="word-break:break-all" align="right"><?php echo number_format($row['quantity'],4); ?></td>
						</tr>
			     	<?
			     	$i++;
			     	}
			    }
			}
		}
        ?>
        </table>
    </div>
   <?
	exit();
}


if ($action=="service_booking_popup")
{
	echo load_html_head_contents("Booking Search","../../", 1, 1, $unicode);
	extract($_REQUEST);
 	?>
	<script>
	function set_checkvalue(){
		if(document.getElementById('chk_sample').value==0) document.getElementById('chk_sample').value=1;
		else document.getElementById('chk_sample').value=0;
	}
	function js_set_value(booking_no,job_no,within_group,sales_order_id,sales_order_type)
	{
		//alert(within_group);
		document.getElementById('selected_booking').value=booking_no;
		document.getElementById('selected_job').value=job_no;
		document.getElementById('within_group').value=within_group;
		document.getElementById('sales_order_id').value=sales_order_id;
		document.getElementById('sales_order_type').value=sales_order_type;
		parent.emailwindow.hide();
	}
	
	/*function fnc_supplier_disabled(type,val)
	{
		if(val==2){
			document.getElementById('cbo_supplier_name').value=0;
			$("#cbo_supplier_name").attr("disabled",true);
		}else{
			document.getElementById('cbo_supplier_name').value=0;
			$("#cbo_supplier_name").attr("disabled",false);
		}
		
	}*/
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
                        <th width="150">PO Company Name</th>
                        <th width="60">Within Group</th>
                        <th width="150">Buyer Name</th>
                        <th width="150">Sub Con Supplier</th>
                        <th width="120">Sales Order No</th>
                        <th width="120">Fabric Booking No</th>
                        <th width="100">Style reference no</th>
                        <th width="130" colspan="2">Date Range</th>
                        <th><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_sample">Sample Without Order</th>
                    </tr>
                </thead>
                <tr class="general">
                    <td>
                        <input type="hidden" id="selected_booking">
                        <input type="hidden" id="selected_job">
                        <input type="hidden" id="within_group">
                        <input type="hidden" id="sales_order_id">
                        <input type="hidden" id="sales_order_type">
                        <? echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", '', "load_drop_down( 'fabric_issue_for_aop_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'fabric_issue_for_aop_entry_controller', this.value, 'load_drop_down_supplier', 'supplier_td' );",0); ?>
                    </td>
                    <td><?php echo create_drop_down( "cbo_within_group", 60, $yes_no,"", 0, "--  --", 0, "fnc_load_party(1,this.value);" ); ?></td>
                    <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 172, $blank_array,"", 1, "-- Select Buyer --" ); ?></td>
                    <td id="supplier_td"><? echo create_drop_down( "cbo_supplier_name", 172, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=25 and   a.status_active =1 and a.is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "",0 ); ?> </td>
                    <td><input name="txt_sales_order" id="txt_sales_order" class="text_boxes" style="width:107px" placeholder="Enter Full Prefix"></td>
                    <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:107px" placeholder="Enter Full Prefix"></td>
                    <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:87px"></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px"></td>
                    <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"></td>
                    <td><input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('txt_sales_order').value+'_'+document.getElementById('chk_sample').value, 'create_booking_search_list_view', 'search_div', 'fabric_issue_for_aop_entry_controller','setFilterGrid(\'tbl_po_list\',-1)')" style="width:80px;" /></td>
                </tr>
                <tr>
                    <td align="center" valign="middle" colspan="11"><? echo load_month_buttons(1); ?></td>
                </tr>
            </table>
            <div id="search_div"></div>
        </form>
        </div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	<script type="text/javascript">
		/*$("#cbo_company_mst").val(<? //echo $cbo_company_id?>);
		load_drop_down( 'fabric_issue_for_aop_entry_controller', $("#cbo_company_mst").val(), 'load_drop_down_buyer', 'buyer_td' );
		load_drop_down( 'fabric_issue_for_aop_entry_controller', $("#cbo_company_mst").val(), 'load_drop_down_supplier', 'supplier_td' );
		$("#cbo_supplier_name").val(<? //echo $cbo_supplier_name?>);*/
	</script>
	</html>
	<?
	exit();
}



if ($action=="create_booking_search_list_view")
{
	//echo load_html_head_contents("Booking PopUp","../../", 1, 1, $unicode,'','');
	$data=explode('_',$data);
	$within_group=$data[8];
	$without_order=$data[10];
	if ($data[0]!=0)
	{
		if($within_group==1)
		{
			$company="  f.company_id='$data[0]'";
			/*if ($without_order !=1){
				$company="  f.company_id='$data[0]'";
			}
			else{
				$company="  f.company_id='$data[0]'";
			}*/
		}
		else{
			$company="  c.company_id='$data[0]'";
		}
	}
	else 
	{ 
		echo "Please Select Company First."; die;
	}
	 
	if ($data[1]!=0) $buyer=" and f.buyer_id='$data[1]'"; else $buyer="";
	if ($data[4]!=0) $supplier=" and f.supplier_id='$data[4]'"; else $supplier="";

	if ($data[8]!=0) $within_group_cond=" and c.within_group='$data[8]'"; else $within_group_cond="";

	if($within_group==1)
	{
		if($db_type==0)
		{
			if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and f.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
			$year_cond=" and SUBSTRING_INDEX(b.insert_date, '-', 1)=$data[7]";
		}
		else if($db_type==2)
		{
			if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and f.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
			$year_cond=" and to_char(b.insert_date,'YYYY')=$data[7]";
		}
	}else{
		if($db_type==0)
		{
			if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and c.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
			$year_cond=" and SUBSTRING_INDEX(b.insert_date, '-', 1)=$data[7]";
		}
		else if($db_type==2)
		{
			if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and c.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
			$year_cond=" and to_char(b.insert_date,'YYYY')=$data[7]";
		}
	}

	if($data[6]==1){
		if (str_replace("'","",$data[5])!="") $booking_cond=" and c.sales_booking_no='$data[5]'"; else  $booking_cond="";

		if (trim($data[7])!="") $style_cond=" and c.style_ref_no ='$data[7]'"; else $style_cond="";
		if (trim($data[9])!="") $so_cond=" and c.job_no ='$data[9]'"; else $so_cond="";
	}
	else if($data[6]==4 || $data[6]==0){
		if (str_replace("'","",$data[5])!="") $booking_cond=" and c.sales_booking_no like '%$data[5]%'  $booking_year_cond "; else  $booking_cond="";

		if (trim($data[7])!="") $style_cond=" and c.style_ref_no like '%$data[7]%'"; else $style_cond="";
		if (trim($data[9])!="") $so_cond=" and c.job_no like '%$data[9]%'"; else $so_cond="";
	}
	else if($data[6]==2)
	{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and c.sales_booking_no like '$data[5]%' $booking_year_cond "; else  $booking_cond="";
		if (trim($data[7])!="") $style_cond=" and c.style_ref_no like'$data[7]%'"; else $style_cond="";
		if (trim($data[9])!="") $so_cond=" and c.job_no like'$data[9]%'"; else $so_cond="";
	}
	else if($data[6]==3)
	{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and c.sales_booking_no like '%$data[5]' $booking_year_cond "; else  $booking_cond="";
		if (trim($data[7])!="") $style_cond=" and c.style_ref_no like '%$data[7]'"; else $style_cond="";
		if (trim($data[9])!="") $so_cond=" and c.job_no like '%$data[9]'";  else $so_cond="";
	}

	/*$file_no = str_replace("'","",$data[8]);
	$internal_ref = str_replace("'","",$data[9]);
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and d.file_no='".trim($file_no)."' ";
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and d.grouping='".trim($internal_ref)."' ";*/

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');

	if($within_group==1)
	{
		if($without_order !=1){
			$sql= "select f.id,f.booking_no,f.company_id, f.buyer_id,a.job_no, c.id as sales_order_id,c.style_ref_no,c.job_no as fso_number, c.within_group, c.sales_booking_no from 
			wo_pre_cost_fabric_cost_dtls a, fabric_sales_order_dtls b, fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e, wo_booking_mst f  where $company and a.id=b.pre_cost_fabric_cost_dtls_id and b.mst_id=c.id and c.id=d.po_id and d.mst_id=e.id and f.tagged_booking_no = c.sales_booking_no and e.color_id=b.color_id and f.booking_type=3 and f.process=35 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 $booking_cond $style_cond $buyer $supplier $within_group_cond $booking_date $so_cond group by  f.id,f.booking_no,f.company_id, f.buyer_id,a.job_no,c.style_ref_no,c.job_no, c.id, c.within_group, c.sales_booking_no order by f.id desc";
		}else{
			$sql= "select f.id,f.booking_no,f.company_id, f.buyer_id, c.id as sales_order_id,c.style_ref_no,c.job_no as fso_number, c.within_group, c.sales_booking_no from 
			 fabric_sales_order_dtls b, fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e, wo_non_ord_samp_booking_mst f  where $company and  b.mst_id=c.id and c.id=d.po_id and d.mst_id=e.id and f.booking_no = c.sales_booking_no and e.color_id=b.color_id and f.booking_type=4 and f.pay_mode=5 and f.supplier_id=c.company_id  and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 $booking_cond $style_cond $buyer $supplier $within_group_cond $booking_date $so_cond group by  f.id,f.booking_no,f.company_id, f.buyer_id,c.style_ref_no,c.job_no, c.id, c.within_group, c.sales_booking_no order by f.id desc";
		}
	}else{
		$sql= "select c.id as sales_order_id, c.job_no as booking_no,c.company_id, c.buyer_id,c.style_ref_no,c.job_no as fso_number, c.within_group, c.sales_booking_no from 
		 fabric_sales_order_dtls b, fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e where $company and  b.mst_id=c.id and c.id=d.po_id and d.mst_id=e.id and e.color_id=b.color_id  and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 $booking_cond $style_cond $buyer $supplier $within_group_cond $booking_date $so_cond group by  c.id,c.job_no ,c.company_id, c.buyer_id,c.style_ref_no,c.job_no, c.within_group, c.sales_booking_no order by c.id desc";
	}
	
	//echo $sql;
	$result=sql_select($sql);
	?>
	<div style="width:1000px;">
     	<table cellspacing="0" cellpadding="0" border="1" align="left" rules="all" width="980" class="rpt_table">
            <thead> 
                <th width="30">SL</th>
                <th width="170">PO Company Name</th>
                <?
                if($within_group==1)
				{
                ?>
                	<th width="170">PO Buyer Name</th>
                <?
            	}else{
            	?>
                	<th width="170">Buyer Name</th>
                <?
            	}?>
                
                <th width="170">Style Ref. No</th>
                <th width="140">FSO No.</th>
                <th width="140">Fabric Booking No</th>
                <?
                if($within_group==1 && $without_order==0)
				{
                ?>
                	<th>WO No</th>
                <?
            	}?>
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
	     	//$booking_no="'".$row[csf("booking_no")]."'";
	     	?>
	     	<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row[csf("booking_no")];?>' , '<? echo $row[csf("job_no")];?>', '<? echo $row[csf("within_group")];?>', '<? echo $row[csf("sales_order_id")];?>', '<? echo $without_order;?>')" >
				<td width="30" align="center"><?php echo $i; ?></td>
				<td width="170" style="word-break:break-all"><?php echo $comp[$row[csf("company_id")]]; ?></td>
				<td width="170" style="word-break:break-all"><?php echo $buyer_arr[$row[csf("buyer_id")]]; ?></td>
				<td width="170" style="word-break:break-all"><?php echo $row[csf("style_ref_no")]; ?></td>
				<td width="140" style="word-break:break-all"><?php echo $row[csf("fso_number")]; ?></td>
				<td width="140" style="word-break:break-all"><?php echo $row[csf("sales_booking_no")]; ?></td>
				<?
				if($within_group==1 && $without_order==0)
				{
                ?>
                	<td style="word-break:break-all"><?php echo $row[csf("booking_no")]; ?></td>
                <?
            	}?>
				
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
	echo load_html_head_contents("Order Search","../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	</head>
	<body>
        <div align="center" style="width:100%;">
			<? echo load_freeze_divs ("../../",$permission);  ?>
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
			data_all=data_all+get_submitted_data_string('txt_booking_no*termscondition_'+i,"../../",i);
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
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if ($action=="populate_data_from_search_popup")
{
	$data=explode('_',$data);
	$sql= "select id,booking_no,booking_date,company_id,buyer_id,job_no,po_break_down_id,item_category,fabric_source,currency_id,exchange_rate,pay_mode,booking_month,ready_to_approved,supplier_id,attention,delivery_date,source,booking_year,cbo_level,is_short,is_approved,tagged_booking_no from wo_booking_mst  where booking_no='$data[0]' and status_active=1 and is_deleted=0";
	
	
	$data_array=sql_select($sql);
	//$supplier_id=$data_array[0]['supplier_id'];
	//$supplier_address = return_field_value("address_1", "lib_supplier", "id=$supplier_id", "address_1");
	
	foreach ($data_array as $row){
		$supplier_address = return_field_value("address_1", "lib_supplier", "id='".$row[csf("supplier_id")]."'", "address_1");
		echo "document.getElementById('cbo_lc_company_name').value 	 = '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('buyer_id').value 	 		 = '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('cbo_supplier_name').value 	 = '".$row[csf("supplier_id")]."';\n";
		echo "document.getElementById('txt_party_location').value 	 = '".$supplier_address."';\n";
		echo "document.getElementById('txt_order_no').value 		 = '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('hid_order_id').value 		 = '".$row[csf("id")]."';\n";
		echo "document.getElementById('hid_fab_booking').value 		 = '".$row[csf("tagged_booking_no")]."';\n";
		//echo "document.getElementById('cbo_is_short').value 		 = ".$row[csf("is_short")].";\n";
		echo " $('#cbo_company_name').attr('disabled',true);\n";
		//echo " $('#cbo_supplier_name').attr('disabled',true);\n";
		//echo " $('#cbo_level').attr('disabled',true);\n";
		echo " $('#cbo_supplier_name').attr('disabled',true);\n";
		//echo " $('#cbo_is_short').attr('disabled',true);\n";
	}
}

if ($action=="populate_sales_data_from_search_popup")
{
	$data=explode('_',$data);
	$sql= "select c.id, c.job_no as booking_no,c.company_id, c.buyer_id,c.style_ref_no,c.job_no as fso_number, c.within_group, c.sales_booking_no,po_company_id,po_buyer from fabric_sales_order_mst c where id=$data[3]"; 
	//echo $data[3].'==';
	$data_array=sql_select($sql);
	
	foreach ($data_array as $row){
		if($data[4]==1){
			$lc_company=$row[csf("po_company_id")];
			$po_buyer=$row[csf("po_buyer")];
		} else{
			$lc_company=$row[csf("company_id")];
			$po_buyer=$row[csf("buyer_id")];
		}

		echo "document.getElementById('cbo_lc_company_name').value 	 = '".$lc_company."';\n";
		echo "document.getElementById('buyer_id').value 	 		 = '".$po_buyer."';\n";
		echo "document.getElementById('txt_order_no').value 		 = '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('hid_order_id').value 		 = '".$row[csf("id")]."';\n";
		echo "document.getElementById('hid_fab_booking').value 		= '".$row[csf("sales_booking_no")]."';\n";
		//echo "document.getElementById('cbo_is_short').value 		 = ".$row[csf("is_short")].";\n";
		echo " $('#cbo_company_name').attr('disabled',true);\n";
		//echo " $('#cbo_supplier_name').attr('disabled',true);\n";
		//echo " $('#cbo_level').attr('disabled',true);\n";
		echo " $('#cbo_supplier_name').attr('disabled',false);\n";
		//echo " $('#cbo_is_short').attr('disabled',true);\n";
	}
}

if ($action=="populate_issue_data_from_search_popup")
{
	//$data=explode('_',$data);
	
	$sql= "select a.id, a.entry_form, a.sys_no_prefix, a.sys_no_prefix_num, a.sys_no, a.sys_date, a.delivery_date, a.company_id, a.buyer_id, a.issue_purpose, a.source, a.order_id, a.order_no, a.attention, a.vehical_no, a.driver_name, a.dl_no, a.transport, a.mobile_no, a.gate_pass_no, a.is_short, a.remark,a.supplier_id,a.supplier_address,within_group,lc_company_name,fab_booking_no,order_type from wo_fabric_aop_mst a where a.id=$data and a.is_deleted=0 ";

	 $data_array=sql_select($sql);
	 foreach ($data_array as $row){

		echo "document.getElementById('txt_system_id').value 	 	= '".$row[csf("sys_no")]."';\n";
		echo "document.getElementById('cbo_company_name').value 	= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_lc_company_name').value 	= '".$row[csf("lc_company_name")]."';\n";
		echo "document.getElementById('hid_fab_booking').value 		= '".$row[csf("fab_booking_no")]."';\n";
		echo "document.getElementById('buyer_id').value 		 	= '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('cbo_supplier_name').value 	= '".$row[csf("supplier_id")]."';\n";
		echo "document.getElementById('txt_party_location').value 	= '".$row[csf("supplier_address")]."';\n";
		echo "document.getElementById('txt_issue_date').value 		= '".change_date_format($row[csf("sys_date")])."';\n";
		echo "document.getElementById('txt_delivery_date').value 	= '".change_date_format($row[csf("delivery_date")])."';\n";
		echo "document.getElementById('cbo_issue_purpose').value 	= '".$row[csf("issue_purpose")]."';\n";
		echo "document.getElementById('cbo_source').value 		 	= '".$row[csf("source")]."';\n";
		echo "document.getElementById('hid_order_id').value 		= '".$row[csf("order_id")]."';\n";
		echo "document.getElementById('txt_order_no').value 		= '".$row[csf("order_no")]."';\n";
		echo "document.getElementById('txt_attention').value 		= '".$row[csf("attention")]."';\n";
		echo "document.getElementById('txt_vehical_no').value 		= '".$row[csf("vehical_no")]."';\n";
		echo "document.getElementById('txt_driver_name').value 		= '".$row[csf("driver_name")]."';\n";
		echo "document.getElementById('txt_dl_no').value 		 	= '".$row[csf("dl_no")]."';\n";
		echo "document.getElementById('txt_transport').value 		= '".$row[csf("transport")]."';\n";
		echo "document.getElementById('txt_cell_no').value 		 	= '".$row[csf("mobile_no")]."';\n";
		echo "document.getElementById('txt_gate_pass_no').value 	= '".$row[csf("gate_pass_no")]."';\n";
		echo "document.getElementById('cbo_is_short').value 		= '".$row[csf("is_short")]."';\n";
		echo "document.getElementById('txt_remarks').value 		 	= '".$row[csf("remark")]."';\n";
		echo "document.getElementById('update_id').value 		 	= '".$row[csf("id")]."';\n";
		echo "document.getElementById('within_group').value 		= '".$row[csf("within_group")]."';\n";
		echo "document.getElementById('sales_order_type').value 	= '".$row[csf("order_type")]."';\n";
	   	
		echo "document.getElementById('cbo_is_short').value 		= ".$row[csf("is_short")].";\n";

		echo " $('#cbo_company_name').attr('disabled',true);\n";
		//echo " $('#cbo_supplier_name').attr('disabled',true);\n";
		//echo " $('#cbo_level').attr('disabled',true);\n";
		echo " $('#cbo_buyer_name').attr('disabled',true);\n";
		echo " $('#cbo_is_short').attr('disabled',true);\n";
		echo " $('#txt_order_no').attr('disabled',true);\n";
		echo " $('#cbo_supplier_name').attr('disabled',true);\n";
	 }
}
if ($action=="fabric_booking_popup")
{
	//echo load_html_head_contents("Booking Search","../../", 1, 1, $unicode);
	//extract($_REQUEST);
	echo load_html_head_contents("Booking Search","../../", 1, 1, $unicode);
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
						echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", '', "load_drop_down( 'fabric_booking_urmi_controller', this.value, 'load_drop_down_buyer_popup', 'buyer_td' );",1); ?>
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
                    <td align="center">
                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_order_search').value, 'create_booking_search_list_view2', 'search_div', 'fabric_issue_for_aop_entry_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
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
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	<script type="text/javascript">
		$("#cbo_company_mst").val(<? echo $company?>);
		load_drop_down( 'fabric_issue_for_aop_entry_controller', $("#cbo_company_mst").val(), 'load_drop_down_buyer_popup', 'buyer_td' );
	</script>
	</html>
	<?
	exit();
}

if ($action=="load_drop_down_buyer_popup"){
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "","0","" );
	exit();
}

if($action=="multi_issue_number_popup")
{
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>

		function toggle( x, origColor ) 
		{
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function check_all_data()
		{
			
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			//tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var attrData=$('#tr_' +i).attr('onclick');
				var splitArr = attrData.split('"');
				js_set_value( splitArr[1] );
			}
		}

		var selected_id=Array();
		var selected_name=Array();
		var selected_ord=Array();

		function js_set_value(mrr)
		{
			//alert(mrr);
			var splitArr = mrr.split("_");
			$("#hidden_issue_number").val(splitArr[1]); // mrr number
			$("#hidden_issue_id").val(splitArr[2]); // id
			$("#hidden_within_group_number").val(splitArr[3]); // order no

			toggle( document.getElementById( 'tr_' + splitArr[0] ), '#FFFFCC' );

	 		if( jQuery.inArray(splitArr[2], selected_id ) == -1 ) {			
	 			selected_name.push(splitArr[1]);
	 			selected_id.push( splitArr[2]);
	 			selected_ord.push( splitArr[3]);

	 		}
	 		else 
	 		{
	 			for( var i = 0; i < selected_id.length; i++ ) {
	 				if( selected_id[i] == splitArr[2]) break;
	 			} 			
	 			selected_name.splice( i, 1 );
	 			selected_id.splice( i, 1 );
	 			selected_ord.splice( i, 1 );
	 		}

	 		var id = ''; var name = ''; var ord = '';
	 		for( var i = 0; i < selected_id.length; i++ ) {
	 			id += selected_id[i] + ',';
	 			name += selected_name[i] + ',';
	 			ord += selected_ord[i] + ',';
	 		}

	 		id = id.substr( 0, id.length - 1 );
	 		name = name.substr( 0, name.length - 1 );
	 		ord = ord.substr( 0, ord.length - 1 );

	 		$('#hidden_issue_id').val(id);
	 		$('#hidden_issue_number').val(name);
	 		$('#hidden_within_group_number').val(ord);
	 	}

	 	function fnc_close ()
	 	{
	 		parent.emailwindow.hide();
	 	}
 	</script>

	</head>

	<body>
		<div align="center" style="width:100%;" >
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table width="850" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
					<thead>
						<tr>
							<th width="120" class="must_entry_caption">Service Company</th>
							<th width="180">Search By</th>
							<th width="250" align="center" id="search_by_td_up">Enter Issue Number</th>
							<th width="220">Date Range</th>
							<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td align="center">
								<?
								if($supplier_name!=0) $disabled=1; else $disabled=0;
								echo create_drop_down( "cbo_return_to", 120, "select id,supplier_name from lib_supplier order by supplier_name","id,supplier_name", 1, "-- Select --", $supplier_name, "",$disabled );
								?>
							</td>
							<td align="center">
								<?
								$search_by = array(1=>'Issue Number');
								//$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
								echo create_drop_down( "cbo_search_by", 140, $search_by,"",0, "--Select--", "",1,0 );
								?>
							</td>
							<td width="" align="center" id="search_by_td">
								<input type="text" style="width:230px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" />&nbsp;&nbsp;&nbsp;
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" />
							</td>
							<td align="center">
								<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('cbo_return_to').value, 'create_multi_return_search_list_view', 'search_div', 'fabric_issue_for_aop_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td align="center" height="40" valign="middle" colspan="5">
								<? echo load_month_buttons(1);  ?>
								<!-- Hidden field here-->
								<!--END -->
							</td>
						</tr>
					</tbody>
				</tr>
			</table>
			<div align="center" style="margin-top:10px" valign="top" id="search_div"> </div>
			<table width="700" cellspacing="0" cellpadding="0" style="border:none" align="center">
				<tr>
					<td align="center" height="30" valign="bottom">
						<div style="width:100%;">
							<div style="width:50%; float:left" align="left" id="button_div">
								<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
							</div>
							<div style="width:50%; float:left" align="left">
								<input type="button" name="close" onClick="fnc_close();" class="formbutton" value="Close" style="width:100px" />
							</div>
						</div>
					</td>
				</tr>
			</table> 

		</form>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_multi_return_search_list_view")
{
	echo '<input type="hidden" id="hidden_issue_number" value="" /><input type="hidden" id="hidden_issue_id" value="" /><input type="hidden" id="hidden_within_group_number" value="" />';
	$ex_data = explode("_",$data);
	$search_by = $ex_data[0];
	$search_common = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];
	$return_to = $ex_data[5];

	$sql_cond="";
	if($search_by==1)
	{
		if($search_common!="") $sql_cond .= " and h.sys_no like '%$search_common'";
	}

	if( $txt_date_from!="" && $txt_date_to!="" )
	{
		if($db_type==0)
		{
			$sql_cond .= " and h.issue_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
		}
		else
		{
			$sql_cond .= " and h.issue_date between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
		}
	}

	if(trim($company)!="") $sql_cond .= " and h.company_id='$company'";

	if($db_type==0) $year_field=", YEAR(h.insert_date) as year";
	else if($db_type==2) $year_field=", to_char(h.insert_date,'YYYY') as year";
	else $year_field="";//defined Later

	if(str_replace("'","",$return_to==0)){echo "<p style='font-size:25px; color:#F00'>Please Select Service Company.</p>";die;}
	else{$supplier_con=" and h.supplier_id=$return_to";}

	$sql= "select c.company_id, g.quantity , h.sys_no,h.order_no,h.id as issue_id,h.supplier_id,h.within_group,h.sys_date $year_field from 
	fabric_sales_order_dtls b, fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e, wo_fabric_aop_dtls g, wo_fabric_aop_mst h where h.id=g.mst_id and b.mst_id=c.id and c.id=d.po_id and d.mst_id=e.id and g.sales_order_dtls_id=b.id and g.batch_id=e.id and e.color_id=b.color_id and h.entry_form=462 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and g.status_active=1 and g.is_deleted=0 $sql_cond $supplier_con group by  c.company_id, h.sys_no,h.order_no,h.id,h.supplier_id,h.within_group, g.quantity,h.sys_date, h.insert_date   order by h.id desc";

	$result=sql_select($sql);
	$issue_arr=array();
	foreach( $result as $row){
		$issue_arr[$row[csf("company_id")]][$row[csf("issue_id")]][$row[csf("sys_no")]][$row[csf("supplier_id")]]['quantity']+=$row[csf("quantity")];
		$issue_arr[$row[csf("company_id")]][$row[csf("issue_id")]][$row[csf("sys_no")]][$row[csf("supplier_id")]]['order_no']=$row[csf("order_no")];
		$issue_arr[$row[csf("company_id")]][$row[csf("issue_id")]][$row[csf("sys_no")]][$row[csf("supplier_id")]]['within_group']=$row[csf("within_group")];
		$issue_arr[$row[csf("company_id")]][$row[csf("issue_id")]][$row[csf("sys_no")]][$row[csf("supplier_id")]]['year']=$row[csf("year")];
		$issue_arr[$row[csf("company_id")]][$row[csf("issue_id")]][$row[csf("sys_no")]][$row[csf("supplier_id")]]['sys_date']=change_date_format($row[csf("sys_date")]);
	}

	?>
	<div style="width:700px;">
     	<table cellspacing="0" cellpadding="0" align="left" rules="all" width="680" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="50">Within Group</th>
                <th width="110">Isuue No</th>
                <th width="50">Year</th>
                <th width="150">PO Company Name</th>
                <th width="120">Service Company</th>
                <th width="60">Issue Date</th>
                <th>Issue Qty.</th>
            </thead>
     	</table>
    </div>
	<div style="width:700px; max-height:240px;overflow-y:scroll;">
        <table cellspacing="0" cellpadding="0" rules="all" width="680" class="rpt_table" id="tbl_po_list">
	    <?
	    $i=1;
	    $comp = return_library_array("select id,company_name from lib_company","id","company_name");
		$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	    foreach($issue_arr as $company_id=>$company_id_data)
	    {
	    	foreach($company_id_data as $issue_id=>$issue_id_data)
		    {
		    	foreach($issue_id_data as $sys_no=>$sys_no_data)
			    {
				    foreach($sys_no_data as $supplier_id=>$row)
				    {
						if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				     	//$booking_no="'".$row[csf("booking_no")]."'";
				     	?>
				     	<tr id="tr_<? echo $i; ?>" bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $i.'_'. $sys_no.'_'.$issue_id.'_'.$row['within_group']; ?>")' style="cursor:pointer" >
							<td width="30" align="center"><?php echo $i; ?></td>
							<td width="50" style="word-break:break-all"><?php echo $yes_no[$row['within_group']]; ?></td>
							<td width="110" style="word-break:break-all"><?php echo $sys_no; ?></td>
							<td width="50" style="word-break:break-all"><?php echo $row['year']; ?></td>
							<td width="150" style="word-break:break-all"><?php echo $comp[$company_id]; ?></td>
							<td width="120" style="word-break:break-all"><?php echo $supplier_arr[$supplier_id]; ?></td>
							<td width="60" style="word-break:break-all"><?php echo $row['sys_date']; ?></td>
							<td style="word-break:break-all" align="right"><?php echo number_format($row['quantity'],4); ?></td>
						</tr>
			     	<?
			     	$i++;
			     	}
			    }
			}
		}
        ?>
        </table>
    </div>
	<?
	exit();
}

if($action=="issue_print")
{
	//select id, item_name from lib_item_group where item_category=4 and status_active=1
	extract($_REQUEST);
	$data=explode('*',$data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	//$store_name_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$source_for_order = array(1 => 'In-House', 2 => 'Sub-Contract');
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	
	$sql= "select c.id,c.company_id, c.po_buyer as buyer_id,c.style_ref_no,c.job_no as fso_number, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, b.color_id, b.body_part_id , b.fabric_desc, b.gsm_weight, b.dia ,b.id as so_dtls_id,e.id as batch_id,g.id as dtls_id, g.process_type_id, g.quantity, g.rate, g.amount, g.number_of_roll, d.batch_qnty as batch_weight, g.remark  from 
		 fabric_sales_order_dtls b, fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e, wo_fabric_aop_dtls g ,product_details_master  h where b.mst_id=c.id and c.id=d.po_id and d.mst_id=e.id and g.sales_order_dtls_id=b.id and g.batch_id=e.id and e.color_id=b.color_id and d.prod_id=h.id and g.mst_id in ($data[1]) and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and g.status_active=1 and g.is_deleted=0 group by  c.id,c.company_id, c.po_buyer,c.style_ref_no,c.job_no, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, b.color_id, b.body_part_id , b.fabric_desc, b.gsm_weight, b.dia ,b.id,e.id,g.id, g.process_type_id, g.quantity, g.rate, g.amount, g.number_of_roll, d.batch_qnty, g.remark order by b.id desc";
		 //and h.item_description=b.fabric_desc

	$qry_result=sql_select($sql);
	$sql_mst="select id, entry_form, sys_no_prefix, sys_no_prefix_num, sys_no, sys_date, delivery_date, company_id, buyer_id,supplier_id,supplier_address, issue_purpose, source, order_id, order_no, attention, vehical_no, driver_name, dl_no, transport, mobile_no, gate_pass_no, is_short, remark from wo_fabric_aop_mst where id in ($data[1]) and entry_form=462 and status_active=1";

	$dataArray=sql_select($sql_mst);
	$com_dtls = fnc_company_location_address($data[0], 0, 2);
	//echo "<pre>";
	//print_r($wo_arr);
	//die;

	?>
	<style type="text/css">
		td.make_bold {
	  		font-weight: 900;
		}
	</style>
	<div style="width:1400px;">
		<table width="1400" cellspacing="0" align="center" border="0">
			<tr>
            	<td  align="left"><img src="../../<? echo $com_dtls[2]; ?>" height="70" width="200"></td>
            	<td colspan="7" align="center"  style="font-size:xx-large; text-align:left;"><strong ><? echo $com_dtls[0]; ?></strong>
        	</tr>
	        <tr class="form_caption">
	            <td colspan="8" align="center"><? echo $com_dtls[1]; ?> </td>
	        </tr>
	        <tr>
	            <td colspan="8" style="font-size:large; text-align:center;" align="center"><strong ><? echo "Fabric Delivery Challan For AOP"; ?></strong> </td>
	        </tr>
		</table>
		<br>
		<table width="1400" cellspacing="0" align="center" border="0">
			<tr>			
				<td width="100">Company Name </td> <td width="195">: <? echo $company_library[$dataArray[0][csf('company_id')]]; ?></td>
				<td width="100">Challan No </td> <td width="195">: <? echo $dataArray[0][csf('sys_no')]; ?></td>
				<td width="100">Issue Date </td> <td width="195">: <? echo change_date_format($dataArray[0][csf('sys_date')]); ?></td>
				<td width="100">Service Source </td> <td width="195">: <? echo $source[$dataArray[0][csf('source')]]; ?></td>
			</tr>
			<tr>
				<td width="100">Delivery To </td> <td width="195">: <? echo $supplier_arr[$dataArray[0][csf('supplier_id')]]; ?></td>
				<td width="100">Delivery Address </td> <td width="195">: <? echo $dataArray[0][csf('supplier_address')]; ?></td>
				<td width="100">Driver Name </td> <td width="195">: <? echo $dataArray[0][csf('driver_name')]; ?></td>
				<td width="100">Mobile No </td> <td width="195">: <? echo $dataArray[0][csf('mobile_no')]; ?></td>
			</tr>
			<tr>
				<td width="100">DL No </td> <td width="195">: <? echo $dataArray[0][csf('dl_no')]; ?></td>
				<td width="100">Transport </td> <td width="195">: <? echo $dataArray[0][csf('transport')]; ?></td>
				<td width="100">Vahical No </td> <td width="195">: <? echo $dataArray[0][csf('vehical_no')]; ?></td>
				<td width="100">Gate Pass No </td> <td width="195">: <? echo $dataArray[0][csf('gate_pass_no')]; ?></td>
			</tr>
			<tr>
				<td width="100">Remarks </td> <td colspan="7">: <? echo $dataArray[0][csf('remark')]; ?></td>
			</tr>
		</table>
		<br>
		<div style="width:100%;">
			<table align="left" cellspacing="0" width="1400"  border="1" rules="all" class="rpt_table"  >
				<thead>
					<tr> <!-- SL No	Issue Date	Challan No	Buyer	Style Ref. No	Fab. Booking No	FSO No	Batch No	Fab Color	Bodypart	Fabric Description	GSM	DIA	Process Type	Issue Qty.	No. Of Roll	Remarks -->

		        		<th width="30">SL No</th>
		                <th width="110">Buyer</th>
		                <th width="110">Style Ref. No</th>
		                <th width="110">Fab. Booking No</th>
		                <th width="110">FSO No</th>
		                <th width="110">Batch No</th>
		                <th width="80">Fab Color</th>
		                <th width="80">Bodypart</th>
		                <th width="120">Fabric Description</th>
		                <th width="80">GSM</th>
		                <th width="80">DIA</th>
		                <th width="80">Process Type</th>
		                <th width="80">Issue Qty.</th>
		                <th width="80">No. Of Roll</th>
		                <th>Remarks</th>
		        	</tr>
				</thead>
				<tbody>
				<?
				$tblRow=1; $i=1; $tot_issue_quantity=$tot_number_of_roll=0;
				foreach($qry_result as $row)
				{
					/*$qnty=$row['wo_qnty'];
					$amt=$row['amount'];
					$uom_wise_qnty +=$qnty;
					$uom_wise_amt +=$amt;
					if($data[3]==1) $buyer_buyer=$buyer_arr[$row['buyer_buyer']]; else $buyer_buyer=$row['buyer_buyer'];*/
					?> 
					<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
						<td width="30"><?  echo $tblRow; ?></td>
		                <td width="110"  style="word-break:break-all"><?  echo $buyer_arr[$dataArray[0][csf('buyer_id')]]; ?></td>
		                <td width="110"><?  echo $row[csf("style_ref_no")]; ?></td>
		                <td width="110"><?  echo $row[csf("sales_booking_no")]; ?></td>
		                <td width="110"><?  echo $row[csf("fso_number")]; ?></td>
		                <td width="110"><?  echo $row[csf("batch_no")]; ?></td>
		                <td width="80"><?  echo $color_arr[$row[csf("color_id")]]; ?></td>
		                <td width="80"><?  echo $body_part[$row[csf("body_part_id")]]; ?></td>
		                <td width="120"><?  echo $row[csf("fabric_desc")]; ?></td>
		                <td width="80"><?  echo $row[csf("gsm_weight")]; ?></td>
		                <td width="80"><?  echo $row[csf("dia")]; ?></td>
		                <td width="80"><?  echo $emblishment_print_type[$row[csf("process_type_id")]]; ?></td>
		                <td width="80" align="right"><?  echo number_format($row[csf("quantity")]); ?></td>
		                <td width="80" align="right"><?  echo number_format($row[csf("number_of_roll")]); ?></td>
		                <td><?  echo $row[csf("remark")]; ?></td>
					</tr>
					<?
					$tblRow++; 
					$tot_issue_quantity+=$row[csf("quantity")];
					$tot_number_of_roll+=$row[csf("number_of_roll")];
				}
				?>
				</tbody>
				<tfoot>
					<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
						<td colspan="12" align="right">Total</td>
		                <td width="80" align="right"><?  echo number_format($tot_issue_quantity); ?></td>
		                <td width="80" align="right"><?  echo number_format($tot_number_of_roll); ?></td>
		                <td>&nbsp;</td>
					</tr>
				</tfoot>
				</table>
			</div>
		</div>
		<br>
	</div>
	<?
    	echo signature_table(223, $data[0], "1400px");
    ?>
</div>
<?
exit();
}

if($action=="multi_issue_print")
{
	//select id, item_name from lib_item_group where item_category=4 and status_active=1
	extract($_REQUEST);
	$data=explode('*',$data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	//$store_name_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$source_for_order = array(1 => 'In-House', 2 => 'Sub-Contract');
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");

	$sql= "select c.id,c.company_id, c.po_buyer as buyer_id,c.style_ref_no,c.job_no as fso_number, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, b.color_id, b.body_part_id , b.fabric_desc, b.gsm_weight, b.dia ,b.id as so_dtls_id,e.id as batch_id,g.id as dtls_id, g.process_type_id, g.quantity, g.rate, g.amount, g.number_of_roll, d.batch_qnty as batch_weight, g.remark, g.mst_id  from 
		 fabric_sales_order_dtls b, fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e, wo_fabric_aop_dtls g ,product_details_master  h where b.mst_id=c.id and c.id=d.po_id and d.mst_id=e.id and g.sales_order_dtls_id=b.id and g.batch_id=e.id and e.color_id=b.color_id and d.prod_id=h.id  and g.mst_id in ($data[1]) and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and g.status_active=1 and g.is_deleted=0 group by  c.id,c.company_id, c.po_buyer,c.style_ref_no,c.job_no, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, b.color_id, b.body_part_id , b.fabric_desc, b.gsm_weight, b.dia ,b.id,e.id,g.id, g.process_type_id, g.quantity, g.rate, g.amount, g.number_of_roll,d.batch_qnty, g.remark, g.mst_id order by b.id desc";
		 //and h.item_description=b.fabric_desc

	$qry_result=sql_select($sql);
	$sql_mst="select id, entry_form, sys_no_prefix, sys_no_prefix_num, sys_no, sys_date, delivery_date, company_id, buyer_id,supplier_id,supplier_address, issue_purpose, source, order_id, order_no, attention, vehical_no, driver_name, dl_no, transport, mobile_no, gate_pass_no, is_short, remark from wo_fabric_aop_mst where id in ($data[1]) and entry_form=462 and status_active=1";

	$dataArray=sql_select($sql_mst);
	foreach ($dataArray as $row)
	{
		$mst_arr[$row[csf("id")]]['sys_date']=change_date_format($row[csf("sys_date")]);
		$mst_arr[$row[csf("id")]]['sys_no']=$row[csf("sys_no")];
	}
	//unset($mst_arr);
	$com_dtls = fnc_company_location_address($data[0], 0, 2);
	//echo "<pre>";
	//print_r($mst_arr);
	//die;

	?>
	<style type="text/css">
		td.make_bold {
	  		font-weight: 900;
		}
	</style>
	<div style="width:1600px;">
		<table width="1600" cellspacing="0" align="center" border="0">
			<tr>
            	<td  align="left"><img src="../../<? echo $com_dtls[2]; ?>" height="70" width="200"></td>
            	<td colspan="7" align="center"  style="font-size:xx-large; text-align:left;"><strong ><? echo $com_dtls[0]; ?></strong>
        	</tr>
	        <tr class="form_caption">
	            <td colspan="8" align="center"><? echo $com_dtls[1]; ?> </td>
	        </tr>
	        <tr>
	            <td colspan="8" style="font-size:large; text-align:center;" align="center"><strong ><? echo "Fabric Delivery Challan For AOP"; ?></strong> </td>
	        </tr>
		</table>
		<br>
		<table width="1600" cellspacing="0" align="center" border="0">
			<tr>			
				<td width="100">Company Name </td> <td width="195">: <? echo $company_library[$dataArray[0][csf('company_id')]]; ?></td>
				<td width="100">Service Source </td> <td width="195">: <? echo $source[$dataArray[0][csf('source')]]; ?></td>
				<td width="100">Delivery To </td> <td width="195">: <? echo $supplier_arr[$dataArray[0][csf('supplier_id')]]; ?></td>
				<td width="100">Delivery Address </td> <td width="195">: <? echo $dataArray[0][csf('supplier_address')]; ?></td>
			</tr>
			<tr>
				<td width="100">Driver Name </td> <td width="195">: <? echo $dataArray[0][csf('driver_name')]; ?></td>
				<td width="100">Mobile No </td> <td width="195">: <? echo $dataArray[0][csf('mobile_no')]; ?></td>
				<td width="100">DL No </td> <td width="195">: <? echo $dataArray[0][csf('dl_no')]; ?></td>
				<td width="100">Transport </td> <td width="195">: <? echo $dataArray[0][csf('transport')]; ?></td>
			</tr>
			<tr>
				<td width="100">Vahical No </td> <td width="195">: <? echo $dataArray[0][csf('vehical_no')]; ?></td>
				<td width="100">Gate Pass No </td> <td width="195">: <? echo $dataArray[0][csf('gate_pass_no')]; ?></td>
			</tr>
			<tr>
				<td width="100">Remarks </td> <td colspan="7">: <? echo $dataArray[0][csf('remark')]; ?></td>
			</tr>
		</table>
		<br>
		<div style="width:100%;">
			<table align="left" cellspacing="0" width="1600"  border="1" rules="all" class="rpt_table"  >
				<thead>
					<tr>
		        		<th width="30">SL No</th>
		        		<th width="60">Issue Date</th>
		        		<th width="140">Challan No</th>
		                <th width="110">Buyer</th>
		                <th width="110">Style Ref. No</th>
		                <th width="110">Fab. Booking No</th>
		                <th width="110">FSO No</th>
		                <th width="110">Batch No</th>
		                <th width="80">Fab Color</th>
		                <th width="80">Bodypart</th>
		                <th width="120">Fabric Description</th>
		                <th width="60">GSM</th>
		                <th width="60">DIA</th>
		                <th width="80">Process Type</th>
		                <th width="80">Issue Qty.</th>
		                <th width="80">No. Of Roll</th>
		                <th>Remarks</th>
		        	</tr>
				</thead>
				<tbody>
				<?
				$tblRow=1; $i=1; $tot_issue_quantity=$tot_number_of_roll=0;
				foreach($qry_result as $row)
				{
					/*$qnty=$row['wo_qnty'];
					$amt=$row['amount'];
					$uom_wise_qnty +=$qnty;
					$uom_wise_amt +=$amt;
					if($data[3]==1) $buyer_buyer=$buyer_arr[$row['buyer_buyer']]; else $buyer_buyer=$row['buyer_buyer'];*/
					?> 
					<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
						<td width="30"><?  echo $tblRow; ?></td>
		                <td width="60" ><?  echo $mst_arr[$row[csf("mst_id")]]['sys_date']; ?></td>
		                <td width="140"  style="word-break:break-all"><?  echo $mst_arr[$row[csf("mst_id")]]['sys_no']; ?></td>
		                <td width="110"  style="word-break:break-all"><?  echo $buyer_arr[$row[csf("buyer_id")]]; ?></td>
		                <td width="110"><?  echo $row[csf("style_ref_no")]; ?></td>
		                <td width="110"><?  echo $row[csf("sales_booking_no")]; ?></td>
		                <td width="110"><?  echo $row[csf("fso_number")]; ?></td>
		                <td width="110"><?  echo $row[csf("batch_no")]; ?></td>
		                <td width="80"><?  echo $color_arr[$row[csf("color_id")]]; ?></td>
		                <td width="80"><?  echo $body_part[$row[csf("body_part_id")]]; ?></td>
		                <td width="120"><?  echo $row[csf("fabric_desc")]; ?></td>
		                <td width="60"><?  echo $row[csf("gsm_weight")]; ?></td>
		                <td width="60"><?  echo $row[csf("dia")]; ?></td>
		                <td width="80"><?  echo $emblishment_print_type[$row[csf("process_type_id")]]; ?></td>
		                <td width="80" align="right"><?  echo number_format($row[csf("quantity")]); ?></td>
		                <td width="80" align="right"><?  echo number_format($row[csf("number_of_roll")]); ?></td>
		                <td><?  echo $row[csf("remark")]; ?></td>
					</tr>
					<?
					$tblRow++; 
					$tot_issue_quantity+=$row[csf("quantity")];
					$tot_number_of_roll+=$row[csf("number_of_roll")];
				}
				?>
				</tbody>
				<tfoot>
					<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
						<td colspan="14" align="right">Total</td>
		                <td width="80" align="right"><?  echo number_format($tot_issue_quantity); ?></td>
		                <td width="80" align="right"><?  echo number_format($tot_number_of_roll); ?></td>
		                <td>&nbsp;</td>
					</tr>
				</tfoot>
				</table>
				<table>
                    <tr height="20"><td></td></tr>
                    <tr>
                        <td valign="top"><strong>In-Words: </strong></td>
                        <td><? echo number_to_words(number_format($tot_issue_quantity),'','');?></td>
                    </tr>
                    <tr height="50"><td></td></tr>
                </table>
			</div>
		</div>
		<br>
	</div>
	<?
    	echo signature_table(223, $data[0], "1600px");
    ?>
</div>
<?
exit();
}
?>