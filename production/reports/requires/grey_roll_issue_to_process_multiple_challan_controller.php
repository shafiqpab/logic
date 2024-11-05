<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" )
	header("location:login.php");

require_once('../../../includes/common.php');
$user_name=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//------------------------------------------------------------------------------------------

/*
|--------------------------------------------------------------------------
| for load_drop_down_buyer
|--------------------------------------------------------------------------
|
*/
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='".$data."' and buy.id in (select  buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );     	 
	exit();
}

/*
|--------------------------------------------------------------------------
| for load_drop_down_knitting_com
|--------------------------------------------------------------------------
|
*/
if($action=="load_drop_down_knitting_com")
{
	$data = explode("**",$data);
	$company_id=$data[1];
	
	if($data[0]==1)
	{
		echo create_drop_down( "cbo_service_company", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "-- Select --", $company_id, "","" );
	}
	else if($data[0]==3)
	{	
		echo create_drop_down( "cbo_service_company", 150, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 and b.party_type in (21,24,25) group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "" );
	}
	else
	{
		echo create_drop_down( "cbo_service_company", 150, $blank_array,"",1, "-- Select --", 0, "" );
	}
	exit();
}

/*
|--------------------------------------------------------------------------
| for show button
|--------------------------------------------------------------------------
|
*/
if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_id=str_replace("'", "", $cbo_company_name);
	$cbo_service_source=str_replace("'", "", $cbo_service_source);
	$cbo_service_company=str_replace("'", "", $cbo_service_company);
	$txt_challan_no=str_replace("'", "", $txt_challan_no);
	$start_date=str_replace("'", "", $txt_date_from);
	$end_date=str_replace("'", "", $txt_date_to);
	
	$service_source_cond = ($cbo_service_source != 0 ? " and a.dyeing_source = ".$cbo_service_source : '');
	$service_company_cond = ($cbo_service_company != 0 ? " and a.dyeing_company = ".$cbo_service_company : '');

	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.receive_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and a.receive_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}
	
	$search_field_cond="";
	if($txt_challan_no!= '')
	{
		$search_field_cond="and a.recv_number_prefix_num in(".$txt_challan_no.")";
	}
	
	if($db_type==0)
	{
		$year_field="YEAR(a.insert_date) AS YEAR,";
	}
	else if($db_type==2)
	{
		$year_field="to_char(a.insert_date,'YYYY') AS YEAR,";
	}
	else $year_field="";
	
	$sql = "select a.id AS ID,a.recv_number as CHALLAN_NO, a.company_id AS COMPANY_ID, a.wo_no AS WO_NO, $year_field a.recv_number_prefix_num AS RECV_NUMBER_PREFIX_NUM, a.recv_number AS RECV_NUMBER, a.dyeing_source AS DYEING_SOURCE, a.dyeing_company AS DYEING_COMPANY, a.receive_date AS RECEIVE_DATE, a.process_id AS PROCESS_ID, b.batch_id AS BATCH_ID from inv_receive_mas_batchroll a INNER JOIN pro_grey_batch_dtls b ON a.id = b.mst_id where a.entry_form=63 and a.status_active=1 and a.is_deleted=0 and a.company_id = ".$company_id." $service_source_cond $service_company_cond $search_field_cond $date_cond order by a.id"; 
	 //echo $sql;
	$result = sql_select($sql);
	$companyIdArr = array();
	$supplierIdArr = array();
	$batchIdArr = array();
	foreach ($result as $row)
	{
		if($row['BATCH_ID']*1 != 0)
		{
			$batchIdArr[$row['BATCH_ID']] = $row['BATCH_ID'];
		}

		$companyIdArr[$row['COMPANY_ID']] = $row['COMPANY_ID'];
		if($row['DYEING_SOURCE'] == 1)
		{
			$companyIdArr[$row['DYEING_COMPANY']] = $row['DYEING_COMPANY'];
		}
		else
		{
			$supplierIdArr[$row['DYEING_COMPANY']] = $row['DYEING_COMPANY'];
		}
	}
	
	$company_arr = return_library_array( "select id, company_name from lib_company where id in(".implode(',', $companyIdArr).")",'id','company_name');
	$supllier_arr = return_library_array( "select id, supplier_name from lib_supplier where id in(".implode(',', $supplierIdArr).")",'id','supplier_name');
	$batch_arr = return_library_array( "select id, batch_no from pro_batch_create_mst where id in(".implode(',', $batchIdArr).")",'id','batch_no');

	$dataArr = array();
	foreach ($result as $row)
	{
		$dataArr[$row['RECV_NUMBER']]['ID'] =  $row['ID'];
		$dataArr[$row['RECV_NUMBER']]['COMPANY_ID'] =  $row['COMPANY_ID'];
		$dataArr[$row['RECV_NUMBER']]['WO_NO'] =  $row['WO_NO'];
		$dataArr[$row['RECV_NUMBER']]['DYEING_SOURCE'] =  $row['DYEING_SOURCE'];
		$dataArr[$row['RECV_NUMBER']]['DYEING_COMPANY'] =  $row['DYEING_COMPANY'];
		$dataArr[$row['RECV_NUMBER']]['PROCESS_ID'] =  $row['PROCESS_ID'];
		$dataArr[$row['RECV_NUMBER']]['RECEIVE_DATE'] =  $row['RECEIVE_DATE'];
		$dataArr[$row['RECV_NUMBER']]['CHALLAN_NO'] =  $row['CHALLAN_NO'];
		
		
		
		if($row['BATCH_ID']*1 != 0)
		{
			$dataArr[$row['RECV_NUMBER']]['BATCH_ID'][$row['BATCH_ID']] =  $batch_arr[$row['BATCH_ID']];
		}
	}
	//echo "<pre>";
	//print_r($batchIdArr);
	?>
    <fieldset>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="990" class="rpt_table">
        <thead>
            <th width="30"></th>
            <th width="40">SL</th>
            <th width="150">Company</th>
            <th width="100">Challan No</th>
            <th width="100">WO No</th>
            <th width="120">Service Source</th>
            <th width="140">Service Company</th>
            <th width="110">Process</th>
            <th width="100">Batch</th>
            <th>Issue date</th>
        </thead>
	</table>
	<div style="width:990px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="970" class="rpt_table" id="tbl_list_search">  
        <?
		$i=1;
		$noOfServiceCompany = array();
		foreach ($dataArr as $recvNo=>$row)
		{  
			if ($i%2==0)
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";	
			 
			$dye_comp = "";
			if($row['DYEING_SOURCE']==1)
				$dye_comp = $company_arr[$row['DYEING_COMPANY']]; 
			else
				$dye_comp = $supllier_arr[$row['DYEING_COMPANY']];
			
			$noOfServiceCompany[$row['DYEING_COMPANY']] = $row['DYEING_COMPANY'];
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" valign="middle"> 
				<td width="30" align="center">
                    <input type="checkbox" id="tbl_<? echo $i; ?>"  onClick="fnc_checkbox_check(<? echo $i; ?>);"  />
                    <input type="hidden" id="mstidall_<? echo $i; ?>" value="<? echo $row['ID']; ?>" />
                    <input type="hidden" id="issue_to_<? echo $i; ?>" value="<? echo $row['DYEING_COMPANY']; ?>" />	
                    <input type="hidden" id="emb_source_<? echo $i; ?>" value="<? echo $row['DYEING_SOURCE']; ?>" />
                </td>
                <td width="40" align="center"><? echo $i; ?></td>
				<td width="150" style="word-wrap:break-word;"><? echo $company_arr[$row['COMPANY_ID']]; ?></td>
				<td width="100" style="word-wrap:break-word;"><? echo $row['CHALLAN_NO']; ?></td>
				<td width="100" align="center" style="word-wrap:break-word;"><? echo $row['WO_NO']; ?></td>
				<td width="120" style="word-wrap:break-word;"><? echo $knitting_source[$row['DYEING_SOURCE']]; ?></td>
				<td width="140" style="word-wrap:break-word;"><? echo $dye_comp; ?></td>
				<td width="110" style="word-wrap:break-word;"><? echo $conversion_cost_head_array[$row['PROCESS_ID']]; ?></td>
				<td width="100" style="word-wrap:break-word;"><? echo implode(', ',$row['BATCH_ID']); ?></td>
				<td align="center"><? echo change_date_format($row['RECEIVE_DATE']); ?></td>
			</tr>
			<?
            $i++;
		}
		if(count($noOfServiceCompany) == 1)
		{
			?>
			<tfoot>
				<td colspan="10" height="30" valign="middle" style="padding-left:8px; font-weight:bold;"><input type="checkbox" id="check_all"  onClick="func_check_all()"  />&nbsp;Cheak All</td>	
			</tfoot>
			<?php
		}
		?>
        </table>
    </div>
    </fieldset>
	<?	
    exit();
}

