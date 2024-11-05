<? 
header('Content-type:text/html; charset=utf-8');
session_start();

if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$user_id = $_SESSION['logic_erp']['user_id'];

require_once('../../../includes/common.php');
if (!function_exists("pre")) 
{
	function pre($array){
		echo "<pre>";
		print_r($array);
		echo "</pre>";
	} 	 
}

if (!function_exists("fn_num_format")) 
{
	function fn_num_format($val,$after_point=0, $default_value=0){
		if(is_nan($val) || is_infinite($val)){return $default_value;} 
		else{return number_format($val,$after_point);}
	}
}

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
 
   
//--------------------------------------------------------------------------------------------------------------------
if ($action=="load_drop_down_buyer")
{  
	echo create_drop_down( "cbo_buyer_name", 150, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "" );     	 
	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 150, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id in('$data') 
	order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/cutting_to_input_report_controller', document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_line','line_id' );get_php_form_data(this.value, 'eval_multi_select', 'requires/cutting_to_input_report_controller' );",0 );    	 
}

if ($action=="load_drop_down_line")
{ 
	$explode_data = explode("_",$data);
	// print_r($explode_data);die;
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name in($explode_data[0]) and variable_list=23 and is_deleted=0 and status_active=1");
	// $txt_date = $explode_data[3];
	
	$cond="";
	if($prod_reso_allo==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_array=array(); 
		if( $explode_data[0]!=0) $cond = " and a.company_id in($explode_data[0])";
		if($explode_data[1]!=0 ) $cond = " and a.location_id in($explode_data[1])";
		
		$line_data=sql_select( "select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number order by a.line_number"); 
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

		echo create_drop_down( "cbo_line_id", 150,$line_array,"", "", "-- Select --", $selected, "",0,0 );
	}
	else
	{
		if( $explode_data[0]!=0) $cond = " and company_id in($explode_data[0])";
		if($explode_data[1]!=0 ) $cond = " and location_name in($explode_data[1])";
		// if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_name in($explode_data[1])";
		// if( $explode_data[0]!=0 ) $cond = " and floor_name in($explode_data[0])";
		// echo "select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name";
		echo create_drop_down( "cbo_line_id", 150, "select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name","id,line_name", "", "-- Select --", $selected, "",0,0 );
	}
	exit(); 	 
}

if ($action == "eval_multi_select") 
{
    echo "set_multiselect('cbo_line_id','0','0','','0');\n";
    exit();
}
 
