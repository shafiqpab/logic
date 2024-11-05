<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "")
	header("location:login.php");

require_once('../../../includes/common.php');

$user_name = $_SESSION['logic_erp']['user_id'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

if($action=="print_button_variable_setting")
{
	$print_report_format_arr = return_library_array("select format_id,format_id from lib_report_template where template_name =$data and module_id=7 and report_id=77 and is_deleted=0 and status_active=1","format_id","format_id");
	echo "print_report_button_setting('".implode(',',$print_report_format_arr)."');\n";
	exit(); 
}

if ($action == "load_drop_down_buyer")
{
	echo create_drop_down("cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name", "id,buyer_name", 1, "-- All Buyer --", $selected, "");
	exit();
}

if ($action=="action_style_description")
{
  	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
  	extract($_REQUEST);
	?>
	<script>
		var company="<? echo $company; ?>";
		$('#cbo_company_mst').val(company);
		function js_set_value(booking_no)
		{
			document.getElementById('selected_booking').value=booking_no;
			parent.emailwindow.hide();
		}
    </script>
</head>
<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
    <table width="800" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
       <thead>
            <th colspan="7">
              <?
               echo create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" );
              ?>
            </th>
         </thead>
        <thead>
            <th width="150" class="must_entry_caption">Company Name</th>
            <th width="150">Buyer Name</th>
            <th width="100">Booking No</th>
            <th width="80">Style Desc.</th>
            <th width="150" colspan="2">Date Range</th>
            <th>&nbsp;</th>
        </thead>
        <tr class="general">
            <td> <input type="hidden" id="selected_booking">
                <?php
                    echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "--Select Company--", $company, "load_drop_down( 'sample_booking_non_order_controller', this.value, 'load_drop_down_buyer_pop', 'buyer_td');");
                ?>
            </td>
        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 172, $blank_array,"", 1, "-- Select Buyer --" ); ?></td>

        <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:100px"></td>
        <td><input name="txt_style_desc" id="txt_style_desc" class="text_boxes" style="width:80px"></td>
        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"></td>
        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"></td>
         <td align="center">
            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_style_desc').value, 'action_style_description_list_view', 'search_div', 'fabric_prod_status_report_sample_without_order_controller','setFilterGrid(\'table_body\',1)')" style="width:100px;" /></td>
    </tr>
    <tr>
        <td align="center" colspan="7" valign="middle">
        <?
        echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );
        ?>
        <? echo load_month_buttons();  ?>
        </td>
    </tr>
 </table>
    <div id="search_div"></div>
    </form>
   </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action=="action_style_description_list_view")
{
	$data=explode('_',$data);
	$style_desc=$data[7];
	if ($data[0]!=0)
		$company="  a.company_id='$data[0]'";
	else
	{
		echo "Please Select Company First.";
		die;
	}
	
	if($style_desc=="" && trim($data[5])=="" && (trim($data[2])=="" && trim($data[3])==""))
	{
		echo "Please Select Date Range.";
		die;
	}
	
	if ($data[1]!=0)
	{
		$buyer=" and a.buyer_id='$data[1]'";
	}
	else
	{
		$buyer="";
	}

	if($db_type==0)
	{
		$booking_year_cond=" and YEAR(a.insert_date)=$data[4]";
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	else if($db_type==2)
	{
		$booking_year_cond=" and to_char(a.insert_date,'YYYY')=$data[4]";
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}
	
	if($data[6]==4 || $data[6]==0)
	{
		if (str_replace("'","",$data[5])!="")
			$booking_cond=" and a.booking_no_prefix_num like '%$data[5]%'  $booking_year_cond  ";
		else
			$booking_cond="";
		if (str_replace("'","",$data[7])!="")
			$style_des_cond=" and b.style_des like '%$data[7]%' ";
		else
			$style_des_cond="";
	}
	else if($data[6]==1)
	{
		if (str_replace("'","",$data[5])!="")
			$booking_cond=" and a.booking_no ='$data[5]'   ";
		else
			$booking_cond="";
		if (str_replace("'","",$data[7])!="")
			$style_des_cond=" and b.style_des='$data[7]' ";
		else
			$style_des_cond="";
	}
	else if($data[6]==2)
	{
		if (str_replace("'","",$data[5])!="")
			$booking_cond=" and a.booking_no_prefix_num like '$data[5]%'  $booking_year_cond  ";
		else
			$booking_cond="";
		if (str_replace("'","",$data[7])!="")
			$style_des_cond=" and b.style_des like '$data[7]%' ";
		else
			$style_des_cond="";
	}
	else if($data[6]==3)
	{
		if (str_replace("'","",$data[5])!="")
			$booking_cond=" and a.booking_no_prefix_num like '%$data[5]'  $booking_year_cond  ";
		else
			$booking_cond="";
		if (str_replace("'","",$data[7])!="")
			$style_des_cond=" and b.style_des like '%$data[7]' ";
		else
			$style_des_cond="";
	}
	
	$style_library=return_library_array( "select id,style_ref_no from sample_development_mst", "id", "style_ref_no"  );
    //$approved=array(0=>"No",1=>"Yes",3=>"Yes");
    //$is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');

	$sql= "select a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved, a.pay_mode, b.style_id, b.style_des,b.fabric_source as fabric_source_dtls from wo_non_ord_samp_booking_mst a left join wo_non_ord_samp_booking_dtls b on a.booking_no=b.booking_no and b.status_active=1 and b.is_deleted=0 where $company". set_user_lavel_filtering(' and a.buyer_id','buyer_id')." $buyer $booking_date $booking_cond $style_des_cond and a.booking_type=4 and a.status_active=1 and a.is_deleted=0 and( a.entry_form_id is null or a.entry_form_id =0 ) order by a.id DESC";
	//echo $sql;
	?>
    <table class="rpt_table scroll" width="1000" cellpadding="0" cellspacing="0" border="1" rules="all" >
    	<thead>
            <th width="30">Sl</th>
            <th width="100">Booking No</th>
            <th width="80">Booking Date</th>
            <th width="100">Company</th>
            <th width="100">Buyer</th>
            <th width="80">Fabric Nature</th>
            <th width="80">Fabric Source</th>
            <th width="80">Pay Mode</th>
            <th width="100">Supplier</th>
            <th width="50">Style</th>
            <th>Style Desc.</th>
        </thead>
    </table>
    <div style="max-height:300px; overflow-y:scroll; width:1000px" >
    <table width="980" class="rpt_table" id="table_body" border="1" rules="all">
        <tbody>
		<?
        $i=1;
        $sql_data=sql_select($sql);
        foreach($sql_data as $row)
        {
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			if($row[csf('fabric_source')]!=0)
			{
				$fabric_source_txt = $fabric_source[$row[csf('fabric_source')]];
			}
			else
			{
				$fabric_source_txt = $fabric_source[$row[csf('fabric_source_dtls')]];
			}
			?>
			<tr bgcolor="<? echo $bgcolor;?>" onClick="js_set_value('<? echo $row[csf('booking_no')]  ?>')" style="cursor:pointer">
                <td width="30"><? echo $i;?></td>
                <td width="100"><? echo $row[csf('booking_no_prefix_num')];?></td>
                <td width="80"><? echo date("d-m-Y",strtotime($row[csf('booking_date')]));?></td>
                <td width="100"><? echo $comp[$row[csf('company_id')]];?></td>
                <td width="100"><? echo $buyer_arr[$row[csf('buyer_id')]];?></td>
                <td width="80"><? echo $item_category[$row[csf('item_category')]];?></td>
                <td width="80"><? echo $fabric_source_txt;?></td>
                <td width="80"><? echo $pay_mode[$row[csf('pay_mode')]];?></td>
                <td width="100">
                <?
                if($row[csf('pay_mode')]==3 || $row[csf('pay_mode')]==5) echo $comp[$row[csf('supplier_id')]];
                else echo $suplier[$row[csf('supplier_id')]];
                ?>
                </td>
                <td width="50" style="word-wrap: break-word;word-break: break-all;"><? echo $style_library[$row[csf('style_id')]];?></td>
                <td style="word-wrap: break-word;word-break: break-all;"><? echo $row[csf('style_des')];?></td>
			</tr>
			<?
			$i++;
        }
        ?>
        </tbody>
    </table>
    </div>
    <?
	exit();
}

$color_dtls = return_library_array("select id, color_name from lib_color", "id", "color_name");
$company_dtls = return_library_array("select id, company_name from lib_company", "id", "company_name");
//$supplier_dtls = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
$buyer_short_name = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
$yarn_count_dtls = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
$sample_dtls = return_library_array("select id, sample_name from lib_sample", "id", "sample_name");
$machine_dtls = return_library_array("select id, machine_no from lib_machine_name where category_id=1 and status_active=1 and is_deleted=0 and is_locked=0", "id", "machine_no");

$receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan");
$report_format_arr = array(0=>"",1 => "show_fabric_booking_report_gr", 2 => "show_fabric_booking_report", 3 => "show_fabric_booking_report3", 4 => "show_fabric_booking_report1", 5 => "show_fabric_booking_report2", 6 => "show_fabric_booking_report4", 7 => "show_fabric_booking_report5", 8 => "show_fabric_booking_report", 9 => "show_fabric_booking_report3", 10 => "show_fabric_booking_report4", 28 => "show_fabric_booking_report_akh",46=>"show_fabric_booking_report_urmi",136=>"print_booking_3",244=>"show_fabric_booking_report_ntg",38=>"show_fabric_booking",39=>"show_fabric_booking_report2",64=>"show_fabric_booking_report3"); //8,9 for short
//--------------------------------------------------------------------------------------------------------------------

$tmplte = explode("**", $data);

if ($tmplte[0] == "viewtemplate")
    $template = $tmplte[1];
else
    $template = $lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template == "")
    $template = 1;

