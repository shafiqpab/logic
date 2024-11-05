<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );     	 
	exit();
}

if($action=="openJobNoPopup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
    <script>
		
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
    	function check_all_data() 
    	{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				if($('#tr_' + i).is(':visible'))
				{
					var onclickString = $('#tr_' + i).attr('onclick');
					var paramArr = onclickString.split("'");
					var functionParam = paramArr[1];
					js_set_value( functionParam );
				}
				
			}
		}
		
		function toggle( x, origColor ) 
		{
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
				$('#txt_selected_po').val( id );
				$('#txt_selected_job').val( name ); 
				$('#txt_selected_no').val( num );
		}
    </script>
    <?
	$buyer=str_replace("'","",$buyer);
	$lc_company=str_replace("'","",$lc_company);
	if($db_type==0)
	{
		$select_date=" year(b.insert_date)";
	}
	else if($db_type==2)
	{		
		$select_date=" to_char(b.insert_date,'YYYY')";
	}

	if($lc_company!=0) $lc_company_cond="and b.company_name=$lc_company"; else $lc_company_cond="";
	if($buyer!=0) $buyer_cond="and b.buyer_name=$buyer"; else $buyer_cond="";	
	if($year!=0) $year_cond="and to_char(b.insert_date,'YYYY')=$year"; else $year_cond="";	
	
	$sql = "SELECT a.id,a.po_number,a.job_no_mst,b.style_ref_no,b.job_no_prefix_num,$select_date as year from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no $lc_company_cond $buyer_cond $year_cond and a.status_active in(1,2,3) and b.status_active=1"; 
	//echo $sql; die;
	echo create_list_view("list_view", "Year,Job No,Style Ref No,Order NO","70,50,150,150","500","310",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "0", $arr, "year,job_no_prefix_num,style_ref_no,po_number", "","setFilterGrid('list_view',-1)","0","",1) ;	

	echo "<input type='hidden' id='txt_selected_po' />";
	echo "<input type='hidden' id='txt_selected_job' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	exit();
}

if($action=="style_ref_no_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
    <script>
		
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
    	function check_all_data() 
    	{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				if($('#tr_' + i).is(':visible'))
				{
					var onclickString = $('#tr_' + i).attr('onclick');
					var paramArr = onclickString.split("'");
					var functionParam = paramArr[1];
					js_set_value( functionParam );
				}
				
			}
		}
		
		function toggle( x, origColor ) 
		{
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
				$('#txt_selected_po').val( id );
				$('#txt_selected_style').val( name ); 
				$('#txt_selected_no').val( num );
		}
    </script>
    <?
	$buyer=str_replace("'","",$buyer);
	$w_company=str_replace("'","",$w_company);
	$lc_company=str_replace("'","",$lc_company);
	$job_year=str_replace("'","",$job_year);
	$txt_style_ref_id=str_replace("'","",$txt_style_ref_id);
	
	if($lc_company!=0) $lc_company_cond="and b.company_name=$lc_company"; else $lc_company_cond="";
	if($buyer!=0) $buyer_cond="and b.buyer_name=$buyer"; else $buyer_cond="";	
	if($year!=0) $year_cond="and to_char(b.insert_date,'YYYY')=$year"; else $year_cond="";	

	if($db_type==0)
	{
		$select_date=" year(b.insert_date)";
	}
	else if($db_type==2)
	{
		$select_date=" to_char(b.insert_date,'YYYY')";
	}
	
	$sql = "SELECT a.id,a.po_number,a.job_no_mst,b.style_ref_no,b.job_no_prefix_num,$select_date as year from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no $lc_company_cond $buyer_cond $year_cond and a.status_active in(1,2,3) and b.status_active=1";  
	//echo $sql; die;
	echo create_list_view("list_view", "Year,Job No,Style Ref No,Order NO","70,50,150,150","500","310",0, $sql , "js_set_value", "id,style_ref_no", "", 1, "0", $arr, "year,job_no_prefix_num,style_ref_no,po_number", "","setFilterGrid('list_view',-1)","0","",1) ;	

	echo "<input type='hidden' id='txt_selected_po' />";
	echo "<input type='hidden' id='txt_selected_style' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	exit();
}

if($action=="order_no_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
    <script>
		
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
    	function check_all_data() 
    	{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				if($('#tr_' + i).is(':visible'))
				{
					var onclickString = $('#tr_' + i).attr('onclick');
					var paramArr = onclickString.split("'");
					var functionParam = paramArr[1];
					js_set_value( functionParam );
				}
				
			}
		}
		
		function toggle( x, origColor ) 
		{
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
				$('#txt_selected_po').val( id );
				$('#txt_selected_style').val( name ); 
				$('#txt_selected_no').val( num );
		}
    </script>
    <?
	$buyer=str_replace("'","",$buyer);
	$w_company=str_replace("'","",$w_company);
	$lc_company=str_replace("'","",$lc_company);
	$job_year=str_replace("'","",$job_year);
	$txt_style_ref_id=str_replace("'","",$txt_style_ref_id);
	
	if($lc_company!=0) $lc_company_cond="and b.company_name=$lc_company"; else $lc_company_cond="";
	if($buyer!=0) $buyer_cond="and b.buyer_name=$buyer"; else $buyer_cond="";
	if($year!=0) $year_cond="and to_char(b.insert_date,'YYYY')=$year"; else $year_cond="";

	if($db_type==0)
	{
		$select_date=" year(b.insert_date)";
	}
	else if($db_type==2)
	{
		$select_date=" to_char(b.insert_date,'YYYY')";
	}
	
	$sql = "SELECT a.id,a.po_number,a.job_no_mst,b.style_ref_no,b.job_no_prefix_num,$select_date as year from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no $lc_company_cond $buyer_cond $year_cond and a.status_active in(1,2,3) and b.status_active=1"; 
	//echo $sql; die;
	echo create_list_view("list_view", "Year,Job No,Style Ref No,Order NO","70,50,150,150","500","310",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "year,job_no_prefix_num,style_ref_no,po_number", "","setFilterGrid('list_view',-1)","0","",1) ;	

	echo "<input type='hidden' id='txt_selected_po' />";
	echo "<input type='hidden' id='txt_selected_style' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	exit();
}

