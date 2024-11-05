<?php 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];
//--------------------------------------------------------------------------------------------------------------------
if($action=="load_report_format")
{
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=7 and report_id in(40) and is_deleted=0 and status_active=1");
		//echo "document.getElementById('report_ids').value = '".$print_report_format."';\n";
		//echo "print_report_button_setting('".$print_report_format."');\n";
	echo trim($print_report_format);	
	exit();

}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 120, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/date_wise_production_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );",0 );
	exit();  	 
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor", 110, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select --", $selected, "",0 ); 
	exit();   	 
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );     	 
	exit();
}
//style search------------------------------//
if($action=="style_refarence_surch")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	//echo $style_id;die;

	?>
    <script>
		
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
    	function check_all_data() 
		{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) 
			{
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
			//alert(strCon);
				var splitSTR = strCon.split("_");
				var str = splitSTR[0];
				var selectID = splitSTR[1];
				var selectDESC = splitSTR[2];
				//$('#txt_individual_id' + str).val(splitSTR[1]);
				//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
				
				toggle( document.getElementById( 'tr_' + str), '#FFFFCC' );
				
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
				$('#txt_selected_id').val( id );
				$('#txt_selected').val( name ); 
				$('#txt_selected_no').val( num );
		}
		
		/*function js_set_value( str ) { //alert(str);
		toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
		
		if( jQuery.inArray( $('#txt_serial_id' + str).val(), selected_id ) == -1 ) {
			selected_id.push( $('#txt_serial_id' + str).val() );
			selected_no.push( $('#txt_serial_no' + str).val() );
 		}




		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == $('#txt_serial_id' + str).val() ) break;
			}
			selected_id.splice( i, 1 );
			selected_no.splice( i, 1 );
		}
		var id = '';	var no = '';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
			no += selected_no[i] + ',';
		}
		id = id.substr( 0, id.length - 1 );
		no = no.substr( 0, no.length - 1 );
  		$('#txt_string_id').val( id );
		$('#txt_string_no').val( no );
	}
	 
	function fn_onClosed()
	{
		var txt_string = $('#txt_string').val();
		if(txt_string==""){ alert("Please Select The Serial"); return;}
		parent.emailwindow.hide();
	}*/
		
    </script>
    <?
	$buyer=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
	$job_year=str_replace("'","",$job_year);
	if($buyer!=0) $buyer_cond=" and a.buyer_name=$buyer"; else $buyer_cond="";
	if($db_type==0)
	{
		if($job_year!=0) $job_year_cond=" and year(a.insert_date)=$job_year"; else $job_year_cond="";
		$select_date=" year(a.insert_date)";
	}
	else if($db_type==2)
	{
		if($job_year!=0) $job_year_cond=" and to_char(a.insert_date,'YYYY')=$job_year"; else $job_year_cond="";
		$select_date=" to_char(a.insert_date,'YYYY')";
	}
	
	$sql = "select a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num,$select_date as year from wo_po_details_master a where a.company_name=$company $buyer_cond  $job_year_cond and is_deleted=0 order by job_no_prefix_num"; 
	//echo $sql; die;
	echo create_list_view("list_view", "Style Ref No,Job No,Year","160,90,100","400","310",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "0", $arr, "style_ref_no,job_no_prefix_num,year", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	
	?>
    <script language="javascript" type="text/javascript">
	var style_no='<? echo $txt_style_ref_no;?>';
	var style_id='<? echo $txt_style_ref_id;?>';
	var style_des='<? echo $txt_style_ref;?>';
	//alert(style_id);
	if(style_no!="")
	{
		style_no_arr=style_no.split(",");
		style_id_arr=style_id.split(",");
		style_des_arr=style_des.split(",");
		var str_ref="";
		for(var k=0;k<style_no_arr.length; k++)
		{
			str_ref=style_no_arr[k]+'_'+style_id_arr[k]+'_'+style_des_arr[k];
			js_set_value(str_ref);
		}
	}
	</script>
    
    <?
	
	exit();
}


//order search------------------------------//
if($action=="order_surch")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
    <script>
		
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
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
			//alert(strCon);
				var splitSTR = strCon.split("_");
				var str_or = splitSTR[0];
				var selectID = splitSTR[1];
				var selectDESC = splitSTR[2];
				//$('#txt_individual_id' + str).val(splitSTR[1]);
				//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
				
				toggle( document.getElementById( 'tr_' + str_or ), '#FFFFCC' );
				
				if( jQuery.inArray( selectID, selected_id ) == -1 ) {
					selected_id.push( selectID );
					selected_name.push( selectDESC );
					selected_no.push( str_or );				
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
				$('#txt_selected_id').val( id );
				$('#txt_selected').val( name ); 
				$('#txt_selected_no').val( num );
		}
    </script>
    <?
	$buyer=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
	$job_year=str_replace("'","",$job_year);
	$txt_style_ref_id=str_replace("'","",$txt_style_ref_id);
	
	if($buyer!=0) $buyer_cond="and b.buyer_name=$buyer"; else $buyer_cond="";
	if($db_type==0)
	{
		if($job_year!=0) $job_year_cond=" and year(b.insert_date)=$job_year"; else $job_year_cond="";
		$select_date=" year(b.insert_date)";
	}
	else if($db_type==2)
	{
		if($job_year!=0) $job_year_cond=" and to_char(b.insert_date,'YYYY')=$job_year"; else $job_year_cond="";
		$select_date=" to_char(b.insert_date,'YYYY')";
	}
	if($txt_style_ref_id!="") $style_cond=" and b.id in($txt_style_ref_id)"; else $style_cond="";
	$sql = "select a.id,a.po_number,a.job_no_mst,b.style_ref_no,b.job_no_prefix_num,$select_date as year from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and b.company_name=$company $buyer_cond $job_year_cond  $style_cond and a.status_active in(1,2,3) and b.status_active=1"; 
	//echo $sql; die;
	echo create_list_view("list_view", "Order NO,Job No,Year,Style Ref No","150,50,70,150","500","310",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "po_number,job_no_prefix_num,year,style_ref_no", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	
	?>
    <script language="javascript" type="text/javascript">
	var style_no='<? echo $txt_order_id_no;?>';
	var style_id='<? echo $txt_order_id;?>';
	var style_des='<? echo $txt_order;?>';
	//alert(style_id);
	if(style_no!="")
	{
		style_no_arr=style_no.split(",");
		style_id_arr=style_id.split(",");
		style_des_arr=style_des.split(",");
		var str_ref="";
		for(var k=0;k<style_no_arr.length; k++)
		{
			str_ref=style_no_arr[k]+'_'+style_id_arr[k]+'_'+style_des_arr[k];
			js_set_value(str_ref);
		}
	}
	</script>
    
    <?
	exit();
}

$color_Arr_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );	
$size_Arr_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );	
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id","floor_name"  );
$buyer_short_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name"  );


