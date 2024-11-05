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
			                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $sales_order_type;?>+'_'+'<? echo $hid_fab_booking;?>' , 'fabric_search_list_view', 'search_div', 'fabric_issue_for_aop_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" /> 
			                        </td>
			                    </tr>
			                    <tr>
			                    	<td align="center" valign="middle" colspan="9"><? echo load_month_buttons(1); ?>
			                    		<input type="hidden" class="text_boxes" readonly style="width:550px" id="txtSoDtlsId">
			                        	<input type="hidden" id="txtPreCostDtlsId">
			                        	<input type="hidden" id="txtBatchDtlsId">
			                        	<input type="hidden" id="txtBatchId">
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
		var selected_batch_dtls=new Array();
		var selected_batch=new Array();

		function js_set_value( str ) {
			if($("#search"+str).css("display") !='none'){
				toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
				
				if( jQuery.inArray( $('#txt_batch_dtls_id' + str).val(), selected_batch_dtls ) == -1 ) {
					
					selected_id.push( $('#txt_so_dtls_id' + str).val() );
					selected_item.push($('#pre_cost_dtls_id' + str).val());
					selected_batch_dtls.push($('#txt_batch_dtls_id' + str).val());
					selected_batch.push($('#txt_batch_id' + str).val());
					//alert(selected_batch_dtls);
				}
				else{
					for( var i = 0; i < selected_batch_dtls.length; i++ ) {
						if( selected_batch_dtls[i] == $('#txt_batch_dtls_id' + str).val() ) break;
					}
					selected_id.splice( i, 1 );
					selected_item.splice( i,1 );
					selected_batch_dtls.splice( i,1 );
					selected_batch.splice( i,1 );
				}
			}
			var id = '';
			var pre_cost_dtls_id='';
			var txt_batch_dtls_id='';
			var txt_batch_id='';
			for( var i = 0; i < selected_batch_dtls.length; i++ ) {
				id += selected_id[i] + ',';
				pre_cost_dtls_id+=selected_item[i]+ ',';
				txt_batch_dtls_id+=selected_batch_dtls[i]+ ',';
				txt_batch_id+=selected_batch[i]+ ',';
			}
			id = id.substr( 0, id.length - 1 );
			pre_cost_dtls_id = pre_cost_dtls_id.substr( 0, pre_cost_dtls_id.length - 1 );
			txt_batch_dtls_id = txt_batch_dtls_id.substr( 0, txt_batch_dtls_id.length - 1 );
			txt_batch_id = txt_batch_id.substr( 0, txt_batch_id.length - 1 );
			$('#txtSoDtlsId').val( id );
			$('#txtPreCostDtlsId').val( pre_cost_dtls_id );
			$('#txtBatchDtlsId').val( txt_batch_dtls_id );
			$('#txtBatchId').val( txt_batch_id );
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
	$hid_fab_booking=$data[9];
	//6_200_1_19_1____0_UG-Fb-21-00246
	
	if ($company!=0)
	{
		if($cbo_within_group==1){
			if($without_order==0){
				//$company_cond=" and c.company_id='$company'";
				$company_cond=" and c.po_company_id='$company'";
				if ($buyer!=0) $buyer_cond=" and c.po_buyer='$buyer'"; else $buyer_cond='';
				//if ($supplier_name!=0) $supplier_cond=" and f.supplier_id='$supplier_name'"; else $supplier_cond='';
			}
			else{
				$company_cond=" and f.company_id='$company'";
				if ($buyer!=0) $buyer_cond=" and f.buyer_id='$buyer'"; else $buyer_cond='';
			}
			//$company_cond=" and f.company_id='$company'";
			//if ($buyer!=0) $buyer_cond=" and f.buyer_id='$buyer'"; else $buyer_cond='';
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
	// echo $buyer_cond; die;
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

	$batch_dtls_id_str=",listagg(d.id,',') within group (order by d.id)  as batch_dtls_id";

	if($cbo_within_group==1 ){
		if($without_order==0)
		{
			/*$sql= "select f.id,f.company_id, f.buyer_id, 0 as job_no,c.style_ref_no,c.job_no as fso_number, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, e.color_id, b.body_part_id , b.fabric_desc, b.gsm_weight, b.dia, sum(d.batch_qnty) as batch_weight ,e.id as batch_id,d.id as batch_dtls_id from 
			wo_pre_cost_fabric_cost_dtls a, fabric_sales_order_dtls b, fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e, wo_booking_mst f , product_details_master g where a.id=b.pre_cost_fabric_cost_dtls_id and b.mst_id=c.id and c.id=d.po_id and d.mst_id=e.id  and f.tagged_booking_no = c.sales_booking_no and d.prod_id=g.id  and f.booking_type=3 and f.process=35 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and c.sales_booking_no ='$hid_fab_booking' $company_cond $buyer_cond $within_group_cond $batch_date $supplier_cond $search_cond group by  f.id,f.company_id, f.buyer_id,a.job_no,c.style_ref_no,c.job_no, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, e.color_id, b.body_part_id , b.fabric_desc, b.gsm_weight, b.dia,e.id,d.id order by f.id desc"; *///die;

			/*$sql= "SELECT f.id,f.company_id,f.buyer_id,  0 as job_no, c.style_ref_no,c.job_no as fso_number,c.within_group,c.sales_booking_no,e.batch_no,e.extention_no,e.color_id,d.body_part_id,g.item_description as fabric_desc,g.gsm as gsm_weight,g.dia_width as dia ,sum (d.batch_qnty) as batch_weight,e.id as batch_id $batch_dtls_id_str from fabric_sales_order_mst c, pro_batch_create_dtls d, pro_batch_create_mst e, wo_booking_mst f, product_details_master g where c.id = d.po_id and d.mst_id = e.id and f.tagged_booking_no = c.sales_booking_no and d.prod_id = g.id and f.booking_type = 3 and f.process = 35 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and c.sales_booking_no ='$hid_fab_booking' $company_cond $buyer_cond $within_group_cond $batch_date $supplier_cond $search_cond group by   f.id,f.company_id,f.buyer_id, c.style_ref_no,c.job_no,c.within_group,c.sales_booking_no,e.batch_no,e.extention_no,e.color_id,d.body_part_id,g.item_description,g.gsm,g.dia_width,e.id order by f.id desc";*/ //die;

			$sql= "select c.id,c.company_id, c.po_buyer as buyer_id,c.style_ref_no,c.job_no as fso_number, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no,e.color_id,d.body_part_id,g.item_description as fabric_desc,g.gsm as gsm_weight,g.dia_width as dia, sum(d.batch_qnty) as batch_weight ,e.id as batch_id $batch_dtls_id_str  from 
		  fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e , product_details_master  g where  c.id=d.po_id and d.mst_id=e.id and d.prod_id=g.id  and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1  $company_cond $buyer_cond $within_group_cond $batch_date $search_cond and c.sales_booking_no='$hid_fab_booking' group by  c.id,c.company_id, c.po_buyer,c.style_ref_no,c.job_no, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no,e.color_id,d.body_part_id,g.item_description,g.gsm,g.dia_width,e.id order by c.id desc";



			
			//,b.id as so_dtls_id
			//and e.color_id=b.color_id and g.item_description=trim(b.fabric_desc) and b.body_part_id= d.body_part_id
		}else{
			/*$sql= "select c.id,f.company_id, f.buyer_id,c.style_ref_no,c.job_no as fso_number, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, b.color_id, b.body_part_id , b.fabric_desc, b.gsm_weight, b.dia, sum(d.batch_qnty) as batch_weight ,b.id as so_dtls_id,e.id as batch_id from 
			 fabric_sales_order_dtls b, fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e, wo_non_ord_samp_booking_mst f, product_details_master g  where  b.mst_id=c.id and c.id=d.po_id and d.mst_id=e.id and f.booking_no = c.sales_booking_no and e.color_id=b.color_id and f.booking_type=4 and f.pay_mode=5 and f.supplier_id=c.company_id and b.body_part_id= d.body_part_id  and d.prod_id=g.id and g.item_description= trim(b.fabric_desc) and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and f.booking_no ='$hid_fab_booking' $company_cond $buyer_cond $within_group_cond $batch_date $supplier_cond $search_cond group by  c.id,f.company_id, f.buyer_id,c.style_ref_no,c.job_no, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, b.color_id, b.body_part_id , b.fabric_desc, b.gsm_weight, b.dia,b.id,e.id order by c.id desc";*/

			 $sql= "select c.id,f.company_id, f.buyer_id,c.style_ref_no,c.job_no as fso_number, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, e.color_id,d.body_part_id,g.item_description as fabric_desc,g.gsm as gsm_weight,g.dia_width as dia, sum(d.batch_qnty) as batch_weight ,e.id as batch_id $batch_dtls_id_str from 
			  fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e, wo_non_ord_samp_booking_mst f, product_details_master g  where c.id=d.po_id and d.mst_id=e.id and f.booking_no = c.sales_booking_no and f.booking_type=4 and f.pay_mode=5 and f.supplier_id=c.company_id and d.prod_id=g.id and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and f.booking_no ='$hid_fab_booking' $company_cond $buyer_cond $within_group_cond $batch_date $supplier_cond $search_cond group by  c.id,f.company_id, f.buyer_id,c.style_ref_no,c.job_no, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no,e.color_id,d.body_part_id,g.item_description,g.gsm,g.dia_width,e.id order by c.id desc";
		}
	}else{
		$sql= "select c.id,c.company_id, c.buyer_id,c.style_ref_no,c.job_no as fso_number, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no,e.color_id,d.body_part_id,g.item_description as fabric_desc,g.gsm as gsm_weight,g.dia_width, sum(d.batch_qnty) as batch_weight ,e.id as batch_id $batch_dtls_id_str  from 
		  fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e , product_details_master  g where  c.id=d.po_id and d.mst_id=e.id and d.prod_id=g.id  and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1  $company_cond $buyer_cond $within_group_cond $batch_date $search_cond and c.sales_booking_no='$hid_fab_booking' group by  c.id,c.company_id, c.buyer_id,c.style_ref_no,c.job_no, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no,e.color_id,d.body_part_id,g.item_description,g.gsm,g.dia_width,e.id order by c.id desc";
		 // $supplier_cond  and g.item_description=b.fabric_desc
	}
	
	//echo $sql;

	//pro_batch_create_dtls -> po_id id ->fabric_sales_order_mst   wo_pre_cost_fabric_cost_dtls -> id  pre_cost_fabric_cost_dtls_id-> fabric_sales_order_dtls
	$sql_data=sql_select($sql);//. $currency_cond

	//f.id,f.company_id, f.buyer_id,a.job_no,c.style_ref_no,c.job_no as fso_number, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, b.color_id, b.body_part_id , b.fabric_desc, b.gsm_weight, b.dia, sum(d.batch_qnty) as batch_weight ,e.id as batch_id

	foreach ($sql_data as $row)
	{
		$batch_wise_so_arr[$row[csf("id")]][$row[csf("company_id")]][$row[csf("buyer_id")]][$row[csf("job_no")]][$row[csf("within_group")]][$row[csf("fso_number")]][$row[csf("style_ref_no")]][$row[csf("sales_booking_no")]][$row[csf("batch_id")]][$row[csf("batch_no")]][$row[csf("extention_no")]][$row[csf("color_id")]][$row[csf("body_part_id")]][$row[csf("fabric_desc")]][$row[csf("gsm_weight")]][$row[csf("dia")]][$row[csf("batch_weight")]]['batch_dtls_id'] .=$row[csf("batch_dtls_id")].',';
	}
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

	/*foreach($batch_wise_so_arr as $booking_id=> $booking_id_data)
	{
		foreach($booking_id_data as $company_id=> $company_id_data)
		{ 
			foreach($company_id_data as $buyer_id=> $buyer_id_data)
			{
				foreach($buyer_id_data as $job_no=> $job_no_data)
				{ 
					foreach($job_no_data as $within_group=> $within_group_data)
					{
						foreach($within_group_data as $fso_number => $fso_number_data)
						{
							foreach($fso_number_data as $style_ref_no=> $style_ref_no_data)
							{
								foreach($style_ref_no_data as $sales_booking_no=> $sales_booking_no_data)
								{ 
									foreach($sales_booking_no_data as $batch_id=> $batch_id_data)
									{
										foreach($batch_id_data as $batch_no=> $batch_no_data)
										{ 
											foreach($batch_no_data as $extention_no=> $extention_no_data)
											{
												foreach($extention_no_data as $color_id => $color_id_data)
												{
													foreach($color_id_data as $body_part_id => $body_part_id_data)
													{
														foreach($body_part_id_data as $fabric_desc=> $fabric_desc_data)
														{
															foreach($fabric_desc_data as $gsm_weight=> $gsm_weight_data)
															{ 
																foreach($gsm_weight_data as $dia=> $dia_data)
																{
																	foreach($dia_data as $batch_weight=> $row)
																	{ 
																		?>
																        <tr style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
																            <td width="30"><? echo $i; ?>
																                <input type="hidden" name="txt_so_dtls_id" id="txt_so_dtls_id<?php echo $i ?>" value="<? echo $so_dtls_id; ?>"/>
																                <input type="hidden" name="txt_pre_cost_dtls_id" id="txt_pre_cost_dtls_id<?php echo $i ?>" value="<? echo $pre_cost_dtls_id; ?>"/>
																                <input type="hidden" name="txt_batch_id" id="txt_batch_id<?php echo $i ?>" value="<? echo $batch_id; ?>"/>
																                <input type="hidden" name="txt_batch_dtls_id" id="txt_batch_dtls_id<?php echo $i ?>" value="<? echo $batch_dtls_id; ?>"/>
																            </td>
																            <td width="150"><p><? echo $company_library[$company_id]; ?></p></td>
																            <td width="150"><p><? echo $buyer_arr[$buyer_id]; ?></p></td>
																            <td width="100"><p><? echo $style_ref_no; ?></p></td>
																            <td width="100"><p><? echo $fso_number; ?></p></td>
																            <td width="100"><p><? echo $sales_booking_no; ?></p></td>
																            <td width="100"><p><? echo $batch_no; ?></p></td>
																            <td width="70"><p><? echo $extention_no; ?></p></td>
																            <td width="70"><p><? echo $color_library[$color_id]; ?></p></td>
																            <td width="80"><p><? echo $body_part[$body_part_id]; ?></p></td>
																            <td width="200"><p><? echo $fabric_desc; ?></p></td>
																            <td width="70"><p><? echo $gsm_weight; ?></p></td>
																            <td width="70"><p><? echo $dia; ?></p></td>
																            <td width=""><p><? echo $batch_weight; ?></p></td>
																        </tr>
																        <?
																		$i++;
																		
																	}
																}
															}
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}*/



	foreach($sql_data as $sql_row)
	{
		?>
        <tr style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
            <td width="30"><? echo $i; ?>
                <input type="hidden" name="txt_so_dtls_id" id="txt_so_dtls_id<?php echo $i ?>" value="<? echo $sql_row[csf('so_dtls_id')]; ?>"/>
                <input type="hidden" name="txt_pre_cost_dtls_id" id="txt_pre_cost_dtls_id<?php echo $i ?>" value="<? echo $sql_row[csf('pre_cost_dtls_id')]; ?>"/>
                <input type="hidden" name="txt_batch_id" id="txt_batch_id<?php echo $i ?>" value="<? echo $sql_row[csf('batch_id')]; ?>"/>
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
	
	//echo $is_update; die;
	if($is_update==2)
	{
		$mst_id=$data[1];
	}
	else
	{
		$txtSoDtlsId=$data[1];
	}
	$txtPreCostDtlsId=$data[2];
	$txtBatchMstId=$data[3];
	$txt_order_no=$data[4];
	$within_group=$data[5];
	$without_order=$data[6];
	$fab_booking=$data[7];
	$batchDtlsId=$data[8];
	$rowNo=$data[9];
	//$batch_dtls_id_str=",listagg(d.id,',') within group (order by d.id)  as batch_dtls_id";
	if($db_type==0) $batch_dtls_id_str=",group_concat(d.id) as batch_dtls_id";
	else if($db_type==2) $batch_dtls_id_str=",rtrim(xmlagg(xmlelement(e,d.id,',').extract('//text()') order by d.id).GetClobVal(),',') as batch_dtls_id";
	if($is_update==1) // save
	{
		if($within_group==1)
		{

			if($without_order==0)
			{
				/*$sql= "SELECT f.id,f.company_id,f.buyer_id,  0 as job_no, c.style_ref_no,c.job_no as fso_number,c.within_group,c.sales_booking_no,e.batch_no,e.extention_no,e.color_id,d.body_part_id,g.item_description as fabric_desc,g.gsm as gsm_weight,g.dia_width as dia , sum(d.roll_no) as number_of_roll ,sum (d.batch_qnty) as batch_weight,e.id as batch_id,g.id as prod_id $batch_dtls_id_str from fabric_sales_order_mst c, pro_batch_create_dtls d, pro_batch_create_mst e, wo_booking_mst f, product_details_master g where c.id = d.po_id and d.mst_id = e.id and f.tagged_booking_no = c.sales_booking_no and d.prod_id = g.id and f.booking_type = 3 and f.process = 35  and e.id in ($txtBatchMstId)  and d.id in ($batchDtlsId) and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 group by   f.id,f.company_id,f.buyer_id, c.style_ref_no,c.job_no,c.within_group,c.sales_booking_no,e.batch_no,e.extention_no,e.color_id,d.body_part_id,g.item_description,g.gsm,g.dia_width,e.id,g.id order by f.id desc"; */
				$sql= "select c.id,c.company_id, c.po_buyer as buyer_id,c.style_ref_no,c.job_no as fso_number, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no,e.color_id,d.body_part_id,g.item_description as fabric_desc,g.gsm as gsm_weight,g.dia_width as dia , sum(d.roll_no) as number_of_roll ,sum (d.batch_qnty) as batch_weight,e.id as batch_id,g.id as prod_id $batch_dtls_id_str from 
			fabric_sales_order_dtls b, fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e , product_details_master  g where  b.mst_id=c.id and c.id=d.po_id and d.mst_id=e.id and d.prod_id=g.id  and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1  and e.id in ($txtBatchMstId) and c.sales_booking_no='$fab_booking' and d.id in ($batchDtlsId) group by  c.id,c.company_id, c.po_buyer,c.style_ref_no,c.job_no, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no,e.color_id,d.body_part_id,g.item_description,g.gsm,g.dia_width,e.id,g.id order by c.id desc";
			//and d.body_part_id=b.body_part_id
			}else
			{
				$sql= "select f.id,f.company_id, c.po_buyer as buyer_id ,c.style_ref_no,c.job_no as fso_number, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no,e.color_id,d.body_part_id,g.item_description as fabric_desc,g.gsm as gsm_weight,g.dia_width as dia , sum(d.roll_no) as number_of_roll ,sum (d.batch_qnty) as batch_weight,e.id as batch_id,g.id as prod_id $batch_dtls_id_str  from 
				 fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e, wo_non_ord_samp_booking_mst f , product_details_master g where c.id=d.po_id and d.mst_id=e.id and f.booking_no = c.sales_booking_no and f.booking_type=4 and f.pay_mode=5 and f.supplier_id=c.company_id  and d.prod_id=g.id and f.booking_no='$fab_booking' and e.id in ($txtBatchMstId) and d.id in ($batchDtlsId)  and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 group by  f.id,f.company_id, c.po_buyer,c.style_ref_no,c.job_no, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no,e.color_id,d.body_part_id,g.item_description,g.gsm,g.dia_width,e.id,g.id order by f.id desc";
				 // and g.item_description=b.fabric_desc

			}
		}
		else
		{
			$sql= "select c.id,c.company_id, c.buyer_id,c.style_ref_no,c.job_no as fso_number, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no,e.color_id,d.body_part_id,g.item_description as fabric_desc,g.gsm as gsm_weight,g.dia_width as dia , sum(d.roll_no) as number_of_roll ,sum (d.batch_qnty) as batch_weight,e.id as batch_id,g.id as prod_id $batch_dtls_id_str from 
			fabric_sales_order_dtls b, fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e , product_details_master  g where  b.mst_id=c.id and c.id=d.po_id and d.mst_id=e.id and d.prod_id=g.id and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1  and e.id in ($txtBatchMstId) and c.sales_booking_no='$fab_booking' and d.id in ($batchDtlsId) group by  c.id,c.company_id, c.buyer_id,c.style_ref_no,c.job_no, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no,e.color_id,d.body_part_id,g.item_description,g.gsm,g.dia_width,e.id,g.id order by c.id desc";
		}
	}
	else
	{ 
		$batch_dtls_id_sql= "select g.batch_dtls_id from 
		   wo_fabric_aop_dtls g where g.mst_id=$mst_id and g.is_deleted=0 and g.status_active=1 ";
		$batch_dtls_sqlArray=sql_select($batch_dtls_id_sql); $prev_issue_qty_arr=array();
		foreach ($batch_dtls_sqlArray as  $row) 
		{
			//echo $mstIDS.'=='.$ids.'=='.$book_con_dtls_ids.'++'; 
			$prev_issue_qty_arr[$row[csf("batch_id")]][$row[csf("batch_dtls_id")]]["quantity"] +=$row[csf("quantity")];
			$batch_dtls_id .=$row[csf("batch_dtls_id")].',';
		}
		$batch_dtls_id=chop($batch_dtls_id,',');
		$batch_dtls_id=implode(",",array_unique(explode(",",$batch_dtls_id)));
		if($batch_dtls_id!=''){
			$batch_dtls_id_cond =" and d.id in ($batch_dtls_id)";
		}

		  $sql= "SELECT c.id, c.company_id, c.po_buyer AS buyer_id, c.style_ref_no, c.job_no AS fso_number, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, e.color_id, h.item_description AS fabric_desc, h.gsm AS gsm_weight, h.dia_width AS dia, e.id AS batch_id, g.id AS dtls_id, g.batch_dtls_id, g.process_type_id, g.quantity, g.rate, g.amount, g.number_of_roll, g.remark,h.id as prod_id
		FROM  fabric_sales_order_mst c, pro_batch_create_dtls d, pro_batch_create_mst e, wo_fabric_aop_dtls g, product_details_master h
		WHERE   c.id = d.po_id AND d.mst_id = e.id AND g.batch_id = e.id AND d.prod_id = h.id and d.mst_id=e.id and g.batch_id=e.id and d.prod_id=h.id and d.prod_id = g.prod_id and g.mst_id=$mst_id $batch_dtls_id_cond and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and g.status_active=1 and g.is_deleted=0 group by  c.id, c.company_id, c.po_buyer, c.style_ref_no, c.job_no, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, e.color_id, h.item_description, h.gsm, h.dia_width, e.id, g.id, g.batch_dtls_id, g.process_type_id, g.quantity, g.rate, g.amount, g.number_of_roll, g.remark,h.id order by e.id desc";

		$received_sql= "select g.issue_dtls_id from wo_fabric_aop_dtls g, wo_fabric_aop_mst h where h.id=g.mst_id and h.entry_form=467 and h.issue_id=$mst_id and g.status_active=1 and g.is_deleted=0 and h.status_active=1 and h.is_deleted=0 order by h.id desc";
		$received_sql_res=sql_select($received_sql);
		foreach($received_sql_res as $row){
			$issue_dtls_arr[$row[csf("issue_dtls_id")]]["issue_dtls_id"] =$row[csf("issue_dtls_id")];
		}
		 
		/*$batch_dtls_sql= "select  d.id,d.body_part_id,d.batch_qnty from 
		   pro_batch_create_dtls d where d.is_deleted=0 and d.status_active=1 $batch_dtls_id_cond";
		$batch_dtlsArray=sql_select($batch_dtls_sql);
		foreach ($batch_dtlsArray as $row) 
		{
			$batch_dtls_arr[$row[csf("id")]]["body_part_id"] =$row[csf("body_part_id")];
			$batch_dtls_arr[$row[csf("id")]]["batch_qnty"] =$row[csf("batch_qnty")];
		}
*/
		$sql_data=sql_select($sql);
		 $batch_ids='';
		foreach($sql_data as $row)
		{
			$batch_ids .=$row[csf("batch_id")].',';
		}
		$batch_ids=implode(",",array_unique(explode(",",chop($batch_ids,','))));

		if ($batch_ids!="")
		{
			$batch_ids=explode(",",$batch_ids);
			$batch_idsCond=""; 
			//echo count($batch_ids); die;
			if($db_type==2 && count($batch_ids)>=999)
			{
				$chunk_arr=array_chunk($batch_ids,999);
				foreach($chunk_arr as $val)
				{
					$ids=implode(",",$val);
					if($batch_idsCond=="")
					{
						$batch_idsCond.=" and ( e.id in ( $ids) ";
					}
					else
					{
						$batch_idsCond.=" or  e.id in ( $ids) ";
					}
				}
				$batch_idsCond.=")";
			}
			else
			{
				$ids=implode(",",$batch_ids);
				$batch_idsCond.=" and e.id in ($ids) ";
			}
		} 

		


		//and h.item_description=b.fabric_desc 
	}

	$batch_dtls_sql= "select d.id,d.body_part_id,d.batch_qnty from pro_batch_create_dtls d , pro_batch_create_mst e where d.mst_id = e.id  and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 $batch_company $batch_idsCond";
	$batch_dtlsArray=sql_select($batch_dtls_sql);
	foreach ($batch_dtlsArray as $row) 
	{
		$batch_dtls_arr[$row[csf("id")]]["body_part_id"] =$row[csf("body_part_id")];
		$batch_dtls_arr[$row[csf("id")]]["batch_qnty"] =$row[csf("batch_qnty")];
	}

	//echo $sql;
	$prev_qty_sql= "select d.id as batch_dtls_id,e.id as batch_id, g.quantity from 
		  fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e, wo_fabric_aop_dtls g where c.id=d.po_id and d.mst_id=e.id and f.id=g.order_id and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and g.status_active=1 and g.is_deleted=0 group by b.id,e.id,g.quantity";
	$prev_qty_sqlArray=sql_select($prev_qty_sql); $prev_issue_qty_arr=array();
	foreach ($prev_qty_sqlArray as  $row) 
	{
		//echo $mstIDS.'=='.$ids.'=='.$book_con_dtls_ids.'++'; 
		$prev_issue_qty_arr[$row[csf("batch_id")]][$row[csf("batch_dtls_id")]]["quantity"] +=$row[csf("quantity")];
	}


	 
	//pro_batch_create_dtls -> po_id id ->fabric_sales_order_mst   wo_pre_cost_fabric_cost_dtls -> id  pre_cost_fabric_cost_dtls_id-> fabric_sales_order_dtls
	$sql_data=sql_select($sql);//. $currency_cond
	
	/*echo "<pre>";
	print_r($sql_data);
	die;*/
	//echo $sql;

	
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$color_library=return_library_array("select id,color_name from lib_color", "id", "color_name");
	//echo "select buyer_id from wo_fabric_aop_mst where id =$mst_id and entry_form=462 and is_deleted=0 and status_active=1";
	$buyer_id=return_field_value("buyer_id"," wo_fabric_aop_mst","id =$mst_id and entry_form=462 and is_deleted=0 and status_active=1");
	if($rowNo!='')
	{
		$i=$rowNo;
	}
	else
	{
		$i=1;
	}
	
	foreach($sql_data as $sql_row)
	{
		
		$prev_issue_qty=$balance=0; $next_process=$batchDtlsIds='';
		$number_of_roll=$bodyPartID=$batchQty='';
		
		//echo "10**".$sql_row[csf("batch_dtls_id")];  die;
		if($db_type==2 && $sql_row[csf("batch_dtls_id")]!="")
		{ 
		
		
			if($is_update==1){
			$batch_dtls_id = $sql_row[csf("batch_dtls_id")]->load();
			}else{ $batch_dtls_id = $sql_row[csf("batch_dtls_id")];}
		}
		
		$batch_dtls_id=array_unique(explode(",",$batch_dtls_id));
		//echo "10**".$batch_dtls_id;  die;
		foreach ($batch_dtls_id as $value) 
		{
			$batchDtlsIds .=$value.',';
			$prev_issue_qty +=$prev_issue_qty_arr[$sql_row[csf("batch_id")]][$sql_row[csf("batch_dtls_id")]]["quantity"];
		}
		
		if($is_update==1){
			$balance=$sql_row[csf('batch_weight')]-$prev_issue_qty;
		}else{
			$balance=($sql_row[csf('batch_weight')]-$prev_issue_qty)+$sql_row[csf('quantity')];
		}
		
		if($is_update!=1)
		{
			$number_of_roll=$sql_row[csf('number_of_roll')];
			foreach ($batch_dtls_id as $value) 
			{
				$bodyPartID=$batch_dtls_arr[$value]["body_part_id"];
				$batchQty +=$batch_dtls_arr[$value]["batch_qnty"];
			}
			$buyer_id=$buyer_id;
			$next_process=$issue_dtls_arr[$sql_row[csf('dtls_id')]]["issue_dtls_id"];
			if($next_process!=''){
				$disabled='disabled';
			}else{
				$disabled='';
			}
		}
		else
		{
			foreach ($batch_dtls_id as $value)
			 {
				//echo  $value.'=='; 
				$bodyPartID=$batch_dtls_arr[$value]["body_part_id"];
				$batchQty +=$batch_dtls_arr[$value]["batch_qnty"];
			}
			//$bodyPartID=$sql_row[csf('body_part_id')];
			//$batchQty =$sql_row[csf('batch_weight')];
			$buyer_id=$sql_row[csf('buyer_id')];
		}
		$batchDtlsIds=chop($batchDtlsIds,',');
		//echo $batchQty;
		
		
		?>
        
        
		<tr align="center" id="row_<? echo $i;?>">
			<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"><? echo $i; ?></td>
			<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"><? echo $buyer_arr[$buyer_id]; ?></td>
			<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"><? echo $sql_row[csf('style_ref_no')]; ?></td>
			<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"><? echo $sql_row[csf('fso_number')]; ?></td>
			<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"><? echo $sql_row[csf('sales_booking_no')]; ?></td>
			<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"><? echo $sql_row[csf('batch_no')]; ?></td>
			<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"><? echo $color_library[$sql_row[csf('color_id')]]; ?></td>
			<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"><? echo $body_part[$bodyPartID]; ?></td>
			<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"><? echo $sql_row[csf('fabric_desc')]; ?></td>
			<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"><? echo $sql_row[csf('gsm_weight')]; ?></td>
			<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"><? echo $sql_row[csf('dia')]; ?></td>
			<td width="120" ><? 
			asort($emblishment_print_type);
			echo create_drop_down( "cboProcessType_".$i, 120, $emblishment_print_type,"", 1, "-- Select --",$sql_row[csf('process_type_id')],'','','','','','','','',"cboProcessType[]"); ?></td>
			<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"><? echo number_format($batchQty,2,'.',''); ?></td>
			<td><input type="text" name="txtWoqnty[]" id="txtWoqnty_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onKeyUp="calculate_amount(<? echo $i; ?>)" value="<? echo number_format($sql_row[csf('quantity')],2,'.',''); ?>"; placeholder="<? echo number_format($batchQty,2,'.','') ; ?>" /></td>
			<td><input type="text" name="txtRate[]" id="txtRate_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" onKeyUp="calculate_amount(<? echo $i; ?>)" value="<? echo $sql_row[csf('rate')]; ?>";></td>
			<td><input type="text" name="txtAmount[]" id="txtAmount_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" value="<? echo number_format($sql_row[csf('amount')],4,'.',''); ?>";  disabled="disabled"/>

			<td><input type="text" name="txtNumberRoll[]"  id="txtNumberRoll_<? echo $i; ?>" style="width:60px;" class="text_boxes_numeric" value="<? echo $number_of_roll; ?>" /></td>
			<td><input type="text" name="txtRemarks[]"  id="txtRemarks_<? echo $i; ?>" style="width:60px;" class="text_boxes" value="<? echo $sql_row[csf('remark')]; ?>";/></td>
			<td><input type="button" id="decreaseset_<? echo $k;?>" name="decreaseset[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deletebreak_down_tr(<? echo $i;?>,'tbl_dtls_emb','row_');"  <? echo $disabled; ?>  />
			
			<input type="hidden" id="soDtlsId_<? echo $k;?>" name="soDtlsId[]"  value="<? echo $sql_row[csf('so_dtls_id')]; ?>"  />
			<input type="hidden" id="batchDtlsId_<? echo $k;?>" name="batchDtlsId[]"  value="<? echo $batchDtlsIds; ?>"  />
			<input type="hidden" id="hiddenid_<? echo $k;?>" name="hiddenid[]"  value="<? echo $sql_row[csf('dtls_id')]; ?>"  /></td>
			<input type="hidden" id="batchId_<? echo $k;?>" name="batchId[]"  value="<? echo $sql_row[csf('batch_id')]; ?>"  /></td>
			<input type="hidden" id="prodId_<? echo $k;?>" name="prodId[]"  value="<? echo $sql_row[csf('prod_id')]; ?>"  /></td>
		</tr>
		<?
		$i++;
	}
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
		$field_array="id, entry_form, sys_no_prefix, sys_no_prefix_num, sys_no, sys_date, delivery_date, company_id, supplier_id, supplier_address, issue_purpose, source, order_id, order_no, attention, vehical_no, driver_name, dl_no, transport, mobile_no, gate_pass_no, is_short, remark,buyer_id, within_group, lc_company_name, fab_booking_no,order_type, currency_id, inserted_by, insert_date"; 

		$data_array ="(".$id.", 462 ,'".$new_booking_no[1]."',".$new_booking_no[2].",'".$new_booking_no[0]."',".$txt_issue_date.",".$txt_delivery_date.",".$cbo_company_name.",".$cbo_supplier_name.",".$txt_party_location.",".$cbo_issue_purpose.",".$cbo_source.",".$hid_order_id.",".$txt_order_no.",".$txt_attention.",".$txt_vehical_no.",".$txt_driver_name.",".$txt_dl_no.",".$txt_transport.",".$txt_cell_no.",".$txt_gate_pass_no.",".$cbo_is_short.",".$txt_remarks.",".$buyer_id.",".$within_group.",".$cbo_lc_company_name.",".$hid_fab_booking.",".$sales_order_type.",".$cbo_currency_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

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

		$field_array_up="sys_date*delivery_date*supplier_id*supplier_address*issue_purpose*source*order_id*order_no*attention*vehical_no*driver_name*dl_no*transport*mobile_no*gate_pass_no*is_short*remark*currency_id*updated_by*update_date";

		$data_array_up ="".$txt_issue_date."*".$txt_delivery_date."*".$cbo_supplier_name."*".$txt_party_location."*".$cbo_issue_purpose."*".$cbo_source."*".$hid_order_id."*".$txt_order_no."*".$txt_attention."*".$txt_vehical_no."*".$txt_driver_name."*".$txt_dl_no."*".$txt_transport."*".$txt_cell_no."*".$txt_gate_pass_no."*".$cbo_is_short."*".$txt_remarks."*".$cbo_currency_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
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
	
		$is_received=0;
		$sql=sql_select("select sys_no from wo_fabric_aop_mst where issue_id=$update_id and status_active=1 and is_deleted=0");
		foreach($sql as $row){
			$is_received .=$row[csf('sys_no')].',';
		}
		$received_nos = implode(",",array_filter(array_unique(explode(",", chop($is_received,",")))));
		
		if($received_nos)
		{
			echo "20**Delete not Possible. Receive Found.\n Receive No. = $received_nos";disconnect($con); die;
		}
		
		$con = connect();
		if($db_type==0){
		mysql_query("BEGIN");
		}

		$field_array_up ="status_active*is_deleted*updated_by*update_date";
		$data_array_up 	="'0'*'1'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$rID=sql_update("wo_fabric_aop_mst",$field_array_up,$data_array_up,"id","".$update_id."",0);
		$rID1=sql_update("wo_fabric_aop_dtls",$field_array_up,$data_array_up,"mst_id","".$update_id."",0);
		
		if($db_type==0){
			if($rID==1 && $rID1==1 ){
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$txt_system_id).'**'.str_replace("'","",$update_id);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_system_id).'**'.str_replace("'","",$update_id);
			}
		}
		else if($db_type==2 || $db_type==1 ){
			if($rID==1 && $rID1==1){
				oci_commit($con);
				echo "2**".str_replace("'","",$txt_system_id).'**'.str_replace("'","",$update_id);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_system_id).'**'.str_replace("'","",$update_id);
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
		$field_array1=" id, mst_id, order_id, batch_id, sales_order_dtls_id, batch_dtls_id, prod_id, process_type_id, quantity, rate, amount, number_of_roll, remark, inserted_by, insert_date";

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
			$prodId				= "prodId_".$i; 
			
			if ($add_commaa!=0) $data_array1 .=","; $add_comma=0;
			$data_array1 .="(".$id_dtls.",".$update_id.",".$hid_order_id.",".$$batchId.",".$$soDtlsId.",".$$batchDtlsId.",".$$prodId.",".$$cboProcessType.",".str_replace(",",'',$$txtWoqnty).",".str_replace(",",'',$$txtRate).",".str_replace(",",'',$$txtAmount).",".$$txtNumberRoll.",".$$txtRemarks.",'".$user_id."','".$pc_date_time."')";
			$id_dtls=$id_dtls+1; $add_commaa++;
			//echo "10**INSERT INTO subcon_ord_breakdown (".$field_array3.") VALUES ".$data_array3; die;			
		}
		//echo "10**INSERT INTO wo_fabric_aop_dtls (".$field_array1.") VALUES ".$data_array1;die;
		$rID=sql_insert("wo_fabric_aop_dtls",$field_array1,$data_array1,0);
		
		//echo "10**".$rID;die;
		
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
		
		$field_array_up1="order_id*batch_id*sales_order_dtls_id*batch_dtls_id*prod_id*process_type_id*quantity*rate*amount*number_of_roll*remark*status_active*is_deleted*updated_by*update_date";

		$id_dtls=return_next_id( "id", "wo_fabric_aop_dtls", 1 ) ;
		$field_array1=" id, mst_id, order_id, batch_id, sales_order_dtls_id, batch_dtls_id, prod_id, process_type_id, quantity, rate, amount, number_of_roll, remark, inserted_by, insert_date";
		//$update_id=1; 
		$add_commaa=0; $data_array1='';
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
			$prodId				= "prodId_".$i;

			if(str_replace("'",'',$$updateDtlsId)!=""){
				$id_arr[]=str_replace("'",'',$$updateDtlsId);
				$data_array_up1[str_replace("'",'',$$updateDtlsId)] =explode("*",("".$hid_order_id."*".$$batchId."*".$$soDtlsId."*".$$batchDtlsId."*".$$prodId."*".$$cboProcessType."*".str_replace(",",'',$$txtWoqnty)."*".str_replace(",",'',$$txtRate)."*".str_replace(",",'',$$txtAmount)."*".$$txtNumberRoll."*".$$txtRemarks."*1*0*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}else{
				if ($add_commaa!=0) $data_array1 .=","; $add_comma=0;
				$data_array1 .="(".$id_dtls.",".$update_id.",".$hid_order_id.",".$$batchId.",".$$soDtlsId.",".$$batchDtlsId.",".$$prodId.",".$$cboProcessType.",".str_replace(",",'',$$txtWoqnty).",".str_replace(",",'',$$txtRate).",".str_replace(",",'',$$txtAmount).",".$$txtNumberRoll.",".$$txtRemarks.",'".$user_id."','".$pc_date_time."')";
				$id_dtls=$id_dtls+1; $add_commaa++;
			}
		}

		$field_array_status = "updated_by*update_date*status_active*is_deleted";
		$data_array_status = $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*0*1";
		$rID = sql_multirow_update("wo_fabric_aop_dtls", $field_array_status, $data_array_status, "mst_id", $update_id, 1);
		
		$rID1=execute_query(bulk_update_sql_statement( "wo_fabric_aop_dtls", "id", $field_array_up1, $data_array_up1, $id_arr ),1);
		$rID3=1;
		if( $data_array1!=''){
			$rID3=sql_insert("wo_fabric_aop_dtls",$field_array1,$data_array1,0);
		}
		
        //check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID==1 && $rID1==1 && $rID3==1){
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


			if($rID==1 && $rID1==1 && $rID3==1)
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
	else if ($operation==2){
	
		$is_received=0;
		//$sql=sql_select("select sys_no from wo_fabric_aop_mst where issue_id=$update_id and status_active=1 and is_deleted=0");
		$received_sql= "select h.sys_no from wo_fabric_aop_dtls g, wo_fabric_aop_mst h where h.id=g.mst_id and h.entry_form=467 and h.issue_id=$update_id and g.status_active=1 and g.is_deleted=0 and h.status_active=1 and h.is_deleted=0 order by h.id desc";
		$received_sql_res=sql_select($received_sql);
		foreach($received_sql_res as $row){
			$is_received .=$row[csf('sys_no')].',';
		}
		$received_nos = implode(",",array_filter(array_unique(explode(",", chop($is_received,",")))));
		
		if($received_nos)
		{
			echo "20**Delete not Possible. Receive Found.\n Receive No. = $received_nos";disconnect($con); die;
		}
		
		$con = connect();
		if($db_type==0){
		mysql_query("BEGIN");
		}

		$field_array_up ="status_active*is_deleted*updated_by*update_date";
		$data_array_up 	="'0'*'1'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		//$rID=sql_update("wo_fabric_aop_mst",$field_array_up,$data_array_up,"id","".$update_id."",0);
		$rID=sql_update("wo_fabric_aop_dtls",$field_array_up,$data_array_up,"mst_id","".$update_id."",0);
		//echo "10**".$rID; die;
		if($db_type==0){
			if($rID){
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$txt_system_id).'**'.str_replace("'","",$update_id);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_system_id).'**'.str_replace("'","",$update_id);
			}
		}
		else if($db_type==2 || $db_type==1 ){
			if($rID){
				oci_commit($con);
				echo "2**".str_replace("'","",$txt_system_id).'**'.str_replace("'","",$update_id);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_system_id).'**'.str_replace("'","",$update_id);
			}
		}
		disconnect($con);
		die;
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
		load_drop_down( 'fabric_issue_for_aop_entry_controller', $("#cbo_company_mst").val(), 'load_drop_down_buyer', 'buyer_td' );

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
		/*$sql= "select c.company_id, h.sys_no,h.order_no,h.id as issue_id,h.supplier_id,h.within_group from fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e, wo_fabric_aop_mst h where  h.entry_form=462 and h.fab_booking_no= c.sales_booking_no and c.id=d.po_id and d.mst_id=e.id and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 $company $buyer $sys_date $search_cond group by  c.company_id , h.sys_no,h.order_no,h.id ,h.supplier_id,h.within_group order by h.id desc";*/

		//Need work for not in condition
		$sql= "select h.lc_company_name as company_id,h.sys_no,h.order_no,h.id as issue_id,h.supplier_id,h.within_group from 
		 fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e, wo_fabric_aop_mst h where c.id=d.po_id and d.mst_id=e.id and  h.entry_form=462 and h.id not in (select mst_id from wo_fabric_aop_dtls where status_active=1 and is_deleted=0) and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 $company $buyer $sys_date $search_cond group by h.lc_company_name,h.sys_no,h.order_no,h.id,h.supplier_id,h.within_group order by h.id desc";

	}else{
		$sql= "select h.lc_company_name as company_id,g.quantity,h.sys_no,h.order_no,h.id as issue_id,h.supplier_id,h.within_group from 
		 fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e, wo_fabric_aop_dtls g, wo_fabric_aop_mst h where h.id=g.mst_id and  c.id=d.po_id and d.mst_id=e.id and g.batch_id=e.id and h.entry_form=462 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and g.status_active=1 and g.is_deleted=0 $company $buyer $sys_date $search_cond group by  h.lc_company_name,h.sys_no,h.order_no,h.id,h.supplier_id,h.within_group,g.quantity order by h.id desc";
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
		if($within_group==1 )
		{
			if($without_order ==1){
				$company="  f.company_id='$data[0]'";
			}else{
				$company="  c.po_company_id='$data[0]'";
			}
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
	 
	
	if ($data[4]!=0) $supplier=" and f.supplier_id='$data[4]'"; else $supplier="";

	if ($data[8]!=0) $within_group_cond=" and c.within_group='$data[8]'"; else $within_group_cond="";

	if($within_group==1  && $without_order ==1)
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
			/*$sql= "select f.id,f.booking_no,f.company_id, c.po_buyer as buyer_id,a.job_no, c.id as sales_order_id,c.style_ref_no,c.job_no as fso_number, c.within_group, c.sales_booking_no from 
			wo_pre_cost_fabric_cost_dtls a, fabric_sales_order_dtls b, fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e, wo_booking_mst f  where $company and a.id=b.pre_cost_fabric_cost_dtls_id and b.mst_id=c.id and c.id=d.po_id and d.mst_id=e.id and f.tagged_booking_no = c.sales_booking_no and e.color_id=b.color_id and f.booking_type=3 and f.process=35 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 $booking_cond $style_cond $buyer $supplier $within_group_cond $booking_date $so_cond group by  f.id,f.booking_no,f.company_id, c.po_buyer,a.job_no,c.style_ref_no,c.job_no, c.id, c.within_group, c.sales_booking_no order by f.id desc";*/
			if ($data[1]!=0) $buyer=" and c.buyer_id='$data[1]'"; else $buyer="";
			$sql= "select c.id as sales_order_id, c.job_no as booking_no,c.po_company_id as company_id, c.po_buyer as buyer_id,c.style_ref_no,c.job_no as fso_number, c.within_group, c.sales_booking_no from 
		 fabric_sales_order_dtls b, fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e where $company and  b.mst_id=c.id and c.id=d.po_id and d.mst_id=e.id and e.color_id=b.color_id  and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 $booking_cond $style_cond $buyer $supplier $within_group_cond $booking_date $so_cond and c.booking_type=1 group by  c.id,c.job_no ,c.po_company_id, c.po_buyer ,c.style_ref_no,c.job_no, c.within_group, c.sales_booking_no order by c.id desc";
		}else{
			if ($data[1]!=0) $buyer=" and c.po_buyer='$data[1]'"; else $buyer="";
			$sql= "select f.id,f.booking_no,f.company_id, c.po_buyer as buyer_id, c.id as sales_order_id,c.style_ref_no,c.job_no as fso_number, c.within_group, c.sales_booking_no from 
			 fabric_sales_order_dtls b, fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e, wo_non_ord_samp_booking_mst f  where $company and  b.mst_id=c.id and c.id=d.po_id and d.mst_id=e.id and f.booking_no = c.sales_booking_no and e.color_id=b.color_id and f.booking_type=4 and f.pay_mode=5 and f.supplier_id=c.company_id  and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 $booking_cond $style_cond $buyer $supplier $within_group_cond $booking_date $so_cond group by  f.id,f.booking_no,f.company_id, c.po_buyer,c.style_ref_no,c.job_no, c.id, c.within_group, c.sales_booking_no order by f.id desc";
		}
	}else{
		if ($data[1]!=0) $buyer=" and c.buyer_id='$data[1]'"; else $buyer="";
		if($without_order !=1){
			$sql= "select c.id as sales_order_id, c.job_no as booking_no,c.company_id, c.buyer_id as buyer_id,c.style_ref_no,c.job_no as fso_number, c.within_group, c.sales_booking_no from 
		 fabric_sales_order_dtls b, fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e where $company and  b.mst_id=c.id and c.id=d.po_id and d.mst_id=e.id and e.color_id=b.color_id  and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 $booking_cond $style_cond $buyer $supplier $within_group_cond $booking_date $so_cond group by  c.id,c.job_no ,c.company_id, c.buyer_id,c.style_ref_no,c.job_no, c.within_group, c.sales_booking_no order by c.id desc";
		}else{
			$sql= "";
		}
	}
	
	//echo $sql; die;
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