if($action=="report_generate") 
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$lc_company_id 		= str_replace("'","",$cbo_company_id);
	
	$companyArr 	= return_library_array("select id,company_name from lib_company","id","company_name"); 
	$countryArr 	= return_library_array("select id,country_name from lib_country","id","country_name"); 
	$buyerArr 		= return_library_array("select id,short_name from lib_buyer","id","short_name");
	$colorArr 		= return_library_array("select id,color_name from lib_color","id","color_name");
	$sizeArr 		= return_library_array("select id,size_name from lib_size","id","size_name");
	$floorArr 		= return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
	$lineArr 		= return_library_array("select id,line_name from lib_sewing_line where company_name='$lc_company_id'","id","line_name");
	$prod_reso_arr 	= return_library_array("select id,line_number from prod_resource_mst where company_id='$lc_company_id'","id","line_number");
	// ================================= GETTING FORM DATA ====================================
	
	$buyer_id 			= str_replace("'","",$cbo_buyer_id);
	$job_year			= str_replace("'","",$cbo_year);
	$job_no 			= str_replace("'","",$txt_job_no);
	$style_ref_no 		= str_replace("'","",$txt_style_ref_no);
	$order_no 			= str_replace("'","",$txt_order_no);
	$order_id 			= str_replace("'","",$hiden_order_id);
	$report_title 		= str_replace("'","",$report_title);
	$txt_date_from 		= str_replace("'","",$txt_date_from);
	$txt_date_to 		= str_replace("'","",$txt_date_to);	
	$shipment_status 	= str_replace("'","",$cbo_shipment_status);	
	$status 			= str_replace("'","",$cbo_status);	
	$order_stat 		= str_replace("'","",$cbo_order_status);
	
	//******************************************* MAKE QUERY CONDITION ************************************************
	$sql_cond = "";
	$sql_cond .= ($lc_company_id==0) 		? "" : " and a.company_name=$lc_company_id";
	$sql_cond .= ($buyer_id==0) 			? "" : " and a.buyer_name=$buyer_id";
	$sql_cond .= ($style_ref_no=="") 		? "" : " and a.style_ref_no in($style_ref_no)";
	$sql_cond .= ($job_no=="") 		? "" : " and a.job_no_prefix_num in($job_no)";
    $sql_cond .= ($job_year!=0) ? " and to_char(a.insert_date,'YYYY')=$job_year" : "";
	$sql_cond .= ($order_stat==0) 	? "" : " and b.order_status in($order_stat)";
	$sql_cond .= ($shipment_status==0) 	? "" : " and b.shiping_status in($shipment_status)";
	if($order_id =="" && $order_no !=""){ $sql_cond .= " and b.po_number ='$order_no'";}
	if($order_id !="")
	{
		$po_id_arr = explode(",", $order_id);
		$sql_cond .= where_con_using_array($po_id_arr,0,"b.id");
	}

	$sql_cond2="";
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

		
		$sql_cond2=" and c.country_ship_date BETWEEN '$txt_datefrom' and '$txt_dateto'";
		
	}

	// echo $sql_cond;die();
		
	// ================================================ MAIN QUERY ==================================================
	$sql="SELECT a.job_no, a.style_ref_no as style,a.buyer_name,b.is_confirmed,b.id as po_id,b.po_number,c.country_id,c.country_ship_date,c.order_quantity as order_qty,c.plan_cut_qnty,c.id as color_size_id,c.item_number_id as item_id,c.color_number_id as color_id,c.size_number_id as size_id,c.size_order,c.excess_cut_perc
	from  wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,ppl_cut_lay_mst d
	where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and a.job_no=d.job_no  $sql_cond $sql_cond2 $po_ids_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 order by a.job_no,b.po_number,c.color_order,c.size_order";
	// echo $sql;die();
	$sql_res = sql_select($sql);
	if (count($sql_res) < 1) 
	{	
		?>
		<div style="margin:20px auto; width: 90%">
			<div class="alert alert-danger">
			  <strong>Sorry!</strong> Data not available. Please try again after something change.
			</div>
		</div>
		<?
		die();
	}

	$data_array = array();
	$poId_arr = array();
	$size_arr = array();
	$chk_arr = array();
	foreach ($sql_res as $row) 
	{
		$data_array[$row['JOB_NO']][$row['PO_ID']][$row['ITEM_ID']][$row['COUNTRY_ID']][$row['COLOR_ID']]['style'] = $row['STYLE'];
		$data_array[$row['JOB_NO']][$row['PO_ID']][$row['ITEM_ID']][$row['COUNTRY_ID']][$row['COLOR_ID']]['job_no'] = $row['JOB_NO'];
		$data_array[$row['JOB_NO']][$row['PO_ID']][$row['ITEM_ID']][$row['COUNTRY_ID']][$row['COLOR_ID']]['buyer_name'] = $row['BUYER_NAME'];
		$data_array[$row['JOB_NO']][$row['PO_ID']][$row['ITEM_ID']][$row['COUNTRY_ID']][$row['COLOR_ID']]['order_status'] = $row['IS_CONFIRMED'];
		$data_array[$row['JOB_NO']][$row['PO_ID']][$row['ITEM_ID']][$row['COUNTRY_ID']][$row['COLOR_ID']]['po_number'] = $row['PO_NUMBER'];
		$data_array[$row['JOB_NO']][$row['PO_ID']][$row['ITEM_ID']][$row['COUNTRY_ID']][$row['COLOR_ID']]['csdate'] = $row['COUNTRY_SHIP_DATE'];

		if($chk_arr[$row['COLOR_SIZE_ID']]=="")
		{
			$data_array[$row['JOB_NO']][$row['PO_ID']][$row['ITEM_ID']][$row['COUNTRY_ID']][$row['COLOR_ID']][$row['SIZE_ID']]['order_qty'] += $row['ORDER_QTY'];
			$data_array[$row['JOB_NO']][$row['PO_ID']][$row['ITEM_ID']][$row['COUNTRY_ID']][$row['COLOR_ID']][$row['SIZE_ID']]['plan_cut_qnty'] += $row['PLAN_CUT_QNTY'];
			$data_array[$row['JOB_NO']][$row['PO_ID']][$row['ITEM_ID']][$row['COUNTRY_ID']][$row['COLOR_ID']][$row['SIZE_ID']]['excess_cut_perc'] += $row['EXCESS_CUT_PERC'];
			$chk_arr[$row['COLOR_SIZE_ID']] = $row['COLOR_SIZE_ID'];
		}

		$poId_arr[$row['PO_ID']] = $row['PO_ID'];
		// if($size_arr[$row['SIZE_ID']]=="")
		// {
			$size_arr[$row['JOB_NO']][$row['SIZE_ID']] = $row['SIZE_ID'];
			$size_order_arr[$row['SIZE_ID']] = $row['SIZE_ORDER'];
		// }
	}
	unset($sql_res);

	// =========================================== Lay Data ====================================
	$ordr_id_cond = where_con_using_array($poId_arr,0,"c.order_id");
	$sql = "SELECT b.gmt_item_id,b.color_id,c.country_id,c.order_id,c.size_id,c.size_qty from ppl_cut_lay_dtls b, ppl_cut_lay_bundle c where b.id=c.dtls_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $ordr_id_cond";
	// echo $sql;
	$res= sql_select($sql);
	$lay_data_arr = array();
	foreach ($res as $val) 
	{
		$lay_data_arr[$val['ORDER_ID']][$val['GMT_ITEM_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']][$val['SIZE_ID']] += $val['SIZE_QTY'];
	}
	unset($res);
    // ================================================ Production Data ==================================================
    $ordr_id_cond = where_con_using_array($poId_arr,0,"c.po_break_down_id");
	$sql_prod="SELECT c.po_break_down_id as po_id, c.country_id,c.color_number_id as color_id,c.item_number_id as item_id,c.size_number_id as size_id,d.floor_id,d.sewing_line,d.production_type,d.remarks,e.production_qnty as qty,e.reject_qty
	from  wo_po_color_size_breakdown c, pro_garments_production_mst d,pro_garments_production_dtls e
	where c.id=e.color_size_break_down_id and d.id=e.mst_id and d.production_type in(1,4,5,8,11) and c.status_active=1 and d.status_active=1 and e.status_active=1  $ordr_id_cond";
	// echo $sql_prod;die();
	$sql_prod_res = sql_select($sql_prod);		

	$prod_qty_arr = array();
	$prod_floor_arr = array();
	foreach ($sql_prod_res as $row) 
	{
		$prod_qty_arr[$row['PO_ID']][$row['ITEM_ID']][$row['COUNTRY_ID']][$row['COLOR_ID']][$row['SIZE_ID']][$row['PRODUCTION_TYPE']]['ok'] += $row['QTY'];
		$prod_qty_arr[$row['PO_ID']][$row['ITEM_ID']][$row['COUNTRY_ID']][$row['COLOR_ID']][$row['SIZE_ID']][$row['PRODUCTION_TYPE']]['rej'] += $row['REJECT_QTY'];
		$prod_qty_arr[$row['PO_ID']][$row['ITEM_ID']][$row['COUNTRY_ID']][$row['COLOR_ID']][$row['PRODUCTION_TYPE']]['remarks'] .= $row['REMARKS']."**";
		if($row['PRODUCTION_TYPE']==5)
		{
			$prod_qty_arr[$row['PO_ID']][$row['ITEM_ID']][$row['COUNTRY_ID']][$row['COLOR_ID']]['line'] .= $row['SEWING_LINE']."**";
		}
		$prod_floor_arr[$row['PO_ID']][$row['ITEM_ID']][$row['COUNTRY_ID']][$row['COLOR_ID']][$row['PRODUCTION_TYPE']] .= $row['FLOOR_ID']."**";
	}
	unset($sql_prod_res);
	// echo "<pre>";print_r($prod_floor_arr);die();

	// $table_width = 2060+(count($size_arr)*50)*3;
	ob_start();
	?>
	<div class="main" style="margin: 0 auto; padding: 10px;  width: 100%">
		<table width="100%" cellspacing="0" align="center">
	        <tr class="form_caption" style="border:none;">
	            <td colspan="20" align="center"><strong style="font-size: 17px">Country and Color Wise Production Tracking Report</strong></td>
	        </tr>
	        <tr class="form_caption" style="border:none;">
	            <td colspan="20" align="center" ><strong style="font-size: 19px"><u><? echo $companyArr[$lc_company_id]; ?></u></strong></td>
	        </tr>		        
	        <tr class="form_caption" style="border:none;">
	            <td colspan="20" align="center"><strong style="font-size: 15px">Date Range : <? echo change_date_format($txt_date_from); ?> to <? echo change_date_format($txt_date_to); ?></strong></td>
	        </tr>
	    </table>
	    <!-- ================================================ DETAIS PART ================================================ -->
	    <?
	    $sl = 1;
	    foreach ($data_array as $job_no => $job_data) 
	    {
	    	$table_width = 2060+(count($size_arr[$job_no])*50)*3;
		    ?>
		    <div>
		    	<table class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" id="" width="<? echo $table_width;?>">
		    		<thead>
			    		<tr>
			    			<th rowspan="3" width="30">Sl.</th>
			    			<th rowspan="3" width="100">Order No</th>
			    			<th rowspan="3" width="100">Order Status</th>
			    			<th rowspan="3" width="100">Buyer</th>
			    			<th rowspan="3" width="100">Job</th>
			    			<th rowspan="3" width="100">Style Ref.</th>
			    			<th rowspan="3" width="100">Item Name</th>
			    			<th rowspan="3" width="100">Country</th>
			    			<th rowspan="3" width="100">Country Wise Line</th>
			    			<th rowspan="3" width="100">Color Name</th>
			    			<th rowspan="3" width="150">Production Type</th>
			    			<th colspan="<?=count($size_arr[$job_no])+2;?>" width="<?=(count($size_arr[$job_no])*50)+160;?>">Sewing Information</th>
			    			<th colspan="<?=count($size_arr[$job_no])+4;?>" width="<?=(count($size_arr[$job_no])*50)+410;?>">Poly Information</th>
			    			<th colspan="<?=count($size_arr[$job_no])+4;?>" width="<?=(count($size_arr[$job_no])*50)+410;?>">Finishing Information</th>
		    			</tr>
		    			<tr>
		    				<th colspan="<?=count($size_arr[$job_no]);?>" width="<?=count($size_arr[$job_no])*50;?>">Size</th>
		    					
		    				<th rowspan="2" width="80">Total</th>
		    				<th rowspan="2" width="80">Remarks</th>

		    				<!-- ============================ -->
		    				<th rowspan="2" width="100">Unit Name</th>
		    				<th rowspan="2" width="150">Production Type</th>
		    				<th colspan="<?=count($size_arr[$job_no]);?>" width="<?=count($size_arr[$job_no])*50;?>">Size</th>
		    				<th rowspan="2" width="80">Total</th>
		    				<th rowspan="2" width="80">Remarks</th>
		    				<!-- ============================ -->
		    				<th rowspan="2" width="100">Unit Name</th>
		    				<th rowspan="2" width="150">Production Type</th>
		    				<th colspan="<?=count($size_arr[$job_no]);?>" width="<?=count($size_arr[$job_no])*50;?>">Size</th>
		    				<th rowspan="2" width="80">Total</th>
		    				<th rowspan="2" width="80">Remarks</th>
		    			</tr>
		    			<tr>
		    				<?
		    				foreach ($size_arr[$job_no] as  $val) 
		    				{
		    					?>
		    					<th title="<?=$size_order_arr[$val];?>" width="50"><?=$sizeArr[$val];?></th>
		    					<?
		    				}
		    				// =========================
		    				foreach ($size_arr[$job_no] as  $val) 
		    				{
		    					?>
		    					<th width="50"><?=$sizeArr[$val];?></th>
		    					<?
		    				}
		    				// =========================
		    				foreach ($size_arr[$job_no] as  $val) 
		    				{
		    					?>
		    					<th width="50"><?=$sizeArr[$val];?></th>
		    					<?
		    				}
		    				?>
		    			</tr>
		    		</thead>
		    	</table>
		    	
		    	<div style="width: <? echo $table_width+20;?>px; overflow-y: scroll; max-height: 100%" id="scroll_body">
		    		<table cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all" width="<? echo $table_width;?>" id="html_search_">
		    			<?
		    			$i=1;
		    			$floor_total_array = array();
	    				foreach ($job_data as $po_id => $po_data) 
	    				{
							foreach ($po_data as $item_id => $item_data) 
	    					{
	    						foreach ($item_data as $country_id => $country_data) 
	    						{
	    							foreach ($country_data as $color_id => $row) 
	    							{
	    								$line_name = "";
	    								$line_id_arr = array_unique(array_filter(explode("**", $prod_qty_arr[$po_id][$item_id][$country_id][$color_id]['line'])));
										foreach ($line_id_arr as $l_key => $l_val) 
										{
	    									$prod_rso_line_arr = explode(",", $prod_reso_arr[$l_val]);
	    									// print_r($prod_rso_line_arr);echo "$l_val<br>";
	    									foreach ($prod_rso_line_arr as $key => $value) 
	    									{	    					
	    										// echo $value."sdsds";				
												$line_name .= ($line_name=="") ? $lineArr[$value] : ", ".$lineArr[$value];
											}
										}

										$poly_floor = "";
	    								$floor_ids = array_unique(array_filter(explode("**", $prod_floor_arr[$po_id][$item_id][$country_id][$color_id][11])));
										foreach ($floor_ids as $l_key => $l_val) 
										{
											$poly_floor .= ($poly_floor=="") ? $floorArr[$l_val] : ", ".$floorArr[$l_val];
										}

										$finish_floor = "";
	    								$floor_ids = array_unique(array_filter(explode("**", $prod_floor_arr[$po_id][$item_id][$country_id][$color_id][8])));
										foreach ($floor_ids as $l_key => $l_val) 
										{
											$finish_floor .= ($finish_floor=="") ? $floorArr[$l_val] : ", ".$floorArr[$l_val];
										}


	    								$sew_rmks = implode(", ", array_unique(array_filter(explode("**", $prod_qty_arr[$po_id][$item_id][$country_id][$color_id][5]['remarks']))));
	    								$poly_rmks = implode(", ", array_unique(array_filter(explode("**", $prod_qty_arr[$po_id][$item_id][$country_id][$color_id][11]['remarks']))));
	    								$fin_rmks = implode(", ", array_unique(array_filter(explode("**", $prod_qty_arr[$po_id][$item_id][$country_id][$color_id][8]['remarks']))));

										$bgcolor = ($i%2==0) ? "#ffffff" : "#f6faff";
																		
										?>
											<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_1nd<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $sl; ?>" >
												<td rowspan="12" valign="middle" width="30"><?=$i;?></td>
								    			<td rowspan="12" valign="middle" width="100"><p><?=$row['po_number'];?></p></td>
								    			<td rowspan="12" valign="middle" width="100"><p><?=$order_status[$row['order_status']];?></p></td>
								    			<td rowspan="12" valign="middle" width="100"><p><?=$buyerArr[$row['buyer_name']];?></p></td>
								    			<td rowspan="12" valign="middle" width="100"><p><?=$row['job_no'];?></p></td>
								    			<td rowspan="12" valign="middle" width="100"><p><?=$row['style'];?></p></td>
								    			<td rowspan="12" valign="middle" width="100"><p><?=$garments_item[$item_id];?></p></td>
								    			<td rowspan="12" valign="middle" width="100"><p><?=$countryArr[$country_id];?></p></td>
								    			<td rowspan="12" valign="middle" width="100"><p><?=$line_name;?></p></td>
								    			<td rowspan="12" valign="middle" width="100"><p><?=$colorArr[$color_id];?></p></td>
								    			<td valign="middle" width="150"><p>Order Qty.</p></td>	

								    			<?
								    			$tot=0;
							    				foreach ($size_arr[$job_no] as  $val) 
							    				{
							    					?>
							    					<td align="right" width="50"><?=number_format($row[$val]['order_qty'],0);?></td>
							    					<?
							    					$tot+=$row[$val]['order_qty'];
							    				}
							    				?>
							    				<td width="80" align="right"><?=number_format($tot,0);?></td>
							    				<td rowspan="12" width="80" valign="middle"><p><?=$sew_rmks;?></p></td>
							    				<!-- ================= -->
							    				<td rowspan="12" width="100" valign="middle"><p><?=$poly_floor;?></p></td>
		    									<td rowspan="3" width="150">Poly Qty</td>
							    				<?			
							    				$tot=0;			    				
							    				foreach ($size_arr[$job_no] as  $val) 
							    				{
							    					?>
							    					<td align="right" rowspan="3" width="50"><?=number_format($prod_qty_arr[$po_id][$item_id][$country_id][$color_id][$val][11]['ok'],0);?></td>
							    					<?
							    					$tot+= $prod_qty_arr[$po_id][$item_id][$country_id][$color_id][$val][11]['ok'];
							    				}
							    				?>	
							    				<td align="right" rowspan="3" width="80"><?=number_format($tot,0);?></td>
							    				<td rowspan="12" width="80" valign="middle"><p><?=$poly_rmks;?></p></td>
							    				<!-- ================= -->
							    				<td rowspan="12" width="100" valign="middle"><p><?=$finish_floor;?></p></td>
		    									<td rowspan="3" width="150">Finishing Qty</td>
							    				<?
							    				$tot=0;
							    				foreach ($size_arr[$job_no] as  $val) 
							    				{
							    					?>
							    					<td align="right" rowspan="3" width="50"><?=number_format($prod_qty_arr[$po_id][$item_id][$country_id][$color_id][$val][8]['ok'],0);?></td>
							    					<?
							    					$tot+= $prod_qty_arr[$po_id][$item_id][$country_id][$color_id][$val][8]['ok'];
							    				}
							    				?>
							    				<td align="right" rowspan="3" width="80"><?=number_format($tot,0);?></td>
							    				<td rowspan="12" width="80" valign="middle"><p><?=$fin_rmks;?></p></td>	    											
											</tr>
											<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>" >
												<td valign="middle" width="150"><p>Plan Cut Qty %</p></td>	

								    			<?
								    			$tot=0;
								    			$tot_size=0;
							    				foreach ($size_arr[$job_no] as  $val) 
							    				{
							    					?>
							    					<td align="right" width="50"><?=number_format($row[$val]['excess_cut_perc'],2);?></td>
							    					<?
							    					$tot+=$row[$val]['excess_cut_perc'];
							    					if($row[$val]['excess_cut_perc']>0)
							    					{
							    						$tot_size++;
							    					}
							    				}
							    				$tot_prsnt = ($tot_size>0) ? $tot/$tot_size : 0;
							    				?>
							    				<td width="80" align="right"><?=number_format($tot_prsnt,2);?></td>
							    				
											</tr>
											<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>" >
												<td valign="middle" width="150"><p>Plan Cut Qty</p></td>

								    			<?
								    			$tot=0;
							    				foreach ($size_arr[$job_no] as  $val) 
							    				{
							    					?>
							    					<td align="right" width="50"><?=number_format($row[$val]['plan_cut_qnty'],0);?></td>
							    					<?
							    					$tot+=$row[$val]['plan_cut_qnty'];
							    				}
							    				?>
							    				<td width="80" align="right"><?=number_format($tot,0);?></td>
							    				
											</tr>


											<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>" >
												<td valign="middle" width="150"><p>Cut and Lay</p></td>	

								    			<?
								    			$tot=0;
							    				foreach ($size_arr[$job_no] as  $val) 
							    				{
							    					$lay_qty = $lay_data_arr[$po_id][$item_id][$country_id][$color_id][$val];
							    					?>
							    					<td align="right" width="50"><?=number_format($lay_qty,0);?></td>
							    					<?
							    					$tot+=$lay_data_arr[$po_id][$item_id][$country_id][$color_id][$val];
							    				}
							    				?>
							    				<td width="80" align="right"><?=number_format($tot,0);?></td>
							    				<!-- <td width="80"></td> -->
							    				<!-- ================= -->
							    				<!-- <td width="100"></td> -->
		    									<td rowspan="3" width="150"><p>Poly Balance From Required Qty</p></td>
							    				<?	
							    				$tot=0;					    				
							    				foreach ($size_arr[$job_no] as  $val) 
							    				{
							    					$poly_bal =  $prod_qty_arr[$po_id][$item_id][$country_id][$color_id][$val][11]['ok']-$row[$val]['order_qty'];
							    					$txtcolor = ($poly_bal<0) ? "style='color:red;'" : "";
							    					?>
							    					<td <?=$txtcolor;?> align="right" rowspan="3" width="50"><?=number_format($poly_bal,0);?></td>
							    					<?
							    					$tot +=$poly_bal;
							    				}
							    				$txtcolor = ($tot<0) ? "style='color:red;'" : "";
							    				?>						    				
							    				<td <?=$txtcolor;?> align="right" rowspan="3" width="80"><?=number_format($tot,0);?></td>
							    				<!-- <td width="80"></td> -->
							    				<!-- ================= -->
							    				<!-- <td width="100"></td> -->
		    									<td rowspan="3" width="150"><p>Finishing Balance From Required Qty</p></td>
							    				<?
							    				$tot=0;
							    				foreach ($size_arr[$job_no] as  $val) 
							    				{
							    					$fin_bal = $prod_qty_arr[$po_id][$item_id][$country_id][$color_id][$val][8]['ok'] - $row[$val]['order_qty'];
							    					$txtcolor = ($fin_bal<0) ? "style='color:red;'" : "";
							    					?>
							    					<td <?=$txtcolor;?> align="right" rowspan="3" width="50"><?=number_format($fin_bal,0);?></td>
							    					<?
							    					$tot+=$fin_bal;
							    				}
							    				$txtcolor = ($tot<0) ? "style='color:red;'" : "";
							    				?>
							    				<td <?=$txtcolor;?> rowspan="3" align="right" width="80"><?=number_format($tot,0);?></td>
							    				<!-- <td width="80"></td>	    											 -->
											</tr>
											<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>" >
												<td valign="middle" width="150"><p>Cutting QC Pass</p></td>

								    			<?
								    			$tot=0;
							    				foreach ($size_arr[$job_no] as  $val) 
							    				{
							    					?>
							    					<td align="right" width="50"><?=number_format($prod_qty_arr[$po_id][$item_id][$country_id][$color_id][$val][1]['ok'],0);?></td>
							    					<?
							    					$tot +=$prod_qty_arr[$po_id][$item_id][$country_id][$color_id][$val][1]['ok'];
							    				}
							    				$txtcolor = ($tot<0) ? "style='color:red;'" : "";
							    				?>
							    				<td <?=$txtcolor;?> width="80" align="right"><?=number_format($tot,0);?></td>
							    				
											</tr>
											<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>" >
												<td valign="middle" width="150"><p>Cutting Balance</p></td>

								    			<?
								    			$tot=0;
							    				foreach ($size_arr[$job_no] as  $val) 
							    				{
							    					$cut_bal = $prod_qty_arr[$po_id][$item_id][$country_id][$color_id][$val][1]['ok'] - $row[$val]['plan_cut_qnty'];
							    					$txtcolor = ($cut_bal<0) ? "style='color:red;'" : "";
							    					?>
							    					<td <?=$txtcolor;?> align="right" width="50"><?=$cut_bal;?></td>
							    					<?
							    					$tot+=$cut_bal;
							    				}
							    				$txtcolor = ($tot<0) ? "style='color:red;'" : "";
							    				?>
							    				<td <?=$txtcolor;?> width="80" align="right"><?=number_format($tot,0);?></td>
							    				
											</tr>



											<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>" >
												<td valign="middle" width="150"><p>Input</p></td>

								    			<?
								    			$tot=0;
							    				foreach ($size_arr[$job_no] as  $val) 
							    				{
							    					?>
							    					<td align="right" width="50"><?=number_format($prod_qty_arr[$po_id][$item_id][$country_id][$color_id][$val][4]['ok'],0);?></td>
							    					<?
							    					$tot+=$prod_qty_arr[$po_id][$item_id][$country_id][$color_id][$val][4]['ok'];
							    				}
							    				?>
							    				<td width="80" align="right"><?=number_format($tot,0);?></td>
							    				<!-- <td width="80"></td> -->
							    				<!-- ================= -->
							    				<!-- <td width="100"></td> -->
		    									<td rowspan="3" width="150">Poly Reject Qty.</td>
							    				<?		
							    				$tot=0;				    				
							    				foreach ($size_arr[$job_no] as  $val) 
							    				{
							    					?>
							    					<td rowspan="3" align="right" width="50"><?=number_format($prod_qty_arr[$po_id][$item_id][$country_id][$color_id][$val][11]['rej'],0);?></td>
							    					<?
							    					$tot+=$prod_qty_arr[$po_id][$item_id][$country_id][$color_id][$val][11]['rej'];
							    				}
							    				?>						    				
							    				<td rowspan="3" align="right" width="80"><?=number_format($tot,0);?></td>
							    				<!-- <td width="80"></td> -->
							    				<!-- ================= -->
							    				<!-- <td width="100"></td> -->
		    									<td rowspan="3" width="150">Finishing Reject Qty.</td>
							    				<?
							    				$tot=0;
							    				foreach ($size_arr[$job_no] as  $val) 
							    				{
							    					?>
							    					<td align="right" rowspan="3" width="50"><?=number_format($prod_qty_arr[$po_id][$item_id][$country_id][$color_id][$val][8]['rej'],0);?></td>
							    					<?
							    					$tot+=$prod_qty_arr[$po_id][$item_id][$country_id][$color_id][$val][8]['rej'];
							    				}
							    				$txtcolor = ($tot<0) ? "style='color:red;'" : "";
							    				?>
							    				<td <?=$txtcolor;?> rowspan="3" align="right" width="80"><?=number_format($tot,0);?></td>
							    				<!-- <td width="80"></td>	    											 -->
											</tr>
											<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>" >
												<td valign="middle" width="150"><p>Input Balance</p></td>	

								    			<?
								    			$tot=0;
							    				foreach ($size_arr[$job_no] as  $val) 
							    				{
							    					$input_bal=$prod_qty_arr[$po_id][$item_id][$country_id][$color_id][$val][4]['ok'] - $row[$val]['plan_cut_qnty'];
							    					$txtcolor = ($input_bal<0) ? "style='color:red;'" : "";
							    					?>
							    					<td <?=$txtcolor;?> align="right" width="50"><?=number_format($input_bal,0);?></td>
							    					<?
							    					$tot+=$input_bal;
							    				}
							    				$txtcolor = ($tot<0) ? "style='color:red;'" : "";
							    				?>
							    				<td <?=$txtcolor;?> width="80" align="right"><?=number_format($tot,0);?></td>
							    				
											</tr>
											<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>" >
												<td valign="middle" width="150"><p>Output</p></td>

								    			<?
								    			$tot=0;
							    				foreach ($size_arr[$job_no] as  $val) 
							    				{
							    					?>
							    					<td align="right" width="50"><?=number_format($prod_qty_arr[$po_id][$item_id][$country_id][$color_id][$val][5]['ok'],0);?></td>
							    					<?
							    					$tot+=$prod_qty_arr[$po_id][$item_id][$country_id][$color_id][$val][5]['ok'];
							    				}
							    				?>
							    				<td width="80" align="right"><?=number_format($tot,0);?></td>
							    				
											</tr>


											<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>" >
												<td valign="middle" width="150"><p>Sewing Reject</p></td>

								    			<?
								    			$tot=0;
							    				foreach ($size_arr[$job_no] as  $val) 
							    				{
							    					?>
							    					<td align="right" width="50"><?=number_format($prod_qty_arr[$po_id][$item_id][$country_id][$color_id][$val][5]['rej'],0);?></td>
							    					<?
							    					$tot+=$prod_qty_arr[$po_id][$item_id][$country_id][$color_id][$val][5]['rej'];
							    				}
							    				?>
							    				<td width="80" align="right"><?=number_format($tot,0);?></td>
							    				<!-- <td width="80"></td> -->
							    				<!-- ================= -->
							    				<!-- <td width="100"></td> -->
		    									<td rowspan="3" width="150">Poly Balance From Output Qty</td>
							    				<?		
							    				$tot=0;				    				
							    				foreach ($size_arr[$job_no] as  $val) 
							    				{
							    					$poly_bal_frm_out = ($prod_qty_arr[$po_id][$item_id][$country_id][$color_id][$val][11]['ok'] + $prod_qty_arr[$po_id][$item_id][$country_id][$color_id][$val][11]['rej']) - $prod_qty_arr[$po_id][$item_id][$country_id][$color_id][$val][5]['ok'];
							    					$txtcolor = ($poly_bal_frm_out<0) ? "style='color:red;'" : "";
							    					?>
							    					<td <?=$txtcolor;?> rowspan="3" align="right" width="50"><?=number_format($poly_bal_frm_out,0);?></td>
							    					<?
							    					$tot+=$poly_bal_frm_out;
							    				}
							    				$txtcolor = ($tot<0) ? "style='color:red;'" : "";
							    				?>						    				
							    				<td <?=$txtcolor;?> rowspan="3" align="right" width="80"><?=number_format($tot,0);?></td>
							    				<!-- <td width="80"></td> -->
							    				<!-- ================= -->
							    				<!-- <td width="100"></td> -->
		    									<td rowspan="3" width="150">Finishing Balance From Poly Qty</td>
							    				<?
							    				foreach ($size_arr[$job_no] as  $val) 
							    				{
							    					$fin_bal_frm_poly = ($prod_qty_arr[$po_id][$item_id][$country_id][$color_id][$val][8]['ok'] + $prod_qty_arr[$po_id][$item_id][$country_id][$color_id][$val][8]['rej']) - $prod_qty_arr[$po_id][$item_id][$country_id][$color_id][$val][11]['ok'];
							    					$txtcolor = ($fin_bal_frm_poly<0) ? "style='color:red;'" : "";
							    					?>
							    					<td <?=$txtcolor;?> rowspan="3" align="right" width="50"><?=number_format($fin_bal_frm_poly,0);?></td>
							    					<?
							    				}
							    				$txtcolor = ($tot<0) ? "style='color:red;'" : "";
							    				?>
							    				<td <?=$txtcolor;?> rowspan="3" align="right" width="80"><?=number_format($tot,0);?></td>
							    				<!-- <td width="80"></td>	    											 -->
											</tr>
											<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>" >
												<td valign="middle" width="150"><p>Output Balance From Input</p></td>

								    			<?
								    			$tot=0;
							    				foreach ($size_arr[$job_no] as  $val) 
							    				{
							    					$out_bal_frm_in = ($prod_qty_arr[$po_id][$item_id][$country_id][$color_id][$val][5]['rej']+$prod_qty_arr[$po_id][$item_id][$country_id][$color_id][$val][5]['ok']) - $prod_qty_arr[$po_id][$item_id][$country_id][$color_id][$val][4]['ok'];
							    					$txtcolor = ($out_bal_frm_in<0) ? "style='color:red;'" : "";
							    					?>
							    					<td <?=$txtcolor;?> align="right" width="50"><?=number_format($out_bal_frm_in,0);?></td>
							    					<?
							    					$tot+=$out_bal_frm_in;
							    				}
							    				$txtcolor = ($tot<0) ? "style='color:red;'" : "";
							    				?>
							    				<td <?=$txtcolor;?> width="80" align="right"><?=number_format($tot,0);?></td>
							    				
											</tr>
											<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>" >
												<td valign="middle" width="150"><p>Output Bal From Order Qty</p></td>

								    			<?
								    			$tot = 0;
							    				foreach ($size_arr[$job_no] as  $val) 
							    				{
							    					$out_bal_frm_order = $prod_qty_arr[$po_id][$item_id][$country_id][$color_id][$val][5]['ok'] - $row[$val]['order_qty'];
							    					$txtcolor = ($out_bal_frm_order<0) ? "style='color:red;'" : "";
							    					?>
							    					<td <?=$txtcolor;?> align="right" width="50"><?=number_format($out_bal_frm_order,0);?></td>
							    					<?
							    					$tot += $out_bal_frm_order;
							    				}
							    				$txtcolor = ($tot<0) ? "style='color:red;'" : "";
							    				?>
							    				<td <?=$txtcolor;?> width="80" align="right"><?=number_format($tot,0);?></td>
							    				
											</tr>
										<?
										$i++;	
										$sl++;	
		    							
		    						}
		    					}
	    					}
	    				}
		    			?>
		    		</table>
		    	</div>
	    	</div>
	    	<br clear="all">
   			<?
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
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data####$filename";
	exit();      
} 

