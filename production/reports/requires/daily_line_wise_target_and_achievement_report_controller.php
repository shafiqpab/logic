<?
header('Content-type:text/html; charset=utf-8');
session_start();

include('../../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
function pre($array){
    echo "<pre>";
    print_r($array);
    echo "</pre>";
}

//====================== load library ======================== 
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$company_short_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name"  );
$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  ); 
$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  ); 
$lineArr = return_library_array("select id,line_name from lib_sewing_line order by id","id","line_name"); 
$prod_reso_line_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
$lineDataArr = sql_select("select id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1
    order by sewing_line_serial"); 

if ($action=="load_drop_down_location")
{
    echo create_drop_down( "cbo_location_id", 140, "SELECT id,location_name FROM lib_location WHERE status_active=1 AND is_deleted=0 AND company_id='$data' 
    ORDER BY location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/daily_line_wise_target_and_achievement_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/daily_line_wise_target_and_achievement_report_controller' );",0 ); 
    exit();      
}

if ($action=="load_drop_down_floor")
{
    echo create_drop_down( "cbo_floor_id", 150, "SELECT id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process=5 order by floor_name","id,floor_name", 0, "-- Select Floor --", $selected, "load_drop_down( 'requires/daily_line_wise_target_and_achievement_report_controller',this.value+'_'+document.getElementById('cbo_location_id').value+'_'+document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_date').value, 'load_drop_down_line', 'line_td' );get_php_form_data( this.value, 'eval_multi_select2', 'requires/daily_line_wise_target_and_achievement_report_controller' );",0 );             
    exit();   


}
if ($action == "eval_multi_select") {
    echo "set_multiselect('cbo_floor_id','0','0','','0');\n";
	echo 'setTimeout[($("#floor_td a").attr("onclick","disappear_list(cbo_floor_id,0);getLineId();") ,1000)];';
    exit();
}
if ($action == "eval_multi_select2") {
    echo "set_multiselect('cbo_line_id','0','0','','0');\n";
	echo 'setTimeout[($("#line_td a").attr("onclick","disappear_list(cbo_line_id,0);") ,1000)];';
    exit();
}
 

if($action=="load_drop_down_line")
{
	extract($_REQUEST);
	$explode_data = explode("_",str_replace("'", "", $formData));
	$txt_sewing_date = $explode_data[3];
	$cond="";
	$prod_reso_allocation=return_field_value("auto_update","variable_settings_production","company_name=$explode_data[2] and variable_list=23 and is_deleted=0 and status_active=1");
	if($prod_reso_allocation==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_array=array();
		if($txt_sewing_date=="")
		{
			if( $explode_data[1] ) $cond.= " and location_id= $explode_data[1]";
			if( $explode_data[0] ) $cond.= " and floor_id in($explode_data[0])";

			$line_data=sql_select("select id, line_number from prod_resource_mst where is_deleted=0 $cond");
		}
		else
		{
			if( $explode_data[1]) $cond.= " and a.location_id= $explode_data[1]";
			if( $explode_data[0]) $cond.= " and a.floor_id in($explode_data[0])";

			if($db_type==0)
			{
				$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and b.pr_date='".change_date_format($txt_sewing_date,'yyyy-mm-dd')."' and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id  order by a.prod_resource_num");
			}
			else if($db_type==2 || $db_type==1)
			{
				$line_data=sql_select("select a.id, a.line_number,a.prod_resource_num from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and b.pr_date='".date("j-M-Y",strtotime($txt_sewing_date))."' and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id,a.prod_resource_num, a.line_number  order by a.prod_resource_num");
			}
		}
		$line_merge=9999;
		foreach($line_data as $row)
		{
			$line='';
			$line_number=explode(",",$row[csf('line_number')]);
			foreach($line_number as $val)
			{
				if(count($line_number)>1)
				{
					$line_merge++;
					$new_arr[$line_merge]=$row[csf('id')];
				}
				else
					$new_arr[$val]=$row[csf('id')];
				if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
			}
			$line_array[$row[csf('id')]]=$line;
		}

		ksort($new_arr);
		foreach($new_arr as $key=>$v)
		{
			$line_array_new[$v]=$line_array[$v];
		}
		//echo create_drop_down( "cbo_sewing_line", 110,$line_array_new,"", 1, "--- Select ---", $selected, "",0,0 );
		echo create_drop_down( "cbo_line_id", 110,$line_array_new,"", 1, "--- Select ---", $selected, "",0,0 );
	}
	else
	{
		if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_name= $explode_data[1]";
		if( $explode_data[0]!=0 ) $cond = " and floor_name in($explode_data[0])";

		echo create_drop_down( "cbo_line_id", 110, "select id,line_name from lib_sewing_line where  is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name","id,line_name", 0, "--- Select ---", $selected, "",0,0 );
	}
    ?>
    <script>
        set_multiselect('cbo_line_id','0','0','','0');
    </script>
    <?
	exit();
}



if($action=="report_generate") 
{
    $process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	// ======================================== Library Data =============================================
	$companyArr = return_library_array("select id,company_name from lib_company","id","company_name"); 
	$buyerArr = return_library_array("select id,short_name from lib_buyer","id","short_name"); 
	$locationArr = return_library_array("select id,location_name from lib_location","id","location_name"); 
	$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name"); 
	$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name");   
	$prod_reso_arr=return_library_array( "SELECT id, line_number from prod_resource_mst",'id','line_number');
	$lineDataArr = sql_select("select id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1
	order by sewing_line_serial"); 
	foreach($lineDataArr as $lRow)
	{
		$lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
		$lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
		$lastSlNo=$lRow[csf('sewing_line_serial')];
	}

	$comapny_id=str_replace("'","",$cbo_company_id);
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$cbo_company_id and variable_list=23 and is_deleted=0 and status_active=1");
	//echo $prod_reso_allo."eee";die;
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and b.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and b.buyer_name=$cbo_buyer_name";
	}
// 
	$lc_company		 = str_replace("'","",$cbo_lc_company_id);
	$working_company = str_replace("'","",$cbo_company_id);
	$location_id	 = str_replace("'","",$cbo_location_id);
	$style_ref		 = str_replace("'","",$txt_style_ref);
	$floor_id		 = str_replace("'","",$cbo_floor_id);
	$date			 = str_replace("'","",$txt_date);
    $timestamp       = strtotime($date);
    $today_date  = trim(date('d/m/Y', $timestamp)) ;
    $target_date  = trim(date('d-m-Y', $timestamp)) ;

	$prod_cond="";
	if($lc_company==0) $prod_cond .=""; else $prod_cond.=" and d.company_id=".str_replace("'","",$cbo_lc_company_id)."";
	if($working_company==0) $prod_cond .=""; else $prod_cond .=" and d.serving_company= $working_company";
	if($location_id==0) $prod_cond .=""; else $prod_cond .=" and d.location=$location_id";  
	if($location_id==0) $prod_cond .=""; else $location_cond .=" and a.location_id = $location_id";  
    if($style_ref=="") $prod_cond .=""; else $prod_cond .=" and a.style_ref_no = $txt_style_ref";
	if($floor_id==0) $prod_cond .=""; else $prod_cond .=" and d.floor_id = $floor_id";
	if($working_company==0) $prod_cond2 .=""; else $prod_cond2 .=" and a.serving_company= $working_company";
	if($location_id==0) $prod_cond2 .=""; else $prod_cond2 .=" and a.location=$location_id";  
	if($floor_id==0) $prod_cond2 .=""; else $prod_cond2 .=" and a.floor_id = $floor_id";
    if($date=='') $prod_cond2 .=""; else $prod_cond2 .=" and a.production_date = $txt_date";


	/* =====================================================================================================/
	/										Get PO ID 											/
	/===================================================================================================== */
	$po_sql="SELECT a.po_break_down_id from pro_garments_production_mst a  where  a.status_active=1 and a.is_deleted=0 and a.production_type in(4,5) $prod_cond2";
    $po_array = return_library_array($po_sql,'po_break_down_id','po_break_down_id');
    // pre($po_array); die;
    
    //=================================== Delete Order Id From TEMP ENGINE ====================================
	$con = connect();
	execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form =51 and ref_from=1");
    oci_commit($con);  
	//=================================== Insert order_id into TEMP ENGINE ====================================

	fnc_tempengine("gbl_temp_engine", $user_id, 51, 1,$po_array, $empty_arr); 
	oci_commit($con);  

	/* =====================================================================================================/
	/										Gmts Prod and Entry data											/
	/===================================================================================================== */
	
	$sql="SELECT a.id as job_id,b.id as po_id ,d.floor_id,d.sewing_line,a.buyer_name  as buyer,a.style_ref_no,b.po_quantity as po_qty,a.gmts_item_id as item,e.production_type as prod_type,e.production_qnty as total_prod_qty,a.set_smv,d.prod_reso_allo,TO_CHAR (d.production_date, 'DD/MM/YYYY - HH:MI:SS A.M.') AS production_date,TO_CHAR (d.insert_date, 'DD/MM/YYYY - HH:MI:SS A.M.') as insert_date from wo_po_details_master a, wo_po_break_down b,pro_garments_production_mst d, pro_garments_production_dtls e,gbl_temp_engine f where  a.id = b.job_id and  d.po_break_down_id=b.id and d.id=e.mst_id and d.po_break_down_id = f.ref_val and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0  and d.prod_reso_allo=1 and d.production_type in(4,5) and f.entry_form=51 and f.ref_from=1 and f.user_id=$user_id  $prod_cond order by production_date desc";
	// echo $sql;die;
	
	$res = sql_select($sql);
	$data_array = array();
	$lc_com_array = array();
	$poIdArr=array();
	$jobIdArr=array();
	$all_style_arr=array();
	$po_unit_price_array = array();
	$line_wise_po_item_array = array();
	$prod_date_arr = array();
    $today_prod = 0;
	foreach ($res as $v)
	{
		$lc_com_array[$v[csf('company_id')]] = $v[csf('company_id')];
		$poIdArr[$v['PO_ID']] = $v['PO_ID'];	
		$jobIdArr[$v['JOB_ID']] = $v['JOB_ID'];	
		$all_style_arr[$v['STYLE_REF_NO']] = $v['STYLE_REF_NO'];
		$style_wise_po_arr[$v['STYLE_REF_NO']][$v['PO_ID']] = $v['PO_ID'];
		$line_wise_po_item_array[$v['SEWING_LINE']] .= $v['PO_ID']."**".$v['ITEM']."**".$v['STYLE_REF_NO']."__";

		$sewing_line='';

        // echo $v['PROD_RESO_ALLO'];die;
		if($v['PROD_RESO_ALLO']==1)
		{
			$sewing_line_ids=$prod_reso_arr[$v['SEWING_LINE']];
			$sl_ids_arr = explode(",", $sewing_line_ids);
			$sewing_line_id = $sl_ids_arr[0]; // always 1st line id will take
			foreach($sl_ids_arr as $val)
			{
				if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
			}
		}
		else
		{
			$sewing_line_id=$v['SEWING_LINE'];
			$sewing_line=$lineArr[$v['SEWING_LINE']];
		}
		
		if($lineSerialArr[$sewing_line_id]=="")
		{
			$lastSlNo++;
			$slNo=$lastSlNo;
			$lineSerialArr[$sewing_line_id]=$slNo;
		}
		else $slNo=$lineSerialArr[$sewing_line_id]; 

        $sew_line_id_arr [$v['SEWING_LINE']] = $v['SEWING_LINE'];

		$data_array[$v['FLOOR_ID']][$slNo][$sewing_line][$v['JOB_ID']][$v['ITEM']]['PO_QTY'] = $v['PO_QTY'];
		$data_array[$v['FLOOR_ID']][$slNo][$sewing_line][$v['JOB_ID']][$v['ITEM']]['SEWING_LINE'] = $v['SEWING_LINE'];
		$data_array[$v['FLOOR_ID']][$slNo][$sewing_line][$v['JOB_ID']][$v['ITEM']]['PO_ID'] = $v['PO_ID'];
		$data_array[$v['FLOOR_ID']][$slNo][$sewing_line][$v['JOB_ID']][$v['ITEM']]['SMV'] = $v['SET_SMV'];
		$data_array[$v['FLOOR_ID']][$slNo][$sewing_line][$v['JOB_ID']][$v['ITEM']]['STYLE'] = $v['STYLE_REF_NO'];
		$data_array[$v['FLOOR_ID']][$slNo][$sewing_line][$v['JOB_ID']][$v['ITEM']]['BUYER'] = $v['BUYER'];

        $prod_date = explode('-',$v['PRODUCTION_DATE']);

        if (trim($prod_date[0]) < trim($today_date) ) {
            $prod_date_arr [trim($prod_date[0])]= $prod_date[0];
        }  
        if (trim($prod_date[0]) == trim($today_date) ) {
            $today_prod += $v['TOTAL_PROD_QTY'];
        }

		if ($v['PROD_TYPE'] == 4) {
            $input_date = explode('-',$v['INSERT_DATE']);
            
			$data_array[$v['FLOOR_ID']][$slNo][$sewing_line][$v['JOB_ID']][$v['ITEM']]['TOTAL_SEW_IN'] += $v['TOTAL_PROD_QTY'];  
			$data_array[$v['FLOOR_ID']][$slNo][$sewing_line][$v['JOB_ID']][$v['ITEM']]['SEW_IN_DATE'] =$input_date[0];    
			$data_array[$v['FLOOR_ID']][$slNo][$sewing_line][$v['JOB_ID']][$v['ITEM']]['SEW_IN_TIME'] =$input_date[1];    
		}
		if ($v['PROD_TYPE'] == 5) {
			$data_array[$v['FLOOR_ID']][$slNo][$sewing_line][$v['JOB_ID']][$v['ITEM']]['TOTAL_SEW_OUT'] += $v['TOTAL_PROD_QTY'];
            $data_array[$v['FLOOR_ID']][$slNo][$sewing_line][$v['JOB_ID']][$v['ITEM']]['MIN_SEW_OUT_DATE'] = $prod_date[0];     
            $data_array[$v['FLOOR_ID']][$slNo][$sewing_line][$v['JOB_ID']][$v['ITEM']]['MIN_SEW_OUT_TIME'] = $prod_date[1];     
            $data_array[$v['FLOOR_ID']][$slNo][$sewing_line][$v['JOB_ID']][$v['ITEM']][trim($prod_date[0])]['SEW_OUT'] += $v['TOTAL_PROD_QTY'];     
		}  
	}
    // echo $today_prod; die;
    // pre($prod_date_arr);  die;

    if ($today_prod > 0) 
    {
        /*===================================================================================== /
        /										po active days									/
        /===================================================================================== */
        $jobIds_cond = where_con_using_array($jobIdArr,0,"c.job_id");
        $po_active_sql="SELECT a.sewing_line,a.production_date,c.id as po_id,a.item_number_id from  wo_po_break_down c,pro_garments_production_mst a where  a.po_break_down_id=c.id and a.production_type=4 and  a.status_active=1 and a.is_deleted=0 and c.is_deleted=0  $jobIds_cond group by  a.sewing_line,a.production_date,c.id,a.item_number_id";
        // echo $po_active_sql;die;
        foreach(sql_select($po_active_sql) as $v)
        {
            $prod_dates=$v['PRODUCTION_DATE'];
            if($duplicate_date_arr[$v['SEWING_LINE']][$v['PO_ID']][$v['ITEM_NUMBER_ID']][$prod_dates]=="")
            {
                $active_days_arr[$v['SEWING_LINE']][$v['PO_ID']][$v['ITEM_NUMBER_ID']]++;
                $duplicate_date_arr[$v['SEWING_LINE']][$v['PO_ID']][$v['ITEM_NUMBER_ID']][$prod_dates]=$prod_dates;
            }
        }
        // pre($duplicate_date_arr); die; 


        /* =====================================================================================================/
        /												Prod Resource data										/
        /===================================================================================================== */
            
        $prod_resource_array=array();
        $line_cond = where_con_using_array($sew_line_id_arr,0,"a.id");
        $pro_res_sql =  "SELECT a.id as sew_line, a.location_id, a.floor_id, a.line_number, b.active_machine,TO_CHAR (b.pr_date, 'DD/MM/YYYY') AS pr_date, b.man_power, b.operator, b.helper, b.line_chief, b.target_per_hour, b.working_hour,c.capacity from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=c.mst_id and a.id=b.mst_id and c.id=b.mast_dtl_id and a.company_id=$cbo_lc_company_id $location_cond $line_cond ";
        // echo $pro_res_sql;die;
        //and b.pr_date=$txt_date
        $pro_res_arr =sql_select($pro_res_sql); 
        foreach($pro_res_arr as $v)
        {  
            if ($today_date == $v['PR_DATE']) 
            { 
                $prod_resource_array[$v['SEW_LINE']]['CAPACITY']=$v['CAPACITY'];
                $prod_resource_array[$v['SEW_LINE']]['WORKING_HOUR']=$v['WORKING_HOUR'];
                $prod_resource_array[$v['SEW_LINE']]['OPERATOR']=$v['OPERATOR'];
                $prod_resource_array[$v['SEW_LINE']]['HELPER']=$v['HELPER'];
                $prod_resource_array[$v['SEW_LINE']]['TERGET_HOUR']=$v['TARGET_PER_HOUR'];
                $prod_resource_array[$v['SEW_LINE']]['MAN_POWER']=$v['MAN_POWER']; 
                $prod_resource_array[$v['SEW_LINE']]['LINE_CHIEF']=$v['MAN_POWER']; 
            }
                $prod_resource_array[$v['SEW_LINE']][$v['PR_DATE']]['TERGET']=$v['TARGET_PER_HOUR'] * $v['MAN_POWER']; 
            /*  $prod_resource_array[$v['SEW_LINE']][$v['PR_DATE']]['MAN_POWER']= $v['MAN_POWER']; 
                $prod_resource_array[$v['SEW_LINE']][$v['PR_DATE']]['TARGET_PER_HOUR']=$v['TARGET_PER_HOUR']  ;  */ 
            
        }
        // echo array_keys($prod_date_arr)[1];
        
        // pre($prod_resource_array);
        /*===================================================================================== /
        /									Operation Bulletin 									/
        /===================================================================================== */
        $style_cond = where_con_using_array($all_style_arr,1,"a.style_ref");
        $sqlgsd="SELECT a.PROCESS_ID,a.style_ref,a.gmts_item_id,b.id, b.mst_id, b.row_sequence_no, b.body_part_id, b.lib_sewing_id, b.resource_gsd, b.attachment_id, b.efficiency, b.total_smv, b.target_on_full_perc from PPL_GSD_ENTRY_MST a,ppl_gsd_entry_dtls b where a.id=b.mst_id $style_cond and a.bulletin_type=4 and b.is_deleted=0 order by b.row_sequence_no asc";
        // echo $sqlgsd;die;
        $gsd_res=sql_select($sqlgsd);
        $mst_id_arr = array();
        foreach($gsd_res as $row)
        {
            $mst_id_arr[$row['MST_ID']] = $row['MST_ID'];
        }
        $mst_id_cond = where_con_using_array($mst_id_arr,0,"a.gsd_mst_id");
        // ======================================================================
        $balanceDataArray=array();
        $blData=sql_select("SELECT a.id, gsd_dtls_id, smv, layout_mp from ppl_balancing_mst_entry a, ppl_balancing_dtls_entry b where a.id=b.mst_id and a.balancing_page=1 $mst_id_cond and a.is_deleted=0 and b.is_deleted=0");
        foreach($blData as $row)
        {
            $balanceDataArray[$row[csf('gsd_dtls_id')]]['smv']=$row[csf('smv')];
            $balanceDataArray[$row[csf('gsd_dtls_id')]]['layout_mp']=$row[csf('layout_mp')];
        }

        $gsd_data_array = array();

        foreach($gsd_res as $slectResult)
        {
            if($balanceDataArray[$slectResult[csf('id')]]['smv']>0)	
            {
                $smv=$balanceDataArray[$slectResult[csf('id')]]['smv'];
            }
            else
            {
                $smv=$slectResult[csf('total_smv')];
            }
            
            $rescId=$slectResult[csf('resource_gsd')];
            $layOut=$balanceDataArray[$slectResult[csf('id')]]['layout_mp'];
            
            if($rescId==40 || $rescId==41 || $rescId==43 || $rescId==44 || $rescId==48 || $rescId==68 || $rescId==69 || $rescId==70 || $rescId==147)
            {
                $helperSmv=$helperSmv+$smv;
                $helperMp=$helperMp+$layOut;
            }
            else if($rescId==53)
            {
                $fIMSmv=$fIMSmv+$smv;
                $fImMp=$fImMp+$layOut;
            }
            else if($rescId==54)
            {
                $fQISmv=$fQISmv+$smv;
                $fQiMp=$fQiMp+$layOut;
            }
            else if($rescId==55)
            {
                $polyHelperSmv=$polyHelperSmv+$smv;
                $polyHelperMp=$polyHelperMp+$layOut;
            }
            else if($rescId==56)
            {
                $pkSmv=$pkSmv+$smv;
                $pkMp=$pkMp+$layOut;
            }
            else if($rescId==90)
            {
                $htSmv=$htSmv+$smv;
                $htMp=$htMp+$layOut;
            }
            else if($rescId==176)
            {
                $imSmv=$imSmv+$smv;
                $imMp=$imMp+$layOut;
            }
            else
            {
                $machineSmv=$machineSmv+$smv;
                $machineMp=$machineMp+$layOut;
                
                $mpSumm[$rescId]+= $layOut;
            }
            $i++;
            $totMpSumm = $helperMp + $machineMp + $sQiMp + $fImMp + $fQiMp + $polyHelperMp + $pkMp + $htMp + $imMp;
            // echo $helperMp."<br>";
            
            $gsd_data_array[$slectResult['STYLE_REF']][$slectResult['GMTS_ITEM_ID']]['OPERATOR'] = $machineMp;
            $gsd_data_array[$slectResult['STYLE_REF']][$slectResult['GMTS_ITEM_ID']]['SEW_HELPER'] = $helperMp;
            $gsd_data_array[$slectResult['STYLE_REF']][$slectResult['GMTS_ITEM_ID']]['PLAN_MAN'] = $totMpSumm;
        }
        // yesterday Date 
        // pre($prod_date_arr);die;
        $yesterday_date = array_keys($prod_date_arr)[0]; 
    }
    
    //=================================== Delete Order Id From TEMP ENGINE ====================================
	$con = connect();
	execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form =51 and ref_from=1");
    oci_commit($con);  
    disconnect($con);
	ob_start();
    $width = 2920; 
    echo load_html_head_contents("Popup Info", "../../../", 1, 1,'','','');
    ?>
     <style>
        .report-container{ position: relative; } 
        .tableFixHead thead { position: sticky;   top: 0; z-index: 99; }
        .tableFixHead tbody {height: 50px; overflow: scroll;} 
        .report-container{
            margin-top: 50px !important;
        }
        th,td{
            word-break: break-all;
        }
     </style>
    </head>
        <body>
            <div class="report-container" align="center" style="width:<?= $width ?>px;" > 
                <form name="styleRef_form" id="styleRef_form">  
                <div style="margin: 20px auto; ">
                    <div>
                        <h3> <?= $company_library[str_replace("'", "", $cbo_lc_company_id)] ?></h3>
                        <h3> Daily Line Wise Target And Achievement Report </h3>
                    </div>
                </div>
                <fieldset>
                    <legend>Report</legend> 
                    <div align="center" style="height:auto; width:<?= $width+20;?>px; margin:0 auto; padding:0;">  
                        <table border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<?= $width;?>" rules="all" id="rpt_table_header" align="left">
                            <thead>
                                <? $content.=ob_get_flush(); ?>		 
                                <tr>
                                    <th colspan="3" width="300"align="center"><p>Target Date :</p> </th>
                                    <th colspan="2" width="200"align="center"> <?= $target_date ?></th>
                                    
                                    <th rowspan="2" width="100"><p> SMV/Efficiency </p></th>
                                    <th rowspan="2" width="100"><p> MD Sir Target </p></th>

                                    <th  colspan="2" width="200" align="center"><p>Input Date & Time</p></th>
                                    <th  colspan="2" width="200"align="center"><p>1st output date & Time </p></th>

                                    <th rowspan="2" width="50">R. Days</th>
                                    <th rowspan="2" width="50">W/H</th>

                                    <th colspan="3" width="180" align="center"><p>Present Manpower  </p></th>
                                    <th colspan="4" width="240"><p>As per layout Required Manpower  </p></th>

                                    <th rowspan="2" width="100"><p>Hourly Target </p></th>
                                    <th rowspan="2" width="100"><p>Today Target </p></th>
                                    <th rowspan="2" width="100"><p>Today Achieve </p></th>
                                    <th rowspan="2" width="100"><p>Short/Excess </p></th> 

                                    <th colspan="2" width="200"align="center"><p>Production Date  </p></th>
                                    <th colspan="3" width="300"align="center"><?= $yesterday_date ?></th> 

                                    <th rowspan="2" width="100"><p>Total Input </p></th> 
                                    <th rowspan="2" width="100"><p>Total Output </p></th> 
                                    <th rowspan="2" width="100"><p>Line Balance (W.I.P )</p></th> 
                                    <th rowspan="2" ><p>Next Style/Remark </p></th> 
                                </tr>
                                <tr>
                                    <th  width="100">Line</th>
                                    <th  width="100">Buyer</th>
                                    <th  width="100">Style</th>

                                    <th  width="100">Order Qty</th>
                                    <th  width="100">Item</th> 

                                    <th  width="100">Date</th>
                                    <th  width="100">Time</th>

                                    <th  width="100">Date</th>
                                    <th  width="100">Time</th>

                                    <th  width="60">OP</th> 
                                    <th  width="60">Helper</th> 
                                    <th  width="60">Total</th>  

                                    <th  width="60">OP</th> 
                                    <th  width="60">Helper</th> 
                                    <th  width="60">Total</th> 
                                    <th  width="60">Diff</th> 

                                    <th  width="100">Yesterday Target</th> 
                                    <th  width="100">Yesterday  Achieve</th> 

                                    <th  width="100">Achive Avg</th> 
                                    <th  width="100">Short/Excess</th> 

                                    <th  width="100">Short/Exces-%</th> 
                                </tr>
                            </thead>
                        </table>
                        <div style="max-height:300px; overflow-y:auto; width:<?= $width+20?>px" id="scroll_body">
                            <table border="1" cellpadding="0" cellspacing="0" align="left" class="rpt_table"  width="<?= $width ?>" rules="all" id="table_body" >
                                <tbody>
                                    <?
                                        $i=0;
                                        if ($today_prod > 0) 
                                        { 
                                            foreach($data_array as $floor => $floor_arr)
                                            {
                                                foreach($floor_arr as $sl => $sl_arr)
                                                {
                                                    $ft_smv = $ft_pro_res_op = $ft_pro_res_hp = $ft_pro_res_total  = $ft_op_bul_oprator = $ft_op_bul_helper = $ft_op_bul_total =  $ft_diff = $ft_diff = $target_hour = $ft_today_target = $ft_today_achive = $ft_short = $ft_yesterday_target = $ft_yesterday_achive = $ft_achive_avg = $ft_yesterday_short = $ft_yesterday_short_persent = $ft_total_sew_in = $ft_total_sew_out =
                                                    $ft_balance = 0 ;  
                                                    foreach($sl_arr as $line => $line_arr)
                                                    { 
                                                        foreach($line_arr as $job => $job_arr)
                                                        { 
                                                            $active_days = "";
                                                            foreach($job_arr as $item => $v)
                                                            {   
                                                                $i++;
                                                                $active_days .= ($active_days=="") ? $active_days_arr[$v['SEWING_LINE']][$v['PO_ID']][$item] : "/".$active_days_arr[$v['SEWING_LINE']][$v['PO_ID']][$item];

                                                                // PRODUCTION DATA
                                                                $smv          = number_format($v['SMV'],2);
                                                                $total_sew_in = $v['TOTAL_SEW_IN']; 
                                                                $total_sew_out= $v['TOTAL_SEW_OUT']; 
                                                                $today_achive = $v[$today_date]['SEW_OUT'];
                                                                $balance      =$total_sew_in - $total_sew_out ; 

                                                                
                                                                // PROD RESOURCE DATA	
                                                                $pro_res_data = $prod_resource_array[$v['SEWING_LINE']];
                                                                $working_hr   = $pro_res_data['WORKING_HOUR'];
                                                                $pro_res_op   = $pro_res_data['OPERATOR'];
                                                                $pro_res_hp   = $pro_res_data['HELPER'];
                                                                $pro_res_total= $pro_res_op + $pro_res_hp;
                                                                $target_hour  = $pro_res_data['TERGET_HOUR'];
                                                                $today_target = $target_hour * $working_hr;

                                                                $short =  $today_target - $today_achive;

                                                                // OPERATION BULLETIN DATA 
                                                                $op_bulletin_arr = $gsd_data_array[$v['STYLE']][$item];
                                                                $op_bul_oprator  =  $op_bulletin_arr['OPERATOR'];
                                                                $op_bul_helper   =  $op_bulletin_arr['SEW_HELPER'];
                                                                $op_bul_total    =   $op_bul_oprator + $op_bul_helper;
                                                                $diff            =   $op_bul_total - $pro_res_total;
                                                                // YESTERDAT PROD DATA  
                                                                $yesterday_target = $pro_res_data[trim($yesterday_date)]['TERGET'];  
                                                                $yesterday_achive = $v[$yesterday_date]['SEW_OUT'];
                                                                $achive_avg       = number_format($yesterday_achive /  $working_hr,2)  ;
                                                                $yesterday_short  = $yesterday_target - $yesterday_achive;
                                                                $ystrdy_short_prst= number_format($yesterday_short / $yesterday_target,2) ;

                                                                // FLOOR TOTAL
                                                                $ft_smv              += number_format($smv,2);
                                                                $ft_pro_res_op       += $pro_res_op;
                                                                $ft_pro_res_hp       += $pro_res_hp;
                                                                $ft_pro_res_total    += $pro_res_total;
                                                                $ft_op_bul_oprator   += $op_bul_oprator;
                                                                $ft_op_bul_helper    += $op_bul_helper;
                                                                $ft_op_bul_total     += $op_bul_total;
                                                                $ft_diff             += $diff; 
                                                                $ft_target_hour      += $target_hour;
                                                                $ft_today_target     += $today_target;
                                                                $ft_today_achive     += $today_achive;
                                                                $ft_short            += $short;
                                                                $ft_yesterday_target += $yesterday_target;
                                                                $ft_yesterday_achive += $yesterday_achive;
                                                                $ft_achive_avg       += number_format($achive_avg,2);
                                                                $ft_yesterday_short  += $yesterday_short;
                                                                $ft_ystrdy_short_prst+= number_format($ystrdy_short_prst,2);
                                                                $ft_total_sew_in     += $total_sew_in;
                                                                $ft_total_sew_out    += $total_sew_out; 
                                                                $ft_balance          += $balance;

                                                                // FLOOR TOTAL
                                                                $gt_smv              += number_format($smv,2);
                                                                $gt_pro_res_op       += $pro_res_op;
                                                                $gt_pro_res_hp       += $pro_res_hp;
                                                                $gt_pro_res_total    += $pro_res_total;
                                                                $gt_op_bul_oprator   += $op_bul_oprator;
                                                                $gt_op_bul_helper    += $op_bul_helper;
                                                                $gt_op_bul_total     += $op_bul_total;
                                                                $gt_diff             += $diff; 
                                                                $gt_target_hour      += $target_hour;
                                                                $gt_today_target     += $today_target;
                                                                $gt_today_achive     += $today_achive;
                                                                $gt_short            += $short;
                                                                $gt_yesterday_target += $yesterday_target;
                                                                $gt_yesterday_achive += $yesterday_achive;
                                                                $gt_achive_avg       += number_format($achive_avg,2);
                                                                $gt_yesterday_short  += $yesterday_short;
                                                                $gt_ystrdy_short_prst+= number_format($ystrdy_short_prst,2);
                                                                $gt_total_sew_in     += $total_sew_in;
                                                                $gt_total_sew_out    += $total_sew_out; 
                                                                $gt_balance          += $balance;
                                                                
                                                                if ($i % 2 == 0)  $bgcolor = "#E9F3FF";  else $bgcolor = "#FFFFFF";
                                                                ?>
                                                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                                                                        <td width="100"><?= $line; ?></td>
                                                                        <td width="100"><?= $buyerArr[$v['BUYER']]; ?></td>
                                                                        <td width="100"><?= $v['STYLE']; ?></td>
                                                                        <td width="100" align="right"><?= $v['PO_QTY']; ?></td>
                                                                        <td width="100"><?=$garments_item[$item]; ?></td>
                                                                        <td width="100" align="right"><?= $smv; ?></td>
                                                                        <td width="100" align="right"><?= $pro_res_data['CAPACITY'] ; ?></td>
                                                                        <td width="100" align="right"><?= $v['SEW_IN_DATE']; ?></td>
                                                                        <td width="100" align="right"><?= $v['SEW_IN_TIME']    ; ?></td> 
                                                                        <td width="100" align="right"><?= $v['MIN_SEW_OUT_DATE']; ?></td> 
                                                                        <td width="100" align="right"><?= $v['MIN_SEW_OUT_TIME']; ?></td> 
                                                                        <td width="50" align="right"><?= $active_days ; ?></td>
                                                                        <td width="50" align="right"><?= $working_hr ; ?></td>
                                                                        <td width="60" align="right"><?= $pro_res_op; ?></td>
                                                                        <td width="60" align="right"><?= $pro_res_hp; ?></td>
                                                                        <td width="60" align="right"><?= $pro_res_total; ?></td>
                                                                        <td width="60" align="right"><?= $op_bul_oprator; ?></td>
                                                                        <td width="60" align="right"><?= $op_bul_helper; ?></td>
                                                                        <td width="60" align="right"><?= $op_bul_total; ?></td>
                                                                        <td width="60" align="right"><?= $diff; ?></td>
                                                                        <td width="100" align="right"><?= $target_hour; ?></td>
                                                                        <td width="100" align="right"><?= $today_target; ?></td>
                                                                        <td width="100" align="right"><?= $today_achive; ?></td>
                                                                        <td width="100" align="right"><?= $short; ?></td>
                                                                        <td width="100" align="right"><?= $yesterday_target; ?></td>
                                                                        <td width="100" align="right"><?= $yesterday_achive; ?></td>
                                                                        <td width="100" align="right"><?= $achive_avg ; ?></td>
                                                                        <td width="100" align="right"><?= $yesterday_short; ?></td>
                                                                        <td width="100" align="right"><?= $ystrdy_short_prst; ?>%</td> 
                                                                        <td width="100" align="right"><?= $total_sew_in; ?></td>
                                                                        <td width="100" align="right"><?= $total_sew_out ; ?></td>
                                                                        <td width="100" align="right"><?= $balance ?></td>
                                                                        <td > </td>
                                                                        
                                                                    </tr>
                                                                <?
                                                            }	
                                                        }
                                                    }	
                                                }
                                                ?> 
                                                    <tr style='background-color:#c8b6a0;'>
                                                        <th colspan="5" align='center'><?= $floorArr[$floor] ?> Floor </th>
                                                        <th align="right"><?= $ft_smv ?></th>
                                                        <th colspan="7"></th>  
                                                        <th align="right"><?= $ft_pro_res_op ?></th>
                                                        <th align="right"><?= $ft_pro_res_hp ?></th>
                                                        <th align="right"><?= $ft_pro_res_total ?></th>
                                                        <th align="right"><?= $ft_op_bul_oprator ?></th>
                                                        <th align="right"><?= $ft_op_bul_helper ?></th>
                                                        <th align="right"><?= $ft_op_bul_total ?></th>
                                                        <th align="right"><?= $ft_diff ?></th>
                                                        <th align="right"><?= $ft_target_hour ?></th>
                                                        <th align="right"><?= $ft_today_target ?></th>
                                                        <th align="right"><?= $ft_today_achive ?></th>
                                                        <th align="right"><?= $ft_short ?></th>
                                                        <th align="right"><?= $ft_yesterday_target ?></th>
                                                        <th align="right"><?= $ft_yesterday_achive ?></th>
                                                        <th align="right"><?= $ft_achive_avg ?></th>
                                                        <th align="right"><?= $ft_yesterday_short ?></th>
                                                        <th align="right"><?= $ft_ystrdy_short_prst ?>%</th>
                                                        <th align="right"><?= $ft_total_sew_in ?></th>
                                                        <th align="right"><?= $ft_total_sew_out ?></th>
                                                        <th align="right"><?= $ft_balance ?></th> 
                                                        <th></th> 
                                                    </tr>
                                                <?
                                            }
                                        }else
                                        {
                                            ?>
                                            <tr >
                                                <td width="<?=$width?>"> <h2 style="color:#dc3545; margin-left:700px; padding:5px 0;font-size:18px;"> *Data Not Found*</h2></td>
                                            </tr>
                                            <?
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div> 
                        <div style="width:<?= $width+20?>px;">
                            <table width="<?= $width?>px" id="report_table_footer" border="1" class="rpt_table" rules="all" align="left" >
                                <tfoot>
                                    <tr> 
                                        <th width="100"></th>  
                                        <th width="100"></th>  
                                        <th width="100" > Grand Total </th>
                                        <th width="100"></th>  
                                        <th width="100"></th> 
                                        <th width="100" align="right"><?= $gt_smv ?></th>
                                        <th width="100"></th>  
                                        <th width="100"></th>  
                                        <th width="100"></th>  
                                        <th width="100"></th>  
                                        <th width="100"></th>  
                                        <th width="50"></th>  
                                        <th width="50"></th>  
                                        <th width="60" align="right"><?= $gt_pro_res_op ?></th>
                                        <th width="60" align="right"><?= $gt_pro_res_hp ?></th>
                                        <th width="60" align="right"><?= $gt_pro_res_total ?></th>
                                        <th width="60" align="right"><?= $gt_op_bul_oprator ?></th>
                                        <th width="60" align="right"><?= $gt_op_bul_helper ?></th>
                                        <th width="60" align="right"><?= $gt_op_bul_total ?></th>
                                        <th width="60" align="right"><?= $gt_diff ?></th>
                                        <th width="100" align="right"><?= $gt_target_hour ?></th>
                                        <th width="100" align="right"><?= $gt_today_target ?></th>
                                        <th width="100" align="right"><?= $gt_today_achive ?></th>
                                        <th width="100" align="right"><?= $gt_short ?></th>
                                        <th width="100" align="right"><?= $gt_yesterday_target ?></th>
                                        <th width="100" align="right"><?= $gt_yesterday_achive ?></th>
                                        <th width="100" align="right"><?= $gt_achive_avg ?></th>
                                        <th width="100" align="right"><?= $gt_yesterday_short ?></th>
                                        <th width="100" align="right"><?= $gt_ystrdy_short_prst ?>%</th>
                                        <th width="100" align="right"><?= $gt_total_sew_in ?></th>
                                        <th width="100" align="right"><?= $gt_total_sew_out ?></th>
                                        <th width="100" align="right"><?= $gt_balance ?></th> 
                                        <th ></th> 
                                    </tr>
                                </tfoot>
                        
                            </table> 
                        </div> 
                    </div>    
		        </fieldset>
            </div>
        </body>
        <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();  
} 
?>