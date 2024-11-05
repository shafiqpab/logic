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
	order by location_name","id,location_name", 1, "-- All --", $selected, "load_drop_down( 'requires/color_wise_sewing_output_report_controller', document.getElementById('cbo_working_company_id').value+'_'+this.value, 'load_drop_down_floor', 'floor_td' );",0 );
	exit();    	 
}

if ($action=="load_drop_down_floor")
{
	$ex_data = explode("_", $data);
	echo create_drop_down( "cbo_floor_id", 120, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and production_process=5 
	and company_id in($ex_data[0]) and location_id in($ex_data[1]) order by floor_name","id,floor_name", 1, "-- All --", $selected, "",0 );     	 	
	exit();    	 
}

if ($action == "eval_multi_select") {
    echo "set_multiselect('cbo_line_id','0','0','','0');\n";
    // echo "setTimeout[($('#floor_td a').attr('onclick','disappear_list(cbo_floor,0);getCompanyId();') ,3000)];\n";
    exit();
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All --", $selected, "" );     	 
	exit();
}

if($action=="search_by_action")
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
				$('#txt_selected_id').val( id );
				$('#txt_selected').val( name ); 
				$('#txt_selected_no').val( num );
		}
    </script>
    <?
	$buyer=str_replace("'","",$buyer);
	$w_company=str_replace("'","",$w_company);
	$lc_company=str_replace("'","",$lc_company);
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
	$sql = "SELECT a.id,a.po_number,a.job_no_mst,b.style_ref_no,b.job_no_prefix_num,$select_date as year from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and b.company_name=$lc_company $buyer_cond $job_year_cond  $style_cond and a.status_active in(1,2,3) and b.status_active=1"; 
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