if($action=="open_prod_popup")
{
	$ex_data = explode("_",str_replace("'", "", $data));
	// print_r($ex_data);
	$po_id 			= $ex_data[0];
	$country_id 	= $ex_data[1];
	$item_id 		= $ex_data[2];
	$color_id 		= $ex_data[3];
	$floor_id 		= $ex_data[4];
	$sewing_line_id = $ex_data[5];
	$prod_reso_allo = $ex_data[6];
	$date_from 		= $ex_data[7];
	$date_to 		= $ex_data[8];
	$type 			= $ex_data[9]; // input, output, poly etc.
	$level 			= $ex_data[10]; // 4 prev, 1 current, 2 total, 3 reject etc.

	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);	
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	switch ($level) 
	{
		case 1:
			$sql = "SELECT c.color_number_id,c.size_number_id,sum(b.production_qnty) as production_qnty,a.production_date from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c WHERE a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and a.po_break_down_id=$po_id and c.item_number_id=$item_id and c.country_id=$country_id and c.color_number_id=$color_id and a.production_date between '$date_from' and '$date_to' and a.production_type=$type and a.sewing_line in($sewing_line_id) and a.status_active=1 and c.status_active=1
			group by c.color_number_id,c.size_number_id,a.production_date order by c.size_number_id";
			break;

		case 2:
			$sql = "SELECT c.color_number_id,c.size_number_id,sum(b.production_qnty) as production_qnty,a.production_date from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c WHERE a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and a.po_break_down_id=$po_id and c.item_number_id=$item_id and c.country_id=$country_id and c.color_number_id=$color_id and a.production_date<='$date_to' and a.production_type=$type and a.sewing_line in($sewing_line_id) and a.status_active=1 and c.status_active=1
			group by c.color_number_id,c.size_number_id,a.production_date order by c.size_number_id";
			break;
		case 3:
			$sql = "SELECT c.color_number_id,c.size_number_id,sum(case when b.is_rescan=0 then b.reject_qty else 0 end) - sum(case when b.is_rescan=1 then b.production_qnty else 0 end) as production_qnty,a.production_date from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c WHERE a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and a.po_break_down_id=$po_id and c.item_number_id=$item_id and c.country_id=$country_id and c.color_number_id=$color_id and a.production_type=$type  and a.sewing_line in($sewing_line_id) and a.status_active=1 and c.status_active=1
			group by c.color_number_id,c.size_number_id,a.production_date order by c.size_number_id"; //and a.production_date between '$date_from' and '$date_to'
			break;
		case 4:
			$sql = "SELECT c.color_number_id,c.size_number_id,sum(b.production_qnty) as production_qnty,a.production_date from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c WHERE a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and a.po_break_down_id=$po_id and c.item_number_id=$item_id and c.country_id=$country_id and c.color_number_id=$color_id and a.production_date<'$date_from' and a.production_type=$type and a.sewing_line in($sewing_line_id) and a.status_active=1 and c.status_active=1
			group by c.color_number_id,c.size_number_id,a.production_date order by c.size_number_id";
			break;	
		default:
			# code...
			break;
	}
	//echo $sql;

	$sql_res = sql_select($sql);
	$size_qty = array();
	$size_array = array();
	$prod_array = array();
	foreach ($sql_res as $row) 
	{
		$size_qty[$row[csf('production_date')]][$row[csf('size_number_id')]] = $row[csf('production_qnty')];
		$size_array[$row[csf('size_number_id')]] = $row[csf('size_number_id')];
		$prod_array[$row[csf('production_date')]] = $row[csf('production_date')];
	}
	$table_width = 190+(count($size_array)*50);
	?>
	<div id="data_panel" align="" style="width:100%;margin: 20px auto;text-align: center;">
		<table width="<? echo $table_width;?>" align="center" border="1" rules="all" class="rpt_table" >
			<thead>
				<tr>
					<th></th>
					<th></th>
					<th colspan="<? echo count($size_array); ?>">SIZE</th>
					<th></th>
				</tr>
				<tr>
					<th rowspan="2" width="30">Sl</th>
					<th rowspan="2" width="80">Date</th>
					<?
					foreach ($size_array as $key => $val) 
					{
						?>
						<th width="50"><? echo $sizearr[$key];?></th>
						<?
					}
					?>
					<th rowspan="2" width="80">Total</th>	
				</tr>
			</thead>
			<?
			$i=1;
			$size_total_array = array();
			foreach ($prod_array as $key => $val) 
			{	
				?>
				<tr>
					<td><? echo $i++;?></td>
					<td><? echo change_date_format($val);?></td>
					<?
					$h_total = 0;
					foreach ($size_array as $size_key => $value) 
					{
						?>
						<td><? echo $size_qty[$key][$size_key];?></td>
						<?
						$h_total += $size_qty[$key][$size_key];
						$size_total_array[$size_key] += $size_qty[$key][$size_key];
					}
					?>
					<td><? echo $h_total;?></td>
				</tr>
				<?
			}
			?>
			<tfoot>
				<tr>
					<td colspan="2" align="right">Total </td>
					<?
						$v_total = 0;
						foreach ($size_array as $key => $val) 
						{
							?>
							<td><? echo $size_total_array[$key];?></td>
							<?
							$v_total += $size_total_array[$key];
						}
						?>
					<td><? echo $v_total; ?></td>
				</tr>
			</tfoot>
		</table>
	</div>
	<?
}

