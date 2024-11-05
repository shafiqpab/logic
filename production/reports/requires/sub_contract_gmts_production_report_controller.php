<?php 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];




if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );     	 
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
	
	// $sql = "select a.party_id,a.style_ref_no,a.job_no,a.job_no_prefix_num,$select_date as year from wo_po_details_master a where a.company_name=$company $buyer_cond  $job_year_cond and is_deleted=0 order by job_no_prefix_num"; 
	//echo $sql; die;

	$sql="select a.id, b.cust_style_ref, a.job_no_prefix_num,$select_date as year from  subcon_ord_mst a, subcon_ord_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.company_id='$company' $buyer_cond $job_year_cond order by a.id desc";	
 //  echo $sql; die;
	echo create_list_view("list_view", "Style Ref No,Job No,Year","160,90,100","400","310",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "0", $arr, "cust_style_ref,job_no_prefix_num,year", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	echo "<input type='hidden' id='txt_year' />";
	
	?>
    <script language="javascript" type="text/javascript">
	var style_no='<? echo $txt_style_ref_no;?>';
	var style_id='<? echo $txt_style_ref_id;?>';
	var style_des='<? echo $txt_style_ref;?>';
	var year='<? echo $txt_year;?>';
	//alert(style_id);
	if(style_no!="")
	{
		style_no_arr=style_no.split(",");
		style_id_arr=style_id.split(",");
		style_des_arr=style_des.split(",");
		year_arr=year.split(",");
		var str_ref="";
		for(var k=0;k<style_no_arr.length; k++)
		{
			str_ref=style_no_arr[k]+'_'+style_id_arr[k]+'_'+style_des_arr[k]+'_'+year_arr[k];
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
	$cbo_job_year=str_replace("'","",$cbo_job_year);  
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_style_ref_id=str_replace("'","",$txt_style_ref_id);
	
	$shipping_status=str_replace("'","",$shipping_status);

	$txt_style_ref_number=str_replace("'","",$txt_style_ref_number);
	$cbo_style_owner_wise_status=str_replace("'","",$cbo_style_owner_wise_status);
	
	

	// echo $txt_order_id;die;
	

	
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

	
	
		//report order and color wise kaiyum
		$active_status=str_replace("'", "", $active_status);
		$active_cond="";
		if($active_status) $active_cond.=" and a.status_active=1 and d.status_active=1 ";

		$po_break_down_id=$txt_order_id ; 
		$company_name=$cbo_company_name;
		if ($txt_style_ref_number!="")
		{
			$txt_style_ref_number_cond="and e.style_ref_no like '%$txt_style_ref_number%'";
		}
		else	
		{
			$txt_style_ref_number_cond="";
		}
		if($cbo_job_year>0)
		{
			if($db_type==0)
			{
				$cbo_job_year_cond =" and year(f.insert_date)='$cbo_job_year'";
			}
			else
			{
				$cbo_job_year_cond =" and to_char(f.insert_date,'YYYY')='$cbo_job_year'";
			}	
		}
			
		
		 if($txt_style_ref!="")
		{
			$txt_style_ref_id_cond =" and f.job_no_prefix_num in($txt_style_ref)";
		}
		
		if($cbo_style_owner_wise_status==2)
		{
			if($cbo_company_name!=0) $cbo_style_owner_wise_status_cond=" and e.style_owner=$cbo_company_name";	
		}
		else
		{
			if($cbo_company_name!=0) $cbo_style_owner_wise_status_cond=" and e.company_name=$cbo_company_name";
		}
		
		
		
		if($po_break_down_id!="" )  $po_break_down_id_cond =" and a.po_break_down_id in (".$po_break_down_id.") ";
		$po_break_down_id_cond_new=($po_break_down_id!="") ?" and x.po_break_down_id in($po_break_down_id) " : "";

		


		if($cbo_buyer_name!=0 )  $cbo_buyer_name_cond =" and f.party_id =$cbo_buyer_name"; 

		if($txt_datefrom!="" &&  $txt_dateto!="")  $txt_datefrom_to_cond =" and production_date between '$txt_datefrom' and '$txt_dateto'";  

		if($txt_datefrom!="" &&  $txt_dateto!="")  	$date_con =" and a.production_date between '$txt_datefrom' and '$txt_dateto'";  
		$size_Arr_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	
		$color_variable_setting=return_field_value("ex_factory","variable_settings_production","company_name='$company_name' and variable_list=1 and status_active=1 and is_deleted=0","ex_factory");
		$color_variable_setting = ($color_variable_setting==0) ? 3 : $color_variable_setting;
		$ex_fact_qty_arr=array();
		if($color_variable_setting==2 || $color_variable_setting==3)
		{
		 	$sql_exfect="SELECT a.po_break_down_id,a.item_number_id, a.color_number_id, a.size_number_id, sum(y.production_qnty) as production_qnty from pro_ex_factory_mst x,pro_ex_factory_dtls y,wo_po_color_size_breakdown a,wo_po_break_down d,wo_po_details_master e 
			where  x.id=y.mst_id and y.color_size_break_down_id=a.id   and x.status_active=1 and x.is_deleted=0 and y.status_active=1 and y.is_deleted=0   and a.color_number_id>0 and a.size_number_id>0 $po_break_down_id_cond and a.po_break_down_id=d.id and a.job_no_mst=e.job_no and e.company_name=$cbo_company_name  $txt_datefrom_to_cond $txt_style_ref_number_cond  $cbo_buyer_name_cond $cbo_location_cond $cbo_job_year_cond $txt_style_ref_id_cond $cbo_style_owner_wise_status_cond $shipping_status_cond $internal_ref_cond_po group by  a.color_number_id,a.size_number_id,a.po_break_down_id,a.item_number_id order by a.po_break_down_id,a.color_number_id, a.size_number_id";
		
			//echo $sql_exfect."<br>";
			$sql_result_exfact=sql_select($sql_exfect);
			foreach($sql_result_exfact as $row)
			{
				$ex_fact_qty_arr[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['ex_fact_qty']=$row[csf("production_qnty")];
			}
		}
	

		//echo "<pre>";
		 //print_r($color_size_qnty_prod); die;

		 $cut_qty_data=sql_select("select c.color_id,a.order_id, c.size_id, a.id, a.company_id, a.production_date, a.production_qnty, a.reject_qnty, a.production_type, b.production_type, b.gmts_item_id, b.production_date, b.ord_color_size_id, b.prod_qnty from  subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz b,subcon_ord_breakdown c  where a.id=b.dtls_id and a.production_date=b.production_date and a.status_active=1 and a.is_deleted=0 and a.production_type in (1)  and c.id=b.ord_color_size_id $date_con 
		 order by c.color_id,c.id");

		 $color_size_qty=array();
		foreach($cut_qty_data as $row){

			$color_size_cutqty[$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("size_id")]] +=$row[csf("prod_qnty")];
		}

	

		$s_in_qty_data=sql_select("select c.color_id,a.order_id, c.size_id, a.id, a.company_id, a.production_date, a.production_qnty, a.reject_qnty, a.production_type, b.production_type, b.gmts_item_id, b.production_date, b.ord_color_size_id, b.prod_qnty from  subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz b,subcon_ord_breakdown c  where a.id=b.dtls_id and a.production_date=b.production_date and a.status_active=1 and a.is_deleted=0 and a.production_type in (7)  and c.id=b.ord_color_size_id $date_con 
		order by c.color_id,c.id");

		foreach($s_in_qty_data as $row){

			$color_size_s_in_qty[$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("size_id")]] +=$row[csf("prod_qnty")];
		}
 
		//   echo "<pre>";
		//  print_r($color_size_s_in_qty); die;
		$s_out_qty_data=sql_select("select c.color_id,a.order_id, c.size_id, a.id, a.company_id, a.production_date, a.production_qnty, a.reject_qnty, a.production_type, b.production_type, b.gmts_item_id, b.production_date, b.ord_color_size_id, b.prod_qnty from  subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz b,subcon_ord_breakdown c  where a.id=b.dtls_id and a.production_date=b.production_date and a.status_active=1 and a.is_deleted=0 and a.production_type in (2)  and c.id=b.ord_color_size_id $date_con 
		order by c.color_id,c.id");

		foreach($s_out_qty_data as $row){

			$color_size_s_out_qty[$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("size_id")]] +=$row[csf("prod_qnty")];
		}
		
		$poly_out_qty_data=sql_select("select c.color_id,a.order_id, c.size_id, a.id, a.company_id, a.production_date, a.production_qnty, a.reject_qnty, a.production_type, b.production_type, b.gmts_item_id, b.production_date, b.ord_color_size_id, b.prod_qnty from  subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz b,subcon_ord_breakdown c  where a.id=b.dtls_id and a.production_date=b.production_date and a.status_active=1 and a.is_deleted=0 and a.production_type in (5)  and c.id=b.ord_color_size_id $date_con 
		order by c.color_id,c.id");

		foreach($poly_out_qty_data as $row){

			$color_size_poly_out_qty[$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("size_id")]] +=$row[csf("prod_qnty")];
		}

		$packing_fin_qty_data=sql_select("select c.color_id, c.size_id, c.qnty, c.plan_cut, a.id, a.company_id, a.order_id, a.production_date, a.production_qnty, a.production_type,  b.production_type, b.gmts_item_id, b.production_date, b.ord_color_size_id, b.prod_qnty from subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz b,subcon_ord_breakdown c where a.id=b.dtls_id and a.production_date=b.production_date and a.status_active=1 and a.is_deleted=0 and a.production_type='4' $date_con and c.id=b.ord_color_size_id order by c.color_id,c.id");

		foreach($packing_fin_qty_data as $row){

			$packing_out_qty_data[$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("size_id")]] +=$row[csf("prod_qnty")];
		}

		
		// $ex_out_qty_data=sql_select("select item_id,order_id ,sum(delivery_qty) as cut_qty from subcon_gmts_prod_dtls where   is_deleted=0 $txt_datefrom_to_cond group by item_id,order_id ,delivery_qty");
		$ex_out_qty_data=sql_select("select a.id,b.delivery_qty,a.size_id,a.order_id, a.color_id from  subcon_ord_breakdown a,subcon_gmts_delivery_dtls b where  a.id=b.breakdown_color_size_id");
		foreach($ex_out_qty_data as $row){

			$ex_out_qty_arr[$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("size_id")]] +=$row[csf("delivery_qty")];
		}
		// print_r($ex_out_qty_arr);

		$color_size_sql=sql_select("SELECT f.id,h.item_id,h.color_id,h.size_id,g.order_quantity, g.job_no_mst as job_no,f.company_id,f.party_id,g.cust_buyer,g.cust_style_ref,g.id as po_break_id,g.order_no,h.qnty,a.production_date from subcon_ord_mst f,subcon_ord_dtls g,subcon_ord_breakdown h,subcon_gmts_prod_dtls a where  f.id=g.mst_id and g.id=a.order_id  and g.id=h.order_id and f.status_active =1 and g.status_active =1  and h.status_active =1 and a.status_active =1  and f.company_id=$cbo_company_name $txt_style_ref_id_cond $cbo_job_year_cond $cbo_buyer_name_cond $order_no_cond $date_con order by h.size_id asc");

		
 
	
		$job_po_color_size_data=array();$color_size_qnty=array();
		$all_po_arr=array();
		$job_data_arr=array();
		$oldid='';
		foreach($color_size_sql as $row)
		{
			//$test_data[]=$row[csf("color_number_id")];
			$job_data_arr[$row[csf("job_no")]]['cust_party']=$row[csf("party_id")];
			$job_data_arr[$row[csf("job_no")]]['company_name']=$row[csf("company_id")];
			$job_data_arr[$row[csf("job_no")]]['cust_style_ref']=$row[csf("cust_style_ref")];
			$job_data_arr[$row[csf("job_no")]]['cust_buyer']=$row[csf("cust_buyer")];
			$job_data_arr[$row[csf("job_no")]]['order_no']=$row[csf("order_no")];

		
				$job_po_color_size_data[$row[csf("job_no")]][$row[csf("po_break_id")]][$row[csf("item_id")]][$row[csf("color_id")]][$row[csf("size_id")]]['style_ref_no']=$row[csf("cust_style_ref")];
				$job_po_color_size_data[$row[csf("job_no")]][$row[csf("po_break_id")]][$row[csf("item_id")]][$row[csf("color_id")]][$row[csf("size_id")]]['order_quantity']=$row[csf("qnty")];
					$job_po_color_size_data[$row[csf("job_no")]][$row[csf("po_break_id")]][$row[csf("item_id")]][$row[csf("color_id")]][$row[csf("size_id")]]['plan_cut_qnty']=$row[csf("plan_cut_qnty")];
				//$po_color_size_data[$row[csf("po_break_down_id")]][$row[csf("size_number_id")]]['color_number_id']=$row[csf("color_number_id")];
				$all_po_arr[$row[csf("po_break_id")]]=$row[csf("po_break_id")];
				$oldid=$row[csf("job_no")];
		

		}
		
		
	
		
		if($db_type==2 && count($all_po_arr)>999)
		{
			$all_po_id_chunk=array_chunk($all_po_arr,999) ;
			$all_po_id_cond="";
			foreach($all_po_id_chunk as $chunk_arr)
			{
				$ids=implode(",",$chunk_arr);
				if(!$all_po_id_cond) $all_po_id_cond.=" and ( b.order_id in($ids) ";
				else $all_po_id_cond.=" or g.order_no in($ids) ";

			}

			$all_po_id_cond.=" )";			

		}
		else
		{ 	
			$all_po_ids=implode(",",$all_po_arr);
			$all_po_id_cond=" and g.order_no in($all_po_ids)";  
		}

		$lay_sql="SELECT a.gmt_item_id,a.color_id,b.order_id, b.size_id,sum(b.size_qty) as qnty from ppl_cut_lay_dtls a,ppl_cut_lay_bundle b where a.id=b.dtls_id and a.status_active=1 and b.status_active=1 $all_po_id_cond group by a.gmt_item_id,a.color_id,b.order_id, b.size_id ";
		foreach(sql_select($lay_sql) as $rows)
		{
			$color_size_qnty_lay[$rows[csf("order_id")]][$rows[csf("gmt_item_id")]][$rows[csf("color_id")]][$rows[csf("size_id")]]['lay_qty']+=$rows[csf("qnty")];
		}

		
		$all_po_id_cond3=str_replace('b.order_id','b.id',$all_po_id_cond); 


		ob_start();		
	
		?><br>
		<div style="height:auto; width:2060px; margin:0 auto; padding:0;" align="center" >
		<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="2060" >
				<h1>Sub-Contract GMTS Production Report</h1>
				<h2><?=$company_library[$cbo_company_name];?></h2>
				<p>Date:<?=$txt_date_from." To ".$txt_date_to;?></p>
				</table>
		</div>
		<br>


    	<div id="scroll_body" style="height:auto; width:2060px; margin:0 auto; padding:0;">
    	<fieldset>
    	<legend> Color and Size Wise</legend>
    	<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="2060" align="left">
		<!--<div id="scroll_body" style="overflow-y: scroll; width: 1620px; overflow-x: auto;">
    	<table width="1610" cellspacing="0" border="1" class="rpt_table" rules="all" id="">-->
		<!-- <caption><h2>Order and Size - Style NO: <? echo $color_size_sql[0][csf('style_ref_no')] ;?> </h2></caption> -->
         <thead> 
        	<tr>
     		    <th width="" colspan="13"></th>

                <th width="" colspan="11"></th>
           </tr>
        </thead>
        <thead> 
        	<tr>
            	<th width="30">SL</th>
				<th width="200">Company Name</th>
				<th width="80">Party Name</th>
				<th width="80">Cust Buyer</th>
				<th width="80">Cust Style Ref</th>
            	<th width="200">PO</th>
            	<th width="200">Garments Item</th>
                <th width="200">Color</th>
     		    <th width="60">Size</th>
                <th width="60">Order Total</th>
              
                <th width="60">Cutt/Qc Total</th>
     		    <th width="80">Cutt Short/excess</th>
                <th width="60">Inp Total</th>
     		    <th width="80">Inp Short/Excess</th>
                <th width="60">Sewing Output Total</th>
                <th width="80">Output Short/excess</th>
     		    <th width="60">Poly Out Total</th>
                <th width="80">Poly Output Short/excess</th>
                <th width="60"><p>Total Packing and Finishing</p></th>
     		    <th width="80"><p>Packing and finishing Short/excess</p></th>
                <th width="60">Total Ex Factory</th>              
     		    <th>Exfactory Short/excess</th>
                
             </tr>
        </thead>
		</table>
		<div style="width:2080px; max-height:410px; overflow-y: scroll;" id="scroll_body">
		 	<table cellspacing="0" cellpadding="0"  width="2060"  border="1" rules="all" class="rpt_table" id="" >
			  	<tbody>  
		         <!--       
		           </tr>
		        </thead>
				</table>

				<table border="1" class="rpt_table" width="1610" rules="all" id="" >-->
			    <?
			    /*foreach($color_size_sql as $row)
				{*/
				$i=0;
				$garments_item_desc=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
			
				foreach($job_po_color_size_data as $job_id=>$po_color_size_data){
				foreach($po_color_size_data as $poID=>$breakDown_id)
				{					
					foreach($breakDown_id as $itemID=> $items)
					{
						$item_total_order_qty="";
						$item_total_lay_qty=0;
						$item_total_cutt_qty="";
						$item_total_cuttShortEx_qty="";
						
					
						
						$item_total_input_qty="";
						$item_total_inputShortEx_qty="";
						$item_total_sewing_qty="";
						$item_total_swoutShortEx_qty="";
						$item_total_poly_qty="";
						$item_total_polyShortEx_qty="";
						$item_total_carton_qty="";
						$item_total_cartonShortEx_qty="";
						$item_total_exfact_qty="";
						$item_total_fg_qty="";
						$item_total_exfactShortEx_qty="";
						//$gTotal_inputShortEx_qty="";
						foreach($items as $colorID=> $colors)
						{
							$total_order_qty="";
							$total_lay_qty=0;
							$total_cutt_qty="";
							$total_cuttShortEx_qty="";
							
						
							$total_input_qty="";
							$total_inputShortEx_qty="";
							$total_sewing_qty="";
							$total_swoutShortEx_qty="";
							$total_poly_qty="";
							$total_polyShortEx_qty="";
							$total_carton_qty="";
							$total_cartonShortEx_qty="";
							$total_exfact_qty="";
							$total_fg_qty="";
							$total_exfactShortEx_qty="";
							//$gTotal_inputShortEx_qty="";
							
							foreach($colors as $sizeNoID=> $val)
							{
								
								
								$bgcolor = ($i%2) ? "#ebf3ff" : "#ffffff";
								$i++;
								?>
						        <tr onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" bgcolor="<?echo $bgcolor;?>">
						        	<td width="30" align="left"><? echo $i; ?></td>	
									<td width="200" align="left"><? echo $company_library[$job_data_arr[$job_id]['company_name']];?></td>
									<td width="80" align="right" style="color:<? echo $bg_tdColor; ?>"><? echo $buyer_short_library[$job_data_arr[$job_id]['cust_party']];?></td>
									<td width="80" align="right" style="color:<? echo $bg_tdColor; ?>"><? echo $job_data_arr[$job_id]['cust_buyer'];?></td>
									<td width="80" align="right" style="color:<? echo $bg_tdColor; ?>"><? echo $job_data_arr[$job_id]['cust_style_ref'];?></td>
									<td width="200" align="left"><?echo $job_data_arr[$job_id]['order_no']; ?></td>
									<td width="200" align="left"><? echo $garments_item[$itemID]; ?></td>
						            <td width="200"  align="left"><? echo  $color_Arr_library[$colorID]; //$color_Arr_library[$color_size_qnty_prod[$poID][$colorID][$sizeNoID]['color_idd']]; ?></td> 
						            <td width="60" align="left"><p><? echo $size_Arr_library[$sizeNoID]; ?>&nbsp;</p></td>
						            <td width="60" align="right"><? echo $po_color_size_data[$poID][$itemID][$colorID][$sizeNoID]['order_quantity']; ?></td>
						            
						            <td width="60" align="right"><? 
							
								// $color_size_qnty_prod[$poID][$itemID][$colorID][$sizeNoID]['quantity_qc']; 
							
								$cuttQty=$color_size_cutqty[$poID][$colorID][$sizeNoID];
									echo 	$cuttQty; ?></td>
							            <?
										$cut_shortExc= $po_color_size_data[$poID][$itemID][$colorID][$sizeNoID]['order_quantity']-$color_size_cutqty[$poID][$colorID][$sizeNoID]; 
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
						            <td width="80" align="right" style="color:<? echo $bg_tdColor; ?>"><? echo $cut_shortExc;?></td>
						            
						            
						           
						           
						            <td width="60" align="right"><? 
									// echo $inputQty=$color_size_qnty_prod[$poID][$itemID][$colorID][$sizeNoID]['quantity_swinput']; 
										$inputQty=$color_size_s_in_qty[$poID][$colorID][$sizeNoID]; 
									echo	$color_size_s_in_qty[$poID][$colorID][$sizeNoID];
									?></td>
							            <?
										$qtyCuttQC=$color_size_cutqty[$poID][$colorID][$sizeNoID]; 
										$swInPut=$color_size_s_in_qty[$poID][$colorID][$sizeNoID];
										
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
						            <td width="80" align="right"  style="color:<? echo $bg_tdColor; ?>"><? echo $input_shortExc; ?></td>
						            <td width="60" align="right"><?
									//  echo $swoutQty=$color_size_qnty_prod[$poID][$itemID][$colorID][$sizeNoID]['quantity_swoutput']; 
												 $swoutQty=$color_size_s_out_qty[$poID][$colorID][$sizeNoID]; 
											 echo $swoutQty; 
									 ?></td>
							             <?
										$output_shortExc=$color_size_s_in_qty[$poID][$colorID][$sizeNoID]-$color_size_s_out_qty[$poID][$colorID][$sizeNoID];
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
						            <td width="80" align="right" style="color:<? echo $bg_tdColor; ?>"><? echo $output_shortExc ?></td>
						            <td width="60" align="right"><?
									
										  $polyQty=$color_size_poly_out_qty[$poID][$colorID][$sizeNoID];
									echo $color_size_poly_out_qty[$poID][$colorID][$sizeNoID];
									 ?></td>
							             <?
										$poly_output_shortExc=$color_size_s_out_qty[$poID][$colorID][$sizeNoID]-$color_size_poly_out_qty[$poID][$colorID][$sizeNoID];
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
						            <td width="80" align="right" style="color:<? echo $bg_tdColor; ?>"><? echo $poly_output_shortExc; ?></td>
						            <td width="60" align="right"><?
									   $cartonQty=$packing_out_qty_data[$poID][$colorID][$sizeNoID];
								
									 echo  $cartonQty;
									 ?></td>
							             <?
										$carton_shortExc=$color_size_poly_out_qty[$poID][$colorID][$sizeNoID]-$color_size_packing_out_qty[$poID][$itemID];
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
						            <td width="80" align="right" style="color:<? echo $bg_tdColor; ?>"><? echo $carton_shortExc; ?></td>
						            <td width="60" align="right"><?
									//  echo $exFacQty=$ex_fact_qty_arr[$poID][$itemID][$colorID][$sizeNoID]['ex_fact_qty']; 
									$exFacQty=$ex_out_qty_arr[$poID][$colorID][$sizeNoID];
									 echo $exFacQty;
									 ?></td>
						     

							             <?
										$exFactory_shortExc=$po_color_size_data[$poID][$itemID][$colorID][$sizeNoID]['order_quantity']-$ex_out_qty_arr[$poID][$colorID][$sizeNoID]; 
						
										
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
						            <td align="right" style="color:<? echo $bg_tdColor; ?>"><? echo $exFactory_shortExc; ?></td>
								</tr>
							    <?
								$total_order_qty+=$po_color_size_data[$poID][$itemID][$colorID][$sizeNoID]['order_quantity'];
							
								$total_lay_qty+=$layQty;
								$total_cutt_qty+=$cuttQty;
								$total_cuttShortEx_qty+=$po_color_size_data[$poID][$itemID][$colorID][$sizeNoID]['order_quantity']-$color_size_cutqty[$poID][$colorID][$sizeNoID];
						
								
								$total_input_qty+=$inputQty;
								$total_inputShortEx_qty+=$qtyCuttQC-$swInPut;
								$total_sewing_qty+=$swoutQty;
								$total_swoutShortEx_qty+=$color_size_s_in_qty[$poID][$colorID][$sizeNoID]-$color_size_s_out_qty[$poID][$colorID][$sizeNoID];
								$total_poly_qty+=$polyQty;
								$total_polyShortEx_qty+=$color_size_s_out_qty[$poID][$colorID][$sizeNoID]-$color_size_poly_out_qty[$poID][$colorID][$sizeNoID];
								$total_carton_qty+=$cartonQty;
								$total_cartonShortEx_qty+=$color_size_poly_out_qty[$poID][$colorID][$sizeNoID]-$color_size_packing_out_qty[$poID][$itemID]; 
								$total_exfact_qty+=$exFacQty;
								$total_fg_qty+=$fg_inhand_qty;
								$total_exfactShortEx_qty+=$po_color_size_data[$poID][$itemID][$colorID][$sizeNoID]['order_quantity']-$ex_out_qty_arr[$poID][$colorID][$sizeNoID];

								// ====================== item wise ==================================
								$item_total_order_qty+=$po_color_size_data[$poID][$itemID][$colorID][$sizeNoID]['order_quantity'];
							
								$item_total_lay_qty+=$layQty;
								$item_total_cutt_qty+=$cuttQty;
								$item_total_cuttShortEx_qty+=$po_color_size_data[$poID][$itemID][$colorID][$sizeNoID]['order_quantity']-$color_size_cutqty[$poID][$itemID];
								
								$item_total_emblPrint+=$emblishMentPrint;
								$item_total_emblEmbro+=$emblishMentEmbro;
								$item_total_emblSPwork+=$emblishMentSPwork;
								
								$item_total_input_qty+=$inputQty;
								$item_total_inputShortEx_qty+=$qtyCuttQC-$swInPut;
								$item_total_sewing_qty+=$swoutQty;
								$item_total_swoutShortEx_qty+=$color_size_s_in_qty[$poID][$colorID][$sizeNoID]-$color_size_s_out_qty[$poID][$colorID][$sizeNoID];
								$item_total_poly_qty+=$polyQty;
								$item_total_polyShortEx_qty+=$color_size_s_out_qty[$poID][$colorID][$sizeNoID]-$color_size_poly_out_qty[$poID][$colorID][$sizeNoID];
								$item_total_carton_qty+=$cartonQty;
								$item_total_cartonShortEx_qty+=$color_size_poly_out_qty[$poID][$colorID][$sizeNoID]-$color_size_packing_out_qty[$poID][$itemID];
								$item_total_exfact_qty+=$exFacQty;
								$item_total_fg_qty+=$fg_inhand_qty;
								$item_total_exfactShortEx_qty+=$po_color_size_data[$poID][$itemID][$colorID][$sizeNoID]['order_quantity']-$ex_out_qty_arr[$poID][$colorID][$sizeNoID];
							}
							?>
					        <tr class="gd-color3">
					        	<td width="30" align="left"><b>&nbsp;</b></td>
								<td width="200" align="left"><b>&nbsp;</b></td>
								<td width="80" align="right" style="color:<? echo $bg_tdColor; ?>"><b>&nbsp;</b></td>
								<td width="80" align="right" style="color:<? echo $bg_tdColor; ?>"><b>&nbsp;</b></td>
								<td width="80" align="right" style="color:<? echo $bg_tdColor; ?>"><b><? //echo $total_cuttShortEx_qtyy; ?></b></td>
					        	<td width="100" align="left"><b><?// echo $po_no_library[$poID]; ?></b></td>
					        	<td width="200" align="left"><b><? echo $garments_item[$itemID]; ?></b></td>
					            <td width="200" colspan="2" align="right"><b>Color Wise PO Total=</b></td>
					            
					            <td width="60" align="right"><b><? echo $total_order_qty; ?></b></td>
					 
					            <td width="60" align="right"><b><? echo $total_cutt_qty; ?></b></td>
					            
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
					            <td width="80" align="right" style="color:<? echo $bg_tdColor; ?>"><b><? echo $total_cuttShortEx_qtyy; ?></b></td>
					            
					          
					          
					            <td width="60" align="right"><b><? echo $total_input_qty; ?></b></td>
					           <?  $gTotal_inputShortEx_qty=$total_inputShortEx_qty; if($total_inputShortEx_qty<0){  $total_inputShortEx_qty=abs($total_inputShortEx_qty); $total_inputShortEx_qty='('.$total_inputShortEx_qty.')'; $bg_tdColor='red'; } else {  $total_inputShortEx_qty;$bg_tdColor='';  } ?>
					           
					            <td style="color:<? echo $bg_tdColor ?>" width="80" align="right"><b><?
								  echo $total_inputShortEx_qty; $total_inputShortEx_qty='';
								 ?></b></td>
					            <td width="60" align="right"><b><? echo $total_sewing_qty; ?></b></td>
					            <td width="80" align="right"><b><? echo $total_swoutShortEx_qty; ?></b></td>
					            <td width="60" align="right"><b><? echo $total_poly_qty; ?></b></td>
					            
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
					            
					            <td width="80" align="right" style="color:<? echo $bg_tdColor ?>"><b><? echo $total_polyShortEx_qtyy; ?></b></td>
					            <td width="60" align="right"><b><? echo $total_carton_qty; ?></b></th>
					            
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
					            
					            
					            <td width="80" align="right" style="color:<? echo $bg_tdColor ?>"><b><? echo $total_cartonShortEx_qtyy; ?></b></td>
					            <td width="60" align="right"><b><? echo $total_exfact_qty; ?></b></td>
					           
					            
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
					            
					            <td align="right" style="color:<? echo $bg_tdColor ?>"><b><? echo $total_exfactShortEx_qtyy; ?></b></td>
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
							$grandTotal_fg_qty+=$total_fg_qty;
							$total_exfact_qty=0;
							$grandTotal_exfactShortEx_qty+=$total_exfactShortEx_qty;
							$total_exfactShortEx_qty=0;
						}

						?>
					    <tr class="gd-color">
				        	<td width="30" align="left"><b></b></td>
							<td width="200" align="left"><b>&nbsp;</b></td>
							<td width="80" align="right" style="color:<? echo $bg_tdColor; ?>"><b>&nbsp;</b></td>
							<td width="80" align="right" style="color:<? echo $bg_tdColor; ?>"><b>&nbsp;</b></td>
							<td width="80" align="right" style="color:<? echo $bg_tdColor; ?>"><b>&nbsp;</b></td>
				        	<td width="100" align="left"><b><? echo $po_no_library[$poID]; ?></b></td>
				        	<td width="200" align="left"><b><? echo $garments_item[$itemID]; ?></b></td>
				            <td width="200" colspan="2" align="right"><b>Item Wise PO Total=</b></td>
				            
				            <td width="60" align="right"><b><? echo $item_total_order_qty; ?></b></td>
				          
				            <td width="60" align="right"><b><? echo $item_total_cutt_qty; ?></b></td>
				            
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
				            <td width="80" align="right" style="color:<? echo $bg_tdColor; ?>"><b><? echo $item_total_cuttShortEx_qtyy; ?></b></td>
				            
				            
				           
				            <td width="60" align="right"><b><? echo $item_total_input_qty; ?></b></td>
				           <?  $gTotal_inputShortEx_qty=$item_total_inputShortEx_qty; if($item_total_inputShortEx_qty<0){  $item_total_inputShortEx_qty=abs($item_total_inputShortEx_qty); $total_inputShortEx_qty='('.$total_inputShortEx_qty.')'; $bg_tdColor='red'; } else {  $total_inputShortEx_qty;$bg_tdColor='';  } ?>
				           
				            <td style="color:<? echo $bg_tdColor ?>" width="80" align="right"><b><?
							  echo $item_total_inputShortEx_qty; $item_total_inputShortEx_qty='';
							 ?></b></td>
				            <td width="60" align="right"><b><? echo $item_total_sewing_qty; ?></b></td>
				            <td width="80" align="right"><b><? echo $item_total_swoutShortEx_qty; ?></b></td>
				            <td width="60" align="right"><b><? echo $item_total_poly_qty; ?></b></td>
				            
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
				            
				            <td width="80" align="right" style="color:<? echo $bg_tdColor ?>"><b><? echo $item_total_polyShortEx_qtyy; ?></b></td>
				            <td width="60" align="right"><b><? echo $item_total_carton_qty; ?></b></th>
				            
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
				            
				            
				            <td width="80" align="right" style="color:<? echo $bg_tdColor ?>"><b><? echo $item_total_cartonShortEx_qtyy; ?></b></td>
				            <td width="60" align="right"><b><? echo $item_total_exfact_qty; ?></b></td>
				            
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
				            
				            <td align="right" style="color:<? echo $bg_tdColor ?>"><b><? echo $item_total_exfactShortEx_qtyy; ?></b></td>
				        </tr>
				        <?				        
					} // end item wise
				
				
				} 
			}// end po wise
				//  grand total bottom part
				?>
		 		<tr class="gd-color2">
		            <td width="200" colspan="9" align="right"><b>Grand Total </b></td>
		            <td width="60" align="right"><b><? echo $grandTotal_order_qty; ?></b></td>
		           
		            <td width="60" align="right"><b><? echo $grandTotal_cutt_qty; ?></b></td>
		            
		            
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
		            
		            <td width="80" align="right" style="color:<? echo $bg_tdColor; ?>"><b><? echo $grandTotal_cuttShortEx_qty; ?></b></td>
		          
		            
		            
		            <td width="60" align="right"><b><? echo $grandTotal_input_qty; ?></b></td>
		           <? if($grandTotal_inputShortEx_qty<0){  $grandTotal_inputShortEx_qty=abs($grandTotal_inputShortEx_qty); $grandTotal_inputShortEx_qty='('.$grandTotal_inputShortEx_qty.')'; $bg_tdColor='red'; } else {  $grandTotal_inputShortEx_qty;$bg_tdColor='';  } ?>
		            
		            <td style="color:<? echo $bg_tdColor ?>" width="80" align="right"><b><?
					  echo $grandTotal_inputShortEx_qty; 
					 ?></b></td>
		            <td width="60" align="right"><b><? echo $grandTotal_sewing_qty; ?></b></td>
		            <td width="80" align="right"><b><? echo $grandTotal_swoutShortEx_qty; ?></b></td>
		            <td width="60" align="right"><b><? echo $grandTotal_poly_qty; ?></b></td>
		            
		               
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
		            
		            
		            <td width="80" align="right" style="color:<? echo $bg_tdColor ?>"><b><? echo $grandTotal_polyShortEx_qty; ?></b></td>
		            <td width="60" align="right"><b><? echo $grandTotal_carton_qty; ?></b></th>
		            <td width="80" align="right"><b><? echo $grandTotal_cartonShortEx_qty; ?></b></td>
		            <td width="60" align="right"><b><? echo $grandTotal_exfact_qty; ?></b></td>
		        
		            
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
		            
		            <td align="right" style="color:<? echo $bg_tdColor ?>"><b><? echo $grandTotal_exfactShortEx_qty; ?></b></td>
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
	
	
	// $new_link=create_delete_report_file( $html, 1, 1, "../../../" );	
	// echo "$html**$type";
/*	foreach (glob("$user_id*.xls") as $filename) 
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
*/	
	
	
	$total_data=ob_get_contents();
	$xlHtml=$total_data;
	ob_clean();
	
	if($type<1){
		$xlHtml=  strip_tags($xlHtml, '<table></table><thead></thead><tbody></tbody><tfoot></tfoot><tr></tr><th></th><td></td>');
	}

	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename,'w');
	$is_created = fwrite($create_new_doc,$xlHtml);
	echo "$total_data####$filename####$type";
	exit();		
	
	
}

?>