if($action=="color_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
    <script>	
    	// var txt_order_id = $("#txt_order_id").val(); alert(txt_order_id);

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
			// alert(strCon);
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
	$txt_search_by=str_replace("'","",$txt_search_by);
	$order_id=str_replace("'","",$txt_order_id);

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
     			$po_ids_cond.=" and ( a.id in ($imp_ids) ";
     		}
     		else
     		{
     			$po_ids_cond.=" or a.id in ($imp_ids) ";
     		}
     	}
     	 $po_ids_cond.=" )";
    }
    else
    {
     	$po_ids_cond= " and a.id in($order_id) ";
    }
	
	
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

	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
    $arr=array(4=>$color_arr);
    // print_r($arr);

	$sql = "SELECT a.id,a.po_number,b.style_ref_no,b.job_no_prefix_num,$select_date as year,c.color_number_id from wo_po_break_down a, wo_po_details_master b,wo_po_color_size_breakdown c  where a.job_no_mst=b.job_no and a.id=c.po_break_down_id and b.job_no=c.job_no_mst $po_ids_cond and a.status_active in(1,2,3) and b.status_active=1 and c.status_active=1 group by a.id,a.po_number,b.style_ref_no,b.job_no_prefix_num,$select_date,c.color_number_id order by a.id,c.color_number_id"; 
	//echo $sql; die;
	echo create_list_view("list_view", "Order NO,Job No,Year,Style Ref No,Color","150,50,70,150,100","600","310",0, $sql , "js_set_value", "id,color_number_id", "", 1, "0,0,0,0,color_number_id", $arr, "po_number,job_no_prefix_num,year,style_ref_no,color_number_id", "","setFilterGrid('list_view',-1)","0,0,0,0,0","",1) ;

	// echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Style Ref. No, Po No, Cut No.", "120,100,100,100,140,140","740","290",0, $sql , "js_set_value", "job_no,style_ref_no,po_number,cut_no","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,job_no,style_ref_no,po_number,cut_no","",'','0,0,0,0,0,0','',1) ;

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
	$companyArr 	= return_library_array("select id,company_name from lib_company","id","company_name"); 
	$countryArr 	= return_library_array("select id,country_name from lib_country","id","country_name"); 
	$buyerArr 		= return_library_array("select id,short_name from lib_buyer","id","short_name"); 
	$sizeArr 		= return_library_array("select id,size_name from lib_size","id","size_name"); 
	$colorArr 		= return_library_array("select id,color_name from lib_color","id","color_name"); 
	$locationArr	= return_library_array("select id,location_name from lib_location","id","location_name"); 
	$floorArr 		= return_library_array("select id,floor_name from lib_prod_floor","id","floor_name"); 
	$lineArr 		= return_library_array("select id,line_name from lib_sewing_line","id","line_name"); 
	$prod_reso_arr  = return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	// ================================= GETTING FORM DATA ====================================
	$working_company_id =str_replace("'","",$cbo_working_company_id);
	$location_id 		=str_replace("'","",$cbo_location_id);
	$floor_id 			=str_replace("'","",$cbo_floor_id);
	$floor_group 		=str_replace("'","",$cbo_floor_group);
	$buyer_id 			=str_replace("'","",$cbo_buyer_id);
	$job_no 			=str_replace("'","",$txt_job_no);
	$int_ref 			=str_replace("'","",$txt_int_ref);
	$job_year 			=str_replace("'","",$cbo_job_year);
	$txt_date 			=str_replace("'","",$txt_date);
	$shipping_status 	=str_replace("'","",$cbo_shipping_status);

	if($type==1)
	{
		//******************************************* MAKE QUERY CONDITION ************************************************
		$sql_cond = "";
		$sql_cond .= ($working_company_id==0) 	? "" : " and d.serving_company in($working_company_id)";
		$sql_cond .= ($location_id==0) 			? "" : " and d.location in($location_id)";
		$sql_cond .= ($floor_id==0) 			? "" : " and d.floor_id in($floor_id)";
		$sql_cond .= ($buyer_id==0) 			? "" : " and a.buyer_name=$buyer_id";
		$sql_cond .= ($job_no=="") 				? "" : " and a.job_no_prefix_num=$job_no";
		$sql_cond .= ($int_ref=="") 			? "" : " and b.grouping='$int_ref'";
		// $sql_cond .= ($order_id=="") 		? "" : " and b.id in($order_id)";
		$sql_cond .= ($shipping_status==0) 		? "" : " and b.shiping_status in($shipping_status)";

		if($floor_group)
		{
			$group_cond="";
			$group_sql = sql_select("SELECT a.id from lib_prod_floor a where a.status_active=1 and a.group_name='$floor_group' order by a.id");
			
			foreach ($group_sql as $value) 
			{
				if($group_cond=="")
				{
					$group_cond = $value[csf('id')];
				}
				else
				{
					$group_cond .= ",".$value[csf('id')];
				}
			}
			$sql_cond.=" and d.floor_id in($group_cond)";
		}
		else if($floor_id !=0)
		{
			$sql_cond.=" and d.floor_id in($floor_id)";
		}
			
		if($txt_date!="")
		{
			$txt_datefrom=change_date_format($txt_date_from,'','',-1);
			$txt_dateto=change_date_format($txt_date_to,'','',-1);
			
			// $sql_cond .= " and d.production_date='$txt_date'";
		}

		if($job_year>0)
		{
			if($db_type==0)
			{
				$sql_cond .=" and year(a.insert_date)='$job_year'";
			}
			else
			{
				$sql_cond .=" and to_char(a.insert_date,'YYYY')='$job_year'";
			}	
		}

		// echo $sql_cond;die();

		$order_id_arr  = return_library_array( "SELECT po_break_down_id, po_break_down_id from pro_garments_production_mst where status_active=1 and production_date='$txt_date'",'po_break_down_id','po_break_down_id');
		// print_r($order_id_arr);

		$order_id_cond = where_con_using_array($order_id_arr,0,"b.id");
		
		// ================================================ MAIN QUERY ==================================================
		$sql="SELECT a.id as job_id, a.buyer_name,a.job_no,a.style_ref_no as style,b.shiping_status,b.grouping,c.country_ship_date,c.item_number_id as item_id,c.color_number_id as color_id,d.floor_id,d.sewing_line,d.prod_reso_allo,c.order_quantity,c.order_total,
		(case when d.production_type=5 and d.production_date='$txt_date' then e.production_qnty else 0 end) as today_prod,
		(case when d.production_type=5 and d.production_date<='$txt_date' then e.production_qnty else 0 end) as total_prod,
		(case when d.production_type=4 and d.production_date<='$txt_date' then e.production_qnty else 0 end) as total_in,
		(case when d.production_type=5 and d.production_date<='$txt_date' then e.reject_qty else 0 end) as total_rej
		from  wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, pro_garments_production_mst d,pro_garments_production_dtls e  
		where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and b.id=d.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id $sql_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 and d.sewing_line<>0 and d.production_type in(4,5) and e.production_qnty>0 $order_id_cond
		order by d.floor_id,d.sewing_line";
		// echo $sql;die();
		$sql_res = sql_select($sql);
		if (count($sql_res) < 1) 
		{	
			?>
			
			<div style="margin:20px auto; width: 90%">
				<div class="alert alert-danger">
				<strong>Oh Snap!</strong> Change a few things up and try submitting again.
				</div>
			</div>
			<?
			die();
		}

		$data_array = array();
		$chk_arr = array();
		$order_qty_arr = array();
		foreach ($sql_res as $row) 
		{
			if($row[csf("prod_reso_allo")]==1)
			{
				$line_resource_mst_arr=explode(",",$prod_reso_arr[$row[csf('sewing_line')]]);
				$line_name="";
				foreach($line_resource_mst_arr as $resource_id)
				{
					$line_name .= ($line_name == "") ? $lineArr[$resource_id] : ",".$lineArr[$resource_id];
				}
			}
			else
			{
				$line_name=$lineArr[$row[csf('sewing_line')]];
			}

			$data_array[$row[csf('floor_id')]][$line_name][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('color_id')]]['ship_date'] = $row[csf('country_ship_date')];
			$data_array[$row[csf('floor_id')]][$line_name][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('color_id')]]['buyer_name'] = $row[csf('buyer_name')];
			$data_array[$row[csf('floor_id')]][$line_name][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('color_id')]]['style'] = $row[csf('style')];
			$data_array[$row[csf('floor_id')]][$line_name][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('color_id')]]['grouping'] = $row[csf('grouping')];		
			$data_array[$row[csf('floor_id')]][$line_name][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('color_id')]]['today_prod'] += $row[csf('today_prod')];		
			$data_array[$row[csf('floor_id')]][$line_name][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('color_id')]]['total_prod'] += $row[csf('total_prod')];		
			$data_array[$row[csf('floor_id')]][$line_name][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('color_id')]]['total_rej'] += $row[csf('total_rej')];
			$data_array[$row[csf('floor_id')]][$line_name][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('color_id')]]['total_in'] += $row[csf('total_in')];

			if($chk_arr[$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('color_id')]]=="")		
			{
				$order_qty_arr[$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('color_id')]]['qty'] += $row[csf('order_quantity')];
				$order_qty_arr[$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('color_id')]]['amt'] += $row[csf('order_total')];
				$chk_arr[$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('color_id')]] = $row[csf('color_id')];
			}
			$job_id_array[$row[csf('job_id')]] = $row[csf('job_id')];
			
		}
		// echo "<pre>";print_r($data_array);die();

		$job_id_cond = where_con_using_array($job_id_array,0,'job_id');
		$costing_per_arr = return_library_array("SELECT job_no, costing_per from wo_pre_cost_mst where status_active=1 $job_id_cond","job_no","costing_per"); 
		$cm_cost_arr = return_library_array("SELECT job_no, cm_cost from wo_pre_cost_dtls where status_active=1 $job_id_cond","job_no","cm_cost"); 
		// echo "<pre>"; print_r($cm_cost_arr);die();

		ob_start();
		?>
		
		<div class="main" style="margin: 0 auto; padding: 10px;  width: 1280px;">
			<table width="100%" cellspacing="0">
				<tr class="form_caption" style="border:none;">
					<td colspan="9" align="center"><font size="2"><strong>Color Wise Sewing Output Report</strong></font></td>
				</tr>
				<tr class="form_caption" style="border:none;">
					<td colspan="9" align="center" ><font size="3"><strong><? echo $companyArr[$working_company_id]; ?></strong></font></td>
				</tr>
				<tr class="form_caption" style="border:none;">
					<td colspan="9" align="center" ><font size="3"><strong>Date : <?=change_date_format($txt_date);?></strong></font></td>
				</tr>
			</table>
			<div>
				<table class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" id="" width="1260">
					<thead>
						<th width="30">Sl.</th>
						<th width="100">Floor Name</th>
						<th width="80">Line Name</th>
						<th width="100">Buyer Name</th>
						<th width="70">Job No.</th>
						<th width="100">Int. Ref.</th>
						<th width="100">Style Ref.</th>
						<th width="100">Gmts Item</th>
						<th width="60">Ship Date</th>
						<th width="100">Color Name</th>
						<th width="60">Order Qty(pcs)</th>
						<th width="60">Today Prod</th>
						<th width="60">Total Prod</th>
						<th width="60">Total Reject</th>
						<th width="60">Sew WIP</th>
						<th width="60">Day CM</th>
						<th width="60">Day FOB</th>
					</thead>
				</table>
				<div style="width: 1280px; overflow-y: scroll; max-height: 400px" id="scroll_body">
					<table cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all" width="1260" id="html_search">
						<?
						$sl=1;
						$grnd_order_qty 		= 0;
						$grnd_today_prod_qty 	= 0;
						$grnd_total_prod_qty 	= 0;
						$grnd_total_prod_qty 	= 0;
						$grnd_total_rej_qty 	= 0;
						$grnd_total_wip_qty 	= 0;
						$grnd_total_cm_qty 		= 0;
						$grnd_total_fob_qty 	= 0;
						foreach($data_array as $flr_id=>$flr_data)
						{
							
							$flr_order_qty 		= 0;
							$flr_today_prod_qty = 0;
							$flr_total_prod_qty = 0;
							$flr_total_prod_qty = 0;
							$flr_total_rej_qty 	= 0;
							$flr_total_wip_qty 	= 0;
							$flr_total_cm_qty 	= 0;
							$flr_total_fob_qty 	= 0;
							foreach ($flr_data as $line_name => $line_data) 
							{
								foreach ($line_data as $job_no => $job_data) 
								{
									foreach ($job_data as $item_id => $item_data) 
									{
										foreach ($item_data as $color_id => $row) 
										{
											if($row['today_prod']>0)
											{
												$order_qty = $order_qty_arr[$job_no][$item_id][$color_id]['qty'];		
												$order_amt = $order_qty_arr[$job_no][$item_id][$color_id]['amt'];	
												$fob_val = 	($order_amt/$order_qty)*$row['total_prod'];
												$wip = $row['total_in'] - $row['total_prod'];	 

												$costing_per=$costing_per_arr[$job_no];
												$costing_cm=$cm_cost_arr[$job_no];

												if($costing_per==1) $cm_cost_pcs=$costing_cm/12;
												else if($costing_per==3) $cm_cost_pcs=$costing_cm/(12*2);
												else if($costing_per==4) $cm_cost_pcs=$costing_cm/(12*3);
												else if($costing_per==5) $cm_cost_pcs=$costing_cm/(12*4);
												else $cm_cost_pcs=1*$costing_cm;

												$day_cm = $cm_cost_pcs*$row['today_prod'];
												// echo $costing_per."*".$costing_cm."*".$cm_cost_pcs."*".$row['today_prod']."<br>";

												$bgcolor = ($sl%2==0) ? "#ffffff" : "#f6faff";
												?>
												<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_1nd<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $sl; ?>" >
													<td width="30"><?=$sl;?></td>
													<td width="100"><p><?=$floorArr[$flr_id];?></p></td>
													<td width="80"><p><?=$line_name;?></p></td>
													<td width="100"><p><?=$buyerArr[$row['buyer_name']];?></p></td>
													<td width="70"><p><?=$job_no;?></p></td>
													<td width="100"><p><?=$row['grouping'];?></p></td>
													<td width="100"><p><?=$row['style'];?></p></td>
													<td width="100"><p><?=$garments_item[$item_id];?></p></td>
													<td width="60" align="center"><p><?=change_date_format($row['ship_date']);?></p></td>
													<td width="100"><p><?=$colorArr[$color_id]?></p></td>
													<td width="60" align="right"><?=number_format($order_qty,0);?></td>
													<td width="60" align="right"><?=number_format($row['today_prod'],0);?></td>
													<td width="60" align="right"><?=number_format($row['total_prod'],0);?></td>
													<td width="60" align="right"><?=number_format($row['total_rej'],0);?></td>
													<td width="60" align="right"><?=number_format($wip,0);?></td>
													<td width="60" align="right"><?=number_format($day_cm,2);?></td>
													<td width="60" align="right"><?=number_format($fob_val,2);?></td>
												</tr>
												<?    									
												$grnd_order_qty 		+= $order_qty;
												$grnd_today_prod_qty 	+= $row['today_prod'];
												$grnd_total_prod_qty 	+= $row['total_prod'];
												$grnd_total_rej_qty 	+= $row['total_rej'];
												$grnd_total_wip_qty 	+= $wip;
												$grnd_total_cm_qty 		+= $day_cm;
												$grnd_total_fob_qty 	+= $fob_val;
												//==========================================
												
												$flr_order_qty 		+= $order_qty;
												$flr_today_prod_qty += $row['today_prod'];
												$flr_total_prod_qty += $row['total_prod'];
												$flr_total_rej_qty += $row['total_rej'];
												$flr_total_wip_qty 	+= $wip;
												$flr_total_cm_qty 	+= $day_cm;
												$flr_total_fob_qty 	+= $fob_val;
												$sl++;
											}
										}
									}
								}
							}
							?>
							<tr style="text-align: right;font-weight: bold;background: #cddcdc;">
								<td width="30"></td>
								<td width="100"></td>
								<td width="80"></td>
								<td width="100"></td>
								<td width="70"></td>
								<td width="100"></td>
								<td width="100"></td>
								<td width="100"></td>
								<td width="60"></td>
								<td width="100">Floor Total</td>
								<td width="60"><?=number_format($flr_order_qty,0);?></td>
								<td width="60"><?=number_format($flr_today_prod_qty,0);?></td>
								<td width="60"><?=number_format($flr_total_prod_qty,0);?></td>
								<td width="60"><?=number_format($$flr_total_rej_qty,0);?></td>
								<td width="60"><?=number_format($flr_total_wip_qty,0);?></td>
								<td width="60"><?=number_format($flr_total_cm_qty,2);?></td>
								<td width="60"><?=number_format($flr_total_fob_qty,2);?></td>
							</tr>
							<?
						}
						?>
					</table>
				</div>
				<table cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" width="1260">
					<tfoot>
						<tr class="gd-color3">
							<th width="30"></th>
							<th width="100"></th>
							<th width="80"></th>
							<th width="100"></th>
							<th width="70"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="60"></th>
							<th width="100">Grand Total</th>
							<th width="60"><?=number_format($grnd_order_qty,0);?></th>
							<th width="60"><?=number_format($grnd_today_prod_qty,0);?></th>
							<th width="60"><?=number_format($grnd_total_prod_qty,0);?></th>
							<th width="60"><?=number_format($$grnd_total_rej_qty,0);?></th>
							<th width="60"><?=number_format($grnd_total_wip_qty,0);?></th>
							<th width="60"><?=number_format($grnd_total_cm_qty,2);?></th>
							<th width="60"><?=number_format($grnd_total_fob_qty,2);?></th>
						</tr>
					</tfoot>
				</table>
		</div>
		<?
	}
	else if($type==2)
	{
		//******************************************* MAKE QUERY CONDITION ************************************************
		$sql_cond = "";
		$sql_cond .= ($working_company_id==0) 	? "" : " and d.serving_company in($working_company_id)";
		$sql_cond .= ($location_id==0) 			? "" : " and d.location in($location_id)";
		$sql_cond .= ($floor_id==0) 			? "" : " and d.floor_id in($floor_id)";
		$sql_cond .= ($buyer_id==0) 			? "" : " and a.buyer_name=$buyer_id";
		$sql_cond .= ($job_no=="") 				? "" : " and a.job_no_prefix_num=$job_no";
		$sql_cond .= ($int_ref=="") 			? "" : " and b.grouping='$int_ref'";
		// $sql_cond .= ($order_id=="") 		? "" : " and b.id in($order_id)";
		$sql_cond .= ($shipping_status==0) 		? "" : " and b.shiping_status in($shipping_status)";

		if($floor_group)
		{
			$group_cond="";
			$group_sql = sql_select("SELECT a.id from lib_prod_floor a where a.status_active=1 and a.group_name='$floor_group' order by a.id");
			
			foreach ($group_sql as $value) 
			{
				if($group_cond=="")
				{
					$group_cond = $value[csf('id')];
				}
				else
				{
					$group_cond .= ",".$value[csf('id')];
				}
			}
			$sql_cond.=" and d.floor_id in($group_cond)";
		}
		else if($floor_id !=0)
		{
			$sql_cond.=" and d.floor_id in($floor_id)";
		}
			
		if($txt_date!="")
		{
			$txt_datefrom=change_date_format($txt_date_from,'','',-1);
			$txt_dateto=change_date_format($txt_date_to,'','',-1);
			
			// $sql_cond .= " and d.production_date='$txt_date'";
		}

		if($job_year>0)
		{
			if($db_type==0)
			{
				$sql_cond .=" and year(a.insert_date)='$job_year'";
			}
			else
			{
				$sql_cond .=" and to_char(a.insert_date,'YYYY')='$job_year'";
			}	
		}

		// echo $sql_cond;die();

		$order_id_arr  = return_library_array( "SELECT po_break_down_id, po_break_down_id from pro_garments_production_mst where status_active=1 and production_date='$txt_date'",'po_break_down_id','po_break_down_id');
		// print_r($order_id_arr);

		$order_id_cond = where_con_using_array($order_id_arr,0,"b.id");

		// ================================================ MAIN QUERY ==================================================
		$sql="SELECT a.id as JOB_ID, a.buyer_name as BUYER_NAME, a.job_no as JOB_NO, a.style_ref_no as STYLE, b.shiping_status as SHIPING_STATUS, b.grouping as GROUPING, c.country_ship_date as COUNTRY_SHIP_DATE, c.item_number_id as ITEM_ID, c.color_number_id as COLOR_ID, c.size_number_id as SIZE_ID, d.floor_id as FLOOR_ID, d.sewing_line as SEWING_LINE, d.prod_reso_allo as PROD_RESO_ALLO, c.order_quantity as ORDER_QUANTITY, c.order_total as ORDER_TOTAL, e.production_qnty as TODAY_PROD
		from  wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, pro_garments_production_mst d,pro_garments_production_dtls e  
		where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and b.id=d.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id $sql_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 and d.sewing_line<>0 and e.production_qnty>0 $order_id_cond and d.production_type=5 and d.production_date='$txt_date'
		order by d.floor_id,d.sewing_line,c.id";
		// echo $sql;die();
		$sql_res = sql_select($sql);
		if (count($sql_res) < 1) 
		{	
			?>
			
			<div style="margin:20px auto; width: 90%">
				<div class="alert alert-danger">
				<strong>Oh Snap!</strong> Change a few things up and try submitting again.
				</div>
			</div>
			<?
			die();
		}

		$data_array = array();
		$chk_arr = array();
		$order_qty_arr = array();
		foreach ($sql_res as $row) 
		{
			if($row[csf("prod_reso_allo")]==1)
			{
				$line_resource_mst_arr=explode(",",$prod_reso_arr[$row['SEWING_LINE']]);
				$line_name="";
				foreach($line_resource_mst_arr as $resource_id)
				{
					$line_name .= ($line_name == "") ? $lineArr[$resource_id] : ",".$lineArr[$resource_id];
				}
			}
			else
			{
				$line_name=$lineArr[$row['SEWING_LINE']];
			}

			$data_array[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_ID']][$row['COLOR_ID']][$row['SIZE_ID']]['ship_date'] = $row['COUNTRY_SHIP_DATE'];
			$data_array[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_ID']][$row['COLOR_ID']][$row['SIZE_ID']]['buyer_name'] = $row['BUYER_NAME'];
			$data_array[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_ID']][$row['COLOR_ID']][$row['SIZE_ID']]['style'] = $row['STYLE'];
			$data_array[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_ID']][$row['COLOR_ID']][$row['SIZE_ID']]['grouping'] = $row['GROUPING'];		
			$data_array[$row['FLOOR_ID']][$line_name][$row['JOB_NO']][$row['ITEM_ID']][$row['COLOR_ID']][$row['SIZE_ID']]['today_prod'] += $row['TODAY_PROD'];					
		}
		// echo "<pre>";print_r($data_array);die();

		ob_start();
		?>

		<div class="main" style="margin: 0 auto; padding: 10px;  width: 1000px;">
			<table width="100%" cellspacing="0">
				<tr class="form_caption" style="border:none;">
					<td colspan="9" align="center"><font size="2"><strong>Daily Size wise sewing production Report</strong></font></td>
				</tr>
				<tr class="form_caption" style="border:none;">
					<td colspan="9" align="center" ><font size="3"><strong><? echo $companyArr[$working_company_id]; ?></strong></font></td>
				</tr>
				<tr class="form_caption" style="border:none;">
					<td colspan="9" align="center" ><font size="3"><strong>Date : <?=change_date_format($txt_date);?></strong></font></td>
				</tr>
			</table>
			<div>
				<table class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" id="" width="1000">
					<thead>
						<th width="30">Sl.</th>
						<th width="100">Floor Name</th>
						<th width="80">Line Name</th>
						<th width="120">Buyer Name</th>
						<th width="120">Int. Ref.</th>
						<th width="120">Style Ref.</th>
						<th width="120">Garments Item</th>
						<th width="120">Gmt. Color</th>
						<th width="100">Size</th>
						<th >Production Qty</th>
					</thead>
				</table>
				<div style="width: 1020px; overflow-y: scroll; max-height: 400px" id="scroll_body">
					<table cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all" width="1000" id="html_search">
						<?
						$sl=1;
						$grnd_today_prod_qty 	= 0;
						foreach($data_array as $flr_id=>$flr_data)
						{
							foreach ($flr_data as $line_name => $line_data) 
							{
								foreach ($line_data as $job_no => $job_data) 
								{
									foreach ($job_data as $item_id => $item_data) 
									{
										foreach ($item_data as $color_id => $color_data) 
										{
											$clr_today_prod_qty = 0;
											foreach($color_data as $size_id => $row) 
											{												
												if($row['today_prod']>0)
												{
													$bgcolor = ($sl%2==0) ? "#ffffff" : "#f6faff";
													?>
													<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_1nd<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $sl; ?>" >
														<td width="30"><?=$sl;?></td>
														<td width="100"><p><?=$floorArr[$flr_id];?></p></td>
														<td width="80"><p><?=$line_name;?></p></td>
														<td width="120"><p><?=$buyerArr[$row['buyer_name']];?></p></td>
														<td width="120"><p><?=$row['grouping'];?></p></td>
														<td width="120"><p><?=$row['style'];?></p></td>
														<td width="120"><p><?=$garments_item[$item_id];?></p></td>
														<td width="120"><p><?=$colorArr[$color_id]?></p></td>
														<td width="100"><p><?=$sizeArr[$size_id]?></p></td>
														<td  align="right"><?=number_format($row['today_prod'],0);?></td>
													</tr>
													<?    									
													$grnd_today_prod_qty 	+= $row['today_prod'];
													//==========================================

													$clr_today_prod_qty += $row['today_prod'];
													$sl++;
												}
											}
											?>
											<tr style="text-align: right;font-weight: bold;background: #cddcdc;">
												<td width="30"></td>
												<td width="100"></td>
												<td width="80"></td>
												<td width="120"></td>
												<td width="120"></td>
												<td width="120"></td>
												<td width="120"></td>
												<td width="120"></td>
												<td width="100">Color Wise Total </td>
												<td ><?=number_format($clr_today_prod_qty,0);?></td>
											</tr>
											<?
										}
									}
								}
							}
						}
						?>
					</table>
				</div>
				<table cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" width="1000">
					<tfoot>
						<tr class="gd-color3">
							<th width="30"></th>
							<th width="100"></th>
							<th width="80"></th>
							<th width="120"></th>
							<th width="120"></th>
							<th width="120"></th>
							<th width="120"></th>
							<th width="120"></th>
							<th width="100">Grand Total</th>
							<th ><?=number_format($grnd_today_prod_qty,0);?></th>
						</tr>
					</tfoot>
				</table>
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

?>