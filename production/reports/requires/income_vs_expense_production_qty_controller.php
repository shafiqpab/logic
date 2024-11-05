<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];
$color_Arr_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );	
$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  ); 
$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  );
  
if($action=="print_button_variable_setting")
{
	 
    $print_report_format_arr = return_library_array("select format_id,format_id from lib_report_template where template_name in($data) and module_id=7 and report_id=80 and is_deleted=0 and status_active=1","format_id","format_id");
    echo "print_report_button_setting('".implode(',',$print_report_format_arr)."');\n";
    exit(); 
}

if($action=="load_report_format")
{
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=7 and report_id in(40) and is_deleted=0 and status_active=1");
 	echo trim($print_report_format);	
	exit();

}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 120, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/daily_rmg_production_status_format2_controller', this.value, 'load_drop_down_floor', 'floor_td' );",0 );
	exit();  	 
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor", 110, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select --", $selected, "",0 ); 
	exit();   	 
}

if ($action=="load_drop_down_buyer")
{
	//echo $data; exit();
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond group by buy.id,buy.buyer_name order by buyer_name ","id,buyer_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/income_vs_expense_production_qty_controller',this.value, 'load_drop_down_season_buyer', 'season_td' );load_drop_down( 'requires/income_vs_expense_production_qty_controller',this.value, 'load_drop_down_brand', 'brand_td' )" );     	 
	exit();
}

if ($action=="load_drop_down_brand")
{
	echo create_drop_down( "cbo_brand_name", 100, "select id, brand_name from lib_buyer_brand where buyer_id='$data' and status_active =1 and is_deleted=0 order by brand_name ASC","id,brand_name", 1, "--Select Brand--", $selected, "" );
	exit();
}
if ($action=="load_drop_down_season_buyer")
{
    echo create_drop_down( "cbo_season_name", 100, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", "", "" );
    exit();
}


if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 120, "select id, location_name from lib_location where company_id='$data'","id,location_name", 1, "-- Select --", $selected, "" );     	 
	exit();
}

if ($action=="load_drop_down_buyer_popup")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );     	 
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
		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style ) 
			{ 
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( job_id )
		{
			var arrs=job_id.split("_");
 			document.getElementById('selected_id').value=arrs[0];
 			document.getElementById('selected_name').value=arrs[1]; 
			parent.emailwindow.hide();
		}

		function dynamic_ttl_change(data)
		{
			var titles="";
			if(data==1)
			{
				titles="Job No";
			}
			else if(data==2)
			{
				titles="Style Ref."
			}
			else if(data==3)
			{
				titles="Po No.";
			}
			else
			{
				titles="Job No";
			}
			$("#dynamic_ttl").html(titles);
			$("#dynamic_ttl").css('color','blue');
		}
		
		
		
    </script>

    </head>
    <body>
        <div align="center" style="width:100%;" >
            <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
                <table width="600" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
                    <thead>
                        
                        <tr>                	 
                            <th width="150" class="must_entry_caption">Company Name</th>
                            <th width="130" class="">Buyer Name</th>
                            <th width="100" class="must_entry_caption">Search By</th>
                            <th width="100" id="dynamic_ttl"class="must_entry_caption">Job No</th>
                             <th>&nbsp;</th>
                        </tr>           
                    </thead>
                    <tr class="general">
                        <td>
                        <input type="hidden" id="selected_id">
                        <input type="hidden" id="selected_name"> 
                            <?
                            $search_by_arr=[1=>"Job No",2=>"Style Ref.",3=>"Po No"];
                             echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company,"load_drop_down( 'daily_rmg_production_status_format2_controller', this.value, 'load_drop_down_buyer_popup', 'buyer_td_popup' );" );

                             ?>
                        </td>
                        <td id="buyer_td_popup"><? asort($buyer_arrs);echo create_drop_down( "cbo_buyer_name", 130, $buyer_arrs,'', 1, "-- Select Buyer --" ); ?></td>
                        <td>
	                        <? echo create_drop_down( "cbo_search_by", 100, $search_by_arr,'',0, "-- Select--", '',"dynamic_ttl_change(this.value);" );
	                        ?>
                        	
                        </td>
                        <td><input type="text" style="width:100px" class="text_boxes"  name="txt_job_po_style_no" id="txt_job_po_style_no" /></td>
                        <input type="hidden" name="hidden_job_year" id="hidden_job_year" value="<? echo $job_year;?>">
                        
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_job_po_style_no').value+'_'+document.getElementById('hidden_job_year').value, 'create_job_list_view', 'search_div', 'income_vs_expense_production_qty_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
                    </tr>
                    
                </table>
            </form>
        </div>
        <div id="search_div"></div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    
    <?
	
	exit();
}