if($action=="report_generate")
{ 	 
	$process = array(&$_POST);
	// pre($process);die;
	extract(check_magic_quote_gpc( $process ));
	// echo $cbo_location;die;

	//====================== load library ======================== 
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
 	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
 	$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  );
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  ); 
	$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name"  ); 
	$season_lib=return_library_array( "select id,season_name from lib_buyer_season", "id", "season_name"  );

	// $lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name");
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$lineDataArr = sql_select("select id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1
	order by sewing_line_serial"); 
	foreach($lineDataArr as $lRow)
	{
		$lineArr[$lRow['ID']]=$lRow['LINE_NAME'];
		$lineSerialArr[$lRow['ID']]=$lRow['SEWING_LINE_SERIAL'];
		// $lastSlNo=$lRow['SEWING_Line_SERIAL'];
	}
	// echo"<pre>";print_r($prod_reso_arr);
	$company_id=str_replace("'","",$cbo_company_name);
	$location_id=str_replace("'","",$cbo_location);
	$buyer_id=str_replace("'","",$cbo_buyer_name);
	$line_id=str_replace("'","",$cbo_line_id);
	$common_id=str_replace("'","",$txt_search_common);
	$search_by=str_replace("'","",$cbo_search_by); 
	$date=str_replace("'","",$txt_date); 
	$year=str_replace("'","",$cbo_year); 
	if ( !$date)
	{ 
		$prod_date =  "'".date('d-M-Y')."'";
	}else{
		$prod_date = $txt_date;
	} 
	

	$sql_cond = ""; 
	$sql_cond .= ($company_id==0) ? "" : " and a.company_name=$cbo_company_name";
	$sql_cond .= ($location_id==0) ? "" : " and a.location_name=$cbo_location";
	$sql_cond .= ($buyer_id==0) ? "" : " and a.buyer_name=$cbo_buyer_name";
	$sql_cond .= ($line_id==0) ? "" : " and d.sewing_line in($line_id)";
	$search_string = "'%" . trim($common_id) . "%'";
	$year_cond  = '';
	if($year)
	{  
		$year_cond = "and to_char(a.insert_date,'YYYY')='$year'";	
	}
	if ($search_by && $common_id) 
	{  
		if( $search_by == 1)
		{
			$sql_cond .= "and a.style_ref_no like  $search_string";
		}else if ( $search_by == 2)
		{
			$sql_cond .= "and a.job_no_prefix_num = $txt_search_common";
		}else if ($search_by == 3)
		{
			$sql_cond .= "and b.po_number = $txt_search_common";
		}
	} 
	 
	// $sql_cond.= "and b.pub_shipment_date between '".change_date_format('18-FEB-2023','dd-mm-yyyy','-',1)."' and '".change_date_format('20-FEB-2023','dd-mm-yyyy','-',1)."'" ;
	// echo $sql_cond;die();	// ============================================================================================================
	//												PO ID IN DATE RANGE
	// ============================================================================================================
	$break_down_id_cond=''; 
	if(str_replace("'", "", $date) !="")
	{ 
		$pro_po_sql = "SELECT a.po_break_down_id  FROM pro_garments_production_mst a WHERE a.status_active=1 and a.is_deleted = 0 and a.production_date = $prod_date and a.company_id=$company_id and a.production_type = 4";
		// echo $pro_po_sql;die;
		$pro_po_res = sql_select($pro_po_sql); 
		$po_break_down_id_arr = [];
		foreach ($pro_po_res as $v) {
			$po_break_down_id_arr[$v['PO_BREAK_DOWN_ID']] = $v['PO_BREAK_DOWN_ID'];
		}
		$break_down_id_cond = where_con_using_array($po_break_down_id_arr,0,"d.po_break_down_id");
	}	
	// ============================================================================================================
	//												PRODUCTION DATA
	// ============================================================================================================

	$sql= "SELECT d.sewing_line,a.buyer_name,a.job_no,a.style_ref_no as style,a.season_buyer_wise as season,b.po_number,b.id as po_id,c.color_number_id as color,c.item_number_id as item,to_char(b.pub_shipment_date,'DD-MON') as ship_date,d.production_type as prod_type,to_char(d.production_date,'DD-MON') as prod_date,e.bundle_no,d.embel_name,(case when d.production_date=$prod_date then e.production_qnty else 0 end ) as today_prod_qty ,e.production_qnty  as total_prod_qty, d.prod_reso_allo,d.serving_company,d.production_source as source FROM wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c ,pro_garments_production_mst d,pro_garments_production_dtls e WHERE a.id=b.job_id and b.id=c.po_break_down_id and b.id=d.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and d.production_type in (1,2,3,4,5) and e.bundle_no is not null $sql_cond $year_cond $break_down_id_cond";  
	// echo $sql; die;
	$sql_res = sql_select($sql); 
	// pre($sql_res);die;
	$sew_in_array =array();
	$prod_array =array();
	$bundle_wise_line_arr =array();
	$po_id_arr =array();
	$color_size_break_down_id_arr =array();
	$line_name_arr = array();
	$print_emb_status_arr = array();

	// echo $lineArr[981]; die;
	foreach ($sql_res as $v) 
	{
		if($v['PROD_RESO_ALLO']==1)
		{
			$line_name = "";
			// echo $prod_reso_arr[$v['SEWING_LINE']]."<br>";
			$sewing_line_id_arr=explode(",",$prod_reso_arr[$v['SEWING_LINE']]);
			foreach ($sewing_line_id_arr as $r) 
			{					
				// echo $prod_reso_arr[$v['SEWING_LINE']]."<br>";
				$line_name .= ($line_name=="") ? $lineArr[$r] : ",". $lineArr[$r];
			}
			$sewing_line_id = $sewing_line_id_arr[0];
		} 
		else
		{
			$sewing_line_id=$v['SEWING_LINE'];
			$line_name=$lineArr[$v['SEWING_LINE']];
		}
		
		if($lineSerialArr[$sewing_line_id]=="")
		{
			$lastSlNo++;
			$slNo=$lastSlNo;
			$lineSerialArr[$sewing_line_id]=$slNo;
		}
		else
		{
			$slNo=$lineSerialArr[$sewing_line_id];
		}
		$line_name_arr[$v['SEWING_LINE']] = $line_name;
		if($v['PROD_TYPE'] == 4)
		{   
			

			// echo $v['SEWING_LINE']."<br>";

			$po_id_arr[$v['PO_ID']]  = $v['PO_ID']; 
			$bundle_wise_line_arr[$v['BUNDLE_NO']] = $v['SEWING_LINE'];
			$sew_in_array [$slNo][$v['SEWING_LINE']][$v['PO_ID']][$v['JOB_NO']][$v['COLOR']][$v['ITEM']]['STYLE'] = $v['STYLE'];
			$sew_in_array [$slNo][$v['SEWING_LINE']][$v['PO_ID']][$v['JOB_NO']][$v['COLOR']][$v['ITEM']]['BUYER_NAME'] = $v['BUYER_NAME'];
			$sew_in_array [$slNo][$v['SEWING_LINE']][$v['PO_ID']][$v['JOB_NO']][$v['COLOR']][$v['ITEM']]['SEASON'] = $v['SEASON'];
			$sew_in_array [$slNo][$v['SEWING_LINE']][$v['PO_ID']][$v['JOB_NO']][$v['COLOR']][$v['ITEM']]['PO_NUMBER'] = $v['PO_NUMBER'];  
			$sew_in_array [$slNo][$v['SEWING_LINE']][$v['PO_ID']][$v['JOB_NO']][$v['COLOR']][$v['ITEM']]['SHIP_DATE'] = $v['SHIP_DATE'];
			$sew_in_array [$slNo][$v['SEWING_LINE']][$v['PO_ID']][$v['JOB_NO']][$v['COLOR']][$v['ITEM']]['TODAY_PROD_QTY'] += $v['TODAY_PROD_QTY'];
			$sew_in_array [$slNo][$v['SEWING_LINE']][$v['PO_ID']][$v['JOB_NO']][$v['COLOR']][$v['ITEM']]['TOTAL_PROD_QTY'] += $v['TOTAL_PROD_QTY'];
			$sew_in_array [$slNo][$v['SEWING_LINE']][$v['PO_ID']][$v['JOB_NO']][$v['COLOR']][$v['ITEM']]['BUNDLE_NO'] = $v['BUNDLE_NO'];
			$sew_in_array [$slNo][$v['SEWING_LINE']][$v['PO_ID']][$v['JOB_NO']][$v['COLOR']][$v['ITEM']]['SEW_LINE'] = $line_name; 

			if($date  && strtolower($v['PROD_DATE']) == strtolower(date("d-M", strtotime($date))) ){
				$sew_in_array [$slNo][$v['SEWING_LINE']][$v['PO_ID']][$v['JOB_NO']][$v['COLOR']][$v['ITEM']]['INPUT_DATE'] = $v['PROD_DATE']; 
			}
			if(!$date){
				$sew_in_array [$slNo][$v['SEWING_LINE']][$v['PO_ID']][$v['JOB_NO']][$v['COLOR']][$v['ITEM']]['INPUT_DATE'] = $v['PROD_DATE']; 
			}
		

		}
		if ($v['PROD_TYPE'] == 5)
		 {
			$sew_out_array[$slNo][$v['SEWING_LINE']][$v['PO_ID']][$v['JOB_NO']][$v['COLOR']][$v['ITEM']]['TODAY_PROD_QTY'] += $v['TODAY_PROD_QTY'];
			$sew_out_array[$slNo][$v['SEWING_LINE']][$v['PO_ID']][$v['JOB_NO']][$v['COLOR']][$v['ITEM']]['TOTAL_PROD_QTY'] += $v['TOTAL_PROD_QTY'];
		} 
		// For Production Type 1,2,3
		if (in_array($v['PROD_TYPE'],[1,2,3]) ) 
		{
			$prod_array[$v['PO_ID']][$v['JOB_NO']][$v['COLOR']][$v['ITEM']][$v['PROD_TYPE']][$v['EMBEL_NAME']] ['TODAY_PROD_QTY'] += $v['TODAY_PROD_QTY'];
			$prod_array[$v['PO_ID']][$v['JOB_NO']][$v['COLOR']][$v['ITEM']][$v['PROD_TYPE']][$v['EMBEL_NAME']] ['TOTAL_PROD_QTY'] += $v['TOTAL_PROD_QTY'];
			
			if ($v['PROD_TYPE'] == 2 && $v['EMBEL_NAME']==2 ) // BUNDLE ISSUED TO EMBROIDERY  SERVING_COMPANY
			{
				if($v['SOURCE']==1) // inhouse
				{  
					
					$print_emb_status_arr[$v['PO_ID']][$v['COLOR']][$v['ITEM']]['SUPPLIER'] [$company_library[$v['SERVING_COMPANY']]] = $company_library[$v['SERVING_COMPANY']]; 
				}
				else
				{
					$print_emb_status_arr[$v['PO_ID']][$v['COLOR']][$v['ITEM']]['SUPPLIER'][$supplier_library[$v['SERVING_COMPANY']]] = $supplier_library[$v['SERVING_COMPANY']];  
					
				} 
			}
		}
	}   
	ksort($sew_in_array);
	// pre($sew_out_array);
	// ============================================================================================================
	//												DELETE ORDER ID FROM TEMP ENGINE
	// ============================================================================================================
	$con = connect();
	execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form = 124 and ref_from =1");
	oci_commit($con);  

	// ============================================================================================================
	//												INSERT ORDER_ID INTO TEMP ENGINE
	// ============================================================================================================
	fnc_tempengine("gbl_temp_engine", $user_id, 124, 1,$po_id_arr, $empty_arr);  


	// ============================================================================================================
	//												ORDER QUANTITY 
	// ============================================================================================================
    $po_qty_sql = "SELECT  a.po_break_down_id as po_id, a.job_no_mst as job_no,a.item_number_id as item,a.color_number_id as color,a.order_quantity FROM wo_po_color_size_breakdown a,gbl_temp_engine tmp  WHERE a.po_break_down_id=tmp.ref_val and a.status_active=1 and is_deleted=0 and tmp.entry_form=124 and tmp.ref_from=1 and tmp.user_id=$user_id "; 
	$po_qty_res = sql_select($po_qty_sql);  
	// echo $sql; die;
	// pre($data_arr);die;
	$po_qty_arr = [];
	foreach ($po_qty_res as  $v) {
		$po_qty_arr[$v['PO_ID']][$v['COLOR']][$v['ITEM']] +=$v['ORDER_QUANTITY'];
	} 
						
	// ============================================================================================================
	//												BOOKING DATA
	// ============================================================================================================
    $booking_sql = "SELECT a.entry_form, a.source, b.booking_no, b.po_break_down_id as po_id,b.grey_fab_qnty as booking_qty,b.booking_type,b.is_short,a.supplier_id,b.gmts_color_id as color,c.uom,c.item_number_id as fab_booking_gmts_item ,b.gmt_item as ebs_booking_item from wo_booking_mst a, wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c,gbl_temp_engine tmp where a.id=b.booking_mst_id and c.id=b.pre_cost_fabric_cost_dtls_id and b.po_break_down_id=tmp.ref_val and a.entry_form in(108,118,161,88) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_type in(1,6) and tmp.entry_form=124 and tmp.ref_from=1 and tmp.user_id=$user_id ";
	// echo $booking_sql; die;
	$booking_res = sql_select($booking_sql);  
	$booking_arr = array();
	
	foreach ($booking_res as  $v) 
	{  
		if ($v['ENTRY_FORM']==161) 
		{  
			if($v['SOURCE']==1) // inhouse
			{  
				// $booking_arr[$v['PO_ID']][$v['COLOR']][$v['EBS_BOOKING_ITEM']]['SUPPLIER'] [$company_library[$v['SUPPLIER_ID']]] = $company_library[$v['SUPPLIER_ID']]; 
				$print_emb_status_arr[$v['PO_ID']][$v['COLOR']][$v['EBS_BOOKING_ITEM']]['SUPPLIER'] [$company_library[$v['SUPPLIER_ID']]] = $company_library[$v['SUPPLIER_ID']]; 
			}
			else
			{
				// $booking_arr[$v['PO_ID']][$v['COLOR']][$v['EBS_BOOKING_ITEM']]['SUPPLIER'][$supplier_library[$v['SUPPLIER_ID']]] = $supplier_library[$v['SUPPLIER_ID']];  
				$print_emb_status_arr[$v['PO_ID']][$v['COLOR']][$v['EBS_BOOKING_ITEM']]['SUPPLIER'][$supplier_library[$v['SUPPLIER_ID']]] = $supplier_library[$v['SUPPLIER_ID']];  
			} 
		}
		if (in_array($v['ENTRY_FORM'],[108,118,88]) )
		{
			$booking_arr[$v['PO_ID']][$v['COLOR']][$v['FAB_BOOKING_GMTS_ITEM']]['BOOKING_NO'][$v['BOOKING_NO']]=$v['BOOKING_NO'];   
			$booking_arr[$v['PO_ID']][$v['COLOR']][$v['FAB_BOOKING_GMTS_ITEM']]['UOM'] =$v['UOM']; 
			$booking_arr[$v['PO_ID']][$v['COLOR']][$v['FAB_BOOKING_GMTS_ITEM']][$v['UOM']]['BOOKING_QTY'] += $v['BOOKING_QTY'];  

			$color_wise_uom_arr[$v['PO_ID']][$v['COLOR']][$v['FAB_BOOKING_GMTS_ITEM']] = $v['UOM']; 
		}	
	} 
	// pre($booking_arr); die;

	// ============================================================================================================
	//												CUTTING DATA
	// ============================================================================================================
	 
    $cutting_sql = "SELECT c.order_id as po_id ,b.color_id ,b.gmt_item_id ,(case when a.entry_date=$prod_date then c.size_qty else 0 end ) as today_cutting_qty ,c.size_qty  as total_cutting_qty from ppl_cut_lay_mst a,ppl_cut_lay_dtls b,ppl_cut_lay_bundle c,gbl_temp_engine tmp where a.id=b.mst_id and b.id=c.dtls_id and c.order_id=tmp.ref_val and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and tmp.entry_form=124 and tmp.ref_from=1 and tmp.user_id=$user_id ";
	// echo $cutting_sql; die;
	$cutting_sql_res = sql_select($cutting_sql);  
	// pre($cut_sql_res);die;
	$cutting_arr = array();
	foreach ($cutting_sql_res as  $v) {
		$cutting_arr[$v['PO_ID']][$v['COLOR_ID']][$v['GMT_ITEM_ID']]['TODAY_CUTTING_QTY'] +=$v['TODAY_CUTTING_QTY'];   
		$cutting_arr[$v['PO_ID']][$v['COLOR_ID']][$v['GMT_ITEM_ID']]['TOTAL_CUTTING_QTY'] +=$v['TOTAL_CUTTING_QTY'];   
	}
	// ============================================================================================================
	//												FABRIC RECEIVE DATA
	// ============================================================================================================
    $fab_sql = "SELECT a.po_breakdown_id as po_id,a.quantity as received_qty,b.uom,b.color_id from order_wise_pro_details a, pro_finish_fabric_rcv_dtls b,gbl_temp_engine tmp where a.dtls_id = b.id and a.po_breakdown_id=tmp.ref_val and a.status_active=1 and a.is_deleted=0 and a.trans_type =1 and tmp.entry_form=124 and tmp.ref_from=1 and tmp.user_id=$user_id ";
	// echo $fab_sql;die;
	$fab_sql_res = sql_select($fab_sql);  
	// pre($fab_sql_res);die;
	$fab_arr = array();
	foreach ($fab_sql_res as  $v) {
		$fab_arr[$v['PO_ID']][$v['COLOR_ID']][$v['UOM']]['RECEIVED_QTY'] +=$v['RECEIVED_QTY'];    
	} 
	// pre($fab_arr); die;
	// ============================================================================================================
	//												CONSUMTION DATA
	// ============================================================================================================
	$cons_sql = "SELECT a.po_break_down_id as po_id,a.requirment as cons,a.color_number_id as color,b.uom,b.body_part_id,item_number_id as item from wo_pre_cos_fab_co_avg_con_dtls a,wo_pre_cost_fabric_cost_dtls b,gbl_temp_engine tmp where a.po_break_down_id=tmp.ref_val and b.id=a.pre_cost_fabric_cost_dtls_id and  a.requirment > 0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and tmp.entry_form=124 and tmp.ref_from=1 and tmp.user_id=$user_id";
	// echo $cons_sql;die;
	$cons_sql_res = sql_select($cons_sql);  
	// pre($cons_sql_res);die;
	$body_part_cons_arr = array(); 
	foreach ($cons_sql_res as  $v) 
	{ 
		$body_part_cons_arr[$v['PO_ID']][$v['COLOR']][$v['ITEM']][$v['UOM']][$v['BODY_PART_ID']]['CONS'] += $v['CONS'];
		$body_part_cons_arr[$v['PO_ID']][$v['COLOR']][$v['ITEM']][$v['UOM']][$v['BODY_PART_ID']] ['ROW'] ++;
	} 
	// pre($body_part_cons_arr); die;
	// ============================================================================================================
	//												CONSUMPTION CALCULATION
	// ============================================================================================================
	$cons_arr = array(); 
	foreach ($body_part_cons_arr as $po_id => $po_arr) 
	{
		foreach ($po_arr as $color_id => $color_arr) 
		{
			foreach ($color_arr as $item => $item_arr) 
			{
				foreach ($item_arr as $uom => $uom_arr) 
				{
					foreach ($uom_arr as $body_part_id => $v) 
					{
						$avg_cons = $v['CONS'] / $v['ROW'];
						$cons_arr[$po_id][$color_id][$item][$uom]['TOTAL_CONS'] += $avg_cons ;
					}
				}
			}	
		}
	}
	// pre($cons_arr);
	// ============================================================================================================
	//												ROWSPAN CALCULATION
	// ============================================================================================================
	$color_row_span = $color_wise_data_array = array();
	foreach ($sew_in_array as $line_sl => $line_sl_arr) 
	{ 
		foreach ($line_sl_arr as $line => $line_arr) 
		{ 
			foreach ($line_arr as $po_id => $po_arr) 
			{ 
				$a=0;
				foreach ($po_arr as $job_no => $job_arr) 
				{ 
					foreach ($job_arr as $color => $color_arr) 
					{ 
						foreach ($color_arr as $item => $v) 
						{
							$color_row_span[$line_sl][$line][$po_id][$job_no][$color]++	; 

							$total_cut = $cutting_arr[$po_id][$color][$item]['TOTAL_CUTTING_QTY'] ?? 0;

							$consumption_kg  = $cons_arr[$po_id][$color][$item][12]['TOTAL_CONS'] ?? 0;
							$consumption_mtr = $cons_arr[$po_id][$color][$item][23]['TOTAL_CONS'] ?? 0;
							$consumption_yds = $cons_arr[$po_id][$color][$item][27]['TOTAL_CONS'] ?? 0;

							$fabric_rcve_kg  = $fab_arr[$po_id][$color][12]['RECEIVED_QTY'] ?? 0 ; 
							$fabric_rcve_mtr = $fab_arr[$po_id][$color][23]['RECEIVED_QTY'] ?? 0 ;  
							$fabric_rcve_yds = $fab_arr[$po_id][$color][27]['RECEIVED_QTY'] ?? 0 ;  

							$fabric_used_kg  = ($total_cut*($consumption_kg/12))  ?? 0 ;  
							$fabric_used_mtr = ($total_cut*($consumption_mtr/12)) ?? 0 ;  
							$fabric_used_yds = ($total_cut*($consumption_yds/12)) ?? 0 ;  

							$fabric_booking_kg 	= $booking_arr[$po_id][$color][$item][12]['BOOKING_QTY'] ?? 0 ; 
							$fabric_booking_mtr = $booking_arr[$po_id][$color][$item][23]['BOOKING_QTY'] ?? 0 ;  
							$fabric_booking_yds = $booking_arr[$po_id][$color][$item][27]['BOOKING_QTY'] ?? 0 ; 

							$color_wise_data_array[$po_id][$job_no][$color][12]['BOOKING_QTY'] += $fabric_booking_kg;
							$color_wise_data_array[$po_id][$job_no][$color][23]['BOOKING_QTY'] += $fabric_booking_mtr;
							$color_wise_data_array[$po_id][$job_no][$color][27]['BOOKING_QTY'] += $fabric_booking_yds;
							if ($a==0) 
							{
								$color_wise_data_array[$po_id][$job_no][$color][12]['RECEIVED_QTY'] += $fabric_rcve_kg;
								$color_wise_data_array[$po_id][$job_no][$color][23]['RECEIVED_QTY'] += $fabric_rcve_mtr;
								$color_wise_data_array[$po_id][$job_no][$color][27]['RECEIVED_QTY'] += $fabric_rcve_yds;
							}
							$a++;

							$color_wise_data_array[$po_id][$job_no][$color][12]['FABRIC_USED'] += $fabric_used_kg;
							$color_wise_data_array[$po_id][$job_no][$color][23]['FABRIC_USED'] += $fabric_used_mtr;
							$color_wise_data_array[$po_id][$job_no][$color][27]['FABRIC_USED'] += $fabric_used_yds;

							//COLOR_WISE CONS
							$color_wise_cal_array[$po_id][$job_no][$color][12]['CONS'] += $consumption_kg;
							$color_wise_cal_array[$po_id][$job_no][$color][23]['CONS'] += $consumption_mtr;
							$color_wise_cal_array[$po_id][$job_no][$color][27]['CONS'] += $consumption_yds;
							$color_wise_cal_array[$po_id][$job_no][$color][12]['ROW'] ++;
							$color_wise_cal_array[$po_id][$job_no][$color][23]['ROW'] ++;
							$color_wise_cal_array[$po_id][$job_no][$color][27]['ROW'] ++; 

						}	
					}	
				}	
			}	
		}
	}
 
	// pre ($color_wise_cal_array);die;
	// ============================================================================================================
	//												DELETE ORDER ID FROM TEMP ENGINE
	// ============================================================================================================
	$con = connect();
	execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form = 56 and ref_from =1");
	oci_commit($con);  
	disconnect($con);

	ob_start();
	?>
	<style type="text/css">      
	 	table tr td{word-break: break-all;word-wrap: break-word;}
		table tr th{
			padding: 3px 1px; 
		}
    </style> 
	<div id="scroll_body">
		<div style="width:33820pxpx; margin: 0 auto"> 
			<table width="2820" cellspacing="0" style="margin: 20px 0"> 
				<tr style="border:none;">
					<td align="center" style="border:none; font-size:20px;font-weight: bold;" width="100%"><?= $company_library[str_replace("'","",$cbo_company_name)]; ?>                 </td>
				</tr>
				<tr style="border:none;">
					<td align="center" style="border:none; font-size:16px;font-weight: bold;" width="100%"><?= $location_library[str_replace("'","",$cbo_location)]; ?>                 </td>                
				</tr> 
				<tr style="border:none;">
					<td align="center" style="border:none; font-size:14px;font-weight: bold; text-transform:Uppercase" width="100%">Cutting To Input Report</td>
				</tr>   
				<?php
				if ($date)
				{ 
					?> 
					<tr style="border:none;">
						<td align="center" style="border:none; font-size:14px;font-weight: bold;" width="100%">Date: <?= $date ?></td>
					</tr>  
				<?php 
				}
				?>
			</table> 
			<div>
				<table width="3520" cellspacing="0" border="1" align="left" class="rpt_table" rules="all" id="table_header_1">
					<thead> 	 	 	 	 	 	
						<tr > 
							<th width='50'>Line</th>
							<th width='90'>Ref.</th>
							<th width='60'>Buyer</th>
							<th width='90'>Style</th>
							<th width='80'>Job No</th>
							<th width='50'>Season</th>
							<th width='50'>Po No</th>
							<th width='60'>Color</th>
							<th width='50'>Ship Date</th>
							<th width='60'>Item</th>
							<th width='90'>Print/EMB status</th>
							<th width='50'>Input Date</th>
							<th width='50'>Consumption (KG)</th>
							<th width='50'>Consumption (YDS)</th>
							<th width='50'>Consumption (MTR)</th>
							<th width='50'>Order Qty</th>
							<th width='40'>Today cutting Target</th>
							<th width='60'>Today Cutting</th>
							<th width='60'>Total Cutting</th>
							<th width='60'>Cut (%)</th>
							<th width='60'>Cutting Short/EX</th>
							<th width='60'>Today QC Pass</th>
							<th width='60'>Total Qc Pass</th>
							<th width='60'>QC Balance</th>
							<th width='80'>Total Print Send</th>
							<th width='60'>Print Send Balance</th>
							<th width='60'>Total Print Rcvd</th>
							<th width='60'>Print Balance</th>
							<th width='60'>Today Input</th>
							<th width='60'>Total Input</th>
							<th width='60'>Cutting To Input Balance</th>
							<th width='60'>In Hand</th>
							<th width='60'>Line Balance</th>
							<th width='60'>Today Sewing Output</th>
							<th width='60'>Total Sewing Output</th>
							<th width='60'>Input Blanc</th>
							<th width='60'>Sewing In Hand</th> 				
							<th width='80'>Fabric Booking (KG)</th>
							<th width='80'>Fabric Booking (YDS)</th>
							<th width='80'>Fabric Booking (MTR)</th>
							<th width='80'>Fabric Recv (KG)</th>
							<th width='80'>Fabric Recv (YDS)</th>
							<th width='80'>Fabric Recv (MTR)</th>
							<th width='60'>Fabric Balance (KG)</th>
							<th width='60'>Fabric Balance (YDS)</th>
							<th width='60'>Fabric Balance (MTR)</th>
							<th width='60'>Fabrics Used (KG)</th>
							<th width='60'>Fabrics Used (YDS)</th>
							<th width='60'>Fabrics Used (MTR)</th>
							<th width='60'>Fabrics In Hand (KG)</th>
							<th width='60'>Fabrics In Hand (YDS)</th>
							<th width='60'>Fabrics In Hand (MTR)</th>
							<th width='60'>Cutable Qty (KG)</th>
							<th width='60'>Cutable Qty (YDS)</th>
							<th width='60'>Cutable Qty (MTR)</th>
							<th width='80'>Remarks</th>
						</tr> 
					</thead>
					<tbody>
						<?php
						// pre($prod_data_arr); die;
						$po_array=array();
						$i = 2;
						$gt_po_qty = $gt_today_cut = $gt_total_cut = $gt_cutting_ex = $gt_print_send = $gt_print_send_blnc = $gt_print_received = $gt_print_blnc = $gt_today_input = $gt_today_input = $gt_cutting_to_input_blnc = $gt_in_hand = $gt_line_blnc = $gt_today_sew_out = $gt_total_sew_out = $gt_input_blnc = $gt_sew_in_hand = $gt_qc_pass = $gt_qc_blnc = $gt_fab_booking = $gt_fab_received  = $gt_fab_used  = $gt_fab_blnc =  $gt_cuttable_qty = 0;
						foreach ($sew_in_array as $line_sl => $line_sl_arr) 
						{ 
							foreach ($line_sl_arr as $line => $line_arr) 
							{ 
								foreach ($line_arr as $po_id => $po_arr) 
								{ 
									foreach ($po_arr as $job_no => $job_arr) 
									{ 
										foreach ($job_arr as $color => $color_arr) 
										{  
											$jj = 0;
											foreach ($color_arr as $item => $v) 
											{
												
													if ($i % 2 == 0)  $bgcolor = "#E9F3FF";  else $bgcolor = "#FFFFFF";
													$i++; 
													$sew_out_arr = $sew_out_array[$line_sl][$line][$po_id][$job_no][$color][$item];
													$prod_data_arr = $prod_array[$po_id][$job_no][$color][$item];
													$line_name = $v['SEW_LINE'];
													$po_qty = $po_qty_arr[$po_id][$color][$item];
													
													$booking_no_arr = $booking_arr[$po_id][$color][$item]['BOOKING_NO'];
													$fab_booking 	= $booking_arr[$po_id][$color][$item]['BOOKING_QTY'];
													$supplier_arr 	= $print_emb_status_arr[$po_id][$color][$item]['SUPPLIER'];
													$supplier		= implode(',',$supplier_arr);
													$booking_ref	= implode(',',$booking_no_arr);
													
													$today_qc_pass = $prod_data_arr[1][0]['TODAY_PROD_QTY'] ?? 0;
													$total_qc_pass = $prod_data_arr[1][0]['TOTAL_PROD_QTY'] ?? 0; 
													$today_cut = $cutting_arr[$po_id][$color][$item]['TODAY_CUTTING_QTY'] ?? 0;
													$total_cut = $cutting_arr[$po_id][$color][$item]['TOTAL_CUTTING_QTY'] ?? 0;
													$qc_blnc = $total_qc_pass - $total_cut;
													$total_print_send = $prod_data_arr[2][1]['TOTAL_PROD_QTY'] ?? 0;
													$total_print_received = $prod_data_arr[3][1]['TOTAL_PROD_QTY'] ?? 0; 
													$today_input = $v['TODAY_PROD_QTY'] ?? 0;
													$total_input = $v['TOTAL_PROD_QTY'] ?? 0;
													$today_sew_out = $sew_out_arr['TODAY_PROD_QTY'] ?? 0;
													$total_sew_out = $sew_out_arr['TOTAL_PROD_QTY'] ?? 0; 
													
													$cutting_ex 	= $total_cut - $po_qty;
													$cut_persentage = ($po_qty < $total_cut)? round(($cutting_ex/$po_qty)*100) :0; 
													$cutting_to_input_blnc =$total_cut - $total_input;
													$line_blnc 		=  $total_input - $total_sew_out; 
													$sew_in_hand	= $input_blnc =  $total_cut - $total_sew_out; 
													$print_send_blnc= $total_print_send - $total_cut; 
													$print_blnc 	= $total_print_received - $total_print_send; 
													$in_hand 		= $total_print_received - $total_input; 

													$consumption_kg  = $cons_arr[$po_id][$color][$item][12]['TOTAL_CONS'] ?? 0;
													$consumption_mtr = $cons_arr[$po_id][$color][$item][23]['TOTAL_CONS'] ?? 0;
													$consumption_yds = $cons_arr[$po_id][$color][$item][27]['TOTAL_CONS'] ?? 0;

													$consumption_kg  = fn_num_format($consumption_kg ,2);
													$consumption_mtr = fn_num_format($consumption_mtr,2);
													$consumption_yds = fn_num_format($consumption_yds,2);

													$color_wise_data_array2 = $color_wise_data_array[$po_id][$job_no][$color] ;

													$fabric_booking_kg 	= $color_wise_data_array2[12]['BOOKING_QTY'] ?? 0 ; 
													$fabric_booking_mtr = $color_wise_data_array2[23]['BOOKING_QTY'] ?? 0 ;  
													$fabric_booking_yds = $color_wise_data_array2[27]['BOOKING_QTY'] ?? 0 ; 
													
													$fabric_rcve_kg  = $color_wise_data_array2[12]['RECEIVED_QTY'] ?? 0 ; 
													$fabric_rcve_mtr = $color_wise_data_array2[23]['RECEIVED_QTY'] ?? 0 ;  
													$fabric_rcve_yds = $color_wise_data_array2[27]['RECEIVED_QTY'] ?? 0 ; 

													$fabric_used_kg  = $color_wise_data_array2[12]['FABRIC_USED'] ?? 0 ;   
													$fabric_used_mtr = $color_wise_data_array2[23]['FABRIC_USED'] ?? 0 ;    
													$fabric_used_yds = $color_wise_data_array2[27]['FABRIC_USED'] ?? 0 ;   

													
													$fab_blnc_kg   		= $fabric_rcve_kg - $fabric_booking_kg ;
													$fab_blnc_mtr  		= $fabric_rcve_mtr - $fabric_booking_mtr ;
													$fab_blnc_yds 		= $fabric_rcve_yds - $fabric_booking_yds ;	

													$fab_in_hand_kg 	= $fabric_rcve_kg - $fabric_used_kg;
													$fab_in_hand_mtr	= $fabric_rcve_mtr - $fabric_used_mtr;
													$fab_in_hand_yds	= $fabric_rcve_yds - $fabric_used_yds;
													
													$color_cal_array = $color_wise_cal_array[$po_id][$job_no][$color] ;

													$cons_kg   = $color_cal_array[12]['CONS'];
													$cons_mtr  = $color_cal_array[23]['CONS'];
													$cons_yds  = $color_cal_array[27]['CONS'];

													$no_item_kg   = $color_cal_array[12]['ROW'];
													$no_item_mtr  = $color_cal_array[23]['ROW'];
													$no_item_yds  = $color_cal_array[27]['ROW'];

													$avg_cons_kg  = $cons_kg / $no_item_kg ; 
													$avg_cons_mtr = $cons_mtr / $no_item_mtr ;
													$avg_cons_yds = $cons_yds / $no_item_yds ;

													$cuttable_qty_kg  	= $fab_in_hand_kg / $avg_cons_kg * 12;
													$cuttable_qty_mtr 	= $fab_in_hand_mtr / $avg_cons_mtr * 12;
													$cuttable_qty_yds 	= $fab_in_hand_yds / $avg_cons_yds * 12;


													// Grand total
													$gt_po_qty += $po_qty; 
													$gt_today_cut += $today_cut; 
													$gt_total_cut += $total_cut; 
													$gt_cutting_ex += $cutting_ex; 
													$gt_print_send += $total_print_send; 
													$gt_print_send_blnc += $print_send_blnc; 
													$gt_print_received +=    $total_print_received; 
													$gt_print_blnc += $print_blnc; 
													$gt_today_input += $today_input; 
													$gt_total_input += $total_input; 
													$gt_cutting_to_input_blnc += $cutting_to_input_blnc; 
													$gt_in_hand += $in_hand; 
													$gt_line_blnc += $line_blnc; 
													$gt_today_sew_out += $today_sew_out; 
													$gt_total_sew_out += $total_sew_out; 
													$gt_input_blnc += $input_blnc; 
													$gt_sew_in_hand += $sew_in_hand; 
													$gt_qc_pass += $total_qc_pass; 
													$gt_qc_blnc += $qc_blnc; 
													 
													
													$color_span = $color_row_span[$line_sl][$line][$po_id][$job_no][$color];
													
													?>
														<tr  bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
															<td width='50' align="left" title="<?=$line_sl."=".$line;?>"><?=$line_name_arr[$line]; ?></td>
															<td width='90'><?= $booking_ref; ?></td>
															<td width='60'><?= $buyer_library[$v['BUYER_NAME']]; ?></td>
															<td width='90'><?= $v['STYLE']; ?></td>
															<td width='80'><?= $job_no; ?></td>
															<td width='50'><?= $season_lib[$v['SEASON']]; ?></td>
															<td width='50'><?= $v['PO_NUMBER']; ?></td>
															<td width='60'><?= $color_library[$color]; ?></td>
															<td width='50' align="right"><?= $v['SHIP_DATE']; ?></td>
															<td width='60' align="center"><?= $garments_item[$item]; ?></td>
															<td width='90'><?= $supplier; ?></td>
															<td width='50' align="right"><?= $v['INPUT_DATE'] ; ?></td>
															<td width='50' align="right"><?= number_format($consumption_kg,2); ?></td>
															<td width='50' align="right"><?= number_format($consumption_yds,2); ?></td>
															<td width='50' align="right"><?= number_format($consumption_mtr,2); ?></td>
															<td width='50' align="right"><?= $po_qty ?></td>
															<td width='40'> </td>
															<td width='60' align="right"><?= $today_cut ; ?></td>
															<td width='60' align="right"><?= $total_cut ; ?></td> 
															<td width='60' align="right"><?= $cut_persentage.'%'; ?></td>
															<td width='60' align="right" title="Total Cutting - Order Qty"><?= $cutting_ex ; ?></td>
															<td width='60' align="right"><?= $today_qc_pass; ?></td>
															<td width='60' align="right"><?= $total_qc_pass; ?></td>
															<td width='60' align="right"  title="Total QC Pass - Total Cutting "><?= $qc_blnc; ?></td>
															<td width='60' align="right"><?= $total_print_send; ?></td>
															<td width='60' align="right"  title="Total Print Send - Total Cutting "><?= $print_send_blnc; ?></td>
															<td width='60' align="right"><?= $total_print_received; ?></td>
															<td width='60' align="right" title="Total Print Rcvd - Total Print Send"><?= $print_blnc; ?></td>
															<td width='60' align="right"><?= $today_input; ?></td>
															<td width='60' align="right"><?= $total_input ?></td> 
															<td width='60' align="right" title="Total Cutting - Total Input"><?= $cutting_to_input_blnc; ?></td>
															<td width='60' align="right" title="Total Print Rcvd - Total Input"><?= $in_hand; ?></td>
															<td width='60' align="right" title="Total Input - Total Sewing Output"><?= $line_blnc; ?></td>
															<td width='60' align="right"><?= $today_sew_out; ?></td>
															<td width='60' align="right"><?= $total_sew_out ; ?></td> 
															<td width='60' align="right" title="Total Cutting - Total Sewing Output"><?= $input_blnc; ?></td>
															<td width='60' align="right" title="Total Cutting - Total Sewing Output"><?= $sew_in_hand ?></td>					
															<? 
																if ($jj==0)  // condition for rowspan 
																{ 
																	$fabric_booking_kg 	= fn_num_format($fabric_booking_kg,2);
																	$fabric_booking_yds = fn_num_format($fabric_booking_yds,2);
																	$fabric_booking_mtr = fn_num_format($fabric_booking_mtr,2);
																	$fabric_rcve_kg 	= fn_num_format($fabric_rcve_kg,2);
																	$fabric_rcve_yds 	= fn_num_format($fabric_rcve_yds,2);
																	$fabric_rcve_mtr 	= fn_num_format($fabric_rcve_mtr,2);
																	$fab_blnc_kg 		= fn_num_format($fab_blnc_kg ,2);
																	$fab_blnc_yds 		= fn_num_format($fab_blnc_yds ,2);
																	$fab_blnc_mtr 		= fn_num_format($fab_blnc_mtr ,2);
																	$fabric_used_kg 	= fn_num_format($fabric_used_kg,2);
																	$fabric_used_yds	= fn_num_format($fabric_used_yds,2);
																	$fabric_used_mtr 	= fn_num_format($fabric_used_mtr,2);
																	$fab_in_hand_kg 	= fn_num_format($fab_in_hand_kg,2);
																	$fab_in_hand_yds 	= fn_num_format($fab_in_hand_yds,2);
																	$fab_in_hand_mtr 	= fn_num_format($fab_in_hand_mtr,2);
																	$cuttable_qty_kg 	= fn_num_format($cuttable_qty_kg,2);
																	$cuttable_qty_yds 	= fn_num_format($cuttable_qty_yds,2);
																	$cuttable_qty_mtr	= fn_num_format($cuttable_qty_mtr,2);
																	$avg_cons_kg 		= fn_num_format($avg_cons_kg ,2);
																	$avg_cons_yds 		= fn_num_format($avg_cons_yds,2);
																	$avg_cons_mtr		= fn_num_format($avg_cons_mtr,2);

																	$gt_fab_booking_kg  += $fabric_booking_kg;
																	$gt_fab_booking_mtr += $fabric_booking_mtr;
																	$gt_fab_booking_yds += $fabric_booking_yds;

																	$gt_fab_received_kg  += $fabric_rcve_kg; 
																	$gt_fab_received_mtr += $fabric_rcve_mtr; 
																	$gt_fab_received_yds += $fabric_rcve_yds; 

																	$gt_fab_used_kg  += $fabric_used_kg;
																	$gt_fab_used_yds += $fabric_used_mtr;
																	$gt_fab_used_mtr += $fabric_used_yds;
																	$gt_fab_in_hand_kg  += $fab_in_hand_kg;
																	$gt_fab_in_hand_yds += $fab_in_hand_yds;
																	$gt_fab_in_hand_mtr += $fab_in_hand_mtr;
																	$gt_fab_blnc_kg  += $fab_blnc_kg;
																	$gt_fab_blnc_yds += $fab_blnc_yds;
																	$gt_fab_blnc_mtr += $fab_blnc_mtr;
																	
																	$gt_cuttable_qty_kg += $cuttable_qty_kg;
																	$gt_cuttable_qty_yds += $cuttable_qty_yds;
																	$gt_cuttable_qty_mtr += $cuttable_qty_mtr; 
																	?>	  
																		<td width='80' align="right" rowspan="<?= $color_span ?>" valign="middle" ><?= $fabric_booking_kg ?></td>
																		<td width='80' align="right" rowspan="<?= $color_span ?>" valign="middle" ><?= $fabric_booking_yds  ?></td>
																		<td width='80' align="right" rowspan="<?= $color_span ?>" valign="middle" ><?= $fabric_booking_mtr  ?></td>
																		<td width='80' align="right" rowspan="<?= $color_span ?>" valign="middle" ><?= $fabric_rcve_kg ?></td>
																		<td width='80' align="right" rowspan="<?= $color_span ?>" valign="middle" ><?= $fabric_rcve_yds  ?></td>
																		<td width='80' align="right" rowspan="<?= $color_span ?>" valign="middle" ><?= $fabric_rcve_mtr  ?></td>
																		<td width='80' align="right" rowspan="<?= $color_span ?>" valign="middle" title="<?= "Fabric Recv(KG)($fabric_rcve_kg)  - Fabric Booking(KG)($fabric_booking_kg)"?>"><?= $fab_blnc_kg  ?></td> 
																		<td width='80' align="right" rowspan="<?= $color_span ?>" valign="middle" title="<?= "Fabric Recv(YDS)($fabric_rcve_yds) - Fabric Booking(YDS) ($fabric_booking_yds)" ?>"><?= $fab_blnc_yds ?></td> 
																		<td width='80' align="right" rowspan="<?= $color_span ?>" valign="middle" title="<?= "Fabric Recv(MTR)($fabric_rcve_mtr) - Fabric Booking(MTR) ($fabric_booking_mtr)" ?>"><?= $fab_blnc_mtr ?></td> 
																		<td width='60' align="right" rowspan="<?= $color_span ?>" valign="middle" title="<?= "Consumption(KG)/12 * Total Cutting($total_cut)" ?>"><?= $fabric_used_kg?></td>  
																		<td width='60' align="right" rowspan="<?= $color_span ?>" valign="middle" title="<?= "Consumption(YDS)/12 * Total Cutting($total_cut)" ?>"><?= $fabric_used_yds?></td>  
																		<td width='60' align="right" rowspan="<?= $color_span ?>" valign="middle" title="<?= "Consumption(MTR)/12 * Total Cutting($total_cut)" ?>"><?= $fabric_used_mtr?></td>  
																		<td width='60' align="right" rowspan="<?= $color_span ?>" valign="middle" title="<?= "Fabric Recv(KG)($fabric_rcve_kg) - Fabrics Used(KG) ($fabric_used_kg) " ?>"><?= $fab_in_hand_kg?></td>    
																		<td width='60' align="right" rowspan="<?= $color_span ?>" valign="middle" title="<?= "Fabric Recv(YDS)($fabric_rcve_yds) - Fabrics Used(YDS) ($fabric_used_yds) " ?>"><?= $fab_in_hand_yds?></td>    
																		<td width='60' align="right" rowspan="<?= $color_span ?>" valign="middle" title="<?= "Fabric Recv(MTR)($fabric_rcve_mtr) - Fabrics Used(MTR) ($fabric_used_mtr) " ?>"><?= $fab_in_hand_mtr?></td>    
																		<td width='60' align="right" rowspan="<?= $color_span ?>" valign="middle" title="<?= "Fabrics In Hand(KG)($fab_in_hand_kg) /Avg Consumption(KG)($avg_cons_kg)* 12" ?>"><?= $cuttable_qty_kg?></td>    
																		<td width='60' align="right" rowspan="<?= $color_span ?>" valign="middle" title="<?= "Fabrics In Hand(YDS)($fab_in_hand_yds) /Avg Consumption(YDS)($avg_cons_yds)* 12" ?>"><?= $cuttable_qty_yds?></td>    
																		<td width='60' align="right" rowspan="<?= $color_span ?>" valign="middle" title="<?= "Fabrics In Hand(MTR)($fab_in_hand_mtr) /Avg Consumption(MTR)($avg_cons_mtr)* 12" ?>"><?= $cuttable_qty_mtr?></td>    
																	<?
																} 
																$jj++;
															?>			
															
															
															
															<td width='80' align="right">  </td>
														</tr>
													<?php  
												}
											// }		
										}	
									}	
								}	
							}
						}
						
						?>
					</tbody>
					<tfoot>
						<tr style="background: rgb(194,220,255);">
							<th colspan="6" align="right">GRAND TOTAL</th> 
							<th colspan="9"></th>
							<th align="right"><?= $gt_po_qty; ?></th>
							<th></th>
							<th align="right"><?= $gt_today_cut; ?></th> 
							<th align="right"><?= $gt_total_cut; ?></th>  
							<th colspan="" align="right"><?= ($gt_po_qty < $gt_total_cut)? round(($gt_cutting_ex/$gt_po_qty) * 100) :0 ?>%</th>
							<th align="right"><?= $gt_cutting_ex; ?></th>  
							<th align="right"> <?= "" ?> </th>    
							<th align="right"><?= $gt_qc_pass ; ?></th>  
							<th align="right"><?= $gt_qc_blnc ; ?></th>  
							<th align="right"><?= $gt_print_send; ?></th>  
							<th align="right"><?= $gt_print_send_blnc; ?></th>   
							<th align="right"><?= $gt_print_received; ?></th>    
							<th align="right"><?= $gt_print_blnc; ?></th>    
							<th align="right"><?= $gt_today_input; ?></th>     
							<th align="right"><?= $gt_total_input; ?></th>  
							<th align="right"><?= $gt_cutting_to_input_blnc; ?></th>  
							<th align="right"><?= $gt_in_hand; ?></th>  
							<th align="right"><?= $gt_line_blnc; ?></th>  
							<th align="right"><?= $gt_today_sew_out; ?></th>   
							<th align="right"><?= $gt_total_sew_out; ?></th>    
							<th align="right"><?= $gt_input_blnc; ?></th>   
							<th align="right"><?= $gt_sew_in_hand; ?></th>  
							<th align="right"><?= number_format($gt_fab_booking_kg ,2) ?></th>   
							<th align="right"><?= number_format($gt_fab_booking_yds ,2) ?></th>   
							<th align="right"><?= number_format($gt_fab_booking_mtr ,2) ?></th>   
							<th align="right"><?= number_format($gt_fab_received_kg ,2) ?></th>    
							<th align="right"><?= number_format($gt_fab_received_yds ,2) ?></th>    
							<th align="right"><?= number_format($gt_fab_received_mtr ,2) ?></th>    
							<th align="right"><?= number_format($gt_fab_blnc_kg ,2); ?></th>      
							<th align="right"><?= number_format($gt_fab_blnc_yds ,2); ?></th>      
							<th align="right"><?= number_format($gt_fab_blnc_mtr ,2); ?></th>      
							<th align="right"><?= number_format($gt_fab_used_kg ,2); ?></th>    
							<th align="right"><?= number_format($gt_fab_used_yds ,2); ?></th>    
							<th align="right"><?= number_format($gt_fab_used_mtr ,2); ?></th>    
							<th align="right"><?= number_format($gt_fab_in_hand_kg ,2); ?></th>    
							<th align="right"><?= number_format($gt_fab_in_hand_yds ,2); ?></th>    
							<th align="right"><?= number_format($gt_fab_in_hand_mtr ,2); ?></th>    
							<th align="right"><?= number_format($gt_cuttable_qty_kg ,2); ?></th>     
							<th align="right"><?= number_format($gt_cuttable_qty_yds ,2); ?></th>     
							<th align="right"><?= number_format($gt_cuttable_qty_mtr ,2); ?></th>     
							<th colspan="" align="right"> </th> 
						</tr>
					</tfoot>
				</table>    
			</div>
			<br />
		</div>
	</div>
         
	<?
	$floor_name = implode(',', $floor_arr);
	$floor_wise_total = implode(',', $floor_total_arr);
		
	
	foreach (glob($user_id."_*.xls") as $filename)
	{		
		@unlink($filename);
	}
	$name=$user_id."_".time().".xls";
	$create_new_excel = fopen($name, 'w');	
	$is_created = fwrite($create_new_excel,ob_get_contents());
	//$new_link=create_delete_report_file( $html, 1, 1, "../../../" );
	echo "####".$name;
	exit();
}
?>