<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
require_once('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.fabrics.php');

if (!function_exists('pre'))
{
  function pre($array)
  {
    echo "<pre>";
    print_r($array);
    echo "</pre>";
  }
}
$user_id = $_SESSION['logic_erp']['user_id'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$colorname_arr = return_library_array("select id, color_name from lib_color", "id", "color_name");
$country_arr = return_library_array("select id, country_name from lib_country", "id", "country_name");
$company_arr = return_library_array("select id, company_name from lib_company", "id", "company_name");
$buyer_short_library = return_library_array("select id,buyer_name from lib_buyer", "id", "buyer_name");

if ($action == "load_drop_down_buyer") {

	echo create_drop_down("cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
}
//style wise search data
if ($action == "style_wise_search") {
	echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);
?>
	<script>
		var selected_id = new Array;
		var selected_name = new Array;

		function check_all_data() {
			var tbl_row_count = document.getElementById('list_view').rows.length;
			tbl_row_count = tbl_row_count - 0;
			for (var i = 1; i <= tbl_row_count; i++) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value(functionParam);
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function js_set_value(strCon) {
			//alert(strCon);
			var splitSTR = strCon.split("_");
			var str = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			if ($('#tr_' + str).css("display") != 'none') {
				toggle(document.getElementById('tr_' + str), '#FFFFCC');
				if (jQuery.inArray(selectID, selected_id) == -1) {
					selected_id.push(selectID);
					selected_name.push(selectDESC);
				} else {
					for (var i = 0; i < selected_id.length; i++) {
						if (selected_id[i] == selectID) break;
					}
					selected_id.splice(i, 1);
					selected_name.splice(i, 1);
				}
			}
			var id = '';
			var name = '';
			var job = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);
			//alert(name);
			$('#txt_selected_id').val(id);
			$('#txt_selected').val(name);
		}
	</script>
<?
	extract($_REQUEST);
	if ($company == 0) $company_name = "";
	else $company_name = "and a.company_name=$company";
	if ($buyer == 0) $buyer_name = "";
	else $buyer_name = "and a.buyer_name=$buyer";

	

	
	$arr = array();
	$sql = "SELECT b.id,a.style_ref_no,b.po_number,a.job_no,a.job_no_prefix_num,b.grouping,TO_CHAR(a.insert_date,'YYYY') as year from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst $company_name $buyer_name  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by job_no_prefix_num";
  // echo $sql; die;
	echo create_list_view("list_view", "Job Year,Job No ,Style Ref No,Internal Ref No", "80,100,120,120", "480", "310", 0, $sql, "js_set_value", "id,style_ref_no", "", 1, "0", $arr, "year,job_no_prefix_num,style_ref_no,grouping", "", "setFilterGrid('list_view',-1)", "0", "", 1);
	// echo $sql;
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
}
//style wise search data ends here
//Show Button Starts here or generate_report 

if($action=="generate_report")
{
  $process = array( &$_POST );
  extract(check_magic_quote_gpc( $process ));
  // get data starts here with searching
  $floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  ); 
  $line_library=return_library_array( "select id,line_name from lib_sewing_line ", "id", "line_name"  );  
  $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
  $company_id = str_replace("'","",$cbo_company_name);
  $cbo_buyer_name = str_replace("'","",$cbo_buyer_name);
  $txt_style_no = str_replace("'","",$txt_style_no);
  $txt_date_from = str_replace("'","",$txt_date_from);
  $ot_avg_rate = str_replace("'","",$ot_avg_rate);
  $style_ref_cond .= ($txt_style_no !="") ? " and d.style_ref_no IN('$txt_style_no')" : "";
  
  $sql_cond="";
  $sql_cond .= ($company_id!=0) ? " and a.company_id=$company_id" : "";
  
  $buyer_cond .= ($cbo_buyer_name !=0) ? " and d.buyer_name = $cbo_buyer_name" : "";
  $date_cond1 .= ($txt_date_from!="") ? " and c.pr_date='$txt_date_from'" : "";
  $date_cond2 .= ($txt_date_from!="") ? " and a.production_date='$txt_date_from'" : "";
  $sql  = "SELECT a.id as line_id,c.id as dtls_id,d.id as color_size_id,a.company_id,a.location_id,a.floor_id, a.line_number,b.id, b.mst_id,c.target_per_hour,c.pr_date,c.working_hour,b.target_efficiency , c.man_power ,b.target_efficiency,d.po_id,d.working_hour as po_wo_hr
  FROM
    prod_resource_mst a ,
    prod_resource_dtls_mast b,
    prod_resource_dtls c,
    prod_resource_color_size d
  WHERE 
  a.id=b.mst_id 
  and b.ID=c.MAST_DTL_ID
  AND c.mst_id=a.id and d.dtls_id=b.id $sql_cond $date_cond1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  order by floor_id desc";
  // echo    $sql ;die;
  $sql_execute =  sql_select($sql);
  $data_arr = $po_id_array= $color_size_id_array = array();
  foreach($sql_execute as $row)
  {
    $line_cond[$row['LINE_ID']]       = $row['LINE_ID']; 
    $po_id_array[$row['PO_ID']]       = $row['PO_ID']; 
    

    $data_arr[$row['FLOOR_ID']][$row['LINE_ID']]['MAN_POWER'] = $row['MAN_POWER'];
    $data_arr[$row['FLOOR_ID']][$row['LINE_ID']]['LINE_NUMBER'] = $row['LINE_NUMBER'];
    $data_arr[$row['FLOOR_ID']][$row['LINE_ID']]['TARGET_EFFICIENCY'] = $row['TARGET_EFFICIENCY'];

    if (!$color_size_id_array[$row['COLOR_SIZE_ID']]) 
    {
      $po_working_hr_arr[$row['FLOOR_ID']][$row['LINE_ID']][$row['PO_ID']] += $row['PO_WO_HR'];   
    }
    $color_size_id_array[$row['COLOR_SIZE_ID']] = $row['COLOR_SIZE_ID'];

  }
  // pre($po_working_hr_arr); die;
  $line_cond_id = implode(",",$line_cond);

  //=================================== CLEAR TEMP ENGINE ====================================
  $con = connect();
  execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form = 153 and ref_from in(1)");
  oci_commit($con);

  //=================================== INSERT LINE ID INTO TEMP ENGINE ====================================
  fnc_tempengine("gbl_temp_engine", $user_id, 153, 1,$po_id_array, $empty_arr);



  // ================================== EXTRA HOUR SQL =======================================================>
  $extra_hr_sql = "SELECT total_smv,mst_id as line_id,style_ref_no from prod_resource_smv_adj  where mst_id in ($line_cond_id) and adjustment_source=1 ";
  $extra_hr_sql_res = sql_select($extra_hr_sql);
  $style_wise_extra_hr_array = array();
  foreach ($extra_hr_sql_res as $v) 
  { 
    $style_wise_extra_hr_array[$v['LINE_ID']][$v['STYLE_REF_NO']] += $v['TOTAL_SMV'];
  }
  // pre($style_wise_extra_hr_array); die;
  
  // ================================== gmts prod data =======================================================>
  $sql_data = "SELECT d.id as job_id,a.po_break_down_id,a.item_number_id, a.id , a.company_id , a.production_date,a.floor_id,a.sewing_line, b.ID ,b.production_qnty ,d.style_ref_no ,d.buyer_name ,d.style_description,
  a.production_hour,
  TO_CHAR(a.production_hour,'HH24')  as  prod_hour ,
  c.job_no_mst
    from pro_garments_production_mst a, 
    pro_garments_production_dtls b,  
    wo_po_break_down c ,
    wo_po_details_master d 
      WHERE a.id = b.mst_id 
      AND a.po_break_down_id = c.id 
      AND d.id = c.job_id 
      AND a.sewing_line in($line_cond_id)
      and a.status_active = 1
      and a.is_deleted = 0 
      and b.status_active = 1
      and b.is_deleted = 0 
      and c.status_active = 1
      and c.is_deleted = 0 
      and d.status_active = 1
      and d.is_deleted = 0 
      $style_ref_cond $date_cond2 $sql_cond $buyer_cond";
  // echo $sql_data ;
  $dataArr = sql_select($sql_data);
  $pr_data_arr =array();
  // $running_hour = array();
  $po_wise_style_array = array();
  foreach($dataArr as $val)
  {
    $pr_data_arr[$val['FLOOR_ID']][$val['SEWING_LINE']][$val['PO_BREAK_DOWN_ID']][$val['ITEM_NUMBER_ID']]['Pr_Qty'] += $val['PRODUCTION_QNTY'];
    $pr_data_arr[$val['FLOOR_ID']][$val['SEWING_LINE']][$val['PO_BREAK_DOWN_ID']][$val['ITEM_NUMBER_ID']]['BUYER_NAME'] = $val['BUYER_NAME'];
    $pr_data_arr[$val['FLOOR_ID']][$val['SEWING_LINE']][$val['PO_BREAK_DOWN_ID']][$val['ITEM_NUMBER_ID']]['STYLE_REF_NO'] = $val['STYLE_REF_NO'];
    $pr_data_arr[$val['FLOOR_ID']][$val['SEWING_LINE']][$val['PO_BREAK_DOWN_ID']][$val['ITEM_NUMBER_ID']]['STYLE_DESCRIPTION'] = $val['STYLE_DESCRIPTION'];
    $pr_data_arr[$val['FLOOR_ID']][$val['SEWING_LINE']][$val['PO_BREAK_DOWN_ID']][$val['ITEM_NUMBER_ID']]['JOB_NO_MST'] = $val['JOB_NO_MST'];

    // $running_hour[$val['FLOOR_ID']][$val['SEWING_LINE']][$val['PO_BREAK_DOWN_ID']][$val['ITEM_NUMBER_ID']][$val['PROD_HOUR']] =$val['PROD_HOUR'] ;

    $pr_data_arr[$val['FLOOR_ID']][$val['SEWING_LINE']][$val['PO_BREAK_DOWN_ID']][$val['ITEM_NUMBER_ID']]['PO_BREAK_DOWN_ID'] = $val['PO_BREAK_DOWN_ID'];
    $po_wise_style_array[$val['PO_BREAK_DOWN_ID']] = $val['STYLE_REF_NO'];
    $style_array[$val['STYLE_REF_NO']] = $val['STYLE_REF_NO'];
    //  $order_wise_job_arr[$val['PO_BREAK_DOWN_ID']] = $val['JOB_NO_MST'];
  }
  // echo "<pre>";
  // print_r($running_hour);

  foreach ($po_working_hr_arr as $floor_id => $line_array) 
  {
    foreach ($line_array as $line_id => $po_arr) 
    {
      foreach ($po_arr as $po_id => $po_wo_hr) 
      {
        $style_no = $po_wise_style_array[$po_id] ; 
        $style_wise_wo_hr_arr [$floor_id][$line_id][$style_no] += $po_wo_hr;
      }
    }
  }

  //==========================================================================================================================>
  // for HOURLY  TARGET Query
  $Sql_Hour = "SELECT a.working_hour ,b.mast_dtl_id , b.adjust_hour,b.ot_emp_brk_dwn,  c.floor_id,c.id as line_id ,b.remarks,b.style_ref_no from prod_resource_dtls_mast a , prod_resource_smv_adj b ,prod_resource_mst c WHERE 
  a.id = b.mast_dtl_id 
  And b.mst_id = c.id 
  and b.pr_date = '$txt_date_from' 
  AND b.adjustment_source = 1
  AND c.id  in($line_cond_id) " ;
  //echo  $Sql_Hour;
  $hourly_trg_arr =array();
  $ot_emp_category_arr =array(0=>"Size Set",1=>"Input",2=>"Output",3=>"QI",4=>"Button Stich",5=>"Input Thread Cut to Btn",6=>"Output Iron to Poly",7=>"Assort/Sewing");
  $ot_emp_category_active_arr =array();
  $ot_emp_dwn_value=array();
  $valueArr = array();
  foreach(sql_select($Sql_Hour) as $rows)
  {  
      $hourly_trg_arr[$rows['FLOOR_ID']][$rows['LINE_ID']]['GEN_HOUR'] = $rows['WORKING_HOUR'] ;
      $hourly_trg_arr[$rows['FLOOR_ID']][$rows['LINE_ID']]['OT_HOUR'] = $rows['ADJUST_HOUR'] ;
      $remarks_arr[$rows['FLOOR_ID']][$rows['LINE_ID']][$rows['STYLE_REF_NO']]['REMARKS'] = $rows['REMARKS'] ;
      $ot_emp_dwn =  explode("__",$rows['OT_EMP_BRK_DWN']);
      
      foreach($ot_emp_dwn as $key=>$val)
      {
          if($val !="")
          {
              $ot_emp_category_active_arr[$rows['FLOOR_ID']][$key] = $val ;
              
          }
      }
      foreach($ot_emp_category_arr as $k=>$v)
      { 
          $ot_emp_dwn_value[$rows['FLOOR_ID']][$rows['LINE_ID']][$rows['STYLE_REF_NO']][$k] = $ot_emp_dwn[$k];
          $valueArr[$rows['FLOOR_ID']][$k] += $ot_emp_dwn[$k];
          
          
      }
      
    //  echo"<pre>";print_r( $ot_emp_dwn);
  }
  // echo "<pre>";
  // print_r($remarks_arr);
    
  //=======================================================hourly calculation ends =======================>


  //=============================== WIP STARTS=============================================================>

  $sew_sql="SELECT a.sewing_line,a.floor_id,a.po_break_down_id as po_id,a.production_type as prod_type, b.production_qnty
  from 
  pro_garments_production_mst a,
  pro_garments_production_dtls b,gbl_temp_engine tmp
  where a.po_break_down_id=tmp.ref_val and a.production_type in (4,5) and tmp.user_id=$user_id and tmp.entry_form=153 and tmp.ref_from=1 
  and a.id=b.mst_id  
  AND a.production_date <= '$txt_date_from' 
  and a.status_active=1 
  and a.is_deleted=0 
  and b.status_active=1 
  AND a.sewing_line in($line_cond_id)
  and b.is_deleted=0 ";
  // echo $sew_sql ;die;
  $sew_sql_res = sql_select($sew_sql);	
  
  $prod_qty_arr = array();
  foreach($sew_sql_res as $v)
  {
    $prod_qty_arr[$v['FLOOR_ID']][$v['SEWING_LINE']][$v['PO_ID']][$v['PROD_TYPE']] += $v['PRODUCTION_QNTY'];
  }
  // pre ($prod_qty_arr);die;
  foreach ($prod_qty_arr as $floor_id => $line_array) 
  {
    foreach ($line_array as $line_id => $po_arr) 
    {
      foreach ($po_arr as $po_id => $v) 
      {
        $style_no = $po_wise_style_array[$po_id] ; 
        $style_no = trim($style_no);;
        $style_wise_prod_arr [$floor_id][$line_id][$style_no]['SEW_IN']  += $v[4];
        $style_wise_prod_arr [$floor_id][$line_id][$style_no]['SEW_OUT'] += $v[5];
      }
    }
  }  

  //=============================== WIP ENDS HERE =============================================================>


  //=============================== OPERATION BULETIN HERE =============================================================>
  $style_cond = where_con_using_array($style_array,1,'a.style_ref');
  $buletin_sql ="SELECT a.style_ref,a.total_smv FROM ppl_gsd_entry_mst a where status_active=1 and is_deleted=0 $style_cond ";
  // echo $buletin_sql; die;
  foreach(sql_select($buletin_sql) as $v)
  {
    $style_wise_buletin_array [$v['STYLE_REF']] = $v['TOTAL_SMV'];
  }
  // pre($style_wise_buletin_array);die;
  //=============================== OPERATION BULETIN END HERE =============================================================>


  //===============================DAY'S RUNS STARTS HERE =====================================================>
  $po_active_sql="SELECT sewing_line,production_date , production_type ,po_break_down_id 
  from
  pro_garments_production_mst
  where production_type=5 
  AND company_id = $company_id 
  AND sewing_line in($line_cond_id)
  group by  sewing_line,production_date,production_type,po_break_down_id ";
  // echo  $po_active_sql ;
  $active_days_arr = array();
  foreach(sql_select($po_active_sql) as $vals)
  {
      $prod_dates=$vals[csf('production_date')];
      if($duplicate_date_arr[$vals[csf('sewing_line')]][$vals[csf('po_break_down_id')]][$prod_dates]=="")
      {
        $active_days_arr[$vals[csf('sewing_line')]][$vals[csf('po_break_down_id')]]++;
      }
  }
  // echo "<pre>";
  // print_r($active_days_arr);
  //=============================DAY'S RUNS ENDS HERE ========================================================>
  
  $rowspan_arr=array();
  $newData_Arr = array() ; 
  foreach($data_arr as $floor_id => $floor_val)
  {
    foreach($floor_val as $sewing_line=>$sewing_val)
    {
      
        foreach($pr_data_arr[$floor_id][$sewing_line] as $po_id=>$po_val)
        {
            foreach($po_val as $item_id=>$rowsV)
            {  
                // $running_h_arr=  $running_hour[$floor_id][$sewing_line][$po_id][$item_id];
                // $running_hour_val = count($running_h_arr);
                $newData_Arr[$floor_id][$sewing_line][$rowsV['STYLE_REF_NO']]['MAN_POWER'] = $sewing_val['MAN_POWER'];
                $newData_Arr[$floor_id][$sewing_line][$rowsV['STYLE_REF_NO']]['LINE_NUMBER'] = $sewing_val['LINE_NUMBER'];
                $newData_Arr[$floor_id][$sewing_line][$rowsV['STYLE_REF_NO']]['TARGET_EFFICIENCY'] = $sewing_val['TARGET_EFFICIENCY'];
    
                $newData_Arr[$floor_id][$sewing_line][$rowsV['STYLE_REF_NO']]['GEN_HOUR'] =  $hourly_trg_arr[$floor_id][$sewing_line]['GEN_HOUR'] ;
                $newData_Arr[$floor_id][$sewing_line][$rowsV['STYLE_REF_NO']]['OT_HOUR'] =  $hourly_trg_arr[$floor_id][$sewing_line]['OT_HOUR']  ;
                $newData_Arr[$floor_id][$sewing_line][$rowsV['STYLE_REF_NO']]['REMARKS'] =  $remarks_arr[$floor_id][$sewing_line][$rowsV['STYLE_REF_NO']]['REMARKS']  ;
    
                $newData_Arr[$floor_id][$sewing_line][$rowsV['STYLE_REF_NO']]['Pr_Qty'] = $rowsV['Pr_Qty'];
                $newData_Arr[$floor_id][$sewing_line][$rowsV['STYLE_REF_NO']]['BUYER_NAME'] = $rowsV['BUYER_NAME'];
                $newData_Arr[$floor_id][$sewing_line][$rowsV['STYLE_REF_NO']]['STYLE_REF_NO'] = $rowsV['STYLE_REF_NO'];
                $newData_Arr[$floor_id][$sewing_line][$rowsV['STYLE_REF_NO']]['STYLE_DESCRIPTION'] = $rowsV['STYLE_DESCRIPTION'];
                $newData_Arr[$floor_id][$sewing_line][$rowsV['STYLE_REF_NO']]['JOB_NO_MST'] = $rowsV['JOB_NO_MST'];
                $newData_Arr[$floor_id][$sewing_line][$rowsV['STYLE_REF_NO']]['PROD_HOUR'] = $rowsV['PROD_HOUR'];
                $newData_Arr[$floor_id][$sewing_line][$rowsV['STYLE_REF_NO']]['PO_BREAK_DOWN_ID'] = $rowsV['PO_BREAK_DOWN_ID'];  
                $newData_Arr[$floor_id][$sewing_line][$rowsV['STYLE_REF_NO']]['RUNNING_HOUR'] = $running_hour_val;
                
            }
        } 
        
    }
    
  } 
  
  foreach($newData_Arr as $floor_id=>$floor_val)
  { 
  
    foreach($floor_val as $sewing_line=>$style_ref)
    { 
      
      foreach($style_ref as $style_ref=>$data) 
      { 
        $man_power = $data['MAN_POWER'];
        $effi      = $data['TARGET_EFFICIENCY'];
        
        $ot_min       = $style_wise_extra_hr_array[$sewing_line][$style_ref];
        $buletin_smv  = $style_wise_buletin_array[$style_ref];
        $style_wise_working_hr   = $style_wise_wo_hr_arr [$floor_id][$sewing_line][$style_ref];


        $style_wise_working_hour =  ($ot_min/60/$man_power) + $style_wise_working_hr; 

        $hourly_target =   ( ($man_power*60) / $buletin_smv ) *  ($effi/100); 
        $style_wise_target = $hourly_target *  $style_wise_working_hour  ;

        $style_wise_working_hour_line[$sewing_line]  +=  $style_wise_working_hour ;
        $today_target[$sewing_line] +=  $style_wise_target ;

        $rowspan_arr[$sewing_line]++;
      }
    
    }
  
  }

  //=================================== CLEAR TEMP ENGINE ==================================== 
  execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form = 153 and ref_from in(1)");
  oci_commit($con);
  

  ob_start();
  ?>
    <div id="scroll_body">
      <!-- Show or Fetch Data On Table-->
      <h1 style="text-align:center;margin-top:10px; padding:5px;font-size:1.7rem">OT APPROVAL SHEET-(SEWING)</h1>
      <h2 style="text-align:center; padding:1px;font-size:1rem;color:red;margin-bottom:10px"> <?  echo $txt_date_from ?>   </h2>
      <div style="width:1320px;height:400px; overflow-y:scroll">
        <?php   
          $wr_hour = 0 ; 
        
          $i=0;
          $b=0;
          foreach($newData_Arr as $floor_id=>$floor_val)
          { 
              $f =$m=$n=$r= 0;
              $l=0;
              $k=0;
            
              $total_man_power = $total_trg_eff =  $total_today_trg = $total_total_hour_val  =$total_hourly_target=$total_mp = $sub_total_mp =$sub_total_ot_hour=0;
              $bdt =0 ;
              ?>
                <table width="1300" style="padding:10px;" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
                  <thead style="font-weight: bold; background: #dddddd;position:sticky;top:0"> 
                      <!--for colspan starts here-->
                      <?php
                          $j = 0;
                          foreach($ot_emp_category_active_arr[$floor_id] as $key=>$val)
                          {  
                              $j++;
                          }
                      ?>
                      <!--for colspan ends here-->
                      <span style="text-align:center;display:block;font-size:1.2rem; font-weight:bold;border-bottom:2px solid black;width:50px;margin-bottom:20px;margin-top:20px" title="<? echo $floor_id ?>"><? echo $floor_library[$floor_id] ?></span>   
                      <tr>
                        <th rowspan="2">Line No</th>
                        <th  rowspan="2" width="110">Style No</th>
                        <th  rowspan="2"width="110">Buyer</th>
                        <th  rowspan="2"width="110">Description</th>
                        <th  rowspan="2" width="40">MP</th>
                        <th  rowspan="2"width="40" title=" style wise (sewing Input -  sewing Ouput) ">WIP</th>
                        <th  rowspan="2"width="40">Day Run</th>
                        <th  rowspan="2">Target EFF%</th>
                        <th  rowspan="2" title=" (MP*60)/bulletin SMV] * Effi% ">HOURLY TARGET</th>
                        <th  rowspan="2" title=" (total OT Minute/60/MP)+style wise W/hr>>Style popup " width="60">Style Wise W/Hr</th>
                        <th  rowspan="2">W/HR</th>
                        <th  rowspan="2" width="60">Style wise Target</th>
                        <th  rowspan="2">Today Tgt</th>
                        <? if($j) {?>
                        <th colspan="<?  echo $j ?>" style="border-right:4px solid #0000FF !important;border-top:4px solid #0000FF !important;">OT PERSON</th>
                        <?  } ?>
                        <th  rowspan="2" width="40">Total MP</th>
                        <th  rowspan="2" width="40">OT Hr</th>
                        <th  rowspan="2" width="40">Total OT Hr</th>
                        <th width="100" rowspan="2">Remarks</th>
                      </tr>
                      <tr>
                          <?php
                              foreach($ot_emp_category_active_arr[$floor_id] as $key=>$val)
                              {  
                                  ?>
                                    <th width="60"><p><? echo $ot_emp_category_arr[$key];?></p></th>
                              <? 
                              }
                          ?>
                      </tr>
                  </thead>
                  <tbody>
                    <? 
                    //  pre ($style_wise_prod_arr); 
                      $newTotal_mp =  $total_style_wise_target= 0;
                      foreach($floor_val as $sewing_line=>$style_ref)
                      {  
                        foreach($style_ref as $style_ref=>$data) 
                        {   
                          $total_mp=0;
                          $tgt    =0;
                          $man_power = $data['MAN_POWER'];
                          $effi      = $data['TARGET_EFFICIENCY'];
                          
                          $ot_min       = $style_wise_extra_hr_array[$sewing_line][$style_ref];
                          $buletin_smv  = $style_wise_buletin_array[$style_ref];
                          $style_wise_working_hr   = $style_wise_wo_hr_arr [$floor_id][$sewing_line][$style_ref]; 

                          // $style_wise_working_hour =  $data['RUNNING_HOUR'] ;
                          $style_wise_working_hour =  ($ot_min/60/$man_power) + $style_wise_working_hr;
                          $style_wise_working_hour_title = "($ot_min/60/$man_power) + $style_wise_working_hr";
                          $effi_per      = ($effi/100);  
                          $hourly_target = ( ($man_power*60) / $buletin_smv ) * $effi_per;
                          $hourly_target_title =   "( ($man_power*60) / $buletin_smv ) * $effi_per";
                          $style_wise_target = $hourly_target *  $style_wise_working_hour  ;
                          
                          // echo "[$floor_id][$sewing_line][$style_ref]<br>";
                          $prod_arr     = $style_wise_prod_arr[$floor_id][$sewing_line][$style_ref]; 
                          $sew_in_qty   = $prod_arr['SEW_IN'] ;
                          $sew_out_qty  = $prod_arr['SEW_OUT'] ;
                          
                          $wip = $sew_in_qty - $sew_out_qty ;
                          $tgt += $style_wise_target ;
                          // $wr_hour_span += $style_wise_working_hour ;
                          $total_style_wise_target += $style_wise_target ;
                          $total_today_trg += $tgt ;

                          foreach($ot_emp_category_active_arr[$floor_id] as $key=>$val)
                          {  
                              
                              $total_mp +=  $ot_emp_dwn_value[$floor_id][$sewing_line][$style_ref][$key] ;
                              $newTotal_mp += $ot_emp_dwn_value[$floor_id][$sewing_line][$style_ref][$key] ;
                              $total_ot_hour = $data['OT_HOUR'] * $total_mp ;
                          }
                          $bdt =  $total_ot_hour * $ot_avg_rate ;
                        
                          $b++;
                          if ($b%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                          ?>
                          <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_3nd<? echo $b; ?>','<? echo $bgcolor; ?>')" id="tr_3nd<? echo $b; ?>">
                              <? if($f==0)  {?>  
                                <td   rowspan="<? echo $rowspan_arr[$sewing_line]?>" style="font-weight:bold;padding:10px" valign="middle" title="<?=$data['LINE_NUMBER'];?>"><? echo $line_library[$data['LINE_NUMBER']] ; ?></td>
                              <?  $f++ ; } ?>
                              <td style="padding:5px" align="left"><?  echo $data['STYLE_REF_NO'] ?></td>
                              <td align="left"><?  echo  $buyer_arr[$data['BUYER_NAME']] ?></td>
                              <td align="left"><?  echo $data['STYLE_DESCRIPTION'] ;?></td>
                              <? if($l==0)  {?>  
                                <td valign="middle" align="right" rowspan="<? echo $rowspan_arr[$sewing_line]?>" align="right"><? echo $data['MAN_POWER']  ?></td>
                              <?  $l++ ; } ?>
                              <td align="right" title='<?= "Sew In ($sew_in_qty) - Sew Out($sew_out_qty)"  ?>'><? echo $wip ;?></td>
                              <td align="right"><? echo  $active_days_arr[$sewing_line][$data['PO_BREAK_DOWN_ID']] ;  ?></td>
                              <? if($n==0)  {?>  
                                <td  valign="middle" align="right" rowspan="<? echo $rowspan_arr[$sewing_line]?>"><? echo $data['TARGET_EFFICIENCY'] ?>%</td>
                              <?  $n++ ; } ?>
                              <td align="right" title="<?= $hourly_target_title ?>"><?  echo number_format( $hourly_target,2)  ?></td>
                              <td align="right" title="<?=  $style_wise_working_hour_title ?>"><?=  number_format($style_wise_working_hour,2)  ;?></td>
                              <? if($k==0)  {?> 
                                <td valign="middle" align="right" rowspan="<? echo $rowspan_arr[$sewing_line]?>" align="right"><? echo number_format($style_wise_working_hour_line[$sewing_line],2);  ?></td>
                              <?  $k++ ; } ?>
                              <td align="right"><?=  number_format($style_wise_target,2) ?></td>
                              <? if($m==0)  {?> 
                                <td valign="middle" align="right" rowspan="<? echo $rowspan_arr[$sewing_line]?>" align="right"><? echo  number_format($today_target[$sewing_line],2)   ?></td>
                              <?  $m++ ; } ?>
                              <?php
                                  foreach($ot_emp_category_active_arr[$floor_id] as $key=>$val)
                                  {  
                                      ?>
                                        <td align="right" > <? echo $ot_emp_dwn_value[$floor_id][$sewing_line][$style_ref][$key];
                                        ?></td>
                                      
                                      <? 
                                    
                                  }
                              ?>
                              <td align="right"><?  echo $total_mp ?></td>
                              <td align="center"><? echo $data['OT_HOUR']  ?></td>
                              <td align="right"><?  echo $total_ot_hour  ?></td> 
                              
                              <td align="center"   ><? echo $data['REMARKS'] ?></td>
                              
                          </tr>
                    
                          <?php
                            $sub_total_ot_hour += $data['OT_HOUR'] * $total_mp ;
                            
                        }  
                      }  
                      $total_man_power += $data['MAN_POWER'] ;
                      
                      $trg_count = count($data['TARGET_EFFICIENCY']);
                      $total_trg_eff   += (( $data['TARGET_EFFICIENCY'] )/$trg_count) / 8;
                    
                      $total_total_hour_val = ($sub_total_ot_hour/$total_man_power) +8;
                      //echo $total_total_hour_val;
                      
                      $total_hourly_target = $tgt  /  $total_total_hour_val ;
                      $sub_total_mp += $total_mp ;
                    
                      $count ++ ;  
                    ?> 
                    <tr style="background: #dddddd;font-weight:bold">                     
                      <td  style="padding:5px;font-weight:bold;" colspan="4" style="font-weight:bold"> <?  echo $floor_library[$floor_id] ?> Total >>></td>       
                      <td align="right"><?= $total_man_power ?></td>
                      <td></td>
                      <td></td>
                      <td align="right" title="target_eff_avg / 8"><?  echo number_format($total_trg_eff,2) ; ?></td>
                      <td align="right"title="total_target / working_hour"><?  echo number_format($total_hourly_target,2)  ?></td>
                      <td align="right"title="(total_ot_hour / man_power )+8"><? echo number_format($total_total_hour_val,2) ?></td>
                      <td align="right"><? echo number_format($total_total_hour_val,2) ?></td>
                      <td align="right"><? echo number_format($total_style_wise_target,2) ?></td>
                      <td align="right"> <?  echo  number_format($total_today_trg,2)  ; ?></td>
                      <?php
                          foreach($ot_emp_category_active_arr[$floor_id] as $key=>$val)
                          {  
                              ?>
                                <td align="right" style="color:black;font-weight:bold"> <? echo  $valueArr[$floor_id][$key];?></td>
                              
                              <? 

                          
                          }
                      ?>
                      <td align ="right"> <? echo $newTotal_mp   ?></td>
                      <td></td>
                      <td align="right"><?  echo $sub_total_ot_hour ?></td>
                      <td align="right"> BDT &#2547; <? echo $bdt  ?></td>
                      
                    </tr> 
                  </tbody>
                </table>
              <?
          } 
        ?> 
      </div>
    </div>
  <?
  // Show or Fetch Data On table ends section 
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
// Show button ends here or generate_report 
?>