/*
|--------------------------------------------------------------------------
| for print button-1
|--------------------------------------------------------------------------
|
*/
if($action=="action_print_1")
{
	$expData = explode('*',$data);
    $company_id = $expData[0]; 
    $id = implode(',',explode("_",$expData[1]));
	
	$sqlBatchBarcode="SELECT r.barcode_no FROM pro_roll_details r WHERE r.entry_form = 63 AND r.roll_no>0 AND r.status_active = 1 AND r.is_deleted = 0 AND r.mst_id IN( ".$id.")";
	//echo $sqlBatchBarcode; die;
	
	$sql=" SELECT a.id AS ID, a.entry_form AS ENTRY_FORM, a.company_id AS COMPANY_ID, a.receive_basis AS RECEIVE_BASIS, a.booking_no AS RECEIVE_BASIS, a.booking_id AS BOOKING_ID, a.knitting_source AS KNITTING_SOURCE, a.knitting_company AS KNITTING_COMPANY, b.id as DTLS_ID, b.prod_id AS PROD_ID, b.body_part_id AS BODY_PART_ID, b.febric_description_id AS FEBRIC_DESCRIPTION_ID, b.gsm AS GSM, b.width AS WIDTH, b.color_id AS COLOR_ID, c.mst_id AS MST_ID, c.barcode_no AS BARCODE_NO, c.id as ROLL_ID, c.roll_no AS ROLL_NO, c.po_breakdown_id AS PO_BREAKDOWN_ID, c.qnty AS QNTY, c.qc_pass_qnty_pcs AS QC_PASS_QNTY_PCS FROM inv_receive_master a INNER JOIN pro_grey_prod_entry_dtls b ON a.id = b.mst_id INNER JOIN pro_roll_details c ON b.id = c.dtls_id WHERE a.receive_basis<>9 AND a.entry_form IN(2,22) AND c.entry_form IN(2,22) AND c.roll_no>0 AND c.status_active = 1 AND c.is_deleted = 0 AND c.barcode_no IN(".$sqlBatchBarcode.")";
	$sqlRslt=sql_select($sql);
	$barCode=array();
	$poBreakdownId=array();
	$yarnCountDeterminId=array();
	$bookingId=array();
	$dataArr = array();
	foreach($sqlRslt as $row)
	{
		$barCode[$row['BARCODE_NO']]=$row['BARCODE_NO'];
		$poBreakdownId[$row['PO_BREAKDOWN_ID']]=$row['PO_BREAKDOWN_ID'];
		$yarnCountDeterminId[$row['FEBRIC_DESCRIPTION_ID']]=$row['FEBRIC_DESCRIPTION_ID'];
		$bookingId[$row['BOOKING_ID']]=$row['BOOKING_ID'];
		
		$dataArr[$row['BARCODE_NO']]['body_part_id'] = $row['BODY_PART_ID'];
		$dataArr[$row['BARCODE_NO']]['febric_description_id'] = $row['FEBRIC_DESCRIPTION_ID'];
		$dataArr[$row['BARCODE_NO']]['width'] = $row['WIDTH'];
		$dataArr[$row['BARCODE_NO']]['gsm'] = $row['GSM'];
	}
	//echo "<pre>";
	//print_r($yarnCountDeterminId); die;
	
	$sql2 = "SELECT a.wo_no AS WO_NO, a.recv_number AS RECV_NUMBER, a.dyeing_source AS DYEING_SOURCE, a.dyeing_company AS DYEING_COMPANY, a.receive_date AS RECEIVE_DATE, b.roll_wgt AS ROLL_WGT, b.roll_id AS ROLL_ID, b.order_id AS ORDER_ID, b.color_id AS COLOR_ID, b.batch_id AS BATCH_ID, b.process_id AS PROCESS_ID, c.barcode_no AS BARCODE_NO FROM inv_receive_mas_batchroll a INNER JOIN pro_grey_batch_dtls b ON a.id = b.mst_id INNER JOIN pro_roll_details c ON a.id = c.mst_id AND b.id = c.dtls_id WHERE a.id IN(".$id.") AND a.company_id = ".$company_id." AND a.entry_form = 63 AND c.entry_form = 63 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 ORDER BY a.id"; 
	//echo $sql2;
	$sql2Rslt = sql_select($sql2);
	$colorIdArr = array();
	$companySupplierArr = array();
	$processPoBreakdownId = array();
	foreach($sql2Rslt as $row)
	{
		//for company supplier
		$companySupplierArr[$row['DYEING_COMPANY']] = $row['DYEING_COMPANY'];
		
		//for color id
		$expColor = explode(',',$row['COLOR_ID']);
		foreach($expColor as $key=>$val)
		{
			$colorIdArr[$val] = $val;
		}
		$processPoBreakdownId[$row['ORDER_ID']]=$row['ORDER_ID'];
	}
	//echo "<pre>";
	//print_r($companySupplierArr);
	
	$color_arr = return_library_array("select id, color_name from lib_color where id in(".implode(',',$colorIdArr).")","id","color_name");
	$company_name_array = return_library_array("select id,company_name from lib_company where id in(".implode(',',$companySupplierArr).")", "id", "company_name");
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier where id in(".implode(',',$companySupplierArr).")",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id, address_1 from lib_supplier where id in(".implode(',',$companySupplierArr).")",'id','address_1');
	$buyer_name_array=return_library_array("select id, short_name from lib_buyer", "id", "short_name");

	//for batch
	$batchArray = get_batchFor_GreyRollIssueToProcess($barCode);
	//echo "<pre>";
	//print_r($batchArray); die;
	
	//for Yarn Count Determin
	//$yarnCountDeterminArray = get_constructionComposition($yarnCountDeterminId);
	//echo "<pre>";
	//print_r($yarnCountDeterminArray);
	
	//for buyer
	// $poArray = get_buyerFor_GreyRollIssueToProcess($poBreakdownId);
	$poArray = get_buyerFor_GreyRollIssueToProcess($processPoBreakdownId);
	//echo "<pre>";
	//print_r($poArray); die;
	
	//for dia type
	//$diaTypeArray = get_dia_type($bookingId);

	$rptArr = array();
	$noOfRoll = 0;
	$service_company = '';
	$issue_date = '';
	foreach($sql2Rslt as $row)
	{
		$service_company = get_knitting_company_details($row['DYEING_SOURCE'], $row['DYEING_COMPANY']);
		$service_company_address = $supplier_address_arr[$row['DYEING_COMPANY']];
		$issue_date = $row['RECEIVE_DATE'];

		$body_part_id = $dataArr[$row['BARCODE_NO']]['body_part_id'];
		$batch_no = $batchArray[$row['BARCODE_NO']][$row['ORDER_ID']]['batch_no'];
		$febric_description_id = $dataArr[$row['BARCODE_NO']]['febric_description_id'];
		$dia = $dataArr[$row['BARCODE_NO']]['width'];
		$gsm = $dataArr[$row['BARCODE_NO']]['gsm'];

		$rptArr[$row['RECV_NUMBER']][$body_part_id][$batch_no][$febric_description_id][$dia][$gsm]['booking_no'] = $row['WO_NO'];
		$rptArr[$row['RECV_NUMBER']][$body_part_id][$batch_no][$febric_description_id][$dia][$gsm]['process_id'] = $row['PROCESS_ID'];
		$rptArr[$row['RECV_NUMBER']][$body_part_id][$batch_no][$febric_description_id][$dia][$gsm]['buyer'] = $poArray[$row['ORDER_ID']]['buyer_name'];
		$rptArr[$row['RECV_NUMBER']][$body_part_id][$batch_no][$febric_description_id][$dia][$gsm]['file_no'] = $poArray[$row['ORDER_ID']]['file_no'];
		$rptArr[$row['RECV_NUMBER']][$body_part_id][$batch_no][$febric_description_id][$dia][$gsm]['ref_no'] = $poArray[$row['ORDER_ID']]['ref_no'];
		$rptArr[$row['RECV_NUMBER']][$body_part_id][$batch_no][$febric_description_id][$dia][$gsm]['color'] = get_color_details($row['COLOR_ID']);
		
		if(isset($rptArr[$row['RECV_NUMBER']][$body_part_id][$batch_no][$febric_description_id][$dia][$gsm]['no_of_roll']))
		{
			$noOfRoll = $rptArr[$row['RECV_NUMBER']][$body_part_id][$batch_no][$febric_description_id][$dia][$gsm]['no_of_roll'];
		}
		else
		{
			$noOfRoll = 0;	
		}
		
		$rptArr[$row['RECV_NUMBER']][$body_part_id][$batch_no][$febric_description_id][$dia][$gsm]['no_of_roll'] = $noOfRoll+1;
		$rptArr[$row['RECV_NUMBER']][$body_part_id][$batch_no][$febric_description_id][$dia][$gsm]['roll_wgt'] += $row['ROLL_WGT'];
	}
	//echo "<pre>";
	//print_r($rptArr);
	
	//for company details
	$company_array=array();
	$company_data=sql_select("SELECT id AS ID, company_name AS COMPANY_NAME, company_short_name AS COMPANY_SHORT_NAME, plot_no AS PLOT_NO, level_no AS LEVEL_NO, road_no AS ROAD_NO, block_no AS BLOCK_NO, country_id AS COUNTRY_ID, province AS PROVINCE, city AS CITY, zip_code AS ZIP_CODE, email AS EMAIL, website AS WEBSITE FROM lib_company WHERE id=".$company_id."");
	foreach($company_data as $row)
	{
		$company_array['name']=$row['COMPANY_NAME'];
		$company_array['shortname']=$row['COMPANY_SHORT_NAME'];
		$company_array['plot_no']=$row['PLOT_NO'];
		$company_array['level_no']=$row['LEVEL_NO'];
		$company_array['road_no']=$row['ROAD_NO'];
		$company_array['block_no']=$row['BLOCK_NO'];
		$company_array['city']=$row['CITY'];
		$company_array['zip_code']=$row['ZIP_CODE'];
		$company_array['province']=$row['PROVINCE'];
		$company_array['country_id']=$row['COUNTRY_ID'];
		$company_array['email']=$row['EMAIL'];
		$company_array['website']=$row['WEBSITE'];
	}
	
	//for company logo
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='".$expData[0]."'","image_location");
	?>
    <table width="1220" cellspacing="0">
        <tr>
            <td width="200" rowspan="3">
                <img src="../../<? echo $image_location; ?>" height="70" width="200" />
            </td>
            <td colspan="6" align="center" style="font-size:22px">
                <strong><? echo $company_array['name']; ?></strong>
            </td>
            <td width="200"></td>
        </tr>
        <tr class="form_caption">
            <td colspan="6" align="center" style="font-size:14px">  
                Plot No: <? echo $company_array['plot_no']; ?> 
                Level No: <? echo $company_array['level_no']?>
                Road No: <? echo $company_array['road_no']; ?> 
                Block No: <? echo $company_array['block_no'];?> 
                City No: <? echo $company_array['city'];?> 
                Zip Code: <? echo $company_array['zip_code']; ?><br> 
                Province No: <?php echo $company_array['province'];?> 
                Country: <? echo $country_arr[$company_array['country_id']]; ?> 
                Email Address: <? echo $company_array['email'];?> 
                Website No: <? echo $company_array['website'];?>
            </td>
            <td></td>  
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:18px"><strong><u>Service  Fabric Delivery Challan</strong></td>
            <td></td>
        </tr>
        <tr>
            <td colspan="8">&nbsp;</td>
        </tr>
        <tr>
            <td><strong>Service Company</strong></td>
            <td width="10">:</td>
            <td width="150px" colspan="3"><? echo $service_company; ?></td>
            <td width="100"><strong>Issue Date</strong></td>
            <td width="10">:</td>
            <td><? echo change_date_format($issue_date); ?></td>
        </tr>
        <tr>
            <td><strong>Address</strong></td>
            <td>:</td>
            <td><?php echo $service_company_address; ?></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td><strong>Vechical No</strong></td>
            <td>:</td>
            <td></td>
        </tr>
        <tr>
            <td><strong>Receiver Name</strong></td>
            <td>:</td>
            <td></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </table>
    
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1220" class="rpt_table">
        <thead bgcolor="#dddddd">
            <th width="40">SL</th>
            <th width="200">Challan No</th>
            <th width="200">Booking No</th>
            <th width="200">Process</th>
            <th width="150">Buyer</th>
            <th width="100">File No</th>
            <th width="100">Ref. No</th>
            <th width="120">Body Part</th>
            <th width="100">Batch No</th>
            <th width="100">Color</th>
            <th width="200">Fabrication</th>
            <th width="60">Dia</th>
            <th width="60">GSM</th>
            <th width="100">No Of Roll</th>
            <th>Wgt</th>
        </thead>
        <tbody>
        <?php
		$sl = 0;
		$total_roll = 0; 
		$total_wgt = 0; 
		foreach($rptArr as $challanNo=>$challanNoArr)
		{
			foreach($challanNoArr as $bodyPart=>$bodyPartArr)
			{
				foreach($bodyPartArr as $batchNo=>$batchNoArr)
				{
					foreach($batchNoArr as $fabric=>$fabricArr)
					{
						foreach($fabricArr as $dia=>$diaArr)
						{
							foreach($diaArr as $gsm=>$row)
							{
								$sl++;
								//tmp solution
								$yarnCountDeterminArray = get_constructionComposition($fabric);
								?>
                                <tr>
                                	<td align="center"><?php echo $sl; ?></td>
                                	<td align="center"><?php echo $challanNo; ?></td>
                                	<td align="center"><?php echo $row['booking_no']; ?></td>
                                	<td><?php echo $conversion_cost_head_array[$row['process_id']]; ?></td>
                                	<td><?php echo $row['buyer']; ?></td>
                                	<td><?php echo $row['file_no']; ?></td>
                                	<td><?php echo $row['ref_no']; ?></td>
                                	<td><?php echo $body_part[$bodyPart]; ?></td>
                                	<td><?php echo $batchNo; ?></td>
                                	<td><?php echo $row['color']; ?></td>
                                	<td><?php echo $yarnCountDeterminArray[$fabric]; ?></td>
                                	<td align="center"><?php echo $dia; ?></td>
                                	<td align="center"><?php echo $gsm; ?></td>
                                	<td align="center"><?php echo $row['no_of_roll']; ?></td>
                                	<td align="right"><?php echo number_format($row['roll_wgt'], 2); ?></td>
                                </tr>
                                <?php
								$total_roll += $row['no_of_roll'];
								$total_wgt += $row['roll_wgt'];							
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
            	<th colspan="13" align="right">Total&nbsp;</th>
                <th align="center"><?php echo number_format($total_roll); ?></th>
                <th align="center"><?php echo number_format($total_wgt, 2); ?></th>
            </tr>
        </tfoot>
	</table>
	<?php	
    exit();
}

/*
|--------------------------------------------------------------------------
| for print button-2
|--------------------------------------------------------------------------
|
*/
if($action=="action_print_2")
{
	$expData = explode('*',$data);
    $company_id = $expData[0]; 
    $id = implode(',',explode("_",$expData[1]));
	
	$sqlBatchBarcode="SELECT r.barcode_no FROM pro_roll_details r WHERE r.entry_form = 63 AND r.roll_no>0 AND r.status_active = 1 AND r.is_deleted = 0 AND r.mst_id IN( ".$id.")";
	//echo $sqlBatchBarcode; die;
	
	$sql=" SELECT a.id AS ID, a.entry_form AS ENTRY_FORM, a.company_id AS COMPANY_ID, a.receive_basis AS RECEIVE_BASIS, a.booking_no AS RECEIVE_BASIS, a.booking_id AS BOOKING_ID, a.knitting_source AS KNITTING_SOURCE, a.knitting_company AS KNITTING_COMPANY, b.id as DTLS_ID, b.prod_id AS PROD_ID, b.body_part_id AS BODY_PART_ID, b.febric_description_id AS FEBRIC_DESCRIPTION_ID, b.gsm AS GSM, b.width AS WIDTH, b.color_id AS COLOR_ID, c.mst_id AS MST_ID, c.barcode_no AS BARCODE_NO, c.id as ROLL_ID, c.roll_no AS ROLL_NO, c.po_breakdown_id AS PO_BREAKDOWN_ID, c.qnty AS QNTY, c.qc_pass_qnty_pcs AS QC_PASS_QNTY_PCS FROM inv_receive_master a INNER JOIN pro_grey_prod_entry_dtls b ON a.id = b.mst_id INNER JOIN pro_roll_details c ON b.id = c.dtls_id WHERE a.receive_basis<>9 AND a.entry_form IN(2,22) AND c.entry_form IN(2,22) AND c.roll_no>0 AND c.status_active = 1 AND c.is_deleted = 0 AND c.barcode_no IN(".$sqlBatchBarcode.")";
	$sqlRslt=sql_select($sql);
	$barCode=array();
	$poBreakdownId=array();
	$yarnCountDeterminId=array();
	$bookingId=array();
	$dataArr = array();
	foreach($sqlRslt as $row)
	{
		$barCode[$row['BARCODE_NO']]=$row['BARCODE_NO'];
		$poBreakdownId[$row['PO_BREAKDOWN_ID']]=$row['PO_BREAKDOWN_ID'];
		$yarnCountDeterminId[$row['FEBRIC_DESCRIPTION_ID']]=$row['FEBRIC_DESCRIPTION_ID'];
		$bookingId[$row['BOOKING_ID']]=$row['BOOKING_ID'];
		
		$dataArr[$row['BARCODE_NO']]['body_part_id'] = $row['BODY_PART_ID'];
		$dataArr[$row['BARCODE_NO']]['febric_description_id'] = $row['FEBRIC_DESCRIPTION_ID'];
		$dataArr[$row['BARCODE_NO']]['width'] = $row['WIDTH'];
		$dataArr[$row['BARCODE_NO']]['gsm'] = $row['GSM'];
	}
	//echo "<pre>";
	//print_r($barCode); die;

	$sql2 = "SELECT a.wo_no AS WO_NO, a.recv_number AS RECV_NUMBER, a.dyeing_source AS DYEING_SOURCE, a.dyeing_company AS DYEING_COMPANY, a.receive_date AS RECEIVE_DATE, b.roll_wgt AS ROLL_WGT, b.roll_id AS ROLL_ID, b.order_id AS ORDER_ID, b.color_id AS COLOR_ID, b.batch_id AS BATCH_ID, b.process_id AS PROCESS_ID, c.barcode_no AS BARCODE_NO FROM inv_receive_mas_batchroll a INNER JOIN pro_grey_batch_dtls b ON a.id = b.mst_id INNER JOIN pro_roll_details c ON a.id = c.mst_id AND b.id = c.dtls_id WHERE a.id IN(".$id.") AND a.company_id = ".$company_id." AND a.entry_form = 63 AND c.entry_form = 63 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 ORDER BY a.id"; 
	//echo $sql2;
	$sql2Rslt = sql_select($sql2);
	$colorIdArr = array();
	$companySupplierArr = array();
	$processPoBreakdownId = array();
	foreach($sql2Rslt as $row)
	{
		//for company supplier
		$companySupplierArr[$row['DYEING_COMPANY']] = $row['DYEING_COMPANY'];
		//for color id
		$expColor = explode(',',$row['COLOR_ID']);
		foreach($expColor as $key=>$val)
		{
			$colorIdArr[$val] = $val;
		}
		$processPoBreakdownId[$row['ORDER_ID']]=$row['ORDER_ID'];
	}
	
	$color_arr = return_library_array("select id, color_name from lib_color where id in(".implode(',',$colorIdArr).")","id","color_name");
	$company_name_array = return_library_array("select id,company_name from lib_company where id in(".implode(',',$companySupplierArr).")", "id", "company_name");
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier where id in(".implode(',',$companySupplierArr).")",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id, address_1 from lib_supplier where id in(".implode(',',$companySupplierArr).")",'id','address_1');
	$buyer_name_array=return_library_array("select id, short_name from lib_buyer", "id", "short_name");

	//for batch
	$batchArray = get_batchFor_GreyRollIssueToProcess($barCode);
	//echo "<pre>";
	//print_r($batchArray); die;
	
	//for Yarn Count Determin
	//$yarnCountDeterminArray = get_constructionComposition($yarnCountDeterminId);
	//echo "<pre>";
	//print_r($yarnCountDeterminArray);
	
	//for buyer
	// $poArray = get_buyerFor_GreyRollIssueToProcess($poBreakdownId);
	$poArray = get_buyerFor_GreyRollIssueToProcess($processPoBreakdownId);
	//echo "<pre>";
	//print_r($poArray); die;
	
	//for dia type
	//$diaTypeArray = get_dia_type($bookingId);
	
	$rptArr = array();
	$noOfRoll = 0;
	$service_company = '';
	$issue_date = '';
	foreach($sql2Rslt as $row)
	{
		$service_company = get_knitting_company_details($row['DYEING_SOURCE'], $row['DYEING_COMPANY']);
		$service_company_address = $supplier_address_arr[$row['DYEING_COMPANY']];
		$issue_date = $row['RECEIVE_DATE'];
		
		$body_part_id = $dataArr[$row['BARCODE_NO']]['body_part_id'];
		$batch_no = $batchArray[$row['BARCODE_NO']][$row['ORDER_ID']]['batch_no'];
		$febric_description_id = $dataArr[$row['BARCODE_NO']]['febric_description_id'];
		$dia = $dataArr[$row['BARCODE_NO']]['width'];
		$gsm = $dataArr[$row['BARCODE_NO']]['gsm'];

		$rptArr[$row['RECV_NUMBER']][$body_part_id][$batch_no][$febric_description_id][$dia][$gsm][$row['BARCODE_NO']]['booking_no'] = $row['WO_NO'];
		$rptArr[$row['RECV_NUMBER']][$body_part_id][$batch_no][$febric_description_id][$dia][$gsm][$row['BARCODE_NO']]['process_id'] = $row['PROCESS_ID'];
		$rptArr[$row['RECV_NUMBER']][$body_part_id][$batch_no][$febric_description_id][$dia][$gsm][$row['BARCODE_NO']]['buyer'] = $poArray[$row['ORDER_ID']]['buyer_name'];
		$rptArr[$row['RECV_NUMBER']][$body_part_id][$batch_no][$febric_description_id][$dia][$gsm][$row['BARCODE_NO']]['file_no'] = $poArray[$row['ORDER_ID']]['file_no'];
		$rptArr[$row['RECV_NUMBER']][$body_part_id][$batch_no][$febric_description_id][$dia][$gsm][$row['BARCODE_NO']]['ref_no'] = $poArray[$row['ORDER_ID']]['ref_no'];
		$rptArr[$row['RECV_NUMBER']][$body_part_id][$batch_no][$febric_description_id][$dia][$gsm][$row['BARCODE_NO']]['color'] = get_color_details($row['COLOR_ID']);
		$rptArr[$row['RECV_NUMBER']][$body_part_id][$batch_no][$febric_description_id][$dia][$gsm][$row['BARCODE_NO']]['roll_wgt'] += $row['ROLL_WGT'];
	}
	
	//for company details
	$company_array=array();
	$company_data=sql_select("SELECT id AS ID, company_name AS COMPANY_NAME, company_short_name AS COMPANY_SHORT_NAME, plot_no AS PLOT_NO, level_no AS LEVEL_NO, road_no AS ROAD_NO, block_no AS BLOCK_NO, country_id AS COUNTRY_ID, province AS PROVINCE, city AS CITY, zip_code AS ZIP_CODE, email AS EMAIL, website AS WEBSITE FROM lib_company WHERE id=".$company_id."");
	foreach($company_data as $row)
	{
		$company_array['name']=$row['COMPANY_NAME'];
		$company_array['shortname']=$row['COMPANY_SHORT_NAME'];
		$company_array['plot_no']=$row['PLOT_NO'];
		$company_array['level_no']=$row['LEVEL_NO'];
		$company_array['road_no']=$row['ROAD_NO'];
		$company_array['block_no']=$row['BLOCK_NO'];
		$company_array['city']=$row['CITY'];
		$company_array['zip_code']=$row['ZIP_CODE'];
		$company_array['province']=$row['PROVINCE'];
		$company_array['country_id']=$row['COUNTRY_ID'];
		$company_array['email']=$row['EMAIL'];
		$company_array['website']=$row['WEBSITE'];
	}

	//for company logo
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='".$expData[0]."'","image_location");
	?>
    <table width="1220" cellspacing="0">
        <tr>
            <td width="200" rowspan="3">
                <img src="../../<? echo $image_location; ?>" height="70" width="200" />
            </td>
            <td colspan="6" align="center" style="font-size:22px">
                <strong><? echo $company_array['name']; ?></strong>
            </td>
            <td width="200"></td>
        </tr>
        <tr class="form_caption">
            <td colspan="6" align="center" style="font-size:14px">  
                Plot No: <? echo $company_array['plot_no']; ?> 
                Level No: <? echo $company_array['level_no']?>
                Road No: <? echo $company_array['road_no']; ?> 
                Block No: <? echo $company_array['block_no'];?> 
                City No: <? echo $company_array['city'];?> 
                Zip Code: <? echo $company_array['zip_code']; ?><br> 
                Province No: <?php echo $company_array['province'];?> 
                Country: <? echo $country_arr[$company_array['country_id']]; ?> 
                Email Address: <? echo $company_array['email'];?> 
                Website No: <? echo $company_array['website'];?>
            </td>
            <td></td>  
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:18px"><strong><u>Service  Fabric Delivery Challan</strong></td>
            <td></td>
        </tr>
        <tr>
            <td colspan="8">&nbsp;</td>
        </tr>
        <tr>
            <td><strong>Service Company</strong></td>
            <td width="10">:</td>
            <td width="150px" colspan="3"><? echo $service_company; ?></td>
            <td width="100"><strong>Issue Date</strong></td>
            <td width="10">:</td>
            <td><? echo change_date_format($issue_date); ?></td>
        </tr>
        <tr>
            <td><strong>Address</strong></td>
            <td>:</td>
            <td><?php echo $service_company_address; ?></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td><strong>Vechical No</strong></td>
            <td>:</td>
            <td></td>
        </tr>
        <tr>
            <td><strong>Receiver Name</strong></td>
            <td>:</td>
            <td></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </table>
    
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1220" class="rpt_table">
        <thead bgcolor="#dddddd">
            <th width="40">SL</th>
            <th width="200">Challan No</th>
            <th width="200">Booking No</th>
            <th width="200">Process</th>
            <th width="150">Buyer</th>
            <th width="100">File</th>
            <th width="100">Ref. No</th>
            <th width="120">Body Part</th>
            <th width="100">Batch No</th>
            <th width="100">Color</th>
            <th width="200">Fabrication</th>
            <th width="60">Dia</th>
            <th width="60">GSM</th>
            <th width="100">Barcode No</th>
            <th>Wgt</th>
        </thead>
        <tbody>
        <?php
		$sl = 0;
		$total_wgt = 0; 
		foreach($rptArr as $challanNo=>$challanNoArr)
		{
			foreach($challanNoArr as $bodyPart=>$bodyPartArr)
			{
				foreach($bodyPartArr as $batchNo=>$batchNoArr)
				{
					foreach($batchNoArr as $fabric=>$fabricArr)
					{
						foreach($fabricArr as $dia=>$diaArr)
						{
							foreach($diaArr as $gsm=>$gsmArr)
							{
								foreach($gsmArr as $barcode=>$row)
								{
									$sl++;
									//tmp solution
									$yarnCountDeterminArray = get_constructionComposition($fabric);
									?>
									<tr>
										<td align="center"><?php echo $sl; ?></td>
										<td align="center"><?php echo $challanNo; ?></td>
										<td align="center"><?php echo $row['booking_no']; ?></td>
										<td><?php echo $conversion_cost_head_array[$row['process_id']]; ?></td>
										<td><?php echo $row['buyer']; ?></td>
										<td><?php echo $row['file_no']; ?></td>
										<td><?php echo $row['ref_no']; ?></td>
										<td><?php echo $body_part[$bodyPart]; ?></td>
										<td><?php echo $batchNo; ?></td>
										<td><?php echo $row['color']; ?></td>
										<td><?php echo $yarnCountDeterminArray[$fabric]; ?></td>
										<td align="center"><?php echo $dia; ?></td>
										<td align="center"><?php echo $gsm; ?></td>
										<td align="center"><?php echo $barcode; ?></td>
										<td align="right"><?php echo number_format($row['roll_wgt'], 2); ?></td>
									</tr>
									<?php
									$total_wgt += $row['roll_wgt'];							
								}
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
            	<th colspan="14" align="right">Total&nbsp;</th>
                <th align="center"><?php echo number_format($total_wgt, 2); ?></th>
            </tr>
        </tfoot>
	</table>
	<?php	
    exit();
}

//all function
//batch
function get_batchFor_GreyRollIssueToProcess($barCode)
{
	$data=array();
	$sqlBatch=sql_select("SELECT a.id AS ID, a.batch_no AS BATCH_NO, a.color_id AS COLOR_ID, b.po_id AS PO_ID, b.barcode_no AS BARCODE_NO  
	FROM pro_batch_create_mst a 
	INNER JOIN pro_batch_create_dtls b ON a.id = b.mst_id
	WHERE a.status_active=1 
	AND a.is_deleted = 0 
	AND b.barcode_no IN(".implode(",",$barCode).")");

	foreach($sqlBatch as $row)
	{
		$data[$row['BARCODE_NO']][$row['PO_ID']]['batch_id']=$row['ID'];
		$data[$row['BARCODE_NO']][$row['PO_ID']]['batch_no']=$row['BATCH_NO'];	
	}
	
	return $data;
}

//Yarn Count Determin
function get_constructionComposition($yarnCountDeterminId)
{
	$i = 0;
	$id = '';
	$data = array();
	$construction = '';
	$composition_name = '';
	/*
	$sqlYarnCount = sql_select("SELECT a.id AS ID, a.construction AS CONSTRUCTION, b.percent AS PERCENT, c.composition_name AS COMPOSITION_NAME 
	FROM lib_yarn_count_determina_mst a 
	INNER JOIN lib_yarn_count_determina_dtls b ON a.id = b.mst_id
	INNER JOIN lib_composition_array c ON b.copmposition_id = c.id 
	WHERE a.id IN(".implode(",",$yarnCountDeterminId).")");
	*/
	$sqlYarnCount = sql_select("SELECT a.id AS ID, a.construction AS CONSTRUCTION, b.percent AS PERCENT, c.composition_name AS COMPOSITION_NAME 
	FROM lib_yarn_count_determina_mst a 
	INNER JOIN lib_yarn_count_determina_dtls b ON a.id = b.mst_id
	INNER JOIN lib_composition_array c ON b.copmposition_id = c.id 
	WHERE a.id IN(".$yarnCountDeterminId.")");
	foreach( $sqlYarnCount as $row )
	{
		$id=$row['ID'];
		if($i==0)
		{
			$construction.= $row['CONSTRUCTION'].", ";
			$i++;
		}
		
		if($composition_name != '')
		{
			$composition_name .= ', ';
		}
		$composition_name .= $row['COMPOSITION_NAME']." ".$row['PERCENT']."%";
	}
	$data[$id] = $construction.$composition_name;
	return $data;
}

//buyer
function get_buyerFor_GreyRollIssueToProcess($poBreakdownId)
{
	global $buyer_name_array;
	$data=array();
	$sqlPo=sql_select("SELECT a.job_no AS JOB_NO, a.job_no_prefix_num AS JOB_NO_PREFIX_NUM, a.buyer_name AS BUYER_NAME, a.style_ref_no AS STYLE_REF_NO, a.insert_date AS INSERT_DATE, b.po_number AS PO_NUMBER, b.id AS ID, b.file_no AS FILE_NO, b.grouping AS REF_NO 
	FROM wo_po_details_master a 
	INNER JOIN wo_po_break_down b ON a.job_no = b.job_no_mst
	WHERE b.id IN(".implode(",",$poBreakdownId).")");
	foreach($sqlPo as $row)
	{
		$data[$row['ID']]['job_no']=$row['JOB_NO'];
		$data[$row['ID']]['buyer_name']=$buyer_name_array[$row['BUYER_NAME']];
		$data[$row['ID']]['style_ref_no']=$row['STYLE_REF_NO'];
		$data[$row['ID']]['year']=date('Y',strtotime($row['INSERT_DATE']));
		$data[$row['ID']]['po_number']=$row['PO_NUMBER'];
		$data[$row['ID']]['file_no']=$row['FILE_NO'];
		$data[$row['ID']]['ref_no']=$row['REF_NO'];
	}
	return $data;
}

function get_color_details($colorId)
{
	global $color_arr;
	$colorName='';
	$expColorId=explode(",",$colorId);
	foreach($expColorId as $id)
	{
		if($id>0)
			$colorName.=$color_arr[$id].", ";
	}
	$colorName=chop($colorName,', ');
	return $colorName;
}

//knitting_company
function get_knitting_company_details($knittingSource, $knittingCompany)
{ 
	global $company_name_array;
	global $supplier_arr;
	$data='';
	if($knittingSource == 1)
	{
		$data=$company_name_array[$knittingCompany];
	}
	else if($knittingSource == 3 )
	{
		$data=$supplier_arr[$knittingCompany];
	}
	return $data;
}

//receive_basis
function get_receive_basis($entryForm, $receiveBasis)
{
	$data=array();
	if(($entryForm==2 && $receiveBasis==0) || ($entryForm==22 && ($receiveBasis==4 || $receiveBasis==6)))
	{
		$data['id']=0;
		$data['dtls']='Independent';
	}
	else if(($entryForm==2 && $receiveBasis==1) || ($entryForm==22 && $receiveBasis==2)) 
	{
		$data['id']=2;
		$data['dtls']="Booking";
	}
	else if($entryForm==2 && $receiveBasis==2) 
	{
		$data['id']=3;
		$data['dtls']="Knitting Plan";
	}
	else if($entryForm==22 && $receiveBasis==1) 
	{
		$data['id']=1;
		$data['dtls']="PI";
	}
	return $data;
}

//dia type
function get_dia_type($bookingId)
{
	$sqlDiaType="SELECT id AS ID, width_dia_type AS WIDTH_DIA_TYPE 
		FROM ppl_planning_info_entry_dtls 
		WHERE id IN(".implode(",",$bookingId).")";
	$resultdiaType=sql_select($sqlDiaType);
	$data_diaType = array();
	foreach($resultdiaType as $row)
	{
		$data_diaType[$row['ID']]=$row['WIDTH_DIA_TYPE'];
	}
	return $data_diaType;
}
?>