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
	$ex_data = explode("_", $data);
	echo create_drop_down( "cbo_location_id", 120, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id in($ex_data[0]) order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/style_and_line_wise_production_report_v2_controller','$ex_data[0]'+'_'+this.value+'_'+'$ex_data[1]', 'load_drop_down_floor', 'floor_td')" );
	exit();    	 
}

if ($action=="load_drop_down_floor")
{
	$ex_data = explode("_", $data);
	echo create_drop_down( "cbo_floor_id", 120, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 
	and company_id in($ex_data[0]) and location_id in($ex_data[1]) order by floor_name","id,floor_name",1, "-- Select Floor --", $selected, "load_drop_down( 'requires/style_and_line_wise_production_report_v2_controller','$ex_data[1]'+'_'+this.value+'_'+'$ex_data[0]'+'_'+'$ex_data[2]', 'load_drop_down_line', 'line_td');set_multiselect('cbo_line_id','0','0','','0');" );   
	  	 	
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
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name in($explode_data[2]) and variable_list=23 and is_deleted=0 and status_active=1");
	$txt_date = $explode_data[3];
	
	$cond="";
	if($prod_reso_allo==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_array=array();
		
	
			if( $explode_data[0]!=0 ) $cond .= " and a.location_id in($explode_data[0])";
			if( $explode_data[1]!=0 ) $cond .= " and a.floor_id in($explode_data[1])";
			if( $explode_data[2]!=0 ) $cond .= " and a.company_id in($explode_data[2])";
			if($db_type==0){if($explode_data[3]!=0) $cond .=" and year(a.update_date)=$explode_data[3]"; else $cond.="";}
			else if($db_type==2){if($explode_data[3]!=0) $cond .=" and to_char(b.pr_date,'YYYY')=$explode_data[3]"; else $cond .="";}

			// echo "sselect a.id, a.line_number,b.line_name ,to_char(a.update_date,'YYYY') as year
			// from prod_resource_mst a, lib_sewing_line b where a.line_number=b.id and a.is_deleted=0 and b.is_deleted=0 and a.location_id in(5) and a.floor_id in(12) and a.company_id in(3)  and to_char(a.update_date,'YYYY')='2021' group by a.id, a.line_number,b.line_name,to_char(a.update_date,'YYYY') order by b.line_name";
			$line_data=sql_select( "select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b 
			where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond   group by a.id, a.line_number order by a.id asc");
	
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

		echo create_drop_down( "cbo_line_id", 140,$line_array,"", 1, "-- Select --", $selected, "","",0 );
	}
	else
	{
		if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_name=$explode_data[0]";
		if( $explode_data[0]!=0 ) $cond = " and floor_name=$explode_data[1] and company_name in($explode_data[2])";

		echo create_drop_down( "cbo_line_id", 140, "select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name","id,line_name", 1, "-- Select --", $selected, "","load_line();",0 );
		
	}
	exit();
}

if ($action=="load_drop_down_buyer")
{
	
	echo create_drop_down( "cbo_buyer_id", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in ($data)  and buy.id in (select  buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );     	 
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
	$sql = "SELECT a.id,a.po_number,a.job_no_mst,a.grouping,a.file_no,b.style_ref_no,b.job_no_prefix_num,$select_date as year from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and b.company_name=$lc_company $buyer_cond $job_year_cond  $style_cond and a.status_active in(1,2,3) and b.status_active=1"; 
	//echo $sql; die;
	echo create_list_view("list_view", "Order No,Ref No,File No,Job No,Year,Style Ref No","150,80,80,50,70,150","660","310",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "po_number,grouping,file_no,job_no_prefix_num,year,style_ref_no", "","setFilterGrid('list_view',-1)","0","",1) ;	
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
	
	$buyerArr 		= return_library_array("select id,short_name from lib_buyer","id","short_name"); 
	$colorArr 		= return_library_array("select id,color_name from lib_color","id","color_name"); 
	$locationArr	= return_library_array("select id,location_name from lib_location","id","location_name"); 
	$floorArr 		= return_library_array("select id,floor_name from lib_prod_floor","id","floor_name"); 
	$lineArr 		= return_library_array("select id,line_name from lib_sewing_line","id","line_name"); 
	$season_arr= return_library_array("select id,season_name from lib_buyer_season","id","season_name"); 

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
	$txt_job_no 		=str_replace("'","",$txt_job_no);
	
    $today_date 		=date("Y-m-d");
	$txt_date_from 		="".str_replace("'","",$txt_date_from)."";
	$txt_date_to 		=str_replace("'","",$txt_date_to);	
	$report_title 		=str_replace("'","",$report_title);
	//******************************************* MAKE QUERY CONDITION ************************************************
	$sql_cond = "";
	$sql_cond .= ($working_company_id=="") 	? "" : " and d.serving_company in($working_company_id)";
	$sql_cond .= ($location_id==0) 		? "" : " and d.location in($location_id)";
	$sql_cond .= ($floor_id==0) 			? "" : " and d.floor_id in($floor_id)";
	$sql_cond .= ($line_id==0) 			? "" : " and d.sewing_line in($line_id)";
	$sql_cond .= ($lc_company_id==0) 		? "" : " and a.company_name=$lc_company_id";
	$sql_cond .= ($buyer_id==0) 			? "" : " and a.buyer_name=$buyer_id";
	$sql_cond .= ($color_id=="") 			? "" : " and c.color_number_id in($color_id)";
	$sql_cond .= ($txt_job_no=="") 			? "" : " and a.job_no_prefix_num in($txt_job_no)";
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
		

		$date_cond1 = " and e.production_date between '$txt_datefrom' and '$txt_dateto'";
		$date_cond2 = " and g.production_date between '$txt_datefrom' and '$txt_dateto'";
		$date_cond3 = " and a.production_date between '$txt_datefrom' and '$txt_dateto'";
	}else{
		$date_cond1 = "";
		$date_cond2 = "";
	}

	

	if($job_year>0)
	{
		if($db_type==0)
		{
			if($txt_job_no !=""){
			 $sql_cond2 .=" and year(a.insert_date)='$job_year'";
			}
		}
		else
		{
			if($txt_job_no !=""){
			 $sql_cond2 .=" and to_char(a.insert_date,'YYYY')='$job_year'";
			}
		}	
	}

	// echo $sql_cond;die();
	if($type==1)
	{		


		// ================================================ MAIN QUERY ==================================================


		// $sql="SELECT  a.buyer_name,a.client_id as buyer_client,a.style_ref_no as style,a.job_no_prefix_num as job_id,b.id as po_id,b.po_number,c.item_number_id,c.color_number_id,d.floor_id,d.sewing_line,d.prod_reso_allo,a.job_no,b.po_quantity,b.unit_price,b.doc_sheet_qty,a.set_smv,d.production_date,sum(e.production_qnty) as good_qnty,e.production_qnty,e.production_type,d.production_quantity,a.season_buyer_wise,a.company_name, a.total_set_qnty as ratio from  wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, pro_garments_production_mst d,pro_garments_production_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and b.id=d.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id $sql_cond $sql_cond2 $po_ids_cond and a.status_active=1  and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.production_qnty>0 and d.sewing_line>0 	group by  a.buyer_name,a.client_id ,a.style_ref_no ,a.job_no_prefix_num ,b.id,b.po_number,c.item_number_id,c.color_number_id,d.floor_id,d.sewing_line,d.prod_reso_allo,a.job_no,b.po_quantity,b.unit_price,	b.doc_sheet_qty,a.set_smv,d.production_date,e.production_qnty,e.production_type,d.production_quantity,a.season_buyer_wise,a.company_name , a.total_set_qnty
		// order by d.sewing_line,a.job_no_prefix_num asc";

	
		$sql="SELECT  a.buyer_name,a.client_id as buyer_client,a.style_ref_no as style,a.job_no_prefix_num as job_id,b.id as po_id,b.po_number,c.item_number_id,c.color_number_id,d.floor_id,d.sewing_line,d.prod_reso_allo,a.job_no,b.po_quantity,b.unit_price,b.doc_sheet_qty,a.set_smv,d.production_date,sum(e.production_qnty) as good_qnty,e.production_qnty,e.production_type,d.production_quantity,a.season_buyer_wise,a.company_name, a.total_set_qnty as ratio,a.avg_unit_price from  wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, pro_garments_production_mst d,pro_garments_production_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and b.id=d.po_break_down_id and d.id=e.mst_id  $sql_cond $sql_cond2 $po_ids_cond and a.status_active=1  and b.status_active=1 and c.status_active=1 and d.status_active=1  and d.sewing_line>0 and d.production_type in (5,11) and e.production_type in (5,11)	group by  a.buyer_name,a.client_id ,a.style_ref_no ,a.job_no_prefix_num ,b.id,b.po_number,c.item_number_id,c.color_number_id,d.floor_id,d.sewing_line,d.prod_reso_allo,a.job_no,b.po_quantity,b.unit_price,	b.doc_sheet_qty,a.set_smv,d.production_date,e.production_qnty,e.production_type,d.production_quantity,a.season_buyer_wise,a.company_name , a.total_set_qnty,a.avg_unit_price
		order by d.sewing_line,a.job_no_prefix_num asc";
		
		
		 //echo $sql;
		$sql_res=sql_select($sql);
		// echo "<pre>";
		//  print_r($sql_res);die;

		$main_array = array();
		$poId_arr = array();
		$i=0;
	
		foreach ($sql_res as $row) 
		{
			
				$main_array[$row[csf('sewing_line')]][$row[csf('job_id')]][$row[csf('po_number')]][$row[csf('color_number_id')]]['po_id'] = $row[csf('po_id')];
				$main_array[$row[csf('sewing_line')]][$row[csf('job_id')]][$row[csf('po_number')]][$row[csf('color_number_id')]]['color'] = $colorArr[$row[csf('color_number_id')]];
				$main_array[$row[csf('sewing_line')]][$row[csf('job_id')]][$row[csf('po_number')]][$row[csf('color_number_id')]]['buyer_name'] = $buyerArr[$row[csf('buyer_name')]];
				$main_array[$row[csf('sewing_line')]][$row[csf('job_id')]][$row[csf('po_number')]][$row[csf('color_number_id')]]['buyer_client'] = $buyerArr[$row[csf('buyer_client')]];
				$main_array[$row[csf('sewing_line')]][$row[csf('job_id')]][$row[csf('po_number')]][$row[csf('color_number_id')]]['style'] = $row[csf('style')];
				$main_array[$row[csf('sewing_line')]][$row[csf('job_id')]][$row[csf('po_number')]][$row[csf('color_number_id')]]['job_no'] = $row[csf('job_no')];
				$main_array[$row[csf('sewing_line')]][$row[csf('job_id')]][$row[csf('po_number')]][$row[csf('color_number_id')]]['season'] =$season_arr[$row[csf('season_buyer_wise')]];

				$main_array[$row[csf('sewing_line')]][$row[csf('job_id')]][$row[csf('po_number')]][$row[csf('color_number_id')]]['item'] = $garments_item[$row[csf('item_number_id')]];

				$main_array[$row[csf('sewing_line')]][$row[csf('job_id')]][$row[csf('po_number')]][$row[csf('color_number_id')]]['po_quantity'] =$row[csf('po_quantity')];
				$main_array[$row[csf('sewing_line')]][$row[csf('job_id')]][$row[csf('po_number')]][$row[csf('color_number_id')]]['unit_price'] =$row[csf('unit_price')];
				$main_array[$row[csf('sewing_line')]][$row[csf('job_id')]][$row[csf('po_number')]][$row[csf('color_number_id')]]['doc_sheet_qty'] =$row[csf('doc_sheet_qty')];
				$main_array[$row[csf('sewing_line')]][$row[csf('job_id')]][$row[csf('po_number')]][$row[csf('color_number_id')]]['set_smv'] =$row[csf('set_smv')];
				
				$main_array[$row[csf('sewing_line')]][$row[csf('job_id')]][$row[csf('po_number')]][$row[csf('color_number_id')]]['production_date'] =$row[csf('production_date')];

				$main_array[$row[csf('sewing_line')]][$row[csf('job_id')]][$row[csf('po_number')]][$row[csf('color_number_id')]]['production_date'] =$row[csf('production_date')];



				$main_array[$row[csf('sewing_line')]][$row[csf('job_id')]][$row[csf('po_number')]][$row[csf('color_number_id')]]['good_qty'] +=$row[csf('good_qnty')];
				$main_array[$row[csf('sewing_line')]][$row[csf('job_id')]][$row[csf('po_number')]][$row[csf('color_number_id')]]['amount'] =$row[csf('po_quantity')]*$row[csf('unit_price')];
				
				$main_array[$row[csf('sewing_line')]][$row[csf('job_id')]][$row[csf('po_number')]][$row[csf('color_number_id')]]['tot_qty'] =$row[csf('po_quantity')]*$row[csf('ratio')];
				$main_array[$row[csf('sewing_line')]][$row[csf('job_id')]][$row[csf('po_number')]][$row[csf('color_number_id')]]['po_number']=$row[csf('po_number')];
				$line_arr[$row[csf('sewing_line')]]=$row[csf('job_id')];

				$job_arr[$row[csf('job_no')]]="*".$row[csf('job_no')].'*';

				// $job_arr[$row[csf('po_id')]]="*".$row[csf('po_id')].'*';

				$date[$row[csf('production_date')]]="*".$row[csf('production_date')].'*';

				// if($i==0){
				// 	$main_array[$row[csf('job_id')]][$row[csf('sewing_line')]]['po_number'] .=$row[csf('po_number')];
				// 	$i++;
				// }else{
				// 	$main_array[$row[csf('job_id')]][$row[csf('sewing_line')]]['po_number'] .=",".$row[csf('po_number')];
				// }

				if($row[csf('production_type')]==5){

					$main_array[$row[csf('sewing_line')]][$row[csf('job_id')]][$row[csf('po_number')]][$row[csf('color_number_id')]]['sewing_qnty'] +=$row[csf('good_qnty')];
					
					

				}elseif($row[csf('production_type')]==11){
					$main_array[$row[csf('sewing_line')]][$row[csf('job_id')]][$row[csf('po_number')]][$row[csf('color_number_id')]]['poly_qnty'] +=$row[csf('production_quantity')];
				}
			
		}

//==========================================================================================

			$job_no=implode(",",$job_arr);
			$job_no_list=str_replace("*","'",$job_no);
			 $line_id=implode(",",$line_arr);
	
			 $po_qty_sql=sql_select("select c.id,c.plan_cut_qnty,c.job_no_mst,c.color_number_id,c.po_break_down_id from  wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  c.job_no_mst in ($job_no_list) and a.status_active=1 and b.status_active=1 and c.status_active=1 ");
			 foreach($po_qty_sql as $val)
			 {
				$po_qty_arr[$val[csf('job_no_mst')]][$val[csf('po_break_down_id')]][$val[csf('color_number_id')]]+=$val[csf('plan_cut_qnty')];
			 }

			//   echo "<pre>";
			//   print_r($po_qty_arr);

			//  $poly_qty_sql=sql_select("select b.production_qnty,a.po_break_down_id,c.color_number_id,c.job_no_mst ,a.production_date,a.sewing_line	 from pro_garments_production_mst a,pro_garments_production_dtls b,wo_po_color_size_breakdown c where a.id=b.mst_id  and a.po_break_down_id=c.po_break_down_id and c.job_no_mst  in ($job_no_list) and a.status_active=1 and b.status_active=1 and c.status_active=1 and b.production_type=11 $date_cond3 
			//  group by a.po_break_down_id,c.color_number_id,c.job_no_mst ,a.production_date,a.sewing_line,b.production_qnty order by a.sewing_line");

			$poly_qty_sql=sql_select("SELECT sum(b.production_qnty) as production_qnty,c.color_number_id,c.job_no_mst,c.po_break_down_id,a.sewing_line 	from pro_garments_production_mst a,pro_garments_production_dtls b ,wo_po_color_size_breakdown c	where a.id=b.mst_id and c.job_no_mst  in ($job_no_list)  and a.production_type='11'   and b.COLOR_SIZE_BREAK_DOWN_ID=c.id and b.production_type='11' and a.status_active=1 $date_cond3  and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by c.color_number_id,c.job_no_mst,c.po_break_down_id,a.sewing_line ");
			
			 foreach($poly_qty_sql as $val)
			 {
				$poly_qty_arr[$val[csf('sewing_line')]][$val[csf('job_no_mst')]][$val[csf('po_break_down_id')]][$val[csf('color_number_id')]] =$val[csf('production_qnty')];
			 }

			 $sweing_qty_sql=sql_select("SELECT sum(b.production_qnty) as production_qnty,c.color_number_id,c.job_no_mst,c.po_break_down_id,a.sewing_line 	from pro_garments_production_mst a,pro_garments_production_dtls b ,wo_po_color_size_breakdown c	where a.id=b.mst_id and c.job_no_mst  in ($job_no_list)  and a.production_type='5'   and b.COLOR_SIZE_BREAK_DOWN_ID=c.id and b.production_type='5' and a.status_active=1 $date_cond3  and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by c.color_number_id,c.job_no_mst,c.po_break_down_id,a.sewing_line ");
			
			 foreach($sweing_qty_sql as $val)
			 {
				$sweing_qty_arr[$val[csf('sewing_line')]][$val[csf('job_no_mst')]][$val[csf('po_break_down_id')]][$val[csf('color_number_id')]] =$val[csf('production_qnty')];

				$sweing_qty_arr[$val[csf('production_date')]][$val[csf('sewing_line')]][$val[csf('job_no_mst')]][$val[csf('po_break_down_id')]][$val[csf('color_number_id')]] +=$val[csf('production_qnty')];
			 }

			//   echo "<pre>";
			//   print_r($poly_qty_arr);

		// $sql_fab_book= "select a.job_no,a.style_ref_no,b.po_number,d.amount,d.grey_fab_qnty as grey_fab_qnty, d.fin_fab_qnty as fin_fab_qnty,d.rate as rate,c.entry_form	from wo_po_details_master a, wo_po_break_down b, wo_booking_mst c,wo_booking_dtls d  where a.job_no=b.job_no_mst and b.id=d.po_break_down_id and d.booking_no=c.booking_no and  c.booking_type in(1,4) and c.short_booking_type not in(2,3) and a.job_no in ($job_no_list) and b.status_active!=0 and b.is_deleted=0   and c.status_active=1  and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.fin_fab_qnty>0  group by a.job_no,a.style_ref_no,b.po_number,d.amount,d.grey_fab_qnty , d.fin_fab_qnty ,d.rate ,c.entry_form";
		

		$sql_fab_book="select a.job_no,a.style_ref_no,b.id as po_id,b.po_number,c.booking_date,c.booking_no,c.booking_type,c.entry_form,
		c.is_short,c.supplier_id,c.short_booking_type as book_type,c.fabric_source,c.item_category,c.pay_mode,c.is_approved,
		d.pre_cost_fabric_cost_dtls_id as fab_dtls_id,d.gmt_item,d.color_type,d.construction,d.copmposition,d.gsm_weight,
		d.uom,d.dia_width,d.trim_group,d.grey_fab_qnty as grey_fab_qnty, d.fin_fab_qnty as fin_fab_qnty,d.rate as rate,c.remarks,
		 (d.amount) as amount, (CASE WHEN c.booking_type=1 and c.is_short=2 THEN d.amount ELSE 0 END) as fab_main_amount,
		  (CASE WHEN c.booking_type=1 and c.is_short=1 and c.short_booking_type=1 THEN d.amount ELSE 0 END) as fab_short_amount,
		   (CASE WHEN c.booking_type=4 and c.is_short=2 THEN d.amount ELSE 0 END) as fab_with_ord_amount from wo_po_details_master a,
		   wo_po_break_down b, wo_booking_mst c,wo_booking_dtls d where a.job_no=b.job_no_mst and b.id=d.po_break_down_id and 
		   d.booking_no=c.booking_no and c.booking_type in(1,4) and c.short_booking_type not in(2,3) and b.status_active!=0
			and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.fin_fab_qnty>0 
			and a.job_no in ($job_no_list) order by c.booking_no,d.pre_cost_fabric_cost_dtls_id ";


		//  echo $sql_fab_book;
		 $dataArray3=sql_select($sql_fab_book);
		foreach($dataArray3 as $val)
		{
			$entry_form=$val[csf('entry_form')];
			if($entry_form!=108)
			{
				 $grey_fab_qnty=$val[csf('grey_fab_qnty')];
					 // $grey_fab_qnty=$rows[csf('fin_fab_qnty')];
				 if($grey_fab_qnty==0)
				 {
					 $avg_rate=0;
					 $amount=0;
					 }
					 else{
				$avg_rate=$val[csf('amount')]/$grey_fab_qnty;
				$amount=$grey_fab_qnty*$avg_rate;
						 }
			}
			else
			{
				$grey_fab_qnty=$val[csf('fin_fab_qnty')];
				 if($grey_fab_qnty==0)
				 {
					 $avg_rate=0;
					 $amount=0;
					 }
					 else{
				$avg_rate=$val[csf('amount')]/$grey_fab_qnty;
				$amount=$grey_fab_qnty*$avg_rate;
						 }

			}
			$raw_material_all_arr[$val[csf('job_no')]][$val[csf('po_number')]]['fb'] +=$amount;
		}

		$sql_trims_book= "select a.job_no,a.style_ref_no,b.po_number,d.amount,d.exchange_rate,d.wo_qnty	,d.id as dtls_id from wo_po_details_master a, wo_po_break_down b, wo_booking_mst c,wo_booking_dtls d  where a.job_no=b.job_no_mst  and b.id=d.po_break_down_id and d.booking_no=c.booking_no  and c.booking_type in(2,5)  and a.job_no in ($job_no_list)  and  b.status_active!=0 and  c.status_active=1 and  d.status_active=1 and c.item_category=4  and c.is_deleted=0     and d.wo_qnty>0 group by a.job_no,a.style_ref_no,b.po_number,d.amount,d.exchange_rate ,d.wo_qnty,d.id ";

		//echo $sql_trims_book;
		
		 $dataArray4=sql_select($sql_trims_book);

		foreach($dataArray4 as $val)
		{
					$avg_rate=$val[csf('amount')]/$val[csf('wo_qnty')];
					$rate=($avg_rate/$val[csf('exchange_rate')]);
					
					$amount=number_format($val[csf('wo_qnty')]*$rate,6,'.','');
					$raw_material_all_arr[$val[csf('job_no')]][$val[csf('po_number')]]['trims'] +=$amount;
		}

		$sql_embl_book= "select a.job_no,a.style_ref_no,b.po_number,d.amount from wo_po_details_master a, wo_po_break_down b, wo_booking_mst c,wo_booking_dtls d  where a.job_no=b.job_no_mst and a.job_no=c.job_no and b.id=d.po_break_down_id and d.booking_no=c.booking_no  and c.booking_type in(6)  and a.job_no in ($job_no_list)	 and b.status_active!=0 and  c.status_active=1 and  c.item_category=25   and c.is_deleted=0 and  d.status_active=1   and d.wo_qnty>0  group by a.job_no,a.style_ref_no,b.po_number,d.amount ";
		
		 $dataArray5=sql_select($sql_embl_book);

		foreach($dataArray5 as $val)
		{
			
			$raw_material_all_arr[$val[csf('job_no')]][$val[csf('po_number')]]['embl'] +=$val[csf('amount')];

		}

		$sql_aop_book= "select a.job_no,a.style_ref_no,b.po_number,d.amount 
		from wo_po_details_master a, wo_po_break_down b, wo_booking_mst c,wo_booking_dtls d   where a.job_no=b.job_no_mst  and b.id=d.po_break_down_id and d.booking_no=c.booking_no  and c.booking_type in(3)  and d.booking_type in(3)  and a.job_no in ($job_no_list) and  b.is_deleted=0 and b.status_active!=0 and  c.status_active=1 and  c.item_category=12    and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and d.wo_qnty>0  group by a.job_no,a.style_ref_no,b.po_number,d.amount
		  ";
		
		 $dataArray6=sql_select($sql_aop_book);



		foreach($dataArray6 as $val)
		{
			
			$raw_material_all_arr[$val[csf('job_no')]][$val[csf('po_number')]]['aop'] +=$val[csf('amount')];

		}

		$sql_lab_book= "select a.job_no,a.style_ref_no,b.po_number,d.wo_value as amount from wo_po_details_master a, wo_po_break_down b, wo_labtest_mst c,wo_labtest_dtls d   where a.job_no=b.job_no_mst  and a.job_no=d.job_no and c.id=d.mst_id  and d.po_id=b.id and  c.status_active=1   and a.job_no in ($job_no_list) and c.is_deleted=0   group by a.job_no,a.style_ref_no,b.po_number,d.wo_value ";
		
		 $dataArray8=sql_select($sql_lab_book);



		foreach($dataArray8 as $val)
		{
			
			$raw_material_all_arr[$val[csf('job_no')]][$val[csf('po_number')]]['lab'] +=$val[csf('amount')];

		}


		
			
		$sql_access_book="select a.job_no,a.style_ref_no,b.po_number,d.cons_amount as amount,e.item_group_id,sum(f.issue_qnty) as cons_quantity,f.recv_trans_id from wo_po_details_master a, wo_po_break_down b, inv_issue_master c, inv_transaction d,product_details_master e,inv_mrr_wise_issue_details f where a.job_no=b.job_no_mst and b.id=d.order_id and c.id=d.mst_id and d.transaction_type in(2) and a.job_no in ($job_no_list) and f.issue_trans_id=d.id and e.id=f.prod_id and c.entry_form=21 and f.entry_form=21  and 	 e.id=d.prod_id and d.status_active=1 and d.item_category=4 and b.status_active!=0 and c.is_deleted=0 
		  and d.cons_quantity>0  group by a.job_no,a.style_ref_no,b.po_number,d.cons_amount,e.item_group_id ,f.recv_trans_id";
		 $dataArray9=sql_select($sql_access_book);

		 $conv_sql="select a.id as item_id,a.conversion_factor from lib_item_group a,product_details_master b where a.id=b.item_group_id and b.entry_form=20 and b.item_category_id=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
		 //echo $conv_sql;
		 $sql_conv_result=sql_select($conv_sql);
		 foreach($sql_conv_result as $row)
		 {
			 $conversion_arr[$row[csf("item_id")]]['conver_rate']=$row[csf("conversion_factor")];
		 }
		 $sql_mrr_recv= "select a.recv_number,b.id as trans_id,f.issue_qnty,f.issue_trans_id,b.order_rate,b.order_ile,e.product_name_details,e.item_group_id
		 from  inv_receive_master a, inv_transaction b,product_details_master e,inv_mrr_wise_issue_details f  where a.id=b.mst_id and e.id=b.prod_id  and f.recv_trans_id=b.id and b.transaction_type=1 and a.entry_form=20 and a.company_id=$working_company_id  and b.status_active=1 and  b.is_deleted=0  ";
		
			$mrr_recv_result=sql_select($sql_mrr_recv);
			$mrr_recv_arr=array();
			foreach($mrr_recv_result as $row)
			{
				
				$mrr_recv_arr[$row[csf('trans_id')]]['rate']=$row[csf('order_rate')]+$row[csf('order_ile')];
			}
		foreach($dataArray9 as $val)
		{
		
					$converrate=$conversion_arr[$val[csf("item_group_id")]]['conver_rate'];
					 $orderrate=($mrr_recv_arr[$val[csf('recv_trans_id')]]['rate']/$converrate);
					 $gen_amount=$val[csf('cons_quantity')]*$orderrate;
			$raw_material_all_arr[$val[csf('job_no')]][$val[csf('po_number')]]['acces'] +=number_format($gen_amount,6,'.','');;

		}




	 	    // echo "<pre>";
	    //    print_r($raw_material_all_arr);

		
			foreach($raw_material_all_arr as $job_id  => $line_data)
			{
				foreach($line_data as $po_id => $val)
				{
					
					// echo $val['fb'];die;
					$raw_material_all_sum[$job_id][$po_id] =$val['fb']+$val['trims']+$val['aop']+$val['lab']+$val['acces']+$val['embl'];
					
				}
			}
		
	//    echo "<pre>";
	//    print_r($raw_material_all_sum);

		// echo "</pre>";

		// ======================================= FOR available min/hour QNTY ============================================
		// $line_id=implode(",",$line_arr);
	


		$date_no=implode(",",$date);
		$date_no_list=str_replace("*","'",$date_no);
		// echo $date_no_list;
		$prod_resource_array=array();
		$dataArray=sql_select("SELECT a.id, a.location_id, a.floor_id, a.line_number,e.job_no_mst,e.po_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper, b.smv_adjust, 
		b.smv_adjust_type, b.line_chief, b.target_per_hour, b.working_hour, c.from_date,c.to_date, c.capacity as mc_capacity,b.total_smv,g.color_number_id from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_mast c,pro_garments_production_mst d ,wo_po_break_down e,wo_po_color_size_breakdown g  where a.id=c.mst_id and a.id=b.mst_id and c.id=b.mast_dtl_id and a.id=d.sewing_line  and e.id=d.po_break_down_id and e.id=g.po_break_down_id
		 and   d.production_date in ($date_no_list)  and b.pr_date=d.production_date");// and a.id=1 and c.from_date=$txt_date
	

		foreach($dataArray as $val)
		{
		
			$prod_resource_array[$val[csf('id')]][$val[csf('job_no_mst')]][$val[csf('po_number')]][$val[csf('color_number_id')]]['man_power']=$val[csf('man_power')];
			$prod_resource_array[$val[csf('id')]][$val[csf('job_no_mst')]][$val[csf('po_number')]][$val[csf('color_number_id')]]['operator']=$val[csf('operator')];
			$prod_resource_array[$val[csf('id')]][$val[csf('job_no_mst')]][$val[csf('po_number')]][$val[csf('color_number_id')]]['helper']=$val[csf('helper')];
			$prod_resource_array[$val[csf('id')]][$val[csf('job_no_mst')]][$val[csf('po_number')]][$val[csf('color_number_id')]]['terget_hour']=$val[csf('target_per_hour')];
			$prod_resource_array[$val[csf('id')]][$val[csf('job_no_mst')]][$val[csf('po_number')]][$val[csf('color_number_id')]]['working_hour']=$val[csf('working_hour')];
			$prod_resource_array[$val[csf('id')]][$val[csf('job_no_mst')]][$val[csf('po_number')]][$val[csf('color_number_id')]]['tpd']=$val[csf('target_per_hour')]*$val[csf('working_hour')];
			$prod_resource_array[$val[csf('id')]][$val[csf('job_no_mst')]][$val[csf('po_number')]][$val[csf('color_number_id')]]['day_start']=$val[csf('from_date')];
			$prod_resource_array[$val[csf('id')]][$val[csf('job_no_mst')]][$val[csf('po_number')]][$val[csf('color_number_id')]]['day_end']=$val[csf('to_date')];
			$prod_resource_array[$val[csf('id')]][$val[csf('job_no_mst')]][$val[csf('po_number')]][$val[csf('color_number_id')]]['capacity']=$val[csf('mc_capacity')];
			$prod_resource_array[$val[csf('id')]][$val[csf('job_no_mst')]][$val[csf('po_number')]][$val[csf('color_number_id')]]['total_smv']=$val[csf('total_smv')];
			$prod_resource_array[$val[csf('id')]][$val[csf('job_no_mst')]][$val[csf('po_number')]][$val[csf('color_number_id')]]['smv_adjust_type']=$val[csf('smv_adjust_type')];
		}

	$smv_sql="SELECT a.id, a.system_no_prefix, a.bulletin_type, a.buyer_id, a.style_ref, a.working_hour, a.gmts_item_id, a.operation_count, a.mc_operation_count, a.total_smv, a.tot_mc_smv, a.tot_manual_smv, a.tot_finishing_smv, max(b.row_sequence_no) as seq_no	FROM ppl_gsd_entry_mst a, ppl_gsd_entry_dtls b 	where   a.id=b.mst_id and a.bulletin_type>0 and a.is_deleted=0 and b.is_deleted=0	and a.approved=1  group by a.id, a.system_no_prefix, a.bulletin_type, a.buyer_id, a.style_ref, a.working_hour, a.gmts_item_id, a.operation_count, a.mc_operation_count, a.total_smv, a.tot_mc_smv, a.tot_manual_smv, a.tot_finishing_smv order by a.system_no_prefix";
	// echo $smv_sql;
		$dataArray2 =sql_select($smv_sql);
		
			foreach($dataArray2 as $val)
			{
				
				if($val[csf('bulletin_type')]==4){					
					$other_data_arr[$val[csf('style_ref')]]['smv']=$val[csf('total_smv')];					
				}elseif($val[csf('bulletin_type')]==3){
					$other_data_arr[$val[csf('style_ref')]]['budget_smv']=$val[csf('total_smv')];
				}
			}

			//  echo "<pre>";
			//  print_r($date_wise_data_arr);
			
		ob_start();
		?>
		
		<div class="main" style="margin: 0 auto; padding: 10px;  width: 100%">
			<table width="100%" cellspacing="0">
		        <tr class="form_caption" style="border:none;">
		            <td colspan="9" align="center" ><font size="3"><strong><u><? echo $company_details[$lc_company_id]; ?></u></strong></font></td>
		        </tr>
		        <tr class="form_caption" style="border:none;">
		            <td colspan="9" align="center"><h1>Style and Line Wise Production Report for Accounts</h1></td>
		        </tr>
				<tr class="form_caption" style="border:none;">
		            <td colspan="9" align="center"><h3>Date: <?=$txt_date_from." To ".$txt_date_to;?></h3></td>
		        </tr>
		    </table>
			<br>
		    <div>
		    	<table class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" id="" width="2360">
		    		<thead>
		    			<th width="30">Sl.</th>
		    			<th width="100">Buyer Name</th>
		    			<th width="100">Buyer Client</th>
		    			<th width="100">Style Ref.</th>
						<th width="100">Season</th>	    
		    			<th width="100">Job No.</th>
		    			<th width="150">PO No.</th>
		    				
		    			<th width="100">Garments Item</th>
		    			<th width="100">Color Name</th>
		    			<th width="100">PO Qty(pcs)</th>		    		
		    			<th width="100">Sewing Line </th>
		    			<th width="100">Sewing Output</th>
		    			<th width="100">Poly Output</th>
						<th width="100">Budget SMV</th>
		    			<th width="100">AVG. SMV</th>						
		    			<th width="100">FOB Price</th>
		    			<th width="100">FOB Value</th>
		    			<th width="100">Produced Min<br>(Sewing)</th>
		    			<th width="100">Available Min<br>(Sewing)</th>
						<th width="100">Raw Material<br>Cost/Psc</th>
		    			<th width="100">Raw Material<br>Cost(sew,qty)</th>
						<th width="100">Margin</th>
		    			
		    		</thead>
		    	</table>
		    	<div style="width: 2380px; overflow-y: scroll; max-height: 400px" id="scroll_body">
		    		<table cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all" width="2360" id="html_search">
					
						<?
						// echo "<pre>";

						// print_r($main_array);
					foreach ($main_array as $line_id => $job_data){
						foreach ($job_data as $job_key => $po_data) 
						{
							foreach ($po_data as $po_id => $color_data) 
							{
								foreach ($color_data as $color_id => $row) 
								{
									$sweing_qty=$sweing_qty_arr[$line_id][$row['job_no']][$row['po_id']][$color_id];
									$poly_qty=$poly_qty_arr[$line_id][$row['job_no']][$row['po_id']][$color_id];
									if($sweing_qty !="" || $poly_qty !=""){
									$produce_sum[$line_id] +=$sweing_qty*$other_data_arr[$row['style']]['smv'];
					

									}
									if($sweing_qty !=""){
										$sweing_qty_sum[$row['style']][$line_id]+=$sweing_qty;

										if(isset($other_data_arr[$row['style']]['smv'])){											
											$produce_style_sum[$row['style']][$line_id] +=$sweing_qty*$other_data_arr[$row['style']]['smv'];
										}else{
											$produce_style_sum[$row['style']][$line_id] +=$sweing_qty*$other_data_arr[$row['style']]['budget_smv'];										
										}

										
									}

								}
							}

						}
					}
					
					// echo "<pre>";
					// print_r($produce_sum);

						$i=1;
						$line_po_qty_tot=0;$line_po_qty_gtot=0;$line_sewing_qnty_qty_tot=0;$line_sewing_qnty_qty_gtot=0;$line_poly_qnty_qty_tot=0;$line_poly_qnty_qty_gtot=0;$line_set_smv_tot=0;$line_set_smv_gtot=0;$line_budget_smv_gtot=0;$line_fob_value_tot=0;$line_fob_value_gtot=0;$line_production_min_tot=0;$line_production_min_gtot=0;$line_availble_min_tot=0;$line_availble_min_gtot=0;$line_raw_material_cost_tot=0;$line_raw_material_cost_gtot=0;$line_raw_sewing_material_cost_tot=0;$line_raw_sewing_material_cost_gtot=0;$line_margin_tot=0;$line_margin_gtot=0;$current_smv=0;
		
						foreach ($main_array as $line_id => $job_data){
							foreach ($job_data as $job_key => $po_data) 
							{
								foreach ($po_data as $po_id => $color_data) 
								{
									foreach ($color_data as $color_id => $row) 
									{
										$sweing_qty=$sweing_qty_arr[$line_id][$row['job_no']][$row['po_id']][$color_id];
										$poly_qty=$poly_qty_arr[$line_id][$row['job_no']][$row['po_id']][$color_id];
										$man_power=$prod_resource_array[$line_id][$row['job_no']][$po_id][$color_id]['man_power'];
										$working_hour=$prod_resource_array[$line_id][$row['job_no']][$po_id][$color_id]['working_hour'];	
										if($sweing_qty !=""){
										$avg_smv=$produce_style_sum[$row['style']][$line_id]/$sweing_qty_sum[$row['style']][$line_id];
										}
										if($txt_datefrom==$txt_dateto){	
											if(isset($other_data_arr[$row['style']]['smv'])){
												$current_smv=$other_data_arr[$row['style']]['smv'];
											}else{
												$current_smv=$other_data_arr[$row['style']]['budget_smv'];
											}
										}else{
											$current_smv=$avg_smv;
										}
									// $availble_min=$man_power*$working_hour*60;
									// $other_data_arr[$row['style']]['smv'];
									$availble_min=$man_power*$working_hour*60;
									$production_min=$sweing_qty*$current_smv;
									$fob_value=$sweing_qty*$row['unit_price'];
							
									


								
									
									 $raw_material_cost_psc=$raw_material_all_sum[$row['job_no']][$po_id]/$row['tot_qty'];
									 $raw_material_cost_qty=$sweing_qty*number_format($raw_material_cost_psc, 2,'.','');
									 $margin=$fob_value-$raw_material_cost_qty;

									$po_wise_qty=$sweing_qty*$current_smv/$produce_sum[$line_id];

									$bgcolor = ($sl%2==0) ? "#ffffff" : "#f6faff";

									
							// if($lineArr[$prod_reso_arr[$line_id]] !=""){
							if($sweing_qty !="" || $poly_qty !=""){
						  ?>
						<tbody>
							<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30" align="left"><?=$i;?></td>
								<td width="100" align="left"><?=$row['buyer_name'];?></td>
								<td width="100" align="left"><?=$row['buyer_client'];?></td>
								<td width="100" align="left"><?=$row['style'];?></td>
								<td width="100" align="left"><?=$row['season'];?></td>
								<td width="100" align="left"><?=$row['job_no'];?></td>		
								<td width="150" align="left"><?=implode(", ",array_unique(explode(",",$row['po_number'])));?></td>						
								<td width="100" align="left"><?=$row['item'];?></td>
								<td width="100" align="left"><?=$row['color'];?></td>						
								<td width="100" align="right"><?=$po_qty_arr[$row['job_no']][$row['po_id']][$color_id] ;?></td>
							
								<td width="100" align="left"><? echo $lineArr[$prod_reso_arr[$line_id]]; ?>
							    </td>
								<td width="100" align="right"><?=$sweing_qty ;?></td>
								<td width="100" align="right"><?=$poly_qty ;?></td>
								<td width="100" align="right"><?=number_format($other_data_arr[$row['style']]['budget_smv'], 2,'.','');?></td>
								<td width="100" align="right"><?=number_format($current_smv, 2,'.','');?></td>								
								<td width="100" align="right"><?=number_format($row['unit_price'], 4,'.','') ;?></td>
								<td width="100" align="right"><?=number_format($fob_value, 4,'.','') ;?></td>
								<td width="100" align="right" ><?=fn_number_format($sweing_qty*$current_smv, 2,'.','') ;?></td>
								<td width="100" align="right"><?=fn_number_format($po_wise_qty*$availble_min, 4,'.','') ;?></td>
								<td width="100" align="right"><?=number_format($raw_material_cost_psc, 4,'.','') ;?></td>
								<td width="100" align="right"><?=number_format($raw_material_cost_qty, 4,'.','') ;?></td>
								<td width="100" align="right"><?=number_format($margin, 4,'.','') ;?></td>							
							</tr>
							</tbody>
						
							
					      <?
						 
							 $line_availble_min_gtot+=fn_number_format($po_wise_qty*$availble_min, 4,'.','');
							$line_po_qty_tot+=$row['po_quantity'];$line_po_qty_gtot+=$row['po_quantity'];
							$line_sewing_qnty_qty_tot+=$sweing_qty;$line_sewing_qnty_qty_gtot+=$sweing_qty;
							$line_poly_qnty_qty_tot+=$poly_qty_arr[$line_id][$row['job_no']][$row['po_id']][$color_id];$line_poly_qnty_qty_gtot+=$poly_qty_arr[$line_id][$row['job_no']][$row['po_id']][$color_id];
							$line_set_smv_tot+=$current_smv;$line_set_smv_gtot+=$current_smv;
							$line_budget_smv_tot+=$other_data_arr[$row['style']]['budget_smv'];$line_budget_smv_gtot+=$other_data_arr[$row['style']]['budget_smv'];
							$line_fob_value_tot+=$fob_value;$line_fob_value_gtot+=$fob_value;
							$line_production_min_tot+=$sweing_qty*$current_smv;$line_production_min_gtot+=$sweing_qty*$current_smv;
							$line_availble_min_tot+=$po_wise_qty*$availble_min;
							
							$line_raw_material_cost_tot+=$raw_material_cost_psc;$line_raw_material_cost_gtot+=$raw_material_cost_psc;
							$line_raw_sewing_material_cost_tot+=$raw_material_cost_qty;$line_raw_sewing_material_cost_gtot+=$raw_material_cost_qty;
							$line_margin_tot+=$margin;$line_margin_gtot+=$margin;
							$i++;  
									}
							    	//}
								}
							}
							
				    	}
						if($line_sewing_qnty_qty_tot >0){
							?>
					 
					
							<tr style="background-color:#D3D3D3;">
								   <td width="30"></td>
								   <td width="100"></td>
								   <td width="100"></td>
								   <td width="100"></td>
								   <td width="100"></td>	
								   <td width="100"></td>	
								   <td width="150"></td>				
								   <td width="100"></td>
								   <td width="100"><b>Line wise TTL</b></td>						
								   <td width="100" align="right"><b><?=$line_po_qty_tot ; ;?></b></td>
								   <td width="100"><b><? echo $lineArr[$prod_reso_arr[$line_id]]; ?></b></td>
								   <td width="100" align="right"><b><?=$line_sewing_qnty_qty_tot ; ;?></td>
								   <td width="100" align="right"><b><? if($line_poly_qnty_qty_tot !==0){ echo  $line_poly_qnty_qty_tot;;  }
								
								 ?></td>
								  <td width="100" align="right"><b></b></td>
								   <td width="100" align="right"><b></b></td>
								  
								   <td width="100"></td>
								   <td width="100" align="right"><b><?=number_format($line_fob_value_tot, 4,'.',',') ;?></b></td>
								   <td width="100" align="right"><b><?=fn_number_format($line_production_min_tot, 4,'.',',') ;?></b></td>
								   <td width="100" align="right"><b><?=fn_number_format($line_availble_min_tot, 4,'.',',') ;?></b></td>
								   <td width="100"><b></b></td>
								   <td width="100" align="right"><b><?=number_format($line_raw_sewing_material_cost_tot, 4,'.',',');?></b></td>
								   <td width="100" align="right"><b><?=number_format($line_margin_tot, 4,'.',',') ;?></b></td>
							   
							   </tr> 
									
							   <?
									// $line_availble_min_gtot+=$line_availble_min_tot;
									$line_sewing_qnty_qty_tot =0;$line_poly_qnty_qty_tot=0;$line_set_smv_tot=0;$line_budget_smv_tot=0;$line_fob_value_tot=0;$line_production_min_tot=0;$line_availble_min_tot=0;$line_raw_material_cost_tot=0;$line_raw_sewing_material_cost_tot=0;$line_margin_tot=0;$line_po_qty_tot=0;
							}
					
						}?>
						<tr style="background-color:#D3D3D3;">
							<td width="30"></td>
							<td width="100"></td>
							<td width="100"></td>
							<td width="100"></td>
							<td width="100"></td>	
							<td width="100"></td>	
							<td width="150"></td>							
							<td width="100"></td>
							<td width="100"><b>Grand Total</b></td>						
							<td width="100" align="right"><b><?=$line_po_qty_gtot ; ;?></b></td>
							<td width="100"></td>  
							<td width="100" align="right"><b><?=$line_sewing_qnty_qty_gtot ; ;?></b></td>
							<td width="100" align="right"><b><? if($line_poly_qnty_qty_gtot !==0){ echo $line_poly_qnty_qty_gtot;  }?></td>
							<td width="100" align="right"><b></b></td>
							<td width="100" align="right"><b></b></td>
						
							<td width="100"></td>
							<td width="100" align="right"><b><?=number_format($line_fob_value_gtot, 4,'.',',') ;?></b></td>
							<td width="100" align="right"><b><?=number_format($line_production_min_gtot, 4,'.',',') ;?></b></td>
							<td width="100" align="right"><b><?=fn_number_format($line_availble_min_gtot, 4,'.',',') ;?></b></td>
							<td width="100"><b></b></td>
							<td width="100" align="right"><b><?=number_format($line_raw_sewing_material_cost_gtot, 4,'.',',');?></b></td>
							<td width="100" align="right"><b><?=number_format($line_margin_gtot, 4,'.',',') ;?></b></td>
						
						</tr>
					
						
					</table>
		    	</div>
		    	
	    </div>
	   <?
	}else
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










if($action=="job_popup")
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
	
		$year_field="YEAR(a.insert_date)";
	}
	else if($db_type==2)
	{
		if($job_year!=0) $job_year_cond=" and to_char(a.insert_date,'YYYY')=$job_year"; else $job_year_cond="";
		$select_date=" to_char(a.insert_date,'YYYY')";
	
		$year_field="to_char(a.insert_date,'YYYY')";
	}
	
	
	// $sql = "select a.party_id,a.style_ref_no,a.job_no,a.job_no_prefix_num,$select_date as year from wo_po_details_master a where a.company_name=$company $buyer_cond  $job_year_cond and is_deleted=0 order by job_no_prefix_num"; 
	//echo $sql; die;

	// $sql="select a.id, b.cust_style_ref, a.job_no_prefix_num,$select_date as year from  subcon_ord_mst a, subcon_ord_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.company_id='$company' $buyer_cond $job_year_cond order by a.id desc";	

	$sql= "SELECT a.id, a.job_no_prefix_num, a.job_no, a.company_name,a.buyer_name,a.style_ref_no,$year_field as year 
	from wo_po_details_master a,  wo_po_break_down b 
	where a.job_no=b.job_no_mst and b.status_active in(1,2,3) $job_year_cond 
	group by a.id, a.job_no_prefix_num, a.job_no, a.company_name,a.buyer_name,a.style_ref_no,a.insert_date
	order by a.id desc";
//  echo $sql; 
 //die;
	$buyerArr 	= return_library_array("select id,short_name from lib_buyer","id","short_name"); 
	$arr=array (3=>$buyerArr);
	echo create_list_view("list_view", "Job Year,Style Ref No,Job No,Buyer Name","60,140,90,100","500","510",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "0,0,0,buyer_name", $arr, "year,style_ref_no,job_no_prefix_num,buyer_name", "","setFilterGrid('list_view',-1)","0","",1) ;	
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

?>