if ($action == "action_show") 
{
    //echo 'su..re'; die;
	$process = array(&$_POST);
    extract(check_magic_quote_gpc($process));
	$company_id = str_replace("'", "", $cbo_company_name);
	$buyer_id = str_replace("'", "", $cbo_buyer_name);
	$sample_type = str_replace("'", "", $cbo_sample_type);
	$booking_year = str_replace("'", "", $cbo_booking_year);
	$booking_no = str_replace("'", "", $txt_booking_no);
	$style_description = str_replace("'", "", $txt_style_description);
	$program_no = str_replace("'", "", $txt_program_no);
	$requisition_no = str_replace("'", "", $txt_requisition_no);
	$date_from = str_replace("'", "", $txt_date_from);
	$date_to = str_replace("'", "", $txt_date_to);
	$bookingNoArr = array();

	$sampleTypeCond = '';
	if($sample_type != 0)
		$sampleTypeCond="AND b.sample_type = ".$sample_type."";
	
	if (trim($booking_year) != 0) 
	{
		if ($db_type == 0)
			$year_cond = "AND YEAR(a.insert_date) = ".$booking_year."";
		else if ($db_type == 2)
			$year_cond = "AND TO_CHAR(a.insert_date,'YYYY') = ".$booking_year."";
	}
	else
		$year_cond = "";
		
	//date condition
	$dateCond = '';
	if($date_from != '' && $date_to != '')
	{
		$dateCond = "AND a.booking_date BETWEEN '".$date_from."' AND '".$date_to."'";
	}

	//for booking no search
	$bookingNoCond = '';
	if($booking_no != '')
	{
		$bookingNoCond = "AND a.booking_no LIKE '%".$booking_no."'";
	}
	
	//txt_style_description
	if($style_description != '')
	{
		$bookingNoCond = '';
		$bookingNoCond = "AND a.booking_no = '".$style_description."'";
	}
		
	$sqlBooking = " SELECT a.buyer_id, a.booking_date, a.pay_mode, a.supplier_id, b.id, b.booking_no FROM wo_non_ord_samp_booking_mst a INNER JOIN wo_non_ord_samp_booking_dtls b ON a.booking_no = b.booking_no WHERE a.company_id = ".$company_id." AND a.item_category = 2 AND b.fabric_source = 1 ".$year_cond." ".$bookingNoCond." ".$sampleTypeCond." ".$dateCond." GROUP BY a.buyer_id, a.booking_date, a.pay_mode, a.supplier_id, b.id, b.booking_no ORDER BY b.id DESC";
	//echo $sqlBooking; die;
	$sqlBookingRslt = sql_select($sqlBooking);
	foreach($sqlBookingRslt as $row)
	{
		$bookingNoArr[$row[csf('booking_no')]] = $row[csf('booking_no')];
	}
	//echo "<pre>";
	//print_r($bookingNoArr); die;

	//for program no search
	$programNoCond = '';
	if($program_no != '')
	{
		$programNoCond = "AND c.dtls_id IN(".$program_no.")";
		$sqlPlan = "SELECT c.booking_no FROM ppl_planning_entry_plan_dtls c WHERE c.status_active = 1 AND c.is_deleted = 0 ".$programNoCond." GROUP BY c.booking_no";
		// AND c.is_sales = 2
		//echo $sqlPlan; die;
		$sqlPlanRslt = sql_select($sqlPlan);
		foreach($sqlPlanRslt as $row)
		{
			$bookingNoArr[$row[csf('booking_no')]] = $row[csf('booking_no')];
		}
	}
	//echo "<pre>";
	//print_r($bookingNoArr); die;
	
	//for requisition no search
	$requisitionNoCond = '';
	if($requisition_no != '')
	{
		$requisitionNoCond = "AND d.requisition_no IN(".$requisition_no.")";
		$sqlRequisition = "SELECT c.booking_no FROM ppl_planning_entry_plan_dtls c, ppl_yarn_requisition_entry d WHERE c.dtls_id = d.knit_id AND c.status_active = 1 AND c.is_deleted = 0 AND c.is_revised=0 AND c.is_sales = 2 AND d.status_active = 1 AND d.is_deleted = 0 ".$requisitionNoCond." GROUP BY c.booking_no";
		//echo $sqlRequisition; die;
		$sqlRequisitionRslt = sql_select($sqlRequisition);
		foreach($sqlRequisitionRslt as $row)
		{
			$bookingNoArr[$row[csf('booking_no')]] = $row[csf('booking_no')];
		}
	}
	//echo "<pre>";
	//print_r($bookingNoArr); die;
	$sql = "SELECT a.booking_no, a.buyer_id, a.booking_date, a.pay_mode, a.supplier_id, b.id, b.sample_type, b.style_des, b.fabric_description, b.fabric_color, b.uom, b.finish_fabric, b.grey_fabric FROM wo_non_ord_samp_booking_mst a INNER JOIN wo_non_ord_samp_booking_dtls b ON a.booking_no = b.booking_no WHERE a.company_id = ".$company_id." AND a.booking_no IN('".implode("','", $bookingNoArr)."') ".$dateCond."";
	//echo $sql; die;
	$sqlRslt = sql_select($sql);
	$dataArr = array();
	foreach($sqlRslt as $row)
	{
		$dataArr[$row[csf('booking_no')]][$row[csf('sample_type')]][$row[csf('buyer_id')]][$row[csf('style_des')]][$row[csf('fabric_description')]][$row[csf('fabric_color')]]['grey_fabric'] += $row[csf('grey_fabric')];
		$dataArr[$row[csf('booking_no')]][$row[csf('sample_type')]][$row[csf('buyer_id')]][$row[csf('style_des')]][$row[csf('fabric_description')]][$row[csf('fabric_color')]]['uom'] = $row[csf('uom')];
	}
	//echo "<pre>";
	//print_r($dataArr); die;

	//for program no
	$sqlPlan = "SELECT c.dtls_id, c.booking_no, c.buyer_id, c.fabric_desc, c.body_part_id, c.color_type_id, c.determination_id, c.gsm_weight, c.dia, c.program_qnty, g.color_id, g.color_prog_qty FROM ppl_planning_entry_plan_dtls c, ppl_color_wise_break_down g WHERE c.dtls_id = g.program_no AND c.status_active = 1 AND c.is_deleted = 0 AND c.is_revised=0 AND c.booking_no IN('".implode("','", $bookingNoArr)."') ".$programNoCond."";
	//echo $sqlPlan; die;
	$sqlPlanRslt = sql_select($sqlPlan);
	$programData = array();
	$programNoArr = array();
	$progColorData = array();
	foreach($sqlPlanRslt as $row)
	{
		$programNoArr[$row[csf('dtls_id')]] = $row[csf('dtls_id')];
		
		$progColorData[$row[csf('dtls_id')]][$row[csf('color_id')]] += $row[csf('color_prog_qty')];
		$programData[$row[csf('booking_no')]][$row[csf('buyer_id')]][$row[csf('fabric_desc')]][$row[csf('color_id')]]['program_no'][] = $row[csf('dtls_id')];
		$programData[$row[csf('booking_no')]][$row[csf('buyer_id')]][$row[csf('fabric_desc')]][$row[csf('color_id')]]['program_qty'] += $row[csf('color_prog_qty')];
	}
	//echo "<pre>";
	//print_r($programData); die;
	
	//for requisition no
	$sqlRequisition = "SELECT c.dtls_id, c.booking_no, c.buyer_id, c.fabric_desc, c.body_part_id, c.color_type_id, c.determination_id, c.gsm_weight, c.dia, c.program_qnty, d.requisition_no, d.yarn_qnty, g.color_id, g.color_prog_qty FROM ppl_planning_entry_plan_dtls c, ppl_yarn_requisition_entry d, ppl_color_wise_break_down g WHERE c.dtls_id = g.program_no AND c.dtls_id = d.knit_id AND c.status_active = 1 AND c.is_deleted = 0 AND c.is_revised=0 AND d.status_active = 1 AND d.is_deleted = 0 AND c.booking_no IN('".implode("','", $bookingNoArr)."') ".$programNoCond." ".$requisitionNoCond."";
	//echo $sqlRequisition; die;
	$sqlRequisitionRslt = sql_select($sqlRequisition);
	$requisitionData = array();
	//$progReqData = array();
	foreach($sqlRequisitionRslt as $row)
	{
		$colorReqQty = 0;
		if(!empty($progColorData[$row[csf('dtls_id')]]))
		{
			$progQty = array_sum($progColorData[$row[csf('dtls_id')]]);
			$colorQty = $progColorData[$row[csf('dtls_id')]][$row[csf('color_id')]];
			$reqQty = $row[csf('yarn_qnty')];
			$colorReqQty = number_format((($colorQty*$reqQty)/$progQty), 2, '.', '');
		}
		
		$requisitionData[$row[csf('booking_no')]][$row[csf('buyer_id')]][$row[csf('fabric_desc')]][$row[csf('color_id')]]['requisition_no'][$row[csf('requisition_no')]] = $row[csf('requisition_no')];
		$requisitionData[$row[csf('booking_no')]][$row[csf('buyer_id')]][$row[csf('fabric_desc')]][$row[csf('color_id')]]['requisition_qty'] += $colorReqQty;
	}
	//echo "<pre>";
	//print_r($bookingNoArr); die;
	
	//for yarn issue
	$sqlIssue = "SELECT c.dtls_id, c.booking_no, c.buyer_id, c.fabric_desc, c.body_part_id, c.color_type_id, c.determination_id, c.gsm_weight, c.dia, c.program_qnty, d.requisition_no, d.yarn_qnty, d.prod_id, e.cons_quantity, f.id, f.issue_number_prefix_num, g.color_id, g.color_prog_qty FROM ppl_planning_entry_plan_dtls c, ppl_yarn_requisition_entry d, inv_transaction e, inv_issue_master f, ppl_color_wise_break_down g WHERE c.dtls_id = d.knit_id AND d.knit_id = g.program_no AND d.requisition_no = e.requisition_no AND d.prod_id = e.prod_id AND e.mst_id = f.id AND c.status_active = 1 AND c.is_deleted = 0 AND c.is_revised=0 AND d.status_active = 1 AND d.is_deleted = 0 AND e.receive_basis = 3 AND e.item_category = 1 AND e.transaction_type=2 AND e.status_active = 1 AND e.is_deleted = 0 AND e.requisition_no IS NOT NULL AND c.booking_no IN('".implode("','", $bookingNoArr)."') ".$programNoCond." ".$requisitionNoCond."";
	//echo $sqlIssue; die;
	$sqlIssueRslt = sql_select($sqlIssue);
	$issueData = array();
	//$issueDataArr = array();
	foreach($sqlIssueRslt as $row)
	{
		// $row[csf('prod_id')]."<br>";
		$compos = '';
		$sqlComp = sql_select("select lot, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color from product_details_master where id=" . $row[csf('prod_id')]);
		foreach($sqlComp as $yarnComp)
		{
			$yarn_comp_type1st = $yarnComp[csf('yarn_comp_type1st')];
			$yarn_comp_type2nd = $yarnComp[csf('yarn_comp_type2nd')];
			$yarn_comp_percent1st = $yarnComp[csf('yarn_comp_percent1st')];
			$yarn_comp_percent2nd = $yarnComp[csf('yarn_comp_percent2nd')];
			
			if ($yarn_comp_percent2nd != 0)
			{
				$compos = $yarn_count_dtls[$yarnComp[csf('yarn_count_id')]].', '.$composition[$yarn_comp_type1st].' '.$yarn_comp_percent1st."%".' '.$composition[$yarn_comp_type2nd].' '.$yarn_comp_percent2nd.'%, '.$yarn_type[$yarnComp[csf('yarn_type')]];
			}
			else
			{
				$compos = $yarn_count_dtls[$yarnComp[csf('yarn_count_id')]].', '.$composition[$yarn_comp_type1st].' '.$yarn_comp_percent1st.'% '. $composition[$yarn_comp_type2nd].', '.$yarn_type[$yarnComp[csf('yarn_type')]];
			}
		}

		$colorIssueQty = 0;
		if(!empty($progColorData[$row[csf('dtls_id')]]))
		{
			$progQty = array_sum($progColorData[$row[csf('dtls_id')]]);
			$colorQty = $progColorData[$row[csf('dtls_id')]][$row[csf('color_id')]];
			$issueQty = $row[csf('cons_quantity')];
			$colorIssueQty = number_format((($colorQty*$issueQty)/$progQty), 2, '.', '');
		}
		
		$issueData[$row[csf('booking_no')]][$row[csf('buyer_id')]][$row[csf('fabric_desc')]][$row[csf('color_id')]]['issue_id'][$row[csf('id')]] = $row[csf('id')];
		$issueData[$row[csf('booking_no')]][$row[csf('buyer_id')]][$row[csf('fabric_desc')]][$row[csf('color_id')]]['lot'][$yarnComp[csf('lot')]] = $yarnComp[csf('lot')];
		$issueData[$row[csf('booking_no')]][$row[csf('buyer_id')]][$row[csf('fabric_desc')]][$row[csf('color_id')]]['issue_desc'] = $compos;
		$issueData[$row[csf('booking_no')]][$row[csf('buyer_id')]][$row[csf('fabric_desc')]][$row[csf('color_id')]]['issue_qty'] += $colorIssueQty;
	}
	//echo "<pre>";
	//print_r($issueData); die;
	
	//for production
	/*
	$sqlProduction = "SELECT 
	c.dtls_id, c.booking_no, c.buyer_id, c.fabric_desc, c.body_part_id, c.color_type_id, c.determination_id, c.gsm_weight, c.dia, c.program_qnty, 
	g.color_id, g.color_prog_qty ,
	h.grey_receive_qnty
	FROM 
	ppl_planning_entry_plan_dtls c, 
	inv_receive_master f, 
	ppl_color_wise_break_down g, 
	pro_grey_prod_entry_dtls h
	WHERE
	c.dtls_id = f.booking_id 
	AND c.dtls_id = g.program_no 
	AND f.id = h.mst_id
	AND c.status_active = 1 
	AND c.is_deleted = 0 
	AND c.is_revised=0
	AND c.dtls_id IN(".implode(",", $programNoArr).")
	AND c.booking_no IN('".implode("','", $bookingNoArr)."')
	AND f.entry_form = 2 
	AND f.item_category = 13 
	AND f.booking_without_order = 1";
	//echo $sqlProduction; die;
	$sqlProductionRslt = sql_select($sqlProduction);
	$productionData = array();
	foreach($sqlProductionRslt as $row)
	{
		$productionData[$row[csf('booking_no')]][$row[csf('buyer_id')]][$row[csf('fabric_desc')]][$row[csf('color_id')]]['production_qty'] += $row[csf('grey_receive_qnty')];
	}
	*/
	//echo "<pre>";
	//print_r($issueData); die;
	
	//for gery fabric
	/*
		//for knitting info
	$sqlKnitting = "SELECT a.booking_id, b.grey_receive_qnty, reject_fabric_receive, b.trans_id, b.no_of_roll FROM inv_receive_master a, pro_grey_prod_entry_dtls b WHERE a.id=b.mst_id AND a.item_category=13 AND a.entry_form=2 AND a.receive_basis=2 AND b.status_active=1 AND b.is_deleted=0 ".where_con_using_array($progNoArr, '0', 'a.booking_id');
	//echo $sqlKnitting;
	$sqlKnittingRslt = sql_select($sqlKnitting);
	$knittingData = array();
	foreach($sqlKnittingRslt as $row)
	{
		$progNo = $row[csf('booking_id')];
		$knittingData[$progNo]['knitting_qty'] += $row[csf('grey_receive_qnty')];
		$knittingData[$progNo]['no_of_roll'] += $row[csf('no_of_roll')];
		$knittingData[$progNo]['fabric_reject_qty'] += $row[csf('reject_fabric_receive')];
	}
	*/

	$sqlGreyFabric = "SELECT c.mst_id, c.booking_no, c.qnty, c.qc_pass_qnty, c.entry_form FROM pro_roll_details c WHERE c.status_active=1 AND c.is_deleted=0 AND c.booking_without_order = 1 AND c.entry_form IN(2,58,61) AND c.booking_no IN ('".implode("','", $programNoArr)."')";
	//echo $sqlGreyFabric;
	$sqlGreyFabricRslt = sql_select($sqlGreyFabric);
	$greyFabricData = array();
	$productionIdArr = array();
	foreach($sqlGreyFabricRslt as $row)
	{
		$colorProdQty = 0;
		if(!empty($progColorData[$row[csf('booking_no')]]))
		{
			foreach($progColorData[$row[csf('booking_no')]] as $clrId=>$clrQty)
			{
				$progQty = array_sum($progColorData[$row[csf('booking_no')]]);
				$colorQty = $clrQty;
				$prodQty = $row[csf('qnty')];
				$colorProdQty = number_format((($colorQty*$prodQty)/$progQty), 2, '.', '');
				
				if($row[csf('entry_form')] == 2)
				{
					$greyFabricData[$row[csf('booking_no')]][$clrId]['production_qty'] += $colorProdQty;
					$productionIdArr[$row[csf('booking_no')]][$clrId][$row[csf('mst_id')]] = $row[csf('mst_id')];
				}
				else if($row[csf('entry_form')] == 58)
				{
					$greyFabricData[$row[csf('booking_no')]][$clrId]['receive_qty'] += $colorProdQty;
				}
				else if($row[csf('entry_form')] == 61)
				{
					$greyFabricData[$row[csf('booking_no')]][$clrId]['issue_qty'] += $colorProdQty;
				}
			}
		}
	}
	//echo "<pre>";
	//print_r($greyFabricData); die;

	$totalColumn = 34;
	$tblWidth = 3360;
	ob_start();
	?>
    <fieldset style="width:<? echo $table_width + 30; ?>px;">
        <table cellpadding="0" cellspacing="0" width="<? echo $tblWidth; ?>">
            <tr>
                <td align="center" width="100%" colspan="<? echo $totalColumn; ?>" style="font-size:16px"><strong><?php echo $company_library[$company_name]; ?></strong></td>
            </tr>
            <tr>
                <td align="center" width="100%" colspan="<? echo $totalColumn; ?>" style="font-size:16px"><strong><? if ($start_date != "" && $end_date != "") echo "From " . change_date_format($start_date) . " To " . change_date_format($end_date); ?></strong></td>
            </tr>
        </table>
        <table class="rpt_table" border="1" rules="all" width="<? echo $tblWidth; ?>" cellpadding="0" cellspacing="0" id="table_header_1">
            <thead>
                <tr>
                    <th colspan="9">Booking Details</th>
                    <th colspan="8">Knitting Program and Yarn Details</th>
                    <th colspan="7">Grey Fabric Status</th>
                    <th colspan="10">Finish Fabric Status</th>
                </tr>
                <tr>
                    <th width="40">Sl</th>
                    <th width="100">Booking No</th>
                    <th width="100">Sample Type</th>
                    <th width="100">Buyer Name</th>
                    <th width="120">Style Description</th>
                    <th width="120">Fabrication</th>
                    <th width="100">Color</th>
                    <th width="100">Required(As Per Booking)</th>
                    <th width="60">Unit</th>
                    
                    <th width="100">Program No</th>
                    <th width="100">Program Qty</th>
                    <th width="100">Balance</th>
                    <th width="100">Requisition No</th>
                    <th width="100">Requisition Qty</th>
                    <th width="100">Requisition Balance</th>
                    <th width="120">Yarn Description</th>
                    <th width="100">Yarn Issue</th>
                    
                    <th width="100">Knitting Production</th>
                    <th width="100">Knit Balance</th>
                    <th width="100">P.Loss %</th>
                    <th width="100">Grey Fabric Receive</th>
                    <th width="100">Grey Issue</th>
                    <th width="100">Total Available</th>
                    <th width="100">Batch Qty</th>
                    
                    <th width="100">Fabric Color</th>
                    <th width="100">Fin. Fabric Required(As Per Booking)</th>
                    <th width="100">Dyeing Qty</th>
                    <th width="100">Fin. Fab Production</th>
                    <th width="100">Balance</th>
                    <th width="100">P.Loss %</th>
                    <th width="100">Fin. Fab Receive</th>
                    <th width="100">Issue to Cutting</th>
                    <th width="100">Total Available</th>
                    <th width="100">Fabric Source</th>
                </tr>
            </thead>
            <tbody>
            <?php
			$sl = 0;
			foreach($dataArr as $bookingNO=>$bookingArr)
			{
				foreach($bookingArr as $sampleType=>$sampleTypeArr)
				{
					foreach($sampleTypeArr as $buyerId=>$buyerArr)
					{
						foreach($buyerArr as $stypleDesc=>$styleArr)
						{
							foreach($styleArr as $fabricDesc=>$fabricArr)
							{
								foreach($fabricArr as $colorId=>$row)
								{
									$sl++;
									if($i%2==0)
										$bgcolor="#E9F3FF";
									else
										$bgcolor="#FFFFFF";
										
									//for program
									$programNo = implode(', ', $programData[$bookingNO][$buyerId][$fabricDesc][$colorId]['program_no']);
									$programQty = $programData[$bookingNO][$buyerId][$fabricDesc][$colorId]['program_qty'];
									$bookingBalance = $row['grey_fabric'] - $programQty;
										
									//for requisition
									$requisitionNo = implode(', ', $requisitionData[$bookingNO][$buyerId][$fabricDesc][$colorId]['requisition_no']);
									$requisitionQty = $requisitionData[$bookingNO][$buyerId][$fabricDesc][$colorId]['requisition_qty'];
									$requisitionBalance = $programQty - $requisitionQty;
									
									//for issue
									$issueId = implode(', ', $issueData[$bookingNO][$buyerId][$fabricDesc][$colorId]['issue_id']);
									$issueQty = $issueData[$bookingNO][$buyerId][$fabricDesc][$colorId]['issue_qty'];
									$tmp_issue_desc = array();
									if($issueData[$bookingNO][$buyerId][$fabricDesc][$colorId]['issue_desc'] != '')
									{
										$tmp_issue_desc[0] = $issueData[$bookingNO][$buyerId][$fabricDesc][$colorId]['issue_desc'];
									}
									if(!empty($issueData[$bookingNO][$buyerId][$fabricDesc][$colorId]['lot']))
									{
										$tmp_issue_desc[1] = implode(', ',$issueData[$bookingNO][$buyerId][$fabricDesc][$colorId]['lot']);
									}
									$issue_desc = implode(', ',$tmp_issue_desc);
									
									//for production
									//$productionQty = $productionData[$bookingNO][$buyerId][$fabricDesc][$colorId]['production_qty'];
									
									//for gery fabric receive
									$greyFabricproductionQty = 0;
									$greyFabricReceiveQty = 0;
									$greyFabricIssueQty = 0;
									$productionId = array();
									foreach($programData[$bookingNO][$buyerId][$fabricDesc][$colorId]['program_no'] as $pogNo)
									{
										$greyFabricproductionQty += $greyFabricData[$pogNo][$colorId]['production_qty'];
										$greyFabricReceiveQty += $greyFabricData[$pogNo][$colorId]['receive_qty'];
										$greyFabricIssueQty += $greyFabricData[$pogNo][$colorId]['issue_qty'];
										
										foreach($productionIdArr[$pogNo][$colorId] as $pId)
										{
											$productionId[$pId] = $pId;
										}
									}
									
									/*$greyFabricproductionQty = $greyFabricData[$pogNo]['production_qty'];
									$greyFabricReceiveQty = $greyFabricData[$pogNo]['receive_qty'];
									$greyFabricIssueQty = $greyFabricData[$pogNo]['issue_qty'];
									
									foreach($productionIdArr[$pogNo] as $pId)
									{
										$productionId[$pId] = $pId;
									}*/
									
									//for knitting Balance 
									//$knittingBalance = $programQty - $productionQty;
									$knittingBalance = $programQty - $greyFabricproductionQty;
									
									//for process loss
									$processLoss = number_format((($issueQty-$greyFabricproductionQty)/$issueQty)*100, 2);
									
									//for available
									$availableQty = $greyFabricReceiveQty - $greyFabricIssueQty;
									?>
                                    <tr bgcolor="<?php echo $bgcolor; ?>" valign="middle">
                                    	<td align="center"><?php echo $sl; ?></td>
                                    	<td align="center"><?php echo $bookingNO; ?></td>
                                    	<td><?php echo $sample_dtls[$sampleType]; ?></td>
                                    	<td><?php echo $buyer_short_name[$buyerId]; ?></td>
                                    	<td><?php echo $stypleDesc; ?></td>
                                    	<td><?php echo $fabricDesc; ?></td>
                                    	<td><?php echo $color_dtls[$colorId]; ?></td>
                                    	<td align="right"><?php echo number_format($row['grey_fabric'], 2); ?></td>
                                    	<td align="center"><?php echo $unit_of_measurement[$row['uom']]; ?></td>
                                    	<td><?php echo $programNo; ?></td>
                                    	<td align="right"><?php echo number_format($programQty, 2); ?></td>
                                    	<td align="right"><?php echo number_format($bookingBalance, 2); ?></td>
                                    	<td><?php echo $requisitionNo; ?></td>
                                    	<td align="right"><?php echo number_format($requisitionQty, 2); ?></td>
                                    	<td align="right"><?php echo number_format($requisitionBalance, 2); ?></td>
                                    	<td><?php echo $issue_desc; ?></td>
                                    	<td align="right"><a href="##" onClick="func_issue_qty_popup('<?php echo $issueId; ?>')"><?php //echo number_format($issueQty, 2); ?></a><?php echo number_format($issueQty, 2); ?></td>
                                    	<td align="right"><a href="##" onClick="func_production_qty_popup('<?php echo implode(',', $productionId); ?>')"><?php echo number_format($greyFabricproductionQty, 2); ?></a></td>
                                    	<td align="right"><?php echo number_format($knittingBalance, 2); ?></td>
                                    	<td align="right"><?php echo $processLoss; ?></td>
                                    	<td align="right"><?php echo number_format($greyFabricReceiveQty,2); ?></td>
                                    	<td align="right"><?php echo number_format($greyFabricIssueQty,2); ?></td>
                                    	<td align="right"><?php echo number_format($availableQty,2); ?></td>
                                    	<td align="right"><?php  ?></td>
                                    	<td align="right"><?php  ?></td>
                                    	<td align="right"><?php  ?></td>
                                    	<td align="right"><?php  ?></td>
                                    	<td align="right"><?php  ?></td>
                                    	<td align="right"><?php  ?></td>
                                    	<td align="right"><?php  ?></td>
                                    	<td align="right"><?php  ?></td>
                                    	<td align="right"><?php  ?></td>
                                    	<td align="right"><?php  ?></td>
                                    	<td align="right"><?php  ?></td>
                                    </tr>
                                    <?php
									$totalProgramQty += number_format($programQty, 2, '.', '');
									$totalBookingBalance += number_format($bookingBalance, 2, '.', '');
									$totalRequisitionQty += number_format($requisitionQty, 2, '.', '');
									$totalRequisitionBalance += number_format($requisitionBalance, 2, '.', '');
									$totalIssueQty += number_format($issueQty, 2, '.', '');
									$totalProductionQty += number_format($greyFabricproductionQty, 2, '.', '');
									$totalKnittingBalance += number_format($knittingBalance, 2, '.', '');
									$totalGreyFabricReceiveQty += number_format($greyFabricReceiveQty, 2, '.', '');
									$totalGreyFabricIssueQty += number_format($greyFabricIssueQty, 2, '.', '');
									$totalAvailableQty += number_format($availableQty, 2, '.', '');
								}
							}
						}
					}
				}
			}
			?>
            </tbody>
            <tfoot>
            	<tr>
                	<th align="right" colspan="10">Total</th>
                	<th><?php echo number_format($totalProgramQty, 2); ?></th>
                	<th><?php echo number_format($totalBookingBalance, 2); ?></th>
                	<th></th>
                	<th><?php echo number_format($totalRequisitionQty, 2); ?></th>
                	<th><?php echo number_format($totalRequisitionBalance, 2); ?></th>
                	<th></th>
                	<th><?php echo number_format($totalIssueQty, 2); ?></th>
                	<th><?php echo number_format($totalProductionQty, 2); ?></th>
                	<th><?php echo number_format($totalKnittingBalance, 2); ?></th>
                    <th></th>
                	<th><?php echo number_format($totalGreyFabricReceiveQty, 2); ?></th>
                	<th><?php echo number_format($totalGreyFabricIssueQty, 2); ?></th>
                	<th><?php echo number_format($totalAvailableQty, 2); ?></th>
                </tr>
            </tfoot>
        </table>
	</fieldset>    
    <?php
	foreach (glob("$user_name*.xls") as $filename)
	{		
		@unlink($filename);
	}
	
	$html=ob_get_contents();
	ob_clean();
	$name = time();
    $filename = $user_name."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo $html."####".$filename;
    exit();
}

