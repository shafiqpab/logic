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
	
	$sql = "SELECT a.id,a.po_number,a.job_no_mst,b.style_ref_no,b.job_no_prefix_num,$select_date as year from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no $lc_company_cond $buyer_cond and a.status_active in(1,2,3) and b.status_active=1"; 
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
	if($db_type==0)
	{
		$select_date=" year(b.insert_date)";
	}
	else if($db_type==2)
	{
		$select_date=" to_char(b.insert_date,'YYYY')";
	}
	
	$sql = "SELECT a.id,a.po_number,a.job_no_mst,b.style_ref_no,b.job_no_prefix_num,$select_date as year from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no $lc_company_cond $buyer_cond and a.status_active in(1,2,3) and b.status_active=1"; 
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
	if($db_type==0)
	{
		$select_date=" year(b.insert_date)";
	}
	else if($db_type==2)
	{
		$select_date=" to_char(b.insert_date,'YYYY')";
	}
	
	$sql = "SELECT a.id,a.po_number,a.job_no_mst,b.style_ref_no,b.job_no_prefix_num,$select_date as year from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no $lc_company_cond $buyer_cond and a.status_active in(1,2,3) and b.status_active=1"; 
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
	$companyArr 	= return_library_array("select id,company_name from lib_company","id","company_name"); 
	$countryArr 	= return_library_array("select id,country_name from lib_country","id","country_name"); 
	$buyerArr 		= return_library_array("select id,short_name from lib_buyer","id","short_name");
	$floorArr 		= return_library_array("select id,floor_name from lib_prod_floor where company_id=$cbo_company_id and production_process=5 order by floor_name","id","floor_name");
	// ================================= GETTING FORM DATA ====================================
	$lc_company_id 		= str_replace("'","",$cbo_company_id);
	$buyer_id 			= str_replace("'","",$cbo_buyer_id);
	$job_no 			= str_replace("'","",$txt_job_no);
	$style_ref_no 		= str_replace("'","",$txt_style_ref_no);
	$order_no 			= str_replace("'","",$txt_order_no);
	$order_id 			= str_replace("'","",$hiden_order_id);
	$report_title 		= str_replace("'","",$report_title);
	$txt_date_from 		= str_replace("'","",$txt_date_from);
	$txt_date_to 		= str_replace("'","",$txt_date_to);	
	
	//******************************************* MAKE QUERY CONDITION ************************************************
	$sql_cond = "";
	$sql_cond .= ($lc_company_id==0) 		? "" : " and a.company_name=$lc_company_id";
	$sql_cond .= ($buyer_id==0) 			? "" : " and a.buyer_name=$buyer_id";
	$sql_cond .= ($style_ref_no=="") 		? "" : " and a.style_ref_no in($style_ref_no)";
	$sql_cond .= ($inspection_result==0) 	? "" : " and d.inspection_status in($inspection_result)";
	if($order_id =="" && $order_no !=""){ $sql_cond .= " and b.po_number ='$order_no'";}
	if($order_id !="")
	{
		$po_id_arr = explode(",", $order_id);
		if(count($po_id_arr)>999 && $db_type==2)
	    {
	     	$po_chunk=array_chunk($po_id_arr, 999);
	     	$po_ids_cond= "";
	     	foreach($po_chunk as $vals)
	     	{
	     		$imp_ids=implode(",", $vals);
	     		if($po_ids_cond=="") 
	     		{
	     			$po_ids_cond.=" and ( b.id in ($imp_ids) ";
	     		}
	     		else
	     		{
	     			$po_ids_cond.=" or b.id in ($imp_ids) ";
	     		}
	     	}
	     	 $po_ids_cond.=" )";
	    }
	    else
	    {
	     	$po_ids_cond= " and b.id in($order_id) ";
	    }
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
	if($type==1)
	{		
		// ================================================ MAIN QUERY ==================================================
		$sql="SELECT a.style_ref_no as style,b.id as po_id,b.po_number,c.country_id,c.country_ship_date,c.cutup,(a.total_set_qnty*c.order_quantity) as order_qty
		from  wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c
		where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id  $sql_cond $sql_cond2 $po_ids_cond and a.status_active=1 and b.status_active=1 and c.status_active=1  and c.cutup <> 0 order by c.cutup";
		// echo $sql;die();
		$sql_res = sql_select($sql);
		if (count($sql_res) < 1) 
		{	
			?>
			<style type="text/css">
				.alert 
				{
					padding: 12px 35px 12px 14px;
					margin-bottom: 18px;
					text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
					background-color: #fcf8e3;
					border: 1px solid #fbeed5;
					-webkit-border-radius: 4px;
					-moz-border-radius: 4px;
					border-radius: 4px;
					color: #c09853;
					font-size: 16px;
				}
				.alert strong{font-size: 18px;}
				.alert-danger,
				.alert-error 
				{
				  	background-color: #f2dede;
				  	border-color: #eed3d7;
				  	color: #b94a48;
				}
			</style>
			<div style="margin:20px auto; width: 90%">
				<div class="alert alert-error">
				  <strong>Sorry!</strong> Data not available. Please try again after something change.
				</div>
			</div>
			<?
			die();
		}

		$main_array = array();
		$poId_arr = array();
		$country_id_arr = array();
		foreach ($sql_res as $row) 
		{
			$main_array[$row[csf('style')]][$row[csf('po_id')]][$row[csf('cutup')]][$row[csf('country_ship_date')]][$row[csf('country_id')]]['order_qty'] += $row[csf('order_qty')];

			$main_array[$row[csf('style')]][$row[csf('po_id')]][$row[csf('cutup')]][$row[csf('country_ship_date')]][$row[csf('country_id')]]['po_number'] = $row[csf('po_number')];

			$poId_arr[$row[csf('po_id')]] = $row[csf('po_id')];
			$country_id_arr[$row[csf('country_id')]] = $row[csf('country_id')];
		}
		unset($sql_res);
		$all_po_id = implode(",", $poId_arr);
		$all_country_id = implode(",", $country_id_arr);

		if(count($poId_arr)>999 && $db_type==2)
	    {
	     	$po_chunk=array_chunk($poId_arr, 999);
	     	$prod_po_ids_cond= "";
	     	foreach($po_chunk as $vals)
	     	{
	     		$imp_ids=implode(",", $vals);
	     		if($prod_po_ids_cond=="") 
	     		{
	     			$prod_po_ids_cond.=" and ( b.id in ($imp_ids) ";
	     		}
	     		else
	     		{
	     			$prod_po_ids_cond.=" or b.id in ($imp_ids) ";
	     		}
	     	}
	     	 $prod_po_ids_cond.=" )";
	    }
	    else
	    {
	     	$prod_po_ids_cond= " and b.id in($all_po_id) ";
	    }
		// echo "<pre>";
		// print_r($floor_wise_qty_arr);
		// echo "</pre>";
		// die();
	    // ================================================ MAIN QUERY ==================================================
		$sql_prod="SELECT a.style_ref_no as style,b.id as po_id,b.po_number,c.country_id,c.country_ship_date,c.cutup,d.floor_id,e.production_qnty as qty,(a.total_set_qnty*c.order_quantity) as order_qty
		from  wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, pro_garments_production_mst d,pro_garments_production_dtls e
		where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and b.id=d.po_break_down_id and d.id=e.mst_id and d.production_type=4 $sql_cond $sql_cond2 $po_ids_cond $prod_po_ids_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 and c.id=e.color_size_break_down_id and c.cutup <> 0 order by c.cutup";
		// echo $sql_prod;die();
		$sql_prod_res = sql_select($sql_prod);
		
	
		$floor_wise_qty_arr = array();
		foreach ($sql_prod_res as $row) 
		{
			$floor_wise_qty_arr[$row[csf('style')]][$row[csf('po_id')]][$row[csf('cutup')]][$row[csf('country_ship_date')]][$row[csf('country_id')]][$row[csf('floor_id')]]['qty'] += $row[csf('qty')];
		}
		unset($sql_res);

	    // =============================================== Exfactory =========================================
		// $sql_exfact = "SELECT a.style_ref_no as style,b.id as po_id,f.cutup,f.country_ship_date as cdate,f.country_id,e.delivery_floor_id, d.production_qnty as ex_fact_qty from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c, pro_ex_factory_dtls d, pro_ex_factory_delivery_mst e, wo_po_color_size_breakdown f where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.job_no=f.job_no_mst and b.id=f.po_break_down_id and c.id=d.mst_id and e.id=c.delivery_mst_id and f.id=d.color_size_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active in(1,2,3) and f.is_deleted=0 $prod_po_ids_cond ";
		$sql_exfact = "SELECT b.id as po_id,c.country_id, c.ex_factory_qnty as ex_fact_qty from wo_po_break_down b, pro_ex_factory_mst c, pro_ex_factory_delivery_mst e where b.id=c.po_break_down_id and e.id=c.delivery_mst_id  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $prod_po_ids_cond ";
		// echo $sql_exfact;die();
		$exfact_res = sql_select($sql_exfact);
		$exfact_qty_array = array();
		foreach ($exfact_res as  $val) 
		{
			// $exfact_qty_array[$val[csf('style')]][$val[csf('po_id')]][$val[csf('cutup')]][$val[csf('cdate')]][$val[csf('country_id')]] += $val[csf('ex_fact_qty')];
			$exfact_qty_array[$val[csf('po_id')]][$val[csf('country_id')]] += $val[csf('ex_fact_qty')];
		}
		unset($exfact_res);

		$rowspan_arr = array();
		$cutup_rowspan_arr = array();
		foreach ($main_array as $style => $style_data) 
		{			
			foreach ($style_data as $po_id => $po_data) 
			{
				foreach ($po_data as $cutup => $cutup_data) 
				{
					foreach ($cutup_data as $country_ship_date => $country_ship_date_data) 
					{
						foreach ($country_ship_date_data as $country_id => $country_data) 
						{							 								
							$rowspan_arr[$style][$po_id]++;
							$cutup_rowspan_arr[$style][$po_id][$cutup]++;	
						}
					}
					
				}
			}			
		}
		// echo "<pre>";print_r($cutup_rowspan_arr);die();
		$table_width = 930+count($floorArr)*80;
		ob_start();
		?>
		<style type="text/css">
			table tr th, table tr td{ word-wrap: break-word;word-break: break-all; }
		</style>
		<div class="main" style="margin: 0 auto; padding: 10px;  width: 100%">
			<table width="<? echo $table_width;?>" cellspacing="0">
		        <tr class="form_caption" style="border:none;">
		            <td colspan="<? echo count($floorArr)+11;?>" align="center" ><strong style="font-size: 19px"><u><? echo $companyArr[$lc_company_id]; ?></u></strong></td>
		        </tr>
		        <tr class="form_caption" style="border:none;">
		            <td colspan="<? echo count($floorArr)+11;?>" align="center"><strong style="font-size: 17px">Cutt off Date Wise Input Report</strong></td>
		        </tr>		        
		        <tr class="form_caption" style="border:none;">
		            <td colspan="<? echo count($floorArr)+11;?>" align="center"><strong style="font-size: 15px">Date Range : <? echo change_date_format($txt_date_from); ?> to <? echo change_date_format($txt_date_to); ?></strong></td>
		        </tr>
		    </table>
		    <!-- ================================================ DETAIS PART ================================================ -->
		    <div>
		    	<table class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" id="" width="<? echo $table_width;?>">
		    		<thead>
			    		<tr>
			    			<th rowspan="2" width="30">Sl.</th>
			    			<th rowspan="2" width="100">Order No</th>
			    			<th rowspan="2" width="100">Style Ref.</th>
			    			<th rowspan="2" width="100">Cuttoff Date</th>
			    			<th rowspan="2" width="100">Country Ship Date</th>
			    			<th rowspan="2" width="100">Country</th>
			    			<th rowspan="2" width="80">Pcs/Qty</th>
			    			<th colspan="<? echo count($floorArr);?>" width="<? echo count($floorArr)*80;?>">Sewing Input</th>
			    			<th rowspan="2" width="80">Total Input</th>
			    			<th rowspan="2" width="80">Input Balance</th>
			    			<th rowspan="2" width="80">Exfactory</th>
			    			<th rowspan="2" width="80">Ex. Balance</th>
		    			</tr>
		    			<tr>
		    				<?
		    				foreach ($floorArr as  $val) 
		    				{
		    					?>
		    					<th width="80"><? echo $val;?></th>
		    					<?
		    				}
		    				?>
		    			</tr>
		    		</thead>
		    	</table>
		    	
		    	<div style="width: <? echo $table_width+20;?>px; overflow-y: scroll; max-height: 400px" id="scroll_body">
		    		<table cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all" width="<? echo $table_width;?>" id="html_search">
		    			<?
		    			$gr_order_qty 		= 0;
		    			$gr_input_qty 		= 0;
		    			$gr_input_bal_qty 	= 0;
		    			$gr_exfact_qty 		= 0;
		    			$gr_exfact_bal_qty 	= 0;
		    			$sl=1;
		    			$floor_total_array = array();
	    				foreach ($main_array as $style => $style_data) 
	    				{
    						foreach ($style_data as $po_id => $po_data) 
	    					{
	    						$r = 0; 
	    						foreach ($po_data as $cutup => $cutup_data) 
	    						{
	    							$rr = 0;
	    							foreach ($cutup_data as $shipdate => $shipdate_data) 
	    							{ 
	    								foreach ($shipdate_data as $country_id => $row) 
	    								{ 
	    									$extractQty = $exfact_qty_array[$po_id][$country_id];
    										$bgcolor = ($sl%2==0) ? "#ffffff" : "#f6faff";
    										
	    									?>
	    										<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_1nd<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $sl; ?>" >
	    											<? if($r==0){?>
	    											<td valign="middle" rowspan="<? echo $rowspan_arr[$style][$po_id];?>" width="30"><? echo $sl; ?></td>
									    			<td valign="middle" rowspan="<? echo $rowspan_arr[$style][$po_id];?>" width="100"><? echo $row['po_number']; ?></td>
									    			<td valign="middle" rowspan="<? echo $rowspan_arr[$style][$po_id];?>" width="100"><? echo $style; ?></td>
									    			<? $sl++; } ?>
									    			<? if($rr==0){?>
									    			<td valign="middle" rowspan="<? echo $cutup_rowspan_arr[$style][$po_id][$cutup];?>" align="center" width="100"><? echo $cut_up_array[$cutup]; ?></td>
									    			<td valign="middle" rowspan="<? echo $cutup_rowspan_arr[$style][$po_id][$cutup];?>" align="center" width="100"><? echo change_date_format($shipdate); ?></td>
									    			<? }?>
									    			<td align="left" width="100" title="<? echo $country_id;?>"><? echo $countryArr[$country_id]; ?></td>
									    			<td align="right" width="80"><? echo $row['order_qty']; ?></td>
									    			<?
									    			$all_floor_total = 0;
									    			foreach ($floorArr as $fkey => $val) 
									    			{
									    				$floor_qty = $floor_wise_qty_arr[$style][$po_id][$cutup][$shipdate][$country_id][$fkey]['qty'];
									    				?>
									    					<td align="right" width="80"><? echo $floor_qty; ?></td>
									    				<?
									    				$all_floor_total += $floor_qty;
									    				$floor_total_array[$fkey] += $floor_qty;
									    			}
									    			$input_balance = $row['order_qty'] - $all_floor_total;
									    			?>
									    			<td align="right" width="80"><? echo $all_floor_total; ?></td>
									    			<td align="right" width="80"><? echo $input_balance; ?></td>
									    			<td align="right" width="80"><? echo $extractQty; ?></td>
									    			<td align="right" width="80"><? echo $exBalance = $row['order_qty'] - $extractQty; ?></td>			    											
	    										</tr>
	    									<?
	    									$r++;
	    									$rr++;
							    			$gr_order_qty 		+= $row['order_qty'];
							    			$gr_input_qty 		+= $all_floor_total;
							    			$gr_input_bal_qty 	+= $input_balance;
							    			$gr_exfact_qty 		+= $extractQty;
							    			$gr_exfact_bal_qty 	+= $exBalance;					    								
		    								
		    							}
		    						}
		    					}
	    					}
	    				}
		    			?>
		    		</table>
		    	</div>
		    	<div style="width: <? echo $table_width;?>+20px;"">
		    		<table cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width;?>">
		    			<tfoot>
		    				<tr>
		    					<th width="30"></th>
				    			<th width="100"></th>
				    			<th width="100"></th>
				    			<th width="100"></th>
				    			<th width="100"></th>
				    			<th width="100"></th>
				    			<th align="right" width="80"><? echo $gr_order_qty;?></th>
				    			<?
				    			foreach ($floorArr as $fKey => $val) 
				    			{
				    				?>
				    					<th align="right" width="80"><? echo $floor_total_array[$fKey]; ?></th>
				    				<?
				    			}
				    			?>
				    			<th align="right" width="80"><? echo $gr_input_qty;?></th>
				    			<th align="right" width="80"><? echo $gr_input_bal_qty;?></th>
				    			<th align="right" width="80"><? echo $gr_exfact_qty;?></th>
				    			<th align="right" width="80"><? echo $gr_exfact_bal_qty;?></th>
		    			</tfoot>
		    		</table>
		    </div>
	    </div>
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