//echo "<pre>"; print_r ($ex_date_library); die;

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_location=str_replace("'","",$cbo_location);
	$cbo_floor=str_replace("'","",$cbo_floor);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_job_year=str_replace("'","",$cbo_job_year);  
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_style_ref_id=str_replace("'","",$txt_style_ref_id);
	$txt_order_no=str_replace("'","",$txt_order);
	$txt_order_id=str_replace("'","",$txt_order_id);
	$shipping_status=str_replace("'","",$shipping_status);
	$txt_style_ref_number=str_replace("'","",$txt_style_ref_number);
	$cbo_style_owner_wise_status=str_replace("'","",$cbo_style_owner_wise_status);
	
	$txt_order_number=str_replace("'","",$txt_order);
	$txt_order_number=trim($txt_order_number);
	if($txt_order_id=="")
	{
		$txt_order_number_expl=explode(",",$txt_order_number);
		$poDatas="";
		foreach($txt_order_number_expl as $poData)
		{
			$poDatas.="'".$poData."'".",";
		}
		$poNumbers=chop($poDatas,",");
		
		if($txt_order_number!="")
		{
			$str_poNumbers_cond =" and b.po_number in($poNumbers)";
		}
		else
		{
			$str_poNumbers_cond = "";
		}
		//echo $str_poNumbers_cond;
		//echo $txt_order_number;
	}
	
	$country_id=str_replace("'","",$country_id);
	
	if($txt_date_from!="" && $txt_date_to!="")
	{
		if($db_type==0)
		{
			$txt_datefrom=change_date_format($txt_date_from,'yyyy-mm-dd');
			$txt_dateto=change_date_format($txt_date_to,'yyyy-mm-dd');
		}
		else if($db_type==2)
		{
			$txt_datefrom=change_date_format($txt_date_from,'','',-1);
			$txt_dateto=change_date_format($txt_date_to,'','',-1);
		}
	}
	
	if ($txt_style_ref_number!="")
	{
		$txt_style_ref_number_cond="and a.style_ref_no like '%$txt_style_ref_number%'";
	}
	else	
	{
		$txt_style_ref_number_cond="";
	}	 
		
	if($type==0) //Summary
	{	
		$str_po_cond="";
		if($cbo_style_owner_wise_status==1)
		{
			 $str_po_cond.=" and b.pub_shipment_date between '$txt_datefrom'and  '$txt_dateto' ";	
		}
		else
		{
			$ex_po_library=return_library_array( "select po_break_down_id,po_break_down_id as order_id from pro_ex_factory_mst where ex_factory_date between '$txt_datefrom' and  '$txt_dateto'", "po_break_down_id", "order_id"  );
			if(count($ex_po_library)>0)
			{
				$str_po_cond.=where_con_using_array($ex_po_library,0,'b.id');
			}
			else
			{
				?>
				<div class="alert alert-danger">Data Not Found</div>
				<?
				die;
			}	
		}
		//if($cbo_company_name!=0) $str_po_cond.=" and a.company_name=$cbo_company_name";
		if($cbo_buyer_name!=0) $str_po_cond.=" and a.buyer_name=$cbo_buyer_name";
		if($cbo_location>0) $str_po_cond.=" and a.location_name=$cbo_location";
		if($cbo_job_year>0)
		{
			if($db_type==0)
			{
				$str_po_cond .=" and year(a.insert_date)='$cbo_job_year'";
			}
			else
			{
				$str_po_cond .=" and to_char(a.insert_date,'YYYY')='$cbo_job_year'";
			}	
		}
		if($txt_style_ref_id!="")
		{
			$str_po_cond.=" and a.id in($txt_style_ref_id)";
		}
		else if($txt_style_ref!="")
		{
			$str_po_cond .=" and a.job_no_prefix_num in($txt_style_ref)";
		}
		
		/*if($txt_order_id!="")
		{
			$str_po_cond.=" and b.id in($txt_order_id)";
		}
		else if($txt_order_no!="")
		{
			$str_po_cond .=" and b.po_number in($poNumbers)";
		}*/

		$order_no_arr = explode(",", str_replace("'","",$txt_order));
		$order_id_arr = explode(",", str_replace("'","",$txt_order_id));
		$order_no_cond = "";
		$order_id_cond = "";
		$orderNo = "";
		$orderId = "";
		if(count($order_no_arr)==1)
		{
			foreach ($order_no_arr as $val) 
			{
				if($orderNo =="")
				{
					$orderNo .= "'%$val%'";
				}
			}
			$order_no_cond = "and b.po_number LIKE $orderNo";
		}
		else if(count($order_no_arr) > 1)
		{
			foreach ($order_no_arr as $val) 
			{
				if($orderNo =="")
				{
					$orderNo .= "'$val'";
				}
				else
				{
					$orderNo .= ","."'$val'";
				}
			}
			$order_no_cond = " and b.po_number in($orderNo)";
		}
		else
		{

		}
		// ====================================== for order id ==========================
		if(count($order_id_arr)==1)
		{
			foreach ($order_id_arr as $val) 
			{
				if($orderId =="")
				{
					$orderId .= "'%$val%'";
				}
			}
			$order_id_cond = "and b.id LIKE $orderId";
		}
		else if(count($order_id_arr) > 1)
		{
			foreach ($order_id_arr as $val) 
			{
				if($orderId =="")
				{
					$orderId .= "'$val'";
				}
				else
				{
					$orderId .= ","."'$val'";
				}
			}
			$order_id_cond = " and b.id in($orderId)";
		}
		else
		{

		}

		
		if($shipping_status!=0 && $shipping_status!=4)
		{
			$str_po_cond .=" and b.shiping_status='$shipping_status'"; 
		}
		else 
		{
			if($shipping_status==4)
			{
				$str_po_cond .=" and b.shiping_status in(1,2)";
			}
			else
			{
				$str_po_cond .=" and b.shiping_status in(1,2,3)";
			}
		}
		$internal_ref_cond_prod=(str_replace("'", "", $txt_internal_ref)=="")? "" : " and c.po_break_down_id in (select id from wo_po_break_down where grouping=$txt_internal_ref and status_active in(1,2,3) and is_deleted=0) "; 
		$internal_ref_cond_po=(str_replace("'", "", $txt_internal_ref)=="")? "" : " and b.grouping=$txt_internal_ref "; 

		//if($txt_datefrom!="" &&  $txt_dateto!="")  $str_po_cond .=" and b.pub_shipment_date between '$txt_datefrom' and '$txt_dateto'"; 
		if($db_type==0) $select_job_year="year(a.insert_date) as job_year"; else $select_job_year="to_char(a.insert_date,'YYYY') as job_year"; 

		// ====================================== MAIN QUERY ========================================	

		$order_sql="SELECT a.job_no_prefix_num,a.job_no, $select_job_year,b.id, b.po_number, b.po_quantity, b.unit_price, b.po_total_price, b.job_no_mst,b.file_no,b.grouping, b.pub_shipment_date as shipment_date, b.is_confirmed, b.shiping_status, b.excess_cut, b.plan_cut, a.company_name, a.style_owner, a.buyer_name, a.set_break_down, a.style_ref_no,c.color_number_id, c.size_number_id,c.item_number_id,c.size_order,c.article_number
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
		where b.job_id=a.id  and b.id=c.po_break_down_id and a.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 $str_po_cond $txt_style_ref_number_cond $internal_ref_cond_po $order_no_cond $order_id_cond
		order by b.id,c.size_order"; // and b.shiping_status != 3
		// echo $order_sql;die();		
		$sql_po_result=sql_select($order_sql);
		$main_array = array();
		if(count($sql_po_result)==0){ echo "<div style='color:red;font-size:18px;text-align:center;'>Data not available! please try again.</div>"; die();}
		foreach($sql_po_result as $row)
		{
			$all_po_id.=$row[csf("id")].",";
			$all_po_id_arr[$row[csf("id")]]=$row[csf("id")];
			
			$main_array[$row[csf("buyer_name")]][$row[csf("id")]]['po_number'] = $row[csf("po_number")];
			
		}
		


	    //$all_po_id=chop($all_po_id,",");

	    $all_po_ids=implode(",", array_unique($all_po_id_arr)); 
	    $all_po_id = $all_po_ids;
	    $all_po_ids_cond="";
 		if(count($all_po_id_arr)>0)
 		{
 			  if($db_type==2 && count($all_po_id_arr)>999)
			   {
			   		$chunk_arr=array_chunk($all_po_id_arr, 999);
			   		foreach($chunk_arr as $vals)
			   		{
			   			$po_ids=implode(",", $vals);
			   			if($all_po_ids_cond=="")
			   			{
			   				$all_po_ids_cond.=" and (c.order_id in ($po_ids)";
			   			}
			   			else
			   			{
			   				$all_po_ids_cond.=" or  c.order_id in ($po_ids)";
			   			}
			   		}
			   		$all_po_ids_cond.= " )";
			   }
			   else
			   {
			   		$all_po_ids_cond= " and c.order_id in($all_po_ids) ";
			   }

 		}
	 

 		if($all_po_id)
 		{
 			$template_id_arr=return_library_array("select po_number_id, template_id from tna_process_mst where po_number_id in ('".$all_po_id."') group by po_number_id, template_id","po_number_id","template_id");
 		}
 		
				
		$po_product_cond="";
		if($db_type==0)
		{
			if($all_po_id!="") $po_product_cond =" and c.po_break_down_id in($all_po_id)";
		}
		else
		{
			if($all_po_id!="")
			{
				$all_po_id_arr=array_chunk(array_unique(explode(",",$all_po_id)),999);
				$p=1;
				if(!empty($all_po_id_arr))
				{
					foreach($all_po_id_arr as $po_id)
					{
						if($p==1) $po_product_cond =" and (c.po_break_down_id in(".implode(',',$po_id).")"; else $po_product_cond .=" or c.po_break_down_id in(".implode(',',$po_id).")";
						$p++;
					}
					$po_product_cond .=" )";
				}
			}
		}
		// ================================== FOR EXFACTORY ========================================
		$ex_factory_arr=array();		
		$ex_fac_level=sql_select("SELECT ex_factory  from variable_settings_production where company_name=$cbo_company_name and variable_list=1 and status_active=1");
		if($ex_fac_level[0][csf("ex_factory")]==1)
		{
			$ex_factory_data=sql_select("SELECT c.po_break_down_id , c.item_number_id, MAX(c.ex_factory_date) AS ex_factory_date, sum(c.total_carton_qnty) as total_carton_qnty,
			sum(CASE WHEN c.entry_form!=85 THEN c.ex_factory_qnty ELSE 0 END)-sum(CASE WHEN c.entry_form=85 THEN c.ex_factory_qnty ELSE 0 END) as ex_factory_qnty 
			from pro_ex_factory_mst c 	where     c.status_active=1 and c.is_deleted=0  $po_product_cond group by c.po_break_down_id, c.item_number_id");
	
			foreach($ex_factory_data as $exRow)
			{
				$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]]['date']=$exRow[csf('ex_factory_date')];
				$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]]['qty']=$exRow[csf('ex_factory_qnty')];
				$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]]['ex_fac_carton_qty']+=$exRow[csf('total_carton_qnty')];

			}

		}
		else // color and size wise
		{
			$ex_factory_data="SELECT c.po_break_down_id , f.color_number_id,f.size_number_id, sum(c.total_carton_qnty) as total_carton_qnty,
			sum(CASE WHEN c.entry_form!=85 THEN d.production_qnty ELSE 0 END)-sum(CASE WHEN c.entry_form=85 THEN d.production_qnty ELSE 0 END) as ex_factory_qnty 
			from pro_ex_factory_mst c ,pro_ex_factory_dtls d,wo_po_break_down e ,wo_po_color_size_breakdown f 
			where    c.id=d.mst_id and c.po_break_down_id=e.id and e.id=f.po_break_down_id and f.id=d.color_size_break_down_id and 
         	c.status_active=1 and c.is_deleted=0 and    d.status_active=1 and d.is_deleted=0 and      e.status_active in(1,2,3) and e.is_deleted=0 and f.status_active in(1,2,3)    $po_product_cond group by c.po_break_down_id , f.color_number_id,f.size_number_id";
			$ex_factory_data_res = sql_select($ex_factory_data);
			foreach($ex_factory_data_res as $exRow)
			{				
				$ex_factory_arr[$exRow[csf('po_break_down_id')]]['ex_qty']=$exRow[csf('ex_factory_qnty')];

			}
		}
		unset($ex_factory_data_res);
		// ========================================= FOR production ================================
		if($db_type==0)
		{
			
			$prod_sql="SELECT c.po_break_down_id, a.color_number_id,a.size_number_id,
				IFNULL(sum(CASE WHEN d.production_type ='1' THEN d.production_qnty ELSE 0 END),0) AS cutting_qnty,	
				
				IFNULL(sum(CASE WHEN d.production_type ='2' and c.embel_name=1 and c.production_source=1 THEN d.production_qnty ELSE 0 END),0) AS print_issue_inhouse,
				IFNULL(sum(CASE WHEN d.production_type ='3' and c.embel_name=1 and c.production_source=1 THEN d.production_qnty ELSE 0 END),0) AS print_rcv_inhouse,
				
				IFNULL(sum(CASE WHEN d.production_type ='2' and c.embel_name=2 and c.production_source=1 THEN d.production_qnty ELSE 0 END),0) AS emb_issue_inhouse,
				IFNULL(sum(CASE WHEN d.production_type ='3' and c.embel_name=2 and c.production_source=1 THEN d.production_qnty ELSE 0 END),0) AS emb_rcv_inhouse,				
				
				IFNULL(sum(CASE WHEN d.production_type ='4' and c.production_source=1 THEN d.production_qnty ELSE 0 END),0) AS sewingin_qnty_in,
				IFNULL(sum(CASE WHEN d.production_type ='5' and c.production_source=1 THEN d.production_qnty ELSE 0 END),0) AS sewingout_qnty_in,
				
				
				IFNULL(sum(CASE WHEN d.production_type ='8' and c.production_source=1 THEN d.production_qnty ELSE 0 END),0) AS finish_qnty_inhouse,
				IFNULL(sum(CASE WHEN d.production_type ='1' THEN d.reject_qty ELSE 0 END),0) AS cutting_rej_qnty,
				IFNULL(sum(CASE WHEN d.production_type in (1,2,4,3,5,7,8,11) and c.production_type in (1,2,4,3,5,7,8,11) THEN d.reject_qty ELSE 0 END),0) AS rej_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='8' and c.production_source=1 then c.carton_qty ELSE 0 END),0) as carton_qty_no_inhouse, 
				IFNULL(sum(CASE WHEN c.production_type ='8' and c.production_source=3 then c.carton_qty ELSE 0 END),0) as carton_qty_no_outbound,
				IFNULL(sum(CASE When d.production_type ='8' and c.production_type ='8' then c.carton_qty ELSE 0 END),0) as carton_total_qty
 
			from 
				pro_garments_production_mst c,pro_garments_production_dtls d,wo_po_color_size_breakdown a,wo_po_details_master e 
			where  c.id=d.mst_id  and c.po_break_down_id=a.po_break_down_id and a.job_no_mst=e.job_no  and a.id=d.color_size_break_down_id and c.status_active=1 and   c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and e.status_active=1 and e.is_deleted=0  $po_product_cond $internal_ref_cond_prod 
			group by c.po_break_down_id, a.color_number_id,a.size_number_id";
			
			$sql_carton_qty=sql_select("SELECT c.po_break_down_id,
			IFNULL(sum(case when c.production_type ='8' and c.production_source=1 then c.carton_qty else 0 end),0) as carton_qty_no_inhouse,
			IFNULL(sum(case when c.production_type ='8' and c.production_source=3 then c.carton_qty else 0 end),0) as carton_qty_no_outbound,
			IFNULL(sum(case when c.production_type ='8' then c.carton_qty else 0 end),0) as carton_total_qty
			from pro_garments_production_mst c where c.status_active=1 and c.is_deleted=0 $po_product_cond $internal_ref_cond_prod group by c.po_break_down_id");
		}
		else
		{			
			$producttion_cond =" and c.po_break_down_id in($po_id)";
			$prod_sql= "SELECT c.po_break_down_id, a.color_number_id,a.size_number_id, 
				NVL(sum(CASE WHEN d.production_type ='1'  and c.production_type ='1' THEN d.production_qnty ELSE 0 END),0) AS cutting_qnty,
				NVL(sum(CASE WHEN d.production_type ='1' THEN d.reject_qty ELSE 0 END),0) AS cutting_rej_qnty,
				
				NVL(sum(CASE WHEN d.production_type ='2' and c.embel_name=1 THEN d.production_qnty ELSE 0 END),0) AS print_issue_inhouse,
				NVL(sum(CASE WHEN d.production_type ='3' and c.embel_name=1 THEN d.production_qnty ELSE 0 END),0) AS print_rcv_inhouse,
				NVL(sum(CASE WHEN d.production_type ='3' and c.embel_name=1 THEN d.reject_qty ELSE 0 END),0) AS print_rej_inhouse,
				
				NVL(sum(CASE WHEN d.production_type ='2' and c.embel_name=2 THEN d.production_qnty ELSE 0 END),0) AS emb_issue_inhouse,
				NVL(sum(CASE WHEN d.production_type ='3' and c.embel_name=2  THEN d.production_qnty ELSE 0 END),0) AS emb_rcv_inhouse,
				
				NVL(sum(CASE WHEN d.production_type ='4'  and c.production_type ='4' and c.production_source=1 THEN d.production_qnty ELSE 0 END),0) AS sewingin_qnty_in,
				NVL(sum(CASE WHEN d.production_type ='5'  and c.production_type ='5' and c.production_source=1 THEN d.production_qnty ELSE 0 END),0) AS sewingout_qnty_in,
											
				NVL(sum(CASE WHEN d.production_type in (1,3,5,7,8,11) and c.production_type in (1,3,5,7,8,11) THEN d.reject_qty ELSE 0 END),0) AS rej_qnty,				
				NVL(sum(CASE WHEN d.production_type ='8' THEN d.production_qnty ELSE 0 END),0) AS finish_qnty_inhouse
  
			from 
				pro_garments_production_mst c,pro_garments_production_dtls d,wo_po_color_size_breakdown a,wo_po_details_master e 
			where  c.id=d.mst_id     and a.job_no_mst=e.job_no  and a.id=d.color_size_break_down_id and 
				c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and e.status_active=1 and e.is_deleted=0  $po_product_cond $internal_ref_cond_prod 
				group by c.po_break_down_id, a.color_number_id,a.size_number_id";
				
			$sql_carton_qty=sql_select("SELECT c.po_break_down_id,
			NVL(sum(case when c.production_type ='8' and c.production_source=1 then c.carton_qty else 0 end),0) as carton_qty_no_inhouse,
			NVL(sum(case when c.production_type ='8' and c.production_source=3 then c.carton_qty else 0 end),0) as carton_qty_no_outbound,
			NVL(sum(case when c.production_type ='8' then c.carton_qty else 0 end),0) as carton_total_qty
			from pro_garments_production_mst c where c.status_active=1 and c.is_deleted=0 $po_product_cond $internal_ref_cond_prod group by c.po_break_down_id");
			
		}

		$gmts_prod_arr=array();
		$res_gmtsData=sql_select($prod_sql);
		foreach($res_gmtsData as $gmtsRow)
		{
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]]['sQty']=$gmtsRow[csf('sewingin_qnty_in')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]]['soQty']=$gmtsRow[csf('sewingout_qnty_in')];	
	
		}
		// echo "<pre>";print_r($gmts_prod_arr);echo "</pre>"; die;
		unset($res_gmtsData);

		
		//echo $prod_sql; die;
		// ============================================ FOR CUTTING =====================================
		$sql_cutlay="SELECT a.entry_form,b.order_id as po_id,b.color_id,c.order_id,c.size_id,sum(c.size_qty ) as qty  from ppl_cut_lay_mst a ,ppl_cut_lay_dtls b,ppl_cut_lay_bundle c where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and    c.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$cbo_company_name $all_po_ids_cond  group by a.entry_form,b.order_id,c.order_id,b.color_id,c.size_id";
		$sql_res=sql_select($sql_cutlay);
		foreach($sql_res as $vals)
		{

			$entry_form=$vals[csf("entry_form")];
			if($entry_form!=76)
			{
				$cut_lay_arrs[$vals[csf("order_id")]]+=$vals[csf("qty")];
			}
			else
			{
				$cut_lay_arrs[$vals[csf("order_id")]]+=$vals[csf("qty")];
			}
		}		
		// echo "<pre>";
		// print_r($cut_lay_arrs);
		// echo "</pre>";
			
		// ====================================== FOR TRANSFER ===================================

		$po_transfer_in_sql=sql_select("select to_po_id,production_quantity,item_number_id from pro_gmts_delivery_dtls where status_active=1 and is_deleted=0");
		foreach($po_transfer_in_sql as $vals)
		{
			$po_transfer_in_arr[$vals[csf("to_po_id")]][$vals[csf("item_number_id")]]+=$vals[csf("production_quantity")];
		}

		$po_transfer_out_sql=sql_select("select from_po_id,production_quantity,item_number_id from pro_gmts_delivery_dtls where status_active=1 and is_deleted=0");
		foreach($po_transfer_out_sql as $vals)
		{
			$po_transfer_out_arr[$vals[csf("from_po_id")]][$vals[csf("item_number_id")]]+=$vals[csf("production_quantity")];
		}
		//echo "<pre>";print_r($po_transfer_in_arr);echo "</pre>";
		//echo "<pre>";print_r($po_transfer_out_arr);echo "</pre>";die;
		unset($po_transfer_in_sql);
		unset($po_transfer_out_sql);
		// ==================================== FOR COLOR AND SIZE WISE ORDER QNTY ==================================
		$order_qnty_sql = "SELECT b.id as po_id,c.color_number_id,c.size_number_id, sum(c.order_quantity) as order_qnty 
		FROM wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
		WHERE a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id $po_product_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0
		GROUP BY b.id,c.color_number_id,c.size_number_id";
		//	echo $order_qnty_sql; die;
		$order_qnty_sql_res = sql_select($order_qnty_sql);
		$colo_size_ord_qnty_arr = array();
		foreach ($order_qnty_sql_res as  $row) 
		{
			$colo_size_ord_qnty_arr[$row[csf('po_id')]] += $row[csf('order_qnty')];
		}
		// echo "<pre>";
		// print_r($colo_size_ord_qnty_arr);
		// echo "</pre>"; die;

		

		
	
		$buyer_summery=array();
		//$buyer=1;
		foreach($main_array as $buyer_name => $buyer_data)
		{
			foreach($buyer_data as $po_id=>$row)
			{
				
					//$buyer++;
					
					$buyer_summery[$buyer_name]['order_qnty']+=$colo_size_ord_qnty_arr[$po_id];
					$buyer_summery[$buyer_name]['qty']+=$cut_lay_arrs[$po_id];
					$buyer_summery[$buyer_name]['sQty']+=$gmts_prod_arr[$po_id]['sQty'];
					$buyer_summery[$buyer_name]['soQty']+=$gmts_prod_arr[$po_id]['soQty'];
					$buyer_summery[$buyer_name]['finish_qnty_inhouse']+=$gmts_prod_arr[$po_id]['finish_qnty_inhouse'];
					$buyer_summery[$buyer_name]['ex_qty']+=$ex_factory_arr[$po_id]['ex_qty'];

				
			}
		}	
		// echo "<pre>";
       	// print_r($buyer_summery);
       	// echo "</pre>";die;

		$fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
		$toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );
		ob_start();
  		?>
  		<style type="text/css">
            .block_div { 
                    width:auto;
                    height:auto;
                    text-wrap:normal;
                    vertical-align:bottom;
                    display: block;
                    position: !important; 
                    -webkit-transform: rotate(-90deg);
                    -moz-transform: rotate(-90deg);
            }
            hr {
                border: 0; 
                background-color: #000;
                height: 1px;
            }  
            .gd-color
            {
				background: #f0f9ff; /* Old browsers */
				background: -moz-linear-gradient(top, #f0f9ff 0%, #cbebff 47%, #a1dbff 100%); /* FF3.6-15 */
				background: -webkit-linear-gradient(top, #f0f9ff 0%,#cbebff 47%,#a1dbff 100%); /* Chrome10-25,Safari5.1-6 */
				background: linear-gradient(to bottom, #f0f9ff 0%,#cbebff 47%,#a1dbff 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
				filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f0f9ff', endColorstr='#a1dbff',GradientType=0 ); /* IE6-9 */
				font-weight: bold;
			}
			.gd-color2
			{
				background: rgb(247,251,252); /* Old browsers */
				background: -moz-linear-gradient(top, rgba(247,251,252,1) 0%, rgba(217,237,242,1) 40%, rgba(173,217,228,1) 100%); /* FF3.6-15 */
				background: -webkit-linear-gradient(top, rgba(247,251,252,1) 0%,rgba(217,237,242,1) 40%,rgba(173,217,228,1) 100%); /* Chrome10-25,Safari5.1-6 */
				background: linear-gradient(to bottom, rgba(247,251,252,1) 0%,rgba(217,237,242,1) 40%,rgba(173,217,228,1) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
				filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f7fbfc', endColorstr='#add9e4',GradientType=0 ); /* IE6-9 */
				font-weight: bold;
			}
			.gd-color3
			{
				background: rgb(254,255,255); /* Old browsers */
				background: -moz-linear-gradient(top, rgba(254,255,255,1) 0%, rgba(221,241,249,1) 35%, rgba(160,216,239,1) 100%); /* FF3.6-15 */
				background: -webkit-linear-gradient(top, rgba(254,255,255,1) 0%,rgba(221,241,249,1) 35%,rgba(160,216,239,1) 100%); /* Chrome10-25,Safari5.1-6 */
				background: linear-gradient(to bottom, rgba(254,255,255,1) 0%,rgba(221,241,249,1) 35%,rgba(160,216,239,1) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
				filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#feffff', endColorstr='#a0d8ef',GradientType=0 ); /* IE6-9 */
				border: 1px solid #dccdcd;
				font-weight: bold;
			}
        </style> 
		<div style="margin-left:25%">
			<table width="570" cellspacing="0" >
				<tr style="border:none;">
					<td colspan="12" align="center" style="border:none; font-size:16px;">
						Company Name : <? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>                                
					</td>
				</tr>
				<tr class="form_caption" style="border:none;">
					<td colspan="10" align="center" style="border:none;font-size:14px; font-weight:bold" >
						<? echo "Order Wise RMG Production Status V3"; ?>    
					</td>
				</tr>
				<tr style="border:none;">
					<td colspan="12" align="center" style="border:none;font-size:12px; font-weight:bold">
						<?
							if(str_replace("'","",trim($txt_date_from))!="" && str_replace("'","",trim($txt_date_to))!="")
							{
								echo "From $fromDate To $toDate" ;
							}
						?>
					</td>
				</tr>
			</table>
			<div >
			<table width="570" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" align="center">
				<thead>
					<tr>
					    <th width="40"><p>Sl</p></th>
						<th width="100"><p>Buyer</p></th>
						<th width="150"><p>Average of Order to Cut%</p></th>
						<th width="150"><p>Average of Order to Ship%</p></th>
						<th width="150"><p>Average of Cut to Ship%</p></th>

					 </tr>
				</thead>
			</table>
			<div style="max-height:330px;  width:550px" >
			
				<table border="1" class="rpt_table" width="570" rules="all">
				      <tbody>    
					  <?
					$i= 0;
					$gr_avg_total_order_cut=0;
					$gr_avg_total_order_ship=0;
					$gr_avg_total_order_ship_cut=0;

					foreach($buyer_summery as $buyer_name=>$rows)
					{
						$i++;
							?>
                            
							<tr bgcolor='<? echo $bgcolor; ?>' onclick="change_color('tr_2nd<? echo $i;?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i;?>">
								<td width="40"><p><?echo $i;?> </p></td>   
								<td width="100" valign="middle"><p><? echo $buyer_short_library[$buyer_name];?></p></td>
								<td width="150"align="right"><p><? $percent = ($rows['qty'] / $rows['order_qnty'] )*100; echo number_format($percent,2); ?>%</p></td>
								<td width="150" align="right"><p><? $percent_1 = ($rows['ex_qty'] / $rows['order_qnty'] )*100; echo number_format($percent_1,2); ?>%</p></p></td>
								<td width="150" align="right"><p><? $percent_2 = ( $rows['ex_qty'] / $rows['qty']  )*100;  echo  fn_number_format($percent_2,2); ?>%</p></td>

							</tr>     
							
							<?   
							
							$gr_avg_total_order_cut+=fn_number_format($percent,2);
							$gr_avg_total_order_ship+=fn_number_format($percent_1,2);
							$gr_avg_total_order_ship_cut+=fn_number_format($percent_2,2);
						}
						
						?>  
							
						</tbody>		
				</table>
				
				<table border="1" class="rpt_table" width="570" rules="all">
					<tfoot>
                            
                            <tr>
								<th width="40"><p></p></th>
								<th width="100"><p>Grand Total(Avg)</p></th>
								<th width="150" valign="middle"><p><?= fn_number_format(($gr_avg_total_order_cut /$i),2) ; ?>%</p></th>
								<th width="150" valign="middle"><p><?= fn_number_format(($gr_avg_total_order_ship /$i),2) ;?>%</p></th>
								<th width="150" valign="middle"><p><?= fn_number_format(($gr_avg_total_order_ship_cut/$i),2) ;?>%</p></th>
								
								

							</tr>     
					</tfoot>		
							
				</table>
		 	</div> 
				
			</div>
			</div>

			
		<?
	}
	else if($type==1) //Show 
	{
		$str_po_cond="";
		$str_po_cond="";
		if($cbo_style_owner_wise_status==1)
		{
			 $str_po_cond.=" and b.pub_shipment_date between '$txt_datefrom' and  '$txt_dateto' ";	
		}
		else
		{
			$ex_po_library=return_library_array( "select po_break_down_id,po_break_down_id as order_id from pro_ex_factory_mst where ex_factory_date between '$txt_datefrom' and  '$txt_dateto'", "po_break_down_id", "order_id"  );
			if(count($ex_po_library)>0)
			{
				$str_po_cond.=where_con_using_array($ex_po_library,0,'b.id');
			}
			else
			{
				?>
				<div class="alert alert-danger">Data Not Found</div>
				<?
				die;
			}	
		}
		//if($cbo_company_name!=0) $str_po_cond.=" and a.company_name=$cbo_company_name";
		if($cbo_buyer_name!=0) $str_po_cond.=" and a.buyer_name=$cbo_buyer_name";
		if($cbo_location>0) $str_po_cond.=" and a.location_name=$cbo_location";
		if($cbo_job_year>0)
		{
			if($db_type==0)
			{
				$str_po_cond .=" and year(a.insert_date)='$cbo_job_year'";
			}
			else
			{
				$str_po_cond .=" and to_char(a.insert_date,'YYYY')='$cbo_job_year'";
			}	
		}
		if($txt_style_ref_id!="")
		{
			$str_po_cond.=" and a.id in($txt_style_ref_id)";
		}
		else if($txt_style_ref!="")
		{
			$str_po_cond .=" and a.job_no_prefix_num in($txt_style_ref)";
		}
		
		/*if($txt_order_id!="")
		{
			$str_po_cond.=" and b.id in($txt_order_id)";
		}
		else if($txt_order_no!="")
		{
			$str_po_cond .=" and b.po_number in($poNumbers)";
		}*/

		$order_no_arr = explode(",", str_replace("'","",$txt_order));
		$order_id_arr = explode(",", str_replace("'","",$txt_order_id));
		$order_no_cond = "";
		$order_id_cond = "";
		$orderNo = "";
		$orderId = "";
		if(count($order_no_arr)==1)
		{
			foreach ($order_no_arr as $val) 
			{
				if($orderNo =="")
				{
					$orderNo .= "'%$val%'";
				}
			}
			$order_no_cond = "and b.po_number LIKE $orderNo";
		}
		else if(count($order_no_arr) > 1)
		{
			foreach ($order_no_arr as $val) 
			{
				if($orderNo =="")
				{
					$orderNo .= "'$val'";
				}
				else
				{
					$orderNo .= ","."'$val'";
				}
			}
			$order_no_cond = " and b.po_number in($orderNo)";
		}
		else
		{

		}
		// ====================================== for order id ==========================
		if(count($order_id_arr)==1)
		{
			foreach ($order_id_arr as $val) 
			{
				if($orderId =="")
				{
					$orderId .= "'%$val%'";
				}
			}
			$order_id_cond = "and b.id LIKE $orderId";
		}
		else if(count($order_id_arr) > 1)
		{
			foreach ($order_id_arr as $val) 
			{
				if($orderId =="")
				{
					$orderId .= "'$val'";
				}
				else
				{
					$orderId .= ","."'$val'";
				}
			}
			$order_id_cond = " and b.id in($orderId)";
		}
		else
		{

		}

		
		if($shipping_status!=0 && $shipping_status!=4)
		{
			$str_po_cond .=" and b.shiping_status='$shipping_status'"; 
		}
		else 
		{
			if($shipping_status==4)
			{
				$str_po_cond .=" and b.shiping_status in(1,2)";
			}
			else
			{
				$str_po_cond .=" and b.shiping_status in(1,2,3)";
			}
		}
		$internal_ref_cond_prod=(str_replace("'", "", $txt_internal_ref)=="")? "" : " and c.po_break_down_id in (select id from wo_po_break_down where grouping=$txt_internal_ref and status_active in(1,2,3) and is_deleted=0) "; 
		$internal_ref_cond_po=(str_replace("'", "", $txt_internal_ref)=="")? "" : " and b.grouping=$txt_internal_ref "; 

		//if($txt_datefrom!="" &&  $txt_dateto!="")  $str_po_cond .=" and b.pub_shipment_date between '$txt_datefrom' and '$txt_dateto'"; 
		if($db_type==0) $select_job_year="year(a.insert_date) as job_year"; else $select_job_year="to_char(a.insert_date,'YYYY') as job_year"; 

		// ====================================== MAIN QUERY ========================================	

		$order_sql="SELECT a.job_no_prefix_num,a.job_no, $select_job_year,b.id, b.po_number, b.po_quantity, b.unit_price, b.po_total_price, b.job_no_mst,b.file_no,b.grouping, b.pub_shipment_date as shipment_date, b.is_confirmed, b.shiping_status, b.excess_cut, b.plan_cut, a.company_name, a.style_owner, a.buyer_name, a.set_break_down, a.style_ref_no,c.color_number_id, c.size_number_id,c.item_number_id,c.size_order,c.article_number
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
		where b.job_id=a.id  and b.id=c.po_break_down_id and a.id=c.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 $str_po_cond $txt_style_ref_number_cond $internal_ref_cond_po $order_no_cond $order_id_cond
		order by b.id,c.size_order"; // and b.shiping_status != 3  and b.id IN (5382)
		//  echo $order_sql;die();		
		$sql_po_result=sql_select($order_sql);
		$main_array = array();
		if(count($sql_po_result)==0){ echo "<div style='color:red;font-size:18px;text-align:center;'>Data not available! please try again.</div>"; die();}
		foreach($sql_po_result as $row)
		{
			$all_po_id.=$row[csf("id")].",";
			$all_po_id_arr[$row[csf("id")]]=$row[csf("id")];
			$main_array[$row[csf("buyer_name")]][$row[csf("id")]]['style_ref_no']	  = $row[csf("style_ref_no")];
			$main_array[$row[csf("buyer_name")]][$row[csf("id")]]['po_number'] 	  = $row[csf("po_number")];
			
		}
		
        //  echo "<pre>";
		// print_r($main_array);
		// echo "</pre>"; die;
			

	    //$all_po_id=chop($all_po_id,",");

	    $all_po_ids=implode(",", array_unique($all_po_id_arr)); 
	    $all_po_id = $all_po_ids;
	    $all_po_ids_cond="";
 		if(count($all_po_id_arr)>0)
 		{
 			  if($db_type==2 && count($all_po_id_arr)>999)
			   {
			   		$chunk_arr=array_chunk($all_po_id_arr, 999);
			   		foreach($chunk_arr as $vals)
			   		{
			   			$po_ids=implode(",", $vals);
			   			if($all_po_ids_cond=="")
			   			{
			   				$all_po_ids_cond.=" and (c.order_id in ($po_ids)";
			   			}
			   			else
			   			{
			   				$all_po_ids_cond.=" or  c.order_id in ($po_ids)";
			   			}
			   		}
			   		$all_po_ids_cond.= " )";
			   }
			   else
			   {
			   		$all_po_ids_cond= " and c.order_id in($all_po_ids) ";
			   }

 		}
	 

 		if($all_po_id)
 		{
 			$template_id_arr=return_library_array("select po_number_id, template_id from tna_process_mst where po_number_id in ('".$all_po_id."') group by po_number_id, template_id","po_number_id","template_id");
 		}
 		
				
		$po_product_cond="";
		if($db_type==0)
		{
			if($all_po_id!="") $po_product_cond =" and c.po_break_down_id in($all_po_id)";
		}
		else
		{
			if($all_po_id!="")
			{
				$all_po_id_arr=array_chunk(array_unique(explode(",",$all_po_id)),999);
				$p=1;
				if(!empty($all_po_id_arr))
				{
					foreach($all_po_id_arr as $po_id)
					{
						if($p==1) $po_product_cond =" and (c.po_break_down_id in(".implode(',',$po_id).")"; else $po_product_cond .=" or c.po_break_down_id in(".implode(',',$po_id).")";
						$p++;
					}
					$po_product_cond .=" )";
				}
			}
		}
		// ================================== FOR EXFACTORY ========================================
		$ex_factory_arr=array();		
		$ex_fac_level=sql_select("SELECT ex_factory  from variable_settings_production where company_name=$cbo_company_name and variable_list=1 and status_active=1");
		if($ex_fac_level[0][csf("ex_factory")]==1)
		{
			$ex_factory_data=sql_select("SELECT c.po_break_down_id ,a.delivery_floor_id, c.item_number_id, MAX(c.ex_factory_date) AS ex_factory_date, sum(c.total_carton_qnty) as total_carton_qnty,
			sum(CASE WHEN c.entry_form!=85 THEN c.ex_factory_qnty ELSE 0 END)-sum(CASE WHEN c.entry_form=85 THEN c.ex_factory_qnty ELSE 0 END) as ex_factory_qnty 
			from pro_ex_factory_delivery_mst a,pro_ex_factory_mst c 	where a.id=c.delivery_mst_id and c.status_active=1 and c.is_deleted=0  $po_product_cond group by c.po_break_down_id, c.item_number_id,a.delivery_floor_id");
	
			foreach($ex_factory_data as $exRow)
			{
				$ex_factory_arr[$exRow[csf('po_break_down_id')]]['qty']=$exRow[csf('ex_factory_qnty')];
				$ex_factory_arr[$exRow[csf('po_break_down_id')]]['delivery_floor_id'].=$exRow[csf('delivery_floor_id')].",";

			}

		}
		else // color and size wise
		{
			$ex_factory_data="SELECT c.po_break_down_id , f.color_number_id,f.size_number_id, sum(c.total_carton_qnty) as total_carton_qnty,
			sum(CASE WHEN c.entry_form!=85 THEN d.production_qnty ELSE 0 END)-sum(CASE WHEN c.entry_form=85 THEN d.production_qnty ELSE 0 END) as ex_factory_qnty ,a.delivery_floor_id
			from pro_ex_factory_delivery_mst a, pro_ex_factory_mst c ,pro_ex_factory_dtls d,wo_po_break_down e ,wo_po_color_size_breakdown f 
			where    c.id=d.mst_id and c.po_break_down_id=e.id and e.id=f.po_break_down_id and f.id=d.color_size_break_down_id and a.id=c.delivery_mst_id and 
         	c.status_active=1 and c.is_deleted=0 and    d.status_active=1 and d.is_deleted=0 and      e.status_active in(1,2,3) and e.is_deleted=0 and f.status_active in(1,2,3)    $po_product_cond group by c.po_break_down_id , f.color_number_id,f.size_number_id,a.delivery_floor_id";
			// echo $ex_factory_data;die;
			$ex_factory_data_res = sql_select($ex_factory_data);
			foreach($ex_factory_data_res as $exRow)
			{				
				$ex_factory_arr[$exRow[csf('po_break_down_id')]]['ex_qty']+=$exRow[csf('ex_factory_qnty')];
				$ex_factory_arr[$exRow[csf('po_break_down_id')]]['delivery_floor_id'].=$exRow[csf('delivery_floor_id')].",";
				

			}
		}
		unset($ex_factory_data_res);
		// ========================================= FOR production ================================
		// if($db_type==0){$sql_clm=" group_concat(distinct(c.floor_id)) as floor_name";}
		// else{ $sql_clm="listagg(cast(CASE WHEN d.production_type ='8' THEN c.floor_id ELSE 0 END as varchar(4000)),',') within group(order by c.floor_id) as floor_name";}
		if($db_type==0)
		{
			
			$prod_sql="SELECT c.po_break_down_id, a.color_number_id,a.size_number_id,
				IFNULL(sum(CASE WHEN d.production_type ='1' THEN d.production_qnty ELSE 0 END),0) AS cutting_qnty,	
				
				IFNULL(sum(CASE WHEN d.production_type ='2' and c.embel_name=1 and c.production_source=1 THEN d.production_qnty ELSE 0 END),0) AS print_issue_inhouse,
				IFNULL(sum(CASE WHEN d.production_type ='3' and c.embel_name=1 and c.production_source=1 THEN d.production_qnty ELSE 0 END),0) AS print_rcv_inhouse,
				
				IFNULL(sum(CASE WHEN d.production_type ='2' and c.embel_name=2 and c.production_source=1 THEN d.production_qnty ELSE 0 END),0) AS emb_issue_inhouse,
				IFNULL(sum(CASE WHEN d.production_type ='3' and c.embel_name=2 and c.production_source=1 THEN d.production_qnty ELSE 0 END),0) AS emb_rcv_inhouse,				
				
				IFNULL(sum(CASE WHEN d.production_type ='4' and c.production_source=1 THEN d.production_qnty ELSE 0 END),0) AS sewingin_qnty_in,
				IFNULL(sum(CASE WHEN d.production_type ='5' and c.production_source=1 THEN d.production_qnty ELSE 0 END),0) AS sewingout_qnty_in,
				
				
				IFNULL(sum(CASE WHEN d.production_type ='8' and c.production_source=1 THEN d.production_qnty ELSE 0 END),0) AS finish_qnty_inhouse,
				IFNULL(sum(CASE WHEN d.production_type ='1' THEN d.reject_qty ELSE 0 END),0) AS cutting_rej_qnty,
				IFNULL(sum(CASE WHEN d.production_type in (1,2,4,3,5,7,8,11) and c.production_type in (1,2,4,3,5,7,8,11) THEN d.reject_qty ELSE 0 END),0) AS rej_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='8' and c.production_source=1 then c.carton_qty ELSE 0 END),0) as carton_qty_no_inhouse, 
				IFNULL(sum(CASE WHEN c.production_type ='8' and c.production_source=3 then c.carton_qty ELSE 0 END),0) as carton_qty_no_outbound,
				IFNULL(sum(CASE When d.production_type ='8' and c.production_type ='8' then c.carton_qty ELSE 0 END),0) as carton_total_qty
 
			from 
				pro_garments_production_mst c,pro_garments_production_dtls d,wo_po_color_size_breakdown a,wo_po_details_master e 
			where  c.id=d.mst_id  and c.po_break_down_id=a.po_break_down_id and a.job_no_mst=e.job_no  and a.id=d.color_size_break_down_id and c.status_active=1 and   c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and e.status_active=1 and e.is_deleted=0  $po_product_cond $internal_ref_cond_prod 
			group by c.po_break_down_id, a.color_number_id,a.size_number_id";
			
			$sql_carton_qty=sql_select("SELECT c.po_break_down_id,
			IFNULL(sum(case when c.production_type ='8' and c.production_source=1 then c.carton_qty else 0 end),0) as carton_qty_no_inhouse,
			IFNULL(sum(case when c.production_type ='8' and c.production_source=3 then c.carton_qty else 0 end),0) as carton_qty_no_outbound,
			IFNULL(sum(case when c.production_type ='8' then c.carton_qty else 0 end),0) as carton_total_qty
			from pro_garments_production_mst c where c.status_active=1 and c.is_deleted=0 $po_product_cond $internal_ref_cond_prod group by c.po_break_down_id");
		}
		else
		{	
					
			$producttion_cond =" and c.po_break_down_id in($po_id)";
			$prod_sql= "SELECT c.po_break_down_id,c.floor_id,d.production_type,
				NVL(sum(CASE WHEN d.production_type ='1'  and c.production_type ='1' THEN d.production_qnty ELSE 0 END),0) AS cutting_qnty,
				NVL(sum(CASE WHEN d.production_type ='1' THEN d.reject_qty ELSE 0 END),0) AS cutting_rej_qnty,
				
				NVL(sum(CASE WHEN d.production_type ='2' and c.embel_name=1 THEN d.production_qnty ELSE 0 END),0) AS print_issue_inhouse,
				NVL(sum(CASE WHEN d.production_type ='3' and c.embel_name=1 THEN d.production_qnty ELSE 0 END),0) AS print_rcv_inhouse,
				NVL(sum(CASE WHEN d.production_type ='3' and c.embel_name=1 THEN d.reject_qty ELSE 0 END),0) AS print_rej_inhouse,
				
				NVL(sum(CASE WHEN d.production_type ='2' and c.embel_name=2 THEN d.production_qnty ELSE 0 END),0) AS emb_issue_inhouse,
				NVL(sum(CASE WHEN d.production_type ='3' and c.embel_name=2  THEN d.production_qnty ELSE 0 END),0) AS emb_rcv_inhouse,
				
				NVL(sum(CASE WHEN d.production_type ='4'  and c.production_type ='4' and c.production_source=1 THEN d.production_qnty ELSE 0 END),0) AS sewingin_qnty_in,
				NVL(sum(CASE WHEN d.production_type ='5'  and c.production_type ='5' and c.production_source=1 THEN d.production_qnty ELSE 0 END),0) AS sewingout_qnty_in,
											
				NVL(sum(CASE WHEN d.production_type in (1,3,5,7,8,11) and c.production_type in (1,3,5,7,8,11) THEN d.reject_qty ELSE 0 END),0) AS rej_qnty,				
				NVL(sum(CASE WHEN d.production_type ='8' THEN d.production_qnty ELSE 0 END),0) AS finish_qnty_inhouse
  
			from 
				pro_garments_production_mst c,pro_garments_production_dtls d,wo_po_color_size_breakdown a,wo_po_details_master e 
			where  c.id=d.mst_id     and a.job_id=e.id  and a.id=d.color_size_break_down_id and 
				c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and e.status_active=1 and e.is_deleted=0  $po_product_cond $internal_ref_cond_prod 
				group by c.po_break_down_id,c.floor_id,d.production_type";//and c.po_break_down_id IN (5382)
				
			
			
		}
      //echo $prod_sql;die;
		$gmts_prod_arr=array();
		$res_gmtsData=sql_select($prod_sql);
		foreach($res_gmtsData as $gmtsRow)
		{

			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]]['sQty']+=$gmtsRow[csf('sewingin_qnty_in')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]]['soQty']+=$gmtsRow[csf('sewingout_qnty_in')];
			if($gmtsRow[csf('production_type')]==8)	
			{
				$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]]['floor_name']=$gmtsRow[csf('floor_id')];	
			}
			
		}
		//echo "<pre>";print_r($gmts_prod_arr);echo "</pre>"; die;
		unset($res_gmtsData);

		//echo $prod_sql; die;
		// ============================================ FOR CUTTING =====================================
		$sql_cutlay="SELECT a.entry_form,b.order_id as po_id,b.color_id,c.order_id,c.size_id,sum(c.size_qty ) as qty  from ppl_cut_lay_mst a ,ppl_cut_lay_dtls b,ppl_cut_lay_bundle c where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and    c.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$cbo_company_name $all_po_ids_cond  group by a.entry_form,b.order_id,c.order_id,b.color_id,c.size_id";
		$sql_res=sql_select($sql_cutlay);
		foreach($sql_res as $vals)
		{

			$entry_form=$vals[csf("entry_form")];
			if($entry_form!=76)
			{
				$cut_lay_arrs[$vals[csf("order_id")]]+=$vals[csf("qty")];
			}
			else
			{
				$cut_lay_arrs[$vals[csf("order_id")]]+=$vals[csf("qty")];
			}
		}		
		// echo "<pre>";
		// print_r($cut_lay_arrs);
		// echo "</pre>";
			
		// ====================================== FOR TRANSFER ===================================

		$po_transfer_in_sql=sql_select("select to_po_id,production_quantity,item_number_id from pro_gmts_delivery_dtls where status_active=1 and is_deleted=0");
		foreach($po_transfer_in_sql as $vals)
		{
			$po_transfer_in_arr[$vals[csf("to_po_id")]][$vals[csf("item_number_id")]]+=$vals[csf("production_quantity")];
		}

		$po_transfer_out_sql=sql_select("select from_po_id,production_quantity,item_number_id from pro_gmts_delivery_dtls where status_active=1 and is_deleted=0");
		foreach($po_transfer_out_sql as $vals)
		{
			$po_transfer_out_arr[$vals[csf("from_po_id")]][$vals[csf("item_number_id")]]+=$vals[csf("production_quantity")];
		}
		//echo "<pre>";print_r($po_transfer_in_arr);echo "</pre>";
		//echo "<pre>";print_r($po_transfer_out_arr);echo "</pre>";die;
		unset($po_transfer_in_sql);
		unset($po_transfer_out_sql);
		// ==================================== FOR COLOR AND SIZE WISE ORDER QNTY ==================================
		$order_qnty_sql = "SELECT b.id as po_id,c.color_number_id,c.size_number_id, sum(c.order_quantity) as order_qnty 
		FROM wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
		WHERE a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id $po_product_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0
		GROUP BY b.id,c.color_number_id,c.size_number_id";
		//	echo $order_qnty_sql; die;
		$order_qnty_sql_res = sql_select($order_qnty_sql);
		$colo_size_ord_qnty_arr = array();
		foreach ($order_qnty_sql_res as  $row) 
		{
			$colo_size_ord_qnty_arr[$row[csf('po_id')]] += $row[csf('order_qnty')];
		}
		// echo "<pre>";
		// print_r($colo_size_ord_qnty_arr);
		// echo "</pre>"; die;

	
	
		$fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
		$toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );
		ob_start();
  		?>
  		
            
		<div>
			<table width="1580" cellspacing="0" >
				<tr style="border:none;">
					<td colspan="20" align="center" style="border:none; font-size:16px;">
						Company Name : <? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>                                
					</td>
				</tr>
				<tr class="form_caption" style="border:none;">
					<td colspan="20" align="center" style="border:none;font-size:14px; font-weight:bold" >
						<? echo "Order Wise RMG Production Status"; ?>    
					</td>
				</tr>
				<tr style="border:none;">
					<td colspan="20" align="center" style="border:none;font-size:12px; font-weight:bold">
						<?
							if(str_replace("'","",trim($txt_date_from))!="" && str_replace("'","",trim($txt_date_to))!="")
							{
								echo "From $fromDate To $toDate" ;
							}
						?>
					</td>
				</tr>
			</table>
			<?
			$gr_order_qnty 		= 0;
			$gr_cutting_qnty 	= 0;
			$gr_order_cut       = 0;
			$gr_cut_loss         =0;
			$gr_input_qnty 		= 0;
			$gr_order_input		= 0;
			$gr_order_input_loss = 0; 
			$gr_output_qnty 	= 0;
			$gr_order_output 	= 0;
			$gr_order_output_loss = 0;
			$gr_export_qnty 	= 0;
			$gr_order_export 	= 0;
			$gr_order_cut_export = 0;
		
			foreach ($main_array as $buyer_name => $buyer_data) 
			{					
				// initialize po wise sub total	
				foreach ($buyer_data as $po_id => $row) 
				{
								$order_qnty 	= $colo_size_ord_qnty_arr[$po_id];
								$cut_qnty 		= $cut_lay_arrs[$po_id];
								$sewing_in 		= $gmts_prod_arr[$po_id]['sQty'];
								$sewing_out 	= $gmts_prod_arr[$po_id]['soQty'];
								$ex_fact_qnty 	= $ex_factory_arr[$po_id]['ex_qty'];
								$cut_loss 	    = (($cut_qnty - $sewing_in)/$order_qnty)*100;
								$sewing_in_loss = (($sewing_in - $sewing_out)/$order_qnty)*100;
								$sewing_out_loss = (($sewing_out - $ex_fact_qnty)/$order_qnty)*100;

								
								$gr_order_qnty 		+= $order_qnty;
								$gr_cutting_qnty 	+= $cut_qnty;
								$gr_cut_loss        += (($cut_qnty - $sewing_in)/$order_qnty)*100;
								$gr_order_cut       += ($cut_qnty / $order_qnty)*100;
								$gr_input_qnty 		+= $sewing_in;
								$gr_order_input     += ($sewing_in / $order_qnty)*100;
								$gr_order_input_loss += (($sewing_in - $sewing_out)/$order_qnty)*100;
								$gr_output_qnty 	+= $sewing_out;
								$gr_order_output 	+= ($sewing_out / $order_qnty)*100;
								$gr_order_output_loss 	+=  (($sewing_out - $ex_fact_qnty)/$order_qnty)*100;
								$gr_export_qnty 	+= $ex_fact_qnty;
								$gr_order_export 	+= ($ex_fact_qnty / $order_qnty)*100;
								$gr_order_cut_export += ($ex_fact_qnty/$cut_qnty)*100;
								
				}
			}		
			 ?>


			 <table width="1830" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
				
				<thead>
					<tr>
						<th width="30" rowspan="2"><p>SL</p></th>   
						<th width="60" rowspan="2"><p> Buyer Name</p></th>
						<th width="60" rowspan="2"><p>Style</p></th>
						<th width="60" rowspan="2"><p>PO</p></th>
						<th style="text-align: right;" width="50"><p></p> <?=$gr_order_qnty; ?></p></th>
						<th style="text-align: right;"  width="50"><p><?=$gr_cutting_qnty;?></p></th>
						<th style="text-align: right;" width="50"><p><?=number_format((($gr_cutting_qnty /  $gr_order_qnty)*100),2)?>%</p></th>
						<th style="text-align: right;"  width="50"><p><?=number_format(((($gr_cutting_qnty - $gr_input_qnty) / $gr_order_qnty)*100), 2)  ;?>%</p></th>
						<th style="text-align: right;" width="50"><p><?=$gr_input_qnty ;?></th>
						<th style="text-align: right;" width="50"><p><?=number_format((($gr_input_qnty /  $gr_order_qnty)*100),2) ;?>%</p></th>
						<th style="text-align: right;"  width="50"><p><?=number_format(((($gr_input_qnty - $gr_output_qnty) / $gr_order_qnty)*100), 2);?>%</p></th>
						<th  style="text-align: right;" width="50"><p><?=	$gr_output_qnty ;?></th>
						<th style="text-align: right;" width="50"><p><?=number_format(((($gr_output_qnty - $gr_export_qnty) / $gr_order_qnty)*100), 2);?>%</p></th>
						<th style="text-align: right;" width="50"><p><?=number_format((($gr_output_qnty /  $gr_order_qnty)*100),2);?>%</p></th>
						<th  style="text-align: right;" width="50"><p><?=$gr_export_qnty;?></p></th>
						<th style="text-align: right;" width="50"><p><?=number_format((($gr_export_qnty /  $gr_order_qnty)*100),2);?>%</p></th>
						<th  style="text-align: right;" width="50"><p><?=fn_number_format((($gr_export_qnty / $gr_cutting_qnty)*100), 2);?>%</p></th>
						<th  width="50"></th>
						<th  width="50"></th>


					</tr>
					<tr>
						
						<th width="50"><p>Order Qty.</p> </th>
						<th width="50"><p>Cutting</p></th>
						<th width="50"><p>Order to Cutting%</p></th>                        
                        <th width="50"><p>Cutting Loss</p></th>
						<th width="50"><p>Sew. Input</p></th>
						<th width="50"><p>Order to Input %</p></th>
						<th width="50"><p>Sewing  Loss </p>%</th>
						<th width="50"><p>Sewing Output</p></th>
						<th width="50"><p>Finning Loss/Leftover</p>%</th>
						<th width="50"><p>Order to Sew. Output %</p></th>
						<th width="50"><p>Shipment Qty </p></th>
						<th width="50"><p>Order to Shipment %</p></th>
						<th width="50"><p>Cut to Ship %</p></th>
						<th width="50"><p>Factory(Finishing) </p></th>
						<th width="50"><p>Remarks</p></th>

						
					 </tr>
				</thead>
			</table>
			
			<div style="max-height:330px; overflow-y:scroll; width:1850px" id="scroll_body">
				<table border="1" class="rpt_table" width="1830" rules="all" id="table_body">
					<?
					$s=1;
					// initialize grand total
					$gr_order_qnty 		= 0;
					$gr_cutting_qnty 	= 0;
					$gr_cut_bal_qnty 	= 0;
					$gr_cut_qc_qnty 	= 0;
					$gr_cut_rej_qnty 	= 0;
					$gr_printS_qnty 	= 0;
					$gr_printR_qnty 	= 0;
					$gr_print_rej_qnty 	= 0;
					$gr_embS_qnty 		= 0;
					$gr_embR_qnty 		= 0;
					$gr_input_qnty 		= 0;
					$gr_input_bal_qnty 	= 0;
					$gr_output_qnty 	= 0;
					$gr_out_bal_qnty 	= 0;
					$gr_pac_fin_qnty 	= 0;
					$gr_pac_fin_bal_qnty= 0;
					$gr_export_qnty 	= 0;
					$gr_export_bal_qnty = 0;
					foreach ($main_array as $buyer_name => $buyer_data) 
					{					
						// initialize po wise sub total	
						foreach ($buyer_data as $po_id => $row) 
						{
							// initialize color wise sub total
							
								
								$bgcolor = ($s%2==0) ? "#ffffff" : "#ffffff";//#e8f6ff

								
								$order_qnty 	= $colo_size_ord_qnty_arr[$po_id];
								$cut_qnty 		= $cut_lay_arrs[$po_id];
								$sewing_in 		= $gmts_prod_arr[$po_id]['sQty'];
								$sewing_out 	= $gmts_prod_arr[$po_id]['soQty'];
								$cut_loss 	    = (($cut_qnty - $sewing_in)/$order_qnty)*100;
								// echo $row['po_number']."==((".$cut_qnty." - ".$sewing_in.")/".$order_qnty.")*100<br>";
								$floor_arr 	   	= array_filter(explode(',',$ex_factory_arr[$po_id]['delivery_floor_id']));

								$ex_fact_qnty 	= $ex_factory_arr[$po_id]['ex_qty'];
								$sewing_in_loss = (($sewing_in - $sewing_out)/$order_qnty)*100;
								$sewing_out_loss = (($sewing_out - $ex_fact_qnty)/$order_qnty)*100;
							
								
								$floor_id=array_unique($floor_arr);
								$floor_name="";
								foreach($floor_id as $val)
								{
									if($floor_name!=""){
										$floor_name.=" , ";	
									}
									$floor_name.=$floor_library[$val];
									//echo $val;
								}
								//print_r($floor_id);die;
								// grand total increment
								//echo $floor_name;die;
								
								$gr_order_qnty 		+= $order_qnty;
								$gr_cutting_qnty 	+= $cut_qnty;
								$gr_input_qnty 		+= $sewing_in;
								$gr_output_qnty 	+= $sewing_out;
								$gr_export_qnty 	+= $ex_fact_qnty;
							
								?>
                                <tr bgcolor='<? echo $bgcolor; ?>' onclick="change_color('tr_2nd<? echo $s;?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $s;?>">
								<?
								
                                    ?>
                                    <td valign="middle"  width="30"><p><? echo $s;?></p></td>
									<td valign="middle"  width="60"><p><? echo $buyer_short_library[$buyer_name]; ?></p></td>
									<td valign="middle"  width="60"><p><? echo $row['style_ref_no']; ?></p></td>
                                    <td valign="middle"  width="60"><p><? echo $row['po_number']; ?></p></td>
                                    <td  width="50" align="right"><p><? echo number_format($order_qnty,0); ?></p></td>
                                    <td  width="50" align="right"><p><? echo number_format($cut_qnty,0); ?></p></td>
                                    <td width="50" align="right"><p><? $percent = ($cut_qnty / $order_qnty)*100; echo number_format($percent,2); ?>%</p></td>
                                    <td width="50" align="right"><p><? echo  number_format($cut_loss,2); ?>%</p></td>
                                    <td width="50" align="right"><p><? echo number_format($sewing_in,0); ?></p></td>
									<td  width="50" align="right"><p><? $percent = ($sewing_in / $order_qnty)*100; echo number_format($percent,2) ?>%</p></td>
									<td  width="50" align="right"><p><?  echo  number_format($sewing_in_loss,2); ?>%</p></td>
                                    <td width="50" align="right"><p><? echo number_format($sewing_out,0); ?></p></td>
                            
                                    <td width="50" align="right"><p><? echo number_format($sewing_out_loss,2); ?>%</p></td>
									<td width="50" align="right"><p><? $percent = ($sewing_out / $order_qnty)*100; echo number_format($percent,2) ?>%</p></td>
                                    <td  width="50" align="right"><p><? echo number_format($ex_fact_qnty,0); ?></p></td>						
                        			<td width="50" align="right"><p><?$percent = ($ex_fact_qnty / $order_qnty)*100; echo number_format($percent,2);?>%</p></td>
									<td width="50" align="right"><p><?$percent = ($ex_fact_qnty/$cut_qnty)*100; echo fn_number_format($percent,2);?>%</p></td>
									<td width="50" align="left"><p><? echo $floor_name;?></p></td>
									<td width="50" align="left"><p></p></td>
                                </tr>
								<?
								$s++;
                                
						}
						
    
                    }
                    ?>
				</table>
		 	</div>   	
			<div>
	
			
			
		</div>
		<?
	}

	/*$html = ob_get_contents();
	ob_clean();
	$new_link=create_delete_report_file( $html, 1, 1, "../../../" );
	
	echo "$html**$type";
	exit();	*/
	foreach (glob("$user_id*.xls") as $filename)
	{		
		@unlink($filename);

	}
	$name=$user_id.'_'.time().".xls";
	$create_new_excel = fopen($name, 'w');	
	$report_data=ob_get_contents();
	ob_clean();
	$is_created = fwrite($create_new_excel,$report_data);
	echo $report_data."**".$name;
	exit();
}
?>