if($action=="open_prod_popup2")
{
	$ex_data = explode("_",str_replace("'", "", $data));
	// print_r($ex_data);
	$po_id 			= $ex_data[0];
	$item_id 		= $ex_data[1];
	$color_id 		= $ex_data[2];
	$floor_id 		= $ex_data[3];
	$sewing_line_id = $ex_data[4];
	$prod_reso_allo = $ex_data[5];
	$date_from 		= $ex_data[6];
	$date_to 		= $ex_data[7];
	$type 			= $ex_data[8];
	$level 			= $ex_data[9]; // 4 prev, 1 current, 2 total, 3 reject etc.
	// $level 			= $ex_data[10]; // 4 prev, 1 current, 2 total, 3 reject etc.

	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);	
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	switch ($level) 
	{
		case 1:
			$sql = "SELECT c.color_number_id,c.size_number_id,sum(b.production_qnty) as production_qnty,a.production_date 
			from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
			WHERE a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and a.po_break_down_id in($po_id) and c.item_number_id in($item_id)  and c.color_number_id in($color_id) and a.production_date between '$date_from' and '$date_to' and a.production_type=$type and a.sewing_line in($sewing_line_id) and a.status_active=1 and c.status_active=1
			group by c.color_number_id,c.size_number_id,a.production_date 
			order by c.size_number_id";
			break;

		case 2:
			$sql = "SELECT c.color_number_id,c.size_number_id,sum(b.production_qnty) as production_qnty,a.production_date 
			from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
			WHERE a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and a.po_break_down_id in($po_id) and c.item_number_id in($item_id)  and c.color_number_id in($color_id) and a.production_date<='$date_to' and a.production_type=$type and a.sewing_line in($sewing_line_id) and a.status_active=1 and c.status_active=1
			group by c.color_number_id,c.size_number_id,a.production_date 
			order by c.size_number_id";
			break;
		case 3:
			$sql = "SELECT 0 as production_date, b.bundle_no, c.color_number_id,c.size_number_id,sum(case when b.is_rescan=0 and a.production_type=$type  then b.reject_qty else 0 end) as reject_qty , sum(case when b.is_rescan=1 and a.production_type=$type then b.production_qnty else 0 end) as production_qnty 
			from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
			WHERE a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and a.po_break_down_id in($po_id) and c.item_number_id in($item_id)  and c.color_number_id in($color_id) and a.production_type=$type  and a.sewing_line in($sewing_line_id) and a.status_active=1 and c.status_active=1
			group by b.bundle_no,c.color_number_id,c.size_number_id 
			order by c.size_number_id"; // and a.production_date between '$date_from' and '$date_to'
			break;
		case 4:
			$sql = "SELECT c.color_number_id,c.size_number_id,sum(b.production_qnty) as production_qnty,a.production_date from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c WHERE a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and a.po_break_down_id in($po_id) and c.item_number_id in($item_id)  and c.color_number_id in($color_id) and a.production_date<'$date_from' and a.production_type=$type and a.sewing_line in($sewing_line_id) and a.status_active=1 and c.status_active=1
			group by c.color_number_id,c.size_number_id,a.production_date order by c.size_number_id";
			break;		
		default:
			# code...
			break;
	}
	
	// echo $sql;
	$sql_res = sql_select($sql);
	$size_qty = array();
	$size_array = array();
	$prod_array = array();
	$prod_qnty_array = array();
	foreach ($sql_res as $row) 
	{

		
		if($level==3)
		{

			if($row[csf('production_qnty')]> $row[csf('reject_qty')])
			{
				//echo $row[csf("bundle_no")]. 'rep'.$row[csf('production_qnty')].' rej'. $row[csf('reject_qty')]."<br>";
				$row[csf('production_qnty')] =$row[csf('reject_qty')];
			}
			$row[csf('production_qnty')] =$row[csf('reject_qty')]-$row[csf('production_qnty')];
			$size_qty[$row[csf('production_date')]][$row[csf('size_number_id')]] += $row[csf('production_qnty')];
			$size_array[$row[csf('size_number_id')]] = $row[csf('size_number_id')];
			if($row[csf("production_qnty")])$prod_array[$row[csf('production_date')]] = $row[csf('production_date')];

		}
		else
		{
			$size_qty[$row[csf('production_date')]][$row[csf('size_number_id')]] += $row[csf('production_qnty')];
			$size_array[$row[csf('size_number_id')]] = $row[csf('size_number_id')];
			if($row[csf("production_qnty")])$prod_array[$row[csf('production_date')]] = $row[csf('production_date')];
		}
	}
	$table_width = 190+(count($size_array)*50);
	?>
	<div id="data_panel" align="" style="width:100%;margin: 20px auto;text-align: center;">
		<table width="<? echo $table_width;?>" align="center" border="1" rules="all" class="rpt_table" >
			<thead>
				<tr>
					<th></th>
					<th></th>
					<th colspan="<? echo count($size_array); ?>">SIZE</th>
					<th></th>
				</tr>
				<tr>
					<th rowspan="2" width="30">Sl</th>
					<th rowspan="2" width="80">Dates</th>
					<?
					foreach ($size_array as $key => $val) 
					{
						?>
						<th width="50"><? echo $sizearr[$key];?></th>
						<?
					}
					?>
					<th rowspan="2" width="80">Total</th>	
				</tr>
			</thead>
			<?
			$i=1;
			$size_total_array = array();
			foreach ($prod_array as $key => $val) 
			{	
				?>
				<tr>
					<td><? echo $i++;?></td>
					<td><? echo change_date_format($val);?></td>
					<?
					$h_total = 0;
					foreach ($size_array as $size_key => $value) 
					{
						?>
						<td><? echo $size_qty[$key][$size_key];?></td>
						<?
						$h_total += $size_qty[$key][$size_key];
						$size_total_array[$size_key] += $size_qty[$key][$size_key];
					}
					?>
					<td><? echo $h_total;?></td>
				</tr>
				<?
			}
			?>
			<tfoot>
				<tr>
					<td colspan="2"></td>
					<?
						$v_total = 0;
						foreach ($size_array as $key => $val) 
						{
							?>
							<td><? echo $size_total_array[$key];?></td>
							<?
							$v_total += $size_total_array[$key];
						}
						?>
					<td><? echo $v_total; ?></td>
				</tr>
			</tfoot>
		</table>
	</div>
	<?
}

