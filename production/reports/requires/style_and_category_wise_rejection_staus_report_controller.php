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
	order by location_name","id,location_name", 0, "-- Select Location --", $selected, "load_drop_down( 'requires/style_and_category_wise_rejection_staus_report_controller', document.getElementById('cbo_working_company_id').value+'_'+this.value, 'load_drop_down_floor', 'floor_td' );",0 );
	exit();    	 
}

if ($action=="load_drop_down_floor")
{
	$ex_data = explode("_", $data);
	echo create_drop_down( "cbo_floor_id", 120, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and production_process in(5,11) 
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
	$date_from = $explode_data[3];
	$date_to = $explode_data[4];
	
	$cond="";
	if($prod_reso_allo==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_array=array();
		
		if($date_from=="" && $date_to=="")
		{
			if($explode_data[1]!="" ) $cond = " and location_id in($explode_data[1])";
			if( $explode_data[2]!="" ) $cond .= " and floor_id in($explode_data[2])";
			$line_data=sql_select("select id, line_number from prod_resource_mst where is_deleted=0 $cond");
			// echo "select id, line_number from prod_resource_mst where is_deleted=0 $cond";
		}
		else
		{
			if( $explode_data[1]!="" ) $cond = " and a.location_id in($explode_data[1])";
			if( $explode_data[2]!="" ) $cond = " and a.floor_id in($explode_data[2])";
		 	if($db_type==0)
		 	{
		 		$data_format="and b.pr_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
		 	}
		 	if($db_type==2)	
		 	{
		 		$data_format="and b.pr_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
		 	}
	
			$line_data=sql_select( "SELECT a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id $data_format and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number order by a.line_number");
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
	$sql = "SELECT a.id,a.po_number,a.job_no_mst,a.grouping,a.file_no,b.style_ref_no,b.job_no_prefix_num,$select_date as year from wo_po_break_down a, wo_po_details_master b where a.job_id=b.id and b.company_name=$lc_company $buyer_cond $job_year_cond  $style_cond and a.status_active in(1,2,3) and b.status_active=1 order by a.id desc"; 
	// echo $sql; die;
	echo create_list_view("list_view", "Order No,Job No,Year,Style Ref No","150,50,70,150","500","310",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "po_number,job_no_prefix_num,year,style_ref_no", "","setFilterGrid('list_view',-1)","0","",1) ;	
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
	$sizeArr  		= return_library_array( "select id, size_name from lib_size",'id','size_name');
	// ================================= GETTING FORM DATA ====================================
	$working_company_id =str_replace("'","",$cbo_working_company_id);
	$location_id 		=str_replace("'","",$cbo_location_id);
	$floor_id 			=str_replace("'","",$cbo_floor_id);
	$line_id 			=str_replace("'","",$cbo_line_id);
	$lc_company_id 		=str_replace("'","",$cbo_lc_company_id);
	$buyer_id 			=str_replace("'","",$cbo_buyer_id);
	$search_by 			=str_replace("'","",$txt_search_by);
	$job_year 			=str_replace("'","",$cbo_job_year);
	$production_type 	=str_replace("'","",$cbo_production_type);
	$order_id 			=str_replace("'","",$txt_order_id);
	$report_title 		=str_replace("'","",$report_title);
    $today_date 		=date("Y-m-d");
	$txt_date_from 		=str_replace("'","",$txt_date_from);
	$txt_date_to 		=str_replace("'","",$txt_date_to);	
	
	//******************************************* MAKE QUERY CONDITION ************************************************
	$sql_cond = "";
	$sql_cond .= ($working_company_id=="") 	? "" : " and d.serving_company in($working_company_id)";
	$sql_cond .= ($location_id=="") 		? "" : " and d.location in($location_id)";
	$sql_cond .= ($floor_id=="") 			? "" : " and d.floor_id in($floor_id)";
	$sql_cond .= ($line_id=="") 			? "" : " and d.sewing_line in($line_id)";
	$sql_cond .= ($lc_company_id==0) 		? "" : " and a.company_name=$lc_company_id";
	$sql_cond .= ($buyer_id==0) 			? "" : " and a.buyer_name=$buyer_id";
	$sql_cond .= ($production_type==0) 		? " and d.production_type in(5,11)" : " and d.production_type=$production_type";
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
	if($type==1) // show button
	{		
		// ================================================ MAIN QUERY ==================================================
		$sql="SELECT a.company_name as LC_COM, a.BUYER_NAME,a.client_id as BUYER_CLIENT,a.style_ref_no as STYLE,a.JOB_NO,a.season_buyer_wise as SEASON,b.id as PO_ID,b.PO_NUMBER,c.item_number_id as ITEM_ID,c.color_number_id as COLOR_ID,c.size_number_id as SIZE_ID,d.FLOOR_ID,d.SEWING_LINE,d.PROD_RESO_ALLO,d.serving_company as WO_COM,d.production_date as PDATE,d.REMARKS,e.REJECT_QTY
		from  wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, pro_garments_production_mst d,pro_garments_production_dtls e  
		where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and b.id=d.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id $sql_cond $sql_cond2 $po_ids_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and d.sewing_line is not null and e.reject_qty>0 and d.sewing_line is not null order by d.production_date,c.size_order";
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
				  <strong>Oh Snap!</strong> Data not available.
				</div>
			</div>
			<?
			die();
		}

		$dataArray = array();
		$sizeDataArray = array();
		$sizeIdArray = array();
		foreach ($sql_res as $val) 
		{
			$sewing_line='';
			if($val['PROD_RESO_ALLO']==1)
			{
				$line_number=explode(",",$prod_reso_arr[$val['SEWING_LINE']]);
				foreach($line_number as $value)
				{
					if($sewing_line=='') $sewing_line=$lineArr[$value]; else $sewing_line.=",".$lineArr[$value];
				}
			}
			else
			{ 
				$sewing_line=$lineArr[$val['SEWING_LINE']];
			}

			$sizeIdArray[$val['SIZE_ID']] = $val['SIZE_ID'];

			$dataArray[$val['PDATE']][$val['LC_COM']][$val['WO_COM']][$val['FLOOR_ID']][$sewing_line][$val['JOB_NO']][$val['PO_ID']][$val['ITEM_ID']][$val['COLOR_ID']]['style'] = $val['STYLE'];
			$dataArray[$val['PDATE']][$val['LC_COM']][$val['WO_COM']][$val['FLOOR_ID']][$sewing_line][$val['JOB_NO']][$val['PO_ID']][$val['ITEM_ID']][$val['COLOR_ID']]['buyer_name'] = $val['BUYER_NAME'];
			$dataArray[$val['PDATE']][$val['LC_COM']][$val['WO_COM']][$val['FLOOR_ID']][$sewing_line][$val['JOB_NO']][$val['PO_ID']][$val['ITEM_ID']][$val['COLOR_ID']]['buyer_client'] = $val['BUYER_CLIENT'];
			$dataArray[$val['PDATE']][$val['LC_COM']][$val['WO_COM']][$val['FLOOR_ID']][$sewing_line][$val['JOB_NO']][$val['PO_ID']][$val['ITEM_ID']][$val['COLOR_ID']]['season'] = $val['SEASON'];
			$dataArray[$val['PDATE']][$val['LC_COM']][$val['WO_COM']][$val['FLOOR_ID']][$sewing_line][$val['JOB_NO']][$val['PO_ID']][$val['ITEM_ID']][$val['COLOR_ID']]['po_number'] = $val['PO_NUMBER'];
			$dataArray[$val['PDATE']][$val['LC_COM']][$val['WO_COM']][$val['FLOOR_ID']][$sewing_line][$val['JOB_NO']][$val['PO_ID']][$val['ITEM_ID']][$val['COLOR_ID']]['remarks'] .= trim($val['REMARKS']).",";
			$dataArray[$val['PDATE']][$val['LC_COM']][$val['WO_COM']][$val['FLOOR_ID']][$sewing_line][$val['JOB_NO']][$val['PO_ID']][$val['ITEM_ID']][$val['COLOR_ID']]['reject_qty'] += $val['REJECT_QTY'];

			$sizeDataArray[$val['PDATE']][$val['LC_COM']][$val['WO_COM']][$val['FLOOR_ID']][$sewing_line][$val['JOB_NO']][$val['PO_ID']][$val['ITEM_ID']][$val['COLOR_ID']][$val['SIZE_ID']] += $val['REJECT_QTY'];
			
		}
		// ======================================== defect data =======================================
		$sqlDft="SELECT a.company_name as LC_COM,a.JOB_NO, b.id as PO_ID,c.item_number_id as ITEM_ID,c.color_number_id as COLOR_ID,d.FLOOR_ID,d.SEWING_LINE,d.PROD_RESO_ALLO,d.serving_company as WO_COM,d.production_date as PDATE,e.PRODUCTION_TYPE,e.DEFECT_TYPE_ID,e.DEFECT_POINT_ID,e.DEFECT_QTY
		from  wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, pro_garments_production_mst d,pro_gmts_prod_dft e  
		where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and b.id=d.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id $sql_cond $sql_cond2 $po_ids_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and d.sewing_line is not null and e.defect_qty>0 and d.sewing_line is not null order by d.production_date";
		// echo $sqlDft;
		$dft_res = sql_select($sqlDft);
		$dftDataArray = array();
		$dftTypeArray = array();
		foreach ($dft_res as $val) 
		{
			$sewing_line='';
			if($val['PROD_RESO_ALLO']==1)
			{
				$line_number=explode(",",$prod_reso_arr[$val['SEWING_LINE']]);
				foreach($line_number as $value)
				{
					if($sewing_line=='') $sewing_line=$lineArr[$value]; else $sewing_line.=",".$lineArr[$value];
				}
			}
			else
			{ 
				$sewing_line=$lineArr[$val['SEWING_LINE']];
			}
			if($val['PRODUCTION_TYPE']==5 && $val['DEFECT_TYPE_ID']==2 || $val['PRODUCTION_TYPE']==11 && $val['DEFECT_TYPE_ID']==3)
			{
				$dftTypeArray[$val['DEFECT_POINT_ID']] = $val['DEFECT_POINT_ID'];

				$dftDataArray[$val['PDATE']][$val['LC_COM']][$val['WO_COM']][$val['FLOOR_ID']][$sewing_line][$val['JOB_NO']][$val['PO_ID']][$val['ITEM_ID']][$val['COLOR_ID']][$val['DEFECT_POINT_ID']] += $val['DEFECT_QTY'];	
			}		
		}

		

		// echo "<pre>"; print_r($dftDataArray); echo "</pre>"; die();
		$table_width = 1340+(count($dftTypeArray)*70)+(count($sizeIdArray)*70);
		$conspan = 15+count($dftTypeArray)+count($sizeIdArray);

		ob_start();
		?>		
		<div class="main" style="margin: 0 auto; padding: 10px;  width: <? echo $table_width+30;?>px">
			<table width="100%" cellspacing="0">
		        <tr class="form_caption" style="border:none;">
		            <td colspan="<? echo $conspan;?>" align="center" ><strong><? echo $companyArr[$working_company_id]; ?></strong></td>
		        </tr>
		        <tr class="form_caption" style="border:none;">
		            <td colspan="<? echo $conspan;?>" align="center"><font size="2"><strong>Style and Category wise Rejection Status Report</strong></font></td>
		        </tr>
		    </table>
		    <div>
		    	<table class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" id="" width="<? echo $table_width;?>" align="left">
		    		<thead>
		    			<tr>
			    			<th rowspan="2" width="40">Date</th>
			    			<th rowspan="2" width="100">Company Name</th>
			    			<th rowspan="2" width="100">Working Company</th>
			    			<th rowspan="2" width="100">Floor</th>
			    			<th rowspan="2" width="70">Line No</th>
			    			<th rowspan="2" width="100">Buyer</th>
			    			<th rowspan="2" width="100">Buyer Client</th>
			    			<th rowspan="2" width="100">Job</th>
			    			<th rowspan="2" width="100">Style</th>
			    			<th rowspan="2" width="100">PO No</th>
			    			<th rowspan="2" width="80">Seaason</th>
			    			<th rowspan="2" width="100">Item</th>
			    			<th rowspan="2" width="90">Color</th>
			    			<th width="<? echo count($sizeIdArray)*70;?>" colspan="<? echo count($sizeIdArray); ?>">Size Name</th>
			    			<th rowspan="2" width="80">Total</th>
			    			<th width="<? echo count($dftTypeArray)*70;?>" colspan="<? echo count($dftTypeArray); ?>">Type of Rejection</th>
			    			<th rowspan="2" width="80">Remarks</th>
			    		</tr>
			    		<tr>
			    			<? 
			    			foreach ($sizeIdArray as $lkey => $lval) 
			    			{
			    				?>
			    				<th title="<? echo $sizeArr[$lval];?>" width="70"><? echo substr($sizeArr[$lval],0,10);?></th>
			    				<?
			    			} 
			    			?>
			    			<? 
			    			foreach ($dftTypeArray as $key => $val) 
			    			{
			    				?>
			    				<th title="<? echo $val;?>" width="70"><? echo substr($sew_fin_reject_type_arr[$val],0,10);?></th>
			    				<?
			    			} 
			    			?>			    			
			    		</tr>
		    		</thead>
		    	</table>
		    	
		    	<div style="width: <? echo $table_width+20;?>px; overflow-y: scroll; max-height: 400px" id="scroll_body">
		    		<table cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all" width="<? echo $table_width;?>" id="html_search" align="left">
		    			<tbody>	
		    			<?
		    			$i=1;
		    			$size_tot_rej_qty_array = array();
		    			$dft_tot_rej_qty_array = array();
		    			foreach ($dataArray as $date_key => $date_data) 
		    			{
		    				foreach ($date_data as $lc_key => $lc_data) 
		    				{
		    					foreach ($lc_data as $wo_key => $wo_data) 
		    					{
		    						foreach ($wo_data as $f_key => $f_data) 
		    						{
		    							foreach ($f_data as $l_key => $l_data) 
		    							{
		    								foreach ($l_data as $job_key => $job_data) 
		    								{
		    									foreach ($job_data as $po_key => $po_data) 
		    									{
		    										foreach ($po_data as $item_key => $item_data) 
		    										{
		    											foreach ($item_data as $color_key => $row) 
		    											{
										    				?>	    				
											    			<tr>
												    			<td width="40"><p><? echo date('d-M',strtotime($date_key));?></p></td>
												    			<td width="100"><p><? echo $companyArr[$lc_key];?></p></td>
												    			<td width="100"><p><? echo $companyArr[$wo_key];?></p></td>
												    			<td width="100"><p><? echo $floorArr[$f_key];?></p></td>
												    			<td width="70"><p><? echo $l_key;?></p></td>
												    			<td width="100"><p><? echo $buyerArr[$row['buyer_name']];?></p></td>
												    			<td width="100"><p><? echo $buyerArr[$row['buyer_client']];?></p></td>
												    			<td width="100"><p><? echo $job_key;?></p></td>
												    			<td width="100"><p><? echo $row['style'];?></p></td>
												    			<td width="100"><p><? echo $row['po_number'];?></p></td>
												    			<td width="80"><p><? echo $row['season'];?></p></td>
												    			<td width="100"><p><? echo $garments_item[$item_key];?></p></td>
												    			<td width="90"><p><? echo $colorArr[$color_key];?></p></td>	
												    			<? 
												    			$tot_rej = 0;
												    			foreach ($sizeIdArray as $skey => $lval) 
												    			{
												    				$sizeRjeQty = 0;
												    				$sizeRjeQty = $sizeDataArray[$date_key][$lc_key][$wo_key][$f_key][$l_key][$job_key][$po_key][$item_key][$color_key][$skey];
												    				?>
												    				<td align="right" title="<? echo $lval;?>" width="70"><? echo $sizeRjeQty ;?></td>
												    				<?
												    				$tot_rej += $sizeRjeQty;
												    				$size_tot_rej_qty_array[$skey] += $sizeRjeQty;
												    			} 
												    			?>
												    			<td width="80" align="right"><p><? echo $tot_rej;?></p></td>
												    			<? 
												    			foreach ($dftTypeArray as $dft_key => $val) 
												    			{
												    				$dftRjeQty = $dftDataArray[$date_key][$lc_key][$wo_key][$f_key][$l_key][$job_key][$po_key][$item_key][$color_key][$dft_key];
												    				?>
												    				<td align="right" title="<? echo $val;?>" width="70"><? echo $dftRjeQty;?></td>
												    				<?
												    				$dft_tot_rej_qty_array[$dft_key] += $dftRjeQty;
												    			} 
												    			?>
												    			<td width="80"><p><? echo implode(", ",array_unique(array_filter(explode(",", $row['remarks']))));?></p></td>
												    		</tr>
												    		<?
												    		$i++;
												    	}
												    }
												}
											}
										}
									}
								}
							}
				    	}
				    	?>
		    			</tbody>
		    		</table>
		    	</div>
		    	<div style="width: <? echo $table_width;?>px;"">
		    		<table cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width;?>" align="left">
		    			<tfoot>
		    				<tr>
				    			<th width="40"></th>
				    			<th width="100"></th>
				    			<th width="100"></th>
				    			<th width="100"></th>
				    			<th width="70"></th>
				    			<th width="100"></th>
				    			<th width="100"></th>
				    			<th width="100"></th>
				    			<th width="100"></th>
				    			<th width="100"></th>
				    			<th width="80"></th>
				    			<th width="100"></th>
				    			<th width="90">Total</th>
				    			<? 
				    			$gt_total = 0;
				    			foreach ($sizeIdArray as $skey => $lval) 
				    			{
				    				?>
				    				<th title="<? echo $sizeArr[$lval];?>" width="70"><? echo $size_tot_rej_qty_array[$skey];?></th>
				    				<?
				    				$gt_total += $size_tot_rej_qty_array[$skey];
				    			} 
				    			?>
				    			<th width="80"><? echo $gt_total; ?></th>
				    			<? 
				    			foreach ($dftTypeArray as $dftkey => $val) 
				    			{
				    				?>
				    				<th title="<? echo $val;?>" width="70"><? echo $dft_tot_rej_qty_array[$dftkey];?></th>
				    				<?
				    			} 
				    			?>
				    			<th width="80"></th>
				    		</tr>
		    			</tfoot>
		    		</table>
		    </div>
	    </div>
	   <?
	}
	elseif($type==2) // summary button
	{		
		// ================================================ MAIN QUERY ==================================================
		$sql="SELECT a.company_name as LC_COM, a.BUYER_NAME,a.client_id as BUYER_CLIENT,a.style_ref_no as STYLE,a.JOB_NO,a.season_buyer_wise as SEASON,b.id as PO_ID,b.PO_NUMBER,c.item_number_id as ITEM_ID,c.color_number_id as COLOR_ID,c.size_number_id as SIZE_ID,d.FLOOR_ID,d.SEWING_LINE,d.PROD_RESO_ALLO,d.serving_company as WO_COM,d.production_date as PDATE,d.REMARKS,e.REJECT_QTY
		from  wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, pro_garments_production_mst d,pro_garments_production_dtls e  
		where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and b.id=d.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id $sql_cond $sql_cond2 $po_ids_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and d.sewing_line is not null and e.reject_qty>0 and d.sewing_line is not null order by d.production_date,c.size_order";
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
				  <strong>Oh Snap!</strong> Data not available.
				</div>
			</div>
			<?
			die();
		}

		$dataArray = array();
		$sizeDataArray = array();
		$sizeIdArray = array();
		foreach ($sql_res as $val) 
		{
			$sizeIdArray[$val['SIZE_ID']] = $val['SIZE_ID'];

			$dataArray[$val['LC_COM']][$val['WO_COM']][$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']]['style'] = $val['STYLE'];
			$dataArray[$val['LC_COM']][$val['WO_COM']][$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']]['buyer_name'] = $val['BUYER_NAME'];
			$dataArray[$val['LC_COM']][$val['WO_COM']][$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']]['buyer_client'] = $val['BUYER_CLIENT'];
			$dataArray[$val['LC_COM']][$val['WO_COM']][$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']]['season'] = $val['SEASON'];
			$dataArray[$val['LC_COM']][$val['WO_COM']][$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']]['po_number'] = $val['PO_NUMBER'];
			$dataArray[$val['LC_COM']][$val['WO_COM']][$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']]['remarks'] .= $val['REMARKS'].",";
			$dataArray[$val['LC_COM']][$val['WO_COM']][$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']]['reject_qty'] += $val['REJECT_QTY'];

			$sizeDataArray[$val['LC_COM']][$val['WO_COM']][$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']][$val['SIZE_ID']] += $val['REJECT_QTY'];
			
		}
		// ======================================== defect data =======================================
		$sqlDft="SELECT a.company_name as LC_COM,a.JOB_NO, b.id as PO_ID,c.item_number_id as ITEM_ID,c.color_number_id as COLOR_ID,d.FLOOR_ID,d.SEWING_LINE,d.PROD_RESO_ALLO,d.serving_company as WO_COM,d.production_date as PDATE,e.PRODUCTION_TYPE,e.DEFECT_TYPE_ID,e.DEFECT_POINT_ID,e.DEFECT_QTY
		from  wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, pro_garments_production_mst d,pro_gmts_prod_dft e  
		where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and b.id=d.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id $sql_cond $sql_cond2 $po_ids_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and d.sewing_line is not null and e.defect_qty>0 and d.sewing_line is not null order by d.production_date";
		// echo $sqlDft;
		$dft_res = sql_select($sqlDft);
		$dftDataArray = array();
		$dftTypeArray = array();
		foreach ($dft_res as $val) 
		{
			if($val['PRODUCTION_TYPE']==5 && $val['DEFECT_TYPE_ID']==2 || $val['PRODUCTION_TYPE']==11 && $val['DEFECT_TYPE_ID']==3)
			{
				$dftTypeArray[$val['DEFECT_POINT_ID']] = $val['DEFECT_POINT_ID'];
				$dftDataArray[$val['LC_COM']][$val['WO_COM']][$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']][$val['DEFECT_POINT_ID']] += $val['DEFECT_QTY'];
			}			
		}

		

		// echo "<pre>"; print_r($dftDataArray); echo "</pre>"; die();
		$table_width = 950+(count($dftTypeArray)*70)+(count($sizeIdArray)*70);
		$conspan = 15+count($dftTypeArray)+count($sizeIdArray);

		ob_start();
		?>		
		<div class="main" style="margin: 0 auto; padding: 10px;  width: <? echo $table_width+30;?>px">
			<table width="100%" cellspacing="0">
		        <tr class="form_caption" style="border:none;">
		            <td colspan="<? echo $conspan;?>" align="center" ><strong><? echo $companyArr[$working_company_id]; ?></strong></td>
		        </tr>
		        <tr class="form_caption" style="border:none;">
		            <td colspan="<? echo $conspan;?>" align="center"><font size="2"><strong>Style and Category wise Rejection Status Report</strong></font></td>
		        </tr>
		    </table>
		    <div>
		    	<table class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" id="" width="<? echo $table_width;?>" align="left">
		    		<thead>
		    			<tr>			    			
			    			<th rowspan="2" width="100">Company Name</th>
			    			<th rowspan="2" width="100">Working Company</th>			    			
			    			<th rowspan="2" width="100">Buyer</th>
			    			<th rowspan="2" width="100">Buyer Client</th>
			    			<th rowspan="2" width="100">Job</th>
			    			<th rowspan="2" width="100">Style</th>	
			    			<th rowspan="2" width="100">Item</th>
			    			<th rowspan="2" width="90">Color</th>
			    			<th width="<? echo count($sizeIdArray)*70;?>" colspan="<? echo count($sizeIdArray); ?>">Size Name</th>
			    			<th rowspan="2" width="80">Total</th>
			    			<th width="<? echo count($dftTypeArray)*70;?>" colspan="<? echo count($dftTypeArray); ?>">Type of Rejection</th>
			    			<th rowspan="2" width="80">Remarks</th>
			    		</tr>
			    		<tr>
			    			<? 
			    			foreach ($sizeIdArray as $lkey => $lval) 
			    			{
			    				?>
			    				<th title="<? echo $sizeArr[$lval];?>" width="70"><? echo substr($sizeArr[$lval],0,10);?></th>
			    				<?
			    			} 
			    			?>
			    			<? 
			    			foreach ($dftTypeArray as $key => $val) 
			    			{
			    				?>
			    				<th title="<? echo $val;?>" width="70"><? echo substr($sew_fin_reject_type_arr[$val],0,10);?></th>
			    				<?
			    			} 
			    			?>			    			
			    		</tr>
		    		</thead>
		    	</table>
		    	
		    	<div style="width: <? echo $table_width+20;?>px; overflow-y: scroll; max-height: 400px" id="scroll_body">
		    		<table cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all" width="<? echo $table_width;?>" id="html_search" align="left">
		    			<tbody>	
		    			<?
		    			$i=1;
		    			$size_tot_rej_qty_array = array();
		    			$dft_tot_rej_qty_array = array();
		    			
	    				foreach ($dataArray as $lc_key => $lc_data) 
	    				{
	    					foreach ($lc_data as $wo_key => $wo_data) 
	    					{	    						
								foreach ($wo_data as $job_key => $job_data) 
								{									
									foreach ($job_data as $item_key => $item_data) 
									{
										foreach ($item_data as $color_key => $row) 
										{
						    				?>	    				
							    			<tr>
								    			<td width="100"><p><? echo $companyArr[$lc_key];?></p></td>
								    			<td width="100"><p><? echo $companyArr[$wo_key];?></p></td>
								    			<td width="100"><p><? echo $buyerArr[$row['buyer_name']];?></p></td>
								    			<td width="100"><p><? echo $buyerArr[$row['buyer_client']];?></p></td>
								    			<td width="100"><p><? echo $job_key;?></p></td>
								    			<td width="100"><p><? echo $row['style'];?></p></td>
								    			<td width="100"><p><? echo $garments_item[$item_key];?></p></td>
								    			<td width="90"><p><? echo $colorArr[$color_key];?></p></td>	
								    			<? 
								    			$tot_rej = 0;
								    			foreach ($sizeIdArray as $skey => $lval) 
								    			{
								    				$sizeRjeQty = 0;
								    				$sizeRjeQty = $sizeDataArray[$lc_key][$wo_key][$job_key][$item_key][$color_key][$skey];
								    				?>
								    				<td align="right" title="<? echo $lval;?>" width="70"><? echo $sizeRjeQty ;?></td>
								    				<?
								    				$tot_rej += $sizeRjeQty;
								    				$size_tot_rej_qty_array[$skey] += $sizeRjeQty;
								    			} 
								    			?>
								    			<td width="80" align="right"><p><? echo $tot_rej;?></p></td>
								    			<? 
								    			foreach ($dftTypeArray as $dft_key => $val) 
								    			{
								    				$dftRjeQty = $dftDataArray[$lc_key][$wo_key][$job_key][$item_key][$color_key][$dft_key];
								    				?>
								    				<td align="right" title="<? echo $val;?>" width="70"><? echo $dftRjeQty;?></td>
								    				<?
								    				$dft_tot_rej_qty_array[$dft_key] += $dftRjeQty;
								    			} 
								    			?>
								    			<td width="80"><p><? echo implode(", ",array_unique(array_filter(explode(",", $row['remarks']))));?></p></td>
								    		</tr>
								    		<?
								    		$i++;
								    	}
								    }
									
								}
							}
						}
				    	
				    	?>
		    			</tbody>
		    		</table>
		    	</div>
		    	<div style="width: <? echo $table_width;?>px;"">
		    		<table cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width;?>" align="left">
		    			<tfoot>
		    				<tr>
				    			<th width="100"></th>
				    			<th width="100"></th>
				    			<th width="100"></th>
				    			<th width="100"></th>
				    			<th width="100"></th>
				    			<th width="100"></th>
				    			<th width="100"></th>
				    			<th width="90">Total</th>
				    			<? 
				    			$gt_total = 0;
				    			foreach ($sizeIdArray as $skey => $lval) 
				    			{
				    				?>
				    				<th title="<? echo $sizeArr[$lval];?>" width="70"><? echo $size_tot_rej_qty_array[$skey];?></th>
				    				<?
				    				$gt_total += $size_tot_rej_qty_array[$skey];
				    			} 
				    			?>
				    			<th width="80"><? echo $gt_total; ?></th>
				    			<? 
				    			foreach ($dftTypeArray as $dftkey => $val) 
				    			{
				    				?>
				    				<th title="<? echo $val;?>" width="70"><? echo $dft_tot_rej_qty_array[$dftkey];?></th>
				    				<?
				    			} 
				    			?>
				    			<th width="80"></th>
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
	if($type==8 || $type==11)
	{
		$line_cond = "";
	}
	else
	{
		$line_cond = "and a.sewing_line in($sewing_line_id)";
	}
	switch ($level) 
	{
		case 1:
			$sql = "SELECT c.color_number_id,c.size_number_id,sum(b.production_qnty) as production_qnty,a.production_date from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c WHERE a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and a.po_break_down_id=$po_id and c.item_number_id=$item_id and c.country_id=$country_id and c.color_number_id=$color_id and a.production_date between '$date_from' and '$date_to' and a.production_type=$type $line_cond and a.status_active=1 and c.status_active=1
			group by c.color_number_id,c.size_number_id,a.production_date order by c.size_number_id";
			break;

		case 2:
			$sql = "SELECT c.color_number_id,c.size_number_id,sum(b.production_qnty) as production_qnty,a.production_date from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c WHERE a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and a.po_break_down_id=$po_id and c.item_number_id=$item_id and c.country_id=$country_id and c.color_number_id=$color_id and a.production_date<='$date_to' and a.production_type=$type $line_cond and a.status_active=1 and c.status_active=1
			group by c.color_number_id,c.size_number_id,a.production_date order by c.size_number_id";
			break;
		case 3:
			$sql = "SELECT c.color_number_id,c.size_number_id,sum(case when b.is_rescan=0 then b.reject_qty else 0 end) - sum(case when b.is_rescan=1 then b.production_qnty else 0 end) as production_qnty,a.production_date from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c WHERE a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and a.po_break_down_id=$po_id and c.item_number_id=$item_id and c.country_id=$country_id and c.color_number_id=$color_id and a.production_type=$type $line_cond and a.status_active=1 and c.status_active=1
			group by c.color_number_id,c.size_number_id,a.production_date order by c.size_number_id"; //and a.production_date between '$date_from' and '$date_to'
			break;
		case 4:
			$sql = "SELECT c.color_number_id,c.size_number_id,sum(b.production_qnty) as production_qnty,a.production_date from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c WHERE a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and a.po_break_down_id=$po_id and c.item_number_id=$item_id and c.country_id=$country_id and c.color_number_id=$color_id and a.production_date<'$date_from' and a.production_type=$type $line_cond and a.status_active=1 and c.status_active=1
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
	if($type==8 || $type==11)
	{
		$line_cond = "";
	}
	else
	{
		$line_cond = "and a.sewing_line in($sewing_line_id)";
	}
	switch ($level) 
	{
		case 1:
			$sql = "SELECT c.color_number_id,c.size_number_id,sum(b.production_qnty) as production_qnty,a.production_date 
			from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
			WHERE a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and a.po_break_down_id in($po_id) and c.item_number_id in($item_id)  and c.color_number_id in($color_id) and a.production_date between '$date_from' and '$date_to' and a.production_type=$type $line_cond and a.status_active=1 and c.status_active=1
			group by c.color_number_id,c.size_number_id,a.production_date 
			order by c.size_number_id";
			break;

		case 2:
			$sql = "SELECT c.color_number_id,c.size_number_id,sum(b.production_qnty) as production_qnty,a.production_date 
			from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
			WHERE a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and a.po_break_down_id in($po_id) and c.item_number_id in($item_id)  and c.color_number_id in($color_id) and a.production_date<='$date_to' and a.production_type=$type $line_cond and a.status_active=1 and c.status_active=1
			group by c.color_number_id,c.size_number_id,a.production_date 
			order by c.size_number_id";
			break;
		case 3:
			$sql = "SELECT 0 as production_date, b.bundle_no, c.color_number_id,c.size_number_id,sum(case when b.is_rescan=0 and a.production_type=$type  then b.reject_qty else 0 end) as reject_qty , sum(case when b.is_rescan=1 and a.production_type=$type then b.production_qnty else 0 end) as production_qnty 
			from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
			WHERE a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and a.po_break_down_id in($po_id) and c.item_number_id in($item_id)  and c.color_number_id in($color_id) and a.production_type=$type  $line_cond and a.status_active=1 and c.status_active=1
			group by b.bundle_no,c.color_number_id,c.size_number_id 
			order by c.size_number_id"; // and a.production_date between '$date_from' and '$date_to'
			break;
		case 4:
			$sql = "SELECT c.color_number_id,c.size_number_id,sum(b.production_qnty) as production_qnty,a.production_date from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c WHERE a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and a.po_break_down_id in($po_id) and c.item_number_id in($item_id)  and c.color_number_id in($color_id) and a.production_date<'$date_from' and a.production_type=$type $line_cond and a.status_active=1 and c.status_active=1
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
	if($type==8 || $type==11)
	{
		$line_cond = "";
	}
	else
	{
		$line_cond = "and a.sewing_line in($sewing_line_id)";
	}
	switch ($level) 
	{
		case 5:
			 $sql = "SELECT c.color_number_id,c.size_number_id,SUM (CASE WHEN a.production_type = 4  THEN b.production_qnty  ELSE 0 END)
         - (SUM ( CASE  WHEN a.production_type = 5   THEN b.production_qnty ELSE 0 END)
         + (SUM (CASE WHEN a.production_type = 5 and b.is_rescan=0  THEN  b.reject_qty ELSE  0 END) - sum(CASE WHEN a.production_type =5 and b.is_rescan = 1 THEN b.production_qnty else 0 END))) AS production_qnty 
			from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
			WHERE a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and a.po_break_down_id in($po_id) and c.item_number_id=$item_id and c.country_id=$country_id  and c.color_number_id=$color_id and a.production_type in(4,5) $line_cond and a.status_active=1 and c.status_active=1
			group by c.color_number_id,c.size_number_id 
			order by c.size_number_id";
			break;

		case 6:
			$sql = "SELECT c.color_number_id,c.size_number_id,SUM (CASE WHEN a.production_type = 5 AND a.production_date <= '$date_to' THEN b.production_qnty  ELSE 0 END)
         - (SUM ( CASE  WHEN a.production_type = 11 AND a.production_date <= '$date_to' THEN b.production_qnty ELSE 0 END)
         + SUM (CASE WHEN a.production_type = 11  THEN  b.reject_qty ELSE  0 END)) AS production_qnty 
			from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
			WHERE a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and a.po_break_down_id in($po_id) and c.item_number_id=$item_id and c.country_id=$country_id and c.color_number_id=$color_id and a.production_type in(5,11) $line_cond and a.status_active=1 and c.status_active=1
			group by c.color_number_id,c.size_number_id 
			order by c.size_number_id";
			break;
		case 7:
			$sql = "SELECT c.color_number_id,c.size_number_id,SUM (CASE WHEN a.production_type = 4 AND a.production_date <= '$date_to' THEN b.production_qnty  ELSE 0 END)
         - (SUM ( CASE  WHEN a.production_type = 11 AND a.production_date <= '$date_to' THEN b.production_qnty ELSE 0 END)
         + SUM (CASE WHEN a.production_type = 11  THEN  b.reject_qty ELSE  0 END)) AS production_qnty 
			from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
			WHERE a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and a.po_break_down_id in($po_id) and c.item_number_id=$item_id and c.country_id=$country_id  and c.color_number_id=$color_id and a.production_type in(4,11) $line_cond and a.status_active=1 and c.status_active=1
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
	if($type==8 || $type==11)
	{
		$line_cond = "";
	}
	else
	{
		$line_cond = "and a.sewing_line in($sewing_line_id)";
	}
	switch ($level) 
	{
		case 5:
			$sql = "SELECT b.bundle_no, c.color_number_id,c.size_number_id,SUM (CASE WHEN a.production_type = 4 AND a.production_date <= '$date_to' THEN b.production_qnty  ELSE 0 END) as ttl_input,SUM ( CASE  WHEN a.production_type = 5 AND a.production_date <= '$date_to' THEN b.production_qnty ELSE 0 END) as ttl_output ,SUM (CASE WHEN a.production_type = 5 and b.is_rescan=0  THEN  b.reject_qty ELSE  0 END) as reject_qty , sum(CASE WHEN a.production_type =5 and b.is_rescan = 1 THEN b.production_qnty else 0 END) AS production_qnty 
			from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
			WHERE a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and a.po_break_down_id in($po_id) and c.item_number_id=$item_id  and c.color_number_id=$color_id and a.production_type in(4,5) $line_cond and a.status_active=1 and c.status_active=1
			group by b.bundle_no,c.color_number_id,c.size_number_id 
			order by c.size_number_id";
			break;

		case 6:
			$sql = "SELECT c.color_number_id,c.size_number_id,SUM (CASE WHEN a.production_type = 5 AND a.production_date <= '$date_to' THEN b.production_qnty  ELSE 0 END)
         - (SUM ( CASE  WHEN a.production_type = 11 AND a.production_date <= '$date_to' THEN b.production_qnty ELSE 0 END)
         + SUM (CASE WHEN a.production_type = 11  THEN  b.reject_qty ELSE  0 END)) AS production_qnty 
			from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
			WHERE a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and a.po_break_down_id in($po_id) and c.item_number_id=$item_id  and c.color_number_id=$color_id and a.production_type in(5,11) $line_cond and a.status_active=1 and c.status_active=1
			group by c.color_number_id,c.size_number_id 
			order by c.size_number_id";
			break;
		case 7:
			$sql = "SELECT c.color_number_id,c.size_number_id,SUM (CASE WHEN a.production_type = 4 AND a.production_date <= '$date_to' THEN b.production_qnty  ELSE 0 END)
         - (SUM ( CASE  WHEN a.production_type = 11 AND a.production_date <= '$date_to' THEN b.production_qnty ELSE 0 END)
         + SUM (CASE WHEN a.production_type = 11 THEN  b.reject_qty ELSE  0 END)) AS production_qnty 
			from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c 
			WHERE a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and a.po_break_down_id in($po_id) and c.item_number_id=$item_id  and c.color_number_id=$color_id and a.production_type in(4,11) $line_cond and a.status_active=1 and c.status_active=1
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