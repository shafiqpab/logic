<?
/*-------------------------------------------- Comments 
Version          : V1
Purpose			 : This form will create Service Booking
Functionality	 :	
JS Functions	 :
Created by		 : Aziz 
Creation date 	 : 03-09-2018
Requirment Client: 
Requirment By    : 
Requirment type  : 
Requirment       : 
Affected page    : 
Affected Code    :              
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
require_once('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.conversions.php');
require_once('../../../includes/class4/class.yarns.php');
require_once('../../../includes/class4/class.others.php');
	
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$permission=$_SESSION['page_permission'];

//---------------------------------------------------- Start---------------------------------------------------------------------------
$po_number=return_library_array( "select id,po_number from wo_po_break_down", "id", "po_number"  );
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
$trim_group= return_library_array( "select id, item_name from lib_item_group",'id','item_name');

if($action=="print_report_button")
{ 
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=2 and report_id=65 and is_deleted=0 and status_active=1");
	echo "print_report_button_setting('$print_report_format');\n";
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
	$currency_rate=set_conversion_rate( $data[0], $conversion_date, $data[2] );
	echo "1"."_".$currency_rate;
	exit();	
}
if ($action=="load_drop_down_supplier")
{ 
	//echo "dsdsd";
	$data=explode("_",$data);
	$pay_mode_id=$data[0];
	$tag_buyer_id=$data[1];
	$tag_comp_id=$data[2];
	
	if($pay_mode_id==5 || $pay_mode_id==3){
	   echo create_drop_down( "cbo_supplier_name", 130, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "-- Select Company --", "", "get_php_form_data( this.value, 'load_drop_down_attention', 'requires/service_booking_multi_job_wise_knitting_controller');",0,"" );
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
			$tag_buy_supp="select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and b.party_type in (20) and a.tag_company='$tag_comp_id' and c.status_active=1 and c.is_deleted=0 $supplier_string group by c.id, c.supplier_name order by c.supplier_name";
			//and b.party_type in (4,5,20)
			//echo $tag_buy_supp; die;
			//$tag_buy_supp="select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c,lib_supplier_tag_buyer d where c.id=b.supplier_id and a.supplier_id = b.supplier_id and d.supplier_id=c.id and d.supplier_id=a.supplier_id  and d.supplier_id=b.supplier_id and b.party_type  in (4,5) and a.tag_company='$tag_comp_id' and c.status_active=1 and c.is_deleted=0  and d.tag_buyer=$tag_buyer group by c.id, c.supplier_name order by c.supplier_name";
		}
		else
		{
			$tag_buy_supp="select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and b.party_type in (20) and a.tag_company='$tag_comp_id' and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name";
			//and b.party_type in (4,5,20)
		}
		//echo $tag_buy_supp;
		$cbo_supplier_name= create_drop_down( "cbo_supplier_name", 130, $tag_buy_supp,"id,supplier_name", 1, "--Select Supplier--",$selected,"get_php_form_data( this.value, 'load_drop_down_attention', 'requires/service_booking_multi_job_wise_knitting_controller');","");
	}
	echo $cbo_supplier_name;
	exit();
	
	/*
	
	
	
	if($data==5 || $data==3){
	   echo create_drop_down( "cbo_supplier_name", 172, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "-- Select Company --", "", "get_php_form_data( this.value+'_'+document.getElementById('cbo_pay_mode').value, 'load_drop_down_attention', 'requires/service_booking_multi_job_wise_knitting_controller');",0,"" );
	}
	else{
	   echo create_drop_down( "cbo_supplier_name", 172, "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id  and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name","id,supplier_name", 1, "--Select Supplier--",$selected,"get_php_form_data( this.value+'_'+document.getElementById('cbo_pay_mode').value, 'load_drop_down_attention', 'requires/service_booking_multi_job_wise_knitting_controller');","");

	}
	
	exit();*/
}

if($action=="load_drop_down_attention")
{
	$data=explode("_",$data);
	if($data[1]==5 || $data[1]==3 )
	{
		$supplier_name=return_field_value("contract_person","lib_company","id ='".$data[0]."' and is_deleted=0 and status_active=1");
	}
	else
	{
		$supplier_name=return_field_value("contact_person","lib_supplier","id ='".$data[0]."' and is_deleted=0 and status_active=1");
	}
	echo "document.getElementById('txt_attention').value = '".$supplier_name."';\n";
	exit();	
}

if ($action=="order_search_popup")
{
  	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$cbo_short_type=str_replace("'","",$cbo_short_type);
?>
	<script>
	var selected_id = new Array(); 
	var selected_name = new Array(); 
	var conv_ids_fab = new Array();	
	var stringuniq=new Array();
	 function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count;
			// alert(tbl_row_count)
			for( var i = 1; i <= tbl_row_count; i++ ) {
					  js_set_value( i );
				
			}
		}
		
		function toggle2( x, origColor ) 
		{
			//alert(x)
			var newColor = 'yellow';
			//if ( x.style ) 
			//{
			document.getElementById(x).style.backgroundColor = ( newColor == document.getElementById(x).style.backgroundColor )? origColor : newColor;
			//}
		}
		
		function toggle( x, origColor ) {
			//alert(x+'_'+origColor)
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value(tr_id ) {
			//toggle( tr_id, '#FFFFCC');
			var str_data=$('#hiddstrdata_'+tr_id).val();
			//alert(str_data+","+tr_id)
			var str_all=str_data.split("_");
			var str=str_all[0]; //po id
			var str_po=str_all[1]; //po no
			var str_job=str_all[2];//job no
			var conv_id=str_all[3]; //conv id
			//alert(str_all[3]);
			if ( document.getElementById('job_no').value!="" && document.getElementById('job_no').value!=str_job )
			{
				alert('No Job Mix Allowed')
				return;	
			}
			//toggle( tr_id, '#FFFFCC');
			toggle( document.getElementById( 'search'+tr_id ), '#FFFFCC' );
			document.getElementById('job_no').value=str_job;
			
			if( jQuery.inArray( str_data , stringuniq ) == -1 ) {
				stringuniq.push( str_data );
				selected_id.push( str );
				selected_name.push( str_po );
				conv_ids_fab.push( conv_id );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str ) break;
				}
				stringuniq.splice( i, 1 );
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				conv_ids_fab.splice( i, 1 );
				if(selected_id.length==0){
					document.getElementById('job_no').value="";
			 	}
			}
			var id = '' ; var name = ''; var conv_ids= '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				conv_ids += conv_ids_fab[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			conv_ids = conv_ids.substr( 0, conv_ids.length - 1 );
			//alert(conv_ids);
			$('#po_number_id').val( id );
			$('#po_number').val( name );
			$('#conv_fab_mst_id').val( conv_ids );
		}   
    </script>
    
</head>

<body>
<div align="center" style="width:100%;" >
<?
$booking_month=0;
 if(str_replace("'","",$cbo_booking_month)<10)
 {
	 $booking_month.=str_replace("'","",$cbo_booking_month);
 }
 else
 {
	$booking_month=str_replace("'","",$cbo_booking_month); 
 }
$start_date="01"."-".$booking_month."-".str_replace("'","",$cbo_booking_year);
$end_date=cal_days_in_month(CAL_GREGORIAN, $booking_month, str_replace("'","",$cbo_booking_year))."-".$booking_month."-".str_replace("'","",$cbo_booking_year);

if($cbo_short_type==10) $th_show_hide="";
	else $th_show_hide="display:none";

?>
	<form name="searchpofrm_1" id="searchpofrm_1">
    <table  width="980" class="rpt_table" align="center" rules="all">
        <thead>                	 
            <th width="150">Company Name</th>
            <th width="140">Buyer Name</th>
            <th width="80">Job No</th>
            <th width="100">Style Ref.</th>
            <th width="100">Order No</th> 
			<th width="80" style="<?=$th_show_hide;?>">Short Booking No</th>
            <th width="80">Internal Ref.</th>
            <th width="130" colspan="2">Date Range</th>
            <th><input type="checkbox" value="0" onClick="set_checkvalue();" id="chk_job_wo_po">Job Without PO</th>           
        </thead>
        <tr>
            <td><?=create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", str_replace("'","",$cbo_company_name), "load_drop_down( 'service_booking_multi_job_wise_knitting_controller', this.value, 'load_drop_down_buyer', 'buyer_td');",1); ?></td>
            <td id="buyer_td">
            <?
            if(str_replace("'","",$cbo_company_name)!=0)
            {
            	echo create_drop_down( "cbo_buyer_name", 140,"select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='".str_replace("'","",$cbo_company_name)."' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", str_replace("'","",$cbo_buyer_name), "",1 ); 
            }
            else
            {
            	echo create_drop_down( "cbo_buyer_name", 140, $blank_array, 1, "-- Select Buyer --", str_replace("'","",$cbo_buyer_name), "" );
            }
            ?>	
            </td>
            <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:70px"></td>
            <td><input name="txt_styleref" id="txt_styleref" class="text_boxes" style="width:90px"></td>
            <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:90px"></td> 
			 <td style="<?=$th_show_hide;?>"><input name="txt_booking_search"  id="txt_booking_search" class="text_boxes" style="width:80px"></td>
            <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px"></td>
            <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px"/></td>
            <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" /></td> 
            <td align="center">
            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_styleref').value+'_'+document.getElementById('txt_booking_search').value+'_'+'<?=$cbo_short_type;?>', 'create_po_search_list_view', 'search_div', 'service_booking_multi_job_wise_knitting_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100%;" /></td>
    </tr>
    <tr>
    	<td align="center" valign="middle" colspan="9"><?=load_month_buttons(1); ?></td>
    </tr>   
    <tr>
        <td colspan="9" align="center"><strong>Selected PO Number:</strong> &nbsp;
        	<input type="text" class="text_boxes"  readonly style="width:550px" id="po_number">
            <input type="hidden" id="po_number_id">
            <input type="hidden" id="conv_fab_mst_id">
            <input type="hidden" id="job_no">
        </td>
    </tr>
    <tr>
        <td align="center" colspan="9" >
			<div style="width:50%; float:left" align="right">
					<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data();" /> Check / Uncheck All
			</div>
			<div style="width:50%; float:left" align="left">
					<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" /> <!---->
			</div>	
        </td>
    </tr>
 </table>
                        
       <div id="search_div" align="center"></div>
	</form>
   </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
  exit();
}

if($action=="create_po_search_list_view")
{	
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }	
	if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	if($db_type==0) $insert_year="SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year";
	if($db_type==2) $insert_year="to_char(a.insert_date,'YYYY') as year";
	$short_booking=$data[10];
	$short_booking_type=$data[11];

	if($short_booking!="") $short_booking_cond=" and c.booking_no like '%$short_booking%' ";
	else  $short_booking_cond="";
	

	//display:none
	if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num='$data[5]' "; else  $job_cond=""; 
	if (str_replace("'","",$data[6])!="") $order_cond=" and b.po_number like '%$data[6]%'  "; else  $order_cond=""; 
	
	if (str_replace("'","",$data[9])!="") $styleRef_cond=" and a.style_ref_no like '%$data[9]%' "; else $styleRef_cond=""; 
	$internal_ref = str_replace("'","",$data[7]);
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' ";
	
	if($db_type==0)
	{
	if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[8]";
	}
	if($db_type==2)
	{
	if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	$year_cond=" and to_char(a.insert_date,'YYYY')=$data[8]";
	}
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$arr=array (2=>$comp,3=>$buyer_arr,6=>$body_part,7=>$color_library);
	
	$approval_allow = sql_select("select b.id, b.page_id, b.approval_need, b.allow_partial, b.validate_page,a.setup_date from approval_setup_mst a,approval_setup_dtls b 
	where a.id=b.mst_id and a.company_id='$data[0]' and a.status_active=1 and b.page_id=25 and b.status_active=1 and b.is_deleted=0 order by b.id desc ");

	if ($approval_allow[0][csf("approval_need")] == 1 && $approval_allow[0][csf("allow_partial")] == 1)
		$approval_cond = "and c.approved in (1,3)";
	else if ($approval_allow[0][csf("approval_need")] == 1 && $approval_allow[0][csf("allow_partial")] == 2)
		$approval_cond = "and c.approved in (1)";
	else if ($approval_allow[0][csf("approval_need")] == 1 && $approval_allow[0][csf("allow_partial")] == 0)
		$approval_cond = "and c.approved in (1,3)";
	else $approval_cond = "";
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1100" class="rpt_table"  >
        <thead>
            <th width="25">SL</th>
            <th width="100">Buyer</th>
            <th width="50">Year</th>
            <th width="50">Job No</th>
            <th width="110">Style No</th>
            <th width="110">PO No</th>
            <th width="150">Fabric Desc.</th>
            <th width="120">Body Part</th>
            <th width="70">Job Qty.</th>
            <th width="70">PO Qty</th>
            <th width="70">Shipment Date</th>
            <th width="70">Internal Ref.</th>
            <th>File No</th>
        </thead>
	</table>
	<div style="width:1100px; overflow-y:scroll; max-height:340px;" id="buyer_list_view" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1080" class="rpt_table" id="tbl_list_search" >
    <?
	if($short_booking_type==0)
	{
		if ($data[2]==0)
		{
			$sql= "select a.job_no_prefix_num, $insert_year, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id, b.po_number, b.po_quantity, 
			b.shipment_date, (d.fabric_description) as fabric_description, d.body_part_id, b.grouping, b.file_no, f.id as conv_id 
			from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c, wo_pre_cost_fabric_cost_dtls d, wo_pre_cos_fab_co_avg_con_dtls e,
			 wo_pre_cost_fab_conv_cost_dtls f where a.job_no=b.job_no_mst and a.job_no=c.job_no and c.job_no=d.job_no and a.job_no=d.job_no and b.job_no_mst=d.job_no 
			 and b.id=e.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and f.fabric_description=d.id and a.job_no=f.job_no and a.job_no=e.job_no and f.cons_process=1 
			  and f.fabric_description=e.pre_cost_fabric_cost_dtls_id and a.status_active=1 $approval_cond $year_cond $styleRef_cond and b.status_active=1 and c.status_active = 1 
			  and d.status_active = 1 and e.status_active = 1 and f.status_active = 1 and b.shiping_status not in(3) $shipment_date $company $buyer $job_cond $internal_ref_cond 
			  group by a.job_no_prefix_num, a.insert_date, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, 
			  b.id, b.po_number, b.po_quantity, b.shipment_date, d.fabric_description, b.grouping,  b.file_no, d.body_part_id,  f.id order by a.job_no";  
			//echo $sql; 
			//echo  create_list_view("list_view", "Job No,Year,Company,Buyer,Style Ref. No,Fabric Desc.,Body Part,Job Qty.,PO number,PO Qty,Shipment Date,Internal Ref.", "90,60,60,100,120,250,100,80,120,70,80,80","1250","320",0, $sql , "js_set_value", "id,po_number,job_no,conv_id", "this.id", 1, "0,0,company_name,buyer_name,0,0,body_part_id,0,0,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,fabric_description,body_part_id,job_quantity,po_number,po_quantity,shipment_date,grouping", '','','0,0,0,0,0,0,0,1,0,1,3','','');
		}
		else
		{
			$sql= "select a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,b.grouping from wo_po_details_master a, wo_po_break_down b where
			 a.job_no=b.job_no_mst and  a.status_active=1  and a.is_deleted=0 and  b.status_active=1  and b.is_deleted=0 $shipment_date $company $buyer $job_cond $internal_ref_cond order by a.job_no";
			
			//echo  create_list_view("list_view", "Job No,Company,Buyer,Style Ref. No,Internal Ref.", "90,60,50,100,90,90","810","320",0, $sql , "js_set_value", "id", "", 1, "0,company_name,buyer_name,0,0,0,0", $arr , "job_no_prefix_num,company_name,buyer_name,style_ref_no,grouping", '','','0,0,0,0,1,0,2,3','','') ;
		}
	}
	else{
  	$sql= "select a.job_no_prefix_num,$insert_year, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.id, b.po_number, b.po_quantity, 
			b.shipment_date,b.grouping,c.pre_cost_fabric_cost_dtls_id as conv_id,d.body_part_id,(d.fabric_description) as fabric_description 
		from wo_po_details_master a, wo_po_break_down b,wo_booking_dtls c,wo_pre_cost_fabric_cost_dtls d where a.job_no=b.job_no_mst  and c.job_no=d.job_no and a.job_no=d.job_no and c.po_break_down_id=b.id and c.is_short=1 and c.booking_type=1 
		and  d.id=c.pre_cost_fabric_cost_dtls_id and a.status_active=1  and a.is_deleted=0 and  c.status_active=1  and c.is_deleted=0 and  b.status_active=1 
		 and b.is_deleted=0 $company $buyer $internal_ref_cond $year_cond $styleRef_cond $short_booking_cond  group by a.job_no_prefix_num, a.insert_date, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, 
			  b.id, b.po_number, b.po_quantity, b.shipment_date, c.pre_cost_fabric_cost_dtls_id, b.grouping, d.fabric_description, b.file_no, d.body_part_id  order by a.job_no";
	}
	
	$nameArray=sql_select( $sql );  $i=1;
	foreach ($nameArray as $row)
	{
		if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		?>
        <tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i; ?>" onClick="js_set_value(<?=$i; ?>);">
            <td width="25"><?=$i; ?>
                <input type="hidden" id="hiddstrdata_<?=$i; ?>" style="width:60px" value="<?=$row[csf('id')].'_'.$row[csf('po_number')].'_'.$row[csf('job_no')].'_'.$row[csf('conv_id')]; ?>" >
            </td>
            <td width="100" style="word-break:break-all"><?=$buyer_arr[$row[csf('buyer_name')]];?></td>
            <td width="50" style="word-break:break-all"><?=$row[csf('year')]; ?></td>
            <td width="50" style="word-break:break-all"><?=$row[csf('job_no_prefix_num')]; ?></td>
            <td width="110" style="word-break:break-all"><?=$row[csf('style_ref_no')]; ?></td>
            <td width="110" style="word-break:break-all"><?=$row[csf('po_number')]; ?></td>
            <td width="150" style="word-break:break-all"><?=$row[csf('fabric_description')]; ?></td>
            <td width="120" style="word-break:break-all"><?=$body_part[$row[csf('body_part_id')]]; ?></td>
            <td width="70" style="word-break:break-all" align="right"><?=$row[csf('job_quantity')]; ?></td>
            <td width="70" style="word-break:break-all" align="right"><?=$row[csf('po_quantity')]; ?></td>
            <td width="70" style="word-break:break-all"><?=change_date_format($row[csf('shipment_date')]); ?></td>
            <td width="70" style="word-break:break-all"><?=$row[csf('grouping')]; ?></td>
            <td style="word-break:break-all"><?=$row[csf('file_no')]; ?></td>
        </tr>
        <?
		$i++;
	}
	unset($nameArray);
	
	exit();	
} 

if ($action=="populate_order_data_from_search_popup")
{	
	$dataArr=explode("_",$data);
	$poid=$dataArr[0];
	$bookingType=$dataArr[1];
	$data_array=sql_select("select a.job_no,a.company_name,a.buyer_name from wo_po_details_master a, wo_po_break_down b where b.id in (".$poid.") and a.job_no=b.job_no_mst");
	//echo "select a.job_no,a.company_name,a.buyer_name from wo_po_details_master a, wo_po_break_down b where b.id in (".$data.") and a.job_no=b.job_no_mst";
	foreach ($data_array as $row)
	{
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_name")]."';\n";  
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_name")]."';\n";  
	
		$job_no=$row[csf("job_no")].'__'.$bookingType;
		echo "load_drop_down( 'requires/service_booking_multi_job_wise_knitting_controller', '".$job_no."', 'load_drop_down_fabric_description', 'fabric_description_td' )\n";
		$rate_from_library=0;
		$rate_from_library=return_field_value("is_serveice_rate_lib", "variable_settings_production", "service_process_id=2 and company_name=".$row[csf("company_name")]." and status_active=1 and is_deleted=0 ");
		echo "document.getElementById('service_rate_from').value = '".$rate_from_library."';\n";
		
		//echo "get_php_form_data( ".$row[csf("company_name")].", 'print_report_button', 'requires/service_booking_multi_job_wise_knitting_controller');\n";
		//echo "$('#cbo_company_name').attr('disabled',true);\n";
		
		//echo "load_drop_down( 'requires/service_booking_multi_job_wise_knitting_controller', '".$row[csf("job_no")]."', 'load_drop_down_process', 'process_td' )\n";
	}
	exit();
}


// =================================
// program no 
if ($action=="programs_search_popup")
{
  	echo load_html_head_contents("Program Search","../../../", 1, 1, $unicode);
    ?>
     
	<script>	 
      /*  function js_set_value(str_data)
        {         
			document.getElementById('selected_program_no_primary_id').value=str_data;
            parent.emailwindow.hide();
        }	*/
		
	 var selected_id = new Array(); 
	 var selected_name = new Array();	
		
		//var first_item_group=0;
	 
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
		
		function js_set_value( str ) {
			if($("#search"+str).css("display") !='none'){
				toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
				if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
					selected_id.push( $('#txt_individual_id' + str).val() );
					selected_name.push( $('#txt_individual_name' + str).val() );
				}
				else{
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 );
				}
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			//alert(id);
			$('#txt_selected_id').val( id );
			$('#txt_selected_name').val( name );
		}
   
    </script>

    </head>

    <body>
	<?
	 extract($_REQUEST); 
	 ?>
			
        <div align="center" style="width:100%;" >            
            <?
               
               
                if ($orderNo!='') 
                {
                    $orderNumbers = explode(",",$orderNo);
                    $order_numbers = "";
                    //KNITTING_PARTY

                    foreach($orderNumbers as $orderNumber)
                    {         
                        if($order_numbers=="") $order_numbers="'".$orderNumber."'"; else $order_numbers .=",'".$orderNumber."'";
                    }

                    if($supplier_id !=0) 
                    {
                        $supplier_cond = "and knitting_party='".$supplier_id."'";
                    }

					if($pay_mode==3 || $pay_mode==5)
					{
						$knitting_source = 1;
					}else {
						$knitting_source = 3;
					}
					
                    $poids=$order_id; 
                   // $arr=array (2=>$body_part,3=>$color_type);
					//convId
                    $sql_prog = "SELECT  a.mst_id as plan_id,a.dtls_id as program_no,a.booking_no,a.body_part_id,a.color_type_id,a.fabric_desc,a.gsm_weight,a.dia,a.yarn_desc,b.id as fabric_des_id,sum(a.program_qnty) as program_qnty FROM ppl_planning_entry_plan_dtls a,wo_pre_cost_fab_conv_cost_dtls b,ppl_planning_info_entry_dtls c WHERE a.yarn_desc = b.fabric_description and a.dtls_id = c.id and c.knitting_source = $knitting_source and b.cons_process=1 and a.po_id in($poids) and b.id in($convId) $supplier_cond group by a.mst_id ,a.dtls_id,a.booking_no,a.body_part_id,a.color_type_id,a.fabric_desc,a.gsm_weight,a.dia,a.yarn_desc,b.id";
                     //echo $sql_prog; //die;
					 
                   // echo  create_list_view("list_view", "Plan Id,Program no,Body Part,Color Type,Fabric Desc,Fabric Gsm,Dia,Prog. Qnty", "70,80,100,100,200,100,100,80,80","930","320",0, $sql , "js_set_value", "id,fabric_des_id", "", 1, "0,0,body_part_id,color_type_id,0,0,0,0", $arr , "plan_id,program_no,body_part_id,color_type_id,fabric_desc,gsm_weight,dia,program_qnty", '','','0,0,0,0,0,0,0,0,0,0','',''); 
				   ?>
				   <input type="hidden" name="txt_selected_id" id="txt_selected_id" value="<?= $prog_no;  ?>" />
				    <input type="hidden" name="txt_selected_name" id="txt_selected_name" value="<?= $prog_no;  ?>" />
				    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="660" class="rpt_table" >
					<thead>
						<th width="30">SL</th>
						<th width="50">Plan Id</th>
						<th width="100">Program no</th>
						<th width="80">Body Part</th>
						<th width="100">Color Type</th>
						<th width="100">Fabric Desc</th>
						<th width="">Fabric Gsm</th>
						<th width="70">Dia</th>
						<th width="70">Prog. Qnty</th>
						
					</thead>
					<tbody id="tbl_list_search">
					<?
					$k=1;
					$current_prog=explode(",", $prog_no);
					$result_prog=sql_select($sql_prog);
					foreach ($result_prog as $selectResult)
                    {
						if ($k%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";						
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $k;?>" onClick="js_set_value(<? echo $k;?>)">
                    <td width="30">	<? echo $k;?>  </td>
                    <td width="50"><? echo $selectResult[csf('plan_id')];?></td>
                    <td width="100"><? echo $selectResult[csf('program_no')];?></td>
                    <td width="80"><? echo $body_part[$selectResult[csf('body_part_id')]];?></td>
                    <td width="100">
					<? echo $color_type[$selectResult[csf('color_type_id')]];?>
                    <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $k ?>" value="<? echo $selectResult[csf('program_no')]; ?>"/>	
                    <input type="hidden" name="txt_individual_name" id="txt_individual_name<?php echo $k ?>" value="<? echo $selectResult[csf('fabric_des_id')]; ?>"/>
                    </td>
                    <td width="100">
					<? echo $selectResult[csf('fabric_desc')];?>
                    </td>
                    <td width=""><? echo $selectResult[csf('gsm_weight')];?></td>
					<td width="70"><? echo $selectResult[csf('dia')];?></td>
					<td width="70"><? echo number_format($selectResult[csf('program_qnty')],0);?></td>
                    </tr>
					<?
					if(in_array($selectResult[csf('program_no')], $current_prog)){
						?>
						<script>
							var sl=<? echo $k; ?>;
							console.log(sl);
							js_set_value(sl);
						</script>
						<?
					}
					$k++;
					}
					?>
					</tbody>
        		 </table>
				 
				   <?
                    
                }
				
            ?>    
			 <table width="500" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
        </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if ($action=="populate_data_from_program_popup")
{      
    if($data !='')
    {
        $sql = "SELECT id, mst_id as plan_id,dtls_id as program_no,booking_no,body_part_id,color_type_id,fabric_desc,gsm_weight,dia,program_qnty FROM ppl_planning_entry_plan_dtls WHERE id dtls_id($data) ";
        $data_array=sql_select($sql);
           
        foreach ($data_array as $row)
        {
            
            echo "document.getElementById('txt_program_no').value = '".$data."';\n";    
            
            echo "$('#cbo_supplier_name').prop('disabled', true);";
        }  
    }
}
// =================================


if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );   
	exit();	 
} 

if ($action=="load_drop_down_fabric_description")
{	
	$data=explode("_",$data);
	$bookingType=$data[2];
    
	$fabric_description_array=array();
	if($bookingType==0) 
	{
		if($data[1] =="")
		{
			$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls where job_no in('$data[0]')
			 and cons_process=1 and status_active=1 and is_deleted=0 ");
		}
		else
		{
			$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls where job_no in($data[0]) and status_active=1 and is_deleted=0 and cons_process=1  ");
		}
	}
	else
	{
		$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select distinct a.id,c.id as fabric_description from wo_pre_cost_fabric_cost_dtls c,WO_BOOKING_DTLS b,
		wo_pre_cost_fab_conv_cost_dtls a where 
		 b.pre_cost_fabric_cost_dtls_id=c.id and b.pre_cost_fabric_cost_dtls_id=a.fabric_description and c.id=a.fabric_description and c.job_no in('$data[0]')  and b.is_short=1
		 and a.cons_process=1 and b.booking_type=1
		and c.status_active=1 and c.is_deleted=0  and b.status_active=1 and b.is_deleted=0   and a.status_active=1 and a.is_deleted=0  ");
		if(count($wo_pre_cost_fab_conv_cost_dtls_id)<=0)
		{
			echo "<div><b> No Conversation Description Found.</b> </div>";
		}
	}
	foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row)
	{
		if($row[csf("fabric_description")]!=0)
		{
			$fabric_description=sql_select("select id, body_part_id, color_type_id, fabric_description, gsm_weight from wo_pre_cost_fabric_cost_dtls where  id='".$row[csf("fabric_description")]."' and status_active=1 and is_deleted=0");
			list($fabric_description_row)=$fabric_description;
			
			$fabric_description_array[$row[csf("id")]]=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")].', '.$fabric_description_row[csf("gsm_weight")];
			
		}
		
		if($row[csf("fabric_description")]==0)
		{
			$fabric_description=sql_select("select id, body_part_id, color_type_id, fabric_description, gsm_weight from wo_pre_cost_fabric_cost_dtls where job_no in($data[0]) and status_active=1 and is_deleted=0 ");
			foreach( $fabric_description as $fabric_description_row)
	        {
			$fabric_description_array[$row[csf("id")]]="All Fabrics  ".$conversion_cost_head_array[$row[csf("cons_process")]].', '.$row[csf("gsm_weight")];
			}
		}
	}
	echo create_drop_down( "cbo_fabric_description", 470, $fabric_description_array,"", 1, "-- Select --", $selected, "load_drop_down( 'requires/service_booking_multi_job_wise_knitting_controller',this.value, 'load_drop_down_dia', 'dia_td'); set_process(this.value,'set_process')" );
	exit();
} 

if($action=='load_drop_down_dia')
{
	//$exdata=explode("**",$data);    
	//$fab_cost_dtls_id=return_library_array( "select id, fabric_description from wo_pre_cost_fab_conv_cost_dtls where id in($data) ",'id','fabric_description');
	//$dtls_id=$fab_cost_dtls_id[$data];
	 $result_fab=sql_select("select id, fabric_description from wo_pre_cost_fab_conv_cost_dtls where id in($data)");
	// echo "select id, fabric_description from wo_pre_cost_fab_conv_cost_dtls where id in($data)";
	 $dtls_id="";
	 foreach( $result_fab as $row)
	 {
	 	if($dtls_id!="") $dtls_id.=",".$row[csf('fabric_description')]; else  $dtls_id=$row[csf('fabric_description')];
	 }
	//echo "select dia_width from wo_pre_cos_fab_co_avg_con_dtls where pre_cost_fabric_cost_dtls_id in($dtls_id) group by dia_width order by dia_width ASC";
	echo create_drop_down( "cbo_dia", 80, "select dia_width from wo_pre_cos_fab_co_avg_con_dtls where pre_cost_fabric_cost_dtls_id in($dtls_id) group by dia_width order by dia_width ASC","dia_width,dia_width", "", "", $data, "" );
    //echo "document.getElementById('txt_program_no').value = '".$exdata[2]."';\n";   
    exit();
}
 
if($action=="set_process")
{
	$process=return_field_value("cons_process", "wo_pre_cost_fab_conv_cost_dtls", "id=$data");
	echo $process; die;
}
 


