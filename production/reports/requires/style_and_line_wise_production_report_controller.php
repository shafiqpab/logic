<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
if (!function_exists('pre'))
{
	function pre($arr){
		echo "<pre>";
		print_r($arr);
		echo "</pre>";
	}
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 120, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id in($data)
	order by location_name","id,location_name", 0, "-- Select Location --", $selected, "load_drop_down( 'requires/style_and_line_wise_production_report_controller', document.getElementById('cbo_working_company_id').value+'_'+this.value, 'load_drop_down_floor', 'floor_td' );",0 );
	exit();
}

if($action=="print_button_variable_setting")
{
    extract($_REQUEST);
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=7 and report_id=301 and is_deleted=0 and status_active=1");
	$print_report_format_arr=explode(",",$print_report_format);
	foreach($print_report_format_arr as $id)
	{
		if($id==108){echo "$('#search1').show();\n";}
		if($id==195){echo "$('#search2').show();\n";}
		if($id==242){echo "$('#search3').show();\n";}

	}
	exit();
}


if ($action=="load_drop_down_floor")
{
	$ex_data = explode("_", $data);
	echo create_drop_down( "cbo_floor_id", 120, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and production_process=5
	and company_id in($ex_data[0]) and location_id in($ex_data[1]) order by floor_name","id,floor_name", 0, "-- Select Floor --", $selected, "",0 );
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
	$sql = "SELECT a.id,a.po_number,a.job_no_mst,b.style_ref_no,b.job_no_prefix_num,a.grouping,$select_date as year from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and b.company_name=$lc_company $buyer_cond $job_year_cond  $style_cond and a.status_active in(1,2,3) and b.status_active=1";
	//echo $sql; die;
	echo create_list_view("list_view", "Order NO,Job No,Int.Ref,Year,Style Ref No","150,50,70,70,150",490,310,0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "po_number,job_no_prefix_num,grouping,year,style_ref_no", "","setFilterGrid('list_view',-1)","0","",1) ;
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
	$colorArr 		= return_library_array("select id,color_name from lib_color","id","color_name");
	$locationArr	= return_library_array("select id,location_name from lib_location","id","location_name");
	$floorArr 		= return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
	$lineArr 		= return_library_array("select id,line_name from lib_sewing_line","id","line_name");
	$prod_reso_arr  = return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	// ================================= GETTING FORM DATA ====================================
	$working_company_id =str_replace("'","",$cbo_working_company_id);
	$location_id 		=str_replace("'","",$cbo_location_id);
	$floor_id 			=str_replace("'","",$cbo_floor_id);
	$line_id 			=str_replace("'","",$cbo_line_id);
	$lc_company_id 		=str_replace("'","",$cbo_lc_company_id);
	$buyer_id 			=str_replace("'","",$cbo_buyer_id);
	$search_by 			=str_replace("'","",$txt_search_by);
	$job_year 			=str_replace("'","",$cbo_job_year);
	$color_id 			=str_replace("'","",$color_id);
	$shipping_status 	=str_replace("'","",$cbo_shipping_status);
	$order_id 			=str_replace("'","",$txt_order_id);
	$report_title 		=str_replace("'","",$report_title);
    $today_date 		=date("Y-m-d");
	$txt_date_from 		="".str_replace("'","",$txt_date_from)."";
	$txt_date_to 		=str_replace("'","",$txt_date_to);
	$txt_internal_ref 	=str_replace("'","",$txt_internal_ref);

	//******************************************* MAKE QUERY CONDITION ************************************************
	$sql_cond = "";
	$sql_cond .= ($working_company_id=="") 	? "" : " and d.serving_company in($working_company_id)";
	$sql_cond .= ($location_id=="") 		? "" : " and d.location in($location_id)";
	$sql_cond .= ($floor_id=="") 			? "" : " and d.floor_id in($floor_id)";
	$sql_cond .= ($line_id=="") 			? "" : " and d.sewing_line in($line_id)";
	$sql_cond .= ($lc_company_id==0) 		? "" : " and a.company_name=$lc_company_id";
	$sql_cond .= ($buyer_id==0) 			? "" : " and a.buyer_name=$buyer_id";
	$sql_cond .= ($color_id=="") 			? "" : " and c.color_number_id in($color_id)";
	$sql_cond .= ($txt_internal_ref=="") 	? "" : " and b.grouping = '$txt_internal_ref'";

	// $sql_cond .= ($order_id=="") 			? "" : " and b.id in($order_id)";
	$sql_cond .= ($shipping_status==0) 		? "" : " and b.shiping_status in($shipping_status)";
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
		$sql_cond2 .= " and d.production_date between '$txt_datefrom' and '$txt_dateto'";
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
		$sql="SELECT  a.buyer_name,a.client_id as buyer_client,a.style_ref_no as style,a.job_no as job_id,b.id as po_id,b.grouping,b.po_number,b.shiping_status, c.country_id,c.country_ship_date,c.item_number_id,c.color_number_id,d.floor_id,d.sewing_line,d.prod_reso_allo
		from  wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, pro_garments_production_mst d,pro_garments_production_dtls e
		where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and b.id=d.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id $sql_cond $sql_cond2 $po_ids_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 and d.sewing_line<>0
		order by a.job_no,b.id,c.color_number_id";
		// echo $sql;
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
				  <strong>Data not found.</strong>
				</div>
			</div>
			<?
			die();
		}

		$main_array = array();
		$poId_arr = array();
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

				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['country_ship_date'] = $row[csf('country_ship_date')];
				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['po_number'] = $row[csf('po_number')];
				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['grouping'] = $row[csf('grouping')];
				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['shiping_status'] = $row[csf('shiping_status')];
				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['sewing_line'] = $row[csf('sewing_line')];
				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['prod_reso_allo'] = $row[csf('prod_reso_allo')];
				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['buyer_client'] = $row[csf('buyer_client')];
			}
			else
			{
				$line_name=$lineArr[$row[csf('sewing_line')]];
				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['country_ship_date'] = $row[csf('country_ship_date')];
				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['po_number'] = $row[csf('po_number')];
				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['grouping'] = $row[csf('grouping')];
				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['shiping_status'] = $row[csf('shiping_status')];
				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['sewing_line'] = $row[csf('sewing_line')];
				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['prod_reso_allo'] = $row[csf('prod_reso_allo')];
				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['buyer_client'] = $row[csf('buyer_client')];
			}
			$poId_arr[$row[csf('po_id')]] = $row[csf('po_id')];
		}
		$all_po_id = implode(",", $poId_arr);
		if(count($poId_arr)>999 && $db_type==2)
	    {
	     	$po_chunk=array_chunk($poId_arr, 999);
	     	$prod_po_ids_cond= "";
	     	$prod_po_ids_cond2= "";
	     	foreach($po_chunk as $vals)
	     	{
	     		$imp_ids=implode(",", $vals);
	     		if($prod_po_ids_cond=="")
	     		{
	     			$prod_po_ids_cond.=" and ( b.id in ($imp_ids) ";
	     			$prod_po_ids_cond2.=" and ( c.po_break_down_id in ($imp_ids) ";
	     		}
	     		else
	     		{
	     			$prod_po_ids_cond.=" or b.id in ($imp_ids) ";
					 $prod_po_ids_cond2.=" or ( c.po_break_down_id in ($imp_ids) ";
	     		}
	     	}
	     	 $prod_po_ids_cond.=" )";
	     	 $prod_po_ids_cond2 .=" )";
	    }
	    else
	    {
	     	$prod_po_ids_cond= " and b.id in($all_po_id) ";
	     	$prod_po_ids_cond2= " and c.po_break_down_id in($all_po_id) ";
	    }
		// echo "<pre>";
		// print_r($main_array);
		// echo "</pre>";

		// ================================================ PRODUCTION QUERY ==================================================
		$prod_sql="SELECT  a.buyer_name,a.style_ref_no as style,a.job_no as job_id,b.id as po_id,c.country_id,c.item_number_id,c.color_number_id,d.floor_id,d.sewing_line,d.prod_reso_allo,

		sum(case when d.production_type=4 and d.production_type=e.production_type and d.production_date<'$txt_datefrom' then e.production_qnty else 0 end ) as prev_sewing_input,
		sum(case when d.production_type=4 and d.production_type=e.production_type and d.production_date between '$txt_datefrom' and '$txt_dateto' then e.production_qnty else 0 end ) as curr_sewing_input,

		sum(case when d.production_type=5 and d.production_type=e.production_type and d.production_date<'$txt_datefrom' then e.production_qnty else 0 end ) as prev_sewing_output,
		sum(case when d.production_type=5 and d.production_type=e.production_type and d.production_date between '$txt_datefrom' and '$txt_dateto' then e.production_qnty else 0 end ) as curr_sewing_output,
		sum(case when d.production_type=5 and d.production_type=e.production_type and e.is_rescan=0  then e.reject_qty else 0 end ) as sewing_out_rej,

		sum(CASE WHEN d.production_type =5 and d.production_type=e.production_type and e.is_rescan>0 THEN e.reject_qty else 0 END) AS resc_rej_qty,

		sum(case when d.production_type=5 and d.production_type=e.production_type and e.is_rescan=0  then e.reject_qty else 0 end ) -

		sum(CASE WHEN d.production_type =5 and d.production_type=e.production_type and e.is_rescan=1 THEN e.production_qnty else 0 END) AS exact_reject,


		sum(case when d.production_type=11 and d.production_type=e.production_type and d.production_date<'$txt_datefrom' then e.production_qnty else 0 end ) as prev_poly_qty,
		sum(case when d.production_type=11 and d.production_type=e.production_type and d.production_date between '$txt_datefrom' and '$txt_dateto' then e.production_qnty else 0 end ) as curr_poly_qty,
		sum(case when d.production_type=11 and d.production_type=e.production_type then e.reject_qty else 0 end ) as poly_rej

		from  wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, pro_garments_production_mst d,pro_garments_production_dtls e
		where a.id=b.job_id and b.id=c.po_break_down_id and b.id=d.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id $prod_po_ids_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 and d.sewing_line<>0
		group by a.buyer_name,a.style_ref_no,a.job_no,b.id,c.country_id,c.item_number_id,c.color_number_id,d.floor_id,d.sewing_line,d.prod_reso_allo
		order by a.job_no,b.id,c.color_number_id";
		// echo $prod_sql;
		$prod_sql_res = sql_select($prod_sql);

		$prod_array = array();
		foreach ($prod_sql_res as $row)
		{
			if($row[csf("prod_reso_allo")]==1)
		    {
		    	$line_resource_mst_arr=explode(",",$prod_reso_arr[$row[csf('sewing_line')]]);
		    	$line_name="";
				foreach($line_resource_mst_arr as $resource_id)
				{
					$line_name .= ($line_name == "") ? $lineArr[$resource_id] : ",".$lineArr[$resource_id];
				}

				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['prev_sewing_input'] += $row[csf('prev_sewing_input')];
				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['exact_reject'] += $row[csf('exact_reject')];
				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['curr_sewing_input'] += $row[csf('curr_sewing_input')];
				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['prev_sewing_output'] += $row[csf('prev_sewing_output')];
				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['curr_sewing_output'] += $row[csf('curr_sewing_output')];
				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['prev_poly_qty'] += $row[csf('prev_poly_qty')];
				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['curr_poly_qty'] += $row[csf('curr_poly_qty')];
				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['sewing_out_rej'] += $row[csf('sewing_out_rej')];
				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['resc_qcpass'] += $row[csf('resc_rej_qty')];

				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['poly_rej'] += $row[csf('poly_rej')];
			}
			else
			{
				$line_name=$lineArr[$row[csf('sewing_line')]];
				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['prev_sewing_input'] += $row[csf('prev_sewing_input')];
				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['curr_sewing_input'] += $row[csf('curr_sewing_input')];
				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['prev_sewing_output'] += $row[csf('prev_sewing_output')];
				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['curr_sewing_output'] += $row[csf('curr_sewing_output')];
				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['prev_poly_qty'] += $row[csf('prev_poly_qty')];
				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['curr_poly_qty'] += $row[csf('curr_poly_qty')];
				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['sewing_out_rej'] += $row[csf('sewing_out_rej')];
				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['resc_qcpass'] += $row[csf('resc_rej_qty')];
				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['poly_rej'] += $row[csf('poly_rej')];
				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['exact_reject'] += $row[csf('exact_reject')];
			}
		}
		// echo "<pre>";
		// print_r($prod_array);
		// echo "</pre>";

		// ======================================= SEW LOSS QNTY ============================================
		$sql_cond2 	="";
		$sql_cond2 .= ($location_id=="") 		? "" : " and c.location in($location_id)";
		$sql_cond2 .= ($floor_id=="") 			? "" : " and c.floor_id in($floor_id)";
		$sql_cond2 .= ($line_id=="") 			? "" : " and c.sewing_line in($line_id)";
		$sql_cond2 .= ($lc_company_id==0) 		? "" : " and c.company_id=$lc_company_id";

		$prod_defect_sql = "SELECT distinct a.id,a.defect_qty, c.po_break_down_id as po_id, c.item_number_id,c.floor_id,c.sewing_line,c.prod_reso_allo,c.country_id,e.color_number_id
		from pro_gmts_prod_dft a, pro_garments_production_mst c,pro_garments_production_dtls d,wo_po_color_size_breakdown e
		where a.mst_id=c.id and c.id=d.mst_id and c.po_break_down_id=e.po_break_down_id and e.id=d.color_size_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sql_cond2 $prod_po_ids_cond2  and a.defect_point_id=50 and a.defect_type_id=2 and a.production_type = 5";
		// echo $prod_defect_sql ; die;
		$gmts_prod_defect_arr = array();
		$res_gmtsDefectData = sql_select($prod_defect_sql);
		foreach($res_gmtsDefectData as $v)
		{
			if($v["PROD_RESO_ALLO"]==1)
		    {
		    	$line_resource_mst_arr=explode(",",$prod_reso_arr[$v['SEWING_LINE']]);
		    	$line_name="";
				foreach($line_resource_mst_arr as $resource_id)
				{
					$line_name .= ($line_name == "") ? $lineArr[$resource_id] : ",".$lineArr[$resource_id];
				}
			}else{
				$line_name=$lineArr[$v['SEWING_LINE']];
			}
			$gmts_prod_defect_arr[$v['PO_ID']][$v['COUNTRY_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']][$v['FLOOR_ID']][$line_name]['defect_qty'] += $v['DEFECT_QTY'];
		}

		// ======================================= FOR COLOR QNTY ============================================
		$color_sql="SELECT  a.buyer_name,a.style_ref_no as style,a.job_no as job_id,b.id as po_id, c.country_id,c.item_number_id,c.color_number_id, sum(c.order_quantity) as color_qnty
		from  wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c
		where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and a.status_active=1 and b.status_active=1 and c.status_active=1 $prod_po_ids_cond
		group by a.buyer_name,a.style_ref_no,a.job_no,b.id, c.country_id,c.item_number_id,c.color_number_id";
		$color_sql_res = sql_select($color_sql);
		$color_qnty_arr = array();
		foreach ($color_sql_res as $row)
		{
			$color_qnty_arr[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]] = $row[csf('color_qnty')];
		}
		// echo "<pre>";
		// print_r($color_qnty_arr);
		// echo "</pre>";
		$check_po_color = array();
		$po_qty_arr = array();
		foreach ($main_array as $job_key => $job_arr)
		{
			foreach ($job_arr as $style_key => $style_arr)
			{
				foreach ($style_arr as $buyer_key => $buyer_arr)
				{
					foreach ($buyer_arr as $po_key => $po_arr)
					{
						foreach ($po_arr as $country_key => $country_arr)
						{
							foreach ($country_arr as $item_key => $item_arr)
							{
								foreach ($item_arr as $color_key => $color_arr)
								{
									if(!in_array($color_key, $check_po_color[$job_key][$style_key][$buyer_key][$po_key][$country_key][$item_key][$color_key]))
									{
										$po_qty_arr[$style_key] += $color_qnty_arr[$job_key][$style_key][$buyer_key][$po_key][$country_key][$item_key][$color_key];
									}
									$check_po_color[] = $main_array[$job_key][$style_key][$buyer_key][$po_key][$country_key][$item_key][$color_key];

								}
							}
						}
					}
				}
			}
		}

		// echo "<pre>";
		// print_r($po_qty_arr);
		// echo "</pre>";
		// die();


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
			<table width="100%" cellspacing="0">
		        <tr class="form_caption" style="border:none;">
		            <td colspan="9" align="center" ><font size="3"><strong><u><? echo $company_details[$lc_company_id]; ?></u></strong></font></td>
		        </tr>
		        <tr class="form_caption" style="border:none;">
		            <td colspan="9" align="center"><font size="2"><strong>Style and Line Wise Production Report</strong></font></td>
		        </tr>
		    </table>
		    <div>
		    	<table class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" id="" width="2360">
		    		<thead>
		    			<th width="30">Sl.</th>
		    			<th width="100">Buyer Name</th>
		    			<th width="100">Buyer Client</th>
		    			<th width="100">Style Ref.</th>
		    			<th width="100">Job No.</th>
		    			<th width="70">Int.Ref</th>
		    			<th width="100">PO No.</th>
		    			<th width="100">Country Name</th>
		    			<th width="80">C. Ship Date</th>
		    			<th width="100">Garments Item</th>
		    			<th width="90">Color Name</th>
		    			<th width="80">Color Qty(pcs)</th>
		    			<th width="100">Floor Name</th>
		    			<th width="80">Line Name</th>
		    			<th width="80">Prev. Input</th>
		    			<th width="80">Cur. Input</th>
		    			<th width="80">Total Input</th>
		    			<th width="80">Prev. S.output</th>
		    			<th width="80">Cur. S.output</th>
		    			<th width="80">Total Output</th>
		    			<th width="80">Total S.Reject</th>
		    			<th width="80">Sew Loss</th>
		    			<th width="80">Sew WIP</th>
		    			<th width="80">Prev. Poly</th>
		    			<th width="80">Curr. Poly</th>
		    			<th width="80">Total Poly</th>
		    			<th width="80">Total Poly Rej.</th>
		    			<th width="80">Total Poly WIP</th>
		    			<th width="80">Input to Poly WIP</th>
		    			<th width="80">Remarks</th>
		    		</thead>
		    	</table>
		    	<div style="width: 2380px; overflow-y: scroll; max-height: 400px" id="scroll_body">
		    		<table cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all" width="2360" id="html_search">
		    			<?
		    			$check_color			= array();
		    			$sl=1;
		    			$grnd_color_qty 		= 0;
						$grnd_prev_sew_in_qty 	= 0;
						$grnd_curr_sew_in_qty 	= 0;
						$grnd_total_sew_in_qty 	= 0;
						$grnd_prev_sew_out_qty 	= 0;
						$grnd_curr_sew_out_qty 	= 0;
						$grnd_total_sew_out_qty = 0;
						$grnd_sew_out_rej_qty 	= 0;
						$grnd_sew_loss_qty 		= 0;
						$grnd_sew_wip_qty 		= 0;
						$grnd_prev_poly_qty 	= 0;
						$grnd_curr_poly_qty 	= 0;
						$grnd_total_poly_qty 	= 0;
						$grnd_poly_rej_qty 		= 0;
						$grnd_poly_wip_qty 		= 0;
						$grnd_in_to_poly_wip 	= 0;
						// pre($gmts_prod_defect_arr);

		    			foreach($main_array as $job_id=>$job_data)
		    			{
		    				foreach ($job_data as $style => $style_data)
		    				{
		    					$style_color_qty 		= 0;
		    					$style_prev_sew_in_qty 	= 0;
		    					$style_curr_sew_in_qty 	= 0;
		    					$style_total_sew_in_qty = 0;
		    					$style_prev_sew_out_qty = 0;
		    					$style_curr_sew_out_qty = 0;
		    					$style_total_sew_out_qty= 0;
		    					$style_sew_out_rej_qty 	= 0;
		    					$style_sew_wip_qty 		= 0;
		    					$style_sew_loss_qty 	= 0;
		    					$style_prev_poly_qty 	= 0;
		    					$style_curr_poly_qty 	= 0;
		    					$style_total_poly_qty 	= 0;
		    					$style_poly_rej_qty 	= 0;
		    					$style_poly_wip_qty 	= 0;
		    					$style_in_to_poly_wip 	= 0;

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
			    									foreach ($color_data as $floor_id => $floor_data)
			    									{
			    										foreach ($floor_data as $line_name => $row)
			    										{
															$prod_arr = $prod_array[$job_id][$style][$buyer][$po_id][$country_id][$item_id][$color_id][$floor_id][$line_name] ;
			    											$color_qnty = $color_qnty_arr[$job_id][$style][$buyer][$po_id][$country_id][$item_id][$color_id];
			    											$prev_sew_in_qty = $prod_arr['prev_sewing_input'];

															$sew_loss_qty = $gmts_prod_defect_arr[$po_id][$country_id][$item_id][$color_id][$floor_id][$line_name]['defect_qty']??0;


			    											$curr_sew_in_qty = $prod_arr['curr_sewing_input'];
			    											$prev_sew_out_qty = $prod_arr['prev_sewing_output'];
			    											$curr_sew_out_qty = $prod_arr['curr_sewing_output'];
			    											$sew_out_rej_qty = $prod_arr['sewing_out_rej'];
			    											$resc_rej_qty 		= $prod_arr['resc_rej_qty'];
			    											$resc_qcpass_qty = $prod_arr['resc_qcpass'];
			    											$exact_reject = $sew_out_rej_qty - $sew_loss_qty + $resc_qcpass_qty;
			    											$sew_out_rej_qty = $exact_reject;


			    											$prev_poly_qty = $prod_array[$job_id][$style][$buyer][$po_id][$country_id][$item_id][$color_id][$floor_id][$line_name]['prev_poly_qty'];
			    											$curr_poly_qty = $prod_array[$job_id][$style][$buyer][$po_id][$country_id][$item_id][$color_id][$floor_id][$line_name]['curr_poly_qty'];
			    											$poly_rej = $prod_array[$job_id][$style][$buyer][$po_id][$country_id][$item_id][$color_id][$floor_id][$line_name]['poly_rej'];
			    											$sewing_line = $row['sewing_line'];
			    											$prod_reso_allo = $row['prod_reso_allo'];
			    											if($prev_sew_in_qty !=0 || $prev_sew_out_qty !=0 || $prev_poly_qty !=0 || $curr_sew_in_qty !=0 || $curr_sew_out_qty !=0 || $curr_poly_qty !=0){
				    										$bgcolor = ($sl%2==0) ? "#ffffff" : "#f6faff";
					    									?>
					    										<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_1nd<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $sl; ?>" >
					    											<td style="word-wrap: break-word;word-break: break-all;" width="30"><? echo $sl;?></td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $buyerArr[$buyer]; ?></td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $buyerArr[$row['buyer_client']]; ?></td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $style;?></td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $job_id;?></td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="70"><? echo $row['grouping'];?></td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $row['po_number'];?></td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $countryArr[$country_id];?></td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="80"><? echo change_date_format($row['country_ship_date']); ?></td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $garments_item[$item_id]; ?></td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="90"><? echo $colorArr[$color_id]; ?></td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($color_qnty,0); ?></td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $floorArr[$floor_id]; ?></td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="80"><? echo $line_name; ?></td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">
					    												<a href="javascript:void(0)" onclick="openProdPopup('<? echo $po_id.'_'.$country_id.'_'.$item_id.'_'.$color_id.'_'.$floor_id.'_'.$sewing_line.'_'.$prod_reso_allo.'_'.$txt_datefrom.'_'.$txt_dateto.'_'.'4_4';?>');">
					    													<? echo number_format($prev_sew_in_qty,0); ?>
					    												</a>
					    											</td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">
					    												<a href="javascript:void(0)" onclick="openProdPopup('<? echo $po_id.'_'.$country_id.'_'.$item_id.'_'.$color_id.'_'.$floor_id.'_'.$sewing_line.'_'.$prod_reso_allo.'_'.$txt_datefrom.'_'.$txt_dateto.'_'.'4_1';?>');">
					    													<? echo number_format($curr_sew_in_qty,0); ?>
					    												</a>
					    											</td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">
					    												<a href="javascript:void(0)" onclick="openProdPopup('<? echo $po_id.'_'.$country_id.'_'.$item_id.'_'.$color_id.'_'.$floor_id.'_'.$sewing_line.'_'.$prod_reso_allo.'_'.$txt_datefrom.'_'.$txt_dateto.'_'.'4_2';?>');">
					    													<? $total_sew_in = $prev_sew_in_qty+$curr_sew_in_qty; echo number_format($total_sew_in,0); ?>
					    												</a>

					    											</td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">
					    												<a href="javascript:void(0)" onclick="openProdPopup('<? echo $po_id.'_'.$country_id.'_'.$item_id.'_'.$color_id.'_'.$floor_id.'_'.$sewing_line.'_'.$prod_reso_allo.'_'.$txt_datefrom.'_'.$txt_dateto.'_'.'5_4';?>');">
					    													<? echo number_format($prev_sew_out_qty,0); ?>
					    												</a>
					    											</td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">
					    												<a href="javascript:void(0)" onclick="openProdPopup('<? echo $po_id.'_'.$country_id.'_'.$item_id.'_'.$color_id.'_'.$floor_id.'_'.$sewing_line.'_'.$prod_reso_allo.'_'.$txt_datefrom.'_'.$txt_dateto.'_'.'5_1';?>');">
					    													<? echo number_format($curr_sew_out_qty,0); ?>
					    												</a>
					    											</td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">
					    												<a href="javascript:void(0)" onclick="openProdPopup('<? echo $po_id.'_'.$country_id.'_'.$item_id.'_'.$color_id.'_'.$floor_id.'_'.$sewing_line.'_'.$prod_reso_allo.'_'.$txt_datefrom.'_'.$txt_dateto.'_'.'5_2';?>');">
					    													<? $total_sew_out = $prev_sew_out_qty+$curr_sew_out_qty; echo number_format($total_sew_out,0); ?>
					    												</a>
					    											</td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">
					    												<a href="javascript:void(0)" onclick="openProdPopup('<? echo $po_id.'_'.$country_id.'_'.$item_id.'_'.$color_id.'_'.$floor_id.'_'.$sewing_line.'_'.$prod_reso_allo.'_'.$txt_datefrom.'_'.$txt_dateto.'_'.'5_3';?>');">
					    													<?  echo number_format($exact_reject,0); ?>
					    												</a>
					    											</td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">
					    													<? echo number_format($sew_loss_qty,0); ?>

					    											</td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right" title="Total Input-(Total Output+Total S.Reject+Sew Loss)">
					    												<a href="javascript:void(0)" onclick="openProdPopupWIP('<? echo $po_id.'_'.$country_id.'_'.$item_id.'_'.$color_id.'_'.$floor_id.'_'.$sewing_line.'_'.$prod_reso_allo.'_'.$txt_datefrom.'_'.$txt_dateto.'_'.'5_5';?>');">
					    													<? $sew_out_wip = $total_sew_in-($total_sew_out+$exact_reject+$sew_loss_qty); echo number_format($sew_out_wip,0); ?>
					    												</a>
					    											</td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">
					    												<a href="javascript:void(0)" onclick="openProdPopup('<? echo $po_id.'_'.$country_id.'_'.$item_id.'_'.$color_id.'_'.$floor_id.'_'.$sewing_line.'_'.$prod_reso_allo.'_'.$txt_datefrom.'_'.$txt_dateto.'_'.'11_4';?>');">
					    													<? echo number_format($prev_poly_qty,0); ?>
					    												</a>
					    											</td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">
					    												<a href="javascript:void(0)" onclick="openProdPopup('<? echo $po_id.'_'.$country_id.'_'.$item_id.'_'.$color_id.'_'.$floor_id.'_'.$sewing_line.'_'.$prod_reso_allo.'_'.$txt_datefrom.'_'.$txt_dateto.'_'.'11_1';?>');">
					    													<? echo number_format($curr_poly_qty,0); ?>
					    												</a>
					    											</td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">
					    												<a href="javascript:void(0)" onclick="openProdPopup('<? echo $po_id.'_'.$country_id.'_'.$item_id.'_'.$color_id.'_'.$floor_id.'_'.$sewing_line.'_'.$prod_reso_allo.'_'.$txt_datefrom.'_'.$txt_dateto.'_'.'11_2';?>');">
					    													<? $total_poly = $prev_poly_qty+$curr_poly_qty; echo number_format($total_poly,0); ?>
					    											</a>
					    											</td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">
					    												<a href="javascript:void(0)" onclick="openProdPopup('<? echo $po_id.'_'.$country_id.'_'.$item_id.'_'.$color_id.'_'.$floor_id.'_'.$sewing_line.'_'.$prod_reso_allo.'_'.$txt_datefrom.'_'.$txt_dateto.'_'.'11_3';?>');">
					    													<? echo number_format($poly_rej,0); ?>
					    												</a>
					    											</td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">
					    												<a href="javascript:void(0)" onclick="openProdPopupWIP('<? echo $po_id.'_'.$country_id.'_'.$item_id.'_'.$color_id.'_'.$floor_id.'_'.$sewing_line.'_'.$prod_reso_allo.'_'.$txt_datefrom.'_'.$txt_dateto.'_'.'11_6';?>');">
					    													<? $poly_wip = $total_sew_out - ($total_poly+$poly_rej); echo number_format($poly_wip,0); ?>
					    												</a>
					    											</td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">
					    												<a href="javascript:void(0)" onclick="openProdPopupWIP('<? echo $po_id.'_'.$country_id.'_'.$item_id.'_'.$color_id.'_'.$floor_id.'_'.$sewing_line.'_'.$prod_reso_allo.'_'.$txt_datefrom.'_'.$txt_dateto.'_'.'11_7';?>');">
					    													<? $input_to_poly_wip = $total_sew_in - ($total_poly+$poly_rej); echo number_format($input_to_poly_wip,0); ?>
					    												</a>
					    											</td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="80"><? echo $shipment_status[$row['shiping_status']];?></td>
					    										</tr>
					    									<?
					    									/*if($l !=1)
					    									{
					    										if( !in_array($main_array[$job_id][$style][$buyer][$po_id][$country_id][$item_id][$color_id], $check_color))
					    										{
					    											$style_color_qty 		+= $color_qnty;
					    											$grnd_color_qty 		+= $color_qnty;
					    											$check_color[] = $main_array[$job_id][$style][$buyer][$po_id][$country_id][$item_id][$color_id];
					    										}

					    									}*/
					    									// $style_color_qty		+= $po_qty_arr[$style];
					    									$style_prev_sew_in_qty 	+= $prev_sew_in_qty;
									    					$style_curr_sew_in_qty 	+= $curr_sew_in_qty;
									    					$style_total_sew_in_qty += $total_sew_in;
									    					$style_prev_sew_out_qty += $prev_sew_out_qty;
									    					$style_curr_sew_out_qty += $curr_sew_out_qty;
									    					$style_total_sew_out_qty+= $total_sew_out;
									    					$style_sew_out_rej_qty 	+= $sew_out_rej_qty;
									    					$style_sew_wip_qty 		+= $sew_out_wip;
									    					$style_sew_loss_qty 	+= $sew_loss_qty;
									    					$style_prev_poly_qty 	+= $prev_poly_qty;
									    					$style_curr_poly_qty 	+= $curr_poly_qty;
									    					$style_total_poly_qty 	+= $total_poly;
									    					$style_poly_rej_qty 	+= $poly_rej;
									    					$style_poly_wip_qty 	+= $poly_wip;
									    					$style_input_to_poly_wip+= $input_to_poly_wip;
									    					//==========================================
									    					// $grnd_color_qty			+= $po_qty_arr[$style];
					    									$grnd_prev_sew_in_qty 	+= $prev_sew_in_qty;
									    					$grnd_curr_sew_in_qty 	+= $curr_sew_in_qty;
									    					$grnd_total_sew_in_qty 	+= $total_sew_in;
									    					$grnd_prev_sew_out_qty 	+= $prev_sew_out_qty;
									    					$grnd_curr_sew_out_qty 	+= $curr_sew_out_qty;
									    					$grnd_total_sew_out_qty += $total_sew_out;
									    					$grnd_sew_out_rej_qty 	+= $sew_out_rej_qty;
									    					$grnd_sew_wip_qty 		+= $sew_out_wip;
															$grnd_sew_loss_qty 		+= $sew_loss_qty;
									    					$grnd_prev_poly_qty 	+= $prev_poly_qty;
									    					$grnd_curr_poly_qty 	+= $curr_poly_qty;
									    					$grnd_total_poly_qty 	+= $total_poly;
									    					$grnd_poly_rej_qty 		+= $poly_rej;
									    					$grnd_poly_wip_qty 		+= $poly_wip;
									    					$grnd_input_to_poly_wip += $input_to_poly_wip;

									    					$sl++;
									    					}
					    								}
				    								}
			    								}
			    							}
			    						}
			    					}
			    					?>
			    					<tr class="gd-color">
			    						<td align="right" colspan="11">Style Total </td>
			    						<td align="right"><? echo number_format($po_qty_arr[$style],0); ?></td>
			    						<td align="right"></td>
			    						<td align="right"></td>
			    						<td align="right"><? echo number_format($style_prev_sew_in_qty,0); ?></td>
			    						<td align="right"><? echo number_format($style_curr_sew_in_qty,0); ?></td>
			    						<td align="right"><? echo number_format($style_total_sew_in_qty,0); ?></td>
			    						<td align="right"><? echo number_format($style_prev_sew_out_qty,0); ?></td>
			    						<td align="right"><? echo number_format($style_curr_sew_out_qty,0); ?></td>
			    						<td align="right"><? echo number_format($style_total_sew_out_qty,0); ?></td>
			    						<td align="right"><? echo number_format($style_sew_out_rej_qty,0); ?></td>
			    						<td align="right"><? echo number_format($style_sew_loss_qty,0); ?></td>
			    						<td align="right"><? echo number_format($style_sew_wip_qty,0); ?></td>
			    						<td align="right"><? echo number_format($style_prev_poly_qty,0); ?></td>
			    						<td align="right"><? echo number_format($style_curr_poly_qty,0); ?></td>
			    						<td align="right"><? echo number_format($style_total_poly_qty,0); ?></td>
			    						<td align="right"><? echo number_format($style_poly_rej_qty,0); ?></td>
			    						<td align="right"><? echo number_format($style_poly_wip_qty,0); ?></td>
			    						<td align="right"><? echo number_format($style_input_to_poly_wip,0); ?></td>
			    						<td align="right"></td>
			    					</tr>
			    					<?
			    					$grnd_color_qty += $po_qty_arr[$style];
		    					}
		    				}
		    			}
		    			?>
		    		</table>
		    	</div>
		    	<div style="width: 2380px;"">
		    		<table cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" width="2360">
		    			<tfoot>
		    				<tr class="gd-color3">
		    					<td width="30"></td>
		    					<td width="100"></td>
		    					<td width="100"></td>
		    					<td width="100"></td>
		    					<td width="100"></td>
		    					<td width="70"></td>
		    					<td width="100"></td>
		    					<td width="100"></td>
		    					<td width="80"></td>
		    					<td width="100"></td>
								<td width="90" align="right">Grand Total </td>
	    						<td width="80" align="right"><? echo number_format($grnd_color_qty,0); ?></td>
	    						<td width="100" align="right"></td>
	    						<td width="80" align="right"></td>
	    						<td width="80" align="right"><? echo number_format($grnd_prev_sew_in_qty,0); ?></td>
	    						<td width="80" align="right"><? echo number_format($grnd_curr_sew_in_qty,0); ?></td>
	    						<td width="80" align="right"><? echo number_format($grnd_total_sew_in_qty,0); ?></td>
	    						<td width="80" align="right"><? echo number_format($grnd_prev_sew_out_qty,0); ?></td>
	    						<td width="80" align="right"><? echo number_format($grnd_curr_sew_out_qty,0); ?></td>
	    						<td width="80" align="right"><? echo number_format($grnd_total_sew_out_qty,0); ?></td>
	    						<td width="80" align="right"><? echo number_format($grnd_sew_out_rej_qty,0); ?></td>
	    						<td width="80" align="right"><? echo number_format($grnd_sew_loss_qty,0); ?></td>
	    						<td width="80" align="right"><? echo number_format($grnd_sew_wip_qty,0); ?></td>
	    						<td width="80" align="right"><? echo number_format($grnd_prev_poly_qty,0); ?></td>
	    						<td width="80" align="right"><? echo number_format($grnd_curr_poly_qty,0); ?></td>
	    						<td width="80" align="right"><? echo number_format($grnd_total_poly_qty,0); ?></td>
	    						<td width="80" align="right"><? echo number_format($grnd_poly_rej_qty,0); ?></td>
	    						<td width="80" align="right"><? echo number_format($grnd_poly_wip_qty,0); ?></td>
	    						<td width="80" align="right"><? echo number_format($grnd_input_to_poly_wip,0); ?></td>
	    						<td width="80" align="right"></td>
							</tr>
		    			</tfoot>
		    		</table>
		    </div>
	    </div>
	   <?
	}
	elseif($type==2)
	{
		// ================================================ MAIN QUERY ==================================================
		$job_sql="SELECT a.id from  wo_po_details_master a, wo_po_break_down  b, pro_garments_production_mst d
		where a.id=b.job_id and b.id=d.po_break_down_id and a.status_active=1 and b.status_active=1 and d.status_active=1  $sql_cond2 group by a.id";
		$all_job_arr=array();

		foreach(sql_select($job_sql) as $st)
		{
			$all_job_arr[$st[csf("id")]]=$st[csf("id")];
		}
		$job_cond="";
		if(count($all_job_arr)>0)
		{
			$job_ids=implode(",", $all_job_arr);
			$job_cond.=" and a.id in($job_ids)";
		}
		//echo "string".$job_cond;die;


		$sql="SELECT a.job_no, a.buyer_name,a.client_id as buyer_client, a.style_ref_no as style,a.job_no_prefix_num as job_id,b.id as po_id,b.po_number,b.shiping_status, b.grouping as internal_ref, c.country_id,c.country_ship_date,c.item_number_id,c.color_number_id,d.floor_id,d.sewing_line,d.prod_reso_allo
		from  wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, pro_garments_production_mst d
		where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and b.id=d.po_break_down_id $sql_cond $po_ids_cond $job_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and d.sewing_line<>0
		order by d.sewing_line asc";
		// a.job_no_prefix_num,b.id,c.color_number_id
		// echo $sql;die;
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
		$row_wise_po_array = array();
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

				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['country_ship_date'] = $row[csf('country_ship_date')];
				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['po_number'] = $row[csf('po_number')];
				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['shiping_status'] = $row[csf('shiping_status')];
				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['sewing_line'] = $row[csf('sewing_line')];
				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['prod_reso_allo'] = $row[csf('prod_reso_allo')];
				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['buyer_client'] = $row[csf('buyer_client')];
				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['job_no'] = $row[csf('job_no')];
				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['internal_ref'] = $row[csf('internal_ref')];

				$row_wise_po_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name] .= $row[csf('po_id')].",";
			}
			else
			{
				$line_name=$lineArr[$row[csf('sewing_line')]];
				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['country_ship_date'] = $row[csf('country_ship_date')];
				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['po_number'] = $row[csf('po_number')];
				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['shiping_status'] = $row[csf('shiping_status')];
				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['sewing_line'] = $row[csf('sewing_line')];
				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['prod_reso_allo'] = $row[csf('prod_reso_allo')];
				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['buyer_client'] = $row[csf('buyer_client')];
				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['job_no'] = $row[csf('job_no')];
				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['internal_ref'] = $row[csf('internal_ref')];

				$row_wise_po_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name] .= $row[csf('po_id')].",";
			}
			$poId_arr[$row[csf('po_id')]] = $row[csf('po_id')];
		}
		$all_po_id = implode(",", $poId_arr);
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
		// print_r($row_wise_po_array);
		// echo "</pre>";

		$prod_po_ids_condbk="";
		$prod_sql="SELECT  a.buyer_name,a.style_ref_no as style,a.job_no_prefix_num as job_id,c.item_number_id,c.color_number_id,d.floor_id,d.sewing_line,d.prod_reso_allo,

		sum(case when d.production_type=4 and d.production_type=e.production_type and d.production_date<'$txt_datefrom' then e.production_qnty else 0 end ) as prev_sewing_input,
		sum(case when d.production_type=4 and d.production_type=e.production_type and d.production_date between '$txt_datefrom' and '$txt_dateto' then e.production_qnty else 0 end ) as curr_sewing_input,

		sum(case when d.production_type=5 and d.production_type=e.production_type and d.production_date<'$txt_datefrom' then e.production_qnty else 0 end ) as prev_sewing_output,
		sum(case when d.production_type=5 and d.production_type=e.production_type and d.production_date between '$txt_datefrom' and '$txt_dateto' then e.production_qnty else 0 end ) as curr_sewing_output,

		sum(case when d.production_type=5 and d.production_type=e.production_type and e.is_rescan =0 then e.reject_qty else 0 end ) as sewing_out_rej,

		sum(CASE WHEN d.production_type =5 and d.production_type=e.production_type and e.is_rescan = 1 THEN e.reject_qty else 0 END) AS resc_rej_qty,


		sum(case when d.production_type=5  and e.is_rescan=0  then e.reject_qty else 0 end ) -

		sum(CASE WHEN d.production_type =5  and e.is_rescan=1 THEN e.production_qnty else 0 END) AS exact_reject,


		sum(case when d.production_type=11 and d.production_type=e.production_type and d.production_date<'$txt_datefrom' then e.production_qnty else 0 end ) as prev_poly_qty,
		sum(case when d.production_type=11 and d.production_type=e.production_type and d.production_date between '$txt_datefrom' and '$txt_dateto' then e.production_qnty else 0 end ) as curr_poly_qty,
		sum(case when d.production_type=11 and d.production_type=e.production_type then e.reject_qty else 0 end ) as poly_rej

		from  wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, pro_garments_production_mst d,pro_garments_production_dtls e
		where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and b.id=d.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id $prod_po_ids_condbk $job_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and d.sewing_line<>0
		group by a.buyer_name,a.style_ref_no,a.job_no_prefix_num,c.item_number_id,c.color_number_id,d.floor_id,d.sewing_line,d.prod_reso_allo
		order by a.job_no_prefix_num,c.color_number_id";
		// echo $prod_sql;die();
		$prod_sql_res = sql_select($prod_sql);
		$prod_array = array();

		$reject_sql="SELECT e.bundle_no, d.prod_reso_allo,a.buyer_name,a.style_ref_no as style,a.job_no_prefix_num as job_id,c.item_number_id,c.color_number_id,d.floor_id,d.sewing_line,e.bundle_no , sum(case when d.production_type=5  and e.is_rescan=0  then e.reject_qty else 0 end ) as reject_qty , sum(CASE WHEN d.production_type =5  and e.is_rescan=1 THEN e.production_qnty else 0 END) AS replace_qty from  wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, pro_garments_production_mst d,pro_garments_production_dtls e  		where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and b.id=d.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id $prod_po_ids_condbk $job_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and  d.production_type=5 group by e.bundle_no, d.prod_reso_allo,a.buyer_name,a.style_ref_no ,e.bundle_no ,a.job_no_prefix_num ,c.item_number_id,c.color_number_id,d.floor_id,d.sewing_line having sum(case when d.production_type=5  and e.is_rescan=0  then e.reject_qty else 0 end )>0";
		foreach(sql_select($reject_sql) as $row)
		{
			if($row[csf("prod_reso_allo")]==1)
		    {
		    	$line_resource_mst_arr=explode(",",$prod_reso_arr[$row[csf('sewing_line')]]);
		    	$line_name="";
				foreach($line_resource_mst_arr as $resource_id)
				{
					$line_name .= ($line_name == "") ? $lineArr[$resource_id] : ",".$lineArr[$resource_id];
				}
				 if($row[csf('reject_qty')]<=$row[csf('replace_qty')])
				 {
				 	$row[csf('replace_qty')]=$row[csf('reject_qty')];
				 }
				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['exact_reject'] += ($row[csf('reject_qty')]-$row[csf('replace_qty')]);
			}
		}
		//print_r($prod_array);die;

		foreach ($prod_sql_res as $row)
		{
			if($row[csf("prod_reso_allo")]==1)
		    {
		    	$line_resource_mst_arr=explode(",",$prod_reso_arr[$row[csf('sewing_line')]]);
		    	$line_name="";
				foreach($line_resource_mst_arr as $resource_id)
				{
					$line_name .= ($line_name == "") ? $lineArr[$resource_id] : ",".$lineArr[$resource_id];
				}

				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['prev_sewing_input'] += $row[csf('prev_sewing_input')];
				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['curr_sewing_input'] += $row[csf('curr_sewing_input')];
				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['prev_sewing_output'] += $row[csf('prev_sewing_output')];
				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['curr_sewing_output'] += $row[csf('curr_sewing_output')];
				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['prev_poly_qty'] += $row[csf('prev_poly_qty')];
				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['curr_poly_qty'] += $row[csf('curr_poly_qty')];
				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['sewing_out_rej'] += $row[csf('sewing_out_rej')];
				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['resc_qcpass'] += $row[csf('resc_rej_qty')];
				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['poly_rej'] += $row[csf('poly_rej')];
				//$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['exact_reject'] += $row[csf('exact_reject')];
			}
			else
			{
				$line_name=$lineArr[$row[csf('sewing_line')]];
				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['prev_sewing_input'] += $row[csf('prev_sewing_input')];
				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['curr_sewing_input'] += $row[csf('curr_sewing_input')];
				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['prev_sewing_output'] += $row[csf('prev_sewing_output')];
				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['curr_sewing_output'] += $row[csf('curr_sewing_output')];
				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['prev_poly_qty'] += $row[csf('prev_poly_qty')];
				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['curr_poly_qty'] += $row[csf('curr_poly_qty')];
				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['sewing_out_rej'] += $row[csf('sewing_out_rej')];
				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['resc_qcpass'] += $row[csf('resc_rej_qty')];
				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['poly_rej'] += $row[csf('poly_rej')];
				//$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['exact_reject'] += $row[csf('exact_reject')];
			}
		}
		// echo "<pre>";
		// print_r($prod_array);
		// echo "</pre>";

		// ======================================= FOR COLOR QNTY ============================================
		$po_cond = str_replace("b.id", " c.po_break_down_id", $prod_po_ids_cond);
		$color_sql="SELECT c.po_break_down_id,  a.buyer_name,a.style_ref_no as style,a.job_no_prefix_num as job_id,c.item_number_id,c.color_number_id, sum(c.order_quantity) as color_qnty
		from  wo_po_details_master a,wo_po_color_size_breakdown c
		where a.id=c.job_id and a.status_active=1 and c.status_active=1 $po_cond
		group by c.po_break_down_id,a.buyer_name,a.style_ref_no,a.job_no_prefix_num,c.item_number_id,c.color_number_id";
		//echo $color_sql;die;
		$color_sql_res = sql_select($color_sql);
		$color_qnty_arr = array();
		foreach ($color_sql_res as $row)
		{
			$color_qnty_arr[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]] = $row[csf('color_qnty')];
			$color_qnty_arr2[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]] = $row[csf('color_qnty')];
		}
		// echo "<pre>";
		 //print_r($color_qnty_arr2);
		// echo "</pre>";
		$check_po_color = array();
		$po_qty_arr = array();
		foreach ($main_array as $job_key => $job_arr)
		{
			foreach ($job_arr as $style_key => $style_arr)
			{
				foreach ($style_arr as $buyer_key => $buyer_arr)
				{
					foreach ($buyer_arr as $po_key => $po_arr)
					{
						foreach ($po_arr as $item_key => $item_arr)
						{
							foreach ($item_arr as $color_key => $color_arr)
							{
								if( $check_po_color[$job_key][$style_key][$buyer_key][$po_key][$item_key][$color_key]=="")
								{

									$po_qty_arr[$style_key] += $color_qnty_arr2[$job_key][$style_key][$buyer_key][$po_key][$item_key][$color_key];
									 $check_po_color[$job_key][$style_key][$buyer_key][$po_key][$item_key][$color_key]=25;
								}


							}
						}
					}
				}
			}
		}

		// echo "<pre>";
		 //print_r($po_qty_arr);
		// echo "</pre>";
		// die();


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
			<table width="100%" cellspacing="0">
		        <tr class="form_caption" style="border:none;">
		            <td colspan="9" align="center" ><font size="3"><strong><u><? echo $company_details[$lc_company_id]; ?></u></strong></font></td>
		        </tr>
		        <tr class="form_caption" style="border:none;">
		            <td colspan="9" align="center"><font size="2"><strong>Style and Line Wise Production Report</strong></font></td>
		        </tr>
		    </table>
		    <div>
		    	<table class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" id="" width="2050">
		    		<thead>
		    			<th width="30">Sl.</th>
		    			<th width="100">Buyer Name</th>
		    			<th width="100">IR/IB</th>
		    			<th width="100">Style Ref.</th>
		    			<th width="70">Job No.</th>
		    			<th width="100">Garments Item</th>
		    			<th width="90">Color Name</th>
		    			<th width="80">Color Qty(pcs)</th>
		    			<th width="100">Floor Name</th>
		    			<th width="80">Line Name</th>
		    			<th width="80">Prev. Input</th>
		    			<th width="80">Cur. Input</th>
		    			<th width="80">Total Input</th>
		    			<th width="80">Prev. S.output</th>
		    			<th width="80">Cur. S.output</th>
		    			<th width="80">Total Output</th>
		    			<th width="80">Total S.Reject</th>
		    			<th width="80">Sew WIP</th>
		    			<th width="80">Prev. Poly</th>
		    			<th width="80">Curr. Poly</th>
		    			<th width="80">Total Poly</th>
		    			<th width="80">Total Poly Rej.</th>
		    			<th width="80">Total Poly WIP</th>
		    			<th width="80">Input to Poly WIP</th>
		    			<th width="80">Remarks</th>
		    		</thead>
		    	</table>
		    	<div style="width: 2070px; overflow-y: scroll; max-height: 400px" id="scroll_body">
		    		<table cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all" width="2050" id="table_body">
		    			<?
		    			$check_color 			= array();
		    			$sl=1;
		    			$grnd_color_qty 		= 0;
						$grnd_prev_sew_in_qty 	= 0;
						$grnd_curr_sew_in_qty 	= 0;
						$grnd_total_sew_in_qty 	= 0;
						$grnd_prev_sew_out_qty 	= 0;
						$grnd_curr_sew_out_qty 	= 0;
						$grnd_total_sew_out_qty = 0;
						$grnd_sew_out_rej_qty 	= 0;
						$grnd_sew_wip_qty 		= 0;
						$grnd_prev_poly_qty 	= 0;
						$grnd_curr_poly_qty 	= 0;
						$grnd_total_poly_qty 	= 0;
						$grnd_poly_rej_qty 		= 0;
						$grnd_poly_wip_qty 		= 0;
						$grnd_in_to_poly_wip 	= 0;
						$po_qty_arr_style=array();
						$chk_arr=array();
		    			foreach($main_array as $job_id=>$job_data)
		    			{
		    				foreach ($job_data as $style => $style_data)
		    				{
		    					$style_color_qty 		= 0;
		    					$style_prev_sew_in_qty 	= 0;
		    					$style_curr_sew_in_qty 	= 0;
		    					$style_total_sew_in_qty = 0;
		    					$style_prev_sew_out_qty = 0;
		    					$style_curr_sew_out_qty = 0;
		    					$style_total_sew_out_qty= 0;
		    					$style_sew_out_rej_qty 	= 0;
		    					$style_sew_wip_qty 		= 0;
		    					$style_prev_poly_qty 	= 0;
		    					$style_curr_poly_qty 	= 0;
		    					$style_total_poly_qty 	= 0;
		    					$style_poly_rej_qty 	= 0;
		    					$style_poly_wip_qty 	= 0;
		    					$style_in_to_poly_wip 	= 0;

		    					foreach ($style_data as $buyer => $buyer_data)
		    					{
	    							foreach ($buyer_data as $item_id => $item_data)
	    							{
	    								foreach ($item_data as $color_id => $color_data)
	    								{
	    									foreach ($color_data as $floor_id => $floor_data)
	    									{
	    										foreach ($floor_data as $line_name => $row)
	    										{
	    											$color_qnty = $color_qnty_arr[$job_id][$style][$buyer][$item_id][$color_id];
	    											if($chk_arr[$job_id][$style][$buyer][$item_id][$color_id]=="")
	    											{
	    												$po_qty_arr_style[$style]+=$color_qnty;
	    												$chk_arr[$job_id][$style][$buyer][$item_id][$color_id]=$color_qnty;
	    											}

	    											$prev_sew_in_qty = $prod_array[$job_id][$style][$buyer][$item_id][$color_id][$floor_id][$line_name]['prev_sewing_input'];
	    											$exact_reject = $prod_array[$job_id][$style][$buyer][$item_id][$color_id][$floor_id][$line_name]['exact_reject'];
	    											$curr_sew_in_qty = $prod_array[$job_id][$style][$buyer][$item_id][$color_id][$floor_id][$line_name]['curr_sewing_input'];
	    											$prev_sew_out_qty = $prod_array[$job_id][$style][$buyer][$item_id][$color_id][$floor_id][$line_name]['prev_sewing_output'];
	    											$curr_sew_out_qty = $prod_array[$job_id][$style][$buyer][$item_id][$color_id][$floor_id][$line_name]['curr_sewing_output'];
	    											$sew_out_rej_qty = $prod_array[$job_id][$style][$buyer][$item_id][$color_id][$floor_id][$line_name]['sewing_out_rej'];
	    											$resc_qcpass_qty = $prod_array[$job_id][$style][$buyer][$item_id][$color_id][$floor_id][$line_name]['resc_qcpass'];
	    											$sew_out_rej_qty = $sew_out_rej_qty - $resc_qcpass_qty;
	    											$sew_out_rej_qty =$exact_reject;

	    											$prev_poly_qty = $prod_array[$job_id][$style][$buyer][$item_id][$color_id][$floor_id][$line_name]['prev_poly_qty'];
	    											$curr_poly_qty = $prod_array[$job_id][$style][$buyer][$item_id][$color_id][$floor_id][$line_name]['curr_poly_qty'];
	    											$poly_rej = $prod_array[$job_id][$style][$buyer][$item_id][$color_id][$floor_id][$line_name]['poly_rej'];
	    											$sewing_line = $row['sewing_line'];
	    											$prod_reso_allo = $row['prod_reso_allo'];
	    											$job_no = $row['job_no'];

	    											$row_wise_po = $row_wise_po_array[$job_id][$style][$buyer][$item_id][$color_id][$floor_id][$line_name];
	    											$row_wise_po = chop($row_wise_po,',');
	    											$row_wise_po_ex = array_unique(explode(",", $row_wise_po));
	    											$po_id = implode(",", $row_wise_po_ex);
	    											if($prev_sew_in_qty !=0 || $prev_sew_out_qty !=0 || $prev_poly_qty !=0 || $curr_sew_in_qty !=0 || $curr_sew_out_qty !=0 || $curr_poly_qty !=0){
		    										$bgcolor = ($sl%2==0) ? "#ffffff" : "#f6faff";
			    									?>
			    										<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_1nd<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $sl; ?>" >
			    											<td style="word-wrap: break-word;word-break: break-all;" width="30"><? echo $sl;?></td>
			    											<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $buyerArr[$buyer]; ?></td>
			    											<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $row['internal_ref']; ?></td>
			    											<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $style;?></td>
			    											<td style="word-wrap: break-word;word-break: break-all;" width="70"><? echo $job_id;?></td>
			    											<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $garments_item[$item_id]; ?></td>
			    											<td style="word-wrap: break-word;word-break: break-all;" width="90"><? echo $colorArr[$color_id]; ?></td>
			    											<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($color_qnty,0); ?></td>
			    											<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $floorArr[$floor_id]; ?></td>
			    											<td style="word-wrap: break-word;word-break: break-all;" width="80"><? echo $line_name; ?></td>
			    											<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">
			    												<a href="javascript:void(0)" onclick="openProdPopup2('<? echo $po_id.'_'.$item_id.'_'.$color_id.'_'.$floor_id.'_'.$sewing_line.'_'.$prod_reso_allo.'_'.$txt_datefrom.'_'.$txt_dateto.'_'.'4_4_'.$job_no;?>');">
			    													<? echo number_format($prev_sew_in_qty,0); ?>
			    												</a>
			    											</td>
			    											<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">
			    												<a href="javascript:void(0)" onclick="openProdPopup2('<? echo $po_id.'_'.$item_id.'_'.$color_id.'_'.$floor_id.'_'.$sewing_line.'_'.$prod_reso_allo.'_'.$txt_datefrom.'_'.$txt_dateto.'_'.'4_1_'.$job_no;?>');">
			    													<? echo number_format($curr_sew_in_qty,0); ?>
			    												</a>
			    											</td>
			    											<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">
			    												<a href="javascript:void(0)" onclick="openProdPopup2('<? echo $po_id.'_'.$item_id.'_'.$color_id.'_'.$floor_id.'_'.$sewing_line.'_'.$prod_reso_allo.'_'.$txt_datefrom.'_'.$txt_dateto.'_'.'4_2_'.$job_no;?>');">
			    													<? $total_sew_in = $prev_sew_in_qty+$curr_sew_in_qty; echo number_format($total_sew_in,0); ?>
			    												</a>
			    											</td>
			    											<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">
			    												<a href="javascript:void(0)" onclick="openProdPopup2('<? echo $po_id.'_'.$item_id.'_'.$color_id.'_'.$floor_id.'_'.$sewing_line.'_'.$prod_reso_allo.'_'.$txt_datefrom.'_'.$txt_dateto.'_'.'5_4_'.$job_no;?>');">
			    													<? echo number_format($prev_sew_out_qty,0); ?>
			    												</a>
			    											</td>
			    											<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">
			    												<a href="javascript:void(0)" onclick="openProdPopup2('<? echo $po_id.'_'.$item_id.'_'.$color_id.'_'.$floor_id.'_'.$sewing_line.'_'.$prod_reso_allo.'_'.$txt_datefrom.'_'.$txt_dateto.'_'.'5_1_'.$job_no;?>');">
			    													<? echo number_format($curr_sew_out_qty,0); ?>
			    												</a>
			    											</td>
			    											<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">
			    												<a href="javascript:void(0)" onclick="openProdPopup2('<? echo $po_id.'_'.$item_id.'_'.$color_id.'_'.$floor_id.'_'.$sewing_line.'_'.$prod_reso_allo.'_'.$txt_datefrom.'_'.$txt_dateto.'_'.'5_2_'.$job_no;?>');">
			    													<? $total_sew_out = $prev_sew_out_qty+$curr_sew_out_qty; echo number_format($total_sew_out,0); ?>
			    												</a>
			    											</td>
			    											<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">
			    												<a href="javascript:void(0)" onclick="openProdPopup2('<? echo $po_id.'_'.$item_id.'_'.$color_id.'_'.$floor_id.'_'.$sewing_line.'_'.$prod_reso_allo.'_'.$txt_datefrom.'_'.$txt_dateto.'_'.'5_3_'.$job_no;?>');">
			    													<? echo number_format($exact_reject,0); ?>
			    												</a>
			    											</td>
			    											<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">
			    												<a href="javascript:void(0)" onclick="openProdPopupWIP2('<? echo $po_id.'_'.$item_id.'_'.$color_id.'_'.$floor_id.'_'.$sewing_line.'_'.$prod_reso_allo.'_'.$txt_datefrom.'_'.$txt_dateto.'_'.'5_5_'.$job_no;?>');">
			    													<? $sew_out_wip = $total_sew_in-($total_sew_out+$exact_reject); echo number_format($sew_out_wip,0); ?>
			    												</a>
			    											</td>
			    											<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">
			    												<a href="javascript:void(0)" onclick="openProdPopup2('<? echo $po_id.'_'.$item_id.'_'.$color_id.'_'.$floor_id.'_'.$sewing_line.'_'.$prod_reso_allo.'_'.$txt_datefrom.'_'.$txt_dateto.'_'.'11_4_'.$job_no;?>');">
			    													<? echo number_format($prev_poly_qty,0); ?>
			    												</a>
			    											</td>
			    											<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">
			    												<a href="javascript:void(0)" onclick="openProdPopup2('<? echo $po_id.'_'.$item_id.'_'.$color_id.'_'.$floor_id.'_'.$sewing_line.'_'.$prod_reso_allo.'_'.$txt_datefrom.'_'.$txt_dateto.'_'.'11_1_'.$job_no;?>');">
			    													<? echo number_format($curr_poly_qty,0); ?>
			    												</a>
			    											</td>
			    											<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">
			    												<a href="javascript:void(0)" onclick="openProdPopup2('<? echo $po_id.'_'.$item_id.'_'.$color_id.'_'.$floor_id.'_'.$sewing_line.'_'.$prod_reso_allo.'_'.$txt_datefrom.'_'.$txt_dateto.'_'.'11_2_'.$job_no;?>');">
			    													<? $total_poly = $prev_poly_qty+$curr_poly_qty; echo number_format($total_poly,0); ?>
			    											</a>
			    											</td>
			    											<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">
			    												<a href="javascript:void(0)" onclick="openProdPopup2('<? echo $po_id.'_'.$item_id.'_'.$color_id.'_'.$floor_id.'_'.$sewing_line.'_'.$prod_reso_allo.'_'.$txt_datefrom.'_'.$txt_dateto.'_'.'11_3_'.$job_no;?>');">
			    													<? echo number_format($poly_rej,0); ?>
			    												</a>
			    											</td>
			    											<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">
			    												<a href="javascript:void(0)" onclick="openProdPopupWIP2('<? echo $po_id.'_'.$item_id.'_'.$color_id.'_'.$floor_id.'_'.$sewing_line.'_'.$prod_reso_allo.'_'.$txt_datefrom.'_'.$txt_dateto.'_'.'11_6_'.$job_no;?>');">
			    													<? $poly_wip = ($total_sew_out - $total_poly)-$poly_rej; echo number_format($poly_wip,0); ?>
			    												</a>
			    											</td>
			    											<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">
			    												<a href="javascript:void(0)" onclick="openProdPopupWIP2('<? echo $po_id.'_'.$item_id.'_'.$color_id.'_'.$floor_id.'_'.$sewing_line.'_'.$prod_reso_allo.'_'.$txt_datefrom.'_'.$txt_dateto.'_'.'11_7_'.$job_no;?>');">
			    													<? $input_to_poly_wip = ($total_sew_in - $total_poly)-$poly_rej; echo number_format($input_to_poly_wip,0); ?>
			    												</a>
			    											</td>
			    											<td style="word-wrap: break-word;word-break: break-all;" width="80"><? echo $shipment_status[$row['shiping_status']];?></td>
			    										</tr>
			    									<?

			    									/*if($l !=1)
			    									{
			    										if( !in_array($main_array[$job_id][$style][$buyer][$item_id][$color_id], $check_color))
			    										{
			    											$style_color_qty 		+= $color_qnty;
			    											$grnd_color_qty 		+= $color_qnty;
			    											$check_color[] = $main_array[$job_id][$style][$buyer][$item_id][$color_id];
			    										}

			    									}*/
			    									// $style_color_qty 		+= $color_qnty;
			    									$style_prev_sew_in_qty 	+= $prev_sew_in_qty;
							    					$style_curr_sew_in_qty 	+= $curr_sew_in_qty;
							    					$style_total_sew_in_qty += $total_sew_in;
							    					$style_prev_sew_out_qty += $prev_sew_out_qty;
							    					$style_curr_sew_out_qty += $curr_sew_out_qty;
							    					$style_total_sew_out_qty+= $total_sew_out;
							    					$style_sew_out_rej_qty 	+= $sew_out_rej_qty;
							    					$style_sew_wip_qty 		+= $sew_out_wip;
							    					$style_prev_poly_qty 	+= $prev_poly_qty;
							    					$style_curr_poly_qty 	+= $curr_poly_qty;
							    					$style_total_poly_qty 	+= $total_poly;
							    					$style_poly_rej_qty 	+= $poly_rej;
							    					$style_poly_wip_qty 	+= $poly_wip;
							    					$style_input_to_poly_wip+= $input_to_poly_wip;
							    					//==========================================
							    					//$grnd_color_qty 		+= $color_qnty;
			    									$grnd_prev_sew_in_qty 	+= $prev_sew_in_qty;
							    					$grnd_curr_sew_in_qty 	+= $curr_sew_in_qty;
							    					$grnd_total_sew_in_qty 	+= $total_sew_in;
							    					$grnd_prev_sew_out_qty 	+= $prev_sew_out_qty;
							    					$grnd_curr_sew_out_qty 	+= $curr_sew_out_qty;
							    					$grnd_total_sew_out_qty += $total_sew_out;
							    					$grnd_sew_out_rej_qty 	+= $sew_out_rej_qty;
							    					$grnd_sew_wip_qty 		+= $sew_out_wip;
							    					$grnd_prev_poly_qty 	+= $prev_poly_qty;
							    					$grnd_curr_poly_qty 	+= $curr_poly_qty;
							    					$grnd_total_poly_qty 	+= $total_poly;
							    					$grnd_poly_rej_qty 		+= $poly_rej;
							    					$grnd_poly_wip_qty 		+= $poly_wip;
							    					$grnd_input_to_poly_wip += $input_to_poly_wip;
							    					$sl++;
							    					}
			    								}
		    								}
	    								}
	    							}
			    					?>
			    					<tr class="gd-color">
			    						<td align="right" colspan="7">Style Total </td>
			    						<td align="right"><? echo number_format($po_qty_arr_style[$style],0); ?></td>
			    						<td align="right"></td>
			    						<td align="right"></td>
			    						<td align="right"><? echo number_format($style_prev_sew_in_qty,0); ?></td>
			    						<td align="right"><? echo number_format($style_curr_sew_in_qty,0); ?></td>
			    						<td align="right"><? echo number_format($style_total_sew_in_qty,0); ?></td>
			    						<td align="right"><? echo number_format($style_prev_sew_out_qty,0); ?></td>
			    						<td align="right"><? echo number_format($style_curr_sew_out_qty,0); ?></td>
			    						<td align="right"><? echo number_format($style_total_sew_out_qty,0); ?></td>
			    						<td align="right"><? echo number_format($style_sew_out_rej_qty,0); ?></td>
			    						<td align="right"><? echo number_format($style_sew_wip_qty,0); ?></td>
			    						<td align="right"><? echo number_format($style_prev_poly_qty,0); ?></td>
			    						<td align="right"><? echo number_format($style_curr_poly_qty,0); ?></td>
			    						<td align="right"><? echo number_format($style_total_poly_qty,0); ?></td>
			    						<td align="right"><? echo number_format($style_poly_rej_qty,0); ?></td>
			    						<td align="right"><? echo number_format($style_poly_wip_qty,0); ?></td>
			    						<td align="right"><? echo number_format($style_input_to_poly_wip,0); ?></td>
			    						<td align="right"></td>
			    					</tr>
			    					<?
			    					$grnd_color_qty += $po_qty_arr_style[$style];
		    					}
		    				}
		    			}
		    			?>
		    		</table>
		    	</div>
		    	<div style="width: 2070px;"">
		    		<table cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" width="2050">
		    			<tfoot>
		    				<tr class="gd-color3">
		    					<td width="30"></td>
		    					<td width="100"></td>
		    					<td width="100"></td>
		    					<td width="100"></td>
		    					<td width="70"></td>
		    					<td width="100"></td>
								<td width="90" align="right">Grand Total </td>
	    						<td width="80" align="right"><? echo number_format($grnd_color_qty,0); ?></td>
	    						<td width="100" align="right"></td>
	    						<td width="80" align="right"></td>
	    						<td width="80" align="right"><? echo number_format($grnd_prev_sew_in_qty,0); ?></td>
	    						<td width="80" align="right"><? echo number_format($grnd_curr_sew_in_qty,0); ?></td>
	    						<td width="80" align="right"><? echo number_format($grnd_total_sew_in_qty,0); ?></td>
	    						<td width="80" align="right"><? echo number_format($grnd_prev_sew_out_qty,0); ?></td>
	    						<td width="80" align="right"><? echo number_format($grnd_curr_sew_out_qty,0); ?></td>
	    						<td width="80" align="right"><? echo number_format($grnd_total_sew_out_qty,0); ?></td>
	    						<td width="80" align="right"><? echo number_format($grnd_sew_out_rej_qty,0); ?></td>
	    						<td width="80" align="right"><? echo number_format($grnd_sew_wip_qty,0); ?></td>
	    						<td width="80" align="right"><? echo number_format($grnd_prev_poly_qty,0); ?></td>
	    						<td width="80" align="right"><? echo number_format($grnd_curr_poly_qty,0); ?></td>
	    						<td width="80" align="right"><? echo number_format($grnd_total_poly_qty,0); ?></td>
	    						<td width="80" align="right"><? echo number_format($grnd_poly_rej_qty,0); ?></td>
	    						<td width="80" align="right"><? echo number_format($grnd_poly_wip_qty,0); ?></td>
	    						<td width="80" align="right"><? echo number_format($grnd_input_to_poly_wip,0); ?></td>
	    						<td width="80" align="right"></td>
							</tr>
		    			</tfoot>
		    		</table>
		    </div>
	    </div>
	   <?
	}

	elseif($type==3)
	{
		// ================================================ MAIN QUERY ==================================================
		$sql="SELECT  a.buyer_name,a.client_id as buyer_client,a.style_ref_no as style,a.job_no as job_id,b.id as po_id,b.grouping,b.po_number,b.shiping_status, c.country_id,c.country_ship_date,c.item_number_id,c.color_number_id,d.floor_id,d.sewing_line,d.prod_reso_allo,c.order_rate
		from  wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, pro_garments_production_mst d,pro_garments_production_dtls e
		where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and b.id=d.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id $sql_cond $sql_cond2 $po_ids_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 and d.sewing_line<>0
		order by a.job_no,b.id,c.color_number_id";
		// echo $sql;
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
				  <strong>Data not found.</strong>
				</div>
			</div>
			<?
			die();
		}

		$main_array = array();
		$item_wise_rate_arr=array();
		$poId_arr = array();
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

				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['country_ship_date'] = $row[csf('country_ship_date')];
				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['po_number'] = $row[csf('po_number')];
				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['grouping'] = $row[csf('grouping')];
				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['shiping_status'] = $row[csf('shiping_status')];
				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['sewing_line'] = $row[csf('sewing_line')];
				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['prod_reso_allo'] = $row[csf('prod_reso_allo')];
				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['buyer_client'] = $row[csf('buyer_client')];
				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['order_rate'] = $row[csf('order_rate')];
				$item_wise_rate_arr[$row[csf('item_number_id')]]['order_rate'] = $row[csf('order_rate')];
			}
			else
			{
				$line_name=$lineArr[$row[csf('sewing_line')]];
				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['country_ship_date'] = $row[csf('country_ship_date')];
				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['po_number'] = $row[csf('po_number')];
				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['grouping'] = $row[csf('grouping')];
				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['shiping_status'] = $row[csf('shiping_status')];
				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['sewing_line'] = $row[csf('sewing_line')];
				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['prod_reso_allo'] = $row[csf('prod_reso_allo')];
				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['buyer_client'] = $row[csf('buyer_client')];
				$main_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['order_rate'] = $row[csf('order_rate')];
				$item_wise_rate_arr[$row[csf('item_number_id')]]['order_rate'] = $row[csf('order_rate')];
			}
			$poId_arr[$row[csf('po_id')]] = $row[csf('po_id')];
		}
		$all_po_id = implode(",", $poId_arr);
		if(count($poId_arr)>999 && $db_type==2)
	    {
	     	$po_chunk=array_chunk($poId_arr, 999);
	     	$prod_po_ids_cond= "";
	     	$prod_po_ids_cond2= "";
	     	foreach($po_chunk as $vals)
	     	{
	     		$imp_ids=implode(",", $vals);
	     		if($prod_po_ids_cond=="")
	     		{
	     			$prod_po_ids_cond.=" and ( b.id in ($imp_ids) ";
	     			$prod_po_ids_cond2.=" and ( c.po_break_down_id in ($imp_ids) ";
	     		}
	     		else
	     		{
	     			$prod_po_ids_cond.=" or b.id in ($imp_ids) ";
					 $prod_po_ids_cond2.=" or ( c.po_break_down_id in ($imp_ids) ";
	     		}
	     	}
	     	 $prod_po_ids_cond.=" )";
	     	 $prod_po_ids_cond2 .=" )";
	    }
	    else
	    {
	     	$prod_po_ids_cond= " and b.id in($all_po_id) ";
	     	$prod_po_ids_cond2= " and c.po_break_down_id in($all_po_id) ";
	    }
		// echo "<pre>";
		// print_r($main_array);
		// echo "</pre>";

		// ================================================ PRODUCTION QUERY ==================================================
		$prod_sql="SELECT  a.buyer_name,a.style_ref_no as style,a.job_no as job_id,b.id as po_id,c.country_id,c.item_number_id,c.color_number_id,d.floor_id,d.sewing_line,d.prod_reso_allo,

		sum(case when d.production_type=4 and d.production_type=e.production_type and d.production_date<'$txt_datefrom' then e.production_qnty else 0 end ) as prev_sewing_input,
		sum(case when d.production_type=4 and d.production_type=e.production_type and d.production_date between '$txt_datefrom' and '$txt_dateto' then e.production_qnty else 0 end ) as curr_sewing_input,

		sum(case when d.production_type=5 and d.production_type=e.production_type and d.production_date<'$txt_datefrom' then e.production_qnty else 0 end ) as prev_sewing_output,
		sum(case when d.production_type=5 and d.production_type=e.production_type and d.production_date between '$txt_datefrom' and '$txt_dateto' then e.production_qnty else 0 end ) as curr_sewing_output,
		sum(case when d.production_type=5 and d.production_type=e.production_type and e.is_rescan=0  then e.reject_qty else 0 end ) as sewing_out_rej,

		sum(CASE WHEN d.production_type =5 and d.production_type=e.production_type and e.is_rescan>0 THEN e.reject_qty else 0 END) AS resc_rej_qty,

		sum(case when d.production_type=5 and d.production_type=e.production_type and e.is_rescan=0  then e.reject_qty else 0 end ) -

		sum(CASE WHEN d.production_type =5 and d.production_type=e.production_type and e.is_rescan=1 THEN e.production_qnty else 0 END) AS exact_reject,


		sum(case when d.production_type=11 and d.production_type=e.production_type and d.production_date<'$txt_datefrom' then e.production_qnty else 0 end ) as prev_poly_qty,
		sum(case when d.production_type=11 and d.production_type=e.production_type and d.production_date between '$txt_datefrom' and '$txt_dateto' then e.production_qnty else 0 end ) as curr_poly_qty,
		sum(case when d.production_type=11 and d.production_type=e.production_type then e.reject_qty else 0 end ) as poly_rej

		from  wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, pro_garments_production_mst d,pro_garments_production_dtls e
		where a.id=b.job_id and b.id=c.po_break_down_id and b.id=d.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id $prod_po_ids_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 and d.sewing_line<>0
		group by a.buyer_name,a.style_ref_no,a.job_no,b.id,c.country_id,c.item_number_id,c.color_number_id,d.floor_id,d.sewing_line,d.prod_reso_allo
		order by a.job_no,b.id,c.color_number_id";
		// echo $prod_sql;
		$prod_sql_res = sql_select($prod_sql);
		$item_array=array();

		$prod_array = array();
		foreach ($prod_sql_res as $row)
		{
			if($row[csf("prod_reso_allo")]==1)
		    {
		    	$line_resource_mst_arr=explode(",",$prod_reso_arr[$row[csf('sewing_line')]]);
		    	$line_name="";
				foreach($line_resource_mst_arr as $resource_id)
				{
					$line_name .= ($line_name == "") ? $lineArr[$resource_id] : ",".$lineArr[$resource_id];
				}

				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['prev_sewing_input'] += $row[csf('prev_sewing_input')];
				$item_array[$row[csf('item_number_id')]]['prev_sewing_input']+= $row[csf('prev_sewing_input')];

				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['exact_reject'] += $row[csf('exact_reject')];
				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['curr_sewing_input'] += $row[csf('curr_sewing_input')];

				$item_array[$row[csf('item_number_id')]]['curr_sewing_input']+= $row[csf('curr_sewing_input')];

				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['prev_sewing_output'] += $row[csf('prev_sewing_output')];

				$item_array[$row[csf('item_number_id')]]['prev_sewing_output']+= $row[csf('prev_sewing_output')];

				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['curr_sewing_output'] += $row[csf('curr_sewing_output')];

				$item_array[$row[csf('item_number_id')]]['curr_sewing_output']+= $row[csf('curr_sewing_output')];

				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['sewing_out_rej'] += $row[csf('sewing_out_rej')];

				$item_array[$row[csf('item_number_id')]]['sewing_out_rej']+= $row[csf('sewing_out_rej')];

				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['resc_qcpass'] += $row[csf('resc_rej_qty')];

				$item_array[$row[csf('item_number_id')]]['resc_qcpass']+= $row[csf('resc_rej_qty')];

				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['poly_rej'] += $row[csf('poly_rej')];
			}
			else
			{
				$line_name=$lineArr[$row[csf('sewing_line')]];
				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['prev_sewing_input'] += $row[csf('prev_sewing_input')];
				$item_array[$row[csf('item_number_id')]]['prev_sewing_input']+= $row[csf('prev_sewing_input')];

				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['curr_sewing_input'] += $row[csf('curr_sewing_input')];

				$item_array[$row[csf('item_number_id')]]['curr_sewing_input']+= $row[csf('curr_sewing_input')];

				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['prev_sewing_output'] += $row[csf('prev_sewing_output')];

				$item_array[$row[csf('item_number_id')]]['prev_sewing_output']+= $row[csf('prev_sewing_output')];

				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['curr_sewing_output'] += $row[csf('curr_sewing_output')];

				$item_array[$row[csf('item_number_id')]]['curr_sewing_output']+= $row[csf('curr_sewing_output')];

				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['sewing_out_rej'] += $row[csf('sewing_out_rej')];

				$item_array[$row[csf('item_number_id')]]['sewing_out_rej']+= $row[csf('sewing_out_rej')];



				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['resc_qcpass'] += $row[csf('resc_rej_qty')];

				$item_array[$row[csf('item_number_id')]]['resc_qcpass']+= $row[csf('resc_rej_qty')];


				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['poly_rej'] += $row[csf('poly_rej')];
				$prod_array[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('floor_id')]][$line_name]['exact_reject'] += $row[csf('exact_reject')];
			}
		}
		// echo "<pre>";
		// print_r($prod_array);
		// echo "</pre>";

		// ======================================= SEW LOSS QNTY ============================================
		$sql_cond2 	="";
		$sql_cond2 .= ($location_id=="") 		? "" : " and c.location in($location_id)";
		$sql_cond2 .= ($floor_id=="") 			? "" : " and c.floor_id in($floor_id)";
		$sql_cond2 .= ($line_id=="") 			? "" : " and c.sewing_line in($line_id)";
		$sql_cond2 .= ($lc_company_id==0) 		? "" : " and c.company_id=$lc_company_id";

		$prod_defect_sql = "SELECT distinct a.id,a.defect_qty, c.po_break_down_id as po_id, c.item_number_id,c.floor_id,c.sewing_line,c.prod_reso_allo,c.country_id,e.color_number_id
		from pro_gmts_prod_dft a, pro_garments_production_mst c,pro_garments_production_dtls d,wo_po_color_size_breakdown e
		where a.mst_id=c.id and c.id=d.mst_id and c.po_break_down_id=e.po_break_down_id and e.id=d.color_size_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sql_cond2 $prod_po_ids_cond2  and a.defect_point_id=50 and a.defect_type_id=2 and a.production_type = 5";
		// echo $prod_defect_sql ; die;
		$gmts_prod_defect_arr = array();
		$item_wise_defect_arr=array();
		$res_gmtsDefectData = sql_select($prod_defect_sql);
		foreach($res_gmtsDefectData as $v)
		{
			if($v["PROD_RESO_ALLO"]==1)
		    {
		    	$line_resource_mst_arr=explode(",",$prod_reso_arr[$v['SEWING_LINE']]);
		    	$line_name="";
				foreach($line_resource_mst_arr as $resource_id)
				{
					$line_name .= ($line_name == "") ? $lineArr[$resource_id] : ",".$lineArr[$resource_id];
				}
			}else{
				$line_name=$lineArr[$v['SEWING_LINE']];
			}
			$gmts_prod_defect_arr[$v['PO_ID']][$v['COUNTRY_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']][$v['FLOOR_ID']][$line_name]['defect_qty'] += $v['DEFECT_QTY'];
			$item_wise_defect_arr[$v['ITEM_NUMBER_ID']]['defect_qty'] += $v['DEFECT_QTY'];
		}

		// ======================================= FOR COLOR QNTY ============================================
		$color_sql="SELECT  a.buyer_name,a.style_ref_no as style,a.job_no as job_id,b.id as po_id, c.country_id,c.item_number_id,c.color_number_id, sum(c.order_quantity) as color_qnty
		from  wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c
		where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and a.status_active=1 and b.status_active=1 and c.status_active=1 $prod_po_ids_cond
		group by a.buyer_name,a.style_ref_no,a.job_no,b.id, c.country_id,c.item_number_id,c.color_number_id";
		$color_sql_res = sql_select($color_sql);
		$color_qnty_arr = array();
		foreach ($color_sql_res as $row)
		{
			$color_qnty_arr[$row[csf('job_id')]][$row[csf('style')]][$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]] = $row[csf('color_qnty')];
		}
		// echo "<pre>";
		// print_r($color_qnty_arr);
		// echo "</pre>";
		$check_po_color = array();
		$po_qty_arr = array();
		foreach ($main_array as $job_key => $job_arr)
		{
			foreach ($job_arr as $style_key => $style_arr)
			{
				foreach ($style_arr as $buyer_key => $buyer_arr)
				{
					foreach ($buyer_arr as $po_key => $po_arr)
					{
						foreach ($po_arr as $country_key => $country_arr)
						{
							foreach ($country_arr as $item_key => $item_arr)
							{
								foreach ($item_arr as $color_key => $color_arr)
								{
									if(!in_array($color_key, $check_po_color[$job_key][$style_key][$buyer_key][$po_key][$country_key][$item_key][$color_key]))
									{
										$po_qty_arr[$style_key] += $color_qnty_arr[$job_key][$style_key][$buyer_key][$po_key][$country_key][$item_key][$color_key];
									}
									$check_po_color[] = $main_array[$job_key][$style_key][$buyer_key][$po_key][$country_key][$item_key][$color_key];

								}
							}
						}
					}
				}
			}
		}

		// echo "<pre>";
		// print_r($po_qty_arr);
		// echo "</pre>";
		// die();


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
			<table width="100%" cellspacing="0">
		        <tr class="form_caption" style="border:none;">
		            <td colspan="9" align="center" ><font size="3"><strong><u><? echo $company_details[$lc_company_id]; ?></u></strong></font></td>
		        </tr>
		        <tr class="form_caption" style="border:none;">
		            <td colspan="9" align="center"><font size="2"><strong>Style and Line Wise Production Report</strong></font></td>
		        </tr>
		    </table>
		    <div>
		    	<table class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" id="" width="2080">
		    		<thead>
		    			<th width="30">Sl.</th>
		    			<th width="100">Buyer Name</th>
		    			<th width="100">Buyer Client</th>
		    			<th width="100">Style Ref.</th>
		    			<th width="100">Job No.</th>
		    			<th width="70">Int.Ref</th>
		    			<th width="100">PO No.</th>
		    			<th width="100">Country Name</th>
		    			<th width="80">C. Ship Date</th>
		    			<th width="100">Garments Item</th>
		    			<th width="100">Gmts Rate</th>
		    			<th width="90">Color Name</th>
		    			<th width="80">Color Qty(pcs)</th>
		    			<th width="100">Floor Name</th>
		    			<th width="80">Line Name</th>
		    			<th width="80">Prev. Input</th>
		    			<th width="80">Cur. Input</th>
		    			<th width="80">Total Input</th>
		    			<th width="80">Prev. S.output</th>
		    			<th width="80">Cur. S.output</th>
		    			<th width="80">Total Output</th>
		    			<th width="80">Total S.Reject</th>
		    			<th width="80">Sew Loss</th>
		    			<th width="80">Sew WIP</th>
		    			<th width="100">Amount</th>
		    			<th width="80">Remarks</th>
		    		</thead>
		    	</table>
		    	<div style="width: 2100px; overflow-y: scroll; max-height: 400px" id="scroll_body">
		    		<table cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all" width="2080" id="html_search">
		    			<?
		    			$check_color			= array();
		    			$sl=1;

		    			$grnd_color_qty 		= 0;
						$grnd_prev_sew_in_qty 	= 0;
						$grnd_curr_sew_in_qty 	= 0;
						$grnd_total_sew_in_qty 	= 0;
						$grnd_prev_sew_out_qty 	= 0;
						$grnd_curr_sew_out_qty 	= 0;
						$grnd_total_sew_out_qty = 0;
						$grnd_sew_out_rej_qty 	= 0;
						$grnd_sew_loss_qty 		= 0;
						$grnd_sew_wip_qty 		= 0;
						$grnd_prev_poly_qty 	= 0;
						$grnd_curr_poly_qty 	= 0;
						$grnd_total_poly_qty 	= 0;
						$grnd_poly_rej_qty 		= 0;
						$grnd_poly_wip_qty 		= 0;
						$grnd_in_to_poly_wip 	= 0;
						// pre($gmts_prod_defect_arr);

		    			foreach($main_array as $job_id=>$job_data)
		    			{
		    				foreach ($job_data as $style => $style_data)
		    				{
		    					$style_color_qty 		= 0;
		    					$style_prev_sew_in_qty 	= 0;
		    					$style_curr_sew_in_qty 	= 0;
		    					$style_total_sew_in_qty = 0;
		    					$style_prev_sew_out_qty = 0;
		    					$style_curr_sew_out_qty = 0;
		    					$style_total_sew_out_qty= 0;
		    					$style_sew_out_rej_qty 	= 0;
		    					$style_sew_wip_qty 		= 0;
		    					$style_sew_loss_qty 	= 0;
								$style_amount           =0;

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
			    									foreach ($color_data as $floor_id => $floor_data)
			    									{
			    										foreach ($floor_data as $line_name => $row)
			    										{
															$prod_arr = $prod_array[$job_id][$style][$buyer][$po_id][$country_id][$item_id][$color_id][$floor_id][$line_name] ;
			    											$color_qnty = $color_qnty_arr[$job_id][$style][$buyer][$po_id][$country_id][$item_id][$color_id];
			    											$prev_sew_in_qty = $prod_arr['prev_sewing_input'];

															$sew_loss_qty = $gmts_prod_defect_arr[$po_id][$country_id][$item_id][$color_id][$floor_id][$line_name]['defect_qty']??0;


			    											$curr_sew_in_qty = $prod_arr['curr_sewing_input'];
			    											$prev_sew_out_qty = $prod_arr['prev_sewing_output'];
			    											$curr_sew_out_qty = $prod_arr['curr_sewing_output'];
			    											$sew_out_rej_qty = $prod_arr['sewing_out_rej'];
			    											$resc_rej_qty 		= $prod_arr['resc_rej_qty'];
			    											$resc_qcpass_qty = $prod_arr['resc_qcpass'];
			    											$exact_reject = $sew_out_rej_qty - $sew_loss_qty + $resc_qcpass_qty;
			    											$sew_out_rej_qty = $exact_reject;


			    											$prev_poly_qty = $prod_array[$job_id][$style][$buyer][$po_id][$country_id][$item_id][$color_id][$floor_id][$line_name]['prev_poly_qty'];
			    											$curr_poly_qty = $prod_array[$job_id][$style][$buyer][$po_id][$country_id][$item_id][$color_id][$floor_id][$line_name]['curr_poly_qty'];
			    											$poly_rej = $prod_array[$job_id][$style][$buyer][$po_id][$country_id][$item_id][$color_id][$floor_id][$line_name]['poly_rej'];
			    											$sewing_line = $row['sewing_line'];
			    											$prod_reso_allo = $row['prod_reso_allo'];
			    											if($prev_sew_in_qty !=0 || $prev_sew_out_qty !=0 || $prev_poly_qty !=0 || $curr_sew_in_qty !=0 || $curr_sew_out_qty !=0 || $curr_poly_qty !=0){
				    										$bgcolor = ($sl%2==0) ? "#ffffff" : "#f6faff";
					    									?>
					    										<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_1nd<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $sl; ?>" >
					    											<td style="word-wrap: break-word;word-break: break-all;" width="30"><? echo $sl;?></td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $buyerArr[$buyer]; ?></td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $buyerArr[$row['buyer_client']]; ?></td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $style;?></td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $job_id;?></td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="70"><? echo $row['grouping'];?></td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $row['po_number'];?></td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $countryArr[$country_id];?></td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="80"><? echo change_date_format($row['country_ship_date']); ?></td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $garments_item[$item_id]; ?></td>
																	<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo fn_number_format($row['order_rate'],4); ?></td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="90"><? echo $colorArr[$color_id]; ?></td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($color_qnty,0); ?></td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $floorArr[$floor_id]; ?></td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="80"><? echo $line_name; ?></td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">
					    												<a href="javascript:void(0)" onclick="openProdPopup('<? echo $po_id.'_'.$country_id.'_'.$item_id.'_'.$color_id.'_'.$floor_id.'_'.$sewing_line.'_'.$prod_reso_allo.'_'.$txt_datefrom.'_'.$txt_dateto.'_'.'4_4';?>');">
					    													<? echo number_format($prev_sew_in_qty,0); ?>
					    												</a>
					    											</td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">
					    												<a href="javascript:void(0)" onclick="openProdPopup('<? echo $po_id.'_'.$country_id.'_'.$item_id.'_'.$color_id.'_'.$floor_id.'_'.$sewing_line.'_'.$prod_reso_allo.'_'.$txt_datefrom.'_'.$txt_dateto.'_'.'4_1';?>');">
					    													<? echo number_format($curr_sew_in_qty,0); ?>
					    												</a>
					    											</td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">
					    												<a href="javascript:void(0)" onclick="openProdPopup('<? echo $po_id.'_'.$country_id.'_'.$item_id.'_'.$color_id.'_'.$floor_id.'_'.$sewing_line.'_'.$prod_reso_allo.'_'.$txt_datefrom.'_'.$txt_dateto.'_'.'4_2';?>');">
					    													<? $total_sew_in = $prev_sew_in_qty+$curr_sew_in_qty; echo number_format($total_sew_in,0); ?>
					    												</a>

					    											</td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">
					    												<a href="javascript:void(0)" onclick="openProdPopup('<? echo $po_id.'_'.$country_id.'_'.$item_id.'_'.$color_id.'_'.$floor_id.'_'.$sewing_line.'_'.$prod_reso_allo.'_'.$txt_datefrom.'_'.$txt_dateto.'_'.'5_4';?>');">
					    													<? echo number_format($prev_sew_out_qty,0); ?>
					    												</a>
					    											</td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">
					    												<a href="javascript:void(0)" onclick="openProdPopup('<? echo $po_id.'_'.$country_id.'_'.$item_id.'_'.$color_id.'_'.$floor_id.'_'.$sewing_line.'_'.$prod_reso_allo.'_'.$txt_datefrom.'_'.$txt_dateto.'_'.'5_1';?>');">
					    													<? echo number_format($curr_sew_out_qty,0); ?>
					    												</a>
					    											</td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">
					    												<a href="javascript:void(0)" onclick="openProdPopup('<? echo $po_id.'_'.$country_id.'_'.$item_id.'_'.$color_id.'_'.$floor_id.'_'.$sewing_line.'_'.$prod_reso_allo.'_'.$txt_datefrom.'_'.$txt_dateto.'_'.'5_2';?>');">
					    													<? $total_sew_out = $prev_sew_out_qty+$curr_sew_out_qty; echo number_format($total_sew_out,0); ?>
					    												</a>
					    											</td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">
					    												<a href="javascript:void(0)" onclick="openProdPopup('<? echo $po_id.'_'.$country_id.'_'.$item_id.'_'.$color_id.'_'.$floor_id.'_'.$sewing_line.'_'.$prod_reso_allo.'_'.$txt_datefrom.'_'.$txt_dateto.'_'.'5_3';?>');">
					    													<?  echo number_format($exact_reject,0); ?>
					    												</a>
					    											</td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">
					    													<? echo number_format($sew_loss_qty,0); ?>

					    											</td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right" title="Total Input-(Total Output+Total S.Reject+Sew Loss)">
					    												<a href="javascript:void(0)" onclick="openProdPopupWIP('<? echo $po_id.'_'.$country_id.'_'.$item_id.'_'.$color_id.'_'.$floor_id.'_'.$sewing_line.'_'.$prod_reso_allo.'_'.$txt_datefrom.'_'.$txt_dateto.'_'.'5_5';?>');">
					    													<? $sew_out_wip = $total_sew_in-($total_sew_out+$exact_reject+$sew_loss_qty); echo number_format($sew_out_wip,0); ?>
					    												</a>
					    											</td>
																	<td style="word-wrap: break-word;word-break: break-all;" width="100" align="right"><? $amount=$row['order_rate']*$sew_out_wip; echo fn_number_format($amount,2); ?></td>
					    											<td style="word-wrap: break-word;word-break: break-all;" width="80"><? echo $shipment_status[$row['shiping_status']];?></td>
					    										</tr>
					    									<?
					    									/*if($l !=1)
					    									{
					    										if( !in_array($main_array[$job_id][$style][$buyer][$po_id][$country_id][$item_id][$color_id], $check_color))
					    										{
					    											$style_color_qty 		+= $color_qnty;
					    											$grnd_color_qty 		+= $color_qnty;
					    											$check_color[] = $main_array[$job_id][$style][$buyer][$po_id][$country_id][$item_id][$color_id];
					    										}

					    									}*/
					    									// $style_color_qty		+= $po_qty_arr[$style];
					    									$style_prev_sew_in_qty 	+= $prev_sew_in_qty;
									    					$style_curr_sew_in_qty 	+= $curr_sew_in_qty;
									    					$style_total_sew_in_qty += $total_sew_in;
									    					$style_prev_sew_out_qty += $prev_sew_out_qty;
									    					$style_curr_sew_out_qty += $curr_sew_out_qty;
									    					$style_total_sew_out_qty+= $total_sew_out;
									    					$style_sew_out_rej_qty 	+= $sew_out_rej_qty;
									    					$style_sew_wip_qty 		+= $sew_out_wip;
									    					$style_sew_loss_qty 	+= $sew_loss_qty;
															$style_amount+=$amount;

									    					//==========================================
									    					// $grnd_color_qty			+= $po_qty_arr[$style];
					    									$grnd_prev_sew_in_qty 	+= $prev_sew_in_qty;
									    					$grnd_curr_sew_in_qty 	+= $curr_sew_in_qty;
									    					$grnd_total_sew_in_qty 	+= $total_sew_in;
									    					$grnd_prev_sew_out_qty 	+= $prev_sew_out_qty;
									    					$grnd_curr_sew_out_qty 	+= $curr_sew_out_qty;
									    					$grnd_total_sew_out_qty += $total_sew_out;
									    					$grnd_sew_out_rej_qty 	+= $sew_out_rej_qty;
									    					$grnd_sew_wip_qty 		+= $sew_out_wip;
															$grnd_sew_loss_qty 		+= $sew_loss_qty;
															$grand_amount+=$amount;

									    					$sl++;
									    					}
					    								}
				    								}
			    								}
			    							}
			    						}
			    					}
			    					?>
			    					<tr class="gd-color">
			    						<td align="right" colspan="12">Style Total </td>
			    						<td align="right"><? echo number_format($po_qty_arr[$style],0); ?></td>
			    						<td align="right"></td>
			    						<td align="right"></td>
			    						<td align="right"><? echo number_format($style_prev_sew_in_qty,0); ?></td>
			    						<td align="right"><? echo number_format($style_curr_sew_in_qty,0); ?></td>
			    						<td align="right"><? echo number_format($style_total_sew_in_qty,0); ?></td>
			    						<td align="right"><? echo number_format($style_prev_sew_out_qty,0); ?></td>
			    						<td align="right"><? echo number_format($style_curr_sew_out_qty,0); ?></td>
			    						<td align="right"><? echo number_format($style_total_sew_out_qty,0); ?></td>
			    						<td align="right"><? echo number_format($style_sew_out_rej_qty,0); ?></td>
			    						<td align="right"><? echo number_format($style_sew_loss_qty,0); ?></td>
			    						<td align="right"><? echo number_format($style_sew_wip_qty,0); ?></td>
										<td align="right"><? echo number_format($style_amount,0); ?></td>
			    						<td align="right"></td>
			    					</tr>
			    					<?
			    					$grnd_color_qty += $po_qty_arr[$style];
		    					}
		    				}
		    			}
		    			?>
		    		</table>
		    	</div>
		    	<div style="width:2100px;"">
		    		<table cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" width="2080">
		    			<tfoot>
		    				<tr class="gd-color3">
		    					<td width="30"></td>
		    					<td width="100"></td>
		    					<td width="100"></td>
		    					<td width="100"></td>
		    					<td width="100"></td>
		    					<td width="100"></td>
		    					<td width="70"></td>
		    					<td width="100"></td>
		    					<td width="100"></td>
		    					<td width="80"></td>
		    					<td width="100"></td>
								<td width="90" align="right">Grand Total </td>
	    						<td width="80" align="right"><? echo number_format($grnd_color_qty,0); ?></td>
	    						<td width="100" align="right"></td>
	    						<td width="80" align="right"></td>
	    						<td width="80" align="right"><? echo number_format($grnd_prev_sew_in_qty,0); ?></td>
	    						<td width="80" align="right"><? echo number_format($grnd_curr_sew_in_qty,0); ?></td>
	    						<td width="80" align="right"><? echo number_format($grnd_total_sew_in_qty,0); ?></td>
	    						<td width="80" align="right"><? echo number_format($grnd_prev_sew_out_qty,0); ?></td>
	    						<td width="80" align="right"><? echo number_format($grnd_curr_sew_out_qty,0); ?></td>
	    						<td width="80" align="right"><? echo number_format($grnd_total_sew_out_qty,0); ?></td>
	    						<td width="80" align="right"><? echo number_format($grnd_sew_out_rej_qty,0); ?></td>
	    						<td width="80" align="right"><? echo number_format($grnd_sew_loss_qty,0); ?></td>
	    						<td width="80" align="right"><? echo number_format($grnd_sew_wip_qty,0); ?></td>
								<td width="100" align="right"><? echo number_format($grand_amount,0); ?></td>
	    						<td width="80" align="right"></td>
							</tr>
		    			</tfoot>
		    		</table>
		    </div>
			   <br>
			     <h1><b>Garments Item Wise Summary</b> </h1>
				 <div style="width:820px">
				 <table class="rpt_table" width="800" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
                          <tr>
						    <th width="100">Garments Item</th>
							<th width="100">Total Input</th>
							<th  width="100">Total Output</th>
							<th width="100">Total S.Reject</th>
							<th width="100">Sew Loss </th>
							<th width="100">Sew Wip</th>
							<th width="100">Amount</th>
							<th width="100">Avg.Rate($)</th>

						  </tr>
				  </thead>
				  <tbody>
				  <?

				  $i=1;
				  foreach($item_array as $item_id=>$val)
				  {
						if ($i%2==0)
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";
					//   $gr_item_wise_sewing_input=0;
					//   $gr_item_wise_sewing_output=0;
					//   $gr_item_wise_extact_reject=0;
					//   $gr_item_wise_sew_loss=0;
					//   $gr_item_wise_sew_wip=0;
					//   $gr_item_wise_rate=0;
					//   $gr_item_wise_amount=0;

					?>
					  <tr bgcolor="<?=$bgcolor;?>">
					    <td><? echo $garments_item[$item_id];?></td>
					    <td align="right"><? $item_wise_total_sewing_input=$val['prev_sewing_input']+$val['curr_sewing_input'];echo $item_wise_total_sewing_input; ?></td>
					    <td align="right"><? $item_wise_total_sewing_output=$val['prev_sewing_output']+$val['curr_sewing_output']; echo $item_wise_total_sewing_output;
						?></td>
					    <td align="right"><? $item_wise_exact_reject=$val['sewing_out_rej']-$item_wise_defect_arr[$item_id]['defect_qty']+$val['resc_qcpass']; echo $item_wise_exact_reject;?></td>
					    <td align="right"><? echo $item_wise_defect_arr[$item_id]['defect_qty']; ?></td>
					    <td align="right"><? $item_wise_sew_wip=$item_wise_total_sewing_input-($item_wise_total_sewing_output+$item_wise_exact_reject+$item_wise_defect_arr[$item_id]['defect_qty']); echo $item_wise_sew_wip;?></td>
						<td align="right"><? $item_wise_amount=$item_wise_sew_wip*$item_wise_rate_arr[$item_id]['order_rate'];  echo $item_wise_amount;?></td>
					    <td align="right" title="Amount/Sew Wip"><? $item_wise_avg_rate=$item_wise_amount/$item_wise_sew_wip; echo $item_wise_avg_rate;?></td>


					  </tr>
					<?
					  $gr_item_wise_sewing_input+=$item_wise_total_sewing_input;
					  $gr_item_wise_sewing_output+=$item_wise_total_sewing_output;
					  $gr_item_wise_extact_reject+=$item_wise_exact_reject;
					  $gr_item_wise_sew_loss+=$item_wise_defect_arr[$item_id]['defect_qty'];
					  $gr_item_wise_sew_wip+=$item_wise_sew_wip;
					  $gr_item_wise_amount+=$item_wise_amount;
					  $gr_item_wise_rate+=$item_wise_avg_rate;
				  }
                 ?>
				  </tbody>
				  <tfoot>
                      <th>Total</th>
                      <th><? echo $gr_item_wise_sewing_input;?></th>
                      <th><? echo $gr_item_wise_sewing_output;?></th>
                      <th><? echo $gr_item_wise_extact_reject; ?></th>
                      <th><? echo $gr_item_wise_sew_loss;?></th>
                      <th><? echo $gr_item_wise_sew_wip;?></th>
                      <th><? echo $gr_item_wise_amount;?></th>
					  <th><??></th>
				  </tfoot>
				 </table>
				 <div>
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
	$defect_arr = array();
	switch ($level)
	{
		case 1:
			$sql = "SELECT c.color_number_id,c.size_number_id,sum(b.production_qnty) as production_qnty,a.production_date from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c WHERE a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and a.po_break_down_id=$po_id and c.item_number_id=$item_id and c.country_id=$country_id and c.color_number_id=$color_id and a.production_date between '$date_from' and '$date_to' and a.production_type=$type and a.sewing_line in($sewing_line_id) and a.status_active=1 and b.status_active=1 and c.status_active=1
			group by c.color_number_id,c.size_number_id,a.production_date order by c.size_number_id";
			break;

		case 2:
			$sql = "SELECT c.color_number_id,c.size_number_id,sum(b.production_qnty) as production_qnty,a.production_date from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c WHERE a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and a.po_break_down_id=$po_id and c.item_number_id=$item_id and c.country_id=$country_id and c.color_number_id=$color_id and a.production_date<='$date_to' and a.production_type=$type and a.sewing_line in($sewing_line_id) and a.status_active=1 and b.status_active=1 and c.status_active=1
			group by c.color_number_id,c.size_number_id,a.production_date order by c.size_number_id";
			break;
		case 3:
			/* $sql = "SELECT c.color_number_id,c.size_number_id,sum(case when b.is_rescan=0 then b.reject_qty else 0 end) - sum(case when b.is_rescan=1 then b.production_qnty else 0 end) as production_qnty,a.production_date from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c WHERE a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and a.po_break_down_id=$po_id and c.item_number_id=$item_id and c.country_id=$country_id and c.color_number_id=$color_id and a.production_type=$type  and a.sewing_line in($sewing_line_id) and a.status_active=1 and b.status_active=1 and c.status_active=1
			group by c.color_number_id,c.size_number_id,a.production_date order by c.size_number_id"; */

			$sql = "SELECT c.color_number_id,c.size_number_id,sum(b.reject_qty) as production_qnty,a.production_date from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c WHERE a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and a.po_break_down_id=$po_id and c.item_number_id=$item_id and c.country_id=$country_id and c.color_number_id=$color_id and a.production_type=$type  and a.sewing_line in($sewing_line_id) and a.status_active=1 and b.status_active=1 and c.status_active=1
			group by c.color_number_id,c.size_number_id,a.production_date order by c.size_number_id";
			//and a.production_date between '$date_from' and '$date_to'


			$prod_defect_sql = "SELECT DISTINCT a.id,a.defect_qty,e.color_number_id,e.size_number_id,c.production_date
			from pro_gmts_prod_dft a, pro_garments_production_mst c,pro_garments_production_dtls d,wo_po_color_size_breakdown e
			where a.mst_id=c.id and c.id=d.mst_id and c.po_break_down_id=e.po_break_down_id and e.id=d.color_size_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_break_down_id =$po_id and c.sewing_line in($sewing_line_id)  and e.item_number_id=$item_id and e.country_id=$country_id and e.color_number_id=$color_id  and a.defect_point_id=50 and a.defect_type_id=2 and a.production_type = 5";


			$res_gmtsDefectData = sql_select($prod_defect_sql);
			foreach($res_gmtsDefectData as $v)
			{
				$defect_arr[$v['PRODUCTION_DATE']][$v['SIZE_NUMBER_ID']] += $v['DEFECT_QTY'];
			}

			break;
		case 4:
			$sql = "SELECT c.color_number_id,c.size_number_id,sum(b.production_qnty) as production_qnty,a.production_date from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c WHERE a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and a.po_break_down_id=$po_id and c.item_number_id=$item_id and c.country_id=$country_id and c.color_number_id=$color_id and a.production_date<'$date_from' and a.production_type=$type and a.sewing_line in($sewing_line_id) and a.status_active=1 and b.status_active=1 and c.status_active=1
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
		$sew_loss_qty = $defect_arr[$row['PRODUCTION_DATE']][$row['SIZE_NUMBER_ID']]??0;
		if ($sew_loss_qty)
		{
			$prod_qty = $row[csf('production_qnty')] - $sew_loss_qty;
			$size_qty[$row[csf('production_date')]][$row[csf('size_number_id')]] = $prod_qty;
			if($prod_qty>0)
			{
				$prod_array[$row[csf('production_date')]] = $row[csf('production_date')];
			}
		}
		else
		{
			$size_qty[$row[csf('production_date')]][$row[csf('size_number_id')]] = $row[csf('production_qnty')];
			$prod_array[$row[csf('production_date')]] = $row[csf('production_date')];
		}

		$size_array[$row[csf('size_number_id')]] = $row[csf('size_number_id')];
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
			<tbody>
			<?
			$i=1;
			$size_total_array = array();
			foreach ($prod_array as $key => $val)
			{
				if ($i%2==0)
				$bgcolor="#E9F3FF";
				else
				$bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td><? echo $i++;?></td>
					<td><? echo change_date_format($val);?></td>
					<?
					$h_total = 0;
					foreach ($size_array as $size_key => $value)
					{
						?>
						<td><? echo number_format($size_qty[$key][$size_key],0);?></td>
						<?
						$h_total += $size_qty[$key][$size_key];
						$size_total_array[$size_key] += $size_qty[$key][$size_key];
					}
					?>
					<td><? echo number_format($h_total,0);?></td>
				</tr>
				<?
			}
			?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="2" align="right">Total </th>
					<?
						$v_total = 0;
						foreach ($size_array as $key => $val)
						{
							?>
							<th><? echo number_format($size_total_array[$key],0);?></th>
							<?
							$v_total += $size_total_array[$key];
						}
						?>
					<th><? echo number_format($v_total,0); ?></th>
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
	$job_no			= $ex_data[10];

	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	switch ($level)
	{
		case 1:
			$sql = "SELECT c.color_number_id,c.size_number_id,sum(b.production_qnty) as production_qnty,a.production_date
			from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c
			WHERE a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and c.job_no_mst='$job_no' and c.item_number_id in($item_id)  and c.color_number_id in($color_id) and a.production_date between '$date_from' and '$date_to' and a.production_type=$type and a.sewing_line in($sewing_line_id) and a.status_active=1 and c.status_active=1
			group by c.color_number_id,c.size_number_id,a.production_date
			order by c.size_number_id"; //a.po_break_down_id in($po_id)
			break;

		case 2:
			$sql = "SELECT c.color_number_id,c.size_number_id,sum(b.production_qnty) as production_qnty,a.production_date
			from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c
			WHERE a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and c.job_no_mst='$job_no' and c.item_number_id in($item_id)  and c.color_number_id in($color_id) and a.production_date<='$date_to' and a.production_type=$type and a.sewing_line in($sewing_line_id) and a.status_active=1 and c.status_active=1
			group by c.color_number_id,c.size_number_id,a.production_date
			order by c.size_number_id";
			break;
		case 3:
			$sql = "SELECT 0 as production_date, b.bundle_no, c.color_number_id,c.size_number_id,sum(case when b.is_rescan=0 and a.production_type=$type  then b.reject_qty else 0 end) as reject_qty , sum(case when b.is_rescan=1 and a.production_type=$type then b.production_qnty else 0 end) as production_qnty
			from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c
			WHERE a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and c.job_no_mst='$job_no' and c.item_number_id in($item_id)  and c.color_number_id in($color_id) and a.production_type=$type  and a.sewing_line in($sewing_line_id) and a.status_active=1 and c.status_active=1
			group by b.bundle_no,c.color_number_id,c.size_number_id
			order by c.size_number_id"; // and a.production_date between '$date_from' and '$date_to'
			break;
		case 4:
			$sql = "SELECT c.color_number_id,c.size_number_id,sum(b.production_qnty) as production_qnty,a.production_date from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c WHERE a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and c.job_no_mst='$job_no' and c.item_number_id in($item_id)  and c.color_number_id in($color_id) and a.production_date<'$date_from' and a.production_type=$type and a.sewing_line in($sewing_line_id) and a.status_active=1 and c.status_active=1
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
						<td align="right"><? echo $size_qty[$key][$size_key];?></td>
						<?
						$h_total += $size_qty[$key][$size_key];
						$size_total_array[$size_key] += $size_qty[$key][$size_key];
					}
					?>
					<td align="right"><? echo $h_total;?></td>
				</tr>
				<?
			}
			?>
			<tfoot>
				<tr>
					<th colspan="2"></th>
					<?
						$v_total = 0;
						foreach ($size_array as $key => $val)
						{
							?>
							<th><? echo $size_total_array[$key];?></th>
							<?
							$v_total += $size_total_array[$key];
						}
						?>
					<th><? echo $v_total; ?></th>
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
         + (SUM (CASE WHEN a.production_type = 5 and b.is_rescan=0  THEN  b.reject_qty ELSE  0 END) + sum(CASE WHEN a.production_type =5 and b.is_rescan > 1 THEN b.reject_qty else 0 END))) AS production_qnty
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
	// pre($defect_arr); die;
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
	$job_no			= $ex_data[10];

	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	switch ($level)
	{
		case 5:
			$sql = "SELECT b.bundle_no, c.color_number_id,c.size_number_id,SUM (CASE WHEN a.production_type = 4 AND a.production_date <= '$date_to' THEN b.production_qnty  ELSE 0 END) as ttl_input,SUM ( CASE  WHEN a.production_type = 5 AND a.production_date <= '$date_to' THEN b.production_qnty ELSE 0 END) as ttl_output ,SUM (CASE WHEN a.production_type = 5 and b.is_rescan=0  THEN  b.reject_qty ELSE  0 END) as reject_qty , sum(CASE WHEN a.production_type =5 and b.is_rescan = 1 THEN b.production_qnty else 0 END) AS production_qnty
			from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c
			WHERE a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and c.job_no_mst='$job_no' and c.item_number_id=$item_id  and c.color_number_id=$color_id and a.production_type in(4,5) and a.sewing_line in($sewing_line_id) and a.status_active=1 and c.status_active=1
			group by b.bundle_no,c.color_number_id,c.size_number_id
			order by c.size_number_id";
			break;

		case 6:
			$sql = "SELECT c.color_number_id,c.size_number_id,SUM (CASE WHEN a.production_type = 5 AND a.production_date <= '$date_to' THEN b.production_qnty  ELSE 0 END)
         - (SUM ( CASE  WHEN a.production_type = 11 AND a.production_date <= '$date_to' THEN b.production_qnty ELSE 0 END)
         + SUM (CASE WHEN a.production_type = 11  THEN  b.reject_qty ELSE  0 END)) AS production_qnty
			from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c
			WHERE a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and c.job_no_mst='$job_no' and c.item_number_id=$item_id  and c.color_number_id=$color_id and a.production_type in(5,11) and a.sewing_line in($sewing_line_id) and a.status_active=1 and c.status_active=1
			group by c.color_number_id,c.size_number_id
			order by c.size_number_id";
			break;
		case 7:
			$sql = "SELECT c.color_number_id,c.size_number_id,SUM (CASE WHEN a.production_type = 4 AND a.production_date <= '$date_to' THEN b.production_qnty  ELSE 0 END)
         - (SUM ( CASE  WHEN a.production_type = 11 AND a.production_date <= '$date_to' THEN b.production_qnty ELSE 0 END)
         + SUM (CASE WHEN a.production_type = 11 THEN  b.reject_qty ELSE  0 END)) AS production_qnty
			from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c
			WHERE a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and c.job_no_mst='$job_no' and c.item_number_id=$item_id  and c.color_number_id=$color_id and a.production_type in(4,11) and a.sewing_line in($sewing_line_id) and a.status_active=1 and c.status_active=1
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
						<td align="right"><? echo $size_qty[$key][$size_key];?></td>
						<?
						$h_total += $size_qty[$key][$size_key];
						$size_total_array[$size_key] += $size_qty[$key][$size_key];
					}
					?>
					<td align="right"><? echo $h_total;?></td>
				</tr>
				<?
			}
			?>
			<tfoot>
				<tr>
					<th colspan="2"></th>
					<?
						$v_total = 0;
						foreach ($size_array as $key => $val)
						{
							?>
							<th><? echo $size_total_array[$key];?></th>
							<?
							$v_total += $size_total_array[$key];
						}
						?>
					<th><? echo $v_total; ?></th>
				</tr>
			</tfoot>
		</table>
	</div>
	<?
}

?>