if($action=="open_prod_popup_wip")
{
	$ex_data = explode("_",str_replace("'", "", $data));
	// print_r($ex_data);
	$po_id 			= $ex_data[0];
	$country_id 	= $ex_data[1];
	$item_id 		= $ex_data[2];
	$color_id 		= $ex_data[3];
	$floor_id 		= $ex_data[4];
	$sewing_line_id = $ex_data[5];
	$prod_reso_allo = $ex_data[6];
	$date_from 		= $ex_data[7];
	$date_to 		= $ex_data[8];
	$type 			= $ex_data[9]; // input, output, poly etc.
	$level 			= $ex_data[10]; // 5 sew wip, 6 poly wip, 7 sew to poly wip etc.

	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);	
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	switch ($level) 
	{
		case 5:
			 $sql = "SELECT c.color_number_id,c.size_number_id,SUM (CASE WHEN a.production_type = 4  THEN b.production_qnty  ELSE 0 END)
         - (SUM ( CASE  WHEN a.production_type = 5   THEN b.production_qnty ELSE 0 END)
         + (SUM (CASE WHEN a.production_type = 5 and b.is_rescan=0  THEN  b.reject_qty ELSE  0 END) - sum(CASE WHEN a.production_type =5 and b.is_rescan = 1 THEN b.production_qnty else 0 END))) AS production_qnty 
			from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
			WHERE a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and a.po_break_down_id in($po_id) and c.item_number_id=$item_id and c.country_id=$country_id  and c.color_number_id=$color_id and a.production_type in(4,5) and a.sewing_line in($sewing_line_id) and a.status_active=1 and c.status_active=1
			group by c.color_number_id,c.size_number_id 
			order by c.size_number_id";
			break;

		case 6:
			$sql = "SELECT c.color_number_id,c.size_number_id,SUM (CASE WHEN a.production_type = 5 AND a.production_date <= '$date_to' THEN b.production_qnty  ELSE 0 END)
         - (SUM ( CASE  WHEN a.production_type = 11 AND a.production_date <= '$date_to' THEN b.production_qnty ELSE 0 END)
         + SUM (CASE WHEN a.production_type = 11  THEN  b.reject_qty ELSE  0 END)) AS production_qnty 
			from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
			WHERE a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and a.po_break_down_id in($po_id) and c.item_number_id=$item_id and c.country_id=$country_id and c.color_number_id=$color_id and a.production_type in(5,11) and a.sewing_line in($sewing_line_id) and a.status_active=1 and c.status_active=1
			group by c.color_number_id,c.size_number_id 
			order by c.size_number_id";
			break;
		case 7:
			$sql = "SELECT c.color_number_id,c.size_number_id,SUM (CASE WHEN a.production_type = 4 AND a.production_date <= '$date_to' THEN b.production_qnty  ELSE 0 END)
         - (SUM ( CASE  WHEN a.production_type = 11 AND a.production_date <= '$date_to' THEN b.production_qnty ELSE 0 END)
         + SUM (CASE WHEN a.production_type = 11  THEN  b.reject_qty ELSE  0 END)) AS production_qnty 
			from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
			WHERE a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and a.po_break_down_id in($po_id) and c.item_number_id=$item_id and c.country_id=$country_id  and c.color_number_id=$color_id and a.production_type in(4,11) and a.sewing_line in($sewing_line_id) and a.status_active=1 and c.status_active=1
			group by c.color_number_id,c.size_number_id 
			order by c.size_number_id";
			break;	
		default:
			# code...
			break;
	}
	
	// echo $sql;
	$sql_res = sql_select($sql);
	$size_qty = array();
	$size_array = array();
	$prod_array = array();
	$prod_qnty_array = array();
	foreach ($sql_res as $row) 
	{
		$size_qty[$row[csf('color_number_id')]][$row[csf('size_number_id')]] += $row[csf('production_qnty')];
		$size_array[$row[csf('size_number_id')]] = $row[csf('size_number_id')];
		$prod_array[$row[csf('color_number_id')]] = $row[csf('color_number_id')];
	}
	$table_width = 190+(count($size_array)*50);
	?>
	<div id="data_panel" align="" style="width:100%;margin: 20px auto;text-align: center;">
		<table width="<? echo $table_width;?>" align="center" border="1" rules="all" class="rpt_table" >
			<thead>
				<tr>
					<th></th>
					<th></th>
					<th colspan="<? echo count($size_array); ?>">Size</th>
					<th></th>
				</tr>
				<tr>
					<th rowspan="2" width="30">Sl</th>
					<th rowspan="2" width="80">Color</th>
					<?
					foreach ($size_array as $key => $val) 
					{
						?>
						<th width="50"><? echo $sizearr[$key];?></th>
						<?
					}
					?>
					<th rowspan="2" width="80">Total</th>	
				</tr>
			</thead>
			<?
			$i=1;
			$size_total_array = array();
			foreach ($prod_array as $key => $val) 
			{	
				?>
				<tr>
					<td><? echo $i++;?></td>
					<td><? echo $colorarr[$val];?></td>
					<?
					$h_total = 0;
					foreach ($size_array as $size_key => $value) 
					{
						?>
						<td><? echo $size_qty[$key][$size_key];?></td>
						<?
						$h_total += $size_qty[$key][$size_key];
						$size_total_array[$size_key] += $size_qty[$key][$size_key];
					}
					?>
					<td><? echo $h_total;?></td>
				</tr>
				<?
			}
			?>
			<tfoot>
				<tr>
					<td colspan="2"></td>
					<?
						$v_total = 0;
						foreach ($size_array as $key => $val) 
						{
							?>
							<td><? echo $size_total_array[$key];?></td>
							<?
							$v_total += $size_total_array[$key];
						}
						?>
					<td><? echo $v_total; ?></td>
				</tr>
			</tfoot>
		</table>
	</div>
	<?
}

