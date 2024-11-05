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
	echo create_drop_down( "cbo_location", 120, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/date_wise_production_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/date_wise_production_report_controller' );",0 );
	exit();  	 
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor", 110, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select --", $selected, "",0 ); 
	exit();   	 
}
if ($action == "eval_multi_select")
{
	echo "set_multiselect('cbo_floor','0','0','','0');\n";
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
		var style_no = new Array;
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
					style_no.push( str );				
				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == selectID ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 );
					selected_no.splice( i, 1 ); 
					style_no.splice( i, 1 ); 
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
	echo create_list_view("list_view", "Style Ref No,Job No,Year","160,90,100","400","310",0, $sql , "js_set_value", "id,style_ref_no", "", 1, "0", $arr, "style_ref_no,job_no_prefix_num,year", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	
	?>
    <script language="javascript" type="text/javascript">
	
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
if($action=="job_no_sreach")
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
				//alert(id);
				$('#txt_selected_id').val( id );
				$('#txt_selected').val( name ); 
				$('#txt_selected_no').val( name ); 
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
	$txt_job_no_id=str_replace("'","",$txt_job_no_id);
	$txt_order_no=str_replace("'","",$txt_order);
	$txt_order_id=str_replace("'","",$txt_order_id);
	$shipping_status=str_replace("'","",$shipping_status);
	
	$color_Arr_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );	
	$size_Arr_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );	
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$buyer_short_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name"  );
	$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
	$floor_arr=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  );
	$sewing_line_arr=return_library_array( "select id,line_name from LIB_SEWING_LINE", "id", "line_name"  );
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	//print_r($floor_arr) ;die;	
		
	if($type==0) //Show 
	{
	
		$str_po_cond="";		
		// if($cbo_company_name!=0) $str_po_cond.=" and a.company_name=$cbo_company_name";
		
		
		if($cbo_buyer_name!=0) $str_po_cond.=" and a.buyer_name=$cbo_buyer_name";
		// if($cbo_location>0) $str_po_cond.=" and a.location_name=$cbo_location";
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

		if ($txt_style_ref!="")
		{
			$txt_style_ref_cond="and a.style_ref_no='$txt_style_ref'";
		}
		else	
		{
			$txt_style_ref_number_cond="";
		}	 

		if($txt_job_no_id!="")
		{
			$job_cond.=" and a.id in($txt_job_no_id)";
		}
		else if($txt_job_no_id!="")
		{
			$job_cond .=" and a.job_no_prefix_num in($txt_job_no_id)";
		}
		
		/*if($txt_order_id!="")
		{
			$str_po_cond.=" and b.id in($txt_order_id)";
		}
		else if($txt_order_no!="")
		{
			$str_po_cond .=" and b.po_number in($poNumbers)";
		}*/
		
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
			if($cbo_location!="0")
			{
				$location =" and location =$cbo_location";
				
			}
			if( $cbo_floor!="")
			{
			
				$floor =" and floor_id in($cbo_floor)";
			}
			
		
			// =================== get prod po =====================
			$prod_po_arr = return_library_array("select po_break_down_id,po_break_down_id from pro_garments_production_mst where status_active=1  and serving_company=$cbo_company_name and production_date between '$txt_datefrom' and '$txt_dateto' $location $floor","po_break_down_id","po_break_down_id");
			// echo "select po_break_down_id,po_break_down_id from pro_garments_production_mst where status_active=1  and serving_company=$cbo_company_name and production_date between '$txt_datefrom' and '$txt_dateto' $location $floor";die;
			
			// =================== get ex po =====================
			// $ex_po_arr = return_library_array("select po_break_down_id,po_break_down_id from PRO_EX_FACTORY_MST where status_active=1 and EX_FACTORY_DATE between '$txt_datefrom' and '$txt_dateto' ","po_break_down_id","po_break_down_id");
			$prod_po_cond = where_con_using_array($prod_po_arr,0,"b.id");
	 	}
       
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

		// if($txt_datefrom!="" &&  $txt_dateto!="")  $str_po_cond .=" and b.pub_shipment_date between '$txt_datefrom' and '$txt_dateto'"; 
		if($db_type==0) $select_job_year="year(a.insert_date) as job_year"; else $select_job_year="to_char(a.insert_date,'YYYY') as job_year"; 

		// ====================================== MAIN QUERY ========================================	

		$order_sql="SELECT a.job_no_prefix_num,a.job_no, $select_job_year,b.id, b.po_number, b.po_quantity, b.unit_price, b.po_total_price, b.job_no_mst,b.file_no,b.grouping, b.pub_shipment_date as shipment_date, b.is_confirmed, b.shiping_status, b.excess_cut, b.plan_cut, a.company_name, a.style_owner, a.buyer_name, a.set_break_down, a.style_ref_no,c.color_number_id, c.size_number_id,c.item_number_id,c.size_order,c.article_number,c.country_id
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
		where a.id=b.job_id  and b.id=c.po_break_down_id and a.id=c.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 $str_po_cond $txt_style_ref_cond $internal_ref_cond_po $order_no_cond $order_id_cond $prod_po_cond $job_cond
		order by b.id,c.size_order"; // and b.shiping_status != 3
		//echo $order_sql;die();		
		
		$sql_po_result=sql_select($order_sql);
		$main_array = array();
		// if(count($sql_po_result)==0){ echo "<div style='color:red;font-size:18px;text-align:center;'>Data not available! please try again.</div>"; die();}
		//echo "777";die;
		foreach($sql_po_result as $row)
		{
			$all_po_id.=$row[csf("id")].",";
			$all_po_id_arr[$row[csf("id")]]=$row[csf("id")];
			$main_array[$row[csf("id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['buyer_name']	  = $row[csf("buyer_name")];
			$main_array[$row[csf("id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['style_ref_no']	  = $row[csf("style_ref_no")];
			$main_array[$row[csf("id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['po_number'] 	  = $row[csf("po_number")];
			$main_array[$row[csf("id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['item_number_id'][] = $row[csf("item_number_id")];
			$main_array[$row[csf("id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['article_number'] = $row[csf("article_number")];
			$main_array[$row[csf("id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['job_no'] = $row[csf("job_no")];
			$main_array[$row[csf("id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['shipment_date'] = $row[csf("shipment_date")];
			$main_array[$row[csf("id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['country_id'] = $row[csf("country_id")];
		}
		//echo"<pre>";print_r($main_array);die;
		// ======================================= FOR ROWSPAN ==============================
		$rowSpanArray = array();
		foreach($main_array as $po_id=>$po_data)
		{
			foreach($po_data as $color_id=>$color_data)
			{
				foreach($color_data as $size_id=>$row)
				{
					$rowSpanArray[$po_id][$color_id]++;
				}
			}
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
				$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('color_number_id')]][$exRow[csf('size_number_id')]]['ex_qty']=$exRow[csf('ex_factory_qnty')];
				$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('color_number_id')]][$exRow[csf('size_number_id')]]['ex_fac_carton_qty']+=$exRow[csf('total_carton_qnty')];

			}
		}
		unset($ex_factory_data_res);
		// ========================================= FOR production ================================

	
		if($cbo_location>0) $location_cond.=" and c.location=$cbo_location";
		if($cbo_floor!="") $floor_cond.=" and c.floor_id in($cbo_floor)";

		if ($txt_datefrom!="" &&  $txt_dateto!="") $date_con = "and c.production_date between '".change_date_format($txt_datefrom, "yyyy-mm-dd", "-",1)."' and '".change_date_format($txt_dateto, "yyyy-mm-dd", "-",1)."'"; else $date_con ="";

		
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
			$prod_sql= "SELECT c.po_break_down_id, a.color_number_id,a.size_number_id, ( case when c.production_date ='$txt_dateto' then c.floor_id else 0 end ) as floor_id,( case when c.production_date ='$txt_dateto' then c.sewing_line else 0 end ) as sewing_line,c.production_type,c.PROD_RESO_ALLO,
				NVL(sum(CASE WHEN d.production_type ='1'  and c.production_type ='1' THEN d.production_qnty ELSE 0 END),0) AS cutting_qnty,
				NVL(sum(CASE WHEN d.production_type ='1'  and c.production_date ='$txt_dateto' THEN d.production_qnty ELSE 0 END),0) AS today_cutting_qnty,
				NVL(sum(CASE WHEN d.production_type ='1' THEN d.reject_qty ELSE 0 END),0) AS cutting_rej_qnty,
				
				NVL(sum(CASE WHEN d.production_type ='2' and c.embel_name=1 THEN d.production_qnty ELSE 0 END),0) AS print_issue_inhouse,
				NVL(sum(CASE WHEN d.production_type ='3' and c.embel_name=1 THEN d.production_qnty ELSE 0 END),0) AS print_rcv_inhouse,
				NVL(sum(CASE WHEN d.production_type ='3' and c.embel_name=1 THEN d.reject_qty ELSE 0 END),0) AS print_rej_inhouse,
				
				NVL(sum(CASE WHEN d.production_type ='2' and c.embel_name=2 THEN d.production_qnty ELSE 0 END),0) AS emb_issue_inhouse,
				NVL(sum(CASE WHEN d.production_type ='3' and c.embel_name=2  THEN d.production_qnty ELSE 0 END),0) AS emb_rcv_inhouse,
				NVL(sum(CASE WHEN d.production_type ='3' and c.embel_name=2  THEN d.reject_qty ELSE 0 END),0) AS emb_rej,
				
				NVL(sum(CASE WHEN d.production_type ='4'  and c.production_type ='4' and c.production_source=1 THEN d.production_qnty ELSE 0 END),0) AS sewingin_qnty_in,
				NVL(sum(CASE WHEN d.production_type ='4'  and c.production_date ='$txt_dateto' and c.production_source=1 THEN d.production_qnty ELSE 0 END),0) AS today_sewingin_qnty,
				NVL(sum(CASE WHEN d.production_type ='5'  and c.production_type ='5' and c.production_source=1 THEN d.production_qnty ELSE 0 END),0) AS sewingout_qnty_in,
				NVL(sum(CASE WHEN d.production_type ='7'  and c.production_type ='7' and c.production_source=1 THEN d.production_qnty ELSE 0 END),0) AS iron_in,
				NVL(sum(CASE WHEN d.production_type ='11'  and c.production_type ='11' and c.production_source=1 THEN d.production_qnty ELSE 0 END),0) AS poly_in,
											
				NVL(sum(CASE WHEN d.production_type in (1,3,5,7,8,11) and c.production_type in (1,3,5,7,8,11) THEN d.reject_qty ELSE 0 END),0) AS rej_qnty,				
				NVL(sum(CASE WHEN d.production_type ='8' THEN d.production_qnty ELSE 0 END),0) AS finish_qnty_inhouse
  
			from 
				pro_garments_production_mst c,pro_garments_production_dtls d,wo_po_color_size_breakdown a,wo_po_details_master e 
			where  c.id=d.mst_id     and a.job_id=e.id  and a.id=d.color_size_break_down_id and 
				c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and e.status_active=1 and e.is_deleted=0  and c.serving_company=$cbo_company_name $po_product_cond $internal_ref_cond_prod $location_cond $floor_cond 
				group by c.po_break_down_id, a.color_number_id,a.size_number_id,c.floor_id,c.sewing_line,c.production_type,c.PROD_RESO_ALLO,c.production_date";
				
			$sql_carton_qty=sql_select("SELECT c.po_break_down_id,
			NVL(sum(case when c.production_type ='8' and c.production_source=1 then c.carton_qty else 0 end),0) as carton_qty_no_inhouse,
			NVL(sum(case when c.production_type ='8' and c.production_source=3 then c.carton_qty else 0 end),0) as carton_qty_no_outbound,
			NVL(sum(case when c.production_type ='8' then c.carton_qty else 0 end),0) as carton_total_qty
			from pro_garments_production_mst c where c.status_active=1 and c.is_deleted=0 $po_product_cond $internal_ref_cond_prod group by c.po_break_down_id");
			
		}
 		// echo $prod_sql; die;
		$gmts_prod_arr=array();
		$res_gmtsData=sql_select($prod_sql);
		foreach($res_gmtsData as $gmtsRow)
		{
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('color_number_id')]][$gmtsRow[csf('size_number_id')]]['cQty']+=$gmtsRow[csf('cutting_qnty')];//+$gmtsRow[csf('replace_qty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('color_number_id')]][$gmtsRow[csf('size_number_id')]]['cutting_rej_qnty']=+$gmtsRow[csf('cutting_rej_qnty')];


			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('color_number_id')]][$gmtsRow[csf('size_number_id')]]['print_issue_inhouse']+=$gmtsRow[csf('print_issue_inhouse')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('color_number_id')]][$gmtsRow[csf('size_number_id')]]['print_rcv_inhouse']+=$gmtsRow[csf('print_rcv_inhouse')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('color_number_id')]][$gmtsRow[csf('size_number_id')]]['print_rej_inhouse']+=$gmtsRow[csf('print_rej_inhouse')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('color_number_id')]][$gmtsRow[csf('size_number_id')]]['emb_issue_inhouse']+=$gmtsRow[csf('emb_issue_inhouse')];

			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('color_number_id')]][$gmtsRow[csf('size_number_id')]]['emb_rcv_inhouse']+=$gmtsRow[csf('emb_rcv_inhouse')];
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('color_number_id')]][$gmtsRow[csf('size_number_id')]]['sQty']+=$gmtsRow[csf('sewingin_qnty_in')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('color_number_id')]][$gmtsRow[csf('size_number_id')]]['soQty']+=$gmtsRow[csf('sewingout_qnty_in')];	

			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('color_number_id')]][$gmtsRow[csf('size_number_id')]]['finish_qnty_inhouse']+=$gmtsRow[csf('finish_qnty_inhouse')];
            if($gmtsRow[csf('production_type')]==4)
			{
				$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('color_number_id')]][$gmtsRow[csf('size_number_id')]]['floor_id'].=$gmtsRow[csf('floor_id')].",";	
			}
			

			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('color_number_id')]][$gmtsRow[csf('size_number_id')]]['emb_rej']+=$gmtsRow[csf('emb_rej')];	
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('color_number_id')]][$gmtsRow[csf('size_number_id')]]['iron_in']+=$gmtsRow[csf('iron_in')];	
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('color_number_id')]][$gmtsRow[csf('size_number_id')]]['poly_in']+=$gmtsRow[csf('poly_in')];	
			if($gmtsRow[csf('production_type')]==4)
			{
				$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('color_number_id')]][$gmtsRow[csf('size_number_id')]]['sewing_line'] .= $gmtsRow[csf('sewing_line')]."___".$gmtsRow[csf('prod_reso_allo')]."==";
			}
			
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('color_number_id')]][$gmtsRow[csf('size_number_id')]]['today_cutting_qnty']+=$gmtsRow[csf('today_cutting_qnty')];
			$gmts_prod_arr[$gmtsRow[csf('po_break_down_id')]][$gmtsRow[csf('color_number_id')]][$gmtsRow[csf('size_number_id')]]['today_sewingin_qnty']+=$gmtsRow[csf('today_sewingin_qnty')];
			
			
		}
		// echo "<pre>";print_r($gmts_prod_arr);echo "</pre>"; die;
		unset($res_gmtsData);

		$carton_arr=array();
		foreach($sql_carton_qty as $crtn)
		{
		  $carton_arr[$crtn[csf("po_break_down_id")]]["carton_qty_no_inhouse"]=$crtn[csf("carton_qty_no_inhouse")];
		  $carton_arr[$crtn[csf("po_break_down_id")]]["carton_qty_no_outbound"]=$crtn[csf("carton_qty_no_outbound")];
		  $carton_arr[$crtn[csf("po_break_down_id")]]["carton_total_qty"]=$crtn[csf("carton_total_qty")];
		}
		//echo $prod_sql; die;
		if($cbo_location>0) $location_cond_lay.=" and a.location_id=$cbo_location";
		if($cbo_floor!="") $floor_cond_lay.=" and a.floor_id in($cbo_floor)";

		if ($txt_datefrom!="" &&  $txt_dateto!="") $date_con_lay = "and a.entry_date between '".change_date_format($txt_datefrom, "yyyy-mm-dd", "-",1)."' and '".change_date_format($txt_dateto, "yyyy-mm-dd", "-",1)."'"; else $date_con_lay ="";
		// ============================================ FOR CUTTING =====================================
		$sql_cutlay="SELECT a.entry_form,b.order_id as po_id,b.color_id,c.order_id,c.size_id,sum(case when a.entry_date='$txt_dateto' then c.size_qty else 0 end ) as today_lay_qty,sum(c.size_qty ) as tot_lay_qty  from ppl_cut_lay_mst a ,ppl_cut_lay_dtls b,ppl_cut_lay_bundle c where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and    c.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.working_company_id=$cbo_company_name $all_po_ids_cond $location_cond_lay $floor_cond_lay  group by a.entry_form,b.order_id,c.order_id,b.color_id,c.size_id";
		// echo $sql_cutlay;die;
		$sql_res=sql_select($sql_cutlay);		
		
		foreach($sql_res as $vals)
		{

			$entry_form=$vals[csf("entry_form")];
			if($entry_form!=76)
			{
				$cut_lay_arrs[$vals[csf("order_id")]][$vals[csf("color_id")]][$vals[csf("size_id")]]['today_lay_qty']+=$vals[csf("today_lay_qty")];
				$cut_lay_arrs[$vals[csf("order_id")]][$vals[csf("color_id")]][$vals[csf("size_id")]]['tot_lay_qty']+=$vals[csf("tot_lay_qty")];
			}
			else
			{
				$cut_lay_arrs[$vals[csf("order_id")]][$vals[csf("color_id")]][$vals[csf("size_id")]]['today_lay_qty']+=$vals[csf("today_lay_qty")];
				$cut_lay_arrs[$vals[csf("order_id")]][$vals[csf("color_id")]][$vals[csf("size_id")]]['tot_lay_qty']+=$vals[csf("tot_lay_qty")];
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
		$order_qnty_sql_res = sql_select($order_qnty_sql);
		$colo_size_ord_qnty_arr = array();
		foreach ($order_qnty_sql_res as  $row) 
		{
			$colo_size_ord_qnty_arr[$row[csf('po_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]] = $row[csf('order_qnty')];
		}
		// echo "<pre>";
		// print_r($colo_size_ord_qnty_arr);
		// echo "</pre>";

		// ===================================== FOR FINISH CONS ======================================
		$all_po_ids_conds = str_replace("c.order_id", "b.po_break_down_id", $all_po_ids_cond) ;
		$sql_fin_con = "SELECT a.avg_finish_cons,a.avg_cons,a.plan_cut_qty, b.cons,b.po_break_down_id as po_id,b.color_number_id as color_id,b.gmts_sizes as size_id 
		FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b
		WHERE a.job_no=b.job_no	and a.id = b.pre_cost_fabric_cost_dtls_id $all_po_ids_conds
		and a.status_active=1";
       	$sql_fin_con_res = sql_select($sql_fin_con);
       	$fin_con_arr = array();
       	foreach ($sql_fin_con_res as $key => $val) 
       	{
       		$fin_con_arr[$val[csf('po_id')]][$val[csf('color_id')]]['con']+=$val[csf('cons')];
       		$fin_con_arr[$val[csf('po_id')]][$val[csf('color_id')]]['plan_cut']+=$val[csf('plan_cut_qty')];
       	}
       	// echo "<pre>";
       	// print_r($fin_con_arr);
       	// echo "</pre>";

       	// ========================================== COLOR WISE BOOKING QNTY ===================================
       	$booking_no_fin_qnty_array=array();
		 //  $all_po_ids_cond4 = str_replace("c.po_break_down_id","e.po_breakdown_id",$all_po_ids_cond4);
		// $booking_sql=sql_select("SELECT a.po_break_down_id ,b.color_number_id,a.booking_no,a.fin_fab_qnty from wo_booking_dtls a,wo_po_color_size_breakdown b  where b.id=a.color_size_table_id  and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and a.po_break_down_id in($all_po_id)");
		$booking_sql=sql_select("SELECT a.po_break_down_id ,a.gmts_color_id,a.booking_no,a.fin_fab_qnty from wo_booking_dtls a  where  a.status_active=1 and a.is_deleted=0 and a.BOOKING_TYPE=1 and a.po_break_down_id in($all_po_id)");
		// echo $booking_sql;die;
		foreach($booking_sql as $vals)
		{
			$booking_no_fin_qnty_array[$vals[csf("po_break_down_id")]][$vals[csf("gmts_color_id")]]["qnty"]+=$vals[csf("fin_fab_qnty")];

		}
       	// =========================================== FOR CUTTING QC ====================================

		   if($cbo_location>0) $location_cond_qc.=" and a.location_id=$cbo_location";
		   if($cbo_floor!="") $floor_cond_qc.=" and a.floor_id in($cbo_floor)";
   
		   if ($txt_datefrom!="" &&  $txt_dateto!="") $date_con_qc = "and a.entry_date between '".change_date_format($txt_datefrom, "yyyy-mm-dd", "-",1)."' and '".change_date_format($txt_dateto, "yyyy-mm-dd", "-",1)."'"; else $date_con_qc ="";

		$cutting_qc_sql="SELECT c.order_id as po_id,c.color_id,c.size_id, sum(c.qc_pass_qty) as qc_pass_qty
        from pro_gmts_cutting_qc_dtls c ,pro_gmts_cutting_qc_mst a
        where c.mst_id=a.id and c.status_active=1 $all_po_ids_cond  $location_cond_qc $floor_cond_qc 
        group by c.order_id,c.color_id,c.size_id";
		//echo $cutting_qc_sql;die;
        $cutting_qc_sql_res = sql_select($cutting_qc_sql);
        $cutting_qc_array = array();
        foreach ($cutting_qc_sql_res as $row) 
        {
        	$cutting_qc_array[$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('size_id')]] = $row[csf('qc_pass_qty')];
        }

		// ===================================== FOR FINISH FAB RCV QUANTITY ======================================
		$all_po_ids_cond3 = str_replace("c.po_break_down_id","e.po_breakdown_id",$all_po_ids_cond3);
		$sql_fin = "SELECT e.color_id, e.po_breakdown_id AS po_id, sum(e.quantity) as qnty,e.trans_type
	  	FROM product_details_master a,  order_wise_pro_details e
       	WHERE a.id = e.prod_id and e.trans_id>0 and a.item_category_id=2 and e.trans_type in(1,2) and e.entry_form in(18,37,68) $all_po_ids_cond3 and a.status_active=1 and e.status_active=1 group by e.color_id, e.po_breakdown_id,e.trans_type";//in(7,37,66,68,225)
		//echo $sql_fin;die;
       	$sql_fin_res = sql_select($sql_fin);
       	$fin_qnty_arr = array();
       	// $fin_qnty_arr2 = array();
       	foreach ($sql_fin_res as $key => $val) 
       	{
       		$fin_qnty_arr[$val[csf('po_id')]][$val[csf('color_id')]][$val[csf('trans_type')]]+=$val[csf('qnty')];
			   $fin_qnty_arr[$val[csf('po_id')]][$val[csf('color_id')]][$val[csf('trans_type')]]+=$val[csf('qnty')];
       		// $fin_qnty_arr2[$val[csf('po_id')]][$val[csf('color_id')]]=+$val[csf('qnty')];
       	}
       	// echo "<pre>";
       	// print_r($fin_qnty_arr);
       	// echo "</pre>"; die;

		// ===================================== FOR GTMS FINISH RCV QUANTITY ======================================
        
		   if($cbo_location>0) $location_cond_fin_rcv.=" and f.location_id=$cbo_location";
		   if($cbo_floor!="") $floor_cond_fin_rcv.=" and f.floor_id in($cbo_floor)";
   
		   if ($txt_datefrom!="" &&  $txt_dateto!="") $date_con_fin_rcv = "and f.production_date between '".change_date_format($txt_datefrom, "yyyy-mm-dd", "-",1)."' and '".change_date_format($txt_dateto, "yyyy-mm-dd", "-",1)."'"; else $date_con_fin_rcv ="";
		$all_po_ids_cond2 = str_replace("c.po_break_down_id","f.po_break_down_id",$all_po_ids_cond2);
		$fin_rcv = "SELECT f.color_id,f.size_id,f.po_break_down_id, sum(f.fin_receive_qnty) as qnty
	  	FROM gmt_finishing_receive_mst a,  gmt_finishing_receive_dtls f
       	WHERE a.id = f.mst_id  and a.production_source=1  $all_po_ids_cond2 and a.status_active=1 and f.status_active=1 and a.is_deleted=0 and f.is_deleted=0  $location_cond_fin_rcv $floor_cond_fin_rcv  $date_con_fin_rcv group by f.COLOR_ID,f.size_id,f.po_break_down_id";
		//	echo $fin_rcv;die;
       	$sql_fin_rcv = sql_select($fin_rcv);
       	$fin_rcv_qnty_arr = array();
       	
       	foreach ($sql_fin_rcv as $key => $val) 
       	{
			$fin_rcv_qnty_arr[$val[csf('po_break_down_id')]][$val[csf('color_id')]][$val[csf('size_id')]]['qnty']+=$val[csf('qnty')];
			
       		// $fin_qnty_arr2[$val[csf('po_id')]][$val[csf('color_id')]]=+$val[csf('qnty')];
       	}
       	// echo "<pre>";
       	// print_r($fin_rcv_qnty_arr);
       	// echo "</pre>"; die;
				
		$fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
		$toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );
		ob_start();
  		?>
  		
		  <div>
			<table width="4000" cellspacing="0" >
				<tr style="border:none;">
					<td colspan="20" align="center" style="border:none; font-size:16px;">
						Company Name : <? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>                                
					</td>
				</tr>
				<tr class="form_caption" style="border:none;">
					<td colspan="20" align="center" style="border:none;font-size:14px; font-weight:bold" >
						<? echo "Order Wise RMG Production Status V4"; ?>    
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
			<table width="4000" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
				<thead>
					<tr>
						<th width="30"><p>SL</p></th> 
						<th width="100"><p>Buyer</p></th>  
						<th width="100"><p>Style Name</p></th>  
						<th width="100"><p>Job No</p></th>
					
						<th width="100"><p>Order Number</p></th>
						<th width="100"><p> Ship Date</p></th>
						
						<th width="100"><p> Country</p></th>
						
						<th width="100"><p> Item Name</p></th>
						<th width="100"><p>Color</p></th>

				
						<th width="60"><p> Fin. Fab. Req.</p></th>
						<th width="60"><p>Fab. Rcv.</p></th>
						<th width="60"><p>Fab. Bal.</p></th>
						
						
						<th width="60"><p>Fab. Issue.</p></th>
						<th width="60"><p> Inhand Fab.</p></th>
						
						<th width="60"><p>Aricle No.</p></th>

						<th width="100"><p> Size</p></th>
						<th width="60"><p> Order Qty.</p></th>

						<th width="100"><p>Sewing  Floor.</p></th>
						<th width="100"><p> Sewing  Line No.</p></th>
						<th width="60"><p> Today Lay Cutting</p></th> 

						<th width="60"><p>Total Cutting</p></th>
						<th width="60"><p> %</p></th>                        
                        <th width="60"><p>Cut. Bal.</p></th>
						<th width="60"><p>Cut. QC</p></th>
						<th width="60"><p>Reject</p></th> 

						<th width="60"><p>Sent to Print</p></th>
						<th width="60" ><p>Print. Rcv.</p></th>
						<th width="60"><p> Print Bal.</p></th>
						<th width="60"><p>Print Reject.</p></th>
						<th width="60"><p>Sent to Emb</p></th>
						<th width="60"><p>Emb. Rcv.</p></th> 

						<th width="60"><p> Emb.Bal.</p></th>
						<th width="60"><p> Emb.Rej</p></th>
						<th width="60"><p>Today Input</p></th> 

						<th width="60"><p> Sew. Input</p></th>
						<th width="60"><p> input Bal.</p></th>
						<th width="60"><p> Sewing Output</p></th>
						<th width="60"><p> Bal.</p></th>

						<th width="60"><p>Finishing Recv</p></th>
						<th width="60"><p>Bal.</p></th>
						<th width="60"><p>Iron</p></th>
						<th width="60"><p>Bal.</p></th>
						<th width="60"><p>Poly</p></th>
						<th width="60"><p>Bal.</p></th>

						<th width="60"><p> Pack. & Fin.</p></th>
						<th width="60"><p> Bal.</p></th>
						<th width="60"><p> Export</p></th>
						<th width="60"><p> Balance</p></th>
					 </tr>
				</thead>
			</table>
			<div style="max-height:330px; overflow-y:scroll; width:4020px" id="scroll_body">
				<table border="1" class="rpt_table" width="4000" rules="all" id="table_body">
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
					$gr_print_rej_in_qnty 	= 0;
					$gr_emb_balance_qnty = 0;
					$gr_emb_rej_qnty 	= 0;
					$gr_finsh_rcv_qnty 	= 0;;
					$gr_finsh_blance_qnty = 0;;
					$gr_iron_qnty 		=  0;;
					$gr_iron_balnce_qnty =  0;;
					$gr_poly_qnty 		=  0;;
					$gr_poly_balnce_qnty 	= 0;
					$gr_today_cutting_qtys     =0;
					$gr_today_sew_qty = 0;
					foreach ($main_array as $po_id => $po_data) 
					{					
						// initialize po wise sub total	
						$po_order_qnty 		= 0;
						$po_cutting_qnty 	= 0;
						$po_cut_bal_qnty 	= 0;
						$po_cut_qc_qnty 	= 0;
						$po_cut_rej_qnty 	= 0;
						$po_printS_qnty 	= 0;
						$po_printR_qnty 	= 0;
						$po_print_rej_qnty 	= 0;
						$po_print_rej_in_qnty 	= 0;
						$po_embS_qnty 		= 0;
						$po_embR_qnty 		= 0;
						$po_emb_balance_qnty = 0;
						$po_emb_rej_qnty 	= 0;
						$po_input_qnty 		= 0;
						$po_input_bal_qnty 	= 0;
						$po_output_qnty 	= 0;
						$po_out_bal_qnty 	= 0;
						$po_pac_fin_qnty 	= 0;
						$po_pac_fin_bal_qnty= 0;
						$po_export_qnty 	= 0;
						$po_export_bal_qnty = 0;
						$po_finsh_rcv_qnty 		= 0;
						$po_finsh_blance_qnty 	= 0;
						$po_iron_qnty 		= 0;
						$po_iron_balnce_qnty 		= 0;
						$po_poly_qnty 		= 0;
						$po_poly_balnce_qnty 		= 0;

						$po_today_cutting_qtys      =0;
						$po_today_sew_qty = 0;
						foreach ($po_data as $color_id => $color_data) 
						{
							// initialize color wise sub total
							$co_order_qnty 		= 0;
							$co_cutting_qnty 	= 0;
							$co_cut_bal_qnty 	= 0;
							$co_cut_qc_qnty 	= 0;
							$co_cut_rej_qnty 	= 0;
							$co_printS_qnty 	= 0;
							$co_printR_qnty 	= 0;
							$co_print_rej_qnty 	= 0;
							$co_print_rej_in_qnty 	= 0;
							$co_embS_qnty 		= 0;
							$co_embR_qnty 		= 0;
							$co_input_qnty 		= 0;
							$co_input_bal_qnty 	= 0;
							$co_output_qnty 	= 0;
							$co_out_bal_qnty 	= 0;
							$co_pac_fin_qnty 	= 0;
							$co_pac_fin_bal_qnty= 0;
							$co_export_qnty 	= 0;
							$co_export_bal_qnty = 0;
							$co_emb_balance_qnty 		= 0;
							$co_emb_rej_qnty 		= 0;

							$co_finsh_rcv_qnty 		= 0;
							$co_finsh_blance_qnty 	= 0;
							$co_iron_qnty 		= 0;
							$co_iron_balnce_qnty 		= 0;
							$co_poly_qnty 		= 0;
							$co_poly_balnce_qnty 		= 0;

							$co_today_cutting_qtys      =0;
						    $co_today_sew_qty = 0;
							$color = 1;
							foreach ($color_data as $size_id => $row) 
							{	
								$bgcolor = ($s%2==0) ? "#ffffff" : "#ffffff";//#e8f6ff
								$fin_con 		= $fin_con_arr[$po_id][$color_id]['con'];
								$plan_cut 		= $fin_con_arr[$po_id][$color_id]['plan_cut'];
								$fab_req_qty	= $booking_no_fin_qnty_array[$po_id][$color_id]['qnty'];
								$fab_fin_qnty_rcv	= $fin_qnty_arr[$po_id][$color_id][1];
								$fab_fin_qnty_issue	= $fin_qnty_arr[$po_id][$color_id][2];
								$order_qnty 	= $colo_size_ord_qnty_arr[$po_id][$color_id][$size_id];
								$cut_qnty 		= $cut_lay_arrs[$po_id][$color_id][$size_id]['tot_lay_qty'];								
								$today_cutting_qtys = $cut_lay_arrs[$po_id][$color_id][$size_id]['today_lay_qty'];
								$cut_balance 	= $order_qnty - $cut_qnty;
								$cut_qc 		= $cutting_qc_array[$po_id][$color_id][$size_id];
								$cut_rej 		= $gmts_prod_arr[$po_id][$color_id][$size_id]['cutting_rej_qnty'];
								$print_issue 	= $gmts_prod_arr[$po_id][$color_id][$size_id]['print_issue_inhouse'];
								$print_recive 	= $gmts_prod_arr[$po_id][$color_id][$size_id]['print_rcv_inhouse'];
								$print_rej_in 	= $gmts_prod_arr[$po_id][$color_id][$size_id]['print_rej_inhouse'];
								$print_reject 	= $print_issue - $print_recive;
								$embel_issue 	= $gmts_prod_arr[$po_id][$color_id][$size_id]['emb_issue_inhouse'];
								$embel_rcv 		= $gmts_prod_arr[$po_id][$color_id][$size_id]['emb_rcv_inhouse'];
								$embel_blance   =($embel_issue -$embel_rcv);
								$embel_rej		= $gmts_prod_arr[$po_id][$color_id][$size_id]['emb_rej'];
								$iron 		= $gmts_prod_arr[$po_id][$color_id][$size_id]['iron_in'];
								$poly 		= $gmts_prod_arr[$po_id][$color_id][$size_id]['poly_in'];
								$sewing_in 		= $gmts_prod_arr[$po_id][$color_id][$size_id]['sQty'];
								$input_bal 		= $cut_qnty - $sewing_in;
								$sewing_out 	= $gmts_prod_arr[$po_id][$color_id][$size_id]['soQty'];
								$sewing_bal 	= $sewing_in - $sewing_out;
								$pack_finish 	= $gmts_prod_arr[$po_id][$color_id][$size_id]['finish_qnty_inhouse'];
								$pack_fin_bal 	= $sewing_out - $pack_finish;
								$ex_fact_qnty 	= $ex_factory_arr[$po_id][$color_id][$size_id]['ex_qty'];
								$ex_fact_bal 	= $order_qnty - $ex_fact_qnty;
								$fin_rcv_qnty = $fin_rcv_qnty_arr[$po_id][$color_id][$size_id]['qnty'];
								$fin_blnc_qnty =($sewing_out - $fin_rcv_qnty);
								$iron_blance = ($fin_rcv_qnty - $iron);
								$poly_blance =($fin_rcv_qnty - $poly);
								// $today_cutting_qtys = $gmts_prod_arr[$po_id][$color_id][$size_id]['today_cutting_qnty'];
								//echo $today_cutting_qtys ;
								$today_sew_qty =$gmts_prod_arr[$po_id][$color_id][$size_id]['today_sewingin_qnty'];
								// color wise increment
								$co_order_qnty 		+= $order_qnty;
								$co_cutting_qnty 	+= $cut_qnty;
								$co_cut_bal_qnty 	+= $cut_balance;
								$co_cut_qc_qnty 	+= $cut_qc;
								$co_cut_rej_qnty 	+= $cut_rej;
								$co_printS_qnty 	+= $print_issue;
								$co_printR_qnty 	+= $print_recive;
								$co_print_rej_in_qnty 	+= $print_rej_in;
								$co_embS_qnty 		+= $embel_issue;
								$co_embR_qnty 		+= $embel_rcv;
								$co_input_qnty 		+= $sewing_in;
								$co_input_bal_qnty 	+= $input_bal;
								$co_output_qnty 	+= $sewing_out;
								$co_out_bal_qnty 	+= $sewing_bal;
								$co_pac_fin_qnty 	+= $pack_finish;
								$co_pac_fin_bal_qnty+= $pack_fin_bal;
								$co_export_qnty 	+= $ex_fact_qnty;
								$co_export_bal_qnty += $ex_fact_bal;
								$co_emb_balance_qnty += $embel_blance;
								$co_emb_rej_qnty 	+= $embel_rej;


								$co_finsh_rcv_qnty 	+= $fin_rcv_qnty;
								$co_finsh_blance_qnty += $fin_blnc_qnty;
								$co_iron_qnty 		+= $iron;
								$co_iron_balnce_qnty += $iron_blance;
								$co_poly_qnty 		+= $poly;
								$co_poly_balnce_qnty 	+= $poly_blance;
								$co_today_cutting_qtys      += $today_cutting_qtys;
								$co_today_sew_qty += $today_sew_qty;
								// po wise increment
								$po_order_qnty 		+= $order_qnty;
								$po_cutting_qnty 	+= $cut_qnty;
								$po_cut_bal_qnty 	+= $cut_balance;
								$po_cut_qc_qnty 	+= $cut_qc;
								$po_cut_rej_qnty 	+= $cut_rej;
								$po_printS_qnty 	+= $print_issue;
								$po_printR_qnty 	+= $print_recive;
								$po_print_rej_qnty 	+= $print_reject;
								$po_print_rej_in_qnty += $print_rej_in;
								$po_embS_qnty 		+= $embel_issue;
								$po_embR_qnty 		+= $embel_rcv;
								$po_input_qnty 		+= $sewing_in;
								$po_input_bal_qnty 	+= $input_bal;
								$po_output_qnty 	+= $sewing_out;
								$po_out_bal_qnty 	+= $sewing_bal;
								$po_pac_fin_qnty 	+= $pack_finish;
								$po_pac_fin_bal_qnty+= $pack_fin_bal;
								$po_export_qnty 	+= $ex_fact_qnty;
								$po_export_bal_qnty += $ex_fact_bal;
								$po_emb_balance_qnty += $embel_blance;
								$po_emb_rej_qnty 	+= $embel_rej;

								$po_finsh_rcv_qnty 	+= $fin_rcv_qnty;
								$po_finsh_blance_qnty += $fin_blnc_qnty;
								$po_iron_qnty 		+= $iron;
								$po_iron_balnce_qnty += $iron_blance;
								$po_poly_qnty 		+= $poly;
								$po_poly_balnce_qnty 	+= $poly_blance;

								$po_today_cutting_qtys      += $today_cutting_qtys;
								$po_today_sew_qty += $today_sew_qty;
								// grand total increment
								$gr_order_qnty 		+= $order_qnty;
								$gr_cutting_qnty 	+= $cut_qnty;
								$gr_cut_bal_qnty 	+= $cut_balance;
								$gr_cut_qc_qnty 	+= $cut_qc;
								$gr_cut_rej_qnty 	+= $cut_rej;
								$gr_printS_qnty 	+= $print_issue;
								$gr_printR_qnty 	+= $print_recive;
								$gr_print_rej_qnty 	+= $print_reject;
								$gr_embS_qnty 		+= $embel_issue;
								$gr_embR_qnty 		+= $embel_rcv;
								$gr_input_qnty 		+= $sewing_in;
								$gr_input_bal_qnty 	+= $input_bal;
								$gr_output_qnty 	+= $sewing_out;
								$gr_out_bal_qnty 	+= $sewing_bal;
								$gr_pac_fin_qnty 	+= $pack_finish;
								$gr_pac_fin_bal_qnty+= $pack_fin_bal;
								$gr_export_qnty 	+= $ex_fact_qnty;
								$gr_export_bal_qnty += $ex_fact_bal;

								$gr_print_rej_in_qnty 	+=$print_rej_in;
								$gr_emb_balance_qnty += $embel_blance;
								$gr_emb_rej_qnty 	+= $embel_rej;
								$gr_finsh_rcv_qnty += $fin_rcv_qnty;
								$gr_finsh_blance_qnty += $fin_blnc_qnty;
								$gr_iron_qnty 		+= $iron;
								$gr_iron_balnce_qnty += $po_iron_balnce_qnty;
								$gr_poly_qnty 		+= $poly ;
								$gr_poly_balnce_qnty 	+= $poly_blance;

								$gr_today_cutting_qtys      += $today_cutting_qtys;
								$gr_today_sew_qty += $today_sew_qty;
								
								$f_id_arr = array_unique(array_filter(explode(",",$gmts_prod_arr[$po_id][$color_id][$size_id]['floor_id'])));
								$flr_name = "";
								foreach ($f_id_arr as $v) 
								{
									$flr_name .= ($flr_name=="") ? $floor_arr[$v] : ", ".$floor_arr[$v];
								}

								$s_id_arr = array_unique(array_filter(explode("==",$gmts_prod_arr[$po_id][$color_id][$size_id]['sewing_line'])));
								$sewing_line = "";
								foreach ($s_id_arr as $r) 
								{
									$rex = explode("___",$r);
									if($rex[1]==1)
									{
										
										$sewing_line .= ($sewing_line=="") ? $sewing_line_arr[$prod_reso_arr[$rex[0]]] : ", ".$sewing_line_arr[$prod_reso_arr[$rex[0]]];
									}
									else
									{
										$sewing_line .= ($sewing_line=="") ? $sewing_line_arr[$rex[0]] : ", ".$sewing_line_arr[$rex[0]];
									}
								}
								?>
								
								
                                <tr bgcolor='<? echo $bgcolor; ?>' onclick="change_color('tr_2nd<? echo $s;?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $s;?>">
								<?
								if($color==1)
                                {
                                    ?>
											<td  rowspan="<? echo $rowSpanArray[$po_id][$color_id];?>"  width="30"><? echo $s;?></td>
											 <td  valign="middle" rowspan="<? echo $rowSpanArray[$po_id][$color_id];?>" width="100"><p><?= $buyer_short_library[$row['buyer_name']]; ?><p></td>  
												<td valign="middle" rowspan="<? echo $rowSpanArray[$po_id][$color_id];?>" width="100"><p><?= $row['style_ref_no']?><p></td>  
												<td valign="middle" rowspan="<? echo $rowSpanArray[$po_id][$color_id];?>" width="100"><p><? echo $row['job_no']; ?></p></th>
											
												<td valign="middle" rowspan="<? echo $rowSpanArray[$po_id][$color_id];?>"  width="100"><p><? echo $row['po_number']; ?></p></td>
												<td valign="middle" rowspan="<? echo $rowSpanArray[$po_id][$color_id];?>"  width="100"><p><? echo change_date_format($row['shipment_date']);  ?></p></td>
												<td valign="middle"  width="100" rowspan="<? echo $rowSpanArray[$po_id][$color_id];?>"><p><?=$country_library[$row['country_id']];?></p></td>
												
												<td valign="middle" rowspan="<? echo $rowSpanArray[$po_id][$color_id];?>"  width="100">
												<p><?
														$itemDetails = '';
														foreach($row['item_number_id'] as $itemNumID)
														{
															if($itemDetails != '')
																$itemDetails .= ', ';
															
															$itemDetails .= $garments_item[$itemNumID];
														}
														echo implode(", ", array_unique(array_filter(explode(", ", $itemDetails))));
														?></p>
												</td>

												<td valign="middle" rowspan="<? echo $rowSpanArray[$po_id][$color_id];?>"  width="100"></p><? echo $color_Arr_library[$color_id]; ?></p></td>
											
												<td title="<? echo 'fin con = '.$fin_con.' , plan_cut='.$plan_cut;?>" valign="middle" rowspan="<? echo $rowSpanArray[$po_id][$color_id];?>"  width="60" align="right"><p><?  echo number_format($fab_req_qty); ?></p></td>
												<td valign="middle" rowspan="<? echo $rowSpanArray[$po_id][$color_id];?>"  width="60" align="right"><p><? echo number_format($fab_fin_qnty_rcv,2); ?></p></td>
												<td valign="middle" rowspan="<? echo $rowSpanArray[$po_id][$color_id];?>"  width="60" align="right"><p><? echo number_format($fab_req_qty - $fab_fin_qnty_rcv); ?></p></td>
												
												
												<td valign="middle" width="60" rowspan="<? echo $rowSpanArray[$po_id][$color_id];?>" align="right"><p><? echo number_format($fab_fin_qnty_issue,2); ?></p></td>
												<td valign="middle" width="60" rowspan="<? echo $rowSpanArray[$po_id][$color_id]; ?>"align="center"><p><?=$fab_fin_qnty_rcv - $fab_fin_qnty_issue?></p></td>
												
												<?
												$s++;
												}
												?>
												
												<td  width="60"><p><? echo $row['article_number']; ?></p></td>
                                   				 <td  width="100"><p><? echo $size_Arr_library[$size_id]; ?></p></td>
												<td  width="60" align="right"><p><? echo number_format($order_qnty,0); ?></p></td>

												<td width="100"><p><? echo  $flr_name  ?></p></td>
												<td width="100"><p><?= $sewing_line  ?></p></td>
												<td width="60" align="right"><p><? echo number_format($today_cutting_qtys,0) ;?></p></td> 

												<td  width="60" align="right"><p><? echo number_format($cut_qnty,0); ?></p></td>
												<td  width="60" align="right"><p><? $percent = ($cut_qnty / $order_qnty)*100; echo number_format($percent,2); ?></p>%</td>            
												<td  width="60" align="right"><p><? echo number_format($cut_balance,0); ?></p></td>
												<td  width="60" align="right"><p><? echo number_format($cut_qc,0); ?></p></td>
												<td  width="60" align="right"><p><? echo number_format($cut_rej,0); ?></p</td>

												<td  width="60" align="right"><p><? echo number_format($print_issue,0); ?></p></td>
												<td  width="60" align="right"><p><? echo number_format($print_recive,0); ?></p></td>
												<td  width="60" align="right"><p><?  echo number_format($print_reject,0); ?></p></td>
												<td width="60" align="right"><p><?= number_format($print_rej_in,0)?></p></td>

												<td  width="60" align="right"><p><? echo number_format($embel_issue,0); ?></p></td>

											   <td  width="60" align="right"><p><? echo number_format($embel_rcv,0); ?></p></td>

												<td width="60" align="right"><p><?=number_format($embel_blance,0)?></p></td>
												<td width="60" align="right"><p><?=number_format($embel_rej,0)?></p></td>
												<td width="60" align="right"><p><?=number_format($today_sew_qty) ;?></p></td>

												<td  width="60" align="right"><p><? echo number_format($sewing_in,0); ?></p></td>
												<td  width="60" align="right"><p><? echo number_format($input_bal,0); ?></p></td>
												<td  width="60" align="right"><p><? echo number_format($sewing_out,0); ?></p></td>
												<td  width="60" align="right"><p><? echo number_format($sewing_bal,0); ?></p</td>

												<td width="60" align="right"><p><?= number_format($fin_rcv_qnty,0)?></p></td>
												<td width="60" align="right"><p><? echo number_format($fin_blnc_qnty,0); ?></p></td>
												<td width="60" align="right"><p><?=number_format($iron,0)?></p></td>
												<td width="60" align="right"><p><? echo number_format($iron_blance,0); ?></p></td>
												<td width="60" align="right"><p><?=number_format($poly)?></p></td>
												<td width="60" align="right"><p><? echo number_format($poly_blance,0); ?></p></td>

												<td  width="60" align="right"><p><? echo number_format($pack_finish,0); ?></p></td>
												<td  width="60" align="right"><p><? echo number_format($pack_fin_bal,0); ?></p></td>
												<td  width="60" align="right"><p><? echo number_format($ex_fact_qnty,0); ?></p></td>	
												<td  width="60" align="right"><p><? echo number_format($ex_fact_bal,0); ?></p></td>
                                   
                                <? 
								$s++;
								$color++;					
                                
							}
							?>
							<tr class="gd-color2">
								<td colspan="16" align="right"><b> Color Wise Sub Total</b></td>
								<td align="right"><? echo number_format($co_order_qnty);?></td>
								<td align="right"></td>
								<td align="right"></td>
								<td align="right"><? echo number_format($co_today_cutting_qtys,0);?></td>
								
								<td align="right"><? echo number_format($co_cutting_qnty,0);?></td>
								<td align="right"></td>
								<td align="right"><? echo number_format($co_cut_bal_qnty,0);?></td>
								<td align="right"><? echo number_format($co_cut_qc_qnty,0);?></td>
								<td align="right"><? echo number_format($co_cut_rej_qnty);?></td>
								<td align="right"><? echo number_format($co_printS_qnty);?></td>
								<td align="right"><? echo number_format($co_printR_qnty);?></td>
								<td align="right"><? echo number_format($co_print_rej_qnty);?></td>
								<td align="right"><? echo number_format($co_print_rej_in_qnty);?></td>
								<td align="right"><? echo number_format($co_embS_qnty);?></td>
								<td align="right"><? echo number_format($co_embR_qnty);?></td>

								<td align="right"><? echo number_format($co_emb_balance_qnty);?></td>
								<td align="right"><? echo number_format($co_emb_rej_qnty);?></td>
								<td align="right"><? echo number_format($co_today_sew_qty);?></td>
								<td align="right"><? echo number_format($co_input_qnty);?></td>
								<td align="right"><? echo number_format($co_input_bal_qnty);?></td>
								<td align="right"><? echo number_format($co_output_qnty);?></td>
								<td align="right"><? echo number_format($co_out_bal_qnty);?></td>
								<td align="right"><? echo number_format($co_finsh_rcv_qnty);?></td>
								<td align="right"><? echo number_format($co_finsh_blance_qnty);?></td>
								<td align="right"><? echo number_format($co_iron_qnty);?></td>
								<td align="right"><? echo number_format($co_iron_balnce_qnty);?></td>
								<td align="right"><? echo number_format($co_poly_qnty);?></td>
								<td align="right"><? echo number_format($co_poly_balnce_qnty);?></td>
								<td align="right"><? echo number_format($co_pac_fin_qnty);?></td>
								<td align="right"><? echo number_format($co_pac_fin_bal_qnty);?></td>
								<td align="right"><? echo number_format($co_export_qnty);?></td>
								<td align="right"><? echo number_format($co_export_bal_qnty);?></td>
							</tr>
							<?
						}
						?>
                        <tr class="gd-color3">
                           		 <td colspan="16" align="right"><b> Po Wise Sub Total</b></td>
                                <td align="right"><? echo number_format($po_order_qnty);?></td>
								<td align="right"></td>
								<td align="right"></td>
								<td align="right"><? echo number_format($po_today_cutting_qtys);?></td>
                                <td align="right"><? echo number_format($po_cutting_qnty);?></td>
                                <td align="right"></td>
                                <td align="right"><? echo number_format($po_cut_bal_qnty);?></td>
                                <td align="right"><? echo number_format($po_cut_qc_qnty);?></td>
                                <td align="right"><? echo number_format($po_cut_rej_qnty);?></td>

                                <td align="right"><? echo number_format($po_printS_qnty);?></td>
                                <td align="right"><? echo number_format($po_printR_qnty);?></td>
                                <td align="right"><? echo number_format($po_print_rej_qnty);?></td>
								<td align="right"><? echo number_format($po_print_rej_in_qnty);?></td>
                                <td align="right"><? echo number_format($po_embS_qnty);?></td>
								<td align="right"><? echo number_format($po_embR_qnty);?></td>

								<td align="right"><? echo number_format($po_emb_balance_qnty);?></td>
								<td align="right"><? echo number_format($po_emb_rej_qnty);?></td>
                                <td align="right"><? echo number_format($po_today_sew_qty);?></td>

                                <td align="right"><? echo number_format($po_input_qnty);?></td>
                                <td align="right"><? echo number_format($po_input_bal_qnty);?></td>
                                <td align="right"><? echo number_format($po_output_qnty);?></td>
                                <td align="right"><? echo number_format($po_out_bal_qnty);?></td>

								<td align="right"><? echo number_format($po_finsh_rcv_qnty);?></td>
								<td align="right"><? echo number_format($po_finsh_blance_qnty);?></td>
								<td align="right"><? echo number_format($po_iron_qnty);?></td>
								<td align="right"><? echo number_format($po_iron_balnce_qnty);?></td>
								<td align="right"><? echo number_format($po_poly_qnty);?></td>
								<td align="right"><? echo number_format($po_poly_balnce_qnty);?></td>

                                <td align="right"><? echo number_format($po_pac_fin_qnty);?></td>
                                <td align="right"><? echo number_format($po_pac_fin_bal_qnty);?></td>
                                <td align="right"><? echo number_format($po_export_qnty);?></td>
                                <td align="right"><? echo number_format($po_export_bal_qnty);?></td>
                        </tr>
                        <?
                    }
                    ?>
					<tfoot>
						<tr>
						        <th colspan="16" align="right"><b> Grand Total</b></th>
						        <th align="right"><? echo number_format($gr_order_qnty);?></th>
								<th align="right"></th>
								<th align="right"></th>
								<th align="right"><? echo number_format($gr_today_cutting_qtys);?></th>
                                <th align="right"><? echo number_format($gr_cutting_qnty);?></th>
                                <th align="right"></th>
                                <th align="right"><? echo number_format($gr_cut_bal_qnty);?></th>
                                <th align="right"><? echo number_format($gr_cut_qc_qnty);?></th>
                                <th align="right"><? echo number_format($gr_cut_rej_qnty);?></th>

                                <th align="right"><? echo number_format($gr_printS_qnty);?></th>
                                <th align="right"><? echo number_format($gr_printR_qnty);?></th>
                                <th align="right"><? echo number_format($gr_print_rej_qnty);?></th>
								<th align="right"><? echo number_format($gr_print_rej_in_qnty);?></th>
                                <th align="right"><? echo number_format($gr_embS_qnty);?></th>
								<th align="right"><? echo number_format($gr_embR_qnty);?></th>

								<th align="right"><? echo number_format($gr_emb_balance_qnty);?></th>
								<th align="right"><? echo number_format($gr_emb_rej_qnty);?></th>
                                <th align="right"><? echo number_format($gr_today_sew_qty);?></th>

                                <th align="right"><? echo number_format($gr_input_qnty);?></th>
                                <th align="right"><? echo number_format($gr_input_bal_qnty);?></th>
                                <th align="right"><? echo number_format($gr_output_qnty);?></th>
                                <th align="right"><? echo number_format($gr_out_bal_qnty);?></th>

								<th align="right"><? echo number_format($gr_finsh_rcv_qnty);?></th>
								<th align="right"><? echo number_format($gr_finsh_blance_qnty);?></th>
								<th align="right"><? echo number_format($gr_iron_qnty);?></th>
								<th align="right"><? echo number_format($gr_iron_balnce_qnty);?></th>
								<th align="right"><? echo number_format($gr_poly_qnty);?></th>
								<th align="right"><? echo number_format($gr_poly_balnce_qnty);?></th>

                                <th align="right"><? echo number_format($gr_pac_fin_qnty);?></th>
                                <th align="right"><? echo number_format($gr_pac_fin_bal_qnty);?></th>
                                <th align="right"><? echo number_format($gr_export_qnty);?></th>
                                <th align="right"><? echo number_format($gr_export_bal_qnty);?></th>
						
						


						</tr>
					</tfoot>
				</table>
		 	</div>   	
			
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