<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "")
	header("location:login.php");

require_once('../../../includes/common.php');
$user_name = $_SESSION['logic_erp']['user_id'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];



$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");

/*
|------------------------------------------------------------------------
| for job_no_search_popup
|------------------------------------------------------------------------
*/
if ($action == "job_no_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
    <script>
        var selected_id = new Array;
        var selected_name = new Array;

        /* function check_all_data()
         {
         var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
         tbl_row_count = tbl_row_count - 1;

         for (var i = 1; i <= tbl_row_count; i++)
         {
         $('#tr_' + i).trigger('click');
         }
         }

         function toggle(x, origColor) {
         var newColor = 'yellow';
         if (x.style) {
         x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
         }
         }

         function js_set_value_job(str) {

         if (str != "")
         str = str.split("_");

         toggle(document.getElementById('tr_' + str[0]), '#FFFFCC');

         if (jQuery.inArray(str[1], selected_id) == -1) {
         selected_id.push(str[1]);
         selected_name.push(str[2]);

         } else {
         for (var i = 0; i < selected_id.length; i++) {
         if (selected_id[i] == str[1])
         break;
         }
         selected_id.splice(i, 1);
         selected_name.splice(i, 1);
         }
         var id = '';
         var name = '';
         for (var i = 0; i < selected_id.length; i++) {
         id += selected_id[i] + ',';
         name += selected_name[i] + '*';
         }

         id = id.substr(0, id.length - 1);
         name = name.substr(0, name.length - 1);

         $('#hide_job_no').val(id);
         $('#hide_job_id').val(name);
     }*/


     function js_set_value_job(str) {
            //alert(str);
            $('#hide_job_no').val(str);
            parent.emailwindow.hide();
        }
    </script>

    </head>

    <body>
        <div align="center">
            <form name="styleRef_form" id="styleRef_form">
                <fieldset style="width:780px;">
                    <table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center"
                    class="rpt_table" id="tbl_list">
                    <thead>
                        <th>PO Company</th>
                        <th>Search By</th>
                        <th id="search_by_td_up" width="170">Please Enter Sales No</th>
                        <th>Booking Date</th>
                        <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;"></th>
                        <input type="hidden" name="hide_job_no" id="hide_job_no" value=""/>
                        <input type="hidden" name="hide_job_id" id="hide_job_id" value=""/>
                    </thead>
                    <tbody>
                        <tr>
                            <td align="center">
                            <?

                            echo create_drop_down("cbo_buyer_name", 140, "select buy.id, buy.company_name from  lib_company buy where buy.status_active =1 and buy.is_deleted=0   order by buy.company_name", "id,company_name", 1, "-- All--", 0, "", 0);
                            ?>
                        </td>
                        <td align="center">
                            <?
                            $search_by_arr = array(1 => "Sales No", 2 => "Style Ref", 3 => "Booking No");
                            $dd = "change_search_event(this.value, '0*0*0', '0*0*0', '../') ";
                            echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
                            ?>
                        </td>
                        <td align="center" id="search_by_td">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
                            id="txt_search_common"/>
                        </td>
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"
                            style="width:70px" readonly>To
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"
                            readonly>
                        </td>
                        <td align="center">
                            <input type="button" name="button" class="formbutton" value="Show"
                            onClick="show_list_view('<? echo $companyID; ?>' + '**' + document.getElementById('cbo_buyer_name').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value, 'create_job_no_search_list_view', 'search_div', 'dyeing_production_and_revenue_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');"
                            style="width:100px;"/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
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
    <?
    exit();
}

/*
|------------------------------------------------------------------------
| for create_job_no_search_list_view
|------------------------------------------------------------------------
*/
if ($action == "create_job_no_search_list_view")
{
	$data = explode('**', $data);
	$company_id = $data[0];

	if ($data[1] == 0) {
		if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
			if ($_SESSION['logic_erp']["buyer_id"] != "")
				$buyer_id_cond = " and a.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			else
				$buyer_id_cond = "";
		} else {
			$buyer_id_cond = "";
		}
	} else {
		$buyer_id_cond = " and a.buyer_id=$data[1]";
	}

	$search_by = $data[2];
	$search_string = "%" . trim($data[3]) . "%";

	if ($search_by == 1)
		$search_field = "a.job_no";
	else if ($search_by == 2)
		$search_field = "a.style_ref_no";
	else
		$search_field = "a.sales_booking_no";

	$start_date = $data[4];
	$end_date = $data[5];

	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$date_cond = "and a.booking_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd") . "'";
		} else {
			$date_cond = "and a.booking_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
		}
	} else {
		$date_cond = "";
	}

	$arr = array(0 => $company_library, 1 => $company_library);
	if ($db_type == 0)
		$year_field = "YEAR(a.insert_date) as year";
	else if ($db_type == 2)
		$year_field = "to_char(a.insert_date,'YYYY') as year";
	else
		$year_field = ""; //defined Later

	$sql = "select a.id, a.job_no, $year_field, a.company_id, a.buyer_id, a.style_ref_no,a.booking_date,a.sales_booking_no from  fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0  and a.company_id=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond order by a.id, a.booking_date";
	// echo $sql;	die;
	echo create_list_view("tbl_list_search", "Company,Buyer/Unit,Year,Sales No,Style Ref., Booking No, Booking Date", "120,120,50,110,120,120,80", "800", "220", 0, $sql, "js_set_value_job", "id,job_no", "", 1, "company_id,buyer_id,0,0,0,0,0", $arr, "company_id,buyer_id,year,job_no,style_ref_no,sales_booking_no,booking_date", "", '', '0,0,0,0,0,0,3', '');
	exit();
}


