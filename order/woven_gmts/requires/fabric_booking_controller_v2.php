<? 
/*-------------------------------------------- Comments
Version                  :  V1
Purpose			         : 	This Form will create Dia Wise Fabric Booking
Functionality	         :	
JS Functions	         :
Created by		         :	Monzu 
Creation date 	         : 	23-12-2015
Requirment Client        :  AKH
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
-----------------------------------------------------*/
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$user_id=$_SESSION['logic_erp']['user_id'];
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');

//---------------------------------------------------- Start---------------------------------------------------------------------------
if ($action=="load_drop_down_buyer"){
	echo create_drop_down( "cbo_buyer_name", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "","","" );
}

if ($action=="load_drop_down_suplier"){
	if($data==5 || $data==3){
	echo create_drop_down( "cbo_supplier_name", 172, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "-- Select Supplier --", "", "validate_suplier()",0,"" );
	}
	else{
	echo create_drop_down( "cbo_supplier_name", 172, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=9 and   a.status_active =1 and a.is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "",0 );

	}
}

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
if($action=="check_month_maintain"){ 
	$sql_result=sql_select("select tna_integrated from variable_order_tracking where company_name='$data' and variable_list=14 and status_active=1 and is_deleted=0");
	$maintain_setting=$sql_result[0][csf('tna_integrated')];
	if($maintain_setting==1){
		echo "1"."_";
	}
	else{
		echo "0"."_";
	}
	exit();	
}


if ($action=="fabric_booking_popup"){
  	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
?>
	<script>
	function js_set_value(booking_no){
		document.getElementById('selected_booking').value=booking_no;
		parent.emailwindow.hide();
	}
    </script>
</head>
<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="1100" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
        <thead>
        <tr>
            <th  colspan="8">
            <input type="hidden" id="cbo_search_category"> 
            </th>
        </tr>
        <tr>              	 
            <th width="150">Company Name</th>
            <th width="150">Buyer Name</th>
            <th width="100">Booking No</th>
            <th width="100">Job No</th>
            <th width="100">File No</th>
            <th width="100">Internal Ref.</th>
            <th width="200">Date Range</th>
            <th></th> 
        </tr>          
        </thead>
        <tr>
            <td> 
            <input type="hidden" id="selected_booking"> 
            <? 
            echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '', "load_drop_down( 'size_color_breakdown_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
            ?>
            </td>
            <td id="buyer_td">
            <? 
            echo create_drop_down( "cbo_buyer_name", 172, $blank_array,"", 1, "-- Select Buyer --" );
            ?>	
            </td>
            <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:100px"></td>
            <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:100px"></td>
            <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:90px"></td>
            <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:90px"></td>
            <td>
            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
            </td>
            <td align="center">
            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_internal_ref').value, 'create_booking_search_list_view', 'search_div', 'fabric_booking_controller_v2','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
            </td>
        </tr>
        <tr>
            <td colspan="8"  align="center" height="40" valign="middle">
            <? 
			echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );		
			?>
			<? echo load_month_buttons();  ?>
            </td>
        </tr>
        <tr>
            <td  colspan="8" align="center"valign="top" id="search_div"></td>
        </tr>
    </table>    
    </form>
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
	if($db_type==0) $year_cond=" and SUBSTRING_INDEX(b.insert_date, '-', 1)=$data[5]";
	if($db_type==2) $year_cond=" and to_char(b.insert_date,'YYYY')=$data[5]";
	if($db_type==0) $booking_year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[5]";
	if($db_type==2) $booking_year_cond=" and to_char(a.insert_date,'YYYY')=$data[5]";
	if($data[7]==1){
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num='$data[6]'"; else  $booking_cond="";
		if (str_replace("'","",$data[4])!="") $job_cond=" and b.job_no_prefix_num='$data[4]'"; else  $job_cond=""; 
	}
	if($data[7]==4 || $data[7]==0){
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[6]%'  $booking_year_cond  "; else  $booking_cond="";
		if (str_replace("'","",$data[4])!="") $job_cond=" and b.job_no_prefix_num like '%$data[4]%'  $year_cond  "; else  $job_cond=""; 
	}
	
	if($data[7]==2){
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '$data[6]%'  $booking_year_cond  "; else  $booking_cond="";
		if (str_replace("'","",$data[4])!="") $job_cond=" and b.job_no_prefix_num like '$data[4]%'  $year_cond  "; else  $job_cond=""; 
	}
	
	if($data[7]==3){
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[6]'  $booking_year_cond  "; else  $booking_cond="";
		if (str_replace("'","",$data[4])!="") $job_cond=" and b.job_no_prefix_num like '%$data[4]'  $year_cond  "; else  $job_cond=""; 
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
	//echo "select a.booking_no,a.po_break_down_id,a.job_no from wo_booking_mst  a where $company $buyer $booking_date and a.booking_type=1 and a.is_short=3 and   a.status_active=1  and 	a.is_deleted=0 order by a.booking_no";
	$sql_po= sql_select("select a.booking_no,a.po_break_down_id,a.job_no from wo_booking_mst  a where $company $buyer $booking_date and a.booking_type=1 and a.is_short=3 and   a.status_active=1  and 	a.is_deleted=0 order by a.booking_no");
	foreach($sql_po as $row)
	{
		$po_id=explode(",",$row[csf("po_break_down_id")]);
		$job_prefix_arr=explode("-",$row[csf("job_no")]);
		$po_number_string="";
		foreach($po_id as $key=> $value )
		{
			$po_number_string.=$po_number[$value].",";
		}
		$po_array[$row[csf("po_break_down_id")]]=rtrim($po_number_string,",");
		$job_prefix_num[$row[csf("job_no")]]=ltrim($job_prefix_arr[2],0);
	}
	$approved=array(0=>"No",1=>"Yes");
	$is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$arr=array (2=>$comp,3=>$buyer_arr,4=>$job_prefix_num,6=>$po_array,9=>$item_category,10=>$fabric_source,11=>$suplier,12=>$approved,13=>$is_ready);
	$sql= "select a.booking_no_prefix_num,c.file_no,c.grouping,a.booking_date,a.company_id,a.buyer_id,a.job_no,a.po_break_down_id,a.item_category,a.fabric_source,a.supplier_id,a.is_approved,a.booking_no,a.ready_to_approved,b.style_ref_no  from wo_booking_mst a, wo_po_details_master b ,wo_po_break_down c  where $company $buyer $job_cond $booking_date $booking_cond  $file_no_cond  $internal_ref_cond". set_user_lavel_filtering(' and a.buyer_id','buyer_id')." and a.job_no=b.job_no and a.job_no=c.job_no_mst and b.job_no=c.job_no_mst and a.booking_type=1 and a.is_short=3 and   a.status_active=1  and 	a.is_deleted=0 order by a.booking_no"; 
	
	echo  create_list_view("list_view", "Booking No,Booking Date,Company,Buyer,Job No., Style Ref.,PO number,Internal Ref,File No,Fabric Nature,Fabric Source,Supplier,Approved,Is-Ready", "80,80,70,100,90,100,200,100,100,80,80,50,50","1320","320",0, $sql , "js_set_value", "booking_no", "", 1, "0,0,company_id,buyer_id,job_no,0,po_break_down_id,0,0,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", $arr , "booking_no_prefix_num,booking_date,company_id,buyer_id,job_no,style_ref_no,po_break_down_id,grouping,file_no,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", '','','0,3,0,0,0,0,0,0,0,0,0,0,0,0','','');
}

