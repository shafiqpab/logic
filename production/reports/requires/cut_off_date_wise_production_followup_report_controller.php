<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );
	exit();
}


if($action=="report_generate")
{
    $process = array( &$_POST );
	//var_dump($process); die();
    extract(check_magic_quote_gpc( $process ));

	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_style_no=str_replace("'","",$txt_style_no);
	$hidden_job_id=str_replace("'","",$hidden_job_id);
	$txt_po_no=str_replace("'","",$txt_po_no);
	$hidden_po_id=str_replace("'","",$hidden_po_id);
	$cbo_date_type=str_replace("'","",$cbo_date_type);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);


	if($txt_date_from && $txt_date_to)
	{
		if($cbo_date_type==1){
			$whereCon .= " and d.CUTUP_DATE between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";

		}
		else{
			$whereCon .= " and d.COUNTRY_SHIP_DATE between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";

		}
	}

	if($cbo_company_name) $whereCon .= " and a.company_name='$cbo_company_name'";
	if($cbo_buyer_name) $whereCon .= " and a.buyer_name in(".$cbo_buyer_name.")";
	if($txt_job_no) $whereCon .= " and a.JOB_NO like('%$txt_job_no')";
	if($txt_style_no) $whereCon .= " and a.style_ref_no like('%$txt_style_no')";
	if($txt_po_no) $whereCon .= " and b.po_number like('%$txt_po_no')";



	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	//$country_arr=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
	$company_arr=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$buyerArr=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	//$supplier_arr=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name"  );
	$locationArr=return_library_array( "select id,location_name from lib_location", "id", "location_name"  );



	$orderSql="SELECT a.id as JOB_ID,b.po_number,b.po_quantity, a.JOB_NO, a.company_name, a.buyer_name as BUYER_ID, a.STYLE_REF_NO, b.id as PO_ID,b.PO_NUMBER,c.gmts_item_id as ITEM_ID,d.order_quantity as ORDER_QUANTITY,d.order_total as ORDER_TOTAL,  c.SMV_PCS,d.id as COLOR_SIZE_ID,d.color_number_id as COLOR_ID,d.cutup_date as CUTUP_DATE,d.country_ship_date,d.PLAN_CUT_QNTY,(a.TOTAL_SET_QNTY*b.PO_QUANTITY) as PO_QTY,b.SHIPING_STATUS,
	c.COMPLEXITY,c.EMBELISHMENT,c.EMBRO,c.WASH,c.SPWORKS,c.GMTSDYING,c.AOP,c.BUSH,c.PEACH,c.YD from wo_po_details_master a,wo_po_break_down b,  wo_po_details_mas_set_details c, wo_po_color_size_breakdown d where a.id=b.job_id and  b.job_id=c.job_id and c.job_id=d.job_id  AND D.PO_BREAK_DOWN_ID=B.ID AND C.GMTS_ITEM_ID=D.ITEM_NUMBER_ID and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and d.status_active=1 and d.is_deleted=0 $whereCon order by a.JOB_NO,b.po_number,c.gmts_item_id,d.color_number_id";

	// echo $orderSql;die;
	$orderSqlResult = sql_select($orderSql);
	$allPoArr = array();
	$dataArr = array();
	foreach($orderSqlResult as $rows)
	{
		$key=$rows["STYLE_REF_NO"].'**'.$rows["JOB_NO"].'**'.$rows["PO_ID"].'**'.$rows["ITEM_ID"].'**'.$rows["COLOR_ID"].'**'.$rows["CUTUP_DATE"];
		$dataArr[$key][ORDER_QUANTITY] += $rows["ORDER_QUANTITY"];
		$dataArr[$key][ORDER_TOTAL] += $rows["ORDER_TOTAL"];
		$dataArr[$key][PO_NUMBER] = $rows["PO_NUMBER"];
		$dataArr[$key][PO_ID] = $rows["PO_ID"];
		$dataArr[$key][BUYER_ID] = $rows["BUYER_ID"];
		$dataArr[$key][SMV_PCS] = $rows["SMV_PCS"];
		$dataArr[$key][CUTUP_DATE] = $rows["CUTUP_DATE"];
		$dataArr[$key][PLAN_CUT_QNTY] += $rows["PLAN_CUT_QNTY"];
		$dataArr[$key][SHIPING_STATUS] = $rows["SHIPING_STATUS"];

		$itemComplexityArr=array();
		if($rows["COMPLEXITY"]>0){$itemComplexityArr[]='Complexity';}
		if($rows["EMBELISHMENT"]==1){$itemComplexityArr[]='Print';}
		if($rows["EMBRO"]==1){$itemComplexityArr[]='Embro';}
		if($rows["WASH"]==1){$itemComplexityArr[]='Wash';}
		if($rows["SPWORKS"]==1){$itemComplexityArr[]='SP. Works';}
		if($rows["GMTSDYING"]==1){$itemComplexityArr[]='Gmts Dyeing';}
		if($rows["AOP"]==1){$itemComplexityArr[]='AOP';}
		if($rows["BUSH"]==1){$itemComplexityArr[]='Brushing';}
		if($rows["PEACH"]==1){$itemComplexityArr[]='Peached Finish';}
		if($rows["YD"]==1){$itemComplexityArr[]='Yarn Dyeing';}

		$dataArr[$key][COMPLEXITY] = implode(', ',$itemComplexityArr);

		$orderQtyArr[$rows["JOB_NO"]][$rows["PO_ID"]] = $rows["PO_QTY"];
		$orderSpanArr[$rows["JOB_NO"]][$rows["PO_ID"]][$key] = 1;
		$orderColorSpanArr[$rows["JOB_NO"]][$rows["PO_ID"]][$rows["ITEM_ID"]][$rows["COLOR_ID"]][$key] = 1;

		$allPoArr[$rows["PO_ID"]]=$rows["PO_ID"];
		$allJobArr[$rows["JOB_ID"]]=$rows["JOB_ID"];
		$allColorSizeIdArr[$rows["COLOR_SIZE_ID"]]=$rows["COLOR_SIZE_ID"];
		$colorSizeIdWiseCutupDateArr[$rows["COLOR_SIZE_ID"]]=$rows["CUTUP_DATE"];
	}

	// ============================================ FOR PRODUCTION ================================================

  	if($db_type==0)
	{
		$loacation_field="group_concat(d.LOCATION) as LOCATION";
	}
	else
	{
		$loacation_field="(listagg(d.LOCATION,',') within group (order by d.LOCATION)) as LOCATION";
	}



	$productionSql="SELECT c.JOB_NO_MST, c.PO_BREAK_DOWN_ID as PO_ID, c.item_number_id as ITEM_ID,c.color_number_id as COLOR_ID,e.color_size_break_down_id as COLOR_SIZE_ID,

	(case when d.production_type=1 and e.production_type=1 then e.production_qnty else 0 end ) as CUTTING,
	(case when d.production_type=4 and e.production_type=4 then e.production_qnty else 0 end ) as SEWING_INPUT,
	(case when d.production_type=5 and e.production_type=5 then e.production_qnty else 0 end ) as SEWING_OUTPUT,
	(case when d.production_type=7 and e.production_type=7 then e.production_qnty else 0 end ) as IRON,
	(case when d.production_type=8 and e.production_type=8 then e.production_qnty else 0 end ) as PACKING,

	(case when d.production_type=2 and e.production_type=2 and d.embel_name=1 then e.production_qnty else 0 end) as PRINT_SEND,
	(case when d.production_type=3 and e.production_type=3 and d.embel_name=1 then e.production_qnty else 0 end) as PRINT_REC,
	(case when d.production_type=2 and e.production_type=2 and d.embel_name=2 then e.production_qnty else 0 end) as EMB_SEND,
	(case when d.production_type=3 and e.production_type=3 and d.embel_name=2 then e.production_qnty else 0 end) as EMB_REC

	from wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e

	where d.po_break_down_id=c.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0  ".where_con_using_array($allPoArr,0,'c.PO_BREAK_DOWN_ID')." and d.production_type in(1,2,3,4,5,7,8)";
	  //echo $productionSql;die;
	$productionSqlRes = sql_select($productionSql);
	$proDataArr = array();
	foreach($productionSqlRes as $row)
	{
		$key=$row[JOB_NO_MST].'**'.$row[PO_ID].'**'.$row[ITEM_ID].'**'.$row[COLOR_ID].'**'.$colorSizeIdWiseCutupDateArr[$row[COLOR_SIZE_ID]];
		$proDataArr[$key][CUTTING]+=$row[CUTTING];
		$proDataArr[$key][SEWING_INPUT]+=$row[SEWING_INPUT];
		$proDataArr[$key][SEWING_OUTPUT]+=$row[SEWING_OUTPUT];
		$proDataArr[$key][IRON]+=$row[IRON];
		$proDataArr[$key][PRINT_SEND]+=$row[PRINT_SEND];
		$proDataArr[$key][PRINT_REC]+=$row[PRINT_REC];
		$proDataArr[$key][EMB_SEND]+=$row[EMB_SEND];
		$proDataArr[$key][EMB_REC]+=$row[EMB_REC];
		$proDataArr[$key][PACKING]+=$row[PACKING];
	}

	//print_r($proDataArr_);

	$sewingLoacationSql="select ITEM_NUMBER_ID,PO_BREAK_DOWN_ID,LOCATION from pro_garments_production_mst where production_type =5  AND  status_active = 1  AND  is_deleted = 0 ".where_con_using_array($allPoArr,0,'PO_BREAK_DOWN_ID')."";
	// echo $sewingLoacationSql;die;
	$sewingLoacationSqlRes = sql_select($sewingLoacationSql);
	$proLocDataArr = array();
	foreach($sewingLoacationSqlRes as $row)
	{
		$key=$row[PO_BREAK_DOWN_ID].$row[ITEM_NUMBER_ID];
		$proLocDataArr[$key][$row[LOCATION]]=$locationArr[$row[LOCATION]];
	}
 //print_r($proLocDataArr);

	// ======================================== Booking No =================================================

		$bookingDataArr=array();
		$bookingSql="SELECT b.CUTUP_DATE,a.COLOR_SIZE_TABLE_ID as COLOR_SIZE_ID,a.po_break_down_id as PO_ID ,a.booking_no AS BOOKING_NO,a.fin_fab_qnty as FIN_FAB_QTY,b.JOB_NO_MST,b.COLOR_NUMBER_ID,a.FABRIC_COLOR_ID,b.ITEM_NUMBER_ID from wo_booking_dtls a,WO_PO_COLOR_SIZE_BREAKDOWN b where b.id=a.COLOR_SIZE_TABLE_ID and a.po_break_down_id=b.PO_BREAK_DOWN_ID and a.status_active=1 and a.is_deleted=0 ".where_con_using_array($allPoArr,0,'a.po_break_down_id')."";
		 //echo $bookingSql;die;
		$bookingSqlRes=sql_select($bookingSql);
		$gmts_color_wise_fab_color_arr = array();
		$job_wise_gmts_color_arr = array();
		foreach($bookingSqlRes as $row)
		{
			$key=$row['JOB_NO_MST'].'**'.$row['PO_ID'].'**'.$row['ITEM_NUMBER_ID'].'**'.$row['COLOR_NUMBER_ID'];
			$bookingDataArr[$key]['FIN_FAB_QTY']+=$row['FIN_FAB_QTY'];
			$bookingDataArr[$key]['BOOKING_NO'][$row['BOOKING_NO']]=$row['BOOKING_NO'];
			$allBookingArr[$row['BOOKING_NO']]=$row['BOOKING_NO'];
			$gmts_color_wise_fab_color_arr[$row['JOB_NO_MST']][$row['COLOR_NUMBER_ID']][$row['FABRIC_COLOR_ID']]=$row['FABRIC_COLOR_ID'];
			$job_wise_gmts_color_arr[$row['JOB_NO_MST']][$row['COLOR_NUMBER_ID']]=$row['COLOR_NUMBER_ID'];
		}

		// echo "<pre>";print_r($gmts_color_wise_fab_color_arr);

	/*	$fabRecSql="select a.RECEIVE_QNTY,a.BOOKING_NO,a.JOB_NO  from PRO_FINISH_FABRIC_RCV_DTLS a  where  a.TRANS_ID>0 ".where_con_using_array($allBookingArr,1,'a.BOOKING_NO')."";
		$fabRecSqlRes=sql_select($fabRecSql);
		foreach($fabRecSqlRes as $row)
		{
			$bookingDataArr[$row[BOOKING_NO]]+=$row[RECEIVE_QNTY];
		}
	*/

	$fabRecSql="SELECT B.COLOR_ID, d.quantity as RECEIVE_QNTY,  B.ORDER_ID, b.JOB_NO as JOB_NO,B.BOOKING_NO,B.BOOKING_ID  from inv_receive_master a, inv_transaction c,pro_finish_fabric_rcv_dtls b,ORDER_WISE_PRO_DETAILS d where a.id=c.mst_id and c.id=b.trans_id and b.ID=d.dtls_id and c.id=d.trans_id and a.item_category=2 and a.entry_form=37 and a.status_active=1 and c.status_active=1 and b.status_active=1 ".where_con_using_array($allPoArr,0,'d.po_breakdown_id')."";//.where_con_using_array($allBookingArr,1,'b.BOOKING_NO')."
	// echo $fabRecSql;
	$fabRecSqlRes=sql_select($fabRecSql);
	foreach($fabRecSqlRes as $row)
	{
		foreach(explode(',',$row[ORDER_ID]) as $po_id)
		{
			$key=$row[JOB_NO].'**'.$po_id.'**'.$row[COLOR_ID];
			$bookingRecDataArr[$key]+=$row[RECEIVE_QNTY];
		}
	}

	// print_r($bookingDataArr);


	// ========================================= FOR EX-FACTORY QTY ==========================================

	// 		$exFactorySql="SELECT a.po_break_down_id AS PO_ID,  a.CUTUP_DATE,a.JOB_NO_MST,  a.item_number_id AS ITEM_ID, a.color_number_id AS COLOR_ID,
	//           sum(CASE WHEN b.entry_form!=85 THEN ex.production_qnty ELSE 0 END)- sum(CASE WHEN b.entry_form=85 THEN ex.production_qnty ELSE 0 END) AS EXFACTORY_QTY
	//     FROM    wo_po_color_size_breakdown a, pro_ex_factory_dtls ex,pro_ex_factory_mst b
	//    WHERE    ex.color_size_break_down_id = a.id   and  a.item_number_id = '1' and b.id=ex.mst_id  AND a.is_deleted = 0 AND a.status_active = 1   AND (a.po_break_down_id IN (48526, 48527, 48528))
	// GROUP BY a.CUTUP_DATE,  a.JOB_NO_MST, a.po_break_down_id,  a.item_number_id,  a.color_number_id";
	// 		$exFactorySql="SELECT a.po_break_down_id AS PO_ID,  a.CUTUP_DATE,a.JOB_NO_MST,  a.item_number_id AS ITEM_ID, a.color_number_id AS COLOR_ID,
	//           sum(CASE WHEN b.entry_form!=85 THEN ex.production_qnty ELSE 0 END)- sum(CASE WHEN b.entry_form=85 THEN ex.production_qnty ELSE 0 END) AS EXFACTORY_QTY
	//     FROM    wo_po_color_size_breakdown a, pro_ex_factory_dtls ex,pro_ex_factory_mst b
	//    WHERE    ex.color_size_break_down_id = a.id   and  a.item_number_id = '1' and b.id=ex.mst_id  AND a.is_deleted = 0 AND a.status_active = 1   ".where_con_using_array($allPoArr,0,'a.po_break_down_id')."
	// GROUP BY a.CUTUP_DATE,  a.JOB_NO_MST, a.po_break_down_id,  a.item_number_id,  a.color_number_id";
		$exFactorySql="SELECT a.po_break_down_id AS PO_ID,  a.CUTUP_DATE,a.JOB_NO_MST,  a.item_number_id AS ITEM_ID, a.color_number_id AS COLOR_ID,
          sum(CASE WHEN b.entry_form!=85 THEN ex.production_qnty ELSE 0 END)- sum(CASE WHEN b.entry_form=85 THEN ex.production_qnty ELSE 0 END) AS EXFACTORY_QTY
    FROM    wo_po_color_size_breakdown a, pro_ex_factory_dtls ex,pro_ex_factory_mst b
   WHERE    ex.color_size_break_down_id = a.id  and b.id=ex.mst_id  AND a.is_deleted = 0 AND a.status_active = 1  and B.STATUS_ACTIVE=1  and B.IS_DELETED=0 and EX.STATUS_ACTIVE=1 and EX.IS_DELETED=0  ".where_con_using_array($allPoArr,0,'a.po_break_down_id')."
     GROUP BY a.CUTUP_DATE,  a.JOB_NO_MST, a.po_break_down_id,  a.item_number_id,  a.color_number_id";

		//   echo $exFactorySql;
		$exfactorydATAarr=array();
		$exFactorySqlRes = sql_select($exFactorySql);
		foreach($exFactorySqlRes as $row)
		{
			$key=$row[JOB_NO_MST].'**'.$row[PO_ID].'**'.$row[ITEM_ID].'**'.$row[COLOR_ID].'**'.$row[CUTUP_DATE];
			$exfactorydATAarr[$key]['EXFACTORY_QTY']=+$row[EXFACTORY_QTY];
		}

	//print_r($exfactorydATAarr);

	$preCostSql="select JOB_NO,EMB_TYPE,EMB_NAME from WO_PRE_COST_EMBE_COST_DTLS where STATUS_ACTIVE=1 and IS_DELETED=0   ".where_con_using_array($allJobArr,0,'JOB_ID')."";

	//echo $preCostSql;
	$embTypeArr=array();
	$preCostSqlRes = sql_select($preCostSql);
	foreach($preCostSqlRes as $row)
	{
		$emb='';
		if($row[EMB_NAME]==2){$emb=$emblishment_embroy_type[$row[EMB_TYPE]];}
		elseif($row[EMB_NAME]==1){$emb=$emblishment_print_type[$row[EMB_TYPE]];}
		elseif($row[EMB_NAME]==4){$emb=$emblishment_spwork_type[$row[EMB_TYPE]];}
		elseif($row[EMB_NAME]==5){$emb=$emblishment_gmts_type[$row[EMB_TYPE]];}
		$embTypeArr[$row[JOB_NO]][$row[EMB_TYPE]]=$emb;
	}

	//print_r($embTypeArr);die;



		$width=3280;
		$colspan = 8;
		ob_start();

		?>
		<fieldset style="width:<? echo $width+30;?>px;">
			<div>
		        <table width="<? echo $width;?>" cellpadding="0" cellspacing="0">
		            <tr class="form_caption">
		            	<td colspan="<? echo $colspan;?>" align="center"><strong>Cut Off Date Wise Production Followup Report</strong></td>
		            </tr>
		            <tr class="form_caption">
		            	<td colspan="<? echo $colspan;?>" align="center"><strong><? echo $company_arr[$cbo_company_name]; ?></strong></td>
		            </tr>
		            <tr class="form_caption">
		            	<td colspan="<? echo $colspan;?>" align="center"><strong><? //echo "Date:  ".change_date_format( str_replace("'","",trim($txt_production_date)) ); ?></strong></td>
		            </tr>
		        </table>
		    </div>

			<div style="width:<?=$width+25;?>px;" >

				<table width="<?=$width;?>" cellspacing="0" border="1" align="left" class="rpt_table" rules="all" id="table_header" >
					<thead>

						<th width="30">Sl</th>
						<th width="80">Buyer</th>
						<th width="80">Job no</th>
						<th width="80">Style no</th>
						<th width="80">Order no</th>
						<th width="100">Fab Booking No</th>
						<th width="80">Tod/Cutt off date</th>
						<th width="100">Item no</th>
						<th width="80">Color no</th>
						<th width="80">Total Ord</th>
						<th width="80">Color wise Qty</th>
						<th width="60">Fob</th>
						<th width="60">SMV</th>
						<th width="80">Emblishment Type</th>
						<th width="80">Ord plan cut Qty</th>
						<th width="80">Fab Consumption Per DZN</th>
						<th width="80">Fabric Required</th>
						<th width="80">Fabric Rcv</th>
						<th width="80">Fabric Balance</th>
						<th width="80">Work Location</th>
						<th width="80">Total Cut</th>
						<th width="80">Cut Balance</th>
						<th width="80">Total Print Sent</th>
						<th width="80">Total Print Rcvd</th>
						<th width="80">Print Balance</th>
						<th width="80">Tot Emb Sent</th>
						<th width="80">Tot Emb Rcvd</th>
						<th width="80">Emb Balance</th>
						<th width="80">Tot Swe In</th>
						<th width="80">Tot Swe Out</th>
						<th width="80">Swe Balance</th>
						<th width="80">Total Iron</th>
						<th width="80">Iron Balance</th>
						<th width="80">Tot Finish</th>
						<th width="80">Finish Balance</th>
						<th width="80">Total Ex-Factory</th>
						<th width="80">Ex F Balance</th>
						<th width="80">Shipment Status</th>
						<th>Emblishmentmean as per below</th>
					</thead>
					</table>
					<div style="max-height:425px; overflow-y:scroll; width:<?=$width+18;?>px" id="scroll_body">
						<table border="1" class="rpt_table" width="<?=$width;?>" rules="all" id="table_body">

						<tbody>
						<?
						$i=1;
						$tempPo=array();
						foreach($dataArr as $key=>$row)
						{
							list($style,$job,$po_id,$item_id,$color_id,$cutup_date)=explode('**',$key);
							$newKey=$job.'**'.$po_id.'**'.$item_id.'**'.$color_id.'**'.$cutup_date;
							$newKey1=$job.'**'.$po_id.'**'.$item_id.'**'.$color_id;
							$newKey2=$job.'**'.$po_id.'**'.$color_id;
							$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;

							// ========== contrust color ff issue qty
							$fab_color_arr = $gmts_color_wise_fab_color_arr[$job][$color_id];
							$contrust_color_issue_qty = 0;
							foreach ($fab_color_arr as $v) 
							{
								if($job_wise_gmts_color_arr[$job][$v]=="")
								{
									$newKey3=$job.'**'.$po_id.'**'.$v;
									// echo $color_id."=".$v."=".$newKey3."<br>";
									$contrust_color_issue_qty += $bookingRecDataArr[$newKey3];
								}
							}

							// echo $newKey1.'==='.$bookingDataArr[$newKey1][FIN_FAB_QTY]."<br>";
							?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
								<td width="30"><?= $i; ?></td>
								<td width="80"><?= $buyerArr[$row['BUYER_ID']];?></td>
								<td width="80"><?= $job;?></td>
								<td width="80"><p><?= $style;?></p></td>
								<td width="80"><p><?= $row['PO_NUMBER'];?></p></td>
								<td width="100"><p><?
									$booking_no= implode(',',$bookingDataArr[$newKey1][BOOKING_NO]);
									echo $booking_no;

								?></p></td>
								<td width="80" align="center"><?= change_date_format($row['CUTUP_DATE']); $cutup_date=$row['CUTUP_DATE']; ?></td>
								<td width="100"><p><?= $garments_item[$item_id];?></p></td>
								<td width="80"><p><?= $color_arr[$color_id];?></p></td>
								<? if($tempPo[$job.$row["PO_ID"]]==0){ ?>
                                <td width="80" align="right" rowspan="<?= count($orderSpanArr[$job][$row["PO_ID"]]);?>">
								<?= $orderQtyArr[$job][$row["PO_ID"]];
								$order_qty=$orderQtyArr[$job][$row["PO_ID"]];
								?></td>
                                <?
									$tempPo[$job.$row["PO_ID"]]=1;
								} ?>

								<td width="80" align="right"><?= number_format($row['ORDER_QUANTITY'],0);?></td>
								<td width="60" align="center"><?= number_format($row['ORDER_TOTAL']/$row['ORDER_QUANTITY'],2);?></td>
								<td width="60" align="center"><?= $row['SMV_PCS'];?></td>
								<td width="80"><p><?= implode(', ',$embTypeArr[$job]);?></p></td>
								<td width="80" align="right"><?= $row[PLAN_CUT_QNTY];?></td>
								<td width="80" align="right"><?
								    $fab_consumption_per_dzan=($bookingDataArr[$newKey1][FIN_FAB_QTY] / $row['ORDER_QUANTITY']) * 12;
									echo  number_format($fab_consumption_per_dzan,2);
									$total_fab_consumption_per_dzan += $fab_consumption_per_dzan;
									   ?>
								</td>

                                <? if($tempPoColor[$job.$row["PO_ID"].$item_id.$color_id]==0){
									// echo $newKey1.'==='.$bookingDataArr[$newKey1][FIN_FAB_QTY]."<br>";
									?>

                                <td rowspan="<?= count($orderColorSpanArr[$job][$row["PO_ID"]][$item_id][$color_id]);?>" width="80" align="right"><p><? echo number_format($bookingDataArr[$newKey1][FIN_FAB_QTY],0);?></p></td>
								<td rowspan="<?= count($orderColorSpanArr[$job][$row["PO_ID"]][$item_id][$color_id]);?>" width="80" align="right">

								<a href="##" style="color:blue;" onclick="openmypage_fabric_recive('<? echo $fab_consumption_per_dzan; ?>', '<? echo $booking_no; ?>','<? echo $order_qty ?>','<? echo $cutup_date; ?>','<? echo $job; ?>','<? echo $color_id; ?>','<? echo $row['PO_ID']; ?>','fabric_receive_popup')"><? echo $bookingRecDataArr[$newKey2]+$contrust_color_issue_qty; ?></a>

								</td>
								<td rowspan="<?= count($orderColorSpanArr[$job][$row["PO_ID"]][$item_id][$color_id]);?>" width="80" align="right"><?= number_format(($bookingDataArr[$newKey1][FIN_FAB_QTY]-$bookingRecDataArr[$newKey2]+$contrust_color_issue_qty),0);?></td>
                                <?

									$totalFabricRequired+=$bookingDataArr[$newKey1][FIN_FAB_QTY];
									$totalFabricRec+=$bookingRecDataArr[$newKey2]+$contrust_color_issue_qty;
									$totalFabricBlance+=$bookingDataArr[$newKey1][FIN_FAB_QTY]-($bookingRecDataArr[$newKey2]+$contrust_color_issue_qty);
									$tempPoColor[$job.$row["PO_ID"].$item_id.$color_id]=1;
								} ?>


								<td width="80"><?= implode(', ',$proLocDataArr[$po_id.$item_id]);?></td>
								<td width="80" align="right"><? echo $proDataArr[$newKey][CUTTING];?></td>
								<td width="80" align="right"><? echo $row['ORDER_QUANTITY']-$proDataArr[$newKey][CUTTING];?></td>
								<td width="80" align="right"><? echo $proDataArr[$newKey]['PRINT_SEND'];?></td>
								<td width="80" align="right"><? echo $proDataArr[$newKey]['PRINT_REC'];?></td>
								<td width="80" align="right"><? echo $proDataArr[$newKey]['PRINT_SEND']-$proDataArr[$newKey]['PRINT_REC'];?></td>
								<td width="80" align="right"><? echo $proDataArr[$newKey]['EMB_SEND'];?></td>
								<td width="80" align="right"><? echo $proDataArr[$newKey]['EMB_REC'];?></td>
								<td width="80" align="right"><? echo $proDataArr[$newKey]['EMB_SEND']-$proDataArr[$newKey]['EMB_REC'];?></td>
								<td width="80" align="right"><? echo $proDataArr[$newKey]['SEWING_INPUT'];?></td>
								<td width="80" align="right"><? echo $proDataArr[$newKey]['SEWING_OUTPUT'];?></td>
								<td width="80" align="right"><? echo $proDataArr[$newKey]['SEWING_INPUT']-$proDataArr[$newKey]['SEWING_OUTPUT'];?></td>
								<td width="80" align="right"><?= $proDataArr[$newKey]['IRON'];?></td>
								<td width="80" align="right"><?= $row['ORDER_QUANTITY']-$proDataArr[$newKey]['IRON'];?></td>
								<td width="80" align="right"><? echo $proDataArr[$newKey]['PACKING'];?></td>
								<td width="80" align="right"><? echo $row['ORDER_QUANTITY']-$proDataArr[$newKey]['PACKING'];?></td>
								<td width="80" align="right"><?= number_format($exfactorydATAarr[$newKey]['EXFACTORY_QTY'],0);?></td>
								<td width="80" align="right"><?= number_format($row['ORDER_QUANTITY']-$exfactorydATAarr[$newKey]['EXFACTORY_QTY'],0);?></td>
								<td width="80" align="center"><?= $delivery_status[$row['SHIPING_STATUS']];?></td>
								<td><p><?= $row['COMPLEXITY'];?></p></td>
							</tr>

							<?
							//Total sum--------------
							$totalColorwiseQty+=$row['ORDER_QUANTITY'];
							$totalOrdplancutQty+=$row[PLAN_CUT_QNTY];
							$totalCut+=$proDataArr[$newKey][CUTTING];
							$totalCutBalance+=$row['ORDER_QUANTITY']-$proDataArr[$newKey][CUTTING];
							$totalPrintSent+=$proDataArr[$newKey]['PRINT_SEND'];
							$totalPrintRcvd+=$proDataArr[$newKey]['PRINT_REC'];
							$totalPrintBalance+=$proDataArr[$newKey]['PRINT_SEND']-$proDataArr[$newKey]['PRINT_REC'];
							$totalEmbSent+=$proDataArr[$newKey]['EMB_SEND'];
							$totalEmbRcvd+=$proDataArr[$newKey]['EMB_REC'];
							$totalEmbBalance+=$proDataArr[$newKey]['EMB_SEND']-$proDataArr[$newKey]['EMB_REC'];
							$totalSweIn+=$proDataArr[$newKey]['SEWING_INPUT'];
							$totalSweOut+=$proDataArr[$newKey]['SEWING_OUTPUT'];
							$totalSweBalance+=$proDataArr[$newKey]['SEWING_INPUT']-$proDataArr[$newKey]['SEWING_OUTPUT'];
							$totalIron+=$proDataArr[$newKey]['IRON'];
							$totalIronBalance+=$row['ORDER_QUANTITY']-$proDataArr[$newKey]['IRON'];
							$totalFinish+=$proDataArr[$newKey]['PACKING'];
							$totalFinishBalance+=$row['ORDER_QUANTITY']-$proDataArr[$newKey]['PACKING'];
							$totalExFactory+=$exfactorydATAarr[$newKey]['EXFACTORY_QTY'];
							$totalExFBalance+=$row['ORDER_QUANTITY']-$exfactorydATAarr[$newKey]['EXFACTORY_QTY'];




							$i++;

						}
						?>
						</tbody>

						</table>


						<table border="1" class="tbl_bottom" width="<?=$width;?>" rules="all" id="report_table_footer_1" >
							<tr>
								<td width="30"></td>
								<td width="80"></td>
								<td width="80"></td>
								<td width="80"></td>
								<td width="80"></td>
								<td width="100"></td>
								<td width="80"></td>
								<td width="100"></td>
								<td width="80"></td>
								<td width="80"></td>
								<td width="80" id="total_color_wise_qty"><?=$totalColorwiseQty;?></td>
								<td width="60"></td>
								<td width="60" ></td>
								<td width="80"></td>
								<td width="80"><?=$totalOrdplancutQty;?></td>
								<td width="80"><? echo number_format($total_fab_consumption_per_dzan,2); ?></td>
								<td width="80" id="total_fabric_required"><?=$totalFabricRequired;?></td>
								<td width="80" id="total_fabric_rcv"><?=$totalFabricRec;?></td>
								<td width="80" id="total_fabric_balance"><?=$totalFabricBlance;?></td>
								<td width="80"></td>
								<td width="80" id="total_cut"><?=$totalCut;?></td>
								<td width="80" id="total_cut_balance"><?=$totalCutBalance;?></td>
								<td width="80" id="total_print_sent"><?=$totalPrintSent;?></td>
								<td width="80" id="total_print_rcvd"><?=$totalPrintRcvd;?></td>
								<td width="80" id="total_print_balance"><?=$totalPrintBalance;?></td>
								<td width="80" id="total_emb_sent"><?=$totalEmbSent;?></td>
								<td width="80" id="total_emb_rcvd"><?=$totalEmbRcvd;?></td>
								<td width="80" id="total_emb_balance"><?=$totalEmbBalance;?></td>
								<td width="80" id="total_swe_in"><?=$totalSweIn;?></td>
								<td width="80" id="total_swe_out"><?=$totalSweOut;?></td>
								<td width="80" id="total_swe_balance"><?=$totalSweBalance;?></td>
								<td width="80" id="total_iron"><?=$totalIron;?></td>
								<td width="80" id="total_iron_balance"><?=$totalIronBalance;?></td>
								<td width="80" id="total_finish"><?=$totalFinish;?></td>
								<td width="80" id="total_finish_balance"><?=$totalFinishBalance;?></td>
								<td width="80" id="total_ex_factory"><?=$totalExFactory;?></td>
								<td width="80" id="total_exf_balance"><?=$totalExFBalance;?></td>
								<td width="80"></td>
								<td></td>
							</tr>
						</table>

					</div>
				</table>
			</div>
		</fieldset>




	<?
	foreach (glob("$user_id*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	//$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";

	exit();
}
if($action=="fabric_receive_popup")
{
	echo load_html_head_contents("Fabric Received Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";

			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";
		}
	</script>
	<div style="width:650px" align="center"><input type="button" value="Print Preview" onClick="print_window();" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:650px; margin-left:20px">
		<div id="report_container">
			<?
			$colorArr=return_library_array("select id, color_name from lib_color", "id", "color_name");
			$buyerArr=return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");

			// $fabRecDetailsSql="SELECT B.COLOR_ID, B.RECEIVE_QNTY,  B.ORDER_ID, B.job_no,B.BOOKING_NO,B.BOOKING_ID,b.INSERT_DATE  from inv_receive_master a, pro_finish_fabric_rcv_dtls b, inv_transaction c where a.id=c.mst_id and c.id=b.trans_id  and a.item_category=2 and a.entry_form=37 and a.status_active=1 and c.status_active=1 and b.status_active=1 and b.BOOKING_NO='$booking_no' and b.job_no='$job' and b.COLOR_ID='$color_id' and b.ORDER_ID='$poId' ";

			$fabric_color_id_arr = return_library_array("SELECT fabric_color_id from WO_BOOKING_DTLS where po_break_down_id=$poId and status_active=1 and gmts_color_id=$color_id","fabric_color_id","fabric_color_id");
			// echo "SELECT fabric_color_id from WO_BOOKING_DTLS where po_break_down_id=$poId and status_active=1 and gmts_color_id=$color_id";
			if(count($fabric_color_id_arr)>0)
			{
				$fab_color_ids = implode(",",$fabric_color_id_arr);
				$color_id = $color_id.",".$fab_color_ids;
			}

			$fabRecDetailsSql="SELECT B.COLOR_ID, d.quantity as RECEIVE_QNTY,  B.ORDER_ID, B.JOB_NO,B.BOOKING_NO,B.BOOKING_ID,b.INSERT_DATE  from inv_receive_master a, inv_transaction c,pro_finish_fabric_rcv_dtls b,ORDER_WISE_PRO_DETAILS d where a.id=c.mst_id and c.id=b.trans_id and b.ID=d.dtls_id and c.id=d.trans_id and a.item_category=2 and a.entry_form=37 and a.status_active=1 and c.status_active=1 and b.status_active=1  and b.COLOR_ID in($color_id) and d.po_breakdown_id='$poId' ";//and b.job_no='$job'
			
			// echo $fabRecDetailsSql;

			$sql_trans="SELECT a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no,b.po_number as po_no, d.po_breakdown_id as po_id, d.color_id,e.TRANSFER_SYSTEM_ID as sys_no,
			(case when d.entry_form in(14,15,134,306) and d.trans_type=6 then d.quantity else 0 end) as trns_out_qnty,
			(case when d.entry_form in(14,15,134,306) and d.trans_type=5 then d.quantity else 0 end) as trns_in_qnty
			from wo_po_details_master a, wo_po_break_down b, order_wise_pro_details d, inv_transaction c,  inv_item_transfer_mst e
			where a.id=b.job_id and d.po_breakdown_id=b.id and c.id = d.trans_id and c.mst_id=e.id and b.id=$poId and d.color_id in($color_id) and d.entry_form in (14,15,134,306) and c.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 order by e.TRANSFER_SYSTEM_ID";
			// echo $sql_trans;
			$trans_res = sql_select($sql_trans);
			$trans_array = array();
			foreach ($trans_res as $v) 
			{
				$trans_array[$v['JOB_NO']][$v['PO_ID']][$v['COLOR_ID']][$v['SYS_NO']]['in_qty'] +=  $v['TRNS_IN_QNTY'];
				$trans_array[$v['JOB_NO']][$v['PO_ID']][$v['COLOR_ID']][$v['SYS_NO']]['out_qty'] +=  $v['TRNS_OUT_QNTY'];
				$trans_array[$v['JOB_NO']][$v['PO_ID']][$v['COLOR_ID']][$v['SYS_NO']]['style'] =  $v['STYLE_REF_NO'];
				$trans_array[$v['JOB_NO']][$v['PO_ID']][$v['COLOR_ID']][$v['SYS_NO']]['buyer'] =  $v['BUYER_NAME'];
				$trans_array[$v['JOB_NO']][$v['PO_ID']][$v['COLOR_ID']][$v['SYS_NO']]['po_no'] =  $v['PO_NO'];
			}
			// print_r($trans_array);

			$fabRecDetailsSqlRes=sql_select($fabRecDetailsSql);

			$tblWidth=650;
		   ?>
			<table border="1" class="rpt_table" rules="all" width="<?=$tblWidth; ?>" cellpadding="0" cellspacing="0">
			    <caption> <h3> Cut off Date Wise Fabric Recv Qty </h3>	</caption>
				<thead style="font-size:13px">
                	<tr>
                        <th width="30" rowspan="4">SL</th>
                        <th width="100" rowspan="4">Rcv Date</th>
                        <th width="100" rowspan="4">Cut off Date </th>
                        <th width="100" rowspan="4">Color</th>
                        <th width="100" rowspan="4">Order Qty</th>
                        <th width="100" rowspan="4">Consumption</th>
                        <th width="100" rowspan="4">Fab Rcv Qty</th>
                    </tr>
				</thead>
            </table>
            <div style="width:<? echo $tblWidth+20; ?>px; max-height:320px;  overflow-y:auto" id="scroll_body">
            <!-- <div style="width:<?//echo $tblWidth; ?>px; max-height:320px;"> -->
                <table border="1" class="rpt_table" rules="all" width="<?=$tblWidth; ?>" cellpadding="0" cellspacing="0">
                    <?
					$i=1;
					$tot_qty = 0;
					foreach($fabRecDetailsSqlRes as $row)
					{
								// echo"<pre>";
								// print_r($row);
                        ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>"  style="font-size:13px">
                                    <td width="30" align="center"><? echo $i; ?></td>
                                    <td width="100" style="word-break:break-all"><? echo change_date_format($row[INSERT_DATE]); ?></td>
                                    <td width="100" style="word-break:break-all"><? echo change_date_format($cutup_date); ?></td>
                                    <td width="100" style="word-break:break-all"><? echo $colorArr[$row[COLOR_ID]]; ?></td>

                                    <td width="100" style="word-break:break-all"><? echo $order_qty ?></td>
                                    <td width="100" style="word-break:break-all"><? echo number_format($fab_consumption_per_dzan,2); ?></td>
                                    <td width="100" style="word-break:break-all" align="right"><? echo $row[RECEIVE_QNTY]; ?></td>
                                </tr>
						<?
						$i++;
						$tot_qty += $row[RECEIVE_QNTY];
					}
                    ?>
                </table>
            </div>
			<table border="1" class="rpt_table" rules="all" width="<?=$tblWidth; ?>" cellpadding="0" cellspacing="0">
				<tfoot>
                	<tr>
                        <th width="30"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"><?=number_format($tot_qty,0);?></th>
                    </tr>
				</tfoot>
            </table>


			<table border="1" class="rpt_table" rules="all" width="<?=$tblWidth; ?>" cellpadding="0" cellspacing="0">
			    <caption> <h3> Transfer History </h3>	</caption>
				<thead style="font-size:13px">
                	<tr>
                        <th width="30" rowspan="4">SL</th>
                        <th width="100" rowspan="4">Buyer </th>
                        <th width="100" rowspan="4">Job</th>
                        <th width="100" rowspan="4">Order</th>
                        <th width="100" rowspan="4">Color</th>
                        <th width="100" rowspan="4">System No</th>
                        <th width="80" rowspan="4">Transfer In</th>
                        <th width="80" rowspan="4">Transfer Out</th>
                    </tr>
				</thead>
            </table>
            <div style="width:<? echo $tblWidth+20; ?>px; max-height:320px;  overflow-y:auto" id="scroll_body2">
            <!-- <div style="width:<?//echo $tblWidth; ?>px; max-height:320px;"> -->
                <table border="1" class="rpt_table" rules="all" width="<?=$tblWidth; ?>" cellpadding="0" cellspacing="0">
                    <?
					$sl=1;
					$tot_qty = 0;
					foreach($trans_array as $job_key => $job_data)
					{
						foreach($job_data as $po_key => $po_data)
						{
							foreach($po_data as $color_key => $color_data)
							{
								foreach($color_data as $sys_key => $row)
								{
								
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>"  style="font-size:13px">
										<td width="30" align="center"><? echo $sl; ?></td>
										<td width="100" style="word-break:break-all"><? echo $buyerArr[$row['buyer']]; ?></td>
										<td width="100" style="word-break:break-all"><? echo $job_key; ?></td>
										<td width="100" style="word-break:break-all"><? echo $row['po_no']; ?></td>
										<td width="100" style="word-break:break-all"><? echo $colorArr[$color_key]; ?></td>
										<td width="100" style="word-break:break-all"><? echo $sys_key; ?></td>
										<td width="80" style="word-break:break-all" align="right"><? echo $row['in_qty']; ?></td>
										<td width="80" style="word-break:break-all" align="right"><? echo $row['out_qty']; ?></td>
									</tr>
									<?
									$i++;
									$sl++;
									$in_qty += $row['in_qty'];
									$out_qty += $row['out_qty'];
								}
							}
						}
					}
                    ?>
                </table>
            </div>
			<table border="1" class="rpt_table" rules="all" width="<?=$tblWidth; ?>" cellpadding="0" cellspacing="0">
				<tfoot>
                	<tr>
                        <th width="30"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100">Total</th>
                        <th width="80"><?=number_format($in_qty,0);?></th>
                        <th width="80"><?=number_format($out_qty,0);?></th>
                    </tr>
				</tfoot>
            </table>
        </div>
	</fieldset>
 <?
    exit();
}
?>