/*
|------------------------------------------------------------------------
| for report_generate
|------------------------------------------------------------------------
*/
if ($action == "report_generate")
{
    $started = microtime(true);
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$presentationType = str_replace("'", "", $presentationType);
	$companyId = str_replace("'", "", $cbo_company_name);

	$sales_no_cond = "";
	if (str_replace("'", "", $txt_sales_no) != "")
	{
		$chk_prefix_sales_no=explode("-",str_replace("'", "", $txt_sales_no));
		if($chk_prefix_sales_no[3]!="")
		{
			$sales_number = "%" . trim(str_replace("'", "", $txt_sales_no));
			$sales_no_cond = " AND D.JOB_NO LIKE '".$sales_number."'";
		}
		else
		{
			$sales_number = trim(str_replace("'", "", $txt_sales_no));
			$sales_no_cond = " AND D.JOB_NO_PREFIX_NUM = '".$sales_number."'";
		}
	}
	
		
	//for date
	if (str_replace("'", "", trim($txt_date_from)) != "" && str_replace("'", "", trim($txt_date_to)) != "")
	{
		$date_cond = " AND B.PRODUCTION_DATE BETWEEN " . trim($txt_date_from) . " AND " . trim($txt_date_to) . "";
		$date_cond2 = " AND F.PRODUCTION_DATE BETWEEN " . trim($txt_date_from) . " AND " . trim($txt_date_to) . "";

        $date1=date_create(str_replace("'", "", trim($txt_date_from)));
        $date2=date_create(str_replace("'", "", trim($txt_date_to)));
        $diff=date_diff($date1,$date2);
        $no_of_days = 1+$diff->format("%a");
	
	}
	else
	{
		$date_cond = "";
		$date_cond2 = "";
	
	}


	/* $sql1 = "SELECT  A.ID AS MACHINE_ID, A.MACHINE_NO ,A.PROD_CAPACITY
    FROM   LIB_MACHINE_NAME A, PRO_FAB_SUBPROCESS B WHERE A.ID=B.MACHINE_ID AND
    A.COMPANY_ID='$companyId' $date_cond $machineIdCond AND B.SERVICE_SOURCE IN(1)  
    AND B.ENTRY_FORM in(35) AND  B.LOAD_UNLOAD_ID=2   
    AND B.RESULT=1 AND  B.STATUS_ACTIVE=1 AND B.IS_DELETED=0  
    GROUP BY A.ID, A.MACHINE_NO ,B.RESULT, A.PROD_CAPACITY ORDER BY A.MACHINE_NO ASC "; */

    $sql = "SELECT  A.ID AS MACHINE_ID, A.MACHINE_NO ,A.PROD_CAPACITY
    FROM   LIB_MACHINE_NAME A WHERE 
    A.COMPANY_ID='$companyId' AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND A.CATEGORY_ID=2 
    GROUP BY A.ID, A.MACHINE_NO, A.PROD_CAPACITY ORDER BY A.MACHINE_NO ASC ";

	//echo $sql;
	//die;
	$nameArray = sql_select($sql);
	if(empty($nameArray))
	{
		echo get_empty_data_msg();
		die;
	}
	

	$print_data = array();
	$machine_ids_arr = array();
	$machine_ids_arr1 = array();
	foreach ($nameArray as $row) 
	{

		$print_data[$row['MACHINE_ID']][$row['MACHINE_NO']]['machine_no'] = $row['MACHINE_NO'];
		$print_data[$row['MACHINE_ID']][$row['MACHINE_NO']]['prod_capacity'] = $row['PROD_CAPACITY'];
		
		if($duplicate_check[$row['MACHINE_ID']] == '')
		{
			$duplicate_check[$row['MACHINE_ID']] = $row['MACHINE_ID'];
			array_push($machine_ids_arr, $row['MACHINE_ID']);
			array_push($machine_ids_arr1, $row['MACHINE_ID']);
		}

	}
	unset($nameArray);
	// echo "<pre>";
	// print_r($print_data);	
	// echo "</pre>";
   
/* 
    $sql_batch1 = "SELECT  A.BATCH_NO, A.BATCH_WEIGHT, A.ID AS BATCH_ID,  (B.BATCH_QNTY) AS BATCH_QNTY,  B.PO_ID, B.PROD_ID, B.WIDTH_DIA_TYPE, D.JOB_NO_PREFIX_NUM, D.BUYER_ID, D.PO_BUYER, F.REMARKS, F.SHIFT_NAME, F.FLOOR_ID,F.MULTI_BATCH_LOAD_ID, F.HOUR_UNLOAD_METER, F.WATER_FLOW_METER, F.END_MINUTES, F.MACHINE_ID, F.LOAD_UNLOAD_ID, F.FABRIC_TYPE, F.RESULT  
    FROM PRO_BATCH_CREATE_DTLS B, FABRIC_SALES_ORDER_MST D, PRO_FAB_SUBPROCESS F, PRO_BATCH_CREATE_MST A 
    WHERE F.BATCH_ID=A.ID  AND A.ENTRY_FORM=0  AND A.ID=B.MST_ID AND F.BATCH_ID=B.MST_ID AND A.company_id='$companyId' $date_cond2 $sales_no_cond AND F.ENTRY_FORM=35 AND F.LOAD_UNLOAD_ID=2 AND A.BATCH_AGAINST IN(1,11,2,3) AND B.PO_ID=D.ID AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND F.STATUS_ACTIVE=1 AND F.IS_DELETED=0 AND A.IS_SALES=1 AND B.IS_SALES=1 AND F.RESULT=1 ".where_con_using_array($machine_ids_arr,0,'F.MACHINE_ID')." ORDER BY F.MACHINE_ID
    UNION ALL 
    SELECT A.BATCH_NO, A.BATCH_WEIGHT, A.ID AS BATCH_ID,   B.BATCH_QNTY AS SUB_BATCH_QNTY,  B.PO_ID, B.PROD_ID, B.WIDTH_DIA_TYPE, D.SUBCON_JOB AS JOB_NO_PREFIX_NUM, D.PARTY_ID AS BUYER_ID, D.PARTY_ID AS PO_BUYER, F.REMARKS, F.SHIFT_NAME,F.FLOOR_ID,F.MULTI_BATCH_LOAD_ID, F.HOUR_UNLOAD_METER, F.WATER_FLOW_METER, F.END_MINUTES, F.MACHINE_ID, F.LOAD_UNLOAD_ID, F.FABRIC_TYPE, F.RESULT 
    FROM PRO_BATCH_CREATE_DTLS B, SUBCON_ORD_DTLS C, SUBCON_ORD_MST D, PRO_BATCH_CREATE_MST A, PRO_FAB_SUBPROCESS F
    WHERE F.BATCH_ID=A.ID AND F.BATCH_ID=B.MST_ID  AND A.ENTRY_FORM=36 AND A.ID=B.MST_ID AND A.company_id='$companyId' $date_cond2 AND F.ENTRY_FORM=38 AND F.LOAD_UNLOAD_ID=2 AND A.BATCH_AGAINST IN(1,11,2,3) AND B.PO_ID=C.ID AND D.SUBCON_JOB=C.JOB_NO_MST AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND F.STATUS_ACTIVE=1 AND F.IS_DELETED=0 AND F.RESULT=1 ".where_con_using_array($machine_ids_arr,0,'F.MACHINE_ID')." ORDER BY F.MACHINE_ID";

     */
    $sql_batch = "SELECT  A.BATCH_NO, A.BATCH_WEIGHT, A.ID AS BATCH_ID, F.MACHINE_ID,A.ENTRY_FORM
    FROM PRO_BATCH_CREATE_DTLS B, FABRIC_SALES_ORDER_MST D, PRO_FAB_SUBPROCESS F, PRO_BATCH_CREATE_MST A 
    WHERE F.BATCH_ID=A.ID  AND A.ENTRY_FORM=0  AND A.ID=B.MST_ID AND F.BATCH_ID=B.MST_ID AND A.WORKING_COMPANY_ID='$companyId' $date_cond2 $sales_no_cond AND F.ENTRY_FORM=35 AND F.LOAD_UNLOAD_ID=2 AND A.BATCH_AGAINST IN(1,11,2,3) AND B.PO_ID=D.ID AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND F.STATUS_ACTIVE=1 AND F.IS_DELETED=0 AND A.IS_SALES=1 AND B.IS_SALES=1 AND F.RESULT=1 ".where_con_using_array($machine_ids_arr,0,'F.MACHINE_ID')."
    GROUP BY A.ID, A.BATCH_NO,F.MACHINE_ID,A.ENTRY_FORM, A.BATCH_WEIGHT
    UNION ALL 
    SELECT A.BATCH_NO, A.BATCH_WEIGHT, A.ID AS BATCH_ID, F.MACHINE_ID,A.ENTRY_FORM
    FROM PRO_BATCH_CREATE_DTLS B, SUBCON_ORD_DTLS C, SUBCON_ORD_MST D, PRO_BATCH_CREATE_MST A, PRO_FAB_SUBPROCESS F
    WHERE F.BATCH_ID=A.ID AND F.BATCH_ID=B.MST_ID  AND A.ENTRY_FORM=36 AND A.ID=B.MST_ID AND D.COMPANY_ID='$companyId' $date_cond2 AND F.ENTRY_FORM=38 AND F.LOAD_UNLOAD_ID=2 AND A.BATCH_AGAINST IN(1,11,2,3) AND B.PO_ID=C.ID AND D.SUBCON_JOB=C.JOB_NO_MST AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND F.STATUS_ACTIVE=1 AND F.IS_DELETED=0 AND F.RESULT=1 ".where_con_using_array($machine_ids_arr,0,'F.MACHINE_ID')."  GROUP BY A.ID, A.BATCH_NO,F.MACHINE_ID,A.ENTRY_FORM, A.BATCH_WEIGHT";

    //echo $sql_batch;
  
    //array_merge($sql_batch,$sql_sub_batch);
    //echo $sql_batch;
    $batch_rslt = sql_select($sql_batch);
    $all_batch_id_cond=array();
    $duplicate_batch_id_check=array();
    foreach ($batch_rslt as $row) 
	{
        if($duplicate_batch_id_check[$row['BATCH_ID']] == '')
        {
            $duplicate_batch_id_check[$row['BATCH_ID']] = $row['BATCH_ID'];
            array_push($all_batch_id_cond,$row['BATCH_ID']);
        }
    }

    
    $sql_dyes_cost = "SELECT a.batch_no, a.issue_basis, a.issue_purpose,c.sub_process,b.item_category,sum(b.cons_amount) as dyes_chemical_cost
    from inv_issue_master a, inv_transaction b,dyes_chem_issue_dtls c
    where a.id=b.mst_id and a.id=c.mst_id and b.id=c.trans_id and b.transaction_type=2 and a.entry_form=5 and a.batch_no  is not null and a.issue_purpose in(8,18,53,54,56,57,65,66,69) and a.issue_basis in(5,7) and  b.item_category in (5,6,7) and a.company_id='$companyId' ".where_con_using_array( $all_batch_id_cond,1,'a.batch_no')." 
    group by a.batch_no, a.issue_basis, a.issue_purpose,b.item_category,c.sub_process "; 

    //echo  $sql_dyes_cost;
    $rsl_dyes_cost =sql_select($sql_dyes_cost);

    $tot_dyes_chemical=0;
    foreach($rsl_dyes_cost as $val)
    {
        $sub_process=$val[csf("sub_process")];
        $all_batch_no=explode(",",$val[csf("batch_no")]);
        foreach ($all_batch_no as $key => $batchId) 
        {
            if($sub_process!=92)
            {
                $dyes_chemical_arr[$batchId][$val[csf("item_category")]]['chemical_cost']+=$val[csf("dyes_chemical_cost")];
            }
            else
            {
                $dyes_chemical_arr[$batchId][$val[csf("item_category")]]['chemical_cost_finish']+=$val[csf("dyes_chemical_cost")];
            }
            if ($val[csf("issue_basis")]==7) 
            {
                $chemical_issue_batch_arr[$batchId]['issue_basis']= $receive_basis_arr[$val[csf("issue_basis")]];
                $chemical_issue_batch_arr[$batchId]['issue_purpose']= $yarn_issue_purpose[$val[csf("issue_purpose")]];
            }
        }
    }
    //var_dump($dyes_chemical_arr);
 
    $batch_info_arr = array();
    $duplicate_batch_check = array();
    $duplicate_sub_batch_check = array();
    $tot_inhouse_qty_without_reprocess=0;
    $tot_subcon_qty_without_reprocess=0;
    $first_chemi_cost=0;
    $first_dyeing_cost=0;
    $chemical_cost_finish=0;
    $batch_weight=0;
    foreach ($batch_rslt as $row) 
	{
        if($row['ENTRY_FORM']==0)
        {
            if($duplicate_batch_check[$row['BATCH_ID']] == '')
            {
                $duplicate_batch_check[$row['BATCH_ID']] = $row['BATCH_ID'];
                $batch_info_arr[$row['MACHINE_ID']]['batch_weight'] += $row['BATCH_WEIGHT'];
                $batch_info_arr[$row['MACHINE_ID']]['no_of_batch']++;
               $tot_inhouse_qty_without_reprocess +=$row["BATCH_WEIGHT"];
               $batch_weight += $row['BATCH_WEIGHT'];

               
               $first_chemi_cost=$dyes_chemical_arr[$row['BATCH_ID']][5]['chemical_cost']+$dyes_chemical_arr[$row['BATCH_ID']][7]['chemical_cost'];
               $first_dyeing_cost=$dyes_chemical_arr[$batch_id][6]['chemical_cost'];
               $chemical_cost_finish=$dyes_chemical_arr[$row['BATCH_ID']][5]['chemical_cost_finish']+$dyes_chemical_arr[$row['BATCH_ID']][7]['chemical_cost_finish'];
            }

        }
        else  if($row['ENTRY_FORM']==36)
        {
            if($duplicate_sub_batch_check[$row['BATCH_ID']] == '')
            {
                $duplicate_sub_batch_check[$row['BATCH_ID']] = $row['BATCH_ID'];
                $batch_info_arr[$row['MACHINE_ID']]['batch_weight'] += $row['BATCH_WEIGHT'];
                $batch_info_arr[$row['MACHINE_ID']]['no_of_batch']++;
                $tot_subcon_qty_without_reprocess+=$row["BATCH_WEIGHT"];
                $batch_weight += $row['BATCH_WEIGHT'];

                $first_chemi_cost=$dyes_chemical_arr[$row['BATCH_ID']][5]['chemical_cost']+$dyes_chemical_arr[$row['BATCH_ID']][7]['chemical_cost'];
                $first_dyeing_cost=$dyes_chemical_arr[$row['BATCH_ID']][6]['chemical_cost'];
                $chemical_cost_finish=$dyes_chemical_arr[$row['BATCH_ID']][5]['chemical_cost_finish']+$dyes_chemical_arr[$row['BATCH_ID']][7]['chemical_cost_finish'];
            }
        }

      
	}
	unset($batch_rslt);
    //ar_dump($chemical_cost_finish);
    

	if ($presentationType == 1)
	{
		ob_start();
		?>
		<style>
			.cls_break td{
				word-break:break-all; 
			}
			
			.cls_tot{
				text-align:right;
				font-weight:bold;					
			}
		</style>
		<fieldset style="width:100%;">
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td align="center" width="100%" style="font-size:16px">
					<strong>Daily Dyeing Production And Revenue Report</strong></td>
				</tr>
                <tr class="form_caption" style="border:none;">
                    <td colspan="12" align="center" style="border:none;font-size:16px; font-weight:bold; padding: 0px 2px;" ><? echo $company_library[$companyId]; ?></td>
                </tr>
                <tr style="border:none;">
                    <td colspan="12" align="center" style="border:none; font-size:14px; padding: 3px 2px;">
                        <strong>Date Range : <? echo $txt_date_from; ?> To <? echo $txt_date_to; ?></strong>
                    </td>
                </tr>
			</table>
          
            <table cellspacing="0" cellpadding="3" border="1" rules="all"  class="rpt_table" id="scroll_body">
				
                    <tr>
                        <td style="font: 16px tahoma;">Details</td>
                    </tr>
                    <tr>
                        <td width="100" style="font: 14px tahoma;" bgcolor="#E9F3FF">M/C NO.</td>
                        <? 
                        foreach($print_data as $m_id=>$m_no)
                        { 
                            foreach ($m_no as $key => $rows) 
                            { ?>
                                <td align="center" width="100" bgcolor="#E9F3FF">
                                <?
                                    $data = explode('-',$rows['machine_no']);
                                    $sliced = array_slice($data, 0, -1);
                                    $string = implode("-", $sliced); 
                                  
                                    echo  rtrim($string,'-');
                                   //echo $rows['machine_no']; 
                                    ?>
                                </td>
                            <? } 
                        }?>
                   </tr>
                   <tr>
                        <td width='100' style="font: 14px tahoma;">M/C Name</td>
                        <? 
                        foreach($print_data as $m_id=>$m_no)
                        { 
                            foreach ($m_no as $key => $rows) 
                            { ?>
                                <td align="center" width="100">
                                    <?
                                    $data = explode('-',$rows['machine_no']);
                                    $lindex = count($data)-1;
                                    echo  $data[$lindex];
                                    ?>
                                </td>
                            <? } 
                        }?>
                   </tr>
                   <tr>
                        <td width="100" style="font: 14px tahoma;" bgcolor="#E9F3FF">M/C Capacity</td>
                        <? 
                        foreach($print_data as $m_id=>$m_no)
                        { 
                            foreach ($m_no as $key => $rows) 
                            { ?>
                                <td align="center" width="100" bgcolor="#E9F3FF">
                                    <?=number_format($rows['prod_capacity'],2);
                                     $tot_prod_capacity +=$rows['prod_capacity'];
                                    ?>
                                </td>
                            <? } 
                        }?>
                   </tr>
                   <tr>
                         <td width="100" style="font: 14px tahoma;">Loading Capacity</td>
                         <? 
                        foreach($print_data as $m_id=>$m_no)
                        { 
                            foreach ($m_no as $key => $rows) 
                            { ?>
                                <td align="center" width="100">
                                    <?
                                    $lcapacity = $rows['prod_capacity']*(80/100);
                                    echo number_format($lcapacity,2);
                                    $tot_lcapacity += $lcapacity;
                                    ?>
                                </td>
                            <? } 
                        }?>
                   </tr>
                   <tr>
                    
                        <td width="100" style="font: 14px tahoma;" bgcolor="#E9F3FF">No.of Batch</td>
                        <? 
                        foreach($print_data as $m_id=>$m_no)
                        { 
                            foreach ($m_no as $key => $rows) 
                            { ?>
                                <td align="center" width="100" bgcolor="#E9F3FF">
                                    <?=number_format($batch_info_arr[$m_id]['no_of_batch'],2);?>
                                </td>
                            <? } 
                        }?>
                       
                   </tr>
                   <tr>
                        <td width="100" style="font: 14px tahoma;">Fresh Production</td>
                        <? 
                        foreach($print_data as $m_id=>$m_no)
                        { 
                            foreach ($m_no as $key => $rows) 
                            {    ?>
                          
                                <td align="center" width="100">
                                    <?= number_format($batch_info_arr[$m_id]['batch_weight'],2);
                                         $tot_production +=$batch_info_arr[$m_id]['batch_weight'];
                                        $r_span +=count($m_id);
                                    ?>
                                </td>
                            <? } 
                        }
                        
                        ?>
                   </tr>
                   <tr>
                    <td></td>
                        <td colspan="<?=$r_span?>" align="right" style="font: 14px tahoma;"><? echo 'Total Prodduction (kgs) : '.number_format($tot_production,2)?></td>
                   </tr>
				
			
			</table>
            <br>

            <div>
                <table cellpadding="0" width="300" cellspacing="0" align="left" class="rpt_table" rules="all" border="1">
                    <thead>
                        <tr>
                            <th colspan="2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr bgcolor="#E9F3FF" >
                            <td style="font: 14px tahoma;">100% Load. Capacity/Day : </td>
                            <td align="right" style="font: 14px tahoma;"><? echo number_format($tot_prod_capacity,2).' kgs';?></td>
                        </tr>
                    </tbody>
                </table>
                    

                <table cellpadding="0" width="3" cellspacing="0" align="left" rules="all" border="1">
                    <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
                </table>

                <table cellpadding="0" width="300" cellspacing="0" align="left" class="rpt_table" rules="all" border="1">
                    <thead>
                        <tr>
                            <th colspan="2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr bgcolor="#E9F3FF" >
                            <td style="font: 14px tahoma;">80% Load. Capacity/Day :	</td>
                            <td align="right" style="font: 14px tahoma;"><? echo number_format($tot_lcapacity,2).' kgs';?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 70px">
                <table cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td align="left" width="100%" style="font: 16px tahoma;">
                        <strong><u>Dyeing Production Summary</u></strong></td>
                        <td></td>
                    </tr>
                </table>
                <br>
                <table cellpadding="0" width="300" cellspacing="0" align="left" class="rpt_table" rules="all" border="1">
                    <thead>
                        <tr>
                            <th colspan="2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr bgcolor="#E9F3FF" >
                            <td style="font: 14px tahoma;">Inhose </td>
                            <td align="right" style="font: 14px tahoma;">
                            <? echo number_format($tot_inhouse_qty_without_reprocess,2,'.','');
                                $total_in_out_subc_qty+= number_format($tot_inhouse_qty_without_reprocess,2,'.','');
                            ?>
                             </td>
                        </tr>
                        <tr bgcolor="#E9F3FF" >
                            <td style="font: 14px tahoma;">Inbound Subcontract </td>
                            <td align="right" style="font: 14px tahoma;"> 
                            <? echo number_format($tot_subcon_qty_without_reprocess,2,'.',''); 
                                 $total_in_out_subc_qty+= number_format($tot_subcon_qty_without_reprocess,2,'.','');
                            ?>
                            </td>
                        </tr>
                        <tr bgcolor="#E9F3FF" >
                            <td style="font: 14px tahoma;"><b>Total : </b> </td>
                            <td align="right" style="font: 14px tahoma;"> <b><? echo number_format($total_in_out_subc_qty,2,'.','');?> 	</b></td>
                        </tr>
                    </tbody>
                        
                </table>
                     
                <table cellpadding="0" width="3" cellspacing="0" align="left" rules="all" border="1">
                    <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
                </table>

                <?

                $txt_date_from=str_replace("'", "", trim($txt_date_from));
                $txt_date_to=str_replace("'", "", trim($txt_date_to));

                $txt_date_from=$txt_date_to;

                //if (str_replace("'", "", trim($txt_date_from)) != "" && str_replace("'", "", trim($txt_date_to)) != "")

                if($txt_date_from!="")
                {
                  
                    if($txt_date_to=="") $txt_date_to=$txt_date_from;
                    $date_distance=datediff("d",$txt_date_from, $txt_date_to);
                    $month_name=date('F',strtotime($txt_date_from));
                    $year_name=date('Y',strtotime($txt_date_from));
                    $day_of_month=explode("-",$txt_date_from);
                    if($db_type==0)
                    {
                        $fist_day_of_month=$day_of_month[2]*1;
                    }
                    else
                    {
                        $fist_day_of_month=$day_of_month[0]*1;
                    }

                    if($date_distance==1 && $fist_day_of_month>1)
                    {
                        $query_cond_month=date('m',strtotime($txt_date_from));
                        $query_cond_year=date('Y',strtotime($txt_date_from));
                        $sql_cond="";

                        if($db_type==0) $sql_cond="  AND month(F.PRODUCTION_DATE)='$query_cond_month' and year(F.PRODUCTION_DATE)='$query_cond_year'"; else $sql_cond="  and to_char(F.PRODUCTION_DATE,'mm')='$query_cond_month' and to_char(F.PRODUCTION_DATE,'yyyy')='$query_cond_year'";
                        
                        $sql_montyly_inhouse = "SELECT  A.BATCH_NO, A.BATCH_WEIGHT, A.ID AS BATCH_ID
                        FROM PRO_BATCH_CREATE_DTLS B, FABRIC_SALES_ORDER_MST D, PRO_FAB_SUBPROCESS F, PRO_BATCH_CREATE_MST A 
                        WHERE F.BATCH_ID=A.ID  AND A.ENTRY_FORM=0  AND A.ID=B.MST_ID AND F.BATCH_ID=B.MST_ID AND D.COMPANY_ID='$companyId' AND F.ENTRY_FORM=35 AND F.LOAD_UNLOAD_ID=2 AND A.BATCH_AGAINST IN(1,11,2,3) AND B.PO_ID=D.ID AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND F.STATUS_ACTIVE=1 AND F.IS_DELETED=0 AND A.IS_SALES=1 AND B.IS_SALES=1 AND F.RESULT=1 AND F.PRODUCTION_DATE<'".$txt_date_from."' $sql_cond ".where_con_using_array($machine_ids_arr,0,'F.MACHINE_ID')."  GROUP BY A.ID, A.BATCH_NO, A.BATCH_WEIGHT";

                        $sql_montyly_inbount = "SELECT A.BATCH_NO, A.BATCH_WEIGHT, A.ID AS BATCH_ID
                        FROM PRO_BATCH_CREATE_DTLS B, SUBCON_ORD_DTLS C, SUBCON_ORD_MST D, PRO_BATCH_CREATE_MST A, PRO_FAB_SUBPROCESS F
                        WHERE F.BATCH_ID=A.ID AND F.BATCH_ID=B.MST_ID  AND A.ENTRY_FORM=36 AND A.ID=B.MST_ID AND D.COMPANY_ID='$companyId' AND F.ENTRY_FORM=38 AND F.LOAD_UNLOAD_ID=2 AND A.BATCH_AGAINST IN(1,11,2,3) AND B.PO_ID=C.ID AND D.SUBCON_JOB=C.JOB_NO_MST AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND F.STATUS_ACTIVE=1 AND F.IS_DELETED=0 AND F.RESULT=1 AND F.PRODUCTION_DATE<'".$txt_date_from."' $sql_cond ".where_con_using_array($machine_ids_arr,0,'F.MACHINE_ID')."  GROUP BY A.ID, A.BATCH_NO, A.BATCH_WEIGHT";

                        //echo  $sql_montyly_inhouse;

                        $rslt_montyly_inhouse =  sql_select($sql_montyly_inhouse);
                        $upto_y_inhouse_produc=0;
                        foreach ($rslt_montyly_inhouse as $row) 
                        {
                           //$upto_y_inhouse_produc +=$batch_product_arr[$row['BATCH_ID']][$row['PROD_ID']];
                           if($dup_byi_chk[$row['BATCH_ID']]=='')
                           {
                                $dup_byi_chk[$row['BATCH_ID']] = $row['BATCH_ID'];
                                $upto_y_inhouse_produc +=$row['BATCH_WEIGHT'];
                           }
                          
                        }

                        $rslt_montyly_inbount =  sql_select($sql_montyly_inbount);
                        $upto_y_inbount_produc=0;
                        foreach ($rslt_montyly_inbount as $row) 
                        {
                          
                           if($dup_byib_chk[$row['BATCH_ID']]=='')
                           {
                                $dup_byib_chk[$row['BATCH_ID']] = $row['BATCH_ID'];
                                $upto_y_inbount_produc +=$row['BATCH_WEIGHT'];
                           }
                        }
                    }
                
                    ?>       
                    <table cellpadding="0" width="500" cellspacing="0" align="left" class="rpt_table" rules="all" border="1">
                        <thead>
                            <tr>
                                <th colspan="2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr bgcolor="#E9F3FF" >
                                <td style="font: 14px tahoma;">Upto yesterday Production of &nbsp;<? echo $month_name; ?>-<? echo $year_name; ?> [Inhouse]	 </td>
                                <td align="right" style="font: 14px tahoma;">
                                <? echo number_format($upto_y_inhouse_produc,2); 
                                 $tot_y_production +=$upto_y_inhouse_produc;
                                ?>	</td>
                            </tr>
                            <tr bgcolor="#E9F3FF" >
                                <td style="font: 14px tahoma;">Upto yesterday Production of &nbsp;<? echo $month_name; ?>-<? echo $year_name; ?> [Inbound Subcontract]	 </td>
                                <td align="right" style="font: 14px tahoma;">
                                <? echo number_format($upto_y_inbount_produc,2);
                                 $tot_y_production +=$upto_y_inbount_produc;
                                ?></td>
                            </tr>
                            <tr bgcolor="#E9F3FF" >
                                <td style="font: 14px tahoma;"><b>Total Production [Inhouse + Inbound Subcomntract]	 : </b> </td>
                                <td align="right" style="font: 14px tahoma;"><b><? echo number_format($tot_y_production,2);?></b></td>
                            </tr>
                        </tbody>
                    </table>

                    <table cellpadding="0" width="3" cellspacing="0" align="left" rules="all" border="1">
                        <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
                    </table>


                    <table cellpadding="0" width="300" cellspacing="0" align="left" class="rpt_table" rules="all" border="1">
                        <thead>
                            <tr>
                                <th colspan="2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr bgcolor="#E9F3FF" >
                                <td style="font: 14px tahoma;"> Avg. Dyeing Production </td>
                                <td align="right" style="font: 14px tahoma;"> <? echo number_format($tot_y_production/$no_of_days,2);?></td>
                            </tr>
                            <tr bgcolor="#E9F3FF" >
                                <td style="font: 14px tahoma;"> No of Working Days  </td>
                                <td align="right" style="font: 14px tahoma;"><?=$no_of_days;?>	</td>
                            </tr>
                            
                        </tbody>
                    </table>

                <?}?>
            </div>

            <div style="margin-top: 100px">

            <?
            $sql_batch_fc = "SELECT  A.BATCH_NO, A.BATCH_WEIGHT, A.ID AS BATCH_ID, F.MACHINE_ID,A.ENTRY_FORM,F.FABRIC_TYPE,A.COLOR_RANGE_ID, F.PRODUCTION_DATE, D.SALES_BOOKING_NO 
            FROM PRO_BATCH_CREATE_DTLS B, FABRIC_SALES_ORDER_MST D, PRO_FAB_SUBPROCESS F, PRO_BATCH_CREATE_MST A 
            WHERE F.BATCH_ID=A.ID  AND A.ENTRY_FORM=0  AND A.ID=B.MST_ID AND F.BATCH_ID=B.MST_ID AND A.WORKING_COMPANY_ID='$companyId' $date_cond2 $sales_no_cond AND F.ENTRY_FORM=35 AND F.LOAD_UNLOAD_ID=2 AND A.BATCH_AGAINST IN(1,11,2,3) AND B.PO_ID=D.ID AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND F.STATUS_ACTIVE=1 AND F.IS_DELETED=0 AND A.IS_SALES=1 AND B.IS_SALES=1 AND F.RESULT=1  ".where_con_using_array($machine_ids_arr,0,'F.MACHINE_ID')."
            GROUP BY A.ID, A.BATCH_NO,F.MACHINE_ID,A.ENTRY_FORM,F.FABRIC_TYPE,A.COLOR_RANGE_ID,A.BATCH_WEIGHT, F.PRODUCTION_DATE, D.SALES_BOOKING_NO 
            UNION ALL 
            SELECT A.BATCH_NO, A.BATCH_WEIGHT, A.ID AS BATCH_ID, F.MACHINE_ID,A.ENTRY_FORM,F.FABRIC_TYPE,A.COLOR_RANGE_ID, F.PRODUCTION_DATE, NULL AS SALES_BOOKING_NO 
            FROM PRO_BATCH_CREATE_DTLS B, SUBCON_ORD_DTLS C, SUBCON_ORD_MST D, PRO_BATCH_CREATE_MST A, PRO_FAB_SUBPROCESS F
            WHERE F.BATCH_ID=A.ID AND F.BATCH_ID=B.MST_ID  AND A.ENTRY_FORM=36 AND A.ID=B.MST_ID AND D.COMPANY_ID='$companyId' $date_cond2 AND F.ENTRY_FORM=38 AND F.LOAD_UNLOAD_ID=2 AND A.BATCH_AGAINST IN(1,11,2,3) AND B.PO_ID=C.ID AND D.SUBCON_JOB=C.JOB_NO_MST AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND F.STATUS_ACTIVE=1 AND F.IS_DELETED=0 AND F.RESULT=1  ".where_con_using_array($machine_ids_arr,0,'F.MACHINE_ID')."  GROUP BY A.ID, A.BATCH_NO,F.MACHINE_ID,A.ENTRY_FORM,F.FABRIC_TYPE,A.COLOR_RANGE_ID,A.BATCH_WEIGHT, F.PRODUCTION_DATE  
                
           ";

           //echo  $sql_batch_fc;

            $rsl_batch_fc = sql_select($sql_batch_fc);

            $machine_ids_arr = array();
            $duplicate_bid_check = array();
            $f_type_arr = array();
            $c_range_arr = array();
            $fabric_type_arr = array();
            foreach ($rsl_batch_fc as $row) 
            {
                $sales_booking_no = $row['SALES_BOOKING_NO'];
                $salesBookingNo = explode('-',$sales_booking_no);
                //var_dump($salesBookingNo[1]);

                if($duplicate_bid_check[$row['BATCH_ID']] == '')
                {
                    $duplicate_bid_check[$row['BATCH_ID']] = $row['BATCH_ID'];
                    if($salesBookingNo[1] !='SMN')
                    {
                        $batch_arr[$row['FABRIC_TYPE']][$row['COLOR_RANGE_ID']]['batch_weight'] += $row['BATCH_WEIGHT'];
                        //$batch_arr[$row['FABRIC_TYPE']][$row['COLOR_RANGE_ID']]['production_date'] = $row['PRODUCTION_DATE'];
                    }

                    if($duplicate_f_check[$row['FABRIC_TYPE']] == '')
                    {
                        $duplicate_f_check[$row['FABRIC_TYPE']] = $row['FABRIC_TYPE'];
                        $f_type_arr[$row['FABRIC_TYPE']]['fabric_type'] = $row['FABRIC_TYPE'];
                        array_push( $fabric_type_arr, $row['FABRIC_TYPE']);
                    }

                    if($duplicate_cid_check[$row['COLOR_RANGE_ID']] == '')
                    {
                        $duplicate_cid_check[$row['COLOR_RANGE_ID']] = $row['COLOR_RANGE_ID'];
                        if($salesBookingNo[1] !='SMN')
                        {
                            if($row['COLOR_RANGE_ID'] !=0)
                            {
                                //$c_range_arr[$row['COLOR_RANGE_ID']]['color_range_id'] = 'Sample';
                                array_push($c_range_arr,$row['COLOR_RANGE_ID']);
                                //$c_range_arr[$row['COLOR_RANGE_ID']]['color_range_id'] = $row['COLOR_RANGE_ID'];
                               
                            }
                        }
                       
                      
                      
                    }

                    if($salesBookingNo[1] =='SMN')
                    {
                        if($duplicate_smn_check[$salesBookingNo[1]] == '')
                        {
                            $duplicate_smn_check[$salesBookingNo[1]] = $salesBookingNo[1];
                            array_push($c_range_arr,'Sample');
                        }
                        //$c_range_arr[$row['COLOR_RANGE_ID']]['color_range_id'] = 'Sample';
                        $batch_smn_arr[$row['FABRIC_TYPE']]['batch_weight'] += $row['BATCH_WEIGHT'];
                    }
                }
        
            }
            unset($rsl_batch_fc);
            //var_dump($c_range_arr);
          
           

            if ($db_type == 0) 
            {
                $date_cond1 = "and FROM_DATE = '" . change_date_format(date('Y-m-01')) . "' and TO_DATE ='" . change_date_format(date('Y-m-t'), "yyyy-mm-dd") . "'";
            } 
            else 
            {
                $date_cond1 = "and FROM_DATE = '" . change_date_format(date('Y-m-01'), '', '', 1) . "' and TO_DATE ='" . change_date_format(date('Y-m-t'), '', '', 1) . "'";
            }

            // if ($db_type == 0) 
            // {
            //     $date_cond1 = "and FROM_DATE = '" . change_date_format('01-Jun-2022') . "' and TO_DATE ='" . change_date_format('30-Jun-2022', "yyyy-mm-dd") . "'";
            // } 
            // else 
            // {
            //     $date_cond1 = "and FROM_DATE = '" . change_date_format('01-Jun-2022', '', '', 1) . "' and TO_DATE ='" . change_date_format('30-Jun-2022', '', '', 1) . "'";
            // }


            $sql_d_rate_chart = "SELECT COMPANY_ID, FABRIC_TYPE_ID, COLOR_RANGE_ID, RATE, EXCHANGE_RATE, FROM_DATE, TO_DATE 
            FROM LIB_DYEING_RATE_CHART_DTLS WHERE COMPANY_ID='$companyId' AND ENTRY_FORM=540 AND STATUS_ACTIVE=1 AND IS_DELETED=0
            ".where_con_using_array($fabric_type_arr,0,'fabric_type_id')." $date_cond1";
            //echo $sql_d_rate_chart;

            $rsl_d_rate_chart = sql_select($sql_d_rate_chart);
            $dRateChartArr = array();
            $dRateChartSmnArr = array();
            foreach ($rsl_d_rate_chart as $row) 
            {
                //$f_date = explode('-',$row['FROM_DATE']);
                // var_dump( $f_date);
                $dRateChartArr[$row['FABRIC_TYPE_ID']][$row['COLOR_RANGE_ID']]['rate'] = $row['RATE'];
                $dRateChartArr[$row['FABRIC_TYPE_ID']][$row['COLOR_RANGE_ID']]['exchange_rate'] =  $row['EXCHANGE_RATE'];
                $dRateChartArr[$row['FABRIC_TYPE_ID']][$row['COLOR_RANGE_ID']]['amount_tk'] = $row['RATE']*$row['EXCHANGE_RATE'];

                
                $dRateChartSmnArr[$row['FABRIC_TYPE_ID']]['rate'] += $row['RATE'];
                $dRateChartSmnArr[$row['FABRIC_TYPE_ID']]['rate_no']++;

                $dRateChartSmnArr[$row['FABRIC_TYPE_ID']]['exchange_rate'] += $row['EXCHANGE_RATE'];
                $dRateChartSmnArr[$row['FABRIC_TYPE_ID']]['exchange_rate_no']++;

            }
            unset($rsl_d_rate_chart);
            //var_dump($dRateChartSmnArr);
          

         
            $txt_date_from1=str_replace("'", "", trim($txt_date_from));
            $txt_date_to1=str_replace("'", "", trim($txt_date_to));

            $txt_date_from1=$txt_date_to1;

            if($txt_date_from1!="")
            {
              
                if($txt_date_to1=="") $txt_date_to1=$txt_date_fro1;
                $date_distance1=datediff("d",$txt_date_from1, $txt_date_to1);
                $month_name1=date('F',strtotime($txt_date_from1));
                $year_name1=date('Y',strtotime($txt_date_from1));
                $day_of_month1=explode("-",$txt_date_from1);
                if($db_type==0)
                {
                    $fist_day_of_month1=$day_of_month1[2]*1;
                }
                else
                {
                    $fist_day_of_month1=$day_of_month1[0]*1;
                }

                if($date_distance1==1 && $fist_day_of_month1>1)
                {
                    $query_cond_month=date('m',strtotime($txt_date_from1));
                    $query_cond_year=date('Y',strtotime($txt_date_from1));
                    $sql_cond="";

                    if($db_type==0) $sql_cond="  AND month(F.PRODUCTION_DATE)='$query_cond_month' and year(F.PRODUCTION_DATE)='$query_cond_year'"; else $sql_cond="  and to_char(F.PRODUCTION_DATE,'mm')='$query_cond_month' and to_char(F.PRODUCTION_DATE,'yyyy')='$query_cond_year'";


                    $sql_batch_up = "SELECT  A.BATCH_NO, A.BATCH_WEIGHT, A.ID AS BATCH_ID, F.MACHINE_ID,A.ENTRY_FORM,F.FABRIC_TYPE,A.COLOR_RANGE_ID
                    FROM PRO_BATCH_CREATE_DTLS B, FABRIC_SALES_ORDER_MST D, PRO_FAB_SUBPROCESS F, PRO_BATCH_CREATE_MST A 
                    WHERE F.BATCH_ID=A.ID  AND A.ENTRY_FORM=0  AND A.ID=B.MST_ID AND F.BATCH_ID=B.MST_ID AND A.WORKING_COMPANY_ID='$companyId'  $sales_no_cond AND F.ENTRY_FORM=35 AND F.LOAD_UNLOAD_ID=2 AND A.BATCH_AGAINST IN(1,11,2,3) AND B.PO_ID=D.ID AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND F.STATUS_ACTIVE=1 AND F.IS_DELETED=0 AND A.IS_SALES=1 AND B.IS_SALES=1 AND F.RESULT=1 AND F.PRODUCTION_DATE<'".$txt_date_from1."' $sql_cond  ".where_con_using_array($machine_ids_arr1,0,'F.MACHINE_ID')."
                    GROUP BY A.ID, A.BATCH_NO,F.MACHINE_ID,A.ENTRY_FORM, A.BATCH_WEIGHT,F.FABRIC_TYPE,A.COLOR_RANGE_ID
                    UNION ALL 
                    SELECT A.BATCH_NO, A.BATCH_WEIGHT, A.ID AS BATCH_ID, F.MACHINE_ID,A.ENTRY_FORM,F.FABRIC_TYPE,A.COLOR_RANGE_ID
                    FROM PRO_BATCH_CREATE_DTLS B, SUBCON_ORD_DTLS C, SUBCON_ORD_MST D, PRO_BATCH_CREATE_MST A, PRO_FAB_SUBPROCESS F
                    WHERE F.BATCH_ID=A.ID AND F.BATCH_ID=B.MST_ID  AND A.ENTRY_FORM=36 AND A.ID=B.MST_ID AND D.COMPANY_ID='$companyId'  AND F.ENTRY_FORM=38 AND F.LOAD_UNLOAD_ID=2 AND A.BATCH_AGAINST IN(1,11,2,3) AND B.PO_ID=C.ID AND D.SUBCON_JOB=C.JOB_NO_MST AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND F.STATUS_ACTIVE=1 AND F.IS_DELETED=0 AND F.RESULT=1 AND F.PRODUCTION_DATE<'".$txt_date_from1."' $sql_cond  ".where_con_using_array($machine_ids_arr1,0,'F.MACHINE_ID')."  GROUP BY A.ID, A.BATCH_NO,F.MACHINE_ID,A.ENTRY_FORM, A.BATCH_WEIGHT,F.FABRIC_TYPE,A.COLOR_RANGE_ID";
                
                   //echo $sql_batch_up;
                  
                    //array_merge($sql_batch,$sql_sub_batch);
                    //echo $sql_batch;
                    $batch_rslt_up = sql_select($sql_batch_up);
                    $all_batch_id_cond_up=array();
                    $duplicate_batch_id_check=array();
                    foreach ($batch_rslt_up as $row) 
                    {
                        if($duplicate_batch_id_check[$row['BATCH_ID']] == '')
                        {
                            $duplicate_batch_id_check[$row['BATCH_ID']] = $row['BATCH_ID'];
                            array_push($all_batch_id_cond_up,$row['BATCH_ID']);
                            
                        }
                    }
                
                    
                    $sql_dyes_cost_up = "SELECT a.batch_no, a.issue_basis, a.issue_purpose,c.sub_process,b.item_category,sum(b.cons_amount) as dyes_chemical_cost
                    from inv_issue_master a, inv_transaction b,dyes_chem_issue_dtls c
                    where a.id=b.mst_id and a.id=c.mst_id and b.id=c.trans_id and b.transaction_type=2 and a.entry_form=5 and a.batch_no  is not null and a.issue_purpose in(8,18,53,54,56,57,65,66,69) and a.issue_basis in(5,7) and  b.item_category in (5,6,7) and a.company_id='$companyId' ".where_con_using_array( $all_batch_id_cond_up,1,'a.batch_no')." 
                    group by a.batch_no, a.issue_basis, a.issue_purpose,b.item_category,c.sub_process "; 
                
                    //echo  $sql_dyes_cost_up;
                    $rsl_dyes_cost_up =sql_select($sql_dyes_cost_up);
                
                    $tot_dyes_chemical=0;
                    foreach($rsl_dyes_cost_up as $val)
                    {
                        $sub_process=$val[csf("sub_process")];
                        $all_batch_no=explode(",",$val[csf("batch_no")]);
                        foreach ($all_batch_no as $key => $batchId) 
                        {
                            if($sub_process!=92)
                            {
                                $dyes_chemical_arr_up[$batchId][$val[csf("item_category")]]['chemical_cost']+=$val[csf("dyes_chemical_cost")];
                            }
                            else
                            {
                                $dyes_chemical_arr_up[$batchId][$val[csf("item_category")]]['chemical_cost_finish']+=$val[csf("dyes_chemical_cost")];
                            }
                          
                        }
                    }
                    //var_dump($dyes_chemical_arr);
                 
                    $batch_info_arr = array();
                    $duplicate_batch_check = array();
                    $duplicate_sub_batch_check = array();
                    $tot_inhouse_qty_without_reprocess=0;
                    $tot_subcon_qty_without_reprocess=0;
                    $first_chemi_cost_up=0;
                    $first_dyeing_cost_up=0;
                    $chemical_cost_finish_up=0;
                    $batch_weight_up=0;
                    $fabric_type_arr_up = array();
                    foreach ($batch_rslt_up as $row) 
                    {
                        if($row['ENTRY_FORM']==0)
                        {
                            if($duplicate_batch_up_check[$row['BATCH_ID']] == '')
                            {
                                $duplicate_batch_up_check[$row['BATCH_ID']] = $row['BATCH_ID'];
                                $batch_weight_up += $row['BATCH_WEIGHT'];
                               $first_chemi_cost_up=$dyes_chemical_arr_up[$row['BATCH_ID']][5]['chemical_cost']+$dyes_chemical_arr_up[$row['BATCH_ID']][7]['chemical_cost'];
                               $first_dyeing_cost_up=$dyes_chemical_arr_up[$batch_id][6]['chemical_cost'];
                               $chemical_cost_finish_up=$dyes_chemical_arr_up[$row['BATCH_ID']][5]['chemical_cost_finish']+$dyes_chemical_arr_up[$row['BATCH_ID']][7]['chemical_cost_finish'];

                                if($duplicate_fup_check[$row['FABRIC_TYPE']] == '')
                                {
                                    $duplicate_fup_check[$row['FABRIC_TYPE']] = $row['FABRIC_TYPE'];
                                    array_push( $fabric_type_arr_up, $row['FABRIC_TYPE']);
                                }
                            }
                
                        }
                        else  if($row['ENTRY_FORM']==36)
                        {
                            if($duplicate_sub_up_batch_check[$row['BATCH_ID']] == '')
                            {
                                $duplicate_sub_up_batch_check[$row['BATCH_ID']] = $row['BATCH_ID'];
                                $batch_weight_up += $row['BATCH_WEIGHT'];
                
                                $first_chemi_cost_up=$dyes_chemical_arr_up[$row['BATCH_ID']][5]['chemical_cost']+$dyes_chemical_arr_up[$row['BATCH_ID']][7]['chemical_cost'];
                                $first_dyeing_cost_up=$dyes_chemical_arr_up[$row['BATCH_ID']][6]['chemical_cost'];
                                $chemical_cost_finish_up=$dyes_chemical_arr_up[$row['BATCH_ID']][5]['chemical_cost_finish']+$dyes_chemical_arr_up[$row['BATCH_ID']][7]['chemical_cost_finish'];

                                if($duplicate_fups_check[$row['FABRIC_TYPE']] == '')
                                {
                                    $duplicate_fups_check[$row['FABRIC_TYPE']] = $row['FABRIC_TYPE'];
                                    array_push( $fabric_type_arr_up, $row['FABRIC_TYPE']);
                                }
                            } 
                        }
                    }
                    unset($batch_rslt_up);
                    //var_dump($chemical_cost_finish_up);

                    if ($db_type == 0) 
                    {
                        $date_cond1 = "and FROM_DATE = '" . change_date_format(date('Y-m-01')) . "' and TO_DATE ='" . change_date_format(date('Y-m-t'), "yyyy-mm-dd") . "'";
                    } 
                    else 
                    {
                        $date_cond1 = "and FROM_DATE = '" . change_date_format(date('Y-m-01'), '', '', 1) . "' and TO_DATE ='" . change_date_format(date('Y-m-t'), '', '', 1) . "'";
                    }

                    // if ($db_type == 0) 
                    // {
                    //     $date_cond1 = "and FROM_DATE = '" . change_date_format('01-Jun-2022') . "' and TO_DATE ='" . change_date_format('30-Jun-2022', "yyyy-mm-dd") . "'";
                    // } 
                    // else 
                    // {
                    //     $date_cond1 = "and FROM_DATE = '" . change_date_format('01-Jun-2022', '', '', 1) . "' and TO_DATE ='" . change_date_format('30-Jun-2022', '', '', 1) . "'";
                    // }



                    $sql_d_rate_chart_up = "SELECT COMPANY_ID, FABRIC_TYPE_ID, COLOR_RANGE_ID, RATE, EXCHANGE_RATE, FROM_DATE, TO_DATE 
                    FROM LIB_DYEING_RATE_CHART_DTLS WHERE COMPANY_ID='$companyId' AND ENTRY_FORM=540 AND STATUS_ACTIVE=1 AND IS_DELETED=0
                    ".where_con_using_array($fabric_type_arr_up,0,'fabric_type_id')." $date_cond1";
                    //echo $sql_d_rate_chart_up;

                    $rsl_d_rate_chart_up = sql_select($sql_d_rate_chart_up);
                    $dRateChartArr_up = array();
                    foreach ($rsl_d_rate_chart_up as $row) 
                    {
                        //$f_date = explode('-',$row['FROM_DATE']);
                        // var_dump( $f_date);
                        $dRateChartArr_up[$row['FABRIC_TYPE_ID']][$row['COLOR_RANGE_ID']]['rate'] = $row['RATE'];
                        $dRateChartArr_up[$row['FABRIC_TYPE_ID']][$row['COLOR_RANGE_ID']]['exchange_rate'] = $row['EXCHANGE_RATE'];
                        $dRateChartArr_up[$row['FABRIC_TYPE_ID']][$row['COLOR_RANGE_ID']]['amount_tk'] = $row['RATE']*$row['EXCHANGE_RATE'];

                        $dRateChartSmnArr_up[$row['FABRIC_TYPE_ID']]['rate'] += $row['RATE'];
                        $dRateChartSmnArr_up[$row['FABRIC_TYPE_ID']]['rate_no']++;
                        $dRateChartSmnArr_up[$row['FABRIC_TYPE_ID']]['exchange_rate'] += $row['RATE']*$row['EXCHANGE_RATE'];
                    }
                    unset($rsl_d_rate_chart_up);
                    //var_dump($dRateChartArr1);
                }
             }

            ?>
               
                <table cellpadding="0"  cellspacing="0" align="left" class="rpt_table" rules="all" border="1">
                    <thead>
                        <tr>
                            <th width="100" style="font: 14px tahoma;">Fabric Type	</th>
                            <? 
                            foreach($f_type_arr as $k_f_type=>$v_f_type)
                            { 
                                
                                    ?>
                                     <th align="center" width="300" colspan="3" title="<?=$k_f_type;?>"  style="font: 14px tahoma;">
                                    <? 
                                    echo $fabric_type_for_dyeing[$k_f_type];
                                   
                                    ?>
                                </th>
                                    <?
                               
                            }
                            
                            ?>
                            <th width="100" style="font: 14px tahoma;" rowspan="2">Total Amount (Taka)</th>
                            <th width="100" style="font: 14px tahoma;" rowspan="2">Upto yesterday Amount (Taka)</th>
                            <th width="100" style="font: 14px tahoma;" rowspan="2">Upto Today Amount (Taka)	</th>

                        </tr>
                        <tr>
                        <th width="100" style="font: 14px tahoma;">Color Range	</th>
                        <? 
                            foreach($f_type_arr as $k_f_type=>$v_f_type)
                            { 
                                ?>
                                <th  style="font: 14px tahoma;">Weight in Kgs</th>
                                <th  style="font: 14px tahoma;">Rate</th>
                                <th  style="font: 14px tahoma;">Taka</th>
                            <?
                            }
                            
                            ?>
                        </tr>
                       
                    </thead>
                    <tbody>
                    <?
                     $g_tot_tk=0;$g_uya_tk=0;$g_uta_tk=0;
                   
                        foreach($c_range_arr as $k_c_range=>$v_c_range)
                        { 
                            
                            $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                            ?>
                            <tr bgcolor="#FFFFFF" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" class="cls_break">
                        
                            <?
                            if($v_c_range !='Sample')
                            {
                                ?>
                                <td align="right" style="font: 14px tahoma;" title="<?=$v_c_range;?>"> <? echo  $color_range[$v_c_range];?></td>
                                <?
                            }
                            else
                            {
                                ?>
                                <td align="right" style="font: 14px tahoma;" title="<?=$v_c_range;?>"> <? echo $v_c_range;?></td>
                                <?
                            }
                            ?>
                           

                            <? 
                                $tot_tk = 0;
                                $k_f_type = 1;
                                $f_r_span=0;
                                foreach($f_type_arr as $k_f_type=>$v_f_type)
                                { 
                                    $f_r_span++;
                                    ?>
                                    <td align="right"  style="font: 14px tahoma;">
                                        <? 
                                        //echo $k_f_type.'**'.$v_c_range;
                                        if($v_c_range !='Sample')
                                        {
                                            echo number_format($batch_arr[$k_f_type][$v_c_range]['batch_weight'],2);
                                        }
                                        else
                                        {
                                            echo number_format($batch_smn_arr[$k_f_type]['batch_weight'],2);
                                        }
                                       
                                        ?>
                                    </td>
                                    <td align="right"  style="font: 14px tahoma;">
                                        <? 
                                        if($v_c_range !='Sample')
                                        {
                                            $avg_rate  = $dRateChartArr[$k_f_type][$v_c_range]['rate'];
                                            echo number_format($avg_rate,2);
                                        }
                                        else
                                        {
                                            $tot_rate = $dRateChartSmnArr[$k_f_type]['rate'];
                                            $no_of_rate = $dRateChartSmnArr[$k_f_type]['rate_no'];
                                            $avg_rate = $tot_rate/$no_of_rate;
                                            echo number_format($avg_rate,2);
                                        }

                                      

                                        ?>
                                    </td>
                                    <td align="right"  style="font: 14px tahoma;">
                                        <? 
                                        if($v_c_range !='Sample')
                                        {
                                            $rate = $dRateChartArr[$k_f_type][$v_c_range]['rate'];
                                            $exchange_rate = $dRateChartArr[$k_f_type][$v_c_range]['exchange_rate'];
                                            $amount_tk = $dRateChartArr[$k_f_type][$v_c_range]['amount_tk'];
                                        
                                            ?>
                                            <p title="<? echo 'Rate : '.$rate.', Exchange Rate : '.$exchange_rate?>">
                                                <?
                                                     $taka = $amount_tk;
                                                    echo number_format($taka,2);
                                                
                                                ?>
                                            </p>
                                            <?
                                        }
                                        else
                                        {
                                           $exchange_rate =  $dRateChartSmnArr[$k_f_type]['exchange_rate'];
                                           $exchange_rate_no =$dRateChartSmnArr[$k_f_type]['exchange_rate_no'];
                                           $avg_exchange_rate = $exchange_rate/$exchange_rate_no;
                                            $taka =$avg_rate* $avg_exchange_rate;
                                            echo number_format($taka,2);
                                        }
                                        ?>
                                        
                                    </td>
                                    <?
                                    $tot_tk += $taka;
                                    ?>
                                   
                                   
                                 
                                    <?
                                }
                            ?>
                                
                                <td align="right" style="font: 14px tahoma;" > <? echo number_format( $tot_tk,2); $g_tot_tk +=$tot_tk; ?></td>
                                <td align="right" style="font: 14px tahoma;" > <? 
                                    $g_uya_tk =0;
                                    $uya_tk =0;
                                    $uya_smn_tk =0;
                                    $uya_smn_rate_no =0;
                                    if($v_c_range !='Sample')
                                    {
                                        foreach($f_type_arr as $k_f_type=>$v_f_type)
                                        {  
                                        $uya_tk += $dRateChartArr_up[$k_f_type][$v_c_range]['amount_tk'];
                                        }
    
                                        echo number_format( $uya_tk,2);
                                        $g_uya_tk +=$uya_tk;
                                    }
                                    else
                                    {
                                        foreach($f_type_arr as $k_f_type=>$v_f_type)
                                        {  
                                          $uya_smn_tk =$dRateChartSmnArr_up[$k_f_type]['exchange_rate'];
                                          $uya_smn_rate_no =$dRateChartSmnArr_up[$k_f_type]['rate_no'];
                                          $uya_tk += $uya_smn_tk/$uya_smn_rate_no;
                                        }
                                       
                                        echo number_format( $uya_tk,2);
                                        $g_uya_tk +=$uya_tk;
                                    }
                                 ?>
                                </td>
                                <td align="right" style="font: 14px tahoma;" > <? echo number_format($tot_tk+$uya_tk,2); $g_uta_tk +=($tot_tk+$uya_tk); ?></td>
                               
                            </tr>
                        <?
                       
                        }
                        ?>
                        <tr>
                            <td></td>
                            <td colspan="<? echo  $f_r_span*3;?>" align="right"  style="font: 14px tahoma;"><b>Grand Total : </b></td>
                            <td align="right"  style="font: 14px tahoma;"><b><? echo number_format( $g_tot_tk,2);?></b></td>
                            <td align="right"  style="font: 14px tahoma;"><b><? echo number_format( $g_uya_tk,2);?></b></td>
                            <td align="right"  style="font: 14px tahoma;"><b><? echo number_format( $g_uta_tk,2);?></b></td>
                        </tr>
                        
                    </tbody>
                </table>    
            </div>
            <br><br>    
            <div style="margin-top: 170px">
              
                <table cellpadding="0" width="400" cellspacing="0" align="left" class="rpt_table" rules="all" border="1">
                    <thead>
                        <tr>
                            <th colspan="2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr bgcolor="#E9F3FF" >
                            <td style="font: 14px tahoma;">Today Dyes And Chemical Expense </td>
                            <td align="right" style="font: 14px tahoma;"> 
                                <?
                                $tot_chemical_cost_finish= $chemical_cost_finish+$first_dyeing_cost+$first_chemi_cost;
                                echo number_format($tot_chemical_cost_finish,4,".","");
                                $g_tot_chemical_cost_finish +=$tot_chemical_cost_finish;
                                ?>
                            </td>
                        </tr>
                        <tr bgcolor="#E9F3FF" >
                            <td style="font: 14px tahoma;">Upto yesterday Dyes And Chemical Expense	 </td>
                            <td align="right" style="font: 14px tahoma;">
                                 <?
                                $tot_chemical_cost_finish_up= $chemical_cost_finish_up+$first_dyeing_cost_up+$first_chemi_cost_up;
                                echo number_format($tot_chemical_cost_finish_up,4,".","");
                                $g_tot_chemical_cost_finish_up +=$tot_chemical_cost_finish_up;
                                ?>
                            </td>
                        </tr>
                        <tr bgcolor="#E9F3FF" >
                            <td style="font: 14px tahoma;">Upto Today Dyes And Chemical Expense	 </td>
                            <td align="right" style="font: 14px tahoma;"> 
                                <?
                                 echo number_format($g_tot_chemical_cost_finish+$g_tot_chemical_cost_finish_up,4,".","");
                                ?>
                            </td>
                        </tr>
                    </tbody>
                        
                </table>
                <table cellpadding="0" width="3" cellspacing="0" align="left" rules="all" border="1">
                        <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
                    </table>
                <table cellpadding="0" width="400" cellspacing="0" align="left" class="rpt_table" rules="all" border="1">
                    <thead>
                        <tr>
                            <th colspan="2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr bgcolor="#E9F3FF" >
                            <td style="font: 14px tahoma;">Dyeing Production Cost Per KG's	 </td>
                            <td align="right" style="font: 14px tahoma;"> 
                            <? echo number_format($tot_chemical_cost_finish/$batch_weight,4,".","");  ?>
                            </td>
                        </tr>
                        <tr bgcolor="#E9F3FF" >
                            <td style="font: 14px tahoma;">Upto Today Dyeing Cost Per KG's </td>
                            <td align="right" style="font: 14px tahoma;">
                            <? echo number_format($tot_chemical_cost_finish_up/$batch_weight_up,4,".","");  ?>
                           </td>
                        </tr>
                    </tbody>
                        
                </table>
            </div>

			
		</fieldset>
		<?
	} 
	
    
    echo "<br />Execution Time: " . (microtime(true) - $started).'S';
	foreach (glob("$user_name*.xls") as $filename)
	{
		if (@filemtime($filename) < (time() - $seconds_old))
		@unlink($filename);
	}
	//---------end------------//
    $html =ob_get_contents();
    ob_clean();
    $total_data=$html;
    $html = strip_tags($html, '<table><thead><tbody><tfoot><tr><td><th>');
	$name = time();
	$filename = $user_name . "_" . $name . ".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	$filename = "requires/" . $user_name . "_" . $name . ".xls";
	echo "$total_data####$filename";
	exit();
}

?>