//actn_issue_qty_popup
if ($action == "actn_issue_qty_popup")
{
    echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);
    ?>
    </head>
    <body>
    <?php
	$sqlIssue = "SELECT d.knit_id, d.requisition_no, e.cons_quantity, e.transaction_type, f.id, f.issue_number, f.issue_date, f.remarks FROM ppl_yarn_requisition_entry d, inv_transaction e, inv_issue_master f WHERE d.requisition_no = e.requisition_no AND e.mst_id = f.id AND d.status_active = 1 AND d.is_deleted = 0 AND e.receive_basis = 3 AND e.item_category = 1 AND e.transaction_type IN(2,4) AND e.status_active = 1 AND e.is_deleted = 0 AND e.requisition_no IS NOT NULL AND f.id IN(".$id.")";
	//echo $sqlIssue; die;
	$sqlIssueRslt = sql_select($sqlIssue);
	$dataArr = array();
	$dataArrRtn = array();
	foreach($sqlIssueRslt as $row)
	{
		if($row[csf('transaction_type')] == 2)
		{
			$dataArr[$row[csf('id')]]['issue_date'] = $row[csf('issue_date')];
			$dataArr[$row[csf('id')]]['issue_number'] = $row[csf('issue_number')];
			$dataArr[$row[csf('id')]]['knit_id'] = $row[csf('knit_id')];
			$dataArr[$row[csf('id')]]['requisition_no'] = $row[csf('requisition_no')];
			$dataArr[$row[csf('id')]]['cons_quantity'] = $row[csf('cons_quantity')];
			$dataArr[$row[csf('id')]]['remarks'] = $row[csf('remarks')];
		}
		else
		{
			$dataArrRtn[$row[csf('id')]]['issue_date'] = $row[csf('issue_date')];
			$dataArrRtn[$row[csf('id')]]['issue_number'] = $row[csf('issue_number')];
			$dataArrRtn[$row[csf('id')]]['knit_id'] = $row[csf('knit_id')];
			$dataArrRtn[$row[csf('id')]]['requisition_no'] = $row[csf('requisition_no')];
			$dataArrRtn[$row[csf('id')]]['cons_quantity'] = $row[csf('cons_quantity')];
			$dataArrRtn[$row[csf('id')]]['remarks'] = $row[csf('remarks')];
		}
	}
	?>
    <!--<div style="width:670px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
    <fieldset style="width:670px; margin-left:7px">
        <div id="report_container">
            <table border="1" class="rpt_table" rules="all" width="670" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th colspan="7">Yarn Issue</th>
                    </tr>
                    <tr>
                        <th width="40">SL</th>
                        <th width="80">Issue Date</th>
                        <th width="100">Issue Id</th>
                        <th width="100">Program No</th>
                        <th width="100">Requisition No</th>
                        <th width="100">Issue Qty</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                <?php
				$sl =0;
				foreach($dataArr as $row)
				{
                	$sl++;
					?>
					<tr>
                        <td align="center"><?php echo $sl; ?></td>	
                        <td align="center"><?php echo date('d-m-Y', strtotime($row['issue_date'])); ?></td>
                        <td align="center"><?php echo $row['issue_number']; ?></td>	
                        <td align="center"><?php echo $row['knit_id']; ?></td>	
                        <td align="center"><?php echo $row['requisition_no']; ?></td>	
                        <td align="right"><?php echo number_format($row['cons_quantity'], 2); ?></td>	
                        <td><?php echo $row['remarks']; ?></td>	
                    </tr>
                    <?php
					$yarnIssueQty += number_format($row['cons_quantity'], 2, '.', '');
					$totalConsQty += number_format($row['cons_quantity'], 2, '.', '');
				}
				?>
                </tbody>
                <tfoot>
                	<tr>
                    	<th colspan="5">Issue Total</th>
                        <th><?php echo number_format($yarnIssueQty,2); ?></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
            <br/>    
            <table border="1" class="rpt_table" rules="all" width="670" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th colspan="7">Yarn Return</th>
                    </tr>
                    <tr>
                        <th width="40">SL</th>
                        <th width="80">Issue Date</th>
                        <th width="100">Issue Id</th>
                        <th width="100">Program No</th>
                        <th width="100">Requisition No</th>
                        <th width="100">Return Qty</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                <?php
				$sl =0;
				foreach($dataArrRtn as $row)
				{
                	$sl++;
					?>
					<tr>
                        <td align="center"><?php echo $sl; ?></td>	
                        <td align="center"><?php echo date('d-m-Y', strtotime($row['issue_date'])); ?></td>
                        <td align="center"><?php echo $row['issue_number']; ?></td>	
                        <td align="center"><?php echo $row['knit_id']; ?></td>	
                        <td align="center"><?php echo $row['requisition_no']; ?></td>	
                        <td align="right"><?php echo number_format($row['cons_quantity'], 2); ?></td>	
                        <td><?php echo $row['remarks']; ?></td>	
                    </tr>
                    <?php
					$yarnReturnQty += number_format($row['cons_quantity'], 2, '.', '');
					$totalConsQty += number_format($row['cons_quantity'], 2, '.', '');
				}
				?>
                </tbody>
                <tfoot>
                	<tr>
                    	<th colspan="5">Return Total</th>
                        <th><?php echo number_format($yarnReturnQty,2); ?></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>    
            <table border="1" class="rpt_table" rules="all" width="670" cellpadding="0" cellspacing="0">
                <tfoot>
                	<tr>
                        <th width="40"></th>
                        <th width="80"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100">Total</th>
                        <th width="100"><?php echo number_format($yarnReturnQty,2); ?></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>    
        </div>
    </fieldset>
    </body>
    </html> 
    <?php
    exit();
}