if($action=="create_job_list_view")
{
	$data=explode('_',$data);
	if(!$data[0])
	{
		echo '<div style="text-align: center;color: red;font-weight: bold;font-size: 14px;">Select Company Name</div>';die;
	}
	elseif($data[3]=="")
	{
		echo '<div style="text-align: center;color: red;font-weight: bold;font-size: 14px;">Please enter search string</div>';die;
	}
	$str_cond="";
	$str_cond.=($data[0])? " and a.company_name='$data[0]' " : "";
	$str_cond.=($data[1])? " and a.buyer_name='$data[1]' " : "";
	if($data[3])
	{
		if($data[2]==1)
		{
			$str_cond.= " and a.job_no_prefix_num='$data[3]'";

		}
		else if($data[2]==2)
		{
			$str_cond.= " and a.style_ref_no like '%$data[3]%'";

		}
		else if($data[2]==3)
		{
			$str_cond.= " and b.po_number like '%$data[3]%'";

		}
	}
	if($data[4])
	{
	   if($db_type==2)
	   {
	   	 $str_cond.=" and to_char(a.insert_date,'YYYY')='$data[4]'";
	   }
	   else
	   {
	   		$str_cond.=" and year(a.insert_date)='$data[4]'";
	   }
	}
	 
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (0=>$comp,1=>$buyer_arr);
	 $sql= "SELECT a.id,b.po_number,a.job_no_prefix_num as job_no,a.style_ref_no,a.company_name,a.buyer_name from wo_po_details_master a,wo_po_break_down b where a.id=b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 $str_cond  group by a.id,b.po_number,a.job_no_prefix_num,a.style_ref_no,a.company_name,a.buyer_name";
	 //echo $sql;die;
	echo  create_list_view("list_view", "Company,Buyer Name,Job No,Style,Po No", "120,100,100,100,140","600","290",0, $sql , "js_set_value", "id,style_ref_no", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no,style_ref_no,po_number", "",'','0,0,0,0,0') ;
	exit();
} 

if($action=="report_generate") //Show button.
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$cbo_company_name			=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name				=str_replace("'","",$cbo_buyer_name);
	$cbo_brand_name				=str_replace("'","",$cbo_brand_name);
	$cbo_season_name			=str_replace("'","",$cbo_season_name);
	$cbo_location				=str_replace("'","",$cbo_location);
	$cbo_floor					=str_replace("'","",$cbo_floor);
	$cbo_job_year				=str_replace("'","",$cbo_job_year);
	$txt_job_no					=str_replace("'","",$txt_job_no);
	$txt_style_ref				=str_replace("'","",$txt_style_ref);  
	$hidden_job_id				=str_replace("'","",$hidden_job_id);  
	$from_date					=str_replace("'","",$txt_date_from);
	$to_date					=str_replace("'","",$txt_date_to);
	$search_by					=str_replace("'","",$cbo_search_by);

	// ========================= Filtering ======================
	$filter = '';
	if(!empty($hidden_job_id)){
		$filter .= " and a.id='$hidden_job_id' ";
	}else{
		if(!empty($txt_style_ref)){
			$filter .= " and a.style_ref_no='$txt_style_ref' ";
		}else{
			//echo "Please write style ref."; exit();
		}
	}
	if($cbo_location != 0) 		$filter .= " and a.location_name='$cbo_location' ";
	if($cbo_buyer_name 	!= 0) 	$filter .= " and a.buyer_name='$cbo_buyer_name' ";
	if($cbo_brand_name 	!= 0) 	$filter .= " and a.brand_id='$cbo_brand_name' ";
	if($cbo_season_name != 0) 	$filter .= " and a.season_buyer_wise='$cbo_season_name' ";
	if($cbo_job_year 	!= 0) 	$filter .= " and a.season_year='$cbo_job_year' ";

	$search_between1  = " ";
	$search_between2  = " ";
	$search_between3  = " ";
	if($from_date != '' && $to_date != ''){
		$search_between1 .= " AND A.EX_FACTORY_DATE BETWEEN '$from_date' AND '$to_date' ";
		$search_between2 .= " AND A.PRODUCTION_DATE BETWEEN '$from_date' AND '$to_date' ";
		$search_between3 .= " AND B.PR_DATE BETWEEN '$from_date' AND '$to_date' ";
		$date_prod	 	 .= " AND D.PRODUCTION_DATE BETWEEN '$from_date' AND '$to_date' ";
		$date_exfact	 .= " AND D.EX_FACTORY_DATE BETWEEN '$from_date' AND '$to_date' ";
	}	
	
	
	//echo $filter; exit();

	
	if ($search_by==1) 
	{ 
		$data_sql = "SELECT A.AVG_UNIT_PRICE, A.JOB_NO, A.JOB_QUANTITY, A.STYLE_REF_NO, A.BUYER_NAME, C.PO_BREAK_DOWN_ID, C.JOB_ID
	
			FROM 
				WO_PO_DETAILS_MASTER 	A,
				WO_PO_BREAK_DOWN 		B,
				WO_PO_COLOR_SIZE_BREAKDOWN C,
				PRO_GARMENTS_PRODUCTION_MST		D,
				PRO_GARMENTS_PRODUCTION_DTLS	E
			WHERE 
				A.ID=B.JOB_ID AND B.ID=C.PO_BREAK_DOWN_ID AND A.ID=C.JOB_ID AND D.PO_BREAK_DOWN_ID=B.ID  AND E.MST_ID=D.ID AND E.COLOR_SIZE_BREAK_DOWN_ID=C.ID
				AND A.COMPANY_NAME = '$cbo_company_name' $filter $date_prod AND D.PRODUCTION_TYPE=5 AND D.PRODUCTION_QUANTITY>0
				AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND D.STATUS_ACTIVE=1 AND D.IS_DELETED=0 AND E.STATUS_ACTIVE=1 AND E.IS_DELETED=0";
	}
	else if ($search_by==2) 
	{ 
		$data_sql = "SELECT A.AVG_UNIT_PRICE, A.JOB_NO, A.JOB_QUANTITY, A.STYLE_REF_NO, A.BUYER_NAME, C.PO_BREAK_DOWN_ID, C.JOB_ID
	
			FROM 
				WO_PO_DETAILS_MASTER 	A,
				WO_PO_BREAK_DOWN 		B,
				WO_PO_COLOR_SIZE_BREAKDOWN C,
				PRO_EX_FACTORY_MST 		D,
				PRO_EX_FACTORY_DTLS 	E
			WHERE 
				A.ID=B.JOB_ID AND B.ID=C.PO_BREAK_DOWN_ID AND A.ID=C.JOB_ID AND D.PO_BREAK_DOWN_ID=B.ID AND D.ID=E.MST_ID  
				AND A.COMPANY_NAME = '$cbo_company_name' $filter $date_exfact AND D.EX_FACTORY_QNTY>0
				AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND D.STATUS_ACTIVE=1 AND D.IS_DELETED=0 AND E.STATUS_ACTIVE=1 AND E.IS_DELETED=0";
	}
	else
	{
		echo "<h1 style='color:red; font-size: 17px;text-align:center;margin-top:20px;'> ** Please Select Search By ** </h1>" ;
        die();
	}			
	// echo $data_sql; exit();

	$dataArr 				= sql_select($data_sql);
	$jobArray 				= array();
	$poBreakDownIdArray 	= array();
	$jobIdArray 			= array();
	$orderWiseJobArray 		= array();
	$jobIdJobNOArray 		= array();
	foreach($dataArr as $data){
		$jobArray[$data['JOB_NO']]['STYLE_REF_NO'] 			= $data['STYLE_REF_NO'];
		$jobArray[$data['JOB_NO']]['BUYER_NAME'] 			= $data['BUYER_NAME'];
		$jobArray[$data['JOB_NO']]['JOB_QUANTITY'] 			= $data['JOB_QUANTITY'];
		$jobArray[$data['JOB_NO']]['PO_BREAK_DOWN_ID'] 		= $data['PO_BREAK_DOWN_ID'];
		$jobArray[$data['JOB_NO']]['FOB'] 					= $data['AVG_UNIT_PRICE'];


		$poBreakDownIdArray[$data['PO_BREAK_DOWN_ID']] 		= $data['PO_BREAK_DOWN_ID'];
		$jobIdArray[$data['JOB_ID']] 						= $data['JOB_ID'];
		$orderWiseJobArray[$data['PO_BREAK_DOWN_ID']] 		= $data['JOB_NO'];
		$jobIdJobNOArray[$data['JOB_ID']] 					= $data['JOB_NO'];
	}
	//print_r($jobIdArray); exit();
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in(1,2) and ENTRY_FORM=131");
	oci_commit($con);

	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 131, 1, $poBreakDownIdArray, $empty_arr);//Po ID
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 131, 2, $jobIdArray, $empty_arr);//Job ID
	disconnect($con);

	// ============================ ex-factory data ==================================
	$exFactSQL = sql_select("SELECT B.PRODUCTION_QNTY, A.PO_BREAK_DOWN_ID
				FROM 
					PRO_EX_FACTORY_MST 		A,
					PRO_EX_FACTORY_DTLS		B,
					GBL_TEMP_ENGINE			C
				WHERE 
					A.ID=B.MST_ID AND A.PO_BREAK_DOWN_ID=C.REF_VAL $search_between1
					and a.is_deleted=0 and a.STATUS_ACTIVE=1 and b.is_deleted=0 and b.STATUS_ACTIVE=1
					and C.ENTRY_FORM=131 AND C.REF_FROM=1 AND C.USER_ID='$user_id'");
	//echo $exFactSQL; exit();
	//$dateJobArr = array();
	$poExFactoryArray = array();
	foreach($exFactSQL as $exFact){
		$poExFactoryArray[$orderWiseJobArray[$exFact['PO_BREAK_DOWN_ID']]] += $exFact['PRODUCTION_QNTY'];
		//$dateJobArr[] = $orderWiseJobArray[$exFact['PO_BREAK_DOWN_ID']];
	}
	//print_r($dateJobArr); exit();

	// ============================= budget smv===========================
	$budgetSQL = sql_select("SELECT A.JOB_NO, A.SEW_SMV, A.JOB_ID
				FROM 
					WO_PRE_COST_MST 		A,
					GBL_TEMP_ENGINE			C
				WHERE 
					A.JOB_ID=C.REF_VAL AND A.ENTRY_FROM=425
					AND A.IS_DELETED=0 AND A.STATUS_ACTIVE=1 
					AND C.ENTRY_FORM=131 AND C.REF_FROM=2 AND C.USER_ID='$user_id'");
	//echo $budgetSQL; exit(); ppl_gsd_entry_mst
	$jobBookingSAM = array();
	foreach($budgetSQL as $budgetSMV){
		$jobBookingSAM[$jobIdJobNOArray[$budgetSMV['JOB_ID']]] 		= $budgetSMV['SEW_SMV'];
	}

	// ============================= Floor smv===========================
	$floorSMVSQL = sql_select("SELECT B.TOTAL_SMV, B.JOB_ID
				FROM 
					PPL_GSD_ENTRY_MST		B,
					GBL_TEMP_ENGINE			C
				WHERE 
					B.JOB_ID=C.REF_VAL
					AND B.IS_DELETED=0 AND B.STATUS_ACTIVE=1 
					AND C.ENTRY_FORM=131 AND C.REF_FROM=2 AND C.USER_ID='$user_id'");
	//echo $budgetSQL; exit(); ppl_gsd_entry_mst
	$jobFloorSAM = array();
	foreach($floorSMVSQL as $floorSMV){
		$jobFloorSAM[$jobIdJobNOArray[$floorSMV['JOB_ID']]] 			= $floorSMV['TOTAL_SMV'];
	}

	// ============================= Total achieved production ===========================

	// ========================= Production start time variable setting ======================
	$prodStartTimeArray = array();
	$startTimeSQL = sql_select(" SELECT COMPANY_NAME, SHIFT_ID, TO_CHAR(PROD_START_TIME,'HH24:MI') AS PROD_START_TIME,
					TO_CHAR(LUNCH_START_TIME,'HH24:MI') AS LUNCH_START_TIME 
				FROM 
					VARIABLE_SETTINGS_PRODUCTION 
				WHERE 
					COMPANY_NAME IN($cbo_company_name) AND  SHIFT_ID=1 AND VARIABLE_LIST=26 AND STATUS_ACTIVE=1 AND IS_DELETED=0");	
	foreach($startTimeSQL as $row){
		$prodStartTimeArray[$row[csf('SHIFT_ID')]]['PST']=$row[csf('PROD_START_TIME')];
		$prodStartTimeArray[$row[csf('SHIFT_ID')]]['LST']=$row[csf('LUNCH_START_TIME')];
	}
	$startHour=$prodStartTimeArray[1]['PST'];
	$lanchHour=$prodStartTimeArray[1]['LST'];
	$baseHour = date('H', strtotime($startHour));
	//echo $hour; exit();
	// ============================ Query building start ==================================	
	$achvdPrdSQL = "SELECT "; 
	$first=1;
	for($hour=$baseHour; $hour <= 24; $hour++)
	{
		if($first == 1){
			$firstHour = "00:00";
			$lastHour = str_pad($hour,2,'0',STR_PAD_LEFT).":00";
		}else{
			$lastHour = str_pad($hour,2,'0',STR_PAD_LEFT).":00";
			$preHour = $hour - 1;
			$firstHour = str_pad($preHour,2,'0',STR_PAD_LEFT).":00";
			//echo $preHour."<br>";
		}
		$prod_hour = "PROD_HOUR".str_pad($hour,2,'0',STR_PAD_LEFT);
		//echo $firstHour."-".$lastHour."<br>";
		$achvdPrdSQL .= "SUM(CASE WHEN TO_CHAR(A.PRODUCTION_HOUR,'HH24:MI')>'$firstHour' AND TO_CHAR(A.PRODUCTION_HOUR,'HH24:MI')<='$lastHour' AND A.PRODUCTION_TYPE = 5
			THEN B.PRODUCTION_QNTY ELSE 0 END) AS $prod_hour,";
		$first++; 
	}
	//exit();
	//echo $achvdPrdSQL; exit();

	// ============================ Query building end ==================================	
	$achvdPrdSQL .= " B.PRODUCTION_TYPE, SUM(B.PRODUCTION_QNTY) AS PRODUCTION_QNTY, A.PO_BREAK_DOWN_ID, A.PRODUCTION_DATE, A.ITEM_NUMBER_ID, A.SEWING_LINE
				FROM 
					PRO_GARMENTS_PRODUCTION_MST		A,
					PRO_GARMENTS_PRODUCTION_DTLS	B,
					GBL_TEMP_ENGINE					C
				WHERE 
					A.ID=B.MST_ID AND A.PO_BREAK_DOWN_ID = C.REF_VAL AND A.PRODUCTION_TYPE = 5 AND B.PRODUCTION_TYPE = 5  $search_between2
					AND A.IS_DELETED=0 AND A.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND B.STATUS_ACTIVE=1 
					AND C.ENTRY_FORM=131 AND C.REF_FROM=1 AND C.USER_ID='$user_id' 
					
				GROUP BY 
					B.PRODUCTION_TYPE, B.PRODUCTION_QNTY, A.PO_BREAK_DOWN_ID, A.PRODUCTION_DATE, A.ITEM_NUMBER_ID, A.SEWING_LINE";

	//echo $achvdPrdSQL; exit();
	$achvdPrdResult 		= sql_select($achvdPrdSQL);
	$achvdProdArray 		= array();
	$dateOrderItemArray 	= array();
	$jobProdHourArray 		= array();
	$sewingLineArray 		= array();
	foreach($achvdPrdResult as $achvdProd)
	{
		$sewingLineArray[$achvdProd['SEWING_LINE']] = $achvdProd['SEWING_LINE'];
		$achvdProdArray[$orderWiseJobArray[$achvdProd['PO_BREAK_DOWN_ID']]] 	+= $achvdProd['PRODUCTION_QNTY'];
		for($hour=$baseHour; $hour <= 24; $hour++){
			$prod_hour = "PROD_HOUR".str_pad($hour,2,'0',STR_PAD_LEFT);	
			$jobProdHourArray[$orderWiseJobArray[$achvdProd['PO_BREAK_DOWN_ID']]][$achvdProd['PRODUCTION_DATE']][$achvdProd['PO_BREAK_DOWN_ID']][$achvdProd['ITEM_NUMBER_ID']][$achvdProd['SEWING_LINE']][$prod_hour] += $achvdProd[$prod_hour];
			

		} 
	}
	//echo "<pre>";
	//print_r($jobProdHourArray); exit();
	// ============================ SewingLineSQL ==================================	
	$sewingLineComma = implode(',', $sewingLineArray);
	$lineManArray = array();
	$SewingLineSQL = sql_select("SELECT B.MAN_POWER, A.ID, B.PR_DATE
					FROM 
						PROD_RESOURCE_MST A, PROD_RESOURCE_DTLS B
					WHERE A.ID = B.MST_ID
						AND A.ID IN ($sewingLineComma)  $search_between3 AND A.IS_DELETED=0 AND B.IS_DELETED=0 ");

	//echo $SewingLineSQL; exit;
	foreach($SewingLineSQL as $lineVAl)
	{
		$lineManArray[$lineVAl['PR_DATE']][$lineVAl['ID']]  = $lineVAl['MAN_POWER'];
	}


	//print_r($lineManArray); exit();
	// ============================= Total Expence ===========================
	$expenseSQL = sql_select("SELECT A.JOB_NO, A.PO_ID, A.AMOUNT_USD
	FROM 
		WO_ACTUAL_COST_ENTRY 	A,
		GBL_TEMP_ENGINE			C
	WHERE 
		A.PO_ID=C.REF_VAL AND A.COST_HEAD=5
		AND A.IS_DELETED=0 AND A.STATUS_ACTIVE=1 
		AND C.ENTRY_FORM=131 AND C.REF_FROM=1 AND C.USER_ID='$user_id'");
	//echo $budgetSQL; exit(); ppl_gsd_entry_mst
	$jobCostArray = array();
	foreach($expenseSQL as $expenseData){
		$jobCostArray[$expenseData['JOB_NO']]		+= $expenseData['AMOUNT_USD'];
	}
		
	//echo $SewingLineSQL; exit();
	//echo "<pre>"; print_r($jobCostArray); exit();
	$workingHourArr = array();
	foreach ($jobProdHourArray as $j_key => $j_value) 
	{
		foreach ($j_value as $d_key => $d_value) 
		{
			foreach ($d_value as $ord_key => $ord_value) 
			{
				foreach ($ord_value as $itm_key => $itm_value) 
				{
					foreach ($itm_value as $l_key => $r) 
					{
						for($hour=$baseHour; $hour <= 24; $hour++)
						{
							$prod_hour = "PROD_HOUR".str_pad($hour,2,'0',STR_PAD_LEFT);	
							$prodQtyHourly = $r[$prod_hour];
							if(!empty($prodQtyHourly))
							{
								//$workingHourArr[$j_key][$l_key]++;
								$workingHourArr[$j_key][$d_key][$l_key]++;
							}
						}
					}
				}
			}
		}
	}

	//print_r($workingHourArr); exit();
	$factoryManPower = 0;
	$factoryWorkingHour = 0;
	$jobWorkHourArr = array();
	$monthManPower = array();
	$totalAvailableMinutes = array();
	foreach ($workingHourArr as $job_key => $job_value) 
	{
		foreach($job_value as $day_key => $day_value)
		{
			foreach ($day_value as $lkey => $workHour) 
			{
				$jobWorkHourArr[$job_key] +=  $workHour;
				$monthManPower[$job_key] += $lineManArray[$day_key][$lkey];
				$factoryManPower += $lineManArray[$day_key][$lkey];
				$factoryWorkingHour +=  $workHour;
				$totalAvailableMinutes[$job_key] += $lineManArray[$day_key][$lkey] * $workHour * 60; //$lineManArray[$day_key][$lkey]
				//echo $lkey.">>".$lineManArray[$day_key][$lkey]."<br>";
			}
		}
		
	}
	//exit();
	//print_r($totalAvailableMinutes); exit();
	// ============================= AllocatedCM ===========================
	//PRICE_DZN = total fob
	$allocatedCMSQL = sql_select("SELECT A.JOB_NO, A.JOB_ID, A.TOTAL_COST, A.CM_COST, B.COSTING_DATE, A.PRICE_DZN
	FROM 
		WO_PRE_COST_DTLS 		A,
		WO_PRE_COST_MST			B,
		GBL_TEMP_ENGINE			C
	WHERE 
		A.JOB_ID=C.REF_VAL AND C.REF_VAL = B.JOB_ID
		AND A.IS_DELETED=0 AND A.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND B.STATUS_ACTIVE=1 
		AND C.ENTRY_FORM=131 AND C.REF_FROM=2 AND C.USER_ID='$user_id'");
	//echo $budgetSQL; exit(); ppl_gsd_entry_mst
	$jobAllocatedCmArray 	= array();
	$jobCostBufferArray 	= array();
	$jobCostingDateArray 	= array();
	$totalFobArray 			= array();
	foreach($allocatedCMSQL as $allCM){
		$jobAllocatedCmArray[$allCM['JOB_NO']] 								= $allCM['CM_COST'];
		$jobCostBufferArray[$allCM['JOB_NO']] 								= $allCM['TOTAL_COST'];
		$jobCostingDateArray[$allCM['JOB_NO']][$allCM['COSTING_DATE']]		= $allCM['COSTING_DATE'];
		$totalFobArray[$allCM['JOB_NO']]									= $allCM['PRICE_DZN'];
	}

	// ============================= Asking profit for buffer calculation ===========================
	$askingProfitSql = sql_select("SELECT A.APPLYING_PERIOD_DATE, A.APPLYING_PERIOD_TO_DATE, A.ASKING_PROFIT
	FROM 
		LIB_STANDARD_CM_ENTRY	A
	WHERE 
		A.COMPANY_ID='$cbo_company_name'
		AND A.IS_DELETED=0 AND A.STATUS_ACTIVE=1");
	//echo $budgetSQL; exit(); ppl_gsd_entry_mst
	$askingProfitDateArray 	= array();
	foreach($askingProfitSql as $asking){
		$period = new DatePeriod(
			new DateTime($asking['APPLYING_PERIOD_DATE']),
			new DateInterval('P1D'),
			new DateTime($asking['APPLYING_PERIOD_TO_DATE'] . " +1 day")
	   );
	   
	   foreach ($period as $key => $value) {
			$askingProfitDateArray[csf($value->format('d-M-y'))] 		= $asking['ASKING_PROFIT'];
	   }
	}
	$jobAskingProfit = array();
	foreach($jobCostingDateArray as $jb_key => $costingDate){
		foreach($askingProfitDateArray as $askingDateKey => $currentJobAskingProfit){
			if($askingDateKey == $costingDate[$askingDateKey]){
				$jobAskingProfit[$jb_key] 		=  $currentJobAskingProfit/100;
			}
			
		}
		
	}

	//print_r($jobAskingProfit); exit();

	$gtAvlbleMinuts = 0;
	foreach($jobArray as $jobNo => $jobdata)
	{
		$gtAvlbleMinuts  += $totalAvailableMinutes[$jobNo]; 
	}	 


	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in(1,2) and ENTRY_FORM=131");
	oci_commit($con);
	disconnect($con);

	if ($search_by==1) 
	{
		$realizedCmTitle = "(Total expense/Total Acvhieved production)";
		$earnPerCmTitle = "(Total Achieved Production*allocated CM)";
		$avgCmBufferTitle ="";
		$earnCmBufferTitle="(Average of CM with Buffer*Total Achieved Production)";
		$sumOfTotalFobTitle="(Average of FOB Per Pcs*Total Achieved Production)";
		$sumOfBookingProdMinTitle="Budget SMV*Acvh Production";
	}
	else if($search_by==2)
	{
		$realizedCmTitle = "(Total expense/Total Shipment qty)";
		$earnPerCmTitle = "(Total Ship Qty*allocated CM)";
		$avgCmBufferTitle ="";
		$earnCmBufferTitle="(Average of CM with Buffer*Total Ship Qty)";
		$sumOfTotalFobTitle="(Average of FOB Per Pcs*Total Ship Qty)";
		$sumOfBookingProdMinTitle="Budget SMV*Total Ship Qty";
	}	
	ob_start();	
    ?>

	<style>
		.word_break{
			word-wrap: break-word;
			word-break: break-all;
		}
	</style>
		 
        <div id="file_wrapper">
        	<table width="2810" cellspacing="0" >
        		<tr class="form_caption" style="border:none;">
        			<td colspan="30" align="center" style="border:none;font-size:16px; font-weight:bold" ><strong>
                        Income VS Expense-Produced Qty
        			</strong></td>
        		</tr>
        		<tr style="border:none;">
        			<td colspan="30" align="center" style="border:none; font-size:14px;">
        				<strong>
        					Working Company Name : <? 
        					$comp_names=""; 
        					foreach(explode(",",$cbo_company_name) as $vals) 
        					{
        						$comp_names.=($comp_names)? ' , '.$company_library[$vals] : $company_library[$vals];
        					}
        					echo $comp_names;
        					 ?>
        				</strong>                                
        			</td>
        		</tr>
        		<tr style="border:none;">
        			<td colspan="30" align="center" style="border:none;font-size:12px; font-weight:bold" >
						<?=$from_date." To ".$to_date;?></td>
        		</tr>
        	</table>
            
			<div style="float:left; width:2850px">
				<table width="2830" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
					<thead>
						<tr>
                            <th class="word_break" width="30" ><p>SL</p></th>
                            <th class="word_break" width="100"><p>Job No.</p></th>
                            <th class="word_break" width="100"><p>Style</p></th>
                            <th class="word_break" width="100"><p>Buyer</p></th>
                            <th class="word_break" width="100"><p>Total Order Qty</p></th>
                            <th class="word_break" width="100" title=""><p>Total Ship Qty</p></th>
                            <th class="word_break" width="100" title="Budget SMV"><p>Booking SAM</p></th>
                            <th class="word_break" width="100" title="Operation Bulletin SMV"><p>Floor SAM</p></th>
                            <th class="word_break" width="100" title="style wise total sewing output production"><p>Total Achieved Production</p></th>
                            <th class="word_break" width="100" title="(Booking SAM*Total Achieved Production)"><p>Total Produced Minutes (As per Booked)</p></th>
                            <th class="word_break" width="100" title="Floor SAM*Total Achieved Production"><p>Total Produced Minutes (As per Floor)</p></th>
                            <th class="word_break" width="100" title="(MP*available WH till the end*60)"><p>Total Available Minutes</p></th>
                            <th class="word_break" width="100" title="(Total Produced Minutes (As per Booked)/Total Available Min)*100"><p>Average of Achive Efficiency (Booked)</p></th>
                            <th class="word_break" width="100" title="Total Produced Minutes (As per Floor)/Total Available Min)*100"><p>Average of Achive Efficiency (Floor)</p></th>
                            <th class="word_break" width="100" title=" (Total  cost/total month available min)*style available min"><p>Total Expence</p></th>
                            <th class="word_break" width="100" title=" <?= $realizedCmTitle  ?>"><p>Realized CM</p></th>
                            <th class="word_break" width="100" title="Budget CM"><p>Allocated CM </p></th>
                            <th class="word_break" width="100" title="<?= $earnPerCmTitle ?>"><p>Earn as per CM</p></th> 
                            <th class="word_break" width="100" title="(Earn as per CM - Total Expense)"><p>Profit/ Loss as per CM</p></th>
                            <th class="word_break" width="100" title="(Profit or Loss as per CM/Total Expense)*100"><p>Profit/ Loss as per CM (%)</p></th> 
                            <th class="word_break" width="100" title="Allocated CM + (Total FOB - Total Cost - Profit Margin)"><p>Average of CM with Buffer</p></th>
                            <th class="word_break" width="100" title="<?=$earnCmBufferTitle?>"><p>Earn as per CM with Buffer</p></th>
                            <th class="word_break" width="100" title="(Earn as per CM with Buffer - Total Expense)"><p>Pofit/ Loss as per CM with Buffer</p></th>
                            <th class="word_break" width="100" title="(Pofit or Loss as per CM with Buffer/Total Expense)*100"><p>Pofit/ Loss as per CM with Buffer %</p></th>
                            <th class="word_break" width="100" title="(Average of CM with Buffer - Allocated CM)"><p>Average of Buffer Per Pcs</p></th>
                            <th class="word_break" width="100" title="(Earn as per CM with Buffer - Earn as per CM)"><p>Sum of Total Buffer</p></th>
                            <th class="word_break" width="100" title="Avg. Unit Price"><p>Average of FOB Per Pcs</p></th>
                            <th class="word_break" width="100" title="<?= $sumOfTotalFobTitle ?>"><p>Sum of Total FOB</p></th>
                            <th class="word_break" width="100" title="<?= $sumOfBookingProdMinTitle ?>"><p>Sum of Booking Produce Minutes</p></th>
                        </tr>
					</thead>
				</table>
				<div style="max-height:425px; overflow-y:scroll; width: 2850px;" id="scroll_body">
					<table cellspacing="0" border="1" class="rpt_table"  width="2830" rules="all" id="table_body">
						<?php 
							$i = 1;
							$totOrderQty = 0;
							foreach($jobArray as $jobNo => $jobdata)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$bookingSamMinutes 	= $jobBookingSAM[$jobNo] * $achvdProdArray[$jobNo];
								$floorSamMinutes 	= $jobFloorSAM[$jobNo] * $achvdProdArray[$jobNo];
								$bookedEfficiency 	= ($bookingSamMinutes / $totalAvailableMinutes[$jobNo]) * 100;
								$floorEfficiency 	= ($floorSamMinutes / $totalAvailableMinutes[$jobNo]) * 100;
								$totalMonthAvailableMinutes = $monthManPower[$jobNo]*$jobWorkHourArr[$jobNo]*60;
								//$totalExp			= ($jobCostArray[$jobNo] / $totalMonthAvailableMinutes)*$totalAvailableMinutes[$jobNo];
								$totalMonthAvailableMinutes2 = $factoryManPower * $factoryWorkingHour * 60;
								$totalExp2 			= ($jobCostArray[$jobNo] / $gtAvlbleMinuts) * $totalAvailableMinutes[$jobNo];
								$jobCost  			= $jobCostArray[$jobNo];
								$jobAvailableMinutes= $totalAvailableMinutes[$jobNo];
								$totalExp_title 	= "($jobCost / $gtAvlbleMinuts) * $jobAvailableMinutes";
								$totalExp			= $totalExp2; 
								
								$profitLossAsPerCm 	= $earnAsPerCM - $totalExp;
								$profitLossAsPerCmPercent = ($profitLossAsPerCm/$totalExp)/100;
								$profitMargin		= $jobAllocatedCmArray[$jobNo] * $jobAskingProfit[$jobNo];
								$AvgCmBuffer 		= $jobAllocatedCmArray[$jobNo] + $totalFobArray[$jobNo] - $jobCostBufferArray[$jobNo] - $profitMargin; //$jobdata['FOB']
								$AvgCmBufferTitle 	= "$jobAllocatedCmArray[$jobNo] + ($totalFobArray[$jobNo] - $jobCostBufferArray[$jobNo] - $profitMargin)";

								if ($search_by==1) 
								{
									$achivedProdQty 	= $achivedProdQty;
									$releasedCM 		= $totalExp / $achivedProdQty; 
									$earnAsPerCM 		= $achivedProdQty *  $jobAllocatedCmArray[$jobNo]; 
									$earnCmBuffer 		= $achivedProdQty * $AvgCmBuffer;
									$sumTotalFOB        = $achivedProdQty * $jobdata['FOB'];
									$sumBookingProduceMinutes = $achivedProdQty * $jobBookingSAM[$jobNo]; 
								}
								else if($search_by==2)
								{ 
									$ExFactoryQty 		= $poExFactoryArray[$jobNo];
									$releasedCM 		= $totalExp / $ExFactoryQty;
									$earnAsPerCM 		= $ExFactoryQty *  $jobAllocatedCmArray[$jobNo];  
									$earnCmBuffer 		= $ExFactoryQty * $AvgCmBuffer;
									$sumTotalFOB        = $ExFactoryQty * $jobdata['FOB'];
									$sumBookingProduceMinutes = $ExFactoryQty * $jobBookingSAM[$jobNo]; 
								}
								
								
								$profitLossCmBuffer = $earnCmBuffer - $totalExp;
								$profitLossCmBufferPercent = ($earnCmBuffer / $totalExp)*100;
								$avgBufferPerPiece  = $AvgCmBuffer - $jobAllocatedCmArray[$jobNo];
								$sumTotalBuffer     = $earnCmBuffer - $earnAsPerCM;
								


								///Final total sum ...
								$totOrderQty 			+= $jobdata['JOB_QUANTITY']; 
								$totShipQty 			+= $poExFactoryArray[$jobNo]; 
								$totBookingSAM 			+= $jobBookingSAM[$jobNo]; 
								$totFloorSAM 			+= $jobFloorSAM[$jobNo]; 
								$totAchvdProd 			+= $achvdProdArray[$jobNo]; 
								$totBookingSamMinutes   += $bookingSamMinutes; 
								$totFloorSamMinutes     += $floorSamMinutes; 
								$totAvlbleMinuts        += $totalAvailableMinutes[$jobNo]; 
								$totBookedEfficiency    += $bookedEfficiency; 
								$totFloorEfficiency     += $floorEfficiency; 
								$totTotalExp     		+= $totalExp; 
								$totReleasedCM     		+= $releasedCM; 
								$totAllocatedCM    		+= $jobAllocatedCmArray[$jobNo]; 
								$totEarnAsPerCM    		+= $earnAsPerCM; 
								$totProfitLossAsPerCm   += $profitLossAsPerCm; 
								$totProfitLossPercent   += $profitLossAsPerCmPercent; 
								$totAvgCmBuffer   		+= $AvgCmBuffer; 
								$totEarnCmBuffer   		+= $earnCmBuffer; 
								$totPLCmBuffer   		+= $profitLossCmBuffer; 
								$totPLCmBufferPercent   += $profitLossCmBufferPercent; 
								$totAvgBufferPerPiece   += $avgBufferPerPiece; 
								$totSumTotalBuffer   	+= $sumTotalBuffer; 
								$totAvgFOB   			+= $jobdata['AVG_UNIT_PRICE']; 
								$totsumTotalFOB   		+= $sumTotalFOB; 
								$totsumBookingMinutes   += $sumBookingProduceMinutes; 
								?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>');" id="tr_2nd<? echo $i; ?>">
										<td class="word_break" width="30" align="left"><p><?=$i;?></p></td>
										<td class="word_break" width="100" align="left"><p><?=$jobNo;?></p></td>
										<td class="word_break" width="100" align="left"><p><?=$jobdata['STYLE_REF_NO'];?></p></td>
										<td class="word_break" width="100" align="left"><p><?=$buyer_library[$jobdata['BUYER_NAME']];?></p></td>
										<td class="word_break" width="100" align="right"><p><?=$jobdata['JOB_QUANTITY'];?></p></td>
										<td class="word_break" width="100" align="right"><p><?=$poExFactoryArray[$jobNo];?></p></td>
										<td class="word_break" width="100" align="right"><p><?=$jobBookingSAM[$jobNo];?></p></td>
										<td class="word_break" width="100" align="right"><p><?=$jobFloorSAM[$jobNo];?></p></td>
										<td class="word_break" width="100" align="right"><p><?=$achvdProdArray[$jobNo];?></p></td>
										<td class="word_break" width="100" align="right"><p><?=$bookingSamMinutes;?></p></td>
										<td class="word_break" width="100" align="right"><p><?=$floorSamMinutes;?></p></td>
										<td class="word_break" width="100" align="right"><p><?=$totalAvailableMinutes[$jobNo];?></p></td>
										<td class="word_break" width="100" align="right"><p><?=round($bookedEfficiency,2);?></p></td>
										<td class="word_break" width="100" align="right"><p><?=round($floorEfficiency, 2);?></p></td>
										<td class="word_break" width="100" align="right" title="<?= $totalExp_title ?>"><p><?=round($totalExp,2);?></p></td>
										<td class="word_break" width="100" align="right"><p><?=round($releasedCM, 2);?></p></td>
										<td class="word_break" width="100" align="right"><p><?=$jobAllocatedCmArray[$jobNo];?></p></td>
										<td class="word_break" width="100" align="right"><p><?=$earnAsPerCM;?></p></td> 
										<td class="word_break" width="100" align="right"><p><?=round($profitLossAsPerCm,2);?></p></td>
										<td class="word_break" width="100" align="right"><p><?=round($profitLossAsPerCmPercent,2);?></p></td> 
										<td class="word_break" width="100" align="right" title="<?= $AvgCmBufferTitle ?>"><p><?=round($AvgCmBuffer, 2);?></p></td>
										<td class="word_break" width="100" align="right"><p><?=round($earnCmBuffer, 2);?></p></td>
										<td class="word_break" width="100" align="right"><p><?=round($profitLossCmBuffer,2);?></p></td>
										<td class="word_break" width="100" align="right"><p><?=round($profitLossCmBufferPercent,2);?></p></td>
										<td class="word_break" width="100" align="right"><p><?=round($avgBufferPerPiece,2);?></p></td>
										<td class="word_break" width="100" align="right"><p><?=round($sumTotalBuffer,2);?></p></td>
										<td class="word_break" width="100" align="right"><p><?=$jobdata['FOB'];?></p></td>
										<td class="word_break" width="100" align="right"><p><?=$sumTotalFOB;?></p></td>
										<td class="word_break" width="100" align="right"><p><?=$sumBookingProduceMinutes;?></p></td>
									</tr>
								<?php 
								$i++;
							}
						?>
						
					</table>
				
					<table border="1" class="tbl_bottom" width="2830" rules="all" id="report_table_footer_1" >
						<!-- Footer -->
                        <tr>
                            <td class="word_break" width="30" ><p></p></td>
                            <td class="word_break" width="100"><p></p></td>
                            <td class="word_break" width="100"><p></p></td>
                            <td class="word_break" width="100"><p>Total</p></td>
                            <td class="word_break" width="100" id="value_totOrderQty"><p><?=$totOrderQty;?></p></td>
                            <td class="word_break" width="100" id="value_totShipQty"><p><?=$totShipQty;?></p></td>
                            <td class="word_break" width="100" id="value_totBookingSAM"><p><?=$totBookingSAM;?></p></td>
                            <td class="word_break" width="100" id="value_totFloorSAM"><p><?=$totFloorSAM;?></p></td>
                            <td class="word_break" width="100" id="value_totAchvdProd"><p><?=$totAchvdProd;?></p></td>
                            <td class="word_break" width="100" id="value_totBookingSamMinutes"><p><?=$totBookingSamMinutes;?></p></td>
                            <td class="word_break" width="100" id="value_totFloorSamMinutes"><p><?=$totFloorSamMinutes;?></p></td>
                            <td class="word_break" width="100" id="value_totAvlbleMinuts"><p><?=$totAvlbleMinuts;?></p></td>
                            <td class="word_break" width="100" id="value_totBookedEfficiency"><p><?=$totBookedEfficiency;?></p></td>
                            <td class="word_break" width="100" id="value_totFloorEfficiency"><p><?=$totFloorEfficiency;?></p></td>
                            <td class="word_break" width="100" id="value_totTotalExp"><p><?=round($totTotalExp,2);?></p></td>
                            <td class="word_break" width="100" id="value_totReleasedCM"><p><?=round($totReleasedCM,2);?></p></td>
                            <td class="word_break" width="100" id="value_totAllocatedCM"><p><?=$totAllocatedCM;?></p></td>
                            <td class="word_break" width="100" id="value_totEarnAsPerCM"><p><?=$totEarnAsPerCM;?></p></td>
                            <td class="word_break" width="100" id="value_totProfitLossAsPerCm"><p><?=round($totProfitLossAsPerCm,2);?></p></td>
                            <td class="word_break" width="100" id="value_totProfitLossPercent"><p><?=round($totProfitLossPercent,2);?></p></td>
                            <td class="word_break" width="100" id="value_totAvgCmBuffer"><p><?=$totAvgCmBuffer;?></p></td>
                            <td class="word_break" width="100" id="value_totEarnCmBuffer"><p><?=$totEarnCmBuffer;?></p></td>
                            <td class="word_break" width="100" id="value_totPLCmBuffer"><p><?=round($totPLCmBuffer,2);?></p></td>
                            <td class="word_break" width="100" id="value_totPLCmBufferPercent"><p><?=round($totPLCmBufferPercent,2);?></p></td>
                            <td class="word_break" width="100" id="value_totAvgBufferPerPiece"><p><?=$totAvgBufferPerPiece;?></p></td>
                            <td class="word_break" width="100" id="value_totSumTotalBuffer"><p><?=$totSumTotalBuffer;?></p></td>
                            <td class="word_break" width="100" id="value_totAvgFOB"><p><?=$totAvgFOB;?></p></td>
                            <td class="word_break" width="100" id="value_totsumTotalFOB"><p><?=$totsumTotalFOB;?></p></td>
                            <td class="word_break" width="100" id="value_totsumBookingMinutes"><p><?=$totsumBookingMinutes;?></p></td>
                            
                        </tr>
					</table>
					
					  
				</div>
			</div>
			 
		 </div> 
        <?

	foreach (glob("$user_id*.xls") as $filename) 
    {
        if( @filemtime($filename) < (time()-$seconds_old) )
        @unlink($filename);
    }
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename,'w');
    $is_created = fwrite($create_new_doc,ob_get_contents());
    $html = ob_get_contents();
    ob_clean();
    echo "$html**$filename**$type";
    exit();  
	 
}


if($action=="remarks_popup__")
{
	extract($_REQUEST);
	list($po,$item,$country,$color)=explode('**', $data);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name");
	$sewing_line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name");
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');


	 $production_sql="SELECT a.production_date,c.id,c.color_number_id,c.size_number_id,a.sewing_line,a.floor_id,a.production_type,a.remarks,a.prod_reso_allo,sum(b.production_qnty) as qntys from pro_garments_production_mst a,pro_garments_production_dtls b ,wo_po_color_size_breakdown c , wo_po_break_down d where a.id=b.mst_id and b.color_size_break_down_id=c.id and c.po_break_down_id=d.id and c.job_no_mst=d.job_no_mst and a.po_break_down_id=d.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and a.po_break_down_id='$po' and a.item_number_id='$item' and a.country_id='$country' and c.color_number_id='$color' group by a.production_date,c.id,c.color_number_id,c.size_number_id,a.sewing_line,a.floor_id,a.production_type,a.remarks,a.prod_reso_allo order by c.id";
	 //echo $production_sql;
	 $type_line_wise_arr=array();
	 $size_all_arr=array();
	foreach(sql_select($production_sql) as $keys=>$vals)
	{
	 	$type_line_wise_arr[$vals[csf("production_type")]][$vals[csf("production_date")]][$vals[csf("floor_id")]][$vals[csf("sewing_line")]][$vals[csf("color_number_id")]]["qntys"]+=$vals[csf("qntys")];

	 	$type_line_wise_arr[$vals[csf("production_type")]][$vals[csf("production_date")]][$vals[csf("floor_id")]][$vals[csf("sewing_line")]][$vals[csf("color_number_id")]]["prod_reso_allo"]=$vals[csf("prod_reso_allo")];

	 	$type_line_wise_arr[$vals[csf("production_type")]][$vals[csf("production_date")]][$vals[csf("floor_id")]][$vals[csf("sewing_line")]][$vals[csf("color_number_id")]]["remarks"]=$vals[csf("remarks")];

	 	$type_line_wise_arr_sizewise[$vals[csf("production_type")]][$vals[csf("production_date")]][$vals[csf("floor_id")]][$vals[csf("sewing_line")]][$vals[csf("color_number_id")]][$vals[csf("size_number_id")]]["size_qty"]+=$vals[csf("qntys")];

	 	$size_all_arr[$vals[csf("size_number_id")]]=$vals[csf("size_number_id")];
	}
	 $cut_lay_sql="SELECT a.entry_date as production_date,b.color_id as color_number_id ,c.size_id as size_number_id ,sum(c.size_qty) as qntys,0 as floor_id,0 as  sewing_line  from ppl_cut_lay_mst a ,ppl_cut_lay_dtls b ,ppl_cut_lay_bundle c where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and  c.is_deleted=0 and c.order_id='$po' and b.color_id='$color' and b.gmt_item_id ='$item' and c.country_id='$country'  group by  a.entry_date,b.color_id,c.size_id order by c.size_id";
	 //echo $cut_lay_sql;
	foreach(sql_select($cut_lay_sql) as $keys=>$vals)
	{
	 	$type_line_wise_arr[0][$vals[csf("production_date")]][$vals[csf("floor_id")]][$vals[csf("sewing_line")]][$vals[csf("color_number_id")]]["qntys"]+=$vals[csf("qntys")];

	 	$type_line_wise_arr_sizewise[0][$vals[csf("production_date")]][$vals[csf("floor_id")]][$vals[csf("sewing_line")]][$vals[csf("color_number_id")]][$vals[csf("size_number_id")]]["size_qty"]+=$vals[csf("qntys")];

	 	$size_all_arr[$vals[csf("size_number_id")]]=$vals[csf("size_number_id")];
	}
	 
	 $size_all_ids=implode(',', $size_all_arr);
	 //$type_name=[1=>"Cutting",4=>"Sewing Input",5=>"Sewing Output",8=>"Finishing & Packing",11=>"Poly",0=>"Cut and Lay"];
	 

	?>
     

    </head>
    <body>
	    <div id="data_panel" style="width:100%;text-align: center;padding: 5px;">
			<script>
	            function new_window()
	            {
	            	$('.fltrow').hide();
	                var w = window.open("Surprise", "#");
	                var d = w.document.open();
	                d.write(document.getElementById('details_reports').innerHTML);
	                d.close();
	                $('.fltrow').show();
	            }
	        </script>
	    	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onClick="new_window()" />
	    	<span id="popup_report_container" align="center" style="width: 120px;"> </span>
	    </div>
	    <?
	    ob_start();
		?>
        <div id="details_reports" align="center" style="width:100%;" >
            
            <?
            ksort($type_line_wise_arr);
            /*echo "<pre>";
            print_r($type_line_wise_arr);die;*/
            $total_type=0;
            $production_type[0]="Cut and Lay";
            foreach($type_line_wise_arr as $type_id=>$date_data)
            {

             	$total_type++;
             	$p=0;
             	?>
             	<table width="620" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all" style="padding-top: 15px;">
             		<caption> <strong><? echo $production_type[$type_id];?></strong></caption>
             		<thead>

             			<tr>                	 
             				<th width="30" >SL</th>
             				<th width="90">Date</th>
             				<th width="90">Color</th>
             				<?
             				foreach($size_all_arr as $key=>$val)
             				{
             					?>
             					<th width="45"><? echo $sizearr[ $key] ;?></th>

             					<?
             				}

             				?>

             				<th width="80">Prod. Qty.</th>
             				<th width="80">Remarks</th>
             				<? if($type_id!=1 && $type_id !=0)
             				{
             					?>
             					<th width="100">Floor</th>
             					<th width="80">Line</th>

             					<?

             				}
             				?>
             			</tr>           
             		</thead>
             	</table>
             	<div style="max-height:300px; overflow:auto;">
             	<table id="table_body<? echo $total_type;?>" width="620" border="1" rules="all" class="rpt_table" align="center">
             	<tbody>

             	<?
             	$size_wise_qty = array();
             	$total_prod_qty = 0;
             	foreach($date_data as $date_id=>$floor_data)
             	{
             		foreach($floor_data as $floor_id=>$line_data)
             		{
             			foreach($line_data as $line_id=>$color_data)
             			{
             				foreach($color_data as $color_id=>$rows)
             				{
             					$p++;
             					?>


             					<tr>                	 
             						<td align="center" width="30" ><? echo $p;?></td>
             						<td align="center"  width="90"><? echo change_date_format($date_id);?></td>
             						<td align="center"  width="90"><? echo $colorarr[$color_id] ;?></td>
             						<?
             						foreach($size_all_arr as $key=>$val)
             						{
             							?>
             							<td align="right"  width="45"><? echo $col_size_qty = $type_line_wise_arr_sizewise[$type_id][$date_id][$floor_id][$line_id][$color_id][$key]["size_qty"] ;?></td>

             							<?
             							$total_prod_qty += $col_size_qty;
             							$size_wise_qty[$key] += $type_line_wise_arr_sizewise[$type_id][$date_id][$floor_id][$line_id][$color_id][$key]["size_qty"];
             						}

             						?>

             						<td align="right"  width="80"><? echo $rows["qntys"];?></td>
             						<td align="center"  width="80"><? echo $rows["remarks"];?></td>
             						<? if($type_id!=1 && $type_id !=0)
             						{
             							?>
             							<td align="center"  width="100"><? echo $floor_library[$floor_id]; ?></td>
             							<td align="center"  width="80">
             								<?
             								$sewing_line='';

             								if($rows['prod_reso_allo']==1)
             								{
             									$line_number=explode(",",$prod_reso_arr[$line_id]);
             									foreach($line_number as $line_val)
             									{
             										if($sewing_line=='') $sewing_line=$sewing_line_library[$line_val]; else $sewing_line.=",".$sewing_line_library[$line_val];
             									}
             								}
             								else 
             								{
             									$sewing_line=$sewing_line_library[$line_id];
             								}
             								echo $sewing_line;

             								?>
             							 	
             							 </td>

             							<?

             						}
             						?>
             					</tr>           



             					<?

             				}
             				

             			}

             		}


             	}
             	?>
             	</tbody>
             	<!-- ================================ For Total ============================== -->
             	<tfoot>
             		<tr class="tbl_bottom">                	 
         				<th colspan="3" width="30" align="right" >Total </th>
         				<?
         				foreach($size_all_arr as $key=>$val)
         				{
         					?>
         					<th align="right" width="45"><? echo $size_wise_qty[$key];?></th>

         					<?
         				}

         				?>

         				<th width="80" align="right"><? echo number_format($total_prod_qty,0); ?></th>
         				<th width="80"></th>
         				<? if($type_id!=1 && $type_id !=0)
         				{
         					?>
         					<th width="100"></th>
         					<th width="80"></th>

         					<?

         				}
         				?>
         			</tr> 
             	</tfoot>
             		              		 
             	</table>
             	</div>
             		
             	<?
             
         	}

            ?>

                 <script> 
                 var total_type='<? echo $total_type;?>';
                 for(i=1;i<=total_type;i++)
                 {
                 	setFilterGrid("table_body"+i,-1);
                 }
                 
                  </script>
          
        </div>
      	<?
		$html=ob_get_contents();
		ob_flush();
		
		foreach (glob(""."*.xls") as $filename) 
		{
		   @unlink($filename);
		}
		
		//html to xls convert
		$name=time();
		$name=$user_id."_".$name.".xls";
		$create_new_excel = fopen(''.$name, 'w');	
		$is_created = fwrite($create_new_excel,$html);	
		?>
	    <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
	    <script>
			$(document).ready(function(e) 
			{
				document.getElementById('popup_report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Convert to Excel" name="excel" id="excel" style="padding:0 2px;" class="formbutton"/></a>&nbsp;&nbsp;';
			});	
		</script>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    
    <?
	
	exit();
}

if($action=="remarks_popup")
{
	extract($_REQUEST);
	list($po,$item,$country,$color)=explode('**', $data);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name");
	$sewing_line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name");
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');


	 $production_sql="SELECT a.production_date,c.id,c.color_number_id,c.size_number_id,a.sewing_line,a.floor_id,a.production_type,a.remarks,a.prod_reso_allo,sum(b.production_qnty) as qntys from pro_garments_production_mst a,pro_garments_production_dtls b ,wo_po_color_size_breakdown c , wo_po_break_down d where a.id=b.mst_id and b.color_size_break_down_id=c.id and c.po_break_down_id=d.id and c.job_no_mst=d.job_no_mst and a.po_break_down_id=d.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and a.po_break_down_id='$po' and a.item_number_id='$item' and a.country_id='$country' and c.color_number_id='$color' group by a.production_date,c.id,c.color_number_id,c.size_number_id,a.sewing_line,a.floor_id,a.production_type,a.remarks,a.prod_reso_allo order by a.production_date";
	 //echo $production_sql;
	 $type_line_wise_arr=array();
	 $size_all_arr=array();
	foreach(sql_select($production_sql) as $keys=>$vals)
	{
	 	$type_line_wise_arr[$vals[csf("production_type")]][$vals[csf("production_date")]][$vals[csf("floor_id")]][$vals[csf("sewing_line")]][$vals[csf("color_number_id")]]["qntys"]+=$vals[csf("qntys")];

	 	$type_line_wise_arr[$vals[csf("production_type")]][$vals[csf("production_date")]][$vals[csf("floor_id")]][$vals[csf("sewing_line")]][$vals[csf("color_number_id")]]["prod_reso_allo"]=$vals[csf("prod_reso_allo")];

	 	$type_line_wise_arr[$vals[csf("production_type")]][$vals[csf("production_date")]][$vals[csf("floor_id")]][$vals[csf("sewing_line")]][$vals[csf("color_number_id")]]["remarks"]=$vals[csf("remarks")];

	 	$type_line_wise_arr_sizewise[$vals[csf("production_type")]][$vals[csf("production_date")]][$vals[csf("floor_id")]][$vals[csf("sewing_line")]][$vals[csf("color_number_id")]][$vals[csf("size_number_id")]]["size_qty"]+=$vals[csf("qntys")];

	 	$size_all_arr[$vals[csf("size_number_id")]]=$vals[csf("size_number_id")];
	}
	 $cut_lay_sql="SELECT a.entry_date as production_date,b.color_id as color_number_id ,c.size_id as size_number_id ,sum(c.size_qty) as qntys,0 as floor_id,0 as  sewing_line  from ppl_cut_lay_mst a ,ppl_cut_lay_dtls b ,ppl_cut_lay_bundle c where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and  c.is_deleted=0 and c.order_id='$po' and b.color_id='$color' and b.gmt_item_id ='$item' and c.country_id='$country'  group by  a.entry_date,b.color_id,c.size_id order by c.size_id";
	 //echo $cut_lay_sql;
	foreach(sql_select($cut_lay_sql) as $keys=>$vals)
	{
	 	$type_line_wise_arr[0][$vals[csf("production_date")]][$vals[csf("floor_id")]][$vals[csf("sewing_line")]][$vals[csf("color_number_id")]]["qntys"]+=$vals[csf("qntys")];

	 	$type_line_wise_arr_sizewise[0][$vals[csf("production_date")]][$vals[csf("floor_id")]][$vals[csf("sewing_line")]][$vals[csf("color_number_id")]][$vals[csf("size_number_id")]]["size_qty"]+=$vals[csf("qntys")];

	 	$size_all_arr[$vals[csf("size_number_id")]]=$vals[csf("size_number_id")];
	}
	 
	 $size_all_ids=implode(',', $size_all_arr);


	 //$type_name=[1=>"Cutting",4=>"Sewing Input",5=>"Sewing Output",8=>"Finishing & Packing",11=>"Poly",0=>"Cut and Lay"];
	 

	?>
     

    </head>
    <body>
	    <div id="data_panel" style="width:100%;text-align: center;padding: 5px;">
			<script>
	            function new_window()
	            {
	            	$('.fltrow').hide();
	                var w = window.open("Surprise", "#");
	                var d = w.document.open();
	                d.write(document.getElementById('details_reports').innerHTML);
	                d.close();
	                $('.fltrow').show();
	            }
	        </script>
	    	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onClick="new_window()" />
	    	<span id="popup_report_container" align="center" style="width: 120px;"> </span>
	    </div>
	    <?
	    ob_start();
		?>
        <div id="details_reports" align="center" style="width:100%;" >
            
            <?
            ksort($type_line_wise_arr);
            /*echo "<pre>";
            print_r($type_line_wise_arr);die;*/
            $total_type=0;
            $production_type[0]="Cut and Lay";
			
            foreach($type_line_wise_arr as $type_id=>$date_data)
            {
				$tble_width = 0;
				if($type_id!=1 && $type_id !=0)
				{
					$tble_width = 550+(count($size_all_arr)*45);
				}
				else
				{
					$tble_width = 370+(count($size_all_arr)*45);
				}

             	$total_type++;
             	$p=0;
             	?>
                <div style="width:<? echo $tble_width+20;?>px;float: left;">
             	<table width="<? echo $tble_width;?>" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="left" rules="all" style="padding-top: 15px;">
             		<caption> <strong><? echo $production_type[$type_id];?></strong></caption>
             		<thead>

             			<tr>                	 
             				<th width="30" >SL</th>
             				<th width="90">Date</th>
             				<th width="90">Color</th>
             				<?
             				foreach($size_all_arr as $key=>$val)
             				{
             					?>
             					<th width="45"><? echo $sizearr[ $key] ;?></th>

             					<?
             				}

             				?>

             				<th width="80">Prod. Qty.</th>
             				<th width="80">Remarks</th>
             				<? if($type_id!=1 && $type_id !=0)
             				{
             					?>
             					<th width="100">Floor</th>
             					<th width="80">Line</th>

             					<?

             				}
             				?>
             			</tr>           
             		</thead>
             	</table>
             	<div style="max-height:<? echo $tble_width*50;?>px; width:<? echo $tble_width+20;?>px;float: left;">
             	<table id="table_body<? echo $total_type;?>" width="<? echo $tble_width;?>" border="1" rules="all" class="rpt_table" align="left">
             	<tbody>

             	<?
             	$size_wise_qty = array();
             	$total_prod_qty = 0;
             	foreach($date_data as $date_id=>$floor_data)
             	{
             		foreach($floor_data as $floor_id=>$line_data)
             		{
             			foreach($line_data as $line_id=>$color_data)
             			{
             				foreach($color_data as $color_id=>$rows)
             				{
             					$p++;
             					?>


             					<tr>                	 
             						<td align="center" width="30" ><? echo $p;?></td>
             						<td align="center"  width="90"><? echo change_date_format($date_id);?></td>
             						<td align="center"  width="90"><? echo $colorarr[$color_id] ;?></td>
             						<?
             						foreach($size_all_arr as $key=>$val)
             						{
             							?>
             							<td align="right"  width="45"><? echo $col_size_qty = $type_line_wise_arr_sizewise[$type_id][$date_id][$floor_id][$line_id][$color_id][$key]["size_qty"] ;?></td>

             							<?
             							$total_prod_qty += $col_size_qty;
             							$size_wise_qty[$key] += $type_line_wise_arr_sizewise[$type_id][$date_id][$floor_id][$line_id][$color_id][$key]["size_qty"];
             						}

             						?>

             						<td align="right"  width="80"><? echo $rows["qntys"];?></td>
             						<td align="center"  width="80"><? echo $rows["remarks"];?></td>
             						<? if($type_id!=1 && $type_id !=0)
             						{
             							?>
             							<td align="center"  width="100"><? echo $floor_library[$floor_id]; ?></td>
             							<td align="center"  width="80">
             								<?
             								$sewing_line='';

             								if($rows['prod_reso_allo']==1)
             								{
             									$line_number=explode(",",$prod_reso_arr[$line_id]);
             									foreach($line_number as $line_val)
             									{
             										if($sewing_line=='') $sewing_line=$sewing_line_library[$line_val]; else $sewing_line.=",".$sewing_line_library[$line_val];
             									}
             								}
             								else 
             								{
             									$sewing_line=$sewing_line_library[$line_id];
             								}
             								echo $sewing_line;

             								?>
             							 	
             							 </td>

             							<?

             						}
             						?>
             					</tr>           



             					<?

             				}
             				

             			}

             		}


             	}
             	?>
             	</tbody>
             	<!-- ================================ For Total ============================== -->
             	<tfoot>
             		<tr class="tbl_bottom">                	 
         				<th colspan="3" width="30" align="right" >Total </th>
         				<?
         				foreach($size_all_arr as $key=>$val)
         				{
         					?>
         					<th align="right" width="45"><? echo $size_wise_qty[$key];?></th>

         					<?
         				}

         				?>

         				<th width="80" align="right"><? echo number_format($total_prod_qty,0); ?></th>
         				<th width="80"></th>
         				<? if($type_id!=1 && $type_id !=0)
         				{
         					?>
         					<th width="100"></th>
         					<th width="80"></th>

         					<?

         				}
         				?>
         			</tr> 
             	</tfoot>
             		              		 
             	</table>
             	</div>
             		
             	<?
             
         	}

         	$ex_factory_sql="SELECT a.EX_FACTORY_DATE as PRODUCTION_DATE, a.REMARKS, b.PRODUCTION_QNTY as QNTYS,c.color_number_id as COLOR_ID,c.size_number_id as SIZE_ID from PRO_EX_FACTORY_MST a ,PRO_EX_FACTORY_DTLS b, WO_PO_COLOR_SIZE_BREAKDOWN c where a.id=b.mst_id and b.COLOR_SIZE_BREAK_DOWN_ID=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and  c.is_deleted=0 and c.po_break_down_id=$po and c.item_number_id=$item and c.color_number_id=$color and c.country_id=$country  order by c.SIZE_ORDER";
	 			//echo $ex_factory_sql; die;
	 			$ex_factory_array = array();
	 			$size_qty_array = array();
				foreach(sql_select($ex_factory_sql) as $keys=>$vals)
				{
				 	$ex_factory_array[$vals["PRODUCTION_DATE"]][$vals["COLOR_ID"]]['qty']+=$vals["QNTYS"];
				 	$ex_factory_array[$vals["PRODUCTION_DATE"]][$vals["COLOR_ID"]]['remarks'] = $vals["REMARKS"];
				 	$size_qty_array[$vals["SIZE_ID"]]+=$vals["QNTYS"];
				 	$size_all_arr[$vals["SIZE_ID"]]=$vals["SIZE_ID"];
				}
				 
				 $size_all_ids=implode(',', $size_all_arr);
				 //echo "<pre>";
				 //print_r($ex_factory_array);
				 //echo "</pre>";
				 $total_type=0;
				 if(count(sql_select($ex_factory_sql)))
				 {
				 	$tble_width = 0;
					if($type_id!=1 && $type_id !=0)
					{
						$tble_width = 550+(count($size_all_arr)*45);
					}
					else
					{
						$tble_width = 370+(count($size_all_arr)*45);
					}

					$total_type++;
             		$i=0;
				 	?>
				 	<table width="<? echo $tble_width;?>" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="left" rules="all" style="padding-top: 15px;">
             		<caption> <strong>Ex-Factory</strong></caption>
             		<thead>

             			<tr>                	 
             				<th width="30" >SL</th>
             				<th width="90">Date</th>
             				<th width="90">Color</th>
             				<?
             				foreach($size_all_arr as $key=>$val)
             				{
             					?>
             					<th width="45"><? echo $sizearr[ $key] ;?></th>

             					<?
             				}

             				?>

             				<th width="80">Prod. Qty.</th>
             				<th width="80">Remarks</th>
             				
             			</tr>           
             		</thead>
             		<tbody>
             			<?php
             			$size_total_qty_arr = array();
             			foreach($ex_factory_array as $date=>$date_data)
             			{
             				foreach($date_data as $color_id=>$row)
             				{
             					// foreach($color_data as $size_id=>$vals)
             					// {
		             				$i++;
		             				?>
		             				<tr>
		             					<td><?php echo $i; ?></td>
		             					<td align="center"><?php echo $date; ?></td>
		             					<td align="center"><?php echo $colorarr[$color_id]; ?></td>
		             					<?
		             						$total_size_qty =0;
		             						foreach($size_all_arr as $key=>$val)
		             						{
		             							?>
		             							<td align="right"  width="45"><? echo $size_qty_array[$key];?></td>

		             							<?
		             							$total_size_qty += $size_qty_array[$key];
		             							$size_total_qty_arr[$key] += $size_qty_array[$key];
		             							//$size_wise_qty[$key] += $type_line_wise_arr_sizewise[$type_id][$date_id][$floor_id][$line_id][$color_id][$key]["size_qty"];
		             						}

		             						?>
		             					<td align="right"><?php echo $total_size_qty; ?></td>
		             					<td align="center"><?php echo $row["remarks"]; ?></td>
		             				</tr>
		             				<?php
		             			// }
		             		}
		             	}
             			?>
             		</tbody>
             		<tfoot>
             			<tr>
             				<th></th>
             				<th></th>
             				<th>Total</th>
             				<?
             				$gr_total = 0;
             				foreach($size_all_arr as $key=>$val)
             				{
             					?>
             					<th width="45">

             						<? echo $size_total_qty_arr[ $key] ; $gr_total+=$size_total_qty_arr[ $key];?>
             							
             						</th>

             					<?
             				}

             				?>
             				<th><? echo $gr_total;?></th>
             				<th></th>
             			</tr>
             		</tfoot>
             	</table>
				 	<?
				 }

            ?>

                 <script> 
                 var total_type='<? echo $total_type;?>';
                 for(i=1;i<=total_type;i++)
                 {
                 	setFilterGrid("table_body"+i,-1);
                 }
                 
                  </script>
          </div>
        </div>
      	<?
		$html=ob_get_contents();
		ob_flush();
		
		foreach (glob(""."*.xls") as $filename) 
		{
		   @unlink($filename);
		}
		
		//html to xls convert
		$name=time();
		$name=$user_id."_".$name.".xls";
		$create_new_excel = fopen(''.$name, 'w');	
		$is_created = fwrite($create_new_excel,$html);	
		?>
	    <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
	    <script>
			$(document).ready(function(e) 
			{
				document.getElementById('popup_report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Convert to Excel" name="excel" id="excel" style="padding:0 2px;" class="formbutton"/></a>&nbsp;&nbsp;';
			});	
		</script>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    
    <?
	
	exit();
}
 
 if($action=="cutting_sewing_action")
{
	extract($_REQUEST);
	list($po,$item,$cutting,$type,$color)=explode('**', $data);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name");
	$sewing_line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name");
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');


	$production_sql="SELECT a.serving_company, c.color_number_id,c.size_number_id,sum(b.production_qnty) as qntys,c.order_quantity  from pro_garments_production_mst a,pro_garments_production_dtls b ,wo_po_color_size_breakdown c , wo_po_break_down d where a.id=b.mst_id and b.color_size_break_down_id=c.id and c.po_break_down_id=d.id and c.job_no_mst=d.job_no_mst and a.po_break_down_id=d.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and a.po_break_down_id='$po' and a.item_number_id='$item' and b.cut_no='$cutting' and c.color_number_id='$color' and a.production_type='$type' group by  a.serving_company, c.color_number_id,c.size_number_id,c.order_quantity";
	 $color_size_wise_qnty=array();
	 $size_all_arr=array();
	 foreach(sql_select($production_sql) as $keys=>$vals)
	 {
	 	if($po_col_size_arr[$vals[csf("color_number_id")]][$vals[csf("size_number_id")]]=="")
	 	{
	 		$color_size_wise_qnty[$vals[csf("color_number_id")]][$vals[csf("size_number_id")]]["order_quantity"]+=$vals[csf("order_quantity")];
	 		$po_col_size_arr[$vals[csf("color_number_id")]][$vals[csf("size_number_id")]]=1;
	 	}
	 	
	 	$working_comp_color_size_wise_qnty[$vals[csf("serving_company")]][$vals[csf("color_number_id")]][$vals[csf("size_number_id")]]["qntys"]+=$vals[csf("qntys")];
	 	$details_part_array[$vals[csf("serving_company")]][$vals[csf("color_number_id")]]=$vals[csf("serving_company")];
	 	$size_all_arr[$vals[csf("size_number_id")]]=$vals[csf("size_number_id")];
	 	$color_all_arr[$vals[csf("color_number_id")]]=$vals[csf("color_number_id")];
	 }
	  
	 $size_count=count($size_all_arr)*45;
	 $tbl_width=200+$size_count;
	?>
     

    </head>
    <body>
        <div align="center" style="width:100%;" >
            
            
             	<table width="<? echo $tbl_width ;?>" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
             		<caption> <strong>Size</strong></caption>
             		<thead>
             			<tr>                	 
             				<th width="30" >SL</th>
             				<th width="90">Color Name</th>             				 
             				<?
             				foreach($size_all_arr as $key=>$val)
             				{
             					?>
             					<th width="45"><? echo $sizearr[ $key] ;?></th>
             					<?
             				}
             				?>
             				<th width="80">Total</th>
             			</tr>           
             		</thead>
             	</table>
             	<div style="max-height:300px; overflow:auto;">
             	<table  width="<? echo $tbl_width ;?>" border="1" rules="all" class="rpt_table">
             	<?
             	$p=1;
              	$gr_size_total=array();
              	$size_total=0;
             	foreach($color_all_arr as  $keys=> $rows)            		 
             	{
             		$total_sizeqnty=0;
             		
             		?>
             			<tr>                	 
         						<td align="center" width="30" ><? echo $p++;?></td>
          						<td align="center"  width="90"><? echo $colorarr[$keys] ;?></td>
         						<?
         						
         						foreach($size_all_arr as $size_key=>$val)
         						{
         							?>
         							<td align="right"  width="45"><? echo  $value= $color_size_wise_qnty[$keys][$size_key]["order_quantity"] ;?></td>

         							<?
         							$gr_size_total[$size_key]+=$value;
         							$total_sizeqnty+=$value;
         							 
         							
         						}
         					 
         						?>

         						<td align="right"  width="80"><b><? echo $total_sizeqnty;?></b></td>
         						 
             						 
             			</tr>  
				<?
				}
				?>   
						<tr>
							<td colspan="2" align="right"><b>Total</b></td>
							<?
							$gr_all_size=0;
             				foreach($size_all_arr as $key=>$val)
             				{
             					?>
             					<td width="45" align="right"><b><? echo $vals=$gr_size_total[$key];?></b></td>

             					<?
             					$gr_all_size+=$vals;
             				}

             				?>
             				<td align="right"><b><? echo $gr_all_size?></b></td>
						</tr>            		 
             		</table>
             		</div>

             		<table width="<? echo $tbl_width+120 ;?>" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
             		<caption> <strong>Details</strong></caption>
             		<thead>

             			<tr>                	 
             				<th width="30" >SL</th>
             				<th width="120">Working Company</th>             				 
             				<th width="90">Color Name</th>             				 
             				<?
             				foreach($size_all_arr as $key=>$val)
             				{
             					?>
             					<th width="45" align="right"><? echo $sizearr[ $key] ;?></th>

             					<?
             				}

             				?>

             				<th width="80">Total</th>
             				 
             			</tr>           
             		</thead>
             	</table>
             	<div style="max-height:300px; overflow:auto;">
             	<table  width="<? echo $tbl_width+120 ;?>" border="1" rules="all" class="rpt_table">
             	<?
             	$p=1;
             	$detail_grand_total = 0;
             	$dtls_gr_size=array();
             	foreach($details_part_array as  $company_id=> $color_data)            		 
             	{             		
             		foreach($color_data as  $color_id=> $rows)
             		{
             			?>
             			<tr>                	 
         						<td align="center" width="30" ><? echo $p++;?></td>
          						<td align="center"  width="120"><? echo $company_library[$company_id] ;?></td>
          						<td align="center"  width="90"><? echo $colorarr[$color_id] ;?></td>
         						<?
         						$size_total=0;
         						foreach($size_all_arr as $size_key=>$val)
         						{
         							?>
         							<td align="right"  width="45"><? echo $size_qn=  $working_comp_color_size_wise_qnty[$company_id][$color_id][$size_key]["qntys"] ;?></td>

         							<?
         							$size_total+=$size_qn;
         							$dtls_gr_size[$size_key]+=$size_qn;
         							
         						}
         						$detail_grand_total += $size_total;
         						?>
         						<td align="right"  width="80"><b><? echo $size_total;?></b></td>         						 
             						 
             			</tr>  
					<?
					}
				}
				?>
					<tr>
						<td colspan="3" align="right"><b>Total</b></td>
						<?
         				foreach($size_all_arr as $key=>$val)
         				{
         					?>
         					<td width="45" align="right"><b><? echo $dtls_gr_size[$key];?></b></td>

         					<?
         				}

         				?>
         				<td align="right"><b><?php echo $detail_grand_total;?></b></td>
					</tr>               		 
             	</table>
             </div>
             
        </div>
      
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    
    <?
	
	exit();
}

 if($action=="fab_issue_popup")
{
	extract($_REQUEST);	 
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	 ?>

    </head>
    <body>
        <div align="center" style="width:100%;" >
            
            
             	<table width="660" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
             		<caption> <strong>Issue To Cutting Info</strong></caption>
             		<thead>
             			<tr>                	 
             				<th width="30" >SL</th>
             				<th width="110">Issue No</th>             				 
             				<th width="90">Challan No</th>             				 
             				<th width="90">Issue Date</th>             				 
             				<th width="90">Batch No</th>           				 
             				<th width="90">Issue Qnty</th>
             				<th width="160">Fabric Description</th>
             			</tr>           
             		</thead>
             	</table>
             	<div style="">
             	<table  width="660" border="1" rules="all" class="rpt_table">
             	<?
             	$p=1; 
             	$sqls=sql_select("SELECT a.issue_number,a.issue_date,a.challan_no,b.batch_id,sum(b.issue_qnty) as qnty from inv_issue_master a,inv_finish_fabric_issue_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and b.batch_id='$batch_id' group by a.issue_number,a.issue_date,a.challan_no,b.batch_id");

             	$batch_sql="SELECT a.id, a.batch_no,b.item_description from pro_batch_create_mst a,PRO_BATCH_CREATE_DTLS b  where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id='$batch_id'";
             	foreach(sql_select($batch_sql) as $vals)
             	{
             		$batch_array[$vals[csf("id")]]["batch_no"]=$vals[csf("batch_no")];
             		$batch_array[$vals[csf("id")]]["item_description"]=$vals[csf("item_description")];
             	}
             	$total=0;
             	foreach($sqls as  $keys=> $rows)            		 
             	{
             		
             		?>

             		<tr>                	 
         				<td align="center" width="30" ><? echo $p++;?></td>
         				<td align="center"  width="110"><? echo $rows[csf("issue_number")];?></td>             				 
         				<td align="center"  width="90"><? echo $rows[csf("challan_no")];?></td>             				 
         				<td align="center"  width="90"><? echo $rows[csf("issue_date")];?></td>             				 
         				<td align="center"  width="90"><? echo $batch_array[$rows[csf("batch_id")]]["batch_no"];?></td>           				 
         				<td align="center"  width="90"><? echo $rows[csf("qnty")];?></td>
         				<td align="center"  width="160"><? echo $batch_array[$rows[csf("batch_id")]]["item_description"];?></td>
             			</tr>   
             			 
					<?
					$total+=$rows[csf("qnty")];
				}
				?>   
						<tr bgcolor="#E4E4E4">                	 
             				<td colspan="5" align="right">Total</td>       				 
             				<td  align="center"  width="90"><? echo $total;?></td>
             				<td  align="center"  width="160">&nbsp;</td>
             			</tr>              		 
             		</table>
             		</div>

             		
          
             
        </div>
      
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    
    <?
	
	exit();
}
?>