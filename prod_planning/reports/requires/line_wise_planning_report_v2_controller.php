<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$type=$_REQUEST['type'];

if($action=="load_drop_down_buyer")
{
	// echo $data;die;
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "- All -", $selected, "" );     	 
	exit();
}

if($action=="print_button_variable_setting")
{
	$print_report_format = return_field_value("format_id","lib_report_template","template_name in($data) and module_id=4 and report_id=294 and is_deleted=0 and status_active=1");
	$buttonHtml ='';
	$printButton = explode(",", $print_report_format);
	foreach($printButton as $id){
		if($id==108)$buttonHtml.='<input type="button" id="show_button" class="formbutton" style="width:50px" value="Show" onClick="fn_report_generate(1);" />';	
		if($id==195)$buttonHtml.='<input type="button" id="show_button" class="formbutton" style="width:50px" value="Show 2" onClick="fn_report_generate(2);" />';
		if($id==242)$buttonHtml.='<input type="button" id="show_button" class="formbutton" style="width:50px" value="Show 3" onClick="fn_report_generate(3);" />';	
	}
    echo "document.getElementById('button_data_panel').innerHTML = '".$buttonHtml."';\n";
    exit();
}

if($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 120, "select id,location_name from lib_location where status_active=1 and company_id in($data)","id,location_name", 0, "-- Select --", $selected, "" );     	 
	exit();
}

if($action=="load_drop_down_floor")
{
	$data_arr = explode("_", $data);
	$company_id = $data_arr[0];
	$location_id = $data_arr[1];
	echo create_drop_down( "cbo_floor_id", 120, "select id,floor_name from lib_prod_floor  where status_active=1  and company_id in($company_id) and location_id in($location_id) and production_process=5","id,floor_name", 0, "-- Select --", $selected, "" );   	 
	exit();
}

if ($action=="load_drop_down_brand")
{
	echo create_drop_down( "cbo_brand_name", 100, "select id, brand_name from lib_buyer_brand where buyer_id='$data' and status_active =1 and is_deleted=0 $userbrand_idCond order by brand_name ASC","id,brand_name", 1, "--Select--", "", "" );
	exit();
}
if ($action=="load_drop_down_buyer_season")
{
	echo create_drop_down( "cbo_buyer_season_name", 100, "select season_name,id from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "--Select--", "", "" );
	exit();
}
 
if($action=="style_no_popup")
{
	echo load_html_head_contents("Style Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
	
		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_style_id").val(splitData[0]); 
			$("#hide_style_no").val(splitData[1]); 
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:880px;">
            <table width="870" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
					<th>Brand</th>
					<th>Season</th>
					<th>Season Year</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter PO No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_style_id" id="hide_style_id" value="" />
                    <input type="hidden" name="hide_style_no" id="hide_style_no" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"load_drop_down( 'line_wise_planning_report_v2_controller',this.value, 'load_drop_down_brand', 'brand_td');load_drop_down( 'line_wise_planning_report_v2_controller',this.value, 'load_drop_down_buyer_season', 'buyer_season_td')",0 );
							?>
                        </td>  
						<td id="brand_td">
							<? 
							   echo create_drop_down( "cbo_brand_name", 100, $blank_array,"", 1, "-- Select Brand --", $selected, "",0,"" );
							?>
                        </td>
	                    <td id="buyer_season_td">
							<? 
							   echo create_drop_down( "cbo_buyer_season_name", 100, $blank_array,"", 1, "-- Select Brand --", $selected, "",0,"" );
							?>
                        </td>
	                    <td width="100">
							<? 
							  echo create_drop_down( "cbo_year", 100, $year,"", 1, "--Year--", 0, "",0 );
							?>
	                    </td>               
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref",3=>"PO");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "3",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>'+'**'+document.getElementById('cbo_brand_name').value+'**'+document.getElementById('cbo_buyer_season_name').value+'**'+document.getElementById('cbo_year').value, 'create_style_no_search_list_view', 'search_div', 'line_wise_planning_report_v2_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	
    </html>
	<div id="report_container2"></div>
    <?
	exit(); 
}

if($action=="create_style_no_search_list_view")
{
	$data=explode('**',$data);
	// echo"<pre>";
	// print_r($data);
	$company_id=$data[0];
	$year_id=$data[4];
	$month_id=$data[5];
	$brand_id=$data[6];
	$buyer_season_name_id=$data[7];
	$season_year=$data[8];
	//echo $month_id;
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$data[1]";
	}
	
	$search_by=$data[2];
	$search_string=trim($data[3]);
	
	if($search_string!=''){
		if($search_by==1){$search_con=" and a.job_no like('%$search_string')";}
		else if($search_by==2){$search_con=" and a.style_ref_no like('%$search_string')";}
		else if($search_by==3){ $search_con=" and b.po_number like('%$search_string')";}
	}
 	
	//$year="year(insert_date)";
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";
	
	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(a.insert_date)=$year_id"; else $year_cond="";	
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(a.insert_date,'YYYY')";
		if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";	
	}

	if(str_replace("'","",$brand_id)==0) $brand_name_cond=""; else $brand_name_cond="and a.brand_id=".str_replace("'","",$brand_id)."";
	if(str_replace("'","",$buyer_season_name_id)==0) $season_name_cond=""; else $season_name_cond="and a.season_buyer_wise=".str_replace("'","",$buyer_season_name_id)."";
	if(str_replace("'","",$season_year)==0) $season_year_cond=""; else $season_year_cond="and a.season_year=".str_replace("'","",$season_year)."";
	 
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	$sql= "SELECT b.po_number, a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,a.brand_id,a.season_buyer_wise,a.season_year, $year_field from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.company_name in($company_id) $search_con  $buyer_id_cond $year_cond  
	$brand_name_cond $season_name_cond $season_year_cond  order by job_no";
	// echo $sql;//die;
	
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No,PO", "120,130,80,60,80","600","240",0, $sql , "js_set_value", "id,style_ref_no", "", 1, "company_name,buyer_name,0,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no,po_number", "",'','0,0,0,0,0','') ;
	exit(); 
}  

 

