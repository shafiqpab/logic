<?php 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];
// $order_status = array(1 => "Confirmed", 2 => "Projected");
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
	if($buyer!=0) $buyer_cond=" and a.party_id=$buyer"; else $buyer_cond="";
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
	
	//$sql = "select a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num,$select_date as year from wo_po_details_master a where a.company_name=$company $buyer_cond  $job_year_cond and is_deleted=0 order by job_no_prefix_num";
	 $sql="SELECT a.id,a.subcon_job as job_no,a.job_no_prefix_num,$select_date as year,b.cust_style_ref from  subcon_ord_mst a,subcon_ord_dtls b 
			where   a.subcon_job=b.job_no_mst and a.status_active=1 and b.status_active=1  and a.company_id=$company $buyer_cond  group by a.id,a.subcon_job,a.job_no_prefix_num,a.insert_date,b.cust_style_ref order by a.id desc "; 
	//echo $sql; 
	echo create_list_view("list_view", "Style Ref No,Job No,Year","160,90,100","400","310",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "0", $arr, "cust_style_ref,job_no_prefix_num,year", "","setFilterGrid('list_view',-1)","0","",1) ;	
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
	echo create_list_view("list_view", "Order NO,Job No,Year,Style Ref No","150,80,70,150","500","310",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "po_number,job_no_prefix_num,year,style_ref_no", "","setFilterGrid('list_view',-1)","0","",1) ;	
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
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_short_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name"  );
//$po_no_library=return_library_array( "select id,po_number from wo_po_break_down", "id", "po_number"  );

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);

	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	//$cbo_job_year=str_replace("'","",$cbo_job_year);  
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_job_id=str_replace("'","",$txt_job_id);

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
	//if($txt_style_ref_number=="") $txt_style_ref_number_cond=''; else $txt_style_ref_number_cond=" and a.style_ref_no like ".$txt_style_ref_number."";
	
	 
	 if($type==1) //order and size wise 
	{
		//report order and color wise kaiyum
		
		$company_name=$cbo_company_name;
			
			
		if($txt_job_id!="")
		{
			$txt_style_ref_id_cond=" and e.id in($txt_job_id)";
		}
		else if($txt_job_no!="")
		{
			$txt_style_ref_id_cond =" and e.job_no_prefix_num in($txt_job_no)";
		}
		
		
				if($cbo_company_name!=0) $style_comp=" and e.company_id=$cbo_company_name";
			
		if($cbo_buyer_name!=0 )  $cbo_buyer_name_cond =" and e.party_id =$cbo_buyer_name"; 
		if($txt_datefrom!="" &&  $txt_dateto!="")  $txt_datefrom_to_cond =" and d.delivery_date between '$txt_datefrom' and '$txt_dateto'";  
		$size_Arr_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
		$color_variable_setting=return_field_value("cutting_update","variable_settings_production","company_name='$company_name' and variable_list=1 and status_active=1 and is_deleted=0","cutting_update");
		//echo $color_variable_setting;
		//$sql_result = sql_select("select cutting_update,sewing_production from  variable_settings_production where company_name=$data and variable_list=1 and status_active=1");
		 $sql_po="SELECT e.company_id,e.party_id,d.cust_style_ref as style_ref_no,d.order_no,a.order_id as po_break_down_id,a.item_id as item_number_id, a.color_id as color_number_id, a.size_id as size_number_id, sum(d.order_quantity) as order_quantity from subcon_ord_breakdown a,subcon_ord_dtls d,subcon_ord_mst e 
			where   a.order_id=d.id and a.job_no_mst=e.subcon_job  and e.subcon_job=d.job_no_mst and a.color_id>0 and a.size_id>0 $po_break_down_id_cond   $cbo_buyer_name_cond  $txt_style_ref_id_cond $style_comp $txt_datefrom_to_cond group by  e.company_id,e.party_id,d.order_no,d.cust_style_ref,a.color_id,a.size_id,a.order_id,a.item_id order by a.order_id,a.color_id, a.size_id";
		
			//echo $sql_exfect."<br>";
			$sql_result_po=sql_select($sql_po);
			$style_ref="";
			foreach($sql_result_po as $row)
			{
				$po_qty_arr[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['order_quantity']=$row[csf("order_quantity")];
				$all_po_arr[$row[csf("po_break_down_id")]]=$row[csf("po_break_down_id")];
				
				$po_color_size_data[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['style_ref_no']=$row[csf("style_ref_no")];
				$po_color_size_data[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['order_quantity']=$row[csf("order_quantity")];
				$po_color_size_data[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['plan_cut_qnty']=$row[csf("plan_cut_qnty")];
				$po_no_library[$row[csf("po_break_down_id")]]=$row[csf("order_no")];
				$po_no_arr[$row[csf("po_break_down_id")]]['party_id']=$row[csf("party_id")];
				$po_no_arr[$row[csf("po_break_down_id")]]['company_id']=$row[csf("company_id")];
				$po_no_arr[$row[csf("po_break_down_id")]]['style_ref_no']=$row[csf("style_ref_no")];
				$style_ref.=$row[csf("style_ref_no")].',';
			}
			//echo $style_ref.'ddddddsa';
			//print_r($po_id_arr);
		if($db_type==2 && count($all_po_arr)>999)
		{
			$all_po_id_chunk=array_chunk($all_po_arr,999) ;
			$all_po_id_cond="";
			foreach($all_po_id_chunk as $chunk_arr)
			{
				$ids=implode(",",$chunk_arr);
				if(!$all_po_id_cond) $all_po_id_cond.=" and ( x.order_id in($ids) ";
				else $all_po_id_cond.=" or x.order_id in($ids) ";

			}

			$all_po_id_cond.=" )";			

		}
		else
		{ 	
			$all_po_ids=implode(",",$all_po_arr);
			$all_po_id_cond=" and x.order_id in($all_po_ids)";  
		}
		
			
		$ex_fact_qty_arr=array();
		if($color_variable_setting==2 || $color_variable_setting==3)
		{
		 	 $sql_exfect="SELECT a.order_id as po_break_down_id,a.item_id as item_number_id, a.color_id as color_number_id, a.size_id as size_number_id, sum(x.delivery_qty) as production_qnty from subcon_gmts_delivery_dtls x,subcon_delivery_dtls y,subcon_ord_breakdown a,subcon_ord_dtls d,subcon_ord_mst e 
			where x.dtls_mst_id=y.id and x.mst_id=y.mst_id and x.breakdown_color_size_id=a.id and a.order_id=d.id and a.job_no_mst=e.subcon_job  and e.subcon_job=d.job_no_mst   and a.color_id>0 and a.size_id>0 $po_break_down_id_cond   $cbo_buyer_name_cond  $txt_style_ref_id_cond $style_comp $txt_datefrom_to_cond group by  a.color_id,a.size_id,a.order_id,a.item_id order by a.order_id,a.color_id, a.size_id";
		
			//echo $sql_exfect."<br>";
			$sql_result_exfact=sql_select($sql_exfect);
			foreach($sql_result_exfact as $row)
			{
				$ex_fact_qty_arr[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['ex_fact_qty']=$row[csf("production_qnty")];
				//$po_id_arr[$row[csf("po_break_down_id")]]=$row[csf("po_break_down_id")];
			}
		}
	
		if($db_type==0)
		{
			
			$prod_sq="SELECT x.order_id as po_break_down_id, y.ord_color_size_id as color_size_break_down_id, a.item_id as item_number_id, a.color_id as color_number_id, a.size_id as size_number_id, 
			IFNULL(sum(case when y.production_type ='1' and x.production_type ='1' then y.prod_qnty else 0 end),0) as cutting_qnty, 
			IFNULL(sum(case when y.production_type ='1' and x.production_type ='1' and x.entry_break_down_type=3 then y.prod_qnty else 0 end),0) as cutting_qnty_qc,
			IFNULL(sum(case when x.production_type ='7' and y.production_type ='7' then y.prod_qnty else 0 end),0) as sewingin_qnty, 
			IFNULL(sum(case when x.production_type ='2' and  y.production_type ='2' then y.prod_qnty else 0 end),0) as sewingout_qnty, 
			IFNULL(sum(case when x.production_type ='4' and y.production_type ='4' then y.prod_qnty else 0 end),0) as finishin_qnty, 
			IFNULL(sum(case when x.production_type ='3' and y.production_type ='3' then y.prod_qnty else 0 end),0) as iron_qnty, 
			IFNULL(sum(case when x.production_type ='8' and y.production_type ='8' then y.prod_qnty else 0 end),0) as finish_qnty, 
			IFNULL(sum(case when x.production_type ='5' and y.production_type ='5' then y.prod_qnty else 0 end),0) as poly_qty
			
			from 
			subcon_gmts_prod_dtls x, subcon_gmts_prod_col_sz y , subcon_ord_breakdown a,subcon_ord_mst e 
			where 
			x.id=y.dtls_id and y.ord_color_size_id=a.id and e.entry_form=238   and e.status_active=1   and a.job_no_mst=e.subcon_job   and e.is_deleted=0 
			   $style_comp  $cbo_buyer_name_cond   $all_po_id_cond
			 and x.status_active=1 and x.is_deleted=0  and a.status_active=1
			group by x.order_id, y.ord_color_size_id, a.item_id, a.color_id, a.size_id order by x.order_id,a.color_id ,a.size_id ";
			
			$prod_sql=sql_select($prod_sq);
		}
		else
		{
		    $prod_sq="SELECT x.order_id as po_break_down_id, y.ord_color_size_id as color_size_break_down_id, a.item_id as item_number_id, a.color_id as color_number_id, a.size_id as size_number_id, 
			nvl(sum(case when y.production_type ='1' and x.production_type ='1' then y.prod_qnty else 0 end),0) as cutting_qnty, 
			nvl(sum(case when y.production_type ='1' and x.production_type ='1' and x.entry_break_down_type=3 then y.prod_qnty else 0 end),0) as cutting_qnty_qc,
			nvl(sum(case when x.production_type ='7' and y.production_type ='7' then y.prod_qnty else 0 end),0) as sewingin_qnty, 
			nvl(sum(case when x.production_type ='2' and  y.production_type ='2' then y.prod_qnty else 0 end),0) as sewingout_qnty, 
			nvl(sum(case when x.production_type ='4' and y.production_type ='4' then y.prod_qnty else 0 end),0) as finishin_qnty, 
			nvl(sum(case when x.production_type ='3' and y.production_type ='3' then y.prod_qnty else 0 end),0) as iron_qnty, 
			nvl(sum(case when x.production_type ='8' and y.production_type ='8' then y.prod_qnty else 0 end),0) as finish_qnty, 
			nvl(sum(case when x.production_type ='5' and y.production_type ='5' then y.prod_qnty else 0 end),0) as poly_qty
			
			from 
			subcon_gmts_prod_dtls x, subcon_gmts_prod_col_sz y , subcon_ord_breakdown a,subcon_ord_mst e 
			where 
			x.id=y.dtls_id and y.ord_color_size_id=a.id and e.entry_form=238   and e.status_active=1   and a.job_no_mst=e.subcon_job   and e.is_deleted=0 
			   $style_comp  $cbo_buyer_name_cond   $all_po_id_cond
			 and x.status_active=1 and x.is_deleted=0  and a.status_active=1
			group by x.order_id, y.ord_color_size_id, a.item_id, a.color_id, a.size_id order by x.order_id,a.color_id ,a.size_id ";
			$prod_sql=sql_select($prod_sq);
		}
		
		$prod_color_size_data=array();
		foreach($prod_sql as $row)
		{	
			$color_size_qnty_prod[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['quantity_qc']+=$row[csf("cutting_qnty_qc")] ;
			$color_size_qnty_prod[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['replace_qnty_qc']+=$row[csf("replace_qnty_qc")];
			
			$color_size_qnty_prod[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['quantity_toprint']+=$row[csf("printing_qnty")];
			$color_size_qnty_prod[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['quantity_fromprint']+=$row[csf("printreceived_qnty")];
			$color_size_qnty_prod[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['quantity_toemb']+=$row[csf("emb_qnty")];
			$color_size_qnty_prod[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['quantity_fromemb']+=$row[csf("embreceived_qnty")];
			$color_size_qnty_prod[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['quantity_to_spwork']+=$row[csf("sp_qnty")];
			$color_size_qnty_prod[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['quantity_from_spwork']+=$row[csf("spreceived_qnty")];
			$color_size_qnty_prod[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['quantity_swinput']+=$row[csf("sewingin_qnty")];
			$color_size_qnty_prod[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['quantity_swoutput']+=$row[csf("sewingout_qnty")];
			$color_size_qnty_prod[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['quantity_poly']+=$row[csf("poly_qty")];
			$color_size_qnty_prod[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['quantity_packing']+=$row[csf("finishin_qnty")];
		  	$color_size_qnty_prod[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['color_idd']=$row[csf("color_number_id")];
			//$color[$row[csf("po_break_down_id")]][$row[csf("size_number_id")]]['color_ids'].=$row[csf("color_number_id")].'__';
			//$color_size_qnty_prod[$row[csf("color_number_id")]][$row[csf("style_ref_no")]][$row[csf("size_number_id")]]['quantity_exfact']+=$row[csf("finish_qnty")];
		}	
		//echo "<pre>";
		//print_r($color_size_qnty_prod);  

 
		ob_start();		
	
		?>
    	<div id="scroll_body" style="height:auto; width:1950px; margin:0 auto; padding:0;">
    	<fieldset>
    	<legend> Order and Size Wise</legend>
    	<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="1960" align="left">
		<!--<div id="scroll_body" style="overflow-y: scroll; width: 1620px; overflow-x: auto;">
    	<table width="1610" cellspacing="0" border="1" class="rpt_table" rules="all" id="">-->
		<caption><h2>Order and Size - Style NO: <? $style_ref_no=rtrim($style_ref,',');echo implode(",",array_unique(explode(",",$style_ref_no)));?> </h2></caption>
         
        <thead> 
        	<tr>
            	<th width="30">SL</th>
                <th width="110">Company</th>
                <th width="100">Buyer</th>
                <th width="110">Style</th>
                
            	<th width="200">PO</th>
            	<th width="200">Garments Item</th>
                <th width="200">Color</th>
     		    <th width="60">Size</th>
                <th width="60">Order Total</th>
                <th width="60">Cutt/ Qc Total</th>
     		    <th width="80">Cutt Short/ excess</th>
                <th width="60">Inp Total</th>
     		    <th width="80">Inp Short/ Excess</th>
                <th width="60">Sewing Output Total</th>
                <th width="80">Output Short/ excess</th>
     		    <th width="60">Poly Out Total</th>
                <th width="80">Poly Output Short/ excess</th>
                <th width="60"><p>Total Packing and Finishing</p></th>
     		    <th width="80"><p>Packing and finishing Short/ excess</p></th>
                <th width="60">Total Ex Factory</th>
     		    <th>Exfactory Short/ excess</th>
                
             </tr>
        </thead>
		</table>
		<div style="width:1960px; max-height:430px; overflow-y: scroll;" id="scroll_body">
		 	<table cellspacing="0" cellpadding="0"  width="1940"  border="1" rules="all" class="rpt_table" id="" >
			  	<tbody>  
		         <!--       
		           </tr>
		        </thead>
				</table>

				<table border="1" class="rpt_table" width="1610" rules="all" id="" >-->
			    <?
				//die;
			    /*foreach($color_size_sql as $row)
				{*/
				$i=0;
				foreach($po_color_size_data as $poID=>$breakDown_id)
				{					
					foreach($breakDown_id as $itemID=> $items)
					{
						$item_total_order_qty="";
						$item_total_lay_qty=0;
						$item_total_cutt_qty="";
						$item_total_cuttShortEx_qty="";
						
						$item_total_emblPrint="";
						$item_total_emblEmbro="";
						$item_total_emblSPwork="";
						
						$item_total_input_qty="";
						$item_total_inputShortEx_qty="";
						$item_total_sewing_qty="";
						$item_total_swoutShortEx_qty="";
						$item_total_poly_qty="";
						$item_total_polyShortEx_qty="";
						$item_total_carton_qty="";
						$item_total_cartonShortEx_qty="";
						$item_total_exfact_qty="";
						$item_total_exfactShortEx_qty="";
						//$gTotal_inputShortEx_qty="";
						foreach($items as $colorID=> $colors)
						{
							$total_order_qty="";
							$total_lay_qty=0;
							$total_cutt_qty="";
							$total_cuttShortEx_qty="";
							
							$total_emblPrint="";
							$total_emblEmbro="";
							$total_emblSPwork="";
							
							$total_input_qty="";
							$total_inputShortEx_qty="";
							$total_sewing_qty="";
							$total_swoutShortEx_qty="";
							$total_poly_qty="";
							$total_polyShortEx_qty="";
							$total_carton_qty="";
							$total_cartonShortEx_qty="";
							$total_exfact_qty="";
							$total_exfactShortEx_qty="";
							//$gTotal_inputShortEx_qty="";
							foreach($colors as $sizeNoID=> $val)
							{
								
								$bgcolor = ($i%2) ? "#ebf3ff" : "#ffffff";
								$i++;
								
								?>
						        <tr onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" bgcolor="<? echo $bgcolor;?>">
						        	<td width="30" align="center"><? echo $i; ?></td>
                                    <td width="110" style="word-wrap:break-word" align="center"><? echo $company_library[$po_no_arr[$poID]['company_id']]; ?></td>
                                    <td width="100" style="word-wrap:break-word" align="center"><? echo  $buyer_short_library[$po_no_arr[$poID]['party_id']]; ?></td>
                                    <td width="110"  style="word-wrap:break-word" align="center"><? echo  $po_no_arr[$poID]['style_ref_no']; ?></td>
                                    
									<td width="200" style="word-wrap:break-word" align="center"><? echo $po_no_library[$poID]; ?></td>
									<td width="200" style="word-wrap:break-word" align="left"><? echo $garments_item[$itemID]; ?></td>
						            <td width="200" style="word-wrap:break-word"><? echo  $color_Arr_library[$colorID]; //$color_Arr_library[$color_size_qnty_prod[$poID][$colorID][$sizeNoID]['color_idd']]; ?></td> 
						            <td width="60" align="center"><p><? echo $size_Arr_library[$sizeNoID]; ?>&nbsp;</p></td>
						            <td width="60" align="center"><? echo $po_color_size_data[$poID][$itemID][$colorID][$sizeNoID]['order_quantity'];//echo $row[csf('order_quantity')]; ?></td>
						           
						            <td width="60" align="center"><? echo $cuttQty=$color_size_qnty_prod[$poID][$itemID][$colorID][$sizeNoID]['quantity_qc']; ?></td>
							            <?
										$cut_shortExc= $po_color_size_data[$poID][$itemID][$colorID][$sizeNoID]['order_quantity']-$color_size_qnty_prod[$poID][$itemID][$colorID][$sizeNoID]['quantity_qc']; 
										if($cut_shortExc<0)
										{
											$cut_shortExc=abs($cut_shortExc);
											$cut_shortExc='('.$cut_shortExc.')'; $bg_tdColor='red';
										}
										else
										{
											$cut_shortExc=$cut_shortExc;$bg_tdColor='';
										}
										?>
						            <td width="80" align="center" style="color:<? echo $bg_tdColor; ?>"><? echo $cut_shortExc;?></td>
						            
						            
						            
						             <td width="60" align="center"><? echo $swoutQty=$color_size_qnty_prod[$poID][$itemID][$colorID][$sizeNoID]['quantity_swinput']; ?></td>
						             <?
										$qtyCuttQC=$color_size_qnty_prod[$poID][$itemID][$colorID][$sizeNoID]['quantity_qc'];
										$swInPut=$color_size_qnty_prod[$poID][$itemID][$colorID][$sizeNoID]['quantity_swinput'];
										
							            $input_shortExc=$qtyCuttQC-$swInPut;
										if($input_shortExc<0)
										{
											$input_shortExc=abs($input_shortExc);
											$input_shortExc='('.$input_shortExc.')'; $bg_tdColor='red';
										}
										else
										{
											$input_shortExc=$input_shortExc;$bg_tdColor='';
										}
										?>
						            <td width="60" align="center"  style="color:<? echo $bg_tdColor; ?>"><? echo $input_shortExc; ?></td>
                                     <td width="60" align="center"><? echo $inputQty=$color_size_qnty_prod[$poID][$itemID][$colorID][$sizeNoID]['quantity_swoutput']; ?></td>
						              <?
										$output_shortExc=$color_size_qnty_prod[$poID][$itemID][$colorID][$sizeNoID]['quantity_swinput']-$color_size_qnty_prod[$poID][$itemID][$colorID][$sizeNoID]['quantity_swoutput']; 
										if($output_shortExc<0)
										{
											$output_shortExc=abs($output_shortExc);
											$output_shortExc='('.$output_shortExc.')'; $bg_tdColor='red';
										}
										else
										{
											$output_shortExc=$output_shortExc;$bg_tdColor='';
										}
										?>
						            <td width="60" align="center" style="color:<? echo $bg_tdColor; ?>"><? echo $output_shortExc ?></td>
						            
						           
							           <td width="60" align="center"><? echo $polyQty=$color_size_qnty_prod[$poID][$itemID][$colorID][$sizeNoID]['quantity_poly']; ?></td>
							             <?
										$poly_output_shortExc=$color_size_qnty_prod[$poID][$itemID][$colorID][$sizeNoID]['quantity_swoutput']-$color_size_qnty_prod[$poID][$itemID][$colorID][$sizeNoID]['quantity_poly']; 
										if($poly_output_shortExc<0)
										{
											$poly_output_shortExc=abs($poly_output_shortExc);
											$poly_output_shortExc='('.$poly_output_shortExc.')'; $bg_tdColor='red';
										}
										else
										{
											$poly_output_shortExc=$poly_output_shortExc;$bg_tdColor='';
										}
										?>
						            <td width="60" align="center" style="color:<? echo $bg_tdColor; ?>"><? echo $poly_output_shortExc; ?></td>
							             <td width="60" align="center"><? echo $cartonQty=$color_size_qnty_prod[$poID][$itemID][$colorID][$sizeNoID]['quantity_packing']; ?></td>
							             <?
										$carton_shortExc=$color_size_qnty_prod[$poID][$itemID][$colorID][$sizeNoID]['quantity_poly']-$color_size_qnty_prod[$poID][$itemID][$colorID][$sizeNoID]['quantity_packing']; 
										if($carton_shortExc<0)
										{
											$carton_shortExc=abs($carton_shortExc);
											$carton_shortExc='('.$carton_shortExc.')'; $bg_tdColor='red';
										}
										else
										{
											$carton_shortExc=$carton_shortExc;$bg_tdColor='';
										}
										?>
						            <td width="80" align="center" style="color:<? echo $bg_tdColor; ?>"><? echo $carton_shortExc; ?></td>
							             <?
										$poly_output_shortExc=$color_size_qnty_prod[$poID][$itemID][$colorID][$sizeNoID]['quantity_swoutput']-$color_size_qnty_prod[$poID][$itemID][$colorID][$sizeNoID]['quantity_poly']; 
										if($poly_output_shortExc<0)
										{
											$poly_output_shortExc=abs($poly_output_shortExc);
											$poly_output_shortExc='('.$poly_output_shortExc.')'; $bg_tdColor='red';
										}
										else
										{
											$poly_output_shortExc=$poly_output_shortExc;$bg_tdColor='';
										}
										?>
						            <td width="60" align="center"><? echo $exFacQty=$ex_fact_qty_arr[$poID][$itemID][$colorID][$sizeNoID]['ex_fact_qty']; ?></td>
							             <?
										$exFactory_shortExc=$po_color_size_data[$poID][$itemID][$colorID][$sizeNoID]['order_quantity']-$ex_fact_qty_arr[$poID][$itemID][$colorID][$sizeNoID]['ex_fact_qty']; 
						
										
										if($exFactory_shortExc<0)
										{
											$exFactory_shortExc=abs($exFactory_shortExc);
											$exFactory_shortExc='('.$exFactory_shortExc.')'; $bg_tdColor='red';
										}
										else
										{
											$exFactory_shortExc=$exFactory_shortExc;$bg_tdColor='';
										}
										?>
						            <td align="center" style="color:<? echo $bg_tdColor; ?>"><? echo $exFactory_shortExc; ?></td>
										
						            
								</tr>
							    <?
								$total_order_qty+=$po_color_size_data[$poID][$itemID][$colorID][$sizeNoID]['order_quantity'];
							
								$total_lay_qty+=$layQty;
								$total_cutt_qty+=$cuttQty;
								$total_cuttShortEx_qty+=$po_color_size_data[$poID][$itemID][$colorID][$sizeNoID]['order_quantity']-$color_size_qnty_prod[$poID][$itemID][$colorID][$sizeNoID]['quantity_qc'];
								
								$total_emblPrint+=$emblishMentPrint;
								$total_emblEmbro+=$emblishMentEmbro;
								$total_emblSPwork+=$emblishMentSPwork;
								
								$total_input_qty+=$inputQty;
								$total_inputShortEx_qty+=$qtyCuttQC-$swInPut;
								$total_sewing_qty+=$swoutQty;
								$total_swoutShortEx_qty+=$color_size_qnty_prod[$poID][$itemID][$colorID][$sizeNoID]['quantity_swinput']-$color_size_qnty_prod[$poID][$itemID][$colorID][$sizeNoID]['quantity_swoutput']; 
								$total_poly_qty+=$polyQty;
								$total_polyShortEx_qty+=$color_size_qnty_prod[$poID][$itemID][$colorID][$sizeNoID]['quantity_swoutput']-$color_size_qnty_prod[$poID][$itemID][$colorID][$sizeNoID]['quantity_poly'];
								$total_carton_qty+=$cartonQty;
								$total_cartonShortEx_qty+=$color_size_qnty_prod[$poID][$itemID][$colorID][$sizeNoID]['quantity_poly']-$color_size_qnty_prod[$poID][$itemID][$colorID][$sizeNoID]['quantity_packing']; 
								$total_exfact_qty+=$exFacQty;
								$total_exfactShortEx_qty+=$po_color_size_data[$poID][$itemID][$colorID][$sizeNoID]['order_quantity']-$ex_fact_qty_arr[$poID][$itemID][$colorID][$sizeNoID]['ex_fact_qty'];

								// ====================== item wise ==================================
								$item_total_order_qty+=$po_color_size_data[$poID][$itemID][$colorID][$sizeNoID]['order_quantity'];
							
								$item_total_lay_qty+=$layQty;
								$item_total_cutt_qty+=$cuttQty;
								$item_total_cuttShortEx_qty+=$po_color_size_data[$poID][$itemID][$colorID][$sizeNoID]['order_quantity']-$color_size_qnty_prod[$poID][$itemID][$colorID][$sizeNoID]['quantity_qc'];
								
								$item_total_emblPrint+=$emblishMentPrint;
								$item_total_emblEmbro+=$emblishMentEmbro;
								$item_total_emblSPwork+=$emblishMentSPwork;
								
								$item_total_input_qty+=$inputQty;
								$item_total_inputShortEx_qty+=$qtyCuttQC-$swInPut;
								$item_total_sewing_qty+=$swoutQty;
								$item_total_swoutShortEx_qty+=$color_size_qnty_prod[$poID][$itemID][$colorID][$sizeNoID]['quantity_swinput']-$color_size_qnty_prod[$poID][$itemID][$colorID][$sizeNoID]['quantity_swoutput']; 
								$item_total_poly_qty+=$polyQty;
								$item_total_polyShortEx_qty+=$color_size_qnty_prod[$poID][$itemID][$colorID][$sizeNoID]['quantity_swoutput']-$color_size_qnty_prod[$poID][$itemID][$colorID][$sizeNoID]['quantity_poly'];
								$item_total_carton_qty+=$cartonQty;
								$item_total_cartonShortEx_qty+=$color_size_qnty_prod[$poID][$itemID][$colorID][$sizeNoID]['quantity_poly']-$color_size_qnty_prod[$poID][$itemID][$colorID][$sizeNoID]['quantity_packing']; 
								$item_total_exfact_qty+=$exFacQty;
								$item_total_exfactShortEx_qty+=$po_color_size_data[$poID][$itemID][$colorID][$sizeNoID]['order_quantity']-$ex_fact_qty_arr[$poID][$itemID][$colorID][$sizeNoID]['ex_fact_qty'];
							}
							?>
					        <tr class="gd-color3">
					        	<td width="30" align="center"><b></b></td>
                                 <td width="110" align="center"><? //echo $company_library[$po_no_arr[$poID]['company_id']]; ?></td>
                                 <td width="100" align="center"><? //echo  $buyer_short_library[$po_no_arr[$poID]['party_id']]; ?></td>
                                 <td width="110" align="center"><? //echo  $po_no_arr[$poID]['style_ref_no']; ?></td>
                                
					        	<td width="200" align="center"><b><? //echo $po_no_library[$poID]; ?></b></td>
					        	<td width="200" align="center"><b><? // echo $garments_item[$itemID]; ?></b></td>
                              
					            <td width="" align="right"><b>Color Wise PO Total=</b></td>
                                <td width="60" align="center"><b><? // echo $garments_item[$itemID]; ?></b></td>
					            
					            <td width="60" align="center"><b><? echo $total_order_qty; ?></b></td>
					           
					            <td width="60" align="center"><b><? echo $total_cutt_qty; ?></b></td>
					            
					             <? 
								 $bg_tdColor='';
								 $total_cuttShortEx_qtyy=$total_cuttShortEx_qty;
					            if($total_cuttShortEx_qtyy<0)
									{
										$total_cuttShortEx_qtyy=abs($total_cuttShortEx_qtyy);
										$total_cuttShortEx_qtyy='('.$total_cuttShortEx_qtyy.')'; $bg_tdColor='red';
									}
									else
									{
										$total_cuttShortEx_qtyy=$total_cuttShortEx_qty;$bg_tdColor='';
									}
					            ?>
					            <td width="80" align="center" style="color:<? echo $bg_tdColor; ?>"><b><? echo $total_cuttShortEx_qtyy; ?></b></td>
					            
					          
					            <td width="60" align="center"><b><? echo $total_input_qty; ?></b></td>
					           <?  $gTotal_inputShortEx_qty=$total_inputShortEx_qty; if($total_inputShortEx_qty<0){  $total_inputShortEx_qty=abs($total_inputShortEx_qty); $total_inputShortEx_qty='('.$total_inputShortEx_qty.')'; $bg_tdColor='red'; } else {  $total_inputShortEx_qty;$bg_tdColor='';  } ?>
					           
					            <td style="color:<? echo $bg_tdColor ?>" width="80" align="center"><b><?
								  echo $total_inputShortEx_qty; $total_inputShortEx_qty='';
								 ?></b></td>
					            <td width="60" align="center"><b><? echo $total_sewing_qty; ?></b></td>
					            <td width="80" align="center"><b><? echo $total_swoutShortEx_qty; ?></b></td>
					            <td width="60" align="center"><b><? echo $total_poly_qty; ?></b></td>
					            
					             <? 
								 $total_polyShortEx_qtyy=$total_polyShortEx_qty;
					            if($total_polyShortEx_qtyy<0)
									{
										$total_polyShortEx_qtyy=abs($total_polyShortEx_qtyy);
										$total_polyShortEx_qtyy='('.$total_polyShortEx_qtyy.')'; $bg_tdColor='red';
									}
									else
									{
										$total_polyShortEx_qtyy=$total_polyShortEx_qty;$bg_tdColor='';
									}
					            ?>
					            
					            <td width="80" align="center" style="color:<? echo $bg_tdColor ?>"><b><? echo $total_polyShortEx_qtyy; ?></b></td>
					            <td width="60" align="center"><b><? echo $total_carton_qty; ?></b></th>
					            
					             <? 
								 $total_cartonShortEx_qtyy=$total_cartonShortEx_qty;
					            if($total_cartonShortEx_qtyy<0)
									{
										$total_cartonShortEx_qtyy=abs($total_cartonShortEx_qtyy);
										$total_cartonShortEx_qtyy='('.$total_cartonShortEx_qtyy.')'; $bg_tdColor='red';
									}
									else
									{
										$total_cartonShortEx_qtyy=$total_cartonShortEx_qty;$bg_tdColor='';
									}
					            ?>
					            
					            
					            <td width="80" align="center" style="color:<? echo $bg_tdColor ?>"><b><? echo $total_cartonShortEx_qtyy; ?></b></td>
					            <td width="60" align="center"><b><? echo $total_exfact_qty; ?></b></td>
                                
                                 
					            
					             <? 
								 $total_exfactShortEx_qtyy=$total_exfactShortEx_qty;
					            if($total_exfactShortEx_qtyy<0)
									{
										$total_exfactShortEx_qtyy=abs($total_exfactShortEx_qtyy);
										$total_exfactShortEx_qtyy='('.$total_exfactShortEx_qtyy.')'; $bg_tdColor='red';
									}
									else
									{
										$total_exfactShortEx_qtyy=$total_exfactShortEx_qty;$bg_tdColor='';
									}
					            ?>
					            
					            <td align="center" style="color:<? echo $bg_tdColor ?>"><b><? echo $total_exfactShortEx_qtyy; ?></b></td>
					        </tr>
					        <?
					        $grandTotal_order_qty+=$total_order_qty;
							$total_order_qty=0;
							$grandTotal_lay_qty+=$total_lay_qty;
							$grandTotal_cutt_qty+=$total_cutt_qty;
							$total_lay_qty=0;
							$total_cutt_qty=0;
							$grandTotal_cuttShortEx_qty+=$total_cuttShortEx_qty;
							$total_cuttShortEx_qty=0;
							
							$grandTotal_emblPrint+=$total_emblPrint;
							$total_emblPrint=0;
							$grandTotal_emblEmbro+=$total_emblEmbro;
							$total_emblEmbro=0;
							$grandTotal_emblSPwork+=$total_emblSPwork;
							$total_emblSPwork=0;
							
							$grandTotal_input_qty+=$total_input_qty;
							$total_input_qty=0;
							$grandTotal_inputShortEx_qty+=$gTotal_inputShortEx_qty;
							$gTotal_inputShortEx_qty=0;
							$grandTotal_sewing_qty+=$total_sewing_qty;
							$total_sewing_qty=0;
							$grandTotal_swoutShortEx_qty+=$total_swoutShortEx_qty;
							$total_swoutShortEx_qty=0;
							$grandTotal_poly_qty+=$total_poly_qty;
							$total_poly_qty=0;
							$grandTotal_polyShortEx_qty+=$total_polyShortEx_qty;
							$total_polyShortEx_qty=0;
							$grandTotal_carton_qty+=$total_carton_qty;
							$total_carton_qty=0;
							$grandTotal_cartonShortEx_qty+=$total_cartonShortEx_qty;
							$total_cartonShortEx_qty=0;
							$grandTotal_exfact_qty+=$total_exfact_qty;
							$total_exfact_qty=0;
							$grandTotal_exfactShortEx_qty+=$total_exfactShortEx_qty;
							$total_exfactShortEx_qty=0;
						}

						?>
					    <tr class="gd-color">
				        	<td width="30" align="center"><b></b></td>
				        	<td width="110" align="center"><? //echo $company_library[$po_no_arr[$poID]['company_id']]; ?></td>
                            <td width="100" align="center"><? //echo  $buyer_short_library[$po_no_arr[$poID]['party_id']]; ?></td>
                            <td width="110" align="center"><? //echo  $po_no_arr[$poID]['style_ref_no']; ?></td>
                            
                            <td width="200" align="center"><b><? //echo $po_no_library[$poID]; ?></b></td>
                            
				        	<td width="200" align="center"><b><? //echo $garments_item[$itemID]; ?></b></td>
                           
				            <td width=""  align="right"><b>Item Wise PO Total=</b></td>
                             <td width="60" align="center"><b><? //echo $garments_item[$itemID]; ?></b></td>
				            
				            <td width="60" align="center"><b><? echo $item_total_order_qty; ?></b></td>
				            
				            <td width="60" align="center"><b><? echo $item_total_cutt_qty; ?></b></td>
				            
				             <? 
							 $bg_tdColor='';
							 $item_total_cuttShortEx_qtyy=$item_total_cuttShortEx_qty;
				            if($item_total_cuttShortEx_qtyy<0)
								{
									$item_total_cuttShortEx_qtyy=abs($item_total_cuttShortEx_qtyy);
									$item_total_cuttShortEx_qtyy='('.$item_total_cuttShortEx_qtyy.')'; $bg_tdColor='red';
								}
								else
								{
									$item_total_cuttShortEx_qtyy=$item_total_cuttShortEx_qty;$bg_tdColor='';
								}
				            ?>
				            <td width="80" align="center" style="color:<? echo $bg_tdColor; ?>"><b><? echo $item_total_cuttShortEx_qtyy; ?></b></td>
				            
				          
				            <td width="60" align="center"><b><? echo $item_total_input_qty; ?></b></td>
				           <?  $gTotal_inputShortEx_qty=$item_total_inputShortEx_qty; if($item_total_inputShortEx_qty<0){  $item_total_inputShortEx_qty=abs($item_total_inputShortEx_qty); $total_inputShortEx_qty='('.$total_inputShortEx_qty.')'; $bg_tdColor='red'; } else {  $total_inputShortEx_qty;$bg_tdColor='';  } ?>
				           
				            <td style="color:<? echo $bg_tdColor ?>" width="80" align="center"><b><?
							  echo $item_total_inputShortEx_qty; $item_total_inputShortEx_qty='';
							 ?></b></td>
				            <td width="60" align="center"><b><? echo $item_total_sewing_qty; ?></b></td>
				            <td width="80" align="center"><b><? echo $item_total_swoutShortEx_qty; ?></b></td>
				            <td width="60" align="center"><b><? echo $item_total_poly_qty; ?></b></td>
				            
				             <? 
							 $item_total_polyShortEx_qtyy=$item_total_polyShortEx_qty;
				            if($item_total_polyShortEx_qtyy<0)
								{
									$item_total_polyShortEx_qtyy=abs($item_total_polyShortEx_qtyy);
									$item_total_polyShortEx_qtyy='('.$item_total_polyShortEx_qtyy.')'; $bg_tdColor='red';
								}
								else
								{
									$item_total_polyShortEx_qtyy=$item_total_polyShortEx_qty;$bg_tdColor='';
								}
				            ?>
				            
				            <td width="80" align="center" style="color:<? echo $bg_tdColor ?>"><b><? echo $item_total_polyShortEx_qtyy; ?></b></td>
				            <td width="60" align="center"><b><? echo $item_total_carton_qty; ?></b></th>
				            
				             <? 
							 $item_total_cartonShortEx_qtyy=$item_total_cartonShortEx_qty;
				            if($total_cartonShortEx_qtyy<0)
								{
									$item_total_cartonShortEx_qtyy=abs($item_total_cartonShortEx_qtyy);
									$item_total_cartonShortEx_qtyy='('.$item_total_cartonShortEx_qtyy.')'; $bg_tdColor='red';
								}
								else
								{
									$item_total_cartonShortEx_qtyy=$item_total_cartonShortEx_qty;$bg_tdColor='';
								}
				            ?>
				            
				            
				            <td width="80" align="center" style="color:<? echo $bg_tdColor ?>"><b><? echo $item_total_cartonShortEx_qtyy; ?></b></td>
				            <td width="60" align="center"><b><? echo $item_total_exfact_qty; ?></b></td>
				            
				             <? 
							 $item_total_exfactShortEx_qtyy=$item_total_exfactShortEx_qty;
				            if($item_total_exfactShortEx_qtyy<0)
								{
									$item_total_exfactShortEx_qtyy=abs($item_total_exfactShortEx_qtyy);
									$item_total_exfactShortEx_qtyy='('.$item_total_exfactShortEx_qtyy.')'; $bg_tdColor='red';
								}
								else
								{
									$item_total_exfactShortEx_qtyy=$item_total_exfactShortEx_qty;$bg_tdColor='';
								}
				            ?>
				            
				            <td align="center" style="color:<? echo $bg_tdColor ?>"><b><? echo $item_total_exfactShortEx_qtyy; ?></b></td>
				        </tr>
				        <?				        
					} // end item wise
				
				
				} // end po wise
				//  grand total bottom part
				?>
		 		<tr class="gd-color2">
		            <td width="200" colspan="7" align="right"><b>Grand Total </b></td>
                     <td width="60" align="center"><b></b></td>
		            <td width="60" align="center"><b><? echo $grandTotal_order_qty; ?></b></td>
		           
		            <td width="60" align="center"><b><? echo $grandTotal_cutt_qty; ?></b></td>
		            
		            
		              <? 
		            if($grandTotal_cuttShortEx_qty<0)
						{
							$grandTotal_cuttShortEx_qty=abs($grandTotal_cuttShortEx_qty);
							$grandTotal_cuttShortEx_qty='('.$grandTotal_cuttShortEx_qty.')'; $bg_tdColor='red';
						}
						else
						{
							$grandTotal_cuttShortEx_qty=$grandTotal_cuttShortEx_qty;$bg_tdColor='';
						}
		            ?>
		            
		            <td width="80" align="center" style="color:<? echo $bg_tdColor; ?>"><b><? echo $grandTotal_cuttShortEx_qty; ?></b></td>
		            
		            
		            
		            <td width="60" align="center"><b><? echo $grandTotal_input_qty; ?></b></td>
		           <? if($grandTotal_inputShortEx_qty<0){  $grandTotal_inputShortEx_qty=abs($grandTotal_inputShortEx_qty); $grandTotal_inputShortEx_qty='('.$grandTotal_inputShortEx_qty.')'; $bg_tdColor='red'; } else {  $grandTotal_inputShortEx_qty;$bg_tdColor='';  } ?>
		            
		            <td style="color:<? echo $bg_tdColor ?>" width="80" align="center"><b><?
					  echo $grandTotal_inputShortEx_qty; 
					 ?></b></td>
		            <td width="60" align="center"><b><? echo $grandTotal_sewing_qty; ?></b></td>
		            <td width="80" align="center"><b><? echo $grandTotal_swoutShortEx_qty; ?></b></td>
		            <td width="60" align="center"><b><? echo $grandTotal_poly_qty; ?></b></td>
		            
		               
		            <? 
		            if($grandTotal_polyShortEx_qty<0)
						{
							$grandTotal_polyShortEx_qty=abs($grandTotal_polyShortEx_qty);
							$grandTotal_polyShortEx_qty='('.$grandTotal_polyShortEx_qty.')'; $bg_tdColor='red';
						}
						else
						{
							$grandTotal_polyShortEx_qty=$grandTotal_polyShortEx_qty;$bg_tdColor='';
						}
		            ?>
		            
		            
		            <td width="80" align="center" style="color:<? echo $bg_tdColor ?>"><b><? echo $grandTotal_polyShortEx_qty; ?></b></td>
		            <td width="60" align="center"><b><? echo $grandTotal_carton_qty; ?></b></th>
		            <td width="80" align="center"><b><? echo $grandTotal_cartonShortEx_qty; ?></b></td>
		            <td width="60" align="center"><b><? echo $grandTotal_exfact_qty; ?></b></td>
		            
		            <?  
		            if($grandTotal_exfactShortEx_qty<0)
						{
							$grandTotal_exfactShortEx_qty=abs($grandTotal_exfactShortEx_qty);
							$grandTotal_exfactShortEx_qty='('.$grandTotal_exfactShortEx_qty.')'; $bg_tdColor='red';
						}
						else
						{
							$grandTotal_exfactShortEx_qty=$grandTotal_exfactShortEx_qty;$bg_tdColor='';
						}
		            ?>
		            
		            <td align="center" style="color:<? echo $bg_tdColor ?>"><b><? echo $grandTotal_exfactShortEx_qty; ?></b></td>
                     
		        </tr>
		        <?
				//	
			
				//}
				?>
			    </tbody>
		    </table>
	    </div>
	    </fieldset> 
		</div>   	
	  
		<?
	}
	
	// $new_link=create_delete_report_file( $html, 1, 1, "../../../" );	
	// echo "$html**$type";
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename,'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data####$filename####$type";
	exit();	
}
//-------------------------------------------END Show Date Wise------------------------

if($action=="update_tna_progress_comment")
{
	//echo load_html_head_contents("TNA Info","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	
	if($db_type==0) $blank_date="0000-00-00"; else $blank_date="";

	$tna_task_arr=return_library_array( "select task_name, task_short_name from lib_tna_task",'task_name','task_short_name');
	
	$tna_task_id=array(); $plan_start_array=array(); $plan_finish_array=array();
	$actual_start_array=array();
	$actual_finish_array=array();
	
	$notice_start_array=array();
	$notice_finish_array=array();
	
	$task_sql= sql_select("select a.task_number,a.task_start_date,a.task_finish_date,a.actual_start_date,a.actual_finish_date,a.notice_date_start,a.notice_date_end from tna_process_mst a, lib_tna_task b where a.task_number=b.task_name and a.template_id=$template_id and a.po_number_id=$po_id order by b.task_sequence_no asc");
	foreach ($task_sql as $row_task)
	{
		$tna_task_id[]=$row_task[csf("task_number")];
		
		$plan_start_array[$row_task[csf("task_number")]] =$row_task[csf("task_start_date")];
		$plan_finish_array[$row_task[csf("task_number")]]=$row_task[csf("task_finish_date")];
		
		$actual_start_array[$row_task[csf("task_number")]] =$row_task[csf("actual_start_date")];
		$actual_finish_array[$row_task[csf("task_number")]]=$row_task[csf("actual_finish_date")];
		
		$notice_start_array[$row_task[csf("task_number")]] =$row_task[csf("notice_date_start")];
		$notice_finish_array[$row_task[csf("task_number")]]=$row_task[csf("notice_date_end")];
	}
	
	
	
	$comments_array=array(); $responsible_array=array();
	$res_comm_sql= sql_select("select task_id, comments, responsible from tna_progress_comments where tamplate_id=$template_id and order_id=$po_id");
	foreach ($res_comm_sql as $row_res_comm)
	{
		$comments_array[$row_res_comm[csf("task_id")]] =$row_res_comm[csf("comments")];
		$responsible_array[$row_res_comm[csf("task_id")]]=$row_res_comm[csf("responsible")];
	}
	
	$execution_time_array=array();
	$execution_time_sql= sql_select("select tna_task_id, execution_days from tna_task_template_details where task_template_id=$template_id");
	foreach ($execution_time_sql as $row_execution_time)
	{
		$execution_time_array[$row_execution_time[csf("tna_task_id")]] =$row_execution_time[csf("execution_days")];
	}
	
	$lead_time=return_library_array("select task_template_id,lead_time from tna_task_template_details group by task_template_id,lead_time","task_template_id","lead_time");
	$company_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name"  );
    $buyer_short_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name"  );
	?>


	<fieldset style="width:1010px"> 
        <div class="form_caption" align="center"><strong>TNA Progress Comment</strong></div>
        <table style="margin-top:10px" width="1000" border="1" rules="all" class="rpt_table">
            <?
			$sql ="select b.company_name,b.buyer_name,a.po_number,b.job_no,b.style_ref_no,b.gmts_item_id,a.po_received_date,a.shipment_date from wo_po_break_down a,wo_po_details_master b where a.id=$po_id and a.job_no_mst=b.job_no";
			//echo $sql;die;
			$result=sql_select($sql);
            foreach($result as $row)
            {
            ?>
            	<thead>
                    <tr bgcolor="#E9F3FF">
                        <th width="130">Company</th>
                        <td width="196" style="padding-left:5px"><? echo $company_library[$row[csf('company_name')]];  ?></td>
                        <th width="130">Buyer</th>
                        <td width="186" style="padding-left:5px"><? echo $buyer_short_library[$row[csf('buyer_name')]];  ?></td>
                        <th width="130">Order No</th>
                        <td width="186" style="padding-left:5px"><p><? echo $row[csf('po_number')]; ?></p></td>
                    </tr>
                    <tr bgcolor="#FFFFFF">
                        <th>Style Ref.</th>
                        <td style="padding-left:5px"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                        <th>RMG Item</th>
                        <td style="padding-left:5px"><p><? echo $garments_item[$row[csf('gmts_item_id')]]; ?></p></td>
                        <th>Order Recv. Date</th>
                        <td style="padding-left:5px"><? echo change_date_format($row[csf('po_received_date')]); ?></td>
                    </tr>
                    <tr bgcolor="#E9F3FF">
                        <th>Ship Date</th>
                        <td style="padding-left:5px"><? echo change_date_format($row[csf('shipment_date')]); ?></td>
                        <th>Lead Time</th>
                        <td style="padding-left:5px">
                            <?
								$template_id=str_replace("'","",$template_id);
								
								if($tna_process_type==1)
								{
									$lead_timee=$lead_time[$template_id];
								}
								else
								{
									$lead_timee=$template_id;
								}
								//echo $lead_time=return_field_value("lead_time","tna_task_template_details", "task_template_id='$template_id' and status_active=1 and is_deleted=0");
								echo $lead_timee;
							?>
                        </td>
                        <th>Job Number</th>
                        <td style="padding-left:5px">
							<? echo $row[csf('job_no')];   ?>
                        </td>
                    </tr>
                </thead>
            <?
            }
            ?>
        </table>
        <table style="margin-top:5px" cellpadding="0" width="1000" class="rpt_table" rules="all" border="1">
            <thead>
                <th width="50">Task No</th>
                <th width="150">Task Name</th>
                <th width="60">Allowed Days</th>
                <th width="80">Plan Start Date</th>
                <th width="80">Plan Finish Date</th>
                <th width="80">Actual Start Date</th>
                <th width="80">Actual Finish Date</th>
                <th width="80">Start Delay/ Early By</th>
                <th width="80">Finish Delay/ Early By</th>
                <th width="100">Responsible</th>
                <th>Comments</th>
            </thead> 	 	
        </table>
        
          
        
            <table cellpadding="0" width="1000" cellspacing="0" border="1" rules="all" class="rpt_table">
                <? 
				
				
				$i=1;
                foreach($tna_task_id as $key)
                { 
                    if($i%2==0) $trcolor="#E9F3FF"; else $trcolor="#FFFFFF";
					
					$bgcolor1=""; $bgcolor="";
									
					if ($plan_start_array[$key]!=$blank_date) 
					{
						if (strtotime($notice_start_array[$key])<=strtotime(date("Y-m-d",time())) && strtotime(date("Y-m-d",time()))<=strtotime($plan_start_array[$key]))  $bgcolor="#FFFF00";
						else if (strtotime($plan_start_array[$key])<strtotime(date("Y-m-d",time())))  $bgcolor="#FF0000";
						else $bgcolor="";
						
					}
					 
					if ($plan_finish_array[$key]!=$blank_date) {
						if (strtotime($notice_finish_array[$key])<=strtotime(date("Y-m-d",time())) && strtotime(date("Y-m-d",time()))<=strtotime($plan_finish_array[$key]))  $bgcolor1="#FFFF00";
						else if (strtotime($plan_finish_array[$key])<strtotime(date("Y-m-d",time())))  $bgcolor1="#FF0000"; else $bgcolor1="";
					}
					
					if ($actual_start_array[$key]!=$blank_date) $bgcolor="";
					if ($actual_finish_array[$key]!=$blank_date) $bgcolor1="";
					
					// Delay / Early............
									
					$bgcolor5=""; $bgcolor6="";
					$delay=""; $early="";
					
					if($actual_start_array[$key]!=$blank_date)
					{
						$start_diff1 = datediff( "d", $actual_start_array[$key], $plan_start_array[$key]);
						$finish_diff1 = datediff( "d", $actual_finish_array[$key], $plan_finish_array[$key]);
						
						$start_diff=$start_diff1-1;
						$finish_diff=$finish_diff1-1;
						
						if($start_diff<0)
						{
							$bgcolor5="#2A9FFF";	//Blue
							$start="(Delay)";
						}
						if($start_diff>0)
						{
							$bgcolor5="";
							$start="(Early)";
							
						}
						if($finish_diff<0)
						{
							$bgcolor6="#2A9FFF";
							$finish="(Delay)";
						}
						if($finish_diff>0)
						{	
							$bgcolor6="";
							$finish="(Early)";
						}
						
						
					}
					else
					{
						if(date("Y-m-d")>$plan_start_array[$key])
						{
							$start_diff1 = datediff( "d", $plan_start_array[$key], date("Y-m-d"));
							$start_diff=$start_diff1-1;
							$bgcolor5="#FF0000";		//Red
							$start="(Delay)";
						}
						if(date("Y-m-d")>$plan_finish_array[$key])
						{
							$finish_diff1 = datediff( "d", $plan_finish_array[$key], date("Y-m-d"));
							$finish_diff=$finish_diff1-1;
							$bgcolor6="#FF0000";
							$finish="(Delay)";
						}
						if(date("Y-m-d")<=$plan_start_array[$key])
						{
							$start_diff = "";
							$bgcolor5="";
							$start="(Ac. Start Dt. Not Found)";
						}
						if(date("Y-m-d")<=$plan_finish_array[$key])
						{
							$finish_diff = "";
							$bgcolor6="";
							$finish="(Ac. Finish Dt. Not Found)";
							
						}
					}
							
                    ?>
                    <tr bgcolor="<? echo $trcolor; ?>" id="tr_<? echo $i; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')">
                        <td align="center" width="50"><? echo $i; ?></td>
                        <td width="150"><? echo $tna_task_arr[$key]; ?></td>
                        <td align="center" width="60"><? echo datediff( "d", $plan_start_array[$key],$plan_finish_array[$key]);//$execution_time_array[$key]; ?></td>
                        <td align="center" width="80"><? echo change_date_format($plan_start_array[$key]); ?>&nbsp;</td>
                        <td align="center" width="80"><? echo change_date_format($plan_finish_array[$key]); ?>&nbsp;</td>
                        <td align="center" width="80" bgcolor="<? echo $bgcolor;  ?>">
                            <? 
                                if($actual_start_array[$key]=="0000-00-00" || $actual_start_array[$key]=="") echo "&nbsp;";
                                else echo change_date_format($actual_start_array[$key]);
                            ?>
                        </td>
                        <td align="center" width="80" bgcolor="<? echo $bgcolor1;  ?>">
                            <?  
                                 if($actual_finish_array[$key]=="0000-00-00" || $actual_finish_array[$key]=="") echo "&nbsp;";
                                 else echo change_date_format($actual_finish_array[$key]);
                            ?>
                        </td>
                        <td align="center" width="80" bgcolor="<? echo $bgcolor5;  ?>">
							<?  
                                echo $start_diff." ".$start;
                            ?>
                        </td>
                        <td align="center" width="80" bgcolor="<? echo $bgcolor6;  ?>">
                            <?  
                                echo $finish_diff." ".$finish;
                            ?>
                        </td>

                        <td width="100"><p><? echo $responsible_array[$key]; ?>&nbsp;</p></td>
                        <td><p><? echo $comments_array[$key]; ?>&nbsp;</p></td>
                    </tr>
              	<? 
                    $i++;
                }
                ?>
            </table>
    </fieldset>
	<?
	exit();
}

if($action=='OrderPopup')
{
	echo load_html_head_contents("Order Wise Production Report", "../../../", 1, 1,$unicode,'','');
	
	$po_break_down_id=$_REQUEST['po_break_down_id'];
	$item_id=$_REQUEST['item_id'];
	$company_name=str_replace("'","",$_REQUEST['company_name']);
	$color_variable_setting=return_field_value("ex_factory","variable_settings_production","company_name='$company_name' and variable_list=1 and status_active=1 and is_deleted=0","ex_factory");
	$ex_fact_qty_arr=array();
	if($color_variable_setting==2 || $color_variable_setting==3)
	{
		$sql_exfect="SELECT c.color_number_id, c.size_number_id, sum(b.production_qnty) as production_qnty from pro_ex_factory_mst a,pro_ex_factory_dtls b, wo_po_color_size_breakdown c where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id in ($po_break_down_id) and a.item_number_id=$item_id and a.status_active=1 and a.is_deleted=0 group by  c.color_number_id, c.size_number_id";
		//echo $sql_exfect."<br>";
		$sql_result_exfact=sql_select($sql_exfect);
		foreach($sql_result_exfact as $row)
		{
			$ex_fact_qty_arr[$row[csf("color_number_id")]][$row[csf("size_number_id")]]=$row[csf("production_qnty")];
		}
	}
	
	//var_dump($color_variable_setting);die;
	//echo "select a.id, a.color_number_id, a.size_number_id from wo_po_color_size_breakdown a where a.status_active=1 and a.color_number_id>0 and a.size_number_id>0 and a.po_break_down_id in (".$po_break_down_id.")";die;
	$color_size_sql=sql_select( "select a.id, a.color_number_id, a.size_number_id from wo_po_color_size_breakdown a where   a.color_number_id>0 and a.size_number_id>0 and a.po_break_down_id in (".$po_break_down_id.") order by a.color_order,a.size_order");
	$color_size_data=array();
	foreach($color_size_sql as $row)
	{
		//$test_data[]=$row[csf("color_number_id")];
		
		$color_size_data[$row[csf("id")]]["color_number_id"]=$row[csf("color_number_id")];
		$color_size_data[$row[csf("id")]]["size_number_id"]=$row[csf("size_number_id")];
		$color_library[$row[csf("color_number_id")]]=$row[csf("color_number_id")];
		$size_library[$row[csf("size_number_id")]]=$row[csf("size_number_id")];
		
	}
	
	//var_dump($color_size_data);die;
	
	//echo "<pre>";print_r($color_library);
	//echo "<pre>";print_r($size_library);die;
	
	//$color_library=sql_select("select distinct(color_number_id) as color_number_id from wo_po_color_size_breakdown where po_break_down_id in ($po_break_down_id) and color_mst_id!=0 and status_active=1 ");
	//$size_library=sql_select("select distinct(size_number_id) as size_number_id from wo_po_color_size_breakdown where po_break_down_id in ($po_break_down_id) and size_mst_id!=0 and status_active=1");
	
	if($db_type==0)
	{
		$prod_sql= sql_select("SELECT a.po_break_down_id, c.color_size_break_down_id,  
				IFNULL(sum(CASE WHEN a.production_type ='1' THEN c.production_qnty  ELSE 0 END),0) AS cutting_qnty,
				IFNULL(sum(CASE WHEN a.production_type ='2' AND a.embel_name=1 THEN c.production_qnty ELSE 0 END),0) AS printing_qnty,
				IFNULL(sum(CASE WHEN a.production_type ='3' AND a.embel_name=1 THEN c.production_qnty ELSE 0 END),0) AS printreceived_qnty, 
				IFNULL(sum(CASE WHEN a.production_type ='2' AND a.embel_name=2 THEN c.production_qnty ELSE 0 END),0) AS emb_qnty,
				IFNULL(sum(CASE WHEN a.production_type ='3' AND a.embel_name=2 THEN c.production_qnty ELSE 0 END),0) AS embreceived_qnty, 
				IFNULL(sum(CASE WHEN a.production_type ='2' AND a.embel_name=3 THEN c.production_qnty ELSE 0 END),0) AS wash_qnty,
				IFNULL(sum(CASE WHEN a.production_type ='3' AND a.embel_name=3 THEN c.production_qnty ELSE 0 END),0) AS washreceived_qnty, 
				IFNULL(sum(CASE WHEN a.production_type ='2' AND a.embel_name=4 THEN c.production_qnty ELSE 0 END),0) AS sp_qnty,
				IFNULL(sum(CASE WHEN a.production_type ='3' AND a.embel_name=4 THEN c.production_qnty ELSE 0 END),0) AS spreceived_qnty, 
				IFNULL(sum(CASE WHEN a.production_type ='4' THEN c.production_qnty ELSE 0 END),0) AS sewingin_qnty,
				IFNULL(sum(CASE WHEN a.production_type ='5' THEN c.production_qnty ELSE 0 END),0) AS sewingout_qnty,
				IFNULL(sum(CASE WHEN a.production_type ='6' THEN c.production_qnty ELSE 0 END),0) AS finishin_qnty, 
				IFNULL(sum(CASE WHEN a.production_type ='7' THEN c.production_qnty ELSE 0 END),0) AS iron_qnty,
				IFNULL(sum(CASE WHEN a.production_type ='8' THEN c.production_qnty ELSE 0 END),0) AS finish_qnty,
				IFNULL(sum(CASE WHEN a.production_type ='11' THEN c.production_qnty ELSE 0 END),0) AS poly_qty
			from 
				pro_garments_production_mst a, pro_garments_production_dtls c
			where  
				a.id=c.mst_id and a.po_break_down_id in (".$po_break_down_id.") and a.item_number_id=$item_id and c.status_active=1 and a.status_active=1
			group by a.po_break_down_id,c.color_size_break_down_id");
				/*IFNULL(sum(CASE WHEN c.production_type ='2' THEN c.production_qnty ELSE 0 END),0) AS printing_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='3' THEN c.production_qnty ELSE 0 END),0) AS printreceived_qnty, */
	}
	else
	{
		$prod_sql=sql_select("SELECT a.po_break_down_id, c.color_size_break_down_id, 
				NVL(sum(CASE WHEN a.production_type ='1' THEN c.production_qnty  ELSE 0 END),0) AS cutting_qnty,
				NVL(sum(CASE WHEN a.production_type ='2' AND a.embel_name=1 THEN c.production_qnty ELSE 0 END),0) AS printing_qnty,
				NVL(sum(CASE WHEN a.production_type ='3' AND a.embel_name=1 THEN c.production_qnty ELSE 0 END),0) AS printreceived_qnty,
				NVL(sum(CASE WHEN a.production_type ='2' AND a.embel_name=2 THEN c.production_qnty ELSE 0 END),0) AS emb_qnty,
				NVL(sum(CASE WHEN a.production_type ='3' AND a.embel_name=2 THEN c.production_qnty ELSE 0 END),0) AS embreceived_qnty, 
				NVL(sum(CASE WHEN a.production_type ='2' AND a.embel_name=3 THEN c.production_qnty ELSE 0 END),0) AS wash_qnty,
				NVL(sum(CASE WHEN a.production_type ='3' AND a.embel_name=3 THEN c.production_qnty ELSE 0 END),0) AS washreceived_qnty, 
				NVL(sum(CASE WHEN a.production_type ='2' AND a.embel_name=4 THEN c.production_qnty ELSE 0 END),0) AS sp_qnty,
				NVL(sum(CASE WHEN a.production_type ='3' AND a.embel_name=4 THEN c.production_qnty ELSE 0 END),0) AS spreceived_qnty, 
				NVL(sum(CASE WHEN a.production_type ='4' THEN c.production_qnty ELSE 0 END),0) AS sewingin_qnty,
				NVL(sum(CASE WHEN a.production_type ='5' THEN c.production_qnty ELSE 0 END),0) AS sewingout_qnty,
				NVL(sum(CASE WHEN a.production_type ='6' THEN c.production_qnty ELSE 0 END),0) AS finishin_qnty, 
				NVL(sum(CASE WHEN a.production_type ='7' THEN c.production_qnty ELSE 0 END),0) AS iron_qnty,
				NVL(sum(CASE WHEN a.production_type ='8' THEN c.production_qnty ELSE 0 END),0) AS finish_qnty,
				NVL(sum(CASE WHEN a.production_type ='11' THEN c.production_qnty ELSE 0 END),0) AS poly_qty
			from 
				pro_garments_production_mst a, pro_garments_production_dtls c
			where  
				a.id=c.mst_id and a.po_break_down_id in (".$po_break_down_id.") and a.item_number_id=$item_id and c.status_active=1 and a.status_active=1
			group by a.po_break_down_id,c.color_size_break_down_id");	
				/*NVL(sum(CASE WHEN c.production_type ='2' THEN  c.production_qnty  ELSE 0 END),0) AS printing_qnty,
				NVL(sum(CASE WHEN c.production_type ='3' THEN  c.production_qnty  ELSE 0 END),0) AS printreceived_qnty, */
	}
	$prod_color_size_data=array();
	foreach($prod_sql as $row)
	{
		$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["cutting_qnty"]+=$row[csf("cutting_qnty")];
		$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["printing_qnty"]+=$row[csf("printing_qnty")];
		$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["printreceived_qnty"]+=$row[csf("printreceived_qnty")];
		$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["emb_qnty"]+=$row[csf("emb_qnty")];
		$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["embreceived_qnty"]+=$row[csf("embreceived_qnty")];
		$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["wash_qnty"]+=$row[csf("wash_qnty")];
		$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["washreceived_qnty"]+=$row[csf("washreceived_qnty")];
		$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["sp_qnty"]+=$row[csf("sp_qnty")];
		$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["spreceived_qnty"]+=$row[csf("spreceived_qnty")];
		$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["sewingin_qnty"]+=$row[csf("sewingin_qnty")];
		$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["sewingout_qnty"]+=$row[csf("sewingout_qnty")];
		$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["finishin_qnty"]+=$row[csf("finishin_qnty")];
		$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["iron_qnty"]+=$row[csf("iron_qnty")];
		$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["finish_qnty"]+=$row[csf("finish_qnty")];
		$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["poly_qty"]+=$row[csf("poly_qty")];
	}
	//echo "<pre>";
	//print_r($prod_color_size_data);die;
	
	
	?>
	 <div id="data_panel" align="center" style="width:100%">
	         <script>
			 	function new_window()
				 {
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				 }
	         </script>
	 	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
	 </div>
	  
	<div style="width:700px" align="center" id="details_reports"> 
	  	<legend>Color And Size Wise Summary</legend>
	    <table id="tbl_id" class="rpt_table" width="" border="1" rules="all" >
	    	<thead>
	        	<tr>
	            	<th width="100">Buyer</th>
	                <th width="100">Job Number</th>
	                <th width="100">Style Name</th>
	                <th width="300">Order Number</th>
	                <th width="100">Ship Date</th>
	                <th width="100">Item Name</th>
	                <th width="100">Order Qty.</th>
	            </tr>
	        </thead>
	       	<?
	        	$buyer_short_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name"  );
				if($db_type==0)
				{
	 				$sql = "SELECT a.job_no_mst,group_concat(distinct(d.order_quantity)) as po_number,a.pub_shipment_date, sum(a.po_quantity) as po_quantity,a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio,c.gmts_item_id,d.item_number_id
						from wo_po_break_down a, wo_po_details_master b, wo_po_details_mas_set_details c,wo_po_color_size_breakdown d
						where a.job_no_mst=b.job_no and b.job_no=c.job_no and a.id in ($po_break_down_id) and c.gmts_item_id=$item_id and d.item_number_id=$item_id and a.id=d.po_break_down_id and b.job_no=d.job_no_mst and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
				}
				else
				{
					$sql = "SELECT a.job_no_mst, LISTAGG(a.po_number, ',') WITHIN GROUP (ORDER BY a.id) as po_number,max(a.pub_shipment_date) as pub_shipment_date, sum(d.order_quantity) as po_quantity,a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio,c.gmts_item_id,d.item_number_id 
						from wo_po_break_down a, wo_po_details_master b, wo_po_details_mas_set_details c,wo_po_color_size_breakdown d
						where a.job_no_mst=b.job_no and b.job_no=c.job_no and a.id in ($po_break_down_id) and c.gmts_item_id=$item_id and d.item_number_id=$item_id and a.id=d.po_break_down_id and b.job_no=d.job_no_mst and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.job_no_mst, a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio,c.gmts_item_id,d.item_number_id";
				}
				//echo $sql;die;
				$resultRow=sql_select($sql);
					
				$cons_embr=return_field_value("sum(cons_dzn_gmts) as cons_dzn_gmts","wo_pre_cost_embe_cost_dtls","job_no='".$resultRow[0][csf("job_no_mst")]."' and status_active=1 and is_deleted=0","cons_dzn_gmts");
				
	 		?> 
	        <tr>
	        	<td><? echo $buyer_short_library[$resultRow[0][csf("buyer_name")]]; ?></td>
	            <td><p><? echo $resultRow[0][csf("job_no_mst")]; ?></p></td>
	            <td><p><? echo $resultRow[0][csf("style_ref_no")]; ?></p></td>
	            <td><p><? echo implode(",",array_unique(explode(",",$resultRow[0][csf("po_number")]))); ?></p></td>
	            <td><? echo change_date_format($resultRow[0][csf("pub_shipment_date")]); ?></td>
	            <td><? echo $garments_item[$item_id]; ?></td>
	            <td><? echo $resultRow[0][csf("po_quantity")]*$resultRow[0][csf("set_item_ratio")]; ?></td>
	        </tr>
	         <?
	         $prod_sewing_sql=sql_select("SELECT sum(alter_qnty) as alter_qnty, sum(reject_qnty) as reject_qnty from pro_garments_production_mst where production_type=5 and po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and is_deleted=0 and status_active=1");
			 foreach($prod_sewing_sql as $sewingRow);
			?> 	
	        <tr>
	        	<td colspan="2">Total Alter Sewing Qty : <b><? echo $sewingRow[csf("alter_qnty")]; ?></b></td>
	        	<td colspan="2">Total Reject Sewing Qty : <b><? echo $sewingRow[csf("reject_qnty")]; ?></b></td>
	            <td colspan="2">Pack Assortment: <b><? echo $packing[$resultRow[csf("packing")]]; ?></b></td>
	        </tr>
	    </table>
	    <?
					  
		  $size_Arr_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
		  $count = count($size_library);	
		  $width= $count*70+350; 		
		  	  
		?>
	    <table id="tblDtls_id" class="rpt_table" width="<? echo $width; ?>" border="1" rules="all" >
		 	<thead>
	        	<tr>
	            	<th width="100">Color Name</th>
	                <th width="170">Production Type</th>
	 				<?
					foreach($size_library as $sizeRes)
					{
					 	?><th width="80"><? echo $size_Arr_library[$sizeRes]; ?></th><?
					}
					?>
	     		    <th width="60">Total</th>
	           </tr>
	        </thead>
	        <?
			  //var_dump($color_library);
	       		$cut_lay_sqls="select b.color_id,c.size_id,sum(c.size_qty) as qty from  ppl_cut_lay_mst a,  ppl_cut_lay_dtls b, ppl_cut_lay_bundle c where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id and c.order_id in($po_break_down_id) and b.gmt_item_id=$item_id group by b.color_id,c.size_id";
				foreach(sql_select($cut_lay_sqls) as $k=>$vl)
				{
					$cut_lay_arr[$vl[csf("color_id")]][$vl[csf("size_id")]]=$vl[csf("qty")];
					$cut_lay_total[$vl[csf("color_id")]]+=$vl[csf("qty")];
				}
			  foreach($color_library as $colorRes)
			  {
				  if($color_variable_setting==2 || $color_variable_setting==3) $row_span=18; else $row_span=17;  
				?>	  
				<tr>
					<td rowspan="<? echo $row_span; ?>"><? echo $color_Arr_library[$colorRes]; ?></td>
				
	 			<?
	            	  $i=0;$j=0;$sqlPart="";
					  foreach($size_library as $sizeRes)
					  {
						  $i++;$j++;
						  if($i>1) $sqlPart .=",";
						  $sqlPart .= 'SUM( CASE WHEN color_number_id='.$colorRes.' and size_number_id='.$sizeRes.' THEN order_quantity ELSE 0 END ) as '."col".$i;
						  $sqlPart .= ',SUM( CASE WHEN color_number_id='.$colorRes.' and size_number_id='.$sizeRes.' THEN plan_cut_qnty ELSE 0 END ) as '."pcut".$i;
						  $sqlPart .= ',SUM( CASE WHEN color_number_id='.$colorRes.' and size_number_id='.$sizeRes.' THEN excess_cut_perc ELSE 0 END ) as '."excess_cut".$i;
					  }
					  if($j>1)
					  {
						 $sqlPart .=',SUM( CASE WHEN color_number_id='.$colorRes.' THEN order_quantity ELSE 0 END ) as totalorderqnty';
						 $sqlPart .=',SUM( CASE WHEN color_number_id='.$colorRes.' THEN plan_cut_qnty ELSE 0 END ) as totalplancutqnty';
					  }
					$sql = sql_select("select avg(excess_cut_perc) as avg_excess_cut_perc,max(excess_cut_perc) as excess_cut_perc,". $sqlPart ." from wo_po_color_size_breakdown where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and status_active in(1,2,3)  and is_deleted=0");
					//echo $sql;die;
					foreach($sql as $resRow); 
	 					$bgcolor1="#E9F3FF"; 
						$bgcolor2="#FFFFFF";
						
					?>
						</tr>
						<tr bgcolor="<? echo $bgcolor1; ?>">
							<td><b>Order Quantity</b></td>	
	                        <? for($k=1;$k<=$i;$k++) {	$col = 'col'.$k; ?>	
	                         	<td><? echo $resRow[csf($col)]; ?></td>
							<? } ?>
	                         <td><? echo $resRow[csf("totalorderqnty")]; ?></td> 
						</tr>
	                    <tr bgcolor="<? echo $bgcolor2; ?>">
	                    	<td><b>Plan To Cut (AVG <? echo number_format($resRow[csf("avg_excess_cut_perc")],2); ?>)% </b></td>	
	                        <? for($k=1;$k<=$i;$k++){ $col = 'pcut'.$k;$excess_cut = 'excess_cut'.$k;	?>	
	                         	<td title="Excess Cut <? echo $resRow[csf($excess_cut)]; ?>%"><? echo $resRow[csf($col)]; ?></td>
							<? } ?>
	                         <td><? echo $resRow[csf("totalplancutqnty")]; ?></td> 
	                    </tr>

	                    <tr bgcolor="<? echo $bgcolor1; ?>">
							<td><b>Cut and Lay</b></td>	
	                        <? foreach($size_library as $size_id=>$size_val){?>	
	                         	<td><? echo $cut_lay_arr[$colorRes][$size_id]; ?></td>
							<? } ?>
	                         <td><? echo $cut_lay_total[$colorRes]; ?></td> 
						</tr>


						
	                <?
	 				$total_cutting=0;$total_sew_in=0;$total_sew_out=0;$total_fin_in=0;$total_fin_out=0;$total_iron_out=0; $total_exfact_qnty=0;
					$total_print_issue=0;$total_print_rcv=0;$total_embro_issue=0;$total_embro_rcv=0; $total_sp_issue=0;$total_sp_rcv=0; $total_wash_issue=0; $total_wash_rcv=0; $total_poly=0;
					$cutting_html='';$sewin_html='';$sewout_html=''; $poly_html=''; $finisin_html='';$finisout_html='';$iron_html=''; $exfact_html='';
					$printiss_html=''; $printrcv_html=''; $embroiss_html=''; $embrorcv_html=''; $spiss_html=''; $sprcv_html=''; $washiss_html=''; $washrcv_html='';
					$k=0;
					foreach($size_library as $sizeRes)
					{ 
						$k++;
						
						/*if($db_type==0)
						{
							$prod_sql= sql_select("SELECT  
									IFNULL(sum(CASE WHEN c.production_type ='1' THEN c.production_qnty  ELSE 0 END),0) AS cutting_qnty,
									IFNULL(sum(CASE WHEN c.production_type ='2' AND a.embel_name=1 THEN c.production_qnty ELSE 0 END),0) AS printing_qnty,
									IFNULL(sum(CASE WHEN c.production_type ='3' AND a.embel_name=1 THEN c.production_qnty ELSE 0 END),0) AS printreceived_qnty, 
									IFNULL(sum(CASE WHEN c.production_type ='2' AND a.embel_name=2 THEN c.production_qnty ELSE 0 END),0) AS emb_qnty,
									IFNULL(sum(CASE WHEN c.production_type ='3' AND a.embel_name=2 THEN c.production_qnty ELSE 0 END),0) AS embreceived_qnty, 
									IFNULL(sum(CASE WHEN c.production_type ='2' AND a.embel_name=3 THEN c.production_qnty ELSE 0 END),0) AS wash_qnty,
									IFNULL(sum(CASE WHEN c.production_type ='3' AND a.embel_name=3 THEN c.production_qnty ELSE 0 END),0) AS washreceived_qnty, 
									IFNULL(sum(CASE WHEN c.production_type ='2' AND a.embel_name=4 THEN c.production_qnty ELSE 0 END),0) AS sp_qnty,
									IFNULL(sum(CASE WHEN c.production_type ='3' AND a.embel_name=4 THEN c.production_qnty ELSE 0 END),0) AS spreceived_qnty, 
									IFNULL(sum(CASE WHEN c.production_type ='4' THEN c.production_qnty ELSE 0 END),0) AS sewingin_qnty,
									IFNULL(sum(CASE WHEN c.production_type ='5' THEN c.production_qnty ELSE 0 END),0) AS sewingout_qnty,
									IFNULL(sum(CASE WHEN c.production_type ='6' THEN c.production_qnty ELSE 0 END),0) AS finishin_qnty, 
									IFNULL(sum(CASE WHEN c.production_type ='7' THEN c.production_qnty ELSE 0 END),0) AS iron_qnty,
									IFNULL(sum(CASE WHEN c.production_type ='8' THEN c.production_qnty ELSE 0 END),0) AS finish_qnty 
								from 
									pro_garments_production_mst a, pro_garments_production_dtls c,wo_po_color_size_breakdown d
								where  
									a.id=c.mst_id and d.po_break_down_id in (".$po_break_down_id.") and d.item_number_id='$item_id' and d.color_number_id=".$colorRes[csf("color_number_id")]." and d.size_number_id=".$sizeRes[csf("size_number_id")]." and c.color_size_break_down_id=d.id and c.status_active=1 $location $floor ");
						}
						else
						{
							$prod_sql=sql_select("SELECT  
									NVL(sum(CASE WHEN c.production_type ='1' THEN c.production_qnty  ELSE 0 END),0) AS cutting_qnty,
									NVL(sum(CASE WHEN c.production_type ='2' AND a.embel_name=1 THEN c.production_qnty ELSE 0 END),0) AS printing_qnty,
									NVL(sum(CASE WHEN c.production_type ='3' AND a.embel_name=1 THEN c.production_qnty ELSE 0 END),0) AS printreceived_qnty,
									NVL(sum(CASE WHEN c.production_type ='2' AND a.embel_name=2 THEN c.production_qnty ELSE 0 END),0) AS emb_qnty,
									NVL(sum(CASE WHEN c.production_type ='3' AND a.embel_name=2 THEN c.production_qnty ELSE 0 END),0) AS embreceived_qnty, 
									NVL(sum(CASE WHEN c.production_type ='2' AND a.embel_name=3 THEN c.production_qnty ELSE 0 END),0) AS wash_qnty,
									NVL(sum(CASE WHEN c.production_type ='3' AND a.embel_name=3 THEN c.production_qnty ELSE 0 END),0) AS washreceived_qnty, 
									NVL(sum(CASE WHEN c.production_type ='2' AND a.embel_name=4 THEN c.production_qnty ELSE 0 END),0) AS sp_qnty,
									NVL(sum(CASE WHEN c.production_type ='3' AND a.embel_name=4 THEN c.production_qnty ELSE 0 END),0) AS spreceived_qnty, 
									NVL(sum(CASE WHEN c.production_type ='4' THEN c.production_qnty ELSE 0 END),0) AS sewingin_qnty,
									NVL(sum(CASE WHEN c.production_type ='5' THEN c.production_qnty ELSE 0 END),0) AS sewingout_qnty,
									NVL(sum(CASE WHEN c.production_type ='6' THEN c.production_qnty ELSE 0 END),0) AS finishin_qnty, 
									NVL(sum(CASE WHEN c.production_type ='7' THEN c.production_qnty ELSE 0 END),0) AS iron_qnty,
									NVL(sum(CASE WHEN c.production_type ='8' THEN c.production_qnty ELSE 0 END),0) AS finish_qnty 
								from 
									pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d
								where  
									a.id=c.mst_id and d.po_break_down_id in (".$po_break_down_id.") and d.item_number_id='$item_id' and d.color_number_id=".$colorRes[csf("color_number_id")]." and d.size_number_id=".$sizeRes[csf("size_number_id")]." and c.color_size_break_down_id=d.id and c.status_active=1 $location $floor ");	
						}*/
						//echo $prod_sql;$prod_sql; $prod_color_size_data[$row[csf("color_number_id")]][$row[csf("size_number_id")]]["cutting_qnty"]
						//foreach($prod_sql as $prodRow);  
						
						$col = 'col'.$k;
	                    if($prod_color_size_data[$colorRes][$sizeRes]["cutting_qnty"]==0)$bgCol="bgcolor='#FF0000'"; 
						else if($prod_color_size_data[$colorRes][$sizeRes]["cutting_qnty"] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($prod_color_size_data[$colorRes][$sizeRes]["cutting_qnty"] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'"; 
						$cutting_html .='<td '.$bgCol.'>'.$prod_color_size_data[$colorRes][$sizeRes]["cutting_qnty"].'</td>';
	                    $total_cutting+=$prod_color_size_data[$colorRes][$sizeRes]["cutting_qnty"];
	                 	
						if($cons_embr>0)
						{
							if($prod_color_size_data[$colorRes][$sizeRes]["printing_qnty"]==0)$bgCol="bgcolor='#FF0000'"; 
							else if($prod_color_size_data[$colorRes][$sizeRes]["printing_qnty"] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
							else if($prod_color_size_data[$colorRes][$sizeRes]["printing_qnty"] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						}
						else $bgCol='';
	                    $printiss_html .='<td '.$bgCol.'>'.$prod_color_size_data[$colorRes][$sizeRes]["printing_qnty"].'</td>';
	                    $total_print_issue+=$prod_color_size_data[$colorRes][$sizeRes]["printing_qnty"];
	                    
						if($cons_embr>0)
						{
							if($prod_color_size_data[$colorRes][$sizeRes]["printreceived_qnty"]==0)$bgCol="bgcolor='#FF0000'"; 
							else if($prod_color_size_data[$colorRes][$sizeRes]["printreceived_qnty"] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
							else if($prod_color_size_data[$colorRes][$sizeRes]["printreceived_qnty"] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						}
						else $bgCol='';
						
	                    $printrcv_html .='<td '.$bgCol.'>'.$prod_color_size_data[$colorRes][$sizeRes]["printreceived_qnty"].'</td>';
	                    $total_print_rcv+=$prod_color_size_data[$colorRes][$sizeRes]["printreceived_qnty"];
						
						if($cons_embr>0)
						{
							if($prod_color_size_data[$colorRes][$sizeRes]["emb_qnty"]==0)$bgCol="bgcolor='#FF0000'"; 
							else if($prod_color_size_data[$colorRes][$sizeRes]["emb_qnty"] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
							else if($prod_color_size_data[$colorRes][$sizeRes]["emb_qnty"] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						}
						else $bgCol='';
	                    $embroiss_html .='<td '.$bgCol.'>'.$prod_color_size_data[$colorRes][$sizeRes]["emb_qnty"].'</td>';
	                    $total_embro_issue+=$prod_color_size_data[$colorRes][$sizeRes]["emb_qnty"];
	                    
						if($cons_embr>0)
						{
							if($prod_color_size_data[$colorRes][$sizeRes]["embreceived_qnty"]==0)$bgCol="bgcolor='#FF0000'"; 
							else if($prod_color_size_data[$colorRes][$sizeRes]["embreceived_qnty"] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
							else if($prod_color_size_data[$colorRes][$sizeRes]["embreceived_qnty"] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						}
						else $bgCol='';
						
	                    $embrorcv_html .='<td '.$bgCol.'>'.$prod_color_size_data[$colorRes][$sizeRes]["embreceived_qnty"].'</td>';
	                    $total_embro_rcv+=$prod_color_size_data[$colorRes][$sizeRes]["embreceived_qnty"];
						
						if($cons_embr>0)
						{
							if($prod_color_size_data[$colorRes][$sizeRes]["sp_qnty"]==0)$bgCol="bgcolor='#FF0000'"; 
							else if($prod_color_size_data[$colorRes][$sizeRes]["sp_qnty"] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
							else if($prod_color_size_data[$colorRes][$sizeRes]["sp_qnty"] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						}
						else $bgCol='';
	                    $spiss_html .='<td '.$bgCol.'>'.$prod_color_size_data[$colorRes][$sizeRes]["sp_qnty"].'</td>';
	                    $total_sp_issue+=$prod_color_size_data[$colorRes][$sizeRes]["sp_qnty"];
	                    
						if($cons_embr>0)
						{
							if($prod_color_size_data[$colorRes][$sizeRes]["spreceived_qnty"]==0)$bgCol="bgcolor='#FF0000'"; 
							else if($prod_color_size_data[$colorRes][$sizeRes]["spreceived_qnty"] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
							else if($prod_color_size_data[$colorRes][$sizeRes]["spreceived_qnty"] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						}
						else $bgCol='';
						
	                    $sprcv_html .='<td '.$bgCol.'>'.$prod_color_size_data[$colorRes][$sizeRes]["spreceived_qnty"].'</td>';
	                    $total_sp_rcv+=$prod_color_size_data[$colorRes][$sizeRes]["spreceived_qnty"];
						
						if($cons_embr>0)
						{
							if($prod_color_size_data[$colorRes][$sizeRes]["wash_qnty"]==0)$bgCol="bgcolor='#FF0000'"; 
							else if($prod_color_size_data[$colorRes][$sizeRes]["wash_qnty"] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
							else if($prod_color_size_data[$colorRes][$sizeRes]["wash_qnty"] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						}
						else $bgCol='';
	                    $washiss_html .='<td '.$bgCol.'>'.$prod_color_size_data[$colorRes][$sizeRes]["wash_qnty"].'</td>';
	                    $total_wash_issue+=$prod_color_size_data[$colorRes][$sizeRes]["wash_qnty"];
	                    
						if($cons_embr>0)
						{
							if($prod_color_size_data[$colorRes][$sizeRes]["washreceived_qnty"]==0)$bgCol="bgcolor='#FF0000'"; 
							else if($prod_color_size_data[$colorRes][$sizeRes]["washreceived_qnty"] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
							else if($prod_color_size_data[$colorRes][$sizeRes]["washreceived_qnty"]>= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						}
						else $bgCol='';
						
	                    $washrcv_html .='<td '.$bgCol.'>'.$prod_color_size_data[$colorRes][$sizeRes]["washreceived_qnty"].'</td>';
	                    $total_wash_rcv+=$prod_color_size_data[$colorRes][$sizeRes]["washreceived_qnty"];
	                    
						if($prod_color_size_data[$colorRes][$sizeRes]["sewingin_qnty"]==0)$bgCol="bgcolor='#FF0000'"; 
						else if($prod_color_size_data[$colorRes][$sizeRes]["sewingin_qnty"] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($prod_color_size_data[$colorRes][$sizeRes]["sewingin_qnty"] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'"; 
	                    $sewin_html .='<td '.$bgCol.'>'.$prod_color_size_data[$colorRes][$sizeRes]["sewingin_qnty"].'</td>';
	                    $total_sew_in+=$prod_color_size_data[$colorRes][$sizeRes]["sewingin_qnty"];
	                    
						if($prod_color_size_data[$colorRes][$sizeRes]["sewingout_qnty"]==0)$bgCol="bgcolor='#FF0000'"; 
						else if($prod_color_size_data[$colorRes][$sizeRes]["sewingout_qnty"] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($prod_color_size_data[$colorRes][$sizeRes]["sewingout_qnty"] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";  
	                    $sewout_html .='<td '.$bgCol.'>'.$prod_color_size_data[$colorRes][$sizeRes]["sewingout_qnty"].'</td>';
	                    $total_sew_out+=$prod_color_size_data[$colorRes][$sizeRes]["sewingout_qnty"];
	                    
						/*if($prodRow[csf("finishin_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
						else if($prodRow[csf("finishin_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($prodRow[csf("finishin_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'"; 
	                    $finisin_html .='<td '.$bgCol.'>'.$prodRow[csf("finishin_qnty")].'</td>';
	                    $total_fin_in+=$prodRow[csf("finishin_qnty")];*/
	                    
						if($prod_color_size_data[$colorRes][$sizeRes]["finish_qnty"]==0)$bgCol="bgcolor='#FF0000'"; 
						else if($prod_color_size_data[$colorRes][$sizeRes]["finish_qnty"] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($prod_color_size_data[$colorRes][$sizeRes]["finish_qnty"] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'"; 
	                    $finisout_html .='<td '.$bgCol.'>'.$prod_color_size_data[$colorRes][$sizeRes]["finish_qnty"].'</td>';
	                    $total_fin_out+=$prod_color_size_data[$colorRes][$sizeRes]["finish_qnty"];
						
						if($prod_color_size_data[$colorRes][$sizeRes]["iron_qnty"]==0)$bgCol="bgcolor='#FF0000'"; 
						else if($prod_color_size_data[$colorRes][$sizeRes]["iron_qnty"] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($prod_color_size_data[$colorRes][$sizeRes]["iron_qnty"] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'"; 
	                    $iron_html .='<td '.$bgCol.'>'.$prod_color_size_data[$colorRes][$sizeRes]["iron_qnty"].'</td>';
	                    $total_iron_out+=$prod_color_size_data[$colorRes][$sizeRes]["iron_qnty"];
						
						//if($prodRow[csf("iron_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
						//else if($prodRow[csf("iron_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						//else if($prodRow[csf("iron_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						if($color_variable_setting==2 || $color_variable_setting==3)
						{ 
							$bgCol=="bgcolor='#FFFFFF'";
							$exfact_html.='<td>'.$ex_fact_qty_arr[$colorRes][$sizeRes].'&nbsp;</td>';
							
							$total_exfact_qnty+=$ex_fact_qty_arr[$colorRes][$sizeRes];
						}
						
						if($prod_color_size_data[$colorRes][$sizeRes]["poly_qty"]==0)$bgCol="bgcolor='#FF0000'"; 
						else if($prod_color_size_data[$colorRes][$sizeRes]["poly_qty"] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($prod_color_size_data[$colorRes][$sizeRes]["poly_qty"] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";  
	                    $poly_html.='<td '.$bgCol.'>'.$prod_color_size_data[$colorRes][$sizeRes]["poly_qty"].'</td>';
	                    $total_poly+=$prod_color_size_data[$colorRes][$sizeRes]["poly_qty"];
						
	 				 
					}// end size foreach loop	
					
					?>
						<tr bgcolor="<? echo $bgcolor1; ?>">
	                    	<td><b>Cutting</b></td>
	                        <? echo $cutting_html; ?> 
	                        <td><? echo $total_cutting; ?></td> 
	                    </tr>
	                    <tr bgcolor="<? echo $bgcolor2; ?>">
	                    	<td><b>Print Issue</b></td>
	                        <? echo $printiss_html; ?> 
	                        <td><? echo $total_print_issue; ?></td> 
	                    </tr>
	                    <tr bgcolor="<? echo $bgcolor1; ?>">
	                    	<td><b>Print Received</b></td>
	                        <? echo $printrcv_html; ?> 
	                        <td><? echo $total_print_rcv; ?></td> 
	                    </tr>
	                    <tr bgcolor="<? echo $bgcolor2; ?>">
	                    	<td><b>Embro Issue</b></td>
	                        <? echo $embroiss_html; ?> 
	                        <td><? echo $total_embro_issue; ?></td> 
	                    </tr>
	                    <tr bgcolor="<? echo $bgcolor1; ?>">
	                    	<td><b>Embro Received</b></td>
	                        <? echo $embrorcv_html; ?> 
	                        <td><? echo $total_embro_rcv; ?></td> 
	                    </tr>
	                    <tr bgcolor="<? echo $bgcolor2; ?>">
	                    	<td><b>Issue For Special Works</b></td>
	                        <? echo $spiss_html; ?> 
	                        <td><? echo $total_sp_issue; ?></td> 
	                    </tr>
	                    <tr bgcolor="<? echo $bgcolor1; ?>">
	                    	<td><b>Recv. From Special Works</b></td>
	                        <? echo $sprcv_html; ?> 
	                        <td><? echo $total_sp_rcv; ?></td> 
	                    </tr>
	                    <tr bgcolor="<? echo $bgcolor2; ?>">
	                    	<td><b>Sewing Input</b></td>
	                       <? echo $sewin_html; ?> 
	                        <td><? echo $total_sew_in; ?></td> 
	                    </tr>
	                    <tr bgcolor="<? echo $bgcolor1; ?>">
	                    	<td><b>Sewing Output</b></td>
	                        <? echo $sewout_html; ?> 
	                        <td><? echo $total_sew_out; ?></td> 
	                    </tr>
	                    <tr bgcolor="<? echo $bgcolor2; ?>">
	                    	<td><b>Issue For Wash</b></td>
	                        <? echo $washiss_html; ?> 
	                        <td><? echo $total_wash_issue; ?></td> 
	                    </tr>
	                    <tr bgcolor="<? echo $bgcolor1; ?>">
	                    	<td><b>Recv. From Wash</b></td>
	                        <? echo $washrcv_html; ?> 
	                        <td><? echo $total_wash_rcv; ?></td> 
	                    </tr>
	                    <tr bgcolor="<? echo $bgcolor2; ?>">
	                    	<td><b>Iron Output</b></td>
	                        <? echo $iron_html; ?> 
	                        <td><? echo $total_iron_out; ?></td> 
	                    </tr>
	                    <tr bgcolor="<? echo $bgcolor1; ?>">
	                    	<td><b>Poly Qty.</b></td>
	                       <? echo $poly_html; ?> 
	                        <td><? echo $total_poly; ?></td> 
	                    </tr>
	                   <tr bgcolor="<? echo $bgcolor1; ?>">
	                    	<td><b>Finishing Output</b></td>
	                       <? echo $finisout_html; ?> 
	                        <td><? echo $total_fin_out; ?></td> 
	                    </tr>
	                    <? 
						if($color_variable_setting==2 || $color_variable_setting==3)
						{
							?>
							<tr>
								<td colspan="2" align="center"><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ex-Factory Qty.</b></td>
								 <? echo $exfact_html; ?> 
								<td><? echo $total_exfact_qnty; ?>&nbsp;</td> 
							</tr>
							<?
						}
						?>
				<?	
				}// end color foreach loop
				?>
	           
			 
	 </table>
	</div>    


	<?
	exit();

}// end if condition

if ($action=='OrderPopup_country')
{
	echo load_html_head_contents("Order Wise Production Report", "../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	
	$po_break_down_id=str_replace("'","",$po_break_down_id);
	$company_name=str_replace("'","",$company_name);
	$item_id=str_replace("'","",$item_id);
	$country_id=str_replace("'","",$country_id);
	$color_id=str_replace("'","",$color_id);
	
	//echo $po_break_down_id."****".$company_name."****".$item_id."****".$country_id."****".$color_id;die;
	if($country_id>0) $contry_cond=" and a.country_id='$country_id'"; else $contry_cond="";
	$sql_exfect="SELECT c.color_number_id, c.size_number_id, sum(b.production_qnty) as production_qnty from pro_ex_factory_mst a,pro_ex_factory_dtls b, wo_po_color_size_breakdown c where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id =$po_break_down_id and c.color_number_id='$color_id' $contry_cond and a.status_active=1 and a.is_deleted=0 group by  c.color_number_id, c.size_number_id";
	$sql_result_exfact=sql_select($sql_exfect);
	foreach($sql_result_exfact as $row)
	{
		$ex_fact_qty_arr[$row[csf("color_number_id")]][$row[csf("size_number_id")]]=$row[csf("production_qnty")];
	}
	
	$color_library=array(); $size_library=array(); $color_library_plan=array(); $dataQty=array();
	$colorSizeData=sql_select("select id, color_number_id, size_number_id, order_quantity, plan_cut_qnty, excess_cut_perc from wo_po_color_size_breakdown where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and color_number_id='$color_id' and status_active=1 and is_deleted=0");
 	  foreach($colorSizeData as $csRow)
	  {
		  if($csRow[csf('color_number_id')]>0)
		  {
			  $color_library[$csRow[csf('color_number_id')]]+=$csRow[csf('order_quantity')];
			  $color_library_plan[$csRow[csf('color_number_id')]]+=$csRow[csf('plan_cut_qnty')];
		  }
		  
		  if($csRow[csf('size_number_id')]>0)
		  {
			  $size_library[$csRow[csf('size_number_id')]]=$csRow[csf('size_number_id')];
		  }
		  
		  $dataQty[$csRow[csf('color_number_id')]][$csRow[csf('size_number_id')]][1]+=$csRow[csf('order_quantity')];
		  $dataQty[$csRow[csf('color_number_id')]][$csRow[csf('size_number_id')]][2]+=$csRow[csf('plan_cut_qnty')];
		  $dataQty[$csRow[csf('color_number_id')]][$csRow[csf('size_number_id')]][3]+=$csRow[csf('excess_cut_perc')];
		  $dataQty[$csRow[csf('color_number_id')]][$csRow[csf('size_number_id')]][4]+=1;
		  $color_size_data[$csRow[csf("id")]]["color_number_id"]=$csRow[csf('color_number_id')];
		  $color_size_data[$csRow[csf("id")]]["size_number_id"]=$csRow[csf('size_number_id')];
	  }
	
	
	
	
	if($db_type==0)
	{
		$prod_sql= sql_select("SELECT a.po_break_down_id, c.color_size_break_down_id,  
				IFNULL(sum(CASE WHEN a.production_type ='1' THEN c.production_qnty  ELSE 0 END),0) AS cutting_qnty,
				IFNULL(sum(CASE WHEN a.production_type ='2' AND a.embel_name=1 THEN c.production_qnty ELSE 0 END),0) AS printing_qnty,
				IFNULL(sum(CASE WHEN a.production_type ='3' AND a.embel_name=1 THEN c.production_qnty ELSE 0 END),0) AS printreceived_qnty, 
				IFNULL(sum(CASE WHEN a.production_type ='2' AND a.embel_name=2 THEN c.production_qnty ELSE 0 END),0) AS emb_qnty,
				IFNULL(sum(CASE WHEN a.production_type ='3' AND a.embel_name=2 THEN c.production_qnty ELSE 0 END),0) AS embreceived_qnty, 
				IFNULL(sum(CASE WHEN a.production_type ='2' AND a.embel_name=3 THEN c.production_qnty ELSE 0 END),0) AS wash_qnty,
				IFNULL(sum(CASE WHEN a.production_type ='3' AND a.embel_name=3 THEN c.production_qnty ELSE 0 END),0) AS washreceived_qnty, 
				IFNULL(sum(CASE WHEN a.production_type ='2' AND a.embel_name=4 THEN c.production_qnty ELSE 0 END),0) AS sp_qnty,
				IFNULL(sum(CASE WHEN a.production_type ='3' AND a.embel_name=4 THEN c.production_qnty ELSE 0 END),0) AS spreceived_qnty, 
				IFNULL(sum(CASE WHEN a.production_type ='4' THEN c.production_qnty ELSE 0 END),0) AS sewingin_qnty,
				IFNULL(sum(CASE WHEN a.production_type ='5' THEN c.production_qnty ELSE 0 END),0) AS sewingout_qnty,
				IFNULL(sum(CASE WHEN a.production_type ='6' THEN c.production_qnty ELSE 0 END),0) AS finishin_qnty, 
				IFNULL(sum(CASE WHEN a.production_type ='7' THEN c.production_qnty ELSE 0 END),0) AS iron_qnty,
				IFNULL(sum(CASE WHEN a.production_type ='8' THEN c.production_qnty ELSE 0 END),0) AS finish_qnty 
			from 
				pro_garments_production_mst a, pro_garments_production_dtls c
			where  
				a.id=c.mst_id and a.status_active=1 and c.status_active=1 and a.po_break_down_id =".$po_break_down_id." and a.item_number_id='$item_id' $contry_cond
			group by a.po_break_down_id,c.color_size_break_down_id");
				/*IFNULL(sum(CASE WHEN c.production_type ='2' THEN c.production_qnty ELSE 0 END),0) AS printing_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='3' THEN c.production_qnty ELSE 0 END),0) AS printreceived_qnty, */
	}
	else
	{
		 $pr_sql="SELECT a.po_break_down_id, c.color_size_break_down_id, 
				NVL(sum(CASE WHEN a.production_type ='1' THEN c.production_qnty  ELSE 0 END),0) AS cutting_qnty,
				NVL(sum(CASE WHEN a.production_type ='2' AND a.embel_name=1 THEN c.production_qnty ELSE 0 END),0) AS printing_qnty,
				NVL(sum(CASE WHEN a.production_type ='3' AND a.embel_name=1 THEN c.production_qnty ELSE 0 END),0) AS printreceived_qnty,
				NVL(sum(CASE WHEN a.production_type ='2' AND a.embel_name=2 THEN c.production_qnty ELSE 0 END),0) AS emb_qnty,
				NVL(sum(CASE WHEN a.production_type ='3' AND a.embel_name=2 THEN c.production_qnty ELSE 0 END),0) AS embreceived_qnty, 
				NVL(sum(CASE WHEN a.production_type ='2' AND a.embel_name=3 THEN c.production_qnty ELSE 0 END),0) AS wash_qnty,
				NVL(sum(CASE WHEN a.production_type ='3' AND a.embel_name=3 THEN c.production_qnty ELSE 0 END),0) AS washreceived_qnty, 
				NVL(sum(CASE WHEN a.production_type ='2' AND a.embel_name=4 THEN c.production_qnty ELSE 0 END),0) AS sp_qnty,
				NVL(sum(CASE WHEN a.production_type ='3' AND a.embel_name=4 THEN c.production_qnty ELSE 0 END),0) AS spreceived_qnty, 
				NVL(sum(CASE WHEN a.production_type ='4' THEN c.production_qnty ELSE 0 END),0) AS sewingin_qnty,
				NVL(sum(CASE WHEN a.production_type ='5' THEN c.production_qnty ELSE 0 END),0) AS sewingout_qnty,
				NVL(sum(CASE WHEN a.production_type ='6' THEN c.production_qnty ELSE 0 END),0) AS finishin_qnty, 
				NVL(sum(CASE WHEN a.production_type ='7' THEN c.production_qnty ELSE 0 END),0) AS iron_qnty,
				NVL(sum(CASE WHEN a.production_type ='8' THEN c.production_qnty ELSE 0 END),0) AS finish_qnty
			from 
				pro_garments_production_mst a, pro_garments_production_dtls c
			where  
				a.id=c.mst_id and a.status_active=1 and c.status_active=1 and a.po_break_down_id =".$po_break_down_id." and a.item_number_id='$item_id' $contry_cond
			group by a.po_break_down_id,c.color_size_break_down_id";
			$prod_sql=sql_select($pr_sql);
				
				
	}
	//echo $prod_sql;
	$prod_color_size_data=array();
	foreach($prod_sql as $row)
	{
		$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["cutting_qnty"]+=$row[csf("cutting_qnty")];
		$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["printing_qnty"]+=$row[csf("printing_qnty")];
		$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["printreceived_qnty"]+=$row[csf("printreceived_qnty")];
		$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["emb_qnty"]+=$row[csf("emb_qnty")];
		$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["embreceived_qnty"]+=$row[csf("embreceived_qnty")];
		$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["wash_qnty"]+=$row[csf("wash_qnty")];
		$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["washreceived_qnty"]+=$row[csf("washreceived_qnty")];
		$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["sp_qnty"]+=$row[csf("sp_qnty")];
		$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["spreceived_qnty"]+=$row[csf("spreceived_qnty")];
		$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["sewingin_qnty"]+=$row[csf("sewingin_qnty")];
		$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["sewingout_qnty"]+=$row[csf("sewingout_qnty")];
		$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["finishin_qnty"]+=$row[csf("finishin_qnty")];
		$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["iron_qnty"]+=$row[csf("iron_qnty")];
		$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["finish_qnty"]+=$row[csf("finish_qnty")];
		//$color_library[$row[csf("color_number_id")]]=$row[csf("color_number_id")];
		//$size_library[$row[csf("size_number_id")]]=$row[csf("size_number_id")];
	}
	//var_dump($color_library);die;
	
	?>
	 <div id="data_panel" align="center" style="width:100%">
	         <script>
			 	function new_window()
				 {
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				 }
	         </script>
	 	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
	 </div>
	  
	<div style="width:700px" align="center" id="details_reports"> 
	  	<legend>Color And Size Wise Summary</legend>
	    <table id="tbl_id" class="rpt_table" width="700" border="1" rules="all" >
	    	<thead>
	        	<tr>
	            	<th width="70">Buyer</th>
	                <th width="100">Job Number</th>
	                <th width="120">Style Name</th>
	                <th width="100">Order Number</th>
	                <th width="70">Ship Date</th>
	                <th width="150">Item Name</th>
	                <th >Order Qty.</th>
	            </tr>
	        </thead>
	       	<?
				if($country_id>0) $contry_cond=" and c.country_id='$country_id'"; else $contry_cond="";
				$sql = "select a.job_no_mst, a.po_number as po_number,a.pub_shipment_date as pub_shipment_date,a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,b.total_set_qnty as set_item_ratio, sum(c.order_quantity) as po_quantity
						from wo_po_break_down a, wo_po_details_master b, wo_po_color_size_breakdown c
						where a.job_no_mst=b.job_no and a.id=c.po_break_down_id and a.id =$po_break_down_id and c.item_number_id=$item_id and c.color_number_id='$color_id' $contry_cond and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active=1 and c.status_active in(1,2,3) and b.is_deleted=0 
						group by a.job_no_mst, a.po_number,a.pub_shipment_date,a.pub_shipment_date,a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,b.total_set_qnty";
				//echo $sql;die;
				$resultRow=sql_select($sql);
					
				$cons_embr=return_field_value("sum(cons_dzn_gmts) as cons_dzn_gmts","wo_pre_cost_embe_cost_dtls","job_no='".$resultRow[0][csf("job_no_mst")]."' and status_active=1 and is_deleted=0","cons_dzn_gmts");
				
	 		?> 
	        <tbody>
	        	<tr>
	                <td><p><? echo $buyer_short_library[$resultRow[0][csf("buyer_name")]]; ?></p></td>
	                <td><p><? echo $resultRow[0][csf("job_no_mst")]; ?></p></td>
	                <td><p><? echo $resultRow[0][csf("style_ref_no")]; ?></p></td>
	                <td><p><? echo implode(",",array_unique(explode(",",$resultRow[0][csf("po_number")]))); ?></p></td>
	                <td align="center"><? echo change_date_format($resultRow[0][csf("pub_shipment_date")]); ?></td>
	                <td><? echo $garments_item[$item_id]; ?></td>
	                <td align="right"><? echo $resultRow[0][csf("po_quantity")]*$resultRow[0][csf("set_item_ratio")]; ?></td>
	            </tr>
	            <?
				$prod_sewing_sql=sql_select("SELECT sum(alter_qnty) as alter_qnty, sum(reject_qnty) as reject_qnty from pro_garments_production_mst where production_type=5 and po_break_down_id=$po_break_down_id and item_number_id=$item_id and is_deleted=0 and status_active=1");
				?>
	            <tr>
	                <td colspan="2">Total Alter Sewing Qty : <b><? echo $prod_sewing_sql[0][csf("alter_qnty")]; ?></b></td>
	                <td colspan="2">Total Reject Sewing Qty : <b><? echo  $prod_sewing_sql[0][csf("reject_qnty")]; ?></b></td>
	                <td colspan="2">Pack Assortment: <b><? echo $packing[$resultRow[csf("packing")]]; ?></b></td>
	            </tr>
	        </tbody>
	    </table>
	    <?
					  
		  $size_Arr_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
		  //$color_library_sql;
		  if($country_id>0) $contry_cond=" and country_id='$country_id'"; else $contry_cond="";
		  /*$color_size_sql=sql_select("select color_number_id as color_number_id,size_number_id as size_number_id from wo_po_color_size_breakdown where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and color_number_id='$color_id' $contry_cond  and status_active=1 ");
		  foreach($color_size_sql as $row)
		  {
			  $color_library[$row[csf("color_number_id")]]=$row[csf("color_number_id")];
			  $size_library[$row[csf("size_number_id")]]=$row[csf("size_number_id")];
		  }*/
		  //$size_library=sql_select("select distinct(size_number_id) as size_number_id from wo_po_color_size_breakdown where po_break_down_id in ($po_break_down_id) and size_mst_id!=0 and status_active=1");
		  $count = count($size_library);	
		  $width= $count*70+350; 		
		  	  
		?>
	    <table id="tblDtls_id" class="rpt_table" width="<? echo $width; ?>" border="1" rules="all" >
		 	<thead>
	        	<tr>
	            	<th width="100">Color Name</th>
	                <th width="170">Production Type</th>
	 				<?
					foreach($size_library as $val)
					{
					 	?><th width="80"><? echo $size_Arr_library[$val]; ?></th><?
					}
					?>
	     		    <th width="60">Total</th>
	           </tr>
	        </thead>
	        <?
			  foreach($color_library as $colorId=>$totalorderqnty)
			  {
				  if($color_variable_setting==2 || $color_variable_setting==3) $row_span=17; else $row_span=16;  
				?>	  
				<tr>

					<td rowspan="<? echo $row_span; ?>"><? echo $color_Arr_library[$color_id]; ?></td>
				
	 			<?
	            	 /* $i=0;$j=0;$sqlPart="";
					  foreach($size_library as $size_id)
					  {
						  $i++;$j++;
						  if($i>1) $sqlPart .=",";
						  $sqlPart .= 'SUM( CASE WHEN color_number_id='.$color_id.' and size_number_id='.$size_id.' THEN order_quantity ELSE 0 END ) as '."col".$i;
						  $sqlPart .= ',SUM( CASE WHEN color_number_id='.$color_id.' and size_number_id='.$size_id.' THEN plan_cut_qnty ELSE 0 END ) as '."pcut".$i;
						  $sqlPart .= ',SUM( CASE WHEN color_number_id='.$color_id.' and size_number_id='.$size_id.' THEN excess_cut_perc ELSE 0 END ) as '."excess_cut".$i;
					  }
					  if($j>1)
					  {
						 $sqlPart .=',SUM( CASE WHEN color_number_id='.$color_id.' THEN order_quantity ELSE 0 END ) as totalorderqnty';
						 $sqlPart .=',SUM( CASE WHEN color_number_id='.$color_id.' THEN plan_cut_qnty ELSE 0 END ) as totalplancutqnty';
					  }
					$sql = sql_select("select avg(excess_cut_perc) as avg_excess_cut_perc,max(excess_cut_perc) as excess_cut_perc,". $sqlPart ." from wo_po_color_size_breakdown where status_active=1 and po_break_down_id =$po_break_down_id and item_number_id=$item_id");
					//echo $sql;die;
					foreach($sql as $resRow); */
					
	 					$bgcolor1="#E9F3FF"; 
						$bgcolor2="#FFFFFF";
					?>
						</tr>
	                    
	                    <tr bgcolor="<? echo $bgcolor1; ?>">
	                        <td><b>Order Quantity</b></td>	
	                        <? 
	                        foreach($size_library as $sizeId=>$sizeRes)
	                        {
	                        ?>	
	                            <td><? echo $dataQty[$colorId][$sizeId][1]; ?></td>
	                        <? 
	                        } 
	                        ?>
	                        <td><? echo $totalorderqnty; ?></td> 
	                    </tr>
						
	                    <tr bgcolor="<? echo $bgcolor2; ?>">
	                        <td><b>Plan To Cut (AVG <? echo number_format($dataQty[$colorId][$sizeId][3]/$dataQty[$colorId][$sizeId][4],2); ?>)% </b></td>	
	                        <? 
	                        foreach($size_library as $sizeId=>$sizeRes)
	                        {
	                        ?>	
	                            <td title="Excess Cut <? echo $dataQty[$colorId][$sizeId][3]; ?>%"><? echo $dataQty[$colorId][$sizeId][2]; ?></td>
	                        <? 
	                        } 
	                        ?>
	                        <td><? echo $color_library_plan[$colorId]; ?></td> 
	                    </tr>
						<?
	 				$total_cutting=0;$total_sew_in=0;$total_sew_out=0;$total_fin_in=0;$total_fin_out=0;$total_iron_out=0; $total_exfact_qnty=0;
					$total_print_issue=0;$total_print_rcv=0;$total_embro_issue=0;$total_embro_rcv=0; $total_sp_issue=0;$total_sp_rcv=0; $total_wash_issue=0;$total_wash_rcv=0;
					$cutting_html='';$sewin_html='';$sewout_html='';$finisin_html='';$finisout_html='';$iron_html=''; $exfact_html='';
					$printiss_html=''; $printrcv_html=''; $embroiss_html=''; $embrorcv_html=''; $spiss_html=''; $sprcv_html=''; $washiss_html=''; $washrcv_html='';
					foreach($size_library as $sizeId=>$sizeRes)
					{ 
						$cutting_qnty=$prod_color_size_data[$colorId][$sizeId]['cutting_qnty'];
						$printing_qnty=$prod_color_size_data[$colorId][$sizeId]['printing_qnty'];
						$printreceived_qnty=$prod_color_size_data[$colorId][$sizeId]['printreceived_qnty'];
						$emb_qnty=$prod_color_size_data[$colorId][$sizeId]['emb_qnty'];
						$embreceived_qnty=$prod_color_size_data[$colorId][$sizeId]['embreceived_qnty'];
						$wash_qnty=$prod_color_size_data[$colorId][$sizeId]['wash_qnty'];
						$washreceived_qnty=$prod_color_size_data[$colorId][$sizeId]['washreceived_qnty'];
						$sp_qnty=$prod_color_size_data[$colorId][$sizeId]['sp_qnty'];
						$spreceived_qnty=$prod_color_size_data[$colorId][$sizeId]['spreceived_qnty'];
						$sewingin_qnty=$prod_color_size_data[$colorId][$sizeId]['sewingin_qnty'];
						$sewingout_qnty=$prod_color_size_data[$colorId][$sizeId]['sewingout_qnty'];
						$finishin_qnty=$prod_color_size_data[$colorId][$sizeId]['finishin_qnty'];
						$iron_qnty=$prod_color_size_data[$colorId][$sizeId]['iron_qnty'];
						$finish_qnty=$prod_color_size_data[$colorId][$sizeId]['finish_qnty'];
						
						$resRow[csf($col)]=$dataQty[$colorId][$sizeId][2];
	                    if($cutting_qnty==0)$bgCol="bgcolor='#FF0000'"; 
						else if($cutting_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($cutting_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'"; 
						$cutting_html .='<td '.$bgCol.'>'.$cutting_qnty.'</td>';
	                    $total_cutting+=$cutting_qnty;
	                 	
						if($cons_embr>0)
						{
							if($printing_qnty==0)$bgCol="bgcolor='#FF0000'"; 
							else if($printing_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
							else if($printing_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						}
						else $bgCol='';
	                    $printiss_html .='<td '.$bgCol.'>'.$printing_qnty.'</td>';
	                    $total_print_issue+=$printing_qnty;
	                    
						if($cons_embr>0)
						{
							if($printreceived_qnty==0)$bgCol="bgcolor='#FF0000'"; 
							else if($printreceived_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
							else if($printreceived_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						}
						else $bgCol='';
						
	                    $printrcv_html .='<td '.$bgCol.'>'.$printreceived_qnty.'</td>';
	                    $total_print_rcv+=$printreceived_qnty;
						
						if($cons_embr>0)
						{
							if($emb_qnty==0)$bgCol="bgcolor='#FF0000'"; 
							else if($emb_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
							else if($emb_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						}
						else $bgCol='';
	                    $embroiss_html .='<td '.$bgCol.'>'.$emb_qnty.'</td>';
	                    $total_embro_issue+=$emb_qnty;
	                    
						if($cons_embr>0)
						{
							if($embreceived_qnty==0)$bgCol="bgcolor='#FF0000'"; 
							else if($embreceived_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
							else if($embreceived_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						}
						else $bgCol='';
						
	                    $embrorcv_html .='<td '.$bgCol.'>'.$embreceived_qnty.'</td>';
	                    $total_embro_rcv+=$embreceived_qnty;
						
						if($cons_embr>0)
						{
							if($sp_qnty==0)$bgCol="bgcolor='#FF0000'"; 
							else if($sp_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
							else if($sp_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						}
						else $bgCol='';
	                    $spiss_html .='<td '.$bgCol.'>'.$sp_qnty.'</td>';
	                    $total_sp_issue+=$sp_qnty;
	                    
						if($cons_embr>0)
						{
							if($spreceived_qnty==0)$bgCol="bgcolor='#FF0000'"; 
							else if($spreceived_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
							else if($spreceived_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						}
						else $bgCol='';
						
	                    $sprcv_html .='<td '.$bgCol.'>'.$spreceived_qnty.'</td>';
	                    $total_sp_rcv+=$spreceived_qnty;
						
						if($cons_embr>0)
						{
							if($wash_qnty==0)$bgCol="bgcolor='#FF0000'"; 
							else if($wash_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
							else if($wash_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						}
						else $bgCol='';
	                    $washiss_html .='<td '.$bgCol.'>'.$wash_qnty.'</td>';
	                    $total_wash_issue+=$wash_qnty;
	                    
						if($cons_embr>0)
						{
							if($washreceived_qnty==0)$bgCol="bgcolor='#FF0000'"; 
							else if($washreceived_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
							else if($washreceived_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						}
						else $bgCol='';
						
	                    $washrcv_html .='<td '.$bgCol.'>'.$washreceived_qnty.'</td>';
	                    $total_wash_rcv+=$washreceived_qnty;
	                    
						if($sewingin_qnty==0)$bgCol="bgcolor='#FF0000'"; 
						else if($sewingin_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($sewingin_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'"; 
	                    $sewin_html .='<td '.$bgCol.'>'.$sewingin_qnty.'</td>';
	                    $total_sew_in+=$sewingin_qnty;
	                    
						if($sewingout_qnty==0)$bgCol="bgcolor='#FF0000'"; 
						else if($sewingout_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($sewingout_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";  
	                    $sewout_html .='<td '.$bgCol.'>'.$sewingout_qnty.'</td>';
	                    $total_sew_out+=$sewingout_qnty;
	                    
						/*if($prodRow[csf("finishin_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
						else if($prodRow[csf("finishin_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($prodRow[csf("finishin_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'"; 
	                    $finisin_html .='<td '.$bgCol.'>'.$prodRow[csf("finishin_qnty")].'</td>';
	                    $total_fin_in+=$prodRow[csf("finishin_qnty")];*/
	                    
						if($finish_qnty==0)$bgCol="bgcolor='#FF0000'"; 
						else if($finish_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($finish_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'"; 
	                    $finisout_html .='<td '.$bgCol.'>'.$finish_qnty.'</td>';
	                    $total_fin_out+=$finish_qnty;
						
						if($iron_qnty==0)$bgCol="bgcolor='#FF0000'"; 
						else if($iron_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($iron_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'"; 
	                    $iron_html .='<td '.$bgCol.'>'.$iron_qnty.'</td>';
	                    $total_iron_out+=$iron_qnty;
						
						//if($prodRow[csf("iron_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
						//else if($prodRow[csf("iron_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						//else if($prodRow[csf("iron_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						if($color_variable_setting==2 || $color_variable_setting==3)
						{ 
							$bgCol=="bgcolor='#FFFFFF'";
							$exfact_html.='<td>'.$ex_fact_qty_arr[$colorId][$sizeId].'&nbsp;</td>';
							
							$total_exfact_qnty+=$ex_fact_qty_arr[$colorId][$sizeId];
						}
						
	 				 
					}// end size foreach loop		
					
					?>
						<tr bgcolor="<? echo $bgcolor1; ?>">
	                    	<td><b>Cutting</b></td>
	                        <? echo $cutting_html; ?> 
	                        <td><? echo $total_cutting; ?></td> 
	                    </tr>
	                    <tr bgcolor="<? echo $bgcolor2; ?>">
	                    	<td><b>Print Issue</b></td>
	                        <? echo $printiss_html; ?> 
	                        <td><? echo $total_print_issue; ?></td> 
	                    </tr>
	                    <tr bgcolor="<? echo $bgcolor1; ?>">
	                    	<td><b>Print Received</b></td>
	                        <? echo $printrcv_html; ?> 
	                        <td><? echo $total_print_rcv; ?></td> 
	                    </tr>
	                    <tr bgcolor="<? echo $bgcolor2; ?>">
	                    	<td><b>Embro Issue</b></td>
	                        <? echo $embroiss_html; ?> 
	                        <td><? echo $total_embro_issue; ?></td> 
	                    </tr>
	                    <tr bgcolor="<? echo $bgcolor1; ?>">
	                    	<td><b>Embro Received</b></td>
	                        <? echo $embrorcv_html; ?> 
	                        <td><? echo $total_embro_rcv; ?></td> 
	                    </tr>
	                    <tr bgcolor="<? echo $bgcolor2; ?>">
	                    	<td><b>Issue For Special Works</b></td>
	                        <? echo $spiss_html; ?> 
	                        <td><? echo $total_sp_issue; ?></td> 
	                    </tr>
	                    <tr bgcolor="<? echo $bgcolor1; ?>">
	                    	<td><b>Recv. From Special Works</b></td>
	                        <? echo $sprcv_html; ?> 
	                        <td><? echo $total_sp_rcv; ?></td> 
	                    </tr>
	                    <tr bgcolor="<? echo $bgcolor2; ?>">
	                    	<td><b>Sewing Input</b></td>
	                       <? echo $sewin_html; ?> 
	                        <td><? echo $total_sew_in; ?></td> 
	                    </tr>
	                    <tr bgcolor="<? echo $bgcolor1; ?>">
	                    	<td><b>Sewing Output</b></td>
	                        <? echo $sewout_html; ?> 
	                        <td><? echo $total_sew_out; ?></td> 
	                    </tr>
	                    <tr bgcolor="<? echo $bgcolor2; ?>">
	                    	<td><b>Issue For Wash</b></td>
	                        <? echo $washiss_html; ?> 
	                        <td><? echo $total_wash_issue; ?></td> 
	                    </tr>
	                    <tr bgcolor="<? echo $bgcolor1; ?>">
	                    	<td><b>Recv. From Wash</b></td>
	                        <? echo $washrcv_html; ?> 
	                        <td><? echo $total_wash_rcv; ?></td> 
	                    </tr>
	                    <tr bgcolor="<? echo $bgcolor2; ?>">
	                    	<td><b>Iron Output</b></td>
	                        <? echo $iron_html; ?> 
	                        <td><? echo $total_iron_out; ?></td> 
	                    </tr>
	                   <tr bgcolor="<? echo $bgcolor1; ?>">
	                    	<td><b>Finishing Output</b></td>
	                       <? echo $finisout_html; ?> 
	                        <td><? echo $total_fin_out; ?></td> 
	                    </tr>
	                    <? 
						if($color_variable_setting==2 || $color_variable_setting==3)
						{
							?>
							<tr>
								<td><b>Ex-Factory Qty.</b></td>
								 <? echo $exfact_html; ?> 
								<td><? echo $total_exfact_qnty; ?>&nbsp;</td> 
							</tr>
							<?
						}
						?>
				<?	
				}// end color foreach loop
				?>
	           
			 
	 </table>
	</div>    


	<?
	exit();

}

if ($action=="product_popup")  // All Production Data popup
{
	
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	
	$po_break_down_id=str_replace("'","",$po_break_down_id);
	$item_id=str_replace("'","",$item_id);
	$country_id=str_replace("'","",$country_id);
	$color_id=str_replace("'","",$color_id);
    $prod_popup_type=str_replace("'","",$prod_popup_type);
	$prod_popup_lelel=str_replace("'","",$prod_popup_lelel);
	$prod_popup_lelel_arr=explode('*',$prod_popup_lelel);
	$prod_popup_lelel_type=$prod_popup_lelel_arr[0];
	$prod_popup_lelel_cat=$prod_popup_lelel_arr[1];
	
	
	if($db_type==0) $rmg_process_breakdown=return_field_value("a.rmg_process_breakdown as rmg_process_breakdown"," wo_booking_mst a, wo_booking_dtls b","a.booking_no=b.booking_no and  b.po_break_down_id=$po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id asc limit 1","rmg_process_breakdown");
	else if($db_type==2) $rmg_process_breakdown=return_field_value("rmg_process_breakdown"," (select a.id, a.rmg_process_breakdown  from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id=$po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id asc)","ROWNUM = 1","rmg_process_breakdown");
	
	if($rmg_process_breakdown!="")
	{
		$rmg_process_breakdown_arr=explode("_",$rmg_process_breakdown);
		//var_dump($rmg_process_breakdown_arr);die;
		$cut_panel_rejection=$rmg_process_breakdown_arr[8];
		$chest_printing=$rmg_process_breakdown_arr[2];
		$neck_sleeve_printing=$rmg_process_breakdown_arr[10];
		$embroidery=$rmg_process_breakdown_arr[1];
		$sewing_input=$rmg_process_breakdown_arr[4];
		$garments_wash=$rmg_process_breakdown_arr[3];
		$gmts_finishing=$rmg_process_breakdown_arr[15];
	}
	
	//echo $rmg_process_breakdown;die;
	//select rmg_process_breakdown from (select a.id, a.rmg_process_breakdown  from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id=4444 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id asc)   where  ROWNUM = 1
	//echo "select rmg_process_breakdown as rmg_process_breakdown from  wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and  b.po_break_down_id=$po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $row_num";die;
	
	
	
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$size_Arr_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id order by size_order","size_number_id","size_number_id");
	$line_Arr_library=return_library_array( "select id,line_name from lib_sewing_line where  is_deleted=0 and status_active=1", "id", "line_name" );
	$resource_alocate_line = return_library_array("select id, line_number from prod_resource_mst", "id", "line_number");


	$country_cond="";
	if($country_id>0) $country_cond=" and c.country_id=$country_id";
	$color_cond="";
	if($color_id>0) $color_cond=" and c.color_number_id=$color_id";
	
	$sql_order_size= sql_select("SELECT c.size_number_id, c.order_quantity as order_quantity, c.plan_cut_qnty as plan_cut_qnty
		from wo_po_color_size_breakdown c 
		where c.status_active=1 and c.is_deleted=0 and c.po_break_down_id=$po_break_down_id and c.item_number_id=$item_id $country_cond $color_cond  
		 order by c.size_order");
	foreach($sql_order_size as $row)
	{
		$order_size_qnty[$row[csf('size_number_id')]] +=$row[csf('order_quantity')];
		$plan_order_size_qnty[$row[csf('size_number_id')]] +=$row[csf('plan_cut_qnty')];
	}
	$country_cond="";
	if($country_id>0) $country_cond=" and a.country_id=$country_id";
	$color_cond="";
	if($color_id>0) $color_cond=" and a.color_number_id=$color_id";
	
	$color_size_sql=sql_select( "select a.id, a.color_number_id, a.size_number_id from wo_po_color_size_breakdown a where   a.color_number_id>0 and a.size_number_id>0 and a.po_break_down_id in (".$po_break_down_id.")  $country_cond $color_cond order by a.color_order");
	$color_size_data=array(); $allcolor_id_arr=array();
	foreach($color_size_sql as $row)
	{
		$allcolor_id_arr[$row[csf("color_number_id")]]=$row[csf("color_number_id")];
		$color_size_data[$row[csf("id")]]["color_number_id"]=$row[csf("color_number_id")];
		$color_size_data[$row[csf("id")]]["size_number_id"]=$row[csf("size_number_id")];
	}
	$sql_supplier=sql_select("select id,supplier_name from lib_supplier where status_active =1 and is_deleted=0 order by supplier_name");
	$supplier_arr=array();
	foreach($sql_supplier as $row)
	{
		$supplier_arr[$row[csf('id')]]=$row[csf('supplier_name')];
	}

	//echo "<pre>";
	//print_r($color_size_data); die;
	
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		$prod_print_cond=''; $prod_sourceCond='';
		
		if($prod_popup_lelel_cat=="") $prod_print_cond=''; else $prod_print_cond="and a.embel_name='$prod_popup_lelel_cat'";
		if($prod_popup_lelel_type!=0)
		{
			if($prod_popup_lelel_type=="") $prod_sourceCond=''; else $prod_sourceCond="and a.production_source='$prod_popup_lelel_type'";
		}
 		if($prod_popup_type==10)
		{
			$serving_company_id = [];
			$sql = "SELECT serving_company   FROM pro_garments_production_mst  WHERE po_break_down_id = $po_break_down_id  group by serving_company";
			$sql_res = sql_select($sql);
			// echo $sql_res[0][csf('serving_company')];
			$sql_colsiz="SELECT b.color_size_break_down_id,
			sum(CASE WHEN a.entry_form!=85 THEN b.production_qnty ELSE 0 END)-sum(CASE WHEN a.entry_form=85 THEN b.production_qnty ELSE 0 END) as production_qnty
			from pro_ex_factory_mst a,  pro_ex_factory_dtls b, wo_po_break_down c ,wo_po_color_size_breakdown d
			where a.id=b.mst_id and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond and c.id=d.po_break_down_id and d.id=b.color_size_break_down_id and a.po_break_down_id = c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active in(1,2,3) and d.status_active in(1,2,3) and d.is_deleted=0  group by b.color_size_break_down_id";

			$ex_fac_level=sql_select("select ex_factory  from variable_settings_production where company_name in(select a.company_name from  wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$po_break_down_id ) and variable_list=1 and b.status_active in(1,2,3)");
			if($ex_fac_level[0][csf("ex_factory")]==1)
			{  
				$sql_colsiz="SELECT 
				sum(CASE WHEN entry_form!=85 THEN  ex_factory_qnty ELSE 0 END)-sum(CASE WHEN entry_form=85 THEN  ex_factory_qnty ELSE 0 END) as production_qnty
				from pro_ex_factory_mst 
				where po_break_down_id=$po_break_down_id and item_number_id=$item_id $country_cond and status_active=1 and is_deleted=0";
			}
		}
		else if($prod_popup_type==11)
		{
			 $sql_colsiz="SELECT e.color_number_id,e.size_number_id ,b.color_size_break_down_id, sum(b.production_qnty) as production_qnty,a.serving_company,a.sewing_line  
			from pro_garments_production_mst a, pro_garments_production_dtls b ,wo_po_color_size_breakdown e
			where a.id=b.mst_id    and e.id=b.color_size_break_down_id    and a.production_type='$prod_popup_type' and b.production_type='$prod_popup_type'  and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $prod_print_cond $prod_sourceCond $country_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 group by e.color_number_id,e.size_number_id , b.color_size_break_down_id,a.serving_company,a.sewing_line order by a.serving_company";
		}
 

		else if($prod_popup_type==17)
		{
  			$sql_colsiz="SELECT b.color_id  as color_number_id,c.size_id as size_number_id ,sum(c.size_qty ) as production_qnty,a.working_company_id as serving_company  from ppl_cut_lay_mst a ,ppl_cut_lay_dtls b,ppl_cut_lay_bundle c where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and    c.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.gmt_item_id=$item_id and (c.order_id=$po_break_down_id or b.order_id=$po_break_down_id)  group by b.color_id,c.size_id,a.working_company_id order by a.working_company_id";
 			 
			
			foreach(sql_select($sql_colsiz) as $vals)
			{
				$col_size_wise_lay[$vals[csf("color_number_id")]][$vals[csf("size_number_id")]]+=$vals[csf("production_qnty")];
				if($vals[csf("serving_company")])
				{
					$col_size_wise_lay_company[$vals[csf("color_number_id")]][$vals[csf("size_number_id")]] =$vals[csf("serving_company")];
				
					$allcolor_id_arr_dtls[$vals[csf("serving_company")]][$vals[csf("color_number_id")]]=$vals[csf("color_number_id")];  
					$col_size_wise_lay_company_qnty[$vals[csf("serving_company")]][$vals[csf("color_number_id")]][$vals[csf("size_number_id")]]+=$vals[csf("production_qnty")];

				}		 
				
				 
			}

			 
		}  
  		else if($prod_popup_type==7)
		{
			 $sql_colsiz="SELECT e.color_number_id,e.size_number_id ,b.color_size_break_down_id, sum(b.production_qnty) as 
			 production_qnty,a.serving_company		   
			from pro_garments_production_mst a, pro_garments_production_dtls b ,wo_po_details_master c,wo_po_color_size_breakdown e
			where a.id=b.mst_id  and c.job_no=e.job_no_mst and e.id=b.color_size_break_down_id and a.po_break_down_id=e.po_break_down_id  and a.production_type='$prod_popup_type' and b.production_type='$prod_popup_type' and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $prod_print_cond $prod_sourceCond $country_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0  group by e.color_number_id,e.size_number_id ,b.color_size_break_down_id,a.serving_company order by a.serving_company";
		}
		else
		{  
			 $sql_colsiz="SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty,a.serving_company,a.sewing_line  
			from pro_garments_production_mst a, pro_garments_production_dtls b ,wo_po_details_master c,wo_po_color_size_breakdown e
			where a.id=b.mst_id  and c.job_no=e.job_no_mst and e.id=b.color_size_break_down_id and a.po_break_down_id=e.po_break_down_id  and a.production_type='$prod_popup_type' and b.production_type='$prod_popup_type' and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $prod_print_cond $prod_sourceCond $country_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0  group by b.color_size_break_down_id,a.serving_company,a.sewing_line order by a.serving_company";
		}
		 //echo $sql_colsiz;
		$sql_color_size=sql_select($sql_colsiz);
		$prod_color_size_data=array();
		$prod_color_size_data_working_comp=array();
		$color_size_working_comp=array();
		$color_size_sewingLine=array();
	
		foreach($sql_color_size as $row)
		{
			if($prod_popup_type==11)
			{ 
				$allcolor_id_arr[$row[csf("color_number_id")]]=$row[csf("color_number_id")];
				$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]=$row[csf("color_number_id")];
				$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]=$row[csf("size_number_id")];
				//$color_size_data[$row[csf("color_size_break_down_id")]]["serving_company"]=$row[csf("serving_company")];
				//$col_size_wise_company_arr[$row[csf("serving_company")]] =$row[csf("serving_company")];
			}
			if($prod_popup_type==7)
			{
				$allcolor_id_arr[$row[csf("color_number_id")]]=$row[csf("color_number_id")];
				$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]=$row[csf("color_number_id")];
				$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]=$row[csf("size_number_id")];
			}
			$allcolor_id_arr_dtls[$row[csf("serving_company")]][$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]]["color"]=$row[csf("color_number_id")];

			if($color_size_sewingLine[$row[csf("serving_company")]]["sewing_line"]=="")
			{
				$color_size_sewingLine[$row[csf("serving_company")]]["sewing_line"]=$row[csf("sewing_line")];
			}
			else
			{
				$color_size_sewingLine[$row[csf("serving_company")]]["sewing_line"].=','.$row[csf("sewing_line")];
			}

			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["qty"]+=$row[csf('production_qnty')];
			$prod_color_size_data_working_comp[$row[csf("serving_company")]][$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["qty"]+=$row[csf('production_qnty')];
		}
		//print_r($color_size_sewingLine);
		//echo $prod_popup_type;
		if($prod_popup_type==8)
        {

			$transfer_info_sql="select c.po_break_down_id as po_id,c.color_number_id as colors ,sum( case when a.trans_type=5 and  b.trans_type=5  then b.production_qnty else 0 end) as transfer_in ,sum( case when a.trans_type=6 and  b.trans_type=6  then b.production_qnty else 0 end) as transfer_out,a.serving_company,a.sewing_line from pro_garments_production_mst a,pro_garments_production_dtls b,wo_po_color_size_breakdown c where a.id=b.mst_id and b.color_size_break_down_id=c.id and   a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and a.production_type=10 and b.production_type=10 and a.trans_type in(5,6)  and b.trans_type in(5,6) and a.po_break_down_id=$po_break_down_id group by c.po_break_down_id  ,c.color_number_id,a.serving_company,a.sewing_line order by a.serving_company";
			$transfer_info=sql_select($transfer_info_sql);
			foreach($transfer_info as $trans_val)
			{
				$transfer_info_arr[$trans_val[csf("po_id")]][$trans_val[csf("colors")]]['transfer_in']+=$trans_val[csf("transfer_in")];
				$transfer_info_arr[$trans_val[csf("po_id")]][$trans_val[csf("colors")]]['transfer_out']+=$trans_val[csf("transfer_out")];
			}
		}
		//print_r($prod_color_size_data);
		$table_width=(200+(count($sizearr_order)*60));
		
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <div id="exc"></div>
        </div>
        
		<? ob_start(); ?>
        <div style="width:<? echo $table_width+200; ?>px" align="left" id="details_reports">
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
            	<tr>
                	<th width colspan="2"></th>
                    <th width colspan="<?  echo  count($sizearr_order); ?>">Size</th>
                    <?
                    $colspan = 0;
                     if($prod_popup_type==8)
                     {
                     	if($prod_popup_lelel !=1)
                     	{
                     		$colspan=2;
                     	}
                     	else
                     	{
                     		$colspan = 3;
                     	}
                     } 
                     ?>
                    <th colspan="<? echo $colspan; ?>"></th>
                </tr>
                <tr>

                	<th width="30" >SL</th>
                    <th width="100" >Color Name</th>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                        <?
                    }
                    if($prod_popup_type==8)
                    {
                    	if($prod_popup_lelel ==3)
                     	{
                     		?>
	                    	<th width="100" >Transfer In</th>
	                    	<?
                     	}
                     	else if($prod_popup_lelel ==2)
                     	{
                     		?>
	                    	<th width="100" >Transfer Out</th>
	                    	<?
                     	}
                     	else
                     	{
	                    	?>
	                    	<th width="100" >Transfer In</th>
	                    	<th width="100" >Transfer Out</th>
	                    	<?
                    	}
                    }
                    ?>

                    <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
            	<? $sl=1; $sub_total_prod_poly_qntyy=array(); $sub_totals=0; 
            	foreach($allcolor_id_arr as $inc=>$color_id_val)
            	 { 
            		if($color_id_val)
            		{

            		?>
                <tr>
                 	<td align="center" valign="middle"><? echo $sl; ?></td>
                    <td align="center" valign="middle"><? echo $color_Arr_library[$color_id_val]; ?></td>
                    <?
					$total_prod_poly_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                    	if($prod_popup_type==17)
                    	{
							$sub_total_prod_poly_qntyy[$size_id]+=$col_size_wise_lay[$color_id_val][$size_id];
							 ?>
                    <td align="right"><? echo number_format($col_size_wise_lay[$color_id_val][$size_id],0); $total_prod_poly_qnty+=$col_size_wise_lay[$color_id_val][$size_id] ; ?></td> 

                    <?	}
                    	else
                    	{
							$sub_total_prod_poly_qntyy[$size_id]+=$prod_color_size_data[$color_id_val][$size_id]["qty"];
	                        ?>
	                        <td align="right"><? echo number_format($prod_color_size_data[$color_id_val][$size_id]["qty"],0); $total_prod_poly_qnty+=$prod_color_size_data[$color_id_val][$size_id]["qty"]; ?></td> 
	                        <?  
                    	}
                    }
                    if($prod_popup_type==8)
                    { 
                    	if($prod_popup_lelel ==3)
                     	{
                     		?>
		                    <td align="right"><? $trans_in=$transfer_info_arr[$po_break_down_id][$inc]['transfer_in']; echo number_format($trans_in,0); ?></td>
		                    <?
                     	}
                     	else if($prod_popup_lelel ==2)
                     	{
                     		?>
		                    <td align="right"><? $trans_out=$transfer_info_arr[$po_break_down_id][$inc]['transfer_out']; echo number_format($trans_out,0); ?></td>
		                    <?
                     	}
                     	else
                     	{
		                    ?>
		                    <td align="right"><? $trans_in=$transfer_info_arr[$po_break_down_id][$inc]['transfer_in']; echo number_format($trans_in,0); ?></td>
		                    <td align="right"><? $trans_out=$transfer_info_arr[$po_break_down_id][$inc]['transfer_out']; echo number_format($trans_out,0); ?></td>
		                    <?
	                	}
                	}
               		 ?>
                    <td align="right"><? $totals=$total_prod_poly_qnty+ $trans_in-$trans_out;echo number_format($totals,0); ?></td>
                </tr>
                <? $sub_totals+=$totals; $sl++; 
          		    }
                } 
                ?>
            </tbody>
             <tfoot>
            	<td align="right" colspan="2"><b>Total</b></td>
                <? 
				foreach($sizearr_order as $size_id)
				{
				?>
					<td align="right"><? echo $sub_total_prod_poly_qntyy[$size_id]; ?></td>
				<?	
				}
				if($prod_popup_type==8)
				{
					if($prod_popup_lelel ==3)
                     	{
                     		?>	
								<td colspan="1"></td>
							<?
                     	}
                     	else if($prod_popup_lelel ==2)
                     	{
                     		?>	
								<td colspan="1"></td>
							<?
                     	}
                     	else
                     	{
							?>	
								<td colspan="2"></td>
							<?
						}
				}
                ?>
                <td align="right"> <? echo $sub_totals; ?></td>
            </tfoot>
		</table>
    	</div>
    	<? if($prod_popup_type==8 && $prod_popup_lelel !=1){ die();}?>
    	<!-- ================================ DETAILS PART =================================== -->
        <div style="width:<? echo $table_width+200; ?>px" align="center" id="details_reports">
        <?
			$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name"); 
			$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
			$prod_reso_allocation=array();
		    $nameArray = sql_select("select id, auto_update,company_name from variable_settings_production where variable_list=23 and status_active=1 and is_deleted=0");
			foreach($nameArray as $nameArrayRow)
			{
				$prod_reso_allocation[$nameArrayRow[csf('company_name')]]["auto_update"]= $nameArrayRow[csf('auto_update')];
			}
		   // $prod_reso_allocation = $nameArray[0][csf('auto_update')];
		?>
        <strong>Details</strong>
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width+200; ?>">
            <thead>
            	<tr>
                	<th width colspan="<?  if($prod_popup_type==4 || $prod_popup_type==5 || $prod_popup_type==11){ echo "4"; }else{echo "3";} ?>"></th>
                    <th width colspan="<?  echo  count($sizearr_order); ?>">Size</th>
                    <th colspan="<? if($prod_popup_type==8){echo "3";} ?>"></th>
                </tr>
                <tr>
                	<th width="30" >SL</th>
                  	<th width="200" >Working Company</th>
                    <?
                    if($prod_popup_type==4 || $prod_popup_type==5 || $prod_popup_type==11)
                    {
                    	?>
                    	<th width="100" >Line</th>
                    	<?
                    }
					?>
                    <th width="100" >Color Name</th> <!--$prod_popup_type-->
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                        <?
                    }
                    if($prod_popup_type==8)
                    {
                    	?>
                    	<th width="100" >Transfer In</th>
                    	<th width="100" >Transfer Out</th>

                    	<?
                    }
                    ?>

                    <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
            	<? $sl=1; $sub_total_prod_poly_qnty=array(); $sub_totals=0;
            	   $po_company=sql_select(" SELECT a.company_name from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active in(1,2,3) and b.id in($po_break_down_id)");
            	/*echo "<pre>";
            	print_r($allcolor_id_arr_dtls);die;*/
				//foreach($col_size_wise_company_arr as $dataRow){
				 foreach($allcolor_id_arr_dtls as $company=>$company_data) { 
				 	  				 
				   foreach($company_data as $colors=>$val) { 
				   	if($colors){
				 ?>
                <tr>
                	<td align="center" valign="middle"><? echo $sl; ?></td>
                    <td align="center" valign="middle"><? if($company_library[$company]) echo $company_library[$company];  else echo $supplier_arr[$company ];
                    if($prod_popup_type==10){ echo $company_library[$sql_res[0][csf('serving_company')]]; }
						
					?></td>
                    <?
                    if($prod_popup_type==4 || $prod_popup_type==5 || $prod_popup_type==11)
                    {
                    	?>
                    	 <td align="center" valign="middle"><? 
						if($prod_reso_allocation[$po_company[0][csf("company_name")]]["auto_update"]==1)
						{
							$serving_company_lines=explode(",",$color_size_sewingLine[$company]["sewing_line"]);
							$sewing_line_name = "";
							$line_duplicate_arr=array();
							foreach($serving_company_lines as $lines_id)
							{
								$sewing_line =  $resource_alocate_line[$lines_id];
								$sewing_line_arr = explode(",", $sewing_line);

								foreach ($sewing_line_arr as $line_id) 
								{
								//$lines= $prod_reso_arr[$line_id] ;
								//foreach(explode(",", $lines) as $key=>$value)
								//{
									if($line_duplicate_arr[$line_id]=="")
									{
										$sewing_line_name.=$lineArr[$line_id]." ,";
										$line_duplicate_arr[$line_id]=$line_id;
									}
									
								//}
								}

							}
						    
							$sewing_line_name = chop($sewing_line_name, ",");
							echo $sewing_line_name;

						}
						else
						{  
							$sewing_line =  $color_size_sewingLine[$company]["sewing_line"];
							foreach(explode(",", $sewing_line) as $key=>$value)
							{
								$sewing_line_name.=$lineArr[$value]." ,";
							}
								$sewing_line_name = chop($sewing_line_name, ",");
						   		echo $sewing_line_name;
						}
						 ?></td>
                    	<?
                    }
					?>
                    <td align="center" valign="middle"><? echo $color_Arr_library[$colors]; ?></td>
                    <?
					$total_prod_poly_qnty=0; 
                    foreach($sizearr_order as $size_id)
                    {
						
                    	if($prod_popup_type==17)
                    	{ 
						$sub_total_prod_poly_qnty[$size_id]+=$col_size_wise_lay_company_qnty[$company][$colors][$size_id];
						?>
                    <td align="right"><? 
					echo number_format($col_size_wise_lay_company_qnty[$company][$colors][$size_id],0);$total_prod_poly_qnty+=$col_size_wise_lay_company_qnty[$company][$colors][$size_id]; 
					?></td> 

                    <?	}
                    	else{
							$sub_total_prod_poly_qnty[$size_id]+=$prod_color_size_data_working_comp[$company][$colors][$size_id]["qty"];
                        ?>
                        <td align="right"><? echo number_format($prod_color_size_data_working_comp[$company][$colors][$size_id]["qty"],0); $total_prod_poly_qnty+=$prod_color_size_data_working_comp[$company][$colors][$size_id]["qty"]; ?></td> 
                        <?  }
                    }
                    if($prod_popup_type==8)
                    { 
                    ?>
                    <td align="right"><? $trans_in=$transfer_info_arr[$po_break_down_id][$colors]['transfer_in']; echo number_format($trans_in,0); ?></td>
                    <td align="right"><? $trans_out=$transfer_info_arr[$po_break_down_id][$colors]['transfer_out']; echo number_format($trans_out,0); ?></td>
                    <?
                	}
               		 ?>
                    <td align="right"><? $totals=$total_prod_poly_qnty+ $trans_in-$trans_out;echo number_format($totals,0); ?></td>
                </tr>
                <?			
                   $sub_totals+=$totals; $sl++;
               }
				}
				} ?>
            </tbody>
            <tfoot>
            	<td align="right" colspan=" <? if($prod_popup_type==4 || $prod_popup_type==5 || $prod_popup_type==11){echo "4";}else{echo "3";} ?>"><b>Total</b></td>
                <? 
				foreach($sizearr_order as $size_id)
				{
				?>
					<td align="right"><? echo $sub_total_prod_poly_qnty[$size_id]; ?></td>
				<?	
				}
				if($prod_popup_type==8){
				?>	
					<td colspan="2"></td>
				<?	
				}
                ?>
                <td align="right"> <? echo $sub_totals; ?></td>
            </tfoot>
		</table>
    	</div>
    	</div>
		<?
		
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
		//echo "$total_data####$filename####$reportType";
		$filename=$filename;

		?>
		<script>
            document.getElementById('exc').innerHTML='<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />&nbsp;<a href="<? echo $filename;?>" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>';
		</script>

		<?		
		die;
			
	if($prod_popup_type==1 && $prod_popup_lelel_type==1) //for cutting
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b
	where a.id=b.mst_id and a.production_type=1 and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id  $country_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	group by b.color_size_break_down_id");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]] +=$row[csf('production_qnty')]; 
		}
		$table_width=(300+(count($sizearr_order)*60));
		
		
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
                <thead>
                    <tr>
                        <th width="100" >Color Name</th>
                        <th width="100">Production Type</th>
                        <?
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                            <?
                        }
                        ?>
                        <th >Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td rowspan="4" align="center" valign="middle" ><? echo $color_Arr_library[$color_id]; ?></td>
                        <td >Order Qty.</td>
                        <?
						$total_ord_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Plan Cut Qty.</td>
                        <?
						$total_plan_ord_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0); $total_plan_ord_qnty+=$plan_order_size_qnty[$size_id]; ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_plan_ord_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Cutting Qty.</td>
                        <?
						$total_color_size_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right" ><? echo number_format($prod_color_size_data[$size_id],0); $total_color_size_qnty+=$prod_color_size_data[$size_id];  ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_color_size_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Cutting Balance.</td>
                        <?
						$total_cut_balance=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right" title="Cutting Qty-Plan Cut Qty."><? $cut_balance=($plan_order_size_qnty[$size_id]-$prod_color_size_data[$size_id]); echo number_format($cut_balance,0); $total_cut_balance+=$cut_balance; ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_cut_balance,0); ?></td>
                    </tr>
                </tbody>
            </table>
            <br />
    	</div>
    	<?
		
	}
	else if($prod_popup_type==2 && $prod_popup_lelel_type==1 && $prod_popup_lelel_cat==1) //for emb print inhouse issue
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b
	where a.id=b.mst_id and a.production_type=2 and a.embel_name=1 and a.production_source=1 and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id ");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]] +=$row[csf('production_qnty')];
		}
		$table_width=(300+(count($sizearr_order)*60));
		?>
        <div id="data_panel"  style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
                <thead>
                    <tr>
                        <th width="100" >Color Name</th>
                        <th width="100">Production Type</th>
                        <?
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                            <?
                        }
                        ?>
                        <th width="100" >Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td rowspan="5" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                        <td>Order Qty.</td>
                        <?
						$total_ord_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Plan Cut Qty.</td>
                        <?
						$total_plan_ord_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0); $total_plan_ord_qnty+=$plan_order_size_qnty[$size_id]; ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_plan_ord_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Print Sent Plan Qty.</td>
                        <?
						$total_print_sent_plan_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right">
                            <?
                            $print_sent_plan_cut=$plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$cut_panel_rejection)/100;
                            echo number_format($print_sent_plan_cut,0); $total_print_sent_plan_qnty+=$print_sent_plan_cut;
                            ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_print_sent_plan_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Print Sent Qty.</td>
                        <?
						$total_print_sent_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format( $prod_color_size_data[$size_id],0); $total_print_sent_qnty+=$prod_color_size_data[$size_id]; ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_print_sent_qnty,0); ?></td>
                    </tr>
                    <tr>

                        <td>Print Sent Balance</td>
                        <?
						$total_print_sent_balence=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right">
                            <?
                            $print_sent_balence=(($plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$cut_panel_rejection)/100)-$prod_color_size_data[$size_id]);
                             echo number_format($print_sent_balence,0); $total_print_sent_balence+=$print_sent_balence;
                            ?>
                             </td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_print_sent_balence,0); ?></td>
                    </tr>
                </tbody>
            </table>
    	</div>
    	<?
		
		
	}
	else if($prod_popup_type==2 && $prod_popup_lelel_type==1 && $prod_popup_lelel_cat==2) //for emb print subcontract issue
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b 
	where a.id=b.mst_id and a.production_type=2 and a.embel_name=1 and a.production_source<>1 and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by b.color_size_break_down_id ");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]] +=$row[csf('production_qnty')];
		}
		$table_width=(300+(count($sizearr_order)*60));
		?>
        <div id="data_panel"  style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
                <thead>
                    <tr>
                        <th width="100" >Color Name</th>
                        <th width="100">Production Type</th>
                        <?
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                            <?
                        }
                        ?>
                        <th width="100" >Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td rowspan="5" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                        <td>Order Qty.</td>
                        <?
						$total_ord_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Plan Cut Qty.</td>
                        <?
						$total_plan_ord_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0); $total_plan_ord_qnty+=$plan_order_size_qnty[$size_id]; ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_plan_ord_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Print Sent Plan Qty.</td>
                        <?
						$total_print_sent_plan_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right">
                            <?
                            $print_sent_plan_cut=$plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$cut_panel_rejection)/100;
                            echo number_format($print_sent_plan_cut,0); $total_print_sent_plan_qnty+=$print_sent_plan_cut;
                            ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_print_sent_plan_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Print Sent Qty.</td>
                        <?
						$total_print_sent_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format( $prod_color_size_data[$size_id],0); $total_print_sent_qnty+=$prod_color_size_data[$size_id]; ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_print_sent_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Print Sent Balance</td>
                        <?
						$total_print_sent_balence=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right">
                            <?
                            $print_sent_balence=(($plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$cut_panel_rejection)/100)-$prod_color_size_data[$size_id]);
                             echo number_format($print_sent_balence,0); $total_print_sent_balence+=$print_sent_balence;
                            ?>
                             </td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_print_sent_balence,0); ?></td>
                    </tr>
                </tbody>
            </table>
    	</div>
    	<?
		
		
	}
	else if($prod_popup_type==3 && $prod_popup_lelel_type==1  && $prod_popup_lelel_cat==1) //for emb print inhouse receive
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b 
	where a.id=b.mst_id and a.production_type=3 and a.embel_name=1  and a.production_source=1 and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by b.color_size_break_down_id ");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]] +=$row[csf('production_qnty')];
		}
		$table_width=(300+(count($sizearr_order)*60));
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
                <thead>
                    <tr>
                        <th width="100" >Color Name</th>
                        <th width="100">Production Type</th>
                        <?
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                            <?
                        }
                        ?>
                        <th width="100" >Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td rowspan="5" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                        <td>Order Qty.</td>
                        <?
						$total_ord_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Plan Cut Qty.</td>
                        <?
						$total_plan_ord_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0); $total_plan_ord_qnty+=$plan_order_size_qnty[$size_id]; ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_plan_ord_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Print Rcvd. Plan Qty.</td>
                        <?
						$total_print_sent_plan_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right">
                            <?
                            $total_cut_reject=$cut_panel_rejection+$chest_printing+$neck_sleeve_printing;
                            $print_sent_plan_cut=$plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100;
                            echo number_format($print_sent_plan_cut,0); $total_print_sent_plan_qnty +=$print_sent_plan_cut;
                            ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_print_sent_plan_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Print Received Qty.</td>
                        <?
						$total_prod_print_rcv_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format( $prod_color_size_data[$size_id],0); $total_prod_print_rcv_qnty+= $prod_color_size_data[$size_id]; ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_prod_print_rcv_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Print Rcvd. Balance</td>
                        <?
						$total_print_sent_balance=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right">
                            <?
                            $print_sent_balence=(($plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100)-$prod_color_size_data[$size_id]);
                             echo number_format($print_sent_balence,0); $total_print_sent_balance+=$print_sent_balence;
                            ?>
                             </td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_print_sent_balance,0); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    	<?
	}
	else if($prod_popup_type==3 && $prod_popup_lelel_type==1  && $prod_popup_lelel_cat==2) //for emb print subcontract receive
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b
	where a.id=b.mst_id and a.production_type=3 and a.embel_name=1  and a.production_source<>1 and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id ");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]] +=$row[csf('production_qnty')];
		}
		$table_width=(300+(count($sizearr_order)*60));
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
                <thead>
                    <tr>
                        <th width="100" >Color Name</th>
                        <th width="100">Production Type</th>
                        <?
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                            <?
                        }
                        ?>
                        <th width="100" >Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td rowspan="5" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                        <td>Order Qty.</td>
                        <?
						$total_ord_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Plan Cut Qty.</td>
                        <?
						$total_plan_ord_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0); $total_plan_ord_qnty+=$plan_order_size_qnty[$size_id]; ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_plan_ord_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Print Rcvd. Plan Qty.</td>
                        <?
						$total_print_sent_plan_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right">
                            <?
                            $total_cut_reject=$cut_panel_rejection+$chest_printing+$neck_sleeve_printing;
                            $print_sent_plan_cut=$plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100;
                            echo number_format($print_sent_plan_cut,0); $total_print_sent_plan_qnty +=$print_sent_plan_cut;
                            ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_print_sent_plan_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Print Received Qty.</td>
                        <?
						$total_prod_print_rcv_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format( $prod_color_size_data[$size_id],0); $total_prod_print_rcv_qnty+= $prod_color_size_data[$size_id]; ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_prod_print_rcv_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Print Rcvd. Balance</td>
                        <?
						$total_print_sent_balance=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right">
                            <?
                            $print_sent_balence=(($plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100)-$prod_color_size_data[$size_id]);
                             echo number_format($print_sent_balence,0); $total_print_sent_balance+=$print_sent_balence;
                            ?>
                             </td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_print_sent_balance,0); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    	<?
	}
	else if($prod_popup_type==2 && $prod_popup_lelel_type==2  && $prod_popup_lelel_cat==1) //for emb ebroidery inhouse issue
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b 
	where a.id=b.mst_id and a.production_type=2 and a.embel_name=2 and a.production_source=1  and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by b.color_size_break_down_id ");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]] +=$row[csf('production_qnty')];
		}
		$table_width=(300+(count($sizearr_order)*60));
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                <th width="100" >Color Name</th>
                <th width="100">Production Type</th>
                <?
                foreach($sizearr_order as $size_id)
                {
					?>
					<th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
					<?
                }
                ?>
                <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="5" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                    <td>Order Qty.</td>
                    <?
                    $total_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
                    
                </tr>
                <tr>
                    <td>Plan Cut Qty.</td>
                    <?
                    $total_plan_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0); $total_plan_ord_qnty+=$plan_order_size_qnty[$size_id]; ?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_plan_ord_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Emb. Sent Plan Qty</td>
                    <?
					$total_plan_sent_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$total_cut_reject=$cut_panel_rejection+$chest_printing+$neck_sleeve_printing;
						$print_emb_plan_cut=$plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100;
						echo number_format($print_emb_plan_cut,0); $total_plan_sent_qnty+=$print_emb_plan_cut;
						?>
                        </td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_plan_sent_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Print Received Qty.</td>
                    <?
					$total_prod_print_rcv_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format( $prod_color_size_data[$size_id],0); $total_prod_print_rcv_qnty+=$prod_color_size_data[$size_id];?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_prod_print_rcv_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Print Rcvd. Balance</td>
                    <?
					$total_print_rcv_balance=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$print_sent_balence=(($plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100)-$prod_color_size_data[$size_id]);
						 echo number_format($print_sent_balence,0); $total_print_rcv_balance+=$print_sent_balence;
						?>
                         </td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_print_rcv_balance,0); ?></td>
                </tr>
            </tbody>
		</table>
        </div>
    	<?
	}
	else if($prod_popup_type==2 && $prod_popup_lelel_type==2  && $prod_popup_lelel_cat==2) //for emb ebroidery subcontract issue
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b
	where a.id=b.mst_id and a.production_type=2 and a.embel_name=2 and a.production_source<>1  and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by b.color_size_break_down_id");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]] +=$row[csf('production_qnty')];
		}
		$table_width=(300+(count($sizearr_order)*60));
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                <th width="100" >Color Name</th>
                <th width="100">Production Type</th>
                <?
                foreach($sizearr_order as $size_id)
                {
					?>
					<th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
					<?
                }
                ?>
                <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="5" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                    <td>Order Qty.</td>
                    <?
                    $total_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
                    
                </tr>
                <tr>
                    <td>Plan Cut Qty.</td>
                    <?
                    $total_plan_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0); $total_plan_ord_qnty+=$plan_order_size_qnty[$size_id]; ?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_plan_ord_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Emb. Sent Plan Qty</td>
                    <?
					$total_plan_sent_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$total_cut_reject=$cut_panel_rejection+$chest_printing+$neck_sleeve_printing;
						$print_emb_plan_cut=$plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100;
						echo number_format($print_emb_plan_cut,0); $total_plan_sent_qnty+=$print_emb_plan_cut;
						?>
                        </td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_plan_sent_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Print Received Qty.</td>
                    <?
					$total_prod_print_rcv_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format( $prod_color_size_data[$size_id],0); $total_prod_print_rcv_qnty+=$prod_color_size_data[$size_id];?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_prod_print_rcv_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Print Rcvd. Balance</td>
                    <?
					$total_print_rcv_balance=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$print_sent_balence=(($plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100)-$prod_color_size_data[$size_id]);
						 echo number_format($print_sent_balence,0); $total_print_rcv_balance+=$print_sent_balence;
						?>
                         </td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_print_rcv_balance,0); ?></td>
                </tr>
            </tbody>
		</table>
        </div>
    	<?
	}
	else if($prod_popup_type==3 && $prod_popup_lelel_type==2 && $prod_popup_lelel_cat==1) //for emb ebroidery inhouse receive
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b
	where a.id=b.mst_id and a.production_type=3 and a.embel_name=2 and a.production_source=1  and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]] +=$row[csf('production_qnty')];
		}
		$table_width=(300+(count($sizearr_order)*60));
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
                <thead>
                    <tr>
                    <th width="100" >Color Name</th>
                    <th width="100">Production Type</th>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                        <?
                    }
                    ?>
                    <th width="100" >Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td rowspan="5" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                        <td>Order Qty.</td>
                        <?
                        $total_ord_qnty=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
							<?
						}
						?>
						<td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Plan Cut Qty.</td>
                        <?
						$total_plan_ord_qnty=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0); $total_plan_ord_qnty+=$plan_order_size_qnty[$size_id]; ?></td> 
							<?
						}
						?>
						<td align="right"><? echo number_format($total_plan_ord_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Emb. Rcvd. Plan Qty.</td>
                        <?
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right">
                            <?
							$total_rcv_plan_qnty=0;
                            $total_cut_reject=$cut_panel_rejection+$chest_printing+$neck_sleeve_printing+$embroidery;
                            $print_enb_plan_cut=$plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100;
                            echo number_format($print_enb_plan_cut,0); $total_rcv_plan_qnty+=$print_enb_plan_cut;
                            ?>
                            </td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_rcv_plan_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Emb. Rcvd. Qty.</td>
                        <?
						$total_pord_recv_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format( $prod_color_size_data[$size_id],0); $total_pord_recv_qnty+=$prod_color_size_data[$size_id]; ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_pord_recv_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Emb. Rcvd. Balance</td>
                        <?
						$total_emb_balect=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right">
                            <?
                            $print_sent_balence=(($plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100)-$prod_color_size_data[$size_id]);
                             echo number_format($print_sent_balence,0); $total_emb_balect+=$print_sent_balence;
                            ?>
                             </td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_emb_balect,0); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    	<?
	}
	else if($prod_popup_type==3 && $prod_popup_lelel_type==2 && $prod_popup_lelel_cat==2) //for emb ebroidery subcon receive
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b 
	where a.id=b.mst_id and a.production_type=3 and a.embel_name=2 and a.production_source<>1  and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id ");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]] =$row[csf('production_qnty')];
		}
		$table_width=(300+(count($sizearr_order)*60));
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
                <thead>
                    <tr>
                    <th width="100" >Color Name</th>
                    <th width="100">Production Type</th>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                        <?
                    }
                    ?>
                    <th width="100" >Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td rowspan="5" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                        <td>Order Qty.</td>
                        <?
                        $total_ord_qnty=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
							<?
						}
						?>
						<td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Plan Cut Qty.</td>
                        <?
						$total_plan_ord_qnty=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0); $total_plan_ord_qnty+=$plan_order_size_qnty[$size_id]; ?></td> 
							<?
						}
						?>
						<td align="right"><? echo number_format($total_plan_ord_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Emb. Rcvd. Plan Qty.</td>
                        <?
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right">
                            <?
							$total_rcv_plan_qnty=0;
                            $total_cut_reject=$cut_panel_rejection+$chest_printing+$neck_sleeve_printing+$embroidery;
                            $print_enb_plan_cut=$plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100;
                            echo number_format($print_enb_plan_cut,0); $total_rcv_plan_qnty+=$print_enb_plan_cut;
                            ?>
                            </td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_rcv_plan_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Emb. Rcvd. Qty.</td>
                        <?
						$total_pord_recv_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format( $prod_color_size_data[$size_id],0); $total_pord_recv_qnty+=$prod_color_size_data[$size_id]; ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_pord_recv_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Emb. Rcvd. Balance</td>
                        <?
						$total_emb_balect=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right">
                            <?
                            $print_sent_balence=(($plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100)-$prod_color_size_data[$size_id]);
                             echo number_format($print_sent_balence,0); $total_emb_balect+=$print_sent_balence;
                            ?>
                             </td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_emb_balect,0); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    	<?
	}
	else if($prod_popup_type==2 && $prod_popup_lelel_type==4) //for emb sp issue
	{
		/*$sql_color_size= sql_select("SELECT c.size_number_id, sum(b.production_qnty) as production_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
	where a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.production_type=2 and a.embel_name=4 and c.po_break_down_id=$po_break_down_id and c.item_number_id=$item_id and c.country_id=$country_id and c.color_number_id=$color_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
	group by c.size_number_id
	order by c.size_number_id");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$row[csf('size_number_id')]] =$row[csf('production_qnty')];
		}
		$table_width=(200+count($table_width)*60);
		?>
		<div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                <th width="100" >Color Name</th>
                <th width="100">Production Type</th>
                <?
                foreach($sizearr_order as $size_id)
                {
					?>
					<th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
					<?
                }
                ?>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="5" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                    <td>Order Qty.</td>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
						?>
						<td align="right"><? echo number_format($order_size_qnty[$size_id],0);?></td> 
						<?
                    }
                    ?>
                </tr>
                <tr>
                    <td>Plan Cut Qty.</td>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0);?></td> 
                        <?
                    }
                    ?>
                </tr>
                <tr>
                    <td>Emb. Rcvd. Plan Qty.</td>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$total_cut_reject=$cut_panel_rejection+$chest_printing+$neck_sleeve_printing+$embroidery;
						$print_enb_plan_cut=$plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100;
						echo number_format($print_enb_plan_cut,0);
						?></td> 
                        <?
                    }
                    ?>
                </tr>
                <tr>
                    <td>Emb. Rcvd. Qty.</td>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format( $prod_color_size_data[$size_id],0);?></td> 
                        <?
                    }
                    ?>
                </tr>
                <tr>
                    <td>Emb. Rcvd. Balance</td>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$print_sent_balence=($prod_color_size_data[$size_id]-($plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100));
						 echo number_format($print_sent_balence,0);
						?>
                         </td> 
                        <?
                    }
                    ?>
                </tr>
            </tbody>
		</table>
		</div>
    	<?*/
	}
	else if($prod_popup_type==3 && $prod_popup_lelel_type==4) //for emb sp receive
	{
		/*$sql_color_size= sql_select("SELECT c.size_number_id, sum(b.production_qnty) as production_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
	where a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.production_type=3 and a.embel_name=4 and c.po_break_down_id=$po_break_down_id and c.item_number_id=$item_id and c.country_id=$country_id and c.color_number_id=$color_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
	group by c.size_number_id
	order by c.size_number_id");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$row[csf('size_number_id')]] =$row[csf('production_qnty')];
		}*/
	}
	else if($prod_popup_type==4 && $prod_popup_lelel_type==1) //for sewing input
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id,
		sum(case when a.production_source=1 then b.production_qnty else 0 end) as production_inhouse_qnty,
		sum(case when a.production_source=3 then b.production_qnty else 0 end) as production_subcon_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b
	where a.id=b.mst_id and a.production_type=4 and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id ");
	
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["production_inhouse_qnty"]+=$row[csf('production_inhouse_qnty')];
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["production_subcon_qnty"]+=$row[csf('production_subcon_qnty')];
		}
		
		$table_width=(300+(count($sizearr_order)*60));
		
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                <th width="100" >Color Name</th>
                <th width="100">Production Type</th>
                <?
                foreach($sizearr_order as $size_id)
                {
					?>
					<th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
					<?
                }
                ?>
                <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="6" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                    <td>Order Qty.</td>
                    <?
					$total_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
				</tr>
				<tr>
					<td>Plan Cut Qty.</td>
					<?
					$total_plan_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0); $total_plan_ord_qnty+=$plan_order_size_qnty[$size_id]; ?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_plan_ord_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Sewing Input Plan Qty.</td>
                    <?
					$sewing_in_plan_cut=$total_sew_plan_qnty=0;
					
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$total_cut_reject=($cut_panel_rejection+$chest_printing+$neck_sleeve_printing+$embroidery);
						$sewing_in_plan_cut=($plan_order_size_qnty[$size_id]-(($plan_order_size_qnty[$size_id]*$total_cut_reject)/100)); 
						echo number_format($sewing_in_plan_cut,0); $total_sew_plan_qnty+=$sewing_in_plan_cut;
						?>
                        </td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_sew_plan_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Inhouse Sewing Input Qty.</td>
                    <?
					$total_prod_sew_inhouse_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($prod_color_size_data[$size_id]["production_inhouse_qnty"],0); $total_prod_sew_inhouse_qnty+=$prod_color_size_data[$size_id]["production_inhouse_qnty"]; ?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_prod_sew_inhouse_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Sub Con. Sewing Input Qty.</td>
                    <?
					$total_production_subcon_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($prod_color_size_data[$size_id]["production_subcon_qnty"],0); $total_production_subcon_qnty+=$prod_color_size_data[$size_id]["production_subcon_qnty"];?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_production_subcon_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Sewing Input Balance</td>
                    <?
					$total_prod_in_balance=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<? 
						$total_in_balance= ($plan_order_size_qnty[$size_id]-(($plan_order_size_qnty[$size_id]*$total_cut_reject)/100))-($prod_color_size_data[$size_id]["production_subcon_qnty"]+$prod_color_size_data[$size_id]["production_inhouse_qnty"]);
						echo number_format($total_in_balance,0); $total_prod_in_balance+=$total_in_balance;
						?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_prod_in_balance,0); ?></td>
                </tr>
            </tbody>
		</table>
    	</div>
    	<?
		
	}
	else if($prod_popup_type==4 && $prod_popup_lelel_type==11) //for sewing input inhouse
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(case when a.production_source=1 then b.production_qnty else 0 end) as production_inhouse_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b
	where a.id=b.mst_id and a.production_type=4 and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by b.color_size_break_down_id ");
	
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["production_inhouse_qnty"]+=$row[csf('production_inhouse_qnty')];
		}
		
		$table_width=(200+(count($sizearr_order)*60));
		
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                <th width="100" >Color Name</th>
                <?
                foreach($sizearr_order as $size_id)
                {
					?>
					<th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
					<?
                }
                ?>
                <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                    <?
					$total_prod_sew_inhouse_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($prod_color_size_data[$size_id]["production_inhouse_qnty"],0); $total_prod_sew_inhouse_qnty+=$prod_color_size_data[$size_id]["production_inhouse_qnty"]; ?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_prod_sew_inhouse_qnty,0); ?></td>
                </tr>
            </tbody>
		</table>
    	</div>
    	<?
		
	}
	else if($prod_popup_type==4 && $prod_popup_lelel_type==13) //for sewing input subcon
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(case when a.production_source=3 then b.production_qnty else 0 end) as production_subcon_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b
	where a.id=b.mst_id and a.production_type=4 and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id");
	
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["production_subcon_qnty"]+=$row[csf('production_subcon_qnty')];
		}
		
		$table_width=(200+(count($sizearr_order)*60));
		
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                <th width="100" >Color Name</th>
                <?
                foreach($sizearr_order as $size_id)
                {
					?>
					<th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
					<?
                }
                ?>
                <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                    <?
					$total_production_subcon_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($prod_color_size_data[$size_id]["production_subcon_qnty"],0); $total_production_subcon_qnty+=$prod_color_size_data[$size_id]["production_subcon_qnty"];?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_production_subcon_qnty,0); ?></td>
                </tr>
            </tbody>
		</table>
    	</div>
    	<?
		
	}
	else if($prod_popup_type==5 && $prod_popup_lelel_type==1) //for emb sewing output
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id,
		sum(case when a.production_source=1 then b.production_qnty else 0 end) as production_inhouse_qnty,
		sum(case when a.production_source=3 then b.production_qnty else 0 end) as production_subcon_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b 
	where a.id=b.mst_id and a.production_type=5 and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id");
		
		$prod_color_size_data=array();
	
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["production_inhouse_qnty"]+=$row[csf('production_inhouse_qnty')];
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["production_subcon_qnty"]+=$row[csf('production_subcon_qnty')];
		}
		
		$table_width=(300+(count($sizearr_order)*60));
		
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                    <th width="100" >Color Name</th>
                    <th width="100">Production Type</th>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                        <?
                    }
                    ?>
                    <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="6" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                    <td>Order Qty.</td>
                    <?
					$total_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
				</tr>
				<tr>
					<td>Plan Cut Qty.</td>
					<?
					$total_plan_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0); $total_plan_ord_qnty+=$plan_order_size_qnty[$size_id]; ?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_plan_ord_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Sewing Plan Output Qty.</td>
                    <?
					$sewing_in_plan_cut=$total_sew_out_plan_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$total_cut_reject=($cut_panel_rejection+$chest_printing+$neck_sleeve_printing+$embroidery+$sewing_input);  
						$sewing_out_plan_cut=($plan_order_size_qnty[$size_id]-(($plan_order_size_qnty[$size_id]*$total_cut_reject)/100)); 
						echo number_format($sewing_out_plan_cut,0); $total_sew_out_plan_qnty+=$sewing_out_plan_cut;
						?>
                        </td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_sew_out_plan_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Inhouse Sewing Output Qty.</td>
                    <?
					$total_prod_sew_in_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($prod_color_size_data[$size_id]["production_inhouse_qnty"],0); $total_prod_sew_in_qnty+=$prod_color_size_data[$size_id]["production_inhouse_qnty"]; ?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_prod_sew_in_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Sub Con. Sewing Output Qty.</td>
                    <?
					$total_prod_sew_out_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($prod_color_size_data[$size_id]["production_subcon_qnty"],0); $total_prod_sew_out_qnty+=$prod_color_size_data[$size_id]["production_subcon_qnty"]; ?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_prod_sew_out_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Sewing Output Balance</td>
                    <?
					$total_out_balance_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$total_out_balance= ($plan_order_size_qnty[$size_id]-(($plan_order_size_qnty[$size_id]*$total_cut_reject)/100))-($prod_color_size_data[$size_id]["production_subcon_qnty"]+$prod_color_size_data[$size_id]["production_inhouse_qnty"]);
						echo number_format($total_out_balance,0); $total_out_balance_qnty+=$total_out_balance;
						?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_out_balance_qnty,0); ?></td>
                </tr>
            </tbody>
		</table>
    	</div>
    	<?
	}
	else if($prod_popup_type==5 && $prod_popup_lelel_type==11) //for emb sewing output Inhouse
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id,
		sum(case when a.production_source=1 then b.production_qnty else 0 end) as production_inhouse_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b 
	where a.id=b.mst_id and a.production_type=5  and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id");
		
		$prod_color_size_data=array();
	
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["production_inhouse_qnty"]+=$row[csf('production_inhouse_qnty')];
		}
		
		$table_width=(200+(count($sizearr_order)*60));
		
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                    <th width="100" >Color Name</th>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                        <?
                    }
                    ?>
                    <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                    <?
					$total_prod_sew_in_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($prod_color_size_data[$size_id]["production_inhouse_qnty"],0); $total_prod_sew_in_qnty+=$prod_color_size_data[$size_id]["production_inhouse_qnty"]; ?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_prod_sew_in_qnty,0); ?></td>
                </tr>
            </tbody>
		</table>
    	</div>
    	<?
	}
	else if($prod_popup_type==5 && $prod_popup_lelel_type==13) //for emb sewing output
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id,
		sum(case when a.production_source=3 then b.production_qnty else 0 end) as production_subcon_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b 
	where a.id=b.mst_id and a.production_type=5  and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by b.color_size_break_down_id ");
		
		$prod_color_size_data=array();
	
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["production_subcon_qnty"]+=$row[csf('production_subcon_qnty')];
		}
		
		$table_width=(200+(count($sizearr_order)*60));
		
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                    <th width="100" >Color Name</th>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                        <?
                    }
                    ?>
                    <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                    <?
					$total_prod_sew_out_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($prod_color_size_data[$size_id]["production_subcon_qnty"],0); $total_prod_sew_out_qnty+=$prod_color_size_data[$size_id]["production_subcon_qnty"]; ?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_prod_sew_out_qnty,0); ?></td>
                </tr>
            </tbody>
		</table>
    	</div>
    	<?
	}
	else if($prod_popup_type==2 && $prod_popup_lelel_type==3) //for emb wash issue
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b
	where a.id=b.mst_id and a.production_type=2 and a.embel_name=3 and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id ");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]] +=$row[csf('production_qnty')];
		}
		$table_width=(300+(count($sizearr_order)*60));
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                    <th width="100" >Color Name</th>
                    <th width="100">Production Type</th>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                        <?
                    }
                    ?>
                    <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="5" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                    <td>Order Qty.</td>
                    <?
					$total_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
				</tr>
				<tr>
					<td>Plan Cut Qty.</td>
					<?
					$total_plan_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0); $total_plan_ord_qnty+=$plan_order_size_qnty[$size_id]; ?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_plan_ord_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Wash Sent Plan Qty</td>
                    <?
					$total_emp_plan_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$total_cut_reject=$cut_panel_rejection+$chest_printing+$neck_sleeve_printing+$embroidery+$sewing_input;
						$print_enb_plan_cut=$plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100;
						echo number_format($print_enb_plan_cut,0); $total_emp_plan_qnty+=$print_enb_plan_cut;
						?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_emp_plan_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Wash Sent Qty.</td>
                    <?
					$total_prod_sent_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format( $prod_color_size_data[$size_id],0); $total_prod_sent_qnty+=$prod_color_size_data[$size_id];?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_prod_sent_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Wash Sent  Balance</td>
                    <?
					$total_wash_balance=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$print_sent_balence=(($plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100)-$prod_color_size_data[$size_id]);
						 echo number_format($print_sent_balence,0); $total_wash_balance+=$print_sent_balence;
						?>
                         </td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_wash_balance,0); ?></td>
                </tr>
            </tbody>
		</table>
        </div>
    	<?
		
	}
	else if($prod_popup_type==3 && $prod_popup_lelel_type==3) //for emb wash receive
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b
	where a.id=b.mst_id and a.production_type=3 and a.embel_name=3 and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]] +=$row[csf('production_qnty')];
		}
		$table_width=(300+(count($sizearr_order)*60));
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                    <th width="100" >Color Name</th>
                    <th width="100">Production Type</th>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                        <?
                    }
                    ?>
                    <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="5" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                    <td>Order Qty.</td>
                    <?
					$total_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
				</tr>
				<tr>
					<td>Plan Cut Qty.</td>
					<?
					$total_plan_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0); $total_plan_ord_qnty+=$plan_order_size_qnty[$size_id]; ?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_plan_ord_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Wash Rcvd. Plan Qty.</td>
                    <?
					$total_plan_wash_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$total_cut_reject=$cut_panel_rejection+$chest_printing+$neck_sleeve_printing+$embroidery+$sewing_input;
						$print_enb_plan_cut=$plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100;
						echo number_format($print_enb_plan_cut,0); $total_plan_wash_qnty+=$print_enb_plan_cut;
						?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_plan_wash_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Wash Rcvd. Qty.</td>
                    <?
					$total_prod_wash_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format( $prod_color_size_data[$size_id],0); $total_prod_wash_qnty+=$prod_color_size_data[$size_id]; ?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_prod_wash_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Wash Rcvd. Balance</td>
                    <?
					$taoal_wash_balance=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$print_sent_balence=(($plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100)-$prod_color_size_data[$size_id]);
						 echo number_format($print_sent_balence,0); $taoal_wash_balance+=$print_sent_balence;
						?>
                         </td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($taoal_wash_balance,0); ?></td>
                </tr>
            </tbody>
		</table>
        </div>
    	<?
		
	}
	else if($prod_popup_type==7 && $prod_popup_lelel_type==1) //for iron
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b 
	where a.id=b.mst_id and a.production_type=7 and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]] +=$row[csf('production_qnty')];
		}
		$table_width=(300+(count($sizearr_order)*60));
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                    <th width="100" >Color Name</th>
                    <th width="100">Production Type</th>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                        <?
                    }
                    ?>
                    <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="5" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                    <td>Order Qty.</td>
                    <?
                    $total_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
				</tr>
				<tr>
					<td>Plan Cut Qty.</td>
					<?
					$total_plan_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0); $total_plan_ord_qnty+=$plan_order_size_qnty[$size_id]; ?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_plan_ord_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Iron Plan Qty</td>
                    <?
					$toal_plan_iron_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$total_cut_reject=$cut_panel_rejection+$chest_printing+$neck_sleeve_printing+$embroidery+$sewing_input+$garments_wash;
						$print_enb_plan_cut=$plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100;
						echo number_format($print_enb_plan_cut,0); $toal_plan_iron_qnty+=$print_enb_plan_cut;
						?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($toal_plan_iron_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Iron Qty.</td>
                    <?
					$total_prod_iron_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format( $prod_color_size_data[$size_id],0); $total_prod_iron_qnty+=$prod_color_size_data[$size_id];?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_prod_iron_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Iron Balance</td>
                    <?
					$total_iron_balance=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$print_sent_balence=(($plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100)-$prod_color_size_data[$size_id]);
						 echo number_format($print_sent_balence,0); $total_iron_balance+=$print_sent_balence;
						?>
                         </td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_iron_balance,0); ?></td>
                </tr>
            </tbody>
		</table>
        </div>
    	<?
	}
	else if($prod_popup_type==8 && $prod_popup_lelel_type==1) //for finish
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b 
	where a.id=b.mst_id and a.production_type=8 and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]] +=$row[csf('production_qnty')];
		}
		
		$table_width=(300+(count($sizearr_order)*60));
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                    <th width="100" >Color Name</th>
                    <th width="100">Production Type</th>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                        <?
                    }
                    ?>
                    <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="5" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                    <td>Order Qty.</td>
                    <?
                    $total_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
				</tr>
				<tr>
					<td>Plan Cut Qty.</td>
					<?
					$total_plan_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0); $total_plan_ord_qnty+=$plan_order_size_qnty[$size_id]; ?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_plan_ord_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Packing/Carton Plan Qty.</td>
                    <?
					$total_plan_finish_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$total_cut_reject=$cut_panel_rejection+$chest_printing+$neck_sleeve_printing+$embroidery+$sewing_input+$garments_wash+$gmts_finishing;
						$print_enb_plan_cut=$plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100;
						echo number_format($print_enb_plan_cut,0); $total_plan_finish_qnty+=$print_enb_plan_cut;
						?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_plan_finish_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Packing/Carton Qty.</td>
                    <?
					$total_prod_finish_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format( $prod_color_size_data[$size_id],0); $total_prod_finish_qnty+=$prod_color_size_data[$size_id];?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_prod_finish_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Packing/Carton  Balance</td>
                    <?
					$toal_finash_balance=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$print_sent_balence=(($plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100)-$prod_color_size_data[$size_id]);
						 echo number_format($print_sent_balence,0); $toal_finash_balance+=$print_sent_balence;
						?>
                         </td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($toal_finash_balance,0); ?></td>
                </tr>
            </tbody>
		</table>
        </div>
    	<?
		
	}
	else if($prod_popup_type==10 && $prod_popup_lelel_type==1) //for garments Delivery
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id,
		sum(CASE WHEN a.entry_form!=85 THEN b.production_qnty ELSE 0 END)-sum(CASE WHEN a.entry_form=85 THEN b.production_qnty ELSE 0 END) as production_qnty
	from pro_ex_factory_mst a,  pro_ex_factory_dtls b
	where a.id=b.mst_id and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]] +=$row[csf('production_qnty')];
		}
		
		$table_width=(300+(count($sizearr_order)*60));
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                    <th width="100" >Color Name</th>
                    <th width="100">Production Type</th>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                        <?
                    }
                    ?>
                    <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="3" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                    <td>Order Qty.</td>
                    <?
                    $total_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
                </tr>
                
                <tr>
                    <td>Garments Delivery Qty.</td>
                    <?
					$total_delivery_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($prod_color_size_data[$size_id],0); $total_delivery_qnty+=$prod_color_size_data[$size_id];?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_delivery_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Gmts Deliv. Balance</td>
                    <?
					$total_delivery_balance=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? $delivery_balance=($order_size_qnty[$size_id]-$prod_color_size_data[$size_id]); echo number_format($delivery_balance,0); $total_delivery_balance+=$delivery_balance;?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_delivery_balance,0); ?></td>
                </tr>
            </tbody>
		</table>
    	</div>
    	<?
		
	}
	else if($prod_popup_type==11) //for emb sewing output
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		if($prod_popup_lelel_type=="") $prod_source_cond=''; 
		else 
		{
			$prod_source_cond=$prod_popup_lelel_type;
		}
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(case when a.production_source='$prod_source_cond' then b.production_qnty else 0 end) as poly_qnty
	from pro_garments_production_mst a, pro_garments_production_dtls b 
	where a.id=b.mst_id and a.production_type=11 and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id ");
	/*echo "SELECT b.color_size_break_down_id,
		sum(case when a.production_source='$prod_source_cond' then b.production_qnty else 0 end) as poly_qnty
	from pro_garments_production_mst a, pro_garments_production_dtls b 
	where a.id=b.mst_id and a.production_type=11 and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id "; die;*/
		
		$prod_color_size_data=array();
	
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["poly_qnty"]+=$row[csf('poly_qnty')];
		}
		//print_r($prod_color_size_data);
		$table_width=(200+(count($sizearr_order)*60));
		
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                    <th width="100" >Color Name</th>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                        <?
                    }
                    ?>
                    <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
            	<? foreach($allcolor_id_arr as $inc=>$color_id_val) { ?>
                <tr>
                    <td align="center" valign="middle"><? echo $color_Arr_library[$color_id_val]; ?></td>
                    <?
					$total_prod_poly_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($prod_color_size_data[$color_id_val][$size_id]["poly_qnty"],0); $total_prod_poly_qnty+=$prod_color_size_data[$color_id_val][$size_id]["poly_qnty"]; ?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_prod_poly_qnty,0); ?></td>
                </tr>
                <? } ?>
            </tbody>
		</table>
    	</div>
    	<?
	}
	exit(); 
}

if ($action=="transfer_popup")  // All Production Data popup
{
	
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	
	$po_break_down_id=str_replace("'","",$po_break_down_id);
	$item_id=str_replace("'","",$item_id);
	$country_id=str_replace("'","",$country_id);
	$color_id=str_replace("'","",$color_id);
    $prod_popup_type=str_replace("'","",$prod_popup_type);
	$prod_popup_lelel=str_replace("'","",$prod_popup_lelel);
			
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$lib_color=return_library_array( "select id,color_name from lib_color", "id", "color_name");

	$color_size_sql=sql_select( "SELECT a.id, a.color_number_id, a.size_number_id from wo_po_color_size_breakdown a where   a.color_number_id>0 and a.size_number_id>0 and a.po_break_down_id in (".$po_break_down_id.") order by a.color_order");
	$color_size_data=array(); $allcolor_id_arr=array();
	foreach($color_size_sql as $row)
	{
		$allcolor_id_arr[$row[csf("color_number_id")]]=$row[csf("color_number_id")];
		$color_size_data[$row[csf("id")]]["color_number_id"]=$row[csf("color_number_id")];
	}

 		if($prod_popup_lelel==1)
		{			
			$sql = "SELECT c.to_po_id, c.production_quantity, c.item_number_id ,d.color_number_id,a.job_no,a.style_ref_no,b.po_number
			FROM wo_po_details_master a, wo_po_break_down b, pro_gmts_delivery_dtls c, wo_po_color_size_breakdown d  
			WHERE a.job_no=b.job_no_mst and b.id=c.to_po_id and b.id=d.po_break_down_id and c.to_po_id in( $po_break_down_id) and a.status_active=1 and b.status_active=1 and c.status_active=1
			";
		}
		else
		{
			$sql = "SELECT c.from_po_id, c.production_quantity, c.item_number_id ,d.color_number_id,a.job_no,a.style_ref_no,b.po_number
			FROM wo_po_details_master a, wo_po_break_down b, pro_gmts_delivery_dtls c, wo_po_color_size_breakdown d  
			WHERE a.job_no=b.job_no_mst and b.id=c.from_po_id and b.id=d.po_break_down_id and c.from_po_id in( $po_break_down_id) and a.status_active=1 and b.status_active=1 and c.status_active=1
			";
		}		 
  		$sql_res = sql_select($sql);
  		$data_array = array();
  		foreach ($sql_res as $val) 
  		{
  			$data_array[$val[csf('job_no')]][$val[csf('style_ref_no')]][$val[csf('po_number')]][$val[csf('color_number_id')]]['production_quantity'] += $val[csf('production_quantity')];
  			$data_array[$val[csf('job_no')]][$val[csf('style_ref_no')]][$val[csf('po_number')]][$val[csf('color_number_id')]]['po_number'] += $val[csf('po_number')];
  		}	
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <div id="exc"></div>
        </div>
        
		<? ob_start(); $table_width=480;?>
        <div style="width:<? echo $table_width; ?>px" align="left" id="details_reports">
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>

                	<th width="30" >SL</th>
                    <th width="100" >Job</th>
                    <th width="100" >Style</th>
                    <th width="100" >PO NUmber</th>
                    <th width="100" >Color Name</th>                    
                    <th width="50" >Qnty</th>                    
                </tr>
            </thead>
            <tbody>
            	<? 
            	$sl=1; 
            	$sub_total_prod_poly_qntyy=array(); 
            	$sub_totals=0; 
            	foreach($data_array as $job_no=>$job_no_data)
            	{ 
            		foreach ($job_no_data as $style => $style_data) 
            		{
            			foreach ($style_data as $po_id => $po_data) 
            			{
            				foreach ($po_data as $color_id => $row) 
            				{
            					?>
            					<tr>
            						<td><? echo $i;;?></td>
            						<td><? echo $job_no;?></td>
            						<td><? echo $style;?></td>
            						<td><? echo $row['po_number'];?></td>
            						<td><? echo $lib_color[$color_id];?></td>
            						<td><? echo $row['production_qnty'];?></td>
            					</tr>
            					<?
            				}
            			}
            		}
            		
                } 
                ?>
            </tbody>             
		</table>
    	</div>        
    	</div>
		<?
		
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
		//echo "$total_data####$filename####$reportType";
		$filename=$filename;

		?>
		<script>
            document.getElementById('exc').innerHTML='<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />&nbsp;<a href="<? echo $filename;?>" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>';
		</script>

		<?		
		die;
			
	if($prod_popup_type==1 && $prod_popup_lelel_type==1) //for cutting
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b
	where a.id=b.mst_id and a.production_type=1 and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id  $country_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	group by b.color_size_break_down_id");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]] +=$row[csf('production_qnty')]; 
		}
		$table_width=(300+(count($sizearr_order)*60));
		
		
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
                <thead>
                    <tr>
                        <th width="100" >Color Name</th>
                        <th width="100">Production Type</th>
                        <?
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                            <?
                        }
                        ?>
                        <th >Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td rowspan="4" align="center" valign="middle" ><? echo $color_Arr_library[$color_id]; ?></td>
                        <td >Order Qty.</td>
                        <?
						$total_ord_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Plan Cut Qty.</td>
                        <?
						$total_plan_ord_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0); $total_plan_ord_qnty+=$plan_order_size_qnty[$size_id]; ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_plan_ord_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Cutting Qty.</td>
                        <?
						$total_color_size_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right" ><? echo number_format($prod_color_size_data[$size_id],0); $total_color_size_qnty+=$prod_color_size_data[$size_id];  ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_color_size_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Cutting Balance.</td>
                        <?
						$total_cut_balance=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right" title="Cutting Qty-Plan Cut Qty."><? $cut_balance=($plan_order_size_qnty[$size_id]-$prod_color_size_data[$size_id]); echo number_format($cut_balance,0); $total_cut_balance+=$cut_balance; ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_cut_balance,0); ?></td>
                    </tr>
                </tbody>
            </table>
            <br />
    	</div>
    	<?
		
	}
	else if($prod_popup_type==2 && $prod_popup_lelel_type==1 && $prod_popup_lelel_cat==1) //for emb print inhouse issue
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b
	where a.id=b.mst_id and a.production_type=2 and a.embel_name=1 and a.production_source=1 and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id ");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]] +=$row[csf('production_qnty')];
		}
		$table_width=(300+(count($sizearr_order)*60));
		?>
        <div id="data_panel"  style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
                <thead>
                    <tr>
                        <th width="100" >Color Name</th>
                        <th width="100">Production Type</th>
                        <?
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                            <?
                        }
                        ?>
                        <th width="100" >Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td rowspan="5" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                        <td>Order Qty.</td>
                        <?
						$total_ord_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Plan Cut Qty.</td>
                        <?
						$total_plan_ord_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0); $total_plan_ord_qnty+=$plan_order_size_qnty[$size_id]; ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_plan_ord_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Print Sent Plan Qty.</td>
                        <?
						$total_print_sent_plan_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right">
                            <?
                            $print_sent_plan_cut=$plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$cut_panel_rejection)/100;
                            echo number_format($print_sent_plan_cut,0); $total_print_sent_plan_qnty+=$print_sent_plan_cut;
                            ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_print_sent_plan_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Print Sent Qty.</td>
                        <?
						$total_print_sent_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format( $prod_color_size_data[$size_id],0); $total_print_sent_qnty+=$prod_color_size_data[$size_id]; ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_print_sent_qnty,0); ?></td>
                    </tr>
                    <tr>

                        <td>Print Sent Balance</td>
                        <?
						$total_print_sent_balence=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right">
                            <?
                            $print_sent_balence=(($plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$cut_panel_rejection)/100)-$prod_color_size_data[$size_id]);
                             echo number_format($print_sent_balence,0); $total_print_sent_balence+=$print_sent_balence;
                            ?>
                             </td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_print_sent_balence,0); ?></td>
                    </tr>
                </tbody>
            </table>
    	</div>
    	<?
		
		
	}
	else if($prod_popup_type==2 && $prod_popup_lelel_type==1 && $prod_popup_lelel_cat==2) //for emb print subcontract issue
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b 
	where a.id=b.mst_id and a.production_type=2 and a.embel_name=1 and a.production_source<>1 and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by b.color_size_break_down_id ");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]] +=$row[csf('production_qnty')];
		}
		$table_width=(300+(count($sizearr_order)*60));
		?>
        <div id="data_panel"  style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
                <thead>
                    <tr>
                        <th width="100" >Color Name</th>
                        <th width="100">Production Type</th>
                        <?
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                            <?
                        }
                        ?>
                        <th width="100" >Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td rowspan="5" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                        <td>Order Qty.</td>
                        <?
						$total_ord_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Plan Cut Qty.</td>
                        <?
						$total_plan_ord_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0); $total_plan_ord_qnty+=$plan_order_size_qnty[$size_id]; ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_plan_ord_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Print Sent Plan Qty.</td>
                        <?
						$total_print_sent_plan_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right">
                            <?
                            $print_sent_plan_cut=$plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$cut_panel_rejection)/100;
                            echo number_format($print_sent_plan_cut,0); $total_print_sent_plan_qnty+=$print_sent_plan_cut;
                            ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_print_sent_plan_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Print Sent Qty.</td>
                        <?
						$total_print_sent_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format( $prod_color_size_data[$size_id],0); $total_print_sent_qnty+=$prod_color_size_data[$size_id]; ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_print_sent_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Print Sent Balance</td>
                        <?
						$total_print_sent_balence=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right">
                            <?
                            $print_sent_balence=(($plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$cut_panel_rejection)/100)-$prod_color_size_data[$size_id]);
                             echo number_format($print_sent_balence,0); $total_print_sent_balence+=$print_sent_balence;
                            ?>
                             </td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_print_sent_balence,0); ?></td>
                    </tr>
                </tbody>
            </table>
    	</div>
    	<?
		
		
	}
	else if($prod_popup_type==3 && $prod_popup_lelel_type==1  && $prod_popup_lelel_cat==1) //for emb print inhouse receive
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b 
	where a.id=b.mst_id and a.production_type=3 and a.embel_name=1  and a.production_source=1 and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by b.color_size_break_down_id ");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]] +=$row[csf('production_qnty')];
		}
		$table_width=(300+(count($sizearr_order)*60));
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
                <thead>
                    <tr>
                        <th width="100" >Color Name</th>
                        <th width="100">Production Type</th>
                        <?
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                            <?
                        }
                        ?>
                        <th width="100" >Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td rowspan="5" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                        <td>Order Qty.</td>
                        <?
						$total_ord_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Plan Cut Qty.</td>
                        <?
						$total_plan_ord_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0); $total_plan_ord_qnty+=$plan_order_size_qnty[$size_id]; ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_plan_ord_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Print Rcvd. Plan Qty.</td>
                        <?
						$total_print_sent_plan_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right">
                            <?
                            $total_cut_reject=$cut_panel_rejection+$chest_printing+$neck_sleeve_printing;
                            $print_sent_plan_cut=$plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100;
                            echo number_format($print_sent_plan_cut,0); $total_print_sent_plan_qnty +=$print_sent_plan_cut;
                            ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_print_sent_plan_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Print Received Qty.</td>
                        <?
						$total_prod_print_rcv_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format( $prod_color_size_data[$size_id],0); $total_prod_print_rcv_qnty+= $prod_color_size_data[$size_id]; ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_prod_print_rcv_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Print Rcvd. Balance</td>
                        <?
						$total_print_sent_balance=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right">
                            <?
                            $print_sent_balence=(($plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100)-$prod_color_size_data[$size_id]);
                             echo number_format($print_sent_balence,0); $total_print_sent_balance+=$print_sent_balence;
                            ?>
                             </td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_print_sent_balance,0); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    	<?
	}
	else if($prod_popup_type==3 && $prod_popup_lelel_type==1  && $prod_popup_lelel_cat==2) //for emb print subcontract receive
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b
	where a.id=b.mst_id and a.production_type=3 and a.embel_name=1  and a.production_source<>1 and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id ");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]] +=$row[csf('production_qnty')];
		}
		$table_width=(300+(count($sizearr_order)*60));
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
                <thead>
                    <tr>
                        <th width="100" >Color Name</th>
                        <th width="100">Production Type</th>
                        <?
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                            <?
                        }
                        ?>
                        <th width="100" >Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td rowspan="5" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                        <td>Order Qty.</td>
                        <?
						$total_ord_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Plan Cut Qty.</td>
                        <?
						$total_plan_ord_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0); $total_plan_ord_qnty+=$plan_order_size_qnty[$size_id]; ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_plan_ord_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Print Rcvd. Plan Qty.</td>
                        <?
						$total_print_sent_plan_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right">
                            <?
                            $total_cut_reject=$cut_panel_rejection+$chest_printing+$neck_sleeve_printing;
                            $print_sent_plan_cut=$plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100;
                            echo number_format($print_sent_plan_cut,0); $total_print_sent_plan_qnty +=$print_sent_plan_cut;
                            ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_print_sent_plan_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Print Received Qty.</td>
                        <?
						$total_prod_print_rcv_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format( $prod_color_size_data[$size_id],0); $total_prod_print_rcv_qnty+= $prod_color_size_data[$size_id]; ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_prod_print_rcv_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Print Rcvd. Balance</td>
                        <?
						$total_print_sent_balance=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right">
                            <?
                            $print_sent_balence=(($plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100)-$prod_color_size_data[$size_id]);
                             echo number_format($print_sent_balence,0); $total_print_sent_balance+=$print_sent_balence;
                            ?>
                             </td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_print_sent_balance,0); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    	<?
	}
	else if($prod_popup_type==2 && $prod_popup_lelel_type==2  && $prod_popup_lelel_cat==1) //for emb ebroidery inhouse issue
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b 
	where a.id=b.mst_id and a.production_type=2 and a.embel_name=2 and a.production_source=1  and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by b.color_size_break_down_id ");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]] +=$row[csf('production_qnty')];
		}
		$table_width=(300+(count($sizearr_order)*60));
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                <th width="100" >Color Name</th>
                <th width="100">Production Type</th>
                <?
                foreach($sizearr_order as $size_id)
                {
					?>
					<th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
					<?
                }
                ?>
                <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="5" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                    <td>Order Qty.</td>
                    <?
                    $total_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
                    
                </tr>
                <tr>
                    <td>Plan Cut Qty.</td>
                    <?
                    $total_plan_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0); $total_plan_ord_qnty+=$plan_order_size_qnty[$size_id]; ?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_plan_ord_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Emb. Sent Plan Qty</td>
                    <?
					$total_plan_sent_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$total_cut_reject=$cut_panel_rejection+$chest_printing+$neck_sleeve_printing;
						$print_emb_plan_cut=$plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100;
						echo number_format($print_emb_plan_cut,0); $total_plan_sent_qnty+=$print_emb_plan_cut;
						?>
                        </td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_plan_sent_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Print Received Qty.</td>
                    <?
					$total_prod_print_rcv_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format( $prod_color_size_data[$size_id],0); $total_prod_print_rcv_qnty+=$prod_color_size_data[$size_id];?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_prod_print_rcv_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Print Rcvd. Balance</td>
                    <?
					$total_print_rcv_balance=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$print_sent_balence=(($plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100)-$prod_color_size_data[$size_id]);
						 echo number_format($print_sent_balence,0); $total_print_rcv_balance+=$print_sent_balence;
						?>
                         </td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_print_rcv_balance,0); ?></td>
                </tr>
            </tbody>
		</table>
        </div>
    	<?
	}
	else if($prod_popup_type==2 && $prod_popup_lelel_type==2  && $prod_popup_lelel_cat==2) //for emb ebroidery subcontract issue
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b
	where a.id=b.mst_id and a.production_type=2 and a.embel_name=2 and a.production_source<>1  and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by b.color_size_break_down_id");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]] +=$row[csf('production_qnty')];
		}
		$table_width=(300+(count($sizearr_order)*60));
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                <th width="100" >Color Name</th>
                <th width="100">Production Type</th>
                <?
                foreach($sizearr_order as $size_id)
                {
					?>
					<th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
					<?
                }
                ?>
                <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="5" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                    <td>Order Qty.</td>
                    <?
                    $total_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
                    
                </tr>
                <tr>
                    <td>Plan Cut Qty.</td>
                    <?
                    $total_plan_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0); $total_plan_ord_qnty+=$plan_order_size_qnty[$size_id]; ?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_plan_ord_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Emb. Sent Plan Qty</td>
                    <?
					$total_plan_sent_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$total_cut_reject=$cut_panel_rejection+$chest_printing+$neck_sleeve_printing;
						$print_emb_plan_cut=$plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100;
						echo number_format($print_emb_plan_cut,0); $total_plan_sent_qnty+=$print_emb_plan_cut;
						?>
                        </td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_plan_sent_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Print Received Qty.</td>
                    <?
					$total_prod_print_rcv_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format( $prod_color_size_data[$size_id],0); $total_prod_print_rcv_qnty+=$prod_color_size_data[$size_id];?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_prod_print_rcv_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Print Rcvd. Balance</td>
                    <?
					$total_print_rcv_balance=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$print_sent_balence=(($plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100)-$prod_color_size_data[$size_id]);
						 echo number_format($print_sent_balence,0); $total_print_rcv_balance+=$print_sent_balence;
						?>
                         </td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_print_rcv_balance,0); ?></td>
                </tr>
            </tbody>
		</table>
        </div>
    	<?
	}
	else if($prod_popup_type==3 && $prod_popup_lelel_type==2 && $prod_popup_lelel_cat==1) //for emb ebroidery inhouse receive
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b
	where a.id=b.mst_id and a.production_type=3 and a.embel_name=2 and a.production_source=1  and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]] +=$row[csf('production_qnty')];
		}
		$table_width=(300+(count($sizearr_order)*60));
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
                <thead>
                    <tr>
                    <th width="100" >Color Name</th>
                    <th width="100">Production Type</th>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                        <?
                    }
                    ?>
                    <th width="100" >Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td rowspan="5" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                        <td>Order Qty.</td>
                        <?
                        $total_ord_qnty=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
							<?
						}
						?>
						<td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Plan Cut Qty.</td>
                        <?
						$total_plan_ord_qnty=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0); $total_plan_ord_qnty+=$plan_order_size_qnty[$size_id]; ?></td> 
							<?
						}
						?>
						<td align="right"><? echo number_format($total_plan_ord_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Emb. Rcvd. Plan Qty.</td>
                        <?
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right">
                            <?
							$total_rcv_plan_qnty=0;
                            $total_cut_reject=$cut_panel_rejection+$chest_printing+$neck_sleeve_printing+$embroidery;
                            $print_enb_plan_cut=$plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100;
                            echo number_format($print_enb_plan_cut,0); $total_rcv_plan_qnty+=$print_enb_plan_cut;
                            ?>
                            </td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_rcv_plan_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Emb. Rcvd. Qty.</td>
                        <?
						$total_pord_recv_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format( $prod_color_size_data[$size_id],0); $total_pord_recv_qnty+=$prod_color_size_data[$size_id]; ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_pord_recv_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Emb. Rcvd. Balance</td>
                        <?
						$total_emb_balect=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right">
                            <?
                            $print_sent_balence=(($plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100)-$prod_color_size_data[$size_id]);
                             echo number_format($print_sent_balence,0); $total_emb_balect+=$print_sent_balence;
                            ?>
                             </td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_emb_balect,0); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    	<?
	}
	else if($prod_popup_type==3 && $prod_popup_lelel_type==2 && $prod_popup_lelel_cat==2) //for emb ebroidery subcon receive
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b 
	where a.id=b.mst_id and a.production_type=3 and a.embel_name=2 and a.production_source<>1  and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id ");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]] =$row[csf('production_qnty')];
		}
		$table_width=(300+(count($sizearr_order)*60));
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
                <thead>
                    <tr>
                    <th width="100" >Color Name</th>
                    <th width="100">Production Type</th>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                        <?
                    }
                    ?>
                    <th width="100" >Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td rowspan="5" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                        <td>Order Qty.</td>
                        <?
                        $total_ord_qnty=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
							<?
						}
						?>
						<td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Plan Cut Qty.</td>
                        <?
						$total_plan_ord_qnty=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0); $total_plan_ord_qnty+=$plan_order_size_qnty[$size_id]; ?></td> 
							<?
						}
						?>
						<td align="right"><? echo number_format($total_plan_ord_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Emb. Rcvd. Plan Qty.</td>
                        <?
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right">
                            <?
							$total_rcv_plan_qnty=0;
                            $total_cut_reject=$cut_panel_rejection+$chest_printing+$neck_sleeve_printing+$embroidery;
                            $print_enb_plan_cut=$plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100;
                            echo number_format($print_enb_plan_cut,0); $total_rcv_plan_qnty+=$print_enb_plan_cut;
                            ?>
                            </td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_rcv_plan_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Emb. Rcvd. Qty.</td>
                        <?
						$total_pord_recv_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format( $prod_color_size_data[$size_id],0); $total_pord_recv_qnty+=$prod_color_size_data[$size_id]; ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_pord_recv_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Emb. Rcvd. Balance</td>
                        <?
						$total_emb_balect=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right">
                            <?
                            $print_sent_balence=(($plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100)-$prod_color_size_data[$size_id]);
                             echo number_format($print_sent_balence,0); $total_emb_balect+=$print_sent_balence;
                            ?>
                             </td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_emb_balect,0); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    	<?
	}
	else if($prod_popup_type==2 && $prod_popup_lelel_type==4) //for emb sp issue
	{
		/*$sql_color_size= sql_select("SELECT c.size_number_id, sum(b.production_qnty) as production_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
	where a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.production_type=2 and a.embel_name=4 and c.po_break_down_id=$po_break_down_id and c.item_number_id=$item_id and c.country_id=$country_id and c.color_number_id=$color_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
	group by c.size_number_id
	order by c.size_number_id");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$row[csf('size_number_id')]] =$row[csf('production_qnty')];
		}
		$table_width=(200+count($table_width)*60);
		?>
		<div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                <th width="100" >Color Name</th>
                <th width="100">Production Type</th>
                <?
                foreach($sizearr_order as $size_id)
                {
					?>
					<th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
					<?
                }
                ?>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="5" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                    <td>Order Qty.</td>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
						?>
						<td align="right"><? echo number_format($order_size_qnty[$size_id],0);?></td> 
						<?
                    }
                    ?>
                </tr>
                <tr>
                    <td>Plan Cut Qty.</td>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0);?></td> 
                        <?
                    }
                    ?>
                </tr>
                <tr>
                    <td>Emb. Rcvd. Plan Qty.</td>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$total_cut_reject=$cut_panel_rejection+$chest_printing+$neck_sleeve_printing+$embroidery;
						$print_enb_plan_cut=$plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100;
						echo number_format($print_enb_plan_cut,0);
						?></td> 
                        <?
                    }
                    ?>
                </tr>
                <tr>
                    <td>Emb. Rcvd. Qty.</td>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format( $prod_color_size_data[$size_id],0);?></td> 
                        <?
                    }
                    ?>
                </tr>
                <tr>
                    <td>Emb. Rcvd. Balance</td>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$print_sent_balence=($prod_color_size_data[$size_id]-($plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100));
						 echo number_format($print_sent_balence,0);
						?>
                         </td> 
                        <?
                    }
                    ?>
                </tr>
            </tbody>
		</table>
		</div>
    	<?*/
	}
	else if($prod_popup_type==3 && $prod_popup_lelel_type==4) //for emb sp receive
	{
		/*$sql_color_size= sql_select("SELECT c.size_number_id, sum(b.production_qnty) as production_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
	where a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.production_type=3 and a.embel_name=4 and c.po_break_down_id=$po_break_down_id and c.item_number_id=$item_id and c.country_id=$country_id and c.color_number_id=$color_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
	group by c.size_number_id
	order by c.size_number_id");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$row[csf('size_number_id')]] =$row[csf('production_qnty')];
		}*/
	}
	else if($prod_popup_type==4 && $prod_popup_lelel_type==1) //for sewing input
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id,
		sum(case when a.production_source=1 then b.production_qnty else 0 end) as production_inhouse_qnty,
		sum(case when a.production_source=3 then b.production_qnty else 0 end) as production_subcon_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b
	where a.id=b.mst_id and a.production_type=4 and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id ");
	
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["production_inhouse_qnty"]+=$row[csf('production_inhouse_qnty')];
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["production_subcon_qnty"]+=$row[csf('production_subcon_qnty')];
		}
		
		$table_width=(300+(count($sizearr_order)*60));
		
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                <th width="100" >Color Name</th>
                <th width="100">Production Type</th>
                <?
                foreach($sizearr_order as $size_id)
                {
					?>
					<th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
					<?
                }
                ?>
                <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="6" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                    <td>Order Qty.</td>
                    <?
					$total_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
				</tr>
				<tr>
					<td>Plan Cut Qty.</td>
					<?
					$total_plan_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0); $total_plan_ord_qnty+=$plan_order_size_qnty[$size_id]; ?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_plan_ord_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Sewing Input Plan Qty.</td>
                    <?
					$sewing_in_plan_cut=$total_sew_plan_qnty=0;
					
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$total_cut_reject=($cut_panel_rejection+$chest_printing+$neck_sleeve_printing+$embroidery);
						$sewing_in_plan_cut=($plan_order_size_qnty[$size_id]-(($plan_order_size_qnty[$size_id]*$total_cut_reject)/100)); 
						echo number_format($sewing_in_plan_cut,0); $total_sew_plan_qnty+=$sewing_in_plan_cut;
						?>
                        </td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_sew_plan_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Inhouse Sewing Input Qty.</td>
                    <?
					$total_prod_sew_inhouse_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($prod_color_size_data[$size_id]["production_inhouse_qnty"],0); $total_prod_sew_inhouse_qnty+=$prod_color_size_data[$size_id]["production_inhouse_qnty"]; ?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_prod_sew_inhouse_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Sub Con. Sewing Input Qty.</td>
                    <?
					$total_production_subcon_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($prod_color_size_data[$size_id]["production_subcon_qnty"],0); $total_production_subcon_qnty+=$prod_color_size_data[$size_id]["production_subcon_qnty"];?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_production_subcon_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Sewing Input Balance</td>
                    <?
					$total_prod_in_balance=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<? 
						$total_in_balance= ($plan_order_size_qnty[$size_id]-(($plan_order_size_qnty[$size_id]*$total_cut_reject)/100))-($prod_color_size_data[$size_id]["production_subcon_qnty"]+$prod_color_size_data[$size_id]["production_inhouse_qnty"]);
						echo number_format($total_in_balance,0); $total_prod_in_balance+=$total_in_balance;
						?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_prod_in_balance,0); ?></td>
                </tr>
            </tbody>
		</table>
    	</div>
    	<?
		
	}
	else if($prod_popup_type==4 && $prod_popup_lelel_type==11) //for sewing input inhouse
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(case when a.production_source=1 then b.production_qnty else 0 end) as production_inhouse_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b
	where a.id=b.mst_id and a.production_type=4 and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by b.color_size_break_down_id ");
	
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["production_inhouse_qnty"]+=$row[csf('production_inhouse_qnty')];
		}
		
		$table_width=(200+(count($sizearr_order)*60));
		
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                <th width="100" >Color Name</th>
                <?
                foreach($sizearr_order as $size_id)
                {
					?>
					<th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
					<?
                }
                ?>
                <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                    <?
					$total_prod_sew_inhouse_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($prod_color_size_data[$size_id]["production_inhouse_qnty"],0); $total_prod_sew_inhouse_qnty+=$prod_color_size_data[$size_id]["production_inhouse_qnty"]; ?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_prod_sew_inhouse_qnty,0); ?></td>
                </tr>
            </tbody>
		</table>
    	</div>
    	<?
		
	}
	else if($prod_popup_type==4 && $prod_popup_lelel_type==13) //for sewing input subcon
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(case when a.production_source=3 then b.production_qnty else 0 end) as production_subcon_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b
	where a.id=b.mst_id and a.production_type=4 and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id");
	
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["production_subcon_qnty"]+=$row[csf('production_subcon_qnty')];
		}
		
		$table_width=(200+(count($sizearr_order)*60));
		
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                <th width="100" >Color Name</th>
                <?
                foreach($sizearr_order as $size_id)
                {
					?>
					<th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
					<?
                }
                ?>
                <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                    <?
					$total_production_subcon_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($prod_color_size_data[$size_id]["production_subcon_qnty"],0); $total_production_subcon_qnty+=$prod_color_size_data[$size_id]["production_subcon_qnty"];?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_production_subcon_qnty,0); ?></td>
                </tr>
            </tbody>
		</table>
    	</div>
    	<?
		
	}
	else if($prod_popup_type==5 && $prod_popup_lelel_type==1) //for emb sewing output
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id,
		sum(case when a.production_source=1 then b.production_qnty else 0 end) as production_inhouse_qnty,
		sum(case when a.production_source=3 then b.production_qnty else 0 end) as production_subcon_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b 
	where a.id=b.mst_id and a.production_type=5 and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id");
		
		$prod_color_size_data=array();
	
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["production_inhouse_qnty"]+=$row[csf('production_inhouse_qnty')];
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["production_subcon_qnty"]+=$row[csf('production_subcon_qnty')];
		}
		
		$table_width=(300+(count($sizearr_order)*60));
		
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                    <th width="100" >Color Name</th>
                    <th width="100">Production Type</th>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                        <?
                    }
                    ?>
                    <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="6" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                    <td>Order Qty.</td>
                    <?
					$total_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
				</tr>
				<tr>
					<td>Plan Cut Qty.</td>
					<?
					$total_plan_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0); $total_plan_ord_qnty+=$plan_order_size_qnty[$size_id]; ?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_plan_ord_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Sewing Plan Output Qty.</td>
                    <?
					$sewing_in_plan_cut=$total_sew_out_plan_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$total_cut_reject=($cut_panel_rejection+$chest_printing+$neck_sleeve_printing+$embroidery+$sewing_input);  
						$sewing_out_plan_cut=($plan_order_size_qnty[$size_id]-(($plan_order_size_qnty[$size_id]*$total_cut_reject)/100)); 
						echo number_format($sewing_out_plan_cut,0); $total_sew_out_plan_qnty+=$sewing_out_plan_cut;
						?>
                        </td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_sew_out_plan_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Inhouse Sewing Output Qty.</td>
                    <?
					$total_prod_sew_in_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($prod_color_size_data[$size_id]["production_inhouse_qnty"],0); $total_prod_sew_in_qnty+=$prod_color_size_data[$size_id]["production_inhouse_qnty"]; ?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_prod_sew_in_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Sub Con. Sewing Output Qty.</td>
                    <?
					$total_prod_sew_out_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($prod_color_size_data[$size_id]["production_subcon_qnty"],0); $total_prod_sew_out_qnty+=$prod_color_size_data[$size_id]["production_subcon_qnty"]; ?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_prod_sew_out_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Sewing Output Balance</td>
                    <?
					$total_out_balance_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$total_out_balance= ($plan_order_size_qnty[$size_id]-(($plan_order_size_qnty[$size_id]*$total_cut_reject)/100))-($prod_color_size_data[$size_id]["production_subcon_qnty"]+$prod_color_size_data[$size_id]["production_inhouse_qnty"]);
						echo number_format($total_out_balance,0); $total_out_balance_qnty+=$total_out_balance;
						?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_out_balance_qnty,0); ?></td>
                </tr>
            </tbody>
		</table>
    	</div>
    	<?
	}
	else if($prod_popup_type==5 && $prod_popup_lelel_type==11) //for emb sewing output Inhouse
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id,
		sum(case when a.production_source=1 then b.production_qnty else 0 end) as production_inhouse_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b 
	where a.id=b.mst_id and a.production_type=5  and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id");
		
		$prod_color_size_data=array();
	
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["production_inhouse_qnty"]+=$row[csf('production_inhouse_qnty')];
		}
		
		$table_width=(200+(count($sizearr_order)*60));
		
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                    <th width="100" >Color Name</th>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                        <?
                    }
                    ?>
                    <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                    <?
					$total_prod_sew_in_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($prod_color_size_data[$size_id]["production_inhouse_qnty"],0); $total_prod_sew_in_qnty+=$prod_color_size_data[$size_id]["production_inhouse_qnty"]; ?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_prod_sew_in_qnty,0); ?></td>
                </tr>
            </tbody>
		</table>
    	</div>
    	<?
	}
	else if($prod_popup_type==5 && $prod_popup_lelel_type==13) //for emb sewing output
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id,
		sum(case when a.production_source=3 then b.production_qnty else 0 end) as production_subcon_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b 
	where a.id=b.mst_id and a.production_type=5  and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by b.color_size_break_down_id ");
		
		$prod_color_size_data=array();
	
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["production_subcon_qnty"]+=$row[csf('production_subcon_qnty')];
		}
		
		$table_width=(200+(count($sizearr_order)*60));
		
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                    <th width="100" >Color Name</th>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                        <?
                    }
                    ?>
                    <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                    <?
					$total_prod_sew_out_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($prod_color_size_data[$size_id]["production_subcon_qnty"],0); $total_prod_sew_out_qnty+=$prod_color_size_data[$size_id]["production_subcon_qnty"]; ?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_prod_sew_out_qnty,0); ?></td>
                </tr>
            </tbody>
		</table>
    	</div>
    	<?
	}
	else if($prod_popup_type==2 && $prod_popup_lelel_type==3) //for emb wash issue
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b
	where a.id=b.mst_id and a.production_type=2 and a.embel_name=3 and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id ");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]] +=$row[csf('production_qnty')];
		}
		$table_width=(300+(count($sizearr_order)*60));
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                    <th width="100" >Color Name</th>
                    <th width="100">Production Type</th>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                        <?
                    }
                    ?>
                    <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="5" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                    <td>Order Qty.</td>
                    <?
					$total_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
				</tr>
				<tr>
					<td>Plan Cut Qty.</td>
					<?
					$total_plan_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0); $total_plan_ord_qnty+=$plan_order_size_qnty[$size_id]; ?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_plan_ord_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Wash Sent Plan Qty</td>
                    <?
					$total_emp_plan_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$total_cut_reject=$cut_panel_rejection+$chest_printing+$neck_sleeve_printing+$embroidery+$sewing_input;
						$print_enb_plan_cut=$plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100;
						echo number_format($print_enb_plan_cut,0); $total_emp_plan_qnty+=$print_enb_plan_cut;
						?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_emp_plan_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Wash Sent Qty.</td>
                    <?
					$total_prod_sent_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format( $prod_color_size_data[$size_id],0); $total_prod_sent_qnty+=$prod_color_size_data[$size_id];?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_prod_sent_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Wash Sent  Balance</td>
                    <?
					$total_wash_balance=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$print_sent_balence=(($plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100)-$prod_color_size_data[$size_id]);
						 echo number_format($print_sent_balence,0); $total_wash_balance+=$print_sent_balence;
						?>
                         </td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_wash_balance,0); ?></td>
                </tr>
            </tbody>
		</table>
        </div>
    	<?
		
	}
	else if($prod_popup_type==3 && $prod_popup_lelel_type==3) //for emb wash receive
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b
	where a.id=b.mst_id and a.production_type=3 and a.embel_name=3 and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]] +=$row[csf('production_qnty')];
		}
		$table_width=(300+(count($sizearr_order)*60));
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                    <th width="100" >Color Name</th>
                    <th width="100">Production Type</th>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                        <?
                    }
                    ?>
                    <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="5" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                    <td>Order Qty.</td>
                    <?
					$total_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
				</tr>
				<tr>
					<td>Plan Cut Qty.</td>
					<?
					$total_plan_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0); $total_plan_ord_qnty+=$plan_order_size_qnty[$size_id]; ?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_plan_ord_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Wash Rcvd. Plan Qty.</td>
                    <?
					$total_plan_wash_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$total_cut_reject=$cut_panel_rejection+$chest_printing+$neck_sleeve_printing+$embroidery+$sewing_input;
						$print_enb_plan_cut=$plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100;
						echo number_format($print_enb_plan_cut,0); $total_plan_wash_qnty+=$print_enb_plan_cut;
						?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_plan_wash_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Wash Rcvd. Qty.</td>
                    <?
					$total_prod_wash_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format( $prod_color_size_data[$size_id],0); $total_prod_wash_qnty+=$prod_color_size_data[$size_id]; ?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_prod_wash_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Wash Rcvd. Balance</td>
                    <?
					$taoal_wash_balance=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$print_sent_balence=(($plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100)-$prod_color_size_data[$size_id]);
						 echo number_format($print_sent_balence,0); $taoal_wash_balance+=$print_sent_balence;
						?>
                         </td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($taoal_wash_balance,0); ?></td>
                </tr>
            </tbody>
		</table>
        </div>
    	<?
		
	}
	else if($prod_popup_type==7 && $prod_popup_lelel_type==1) //for iron
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b 
	where a.id=b.mst_id and a.production_type=7 and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]] +=$row[csf('production_qnty')];
		}
		$table_width=(300+(count($sizearr_order)*60));
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                    <th width="100" >Color Name</th>
                    <th width="100">Production Type</th>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                        <?
                    }
                    ?>
                    <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="5" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                    <td>Order Qty.</td>
                    <?
                    $total_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
				</tr>
				<tr>
					<td>Plan Cut Qty.</td>
					<?
					$total_plan_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0); $total_plan_ord_qnty+=$plan_order_size_qnty[$size_id]; ?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_plan_ord_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Iron Plan Qty</td>
                    <?
					$toal_plan_iron_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$total_cut_reject=$cut_panel_rejection+$chest_printing+$neck_sleeve_printing+$embroidery+$sewing_input+$garments_wash;
						$print_enb_plan_cut=$plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100;
						echo number_format($print_enb_plan_cut,0); $toal_plan_iron_qnty+=$print_enb_plan_cut;
						?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($toal_plan_iron_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Iron Qty.</td>
                    <?
					$total_prod_iron_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format( $prod_color_size_data[$size_id],0); $total_prod_iron_qnty+=$prod_color_size_data[$size_id];?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_prod_iron_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Iron Balance</td>
                    <?
					$total_iron_balance=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$print_sent_balence=(($plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100)-$prod_color_size_data[$size_id]);
						 echo number_format($print_sent_balence,0); $total_iron_balance+=$print_sent_balence;
						?>
                         </td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_iron_balance,0); ?></td>
                </tr>
            </tbody>
		</table>
        </div>
    	<?
	}
	else if($prod_popup_type==8 && $prod_popup_lelel_type==1) //for finish
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b 
	where a.id=b.mst_id and a.production_type=8 and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]] +=$row[csf('production_qnty')];
		}
		
		$table_width=(300+(count($sizearr_order)*60));
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                    <th width="100" >Color Name</th>
                    <th width="100">Production Type</th>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                        <?
                    }
                    ?>
                    <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="5" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                    <td>Order Qty.</td>
                    <?
                    $total_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
				</tr>
				<tr>
					<td>Plan Cut Qty.</td>
					<?
					$total_plan_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0); $total_plan_ord_qnty+=$plan_order_size_qnty[$size_id]; ?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_plan_ord_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Packing/Carton Plan Qty.</td>
                    <?
					$total_plan_finish_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$total_cut_reject=$cut_panel_rejection+$chest_printing+$neck_sleeve_printing+$embroidery+$sewing_input+$garments_wash+$gmts_finishing;
						$print_enb_plan_cut=$plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100;
						echo number_format($print_enb_plan_cut,0); $total_plan_finish_qnty+=$print_enb_plan_cut;
						?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_plan_finish_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Packing/Carton Qty.</td>
                    <?
					$total_prod_finish_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format( $prod_color_size_data[$size_id],0); $total_prod_finish_qnty+=$prod_color_size_data[$size_id];?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_prod_finish_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Packing/Carton  Balance</td>
                    <?
					$toal_finash_balance=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$print_sent_balence=(($plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100)-$prod_color_size_data[$size_id]);
						 echo number_format($print_sent_balence,0); $toal_finash_balance+=$print_sent_balence;
						?>
                         </td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($toal_finash_balance,0); ?></td>
                </tr>
            </tbody>
		</table>
        </div>
    	<?
		
	}
	else if($prod_popup_type==10 && $prod_popup_lelel_type==1) //for garments Delivery
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id,
		sum(CASE WHEN a.entry_form!=85 THEN b.production_qnty ELSE 0 END)-sum(CASE WHEN a.entry_form=85 THEN b.production_qnty ELSE 0 END) as production_qnty
	from pro_ex_factory_mst a,  pro_ex_factory_dtls b
	where a.id=b.mst_id and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]] +=$row[csf('production_qnty')];
		}
		
		$table_width=(300+(count($sizearr_order)*60));
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                    <th width="100" >Color Name</th>
                    <th width="100">Production Type</th>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                        <?
                    }
                    ?>
                    <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="3" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                    <td>Order Qty.</td>
                    <?
                    $total_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
                </tr>
                
                <tr>
                    <td>Garments Delivery Qty.</td>
                    <?
					$total_delivery_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($prod_color_size_data[$size_id],0); $total_delivery_qnty+=$prod_color_size_data[$size_id];?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_delivery_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Gmts Deliv. Balance</td>
                    <?
					$total_delivery_balance=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? $delivery_balance=($order_size_qnty[$size_id]-$prod_color_size_data[$size_id]); echo number_format($delivery_balance,0); $total_delivery_balance+=$delivery_balance;?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_delivery_balance,0); ?></td>
                </tr>
            </tbody>
		</table>
    	</div>
    	<?
		
	}
	else if($prod_popup_type==11) //for emb sewing output
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		if($prod_popup_lelel_type=="") $prod_source_cond=''; 
		else 
		{
			$prod_source_cond=$prod_popup_lelel_type;
		}
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(case when a.production_source='$prod_source_cond' then b.production_qnty else 0 end) as poly_qnty
	from pro_garments_production_mst a, pro_garments_production_dtls b 
	where a.id=b.mst_id and a.production_type=11 and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id ");
	/*echo "SELECT b.color_size_break_down_id,
		sum(case when a.production_source='$prod_source_cond' then b.production_qnty else 0 end) as poly_qnty
	from pro_garments_production_mst a, pro_garments_production_dtls b 
	where a.id=b.mst_id and a.production_type=11 and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id "; die;*/
		
		$prod_color_size_data=array();
	
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["poly_qnty"]+=$row[csf('poly_qnty')];
		}
		//print_r($prod_color_size_data);
		$table_width=(200+(count($sizearr_order)*60));
		
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                    <th width="100" >Color Name</th>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                        <?
                    }
                    ?>
                    <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
            	<? foreach($allcolor_id_arr as $inc=>$color_id_val) { ?>
                <tr>
                    <td align="center" valign="middle"><? echo $color_Arr_library[$color_id_val]; ?></td>
                    <?
					$total_prod_poly_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($prod_color_size_data[$color_id_val][$size_id]["poly_qnty"],0); $total_prod_poly_qnty+=$prod_color_size_data[$color_id_val][$size_id]["poly_qnty"]; ?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_prod_poly_qnty,0); ?></td>
                </tr>
                <? } ?>
            </tbody>
		</table>
    	</div>
    	<?
	}
	exit(); 
}



if ($action=='exfactory')  // exfactory date popup
{
	 
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	
 	?>
    <fieldset>
    <div style="margin-left:60px">
        <table width="500" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" >
            <thead>
                    <tr>
                        <th width="50">Sl.</th>    
                        <th width="200">Ex Factory Date</th>
                        <th width="">Ex Factory Qnty</th>
               		</tr>
            </thead>
        </table>
        <div style="max-height:425px; overflow-y:scroll; width:518px;" id="scroll_body">
            <table cellspacing="0" border="1" class="rpt_table"  width="500" rules="all" id="table_body" >
            <?
             $total_quantity=0;
             $sql=sql_select("select sum(ex_factory_qnty) as ex_factory_qnty, ex_factory_date from pro_ex_factory_mst where po_break_down_id in ($po_break_down_id) and item_number_id='$item_id' and status_active=1 and is_deleted=0 group by ex_factory_date"); 
            //echo $sql; 
			$i=1;
            foreach($sql as $resultRow)
            {
                 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
             	?>
                 <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                    <td width="50"><? echo $i;?></td>
                    <td width="200" align="right"><? if($resultRow[csf("ex_factory_date")]!="0000-00-00") echo change_date_format($resultRow[csf("ex_factory_date")]); ?></td>
                    <td width="" align="right"><? echo number_format($resultRow[csf("ex_factory_qnty")]); ?></td>
                 </tr>	
                 <?		
                    $total_quantity+=$resultRow[csf("ex_factory_qnty")];
            	 	$i++;
            
        }//end foreach 1st
        ?>
        </table>
        <table cellspacing="0" border="1" class="tbl_bottom"  width="500" rules="all" id="body_bottom" >
                 <tr> 
                    <td width="50">&nbsp;</td> 
                    <td width="200">Total</td> 
                    <td width=""><? echo number_format($total_quantity); ?></td>
                  </tr>
         </table>
       </div>
     </div>
     </fieldset>
    <?
 	
 exit();
 
}

if($action=='date_wise_production_report') 
{	
	extract($_REQUEST); 
 	echo load_html_head_contents("Remarks", "../../../", 1, 1,$unicode,'',''); 
	?>
    <div align="center">
        <fieldset style="width:480px">
        <legend>Cutting</legend>
            <? 
                 
                 $sql= "SELECT id,production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type='1' and is_deleted=0 and status_active=1";
                 //echo $sql;
                 echo  create_list_view ( "list_view_1", "Date,Production Qnty,Remarks", "100,120,280","470","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "", '','3,1,0','0,0,production_quantity,0');
                
            ?>
        </fieldset>
        
        <fieldset style="width:480px">
        <legend>Print/Embr Issue</legend>
            <? 
                 
                  $sql= "SELECT production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type='2' and is_deleted=0 and status_active=1";
                  
                 echo  create_list_view ( "list_view_2", "Date,Production Qnty,Remarks", "100,120,280","470","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "", '','3,1,0','0,0,production_quantity,0');
            ?>
        </fieldset>
        
        <fieldset style="width:480px">
        <legend>Print/Embr Receive</legend>
            <? 
                 
                  $sql= "SELECT production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type='3' and is_deleted=0 and status_active=1";
                  
                 echo  create_list_view ( "list_view_3", "Date,Production Qnty,Remarks", "100,120,280","470","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "", '','3,1,0','0,0,production_quantity,0');
            ?>
        </fieldset>
        
        
        <fieldset style="width:480px">
        <legend>Sewing Input</legend>
            <? 
                 
                  $sql= "SELECT production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type='4' and is_deleted=0 and status_active=1";
                  
                 echo  create_list_view ( "list_view_4", "Date,Production Qnty,Remarks", "100,120,280","470","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "", '','3,1,0','0,0,production_quantity,0');
            ?>
        </fieldset>
        
        
        <fieldset style="width:480px">
        <legend >Sewing Output</legend>
            <? 
                 
                  $sql= "SELECT production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type='5' and is_deleted=0 and status_active=1";
                  
                 echo  create_list_view ( "list_view_5", "Date,Production Qnty,Remarks", "100,120,280","470","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "", '','3,1,0','0,0,production_quantity,0');
            ?>
        </fieldset>
        
        
        <fieldset style="width:480px">
        <legend>Finish Input</legend>
            <? 
                 
                  $sql= "SELECT production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type='6' and is_deleted=0 and status_active=1";
                  
                 echo  create_list_view ( "list_view_6", "Date,Production Qnty,Remarks", "100,120,280","470","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "", '','3,1,0','0,0,production_quantity,0');
            ?>
        </fieldset>
        
        <fieldset style="width:480px">
        <legend>Finish Output</legend>
            <? 
                 
                  $sql= "SELECT production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type='8' and is_deleted=0 and status_active=1";
                 
                  echo  create_list_view ( "list_view_7", "Date,Production Qnty,Remarks", "100,120,280","470","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "", '','3,1,0','0,0,production_quantity,0');
            ?>
        </fieldset>
	</div>  
	<?
	exit();
}//end if

if($action=="product_all_remarks")
{
	extract($_REQUEST); 
 	echo load_html_head_contents("Remarks", "../../../", 1, 1,$unicode,'',''); 
	$po_break_down_id=str_replace("'","",$po_break_down_id);
	$item_id=str_replace("'","",$item_id);
	$country_id=str_replace("'","",$country_id);
	$color_id=str_replace("'","",$color_id);
	$floor_library=return_library_array( "select id,floor_name from  lib_prod_floor", "id", "floor_name"  );
	$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
	$actual_resource_library=return_library_array( "select id,line_number from prod_resource_mst", "id", "line_number"  );
	$job_sql = sql_select("SELECT a.company_name,a.style_ref_no,a.buyer_name,b.po_number from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($po_break_down_id)");
	?>
    <div id="data_panel" style="width:100%;text-align: center;padding: 5px;">
		<script>
            function new_window()
            {
                var w = window.open("Surprise", "#");
                var d = w.document.open();
                d.write(document.getElementById('details_reports').innerHTML);
                d.close();
            }
        </script>
    	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
    	<span id="popup_report_container" align="center" style="width: 120px;"> </span>
    </div>
    <?
     ob_start();
	?>
    <div align="center" id="details_reports">
    <div style="width:480px">
	    <table width="100%">
	    	<tr>
	    		<th colspan="2" align="left" width="49%">Company : <? echo $company_library[$job_sql[0][csf('company_name')]];?></th>
	    		<th colspan="2" align="left" width="49%">Style : <? echo $job_sql[0][csf('style_ref_no')];?></th>
	    	</tr>
	    	<tr>
	    		<th colspan="2" align="left" width="49%">Buyer : <? echo $buyer_short_library[$job_sql[0][csf('buyer_name')]];?></th>
	    		<th colspan="2" align="left" width="49%">Po No. : <? echo $job_sql[0][csf('po_number')];?></th>
	    	</tr>
	    </table>
	</div>
    <fieldset style="width:480px">
        <legend>Cut and Lay</legend>
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="470">
                <thead>
                    <tr>
                        <td width="30">SL</td>
                        <td width="100">Date</td>
                        <td width="120">Production Qnty</td>
                        <td>Remarks</td>
                    </tr>
                </thead>
                <tbody>
				<?
                if($country_id>0) $country_cond=" and a.country_id='$country_id'"; else $country_cond="";
                if($color_id>0) $color_cond=" and c.color_number_id=$color_id"; else $color_cond="";
                
                $sql_cutAndLay= sql_select("SELECT a.remarks, max(a.entry_date) as production_date ,sum(c.size_qty) as production_quantity from  ppl_cut_lay_mst a,  ppl_cut_lay_dtls b, ppl_cut_lay_bundle c where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id and c.order_id in($po_break_down_id) and b.gmt_item_id=$item_id group by a.remarks order by production_date");
                $i=1;
                foreach($sql_cutAndLay as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td align="center"><? if($row[csf("production_date")]!="" && $row[csf("production_date")]!="0000-00-00") echo change_date_format($row[csf("production_date")]); ?></td>
                        <td align="right"><? echo number_format($row[csf("production_quantity")],2); ?></td>
                        <td><? echo $row[csf('remarks')];?></td>
                    </tr>
                    <?
                    $i++;
                }
                ?>
                </tbody>
            </table>
        </fieldset>



        <fieldset style="width:480px">
        <legend>Cutting</legend>
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="470">
                <thead>
                    <tr>
                        <td width="30">SL</td>
                        <td width="100">Date</td>
                        <td width="120">Production Qnty</td>
                        <td>Remarks</td>
                    </tr>
                </thead>
                <tbody>
				<?
                if($country_id>0) $country_cond=" and a.country_id='$country_id'"; else $country_cond="";
                if($color_id>0) $color_cond=" and c.color_number_id=$color_id"; else $color_cond="";
                
                $sql_cutting= sql_select("SELECT a.production_date,a.remarks,sum(b.production_qnty) as production_quantity from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
                where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond $color_cond and a.production_type='1' and b.production_type='1' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1   group by a.production_date,a.remarks order by a.production_date");
                $i=1;
                foreach($sql_cutting as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td align="center"><? if($row[csf("production_date")]!="" && $row[csf("production_date")]!="0000-00-00") echo change_date_format($row[csf("production_date")]); ?></td>
                        <td align="right"><? echo number_format($row[csf("production_quantity")],2); ?></td>
                        <td><? echo $row[csf("remarks")]; ?></td>
                    </tr>
                    <?
                    $i++;
                }
                ?>
                </tbody>
            </table>
        </fieldset>
        <fieldset style="width:480px">
        <legend>Print/Embr Issue</legend>
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="470">
                <thead>
                    <tr>
                        <td width="30">SL</td>
                        <td width="100">Date</td>
                        <td width="120">Production Qnty</td>
                        <td>Remarks</td>
                    </tr>
                </thead>
                <tbody>
                <?
                $sql_print_issue= sql_select("SELECT a.production_date,a.remarks,sum(b.production_qnty) as production_quantity 
                from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
                where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond $color_cond and a.production_type='2' and b.production_type='2' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1   group by a.production_date,a.remarks  order by a.production_date");
                $i=1;
                foreach($sql_print_issue as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td align="center"><? if($row[csf("production_date")]!="" && $row[csf("production_date")]!="0000-00-00") echo change_date_format($row[csf("production_date")]); ?></td>
                        <td align="right"><? echo number_format($row[csf("production_quantity")],2); ?></td>
                        <td><? echo $row[csf("remarks")]; ?></td>
                    </tr>
                    <?
                    $i++;
                }
                ?>
                </tbody>
            </table>
        </fieldset>
        <fieldset style="width:480px">
        <legend>Print/Embr Receive</legend>
             <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="470">
                <thead>
                    <tr>
                        <td width="30">SL</td>
                        <td width="100">Date</td>
                        <td width="120">Production Qnty</td>
                        <td>Remarks</td>
                    </tr>
                </thead>
                <tbody>
                <?
                $sql_print_rcv=  sql_select("SELECT a.production_date,a.remarks,sum(b.production_qnty) as production_quantity 
                from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
                where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond $color_cond and a.production_type='3' and b.production_type='3' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1   group by a.production_date,a.remarks  order by a.production_date");
                
                $i=1;
                foreach($sql_print_rcv as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td align="center"><? if($row[csf("production_date")]!="" && $row[csf("production_date")]!="0000-00-00") echo change_date_format($row[csf("production_date")]); ?></td>
                        <td align="right"><? echo number_format($row[csf("production_quantity")],2); ?></td>
                        <td><? echo $row[csf("remarks")]; ?></td>
                    </tr>
                    <?
                    $i++;
                }
                ?>
                </tbody>
            </table>
        </fieldset>
        <fieldset style="width:480px">
        <legend>Sewing Input</legend>
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="470">
                <thead>
                    <tr>
                        <td width="30">SL</td>
                        <td width="70">Date</td>
                        <td width="80">Production Qnty</td>
                        <td width="100">Floor</td>
                        <td width="70">Line No</td>
                        <td>Remarks</td>
                    </tr>
                </thead>
                <tbody>
                <?
                $sql_sewing_in= sql_select("SELECT a.production_date, a.floor_id, a.prod_reso_allo, a.sewing_line, sum(b.production_qnty) as production_quantity, max(a.remarks) as remarks 
                from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
                where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond $color_cond and a.production_type='4' and b.production_type='4' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1   group by a.production_date, a.floor_id, a.prod_reso_allo, a.sewing_line  order by a.production_date , a.floor_id");
                $i=1;
                foreach($sql_sewing_in as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td align="center"><? if($row[csf("production_date")]!="" && $row[csf("production_date")]!="0000-00-00") echo change_date_format($row[csf("production_date")]); ?></td>
                        <td align="right"><? echo number_format($row[csf("production_quantity")],2); ?></td>
                        <td ><? echo $floor_library[$row[csf("floor_id")]]; ?></td>
                        <td style="padding-left:3px;">
                        <?
                        if($row[csf("prod_reso_allo")]==1)
                        {
                            $sewing_ling_arr=array_unique(explode(",",$actual_resource_library[$row[csf("sewing_line")]]));
                            $all_sewing_line="";
                            foreach($sewing_ling_arr as $line_id)
                            {
                                $all_sewing_line.=$line_library[$line_id].",";
                            }
                            $all_sewing_line=chop($all_sewing_line," , ");
                            echo $all_sewing_line;
                        }
                        else
                        {
                            echo $line_library[$row[csf("sewing_line")]];
                        }
                        ?>
                        </td>
                        <td><? echo $row[csf("remarks")]; ?></td>
                    </tr>
                    <?
                    $i++;
                }
                ?>
                </tbody>
            </table>
        </fieldset>
        <fieldset style="width:480px">
        <legend >Sewing Output</legend>
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="470">
                <thead>
                    <tr>
                        <td width="30">SL</td>
                        <td width="70">Date</td>
                        <td width="80">Production Qnty</td>
                        <td width="100">Floor</td>
                        <td width="70">Line No</td>
                        <td>Remarks</td>
                    </tr>
                </thead>
                <tbody>
                <?
                $sql_sewing_out= sql_select("SELECT a.production_date, a.floor_id, a.prod_reso_allo, a.sewing_line, sum(b.production_qnty) as production_quantity, max(a.remarks) as remarks 
                from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
                where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond $color_cond and a.production_type='5' and b.production_type='5' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1     group by  a.production_date, a.floor_id, a.prod_reso_allo, a.sewing_line order by a.production_date , a.floor_id");
                $i=1;
                foreach($sql_sewing_out as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td align="center"><? if($row[csf("production_date")]!="" && $row[csf("production_date")]!="0000-00-00") echo change_date_format($row[csf("production_date")]); ?></td>
                        <td align="right"><? echo number_format($row[csf("production_quantity")],2); ?></td>
                        <td ><? echo $floor_library[$row[csf("floor_id")]]; ?></td>
                        <td style="padding-left:3px;">
                        <?
                        if($row[csf("prod_reso_allo")]==1)
                        {
                            $sewing_ling_arr=array_unique(explode(",",$actual_resource_library[$row[csf("sewing_line")]]));
                            $all_sewing_line="";
                            foreach($sewing_ling_arr as $line_id)
                            {
                                $all_sewing_line.=$line_library[$line_id].",";
                            }
                            $all_sewing_line=chop($all_sewing_line," , ");
                            echo $all_sewing_line;
                        }
                        else
                        {
                            echo $line_library[$row[csf("sewing_line")]];
                        }
                        ?>
                        </td>
                        <td><? echo $row[csf("remarks")]; ?></td>
                    </tr>
                    <?
                    $i++;
                }
                ?>
                </tbody>
            </table>
        </fieldset>
        <fieldset style="width:480px">
        <legend>Poly</legend>
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="470">
                <thead>
                    <tr>
                        <td width="30">SL</td>
                        <td width="70">Date</td>
                        <td width="80">Production Qty</td>
                        <td width="100">Floor</td>
                        <td width="70">Line No</td>
                        <td>Remarks</td>
                    </tr>
                </thead>
                <tbody>
                <?
                $sql_sewing_out= sql_select("SELECT a.production_date, a.floor_id, a.prod_reso_allo, a.sewing_line, sum(b.production_qnty) as production_quantity, max(a.remarks) as remarks 
                from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
                where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond $color_cond and a.production_type='11' and b.production_type='11' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1   group by  a.production_date, a.floor_id, a.prod_reso_allo, a.sewing_line order by a.production_date , a.floor_id");
                $i=1;
                foreach($sql_sewing_out as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td align="center"><? if($row[csf("production_date")]!="" && $row[csf("production_date")]!="0000-00-00") echo change_date_format($row[csf("production_date")]); ?></td>
                        <td align="right"><? echo number_format($row[csf("production_quantity")],2); ?></td>
                        <td><? echo $floor_library[$row[csf("floor_id")]]; ?></td>
                        <td style="padding-left:3px;">
                        <?
                        if($row[csf("prod_reso_allo")]==1)
                        {
                            $sewing_ling_arr=array_unique(explode(",",$actual_resource_library[$row[csf("sewing_line")]]));
                            $all_sewing_line="";
                            foreach($sewing_ling_arr as $line_id)
                            {
                                $all_sewing_line.=$line_library[$line_id].",";
                            }
                            $all_sewing_line=chop($all_sewing_line," , ");
                            echo $all_sewing_line;
                        }
                        else
                        {
                            echo $line_library[$row[csf("sewing_line")]];
                        }
                        ?>
                        </td>
                        <td><? echo $row[csf("remarks")]; ?></td>
                    </tr>
                    <?
                    $i++;
                }
                ?>
                </tbody>
            </table>
        </fieldset>
        <fieldset style="width:480px">
        <legend>Finishing & Packing</legend>
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="470">
                <thead>
                    <tr>
                        <td width="30">SL</td>
                        <td width="100">Date</td>
                        <td width="100">Production Qnty</td>
                        <td width="100">Floor</td>
                        <td>Remarks</td>
                    </tr>
                </thead>
                <tbody>
                <?
                $sql_finish_out= sql_select("SELECT a.production_date, a.floor_id, sum(b.production_qnty) as production_quantity, max(a.remarks) as remarks 
                from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
                where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=$po_break_down_id and a.item_number_id=$item_id $country_cond $color_cond and a.production_type='8' and b.production_type='8' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1    group by a.production_date,a.floor_id order by a.production_date , a.floor_id");
                $i=1;
                foreach($sql_finish_out as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td align="center"><? if($row[csf("production_date")]!="" && $row[csf("production_date")]!="0000-00-00") echo change_date_format($row[csf("production_date")]); ?></td>
                        <td align="right"><? echo number_format($row[csf("production_quantity")],2); ?></td>
                        <td ><? echo $floor_library[$row[csf("floor_id")]]; ?></td>
                        <td><? echo $row[csf("remarks")]; ?></td>
                    </tr>
                    <?
                    $i++;
                }
                ?>
                </tbody>
            </table>
        </fieldset>
	<?
	$html=ob_get_contents();
	ob_flush();
	
	foreach (glob(""."*.xls") as $filename) 
	{
	   @unlink($filename);
	}
	
	//html to xls convert
	$name=time();
	$name=$user_id."_".$name.".xls";
	$create_new_excel = fopen(''.$name, 'w');	
	$is_created = fwrite($create_new_excel,$html);	
	?>
    <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
    <script>
		$(document).ready(function(e) 
		{
			document.getElementById('popup_report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Convert to Excel" name="excel" id="excel" style="padding:0 2px;" class="formbutton"/></a>&nbsp;&nbsp;';
		});	
	</script>
	</div>  
	<?
	exit();
}


if ($action=="reject_qty_backup")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	
	//echo $po_id;
	?>
     <div style="width:780px;" align="center"> 
       <table width="770" cellspacing="0" border="1" class="rpt_table" rules="all" > 
             <thead>
             	<tr>
                	<th colspan="13">Reject Qty Details</th>
                </tr>
                <tr>
                    <th width="20">SL</th>
                    <th width="80">Color</th>
                    <th width="70">Size</th>
                    <th width="60">Cutting Reject Qty</th>
                    <th width="60">Print Reject Qty</th>
                    <th width="60">Emb. Reject Qty</th>
                    <th width="60">Wash Reject Qty</th>
                    <th width="60">Special Reject Qty</th>
                    <th width="60">Sewing Out Reject Qty</th>
                    <th width="60">Iron Reject Qty</th>
                    <th width="60">Poly Reject Qty</th>
                    <th width="60">Finish Reject Qty.</th>
                    <th>Total Reject Qty.</th>
                 </tr>
              </thead>
              <tbody> 
			 <?
			 $color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
			 $size_arr=return_library_array( "select id, size_name from lib_size", "id", "size_name");
			$po_id=str_replace("'","",$po_id); 
			$item_id=str_replace("'","",$item_id);
			$country_id=str_replace("'","",$country_id);
			$color_id=str_replace("'","",$color_id);
			//echo $po_id."*".$item_id."*".$country_id."*".$color_id."<br>";
			$sql_cond="";
			if($country_id>0) $sql_cond =" and a.country_id=$country_id";
			if($color_id>0) $sql_cond .=" and c.color_number_id=$color_id";
			 $rej_data_arr=array(); $rej_qty_arr=array();
			/*$sql_qry="Select a.po_break_down_id, c.color_number_id, c.size_number_id,
							sum(CASE WHEN a.production_type ='1' THEN b.reject_qty ELSE 0 END) AS cutting_rej_qnty,
							sum(CASE WHEN a.production_type ='3' THEN b.reject_qty ELSE 0 END) AS emb_rej_qnty,
							
							sum(CASE WHEN a.production_type ='3' AND a.embel_name=1 THEN b.reject_qty ELSE 0 END) AS printreceived_qnty,
							sum(CASE WHEN a.production_type ='3' AND a.embel_name=2 THEN b.reject_qty ELSE 0 END) AS embreceived_qnty, 
							sum(CASE WHEN a.production_type ='3' AND a.embel_name=3 THEN b.reject_qty ELSE 0 END) AS washreceived_qnty, 
							sum(CASE WHEN a.production_type ='3' AND a.embel_name=4 THEN b.reject_qty ELSE 0 END) AS spreceived_qnty, 
							
							sum(CASE WHEN a.production_type ='7' THEN b.reject_qty ELSE 0 END) AS iron_rej_qnty,
			 				sum(CASE WHEN a.production_type ='8' THEN b.reject_qty ELSE 0 END) AS finish_rej_qnty,
							sum(CASE WHEN a.production_type ='5' THEN b.reject_qty ELSE 0 END) AS sewingout_rej_qnty,
							sum(CASE WHEN a.production_type ='11' THEN b.reject_qty ELSE 0 END) AS poly_rej_qnty
							from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c
							where a.id=b.mst_id and b.color_size_break_down_id=c.id and  a.po_break_down_id in ($po_id) and a.item_number_id='$item_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sql_cond  group by a.po_break_down_id, c.color_number_id, c.size_number_id";*/
							
				$sql_qry="SELECT a.po_break_down_id, c.color_number_id, c.size_number_id, a.production_type, a.embel_name, b.reject_qty
							
							from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c
							where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id in ($po_id) and a.production_type in(1,3,5,7,8,11) and a.item_number_id='$item_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $sql_cond order by c.color_order, c.size_order";
			//echo $sql_qry;
			$sql_result=sql_select($sql_qry); 
			foreach($sql_result as $row)
			{
				if($row[csf('embel_name')]=="") $row[csf('embel_name')]=0;
				$rej_data_arr[$row[csf('color_number_id')]]['size'][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$rej_qty_arr[$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('production_type')]][$row[csf('embel_name')]]+=$row[csf('reject_qty')];
			}
			unset($sql_result);
			
			$i=1;	 
			foreach($rej_data_arr as $color_id=>$color_data)
			{
				$rowspn=count($color_data['size']); $k=1;
				foreach($color_data as $ind=>$size_data)
				{
					foreach($size_data as $size_id)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
						$cutting_rej_qty=$printreceived_qty=$embreceived_qty=$washreceived_qty=$spreceived_qty=$sewingout_rej_qty=$iron_rej_qty=$poly_rej_qty=$finish_rej_qty=0;
						
						$cutting_rej_qty=$rej_qty_arr[$color_id][$size_id][1][0];
						$printreceived_qty=$rej_qty_arr[$color_id][$size_id][3][1];
						$embreceived_qty=$rej_qty_arr[$color_id][$size_id][3][2];
						$washreceived_qty=$rej_qty_arr[$color_id][$size_id][3][3];
						$spreceived_qty=$rej_qty_arr[$color_id][$size_id][3][4];
						$sewingout_rej_qty=$rej_qty_arr[$color_id][$size_id][5][0];
						$iron_rej_qty=$rej_qty_arr[$color_id][$size_id][7][0];
						$poly_rej_qty=$rej_qty_arr[$color_id][$size_id][11][0];
						$finish_rej_qty=$rej_qty_arr[$color_id][$size_id][8][0];
						$total_reject=$cutting_rej_qty+$printreceived_qty+$embreceived_qty+$washreceived_qty+$spreceived_qty+$sewingout_rej_qty+$iron_rej_qty+$poly_rej_qty+$finish_rej_qty;
						
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<? if ($k==1) { ?>
                                <td rowspan="<? echo $rowspn; ?>"><? echo $i; ?></td>
                                <td rowspan="<? echo $rowspn; ?>"><? echo $color_arr[$color_id]; ?></td>
                            <? $k++; } ?>
                            <td><? echo $size_arr[$size_id]; ?></td>
                            <td align="right"><? echo number_format($cutting_rej_qty,0); ?>&nbsp;</td>
                            <td align="right"><? echo number_format($printreceived_qty,0); ?>&nbsp;</td>
                            <td align="right"><? echo number_format($embreceived_qty,0); ?>&nbsp;</td>
                            <td align="right"><? echo number_format($washreceived_qty,0); ?>&nbsp;</td>
                            <td align="right"><? echo number_format($spreceived_qty,0); ?>&nbsp;</td>
                            <td align="right"><? echo number_format($sewingout_rej_qty,0); ?>&nbsp;</td>
                            <td align="right"><? echo number_format($iron_rej_qty,0); ?>&nbsp;</td>
                            <td align="right"><? echo number_format($poly_rej_qty,0); ?>&nbsp;</td>
                            <td align="right"><? echo number_format($finish_rej_qty,0); ?>&nbsp;</td>
                            <td align="right"><? echo $total_reject; ?>&nbsp;</td>
						</tr>   
						<? 
						$i++;
						$tot_cutting_rej+=$cutting_rej_qty; 
						$tot_printrec_rej+=$printreceived_qty;
						$tot_embrec_rej+=$embreceived_qty;
						$tot_washrec_rej+=$washreceived_qty;
						$tot_sphrec_rej+=$spreceived_qty;
						$tot_sewing_rej+=$sewingout_rej_qty;
						$tot_iron_rej+=$iron_rej_qty;
						$tot_poly_rej+=$poly_rej_qty;
						$tot_finish_rej+=$finish_rej_qty;
						$tot_rej+=$total_reject;
					}
				}
			} 
			?> 
            </tbody>
            <tfoot>
                <tr bgcolor="#CCCCCC">
                    <td colspan="3" align="right">Total:</td>
                    <td align="right"><? echo number_format($tot_cutting_rej,0); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($tot_printrec_rej,0); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($tot_embrec_rej,0); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($tot_washrec_rej,0); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($tot_sphrec_rej,0); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($tot_sewing_rej,0); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($tot_iron_rej,0); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($tot_poly_rej,0); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($tot_finish_rej,0); ?>&nbsp;</td>
                    <td align="right"><? echo $tot_rej; ?>&nbsp;</td>
                </tr> 
            </tfoot>
        </table>
		</div>    
	<?
	exit();
}

if ($action=="reject_qty")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	
	//echo $po_id;
	?>
     <div style="width:720px;" align="center"> 
       <table width="710" cellspacing="0" border="1" class="rpt_table" rules="all" > 
             <thead>
             	<tr>
                	<th colspan="13">Reject Qty Details</th>
                </tr>
                <tr>
                    <th width="20">SL</th>
                    <th width="80">Color</th>
                    <th width="70">Size</th>
                    <th width="60">Cutting Reject Qty</th>
                    <th width="60">Print Reject Qty</th>
                    <th width="60">Emb. Reject Qty</th>
                    <th width="60">Wash Reject Qty</th>
                    <th width="60">Special Reject Qty</th>
                    <!-- <th width="60">Sewing Out Reject Qty</th> -->
                    <th width="60">Iron Reject Qty</th>
                    <th width="60">Poly Reject Qty</th>
                    <th width="60">Finish Reject Qty.</th>
                    <th>Total Reject Qty.</th>
                 </tr>
              </thead>
              <tbody> 
			 <?
			 $color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
			 $size_arr=return_library_array( "select id, size_name from lib_size", "id", "size_name");
			$po_id=str_replace("'","",$po_id); 
			$item_id=str_replace("'","",$item_id);
			$country_id=str_replace("'","",$country_id);
			$color_id=str_replace("'","",$color_id);
			//echo $po_id."*".$item_id."*".$country_id."*".$color_id."<br>";
			$sql_cond="";
			if($country_id>0) $sql_cond =" and a.country_id=$country_id";
			if($color_id>0) $sql_cond .=" and c.color_number_id=$color_id";
			 $rej_data_arr=array(); $rej_qty_arr=array();
			/*$sql_qry="Select a.po_break_down_id, c.color_number_id, c.size_number_id,
							sum(CASE WHEN a.production_type ='1' THEN b.reject_qty ELSE 0 END) AS cutting_rej_qnty,
							sum(CASE WHEN a.production_type ='3' THEN b.reject_qty ELSE 0 END) AS emb_rej_qnty,
							
							sum(CASE WHEN a.production_type ='3' AND a.embel_name=1 THEN b.reject_qty ELSE 0 END) AS printreceived_qnty,
							sum(CASE WHEN a.production_type ='3' AND a.embel_name=2 THEN b.reject_qty ELSE 0 END) AS embreceived_qnty, 
							sum(CASE WHEN a.production_type ='3' AND a.embel_name=3 THEN b.reject_qty ELSE 0 END) AS washreceived_qnty, 
							sum(CASE WHEN a.production_type ='3' AND a.embel_name=4 THEN b.reject_qty ELSE 0 END) AS spreceived_qnty, 
							
							sum(CASE WHEN a.production_type ='7' THEN b.reject_qty ELSE 0 END) AS iron_rej_qnty,
			 				sum(CASE WHEN a.production_type ='8' THEN b.reject_qty ELSE 0 END) AS finish_rej_qnty,
							sum(CASE WHEN a.production_type ='5' THEN b.reject_qty ELSE 0 END) AS sewingout_rej_qnty,
							sum(CASE WHEN a.production_type ='11' THEN b.reject_qty ELSE 0 END) AS poly_rej_qnty
							from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c
							where a.id=b.mst_id and b.color_size_break_down_id=c.id and  a.po_break_down_id in ($po_id) and a.item_number_id='$item_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sql_cond  group by a.po_break_down_id, c.color_number_id, c.size_number_id";*/
							
				$sql_qry="SELECT a.po_break_down_id, c.color_number_id, c.size_number_id, a.production_type, a.embel_name, b.reject_qty
							
							from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c
							where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id in ($po_id) and a.production_type in(1,3,5,7,8,11) and a.item_number_id='$item_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $sql_cond order by c.color_order, c.size_order";
			// echo $sql_qry;
			$sql_result=sql_select($sql_qry); 
			foreach($sql_result as $row)
			{
				if($row[csf('embel_name')]=="") $row[csf('embel_name')]=0;
				$rej_data_arr[$row[csf('color_number_id')]]['size'][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$rej_qty_arr[$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('production_type')]][$row[csf('embel_name')]]+=$row[csf('reject_qty')];
			}
			unset($sql_result);
			
			$i=1;	 
			foreach($rej_data_arr as $color_id=>$color_data)
			{
				$rowspn=count($color_data['size']); $k=1;
				foreach($color_data as $ind=>$size_data)
				{
					foreach($size_data as $size_id)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
						$cutting_rej_qty=$printreceived_qty=$embreceived_qty=$washreceived_qty=$spreceived_qty=$sewingout_rej_qty=$iron_rej_qty=$poly_rej_qty=$finish_rej_qty=0;
						
						$cutting_rej_qty=$rej_qty_arr[$color_id][$size_id][1][0];
						$printreceived_qty=$rej_qty_arr[$color_id][$size_id][3][1];
						$embreceived_qty=$rej_qty_arr[$color_id][$size_id][3][2];
						$washreceived_qty=$rej_qty_arr[$color_id][$size_id][3][3];
						$spreceived_qty=$rej_qty_arr[$color_id][$size_id][3][4];
						// $sewingout_rej_qty=$rej_qty_arr[$color_id][$size_id][5][0];
						$iron_rej_qty=$rej_qty_arr[$color_id][$size_id][7][0];
						$poly_rej_qty=$rej_qty_arr[$color_id][$size_id][11][0];
						$finish_rej_qty=$rej_qty_arr[$color_id][$size_id][8][0];
						$total_reject=$cutting_rej_qty+$printreceived_qty+$embreceived_qty+$washreceived_qty+$spreceived_qty+$iron_rej_qty+$poly_rej_qty+$finish_rej_qty;
						
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<? if ($k==1) { ?>
                                <td rowspan="<? echo $rowspn; ?>"><? echo $i; ?></td>
                                <td rowspan="<? echo $rowspn; ?>"><? echo $color_arr[$color_id]; ?></td>
                            <? $k++; } ?>
                            <td><? echo $size_arr[$size_id]; ?></td>
                            <td align="right"><? echo number_format($cutting_rej_qty,0); ?>&nbsp;</td>
                            <td align="right"><? echo number_format($printreceived_qty,0); ?>&nbsp;</td>
                            <td align="right"><? echo number_format($embreceived_qty,0); ?>&nbsp;</td>
                            <td align="right"><? echo number_format($washreceived_qty,0); ?>&nbsp;</td>
                            <td align="right"><? echo number_format($spreceived_qty,0); ?>&nbsp;</td>
                            <!--<td align="right"><? //echo number_format($sewingout_rej_qty,0);?>&nbsp;</td>-->
                            <td align="right"><? echo number_format($iron_rej_qty,0); ?>&nbsp;</td>
                            <td align="right"><? echo number_format($poly_rej_qty,0); ?>&nbsp;</td>
                            <td align="right"><? echo number_format($finish_rej_qty,0); ?>&nbsp;</td>
                            <td align="right"><? echo $total_reject; ?>&nbsp;</td>
						</tr>   
						<? 
						$i++;
						$tot_cutting_rej+=$cutting_rej_qty; 
						$tot_printrec_rej+=$printreceived_qty;
						$tot_embrec_rej+=$embreceived_qty;
						$tot_washrec_rej+=$washreceived_qty;
						$tot_sphrec_rej+=$spreceived_qty;
						// $tot_sewing_rej+=$sewingout_rej_qty;
						$tot_iron_rej+=$iron_rej_qty;
						$tot_poly_rej+=$poly_rej_qty;
						$tot_finish_rej+=$finish_rej_qty;
						$tot_rej+=$total_reject;
					}
				}
			} 
			?> 
            </tbody>
            <tfoot>
                <tr bgcolor="#CCCCCC">
                    <td colspan="3" align="right">Total:</td>
                    <td align="right"><? echo number_format($tot_cutting_rej,0); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($tot_printrec_rej,0); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($tot_embrec_rej,0); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($tot_washrec_rej,0); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($tot_sphrec_rej,0); ?>&nbsp;</td>
                    <!--<td align="right"><? //echo number_format($tot_sewing_rej,0); ?>&nbsp;</td>-->
                    <td align="right"><? echo number_format($tot_iron_rej,0); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($tot_poly_rej,0); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($tot_finish_rej,0); ?>&nbsp;</td>
                    <td align="right"><? echo $tot_rej; ?>&nbsp;</td>
                </tr> 
            </tfoot>
        </table>
		</div>    
	<?
	exit();
}


if ($action=="sewing_reject_qty")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	
	//echo $po_id;
	?>
     <div style="width:310px;" align="center"> 
       <table width="340" cellspacing="0" border="1" class="rpt_table" rules="all" > 
             <thead>
             	<tr>
                	<th colspan="13">Sewing Reject Qty Details</th>
                </tr>
                <tr>
                    <th width="20">SL</th>
                    <th width="130">Color</th>
                    <th width="70">Size</th>
                    <th width="60">Sewing Out Reject Qty</th>
                    <th>Total Reject Qty.</th>
                 </tr>
              </thead>
              <tbody> 
			 <?
			 $color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
			 $size_arr=return_library_array( "select id, size_name from lib_size", "id", "size_name");
			$po_id=str_replace("'","",$po_id); 
			$item_id=str_replace("'","",$item_id);
			$country_id=str_replace("'","",$country_id);
			$color_id=str_replace("'","",$color_id);
			//echo $po_id."*".$item_id."*".$country_id."*".$color_id."<br>";
			$sql_cond="";
			if($country_id>0) $sql_cond =" and a.country_id=$country_id";
			if($color_id>0) $sql_cond .=" and c.color_number_id=$color_id";
			$rej_data_arr=array(); 
			$rej_qty_arr=array();
							
			$sql_qry="SELECT a.po_break_down_id, c.color_number_id, c.size_number_id, a.production_type, a.embel_name, b.reject_qty						
			from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c
			where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id in ($po_id) and a.production_type in(1,3,5,7,8,11) and a.item_number_id='$item_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $sql_cond order by c.color_order, c.size_order";
			// echo $sql_qry;
			$sql_result=sql_select($sql_qry); 
			foreach($sql_result as $row)
			{
				if($row[csf('embel_name')]=="") $row[csf('embel_name')]=0;
				$rej_data_arr[$row[csf('color_number_id')]]['size'][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$rej_qty_arr[$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('production_type')]][$row[csf('embel_name')]]+=$row[csf('reject_qty')];
			}
			unset($sql_result);
			
			$i=1;	 
			foreach($rej_data_arr as $color_id=>$color_data)
			{
				$rowspn=count($color_data['size']); $k=1;
				foreach($color_data as $ind=>$size_data)
				{
					foreach($size_data as $size_id)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
						$cutting_rej_qty=$printreceived_qty=$embreceived_qty=$washreceived_qty=$spreceived_qty=$sewingout_rej_qty=$iron_rej_qty=$poly_rej_qty=$finish_rej_qty=0;
												
						$sewingout_rej_qty=$rej_qty_arr[$color_id][$size_id][5][0];
						$total_reject=$sewingout_rej_qty;
						
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<? if ($k==1) { ?>
                                <td rowspan="<? echo $rowspn; ?>"><? echo $i; ?></td>
                                <td rowspan="<? echo $rowspn; ?>"><? echo $color_arr[$color_id]; ?></td>
                            <? $k++; } ?>
                            <td valign="middle"><? echo $size_arr[$size_id]; ?></td>
                            <td align="right"><? echo number_format($sewingout_rej_qty,0);?>&nbsp;</td>
                            <td align="right"><? echo $total_reject; ?>&nbsp;</td>
						</tr>   
						<? 
						$i++;
						$tot_sewing_rej+=$sewingout_rej_qty;
						$tot_rej+=$total_reject;
					}
				}
			} 
			?> 
            </tbody>
            <tfoot>
                <tr bgcolor="#CCCCCC">
                    <td colspan="3" align="right">Total:</td>
                    <td align="right"><? echo number_format($tot_sewing_rej,0); ?>&nbsp;</td>
                    <td align="right"><? echo $tot_rej; ?>&nbsp;</td>
                </tr> 
            </tfoot>
        </table>
		</div>    
	<?
	exit();
}
//-------------------------------------------END Show Date Location Floor & Line Wise------------------------
//-------------------------------------------end-----------------------------------------------------------------------------//

if ($action==1 || $action==5) 
{
 	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	if($country_id=='') $country_cond=""; else $country_cond=" and country_id='$country_id'";
 	?>
    <script>
	 var tableFilters_sewout = 
		{
			//col_0: "none",col_1:"none",display_all_text: " -- All --",
			col_operation: { 
				id: ["total_sew_qty"],
				col: [4],
				operation: ["sum"],
				write_method: ["innerHTML"]
			}
		}
	
		function openmypage(po_break_down_id,item_id,prod_type,location_id,floor_id,dateOrLocWise,country_id,prod_date,action)
		{
			var popupWidth = "width=550px,height=320px,";	
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'order_wise_production_report_format2_controller.php?po_break_down_id='+po_break_down_id+'&item_id='+item_id+'&action='+action+'&location_id='+location_id+'&floor_id='+floor_id+'&dateOrLocWise='+dateOrLocWise+'&country_id='+country_id+'&prod_date='+prod_date+'&prod_type='+prod_type, 'Production Quantity', popupWidth+'center=1,resize=0,scrolling=0','../../');
		}
	</script>
    <fieldset>
    <div style="margin-left:50px">
        <table width="620" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" >
            <thead>
                 <? if($action==1){ ?>
                    <tr>
                        <th width="50">Sl.</th>    
                        <th width="100">Cutting Date</th>
                        <th width="160">Cutt. Qty(In-house)</th>
                        <th width="160">Cutt. Qty(Out-bound)</th>
                        <th width="">Cutting Company</th>
                 	</tr>
				<? } else if($action==5){ ?>
                    <tr>
                        <th width="30">Sl.</th>    
                        <th width="80">Output Date</th>
                        <th width="80">PO No</th>
                        <th width="80">Sewing Line</th>
                        <th width="80">Sew.Qty</th>
                        <th width="100">Source</th>
                        <th width="">Sewing Company</th>
                    </tr>
 				<? } ?>
            </thead>
        </table>
        <div style="max-height:370px; overflow-y:scroll; width:638px;" id="scroll_body">
            <table cellspacing="0" border="1" class="rpt_table" width="620" rules="all" id="table_body" >
            <?
             $total_in_quantity=0;$total_out_quantity=0;
             $i=1;
 			 $company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
 			 $supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id", "supplier_name" );
			 $po_array=return_library_array( "select id,po_number from  wo_po_break_down where id in ($po_break_down_id)", "id", "po_number");
			 $sewing_library=return_library_array( "select id,line_name from  lib_sewing_line", "id", "line_name");
			 $prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
			
			 $location="";$floor="";
			 if($dateOrLocWise==2 || $dateOrLocWise==4) // only for location floor and line wise
			 {
				 if($location_id!="") $location=" and location=$location_id";
				 if($floor_id!="") $floor=" and floor_id=$floor_id";
			 }
			 if($action==5)
			 {
				 $sql=sql_select("select po_break_down_id,production_date,prod_reso_allo,sewing_line,production_source,serving_company,
					  SUM(production_quantity) as production_quantity
					  from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type=$action and status_active=1 $location $floor $country_cond group by serving_company,production_date,po_break_down_id,prod_reso_allo,sewing_line,production_source"); 
			 }
			 else
			 {
				 $sql=sql_select("select po_break_down_id,production_date,production_source,serving_company,
					  SUM(CASE WHEN production_source=1 THEN production_quantity ELSE 0 END) as in_house_cut_qnty,
					  SUM(CASE WHEN production_source=3 THEN production_quantity ELSE 0 END) as out_bound_cut_qnty
					  from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type=$action and status_active=1 $location $floor $country_cond group by po_break_down_id,serving_company,production_date,production_source");
			 }
				  
            foreach($sql as $resultRow)
            {
                 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				 if($action==5)
				 {
					$sewing_line='';
					if($resultRow[csf('prod_reso_allo')]==1)
					{
						$line_number=explode(",",$prod_reso_arr[$resultRow[csf('sewing_line')]]);
						foreach($line_number as $val)
						{
							if($sewing_line=='') $sewing_line=$sewing_library[$val]; else $sewing_line.=",".$sewing_library[$val];
						}
					}
					else $sewing_line=$sewing_library[$resultRow[csf('sewing_line')]];
             	?>
                 <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                    <td width="30"><? echo $i;?></td>
                    <td width="80"><a href="##" onclick="openmypage('<? echo $po_break_down_id ?>','<? echo $item_id ?>','<? echo $action ?>','<? echo $location_id ?>','<? echo $floor_id ?>','<? echo $dateOrLocWise ?>','<? echo $country_id ?>','<? echo $resultRow[csf("production_date")]; ?>','challanPopup')"><? echo change_date_format($resultRow[csf("production_date")]); ?></a></td>
                    <td width="80"><p><? echo $po_array[$resultRow[csf("po_break_down_id")]]; ?></p></td>
                    <td width="80"><? echo $sewing_line; ?></td>
                    <td width="80" align="right"><? echo number_format($resultRow[csf("production_quantity")]); ?>&nbsp;</td>
                    <td width="100"><? echo $knitting_source[$resultRow[csf("production_source")]]; ?></td>
                    <?
                    	$source= $resultRow[csf('production_source')];
					    if($source==3)
						{
							$serving_company= $supplier_library[$resultRow[csf('serving_company')]];
						}
						else
						{
							$serving_company= $company_library[$resultRow[csf('serving_company')]];
						}
					?>
                    <td width=""><p><? echo $serving_company; ?></p></td>
                 </tr>	
                 <?	
				 }
				 else
				 {
             	?>
                 <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                    <td width="50"><? echo $i;?></td>
                    <td width="100"><a href="##" onclick="openmypage('<? echo $po_break_down_id ?>','<? echo $item_id ?>','<? echo $action ?>','<? echo $location_id ?>','<? echo $floor_id ?>','<? echo $dateOrLocWise ?>','<? echo $country_id ?>','<? echo $resultRow[csf("production_date")]; ?>','challanPopup')"><? echo change_date_format($resultRow[csf("production_date")]); ?></a></td>
                    <td width="160" align="right"><? echo number_format($resultRow[csf("in_house_cut_qnty")]); ?></td>
                    <td width="160" align="right"><? echo number_format($resultRow[csf("out_bound_cut_qnty")]); ?></td>
                    <?
                    	$source= $resultRow[csf('production_source')];
					    if($source==3)
						{
							$serving_company= $supplier_library[$resultRow[csf('serving_company')]];
						}
						else
						{
							$serving_company= $company_library[$resultRow[csf('serving_company')]];
						}
					?>
                    <td width=""><p><? echo $serving_company; ?></p></td>
                 </tr>	
                 <?	
				 }
				 	
				$total_sewing_quantity+=$resultRow[csf("production_quantity")];
				$total_in_quantity+=$resultRow[csf("in_house_cut_qnty")];
				$total_out_quantity+=$resultRow[csf("out_bound_cut_qnty")];
				$i++;
			}//end foreach 1st
			?>
			</table>
        <table width="500" cellspacing="0" border="1" class="tbl_bottom" rules="all" id="body_bottom" >	
           <?
			if($action==5)
			{
			?>
            	<tfoot class="tbl_bottom">
                     <tr> 

                        <td width="30">&nbsp;</td> 
                        <td width="80">&nbsp;</td> 
                        <td width="80">&nbsp;</td> 
                        <td width="80">Total</td> 
                        <td width="80" id="total_sew_qty"><? echo number_format($total_sewing_quantity); ?>&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td>&nbsp;</td> 
                     </tr>
                 </tfoot>
			 <?
			 }
			 else
			 {
			?>
            	<tfoot class="tbl_bottom">
                     <tr> 
                        <td width="50">&nbsp;</td> 
                        <td width="100">Total</td> 
                        <td width="160"><? echo number_format($total_in_quantity); ?> </td>
                        <td width="160"><? echo number_format($total_out_quantity); ?></td>
                        <td width="">&nbsp;</td> 
                     </tr>
                 </tfoot>
			 <?
			 }
			 ?>
			</table>
		</div>
	</div>
	</fieldset>
    <script>
	var action=<? echo $action; ?>;
	if(action==5) setFilterGrid("table_body",-1,tableFilters_sewout);
	</script>
    <?
 exit();
 
}


//---- sewing input-4, iron input-7, finish-8, re_iron input-9-----------popup--------// 
if ($action==4 || $action==7 || $action==8 || $action==9) // popup
{
	 
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	?>
	 <script>
	 var tableFilters_sewin = 
	{
		//col_0: "none",col_1:"none",display_all_text: " -- All --",
		col_operation: { 
			id: ["total_issue_qty"],
			col: [4],
			operation: ["sum"],
			write_method: ["innerHTML"]
		}
 	}
	</script>
    <?
	if($country_id=='') $country_cond=""; else $country_cond=" and country_id='$country_id'";
	
 	?>
    <fieldset>
    <div style="margin-left:60px">
        <table width="500" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" >
            <thead>
				<? if($action==2){ ?>
                
                    <tr>
                        <th width="50">Sl.</th>    
                        <th width="200">Print/ Emb. Issue Date</th>
                        <th width="">Print/ Emb. Issue Qnty</th>
                    </tr>
                
				<? } else if($action==3){ ?>
               
                    <tr>
                        <th width="50">Sl.</th>    
                        <th width="200">Print/ Emb. Receive Date</th>
                        <th width="">Print/ Emb. Receive Qnty</th>
                    </tr>
                
				<? } else if($action==4){ ?>
                
                    <tr>
                        <th width="30">Sl.</th>    
                        <th width="70">Sewing Date</th>
                        <th width="80">PO No</th>
                        <th width="80">Sewing Line</th>
                        <th width="80">Sewing Qty</th>
                        <th width="">Source</th>
                    </tr>
                <? } else if($action==7){ ?>
                
                    <tr>
                        <th width="50">Sl.</th>    
                        <th width="200">Iron Output Date</th>
                        <th width="">Iron Output Qnty</th>
                    </tr>
                <? } else if($action==8){ ?>
                
                    <tr>
                        <th width="50">Sl.</th>    
                        <th width="200">Finish Date</th>
                        <th width="">Finish Qty</th>
                    </tr>
                <? } else if($action==9){ ?>
                    <tr>
                        <th width="50">Sl.</th>    
                        <th width="200">Iron Output Date</th>
                        <th width="">Re-Iron Output Qty</th>
                    </tr>
               <? } ?> 
            </thead>
        </table>
        <div style="max-height:425px; overflow-y:scroll; width:518px;" id="scroll_body">
            <table width="500" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_body" >
            <?
				$po_array=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number");
				$sewing_library=return_library_array( "select id,line_name from  lib_sewing_line", "id", "line_name");
				$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
             $total_quantity=0;
             $i=1;
			 $location="";$floor="";
			 if($dateOrLocWise==2 || $dateOrLocWise==4) // only for location floor and line wise
			 {
				 if($location_id!="") $location=" and location=$location_id";
				 if($floor_id!="") $floor=" and floor_id=$floor_id";
			 }
			 if ($action==9)
			 {
				 $sql=sql_select("select production_date, sum(re_production_qty) as production_quantity	  
				  from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and re_production_qty!=0 and item_number_id=$item_id and status_active=1 and is_deleted=0 and production_type=7 $location $floor $country_cond group by production_date");
			 }
			 else if ($action==4)
			 {
				 $sql=sql_select("select po_break_down_id,production_date,prod_reso_allo,sewing_line,production_source,sum(production_quantity) as production_quantity
				  from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and status_active=1 and is_deleted=0 and production_type=$action $location $floor $country_cond group by po_break_down_id,production_date,prod_reso_allo,sewing_line,production_source");
			 }
			 else
			 {
				 $sql=sql_select("select production_date,sum(production_quantity) as production_quantity
				  from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and status_active=1 and is_deleted=0 and production_type=$action $location $floor $country_cond group by production_date"); 
			 }
 
            foreach($sql as $resultRow)
            {
                 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				 if ($action==4)
				 {
					 $sewing_line='';
					if($resultRow[csf('prod_reso_allo')]==1)
					{
						$line_number=explode(",",$prod_reso_arr[$resultRow[csf('sewing_line')]]);
						foreach($line_number as $val)
						{
							if($sewing_line=='') $sewing_line=$sewing_library[$val]; else $sewing_line.=",".$sewing_library[$val];
						}
					}
					else $sewing_line=$sewing_library[$resultRow[csf('sewing_line')]];
             	?>
                     <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                        <td width="30"><? echo $i;?></td>
                        <td width="70" align="center"><? echo change_date_format($resultRow[csf("production_date")]); ?></td>
                        <td width="80" align="center"><p><? echo $po_array[$resultRow[csf("po_break_down_id")]]; ?></p></td>
                        <td width="80" align="center"><? echo $sewing_line; ?></td>
                        <td width="80" align="right"><? echo number_format($resultRow[csf("production_quantity")],0); ?>&nbsp;</td>
                        <td><? echo $knitting_source[$resultRow[csf("production_source")]]; ?></td>
                     </tr>	
                 <?	
				 }
				 else
				 {
             	?>
                 <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                    <td width="50"><? echo $i;?></td>
                    <td width="200" align="right"><? echo change_date_format($resultRow[csf("production_date")]); ?></td>
                    <td width="" align="right"><? echo number_format($resultRow[csf("production_quantity")],0); ?>&nbsp;</td>
                 </tr>	
                 <?	
				 }
                    $total_quantity+=$resultRow[csf("production_quantity")];
            	 	$i++;
            
        }//end foreach 1st
        ?>
        </table>
        <table width="500" cellspacing="0" border="1" class="tbl_bottom" rules="all" id="body_bottom" >
        	<?
			if ($action==4)
			{
			?>
                 <tr> 
                    <td width="30">&nbsp;</td>
                    <td width="70">&nbsp;</td>
                    <td width="80">&nbsp;</td>
                    <td width="80">Total</td> 
                    <td width="80" id="total_issue_qty"><? echo number_format($total_quantity,0); ?>&nbsp;</td>
                    <td>&nbsp;</td>
                 </tr>
             <?
			}
			else
			{
			?>
                 <tr> 
                    <td width="50">&nbsp;</td> 
                    <td width="200">Total</td> 
                    <td><? echo number_format($total_quantity,0); ?>&nbsp;</td>
                 </tr>
             <?
			}
			 ?>
         </table>
       </div>
     </div>
     </fieldset>
     <script>
	var action=<? echo $action; ?>;
	
	//if (action==4) setFilterGrid("tableFilters_sewin",-1);
	if(action==4) setFilterGrid("table_body",-1,tableFilters_sewin);
	
	</script>
    <?
 	
 exit();
 
}



//--print/emb issue-2,print/emb receive-3,
if ($action==2 || $action==3)
{
	 
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,1,1);
	
	if($country_id=='') $country_cond=""; else $country_cond=" and country_id='$country_id'";
	$emple_type=$location_id;
	if($emple_type==1) $hedding="Pringing";
	if($emple_type==2) $hedding="Embroidery";
	if($emple_type==3) $hedding="Wash";
	if($emple_type==4) $hedding="Special Work";
 	?>
    <div id="data_panel" align="center" style="width:100%">
         <script>
		 	function new_window()
			 {
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write(document.getElementById('details_reports').innerHTML);
				d.close();
			 }
          </script>
 	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
 	</div>
    <div id="details_reports">
        <table width="400" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" >
            <thead>
                   
                 <? if ($action==2) { ?>  
                   <tr>
                        <th width="30" rowspan="2">Sl.</th>    
                        <th width="80" rowspan="2">Date</th>
                        <th colspan="3"><? echo $hedding; ?> &nbsp; Issue</th>
                        
                    </tr> 
                 <? } else {?>
                 	<tr>
                        <th width="30" rowspan="2">Sl.</th>    
                        <th width="80" rowspan="2">Date</th>
                        <th colspan="3"><? echo $hedding; ?> &nbsp; Receive</th>
                       
                    </tr> 
                 <? } ?>   
                    
                    <tr>
                      
                      <th width="70">In-house</th>
                      <th width="70">Outside</th>
                      <th width="80">Embl. Company</th>
                      
                    
                    </tr>
            </thead>
        </table>
        <div style="max-height:425px; overflow-y:scroll; width:420px;" id="scroll_body">
            <table cellspacing="0" border="1" class="rpt_table"  width="399" rules="all" id="table_body" >
            <?
			$company_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name" );
 			$supplier_library=return_library_array( "select id,short_name from  lib_supplier", "id", "short_name" );	
 			 
			$sql = sql_select("SELECT production_date,production_source,serving_company,
						SUM(CASE WHEN production_source =1 AND embel_name=$emple_type THEN production_quantity ELSE 0 END) AS prod11,  
						SUM(CASE WHEN production_source =3 AND embel_name=$emple_type THEN production_quantity ELSE 0 END) AS prod31  
 					FROM
						pro_garments_production_mst 
					WHERE 
						status_active=1 and is_deleted=0 and production_type=$action and po_break_down_id in ($po_break_down_id) and item_number_id=$item_id $country_cond
					GROUP BY production_date,production_source,serving_company");
			// echo $sql; die;
			
		   	$printing_in_qnty=0;$emb_in_qnty=0;$wash_in_qnty=0;$special_in_qnty=0;
			$printing_out_qnty=0;$emb_out_qnty=0;$wash_out_qnty=0;$special_out_qnty=0;
			$dataArray=array();$companyArray=array();
            $i=1;
			foreach($sql as $resultRow)
            {
                 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				 
				 if($resultRow[csf('production_source')]==3)
					$serving_company= $supplier_library[$resultRow[csf('serving_company')]];
				else
					$serving_company= $company_library[$resultRow[csf('serving_company')]];
				$td_count = 2;	
             	?>
                 <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                    <td width="30"><? echo $i;?></td>
                    <td width="80" align="center"><? echo change_date_format($resultRow[csf("production_date")]); ?></td>
                    
                    <td width="70" align="right"><? if($resultRow[csf('production_source')]==1){ echo $resultRow[csf("prod11")];$printing_in_qnty+=$resultRow[csf("prod11")];}else echo "0"; ?></td>
                    <td width="70" align="right"><? if($resultRow[csf('production_source')]==3){ echo $resultRow[csf("prod31")];$printing_out_qnty+=$resultRow[csf("prod31")];}else echo "0"; ?></td>
                    <td width="80"><p>&nbsp;<? if($resultRow[csf('prod11')]>0 || $resultRow[csf('prod31')]>0) echo $serving_company; ?></p></td>
                    <? 
					$companyArray[$serving_company]=$serving_company;
					$dataArray[1][$serving_company]+=$resultRow[csf("prod11")]+$resultRow[csf("prod31")] ?>
                  </tr> 
 				 <?		
             	$i++;
            
        }//end foreach 1st
        ?>
        		<tfoot>
                    <tr>
                       <th align="right" colspan="2">Grand Total</th>
                       <th align="right"><? echo $printing_in_qnty; ?></th>
                       <th align="right"><? echo $printing_out_qnty; ?></th>
                       <th align="right">&nbsp;</th>
                       
                     </tr>
               </tfoot>      
        </table>
       </div>
     
     
     
 </div>    
    
	<?
  	exit();
 
}



//cutting-1,sewing ouput-5--------------------popup-----------//
if ($action=="challanPopup") 
{
 	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	
	if($country_id=='') $country_cond=""; else $country_cond=" and country_id='$country_id'";
	
 	?>
    <script>

		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="260px";
		}	
		
	</script>
    <div style="width:530px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
    <fieldset style="width:530px; margin-left:5px">
        <div id="report_container">
            <table width="500" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" >
                <thead>
                    <th width="50">Sl.</th>    
                    <th width="150">Production Date</th>
                    <th width="160">Challan No</th>
                    <th>Quantity</th>
                </thead>
            </table>
            <div style="max-height:260px; overflow-y:scroll; width:520px;" id="scroll_body">
                <table cellspacing="0" border="1" class="rpt_table" width="500" rules="all" id="table_body" >
                <?
					 $i=1; $total_quantity=0; $location="";$floor="";
					 if($dateOrLocWise==2 || $dateOrLocWise==4) // only for location floor and line wise
					 {
						 if($location_id!="") $location=" and location=$location_id";
						 if($floor_id!="") $floor=" and floor_id=$floor_id";
					 }
					 
					 $sql=sql_select("select production_date, challan_no, SUM(production_quantity) as production_quantity from pro_garments_production_mst where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and production_type=$prod_type and status_active=1 and production_date='$prod_date' $location $floor $country_cond group by production_date, challan_no");
					foreach($sql as $resultRow)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                         <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                            <td width="50"><? echo $i;?></td>
                            <td width="150" align="center"><? echo change_date_format($resultRow[csf("production_date")]); ?></td>
                            <td width="160"><? echo $resultRow[csf("challan_no")]; ?>&nbsp;</td>
                            <td align="right" style="padding-right:2px"><? echo number_format($resultRow[csf("production_quantity")]); ?></td>
                         </tr>	
                        <?	
                        $total_sewing_quantity+=$resultRow[csf("production_quantity")];
                        $i++;
                    }
                    ?>
                   <tfoot class="tbl_bottom">
                        <td>&nbsp;</td> 
                        <td>&nbsp;</td> 
                        <td align="right">Total</td> 
                        <td align="right" style="padding-right:2px"><? echo number_format($total_sewing_quantity); ?></td>
					</tfoot>
                </table>
            </div>
        </div>
	</fieldset>
    <?
 exit();
 
}

// ===================================== Job and color wise ===================================

if($action=="update_tna_progress_comment_job")
{
	//echo load_html_head_contents("TNA Info","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	// var_dump($_REQUEST);
	$po_no = str_replace("'", "", $po_no);
	if($db_type==0) $blank_date="0000-00-00"; else $blank_date="";

	$tna_task_arr=return_library_array( "select task_name, task_short_name from lib_tna_task",'task_name','task_short_name');
	
	$tna_task_id=array(); $plan_start_array=array(); $plan_finish_array=array();
	$actual_start_array=array();
	$actual_finish_array=array();
	
	$notice_start_array=array();
	$notice_finish_array=array();
	$task_sql= sql_select("select a.task_number,a.task_start_date,a.task_finish_date,a.actual_start_date,a.actual_finish_date,a.notice_date_start,a.notice_date_end from tna_process_mst a, lib_tna_task b where a.task_number=b.task_name and a.template_id=$template_id and a.po_number_id in($po_no) order by b.task_sequence_no asc");
	foreach ($task_sql as $row_task)
	{
		$tna_task_id[]=$row_task[csf("task_number")];
		
		$plan_start_array[$row_task[csf("task_number")]] =$row_task[csf("task_start_date")];
		$plan_finish_array[$row_task[csf("task_number")]]=$row_task[csf("task_finish_date")];
		
		$actual_start_array[$row_task[csf("task_number")]] =$row_task[csf("actual_start_date")];
		$actual_finish_array[$row_task[csf("task_number")]]=$row_task[csf("actual_finish_date")];
		
		$notice_start_array[$row_task[csf("task_number")]] =$row_task[csf("notice_date_start")];
		$notice_finish_array[$row_task[csf("task_number")]]=$row_task[csf("notice_date_end")];
	}
	
	
	
	$comments_array=array(); $responsible_array=array();
	$res_comm_sql= sql_select("select task_id, comments, responsible from tna_progress_comments where tamplate_id=$template_id and order_id in($po_no)");
	foreach ($res_comm_sql as $row_res_comm)
	{
		$comments_array[$row_res_comm[csf("task_id")]] =$row_res_comm[csf("comments")];
		$responsible_array[$row_res_comm[csf("task_id")]]=$row_res_comm[csf("responsible")];
	}
	
	$execution_time_array=array();
	$execution_time_sql= sql_select("select tna_task_id, execution_days from tna_task_template_details where task_template_id=$template_id");
	foreach ($execution_time_sql as $row_execution_time)
	{
		$execution_time_array[$row_execution_time[csf("tna_task_id")]] =$row_execution_time[csf("execution_days")];
	}
	
	$lead_time=return_library_array("select task_template_id,lead_time from tna_task_template_details group by task_template_id,lead_time","task_template_id","lead_time");
	$company_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name"  );
    $buyer_short_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name"  );
	?>


	<fieldset style="width:1010px"> 
        <div class="form_caption" align="center"><strong>TNA Progress Comment</strong></div>
        <table style="margin-top:10px" width="1000" border="1" rules="all" class="rpt_table">
            <?
			$sql ="select b.company_name,b.buyer_name,a.po_number,b.job_no,b.style_ref_no,b.gmts_item_id,a.po_received_date,a.shipment_date from wo_po_break_down a,wo_po_details_master b where b.job_no='$job_no' and a.id in($po_no) and a.job_no_mst=b.job_no";
			//echo $sql;die;
			$result=sql_select($sql);
            foreach($result as $row)
            {
            ?>
            	<thead>
                    <tr bgcolor="#E9F3FF">
                        <th width="130">Company</th>
                        <td width="196" style="padding-left:5px"><? echo $company_library[$row[csf('company_name')]];  ?></td>
                        <th width="130">Buyer</th>
                        <td width="186" style="padding-left:5px"><? echo $buyer_short_library[$row[csf('buyer_name')]];  ?></td>
                        <th width="130">Order No</th>
                        <td width="186" style="padding-left:5px"><p><? echo $row[csf('po_number')]; ?></p></td>
                    </tr>
                    <tr bgcolor="#FFFFFF">
                        <th>Style Ref.</th>
                        <td style="padding-left:5px"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                        <th>RMG Item</th>
                        <td style="padding-left:5px"><p><? echo $garments_item[$row[csf('gmts_item_id')]]; ?></p></td>
                        <th>Order Recv. Date</th>
                        <td style="padding-left:5px"><? echo change_date_format($row[csf('po_received_date')]); ?></td>
                    </tr>
                    <tr bgcolor="#E9F3FF">
                        <th>Ship Date</th>
                        <td style="padding-left:5px"><? echo change_date_format($row[csf('shipment_date')]); ?></td>
                        <th>Lead Time</th>
                        <td style="padding-left:5px">
                            <?
								$template_id=str_replace("'","",$template_id);
								
								if($tna_process_type==1)
								{
									$lead_timee=$lead_time[$template_id];
								}
								else
								{
									$lead_timee=$template_id;
								}
								//echo $lead_time=return_field_value("lead_time","tna_task_template_details", "task_template_id='$template_id' and status_active=1 and is_deleted=0");
								echo $lead_timee;
							?>
                        </td>
                        <th>Job Number</th>
                        <td style="padding-left:5px">
							<? echo $row[csf('job_no')];   ?>
                        </td>
                    </tr>
                </thead>
            <?
            }
            ?>
        </table>
        <table style="margin-top:5px" cellpadding="0" width="1000" class="rpt_table" rules="all" border="1">
            <thead>
                <th width="50">Task No</th>
                <th width="150">Task Name</th>
                <th width="60">Allowed Days</th>
                <th width="80">Plan Start Date</th>
                <th width="80">Plan Finish Date</th>
                <th width="80">Actual Start Date</th>
                <th width="80">Actual Finish Date</th>
                <th width="80">Start Delay/ Early By</th>
                <th width="80">Finish Delay/ Early By</th>
                <th width="100">Responsible</th>
                <th>Comments</th>
            </thead> 	 	
        </table>
        
          
        
            <table cellpadding="0" width="1000" cellspacing="0" border="1" rules="all" class="rpt_table">
                <? 
				
				
				$i=1;
                foreach($tna_task_id as $key)
                { 
                    if($i%2==0) $trcolor="#E9F3FF"; else $trcolor="#FFFFFF";
					
					$bgcolor1=""; $bgcolor="";
									
					if ($plan_start_array[$key]!=$blank_date) 
					{
						if (strtotime($notice_start_array[$key])<=strtotime(date("Y-m-d",time())) && strtotime(date("Y-m-d",time()))<=strtotime($plan_start_array[$key]))  $bgcolor="#FFFF00";
						else if (strtotime($plan_start_array[$key])<strtotime(date("Y-m-d",time())))  $bgcolor="#FF0000";
						else $bgcolor="";
						
					}
					 
					if ($plan_finish_array[$key]!=$blank_date) {
						if (strtotime($notice_finish_array[$key])<=strtotime(date("Y-m-d",time())) && strtotime(date("Y-m-d",time()))<=strtotime($plan_finish_array[$key]))  $bgcolor1="#FFFF00";
						else if (strtotime($plan_finish_array[$key])<strtotime(date("Y-m-d",time())))  $bgcolor1="#FF0000"; else $bgcolor1="";
					}
					
					if ($actual_start_array[$key]!=$blank_date) $bgcolor="";
					if ($actual_finish_array[$key]!=$blank_date) $bgcolor1="";
					
					// Delay / Early............
									
					$bgcolor5=""; $bgcolor6="";
					$delay=""; $early="";
					
					if($actual_start_array[$key]!=$blank_date)
					{
						$start_diff1 = datediff( "d", $actual_start_array[$key], $plan_start_array[$key]);
						$finish_diff1 = datediff( "d", $actual_finish_array[$key], $plan_finish_array[$key]);
						
						$start_diff=$start_diff1-1;
						$finish_diff=$finish_diff1-1;
						
						if($start_diff<0)
						{
							$bgcolor5="#2A9FFF";	//Blue
							$start="(Delay)";
						}
						if($start_diff>0)
						{
							$bgcolor5="";
							$start="(Early)";
							
						}
						if($finish_diff<0)
						{
							$bgcolor6="#2A9FFF";
							$finish="(Delay)";
						}
						if($finish_diff>0)
						{	
							$bgcolor6="";
							$finish="(Early)";
						}
						
						
					}
					else
					{
						if(date("Y-m-d")>$plan_start_array[$key])
						{
							$start_diff1 = datediff( "d", $plan_start_array[$key], date("Y-m-d"));
							$start_diff=$start_diff1-1;
							$bgcolor5="#FF0000";		//Red
							$start="(Delay)";
						}
						if(date("Y-m-d")>$plan_finish_array[$key])
						{
							$finish_diff1 = datediff( "d", $plan_finish_array[$key], date("Y-m-d"));
							$finish_diff=$finish_diff1-1;
							$bgcolor6="#FF0000";
							$finish="(Delay)";
						}
						if(date("Y-m-d")<=$plan_start_array[$key])
						{
							$start_diff = "";
							$bgcolor5="";
							$start="(Ac. Start Dt. Not Found)";
						}
						if(date("Y-m-d")<=$plan_finish_array[$key])
						{
							$finish_diff = "";
							$bgcolor6="";
							$finish="(Ac. Finish Dt. Not Found)";
							
						}
					}
							
                    ?>
                    <tr bgcolor="<? echo $trcolor; ?>" id="tr_<? echo $i; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')">
                        <td align="center" width="50"><? echo $i; ?></td>
                        <td width="150"><? echo $tna_task_arr[$key]; ?></td>
                        <td align="center" width="60"><? echo datediff( "d", $plan_start_array[$key],$plan_finish_array[$key]);//$execution_time_array[$key]; ?></td>
                        <td align="center" width="80"><? echo change_date_format($plan_start_array[$key]); ?>&nbsp;</td>
                        <td align="center" width="80"><? echo change_date_format($plan_finish_array[$key]); ?>&nbsp;</td>
                        <td align="center" width="80" bgcolor="<? echo $bgcolor;  ?>">
                            <? 
                                if($actual_start_array[$key]=="0000-00-00" || $actual_start_array[$key]=="") echo "&nbsp;";
                                else echo change_date_format($actual_start_array[$key]);
                            ?>
                        </td>
                        <td align="center" width="80" bgcolor="<? echo $bgcolor1;  ?>">
                            <?  
                                 if($actual_finish_array[$key]=="0000-00-00" || $actual_finish_array[$key]=="") echo "&nbsp;";
                                 else echo change_date_format($actual_finish_array[$key]);
                            ?>
                        </td>
                        <td align="center" width="80" bgcolor="<? echo $bgcolor5;  ?>">
							<?  
                                echo $start_diff." ".$start;
                            ?>
                        </td>
                        <td align="center" width="80" bgcolor="<? echo $bgcolor6;  ?>">
                            <?  
                                echo $finish_diff." ".$finish;
                            ?>
                        </td>

                        <td width="100"><p><? echo $responsible_array[$key]; ?>&nbsp;</p></td>
                        <td><p><? echo $comments_array[$key]; ?>&nbsp;</p></td>
                    </tr>
              	<? 
                    $i++;
                }
                ?>
            </table>
    </fieldset>
	<?
	exit();
}

if($action=="job_color_wise_remarks")
{
	extract($_REQUEST); 
 	echo load_html_head_contents("Remarks", "../../../", 1, 1,$unicode,'',''); 
	// $job_no=str_replace("'","",$job_no);
	$color_id=str_replace("'","",$color_id);
	$po_break_down_id = $po_no;
	$floor_library=return_library_array( "select id,floor_name from  lib_prod_floor", "id", "floor_name"  );
	$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
	$actual_resource_library=return_library_array( "select id,line_number from prod_resource_mst", "id", "line_number"  );
	$job_sql = sql_select("SELECT a.company_name,a.style_ref_no,a.buyer_name,b.po_number from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($po_break_down_id)");
	?>
    <div id="data_panel" style="width:100%;text-align: center;padding: 5px;">
		<script>
            function new_window()
            {
                var w = window.open("Surprise", "#");
                var d = w.document.open();
                d.write(document.getElementById('details_reports').innerHTML);
                d.close();
            }
        </script>
    	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
    	<span id="popup_report_container" align="center" style="width: 120px;"> </span>
    </div>
    <?
     ob_start();
	?>
    <div align="center" id="details_reports">
    <div style="width:480px">
	    <table width="100%">
	    	<tr>
	    		<th colspan="2" align="left" width="49%">Company : <? echo $company_library[$job_sql[0][csf('company_name')]];?></th>
	    		<th colspan="2" align="left" width="49%">Style : <? echo $job_sql[0][csf('style_ref_no')];?></th>
	    	</tr>
	    	<tr>
	    		<th colspan="2" align="left" width="49%">Buyer : <? echo $buyer_short_library[$job_sql[0][csf('buyer_name')]];?></th>
	    		<th colspan="2" align="left" width="49%">Po No. : <? echo $job_sql[0][csf('po_number')];?></th>
	    	</tr>
	    </table>
	</div>
    <fieldset style="width:480px">
        <legend>Cut and Lay</legend>
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="470">
                <thead>
                    <tr>
                        <td width="30">SL</td>
                        <td width="100">Date</td>
                        <td width="120">Production Qnty</td>
                        <td>Remarks</td>
                    </tr>
                </thead>
                <tbody>
				<?
                if($color_id>0) $color_cond=" and c.color_number_id=$color_id"; else $color_cond="";
               
                $sql_cutAndLay= sql_select("SELECT max(a.entry_date) as production_date ,sum(c.size_qty) as production_quantity from  ppl_cut_lay_mst a,  ppl_cut_lay_dtls b, ppl_cut_lay_bundle c where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id and a.job_no='$job_no' and b.color_id=$color_id order by production_date");
                $i=1;
                $cutTotal=0;
                foreach($sql_cutAndLay as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td align="center"><? if($row[csf("production_date")]!="" && $row[csf("production_date")]!="0000-00-00") echo change_date_format($row[csf("production_date")]); ?></td>
                        <td align="right"><? echo number_format($row[csf("production_quantity")],2); ?></td>
                        <td>&nbsp;</td>
                    </tr>
                    <?
                     $cutTotal+=$row[csf("production_quantity")];
                    $i++;
                }
                ?>
                </tbody>
                <tfoot>
                	<tr>
                		<td></td>
                		<td align="right">Total</td>
                		<td align="right"><? echo number_format($cutTotal,2); ?></td>
                		<td></td>
                	</tr>
                </tfoot>
            </table>
    </fieldset>



    	<fieldset style="width:480px">
        	<legend>Cutting</legend>
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="470">
                <thead>
                    <tr>
                        <td width="30">SL</td>
                        <td width="100">Date</td>
                        <td width="120">Production Qnty</td>
                        <td>Remarks</td>
                    </tr>
                </thead>
                <tbody>
				<?
                if($color_id>0) $color_cond=" and c.color_number_id=$color_id"; else $color_cond="";
                
                $sql_cutting= sql_select("SELECT a.production_date,a.remarks,sum(b.production_qnty) as production_quantity from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
                where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id in($po_break_down_id) $color_cond and a.production_type='1' and b.production_type='1' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1   group by a.production_date,a.remarks order by a.production_date");
                $i=1;
                $cuttingTotal = 0;
                foreach($sql_cutting as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td align="center"><? if($row[csf("production_date")]!="" && $row[csf("production_date")]!="0000-00-00") echo change_date_format($row[csf("production_date")]); ?></td>
                        <td align="right"><? echo number_format($row[csf("production_quantity")],2); ?></td>
                        <td><? echo $row[csf("remarks")]; ?></td>
                    </tr>
                    <?
                    $cuttingTotal += $row[csf("production_quantity")];
                    $i++;
                }
                ?>
                </tbody>
                <tfoot>
                	<tr>
                		<td></td>
                		<td align="right">Total</td>
                		<td align="right"><? echo number_format($cuttingTotal,2); ?></td>
                		<td></td>
                	</tr>
                </tfoot>
            </table>
        </fieldset>

        <fieldset style="width:480px">
        <legend>Print/Embr Issue</legend>
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="470">
                <thead>
                    <tr>
                        <td width="30">SL</td>
                        <td width="100">Date</td>
                        <td width="120">Production Qnty</td>
                        <td>Remarks</td>
                    </tr>
                </thead>
                <tbody>
                <?
                $sql_print_issue= sql_select("SELECT a.production_date,a.remarks,sum(b.production_qnty) as production_quantity 
                from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
                where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id in($po_break_down_id) $color_cond and a.production_type='2' and b.production_type='2' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1   group by a.production_date,a.remarks  order by a.production_date");
                $i=1;
                $embIssueTotal = 0;
                foreach($sql_print_issue as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td align="center"><? if($row[csf("production_date")]!="" && $row[csf("production_date")]!="0000-00-00") echo change_date_format($row[csf("production_date")]); ?></td>
                        <td align="right"><? echo number_format($row[csf("production_quantity")],2); ?></td>
                        <td><? echo $row[csf("remarks")]; ?></td>
                    </tr>
                    <?
                    $embIssueTotal += $row[csf("production_quantity")];
                    $i++;
                }
                ?>
                </tbody>
                <tfoot>
                	<tr>
                		<td></td>
                		<td align="right">Total</td>
                		<td align="right"><? echo number_format($embIssueTotal,2); ?></td>
                		<td></td>
                	</tr>
                </tfoot>
            </table>
        </fieldset>
        <fieldset style="width:480px">
        <legend>Print/Embr Receive</legend>
             <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="470">
                <thead>
                    <tr>
                        <td width="30">SL</td>
                        <td width="100">Date</td>
                        <td width="120">Production Qnty</td>
                        <td>Remarks</td>
                    </tr>
                </thead>
                <tbody>
                <?
                $sql_print_rcv=  sql_select("SELECT a.production_date,a.remarks,sum(b.production_qnty) as production_quantity 
                from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
                where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id in($po_break_down_id) $color_cond and a.production_type='3' and b.production_type='3' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1   group by a.production_date,a.remarks  order by a.production_date");
                
                $i=1;
                $embRcvTotal = 0;
                foreach($sql_print_rcv as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td align="center"><? if($row[csf("production_date")]!="" && $row[csf("production_date")]!="0000-00-00") echo change_date_format($row[csf("production_date")]); ?></td>
                        <td align="right"><? echo number_format($row[csf("production_quantity")],2); ?></td>
                        <td><? echo $row[csf("remarks")]; ?></td>
                    </tr>
                    <?
                    $embRcvTotal += $row[csf("production_quantity")];
                    $i++;
                }
                ?>
                </tbody>
                <tfoot>
                	<tr>
                		<td></td>
                		<td align="right">Total</td>
                		<td align="right"><? echo number_format($embRcvTotal,2); ?></td>
                		<td></td>
                	</tr>
                </tfoot>
            </table>
        </fieldset>

        <fieldset style="width:480px">
        	<legend>Sewing Input</legend>
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="470">
                <thead>
                    <tr>
                        <td width="30">SL</td>
                        <td width="70">Date</td>
                        <td width="80">Production Qnty</td>
                        <td width="100">Floor</td>
                        <td width="70">Line No</td>
                        <td>Remarks</td>
                    </tr>
                </thead>
                <tbody>
                <?
                $sql_sewing_in= sql_select("SELECT a.production_date, a.floor_id, a.prod_reso_allo, a.sewing_line, sum(b.production_qnty) as production_quantity, max(a.remarks) as remarks 
                from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
                where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id in($po_break_down_id) $color_cond and a.production_type='4' and b.production_type='4' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1   group by a.production_date, a.floor_id, a.prod_reso_allo, a.sewing_line  order by a.production_date , a.floor_id");
                $sewing_in_array = [];
                foreach ($sql_sewing_in as $key => $row) 
                {
                	if($row[csf("prod_reso_allo")]==1)
                    {
                        $sewing_ling_arr=array_unique(explode(",",$actual_resource_library[$row[csf("sewing_line")]]));
                        $all_sewing_line="";
                        foreach($sewing_ling_arr as $line_id)
                        {
                            $all_sewing_line.=$line_library[$line_id].",";
                        }
                        $all_sewing_line=chop($all_sewing_line," , ");
                        // echo $all_sewing_line;
                        $sewing_in_array[$row[csf("floor_id")]][$row[csf("production_date")]][$all_sewing_line]['production_quantity'] += $row[csf("production_quantity")];
	                	$sewing_in_array[$row[csf("floor_id")]][$row[csf("production_date")]][$all_sewing_line]['remarks'] = $row[csf("remarks")];
                    }
                    else
                    {
                        $all_sewing_line = $line_library[$row[csf("sewing_line")]];
                        $sewing_in_array[$row[csf("floor_id")]][$row[csf("production_date")]][$all_sewing_line]['production_quantity'] += $row[csf("production_quantity")];
	                	$sewing_in_array[$row[csf("floor_id")]][$row[csf("production_date")]][$all_sewing_line]['remarks'] = $row[csf("remarks")];
                    }
	                	
                }
                /*echo "<pre>";
                print_r($sewing_in_array);
                echo "</pre>";*/
                $i=1;
                $GrandTotal = 0;
                foreach($sewing_in_array as $floor_id=>$floor_data)
                {      
                	$floorTotal = 0;          	
                	foreach ($floor_data as $date => $date_data) 
                	{                		
                		foreach ($date_data as $line_name => $row) 
                		{                			                		
		                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		                    ?>
		                    <tr bgcolor="<? echo $bgcolor; ?>">
		                        <td align="center"><? echo $i; ?></td>
		                        <td align="center"><? if($date!="" && $date!="0000-00-00") echo change_date_format($date); ?></td>
		                        <td align="right"><? echo number_format($row["production_quantity"],2); ?></td>
		                        <td ><? echo $floor_library[$floor_id]; ?></td>
		                        <td style="padding-left:3px;"><? echo $line_name; ?> </td>
		                        <td><? echo $row["remarks"]; ?></td>
		                    </tr>
		                    <?
		                    $i++;
		                    $floorTotal += $row["production_quantity"];
		                    $GrandTotal += $row["production_quantity"];
                    	}                		
                	}
                	?>
                	<tr>
                		<td></td>
                		<td align="right">Total</td>
                		<td align="right"><? echo number_format($floorTotal,2); ?></td>
                		<td></td>
                		<td></td>
                		<td></td>
                	</tr>
                	<?
                }
                ?>
                	<tr>
                		<td></td>
                		<td align="right">Grand Total</td>
                		<td align="right"><? echo number_format($GrandTotal,2); ?></td>
                		<td></td>
                		<td></td>
                		<td></td>
                	</tr>
                </tbody>
            </table>
        </fieldset>

        <fieldset style="width:480px">
        	<legend >Sewing Output</legend>
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="470">
                <thead>
                    <tr>
                        <td width="30">SL</td>
                        <td width="70">Date</td>
                        <td width="80">Production Qnty</td>
                        <td width="100">Floor</td>
                        <td width="70">Line No</td>
                        <td>Remarks</td>
                    </tr>
                </thead>
                <tbody>
                <?
                $sql_sewing_out= sql_select("SELECT a.production_date, a.floor_id, a.prod_reso_allo, a.sewing_line, sum(b.production_qnty) as production_quantity, max(a.remarks) as remarks 
                from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
                where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id in($po_break_down_id) $color_cond and a.production_type='5' and b.production_type='5' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1     group by  a.production_date, a.floor_id, a.prod_reso_allo, a.sewing_line order by a.production_date , a.floor_id");
                $sewing_out_array = [];
                foreach ($sql_sewing_out as $key => $row) 
                {
                	if($row[csf("prod_reso_allo")]==1)
                    {
                        $sewing_ling_arr=array_unique(explode(",",$actual_resource_library[$row[csf("sewing_line")]]));
                        $all_sewing_line="";
                        foreach($sewing_ling_arr as $line_id)
                        {
                            $all_sewing_line.=$line_library[$line_id].",";
                        }
                        $all_sewing_line=chop($all_sewing_line," , ");
                        // echo $all_sewing_line;
                        $sewing_out_array[$row[csf("floor_id")]][$row[csf("production_date")]][$all_sewing_line]['production_quantity'] += $row[csf("production_quantity")];
	                	$sewing_out_array[$row[csf("floor_id")]][$row[csf("production_date")]][$all_sewing_line]['remarks'] = $row[csf("remarks")];
                    }
                    else
                    {
                        $all_sewing_line = $line_library[$row[csf("sewing_line")]];
                        $sewing_out_array[$row[csf("floor_id")]][$row[csf("production_date")]][$all_sewing_line]['production_quantity'] += $row[csf("production_quantity")];
	                	$sewing_out_array[$row[csf("floor_id")]][$row[csf("production_date")]][$all_sewing_line]['remarks'] = $row[csf("remarks")];
                    }
	                	
                }
                /*echo "<pre>";
                print_r($sewing_in_array);
                echo "</pre>";*/
                $i=1;
                $GrandTotal = 0;
                foreach($sewing_out_array as $floor_id=>$floor_data)
                {      
                	$floorTotal = 0;          	
                	foreach ($floor_data as $date => $date_data) 
                	{                		
                		foreach ($date_data as $line_name => $row) 
                		{                			                		
		                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		                    ?>
		                    <tr bgcolor="<? echo $bgcolor; ?>">
		                        <td align="center"><? echo $i; ?></td>
		                        <td align="center"><? if($date!="" && $date!="0000-00-00") echo change_date_format($date); ?></td>
		                        <td align="right"><? echo number_format($row["production_quantity"],2); ?></td>
		                        <td ><? echo $floor_library[$floor_id]; ?></td>
		                        <td style="padding-left:3px;"><? echo $line_name; ?> </td>
		                        <td><? echo $row["remarks"]; ?></td>
		                    </tr>
		                    <?
		                    $i++;
		                    $floorTotal += $row["production_quantity"];
		                    $GrandTotal += $row["production_quantity"];
                    	}                		
                	}
                	?>
                	<tr>
                		<td></td>
                		<td align="right">Total</td>
                		<td align="right"><? echo number_format($floorTotal,2); ?></td>
                		<td></td>
                		<td></td>
                		<td></td>
                	</tr>
                	<?
                }
                ?>
                	<tr>
                		<td></td>
                		<td align="right">Grand Total</td>
                		<td align="right"><? echo number_format($GrandTotal,2); ?></td>
                		<td></td>
                		<td></td>
                		<td></td>
                	</tr>
                </tbody>
            </table>
        </fieldset>

        <fieldset style="width:480px">
        	<legend>Poly</legend>
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="470">
                <thead>
                    <tr>
                        <td width="30">SL</td>
                        <td width="70">Date</td>
                        <td width="80">Production Qty</td>
                        <td width="100">Floor</td>
                        <td width="70">Line No</td>
                        <td>Remarks</td>
                    </tr>
                </thead>
                <tbody>
                <?
                $sql_sewing_out= sql_select("SELECT a.production_date, a.floor_id, a.prod_reso_allo, a.sewing_line, sum(b.production_qnty) as production_quantity, max(a.remarks) as remarks 
                from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
                where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id in($po_break_down_id) $color_cond and a.production_type='11' and b.production_type='11' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1   group by  a.production_date, a.floor_id, a.prod_reso_allo, a.sewing_line order by a.production_date , a.floor_id");
                $i=1;
                $polyTotal = 0;
                foreach($sql_sewing_out as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td align="center"><? if($row[csf("production_date")]!="" && $row[csf("production_date")]!="0000-00-00") echo change_date_format($row[csf("production_date")]); ?></td>
                        <td align="right"><? echo number_format($row[csf("production_quantity")],2); ?></td>
                        <td><? echo $floor_library[$row[csf("floor_id")]]; ?></td>
                        <td style="padding-left:3px;">
                        <?
                        if($row[csf("prod_reso_allo")]==1)
                        {
                            $sewing_ling_arr=array_unique(explode(",",$actual_resource_library[$row[csf("sewing_line")]]));
                            $all_sewing_line="";
                            foreach($sewing_ling_arr as $line_id)
                            {
                                $all_sewing_line.=$line_library[$line_id].",";
                            }
                            $all_sewing_line=chop($all_sewing_line," , ");
                            echo $all_sewing_line;
                        }
                        else
                        {
                            echo $line_library[$row[csf("sewing_line")]];
                        }
                        ?>
                        </td>
                        <td><? echo $row[csf("remarks")]; ?></td>
                    </tr>
                    <?
                    $polyTotal += $row[csf("production_quantity")];
                    $i++;
                }
                ?>
                </tbody>
                <tfoot>
                	<tr>
                		<td></td>
                		<td align="right">Total</td>
                		<td align="right"><? echo number_format($polyTotal,2); ?></td>
                		<td></td>
                		<td></td>
                		<td></td>
                	</tr>
                </tfoot>
            </table>
        </fieldset>

        <fieldset style="width:480px">
        	<legend>Finishing & Packing</legend>
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="470">
                <thead>
                    <tr>
                        <td width="30">SL</td>
                        <td width="100">Date</td>
                        <td width="100">Production Qnty</td>
                        <td width="100">Floor</td>
                        <td>Remarks</td>
                    </tr>
                </thead>
                <tbody>
                <?
                $sql_finish_out= sql_select("SELECT a.production_date, a.floor_id, sum(b.production_qnty) as production_quantity, max(a.remarks) as remarks 
                from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
                where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id in($po_break_down_id) $color_cond and a.production_type='8' and b.production_type='8' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1    group by a.production_date,a.floor_id order by a.production_date , a.floor_id");
                $i=1;
                $finTotal = 0;
                foreach($sql_finish_out as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td align="center"><? if($row[csf("production_date")]!="" && $row[csf("production_date")]!="0000-00-00") echo change_date_format($row[csf("production_date")]); ?></td>
                        <td align="right"><? echo number_format($row[csf("production_quantity")],2); ?></td>
                        <td ><? echo $floor_library[$row[csf("floor_id")]]; ?></td>
                        <td><? echo $row[csf("remarks")]; ?></td>
                    </tr>
                    <?
                    $i++;
                    $finTotal += $row[csf("production_quantity")];
                }
                ?>
                </tbody>
                <tfoot>
                	<tr>
                		<td></td>
                		<td align="right">Total</td>
                		<td align="right"><? echo number_format($finTotal,2); ?></td>
                		<td></td>
                		<td></td>
                	</tr>
                </tfoot>
            </table>
        </fieldset>
    <?
	$html=ob_get_contents();
	ob_flush();
	
	foreach (glob(""."*.xls") as $filename) 
	{
	   @unlink($filename);
	}
	
	//html to xls convert
	$name=time();
	$name=$user_id."_".$name.".xls";
	$create_new_excel = fopen(''.$name, 'w');	
	$is_created = fwrite($create_new_excel,$html);	
	?>
    <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
    <script>
		$(document).ready(function(e) 
		{
			document.getElementById('popup_report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Convert to Excel" name="excel" id="excel" style="padding:0 2px;" class="formbutton"/></a>&nbsp;&nbsp;';
		});	
	</script>
	</div>  
	<?
	exit();
}

if ($action=='JobColorOrderPopup')
{
	echo load_html_head_contents("Order Wise Production Report", "../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	
	$po_break_down_id=$po_no;
	$company_name=str_replace("'","",$company_name);
	$color_id=str_replace("'","",$color_id);
	
	//echo $po_break_down_id."****".$company_name."****".$item_id."****".$country_id."****".$color_id;die;
	$sql_exfect="SELECT c.color_number_id, c.size_number_id, sum(b.production_qnty) as production_qnty from pro_ex_factory_mst a,pro_ex_factory_dtls b, wo_po_color_size_breakdown c where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id in($po_break_down_id) and c.color_number_id='$color_id' and a.status_active=1 and a.is_deleted=0 group by  c.color_number_id, c.size_number_id";
	$sql_result_exfact=sql_select($sql_exfect);
	foreach($sql_result_exfact as $row)
	{
		$ex_fact_qty_arr[$row[csf("color_number_id")]][$row[csf("size_number_id")]]=$row[csf("production_qnty")];
	}
	
	$color_library=array(); 
	$size_library=array(); 
	$color_library_plan=array(); 
	$dataQty=array();
	$colorSizeData=sql_select("SELECT id, color_number_id, size_number_id, order_quantity, plan_cut_qnty, excess_cut_perc from wo_po_color_size_breakdown where po_break_down_id in ($po_break_down_id) and color_number_id='$color_id' and status_active=1 and is_deleted=0");
 	  foreach($colorSizeData as $csRow)
	  {
		  if($csRow[csf('color_number_id')]>0)
		  {
			  $color_library[$csRow[csf('color_number_id')]]+=$csRow[csf('order_quantity')];
			  $color_library_plan[$csRow[csf('color_number_id')]]+=$csRow[csf('plan_cut_qnty')];
		  }
		  
		  if($csRow[csf('size_number_id')]>0)
		  {
			  $size_library[$csRow[csf('size_number_id')]]=$csRow[csf('size_number_id')];
		  }
		  
		  $dataQty[$csRow[csf('color_number_id')]][$csRow[csf('size_number_id')]][1]+=$csRow[csf('order_quantity')];
		  $dataQty[$csRow[csf('color_number_id')]][$csRow[csf('size_number_id')]][2]+=$csRow[csf('plan_cut_qnty')];
		  $dataQty[$csRow[csf('color_number_id')]][$csRow[csf('size_number_id')]][3]+=$csRow[csf('excess_cut_perc')];
		  $dataQty[$csRow[csf('color_number_id')]][$csRow[csf('size_number_id')]][4]+=1;
		  $color_size_data[$csRow[csf("id")]]["color_number_id"]=$csRow[csf('color_number_id')];
		  $color_size_data[$csRow[csf("id")]]["size_number_id"]=$csRow[csf('size_number_id')];
	  }
	
	
	
	
	if($db_type==0)
	{
		$prod_sql= sql_select("SELECT a.po_break_down_id, c.color_size_break_down_id,  
				IFNULL(sum(CASE WHEN a.production_type ='1' THEN c.production_qnty  ELSE 0 END),0) AS cutting_qnty,
				IFNULL(sum(CASE WHEN a.production_type ='2' AND a.embel_name=1 THEN c.production_qnty ELSE 0 END),0) AS printing_qnty,
				IFNULL(sum(CASE WHEN a.production_type ='3' AND a.embel_name=1 THEN c.production_qnty ELSE 0 END),0) AS printreceived_qnty, 
				IFNULL(sum(CASE WHEN a.production_type ='2' AND a.embel_name=2 THEN c.production_qnty ELSE 0 END),0) AS emb_qnty,
				IFNULL(sum(CASE WHEN a.production_type ='3' AND a.embel_name=2 THEN c.production_qnty ELSE 0 END),0) AS embreceived_qnty, 
				IFNULL(sum(CASE WHEN a.production_type ='2' AND a.embel_name=3 THEN c.production_qnty ELSE 0 END),0) AS wash_qnty,
				IFNULL(sum(CASE WHEN a.production_type ='3' AND a.embel_name=3 THEN c.production_qnty ELSE 0 END),0) AS washreceived_qnty, 
				IFNULL(sum(CASE WHEN a.production_type ='2' AND a.embel_name=4 THEN c.production_qnty ELSE 0 END),0) AS sp_qnty,
				IFNULL(sum(CASE WHEN a.production_type ='3' AND a.embel_name=4 THEN c.production_qnty ELSE 0 END),0) AS spreceived_qnty, 
				IFNULL(sum(CASE WHEN a.production_type ='4' THEN c.production_qnty ELSE 0 END),0) AS sewingin_qnty,
				IFNULL(sum(CASE WHEN a.production_type ='5' THEN c.production_qnty ELSE 0 END),0) AS sewingout_qnty,
				IFNULL(sum(CASE WHEN a.production_type ='6' THEN c.production_qnty ELSE 0 END),0) AS finishin_qnty, 
				IFNULL(sum(CASE WHEN a.production_type ='7' THEN c.production_qnty ELSE 0 END),0) AS iron_qnty,
				IFNULL(sum(CASE WHEN a.production_type ='8' THEN c.production_qnty ELSE 0 END),0) AS finish_qnty 
			from 
				pro_garments_production_mst a, pro_garments_production_dtls c
			where  
				a.id=c.mst_id and a.status_active=1 and c.status_active=1 and a.po_break_down_id in($po_break_down_id) group by a.po_break_down_id,c.color_size_break_down_id");
				/*IFNULL(sum(CASE WHEN c.production_type ='2' THEN c.production_qnty ELSE 0 END),0) AS printing_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='3' THEN c.production_qnty ELSE 0 END),0) AS printreceived_qnty, */
	}
	else
	{
		 $pr_sql="SELECT a.po_break_down_id, c.color_size_break_down_id, 
				NVL(sum(CASE WHEN a.production_type ='1' THEN c.production_qnty  ELSE 0 END),0) AS cutting_qnty,
				NVL(sum(CASE WHEN a.production_type ='2' AND a.embel_name=1 THEN c.production_qnty ELSE 0 END),0) AS printing_qnty,
				NVL(sum(CASE WHEN a.production_type ='3' AND a.embel_name=1 THEN c.production_qnty ELSE 0 END),0) AS printreceived_qnty,
				NVL(sum(CASE WHEN a.production_type ='2' AND a.embel_name=2 THEN c.production_qnty ELSE 0 END),0) AS emb_qnty,
				NVL(sum(CASE WHEN a.production_type ='3' AND a.embel_name=2 THEN c.production_qnty ELSE 0 END),0) AS embreceived_qnty, 
				NVL(sum(CASE WHEN a.production_type ='2' AND a.embel_name=3 THEN c.production_qnty ELSE 0 END),0) AS wash_qnty,
				NVL(sum(CASE WHEN a.production_type ='3' AND a.embel_name=3 THEN c.production_qnty ELSE 0 END),0) AS washreceived_qnty, 
				NVL(sum(CASE WHEN a.production_type ='2' AND a.embel_name=4 THEN c.production_qnty ELSE 0 END),0) AS sp_qnty,
				NVL(sum(CASE WHEN a.production_type ='3' AND a.embel_name=4 THEN c.production_qnty ELSE 0 END),0) AS spreceived_qnty, 
				NVL(sum(CASE WHEN a.production_type ='4' THEN c.production_qnty ELSE 0 END),0) AS sewingin_qnty,
				NVL(sum(CASE WHEN a.production_type ='5' THEN c.production_qnty ELSE 0 END),0) AS sewingout_qnty,
				NVL(sum(CASE WHEN a.production_type ='6' THEN c.production_qnty ELSE 0 END),0) AS finishin_qnty, 
				NVL(sum(CASE WHEN a.production_type ='7' THEN c.production_qnty ELSE 0 END),0) AS iron_qnty,
				NVL(sum(CASE WHEN a.production_type ='8' THEN c.production_qnty ELSE 0 END),0) AS finish_qnty
			from 
				pro_garments_production_mst a, pro_garments_production_dtls c
			where  
				a.id=c.mst_id and a.status_active=1 and c.status_active=1 and a.po_break_down_id in($po_break_down_id)
			group by a.po_break_down_id,c.color_size_break_down_id";
			$prod_sql=sql_select($pr_sql);
				
				
	}
	//echo $prod_sql;
	$prod_color_size_data=array();
	foreach($prod_sql as $row)
	{
		$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["cutting_qnty"]+=$row[csf("cutting_qnty")];
		$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["printing_qnty"]+=$row[csf("printing_qnty")];
		$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["printreceived_qnty"]+=$row[csf("printreceived_qnty")];
		$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["emb_qnty"]+=$row[csf("emb_qnty")];
		$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["embreceived_qnty"]+=$row[csf("embreceived_qnty")];
		$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["wash_qnty"]+=$row[csf("wash_qnty")];
		$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["washreceived_qnty"]+=$row[csf("washreceived_qnty")];
		$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["sp_qnty"]+=$row[csf("sp_qnty")];
		$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["spreceived_qnty"]+=$row[csf("spreceived_qnty")];
		$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["sewingin_qnty"]+=$row[csf("sewingin_qnty")];
		$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["sewingout_qnty"]+=$row[csf("sewingout_qnty")];
		$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["finishin_qnty"]+=$row[csf("finishin_qnty")];
		$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["iron_qnty"]+=$row[csf("iron_qnty")];
		$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["finish_qnty"]+=$row[csf("finish_qnty")];
		//$color_library[$row[csf("color_number_id")]]=$row[csf("color_number_id")];
		//$size_library[$row[csf("size_number_id")]]=$row[csf("size_number_id")];
	}
	//var_dump($color_library);die;
	
	?>
	 <div id="data_panel" align="center" style="width:100%">
	         <script>
			 	function new_window()
				 {
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				 }
	         </script>
	 	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
	 </div>
	  
	<div style="width:700px" align="center" id="details_reports"> 
	  	<legend>Color And Size Wise Summary</legend>
	    <table id="tbl_id" class="rpt_table" width="700" border="1" rules="all" >
	    	<thead>
	        	<tr>
	            	<th width="70">Buyer</th>
	                <th width="100">Job Number</th>
	                <th width="120">Style Name</th>
	                <th width="100">Order Number</th>
	                <th width="70">Ship Date</th>
	                <th width="150">Item Name</th>
	                <th >Order Qty.</th>
	            </tr>
	        </thead>
	       	<?
				$sql = "SELECT a.job_no_mst, a.po_number as po_number,a.pub_shipment_date as pub_shipment_date,a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,b.total_set_qnty as set_item_ratio,c.item_number_id, sum(c.order_quantity) as po_quantity
						from wo_po_break_down a, wo_po_details_master b, wo_po_color_size_breakdown c
						where a.job_no_mst=b.job_no and a.id=c.po_break_down_id and b.job_no='$job_no' and a.id in($po_break_down_id) and c.color_number_id='$color_id' and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active=1 and c.status_active in(1,2,3) and b.is_deleted=0 
						group by a.job_no_mst, a.po_number,a.pub_shipment_date,a.pub_shipment_date,a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,b.total_set_qnty,c.item_number_id";
				//echo $sql;die;
				$resultRow=sql_select($sql);
					
				$cons_embr=return_field_value("sum(cons_dzn_gmts) as cons_dzn_gmts","wo_pre_cost_embe_cost_dtls","job_no='$job_no' and status_active=1 and is_deleted=0","cons_dzn_gmts");
				
	 		?> 
	        <tbody>
	        	<?
	        	foreach ($resultRow as $key => $row) 
	        	{  
	        	?>
	        	<tr>
	                <td><p><? echo $buyer_short_library[$row[csf("buyer_name")]]; ?></p></td>
	                <td><p><? echo $job_no; ?></p></td>
	                <td><p><? echo $row[csf("style_ref_no")]; ?></p></td>
	                <td><p><? echo implode(",",array_unique(explode(",",$row[csf("po_number")]))); ?></p></td>
	                <td align="center"><? echo change_date_format($row[csf("pub_shipment_date")]); ?></td>
	                <td><? echo $garments_item[$row[csf('item_number_id')]]; ?></td>
	                <td align="right"><? echo $row[csf("po_quantity")]*$row[csf("set_item_ratio")]; ?></td>
	            </tr>
	            <?
	        	}
				$prod_sewing_sql=sql_select("SELECT sum(alter_qnty) as alter_qnty, sum(reject_qnty) as reject_qnty from pro_garments_production_mst where production_type=5 and po_break_down_id in($po_break_down_id) and is_deleted=0 and status_active=1");
				foreach ($prod_sewing_sql as $key => $val) {
					
				?>
	            <tr>
	                <td colspan="2">Total Alter Sewing Qty : <b><? echo $val[csf("alter_qnty")]; ?></b></td>
	                <td colspan="2">Total Reject Sewing Qty : <b><? echo  $val[csf("reject_qnty")]; ?></b></td>
	                <td colspan="2">Pack Assortment: <b><? //echo $packing[$resultRow[csf("packing")]]; ?></b></td>
	            </tr>
	            <?
	        	}
	            ?>
	        </tbody>
	    </table>
	    <?
					  
		  $size_Arr_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
		  //$color_library_sql;
		  if($country_id>0) $contry_cond=" and country_id='$country_id'"; else $contry_cond="";
		
		  $count = count($size_library);	
		  $width= $count*70+350; 		
		  	  
		?>
	    <table id="tblDtls_id" class="rpt_table" width="<? echo $width; ?>" border="1" rules="all" >
		 	<thead>
	        	<tr>
	            	<th width="100">Color Name</th>
	                <th width="170">Production Type</th>
	 				<?
					foreach($size_library as $val)
					{
					 	?><th width="80"><? echo $size_Arr_library[$val]; ?></th><?
					}
					?>
	     		    <th width="60">Total</th>
	           </tr>
	        </thead>
	        <?
			  foreach($color_library as $colorId=>$totalorderqnty)
			  {
				  if($color_variable_setting==2 || $color_variable_setting==3) $row_span=17; else $row_span=16;  
				?>	  
				<tr>

					<td rowspan="<? echo $row_span; ?>"><? echo $color_Arr_library[$color_id]; ?></td>
				
	 			<?            	 
					
	 					$bgcolor1="#E9F3FF"; 
						$bgcolor2="#FFFFFF";
					?>
						</tr>
	                    
	                    <tr bgcolor="<? echo $bgcolor1; ?>">
	                        <td><b>Order Quantity</b></td>	
	                        <? 
	                        foreach($size_library as $sizeId=>$sizeRes)
	                        {
	                        ?>	
	                            <td><? echo $dataQty[$colorId][$sizeId][1]; ?></td>
	                        <? 
	                        } 
	                        ?>
	                        <td><? echo $totalorderqnty; ?></td> 
	                    </tr>
						
	                    <tr bgcolor="<? echo $bgcolor2; ?>">
	                        <td><b>Plan To Cut (AVG <? echo number_format($dataQty[$colorId][$sizeId][3]/$dataQty[$colorId][$sizeId][4],2); ?>)% </b></td>	
	                        <? 
	                        foreach($size_library as $sizeId=>$sizeRes)
	                        {
	                        ?>	
	                            <td title="Excess Cut <? echo $dataQty[$colorId][$sizeId][3]; ?>%"><? echo $dataQty[$colorId][$sizeId][2]; ?></td>
	                        <? 
	                        } 
	                        ?>
	                        <td><? echo $color_library_plan[$colorId]; ?></td> 
	                    </tr>
						<?
	 				$total_cutting=0;$total_sew_in=0;$total_sew_out=0;$total_fin_in=0;$total_fin_out=0;$total_iron_out=0; $total_exfact_qnty=0;
					$total_print_issue=0;$total_print_rcv=0;$total_embro_issue=0;$total_embro_rcv=0; $total_sp_issue=0;$total_sp_rcv=0; $total_wash_issue=0;$total_wash_rcv=0;
					$cutting_html='';$sewin_html='';$sewout_html='';$finisin_html='';$finisout_html='';$iron_html=''; $exfact_html='';
					$printiss_html=''; $printrcv_html=''; $embroiss_html=''; $embrorcv_html=''; $spiss_html=''; $sprcv_html=''; $washiss_html=''; $washrcv_html='';
					foreach($size_library as $sizeId=>$sizeRes)
					{ 
						$cutting_qnty=$prod_color_size_data[$colorId][$sizeId]['cutting_qnty'];
						$printing_qnty=$prod_color_size_data[$colorId][$sizeId]['printing_qnty'];
						$printreceived_qnty=$prod_color_size_data[$colorId][$sizeId]['printreceived_qnty'];
						$emb_qnty=$prod_color_size_data[$colorId][$sizeId]['emb_qnty'];
						$embreceived_qnty=$prod_color_size_data[$colorId][$sizeId]['embreceived_qnty'];
						$wash_qnty=$prod_color_size_data[$colorId][$sizeId]['wash_qnty'];
						$washreceived_qnty=$prod_color_size_data[$colorId][$sizeId]['washreceived_qnty'];
						$sp_qnty=$prod_color_size_data[$colorId][$sizeId]['sp_qnty'];
						$spreceived_qnty=$prod_color_size_data[$colorId][$sizeId]['spreceived_qnty'];
						$sewingin_qnty=$prod_color_size_data[$colorId][$sizeId]['sewingin_qnty'];
						$sewingout_qnty=$prod_color_size_data[$colorId][$sizeId]['sewingout_qnty'];
						$finishin_qnty=$prod_color_size_data[$colorId][$sizeId]['finishin_qnty'];
						$iron_qnty=$prod_color_size_data[$colorId][$sizeId]['iron_qnty'];
						$finish_qnty=$prod_color_size_data[$colorId][$sizeId]['finish_qnty'];
						
						$resRow[csf($col)]=$dataQty[$colorId][$sizeId][2];
	                    if($cutting_qnty==0)$bgCol="bgcolor='#FF0000'"; 
						else if($cutting_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($cutting_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'"; 
						$cutting_html .='<td '.$bgCol.'>'.$cutting_qnty.'</td>';
	                    $total_cutting+=$cutting_qnty;
	                 	
						if($cons_embr>0)
						{
							if($printing_qnty==0)$bgCol="bgcolor='#FF0000'"; 
							else if($printing_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
							else if($printing_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						}
						else $bgCol='';
	                    $printiss_html .='<td '.$bgCol.'>'.$printing_qnty.'</td>';
	                    $total_print_issue+=$printing_qnty;
	                    
						if($cons_embr>0)
						{
							if($printreceived_qnty==0)$bgCol="bgcolor='#FF0000'"; 
							else if($printreceived_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
							else if($printreceived_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						}
						else $bgCol='';
						
	                    $printrcv_html .='<td '.$bgCol.'>'.$printreceived_qnty.'</td>';
	                    $total_print_rcv+=$printreceived_qnty;
						
						if($cons_embr>0)
						{
							if($emb_qnty==0)$bgCol="bgcolor='#FF0000'"; 
							else if($emb_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
							else if($emb_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						}
						else $bgCol='';
	                    $embroiss_html .='<td '.$bgCol.'>'.$emb_qnty.'</td>';
	                    $total_embro_issue+=$emb_qnty;
	                    
						if($cons_embr>0)
						{
							if($embreceived_qnty==0)$bgCol="bgcolor='#FF0000'"; 
							else if($embreceived_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
							else if($embreceived_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						}
						else $bgCol='';
						
	                    $embrorcv_html .='<td '.$bgCol.'>'.$embreceived_qnty.'</td>';
	                    $total_embro_rcv+=$embreceived_qnty;
						
						if($cons_embr>0)
						{
							if($sp_qnty==0)$bgCol="bgcolor='#FF0000'"; 
							else if($sp_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
							else if($sp_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						}
						else $bgCol='';
	                    $spiss_html .='<td '.$bgCol.'>'.$sp_qnty.'</td>';
	                    $total_sp_issue+=$sp_qnty;
	                    
						if($cons_embr>0)
						{
							if($spreceived_qnty==0)$bgCol="bgcolor='#FF0000'"; 
							else if($spreceived_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
							else if($spreceived_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						}
						else $bgCol='';
						
	                    $sprcv_html .='<td '.$bgCol.'>'.$spreceived_qnty.'</td>';
	                    $total_sp_rcv+=$spreceived_qnty;
						
						if($cons_embr>0)
						{
							if($wash_qnty==0)$bgCol="bgcolor='#FF0000'"; 
							else if($wash_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
							else if($wash_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						}
						else $bgCol='';
	                    $washiss_html .='<td '.$bgCol.'>'.$wash_qnty.'</td>';
	                    $total_wash_issue+=$wash_qnty;
	                    
						if($cons_embr>0)
						{
							if($washreceived_qnty==0)$bgCol="bgcolor='#FF0000'"; 
							else if($washreceived_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
							else if($washreceived_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						}
						else $bgCol='';
						
	                    $washrcv_html .='<td '.$bgCol.'>'.$washreceived_qnty.'</td>';
	                    $total_wash_rcv+=$washreceived_qnty;
	                    
						if($sewingin_qnty==0)$bgCol="bgcolor='#FF0000'"; 
						else if($sewingin_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($sewingin_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'"; 
	                    $sewin_html .='<td '.$bgCol.'>'.$sewingin_qnty.'</td>';
	                    $total_sew_in+=$sewingin_qnty;
	                    
						if($sewingout_qnty==0)$bgCol="bgcolor='#FF0000'"; 
						else if($sewingout_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($sewingout_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";  
	                    $sewout_html .='<td '.$bgCol.'>'.$sewingout_qnty.'</td>';
	                    $total_sew_out+=$sewingout_qnty;
	                    
						/*if($prodRow[csf("finishin_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
						else if($prodRow[csf("finishin_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($prodRow[csf("finishin_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'"; 
	                    $finisin_html .='<td '.$bgCol.'>'.$prodRow[csf("finishin_qnty")].'</td>';
	                    $total_fin_in+=$prodRow[csf("finishin_qnty")];*/
	                    
						if($finish_qnty==0)$bgCol="bgcolor='#FF0000'"; 
						else if($finish_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($finish_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'"; 
	                    $finisout_html .='<td '.$bgCol.'>'.$finish_qnty.'</td>';
	                    $total_fin_out+=$finish_qnty;
						
						if($iron_qnty==0)$bgCol="bgcolor='#FF0000'"; 
						else if($iron_qnty < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						else if($iron_qnty >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'"; 
	                    $iron_html .='<td '.$bgCol.'>'.$iron_qnty.'</td>';
	                    $total_iron_out+=$iron_qnty;
						
						//if($prodRow[csf("iron_qnty")]==0)$bgCol="bgcolor='#FF0000'"; 
						//else if($prodRow[csf("iron_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'"; 
						//else if($prodRow[csf("iron_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
						if($color_variable_setting==2 || $color_variable_setting==3)
						{ 
							$bgCol=="bgcolor='#FFFFFF'";
							$exfact_html.='<td>'.$ex_fact_qty_arr[$colorId][$sizeId].'&nbsp;</td>';
							
							$total_exfact_qnty+=$ex_fact_qty_arr[$colorId][$sizeId];
						}
						
	 				 
					}// end size foreach loop		
					
					?>
						<tr bgcolor="<? echo $bgcolor1; ?>">
	                    	<td><b>Cutting</b></td>
	                        <? echo $cutting_html; ?> 
	                        <td><? echo $total_cutting; ?></td> 
	                    </tr>
	                    <tr bgcolor="<? echo $bgcolor2; ?>">
	                    	<td><b>Print Issue</b></td>
	                        <? echo $printiss_html; ?> 
	                        <td><? echo $total_print_issue; ?></td> 
	                    </tr>
	                    <tr bgcolor="<? echo $bgcolor1; ?>">
	                    	<td><b>Print Received</b></td>
	                        <? echo $printrcv_html; ?> 
	                        <td><? echo $total_print_rcv; ?></td> 
	                    </tr>
	                    <tr bgcolor="<? echo $bgcolor2; ?>">
	                    	<td><b>Embro Issue</b></td>
	                        <? echo $embroiss_html; ?> 
	                        <td><? echo $total_embro_issue; ?></td> 
	                    </tr>
	                    <tr bgcolor="<? echo $bgcolor1; ?>">
	                    	<td><b>Embro Received</b></td>
	                        <? echo $embrorcv_html; ?> 
	                        <td><? echo $total_embro_rcv; ?></td> 
	                    </tr>
	                    <tr bgcolor="<? echo $bgcolor2; ?>">
	                    	<td><b>Issue For Special Works</b></td>
	                        <? echo $spiss_html; ?> 
	                        <td><? echo $total_sp_issue; ?></td> 
	                    </tr>
	                    <tr bgcolor="<? echo $bgcolor1; ?>">
	                    	<td><b>Recv. From Special Works</b></td>
	                        <? echo $sprcv_html; ?> 
	                        <td><? echo $total_sp_rcv; ?></td> 
	                    </tr>
	                    <tr bgcolor="<? echo $bgcolor2; ?>">
	                    	<td><b>Sewing Input</b></td>
	                       <? echo $sewin_html; ?> 
	                        <td><? echo $total_sew_in; ?></td> 
	                    </tr>
	                    <tr bgcolor="<? echo $bgcolor1; ?>">
	                    	<td><b>Sewing Output</b></td>
	                        <? echo $sewout_html; ?> 
	                        <td><? echo $total_sew_out; ?></td> 
	                    </tr>
	                    <tr bgcolor="<? echo $bgcolor2; ?>">
	                    	<td><b>Issue For Wash</b></td>
	                        <? echo $washiss_html; ?> 
	                        <td><? echo $total_wash_issue; ?></td> 
	                    </tr>
	                    <tr bgcolor="<? echo $bgcolor1; ?>">
	                    	<td><b>Recv. From Wash</b></td>
	                        <? echo $washrcv_html; ?> 
	                        <td><? echo $total_wash_rcv; ?></td> 
	                    </tr>
	                    <tr bgcolor="<? echo $bgcolor2; ?>">
	                    	<td><b>Iron Output</b></td>
	                        <? echo $iron_html; ?> 
	                        <td><? echo $total_iron_out; ?></td> 
	                    </tr>
	                   <tr bgcolor="<? echo $bgcolor1; ?>">
	                    	<td><b>Finishing Output</b></td>
	                       <? echo $finisout_html; ?> 
	                        <td><? echo $total_fin_out; ?></td> 
	                    </tr>
	                    <? 
						if($color_variable_setting==2 || $color_variable_setting==3)
						{
							?>
							<tr>
								<td><b>Ex-Factory Qty.</b></td>
								 <? echo $exfact_html; ?> 
								<td><? echo $total_exfact_qnty; ?>&nbsp;</td> 
							</tr>
							<?
						}
						?>
				<?	
				}// end color foreach loop
				?>
	           
			 
	 </table>
	</div>    


	<?
	exit();

}


if ($action=="JobColorProdPopup") 
{
	
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	// echo $po_no;
	$company_name=str_replace("'","",$company_name);
	$color_id=str_replace("'","",$color_id);
	// $prod_type=str_replace("'","",$prod_type);	
	$po_break_down_id = $po_no;	
    $prod_popup_type=str_replace("'","",$prod_popup_type);
	$prod_popup_lelel=str_replace("'","",$prod_popup_lelel);
	$prod_popup_lelel_arr=explode('*',$prod_popup_lelel);
	$prod_popup_lelel_type=$prod_popup_lelel_arr[0];  // source (inhouse / subcon)
	$prod_popup_lelel_cat=$prod_popup_lelel_arr[1]; // embel name
	
	if($db_type==0) $rmg_process_breakdown=return_field_value("a.rmg_process_breakdown as rmg_process_breakdown"," wo_booking_mst a, wo_booking_dtls b","a.booking_no=b.booking_no and  b.job_no='$job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id asc limit 1","rmg_process_breakdown");
	else if($db_type==2) $rmg_process_breakdown=return_field_value("rmg_process_breakdown"," (select a.id, a.rmg_process_breakdown  from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.job_no='$job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id asc)","ROWNUM = 1","rmg_process_breakdown");
	// print_r($rmg_process_breakdown);
	if($rmg_process_breakdown!="")
	{
		$rmg_process_breakdown_arr=explode("_",$rmg_process_breakdown);
		//var_dump($rmg_process_breakdown_arr);die;
		$cut_panel_rejection=$rmg_process_breakdown_arr[8];
		$chest_printing=$rmg_process_breakdown_arr[2];
		$neck_sleeve_printing=$rmg_process_breakdown_arr[10];
		$embroidery=$rmg_process_breakdown_arr[1];
		$sewing_input=$rmg_process_breakdown_arr[4];
		$garments_wash=$rmg_process_breakdown_arr[3];
		$gmts_finishing=$rmg_process_breakdown_arr[15];
	}
	
	
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$size_Arr_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where job_no_mst='$job_no' and color_number_id=$color_id order by size_order","size_number_id","size_number_id");
	$line_Arr_library=return_library_array( "select id,line_name from lib_sewing_line where  is_deleted=0 and status_active=1", "id", "line_name" );
	$resource_alocate_line = return_library_array("select id, line_number from prod_resource_mst", "id", "line_number");
	// print_r($sizearr_order);
	$sql_order_size= sql_select("SELECT c.size_number_id, c.order_quantity as order_quantity, c.plan_cut_qnty as plan_cut_qnty
		from wo_po_color_size_breakdown c 
		where c.status_active=1 and c.is_deleted=0 and c.job_no_mst='$job_no' and c.color_number_id=$color_id  
		 order by c.size_order");
	// print_r($sql_order_size);
	foreach($sql_order_size as $row)
	{
		$order_size_qnty[$row[csf('size_number_id')]] +=$row[csf('order_quantity')];
		$plan_order_size_qnty[$row[csf('size_number_id')]] +=$row[csf('plan_cut_qnty')];
	}
		
	$color_size_sql=sql_select( "SELECT a.job_no_mst as job_no,a.id, a.color_number_id, a.size_number_id from wo_po_color_size_breakdown a where   a.color_number_id>0 and a.size_number_id>0 and a.job_no_mst='$job_no' and a.color_number_id=$color_id order by a.color_order");
	$color_size_data=array(); $allcolor_id_arr=array();
	foreach($color_size_sql as $row)
	{
		$allcolor_id_arr[$row[csf("color_number_id")]]=$row[csf("color_number_id")];
		$color_size_data[$row[csf("id")]]["color_number_id"]=$row[csf("color_number_id")];
		$color_size_data[$row[csf("id")]]["size_number_id"]=$row[csf("size_number_id")];
	}
	// print_r($color_size_data);
	$sql_supplier=sql_select("select id,supplier_name from lib_supplier where status_active =1 and is_deleted=0 order by supplier_name");
	$supplier_arr=array();
	foreach($sql_supplier as $row)
	{
		$supplier_arr[$row[csf('id')]]=$row[csf('supplier_name')];
	}

	//echo "<pre>";
	//print_r($color_size_data); die;
	
				
		
 		if($prod_popup_type==10)
		{
			$serving_company_id = [];
			$sql = "SELECT serving_company FROM pro_garments_production_mst  WHERE po_break_down_id in($po_break_down_id)  group by serving_company";
			$sql_res = sql_select($sql);
			// echo $sql_res[0][csf('serving_company')];
			$sql_colsiz="SELECT b.color_size_break_down_id,d.color_number_id,d.size_number_id,
			sum(CASE WHEN a.entry_form!=85 THEN b.production_qnty ELSE 0 END)-sum(CASE WHEN a.entry_form=85 THEN b.production_qnty ELSE 0 END) as production_qnty
			from pro_ex_factory_mst a,  pro_ex_factory_dtls b, wo_po_break_down c ,wo_po_color_size_breakdown d
			where a.id=b.mst_id and a.po_break_down_id in($po_break_down_id) and d.color_number_id=$color_id  and c.id=d.po_break_down_id and d.id=b.color_size_break_down_id and a.po_break_down_id = c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active in(1,2,3) and d.status_active in(1,2,3) and d.is_deleted=0  group by b.color_size_break_down_id,d.color_number_id,d.size_number_id";

			$ex_fac_level=sql_select("select ex_factory  from variable_settings_production where company_name in(select a.company_name from  wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and a.job_no='$job_no' and b.status_active in(1,2,3)) and variable_list=1");
			if($ex_fac_level[0][csf("ex_factory")]==1)
			{  
				$sql_colsiz="SELECT 
				sum(CASE WHEN entry_form!=85 THEN  ex_factory_qnty ELSE 0 END)-sum(CASE WHEN entry_form=85 THEN  ex_factory_qnty ELSE 0 END) as production_qnty
				from pro_ex_factory_mst 
				where po_break_down_id in($po_break_down_id) and status_active=1 and is_deleted=0";
			}
		}
		else if($prod_popup_type==11)
		{
			$sql_colsiz="SELECT e.color_number_id,e.size_number_id ,b.color_size_break_down_id, sum(b.production_qnty) as production_qnty,a.serving_company,a.sewing_line  
			from pro_garments_production_mst a, pro_garments_production_dtls b ,wo_po_color_size_breakdown e
			where a.id=b.mst_id    and e.id=b.color_size_break_down_id    and a.production_type='$prod_popup_type' and b.production_type='$prod_popup_type'  and a.po_break_down_id in($po_break_down_id) and e.color_number_id=$color_id and a.production_source=$prod_popup_lelel_type and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 group by e.color_number_id,e.size_number_id , b.color_size_break_down_id,a.serving_company,a.sewing_line order by a.serving_company";
		}
		else if($prod_popup_type==17)
		{
  			$sql_colsiz="SELECT b.color_id  as color_number_id,c.size_id as size_number_id ,sum(c.size_qty ) as production_qnty,a.working_company_id as serving_company  from ppl_cut_lay_mst a ,ppl_cut_lay_dtls b,ppl_cut_lay_bundle c where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and    c.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.job_no='$job_no' and b.color_id=$color_id and (c.order_id in($po_break_down_id) or b.order_id in($po_break_down_id))  group by b.color_id,c.size_id,a.working_company_id order by a.working_company_id";
 			 
			
			foreach(sql_select($sql_colsiz) as $vals)
			{
				$col_size_wise_lay[$vals[csf("color_number_id")]][$vals[csf("size_number_id")]]+=$vals[csf("production_qnty")];
				if($vals[csf("serving_company")])
				{
					$col_size_wise_lay_company[$vals[csf("color_number_id")]][$vals[csf("size_number_id")]] =$vals[csf("serving_company")];
				
					$allcolor_id_arr_dtls[$vals[csf("serving_company")]][$vals[csf("color_number_id")]]=$vals[csf("color_number_id")];  
					$col_size_wise_lay_company_qnty[$vals[csf("serving_company")]][$vals[csf("color_number_id")]][$vals[csf("size_number_id")]]+=$vals[csf("production_qnty")];

				}		 
				
				 
			}			 
		}  
  		else if($prod_popup_type==7)
		{
			$sql_colsiz="SELECT e.color_number_id,e.size_number_id ,b.color_size_break_down_id, sum(b.production_qnty) as 
			production_qnty,a.serving_company		   
			from pro_garments_production_mst a, pro_garments_production_dtls b ,wo_po_details_master c,wo_po_color_size_breakdown e
			where a.id=b.mst_id  and c.job_no=e.job_no_mst and e.id=b.color_size_break_down_id and a.po_break_down_id=e.po_break_down_id and c.job_no='$job_no'  and a.production_type='$prod_popup_type' and b.production_type='$prod_popup_type' and a.po_break_down_id in($po_break_down_id) and a.color_number_id=$color_id $prod_print_cond $prod_sourceCond $country_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0  group by e.color_number_id,e.size_number_id ,b.color_size_break_down_id,a.serving_company order by a.serving_company";
		}		 
  		else if($prod_popup_type==6)
		{
			$sql_colsiz="SELECT e.color_number_id,c.job_no, sum(b.reject_qty) as production_qnty,a.serving_company,a.sewing_line  
			from pro_garments_production_mst a, pro_garments_production_dtls b ,wo_po_details_master c,wo_po_color_size_breakdown e
			where a.id=b.mst_id  and c.job_no=e.job_no_mst and e.id=b.color_size_break_down_id and a.po_break_down_id=e.po_break_down_id  and a.production_type in(1,3,5,7,8,11) and b.production_type in(1,3,5,7,8,11) and a.po_break_down_id in($po_break_down_id) and e.color_number_id=$color_id and c.job_no='$job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0  group by e.color_number_id,c.job_no,a.serving_company,a.sewing_line order by a.serving_company";
		}
		else
		{  
			$embel_con = "";
			$source_cond = "";
			if($prod_popup_type==2 && $prod_popup_lelel_type !="" || $prod_popup_type==3 && $prod_popup_lelel_type !="" || $prod_popup_type==4 && $prod_popup_lelel_type !="" || $prod_popup_type==5 && $prod_popup_lelel_type !="")
				{ $source_cond = "and a.production_source=$prod_popup_lelel_type";}
			if($prod_popup_type==2 || $prod_popup_type==3){$embel_con=" and a.embel_name=$prod_popup_lelel_cat";}
			// $source_cond = ($prod_popup_type==1 && $prod_popup_lelel_type !="") ? "" : "and a.production_source=$prod_popup_lelel_type";
			$sql_colsiz="SELECT e.color_number_id,c.job_no, sum(b.production_qnty) as production_qnty,b.color_size_break_down_id,a.serving_company,a.sewing_line  
			from pro_garments_production_mst a, pro_garments_production_dtls b ,wo_po_details_master c,wo_po_color_size_breakdown e
			where a.id=b.mst_id  and c.job_no=e.job_no_mst and e.id=b.color_size_break_down_id and a.po_break_down_id=e.po_break_down_id  and a.production_type='$prod_popup_type' $source_cond $embel_con and b.production_type='$prod_popup_type' and a.po_break_down_id in($po_break_down_id) and e.color_number_id=$color_id and c.job_no='$job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0  group by b.color_size_break_down_id,e.color_number_id,c.job_no,a.serving_company,a.sewing_line order by a.serving_company";
		}
		//echo $sql_colsiz;
		$sql_color_size=sql_select($sql_colsiz);
		// print_r($sql_color_size);
		$prod_color_size_data=array();
		$prod_color_size_data_working_comp=array();
		$color_size_working_comp=array();
		$color_size_sewingLine=array();
		// print_r($sql_color_size);die();
		foreach($sql_color_size as $row)
		{
			if($prod_popup_type==11)
			{ 
				$allcolor_id_arr[$row[csf("color_number_id")]]=$row[csf("color_number_id")];
				$color_size_data[$row[csf("job_no")]]["color_number_id"]=$row[csf("color_number_id")];
				$color_size_data[$row[csf("job_no")]]["size_number_id"]=$row[csf("size_number_id")];
				//$color_size_data[$row[csf("color_size_break_down_id")]]["serving_company"]=$row[csf("serving_company")];
				//$col_size_wise_company_arr[$row[csf("serving_company")]] =$row[csf("serving_company")];
			}
			if($prod_popup_type==10)
			{ 
				$allcolor_id_arr[$row[csf("color_number_id")]]=$row[csf("color_number_id")];
				$color_size_data[$row[csf("job_no")]]["color_number_id"]=$row[csf("color_number_id")];
				$color_size_data[$row[csf("job_no")]]["size_number_id"]=$row[csf("size_number_id")];
			}
			if($prod_popup_type==7)
			{
				$allcolor_id_arr[$row[csf("color_number_id")]]=$row[csf("color_number_id")];
				$color_size_data[$row[csf("job_no")]]["color_number_id"]=$row[csf("color_number_id")];
				$color_size_data[$row[csf("job_no")]]["size_number_id"]=$row[csf("size_number_id")];
			}
			$allcolor_id_arr_dtls[$row[csf("serving_company")]][$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]]["color"]=$row[csf("color_number_id")];
			// print_r($allcolor_id_arr_dtls);
			if($color_size_sewingLine[$row[csf("serving_company")]]["sewing_line"]=="")
			{
				$color_size_sewingLine[$row[csf("serving_company")]]["sewing_line"]=$row[csf("sewing_line")];
			}
			else
			{
				$color_size_sewingLine[$row[csf("serving_company")]]["sewing_line"].=','.$row[csf("sewing_line")];
			}

			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["qty"]+=$row[csf('production_qnty')];
			$prod_color_size_data_working_comp[$row[csf("serving_company")]][$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["qty"]+=$row[csf('production_qnty')];
		}
		//print_r($color_size_sewingLine);
		//echo $prod_popup_type;
		if($prod_popup_type==8)
        {

			$transfer_info_sql="SELECT c.job_no_mst as job_no, c.po_break_down_id as po_id,c.color_number_id as colors ,sum( case when a.trans_type=5 and  b.trans_type=5  then b.production_qnty else 0 end) as transfer_in ,sum( case when a.trans_type=6 and  b.trans_type=6  then b.production_qnty else 0 end) as transfer_out,a.serving_company,a.sewing_line from pro_garments_production_mst a,pro_garments_production_dtls b,wo_po_color_size_breakdown c where a.id=b.mst_id and b.color_size_break_down_id=c.id and   a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and a.production_type=10 and b.production_type=10 and a.trans_type in(5,6)  and b.trans_type in(5,6) and a.po_break_down_id in($po_break_down_id) and c.color_number_id=$color_id group by c.job_no_mst, c.po_break_down_id  ,c.color_number_id,a.serving_company,a.sewing_line order by a.serving_company";
			$transfer_info=sql_select($transfer_info_sql);
			foreach($transfer_info as $trans_val)
			{
				$transfer_info_arr[$trans_val[csf("job_no")]][$trans_val[csf("colors")]]['transfer_in']+=$trans_val[csf("transfer_in")];
				$transfer_info_arr[$trans_val[csf("job_no")]][$trans_val[csf("colors")]]['transfer_out']+=$trans_val[csf("transfer_out")];
			}
		}
		//print_r($prod_color_size_data);
		$table_width=(200+(count($sizearr_order)*60));
		
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <div id="exc"></div>
        </div>
        
		<? ob_start(); ?>
        <div style="width:<? echo $table_width+200; ?>px" align="left" id="details_reports">
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
            	<tr>
                	<th width colspan="2"></th>
                    <th width colspan="<?  echo  count($sizearr_order); ?>">Size</th>
                    <th colspan="<? if($prod_popup_type==8){echo "3";} ?>"></th>
                </tr>
                <tr>

                	<th width="30" >SL</th>
                    <th width="100" >Color Name</th>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                        <?
                    }
                    if($prod_popup_type==8)
                    {
                    	?>
                    	<th width="100" >Transfer In</th>
                    	<th width="100" >Transfer Out</th>

                    	<?
                    }
                    ?>

                    <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
            	<? $sl=1; $sub_total_prod_poly_qntyy=array(); $sub_totals=0; 
            	foreach($allcolor_id_arr as $inc=>$color_id_val)
            	{ 
            		if($color_id_val)
            		{
		            	?>
		                <tr>
		                 	<td align="center" valign="middle"><? echo $sl; ?></td>
		                    <td align="center" valign="middle"><? echo $color_Arr_library[$color_id_val]; ?></td>
		                    <?
							$total_prod_poly_qnty=0;
		                    foreach($sizearr_order as $size_id)
		                    {
		                    	if($prod_popup_type==17)
		                    	{
									$sub_total_prod_poly_qntyy[$size_id]+=$col_size_wise_lay[$color_id_val][$size_id];
									 ?>
		                    		<td align="right"><? echo number_format($col_size_wise_lay[$color_id_val][$size_id],0); $total_prod_poly_qnty+=$col_size_wise_lay[$color_id_val][$size_id] ; ?></td> 
		                    		<?	
		                		}
		                    	else
		                    	{
									$sub_total_prod_poly_qntyy[$size_id]+=$prod_color_size_data[$color_id_val][$size_id]["qty"];
		                        	?>
		                        	<td align="right"><? echo number_format($prod_color_size_data[$color_id_val][$size_id]["qty"],0); $total_prod_poly_qnty+=$prod_color_size_data[$color_id_val][$size_id]["qty"]; ?></td> 
		                        	<?  
		                    	}
		                    }
		                    if($prod_popup_type==8)
		                    { 
			                    ?>
			                    <td align="right"><? $trans_in=$transfer_info_arr[$job_no][$inc]['transfer_in']; echo number_format($trans_in,0); ?></td>
			                    <td align="right"><? $trans_out=$transfer_info_arr[$job_no][$inc]['transfer_out']; echo number_format($trans_out,0); ?></td>
			                    <?
		                	}
		               		?>
		                    <td align="right"><? $totals=$total_prod_poly_qnty+ $trans_in-$trans_out;echo number_format($totals,0); ?></td>
		                </tr>
		                <? $sub_totals+=$totals; $sl++; 
          		    }
                } 
                ?>
            </tbody>
             <tfoot>
            	<td align="right" colspan="2"><b>Total</b></td>
                <? 
				foreach($sizearr_order as $size_id)
				{
				?>
					<td align="right"><? echo $sub_total_prod_poly_qntyy[$size_id]; ?></td>
				<?	
				}
				if($prod_popup_type==8){
				?>	
					<td colspan="2"></td>
				<?
				}
                ?>
                <td align="right"> <? echo $sub_totals; ?></td>
            </tfoot>
		</table>
    	</div>
        <div style="width:<? echo $table_width+200; ?>px" align="center" id="details_reports">
        <?
			$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name"); 
			$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
			$prod_reso_allocation=array();
		    $nameArray = sql_select("select id, auto_update,company_name from variable_settings_production where variable_list=23 and status_active=1 and is_deleted=0");
			foreach($nameArray as $nameArrayRow)
			{
				$prod_reso_allocation[$nameArrayRow[csf('company_name')]]["auto_update"]= $nameArrayRow[csf('auto_update')];
			}
		   // $prod_reso_allocation = $nameArray[0][csf('auto_update')];
		?>
        <strong>Details</strong>
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width+200; ?>">
            <thead>
            	<tr>
                	<th width colspan="<?  if($prod_popup_type==4 || $prod_popup_type==5 || $prod_popup_type==11){ echo "4"; }else{echo "3";} ?>"></th>
                    <th width colspan="<?  echo  count($sizearr_order); ?>">Size</th>
                    <th colspan="<? if($prod_popup_type==8){echo "3";} ?>"></th>
                </tr>
                <tr>
                	<th width="30" >SL</th>
                  	<th width="200" >Working Company</th>
                    <?
                    if($prod_popup_type==4 || $prod_popup_type==5 || $prod_popup_type==11)
                    {
                    	?>
                    	<th width="100" >Line</th>
                    	<?
                    }
					?>
                    <th width="100" >Color Name</th> <!--$prod_popup_type-->
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                        <?
                    }
                    if($prod_popup_type==8)
                    {
                    	?>
                    	<th width="100" >Transfer In</th>
                    	<th width="100" >Transfer Out</th>

                    	<?
                    }
                    ?>

                    <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
            	<? 
            	$sl=1; 
            	$sub_total_prod_poly_qnty=array(); 
            	$sub_totals=0;            	
            	$po_company=sql_select("SELECT a.company_name from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and a.job_no='$job_no' and b.id in($po_break_down_id) and a.status_active=1 and b.status_active in(1,2,3)");
            	// echo "<pre>";
            	// print_r($allcolor_id_arr_dtls);
				//foreach($col_size_wise_company_arr as $dataRow){
				foreach($allcolor_id_arr_dtls as $company=>$company_data) 
				{ 				 	  				 
				   	foreach($company_data as $colors=>$val) 
				   	{  
				   		if($colors)
				   		{
							?>
			                <tr>
			                	<td align="center" valign="middle"><? echo $sl; ?></td>
			                    <td align="center" valign="middle"><? if($company_library[$company]) echo $company_library[$company];  else echo $supplier_arr[$company ];
			                    if($prod_popup_type==10){ echo $company_library[$sql_res[0][csf('serving_company')]]; }
									
								?></td>
			                    <?
			                    if($prod_popup_type==4 || $prod_popup_type==5 || $prod_popup_type==11)
			                    {
			                    	?>
			                    	 <td align="center" valign="middle"><? 
									if($prod_reso_allocation[$po_company[0][csf("company_name")]]["auto_update"]==1)
									{
										$serving_company_lines=explode(",",$color_size_sewingLine[$company]["sewing_line"]);
										$sewing_line_name = "";
										$line_duplicate_arr=array();
										foreach($serving_company_lines as $lines_id)
										{
											$sewing_line =  $resource_alocate_line[$lines_id];
											$sewing_line_arr = explode(",", $sewing_line);

											foreach ($sewing_line_arr as $line_id) 
											{
												if($line_duplicate_arr[$line_id]=="")
												{
													$sewing_line_name.=$lineArr[$line_id]." ,";
													$line_duplicate_arr[$line_id]=$line_id;
												}
											}
										}									    
										$sewing_line_name = chop($sewing_line_name, ",");
										echo $sewing_line_name;

									}
									else
									{  
										$sewing_line =  $color_size_sewingLine[$company]["sewing_line"];
										foreach(explode(",", $sewing_line) as $key=>$value)
										{
											$sewing_line_name.=$lineArr[$value]." ,";
										}
											$sewing_line_name = chop($sewing_line_name, ",");
									   		echo $sewing_line_name;
									}
									 ?></td>
			                    	<?
			                    }
								?>
			                    <td align="center" valign="middle"><? echo $color_Arr_library[$colors]; ?></td>
			                    <?
								$total_prod_poly_qnty=0; 
			                    foreach($sizearr_order as $size_id)
			                    {
									
			                    	if($prod_popup_type==17)
			                    	{ 
										$sub_total_prod_poly_qnty[$size_id]+=$col_size_wise_lay_company_qnty[$company][$colors][$size_id];
										?>
				                    	<td align="right"><? 
										echo number_format($col_size_wise_lay_company_qnty[$company][$colors][$size_id],0);$total_prod_poly_qnty+=$col_size_wise_lay_company_qnty[$company][$colors][$size_id]; 
										?></td> 

			                    		<?	
			                    	}
			                    	else
			                    	{
										$sub_total_prod_poly_qnty[$size_id]+=$prod_color_size_data_working_comp[$company][$colors][$size_id]["qty"];
				                        ?>
				                        <td align="right"><? echo number_format($prod_color_size_data_working_comp[$company][$colors][$size_id]["qty"],0); $total_prod_poly_qnty+=$prod_color_size_data_working_comp[$company][$colors][$size_id]["qty"]; ?></td> 
				                        <?  
			                    	}
			                    }
			                    if($prod_popup_type==8)
			                    { 
				                    ?>
				                    <td align="right"><? $trans_in=$transfer_info_arr[$po_break_down_id][$colors]['transfer_in']; echo number_format($trans_in,0); ?></td>
				                    <td align="right"><? $trans_out=$transfer_info_arr[$po_break_down_id][$colors]['transfer_out']; echo number_format($trans_out,0); ?></td>
				                    <?
			                	}
			               		?>
			                    <td align="right"><? $totals=$total_prod_poly_qnty+ $trans_in-$trans_out;echo number_format($totals,0); ?></td>
			                </tr>
			                <?			
			                $sub_totals+=$totals; $sl++;
		               	}
					}
				} 
				?>
            </tbody>
            <tfoot>
            	<td align="right" colspan=" <? if($prod_popup_type==4 || $prod_popup_type==5 || $prod_popup_type==11){echo "4";}else{echo "3";} ?>"><b>Total</b></td>
                <? 
				foreach($sizearr_order as $size_id)
				{
				?>
					<td align="right"><? echo $sub_total_prod_poly_qnty[$size_id]; ?></td>
				<?	
				}
				if($prod_popup_type==8){
				?>	
					<td colspan="2"></td>
				<?	
				}
                ?>
                <td align="right"> <? echo $sub_totals; ?></td>
            </tfoot>
		</table>
    	</div>
    	</div>
		<?
		
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
		//echo "$total_data####$filename####$reportType";
		$filename=$filename;

		?>
		<script>
            document.getElementById('exc').innerHTML='<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />&nbsp;<a href="<? echo $filename;?>" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>';
		</script>

		<?		
		die;
			
	if($prod_popup_type==1 && $prod_popup_lelel_type==1) //for cutting
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
		from pro_garments_production_mst a,  pro_garments_production_dtls b
		where a.id=b.mst_id and a.production_type=1 and a.po_break_down_id in($po_break_down_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by b.color_size_break_down_id");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]] +=$row[csf('production_qnty')]; 
		}
		$table_width=(300+(count($sizearr_order)*60));
		
		
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
                <thead>
                    <tr>
                        <th width="100" >Color Name</th>
                        <th width="100">Production Type</th>
                        <?
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                            <?
                        }
                        ?>
                        <th >Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td rowspan="4" align="center" valign="middle" ><? echo $color_Arr_library[$color_id]; ?></td>
                        <td >Order Qty.</td>
                        <?
						$total_ord_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Plan Cut Qty.</td>
                        <?
						$total_plan_ord_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0); $total_plan_ord_qnty+=$plan_order_size_qnty[$size_id]; ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_plan_ord_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Cutting Qty.</td>
                        <?
						$total_color_size_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right" ><? echo number_format($prod_color_size_data[$size_id],0); $total_color_size_qnty+=$prod_color_size_data[$size_id];  ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_color_size_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Cutting Balance.</td>
                        <?
						$total_cut_balance=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right" title="Cutting Qty-Plan Cut Qty."><? $cut_balance=($plan_order_size_qnty[$size_id]-$prod_color_size_data[$size_id]); echo number_format($cut_balance,0); $total_cut_balance+=$cut_balance; ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_cut_balance,0); ?></td>
                    </tr>
                </tbody>
            </table>
            <br />
    	</div>
    	<?
		
	}
	else if($prod_popup_type==2 && $prod_popup_lelel_type==1 && $prod_popup_lelel_cat==1) //for emb print inhouse issue
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
		from pro_garments_production_mst a,  pro_garments_production_dtls b
		where a.id=b.mst_id and a.production_type=2 and a.embel_name=1 and a.production_source=1 and a.po_break_down_id in($po_break_down_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id ");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]] +=$row[csf('production_qnty')];
		}
		$table_width=(300+(count($sizearr_order)*60));
		?>
        <div id="data_panel"  style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
                <thead>
                    <tr>
                        <th width="100" >Color Name</th>
                        <th width="100">Production Type</th>
                        <?
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                            <?
                        }
                        ?>
                        <th width="100" >Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td rowspan="5" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                        <td>Order Qty.</td>
                        <?
						$total_ord_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Plan Cut Qty.</td>
                        <?
						$total_plan_ord_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0); $total_plan_ord_qnty+=$plan_order_size_qnty[$size_id]; ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_plan_ord_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Print Sent Plan Qty.</td>
                        <?
						$total_print_sent_plan_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right">
                            <?
                            $print_sent_plan_cut=$plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$cut_panel_rejection)/100;
                            echo number_format($print_sent_plan_cut,0); $total_print_sent_plan_qnty+=$print_sent_plan_cut;
                            ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_print_sent_plan_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Print Sent Qty.</td>
                        <?
						$total_print_sent_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format( $prod_color_size_data[$size_id],0); $total_print_sent_qnty+=$prod_color_size_data[$size_id]; ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_print_sent_qnty,0); ?></td>
                    </tr>
                    <tr>

                        <td>Print Sent Balance</td>
                        <?
						$total_print_sent_balence=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right">
                            <?
                            $print_sent_balence=(($plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$cut_panel_rejection)/100)-$prod_color_size_data[$size_id]);
                             echo number_format($print_sent_balence,0); $total_print_sent_balence+=$print_sent_balence;
                            ?>
                             </td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_print_sent_balence,0); ?></td>
                    </tr>
                </tbody>
            </table>
    	</div>
    	<?
		
		
	}
	else if($prod_popup_type==2 && $prod_popup_lelel_type==1 && $prod_popup_lelel_cat==2) //for emb print subcontract issue
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
		from pro_garments_production_mst a,  pro_garments_production_dtls b 
		where a.id=b.mst_id and a.production_type=2 and a.embel_name=1 and a.production_source<>1 and a.po_break_down_id in($po_break_down_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by b.color_size_break_down_id ");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]] +=$row[csf('production_qnty')];
		}
		$table_width=(300+(count($sizearr_order)*60));
		?>
        <div id="data_panel"  style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
                <thead>
                    <tr>
                        <th width="100" >Color Name</th>
                        <th width="100">Production Type</th>
                        <?
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                            <?
                        }
                        ?>
                        <th width="100" >Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td rowspan="5" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                        <td>Order Qty.</td>
                        <?
						$total_ord_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Plan Cut Qty.</td>
                        <?
						$total_plan_ord_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0); $total_plan_ord_qnty+=$plan_order_size_qnty[$size_id]; ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_plan_ord_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Print Sent Plan Qty.</td>
                        <?
						$total_print_sent_plan_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right">
                            <?
                            $print_sent_plan_cut=$plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$cut_panel_rejection)/100;
                            echo number_format($print_sent_plan_cut,0); $total_print_sent_plan_qnty+=$print_sent_plan_cut;
                            ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_print_sent_plan_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Print Sent Qty.</td>
                        <?
						$total_print_sent_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format( $prod_color_size_data[$size_id],0); $total_print_sent_qnty+=$prod_color_size_data[$size_id]; ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_print_sent_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Print Sent Balance</td>
                        <?
						$total_print_sent_balence=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right">
                            <?
                            $print_sent_balence=(($plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$cut_panel_rejection)/100)-$prod_color_size_data[$size_id]);
                             echo number_format($print_sent_balence,0); $total_print_sent_balence+=$print_sent_balence;
                            ?>
                             </td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_print_sent_balence,0); ?></td>
                    </tr>
                </tbody>
            </table>
    	</div>
    	<?
		
		
	}
	else if($prod_popup_type==3 && $prod_popup_lelel_type==1  && $prod_popup_lelel_cat==1) //for emb print inhouse receive
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
		from pro_garments_production_mst a,  pro_garments_production_dtls b 
		where a.id=b.mst_id and a.production_type=3 and a.embel_name=1  and a.production_source=1 and a.po_break_down_id in($po_break_down_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by b.color_size_break_down_id ");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]] +=$row[csf('production_qnty')];
		}
		$table_width=(300+(count($sizearr_order)*60));
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
                <thead>
                    <tr>
                        <th width="100" >Color Name</th>
                        <th width="100">Production Type</th>
                        <?
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                            <?
                        }
                        ?>
                        <th width="100" >Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td rowspan="5" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                        <td>Order Qty.</td>
                        <?
						$total_ord_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Plan Cut Qty.</td>
                        <?
						$total_plan_ord_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0); $total_plan_ord_qnty+=$plan_order_size_qnty[$size_id]; ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_plan_ord_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Print Rcvd. Plan Qty.</td>
                        <?
						$total_print_sent_plan_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right">
                            <?
                            $total_cut_reject=$cut_panel_rejection+$chest_printing+$neck_sleeve_printing;
                            $print_sent_plan_cut=$plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100;
                            echo number_format($print_sent_plan_cut,0); $total_print_sent_plan_qnty +=$print_sent_plan_cut;
                            ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_print_sent_plan_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Print Received Qty.</td>
                        <?
						$total_prod_print_rcv_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format( $prod_color_size_data[$size_id],0); $total_prod_print_rcv_qnty+= $prod_color_size_data[$size_id]; ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_prod_print_rcv_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Print Rcvd. Balance</td>
                        <?
						$total_print_sent_balance=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right">
                            <?
                            $print_sent_balence=(($plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100)-$prod_color_size_data[$size_id]);
                             echo number_format($print_sent_balence,0); $total_print_sent_balance+=$print_sent_balence;
                            ?>
                             </td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_print_sent_balance,0); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    	<?
	}
	else if($prod_popup_type==3 && $prod_popup_lelel_type==1  && $prod_popup_lelel_cat==2) //for emb print subcontract receive
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
		from pro_garments_production_mst a,  pro_garments_production_dtls b
		where a.id=b.mst_id and a.production_type=3 and a.embel_name=1  and a.production_source<>1 and a.po_break_down_id in($po_break_down_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id ");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]] +=$row[csf('production_qnty')];
		}
		$table_width=(300+(count($sizearr_order)*60));
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
                <thead>
                    <tr>
                        <th width="100" >Color Name</th>
                        <th width="100">Production Type</th>
                        <?
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                            <?
                        }
                        ?>
                        <th width="100" >Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td rowspan="5" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                        <td>Order Qty.</td>
                        <?
						$total_ord_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Plan Cut Qty.</td>
                        <?
						$total_plan_ord_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0); $total_plan_ord_qnty+=$plan_order_size_qnty[$size_id]; ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_plan_ord_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Print Rcvd. Plan Qty.</td>
                        <?
						$total_print_sent_plan_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right">
                            <?
                            $total_cut_reject=$cut_panel_rejection+$chest_printing+$neck_sleeve_printing;
                            $print_sent_plan_cut=$plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100;
                            echo number_format($print_sent_plan_cut,0); $total_print_sent_plan_qnty +=$print_sent_plan_cut;
                            ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_print_sent_plan_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Print Received Qty.</td>
                        <?
						$total_prod_print_rcv_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format( $prod_color_size_data[$size_id],0); $total_prod_print_rcv_qnty+= $prod_color_size_data[$size_id]; ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_prod_print_rcv_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Print Rcvd. Balance</td>
                        <?
						$total_print_sent_balance=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right">
                            <?
                            $print_sent_balence=(($plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100)-$prod_color_size_data[$size_id]);
                             echo number_format($print_sent_balence,0); $total_print_sent_balance+=$print_sent_balence;
                            ?>
                             </td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_print_sent_balance,0); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    	<?
	}
	else if($prod_popup_type==2 && $prod_popup_lelel_type==2  && $prod_popup_lelel_cat==1) //for emb ebroidery inhouse issue
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
		from pro_garments_production_mst a,  pro_garments_production_dtls b 
		where a.id=b.mst_id and a.production_type=2 and a.embel_name=2 and a.production_source=1  and a.po_break_down_id in($po_break_down_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by b.color_size_break_down_id ");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]] +=$row[csf('production_qnty')];
		}
		$table_width=(300+(count($sizearr_order)*60));
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                <th width="100" >Color Name</th>
                <th width="100">Production Type</th>
                <?
                foreach($sizearr_order as $size_id)
                {
					?>
					<th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
					<?
                }
                ?>
                <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="5" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                    <td>Order Qty.</td>
                    <?
                    $total_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
                    
                </tr>
                <tr>
                    <td>Plan Cut Qty.</td>
                    <?
                    $total_plan_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0); $total_plan_ord_qnty+=$plan_order_size_qnty[$size_id]; ?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_plan_ord_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Emb. Sent Plan Qty</td>
                    <?
					$total_plan_sent_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$total_cut_reject=$cut_panel_rejection+$chest_printing+$neck_sleeve_printing;
						$print_emb_plan_cut=$plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100;
						echo number_format($print_emb_plan_cut,0); $total_plan_sent_qnty+=$print_emb_plan_cut;
						?>
                        </td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_plan_sent_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Print Received Qty.</td>
                    <?
					$total_prod_print_rcv_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format( $prod_color_size_data[$size_id],0); $total_prod_print_rcv_qnty+=$prod_color_size_data[$size_id];?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_prod_print_rcv_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Print Rcvd. Balance</td>
                    <?
					$total_print_rcv_balance=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$print_sent_balence=(($plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100)-$prod_color_size_data[$size_id]);
						 echo number_format($print_sent_balence,0); $total_print_rcv_balance+=$print_sent_balence;
						?>
                         </td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_print_rcv_balance,0); ?></td>
                </tr>
            </tbody>
		</table>
        </div>
    	<?
	}
	else if($prod_popup_type==2 && $prod_popup_lelel_type==2  && $prod_popup_lelel_cat==2) //for emb ebroidery subcontract issue
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
		from pro_garments_production_mst a,  pro_garments_production_dtls b
		where a.id=b.mst_id and a.production_type=2 and a.embel_name=2 and a.production_source<>1  and a.po_break_down_id in($po_break_down_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by b.color_size_break_down_id");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]] +=$row[csf('production_qnty')];
		}
		$table_width=(300+(count($sizearr_order)*60));
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                <th width="100" >Color Name</th>
                <th width="100">Production Type</th>
                <?
                foreach($sizearr_order as $size_id)
                {
					?>
					<th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
					<?
                }
                ?>
                <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="5" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                    <td>Order Qty.</td>
                    <?
                    $total_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
                    
                </tr>
                <tr>
                    <td>Plan Cut Qty.</td>
                    <?
                    $total_plan_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0); $total_plan_ord_qnty+=$plan_order_size_qnty[$size_id]; ?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_plan_ord_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Emb. Sent Plan Qty</td>
                    <?
					$total_plan_sent_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$total_cut_reject=$cut_panel_rejection+$chest_printing+$neck_sleeve_printing;
						$print_emb_plan_cut=$plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100;
						echo number_format($print_emb_plan_cut,0); $total_plan_sent_qnty+=$print_emb_plan_cut;
						?>
                        </td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_plan_sent_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Print Received Qty.</td>
                    <?
					$total_prod_print_rcv_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format( $prod_color_size_data[$size_id],0); $total_prod_print_rcv_qnty+=$prod_color_size_data[$size_id];?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_prod_print_rcv_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Print Rcvd. Balance</td>
                    <?
					$total_print_rcv_balance=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$print_sent_balence=(($plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100)-$prod_color_size_data[$size_id]);
						 echo number_format($print_sent_balence,0); $total_print_rcv_balance+=$print_sent_balence;
						?>
                         </td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_print_rcv_balance,0); ?></td>
                </tr>
            </tbody>
		</table>
        </div>
    	<?
	}
	else if($prod_popup_type==3 && $prod_popup_lelel_type==2 && $prod_popup_lelel_cat==1) //for emb ebroidery inhouse receive
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
		from pro_garments_production_mst a,  pro_garments_production_dtls b
		where a.id=b.mst_id and a.production_type=3 and a.embel_name=2 and a.production_source=1  and a.po_break_down_id in($po_break_down_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]] +=$row[csf('production_qnty')];
		}
		$table_width=(300+(count($sizearr_order)*60));
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
                <thead>
                    <tr>
                    <th width="100" >Color Name</th>
                    <th width="100">Production Type</th>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                        <?
                    }
                    ?>
                    <th width="100" >Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td rowspan="5" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                        <td>Order Qty.</td>
                        <?
                        $total_ord_qnty=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
							<?
						}
						?>
						<td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Plan Cut Qty.</td>
                        <?
						$total_plan_ord_qnty=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0); $total_plan_ord_qnty+=$plan_order_size_qnty[$size_id]; ?></td> 
							<?
						}
						?>
						<td align="right"><? echo number_format($total_plan_ord_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Emb. Rcvd. Plan Qty.</td>
                        <?
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right">
                            <?
							$total_rcv_plan_qnty=0;
                            $total_cut_reject=$cut_panel_rejection+$chest_printing+$neck_sleeve_printing+$embroidery;
                            $print_enb_plan_cut=$plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100;
                            echo number_format($print_enb_plan_cut,0); $total_rcv_plan_qnty+=$print_enb_plan_cut;
                            ?>
                            </td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_rcv_plan_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Emb. Rcvd. Qty.</td>
                        <?
						$total_pord_recv_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format( $prod_color_size_data[$size_id],0); $total_pord_recv_qnty+=$prod_color_size_data[$size_id]; ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_pord_recv_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Emb. Rcvd. Balance</td>
                        <?
						$total_emb_balect=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right">
                            <?
                            $print_sent_balence=(($plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100)-$prod_color_size_data[$size_id]);
                             echo number_format($print_sent_balence,0); $total_emb_balect+=$print_sent_balence;
                            ?>
                             </td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_emb_balect,0); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    	<?
	}
	else if($prod_popup_type==3 && $prod_popup_lelel_type==2 && $prod_popup_lelel_cat==2) //for emb ebroidery subcon receive
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
		from pro_garments_production_mst a,  pro_garments_production_dtls b 
		where a.id=b.mst_id and a.production_type=3 and a.embel_name=2 and a.production_source<>1  and a.po_break_down_id in($po_break_down_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id ");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]] =$row[csf('production_qnty')];
		}
		$table_width=(300+(count($sizearr_order)*60));
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
            <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
                <thead>
                    <tr>
                    <th width="100" >Color Name</th>
                    <th width="100">Production Type</th>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                        <?
                    }
                    ?>
                    <th width="100" >Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td rowspan="5" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                        <td>Order Qty.</td>
                        <?
                        $total_ord_qnty=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
							<?
						}
						?>
						<td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Plan Cut Qty.</td>
                        <?
						$total_plan_ord_qnty=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0); $total_plan_ord_qnty+=$plan_order_size_qnty[$size_id]; ?></td> 
							<?
						}
						?>
						<td align="right"><? echo number_format($total_plan_ord_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Emb. Rcvd. Plan Qty.</td>
                        <?
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right">
                            <?
							$total_rcv_plan_qnty=0;
                            $total_cut_reject=$cut_panel_rejection+$chest_printing+$neck_sleeve_printing+$embroidery;
                            $print_enb_plan_cut=$plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100;
                            echo number_format($print_enb_plan_cut,0); $total_rcv_plan_qnty+=$print_enb_plan_cut;
                            ?>
                            </td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_rcv_plan_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Emb. Rcvd. Qty.</td>
                        <?
						$total_pord_recv_qnty=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right"><? echo number_format( $prod_color_size_data[$size_id],0); $total_pord_recv_qnty+=$prod_color_size_data[$size_id]; ?></td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_pord_recv_qnty,0); ?></td>
                    </tr>
                    <tr>
                        <td>Emb. Rcvd. Balance</td>
                        <?
						$total_emb_balect=0;
                        foreach($sizearr_order as $size_id)
                        {
                            ?>
                            <td align="right">
                            <?
                            $print_sent_balence=(($plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100)-$prod_color_size_data[$size_id]);
                             echo number_format($print_sent_balence,0); $total_emb_balect+=$print_sent_balence;
                            ?>
                             </td> 
                            <?
                        }
                        ?>
                        <td align="right"><? echo number_format($total_emb_balect,0); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    	<?
	}
	else if($prod_popup_type==2 && $prod_popup_lelel_type==4) //for emb sp issue
	{
		/*$sql_color_size= sql_select("SELECT c.size_number_id, sum(b.production_qnty) as production_qnty
		from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
		where a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.production_type=2 and a.embel_name=4 and c.po_break_down_id=$po_break_down_id and c.item_number_id=$item_id and c.country_id=$country_id and c.color_number_id=$color_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
		group by c.size_number_id
		order by c.size_number_id");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$row[csf('size_number_id')]] =$row[csf('production_qnty')];
		}
		$table_width=(200+count($table_width)*60);
		?>
		<div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                <th width="100" >Color Name</th>
                <th width="100">Production Type</th>
                <?
                foreach($sizearr_order as $size_id)
                {
					?>
					<th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
					<?
                }
                ?>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="5" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                    <td>Order Qty.</td>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
						?>
						<td align="right"><? echo number_format($order_size_qnty[$size_id],0);?></td> 
						<?
                    }
                    ?>
                </tr>
                <tr>
                    <td>Plan Cut Qty.</td>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0);?></td> 
                        <?
                    }
                    ?>
                </tr>
                <tr>
                    <td>Emb. Rcvd. Plan Qty.</td>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$total_cut_reject=$cut_panel_rejection+$chest_printing+$neck_sleeve_printing+$embroidery;
						$print_enb_plan_cut=$plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100;
						echo number_format($print_enb_plan_cut,0);
						?></td> 
                        <?
                    }
                    ?>
                </tr>
                <tr>
                    <td>Emb. Rcvd. Qty.</td>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format( $prod_color_size_data[$size_id],0);?></td> 
                        <?
                    }
                    ?>
                </tr>
                <tr>
                    <td>Emb. Rcvd. Balance</td>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$print_sent_balence=($prod_color_size_data[$size_id]-($plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100));
						 echo number_format($print_sent_balence,0);
						?>
                         </td> 
                        <?
                    }
                    ?>
                </tr>
            </tbody>
		</table>
		</div>
    	<?*/
	}
	else if($prod_popup_type==3 && $prod_popup_lelel_type==4) //for emb sp receive
	{
		/*$sql_color_size= sql_select("SELECT c.size_number_id, sum(b.production_qnty) as production_qnty
		from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
		where a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.production_type=3 and a.embel_name=4 and c.po_break_down_id=$po_break_down_id and c.item_number_id=$item_id and c.country_id=$country_id and c.color_number_id=$color_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
		group by c.size_number_id
		order by c.size_number_id");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$row[csf('size_number_id')]] =$row[csf('production_qnty')];
		}*/
	}
	else if($prod_popup_type==4 && $prod_popup_lelel_type==1) //for sewing input
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id,
		sum(case when a.production_source=1 then b.production_qnty else 0 end) as production_inhouse_qnty,
		sum(case when a.production_source=3 then b.production_qnty else 0 end) as production_subcon_qnty
		from pro_garments_production_mst a,  pro_garments_production_dtls b
		where a.id=b.mst_id and a.production_type=4 and a.po_break_down_id in($po_break_down_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id ");
	
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["production_inhouse_qnty"]+=$row[csf('production_inhouse_qnty')];
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["production_subcon_qnty"]+=$row[csf('production_subcon_qnty')];
		}
		
		$table_width=(300+(count($sizearr_order)*60));
		
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                <th width="100" >Color Name</th>
                <th width="100">Production Type</th>
                <?
                foreach($sizearr_order as $size_id)
                {
					?>
					<th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
					<?
                }
                ?>
                <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="6" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                    <td>Order Qty.</td>
                    <?
					$total_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
				</tr>
				<tr>
					<td>Plan Cut Qty.</td>
					<?
					$total_plan_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0); $total_plan_ord_qnty+=$plan_order_size_qnty[$size_id]; ?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_plan_ord_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Sewing Input Plan Qty.</td>
                    <?
					$sewing_in_plan_cut=$total_sew_plan_qnty=0;
					
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$total_cut_reject=($cut_panel_rejection+$chest_printing+$neck_sleeve_printing+$embroidery);
						$sewing_in_plan_cut=($plan_order_size_qnty[$size_id]-(($plan_order_size_qnty[$size_id]*$total_cut_reject)/100)); 
						echo number_format($sewing_in_plan_cut,0); $total_sew_plan_qnty+=$sewing_in_plan_cut;
						?>
                        </td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_sew_plan_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Inhouse Sewing Input Qty.</td>
                    <?
					$total_prod_sew_inhouse_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($prod_color_size_data[$size_id]["production_inhouse_qnty"],0); $total_prod_sew_inhouse_qnty+=$prod_color_size_data[$size_id]["production_inhouse_qnty"]; ?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_prod_sew_inhouse_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Sub Con. Sewing Input Qty.</td>
                    <?
					$total_production_subcon_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($prod_color_size_data[$size_id]["production_subcon_qnty"],0); $total_production_subcon_qnty+=$prod_color_size_data[$size_id]["production_subcon_qnty"];?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_production_subcon_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Sewing Input Balance</td>
                    <?
					$total_prod_in_balance=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<? 
						$total_in_balance= ($plan_order_size_qnty[$size_id]-(($plan_order_size_qnty[$size_id]*$total_cut_reject)/100))-($prod_color_size_data[$size_id]["production_subcon_qnty"]+$prod_color_size_data[$size_id]["production_inhouse_qnty"]);
						echo number_format($total_in_balance,0); $total_prod_in_balance+=$total_in_balance;
						?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_prod_in_balance,0); ?></td>
                </tr>
            </tbody>
		</table>
    	</div>
    	<?
		
	}
	else if($prod_popup_type==4 && $prod_popup_lelel_type==11) //for sewing input inhouse
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(case when a.production_source=1 then b.production_qnty else 0 end) as production_inhouse_qnty
		from pro_garments_production_mst a,  pro_garments_production_dtls b
		where a.id=b.mst_id and a.production_type=4 and a.po_break_down_id in($po_break_down_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by b.color_size_break_down_id ");
	
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["production_inhouse_qnty"]+=$row[csf('production_inhouse_qnty')];
		}
		
		$table_width=(200+(count($sizearr_order)*60));
		
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                <th width="100" >Color Name</th>
                <?
                foreach($sizearr_order as $size_id)
                {
					?>
					<th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
					<?
                }
                ?>
                <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                    <?
					$total_prod_sew_inhouse_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($prod_color_size_data[$size_id]["production_inhouse_qnty"],0); $total_prod_sew_inhouse_qnty+=$prod_color_size_data[$size_id]["production_inhouse_qnty"]; ?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_prod_sew_inhouse_qnty,0); ?></td>
                </tr>
            </tbody>
		</table>
    	</div>
    	<?
		
	}
	else if($prod_popup_type==4 && $prod_popup_lelel_type==13) //for sewing input subcon
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(case when a.production_source=3 then b.production_qnty else 0 end) as production_subcon_qnty
		from pro_garments_production_mst a,  pro_garments_production_dtls b
		where a.id=b.mst_id and a.production_type=4 and a.po_break_down_id in($po_break_down_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id");
	
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["production_subcon_qnty"]+=$row[csf('production_subcon_qnty')];
		}
		
		$table_width=(200+(count($sizearr_order)*60));
		
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                <th width="100" >Color Name</th>
                <?
                foreach($sizearr_order as $size_id)
                {
					?>
					<th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
					<?
                }
                ?>
                <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                    <?
					$total_production_subcon_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($prod_color_size_data[$size_id]["production_subcon_qnty"],0); $total_production_subcon_qnty+=$prod_color_size_data[$size_id]["production_subcon_qnty"];?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_production_subcon_qnty,0); ?></td>
                </tr>
            </tbody>
		</table>
    	</div>
    	<?
		
	}
	else if($prod_popup_type==5 && $prod_popup_lelel_type==1) //for emb sewing output
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id,
		sum(case when a.production_source=1 then b.production_qnty else 0 end) as production_inhouse_qnty,
		sum(case when a.production_source=3 then b.production_qnty else 0 end) as production_subcon_qnty
		from pro_garments_production_mst a,  pro_garments_production_dtls b 
		where a.id=b.mst_id and a.production_type=5 and a.po_break_down_id in($po_break_down_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id");
		
		$prod_color_size_data=array();
	
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["production_inhouse_qnty"]+=$row[csf('production_inhouse_qnty')];
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["production_subcon_qnty"]+=$row[csf('production_subcon_qnty')];
		}
		
		$table_width=(300+(count($sizearr_order)*60));
		
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                    <th width="100" >Color Name</th>
                    <th width="100">Production Type</th>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                        <?
                    }
                    ?>
                    <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="6" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                    <td>Order Qty.</td>
                    <?
					$total_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
				</tr>
				<tr>
					<td>Plan Cut Qty.</td>
					<?
					$total_plan_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0); $total_plan_ord_qnty+=$plan_order_size_qnty[$size_id]; ?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_plan_ord_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Sewing Plan Output Qty.</td>
                    <?
					$sewing_in_plan_cut=$total_sew_out_plan_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$total_cut_reject=($cut_panel_rejection+$chest_printing+$neck_sleeve_printing+$embroidery+$sewing_input);  
						$sewing_out_plan_cut=($plan_order_size_qnty[$size_id]-(($plan_order_size_qnty[$size_id]*$total_cut_reject)/100)); 
						echo number_format($sewing_out_plan_cut,0); $total_sew_out_plan_qnty+=$sewing_out_plan_cut;
						?>
                        </td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_sew_out_plan_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Inhouse Sewing Output Qty.</td>
                    <?
					$total_prod_sew_in_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($prod_color_size_data[$size_id]["production_inhouse_qnty"],0); $total_prod_sew_in_qnty+=$prod_color_size_data[$size_id]["production_inhouse_qnty"]; ?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_prod_sew_in_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Sub Con. Sewing Output Qty.</td>
                    <?
					$total_prod_sew_out_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($prod_color_size_data[$size_id]["production_subcon_qnty"],0); $total_prod_sew_out_qnty+=$prod_color_size_data[$size_id]["production_subcon_qnty"]; ?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_prod_sew_out_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Sewing Output Balance</td>
                    <?
					$total_out_balance_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$total_out_balance= ($plan_order_size_qnty[$size_id]-(($plan_order_size_qnty[$size_id]*$total_cut_reject)/100))-($prod_color_size_data[$size_id]["production_subcon_qnty"]+$prod_color_size_data[$size_id]["production_inhouse_qnty"]);
						echo number_format($total_out_balance,0); $total_out_balance_qnty+=$total_out_balance;
						?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_out_balance_qnty,0); ?></td>
                </tr>
            </tbody>
		</table>
    	</div>
    	<?
	}
	else if($prod_popup_type==5 && $prod_popup_lelel_type==11) //for emb sewing output Inhouse
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id,
		sum(case when a.production_source=1 then b.production_qnty else 0 end) as production_inhouse_qnty
		from pro_garments_production_mst a,  pro_garments_production_dtls b 
		where a.id=b.mst_id and a.production_type=5  and a.po_break_down_id in($po_break_down_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id");
		
		$prod_color_size_data=array();
	
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["production_inhouse_qnty"]+=$row[csf('production_inhouse_qnty')];
		}
		
		$table_width=(200+(count($sizearr_order)*60));
		
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                    <th width="100" >Color Name</th>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                        <?
                    }
                    ?>
                    <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                    <?
					$total_prod_sew_in_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($prod_color_size_data[$size_id]["production_inhouse_qnty"],0); $total_prod_sew_in_qnty+=$prod_color_size_data[$size_id]["production_inhouse_qnty"]; ?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_prod_sew_in_qnty,0); ?></td>
                </tr>
            </tbody>
		</table>
    	</div>
    	<?
	}
	else if($prod_popup_type==5 && $prod_popup_lelel_type==13) //for emb sewing output
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id,
		sum(case when a.production_source=3 then b.production_qnty else 0 end) as production_subcon_qnty
		from pro_garments_production_mst a,  pro_garments_production_dtls b 
		where a.id=b.mst_id and a.production_type=5  and a.po_break_down_id in($po_break_down_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by b.color_size_break_down_id ");
		
		$prod_color_size_data=array();
	
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["production_subcon_qnty"]+=$row[csf('production_subcon_qnty')];
		}
		
		$table_width=(200+(count($sizearr_order)*60));
		
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                    <th width="100" >Color Name</th>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                        <?
                    }
                    ?>
                    <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                    <?
					$total_prod_sew_out_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($prod_color_size_data[$size_id]["production_subcon_qnty"],0); $total_prod_sew_out_qnty+=$prod_color_size_data[$size_id]["production_subcon_qnty"]; ?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_prod_sew_out_qnty,0); ?></td>
                </tr>
            </tbody>
		</table>
    	</div>
    	<?
	}
	else if($prod_popup_type==2 && $prod_popup_lelel_type==3) //for emb wash issue
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
		from pro_garments_production_mst a,  pro_garments_production_dtls b
		where a.id=b.mst_id and a.production_type=2 and a.embel_name=3 and a.po_break_down_id in($po_break_down_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id ");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]] +=$row[csf('production_qnty')];
		}
		$table_width=(300+(count($sizearr_order)*60));
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                    <th width="100" >Color Name</th>
                    <th width="100">Production Type</th>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                        <?
                    }
                    ?>
                    <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="5" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                    <td>Order Qty.</td>
                    <?
					$total_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
				</tr>
				<tr>
					<td>Plan Cut Qty.</td>
					<?
					$total_plan_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0); $total_plan_ord_qnty+=$plan_order_size_qnty[$size_id]; ?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_plan_ord_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Wash Sent Plan Qty</td>
                    <?
					$total_emp_plan_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$total_cut_reject=$cut_panel_rejection+$chest_printing+$neck_sleeve_printing+$embroidery+$sewing_input;
						$print_enb_plan_cut=$plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100;
						echo number_format($print_enb_plan_cut,0); $total_emp_plan_qnty+=$print_enb_plan_cut;
						?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_emp_plan_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Wash Sent Qty.</td>
                    <?
					$total_prod_sent_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format( $prod_color_size_data[$size_id],0); $total_prod_sent_qnty+=$prod_color_size_data[$size_id];?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_prod_sent_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Wash Sent  Balance</td>
                    <?
					$total_wash_balance=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$print_sent_balence=(($plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100)-$prod_color_size_data[$size_id]);
						 echo number_format($print_sent_balence,0); $total_wash_balance+=$print_sent_balence;
						?>
                         </td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_wash_balance,0); ?></td>
                </tr>
            </tbody>
		</table>
        </div>
    	<?
		
	}
	else if($prod_popup_type==3 && $prod_popup_lelel_type==3) //for emb wash receive
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
		from pro_garments_production_mst a,  pro_garments_production_dtls b
		where a.id=b.mst_id and a.production_type=3 and a.embel_name=3 and a.po_break_down_id in($po_break_down_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]] +=$row[csf('production_qnty')];
		}
		$table_width=(300+(count($sizearr_order)*60));
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                    <th width="100" >Color Name</th>
                    <th width="100">Production Type</th>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                        <?
                    }
                    ?>
                    <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="5" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                    <td>Order Qty.</td>
                    <?
					$total_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
				</tr>
				<tr>
					<td>Plan Cut Qty.</td>
					<?
					$total_plan_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0); $total_plan_ord_qnty+=$plan_order_size_qnty[$size_id]; ?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_plan_ord_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Wash Rcvd. Plan Qty.</td>
                    <?
					$total_plan_wash_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$total_cut_reject=$cut_panel_rejection+$chest_printing+$neck_sleeve_printing+$embroidery+$sewing_input;
						$print_enb_plan_cut=$plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100;
						echo number_format($print_enb_plan_cut,0); $total_plan_wash_qnty+=$print_enb_plan_cut;
						?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_plan_wash_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Wash Rcvd. Qty.</td>
                    <?
					$total_prod_wash_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format( $prod_color_size_data[$size_id],0); $total_prod_wash_qnty+=$prod_color_size_data[$size_id]; ?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_prod_wash_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Wash Rcvd. Balance</td>
                    <?
					$taoal_wash_balance=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$print_sent_balence=(($plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100)-$prod_color_size_data[$size_id]);
						 echo number_format($print_sent_balence,0); $taoal_wash_balance+=$print_sent_balence;
						?>
                         </td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($taoal_wash_balance,0); ?></td>
                </tr>
            </tbody>
		</table>
        </div>
    	<?
		
	}
	else if($prod_popup_type==7 && $prod_popup_lelel_type==1) //for iron
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
		from pro_garments_production_mst a,  pro_garments_production_dtls b 
		where a.id=b.mst_id and a.production_type=7 and a.po_break_down_id in($po_break_down_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]] +=$row[csf('production_qnty')];
		}
		$table_width=(300+(count($sizearr_order)*60));
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                    <th width="100" >Color Name</th>
                    <th width="100">Production Type</th>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                        <?
                    }
                    ?>
                    <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="5" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                    <td>Order Qty.</td>
                    <?
                    $total_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
				</tr>
				<tr>
					<td>Plan Cut Qty.</td>
					<?
					$total_plan_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0); $total_plan_ord_qnty+=$plan_order_size_qnty[$size_id]; ?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_plan_ord_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Iron Plan Qty</td>
                    <?
					$toal_plan_iron_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$total_cut_reject=$cut_panel_rejection+$chest_printing+$neck_sleeve_printing+$embroidery+$sewing_input+$garments_wash;
						$print_enb_plan_cut=$plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100;
						echo number_format($print_enb_plan_cut,0); $toal_plan_iron_qnty+=$print_enb_plan_cut;
						?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($toal_plan_iron_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Iron Qty.</td>
                    <?
					$total_prod_iron_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format( $prod_color_size_data[$size_id],0); $total_prod_iron_qnty+=$prod_color_size_data[$size_id];?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_prod_iron_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Iron Balance</td>
                    <?
					$total_iron_balance=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$print_sent_balence=(($plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100)-$prod_color_size_data[$size_id]);
						 echo number_format($print_sent_balence,0); $total_iron_balance+=$print_sent_balence;
						?>
                         </td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_iron_balance,0); ?></td>
                </tr>
            </tbody>
		</table>
        </div>
    	<?
	}
	else if($prod_popup_type==8 && $prod_popup_lelel_type==1) //for finish
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(b.production_qnty) as production_qnty
		from pro_garments_production_mst a,  pro_garments_production_dtls b 
		where a.id=b.mst_id and a.production_type=8 and a.po_break_down_id in($po_break_down_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]] +=$row[csf('production_qnty')];
		}
		
		$table_width=(300+(count($sizearr_order)*60));
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                    <th width="100" >Color Name</th>
                    <th width="100">Production Type</th>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                        <?
                    }
                    ?>
                    <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="5" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                    <td>Order Qty.</td>
                    <?
                    $total_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
				</tr>
				<tr>
					<td>Plan Cut Qty.</td>
					<?
					$total_plan_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($plan_order_size_qnty[$size_id],0); $total_plan_ord_qnty+=$plan_order_size_qnty[$size_id]; ?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_plan_ord_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Packing/Carton Plan Qty.</td>
                    <?
					$total_plan_finish_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$total_cut_reject=$cut_panel_rejection+$chest_printing+$neck_sleeve_printing+$embroidery+$sewing_input+$garments_wash+$gmts_finishing;
						$print_enb_plan_cut=$plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100;
						echo number_format($print_enb_plan_cut,0); $total_plan_finish_qnty+=$print_enb_plan_cut;
						?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_plan_finish_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Packing/Carton Qty.</td>
                    <?
					$total_prod_finish_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format( $prod_color_size_data[$size_id],0); $total_prod_finish_qnty+=$prod_color_size_data[$size_id];?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_prod_finish_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Packing/Carton  Balance</td>
                    <?
					$toal_finash_balance=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right">
						<?
						$print_sent_balence=(($plan_order_size_qnty[$size_id]-($plan_order_size_qnty[$size_id]*$total_cut_reject)/100)-$prod_color_size_data[$size_id]);
						 echo number_format($print_sent_balence,0); $toal_finash_balance+=$print_sent_balence;
						?>
                         </td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($toal_finash_balance,0); ?></td>
                </tr>
            </tbody>
		</table>
        </div>
    	<?
		
	}
	else if($prod_popup_type==10 && $prod_popup_lelel_type==1) //for garments Delivery
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id,
		sum(CASE WHEN a.entry_form!=85 THEN b.production_qnty ELSE 0 END)-sum(CASE WHEN a.entry_form=85 THEN b.production_qnty ELSE 0 END) as production_qnty
		from pro_ex_factory_mst a,  pro_ex_factory_dtls b
		where a.id=b.mst_id and a.po_break_down_id in($po_break_down_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id");
		$prod_color_size_data=array();
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]] +=$row[csf('production_qnty')];
		}
		
		$table_width=(300+(count($sizearr_order)*60));
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                    <th width="100" >Color Name</th>
                    <th width="100">Production Type</th>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                        <?
                    }
                    ?>
                    <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="3" align="center" valign="middle"><? echo $color_Arr_library[$color_id]; ?></td>
                    <td>Order Qty.</td>
                    <?
                    $total_ord_qnty=0;
					foreach($sizearr_order as $size_id)
					{
						?>
						<td align="right"><? echo number_format($order_size_qnty[$size_id],0); $total_ord_qnty+=$order_size_qnty[$size_id];?></td> 
						<?
					}
					?>
					<td align="right"><? echo number_format($total_ord_qnty,0); ?></td>
                </tr>
                
                <tr>
                    <td>Garments Delivery Qty.</td>
                    <?
					$total_delivery_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($prod_color_size_data[$size_id],0); $total_delivery_qnty+=$prod_color_size_data[$size_id];?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_delivery_qnty,0); ?></td>
                </tr>
                <tr>
                    <td>Gmts Deliv. Balance</td>
                    <?
					$total_delivery_balance=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? $delivery_balance=($order_size_qnty[$size_id]-$prod_color_size_data[$size_id]); echo number_format($delivery_balance,0); $total_delivery_balance+=$delivery_balance;?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_delivery_balance,0); ?></td>
                </tr>
            </tbody>
		</table>
    	</div>
    	<?
		
	}
	else if($prod_popup_type==11) //for emb sewing output
	{
		$country_cond="";
		if($country_id>0) $country_cond=" and a.country_id=$country_id";
		if($prod_popup_lelel_type=="") $prod_source_cond=''; 
		else 
		{
			$prod_source_cond=$prod_popup_lelel_type;
		}
		
		$sql_color_size= sql_select("SELECT b.color_size_break_down_id, sum(case when a.production_source='$prod_source_cond' then b.production_qnty else 0 end) as poly_qnty
		from pro_garments_production_mst a, pro_garments_production_dtls b 
		where a.id=b.mst_id and a.production_type=11 and a.po_break_down_id in($po_break_down_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_size_break_down_id ");
			
		$prod_color_size_data=array();
	
		foreach($sql_color_size as $row)
		{
			$prod_color_size_data[$color_size_data[$row[csf("color_size_break_down_id")]]["color_number_id"]][$color_size_data[$row[csf("color_size_break_down_id")]]["size_number_id"]]["poly_qnty"]+=$row[csf('poly_qnty')];
		}
		//print_r($prod_color_size_data);
		$table_width=(200+(count($sizearr_order)*60));
		
		?>
        <div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                    <th width="100" >Color Name</th>
                    <?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th width="60"><? echo $size_Arr_library[$size_id]; ?></th>
                        <?
                    }
                    ?>
                    <th width="100" >Total</th>
                </tr>
            </thead>
            <tbody>
            	<? foreach($allcolor_id_arr as $inc=>$color_id_val) { ?>
                <tr>
                    <td align="center" valign="middle"><? echo $color_Arr_library[$color_id_val]; ?></td>
                    <?
					$total_prod_poly_qnty=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($prod_color_size_data[$color_id_val][$size_id]["poly_qnty"],0); $total_prod_poly_qnty+=$prod_color_size_data[$color_id_val][$size_id]["poly_qnty"]; ?></td> 
                        <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_prod_poly_qnty,0); ?></td>
                </tr>
                <? } ?>
            </tbody>
		</table>
    	</div>
    	<?
	}
	exit(); 
}

?>