if($action=="open_prod_popup_wip2")
{
	$ex_data = explode("_",str_replace("'", "", $data));
	// print_r($ex_data);
	$po_id 			= $ex_data[0];
	$item_id 		= $ex_data[1];
	$color_id 		= $ex_data[2];
	$floor_id 		= $ex_data[3];
	$sewing_line_id = $ex_data[4];
	$prod_reso_allo = $ex_data[5];
	$date_from 		= $ex_data[6];
	$date_to 		= $ex_data[7];
	$type 			= $ex_data[8];
	$level 			= $ex_data[9];

	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);	
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	switch ($level) 
	{
		case 5:
			$sql = "SELECT b.bundle_no, c.color_number_id,c.size_number_id,SUM (CASE WHEN a.production_type = 4 AND a.production_date <= '$date_to' THEN b.production_qnty  ELSE 0 END) as ttl_input,SUM ( CASE  WHEN a.production_type = 5 AND a.production_date <= '$date_to' THEN b.production_qnty ELSE 0 END) as ttl_output ,SUM (CASE WHEN a.production_type = 5 and b.is_rescan=0  THEN  b.reject_qty ELSE  0 END) as reject_qty , sum(CASE WHEN a.production_type =5 and b.is_rescan = 1 THEN b.production_qnty else 0 END) AS production_qnty 
			from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
			WHERE a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and a.po_break_down_id in($po_id) and c.item_number_id=$item_id  and c.color_number_id=$color_id and a.production_type in(4,5) and a.sewing_line in($sewing_line_id) and a.status_active=1 and c.status_active=1
			group by b.bundle_no,c.color_number_id,c.size_number_id 
			order by c.size_number_id";
			break;

		case 6:
			$sql = "SELECT c.color_number_id,c.size_number_id,SUM (CASE WHEN a.production_type = 5 AND a.production_date <= '$date_to' THEN b.production_qnty  ELSE 0 END)
         - (SUM ( CASE  WHEN a.production_type = 11 AND a.production_date <= '$date_to' THEN b.production_qnty ELSE 0 END)
         + SUM (CASE WHEN a.production_type = 11  THEN  b.reject_qty ELSE  0 END)) AS production_qnty 
			from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
			WHERE a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and a.po_break_down_id in($po_id) and c.item_number_id=$item_id  and c.color_number_id=$color_id and a.production_type in(5,11) and a.sewing_line in($sewing_line_id) and a.status_active=1 and c.status_active=1
			group by c.color_number_id,c.size_number_id 
			order by c.size_number_id";
			break;
		case 7:
			$sql = "SELECT c.color_number_id,c.size_number_id,SUM (CASE WHEN a.production_type = 4 AND a.production_date <= '$date_to' THEN b.production_qnty  ELSE 0 END)
         - (SUM ( CASE  WHEN a.production_type = 11 AND a.production_date <= '$date_to' THEN b.production_qnty ELSE 0 END)
         + SUM (CASE WHEN a.production_type = 11 THEN  b.reject_qty ELSE  0 END)) AS production_qnty 
			from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
			WHERE a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and a.po_break_down_id in($po_id) and c.item_number_id=$item_id  and c.color_number_id=$color_id and a.production_type in(4,11) and a.sewing_line in($sewing_line_id) and a.status_active=1 and c.status_active=1
			group by c.color_number_id,c.size_number_id 
			order by c.size_number_id";
			break;	
		default:
			# code...
			break;
	}
	
	// echo $sql;
	$sql_res = sql_select($sql);
	$size_qty = array();
	$size_array = array();
	$prod_array = array();
	$prod_qnty_array = array();
	foreach ($sql_res as $row) 
	{
		if($level==5)
		{
			if($row[csf('production_qnty')]>$row[csf('reject_qty')])
			{
				$row[csf('production_qnty')]=$row[csf('reject_qty')];
			}
			$row[csf('production_qnty')]=$row[csf('reject_qty')]-$row[csf('production_qnty')];
			$size_qty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]  +=($row[csf("ttl_input")])-($row[csf("ttl_output")]+$row[csf('production_qnty')]) ;
			$size_array[$row[csf('size_number_id')]] = $row[csf('size_number_id')];
			$prod_array[$row[csf('color_number_id')]] = $row[csf('color_number_id')];

		}
		else 
		{
			$size_qty[$row[csf('color_number_id')]][$row[csf('size_number_id')]] += $row[csf('production_qnty')];
			$size_array[$row[csf('size_number_id')]] = $row[csf('size_number_id')];
			$prod_array[$row[csf('color_number_id')]] = $row[csf('color_number_id')];
		}
		
	}
	$table_width = 190+(count($size_array)*50);
	?>
	<div id="data_panel" align="" style="width:100%;margin: 20px auto;text-align: center;">
		<table width="<? echo $table_width;?>" align="center" border="1" rules="all" class="rpt_table" >
			<thead>
				<tr>
					<th></th>
					<th></th>
					<th colspan="<? echo count($size_array); ?>">Size</th>
					<th></th>
				</tr>
				<tr>
					<th rowspan="2" width="30">Sl</th>
					<th rowspan="2" width="80">Color</th>
					<?
					foreach ($size_array as $key => $val) 
					{
						?>
						<th width="50"><? echo $sizearr[$key];?></th>
						<?
					}
					?>
					<th rowspan="2" width="80">Total</th>	
				</tr>
			</thead>
			<?
			$i=1;
			$size_total_array = array();
			foreach ($prod_array as $key => $val) 
			{	
				?>
				<tr>
					<td><? echo $i++;?></td>
					<td><? echo $colorarr[$val];?></td>
					<?
					$h_total = 0;
					foreach ($size_array as $size_key => $value) 
					{
						?>
						<td><? echo $size_qty[$key][$size_key];?></td>
						<?
						$h_total += $size_qty[$key][$size_key];
						$size_total_array[$size_key] += $size_qty[$key][$size_key];
					}
					?>
					<td><? echo $h_total;?></td>
				</tr>
				<?
			}
			?>
			<tfoot>
				<tr>
					<td colspan="2"></td>
					<?
						$v_total = 0;
						foreach ($size_array as $key => $val) 
						{
							?>
							<td><? echo $size_total_array[$key];?></td>
							<?
							$v_total += $size_total_array[$key];
						}
						?>
					<td><? echo $v_total; ?></td>
				</tr>
			</tfoot>
		</table>
	</div>
	<?
}
?>