//Version 2 is developed by REZA.
if($action=="report_generate_v2")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_id=str_replace("'","",$cbo_company_id);
	$buyer_id=str_replace("'","",$cbo_buyer_name);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$style_no=strtolower(str_replace("'","",$txt_style_no));
	$style_id=str_replace("'","",$txt_style_id);
	$location_id=str_replace("'","",$cbo_location_id);
	$floor_id=str_replace("'","",$cbo_floor_id);
	$txt_internal_ref_no=str_replace("'","",$txt_internal_ref_no); 
	
	$cbo_search_type=str_replace("'","",$cbo_search_type); 
	$txt_search_by=str_replace("'","",$txt_search_by);

	$line_location=return_library_array( "select id, LOCATION_NAME from LIB_LOCATION where is_deleted=0 and status_active=1",'id','LOCATION_NAME');
	$company_lib=return_library_array( "select id, company_short_name from lib_company where is_deleted=0 and status_active=1", "id", "company_short_name");
	$buyer_lib=return_library_array( "select id, short_name from lib_buyer where is_deleted=0 and status_active=1", "id", "short_name");
	$floor_lib=return_library_array( "select id, floor_name from lib_prod_floor where is_deleted=0 and status_active=1",'id','floor_name');
	$line_lib=return_library_array( "select id, line_name from lib_sewing_line where is_deleted=0 and status_active=1",'id','line_name');
	$buyer_brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand",'id','brand_name');
	$buyer_season_arr=return_library_array( "select id, season_name from  lib_buyer_season",'id','season_name');

	// show button
	if($type==1){
  
		if($date_from!="" && $date_to!=""){
			$start_date=change_date_format($date_from,"","",1);
			$end_date=change_date_format($date_to,"","",1);
			$where_con=" and d.plan_date between '$start_date' and '$end_date'"; 
			$where_con_sub=" and d.plan_date between '$start_date' and '$end_date'"; 
		}

		if($buyer_id!=0){
			$where_con.=" and A.BUYER_NAME=$buyer_id"; 
			$where_con_sub.=" and A.PARTY_ID=$buyer_id"; 
		}
		if($location_id!=""){
			$where_con.=" and C.LOCATION_ID in($location_id)"; 
			$where_con_sub.=" and C.LOCATION_ID in($location_id)"; 
		}
		if($floor_id!=""){
			$where_con.=" and F.FLOOR_NAME in($floor_id)";
			$where_con_sub.=" and F.FLOOR_NAME in($floor_id)";
		 }
		if($style_no!=""){
			$where_con.=" and LOWER(A.STYLE_REF_NO) like('%$style_no%')"; 
			$where_con_sub.=" and LOWER(b.CUST_STYLE_REF) like('%$style_no%')"; 
		}
		//if($txt_internal_ref_no!=""){$where_con.=" and LOWER(b.GROUPING) like('%$txt_internal_ref_no%')"; }

		if($cbo_search_type==1 && $txt_search_by!=''){
			$where_con.=" and A.JOB_NO like('%$txt_search_by')";
			$where_con_sub.=" and A.SUBCON_JOB like('%$txt_search_by')";
		 }
		if($cbo_search_type==2 && $txt_search_by!=''){
			$where_con.=" and B.PO_NUMBER like('%$txt_search_by')";
			$where_con_sub.=" and B.ORDER_NO like('%$txt_search_by')";
		 }
		if($cbo_search_type==3 && $txt_search_by!=''){
			$where_con.=" and b.GROUPING like('%$txt_search_by')"; 
		}
		if($cbo_search_type==4 && $txt_search_by!=''){
			$item_id_arr=implode(',',return_library_array( "select id from lib_garment_item where is_deleted=0 and item_name like('%$txt_search_by')",'id','id'));
			$where_con.=" and e.ITEM_NUMBER_ID in($item_id_arr)"; 
			$where_con_sub.=" and e.ITEM_NUMBER_ID in($item_id_arr)"; 
		}

		

			$order_sql = "SELECT C.COMPANY_ID,c.PLAN_ID,c.MERGED_PLAN_ID,C.LOCATION_ID,F.FLOOR_NAME,C.LINE_ID,d.PLAN_QNTY,E.ITEM_NUMBER_ID , A.SET_SMV,(B.PO_QUANTITY*a.TOTAL_SET_QNTY) as PO_QUANTITY,B.PLAN_CUT,B.PUB_SHIPMENT_DATE,b.PO_TOTAL_PRICE,C.START_DATE,C.END_DATE,A.JOB_NO,A.ID AS JOB_ID,B.UNIT_PRICE,B.ID AS PO_ID,e.COLOR_NUMBER_ID, B.PO_NUMBER,cast(b.GROUPING as VARCHAR(4000)) as GROUPING,A.BUYER_NAME,A.STYLE_REF_NO, d.PLAN_DATE,A.BRAND_ID,c.ALLOCATED_MP, A.SEASON_BUYER_WISE, A.SEASON_YEAR,c.FIRST_DAY_OUTPUT, (d.WORKING_HOUR) as WORKING_HOUR, 1 as ORDER_TYPE,f.SEWING_LINE_SERIAL from wo_po_details_master a, wo_po_break_down b, ppl_sewing_plan_board c,ppl_sewing_plan_board_dtls d,ppl_sewing_plan_board_powise e, LIB_SEWING_LINE f where a.id=b.job_id and b.id=e.po_break_down_id and d.plan_id=e.plan_id and c.plan_id=d.plan_id and f.id=C.LINE_ID  and c.company_id in($company_id) $where_con  and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
			
			$sub_sql = "SELECT C.COMPANY_ID,c.PLAN_ID,c.MERGED_PLAN_ID,C.LOCATION_ID,F.FLOOR_NAME,C.LINE_ID,d.PLAN_QNTY,E.ITEM_NUMBER_ID , 0 as SET_SMV,B.ORDER_QUANTITY as PO_QUANTITY,B.PLAN_CUT,B.DELIVERY_DATE as PUB_SHIPMENT_DATE,b.AMOUNT as PO_TOTAL_PRICE,C.START_DATE,C.END_DATE,A.SUBCON_JOB as JOB_NO,A.ID AS JOB_ID,B.RATE as UNIT_PRICE,B.ID AS PO_ID,e.COLOR_NUMBER_ID, B.ORDER_NO as PO_NUMBER,'' as GROUPING,A.PARTY_ID as BUYER_NAME,B.CUST_STYLE_REF as STYLE_REF_NO, d.PLAN_DATE,0 as BRAND_ID,c.ALLOCATED_MP, 0 as SEASON_BUYER_WISE, 0 as SEASON_YEAR,c.FIRST_DAY_OUTPUT, (d.WORKING_HOUR) as WORKING_HOUR, 2 as ORDER_TYPE,f.SEWING_LINE_SERIAL from SUBCON_ORD_MST a, SUBCON_ORD_DTLS b, ppl_sewing_plan_board c,ppl_sewing_plan_board_dtls d,ppl_sewing_plan_board_powise e, LIB_SEWING_LINE f where a.id=b.mst_id and b.id=e.po_break_down_id and d.plan_id=e.plan_id and c.plan_id=d.plan_id and f.id=C.LINE_ID  and c.company_id in($company_id) $where_con_sub  and b.MAIN_PROCESS_ID =5  AND a.ENTRY_FORM = 238 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
			
			$sql = " SELECT x.* from ($order_sql union all $sub_sql) x  order by x.SEWING_LINE_SERIAL";


		 	//echo $sql; 
	
		$dataArr=array();	
		$line_arr=array();

		$sql_result=sql_select($sql);
		foreach($sql_result as $row){
			$dateKey=date("d M-Y",strtotime($row['PLAN_DATE']));
			
			$dataArr[$row['COMPANY_ID']][$row['LOCATION_ID']][$row['FLOOR_NAME']][$row['LINE_ID']][$row['PLAN_ID']]=array(
				'JOB_NO'=>$row['JOB_NO'],
				'FLOOR_NAME'=>$row['FLOOR_NAME'],
				'LINE_ID'=>$row['LINE_ID'],
				'BUYER_NAME'=>$row['BUYER_NAME'],
				'STYLE_REF_NO'=>$row['STYLE_REF_NO'],
				'ITEM_NUMBER_ID'=>$row['ITEM_NUMBER_ID'],
				'SET_SMV'=>$row['SET_SMV'],
				'START_DATE'=>$row['START_DATE'],
				'END_DATE'=>$row['END_DATE'],
				'PUB_SHIPMENT_DATE'=>$row['PUB_SHIPMENT_DATE'],
				'COLOR_NUMBER_ID'=>$row['COLOR_NUMBER_ID'],
				'BRAND_ID'=>$row['BRAND_ID'],
				'SEASON_BUYER_WISE'=>$row['SEASON_BUYER_WISE'],
				'SEASON_YEAR'=>$row['SEASON_YEAR'],
				'ALLOCATED_MP'=>$row['ALLOCATED_MP'],
				'FIRST_DAY_OUTPUT'=>$row['FIRST_DAY_OUTPUT'],
				'PLAN_ID'=>$row['PLAN_ID']
			);
			
			
			$key2=$row["COMPANY_ID"].'**'.$row["LOCATION_ID"].'**'.$row['FLOOR_NAME'].'**'.$row['LINE_ID'].'**'.$row["PLAN_ID"];
			$planDataArr["PLAN_QNTY"][$key2][$dateKey]=$row['PLAN_QNTY'];
			
			$planDataArr["PO_QUANTITY"][$key2][$row['PO_ID']]=$row['PO_QUANTITY'];
			$planDataArr['PO_TOTAL_PRICE'][$key2][$row['PO_ID']]=$row['PO_TOTAL_PRICE'];
			$planDataArr['PLAN_CUT'][$key2][$row['PO_NUMBER']]=$row['PLAN_CUT'];
			$planDataArr['PO_NUMBER'][$key2][$row['PO_NUMBER']]=$row['PO_NUMBER'];
			$planDataArr['GROUPING'][$key2][$row['GROUPING']]=$row['GROUPING'];
			$planDataArr['UNIT_PRICE'][$key2][$row['PO_ID']]=$row['UNIT_PRICE'];
			$planDataArr['WORKING_HOUR'][$key2][$row['WORKING_HOUR']]=$row['WORKING_HOUR'];
			$allFloorArr[$row['FLOOR_NAME']]=$row['FLOOR_NAME'];
			$allJobIdArr[$row['JOB_ID']]=$row['JOB_ID'];
			$allPoIdArr[$row['PO_ID']]=$row['PO_ID'];
			$allColorArr[$row['COLOR_NUMBER_ID']]=$row['COLOR_NUMBER_ID'];
			$m=date("ym",strtotime($row['PLAN_DATE']))*1;
			$d=date("d",strtotime($row['PLAN_DATE']))*1;
			$tempMonthArr[$m][$d]=$dateKey;
			$plan_id_arr[$row['PLAN_ID']]=$row['PLAN_ID'];
			$line_arr[$row['LINE_ID']]=$row['LINE_ID'];

		}
		unset($sql_result);
		ksort($tempMonthArr); 
	    // print_r(count($line_arr));

		$tot_line_arr=count($line_arr);

		// foreach($line_arr as $line_id=>$val)
		// {
		// 	$tot_line+=$val;
		// }
		// echo $tot_line;
	
		//echo "select PLAN_ID,LINE_ID from PPL_SEWING_PLAN_BOARD where 1=1 ".where_con_using_array($parge_plan_id,0,'PLAN_ID')."";die; 
	
		$marged_line_arr=return_library_array( "select MERGED_PLAN_ID,LINE_ID from PPL_SEWING_PLAN_BOARD where STATUS_ACTIVE=1 and IS_DELETED=0 ".where_con_using_array($plan_id_arr,0,'MERGED_PLAN_ID')."", "MERGED_PLAN_ID", "LINE_ID"  );
		
		$monthArr=array();
		foreach($tempMonthArr as $valArr){
			asort($valArr);
			foreach($valArr as $date){
				$monthArr[$date]=$date;
			}
		}
		unset($tempMonthArr);

	
		$resource_sql="SELECT A.COMPANY_ID,A.LOCATION_ID,A.FLOOR_ID,A.LINE_NUMBER,B.ACTIVE_MACHINE,B.HELPER,B.WORKING_HOUR,B.TARGET_EFFICIENCY from prod_resource_mst a ,prod_resource_dtls_mast b where a.id=b.mst_id and a.company_id in($company_id) ".where_con_using_array($allFloorArr,0,'a.FLOOR_ID')."";
		//echo $resource_sql;die;
		$resource_sql_res=sql_select($resource_sql);
		
		foreach($resource_sql_res as $row)
		{
			$lineArr=explode(",",$row['LINE_NUMBER']);
			foreach($lineArr as $line_id)
			{

				$key3=$row['COMPANY_ID'].'**'.$row['LOCATION_ID'].'**'.$row['FLOOR_ID'].'**'.$line_id;
				$resource_arr['WH'][$key3]=$row['WORKING_HOUR'];
				$resource_arr['TGT'][$key3]=$row['TARGET_EFFICIENCY'];
			}
		}
	    unset($resource_sql_res);
	
		$pre_cost_sql="select a.COSTING_PER,b.JOB_NO, b.CM_COST from wo_pre_cost_mst a,wo_pre_cost_dtls b where a.job_no=b.job_no and  b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and  a.STATUS_ACTIVE=1 and a.IS_DELETED=0 ".where_con_using_array($allJobIdArr,0,'b.job_id')."";
		//echo $pre_cost_sql;die;
		
		$pre_cost_sql_res=sql_select($pre_cost_sql); 
		foreach($pre_cost_sql_res as $row)
		{
			$pre_cost_arr['CM_COST'][$row['JOB_NO']]=$row['CM_COST'];
			$pre_cost_arr['COSTING_PER'][$row['JOB_NO']]=$row['COSTING_PER'];
		}
		unset($pre_cost_sql_res);

		
	
	
	
		$production_sql="SELECT  B.LINE_NUMBER,A.PO_BREAK_DOWN_ID,A.ITEM_NUMBER_ID,SUM(A.PRODUCTION_QUANTITY) AS QTY from pro_garments_production_mst a,prod_resource_mst b where a.sewing_line=b.id   and  a.status_active=1 and a.production_type=5 ".where_con_using_array($allPoIdArr,0,'a.po_break_down_id')." group by  b.line_number,a.po_break_down_id,a.item_number_id ";
		$production_sql_res=sql_select($production_sql);
		foreach($production_sql_res as $p)
		{
			$val=array_unique(explode(",",$p['LINE_NUMBER']));
			foreach($val as $line_key)
			{
				$production_arr[$p['PO_BREAK_DOWN_ID']][$p['ITEM_NUMBER_ID']][$line_key]+=$p['QTY'];
			}
		}
		unset($production_sql_res);
		
	   //print_r($production_sql);die;
	
	   $color_arr=return_library_array( "select ID, COLOR_NAME from LIB_COLOR where STATUS_ACTIVE=1 and IS_DELETED=0 ".where_con_using_array($allColorArr,0,'id')."", "id", "COLOR_NAME"  );

	
		$width=(count($monthArr)*50)+2130;

		ob_start();	
		?>
		<div style="margin:0 auto; width:<?=$width+25;?>px;">
		<table width="100%" border="0">
			<thead>
				<tr><td align="center" colspan="<?=(count($monthArr)+25);?>" style="font-size:18px; font-weight:bold; text-decoration:underline;">Line Wise Planning Report</td></tr>
				<tr><td align="center" colspan="<?=(count($monthArr)+25);?>"><b>Plan Date Range : <?=change_date_format($date_from);?> to <?=change_date_format($date_to);?></b></td></tr>
				<tr><td align="center" colspan="<?=(count($monthArr)+25);?>">Report Generate on : <?=date("d-m-Y, h:i A");?></td></tr>
			</thead>
		</table>

		<table width="<?=$width;?>" id="table_header_1"cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="60">Floor</th>
				<th width="60">Line</th>
				<th width="60">Buyer</th>
				<th width="60">Brand</th>
				<th width="60">Season</th>
				<th width="60">Season Year</th>
				<th width="80">Job</th>
				<th width="80">Style Ref</th>
				<th width="100">Internal Ref</th>
				<th width="100">PO Number</th>
				<th width="100">Item</th>
				<th width="100">Color</th>
				<th width="50">SMV</th>
				<th width="50" title="Man Power">MP</th>
				<th width="50" title="Working Hour">WH</th>
				<th width="50" title="Hourly Plan qty">HPQ</th>
				<th width="80">Order Qty</th>
				<th width="80">Plan Cut Qty</th>
				<th width="80">Output</th>
				<th width="80">Plan Eff%</th>
				<th width="80">Planned Qty</th>
				<th width="60">Ship Date</th>
				<th width="60">Sewing Start</th>
				<th width="60">Sewing End</th>
				<th width="50">Late/Early By</th>
				<th width="50">CM/Pcs</th>
				<th width="50">FOB Price</th>
				<th width="80">Total CM</th>
				<th width="80">Total FOB</th>
				<? foreach($monthArr as $date){ ?><th width="50"><?=$date;?></th> <? } ?>
			</thead>
		</table>



		<div style="width:<?=$width+18;?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
			<table width="<?=$width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
				<tbody>
					<?  
					$i=1;
					$grandTotal=array();
					foreach($dataArr as $company_id=>$companyRows)
					{
						$companyTotal=array();
						foreach($companyRows as $location_id=>$locationRows)
						{
							$locationTotal=array();
							echo "<tr bgcolor='#CCCCCC'><td colspan='".(count($monthArr)+30)."'> {$company_lib[$company_id]}, {$line_location[$location_id]} </td></tr>";
							
							foreach($locationRows as $floor_id=>$floorRows)
							{ 
								$floorTotal=array();
								
								foreach($floorRows as $line_id=>$lineRows)
								{ 
									$lineTotal=array();$s=1;
									foreach($lineRows as $plan_id=>$rows)
									{ 
										$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
										$key2=$company_id.'**'.$location_id.'**'.$floor_id.'**'.$line_id.'**'.$plan_id;
										$key3=$company_id.'**'.$location_id.'**'.$floor_id.'**'.$line_id;
										
										//$lateEarlyBy = (abs(strtotime($rows[END_DATE]) - strtotime($rows[PUB_SHIPMENT_DATE])))/(60*60*24);
										$lateEarlyBy = (strtotime($rows["PUB_SHIPMENT_DATE"]) - strtotime($rows["END_DATE"]))/(60*60*24);
										$PLAN_QNTY=array_sum($planDataArr["PLAN_QNTY"][$key2]);		
										// $total_line=count($line_arr[$rows['LINE_ID']]);

										// $main_total_line+=$total_line;
										// echo $main_total_line;
											
															
																				
										$sewing_out_qty=0;
										foreach($planDataArr["PO_QUANTITY"][$key2] as $poId=>$v){
											$sewing_out_qty+=$production_arr[$poId][$rows['ITEM_NUMBER_ID']][$line_id];
										}
										
										
										$costing_per=$pre_cost_arr["COSTING_PER"][$rows['JOB_NO']];
										if($costing_per==1){$costing_per_value=12;}
										else if($costing_per==2){ $costing_per_value=1;}
										else if($costing_per==3){ $costing_per_value=24;}
										else if($costing_per==4){ $costing_per_value=36;}
										else if($costing_per==5){ $costing_per_value=48;}
										else{$costing_per_value=0;}
										
										
										
										$job_avg_rate=array_sum($planDataArr["PO_TOTAL_PRICE"][$key2])/array_sum($planDataArr["PO_QUANTITY"][$key2]);
										
										
										$total_cm=$PLAN_QNTY*($pre_cost_arr["CM_COST"][$rows["JOB_NO"]]/$costing_per_value);
										$total_fob=$PLAN_QNTY*$job_avg_rate;
										
										//floor..................................
										$floorTotal["sewing_out"]+=$sewing_out_qty;
										$floorTotal["planned_qty"]+=$PLAN_QNTY;
										$floorTotal["total_cm"]+=$total_cm;
										$floorTotal["total_fob"]+=$total_fob;
										//Line..................................
										$lineTotal["sewing_out"]+=$sewing_out_qty;
										$lineTotal["planned_qty"]+=$PLAN_QNTY;
										$lineTotal["total_cm"]+=$total_cm;
										$lineTotal["total_fob"]+=$total_fob;
										//location..................................
										$locationTotal["sewing_out"]+=$sewing_out_qty;
										$locationTotal["planned_qty"]+=$PLAN_QNTY;
										$locationTotal["total_cm"]+=$total_cm;
										$locationTotal["total_fob"]+=$total_fob;
										//company..................................
										$companyTotal["sewing_out"]+=$sewing_out_qty;
										$companyTotal["planned_qty"]+=$PLAN_QNTY;
										$companyTotal["total_cm"]+=$total_cm;
										$companyTotal["total_fob"]+=$total_fob;
										//grand..................................
										$grandTotal["sewing_out"]+=$sewing_out_qty;
										$grandTotal["planned_qty"]+=$PLAN_QNTY;
										$grandTotal["total_cm"]+=$total_cm;
										$grandTotal["total_fob"]+=$total_fob;
										
										$bg_late="#FF0000";
										
										
										?>
										<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>">
											<td align="center" width="30"><?=$s++;?></td>
											<td width="60"><p><?=$floor_lib[$floor_id];?></p></td>
											<td width="60" title="Plan id:<?=$rows['PLAN_ID'];?>">
												<p>
													<?=$line_lib[$rows['LINE_ID']];?>
													<?=($marged_line_arr[$rows['PLAN_ID']])?",<b style='color:red;'>".$line_lib[$marged_line_arr[$rows['PLAN_ID']]]."</b>":'';?>
												</p>
											</td>
											<td width="60"><?=$buyer_lib[$rows['BUYER_NAME']];?></td>
											<td width="60"><?=$buyer_brand_arr[$rows['BRAND_ID']];?></td>
											<td width="60"><?=$buyer_season_arr[$rows['SEASON_BUYER_WISE']];?></td>
											<td width="60"><?=$rows['SEASON_YEAR'];?></td>
											<td width="80" align="center"><?=$rows['JOB_NO'];?></td>
											<td width="80"><p><?=$rows['STYLE_REF_NO'];?></p></td>
											<td width="100"><p><?=implode(', ',$planDataArr['GROUPING'][$key2]);?></p></td>
											<td width="100"><p><?=implode(', ',$planDataArr['PO_NUMBER'][$key2]);?></p></td>
											<td width="100"><p><?=$garments_item[$rows['ITEM_NUMBER_ID']];?></p></td>
											<td width="100" align="center"><p><?=$color_arr[$rows['COLOR_NUMBER_ID']];?></p></td>
											<td width="50" align="center"><?=$rows['SET_SMV'];?></td>
											<td width="50" align="right"><?=$rows['ALLOCATED_MP'];?></td>
											<td width="50" align="center">
											<?  echo max($planDataArr['WORKING_HOUR'][$key2]);?>
											<?//$resource_arr[WH][$key3];?></td>
											<td width="50" align="right"><?=round($PLAN_QNTY/$resource_arr['WH'][$key3]);?></td>

											<td width="80" align="right"><?=number_format(array_sum($planDataArr['PO_QUANTITY'][$key2]));?></td>
											<td width="80" align="right"><?=number_format(array_sum($planDataArr['PLAN_CUT'][$key2]));?></td>
											<td width="80" align="right"><?=number_format($sewing_out_qty);?></td>
											
											<td width="80" align="right" title="<?=$rows['FIRST_DAY_OUTPUT'].'; Max:'.max(explode(',',$rows['FIRST_DAY_OUTPUT']));?>">
											<?=max(explode(',',$rows['FIRST_DAY_OUTPUT']));
											//number_format((($rows[ALLOCATED_MP]*60)/$rows[SET_SMV])*max(explode(',',$rows[FIRST_DAY_OUTPUT])),2);?>
											</td>
											
											<td width="80" align="right"><?=number_format($PLAN_QNTY);?></td>
											<td width="60" align="center"><?=change_date_format($rows['PUB_SHIPMENT_DATE']);?></td>
											<td width="60" align="center"><?=change_date_format($rows['START_DATE']);?></td>
											<td width="60" align="center"><?=change_date_format($rows['END_DATE']);?></td>
											<td width="50" align="center" <? if($lateEarlyBy<0){?>  bgcolor="<?=$bg_late; ?>" <?}?> ><?=$lateEarlyBy;?></td>
											<td width="50" align="center"><?=number_format($pre_cost_arr['CM_COST'][$rows['JOB_NO']]/$costing_per_value,2);?></td>
											<td width="50" align="center"><?=implode(', ',$planDataArr['UNIT_PRICE'][$key2])." (".number_format($job_avg_rate,2).")";?></td>
											<td width="80" align="right"><?=number_format($total_cm,2);?></td>
											<td width="80" align="right"><?=number_format($total_fob,2);?></td>
											<? 
											foreach($monthArr as $dateKey=>$date){ 
												$floorTotal[planned_qty_month][$dateKey]+=$planDataArr['PLAN_QNTY'][$key2][$dateKey];
												$lineTotal[planned_qty_month][$dateKey]+=$planDataArr['PLAN_QNTY'][$key2][$dateKey];
												$locationTotal[planned_qty_month][$dateKey]+=$planDataArr['PLAN_QNTY'][$key2][$dateKey];
												$companyTotal[planned_qty_month][$dateKey]+=$planDataArr['PLAN_QNTY'][$key2][$dateKey];
												$grandTotal[planned_qty_month][$dateKey]+=$planDataArr['PLAN_QNTY'][$key2][$dateKey];
											?>
												<td width="50" align="right"><?=$planDataArr['PLAN_QNTY'][$key2][$dateKey];?></td> 
											<? } ?>
										</tr>
										<? $i++;
										
									}
									?>
									<tr bgcolor="#ffd9b3">
										<td colspan="19" align="right"><b>Line Total</b></td>
										<td align="right"><?=number_format($lineTotal[sewing_out]);?></td>
										<td></td>
										<td align="right"><?=number_format($lineTotal[planned_qty]);?></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td align="right"><?=number_format($lineTotal[total_cm]);?></td>
										<td align="right"><?=number_format($lineTotal[total_fob]);?></td>
										<? foreach($monthArr as $dateKey=>$date){ ?>
											<td width="50" align="right"><?=number_format($lineTotal[planned_qty_month][$dateKey]);?></td> 
										<? } ?>
									</tr>
									<?
								}
								?>
								<tr bgcolor="#FFFCCC">
									<td colspan="19" align="right"><b>Floor Total</b></td>
									<td align="right"><?=number_format($floorTotal[sewing_out]);?></td>
									<td></td>
									<td align="right"><?=number_format($floorTotal[planned_qty]);?></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td align="right"><?=number_format($floorTotal[total_cm]);?></td>
									<td align="right"><?=number_format($floorTotal[total_fob]);?></td>
									<? foreach($monthArr as $dateKey=>$date){ ?>
										<td width="50" align="right"><?=number_format($floorTotal[planned_qty_month][$dateKey]);?></td> 
									<? } ?>
								</tr>
								<?
							} 
							?>
							<tr bgcolor="#FFEDDD">
								<td colspan="19" align="right"><b>Location Total</b></td>
								<td align="right"><?=number_format($locationTotal[sewing_out]);?></td>
								<td></td>
								<td align="right"><?=number_format($locationTotal[planned_qty]);?></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td align="right"><?=number_format($locationTotal[total_cm]);?></td>
								<td align="right"><?=number_format($locationTotal[total_fob]);?></td>
								<? foreach($monthArr as $dateKey=>$date){ ?>
									<td width="50" align="right"><?=number_format($locationTotal[planned_qty_month][$dateKey]);?></td> 
								<? } ?>
							</tr>
							<?
						} 
						?>
						<tr bgcolor="#CCCDDD">
							<td colspan="4"><b>Total Planned Line Number:<? echo $tot_line_arr;?><b></td>
							<td colspan="15" align="right"><b>Company Total</b></td>
							<td align="right"><?=number_format($companyTotal[sewing_out]);?></td>
							<td></td>
							<td align="right"><?=number_format($companyTotal[planned_qty]);?></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td align="right"><?=number_format($companyTotal[total_cm]);?></td>
							<td align="right"><?=number_format($companyTotal[total_fob]);?></td>
							<? foreach($monthArr as $dateKey=>$date){ ?>
								<td width="50" align="right"><?=number_format($companyTotal[planned_qty_month][$dateKey]);?></td> 
							<? } ?>
						</tr>
						<?
					} 
					
					?>
				</tbody>
			</table>
		</div>

		<table width="<?=$width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
			<tfoot>
				<th colspan="19" align="right"><b>Grand Total</b></th>
				<th width="80" align="right"><?=number_format($grandTotal[sewing_out]);?></th>
				<th width="80" align="right"></th>
				<th width="80" align="right"><?=number_format($grandTotal[planned_qty]);?></th>
				<th width="60"></th>
				<th width="60"></th>
				<th width="60"></th>
				<th width="50"></th>
				<th width="50"></th>
				<th width="50"></th>
				<th width="80"><?=number_format($grandTotal[total_cm]);?></th>
				<th width="80"><?=number_format($grandTotal[total_fob]);?></th>
				<? foreach($monthArr as $dateKey=>$date){ ?>
					<th width="50" align="right"><?=number_format($grandTotal[planned_qty_month][$dateKey]);?></th> 
				<? } ?>
			</tfoot>
		</table>
	   </div>
	   <?
		$html=ob_get_contents();
		ob_clean();

		foreach (glob("$user_name*.xls") as $filename) 
		{
			@unlink($filename);
		}
		
		$filename=$user_name."_".time().".xls";
		$create_new_doc = fopen($filename, 'w');
		fwrite($create_new_doc,$html);
		
		echo "$html****$filename";
		exit();
	}
    // show 2 button
	else if($type ==2){
		 
		if($date_from!="" && $date_to!=""){
			$start_date=change_date_format($date_from,"","",1);
			$end_date=change_date_format($date_to,"","",1);
			$where_con=" and d.plan_date between '$start_date' and '$end_date'"; 
			$where_con_sub=" and d.plan_date between '$start_date' and '$end_date'"; 
		}

		if($buyer_id!=0){
			$where_con.=" and A.BUYER_NAME=$buyer_id"; 
			$where_con_sub.=" and A.PARTY_ID=$buyer_id"; 
		}
		if($location_id!=""){
			$where_con.=" and C.LOCATION_ID in($location_id)"; 
			$where_con_sub.=" and C.LOCATION_ID in($location_id)"; 
		}
		if($floor_id!=""){
			$where_con.=" and F.FLOOR_NAME in($floor_id)"; 
			$where_con_sub.=" and F.FLOOR_NAME in($floor_id)";
		}
		if($style_no!=""){
			$where_con.=" and LOWER(A.STYLE_REF_NO) like('%$style_no%')";
			$where_con_sub.=" and LOWER(b.CUST_STYLE_REF) like('%$style_no%')";  
		}
		//if($txt_internal_ref_no!=""){$where_con.=" and LOWER(b.GROUPING) like('%$txt_internal_ref_no%')"; }

		if($cbo_search_type==1 && $txt_search_by!=''){
			$where_con.=" and A.JOB_NO like('%$txt_search_by')";
			$where_con_sub.=" and A.SUBCON_JOB like('%$txt_search_by')"; 
		}
		if($cbo_search_type==2 && $txt_search_by!=''){
			$where_con.=" and B.PO_NUMBER like('%$txt_search_by')"; 
			$where_con_sub.=" and B.ORDER_NO like('%$txt_search_by')";
		}
		if($cbo_search_type==3 && $txt_search_by!=''){
			$where_con.=" and b.GROUPING like('%$txt_search_by')"; 
		}
		if($cbo_search_type==4 && $txt_search_by!=''){
			$item_id_arr=implode(',',return_library_array( "select id from lib_garment_item where is_deleted=0 and item_name like('%$txt_search_by')",'id','id'));
			$where_con.=" and c.ITEM_NUMBER_ID in($item_id_arr)"; 
			$where_con_sub.=" and e.ITEM_NUMBER_ID in($item_id_arr)"; 
		}
  
		
		$order_sql="SELECT C.COMPANY_ID,c.PLAN_ID,c.MERGED_PLAN_ID, C.LOCATION_ID, F.FLOOR_NAME, C.LINE_ID,  d.PLAN_QNTY, E.ITEM_NUMBER_ID, A.SET_SMV, (B.PO_QUANTITY*a.TOTAL_SET_QNTY) as PO_QUANTITY, B.PLAN_CUT, B.PUB_SHIPMENT_DATE, b.PO_TOTAL_PRICE, C.START_DATE, C.END_DATE, A.JOB_NO, A.ID AS JOB_ID, B.UNIT_PRICE,B.ID AS PO_ID,e.COLOR_NUMBER_ID, B.PO_NUMBER, cast(b.GROUPING as VARCHAR(4000)) as GROUPING, A.BUYER_NAME, A.STYLE_REF_NO, d.PLAN_DATE, A.BRAND_ID, c.ALLOCATED_MP, A.SEASON_BUYER_WISE, A.SEASON_YEAR, c.FIRST_DAY_OUTPUT, (d.WORKING_HOUR) as WORKING_HOUR, 1 as ORDER_TYPE,f.LINE_NAME
		from wo_po_details_master a, wo_po_break_down b, ppl_sewing_plan_board c, ppl_sewing_plan_board_dtls d, ppl_sewing_plan_board_powise e, LIB_SEWING_LINE f where a.job_no=b.job_no_mst and a.job_no=e.job_no  and b.id=e.po_break_down_id and c.plan_id=d.plan_id and d.plan_id=e.plan_id and f.id=C.LINE_ID  and c.company_id in($company_id) $where_con  and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	 
		$sub_sql = "SELECT C.COMPANY_ID,c.PLAN_ID,c.MERGED_PLAN_ID,C.LOCATION_ID,F.FLOOR_NAME,C.LINE_ID,d.PLAN_QNTY,E.ITEM_NUMBER_ID , 0 as SET_SMV,B.ORDER_QUANTITY as PO_QUANTITY,B.PLAN_CUT,B.DELIVERY_DATE as PUB_SHIPMENT_DATE,b.AMOUNT as PO_TOTAL_PRICE,C.START_DATE,C.END_DATE,A.SUBCON_JOB as JOB_NO,A.ID AS JOB_ID,B.RATE as UNIT_PRICE,B.ID AS PO_ID,e.COLOR_NUMBER_ID, B.ORDER_NO as PO_NUMBER,'' as GROUPING,A.PARTY_ID as BUYER_NAME,B.CUST_STYLE_REF as STYLE_REF_NO, d.PLAN_DATE,0 as BRAND_ID,c.ALLOCATED_MP, 0 as SEASON_BUYER_WISE, 0 as SEASON_YEAR,c.FIRST_DAY_OUTPUT, (d.WORKING_HOUR) as WORKING_HOUR, 2 as ORDER_TYPE,f.LINE_NAME from SUBCON_ORD_MST a, SUBCON_ORD_DTLS b, ppl_sewing_plan_board c,ppl_sewing_plan_board_dtls d,ppl_sewing_plan_board_powise e, LIB_SEWING_LINE f where a.id=b.mst_id and b.id=e.po_break_down_id and d.plan_id=e.plan_id and c.plan_id=d.plan_id and f.id=C.LINE_ID  and c.company_id in($company_id) $where_con_sub  and b.MAIN_PROCESS_ID =5  AND a.ENTRY_FORM = 238 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
				
		$sql = " SELECT x.* from ($order_sql union all $sub_sql) x   order by x.LINE_NAME,x.START_DATE";

		//echo $sql;die;

		$sql_result = sql_select($sql);

		// echo "<pre>";
		// print_r($sql_result);
		// echo "</pre>";
		// exit;

		$dataArr = array();
		foreach($sql_result as $row){
			$dateKey=date("d M-Y",strtotime($row['PLAN_DATE']));
			
			$dataArr[$row['COMPANY_ID']][$row['LOCATION_ID']][$row['FLOOR_NAME']][$row['LINE_ID']][$dateKey]=array(
				'JOB_NO'=>$row['JOB_NO'],
				'FLOOR_NAME'=>$row['FLOOR_NAME'],
				'LINE_ID'=>$row['LINE_ID'],
				'BUYER_NAME'=>$row['BUYER_NAME'],
				'STYLE_REF_NO'=>$row['STYLE_REF_NO'],
				'ITEM_NUMBER_ID'=>$row['ITEM_NUMBER_ID'],
				'SET_SMV'=>$row['SET_SMV'],
				'START_DATE'=>$row['START_DATE'],
				'END_DATE'=>$row['END_DATE'],
				'PUB_SHIPMENT_DATE'=>$row['PUB_SHIPMENT_DATE'],
				'COLOR_NUMBER_ID'=>$row['COLOR_NUMBER_ID'],
				'BRAND_ID'=>$row['BRAND_ID'],
				'SEASON_BUYER_WISE'=>$row['SEASON_BUYER_WISE'],
				'SEASON_YEAR'=>$row['SEASON_YEAR'],
				'ALLOCATED_MP'=>$row['ALLOCATED_MP'],
				'FIRST_DAY_OUTPUT'=>$row['FIRST_DAY_OUTPUT'],
				'PLAN_ID'=>$row['PLAN_ID'],
				'PLAN_QNTY'=>$row['PLAN_QNTY'],
				'PLAN_DATE'=>$row['PLAN_DATE'],
				'WORKING_HOUR'=>$row['WORKING_HOUR'],
			); 
		
			$m=date("ym",strtotime($row['PLAN_DATE']))*1;
			$d=date("d",strtotime($row['PLAN_DATE']))*1;
			$tempMonthArr[$m][$d]=$dateKey;
			  
			$plan_id_arr[$row['PLAN_ID']]=$row['PLAN_ID'];
			$companyWiseLine[$row['COMPANY_ID']][$row['LOCATION_ID']][$row['LINE_ID']]=$row['LINE_ID'];
		} 
 

		ksort($tempMonthArr);

		$monthArr=array();
		foreach($tempMonthArr as $valArr){
			asort($valArr);
			foreach($valArr as $date){
				$monthArr[$date]=$date;
			}
		}
		unset($tempMonthArr);
		?>
		<br/>
		<div style="margin:0 auto; width:1200px;">
			<?
			$i=1;
			$grandTotal=array();
			foreach($dataArr as $company_id=>$companyRows)
			{ 
				?>
				<table width="100%" id="table_header_1" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
				<?
				$companyTotal=array();
				foreach($companyRows as $location_id=>$locationRows)
				{
					$colspan = (count($companyWiseLine[$company_id][$location_id])*3)+11;
				?>
				<thead>
					<tr align="center">
						<th colspan="<?= $colspan; ?>"><?= $company_lib[$company_id]; ?></th> 
					</tr>
					<tr align="center">
						<th colspan="<?= $colspan; ?>">PRODUCTION PLAN</th> 
					</tr>
					
					<tr align="center">
						<th colspan="<?= $colspan; ?>">
							
							<?= $company_lib[$company_id]; ?>, <?= $line_location[$location_id];?>,
							<?php
							$floorid = '';
							foreach($locationRows as $floor_id=>$floorRows)
							{ 
								$floorid .= $floor_lib[$floor_id].',';
							}
							echo $floor_name = rtrim($floorid, ',');
							?>
						</th> 
					</tr>
					<tr>
						<th rowspan="2">Date</th>
						<th rowspan="2">Day</th>
						<?php
						foreach($locationRows as $floor_id=>$floorRows)
						{
							foreach($floorRows as $line_id=>$lineRows)
							{
								?>
									<th colspan="4">Line-<?=$line_lib[$line_id];?></th>
								<?php
							}
						}
						?>
						<th colspan="2">Total Qunatity</th>
					</tr>

					<tr>
						<?php
						foreach($locationRows as $floor_id=>$floorRows)
						{
							foreach($floorRows as $line_id=>$lineRows)
							{
								?> 
									<th>Style Ref</th>
									<th>Qnt.</th>
									<th>MP</th>
									<th>WH</th>
								<?php
							}
						}
						?>
						<th>Total Qunatity</th>
						<th>Total MP</th>
					</tr>
				</thead>
				<tbody> 
					<?php 
					$floorid = '';
					
					foreach($monthArr as $dateKey=>$date){
					?>
					<tr>
						<td align="center"><?= $date;?></td>
						<td align="center"><?= date('D', strtotime($date)); ?></td>
						<?
						$TotalQnt = '';
						$TotalMP = '';
						foreach($locationRows as $floorRows)
						{ 
							foreach($floorRows as $line_id=>$rows)
							{
								?>
								<td align="center"><?= $rows[$date]['STYLE_REF_NO']; ?></td>
								<td align="center"><?= $rows[$date]['PLAN_QNTY']; ?></td>
								<td align="center"><?= $rows[$date]['ALLOCATED_MP']; ?></td> 
								<td align="center"><?= $rows[$date]['WORKING_HOUR']; ?></td> 
								<?php
								$TotalQnt += $rows[$date]['PLAN_QNTY'];
								$TotalMP += $rows[$date]['ALLOCATED_MP'];
							}
							?>
							<?php
						}
						?>  
						<td align="center"><?= $TotalQnt; ?></td>
						<td align="center"><?= $TotalMP; ?></td>
					</tr>
					<?php
					}
					?>
				</tbody> 
				<?php
				}
				?>
				</table>
				<?
			}
		?> 

		</div>

		<?
		$html=ob_get_contents();
		ob_clean();

		foreach (glob("$user_name*.xls") as $filename) 
		{
			@unlink($filename);
		}
		
		$filename=$user_name."_".time().".xls";
		$create_new_doc = fopen($filename, 'w');
		fwrite($create_new_doc,$html);
		
		echo "$html****$filename";
		exit();
	 
	}
    // show 3 button
	elseif($type==3){
  
		if($date_from!="" && $date_to!=""){
			$start_date=change_date_format($date_from,"","",1);
			$end_date=change_date_format($date_to,"","",1);
			$where_con.=" and c.START_DATE between '$start_date' and '$end_date'";
			$where_con_sub=" and d.plan_date between '$start_date' and '$end_date'";  
		}

		if($buyer_id!=0){
			$where_con.=" and A.BUYER_NAME=$buyer_id";
			$where_con_sub.=" and A.PARTY_ID=$buyer_id"; 
		 }
		if($location_id!=""){
			$where_con.=" and C.LOCATION_ID in($location_id)"; 
			$where_con_sub.=" and C.LOCATION_ID in($location_id)"; 
		}
		if($floor_id!=""){
			$where_con.=" and F.FLOOR_NAME in($floor_id)";
			$where_con_sub.=" and F.FLOOR_NAME in($floor_id)";
		 }
		if($style_no!=""){
			$where_con.=" and LOWER(A.STYLE_REF_NO) like('%$style_no%')"; 
			$where_con_sub.=" and LOWER(b.CUST_STYLE_REF) like('%$style_no%')"; 
		}
		//if($txt_internal_ref_no!=""){$where_con.=" and LOWER(b.GROUPING) like('%$txt_internal_ref_no%')"; }

		if($cbo_search_type==1 && $txt_search_by!=''){
			$where_con.=" and A.JOB_NO like('%$txt_search_by')"; 
			$where_con_sub.=" and A.SUBCON_JOB like('%$txt_search_by')";
		}
		if($cbo_search_type==2 && $txt_search_by!=''){
			$where_con.=" and B.PO_NUMBER like('%$txt_search_by')"; 
			$where_con_sub.=" and B.ORDER_NO like('%$txt_search_by')";
		}
		if($cbo_search_type==3 && $txt_search_by!=''){
			$where_con.=" and b.GROUPING like('%$txt_search_by')"; 
		}
		if($cbo_search_type==4 && $txt_search_by!=''){
			$item_id_arr=implode(',',return_library_array( "select id from lib_garment_item where is_deleted=0 and item_name like('%$txt_search_by')",'id','id'));
			$where_con.=" and c.ITEM_NUMBER_ID in($item_id_arr)"; 
			$where_con_sub.=" and e.ITEM_NUMBER_ID in($item_id_arr)"; 
		}
 

		$order_sql="SELECT C.COMPANY_ID,c.PLAN_ID,c.MERGED_PLAN_ID,C.LOCATION_ID,F.FLOOR_NAME,C.LINE_ID,d.PLAN_QNTY,E.ITEM_NUMBER_ID , A.SET_SMV,(B.PO_QUANTITY*a.TOTAL_SET_QNTY) as PO_QUANTITY,B.PLAN_CUT,B.PUB_SHIPMENT_DATE,b.PO_TOTAL_PRICE,C.START_DATE,C.END_DATE,A.JOB_NO,A.ID AS JOB_ID,B.UNIT_PRICE,B.ID AS PO_ID,e.COLOR_NUMBER_ID, B.PO_NUMBER,cast(b.GROUPING as VARCHAR(4000)) as GROUPING,A.BUYER_NAME,A.STYLE_REF_NO, d.PLAN_DATE,A.BRAND_ID,c.ALLOCATED_MP, A.SEASON_BUYER_WISE, A.SEASON_YEAR,c.FIRST_DAY_OUTPUT, (d.WORKING_HOUR) as WORKING_HOUR, 1 as ORDER_TYPE,f.LINE_NAME
		from wo_po_details_master a, wo_po_break_down b, ppl_sewing_plan_board c,ppl_sewing_plan_board_dtls d,ppl_sewing_plan_board_powise e, LIB_SEWING_LINE f  where  a.job_no=b.job_no_mst and a.job_no=e.job_no  and b.id=e.po_break_down_id and c.plan_id=d.plan_id and d.plan_id=e.plan_id and f.id=C.LINE_ID  and c.company_id in($company_id) $where_con  and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";

		$sub_sql = "SELECT C.COMPANY_ID,c.PLAN_ID,c.MERGED_PLAN_ID,C.LOCATION_ID,F.FLOOR_NAME,C.LINE_ID,d.PLAN_QNTY,E.ITEM_NUMBER_ID , 0 as SET_SMV,B.ORDER_QUANTITY as PO_QUANTITY,B.PLAN_CUT,B.DELIVERY_DATE as PUB_SHIPMENT_DATE,b.AMOUNT as PO_TOTAL_PRICE,C.START_DATE,C.END_DATE,A.SUBCON_JOB as JOB_NO,A.ID AS JOB_ID,B.RATE as UNIT_PRICE,B.ID AS PO_ID,e.COLOR_NUMBER_ID, B.ORDER_NO as PO_NUMBER,'' as GROUPING,A.PARTY_ID as BUYER_NAME,B.CUST_STYLE_REF as STYLE_REF_NO, d.PLAN_DATE,0 as BRAND_ID,c.ALLOCATED_MP, 0 as SEASON_BUYER_WISE, 0 as SEASON_YEAR,c.FIRST_DAY_OUTPUT, (d.WORKING_HOUR) as WORKING_HOUR, 2 as ORDER_TYPE,f.LINE_NAME from SUBCON_ORD_MST a, SUBCON_ORD_DTLS b, ppl_sewing_plan_board c,ppl_sewing_plan_board_dtls d,ppl_sewing_plan_board_powise e, LIB_SEWING_LINE f where a.id=b.mst_id and b.id=e.po_break_down_id and d.plan_id=e.plan_id and c.plan_id=d.plan_id and f.id=C.LINE_ID  and c.company_id in($company_id) $where_con_sub  and b.MAIN_PROCESS_ID =5  AND a.ENTRY_FORM = 238 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
						
		$sql = " SELECT x.* from ($order_sql union all $sub_sql) x   order by x.LINE_NAME,x.START_DATE";

		//echo $sql;

 
		//$sql="SELECT C.COMPANY_ID,c.PLAN_ID,c.MERGED_PLAN_ID,C.LOCATION_ID,F.FLOOR_NAME,C.LINE_ID,C.PLAN_ID,E.ITEM_NUMBER_ID , A.SET_SMV,(B.PO_QUANTITY*a.TOTAL_SET_QNTY) as PO_QUANTITY,B.PLAN_CUT,B.PUB_SHIPMENT_DATE,b.PO_TOTAL_PRICE,C.START_DATE,C.END_DATE,A.JOB_NO,A.ID AS JOB_ID,B.UNIT_PRICE,B.ID AS PO_ID,e.COLOR_NUMBER_ID, B.PO_NUMBER,b.GROUPING,A.BUYER_NAME,A.STYLE_REF_NO,A.BRAND_ID,c.ALLOCATED_MP, A.SEASON_BUYER_WISE, A.SEASON_YEAR,c.FIRST_DAY_OUTPUT
		//from wo_po_details_master a, wo_po_break_down b, ppl_sewing_plan_board c,ppl_sewing_plan_board_powise e, LIB_SEWING_LINE f  where  a.job_no=b.job_no_mst and a.job_no=e.job_no  and b.id=e.po_break_down_id and f.id=C.LINE_ID  and c.company_id in($company_id) $where_con  and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by f.LINE_NAME,C.START_DATE";

 
	 // echo $sql;die;//and b.status_active=1

		$dataArr=array();	
		$sql_result=sql_select($sql);
		foreach($sql_result as $row){
			$dateKey=date("d M-Y",strtotime($row['PLAN_DATE']));
			
			$dataArr[$row['COMPANY_ID']][$row['LOCATION_ID']][$row['FLOOR_NAME']][$row['LINE_ID']][$row['PLAN_ID']]=array(
				'JOB_NO'=>$row['JOB_NO'],
				'FLOOR_NAME'=>$row['FLOOR_NAME'],
				'LINE_ID'=>$row['LINE_ID'],
				'BUYER_NAME'=>$row['BUYER_NAME'],
				'STYLE_REF_NO'=>$row['STYLE_REF_NO'],
				'ITEM_NUMBER_ID'=>$row['ITEM_NUMBER_ID'],
				'SET_SMV'=>$row['SET_SMV'],
				'START_DATE'=>$row['START_DATE'],
				'END_DATE'=>$row['END_DATE'],
				'PUB_SHIPMENT_DATE'=>$row['PUB_SHIPMENT_DATE'],
				'COLOR_NUMBER_ID'=>$row['COLOR_NUMBER_ID'],
				'BRAND_ID'=>$row['BRAND_ID'],
				'SEASON_BUYER_WISE'=>$row['SEASON_BUYER_WISE'],
				'SEASON_YEAR'=>$row['SEASON_YEAR'],
				'ALLOCATED_MP'=>$row['ALLOCATED_MP'],
				'FIRST_DAY_OUTPUT'=>$row['FIRST_DAY_OUTPUT'],
				'PLAN_ID'=>$row['PLAN_ID'],
				
			);
			
			
			$key2=$row["COMPANY_ID"].'**'.$row["LOCATION_ID"].'**'.$row['FLOOR_NAME'].'**'.$row['LINE_ID'].'**'.$row["PLAN_ID"];
			$planDataArr["PLAN_QNTY"][$key2][$dateKey]=$row['PLAN_QNTY'];
			
			$planDataArr["PO_QUANTITY"][$key2][$row['PO_ID']]=$row['PO_QUANTITY'];
			$planDataArr['PO_TOTAL_PRICE'][$key2][$row['PO_ID']]=$row['PO_TOTAL_PRICE'];
			$planDataArr['PLAN_CUT'][$key2][$row['PO_NUMBER']]=$row['PLAN_CUT'];
			$planDataArr['PO_NUMBER'][$key2][$row['PO_NUMBER']]=$row['PO_NUMBER'];
			$planDataArr['GROUPING'][$key2][$row['GROUPING']]=$row['GROUPING'];
			$planDataArr['UNIT_PRICE'][$key2][$row['PO_ID']]=$row['UNIT_PRICE'];
			$planDataArr['WORKING_HOUR'][$key2][$row['WORKING_HOUR']]=$row['WORKING_HOUR'];
			$allFloorArr[$row['FLOOR_NAME']]=$row['FLOOR_NAME'];
			$allJobIdArr[$row['JOB_ID']]=$row['JOB_ID'];
			$allPoIdArr[$row['PO_ID']]=$row['PO_ID'];
			$allColorArr[$row['COLOR_NUMBER_ID']]=$row['COLOR_NUMBER_ID'];
			$m=date("ym",strtotime($row['PLAN_DATE']))*1;
			$d=date("d",strtotime($row['PLAN_DATE']))*1;
			$tempMonthArr[$m][$d]=$dateKey;
			$plan_id_arr[$row['PLAN_ID']]=$row['PLAN_ID'];
		}
		unset($sql_result);
		ksort($tempMonthArr); 
	
		// print_r($key2);
	
		//echo "select PLAN_ID,LINE_ID from PPL_SEWING_PLAN_BOARD where 1=1 ".where_con_using_array($parge_plan_id,0,'PLAN_ID')."";die; 
	
		$marged_line_arr=return_library_array( "select MERGED_PLAN_ID,LINE_ID from PPL_SEWING_PLAN_BOARD where STATUS_ACTIVE=1 and IS_DELETED=0 ".where_con_using_array($plan_id_arr,0,'MERGED_PLAN_ID')."", "MERGED_PLAN_ID", "LINE_ID"  );
		
		$monthArr=array();
		foreach($tempMonthArr as $valArr){
			asort($valArr);
			foreach($valArr as $date){
				$monthArr[$date]=$date;
			}
		}
		unset($tempMonthArr);

	
		$resource_sql="SELECT A.COMPANY_ID,A.LOCATION_ID,A.FLOOR_ID,A.LINE_NUMBER,B.ACTIVE_MACHINE,B.HELPER,B.WORKING_HOUR,B.TARGET_EFFICIENCY from prod_resource_mst a ,prod_resource_dtls_mast b where a.id=b.mst_id and a.company_id in($company_id) ".where_con_using_array($allFloorArr,0,'a.FLOOR_ID')."";
		//echo $resource_sql;die;
		$resource_sql_res=sql_select($resource_sql);
		
		foreach($resource_sql_res as $row)
		{
			$lineArr=explode(",",$row["LINE_NUMBER"]);
			foreach($lineArr as $line_id)
			{
				$key3=$row[COMPANY_ID].'**'.$row["LOCATION_ID"].'**'.$row["FLOOR_ID"].'**'.$line_id;
				$resource_arr[WH][$key3]=$row["WORKING_HOUR"];
				$resource_arr[TGT][$key3]=$row["TARGET_EFFICIENCY"];
			}
		}
	    unset($resource_sql_res);
	
		$pre_cost_sql="select a.COSTING_PER,b.JOB_NO, b.CM_COST from wo_pre_cost_mst a,wo_pre_cost_dtls b where a.job_no=b.job_no and  b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and  a.STATUS_ACTIVE=1 and a.IS_DELETED=0 ".where_con_using_array($allJobIdArr,0,'b.job_id')."";
		//echo $pre_cost_sql;die;
		
		$pre_cost_sql_res=sql_select($pre_cost_sql); 
		foreach($pre_cost_sql_res as $row)
		{
			$pre_cost_arr['CM_COST'][$row['JOB_NO']]=$row['CM_COST'];
			$pre_cost_arr['COSTING_PER'][$row['JOB_NO']]=$row['COSTING_PER'];
		}
		unset($pre_cost_sql_res);

		
	
	
	
		$production_sql="SELECT  B.LINE_NUMBER,A.PO_BREAK_DOWN_ID,A.ITEM_NUMBER_ID,SUM(A.PRODUCTION_QUANTITY) AS QTY from pro_garments_production_mst a,prod_resource_mst b where a.sewing_line=b.id   and  a.status_active=1 and a.production_type=5 ".where_con_using_array($allPoIdArr,0,'a.po_break_down_id')." group by  b.line_number,a.po_break_down_id,a.item_number_id ";
		$production_sql_res=sql_select($production_sql);
		foreach($production_sql_res as $p)
		{
			$val=array_unique(explode(",",$p["LINE_NUMBER"]));
			foreach($val as $line_key)
			{
				$production_arr[$p["PO_BREAK_DOWN_ID"]][$p["ITEM_NUMBER_ID"]][$line_key]+=$p[QTY];
			}
		}
		unset($production_sql_res);
		
	   //print_r($production_sql);die;
	
	   $color_arr=return_library_array( "select ID, COLOR_NAME from LIB_COLOR where STATUS_ACTIVE=1 and IS_DELETED=0 ".where_con_using_array($allColorArr,0,'id')."", "id", "COLOR_NAME"  );

	
		$width=(count($monthArr)*50)+2130;

		ob_start();	
		?>
		<div style="margin:0 auto; width:<?=$width+25;?>px;">
		<table width="100%" border="0">
			<thead>
				<tr><td align="center" colspan="<?=(count($monthArr)+25);?>" style="font-size:18px; font-weight:bold; text-decoration:underline;">Line Wise Planning Report</td></tr>
				<tr><td align="center" colspan="<?=(count($monthArr)+25);?>"><b>Plan Date Range : <?=change_date_format($date_from);?> to <?=change_date_format($date_to);?></b></td></tr>
				<tr><td align="center" colspan="<?=(count($monthArr)+25);?>">Report Generate on : <?=date("d-m-Y, h:i A");?></td></tr>
			</thead>
		</table>

		<table width="<?=$width;?>" id="table_header_1"cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="60">Floor</th>
				<th width="60">Line</th>
				<th width="60">Buyer</th>
				<th width="60">Brand</th>
				<th width="60">Season</th>
				<th width="60">Season Year</th>
				<th width="80">Job</th>
				<th width="80">Style Ref</th>
				<th width="100">Internal Ref</th>
				<th width="100">PO Number</th>
				<th width="100">Item</th>
				<th width="100">Color</th>
				<th width="50">SMV</th>
				<th width="50" title="Man Power">MP</th>
				<th width="50" title="Working Hour">WH</th>
				<th width="50" title="Hourly Plan qty">HPQ</th>
				<th width="80">Order Qty</th>
				<th width="80">Plan Cut Qty</th>
				<th width="80">Output</th>
				<th width="80">Plan Eff%</th>
				<th width="80">Planned Qty</th>
				<th width="60">Ship Date</th>
				<th width="60">Sewing Start</th>
				<th width="60">Sewing End</th>
				<th width="50">Late/Early By</th>
				<th width="50">CM/Pcs</th>
				<th width="50">FOB Price</th>
				<th width="80">Total CM</th>
				<th width="80">Total FOB</th>
				<? foreach($monthArr as $date){ ?><th width="50"><?=$date;?></th> <? } ?>
			</thead>
		</table>



		<div style="width:<?=$width+18;?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
			<table width="<?=$width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
				<tbody>
					<?  
					$i=1;
					$dat = 0;
					$planned_minutes=0;
					$total_smv = 0;
					$grandTotal=array();
					foreach($dataArr as $company_id=>$companyRows)
					{
						$companyTotal=array();
						foreach($companyRows as $location_id=>$locationRows)
						{
							$locationTotal=array();
							echo "<tr bgcolor='#CCCCCC'><td colspan='".(count($monthArr)+30)."'> {$company_lib[$company_id]}, {$line_location[$location_id]} </td></tr>"; 
							foreach($locationRows as $floor_id=>$floorRows)
							{ 
								$floorTotal=array();
								$floorTotalSMV=array();
								
								foreach($floorRows as $line_id=>$lineRows)
								{ 
									$planned_minutes_arr = array();
									$lineTotal=array();$s=1;
									foreach($lineRows as $plan_id=>$rows)
									{ 
										$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
										$key2=$company_id.'**'.$location_id.'**'.$floor_id.'**'.$line_id.'**'.$plan_id;
										$key3=$company_id.'**'.$location_id.'**'.$floor_id.'**'.$line_id;
										
										//$lateEarlyBy = (abs(strtotime($rows[END_DATE]) - strtotime($rows[PUB_SHIPMENT_DATE])))/(60*60*24);
										$lateEarlyBy = (strtotime($rows["PUB_SHIPMENT_DATE"]) - strtotime($rows["END_DATE"]))/(60*60*24);
										$PLAN_QNTY=array_sum($planDataArr["PLAN_QNTY"][$key2]);										
																				
										$sewing_out_qty=0;
										foreach($planDataArr["PO_QUANTITY"][$key2] as $poId=>$v){
											$sewing_out_qty+=$production_arr[$poId][$rows["ITEM_NUMBER_ID"]][$line_id];
										}
										
										
										$costing_per=$pre_cost_arr["COSTING_PER"][$rows["JOB_NO"]];
										if($costing_per==1){$costing_per_value=12;}
										else if($costing_per==2){ $costing_per_value=1;}
										else if($costing_per==3){ $costing_per_value=24;}
										else if($costing_per==4){ $costing_per_value=36;}
										else if($costing_per==5){ $costing_per_value=48;}
										else{$costing_per_value=0;}
										
										 
										$job_avg_rate=array_sum($planDataArr["PO_TOTAL_PRICE"][$key2])/array_sum($planDataArr["PO_QUANTITY"][$key2]);
										
										
										$total_cm=$PLAN_QNTY*($pre_cost_arr["CM_COST"][$rows["JOB_NO"]]/$costing_per_value);
										$total_fob=$PLAN_QNTY*$job_avg_rate;
										
										//floor..................................
										$floorTotal["SEWING_OUT"]+=$sewing_out_qty;
										$floorTotal["PLANNED_QTY"]+=$PLAN_QNTY;
										$floorTotal["TOTAL_CM"]+=$total_cm;
										$floorTotal["TOTAL_FOB"]+=$total_fob;
 
										//Line..................................
										$lineTotal["SEWING_OUT"]+=$sewing_out_qty;
										$lineTotal["PLANNED_QTY"]+=$PLAN_QNTY;
										$lineTotal["TOTAL_CM"]+=$total_cm;
										$lineTotal["TOTAL_FOB"]+=$total_fob;
										//location..................................
										$locationTotal["SEWING_OUT"]+=$sewing_out_qty;
										$locationTotal["PLANNED_QTY"]+=$PLAN_QNTY;
										$locationTotal["TOTAL_CM"]+=$total_cm;
										$locationTotal["TOTAL_FOB"]+=$total_fob;
										//company..................................
										$companyTotal["SEWING_OUT"]+=$sewing_out_qty;
										$companyTotal["PLANNED_QTY"]+=$PLAN_QNTY;
										$companyTotal["TOTAL_CM"]+=$total_cm;
										$companyTotal["TOTAL_FOB"]+=$total_fob;
										//grand..................................
										$grandTotal["SEWING_OUT"]+=$sewing_out_qty;
										$grandTotal["PLANNED_QTY"]+=$PLAN_QNTY;
										$grandTotal["TOTAL_CM"]+=$total_cm;
										$grandTotal["TOTAL_FOB"]+=$total_fob;
										
										$bg_late="#FF0000";
										
										
										?>
										<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>">
											<td align="center" width="30"><?=$s++;?></td>
											<td width="60"><p><?=$floor_lib[$floor_id];?></p></td>
											<td width="60" title="Plan id:<?=$rows['PLAN_ID'];?>">
												<p>
													<?=$line_lib[$rows['LINE_ID']];?>
													<?=($marged_line_arr[$rows['PLAN_ID']])?",<b style='color:red;'>".$line_lib[$marged_line_arr[$rows['PLAN_ID']]]."</b>":'';?>
												</p>
											</td>
											<td width="60"><?=$buyer_lib[$rows['BUYER_NAME']];?></td>
											<td width="60"><?=$buyer_brand_arr[$rows['BRAND_ID']];?></td>
											<td width="60"><?=$buyer_season_arr[$rows['SEASON_BUYER_WISE']];?></td>
											<td width="60"><?=$rows['SEASON_YEAR'];?></td>
											<td width="80" align="center"><?=$rows['JOB_NO'];?></td>
											<td width="80"><p><?=$rows['STYLE_REF_NO'];?></p></td>
											<td width="100"><p><?=implode(', ',$planDataArr['GROUPING'][$key2]);?></p></td>

											<td width="100"><p><?=implode(', ',$planDataArr['PO_NUMBER'][$key2]);?></p></td>

											<td width="100"><p><?=$garments_item[$rows['ITEM_NUMBER_ID']];?></p></td>
											<td width="100" align="center"><p><?=$color_arr[$rows['COLOR_NUMBER_ID']];?></p></td>
											<td width="50" align="center"><?=$rows['SET_SMV'];?></td>
											<td width="50" align="right"><?=$rows['ALLOCATED_MP'];?></td>
											<td width="50" align="center">
											<?  echo max($planDataArr['WORKING_HOUR'][$key2]);?>
											<?//$resource_arr[WH][$key3];?></td>
											<td width="50" align="right"><?=round($PLAN_QNTY/$resource_arr['WH'][$key3]);?></td>

											<td width="80" align="right"><?=number_format(array_sum($planDataArr['PO_QUANTITY'][$key2]));?></td>
											<td width="80" align="right"><?=number_format(array_sum($planDataArr['PLAN_CUT'][$key2]));?></td>
											<td width="80" align="right"><?=number_format($sewing_out_qty);?></td>
											
											<td width="80" align="right" title="<?=$rows['FIRST_DAY_OUTPUT'].'; Max:'.max(explode(',',$rows['FIRST_DAY_OUTPUT']));?>">
											<?=max(explode(',',$rows['FIRST_DAY_OUTPUT']));
											//number_format((($rows[ALLOCATED_MP]*60)/$rows[SET_SMV])*max(explode(',',$rows[FIRST_DAY_OUTPUT])),2);?>
											</td>
											
											<td width="80" align="right"><?=number_format($PLAN_QNTY);?></td>
											<td width="60" align="center"><?=change_date_format($rows['PUB_SHIPMENT_DATE']);?></td>
											<td width="60" align="center"><?=change_date_format($rows['START_DATE']);?></td>
											<td width="60" align="center"><?=change_date_format($rows['END_DATE']);?></td>
											<td width="50" align="center" <? if($lateEarlyBy<0){?>  bgcolor="<?=$bg_late; ?>" <?}?> ><?=$lateEarlyBy;?></td>
											<td width="50" align="center"><?=number_format($pre_cost_arr['CM_COST'][$rows['JOB_NO']]/$costing_per_value,2);?></td>
											<td width="50" align="center"><?=implode(', ',$planDataArr['UNIT_PRICE'][$key2])." (".number_format($job_avg_rate,2).")";?></td>
											<td width="80" align="right"><?=number_format($total_cm,2);?></td>
											<td width="80" align="right"><?=number_format($total_fob,2);?></td>
											<?
											$cvalues = 0;
											foreach($monthArr as $dateKey=>$date){
												$floorTotal["PLANNED_QTY_MONTH"][$dateKey]+=$planDataArr['PLAN_QNTY'][$key2][$dateKey];
												$lineTotal["PLANNED_QTY_MONTH"][$dateKey]+=$planDataArr['PLAN_QNTY'][$key2][$dateKey];
												$locationTotal["PLANNED_QTY_MONTH"][$dateKey]+=$planDataArr['PLAN_QNTY'][$key2][$dateKey];
												$companyTotal["PLANNED_QTY_MONTH"][$dateKey]+=$planDataArr['PLAN_QNTY'][$key2][$dateKey];
												$grandTotal["PLANNED_QTY_MONTH"][$dateKey]+=$planDataArr['PLAN_QNTY'][$key2][$dateKey];

											    $floorTotalSMV["PLANNED_QTY_MONTH"][$dateKey] = $rows['SET_SMV'];

											?>
												<td width="50" align="right">
													<?php
													if($planDataArr['PLAN_QNTY'][$key2][$dateKey] != ''){
														$totals = $planDataArr['PLAN_QNTY'][$key2][$dateKey]*$floorTotalSMV["PLANNED_QTY_MONTH"][$dateKey];
														$planned_minutes_arr[$dateKey] += $totals;
													}
													else{
														$totals = '';
													}
													?>
													<?=$planDataArr['PLAN_QNTY'][$key2][$dateKey];?>
												</td> 
											<? 
											}
											 
											?>
										</tr>
										<? 
										$i++;

										
										
										$planned_minutes = $lineTotal["PLANNED_QTY"]*$rows['SET_SMV'];
										$total_smv += $rows['SET_SMV'];
									} 
									//print_r($planned_minutes_arr);die;
									?>
									<tr bgcolor="#ffd9b3">
										<td colspan="19" align="right"><b>Line Total</b></td>
										<td align="right"><?=number_format($lineTotal["SEWING_OUT"]);?></td>
										<td></td>
										<td align="right"><?=number_format($lineTotal["PLANNED_QTY"]);?></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td align="right"><?=number_format($lineTotal["TOTAL_CM"]);?></td>
										<td align="right"><?=number_format($lineTotal["TOTAL_FOB"]);?></td>
										<? foreach($monthArr as $dateKey=>$date){ ?>
											<td width="50" align="right"><?=number_format($lineTotal["PLANNED_QTY_MONTH"][$dateKey]);?></td> 
										<? } ?>
									</tr>

									<tr bgcolor="#ffd9b3"> 
										<td colspan="19" align="right"><b>Planned Minutes</b></td>
										<td align="right"><?=number_format($lineTotal["SEWING_OUT"]);?></td>
										<td></td>
										<td align="right"><?=number_format($planned_minutes );?></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td align="right"><?=number_format($lineTotal["TOTAL_CM"]);?></td>
										<td align="right"><?=number_format($lineTotal["TOTAL_FOB"]);?></td>
										<? foreach($monthArr as $dateKey=>$date){ 
											$total_planned_minutes = $planned_minutes_arr[$dateKey] 
											?>
											<td width="50" align="right"><?= number_format($total_planned_minutes);?></td> 
										<? } ?>
									</tr>

									<?
								}
								?>
								<tr bgcolor="#FFFCCC">
									<td colspan="19" align="right"><b>Floor Total</b></td>
									<td align="right"><?=number_format($floorTotal[sewing_out]);?></td>
									<td></td>
									<td align="right"><?=number_format($floorTotal[planned_qty]);?></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td align="right"><?=number_format($floorTotal[total_cm]);?></td>
									<td align="right"><?=number_format($floorTotal[total_fob]);?></td>
									<? foreach($monthArr as $dateKey=>$date){ ?>
										<td width="50" align="right"><?=number_format($floorTotal[planned_qty_month][$dateKey]);?></td> 
									<? } ?>
								</tr>
								<?
							} 
							?>
							<tr bgcolor="#FFEDDD">
								<td colspan="19" align="right"><b>Location Total</b></td>
								<td align="right"><?=number_format($locationTotal[sewing_out]);?></td>
								<td></td>
								<td align="right"><?=number_format($locationTotal[planned_qty]);?></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td align="right"><?=number_format($locationTotal[total_cm]);?></td>
								<td align="right"><?=number_format($locationTotal[total_fob]);?></td>
								<? foreach($monthArr as $dateKey=>$date){ ?>
									<td width="50" align="right"><?=number_format($locationTotal[planned_qty_month][$dateKey]);?></td> 
								<? } ?>
							</tr>
							<?
						} 
						?>
						<tr bgcolor="#CCCDDD">
							<td colspan="19" align="right"><b>Company Total</b></td>
							<td align="right"><?=number_format($companyTotal[sewing_out]);?></td>
							<td></td>
							<td align="right"><?=number_format($companyTotal[planned_qty]);?></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td align="right"><?=number_format($companyTotal[total_cm]);?></td>
							<td align="right"><?=number_format($companyTotal[total_fob]);?></td>
							<? foreach($monthArr as $dateKey=>$date){ ?>
								<td width="50" align="right"><?=number_format($companyTotal[planned_qty_month][$dateKey]);?></td> 
							<? } ?>
						</tr>
						<?
					} 
					
					?>
				</tbody>
			</table>
		</div>

		<table width="<?=$width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
			<tfoot>
				<th colspan="19" align="right"><b>Grand Total</b></th>
				<th width="80" align="right"><?=number_format($grandTotal[sewing_out]);?></th>
				<th width="80" align="right"></th>
				<th width="80" align="right"><?=number_format($grandTotal[planned_qty]);?></th>
				<th width="60"></th>
				<th width="60"></th>
				<th width="60"></th>
				<th width="50"></th>
				<th width="50"></th>
				<th width="50"></th>
				<th width="80"><?=number_format($grandTotal[total_cm]);?></th>
				<th width="80"><?=number_format($grandTotal[total_fob]);?></th>
				<? foreach($monthArr as $dateKey=>$date){ ?>
					<th width="50" align="right"><?=number_format($grandTotal[planned_qty_month][$dateKey]);?></th> 
				<? } ?>
			</tfoot>
		</table>
	   </div>
	   <?
		$html=ob_get_contents();
		ob_clean();

		foreach (glob("$user_name*.xls") as $filename) 
		{
			@unlink($filename);
		}
		
		$filename=$user_name."_".time().".xls";
		$create_new_doc = fopen($filename, 'w');
		fwrite($create_new_doc,$html);
		
		echo "$html****$filename";
		exit();
	}

}



?>
      
 