if ($action=="populate_data_from_search_popup"){
	 $sql= "select booking_no,booking_date,company_id,buyer_id,job_no,po_break_down_id,item_category,fabric_source,currency_id,exchange_rate,pay_mode,booking_month,supplier_id,attention,booking_percent,delivery_date,source,booking_year,colar_excess_percent,cuff_excess_percent,is_approved,ready_to_approved,is_apply_last_update,rmg_process_breakdown,fabric_composition from wo_booking_mst  where booking_no='$data'"; 
	 $poid=''; 
	 $cbo_fabric_natu=''; 
	 $cbo_fabric_source='';
	 $data_array=sql_select($sql);
	 foreach ($data_array as $row){
		$poid=$row[csf("po_break_down_id")]; 
	    $cbo_fabric_natu=$row[csf("item_category")]; 
	    $cbo_fabric_source=$row[csf("fabric_source")];
		
		echo "document.getElementById('txt_order_no_id').value = '".$row[csf("po_break_down_id")]."';\n";  
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";  
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";  
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('txt_booking_no').value = '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('cbo_fabric_natu').value = '".$row[csf("item_category")]."';\n";
		echo "document.getElementById('cbo_fabric_source').value = '".$row[csf("fabric_source")]."';\n";
		echo "document.getElementById('cbo_currency').value = '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value = '".$row[csf("exchange_rate")]."';\n";
		echo "document.getElementById('cbo_pay_mode').value = '".$row[csf("pay_mode")]."';\n";
		echo "document.getElementById('txt_booking_date').value = '".change_date_format($row[csf("booking_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('cbo_booking_month').value = '".$row[csf("booking_month")]."';\n";
		echo "document.getElementById('cbo_supplier_name').value = '".$row[csf("supplier_id")]."';\n";
		echo "document.getElementById('txt_attention').value = '".$row[csf("attention")]."';\n";
		echo "document.getElementById('txt_booking_percent').value = '".$row[csf("booking_percent")]."';\n";
		echo "document.getElementById('txt_delivery_date').value = '".change_date_format($row[csf("delivery_date")],'dd-mm-yyyy','-')."';\n";
	    echo "document.getElementById('cbo_source').value = '".$row[csf("source")]."';\n";
		echo "document.getElementById('cbo_booking_year').value = '".$row[csf("booking_year")]."';\n";
		echo "document.getElementById('txt_colar_excess_percent').value = '".$row[csf("colar_excess_percent")]."';\n";
		echo "document.getElementById('txt_cuff_excess_percent').value = '".$row[csf("cuff_excess_percent")]."';\n";
		echo "document.getElementById('id_approved_id').value = '".$row[csf("is_approved")]."';\n";
		echo "document.getElementById('cbo_ready_to_approved').value = '".$row[csf("ready_to_approved")]."';\n";
		echo "document.getElementById('processloss_breck_down').value = '".$row[csf("rmg_process_breakdown")]."';\n";
		echo "document.getElementById('txt_fabriccomposition').value = '".$row[csf("fabric_composition")]."';\n";
		
		if($row[csf("is_approved")]==1){
			echo "document.getElementById('app_sms2').innerHTML = 'This booking is approved';\n";
			echo "document.getElementById('txt_un_appv_request').disabled = '".false."';\n";
		}else{
			echo "document.getElementById('app_sms2').innerHTML = '';\n";
			echo "document.getElementById('txt_un_appv_request').disabled = '".true."';\n";
		}
		
		if($row[csf("is_apply_last_update")]==2){
			echo "document.getElementById('app_sms3').innerHTML = 'Booking Info not synchronized with order entry and pre-costing. order entry or pre-costing has updated after booking entry.  Contact to ".$marchentrArr[$po_m]."';\n";
		}else{
			echo "document.getElementById('app_sms3').innerHTML = '';\n";
		}
		
		$colar_culff_percent=return_field_value("colar_culff_percent", "variable_order_tracking", "company_name='".$row[csf("company_id")]."'  and variable_list=40 and status_active=1 and is_deleted=0");
		if($colar_culff_percent==1){
			echo "$('#txt_colar_excess_percent').removeAttr('disabled')".";\n";
			echo "$('#txt_cuff_excess_percent').removeAttr('disabled')".";\n";
		}
		if($colar_culff_percent==2){
			echo "$('#txt_colar_excess_percent').attr('disabled','true')".";\n";
		    echo "$('#txt_cuff_excess_percent').attr('disabled','true')".";\n";
		}

		$po_no="";
		$sql_po= "select po_number from  wo_po_break_down  where id in(".$row[csf('po_break_down_id')].")"; 
		$data_array_po=sql_select($sql_po);
		foreach ($data_array_po as $row_po)
		{
			$po_no.=$row_po[csf('po_number')].",";
		}
		echo "document.getElementById('txt_order_no').value = '".substr($po_no, 0, -1)."';\n";
		
		$po_m="";
		$sql_m= "select dealing_marchant from   wo_po_details_master  where job_no='".$row[csf("job_no")]."'"; 
		$data_array_m=sql_select($sql_m);
		foreach ($data_array_m as $row_m)
		{
			$po_m=$row_m[csf('dealing_marchant')];
		}
		
		$sql_delevary=sql_select("select task_number,max(task_finish_date) as task_finish_date from tna_process_mst where po_number_id in(".$row[csf("po_break_down_id")].") and task_number in(73) and is_deleted = 0 and 	status_active=1 group by task_number");
		foreach($sql_delevary as $row_delevary)
		{
		   echo "document.getElementById('txt_delivery_date').value = '".change_date_format($row_delevary[csf("task_finish_date")],'dd-mm-yyyy','-')."';\n"; 
		   echo "document.getElementById('txt_tna_date').value = '".change_date_format($row_delevary[csf("task_finish_date")],'dd-mm-yyyy','-')."';\n";
		}
		
		if($db_type==2){
			$group_concat_all=" listagg(cast(b.grouping as varchar2(4000)),',') within group (order by b.grouping) as grouping, listagg(cast(b.file_no as varchar2(4000)),',') within group (order by b.file_no) as file_no  ";
		}else{ 
			$group_concat_all="group_concat(b.grouping) as grouping, group_concat(b.file_no) as file_no";
		}
	
		$data_array3=sql_select("select a.job_no,a.company_name,a.buyer_name,$group_concat_all from wo_po_details_master a, wo_po_break_down b where b.id in (".$row[csf("po_break_down_id")].") and a.job_no=b.job_no_mst group by a.job_no,a.company_name,a.buyer_name");
		foreach($data_array3 as $inv){
			$grouping=implode(",",array_unique(explode(",",$inv[csf("grouping")])));
			$file_no=implode(",",array_unique(explode(",",$inv[csf("file_no")])));
			echo "document.getElementById('txt_file_no').value = '".$file_no."';\n";
			echo "document.getElementById('txt_intarnal_ref').value = '".$grouping."';\n";
		}
	 }
	 load_drop_down_po_number($poid, $cbo_fabric_natu, $cbo_fabric_source);
	 load_drop_down_po_item($poid, $cbo_fabric_natu, $cbo_fabric_source);
}

if ($action=="order_search_popup"){
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$booking_month=0;
	if(str_replace("'","",$cbo_booking_month)<10){
		$booking_month.=str_replace("'","",$cbo_booking_month);
	}
	else{
		$booking_month=str_replace("'","",$cbo_booking_month); 
	}
	$start_date="01"."-".$booking_month."-".str_replace("'","",$cbo_booking_year);
	$end_date=cal_days_in_month(CAL_GREGORIAN, $booking_month, str_replace("'","",$cbo_booking_year))."-".$booking_month."-".str_replace("'","",$cbo_booking_year);
	if($booking_month!=0){
		$start_date=$start_date;
		$end_date=$end_date;
	}
	else{
		$start_date='';
		$end_date='';
	}
	$cbo_string_search_drop_down=create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "-- Searching Type --" );
	$cbo_company_drop_down= create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "- Select Company -", str_replace("'","",$cbo_company_name), "load_drop_down( 'fabric_booking_controller_v2', this.value, 'load_drop_down_buyer', 'buyer_td' );");
	$cbo_buyer_drop_down=create_drop_down( "cbo_buyer_name", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", str_replace("'","",$cbo_buyer_name), "" );
	?>
	<script>
		var selected_id = new Array, selected_name = new Array();	
		function check_all_data(){
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 
			tbl_row_count = tbl_row_count;
			for( var i = 1; i <= tbl_row_count; i++ ){
				js_set_value( i );
			}
		}
		
		function toggle( x, origColor ){
			var newColor = 'yellow';
			document.getElementById(x).style.backgroundColor = ( newColor == document.getElementById(x).style.backgroundColor )? origColor : newColor;
		}
		
		function js_set_value( str_data,tr_id ){
			var str_all=str_data.split("_");
			var str_po=str_all[1];
			var str=str_all[0];
			if ( document.getElementById('job_no').value!="" && document.getElementById('job_no').value!=str_all[2] ){
				alert('No Job Mix Allowed')
				return;	
			}
			
			toggle( tr_id, '#FFFFCC');
			document.getElementById('job_no').value=str_all[2];
			
			if( jQuery.inArray( str , selected_id ) == -1 ){
				selected_id.push( str );
				selected_name.push( str_po );
			}
			else{
				for( var i = 0; i < selected_id.length; i++ ){
					if( selected_id[i] == str ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				if(selected_id.length==0){
					document.getElementById('job_no').value="";
				}
			}
			var id = '' ; var name = '';
			for( var i = 0; i < selected_id.length; i++ ){
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
            <form name="searchpofrm_1" id="searchpofrm_1">
                <table  width="1200" class="rpt_table" align="center" rules="all">
                    <thead> 
                        <tr>
                            <th width="150" colspan="4"> </th>
                            <th><? echo $cbo_string_search_drop_down;?></th>
                            <th width="150" colspan="4"> </th>
                        </tr> 
                        <tr>               	 
                            <th width="150">Company Name</th>
                            <th width="150">Buyer Name</th>
                            <th width="100">Job No</th>
                            <th width="100">Internal Ref</th>
                            <th width="100">File No</th>
                            <th width="100">Style Ref </th>
                            <th width="150">Order No</th>
                            <th width="200">Date Range</th>
                            <th></th>
                        </tr>           
                    </thead>
                    <tr>
                        <td> 
                        <? echo $cbo_company_drop_down; ?>
                        </td>
                        <td id="buyer_td">
                        <? echo $cbo_buyer_drop_down; ?>	
                        </td>
                        <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:100px"></td>
                        <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:90px"></td>
                        <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:90px"></td>
                        <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:100px"></td>
                        <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:150px"></td>
                        <td>
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:85px" value="<? echo $start_date; ?>"/>
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:85px" value="<? echo $end_date; ?>"/>
                        </td> 
                        <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value, 'create_po_search_list_view', 'search_div', 'fabric_booking_controller_v2', 'setFilterGrid(\'list_view\',-1)')" style="width:100%;" />
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Selected PO Number:</strong></td>
                        <td colspan="7" align="center">
                        <input type="text" class="text_boxes" readonly style="width:98%" id="po_number">
                        <input type="hidden" id="po_number_id">
                        <input type="hidden" id="job_no">
                        </td>
                        <td><input type="button" name="close" onClick="parent.emailwindow.hide();"  class="formbutton" value="Close" style="width:100%" /> </td>
                    </tr>
                    <tr>
                        <td colspan="9" id="search_div" align="center" style="padding:2px">
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
	if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else $buyer=""; //{ echo "Please Select Buyer First."; die; }
	$job_cond="";
	$order_cond=""; 
	$style_cond="";
	if($data[7]==1){
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num='$data[4]'"; //else  $job_cond=""; 
		if (str_replace("'","",$data[5])!="") $order_cond=" and b.po_number = '$data[5]'  "; //else  $order_cond=""; 
		if (trim($data[6])!="") $style_cond=" and a.style_ref_no ='$data[6]'"; //else  $style_cond=""; 
	}
	if($data[7]==2){
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '$data[4]%'"; //else  $job_cond=""; 
		if (str_replace("'","",$data[5])!="") $order_cond=" and b.po_number like '$data[5]%'  "; //else  $order_cond=""; 
		if (trim($data[6])!="") $style_cond=" and a.style_ref_no like '$data[6]%'  "; //else  $style_cond=""; 
	}
	if($data[7]==3){
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '%$data[4]'"; //else  $job_cond=""; 
		if (str_replace("'","",$data[5])!="") $order_cond=" and b.po_number like '%$data[5]'  "; //else  $order_cond=""; 
		if (trim($data[6])!="") $style_cond=" and a.style_ref_no like '%$data[6]'"; //else  $style_cond=""; 
	}
	if($data[7]==4 || $data[7]==0){
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '%$data[4]%'"; //else  $job_cond=""; 
		if (str_replace("'","",$data[5])!="") $order_cond=" and b.po_number like '%$data[5]%'  "; //else  $order_cond=""; 
		if (trim($data[6])!="") $style_cond=" and a.style_ref_no like '%$data[6]%'"; //else  $style_cond=""; 
	}
	
	$internal_ref = str_replace("'","",$data[8]);
	$file_no = str_replace("'","",$data[9]);
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='".trim($file_no)."' "; 
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping like '%".trim($internal_ref)."%' "; 
	
	
	if($db_type==0){
		if ($data[2]!="" &&  $data[3]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}
	
	if($db_type==2){
		if ($data[2]!="" &&  $data[3]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}
	
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$arr=array (1=>$comp,2=>$buyer_arr);
	$sql= "select a.job_no_prefix_num,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity,b.id, b.po_number,b.grouping,b.file_no,b.po_quantity,b.shipment_date,a.job_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." and a.status_active=1 and b.status_active=1  $shipment_date $company $buyer $job_cond $order_cond $style_cond $file_no_cond $internal_ref_cond order by a.job_no"; 
	
	echo  create_list_view("list_view", "Job No,Company,Buyer,Style Ref. No,Internal Ref,File No,Job Qty.,PO number,PO Qty,Shipment Date", "60,60,50,100,100,100,70,150,80","1200","320",0, $sql , "js_set_value", "id,po_number,job_no", "this.id", 1, "0,company_name,buyer_name,0,0,0,0,0,0", $arr , "job_no_prefix_num,company_name,buyer_name,style_ref_no,grouping,file_no,job_quantity,po_number,po_quantity,shipment_date", '','','0,0,0,0,0,0,1,0,1,3','','');
} 
if ($action=="populate_order_data_from_search_popup")
{
	$data=explode("_",$data);
	$poid = $data[0];
	$cbo_fabric_natu = $data[1];
	$cbo_fabric_source = $data[2];
	if($db_type==2) $group_concat_all=" listagg(cast(b.grouping as varchar2(4000)),',') within group (order by b.grouping) as grouping, 
	                                    listagg(cast(b.file_no as varchar2(4000)),',') within group (order by b.file_no) as file_no  ";
	else { $group_concat_all="group_concat(b.grouping) as grouping, group_concat(b.file_no) as file_no";}
	
	$data_array=sql_select("select a.job_no,a.company_name,a.buyer_name,$group_concat_all from wo_po_details_master a, wo_po_break_down b where b.id in (".$poid.") and a.job_no=b.job_no_mst group by a.job_no,a.company_name,a.buyer_name");
	foreach ($data_array as $row)
	{
		$grouping=implode(",",array_unique(explode(",",$row[csf("grouping")])));
		$file_no=implode(",",array_unique(explode(",",$row[csf("file_no")])));
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_name")]."';\n"; 
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_name")]."';\n";  
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('txt_intarnal_ref').value = '".$grouping."';\n";
		echo "document.getElementById('txt_file_no').value = '".$file_no."';\n";
		$booking_no="";
		$sql= sql_select("select booking_no from wo_booking_mst  where job_no='".$row[csf("job_no")]."' and booking_type=1");
		foreach($sql as $sql_row)
		{
			$booking_no.=$sql_row[csf('booking_no')].", ";
		}
		
		if($booking_no=="")
		{
			 echo "document.getElementById('app_sms3').innerHTML = '';\n";
		}
		else
		{
			echo "document.getElementById('app_sms3').innerHTML = 'Booking No ".rtrim($booking_no ,", ")." is found against  this Job No';\n";
		}
		
		$colar_culff_percent=return_field_value("colar_culff_percent", "variable_order_tracking", "company_name='".$row[csf("company_name")]."'  and variable_list=40 and status_active=1 and is_deleted=0");
		if($colar_culff_percent==1)
		{
			echo "$('#txt_colar_excess_percent').removeAttr('disabled')".";\n";
			echo "$('#txt_cuff_excess_percent').removeAttr('disabled')".";\n";
		}
		if($colar_culff_percent==2)
		{
			echo "$('#txt_colar_excess_percent').attr('disabled','true')".";\n";
		    echo "$('#txt_cuff_excess_percent').attr('disabled','true')".";\n";
		}
		
		$sql_delevary=sql_select("select task_number,max(task_finish_date) as task_finish_date from tna_process_mst where po_number_id in(".$poid.") and task_number in(73) and is_deleted = 0 and 	status_active=1 group by task_number");
		foreach($sql_delevary as $row_delevary)
		{
		   echo "document.getElementById('txt_delivery_date').value = '".change_date_format($row_delevary[csf("task_finish_date")],'dd-mm-yyyy','-')."';\n"; 
		   echo "document.getElementById('txt_tna_date').value = '".change_date_format($row_delevary[csf("task_finish_date")],'dd-mm-yyyy','-')."';\n";
		}
	}
	
    load_drop_down_po_number($poid, $cbo_fabric_natu, $cbo_fabric_source);
	load_drop_down_po_item($poid, $cbo_fabric_natu, $cbo_fabric_source);
}


function load_drop_down_po_number($poid,$cbo_fabric_natu,$cbo_fabric_source){
	$po_number=array();
	$po_id=array();
	$sql=sql_select( "select id,po_number from wo_po_break_down where id in ($poid)");
	foreach($sql as $row){
	$po_number[$row[csf('id')]]=$row[csf('po_number')];	
	$po_id[$row[csf('id')]]=$row[csf('id')];	
	}
	$poid=implode(",",$po_id);
	
	$selected_po=0;
	if(count($po_number)==1){
		$selected_po=$row[csf('id')];
	}
	$po_dropdown= create_drop_down( "cbo_order_id",210, $po_number,"", 1, "--Select--", $selected_po, "loadmatrix()","",'',"","","","" );
	echo "document.getElementById('order_drop_down_td').innerHTML = '".$po_dropdown."';\n";
	load_drop_down_po_item($poid,$cbo_fabric_natu,$cbo_fabric_source);
}

function load_drop_down_po_item($poid,$cbo_fabric_natu,$cbo_fabric_source){
	global $garments_item;
	$item_number_id=array();
	$sql=sql_select( "select distinct item_number_id from wo_po_color_size_breakdown where po_break_down_id in ($poid)");
	foreach($sql as $row){
	$item_number_id[$row[csf('item_number_id')]]=$garments_item[$row[csf('item_number_id')]];	
	}
	
	$selected_item=0;
	if(count($item_number_id)==1){
		$selected_item=$row[csf('item_number_id')];
		load_drop_down_fabric($poid,$cbo_fabric_natu,$cbo_fabric_source,$selected_item);
	}
	$item_dropdown= create_drop_down( "cbo_gmt_item_id",210, $item_number_id,"", 1, "--Select--", $selected_item, "loadrelateddata()","",'',"","","","" );
	echo "document.getElementById('gmt_item_td').innerHTML = '".$item_dropdown."';\n";
}

function load_drop_down_fabric($poid,$cbo_fabric_natu,$cbo_fabric_source,$gmts_item){
	global $body_part;
	global $color_type;
	if ($cbo_fabric_natu!=0) $cbo_fabric_natu="and a.fab_nature_id='$cbo_fabric_natu'"; 
	if ($cbo_fabric_source!=0) $cbo_fabric_source_cond="and a.fabric_source='$cbo_fabric_source'";
	if ($gmts_item!=0) $gmts_item_cond="and a.item_number_id='$gmts_item'";
	$nameArray=sql_select( "
	select
	a.id as pre_cost_fabric_cost_dtls_id,
	a.body_part_id,
	a.color_type_id,
	a.gsm_weight,
	a.construction,
	a.composition
	FROM
	wo_pre_cost_fabric_cost_dtls a,
	wo_po_break_down b
	WHERE
	a.job_no=b.job_no_mst and 
	b.id in (".$poid.")  $cbo_fabric_natu $cbo_fabric_source_cond $gmts_item_cond
	group by a.id,a.body_part_id,a.color_type_id,a.gsm_weight,a.construction,a.composition order by a.id"); 
	
	$fabric_description_array= array();
	foreach ($nameArray as $result){
		if (count($nameArray)>0 ){
			$fabric_description_array[$result[csf("pre_cost_fabric_cost_dtls_id")]]=$body_part[$result[csf("body_part_id")]].', '.$color_type[$result[csf("color_type_id")]].', '.$result[csf("construction")].', '.$result[csf("composition")].', '.$result[csf("gsm_weight")];
		}
	}
	
	$selected_fabric=0;
	if(count($fabric_description_array)==1){
		$selected_fabric=$result[csf("pre_cost_fabric_cost_dtls_id")];
	}
	
	$fabricdescription_dropdown= create_drop_down( "cbo_fabricdescription_id", 400, $fabric_description_array,"", 1, "--Select--", $selected_fabric, "loadmatrix()","","","","","","" );
	echo "document.getElementById('fabricdescription_id_td').innerHTML = '".$fabricdescription_dropdown."';\n";
}

function load_color_size($poid,$fabric_id,$gmts_item){
	$nameArray=sql_select( "select
	a.id as pre_cost_fabric_cost_dtls_id,
	a.job_no,					  
	a.color_type_id,
	a.construction,
	a.composition,
	a.gsm_weight,
	a.color_size_sensitive,
	a.color,
	a.color_break_down,
	b.color_size_table_id,
	b.color_number_id,
	b.gmts_sizes,
	b.dia_width,
	b.item_size,
	b.requirment
	FROM
	wo_pre_cost_fabric_cost_dtls a,
	wo_pre_cos_fab_co_avg_con_dtls b
	WHERE
	a.job_no=b.job_no and 
	a.id=b.pre_cost_fabric_cost_dtls_id and 
	b.po_break_down_id in (".$poid.") and a.id=$fabric_id and a.item_number_id=$gmts_item and b.requirment >0 order by a.id, b.color_size_table_id");
	$data=array();
	foreach ($nameArray as $result){
		$constrast_color_arr=array();
		if($result[csf("color_size_sensitive")]==3){
			$constrast_color=explode('__',$result[csf("color_break_down")]);
			for($i=0;$i<count($constrast_color);$i++){
				$constrast_color2=explode('_',$constrast_color[$i]);
				$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
			}
		}
		$color_id="";
		if($result[csf("color_size_sensitive")]==3){
			$color_id=return_field_value("contrast_color_id","wo_pre_cos_fab_co_color_dtls","job_no='".$result[csf('job_no')]."' and pre_cost_fabric_cost_dtls_id=".$result[csf('pre_cost_fabric_cost_dtls_id')]." and gmts_color_id=".$result[csf('color_number_id')]."");
		}
		else if($result[csf("color_size_sensitive")]==0){
			$color_id=$result[csf("color")];
		}
		else{
			$color_id=$result[csf("color_number_id")];
		}
		
		$data['color'][$result[csf('color_number_id')]]=$result[csf('color_number_id')];
		$data['fabcolor'][$result[csf('color_number_id')]]=$color_id;
		$data['gmtssizes'][$result[csf('gmts_sizes')]]=$result[csf('gmts_sizes')];
		$data['color_size'][$result[csf('color_number_id')]][$result[csf('gmts_sizes')]]=$result[csf('gmts_sizes')];
		$data['itemsize'][$result[csf('color_number_id')]][$result[csf('gmts_sizes')]]=$result[csf('item_size')];
		$data['diawidth'][$result[csf('color_number_id')]][$result[csf('gmts_sizes')]]=$result[csf('dia_width')];
		$data['cons'][$result[csf('color_number_id')]][$result[csf('gmts_sizes')]]=$result[csf('requirment')];
		$data['color_size_table_id'][$result[csf('color_number_id')]][$result[csf('gmts_sizes')]]=$result[csf('color_size_table_id')];
		$data['colortype']=$result[csf('color_type_id')];
		$data['construction']=$result[csf('construction')];
		$data['composition']=$result[csf('composition')];
		$data['gsm_weight']=$result[csf('gsm_weight')];
	}
	$sql=sql_select("select a.id, b.color_number_id,b.size_number_id,b.plan_cut_qnty from wo_po_break_down a,wo_po_color_size_breakdown b where a.id=b.po_break_down_id and a.id=$poid and item_number_id=$gmts_item");
	foreach($sql as $row){
		$data['color_size_po_qty'][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
	}
	return $data;
}

function load_booking_data($poid,$fabric_id,$gmts_item,$newdia,$txt_booking_no){
	$sql_data=sql_select("select  a.id,a.po_break_down_id,a.pre_cost_fabric_cost_dtls_id,a.dia_width,a.fin_fab_qnty,a.grey_fab_qnty,a.process_loss_percent,a.rmg_qty,a.new_cons,a.grey_cons_dzn,b.item_number_id,c.color_number_id,c.gmts_sizes from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b , wo_pre_cos_fab_co_avg_con_dtls c where  a.pre_cost_fabric_cost_dtls_id=b.id and a.pre_cost_fabric_cost_dtls_id=c.pre_cost_fabric_cost_dtls_id and a.po_break_down_id=c.po_break_down_id and c.color_size_table_id=a.color_size_table_id and a.booking_no='$txt_booking_no' and a.dia_width='$newdia' and a.pre_cost_fabric_cost_dtls_id=$fabric_id and  a.po_break_down_id=$poid");
	$data=array();
	foreach ($sql_data as $result){
		$data['rmg_qty'][$result[csf('color_number_id')]][$result[csf('gmts_sizes')]]=$result[csf('rmg_qty')];
		$data['newcons'][$result[csf('color_number_id')]][$result[csf('gmts_sizes')]]=$result[csf('new_cons')];
		$data['fin_fab_qnty'][$result[csf('color_number_id')]][$result[csf('gmts_sizes')]]=$result[csf('fin_fab_qnty')];
		$data['process_loss_percent'][$result[csf('color_number_id')]][$result[csf('gmts_sizes')]]=$result[csf('process_loss_percent')];
		$data['grey_cons_dzn'][$result[csf('color_number_id')]][$result[csf('gmts_sizes')]]=$result[csf('grey_cons_dzn')];
		$data['grey_fab_qnty'][$result[csf('color_number_id')]][$result[csf('gmts_sizes')]]=$result[csf('grey_fab_qnty')];
		$data['update_id'][$result[csf('color_number_id')]][$result[csf('gmts_sizes')]]=$result[csf('id')];
	}
	return $data;
}

if($action=="load_drop_down_fabric"){
	$data=explode("_",$data);
	$poid=$data[0];
	$cbo_fabric_natu=$data[1];
	$cbo_fabric_source=$data[2];
	$gmts_item=$data[3];
	load_drop_down_fabric($poid,$cbo_fabric_natu,$cbo_fabric_source,$gmts_item);
}

if($action=="check_color_size_qty"){
	$data=explode("_",$data);
	$poid=$data[0];
	$color_id=$data[1];
	$size_id=$data[2];
	$gmts_item=$data[3];
	
	$sql="select a.id, b.color_number_id,b.size_number_id,b.plan_cut_qnty from wo_po_break_down a,wo_po_color_size_breakdown b where a.id=b.po_break_down_id and a.id=$poid and item_number_id=$gmts_item and b.color_number_id=$color_id and b.size_number_id=$size_id and b.status_active=1 and b.is_deleted=0";
	$results=sql_select($sql);
	$plan_cut_qnty=0;
	foreach($results as $row){
		$plan_cut_qnty+=$row[csf('plan_cut_qnty')];
		//$size_number_id==$row[csf('size_number_id')];
	}
	echo $plan_cut_qnty.'_';
}

if($action=="load_color_size"){
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
    $size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name");
	$data=explode("_",$data);
	$poid=$data[0];
	$fabric_id=$data[1];
	$gmts_item=$data[2];
	$newdia=$data[3];
	$txt_booking_no=$data[4];
	$cbo_company_name=$data[5];
	$cbo_fabric_natu=$data[6];
	$colorsize=$action($poid,$fabric_id,$gmts_item);
	$booking_data=load_booking_data($poid,$fabric_id,$gmts_item,$newdia,$txt_booking_no);
	$process_loss_method=return_field_value("process_loss_method", "variable_order_tracking", "company_name=$cbo_company_name  and variable_list=18 and item_category_id=$cbo_fabric_natu and status_active=1 and is_deleted=0");
	$num_col=count($colorsize['gmtssizes']);
	$td_with=60;
	$table_width=($num_col*$td_with)+400;
	?>
    <input type="hidden" id="colortype" value="<? echo $colorsize['colortype'] ?>"/>
    <input type="hidden" id="construction" value="<? echo $colorsize['construction'] ?>"/>
    <input type="hidden" id="composition" value="<? echo $colorsize['composition'] ?>"/>
    <input type="hidden" id="gsm_weight" value="<? echo $colorsize['gsm_weight'] ?>"/>
    <input type="hidden" id="process_loss_method_id" value="<? echo $process_loss_method ?>"/>
    <table class="rpt_table" border="0" cellpadding="0" cellspacing="0" style="width:<? echo $table_width; ?>px;text-align:center" rules="all" id="table_1">
        <thead>
            <tr>
            <th width="100">
             Gmts Color
             </th>
            <th width="100">
             Fabric Color
             </th>
            <th width="100">
             Particulars
             </th>
            
            <?
			$col=1;
            foreach($colorsize['gmtssizes'] as $sizeid=>$sizevalue){
            ?>
            <th width="60" align="center">
			<? echo $size_library[$sizeid]; ?>
            <input type="hidden" id="gmtssize_<? echo $col ?>"  class="text_boxes" style="width:60px" value="<? echo $sizeid; ?>" readonly/>
            </th>
            <?
			$col++;
            }
            ?>
             <th width="100">
             Total
             </th>
            </tr>
        </thead>
        <tbody>
			<?
            $row=1;
            foreach($colorsize['color'] as $colorid=>$colorvalue){
            ?>
            
                <tr id="<? echo "tr_gmtsqty_".$row ?>">
                    <td width="100">
                    <? echo $color_library[$colorid]; ?>
                    <input type="hidden" id="gmtscolor_<? echo $row; ?>"   class="text_boxes" style="width:60px" value="<? echo $colorid; ?>" />
                    </td>
                    <td width="100"> 
                    <? echo $color_library[$colorsize['fabcolor'][$colorid]]; ?>
                    <input type="hidden" id="fabcolor_<? echo $row; ?>"  class="text_boxes" style="width:60px" value="<? echo $colorsize['fabcolor'][$colorid]; ?>" />
                    </td>
                    <td width="100">
                    Wo Gmts Qty/Pcs
                    </td>
                    
                    <?
					$total_gmts_qty=0;
					$col=1;
                    foreach($colorsize['gmtssizes'] as $sizeid=>$sizevalue){
						if($colorsize['color_size'][$colorid][$sizeid]){
							$disabled="";
						}else{
							$disabled="disabled";
						}
						$total_gmts_qty+=$booking_data['rmg_qty'][$colorid][$sizeid];
                    ?>
                    <td width="60">
                   <input type="text" id="gmtsqty_<? echo $row."_".$col?>" title="<? echo $booking_data['rmg_qty'][$colorid][$sizeid] ?>"   class="text_boxes_numeric" style="width:60px" value="<? echo $booking_data['rmg_qty'][$colorid][$sizeid] ?>" onChange="copy_data(this.id);fnc_check_po_size_qty(this.id,<? echo $poid.','.$colorid.','.$sizeid.','.$gmts_item; ?>);" <? echo $disabled ?> />
                   <input type="hidden" id="hidgmtsqty_<? echo $row."_".$col?>"   class="text_boxes_numeric" style="width:60px" value="<? echo $colorsize['color_size_po_qty'][$colorid][$sizeid] ?>" onChange="copy_data(this.id)" <? echo $disabled ?> />
                    </td>
                    <?
					$col++;
                    }
                    ?>
                    <td width="100"> 
                    <input type="text" id="totalgmtsqty_<? echo $row; ?>"  class="text_boxes_numeric" style="width:100px" value="<? echo $total_gmts_qty; ?>" />
                    </td>
                </tr>
                <tr id="<? echo "tr_newcons_".$row ?>">
                <td width="100">
                    </td>
                    <td width="100"> 
                    </td>
                    <td width="100" >
                    New Cons/Dzn
                    </td>
                    <?
					$col=1;
                    foreach($colorsize['gmtssizes'] as $sizeid=>$sizevalue){
						if($colorsize['color_size'][$colorid][$sizeid]){
							$disabled="";
						}else{
							$disabled="disabled";
						}
                    ?>
                    <td width="60">
                     <input type="text" id="newcons_<? echo $row."_".$col?>"  class="text_boxes_numeric" style="width:60px" value="<? echo $booking_data['newcons'][$colorid][$sizeid] ?>" onChange="copy_data(this.id)" <? echo $disabled ?>/>
                    </td>
                    <?
					$col++;
                    }
                    ?>
                     <td width="100"> 
                    <input type="hidden" id="totalnewcons_<? echo $row; ?>"  class="text_boxes" style="width:60px" value="<? //echo $colorsize['fabcolor'][$colorid]; ?>" />
                    </td>
                </tr>
                <tr id="<? echo "tr_totfincons_".$row ?>" style="display:none">
                    <td width="100">
                    </td>
                    <td width="100"> 
                    </td>
                    <td width="100">
                    Fin Cons for Order Qty
                    </td>
                    <?
					$col=1;
                    foreach($colorsize['gmtssizes'] as $sizeid=>$sizevalue){
						if($colorsize['color_size'][$colorid][$sizeid]){
							$disabled="";
						}else{
							$disabled="disabled";
						}
                    ?>
                    <td width="60">
                     <input type="text" id="totfincons_<? echo $row."_".$col; ?>"  class="text_boxes_numeric" style="width:60px" value="<? echo $booking_data['fin_fab_qnty'][$colorid][$sizeid] ?>" <? echo $disabled ?> readonly/>
                    </td>
                    <?
					$col++;
                    }
                    ?>
                     <td width="100"> 
                    <input type="hidden" id="totaltotfincons_<? echo $row; ?>"  class="text_boxes" style="width:100px" value="<? //echo $colorsize['fabcolor'][$colorid]; ?>" />
                    </td>
                </tr>
                <tr id="<? echo "tr_processloss_".$row ?>">
                    <td width="100">
                    </td>
                    <td width="100"> 
                    </td>
                    <td width="100" >
                    Process Loss
                    </td>
                    <?
					$col=1;
                    foreach($colorsize['gmtssizes'] as $sizeid=>$sizevalue){
						if($colorsize['color_size'][$colorid][$sizeid]){
							$disabled="";
						}else{
							$disabled="disabled";
						}
                    ?>
                    <td width="60">
                     <input type="text" id="processloss_<? echo $row."_".$col?>"  class="text_boxes_numeric" style="width:60px" value="<? echo $booking_data['process_loss_percent'][$colorid][$sizeid] ?>" onChange="copy_data(this.id)" <? echo $disabled ?>/>
                    </td>
                    <?
					$col++;
                    }
                    ?>
                     <td width="100"> 
                    <input type="hidden" id="totalprocessloss__<? echo $row; ?>"  class="text_boxes" style="width:100px" value="<? //echo $colorsize['fabcolor'][$colorid]; ?>" />
                    </td>
                </tr>
               <tr id="<? echo "tr_greyconsdzn_".$row ?>" style="display:none">
                    <td width="100">
                    </td>
                    <td width="100"> 
                    </td>
                    <td width="100" >
                    Grey Cons Dzn
                    </td>
                    <?
					$col=1;
                    foreach($colorsize['gmtssizes'] as $sizeid=>$sizevalue){
						if($colorsize['color_size'][$colorid][$sizeid]){
							$disabled="";
						}else{
							$disabled="disabled";
						}
                    ?>
                    <td width="60">
                     <input type="text" id="greyconsdzn_<? echo $row."_".$col?>"  class="text_boxes_numeric" style="width:60px" value="<? echo $booking_data['grey_cons_dzn'][$colorid][$sizeid] ?>" onChange="copy_data(this.id)" <? echo $disabled ?>/>
                    </td>
                    <?
					$col++;
                    }
                    ?>
                    <td width="100"> 
                    <input type="hidden" id="totalgreyconsdzn_<? echo $row; ?>"  class="text_boxes" style="width:100px" value="<? //echo $colorsize['fabcolor'][$colorid]; ?>" />
                    </td>
                </tr>
                <tr id="<? echo "tr_totcons_".$row ?>">
                    <td width="100">
                    </td>
                    <td width="100"> 
                    </td>
                    <td width="100">
                    Cons for Order Qty
                    </td>
                    <?
					$total_grey=0;
					$col=1;
                    foreach($colorsize['gmtssizes'] as $sizeid=>$sizevalue){
						if($colorsize['color_size'][$colorid][$sizeid]){
							$disabled="";
						}else{
							$disabled="disabled";
						}
						$total_grey+=$booking_data['grey_fab_qnty'][$colorid][$sizeid];
                    ?>
                    <td width="60">
                     <input type="text" id="totcons_<? echo $row."_".$col; ?>"  class="text_boxes_numeric" style="width:60px" value="<? echo $booking_data['grey_fab_qnty'][$colorid][$sizeid] ?>"  <? echo $disabled ?> readonly/>
                    </td>
                    <?
					$col++;
                    }
                    ?>
                     <td width="100"> 
                    <input type="text" id="totaltotcons_<? echo $row; ?>"  class="text_boxes_numeric" style="width:100px" value="<? echo number_format($total_grey,4); ?>" />
                    </td>
                </tr>
                <tr id="<? echo "tr_itemsize_".$row ?>">
                    <td width="100">
                    </td>
                    <td width="100"> 
                    </td>
                    <td width="100">
                    Item Size
                     </td>
                    <?
					$col=1;
                    foreach($colorsize['gmtssizes'] as $sizeid=>$sizevalue){
						if($colorsize['color_size'][$colorid][$sizeid]){
							$disabled="";
						}else{
							$disabled="disabled";
						}
                    ?>
                    <td width="60">
                    <? echo $colorsize['itemsize'][$colorid][$sizeid]; ?>
                     <input type="hidden" id="itemsize_<? echo $row."_".$col?>"  class="text_boxes" style="width:60px" value="  <? echo $colorsize['itemsize'][$colorid][$sizeid]; ?>" <? echo $disabled ?>/>
                    </td>
                    <?
					$col++;
                    }
                    ?>
                </tr>
                <tr id="<? echo "tr_dia_".$row ?>">
                    <td width="100">
                    </td>
                    <td width="100"> 
                    </td>
                    <td width="100" >
                    Dia
                    </td>
                    <?
					$col=1;
                    foreach($colorsize['gmtssizes'] as $sizeid=>$diavalue){
						if($colorsize['color_size'][$colorid][$sizeid]){
							$disabled="";
						}else{
							$disabled="disabled";
						}
                    ?>
                    <td width="60">
                    <? echo $colorsize['diawidth'][$colorid][$sizeid]; ?>
                    <input type="hidden" id="dia_<? echo $row."_".$col?>"  class="text_boxes" style="width:60px" value=" <? echo $colorsize['diawidth'][$colorid][$sizeid]; ?>" <? echo $disabled ?>/>
                    </td>
                    <?
					$col++;
                    }
                    ?>
                </tr>
                <tr id="<? echo "tr_cons_".$row ?>">
                    <td width="100">
                    </td>
                    <td width="100"> 
                    </td>
                    <td width="100" >
                     Cons
                    </td>
                    <?
					$col=1;
                    foreach($colorsize['gmtssizes'] as $sizeid=>$sizevalue){
						if($colorsize['color_size'][$colorid][$sizeid]){
							$disabled="";
						}else{
							$disabled="disabled";
						}
                    ?>
                    <td width="60">
                    <? echo $colorsize['cons'][$colorid][$sizeid]; ?>
                    <input type="hidden" id="cons_<? echo $row."_".$col?>"  class="text_boxes" style="width:60px" value="<? echo $colorsize['cons'][$colorid][$sizeid]; ?>" <? echo $disabled ?>/>
                    </td>
                    <?
					$col++;
                    }
                    ?>
                </tr>
                 <tr id="<? echo "tr_colorsizetableid_".$row ?>" style="display:none">
                    <td width="100">
                    </td>
                    <td width="100"> 
                    </td>
                    <td width="100" >
                    </td>
                    <?
					$col=1;
                    foreach($colorsize['gmtssizes'] as $sizeid=>$sizevalue){
						if($colorsize['color_size'][$colorid][$sizeid]){
							$disabled="";
						}else{
							$disabled="disabled";
						}
                    ?>
                    <td width="60">
                    <input type="hidden" id="colorsizetableid_<? echo $row."_".$col?>"  class="text_boxes" style="width:60px" value="<? echo $colorsize['color_size_table_id'][$colorid][$sizeid]; ?>" <? echo $disabled ?>/>
                    </td>
                    <?
					$col++;
                    }
                    ?>
                </tr>
                <tr id="<? echo "tr_updateid_".$row ?>" style="display:none">
                    <td width="100">
                    </td>
                    <td width="100"> 
                    </td>
                    <td width="100" >
                    </td>
                    <?
					$col=1;
                    foreach($colorsize['gmtssizes'] as $sizeid=>$sizevalue){
						if($colorsize['color_size'][$colorid][$sizeid]){
							$disabled="";
						}else{
							$disabled="disabled";
						}
                    ?>
                    <td width="60">
                    <input type="hidden" id="updateid_<? echo $row."_".$col?>"  class="text_boxes" style="width:60px" value="<? echo $booking_data['update_id'][$colorid][$sizeid]; ?>" <? echo $disabled ?>/>
                    </td>
                    <?
					$col++;
                    }
                    ?>
                </tr>
            <?
            $row++;
            }
            ?>
        </tbody>
    </table>
    <?
}
if($action=='show_listview'){
	$data=explode("_",$data);
	$booking_no=$data[0];
	$txt_order_no_id=$data[1];
	$cbo_fabric_natu=$data[2];
	$cbo_fabric_source=$data[3];
	$po_number=array();
	$sql=sql_select( "select id,po_number from wo_po_break_down where id in ($txt_order_no_id)");
	foreach($sql as $row){
	$po_number[$row[csf('id')]]=$row[csf('po_number')];	
	}
	if ($cbo_fabric_natu!=0) $cbo_fabric_natu="and a.fab_nature_id='$cbo_fabric_natu'"; 
	if ($cbo_fabric_source!=0) $cbo_fabric_source_cond="and a.fabric_source='$cbo_fabric_source'"; 
	$nameArray=sql_select( "
	select
	a.id as pre_cost_fabric_cost_dtls_id,
	a.body_part_id,
	a.color_type_id,
	a.gsm_weight,
	a.construction,
	a.composition
	FROM
	wo_pre_cost_fabric_cost_dtls a,
	wo_po_break_down b
	WHERE
	a.job_no=b.job_no_mst and 
	b.id in (".$txt_order_no_id.")  $cbo_fabric_natu $cbo_fabric_source_cond
	group by a.id,a.body_part_id,a.color_type_id,a.gsm_weight,a.construction,a.composition order by a.id"); 
	
	$fabric_description_array= array();
	foreach ($nameArray as $result){
		if (count($nameArray)>0 ){
			$fabric_description_array[$result[csf("pre_cost_fabric_cost_dtls_id")]]=$body_part[$result[csf("body_part_id")]].', '.$color_type[$result[csf("color_type_id")]].', '.$result[csf("construction")].', '.$result[csf("composition")].', '.$result[csf("gsm_weight")];
		}
	}
	?>
    <table  cellpadding="0" cellspacing="2" border="1" class="rpt_table" rules="all">
        <thead>
            <tr align="center" >
            <th  width="30">#</th>
            <th  width="210">PO No</th>
            <th  width="210">Gmt. Item</th>
            <th  width="500">Fabric Description</th>
            <th  width="70">Dia</th>
            </tr> 
        </thead>
        <tbody>
        <?
		$sql_data=sql_select("select min (a.id) as id, a.po_break_down_id,a.pre_cost_fabric_cost_dtls_id,a.dia_width,b.item_number_id from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b where  b.id=a.pre_cost_fabric_cost_dtls_id and a.booking_no='$booking_no' group by a.po_break_down_id,a.pre_cost_fabric_cost_dtls_id,a.dia_width,b.item_number_id");
		$i=1;
		foreach($sql_data as $row_data){
			$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF"; 
		?>
            <tr bgcolor="<? echo $bgcolor;?>" align="center" onClick="load_dtls_data(<? echo $row_data[csf('po_break_down_id')]; ?>,<? echo $row_data[csf('item_number_id')]; ?>,<? echo $row_data[csf('pre_cost_fabric_cost_dtls_id')]; ?>,'<? echo $row_data[csf('dia_width')]; ?>')" >
            <td  width="30"><? echo $i; ?></td>
            <td  width="210"><? echo $po_number[$row_data[csf('po_break_down_id')]]?></td>
            <td  width="210"><? echo $garments_item[$row_data[csf('item_number_id')]]?></td>
            <td  width="500"><? echo $fabric_description_array[$row_data[csf('pre_cost_fabric_cost_dtls_id')]]?></td>
            <td  width="70"><? echo $row_data[csf('dia_width')]?></td>
            </tr> 
            <?
			$i++;
		}
			?>
        </tbody>
   </table>
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
		if($db_type==0){
			$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'Fb', date("Y",time()), 5, "select booking_no_prefix, booking_no_prefix_num from wo_booking_mst where company_id=$cbo_company_name and booking_type=1 and YEAR(insert_date)=".date('Y',time())." order by booking_no_prefix_num desc ", "booking_no_prefix", "booking_no_prefix_num" ));
		}
		else if($db_type==2){
			$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'Fb', date("Y",time()), 5, "select booking_no_prefix, booking_no_prefix_num from wo_booking_mst where company_id=$cbo_company_name and booking_type=1 and to_char(insert_date,'YYYY')=".date('Y',time())." order by booking_no_prefix_num desc ", "booking_no_prefix", "booking_no_prefix_num" ));
		}
		$id=return_next_id( "id", "wo_booking_mst", 1 ) ;
		$field_array="id,booking_type,is_short,booking_no_prefix,booking_no_prefix_num,booking_no,company_id,buyer_id,job_no,po_break_down_id,item_category,fabric_source,currency_id,exchange_rate,pay_mode,source,booking_date,delivery_date,booking_month,booking_year,supplier_id,attention,booking_percent,colar_excess_percent,cuff_excess_percent,ready_to_approved,inserted_by,insert_date,rmg_process_breakdown,fabric_composition,entry_form"; 
	    $data_array ="(".$id.",1,3,'".$new_booking_no[1]."',".$new_booking_no[2].",'".$new_booking_no[0]."',".$cbo_company_name.",".$cbo_buyer_name.",".$txt_job_no.",".$txt_order_no_id.",".$cbo_fabric_natu.",".$cbo_fabric_source.",".$cbo_currency.",".$txt_exchange_rate.",".$cbo_pay_mode.",".$cbo_source.",".$txt_booking_date.",".$txt_delivery_date.",".$cbo_booking_month.",".$cbo_booking_year.",".$cbo_supplier_name.",".$txt_attention.",".$txt_booking_percent.",".$txt_colar_excess_percent.",".$txt_cuff_excess_percent.",".$cbo_ready_to_approved.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$processloss_breck_down.",".$txt_fabriccomposition.",119)";
		$rID=sql_insert("wo_booking_mst",$field_array,$data_array,0);
		if($db_type==0){
			if($rID){
				mysql_query("COMMIT");  
				echo "0**".$new_booking_no[0];
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		
		if($db_type==2 || $db_type==1 ){
			if($rID){
				oci_commit($con);  
				echo "0**".$new_booking_no[0];
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
		if($db_type==0){
			mysql_query("BEGIN");
		}
		$field_array="company_id*buyer_id*job_no*po_break_down_id*item_category*fabric_source*currency_id*exchange_rate*pay_mode*source*booking_date*delivery_date*booking_month*booking_year*supplier_id*attention*booking_percent*colar_excess_percent*cuff_excess_percent*ready_to_approved*updated_by*update_date*rmg_process_breakdown*fabric_composition"; 
		$data_array ="".$cbo_company_name."*".$cbo_buyer_name."*".$txt_job_no."*".$txt_order_no_id."*".$cbo_fabric_natu."*".$cbo_fabric_source."*".$cbo_currency."*".$txt_exchange_rate."*".$cbo_pay_mode."*".$cbo_source."*".$txt_booking_date."*".$txt_delivery_date."*".$cbo_booking_month."*".$cbo_booking_year."*".$cbo_supplier_name."*".$txt_attention."*".$txt_booking_percent."*".$txt_colar_excess_percent."*".$txt_cuff_excess_percent."*".$cbo_ready_to_approved."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$processloss_breck_down."*".$txt_fabriccomposition."";
		$rID=sql_update("wo_booking_mst",$field_array,$data_array,"booking_no","".$txt_booking_no."",0);
		
		if($db_type==0){
			if($rID ){
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		if($db_type==2 || $db_type==1 ){
			if($rID ){
				oci_commit($con);   
				echo "1**".str_replace("'","",$txt_booking_no);
			}
			else{
				oci_roolback($con);  
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
		
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'2'*'1'";
		$rID=sql_delete("wo_booking_mst",$field_array,$data_array,"id","".$update_id."",1);
		if($db_type==0){
			if($rID ){
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		
		if($db_type==2 || $db_type==1 ){
			if($rID ){
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

if($action=="save_update_delete_dtls"){
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if ($operation==0){
		$con = connect();
		if($db_type==0){
			mysql_query("BEGIN");
		}
		// if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}	
		$counter=0;
		$fail=0;
		$add_comma=1;
		$id=return_next_id( "id", "wo_booking_dtls", 1 ) ;
		$id_found=0;
		$sq_data=sql_select("select count (id) as id from wo_booking_dtls where booking_no=$txt_booking_no and po_break_down_id=$cbo_order_id and pre_cost_fabric_cost_dtls_id =$cbo_fabricdescription_id and dia_width=$newdia group by booking_no");
		
		foreach($sq_data as $sq_row){
			$id_found=$sq_row[csf('id')]; 
		}
		if($id_found > 0){
			 echo "11**0"; 
			  disconnect($con);die;
		}
		$field_array="id,job_no,po_break_down_id,pre_cost_fabric_cost_dtls_id,color_size_table_id,booking_no,booking_type, is_short,fabric_color_id,fin_fab_qnty,grey_fab_qnty,process_loss_percent,color_type,construction,copmposition,gsm_weight,dia_width,rmg_qty,new_cons,grey_cons_dzn,inserted_by,insert_date";
		for($i=1; $i<=$total_row; $i++){
			$gmtscolor="gmtscolor_".$i;
			$fabcolor="fabcolor_".$i;
			if(str_replace("'","",$$gmtscolor)!=""){
				for($m=1; $m<=$total_col; $m++){
					$gmtssize="gmtssize_".$m;
					if(str_replace("'","",$$gmtssize)!=""){
						$counter++;
						$gmtsqty="gmtsqty_".$i.'_'.$m;
						$newcons="newcons_".$i.'_'.$m;
						$totfincons="totfincons_".$i.'_'.$m;
						$processloss="processloss_".$i.'_'.$m;
						$greyconsdzn="greyconsdzn_".$i.'_'.$m;
						$totcons="totcons_".$i.'_'.$m;
						$itemsize="itemsize".$i.'_'.$m;
						$diaold="dia_".$i.'_'.$m;
						$cons="cons_".$i.'_'.$m;
						$colorsizetableid="colorsizetableid_".$i.'_'.$m;
						
						if(str_replace("'","",$$colorsizetableid) !=""){
							if ($add_comma!=1) $data_array .=",";
							$data_array .="(".$id.",".$txt_job_no.",".$cbo_order_id.",".$cbo_fabricdescription_id.",".$$colorsizetableid.",".$txt_booking_no.",1,3,".$$fabcolor.",".$$totfincons.",".$$totcons.",".$$processloss.",".$colortype.",".$construction.",".$composition.",".$gsm_weight.",".$newdia.",".$$gmtsqty.",".$$newcons.",".$$greyconsdzn.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
							if ($data_array!="" && $counter==100){
								$counter=0;
								$rID=sql_insert("wo_booking_dtls",$field_array,$data_array,1);  
								$data_array="";
								$add_comma=1;
								if( $rID && $fail==0) { $rID=1;  } else { $rID=0; $fail=1; }
							}
							$id=$id+1;
							$add_comma++;
						}
					}
				}
				if ($data_array!="" && $counter!=100){
					$counter=0;
					$rID=sql_insert("wo_booking_dtls",$field_array,$data_array,1);  
					$data_array="";
					$add_comma=1;
					if( $rID && $fail==0) { $rID=1;  } else { $rID=0; $fail=1; }
				}
			}
		}
		
		if($db_type==0){
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**".$new_booking_no[0];
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_booking_no[0];
			}
		}
		
		if($db_type==2 || $db_type==1 ){
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
	
	if ($operation==1){
		$con = connect();
		if($db_type==0){
			mysql_query("BEGIN");
		}
		 //if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; die;}		
		 $counter=0;
		 $fail=0;
		 $add_comma=1;
		 $id=return_next_id( "id", "wo_booking_dtls", 1 ) ;
		 if($newdia != $saveddia){
			 $id_found=0;
			 $sq_data=sql_select("select count (id) as id from wo_booking_dtls where booking_no=$txt_booking_no and po_break_down_id=$cbo_order_id and pre_cost_fabric_cost_dtls_id =$cbo_fabricdescription_id and dia_width=$newdia group by booking_no");
			 foreach($sq_data as $sq_row){
				$id_found=$sq_row[csf('id')]; 
			 }
			 if($id_found > 0){
				 echo "11**0"; 
				 disconnect($con); die;
			 }
		 }
		 $rID=execute_query( "delete  from  wo_booking_dtls  where booking_no=$txt_booking_no and po_break_down_id=$cbo_order_id and pre_cost_fabric_cost_dtls_id =$cbo_fabricdescription_id and dia_width=$saveddia",0);	
		 $field_array="id,job_no,po_break_down_id,pre_cost_fabric_cost_dtls_id,color_size_table_id,booking_no,booking_type, is_short,fabric_color_id,fin_fab_qnty,grey_fab_qnty,process_loss_percent,color_type,construction,copmposition,gsm_weight,dia_width,rmg_qty,new_cons,grey_cons_dzn,updated_by,update_date";
		 for($i=1; $i<=$total_row; $i++){
				$gmtscolor="gmtscolor_".$i;
				$fabcolor="fabcolor_".$i;
				if(str_replace("'","",$$gmtscolor)!=""){
					for($m=1; $m<=$total_col; $m++){
						$gmtssize="gmtssize_".$m;
						if(str_replace("'","",$$gmtssize)!=""){
							$counter++;
							$gmtsqty="gmtsqty_".$i.'_'.$m;
							$newcons="newcons_".$i.'_'.$m;
							$totfincons="totfincons_".$i.'_'.$m;
							$processloss="processloss_".$i.'_'.$m;
							$greyconsdzn="greyconsdzn_".$i.'_'.$m;
							$totcons="totcons_".$i.'_'.$m;
							$itemsize="itemsize".$i.'_'.$m;
							$diaold="dia_".$i.'_'.$m;
							$cons="cons_".$i.'_'.$m;
							$colorsizetableid="colorsizetableid_".$i.'_'.$m;
							if(str_replace("'","",$$colorsizetableid) !=""){
							if ($add_comma!=1) $data_array .=",";
							$data_array .="(".$id.",".$txt_job_no.",".$cbo_order_id.",".$cbo_fabricdescription_id.",".$$colorsizetableid.",".$txt_booking_no.",1,3,".$$fabcolor.",".$$totfincons.",".$$totcons.",".$$processloss.",".$colortype.",".$construction.",".$composition.",".$gsm_weight.",".$newdia.",".$$gmtsqty.",".$$newcons.",".$$greyconsdzn.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
							if ($data_array!="" && $counter==100){
								$counter=0;
								$rID=sql_insert("wo_booking_dtls",$field_array,$data_array,1);  
								$data_array="";
								$add_comma=1;
								if( $rID && $fail==0) { $rID=1;  } else { $rID=0; $fail=1; }
							}
							$id=$id+1;
							$add_comma++;
							}
						}
					}
					if ($data_array!="" && $counter!=100){
						$counter=0;
						$rID=sql_insert("wo_booking_dtls",$field_array,$data_array,1);  
						$data_array="";
						$add_comma=1;
						if( $rID && $fail==0) { $rID=1;  } else { $rID=0; $fail=1; }
					 }
				}
			}
		
		if($db_type==0){
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**".$new_booking_no[0];
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_booking_no[0];
			}
		}
		
		if($db_type==2 || $db_type==1 ){
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
	if ($operation==2){
		$con = connect();
		if($db_type==0){
			mysql_query("BEGIN");
		}
		$rID=execute_query( "delete  from  wo_booking_dtls  where booking_no=$txt_booking_no and po_break_down_id=$cbo_order_id and pre_cost_fabric_cost_dtls_id =$cbo_fabricdescription_id and dia_width=$saveddia",0);	

		if($db_type==0){
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**".str_replace(",","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace(",","",$txt_booking_no);
			}
		}
		
		if($db_type==2 || $db_type==1 ){
			if($rID ){
				oci_commit($con);   
				echo "0**".str_replace(",","",$txt_booking_no);
			}
			else{
				oci_rollback($con);  
				echo "10**".str_replace(",","",$txt_booking_no);
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="check_is_booking_used"){
	$work_order_no=return_field_value("work_order_no","com_pi_item_details","work_order_no='$data' and status_active =1 and is_deleted=0");
	echo $work_order_no;
	die;
}

if($action=="delete_booking_item"){
	$con = connect();
	if($db_type==0){
		mysql_query("BEGIN");
	}
	$rID = execute_query( "update wo_booking_dtls set status_active=0,is_deleted=1 where  booking_no ='$data'",0);	
	if($db_type==0){
		if($rID ){
			mysql_query("COMMIT");  
			echo "0**".str_replace(",","",$txt_booking_no);
		}
		else{
			mysql_query("ROLLBACK"); 
			echo "10**".str_replace(",","",$txt_booking_no);
		}
	}
	
	if($db_type==2 || $db_type==1 ){
		if($rID ){
			oci_commit($con);   
			echo "0**".str_replace(",","",$txt_booking_no);
		}
		else{
			oci_rollback($con);  
			echo "10**".str_replace(",","",$txt_booking_no);
		}
	}
}



if($action=="rmg_process_loss_popup"){
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>
<script>
function js_set_value_set(){
	var cutting_per=$('#cutting_per').val(); 
	if(cutting_per==""){
		cutting_per=0;  
	}
	
	var embbroidery_per=$('#embbroidery_per').val(); 
	if(embbroidery_per==""){
		embbroidery_per=0;  
	}
	
	var printing_per=$('#printing_per').val(); 
	if(printing_per==""){
		printing_per=0;  
	}
	
	var wash_per=$('#wash_per').val(); 
	if(wash_per==""){
		wash_per=0;  
	}
	
	var sew_per=$('#sew_per').val(); 
	if(sew_per==""){
		sew_per=0;  
	}
	
	var fin_per=$('#fin_per').val(); 
	if(fin_per==""){
		fin_per=0;  
	}
	
	var knitt_per=$('#knitt_per').val(); 
	if(knitt_per==""){
		knitt_per=0;  
	}
	
	var dying_per=$('#dying_per').val(); 
	if(dying_per==""){
		dying_per=0;  
	}
	
	var extracutt_per=$('#extracutt_per').val(); 
	if(extracutt_per==""){
		extracutt_per=0;  
	}
	
	var other_per=$('#other_per').val(); 
	if(other_per==""){
		other_per=0;  
	}
	
	var neck_sleev_printing_per=$('#neck_sleev_printing_per').val();
	if(neck_sleev_printing_per==""){
		neck_sleev_printing_per=0;  
	}
	
	var gmt_other_per=$('#gmt_other_per').val();
	if(gmt_other_per==""){
		gmt_other_per=0;  
	}
	
	var yarn_dyeing_per=$('#yarn_dyeing_per').val();
	if(yarn_dyeing_per==""){
		yarn_dyeing_per=0;  
	}
	
	var all_over_print_per=$('#all_over_print_per').val();
	if(all_over_print_per==""){
	all_over_print_per=0;  
	}
	
	var lay_wash_per=$('#lay_wash_per').val();
	if(lay_wash_per==""){
		lay_wash_per=0;  
	}
	
	var gmtfinish_per=$('#gmtfinish_per').val();
	if(gmtfinish_per==""){
		gmtfinish_per=0;  
	}
	var processloss_breck_down=cutting_per+'_'+embbroidery_per+'_'+printing_per+'_'+wash_per+'_'+sew_per+'_'+fin_per+'_'+knitt_per+'_'+dying_per+'_'+extracutt_per+'_'+other_per+'_'+neck_sleev_printing_per+'_'+gmt_other_per+'_'+yarn_dyeing_per+'_'+all_over_print_per+'_'+lay_wash_per+'_'+gmtfinish_per;
	document.getElementById('processloss_breck_down').value=processloss_breck_down;
	parent.emailwindow.hide();
}
</script>
</head>
<body>
<div align="center" style="width:100%;" >
 <? 
 echo load_freeze_divs ("../../../",$permission); 
 $data=explode("_",$processloss_breck_down);
 ?>
<fieldset>
    <form autocomplete="off">
        <input style="width:60px;" type="hidden" class="text_boxes"  name="processloss_breck_down" id="processloss_breck_down" /> 
        <table width="180" class="rpt_table" border="1" rules="all">
            <tr>
                <td width="130">
                Cut Panel rejection <!--  Extra Cutting %  breack Down 8-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="extracutt_per" id="extracutt_per" value="<? echo $data[8];  ?>"  /> 
                </td>
            </tr>
            <tr>
                <td width="130">
                Chest Printing <!-- Printing % breack Down 2-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="printing_per" id="printing_per" value="<? echo $data[2];  ?>" /> 
                </td>
            </tr>
            <tr>
                <td width="130">
                Neck/Sleeve Printing <!-- new breack Down 10-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="neck_sleev_printing_per" id="neck_sleev_printing_per" value="<? echo $data[10];  ?>" /> 
                </td>
            </tr>
            <tr>
                <td width="130">
                Embroidery  <!-- Embroidery  % breack Down 1-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="embbroidery_per" id="embbroidery_per" value="<? echo $data[1];  ?>"  /> 
                </td>
            </tr>
            <tr>
                <td width="130">
                Sewing/Input <!-- Sewing % breack Down 4-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="sew_per" id="sew_per" value="<? echo $data[4];  ?>" /> 
                </td>
            </tr>
            <tr>
                <td width="130">
                Garments Wash  <!-- Washing % breack Down 3-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="wash_per" id="wash_per"  value="<? echo $data[3];  ?>" /> 
                </td>
            </tr>
            <tr>
                <td width="130">
                Gmts Finishing  <!-- Washing % breack Down 3-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="gmtfinish_per" id="gmtfinish_per"  value="<? echo $data[15];  ?>" /> 
                </td>
            </tr>
            <tr>
                <td width="130">
                Others  <!-- New breack Down 11-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="gmt_other_per" id="gmt_other_per" value="<? echo $data[11];  ?>"  /> 
                </td>
            </tr>
            <tr>
                <td width="130">
                Knitting   <!-- Knitting % breack Down 6-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="knitt_per" id="knitt_per" value="<? echo $data[6];  ?>"  /> 
                </td>
            </tr>
            <tr>
                <td width="130">
                Yarn Dyeing   <!-- New breack Down 12-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="yarn_dyeing_per" id="yarn_dyeing_per" value="<? echo $data[12];  ?>"  /> 
                </td>
            </tr>
            <tr>
                <td width="130">
                Dyeing & Finishing   <!-- Finishing % breack Down 5-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="fin_per" id="fin_per" value="<? echo $data[5];  ?>"  /> 
                </td>
            </tr>
            <tr>
                <td width="130">
                All Over Print  <!-- New breack Down 13-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="all_over_print_per" id="all_over_print_per" value="<? echo $data[13];  ?>"  /> 
                </td>
            </tr>
            <tr>
                <td width="130">
                Lay Wash (Fabric)  <!-- New breack Down 14-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="lay_wash_per" id="lay_wash_per" value="<? echo $data[14];  ?>"  /> 
                </td>
            </tr>
            <tr>
                <td width="130">
                Dying  <!--breack Down 7-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="dying_per" id="dying_per" value="<? echo $data[7];  ?>"  /> 
                </td>
            </tr>
            <tr>
                <td width="130">
                Cutting (Febric) <!-- Cutting % breack Down 0-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="cutting_per" id="cutting_per" value="<? echo $data[0];  ?>" /> 
                </td>
            </tr>
            <tr>
                <td width="130">
                Others <!--breack Down 9-->
                </td>
                <td>
                <input style="width:60px;" type="text" class="text_boxes_numeric"  name="other_per" id="other_per" value="<? echo $data[9];  ?>"  /> 
                </td>
            </tr>
            <tr>
                <td align="center"  class="button_container" colspan="2">
                <input type="button" class="formbutton" value="Close" onClick="js_set_value_set()"/> 
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
if($action=="terms_condition_popup"){
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>
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

function fn_deletebreak_down_tr(rowNo){   
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
		freeze_window(operation);
		http.open("POST","fabric_booking_controller_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_booking_terms_condition_reponse;
}

function fnc_fabric_booking_terms_condition_reponse(){
	if(http.readyState == 4){
	    var reponse=trim(http.responseText).split('**');
		if (reponse[0].length>2) reponse[0]=10;
		release_freezing();
		if(reponse[0]==0 || reponse[0]==1){
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
            <input type="hidden" id="txt_booking_no" name="txt_booking_no" value="<? echo str_replace("'","",$txt_booking_no) ?>"/>
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
                    else{
						$data_array=sql_select("select id, terms from  lib_terms_condition where is_default=1");// quotation_id='$data'
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
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="save_update_delete_fabric_booking_terms_condition"){
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if ($operation==0){
		$con = connect();
		if($db_type==0){
			mysql_query("BEGIN");
		}
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}		
		$id=return_next_id( "id", "wo_booking_terms_condition", 1 ) ;
		$field_array="id,booking_no,terms";
		for ($i=1;$i<=$total_row;$i++){
			$termscondition="termscondition_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$txt_booking_no.",".$$termscondition.")";
			$id=$id+1;
		}
		$rID_de3=execute_query( "delete from wo_booking_terms_condition where  booking_no =".$txt_booking_no."",0);
		$rID=sql_insert("wo_booking_terms_condition",$field_array,$data_array,1);
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0){
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**".$new_booking_no[0];
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_booking_no[0];
			}
		}
		
		if($db_type==2 || $db_type==1 ){
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

if ($action=="unapp_request_popup"){
	$menu_id=$_SESSION['menu_id'];
	$user_id=$_SESSION['logic_erp']['user_id'];
	echo load_html_head_contents("Un Approval Request","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data_all=explode('_',$data);
	$booking_no=$data_all[0];
	$unapp_request=$data_all[1];
	$wo_id=return_field_value("id", "wo_booking_mst", "booking_no='$booking_no' and status_active=1 and is_deleted=0");
	if($unapp_request==""){
		$sql_request="select MAX(id) as id from fabric_booking_approval_cause where page_id='$menu_id' and entry_form=7 and user_id='$user_id' and booking_id='$wo_id' and approval_type=2 and status_active=1 and is_deleted=0";
		$nameArray_request=sql_select($sql_request);
		foreach($nameArray_request as $row){
			$unapp_request=return_field_value("approval_cause", "fabric_booking_approval_cause", "id='".$row[csf('id')]."' and status_active=1 and is_deleted=0");
		}
	}
	?>
    <script>
		$( document ).ready(function() {
			document.getElementById("unappv_request").value='<? echo $unapp_request; ?>';
		});
		var permission='<? echo $permission; ?>';
		function fnc_appv_entry(operation){
			var unappv_request = $('#unappv_request').val();
			if (form_validation('unappv_request','Un Approval Request')==false){
				if (unappv_request==''){
					alert("Please write request.");
				}
				return;
			}
			else{
				
				var data="action=save_update_delete_unappv_request&operation="+operation+get_submitted_data_string('unappv_request*wo_id*page_id*user_id',"../../../");
				freeze_window(operation);
				http.open("POST","fabric_booking_controller_v2.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange=fnc_appv_entry_Reply_info;
			}
		}
		
		function fnc_appv_entry_Reply_info(){
			if(http.readyState == 4){
				var reponse=trim(http.responseText).split('**');	
				show_msg(reponse[0]);
				set_button_status(1, permission, 'fnc_appv_entry',1);
				release_freezing();
			}
		}
		
		function fnc_close(){	
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
                            if($id_up!=''){
                                echo load_submit_buttons($permission, "fnc_appv_entry", 1,0,"reset_form('size_1','','','','','');",1);
                            }
                            else{
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
	if ($operation==0){
		$con = connect();
		if($db_type==0){
			mysql_query("BEGIN");
		}
		$approved_no=return_field_value("MAX(approved_no)","approval_history","entry_form=7 and mst_id=$wo_id");
		$unapproved_request=return_field_value("id","fabric_booking_approval_cause","page_id=$page_id and entry_form=7 and user_id=$user_id and booking_id=$wo_id and approval_type=2 and approval_no=$approved_no");
		if($unapproved_request==""){
			$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;
			$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
			$data_array="(".$id_mst.",".$page_id.",7,".$user_id.",".$wo_id." ,2,".$approved_no.",".$unappv_request.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,0);
			if($db_type==0){
				if($rID ){
					mysql_query("COMMIT");
					echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$unappv_request)."**".str_replace("'","",$user_id);
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID;
				}
			}
			if($db_type==2){
				if($rID ){
					oci_commit($con);  
					echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$unappv_request)."**".str_replace("'","",$user_id);
				}
				else{
					oci_rollback($con);   
					echo "10**".$rID;
				}
			}
			if($db_type==1 ){
				echo "0**".$rID."**".$wo_id;
			}
			disconnect($con);
			die;	
		}
		else{
			$con = connect();
			if($db_type==0){
				mysql_query("BEGIN");
			}
			$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
			$data_array="".$page_id."*7*".$user_id."*".$wo_id."*2*".$approved_no."*".$unappv_request."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";
			$rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id","".$unapproved_request."",0);
			if($db_type==0){
				if($rID ){
					mysql_query("COMMIT"); 
					echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$unappv_request)."**".str_replace("'","",$user_id); 
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID;
				}
			}
			
			if($db_type==2){
				if($rID ){
					oci_commit($con);
					echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$unappv_request)."**".str_replace("'","",$user_id); 
				}
				else{
					oci_rollback($con);
					echo "10**".$rID;
				}
			}
			if($db_type==1 ){
				echo "1**".$rID."**".str_replace("'","",$wo_id);
			}
			disconnect($con);
			die;
		}
	}
	if ($operation==1){	
	}
}

if($action=="show_fabric_booking_report"){
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$location_name_arr=return_library_array( "select id,location_name from lib_location",'id','location_name');
	$pro_sub_dept_array=return_library_array( "select id,sub_department_name from lib_pro_sub_deparatment",'id','sub_department_name');
    $color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
    $size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name");

	$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	$po_qnty_tot1=return_field_value( "sum(po_quantity)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	?>
	<div style="width:1330px" align="center">       
    <?php
		$nameArray_approved=sql_select( "select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7"); 
		list($nameArray_approved_row)=$nameArray_approved;
		$nameArray_approved_date=sql_select( "select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."'");
		list($nameArray_approved_date_row)=$nameArray_approved_date;
		$nameArray_approved_comments=sql_select( "select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."'");
		list($nameArray_approved_comments_row)=$nameArray_approved_comments;
    ?>										<!--    Header Company Information         --> 
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black" >
           <tr>
               <td width="100"> 
               <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="1250">                                     
                    <table width="100%" cellpadding="0" cellspacing="0"  >
                        <tr>
                            <td align="center" style="font-size:20px;">
                              <?php      
                                    echo $company_library[$cbo_company_name];
                              ?>
                            </td>
                            <td rowspan="3" width="250">
                            
                               <span style="font-size:18px"><b> Job No:&nbsp;&nbsp;<? echo trim($txt_job_no,"'"); ?></b></span><br/>
                                <?
								 if($nameArray_approved_row[csf('approved_no')]>1){
								 ?>
								 <b> Revised No: <? echo $nameArray_approved_row[csf('approved_no')]-1; ?></b>
                                  <br/>
								  Approved Date: <? echo $nameArray_approved_date_row[csf('approved_date')]; ?>
								  <?
								 }
							  	?>
                                
                            
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">  
                            <?
                            $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name"); 
							if($txt_job_no!=""){
							 $location=return_field_value( "location_name", "wo_po_details_master","job_no='$txt_job_no'");
							}
							else{
							$location="";	
							}
							foreach ($nameArray as $result){
							echo  $location_name_arr[$location]; 
                            ?>
                                Email Address: <? echo $result[csf('email')];?> 
                                Website No: <? echo $result[csf('website')]; ?>
                                <?
                            }
                            ?>   
                               </td> 
                            </tr>
                            <tr>
                            <td align="center" style="font-size:20px">  
                                <strong><? if($report_title !=""){ echo $report_title;} else { echo "General Work Order";}?> &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:#F00"><? if(str_replace("'","",$id_approved_id) ==1){ echo "(Approved)";}else{echo "";}; ?> </font></strong>
                             </td> 
                            </tr>
                      </table>
                </td>       
            </tr>
       </table>
       
       
       
       
                <?
				$job_no='';
				$total_set_qnty=0;
				$colar_excess_percent=0;
				$cuff_excess_percent=0;
				$rmg_process_breakdown=0;
                $nameArray=sql_select( "select a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.po_break_down_id,a.colar_excess_percent,a.cuff_excess_percent,a.delivery_date,a.is_apply_last_update,a.fabric_source,a.rmg_process_breakdown,a.fabric_composition,a.insert_date,a.update_date,b.job_no,b.buyer_name, b.style_ref_no ,b.gmts_item_id,b.order_uom,b.total_set_qnty,b.style_description,b.season,b.product_dept,b.product_code,b.pro_sub_dep,b.dealing_marchant from wo_booking_mst a, wo_po_details_master b where  a.job_no=b.job_no and a.booking_no=$txt_booking_no"); 
				foreach ($nameArray as $result)
				{
					$total_set_qnty=$result[csf('total_set_qnty')];
					$colar_excess_percent=$result[csf('colar_excess_percent')];
				    $cuff_excess_percent=$result[csf('cuff_excess_percent')];
					$rmg_process_breakdown=$result[csf('rmg_process_breakdown')];
					$job_no= $result[csf('job_no')];
					$style_ref_no=$result[csf('style_ref_no')];
					$po_no="";
					$shipment_date="";
					$sql_po= "select po_number,MIN(pub_shipment_date) pub_shipment_date from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].") group by po_number"; 
					$data_array_po=sql_select($sql_po);
					foreach ($data_array_po as $row_po)
					{
						$po_no.=$row_po[csf('po_number')].", ";
						$shipment_date.=change_date_format($row_po[csf('pub_shipment_date')],'dd-mm-yyyy','-').", ";
					}
					$lead_time="";
					if($db_type==0)
					{
					$sql_lead_time= "select DATEDIFF(pub_shipment_date,po_received_date) date_diff from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")"; 

					}
					if($db_type==2)
					{
					$sql_lead_time= "select (pub_shipment_date-po_received_date) date_diff from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")"; 
					}
					$data_array_lead_time=sql_select($sql_lead_time);
					foreach ($data_array_lead_time as $row_lead_time)
					{
						$lead_time.=$row_lead_time[csf('date_diff')].",";
					}
					$po_received_date="";
					$sql_po_received_date= "select MIN(po_received_date) as po_received_date from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")"; 
					$data_array_po_received_date=sql_select($sql_po_received_date);
					foreach ($data_array_po_received_date as $row_po_received_date)
					{
						$po_received_date=change_date_format($row_po_received_date[csf('po_received_date')],'dd-mm-yyyy','-');
					}
				
				
				
				
				
				
					$sql_po= "select po_number,MIN(pub_shipment_date) pub_shipment_date, MIN(insert_date) as insert_date,shiping_status from wo_po_break_down  where id in(".$result[csf('po_break_down_id')].") group by po_number,shiping_status"; 
					$data_array_po=sql_select($sql_po);
					foreach ($data_array_po as $rows)
					{
						$daysInHand.=(datediff('d',date('d-m-Y',time()),$rows[csf('pub_shipment_date')])-1).",";
						$booking_date=$result[csf('update_date')];
						if($booking_date=="" || $booking_date=="0000-00-00 00:00:00")
						{
							$booking_date=$result[csf('insert_date')];
						}
						$WOPreparedAfter.=(datediff('d',$rows[csf('insert_date')],$booking_date)-1).",";
					
						if($rows[csf('shiping_status')]==1)
						{
						$shiping_status.= "FP".",";
						}
						else if($rows[csf('shiping_status')]==2)
						{
						$shiping_status.= "PS".",";
						}
						else if($rows[csf('shiping_status')]==3)
						{
						$shiping_status.= "FS".",";
						}
					
					}
					
				if($db_type==2) $group_concat_all=" listagg(cast(b.grouping as varchar2(4000)),',') within group (order by b.grouping) as grouping, 
	                                    listagg(cast(b.file_no as varchar2(4000)),',') within group (order by b.file_no) as file_no  ";
				else { $group_concat_all="group_concat(b.grouping) as grouping, group_concat(b.file_no) as file_no";}
				$data_array3=sql_select("select a.job_no,a.company_name,a.buyer_name,$group_concat_all from wo_po_details_master a, wo_po_break_down b where b.id in (".$result[csf('po_break_down_id')].") and a.job_no=b.job_no_mst group by a.job_no,a.company_name,a.buyer_name");
				
				?>
       <table width="100%" style="border:1px solid black" >                    	
            <tr>
                <td colspan="6" valign="top" style="font-size:18px; color:#F00"><? if($result[csf('is_apply_last_update')]==2){echo "Booking Info not synchronized with order entry and pre-costing. order entry or pre-costing has updated after booking entry.  Contact to ".$marchentrArr[$result[csf('dealing_marchant')]]; } else{ echo "";} ?></td>                             
            </tr>                                                
            <tr>
                <td width="100"><span style="font-size:18px"><b>Buyer/Agent Name</b></span></td>
                <td width="110">:&nbsp;<span style="font-size:18px"><b><? echo $buyer_name_arr[$result[csf('buyer_name')]]; ?></b></span></td>
                <td width="100" style="font-size:18px"><b>Style Ref.</b>   </td>
                <td width="110" style="font-size:18px">:&nbsp;<b><? echo $style_sting=$style_ref_no; ?> </b> </td>	
                <td width="100"><span style="font-size:12px"><b>Order Qnty</b></span></td>
                <td width="110">:&nbsp;
				<?  echo $po_qnty_tot1." ".$unit_of_measurement[$result[csf('order_uom')]] ; ?>
                </td>
            </tr>
            <tr>
                
                <td width="100" style="font-size:12px"><b>Garments Item</b></td>
                <td width="110">:&nbsp;
				<? 
				$gmts_item_name="";
				$gmts_item=explode(',',$result[csf('gmts_item_id')]);
				for($g=0;$g<=count($gmts_item); $g++)
				{
					$gmts_item_name.= $garments_item[$gmts_item[$g]].",";
				}
				echo rtrim($gmts_item_name,',');
				?>
                </td>
                <td width="100" style="font-size:12px"><b>Booking Release Date</b></td>
                <td width="110">:&nbsp;
				<? 
				$booking_date=$result[csf('update_date')];
				if($booking_date=="" || $booking_date=="0000-00-00 00:00:00")
				{
					$booking_date=$result[csf('insert_date')];
				}
				echo change_date_format($booking_date,'dd-mm-yyyy','-','');
				?>&nbsp;&nbsp;&nbsp;</td>
                <td width="100" style="font-size:12px"><b>Dealing Merchant</b></td>
                <td width="110">:&nbsp;<? echo $marchentrArr[$result[csf('dealing_marchant')]]; ?></td>
            </tr>
             <tr>
                <td width="100"><span style="font-size:12px"><b>Dept.</b></span></td>
                <td width="110" colspan="3">:&nbsp;<? echo $product_dept[$result[csf('product_dept')]] ; if($result[csf('product_code')] !=""){ echo " (".$result[csf('product_code')].")";} if($result[csf('pro_sub_dep')] !=0){ echo " (".$pro_sub_dept_array[$result[csf('pro_sub_dep')]].")";}?></td>
                
                <td width="100" style="font-size:12px"><b>Delivery Date</b></td>
               	<td width="110">:&nbsp;<? echo change_date_format( $result[csf('delivery_date')],'dd-mm-yyyy','-');?></td> 
            </tr>
            <tr>
               <td width="100" style="font-size:18px"><b>Order No</b></td>
               <td width="110" style="font-size:18px" colspan="3">:&nbsp;<b><? echo rtrim($po_no,", "); ?></b></td>
               <td width="100" style="font-size:18px"><b>Job No</b></td>
               <td width="110" style="font-size:18px">:&nbsp;<b><? echo $job_no; ?></b></td>
            </tr> 
            <tr>
               <td width="100" style="font-size:18px"><b>Internal Ref No</b></td>
               <td width="110" style="font-size:18px"> :&nbsp;<b><? echo implode(",",array_unique(explode(",",$data_array3[0][csf("grouping")]))); ?></b></td>
               <td width="100" style="font-size:18px"><b>File no</b></td>
               <td width="110" style="font-size:18px"> :&nbsp;<b><? echo  implode(",",array_unique(explode(",",$data_array3[0][csf("file_no")])));?></b></td>
               <td width="100" style="font-size:18px"><b>Booking No </b>   </td>
               <td width="110" style="font-size:18px">:&nbsp;<b><? echo $result[csf('booking_no')];?></b><? echo "(".$fabric_source[$result[csf('fabric_source')]].")"?></td>
            </tr>
            <tr>
               <td width="100" style="font-size:18px"><b>Fab. Composition</b></td>
               <td width="110" colspan="5"> :&nbsp;<b><? echo $result[csf('fabric_composition')] ?></b></td>
               
            </tr>
        </table>  
           <?
			}
	       ?>
      <br/> 
      <!--  Here will be the main portion  -->
     <?
	$costing_per="";
	$costing_per_qnty=0;
	$costing_per_id=return_field_value( "costing_per", "wo_pre_cost_mst","job_no ='$job_no'");
	if($costing_per_id==1){
		$costing_per="1 Dzn";
		$costing_per_qnty=12;
	}
	if($costing_per_id==2){
		$costing_per="1 Pcs";
		$costing_per_qnty=1;
	}
	if($costing_per_id==3){
		$costing_per="2 Dzn";
		$costing_per_qnty=24;
	}
	if($costing_per_id==4){
		$costing_per="3 Dzn";
		$costing_per_qnty=36;
	}
	if($costing_per_id==5){
		$costing_per="4 Dzn";
		$costing_per_qnty=48;
	}
	
	$process_loss_method=return_field_value( "process_loss_method", "wo_pre_cost_fabric_cost_dtls","job_no='$job_no'");;
	$sql= sql_select("select a.id as fabric_cost_dtls_id, a.body_part_id,a.construction, a.composition,a.gsm_weight,a.width_dia_type as width_dia_type, b.gmts_sizes, d.fabric_color_id, d.fin_fab_qnty, d.grey_fab_qnty, d.dia_width, d.process_loss_percent, d.rmg_qty, d.gmt_item, d.new_cons,d.grey_cons_dzn FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d   
	WHERE a.job_no=b.job_no and
	a.id=b.pre_cost_fabric_cost_dtls_id and
	c.job_no_mst=a.job_no and 
	c.id=b.color_size_table_id and
	b.po_break_down_id=d.po_break_down_id and 
	b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
	d.booking_no =$txt_booking_no and 
	d.status_active=1 and 
	d.is_deleted=0 order by d.id
	");
	$data_array=array();
	foreach($sql as $row){
		$data_array[$row[csf('fabric_color_id')]][$row[csf('body_part_id')]][$row[csf('construction')]][$row[csf('composition')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['rmg_qty']+=$row[csf('rmg_qty')];
		$data_array[$row[csf('fabric_color_id')]][$row[csf('body_part_id')]][$row[csf('construction')]][$row[csf('composition')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['fin_fab_qnty']+=$row[csf('fin_fab_qnty')];
		$data_array[$row[csf('fabric_color_id')]][$row[csf('body_part_id')]][$row[csf('construction')]][$row[csf('composition')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['grey_fab_qnty']+=$row[csf('grey_fab_qnty')];
		$data_array[$row[csf('fabric_color_id')]][$row[csf('body_part_id')]][$row[csf('construction')]][$row[csf('composition')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['process_loss_percent']=$row[csf('process_loss_percent')];
		$data_array[$row[csf('fabric_color_id')]][$row[csf('body_part_id')]][$row[csf('construction')]][$row[csf('composition')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['new_cons']=$row[csf('new_cons')];
		if($row[csf('rmg_qty')]>0)
		{
		$data_array[$row[csf('fabric_color_id')]][$row[csf('body_part_id')]][$row[csf('construction')]][$row[csf('composition')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['gmts_sizes'].=$size_library[$row[csf('gmts_sizes')]].",";
		}
		
	}
	?>
    <table border="1" class="rpt_table" rules="all" width="100%">
    <thead>
    <th>#</th>
    <th>Style</th>
    <th>Fabric Color</th>
    <th>Body Part</th>
    <th>Fabric Description</th>
    <th>Gmts Size</th>
    <th>GSM</th>
    <th>F. Dia</th>
    <th>Rmg Qty.</th>
    <th>Fin. Cons. Per Dzn.</th>
    <th>Fini. Fab. Qty</th>
    <th>Process Loss %</th>
    <th>Grey Fab. Qty</th>
    </thead>
   
    <?
	$i=0;
	foreach($data_array as $fabricColorId=>$fabricColorValue){
		$j=0;
		foreach($fabricColorValue as $bodypartId=>$bodypartValue){
			$k=0;
			foreach($bodypartValue as $constructionId=>$constructionValue){
				$l=0;
				foreach($constructionValue as $compositionId=>$compositionValue){
					$m=0;
					foreach($compositionValue as $gsm_weightId=>$gsm_weightValue){
						$n=0;
						foreach($gsm_weightValue as $dia_widthId=>$dia_widthValue){
							$i++;
							$j++;
							$k++;
							$l++;
							$m++;
							$n++;
						}
					}
				}
			}
		}
	}
	//echo $i."_".$j."_".$k."_".$l."_".$m."_".$n;
	
	$o=1;
	$TotalRmgQty=0;
	$TotalFinFabQnty=0;
	$TotalGreyFabQnty=0;
	foreach($data_array as $fabricColorId=>$fabricColorValue){
		foreach($fabricColorValue as $bodypartId=>$bodypartValue){
			foreach($bodypartValue as $constructionId=>$constructionValue){
				foreach($constructionValue as $compositionId=>$compositionValue){
					foreach($compositionValue as $gsm_weightId=>$gsm_weightValue){
						foreach($gsm_weightValue as $dia_widthId=>$dia_widthValue){
							?>
							<tbody>
                                <tr>
                                    <td><? echo $o; ?></td>
                                    <td><? echo $style_ref_no;?></td>
                                    <td><? echo $color_library[$fabricColorId]; ?></td>
                                    <td><? echo $body_part[$bodypartId]; ?></td>
                                    <td><? echo $constructionId." ".$compositionId ?></td>
                                    <td>
                                    <?
                                    $GmtsSizes=$data_array[$fabricColorId][$bodypartId][$constructionId][$compositionId][$gsm_weightId][$dia_widthId]['gmts_sizes'];
                                    echo  rtrim($GmtsSizes,",");
                                    ?>
                                    </td>
                                    <td><? echo $gsm_weightId;?></td>
                                    <td><? echo $dia_widthId;?></td>
                                    <td align="right">
                                    <?
									 $RmgQty=$data_array[$fabricColorId][$bodypartId][$constructionId][$compositionId][$gsm_weightId][$dia_widthId]['rmg_qty'];

                                    echo  $RmgQty;
                                    $TotalRmgQty+=$RmgQty;
                                    ?>
                                    </td>
                                    <td align="right">
                                    <?
                                    $NewCons=def_number_format($data_array[$fabricColorId][$bodypartId][$constructionId][$compositionId][$gsm_weightId][$dia_widthId]['new_cons'],5,"");
                                    echo  $NewCons;
                                    ?>
                                    </td>
                                    <td align="right">
                                    <?
                                    $FinFabQnty=def_number_format($data_array[$fabricColorId][$bodypartId][$constructionId][$compositionId][$gsm_weightId][$dia_widthId]['fin_fab_qnty'],5,"");
                                    $TotalFinFabQnty+=$FinFabQnty;
                                    echo  $FinFabQnty;
                                    ?>
                                    </td>
                                    <td align="right">
                                    <?
                                    $ProcessLossPercent=def_number_format($data_array[$fabricColorId][$bodypartId][$constructionId][$compositionId][$gsm_weightId][$dia_widthId]['process_loss_percent'],5,"");
                                    echo  $ProcessLossPercent;
                                    ?>
                                    </td>
                                    <td align="right">
                                    <?
                                    $GreyFabQnty=def_number_format($data_array[$fabricColorId][$bodypartId][$constructionId][$compositionId][$gsm_weightId][$dia_widthId]['grey_fab_qnty'],5,"");
                                    $TotalGreyFabQnty+=$GreyFabQnty;
                                    echo  $GreyFabQnty;
                                    ?>
                                    </td>
                                </tr>
							</tbody>  
							<?
							$o++;
						}
					}
				}
			}
		}
	}
	?>
    <tfoot>
    <th colspan="8" align="right">Total</th>
    <th align="right"><? echo $TotalRmgQty; ?></th>
    <th></th>
    <th align="right"><? echo def_number_format($TotalFinFabQnty,5,""); ?></th>
    <th></th>
    <th align="right"><? echo def_number_format($TotalGreyFabQnty,5,""); ?></th>
    </tfoot>
    </table>
    <br/>
    
<?
if($cbo_fabric_source==1){
?>
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
        <tr>
        <?
		$nameArray_size=sql_select( "select  size_number_id,min(id) as id,	min(size_order) as size_order from wo_po_color_size_breakdown where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  	is_deleted=0 and status_active=1 group by size_number_id order by size_order");
		$size_tatal=array();

		$colar_percent_size_wise_array=return_library_array( "select a.colar_cuff_per,b.gmts_sizes from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b,wo_pre_cost_fabric_cost_dtls c  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.pre_cost_fabric_cost_dtls_id=c.id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id    and a.booking_no=$txt_booking_no and a.booking_type=1 and c.body_part_id in(2) and a.status_active=1 and a.is_deleted=0", "gmts_sizes", "colar_cuff_per");
		$nameArray_item_size=sql_select( "select min(c.id) as id,b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no  and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id  and d.status_active=1 and d.is_deleted=0 group by b.item_size,c.size_number_id order by id");
		if(count($nameArray_item_size)>0){
        ?>
        <td width="49%">
            <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                <tr>
                    <td colspan="<? echo count($nameArray_size)+4;?>" align="center"><b>Collar -  Colour Size Brakedown in Pcs</b></td>
                </tr>
                <tr>
                    <td width="70">Size</td>
                    <?  
                    foreach($nameArray_item_size  as $result_size){	     
                    ?>
                    <td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
                    <?	
                    }    
                    ?>	
                    <td rowspan="2" align="center"><strong>Total</strong></td> 
                    <td width="60" rowspan="2" align="center"><strong>Extra %</strong></td>
                </tr>
                <tr>
                <td>Collar Size</td>
                <?
                foreach($nameArray_item_size  as $result_item_size){	     
					?>
					<td align="center" style="border:1px solid black"><strong><? echo $result_item_size[csf('item_size')];?></strong></td>
					<?	
                }    
                ?>	
                <?
                $color_wise_wo_sql=sql_select("select a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method,c.color_number_id,sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by c.color_number_id,a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method order by a.id 
                ");
                foreach($color_wise_wo_sql as $color_wise_wo_result){
					$color_total_collar=0;
					$color_total_collar_order_qnty=0;
					$process_loss_method=$color_wise_wo_result[csf("process_loss_method")];
					$constrast_color_arr=array();
					if($color_wise_wo_result[csf("color_size_sensitive")]==3){
						$constrast_color=explode('__',$color_wise_wo_result[csf("color_break_down")]);
						for($i=0;$i<count($constrast_color);$i++){
							$constrast_color2=explode('_',$constrast_color[$i]);
							$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
						}
					}
					?> 
					<tr>
					<td>
					<?
					if($color_wise_wo_result[csf("color_size_sensitive")]==3){
						echo strtoupper ($constrast_color_arr[$color_wise_wo_result[csf('color_number_id')]]) ;
						$lab_dip_color_id=return_field_value("contrast_color_id","wo_pre_cos_fab_co_color_dtls","job_no='".$color_wise_wo_result[csf('job_no')]."' and pre_cost_fabric_cost_dtls_id=".$color_wise_wo_result[csf('id')]." and gmts_color_id=".$color_wise_wo_result[csf('color_number_id')]."");
					}
					else{
						echo $color_library[$color_wise_wo_result[csf('color_number_id')]];
						$lab_dip_color_id=$color_wise_wo_result[csf('color_number_id')];
					}
					?>
					
					</td>
					<?
					foreach($nameArray_item_size  as $result_size){
						?>
						<td align="center" style="border:1px solid black">
						<? 
						$color_wise_wo_sql_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$color_wise_wo_result[csf('color_number_id')]."   and  status_active=1 and is_deleted =0");                          
						list($plan_cut_qnty)=$color_wise_wo_sql_qnty;
						$plan_cut=$plan_cut_qnty[csf('plan_cut_qnty')];
						$colar_excess_per=($plan_cut*$colar_excess_percent)/100;
						echo number_format($plan_cut+$colar_excess_per,0); 
						$color_total_collar+=$plan_cut+$colar_excess_per; 
						$color_total_collar_order_qnty+=$plan_cut; 
						$grand_total_collar+=$plan_cut+$colar_excess_per; 
						$grand_total_collar_order_qnty+=$plan_cut; 
						$size_tatal[$result_size[csf('size_number_id')]]+=$plan_cut+$colar_excess_per;
						?>
						</td>
						<?	
					}    
					?>	
					
					<td align="center"><? echo number_format($color_total_collar,0); ?></td>
					<td align="center"><? echo number_format((($color_total_collar-$color_total_collar_order_qnty)/$color_total_collar_order_qnty)*100,2); ?></td>
					</tr>
					<?
                }
                ?>
                <tr>
                <td>Size Total</td>
                <?
                foreach($nameArray_item_size  as $result_size){
					?>
					<td style="border:1px solid black;  text-align:center"><? $colar_excess_pers=($size_tatal[$result_size[csf('size_number_id')]]*$colar_excess_percent)/100; echo number_format($size_tatal[$result_size[csf('size_number_id')]],0); ?></td>
					<?
                }
                ?>
                <td  style="border:1px solid black;  text-align:center"><?  echo number_format($grand_total_collar,0); ?></td>
                <td align="center" style="border:1px solid black"> <? echo number_format((($grand_total_collar-$grand_total_collar_order_qnty)/$grand_total_collar_order_qnty)*100,2); ?></td>
                </tr>
            </table>
        </td>
        <td width="2%">
        </td>
        <?
        }
		?>
        
        <?
		$cuff_percent_size_wise_array=return_library_array( "select a.colar_cuff_per,b.gmts_sizes from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b,wo_pre_cost_fabric_cost_dtls c  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.pre_cost_fabric_cost_dtls_id=c.id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id    and a.booking_no=$txt_booking_no and a.booking_type=1 and c.body_part_id in(3) and a.status_active=1 and a.is_deleted=0", "gmts_sizes", "colar_cuff_per");
		$nameArray_item_size=sql_select( "select min(c.id) as id, b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no  and a.body_part_id=3  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by b.item_size,c.size_number_id order by id");
		
		if(count($nameArray_item_size)>0)
		{
        ?>
        <td width="49%">
            <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                <tr>
                <td colspan="<? echo count($nameArray_size)+4;?>" align="center"><b>Cuff -  Colour Size Brakedown in Pcs</b></td>
                </tr>
                <tr>
                <td width="70">Size</td>
                <?  
                foreach($nameArray_item_size  as $result_size){	     
                ?>
                    <td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
                <?	
                }    
                ?>	
                <td rowspan="2" align="center"><strong>Total</strong></td> 
                <td width="60" rowspan="2" align="center"><strong>Extra %</strong></td>
                </tr>
                <tr>
                <td>Cuff Size</td>
                <?
                foreach($nameArray_item_size  as $result_item_size){	     
                ?>
                <td align="center" style="border:1px solid black"><strong><? echo $result_item_size[csf('item_size')];?></strong></td>
                <?	
                }    
                 $color_wise_wo_sql=sql_select("select a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method,c.color_number_id,sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and a.body_part_id=3  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and  d.status_active=1 and d.is_deleted=0 group by c.color_number_id ,a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method order by a.id ");
                foreach($color_wise_wo_sql as $color_wise_wo_result){
                    $color_total_cuff=0;
                    $color_total_cuff_order_qnty=0;
                    $process_loss_method=$color_wise_wo_result[csf("process_loss_method")];
                    $constrast_color_arr=array();
                    if($color_wise_wo_result[csf("color_size_sensitive")]==3){
                        $constrast_color=explode('__',$color_wise_wo_result[csf("color_break_down")]);
                        for($i=0;$i<count($constrast_color);$i++){
                            $constrast_color2=explode('_',$constrast_color[$i]);
                            $constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
                        }
                    }
                ?> 
                    <tr>
                    <td>
                    <?
                    if($color_wise_wo_result[csf("color_size_sensitive")]==3){
                        echo strtoupper ($constrast_color_arr[$color_wise_wo_result[csf('color_number_id')]]);
                        $lab_dip_color_id=return_field_value("contrast_color_id","wo_pre_cos_fab_co_color_dtls","job_no='".$color_wise_wo_result[csf('job_no')]."' and pre_cost_fabric_cost_dtls_id=".$color_wise_wo_result[csf('id')]." and gmts_color_id=".$color_wise_wo_result[csf('color_number_id')]."");
                    }
                    else{
                        echo $color_library[$color_wise_wo_result[csf('color_number_id')]];
                        $lab_dip_color_id=$color_wise_wo_result[csf('color_number_id')];
                    }
                    ?>
                    
                    </td>
                    <?
                    foreach($nameArray_item_size  as $result_size){
                        ?>
                        <td align="center" style="border:1px solid black">
                        <?
                        $color_wise_wo_sql_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$color_wise_wo_result[csf('color_number_id')]."   and  status_active=1 and is_deleted =0");
                        
                        list($plan_cut_qnty)=$color_wise_wo_sql_qnty;
                        $plan_cut=$plan_cut_qnty[csf('plan_cut_qnty')];
                        $cuff_excess_per=(($plan_cut*2)*$cuff_excess_percent)/100;
                        echo number_format($plan_cut*2+$cuff_excess_per,0); 
                        $color_total_cuff+=$plan_cut*2+$cuff_excess_per; 
                        $color_total_cuff_order_qnty+=$plan_cut*2; 
                        $grand_total_cuff+=$plan_cut*2+$cuff_excess_per; 
                        $grand_total_cuff_order_qnty+=$plan_cut*2;
                        ?>
                        </td>
                        <?	
                    }    
                    ?>	
                    
                    <td align="center"><? echo number_format($color_total_cuff,0); ?></td>
                    <td align="center"><? echo number_format((($color_total_cuff-$color_total_cuff_order_qnty)/$color_total_cuff_order_qnty)*100,2); ?></td>
                    </tr>
                    <?
                    }
                    ?>
                    <tr>
                    <td>Size Total</td>
                    <?
                    foreach($nameArray_item_size  as $result_size){
						?>
						<td style="border:1px solid black;  text-align:center"><? $cuff_excess_pers=(($size_tatal[$result_size[csf('size_number_id')]]*2)*$cuff_excess_percent)/100; echo number_format($size_tatal[$result_size[csf('size_number_id')]]*2+$cuff_excess_pers,0); ?></td>
						<?
                    }
                    ?>
                    <td  style="border:1px solid black;  text-align:center"><?  echo number_format($grand_total_cuff,0); ?></td>
                    <td align="center" style="border:1px solid black"> <? echo number_format((($grand_total_cuff-$grand_total_cuff_order_qnty)/$grand_total_cuff_order_qnty)*100,2); ?></td>
                    </tr>
            </table>
        </td>
        <?
		}
		?>
        </tr>
        </table>
        
        
        <br/>
        <?
		$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');			
		$yarn_sql_array=sql_select("SELECT min(id) as id ,count_id, copm_one_id, percent_one,copm_two_id, percent_two,color, type_id, sum(cons_qnty) as yarn_required, AVG(rate) as rate from wo_pre_cost_fab_yarn_cost_dtls where job_no='$job_no' and  status_active=1 and is_deleted=0 group by count_id,copm_one_id,percent_one, copm_two_id,percent_two,color,type_id order by id");
		?>
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
            <tr>
                <td width="49%" valign="top">
                    <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                        <tr align="center">
                            <td colspan="7"><b>Yarn Required Summary (Pre Cost)</b></td>
                        </tr>
                        <tr align="center">
                            <td>Sl</td>
                            <td>Yarn Description</td>
                            <td>Brand</td>
                            <td>Lot</td>
                            <?
                            if($show_yarn_rate==1){
                            ?>
                            <td>Rate</td>
                            <?
                            }
                            ?>
                            <td>Cons for <? echo $costing_per; ?> Gmts</td>
                            <td>Total (KG)</td>
                        </tr>
                        <?
                        $i=0;
                        $total_yarn=0;
                        foreach($yarn_sql_array  as $row){
							$i++;
							?>
							<tr align="center">
                                <td><? echo $i; ?></td>
                                <td>
                                <?
                                $yarn_des=$yarn_count_arr[$row[csf('count_id')]]." ".$composition[$row[csf('copm_one_id')]]." ".$row[csf('percent_one')]."%  ";
                                if($row['copm_two_id'] !=0){
									$yarn_des.=$composition[$row[csf('copm_two_id')]]." ".$row[csf('percent_two')]."%";
                                }
                                $yarn_des.=$color_library[$row[csf('color')]]." ";
                                $yarn_des.=$yarn_type[$row[csf('type_id')]];
                                echo $yarn_des;
                                ?>
                                </td>
                                <td></td>
                                <td></td>
                                <?
                                if($show_yarn_rate==1){
									?>
									<td><? echo number_format($row[csf('rate')],4); ?></td>
									<?
                                }
                                ?>
                                <td><? echo number_format($row[csf('yarn_required')],4); ?></td>
                                <td align="right"></td>
							</tr>
							<?
                        }
                        ?>
                        <tr align="center">
                            <td>Total</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <?
                            if($show_yarn_rate==1){
								?>
								<td></td>
								<?
                            }
                            ?>
                            <td></td>
                            <td align="right"></td>
                        </tr>
                    </table>
                </td>
                <td width="2%">
                </td>
                <td width="49%" valign="top" align="center">
					<? 
                    $nameArray_imge =sql_select("SELECT image_location FROM common_photo_library where master_tble_id='$job_no' and file_type=1");
                    ?>
                    <div id="div_size_color_matrix" style="float:left;">
                        <fieldset id="" >
                            <legend>Image </legend>
                            <table width="310">
                                <tr>
									<?
										$img_counter = 0;
										foreach($nameArray_imge as $result_imge){	
											if($path==""){
												$path='../../';
											}
											?>
											<td>
												<img src="<? echo $path.$result_imge[csf('image_location')]; ?>" width="90" height="100" border="2"/>
											</td>
											<?
											$img_counter++;
										}
                                    ?>
                                </tr>
                            </table>   
                        </fieldset>
                    </div>  
                </td>
            </tr>
        </table>
        <br/>
        
        
        
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td width="49%" style="border:solid; border-color:#000; border-width:thin" valign="top">
                    <table  width="100%"  border="0" cellpadding="0" cellspacing="0">
                	<thead>
                    	<tr>
                        	<th width="3%"></th><th width="97%" align="left"><u>Special Instruction</u></th>
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
                            	<tr id="settr_1" valign="top">
                                    <td style="vertical-align:top">
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                   <strong style="font-size:20px"> <? echo $row[csf('terms')]; ?></strong>
                                    </td>
                                </tr>
                            <?
						}
					}
					?>
                </tbody>
                </table>
                </td>
                <td width="2%">
                </td>
                <td width="49%" valign="top">
	<!--ggg-->
                </td>
            </tr>
        </table>
       <br>
       
<fieldset id="div_size_color_matrix" style="max-width:1000;">
        <legend>TNA Information</legend>
        <!--<span style="font-size:180; font-weight:bold;"></span>-->
        <table width="100%" style="border:1px solid black;font-size:12px" border="1" cellpadding="2" cellspacing="0" rules="all">                    	
            <tr>
            	<td rowspan="2" align="center" valign="top">SL</td>
            	<td width="180" rowspan="2"  align="center" valign="top"><b>Order No</b></td>
                <td colspan="2" align="center" valign="top"><b>Yarn Receive</b></td>
                <td colspan="2" align="center" valign="top"><b>Knitting</b></td>
                <td colspan="2" align="center" valign="top"><b>Dyeing</b></td>
                <td colspan="2" align="center" valign="top"><b>Finishing Fabric</b></td>
                <td colspan="2" align="center" valign="top"><b>Cutting </b></td>
                <td colspan="2" align="center" valign="top"><b>Sewing </b></td>
                <td colspan="2"  align="center" valign="top"><b>Ex-factory </b></td>
            </tr> 
            <tr>
            	<td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                
            </tr>
            <? 
			
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	if ($cbo_fabric_natu!=0) $cbo_fabric_natu="and a.fab_nature_id='$cbo_fabric_natu'"; 
	if ($cbo_fabric_source!=0) $cbo_fabric_source_cond="and a.fabric_source='$cbo_fabric_source'"; 
	$paln_cut_qnty_array=return_library_array( "select min(id) as id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown  where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and is_deleted=0 and status_active=1 group by color_number_id,size_number_id,item_number_id,po_break_down_id", "id", "plan_cut_qnty");
	$item_ratio_array=return_library_array( "select gmts_item_id,set_item_ratio from wo_po_details_mas_set_details  where job_no =$txt_job_no", "gmts_item_id", "set_item_ratio");
	
	//------------------------------ Query for TNA start-----------------------------------
				$po_id_all=str_replace("'","",$txt_order_no_id);
				$po_num_arr=return_library_array("select id,po_number from wo_po_break_down where id in($po_id_all)",'id','po_number');
				$tna_start_sql=sql_select( "select id,po_number_id, 
								(case when task_number=31 then task_start_date else null end) as fab_booking_start_date,
								(case when task_number=31 then task_finish_date else null end) as fab_booking_end_date,
								(case when task_number=60 then task_start_date else null end) as knitting_start_date,
								(case when task_number=60 then task_finish_date else null end) as knitting_end_date,
								(case when task_number=61 then task_start_date else null end) as dying_start_date,
								(case when task_number=61 then task_finish_date else null end) as dying_end_date,
								(case when task_number=73 then task_start_date else null end) as finishing_start_date,
								(case when task_number=73 then task_finish_date else null end) as finishing_end_date,
								(case when task_number=84 then task_start_date else null end) as cutting_start_date,
								(case when task_number=84 then task_finish_date else null end) as cutting_end_date,
								(case when task_number=86 then task_start_date else null end) as sewing_start_date,
								(case when task_number=86 then task_finish_date else null end) as sewing_end_date,
								(case when task_number=110 then task_start_date else null end) as exfact_start_date,
								(case when task_number=110 then task_finish_date else null end) as exfact_end_date,
								(case when task_number=47 then task_start_date else null end) as yarn_rec_start_date,
								(case when task_number=47 then task_finish_date else null end) as yarn_rec_end_date
								from tna_process_mst
								where status_active=1 and po_number_id in($po_id_all)");
				$tna_fab_start=$tna_knit_start=$tna_dyeing_start=$tna_fin_start=$tna_cut_start=$tna_sewin_start=$tna_exfact_start="";
				$tna_date_task_arr=array();
				foreach($tna_start_sql as $row)
				{
					if($row[csf("fab_booking_start_date")]!="" && $row[csf("fab_booking_start_date")]!="0000-00-00")
					{
						if($tna_fab_start=="")
						{
							$tna_fab_start=$row[csf("fab_booking_start_date")];
						}
					}
					

					if($row[csf("knitting_start_date")]!="" && $row[csf("knitting_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['knitting_start_date']=$row[csf("knitting_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['knitting_end_date']=$row[csf("knitting_end_date")];
					}
					if($row[csf("dying_start_date")]!="" && $row[csf("dying_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['dying_start_date']=$row[csf("dying_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['dying_end_date']=$row[csf("dying_end_date")];
					}
					if($row[csf("finishing_start_date")]!="" && $row[csf("finishing_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['finishing_start_date']=$row[csf("finishing_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['finishing_end_date']=$row[csf("finishing_end_date")];
					}
					if($row[csf("cutting_start_date")]!="" && $row[csf("cutting_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['cutting_start_date']=$row[csf("cutting_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['cutting_end_date']=$row[csf("cutting_end_date")];
					}
					if($row[csf("sewing_start_date")]!="" && $row[csf("sewing_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['sewing_start_date']=$row[csf("sewing_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['sewing_end_date']=$row[csf("sewing_end_date")];
					}
					if($row[csf("exfact_start_date")]!="" && $row[csf("exfact_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['exfact_start_date']=$row[csf("exfact_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['exfact_end_date']=$row[csf("exfact_end_date")];
					}
					if($row[csf("yarn_rec_start_date")]!="" && $row[csf("yarn_rec_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_rec_start_date']=$row[csf("yarn_rec_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_rec_end_date']=$row[csf("yarn_rec_end_date")];
					}
				}
				
			$i=1;
			foreach($tna_date_task_arr as $order_id=>$row)
			{
				 //$tna_date_task_arr//knitting_start_date dying_start_date finishing_start_date cutting_start_date sewing_start_date exfact_start_date
				?>
                <tr>
                	<td><? echo $i; ?></td>
                    <td><? echo $po_num_arr[$order_id]; ?></td>
                    <td align="center"><? echo change_date_format($row['yarn_rec_start_date']); ?></td>
                    <td  align="center"><? echo change_date_format($row['yarn_rec_end_date']); ?></td>
                	<td align="center"><? echo change_date_format($row['knitting_start_date']); ?></td>
                    <td  align="center"><? echo change_date_format($row['knitting_end_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['dying_start_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['dying_end_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['finishing_start_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['finishing_end_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['cutting_start_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['cutting_end_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['sewing_start_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['sewing_end_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['exfact_start_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['exfact_end_date']); ?></td>
                </tr>
                <?
				$i++;
			}
			?> 
           
        </table>
        </fieldset>       
       
       
       
        <?
		}// fabric Source End
		?>
	<?
    echo signature_table(1, $cbo_company_name, "1330px");
	echo "****".custom_file_name($txt_booking_no,$style_sting,$txt_job_no);
    ?>
    </div>
    <?
}
?>