if ($action=="populate_all_data_from_search_popup")
{
	$data=explode('_',$data);
	//UG-FSOE-21-00021__1_1868_0
	$sql= "select c.id, c.job_no as booking_no,c.company_id, c.buyer_id,c.style_ref_no,c.job_no as fso_number, c.within_group, c.sales_booking_no,po_company_id,po_buyer from fabric_sales_order_mst c where id=$data[3]"; 
	//echo $data[3].'==';
	$data_array=sql_select($sql);
	
	foreach ($data_array as $row){
		if($data[4]==1){
			$lc_company=$row[csf("po_company_id")];
			$po_buyer=$row[csf("po_buyer")];
		} else{
			if($data[2]==1){
				//echo 'fdsgf';
				$lc_company=$row[csf("po_company_id")];
				$po_buyer=$row[csf("po_buyer")];
			}else{
				$lc_company=$row[csf("company_id")];
				$po_buyer=$row[csf("buyer_id")];
			}
			
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
	
	$sql= "select a.id, a.entry_form, a.sys_no_prefix, a.sys_no_prefix_num, a.sys_no, a.sys_date, a.delivery_date, a.company_id, a.buyer_id, a.issue_purpose, a.source, a.order_id, a.order_no, a.attention, a.vehical_no, a.driver_name, a.dl_no, a.transport, a.mobile_no, a.gate_pass_no, a.is_short, a.remark,a.supplier_id,a.supplier_address,within_group,lc_company_name,fab_booking_no,order_type,currency_id from wo_fabric_aop_mst a where a.id=$data and a.is_deleted=0 ";

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
		echo "document.getElementById('cbo_currency_id').value 		= '".$row[csf("currency_id")]."';\n";
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
			
			var tbl_row_count = document.getElementById( 'tbl_po_list' ).rows.length;
			//alert(tbl_row_count);
			//tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var attrData= $('#hidden_data_'+i).val();
				//var attrData=$('#tr_' +i).attr('onclick');
				//var splitArr = attrData.split('"');
				js_set_value( attrData );
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
			$sql_cond .= " and h.sys_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
		}
		else
		{
			$sql_cond .= " and h.sys_date between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
		}
	}

	if(trim($company)!="") $sql_cond .= " and h.company_id='$company'";

	if($db_type==0) $year_field=", YEAR(h.insert_date) as year";
	else if($db_type==2) $year_field=", to_char(h.insert_date,'YYYY') as year";
	else $year_field="";//defined Later

	if(str_replace("'","",$return_to==0)){echo "<p style='font-size:25px; color:#F00'>Please Select Service Company.</p>";die;}
	else{$supplier_con=" and h.supplier_id=$return_to";}

	$sql= "select c.company_id, g.quantity , h.sys_no,h.order_no,h.id as issue_id,h.supplier_id,h.within_group,h.sys_date $year_field from 
	 fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e, wo_fabric_aop_dtls g, wo_fabric_aop_mst h where h.id=g.mst_id and c.id=d.po_id and d.mst_id=e.id and g.batch_id=e.id and h.entry_form=462 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and g.status_active=1 and g.is_deleted=0 $sql_cond $supplier_con group by  c.company_id, h.sys_no,h.order_no,h.id,h.supplier_id,h.within_group, g.quantity,h.sys_date, h.insert_date   order by h.id desc";


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
							<td style="word-break:break-all" align="right"><?php echo number_format($row['quantity'],4); ?>
							<input class="text_boxes" type="hidden"  name="hidden_data_<? echo $i; ?>" id="hidden_data_<? echo $i; ?>" value="<? echo $i.'_'. $sys_no.'_'.$issue_id.'_'.$row['within_group']; ?>" style="width:70px">
							</td>
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
	
	/*$sql= "select c.id,c.company_id, c.po_buyer as buyer_id,c.style_ref_no,c.job_no as fso_number, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, b.color_id, b.body_part_id , b.fabric_desc, b.gsm_weight, b.dia ,b.id as so_dtls_id,e.id as batch_id,g.id as dtls_id, g.process_type_id, g.quantity, g.rate, g.amount, g.number_of_roll, sum(d.batch_qnty) as batch_weight, g.remark  from 
		 fabric_sales_order_dtls b, fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e, wo_fabric_aop_dtls g ,product_details_master  h where b.mst_id=c.id and c.id=d.po_id and d.mst_id=e.id and g.sales_order_dtls_id=b.id and g.batch_id=e.id and e.color_id=b.color_id and d.prod_id=h.id and g.mst_id in ($data[1]) and b.body_part_id= d.body_part_id and h.item_description=trim(b.fabric_desc) and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and g.status_active=1 and g.is_deleted=0 group by  c.id,c.company_id, c.po_buyer,c.style_ref_no,c.job_no, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, b.color_id, b.body_part_id , b.fabric_desc, b.gsm_weight, b.dia ,b.id,e.id,g.id, g.process_type_id, g.quantity, g.rate, g.amount, g.number_of_roll, g.remark order by b.id desc";*/

	$batch_dtls_id_sql= "select g.batch_dtls_id from 
	   wo_fabric_aop_dtls g where g.mst_id in ($data[1]) and g.is_deleted=0 and g.status_active=1 ";
	$batch_dtls_sqlArray=sql_select($batch_dtls_id_sql); $prev_issue_qty_arr=array();
	foreach ($batch_dtls_sqlArray as  $row) 
	{
		$batch_dtls_id .=$row[csf("batch_dtls_id")].',';
	}
	$batch_dtls_id=chop($batch_dtls_id,',');
	$batch_dtls_id=implode(",",array_unique(explode(",",$batch_dtls_id)));
	if($batch_dtls_id!=''){
		$batch_dtls_id_cond =" and d.id in ($batch_dtls_id)";
	}

	$sql= "SELECT c.id, c.company_id, c.po_buyer AS buyer_id, c.style_ref_no, c.job_no AS fso_number, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, e.color_id, h.item_description AS fabric_desc, h.gsm AS gsm_weight, h.dia_width AS dia, e.id AS batch_id, g.id AS dtls_id, g.process_type_id, g.quantity, g.rate, g.amount, g.number_of_roll, g.remark,g.batch_dtls_id
		FROM  fabric_sales_order_mst c, pro_batch_create_dtls d, pro_batch_create_mst e, wo_fabric_aop_dtls g, product_details_master h
		WHERE   c.id = d.po_id AND d.mst_id = e.id AND g.batch_id = e.id AND d.prod_id = h.id and d.mst_id=e.id and g.batch_id=e.id and d.prod_id=h.id and d.prod_id = g.prod_id and g.mst_id in ($data[1]) $batch_dtls_id_cond and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and g.status_active=1 and g.is_deleted=0 group by  c.id, c.company_id, c.po_buyer, c.style_ref_no, c.job_no, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, e.color_id, h.item_description, h.gsm, h.dia_width, e.id, g.id, g.process_type_id, g.quantity, g.rate, g.amount, g.number_of_roll, g.remark,g.batch_dtls_id order by e.id desc";
		 //and h.item_description=b.fabric_desc

	$qry_result=sql_select($sql);
	$batch_dtls_sql= "select  d.id,d.body_part_id,d.batch_qnty from 
		   pro_batch_create_dtls d where d.is_deleted=0 and d.status_active=1 $batch_dtls_id_cond";
	$batch_dtlsArray=sql_select($batch_dtls_sql);
	foreach ($batch_dtlsArray as $row) 
	{
		$batch_dtls_arr[$row[csf("id")]]["body_part_id"] =$row[csf("body_part_id")];
		$batch_dtls_arr[$row[csf("id")]]["batch_qnty"] =$row[csf("batch_qnty")];
	}

	$sql_mst="select id, entry_form, sys_no_prefix, sys_no_prefix_num, sys_no, sys_date, delivery_date, company_id, buyer_id,supplier_id,supplier_address, issue_purpose, source, order_id, order_no, attention, currency_id, vehical_no, driver_name, dl_no, transport, mobile_no, gate_pass_no, is_short, remark from wo_fabric_aop_mst where id in ($data[1]) and entry_form=462 and status_active=1";

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
				<td width="100">Currency </td> <td width="195">: <? echo $currency[$dataArray[0][csf('currency_id')]]; ?></td>
				<td width="100">Delivery To </td> <td width="195">: <? echo $supplier_arr[$dataArray[0][csf('supplier_id')]]; ?></td>
				<td width="100">Delivery Address </td> <td width="195">: <? echo $dataArray[0][csf('supplier_address')]; ?></td>
				<td width="100">Driver Name </td> <td width="195">: <? echo $dataArray[0][csf('driver_name')]; ?></td>
			</tr>
			<tr>
				<td width="100">Mobile No </td> <td width="195">: <? echo $dataArray[0][csf('mobile_no')]; ?></td>
				<td width="100">DL No </td> <td width="195">: <? echo $dataArray[0][csf('dl_no')]; ?></td>
				<td width="100">Transport </td> <td width="195">: <? echo $dataArray[0][csf('transport')]; ?></td>
				<td width="100">Vahical No </td> <td width="195">: <? echo $dataArray[0][csf('vehical_no')]; ?></td>
			</tr>
			<tr>
				<td width="100">Gate Pass No </td> <td width="195">: <? echo $dataArray[0][csf('gate_pass_no')]; ?></td>
				<td width="100">Remarks </td> <td colspan="5">: <? echo $dataArray[0][csf('remark')]; ?></td>
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

					$batch_dtls_id=array_unique(explode(",",chop($row[csf("batch_dtls_id")],',')));
					foreach ($batch_dtls_id as $value) {
						$bodyPartID=$batch_dtls_arr[$value]["body_part_id"];
						$batchQty +=$batch_dtls_arr[$value]["batch_qnty"];
					}
					?> 
					<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
						<td width="30"><?  echo $tblRow; ?></td>
		                <td width="110"  style="word-break:break-all"><?  echo $buyer_arr[$dataArray[0][csf('buyer_id')]]; ?></td>
		                <td width="110"><?  echo $row[csf("style_ref_no")]; ?></td>
		                <td width="110"><?  echo $row[csf("sales_booking_no")]; ?></td>
		                <td width="110"><?  echo $row[csf("fso_number")]; ?></td>
		                <td width="110"><?  echo $row[csf("batch_no")]; ?></td>
		                <td width="80"><?  echo $color_arr[$row[csf("color_id")]]; ?></td>
		                <td width="80"><?  echo $body_part[$bodyPartID]; ?></td>
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

	 /*$sql= "select c.id,c.company_id, c.po_buyer as buyer_id,c.style_ref_no,c.job_no as fso_number, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, b.color_id, b.body_part_id , b.fabric_desc, b.gsm_weight, b.dia ,b.id as so_dtls_id,e.id as batch_id,g.id as dtls_id, g.process_type_id, g.quantity, g.rate, g.amount, g.number_of_roll, sum(d.batch_qnty) as batch_weight, g.remark, g.mst_id  from 
		 fabric_sales_order_dtls b, fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e, wo_fabric_aop_dtls g ,product_details_master  h where b.mst_id=c.id and c.id=d.po_id and d.mst_id=e.id and g.sales_order_dtls_id=b.id and g.batch_id=e.id and e.color_id=b.color_id and d.prod_id=h.id  and g.mst_id in ($data[1]) and b.body_part_id= d.body_part_id and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and g.status_active=1 and g.is_deleted=0 group by  c.id,c.company_id, c.po_buyer,c.style_ref_no,c.job_no, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, b.color_id, b.body_part_id , b.fabric_desc, b.gsm_weight, b.dia ,b.id,e.id,g.id, g.process_type_id, g.quantity, g.rate, g.amount, g.number_of_roll, g.remark, g.mst_id order by b.id desc";*/

	$batch_dtls_id_sql= "select g.batch_dtls_id from 
	   wo_fabric_aop_dtls g where g.mst_id in ($data[1]) and g.is_deleted=0 and g.status_active=1 and g.batch_dtls_id is not null";
	$batch_dtls_sqlArray=sql_select($batch_dtls_id_sql); $prev_issue_qty_arr=array();
	foreach ($batch_dtls_sqlArray as  $row) 
	{
		$batch_dtls_id .=$row[csf("batch_dtls_id")].',';
	}
	$batch_dtls_id=chop($batch_dtls_id,',');
	$batch_dtls_id=implode(",",array_unique(explode(",",$batch_dtls_id)));
	if($batch_dtls_id!=''){
		$batch_dtls_id_cond =" and d.id in ($batch_dtls_id)";
	}

	$sql= "SELECT c.id, c.company_id, c.po_buyer AS buyer_id, c.style_ref_no, c.job_no AS fso_number, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, e.color_id, h.item_description AS fabric_desc, h.gsm AS gsm_weight, h.dia_width AS dia, e.id AS batch_id,g.mst_id, g.id AS dtls_id, g.process_type_id, g.quantity, g.rate, g.amount, g.number_of_roll, g.remark, g.batch_dtls_id
		FROM  fabric_sales_order_mst c, pro_batch_create_dtls d, pro_batch_create_mst e, wo_fabric_aop_dtls g, product_details_master h
		WHERE   c.id = d.po_id AND d.mst_id = e.id AND g.batch_id = e.id AND d.prod_id = h.id and d.mst_id=e.id and g.batch_id=e.id and d.prod_id=h.id  and d.prod_id = g.prod_id and g.mst_id in ($data[1]) $batch_dtls_id_cond and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and g.status_active=1 and g.is_deleted=0 group by  c.id, c.company_id, c.po_buyer, c.style_ref_no, c.job_no, c.within_group, c.sales_booking_no, e.batch_no, e.extention_no, e.color_id, h.item_description, h.gsm, h.dia_width, e.id, g.mst_id, g.id, g.process_type_id, g.quantity, g.rate, g.amount, g.number_of_roll, g.remark, g.batch_dtls_id order by g.id desc";
		 //and h.item_description=b.fabric_desc

	$qry_result=sql_select($sql);
	$batch_dtls_sql= "select  d.id,d.body_part_id,d.batch_qnty from 
		   pro_batch_create_dtls d where d.is_deleted=0 and d.status_active=1 $batch_dtls_id_cond";
	$batch_dtlsArray=sql_select($batch_dtls_sql);
	foreach ($batch_dtlsArray as $row) 
	{
		$batch_dtls_arr[$row[csf("id")]]["body_part_id"] =$row[csf("body_part_id")];
		//$batch_dtls_arr[$row[csf("id")]]["batch_qnty"] =$row[csf("batch_qnty")];
	}
	$sql_mst="select id, entry_form, sys_no_prefix, sys_no_prefix_num, sys_no, sys_date, delivery_date, company_id, buyer_id,supplier_id,supplier_address, issue_purpose, source, order_id, order_no, attention, currency_id, vehical_no, driver_name, dl_no, transport, mobile_no, gate_pass_no, is_short, remark from wo_fabric_aop_mst where id in ($data[1]) and entry_form=462 and status_active=1";

	$dataArray=sql_select($sql_mst);
	foreach ($dataArray as $row)
	{
		$mst_arr[$row[csf("id")]]['sys_date']=change_date_format($row[csf("sys_date")]);
		$mst_arr[$row[csf("id")]]['sys_no']=$row[csf("sys_no")];
		$mst_arr[$row[csf("id")]]['buyer_id']=$row[csf("buyer_id")];
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
				<td width="100">Currency </td> <td width="195">: <? echo $currency[$dataArray[0][csf('currency_id')]]; ?></td>
				<td width="100">Driver Name </td> <td width="195">: <? echo $dataArray[0][csf('driver_name')]; ?></td>
				<td width="100">Mobile No </td> <td width="195">: <? echo $dataArray[0][csf('mobile_no')]; ?></td>
				<td width="100">DL No </td> <td width="195">: <? echo $dataArray[0][csf('dl_no')]; ?></td>
			</tr>
			<tr>
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
					$buyer_id=return_field_value("buyer_id"," wo_fabric_aop_mst","id =$mst_id and entry_form=462 and is_deleted=0 and status_active=1");
					$batch_dtls_id=array_unique(explode(",",chop($row[csf("batch_dtls_id")],',')));
					foreach ($batch_dtls_id as $value) {
						$bodyPartID=$batch_dtls_arr[$value]["body_part_id"];
						//$batchQty +=$batch_dtls_arr[$value]["batch_qnty"];
					}
					?> 
					<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
						<td width="30"><?  echo $tblRow; ?></td>
		                <td width="60" ><?  echo $mst_arr[$row[csf("mst_id")]]['sys_date']; ?></td>
		                <td width="140"  style="word-break:break-all"><?  echo $mst_arr[$row[csf("mst_id")]]['sys_no']; ?></td>
		                <td width="110"  style="word-break:break-all"><?  echo $buyer_arr[$mst_arr[$row[csf("mst_id")]]['buyer_id']]; ?></td>
		                <td width="110"><?  echo $row[csf("style_ref_no")]; ?></td>
		                <td width="110"><?  echo $row[csf("sales_booking_no")]; ?></td>
		                <td width="110"><?  echo $row[csf("fso_number")]; ?></td>
		                <td width="110"><?  echo $row[csf("batch_no")]; ?></td>
		                <td width="80"><?  echo $color_arr[$row[csf("color_id")]]; ?></td>
		                <td width="80"><?  echo $body_part[$bodyPartID]; ?></td>
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