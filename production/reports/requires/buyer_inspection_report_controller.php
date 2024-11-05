<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 120, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id in($data) 
	order by location_name","id,location_name", 0, "-- Select Location --", $selected, "load_drop_down( 'requires/buyer_inspection_report_controller', document.getElementById('cbo_working_company_id').value+'_'+this.value, 'load_drop_down_floor', 'floor_td' );",0 );
	exit();    	 
}

if ($action=="load_drop_down_floor")
{
	$ex_data = explode("_", $data);
	echo create_drop_down( "cbo_floor_id", 120, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and production_process=11 and company_id in($ex_data[0]) and location_id in($ex_data[1]) order by floor_name","id,floor_name", 0, "-- Select Floor --", $selected, "",0 );     	 	
	exit();	 
}

if ($action == "eval_multi_select") {
    echo "set_multiselect('cbo_line_id','0','0','','0');\n";
    // echo "setTimeout[($('#floor_td a').attr('onclick','disappear_list(cbo_floor,0);getCompanyId();') ,3000)];\n";
    exit();
}

if ($action=="load_drop_down_line")
{
	$explode_data = explode("_",$data);
	// print_r($explode_data);
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name in($explode_data[0]) and variable_list=23 and is_deleted=0 and status_active=1");
	$txt_date = $explode_data[3];
	
	$cond="";
	if($prod_reso_allo==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_array=array();
		
		if($txt_date=="")
		{
			if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_id in($explode_data[1])";
			if( $explode_data[0]!=0 ) $cond .= " and floor_id in($explode_data[2])";
			$line_data=sql_select("select id, line_number from prod_resource_mst where is_deleted=0 $cond");
			// echo "select id, line_number from prod_resource_mst where is_deleted=0 $cond";
		}
		else
		{
			if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and a.location_id in($explode_data[1])";
			if( $explode_data[0]!=0 ) $cond = " and a.floor_id in($explode_data[2])";
		 if($db_type==0)	$data_format="and b.pr_date='".change_date_format($txt_date,'yyyy-mm-dd')."'";
		 if($db_type==2)	$data_format="and b.pr_date='".change_date_format($txt_date,'','',1)."'";
	
			$line_data=sql_select( "select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id $data_format and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number order by a.line_number");
		}
		
		foreach($line_data as $row)
		{
			$line='';
			$line_number=explode(",",$row[csf('line_number')]);
			foreach($line_number as $val)
			{
				if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
			}
			$line_array[$row[csf('id')]]=$line;
		}

		echo create_drop_down( "cbo_line_id", 120,$line_array,"", "", "-- Select --", $selected, "",0,0 );
	}
	else
	{
		if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_name in($explode_data[1])";
		if( $explode_data[0]!=0 ) $cond = " and floor_name in($explode_data[0])";
		// echo "select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name";
		echo create_drop_down( "cbo_line_id", 120, "select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name","id,line_name", "", "-- Select --", $selected, "",0,0 );
	}
	exit();
}

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
	$w_company=str_replace("'","",$w_company);
	$lc_company=str_replace("'","",$lc_company);
	$job_year=str_replace("'","",$job_year);
	$txt_style_ref_id=str_replace("'","",$txt_style_ref_id);
	
	if($lc_company!=0) $lc_company_cond="and b.company_name=$lc_company"; else $lc_company_cond="";
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
	$sql = "SELECT a.id,a.po_number,a.job_no_mst,b.style_ref_no,b.job_no_prefix_num,$select_date as year from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no $lc_company_cond $buyer_cond $job_year_cond  $style_cond and a.status_active in(1,2,3) and b.status_active=1"; 
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
		if($job_year!=0) $job_year_cond=" and year(b.insert_date)=$job_year"; else $job_year_cond="";
		$select_date=" year(b.insert_date)";
	}
	else if($db_type==2)
	{
		if($job_year!=0) $job_year_cond=" and to_char(b.insert_date,'YYYY')=$job_year"; else $job_year_cond="";
		$select_date=" to_char(b.insert_date,'YYYY')";
	}
	if($txt_style_ref_id!="") $style_cond=" and b.id in($txt_style_ref_id)"; else $style_cond="";
	$sql = "SELECT a.id,a.po_number,a.job_no_mst,b.style_ref_no,b.job_no_prefix_num,$select_date as year from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no $lc_company_cond $buyer_cond $job_year_cond  $style_cond and a.status_active in(1,2,3) and b.status_active=1"; 
	//echo $sql; die;
	echo create_list_view("list_view", "Year,Job No,Style Ref No,Order NO","70,50,150,150","500","310",0, $sql , "js_set_value", "id,style_ref_no", "", 1, "0", $arr, "year,job_no_prefix_num,style_ref_no,po_number", "","setFilterGrid('list_view',-1)","0","",1) ;	

	echo "<input type='hidden' id='txt_selected_po' />";
	echo "<input type='hidden' id='txt_selected_style' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	exit();
}