if($action=="show_detail_booking_list_view") 
{
	$data=explode("**",$data);  
    
	$job_po_id=$data[0];
	$type=$data[1];
	$fabric_description_id=$data[2];
	$process=$data[3];
	$txt_booking_no=$data[6];
	$dtls_id=implode(",",explode(",",$data[7]));
	$rate_from_library=$data[8];
    $programNo=$data[9];
	$paymode=$data[10];
	$company=$data[11];
	$short_type=$data[12];
	//echo  $programNo.'DD';
	if($paymode==3 || $paymode==5)
	{
		$kinitting_source = 1;
	}else {
		$kinitting_source = 3;
	}
    
	$fab_req_source=return_field_value("excut_source", "variable_order_tracking", "company_name=$company and variable_list=66 and status_active=1 and is_deleted=0");
	if($fab_req_source=="" || $fab_req_source==0) $fab_req_source=1;else $fab_req_source=$fab_req_source;
	//echo $fab_req_source.'DD';
	
	$fabric_description_array_empty=array();
	$fabric_description_array=array();
	$short_cond="";
	if($short_type==0)
	{
	$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select c.job_no,c.id,c.fabric_description,c.cons_process from wo_pre_cost_fab_conv_cost_dtls c,wo_po_break_down b where  
	c.job_id=b.job_id and b.id in($job_po_id) group by c.job_no,c.id,c.fabric_description,c.cons_process");
	}
	else //short booking
	{
		$short_cond=" and b.is_short=1";
	$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select c.job_no,a.id,a.fabric_description from wo_pre_cost_fabric_cost_dtls c,wo_po_break_down b,wo_booking_dtls d,
	wo_pre_cost_fab_conv_cost_dtls a where 
	 c.job_id=b.job_id and d.pre_cost_fabric_cost_dtls_id=c.id and d.pre_cost_fabric_cost_dtls_id=a.fabric_description and c.id=a.fabric_description and d.is_short=1 and d.booking_type=1  and b.id in($job_po_id) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.is_deleted=0 
	 and d.status_active=1 and d.is_deleted=0 group by c.job_no,a.id,a.fabric_description");
	
	}
	foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id)
	{
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]!=0)
		{
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls  
			where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."'");
			list($fabric_description_row)=$fabric_description;
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")];
		}
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]==0)
		{			
			$fabric_description_string="";
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls  
			where  job_no='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("job_no")]."'");
			foreach( $fabric_description as $fabric_description_row)
	        {
			$fabric_description_string.=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")]." and ";
			}
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]].=rtrim($fabric_description_string,"and ");
		}
	}
	unset($wo_pre_cost_fab_conv_cost_dtls_id);
	$process=$data[3];
	$sensitivity=$data[4];
	$txt_order_no_id=$data[5];
	$program_cond="";
	
	 if($programNo!='')
	 {
	   $program_cond=" and a.dtls_id in($programNo)";
	 $sql_prog = "SELECT  a.po_id,a.dtls_id as program_no,a.booking_no,c.machine_gg,c.machine_dia,c.stitch_length,b.id as fabric_des_id,sum(a.program_qnty) as program_qnty FROM ppl_planning_entry_plan_dtls a,wo_pre_cost_fab_conv_cost_dtls b,ppl_planning_info_entry_dtls c WHERE a.yarn_desc = b.fabric_description and a.dtls_id = c.id  and b.cons_process=1 and a.po_id in($job_po_id) and b.id in($fabric_description_id) $program_cond  group by a.po_id ,a.dtls_id,a.booking_no,c.machine_gg,c.machine_dia,c.stitch_length,b.id";
		$dataResultProg=sql_select($sql_prog);
		foreach( $dataResultProg as $row)
	        {
				$prog_data_arr[$row[csf('po_id')]]['machine_gg']=$row[csf('machine_gg')];
				$prog_data_arr[$row[csf('po_id')]]['machine_dia']=$row[csf('machine_dia')];
				$prog_data_arr[$row[csf('po_id')]]['stitch_length']=$row[csf('stitch_length')];
			}
	 }
	// print_r($prog_data_arr);
		
	$wo_pre_cost_fab_co_color_sql=sql_select("select b.gmts_color_id,b.contrast_color_id,c.id as fab_dtls_id from wo_pre_cos_fab_co_color_dtls b,wo_pre_cost_fab_conv_cost_dtls c  where  c.job_no=b.job_no and c.id in($fabric_description_id) and b.pre_cost_fabric_cost_dtls_id=c.fabric_description"); 
	foreach( $wo_pre_cost_fab_co_color_sql as $row)
	{
		$contrast_color_arr[$row[csf('fab_dtls_id')]][$row[csf('gmts_color_id')]]['contrast_color']=$row[csf('contrast_color_id')];
	}
	unset($wo_pre_cost_fab_co_color_sql);
	$wo_pre_cost_fab_avg=sql_select("select b.color_number_id,d.id as fab_dtls_id,b.dia_width,c.gsm_weight,b.po_break_down_id as po_id from wo_pre_cos_fab_co_avg_con_dtls b,wo_pre_cost_fabric_cost_dtls c,wo_pre_cost_fab_conv_cost_dtls d  where  c.job_id=b.job_id and d.job_id=b.job_id and c.id=b.pre_cost_fabric_cost_dtls_id   and d.id in($fabric_description_id) and b.po_break_down_id in($txt_order_no_id) and b.pre_cost_fabric_cost_dtls_id=d.fabric_description");
	foreach( $wo_pre_cost_fab_avg as $row)
	{
		if($row[csf('dia_width')]!='' || $row[csf('gsm_weight')]!='')
		{
			//echo $row[csf('dia_width')].', ';
			$fav_avg_color_arr[$row[csf('fab_dtls_id')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['dia_width']=$row[csf('dia_width')];
			$fav_avg_color_arr[$row[csf('fab_dtls_id')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['gsm_weight']=$row[csf('gsm_weight')];
		}
	}
	unset($wo_pre_cost_fab_avg);
	//echo $fab_req_source.'d';
	if($fab_req_source==1) //Budget
	{
		$condition= new condition();
		if(str_replace("'","",$job_po_id) !=''){
			$condition->po_id("in($job_po_id)");
		}

		$condition->init();
		
		$conversion= new conversion($condition);
		$conversion_knit_qty_arr=$conversion->getQtyArray_by_ConversionidOrderColorAndUom();
		//print_r($conversion_knit_qty_arr);
		$conversion_color_size_knit_qty_arr=$conversion->getQtyArray_by_ConversionidOrderColorSizeidAndUom();
		$conversion_po_size_knit_qty_arr=$conversion->getQtyArray_by_ConversionidOrderSizeidAndUom();
	
	}
	else //Fabric Booking
	{
		   $sql_data_fab="select a.id as fab_dtl_id,b.job_no,b.po_break_down_id as po_id,b.sensitivity,b.gmts_color_id,b.fabric_color_id,(b.grey_fab_qnty) as grey_fab_qnty,
		  (b.amount) as amount from  wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b where  b.pre_cost_fabric_cost_dtls_id=a.id and b.job_no=a.job_no and b.status_active=1 
		  and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and b.booking_type=1   and b.po_break_down_id in($job_po_id) $short_cond ";//group by b.job_no,a.id,b.po_break_down_id,b.sensitivity,b.gmts_color_id,b.fabric_color_id
		  $resultData_fab=sql_select($sql_data_fab);
		  	foreach($resultData_fab as $row)
			{
				$fab_req_qty_booking_arr[$row[csf('po_id')]][$row[csf('fab_dtl_id')]][$row[csf('gmts_color_id')]]['grey_fab_qnty']+=$row[csf('grey_fab_qnty')];
			}
		
	}
		//print_r($conversion_po_size_knit_qty_arr);
	  if($programNo!='') $program_cond=" and b.program_no in($programNo)";else $program_cond="";
	  $sql_data_Priv="select c.id as conv_dtl_id,b.job_no,b.po_break_down_id as po_id,b.sensitivity,b.gmts_color_id,b.fabric_color_id,sum(b.wo_qnty) as wo_qnty,
	  sum(b.amount) as amount from  wo_pre_cost_fab_conv_cost_dtls c,wo_booking_dtls b where  b.pre_cost_fabric_cost_dtls_id=c.id and b.job_no=c.job_no and b.status_active=1
	   and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and b.booking_type=3 and b.entry_form_id=228 and b.po_break_down_id in($job_po_id)  and b.process=1 $program_cond group by b.job_no,c.id,b.po_break_down_id,b.sensitivity,b.gmts_color_id,b.fabric_color_id";
	 
		$dataResultPre=sql_select($sql_data_Priv);
		$po_fab_prev_booking_arr=array();
		foreach($dataResultPre as $row)
		{
			if($row[csf('sensitivity')]==1 || $row[csf('sensitivity')]==3)// AS Per Garments/Contrast Color
			{
				$po_fab_prev_color_booking_arr[$row[csf('po_id')]][$row[csf('conv_dtl_id')]][$row[csf('gmts_color_id')]]['wo_qnty']=$row[csf('wo_qnty')];			
				$po_fab_prev_color_booking_arr[$row[csf('po_id')]][$row[csf('conv_dtl_id')]][$row[csf('gmts_color_id')]]['amount']=$row[csf('amount')];
				$po_fab_prev_booking_arr2[$row[csf('conv_dtl_id')]]['wo_qty']=$row[csf('wo_qnty')];
			}
		}
		unset($dataResultPre);
		
		//print_r($po_fab_prev_color_booking_arr);
		
		
				
		if(($programNo =='') && $sensitivity==1 || $sensitivity==3 )
		{
			// $pre_item_color_arr=return_library_array("select pre_cost_fabric_cost_dtls_id, contrast_color_id from wo_pre_cos_fab_co_color_dtls group by pre_cost_fabric_cost_dtls_id, contrast_color_id","pre_cost_fabric_cost_dtls_id","contrast_color_id");
			 $pre_item_color_arr=return_library_array("select b.pre_cost_fabric_cost_dtls_id, b.contrast_color_id from wo_pre_cos_fab_co_color_dtls b,wo_po_break_down c where b.job_id=c.job_id and   c.status_active=1  and c.is_deleted=0 and b.status_active=1  and b.is_deleted=0 and c.id in($job_po_id)  group by b.pre_cost_fabric_cost_dtls_id, b.contrast_color_id","pre_cost_fabric_cost_dtls_id","contrast_color_id");
			 
			  $sql1="select a.job_no,b.id as po_id, b.po_number,min(c.id)as color_size_table_id, c.color_number_id, sum(c.plan_cut_qnty) as plan_cut_qnty,f.fabric_description as fabric_desc, e.cons_process, e.charge_unit,f.id as pre_cost_fabric_cost_dtls_id,f.uom, f.body_part_id,f.gsm_weight
			 
			 from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_mst d, wo_pre_cost_fab_conv_cost_dtls e, wo_pre_cost_fabric_cost_dtls f, wo_pre_cos_fab_co_avg_con_dtls g  
			 where  e.id in($fabric_description_id) and b.id in($txt_order_no_id) and a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id and a.id=g.job_id and b.id=c.po_break_down_id and b.id=g.po_break_down_id and c.color_number_id=g.color_number_id and  c.size_number_id=g.gmts_sizes   and c.id=g.color_size_table_id and c.item_number_id=f.item_number_id and f.id=g.pre_cost_fabric_cost_dtls_id and e.fabric_description=f.id   and a.status_active=1 and a.is_deleted=0  and b.status_active=1  and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and e.cons_process=1  
			 group by a.job_no,b.id, b.po_number, c.color_number_id, f.fabric_description, e.cons_process, e.charge_unit,f.id,f.body_part_id,f.uom,f.gsm_weight";
			 
			 
				 
			  /* $sql1="select a.job_no,b.id as po_id, b.po_number,min(c.id)as color_size_table_id, c.color_number_id, sum(c.plan_cut_qnty) as plan_cut_qnty,f.fabric_description as fabric_desc, e.cons_process, e.charge_unit,f.id as pre_cost_fabric_cost_dtls_id,f.uom, f.body_part_id,f.gsm_weight
			 
			 from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_mst d, wo_pre_cost_fab_conv_cost_dtls e,
			  wo_pre_cost_fabric_cost_dtls f, wo_pre_cos_fab_co_avg_con_dtls g ,wo_booking_dtls h 
			 where  e.id in($fabric_description_id) and b.id in($txt_order_no_id) and a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id
			  and a.id=g.job_id and b.id=c.po_break_down_id  and b.id=h.po_break_down_id and f.id=h.pre_cost_fabric_cost_dtls_id   and e.fabric_description=h.pre_cost_fabric_cost_dtls_id  
			  and b.id=g.po_break_down_id and e.fabric_description=h.pre_cost_fabric_cost_dtls_id  and c.color_number_id=g.color_number_id and  c.size_number_id=g.gmts_sizes  
			   and c.id=g.color_size_table_id and c.item_number_id=f.item_number_id and f.id=g.pre_cost_fabric_cost_dtls_id and e.fabric_description=f.id and h.is_short=1  
			    and a.status_active=1 and a.is_deleted=0  and b.status_active=1  and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
				 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and e.cons_process=1  
			 group by a.job_no,b.id, b.po_number, c.color_number_id, f.fabric_description, e.cons_process, e.charge_unit,f.id,f.body_part_id,f.uom,f.gsm_weight";*/
			 
			 
		 
			
		}
        else if (($sensitivity == 1 || $sensitivity == 3) && $programNo!='')
        {
         /* $sql1 = "SELECT a.id, a.mst_id as plan_id,a.dtls_id as program_no,a.booking_no,a.body_part_id,a.color_type_id,a.fabric_desc,a.gsm_weight,a.dia,a.program_qnty,a.yarn_desc,a.po_id as po_id, b.id as fabric_des_id,b.amount,b.job_no,b.charge_unit,min(d.id)as color_size_table_id,d.color_number_id as color_number_id,sum(d.plan_cut_qnty) as plan_cut_qnty
            FROM ppl_planning_entry_plan_dtls a,wo_pre_cost_fab_conv_cost_dtls b,ppl_planning_info_entry_dtls c, wo_po_color_size_breakdown d WHERE a.yarn_desc = b.fabric_description and a.dtls_id = c.id and c.knitting_source = $kinitting_source and b.cons_process=1 and a.dtls_id in($programNo) and b.id in($fabric_description_id) and a.po_id=d.po_break_down_id and  d.color_number_id=c.color_id and d.is_deleted=0 and d.status_active=1 and b.status_active=1 and a.status_active=1 group by a.id,a.mst_id,a.dtls_id,a.booking_no,a.body_part_id,a.color_type_id,a.fabric_desc,a.gsm_weight,a.dia,a.program_qnty,a.yarn_desc,a.po_id , b.id,b.amount,b.charge_unit,b.job_no,d.color_number_id,a.gsm_weight "; */
			 $sql1 = "SELECT a.id, a.mst_id as plan_id,a.dtls_id as program_no,a.booking_no,a.body_part_id,a.color_type_id,a.fabric_desc,a.gsm_weight,a.dia,a.program_qnty,(e.color_prog_qty) as color_prog_qty,a.yarn_desc,a.po_id as po_id, b.id as fabric_des_id,b.amount,b.job_no,b.charge_unit,min(d.id)as color_size_table_id,d.color_number_id as color_number_id,sum(d.plan_cut_qnty) as plan_cut_qnty
            FROM ppl_planning_entry_plan_dtls a,wo_pre_cost_fab_conv_cost_dtls b,ppl_planning_info_entry_dtls c, wo_po_color_size_breakdown d,ppl_color_wise_break_down e WHERE a.yarn_desc = b.fabric_description and a.dtls_id = c.id and a.dtls_id = e.program_no and c.id = e.program_no and e.color_id=d.color_number_id and c.knitting_source = $kinitting_source and b.cons_process=1 and a.dtls_id in($programNo) and b.id in($fabric_description_id) and a.po_id=d.po_break_down_id and d.is_deleted=0 and d.status_active=1 and b.status_active=1 and a.status_active=1 group by a.id,a.mst_id,a.dtls_id,a.booking_no,a.body_part_id,a.color_type_id,a.fabric_desc,a.gsm_weight,a.dia,e.color_prog_qty,a.program_qnty,a.yarn_desc,a.po_id , b.id,b.amount,b.charge_unit,b.job_no,d.color_number_id,a.gsm_weight "; 
        }
		
		// echo $sql1;
		$dataArray=sql_select($sql1);
		
		foreach($dataArray as $row)
		{
			$fabric_desc=$body_part[$row[csf('body_part_id')]].','.$row[csf('fabric_desc')].','.$row[csf('gsm_weight')];
			$fab_conv_detail_arr[$fabric_description_id][$row[csf('po_id')]][$row[csf('color_number_id')]]['color_size_table_id']=$row[csf('color_size_table_id')];
			$fab_conv_detail_arr[$fabric_description_id][$row[csf('po_id')]][$row[csf('color_number_id')]]['plan_cut_qnty']=$row[csf('plan_cut_qnty')];
			$fab_conv_detail_arr[$fabric_description_id][$row[csf('po_id')]][$row[csf('color_number_id')]]['program_qnty']=$row[csf('program_qnty')];
			$fab_conv_detail_arr[$fabric_description_id][$row[csf('po_id')]][$row[csf('color_number_id')]]['program_no']=$row[csf('program_no')];
			$fab_conv_detail_arr[$fabric_description_id][$row[csf('po_id')]][$row[csf('color_number_id')]]['charge_unit']=$row[csf('charge_unit')];
			$fab_conv_detail_arr[$fabric_description_id][$row[csf('po_id')]][$row[csf('color_number_id')]]['costing_per']=$row[csf('costing_per')];
			$fab_conv_detail_arr[$fabric_description_id][$row[csf('po_id')]][$row[csf('color_number_id')]]['po_number']=$row[csf('po_number')];
			$fab_conv_detail_arr[$fabric_description_id][$row[csf('po_id')]][$row[csf('color_number_id')]]['fabric_description']=$fabric_desc;
			$fab_conv_detail_arr[$fabric_description_id][$row[csf('po_id')]][$row[csf('color_number_id')]]['body_part_id']=$row[csf('body_part_id')];
			$fab_conv_detail_arr[$fabric_description_id][$row[csf('po_id')]][$row[csf('color_number_id')]]['job_no']=$row[csf('job_no')];
			$fab_conv_detail_arr[$fabric_description_id][$row[csf('po_id')]][$row[csf('color_number_id')]]['uom']=$row[csf('uom')];
			$fab_conv_detail_arr[$fabric_description_id][$row[csf('po_id')]][$row[csf('color_number_id')]]['fabric_dtl_id']=$row[csf('pre_cost_fabric_cost_dtls_id')];
			$fab_conv_detail_arr[$fabric_description_id][$row[csf('po_id')]][$row[csf('color_number_id')]]['convid']=$row[csf('convid')];
		}
		
		
		$tot_prev_wo_qty=$po_fab_prev_booking_arr2[$fabric_description_id]['wo_qty'];
		$prev_wo_qty=$po_fab_prev_booking_arr[$row[csf("po_break_down_id")]][$fabric_description_id]['wo_qty'];
		$charge_unit=$po_fab_con_charge_arr[$row[csf("po_break_down_id")]][$fabric_description_id]['rate'];
		if($prev_wo_qty=='' || $prev_wo_qty==0) $prev_wo_qty=0;else $prev_wo_qty=$prev_wo_qty;
		//if($prev_wo_qty=='' || $prev_wo_qty==0) $prev_wo_qty=0;else $prev_wo_qty=$prev_wo_qty;
		?>
			
            
			<div id="content_search_panel_<? echo $fabric_description_id; ?>" style="" class="accord_close2">
            <p> <?
            if($fab_req_source!=1) echo "<b style='color:#999933'> Data Populate from Fabric Booking.</b>";
			?></p>
				<table class="rpt_table" border="1" width="1280"  cellpadding="0" cellspacing="0" rules="all" id="table_<? echo $fabric_description_id; ?>">
					<thead>
						<th>Job No </th>
						<th>Po Number </th>
						<th title="Fab. Req. Source=<? echo $fab_req_source;?>">Fabric Description</th>
                        <th>Artwork No</th>
						<th>Y.Count</th>
						<th>Lot</th>
						<th>Brand</th>
						<th>Gmts. Color</th>
						<th>Item Color</th>
						<th>Item Size</th>
						 
                        <th>Fab. Mapping</th>
						<th>M/C Dia/GG </th>
						
						<th>Fin Dia</th>
						<th>Fin GSM</th>
						<th>S.length</th>
						<th>Delivery Start Date</th>
                        <th>Delivery End Date</th>
                        <th>UOM</th>
                      
                        <th>WO. Qnty</th>
                        <th>Rate</th>
                        <th>Amount</th>
                        <th title="<? echo $tot_prev_wo_qty;?>">Plan Cut Qnty</th>
						<th>Remark</th>
					</thead>
					<tbody>
					<? //$fab_req_source=1;
					if ($sensitivity == 1 && $programNo!='')
                    {
                       
                        $i = 1;
                        foreach($fab_conv_detail_arr as $fab_id=>$fab_data)
                        {
							foreach($fab_data as $po_id=>$po_data)
							{
								foreach($po_data as $color_id=>$row)
								{
                            
								//echo $row[("program_qnty")].'DS----';
								//$pre_req_qnty=array_sum($conversion_knit_qty_arr[$fabric_description_id][$po_id][$color_id]);
								$wo_prev_qnty=$po_fab_prev_color_booking_arr[$po_id][$fabric_description_id][$color_id]['wo_qnty'];
								$wo_prev_amount=$po_fab_prev_color_booking_arr[$po_id][$fabric_description_id][$color_id]['amount'];
									
								if($row[("body_part_id")]==2 || $row[("body_part_id")]==3 )
								{
									$woqnty=$row[("program_qnty")]*2;
									$uom_item="1,2";
									$selected_uom="1";
									$bal_woqnty=$woqnty-$wo_prev_qnty;
								}
								else 
								{
									$woqnty=$row[("program_qnty")];
									$selected_uom="12";
									$bal_woqnty=$woqnty-$wo_prev_qnty;
									
									$rate=$row[("charge_unit")];
									$amount=$rate*$bal_woqnty;
								}
								//echo $woqnty.'='.$rate.', '; 
								
								/*if($row[("body_part_id")]==2 || $row[("body_part_id")]==3)
								{
									$rate="";
									$amount="";	
								}
								else
								{                                
									$rate=$row[("charge_unit")];
									$amount=$rate*$row[("program_qnty")];
								}*/
								
								if($rate_from_library==1)
								{
									$rate='';
									$amount="";
									$rate_disable="disabled";	
								}
								else
								{
									$fab_mapping_disable="disabled";
								}
								
                           
								$item_color="";
								$item_color_id="";
								if($sensitivity==3)
								{
									
									$item_colorID=$contrast_color_arr[$fabric_description_id][$color_id]['contrast_color'];
									if($item_colorID!=0)
									{
										$item_color=$color_library[$contrast_color_arr[$fabric_description_id][$color_id]['contrast_color']];
										$item_color_id=$contrast_color_arr[$fabric_description_id][$color_id]['contrast_color'];
									}
									else
									{
										$item_color=$color_library[$color_id];
										$item_color_id=$color_id;
									}
								}
								else if($sensitivity==1 || $sensitivity==4)
								{
									$item_color=$color_library[$color_id];
									$item_color_id=$color_id;
								}
								else 
								{
									$item_color="";
									$item_color_id="";
								}	
								$dia_width=$fav_avg_color_arr[$fabric_description_id][$po_id][$color_id]['dia_width'];
								$gsm_weight=$fav_avg_color_arr[$fabric_description_id][$po_id][$color_id]['gsm_weight'];
								$machine_gg=$prog_data_arr[$po_id]['machine_gg'];
								$machine_dia=$prog_data_arr[$po_id]['machine_dia'];
								$stitch_length=$prog_data_arr[$po_id]['stitch_length'];
								//echo $machine_gg.'=='.$machine_dia.'<br>';
								if($machine_gg=='' && $machine_dia=='')
								{ 
									$mc_cond="";
								}
								else if($machine_gg!='' && $machine_dia!='')
								{ 
									$mc_cond=$machine_gg.'X'.$machine_dia;
								}
								else if($machine_gg!='' && $machine_dia=='')
								{ 
									$mc_cond=$machine_gg;
								}
								else if($machine_gg=='' && $machine_dia!='')
								{ 
									$mc_cond=$machine_dia;
								}
								else $mc_cond="";
								$bal_amount=($row[("program_qnty")]*$rate)-$wo_prev_amount;
                            
                        ?>
                            <tr align="center">
							<td>
								<input type="text" name="txt_job_no_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_job_no_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[("job_no")]; ?>" style="width:70px;" class="text_boxes">
							</td>
                                <td>
                                    <?
                                        echo create_drop_down("po_no_".$fabric_description_id."_".$i, 100, $po_number,"", 1,'', $po_id,"",1);
                                    ?>
                                    <input type="hidden" name="po_id_<? echo $fabric_description_id.'_'.$i; ?>" id="po_id_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $po_id; ?>" style="width:110px;" class="text_boxes" disabled="disabled">
                                </td>
                                <td>
                                    <?
                                        echo create_drop_down("fabric_description_".$fabric_description_id."_".$i, 250, $fabric_description_array,"", 1,'', $fabric_description_id,"",1);
                                    ?>
                                    <input type="hidden" name="fabric_description_id_<? echo $fabric_description_id.'_'.$i; ?>" id="fabric_description_id_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $fabric_description_id; ?>" style="width:110px;" class="text_boxes" disabled="disabled">
                                </td>
                                
                                <td>
                                    <input type="text" name="artworkno_<? echo $fabric_description_id.'_'.$i; ?>" id="artworkno_<? echo $fabric_description_id.'_'.$i; ?>" value="<? //echo $fabric_description_id; ?>" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'artworkno');" class="text_boxes">
                                </td>
								<td>
                                    <input type="text" name="txt_ycount_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_ycount_<? echo $fabric_description_id.'_'.$i; ?>" value="<? //echo $fabric_description_id; ?>" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'ycount');" class="text_boxes">
                                </td>
								<td>
                                    <input type="text" name="txt_lot_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_lot_<? echo $fabric_description_id.'_'.$i; ?>" value="<? //echo $fabric_description_id; ?>" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'lotno');" class="text_boxes">
                                </td>
								<td>
                                    <input type="text" name="txt_brand_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_brand_<? echo $fabric_description_id.'_'.$i; ?>" value="<? //echo $fabric_description_id; ?>" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'brand');" class="text_boxes">
                                </td>
								 <td>
                                    <input type="hidden" name="color_size_table_id_<? echo $fabric_description_id.'_'.$i; ?>" id="color_size_table_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<?  echo $row[("color_size_table_id")];?>" disabled="disabled"/>
                                    <input type="text" name="gmts_color_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_color_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==1 || $sensitivity==3 ){ echo $color_library[$color_id];} else { echo "";}?>" disabled="disabled"/>
                                    <input type="hidden" name="gmts_color_id_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_color_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==1 || $sensitivity==3 ){ echo $color_id;} else { echo "";}?>"disabled="disabled"/>
                                </td>
                                <td>
                                    <input type="text" name="item_color_<? echo $fabric_description_id.'_'.$i; ?>" id="item_color_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes" onChange="copy_value()" value="<? echo $item_color; ?>"/>
                                    <input type="hidden" name="item_color_id_<? echo $fabric_description_id.'_'.$i; ?>" id="item_color_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? echo $item_color_id; ?>" disabled="disabled"/>
                                </td>
								 <td>
                                    <input type="text" name="item_size_<? echo $fabric_description_id.'_'.$i; ?>" id="item_size_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes" onChange="copy_value()" value="<? //echo $item_color; ?>"/>
                                    
                                </td>
                                <td>
                                    <input type="text" name="subcon_supplier_compo_<? echo $fabric_description_id.'_'.$i; ?>" id="subcon_supplier_compo_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="" onDblClick="service_supplier_popup('<? echo $fabric_description_id.'_'.$i; ?>')" placeholder="Browse" <?php echo $fab_mapping_disable; ?>>
									 <input type="hidden" name="updateid_<? echo $fabric_description_id.'_'.$i; ?>" id="updateid_<? echo $fabric_description_id.'_'.$i; ?>" value="">
                                  
                                    <input type="hidden" name="subcon_supplier_rateid_<? echo $fabric_description_id.'_'.$i; ?>" id="subcon_supplier_rateid_<? echo $fabric_description_id.'_'.$i; ?>" value="">
                                </td>
								<td>
                                    <input type="text" name="txt_mcdia_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_mcdia_<? echo $fabric_description_id.'_'.$i; ?>"  style="width:80px;" value="<? echo $mc_cond;?>" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'mcdia');" class="text_boxes">
                                </td>
								
								<td>
                                    <input type="text" name="txt_findia_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_findia_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $dia_width; ?>" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'findia');" class="text_boxes">
                                </td>
								<td>
                                    <input type="text" name="txt_fingsm_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_fingsm_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $gsm_weight; ?>" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'fingsm');" class="text_boxes">
                                </td>
								<td>
                                    <input type="text" name="txt_slength_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_slength_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $stitch_length; ?>" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'slength');" class="text_boxes">
                                </td>
								 <td>
                                    <input type="text" name="startdate_<? echo $fabric_description_id.'_'.$i; ?>" id="startdate_<? echo $fabric_description_id.'_'.$i; ?>" value="<? //echo $row[csf("start_date")]; ?>" style="width:70px;"  onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'startdate');" class="datepicker">
                                </td>
                                <td>
                                    <input type="text" name="enddate_<? echo $fabric_description_id.'_'.$i; ?>" id="enddate_<? echo $fabric_description_id.'_'.$i; ?>" value="<? // echo $row[csf("end_date")]; ?>" style="width:70px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'enddate');" class="datepicker">
                                </td>
                                <td>
                                    <?
                                    echo create_drop_down("uom_".$fabric_description_id."_".$i, 50, $unit_of_measurement,"", 1, "--Select--",$selected_uom,"copy_value(".$fabric_description_id.",".$i.",'uom')","",$row[("uom")]);
                                    ?>
                                </td>
								
								
                               
                                <td title="<? echo 'Req. Qty='.number_format($row[("program_qnty")],2, ".", "").', '.'Prev. Qty='.$wo_prev_qnty.', '.'Balance. Qty='.$bal_woqnty;?>">
                                    <input type="text" name="txt_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'txt_woqnty');calculate_amount(<? echo $fabric_description_id; ?>,<? echo $i; ?>)" value="<? echo number_format($bal_woqnty,2, ".", ""); ?>"/>
									 <input type="hidden" name="txt_reqqty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_reqqty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric" value="<? echo number_format($row[("program_qnty")],2, ".", "");?>" />
									 
									 <input type="hidden" name="txt_program_nos_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_program_nos_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric" value="<? echo $row[("program_no")];?>" />
									 
									 <input type="hidden" name="txt_hidden_bal_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_hidden_bal_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? echo number_format($bal_woqnty,2, ".", ""); ?>"/>
									
									  <input type="hidden" name="txt_prev_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_prev_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric" value="<? echo number_format($wo_prev_qnty,2, ".", "")?>" />
                                </td>
                                <td>
                                    <input type="text" name="txt_rate_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_rate_<? echo $fabric_description_id.'_'.$i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'txt_rate');calculate_amount(<? echo $fabric_description_id; ?>,<? echo $i; ?>)"  value="<? echo $rate; ?>" pre-cost-rate="<? echo $rate; ?>" <?php //echo $rate_disable; ?>>
                                </td>
                                <td>
                                    <input type="text" name="txt_amount_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_amount_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo $amount; ?>" disabled="disabled"/>
                                    <input type="hidden" name="txt_pre_amount_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_pre_amount_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo $amount; ?>" disabled="disabled"/>
                                    <input type="hidden" name="txt_priv_amount_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_priv_amount_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo number_format($wo_prev_amount,4,'.',''); ?>" disabled="disabled"/>
									<input type="hidden" name="hidd_bal_amount_<? echo $fabric_description_id.'_'.$i; ?>" id="hidd_bal_amount_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo $bal_amount; ?>" disabled="disabled"/>
                                </td>
                                <td>
                                    <input type="text" name="txt_paln_cut_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_paln_cut_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo  $row[("plan_cut_qnty")]; ?>" disabled>
                                </td>
                                <td><input type="text" name="txt_remark_dtls_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_remark_dtls_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="" ></td>
                            </tr>
                        <?
                        		$i++;
								}
                        	}
						}
                    }
                    else 
                    {
                        $i=1;
                        //print_r($dataArray);
                        foreach($fab_conv_detail_arr as $fab_id=>$fab_data)
                        {
							foreach($fab_data as $po_id=>$po_data)
							{
								foreach($po_data as $color_id=>$row)
								{
								//$woqnty="";
								if($sensitivity==1 || $sensitivity==3) // AS Per Garments/Contrast Color
								{
									if($fab_req_source==1) //Budget
									{
										$pre_req_qnty=array_sum($conversion_knit_qty_arr[$fabric_description_id][$po_id][$color_id]); //getQtyArray_by_ConversionidOrderColorAndUom
										 
									}
									else //Booking
									{
										$pre_req_qnty=$fab_req_qty_booking_arr[$po_id][$row[('fabric_dtl_id')]][$color_id]['grey_fab_qnty'];
										 
									}
									$wo_prev_qnty=$po_fab_prev_color_booking_arr[$po_id][$fabric_description_id][$color_id]['wo_qnty'];
									$wo_prev_amount=$po_fab_prev_color_booking_arr[$po_id][$fabric_description_id][$color_id]['amount'];
								}
							
							//echo $pre_req_qnty.'='.$fabric_description_id.'='.$row[csf("size_number_id")];
							// echo $woqnty.'='.$row[("body_part_id")].'='.$pre_req_qnty.'<br>';
							
						
                            if($row[("body_part_id")]==3)
                            {
                                $woqnty=$pre_req_qnty*2;
                               // $uom_item="1,2";
                               // $selected_uom="1";
							   //echo "1";
                            }
                            else if($row[("body_part_id")]==2)
                            {
                                $woqnty=$pre_req_qnty*1;
                               // $uom_item="1,2";
                               // $selected_uom="1";
							  // echo "2";
                            }
                            else if($row[("body_part_id")]!=2 || $row[("body_part_id")]!=3 )
                            {
                                $woqnty=$pre_req_qnty*1;
                               // $selected_uom="12";
							   //echo "3";
                            }
                            
                            if($row[("body_part_id")]==2 || $row[("body_part_id")]==3)
                            {
                                $rate="";
                                $amount="";	
								$woqnty=$pre_req_qnty*1;
								if($wo_prev_qnty=="") $wo_prev_qnty='';
								$bal_woqnty=$woqnty-$wo_prev_qnty;
								//echo "4";
                            }
                            else
                            {
								$bal_woqnty=$woqnty-$wo_prev_qnty;
                                $rate=$row[("charge_unit")];
                                $amount=$rate*$bal_woqnty;
								//echo "5";
                            }
							
                            if($rate_from_library==1)
                            {
                                $rate='';
                                $amount="";
                                $rate_disable="disabled";	
                            }
                            else
                            {
                                $fab_mapping_disable="disabled";
                            }
							
							$item_color="";
							$item_color_id="";
							
							if($sensitivity==3)
							{
								$item_colorID=$contrast_color_arr[$fabric_description_id][$color_id]['contrast_color'];
								
								if($item_colorID!=0)
								{
									$item_color=$color_library[$contrast_color_arr[$fabric_description_id][$color_id]['contrast_color']];
									$item_color_id=$contrast_color_arr[$fabric_description_id][$color_id]['contrast_color'];
								}
								else
								{
									$item_color=$color_library[$color_id];
									$item_color_id=$color_id;
								}
								
							}
							else if($sensitivity==1)
							{
								$item_color=$color_library[$color_id];
								$item_color_id=$color_id;
							}
							else 
							{
								$item_color="";
								$item_color_id="";
							}
							$dia_width=$fav_avg_color_arr[$fabric_description_id][$po_id][$color_id]['dia_width'];
							$gsm_weight=$fav_avg_color_arr[$fabric_description_id][$po_id][$color_id]['gsm_weight'];
							//echo $bal_woqnty.'<br>';	
							if($bal_woqnty>0)
							{

								$bal_amount=($woqnty*$rate)-$wo_prev_amount;
							//echo $woqnty.'='.$prev_wo_qnty.'='.$fabric_description_id;;//.'='.$fabric_description_id
                        ?>
                            <tr align="center">
							<td>
								<input type="text" name="txt_job_no_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_job_no_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[("job_no")]; ?>" style="width:90px;" class="text_boxes">
							</td>
                                <td>
                                    <?
                                        echo create_drop_down("po_no_".$fabric_description_id."_".$i, 100, $po_number,"", 1,'', $po_id,"",1);
                                    ?>
                                    <input type="hidden" name="po_id_<? echo $fabric_description_id.'_'.$i; ?>" id="po_id_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $po_id; ?>" style="width:110px;" class="text_boxes" disabled="disabled">
                                </td>
                                <td>
                                    <?
                                        echo create_drop_down("fabric_description_".$fabric_description_id."_".$i, 250, $fabric_description_array,"", 1,'', $fabric_description_id,"",1);
                                    ?>
                                    <input type="hidden" name="fabric_description_id_<? echo $fabric_description_id.'_'.$i; ?>" id="fabric_description_id_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $fabric_description_id; ?>" style="width:110px;" class="text_boxes" disabled="disabled">
                                </td>
                                
                                <td>
                                    <input type="text" name="artworkno_<? echo $fabric_description_id.'_'.$i; ?>" id="artworkno_<? echo $fabric_description_id.'_'.$i; ?>" value="<? //echo $fabric_description_id; ?>" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'artworkno');" class="text_boxes">
                                </td>
								<td>
                                    <input type="text" name="txt_ycount_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_ycount_<? echo $fabric_description_id.'_'.$i; ?>" value="<? //echo $fabric_description_id; ?>" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'ycount');" class="text_boxes">
                                </td>
								<td>
                                    <input type="text" name="txt_lot_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_lot_<? echo $fabric_description_id.'_'.$i; ?>" value="<? //echo $fabric_description_id; ?>" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'lotno');" class="text_boxes">
                                </td>
								<td>
                                    <input type="text" name="txt_brand_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_brand_<? echo $fabric_description_id.'_'.$i; ?>" value="<? //echo $fabric_description_id; ?>" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'brand');" class="text_boxes">
                                </td>
								   <td>
                                <input type="hidden" name="color_size_table_id_<? echo $fabric_description_id.'_'.$i; ?>" id="color_size_table_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<?  echo $row[("color_size_table_id")];?>" disabled="disabled"/>
                                
                                    <input type="text" name="gmts_color_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_color_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==1 || $sensitivity==3){ echo $color_library[$color_id];} else { echo "";}?>" disabled="disabled"/>
                                    <input type="hidden" name="gmts_color_id_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_color_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==1 || $sensitivity==3   ){ echo $color_id;} else { echo "";}?>"disabled="disabled"/>
                                </td>
                                <td>
                                    <input type="text" name="item_color_<? echo $fabric_description_id.'_'.$i; ?>" id="item_color_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes" onChange="copy_value()" value="<? echo $item_color; ?>"/>
                                    <input type="hidden" name="item_color_id_<? echo $fabric_description_id.'_'.$i; ?>" id="item_color_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? echo $item_color_id; ?>" disabled="disabled"/>
                                </td>
								 <td>
                                    <input type="text" name="item_size_<? echo $fabric_description_id.'_'.$i; ?>" id="item_size_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes" onChange="copy_value()" value="<? //echo $item_color; ?>"/>
                                   
                                </td>
                             
                                <td>
								 <input type="hidden" name="updateid_<? echo $fabric_description_id.'_'.$i; ?>" id="updateid_<? echo $fabric_description_id.'_'.$i; ?>" value="">
                                    <input type="text" name="subcon_supplier_compo_<? echo $fabric_description_id.'_'.$i; ?>" id="subcon_supplier_compo_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="" onDblClick="service_supplier_popup('<? echo $fabric_description_id.'_'.$i; ?>')" placeholder="Browse" <?php echo $fab_mapping_disable; ?>>
                                  
                                    <input type="hidden" name="subcon_supplier_rateid_<? echo $fabric_description_id.'_'.$i; ?>" id="subcon_supplier_rateid_<? echo $fabric_description_id.'_'.$i; ?>" value="">
                                </td>
								<td>
                                    <input type="text" name="txt_mcdia_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_mcdia_<? echo $fabric_description_id.'_'.$i; ?>" value="<? //echo $fabric_description_id; ?>" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'mcdia');" class="text_boxes">
                                </td>
								
								<td>
                                    <input type="text" name="txt_findia_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_findia_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $dia_width; ?>" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'findia');" class="text_boxes">
                                </td>
								<td>
                                    <input type="text" name="txt_fingsm_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_fingsm_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $gsm_weight; ?>" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'fingsm');" class="text_boxes">
                                </td>
								<td>
                                    <input type="text" name="txt_slength_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_slength_<? echo $fabric_description_id.'_'.$i; ?>" value="<? //echo $fabric_description_id; ?>" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'slength');" class="text_boxes">
                                </td>
								 <td>
                                    <input type="text" name="startdate_<? echo $fabric_description_id.'_'.$i; ?>" id="startdate_<? echo $fabric_description_id.'_'.$i; ?>" value="<? //echo $row[csf("start_date")]; ?>" style="width:70px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'startdate');"  class="datepicker">
                                </td>
                                <td>
                                    <input type="text" name="enddate_<? echo $fabric_description_id.'_'.$i; ?>" id="enddate_<? echo $fabric_description_id.'_'.$i; ?>" value="<? // echo $row[csf("end_date")]; ?>" style="width:70px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'enddate');" class="datepicker">
                                </td>
                                <td>
                                    <?
                                    echo create_drop_down("uom_".$fabric_description_id."_".$i, 50, $unit_of_measurement,"", 1, "--Select--",$row[("uom")],"copy_value(".$fabric_description_id.",".$i.",'uom')","",$row[("uom")]);
                                    ?>
                                </td>
								
								
                               
                                <td title="<? echo 'Req. Qty='.$woqnty.', Prev Wo Qty='.$wo_prev_qnty.', Balance. Qty='.$bal_woqnty;?>">
                                    <input type="text" name="txt_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'txt_woqnty');calculate_amount(<? echo $fabric_description_id; ?>,<? echo $i; ?>)" value="<? echo  number_format($bal_woqnty,2, ".", ""); ?>"/>
									<input type="hidden" name="txt_hidden_bal_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_hidden_bal_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'txt_woqnty');calculate_amount(<? echo $fabric_description_id; ?>,<? echo $i; ?>)" value="<? echo number_format($bal_woqnty,2, ".", ""); ?>"/>
									 <input type="hidden" name="txt_reqqty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_reqqty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric" value="<? echo number_format($woqnty,2, ".", ""); ?>"  />
									  <input type="hidden" name="txt_prev_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_prev_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric" value="<? echo number_format($wo_prev_qnty,2, ".", "")?>" />
									  
									   <input type="hidden" name="txt_program_nos_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_program_nos_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric" value="<? //echo $row[("program_no")];?>" />
									 
									 
                                </td>
                                <td>
                                    <input type="text" name="txt_rate_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_rate_<? echo $fabric_description_id.'_'.$i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'txt_rate');calculate_amount(<? echo $fabric_description_id; ?>,<? echo $i; ?>)" value="<? echo $rate; ?>" pre-cost-rate="<? echo $rate; ?>" <?php //echo $rate_disable; ?>>
                                </td>
                                <td>
                                    <input type="text" name="txt_amount_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_amount_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo $amount; ?>" disabled="disabled"/>
                                     <input type="hidden" name="txt_pre_amount_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_pre_amount_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo $amount; ?>" disabled="disabled"/>
                                      <input type="hidden" name="txt_priv_amount_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_priv_amount_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo number_format($wo_prev_amount,4,'.',''); ?>" disabled="disabled"/>
									  <input type="hidden" name="hidd_bal_amount_<? echo $fabric_description_id.'_'.$i; ?>" id="hidd_bal_amount_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo $bal_amount; ?>" disabled="disabled"/>
                                </td>
                                <td>
                                    <input type="text" name="txt_paln_cut_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_paln_cut_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo  $row[("plan_cut_qnty")]; ?>" disabled>
                                </td>
                                <td><input type="text" name="txt_remark_dtls_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_remark_dtls_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="" ></td>
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
			</div>
		<?
		
	exit();
}
if($action=="update_detail_booking_list_view")
{
	$data=explode("**",$data);  
    
	$job_po_id=$data[0];
	//$type=$data[1];
	$fabric_conv_id=$data[2];
	$process=$data[3];
	$sensitivity=$data[4];
	$txt_booking_no=$data[6];
	$dtls_id=implode(",",explode(",",$data[7]));
	$rate_from_library=$data[8];
    $programNo=$data[9];
	$company=$data[10];
	$short_type=$data[11];
	
	
	//echo $fabric_conv_id.'d';
	$fab_req_source=return_field_value("excut_source", "variable_order_tracking", "company_name=$company and variable_list=66 and status_active=1 and is_deleted=0");
	
	if($fab_req_source=="" || $fab_req_source==0) $fab_req_source=1;else $fab_req_source=$fab_req_source;
	//echo $fab_req_source.'XX'.$company;
	
	$fabric_description_array_empty=array();
	$fabric_description_array=array();
	$short_cond="";
	if($short_type==0)
	{
		$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select c.job_no,c.id,c.fabric_description,c.cons_process from wo_pre_cost_fab_conv_cost_dtls c,wo_po_break_down b where  c.job_id=b.job_id and c.job_no in('$job_po_id') group by c.job_no,  c.id,c.fabric_description,c.cons_process");
	}
	else
	{
		$short_cond=" and b.is_short=1";
	$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select c.job_no,a.id,a.fabric_description from wo_pre_cost_fabric_cost_dtls c,wo_po_break_down b,wo_booking_dtls d,
	wo_pre_cost_fab_conv_cost_dtls a where 
	 c.job_id=b.job_id and d.pre_cost_fabric_cost_dtls_id=c.id and d.pre_cost_fabric_cost_dtls_id=a.fabric_description and c.id=a.fabric_description and d.is_short=1 and d.booking_type=1  and b.id in($job_po_id) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.is_deleted=0 
	 and d.status_active=1 and d.is_deleted=0 group by c.job_no,a.id,a.fabric_description");
	}
	
	//echo "select c.job_no,c.id,c.fabric_description,c.cons_process from wo_pre_cost_fab_conv_cost_dtls c,wo_po_break_down b where  c.job_no=b.job_no_mst and c.job_no in('$job_po_id') group by c.job_no,  c.id,c.fabric_description,c.cons_process";
	foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id)
	{
		$fab_dtls_id_arr[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")];
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]!=0)
		{
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls  
			where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."'");
			list($fabric_description_row)=$fabric_description;
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")];
		}
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]==0)
		{			
			$fabric_description_string="";
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls  
			where  job_no='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("job_no")]."'");
			foreach( $fabric_description as $fabric_description_row)
	        {
			$fabric_description_string.=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")]." and ";
			}
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]].=rtrim($fabric_description_string,"and ");
		}
	}
	$wo_pre_cost_fab_co_color_sql=sql_select("select b.gmts_color_id,b.contrast_color_id,c.id as fab_dtls_id from wo_pre_cos_fab_co_color_dtls b,wo_pre_cost_fab_conv_cost_dtls c  where  c.job_id=b.job_id and c.id=$data[2] and b.pre_cost_fabric_cost_dtls_id=c.fabric_description");
	//echo "select b.gmts_color_id,b.contrast_color_id,c.id as fab_dtls_id from wo_pre_cos_fab_co_color_dtls b,wo_pre_cost_fab_conv_cost_dtls c  where  c.job_no=b.job_no and c.id=$data[2] and b.pre_cost_fabric_cost_dtls_id=c.fabric_description";
	foreach( $wo_pre_cost_fab_co_color_sql as $row)
	{
		$contrast_color_arr[$row[csf('fab_dtls_id')]][$row[csf('gmts_color_id')]]['contrast_color']=$row[csf('contrast_color_id')];
	}
	
	if($fab_req_source==1) //Budget
	{
		$condition= new condition();
		if(str_replace("'","",$job_po_id) !=''){
			$condition->job_no("in('$job_po_id')");
		}

		$condition->init();
		
		$conversion= new conversion($condition);
		$conversion_knit_qty_arr=$conversion->getQtyArray_by_ConversionidOrderColorAndUom();
		//print_r($conversion_knit_qty_arr);
		$conversion_color_size_knit_qty_arr=$conversion->getQtyArray_by_ConversionidOrderColorSizeidAndUom();
		$conversion_po_size_knit_qty_arr=$conversion->getQtyArray_by_ConversionidOrderSizeidAndUom();
	}
	else //Fab Booking
	{
		   $sql_data_fab="select a.id as fab_dtl_id,b.job_no,b.po_break_down_id as po_id,b.sensitivity,b.gmts_color_id,b.fabric_color_id,
		   sum(b.grey_fab_qnty) as grey_fab_qnty,sum(b.amount) as amount from  wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b 
		   where  b.pre_cost_fabric_cost_dtls_id=a.id and b.job_no=a.job_no and b.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		     and b.booking_type=1   and b.job_no in('$job_po_id')  $short_cond group by b.job_no,a.id,b.po_break_down_id,b.sensitivity,b.gmts_color_id,b.fabric_color_id";
		  $resultData_fab=sql_select($sql_data_fab);
		  	foreach($resultData_fab as $row)
			{
				$fab_req_qty_booking_arr[$row[csf('po_id')]][$row[csf('fab_dtl_id')]][$row[csf('gmts_color_id')]]['grey_fab_qnty']=$row[csf('grey_fab_qnty')];
			}
	}
		 if($programNo!='') $program_cond=" and b.program_no in($programNo)";else $program_cond="";
		 
		 $sql_data_Priv="select c.id as conv_dtl_id,b.job_no,b.po_break_down_id as po_id,b.sensitivity,b.uom,b.gmts_color_id,b.fabric_color_id,sum(b.wo_qnty) as wo_qnty,sum(b.amount) as amount,c.charge_unit from  wo_pre_cost_fab_conv_cost_dtls c,wo_booking_dtls b where  b.pre_cost_fabric_cost_dtls_id=c.id and b.job_no=c.job_no and b.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and b.booking_type=3 and b.entry_form_id=228 and  b.job_no in('$job_po_id') and c.id in($fabric_conv_id) and b.process=1 and c.is_deleted=0 and c.status_active=1 and b.is_deleted=0 and b.status_active=1 $program_cond group by b.job_no,c.id,b.po_break_down_id,b.sensitivity,b.uom,b.gmts_color_id,b.fabric_color_id,c.charge_unit";
	 
		$dataResultPre=sql_select($sql_data_Priv);
		$po_fab_prev_booking_arr=array();
		foreach($dataResultPre as $row)
		{
			if($row[csf('sensitivity')]==1 || $row[csf('sensitivity')]==3)// AS Per Garments/Contrast Color
			{
				$po_fab_prev_color_booking_arr[$row[csf('po_id')]][$row[csf('conv_dtl_id')]][$row[csf('gmts_color_id')]]['wo_qnty']=$row[csf('wo_qnty')];
				$po_fab_prev_color_booking_arr[$row[csf('po_id')]][$row[csf('conv_dtl_id')]][$row[csf('gmts_color_id')]]['amount']=$row[csf('amount')];
				$po_fab_prev_booking_arr2[$row[csf('conv_dtl_id')]]['wo_qty']=$row[csf('wo_qnty')];

				$po_fab_prev_color_booking_arr[$row[csf('po_id')]][$row[csf('conv_dtl_id')]][$row[csf('gmts_color_id')]]['charge_unit']=$row[csf('charge_unit')];
			}
			
		}
		
		if (($sensitivity == 1 || $sensitivity == 3) && $programNo!='')
        {
          $prog_sql = "SELECT a.id, a.mst_id as plan_id,a.dtls_id as program_no,a.booking_no,a.body_part_id,a.color_type_id,a.fabric_desc,a.gsm_weight,a.dia,a.program_qnty,a.yarn_desc,a.po_id as po_id, b.id as fabric_des_id,b.amount,b.job_no,b.charge_unit,min(d.id)as color_size_table_id,d.color_number_id,sum(d.plan_cut_qnty) as plan_cut_qnty
            FROM ppl_planning_entry_plan_dtls a,wo_pre_cost_fab_conv_cost_dtls b,ppl_planning_info_entry_dtls c, wo_po_color_size_breakdown d WHERE a.yarn_desc = b.fabric_description and a.dtls_id = c.id and b.cons_process=1 and a.dtls_id=$programNo and b.id in($fabric_conv_id) and a.po_id=d.po_break_down_id and d.is_deleted=0 and d.status_active=1 and b.status_active=1 and a.status_active=1 group by a.id,a.mst_id,a.dtls_id,a.booking_no,a.body_part_id,a.color_type_id,a.fabric_desc,a.gsm_weight,a.dia,a.program_qnty,a.yarn_desc,a.po_id , b.id,b.amount,b.charge_unit,b.job_no,d.color_number_id "; 
			$prog_dataArray=sql_select($prog_sql);
			foreach($prog_dataArray as $row)
			{
				$prog_req_qty_arr[$row[csf('fabric_des_id')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['program_qnty']=$row[csf('program_qnty')];
			}
        }
		
		
		 $booking_dtls_sql="select a.id,a.pre_cost_fabric_cost_dtls_id as fab_cost_dtls,a.artwork_no,a.po_break_down_id as po_id,a.color_size_table_id,a.fabric_color_id,a.item_size,a.process,a.program_no,a.slength,a.mc_dia,a.mc_gauge,a.fin_dia,a.remark,a.fin_gsm,a.yarn_count,a.lot_no,a.brand,a.sensitivity,a.job_no,booking_no,a.booking_type,a.description,a.uom,a.delivery_date,a.delivery_end_date,a.sensitivity,a.wo_qnty,a.rate,
		a.amount,b.size_number_id,b.color_number_id,a.lib_composition,a.lib_supplier_rate_id,b.color_number_id,b.plan_cut_qnty,a.delivery_end_date,a.delivery_date
		from wo_booking_dtls a, wo_po_color_size_breakdown b where a.job_no=b.job_no_mst and
		a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.id  and a.booking_type=3 and a.process=1 and
		a.booking_no='$txt_booking_no' and a.id in ($dtls_id) and a.status_active=1 and a.pre_cost_fabric_cost_dtls_id=$data[2] and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 ";
		$dtls_dataArray=sql_select($booking_dtls_sql);
		   
		foreach($dtls_dataArray as $row)
		{//fab_cost_dtls
			$fabric_desc=$fabric_description_array[$row[csf('fab_cost_dtls')]];
			$fab_booking_arr[$row[csf('fab_cost_dtls')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['color_size_table_id']=$row[csf('color_size_table_id')];
			$fab_booking_arr[$row[csf('fab_cost_dtls')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['plan_cut_qnty']=$row[csf('plan_cut_qnty')];
			$fab_booking_arr[$row[csf('fab_cost_dtls')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['charge_unit']=$row[csf('charge_unit')];
			$fab_booking_arr[$row[csf('fab_cost_dtls')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['costing_per']=$row[csf('costing_per')];
			$fab_booking_arr[$row[csf('fab_cost_dtls')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['po_number']=$row[csf('po_number')];
			$fab_booking_arr[$row[csf('fab_cost_dtls')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['fabric_description']=$fabric_desc;
			$fab_booking_arr[$row[csf('fab_cost_dtls')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['body_part_id']=$row[csf('body_part_id')];
			$fab_booking_arr[$row[csf('fab_cost_dtls')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['job_no']=$row[csf('job_no')];
			$fab_booking_arr[$row[csf('fab_cost_dtls')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['delivery_end_date']=$row[csf('delivery_end_date')];
			$fab_booking_arr[$row[csf('fab_cost_dtls')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['delivery_date']=$row[csf('delivery_date')];
			$fab_booking_arr[$row[csf('fab_cost_dtls')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['wo_qnty']+=$row[csf('wo_qnty')];
			$fab_booking_arr[$row[csf('fab_cost_dtls')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['program_no']=$row[csf('program_no')];
			$fab_booking_arr[$row[csf('fab_cost_dtls')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['rate']=$row[csf('rate')];
			$fab_booking_arr[$row[csf('fab_cost_dtls')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['amount']+=$row[csf('amount')];
			$fab_booking_arr[$row[csf('fab_cost_dtls')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['artwork_no']=$row[csf('artwork_no')];
			$fab_booking_arr[$row[csf('fab_cost_dtls')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['plan_cut_qnty']+=$row[csf('plan_cut_qnty')];
			$fab_booking_arr[$row[csf('fab_cost_dtls')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['dtls_id']=$row[csf('id')];
			$fab_booking_arr[$row[csf('fab_cost_dtls')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['uom']=$row[csf('uom')];
			
			$fab_booking_arr[$row[csf('fab_cost_dtls')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['slength']=$row[csf('slength')];
			$fab_booking_arr[$row[csf('fab_cost_dtls')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['mc_dia']=$row[csf('mc_dia')];
			$fab_booking_arr[$row[csf('fab_cost_dtls')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['mc_gauge']=$row[csf('mc_gauge')];
			$fab_booking_arr[$row[csf('fab_cost_dtls')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['fin_dia']=$row[csf('fin_dia')];
			$fab_booking_arr[$row[csf('fab_cost_dtls')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['fin_gsm']=$row[csf('fin_gsm')];
			$fab_booking_arr[$row[csf('fab_cost_dtls')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['yarn_count']=$row[csf('yarn_count')];
			$fab_booking_arr[$row[csf('fab_cost_dtls')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['lot_no']=$row[csf('lot_no')];
			$fab_booking_arr[$row[csf('fab_cost_dtls')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['brand']=$row[csf('brand')];
			$fab_booking_arr[$row[csf('fab_cost_dtls')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['item_size']=$row[csf('item_size')];
			$fab_booking_arr[$row[csf('fab_cost_dtls')]][$row[csf('po_id')]][$row[csf('color_number_id')]]['remark']=$row[csf('remark')];
			
		}
		?>
			<div id="content_search_panel_<? echo $data[2]; ?>" style="" class="accord_close2">
            
				<table class="rpt_table" border="1" width="1280" cellpadding="0" cellspacing="0" rules="all" id="table_<? echo $data[2]; ?>">
					<thead>
						<th>Job No </th>
						<th>Po Number </th>
						<th title="Fab. Req. Source=<? echo $fab_req_source;?>">Fabric Description</th>
                        <th>Artwork No</th>
						
						<th>Y.Count</th>
						<th>Lot</th>
						<th>Brand</th>
						
						<th>Gmts. Color</th>
						<th>Item Color</th>
						<th>Item Size</th>
						
                        <th>Fab. Mapping</th>
						<th>M/C DiaXGG</th>
						
						<th>Fin Dia</th>
						<th>Fin GSM</th>
						<th>S.length</th>
						
						<th>Delivery Start Date</th>
                        <th>Delivery End Date</th>
                        <th>UOM</th>
						
                        <th>WO. Qnty</th>
                        <th>Rate</th>
                        <th>Amount</th>
                        <th>Plan Cut Qnty</th>
						<th>Remark</th>
					</thead>
					<tbody>
					<?
					
                        $i=1;
                        foreach($fab_booking_arr as $fabric_description_id=>$fab_data)
                        {
							foreach($fab_data as $po_id=>$po_data)
							{
								foreach($po_data as $color_id=>$row)
								{
								//$woqnty="";
								$fab_dtls_id=$fab_dtls_id_arr[$fabric_description_id];
								if($programNo!='' && ($sensitivity==1 || $sensitivity==3)) // AS Per Garments/Contrast Color
								{
									//echo $programNo.'ddd';
									$pre_req_qnty=$prog_req_qty_arr[$fabric_description_id][$po_id][$color_id]['program_qnty'];
									$wo_prev_qnty=$po_fab_prev_color_booking_arr[$po_id][$fabric_description_id][$color_id]['wo_qnty'];
									$wo_prev_amount=$po_fab_prev_color_booking_arr[$po_id][$fabric_description_id][$color_id]['amount'];
									$budgetRate=$po_fab_prev_color_booking_arr[$po_id][$fabric_description_id][$color_id]['charge_unit'];
								}
								else
								{
									if($fab_req_source==1) //Budget
									{
										$pre_req_qnty=array_sum($conversion_knit_qty_arr[$fabric_description_id][$po_id][$color_id]);
										$budgetRate=$po_fab_prev_color_booking_arr[$po_id][$fabric_description_id][$color_id]['charge_unit'];
									}
									else
									{
										$pre_req_qnty=$fab_req_qty_booking_arr[$po_id][$fab_dtls_id][$color_id]['grey_fab_qnty'];
										//echo $pre_req_qnty.'dd';
									}
									$wo_prev_qnty=$po_fab_prev_color_booking_arr[$po_id][$fabric_description_id][$color_id]['wo_qnty'];
									$wo_prev_amount=$po_fab_prev_color_booking_arr[$po_id][$fabric_description_id][$color_id]['amount'];
								}
							//echo $wo_prev_qnty.'='.$fabric_description_id;
							//echo $wo_prev_qnty.'='.$fabric_description_id;
						
                          							
                            if($rate_from_library==1)
                            {
                                $rate='';
                                $amount="";
                                $rate_disable="disabled";	
                            }
                            else
                            {
                                $fab_mapping_disable="disabled";
                            }
							
							$item_color="";
							$item_color_id="";
							if($sensitivity==3)
							{
								
								$item_color_id=$contrast_color_arr[$fabric_description_id][$color_id]['contrast_color'];
								if($item_color_id!=0)
								{
									//$item_color_id=$contrast_color_arr[$fabric_description_id][$color_id]['contrast_color'];
									$item_color=$color_library[$contrast_color_arr[$fabric_description_id][$color_id]['contrast_color']];
								}
								else
								{
									$item_color=$color_library[$color_id];
									$item_color_id=$color_id;
								}
							}
							else if($sensitivity==1)
							{
								$item_color=$color_library[$color_id];
								$item_color_id=$color_id;
							}
							else 
							{
								$item_color="";
								$item_color_id="";
							}
							//echo $sensitivity.'='.$item_color_id;
							$woqnty=$row["wo_qnty"];
							$amount=$row["amount"];
							$rate=$row["rate"];
							$bal_wo_qty=$pre_req_qnty-$wo_prev_qnty;
							//echo $woqnty.'='.$prev_wo_qnty.'='.$fabric_description_id;;//.'='.$fabric_description_id
							$bal_amount=($pre_req_qnty*$rate)-$wo_prev_amount;
                        ?>
                            <tr align="center">
							<td>
								<input type="text" name="txt_job_no_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_job_no_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[("job_no")]; ?>" style="width:90px;" class="text_boxes">
							</td>
                                <td>
                                    <?
                                        echo create_drop_down("po_no_".$fabric_description_id."_".$i, 100, $po_number,"", 1,'', $po_id,"",1);
                                    ?>
                                    <input type="hidden" name="po_id_<? echo $fabric_description_id.'_'.$i; ?>" id="po_id_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $po_id; ?>" style="width:110px;" class="text_boxes" disabled="disabled">
                                </td>
                                <td>
                                    <?
                                        echo create_drop_down("fabric_description_".$fabric_description_id."_".$i, 250, $fabric_description_array,"", 1,'', $fabric_description_id,"",1);
                                    ?>
                                    <input type="hidden" name="fabric_description_id_<? echo $fabric_description_id.'_'.$i; ?>" id="fabric_description_id_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $fabric_description_id; ?>" style="width:110px;" class="text_boxes" disabled="disabled">
                                </td>
                                
                                <td>
                                    <input type="text" name="artworkno_<? echo $fabric_description_id.'_'.$i; ?>" id="artworkno_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[("artwork_no")]; ?>" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'artworkno');" class="text_boxes">
                                </td>
								<td>
                                    <input type="text" name="txt_ycount_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_ycount_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[("yarn_count")]; ?>" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'ycount');" class="text_boxes">
                                </td>
								<td>
                                    <input type="text" name="txt_lot_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_lot_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[("lot_no")]; ?>" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'lotno');" class="text_boxes">
                                </td>
								<td>
                                    <input type="text" name="txt_brand_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_brand_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[("brand")]; ?>" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'brand');" class="text_boxes">
                                </td>
								<td>
                                	<input type="hidden" name="color_size_table_id_<? echo $fabric_description_id.'_'.$i; ?>" id="color_size_table_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<?  echo $row[("color_size_table_id")];?>" disabled="disabled"/>
                                    <input type="text" name="gmts_color_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_color_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==1 || $sensitivity==3){ echo $color_library[$color_id];} else { echo "";}?>" disabled="disabled"/>
                                    <input type="hidden" name="gmts_color_id_<? echo $fabric_description_id.'_'.$i; ?>" id="gmts_color_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? if($sensitivity==1 || $sensitivity==3   ){ echo $color_id;} else { echo "";}?>"disabled="disabled"/>
                                </td>
                                <td>
                                    <input type="text" name="item_color_<? echo $fabric_description_id.'_'.$i; ?>" id="item_color_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes" onChange="copy_value()" value="<? echo $item_color; ?>"/>
                                    <input type="hidden" name="item_color_id_<? echo $fabric_description_id.'_'.$i; ?>" id="item_color_id_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? echo $item_color_id; ?>" disabled="disabled"/>
                                </td>
								 <td>
                                    <input type="text" name="item_size_<? echo $fabric_description_id.'_'.$i; ?>" id="item_size_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes" onChange="copy_value()" value="<? echo $row[("item_size")]; ?>"/>
                                   
                                </td>

                                <td>
								 <input type="hidden" name="updateid_<? echo $fabric_description_id.'_'.$i; ?>" id="updateid_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[("dtls_id")]?>">
                                    <input type="text" name="subcon_supplier_compo_<? echo $fabric_description_id.'_'.$i; ?>" id="subcon_supplier_compo_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="" onDblClick="service_supplier_popup('<? echo $fabric_description_id.'_'.$i; ?>')" placeholder="Browse" <?php echo $fab_mapping_disable; ?>>
                                  
                                    <input type="hidden" name="subcon_supplier_rateid_<? echo $fabric_description_id.'_'.$i; ?>" id="subcon_supplier_rateid_<? echo $fabric_description_id.'_'.$i; ?>" value="">
                                </td>
								<td>
                                    <input type="text" name="txt_mcdia_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_mcdia_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[("mc_dia")]; ?>" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'mcdia');" class="text_boxes">
                                </td>
								
								<td>
                                    <input type="text" name="txt_findia_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_findia_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[("fin_dia")]; ?>" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'findia');" class="text_boxes">
                                </td>
								<td>
                                    <input type="text" name="txt_fingsm_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_fingsm_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[("fin_gsm")]; ?>" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'fingsm');" class="text_boxes">
                                </td>
								<td>
                                    <input type="text" name="txt_slength_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_slength_<? echo $fabric_description_id.'_'.$i; ?>" value="<?  echo $row[("slength")];?>" style="width:80px;" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'slength');" class="text_boxes">
                                </td>
								
								  <td>
                                    <input type="text" name="startdate_<? echo $fabric_description_id.'_'.$i; ?>" id="startdate_<? echo $fabric_description_id.'_'.$i; ?>"  value="<? echo change_date_format($row[("delivery_date")],"dd-mm-yyyy","-"); ?>" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'startdate');" style="width:70px;" class="datepicker">
                                </td>
                                <td>
                                    <input type="text" name="enddate_<? echo $fabric_description_id.'_'.$i; ?>" id="enddate_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo change_date_format($row[("delivery_end_date")],"dd-mm-yyyy","-"); ?>" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'enddate');" style="width:70px;" class="datepicker">
                                </td>
                                <td>
                                    <?
                                    echo create_drop_down("uom_".$fabric_description_id."_".$i, 50, $unit_of_measurement,"", 1, "--Select--",$row[("uom")],"copy_value(".$fabric_description_id.",".$i.",'uom')","",$row[("uom")]);
                                    ?>
                                </td> 

                                <td title="<? echo 'Req. Qty='.$pre_req_qnty.', Balance Wo Qty='.$bal_wo_qty;?>">
                                    <input type="text" name="txt_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'txt_woqnty');calculate_amount(<? echo $fabric_description_id; ?>,<? echo $i; ?>)" value="<? echo number_format($woqnty,2, ".", ""); ?>"/>
									<input type="hidden" name="txt_hidden_bal_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_hidden_bal_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:60px;" class="text_boxes_numeric"  value="<? echo number_format($bal_wo_qty,2, ".", ""); ?>"/>
									 <input type="hidden" name="txt_reqqty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_reqqty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric" value="<? echo number_format($pre_req_qnty,2, ".", "");?>" />
									  <input type="hidden" name="txt_prev_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_prev_woqnty_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric" value="<? echo number_format($wo_prev_qnty,2, ".", "")?>" />
									  <input type="hidden" name="txt_program_nos_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_program_nos_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric" value="<? echo $row[("program_no")];?>" />

                                </td>
                                <td title="<?=$budgetRate;?>">
                                    <input type="text" name="txt_rate_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_rate_<? echo $fabric_description_id.'_'.$i; ?>" style="width:60px;" class="text_boxes_numeric" onChange="copy_value(<? echo $fabric_description_id; ?>,<? echo $i; ?>,'txt_rate');calculate_amount(<? echo $fabric_description_id; ?>,<? echo $i; ?>)" pre-cost-rate="<? echo $rate; ?>" value="<? echo $rate; ?>" <?php //echo $rate_disable; ?>>
									<input type="hidden" name="txt_rate_<? echo $fabric_description_id.'_'.$i; ?>" id="pre_cost_rate_<? echo $fabric_description_id.'_'.$i; ?>" style="width:60px;" class="text_boxes_numeric" value="<? echo $budgetRate; ?>" <?php //echo $rate_disable; ?>>
                                </td>
                                <td>
                                    <input type="text" name="txt_amount_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_amount_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo $amount; ?>" disabled="disabled"/>
                                    <input type="hidden" name="txt_pre_amount_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_pre_amount_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo $amount; ?>" disabled="disabled"/>
                                     <input type="hidden" name="txt_priv_amount_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_priv_amount_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo number_format($wo_prev_amount,4,'.',''); ?>" disabled="disabled"/>
									 <input type="hidden" name="hidd_bal_amount_<? echo $fabric_description_id.'_'.$i; ?>" id="hidd_bal_amount_<? echo $fabric_description_id.'_'.$i; ?>" value="<?=$bal_amount; ?>" />
                                </td>
                                <td>
                                    <input type="text" name="txt_paln_cut_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_paln_cut_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes_numeric"  value="<? echo  $row[("plan_cut_qnty")]; ?>" disabled>
                                </td>
                                <td><input type="text" name="txt_remark_dtls_<? echo $fabric_description_id.'_'.$i; ?>" id="txt_remark_dtls_<? echo $fabric_description_id.'_'.$i; ?>" style="width:70px;" class="text_boxes"  value="<? echo  $row[("remark")]; ?>" ></td>
                            </tr>
                        <?	
                        $i++;
							 
						    }
						   }
						 }
                         
                   
					?>
					</tbody>
				</table>
			</div>
		<?
		
		exit();
}
if ($action=="fabric_detls_list_view")
{
	$data=explode("**",$data);
	
	$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select  c.job_no,c.id,c.fabric_description,c.cons_process from wo_booking_dtls b,wo_pre_cost_fab_conv_cost_dtls c  where c.id=b.pre_cost_fabric_cost_dtls_id and b.booking_no='$data[1]' group by c.job_no,c.id,c.fabric_description,c.cons_process");
	//echo "select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where job_no='$data[0]' ";
	foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id)
	{
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]!=0)
		{
			
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls 
			where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."'");
			list($fabric_description_row)=$fabric_description;
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$body_part[$fabric_description_row[csf("body_part_id")]].', 
			'.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")];
		}
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]==0)
		{
			
			$fabric_description_string="";
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls 
			where  job_no=".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf('job_no')]." ");
			foreach( $fabric_description as $fabric_description_row)
	        {
			$fabric_description_string.=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")]." and ";
			}
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]].=rtrim($fabric_description_string,"and ");
		}
	}
	
	if($db_type==0) { $group_concat="group_concat(b.po_break_down_id) as order_id"; $group_concat.=",group_concat(b.id) as dtls_id";}
	if($db_type==2)
	 { $group_concat="listagg(cast(b.po_break_down_id as varchar2(4000)),',') within group (order by b.po_break_down_id) as order_id";
	   $group_concat.=",listagg(cast(b.id as varchar2(4000)),',') within group (order by b.id) as dtls_id";
	}
	$sql="select a.id, b.job_no, b.booking_no, $group_concat, b.dia_width, b.pre_cost_fabric_cost_dtls_id, sum(b.amount) as amount, b.process, b.sensitivity,
	sum(b.wo_qnty) as wo_qnty, b.insert_date,b.program_no from wo_booking_dtls b, wo_booking_mst a
  	where b.booking_no=a.booking_no and a.booking_no='$data[1]' and a.entry_form=228 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 
	and b.process=1
  	group by b.job_no,a.id, b.dia_width, b.pre_cost_fabric_cost_dtls_id,b.process,b.sensitivity,b.booking_no,b.insert_date,b.program_no";
	//echo $sql;
		?>
    <div id="" style="" class="accord_close">
    
        <table class="rpt_table" border="1" width="1100" cellpadding="0" cellspacing="0" rules="all" id="">
            <thead>
                <th width="50px">Sl</th>
                <th width="300px">Fabric Description</th>
                <th width="100px">Job No</th>
                <th width="100px">Booking No</th>
                <th width="200px">Po Number</th>
                <th width="100px">Process </th>
                <th width="120px">Sensitivity</th>
                <th width="80px">WO. Qnty</th>
                <th width="80px">Amount</th>
                <th></th>
            </thead>
            <tbody>
            <?
            $dataArray=sql_select($sql);
        
            $i=1;
            foreach($dataArray as $row)
            {
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$allorder='';
				$all_po_number=explode(",",$row[csf('order_id')]);
				foreach($all_po_number as $po_id)
				{
				if($allorder!="") 	$allorder.=",".$po_number[$po_id];
				else 				$allorder=$po_number[$po_id];
					
				}  
				$all_po_nos=implode(",",array_unique(explode(",",$allorder)));  
            ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='update_booking_data("<? echo $row[csf("dtls_id")]."_".$row[csf("job_no")]."_".$row[csf("pre_cost_fabric_cost_dtls_id")]."_".$row[csf("process")]."_".$row[csf("sensitivity")]."_".$row[csf("order_id")]."_".$row[csf("booking_no")]."_".$row[csf("dia_width")]."_".$row[csf("program_no")]."_".$all_po_nos;?>","child_form_input_data","requires/chemical_dyes_receive_controller")' style="cursor:pointer" >
                    <td> <? echo $i; ?>
                        <input type="hidden" name="po_id_<? echo $fabric_description_id.'_'.$i; ?>" id="po_id_<? echo $fabric_description_id.'_'.$i; ?>" value="<? echo $row[csf("po_break_down_id")]; ?>" style="width:110px;" class="text_boxes" disabled="disabled">
                    </td>
                    <td><p><? echo  $fabric_description_array[$row[csf('pre_cost_fabric_cost_dtls_id')]]; ?></p> </td>
                    <td>	<? echo  $row[csf('job_no')]; ?></td>
                    <td>	<? echo  $row[csf('booking_no')]; ?></td>
                    <td>	<p><? echo  $all_po_nos; ?></p></td>
                    <td>	<? echo  $conversion_cost_head_array[$row[csf('process')]]; ?></td>
                    <td>	<? echo  $size_color_sensitive[$row[csf('sensitivity')]]; ?></td>
                    <td align="right">	<? echo number_format($row[csf('wo_qnty')],4); ?></td>
                    <td align="right"><? echo  number_format($row[csf('amount')],4); ?></td>
                    <td></td>
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




if ($action=="save_update_delete")
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
		$response_booking_no="";
		$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'KSB', date("Y",time()), 5,"select id, booking_no_prefix, booking_no_prefix_num from wo_booking_mst where company_id=$cbo_company_name and entry_form=228 and booking_type=3 and to_char(insert_date,'YYYY')=".date('Y',time())." order by id desc ", "booking_no_prefix", "booking_no_prefix_num" ));
		
		$id=return_next_id( "id", "wo_booking_mst", 1 ) ;
		$field_array="id, booking_type,is_short, booking_month, booking_year, booking_no_prefix, booking_no_prefix_num, booking_no, company_id, buyer_id, entry_form, item_category, supplier_id, currency_id, exchange_rate, booking_date, delivery_date, pay_mode, source, attention, tenor, ready_to_approved, process, remarks, delivery_to, inserted_by, insert_date, status_active, is_deleted";//
		$data_array ="(".$id.",3,".$cbo_short_type.",".$cbo_booking_month.",".$cbo_booking_year.",'".$new_booking_no[1]."',".$new_booking_no[2].",'".$new_booking_no[0]."',".$cbo_company_name.",".$cbo_buyer_name.",228,12,".$cbo_supplier_name.",".$cbo_currency.",".$txt_exchange_rate.",".$txt_booking_date.",".$txt_delivery_date.",".$cbo_pay_mode.",".$cbo_source.",".$txt_attention.",".$txt_tenor.",".$cbo_ready_to_approved.",".$cbo_process.",".$txt_remark.",".$txt_delivery_to.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		$response_booking_no=$new_booking_no[0];
		//echo "insert into wo_booking_mst($field_array)values".$data_array;die;
		$rID=sql_insert("wo_booking_mst",$field_array,$data_array,0);
		check_table_status( $_SESSION['menu_id'],0); 
			
		if($db_type==0)
		{
			if($rID){
				mysql_query("COMMIT");  
				echo "0**".$response_booking_no."**".$id;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$response_booking_no."**".$id;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID){
				oci_commit($con);  
				echo "0**".$response_booking_no."**".$id;
			}
			else{
				oci_rollback($con);  
				echo "10**".$response_booking_no."**".$id;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{
		 $con = connect();
		 if($db_type==0)
		 {
			mysql_query("BEGIN");
		 }
		$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_booking_no");
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			 disconnect($con);die;
		}
		 
		 $field_array_up="booking_type*is_short*booking_month*booking_year*buyer_id*item_category*supplier_id*currency_id*exchange_rate*booking_date*delivery_date*pay_mode*source*attention*tenor*ready_to_approved*remarks*delivery_to*updated_by*update_date";
		 $data_array_up ="3*".$cbo_short_type."*".$cbo_booking_month."*".$cbo_booking_year."*".$cbo_buyer_name."*12*".$cbo_supplier_name."*".$cbo_currency."*".$txt_exchange_rate."*".$txt_booking_date."*".$txt_delivery_date."*".$cbo_pay_mode."*".$cbo_source."*".$txt_attention."*".$txt_tenor."*".$cbo_ready_to_approved."*".$txt_remark."*".$txt_delivery_to."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		 //=======================================================================================================
		 $rID=sql_update("wo_booking_mst",$field_array_up,$data_array_up,"booking_no","".$txt_booking_no."",0);
		if($db_type==0)
		{
			if($rID){
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		if($db_type==2 || $db_type==1 )
		{
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
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_booking_no");
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			 disconnect($con);die;
		}
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID=sql_delete("wo_booking_mst",$field_array,$data_array,"booking_no","".$txt_booking_no."",1);
		$rID1=execute_query( "update wo_booking_dtls set status_active=0,is_deleted=1,updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."'   where  booking_no=$txt_booking_no",0);
		if($db_type==0)
		{
			if($rID){
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
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
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_booking_no");
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			 disconnect($con);die;
		}
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0";  disconnect($con);die;}		
		 $id_dtls=return_next_id( "id", "wo_booking_dtls", 1 ) ;
		 $field_array1="id,booking_mst_id, pre_cost_fabric_cost_dtls_id,entry_form_id, artwork_no,slength,item_size,mc_dia,po_break_down_id, color_size_table_id, job_no, booking_no, booking_type, fabric_color_id, gmts_color_id, description, dia_width, uom, process, sensitivity, wo_qnty, rate, amount,delivery_date, delivery_end_date,lib_composition,lib_supplier_rate_id,inserted_by, insert_date,program_no,fin_dia,fin_gsm,yarn_count,lot_no,brand,remark";
		 $new_array_color=array();
		// echo "10**jahid##$row_num";die;
		 for ($i=1;$i<=$row_num;$i++)
		 {
			 $po_id="po_id_".$hide_fabric_description."_".$i;
			 $txt_job_no="txt_job_no_".$hide_fabric_description."_".$i;
			 $fabric_description_id="fabric_description_id_".$hide_fabric_description."_".$i;
			 $artworkno="artworkno_".$hide_fabric_description."_".$i;
			 
			 $txt_mcdia="txt_mcdia_".$hide_fabric_description."_".$i;
			// $txt_gg="txt_gg_".$hide_fabric_description."_".$i;
			 $txt_slength="txt_slength_".$hide_fabric_description."_".$i;
			 $item_size="item_size_".$hide_fabric_description."_".$i;
			 
			// $txt_findia="txt_gg_".$txt_findia."_".$i;
			 $txt_findia="txt_findia_".$hide_fabric_description."_".$i;
			 $txt_fingsm="txt_fingsm_".$hide_fabric_description."_".$i;
			 $txt_ycount="txt_ycount_".$hide_fabric_description."_".$i;
			 $txt_lot="txt_lot_".$hide_fabric_description."_".$i;
			 $txt_brand="txt_brand_".$hide_fabric_description."_".$i;
			
			 
			 
				
             $color_size_table_id="color_size_table_id_".$hide_fabric_description."_".$i;			 
			 $gmts_color_id="gmts_color_id_".$hide_fabric_description."_".$i;
			 $item_color_id="item_color_id_".$hide_fabric_description."_".$i;
			 $item_color="item_color_".$hide_fabric_description."_".$i;
			 $txt_program_nos="txt_program_nos_".$hide_fabric_description."_".$i;
			
			 $uom="uom_".$hide_fabric_description."_".$i;
			 $txt_woqnty="txt_woqnty_".$hide_fabric_description."_".$i;
			 $txt_rate="txt_rate_".$hide_fabric_description."_".$i;
			 $txt_amount="txt_amount_".$hide_fabric_description."_".$i;
			 $txt_paln_cut="txt_paln_cut".$hide_fabric_description."_".$i;
			 $txt_remark_dtls="txt_remark_dtls_".$hide_fabric_description."_".$i;
			 $updateid="updateid_".$hide_fabric_description."_".$i;
			 $startdate="startdate_".$hide_fabric_description."_".$i;
			 $enddate="enddate_".$hide_fabric_description."_".$i;
			 $lib_composition="subcon_supplier_compo_".$hide_fabric_description."_".$i;
			 $lib_supplier_rateId="subcon_supplier_rateid_".$hide_fabric_description."_".$i;
			 $txt_program_no="txt_program_nos_".$hide_fabric_description."_".$i;
			 
			 if ($i!=1) $data_array1 .=",";
			 $data_array1 .="(".$id_dtls.",".$update_id.",".$$fabric_description_id.",228,".$$artworkno.",".$$txt_slength.",".$$item_size.",".$$txt_mcdia.",".$$po_id.",".$$color_size_table_id.",".$$txt_job_no.",".$txt_booking_no.",3,".$$item_color_id.",".$$gmts_color_id.",".$$fabric_description_id.",".$cbo_dia.",".$$uom.",".$cbo_process.",".$cbo_colorsizesensitive.",".$$txt_woqnty.",".$$txt_rate.",".$$txt_amount.",".$$startdate.",".$$enddate.",".$$lib_composition.",".$$lib_supplier_rateId.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$txt_program_nos.",".$$txt_findia.",".$$txt_fingsm.",".$$txt_ycount.",".$$txt_lot.",".$$txt_brand.",".$$txt_remark_dtls.")";
		     $id_dtls=$id_dtls+1;
		 }
		//echo "10** insert into wo_booking_dtls ($field_array1) values $data_array1";die; 
		$rID=sql_insert("wo_booking_dtls",$field_array1,$data_array1,0);
		check_table_status( $_SESSION['menu_id'],0);   
		
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'","",$$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);  
				echo "0**".str_replace("'","",$$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
			}
			else
			{
				oci_rollback($con);  
				echo "10**".str_replace("'","",$$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive)."** insert into wo_booking_dtls ($field_array1) values $data_array1";
			}
		}
		disconnect($con);
		die;
	}	
	else if ($operation==1)   // Update Here
	{
		 $con = connect();
		 if($db_type==0)
		 {
			mysql_query("BEGIN");
		 }
		 $is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_booking_no");
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			 disconnect($con);die;
		}
		 $field_array_up1="pre_cost_fabric_cost_dtls_id*artwork_no*mc_dia*slength*item_size*po_break_down_id*color_size_table_id*job_no*booking_no*booking_type*fabric_color_id
*gmts_color_id*description*dia_width*uom*process*sensitivity*wo_qnty*rate*amount*delivery_date*delivery_end_date*lib_composition*lib_supplier_rate_id*updated_by*update_date*program_no*fin_dia*fin_gsm*yarn_count*lot_no*brand*remark";
		 $new_array_color=array();
		 for ($i=1;$i<=$row_num;$i++)
		 {
			 $po_id="po_id_".$hide_fabric_description."_".$i;
			 $txt_job_no="txt_job_no_".$hide_fabric_description."_".$i;
			 $fabric_description_id="fabric_description_id_".$hide_fabric_description."_".$i;
			 $artworkno="artworkno_".$hide_fabric_description."_".$i;
			 $txt_mcdia="txt_mcdia_".$hide_fabric_description."_".$i;
			// $txt_gg="txt_gg_".$hide_fabric_description."_".$i;
			 $txt_slength="txt_slength_".$hide_fabric_description."_".$i;
			  $item_size="item_size_".$hide_fabric_description."_".$i;
			 $txt_findia="txt_findia_".$hide_fabric_description."_".$i;
			 $txt_fingsm="txt_fingsm_".$hide_fabric_description."_".$i;
			 $txt_ycount="txt_ycount_".$hide_fabric_description."_".$i;
			 $txt_lot="txt_lot_".$hide_fabric_description."_".$i;
			 $txt_brand="txt_brand_".$hide_fabric_description."_".$i;
			 $txt_remark_dtls="txt_remark_dtls_".$hide_fabric_description."_".$i;
			 
			 
             $color_size_table_id="color_size_table_id_".$hide_fabric_description."_".$i;			 
			 $gmts_color_id="gmts_color_id_".$hide_fabric_description."_".$i;
			 $item_color_id="item_color_id_".$hide_fabric_description."_".$i;
			 $item_color="item_color_".$hide_fabric_description."_".$i;
			 $txt_program_nos="txt_program_nos_".$hide_fabric_description."_".$i;
			 $uom="uom_".$hide_fabric_description."_".$i;
			 $txt_woqnty="txt_woqnty_".$hide_fabric_description."_".$i;
			 $txt_rate="txt_rate_".$hide_fabric_description."_".$i;
			 $txt_amount="txt_amount_".$hide_fabric_description."_".$i;
			 $txt_paln_cut="txt_paln_cut".$hide_fabric_description."_".$i;
			 $updateid="updateid_".$hide_fabric_description."_".$i;
			 $startdate="startdate_".$hide_fabric_description."_".$i;
			 $enddate="enddate_".$hide_fabric_description."_".$i;
			 $lib_composition="subcon_supplier_compo_".$hide_fabric_description."_".$i;
			 $lib_supplier_rateId="subcon_supplier_rateid_".$hide_fabric_description."_".$i;
			 
		     $new_array_color=return_library_array( "select a.fabric_color_id,b.id,b.color_name from wo_booking_dtls a, lib_color b 
			 where b.id=a.fabric_color_id and a.pre_cost_fabric_cost_dtls_id=".$$fabric_description_id."", "id", "color_name"  );
			 
			 if(str_replace("'","",$$item_color)!="")
			 {
				 if (!in_array(str_replace("'","",$$item_color),$new_array_color))
				 {
					  $color_id = return_id( str_replace("'","",$$item_color), $color_library, "lib_color", "id,color_name","228");  
					  $new_array_color[$color_id]=str_replace("'","",$$item_color);
				 }
				 else $color_id =  array_search(str_replace("'","",$$item_color), $new_array_color); 
			 }
			 else $color_id =0;

			if(str_replace("'",'',$$updateid)!="")
			{
			$id_arr[]=str_replace("'",'',$$updateid);
			$data_array_up1[str_replace("'",'',$$updateid)] =explode("*",("".$$fabric_description_id."*".$$artworkno."*".$$txt_mcdia."*".$$txt_slength."*".$$item_size."*".$$po_id."*".$$color_size_table_id."*".$$txt_job_no."*".$txt_booking_no."*3*".$$item_color_id."*".$$gmts_color_id."*".$$fabric_description_id."*".$cbo_dia."*".$$uom."*".$cbo_process."*".$cbo_colorsizesensitive."*".$$txt_woqnty."*".$$txt_rate."*".$$txt_amount."*".$$startdate."*".$$enddate."*".$$lib_composition."*".$$lib_supplier_rateId."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"."*".$$txt_program_nos."*".$$txt_findia."*".$$txt_fingsm."*".$$txt_ycount."*".$$txt_lot."*".$$txt_brand."*".$$txt_remark_dtls));
			}
			//txt_program_no
		 }  
		//echo "10**<pre>".print_r($data_array_up1); die;
		 $rID=execute_query(bulk_update_sql_statement( "wo_booking_dtls", "id", $field_array_up1, $data_array_up1, $id_arr ),1);
         check_table_status( $_SESSION['menu_id'],0);
		 
		if($db_type==0)
		{
			if($rID==1){
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			
			if($rID==1)
			{
				oci_commit($con);  
				echo "1**".str_replace("'","",$$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
			}
			else{
				oci_rollback($con); 
				echo "10**".str_replace("'","",$$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
			}
		}
		disconnect($con);
		die;
	}
	
	
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_booking_no");
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			 disconnect($con);die;
		}
		$field_array="status_active*is_deleted";
		$data_array="'0'*'1'";
		$txt_all_update_id=str_replace("*",",",str_replace("'","",$txt_all_update_id));
		$rID=sql_multirow_update("wo_booking_dtls",$field_array,$data_array,"id","".$txt_all_update_id."",1);
		//$rID1=sql_delete("wo_booking_dtls",$field_array,$data_array,"booking_no","".$txt_booking_no."",1);
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID){
				oci_commit($con);  
				echo "2**".str_replace("'","",$$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
			}
			else{
				oci_rollback($con); 
				echo "10**".str_replace("'","",$$txt_job_no)."**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$cbo_colorsizesensitive);
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
	function set_checkvalue()
	{
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
	<table width="1000" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
        <thead>
            <tr>
                <th width="150">Company Name</th>
                <th width="172">Buyer Name</th>
                 <th width="80">Internal Ref.</th>
                <th width="110">Booking No</th>
                <th width="110">Job No</th>
                <th width="180">Date Range</th>
               <th><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">Booking Without Dtls</th> 
            </tr>                	 
        </thead>
        <tr class="general"> 
            <td> <input type="hidden" id="selected_booking">
                <? 
				//echo $company.'ddd';
                echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company, "load_drop_down( 'service_booking_multi_job_wise_knitting_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
                ?>
            </td>
            <td id="buyer_td">
                <? 
                echo create_drop_down( "cbo_buyer_name", 172, $blank_array,"", 1, "-- Select Buyer --" );
                ?>
            </td>
            <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px"></td>
            <td><input name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:100px"></td>            
            <td><input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:100px"></td>
            <td>
            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
            </td> 
            <td>
            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_booking_no').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('cbo_year_selection').value, 'create_booking_search_list_view', 'search_div', 'service_booking_multi_job_wise_knitting_controller', 'setFilterGrid(\'list_view\',-1)') " style="width:100px;" /></td>
        </tr>
        <tr>
        	<td align="center" valign="middle" colspan="6"><? echo load_month_buttons(1); ?> </td>
        </tr>
    </table>
    </form>
    <div id="search_div"></div>    

   </div>
</body>     
<script> 	
	load_drop_down( 'service_booking_multi_job_wise_knitting_controller', <? echo $company;?>, 'load_drop_down_buyer', 'buyer_td' );
</script>      
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action=="create_booking_search_list_view")
{
	$data=explode('_',$data);
	
	// print_r($data);
	
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First.";  disconnect($con);die; }
	if ($data[1]!=0) $buyer=" and a.buyer_id='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	//if ($data[4]!="") $jobNoCond=" and a.job_no='$data[4]'"; else $jobNoCond="";
   	if (str_replace("'","",$data[4])!="") $jobNoCond=" and b.job_no like '%$data[4]%'"; else  $jobNoCond=""; 
    if (str_replace("'","",$data[5])!="") $bookingNoCond=" and a.booking_no_prefix_num like '%$data[5]%'  $booking_year_cond  "; else $bookingNoCond="";

	$internal_ref = str_replace("'","",$data[6]);
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and c.grouping='".trim($internal_ref)."' ";
	
	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
		$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[8]";
	}
	else if($db_type==2)
	{
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
		$year_cond=" and to_char(a.insert_date,'YYYY')=$data[8]";
	}
	 
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	//$po_no=return_library_array( "select id, job_no_prefix_num from wo_po_details_master",'id','job_no_prefix_num');
	
	
	//$arr=array (3=>$comp,3=>$buyer_arr,5=>$po_no,6=>$item_category,7=>$fabric_source,8=>$suplier);


	if($db_type==0)
	{

	if ($data[7]==0)
		{	
			$sql= "select a.id, a.process, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.delivery_date,  a.job_no, a.po_break_down_id, a.item_category, a.fabric_source,group_concat(distinct c.grouping) as internal_ref, from wo_booking_mst a,wo_booking_dtls b,wo_po_break_down c
			where  a.booking_no=b.booking_no and b.po_break_down_id=c.id  and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0  $company $buyer $booking_date $jobNoCond $bookingNoCond $year_cond and a.booking_type=3 and a.entry_form=228 and a.status_active=1 and a.is_deleted=0 and a.process=1 $internal_ref_cond group by  a.id, a.process, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.delivery_date,  a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.supplier_id order by a.booking_no DESC";
		}
		else
		{
			 $sql= "select a.id, a.process, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.delivery_date,  a.job_no, a.po_break_down_id, a.item_category, a.fabric_source,group_concat(distinct c.grouping) as internal_ref, from wo_booking_mst a
   			 where     a.booking_type=3 and a.entry_form=228 and a.status_active=1 and a.is_deleted=0 and a.process=1 $company $buyer $booking_date  $bookingNoCond $year_cond  group by  a.id, a.process, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.delivery_date,  a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.supplier_id order by a.booking_no DESC";
		}
	}
	else if($db_type==2)
	{
		if ($data[7]==0)
		{	
			$sql= "select a.id, a.process, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.delivery_date,  a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.supplier_id,listagg(CAST(c.grouping AS VARCHAR(4000)),',')  within group (order by c.grouping) as internal_ref from wo_booking_mst a,wo_booking_dtls b,wo_po_break_down c
			where  a.booking_no=b.booking_no and b.po_break_down_id=c.id  $company $buyer $booking_date $jobNoCond $bookingNoCond $year_cond and a.booking_type=3 and a.entry_form=228 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and a.process=1 $internal_ref_cond group by  a.id, a.process, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.delivery_date,  a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.supplier_id order by a.booking_no DESC";
		}
		else
		{
			 $sql= "select a.id, a.process, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.delivery_date,  a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.supplier_id,null as internal_ref from wo_booking_mst a
			where   a.booking_type=3 and a.entry_form=228 and a.status_active=1 and a.is_deleted=0 and a.process=1 and  NOT EXISTS (SELECT b.booking_no FROM wo_booking_dtls b WHERE a.booking_no=b.booking_no and b.status_active=1)  $company $buyer $booking_date   $bookingNoCond $year_cond  group by   a.id,a.booking_no, a.process, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.delivery_date,  a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.supplier_id order by a.id DESC";
		}
	}
	//echo  $sql;
	$booking_wise_ref=array();
	$booking_wise_ref_dup=array();
	foreach(sql_select( $sql) as $vals)
	{
		if($booking_wise_ref_dup[$vals[csf("booking_no")]][$vals[csf("internal_ref")]]=="")
		{
			$booking_wise_ref[$vals[csf("booking_no")]] =implode(",", array_unique(explode(",",$vals[csf("internal_ref")])) ) ;
			$booking_wise_ref_dup[$vals[csf("booking_no")]][$vals[csf("internal_ref")]]=420;
		}
		 
	}
	//print_r($booking_wise_ref);
$arr=array(2=>$comp,3=>$buyer_arr,5=>$item_category,6=>$booking_wise_ref,7=>$fabric_source,8=>$suplier);
	 

 
//echo $sql;
	echo  create_list_view("list_view", "Booking No,Booking Date,Company,Buyer,Delivery Date,Category,Internal Ref.", "70,80,100,100,100,100,100","970","320",0, $sql , "js_set_value", "booking_no", "", 1, "0,0,company_id,buyer_id,0,item_category,booking_no", $arr , "booking_no_prefix_num,booking_date,company_id,buyer_id,delivery_date,item_category,booking_no", '','','0,3,0,0,3,0','','');
	exit();
}

if($action=="terms_condition_popup")
{
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>
	<script>
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
			
			data_all=data_all+get_submitted_data_string('txt_booking_no*termscondition_'+i,"../../../",i);
		}
		var data="action=save_update_delete_fabric_booking_terms_condition&operation="+operation+'&total_row='+row_num+data_all;
		//freeze_window(operation);
		http.open("POST","service_booking_multi_job_wise_knitting_controller.php",true);
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
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
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
                                    <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>);" />
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
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="show_service_booking_report")
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	//$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library",'master_tble_id','image_location');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$path=($path=='')?'../../':$path;
	?>
	<div style="width:1150px" align="left">       
       <table width="90%" cellpadding="0" cellspacing="0" style="border:1px solid black;margin:5px;" >
           <tr>
               <td width="100"> 
               <img  src='<? echo $path.$imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="">                                     
                    <table width="90%" cellpadding="0" cellspacing="0" >
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
                                            Email Address: <? echo $result[csf('email')];?> 
                                            Website No: <? echo $result[csf('website')];
                            }
                            ?>   
                               </td> 
                            </tr>
                            <tr>
                            <td align="center" style="font-size:20px">  
                            <strong>Service Booking Sheet For Knitting</strong>
                             </td> 
                            </tr>
                      </table>
                </td> 
                <td width="250" id="barcode_img_id"> 
              
               </td>      
            </tr>
       </table>
		<?
		if($db_type==0) { $group_concat="group_concat(a.program_no) as program_no"; $group_concat.=",group_concat(a.program_no) as program_no";}
		if($db_type==2)
		 { $group_concat="listagg(cast(a.program_no as varchar2(4000)),',') within group (order by a.program_no) as program_no";
		 
		}
	
		$booking_grand_total=0;
		$job_no="";
		$po_no="";$pre_cost_fabric_dtls_id="";
		$nameArray_job=sql_select( "select  b.po_number,a.fabric_color_id,a.job_no,a.description,a.program_no ,a.pre_cost_fabric_cost_dtls_id as fab_dtls_id,a.sensitivity,a.rate,a.uom,a.wo_qnty from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no and a.entry_form_id=228  and a.status_active=1 and b.status_active=1  "); 
		//echo  "select  b.po_number,a.fabric_color_id,a.job_no,a.description,$group_concat ,a.pre_cost_fabric_cost_dtls_id as fab_dtls_id,a.sensitivity from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no and a.entry_form_id=228 group by  b.po_number,a.fabric_color_id,a.job_no,a.description,a.pre_cost_fabric_cost_dtls_id,a.sensitivity   ";
        foreach ($nameArray_job as $result_job)
        {
			$po_no.=$result_job[csf('po_number')].",";
			$job_no.=$result_job[csf('job_no')].",";
			$description=$result_job[csf('description')];
			$pre_cost_fabric_dtls_id.=$result_job[csf('fab_dtls_id')].",";
			$sensitivity_arr[$result_job[csf('sensitivity')]]=$result_job[csf('sensitivity')];
			$sensitivity_prog_arr[$result_job[csf('sensitivity')]][$result_job[csf('program_no')]][$description]['rate']=$result_job[csf('rate')];
			$sensitivity_prog_arr[$result_job[csf('sensitivity')]][$result_job[csf('program_no')]][$description]['wo_qnty']=$result_job[csf('wo_qnty')];
			$sensitivity_prog_arr[$result_job[csf('sensitivity')]][$result_job[csf('program_no')]][$description]['uom']=$result_job[csf('uom')];
			
			$sensitivity_prog_wise_color_arr[$result_job[csf('sensitivity')]][$result_job[csf('program_no')]][$description][$result_job[csf('fabric_color_id')]]['wo_qnty']+=$result_job[csf('wo_qnty')];
				
			$nameArray_color[$result_job[csf('fabric_color_id')]]=$result_job[csf('fabric_color_id')];
		}
		//print_r($sensitivity_prog_arr);
		unset($nameArray_job);
		$pre_cost_fabric_dtls_id=rtrim($pre_cost_fabric_dtls_id,',');
		$job_no=rtrim($job_no,',');
		
        $nameArray=sql_select( "select a.booking_no,a.booking_date,a.buyer_id,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.source  from wo_booking_mst a where  a.booking_no=$txt_booking_no and a.entry_form=228 and a.status_active=1 "); 
        foreach ($nameArray as $result)
        {
			$varcode_booking_no=$result[csf('booking_no')];
        ?>
       <table width="90%" style="border:1px solid black;margin:5px;">                    	
            <tr>
                <td colspan="6" valign="top"></td>                             
            </tr>                                                
            <tr>
                <td width="100" style="font-size:12px"><b>Work order No</b>   </td>
                <td width="110">:&nbsp;<? echo $result[csf('booking_no')];?> </td>
                <td width="100" style="font-size:12px"><b>Booking Date</b></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>
                <td width="100"><span style="font-size:12px"><b>Delivery Date</b></span></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>	
            </tr>
            <tr>
                <td width="100" style="font-size:12px"><b>Currency</b></td>
                <td width="110">:&nbsp;<? echo $currency[$result[csf('currency_id')]]; ?></td>
                <td  width="100" style="font-size:12px"><b>Conversion Rate</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('exchange_rate')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Source</b></td>
                <td  width="110" >:&nbsp;<? echo $source[$result[csf('source')]]; ?></td>
            </tr> 
             <tr>
                <td width="100" style="font-size:12px"><b>Supplier Name</b>   </td>
                <td width="110">:&nbsp;<? echo $supplier_name_arr[$result[csf('supplier_id')]];?>    </td>
                 <td width="100" style="font-size:12px"><b>Supplier Address</b></td>
               	<td width="110"><p>:&nbsp;<? echo $supplier_address_arr[$result[csf('supplier_id')]];?></p></td>
                <td  width="100" style="font-size:12px"><b>Attention</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('attention')]; ?></td>
            </tr>  
            <tr>
                <td width="100" style="font-size:12px"><b>Job No</b>   </td>
                <td width="110">:&nbsp;
				<? 
				echo $txt_job_no=implode(",",array_unique(explode(",",$job_no)));
				?> 
                </td>
                 
               	<td width="110" style="font-size:12px"><b>Buyer</b> </td>
                <td  width="100" style="font-size:12px" colspan="3">:&nbsp;<? echo $buyer_name_arr[$result[csf('buyer_id')]]; ?> </td>
            </tr> 
        </table>  
		<?
        }
		
        ?>
          <!--==============================================AS PER GMTS COLOR START=========================================  -->
		<?
		//========================================
		$fabric_description_array=array();
		$pre_cost_fabric_dtls_id=implode(",",array_unique(explode(",",$pre_cost_fabric_dtls_id)));
	$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where id in($pre_cost_fabric_dtls_id)");
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
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls  where  id in($pre_cost_fabric_dtls_id)");
			//list($fabric_description_row)=$fabric_description;
			foreach( $fabric_description as $fabric_description_row)
	        {
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]].=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")].", ";
			
			//$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]="All Fabrics  ".$conversion_cost_head_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("cons_process")]];
			}
		}
							
	}
	unset($wo_pre_cost_fab_conv_cost_dtls_id);
	
	//print_r($fabric_description_array); //listagg(program_no ,',') within group (order by program_no) AS program_no
	//=================================================
	$grand_total_as_per_gmts_color=0;
	foreach($sensitivity_prog_arr as $sensitivity_id=>$sensitivity_data)
	{
		if(count($sensitivity_prog_arr)>0)
		{
		if($sensitivity_id==1) $sensitivity_txt="As Per Garments Color";else $sensitivity_txt="As Per Contrast Color";
        ?>
        <table border="0" align="left" class="rpt_table" style="margin:5px;"  cellpadding="0" width="90%" cellspacing="0" >
            <tr>
                <td colspan="<? echo count($nameArray_color)+8; ?>" align="">
                <strong><? echo $sensitivity_txt;?></strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Program No</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <?  				
                foreach($nameArray_color  as $color_id=>$color)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $color_library[$color_id];?></strong></td>
                <?	}    ?>				
                <td style="border:1px solid black" align="center"><strong>Total</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			$i=0;
            $total_amount_as_per_gmts_color=0;
           foreach($sensitivity_data as $prog_id=>$prog_data)
           {
		    foreach($prog_data as $desc_id=>$row)
            {
				$i++;
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? //echo count($nameArray_item_description)+1; ?>">  <? echo $i; ?> </td>
                <td align="center" style="border:1px solid black" rowspan="<? //echo count($nameArray_item_description)+1; ?>">
                <p><? echo $prog_id; ?> </p> </td>
                <? 
                ?>
                <td style="border:1px solid black"><? echo rtrim($fabric_description_array[$desc_id],", "); ?> </td>
                <td style="border:1px solid black"><? //echo $result_itemdescription['brand_supplier']; ?> Booking Qnty </td>
				<?
				$total_color_qty=0;
				foreach($nameArray_color  as $color_id=>$color)
                {
				$item_desctiption_total=$sensitivity_prog_wise_color_arr[$sensitivity_id][$prog_id][$desc_id][$color_id]['wo_qnty'];
				?>
                <td style="border:1px solid black; text-align:right"><? 
				if($item_desctiption_total>0) echo number_format($item_desctiption_total,4);else echo "";?> </td>
				<?
				$total_color_qty+=$item_desctiption_total;
				$total_color_qty_arr[$color_id]+=$item_desctiption_total;
				}
				?>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_as_per_gmts_color = $total_color_qty*$row[('rate')];echo number_format($total_color_qty,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
					<td style="border:1px solid black; text-align:right"><? echo $unit_of_measurement[$row[('uom')]]; ?></td>
					<td style="border:1px solid black; text-align:right"><? echo number_format($row[('rate')],2); ?></td>
					<td style="border:1px solid black; text-align:right"><? echo number_format($amount_as_per_gmts_color,2); ?></td>
            </tr>
            <?
          	 } //Prog End
			 	
		   } //Desc End
            ?>
            <tr  style="border:1px solid black">
                <td align="right" style="border:1px solid black" colspan="4" ><strong>Total</strong></td>
				<?  	
				$totol_all_color=0;			
                foreach($nameArray_color  as $color_id=>$color)
                {	     ?>
               	 <td align="right" style="border:1px solid black"><strong><? echo $total_color_qty_arr[$color_id];
				 			$totol_all_color+=$total_color_qty_arr[$color_id];
				 ?></strong></td>
             <? }    ?>	
				<td  style="border:1px solid black;  text-align:right"><?  echo number_format($totol_all_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
				<td style="border:1px solid black" align="right"> </td>
				<td style="border:1px solid black" align="right"> </td>
				<td style="border:1px solid black" align="right"><?  echo number_format($total_amount_as_per_gmts_color,4);  ?></td>
            </tr>
        </table>
        <br/>
        <?
			}
			?>
			 <br/>
			<?
			 $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
		} //Sensitivity End
		?>
        <!--==============================================AS PER  COLOR END=========================================  -->
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
     
       &nbsp;
       <table  width="90%" class="rpt_table" style="border:1px solid black;margin:5px"   border="0" cellpadding="0" cellspacing="0">
       <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount</th><td width="30%" style="border:1px solid black; text-align:right"><? echo number_format($grand_total_as_per_gmts_color,2);?></td>
            </tr>
            <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount (in word)</th><td width="30%" style="border:1px solid black;"><? echo number_to_words(def_number_format($grand_total_as_per_gmts_color,2,""),$mcurrency, $dcurrency);?></td>
            </tr>
       </table>
          &nbsp;
           <table  width="90%" class="rpt_table" style="border:1px solid black;margin:5px"   border="0" cellpadding="0" cellspacing="0">
          <tr>
          <td>
           <?
		  	 echo get_spacial_instruction($txt_booking_no);
		  ?>
          </td>
          </tr>
           </table>
         <br/>
		 <?
            echo signature_table(81, $cbo_company_name, "1113px");
			echo "****".custom_file_name($txt_booking_no,$style_sting,$txt_job_no);
         ?>
    </div>
	<script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
    fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
    </script>
<?
}
if($action=="show_service_booking_report2")
{
		extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	//$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library",'master_tble_id','image_location');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$path=($path=='')?'../../':$path;
	?>
	<div style="width:1150px" align="left">       
       <table width="100%" cellpadding="0" cellspacing="0" style="border:0px solid black;margin:5px;font-family:'Arial Narrow'; font-size:16px;" >
           <tr>
               <td width="100"> 
               <img  src='<? echo $path.$imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="">                                     
                    <table width="100%" cellpadding="0" cellspacing="0" style="font-family:'Arial Narrow';font-size:16px;" >
                        <tr>
                            <td align="center" style="font-size:20px;">
                              <?php      
                                    echo $company_library[$cbo_company_name];
                              ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:16px;">  
                            <?
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
                                            Email Address: <? echo $result[csf('email')];?> 
                                            Website No: <? echo $result[csf('website')];
											
									$email=$result[csf('email')];
									$city=$result[csf('city')];
									$road_no=$result[csf('road_no')];
									$block_no=$result[csf('block_no')];
                            }
                            ?>   
                               </td> 
                            </tr>
                            <tr>
                            <td align="center" style="font-size:20px">  
                            <strong>Service Booking Sheet For Knitting</strong>
                             </td> 
                            </tr>
                      </table>
                </td> 
                <td width="250" id="barcode_img_id"> 
              
               </td>      
            </tr>
       </table>
		<?
		if($db_type==0) { $group_concat="group_concat(a.program_no) as program_no"; $group_concat.=",group_concat(a.program_no) as program_no";}
		if($db_type==2)
		 { $group_concat="listagg(cast(a.program_no as varchar2(4000)),',') within group (order by a.program_no) as program_no";
		 
		}
	
		$booking_grand_total=0;
		$job_no="";
		$po_no="";$pre_cost_fabric_dtls_id="";
		$nameArray_job=sql_select( "select  b.id as po_id,b.po_number,b.grouping,a.booking_no,a.slength,a.mc_dia,a.mc_gauge,a.artwork_no,a.fin_dia,a.fabric_color_id,a.remark,a.fin_gsm,a.yarn_count,a.lot_no,a.brand,a.gmts_color_id,a.fabric_color_id,a.job_no,a.description,a.program_no,a.lib_composition,a.delivery_date,a.delivery_end_date,a.pre_cost_fabric_cost_dtls_id as fab_dtls_id,a.sensitivity,a.rate,a.uom,a.amount,a.wo_qnty from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no and a.entry_form_id=228 and a.amount>0 and a.process in(1) and a.status_active=1 and b.status_active=1"); 
		//echo  "select  b.id as po_id,b.po_number,a.slength,a.mc_dia,a.mc_gauge,a.artwork_no,a.fin_dia,a.fabric_color_id,a.fin_gsm,a.yarn_count,a.lot_no,a.brand,a.gmts_color_id,a.fabric_color_id,a.job_no,a.description,a.program_no,a.lib_composition,a.delivery_date,a.delivery_end_date,a.pre_cost_fabric_cost_dtls_id as fab_dtls_id,a.sensitivity,a.rate,a.uom,a.wo_qnty from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no and a.entry_form_id=228";
        foreach ($nameArray_job as $row)
        {
			$po_no.=$row[csf('po_number')].",";
			$job_no.=$row[csf('job_no')].",";
			$description=$row[csf('description')];
			$pre_cost_fabric_dtls_id.=$row[csf('fab_dtls_id')].",";
			$varcode_booking_no=$row[csf('booking_no')];
			$sensitivity_arr[$row[csf('sensitivity')]]=$row[csf('sensitivity')];
			$sensitivity_prog_arr[$row[csf('sensitivity')]][$row[csf('po_id')]][$description][$row[csf('gmts_color_id')]]['po_number']=$row[csf('po_number')];
			$sensitivity_prog_arr[$row[csf('sensitivity')]][$row[csf('po_id')]][$description][$row[csf('gmts_color_id')]]['grouping']=$row[csf('grouping')];
			
			$sensitivity_prog_arr[$row[csf('sensitivity')]][$row[csf('po_id')]][$description][$row[csf('gmts_color_id')]]['fabric_color_id']=$row[csf('fabric_color_id')];
			$sensitivity_prog_arr[$row[csf('sensitivity')]][$row[csf('po_id')]][$description][$row[csf('gmts_color_id')]]['lib_composition']=$row[csf('lib_composition')];
			$sensitivity_prog_arr[$row[csf('sensitivity')]][$row[csf('po_id')]][$description][$row[csf('gmts_color_id')]]['rate']=$row[csf('rate')];
			$sensitivity_prog_arr[$row[csf('sensitivity')]][$row[csf('po_id')]][$description][$row[csf('gmts_color_id')]]['wo_qnty']+=$row[csf('wo_qnty')];
			$sensitivity_prog_arr[$row[csf('sensitivity')]][$row[csf('po_id')]][$description][$row[csf('gmts_color_id')]]['amount']+=$row[csf('amount')];
			$sensitivity_prog_arr[$row[csf('sensitivity')]][$row[csf('po_id')]][$description][$row[csf('gmts_color_id')]]['remark']=$row[csf('remark')];
			$sensitivity_prog_arr[$row[csf('sensitivity')]][$row[csf('po_id')]][$description][$row[csf('gmts_color_id')]]['uom']=$row[csf('uom')];
			$sensitivity_prog_arr[$row[csf('sensitivity')]][$row[csf('po_id')]][$description][$row[csf('gmts_color_id')]]['program_no'].=$row[csf('program_no')].',';
			
			if($row[csf('mc_dia')]!="" || $row[csf('mc_gauge')]!="" || $row[csf('artwork_no')]!="" || $row[csf('fin_dia')]!=""  || $row[csf('fin_gsm')]!="")
			{
			$sensitivity_prog_arr[$row[csf('sensitivity')]][$row[csf('po_id')]][$description][$row[csf('gmts_color_id')]]['mc_dia'].=$row[csf('mc_dia')].',';
			$sensitivity_prog_arr[$row[csf('sensitivity')]][$row[csf('po_id')]][$description][$row[csf('gmts_color_id')]]['mc_gauge'].=$row[csf('mc_gauge')].',';
			$sensitivity_prog_arr[$row[csf('sensitivity')]][$row[csf('po_id')]][$description][$row[csf('gmts_color_id')]]['artwork_no'].=$row[csf('artwork_no')].',';	
			$sensitivity_prog_arr[$row[csf('sensitivity')]][$row[csf('po_id')]][$description][$row[csf('gmts_color_id')]]['fin_dia'].=$row[csf('fin_dia')].',';
			$sensitivity_prog_arr[$row[csf('sensitivity')]][$row[csf('po_id')]][$description][$row[csf('gmts_color_id')]]['fin_gsm'].=$row[csf('fin_gsm')].',';
			
			}
			if($row[csf('yarn_count')]!="" )
			{
				$sensitivity_prog_arr2[$row[csf('sensitivity')]][$row[csf('po_id')]][$description]['yarn_count'].=$row[csf('yarn_count')].',';	
			}
			if($row[csf('lot_no')]!="" )
			{
				$sensitivity_prog_arr2[$row[csf('sensitivity')]][$row[csf('po_id')]][$description]['lot_no'].=$row[csf('lot_no')].',';
			}
			if($row[csf('brand')]!="" )
			{
				$sensitivity_prog_arr2[$row[csf('sensitivity')]][$row[csf('po_id')]][$description]['brand'].=$row[csf('brand')].',';
			}
			if($row[csf('slength')]!="" )
			{
				$sensitivity_prog_arr[$row[csf('sensitivity')]][$row[csf('po_id')]][$description][$row[csf('gmts_color_id')]]['slength'].=$row[csf('slength')].',';
			}
			
			
			$sensitivity_prog_arr[$row[csf('sensitivity')]][$row[csf('po_id')]][$description][$row[csf('gmts_color_id')]]['delivery_date'].=$row[csf('delivery_date')].',';
			$sensitivity_prog_arr[$row[csf('sensitivity')]][$row[csf('po_id')]][$description][$row[csf('gmts_color_id')]]['delivery_end_date'].=$row[csf('delivery_end_date')].',';
			//$sensitivity_prog_wise_color_arr[$row[csf('sensitivity')]][$description][$row[csf('gmts_color_id')]]['wo_qnty']+=$row[csf('wo_qnty')];
				
			//$nameArray_color[$result_job[csf('fabric_color_id')]]=$result_job[csf('fabric_color_id')];
		}
		//print_r($sensitivity_prog_arr);
		unset($nameArray_job);
		$pre_cost_fabric_dtls_id=rtrim($pre_cost_fabric_dtls_id,',');
		$job_no=rtrim($job_no,',');
		
        $nameArray=sql_select( "select a.booking_no,a.booking_date,a.pay_mode,a.remarks,a.buyer_id,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.source  from wo_booking_mst a where  a.booking_no=$txt_booking_no and a.entry_form=228"); 
        foreach ($nameArray as $result)
        {
			$varcode_booking_no=$result[csf('booking_no')];
			$pay_mode=$result[csf('pay_mode')];$supplier_id=$result[csf('supplier_id')];
			if($pay_mode==5 || $pay_mode==3){
						$com_supp=$company_library[$supplier_id];
						$suplier_address=$road_no.', '.$block_no.', '.$city.', '.$email;
					}
					else{
						$com_supp=$supplier_name_arr[$supplier_id];
						$suplier_address=$supplier_address_arr[$supplier_id];
					}
					
        ?>
       <table width="100%" style="border:0px solid black;margin:5px;font-family:'Arial Narrow'; font-size:16px;">                    	
            <tr>
                <td colspan="6" valign="top"></td>                             
            </tr> 
			<tr>
                <td width="100" style="font-size:12px" colspan="6" align="left"><b>To</b></td>
				
			</tr>
			<tr>
				<td width="100" style="font-size:12px" ><b>Supplier Name</b>   </td>
                <td width="160" colspan="2">:&nbsp;<? echo $com_supp;?> </td>
				 <td width="350" style="font-size:12px">&nbsp;   </td>
				 
				 <td width="120" style="font-size:12px"><b>Work order No</b>   </td>
                <td width="120" colspan="2">:&nbsp;<? echo $result[csf('booking_no')];?> </td>
			</tr>
			<tr>
				<td width="100" valign="top" style="font-size:12px" ><b>Address</b>   </td>
                <td width="160" colspan="2"   valign="top">:&nbsp;<? echo $suplier_address;?> </td>
				 <td width="350" style="font-size:12px">&nbsp; </td>
				 <td width="120" style="font-size:12px"><b>Booking Date</b>   </td>
                <td width="120" colspan="2">:&nbsp;<? echo change_date_format($result[csf('booking_date')]);?> </td>
			</tr>
			<tr>
				<td width="100"   style="font-size:12px" ><b>Attention</b>   </td>
                <td width="160" colspan="2"  >:&nbsp;<? echo $result[csf('attention')];?> </td>
				 <td width="350" style="font-size:12px">&nbsp; </td>
				 <td width="120" style="font-size:12px"><b>Delivery  Date</b>   </td>
                <td width="120" colspan="2">:&nbsp;<? echo change_date_format($result[csf('delivery_date')]);?> </td>
			</tr>
			<tr>
				<td width="100"   style="font-size:12px" ><b>Currency</b>   </td>
                <td width="160" colspan="2"  >:&nbsp;<? echo $currency[$result[csf('currency_id')]];?> </td>
				 <td width="350" style="font-size:12px">&nbsp; </td>
			
				 <td width="120" style="font-size:12px"><b>Buyer</b>   </td>
                <td width="120" colspan="2">:&nbsp;<? echo $buyer_name_arr[$result[csf('buyer_id')]];?> </td>
             
			</tr>
			<tr>
				<td width="100"   style="font-size:12px" ><b>Conversion Rate</b></td>
                <td width="160" colspan="2"  >:&nbsp;<? echo $result[csf('exchange_rate')]; ?> </td>
				<td width="350" style="font-size:12px">&nbsp; </td>
				<td width="100"   style="font-size:12px" ><b>Job </b>   </td>
                <td width="160" colspan="2"  >:&nbsp;<? echo $txt_job_no=implode(",",array_unique(explode(",",$job_no)));?> </td>
				
               
			</tr>
			<tr>
				
				
				 <td width="250" style="font-size:12px" colspan="5"><b>Remark : <? echo $result[csf('remarks')];?></b>   </td>
               
			</tr>
        </table>  
		<?
        }
		
        ?>
          <!--==============================================AS PER GMTS COLOR START=========================================  -->
		<?
		//========================================
		$fabric_description_array=array();
		$pre_cost_fabric_dtls_id=implode(",",array_unique(explode(",",$pre_cost_fabric_dtls_id)));
	$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where id in($pre_cost_fabric_dtls_id)   and status_active=1");
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
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description from  wo_pre_cost_fabric_cost_dtls  where  id in($pre_cost_fabric_dtls_id)");
			//list($fabric_description_row)=$fabric_description;
			foreach( $fabric_description as $fabric_description_row)
	        {
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]].=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")].", ";
			
			//$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]="All Fabrics  ".$conversion_cost_head_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("cons_process")]];
			}
		}
							
	}
	unset($wo_pre_cost_fab_conv_cost_dtls_id);
	
	//print_r($fabric_description_array); //listagg(program_no ,',') within group (order by program_no) AS program_no
	//=================================================
	$color_rowspan_arr=array();$po_rowspan_arr=array();$sensity_rowspan_arr=array();
	foreach($sensitivity_prog_arr as $sensitivity_id=>$sensitivity_data)
	{
		  $sensity_rowspan=0;
		foreach($sensitivity_data as $po_id=>$po_data)
        {
		    $po_rowspan=0;
			foreach($po_data as $desc_id=>$desc_data)
            {
				$color_rowspan=0;
				foreach($desc_data as $color_id=>$row)
           		 {
				 	$color_rowspan++;
					$po_rowspan++;
					$sensity_rowspan++;
				 }
				 $sensity_rowspan_arr[$sensitivity_id]=$sensity_rowspan;
				 $po_rowspan_arr[$sensitivity_id][$po_id]=$po_rowspan;
				 $color_rowspan_arr[$sensitivity_id][$po_id][$desc_id]=$color_rowspan;
			}
		}
	}
	//print_r($po_rowspan_arr);
	
	$grand_total_wo_qty=0;$grand_total_wo_amount=0;$all_grand_total_wo_amount=0;$all_grand_total_wo_qty=0;
	foreach($sensitivity_prog_arr as $sensitivity_id=>$sensitivity_data)
	{
		if(count($sensitivity_prog_arr)>0)
		{
		if($sensitivity_id==1) $sensitivity_txt="As Per Garments Color";else $sensitivity_txt="As Per Contrast Color";
    
		?>
        <table border="1" align="left" class="rpt_table" style="margin:5px;font-family:'Arial Narrow'; font-size:15px;"  cellpadding="0" width="100%" cellspacing="0" >
            <caption> <strong><? echo $sensitivity_txt;?></strong></caption>
            <tr>
                <th style=""><strong>Sl</strong> </th>
                <th style="" width="100"><strong>Po No</strong> </th>
                <th style="" width="100"><strong>Internal ref  No</strong> </th>
                <th style="" width="100"><strong>Program No</strong> </th>
                <th style="" width="200"><strong>Fabric Description</strong> </th>
                
				<th style="" width="70" align="center"><strong>Y.Count</strong></th>
				<th style="" width="50" align="center"><strong>Lot</strong></th>
				<th style="" width="50" align="center"><strong>Brand</strong></th>
				<th style="" width="100" align="center"><strong>Gmts Color</strong></th>
				<th style="" width="100" align="center"><strong>Item Color</strong></th>
				
				<th style="" width="60" align="center"><strong>M/C Dia X GG</strong></th>
				<th style="" width="50" align="center"><strong>Fin Dia</strong></th>
				<th style="" width="50" align="center"><strong>Fin GSM </strong></th>
				<th style="" width="60" align="center"><strong>S/L</strong></th>
				
                <th style="" width="40" align="center"><strong>UOM</strong></th>
				<th style="" width="70" align="center"><strong>WO .Qnty</strong></th>
				<? if($show_comments==1){?>
                <th style="" width="50" align="center"><strong>Rate</strong></th>
                <th style="" width="70" align="center"><strong>Amount</strong></th>
				<?}?>
				<th style="" align="center"><strong>Remark</strong></th>
            </tr>
            <?
			$i=1;
            $total_amount_as_per_gmts_color=0;  $sub_total_wo_qty=$sub_total_wo_amount=0;
			$grand_sensitivity_total_wo_qty=array();
           foreach($sensitivity_data as $po_id=>$po_data)
           {
		    	$x=1;
			foreach($po_data as $desc_id=>$desc_data)
            {
				$y=1;
				foreach($desc_data as $color_id=>$row)
           		 {
				$lot_no=$sensitivity_prog_arr2[$sensitivity_id][$po_id][$desc_id]['lot_no'];
				$yarn_count=$sensitivity_prog_arr2[$sensitivity_id][$po_id][$desc_id]['yarn_count'];
				$brand=$sensitivity_prog_arr2[$sensitivity_id][$po_id][$desc_id]['brand'];
				
				$row[('mc_dia')]=rtrim($row[('mc_dia')],',');
				$row[('mc_gauge')]=rtrim($row[('mc_gauge')],',');
				$row[('artwork_no')]=rtrim($row[('artwork_no')],',');
				$lot_no=rtrim($lot_no,',');
				$lot_no=implode(",",array_unique(explode(",",$lot_no)));
				$brand=rtrim($brand,',');
				$yarn_count=rtrim($yarn_count,',');
				$brand=implode(",",array_unique(explode(",",$brand)));
				$yarn_count=implode(",",array_unique(explode(",",$yarn_count)));
				
				$row[('fin_dia')]=rtrim($row[('fin_dia')],',');
				$row[('fin_gsm')]=rtrim($row[('fin_gsm')],',');
				
				$row[('slength')]=rtrim($row[('slength')],',');
				$row[('delivery_date')]=rtrim($row[('delivery_date')],',');
				$row[('delivery_end_date')]=rtrim($row[('delivery_end_date')],',');
				//$row[('remark')]=rtrim($row[('remark')],',');
				//echo $row[('lot_no')].' LO';
				$program_no=rtrim($row[('program_no')],',');
				$program_nos=implode(", ",array_unique(explode(",",$program_no)));

				
				$color_rowspan=$color_rowspan_arr[$sensitivity_id][$po_id][$desc_id];
            ?>
            <tr>
                <?
				if($x==1)
				{ 
				?>
				<td style="" rowspan="<? echo $po_rowspan_arr[$sensitivity_id][$po_id]; ?>">  <? echo $i; ?> </td>
                <td align="center" style="" rowspan="<? echo $po_rowspan_arr[$sensitivity_id][$po_id]; ?>" > <p><? echo $row[('po_number')]; ?> </p> </td>
                <td align="center" style="" rowspan="<? echo $po_rowspan_arr[$sensitivity_id][$po_id]; ?>" > <p><? echo $row[('grouping')]; ?> </p> </td>
                <? 
				}
				?>
					<td style=""><? echo $program_nos; ?></td>
				<?
				if($y==1)
				{ 
				
                ?>
				 <td style="" rowspan="<? echo $color_rowspan; ?>"><? echo rtrim($fabric_description_array[$desc_id],", "); ?> </td>
				
               
                <td style=" text-align:center" rowspan="<? echo $color_rowspan; ?>">  <?   echo $yarn_count; ?>   </td>
				<td style="text-align:center" rowspan="<? echo $color_rowspan; ?>"><? echo $lot_no; ?></td>
				<td style="text-align:center" rowspan="<? echo $color_rowspan; ?>"><? echo $brand; ?></td>
					<?
				}
					?>
					<td style="text-align:center"><? echo $color_library[$color_id]; ?></td>
					<td style="text-align:center"><? echo $color_library[$row[('fabric_color_id')]]; ?></td>
					
					<td style="text-align:center"><? 
					/*if($row[('mc_dia')]!='' && $row[('mc_gauge')]!='') $mc_dia_gg=$row[('mc_dia')].'X'.$row[('mc_gauge')];
					else if($row[('mc_dia')]!='' && $row[('mc_gauge')]=='') $mc_dia_gg=$row[('mc_dia')];
					else if($row[('mc_dia')]=='' && $row[('mc_gauge')]!='') $mc_dia_gg=$row[('mc_gauge')];
					else $mc_dia_gg="";*/ echo $row[('mc_dia')]; ?></td>
					<td style="text-align:center"><? echo $row[('fin_dia')]; ?></td>
					<td style="text-align:center"><? echo $row[('fin_gsm')]; ?></td>
					<td style="text-align:center"><? echo $row[('slength')]; ?></td>
					
					
					<td style="text-align:right"><? echo $unit_of_measurement[$row[('uom')]]; ?></td>
					<td style="text-align:right"><? echo number_format($row[('wo_qnty')],2); ?></td>
					<? if($show_comments==1){?>
					<td style="text-align:right"><? echo number_format(($row[('amount')]/$row[('wo_qnty')]),2); ?></td>
					<td style="text-align:right"><? echo number_format($row[('amount')],2); ?></td>
					<?}?>
					<td style=""><? echo $row[('remark')]; ?></td>
            </tr>
            <?
				$x++;$y++;
				$sub_total_wo_qty+=$row[('wo_qnty')];$sub_total_wo_amount+=$row[('amount')];
				$grand_sensitivity_total_wo_qty[$sensitivity_id]+=$row[('wo_qnty')];
				$grand_sensitivity_total_wo_amount[$sensitivity_id]+=$row[('amount')];
				
				$grand_total_wo_qty+=$row[('wo_qnty')];
				
				 $grand_total_wo_amount+=$row[('amount')];
				 } //Color End
          	 } //Prog End
			 	?>
				<tr  style="border:1px solid black">
                <td align="right" style="border:1px solid black" colspan="15"><strong>Sub Total</strong></td>
					
				
               	 <td align="right" style=""><strong><? echo $sub_total_wo_qty;unset($sub_total_wo_qty);
				 			//$totol_all_color+=$sub_total_wo_qty;
				 ?></strong></td>
           	
				<? if($show_comments==1){?>
				<td style="" align="right"> </td>
				<td  style="text-align:right"><?  echo number_format($sub_total_wo_amount,4);unset($sub_total_wo_amount); ?></td>
				<?}?>
				<td style="" align="right"> </td>
				
            </tr>
				<?
				$i++;
			
		   } //Desc End
            ?>
          	 <tr style="border:1px solid black;">
                <td colspan="15" width="80%" style="border:1px solid black; text-align:right"><strong> Total </strong></td>
				<td style="text-align:right" width="5%"><? echo number_format($grand_total_wo_qty,2);$grand_total_wo_qty=0;?></td>
				<? if($show_comments==1){?>
				<td style="text-align:right" width="3%"><? //echo number_format($grand_total_wo_qty,2);?></td>
				<td style="text-align:right" width="5%" ><? echo number_format($grand_total_wo_amount,2);$grand_total_wo_amount=0;?></td>
				<?}?>
				<td style="text-align:right" width="3%">  &nbsp; &nbsp; &nbsp;<? //echo number_format($grand_total_wo_qty,2);?></td>
      		 </tr>
        </table>
        <br/>
        <?
			}
			?>
			 <br/>
			<?
			 $all_grand_total_wo_qty+=$grand_sensitivity_total_wo_qty[$sensitivity_id];
			 $all_grand_total_wo_amount+=$grand_sensitivity_total_wo_amount[$sensitivity_id];
			 
		} //Sensitivity End
		?>
		  <br>
        <!--==============================================AS PER  COLOR END=========================================  -->
	<table border="1" align="left" class="rpt_table" style="margin:5px;font-family:'Arial Narrow'; font-size:15px;"  cellpadding="0" width="100%" cellspacing="0" >
      <tr style="">
                <td colspan="15" width="80%" style="border:1px solid black; text-align:right"><strong> Grand Total </strong></td>
				<td style="text-align:right" width="5%" colspan="2"><? echo number_format($all_grand_total_wo_qty,2);?></td>
			
				<td style="text-align:right" width="5%" colspan="2" ><? echo number_format($all_grand_total_wo_amount,2);?></td>
				
      </tr>
	</table>
      
          <br/>
		  
        <table  width="100%" class="rpt_table" style="border:1px solid black;margin:5px"   border="0" cellpadding="0" cellspacing="0">
          <tr>
          <td>
           <?
		  	 echo get_spacial_instruction($txt_booking_no);
		  ?>
          </td>
          </tr>
           </table>
         <br/>
		 <?
            echo signature_table(150, $cbo_company_name, "1113px");
			//echo "****".custom_file_name($varcode_booking_no,$style_sting,$txt_job_no);
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
	//$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library",'master_tble_id','image_location');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	$path=($path=='')?'../../':$path;
	
	?>
	<div style="width:1150px" align="left">       
       <table width="90%" cellpadding="0" cellspacing="0" style="border:1px solid black">
           <tr>
               <td width="100"> 
               <img  src='<? echo $path.$imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="1050">                                     
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
                                            Province No: <?php echo $result[csf('province')];?> 
                                            Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
                                            Email Address: <? echo $result[csf('email')];?> 
                                            Website No: <? echo $result[csf('website')];
                            }
                            ?>   
                               </td> 
                            </tr>
                            <tr>
                            <td align="center" style="font-size:20px">  
                            <strong>Service Booking Sheet For Knitting</strong>
                             </td> 
                            </tr>
                      </table>
                </td>       
            </tr>
       </table>
		<?
		$booking_grand_total=0;
		$job_no="";
		$currency_id="";
		$nameArray_job=sql_select( "select distinct b.job_no  from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_no=$txt_booking_no"); 
        foreach ($nameArray_job as $result_job)
        {
			$job_no.=$result_job[csf('job_no')].",";
		}
		$po_no="";
		$nameArray_job=sql_select( "select distinct b.po_number  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no"); 
        foreach ($nameArray_job as $result_job)
        {
			$po_no.="'".$result_job[csf('po_number')]."'".",";
		}
        $nameArray=sql_select( "select a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.source  from wo_booking_mst a where  a.booking_no=$txt_booking_no");
		//echo  "select po_break_down_id,article_number from wo_po_color_size_breakdown where po_break_down_id in(".rtrim($po_no,',').")";
		$article_number_arr=return_library_array( "select po_break_down_id,article_number from wo_po_color_size_breakdown where po_break_down_id in(".rtrim($po_no,',').")", "po_break_down_id", "article_number"  );
		//print_r($article_number_arr);
		$booking_date=$nameArray[0][csf('booking_date')];
        foreach ($nameArray as $result)
        {
        ?>
       <table width="90%" style="border:1px solid black">                    	
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
                <td  width="110" rowspan="6" align="center">
                
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
                <td width="100" style="font-size:12px"><b>Supplier Name</b>   </td>
                <td width="110">:&nbsp;<? echo $supplier_name_arr[$result[csf('supplier_id')]];?>    </td>
            </tr>  
             <tr>
                <td width="100" style="font-size:12px"><b>Supplier Address</b></td>
               	<td width="110" colspan="3">:&nbsp;<? echo $supplier_address_arr[$result[csf('supplier_id')]];?></td>
                
            </tr>  
            <tr>
                <td width="100" style="font-size:12px"><b>Job No</b>   </td>
                <td width="110" colspan="3">:&nbsp;
				<? 
				echo $txt_job_no=rtrim($job_no,',');
				?> 
                </td>
            </tr> 
            <tr>
               	<td width="110" style="font-size:12px"><b>PO No</b> </td>
                <td  width="100" style="font-size:12px" colspan="3">:&nbsp;<? echo rtrim(str_replace("'","",$po_no),','); ?> </td>
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
        $nameArray_item=sql_select( "select distinct process,description from wo_booking_dtls  where booking_no=$txt_booking_no  and process=1 ");//and sensitivity=1 
        $nameArray_color=sql_select( "select distinct fabric_color_id from wo_booking_dtls   where  booking_no=$txt_booking_no and  process=1 "); //and sensitivity=1
		
       
		foreach($nameArray_item as $result_item)
        {
        ?>
        
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="90%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="9" align="">
                <strong><? echo "Fabrication:".rtrim($fabric_description_array[$result_item[csf('description')]],", "); ?> </strong><br/>
                <strong><? echo "Process:".$conversion_cost_head_array[$result_item[csf('process')]]; ?> </strong>
                </td>
            </tr>
            <tr>
                
                <td style="border:1px solid black"><strong>Article No</strong> </td>
                <td style="border:1px solid black"><strong>Order No</strong> </td>
                <td style="border:1px solid black"><strong>Program No</strong> </td>
                <td style="border:1px solid black"><strong>GMT Color</strong> </td>
                <td style="border:1px solid black" align="center"><strong>Wo Qty (Kg)</strong></td>
                <td style="border:1px solid black" align="center"><strong>Artwork No</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			 $total_amount_as_per_gmts_color=0;
            $nameArray_item_description=sql_select( "select  po_break_down_id,fabric_color_id,description,rate,artwork_no,sum(wo_qnty) as cons,program_no from wo_booking_dtls  where booking_no=$txt_booking_no  and process=".$result_item[csf('process')]." and wo_qnty>0 and is_deleted=0 and status_active=1 group by po_break_down_id,fabric_color_id,description,rate,artwork_no,program_no ");//and sensitivity=1 
                foreach($nameArray_item_description as $result_itemdescription)
                {
               
                ?>
            <tr>
                <td align="center" style="border:1px solid black">
                <? echo $article_number_arr[$result_item[csf('po_break_down_id')]]; ?>
                </td>
                <td style="border:1px solid black"><? echo rtrim($po_number[$result_itemdescription[csf('po_break_down_id')]],", "); ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('program_no')]; ?> </td>
                <td style="border:1px solid black"><? echo$color_library[$result_itemdescription[csf('fabric_color_id')]]; ?>  </td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('cons')],2); ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('artwork_no')]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
				echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
				<?
                }
                ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="7"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,4);
                $booking_grand_total+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            
        </table>
        &nbsp;
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
		
		
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2 and process=1"); 
        $nameArray_size=sql_select( "select distinct  item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=2 and process=1 ");
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
            $nameArray_item_description=sql_select( "select distinct description,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2 and process=".$result_item[csf('process')]." and wo_qnty>0 and is_deleted=0 and status_active=1 "); 
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
					$nameArray_size_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where   booking_no=$txt_booking_no and sensitivity=2 and process=". $result_item[csf('process')]." and description='". $result_itemdescription[csf('description')]."' and item_size='".$result_size[csf('gmts_sizes')]."'");  
	
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
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and process=1"); 
        $nameArray_color=sql_select( "select distinct fabric_color_id as color_number_id from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=3 and process=1  and is_deleted=0 and status_active=1"); 
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
            $nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and process=".$result_item[csf('process')]." and wo_qnty>0 and is_deleted=0 and status_active=1 "); 
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
                $nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls    where   booking_no=$txt_booking_no and sensitivity=3 and process=". $result_item[csf('process')]." and description='". $result_itemdescription[csf('description')]."' and fabric_color_id=".$result_color[csf('color_number_id')]."");                          
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

        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=4 and process=1"); 
        $nameArray_size=sql_select( "select distinct item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4 and process=1");
	    $nameArray_color=sql_select( "select distinct fabric_color_id as color_number_id from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4 and process=1"); 

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
            $nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=4 and process=".$result_item[csf('process')]." and wo_qnty>0 and is_deleted=0 and status_active=1"); 
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
                $nameArray_size_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4 and process=". $result_item[csf('process')]." and  description='". $result_itemdescription[csf('description')]."' and  item_size='".$result_size[csf('gmts_sizes')]."' and fabric_color_id=".$result_color[csf('color_number_id')]."");                          
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
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 and process=1"); 
        //$nameArray_color=sql_select( "select distinct b.color_number_id from wo_trims_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.sensitivity=1"); 
		$nameArray_color= array();
		if(count($nameArray_item)>0)
		{
        ?>
        <table border="0" align="left" class="rpt_table"  cellpadding="0" width="90%" cellspacing="0" >
            <tr>
                <td colspan="7" align="">
                <strong>No Sensitivity</strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Service Type</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong></strong> </td>
                <td align="center" style="border:1px solid black"><strong> Qnty</strong></td>
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
            $nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 and process=".$result_item['process']." and wo_qnty>0 and is_deleted=0 and status_active=1"); 
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
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
                $nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls where    booking_no=$txt_booking_no and sensitivity=0 and process=". $result_item[csf('process')]." and  description='". $result_itemdescription[csf('description')]."'");                          
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
                
                <td style="border:1px solid black; text-align:center "><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
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
                <td style="border:1px solid black;  text-align:right">
                <?
                if($color_tatal !='')
                {
                echo number_format($color_tatal,2);  
                }
                ?>
                </td>
                <td style="border:1px solid black;"></td>
                <td style="border:1px solid black; text-align:right"></td>
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
                <td align="right" style="border:1px solid black"  colspan="7"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <?
		//print_r($color_tatal);
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
		   //echo $currency_id.'aaaaa';
		$mcurrency='USD';
		$dcurrency='CENTS'; 
	   }
	   if($currency_id==3)
	   {
		$mcurrency='EURO';
		$dcurrency='CENTS'; 
	   }
	   ?>
       <table  width="90%" class="rpt_table" style="border:1px solid black;"   border="0" cellpadding="0" cellspacing="0">
       <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount</th><td width="30%" style="border:1px solid black; text-align:right"><? echo number_format($booking_grand_total,2);?></td>
            </tr>
            <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount (in word)</th><td width="30%" style="border:1px solid black;"><? echo number_to_words(def_number_format($booking_grand_total,2,''),$mcurrency, $dcurrency);?></td>
            </tr>
       </table>
          &nbsp;
           <table  width="90%" class="rpt_table" style="border:1px solid black;"   border="0" cellpadding="0" cellspacing="0">
            <tr style="border:1px solid black;">
            <td>
				<?
                 echo get_spacial_instruction($txt_booking_no);
                ?>
            </td>
            </tr>
           </table>
        <table  width="90%" class="rpt_table" style="border:1px solid black; display:none"   border="0" cellpadding="0" cellspacing="0">
        <thead>
            <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th><th width="97%" style="border:1px solid black;">Spacial Instruction</th>
            </tr>
        </thead>
        <tbody>
        <?
        $data_array=sql_select("select id, terms from  wo_booking_terms_condition where entry_form=229 and booking_no=$txt_booking_no");// quotation_id='$data'
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
    <table border="0" cellpadding="0" cellspacing="0"  width="90%" class="rpt_table"  style="border:1px solid black;" >
                <tr> <td style="border:1px solid black;" colspan="9" align="center"><b> Comments</b> </td></tr>
                <tr style="border:1px solid black;" align="center">
                    <th style="border:1px solid black;" width="40">SL</th>
                    <th style="border:1px solid black;" width="200">Job No</th>
                    <th style="border:1px solid black;" width="200">PO No</th>
                    <th style="border:1px solid black;" width="80">Ship Date</th>
                    <th style="border:1px solid black;" width="80">Pre-Cost/Budget Value</th>
                    <th style="border:1px solid black;" width="80">WO Value</th>
                   
                    <th style="border:1px solid black;" width="80">Balance</th>
                    <th style="border:1px solid black;" width="">Comments </th>
                </tr>
       <tbody>
       <?
					$po_qty_arr=array();$knit_data_arr=array();
					$sql_po_qty=sql_select("select b.id as po_id,b.pub_shipment_date,sum(b.po_quantity) as order_quantity,(sum(b.po_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.is_deleted=0  and a.status_active=1 group by b.id,a.total_set_qnty,b.pub_shipment_date order by  b.id");
					foreach( $sql_po_qty as $row)
					{
						$po_qty_arr[$row[csf("po_id")]]['order_quantity']=$row[csf("order_quantity_set")];
						$po_qty_arr[$row[csf("po_id")]]['pub_shipment_date']=$row[csf("pub_shipment_date")];
					}
					$pre_cost=sql_select("select job_no,sum(amount) AS knit_cost from wo_pre_cost_fab_conv_cost_dtls where cons_process=1 and status_active=1 and is_deleted=0 group by job_no");
					foreach($pre_cost as $row)
					{ 
						$knit_data_arr[$row[csf('job_no')]]['knit']=$row[csf('knit_cost')];	
					}
					
					if($db_type==0)
					{
						$conversion_date=change_date_format($booking_date, "Y-m-d", "-",1);
					}
					else
					{
						$conversion_date=change_date_format($booking_date, "d-M-y", "-",1);
					}
					
					
					
					$i=1; $total_balance_knit=0;$tot_knit_cost=0;$tot_pre_cost=0;
				
					$sql_knit=( "select b.po_break_down_id as po_id,a.job_no,sum(b.amount) as amount from wo_booking_mst a, wo_booking_dtls b where a.job_no=b.job_no and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.booking_type=3 and a.item_category=12 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id,a.job_no  order by b.po_break_down_id");
					
                    $nameArray=sql_select( $sql_knit );
                    foreach ($nameArray as $selectResult)
                    {
						$costing_per=return_field_value("costing_per", "wo_pre_cost_mst", "job_no='".$selectResult[csf('job_no')]."'");
						//echo $costing_per;
						//echo $selectResult[csf('job_no')];
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
						$po_qty=$po_qty_arr[$selectResult[csf('po_id')]]['order_quantity'];
						$pre_cost_knit=($knit_data_arr[$selectResult[csf('job_no')]]['knit']/$costing_per_qty)*$po_qty;
						
						if($currency_id==1)
						{
							$currency_rate=set_conversion_rate( 2, $conversion_date );
							$knit_charge=$selectResult[csf("amount")]/$currency_rate;	
						}
						else
						{
							$knit_charge=$selectResult[csf("amount")];
						}
						//$knit_charge=$selectResult[csf("amount")]/$result[csf('exchange_rate')];
						$ship_date=$po_qty_arr[$selectResult[csf("po_id")]]['pub_shipment_date'];
	   ?>
                    <tr>
                    <td style="border:1px solid black;" width="40"><? echo $i;?></td>
                    <td style="border:1px solid black;" width="200">
					<? echo $selectResult[csf('job_no')];?> 
                    </td>
                    <td style="border:1px solid black;" width="200">
					<? echo $po_number[$selectResult[csf('po_id')]];?> 
                    </td>
                    <td style="border:1px solid black;" width="80" align="right">
					<? echo change_date_format($ship_date);?> 
                    
                    </td>
                     <td style="border:1px solid black;" width="80" align="right">
                     <? echo number_format($pre_cost_knit,2); ?>
                    </td>
                     <td style="border:1px solid black;" width="80" align="right">
                    <? echo number_format($knit_charge,2); ?>
                    </td>
                  
                    <td style="border:1px solid black;" width="80" align="right">
                       <? $tot_balance=$pre_cost_knit-$knit_charge; echo number_format($tot_balance,2); ?>
                    </td>
                    <td style="border:1px solid black;" width="">
                    <? 
					if( $pre_cost_knit>$knit_charge)
						{
						echo "Less Booking";
						}
					else if ($pre_cost_knit<$knit_charge) 
						{
						echo "Over Booking";
						} 
					else if ($pre_cost_knit==$knit_charge) 
						{
							echo "As Per";
						} 
					else
						{
						echo "";
						}
						?>
                    </td>
                    </tr>
	   <?
	  	 $tot_pre_cost+=$pre_cost_knit;
	  	 $tot_knit_cost+=$knit_charge;
		 $total_balance_knit+=$tot_balance;
	   $i++;
					}
       ?>
	</tbody>
        <tfoot>
            <tr>
                <td style="border:1px solid black;" colspan="4" align="right">  <b>Total</b></td>
                <td style="border:1px solid black;" align="right"> <b><? echo number_format($tot_pre_cost,2); ?></b></td>
                <td style="border:1px solid black;"  align="right"><b> <? echo number_format($tot_knit_cost,2); ?> </b></td>
                <td style="border:1px solid black;"  align="right"><b> <? echo number_format($total_balance_knit,2); ?></b> </td>
                <td style="border:1px solid black;">&nbsp;  </td>
             </tr>
        </tfoot>
    </table>
        
         <br/>
        
		 <?
            echo signature_table(81, $cbo_company_name, "1150px");
			echo "****".custom_file_name($txt_booking_no,$style_sting,$txt_job_no);
         ?>
    </div>
<?
}

if($action=="show_service_booking_report3") 
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
	
	$path=($path=='')?'../../../':$path;

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
                   <img  src='<? echo $path.$imge_arr[$cbo_company_name]; ?>' height='100%' width='100%'/>
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
                            <strong> Service Booking For Knitting:<? echo str_replace("'","",$txt_booking_no); ?></strong>
                             </td>
                            </tr>
                      </table>
                </td>
            </tr>
       </table>
		<?
		$nameArray_job=sql_select(" SELECT b.id as po_id,b.po_number,a.dia_width,a.fin_dia,a.fabric_color_id,a.fin_gsm,a.gmts_color_id,a.fabric_color_id,a.job_no,a.description,a.program_no,a.lib_composition,a.delivery_date, a.delivery_end_date,a.pre_cost_fabric_cost_dtls_id as fab_dtls_id,a.sensitivity,a.rate,a.uom,a.amount,	a.wo_qnty, d.body_part_id, e.style_ref_no , e.id as job_id, d.fabric_description, d.color_type_id, d.id as fabric_id, d.lib_yarn_count_deter_id ,d.gsm_weight ,a.artwork_no,a.printing_color_id,a.id from wo_booking_dtls a join wo_po_break_down b on a.po_break_down_id=b.id join wo_pre_cost_fab_conv_cost_dtls c on c.id=a.pre_cost_fabric_cost_dtls_id join 
		wo_pre_cost_fabric_cost_dtls d on c.fabric_description=d.id join wo_po_details_master e on e.id=b.job_id where 
		a.booking_no=$txt_booking_no and a.entry_form_id=228 and a.status_active=1 group by b.id ,b.po_number, a.dia_width,a.fin_dia, a.fabric_color_id, a.fin_gsm, a.gmts_color_id, a.fabric_color_id, a.job_no, a.description, a.program_no, a.lib_composition, a.delivery_date, a.delivery_end_date, a.pre_cost_fabric_cost_dtls_id , a.sensitivity, a.rate, a.uom, a.amount, a.wo_qnty, d.body_part_id, e.style_ref_no,e.id,d.fabric_description, d.color_type_id, d.id, d.lib_yarn_count_deter_id,d.gsm_weight,a.artwork_no,a.printing_color_id,a.id");
		
		$fabric_atribute_arr=array('body_part_id','fabric_description','dia_width','a.fin_dia','fin_gsm','color_type_id','gsm_weight');
		$fabric_color_attr=array('fabric_color_id','uom','rate','fabric_id','artwork_no','printing_color_id','id');
		$fabric_color_summary_attr=array('fabric_description');
		
		foreach($nameArray_job as $row){
			$sensitivity_prog_arr[$row[csf('job_id')]]['job_no']=$row[csf('job_no')];
			$sensitivity_prog_arr[$row[csf('job_id')]]['style_ref']=$row[csf('style_ref_no')];
			$sensitivity_prog_arr[$row[csf('job_id')]]['po_no'][$row[csf('po_id')]]=$row[csf('po_number')];
			foreach($fabric_atribute_arr as $fabattr){
				$sensitivity_prog_arr[$row[csf('job_id')]]['fabric_data'][$row[csf('fab_dtls_id')]][$row[csf('fin_dia')]][$fabattr]=$row[csf($fabattr)];
			}
			foreach($fabric_color_attr as $fcolorattr){
				$sensitivity_prog_arr[$row[csf('job_id')]]['fabric_data'][$row[csf('fab_dtls_id')]][$row[csf('fin_dia')]]['color_dtls'][$row[csf('fabric_color_id')]][$fcolorattr]=$row[csf($fcolorattr)];				
			}
			$sensitivity_prog_arr[$row[csf('job_id')]]['fabric_data'][$row[csf('fab_dtls_id')]][$row[csf('fin_dia')]]['color_dtls'][$row[csf('fabric_color_id')]]['wo_qnty']+=$row[csf('wo_qnty')];
			$sensitivity_prog_arr[$row[csf('job_id')]]['fabric_data'][$row[csf('fab_dtls_id')]][$row[csf('fin_dia')]]['color_dtls'][$row[csf('fabric_color_id')]]['amount']+=$row[csf('amount')];
		


			$job_po_arr[$row[csf('job_no')]][$row[csf('po_number')]]=$row[csf('po_number')];

			$main_data_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['wo_qnty']+=$row[csf('wo_qnty')];
			$main_data_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['artwork_no']=$row[csf('artwork_no')];		
			$main_data_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['printing_color_id']=$row[csf('printing_color_id')];
			$main_data_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['uom']=$row[csf('uom')];		
			$main_data_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['rate']=$row[csf('rate')];	
			$main_data_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['id']=$row[csf('id')];	

			$main_data_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['color_type_id']=$row[csf('color_type_id')];	
			$main_data_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['gsm_weight']=$row[csf('gsm_weight')];
			$main_data_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['job_no']=$row[csf('job_no')];	
			$main_data_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['style_ref']=$row[csf('style_ref_no')];	
			$main_data_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['amount']+=$row[csf('amount')];	



			$fabric_color_summary[$row[csf('fabric_description')]][$row[csf('color_type_id')]][$row[csf('gsm_weight')]][$row[csf('fin_dia')]]['fabric_color'][$row[csf('fabric_color_id')]]['fabric_color_id']=$row[csf('fabric_color_id')];
			$fabric_color_summary[$row[csf('fabric_description')]][$row[csf('color_type_id')]][$row[csf('gsm_weight')]][$row[csf('fin_dia')]]['fabric_color'][$row[csf('fabric_color_id')]]['wo_qnty']+=$row[csf('wo_qnty')];			
		}

		// echo "<pre>";
		// print_r($main_data_arr);
		
		// 	echo "<pre>";
		// print_r($job_wise_rowspan);
		$suppliar_data=sql_select("SELECT id, contact_no, email,web_site, address_1, address_2, address_3, address_4 from lib_supplier where status_active=1 and is_deleted=0");
		foreach($suppliar_data as $row){
			$supplier_address_arr[$row[csf('id')]]['address']=$row[csf('address_1')].' '.$row[csf('address_2')].' '.$row[csf('address_3')].' '.$row[csf('address_4')];
			$supplier_address_arr[$row[csf('id')]]['contact']=$row[csf('contact_no')];
			$supplier_address_arr[$row[csf('id')]]['email']=$row[csf('email')];
			$supplier_address_arr[$row[csf('id')]]['website']=$row[csf('web_site')];
		}
        $nameArray=sql_select( "select a.booking_no,a.booking_date,a.pay_mode,a.remarks,a.buyer_id,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.source, a.delivery_to, a.attention, a.tenor from wo_booking_mst a where  a.booking_no=$txt_booking_no and a.entry_form=228");
		
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
				<strong>&nbsp;<? echo $com_supp;?></strong><br>
				<? echo $suplier_address;?>
				</td>
				<td width="50%" style="font-size:16px" colspan="6" align="left">
				<strong>&nbsp;<? echo $company_library[$cbo_company_name];?></strong><br>
				<? echo $com_add;?><br>			
				<? echo "TEL:".$contact_no;  ?><br>
				<? echo "Email:".$email;  ?><br>
				<? echo $website;  ?><br>
				</td>
			</tr>
			<tr>
				<th align="left">Issue Date</th>
				<td>&nbsp;<? echo change_date_format($result[csf('booking_date')]);?></td>
				<th align="left">Delivery Date</th>
				<td>&nbsp;<? echo change_date_format($result[csf('delivery_date')]);?></td>
				<th align="left">Contact Person</th>
				<td>&nbsp;<? echo $result[csf('attention')];?></td>
				<th align="left">Buyer</th>
				<td>&nbsp;<? echo $buyer_name_arr[$result[csf('buyer_id')]];?></td>
				<th align="left">Tenor</th>
				<td>&nbsp;<? echo $result[csf('tenor')];?></td>
			</tr>
			<tr>
				<th align="left">Delivery Address</th>
				<td colspan="7">&nbsp;<? echo $result[csf('delivery_to')];?></td>
				<th align="left">Currency</th>
				<td>&nbsp;<? echo $currency[$currency_id];?></td>
			</tr>
			<tr>
				<th align="left">Remarks</th>
				<td colspan="9">&nbsp;<? echo $result[csf('remarks')];?></td>
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
				<th width="160">Gmts. Color</th>
				<th width="160">Fab Color</th>
				<th width="60">Finish Fab Qty</th>
				<th width="60">UOM</th>
				<? if($show_comments==1){?>
				<th width="60">Rate</th>
				<th width="100">Amount</th>
				<?}?>
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
							?>
						<tr>
							<?
							if($j==1){?>
							<td rowspan="<?=$job_id_arr[$job_id];?>"><?= $job_id  ?></td>
							<td rowspan="<?=$job_id_arr[$job_id];?>"><?= $row['style_ref']  ?></td>
							<td rowspan="<?=$job_id_arr[$job_id];?>"><?= implode(", ", $job_po_arr[$job_id])  ?></td>
							<?
							}
							if($b==1){
					
								?>
							<td rowspan="<?=$body_id_arr[$job_id][$body_part_id];?>"><?= $lib_body_part[$body_part_id];  ?></td>
							<?}
							if($fab==1){
							?>
							<td rowspan="<?=$desc_id_arr[$job_id][$body_part_id][$desc_id];?>"><?= $desc_id;  ?></td>							
							<td rowspan="<?=$desc_id_arr[$job_id][$body_part_id][$desc_id];?>"><?= $color_type[$row['color_type_id']];  ?></td>
							<td rowspan="<?=$desc_id_arr[$job_id][$body_part_id][$desc_id];?>"><?= $row['gsm_weight'];  ?></td>	
							<?}?>						
							<td ><?= $dia_id;  ?></td>								
							<td><?= $color_library[$color_id];  ?></td>
							<td><?= $color_library[$color_id];  ?></td>
							<td align="right"><?= number_format($row['wo_qnty'],2)  ?></td>
							<td><?= $unit_of_measurement[$row['uom']]  ?></td>
							<? if($show_comments==1){?>							
							<td align="right"><?= $row['rate']  ?></td>
							<td align="right"><?= number_format($row['amount'],2)  ?></td>
							<?
							$color_wise_amount+=$row['amount'];
							$color_wise_grand_amount+=$row['amount'];
							}?>
							<? 
							$colortr++;
							$color_wise_qty+=$row['wo_qnty'];
							$color_wise_grand_qty+=$row['wo_qnty'];
							$fab++;
							$f2++;$f++;$j++;$b++;
							?>
								<!-- <tr>
								<th colspan="7" align="right">Fabric Total</th>
								<th align="right"><?= number_format($color_wise_qty,2) ?></th>
								<th></th>
								<th></th>
								<th align="right"><?= number_format($color_wise_amount,2) ?></th>
								</tr> -->
					</tr>
				<? } }}}}

				if($currency_id==1){$paysa_sent="Paisa";} else if($currency_id==2){$paysa_sent="CENTS";}
			?>
			<tr>
				<th colspan="10" align="right">Grand Total</th>
				<th align="right"><?= number_format($color_wise_grand_qty,2) ?></th>
				<th></th>
				<? if($show_comments==1){?>
				<th></th>
				<th align="right"><?= number_format($color_wise_grand_amount,2) ?></th>
				<?}?>
			</tr>
			<tr>
				<th colspan="10" align="right">Total Booking Amount (in word)</th>
				<th colspan="4" align="left"><? echo number_to_words(def_number_format($color_wise_grand_amount,2,""),$currency[$currency_id],$paysa_sent); ?></th>
			</tr>
		</table>
		<br><br>
		<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
			<tr><th colspan="6" align="center">Fab Description & Color Wise Summary</th></tr>
			<tr>
				<th>Fabrication</th>
				<th>Color Type</th>
				<th>GSM</th>
				<th>Dia</th>
				<th>Fab. Color</th>
				<th>Fin. Fab. Qty</th>
			</tr>
			<? 
			// $fabric_color_summary[$row[csf('fabric_description')]][$row[csf('color_type_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['fabric_color'][$row[csf('fabric_color_id')]]['wo_qnty']+=$row[csf('wo_qnty')];
			$rowspan=array();
			foreach($fabric_color_summary as $desc_id=>$desc_data){
						
				foreach($desc_data as $colorty_id=>$colorty_data){
					foreach($colorty_data as $gsm_id=>$gsm_data){
						foreach($gsm_data as $dia_id=>$dia_data){
							foreach($dia_data['fabric_color'] as $color_summ){ 
							$rowspan[$desc_id][$colorty_id][$gsm_id]+=1;
							$rowspan2[$desc_id][$colorty_id][$gsm_id][$dia_id]+=1;


						}}
					}
				}
			}
			// echo "<pre>";
			// print_r($rowspan2);
			
			foreach($fabric_color_summary as $desc_id=>$desc_data){
				$color_wise_qty_summ=0;	
				foreach($desc_data as $colorty_id=>$colorty_data){
					foreach($colorty_data as $gsm_id=>$gsm_data){
						$tr=1;	
						foreach($gsm_data as $dia_id=>$dia_data){
				?>
				<tr>
					<?
						if($tr==1){?>
					<td rowspan="<?=$rowspan[$desc_id][$colorty_id][$gsm_id];?>"><?= $desc_id;  ?></td>
					<td rowspan="<?=$rowspan[$desc_id][$colorty_id][$gsm_id];?>"><?= $color_type[$colorty_id]  ?></td>
					<td rowspan="<?=$rowspan[$desc_id][$colorty_id][$gsm_id];?>"><?= $gsm_id  ?></td>
					<? 
						$tr++;	}?>
					<td rowspan="<?=$rowspan2[$desc_id][$colorty_id][$gsm_id][$dia_id];?>"><?= $dia_id  ?></td>
					<?
						$colorsummtr=1;	
					foreach($dia_data['fabric_color'] as $color_summ){ 
						if($colorsummtr!=1) echo '<tr>'	
					?>
						<td align="right"><?= $color_library[$color_summ['fabric_color_id']]  ?></td>
						<td align="right"><?= number_format($color_summ['wo_qnty'],2)  ?></td>
					<? 
					$colorsummtr++;$tr++;
					$color_wise_qty_summ+=$color_summ['wo_qnty'];
					} 
				?>
				</tr>
				
				<?
			}}}
			?>
			<tr>
				<th colspan="5" align="right">Fabric Total :</th>
				<th align="right"><?= number_format($color_wise_qty_summ,2)  ?></th>
			</tr>
			<?
		}
		?>
		</table>
          
       	<table  width="100%" class="rpt_table" style="margin:5px;font-size:16px;font-family:'Arial Narrow';"   border="1" cellpadding="0" cellspacing="0">
          <tr>
            <td><? echo get_spacial_instruction($txt_booking_no); ?></td>
          </tr>
        </table>
        <br/>
		<?
            echo signature_table(150, $cbo_company_name, "1113px");
        ?>
    </div>
	<?
}

if($action=="show_service_booking_report6") //shariar 7379
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
                            <strong> Service Booking For Knitting:<? echo str_replace("'","",$txt_booking_no); ?></strong>
                             </td>
                            </tr>
                      </table>
                </td>
            </tr>
       </table>
		<?
		$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
		$nameArray_count=sql_select(" SELECT a.job_no, d.fabric_description,a.fin_dia,a.fabric_color_id,f.count_id from wo_booking_dtls a join wo_po_break_down b on a.po_break_down_id=b.id join wo_pre_cost_fab_conv_cost_dtls c on c.id=a.pre_cost_fabric_cost_dtls_id join 
		wo_pre_cost_fabric_cost_dtls d on c.fabric_description=d.id join wo_po_details_master e on e.id=b.job_id   JOIN wo_pre_cost_fab_yarn_cost_dtls f on e.id = f.job_id where 
		a.booking_no=$txt_booking_no and a.entry_form_id=228 and a.status_active=1 group by a.job_no, d.fabric_description,a.fin_dia,a.fabric_color_id,f.count_id");
		$fab_arr=array('fabric_description','dia_width','fin_dia','fin_gsm','color_type_id','gsm_weight');
		foreach($nameArray_count as $row){
			$main_data_arr[$row[csf('job_no')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['count_id'].=$yarn_count_arr[$row[csf('count_id')]].",";	
			$fabric_color_summary[$row[csf('fabric_color_id')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]]['count_id'].=$yarn_count_arr[$row[csf('count_id')]].",";
		}

		$nameArray_job=sql_select(" SELECT b.id as po_id,b.po_number,a.dia_width,a.fin_dia,a.fabric_color_id,a.fin_gsm,a.gmts_color_id,a.fabric_color_id,a.job_no,a.description,a.program_no,a.lib_composition,a.delivery_date, a.delivery_end_date,a.pre_cost_fabric_cost_dtls_id as fab_dtls_id,a.sensitivity,a.rate,a.uom,a.amount,	a.wo_qnty,a.grey_fab_qnty, d.body_part_id, e.style_ref_no , e.id as job_id, d.fabric_description, d.color_type_id, d.id as fabric_id, d.lib_yarn_count_deter_id ,d.gsm_weight ,a.artwork_no,a.printing_color_id,a.id from wo_booking_dtls a join wo_po_break_down b on a.po_break_down_id=b.id join wo_pre_cost_fab_conv_cost_dtls c on c.id=a.pre_cost_fabric_cost_dtls_id join 
		wo_pre_cost_fabric_cost_dtls d on c.fabric_description=d.id join wo_po_details_master e on e.id=b.job_id where 
		a.booking_no=$txt_booking_no and a.entry_form_id=228 and a.status_active=1 group by b.id ,b.po_number, a.dia_width,a.fin_dia, a.fabric_color_id, a.fin_gsm, a.gmts_color_id, a.fabric_color_id, a.job_no, a.description, a.program_no, a.lib_composition, a.delivery_date, a.delivery_end_date, a.pre_cost_fabric_cost_dtls_id , a.sensitivity, a.rate, a.uom, a.amount, a.wo_qnty,a.grey_fab_qnty, d.body_part_id, e.style_ref_no,e.id,d.fabric_description, d.color_type_id, d.id, d.lib_yarn_count_deter_id,d.gsm_weight,a.artwork_no,a.printing_color_id,a.id");
		
		$fabric_atribute_arr=array('fabric_description','dia_width','a.fin_dia','fin_gsm','color_type_id','gsm_weight');
		$fabric_color_attr=array('fabric_color_id','uom','rate','fabric_id','artwork_no','printing_color_id','id');
		$fabric_color_summary_attr=array('fabric_description');	
		
		foreach($nameArray_job as $row){
			$sensitivity_prog_arr[$row[csf('job_id')]]['job_no']=$row[csf('job_no')];
			$sensitivity_prog_arr[$row[csf('job_id')]]['style_ref']=$row[csf('style_ref_no')];
			$sensitivity_prog_arr[$row[csf('job_id')]]['count_id']=$yarn_count_arr[$row[csf('count_id')]];
			$sensitivity_prog_arr[$row[csf('job_id')]]['po_no'][$row[csf('po_id')]]=$row[csf('po_number')];
			foreach($fabric_atribute_arr as $fabattr){
				$sensitivity_prog_arr[$row[csf('job_id')]]['fabric_data'][$row[csf('fab_dtls_id')]][$row[csf('fin_dia')]][$fabattr]=$row[csf($fabattr)];
			}
			foreach($fabric_color_attr as $fcolorattr){
				$sensitivity_prog_arr[$row[csf('job_id')]]['fabric_data'][$row[csf('fab_dtls_id')]][$row[csf('fin_dia')]]['color_dtls'][$row[csf('fabric_color_id')]][$fcolorattr]=$row[csf($fcolorattr)];				
			}
			$sensitivity_prog_arr[$row[csf('job_id')]]['fabric_data'][$row[csf('fab_dtls_id')]][$row[csf('fin_dia')]]['color_dtls'][$row[csf('fabric_color_id')]]['wo_qnty']+=$row[csf('wo_qnty')];
			$sensitivity_prog_arr[$row[csf('job_id')]]['fabric_data'][$row[csf('fab_dtls_id')]][$row[csf('fin_dia')]]['color_dtls'][$row[csf('fabric_color_id')]]['grey_fab_qnty']+=$row[csf('grey_fab_qnty')];
			$sensitivity_prog_arr[$row[csf('job_id')]]['fabric_data'][$row[csf('fab_dtls_id')]][$row[csf('fin_dia')]]['color_dtls'][$row[csf('fabric_color_id')]]['amount']+=$row[csf('amount')];
		


			$job_po_arr[$row[csf('job_no')]][$row[csf('po_number')]]=$row[csf('po_number')];

			$main_data_arr[$row[csf('job_no')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['wo_qnty']+=$row[csf('wo_qnty')];
			$main_data_arr[$row[csf('job_no')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['grey_fab_qnty']+=$row[csf('grey_fab_qnty')];
			$main_data_arr[$row[csf('job_no')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['artwork_no']=$row[csf('artwork_no')];		
			$main_data_arr[$row[csf('job_no')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['printing_color_id']=$row[csf('printing_color_id')];
			$main_data_arr[$row[csf('job_no')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['uom']=$row[csf('uom')];		
			$main_data_arr[$row[csf('job_no')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['rate']=$row[csf('rate')];	
			$main_data_arr[$row[csf('job_no')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['id']=$row[csf('id')];	

			$main_data_arr[$row[csf('job_no')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['color_type_id']=$row[csf('color_type_id')];	
			$main_data_arr[$row[csf('job_no')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['gsm_weight']=$row[csf('gsm_weight')];
			$main_data_arr[$row[csf('job_no')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['job_no']=$row[csf('job_no')];	
			$main_data_arr[$row[csf('job_no')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['style_ref']=$row[csf('style_ref_no')];
			//$main_data_arr[$row[csf('job_no')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['count_id'].=$yarn_count_arr[$row[csf('count_id')]].",";	
			$main_data_arr[$row[csf('job_no')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['amount']+=$row[csf('amount')];	

			$fabric_color_summary[$row[csf('fabric_color_id')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]]['fabric_color_id']=$row[csf('fabric_color_id')];
			$fabric_color_summary[$row[csf('fabric_color_id')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]]['fabric_description']=$row[csf('fabric_description')];
			//$fabric_color_summary[$row[csf('fabric_color_id')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]]['count_id'].=$yarn_count_arr[$row[csf('count_id')]].",";
			$fabric_color_summary[$row[csf('fabric_color_id')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]]['gsm_weight']=$row[csf('gsm_weight')];
			$fabric_color_summary[$row[csf('fabric_color_id')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]]['fin_dia']=$row[csf('fin_dia')];
			$fabric_color_summary[$row[csf('fabric_color_id')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]]['wo_qnty']+=$row[csf('wo_qnty')];
			$fabric_color_summary2[$row[csf('fabric_color_id')]]['tot_qnty']+=$row[csf('wo_qnty')];			
		}

		   /*  echo "<pre>";
		 print_r($fabric_color_summary);  die;  */ 
		
	
		// 	echo "<pre>";
		// print_r($job_wise_rowspan);
		$suppliar_data=sql_select("SELECT id, contact_no, email,web_site, address_1, address_2, address_3, address_4 from lib_supplier where status_active=1 and is_deleted=0");
		foreach($suppliar_data as $row){
			$supplier_address_arr[$row[csf('id')]]['address']=$row[csf('address_1')].' '.$row[csf('address_2')].' '.$row[csf('address_3')].' '.$row[csf('address_4')];
			$supplier_address_arr[$row[csf('id')]]['contact']=$row[csf('contact_no')];
			$supplier_address_arr[$row[csf('id')]]['email']=$row[csf('email')];
			$supplier_address_arr[$row[csf('id')]]['website']=$row[csf('web_site')];
		}
        $nameArray=sql_select( "select a.booking_no,a.booking_date,a.pay_mode,a.remarks,a.buyer_id,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.source, a.delivery_to, a.attention, a.tenor from wo_booking_mst a where  a.booking_no=$txt_booking_no and a.entry_form=228");
		

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
				<strong>&nbsp;<? echo $com_supp;?></strong><br>
				<? echo $suplier_address;?>
				</td>
				<td width="50%" style="font-size:16px" colspan="6" align="left">
				<strong>&nbsp;<? echo $company_library[$cbo_company_name];?></strong><br>
				<? echo $com_add;?><br>			
				<? echo "TEL:".$contact_no;  ?><br>
				<? echo "Email:".$email;  ?><br>
				<? echo $website;  ?><br>
				</td>

			</tr>
			<tr>
				<th align="left">Issue Date</th>
				<td>&nbsp;<? echo change_date_format($result[csf('booking_date')]);?></td>
				<th align="left">Delivery Date</th>
				<td>&nbsp;<? echo change_date_format($result[csf('delivery_date')]);?></td>
				<th align="left">Contact Person</th>
				<td>&nbsp;<? echo $result[csf('attention')];?></td>
				<th align="left">Buyer</th>
				<td>&nbsp;<? echo $buyer_name_arr[$result[csf('buyer_id')]];?></td>
				<th align="left">Tenor</th>
				<td>&nbsp;<? echo $result[csf('tenor')];?></td>
			</tr>
			<tr>
				<th align="left">Delivery Address</th>
				<td colspan="7">&nbsp;<? echo $result[csf('delivery_to')];?></td>
				<th align="left">Currency</th>
				<td>&nbsp;<? echo $currency[$currency_id];?></td>
			</tr>
			<tr>
				<th align="left">Remarks</th>
				<td colspan="9">&nbsp;<? echo $result[csf('remarks')];?></td>
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
				<th width="100">Yarn Count</th>
				<th width="250">Fab Description</th>
				<th width="100">Fab Color</th>
				<th width="80">GSM</th>
				<th width="60">Fab Dia</th>
				<th width="160">Color Type</th>
				<th width="60">Grey Fab Qty</th>
				<? if($show_comments==1){?>
				<th width="60">Rate</th>
				<th width="100">Amount</th>
				<?}?>
			</tr>
			<? 
			foreach($main_data_arr as $job_id=>$job_data){
				$job_rowspan=0;
					foreach($job_data as $desc_id=>$gsm_data){
						$desc_rowspan=0;
						foreach($gsm_data as $dia_id=>$dia_data){
							$dia_rowspan=0;
							foreach($dia_data as $color_id=>$row){

								$job_rowspan++;
								$desc_rowspan++;
								$dia_rowspan++;

							}
							$job_id_arr[$job_id]=$job_rowspan;
							$desc_id_arr[$job_id][$desc_id]=$desc_rowspan;
							$dia_id_arr[$job_id][$desc_id][$dia_id]=$dia_rowspan;
						}

					}
				
			}

					// echo "<pre>";
					// print_r($rowc);

		foreach($main_data_arr as $job_id=>$job_data){
			$j=1;
				foreach($job_data as $desc_id=>$gsm_data){
					$fab=1;
					foreach($gsm_data as $dia_id=>$dia_data){
						$d=1;
						foreach($dia_data as $color_id=>$row){
						
					 	?>
					<tr>
					<?
							if($j==1){?>
							<td rowspan="<?=$job_id_arr[$job_id];?>"><?= $job_id  ?></td>
							<td rowspan="<?=$job_id_arr[$job_id];?>"><?= $row['style_ref']  ?></td>
							<td rowspan="<?=$job_id_arr[$job_id];?>"><?= implode(", ", $job_po_arr[$job_id])  ?></td>
							<td rowspan="<?=$job_id_arr[$job_id];?>"><?= rtrim($row['count_id'],",")  ?></td>
							<? 

							}
							
							if($fab==1){
							?>
							<td rowspan="<?=$desc_id_arr[$job_id][$desc_id];?>"><?= $desc_id;  ?></td>
							<?}?>								
							<td><?= $color_library[$color_id];  ?></td>
							<td><?= $row['gsm_weight'];  ?></td>	
												
							<td ><?= $dia_id;  ?></td>								
							<td><?= $color_type[$row['color_type_id']];  ?></td>
							<td align="right"><?= number_format($row['wo_qnty'],2)  ?></td>
							<? if($show_comments==1){?>							
							<td align="right"><?= $row['rate']  ?></td>
							<td align="right"><?= number_format($row['amount'],2)  ?></td>
							<?
								$color_wise_amount+=$row['amount'];
								$color_wise_grand_amount+=$row['amount'];
							}?>
							<? 
							$colortr++;
							$color_wise_qty+=$row['wo_qnty'];
							
							$color_wise_grand_qty+=$row['wo_qnty'];
						
							 ?>
							
							
						<? 
							$fab++;
							$f2++;$f++;$j++;
						
						?>						
					</tr>
				<? } }}}

				if($currency_id==1){$paysa_sent="Paisa";} else if($currency_id==2){$paysa_sent="CENTS";}
			?>
				<tr>
				<th colspan="9" align="right">Grand Total</th>
				<th align="right"><?= number_format($color_wise_grand_qty,2) ?></th>
				<? if($show_comments==1){?>
				<th></th>
				<th align="right"><?= number_format($color_wise_grand_amount,2) ?></th>
				<?}?>
			</tr>
			<tr>
				<th colspan="3" align="left">Total Booking Amount (in word)</th>
				<th colspan="8" align="left"><? echo number_to_words(def_number_format($color_wise_grand_amount,2,""),$currency[$currency_id],$paysa_sent); ?></th>
			</tr>
		</table>
		<br><br>
		<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
			<tr><th colspan="7" align="center">Color Wise Summary</th></tr>
			<tr>
				<th>Fab. Color</th>
				<th>Yarn Count</th>
				<th>Fabrication</th>
				<th>GSM</th>
				<th>Dia</th>
				<th>Quantity</th>
				<th>Color Wise Total</th>
			</tr>
			<? 

		foreach($fabric_color_summary as $color_id=>$color_data){
			$color_rowspan=0;
			foreach($color_data as $desc_id=>$gsm_data){
				$desc_rowspan=0;
				foreach($gsm_data as $dia_id=>$row){
			
					$color_rowspan++;
					$desc_rowspan++;

				}
				$color_id_arr[$color_id]=$color_rowspan;
				$desc_id_arr[$color_id][$desc_id]=$desc_rowspan;
			}}

			foreach($fabric_color_summary as $color_id=>$color_data){
				$j=1;
				$color_wise_qty_summ=0;	
					foreach($color_data as $desc_id=>$gsm_data){
						$fab=1;
						foreach($gsm_data as $dia_id=>$row){
	
							$total_qty=	$fabric_color_summary2[$color_id]['tot_qnty'];
							 ?>
						<tr>
						<?
								if($j==1){?>
								<td rowspan="<?=$color_id_arr[$color_id];?>"><?= $color_library[$color_id]  ?></td>
								<td rowspan="<?=$color_id_arr[$color_id];?>"><?= rtrim($row['count_id'],",")  ?></td>
								<? 
	
								}
								
								if($fab==1){
								?>
								<td rowspan="<?=$desc_id_arr[$color_id][$desc_id];?>"><?= $desc_id;  ?></td>
								<?}?>								
								<td><?= $row['gsm_weight'];  ?></td>	
													
								<td ><?= $dia_id;  ?></td>								
								<td align="right"><?= number_format($row['wo_qnty'],2)  ?></td>
								
								<?
								if($j==1){?>
								<td align="right" rowspan="<?=$color_id_arr[$color_id];?>"><?= number_format($total_qty,2);$grand_total+=$total_qty  ?></td>
								<?}?>	

							
								
								
							<? 
								$fab++;
								$j++;
								
							?>						
						</tr>
					<? } }?>

				<?
			}
			?>
			<tr>
					<th colspan="5" align="right">Fabric Grand Total :</th>
					<th align="right"><?= number_format($grand_total,2)  ?></th>
				</tr>
		</table>
			
         

       	<table  width="100%" class="rpt_table" style="margin:5px;font-size:16px;font-family:'Arial Narrow';"   border="1" cellpadding="0" cellspacing="0">
          <tr>
          <td><? echo get_spacial_instruction($txt_booking_no); ?></td>
          </tr>
        </table>
         <br/>
		 <?
            echo signature_table(82, $cbo_company_name, "1113px");
         ?>
    </div>

	<?
}

if($action=="show_service_booking_report4") //For Shariar 1047
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
    $cbo_template_id = str_replace("'", "", $cbo_template_id);
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library  where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$company_address_arr=return_library_array( "select id,city from   lib_company",'id','city');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	//$po_qnty_tot=return_field_value( "style_ref_no", "wo_po_details_master","job_no in(".str_replace("'","",$txt_order_no_id).")");
	$job_no="";$style_ref="";
	$nameArray_job=sql_select( "select distinct b.job_no,a.buyer_id,c.style_ref_no as style  from wo_booking_mst a, wo_booking_dtls b,wo_po_details_master c  where a.booking_no=b.booking_no and c.job_no=b.job_no and a.booking_no=$txt_booking_no and b.status_active=1");
		$buyer_name=$nameArray_job[0][csf('buyer_id')];
        foreach ($nameArray_job as $result_job)
        {
			$job_no.="'".$result_job[csf('job_no')]."'".",";
			$style_ref.=$result_job[csf('style')].",";
			$job_num=$result_job[csf('job_no')];

		}
	$po_no=""; $po_ids=""; $interRef="";
	$nameArray_job=sql_select( "select distinct b.id, b.po_number, b.grouping from wo_booking_dtls a, wo_po_break_down b  where a.po_break_down_id=b.id  and a.booking_no=$txt_booking_no and a.status_active=1 and b.status_active=1");
	foreach ($nameArray_job as $result_job)
	{
		$po_no.=$result_job[csf('po_number')].",";
		$interRef.=$result_job[csf('grouping')].",";
		if($po_ids!="") $po_ids.=",".$result_job[csf('id')];else $po_ids=$result_job[csf('id')];
	}

	$show_comments=str_replace("'","",$show_comments);
	//echo $show_comments;
	if($show_comments==0)
	{
		$col_span=2;
		$hide_show="display:none";
	}
	else
	{
		$col_span=0;
		$hide_show="";
	}
	$job_nos=str_replace("'","",$job_no);
	$copy_no=array(1,2); //for Dynamic Copy here
	ob_start();
	foreach($copy_no as $cid)
	{
	?>
	<div style="width:1333px" align="center">
        <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black">
            <tr>
                <td width="100">
                	<img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
                </td>
                <td width="1000">
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:18px;"><?=$company_library[$cbo_company_name]; ?>
                            <span style="font-size:x-large; margin-left:10px !important;"> <? echo $cid;?><sup><?php if ($cid == 1) {echo 'st';} elseif ($cid == 2) {echo 'nd';} else {echo 'rd';}?></sup> Copy</span>
                            </td>
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
                            <td align="center" style="font-size:16px">
                            	<strong>Service Booking Sheet</strong>
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="100" style="font-size:18px">
                	<strong> Style Ref: &nbsp; </strong>
                </td>
                <td width="250" style="font-size:18px">
                	<strong><?=$style_ref=rtrim($style_ref,','); ?> </strong>
                </td>
            </tr>
        </table>
		<?
		$booking_grand_total=0;
		//$job_no="";
		$currency_id="";

        $nameArray=sql_select( "select a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.source,a.pay_mode,remarks  from wo_booking_mst a where  a.booking_no=$txt_booking_no and a.entry_form=228");
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
                    <td width="100"><span style="font-size:12px"><b>Delivery Date</b></span></td>
                    <td width="110">:&nbsp;<? echo change_date_format($result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>
                </tr>
                <tr>
                    <td width="100" style="font-size:12px"><b>Conversion Currency</b></td>
                    <td width="110">:&nbsp;<? $currency_id=1; echo $currency[$currency_id];//ISD-23-11716 //$currency_id=$result[csf('currency_id')]; echo $currency[$result[csf('currency_id')]]; ?></td>
                    <td  width="100" style="font-size:12px"><b>Conversion Rate</b></td>
                    <td  width="110" >:&nbsp;<? echo $result[csf('exchange_rate')]; ?></td>
                    <td  width="100" style="font-size:12px"><b>Source</b></td>
                    <td  width="110" >:&nbsp;<? echo $source[$result[csf('source')]]; ?></td>
                </tr>
                <tr>
                    <td width="100" style="font-size:12px"><b>Supplier Name</b>   </td>
                    <td width="110">:&nbsp;<?
                    if($result[csf("pay_mode")]!=3 && $result[csf("pay_mode")]!=5)
                    {
                    echo $supplier_name_arr[$result[csf('supplier_id')]];
                    }
                    else
                    {
                    echo $company_library[$result[csf('supplier_id')]];
                    }
                    ?>    </td>
                    <td width="100" style="font-size:12px"><b>Supplier Address</b></td>
                    <td width="110">:&nbsp;<?
                    if($result[csf("pay_mode")]!=3 && $result[csf("pay_mode")]!=5)
                    {
                    echo $supplier_address_arr[$result[csf('supplier_id')]];
                    }
                    else
                    {
                    echo $company_address_arr[$result[csf('supplier_id')]];
                    }
                    ?></td>
                    <td  width="100" style="font-size:12px"><b>Attention</b></td>
                    <td  width="110" >:&nbsp;<? echo $result[csf('attention')]; ?></td>
                </tr>
                <tr>
                    <td width="100" style="font-size:12px"><b>Job No</b>   </td>
                    <td width="110">:&nbsp;<?=$job_nos=rtrim($job_nos,','); ?></td>
                    <td width="110" style="font-size:12px"><b>PO No</b> </td>
                    <td  width="100" style="font-size:12px" >:&nbsp;<?
                    $po_nos= rtrim($po_no,',');
                    $po_nos=implode(", ",array_unique(explode(",",$po_nos)));
                    echo $po_nos;
                    ?> </td>
                    <td width="110" style="font-size:12px"><b>Buyer Name</b>   </td>
                    <td width="100" >:&nbsp;<?=$buyer_name_arr[$buyer_name]; ?></td>
                </tr>
                <tr>
                    <td width="100" style="font-size:12px"><b>Internal Ref.</b></td><!--ISD-21-01857-->
                    <td width="110" style="word-break:break-all">:&nbsp;<?=implode(",",array_filter(array_unique(explode(",",$interRef)))); ?></td>
                    <td width="110" style="font-size:12px"><b>&nbsp;</b> </td>
                    <td width="100" style="font-size:12px">&nbsp;</td>
                    <td width="110" style="font-size:12px"><b>Remarks</b></td>
                    <td width="100">:&nbsp;<? echo $result[csf('remarks')]; ?></td>
                </tr>
			</table>
			<?
        }
        //-==============================================AS PER GMTS COLOR START=========================================  -->
		//========================================
	$fabric_description_array=array();
	$wo_pre_cost_fab_conv_cost_dtls_id=sql_select("select id,fabric_description,cons_process from wo_pre_cost_fab_conv_cost_dtls  where  job_no in(".rtrim($job_no,", ").")");
	foreach( $wo_pre_cost_fab_conv_cost_dtls_id as $row_wo_pre_cost_fab_conv_cost_dtls_id)
	{
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]!=0)
		{
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description,gsm_weight from  wo_pre_cost_fabric_cost_dtls  where  id='".$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]."'");//gsm_weight
			list($fabric_description_row)=$fabric_description;
			$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]]=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")].', '.$fabric_description_row[csf("gsm_weight")];//.', '.$fabric_description_row[csf("gsm_weight")]
		}
		if($row_wo_pre_cost_fab_conv_cost_dtls_id[csf("fabric_description")]==0)
		{
			$fabric_description=sql_select("select id,body_part_id,color_type_id,fabric_description,gsm_weight from  wo_pre_cost_fabric_cost_dtls  where   job_no in(".rtrim($job_no,", ").")");//gsm_weight
			foreach( $fabric_description as $fabric_description_row)
	        {
				$fabric_description_array[$row_wo_pre_cost_fab_conv_cost_dtls_id[csf("id")]].=$body_part[$fabric_description_row[csf("body_part_id")]].', '.$color_type[$fabric_description_row[csf("color_type_id")]].', '.$fabric_description_row[csf("fabric_description")].", ".$fabric_description_row[csf("gsm_weight")];//.', '.$fabric_description_row[csf("gsm_weight")]
			}
		}


	}
	//print_r($fabric_description_array);
	//=================================================
    $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1 and wo_qnty !=0 and is_deleted=0 and status_active=1" );

	$nameArray_color2=sql_select( "select distinct fabric_color_id,gmts_color_id from wo_booking_dtls   where  booking_no=$txt_booking_no and sensitivity=1 and wo_qnty !=0 and is_deleted=0 and status_active=1");
	foreach( $nameArray_color2 as $row)
	{
		$fab_gmt_color_arr[$row[csf('fabric_color_id')]]=$row[csf('gmts_color_id')];
	}
		
	$only_color_size_qnty=sql_select( "select fabric_color_id, gmts_color_id, slength, fin_dia as dia_width, fin_gsm, rate, description, sum(wo_qnty) as cons from wo_booking_dtls where booking_no=$txt_booking_no and sensitivity=1 and status_active=1 and is_deleted =0 $process_cond group by fabric_color_id, gmts_color_id, slength, fin_dia, fin_gsm, rate, description");
			
	foreach($only_color_size_qnty as $OnlyColorSizeQnty)
	{
		if($OnlyColorSizeQnty[csf('cons')])
		{
		 	$arrOnlyColQty[$OnlyColorSizeQnty[csf('fabric_color_id')]][$OnlyColorSizeQnty[csf('gmts_color_id')]][$OnlyColorSizeQnty[csf('description')]][$OnlyColorSizeQnty[csf('slength')]][$OnlyColorSizeQnty[csf('dia_width')]][$OnlyColorSizeQnty[csf('fin_gsm')]][$OnlyColorSizeQnty[csf('rate')]]['cons']=$OnlyColorSizeQnty[csf('cons')];
		}
	}

    $nameArray_color=sql_select( "select distinct fabric_color_id,gmts_color_id from wo_booking_dtls   where  booking_no=$txt_booking_no and sensitivity=1 and wo_qnty !=0 and is_deleted=0 and status_active=1");
	$nameArray_color_labdib=sql_select( "select  labdip_no from wo_booking_dtls   where  booking_no=$txt_booking_no and sensitivity=1 and wo_qnty !=0 and is_deleted=0 and status_active=1");
	$nameArray_booking_main=sql_select( "select  booking_no from wo_booking_mst   where  job_no in(".rtrim($job_no,", ").") and is_deleted=0 and status_active=1 and entry_form=118");
	foreach( $nameArray_booking_main as $row)
	{
		$booking_no.=$row[csf('booking_no')].',';
	}

	if(count($nameArray_color)>0)
	{
	?>
	<table border="0" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" >
		<tr>
			<td colspan="<? echo count($nameArray_color)+10; ?>" align="">
			<strong>As Per Garments Color</strong>
			</td>
		</tr>
		<tr>
			<td align="center" style="border:1px solid black" rowspan="3"><strong>Sl</strong> </td>
			<td align="center" style="border:1px solid black" rowspan="3"><strong>Service Type</strong> </td>
			<td align="center" style="border:1px solid black" rowspan="3"><strong>Item Description</strong> </td>
			<td align="center" style="border:1px solid black" width="80" rowspan="3"><strong>Y.Count</strong> </td>
			<td align="center" style="border:1px solid black" width="60" rowspan="3"><strong>Lot</strong> </td>
			<td align="center" style="border:1px solid black" width="80" rowspan="3"><strong>Brand</strong> </td>
			<td align="center" style="border:1px solid black" rowspan="3"><strong>Booking No</strong> </td>
			<td align="center" style="border:1px solid black"><strong>Item Color</strong> </td>
			<?
			foreach($nameArray_color  as $result_color)
			{	     ?>
			<td align="center" style="border:1px solid black"><strong><? echo $color_library[$result_color[csf('fabric_color_id')]];?></strong></td>
			<?	}    ?>
			<td style="border:1px solid black" align="center" rowspan="3"><strong>Total</strong></td>
			<td style="border:1px solid black" align="center" rowspan="3"><strong>M/C Dia</strong></td>
			<td style="border:1px solid black" align="center" rowspan="3"><strong>Fin Dia</strong></td>
			<td style="border:1px solid black" align="center" rowspan="3"><strong>Fin GSM</strong></td>
			<td style="border:1px solid black" align="center" rowspan="3"><strong>S/Length</strong></td>
			<td style="border:1px solid black" align="center" rowspan="3"><strong>UOM</strong></td>
			<td style="border:1px solid black;<? echo $hide_show;?>" align="center" rowspan="3"><strong>Rate[BDT]</strong></td>
			<td style="border:1px solid black;<? echo $hide_show;?>" align="center" rowspan="3"><strong>Amount[BDT]</strong></td>
		</tr>
		<tr>
			<td style="border:1px solid black"><strong>Gmts Color </strong> </td>
			<?
			foreach($nameArray_color  as $result_color)
			{
			$gmt_color=$color_library[$fab_gmt_color_arr[$result_color[csf('fabric_color_id')]]];
				?>
			<td align="center" style="border:1px solid black"><strong><? echo $gmt_color;//$color_library[$result_color[csf('fabric_color_id')]];?></strong></td>
			<?	}    ?>

		</tr>
		<tr>
			<td style="border:1px solid black"><strong>Labdip No</strong> </td>
			<?
			foreach($nameArray_color_labdib  as $row)
			{	     ?>
			<td align="center" style="border:1px solid black"><strong><? echo $row[csf('labdip_no')];?></strong></td>
			<?	}    ?>
		</tr>
		<?
		$i=0;
		$grand_total_as_per_gmts_color=0;
		foreach($nameArray_item as $result_item)
		{
			$i++;
			if($result_item[csf('process')]=='' || $result_item[csf('process')]==0)
			{
				$process_id="";
			}
			else
			{
				$process_id="and process=".$result_item[csf('process')]."";
			}

	$group_con="listagg(cast(yarn_count as varchar2(4000)),',') within group (order by yarn_count) AS yarn_count,listagg(cast(lot_no as varchar2(4000)),',') within group (order by lot_no) AS lot_no,listagg(cast(brand as varchar2(4000)),',') within group (order by brand) AS brand";

	$nameArray_item_description=sql_select( "select distinct description, rate, uom, fin_dia as dia_width, mc_dia, fin_gsm, slength,$group_con from wo_booking_dtls where booking_no=$txt_booking_no and sensitivity=1 and wo_qnty !=0  $process_id and status_active=1 and is_deleted=0 group by description, rate, uom, fin_dia, mc_dia, fin_gsm, slength");
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
			$yarn_count=implode(',',array_unique(explode(",",$result_itemdescription[csf('yarn_count')])));
			$lot_no=implode(',',array_unique(explode(",",$result_itemdescription[csf('lot_no')])));
			$brand=implode(',',array_unique(explode(",",$result_itemdescription[csf('brand')])));
			?>
			<td style="border:1px solid black"><? echo rtrim($fabric_description_array[$result_itemdescription[csf('description')]],", "); ?> </td>
			<td style="border:1px solid black" width="80"><? echo $yarn_count; ?> </td>
			<td style="border:1px solid black" width="60"><? echo $lot_no; ?> </td>
			<td style="border:1px solid black" width="80"><? echo $brand; ?> </td>
			<td align="center" style="border:1px solid black"><strong>

			<? 
				$booking_num=implode(',',array_unique(explode(",",$booking_no)));
				echo chop($booking_num,",") ;?>

			</strong></td>
			<td style="border:1px solid black">Booking Qnty </td>
			<? foreach($nameArray_color  as $result_color) { ?>
			<td style="border:1px solid black; text-align:right">
			<?
				$color_cons=0;
				$color_cons=$arrOnlyColQty[$result_color[csf('fabric_color_id')]][$result_color[csf('gmts_color_id')]][$result_itemdescription[csf('description')]][$result_itemdescription[csf('slength')]][$result_itemdescription[csf('dia_width')]][$result_itemdescription[csf('fin_gsm')]][$result_itemdescription[csf('rate')]]['cons'];
				echo number_format($color_cons,2);

				$item_desctiption_total+=$color_cons;
				if (array_key_exists($result_color[csf('fabric_color_id')], $color_tatal))
				{
					$color_tatal[$result_color[csf('fabric_color_id')]]+=$color_cons;
				}
				else
				{
					$color_tatal[$result_color[csf('fabric_color_id')]]=$color_cons;
				}

			?>
			</td>
			<?
			}
			$result_itemdescription[csf('rate')]=$result_itemdescription[csf('rate')]*$result[csf('exchange_rate')];//ISD-23-11716
			?>
			<td style="border:1px solid black; text-align:right"><? echo number_format($item_desctiption_total,2); ?></td>
			<td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('mc_dia')]; ?></td>
			<td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('dia_width')]; ?></td>
			<td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('fin_gsm')]; ?></td>
			<td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('slength')]; ?></td>
			<td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
			<td style="border:1px solid black; text-align:right;<? echo $hide_show;?>"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
			<td style="border:1px solid black; text-align:right;<? echo $hide_show;?>">
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
			<td style="border:1px solid black;  text-align:right" colspan="6"><strong> Item Total</strong></td>
			<?
			foreach($nameArray_color  as $result_color)
			{

			?>
			<td style="border:1px solid black;  text-align:right">
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
			<td style="border:1px solid black;  text-align:right"><? echo number_format(array_sum($color_tatal),2);  ?></td>
			<td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
			<td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
			<td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
			<td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
			<td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
			<td style="border:1px solid black; text-align:right;<? echo $hide_show;?>"><? //echo number_format(round($color_total),0); ?></td>
			<td style="border:1px solid black; text-align:right;<? echo $hide_show;?>">
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
			<td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_color)+(15-$col_span); ?>"><strong><? if($show_comments==0) echo '';else echo 'Total'; ?></strong></td>
			<td  style="border:1px solid black;  text-align:right"><?
			if($show_comments==0) echo '';
			else echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
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


        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2 and wo_qnty !=0");
        $nameArray_size=sql_select( "select distinct  item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=2 and wo_qnty !=0");
		if(count($nameArray_size)>0)
		{
        ?>

        <table border="0" align="left" cellpadding="0" width="100%" cellspacing="0" >
            <tr>
                <td colspan="<? echo count($nameArray_size)+11; ?>" align="">
                <strong>As Per Garments Size </strong>
                </td>

              <!-- <td colspan="8">
                   <table class="rpt_table"  cellpadding="0" width="100%" cellspacing="0">
                   <tr>
                   <td width="200"> <strong> Yarn Count: </strong></td> <td></td>
                    <td width="200">  <strong>Lot :  </strong></td> <td></td>
                    <td width="200"> <strong>Brand:  </strong></td> <td></td>
                   </tr>
                   </table>
               </td> -->
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Service Type</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black" width="80" ><strong>Y.Count</strong> </td>
                <td style="border:1px solid black" width="60"><strong>Lot</strong> </td>
                <td style="border:1px solid black" width="80"><strong>Brand</strong> </td>
                 <td style="border:1px solid black"><strong>Labdip No</strong> </td>
                <td style="border:1px solid black"><strong>Item size</strong> </td>
                <?
                foreach($nameArray_size  as $result_size)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $result_size[csf('gmts_sizes')];?></strong></td>
               <? }    ?>
                <td style="border:1px solid black" align="center"><strong>Total</strong></td>
                <td style="border:1px solid black" align="center"><strong>M/C Dia</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin Dia</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin GSM</strong></td>
                <td style="border:1px solid black" align="center"><strong>S/Length</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black;<? echo $hide_show;?>" align="center"><strong>Rate[BDT]</strong></td>
                <td style="border:1px solid black;<? echo $hide_show;?>" align="center"><strong>Amount[BDT]</strong></td>
            </tr>
            <?
			if($db_type==2)
			{
			$group_con="listagg(cast(yarn_count as varchar2(4000)),',') within group (order by yarn_count) AS yarn_count,
	listagg(cast(lot_no as varchar2(4000)),',') within group (order by lot_no) AS lot_no,
	listagg(cast(brand as varchar2(4000)),',') within group (order by brand) AS brand";
			}
			else
			{
				$group_con="group_concat(yarn_count) AS yarn_count,
		group_concat(lot_no) AS lot_no,
		group_concat(brand) AS brand";
			}
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
			$i++;
            $nameArray_item_description=sql_select( "select distinct description,rate,uom,fin_dia as dia_width,labdip_no,mc_dia,fin_gsm,slength,$group_con from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2 and process=".$result_item[csf('process')]." and wo_qnty !=0 group by description,rate,uom,fin_dia,labdip_no,mc_dia,fin_gsm,slength ");
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
				$yarn_count=implode(',',array_unique(explode(",",$result_itemdescription[csf('yarn_count')])));
				$lot_no=implode(',',array_unique(explode(",",$result_itemdescription[csf('lot_no')])));
				$brand=implode(',',array_unique(explode(",",$result_itemdescription[csf('brand')])));
                ?>
                <td style="border:1px solid black"><? echo $fabric_description_array[$result_itemdescription[csf('description')]]; ?> </td>
                <td style="border:1px solid black" width="80"><? echo $yarn_count; ?> </td>
                <td style="border:1px solid black" width="60"><? echo $lot_no; ?> </td>
                <td style="border:1px solid black" width="80"><? echo $brand; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('labdip_no')]; ?> </td>
                <td style="border:1px solid black"><? //echo $result_itemdescription['brand_supplier']; ?> Booking Qnty  </td>
                <?
				if($db_type==0)
					{
					if($result_itemdescription[csf('dia_width')]=='') $dia_width_con="";else $dia_width_con="and fin_dia='".$result_itemdescription[csf('dia_width')]."'";
					if($result_itemdescription[csf('description')]=='') $description_con="";else $description_con="and description='".$result_itemdescription[csf('description')]."'";
					if($result_itemdescription[csf('uom')]=='') $uom_con="";else $uom_con="and uom='".$result_itemdescription[csf('uom')]."'";
					if($result_itemdescription[csf('fabric_color_id')]!=0) $fcolor_con="and fabric_color_id='".$result_itemdescription[csf('fabric_color_id')]."'";else $fcolor_con="";
					//if($result_itemdescription[csf('fabric_color_id')]!=0) $fcolor_con="and fabric_color_id='".$result_itemdescription[csf('fabric_color_id')]."'";else $fcolor_con="";
					if($result_itemdescription[csf('item_size')]=='') $item_size_con="";else $item_size_con="and item_size='".$result_itemdescription[csf('gmts_sizes')]."'";
					}
					else
					{
					if($result_itemdescription[csf('dia_width')]=='') $dia_width_con="and slength is null";else $dia_width_con="and fin_dia='".$result_itemdescription[csf('dia_width')]."'";
					if($result_itemdescription[csf('description')]=='') $description_con="and description is null";else $description_con="and description='".$result_itemdescription[csf('description')]."'";
					if($result_itemdescription[csf('uom')]=='') $uom_con="and uom is null";else $uom_con="and uom='".$result_itemdescription[csf('uom')]."'";
					//if($result_itemdescription[csf('fabric_color_id')]=='') $fcolor_con="and fabric_color_id is null";else $fcolor_con="and fabric_color_id='".$result_itemdescription[csf('fabric_color_id')]."'";
					if($result_size[csf('gmts_sizes')]=='' || $result_size[csf('gmts_sizes')]==0) $item_size_con="and item_size is null";else $item_size_con="and item_size='".$result_size[csf('gmts_sizes')]."'";
					}
					foreach($nameArray_size  as $result_size)
					{
					/*$nameArray_size_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where   booking_no=$txt_booking_no and sensitivity=2 and process=". $result_item[csf('process')]." and description='". $result_itemdescription[csf('description')]."' and rate='". $result_itemdescription[csf('rate')]."' and uom='". $result_itemdescription[csf('uom')]."' and dia_width='". $result_itemdescription[csf('dia_width')]."' and item_size='".$result_size[csf('gmts_sizes')]."'"); */
					$nameArray_size_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where   booking_no=$txt_booking_no and sensitivity=2 and process=". $result_item[csf('process')]."  and rate='". $result_itemdescription[csf('rate')]."'  $description_con $uom_con  $dia_width_con $item_size_con");

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
				$result_itemdescription[csf('rate')]=$result_itemdescription[csf('rate')]*$result[csf('exchange_rate')];//ISD-23-11716
                ?>

                <td style="border:1px solid black; text-align:right"><? echo number_format($item_desctiption_total,2); ?></td>
                 <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('mc_dia')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('dia_width')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('fin_gsm')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('slength')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
                <td style="border:1px solid black; text-align:right;<? echo $hide_show;?>"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right;<? echo $hide_show;?>">
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
                <td style="border:1px solid black;  text-align:right" colspan="6"><strong> Item Total</strong></td>
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
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
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
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_size)+(15-$col_span); ?>"><strong><? if($show_comments==0) echo '';else echo 'Total'; ?></strong></td>
                <td  style="border:1px solid black;  text-align:right"><?
				if($show_comments==0) echo ' ';
				else echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
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
	//$gmtcolor_library=return_library_array( "select b.item_color,b.color_number_id from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.sensitivity=3 and a.status_active=1 and a.is_deleted=0", "item_color", "color_number_id"  );
	  // echo "select distinct process,fabric_color_id from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and wo_qnty !=0 order by fabric_color_id";
	// echo $wo_pre_cost_fab_co_color_sql="select b.gmts_color_id,b.contrast_color_id,b.pre_cost_fabric_cost_dtls_id as fab_dtls_id from wo_pre_cos_fab_co_color_dtls b,wo_booking_dtls a  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and b.job_no='$job_num'";
	 // echo $wo_pre_cost_fab_co_color_sql="select b.gmts_color_id,b.contrast_color_id,b.pre_cost_fabric_cost_dtls_id as fab_dtls_id from wo_pre_cos_fab_co_color_dtls b  where   b.job_no='$job_num'";
	/*$wo_pre_fab_co_color_result=sql_select($wo_pre_cost_fab_co_color_sql);
	foreach( $wo_pre_fab_co_color_result as $row)
	{
		$contrast_color_arr[$row[csf('fab_dtls_id')]][$row[csf('gmts_color_id')]]['contrast_color']=$row[csf('contrast_color_id')];
	}
	*/
       $only_color_size_qnty=sql_select( "select fabric_color_id, gmts_color_id, slength,fin_dia as  dia_width, fin_gsm, rate, description, sum(wo_qnty) as cons from wo_booking_dtls where booking_no=$txt_booking_no and sensitivity=3 and status_active=1 and is_deleted =0 and wo_qnty>0 group by fabric_color_id, gmts_color_id, slength, fin_dia, fin_gsm, rate, description");
		 
					
		foreach($only_color_size_qnty as $OnlyColorSizeQnty)
		{
		  if($OnlyColorSizeQnty[csf('cons')]){
		 	$arrOnlyColQty[$OnlyColorSizeQnty[csf('fabric_color_id')]][$OnlyColorSizeQnty[csf('gmts_color_id')]][$OnlyColorSizeQnty[csf('description')]][$OnlyColorSizeQnty[csf('slength')]][$OnlyColorSizeQnty[csf('dia_width')]][$OnlyColorSizeQnty[csf('fin_gsm')]][$OnlyColorSizeQnty[csf('rate')]]['cons']=$OnlyColorSizeQnty[csf('cons')];
		  }
		}
		
	    $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and wo_qnty !=0 ");
        $nameArray_color=sql_select( "select distinct fabric_color_id as color_number_id,gmts_color_id from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=3 and wo_qnty !=0 order by fabric_color_id");
		$nameArray_color_labdib=sql_select( "select distinct labdip_no as labdip_no from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=3 and wo_qnty !=0 ");

		if(count($nameArray_color)>0)
		{

        ?>
        <table border="0" align="left" cellpadding="0" width="100%" cellspacing="0" >
            <tr>
                <td colspan="<? echo count($nameArray_color)+10; ?>" align="">
                <strong>Contrast Color</strong>
                </td>

              <!-- <td colspan="7">
                   <table class="rpt_table"  cellpadding="0" width="100%" cellspacing="0">
                   <tr>
                   <td width="200"> <strong> Yarn Count: </strong></td> <td></td>
                    <td width="200">  <strong>Lot :  </strong></td> <td></td>
                    <td width="200"> <strong>Brand:  </strong></td> <td></td>
                   </tr>
                   </table>
               </td> -->
            </tr>
            <tr>
                <td style="border:1px solid black"  rowspan="3"><strong>Sl</strong> </td>
                <td style="border:1px solid black"  rowspan="3"><strong>Service Type</strong> </td>
                <td style="border:1px solid black"  rowspan="3"><strong>Item Description</strong> </td>
                <td style="border:1px solid black" width="80"  rowspan="3"><strong>Y.Count</strong> </td>
                <td style="border:1px solid black" width="60"  rowspan="3"><strong>Lot</strong> </td>
                <td style="border:1px solid black" width="80" rowspan="3"><strong>Brand</strong> </td>
               <!-- <td style="border:1px solid black"  rowspan="2"><strong>Labdip No</strong> </td>-->
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <?
                foreach($nameArray_color  as $result_color)
                {
					//$color_id=$contrast_color_arr[$result_color[csf('pre_cost_fabric_cost_dtls_id')]][$result_color[csf('color_number_id')]]['contrast_color'];	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $color_library[$result_color[csf('color_number_id')]];?></strong></td>
                <?	}    ?>
                <td style="border:1px solid black" align="center"  rowspan="3"><strong>Total</strong></td>
                <td style="border:1px solid black" align="center"  rowspan="3"><strong>M/C Dia</strong></td>
                <td style="border:1px solid black" align="center"  rowspan="3"><strong>Fin Dia</strong></td>
                <td style="border:1px solid black" align="center"  rowspan="3"><strong>Fin GSM</strong></td>
                <td style="border:1px solid black" align="center"  rowspan="3"><strong>S/Length</strong></td>
                <td style="border:1px solid black" align="center"  rowspan="3"><strong>UOM</strong></td>
                <td style="border:1px solid black;<? echo $hide_show;?>" align="center"  rowspan="3"><strong>Rate[BDT]</strong></td>
                <td style="border:1px solid black;<? echo $hide_show;?>" align="center"  rowspan="3"><strong>Amount[BDT]</strong></td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Gmts Color</strong> </td>
                <?
                foreach($nameArray_color  as $result_color)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $color_library[$result_color[csf('gmts_color_id')]];?></strong></td>
                <?	}    ?>

            </tr>
             <tr>
                <td style="border:1px solid black"><strong>Labdip No</strong> </td>
                <?
                foreach($nameArray_color_labdib  as $row)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $row[csf('labdip_no')];?></strong></td>
                <?	}    ?>

            </tr>
            <?
			if($db_type==2)
			{
			$group_con="listagg(cast(yarn_count as varchar2(4000)),',') within group (order by yarn_count) AS yarn_count,
		listagg(cast(lot_no as varchar2(4000)),',') within group (order by lot_no) AS lot_no,
		listagg(cast(brand as varchar2(4000)),',') within group (order by brand) AS brand";
			}
			else
			{
				$group_con="group_concat(yarn_count) AS yarn_count,
		group_concat(lot_no) AS lot_no,
		group_concat(brand) AS brand";
			}
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
				//echo "select distinct description,rate,labdip_no,uom,mc_dia,fin_gsm,slength,fabric_color_id,dia_width from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and process=".$result_item[csf('process')]." and wo_qnty !=0 order by fabric_color_id";

          /* echo "select distinct description,rate,uom,mc_dia,fin_gsm,slength,fabric_color_id,dia_width,$group_con from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and process=".$result_item[csf('process')]." and wo_qnty !=0 group by description,rate,uom,mc_dia,fin_gsm,slength,fabric_color_id,dia_width order by fabric_color_id";*/
		    $nameArray_item_description=sql_select( "select distinct description,rate,uom,mc_dia,fin_gsm,slength,fin_dia as dia_width,$group_con from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and process=".$result_item[csf('process')]." and wo_qnty !=0 group by description,rate,uom,mc_dia,fin_gsm,slength,fin_dia order by description");
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
				$yarn_count=implode(',',array_unique(explode(",",$result_itemdescription[csf('yarn_count')])));
				$lot_no=implode(',',array_unique(explode(",",$result_itemdescription[csf('lot_no')])));
				$brand=implode(',',array_unique(explode(",",$result_itemdescription[csf('brand')])));
				//echo 'BB';
                ?>
                <td style="border:1px solid black"><? echo $fabric_description_array[$result_itemdescription[csf('description')]]; ?> </td>
                 <td style="border:1px solid black" width="80"><? echo $yarn_count; ?> </td>
                 <td style="border:1px solid black" width="60"><? echo $lot_no; ?> </td>
                 <td style="border:1px solid black" width="80"><? echo $brand; ?> </td>
                <td style="border:1px solid black"><? //echo $result_itemdescription['brand_supplier']; ?> Booking Qnty  </td>
                <?
				if($db_type==0) $dia_con="dia_width";
				else $dia_con="nvl(dia_width,0)";
				if($db_type==0)
					{
					if($result_itemdescription[csf('dia_width')]=='') $dia_width_con="";else $dia_width_con="and fin_dia='".$result_itemdescription[csf('dia_width')]."'";
					if($result_itemdescription[csf('description')]=='') $description_con="";else $description_con="and description='".$result_itemdescription[csf('description')]."'";
					if($result_itemdescription[csf('uom')]=='') $uom_con="";else $uom_con="and uom='".$result_itemdescription[csf('uom')]."'";

					if($result_color[csf('fabric_color_id')]!=0) $fcolor_con="and fabric_color_id='".$result_color[csf('color_number_id')]."'";else $fcolor_con="";
					if($result_itemdescription[csf('item_size')]=='') $item_size_con="";else $item_size_con="and item_size='".$result_itemdescription[csf('item_size')]."'";
					}
					else
					{
					if($result_itemdescription[csf('dia_width')]=='') $dia_width_con="and slength is null";else $dia_width_con="and fin_dia='".$result_itemdescription[csf('dia_width')]."'";
					if($result_itemdescription[csf('description')]=='') $description_con="and description is null";else $description_con="and description='".$result_itemdescription[csf('description')]."'";
					if($result_itemdescription[csf('uom')]=='') $uom_con="and uom is null";else $uom_con="and uom='".$result_itemdescription[csf('uom')]."'";
					//if($result_color[csf('color_number_id')]=='') $fcolor_con="and fabric_color_id is null";else $fcolor_con="and fabric_color_id='".$result_color[csf('color_number_id')]."'";
					if($result_itemdescription[csf('item_size')]=='') $item_size_con="and item_size is null";else $item_size_con="and fin_dia='".$result_itemdescription[csf('item_size')]."'";
					}
                foreach($nameArray_color  as $result_color)
                {
					if($result_itemdescription[csf('dia_width')]=='') $dia_width=0;else $dia_width=$result_itemdescription[csf('dia_width')];
                /*$nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls    where   booking_no=$txt_booking_no and sensitivity=3 and process=". $result_item[csf('process')]." and description='". $result_itemdescription[csf('description')]."' and rate='". $result_itemdescription[csf('rate')]."' and uom='". $result_itemdescription[csf('uom')]."' and $dia_con='".$dia_width."' and fabric_color_id=".$result_color[csf('color_number_id')]."  ");    */
			//	$nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls    where   booking_no=$txt_booking_no and sensitivity=3 and process=". $result_item[csf('process')]." and rate='". $result_itemdescription[csf('rate')]."'  and gmts_color_id=".$result_color[csf('gmts_color_id')]." and wo_qnty>0  $dia_width_con $description_con $uom_con $fcolor_con order by fabric_color_id ");
				$color_size_qnty=$arrOnlyColQty[$result_color[csf('color_number_id')]][$result_color[csf('gmts_color_id')]][$result_itemdescription[csf('description')]][$result_itemdescription[csf('slength')]][$result_itemdescription[csf('dia_width')]][$result_itemdescription[csf('fin_gsm')]][$result_itemdescription[csf('rate')]]['cons'];
				//echo  "select sum(wo_qnty) as cons from wo_booking_dtls    where   booking_no=$txt_booking_no and sensitivity=3 and process=". $result_item[csf('process')]." and rate='". $result_itemdescription[csf('rate')]."'  and gmts_color_id=".$result_color[csf('gmts_color_id')]." and wo_qnty>0  $dia_width_con $description_con $uom_con $fcolor_con  order by fabric_color_id";

             //   foreach($nameArray_color_size_qnty as $result_color_size_qnty)
               // {
                ?>
                <td style="border:1px solid black; text-align:right">

                <?
                if($color_size_qnty!= "")
                {
                echo number_format($color_size_qnty,2);
                $item_desctiption_total += $color_size_qnty ;
                if (array_key_exists($result_color[csf('gmts_color_id')], $color_tatal))
                {
                $color_tatal[$result_color[csf('gmts_color_id')]]+=$color_size_qnty;
                }
                else
                {
                $color_tatal[$result_color[csf('gmts_color_id')]]=$color_size_qnty;
                }
                }
                else echo "";
                ?>
                </td>
                <?
                }
              //  }
			  $result_itemdescription[csf('rate')]=$result_itemdescription[csf('rate')]*$result[csf('exchange_rate')];//ISD-23-11716
                ?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($item_desctiption_total,2); ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('mc_dia')]; ?></td>
                <td style="border:1px solid black; text-align:center"><? echo $result_itemdescription[csf('dia_width')]; ?></td>
               <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('fin_gsm')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('slength')]; ?></td>
                <td style="border:1px solid black; text-align:center"><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
                <td style="border:1px solid black; text-align:right;<? echo $hide_show;?>"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right;<? echo $hide_show;?>">
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
                <td style="border:1px solid black;  text-align:right" colspan="5"><strong> Item Total</strong></td>
                <?
                foreach($nameArray_color  as $result_color)
                {

                ?>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($color_tatal[$result_color[csf('gmts_color_id')]] !='')
                {
                echo number_format($color_tatal[$result_color[csf('gmts_color_id')]],2);
                }
                ?>
                </td>
            <?
            }
            ?>
                <td style="border:1px solid black;  text-align:right"><? echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;text-align:center"></td>
                <td style="border:1px solid black;text-align:center"></td>
                <td style="border:1px solid black;text-align:center"></td>
                <td style="border:1px solid black;text-align:center"></td>
                 <td style="border:1px solid black;text-align:center"></td>


                <td style="border:1px solid black; text-align:right;<? echo $hide_show;?>"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right;<? echo $hide_show;?>">
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
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_color)+(14-$col_span); ?>"><strong><? if($show_comments==0) echo '';else echo 'Total'; ?></strong></td>
                <td  style="border:1px solid black;  text-align:right"><?
				if($show_comments==0) echo '';
				else echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
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

        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=4 and wo_qnty !=0 and is_deleted=0 and status_active=1");
        $nameArray_size=sql_select( "select distinct item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4 and wo_qnty !=0 and is_deleted=0 and status_active=1");
	    $nameArray_color=sql_select( "select distinct fabric_color_id as color_number_id from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4 and wo_qnty !=0 and is_deleted=0 and status_active=1");

		if(count($nameArray_size)>0)
		{
        ?>

        <table border="0" align="left" cellpadding="0" width="100%" cellspacing="0" >
            <tr>
                <td colspan="<? echo count($nameArray_size)+12; ?>" align="">
                <strong>Color & size sensitive </strong>
                </td>

              <!-- <td colspan="8">
                   <table class="rpt_table"  cellpadding="0" width="100%" cellspacing="0">
                   <tr>
                   <td width="200"> <strong> Yarn Count: </strong></td> <td></td>
                    <td width="200">  <strong>Lot :  </strong></td> <td></td>
                    <td width="200"> <strong>Brand:  </strong></td> <td></td>
                   </tr>
                   </table>
               </td> -->
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Service Type</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black" width="80"><strong>Y.Count</strong> </td>
                <td style="border:1px solid black" width="60"><strong>Lot</strong> </td>
                <td style="border:1px solid black" width="80"><strong>Brand</strong> </td>
                <td style="border:1px solid black"><strong>Labdip No</strong> </td>

                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <?
                foreach($nameArray_size  as $result_size)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $result_size[csf('gmts_sizes')];?></strong></td>
                <?	}    ?>
                <td style="border:1px solid black" align="center"><strong>Total</strong></td>
                <td style="border:1px solid black" align="center"><strong>M/C Dia</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin Dia</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin GSM</strong></td>
                <td style="border:1px solid black" align="center"><strong>S/Length</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black;<? echo $hide_show;?>" align="center"><strong>Rate[BDT]</strong></td>
                <td style="border:1px solid black;<? echo $hide_show;?>" align="center"><strong>Amount[BDT]</strong></td>
            </tr>

            <?
			if($db_type==2)
			{
			$group_con="listagg(cast(yarn_count as varchar2(4000)),',') within group (order by yarn_count) AS yarn_count,
		listagg(cast(lot_no as varchar2(4000)),',') within group (order by lot_no) AS lot_no,
		listagg(cast(brand as varchar2(4000)),',') within group (order by brand) AS brand";
			}
			else
			{
				$group_con="group_concat(yarn_count) AS yarn_count,
		group_concat(lot_no) AS lot_no,
		group_concat(brand) AS brand";
			}

			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {

			$i++;
            $nameArray_item_description=sql_select( "select distinct description,mc_dia,fin_gsm,slength,rate,uom,fin_dia as dia_width,$group_con from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=4 and process=".$result_item[csf('process')]." and wo_qnty !=0 group by description,labdip_no,mc_dia,fin_gsm,slength,rate,uom,fin_dia order by description");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo   count($nameArray_item_description)+1; ?>">
                <? echo $i ; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)*1+1; ?>">
                <? echo $conversion_cost_head_array[$result_item[csf('process')]]; ?>
                </td>
                <?
                $color_tatal=array();
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
					$yarn_count=implode(',',array_unique(explode(",",$result_itemdescription[csf('yarn_count')])));
					$lot_no=implode(',',array_unique(explode(",",$result_itemdescription[csf('lot_no')])));
					$brand=implode(',',array_unique(explode(",",$result_itemdescription[csf('brand')])));
					?>
                    <td style="border:1px solid black" rowspan="<? echo count($nameArray_color); ?>"><? echo $fabric_description_array[$result_itemdescription[csf('description')]]; ?> </td>
                    <td style="border:1px solid black" width="80"><? echo $yarn_count; ?> </td>
                    <td style="border:1px solid black" width="60"><? echo $lot_no; ?> </td>
                    <td style="border:1px solid black" width="80"><? echo $brand; ?> </td>
                    <td style="border:1px solid black"><? echo $result_itemdescription[csf('labdip_no')]; ?> </td>
                    <td style="border:1px solid black" rowspan="<? //echo count($nameArray_color); ?>"><? //echo $result_itemdescription['brand_supplier']; ?>Booking Qnty </td>

                    <?
					if($db_type==0)
					{
					if($result_itemdescription[csf('dia_width')]=='') $dia_width_con="";else $dia_width_con="and fin_dia='".$result_itemdescription[csf('dia_width')]."'";
					if($result_itemdescription[csf('description')]=='') $description_con="";else $description_con="and description='".$result_itemdescription[csf('description')]."'";
					if($result_itemdescription[csf('uom')]=='') $uom_con="";else $uom_con="and uom='".$result_itemdescription[csf('uom')]."'";

					if($result_color[csf('fabric_color_id')]!=0) $fcolor_con="and fabric_color_id='".$result_color[csf('fabric_color_id')]."'";else $fcolor_con="";
					if($result_itemdescription[csf('item_size')]=='') $item_size_con="";else $item_size_con="and item_size='".$result_itemdescription[csf('gmts_sizes')]."'";
					}
					else
					{
					if($result_itemdescription[csf('dia_width')]=='') $dia_width_con="and slength is null";else $dia_width_con="and fin_dia='".$result_itemdescription[csf('dia_width')]."'";
					if($result_itemdescription[csf('description')]=='') $description_con="and description is null";else $description_con="and description='".$result_itemdescription[csf('description')]."'";
					if($result_itemdescription[csf('uom')]=='') $uom_con="and uom is null";else $uom_con="and uom='".$result_itemdescription[csf('uom')]."'";
					if($result_color[csf('color_number_id')]=='') $fcolor_con="and color_number_id is null";else $fcolor_con="and color_number_id='".$result_color[csf('color_number_id')]."'";
					if($result_size[csf('gmts_sizes')]=='') $item_size_con="and item_size is null";else $item_size_con="and item_size='".$result_size[csf('gmts_sizes')]."'";
					}
                //$item_desctiption_total=0;
				foreach($nameArray_color as $result_color)
                {
					 $item_desctiption_total=0;
                ?>

                <!--<td style="border:1px solid black">DFF<? //echo $color_library[$result_color[csf('color_number_id')]]; ?> </td>-->
                <?
                foreach($nameArray_size  as $result_size)
                {
					//echo 'ssddd';
               /* $nameArray_size_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4 and process=". $result_item[csf('process')]." and  description='". $result_itemdescription[csf('description')]."' and  item_size='".$result_size[csf('gmts_sizes')]."' and rate='". $result_itemdescription[csf('rate')]."' and uom='". $result_itemdescription[csf('uom')]."' and dia_width='". $result_itemdescription[csf('dia_width')]."' and fabric_color_id=".$result_color[csf('color_number_id')].""); */
				 $nameArray_size_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4 and process=". $result_item[csf('process')]." and rate='". $result_itemdescription[csf('rate')]."' $dia_width_con $description_con $uom_con $fcolor_con $item_size_con");
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
				$result_itemdescription[csf('rate')]=$result_itemdescription[csf('rate')]*$result[csf('exchange_rate')];//ISD-23-11716
                ?>

                <td style="border:1px solid black; text-align:right"><? echo number_format($item_desctiption_total,2); ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('mc_dia')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('dia_width')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('fin_gsm')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('slength')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
                <td style="border:1px solid black; text-align:right;<? echo $hide_show;?>"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right;<? echo $hide_show;?>">
                <?
                $amount_as_per_gmts_color = $item_desctiption_total*$result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
			}
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="8"><strong> Item Total</strong></td>

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
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black; text-align:right;<? echo $hide_show;?>"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right;<? echo $hide_show;?>" >
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
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_size)+(15-$col_span); ?>"><strong><? if($show_comments==0) echo '';else echo 'Total'; ?></strong></td>
                <td  style="border:1px solid black;  text-align:right"><?
				if($show_comments==0) echo ' ';else echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER Color & SIZE  END=========================================  -->


         <!--==============================================NO NENSITIBITY START=========================================  -->
		<?
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 and wo_qnty !=0 and is_deleted=0 and status_active=1");
        //$nameArray_color=sql_select( "select distinct b.color_number_id from wo_trims_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.sensitivity=1");
		$nameArray_color= array();
		if(count($nameArray_item)>0)
		{
        ?>
        <table border="0" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" >
            <tr>
                <td align="" colspan="11" >
                <strong>No Sensitivity</strong>
                </td>

               <!--<td colspan="6">
                   <table class="rpt_table"  cellpadding="0" width="100%" cellspacing="0">
                   <tr>
                   <td width="200"> <strong> Yarn Count: </strong></td> <td></td>
                    <td width="200">  <strong>Lot :  </strong></td> <td></td>
                    <td width="200"> <strong>Brand:  </strong></td> <td></td>
                   </tr>
                   </table>
               </td> -->
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Service Type</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black" width="80"><strong>Y.Count</strong> </td>
                <td style="border:1px solid black" width="60"><strong>Lot</strong> </td>
                <td style="border:1px solid black" width="80"><strong>Brand</strong> </td>
                <td style="border:1px solid black"><strong>Labdip No</strong> </td>
                <td style="border:1px solid black"><strong></strong> </td>
                <td style="border:1px solid black" align="center"><strong> Qnty</strong></td>
                <td style="border:1px solid black" align="center"><strong>M/C Dia</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin Dia</strong></td>
                <td style="border:1px solid black" align="center"><strong>Fin GSM</strong></td>
                <td style="border:1px solid black" align="center"><strong>S/Length</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black;<? echo $hide_show;?>" align="center"><strong>Rate[BDT]</strong></td>
                <td style="border:1px solid black;<? echo $hide_show;?>" align="center"><strong>Amount[BDT]</strong></td>
            </tr>
            <?
			if($db_type==2)
			{
			$group_con="listagg(cast(yarn_count as varchar2(4000)),',') within group (order by yarn_count) AS yarn_count,
		listagg(cast(lot_no as varchar2(4000)),',') within group (order by lot_no) AS lot_no,
		listagg(cast(brand as varchar2(4000)),',') within group (order by brand) AS brand";
			}
			else
			{
				$group_con="group_concat(yarn_count) AS yarn_count,
		group_concat(lot_no) AS lot_no,
		group_concat(brand) AS brand";
			}
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
            $nameArray_item_description=sql_select( "select distinct description,labdip_no,rate,mc_dia,fin_gsm,slength,uom,fin_dia as dia_width,$group_con from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 and process=".$result_item[csf('process')]." and wo_qnty !=0 group by description,labdip_no,rate,mc_dia,fin_gsm,slength,uom,fin_dia");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $conversion_cost_head_array[$result_item[csf('process')]]; ?>
                </td>
                <?
                $color_tatal=0;
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $item_desctiption_total=0;
				$yarn_count=implode(',',array_unique(explode(",",$result_itemdescription[csf('yarn_count')])));
				$lot_no=implode(',',array_unique(explode(",",$result_itemdescription[csf('lot_no')])));
				$brand=implode(',',array_unique(explode(",",$result_itemdescription[csf('brand')])));
                ?>
                <td style="border:1px solid black"><? echo $fabric_description_array[$result_itemdescription[csf('description')]]; ?> </td>
                <td style="border:1px solid black" width="80"><? echo $yarn_count; ?> </td>
                <td style="border:1px solid black" width="60"><?  echo $lot_no; ?> </td>
                <td style="border:1px solid black" width="80"><? echo $brand; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('labdip_no')]; ?> </td>
                <td style="border:1px solid black"><? //echo $result_itemdescription['brand_supplier']; ?>Booking Qnty  </td>
                <?
				if($db_type==0)
					{
					if($result_itemdescription[csf('dia_width')]=='') $dia_width_con="";else $dia_width_con="and fin_dia='".$result_itemdescription[csf('dia_width')]."'";
					if($result_itemdescription[csf('description')]=='') $description_con="";else $description_con="and description='".$result_itemdescription[csf('description')]."'";
					if($result_itemdescription[csf('uom')]=='') $uom_con="";else $uom_con="and uom='".$result_itemdescription[csf('uom')]."'";


					}
					else
					{
					if($result_itemdescription[csf('dia_width')]=='') $dia_width_con="and slength is null";else $dia_width_con="and fin_dia='".$result_itemdescription[csf('dia_width')]."'";
					if($result_itemdescription[csf('description')]=='') $description_con="and description is null";else $description_con="and description='".$result_itemdescription[csf('description')]."'";
					if($result_itemdescription[csf('uom')]=='') $uom_con="and uom is null";else $uom_con="and uom='".$result_itemdescription[csf('uom')]."'";

					}

            /*    $nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls where    booking_no=$txt_booking_no and sensitivity=0 and process=". $result_item[csf('process')]." and  description='". $result_itemdescription[csf('description')]."'and rate='". $result_itemdescription[csf('rate')]."' and uom='". $result_itemdescription[csf('uom')]."' and dia_width='". $result_itemdescription[csf('dia_width')]."'");  */
				 $nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls where    booking_no=$txt_booking_no and sensitivity=0 and process=". $result_item[csf('process')]."  and rate='". $result_itemdescription[csf('rate')]."'  $dia_width_con $description_con $uom_con");
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
				$result_itemdescription[csf('rate')]=$result_itemdescription[csf('rate')]*$result[csf('exchange_rate')];//ISD-23-11716
                ?>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('mc_dia')]; ?></td>
                <td style="border:1px solid black; text-align:center "><? echo $result_itemdescription[csf('dia_width')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('fin_gsm')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $result_itemdescription[csf('slength')]; ?></td>
                <td style="border:1px solid black; text-align:center "><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
                <td style="border:1px solid black; text-align:right;<? echo $hide_show;?>"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right;<? echo $hide_show;?>">
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
                <td style="border:1px solid black;  text-align:right" colspan="6"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($color_tatal !='')
                {
                echo number_format($color_tatal,2);
                }
                ?>
                </td>
                  <td style="border:1px solid black;"></td>
                <td style="border:1px solid black;"></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black;"></td>
                <td style="border:1px solid black;"></td>
                <td style="border:1px solid black; text-align:right<? echo $hide_show;?>"></td>
                <td style="border:1px solid black; text-align:right;<? echo $hide_show;?>">
                <?
			 if($show_comments==0) echo '';else echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="15-<? echo $col_span?>"><strong><? if($show_comments==0) echo '';else echo 'Total'; ?></strong></td>
                <td  style="border:1px solid black;  text-align:right"><?
				if($show_comments==0) echo '';else echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <?
		//print_r($color_tatal);
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
       <table  width="100%" class="rpt_table" style="border:1px solid black;"   border="0" cellpadding="0" cellspacing="0">
       <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right"><? if($show_comments==0) echo '';else echo 'Total Booking Amount'; ?></th><td width="30%" style="border:1px solid black; text-align:right"><?  if($show_comments==0) echo '';else echo number_format($booking_grand_total,4);?></td>
            </tr>
            <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right"><? if($show_comments==0) echo '';else echo 'Total Booking Amount (in word)'; ?></th><td width="30%" style="border:1px solid black;"><?
				 $booking_grand_total=number_format($booking_grand_total,2,'.','');
				 if($show_comments==0) echo '';else echo number_to_words($booking_grand_total,$mcurrency, $dcurrency);?></td>
            </tr>
       </table>
          &nbsp;
        <table  width="100%" class="rpt_table" style="border:1px solid black;"   border="0" cellpadding="0" cellspacing="0">

        <?
        $data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no");// quotation_id='$data'
        if ( count($data_array)>0)
        {
		?>

         <thead>
            <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th><th width="97%" style="border:1px solid black;font-weight:bold;"><b>Spacial Instruction</b></th>
            </tr>
        </thead>
        <tbody>

        <?
            $i=0;
            foreach( $data_array as $row )
            {
                $i++;
                ?>
                    <tr id="settr_1" align="" style="border:1px solid black;">
                        <td style="border:1px solid black;">
                        <? echo $i;?>
                        </td>
                        <td style="border:1px solid black;"><strong style=" font-weight:bold; ">
                        <? echo $row[csf('terms')]; ?>
                        </strong>
                        </td>
                    </tr>
                <?
            }
        }
        else
        { 

		?>
         <thead>
            <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th><th width="97%" style="border:1px solid black;font-weight:bold;"><b>Spacial Instruction</b></th>
            </tr>
        </thead>
        <tbody>
        <?
			$i=0;
        $data_array=sql_select("select id, terms from  lib_terms_condition where is_default=1 and page_id=176");// quotation_id='$data'
        foreach( $data_array as $row )
            {
                $i++;
        ?>
        <tr id="settr_1" align="" style="border:1px solid black;">
                        <td style="border:1px solid black;">
                        <? echo $i;?>
                        </td>
                        <td style="border:1px solid black;">
                        <strong style="  font-weight:bold;">
                        <? echo $row[csf('terms')]; ?>
                        </strong>
                        </td>

                    </tr>
        <?
            }
        }
		//$po_no
		//$po_no=rtrim($po_no,',');//wo_booking_mst
		$po_ids=implode(",",array_unique(explode(",",$po_ids)));
		$booking_nos='';$booking_dates='';
	  $booking_arr="select a.booking_date,a.booking_no  from wo_booking_mst a ,wo_booking_dtls b, wo_po_break_down c  where  a.booking_no=b.booking_no and b.po_break_down_id=c.id  and a.booking_no!=$txt_booking_no and c.id in($po_ids) and a.company_id=$cbo_company_name and a.item_category=12 and b.job_no=c.job_no_mst  and a.booking_type=3  and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 group by a.booking_date,a.booking_no";
	  $booking_previous=sql_select($booking_arr);
	foreach ($booking_previous as $result_book)
	{
		if($booking_nos!='') $booking_nos.=",".$result_book[csf('booking_no')].'; '.change_date_format($result_book[csf('booking_date')]);else  $booking_nos=$result_book[csf('booking_no')].'; '.change_date_format($result_book[csf('booking_date')]);
	}
        ?>

    </tbody>
    </table>
    <? if($cid==2){?>
    <br/>
     <table  width="100%" class="rpt_table" style="border:1px solid black;"   border="0" cellpadding="0" cellspacing="0">
        <thead>
         <tr align="left" style="border:1px solid black;font-weight:bold;">
          <th> Booking No & Date</th>
         </tr>
         </thead>
     <tr style="border:1px solid black;">
         <td><? echo $booking_nos;?> </td>

        </tr>
     </table>

     <?
	}
	 ?>
         <br/>

		 <?
            echo signature_table(150, $cbo_company_name, "1313px",$cbo_template_id);
			if($cid==2){
			echo "****".custom_file_name($txt_booking_no,$style_sting,$job_no);
			}
         ?>
    </div>
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
		fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id_<? echo $cid; ?>');
	</script>
	<p style="page-break-after:always;"></p>
    <?
	}
	
	$html = ob_get_contents();
	ob_clean();
	list($is_mail_send,$mail)=explode('___',$mail_send_data);
	if($is_mail_send==1){
		require_once('../../../mailer/class.phpmailer.php');
		require_once('../../../auto_mail/setting/mail_setting.php');
		$mailBody = preg_replace("/<img[^>]+\>/i", " ", $html); 
			
		$mailToArr=array();
		$mailSql = "select b.EMAIL  from wo_booking_mst a,LIB_SUPPLIER b where b.id=a.supplier_id and a.booking_no=$txt_booking_no";
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
		
		$to=implode(',',$mailToArr);
 		$subject=" Service Booking Sheet ";
		$header=mailHeader();
		echo sendMailMailer( $to, $subject, $mailBody, $from_mail,$att_file_arr );
		
	}
	else{
		echo $html;
	}
	exit();


}

if($action=="show_service_booking_report5") //For Shariar 3527
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$color_library=return_library_array( "select id,color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name");
	$lib_body_part=return_library_array( "select id,body_part_full_name from lib_body_part  where status_active=1 and is_deleted=0", "id", "body_part_full_name");
	$fabric_ima_lib=return_library_array( "select master_tble_id,image_location from common_photo_library  where is_deleted=0 and file_type=1 and form_name='aop_v2' ", "master_tble_id", "image_location");
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$trim_group= return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	
	$path=($path=='')?'../../../':$path;

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
                            <strong> Service Booking For Knitting:<? echo str_replace("'","",$txt_booking_no); ?></strong>
                             </td>
                            </tr>
                      </table>
                </td>
            </tr>
       </table>
		<?
		$nameArray_job=sql_select(" SELECT b.id as po_id,b.po_number,b.plan_cut,a.dia_width,a.fin_dia,a.fabric_color_id,a.fin_gsm,a.gmts_color_id,a.fabric_color_id,a.job_no,a.description,a.program_no,a.lib_composition,a.delivery_date, a.delivery_end_date,a.pre_cost_fabric_cost_dtls_id as fab_dtls_id,a.sensitivity,a.rate,a.uom,a.amount,	a.wo_qnty, d.body_part_id, e.style_ref_no , e.id as job_id, d.fabric_description, d.color_type_id, d.id as fabric_id, d.lib_yarn_count_deter_id ,d.gsm_weight ,a.artwork_no,a.printing_color_id,a.id from wo_booking_dtls a join wo_po_break_down b on a.po_break_down_id=b.id join wo_pre_cost_fab_conv_cost_dtls c on c.id=a.pre_cost_fabric_cost_dtls_id join 
		wo_pre_cost_fabric_cost_dtls d on c.fabric_description=d.id join wo_po_details_master e on e.id=b.job_id where 
		a.booking_no=$txt_booking_no and a.entry_form_id=228 and a.status_active=1 group by b.id ,b.po_number, b.plan_cut,a.dia_width,a.fin_dia, a.fabric_color_id, a.fin_gsm, a.gmts_color_id, a.fabric_color_id, a.job_no, a.description, a.program_no, a.lib_composition, a.delivery_date, a.delivery_end_date, a.pre_cost_fabric_cost_dtls_id , a.sensitivity, a.rate, a.uom, a.amount, a.wo_qnty, d.body_part_id, e.style_ref_no,e.id,d.fabric_description, d.color_type_id, d.id, d.lib_yarn_count_deter_id,d.gsm_weight,a.artwork_no,a.printing_color_id,a.id");
		$fabric_atribute_arr=array('body_part_id','fabric_description','dia_width','a.fin_dia','fin_gsm','color_type_id','gsm_weight');
		$fabric_color_attr=array('fabric_color_id','uom','rate','fabric_id','artwork_no','printing_color_id','id');
		$fabric_color_summary_attr=array('fabric_description');
		
		foreach($nameArray_job as $row){
			$job_no=$row[csf("job_no")];
			$sensitivity_prog_arr[$row[csf('job_id')]]['job_no']=$row[csf('job_no')];
			$sensitivity_prog_arr[$row[csf('job_id')]]['style_ref']=$row[csf('style_ref_no')];
			$sensitivity_prog_arr[$row[csf('job_id')]]['po_no'][$row[csf('po_id')]]=$row[csf('po_number')];
			foreach($fabric_atribute_arr as $fabattr){
				$sensitivity_prog_arr[$row[csf('job_id')]]['fabric_data'][$row[csf('fab_dtls_id')]][$row[csf('fin_dia')]][$fabattr]=$row[csf($fabattr)];
			}
			foreach($fabric_color_attr as $fcolorattr){
				$sensitivity_prog_arr[$row[csf('job_id')]]['fabric_data'][$row[csf('fab_dtls_id')]][$row[csf('fin_dia')]]['color_dtls'][$row[csf('fabric_color_id')]][$fcolorattr]=$row[csf($fcolorattr)];				
			}
			$sensitivity_prog_arr[$row[csf('job_id')]]['fabric_data'][$row[csf('fab_dtls_id')]][$row[csf('fin_dia')]]['color_dtls'][$row[csf('fabric_color_id')]]['wo_qnty']+=$row[csf('wo_qnty')];
			$sensitivity_prog_arr[$row[csf('job_id')]]['fabric_data'][$row[csf('fab_dtls_id')]][$row[csf('fin_dia')]]['color_dtls'][$row[csf('fabric_color_id')]]['amount']+=$row[csf('amount')];
		


			$job_po_arr[$row[csf('job_no')]][$row[csf('po_number')]]=$row[csf('po_number')];

			$main_data_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['wo_qnty']+=$row[csf('wo_qnty')];
			$main_data_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['artwork_no']=$row[csf('artwork_no')];		
			$main_data_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['printing_color_id']=$row[csf('printing_color_id')];
			$main_data_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['uom']=$row[csf('uom')];		
			$main_data_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['rate']=$row[csf('rate')];	
			$main_data_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['id']=$row[csf('id')];	

			$main_data_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['color_type_id']=$row[csf('color_type_id')];	
			$main_data_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['gsm_weight']=$row[csf('gsm_weight')];
			$main_data_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['job_no']=$row[csf('job_no')];	
			$main_data_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['style_ref']=$row[csf('style_ref_no')];	
			$main_data_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('fabric_description')]][$row[csf('fin_dia')]][$row[csf('fabric_color_id')]]['amount']+=$row[csf('amount')];	



			$fabric_color_summary[$row[csf('fabric_description')]][$row[csf('color_type_id')]][$row[csf('gsm_weight')]][$row[csf('fin_dia')]]['fabric_color'][$row[csf('fabric_color_id')]]['fabric_color_id']=$row[csf('fabric_color_id')];
			$fabric_color_summary[$row[csf('fabric_description')]][$row[csf('color_type_id')]][$row[csf('gsm_weight')]][$row[csf('fin_dia')]]['fabric_color'][$row[csf('fabric_color_id')]]['wo_qnty']+=$row[csf('wo_qnty')];			
		}

		$suppliar_data=sql_select("SELECT id, contact_no, email,web_site, address_1, address_2, address_3, address_4 from lib_supplier where status_active=1 and is_deleted=0");
		foreach($suppliar_data as $row){
			$supplier_address_arr[$row[csf('id')]]['address']=$row[csf('address_1')].' '.$row[csf('address_2')].' '.$row[csf('address_3')].' '.$row[csf('address_4')];
			$supplier_address_arr[$row[csf('id')]]['contact']=$row[csf('contact_no')];
			$supplier_address_arr[$row[csf('id')]]['email']=$row[csf('email')];
			$supplier_address_arr[$row[csf('id')]]['website']=$row[csf('web_site')];
		}
        $nameArray=sql_select( "select a.booking_no,a.booking_date,a.pay_mode,a.remarks,a.buyer_id,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.source, a.delivery_to, a.attention, a.tenor from wo_booking_mst a where  a.booking_no=$txt_booking_no and a.entry_form=228");
		

        foreach ($nameArray as $result)
        {
			$varcode_booking_no=$result[csf('booking_no')];
			$pay_mode=$result[csf('pay_mode')];$supplier_id=$result[csf('supplier_id')];
			$supp_address=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website,contact_no from lib_company where id=$supplier_id");
			if($pay_mode==5 || $pay_mode==3){
				$com_supp=$company_library[$supplier_id];
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
				<strong>&nbsp;<? echo $com_supp;?></strong><br>
				<? echo $suplier_address;?>
				</td>
				<td width="50%" style="font-size:16px" colspan="6" align="left">
				<strong>&nbsp;<? echo $company_library[$cbo_company_name];?></strong><br>
				<? echo $com_add;?><br>			
				<? echo "TEL:".$contact_no;  ?><br>
				<? echo "Email:".$email;  ?><br>
				<? echo $website;  ?><br>
				</td>

			</tr>
			<tr>
				<th align="left">Issue Date</th>
				<td>&nbsp;<? echo change_date_format($result[csf('booking_date')]);?></td>
				<th align="left">Delivery Date</th>
				<td>&nbsp;<? echo change_date_format($result[csf('delivery_date')]);?></td>
				<th align="left">Contact Person</th>
				<td>&nbsp;<? echo $result[csf('attention')];?></td>
				<th align="left">Buyer</th>
				<td>&nbsp;<? echo $buyer_name_arr[$result[csf('buyer_id')]];?></td>
				<th align="left">Tenor</th>
				<td>&nbsp;<? echo $result[csf('tenor')];?></td>
			</tr>
			<tr>
				<th align="left">Delivery Address</th>
				<td colspan="7">&nbsp;<? echo $result[csf('delivery_to')];?></td>
				<th align="left">Currency</th>
				<td>&nbsp;<? echo $currency[$currency_id];?></td>
			</tr>
			<tr>
				<th align="left">Remarks</th>
				<td colspan="9">&nbsp;<? echo $result[csf('remarks')];?></td>
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
				<th width="160">Gmts. Color</th>
				<th width="160">Fab Color</th>
				<th width="60">Grey Fab Qty</th>
				<th width="60">UOM</th>
				<? if($show_comments==1){?>
				<th width="60">Rate</th>
				<th width="100">Amount</th>
				<?}?>
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


					 	?>
					<tr>
					<?
							if($j==1){?>
							<td rowspan="<?=$job_id_arr[$job_id];?>"><?= $job_id  ?></td>
							<td rowspan="<?=$job_id_arr[$job_id];?>"><?= $row['style_ref']  ?></td>
							<td rowspan="<?=$job_id_arr[$job_id];?>"><?= implode(", ", $job_po_arr[$job_id])  ?></td>
							<? 

							}
							if($b==1){
					
							 ?>
							<td rowspan="<?=$body_id_arr[$job_id][$body_part_id];?>"><?= $lib_body_part[$body_part_id];  ?></td>
							<?}
							if($fab==1){
							?>
							<td rowspan="<?=$desc_id_arr[$job_id][$body_part_id][$desc_id];?>"><?= $desc_id;  ?></td>							
							<td rowspan="<?=$desc_id_arr[$job_id][$body_part_id][$desc_id];?>"><?= $color_type[$row['color_type_id']];  ?></td>
							<td rowspan="<?=$desc_id_arr[$job_id][$body_part_id][$desc_id];?>"><?= $row['gsm_weight'];  ?></td>	
							<?}?>						
							<td ><?= $dia_id;  ?></td>								
							<td><?= $color_library[$color_id];  ?></td>
							<td><?= $color_library[$color_id];  ?></td>
							<td align="right"><?= number_format($row['wo_qnty'],2)  ?></td>
							<td><?= $unit_of_measurement[$row['uom']]  ?></td>
							<? if($show_comments==1){?>							
							<td align="right"><?= $row['rate']  ?></td>
							<td align="right"><?= number_format($row['amount'],2)  ?></td>
							<?
								$color_wise_amount+=$row['amount'];
								$color_wise_grand_amount+=$row['amount'];
							}?>
							<? 
							$colortr++;
							$color_wise_qty+=$row['wo_qnty'];
							
							$color_wise_grand_qty+=$row['wo_qnty'];
						
							 ?>
							
							
						<? 
							$fab++;
							$f2++;$f++;$j++;$b++;					
						?>						
					</tr>
				<? } }}}}

				if($currency_id==1){$paysa_sent="Paisa";} else if($currency_id==2){$paysa_sent="CENTS";}
			?>
			<tr>
				<th colspan="10" align="right">Grand Total</th>
				<th align="right"><?= number_format($color_wise_grand_qty,2) ?></th>
				<th></th>
				<? if($show_comments==1){?>
				<th></th>
				<th align="right"><?= number_format($color_wise_grand_amount,2) ?></th>
				<?}?>
			</tr>
			<tr>
				<th colspan="10" align="right">Total Booking Amount (in word)</th>
				<th colspan="4" align="left"><? echo number_to_words(def_number_format($color_wise_grand_amount,2,""),$currency[$currency_id],$paysa_sent); ?></th>
			</tr>
		</table>
		<br><br>
		<?

$costing_per=""; $costing_per_qnty=0;
$costing_per_id=return_field_value( "costing_per", "wo_pre_cost_mst","job_no ='$job_no'");
if($costing_per_id==1)
   {
	   $costing_per="1 Dzn";
	   $costing_per_qnty=12;
   }
   if($costing_per_id==2)
   {
	   $costing_per="1 Pcs";
	   $costing_per_qnty=1;
   }
   if($costing_per_id==3)
   {
	   $costing_per="2 Dzn";
	   $costing_per_qnty=24;
   }
   if($costing_per_id==4)
   {
	   $costing_per="3 Dzn";
	   $costing_per_qnty=36;
   }
   if($costing_per_id==5)
   {
	   $costing_per="4 Dzn";
	   $costing_per_qnty=48;
   }
		$condition= new condition();
		if(str_replace("'","",$job_no) !=''){
			$condition->job_no("='$job_no'");
		}
		$condition->init();
		$cos_per_arr=$condition->getCostingPerArr();
		$yarn= new yarn($condition);
		$yarn_data_array=$yarn->getCountCompositionPercentTypeColorAndRateWiseYarnQtyAndAmountArray();
		$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
		$yarn_sql_array=sql_select("SELECT min(a.id) as id ,a.count_id, a.copm_one_id, a.percent_one, a.color, a.type_id, sum(a.cons_qnty) as yarn_required, a.rate,sum(b.wo_qnty*a.cons_ratio/100) as booking_qty  from wo_pre_cost_fab_yarn_cost_dtls a, wo_booking_dtls b, wo_pre_cost_fab_conv_cost_dtls c where a.job_no=b.job_no and c.id=b.pre_cost_fabric_cost_dtls_id and a.fabric_cost_dtls_id=c.fabric_description and a.job_no='$job_no' and b.booking_no=$txt_booking_no  and  b.booking_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.count_id,a.copm_one_id,a.percent_one,a.color,a.type_id,a.rate order by id");
		$po_sql=sql_select("select  plan_cut, po_quantity from wo_po_break_down where job_no_mst='$job_no' and status_active=1 and is_deleted=0 group by plan_cut, po_quantity");
        foreach($po_sql as $row)
        {
            $po_qnty_tot+=$row[csf('plan_cut')];
		}
		?>
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0" style="font-family:Arial Narrow;" >
            <tr>
                <td width="49%" valign="top">
                    <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                    <tr align="center">
                    <td colspan="8"><b>Yarn Required Summary (Pre Cost)</b></td>

                    </tr>
                    <tr align="center">
                    <td>Sl</td>
                    <td>Yarn Description</td>
                    <td>Brand</td>
                    <td>Lot</td>
                    <td>Rate</td>
                    <td>Cons for <? //echo $costing_per; ?> Gmts</td>
                    <td>Total (KG)</td>
					<td>Booking Qnty</td>
                    </tr>
                    <?
					$i=0;
					$total_yarn=0;$total_booking_qty=0;
					foreach($yarn_sql_array  as $row)
                    {

						$i++;
						$rowcons_qnty = $yarn_data_array[$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['qty'];
						$rowcons_Amt = $yarn_data_array[$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['amount'];
						$booing_qnty=$row[csf("booking_qty")];

						$rate=$rowcons_Amt/$rowcons_qnty;
						$rowcon_qnty =($rowcons_qnty/100);
					?>
                    <tr align="center">
                    <td><? echo $i; ?></td>
                    <td>
					<?
					$yarn_des=$yarn_count_arr[$row[csf('count_id')]]." ".$composition[$row[csf('copm_one_id')]]." ".$row[csf('percent_one')]."%  ";
					$yarn_des.=$color_library[$row[csf('color')]]." ";
					$yarn_des.=$yarn_type[$row[csf('type_id')]];
					echo $yarn_des;
					?>
                    </td>
                    <td></td>
                    <td></td>
                    <td><? echo number_format($row[csf('rate')],4); ?></td>
                    <td><? echo number_format(($rowcons_qnty/$po_qnty_tot)*$cos_per_arr[$job_no],4); ?></td>
                    <td align="right"><? echo number_format($rowcons_qnty,2); $total_yarn+=$rowcons_qnty; ?></td>
					<td align="right"><? echo number_format($booing_qnty,2); $total_booking_qty+=$booing_qnty; ?></td>
                    </tr>
                    <?
					}
					?>
                    <tr align="center">
                    <td colspan="6" align="right">Total</td>
                    <td align="right"><? echo number_format($total_yarn,2); ?></td>
					<td align="right"><? echo number_format($total_booking_qty,2); ?></td>
					<td></td>
                    </tr>
                    </table>
                </td>
			</tr>
		</table>
		<br><br>
         

       	<table  width="100%" class="rpt_table" style="margin:5px;font-size:16px;font-family:'Arial Narrow';"   border="1" cellpadding="0" cellspacing="0">
          <tr>
          <td><? echo get_spacial_instruction($txt_booking_no); ?></td>
          </tr>
        </table>
         <br/>
		 <?
            echo signature_table(82, $cbo_company_name, "1113px");
         ?>
    </div>

	<?
}

if($action=="show_trim_booking_report2" || $action=="show_trim_booking_report3")
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	//$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library",'master_tble_id','image_location');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$job_no="";
		$nameArray_job=sql_select( "select distinct b.job_no,a.is_approved  from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_no=$txt_booking_no"); 
        foreach ($nameArray_job as $result_job)
        {
			$job_no.=$result_job[csf('job_no')].",";
			$is_approved=$result_job[csf('is_approved')];
		}
		
	$path=($path=='')?'../../':$path;
		
	?>
	<div style="width:1150px" align="left">       
       <table width="90%" cellpadding="0" cellspacing="0" style="border:1px solid black">
           <tr>
               <td width="100"> 
               <img  src='<? echo $path.$imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="">                                     
                    <table width="90%" cellpadding="0" cellspacing="0" >
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
                                            Province No: <?php echo $result[csf('province')];?> 
                                            Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
                                            Email Address: <? echo $result[csf('email')];?> 
                                            Website No: <? echo $result[csf('website')];
                            }
                            ?>   
                               </td> 
                            </tr>
                            <tr>
                            <td align="center" style="font-size:20px">  
                            <strong>Service Booking Sheet For Knitting</strong>
                             </td> 
                             <td  width="50px">&nbsp; </td>
                             <td align="right">  &nbsp; &nbsp;<? if($is_approved==1) echo "<font style='color:red;font-size:25px;'><i>Approved</i></font>";else echo "";?></td>
                            </tr>
                      </table>
                </td> 
                <td width="250" id="barcode_img_id"> 
              
               </td>      
            </tr>
       </table>
		<?
		$booking_grand_total=0;
		
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
       <table width="90%" style="border:1px solid black">                    	
            <tr>
                <td colspan="6" valign="top"></td>                             
            </tr>                                                
            <tr>
                <td width="100" style="font-size:12px"><b>Work Order No </b>   </td>
                <td width="110">:&nbsp;<? echo $result[csf('booking_no')];?> </td>
                <td width="100" style="font-size:12px"><b>Booking Date</b></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>
                <td width="100"><span style="font-size:12px"><b>Delivery Date</b></span></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>	
            </tr>
            <tr>
                <td width="100" style="font-size:12px"><b>Currency</b></td>
                <td width="110">:&nbsp;<? echo $currency[$result[csf('currency_id')]]; ?></td>
                <td  width="100" style="font-size:12px"><b>Conversion Rate</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('exchange_rate')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Source</b></td>
                <td  width="110" >:&nbsp;<? echo $source[$result[csf('source')]]; ?></td>
            </tr> 
             <tr>
                <td width="100" style="font-size:12px"><b>Supplier Name</b>   </td>
                <td width="110">:&nbsp;<? echo $supplier_name_arr[$result[csf('supplier_id')]];?>    </td>
                 <td width="100" style="font-size:12px"><b>Supplier Address</b></td>
               	<td width="110">:&nbsp;<? echo $supplier_address_arr[$result[csf('supplier_id')]];?></td>
                <td  width="100" style="font-size:12px"><b>Attention</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('attention')]; ?></td>
            </tr>  
            <tr>
                <td width="100" style="font-size:12px"><b>Job No</b>   </td>
                <td width="110">:&nbsp;
				<? 
				echo $txt_job_no=rtrim($job_no,',');
				?> 
                </td>
                 
               	<td width="110" style="font-size:12px"><b>PO No</b> </td>
                <td  width="100" style="font-size:12px" colspan="3">:&nbsp;<? echo rtrim($po_no,','); ?> </td>
            </tr> 
        </table>  
		<?
        }
        ?>
          <!--==============================================AS PER GMTS COLOR START=========================================  -->
		<?
		//========================================
		$fabric_description_array=array();
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
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1 and process=1"); 
		//echo "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1"; 
        $nameArray_color=sql_select( "select distinct fabric_color_id from wo_booking_dtls   where  booking_no=$txt_booking_no and sensitivity=1 and process=1 and wo_qnty>0 and is_deleted=0 and status_active=1"); 
		if(count($nameArray_color)>0)
		{
        ?>
        <table border="0" align="left" class="rpt_table"  cellpadding="0" width="90%" cellspacing="0" >
            <tr>
                <td colspan="<? echo count($nameArray_color)+8; ?>" align="">
                <strong>As Per Garments Color</strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Service Type</strong> </td>
                <td style="border:1px solid black"><strong>Program No</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                		
                <td style="border:1px solid black" align="center"><strong>W/O Qty</strong></td>
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
            $nameArray_item_description=sql_select( "select distinct wo_qnty,program_no,description,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1 and process=".$result_item[csf('process')]." and wo_qnty>0 and is_deleted=0 and status_active=1 "); 
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
                <td align="center" style="border:1px solid black" ><? echo $result_itemdescription[csf('program_no')]; ?></td>
                <td style="border:1px solid black"><? echo rtrim($fabric_description_array[$result_itemdescription[csf('description')]],", "); ?> </td>
                

                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('wo_qnty')],2); $total_wo+=$result_itemdescription[csf('wo_qnty')]; ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_as_per_wo_qnty = $result_itemdescription[csf('wo_qnty')]*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_wo_qnty,2);
                $total_amount_as_per_wo_qnty+=$amount_as_per_wo_qnty;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="2"><strong>Total</strong></td>
               
                <td style="border:1px solid black;  text-align:right"><? echo number_format($total_wo,2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right">
                <? 
                echo number_format($total_amount_as_per_wo_qnty,2);
                $grand_total_as_per_wo_qnty+=$total_amount_as_per_wo_qnty; $booking_grand_total+=$grand_total_as_per_wo_qnty;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
           
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
		
		
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2 and process=1"); 
        $nameArray_size=sql_select( "select distinct  item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=2 and process=1 and wo_qnty>0 and is_deleted=0 and status_active=1");
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
            $nameArray_item_description=sql_select( "select distinct description,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2 and process=".$result_item[csf('process')]." and wo_qnty>0 and is_deleted=0 and status_active=1 "); 
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
					$nameArray_size_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where   booking_no=$txt_booking_no and sensitivity=2 and process=". $result_item[csf('process')]." and description='". $result_itemdescription[csf('description')]."' and item_size='".$result_size[csf('gmts_sizes')]."'");  
	
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
                $amount_as_per_gmts_color = $item_desctiption_total*$result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,2);
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
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_size)+7; ?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
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
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and process=1"); 
        $nameArray_color=sql_select( "select distinct fabric_color_id as color_number_id from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=3 and process=1 and wo_qnty>0 and is_deleted=0 and status_active=1"); 
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
            $nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and process=".$result_item[csf('process')]." and wo_qnty>0 and is_deleted=0 and status_active=1 "); 
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
                $nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls    where   booking_no=$txt_booking_no and sensitivity=3 and process=". $result_item[csf('process')]." and description='". $result_itemdescription[csf('description')]."' and fabric_color_id=".$result_color[csf('color_number_id')]."");                          
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
                $amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,2);
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
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_color)+7; ?>"><strong>Total</strong></td>
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
		//$nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2"); 
       // $nameArray_size=sql_select( "select distinct  item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=2");
	   //$nameArray_color=sql_select( "select distinct fabric_color_id as color_number_id from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=3"); 

        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=4 and process=1"); 
        $nameArray_size=sql_select( "select distinct item_size  as gmts_sizes from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4 and process=1");
	    $nameArray_color=sql_select( "select distinct fabric_color_id as color_number_id from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4 and process=1 and wo_qnty>0 and is_deleted=0 and status_active=1"); 

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
            $nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=4 and process=".$result_item[csf('process')]." and wo_qnty>0 and is_deleted=0 and status_active=1"); 
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
                $nameArray_size_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls   where booking_no=$txt_booking_no and sensitivity=4 and process=". $result_item[csf('process')]." and  description='". $result_itemdescription[csf('description')]."' and  item_size='".$result_size[csf('gmts_sizes')]."' and fabric_color_id=".$result_color[csf('color_number_id')]."");                          
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
                $amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,2);
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
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_size)+8; ?>"><strong>Total</strong></td>
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
        $nameArray_item=sql_select( "select distinct process from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 and process=1"); 
        //$nameArray_color=sql_select( "select distinct b.color_number_id from wo_trims_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.sensitivity=1"); 
		$nameArray_color= array();
		if(count($nameArray_item)>0)
		{
        ?>
        <table border="0" align="left" class="rpt_table"  cellpadding="0" width="90%" cellspacing="0" >
            <tr>
                <td colspan="7" align="">
                <strong>No Sensitivity</strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Service Type</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong></strong> </td>
                <td align="center" style="border:1px solid black"><strong> Qnty</strong></td>
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
            $nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 and process=".$result_item['process']." and wo_qnty>0 and is_deleted=0 and status_active=1"); 
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
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
                $nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls where    booking_no=$txt_booking_no and sensitivity=0 and process=". $result_item[csf('process')]." and  description='". $result_itemdescription[csf('description')]."'");                          
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
                
                <td style="border:1px solid black; text-align:center "><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
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
                <td style="border:1px solid black;  text-align:right">
                <?
                if($color_tatal !='')
                {
                echo number_format($color_tatal,2);  
                }
                ?>
                </td>
                <td style="border:1px solid black;"></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="7"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <?
		//print_r($color_tatal);
		}
		
		
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
        <!--==============================================NO NENSITIBITY END=========================================  -->
       &nbsp;
       <table  width="90%" class="rpt_table" style="border:1px solid black;"   border="0" cellpadding="0" cellspacing="0">
       <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount</th><td width="30%" style="border:1px solid black; text-align:right"><? echo number_format($booking_grand_total,2);?></td>
            </tr>
            <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount (in word)</th><td width="30%" style="border:1px solid black;"><? echo number_to_words(def_number_format($booking_grand_total,2,""),$mcurrency, $dcurrency);?></td>
            </tr>
       </table>
          &nbsp;
            <table  width="90%" class="rpt_table" style="border:1px solid black;"   border="0" cellpadding="0" cellspacing="0">
            <tr>
            <td>
				<?
                 echo get_spacial_instruction($txt_booking_no);
                ?>
            </td>
            </tr>
           </table>
           <!--Not used-->
        <table  width="90%" class="rpt_table" style="border:1px solid black; display:none"   border="0" cellpadding="0" cellspacing="0">
        <thead>
            <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th><th width="97%" style="border:1px solid black;">Spacial Instruction</th>
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
    <? if ($show_comments!=1) { ?>
    <table border="0" cellpadding="0" cellspacing="0"  width="90%" class="rpt_table"  style="border:1px solid black;" >
                <tr> <td style="border:1px solid black;" colspan="9" align="center"><b> Comments</b> </td></tr>
                <tr style="border:1px solid black;" align="center">
                    <th style="border:1px solid black;" width="40">SL</th>
                    <th style="border:1px solid black;" width="200">Job No</th>
                    <th style="border:1px solid black;" width="200">PO No</th>
                    <th style="border:1px solid black;" width="80">Ship Date</th>
                    <th style="border:1px solid black;" width="80">Pre-Cost/Budget Value</th>
                    <th style="border:1px solid black;" width="80">WO Value</th>
                    <th style="border:1px solid black;" width="80">Balance</th>
                    <th style="border:1px solid black;" width="">Comments </th>
                </tr>
       <tbody>
       <?
					$po_qty_arr=array();$knit_data_arr=array();
					$sql_po_qty=sql_select("select b.id as po_id,b.pub_shipment_date,sum(b.po_quantity) as order_quantity,(sum(b.po_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst   and a.is_deleted=0  and a.status_active=1 group by b.id,a.total_set_qnty,b.pub_shipment_date order by  b.id");
					foreach( $sql_po_qty as $row)
					{
						$po_qty_arr[$row[csf("po_id")]]['order_quantity']=$row[csf("order_quantity_set")];
						$po_qty_arr[$row[csf("po_id")]]['pub_shipment_date']=$row[csf("pub_shipment_date")];
					}
					$pre_cost=sql_select("select job_no,sum(amount) AS knit_cost from wo_pre_cost_fab_conv_cost_dtls where cons_process=1 and status_active=1 and is_deleted=0 group by job_no");
					foreach($pre_cost as $row)
					{ 
						$knit_data_arr[$row[csf('job_no')]]['knit']=$row[csf('knit_cost')];	
					}
					$i=1; $total_balance_knit=0;$tot_knit_cost=0;$tot_pre_cost=0;
					$sql_knit=( "select b.po_break_down_id as po_id,a.job_no,sum(b.amount) as amount from wo_booking_mst a, wo_booking_dtls b    where a.job_no=b.job_no and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.booking_type=3 and a.item_category=12 and  a.status_active=1  and a.is_deleted=0  group by b.po_break_down_id,a.job_no  order by b.po_break_down_id");
					
                    $nameArray=sql_select( $sql_knit );
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
						$po_qty=$po_qty_arr[$selectResult[csf('po_id')]]['order_quantity'];
						$pre_cost_knit=($knit_data_arr[$selectResult[csf('job_no')]]['knit']/$costing_per_qty)*$po_qty;
						$knit_charge=$selectResult[csf("amount")]/$result[csf('exchange_rate')];
						$ship_date=$po_qty_arr[$selectResult[csf("po_id")]]['pub_shipment_date'];
	   ?>
                    <tr>
                    <td style="border:1px solid black;" width="40"><? echo $i;?></td>
                    <td style="border:1px solid black;" width="200">
					<? echo $selectResult[csf('job_no')];?> 
                    </td>
                    <td style="border:1px solid black;" width="200">
					<? echo $po_number[$selectResult[csf('po_id')]];?> 
                    </td>
                    <td style="border:1px solid black;" width="80" align="right">
					<? echo change_date_format($ship_date);?> 
                    </td>
                     <td style="border:1px solid black;" width="80" align="right">
                     <? echo number_format($pre_cost_knit,2); ?>
                    </td>
                     <td style="border:1px solid black;" width="80" align="right">
                    <? echo number_format($knit_charge,2); ?>
                    </td>
                    <td style="border:1px solid black;" width="80" align="right">
                       <? $tot_balance=$pre_cost_knit-$knit_charge; echo number_format($tot_balance,2); ?>
                    </td>
                    <td style="border:1px solid black;" width="">
                    <? 
					if( $pre_cost_knit>$knit_charge)
						{
						echo "Less Booking";
						}
					else if ($pre_cost_knit<$knit_charge) 
						{
						echo "Over Booking";
						} 
					else if ($pre_cost_knit==$knit_charge) 
						{
							echo "As Per";
						} 
					else
						{
						echo "";
						}
						?>
                    </td>
                    </tr>
	   <?
	  	 $tot_pre_cost+=$pre_cost_knit;
	  	 $tot_knit_cost+=$knit_charge;
		 $total_balance_knit+=$tot_balance;
	   $i++;
					}
       ?>
	</tbody>
        <tfoot>
            <tr>
                <td style="border:1px solid black;" colspan="4" align="right">  <b>Total</b></td>
                <td style="border:1px solid black;" align="right"> <b><? echo number_format($tot_pre_cost,2); ?></b></td>
                <td style="border:1px solid black;"  align="right"><b> <? echo number_format($tot_knit_cost,2); ?> </b></td>
                <td style="border:1px solid black;"  align="right"><b> <? echo number_format($total_balance_knit,2); ?></b> </td>
                <td style="border:1px solid black;">&nbsp;  </td>
             </tr>
        </tfoot>
    </table>
    <br/>
	<?
	}
    echo signature_table(81, $cbo_company_name, "1113px");
	echo "****".custom_file_name($txt_booking_no,$style_sting,$txt_job_no);
    ?>
    </div>
	<script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
    fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
    </script>
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
		
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0";  disconnect($con);die;}		
		 $id=return_next_id( "id", "wo_booking_terms_condition", 1 ) ;
		 $field_array="id,booking_no,terms";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $termscondition="termscondition_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$txt_booking_no.",".$$termscondition.")";
			$id=$id+1;
		 }
		// echo  $data_array;
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
	 $sql= "select id,booking_no,booking_date,company_id,buyer_id,is_approved,job_no,po_break_down_id,item_category,fabric_source,is_short,currency_id,exchange_rate,pay_mode,booking_month,supplier_id,attention,tenor,delivery_date,source,booking_year,ready_to_approved,remarks,delivery_to from wo_booking_mst  where booking_no='$data'";     
	 $data_array=sql_select($sql);
	 foreach ($data_array as $row)
	 {
		echo "get_php_form_data( ".$row[csf("company_id")].", 'print_report_button', 'requires/service_booking_multi_job_wise_knitting_controller');\n";
		//echo "document.getElementById('txt_order_no_id').value = '".$row[csf("po_break_down_id")]."';\n";  
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";  
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";  
		echo "document.getElementById('txt_delivery_to').value = '".$row[csf("delivery_to")]."';\n";  
		//	echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('txt_booking_no').value = '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('cbo_process').value = '1';\n";
		echo "$('#cbo_company_name').attr('disabled',true);\n";
		//echo "document.getElementById('cbo_fabric_source').value = '".$row[csf("fabric_source")]."';\n";
		echo "document.getElementById('cbo_currency').value = '".$row[csf("currency_id")]."';\n";	
		echo "document.getElementById('cbo_short_type').value = '".$row[csf("is_short")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value = '".$row[csf("exchange_rate")]."';\n";
		echo "document.getElementById('cbo_pay_mode').value = '".$row[csf("pay_mode")]."';\n";
		echo "document.getElementById('txt_booking_date').value = '".change_date_format($row[csf("booking_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('cbo_booking_month').value = '".$row[csf("booking_month")]."';\n";
		$paymodeData=$row[csf("pay_mode")].'_'.$row[csf("buyer_id")].'_'.$row[csf("company_id")];
		echo "load_drop_down( 'requires/service_booking_multi_job_wise_knitting_controller', '".$paymodeData."', 'load_drop_down_supplier', 'supplier_td' );\n";
		
		echo "document.getElementById('cbo_supplier_name').value = '".$row[csf("supplier_id")]."';\n";
        echo "$('#cbo_supplier_name').prop('disabled', true);";
		echo "document.getElementById('txt_attention').value = '".$row[csf("attention")]."';\n";
		echo "document.getElementById('txt_tenor').value = '".$row[csf("tenor")]."';\n";
		echo "document.getElementById('update_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_ready_to_approved').value = '".$row[csf("ready_to_approved")]."';\n";
		echo "document.getElementById('txt_delivery_date').value = '".change_date_format($row[csf("delivery_date")],'dd-mm-yyyy','-')."';\n";
	    echo "document.getElementById('cbo_source').value = '".$row[csf("source")]."';\n";
		echo "document.getElementById('cbo_booking_year').value = '".$row[csf("booking_year")]."';\n";
		echo "document.getElementById('txt_remark').value = '".$row[csf("remarks")]."';\n";
		if($row[csf("is_approved")]==1){
			echo "document.getElementById('app_status').innerHTML = 'This booking is approved';\n";
			
		}else{
			echo "document.getElementById('app_status').innerHTML = '';\n";
			
		}
	//	$rate_from_library=return_field_value("po_break_down_id", "wo_booking_dtls", "service_process_id=2 and booking_no='".$row[csf("booking_no")]."' and status_active=1 and is_deleted=0 ");
		
		$po_no="";$job_no="";
		$sql_po= "select b.po_number,c.job_no from  wo_po_break_down b,wo_booking_dtls c  where c.po_break_down_id=b.id and c.booking_no in(".$row[csf('booking_no')].")"; 
		$data_array_po=sql_select($sql_po);
		foreach ($data_array_po as $row_po)
		{
			$po_no.=$row_po[csf('po_number')].",";
			if($job_no=="") $job_no="'".$job_no."'";else $job_no.=","."'".$job_no."'";
		}
		//echo "document.getElementById('txt_order_no').value = '".substr($po_no, 0, -1)."';\n";
		echo "load_drop_down( 'requires/service_booking_multi_job_wise_knitting_controller', '".$job_no."_".$row[csf("booking_no")]."', 'load_drop_down_fabric_description', 'fabric_description_td' )\n";
		
		$rate_from_library=0;
		$rate_from_library=return_field_value("is_serveice_rate_lib", "variable_settings_production", "service_process_id=2 and company_name=".$row[csf("company_id")]." and status_active=1 and is_deleted=0 ");
		echo "document.getElementById('service_rate_from').value = '".$rate_from_library."';\n";

		$is_editable=return_field_value("editable", "variable_order_tracking ", "company_name=".$row[csf("company_id")]." and variable_list=91");
		echo "document.getElementById('txt_amount_vali_id').value = '".$is_editable."';\n";
		
		//echo "load_drop_down( 'requires/service_booking_multi_job_wise_knitting_controller', '".$row[csf("job_no")]."', 'load_drop_down_process', 'process_td' )\n";
	 }
}

if ($action=="Supplier_workorder_popup")
{
	echo load_html_head_contents("Production Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
?> 
	<script>
		var permission='<? echo $permission; ?>';
		
		function js_set_value(id,rate,cons_compo)
		{
			document.getElementById('hide_charge_id').value=id;
			document.getElementById('hide_supplier_rate').value=rate;
			document.getElementById('hide_construction_compo').value=cons_compo;
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>

	<form name="searchdescfrm"  id="searchdescfrm">
            <input type="hidden" name="hide_supplier_rate" id="hide_supplier_rate" class="text_boxes" value="">
            <input type="hidden" name="hide_charge_id" id="hide_charge_id" class="text_boxes" value="">
            <input type="hidden" name="hide_construction_compo" id="hide_construction_compo" class="text_boxes" value="">
            <div style="width:720px;max-height:450px;" align="center">
                <table cellspacing="0" width="700" cellpadding="0" class="rpt_table" rules="all" border="1" id="tbl_list_search">
                	<thead>
                    	<th width="35">SL</th>
                        <th width="150">Body Part</th>
                        <th width="200">Construction & Composition </th>
                        <th width="50">GSM</th>
                        <th width="150">Yarn Description </th>
                        <th width="50">UOM</th>
                        <th width="">rate</th>
                    </thead>
                    <tbody id="supplier_body">
						<?
						
						$supplier_sql=sql_select("select c.id as ID,c.mst_id as MST_ID,a.supplier_name as NAME,c.supplier_rate as RATE,d.body_part as BODY_PART,d.const_comp as CONST_COMP,d.gsm as GSM,d.yarn_description as YARN_DESCRIPTION,d.uom_id as UOM_ID from lib_supplier a, lib_supplier_party_type b,lib_subcon_supplier_rate c,lib_subcon_charge d where a.id=b.supplier_id and b.party_type=20 and b.supplier_id=c.supplier_id and c.mst_id=d.id and d.rate_type_id=2 and d.comapny_id=$cbo_company_name and a.id=$cbo_supplier_name");
						
						$i=1;
						foreach($supplier_sql as $row)
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$rate=$row['RATE']/($txt_exchange_rate*1);
							if($hidden_supplier_rate_id==$row['ID'])  $bgcolor="#FFFF00";
							
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" align="center" valign="middle" height="25" onClick="js_set_value('<? echo $row['ID']; ?>','<? echo $rate; ?>','<? echo $row['CONST_COMP']; ?>')" style="cursor:pointer"> 
								<td><?php echo $i; ?></td>
								<td align="left"><? echo $body_part[$row['BODY_PART']]; ?>
								</td>
                                <td align="left"><? echo $row['CONST_COMP']; ?></td>
                                <td align="left"><? echo $row['GSM']; ?></td>
                                <td align="left"><? echo $row['YARN_DESCRIPTION']; ?></td>
                                <td align="left"><? echo $unit_of_measurement[$row['UOM_ID']]; ?></td>
								<td><? echo number_format($rate,4,".",""); ?>
									
                                    <input type="hidden"name="update_details_id[]" id="update_details_id_<? echo $i; ?>" value="<? echo $row['ID']; ?>">
								</td>
							</tr>
							<? 
							$i++;
						}
                        ?>
                    </tbody>
                </table>
               
            </div>
	</form>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}


?>