//actn_production_qty_popup
if ($action == "actn_production_qty_popup")
{
    echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);
    ?>
    </head>
    <body>
    	<?php
		//$data_array = sql_select("select id, body_part_id, trans_id,	prod_id, febric_description_id, no_of_roll, gsm, width, grey_receive_qnty,grey_receive_qnty_pcs, reject_fabric_receive, uom, yarn_lot,yarn_prod_id, yarn_count, brand_id, shift_name, floor_id, machine_no_id, order_id, room, rack, self, store_floor, color_id, color_range_id, stitch_length, machine_dia, machine_gg,operator_name, yarn_rate, kniting_charge, order_yarn_rate, order_knitting_charge,coller_cuff_size, rate from pro_grey_prod_entry_dtls where id='$id' and status_active=1 and is_deleted=0");

		//$field_array_trans = "id, mst_id, receive_basis, pi_wo_batch_no, booking_without_order, company_id, prod_id, item_category, transaction_type, transaction_date, store_id, brand_id, order_uom, order_qnty, order_rate, order_amount, cons_uom, cons_quantity, cons_reject_qnty, cons_rate, cons_amount, balance_qnty, balance_amount, floor_id, machine_id, room, rack, self,production_floor, inserted_by, insert_date";

		//, company_id, booking_without_order, buyer_id, store_id, location_id, knitting_source, sub_contract, knitting_location_id, yarn_issue_challan_no 
		$sql = "SELECT a.id, a.recv_number,a. receive_basis, a.booking_id, a.booking_no, a.receive_date, a.challan_no, a.knitting_company, a.knitting_source, a.remarks, b.prod_id, b.febric_description_id, b.machine_no_id, b.grey_receive_qnty, gsm, width, stitch_length FROM inv_receive_master a, pro_grey_prod_entry_dtls b WHERE a.id = b.mst_id AND a.id IN(".$id.") AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0";
		//echo $sql;
		$sqlRslt = sql_select($sql);
		$dataArr = array();
		foreach($sqlRslt as $row)
		{
			$dataArr[$row[csf('id')]]['recv_number'] = $row[csf('recv_number')];
			$dataArr[$row[csf('id')]]['receive_basis'] = $row[csf('receive_basis')];
			$dataArr[$row[csf('id')]]['booking_no'] = $row[csf('booking_no')];
			$dataArr[$row[csf('id')]]['machine_no_id'] = $row[csf('machine_no_id')];
			$dataArr[$row[csf('id')]]['receive_date'] = $row[csf('receive_date')];
			$dataArr[$row[csf('id')]]['challan_no'] = $row[csf('challan_no')];
			$dataArr[$row[csf('id')]]['knitting_company'] = $row[csf('knitting_company')];
			$dataArr[$row[csf('id')]]['remarks'] = $row[csf('remarks')];
			
			if($row[csf('knitting_source')] == 1)
			{
				$dataArr[$row[csf('id')]]['inhouse_qty'] = $row[csf('grey_receive_qnty')];
				$dataArr[$row[csf('id')]]['outside_qty'] = 0;
			}
			elseif($row[csf('knitting_source')] == 3)
			{
				$dataArr[$row[csf('id')]]['inhouse_qty'] = 0;
				$dataArr[$row[csf('id')]]['outside_qty'] = $row[csf('grey_receive_qnty')];
			}
			
			$comp = '';
			if ($row[csf('febric_description_id')] == 0 || $row[csf('febric_description_id')] == "")
			{
				$comp = return_field_value("item_description", "product_details_master", "id=".$row[csf('prod_id')]);
			}
			else
			{
				$determination_sql = sql_select("select a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=" . $row[csf('febric_description_id')]);
				if ($determination_sql[0][csf('construction')] != "")
				{
					$comp = $determination_sql[0][csf('construction')].", ";
				}
	
				foreach ($determination_sql as $d_row)
				{
					$comp .= $composition[$d_row[csf('copmposition_id')]]." ".$d_row[csf('percent')]."%, ";
				}
			}
			$comp .= $row[csf('gsm')].", ".$row[csf('width')].", ".$row[csf('stitch_length')];
			$dataArr[$row[csf('id')]]['febric_description'] = $comp;
		}
		?>
    <!--<div style="width:670px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
    <fieldset style="width:1170px; margin-left:7px">
        <div id="report_container">
            <table border="1" class="rpt_table" rules="all" width="1170" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th colspan="13">Grey Receive Information</th>
                    </tr>
                    <tr>
                        <th width="40">SL</th>
                        <th width="100">Receive Id</th>
                        <th width="100">Prod. Basis</th>
                        <th width="190">Product Details</th>
                        <th width="100">Booking / program No</th>
                        <th width="60">Machine NO</th>
                        <th width="70">Production Date</th>
                        <th width="80">Inhouse Production</th>
                        <th width="80">Outside Production</th>
                        <th width="100">Challan No</th>
                        <th width="140">Knitting Company</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                <?php
				$sl = 0;
				foreach($dataArr as $row)
				{
					$sl++;
					?>
                    <tr valign="middle">
                    	<td align="center"><?php echo $sl; ?></td>
                    	<td align="center"><?php echo $row['recv_number']; ?></td>
                    	<td><?php echo $receive_basis[$row['receive_basis']]; ?></td>
                    	<td><?php echo $row['febric_description']; ?></td>
                    	<td><?php echo $row['booking_no']; ?></td>
                    	<td><?php echo $machine_dtls[$row['machine_no_id']]; ?></td>
                    	<td align="center"><?php echo date('d-m-Y', strtotime($row['receive_date'])); ?></td>
                    	<td align="right"><?php echo number_format($row['inhouse_qty'], 2); ?></td>
                    	<td align="right"><?php echo number_format($row['outside_qty'], 2); ?></td>
                    	<td><?php echo $row['challan_no']; ?></td>
                    	<td><?php echo $company_dtls[$row['knitting_company']]; ?></td>
                    	<td><?php echo $row['remarks']; ?></td>
                    </tr>
                    <?php
					$totalInhouseQty += number_format($row['inhouse_qty'], 2, '.', '');
					$totalOutsideQty += number_format($row['outside_qty'], 2, '.', '');
				}
				?>
                </tbody>
                <tfoot>
                	<tr>
                    	<th colspan="7">Total</th>
                        <th><?php echo number_format($totalInhouseQty, 2); ?></th>
                        <th><?php echo number_format($totalOutsideQty, 2); ?></th>
                        <th colspan="3"></th>
                    </tr>
                </tfoot>
            </table>    
        </div>
    </fieldset>
    </body>
    </html>
    <?php
    exit();
}
?>