if($action=="report_generate") 
{
	// var_dump($_REQUEST);die();
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$companyArr 	= return_library_array("select id,company_name from lib_company","id","company_name"); 
	$countryArr 	= return_library_array("select id,country_name from lib_country","id","country_name"); 
	$buyerArr 		= return_library_array("select id,short_name from lib_buyer","id","short_name"); 
	$colorArr 		= return_library_array("select id,color_name from lib_color","id","color_name"); 
	$locationArr	= return_library_array("select id,location_name from lib_location","id","location_name"); 
	$floorArr 		= return_library_array("select id,floor_name from lib_prod_floor","id","floor_name"); 
	$seasonArr 		= return_library_array("select id,season_name from lib_buyer_season","id","season_name"); 
	$prod_reso_arr  = return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	// ================================= GETTING FORM DATA ====================================
	$working_company_id =str_replace("'","",$cbo_working_company_id);
	$location_id 		=str_replace("'","",$cbo_location_id);
	$floor_id 			=str_replace("'","",$cbo_floor_id);
	$lc_company_id 		=str_replace("'","",$cbo_lc_company_id);
	$buyer_id 			=str_replace("'","",$cbo_buyer_id);
	$job_year 			=str_replace("'","",$cbo_job_year);
	$job_no 			=str_replace("'","",$txt_job_no);
	$style_ref_no 		=str_replace("'","",$txt_style_ref_no);
	$date_type 			=str_replace("'","",$cbo_date_type);
	$inspection_stat 	=str_replace("'","",$cbo_inspection_status);
	$inspection_result 	=str_replace("'","",$cbo_inspection_result);
	$order_id 			=str_replace("'","",$hiden_order_id);
	$report_title 		=str_replace("'","",$report_title);
	$txt_date_from 		=str_replace("'","",$txt_date_from);
	$txt_date_to 		=str_replace("'","",$txt_date_to);	
	
	//******************************************* MAKE QUERY CONDITION ************************************************
	$sql_cond = "";
	$sql_cond .= ($working_company_id=="") 	? "" : " and d.working_company in($working_company_id)";
	$sql_cond .= ($location_id=="") 		? "" : " and d.working_location in($location_id)";
	$sql_cond .= ($floor_id=="") 			? "" : " and d.working_floor in($floor_id)";
	$sql_cond .= ($line_id=="") 			? "" : " and d.sewing_line in($line_id)";
	$sql_cond .= ($lc_company_id==0) 		? "" : " and a.company_name=$lc_company_id";
	$sql_cond .= ($buyer_id==0) 			? "" : " and a.buyer_name=$buyer_id";
	$sql_cond .= ($job_no=="") 				? "" : " and a.job_no_prefix_num in($job_no)";
	$sql_cond .= ($style_ref_no=="") 		? "" : " and a.style_ref_no ='$style_ref_no'";
	$sql_cond .= ($inspection_result==0) 	? "" : " and d.inspection_status in($inspection_result)";

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

		if($date_type==2)
		{
			$sql_cond2=" and b.pub_shipment_date BETWEEN '$txt_datefrom' and '$txt_dateto'";
		}
		else if($date_type==3)
		{
			$sql_cond2=" and d.inspection_date BETWEEN '$txt_datefrom' and '$txt_dateto'";
		}
		else
		{
			$sql_cond2=" and c.country_ship_date BETWEEN '$txt_datefrom' and '$txt_dateto'";
		}

		// $sql_cond2 .= " and d.inspection_date between '$txt_datefrom' and '$txt_dateto'";
	}

	if($job_year>0)
	{
		if($db_type==0)
		{
			$sql_cond2 .=" and year(a.insert_date)='$job_year'";
		}
		else
		{
			$sql_cond2 .=" and to_char(a.insert_date,'YYYY')='$job_year'";
		}	
	}	

	// echo $sql_cond;die();
	if($type==1)
	{		
		// ================================================ MAIN QUERY ==================================================
		$sql="SELECT a.company_name, a.job_no_prefix_num as job_id,a.season_buyer_wise, a.buyer_name,a.client_id as buyer_client,a.style_ref_no as style,a.style_description,a.product_dept,a.product_category,b.id as po_id,b.po_number,c.country_id,c.country_ship_date,c.item_number_id as item_id,c.color_number_id as color_id,d.working_company,d.working_location,d.working_floor,d.week_id,d.inspected_by,d.inspection_date,d.inspection_status,d.inspection_cause,d.ins_reason,d.country_id,e.item_id,e.color_id
		from  wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, pro_buyer_inspection d,pro_buyer_inspection_breakdown e
		where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and b.id=d.po_break_down_id and d.country_id=c.country_id and d.id=e.mst_id  $sql_cond $sql_cond2 $po_ids_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 and d.inspection_qnty>0
		order by d.inspection_date";
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
				  <strong>Oh Snap!</strong> Change a few things up and try submitting again.
				</div>
			</div>
			<?
			die();
		}

		$main_array = array();
		$poId_arr = array();
		$country_id_arr = array();
		$item_id_arr = array();
		$color_id_arr = array();
		foreach ($sql_res as $row) 
		{
			$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_id')]][$row[csf('color_id')]][$row[csf('inspection_date')]][$row[csf('inspection_status')]]['company_name'] = $row[csf('company_name')];

			$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_id')]][$row[csf('color_id')]][$row[csf('inspection_date')]][$row[csf('inspection_status')]]['working_company'] = $row[csf('working_company')];

			$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_id')]][$row[csf('color_id')]][$row[csf('inspection_date')]][$row[csf('inspection_status')]]['working_location'] = $row[csf('working_location')];

			$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_id')]][$row[csf('color_id')]][$row[csf('inspection_date')]][$row[csf('inspection_status')]]['working_floor'] = $row[csf('working_floor')];

			$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_id')]][$row[csf('color_id')]][$row[csf('inspection_date')]][$row[csf('inspection_status')]]['season'] = $row[csf('season_buyer_wise')];

			$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_id')]][$row[csf('color_id')]][$row[csf('inspection_date')]][$row[csf('inspection_status')]]['style_description'] = $row[csf('style_description')];

			$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_id')]][$row[csf('color_id')]][$row[csf('inspection_date')]][$row[csf('inspection_status')]]['product_dept'] = $row[csf('product_dept')];

			$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_id')]][$row[csf('color_id')]][$row[csf('inspection_date')]][$row[csf('inspection_status')]]['product_category'] = $row[csf('product_category')];

			$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_id')]][$row[csf('color_id')]][$row[csf('inspection_date')]][$row[csf('inspection_status')]]['po_number'] = $row[csf('po_number')];

			$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_id')]][$row[csf('color_id')]][$row[csf('inspection_date')]][$row[csf('inspection_status')]]['country_ship_date'] = $row[csf('country_ship_date')];

			$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_id')]][$row[csf('color_id')]][$row[csf('inspection_date')]][$row[csf('inspection_status')]]['week'] = $row[csf('week_id')];

			$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_id')]][$row[csf('color_id')]][$row[csf('inspection_date')]][$row[csf('inspection_status')]]['inspected_by'] = $row[csf('inspected_by')];

			$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_id')]][$row[csf('color_id')]][$row[csf('inspection_date')]][$row[csf('inspection_status')]]['inspection_cause'] = $row[csf('inspection_cause')];

			$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_id')]][$row[csf('color_id')]][$row[csf('inspection_date')]][$row[csf('inspection_status')]]['ins_reason'] = $row[csf('ins_reason')];

			$poId_arr[$row[csf('po_id')]] = $row[csf('po_id')];
			$country_id_arr[$row[csf('country_id')]] = $row[csf('country_id')];
			$item_id_arr[$row[csf('item_id')]] = $row[csf('item_id')];
			$color_id_arr[$row[csf('color_id')]] = $row[csf('color_id')];
			// $buyer_summary_arr[$row[csf('buyer_name')]] = $row[csf('buyer_name')];
		}
		
		$all_po_id = implode(",", $poId_arr);
		$all_country_id = implode(",", $country_id_arr);
		$all_item_id = implode(",", $item_id_arr);
		$all_color_id = implode(",", $color_id_arr);

		if(count($poId_arr)>999 && $db_type==2)
	    {
	     	$po_chunk=array_chunk($poId_arr, 999);
	     	$prod_po_ids_cond= "";
	     	foreach($po_chunk as $vals)
	     	{
	     		$imp_ids=implode(",", $vals);
	     		if($prod_po_ids_cond=="") 
	     		{
	     			$prod_po_ids_cond.=" and ( a.po_break_down_id in ($imp_ids) ";
	     		}
	     		else
	     		{
	     			$prod_po_ids_cond.=" or a.po_break_down_id in ($imp_ids) ";
	     		}
	     	}
	     	 $prod_po_ids_cond.=" )";
	    }
	    else
	    {
	     	$prod_po_ids_cond= " and a.po_break_down_id in($all_po_id) ";
	    }
		// echo "<pre>";
		// print_r($main_array);
		// echo "</pre>";
		// die();
	    // print_r($inspection_status);


	    $sql_cond3 = "";
		$sql_cond3 .= ($working_company_id=="") ? "" : " and a.working_company in($working_company_id)";
		$sql_cond3 .= ($location_id=="") 		? "" : " and a.working_location in($location_id)";
		$sql_cond3 .= ($floor_id=="") 			? "" : " and a.working_floor in($floor_id)";
		$date_cond = "";
		if($txt_datefrom !="" && $txt_dateto !="")
		{
			$date_cond = ($date_type==3) 			? " and a.inspection_date between '$txt_datefrom' and '$txt_dateto'" : "";
		}

		// ================================================ INSPECTION QTY QUERY ==================================================
		 $insp_sql="SELECT a.job_no,a.po_break_down_id as po_id,a.country_id,b.item_id,b.color_id,a.inspection_date,a.inspection_status,c.buyer_name,
		sum(b.ins_qty ) as inspection_qnty,
		sum(case when a.inspection_status=1 then b.ins_qty else 0 end) as pass_qty,
		sum(case when a.inspection_status in(2,4,5) then b.ins_qty else 0 end) as re_check_qty,
		sum(case when a.inspection_status=3 then b.ins_qty else 0 end) as fail_qty,
		sum(case when a.inspection_status=1 then 1 else 0 end) as pass_count,
		sum(case when a.inspection_status in(2,4,5) then 1 else 0 end) as re_check_count,
		sum(case when a.inspection_status=3 then 1 else 0 end) as fail_count,
		sum(a.po_break_down_id) over (partition by a.po_break_down_id) as conduct_po
		from  pro_buyer_inspection a, pro_buyer_inspection_breakdown b,wo_po_details_master c  
		where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.job_no=c.job_no and a.country_id in($all_country_id) and b.item_id in($all_item_id) and b.color_id in($all_color_id) $prod_po_ids_cond $sql_cond3 $date_cond and a.inspection_qnty>0
		group by a.job_no,a.po_break_down_id,a.country_id,b.item_id,b.color_id,a.inspection_date,a.inspection_status,c.buyer_name
		order by a.job_no";
		// echo $insp_sql;die();
		$insp_sql_res = sql_select($insp_sql);
		
		$insp_qty_array = array();
		$buyer_summary_arr = array();
		$po_wise_pass_count=array();
		$po_wise_fail_count=array();
		$po_wise_recheck_count=array();
		foreach ($insp_sql_res as $row) 
		{
			if( $row[csf("inspection_status")]==1 )
			{
				if($po_wise_pass_count[$row[csf('buyer_name')]][$row[csf('po_id')]]=="")
				{
					$buyer_summary_arr[$row[csf('buyer_name')]]['pass_count'] 		+= 1;
					$po_wise_pass_count[$row[csf('buyer_name')]][$row[csf('po_id')]]=420;
				}

			}
			if( $row[csf("inspection_status")]==2  || $row[csf("inspection_status")]==4 || $row[csf("inspection_status")]==5)
			{
				if($po_wise_recheck_count[$row[csf('buyer_name')]][$row[csf('po_id')]]=="")
				{
					$buyer_summary_arr[$row[csf('buyer_name')]]['re_check_count'] 		+= 1;
					$po_wise_recheck_count[$row[csf('buyer_name')]][$row[csf('po_id')]]=420;
				}

			}

			/*if( $row[csf("inspection_status")]==3 )
			{
				if($po_wise_fail_count[$row[csf('po_id')]]=="")
				{
					$buyer_summary_arr[$row[csf('buyer_name')]]['fail_count'] +=  1;
					$po_wise_fail_count[$row[csf('po_id')]]=420;
				}

			}*/
			$insp_qty_array[$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_id')]][$row[csf('color_id')]][$row[csf('inspection_date')]][$row[csf('inspection_status')]]['insp_qty'] = $row[csf('inspection_qnty')];
			$insp_qty_array[$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_id')]][$row[csf('color_id')]][$row[csf('inspection_date')]][$row[csf('inspection_status')]]['pass_qty'] = $row[csf('pass_qty')];
			$insp_qty_array[$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_id')]][$row[csf('color_id')]][$row[csf('inspection_date')]][$row[csf('inspection_status')]]['re_check_qty'] = $row[csf('re_check_qty')];
			$insp_qty_array[$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_id')]][$row[csf('color_id')]][$row[csf('inspection_date')]][$row[csf('inspection_status')]]['fail_qty'] = $row[csf('fail_qty')];
			$insp_qty_array[$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_id')]][$row[csf('color_id')]][$row[csf('inspection_date')]][$row[csf('inspection_status')]]['re_check_count'] += $row[csf('re_check_count')];
			$insp_qty_array[$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_id')]][$row[csf('color_id')]][$row[csf('inspection_date')]][$row[csf('inspection_status')]]['fail_count'] += $row[csf('fail_count')];

			$buyer_summary_arr[$row[csf('buyer_name')]]['conduct_po'] 		= $row[csf('conduct_po')];
			//$buyer_summary_arr[$row[csf('buyer_name')]]['pass_count'] 		+= $row[csf('pass_count')];
			//$buyer_summary_arr[$row[csf('buyer_name')]]['re_check_count'] 	+= $row[csf('re_check_count')];
			//$buyer_summary_arr[$row[csf('buyer_name')]]['fail_count'] 		+= $row[csf('fail_count')];
			$buyer_summary_arr[$row[csf('buyer_name')]]['pass_qty'] 		+= $row[csf('pass_qty')];
			$buyer_summary_arr[$row[csf('buyer_name')]]['re_check_qty'] 	+= $row[csf('re_check_qty')];
			$buyer_summary_arr[$row[csf('buyer_name')]]['fail_qty'] 		+= $row[csf('fail_qty')];
			$buyer_summary_arr[$row[csf('buyer_name')]]['inspection_qnty'] 	+= $row[csf('inspection_qnty')];
		}
		// echo "<pre>";
		// print_r($insp_qty_array);
		// echo "</pre>";

		$buyer_wise_data = array();
		$buyer_active_color = array();
	    foreach($main_array as $job_id=>$job_data)
		{
			foreach ($job_data as $style => $style_data) 
			{
				foreach ($style_data as $buyer => $buyer_data) 
				{
					foreach ($buyer_data as $po_id => $po_data) 
					{
						foreach ($po_data as $country_id => $country_data) 
						{
							foreach ($country_data as $item_id => $item_data) 
							{
								foreach ($item_data as $color_id => $color_data) 
								{
									foreach ($color_data as $insp_date => $insp_date_data) 
									{ 
										foreach ($insp_date_data as $insp_status => $row) 
										{
											$insp_qty = $insp_qty_array[$po_id][$country_id][$item_id][$color_id][$insp_date][$insp_status]['insp_qty'];
											if($insp_qty > 0)
											{
												if($insp_status==3 )
												{				 
													$buyer_summary_arr[$buyer]['fail_count']+= $insp_qty_array[$po_id][$country_id][$item_id][$color_id][$insp_date][$insp_status]['fail_count'];		 

												}
												
												$buyer_active_color[$buyer][$po_id][$country_id][$item_id][$color_id]=$color_id;
											}
										}
									}
								}
							}
						}
						$buyer_wise_data[$buyer]['conduct_po']++;
					}
				}
			}
		}
		// ================================================ CUMULATIVE QTY QUERY ==================================================
		if($txt_datefrom !="" && $txt_dateto !="")
		{
			$date_cond2 = ($date_type==3) ? " and a.inspection_date between '$txt_datefrom' and '$txt_dateto' " : "";
		}

		$cmltv_sql="SELECT a.job_no,a.po_break_down_id as po_id,a.country_id,b.item_id,b.color_id,a.inspection_date,a.inspection_status,
		sum(case when a.inspection_status=1 then b.ins_qty else 0 end) as cmltv_qty
		from  pro_buyer_inspection a, pro_buyer_inspection_breakdown b  
		where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.country_id in($all_country_id) and b.item_id in($all_item_id) and b.color_id in($all_color_id) $prod_po_ids_cond $sql_cond3 $date_cond2
		group by a.job_no,a.po_break_down_id,a.country_id,b.item_id,b.color_id,a.inspection_date,a.inspection_status
		order by a.job_no";
		// echo $cmltv_sql;die();
		$cmltv_sql_res = sql_select($cmltv_sql);
		
		$cmltv_qty_array = array();
		foreach ($cmltv_sql_res as $row) 
		{
			$cmltv_qty_array[$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_id')]][$row[csf('color_id')]][$row[csf('inspection_status')]]['cmltv_qty'] += $row[csf('cmltv_qty')];
		}
		// echo "<pre>";
		// print_r($cmltv_qty_array);
		// echo "</pre>";

		// ======================================= FOR COLOR QNTY ============================================
		$prod_po_ids_cond2 = str_replace("a.po_break_down_id", "b.id", $prod_po_ids_cond);
		$color_sql="SELECT  a.buyer_name,a.style_ref_no as style,a.job_no_prefix_num as job_id,b.id as po_id, c.country_id,c.item_number_id,c.color_number_id, sum(c.order_quantity) as color_qnty
		from  wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c  
		where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and c.country_id in($all_country_id) and c.item_number_id in($all_item_id) and c.color_number_id in($all_color_id) $prod_po_ids_cond2
		group by a.buyer_name,a.style_ref_no,a.job_no_prefix_num,b.id, c.country_id,c.item_number_id,c.color_number_id";
		$color_sql_res = sql_select($color_sql);
		$color_qnty_arr = array();
		$buyer_qnty_arr = array();
		foreach ($color_sql_res as $row) 
		{
			$color_qnty_arr[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]] = $row[csf('color_qnty')];
			if(isset($buyer_active_color[$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]))
			{
				$buyer_qnty_arr[$row[csf('buyer_name')]] += $row[csf('color_qnty')];
			}
		}
		// echo "<pre>";
		// print_r($color_qnty_arr);
		// echo "</pre>";
		
		ob_start();
		?>
		<style type="text/css">    
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
		<div class="main" style="margin: 0 auto; padding: 10px;  width: 100%">
			<table width="2660" cellspacing="0">
		        <tr class="form_caption" style="border:none;">
		            <td colspan="34" align="center" ><strong style="font-size: 19px"><u><? echo $companyArr[$lc_company_id]; ?></u></strong></td>
		        </tr>
		        <tr class="form_caption" style="border:none;">
		            <td colspan="34" align="center"><strong style="font-size: 17px">Buyer Inspection Report</strong></td>
		        </tr>		        
		        <tr class="form_caption" style="border:none;">
		            <td colspan="34" align="center"><strong style="font-size: 15px">Date Range : <? echo change_date_format($txt_date_from); ?> to <? echo change_date_format($txt_date_to); ?></strong></td>
		        </tr>
		    </table>
		    <!-- ========================================== SUMMARY PART ==================================================== -->
		    <div style="width: 1650px">
			    <div class="summary-part" style="padding: 10px 0;width: 1300px;float: left;">
			    	<table class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" id="" width="1300">
			    		<caption style="font-size: 18px;padding: 5px 0">Summary Part</caption>
			    		<thead>
			    			<tr>
			    				<th rowspan="2" width="100">Buyer</th>
			    				<th colspan="8" width="640">PO Wise Inspection Status</th>
			    				<th colspan="7" width="560">Quantity Wise Inspection Status</th>
			    			</tr>
			    			<tr>
			    				<th width="80" align="right">TTL order Qty.</th>
			    				<th width="80" align="right">Conducted</th>
			    				<th width="80" align="right">Pass Count</th>
			    				<th width="80" align="right">Pass %</th>
			    				<th width="80" align="right">Fail Count</th>
			    				<th width="80" align="right">Fail %</th>
			    				<th width="80" align="right">Recheck Count</th>
			    				<th width="80" align="right">Recheck % </th>

			    				<th width="80" align="right">Inspected Quantity</th>
			    				<th width="80" align="right">Pass Quantity</th>
			    				<th width="80" align="right">Pass Quantity %</th>
			    				<th width="80" align="right">Fail Quantity</th>
			    				<th width="80" align="right">Fail Quantity %</th>
			    				<th width="80" align="right">Recheck Qty</th>
			    				<th width="80" align="right">Recheck % </th>
			    			</tr>
			    		</thead>
			    		<tbody>
			    			<?
			    			$gr_order_qty 		= 0;
			    			$gr_condct_qty 		= 0;
			    			$gr_pass_count 		= 0;
			    			$gr_pass_count_prsnt= 0;
			    			$gr_fail_count 		= 0;
			    			$gr_fail_count_prsnt= 0;
			    			$gr_rechk_count 	= 0;
			    			$gr_rechk_count_prsnt= 0;

			    			$gr_insp_qty 		= 0;
			    			$gr_pass_qty 		= 0;
			    			$gr_pass_qty_prsnt 	= 0;
			    			$gr_fail_qty 		= 0;
			    			$gr_fail_qty_prsnt 	= 0;
			    			$gr_rechk_qty 		= 0;
			    			$gr_rechk_qty_prsnt = 0;
							$extra_inspected_quantity = 0;
			    			foreach ($buyer_summary_arr as $buyer_id => $val) 
			    			{
			    				//$conduct_po 	= $buyer_wise_data[$buyer_id]['conduct_po'];
			    				$pass_count 	= $val['pass_count'];
			    				$fail_count 	= $val['fail_count'];
			    				$re_check_count = $val['re_check_count'];
								$conduct_po=$pass_count+$fail_count+$re_check_count;//New Calculation Issue= 7257
								
								$pass_prsnt 	= $pass_count > 0 ? ($pass_count/$conduct_po)*100 : 0;
								$fail_prsnt 	= $fail_count > 0 ? ($fail_count/$conduct_po)*100 : 0;
			    				$rechk_cnt_prsnt= $re_check_count > 0 ? ($re_check_count/$conduct_po)*100 : 0;

			    				$inspection_qnty= $val['inspection_qnty'];
			    				$pass_qty 		= $val['pass_qty'];
			    				$pass_qty_prsnt = $pass_qty > 0 ? ($pass_qty/$inspection_qnty)*100 : 0;
			    				$fail_qty 		= $val['fail_qty'];
			    				$fail_qty_prsnt = $fail_qty > 0 ? ($fail_qty/$inspection_qnty)*100 : 0;
			    				$re_check_qty 	= $val['re_check_qty'];
			    				$rechk_qty_prsnt= $re_check_qty > 0 ? ($re_check_qty/$inspection_qnty)*100 : 0;

								$extra_qty = $inspection_qnty - $buyer_qnty_arr[$buyer_id];
								if($extra_qty > 0){
									$extra_inspected_quantity += $extra_qty; //pass to the graph
								}

				    			$gr_order_qty 		+= $buyer_qnty_arr[$buyer_id];
				    			$gr_condct_qty 		+= $conduct_po;
				    			$gr_pass_count 		+= $pass_count;
				    			$gr_pass_count_prsnt+= $pass_prsnt;
				    			$gr_fail_count 		+= $fail_count;
				    			$gr_fail_count_prsnt+= $fail_prsnt;
				    			$gr_rechk_count 	+= $re_check_count;
				    			$gr_rechk_count_prsnt+= $rechk_cnt_prsnt;

				    			$gr_insp_qty 		+= $inspection_qnty;
				    			$gr_pass_qty 		+= $pass_qty;
				    			$gr_pass_qty_prsnt 	+= $pass_qty_prsnt;
				    			$gr_fail_qty 		+= $fail_qty;
				    			$gr_fail_qty_prsnt 	+= $fail_qty_prsnt;
				    			$gr_rechk_qty 		+= $re_check_qty;
				    			$gr_rechk_qty_prsnt += $rechk_qty_prsnt;

			    				?>
			    				<tr>
			    					<td align="left"><? echo $buyerArr[$buyer_id];?></td>
			    					<td align="right"><? echo $buyer_qnty_arr[$buyer_id];?></td>
			    					<td align="right"><? echo number_format($conduct_po,0);?></td>
			    					<td align="right"><? echo number_format($pass_count,0);?></td>
			    					<td align="right"><? echo number_format($pass_prsnt,2);?>%</td>
			    					<td align="right"><? echo number_format($fail_count,0);?></td>
			    					<td align="right"><? echo number_format($fail_prsnt,2);?>%</td>
			    					<td align="right"><? echo number_format($re_check_count,0);?></td>
			    					<td align="right"><? echo number_format($rechk_cnt_prsnt,2);?>%</td>

			    					<td align="right"><? echo number_format($inspection_qnty,0);?></td>
			    					<td align="right"><? echo number_format($pass_qty,0);?></td>
			    					<td align="right"><? echo number_format($pass_qty_prsnt,2);?>%</td>
			    					<td align="right"><? echo number_format($fail_qty,0);?></td>
			    					<td align="right"><? echo number_format($fail_qty_prsnt,2);?>%</td>
			    					<td align="right"><? echo number_format($re_check_qty,0);?></td>
			    					<td align="right"><? echo number_format($rechk_qty_prsnt,2);?>%</td>
			    				</tr>
			    				<?
			    			}
			    			?>
			    		</tbody>
			    		<tfoot>
			    			<tr>
			    				<th>Total</th>
			    				<th><? echo number_format($gr_order_qty,0); ?></th>
			    				<th><? echo number_format($gr_condct_qty,0); ?></th>
			    				<th><? echo number_format($gr_pass_count,0); ?></th>
			    				<th title="Pass Count/Conducted*100"><? echo number_format((($gr_pass_count/$gr_condct_qty)*100),2); ?>%</th>
			    				<th><? echo number_format($gr_fail_count,0); ?></th>
			    				<th  title="Fail Count/Conducted*100"><? echo number_format((($gr_fail_count/$gr_condct_qty)*100),2); ?>%</th>
			    				<th><? echo number_format($gr_rechk_count,0); ?></th>
			    				<th  title="ReCheck Count/Conducted*100"><? echo number_format(($gr_rechk_count/$gr_condct_qty*100),2); ?>%</th>

			    				<th><? echo number_format($gr_insp_qty,0); ?></th>
			    				<th><? echo number_format($gr_pass_qty,0); ?></th>
			    				<th title="Pass Qty/Inspect Qty*100"><? echo number_format($gr_pass_qty/$gr_insp_qty*100,2); ?>%</th>
			    				<th><? echo number_format($gr_fail_qty,0); ?></th>
			    				<th title="Fail Qty/Inspect Qty*100"><? echo number_format($gr_fail_qty/$gr_insp_qty,2); ?>%</th>
			    				<th><? echo number_format($gr_rechk_qty,0); ?></th>
			    				<th title="ReChk Qty/Inspect Qty*100"><? echo number_format($gr_rechk_qty/$gr_insp_qty*100,2); ?>%</th>
			    			</tr>
			    		</tfoot>
			    	</table>		    			
			    </div>
			    <div class="chart-div" style="float: left;width: 300px;margin: 10px;">
			    	<fieldset>
				    	<div id="container1" style="height:250px; width:300px;margin:10px;">
					   		<script>  hs_chart(1,<? echo $gr_order_qty;?>,<? echo $gr_pass_qty;?>,<? echo ($gr_order_qty-$gr_pass_qty);?>,<? echo $extra_inspected_quantity;?>); </script>
					  	</div>
				  	</fieldset>
			    </div>
			</div>
		    <!-- ================================================ DETAIS PART ================================================ -->
		    <div>
		    	<table class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" id="" width="2580">
		    		<caption style="font-size: 18px;padding: 5px 0">Details Part</caption>
		    		<thead>
		    			<th style="word-wrap: break-word;word-break: break-all;" width="30">Sl.</th>
		    			<th style="word-wrap: break-word;word-break: break-all;" width="100">Working Company</th>
		    			<th style="word-wrap: break-word;word-break: break-all;" width="100">Location</th>
		    			<th style="word-wrap: break-word;word-break: break-all;" width="100">Floor</th>
		    			<th style="word-wrap: break-word;word-break: break-all;" width="100">LC Company</th>
		    			<th style="word-wrap: break-word;word-break: break-all;" width="70">Job No.</th>
		    			<th style="word-wrap: break-word;word-break: break-all;" width="100">Buyer</th>
		    			<th style="word-wrap: break-word;word-break: break-all;" width="100">Buyer Client</th>
		    			<th style="word-wrap: break-word;word-break: break-all;" width="80">Season</th>
		    			<th style="word-wrap: break-word;word-break: break-all;" width="100">Style</th>
		    			<th style="word-wrap: break-word;word-break: break-all;" width="100">Style Desc.</th>
		    			<th style="word-wrap: break-word;word-break: break-all;" width="100">Product Department</th>
		    			<th style="word-wrap: break-word;word-break: break-all;" width="80">Product Category</th>
		    			<th style="word-wrap: break-word;word-break: break-all;" width="80">PO Number</th>
		    			<th style="word-wrap: break-word;word-break: break-all;" width="100">Country</th>
		    			<th style="word-wrap: break-word;word-break: break-all;" width="100">Item Name</th>
		    			<th style="word-wrap: break-word;word-break: break-all;" width="100">Color</th>
		    			<th style="word-wrap: break-word;word-break: break-all;" width="80">Order Qty.</th>
		    			<th style="word-wrap: break-word;word-break: break-all;" width="80">Country Ship Date</th>
		    			<th style="word-wrap: break-word;word-break: break-all;" width="50">Week</th>
		    			<th style="word-wrap: break-word;word-break: break-all;" width="80">Inspection By</th>
		    			<th style="word-wrap: break-word;word-break: break-all;" width="80">Inspection Date</th>
		    			<th style="word-wrap: break-word;word-break: break-all;" width="60">Inspection Qty</th>
		    			<th style="word-wrap: break-word;word-break: break-all;" width="60">Cumulative Isap. Qty</th>
		    			<th style="word-wrap: break-word;word-break: break-all;" width="60">Yet to Insp.</th>
		    			<th style="word-wrap: break-word;word-break: break-all;" width="60">Re-Insp. Count</th>
		    			<th style="word-wrap: break-word;word-break: break-all;" width="60">Fail Count</th>
		    			<th style="word-wrap: break-word;word-break: break-all;" width="60">Insp. Result</th>
		    			<th style="word-wrap: break-word;word-break: break-all;" width="60">Pass Qty</th>
		    			<th style="word-wrap: break-word;word-break: break-all;" width="60">Fail Qty</th>
		    			<th style="word-wrap: break-word;word-break: break-all;" width="60">Re-Check Qty</th>
		    			<th style="word-wrap: break-word;word-break: break-all;" width="80">Cause</th>
		    			<th style="word-wrap: break-word;word-break: break-all;" width="100">Insp. Fail Reason</th>
		    		</thead>
		    	</table>
		    	<div style="width: 2600px; overflow-y: scroll; max-height: 400px" id="scroll_body">
		    		<table cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all" width="2580" id="html_search">
		    			<?
		    			$sl=1;
		    			// $gr_order_qty 		= 0;
		    			$gr_insp_qty 		= 0;
		    			$gr_cumulitive_qty 	= 0;
		    			$gr_yet_to_insp_qty = 0;
		    			$gr_pass_qty 		= 0;
		    			$gr_fail_qty 		= 0;
		    			$gr_recheck_qty 	= 0;
		    			$color_check_array 	= array();
		    			foreach($main_array as $job_id=>$job_data)
		    			{
		    				foreach ($job_data as $style => $style_data) 
		    				{
		    					foreach ($style_data as $buyer => $buyer_data) 
		    					{
		    						foreach ($buyer_data as $po_id => $po_data) 
			    					{
			    						foreach ($po_data as $country_id => $country_data) 
			    						{
			    							foreach ($country_data as $item_id => $item_data) 
			    							{
			    								foreach ($item_data as $color_id => $color_data) 
			    								{
			    									foreach ($color_data as $insp_date => $insp_date_data) 
			    									{ 
			    										foreach ($insp_date_data as $insp_status => $row) 
			    										{
			    											
			    											$insp_qty = $insp_qty_array[$po_id][$country_id][$item_id][$color_id][$insp_date][$insp_status]['insp_qty'];
			    											if($insp_qty > 0)
				    										{
			    											$color_qnty = $color_qnty_arr[$job_id][$style][$buyer][$po_id][$country_id][$item_id][$color_id];
			    											$pass_qty = $insp_qty_array[$po_id][$country_id][$item_id][$color_id][$insp_date][$insp_status]['pass_qty'];
			    											$re_check_qty = $insp_qty_array[$po_id][$country_id][$item_id][$color_id][$insp_date][$insp_status]['re_check_qty'];
			    											$fail_qty = $insp_qty_array[$po_id][$country_id][$item_id][$color_id][$insp_date][$insp_status]['fail_qty'];

			    											$re_check_count = $insp_qty_array[$po_id][$country_id][$item_id][$color_id][$insp_date][$insp_status]['re_check_count'];
			    											$fail_count = $insp_qty_array[$po_id][$country_id][$item_id][$color_id][$insp_date][$insp_status]['fail_count'];

			    											$cmltv_qty = $cmltv_qty_array[$po_id][$country_id][$item_id][$color_id][$insp_status]['cmltv_qty'];
			    											$cmltv_qty = $cmltv_qty;//+$pass_qty;

			    											$yet_to_insp_qty = $color_qnty - $cmltv_qty;
			    											
			    											
				    										$bgcolor = ($sl%2==0) ? "#ffffff" : "#f6faff";
				    										
					    									?>
					    										<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_1nd<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $sl; ?>" >

					    											<td style="word-wrap: break-word;word-break: break-all;" width="30"><? echo $sl; ?></td>
													    			<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $companyArr[$row['working_company']]; ?></td>
													    			<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $locationArr[$row['working_location']]; ?></td>
													    			<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $floorArr[$row['working_floor']]; ?></td>
													    			<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $companyArr[$row['company_name']]; ?></td>
													    			<td style="word-wrap: break-word;word-break: break-all;" width="70"><? echo $job_id; ?></td>
													    			<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $buyerArr[$buyer]; ?></td>
													    			<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $buyerArr[$row['buyer_client']]; ?></td>
													    			<td style="word-wrap: break-word;word-break: break-all;" width="80"><? echo $seasonArr[$row['season']]; ?></td>
													    			<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $style; ?></td>
													    			<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $row['style_description']; ?></td>
													    			<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $product_dept[$row['product_dept']]; ?></td>

													    			<td style="word-wrap: break-word;word-break: break-all;" width="80"><? echo $product_category[$row['product_category']]; ?></td>



													    			<td style="word-wrap: break-word;word-break: break-all;" width="80"><? echo $row['po_number']; ?></td>
													    			<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $countryArr[$country_id]; ?></td>
													    			<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $garments_item[$item_id]; ?></td>
													    			<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $colorArr[$color_id]; ?></td>
													    			<td align="right" style="word-wrap: break-word;word-break: break-all;" width="80"><? echo $color_qnty; ?></td>
													    			<td style="word-wrap: break-word;word-break: break-all;" width="80" align="center"><? echo change_date_format($row['country_ship_date']); ?></td>
													    			<td style="word-wrap: break-word;word-break: break-all;" width="50"><? echo $row['week']; ?></td>
													    			<td style="word-wrap: break-word;word-break: break-all;" width="80"><? echo $inspected_by_arr[$row['inspected_by']]; ?></td>
													    			<td style="word-wrap: break-word;word-break: break-all;" width="80"><? echo change_date_format($insp_date); ?></td>
													    			<td align="right" style="word-wrap: break-word;word-break: break-all;" width="60"><? echo $insp_qty; ?></td>
													    			<td align="right" style="word-wrap: break-word;word-break: break-all;" width="60"><? echo $cmltv_qty; ?></td>
													    			<td align="right" style="word-wrap: break-word;word-break: break-all;" width="60"><? echo $yet_to_insp_qty;  ?></td>
													    			<td style="word-wrap: break-word;word-break: break-all;" align="right" width="60"><? echo $re_check_count; ?></td>
													    			<td align="right" style="word-wrap: break-word;word-break: break-all;" width="60"><? echo $fail_count; ?></td>
													    			<td style="word-wrap: break-word;word-break: break-all;" width="60"><? echo $inspection_status[$insp_status]; ?></td>
													    			<td align="right" style="word-wrap: break-word;word-break: break-all;" width="60"><? echo $pass_qty; ?></td>
													    			<td align="right" style="word-wrap: break-word;word-break: break-all;" width="60"><? echo $fail_qty; ?></td>
													    			<td align="right" style="word-wrap: break-word;word-break: break-all;" width="60"><? echo $re_check_qty; ?></td>
													    			<td style="word-wrap: break-word;word-break: break-all;" width="80"><? echo $inspection_cause[$row['inspection_cause']]; ?></td>
													    			<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $row['ins_reason']; ?></td>					    											
					    										</tr>
					    									<?
					    									
					    									/*if(!in_array($color_id, $color_check_array[$job_id][$style][$buyer][$po_id][$country_id][$item_id][$color_id]))
															{
																$gr_order_qty += $color_qnty;
															}
															$color_check_array[] = $main_array[$job_id][$style][$buyer][$po_id][$country_id][$item_id][$color_id];*/
					    									// $gr_order_qty 		+= $color_qnty;
											    			$gr_insp_qty 		+= $insp_qty;
											    			$gr_cumulitive_qty 	+= $cmltv_qty;
											    			if($insp_status==1)
											    			{
											    				$gr_yet_to_insp_qty += $yet_to_insp_qty;
											    			}
											    			
											    			$gr_pass_qty 		+= $pass_qty;
											    			$gr_fail_qty 		+= $fail_qty;
											    			$gr_recheck_qty 	+= $re_check_qty;

									    					$sl++;
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
		    			// echo "<pre>";
		    			// print_r($color_check_array);
		    			?>
		    		</table>
		    	</div>
		    	<div style="width: 2580px;"">
		    		<table cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" width="2580">
		    			<tfoot>
		    				<tr class="gd-color3">
		    					<td style="word-wrap: break-word;word-break: break-all;" width="30"><? echo $a; ?></td>
				    			<td style="word-wrap: break-word;word-break: break-all;" width="100"></td>
				    			<td style="word-wrap: break-word;word-break: break-all;" width="100"></td>
				    			<td style="word-wrap: break-word;word-break: break-all;" width="100"></td>
				    			<td style="word-wrap: break-word;word-break: break-all;" width="100"></td>
				    			<td style="word-wrap: break-word;word-break: break-all;" width="70"></td>
				    			<td style="word-wrap: break-word;word-break: break-all;" width="100"></td>
				    			<td style="word-wrap: break-word;word-break: break-all;" width="100"></td>
				    			<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
				    			<td style="word-wrap: break-word;word-break: break-all;" width="100"></td>
				    			<td style="word-wrap: break-word;word-break: break-all;" width="100"></td>
				    			<td style="word-wrap: break-word;word-break: break-all;" width="100"></td>
				    			<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
				    			<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
				    			<td style="word-wrap: break-word;word-break: break-all;" width="100"></td>
				    			<td style="word-wrap: break-word;word-break: break-all;" width="100"></td>
				    			<td style="word-wrap: break-word;word-break: break-all;" width="100" align="right">Grand Total </td>
				    			<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($gr_order_qty,0); ?></td>
				    			<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
				    			<td style="word-wrap: break-word;word-break: break-all;" width="50"></td>
				    			<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
				    			<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
				    			<td style="word-wrap: break-word;word-break: break-all;" width="60" align="right"><? echo number_format($gr_insp_qty,0); ?></td>
				    			<td style="word-wrap: break-word;word-break: break-all;" width="60" align="right"><? echo number_format($gr_cumulitive_qty,0); ?></td>
				    			<td style="word-wrap: break-word;word-break: break-all;" width="60" align="right"><? echo number_format($gr_yet_to_insp_qty,0); ?></td>
				    			<td style="word-wrap: break-word;word-break: break-all;" width="60"></td>
				    			<td style="word-wrap: break-word;word-break: break-all;" width="60"></td>
				    			<td style="word-wrap: break-word;word-break: break-all;" width="60"></td>
				    			<td style="word-wrap: break-word;word-break: break-all;" width="60" align="right"><? echo number_format($gr_pass_qty,0); ?></td>
				    			<td style="word-wrap: break-word;word-break: break-all;" width="60" align="right"><? echo number_format($gr_fail_qty,0); ?></td>
				    			<td style="word-wrap: break-word;word-break: break-all;" width="60" align="right"><? echo number_format($gr_recheck_qty,0); ?></td>
				    			<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
				    			<td style="word-wrap: break-word;word-break: break-all;" width="100"></td>
							</tr>
		    			</tfoot>
		    		</table>
		    </div>
	    </div>
	   <?
	}
	else
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
			  <strong>Oh Snap!</strong> Change a few things up and try submitting again.
			</div>
		</div>
		<?
		die();
		
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